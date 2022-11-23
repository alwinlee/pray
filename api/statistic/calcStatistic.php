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

    $sql="SELECT `group`, `subgroup`, concat(`group`, '-',`subgroup`) as `whole`, COUNT(*) as count FROM `".$tbname."` where (`invalidate`<=0) group by `group`, `subgroup` ";
    //$sql.="order by `group` ASC ,`subgroup` ASC";
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
        $statistic[$i]["value"] = $row["count"];
        $i++;
    }

    $sql="SELECT COUNT(*) as count8 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)>=7 AND `invalidate`<=0";
    $record=mysql_query($sql);
    $row=mysql_fetch_array($record, MYSQL_ASSOC);
    $statistic[$i]["item"]="參加全程人數";
    $statistic[$i++]["value"]=$row["count8"];

    //$sql="SELECT COUNT(*) as count7 FROM `".$tbname."` WHERE (join1+join2+join3+join4+join5+join6+join7+join8)=7 AND `invalidate`<=0";
    //$record=mysql_query($sql);
    //$row=mysql_fetch_array($record, MYSQL_ASSOC);
    //$statistic[$i]["item"]="參加七天人數";
    //$statistic[$i++]["value"]=$row["count7"];

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


    $code=1;
    $desc="success";
    $json_ret=array("draw"=>$draw,"recordsTotal"=>($numrows+8),"recordsFiltered"=>($numrows+8),"data"=>$statistic);
    echo json_encode($json_ret);//header("Content-Type: text/html; charset=utf-8");
?>

