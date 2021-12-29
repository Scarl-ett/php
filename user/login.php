<?php
session_start();

include '../resources/dbInfo.php';
include '../resources/config.php';
include '../resources/password.php'; //php 5.3.7 이상부터 내장함수

//파라미터 검증
$sUserId   = !empty($_POST['user_id']) ? trim($_POST['user_id']) : "";
$sUserPw   = !empty($_POST['user_pw']) ? trim($_POST['user_pw']) : "";
$sRemember = !empty($_POST['remember']) ? trim($_POST['remember']) : "";

//아이디, 비밀번호 null 체크
if (empty($sUserId)) {
    echo "<script>";
    echo "    alert('아이디를 입력해주세요.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
} else if (empty($sUserPw)) {
    echo "<script>";
    echo "    alert('비밀번호를 입력해주세요.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
}

//아이디 정보 조회
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

if ($iRow == 0) { //아이디 존재여부 확인
    echo "<script>";
    echo "    alert('입력하신 아이디는 존재하지 않습니다.');";
    echo "    window.location.href='../';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} 

//아이디가 존재할때
$aArray  = mysqli_fetch_array($oData);
//입력한 비밀번호와 암호화된 비밀번호가 동일한지 확인
$sUserPw = mysqli_real_escape_string($conn, $sUserPw);
$bVerify = password_verify($sUserPw, $aArray['user_pw']);
if ($bVerify) { //비밀번호 동일
    //로그인 정보 세션저장
    $_SESSION['is_login']  = true;
    $_SESSION['user_id']   = $sUserId;
    $_SESSION['user_name'] = $aArray['user_name'];
    
    //아이디 기억하기 체크했을 때
    if (!empty($sRemember)) {
        //아이디 쿠키 저장
        setcookie('userId', $sUserId, time() + 86400 * 30, "/");
    } else {//아이디 기억하기 해제
        //쿠키가 존재한다면
        if (isset($_COOKIE['userId'])) {
            //쿠키삭제
            setcookie('userId', $sUserId, time() - 86400 * 30, "/");
            unset($_COOKIE['userId']);
        }
    }
    echo "<script>";
    echo "    window.location.href='../board/main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} else { //비밀번호 비일치
    echo "<script>";
    echo "    alert('비밀번호가 일치하지 않습니다.');";
    echo "    window.location.href='../';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}//비밀번호 일치여부 end
?>