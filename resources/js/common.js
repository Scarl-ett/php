/**
 * 
 */
//null, '', undefined �϶�
function isEmpty(val) {
    if (val == null || val == '' || val == 'undefined') {
        return true;
    } else {
        return false;
    }
}

//null, '', undefined�� �ƴҶ�
function isNotEmpty(val) {
    if (val != null && val != '' && val != 'undefined') {
        return true;
    } else {
        return false;
    }
}