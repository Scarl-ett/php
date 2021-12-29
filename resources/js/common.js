/**
 * 
 */
//null, '', undefined 일때
function isEmpty(val) {
    if (val == null || val == '' || val == 'undefined') {
        return true;
    } else {
        return false;
    }
}

//null, '', undefined가 아닐때
function isNotEmpty(val) {
    if (val != null && val != '' && val != 'undefined') {
        return true;
    } else {
        return false;
    }
}