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
    $draw=$data['data']['draw'];
    $start=$data['data']['start'];
    $length=$data['data']['length'];
    $register=$data['data']['register'];

    //查詢登入會員資料
    // check db exist
    $currY=date('Y');
    $currM=date('m');
    if ($currM>=10){$currY+=1;}
    $tbname="pray_".$currY;
    check_pray_db($tbname);

    $sql="SELECT `group`, `subgroup`, COUNT(*) as count, SUM(`checkin`) as `realcount` FROM `".$tbname."` where (`invalidate`<=0) group by `subgroup` order by `group` ASC ,`subgroup` ASC";

    $record=mysql_query($sql);
    $numrows=mysql_num_rows($record);
    if($numrows<=0){
        $json_ret=array("draw"=>$draw,"recordsTotal"=>0,"recordsFiltered"=>0,"data"=>"");
        echo json_encode($json_ret);
        exit;
    }
    $i=0;
    while($row=mysql_fetch_array($record, MYSQL_ASSOC))
    {
        $statistic[$i]["item"] = $row["group"]."-".$row["subgroup"];
        $statistic[$i]["valueS"] = $row["count"];
        $statistic[$i]["valueR"] = $row["realcount"];
        $i++;
    }

    $sql="SELECT COUNT(*) as count8, SUM(`checkin`) as `realcount8` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=8 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加全程人數";
    $statistic[$i]["valueS"]=$row["count8"];
    $statistic[$i++]["valueR"] = $row["realcount8"];

    $sql="SELECT COUNT(*) as count7, SUM(`checkin`) as `realcount7` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=7 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加七天人數";
    $statistic[$i]["valueS"]=$row["count7"];
    $statistic[$i++]["valueR"] = $row["realcount7"];

    $sql="SELECT COUNT(*) as count6, SUM(`checkin`) as `realcount6` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=6 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加六天人數";
    $statistic[$i]["valueS"]=$row["count6"];
    $statistic[$i++]["valueR"] = $row["realcount6"];


    $sql="SELECT COUNT(*) as count5, SUM(`checkin`) as `realcount5` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=5 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加五天人數";
    $statistic[$i]["valueS"]=$row["count5"];
    $statistic[$i++]["valueR"] = $row["realcount5"];

    $sql="SELECT COUNT(*) as count4, SUM(`checkin`) as `realcount4` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=4 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加四天人數";
    $statistic[$i]["valueS"]=$row["count4"];
    $statistic[$i++]["valueR"] = $row["realcount4"];

    $sql="SELECT COUNT(*) as count3, SUM(`checkin`) as `realcount3` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=3 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加三天人數";
    $statistic[$i]["valueS"]=$row["count3"];
    $statistic[$i++]["valueR"] = $row["realcount3"];

    $sql="SELECT COUNT(*) as count2, SUM(`checkin`) as `realcount2` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=2 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加二天人數";
    $statistic[$i]["valueS"]=$row["count2"];
    $statistic[$i++]["valueR"] = $row["realcount2"];

    $sql="SELECT COUNT(*) as count1, SUM(`checkin`) as `realcount1` FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=1 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"] = "參加一天人數";
    $statistic[$i]["valueS"] = $row["count1"];
    $statistic[$i++]["valueR"] = $row["realcount1"];

    $code=1;
    $desc="success";
    $json_ret=array("draw"=>$draw,"recordsTotal"=>($numrows+3),"recordsFiltered"=>($numrows+3),"data"=>$statistic);
    echo json_encode($json_ret);//header("Content-Type: text/html; charset=utf-8");
?>

