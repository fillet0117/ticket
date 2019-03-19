<?php
if(@$_GET['item'] != null || @$_GET['path0'] != null){
   $station=array("台北","桃園","新竹","苗栗","台中","彰化","嘉義","台南","高雄","屏東");
    $station_num = array("台北"=>'0',"桃園"=>'1',"新竹"=>'2',"苗栗"=>'3',"台中"=>'4',"彰化"=>'5',"嘉義"=>'6',"台南"=>'7',"高雄"=>'8',"屏東"=>'9');
    $i = 0;
    $sum = 0;
    $start = $_GET['start'];
    $end = $_GET['end'];
    $count = $_GET['count']; //總票數,存在check_ticket的tic_count中
    $name = $_GET['uname'];
    $traincode = $_GET['traincode'];
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
    $i = 0;
    while(@$_GET["path$i"] != null){
        $pa = $_GET["path$i"];
        //echo $pa;
        $path[$i] = explode(",",$pa);
        $i++;
    }
    if(@$_GET['item'] != null){
        $it = $_GET['item'];
        //echo $it;
        $item = explode(",",$it);
    }
    
    
    check_userid($name);
    global $checkift;
    if($checkift == 0){
        require('login_sql.php');
        /*$snc = mysql_query("select count(userID) from check_ticket");
        $snc_re = mysql_fetch_array()*/
        $sql40 = "select sum(tic_count) from check_ticket where userID='$name'";  //看這個使用者總共所訂的票數是否超過6張
        $result40 = mysql_query($sql40) or die('MySQL sql40 error');
        $tic_sql = mysql_fetch_array($result40);
    
        $sql = "select count(id) from matrix_$traincode";
        $result20 = mysql_query($sql) or die('MySQL sql error');
        $row20 = mysql_fetch_array($result20);
        $v = $row20[0];     //$v 為有幾個站
        
        if($start < $end){
            $result30 = mysql_query("select origin from tickets_$traincode");
            $row30 = mysql_fetch_array($result30);
            $realstart = $station_num[$row30[0]];
        }
        elseif($start > $end){
            $result30 = mysql_query("select dest from tickets_$traincode");
            $row30 = mysql_fetch_array($result30);
            $realstart = $station_num[$row30[0]];
        }
        
        $query = "select tic_left from tickets_$traincode where origin = '$station[$start]' AND dest = '$station[$end]'";  //原本的票數
        $result = mysql_query($query) or die('MySQL query0 error');
        $ticket_left=mysql_fetch_array($result);   //ticket_left[0]原本tickets的票數
        
        $total = $tic_sql[0] + $count;
        if($total <= 6){
            /*------------直達且沒轉站------------*/
            if(@$_GET['item'] != null && @$_GET['path0'] == null){  
                $max = 0;
                $query = "insert into check_ticket(userID, tic_count, origin, dest) values('$name','$count','$station[$start]','$station[$end]')";
                $result = mysql_query($query) or die('MySQL query1 error');
        
                $query1 = "select bookingcode from check_ticket where userID = '$name' AND tic_count = '$count'";
                $result1 = mysql_query($query1) or die('MySQL query1 error');
                $bookingcode = mysql_fetch_array($result1);
                while($bookingcode != null){       //選擇最大的數
                    if($bookingcode[0] > $max){
                        $max = $bookingcode[0];
                    }
                    $bookingcode = mysql_fetch_array($result1);
                }
        
                $query2 = "insert into book(origin, dest, traincode,bookingcode, tic_count) values('$station[$start]','$station[$end]','$traincode','$max','$count')";
                $result2 = mysql_query($query2) or die('MySQL query2 error');
        
                $sum = $ticket_left[0] - $count;
                $query3 = "update tickets_$traincode set tic_left = $sum where origin = '$station[$start]' AND dest ='$station[$end]'";
                $result3 = mysql_query($query3) or die('MySQL query3 error');
                require('matrix.php');
                echo "<p></p>";
                echo "電腦代碼 : ".$max;
                echo "<p></p>";
                echo "訂票成功";
                echo "<p style=color:red>如需至便利商店取票或事後可能須取消訂票,請謹記電腦代碼,以方便取票<p>";
                echo "
                        
                            <button style=width:90px;height:40px;font-size:10px;><a href = http://localhost/tickets.html>回首頁</a></button>
                            <button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>
                        
                    ";
            }
            /*--------------沒有直達,且有轉站--------------*/
            elseif(@$_GET['item'] == null && $_GET['path0'] != null){
                $max = 0;
                $set = "insert into check_ticket(userID, tic_count, origin, dest)value('$name','$count','$station[$start]','$station[$end]')";
                $result = mysql_query($set) or die('MySQL set error');
            
                $set1 = "select bookingcode from check_ticket where userID = '$name' AND tic_count = '$count'";
                $result1 = mysql_query($set1) or die('MySQL set1 error');
                $bookingcode=mysql_fetch_array($result1);
                while($bookingcode != null){
                    if($bookingcode[0] > $max){
                        $max = $bookingcode[0];
                    }   
                    $bookingcode = mysql_fetch_array($result1);
                }
                
                for($h = 0; $h < $i; $h++){
                    $k = $end-$realstart;
                    $j = 0;
                    while($k != $start-$realstart){      //分段的原本票數
                        $use2 = $station[$path[$h][$k]];
                        $set3 = "select tic_left from tickets_$traincode where origin = '$use2' AND dest = '$station[$k]'";
                        $result3 = mysql_query($set3) or die('MySQL set3 error');
                        $row2=mysql_fetch_array($result3);
                        $array_tic[$j] = $row2[0];
                        $k = $path[$h][$k];
                        $j++;
                    }
                    
                    $k = $end-$realstart;
                    $j = 0;
                    while($k != $start-$realstart){
                        $use = $station[$path[$h][$k]];
                        $re = $path[$h][$v];
                        $set2 = "insert into book(origin, dest, traincode,bookingcode,tic_count)value('$use','$station[$k]','$traincode','$max','$re')";
                        $result2 = mysql_query($set2) or die('MySQL set2 error');
                        $k = $path[$h][$k];
                    }
            
                    $k = $end-$realstart;
                    $j = 0;
                    while($k != $start-$realstart){
                        $use = $station[$path[$h][$k]];
                        $set4 = "update tickets_$traincode set tic_left = $re where origin = '$use' AND dest ='$station[$k]'";
                        $result4 = mysql_query($set4) or die('MySQL set4 error');
                        $k = $path[$h][$k];
                    }
                }
                require('matrix.php');
                echo "<p></p>";
                echo "電腦代碼 : ".$max;
                echo "<p></p>";
                echo "訂票成功";
                echo "<p style=color:red>如需至便利商店取票或事後可能須取消訂票,請謹記電腦代碼,以方便取票<p>";  
                echo "
                            <button style=width:90px;height:40px;font-size:10px;><a href = http://localhost/tickets.html>回首頁</a></button>
                            <button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>
                        ";
            }
            /*--------------有直達,且有轉站-----------------*/
            elseif(@$_GET['item'] != null && $_GET['path0'] != null){
                $max = 0;
                $query4 = "insert into check_ticket(userID, tic_count, origin, dest) values('$name','$count','$station[$start]','$station[$end]')";
                $result4 = mysql_query($query4) or die('MySQL query4 error');
        
                $query5 = "select bookingcode from check_ticket where userID = '$name' AND tic_count = '$count'";
                $result5 = mysql_query($query5) or die('MySQL query5 error');
                $bookingcode = mysql_fetch_array($result5);
                while($bookingcode != null){
                    if($bookingcode[0] > $max){
                        $max = $bookingcode[0];
                    }
                    $bookingcode = mysql_fetch_array($result5);
                }
                
                $query6 = "insert into book(origin, dest, traincode,bookingcode, tic_count) values('$station[$start]','$station[$end]','$traincode','$max','$item[1]')";
                $result6 = mysql_query($query6) or die('MySQL query6 error');
        
                $query2 = "update tickets_$traincode set tic_left = 0 where origin = '$station[$start]' AND dest ='$station[$end]'";
                $result2 = mysql_query($query2) or die('MySQL query2 error');
                for($h = 0; $h < $i; $h++){

                    $k = $end-$realstart;
                    $j = 0;
                    while($k != $start-$realstart){
                        
                        $use2 = $station[$path[$h][$k]];
                        $set3 = "select tic_left from tickets_$traincode where origin = '$use2' AND dest = '$station[$k]'";
                        $result3 = mysql_query($set3) or die('MySQL set3 error');
                        $row2=mysql_fetch_array($result3);
                        $k = $path[$h][$k];
                        $j++;
                    }
            
                    $j = 0;
                    $k = $end-$realstart;
                    while($k != $start-$realstart){
                        //echo $k;
                        $use = $station[$path[$h][$k]];
                        $re = $path[$h][$v];
                        $set2 = "insert into book(origin, dest, traincode, bookingcode,tic_count)value('$use','$station[$k]','$traincode','$max','$re')";
                        $result2 = mysql_query($set2) or die('MySQL set2 error');
                        $k = $path[$h][$k];
                    }
            
                    $k = $end-$realstart;
                    $j = 0;
                    while($k != $start-$realstart){
                        $use = $station[$path[$h][$k]];
                        $set4 = "update tickets_$traincode set tic_left = $re where origin = '$use' AND dest ='$station[$k]'";
                        $result4 = mysql_query($set4) or die('MySQL set4 error');
                        $k = $path[$h][$k];
                        $j++;
                    }
                    
                }
                require('matrix.php');
                echo "<p></p>";
                echo "電腦代碼 : ".$max;
                echo "<p></p>";
                echo "訂票成功";
                echo "<p style=color:red>如需至便利商店取票或事後可能須取消訂票,請謹記電腦代碼,以方便取票<p>";
                echo "
                   
                        <button style=width:90px;height:40px;font-size:10px;><a href = http://localhost/tickets.html>回首頁</a></button>
                        <button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>
                   
                    ";
            }
        }
        elseif($total > 6){
            echo "<p></p>";
            echo "此身分證字號訂票數超過6張";
            echo "<button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>";
        }
    }
}
else{
    echo "請選擇一個車次";
}

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