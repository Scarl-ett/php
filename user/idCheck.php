<?php
include '../resources/dbInfo.php';
include '../resources/config.php';

$sUserId = !empty($_POST['user_id']) ? trim($_POST['user_id']) : "";

$aResp = array('check'=>'FAIL');

//�Ķ���� ����
if (empty($sUserId)) {
    $sResp['check'] = iconv("euc-kr", "utf-8", $sResp['check']);
    echo json_encode($aResp);
    exit;
}

$sSql = "
    SELECT 
        user_id
    FROM 
        user 
    WHERE 
        user_id = '".mysqli_real_escape_string($conn, $sUserId)."'
";

$oData = mysqli_query($conn, $sSql);
$iRow  = mysqli_num_rows($oData);

if ($iRow == 0) {//���̵� �ߺ����� �ʴ´ٸ�
    $aResp['check'] = "OK";
}

$sResp['check'] = iconv("euc-kr", "utf-8", $sResp['check']);
echo json_encode($aResp);
mysqli_close($conn);
?>