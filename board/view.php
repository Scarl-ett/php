<?php
session_start();

$sTitleName = '�Խñ� ��ȸ';
include "../resources/dbInfo.php";
include '../resources/header.php';

//�α��� ���� Ȯ��
if (!isset($_SESSION['is_login'])) {
    //�α����� ���� �ʰ� main.php�� �Դٸ� �α��� ȭ������ �̵�
    echo "<script>";
    echo "    alert('�α����� �ʿ��մϴ�.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
}

//������� ��ư �ּ� ����
$sPrevPage = $_SERVER['HTTP_REFERER'];
//�������� ��η� ����ȸ�� �Ѱ��� �ƴ϶��
if (strpos($sPrevPage, 'main.php') == 0) {
    //������� Ŭ�������� ����Ʈ ȭ������ �̵�
    $sPrevPage = "../board/main.php"; 
} else {
    //�������� ��η� ����ȸ�� �ߴٸ� �ڷΰ���
    $sPrevPage = "javascript:window.history.back();";
}

//�Ķ���� ����
$iBoNo = !empty($_GET['no']) ? (int)trim($_GET['no']) : "";

if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('�Խñ� ��ȣ�� �����Ǿ����ϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
}

//�ش� �Խñ� ��ȣ ���翩�� Ȯ��
$sQuery = "
    SELECT
        bo_del
    FROM
        board
    WHERE
        bo_no  = ".mysqli_real_escape_string($conn, $iBoNo)." AND
        bo_del = 'N'
";

$oBoardCheck = mysqli_query($conn, $sQuery);
$aArray      = $oBoardCheck->fetch_array();
//�ش� ��ȣ�� �Խñ��� �������� �ʰų� ������ ���϶�
if ($oBoardCheck->num_rows == 0) {
    echo "<script>";
    echo "    alert('�������� �ʴ� �Խñ��Դϴ�.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//�Խñ��� ������ ��
$sSql = "
    SELECT
        a.bo_no,
        a.bo_title,
        a.bo_writer,
        a.bo_content,
        DATE_FORMAT(a.bo_date, '%Y-%m-%d') bo_date,
        a.bo_sec,
        b.user_name
    FROM
        board a
        INNER JOIN user b
        ON (a.bo_writer = b.user_id)
    WHERE
        bo_no = ".mysqli_real_escape_string($conn, $iBoNo)."
";

$oData = mysqli_query($conn, $sSql);
$aBoard = $oData->fetch_array();

//��б��� ��� ��й�ȣ ���� ������� Ȯ��
if ($aBoard['bo_sec'] == 'Y') {
    if (!isset($_SESSION['boardauth'])) { //������ ��ġ�� �ʾҴٸ�
        echo "<script>";
        echo "    alert('��б��� ��й�ȣ Ȯ���� �ʿ��մϴ�.');";
        echo "    window.history.back();";
        echo "</script>";
        mysqli_close($conn);
        exit;
    } else {//������ ��������
        //������ �������� �ش� �Խñ��� ������ �ƴҶ�
        if ($_SESSION['boardauth'] != $iBoNo) {
            echo "<script>";
            echo "    alert('��б��� ��й�ȣ Ȯ���� �ʿ��մϴ�.');";
            echo "    window.history.back();";
            echo "</script>";
            mysqli_close($conn);
            exit;
        }

        //������ ����ߴٸ� ���������� ���� - �ٽ� ���� �� �� �ְ� �Ѵ�.
        unset($_SESSION['boardauth']);
    }
}
?>
<body class="bg-light">
<div class="container mb-5">
    <div class="py-5 text-center">
        <h2>�Խñ� ��ȸ</h2>
    </div>
        
    <table class="table bg-white border">
        <colgroup>
            <col width="20%" />
            <col width="20%" />
            <col width="20%" />
            <col width="20%" />
        </colgroup>
        <tbody>
            <tr>
                <th class="th-bg text-center align-middle">�ۼ���</th>
                <td><?= $aBoard['user_name']?></td>
                <th class="th-bg text-center align-middle">�ۼ���</th>
                <td><?= $aBoard['bo_date']?></td>
            </tr>
            <tr>
                <th class="th-bg text-center align-middle">����</th>
                <td colspan="3" class="border-bottom"><?= $aBoard['bo_title']?></td>
            </tr>
        </tbody>
    </table>
    <div class="minH570 bg-white border p-3" style="margin-top: -17px">
        <?= nl2br($aBoard['bo_content'])?>
    </div>
    <div class="d-flex justify-content-center mt-4 btnWrap">
        <a class="btn btn-secondary mr-2"  href="<?= $sPrevPage?>">�������</a>
        <a class="btn btn-success mr-2" href="reply.php?no=<?= $iBoNo?>">��۾���</a>
        <!-- �α����� ����� �ۼ��ڰ� ������ ����, ���� ��ư ���� -->
        <?php
        if ($aBoard['bo_writer'] == $_SESSION['user_id']) {
            echo '<a class="btn btn-primary mr-2" href="./modify.php?no='.$iBoNo.'">����</a>';
            
            if ($aBoard['bo_sec'] == 'Y') {
                echo '<a class="btn btn-danger" data-toggle="modal" href="#myModal" id="pwCheck">����</a>';
            } else {
                echo '<a class="btn btn-danger" data-toggle="modal" href="#myModal2" id="deleteBtn">����</a>';
            }
        }
        ?>
    </div>
</div>

<!-- ��б� ���� ���â -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">�Խñ� ����</h5>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-3 text-center">
                    ������ �Խñ��� ������ �� �����ϴ�.<br/>
                    �Խñ� ������ ���Ͻø� �Խñ� ��й�ȣ�� �Է��ϼ���.
                </p>
                <form action="pwCheck.php" method="post" id="pwCheckForm">
                    <input type="hidden" name="bo_no" value="<?= $iBoNo?>"/>
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">��й�ȣ</label>
                        <input type="text" class="form-control" name="bo_pw" />
                        <div class="invalid-feedback">�Խñ� ��й�ȣ�� �Է��ϼ���.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal" aria-label="Close">���</button>
                <button type="button" class="btn btn-primary" id="pwCheckBtn">Ȯ��</button>
            </div>
        </div>
    </div>
</div>

<!-- �Ϲݱ� ���� ���â -->
<div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">�Խñ� ����</h5>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-3 text-center">
                    ������ �Խñ��� ������ �� �����ϴ�.<br/>
                    �Խñ��� �����Ͻðٽ��ϱ�?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal" aria-label="Close">���</button>
                <a class="btn btn-primary" href="./delete.php?no=<?= $iBoNo?>">Ȯ��</a>
            </div>
        </div>
    </div>
</div>
<?php 
include '../resources/postScript.php';
?>
<script src="../resources/js/regex.js"></script>
<script type="text/javascript">
    $(function() {
        //��б� ��й�ȣ �Է� ����Ȯ�� ���
        var oMyModal     = $("#myModal");
        var oPwCheckBtn  = $("#pwCheckBtn");

        //��� ��й�ȣ �Է�â enterŰ submit ����
        $('input[name="bo_pw"]').keydown(function(event) {
            if (event.keyCode === 13) { //enterŰ�� ��������
                event.preventDefault();
            }
        });

        //��� �ݱ��ư Ŭ���� ��� form �ʱ�ȭ
        $(".btn-close").on("click", function() {
            $(".modal").modal("hide");
            $(".modal").find("[name='bo_pw']").removeClass("is-invalid is-valid invalid valid").val("");
            $(".modal").find("[name='bo_pw']").next().hide();
            return false;
        });
        
        oPwCheckBtn.on("click", function() {
            var oBoPw   = $("[name='bo_pw']");
            var iBoNo   = $.trim($("[name='bo_no']").val());
            var bPwPass = false;

            if (isEmpty($.trim(oBoPw.val()))) { //��й�ȣ nullüũ
                $(oBoPw).addClass("is-invalid");
                bPwPass = false;
            } else { //���Խ�
                if (!pwRgx(oBoPw)) bPwPass = false;
                else bPwPass = true;
            } 

            //��й�ȣ�� null�� �ƴϰ� ���Խ��� ������� ��
            if (bPwPass) {
                $.ajax({
                    url : "./pwCheck.php",
                    method : "post",
                    data : {
                        bo_pw : $.trim(oBoPw.val()),
                        bo_no : iBoNo
                    },
                    dataType : "json",
                    success : function(resp) {
                        if (resp.check == 'OK') {//��й�ȣ ���
                            window.location.href = "./delete.php?no=" + iBoNo;
                        } else {//��й�ȣ ����
                            alert("��й�ȣ�� ��ġ���� �ʽ��ϴ�.");
                            oBoPw.removeClass("is-valid valid").addClass("is-invalid invalid").val("");
                            oBoPw.focus();
                        }
                    },
                    error : function(xhr, error, msg) {
                        console.log(xhr);
                        console.log(error);
                        console.log(msg);
                    }
                });
            } 
            return false;
        });

        //��й�ȣ ����� ���Խ� üũ
        $("input[name='bo_pw']").on("change", function() {
            pwRgx($(this));
            return false;
        });
    });
</script>
</body>
<?php
include '../resources/footer.php';
?>