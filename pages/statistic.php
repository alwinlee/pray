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
    $groupexpire=$_SESSION["groupexpire"];
    $groupok=$_SESSION["group"];
    $expire=$_SESSION["expire4"];

    $menu=checkAuth(3, $auth, $expire, $groupexpire);
    if($menu=="NO"){
        unset($_SESSION["area"]);
        unset($_SESSION["username"]);
        unset($_SESSION["account"]);
        unset($_SESSION["username"]);
        unset($_SESSION["userlevel"]);
        unset($_SESSION["auth"]);
        unset($_SESSION["key"]);
        header("Location: ../index.php");
        exit;
    }

    $groupkey=$groupok[3];
    if ($menu!="YES"){$groupkey=$menu;}

//echo $groupok."<br>";
//echo $groupexpire."<br>";
//echo $groupkey."<br>";
?>
<!DOCTYPE html>
<html lang="en">
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
    html, body{height:100%; margin:0;padding:0;font-family:Meiryo,"微軟正黑體","Microsoft JhengHei";}
    .container-fluid{height:90%;display:table;width:100%;padding:0;}
    .row-fluid{height:100%; display:table-cell; vertical-align: middle;}
    .centering{float:none;margin:0 auto;}
    .righting{float:right;margin:0 auto;}
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
                        <h3 class="page-header">資料查詢與匯出</h3>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-2" align="center"></div>
                    <div class="col-lg-8" align="center">
                        <div class="input-group custom-search-form">
                            <!--<input type="text" class="form-control" placeholder="請輸入組別、錄取編號、姓名、電話或學校關鍵字查詢 ..." id="keyword">-->
                            <select class="form-control" id="exporttype">
                              <?php
                                  $list="";
                                  $livelist="";
                                  $grouplist="";
                                  if ($groupkey=="*"){
                                      $list="義工名冊(總表)";
                                      $livelist="義工名冊(住宿用-總表)";
                                      $grouplist="義工名冊(分組)";
                                  }
                                  else if ($groupkey=="0"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="1"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="2"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="3"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="4"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="5"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="6"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="7"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="8"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  else if ($groupkey=="9"){ $list="義工名冊(".$params_maingroups[$groupkey].")";}
                                  if ($list!=""){
                                      if ($groupkey=="*"){
                                          echo '<option value=0>'.$list.'</option>';
                                      } else {
                                          echo '<option value="'.$groupkey.'">'.$list.'</option>';
                                      }
                                  }
                                  if ($livelist!=""){
                                      echo "<option value=102>".$livelist."</option>";
                                  }
                                  if ($grouplist!=""){
                                      echo "<option value=101>".$grouplist."</option>";
                                  }
                                  if ($groupkey=="*"){
                                      echo "<option value=110>報名統計</option>";
                                      echo "<option value=111>打掃報到統計</option>";
                                      echo "<option value=112>正行報到統計</option>";
                                      echo "<option value=113>打掃報到名冊</option>";
                                      echo "<option value=114>正行報到名冊</option>";
                                      //echo "<option value=20>報到統計</option>";
                                  }
                                  //echo "<option value=8>數據統計</option>";
                              ?>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="list">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="export">
                                    <i class="fa fa-download"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2" align="center"></div>
                </div>
                 <!-- /.row -->
                 <div class="row"><div class="col-lg-12" align="center"><br></div></div>

                <div class="row">
                    <div class="col-lg-12" align="center" id="searchdata">
                    <table id="datagrid" class="table table-striped table-bordered" cellspacing="0" width="100%" style="display:none;" disabled>
                        <thead>
                            <th>報到</th>
                            <th>錄取編號</th>
                            <th>姓名</th>
                            <th>學校</th>
                            <th>科系</th>
                        </thead>
                    </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12" align="center" id="searchdata">
                    </div>
                </div>
                 <!-- /.value -->

                 <?php
                     include("data.php");
                     echo "<input type='hidden' id='groupkey' class='groupkey' name='groupkey' value='".$groupkey."' />";
                 ?>
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
    <script src="../resource/js/api.js?{CEB87E65-C982-482A-8016-17D959F55212}" type="text/javascript" charset="utf-8"></script>
    <script src="../resource/js/statistic.js?{CEB87E65-C982-482A-8016-17D959F55212}" type="text/javascript" charset="utf-8"></script>
</body>

</html>
