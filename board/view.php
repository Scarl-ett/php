<?php
session_start();

$sTitleName = '게시글 조회';
include "../resources/dbInfo.php";
include '../resources/header.php';

//로그인 여부 확인
if (!isset($_SESSION['is_login'])) {
    //로그인을 하지 않고 main.php로 왔다면 로그인 화면으로 이동
    echo "<script>";
    echo "    alert('로그인이 필요합니다.');";
    echo "    window.location.href='../';";
    echo "</script>";
    exit;
}

//목록으로 버튼 주소 설정
$sPrevPage = $_SERVER['HTTP_REFERER'];
//정상적인 경로로 상세조회를 한것이 아니라면
if (strpos($sPrevPage, 'main.php') == 0) {
    //목록으로 클릭했을때 리스트 화면으로 이동
    $sPrevPage = "../board/main.php"; 
} else {
    //정상적인 경로로 상세조회를 했다면 뒤로가기
    $sPrevPage = "javascript:window.history.back();";
}

//파라미터 검증
$iBoNo = !empty($_GET['no']) ? (int)trim($_GET['no']) : "";

if (empty($iBoNo)) {
    echo "<script>";
    echo "    alert('게시글 번호가 누락되었습니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    exit;
}

//해당 게시글 번호 존재여부 확인
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
//해당 번호의 게시글이 존재하지 않거나 삭제된 글일때
if ($oBoardCheck->num_rows == 0) {
    echo "<script>";
    echo "    alert('존재하지 않는 게시글입니다.');";
    echo "    window.location.href='./main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

//게시글이 존재할 때
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

//비밀글일 경우 비밀번호 검증 통과여부 확인
if ($aBoard['bo_sec'] == 'Y') {
    if (!isset($_SESSION['boardauth'])) { //인증을 거치지 않았다면
        echo "<script>";
        echo "    alert('비밀글은 비밀번호 확인이 필요합니다.');";
        echo "    window.history.back();";
        echo "</script>";
        mysqli_close($conn);
        exit;
    } else {//인증을 거쳤을때
        //인증은 거쳤지만 해당 게시글의 인증이 아닐때
        if ($_SESSION['boardauth'] != $iBoNo) {
            echo "<script>";
            echo "    alert('비밀글은 비밀번호 확인이 필요합니다.');";
            echo "    window.history.back();";
            echo "</script>";
            mysqli_close($conn);
            exit;
        }

        //인증을 통과했다면 인증내용을 삭제 - 다시 인증 할 수 있게 한다.
        unset($_SESSION['boardauth']);
    }
}
?>
<body class="bg-light">
<div class="container mb-5">
    <div class="py-5 text-center">
        <h2>게시글 조회</h2>
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
                <th class="th-bg text-center align-middle">작성자</th>
                <td><?= $aBoard['user_name']?></td>
                <th class="th-bg text-center align-middle">작성일</th>
                <td><?= $aBoard['bo_date']?></td>
            </tr>
            <tr>
                <th class="th-bg text-center align-middle">제목</th>
                <td colspan="3" class="border-bottom"><?= $aBoard['bo_title']?></td>
            </tr>
        </tbody>
    </table>
    <div class="minH570 bg-white border p-3" style="margin-top: -17px">
        <?= nl2br($aBoard['bo_content'])?>
    </div>
    <div class="d-flex justify-content-center mt-4 btnWrap">
        <a class="btn btn-secondary mr-2"  href="<?= $sPrevPage?>">목록으로</a>
        <a class="btn btn-success mr-2" href="reply.php?no=<?= $iBoNo?>">답글쓰기</a>
        <!-- 로그인한 사람과 작성자가 같으면 수정, 삭제 버튼 노출 -->
        <?php
        if ($aBoard['bo_writer'] == $_SESSION['user_id']) {
            echo '<a class="btn btn-primary mr-2" href="./modify.php?no='.$iBoNo.'">수정</a>';
            
            if ($aBoard['bo_sec'] == 'Y') {
                echo '<a class="btn btn-danger" data-toggle="modal" href="#myModal" id="pwCheck">삭제</a>';
            } else {
                echo '<a class="btn btn-danger" data-toggle="modal" href="#myModal2" id="deleteBtn">삭제</a>';
            }
        }
        ?>
    </div>
</div>

<!-- 비밀글 삭제 모달창 -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">게시글 삭제</h5>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-3 text-center">
                    삭제한 게시글은 복원할 수 없습니다.<br/>
                    게시글 삭제를 원하시면 게시글 비밀번호를 입력하세요.
                </p>
                <form action="pwCheck.php" method="post" id="pwCheckForm">
                    <input type="hidden" name="bo_no" value="<?= $iBoNo?>"/>
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">비밀번호</label>
                        <input type="text" class="form-control" name="bo_pw" />
                        <div class="invalid-feedback">게시글 비밀번호를 입력하세요.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal" aria-label="Close">취소</button>
                <button type="button" class="btn btn-primary" id="pwCheckBtn">확인</button>
            </div>
        </div>
    </div>
</div>

<!-- 일반글 삭제 모달창 -->
<div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">게시글 삭제</h5>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-3 text-center">
                    삭제한 게시글은 복원할 수 없습니다.<br/>
                    게시글을 삭제하시겟습니까?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal" aria-label="Close">취소</button>
                <a class="btn btn-primary" href="./delete.php?no=<?= $iBoNo?>">확인</a>
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
        //비밀글 비밀번호 입력 삭제확인 모달
        var oMyModal     = $("#myModal");
        var oPwCheckBtn  = $("#pwCheckBtn");

        //모달 비밀번호 입력창 enter키 submit 막기
        $('input[name="bo_pw"]').keydown(function(event) {
            if (event.keyCode === 13) { //enter키를 눌렀을때
                event.preventDefault();
            }
        });

        //모달 닫기버튼 클릭시 모달 form 초기화
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

            if (isEmpty($.trim(oBoPw.val()))) { //비밀번호 null체크
                $(oBoPw).addClass("is-invalid");
                bPwPass = false;
            } else { //정규식
                if (!pwRgx(oBoPw)) bPwPass = false;
                else bPwPass = true;
            } 

            //비밀번호가 null이 아니고 정규식을 통과했을 때
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
                        if (resp.check == 'OK') {//비밀번호 통과
                            window.location.href = "./delete.php?no=" + iBoNo;
                        } else {//비밀번호 오류
                            alert("비밀번호가 일치하지 않습니다.");
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

        //비밀번호 변경시 정규식 체크
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