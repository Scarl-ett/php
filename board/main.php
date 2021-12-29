<?php
session_start();

$sTitleName = 'main';
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
?>
<body class="bg-light">
<div class="container mb-5">
    <div class="py-4 d-flex justify-content-center">
        <h4 class="mr-5">환영합니다. <?= $_SESSION['user_name']?>님</h4>
        <button class="btn btn-primary btn-sm" onclick="location.href='../user/logout.php'">로그아웃</button>
    </div>
    <div class="bg-white p-4">
        <form id="searchForm" action="./list.php">
            <input type="hidden" name="page" value=""/>
            <div class="row mb-4 d-flex justify-content-end">
                <div class="col-md-2">
                    <select class="form-control" name="screenSize">
                        <option value="10">10개씩 보기</option>
                        <option value="20">20개씩 보기</option>
                        <option value="30">30개씩 보기</option>
                        <option value="40">40개씩 보기</option>
                        <option value="50">50개씩 보기</option>
                    </select>
                </div>
            </div>
            <div class="minH570">
                <table class="table table-hover border-bottom mb-5">
                    <colgroup>
                        <col width="15%"/>
                        <col width="45%"/>
                        <col width="20%"/>
                        <col width="20%"/>
                    </colgroup>
                    <thead>
                        <tr>
                          <th scope="col" class="text-center">#</th>
                          <th scope="col" class="text-center">제목</th>
                          <th scope="col" class="text-center">작성자</th>
                          <th scope="col" class="text-center">작성일</th>
                        </tr>
                    </thead>
                    <tbody id="listBody">
                    
                    </tbody>
                </table>
            </div>
            <div class="row mb-5">
                <div class="col-md-2">
                    <select class="form-control" name="searchType">
                        <option value="">전체</option>
                        <option value="bo_title">제목</option>
                        <option value="bo_content">내용</option>
                        <option value="bo_writer">작성자</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="searchWord" class="form-control" value=""/>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div class="dummy"></div>
                <div id="pagingArea">
                
                </div>
                <div>
                    <a class="btn btn-primary" href="write.php">글쓰기</a>
                </div>
            </div>
        </form>
	</div>
</div>

<!-- 비밀글 비밀번호 입력 모달창 -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">비밀글</h5>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="pwCheckForm" class="needs-validation" novalidate>
                    <input type="hidden" name="bo_no" />
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
<?php 
include '../resources/postScript.php';
?>
<script src="../resources/js/regex.js"></script>
<script type="text/javascript">
    $(function() {
        //검색 - 비동기
        var oListBody   = $("#listBody");
        var oPagingArea = $("#pagingArea");
        
        let searchForm = $("#searchForm").on("change", function() {
            //조회 조건이 변경될때마다 page 초기화
            searchForm.find("[name='page']").val("");
            searchForm.submit();
            return false;
        }).ajaxForm({ //전송되는 폼을 가로채서 비동기로 전송
            dataType : "json",
            contentType: 'application/x-www-form-urlencoded;charset=euc-kr',
            success : function(resp) {
                //게시판 검색조건 값
                let iPage       = $.trim($("[name='page']").val());
                let sSearchType = $.trim($("[name='searchType']").val());
                let sSearchWord = $.trim($("[name='searchWord']").val());
                let iScreenSize = $.trim($("[name='screenSize']").val());

                var aSearch = {
                    'curPage'    : iPage,
                    'searchType' : sSearchType,
                    'searchWord' : sSearchWord,
                    'screenSize' : iScreenSize
                };
                //게시판 검색조건 세션 쿠키 생성 - 브라우저를 닫으면 삭제
                $.cookie("search", JSON.stringify(aSearch), {path : "/"});
                
                oListBody.empty();
                let aTrTags = [];
                if (resp.list.length > 0) {//게시글 리스트 길이가 0보다 클때
                    //a태그 href 패턴
                    let sViewURLPtrn = "./view.php?no=%d";

                    //게시글 html 태그 생성
                    $(resp.list).each(function(i, board) {
                        //답글 css
                        let iDepth = parseInt(board.bo_depth);
                        let sReply = "";
                        if (iDepth > 1) {
                            for (var i = 0; i < iDepth; i++) {
                                sReply += '&nbsp;&nbsp;&nbsp;';
                            }
                            sReply += '<i class="bi bi-arrow-return-right"></i> RE : ';
                        }
                        
                        let sViewURL = sViewURLPtrn.replace("%d", board.bo_no);
                        let sATag    = "";

                        //글 상세조회 제목 a태그 생성
                        if (board.bo_del == 'Y') {//삭제된 글
                            sATag = $("<sapn>").html("[삭제된 글 입니다.]").css("color", "#c30");
                            sATag.prepend(sReply);
                        } else if (board.bo_sec == 'Y') { //비밀글일때
                            sATag = $("<a>").html(
                                    '<i class="bi bi-lock-fill"></i> &nbsp;비밀글입니다.'
                                ).attr({
                                    "class"       : "text-dark text-decoration-none d-block w-100 pwCheck",
                                    "data-toggle" : "modal",
                                    "href"        : "#myModal",
                                    "data-no"     : board.bo_no
                                });
                        sATag.prepend(sReply);
                        }else { //비밀글, 삭제된 글이 아닐때
                            sATag = $("<a>").attr({
                                        "class"     : "text-dark text-decoration-none d-block w-100",
                                        "href"      : sViewURL
                                    }).html(board.bo_title);
                            sATag.prepend(sReply);
                        }

                        //게시글 tr 태그 생성
                        let sTr = $("<tr>").append(
                                      $("<td>").attr("class", "text-center").html(board.bo_num),
                                      $("<td>").html(sATag),
                                      $("<td>").attr("class", "text-center").html(board.user_name + " (" + board.bo_writer + ")"),
                                      $("<td>").attr("class", "text-center").html(board.bo_date)
                                  );
                        aTrTags.push(sTr);
                    }); //게시글 리스트 row 데이터 처리 end
                } else {
                    let sTr = $("<tr>").html('<td colspan="4" class="text-center">조회할 게시글이 없습니다.</td>'); 
                    aTrTags.push(sTr);
                } //resp.list 데이터 처리 end
                
                oListBody.html(aTrTags);

                //페이이네이션 html 태그 출력
                var sPagingHTML = "";
                if (resp.config) {
                    sPagingHTML = getPagingHTML(resp.config);
                }
                
                oPagingArea.html(sPagingHTML);
            },
            error : function(xhr, resp, error) {
                console.log(xhr);
                console.log(resp);
                console.log(error);
            } 
        });

        //비동기 -> 동기 처리
        //처음페이지가 로드됐을때 비동기 함수를 실행시킨다.
        //쿠키에 저장되어 있는 게시판 데이터 value세팅
        var aSearch = [];
        if ($.cookie('search')) { //search 쿠키가 있다면
            //쿠키에 저장된 검색조건을 value값으로 지정
            aSearch = JSON.parse($.cookie('search'));
            searchForm.find("[name='page']").val(aSearch.curPage);
            searchForm.find("[name='searchType']").val(aSearch.searchType);
            searchForm.find("[name='searchWord']").val(aSearch.searchWord);
            searchForm.find("[name='screenSize']").val(aSearch.screenSize);
        }
        searchForm.submit();

        //페이지 버튼을 눌렀을 때
        oPagingArea.on("click", "a", function(event) {
            event.preventDefault();
            let iPage = $(this).data("page");
            if (iPage) {
                searchForm.find("[name='page']").val(iPage);
                searchForm.submit();
            }
            return false;
        });

        //비밀글 비밀번호 입력 모달
        var oMyModal     = $("#myModal");
        var oPwCheckBtn  = $("#pwCheckBtn");
        

        //비밀글 클릭시
        $(".table").on("click", ".pwCheck", function() {
            var iBoNo = $(this).data("no");
            $(oMyModal).modal({
                backdrop: 'static', 
                keyboard: false
            }); //모달설정
            oMyModal.find("form").children("[name='bo_no']").val(iBoNo);
            return false;
        });

        //모달 닫기버튼 클릭시 모달 form 초기화
        $(".btn-close").on("click", function() {
            oMyModal.modal("hide");
            oMyModal.find("[name='bo_pw']").removeClass("is-invalid is-valid invalid valid").val("");
            oMyModal.find("[name='bo_pw']").next().hide();
            return false;
        });

        //모달 비밀번호 입력창 enter키 submit 막기
        $('input[name="bo_pw"]').keydown(function(event) {
            if (event.keyCode === 13) { //enter키를 눌렀을때
                return false;
            }
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
                        if (resp.check == 'OK') { //비밀번호 통과
                            window.location.href = "./view.php?no=" + iBoNo;
                        } else { //비밀번호 오류
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

    //페이지네이션 html 태그 생성
    function getPagingHTML(aConfig) {
        var sHtml = "";
        sHtml += '<nav aria-label="Page navigation">'
        sHtml += '<ul class="pagination mb-0">';

        //이전 페이지 이동
        if (aConfig.currentPage > 1) {
            //처음 페이지 이동
            sHtml += '<li class="paga-item">';
            sHtml += '    <a class="page-link" href="#" data-page="1">';
            sHtml +='        <span aria-hidden="true">&laquo;</span>';
            sHtml += '    </a>';
            sHtml += '</li>';
            //이전 페이지 이동
            sHtml += '<li class="page-item">';
            sHtml += '    <a class="page-link" href="#" data-page="' + (parseInt(aConfig.currentPage) - 1) + '">';
            sHtml += '        <span aria-hidden="true">&lt;</span>';
            sHtml += '    </a>';
            sHtml += '</li>';
        }

        //페이지 이동
        for (var i = aConfig.startPage; i <= aConfig.endPage; i++) {
            if (aConfig.currentPage == i) {
                sHtml += '<li class="page-item active"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
            } else {
                sHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
            }
        }

        //다움 페이지 이동
        if (aConfig.currentPage < aConfig.totalPage) {
            //다음 페이지 이동
            sHtml += '<li class="page-item">';
            sHtml += '    <a class="page-link" href="#" data-page="' + (parseInt(aConfig.currentPage) + 1) + '">';
            sHtml += '        <span aria-hidden="true">&gt;</span>';
            sHtml += '    </a>';
            sHtml += '</li>';
            //마지막 페이지 이동
            sHtml += '<li class="paga-item">';
            sHtml += '    <a class="page-link" href="#" data-page="' + parseInt(aConfig.totalPage) + '">';
            sHtml += '        <span aria-hidden="true">&raquo;</span>';
            sHtml += '    </a>';
            sHtml += '</li>';
        }

        sHtml += "</ul></nav>";

        return sHtml;
    }
</script>
</body>
<?php
include '../resources/footer.php';
?>