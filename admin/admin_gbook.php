<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "audit" : audit();break;
	default : headAdmin ("留言管理"); main();break;
}
dispseObj();

function audit()
{
	global $db;
	$g_id = be("arr","g_id");
	if (!isN($g_id)){
		$db->Update ("{pre}gbook",array("g_audit"),array("1") ,"g_id in (". $g_id . ")");
	}
	echo "审核完毕";
}

function main()
{
	global $db;
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1; } else { $pagenum=intval($pagenum); }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$sql = "SELECT count(*) FROM {pre}gbook";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT g_id,g_vid,g_audit,g_name,g_content,g_ip,g_reply,g_time,g_replytime FROM {pre}gbook ORDER BY g_id DESC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
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
	$("#btnCancel").click(function(){
		location.href= location.href;
	});
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}gbook");
			$("#form1").submit();
		}
		else{return false}
	});
	$("#btnAudit").click(function(){
		$("#form1").attr("action","admin_gbook.php?action=audit");
		$("#form1").submit();
	});
});
function reply(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$('#win1').window('open');
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}gbook&col=g_id&val='+id);
}
</script>
	<?php
		if($nums==0){
	?>
	<div align="center">没有任何记录!</div>
	<?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$g_id = $row["g_id"];
		  		$g_content=$row["g_content"];
		  		$g_content = regReplace($g_content, "\[em:(\d{1,})?\]", "<img src=\"../images/face/$1.gif\" border=0/>");
	?>
<form id="form1" name="form1" method="post">
 <div style="width:100%; border:1px solid #B5D6E6; margin-top:10px;">
	 <div style="width:100%; height:30px; line-height:30px; font-size:12px; background:#EEF7FE">
		 <span style="float:left;color:#185691;">
		 	<input type="checkbox" name="g_id[]" value="<?php echo $g_id?>" class="checkbox">
		 	&nbsp;昵称:<strong><?php echo $row["g_name"]?></strong>
		 	&nbsp;&nbsp;IP:<?php echo $row["g_ip"]?>
		 	&nbsp;&nbsp;<?php if ($row["g_audit"]==1){?><font color=green>已审核</font><?php }else{?><font color=red>未审核</font><?php }?>
		 </span>
		 <span style="float:right;text-align:right; font-size:12px;">
		 	发表于:<font color="red"><?php echo $row["g_time"]?></font>
		 </span>
	 </div>
	 <div style="width:95%; margin:0 auto; height:60px; font-size:12px; line-height:20px; border-bottom:1px solid #CCC">
	 	留言内容：<?php echo $g_content?>
	 	<br>
	 	<font color="red">回复内容：</font><?php echo $row["g_reply"]?>
	 </div>
	 <div style="width:100%; height:20px; text-align:right;font-size:12px; line-height:20px;">
	 	&nbsp;&nbsp;[<a style="cursor:pointer" onclick="reply(<?php echo $g_id?>)">
	 	回复</a>]&nbsp;&nbsp;[<a href="admin_ajax.php?action=del&tab={pre}gbook&g_id=<?php echo $g_id?>" onClick="return confirm('确定要删除吗?');">删除</a>]&nbsp;&nbsp;
	 </div>
 </div>
	<?php
			}
		}
	?>
<table class="tb" style="margin-top:5px;">
<tr><td>
全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'g_id[]');"/>&nbsp;
<input type="button" id="btnDel" value="批量删除" class="input">
<input type="button" id="btnAudit" value="批量审核" class="input">
</td></tr>
<tr align="center">
<td>
<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_gbook.php?page={p}")?>
</td>
</tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:400px;" closed="true" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}gbook" method="post" name="form2" id="form2">
<table class="tb">
	<input id="g_id" name="g_id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="20%" >回复内容：</td>
	<td><textarea id="g_reply" rows="8" style="width:200px;" value="" name="g_reply"></textarea>
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