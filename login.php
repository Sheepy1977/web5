<?php
header('Content-Type:text/html;charset=UTF-8');
$username=$_COOKIE['web5'];
if (!$username)
{
	?>
		<span>
			<form method='post' action=''>
				姓名 <input type='text' name='username'  style='width:120px;border:1px solid #dedede;border-radius: 5px;' placeholder='请输入你的姓名'> 
				密码 <input type='password' name='password' style='width:120px;border:1px solid #dedede;border-radius: 5px;' >
				<input type='submit' name='login_submit' class='btn btn-info btn-xs' value='登录'>
			</form>
		</span>
	<?
}else{
	$row=getsql("select * from members where name='$username'",-1);
	$_SESSION['uid']=$row['uid'];
	$_SESSION['name']=$row['name'];
	$_SESSION['adminLevel']=$row['adminLevel'];
	?>
	<form method='post' action=''>
		<span>已登录为 <a href='changePassword.php'><?=$username?></a> 
		<input type='submit' name='login_submit'  class='btn btn-info btn-xs' value='退出'></span>
	</form>
	<?
}

if ($_POST)
{
	foreach ($_POST as $key => $value)
	{
		$tempname=$key;
		$$tempname=trim($value);
	}
	if ($_POST['login_submit']=='登录')
	{
		if ((!$username) or (!$password)) 
		{
			echo "<script>alert('请输入姓名和密码！');</script>";
		}else{
			$sql="select * from members where name='$username' and password='$password'";
			$ok=mysql_query($sql);
			$num=mysql_num_rows($ok);
			if ($num==0)
			{
				echo "<script>alert('姓名或者密码错误！');</script>";
			}else{
				$row=mysql_fetch_array($ok);
				$_SESSION['uid']=$row['uid'];
				$_SESSION['name']=$row['name'];
				$_SESSION['adminLevel']=$row['adminLevel'];
				if ($password=='123456') 
				{
					echo "<script>alert('第1次登录系统，请修改你的密码。\\n点击确定后系统自动跳转,勿使用123456等简单密码。')</script>";
					exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=changePassword.php'>");
				}
				setcookie('web5',$username,time()+3600*48);
				exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=#'>");
			}
		}
	}
	if ($_POST['login_submit']=='退出')
	{
		setcookie('web5',NULL);
		session_unset();
		session_destroy();
		exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=#'>");
	}
}