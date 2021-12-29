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

//파라미터 검증
if (empty($sBoWriter)) {
    echo "<script>";
    echo "    alert('로그인이 필요합니다.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
} else if (empty($sBoTitle)) {
    echo "<script>";
    echo "    alert('제목을 입력해주세요.');";
    echo "    history.back();";
    echo "</script>";
    exit;
} else if (empty($sBoContent)) {
    echo "<script>";
    echo "    alert('내용을 입력해주세요.');";
    echo "    history.back();";
    echo "</script>";
    exit;
}

//비밀글 파라미터 검증
if (!empty($sBoSec)) { //비밀글일 경우
    if (empty($sBoPw)) {//비밀글인데 비밀번호가 없을 경우
        echo "<script>";
        echo "    alert('비밀글일 경우 게시글 비밀번호는 필수입니다.');";
        echo "    history.back();";
        echo "</script>";
        exit;
    } else {
        $sBoPw = mysqli_real_escape_string($conn, $sBoPw);
        $sHash = password_hash($sBoPw, PASSWORD_BCRYPT);
    }
}else {
    //비밀글이 아닐때 비밀번호 입력했다면 초기화
    $sBoSec = 'N';
    $sHash = "";
}

//처음 들어가는 값의 경우 max값이 null이기 때문에 IFNULL로 처리
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

if (mysqli_query($conn, $sSql) === true) { //insert 성공
    //자동 생성된 id 중 마지막 쿼리에 사용된 id를 반환
    $iCurrentNo = mysqli_insert_id($conn);
    
    //비밀글일 경우 글작성 완료 후 상세 조회를 위한 인증 세션 부여
    if (!empty($sBoSec)) {
        $_SESSION['boardauth'] = $iCurrentNo;
    }
    
    //작성한 게시글 상세조회로 이동
    echo "<script>";
    echo "    alert('게시글 작성이 완료되었습니다.');";
    echo "    window.location.href='./view.php?no=".$iCurrentNo."';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} else { //insert 실패
    echo "<script>";
    echo "    alert('게시글 작성에 실패했습니다. 잠시 후 다시 시도해주세요.');";
    echo "    history.back();";
    echo "</script>";
    mysqli_close($conn);
    exit;
}
?>