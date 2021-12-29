<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php';

$sBoWriter  = !empty($_POST['bo_writer']) ? trim($_POST['bo_writer']) : "";
$sBoTitle   = !empty($_POST['bo_title']) ? trim($_POST['bo_title']) : "";
$sBoContent = !empty($_POST['bo_content']) ? $_POST['bo_content'] : "";
$sBoSec     = !empty($_POST['bo_sec']) ? trim($_POST['bo_sec']) : "";
$sBoPw      = !empty($_POST['bo_pw']) ? trim($_POST['bo_pw']) : "";

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

//ó�� ���� ���� ��� max���� null�̱� ������ IFNULL�� ó��
$sSql = "
    INSERT INTO
        board
    SET
        bo_grpno   = (SELECT IFNULL(MAX(bo_grpno) + 1, 1) FROM board b),
        bo_seq     = 0,
        bo_depth   = 1,
        bo_parent  = null,
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
    mysqli_close($conn);
    exit;
} else { //insert ����
    echo "<script>";
    echo "    alert('�Խñ� �ۼ��� �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
    echo "    history.back();";
    echo "</script>";
    mysqli_close($conn);
    exit;
}
?>