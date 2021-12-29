<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php';

$sBoPw = !empty($_POST['bo_pw']) ? trim($_POST['bo_pw']) : "";
$iBoNo = !empty($_POST['bo_no']) ? (int)trim($_POST['bo_no']) : "";

$aResp = array('check'=>'FAIL');

//�Ķ���� ����
if (empty($sBoPw)) {
    $sResp['check'] = iconv("euc-kr", "utf-8", $sResp['check']);
    echo json_encode($aResp);
    exit;
} 

//�Խñ� ��ȸ
$sSql = "
    SELECT
        bo_pw
    FROM
        board
    WHERE
        bo_no = ".mysqli_real_escape_string($conn, $iBoNo)
;

$oData   = mysqli_query($conn, $sSql);
$aArray  = $oData->fetch_array();
$bVerify = password_verify($sBoPw, $aArray['bo_pw']);
//�Խñ� ��й�ȣ ��ġ����
if ($bVerify) {
    $aResp['check'] = "OK";
    //��б� ��ȸ�� ��й�ȣ ���� ������� Ȯ�ο�
    $_SESSION['boardauth'] = $iBoNo;
}

$sResp['check'] = iconv("euc-kr", "utf-8", $sResp['check']);
echo json_encode($aResp);
mysqli_close($conn);
?>