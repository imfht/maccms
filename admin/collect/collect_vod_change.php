<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("视频分类转换管理");

switch($action)
{
	case "save": save();break;
	case "del": del();break;
	default: main();
}

function save()
{
	global $db;
	$c_id = be("arr","c_id");
	
	$ids = explode(",",$c_id);
	if (!isN($c_id)){
		foreach($ids as $id){
			$c_name=be("post","c_name".$id);
			$c_toid=be("post","c_toid".$id);
			$c_pid= be("post","c_pid".$id);
			$sql="update {pre}cj_change set c_name='".$c_name."',c_toid='".$c_toid."',c_pid='".$c_pid."' where c_id=" .$id;
			$db->query($sql);
		}
	}
	else{
		$c_name=be("post","c_name");
		$c_toid=be("post","c_toid");
		$c_pid= be("post","c_pid");
		$c_type=0;
		$sql="insert {pre}cj_change (c_name,c_toid,c_pid,c_type,c_sys) values('".$c_name."','".$c_toid."','".$c_pid."','".$c_type."','0')";
		
		$db->query($sql);
	}
	redirect (getReferer());
}

function del()
{
	global $db;
	$c_id = be("arr","c_id");
	$ids = explode(",",$c_id);
	foreach($ids as $id){
		$db->query( "delete from {pre}cj_change where c_id = ".$id);
	}
	redirect (getReferer());
}

function main()
{
	global $db;
	$pagenum = be("get","page");
	if (isN($pagenum) || !isNum($pagenum)){ $pagenum = 1; }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$pagenum = intval($pagenum);
	
	$project = be("get","{pre}cj_vod_projects");
	$sql="select * from {pre}cj_change where c_sys=0 " ;
	if ($project!= "") {
		$sql = $sql . " and c_pid = " . $project ;
	}
	
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount=ceil($nums/app_pagenum);//总页数
	$sql = $sql ." limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			name:{
				required:true,
				maxlength:32
			},
			toid:{
				required:true
			},
			pid:{
				required:true
			}
		}
	});
	
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","collect_vod_change.php?action=del");
			$("#form1").submit();
		}
		else{return false}
	});
	$("#btnEdit").click(function(){
		if(confirm('确定要保存修改吗')){
			$("#form1").attr("action","collect_vod_change.php?action=save");
			$("#form1").submit();
		}
		else{return false}
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$('#win1').window('open'); 
	});
	$("#btnCancel").click(function(){
		location.href = location.href ;
	});	
});
</script>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td>
		菜单：<a href="collect_vod_manage.php?action=">采集规则列表</a> | <a href="collect_vod_change.php">分类转换</a> | <a href="collect_vod_filters.php">信息过滤</a>  
	&nbsp; 按项目查看：<select onchange=javascript:window.location.href=this.options[this.selectedIndex].value>
	<option value="?action=">所有转换</option>
	<option value="?action=&{pre}cj_vod_projects=0" <?php if ($project=="0"){ echo "selected";} ?>>全局转换</option>
	<?php echo makeSelect("{pre}cj_vod_projects","p_id","p_name","","collect_vod_change.php","&nbsp;|&nbsp;&nbsp;",$project)?>
	</select>
	</td>
	</tr>
</table>

<form id="form1" name="form1" method="post">
<table class="tb">
<tr>
	<td width="10%" >ID</td>
	<td width="30%" >采集分类</td>
	<td width="30%" >系统分类</td>
	<td width="30%" >所属项目</td>
</tr>
	<?php
	if (!$rs){
	?>
	<tr><td align="center" colspan="5">没有任何记录!</td></tr>
    <?php
	}
	else{
		$i=0;
	  	while ($row = $db ->fetch_array($rs))
	  	{
	?>
    <tr>
	<td><input name="c_id[]" type="checkbox" value="<?php echo $row["c_id"]?>" /><?php echo  $row["c_id"] ?></td>
	<td><input name="c_name<?php echo $row["c_id"]?>" type="text" value="<?php echo  $row["c_name"] ?>" size="40"></td>
	<td>
	<select name="c_toid<?php echo $row["c_id"]?>">
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$row["c_toid"]);?>
	</select>
	</td>
	<td>
	<select name="c_pid<?php echo $row["c_id"]?>">
	<option value="0">全局过滤项目</option>
	<? echo makeSelect("{pre}cj_vod_projects","p_id","p_name","","","&nbsp;|&nbsp;&nbsp;",$row["c_pid"])?>
	</select>
	</td>
	</tr>
<?php
		$i=$i+1;
		}
	}
?>
	<tr>
	<tr>
	<td colspan="5">
	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'c_id[]');"/>&nbsp;
    <input type="button" id="btnDel" value="批量删除" class="input">
	<input type="button" id="btnEdit" value="批量修改" class="input">
	<input type="button" id="btnAdd" value="添加转换" class="input">
	</td>
	</tr>
	<th colspan="5" align=center>
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_vod_change.php?page={p}&{pre}cj_vod_projects=".$project."") ?>
    </th>
	</tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:400px;" closed="true" minimizable="false" maximizable="false">
<form action="?action=save" method="post" id="form2" name="form2">
<table class="tb">
<tr>
	<td>
	采集分类：
	<input name="c_name" type="text" size="30">
	</td>
</tr>
<tr>
	<td>
	目标分类：
	<select name="c_toid" style="width:180px;">
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","")?>
	</select>
	</td>
</tr>
<tr>
	<td>所属项目：
	<select name="c_pid" style="width:180px;">
	<option value="0">全局过滤项目</option>
	  	<?php echo makeSelect("{pre}cj_vod_projects","p_id","p_name","","","&nbsp;|&nbsp;&nbsp;","") ?>
	</select>
	</td>
</tr>
<tr>
	<td align=center>
	<input class="input" type="submit" value="保存" id="btnSave">
	<input class="input" type="button" value="返回" id="btnCancel"> 
	</td>
</tr>
</table>
</form>
</div>
<?php
}