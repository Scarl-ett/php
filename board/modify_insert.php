<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php';

$iBoNo      = trim($_POST['bo_no']);
$sBoTitle   = !empty($_POST['bo_title']) ? trim($_POST['bo_title']) : "";
$sBoContent = !empty($_POST['bo_content']) ? $_POST['bo_content'] : "";
$sBoSec     = !empty($_POST['bo_sec']) ? trim($_POST['bo_sec']) : "";
$sBoPw      = !empty($_POST['bo_pw']) ? trim($_POST['bo_pw']) : "";

//�Ķ���� ����
if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('�Խñ� ��ȣ�� �����Ǿ����ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
} else if (!isset($_SESSION['user_id'])) {
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

//������ �Խñ� ��ȸ
$sQuery = "
    SELECT
        bo_writer
    FROM
        board
    WHERE
        bo_no = ".mysqli_real_escape_string($conn, $iBoNo)." AND
        bo_del = 'N'
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

//��б� �Ķ���� ����
if (!empty($sBoSec)) { //��б��� ���
    if (empty($sBoPw)) {
        echo "<script>";
        echo "    alert('��б��� ��� �Խñ� ��й�ȣ�� �ʼ��Դϴ�.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_close($conn);
        exit;
    } else {
        $sBoPw = mysqli_real_escape_string($conn, $sBoPw);
        $sHash = password_hash($sBoPw, PASSWORD_BCRYPT);
    }
}else {
    //��б��� �ƴҶ� ��й�ȣ �Է��ߴٸ� �ʱ�ȭ
    $sBoSec = 'N';
    $sBoPw = "";
}

$sSql = "
    UPDATE board
    SET
        bo_title   = '".mysqli_real_escape_string($conn, $sBoTitle)."',
        bo_content = '".mysqli_real_escape_string($conn, $sBoContent)."',
        bo_sec     = '".mysqli_real_escape_string($conn, $sBoSec)."',
        bo_pw      = '".mysqli_real_escape_string($conn, $sHash)."'
    WHERE
        bo_no = ".mysqli_real_escape_string($conn, $iBoNo)
;

if (mysqli_query($conn, $sSql) === true) { //update ����
    //��б��� ��� ���� �Ϸ��� �� ��ȸ�� ���� ���� ���� �ο�
    if (!empty($sBoSec)) {
        $_SESSION['boardauth'] = $iBoNo;
    }
    
    //�ۼ��� �Խñ� ����ȸ�� �̵�
    echo "<script>";
    echo "    alert('�Խñ� ������ �Ϸ�Ǿ����ϴ�.');";
    echo "    window.location.href='./view.php?no=".$iBoNo."';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} else { //update ����
    echo "<script>";
    echo "    alert('�Խñ� ������ �����߽��ϴ�. ��� �� �ٽ� �õ����ּ���.');";
    echo "    history.back();";
    echo "</script>";
    mysqli_close($conn);
    exit;
}
?>