<?php
include 'config.php'; 
?>
<!doctype HTML>
<html>
<head>
    <meta charset="euc-kr"/>
    <meta name="viewport" content="initial-scale=1, width=device-width"/>
    <meta name="author" content="cw"/>
    <meta name="description" content="php"/>
    <meta name="keywords" content="php, 회원가입, 게시판"/>
    <title>
    <?php
    if (isset($sTitleName)) {
        echo $sTitleName;
    }
    ?>
    </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../resources/css/common.css" type="text/css"/>
    <!-- 공통 css 이외의 css가 추가로 필요할때 -->
    <?php
    if (isset($sCssItem)) {
        echo '<link rel="stylesheet" href="';
        echo (CSSPATH ."$sCssItem").'" ';
        echo 'type="text/css">';
    }
    ?>
</head>