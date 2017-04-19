<?php
error_reporting("E_WARING");
include_once ('../dbconn.php');
header('Content-Type:text/html;charset=UTF-8');
$username=$_GET['username'];
$password=$_GET['password'];