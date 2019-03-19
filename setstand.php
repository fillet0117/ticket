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
$startu = $_GET['start'];
$endu = $_GET['end'];
$count = $_GET['count'];
$traincode = $_GET['traincode'];
$uname = $_GET['uname'];
$prior = [];
$i = 0;
while(@$_GET["station$i"] != null){
    $prior[$i] = explode(",",$_GET["station$i"]);
    $i++;
}

$total = 0;
$result1 = mysql_query("select count(id) from matrix_$traincode");
$vsql = mysql_fetch_array($result1);
$v = $vsql[0];

foreach($prior as $a){
    $cou = 0;
    for($i = 0; $i < $v; $i++){
        if($a[$i] != $a[$v]){
            $cou++;
        }
    }
    $total = $total + $count * ($cou + 1);
}

$result1 = mysql_query("select origin from tickets_$traincode where tick_no = 1");
$stat_count = mysql_fetch_array($result1);
$real = $station_num[$stat_count[0]];
$sql40 = "select sum(tic_count) from check_ticket where userID='$uname'";  //看這個使用者總共所訂的票數是否超過6張
$result40 = mysql_query($sql40) or die('MySQL sql40 error');
$tic_count = mysql_fetch_array($result40);

check_userid($uname);
global $checkift;

if($checkift == 0){
    if(($tic_count[0] + $total) > 6){
        echo "此身分證所訂購的車票數超過6張<p></p>";
    }
    else{
        $max = 0;
        $start = $startu + $real;
        $end = $endu +$real;
        $query4 = "insert into check_ticket(userID, tic_count, origin, dest) values('$uname','$total','$station[$start]','$station[$end]')";
        $result4 = mysql_query($query4) or die('MySQL query4 error');

        $query5 = "select bookingcode from check_ticket where userID = '$uname' AND tic_count = '$total'";
        $result5 = mysql_query($query5) or die('MySQL query5 error');
        $bookingcode = mysql_fetch_array($result5);
        while($bookingcode != null){
            if($bookingcode[0] > $max){
                $max = $bookingcode[0];
            }
            $bookingcode = mysql_fetch_array($result5);
        }
        foreach($prior as $a){
            $cou = 0;
            for($i = 0; $i < $v; $i++){
                if($a[$i] != $a[$v]){
                    $cou++;
                }
            }
            if($cou == 0){
                $start = $station[$a[$v]+$real];
                $end = $station[$a[$v+1]+$real];
                $query = "insert into book(origin, dest, traincode,bookingcode, tic_count) values('$start','$end',$traincode,$max,$count)";
                $result = mysql_query($query) or die('mysql query error');
                $query2 = "update tickets_$traincode set tic_left = tic_left-1 where origin = '$start' AND dest ='$end'";
                $result2 = mysql_query($query2) or die('MySQL query2 error');
            }
            else{
                $start = $a[$v];
                $end = $a[$v+1];
                $i = $end;
                while($i != $start){
                    $startc = $station[$a[$i]+$real];
                    $endc = $station[$i+$real];
                    $query = "insert into book(origin, dest, traincode,bookingcode, tic_count) values('$startc','$endc',$traincode,$max,$count)";
                    $result = mysql_query($query) or die('mysql query error');
                    $query2 = "update tickets_$traincode set tic_left = tic_left-1 where origin = '$startc' AND dest ='$endc'";
                    $result2 = mysql_query($query2) or die('MySQL query2 error');
                    $i = $a[$i];
                }
            }
        }
        $realstart = $real;
        require('matrix.php');
        echo "<p></p>";
        echo "電腦代碼 : ".$max;
        echo "<p></p>";
        echo "訂票成功";
        echo "<p style=color:red>如需至便利商店取票或事後可能須取消訂票,請謹記電腦代碼,以方便取票<p>";
        echo "
            <html>
            <body>
                <button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>
            </body>
            </html>
            ";
    }
}
echo "<button style=width:90px;height:40px;font-size:10px;><a href = http://localhost/tickets.html>回首頁</a></button>";
                                  



function check_userid($name){
    global $checkift;
    $i = 0;
    $sum = 0;
    $mode = 0;
   
    $userid_char = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','x','y');
    $userid_int = array(10,11,12,13,14,15,16,17,34,18,19,20,21,22,35,23,24,25,26,27,28,29,30,31,34);
    
    $array = str_split($name,1);
    if(count($array) != 10){    /*檢查大小是否為10*/
        echo "<p>請輸入身分證字號</p>";
        $checkift++;
    }
    elseif(count($array) == 10){
        for($i = 0; $i < 10; $i++){  /*檢查第一個字元是否為數字*/
            if(ord($array[0]) == $i){
                echo "<p>資料填寫不正確</p>".$i;
                $checkift++;
                break;
            }
        }
        if($array[0] == "w" || $array[0] == "W" || $array[0] == "Z" || $array[0] == "z" ){  /*檢查第一個字元是否為w或x*/
            echo "<p>資料填寫不正確</p>";
            $checkift++;
        }
        elseif($array[0] != "w" && $array[0] != "W" && $array[0] != "Z" && $array[0] != "z" ){
            for($i = 0; $i < 24; $i++){     /*將第一個字元轉換為數字*/
                if($userid_char[$i] == $array[0] || strtoupper($userid_char[$i]) == $array[0]){
                    $array[0] = $userid_int[$i];
                    break;
                }
            }
        }
        elseif($array[1] != 2 || $array[1] != 1){   /*檢查第二個字元是否為1或2*/
            echo "<p>資料填寫不正確</p>";
            $checkift++;
        }
        $j = 8;
        for($i = 0; $i < 9; $i++){
            if($i == 0){
                $sum = $sum + (int)((int)$array[$i]/10);
                $sum = $sum + (int)($array[$i]%10)*9;
            }
            else{
                $sum = $sum + (int)$array[$i]*$j;
                $j--;
            }
            
        }
        //echo $sum;
        $mode = $sum%10;
        $mode = 10 - $mode;
        if($mode == (int)$array[9]){
            if($checkift != 0){
                echo "<p>資料填寫不正確</p>";
            }
            else{
                echo "資料輸入正確";
                $checkift = 0;
            }
        }
        else{
            echo "<p>資料填寫不正確</p>";
            $checkift++;
        }
    }
}

?>
                            </div>
                        </div>
                        </body>
</html>