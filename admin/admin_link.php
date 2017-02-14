<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "last" : moveTolast();break;
	case "next" : moveTonext();break;
	default : headAdmin ("友情链接管理"); main();break;
}
dispseObj();

function editall()
{
	global $db;
	$l_id = be("arr","l_id");
	
	$ids = explode(",",$l_id);
	
	foreach($ids as $id){
		$l_name = be("post","l_name" .$id);
		$l_sort = be("post","l_sort".$id);
		$l_url = be("post","l_url" .$id);
		if (isN($l_name)){ echo "名称不能为空"; exit;}
		if (isN($l_url)){ echo "地址不能为空"; exit;}
		if (isN($l_sort)){ $l_sort= $db->getOne("SELECT MAX(l_sort) FROM {pre}link"); }
		if (!isNum($l_sort)){echo "排序号不能为空"; exit;}
		$db->Update ("{pre}link",array("l_name","l_url", "l_sort"),array($l_name,$l_url,$l_sort),"l_id=".$id);
	}
	echo "修改完毕";
}

function moveToLast()
{
	global $db;
	$l_id = be("get","l_id");
	$CurSort = $db->getOne("SELECT l_sort FROM {pre}link WHERE l_id = " . $l_id);
	$Lessthan = $db->getOne("SELECT COUNT(*) FROM {pre}link WHERE l_sort < " . $CurSort);
	
	if ($Lessthan>0){
	    $l_sort= $db->getOne("SELECT top 1 l_sort FROM {pre}link  WHERE l_sort<".$CurSort." ORDER BY l_sort DESC");
	    $db->Update ("{pre}link" ,array("l_sort"),array($l_sort-1),"l_id=".$l_id);
	}
	else{
		$db->Update ("{pre}link",Array("l_sort"),Array($CurSort-1),"l_id=".$l_id);
	}
	redirect ( getReferer() );
}

function moveToNext()
{
	global $db;
	$l_id = be("get","l_id");
	$CurSort = $db->getOne("SELECT l_sort FROM {pre}link WHERE l_id = " . $l_id);
	$Lessthan = $db->getOne("SELECT COUNT(*) FROM {pre}link WHERE l_sort > " . $CurSort);
	
	if ($Lessthan>0){
		$l_sort=$db->getOne("select top 1 l_sort FROM {pre}link  WHERE l_sort>".$CurSort." ORDER BY l_sort DESC");
		$db->Update ("{pre}link" ,array("l_sort"),array($l_sort+1),"l_id=".$l_id);
	}
	else{
		$db->Update ("{pre}link",array("l_sort"),array($CurSort+1),"l_id=".$l_id);
	}
	redirect ( getReferer() );
}

function main()
{
	global $db;
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}link";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT l_id,l_name,l_type,l_url,l_sort FROM {pre}link ORDER BY l_sort,l_id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			l_name:{
				required:true,
				stringCheck:true,
				maxlength:64
			},
			l_url:{
				required:true,
				maxlength:254
			},
			l_logo:{
				maxlength:254
			},
			l_sort:{
				number:true
			},
			l_type:{
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}link");
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
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}link&col=l_id&val='+id);
}
</script>
<form action="" method="post" name="form1" id="form1">
<table class="tb">
	<tr>
	<td width="5%">&nbsp;</td>
	<td>友情链接名称</td>
	<td width="25%">地址</td>
	<td width="10%">类型</td>
	<td width="10%">排序</td>
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
		  		$id=$row["l_id"];
	?>
    <tr>
	  <td><input name="l_id[]" type="checkbox" id="l_id" value="<?php echo $id?>" /></td>
      <td><input type="text" name="l_name<?php echo $id?>" value="<?php echo $row["l_name"]?>" size="30"/></td>
	  <td><input name="l_url<?php echo $id?>" type="text" value="<?php echo $row["l_url"]?>"  size="30"/></td>
      <td><?php if($row["l_type"] =="font"){echo "文字链接";} else{echo "图片链接";}?></td>
	  <td><input name="l_sort<?php echo $id?>" type="text" value="<?php echo $row["l_sort"]?>"  size="5"/></td>
      <td>
	  <a href="admin_link.php?action=last&l_id=<?php echo $id?>">上移</a> |
	  <a href="admin_link.php?action=next&l_id=<?php echo $id?>">下移</a> |
	  <a href="javascript:void(0)" onclick="edit('<?php echo $id?>');return false;">修改</a> |
	  <a href="admin_ajax.php?action=del&tab={pre}link&l_id=<?php echo $id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td colspan="6">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'l_id[]')" />
	&nbsp;<input type="button" id="btnDel" value="批量删除" class="input" />
	&nbsp;<input type="button" id="btnEdit" value="批量修改" class="input"/>
	&nbsp;<input type="button" id="btnAdd" value="添加"  class="input"/>
	</td></tr>
    <tr align="center">
      <td colspan="6">
	   <?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_link.php?page={p}")?>
       </td>
    </tr>
</table>
</form>
<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:400px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}link" method="post" name="form2" id="form2">
<table class="tb">
	<input id="l_id" name="l_id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="20%">网站名称：</td>
	<td><input id="l_name" size=50 value="" name="l_name">
	</td>
	</tr>
	<tr>
	<td>网站地址：</td>
	<td><input id="l_url" size=50 value="http://" name="l_url" >
	</td>
	</tr>
	<tr>
	<td>链接类型：</td>
	<td>
	<select id="l_type" name="l_type">
	<option value="font">文字链接</option>
	<option value="pic">图片链接</option>
	</select>
	</td>
	</tr>
	<tr>
	<td>logo地址：</td>
	<td>
	<input id="l_logo" size="50" value="http://" name="l_logo" >
	</td>
	</tr>
	<tr>
	<td>排序号：</td>
	<td><input id="l_sort" size="10" value="" name="l_sort" >
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