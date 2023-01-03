<?php
session_start();
if(isset($_SESSION["account"])==false||$_SESSION["account"]==""||isset($_SESSION["auth"])==false||$_SESSION["area"]!="pray"){
    header("Location: ../index.php");
}
// check auth
date_default_timezone_set('Asia/Taipei');
$expire_date=$_SESSION["expireday"];
$today_date=new DateTime();
$today_date=date_format($today_date, 'Y-m-d');
//    echo strtotime($today_date)." - today <br>";
//    echo strtotime($expire_date)." - ".$_SESSION["expireday"]." - expire <br>";
if (strtotime($today_date) > strtotime($expire_date)){
    header("Location: ../index.php");
    //echo strtotime($today_date)." - today <br>";
    //echo strtotime($expire_date)." - expire <br>";
}

require_once("../api/lib/common.php");
require_once("../api/lib/params.php");
$auth=$_SESSION["auth"];
$userlevel=$_SESSION["userlevel"];
$groupexpire=$_SESSION["groupexpire"];
$expire=$_SESSION["expire1"];
$menu=checkAuth(0, $auth, $expire, $groupexpire);
if($menu!="YES"){
    unset($_SESSION["area"]);
    unset($_SESSION["username"]);
    unset($_SESSION["account"]);
    unset($_SESSION["username"]);
    unset($_SESSION["userlevel"]);
    unset($_SESSION["auth"]);
    unset($_SESSION["key"]);
    header("Location: ../index.php");exit;
}

date_default_timezone_set('Asia/Taipei');
$currDate=date('Y-m-d');
$apply=$_SESSION["account"];
$debug="NO";
//$debug="YES";
?>
<!DOCTYPE html>
<html lang="en"><META content="IE=11.0000" http-equiv="X-UA-Compatible">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../resource/img/ucamp.ico">
    <link rel="shortcut icon" href="../resource/img/ucamp.ico">
    <title>
<?php
    echo $sysname.'義工管理';
?>
    </title>
    <link href="../resource/css/bootstrap.min.css" rel="stylesheet"><!-- Bootstrap Core CSS -->
    <link href="../resource/css/metisMenu.min.css" rel="stylesheet"><!-- MetisMenu CSS -->
    <link href="../resource/css/ucamp.css" rel="stylesheet"><!-- Custom CSS -->
    <link href="../resource/css/font-awesome.min.css" rel="stylesheet" type="text/css"><!-- Custom Fonts -->
    <style type="text/css">
    html, body{height:100%; margin:0;padding:0;font-family:Meiryo,"微軟正黑體","Microsoft JhengHei";}
    .container-fluid{height:90%;display:table;width:100%;padding:0;}
    .row-fluid{height:100%; display:table-cell; vertical-align: middle;}
    .centering{float:none;margin:0 auto;}
    .righting{float:right;margin:0 auto;}
    .sx-checkbox {width: 24px; height: 24px;}
    .mx-checkbox {width: 30px; height: 30px;}
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <?php include("menu.php"); ?>

        <!-- Page Content -->
        <div id="page-wrapper" style="background-image: url('../resource/img/back.png');">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12" align="center">
                        <h4 class="page-header">新增義工報名</h4>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-1" align="center"></div>
                    <div class="col-lg-10" align="left">
                    <div class="panel panel-success">
                        <div class="panel-heading"> <h1 class="panel-title">報名填寫說明</h1></div>
                        <div class="panel-body">
                        <p>1. 一律網路報名，各欄均須填寫，請勿重複報名。</p>
                    </div>
                    </div>
                    </div>
                    <div class="col-lg-1" align="center"></div>
                </div>

                <!-- /.row -->
                <?php include("applyform.php"); ?>


                 <!-- /.row -->
                 <div class="row"><div class="col-lg-12" align="center"></div></div>
                 <hr>
                 <div class="row">
                     <div class="col-lg-1" align="center"></div>
                     <div class="col-lg-10" align="center">
                         <button type="button" class="btn btn-primary btn-lg btn-block" id="basic-submit">報　　名</button>
                     </div>
                     <div class="col-lg-1" align="center"></div>
                 </div>
                 <br><br><br><br><br><br>
                 <?php
                     if ($debug=="YES"){
                         echo '<div class="row">';
                         echo '<div class="col-lg-4" align="center"></div>';
                         echo '<div class="col-lg-4" align="center">';
                         echo '<button type="button" class="btn btn-primary btn-lg btn-block" id="basic-testdata">測試</button>';
                         echo '<button type="button" class="btn btn-primary btn-lg btn-block" id="basic-demo">demo</button>';
                         echo '</div>';
                         echo '<div class="col-lg-4" align="center"></div>';
                         echo '</div>';
                     }
                 ?>
                 <!---->
                 <!-- /.value -->
                 <?php include("data.php"); ?>
                 <?php include("dialog.php"); ?>

                 <div class="col-lg-12" align="center" id="searchdata"></div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <script src="../resource/js/jquery-2.1.4.min.js"></script><!-- jQuery -->
    <script src="../resource/js/bootstrap.min.js"></script><!-- Bootstrap Core JavaScript -->
    <script src="../resource/js/metisMenu.min.js"></script><!-- Metis Menu Plugin JavaScript -->
    <script src="../resource/js/ucamp.js"></script><!-- Custom Theme JavaScript -->
    <script src="../resource/js/api.js?{51DDD4FB-D26B-4C7F-A56A-2129EEE72173}"></script>
    <script src="../resource/js/addin.js?{51DDD4FB-D26B-4C7F-A56A-2129EEE72173}" type="text/javascript" charset="utf-8"></script>
</body>

</html>
