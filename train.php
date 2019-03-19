<?php
/*ok的*/
$station=array("台北","桃園","新竹","苗栗","台中","彰化","嘉義","台南","高雄","屏東");
$station_num = array("台北"=>'0',"桃園"=>'1',"新竹"=>'2',"苗栗"=>'3',"台中"=>'4',"彰化"=>'5',"嘉義"=>'6',"台南"=>'7',"高雄"=>'8',"屏東"=>'9');
require('login_sql.php');
if(@$_POST['departure'] != @$_GET['destination']){
    $time = @$_GET['time'];
    $start = $_GET['departure'];
    $end = $_GET['destination'];
    $count = $_GET['count'];
    $check_train = 0;
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
                            <!--表格by http://www.w3big.com/zh-TW/bootstrap/bootstrap-tables.html-->
                        <table class="table table-striped">
                        <caption>查詢結果</caption>
                        <thead>
                            <tr>
                                <th>車次</th>
                                <th>發車站->終點站</th>
                                <th>開車時間</th>
                                <th>抵達時間</th>
                            </tr>
                        </thead>
    <?php
    echo "<p></p>";
    $totalpage=ceil(24/3);  //計算頁數
    if(isset($_GET['page']) && $_GET['page']<=$totalpage){//這裏做了一個判斷，若get到數據並且該數據小於總頁數情況下才付給當前頁參數，否則跳轉到第一頁 
        $thispage=$_GET['page'];
        switch($thispage){
            case 1:
                echo " 00:00 到 02:59 的車次 : <p></p>";
                break;
            case 2:
                echo " 03:00 到 05:59 的車次 : <p></p>";
                break;
            case 3:
                echo " 06:00 到 08:59 的車次 : <p></p>";
                break;
            case 4:
                echo " 09:00 到 11:59 的車次 : <p></p>";
                break;
            case 5:
                echo " 12:00 到 14:59 的車次 : <p></p>";
                break;
            case 6:
                echo " 15:00 到 17:59 的車次 : <p></p>";
                break;
            case 7:
                echo " 18:00 到 20:59 的車次 : <p></p>";
                break;
            case 8:
                echo " 21:00 到 23:59 的車次 : <p></p>";
                break;
        }
    }else{ 
        if($time >= "00:00" && $time <"03:00"){
            echo " 00:00 到 02:59 的車次 : <p></p>";
            $thispage = 1;
        }
        elseif($time >= "03:00" && $time <"06:00"){
            echo " 03:00 到 05:59 的車次 : <p></p>";
            $thispage = 2;
        }
        elseif($time >= "06:00" && $time <"09:00"){
            echo " 06:00 到 08:59 的車次 : <p></p>";
            $thispage = 3;
        }
        elseif($time >= "09:00" && $time <"12:00"){
            echo " 09:00 到 11:59 的車次 : <p></p>";
            $thispage = 4;
        }
        elseif($time >= "12:00" && $time <"15:00"){
            echo " 12:00 到 14:59 的車次 : <p></p>";
            $thispage = 5;
        }
        elseif($time >= "15:00" && $time <"18:00"){
            echo " 15:00 到 17:59 的車次 : <p></p>";
            $thispage = 6;
        }
        elseif($time >= "18:00" && $time <"21:00"){
            echo " 18:00 到 20:59 的車次 : <p></p>";
            $thispage = 7;
        }
        elseif($time >= "21:00" && $time <="23:59"){
            echo " 21:00 到 23:59 的車次 : <p></p>";
            $thispage = 8;
        }
    }
    echo "<form method=post action=tickets.php>";
    if($start < $end){
        $sql1 = "select * from southbound_traincode";
    }
    elseif($start > $end){
        $sql1 = "select * from northbound_traincode";
    }
    $result = mysql_query($sql1);
    while(($row = mysql_fetch_array($result)) != null){
        if($start < $end && $station_num[$row[0]] <= $start && $station_num[$row[1]] >= $end){
            switch($thispage){
                case 1:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='00:00' and origin_time<='03:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 2:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='03:01' and origin_time<='06:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 3:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='06:01' and origin_time<='09:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 4:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='09:01' and origin_time<='12:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 5:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='12:01' and origin_time<='15:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 6:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='15:01' and origin_time<='18:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 7:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='18:01' and  origin_time<='21:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 8:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='21:01' and origin_time<='23:59' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
            }
            $result2 = mysql_query($sql);
                
            while(($data = mysql_fetch_array($result2)) != null){
                ?>
                <tbody>
                    <tr>
                    <td><?php echo $row[4]?></td>
                    <td><?php echo $row[0]."-->".$row[1]?></td>
                    <td><?php echo $data[0]?></td>
                    <td><?php echo $data[1]?></td>
                <?php
                $check_train++;
                ?>
                    <td><?php echo "<input type=radio name=traincode value=$row[4]>"?></td>
                <?php 
                echo "<p></p>";
                ?>
                    </tr>
                    </tbody>
                <?php
            } 
        }
        elseif($start > $end && $station_num[$row[0]] >= $start && $station_num[$row[1]] <= $end){
            switch($thispage){
                case 1:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='00:00' and origin_time<='03:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 2:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='03:01' and origin_time<='06:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 3:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='06:01' and origin_time<='09:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 4:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='09:01' and origin_time<='12:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 5:
                    $sql="select origin_time,dest_time from tickets_$row[4]e where origin_time>='12:01' and origin_time<='15:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 6:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='15:01' and origin_time<='18:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 7:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='18:01' and  origin_time<='21:00' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
                case 8:
                    $sql="select origin_time,dest_time from tickets_$row[4] where origin_time>='21:01' and origin_time<='23:59' and origin='$station[$start]' and dest='$station[$end]' order by origin_time";
                    break;
            }
            $result2 = mysql_query($sql);
            while(($data = mysql_fetch_array($result2)) != null){
                ?>
                <tbody>
                    <tr>
                    <td><?php echo $row[4]?></td>
                    <td><?php echo $row[0]."-->".$row[1]?></td>
                    <td><?php echo $data[0]?></td>
                    <td><?php echo $data[1]?></td>
                <?php
                $check_train++;
                ?>
                    <td><?php echo "<input type=radio name=traincode value=$row[4]>"?></td>
                <?php 
                echo "<p></p>";
                ?>
                    </tr>
                    </tbody>
                <?php
            } 
        }
    }
    if($check_train == 0){
        echo "沒有符合的車次";
    }
    else{
        echo "<input type = hidden name=departure value=$start>";
        echo "<input type = hidden name=destination value=$end>";
        echo "<input type = hidden name=count value=$count>";
        echo "<input type = hidden name=stand value=0>";
        echo "<input type = hidden name=check_c value=0>";
        echo "<input type=submit value=確認>";
        echo "</form>";
    }
    echo "<p></p>";
    
    //for($i=1;$i<=$totalpage;$i++){
        if($thispage != 1){
            $i = $thispage-1;
            echo "<a href=?page=".$i."&departure=$start&destination=$end&count=$count>上一頁</a>";
        }
        echo "<a href=?page=".$thispage."&departure=$start&destination=$end&count=$count>  [".$thispage."]  </a>";
        if($thispage != $totalpage){
            $i = $thispage+1;
            echo "<a href=?page=".$i."&departure=$start&destination=$end&count=$count>下一頁</a>";
        }
        echo "</form>";
    //}
}
else   //起始站和終點站不同(第一層)
		echo '<h3 style=color:red>請選擇不同的起點站和終點站</h3>';
echo "
    <html>
        <body>
            <button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>
        </body>
    </html>
";

?>