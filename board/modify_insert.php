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

//파라미터 검증
if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('게시글 번호가 누락되었습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
} else if (!isset($_SESSION['user_id'])) {
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

//수정할 게시글 조회
$sQuery = "
    SELECT
        bo_writer
    FROM
        board
    WHERE
        bo_no = ".mysqli_real_escape_string($conn, $iBoNo)." AND
        bo_del = 'N'
";

//존재하는 게시글 번호인지 검증
$oBoard = mysqli_query($conn, $sQuery);
if ($oBoard->num_rows == 0) {
    echo "<script>";
    echo "    alert('존재하지 않은 게시글입니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}


//로그인한 정보와 게시글 작성자가 동일한지 확인
$aArray = $oBoard->fetch_array();
if ($aArray['bo_writer'] != $_SESSION['user_id']) {
    echo "<script>";
    echo "    alert('게시글 작성자만 수정할 수 있습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//비밀글 파라미터 검증
if (!empty($sBoSec)) { //비밀글일 경우
    if (empty($sBoPw)) {
        echo "<script>";
        echo "    alert('비밀글일 경우 게시글 비밀번호는 필수입니다.');";
        echo "    history.back();";
        echo "</script>";
        mysqli_close($conn);
        exit;
    } else {
        $sBoPw = mysqli_real_escape_string($conn, $sBoPw);
        $sHash = password_hash($sBoPw, PASSWORD_BCRYPT);
    }
}else {
    //비밀글이 아닐때 비밀번호 입력했다면 초기화
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

if (mysqli_query($conn, $sSql) === true) { //update 성공
    //비밀글일 경우 수정 완료후 상세 조회를 위한 인증 세션 부여
    if (!empty($sBoSec)) {
        $_SESSION['boardauth'] = $iBoNo;
    }
    
    //작성한 게시글 상세조회로 이동
    echo "<script>";
    echo "    alert('게시글 수정이 완료되었습니다.');";
    echo "    window.location.href='./view.php?no=".$iBoNo."';";
    echo "</script>";
    mysqli_close($conn);
    exit;
} else { //update 실패
    echo "<script>";
    echo "    alert('게시글 수정에 실패했습니다. 잠시 후 다시 시도해주세요.');";
    echo "    history.back();";
    echo "</script>";
    mysqli_close($conn);
    exit;
}
?>