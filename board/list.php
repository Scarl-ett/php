<?php
include "../resources/dbInfo.php";
include "../resources/config.php";

//���� ������
$iCurrentPage = !empty($_GET['page']) ? (int)trim($_GET['page']) : 1;

//�˻�����
$sSearchType = !empty($_GET['searchType']) ? trim($_GET['searchType']) : ""; 
$sSearchWord = !empty($_GET['searchWord']) ? trim($_GET['searchWord']) : "";
$iScreenSize = !empty($_GET['screenSize']) ? (int)trim($_GET['screenSize']) : $iScreenSize;

//�Խ��� �˻����� ���� ����
$aSearch = array("curPage"=>$iCurrentPage, "searchType"=>$sSearchType, "searchWord"=>$sSearchWord, "screenSize"=>$iScreenSize);
$_SESSION['search'] = $aSearch;

//�˻�����
$sSearch = "";

//�˻�����
if (!empty($sSearchWord)) { //�˻��ܾ ������
    //utf-8�� �Ѿ�� searchWord euc-kr�� ���ڵ�
    $sSearchWord = iconv("utf-8", "euc-kr", $sSearchWord);
    if (empty($sSearchType)) { //�˻������� ��ü�϶� 
        $sSearch .= "
            AND (
                bo_title LIKE '%".mysqli_real_escape_string($conn, $sSearchWord)."%' OR
                bo_writer LIKE '%".mysqli_real_escape_string($conn, $sSearchWord)."%' OR
                bo_content LIKE '%".mysqli_real_escape_string($conn, $sSearchWord)."%')
        ";
    } else if ($sSearchType == "bo_writer") {
        $sSearch = "
            AND (
                a.bo_writer LIKE '%".mysqli_real_escape_string($conn, $sSearchWord)."%' OR
                b.user_name LIKE '%".mysqli_real_escape_string($conn, $sSearchWord)."%')
       ";
    } else {
        $sSearch .= "
            AND ".mysqli_real_escape_string($conn, $sSearchType)."
            LIKE '%".mysqli_real_escape_string($conn, $sSearchWord)."%'
        ";
    }
}

//�� �Խù� ��ȸ
$sQuery = "
    SELECT
        bo_no
    FROM
        board a
        INNER JOIN user b
        ON (a.bo_writer = b.user_id)
    WHERE
        1=1
        ".$sSearch."
";

//����¡ ó���� �ȿ��� ������ ����
$oBoard       = mysqli_query($conn, $sQuery);
$iTotalRecord = $oBoard->num_rows;
$aTotalBoard  = $oBoard->fetch_array();
$iTotalPage   = ceil($iTotalRecord / $iScreenSize);
 
if ($iCurrentPage > $iTotalPage && $iTotalPage > 0) {
    echo "<script>";
    echo "    alert('�������� �ʴ� �������Դϴ�.');";
    echo "    window.location.href='main.php';";
    echo "</script>";
    mysqli_close($conn);
    exit;
}

$iCurrentBlock = ceil($iCurrentPage / $iBlockSize);
$iStartRow     = ceil(($iCurrentPage - 1) * $iScreenSize);
$iEndRow       = ceil($iCurrentPage * $iScreenSize);
$iStartPage    = ceil(($iCurrentBlock - 1) * $iBlockSize + 1);
$iBoNum        = ceil($iTotalRecord - ($iScreenSize * ($iCurrentPage - 1)));

//������ block���� startPage�� endPage�� ������ blockSize���� ���� ��
if ($iTotalPage < ceil($iStartPage + $iBlockSize - 1)) {
    //������ block�� endPage
    $endPage = $iTotalPage;
} else {
    //������ block�� �ƴҶ� endPage
    $endPage = ceil($iStartPage + $iBlockSize - 1);
}

//html���� ����¡ó���� �ʿ��� �������� �迭�� ����
$aConfig = array('totalPage'=>$iTotalPage, 'startPage'=>$iStartPage, 'endPage'=>$endPage, 'currentPage'=>$iCurrentPage);

//�Խñ� ��ȸ
$sSql = "
    SELECT
        a.bo_no,
        a.bo_depth,
        a.bo_parent,
        a.bo_title,
        a.bo_writer,
        b.user_name,
        a.bo_sec,
        a.bo_date,
        a.bo_del
    FROM
        board a
        INNER JOIN user b
        ON (a.bo_writer = b.user_id)
    WHERE
        1=1
        ".$sSearch."
    ORDER BY 
        bo_grpno DESC,
        bo_seq ASC
    LIMIT
        ".$iStartRow.", ".$iScreenSize
;

$oData  = mysqli_query($conn, $sSql);
$aList  = array();
while ($aRow = $oData->fetch_array()) {
    $sTitle = $aRow['bo_title'];
    //������ 40�ڰ� ������ ...�� ��ü
    if (strlen($sTitle) > 40) {
        $sTitle = str_replace($sTitle, mb_substr($sTitle, 0, 40, "euckr")."...", $sTitle);
        $aRow['bo_title'] = $sTitle;
    }
    
    //euc-kr �ѱ۱��� utf-8 ���ڵ�
    $aRow['bo_title']  = iconv("euc-kr", "utf-8", $aRow['bo_title']);
    $aRow['user_name'] = iconv("euc-kr", "utf-8", $aRow['user_name']);
    $aRow['bo_num']    = $iBoNum;
    $iBoNum--;

    $aList[] = $aRow;
}

$aDataList[] = array();
//��ȸ �Խñ�, ����¡ó�� ������
$aDataList["list"]   = $aList;
$aDataList["config"] = $aConfig;

echo json_encode($aDataList);
mysqli_close($conn);
?>