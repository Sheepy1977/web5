<!DOCTYPE html>
<html lang="UTF-8">
<head>
<meta charset="gb2312">
	<title>第五设计院 内网修改用户密码</title>
    <meta name="keywords" content="">
	<meta name="description" content="">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="font-family:Microsoft YaHei;overflow: scroll;">
<div class='container'>
	<div class='row'>
		<div class='col-md-4'>
		</div>
		<div class='col-md-4'>
			<br>
			<br>
			<br>
			<br>
			<br>
			<?php
			session_start();
			error_reporting("E_WARING");
			include_once ('dbconn.php');
			include("lib.php");
			header('Content-Type:text/html;charset=UTF-8');
			$uid=$_SESSION['uid'];
			if (!isset ($uid)) die();

			$name=getUserName($uid);
			?>
			<form method='post' action=''>
				<table class='table table-condensed'   style='background-color:#efefef;box-shadow: 3px 3px 3px #888888;'>
					<tbody>
						<tr>
							<td colspan=2 style='background-color:#455A64;color:#fff'>修改你的密码</td>
						</tr>
						<tr>
							<td style='vertical-align:middle'>新密码</td><td><input type='password' class='form-control' name='new_password' placeholder='至少6位，仅允许数字和英文字母' onkeyup="value=value.replace(/[^a-zA-Z0-9]/g,'')"></td>
						</tr>
						<tr>
							<td style='vertical-align:middle'>再次输入</td><td><input type='password' class='form-control' name='re_new_password' placeholder='重复输入一次新密码' onkeyup="value=value.replace(/[^a-zA-Z0-9]/g,'')"></td>
						</tr>
						<tr>
							<td colspan=2 align=right><input type='submit' name='password_submit' class='btn btn-primary' value='提交'></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<nav class="navbar navbar-default navbar-fixed-bottom">
		<div class="container-fluid" style="background-color:#dedede">
			<p style='text-align:center'><a href='index.php'>©2017 第五设计院</a> | <a href='/5/admin/' target=_blank>管理入口</a></p>
		</div>
	</nav>
</div>
<?php
if ($_POST)
{
	foreach ($_POST as $key => $value)
	{
		$tempname=$key;
		$$tempname=trim($value);
	}
	if ((!$new_password) or (!$re_new_password)) echo "<script>alert('请输入密码！');</script>";
	if ($new_password!=$re_new_password) echo "<script>alert('两次输入不一致！');</script>";
	$sql="update members set password='$new_password' where uid=$uid";
	mysql_query($sql);
	echo "<script>alert('密码修改完毕，请重新登录。');</script>";
	setcookie('web5',NULL);
	session_unset();
	session_destroy();
	exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=index.php'>");
}	




