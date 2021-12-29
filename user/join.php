<?php
$sTitleName = 'join';
include '../resources/header.php';
?>
<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <h2>회원가입</h2>
    </div>
    <div class="g-5 d-flex justify-content-center row">
        <div class="col-lg-7">
            <form class="needs-validation" novalidate method="post" id="joinForm" action="./join_insert.php">
                <div class="form-row">
                    <div class="col-md-9 mb-3">
                        <label for="userId"><span class="red-color">* </span>아이디</label>
                        <input type="text" data-role="1" name="user_id" class="form-control" id="userId" required>
                        <div class="invalid-feedback">
                            아이디를 입력해주세요.(영소문자로 시작하는 영문자, 숫자 조합 4~12자리)
                        </div>
                    </div>
                    <div class="col-md-3 pt-2">
                        <button class="w-100 btn btn-primary mt-4" type="button" id="idCheckBtn">중복검사</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userPw"><span class="red-color">* </span>비밀번호</label>
                        <input type="password" name="user_pw" class="form-control" id="userPw" required>
                        <div class="invalid-feedback">
                            비밀번호를 입력해주세요.(영문 소문자, 영문 대문자, 숫자, 특수문자(!@$%&* 만 허용) 8~12자리)
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userPw2"><span class="red-color">* </span>비밀번호확인</label>
                        <input type="password" name="user_pw2" class="form-control" id="userPw2" required>
                        <div class="invalid-feedback">비밀번호를 입력해주세요.</div>
                    </div>
                 </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userName"><span class="red-color">* </span>이름</label>
                        <input type="text" name="user_name" class="form-control" id="userName" required>
                        <div class="invalid-feedback">이름을 입력해주세요.</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-5 mb-3">
                        <label for="userEmail1"><span class="red-color">* </span>이메일</label>
                        <input type="text" name="user_email1" class="form-control" id="userEmail1" required>
                        <div class="invalid-feedback">이메일을 입력해주세요.</div>
                    </div>
                    <div class="col-md-1 mt-4 text-center pt-3">
                        <label>@</label>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="userEmail2"><span class="red-color">* </span>이메일</label>
                        <input type="text" name="user_email2" class="form-control" id="userEmail2" required>
                        <div class="invalid-feedback">이메일을 선택하시거나 직접 입력해주세요.</div>
                    </div>
                    <div class="col-md-3 mt-4 pt-2">
                        <select class="form-control2" id="emailSelect">
                            <option value="">이메일선택</option>
                            <option value="naver.com">네이버</option>
                            <option value="nate.com">네이트</option>
                            <option value="daum.net">다음</option>
                            <option value="gmail.com">구글</option>
                            <option value="hanmail.com">한메일</option>
                            <option value="1">직접입력</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="userTel"><span class="red-color">* </span>전화번호</label>
                        <input type="text" name="user_tel" class="form-control" id="userTel" required>
                        <div class="invalid-feedback">전화번호를 입력해주세요.(ex 010-1234-5678)</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="userZip"><span class="red-color">* </span>우편번호</label>
                        <input type="text" name="user_zip" class="form-control" id="userZip" required readonly>
                        <div class="invalid-feedback">주소검색을 통해 우편번호를 입력해주세요.</div>
                    </div>
                    <div class="col-md-3 mb-3 mt-2">
                        <button class="btn btn-primary mt-4" onclick="execDaumPostcode()" type="button">주소검색</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="userAddr1"><span class="red-color">* </span>기본주소</label>
                        <input type="text" name ="user_addr1" class="form-control" id="userAddr1" required readonly>
                        <div class="invalid-feedback">주소검색을 통해 기본주소를 선택해주세요.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="userAddr2"><span class="red-color">* </span>상세주소 </label>
                        <input type="text" name="user_addr2" class="form-control" id="userAddr2" required>
                        <div class="invalid-feedback">상세주소를 입력해주세요.</div>
                    </div>
                </div>
                <hr class="my-4">
                <h6><span class="red-color">* </span>개인정보수집동의</h6>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Y" name="user_privacy" id="invalidCheck" required>
                        <label class="form-check-label" for="invalidCheck"> 개인정보수집에 동의합니다. </label>
                        <div class="invalid-feedback">개인정보동의는 필수입니다.</div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row d-flex justify-content-center">
                    <a class="btn btn-secondary btn-lg mb-5 col-lg-5 mr-4" href="/">취소</a>
                    <button class="btn btn-primary btn-lg mb-5 col-lg-5 ml-4" id="joinBtn" type="button">회원가입</button>
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

        //회원가입 버튼을 눌렀을 때
        $(oJsoinBtn).on("click", function() { 
            var iCount = 0;

            //필수입력태그 정규식, null 체크
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
            
            //null이 아닐때는 정규식 체크
            if (!idRgx($("[name='user_id']"))) { //id정규식을 통과하지 못했다면
                 iCount++;
            } else { //id정규식에 통과했다면
                //id중복체크 - 비동기 요청이 다 끝나면 iCount 체크해서 submit
                idCheck().then(function(resp) {
                    if (resp == "FAIL") {
                        iCount++;
                    } else {
                        //null이 아니고 정규식에 통과했을 경우 submit
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
        //id 중복체크
        var oId = $("input[name='user_id']");

        $(oIdCheckBtn).on("click", function() {
            idCheck();
            return false;
        });
        
        function idCheck(){
            //promise - 비동기 처리를 실행하고 그 처리가 끝난 후 다음 처리 실행
            return new Promise(function(resolve, reject) {
                var sUserId = $.trim(oId.val())
                
                if (isEmpty(sUserId)) { //아이디가 null
                    oId.addClass("is-invalid");
                } else { //아이디가 null아닐때
                    if (idRgx(oId)) {//id정규식에 통과했다면
                        //비동기 id중복체크(id정규식에 통과해야만 중복체크 가능)
                        $.ajax({
                            url : "./idCheck.php",
                            method : "post",
                            data : {
                                user_id : sUserId
                            },
                            dataType : "json",
                            success : function(resp) {
                                if (resp.check == 'OK') { //중복x
                                    resolve(resp.check);
                                    oId.removeClass("is-invalid invalid");
                                    oId.addClass("is-valid valid");
                                    oId.next(".invalid-feedback").text("사용 가능한 아이디입니다.").css({
                                        "display":"block",
                                        "color" : "#28a745"
                                    });
                                } else { //중복
                                    resolve(resp.check);
                                    oId.removeClass("is-valid valid");
                                    oId.addClass("is-invalid invalid");
                                    oId.next(".invalid-feedback").text("이미 등록된 아이디입니다.").css({
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
                    } else {//id정규식 통과하지 못했다면
                        oId.addClass("is-invalid");
                    }
                }
            });
        }

        //id 정규식
        $("[name='user_id']").on("change", function() {
            //아이디가 변경될 때 마다 정규식, 중복체크 재검사
            //정규식 체크
            if (idRgx($(this))) { //정규식에 통과했을 때
                idCheck(); //id중복검사
            }
            return false;
        });

        //password 정규식
        var oUserPw  = $("input[name='user_pw']");
        var oUserPw2 = $("input[name='user_pw2']");
        
        //password 정규식 확인
        $(oUserPw).on("change", function() {
            oUserPw2.val("").removeClass("is-valid valid is-invalid, invalid");
            pwRgx(oUserPw);
            return false;
        });

        //password 일치 여부 판단
        $(oUserPw2).on("change", function() {
            pwCheck(oUserPw, oUserPw2);
            return false;
        });         

        //name 정규식
        $("input[name='user_name']").on("change", function() {
            nameRgx($(this));
            return false;
        });

        //email 정규식
        //email 아이디 정규식
        $("[name='user_email1']").on("change", function() {
            emailIdRgx($(this));
            return false;
        });

        //email 호스트 셀렉트 박스
        $("#emailSelect").on("change", function() {
            var sSelectMail = $.trim($(this).val());
            if (sSelectMail == "1") {//직접입력
                $("[name='user_email2']").val("").removeClass("is-valid valid").attr("readonly", false).focus();
            } else {
                $("[name='user_email2']").val(sSelectMail).attr("readonly", true);
                emailHostRgx($("[name='user_email2']"));
            }
            return false;
        });

        //email 호스트 정규식
        $("[name='user_email2']").on("change", function() {
            emailHostRgx($(this));
            return false;
        });

        //tel 정규식
        $("input[name='user_tel']").on("change", function() {
            telRgx($(this));
            return false;
        });

        //addr2 정규식
        $("input[name='user_addr2']").on("change", function() {
            addr2Rgx($(this));
            return false;
        });

        //privacy 체크 여부 판단
        $("input[name='user_privacy']").on("change", function() {
            privacyRgx($(this));
            return false;
        });
    });
    
    //다음 우편 api
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
                   if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
                       extraAddr += data.bname;
                   }
                   if (data.buildingName !== '' && data.apartment === 'Y') {
                       extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                   }
                   if (extraAddr !== '') {
                       extraAddr = ' (' + extraAddr + ')';
                   }
               }
               
               //우편번호, 기본주소 필드에 정보 입력
               $("input[name='user_zip']").val(data.zonecode);
               $("input[name='user_addr1']").val(addr);
               //상세주소 focus 이동
               $("input[name='user_addr2']").focus();
               
               //우편번호, 기본주소가 필드에 입력되면 validation 통과
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