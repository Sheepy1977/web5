<?php
session_start;
error_reporting("E_WARING");
include_once ('../dbconn.php');
include ('../lib.php');
header('Content-Type:text/html;charset=UTF-8');
date_default_timezone_set('PRC');
$today=date('Y-m-d',time());
$uid=$_GET['uid'];
if ($uid!=0) $adminLevel=getsql("select adminLevel from members where uid=$uid",0); else $adminLevel=0;
if ($adminLevel==6)
{
	$sql="select rid,projectSchedule.pid,stage,timeStart,timeEnd ,projectSchedule.comment 
	from projectSchedule,projectStageMember,projectList where schRid=rid and uid=$uid and timeStart <= '$today' and timeEnd >= '$today'  and projectSchedule.pid=projectList.pid and projectList.archive=0 order by timeEnd";
	$ok=mysql_query($sql);
	$totalItems=mysql_num_rows($ok);	
}else{
	$sql="select * from projectSchedule,projectList  where timeStart <= '$today' and timeEnd >= '$today' and projectSchedule.pid=projectList.pid and projectList.archive=0 order by timeEnd";
	if (!$_GET['p']) $page=1; else $page=$_GET['p'];
	$itemPerPage=4;//每页展示条数
	$ok=mysql_query($sql);
	$totalItems=mysql_num_rows($ok);
	$pageNum=ceil($totalItems/$itemPerPage);
	$offset=($page-1)*$itemPerPage;
	$sql="select *,projectSchedule.comment as schComment from projectSchedule,projectList where timeStart <= '$today' and timeEnd >= '$today' and projectSchedule.pid=projectList.pid and projectList.archive=0 order by timeEnd limit $offset,$itemPerPage";
}
$ok=mysql_query($sql);
$tt=1;
while ($row=mysql_fetch_array($ok))
{
	$rid=$row['rid'];
	$pid=$row['pid'];
	$stage=$row['stage'];
	$timeStart=$row['timeStart'];
	$timeEnd=$row['timeEnd'];
	$timeLeft=floor((strtotime($timeEnd)-time())/86400)+1;
	$timeLast=floor((strtotime($timeEnd)-strtotime($timeStart))/86400)+1;
	$comment=$row['schComment'];
	$showMember=getMemberList("",$rid);
	$showMember=substr($showMember,0,strlen($showMember)-1);
	$r=getsql("select * from projectList where pid=$pid",-1);
	$admin=$r['admin'];
	
	$projectName=$r['name'];
	$adminName=getsql("select name from members where uid=$admin",0);
	if ($headColor=='#455A64') $headColor="#607D8B"; else $headColor="#455A64"; 
	$showItemNum=$tt+$offset."/".$totalItems;
	?>
	
	<div class='row'>
		<div  class='col-md-9'>
			<table class='table table-striped' style="background-color:#fff;border:1px solid #cccccc;box-shadow: 1px 1px 1px #bbbbbb;">
				<tbody>
					<tr style='background-color:<?=$headColor?>;color:#fff'>
						<td><small><?=$showItemNum?></small> <b><?=$projectName?></b> (<?=$stage?>)</td><td style='text-align:right'><small>项目负责人：<?=$adminName?></small></td>
					</tr>
					<tr>
						<td colspan=2><?=$showMember?></td>
					</tr>
					<tr>
						<td colspan=2 style='text-align:right'>截止日期：<?=$timeEnd?>，还有<b><?=$timeLeft?></b>天</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class='col-md-3'>
			<table class='table table-striped' style="border:1px solid #dedede;">
				<tbody>
					<tr>
						<td><textarea rows=4 class='form-control' disabled><?=$comment?></textarea></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	$tt++;
}
$new_page=$page+1;
if ($new_page>$pageNum) $new_page=1;
?>