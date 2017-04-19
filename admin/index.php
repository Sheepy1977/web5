<!DOCTYPE html>
<html lang="UTF-8">
<head>
	<title>第五设计院 内网管理后台</title>
    <meta name="keywords" content="">
	<meta name="description" content="">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="../css/bootstrap.min.css" rel="stylesheet">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../js/web5.js"></script>
	<script type="text/javascript" src="../js/bootstrap-datepicker.js"></script>
	<link rel="stylesheet" href="../css/datepicker.css">
	<link rel="stylesheet" href="../css/font-awesome.min.css">
</head>
<body style="font-family:Microsoft YaHei">
<?php
session_start();
error_reporting("E_WARING");
include_once("../dbconn.php");
include("../lib.php");
if (!$_SESSION['uid']) exit ("<br><br><br><br><p style='text-align:center'><b>请先登录</b></p>");
if ($_SESSION['adminLevel']==6) exit ("<br><br><br><br><p style='text-align:center'><b> 对不起，你无权进入后台</b></p>");
class project
{
	var $pid;
	var $name;
	var $adminId;
	var $adminName;
	var $contract;
	var $comment;
	var $archive;
	var $cr_ed;//创建者和编辑者信息
}
class project_schedule
{
	var $sName;
	var $sStart;
	var $sEnd;
	var $sPid;
	var $sJZ;
	var $sJG;
	var $sShui;
	var $sDian;
	var $sQi;
	var $sZJ;
	var $rid;
	var $comment;
	var $cr_ed;//创建者和编辑者信息
}
$projectTypeArray=array("运行项目列表","归档项目列表");
$sqlArray=array("archive !=1","archive=1");
?>

<div class='container'>
	<div id='memberList' style="width:300px; position: absolute; z-index:9; display:none"></div>
	<div class='row'>
        <nav class="navbar navbar-default navbar-inverse" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php"><b>第五设计院 内网管理后台</b></a>
                </div>
                <div>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="active"><a href="index.php">项目管理</a></li>
                        <li><a href="member.php">人员管理</a></li>
                    </ul>
                </div>
            </div>
        </nav>
		<div class='col-md-4'>
			<?
			for ($i=0;$i<2;$i++)
			{
				echo "<table class='table table-striped ' style='border:1px solid #dedede;'><tbody>";
				echo "<tr style='background-color:#DEDEDE;'><td colspan=3><b>$projectTypeArray[$i]</b></td></tr>";
				$sql="select * from projectList where $sqlArray[$i] order by pid";
				$ok=mysql_query($sql);
				$t=1;
				while ($row=mysql_fetch_array($ok))
				{
					$pid=$row['pid'];
					$p=new project();
					$p->pid=$pid;
					$p->name=$row['name'];
					$p->adminId=$row['admin'];
					$adminName=getsql("select name from members where uid=".$row['admin'],0);
					$p->adminName=$adminName;
					$p->contract=$row['contract'];
					$p->comment=$row['comment'];
					$tt=getUserName($row['creator']).' 创建于'.$row['createTime'];
					if ($row['editor']!=0) $tt=$tt.','.getUserName($row['editor']).' 编辑于'.$row['editTime'];
					$p->cr_ed=$tt;
					
					if (($_SESSION['adminLevel'] > getUserAdminLevel($row['creator'])) and ($_SESSION['uid']!=$row['creator'])) $pCanEdit='disabled'; else $pCanEdit='';//此处只判断是否满足大于创建者的权限，而不判断编辑者的权限。 
					
					$projectList[$pid]=$p;
					echo "<tr><td>$t</td><td><a href='?pid=$pid'>".$row['name']."</a></td><td style='text-align:right;vertical-align:middle;width:60px'>$adminName</td></tr>";
					$t++;
				}
				echo "</tbody></table>";
			}
			?>
		</div>
		<div class='col-md-8'>
			<table class='table table-striped' style="border:1px solid #dedede;">
				<tbody>
					
					<?php
						if ($_GET['pid'])
						{
							$in_pid=$_GET['pid'];
							$p=$projectList[$in_pid];
							$pName=$p->name;
							$pAdminId=$p->adminId;
							$pAdminName=$p->adminName;
							$pContract=$p->contract;
							$pComment=$p->comment;
							$pCr_ed=$p->cr_ed;
						}
						echo "<tr style='background-color:#555555;color:#ffffff'><td colspan=3><b>项目概况</b>&nbsp;&nbsp;&nbsp;&nbsp;<small>$pCr_ed</small></td></tr>";
						echo "<form method='post' action='index.php'>";
						echo "<tr>";
						echo "<td>项目名称<input type='text' name='new_name'  class='form-control' value='$pName'></td>";
						echo "<td>项目负责人";
						$adminLevelSelect='adminLevel<6';
						$majorSelect='';
						showUserList($pAdminId,$adminLevelSelect,$majorSelect);
						echo "</td>";
						echo "<td>合同编号<input type='text' name='new_contract'  class='form-control' value='$pContract'></td></tr>";
						echo "<tr><td colspan=3>备注<textarea name='new_comment'  class='form-control'>$pComment</textarea></td></tr>";
						echo "<tr>";
						echo "<td><input type='submit' name='new_post' value='新增' class='btn btn-success'></td>";
						echo "<td colspan=2 style='text-align:right'><input type='submit' name='new_post' value='编辑' class='btn btn-info' $pCanEdit>&nbsp;";
						echo "<input type='submit' name='new_post' value='归档/开放' class='btn btn-primary' $pCanEdit  >&nbsp;";
						echo "<input type='submit' name='new_post' value='删除' class='btn btn-danger' $pCanEdit onClick=\"return confirm('您确认执行删除操作么?')\"></td></tr>";
						echo "<input type='hidden' name='new_pid' value='$in_pid'>";
						echo "</form>";
					
					?>
				</tbody>
			</table>
			<?php
			if ($_GET['pid'])
			{
				$in_pid=$_GET['pid'];
				$sql="select * from projectSchedule where pid=$in_pid";
				$ok=mysql_query($sql);
				$t=0;
				while ($row=mysql_fetch_array($ok))
				{
					$rid=$row['rid'];
					$ps=new project_schedule();
					$ps->sName=$row['stage'];
					$ps->sStart=$row['timeStart'];
					$ps->sEnd=$row['timeEnd'];
					$ps->comment=$row['comment'];
					$ps->sJZ=getMemberList("建筑",$rid);
					$ps->sJG=getMemberList("结构",$rid);
					$ps->sShui=getMemberList("给排水",$rid);
					$ps->sDian=getMemberList("电气",$rid);
					$ps->sQi=getMemberList("通风",$rid);
					$ps->sZJ=getMemberList("造价",$rid);
					$ps->rid=$rid;
					$projectSchedule[$t]=$ps;
					$tt=getUserName($row['creator']).' 创建于'.$row['createTime'];
					if ($row['editor']!=0) $tt=$tt.','.getUserName($row['editor']).' 编辑于'.$row['editTime'];
					$ps->cr_ed=$tt;
					
					if (($_SESSION['adminLevel']>getUserAdminLevel($row['creator'])) and ($_SESSION['uid']!=$row['creator'])) $sCanEdit='disabled'; else $sCanEdit='';//此处只判断是否满足大于创建者的权限，而不判断编辑者的权限。
					
					$t++;
				}
				if ($t==0) $tt=1; else $tt=$t;//在有阶段记录的情况下，不再出现多余的“新建”空表格。
				$ridArray=array();
				for ($i=0;$i<$tt;$i++)
				{
					$ps=$projectSchedule[$i];
					$sName=$ps->sName;
					$sStart=$ps->sStart;
					$sEnd=$ps->sEnd;
					$sJZ=$ps->sJZ;
					$sJG=$ps->sJG;
					$sShui=$ps->sShui;
					$sDian=$ps->sDian;
					$sQi=$ps->sQi;
					$sZJ=$ps->sZJ;
					$rid=$ps->rid;
					$sComment=$ps->comment; 
					$sCr_ed=$ps->cr_ed;
					if ($i==$t) $editStr='disabled' ;else $editStr='';
					if (!$rid) $rid=0;
					$ridArray[]=$rid;
					?>
					
					<table class='table table-striped' style="border:1px solid #dedede;">
						<tbody>
							<tr style='background-color:#DEDEDE;'><td colspan=3><b><?=$sName?> 阶段</b> (<?=$i+1?>/<?=$tt?>) <small><?=$sCr_ed?></small></td></tr>
							<form method='post' action='index.php'>
							<tr>
								<td>阶段名称<input type='text' name='new_stage'  class='form-control' value='<?=$sName?>'></td>
								<td>起始时间<input type='text' name='new_stage_start' id='dp_<?=$rid?>_1' class='form-control' value='<?=$sStart?>'></td>
								<td>终止时间<input type='text' name='new_stage_end'  id='dp_<?=$rid?>_2' class='form-control' value='<?=$sEnd?>'></td>
							</tr>
							<tr>
								<td >建筑<input type='text' name='new_stage_jz' id='jz<?=$rid?>' class='form-control' value='<?=$sJZ?>' onClick='javascript:memberEdit(0,<?=$rid?>,jz<?=$rid?>)'></td>
								<td >结构<input type='text' name='new_stage_jg' id='jg<?=$rid?>' class='form-control' value='<?=$sJG?>' onClick='javascript:memberEdit(1,<?=$rid?>,jg<?=$rid?>)'></td>
								<td >给排水<input type='text' name='new_stage_shui' id='shui<?=$rid?>' class='form-control' value='<?=$sShui?>' onClick='javascript:memberEdit(2,<?=$rid?>,shui<?=$rid?>)'></td>
							</tr>
							<tr>
								<td >电气<input type='text' name='new_stage_dian' id='dian<?=$rid?>' class='form-control' value='<?=$sDian?>' onClick='javascript:memberEdit(3,<?=$rid?>,dian<?=$rid?>)'></td>
								<td >通风<input type='text' name='new_stage_qi' id='qi<?=$rid?>' class='form-control' value='<?=$sQi?>' onClick='javascript:memberEdit(4,<?=$rid?>,qi<?=$rid?>)'></td>
								<td >造价<input type='text' name='new_stage_zj' id='zj<?=$rid?>' class='form-control' value='<?=$sZJ?>' onClick='javascript:memberEdit(5,<?=$rid?>,zj<?=$rid?>)'></td>
							</tr>
							<tr>
								<td colspan=3>备注<input type='text' name='new_stage_comment'  class='form-control' value='<?=$sComment?>'></td>
							</tr>
							<tr>
								<td><input type='submit' name='stage_submit' value='新增' class='btn btn-success'></td>
								<td colspan=2 style='text-align:right'>
									<input type='submit' name='stage_submit' value='编辑' class='btn btn-info' <?=$editStr?> <?=$sCanEdit?>>
									<input type='submit' name='stage_submit' value='删除' class='btn btn-danger' onClick="return confirm('您确认执行删除操作么?')" <?=$editStr?> <?=$sCanEdit?>>
									<input type='hidden' name='new_pid' value='<?=$_GET['pid']?>'>
									<input type='hidden' name='new_rid' value='<?=$rid?>'>
								</td>
							</tr>
							</form>
						</tbody>
					</table>
				
					<?php
				}
				echo "<script>";
				echo "$(function(){";
				foreach ($ridArray as $value)
				{
					$dpID1='#dp_'.$value."_1";
					$dpID2='#dp_'.$value."_2";
					echo "$('$dpID1').datepicker({
							format: 'yyyy-mm-dd'
						});	";
					echo "$('$dpID2').datepicker({
							format: 'yyyy-mm-dd'
						});	"; 
				}
				echo "})</script>";
			}
			?>
			</div>
			<nav class="navbar navbar-default navbar-fixed-bottom">
				<div class="container-fluid" style="background-color:#dedede">
					<p style='text-align:center'><a href='../index.php'>©2017 第五设计院</a></p>
				</div>
			</nav>
			<?
/////////////////////////////////////////////////////////////////////////////
if ($_POST)
{
	foreach ($_POST as $key => $value)
	{
		$tempname=$key;
		$$tempname=trim($value);
		//echo $tempname.':'.$temp.'<br>';
	}
	$uid=$_SESSION['uid'];
	$datetime=date('Y-m-d H-i-s',time());
	if ($new_post!='')
	{
		if ($new_post=='新增')
		{
			$sql="insert into projectList (name,admin,contract,comment,creator,createTime) value('$new_name','$new_user','$new_contract','$new_comment',$uid,'$datetime')";
			mysql_query($sql);
			$sql="select max(pid) from projectList";
			$new_pid=getsql($sql,0);
		}
		if ($new_post=='编辑')
		{
			$sql="update projectList set name='$new_name' ,admin='$new_user',contract='$new_contract',comment='$new_comment' ,editor = $uid ,editTime='$datetime' where pid=$new_pid";
			mysql_query($sql);
		}
		if ($new_post=='归档/开放')
		{
			$sql="select archive from projectList where pid=$new_pid";
			$arc=getsql($sql,0);
			if ($arc==1) $new_arc=0; else $new_arc=1;
			$sql="update projectList set archive=$new_arc where pid=$new_pid";
			mysql_query($sql);
		}
		if ($new_post=='删除')
		{
			$sql="delete from projectList where pid=$new_pid";//删除项目列表
			mysql_query($sql);
			$sql="delete from projectSchedule where pid=$new_pid";//删除项目阶段列表
			mysql_query($sql);
			$sql="delete from projectStageMember where pid=$new_pid";//删除项目阶段分配人员列表
			mysql_query($sql);
		}
		exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=?pid=$new_pid'>");
	}
	if ($stage_submit!='')
	{
		$new_member=$new_stage_jz.$new_stage_jg.$new_stage_shui.$new_stage_dian.$new_stage_qi.$new_stage_zj;
		$new_member=trim($new_member);
		$memberArray=explode(",",$new_member);
		if ($stage_submit=='新增')
		{
			$sql="insert into projectSchedule (pid,stage,timeStart,timeEnd,comment,creator,createTime) value ($new_pid,'$new_stage','$new_stage_start','$new_stage_end','$new_stage_comment',$uid,'$datetime')";
			mysql_query($sql);
			if (count($memberArray)!=0)
			{
				$rid=getsql("select max(rid) from projectSchedule",0);
				foreach ($memberArray as $userName)
				{
					$userName=trim($userName);
					$uid=getsql("select uid from members where name='$userName'",0);
					$sql="insert into projectStageMember (uid,schRid,pid) value ($uid,$rid,$new_pid)";
					mysql_query($sql);
				}
			}
		}
		if ($stage_submit=='编辑')
		{
			$sql="update projectSchedule set stage='$new_stage',timeStart='$new_stage_start',timeEnd='$new_stage_end',comment='$new_stage_comment',editor=$uid,editTime='$datetime' where rid = $new_rid";
			mysql_query($sql);
			if (count($memberArray)!=0)
			{
				$sql="delete from projectStageMember where schRid=$new_rid";
				mysql_query($sql);//先删除原先的分配人员，直接插入新的
				foreach ($memberArray as $value)
				{
					$userName=trim($value);
					$uid=getsql("select uid from members where name='$userName'",0);
					$sql="insert into projectStageMember (uid,schRid,pid) value ($uid,$new_rid,$new_pid)";
					mysql_query($sql);
				}
			}
		}
		if ($stage_submit=='删除')
		{
			$sql="delete from projectSchedule where rid=$new_rid";
			mysql_query($sql);
			$sql="delete from projectStageMember where schRid=$new_rid";
			mysql_query($sql);
		}
		exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=?pid=$new_pid'>");
	}
	
}



/////////////////////////////////////////////////////////////////////////////			
