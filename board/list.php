<?php
include "../resources/dbInfo.php";
include "../resources/config.php";

//현재 페이지
$iCurrentPage = !empty($_GET['page']) ? (int)trim($_GET['page']) : 1;

//검색조건
$sSearchType = !empty($_GET['searchType']) ? trim($_GET['searchType']) : ""; 
$sSearchWord = !empty($_GET['searchWord']) ? trim($_GET['searchWord']) : "";
$iScreenSize = !empty($_GET['screenSize']) ? (int)trim($_GET['screenSize']) : $iScreenSize;

//게시판 검색조건 세션 생성
$aSearch = array("curPage"=>$iCurrentPage, "searchType"=>$sSearchType, "searchWord"=>$sSearchWord, "screenSize"=>$iScreenSize);
$_SESSION['search'] = $aSearch;

//검색조건
$sSearch = "";

//검색쿼리
if (!empty($sSearchWord)) { //검색단어가 있을때
    //utf-8로 넘어온 searchWord euc-kr로 인코딩
    $sSearchWord = iconv("utf-8", "euc-kr", $sSearchWord);
    if (empty($sSearchType)) { //검색조건이 전체일때 
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

//총 게시물 조회
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

//페이징 처리에 팔요한 변수값 세팅
$oBoard       = mysqli_query($conn, $sQuery);
$iTotalRecord = $oBoard->num_rows;
$aTotalBoard  = $oBoard->fetch_array();
$iTotalPage   = ceil($iTotalRecord / $iScreenSize);
 
if ($iCurrentPage > $iTotalPage && $iTotalPage > 0) {
    echo "<script>";
    echo "    alert('존재하지 않는 페이지입니다.');";
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

//마지막 block에서 startPage와 endPage의 갯수가 blockSize보다 작을 때
if ($iTotalPage < ceil($iStartPage + $iBlockSize - 1)) {
    //마지막 block의 endPage
    $endPage = $iTotalPage;
} else {
    //마지막 block이 아닐때 endPage
    $endPage = ceil($iStartPage + $iBlockSize - 1);
}

//html에서 페이징처리에 필요한 변수값을 배열에 저장
$aConfig = array('totalPage'=>$iTotalPage, 'startPage'=>$iStartPage, 'endPage'=>$endPage, 'currentPage'=>$iCurrentPage);

//게시글 조회
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
    //제목이 40자가 넘으면 ...로 대체
    if (strlen($sTitle) > 40) {
        $sTitle = str_replace($sTitle, mb_substr($sTitle, 0, 40, "euckr")."...", $sTitle);
        $aRow['bo_title'] = $sTitle;
    }
    
    //euc-kr 한글깨짐 utf-8 인코딩
    $aRow['bo_title']  = iconv("euc-kr", "utf-8", $aRow['bo_title']);
    $aRow['user_name'] = iconv("euc-kr", "utf-8", $aRow['user_name']);
    $aRow['bo_num']    = $iBoNum;
    $iBoNum--;

    $aList[] = $aRow;
}

$aDataList[] = array();
//조회 게시글, 페이징처리 변수값
$aDataList["list"]   = $aList;
$aDataList["config"] = $aConfig;

echo json_encode($aDataList);
mysqli_close($conn);
?>