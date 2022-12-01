<?php
    session_start();
    if(isset($_SESSION["account"])==false||$_SESSION["account"]==""||$_SESSION["area"]!="pray"){
        $code=-2;
        $desc="auth failed";
        $json_ret=array("code"=>$code,"desc"=>$desc);echo json_encode($json_ret);exit;
    }

    header("Content-type: application/json; charset=utf-8");
    require_once("../lib/connmysql.php");
    require_once("../lib/common.php");
    ini_set("error_reporting",0);
    ini_set("display_errors","Off"); // On : open, O

    // check db exist
    $currY=date('Y');
    $currM=date('m');
    $currD=date('d');
    if ($currM>=10){$currY+=1;}
    $tbname="pray_".$currY;
    check_pray_db($tbname);

    $checkin1=0;
    $checkin2=0;
    $checkin3=0;
    $checkin4=0;
    $checkin5=0;
    $checkin6=0;
    $checkin7=0;
    $checkin8=0;
    $checkin9=0;
    $checkinx=0;

    $code=-1;
    $desc="data unknown";
    $data=json_decode(file_get_contents('php://input'), true);

    if(isset($data['id'])==false||isset($data['checkin'])==false){
        $json_ret=array("code"=>$code,"desc"=>$desc);echo json_encode($json_ret);exit;
    }

    $id = $data['id'];
    $checkin = $data['checkin'];
    if($id <= 0 || $checkin < 0 || $checkin > 1){
        $json_ret=array("code"=>$code,"desc"=>$desc);echo json_encode($json_ret);exit;
    }
    $join1=0;
    $join2=0;
    $join3=0;
    $join4=0;
    $join5=0;
    $join6=0;
    $join7=0;
    $join8=0;
    $join9=0;
    $joinx=0;
    $sql="select * from `".$tbname."` where `id`=".$id." limit 1";
    $record=mysql_query($sql);
    $numrows=mysql_num_rows($record);
    if ($numrows > 0){
        $row=mysql_fetch_array($record, MYSQL_ASSOC);
        $join1=$row['join1'];
        $join2=$row['join2'];
        $join3=$row['join3'];
        $join4=$row['join4'];
        $join5=$row['join5'];
        $join6=$row['join6'];
        $join7=$row['join7'];
        $join8=$row['join8'];
        $join9=$row['join9'];
        $joinx=$row['joinx'];
    }

    if($checkin>=1){
        if ($join1>0){$checkin1=1;}
        if ($join2>0){$checkin2=1;}
        if ($join3>0){$checkin3=1;}
        if ($join4>0){$checkin4=1;}
        if ($join5>0){$checkin5=1;}
        if ($join6>0){$checkin6=1;}
        if ($join7>0){$checkin7=1;}
        if ($join8>0){$checkin8=1;}
        if ($join9>0){$checkin9=1;}
        if ($joinx>0){$checkinx=1;}
    }

    //更新義工報到資料-並記錄是第幾天報到的
    $sql="update `".$tbname."` set `checkin`=".$checkin.",`checkin1`=".$checkin1.",`checkin2`=".$checkin2.",`checkin3`=".$checkin3;
    $sql.=",`checkin4`=".$checkin4.",`checkin5`=".$checkin5.",`checkin6`=".$checkin6;
    $sql.=",`checkin7`=".$checkin7.",`checkin8`=".$checkin8.",`checkin9`=".$checkin9.",`checkinx`=".$checkinx;
    $sql.=" where `id`=".$id." limit 1";

    $record = mysql_query($sql);

    $code=1;
    $desc="success";
    $json_ret=array("code"=>$code,"desc"=>$desc,"result"=>$record);
    echo json_encode($json_ret);//header("Content-Type: text/html; charset=utf-8");
?>

