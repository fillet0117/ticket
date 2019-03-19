<?php
$stand_station = [];
$inlist[0] = dfs($start,$count);
$prior3 = [];
$prcount = 0;
global $prior;
for($a = 1; $a < $v; $a++){
    $count_2 = 0;
    for($b = 0; $b < count($inlist); $b++){
        $sub = dfs($a,$count);
        $sub2 = array();
        $sub2 = array_intersect($inlist[$b],$sub);
        if(empty($sub2)){
            $count_2++;
        }
        else{
            $inlist[$b] = array_unique(array_merge($inlist[$b], $sub));
        }
    }
    if($count_2 == count($inlist)){
        $inlist[$b] = dfs($a,$count);
    }
}

if(count($inlist) == 1){
    echo "此車次還有票,不需要站票";
}
elseif(count($inlist) == $v){
    echo "此車次已無車票,全程都需站票";
}
else{
    $test_count = 0;
    $maxst = 0;
    $minst = 0;
    foreach($inlist as $a){
        if(count($a) == 1){
            echo $station[$a[0]+$realstart];
            echo "    --站票區間-->    ";
        }
        elseif(count($a) != 1){
            if($test_count == 0){
                $minst = min($a);
                $maxst = max($a);
                if($maxst > $end){
                    $maxst = $end;
                }
                dijstra($minst,$maxst,$count);
                printpath($minst,$maxst);
                array_push($prior,$minst);
                array_push($prior,$maxst);
                $prior3[$prcount] = $prior;
                array_pop($prior);
                array_pop($prior);
                $prcount++;
                if(has_next($inlist) == false){
                    echo "    --站票區間-->    ";
                }
            }
            elseif(min($a) > $maxst){
                $minst = min($a);
                $maxst = max($a);
                if($maxst > $end){
                    $maxst = $end;
                }
                dijstra($minst,$maxst,$count);
                array_push($prior,$minst);
                array_push($prior,$maxst);
                $prior3[$prcount] = $prior;
                array_pop($prior);
                array_pop($prior);
                $prcount++;
                printpath($minst,$maxst);
                if(has_next($inlist) == false){
                    echo "    --站票區間-->    ";
                }
            }
            elseif(min($a) < $maxst){
                $minst = 0;
                foreach($a as $b){
                    if($b > $maxst && $b < max($a)){
                        $minst = min($mist,$b);
                    }
                }
                $maxst = max($a);
                if($maxst > $end){
                    $maxst = $end;
                }
                dijstra($minst,$maxst,$count);
                printpath($minst,$maxst);
                array_push($prior,$minst);
                array_push($prior,$maxst);
                $prior3[$prcount] = $prior;
                array_pop($prior);
                array_pop($prior);
                $prcount++;
                if(has_next($inlist) == false){
                    echo "    --站票區間-->    ";
                }
            }
        }
        $test_count++;
    }
    echo "<p></p>";
    $i = 0;
    foreach($prior3 as $j){
        $output=implode(",",$j);
        echo "<input type = hidden name = station$i value = $output>";
        $i++;
    }
    echo "<input type = hidden name = start value=$start>";
    echo "<input type = hidden name = end value=$end>";
    echo "<input type = hidden name = count value=$count>";
    echo "<input type = hidden name=traincode value=$train_code>";
    echo "<input type=submit value=確認>     ";
    echo '</form>';
}


function dfs($source,$count){
    global $cost;
    global $v;
    for($i = 0; $i < $v ; $i++){
        $visited[$i] = 0;
    }
    $element = $source;
    $item = $source;
    $subgraph = array();
    $stack = array();
    
    array_push($subgraph,$source);
    $visited[$source] = 1;
    array_push($stack,$source);
    
    while(!empty($stack)){
        $element = array_pop($stack);
        array_push($stack,$element);
        $item = $element;
        while($item < $v){
            if($cost[$element][$item] >= $count && $visited[$item] == 0){
                array_push($stack,$item);
                $visited[$item] = 1;
                $element = $item;
                $item = 1;
                array_push($subgraph,$element);
            }
            $item++;
        }
        array_pop($stack);
    }
    return $subgraph;
    
}

function has_next($array) {
    if (is_array($array)) {
        if (next($array) === false) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}


?>
