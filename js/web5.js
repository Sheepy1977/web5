

function memberEdit(major,rid,inputName)//���ɳ�Ա�б��Ա༭  ��רҵ ������Ա
{
		var p = $(inputName);
		var of=p.offset();
		var newY=of.top+35;
		var newX=of.left+15;
		$('#memberList').css({"left":newX,"top":newY});
		$.get('../function/getMemberList.php?rid='+rid+"&major="+major+"&date="+ (new Date()).getTime(),function(data)
		{
			$('#memberList').hide();
			$('#memberList').html(data);
			$('#memberList').show();
		});
}


function cancelEdit()
{
	$('#memberList').hide();
}

function addMember(name,divName) //���밲����Ա
{
	var str="";
	str+=$("input[id='"+divName+"']").val()+name+",";
	$("input[id='"+divName+"']").val(str);
}

