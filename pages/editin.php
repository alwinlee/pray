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
//    echo strtotime($today_date)." - today <br>';
//    echo strtotime($expire_date)." - ".$_SESSION["expireday"]." - expire <br>';
if (strtotime($today_date) > strtotime($expire_date)){
    header("Location: ../index.php");
    //echo strtotime($today_date)." - today <br>';
    //echo strtotime($expire_date)." - expire <br>';
}

require_once("../api/lib/common.php");
require_once("../api/lib/params.php");
$auth=$_SESSION["auth"];
$userlevel=$_SESSION["userlevel"];
$groupexpire=$_SESSION["groupexpire"];
$expire=$_SESSION["expire2"];
$menu=checkAuth(1, $auth, $expire, $groupexpire);
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
    <link href="../resource/dataTables/css/jquery.dataTables.min.css" rel="stylesheet"><!-- MetisMenu CSS -->
    <link href="../resource/css/ucamp.css" rel="stylesheet"><!-- Custom CSS -->
    <link href="../resource/css/font-awesome.min.css" rel="stylesheet" type="text/css"><!-- Custom Fonts -->

    <style type="text/css">
    html, body{height:100%; margin:0;padding:0;font-family:Meiryo,"微軟正黑體","Microsoft JhengHei';}
    .container-fluid{height:90%;display:table;width:100%;padding:0;}
    .row-fluid{height:100%; display:table-cell; vertical-align: middle;}
    .centering{float:none;margin:0 auto;}
    .righting{float:right;margin:0 auto;}
    .sx-checkbox {width: 24px; height: 24px;}
    .mx-checkbox {width: 30px; height: 30px;}
    .sx-radio {width: 18px; height: 18px;}
    .mx-radio {width: 24px; height: 24px;}
    .lx-radio {width: 30px; height: 30px;}
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <?php include("menu.php"); ?>
        <div id="bPrintNotice" data-status="0"></div>
        <!-- Page Content -->
        <div id="page-wrapper" style="background-image: url('../resource/img/back.png');">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12" align="center">
                        <h3 class="page-header">義工資料更新</h3>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-1" align="center"></div>
                    <div class="col-lg-10" align="center">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="請輸入姓名或電話 ..." id="keyword">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="editinsearch">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1" align="center"></div>
                </div>

                <!-- /.row  show data -->
                <br>
                <div class="row">
                    <div class="col-lg-1" align="center"> </div>
                    <div class="col-lg-10" align="center" id="searchdata">
                    <table id="datagrid" class="table table-striped table-bordered" cellspacing="0" width="100%" style="display:none;" disabled>
                        <thead>
                            <th>選取</th>
                            <th>姓名</th>
                            <th>電話</th>
                            <th>母班班級</th>
                        </thead>
                    </table>
                    </div>
                    <div class="col-lg-1" align="center"> </div>
                </div>

                 <!-- /.row -->
                 <div class="row">
                 <div class="col-lg-1" align="center"></div>
                 <div class="col-lg-10" align="center"><hr></div>
                 <div class="col-lg-1" align="center"></div>
                 </div>

                <!-- /.row of member data -->
                <div class="row" class="memberdetaildata" id="memberdetaildata" style="display:none;">
                  <?php include("applyform.php"); ?>

                </div>
                  <div class="row">
                      <div class="col-lg-1" align="center"></div>
                      <div class="col-lg-10" align="center">
                          <hr>
                          <button type="button" class="btn btn-primary btn-lg btn-block" id="basic-submit">更新資料</button>
                      </div>
                      <div class="col-lg-1" align="center"></div>
                  </div>
                </div>
                <br>
                <br>
                <!-- /.value -->
                <?php include("data.php"); ?>
                <?php include("dialog.php"); ?>

                 <!-- /.row show data-->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <script src="../resource/js/jquery-2.1.4.min.js"></script><!-- jQuery -->
    <script src="../resource/js/bootstrap.min.js"></script><!-- Bootstrap Core JavaScript -->
    <script src="../resource/js/metisMenu.min.js"></script><!-- Metis Menu Plugin JavaScript -->
    <script src="../resource/dataTables/js/jquery.dataTables.min.js"></script><!-- Metis Menu Plugin JavaScript -->

    <script src="../resource/js/ucamp.js"></script><!-- Custom Theme JavaScript -->
    <script src="../resource/js/api.js?{F43BAD84-E62B-494D-9A51-863AF3C77EFC}"></script>
    <script src="../resource/js/editin.js?{F43BAD84-E62B-494D-9A51-863AF3C77EFC}" type="text/javascript" charset="utf-8"></script>
</body>

</html>
