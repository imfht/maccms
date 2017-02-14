<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
headAdmin ("管理员管理");main();
dispseObj();

function main()
{
	global $db,$menulist;
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}manager";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT m_id,m_name,m_status,m_logintime,m_loginip FROM {pre}manager ORDER BY m_id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			m_name:{
				required:true,
				stringCheck:true,
				maxlength:32
			},
			m_password:{
				required:true,
				maxlength:32
			},
			m_status:{
				required:true
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}manager");
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
	$.get('admin_ajax.php?action=getinfo&tab={pre}manager&col=m_id&val='+id,function(obj){
		$("#win1 #m_id").val(obj.m_id);
		$("#m_name").val(obj.m_name);
		if(obj.m_status==1){
			$("#m_status").get(0).options[0].selected = true;
		}
		else{
			$("#m_status").get(0).options[1].selected = true;
		}
		$("input[name='m_levels[]']").each(function(i) {
			if(obj.m_levels.indexOf( $(this).val() ) !=-1 ){
				$(this).attr("checked", true);
			}
        });
	},"json");
}
</script>
<form action="" method="post" name="form1" id="form1">
<table class="tb">
	<tr>
	<td width="5%">&nbsp;</td>
	<td>帐号名</td>
	<td width="15%">是否锁定</td>
	<td width="17%">最后登陆</td>
	<td width="15%">登陆IP</td>
	<td width="15%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="6">没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$m_id=$row["m_id"];
	?>
    <tr>
	  <td><input name="m_id[]" type="checkbox" value="<?php echo $m_id?>" /></td>
      <td><?php echo $row["m_name"]?></td>
      <td><?php if($row["m_status"]==1){ echo "<font color=green>启用</font>";} else{ echo "<font color=red>禁用</font>";}?></td>
      <td><?php echo $row["m_logintime"]?></td>
      	  
      <td><?php echo $row["m_loginip"]?></td>
      <td><a href="javascript:void(0)" onclick="edit('<?php echo $m_id?>');return false;">修改</a> |
	  <a href="admin_ajax.php?action=del&tab={pre}manager&m_id=<?php echo $m_id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<td colspan="6">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'m_id[]')" />
	&nbsp;<input type="button" id="btnDel" value="批量删除" class="input" />
	&nbsp;<input type="button" id="btnAdd" value="添加"  class="input"/>
	</td></tr>
    <tr align="center">
      <td colspan="6">
       <?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_manager.php?page={p}")?>
      </td>
    </tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:480px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}manager" method="post" name="form2" id="form2">
<table class="tb">
	<input id="m_id" name="m_id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="20%" >管理员名称：</td>
	<td><INPUT id="m_name" size=50 value="" name="m_name">
	</td>
	</tr>
	<tr>
	<td>管理员密码：</td>
	<td><INPUT id="m_password" type="password" size=50 value="" name="m_password">
	</td>
	</tr>
	<tr>
	<td>管理权限：</td>
	<td>
	<?php
		$list1 = explode(",",$menulist);
		for($i=0;$i<count($list1);$i++){
		 	$list2= explode("|||",$list1[$i]);
			for($j=0;$j<count($list2);$j++){
				$list3=explode("||",$list2[$j]);
				echo "<input type=\"checkbox\" name=\"m_levels[]\" value=\"".$list3[1]."\" class=\"checkbox\"/>".$list3[0];
			}
		}
	?>
	</td>
	</tr>
	<tr>
	<td>管理员状态：</td>
	<td>
	<select id="m_status" name="m_status">
	<option value=1>启动</option>
	<option value=0>锁定</option>
	</select>
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