<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php'; //php 5.3.7 �̻���� �����Լ�

//�Ķ���� ����
$sUserId   = !empty($_POST['user_id']) ? trim($_POST['user_id']) : "";
$sUserPw   = !empty($_POST['user_pw']) ? trim($_POST['user_pw']) : "";
$sRemember = !empty($_POST['remember']) ? trim($_POST['remember']) : "";

//���̵�, ��й�ȣ null üũ
if (empty($sUserId)) {
    echo "<script>";
    echo "    alert('���̵� �Է����ּ���.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
} else if (empty($sUserPw)) {
    echo "<script>";
    echo "    alert('��й�ȣ�� �Է����ּ���.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
}

//���̵� ���� ��ȸ
$sSql = "
    SELECT 
        user_id,
        user_pw,
        user_name
    FROM 
        user 
    WHERE 
        user_id = '".mysqli_real_escape_string($conn, $sUserId)."'
";

$oData = mysqli_query($conn, $sSql);
$iRow  = mysqli_num_rows($oData);

if ($iRow == 0) { //���̵� ���翩�� Ȯ��
    echo "<script>";
    echo "    alert('�Է��Ͻ� ���̵�� �������� �ʽ��ϴ�.');";
    echo "    window.location.href='../';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} 

//���̵� �����Ҷ�
$aArray  = mysqli_fetch_array($oData);
//�Է��� ��й�ȣ�� ��ȣȭ�� ��й�ȣ�� �������� Ȯ��
$sUserPw = mysqli_real_escape_string($conn, $sUserPw);
$bVerify = password_verify($sUserPw, $aArray['user_pw']);
if ($bVerify) { //��й�ȣ ����
    //�α��� ���� ��������
    $_SESSION['is_login']  = true;
    $_SESSION['user_id']   = $sUserId;
    $_SESSION['user_name'] = $aArray['user_name'];
    
    //���̵� ����ϱ� üũ���� ��
    if (!empty($sRemember)) {
        //���̵� ��Ű ����
        setcookie('userId', $sUserId, time() + 86400 * 30, "/");
    } else {//���̵� ����ϱ� ����
        //��Ű�� �����Ѵٸ�
        if (isset($_COOKIE['userId'])) {
            //��Ű����
            setcookie('userId', $sUserId, time() - 86400 * 30, "/");
            unset($_COOKIE['userId']);
        }
    }
    echo "<script>";
    echo "    window.location.href='../board/main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} else { //��й�ȣ ����ġ
    echo "<script>";
    echo "    alert('��й�ȣ�� ��ġ���� �ʽ��ϴ�.');";
    echo "    window.location.href='../';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}//��й�ȣ ��ġ���� end
?>