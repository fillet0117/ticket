<?php
/*ok的*/
$station=array("台北","桃園","新竹","苗栗","台中","彰化","嘉義","台南","高雄","屏東");
$station_num = array("台北"=>'0',"桃園"=>'1',"新竹"=>'2',"苗栗"=>'3',"台中"=>'4',"彰化"=>'5',"嘉義"=>'6',"台南"=>'7',"高雄"=>'8',"屏東"=>'9');
$name = $_POST['uname'];
$start = $_POST['departure'];
$end = $_POST['destination'];
?>
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
                            padding-top: 190px;
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
if($start != $end){
    require('login_sql.php');
    $query = "select count(*) from check_ticket where userID='$name'";
    $result = mysql_query($query) or die('MySQL query error');
    $count = mysql_fetch_array($result);
    if($count == 0){    //沒有這個身分證字號
        echo "查無此資料,確認身分證字號或起始站終點站是否正確";
    }
    else{     //有此身分證字號
        $check_m = 0;
        $query1 = "select bookingcode from check_ticket where userID='$name'";
        $result1 = mysql_query($query1) or die('MySQL query1 error');
        $bookingcode = mysql_fetch_array($result1);
        while($bookingcode[0] != null){
            $j = 0;
            $check_ary = 0;
            if($start < $end){
                $real_start = 10;
                $real_end = 0;
            }
            elseif($start > $end){
                $real_start = 0;
                $real_end = 10;
            }
            $query3 = "select origin,dest from book where bookingcode = '$bookingcode[0]'";
            $result3 = mysql_query($query3) or die('MySQL query3 error');
            $origin_dest = mysql_fetch_array($result3);
            while($origin_dest != null){
                if($start < $end){
                    $real_start = min($station_num[$origin_dest[0]],$real_start);
                    $real_end = max($station_num[$origin_dest[1]],$real_end);
                }
                elseif($start > $end){
                    $real_start = max($station_num[$origin_dest[0]],$real_start);
                    $real_end = min($station_num[$origin_dest[1]],$real_end);
                }
                $origin_dest = mysql_fetch_array($result3);
            }
            echo "<p></p>";
            $check_right = 0;
            if($start < $end){
                if($start == $real_start and $end == $real_end){
                    echo "<p></p>";
                    echo "電腦代碼 : ".$bookingcode[0];
                    $check_right = 1;
                    $check_m++;
                }
                else{
                    $check_right = 0;
                }
            }
            elseif($start > $end){
                if($start == $real_start and $end == $real_end){
                    echo "<p></p>";
                    echo "電腦代碼 : ".$bookingcode[0];
                    $check_right = 1;
                    $check_m++;
                }
                else{
                    $check_right = 0;
                } 
            }
            $bookingcode = mysql_fetch_array($result1);
        }
        if(@$check_right == 0 && $check_m == 0){
            //echo $check_m;
            echo "查無此資料,確認身分證字號或起始站終點站是否正確";
        }
    }
}
else{
    echo "請選擇不一樣的起始站和終點站";
}
unset($max,$min);
echo "<p></p>";
echo "
        <html>
        <body>
        <button style=width:90px;height:40px;font-size:10px;><a href = http://localhost/tickets.html>回首頁</a></button>
        </body>
        </html>
    ";
    
    
function check_userid($name){
    $checkift = 0;
    $i = 0;
    $sum = 0;
    $mode = 0;
   
    $userid_char = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','x','y');
    $userid_int = array(10,11,12,13,14,15,16,17,34,18,19,20,21,22,35,23,24,25,26,27,28,29,30,31,34);
    
    $array = str_split($name,1);
    if(count($array) != 10){    /*檢查大小是否為10*/
        //echo "<p>請輸入身分證字號</p>";
        $checkift++;
    }
    elseif(count($array) == 10){
        for($i = 0; $i < 10; $i++){  /*檢查第一個字元是否為數字*/
            if(ord($array[0]) == $i){
                //echo "<p>資料填寫不正確2</p>".$i;
                $checkift++;
                return 0;
                break;
            }
        }
        if($array[0] == "w" || $array[0] == "W" || $array[0] == "Z" || $array[0] == "z" ){  /*檢查第一個字元是否為w或x*/
            //echo "<p>資料填寫不正確3</p>";
            $checkift++;
            return 0;
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
            //echo "<p>資料填寫不正確5</p>";
            $checkift++;
            return 0;
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
                //echo "<p>資料填寫不正確6</p>";
                $checkift++;
                return 0;
            }
            else{
                //echo "資料輸入正確";
                $checkift = 0;
                return 1;
            }
        }
        else{
            //echo "<p>資料填寫不正確7</p>";
            $checkift++;
            return 0;
        }
    }
}




?>
                            </div></div></body></html>