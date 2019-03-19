<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html>
                    <head>
                    <meta charset="utf-8">
                    <!--以下是bootstrap簡單框架(Ch)
                    by http://www.w3big.com/zh-TW/bootstrap/bootstrap-intro.html-->
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">  
                    <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
                    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                    <!--以下是固定在頂部的導覽列(TW)
                    by http://www.w3big.com/zh-TW/bootstrap/bootstrap-navbar.html-->
                    <link rel="stylesheet" href="http://cdn.static.w3big.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
                    <script src="http://cdn.static.w3big.com/libs/jquery/2.1.1/jquery.min.js"></script>
                    <script src="http://cdn.static.w3big.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                    <style>
                        body {
                            padding-top: 70px;
                        }
                    </style>
                    </head>
                    <body>
                        <!-----------頂部固定導覽列----------->
                        <nav class="navbar navbar-default navbar-fixed-top" role="navigation"> 
                            <div class="container-fluid"> 
                            <div class="navbar-header"> 
                                <a class="navbar-brand" href="#">火車訂票系統</a> 
                            </div> 
                            <div> 
                                <ul class="nav navbar-nav">
                                    <li><a href="http://localhost/tickets.html">查詢訂票</a></li> 
                                    <li class="active"><a href="http://localhost/code.html">車次選擇</a></li> 
                                    <li><a href="http://localhost/cancel.html">取消訂票</a></li>
                                    <li><a href="http://localhost/bookingcode.html">查詢代碼</a></li>
                                </ul> 
                            </div> 
                            </div> 
                        </nav>
                        <div class="container">
                          <div class="jumbotron">
<?php
require('login_sql.php');
$station=array("台北","桃園","新竹","苗栗","台中","彰化","嘉義","台南","高雄","屏東");
$station_num = array("台北"=>'0',"桃園"=>'1',"新竹"=>'2',"苗栗"=>'3',"台中"=>'4',"彰化"=>'5',"嘉義"=>'6',"台南"=>'7',"高雄"=>'8',"屏東"=>'9');
$start = $_GET['start'];
$end = $_GET['end'];
$count = $_GET['count'];
$traincode = $_GET['traincode'];
$i = 0;
$prior = [];
while(@$_GET["station$i"] != null){
    $prior[$i] = explode(",",$_GET["station$i"]);
    $i++;
}
//print_r($prior);
$result1 = mysql_query("select origin from tickets_$traincode where tick_no = 1");
$stat_count = mysql_fetch_array($result1);
$real = $station_num[$stat_count[0]];


$result1 = mysql_query("select count(id) from matrix_$traincode");
$v = mysql_fetch_array($result1);

$cou = 0;

foreach($prior as $a){
    $cou = 0;
    printpath($a[$v[0]],$a[$v[0]+1],$a);
    for($i = 0; $i < $v[0]; $i++){
        if($a[$i] != $a[$v[0]]){
            $cou++;
        }
    }
    echo " : 共需 ";
    echo $count * ($cou + 1);
    echo "張票";
    echo "<p></p>";
}
//print_r($prior);
echo "<form method = get action = setstand.php>";
$i = 0;
foreach($prior as $j){
    $output=implode(",",$j);
    echo "<input type = hidden name = station$i value = $output>";
    $i++;
}
echo "<input type = hidden name = start value=$start>";
echo "<input type = hidden name = end value=$end>";
echo "<input type = hidden name = count value=$count>";
echo "<input type = hidden name=traincode value=$traincode>";
require('popup_window.php');

function printpath($start,$end,$prior){
    global $v;
    global $station;
    global $real;

    for($i = 0; $i < $v[0]; $i++){
        $prior2[$i] = -1;
    }
    echo $station[$start+$real]." -> ";
    $i = $prior[$end];
    $j = 0;
    while($i != $start){
        $prior2[$j] = $i;
        $i = $prior[$i];
        $j++;
    }
    for($i = count($prior2) - 1; $i >= 0; $i--){
        if($prior2[$i] != -1){
            echo $station[$prior2[$i]+$real]." -> ";
        }
    }
    echo $station[$end+$real];
    unset($prior2);
}
?>
                            </div>
                        </div>
                        </body>
</html>
