<!DOCTYPE html>
<html lang="UTF-8">
<head>
<meta charset="gb2312">
	<title>第五设计院 内网</title>
    <meta name="keywords" content="">
	<meta name="description" content="">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scroll.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="css/style.css" rel="stylesheet" />
	<?php
	session_start();
	//error_reporting("E_WARING");
	include_once('dbconn.php');
	include("lib.php");
	date_default_timezone_set('PRC');
	$today=date('Y-m-d',time());
	if (!$_SESSION['uid']) $uid=0;else $uid=$_SESSION['uid'];
	
	if ($_SESSION['adminLevel']==6)
	{
		$sql="select rid,projectSchedule.pid,stage,timeStart,timeEnd ,projectSchedule.comment 
		from projectSchedule,projectStageMember,projectList where schRid=rid and uid=$uid and timeStart < '$today' and timeEnd >= '$today' and projectSchedule.pid=projectList.pid and projectList.archive=0 order by timeEnd";
		$ok=mysql_query($sql);
		$totalItems=mysql_num_rows($ok);	
	}else{
		$sql="select * from projectSchedule,projectList where timeStart <= '$today' and timeEnd >= '$today' and projectSchedule.pid=projectList.pid and projectList.archive=0 order by timeEnd";
		$ok=mysql_query($sql);
		$totalItems=mysql_num_rows($ok);
		
	}
	$itemPerPage=4;//每页展示条数
	$pageNum=ceil($totalItems/$itemPerPage);
	?> 
	<script>
		$(document).ready(function()
		{
			refresh();
			$('.list_lh li:even').addClass('lieven');
			$("div.list_lh").myScroll({
				speed:60, //数值越大，速度越慢
				rowHeight:<?=$totalItems*100+200?> //li的高度
			});
		})

		function refresh()
		{
			$.get('function/getProjectList2.php?uid='+ <?=$uid?> +'&date='+ (new Date()).getTime(),function(data)
			{
				$('#items').html(data);
			});
			setTimeout('refresh()',180000);
		}
	</script>
</head>
<body style="background-color:#dedede;font-family:Microsoft YaHei;overflow: scroll;">
<div class='container'>
	<div  class='row'>
		<div  class='col-md-12'>
			<table class='table table-condensed'>
				<tbody>
					<tr>
						<td><h3>第五设计院<font size=4> <?=date('Y' ,time());?> 年第 <?= date('W',time());?> 周 项目公告</font></h3></td>
						<td style='text-align:right;vertical-align:bottom'><?php include('login.php')?></td>
					</tr>
				</tbody>
			</table>
			<hr>
		</div>
	</div>
	<div id='items' class='list_lh'>
		<!---主要内容-->
	</div>
	<nav class="navbar navbar-default navbar-fixed-bottom">
		<div class="container-fluid" style="background-color:#dedede">
			<p style='text-align:center'><a href='index.php'>©2017 第五设计院</a> | <a href='/5/admin/' target=_blank>管理入口</a></p>
			<p style='text-align:center;font-size:2px'><a href='mailto:776043@qq.com'>776043@qq.com</a></p>
		</div>
	</nav>
	
</div>

	
		
		