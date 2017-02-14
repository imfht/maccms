<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
headAdmin ("用户组管理");main();
dispseObj();

function main()
{
	global $db;
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}user_group";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT ug_id,ug_name,ug_type,ug_popedom,ug_upgrade,ug_popvalue FROM {pre}user_group ORDER BY ug_popvalue DESC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			ug_name:{
				required:true,
				stringCheck:true,
				maxlength:32
			},
			ug_popvalue:{
				number:true
			},
			ug_upgrade:{
				number:true
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
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}user_group");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$("#flag").val("add");
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
	$('#win1').window('open');
	$.get('admin_ajax.php?action=getinfo&tab={pre}user_group&col=ug_id&val='+id,function(obj){
		$("#ugid").val(obj.ug_id);
		$("#ug_name").val(obj.ug_name);
		$("#ug_popvalue").val(obj.ug_popvalue);
		$("#ug_upgrade").val(obj.ug_upgrade);
		$("input[name='ug_type[]']").each(function(i) {
			if(obj.ug_type.indexOf( ','+$(this).val()+',' ) !=-1 ){
				$(this).attr("checked", true);
			}
        });
        $("input[name='ug_popedom[]']").each(function(i) {
			if(obj.ug_popedom.indexOf( $(this).val() ) !=-1 ){
				$(this).attr("checked", true);
			}
        });
	},"json");
}
</script>
<form action="" method="post" id="form1" name="form1">
<table class="tb">
	<tr>
	<td width="4%">&nbsp;</td>
	<td>用户组名称</td>
	<td width="15%">组内人数</td>
	<td width="15%">购买所需积分</td>
	<td width="15%">权限值</td>
	<td width="15%">操作</td>
    </tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="7">没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$m_id=$row["m_id"];
	?>
	<td><?php if (app_reggroup!=$row["ug_id"]){?>
	<input name="ug_id[]" type="checkbox" value="<?php echo $row["ug_id"]?>"/><?php }?></td>
	<td><?php echo $row["ug_name"]?></td>
	<td><?php echo $db->getOne("SELECT count(u_id) FROM {pre}user WHERE u_group=".$row["ug_id"])?></td>
	<td><?php echo $row["ug_upgrade"]?></td>
	<td><?php echo $row["ug_popvalue"]?></td>
	<td>
	<a href="javascript:void(0)" onclick="edit('<?php echo $row["ug_id"]?>');return false;">修改</a> | <?php if (app_reggroup!=$row["ug_id"]){?><a href="admin_ajax.php?action=del&tab={pre}user_group&ug_id=<?php echo $row["ug_id"]?>" onClick="return confirm('确定要删除吗?');">删除</a><?php }?></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<tr>
	<td colspan="7">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'ug_id[]')" />
	&nbsp;<input type="button" id="btnDel" value="批量删除" class="input" />
	&nbsp;<input type="button" id="btnAdd" value="添加"  class="input"/>
	</td></tr>
	<tr align="center">
	<td colspan="7">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_user_group.php?page={p}")?>
	</td>
	</tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:550px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}user_group" method="post" name="form2" id="form2">
<table class="tb">
	<input id="ugid" name="ugid" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="30%">会员组名称：</td>
	<td><INPUT id="ug_name" size=20 value="" name="ug_name">
	</td>
	</tr>
	<tr>
	<td>包含分类：<br></td>
	<td>
	<?php
	$rs1 = $db->query("SELECT t_id,t_name FROM {pre}vod_type");
	$i=0;
	while ($row1 = $db ->fetch_array($rs1))
	{
		if (($i%4)==0){echo "<br>";}
	?>
	<input type="checkbox" name="ug_type[]" value="<?php echo $row1["t_id"]?>"/><?php echo $row1["t_name"]?>
	<?php
		$i++;
	}
	unset($rs1);
	?>
	</td>
	</tr>
	<tr>
	<td>权限：<br></td>
	<td>
	<input type="checkbox" name="ug_popedom[]" value="1" checked/>浏览分类页
	<input type="checkbox" name="ug_popedom[]" value="2" checked/>浏览内容页
	<input type="checkbox" name="ug_popedom[]" value="3" checked/>浏览播放页
	<input type="checkbox" name="ug_popedom[]" value="4" checked/>浏览下载页
	</td>
    </tr>
	<tr>
	<td>升级所需积分：<br>(使用积分购买组权限)</td>
	<td>
	<INPUT id="ug_upgrade" size=20 value="0" name="ug_upgrade">
	</td>
    </tr>
	<tr>
	<td>权限值：<br>(数值越大权限越高)</td>
	<td>
	<INPUT id="ug_popvalue" size=20 value="0" name="ug_popvalue">
	</td>
	</tr>
	<tr align="center">
      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
    </tr>
</table>
</form>
</div>
</body>
</html>
<?php
unset($rs);
}
?>