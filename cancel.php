<?php
/*ok的*/
    $station_num = array("台北"=>'0',"桃園"=>'1',"新竹"=>'2',"苗栗"=>'3',"台中"=>'4',"彰化"=>'5',"嘉義"=>'6',"台南"=>'7',"高雄"=>'8',"屏東"=>'9');
    $station=array("台北","桃園","新竹","苗栗","台中","彰化","嘉義","台南","高雄","屏東");
    ?>
    <html>
    <head>
        <meta charset="utf-8">
        <!--以下是bootstrap簡單框架(Ch)
        by http://www.w3big.com/zh-TW/bootstrap/bootstrap-intro.html-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">  
        <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!--jquery物件-->
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="css/button.css">
        <link rel="stylesheet" type="text/css" href="css/icon.css">
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        <script src="semantic/dist/semantic.min.js"></script>
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
                    <li><a href="http://localhost/search.html">查詢代碼</a></li>
                </ul> 
            </div> 
            </div> 
        </nav>
        <div class="container">
        <div class="jumbotron">
    <?php
    if(@$_POST['uname'] != null && @$_POST['code'] != null){
        $name = $_POST['uname'];
        $code = $_POST['code'];
        if(check_userid($name)){   //身分證正確
            require('login_sql.php');
            $check_bookingcode = 0;
            $query = "select bookingcode from check_ticket where userID='$name'";
            $result = mysql_query($query) or die('MySQL query error');
            $bookingcode = mysql_fetch_array($result);
            while($bookingcode[0] != null){   //看有沒有對應到bookingcode
                if($code == $bookingcode[0]){
                    $check_bookingcode = 1;
                    break;
                }
                else{
                    $check_bookingcode = 0;
                }
                $bookingcode = mysql_fetch_array($result);
            }
            
            if($check_bookingcode){   //有對應到bookingcode
                $query2 = "select origin,dest,tic_count,traincode from book where bookingcode = '$code'";
                $result2 = mysql_query($query2) or die('MySQL query2 error');
                $origin_dest = mysql_fetch_array($result2);
                $traincode = $origin_dest[3];
                while($origin_dest != null){
                    $query3 = "update tickets_$traincode set tic_left = tic_left + $origin_dest[2] where origin = '$origin_dest[0]' and dest = '$origin_dest[1]'";
                    $result3 = mysql_query($query3) or die('MySQL query3 error');
                    $origin_dest = mysql_fetch_array($result2);
                }
                
                $result30 = mysql_query("select origin,dest from check_ticket where bookingcode = $code");
                $row30 = mysql_fetch_array($result30);
                $start = $station_num[$row30[0]];
                $end = $station_num[$row30[1]];
                $result20 = mysql_query("select count(id) from matrix_$traincode");
                $row20 = mysql_fetch_array($result20);
                $v = $row20[0];
                if($start < $end){
                    $result2 = mysql_query("select origin from tickets_$traincode");
                    $row2 = mysql_fetch_array($result2);
                    $realstart = $station_num[$row2[0]];
                }
                elseif($start > $end){
                    $result2 = mysql_query("select dest from tickets_$traincode");
                    $row2 = mysql_fetch_array($result2);
                    $realstart = $station_num[$row2[0]];
                }
                require('matrix.php');
                $query4 = "delete from book where bookingcode = '$code'";
                $result4 = mysql_query($query4) or die('MySQL query4 error');
                $query5 = "delete from check_ticket where userID = '$name' and bookingcode = '$code'";
                $result5 = mysql_query($query5) or die('MySQL query5 error');
                echo "取消訂票完成";
            }
            else{     //沒有對應到bookingcode
                echo "查無此資料,請確認身分證字號或電腦代碼是否輸入正確";
            }
        }
        else{      //身分證不正確
            echo "身分證輸入錯誤,請重新輸入";  
        } 
    }
    else{
        echo "請輸入身分證字號和電腦代碼";
    }
    echo "<p></p>";
    ?>
        
        
        <!--按鈕-->
        <div class="ui inverted segment">
            <button class="ui inverted olive button">
                <a href = http://localhost/tickets.html>回首頁</a>
                <input type="hidden" value="回首頁">
            </button>
            <p></p>
            <button class="ui inverted olive button" style="onClick=javascript:history.back(1)">
                <a href = http://localhost/cancel.html>回上一頁</a>
            </button>
        </div>
    
    
<?php
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
        return 0;
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
</div>
</div>
</body>
</html>