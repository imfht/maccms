<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
$_SESSION["upfolder"] = "../upload/vodtopic";
switch($action)
{
	case "editall" : editall();break;
	default : headAdmin ("视频专题管理");main();break;
}
dispseObj();

function editall()
{
	global $db;
	$t_id = be("arr","t_id");
	$ids = explode(",",$t_id);
	foreach( $ids as $id){
		$t_name = be("post","t_name" .$id);
		$t_sort = be("post","t_sort" .$id);
		$t_enname = be("post","t_enname" .$id);
		$t_template = be("post","t_template" .$id);
		$t_pic = be("post","t_pic" .$id);
		if (isN($t_name)) { echo "信息填写不完整!";exit;}
		if (isN($t_enname))  { echo "信息填写不完整!";exit;}
		if (isN($t_sort)) { $t_sort= $db->getOne("SELECT MAX(t_sort) FROM {pre}vod_topic")+1; }
		if (!isNum($t_sort)) { echo "信息填写不完整!";exit;}
		$db->Update ("{pre}vod_topic",array("t_name", "t_enname","t_template","t_sort","t_pic"),array($t_name,$t_enname,$t_template,$t_sort,$t_pic),"t_id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function main()
{
	global $db;
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}vod_topic";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT * FROM {pre}vod_topic ORDER BY t_sort,t_id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			t_name:{
				required:true,
				stringCheck:true,
				maxlength:64
			},
			t_enname:{
				required:true,
				stringCheck:true,
				maxlength:128
			},
			t_template:{
				required:true,
				maxlength:128
			},
			t_pic:{
				maxlength:254
			},
			t_sort:{
				number:true
			},
			t_des:{
				required:true,
				maxlength:254
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}vod_topic");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#btnEdit").click(function(){
		$("#form1").attr("action","?action=editall");
		$("#form1").submit();
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
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}vod_topic&col=t_id&val='+id);
}
</script>
<table class="tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="10%">编号</td>
	<td>名称</td>
	<td>别称</td>
	<td width="15%">模板</td>
	<td width="15%">图片</td>
	<td width="5%">排序</td>
	<td width="10%">操作</td>
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
		  		$t_id=$row["t_id"];
	?>
    <tr>
	  <td>
	  <input name="t_id[]" type="checkbox" id="t_id" value="<?php echo $t_id?>" /></td>
      <td><?php echo $t_id?></td>
      <td>
      <input type="text" name="t_name<?php echo $t_id?>" value="<?php echo $row["t_name"]?>" size="20"/></td>
	  <td>
	  <input type="text" name="t_enname<?php echo $t_id?>" value="<?php echo $row["t_enname"]?>" size="20"/></td>
	  <td>
	  <input type="text" name="t_template<?php echo $t_id?>" value="<?php echo $row["t_template"]?>" size="20"/></td>
      <td>
      <input type="text" name="t_pic<?php echo $t_id?>" value="<?php echo $row["t_pic"]?>" size="20"/></td>
	  <td>
	  <input name="t_sort<?php echo $t_id?>" type="text" value="<?php echo $row["t_sort"]?>"  size="5"/></td>
      <td>
	  <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>');return false;">修改</a> |
	  <a href="admin_ajax.php?action=del&tab={pre}vod_topic&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td  colspan="8">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" />
	<input type="button" value="批量删除" id="btnDel" class="input"  />
	&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />
	&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />
	</td></tr>
    <tr align="center" >
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_vod_topic.php?page={p}")?>
	</td>
    </tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:500px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}vod_topic" method="post" name="form2" id="form2">
<table class="tb">
	<input id="t_id" name="t_id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="20%">专题名称：</td>
	<td><input id="t_name" size=50 value="" name="t_name">
	</td>
	</tr>
	<tr>
	<td>专题别名：</td>
	<td><input id="t_enname" size=50 value="" name="t_enname" >
	</td>
    </tr>
	<tr>
	<td>模版文件：</td>
	<td><input id="t_template" size=50 value="" name="t_template" >
	</td>
	</tr>
	<tr>
     <td>专题图片：</td>
      <td><input id="t_pic" size=50 value="" name="t_pic" ><br>&nbsp;<iframe src="editor/uploadshow.php?action=vod&id=t_pic" scrolling="no" topmargin="0" width="320" height="24" marginwidth="0" marginheight="0" frameborder="0" align="center"></iframe>
	  </td>
    </tr>
	<tr>
     <td>排序：</td>
      <td><input id="t_sort" size=10 value="" name="t_sort" >
	  </td>
    </tr>
	<tr>
     <td>描述信息：</td>
      <td>
      <TEXTAREA id="t_des" NAME="t_des" ROWS="8" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>
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
unset($rs);
}
?>