<?php
function getsql($sql,$i)
{
	$result=mysql_query($sql);
	//if(! $result) echo $sql.'<br>操作数据库失败.<br>'.mysql_error().'<br>';
	$count=mysql_num_rows($result);
	$row = mysql_fetch_array($result,MYSQL_BOTH);
	if ($i==-1) 
	{
		$re =$row;
	}
	else
	{
		$re = $row[$i];
	}
	if ($count ==0) $re='zero';
	return ($re);
}

function getMemberList($major,$rid)//读取各专业人员名单
{
	if ($major=='') $str=''; else $str=" and members.major='$major'";
	$sql="select members.name from members,projectStageMember where projectStageMember.schRid=$rid and projectStageMember.uid=members.uid $str order by major desc";
	$ok=mysql_query($sql);
	while ($row=mysql_fetch_array($ok))
	{
		$memberList=$row[0].' , '.$memberList;
	}
	return($memberList);
}

function showUserList($selectId,$adminLevelSelect,$majorSelect)
{
	$sql="select * from members where $adminLevelSelect $majorSelect and isOK=1";
	$ok=mysql_query($sql);
	echo "<select name='new_user' class='form-control'> ";
	echo "<option value=0></option>";
	while ($row=mysql_fetch_array($ok))
	{
		$name=$row['name'];
		$uid=$row['uid'];
		if ($uid==$selectId) $str='selected = "selected"'; else $str='';
		echo "<option value=$uid $str>$name</option>";
	}
	echo "</select>";
}	

function getUserName($uid)
{
	$sql="select name from members where uid=$uid";
	$name=getsql($sql,0);
	return ($name);
}

function getUserAdminLevel($uid)
{
	$sql="select adminLevel from members where uid=$uid";
	$adminLevel=getsql($sql,0);
	return ($adminLevel);
}