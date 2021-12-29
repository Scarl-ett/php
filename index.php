<?php
session_start();

define('CSSPATH', './resources/css/'); //css 폴더 위치 정의
$sCssItem   = 'signin.css';
$sTitleName = 'sign in';

include './resources/dbInfo.php';
include './resources/header.php';

//아이디기억하기 쿠키 존재여부 확인
$sUserId        = "";
$sRememberCheck = "";
if (isset($_COOKIE['userId'])) {
    $sUserId        = $_COOKIE['userId'];
    $sRememberCheck = "checked";
}

//로그인 여부 확인
if (isset($_SESSION['is_login'])) {
    //로그인 상태라면 main.php로 이동
    echo "<script>";
    echo "    alert('이미 로그인 상태입니다.');";
    echo "    window.location.href='../board/main.php';";
    echo "</script>";
    exit;
}
?>
<body class="text-center">
<main class="form-signin">
    <form class="needs-validation" novalidate method="post" id="loginForm" action="./user/login.php">
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
        <div class="form-floating mb-2">
            <input type="text" name="user_id" class="form-control" placeholder="Id" value="<?= $sUserId?>" required/>
            <div class="invalid-feedback text-left">아이디를 입력해주세요.</div>
        </div>
        <div class="form-floating">
            <input type="password" name="user_pw" class="form-control" placeholder="Password" required>
            <div class="invalid-feedback text-left">비밀번호를 입력해주세요.</div>
        </div>
        <div class="checkbox mt-4 mb-3">
            <label>
                <input type="checkbox" name="remember" value="Y" <?= $sRememberCheck?>> Remember me
            </label>
        </div>
        <a class="btn btn-secondary" href="user/join.php">회원가입</a>
        <button class="btn btn-primary" id="loginBtn" type="button">로그인</button>
    </form>
</main>
<?php 
include './resources/postScript.php';
?>
<script src="./resources/js/regex.js"></script>
<script type="text/javascript">
    $(function() {
        var oLoginForm = $("#loginForm");
        var oLoginBtn  = $("#loginBtn");

        //name 속성을 가진 모든 필수입력태그
        var aInputs = oLoginForm.find(":input[name][required]");
        //로그인버튼 클릭 이벤트
        oLoginBtn.on("click", function() {
            var iCount = 0;

            //필수입력태그 null 정규식 체크
            if (!idRgx($("[name='user_id']"))) iCount++;
            if (!pwRgx($("[name='user_pw']"))) iCount++;
            
            //null이 아니고 정규식에 통과했을 때 submit
            if (iCount == 0) {
                oLoginForm.submit();
            }

            return false;
        });
        
        //id 정규식  
        $("[name='user_id']").on("change", function() {
            idRgx($(this));
            return false;
        });

        //비밀번호 정규식
        $("input[name='user_pw']").on("change", function() {
            pwRgx($(this));
            return false;
        });
    });
</script>
</body>
<?php
include './resources/footer.php'; 
?>
