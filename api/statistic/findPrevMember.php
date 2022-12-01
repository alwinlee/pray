<?php
    session_start();
    if(isset($_SESSION["account"])==false||$_SESSION["account"]==""||$_SESSION["area"]!="pray"){
        $json_ret=array("draw"=>$draw,"recordsTotal"=>0,"recordsFiltered"=>0,"data"=>"");
        echo json_encode($json_ret);
        exit;
    }

    header("Content-type: application/json; charset=utf-8");
    require_once("../lib/connmysql.php");
    require_once("../lib/common.php");
    ini_set("error_reporting",0);
    ini_set("display_errors","Off"); // On : open, O

    $code=-1;
    $desc="data unknown";
    $data=json_decode(file_get_contents('php://input'), true);

    if(isset($data['data']['draw'])==false||isset($data['data']['start'])==false||isset($data['data']['length'])==false||isset($data['data']['register'])==false){
        $json_ret=array("draw"=>$draw,"recordsTotal"=>0,"recordsFiltered"=>0,"data"=>"");
        echo json_encode($json_ret);
        exit;
    }

    // check db exist
    $currY=date('Y');
    $currM=date('m');
    if ($currM>=10){$currY+=1;}
    $tbname="pray_".$currY;
    check_pray_db($tbname);

    $draw=$data['data']['draw'];
    $start=$data['data']['start'];
    $length=$data['data']['length'];
    $register=$data['data']['register'];
    $groupkey=$data['data']['groupkey'];

    //查詢登入會員資料
    $groupFilterAry=array("大會","教育大組","庶務大組","總務大組","福田大組","廣供大組","餐飲大組","交通大組","多媒體影音","法務組","海外組","護戒組","師長飲食","觀音亭專區","光明燈專案");
    if ($groupkey=="*"){
        $sql="select * from `".$tbname."` where `invalidate`<=0 order by `group`,`subgroup`,`type` limit ".$start.",".($length);
    }else{
        $sql="select * from `".$tbname."` where (`group`='".$groupFilterAry[$groupkey]."' and `invalidate`<=0 ) order by `group`,`subgroup`,`type` limit ".$start.",".($length);
    }

    $record=mysql_query($sql);
    $numrows=mysql_num_rows($record);
    if($numrows<=0){
        $json_ret=array("draw"=>$draw,"recordsTotal"=>0,"recordsFiltered"=>0,"data"=>"");
        echo json_encode($json_ret);
        exit;
    }

    while($row=mysql_fetch_array($record, MYSQL_ASSOC))
    {
        $member[] = $row;
    }


    if ($groupkey=="*"){
        $sql="select COUNT(*) from `".$tbname."` where (`joinclean`='0000000001' AND `invalidate`<=0";
    }else{
        $sql="select COUNT(*) from `".$tbname."` where (`joinclean`='0000000001' AND `group`='".$groupFilterAry[$groupkey]."' and `invalidate`<=0 )";
    }
    $record=mysql_query($sql);
    $totalrows=100;
    $numrows=mysql_num_rows($record);
    if($numrows>0){
        $row1=mysql_fetch_array($record, MYSQL_NUM);
        $totalrows=$row1[0];
    }

    $code=1;
    $desc="success";
    $json_ret=array("draw"=>$draw,"recordsTotal"=>$totalrows,"recordsFiltered"=>$totalrows,"data"=>$member);
    echo json_encode($json_ret);//header("Content-Type: text/html; charset=utf-8");
?>

