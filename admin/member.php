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
class member
{
    var $uid;
    var $name;
    var $major;
    var $phone;
    var $adminLevel;
}
$majorArray=array("建筑","结构","给排水","电气","通风","造价","办公室");
$adminLevelArray=array("员工"=>6,"项目管理"=>5,"副职"=>2,"正职"=>1);
?>

<div class='container'>
	<div id='memberList' style="width:300px; position: absolute; z-index:9; display:none"></div>
	<div class='row'>
        <nav class="navbar navbar-default navbar-inverse " role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php"><b>第五设计院 内网管理后台</b></a>
                </div>
                <div>
                    <ul class="nav navbar-nav navbar-right">
                        <li ><a href="index.php">项目管理</a></li>
                        <li class="active"><a href="member.php">人员管理</a></li>
                    </ul>
                </div>
            </div>
        </nav>
		<div class='col-md-4'>
			<div style='width:100%;height:300px'>
				<table class='table table-striped table-hover' style="border:1px solid #dedede;">
					<tbody>
						<tr style='background-color:#DEDEDE;'><td colspan=2 style='vertical-align:middle'><b>人员列表</b></td>
							<td colspan=2 align=right>
								<form method='post' action=''>
									<input type='text' style="border:1px solid #cdcdcd;border-radius:4px;" name='search_name'>&nbsp;
									<button type='submit' name='search' value=''><i class="fa fa-search" aria-hidden="true"></i></button>
								</form>
							</td>
						</tr>
						<?php
							$sql="select * from members where isOK=1 order by major,uid";
							$ok=mysql_query($sql);
							$t=1;
							while ($row=mysql_fetch_array($ok))
							{
								$name=$row['name'];
								$uid=$row['uid'];
								$major=$row['major'];
								$phone=$row['phone'];
								$workCount=mysql_num_rows(mysql_query("select * from projectStageMember where uid=$uid"));
								if ($workCount!=0) $workCount="($workCount)";else $workCount='';
								$m=new member();
								$m->uid=$uid;
								$m->name=$name;
								$m->major=$major;
								$m->phone=$phone;
								$m->adminLevel=$row['adminLevel'];
								$user[$uid]=$m;
							   
								echo "<tr><td>$t</td><td><a href='?uid=$uid'>$name $workCount</a></td><td align=center>$phone</td><td style='text-align:right'>$major</td></tr>";
								$t++;
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div class='col-md-8'>
			<table class='table table-striped' style="border:1px solid #dedede;">
				<tbody>
					<?php
						if ($_GET['uid']) $in_uid=$_GET['uid']; else $in_uid=0;
							
						$m=$user[$in_uid];
						$name=$m->name;
						$phone=$m->phone;
						$adminLevel=$m->adminLevel;
						$major=$m->major;

						echo "<tr style='background-color:#555555;color:#ffffff'><td colspan=4><b>人员概况</b>&nbsp;&nbsp;&nbsp;&nbsp;<small>$pCr_ed</small></td></tr>";
						echo "<form method='post' action=''>";
						echo "<tr>";
						echo "<td>姓名<input type='text' name='new_name'  class='form-control' value='$name'></td>";
						echo "<td>专业";
                        echo "<select name='new_major' class='form-control'> ";
						foreach ($majorArray as $value)
                        {
                            if ($major==$value) $str='selected = "selected"';else $str='';
                            echo "<option value='$value' $str>$value</option>";
                        }
                        echo "</select>";
						echo "</td>";
						echo "<td>电话号码<input type='text' name='new_phone'  class='form-control' value='$phone'></td>";
                        echo "<td>管理层级";
                        echo "<select name='new_adminLevel' class='form-control'> ";
                        foreach ($adminLevelArray as $key => $value)
                        {
                            if ($adminLevel==$value) $str='selected = "selected"';else $str='';
                            echo "<option value='$value' $str>$key</option>";
                        }
                        echo "</td></tr>";
						echo "<tr><td colspan=4>备注<textarea name='new_comment'  class='form-control'>$comment</textarea></td></tr>";
						echo "<tr>";
						echo "<td><input type='submit' name='new_post' value='新增' class='btn btn-success'></td>";
						echo "<td colspan=3 style='text-align:right'><input type='submit' name='new_post' value='编辑' class='btn btn-info' $pCanEdit>&nbsp;";
						echo "<input type='submit' name='new_post' value='离职/入职' class='btn btn-primary' $pCanEdit  >&nbsp;";
						echo "<input type='submit' name='new_post' value='删除' class='btn btn-danger' $pCanEdit onClick=\"return confirm('您确认执行删除操作么?')\"></td></tr>";
						echo "<input type='hidden' name='new_uid' value='$in_uid'>";
						echo "</form>";
					
					?>
				</tbody>
			</table>
			
					<?php
					if ($_GET['uid'])
					{
						echo "<table class='table table-striped table-bordered' style='border:1px solid #dedede;'><tbody>";
						echo "<tr style='background-color:##DEDEDE;'><td colspan=4><b>$name</b> 的工作</td></tr><tr><td></td><td>项目名称</td><td>阶段</td><td>备注</td></tr>";
						$in_uid=$_GET['uid'];
						$sql="select projectList.name ,projectSchedule.stage,projectSchedule.comment,projectSchedule.rid,projectList.pid from projectStageMember,projectSchedule,projectList where projectStageMember.uid=$in_uid 
						and projectStageMember.pid=projectList.pid
						and projectSchedule.rid=projectStageMember.schRid";
						$ok=mysql_query($sql);
						$t=1;
						while ($row=mysql_fetch_array($ok))
						{
							$projectName=$row['name'];
							$projectPid=$row['pid'];
							$projectStage=$row['stage'];
							$stageComment=$row['comment'];
							echo "<tr><td>$t</td><td><a href='index.php?pid=$projectPid'>$projectName</a></td><td>$projectStage</td><td>$stageComment</td></tr>";
							$t++;
						}
					}
					echo "</tbody></table>";
			?>
			</div>
			<nav class="navbar navbar-default navbar-fixed-bottom">
				<div class="container-fluid" style="background-color:#dedede">
					<p style='text-align:center'><a href='index.php'>©2017 第五设计院</a> </p>
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
	};
	if ($new_post=='新增')
	{
		$sql="insert into members (name,password,major,adminLevel,phone,isOK,comment) value('$new_name','123456','$new_major','$new_adminLevel','$new_phone',1,'$new_comment')";
		mysql_query($sql);
	}
	if ($new_post=='编辑')
	{
		$sql="update members set name='$new_name' ,major='$new_major',adminLevel='$new_adminLevel',phone='$new_phone',comment='$new_comment' where uid=$new_uid";
		mysql_query($sql);
	}
	if ($new_post=='删除')
	{
		$sql="delete from projectStageMember where uid=$new_uid";//删除项目阶段分配人员列表
		mysql_query($sql);
		$sql="delete from members where uid=$new_uid";
		mysql_query($sql);
	}
	if ($search_name!='')
	{
		$uid=getsql("select uid from members where name='$search_name'",0);
		if ($uid=='zero') echo "<script>alert('查无此人')</script>";
		exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=?uid=$uid'>");
	}
	exit ("<META HTTP-EQUIV=REFRESH CONTENT='0;URL=?uid=$new_uid'>");
}



/////////////////////////////////////////////////////////////////////////////			
