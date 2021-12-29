<?php
session_start();

$sTitleName = 'main';
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
?>
<body class="bg-light">
<div class="container mb-5">
    <div class="py-4 d-flex justify-content-center">
        <h4 class="mr-5">ȯ���մϴ�. <?= $_SESSION['user_name']?>��</h4>
        <button class="btn btn-primary btn-sm" onclick="location.href='../user/logout.php'">�α׾ƿ�</button>
    </div>
    <div class="bg-white p-4">
        <form id="searchForm" action="./list.php">
            <input type="hidden" name="page" value=""/>
            <div class="row mb-4 d-flex justify-content-end">
                <div class="col-md-2">
                    <select class="form-control" name="screenSize">
                        <option value="10">10���� ����</option>
                        <option value="20">20���� ����</option>
                        <option value="30">30���� ����</option>
                        <option value="40">40���� ����</option>
                        <option value="50">50���� ����</option>
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
                          <th scope="col" class="text-center">����</th>
                          <th scope="col" class="text-center">�ۼ���</th>
                          <th scope="col" class="text-center">�ۼ���</th>
                        </tr>
                    </thead>
                    <tbody id="listBody">
                    
                    </tbody>
                </table>
            </div>
            <div class="row mb-5">
                <div class="col-md-2">
                    <select class="form-control" name="searchType">
                        <option value="">��ü</option>
                        <option value="bo_title">����</option>
                        <option value="bo_content">����</option>
                        <option value="bo_writer">�ۼ���</option>
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
                    <a class="btn btn-primary" href="write.php">�۾���</a>
                </div>
            </div>
        </form>
	</div>
</div>

<!-- ��б� ��й�ȣ �Է� ���â -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">��б�</h5>
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
<?php 
include '../resources/postScript.php';
?>
<script src="../resources/js/regex.js"></script>
<script type="text/javascript">
    $(function() {
        //�˻� - �񵿱�
        var oListBody   = $("#listBody");
        var oPagingArea = $("#pagingArea");
        
        let searchForm = $("#searchForm").on("change", function() {
            //��ȸ ������ ����ɶ����� page �ʱ�ȭ
            searchForm.find("[name='page']").val("");
            searchForm.submit();
            return false;
        }).ajaxForm({ //���۵Ǵ� ���� ����ä�� �񵿱�� ����
            dataType : "json",
            contentType: 'application/x-www-form-urlencoded;charset=euc-kr',
            success : function(resp) {
                //�Խ��� �˻����� ��
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
                //�Խ��� �˻����� ���� ��Ű ���� - �������� ������ ����
                $.cookie("search", JSON.stringify(aSearch), {path : "/"});
                
                oListBody.empty();
                let aTrTags = [];
                if (resp.list.length > 0) {//�Խñ� ����Ʈ ���̰� 0���� Ŭ��
                    //a�±� href ����
                    let sViewURLPtrn = "./view.php?no=%d";

                    //�Խñ� html �±� ����
                    $(resp.list).each(function(i, board) {
                        //��� css
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

                        //�� ����ȸ ���� a�±� ����
                        if (board.bo_del == 'Y') {//������ ��
                            sATag = $("<sapn>").html("[������ �� �Դϴ�.]").css("color", "#c30");
                            sATag.prepend(sReply);
                        } else if (board.bo_sec == 'Y') { //��б��϶�
                            sATag = $("<a>").html(
                                    '<i class="bi bi-lock-fill"></i> &nbsp;��б��Դϴ�.'
                                ).attr({
                                    "class"       : "text-dark text-decoration-none d-block w-100 pwCheck",
                                    "data-toggle" : "modal",
                                    "href"        : "#myModal",
                                    "data-no"     : board.bo_no
                                });
                        sATag.prepend(sReply);
                        }else { //��б�, ������ ���� �ƴҶ�
                            sATag = $("<a>").attr({
                                        "class"     : "text-dark text-decoration-none d-block w-100",
                                        "href"      : sViewURL
                                    }).html(board.bo_title);
                            sATag.prepend(sReply);
                        }

                        //�Խñ� tr �±� ����
                        let sTr = $("<tr>").append(
                                      $("<td>").attr("class", "text-center").html(board.bo_num),
                                      $("<td>").html(sATag),
                                      $("<td>").attr("class", "text-center").html(board.user_name + " (" + board.bo_writer + ")"),
                                      $("<td>").attr("class", "text-center").html(board.bo_date)
                                  );
                        aTrTags.push(sTr);
                    }); //�Խñ� ����Ʈ row ������ ó�� end
                } else {
                    let sTr = $("<tr>").html('<td colspan="4" class="text-center">��ȸ�� �Խñ��� �����ϴ�.</td>'); 
                    aTrTags.push(sTr);
                } //resp.list ������ ó�� end
                
                oListBody.html(aTrTags);

                //�����̳��̼� html �±� ���
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

        //�񵿱� -> ���� ó��
        //ó���������� �ε������ �񵿱� �Լ��� �����Ų��.
        //��Ű�� ����Ǿ� �ִ� �Խ��� ������ value����
        var aSearch = [];
        if ($.cookie('search')) { //search ��Ű�� �ִٸ�
            //��Ű�� ����� �˻������� value������ ����
            aSearch = JSON.parse($.cookie('search'));
            searchForm.find("[name='page']").val(aSearch.curPage);
            searchForm.find("[name='searchType']").val(aSearch.searchType);
            searchForm.find("[name='searchWord']").val(aSearch.searchWord);
            searchForm.find("[name='screenSize']").val(aSearch.screenSize);
        }
        searchForm.submit();

        //������ ��ư�� ������ ��
        oPagingArea.on("click", "a", function(event) {
            event.preventDefault();
            let iPage = $(this).data("page");
            if (iPage) {
                searchForm.find("[name='page']").val(iPage);
                searchForm.submit();
            }
            return false;
        });

        //��б� ��й�ȣ �Է� ���
        var oMyModal     = $("#myModal");
        var oPwCheckBtn  = $("#pwCheckBtn");
        

        //��б� Ŭ����
        $(".table").on("click", ".pwCheck", function() {
            var iBoNo = $(this).data("no");
            $(oMyModal).modal({
                backdrop: 'static', 
                keyboard: false
            }); //��޼���
            oMyModal.find("form").children("[name='bo_no']").val(iBoNo);
            return false;
        });

        //��� �ݱ��ư Ŭ���� ��� form �ʱ�ȭ
        $(".btn-close").on("click", function() {
            oMyModal.modal("hide");
            oMyModal.find("[name='bo_pw']").removeClass("is-invalid is-valid invalid valid").val("");
            oMyModal.find("[name='bo_pw']").next().hide();
            return false;
        });

        //��� ��й�ȣ �Է�â enterŰ submit ����
        $('input[name="bo_pw"]').keydown(function(event) {
            if (event.keyCode === 13) { //enterŰ�� ��������
                return false;
            }
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
                        if (resp.check == 'OK') { //��й�ȣ ���
                            window.location.href = "./view.php?no=" + iBoNo;
                        } else { //��й�ȣ ����
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

    //���������̼� html �±� ����
    function getPagingHTML(aConfig) {
        var sHtml = "";
        sHtml += '<nav aria-label="Page navigation">'
        sHtml += '<ul class="pagination mb-0">';

        //���� ������ �̵�
        if (aConfig.currentPage > 1) {
            //ó�� ������ �̵�
            sHtml += '<li class="paga-item">';
            sHtml += '    <a class="page-link" href="#" data-page="1">';
            sHtml +='        <span aria-hidden="true">&laquo;</span>';
            sHtml += '    </a>';
            sHtml += '</li>';
            //���� ������ �̵�
            sHtml += '<li class="page-item">';
            sHtml += '    <a class="page-link" href="#" data-page="' + (parseInt(aConfig.currentPage) - 1) + '">';
            sHtml += '        <span aria-hidden="true">&lt;</span>';
            sHtml += '    </a>';
            sHtml += '</li>';
        }

        //������ �̵�
        for (var i = aConfig.startPage; i <= aConfig.endPage; i++) {
            if (aConfig.currentPage == i) {
                sHtml += '<li class="page-item active"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
            } else {
                sHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
            }
        }

        //�ٿ� ������ �̵�
        if (aConfig.currentPage < aConfig.totalPage) {
            //���� ������ �̵�
            sHtml += '<li class="page-item">';
            sHtml += '    <a class="page-link" href="#" data-page="' + (parseInt(aConfig.currentPage) + 1) + '">';
            sHtml += '        <span aria-hidden="true">&gt;</span>';
            sHtml += '    </a>';
            sHtml += '</li>';
            //������ ������ �̵�
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