<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php';

//�Ķ���� ����
$iParentBoNo = !empty($_POST['bo_parent']) ? (int)trim($_POST['bo_parent']) : "";
$sBoWriter   = !empty($_POST['bo_writer']) ? trim($_POST['bo_writer']) : "";
$sBoTitle    = !empty($_POST['bo_title']) ? trim($_POST['bo_title']) : "";
$sBoContent  = !empty($_POST['bo_content']) ? $_POST['bo_content'] : "";
$sBoSec      = !empty($_POST['bo_sec']) ? trim($_POST['bo_sec']) : "";
$sBoPw       = !empty($_POST['bo_pw']) ? trim($_POST['bo_pw']) : "";

//�θ�Խñ��� �������� �ʴ� �Խù��϶�
if (empty($iParentBoNo)) {
    echo "<script>";
    echo "    alert('�θ� �Խñ��� �������� �ʽ��ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
}

//�ش� �Խñ� ��ȣ�� �θ�� ���翩�� Ȯ��
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
//�ش� ��ȣ�� �θ� �Խñ��� �������� ���� ��
if ($oParentBoard->num_rows == 0) {
    echo "<script>";
    echo "    alert('�������� ���� �Խù����� ����� �ۼ��� �� �����ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//�ش� ��ȣ�� �θ� �Խñ��� ������ ��
$aParent    = $oParentBoard->fetch_array();
$iBoGrpno   = $aParent['bo_grpno'];
$iParentSeq = $aParent['bo_seq']; 
$iBoDepth   = $aParent['bo_depth'] + 1;
$iBoSeq     = $iParentSeq + 1;

//�Ķ���� ����
if (empty($sBoWriter)) {
    echo "<script>";
    echo "    alert('�α����� �ʿ��մϴ�.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
} else if (empty($sBoTitle)) {
    echo "<script>";
    echo "    alert('������ �Է����ּ���.');";
    echo "    history.back();";
    echo "</script>";
    exit;
} else if (empty($sBoContent)) {
    echo "<script>";
    echo "    alert('������ �Է����ּ���.');";
    echo "    history.back();";
    echo "</script>";
    exit;
}

//�θ���� ��б��� ��� - �θ���� ��й�ȣ�� ����� ��й�ȣ�� �������� Ȯ��
if ($aParent['bo_sec'] == 'Y') {
    $bVerify = password_verify($sBoPw, $aParent['bo_pw']);
    //�Խñ� ��й�ȣ ��ġ����
    if (!$bVerify) {
        echo "<script>";
        echo "    alert('������ ��й�ȣ�� �������� �ʽ��ϴ�. ��й�ȣ�� �ٽ� �Է��ϼ���.');";
        echo "    history.back();";
        echo "</script>";
        exit;
    }
}

//��б� �Ķ���� ����
if (!empty($sBoSec)) { //��б��� ���
    if (empty($sBoPw)) {//��б��ε� ��й�ȣ�� ���� ���
        echo "<script>";
        echo "    alert('��б��� ��� �Խñ� ��й�ȣ�� �ʼ��Դϴ�.');";
        echo "    history.back();";
        echo "</script>";
        exit;
    } else {
        $sBoPw = mysqli_real_escape_string($conn, $sBoPw);
        $sHash = password_hash($sBoPw, PASSWORD_BCRYPT);
    }
}else {
    //��б��� �ƴҶ� ��й�ȣ �Է��ߴٸ� �ʱ�ȭ
    $sBoSec = 'N';
    $sHash = "";
}

//DBƮ������� ����ϱ� ���� auto_commit ��Ȱ��ȭ
mysqli_autocommit($conn, FALSE);

//�׷쳻 ���� ����
$sUpdate = "
    UPDATE board
    SET
        bo_seq = bo_seq + 1
    WHERE
        bo_grpno = ".$iBoGrpno." AND
        bo_seq > ".$iParentSeq
;

if (mysqli_query($conn, $sUpdate) === false) { //update ����
    echo "<script>";
    echo "    alert('��� �ۼ��� �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
    echo "    history.back();";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//update ����
//insert - ���
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

if (mysqli_query($conn, $sSql) === true) { //insert ����
    //�ڵ� ������ id �� ������ ������ ���� id�� ��ȯ
    $iCurrentNo = mysqli_insert_id($conn);
    
    //��б��� ��� ���ۼ� �Ϸ� �� �� ��ȸ�� ���� ���� ���� �ο�
    if (!empty($sBoSec)) {
        $_SESSION['boardauth'] = $iCurrentNo;
    }
    
    //�ۼ��� �Խñ� ����ȸ�� �̵�
    echo "<script>";
    echo "    alert('�Խñ� �ۼ��� �Ϸ�Ǿ����ϴ�.');";
    echo "    window.location.href='./view.php?no=".$iCurrentNo."';";
    echo "</script>";
    
    //update, insert, update ��� �������� �� ���� commit
    mysqli_commit($conn);
    mysqli_autocommit($conn, TRUE);
    mysqli_close($conn);
    exit;
} else { //insert ����
    echo "<script>";
    echo "    alert('��� �ۼ��� �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.dd');";
    echo "    history.back();";
    echo "</script>";
    
    //update�� �����ϸ� insert, update ���� rollback
    mysqli_rollback($conn);
    mysqli_close($conn);
    exit;
}
?>