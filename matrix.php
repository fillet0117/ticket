<?php
if($start < $end){
    $sql = "select tic_left from tickets_$traincode";
    $result = mysql_query($sql);
	for($i = 0; $i < $v; $i++){
		for($j = 0; $j < $v; $j++){
			if($i < $j){
				$row=mysql_fetch_array($result);
				$cost[$i][$j] = $row[0];
			}
			elseif($i >= $j){
				$cost[$i][$j] = 0;
			}
		}
	}
}
elseif($start > $end){
    $sql = "select tic_left from tickets_$traincode";
    $result = mysql_query($sql);
	for($i = 0; $i < $v; $i++){
		for($j = 0; $j < $v; $j++){
			if($i > $j){
				$row=mysql_fetch_array($result);
				$cost[$i][$j] = $row[0];
			}
			elseif($i <= $j){
				$cost[$i][$j] = 0;
			}
		}
	}
}
	
for($i = 0; $i < $v; $i++){
    for($j = 0; $j < $v; $j++){
        $q = $cost[$i][$j];
        $s = $j+$realstart;
        $query = "update matrix_$traincode set $station[$s] = $q where id = $i";
        $result = mysql_query($query) or die('MySQL query error');
    }
    echo "<p></p>";
}
?>