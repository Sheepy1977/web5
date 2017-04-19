<?php
error_reporting("E_WARING");
include_once ('../dbconn.php');
header('Content-Type:text/html;charset=UTF-8');
$rid=$_GET['rid'];
if (!isset ($rid)) die();
$majorID=$_GET['major'];
$majorList = array('建筑','结构','给排水','电气','通风','造价');
$divList = array("jz","jg","shui","dian","qi","zj");
$major=$majorList[$majorID];
$div=$divList[$majorID].$rid;
$uid=array();
$assignMemberArray=array();
$sql="select uid from projectStageMember where schRid=$rid";
$ok=mysql_query($sql);
while ($row=mysql_fetch_array($ok))
{
	$assignMemberArray[]=$row['0'];//获得当前阶段分配的uid清单
}

$majorMemberArray=array();
$sql="select * from members where isOK=1 and major='$major'";
$ok=mysql_query($sql);
while ($row=mysql_fetch_array($ok))
{
	$majorMemberUidArray[]=$row['uid'];//获得当前专业的uid清单
	$majorMemberNameArray[$row['uid']]=$row['name'];
}

//echo "<form id='editMemberForm'>";
echo "<table class='table table-condensed'   style='background-color:#ffffff;box-shadow: 3px 3px 3px #888888;'>";
echo "<tbody><tr style='background-color:#f1b44d;;color:#ffffff;cursor:pointer'  onClick='javascript:cancelEdit()'><td colspan=3 >$major 专业人员安排</td><td style='text-align:right'>x</td></tr>";


	for ($p=0;$p<count($majorMemberUidArray);$p++)
	{
	 
	 if (in_array($majorMemberUidArray[$p],$assignMemberArray)) 
	 {
	 	$tdstr='<font color=red>';
	 }else{
	 	$tdstr='';
	 }
	 $uid=$majorMemberUidArray[$p];
	 $name=$majorMemberNameArray[$uid];
	 echo "<td style='cursor:pointer' onClick='javascript:addMember(\"$name\",\"$div\")'>$tdstr$name";
	 $str='';
	 echo "</font></td>";
	 if ($pp>2)
	 {
		$col=5-$pp;
		echo "</tr><tr>";
		$pp=0;
	 }else 
	 {
		$pp++;
	 }
	}

?>
<td colspan= <?=$col?>></td></tr>

	</tbody>
</table></form>

