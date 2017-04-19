<?php
//建立数据库连接
//$db = mysql_connect("localhost", "root","root");
$db = mysql_connect("10.80.126.54", "systemuser","79817981");

mysql_select_db("systemuser", $db);
mysql_query("set names 'utf8'");