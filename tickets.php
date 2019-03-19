<?php
/**/
$station=array("台北","桃園","新竹","苗栗","台中","彰化","嘉義","台南","高雄","屏東");
$station_num = array("台北"=>'0',"桃園"=>'1',"新竹"=>'2',"苗栗"=>'3',"台中"=>'4',"彰化"=>'5',"嘉義"=>'6',"台南"=>'7',"高雄"=>'8',"屏東"=>'9');
require('login_sql.php');
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
                              <table class="table table-striped">
                        <caption>查詢結果</caption>
                        <thead>
                            <tr>
                                <th>路徑</th>
                                <th>需要的票數</th>
                            </tr>
                        </thead>
<?php
if(@$_GET['stand'] == 0){
if(@$_POST['traincode'] != null){
    if(@$_POST['departure'] != @$_POST['destination']){ //起始站和終點站不同(第一層)
        $start_user = $_POST['departure']; //使用者所選的起始站
		$end_u = $_POST['destination']; //使用者所選的終點站
		$count = $_POST['count'];   //使用者所需票數
        $train_code = $_POST['traincode'];  //使用者所選的車次
        
        if(@$_POST['check_c'] == 1){
            $end_user = $end_u - 10*$start_user;
        }
        elseif($_POST['check_c'] == 0){
            $end_user = $end_u;
        }
/*-----------------------給電腦查詢的起始站和終點站-------------------------*/
        if($start_user < $end_user){
            $result2 = mysql_query("select origin from tickets_$train_code");
            $row2 = mysql_fetch_array($result2);
            $realstart = $station_num[$row2[0]];
        
            $start = $start_user-$realstart;
            $end = $end_user-$realstart;
        }
        elseif($start_user > $end_user){
            $result2 = mysql_query("select dest from tickets_$train_code");
            $row2 = mysql_fetch_array($result2);
            $realstart = $station_num[$row2[0]];
        
            $start = $start_user-$realstart;
            $end = $end_user-$realstart;
        }
        
/*-------------------------看有幾個站---------------------------*/
        $sql1 = "select count(id) from matrix_$train_code";
        $result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
        $v = $row1[0];  //v為有幾個站
        
/*------------------將matrix的值存入cost中----------------*/      
        $sql = "select * from matrix_$train_code";
        $result = mysql_query($sql);
		for($i = 0; $i < $v; $i++){                //將票數存入cost中
			$row=mysql_fetch_array($result);
			for($j = 0; $j < $v; $j++){
				$cost[$i][$j] = $row[$j];
			}
		}
/*------------------初始個值-----------------*/
        $check_left = 0;
        $check_over = 0;  //票是有超過6張,為1,沒超過6張,為0
        $num = 0;
		$check_arri = 0;  //看是否直達,直達check_arri=0
        $check_have2 = 0;  //看是否有路徑,有路徑check_have2=1
        
        
/*--------------------有直達的票----------------------*/
        if($cost[$start][$end] != 0){
            echo '<form method = get action = rule.php>';
            /*-----直達的票數大於使用者需要的票數-----*/
            if($cost[$start][$end] >= $count){  //如果剩餘票數大於使用者所需票數
                $m = "-1,$count";
            ?>
                <tbody>
                    <tr>
                        <td><?php echo $station[$start_user]." -> ".$station[$end_user];?></td>
                <?php 
                $check_arri = 0;
                $check_have2 = 1;
                ?>
                        <td><?php echo " : 共需要".$count."張票";?></td>
                    </tr>
                </tbody>
                <?php
                echo "<input type=hidden name=item value=$m>";
                echo "<p></p>";
                if($count > 6){  //如果超過6張,check_over=1
                    $check_over = 1;
                }
                else{
                    $check_over = 0;
                }
                
                
            }
            
            /*------直達的票數小於使用者所需要的票數,需轉站--------*/
            elseif($cost[$start][$end] < $count){
                echo '<form method = get action = rule.php>';
                $check_arri = 1;
                /*-------直達部分------*/
                global $prior;
                dijstra($start,$end,$cost[$start][$end]);
                ?>
                <b><?php printpath($start,$end);?></b>
                <b><?php echo " : ".$cost[$start][$end]."人可直達";?></b>
                <?php
                echo "<p></p>";
                
                $num = $count - $cost[$start][$end];  //$num 為所需票數-剩餘票數
                $set = $cost[$start][$end];  //將剩餘票數存起來
                $cost[$start][$end] = 0;  //將剩餘票數設為0
                $sum = $count - $set;
                $m = "-1,$set";
                echo "<input type=hidden name=item value=$m>";
                /*------轉站部分------*/
                ?>
                <b><?php echo $sum."人需轉站 : ";?></b>
                <?php
                echo "<p></p>";
                $u = 0;
                $for_end = 0;
                $for_start = 0;
                /*-------南下部分-------*/
                if($start < $end){
                    $for_start = $start;
                    $for_end = $end;
                }
                /*------北上部分-------*/
                elseif($start > $end){
                    $for_start = $end;
                    $for_end = $start;
                }
                /*---------------開始尋找路徑--------------*/
                for($w = $for_start; $w < $for_end; $w++){
                    $set2 = 0;
                    for($i = 0; $i < count($prior); $i++){
                        $prior_copy[$u][$i] = $start;
                    }
                    if($cost[$w][$for_end] != 0){
                        dijstra($for_start,$w,$num);
                        $prior[$for_end] = $w;
                        $min = find_min($for_start,$for_end);
                        if($min > 6){
                            $min = 6;
                        }
                        $min = min($min,$num);
                        $prior_copy[$u] = $prior;
                        $total = $set+($num * ($set2+1));
                        $check_have2++;
                            ?>
                            <tbody>
                                <tr>
                                <td><?php printpath($for_start,$for_end);?></td>
                                <td><?//php echo " : 轉站需要".$num * ($set2+1)."張票";?></td>
                            <?php echo "<p></p>";?>
                                <td><?//php echo "共需".$total."張票";?></td>
                        
                                  
                        <?php
                            $m = "";
                            for($i = 0; $i < count($prior); $i++){
                                $m = $m.$prior_copy[$u][$i].",";
                            }
                        ?>
                                <td><?php //echo "<form id=form$u name=form$u Action=test87.php method=get>";
                                                echo "<select id=count name=count$u>";
                                                for($n = 0; $n <= $min; $n++){
                                                    echo "<option value=$m$n>$n</option>";
                                                }
                                                echo "</select>";?></td>
                                </tr>
                            </tbody>
                        <?php
                    
                    $u++;
                    $cost[$start][$end] = $set;
                    $check_arri = 1;
                    echo "<p></p>";
                    }
                    
                }
                /*---------可訂值達的票--------*/
                if($check_have2 == 0){
                    echo "無法轉站或轉站所需的票數超過6張";
                    $check_arri = 0;
                    $check_have = 1;
                    $check_have2 = 1;
                    $count = $cost[$start][$end];
                }
            }
            
        }
        /*------------剩餘票數為0-----------*/    
        elseif($cost[$start][$end] == 0){
            echo '<form method = get action = rule.php>';
            global $prior;
            $u = 0;
            $min = 0;
            $check_arri = 1;
            $check_have = 0;
            $check_have2 = 0;
            /*-------南下部分-------*/
            if($start < $end){
                $for_start = $start;
                $for_end = $end;
            }
            /*------北上部分-------*/
            elseif($start > $end){
                $for_start = $end;
                $for_end = $start;
            }
            /*--------------開始尋找路徑-------------*/
            for($w = $for_start; $w < $for_end; $w++){
                $set2 = 0;
                $total = 0;
                
                if($cost[$w][$for_end] != 0){
                    for($k = 0; $k < $v; $k++){
                        $prior_copy[$u][$k] = $start;
                    }
                    dijstra($for_start,$w,$count);
                    $prior[$for_end] = $w;
                    //printpath($for_start,$for_end);
                    $min = find_min($for_start,$for_end);
                    if($min > 6){
                        $min = 6;
                    }
                    $min = min($min,$count);
                    $prior_copy[$u] = $prior;
                    $check_have2++;
                    ?>
                    <tbody>
                        <tr>
                        <td><?php printpath($for_start,$for_end);?></td>
                        
                        <td><?//php echo " : 共需".$total."張票";?></td>
                        <?php
                        $m = "";
                        for($i = 0; $i < count($prior); $i++){
                            $m = $m.$prior_copy[$u][$i].",";
                        }
                        ?>
                        <td><?php
                                echo "<select id=count name=count$u>";
                                for($n = 0; $n <= $min; $n++){
                                    echo "<option value=$m$n>$n</option>";
                                }
                                echo "</select>";?></td>
                        </tr>
                    
                    <?php
                $u++;
                echo "<p></p>";
            }
            }
            if($check_have2 == 0){
                echo "車票已售完";
            }
            
        }?>
        </tbody> 
        </table>
        <?php    
        if($check_have2 != 0){
            echo "<input type = hidden name = start_pass value=$start_user>";
            echo "<input type = hidden name = end_pass value=$end_user>";
            echo "<input type = hidden name = count_pass value=$count>";
            echo "<input type = hidden name=traincode value=$train_code>";
            echo "<input type=submit value=確認>     ";
            echo '</form>';
        }
	}
	else{   //起始站和終點站不同(第一層)
           echo '<h3 style=color:red>請選擇不同的起點站和終點站</h3>';
    }
}
else{ 
    echo "請選擇一個車次";
}
$check_c = $_POST['check_c'];
echo "<a href=?&stand=1&departure=$start_user&destination=$end_u&count=$count&traincode=$train_code&check_c=$check_c>站票機制</a>";
echo "<p></p>";
echo "
    <button style=width:90px;height:40px;font-size:10px;><a href = http://localhost/tickets.html>回首頁</a></button>
    <button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>回上一頁</button>
";
}

else{
    echo '<form method = get action = checkstand.php>';
    $start_user = $_GET['departure']; //使用者所選的起始站
    $end_u = $_GET['destination']; //使用者所選的終點站
    $count = $_GET['count'];   //使用者所需票數
    $train_code = $_GET['traincode'];  //使用者所選的車次
    if(@$_GET['check_c'] == 1){
        $end_user = $end_u - 10*$start_user;
    }
    elseif($_GET['check_c'] == 0){
        $end_user = $end_u;
    }
/*-----------------------給電腦查詢的起始站和終點站-------------------------*/
    if($start_user < $end_user){
        $result2 = mysql_query("select origin from tickets_$train_code");
        $row2 = mysql_fetch_array($result2);
        $realstart = $station_num[$row2[0]];
        
        $start = $start_user-$realstart;
        $end = $end_user-$realstart;
    }
    elseif($start_user > $end_user){
        $result2 = mysql_query("select dest from tickets_$train_code");
        $row2 = mysql_fetch_array($result2);
        $realstart = $station_num[$row2[0]];
        
        $start = $start_user-$realstart;
        $end = $end_user-$realstart;
    }
    /*-------------------------看有幾個站---------------------------*/
    $sql1 = "select count(id) from matrix_$train_code";
    $result1 = mysql_query($sql1);
    $row1 = mysql_fetch_array($result1);
    $v = $row1[0];  //v為有幾個站
    /*------------------將matrix的值存入cost中----------------*/      
    $sql = "select * from matrix_$train_code";
    $result = mysql_query($sql);
    for($i = 0; $i < $v; $i++){                //將票數存入cost中
        $row=mysql_fetch_array($result);
        for($j = 0; $j < $v; $j++){
            $cost[$i][$j] = $row[$j];
        }
    }
    
    require('stand.php');
    echo "<p></p>";
    echo "
        <button style=width:90px;height:40px;font-size:10px;><a href = http://localhost/tickets.html>回首頁</a></button>
        <button style=width:90px;height:40px;font-size:10px; onClick=javascript:history.back(1)>返回轉站頁面</button>
        ";
}





	function dijstra($start,$end,$num){
		global $v;
		global $cost;
		global $dist;
		global $decided;
		global $prior;
		for($i = 0; $i < $v; $i++){
			if($cost[$start][$i] > 0){
				$dist[$i] = 1;
				$decided[$i] = 1;
			}
			else{
				$dist[$i] = 0;
				$decided[$i] = 0;
			}
			$prior[$i] = $start;
		}
		if($cost[$start][$end] > $num){
			$prior[$end] = $start;
            //printpath($start,$end);
		}
		elseif($start < $end){
			for($i = $start; $i <= $end; $i++){
				$vx = $i;
				$decided[$vx] = 1;
				for($w = $i; $w <= $end; $w++){
					if($decided[$w] == 0 && $dist[$vx] != 0 && $cost[$vx][$w] > 0){
						if($dist[$w] > $dist[$vx]+1 && $dist[$w] != 0){
							$dist[$w] = $dist[$vx]+1;
							$prior[$w] = $vx;
						}
						elseif($dist[$w] == 0){
							$dist[$w] = $dist[$vx]+1;
							$prior[$w] = $vx;
						}
					}
				}
			}
			//printpath($start,$end);
		}
		elseif($start > $end){
			for($i = $start; $i >= $end; $i--){
				$vx = $i;
				$decided[$vx] = 1;
				for($w = $i; $w >= $end; $w--){
					if($decided[$w] == 0 && $dist[$vx] != 0 && $cost[$vx][$w] > 0){
						if($dist[$w] > $dist[$vx] + 1 && $dist[$w] != 0){
							$dist[$w] = $dist[$vx] + 1;
							$prior[$w] = $vx;
						}
						elseif($dist[$w] == 0){
							$deist[$w] = $dist[$vx] + 1;
							$prior[$w] = $vx;
						}
					}
				}
			}
			//printpath($start,$end);
		}
	}
	
	function printpath($start,$end){
		global $v;
		global $prior;
		global $station;
		global $dist;
        global $realstart;
        global $check_left;
		
        for($i = 0; $i < $v; $i++){
            $prior2[$i] = -1;
        }
		if($dist[$end] == 0){
			//echo "沒有票";
            $check_left = 1;
            //echo "<p></p>";
		}
		elseif($dist[$end] == 1){
			//echo "有票";
            $check_left = 0;
            //echo "<p></p>";
		}
		elseif($dist[$end] > 1){
			//echo "需要轉".$dist[$end]."站";
            //echo "<p></p>";
            $check_left = 0;
		}
		echo $station[$start+$realstart]." -> ";
		$i = $prior[$end];
		$j = 0;
		while($i != $start){
			$prior2[$j] = $i;
			$i = $prior[$i];
			$j++;
		}
		for($i = count($prior2) - 1; $i >= 0; $i--){
			if($prior2[$i] != -1){
				echo $station[$prior2[$i]+$realstart]." -> ";
			}
		}
		echo $station[$end+$realstart];
	}
function find_min($start,$end){
    global $prior;
    global $cost;
    $i = $end;
    $min = 100;
    while($i != $start){
        $min = min($cost[$prior[$i]][$i],$min);
        $i = $prior[$i];
    }
    return $min;
}
?>

</div>
</div>
</body>
</html>