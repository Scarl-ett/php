<?php
include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php';

//파라미터 검증
$sUserId     = !empty($_POST['user_id']) ? trim($_POST['user_id']) : "";
$sUserPw     = !empty($_POST['user_pw']) ? trim($_POST['user_pw']) : "";
$sUserName   = !empty($_POST['user_name']) ? trim($_POST['user_name']) : "";
$sUserEmail1 = !empty($_POST['user_email1']) ? trim($_POST['user_email1']) : "";
$sUserEmail2 = !empty($_POST['user_email2']) ? trim($_POST['user_email2']) : "";
$sUserTel    = !empty($_POST['user_tel']) ? trim($_POST['user_tel']) : "";
$sUserZip    = !empty($_POST['user_zip']) ? trim($_POST['user_zip']) : "";
$sUserAddr1  = !empty($_POST['user_addr1']) ? trim($_POST['user_addr1']) : "";
$sUserAddr2  = !empty($_POST['user_addr2']) ? trim($_POST['user_addr2']) : "";

$aParam = array($sUserId, $sUserPw, $sUserName, $sUserEmail1, $sUserEmail2, $sUserTel, $sUserZip, $sUserAddr1, $sUserAddr2);

//null 체크
for ($i = 0; $i < count($aParam); $i++) {
    if (empty($aParam[$i])) {
        echo "<script>";
        echo "    history.back();";
        echo "    alert('필수 입력사항이 누락되었습니다.');";
        echo "</script>";
        exit;
    }
}

$sUserPw    = mysqli_real_escape_string($conn, $sUserPw);
$sHash      = password_hash($sUserPw, PASSWORD_BCRYPT); //비밀번호 암호화
$sUserEmail = mysqli_real_escape_string($conn, $sUserEmail1)."@".mysqli_real_escape_string($conn, $sUserEmail2);

$sSql = "
    INSERT INTO
        user
    SET
        user_id    = '".mysqli_real_escape_string($conn, $sUserId)."',
        user_pw    = '".mysqli_real_escape_string($conn, $sHash)."',
        user_name  = '".mysqli_real_escape_string($conn, $sUserName)."',
        user_email = '".mysqli_real_escape_string($conn, $sUserEmail)."',
        user_tel   = '".mysqli_real_escape_string($conn, $sUserTel)."',
        user_zip   = '".mysqli_real_escape_string($conn, $sUserZip)."',
        user_addr1 = '".mysqli_real_escape_string($conn, $sUserAddr1)."',
        user_addr2 = '".mysqli_real_escape_string($conn, $sUserAddr2)."',
        user_dt    = now()
";

if (mysqli_query($conn, $sSql) === true) { //insert 성공
    echo "<script>";
    echo "    alert('회원가입이 완료되었습니다.');";
    echo "    window.location.href='../';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} else { //insert 실패
    echo "<script>";
    echo "    alert('회원가입에 실패했습니다. 잠시 후 다시 시도해주세요.');";
    echo "    history.back();";
    echo "</script>";
    mysqli_close($conn);
    exit;
}
?>