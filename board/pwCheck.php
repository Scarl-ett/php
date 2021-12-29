<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php';

$sBoPw = !empty($_POST['bo_pw']) ? trim($_POST['bo_pw']) : "";
$iBoNo = !empty($_POST['bo_no']) ? (int)trim($_POST['bo_no']) : "";

$aResp = array('check'=>'FAIL');

//파라미터 검증
if (empty($sBoPw)) {
    $sResp['check'] = iconv("euc-kr", "utf-8", $sResp['check']);
    echo json_encode($aResp);
    exit;
} 

//게시글 조회
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
//게시글 비밀번호 일치여부
if ($bVerify) {
    $aResp['check'] = "OK";
    //비밀글 조회시 비밀번호 검증 통과여부 확인용
    $_SESSION['boardauth'] = $iBoNo;
}

$sResp['check'] = iconv("euc-kr", "utf-8", $sResp['check']);
echo json_encode($aResp);
mysqli_close($conn);
?>