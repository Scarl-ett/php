/**
 * 
 */
//정규식 통과했을 때
function regOk(oTag) {
    $(oTag).removeClass("is-invalid invalid");
    $(oTag).addClass("is-valid valid");
    $(oTag).next(".invalid-feedback").css("display", "none");
    return true;
}

//정규식 통과하지 못했을 때
function regFail(oTag, sMessage) {
    $(oTag).removeClass("is-valid valid");
    $(oTag).addClass("is-invalid invalid");
    $(oTag).next(".invalid-feedback").text(sMessage).css({
        "display":"block",
        "color" : "#dc3545"
    });
    $(oTag).focus();
    return false;
}

//id 정규식
function idRgx(oIdTag) {
    var sUserId = $.trim(oIdTag.val());
    //영소문자로 시작하는 영문자, 숫자 조합 4~12자리
    var sRgxId = /^[a-z][a-zA-Z0-9]{3,11}$/;
    
    //id 정규식 통과 후 id 중복체크를 위해 false, true 리턴
    if (sRgxId.test(sUserId)) {//정규식
        return regOk(oIdTag);
    } else if (isEmpty(sUserId)) {//null
        return regFail(oIdTag, "아이디를 입력해주세요.(영소문자로 시작하는 영문자, 숫자 조합 4~12자리)");
    } else {
        return regFail(oIdTag, "아이디 형식이 올바르지 않습니다.(영소문자로 시작하는 영문자, 숫자 조합 4~12자리)");
    }
}

//비밀번호 정규식
function pwRgx(oUserPw) {
    var sPw = $.trim(oUserPw.val());
    
    //영문 소문자, 영문 대문자, 숫자, 특수문자 조합 8~12자리
    var sRegPw = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,12}$/;
    
    //비빌번호 정규식 체크 후 비밀번호 일치 여부 판단을 위해 false, true 반환 
    if (isEmpty(sPw)) {//null
        return regFail(oUserPw, "비밀번호를 입력해주세요.(영문 소문자, 영문 대문자, 숫자, 특수문자(!@$%&* 만 허용) 8~12자리)");
    } else if (!sRegPw.test(sPw)) {//정규식
        return regFail(oUserPw, "비밀번호 형식이 올바르지 않습니다.(영문 소문자, 영문 대문자, 숫자, 특수문자(!@$%&* 만 허용) 8~12자리)");
    } else {
        return regOk(oUserPw);
    }
}

//비밀번호 일치 여부 판단
function pwCheck(oUserPw, oUserPw2) {
    var sPw  = $.trim(oUserPw.val());
    var sPw2 = $.trim(oUserPw2.val());
    
    if (isEmpty(sPw2)) { //비밀번호2 null
        return regFail(oUserPw2, "비밀번호를 입력해주세요.");
    }
    
    if (sPw != sPw2) {//비밀번호가 동일하지 않을때
        $(oUserPw2).val("");
        return regFail(oUserPw2, "비밀번호가 일치하지 않습니다.");
    } else {
        if (pwRgx(oUserPw)) { //정규식을 통과했다면
            return regOk(oUserPw2); //valid 통과처리
        } else { //정규식을 통과하지 않았다면
            return regFail(oUserPw2, ""); //valid 실패
        }
    }
}

//이름 정규식
function nameRgx(oUserName) {
    var sName = $.trim(oUserName.val());
    
    //한글 2~8자리
    var sRegName = /^[가-힝]{2,8}$/;
    
    if (isEmpty(sName)) {//null
        return regFail(oUserName, "이름을 입력해주세요.");
    } else if (!sRegName.test(sName)) { //정규식
        return regFail(oUserName, "이름의 형식이 올바르지 않습니다.(한글 2~8자리)");
    } else {
        return regOk(oUserName);
    }
}

//이메일 아이디 정규식
function emailIdRgx(oEmailId) {
    var sEmailIdStr = $.trim(oEmailId.val());
    
    //한글 제외
    var sRegEmailId = /^[a-zA-Z0-9._%+-]/;
    
    if (isEmpty(sEmailIdStr)) {//null
        return regFail(oEmailId, "이메일을 입력해주세요.");
    } else if (!sRegEmailId.test(sEmailIdStr)) {//정규식
        return regFail(oEmailId, "이메일 형식이 올바르지 않습니다.");
    } else {
        return regOk(oEmailId);
    }
}

//이메일 호스트 정규식
function emailHostRgx(oEmailHost) {
    var sEmailHostStr = $.trim(oEmailHost.val());
    
    var sRegEmailHost = /((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    
    if (isEmpty(sEmailHostStr)) {//null
        return regFail(oEmailHost, "이메일을 선택하시거나 직접 입력해주세요.");
    } else if (!sRegEmailHost.test(sEmailHostStr)) {//정규식
        return regFail(oEmailHost, "이메일 형식이 올바르지 않습니다.");
    } else {
        return regOk(oEmailHost);
    }
}

//전화번호 정규식
function telRgx(oUserTel) {
    var sUserTelStr = $.trim(oUserTel.val());
    
    var sRegTel =  /^\d{3}-\d{3,4}-\d{4}$/;
    
    if (isEmpty(sUserTelStr)) {//null
        return regFail(oUserTel, "전화번호를 입력해주세요. (ex 010-1234-5678)");
    } else if (!sRegTel.test(sUserTelStr)) {//정규식
        return regFail(oUserTel, "전화번호 형식이 올바르지 않습니다.(ex 010-1234-5678)")
    } else {
        return regOk(oUserTel);
    }
}

//상세주소 null 체크
function addr2Rgx(oUserAddr2) {
    var sUserAddrStr = $.trim(oUserAddr2.val());
    
    if (isEmpty(sUserAddrStr)) {//null
        return regFail(oUserAddr2, "상세주소를 입력해주세요.");
    } else {
        return regOk(oUserAddr2);
    }
}

//개인정보동의 체크 여부 판단
function privacyRgx(oUserPrivacy) {
    if (oUserPrivacy.is(":checked") === false) {
        return regFail(oUserPrivacy, "개인정보동의는 필수입니다.");
    } else {
        return regOk(oUserPrivacy);
    }
}

//게시글 제목 null 체크
function titleRgx(oBoTitle) {
    var sBoTitleStr = $.trim(oBoTitle.val());
    
    if (isEmpty(sBoTitleStr)) {//null
        return regFail(oBoTitle, "필수입력 사항입니다.");
    } else {
        return regOk(oBoTitle);
    }
}

//게시글 내용 null 체크
function contentRgx(oBoContent) {
    var sBoContentStr = $.trim(oBoContent.val());
    
    if (isEmpty(sBoContentStr)) {//null
        return regFail(oBoContent, "필수입력 사항입니다.");
    } else {
        return regOk(oBoContent);
    }
}