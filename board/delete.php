<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';

$iBoNo = !empty($_GET['no']) ? (int)trim($_GET['no']) : "";

//게시글 번호 null
if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('게시글 번호가 누락되었습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
}

//삭제할 게시글 조회
$sQuery = "
    SELECT
        a.bo_writer,
        a.bo_seq,
        COUNT(b.bo_no) AS bo_child,
        a.bo_parent,
        a.bo_grpno
    FROM
        board a
        LEFT JOIN board b
        ON (a.bo_no = b.bo_parent)
    WHERE
        a.bo_no  = ".mysqli_real_escape_string($conn, $iBoNo)." AND
        a.bo_del = 'N'
    GROUP BY
        a.bo_writer,
        a.bo_seq,
        a.bo_parent,
        a.bo_grpno
";

//존재하는 게시글 번호인지 검증
$oBoard = mysqli_query($conn, $sQuery);
if ($oBoard->num_rows == 0) {
    echo "<script>";
    echo "    alert('존재하지 않은 게시글입니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//로그인한 정보와 게시글 작성자가 동일한지 확인
$aArray = $oBoard->fetch_array();
if ($aArray['bo_writer'] != $_SESSION['user_id']) {
    echo "<script>";
    echo "    alert('게시글 작성자만 삭제할 수 있습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

$iBoSeq    = $aArray['bo_seq'];
$iBoChild  = $aArray['bo_child'];
$iBoParent = $aArray['bo_parent'];
$iBoGrpno  = $aArray['bo_grpno'];

//DB트랜잭션을 사용하기 위해 auto_commit 비활성화
mysqli_autocommit($conn, FALSE);

//삭제할 글의 자식글이 존재 여부 확인
if ($iBoChild > 0) { //자식글이 존재한다면
    //자식글이 있다면 update 실행 - 삭제여부 컬럼 Y로 변경
    $sSql = "UPDATE board SET bo_del = 'Y' WHERE bo_no = ".mysqli_real_escape_string($conn, $iBoNo);
    
    if (mysqli_query($conn, $sSql) === true) {//삭제여부 컬럼 update 성공
        echo "<script>";
        echo "    alert('게시글 삭제가 완료되었습니다.');";
        echo "    window.location.href='./main.php';";
        echo "</script>";
        mysqli_commit($conn);
        mysqli_autocommit($conn, TRUE);
        mysqli_close($conn);
        exit;
    } else {//삭제여부 컬럼 update 실패
        echo "<script>";
        echo "    alert('게시글 삭제에 실패했습니다. 잠시 후 다시 시도해주세요.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        mysqli_close($conn);
        exit;
    }
} else {//자식글이 존재하지 않는다면
    //delete 실행
    $sSql = "DELETE FROM board WHERE bo_no = ".mysqli_real_escape_string($conn, $iBoNo);
    
    if (mysqli_query($conn, $sSql) === false) {//자식글이 없는 게시글 delete 실패
        echo "<script>";
        echo "    alert('게시글 삭제에 실패했습니다. 잠시 후 다시 시도해주세요.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        mysqli_close($conn);
        exit;
    }
}

//자식글이 없는 게시글 delete 성공
//- 같은 그룹의 삭제여부가 Y이고 자식글이 없는 부모글들 조회
$sDelParents = "
    SELECT
        a.bo_no,
        a.bo_seq
    FROM
        board a
        LEFT JOIN board b
        ON (a.bo_no = b.bo_parent)
    WHERE 
        a.bo_grpno = ".$iBoGrpno." AND
        b.bo_no IS NULL AND 
        a.bo_del = 'Y'
";

$oDelParents = mysqli_query($conn, $sDelParents);
//삭제될 부모글 조회 성공
$aDelNos  = array();
$aDelSeqs = array();

//삭제한 게시글의 seq도 배열에 저장
$aDelSeqs[] = $iBoSeq;
$sDelNos = "";

//삭제될 부모글 조회 rows가 0보다 클때
if ($oDelParents->num_rows > 0) {
    while ($aRow = $oDelParents->fetch_array()) {
        $aDelNos[]  = $aRow['bo_no'];
        $aDelSeqs[] = $aRow['bo_seq'];
    }
    
    if (count($aDelNos) > 1) {
        $sDelNos = implode(', ', $aDelNos);
    } else {
        $sDelNos = $aDelNos[0];
    }
    
    //내림차순으로 재정렬
    rsort($aDelSeqs);
    
    //같은 그룹에 자식글이 없고 삭제여부가 'Y'인 게시글 전부 삭제
    $sAllDelete = "
        DELETE
        FROM
            board
        WHERE
            bo_no IN (".$sDelNos.")
    ";
    
    if (mysqli_query($conn, $sAllDelete) === false) { //부모글들 삭제 실패
        echo "<script>";
        echo "    alert('게시글 삭제에 실패했습니다. 잠시 후 다시 시도해주세요.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        mysqli_close($conn);
        exit;
    }
}

//삭제한 글, 부모글들 삭제 성공
//그룹 순서 재배치
$sAllSeq = "
    UPDATE board
    SET
        bo_seq = bo_seq -1
    WHERE
        bo_grpno = ".$iBoGrpno." AND
        bo_seq > ?
";

//statement 생성
$oStmt = mysqli_prepare($conn, $sAllSeq);

foreach ($aDelSeqs as $sValue) {
    mysqli_stmt_bind_param($oStmt, "s", $sValue);
    if (mysqli_stmt_execute($oStmt) === false) {//그룹 순서 재배치 실패
        echo "<script>";
        echo "    alert('게시글 삭제에 실패했습니다. 잠시 후 다시 시도해주세요.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        //statement 해제
        mysqli_stmt_close($oStmt);
        mysqli_close($conn);
        exit;
    }
}

//그룹 순서 재배치 성공
//원글만 남았을 경우 - 원글 삭제여부 'Y'
$sOrigin = "
    SELECT
        a.bo_no
    FROM
        board a
        LEFT JOIN board b
        ON (a.bo_no = b.bo_parent)
    WHERE
        a.bo_grpno = ".$iBoGrpno." AND
        b.bo_no IS NULL AND
        a.bo_parent IS NULL AND 
        a.bo_del = 'Y'
";

$oOrigin = mysqli_query($conn, $sOrigin);
$aOrigin = $oOrigin->fetch_array();
if ($oOrigin->num_rows == 0) { //원글 - 삭제여부 'N'
    echo "<script>";
    echo "    alert('게시글 삭제가 완료되었습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_commit($conn);
    mysqli_autocommit($conn, TRUE);
    mysqli_close($conn);
    exit;
} else { //원글 - 삭제여부 'Y'
    $sDelOrigin = "DELETE FROM board WHERE bo_no = ".$aOrigin['bo_no']." AND bo_grpno = ".$iBoGrpno;
    
    if (mysqli_query($conn, $sDelOrigin) === true) {
        echo "<script>";
        echo "    alert('게시글 삭제가 완료되었습니다.');";
        echo "    window.location.href='./main.php';";
        echo "</script>";
        mysqli_commit($conn);
        mysqli_autocommit($conn, TRUE);
        mysqli_close($conn);
        exit;
    } else {
        echo "<script>";
        echo "    alert('게시글 삭제에 실패했습니다. 잠시 후 다시 시도해주세요.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        //statement 해제
        mysqli_stmt_close($oStmt);
        mysqli_close($conn);
        exit;
    }
}
?>