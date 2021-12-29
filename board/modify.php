<?php
session_start();

$sTitleName = '게시글 수정';
include '../resources/dbInfo.php';
include '../resources/header.php';

$iBoNo = !empty($_GET['no']) ? (int)trim($_GET['no']) : "";

if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('게시글 번호가 누락되었습니다.');";
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
        <h2>게시글 수정</h2>
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
                    <th class="th-bg text-center align-middle"><span class="red-color">* </span>제목</th>
                    <td colspan="3" class="border-bottom">
                        <input type="text" name="bo_title" class="form-control" required placeholder="게시글 제목을 입력해주세요." value="<?= $sBoTitle?>">
                        <div class="invalid-feedback">필수입력 사항입니다.</div>
                    </td>
                </tr>
                <tr>
                    <th class="th-bg text-center align-middle">비밀글</th>
                    <td colspan="3" class="border-bottom">
                        <input class="form-conrol" type="checkbox" value="Y" name="bo_sec" <?= $sChecked?>/>
                        <label class="form-check-label">비밀글 </label>
                    </td>
                </tr>
                <tr>
                    <th class="th-bg text-center align-middle">비밀번호</th>
                    <td colspan="3" class="border-bottom">
                        <input type="text" name="bo_pw" class="form-control" placeholder="비밀글의 경우 게시글 비밀번호를 입력해주세요." <?= $sRequired?>>
                        <div class="invalid-feedback">비밀글의 경우 게시글 비밀번호를 입력해야 합니다.</div>
                    </td>
                </tr>
                <tr>
                    <th class="th-bg text-center align-middle"><span class="red-color">* </span>내용</th>
                    <td colspan="3" class="border-bottom">
                        <textarea name="bo_content" class="form-control minH480" required placeholder="게시글 내용을 입력해주세요."><?= $sBoContent?></textarea>
                        <div class="invalid-feedback">필수입력 사항입니다.</div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            <a class="btn btn-secondary mr-2"  href="main.php">목록으로</a>
            <button class="btn btn-primary" id="saveBtn" type="button">저장하기</button>
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

        //저장하기 버튼을 눌렀을 때
        oSaveBtn.on("click", function(){
            //name 속성을 가지고 필수입력사항인 입력태그
            var iCount  = 0;

            //필수입력사항 null 정규식 체크
            if (!titleRgx($("[name='bo_title']"))) iCount++;
            if (!contentRgx($("[name='bo_content']"))) iCount++;
            
            if ($("input[name='bo_pw']").prop("required") === true) {
                if (!pwRgx($("input[name='bo_pw']"))) iCount++;
            }

            //필수입력사항이 null이 아닐때 submit
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
        
        //비밀글 여부 체크시 비밀번호 항목 필수항목으로 변경
        $("[name='bo_sec']").on("change", function() {
            if ($(this).is(":checked")) {
                $("[name='bo_pw']").parents("tr").children("th").prepend('<span class="red-color">* </span>');
                $("[name='bo_pw']").prop({"required": true, "readonly": false});
            } else {
                $("[name='bo_pw']").parents("tr").children("th").text("비밀번호");
                $("[name='bo_pw']").prop({"required": false, "readonly": true}).val("");
                $("[name='bo_pw']").removeClass("is-invalid invalid");
                $("[name='bo_pw']").next().hide();
            }
            return false;
        });

        //비밀글일때만 비밀번호 정규식 체크
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