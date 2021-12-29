<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php';

//파라미터 검증
$iParentBoNo = !empty($_POST['bo_parent']) ? (int)trim($_POST['bo_parent']) : "";
$sBoWriter   = !empty($_POST['bo_writer']) ? trim($_POST['bo_writer']) : "";
$sBoTitle    = !empty($_POST['bo_title']) ? trim($_POST['bo_title']) : "";
$sBoContent  = !empty($_POST['bo_content']) ? $_POST['bo_content'] : "";
$sBoSec      = !empty($_POST['bo_sec']) ? trim($_POST['bo_sec']) : "";
$sBoPw       = !empty($_POST['bo_pw']) ? trim($_POST['bo_pw']) : "";

//부모게시글이 존재하지 않는 게시물일때
if (empty($iParentBoNo)) {
    echo "<script>";
    echo "    alert('부모 게시글이 존재하지 않습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
}

//해당 게시글 번호의 부모글 존재여부 확인
$sQuery = "
    SELECT
        bo_grpno,
        bo_seq,
        bo_depth,
        bo_sec,
        bo_pw
    FROM
        board
    WHERE
        bo_no = ".mysqli_real_escape_string($conn, $iParentBoNo)." AND
        bo_del = 'N'
";

$oParentBoard = mysqli_query($conn, $sQuery);
//해당 번호의 부모 게시글이 존재하지 않을 때
if ($oParentBoard->num_rows == 0) {
    echo "<script>";
    echo "    alert('존재하지 않은 게시물에는 답글을 작성할 수 없습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//해당 번호의 부모 게시글이 존재할 때
$aParent    = $oParentBoard->fetch_array();
$iBoGrpno   = $aParent['bo_grpno'];
$iParentSeq = $aParent['bo_seq']; 
$iBoDepth   = $aParent['bo_depth'] + 1;
$iBoSeq     = $iParentSeq + 1;

//파라미터 검증
if (empty($sBoWriter)) {
    echo "<script>";
    echo "    alert('로그인이 필요합니다.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
} else if (empty($sBoTitle)) {
    echo "<script>";
    echo "    alert('제목을 입력해주세요.');";
    echo "    history.back();";
    echo "</script>";
    exit;
} else if (empty($sBoContent)) {
    echo "<script>";
    echo "    alert('내용을 입력해주세요.');";
    echo "    history.back();";
    echo "</script>";
    exit;
}

//부모글이 비밀글일 경우 - 부모글의 비밀번호와 답글의 비밀번호가 동일한지 확인
if ($aParent['bo_sec'] == 'Y') {
    $bVerify = password_verify($sBoPw, $aParent['bo_pw']);
    //게시글 비밀번호 일치여부
    if (!$bVerify) {
        echo "<script>";
        echo "    alert('원글의 비밀번호와 동일하지 않습니다. 비밀번호를 다시 입력하세요.');";
        echo "    history.back();";
        echo "</script>";
        exit;
    }
}

//비밀글 파라미터 검증
if (!empty($sBoSec)) { //비밀글일 경우
    if (empty($sBoPw)) {//비밀글인데 비밀번호가 없을 경우
        echo "<script>";
        echo "    alert('비밀글일 경우 게시글 비밀번호는 필수입니다.');";
        echo "    history.back();";
        echo "</script>";
        exit;
    } else {
        $sBoPw = mysqli_real_escape_string($conn, $sBoPw);
        $sHash = password_hash($sBoPw, PASSWORD_BCRYPT);
    }
}else {
    //비밀글이 아닐때 비밀번호 입력했다면 초기화
    $sBoSec = 'N';
    $sHash = "";
}

//DB트랜잭션을 사용하기 위해 auto_commit 비활성화
mysqli_autocommit($conn, FALSE);

//그룹내 순서 변경
$sUpdate = "
    UPDATE board
    SET
        bo_seq = bo_seq + 1
    WHERE
        bo_grpno = ".$iBoGrpno." AND
        bo_seq > ".$iParentSeq
;

if (mysqli_query($conn, $sUpdate) === false) { //update 실패
    echo "<script>";
    echo "    alert('답글 작성에 실패했습니다. 잠시 후 다시 시도해주세요.');";
    echo "    history.back();";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//update 성공
//insert - 답글
$sSql = "
    INSERT INTO
        board
    SET
        bo_grpno   = ".$iBoGrpno.",
        bo_seq     = ".$iBoSeq.",
        bo_depth   = ".$iBoDepth.",
        bo_parent  = ".mysqli_real_escape_string($conn, $iParentBoNo).",
        bo_title   = '".mysqli_real_escape_string($conn, $sBoTitle)."',
        bo_writer  = '".mysqli_real_escape_string($conn, $sBoWriter)."',
        bo_content = '".mysqli_real_escape_string($conn, $sBoContent)."',
        bo_sec     = '".mysqli_real_escape_string($conn, $sBoSec)."',
        bo_pw      = '".mysqli_real_escape_string($conn, $sHash)."',
        bo_date    = NOW()
";

if (mysqli_query($conn, $sSql) === true) { //insert 성공
    //자동 생성된 id 중 마지막 쿼리에 사용된 id를 반환
    $iCurrentNo = mysqli_insert_id($conn);
    
    //비밀글일 경우 글작성 완료 후 상세 조회를 위한 인증 세션 부여
    if (!empty($sBoSec)) {
        $_SESSION['boardauth'] = $iCurrentNo;
    }
    
    //작성한 게시글 상세조회로 이동
    echo "<script>";
    echo "    alert('게시글 작성이 완료되었습니다.');";
    echo "    window.location.href='./view.php?no=".$iCurrentNo."';";
    echo "</script>";
    
    //update, insert, update 모두 성공했을 때 수동 commit
    mysqli_commit($conn);
    mysqli_autocommit($conn, TRUE);
    mysqli_close($conn);
    exit;
} else { //insert 실패
    echo "<script>";
    echo "    alert('답글 작성에 실패했습니다. 잠시 후 다시 시도해주세요.dd');";
    echo "    history.back();";
    echo "</script>";
    
    //update에 실패하면 insert, update 쿼리 rollback
    mysqli_rollback($conn);
    mysqli_close($conn);
    exit;
}
?>