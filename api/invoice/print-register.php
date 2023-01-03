<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");

    session_start();
    if(isset($_SESSION["account"])==false||$_SESSION["account"]==""||$_SESSION["area"]!="pray"){
        echo "-1";exit;
    }

    ini_set('memory_limit',-1);
    ini_set("error_reporting",0);
    ini_set("display_errors","Off"); // On : open, Off : close

    set_time_limit(1200);
    date_default_timezone_set('Asia/Taipei');//	date_default_timezone_set('Europe/London');
    if (PHP_SAPI=='cli'){die('This example should only be run from a Web Browser');}

    require_once("../lib/connmysql.php");
    require_once("../../resource/tcpdf/tcpdf.php");
    require_once("../lib/invoicecommon.php");
    require_once("../lib/common.php");
    // check db exist
    $currY=date('Y');
    $currM=date('m');
    if ($currM>=10){$currY+=1;}
    $tbname="pray_".$currY;
    check_pray_db($tbname);

    $printarea=$_POST["printarea"];
    $notify=$_POST["notify"];
    $group=$_POST["group"];
    $subgroup=$_POST["subgroup"];
    $grouptext=$_POST["grouptext"];
    $subgrouptext=$_POST["subgrouptext"];
    $idx=$_POST["idx"];
    $yearstart=$_POST["yearstart"];
    $yearend=$_POST["yearend"];

    // page information
    $table_title= $currY."年 祈願法會 義工報到通知單";

    // page setting
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Noman');
    $pdf->SetTitle($table_title);
    $pdf->SetSubject($table_title);

    $tablename = "";//"報到通知單";
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $tablename, $table_title);

    // set header and footer fonts
    $pdf->setHeaderFont(Array('droidsansfallback', 'center', 8));
    $pdf->setFooterFont(Array('droidsansfallback', 'right', 4));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(8, 12, 8, 0);//(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(3);
    $pdf->SetFooterMargin(3);

    // set auto page breaks
    //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set font
    $pdf->SetFont('droidsansfallback','', 8);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // define barcode style
    $barcodestyle = array('position'=>'','align'=>'C','stretch'=>false,
                          'fitwidth'=>true,'cellfitalign'=>'','border'=>true,'hpadding'=>'auto',
                          'vpadding'=>'auto','fgcolor'=>array(0,0,0),
                          'bgcolor'=>false, //array(255,255,255),
                          'text'=>true,'font'=>'helvetica','fontsize'=>8,'stretchtext'=>4);

    $invoice_title=getPDFtitle($table_title);
    //$info=getcleanPDFinfo($currY);
    $html_line=getPDFLn();
    $spLine=getPDFSPLn();

    $area_array=array("A"=>"北區", "H"=>"竹區", "B"=>"中區", "C"=>"雲嘉", "D"=>"園區", "E"=>"南區", "F"=>"高區", "G"=>"海外");
    $notify_array=array(0=>"各組組長", 1=>"研討班", 2=>"各組組長");
    $sex_array=array("M"=>"男", "F"=>"女");
    $conditaion="";

    if (isset($idx)==true && $idx > 0){
        if ($conditaion!=""){$conditaion.=" AND ";}
        $conditaion.=" `id`=".$idx;
    } else {
        if (isset($printarea) && $printarea!="0" && $printarea!="*" && $printarea!="") {
            if ($conditaion!=""){$conditaion.=" AND ";}
            $conditaion.=" `area`='".$printarea."' ";
        }

        if ($notify==1 || $notify==2){ // 研討母班 || 各組組長
            if ($conditaion!=""){$conditaion.=" AND ";}
            $conditaion.=" `notify`=".$notify." ";
        } else if ($notify==3){ //各組組長或未填寫
            if ($conditaion!=""){$conditaion.=" AND ";}
            $conditaion.=" `notify`!=1 ";
        }
        // 大組
        if (isset($grouptext) && $grouptext!="-" && $grouptext!="0" && $grouptext!="") {
            if ($conditaion!=""){$conditaion.=" AND ";}
            $conditaion.=" `group`='".$grouptext."' ";
        }
        // 小組
        if (isset($subgrouptext) && $subgrouptext!="-" && $subgrouptext!="0" && $subgrouptext!="") {
            if ($conditaion!=""){$conditaion.=" AND ";}
            $conditaion.=" `subgroup`='".$subgrouptext."' ";
        }
        //if (isset($yearstart)&&isset($yearend)) {
        //    $conditaion.=" `applydate`>='".$yearstart."' AND `applydate`<='".$yearend."'";
        //}
    }
    if ($conditaion!=""){$conditaion.=" AND ";}
    $conditaion.=" `invalidate`<=0 ";


    if ($conditaion!=""){
        $sql="select * from `".$tbname."` where ( ".$conditaion." ) order by `id`";
    } else {
        $sql="select * from `".$tbname."` order by `id`";
    }

    $result=mysql_query($sql);

    $count=0;
    $stu_name='';
    $stu_group='';
    $stu_subgroup='';
    while($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
        $pdf->AddPage();// add a page
        $pdf->SetFont('droidsansfallback', '', 10);
        // if ($count%2==0){
        //     $pdf->AddPage();// add a page
        //     $pdf->SetFont('droidsansfallback', '', 10);
        // }else{
        //     //$pdf->writeHTML($html_line, true, false, false, false, '');
        //     $pdf->writeHTML($spLine, true, false, false, false, '');
        // }
        $stu_area=$area_array[$row["area"]];
        $stu_clsarea=$row["classarea"];
        $stu_class=$row["classroom"];
        $stu_name=$row["name"];
        $stu_group=$row["group"];
        $stu_subgroup=$row["subgroup"];

        $info=getPDFinfo($currY,$row["area"],$row["notify"], $row["group"],$row["subgroup"]);

        //$stu_barcode=$row["barcode"];
        $stu_barcode = str_replace("#", "X", $row["barcode"]);
        $stu_barcode = $currY.$stu_barcode; //'2O2O'.$stu_barcode;
        $stu_invoicewhere=$notify_array[$row["notify"]];
        $stu_sex=$sex_array[$row["sex"]];
        $params=$pdf->serializeTCPDFtagParameters(array($stu_barcode, 'C39', '', '', '', 16, 0.4, $barcodestyle, 'N'));
        $student_info=getPDFstudent($stu_area,$stu_clsarea,$stu_class,$stu_name,$stu_invoicewhere,$stu_sex,$stu_group,$stu_subgroup,$params);

        $pdf->writeHTML($invoice_title, true, false, false, false, '');
        $pdf->writeHTML($student_info, true, false, false, false, '');
        $pdf->writeHTML($info, true, false, false, false, '');
        $count++;
    }

/*
    // ---------------------------------------------------------
    $pdf->AddPage();// add a page
    $pdf->SetFont('droidsansfallback', '', 12);
    //$pdf->writeHTML($sql, true, false, false, false, '');
    $pdf->writeHTML($invoice_title, true, false, false, false, '');
    $pdf->writeHTML($student_info, true, false, false, false, '');
    $pdf->writeHTML($info, true, false, false, false, '');


    $pdf->writeHTML($html_line, true, false, false, false, '');
    $pdf->writeHTML($spLine, true, false, false, false, '');

    $pdf->writeHTML($invoice_title, true, false, false, false, '');
    $pdf->writeHTML($student_info, true, false, false, false, '');
    $pdf->writeHTML($info, true, false, false, false, '');
*/
    $filename = 'receipt-list.pdf';//$classid.
    if ($count == 1 && $stu_name) {
      $filename = $stu_group.'-'.$stu_name.'-報到通知單.pdf';//$stu_group.'-'.str_replace('/', '', $stu_subgroup).'-'.$stu_name.'-報到通知單.pdf';//$classid.
    } else if ($stu_group && $stu_subgroup) {
      $filename = $stu_group.'-'.str_replace('/', '', $stu_subgroup).'-報到通知單.pdf';//$stu_group.'-'.$stu_subgroup."-報到通知單.pdf";
    }

    $pdf->Output($filename, 'D');

	//============================================================+
	// END OF FILE
	//============================================================+
?>