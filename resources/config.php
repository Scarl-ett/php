<?php
//*************** contentType ***************//
header("Content-Type:text/html; charset=euc-kr");

//****************** ������ *****************//
$http_host = $_SERVER['HTTP_HOST'];
$cPath     = "http://".$http_host;

//*************** �Խ��� ���� ***************//
//�� �������� ������ ����Ʈ ��
$iScreenSize = 10;
//�� ȭ�鿡 ������ ������ ��
$iBlockSize  = 3;

//�Խ��� �˻�����, ������ ��Ű ����
$sCurrentPage = $_SERVER['REQUEST_URI'];
//���� �����ִ� �������� �Խ��� ����ȸ, ����Ʈ �������� �ƴ϶��
if (strpos($sCurrentPage, 'main.php') === false && strpos($sCurrentPage, 'view.php') === false) {
    //�Խ��� ��Ű ���� - search��� ��Ⱑ �����Ѵٸ�
    if (isset($_COOKIE['search'])) {
        setcookie('search', null, -1);
    }
}
?>