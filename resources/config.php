<?php
//*************** contentType ***************//
header("Content-Type:text/html; charset=euc-kr");

//****************** 도메인 *****************//
$http_host = $_SERVER['HTTP_HOST'];
$cPath     = "http://".$http_host;

//*************** 게시판 설정 ***************//
//한 페이지당 보여질 리스트 수
$iScreenSize = 10;
//한 화면에 보여질 페이지 수
$iBlockSize  = 3;

//게시판 검색조건, 페이지 쿠키 삭제
$sCurrentPage = $_SERVER['REQUEST_URI'];
//현재 보고있는 페이지가 게시판 상세조회, 리스트 페이지가 아니라면
if (strpos($sCurrentPage, 'main.php') === false && strpos($sCurrentPage, 'view.php') === false) {
    //게시판 쿠키 삭제 - search라는 쿠기가 존재한다면
    if (isset($_COOKIE['search'])) {
        setcookie('search', null, -1);
    }
}
?>