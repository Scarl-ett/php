<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';

$iBoNo = !empty($_GET['no']) ? (int)trim($_GET['no']) : "";

//�Խñ� ��ȣ null
if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('�Խñ� ��ȣ�� �����Ǿ����ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
}

//������ �Խñ� ��ȸ
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

//�����ϴ� �Խñ� ��ȣ���� ����
$oBoard = mysqli_query($conn, $sQuery);
if ($oBoard->num_rows == 0) {
    echo "<script>";
    echo "    alert('�������� ���� �Խñ��Դϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//�α����� ������ �Խñ� �ۼ��ڰ� �������� Ȯ��
$aArray = $oBoard->fetch_array();
if ($aArray['bo_writer'] != $_SESSION['user_id']) {
    echo "<script>";
    echo "    alert('�Խñ� �ۼ��ڸ� ������ �� �ֽ��ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

$iBoSeq    = $aArray['bo_seq'];
$iBoChild  = $aArray['bo_child'];
$iBoParent = $aArray['bo_parent'];
$iBoGrpno  = $aArray['bo_grpno'];

//DBƮ������� ����ϱ� ���� auto_commit ��Ȱ��ȭ
mysqli_autocommit($conn, FALSE);

//������ ���� �ڽı��� ���� ���� Ȯ��
if ($iBoChild > 0) { //�ڽı��� �����Ѵٸ�
    //�ڽı��� �ִٸ� update ���� - �������� �÷� Y�� ����
    $sSql = "UPDATE board SET bo_del = 'Y' WHERE bo_no = ".mysqli_real_escape_string($conn, $iBoNo);
    
    if (mysqli_query($conn, $sSql) === true) {//�������� �÷� update ����
        echo "<script>";
        echo "    alert('�Խñ� ������ �Ϸ�Ǿ����ϴ�.');";
        echo "    window.location.href='./main.php';";
        echo "</script>";
        mysqli_commit($conn);
        mysqli_autocommit($conn, TRUE);
        mysqli_close($conn);
        exit;
    } else {//�������� �÷� update ����
        echo "<script>";
        echo "    alert('�Խñ� ������ �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        mysqli_close($conn);
        exit;
    }
} else {//�ڽı��� �������� �ʴ´ٸ�
    //delete ����
    $sSql = "DELETE FROM board WHERE bo_no = ".mysqli_real_escape_string($conn, $iBoNo);
    
    if (mysqli_query($conn, $sSql) === false) {//�ڽı��� ���� �Խñ� delete ����
        echo "<script>";
        echo "    alert('�Խñ� ������ �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        mysqli_close($conn);
        exit;
    }
}

//�ڽı��� ���� �Խñ� delete ����
//- ���� �׷��� �������ΰ� Y�̰� �ڽı��� ���� �θ�۵� ��ȸ
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
//������ �θ�� ��ȸ ����
$aDelNos  = array();
$aDelSeqs = array();

//������ �Խñ��� seq�� �迭�� ����
$aDelSeqs[] = $iBoSeq;
$sDelNos = "";

//������ �θ�� ��ȸ rows�� 0���� Ŭ��
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
    
    //������������ ������
    rsort($aDelSeqs);
    
    //���� �׷쿡 �ڽı��� ���� �������ΰ� 'Y'�� �Խñ� ���� ����
    $sAllDelete = "
        DELETE
        FROM
            board
        WHERE
            bo_no IN (".$sDelNos.")
    ";
    
    if (mysqli_query($conn, $sAllDelete) === false) { //�θ�۵� ���� ����
        echo "<script>";
        echo "    alert('�Խñ� ������ �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        mysqli_close($conn);
        exit;
    }
}

//������ ��, �θ�۵� ���� ����
//�׷� ���� ���ġ
$sAllSeq = "
    UPDATE board
    SET
        bo_seq = bo_seq -1
    WHERE
        bo_grpno = ".$iBoGrpno." AND
        bo_seq > ?
";

//statement ����
$oStmt = mysqli_prepare($conn, $sAllSeq);

foreach ($aDelSeqs as $sValue) {
    mysqli_stmt_bind_param($oStmt, "s", $sValue);
    if (mysqli_stmt_execute($oStmt) === false) {//�׷� ���� ���ġ ����
        echo "<script>";
        echo "    alert('�Խñ� ������ �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        //statement ����
        mysqli_stmt_close($oStmt);
        mysqli_close($conn);
        exit;
    }
}

//�׷� ���� ���ġ ����
//���۸� ������ ��� - ���� �������� 'Y'
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
if ($oOrigin->num_rows == 0) { //���� - �������� 'N'
    echo "<script>";
    echo "    alert('�Խñ� ������ �Ϸ�Ǿ����ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_commit($conn);
    mysqli_autocommit($conn, TRUE);
    mysqli_close($conn);
    exit;
} else { //���� - �������� 'Y'
    $sDelOrigin = "DELETE FROM board WHERE bo_no = ".$aOrigin['bo_no']." AND bo_grpno = ".$iBoGrpno;
    
    if (mysqli_query($conn, $sDelOrigin) === true) {
        echo "<script>";
        echo "    alert('�Խñ� ������ �Ϸ�Ǿ����ϴ�.');";
        echo "    window.location.href='./main.php';";
        echo "</script>";
        mysqli_commit($conn);
        mysqli_autocommit($conn, TRUE);
        mysqli_close($conn);
        exit;
    } else {
        echo "<script>";
        echo "    alert('�Խñ� ������ �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_rollback($conn);
        //statement ����
        mysqli_stmt_close($oStmt);
        mysqli_close($conn);
        exit;
    }
}
?>