<?php
session_start();

define('CSSPATH', './resources/css/'); //css ���� ��ġ ����
$sCssItem   = 'signin.css';
$sTitleName = 'sign in';

include './resources/dbInfo.php';
include './resources/header.php';

//���̵����ϱ� ��Ű ���翩�� Ȯ��
$sUserId        = "";
$sRememberCheck = "";
if (isset($_COOKIE['userId'])) {
    $sUserId        = $_COOKIE['userId'];
    $sRememberCheck = "checked";
}

//�α��� ���� Ȯ��
if (isset($_SESSION['is_login'])) {
    //�α��� ���¶�� main.php�� �̵�
    echo "<script>";
    echo "    alert('�̹� �α��� �����Դϴ�.');";
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
            <div class="invalid-feedback text-left">���̵� �Է����ּ���.</div>
        </div>
        <div class="form-floating">
            <input type="password" name="user_pw" class="form-control" placeholder="Password" required>
            <div class="invalid-feedback text-left">��й�ȣ�� �Է����ּ���.</div>
        </div>
        <div class="checkbox mt-4 mb-3">
            <label>
                <input type="checkbox" name="remember" value="Y" <?= $sRememberCheck?>> Remember me
            </label>
        </div>
        <a class="btn btn-secondary" href="user/join.php">ȸ������</a>
        <button class="btn btn-primary" id="loginBtn" type="button">�α���</button>
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

        //name �Ӽ��� ���� ��� �ʼ��Է��±�
        var aInputs = oLoginForm.find(":input[name][required]");
        //�α��ι�ư Ŭ�� �̺�Ʈ
        oLoginBtn.on("click", function() {
            var iCount = 0;

            //�ʼ��Է��±� null ���Խ� üũ
            if (!idRgx($("[name='user_id']"))) iCount++;
            if (!pwRgx($("[name='user_pw']"))) iCount++;
            
            //null�� �ƴϰ� ���ԽĿ� ������� �� submit
            if (iCount == 0) {
                oLoginForm.submit();
            }

            return false;
        });
        
        //id ���Խ�  
        $("[name='user_id']").on("change", function() {
            idRgx($(this));
            return false;
        });

        //��й�ȣ ���Խ�
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
