<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");

    session_start();
    if(isset($_SESSION["account"])==false||$_SESSION["account"]==""||$_SESSION["area"]!="pray"){
        $code=-2;
        $desc="auth failed";
        $json_ret=array("code"=>$code,"desc"=>$desc);echo json_encode($json_ret);exit;
    }

    set_time_limit(1200); // page execution time = 1200 seconds

    ini_set("error_reporting", 0); //error_reporting(E_ALL & ~E_NOTICE);
    ini_set("display_errors","Off"); // On : open, Off : close
    ini_set('memory_limit', -1 );

    date_default_timezone_set('Asia/Taipei');//	date_default_timezone_set('Europe/London');
    if (PHP_SAPI=='cli'){die('This example should only be run from a Web Browser');}

    require_once("../lib/connmysql.php");
    require_once("../lib/common.php");
    require_once("../../resource/Classes/PHPExcel.php"); // PHPExcel // require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
    require_once("../../resource/Classes/PHPExcel/IOFactory.php"); // PHPExcel_IOFactory

    // check db exist
    $currY=date('Y');
    $currM=date('m');
    if ($currM>=10){$currY+=1;}
    $tbname="pray_".$currY;
    check_pray_db($tbname);

    $dateCurr=date('Y');
    $table_title=$dateCurr." 祈願法會 義工人力、床位報名表";

    //------------------------------------------------------------------------------------------------------------------------------
    // Create new PHPExcel object
    $nSheet=0;
    $objPHPExcel=new PHPExcel();
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                 ->setLastModifiedBy("Maarten Balliauw")
                                 ->setTitle("Office 2007 XLSX Test Document")
                                 ->setSubject("Office 2007 XLSX Test Document")
                                 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                 ->setKeywords("office 2007 openxml php")
                                 ->setCategory("Test result file");
    $objWorkSheet=$objPHPExcel->setActiveSheetIndex($nSheet);

    $col=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
              "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
              "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
              "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ");

    $mainitem=-1;//21;
    $roundcnt=1; // 2: 考慮去/回
    $top=3;

    // main title
    $objWorkSheet->mergeCells("A1:AB1");
    $objWorkSheet->setCellValue("A1", $table_title); //合併後的儲存格
    $objWorkSheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objWorkSheet->getStyle("A1")->getFont()->setSize(16);
    $objWorkSheet->getStyle("A1")->getFont()->setBold(true);

    $objWorkSheet->mergeCells("A2:A4");
    $objWorkSheet->setCellValue("A2", "大組");
    $objWorkSheet->mergeCells("B2:B4");
    $objWorkSheet->setCellValue("B2", "組別");

    $objWorkSheet->mergeCells("C2:D3");
    $objWorkSheet->setCellValue("C2", "總義工數");
    $objWorkSheet->setCellValue("C4", "男");
    $objWorkSheet->setCellValue("D4", "女");
    // each sub title
    $days = ["3/3(二)","3/4(三)","3/5(四)","3/6(五)","3/7(六)","3/8(日)","3/9(一)","3/10(二)"];
    $start = 4;
    for ($w = 0; $w < count($days); $w++) {
      $item = $col[$start + $w * 3]."2:".$col[$start + $w * 3 + 2]."2";
      $objWorkSheet->mergeCells($item);
      $item = $col[$start + $w * 3]."2";
      $objWorkSheet->setCellValue($item, $days[$w]);

      $item = $col[$start + $w * 3]."3:".$col[$start + $w * 3]."4";
      $objWorkSheet->mergeCells($item);
      $item = $col[$start + $w * 3]."3";
      $objWorkSheet->setCellValue($item, "義工");

      $item = $col[$start + $w * 3 + 1]."3:".$col[$start + $w * 3 + 2]."3";
      $objWorkSheet->mergeCells($item);
      $item = $col[$start + $w * 3 + 1]."3";
      $objWorkSheet->setCellValue($item, "住宿");

      $item = $col[$start + $w * 3 + 1]."4";
      $objWorkSheet->setCellValue($item, "男");

      $item = $col[$start + $w * 3 + 2]."4";
      $objWorkSheet->setCellValue($item, "女");
    }

    $objWorkSheet->getRowDimension("1")->setRowHeight(25);
    $objWorkSheet->getRowDimension("3")->setRowHeight(18);
    $objWorkSheet->getRowDimension("4")->setRowHeight(18);
    $objWorkSheet->getRowDimension("5")->setRowHeight(18);

    $objWorkSheet->getStyle("A2:AB4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objWorkSheet->getStyle("A2:AB4")->getFill()->getStartColor()->setRGB('DDFFDD');
    $idx=0;
    $iRow=4;

    // 填寫資料
    $sql = "SELECT `group`, `subgroup`, COUNT(*) as count FROM `".$tbname."` where (`invalidate`<=0) group by `group`,`subgroup` order by `group` ASC ,`subgroup` ASC";

    $sql = "";

    $sql .= "SELECT ";
    $sql .= "    X.`group`, '合計' as `subgroup`, concat(`group`,'-合計') as `whole`, ";
    $sql .= "    sum(X.`M`) as M, sum(X.`F`) as F, ";
    $sql .= "    sum(X.`j1_M`) as j1M, sum(X.`j1_F`) as j1F, sum(X.`l1_M`) as l1M, sum(X.`l1_F`) as l1F, ";
    $sql .= "    sum(X.`j2_M`) as j2M, sum(X.`j2_F`) as j2F, sum(X.`l2_M`) as l2M, sum(X.`l2_F`) as l2F, ";
    $sql .= "    sum(X.`j3_M`) as j3M, sum(X.`j3_F`) as j3F, sum(X.`l3_M`) as l3M, sum(X.`l3_F`) as l3F, ";
    $sql .= "    sum(X.`j4_M`) as j4M, sum(X.`j4_F`) as j4F, sum(X.`l4_M`) as l4M, sum(X.`l4_F`) as l4F, ";
    $sql .= "    sum(X.`j5_M`) as j5M, sum(X.`j5_F`) as j5F, sum(X.`l5_M`) as l5M, sum(X.`l5_F`) as l5F, ";
    $sql .= "    sum(X.`j6_M`) as j6M, sum(X.`j6_F`) as j6F, sum(X.`l6_M`) as l6M, sum(X.`l6_F`) as l6F, ";
    $sql .= "    sum(X.`j7_M`) as j7M, sum(X.`j7_F`) as j7F, sum(X.`l7_M`) as l7M, sum(X.`l7_F`) as l7F, ";
    $sql .= "    sum(X.`j8_M`) as j8M, sum(X.`j8_F`) as j8F, sum(X.`l8_M`) as l8M, sum(X.`l8_F`) as l8F, ";
    $sql .= "    sum(X.`j9_M`) as j9M, sum(X.`j9_F`) as j9F, sum(X.`l9_M`) as l9M, sum(X.`l9_F`) as l9F, ";
    $sql .= "    sum(X.`jx_M`) as jxM, sum(X.`jx_F`) as jxF, sum(X.`lx_M`) as lxM, sum(X.`lx_F`) as lxF  ";
    $sql .= "from ( ";
    $sql .= "    SELECT `group`, `subgroup`, ";
    $sql .= "        IF(`sex`='M', 1, 0) as `M`, IF(`sex`='F', 1, 0) as `F`, ";
    $sql .= "        IF(`sex`='M' AND `join1`=1, 1, 0) as `j1_M`, IF(`sex`='F' AND `join1`=1, 1, 0) as `j1_F`, IF(`sex`='M' AND `live1`=1, 1, 0) as `l1_M`, IF(`sex`='F' AND `live1`=1, 1, 0) as `l1_F`, ";
    $sql .= "        IF(`sex`='M' AND `join2`=1, 1, 0) as `j2_M`, IF(`sex`='F' AND `join2`=1, 1, 0) as `j2_F`, IF(`sex`='M' AND `live2`=1, 1, 0) as `l2_M`, IF(`sex`='F' AND `live2`=1, 1, 0) as `l2_F`, ";
    $sql .= "        IF(`sex`='M' AND `join3`=1, 1, 0) as `j3_M`, IF(`sex`='F' AND `join3`=1, 1, 0) as `j3_F`, IF(`sex`='M' AND `live3`=1, 1, 0) as `l3_M`, IF(`sex`='F' AND `live3`=1, 1, 0) as `l3_F`, ";
    $sql .= "        IF(`sex`='M' AND `join4`=1, 1, 0) as `j4_M`, IF(`sex`='F' AND `join4`=1, 1, 0) as `j4_F`, IF(`sex`='M' AND `live4`=1, 1, 0) as `l4_M`, IF(`sex`='F' AND `live4`=1, 1, 0) as `l4_F`, ";
    $sql .= "        IF(`sex`='M' AND `join5`=1, 1, 0) as `j5_M`, IF(`sex`='F' AND `join5`=1, 1, 0) as `j5_F`, IF(`sex`='M' AND `live5`=1, 1, 0) as `l5_M`, IF(`sex`='F' AND `live5`=1, 1, 0) as `l5_F`, ";
    $sql .= "        IF(`sex`='M' AND `join6`=1, 1, 0) as `j6_M`, IF(`sex`='F' AND `join6`=1, 1, 0) as `j6_F`, IF(`sex`='M' AND `live6`=1, 1, 0) as `l6_M`, IF(`sex`='F' AND `live6`=1, 1, 0) as `l6_F`, ";
    $sql .= "        IF(`sex`='M' AND `join7`=1, 1, 0) as `j7_M`, IF(`sex`='F' AND `join7`=1, 1, 0) as `j7_F`, IF(`sex`='M' AND `live7`=1, 1, 0) as `l7_M`, IF(`sex`='F' AND `live7`=1, 1, 0) as `l7_F`, ";
    $sql .= "        IF(`sex`='M' AND `join8`=1, 1, 0) as `j8_M`, IF(`sex`='F' AND `join8`=1, 1, 0) as `j8_F`, IF(`sex`='M' AND `live8`=1, 1, 0) as `l8_M`, IF(`sex`='F' AND `live8`=1, 1, 0) as `l8_F`, ";
    $sql .= "        IF(`sex`='M' AND `join9`=1, 1, 0) as `j9_M`, IF(`sex`='F' AND `join9`=1, 1, 0) as `j9_F`, IF(`sex`='M' AND `live9`=1, 1, 0) as `l9_M`, IF(`sex`='F' AND `live9`=1, 1, 0) as `l9_F`, ";
    $sql .= "        IF(`sex`='M' AND `joinx`=1, 1, 0) as `jx_M`, IF(`sex`='F' AND `joinx`=1, 1, 0) as `jx_F`, IF(`sex`='M' AND `livex`=1, 1, 0) as `lx_M`, IF(`sex`='F' AND `livex`=1, 1, 0) as `lx_F`  ";
    $sql .= "    FROM `".$tbname."` WHERE (`invalidate` <= 0) ";
    $sql .= ") AS X WHERE 1=1 ";
    $sql .= "GROUP BY `group` ";
    $sql .= " ";
    $sql .= " UNION ";
    $sql .= " ";

    $sql .= "SELECT ";
    $sql .= "    '' as `group`, '總計' as `subgroup`, '總計' as `whole`, ";
    $sql .= "    sum(X.`M`) as M, sum(X.`F`) as F, ";
    $sql .= "    sum(X.`j1_M`) as j1M, sum(X.`j1_F`) as j1F, sum(X.`l1_M`) as l1M, sum(X.`l1_F`) as l1F, ";
    $sql .= "    sum(X.`j2_M`) as j2M, sum(X.`j2_F`) as j2F, sum(X.`l2_M`) as l2M, sum(X.`l2_F`) as l2F, ";
    $sql .= "    sum(X.`j3_M`) as j3M, sum(X.`j3_F`) as j3F, sum(X.`l3_M`) as l3M, sum(X.`l3_F`) as l3F, ";
    $sql .= "    sum(X.`j4_M`) as j4M, sum(X.`j4_F`) as j4F, sum(X.`l4_M`) as l4M, sum(X.`l4_F`) as l4F, ";
    $sql .= "    sum(X.`j5_M`) as j5M, sum(X.`j5_F`) as j5F, sum(X.`l5_M`) as l5M, sum(X.`l5_F`) as l5F, ";
    $sql .= "    sum(X.`j6_M`) as j6M, sum(X.`j6_F`) as j6F, sum(X.`l6_M`) as l6M, sum(X.`l6_F`) as l6F, ";
    $sql .= "    sum(X.`j7_M`) as j7M, sum(X.`j7_F`) as j7F, sum(X.`l7_M`) as l7M, sum(X.`l7_F`) as l7F, ";
    $sql .= "    sum(X.`j8_M`) as j8M, sum(X.`j8_F`) as j8F, sum(X.`l8_M`) as l8M, sum(X.`l8_F`) as l8F, ";
    $sql .= "    sum(X.`j9_M`) as j9M, sum(X.`j9_F`) as j9F, sum(X.`l9_M`) as l9M, sum(X.`l9_F`) as l9F, ";
    $sql .= "    sum(X.`jx_M`) as jxM, sum(X.`jx_F`) as jxF, sum(X.`lx_M`) as lxM, sum(X.`lx_F`) as lxF  ";
    $sql .= "from ( ";
    $sql .= "    SELECT `group`, `subgroup` AS '', ";
    $sql .= "        IF(`sex`='M', 1, 0) as `M`, IF(`sex`='F', 1, 0) as `F`, ";
    $sql .= "        IF(`sex`='M' AND `join1`=1, 1, 0) as `j1_M`, IF(`sex`='F' AND `join1`=1, 1, 0) as `j1_F`, IF(`sex`='M' AND `live1`=1, 1, 0) as `l1_M`, IF(`sex`='F' AND `live1`=1, 1, 0) as `l1_F`, ";
    $sql .= "        IF(`sex`='M' AND `join2`=1, 1, 0) as `j2_M`, IF(`sex`='F' AND `join2`=1, 1, 0) as `j2_F`, IF(`sex`='M' AND `live2`=1, 1, 0) as `l2_M`, IF(`sex`='F' AND `live2`=1, 1, 0) as `l2_F`, ";
    $sql .= "        IF(`sex`='M' AND `join3`=1, 1, 0) as `j3_M`, IF(`sex`='F' AND `join3`=1, 1, 0) as `j3_F`, IF(`sex`='M' AND `live3`=1, 1, 0) as `l3_M`, IF(`sex`='F' AND `live3`=1, 1, 0) as `l3_F`, ";
    $sql .= "        IF(`sex`='M' AND `join4`=1, 1, 0) as `j4_M`, IF(`sex`='F' AND `join4`=1, 1, 0) as `j4_F`, IF(`sex`='M' AND `live4`=1, 1, 0) as `l4_M`, IF(`sex`='F' AND `live4`=1, 1, 0) as `l4_F`, ";
    $sql .= "        IF(`sex`='M' AND `join5`=1, 1, 0) as `j5_M`, IF(`sex`='F' AND `join5`=1, 1, 0) as `j5_F`, IF(`sex`='M' AND `live5`=1, 1, 0) as `l5_M`, IF(`sex`='F' AND `live5`=1, 1, 0) as `l5_F`, ";
    $sql .= "        IF(`sex`='M' AND `join6`=1, 1, 0) as `j6_M`, IF(`sex`='F' AND `join6`=1, 1, 0) as `j6_F`, IF(`sex`='M' AND `live6`=1, 1, 0) as `l6_M`, IF(`sex`='F' AND `live6`=1, 1, 0) as `l6_F`, ";
    $sql .= "        IF(`sex`='M' AND `join7`=1, 1, 0) as `j7_M`, IF(`sex`='F' AND `join7`=1, 1, 0) as `j7_F`, IF(`sex`='M' AND `live7`=1, 1, 0) as `l7_M`, IF(`sex`='F' AND `live7`=1, 1, 0) as `l7_F`, ";
    $sql .= "        IF(`sex`='M' AND `join8`=1, 1, 0) as `j8_M`, IF(`sex`='F' AND `join8`=1, 1, 0) as `j8_F`, IF(`sex`='M' AND `live8`=1, 1, 0) as `l8_M`, IF(`sex`='F' AND `live8`=1, 1, 0) as `l8_F`, ";
    $sql .= "        IF(`sex`='M' AND `join9`=1, 1, 0) as `j9_M`, IF(`sex`='F' AND `join9`=1, 1, 0) as `j9_F`, IF(`sex`='M' AND `live9`=1, 1, 0) as `l9_M`, IF(`sex`='F' AND `live9`=1, 1, 0) as `l9_F`, ";
    $sql .= "        IF(`sex`='M' AND `joinx`=1, 1, 0) as `jx_M`, IF(`sex`='F' AND `joinx`=1, 1, 0) as `jx_F`, IF(`sex`='M' AND `livex`=1, 1, 0) as `lx_M`, IF(`sex`='F' AND `livex`=1, 1, 0) as `lx_F`  ";
    $sql .= "    FROM `".$tbname."` WHERE (`invalidate` <= 0) ";
    $sql .= ") AS X WHERE 1=1 ";

    $sql .= " ";
    $sql .= " UNION ";
    $sql .= " ";
    $sql .= "SELECT ";
    $sql .= "    A.`group`, ";
    $sql .= "    A.`subgroup`, ";
    $sql .= "    concat(A.`group`,'-', A.`subgroup`) as `whole`, ";
    $sql .= "    sum(A.`M`) as M, sum(A.`F`) as F, ";
    $sql .= "    sum(A.`j1_M`) as j1M, sum(A.`j1_F`) as j1F, sum(A.`l1_M`) as l1M, sum(A.`l1_F`) as l1F, ";
    $sql .= "    sum(A.`j2_M`) as j2M, sum(A.`j2_F`) as j2F, sum(A.`l2_M`) as l2M, sum(A.`l2_F`) as l2F, ";
    $sql .= "    sum(A.`j3_M`) as j3M, sum(A.`j3_F`) as j3F, sum(A.`l3_M`) as l3M, sum(A.`l3_F`) as l3F, ";
    $sql .= "    sum(A.`j4_M`) as j4M, sum(A.`j4_F`) as j4F, sum(A.`l4_M`) as l4M, sum(A.`l4_F`) as l4F, ";
    $sql .= "    sum(A.`j5_M`) as j5M, sum(A.`j5_F`) as j5F, sum(A.`l5_M`) as l5M, sum(A.`l5_F`) as l5F, ";
    $sql .= "    sum(A.`j6_M`) as j6M, sum(A.`j6_F`) as j6F, sum(A.`l6_M`) as l6M, sum(A.`l6_F`) as l6F, ";
    $sql .= "    sum(A.`j7_M`) as j7M, sum(A.`j7_F`) as j7F, sum(A.`l7_M`) as l7M, sum(A.`l7_F`) as l7F, ";
    $sql .= "    sum(A.`j8_M`) as j8M, sum(A.`j8_F`) as j8F, sum(A.`l8_M`) as l8M, sum(A.`l8_F`) as l8F, ";
    $sql .= "    sum(A.`j9_M`) as j9M, sum(A.`j9_F`) as j9F, sum(A.`l9_M`) as l9M, sum(A.`l9_F`) as l9F, ";
    $sql .= "    sum(A.`jx_M`) as jxM, sum(A.`jx_F`) as jxF, sum(A.`lx_M`) as lxM, sum(A.`lx_F`) as lxF  ";
    $sql .= "FROM ( ";
    $sql .= "    SELECT `group`, `subgroup`, ";
    $sql .= "        IF(`sex`='M', 1, 0) as `M`, IF(`sex`='F', 1, 0) as `F`,";
    $sql .= "        IF(`sex`='M' AND `join1`=1, 1, 0) as `j1_M`, IF(`sex`='F' AND `join1`=1, 1, 0) as `j1_F`, IF(`sex`='M' AND `live1`=1, 1, 0) as `l1_M`, IF(`sex`='F' AND `live1`=1, 1, 0) as `l1_F`, ";
    $sql .= "        IF(`sex`='M' AND `join2`=1, 1, 0) as `j2_M`, IF(`sex`='F' AND `join2`=1, 1, 0) as `j2_F`, IF(`sex`='M' AND `live2`=1, 1, 0) as `l2_M`, IF(`sex`='F' AND `live2`=1, 1, 0) as `l2_F`, ";
    $sql .= "        IF(`sex`='M' AND `join3`=1, 1, 0) as `j3_M`, IF(`sex`='F' AND `join3`=1, 1, 0) as `j3_F`, IF(`sex`='M' AND `live3`=1, 1, 0) as `l3_M`, IF(`sex`='F' AND `live3`=1, 1, 0) as `l3_F`, ";
    $sql .= "        IF(`sex`='M' AND `join4`=1, 1, 0) as `j4_M`, IF(`sex`='F' AND `join4`=1, 1, 0) as `j4_F`, IF(`sex`='M' AND `live4`=1, 1, 0) as `l4_M`, IF(`sex`='F' AND `live4`=1, 1, 0) as `l4_F`, ";
    $sql .= "        IF(`sex`='M' AND `join5`=1, 1, 0) as `j5_M`, IF(`sex`='F' AND `join5`=1, 1, 0) as `j5_F`, IF(`sex`='M' AND `live5`=1, 1, 0) as `l5_M`, IF(`sex`='F' AND `live5`=1, 1, 0) as `l5_F`, ";
    $sql .= "        IF(`sex`='M' AND `join6`=1, 1, 0) as `j6_M`, IF(`sex`='F' AND `join6`=1, 1, 0) as `j6_F`, IF(`sex`='M' AND `live6`=1, 1, 0) as `l6_M`, IF(`sex`='F' AND `live6`=1, 1, 0) as `l6_F`, ";
    $sql .= "        IF(`sex`='M' AND `join7`=1, 1, 0) as `j7_M`, IF(`sex`='F' AND `join7`=1, 1, 0) as `j7_F`, IF(`sex`='M' AND `live7`=1, 1, 0) as `l7_M`, IF(`sex`='F' AND `live7`=1, 1, 0) as `l7_F`, ";
    $sql .= "        IF(`sex`='M' AND `join8`=1, 1, 0) as `j8_M`, IF(`sex`='F' AND `join8`=1, 1, 0) as `j8_F`, IF(`sex`='M' AND `live8`=1, 1, 0) as `l8_M`, IF(`sex`='F' AND `live8`=1, 1, 0) as `l8_F`, ";
    $sql .= "        IF(`sex`='M' AND `join9`=1, 1, 0) as `j9_M`, IF(`sex`='F' AND `join9`=1, 1, 0) as `j9_F`, IF(`sex`='M' AND `live9`=1, 1, 0) as `l9_M`, IF(`sex`='F' AND `live9`=1, 1, 0) as `l9_F`, ";
    $sql .= "        IF(`sex`='M' AND `joinx`=1, 1, 0) as `jx_M`, IF(`sex`='F' AND `joinx`=1, 1, 0) as `jx_F`, IF(`sex`='M' AND `livex`=1, 1, 0) as `lx_M`, IF(`sex`='F' AND `livex`=1, 1, 0) as `lx_F`  ";
    $sql .= "    FROM `".$tbname."` WHERE (`invalidate` <= 0) ";
    $sql .= ") AS A WHERE 1=1 ";
    $sql .= "GROUP BY A.`group`, A.`subgroup` ";
    $sql .= "ORDER BY CASE `whole` ";
    $sql .= "    WHEN '大會-合計' THEN 1001 ";
    $sql .= "    WHEN '大會-大會' THEN 1000 ";
    $sql .= "    WHEN '教育大組-合計'       THEN 2010";
    $sql .= "    WHEN '教育大組-大組'       THEN 2002";
    $sql .= "    WHEN '教育大組-粉專組'     THEN 2003 ";
    $sql .= "    WHEN '教育大組-聽抄組'     THEN 2004";
    $sql .= "    WHEN '庶務大組-合計'       THEN 3010";
    $sql .= "    WHEN '庶務大組-大組'       THEN 3001";
    $sql .= "    WHEN '庶務大組-報到組'     THEN 3002";
    $sql .= "    WHEN '庶務大組-生服組'     THEN 3003";
    $sql .= "    WHEN '庶務大組-服務引導組' THEN 3004";
    $sql .= "    WHEN '庶務大組-保健組'     THEN 3005";
    $sql .= "    WHEN '總務大組-合計'   THEN 4010";
    $sql .= "    WHEN '總務大組-大組'   THEN 4001";
    $sql .= "    WHEN '總務大組-場地組' THEN 4002";
    $sql .= "    WHEN '總務大組-監修組' THEN 4003";
    $sql .= "    WHEN '總務大組-環保組' THEN 4004";
    $sql .= "    WHEN '總務大組-會計組' THEN 4005";
    $sql .= "    WHEN '總務大組-資材組' THEN 4006";
    $sql .= "    WHEN '福田大組-合計'     THEN 5010";
    $sql .= "    WHEN '福田大組-大組'     THEN 5001";
    $sql .= "    WHEN '福田大組-公關組'   THEN 5002";
    $sql .= "    WHEN '福田大組-感恩餐會' THEN 5003";
    $sql .= "    WHEN '廣供大組-合計'        THEN 6010";
    $sql .= "    WHEN '廣供大組-大組'        THEN 6001";
    $sql .= "    WHEN '廣供大組-廣供/壇城組' THEN 6002";
    $sql .= "    WHEN '廣供大組-十供養組'    THEN 6003";
    $sql .= "    WHEN '廣供大組-水月觀音組'  THEN 6004";
    $sql .= "    WHEN '廣供大組-珠寶組'      THEN 6005";
    $sql .= "    WHEN '廣供大組-牌位'        THEN 6006";
    $sql .= "    WHEN '餐飲大組-合計'   THEN 7010";
    $sql .= "    WHEN '餐飲大組-大組'   THEN 7001";
    $sql .= "    WHEN '餐飲大組-餐食組' THEN 7002";
    $sql .= "    WHEN '餐飲大組-茶水組' THEN 7003";
    $sql .= "    WHEN '交通大組-合計'   THEN 8010";
    $sql .= "    WHEN '交通大組-大組'   THEN 8001";
    $sql .= "    WHEN '交通大組-機動組' THEN 8002";
    $sql .= "    WHEN '交通大組-接駁組' THEN 8003";
    $sql .= "    WHEN '交通大組-道路組' THEN 8004";
    $sql .= "    WHEN '交通大組-車場組' THEN 8005";
    $sql .= "    WHEN '多媒體影音-合計'   THEN 9020";
    $sql .= "    WHEN '多媒體影音-大組'   THEN 9000";
    $sql .= "    WHEN '多媒體影音-音響組' THEN 9001";
    $sql .= "    WHEN '多媒體影音-影像組' THEN 9002";
    $sql .= "    WHEN '多媒體影音-導播組' THEN 9003";
    $sql .= "    WHEN '多媒體影音-播放組' THEN 9004";
    $sql .= "    WHEN '多媒體影音-系統組' THEN 9005";
    $sql .= "    WHEN '多媒體影音-設備組' THEN 9006";
    $sql .= "    WHEN '多媒體影音-網傳組' THEN 9007";
    $sql .= "    WHEN '多媒體影音-空拍組' THEN 9008";
    $sql .= "    WHEN '多媒體影音-影製組' THEN 9009";
    $sql .= "    WHEN '多媒體影音-平拍組' THEN 9010";
    $sql .= "    WHEN '多媒體影音-美工組' THEN 9011";
    $sql .= "    WHEN '多媒體影音-資料組' THEN 9012";
    $sql .= "    WHEN '多媒體影音-宣傳組' THEN 9013";
    $sql .= "    WHEN '多媒體影音-行政組' THEN 9014";
    $sql .= "    WHEN '多媒體影音-培訓組' THEN 9015";
    $sql .= "    WHEN '法務組-合計'      THEN 10010";
    $sql .= "    WHEN '法務組-大組'      THEN 10001";
    $sql .= "    WHEN '海外組-合計'      THEN 11010";
    $sql .= "    WHEN '海外組-大組'      THEN 11001";
    $sql .= "    WHEN '護戒組-合計'      THEN 12010";
    $sql .= "    WHEN '護戒組-大組'      THEN 12001";
    $sql .= "    WHEN '師長飲食-合計'    THEN 13010";
    $sql .= "    WHEN '師長飲食-大組'    THEN 13001";
    $sql .= "    WHEN '觀音亭專區-合計'  THEN 14010";
    $sql .= "    WHEN '觀音亭專區-大組'  THEN 14001";
    $sql .= "    WHEN '光明燈專案-合計'  THEN 15010";
    $sql .= "    WHEN '光明燈專案-大組'  THEN 15002";
    $sql .= "    WHEN '總計'            THEN 20000";
    $sql .= "    ELSE 30000 ";
    $sql .= "END ASC";

    $record = mysql_query($sql);
    $numrows = mysql_num_rows($record);
    $prev_group = "";
    $prev_row = 5;
    while($row = mysql_fetch_array($record, MYSQL_ASSOC)) {
        $idx++;
        $iRow++;
        $c = 0;
        //$mantotal = $row['j1M'] +  $row['j2M'] + $row['j3M'] + $row['j4M'] + $row['j5M'] + $row['j6M'] + $row['j7M'];// + $row['j8M'] + $row['j9M'];
        //$womantotal = $row['j1F'] +  $row['j2F'] + $row['j3F'] + $row['j4F'] + $row['j5F'] + $row['j6F'] + $row['j7F'];// + $row['j8F'] + $row['j9F'];
        $objWorkSheet->setCellValue($col[$c++].$iRow, ($row['group'] == $prev_group ? '' : $row['group']))
                     ->setCellValue($col[$c++].$iRow, $row['subgroup'])
                     ->setCellValue($col[$c++].$iRow, $row['M'])
                     ->setCellValue($col[$c++].$iRow, $row['F'])
                     ->setCellValue($col[$c++].$iRow, $row['j1M'] + $row['j1F'])
                     ->setCellValue($col[$c++].$iRow, $row['l1M'])
                     ->setCellValue($col[$c++].$iRow, $row['l1F'])
                     ->setCellValue($col[$c++].$iRow, $row['j2M'] + $row['j2F'])
                     ->setCellValue($col[$c++].$iRow, $row['l2M'])
                     ->setCellValue($col[$c++].$iRow, $row['l2F'])
                     ->setCellValue($col[$c++].$iRow, $row['j3M'] + $row['j3F'])
                     ->setCellValue($col[$c++].$iRow, $row['l3M'])
                     ->setCellValue($col[$c++].$iRow, $row['l3F'])
                     ->setCellValue($col[$c++].$iRow, $row['j4M'] + $row['j4F'])
                     ->setCellValue($col[$c++].$iRow, $row['l4M'])
                     ->setCellValue($col[$c++].$iRow, $row['l4F'])
                     ->setCellValue($col[$c++].$iRow, $row['j5M'] + $row['j5F'])
                     ->setCellValue($col[$c++].$iRow, $row['l5M'])
                     ->setCellValue($col[$c++].$iRow, $row['l5F'])
                     ->setCellValue($col[$c++].$iRow, $row['j6M'] + $row['j6F'])
                     ->setCellValue($col[$c++].$iRow, $row['l6M'])
                     ->setCellValue($col[$c++].$iRow, $row['l6F'])
                     ->setCellValue($col[$c++].$iRow, $row['j7M'] + $row['j7F'])
                     ->setCellValue($col[$c++].$iRow, $row['l7M'])
                     ->setCellValue($col[$c++].$iRow, $row['l7F'])
                     ->setCellValue($col[$c++].$iRow, $row['j8M'] + $row['j8F'])
                     ->setCellValue($col[$c++].$iRow, $row['l8M'])
                     ->setCellValue($col[$c++].$iRow, $row['l8F']);
      if ($row['subgroup'] == "合計") {
        $objWorkSheet->getStyle("B".$iRow.":AB".$iRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objWorkSheet->getStyle("B".$iRow.":AB".$iRow)->getFill()->getStartColor()->setRGB('FFFF66');

        $objWorkSheet->mergeCells("A".$prev_row.":A".$iRow);
        //$objWorkSheet->getStyle("A".$prev_row.":A".$iRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$objWorkSheet->getStyle("A".$prev_row.":A".$iRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $prev_row = $iRow + 1;
      } else if ($row['subgroup'] == "總計") {
        $objWorkSheet->mergeCells("A".$iRow.":B".$iRow);
        $objWorkSheet->setCellValue("A".$iRow, $row['subgroup']);
        $objWorkSheet->getStyle("A".$iRow.":AB".$iRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objWorkSheet->getStyle("A".$iRow.":AB".$iRow)->getFill()->getStartColor()->setRGB('DDFFDD');
        //$objWorkSheet->getStyle("A".$prev_row.":A".$iRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$objWorkSheet->getStyle("A".$prev_row.":A".$iRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $prev_row = $iRow + 1;
      }
      //$objWorkSheet->getStyle("B".$iRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
      //$objWorkSheet->getStyle("B".$iRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      $prev_group = $row['group'];
    }

    $iRow += 1;


    $xwidth = array(20, 25,7,7,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5);
    // 設定欄位寛度
    for($w=0;$w<count($xwidth);$w++){
      $objWorkSheet->getColumnDimension($col[$w])->setWidth($xwidth[$w]);
    }

     // set border
    $range="A2:AB".$iRow;
    $objWorkSheet->getStyle($range)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objWorkSheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle($range)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    //$objWorkSheet->getStyle("B5:B".$iRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //$objWorkSheet->getStyle($range)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    //$objWorkSheet->getStyle($range)->getFill()->getStartColor()->setRGB('DDFFDD');//$objWorkSheet->getStyle("A2")->getFill()->getStartColor()->setRGB('B7B7B7');

    //$range="B5:B".$iRow;
    //$objWorkSheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    // 參與天數統計

    // 填寫資料
    $i=0;
    $sql="SELECT COUNT(*) as count8 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=8 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    //$statistic[$i]["item"]="參加全程人數";
    //$statistic[$i++]["value"]=$row["count8"];
    $join8 = $row["count8"];

    $sql="SELECT COUNT(*) as count7 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=7 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加全程人數";//"參加七天人數";
    $statistic[$i++]["value"]=$row["count7"] + $join8;

    $sql="SELECT COUNT(*) as count6 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=6 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加六天人數";
    $statistic[$i++]["value"]=$row["count6"];

    $sql="SELECT COUNT(*) as count5 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=5 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加五天人數";
    $statistic[$i++]["value"]=$row["count5"];

    $sql="SELECT COUNT(*) as count4 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=4 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加四天人數";
    $statistic[$i++]["value"]=$row["count4"];

    $sql="SELECT COUNT(*) as count3 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=3 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加三天人數";
    $statistic[$i++]["value"]=$row["count3"];

    $sql="SELECT COUNT(*) as count2 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=2 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加二天人數";
    $statistic[$i++]["value"]=$row["count2"];

    $sql="SELECT COUNT(*) as count1 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=1 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加一天人數";
    $statistic[$i++]["value"]=$row["count1"];

    // $sql="SELECT COUNT(*) as count0 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=0 AND `invalidate`<=0";
    // $record=mysql_query($sql);
    // $row=mysql_fetch_array($record, MYSQL_ASSOC);
    // $statistic[$i]["item"]="參加0天人數";
    // $statistic[$i++]["value"]=$row["count0"];

    $sql="SELECT COUNT(*) as countx FROM `".$tbname."` WHERE `joinclean`='0000000001' AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加打掃人數";
    $statistic[$i++]["value"]=$row["countx"];

    $iRow+=2;
    $iStartRow=($iRow+1);
    for($i=0;$i<count($statistic);$i++){
        $iRow++;
        $mergeitem=$col[0].$iRow.":".$col[1].$iRow;
        $objWorkSheet->mergeCells($mergeitem);
        $item=$col[0].$iRow;
        $objWorkSheet->setCellValue($item, $statistic[$i]["item"]);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $item=$col[2].$iRow;
        $objWorkSheet->setCellValue($item, $statistic[$i]["value"]);
    }

    $range="A".$iStartRow.":".$col[2].$iRow;
    $objWorkSheet->getStyle($range)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $range="A".$iStartRow.":".$col[1].$iRow;
    $objWorkSheet->getStyle($range)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objWorkSheet->getStyle($range)->getFill()->getStartColor()->setRGB('DDFFDD');

    // PERCENTAGE FORMAT
    //$range="E".$top.":E".$iRow;
    //$objPHPExcel->getActiveSheet()->getStyle($range)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
    $objWorkSheet->setTitle($table_title);// Rename worksheet
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');// Redirect output to a client’s web browser (Excel5)
    $fileheader="Content-Disposition: attachment;filename=\"".$table_title.".xls\"";//header('Content-Disposition: attachment;filename="simple.xls"');
    header($fileheader);
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');// If you're serving to IE 9, then the following may be needed

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>