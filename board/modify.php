<?php
session_start();

$sTitleName = '�Խñ� ����';
include '../resources/dbInfo.php';
include '../resources/header.php';

$iBoNo = !empty($_GET['no']) ? (int)trim($_GET['no']) : "";

if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('�Խñ� ��ȣ�� �����Ǿ����ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
}

$sQuery = "
    SELECT
        bo_writer,
        bo_title,
        bo_sec,
        bo_content
    FROM
        board
    WHERE
        bo_no  = ".mysqli_real_escape_string($conn, $iBoNo)." AND
        bo_del = 'N'
";

//�����ϴ� �Խñ� ��ȣ���� ����
$oBoard = mysqli_query($conn, $sQuery);
if ($oBoard->num_rows == 0) {
    echo "<script>";
    echo "    alert('�������� ���� �Խñ��Դϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//�α����� ������ �Խñ� �ۼ��ڰ� �������� Ȯ��
$aArray = $oBoard->fetch_array(); 
if ($aArray['bo_writer'] != $_SESSION['user_id']) {
    echo "<script>";
    echo "    alert('�Խñ� �ۼ��ڸ� ������ �� �ֽ��ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

$sChecked  = "";
$sRequired = "";
if ($aArray['bo_sec'] == 'Y') {
    $sChecked  = 'checked';
    $sRequired = 'required';
} else {
    $sRequired = 'readonly';
}

$sBoTitle   = $aArray['bo_title'];
$sBoSec     = $aArray['bo_sec'];
$sBoContent = $aArray['bo_content'];
?>
<body class="bg-light">
<div class="container mb-5">
    <div class="py-5 text-center">
        <h2>�Խñ� ����</h2>
    </div>
    
    <form class="needs-validation" novalidate action="modify_insert.php" method="post" id="writeForm">
        <input type="hidden" value="<?= $iBoNo?>" name="bo_no" />
        <table class="table bg-white border">
            <colgroup>
                <col width="20%" />
                <col width="80%" />
            </colgroup>
            <tbody>
                <tr>
                    <th class="th-bg text-center align-middle"><span class="red-color">* </span>����</th>
                    <td colspan="3" class="border-bottom">
                        <input type="text" name="bo_title" class="form-control" required placeholder="�Խñ� ������ �Է����ּ���." value="<?= $sBoTitle?>">
                        <div class="invalid-feedback">�ʼ��Է� �����Դϴ�.</div>
                    </td>
                </tr>
                <tr>
                    <th class="th-bg text-center align-middle">��б�</th>
                    <td colspan="3" class="border-bottom">
                        <input class="form-conrol" type="checkbox" value="Y" name="bo_sec" <?= $sChecked?>/>
                        <label class="form-check-label">��б� </label>
                    </td>
                </tr>
                <tr>
                    <th class="th-bg text-center align-middle">��й�ȣ</th>
                    <td colspan="3" class="border-bottom">
                        <input type="text" name="bo_pw" class="form-control" placeholder="��б��� ��� �Խñ� ��й�ȣ�� �Է����ּ���." <?= $sRequired?>>
                        <div class="invalid-feedback">��б��� ��� �Խñ� ��й�ȣ�� �Է��ؾ� �մϴ�.</div>
                    </td>
                </tr>
                <tr>
                    <th class="th-bg text-center align-middle"><span class="red-color">* </span>����</th>
                    <td colspan="3" class="border-bottom">
                        <textarea name="bo_content" class="form-control minH480" required placeholder="�Խñ� ������ �Է����ּ���."><?= $sBoContent?></textarea>
                        <div class="invalid-feedback">�ʼ��Է� �����Դϴ�.</div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            <a class="btn btn-secondary mr-2"  href="main.php">�������</a>
            <button class="btn btn-primary" id="saveBtn" type="button">�����ϱ�</button>
        </div>
    </form>
</div>
<?php 
include '../resources/postScript.php';
?>
<script src="../resources/js/regex.js"></script>
<script type="text/javascript">
    $(function() {
        var oWriteForm = $("#writeForm");
        var oSaveBtn   = $("#saveBtn");

        //�����ϱ� ��ư�� ������ ��
        oSaveBtn.on("click", function(){
            //name �Ӽ��� ������ �ʼ��Է»����� �Է��±�
            var iCount  = 0;

            //�ʼ��Է»��� null ���Խ� üũ
            if (!titleRgx($("[name='bo_title']"))) iCount++;
            if (!contentRgx($("[name='bo_content']"))) iCount++;
            
            if ($("input[name='bo_pw']").prop("required") === true) {
                if (!pwRgx($("input[name='bo_pw']"))) iCount++;
            }

            //�ʼ��Է»����� null�� �ƴҶ� submit
            if (iCount == 0) {
                oWriteForm.submit();
            }
            return false;
        });

        $("[name='bo_title']").on("change", function() {
            titleRgx($(this));
            return false;
        });

        $("[name='bo_content']").on("change", function() {
            contentRgx($(this));
            return false;
        });
        
        //��б� ���� üũ�� ��й�ȣ �׸� �ʼ��׸����� ����
        $("[name='bo_sec']").on("change", function() {
            if ($(this).is(":checked")) {
                $("[name='bo_pw']").parents("tr").children("th").prepend('<span class="red-color">* </span>');
                $("[name='bo_pw']").prop({"required": true, "readonly": false});
            } else {
                $("[name='bo_pw']").parents("tr").children("th").text("��й�ȣ");
                $("[name='bo_pw']").prop({"required": false, "readonly": true}).val("");
                $("[name='bo_pw']").removeClass("is-invalid invalid");
                $("[name='bo_pw']").next().hide();
            }
            return false;
        });

        //��б��϶��� ��й�ȣ ���Խ� üũ
        $("input[name='bo_pw']").on("change", function() {
            if ($(this).prop("required", true)) {
                pwRgx($(this));
            }
            return false;
        });
	});
</script>
</body>
<?php
include '../resources/footer.php';
?>>