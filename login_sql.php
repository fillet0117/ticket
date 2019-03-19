<?php
    //header('Content-Type: text/html; charset=utf-8');
    //連線到資料庫伺服器
    if( ! @mysql_connect("localhost","root","")) 
        die("無法連線到資料庫伺服器");
    //else echo '連線成功';
    //設定連線的文字集與校對為utf8編碼
    mysql_query("SET NAMES utf8");
    //選擇資料庫
    if( ! @mysql_select_db('train'))
        die("無法連線到train");
    //else echo '連線到train';
?>