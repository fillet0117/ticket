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
$start=$_GET['start_pass'];
$end = $_GET['end_pass'];
$count = $_GET['count_pass'];
$traincode = $_GET['traincode'];
//echo $start,$end;
$result = mysql_query("select origin,dest from tickets_$traincode");
$rowcount = mysql_fetch_array($result);
$start_com = $start - $station_num[$rowcount[0]];
$end_com = $end - $station_num[$rowcount[0]];

$result1 = mysql_query("select count(id) from matrix_$traincode");
$stat_count = mysql_fetch_array($result1);

$num = $count; //使用者所需票數(人數)

if(@$_GET['item'] != null){ //有無直達
    $it = $_GET['item'];
    $item = explode(",",$it);
    $num = $num - $item[1];
}


$i = 0;
$j = 0;
while(@$_GET["count$i"] != null){
    $select = $_GET["count$i"];
    //echo $select."<p></p>";
    $array = explode(",",$select);
    if($array[$stat_count[0]] != 0){
        $ary[$j] = explode(",",$select);
        $j++;
    }
    $i++;
}

$total = 0;
$totalticket = 0;

for($i = 0; $i < $j; $i++){ //每條路徑票數加總
    $total = $ary[$i][$stat_count[0]] + $total;
}
//echo $total;

if($total > $num){
    echo "超過所選的票數<p></p>";
    echo "<button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>";
}
else{
    echo '<form method = get action = checkC.php>';
    echo "所選的路徑有 : <p></p>";
    if(@$_GET['item']){   //有無直達
        echo $station[$start]." -> ".$station[$end]." : ".$item[1]."張票<p></p>";
        $totalticket = $item[1];
        echo "<input type=hidden name=item value=$it>";
    }
    //$set2 = 0;
    for($i = 0; $i < $j; $i++){
        $m = "";
        $set2 = 0;
        for($k = 0; $k < $stat_count[0]; $k++){
            if($ary[$i][$k] != $start_com){
                $set2++;
            }
        }
        printpath($start_com,$end_com,$ary[$i]);
        echo " : ".$ary[$i][$stat_count[0]]*($set2+1)."張票<p></p>";
        $totalticket = $ary[$i][$stat_count[0]]*($set2+1) + $totalticket;
        for($k = 0; $k < $stat_count[0]; $k++){
            $m = $m.$ary[$i][$k].",";
        }
        $m = $m.$ary[$i][$stat_count[0]];
        //echo $m;
        echo "<input type=hidden name=path$i value=$m>";
    }
    echo "共需". $totalticket ."張票<p></p>";
}
if($totalticket > 6){
    echo "所訂購的車票數超過6張<p></p>";
}
elseif($total <= $num && $totalticket <= 6){
    echo "<input type = hidden name = start value=$start>";
    echo "<input type = hidden name = end value=$end>";
    echo "<input type = hidden name = count value=$totalticket>";
    echo "<input type = hidden name=traincode value=$traincode>";
    require('popup_window.php');
}

function printpath($start_com,$end_com,$array){
    global $traincode;
    global $station_num;
    global $station;
    global $rowcount;
    global $station;
    $prior = $array;
    echo $station[$start_com + $station_num[$rowcount[0]]]." -> ";
    $i = $prior[$end_com];
    $j = 0;
    while($i != $start_com){
        $prior2[$j] = $i;
        $i = $prior[$i];
        $j++;
    }
    for($i = count($prior2) - 1; $i >= 0; $i--){
        if($prior2[$i] != -1){
            echo $station[$prior2[$i] + $station_num[$rowcount[0]]]." -> ";
        }
    }
    echo $station[$end_com + $station_num[$rowcount[0]]]; 
}

?>
</div>
</div>
</body>
</html>