<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
headAdmin ("服务器组管理");main();
dispseObj();

function main()
{
	$xmlpath = "../inc/vodserver.xml";
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xmlpath);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName("server");
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			from:{
				required:true,
				maxlength:32,
				stringCheck:true
			},
			des:{
				required:true,
				maxlength:254
			},
			show:{
				required:true,
				stringCheck:true,
				maxlength:64
			},
			sort:{
				number:true
			},
			tip:{
				maxlength:255
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        	location.href=location.href;
	        });
	    }
	});
	$('#form2').form({
		onSubmit:function(){
			if(!$("#form2").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$("#flag").val("add");
		$("#from").attr("readonly",false);
		$('#win1').window('open');
	});
	$("#btnCancel").click(function(){
		location.href= location.href;
	});
});
function edit(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$("#from").attr("readonly",true);
	$('#win1').window('open');
	$('#form2').form('load','admin_ajax.php?action=getinfoxml&tab=vodserver&val='+id);
}
</script>
<form action="" method="post" id="form1" name="form1">
<table class="tb">
	<tr>
	<td width="10%">序号</td>
	<td width="10%">名称</td>
	<td width="10%">状态</td>
	<td>服务器地址</td>
	<td width="25%">备注</td>
	<td width="15%">操作</td>
	</tr>
	<?php
		if(count($nodes)==0){
    	echo '<tr><td align="center" colspan="6">没有任何记录!</td></tr>';
		}
		else{
			foreach($nodes as $node){
				$from = $node->attributes->item(2)->nodeValue;
				$status = $node->attributes->item(0)->nodeValue;
				$sort = $node->attributes->item(1)->nodeValue;
				$show = $node->attributes->item(3)->nodeValue;
				$des = $node->attributes->item(4)->nodeValue;
				$tip = $node->getElementsByTagName("tip")->item(0)->nodeValue;
	?>
	<tr>
	  <td><?php echo $sort?></td>
	  <td><?php echo $show?></td>
      <td><?php
      if ($status=="1"){ echo "<font color=green>启用</font>";} else{ echo "<font color=red>禁用</font>";}
      ?></td>
	  <td><?php echo $des?></td>
	  <td><?php echo $tip?></td>
      <td>
	  <a href="javascript:void(0)" onclick="edit('<?php echo $from?>');return false;">修改</a> |
	  <a href="admin_ajax.php?action=delxml&tab=vodserver&val=<?php echo $from?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td  colspan="6">
	&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />
	</td></tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=savexml&tab=vodserver" method="post" name="form2" id="form2">
<table class="tb">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="20%">标识(英文)：</td>
	<td><input id="from" size=40 value="" name="from" >
	</td>
    </tr>
	<tr>
	<td>名称：</td>
	<td><input id="show" size=40 value="" name="show" >
	</td>
    </tr>
    <tr>
	<td>状态：</td>
	<td>
	<select id="status" name="status">
	<option value="1">启用</option>
	<option value="0">禁用</option>
	</select>
	</td>
	</tr>
    <tr>
     <td>排序：</td>
      <td><input id="sort" size=10 value="" name="sort" >
	  </td>
    </tr>
	<tr>
	<td width="20%">地址：</td>
	<td><input id="des" size=40 value="" name="des">
	</td>
	</tr>
    <tr>
     <td>描述信息：</td>
      <td>
      <TEXTAREA id="tip" NAME="tip" ROWS="8" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>
	  </td>
    </tr>
    <tr align="center" >
      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"></td>
    </tr>
</table>
</form>
</div>
</body>
</html>
<?php
unset($nodes);
unset($xmlnode);
unset($doc);
}
?>