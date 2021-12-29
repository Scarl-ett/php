<?php
$sTitleName = 'join';
include '../resources/header.php';
?>
<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <h2>ȸ������</h2>
    </div>
    <div class="g-5 d-flex justify-content-center row">
        <div class="col-lg-7">
            <form class="needs-validation" novalidate method="post" id="joinForm" action="./join_insert.php">
                <div class="form-row">
                    <div class="col-md-9 mb-3">
                        <label for="userId"><span class="red-color">* </span>���̵�</label>
                        <input type="text" data-role="1" name="user_id" class="form-control" id="userId" required>
                        <div class="invalid-feedback">
                            ���̵� �Է����ּ���.(���ҹ��ڷ� �����ϴ� ������, ���� ���� 4~12�ڸ�)
                        </div>
                    </div>
                    <div class="col-md-3 pt-2">
                        <button class="w-100 btn btn-primary mt-4" type="button" id="idCheckBtn">�ߺ��˻�</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userPw"><span class="red-color">* </span>��й�ȣ</label>
                        <input type="password" name="user_pw" class="form-control" id="userPw" required>
                        <div class="invalid-feedback">
                            ��й�ȣ�� �Է����ּ���.(���� �ҹ���, ���� �빮��, ����, Ư������(!@$%&* �� ���) 8~12�ڸ�)
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userPw2"><span class="red-color">* </span>��й�ȣȮ��</label>
                        <input type="password" name="user_pw2" class="form-control" id="userPw2" required>
                        <div class="invalid-feedback">��й�ȣ�� �Է����ּ���.</div>
                    </div>
                 </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userName"><span class="red-color">* </span>�̸�</label>
                        <input type="text" name="user_name" class="form-control" id="userName" required>
                        <div class="invalid-feedback">�̸��� �Է����ּ���.</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-5 mb-3">
                        <label for="userEmail1"><span class="red-color">* </span>�̸���</label>
                        <input type="text" name="user_email1" class="form-control" id="userEmail1" required>
                        <div class="invalid-feedback">�̸����� �Է����ּ���.</div>
                    </div>
                    <div class="col-md-1 mt-4 text-center pt-3">
                        <label>@</label>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="userEmail2"><span class="red-color">* </span>�̸���</label>
                        <input type="text" name="user_email2" class="form-control" id="userEmail2" required>
                        <div class="invalid-feedback">�̸����� �����Ͻðų� ���� �Է����ּ���.</div>
                    </div>
                    <div class="col-md-3 mt-4 pt-2">
                        <select class="form-control2" id="emailSelect">
                            <option value="">�̸��ϼ���</option>
                            <option value="naver.com">���̹�</option>
                            <option value="nate.com">����Ʈ</option>
                            <option value="daum.net">����</option>
                            <option value="gmail.com">����</option>
                            <option value="hanmail.com">�Ѹ���</option>
                            <option value="1">�����Է�</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userTel"><span class="red-color">* </span>��ȭ��ȣ</label>
                        <input type="text" name="user_tel" class="form-control" id="userTel" required>
                        <div class="invalid-feedback">��ȭ��ȣ�� �Է����ּ���.(ex 010-1234-5678)</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="userZip"><span class="red-color">* </span>�����ȣ</label>
                        <input type="text" name="user_zip" class="form-control" id="userZip" required readonly>
                        <div class="invalid-feedback">�ּҰ˻��� ���� �����ȣ�� �Է����ּ���.</div>
                    </div>
                    <div class="col-md-3 mb-3 mt-2">
                        <button class="btn btn-primary mt-4" onclick="execDaumPostcode()" type="button">�ּҰ˻�</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="userAddr1"><span class="red-color">* </span>�⺻�ּ�</label>
                        <input type="text" name ="user_addr1" class="form-control" id="userAddr1" required readonly>
                        <div class="invalid-feedback">�ּҰ˻��� ���� �⺻�ּҸ� �������ּ���.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="userAddr2"><span class="red-color">* </span>���ּ� </label>
                        <input type="text" name="user_addr2" class="form-control" id="userAddr2" required>
                        <div class="invalid-feedback">���ּҸ� �Է����ּ���.</div>
                    </div>
                </div>
                <hr class="my-4">
                <h6><span class="red-color">* </span>����������������</h6>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Y" name="user_privacy" id="invalidCheck" required>
                        <label class="form-check-label" for="invalidCheck"> �������������� �����մϴ�. </label>
                        <div class="invalid-feedback">�����������Ǵ� �ʼ��Դϴ�.</div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row d-flex justify-content-center">
                    <a class="btn btn-secondary btn-lg mb-5 col-lg-5 mr-4" href="/">���</a>
                    <button class="btn btn-primary btn-lg mb-5 col-lg-5 ml-4" id="joinBtn" type="button">ȸ������</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
include '../resources/postScript.php';
?>
<script src="../resources/js/postcode.v2.js"></script>
<script src="../resources/js/regex.js"></script>
<script type="text/javascript">
    $(function() {
        var oJsoinBtn  = $("#joinBtn");
        var oJoinForm = $("#joinForm");

        //ȸ������ ��ư�� ������ ��
        $(oJsoinBtn).on("click", function() { 
            var iCount = 0;

            //�ʼ��Է��±� ���Խ�, null üũ
            if (!pwRgx($("[name='user_pw']"))) iCount++;
            if (!pwCheck($("[name='user_pw']"), $("[name='user_pw2']"))) iCount++;
            if (!nameRgx($("[name='user_name']"))) iCount++;
            if (!emailIdRgx($("[name='user_email1']"))) iCount++;
            if (!emailHostRgx($("[name='user_email2']"))) iCount++;
            if (!telRgx($("[name='user_tel']"))) iCount++;
            if (!addr2Rgx($("[name='user_addr2']"))) iCount++;
            var sZip = $.trim($("[name='user_zip']").val());
            if (isEmpty(sZip)) {
                $("[name='user_zip']").addClass("is-invalid invalid");
                iCount++;
            }
            var sAddr1 = $.trim($("[name='user_addr1']").val());
            if (isEmpty(sAddr1)) {
                $("[name='user_addr1']").addClass("is-invalid invalid");
                iCount++;
            }
            if (!privacyRgx($("[name='user_privacy']"))) iCount++;
            
            //null�� �ƴҶ��� ���Խ� üũ
            if (!idRgx($("[name='user_id']"))) { //id���Խ��� ������� ���ߴٸ�
                 iCount++;
            } else { //id���ԽĿ� ����ߴٸ�
                //id�ߺ�üũ - �񵿱� ��û�� �� ������ iCount üũ�ؼ� submit
                idCheck().then(function(resp) {
                    if (resp == "FAIL") {
                        iCount++;
                    } else {
                        //null�� �ƴϰ� ���ԽĿ� ������� ��� submit
                        if (iCount == 0) {
                            oJoinForm.submit();
                        }
                    }
                }, function (error) {
                    console.log(error);
                });
            }

            return false;
        });
        
        var oIdCheckBtn = $("#idCheckBtn");
        //id �ߺ�üũ
        var oId = $("input[name='user_id']");

        $(oIdCheckBtn).on("click", function() {
            idCheck();
            return false;
        });
        
        function idCheck(){
            //promise - �񵿱� ó���� �����ϰ� �� ó���� ���� �� ���� ó�� ����
            return new Promise(function(resolve, reject) {
                var sUserId = $.trim(oId.val())
                
                if (isEmpty(sUserId)) { //���̵� null
                    oId.addClass("is-invalid");
                } else { //���̵� null�ƴҶ�
                    if (idRgx(oId)) {//id���ԽĿ� ����ߴٸ�
                        //�񵿱� id�ߺ�üũ(id���ԽĿ� ����ؾ߸� �ߺ�üũ ����)
                        $.ajax({
                            url : "./idCheck.php",
                            method : "post",
                            data : {
                                user_id : sUserId
                            },
                            dataType : "json",
                            success : function(resp) {
                                if (resp.check == 'OK') { //�ߺ�x
                                    resolve(resp.check);
                                    oId.removeClass("is-invalid invalid");
                                    oId.addClass("is-valid valid");
                                    oId.next(".invalid-feedback").text("��� ������ ���̵��Դϴ�.").css({
                                        "display":"block",
                                        "color" : "#28a745"
                                    });
                                } else { //�ߺ�
                                    resolve(resp.check);
                                    oId.removeClass("is-valid valid");
                                    oId.addClass("is-invalid invalid");
                                    oId.next(".invalid-feedback").text("�̹� ��ϵ� ���̵��Դϴ�.").css({
                                        "display":"block",
                                        "color" : "#dc3545"
                                    });
                                    oId.focus();
                                }
                            },
                            error : function(xhr, error, msg) {
                                reject(Error("error"));
                                console.log(xhr);
                                console.log(error);
                                console.log(msg);
                            }
                        });
                    } else {//id���Խ� ������� ���ߴٸ�
                        oId.addClass("is-invalid");
                    }
                }
            });
        }

        //id ���Խ�
        $("[name='user_id']").on("change", function() {
            //���̵� ����� �� ���� ���Խ�, �ߺ�üũ ��˻�
            //���Խ� üũ
            if (idRgx($(this))) { //���ԽĿ� ������� ��
                idCheck(); //id�ߺ��˻�
            }
            return false;
        });

        //password ���Խ�
        var oUserPw  = $("input[name='user_pw']");
        var oUserPw2 = $("input[name='user_pw2']");
        
        //password ���Խ� Ȯ��
        $(oUserPw).on("change", function() {
            oUserPw2.val("").removeClass("is-valid valid is-invalid, invalid");
            pwRgx(oUserPw);
            return false;
        });

        //password ��ġ ���� �Ǵ�
        $(oUserPw2).on("change", function() {
            pwCheck(oUserPw, oUserPw2);
            return false;
        });         

        //name ���Խ�
        $("input[name='user_name']").on("change", function() {
            nameRgx($(this));
            return false;
        });

        //email ���Խ�
        //email ���̵� ���Խ�
        $("[name='user_email1']").on("change", function() {
            emailIdRgx($(this));
            return false;
        });

        //email ȣ��Ʈ ����Ʈ �ڽ�
        $("#emailSelect").on("change", function() {
            var sSelectMail = $.trim($(this).val());
            if (sSelectMail == "1") {//�����Է�
                $("[name='user_email2']").val("").removeClass("is-valid valid").attr("readonly", false).focus();
            } else {
                $("[name='user_email2']").val(sSelectMail).attr("readonly", true);
                emailHostRgx($("[name='user_email2']"));
            }
            return false;
        });

        //email ȣ��Ʈ ���Խ�
        $("[name='user_email2']").on("change", function() {
            emailHostRgx($(this));
            return false;
        });

        //tel ���Խ�
        $("input[name='user_tel']").on("change", function() {
            telRgx($(this));
            return false;
        });

        //addr2 ���Խ�
        $("input[name='user_addr2']").on("change", function() {
            addr2Rgx($(this));
            return false;
        });

        //privacy üũ ���� �Ǵ�
        $("input[name='user_privacy']").on("change", function() {
            privacyRgx($(this));
            return false;
        });
    });
    
    //���� ���� api
    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete : function(data) {
               var addr      = '';
               var extraAddr = '';
               if (data.userSelectedType === 'R') {
                   addr = data.roadAddress;
               } else {
                   addr = data.jibunAddress;
               }
               if (data.userSelectedType === 'R') {
                   if (data.bname !== '' && /[��|��|��]$/g.test(data.bname)) {
                       extraAddr += data.bname;
                   }
                   if (data.buildingName !== '' && data.apartment === 'Y') {
                       extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                   }
                   if (extraAddr !== '') {
                       extraAddr = ' (' + extraAddr + ')';
                   }
               }
               
               //�����ȣ, �⺻�ּ� �ʵ忡 ���� �Է�
               $("input[name='user_zip']").val(data.zonecode);
               $("input[name='user_addr1']").val(addr);
               //���ּ� focus �̵�
               $("input[name='user_addr2']").focus();
               
               //�����ȣ, �⺻�ּҰ� �ʵ忡 �ԷµǸ� validation ���
               regOk($("input[name='user_zip']"));
               regOk($("input[name='user_addr1']"));
            }
        }).open();
	}
</script>
</body>
<?php
include '../resources/footer.php';
?>