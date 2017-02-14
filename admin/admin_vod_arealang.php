<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "save" : save();break;
	default : headAdmin ("视频地区语言管理");main();break;
}
dispseObj();

function save()
{
	$areastr = be("post","areastr");
	$langstr = be("post","langstr");
	fwrite(fopen("../inc/vodarea.txt","wb"),$areastr);
	fwrite(fopen("../inc/vodlang.txt","wb"),$langstr);
	updateCacheFile();
	echo "修改完毕";
}

function main()
{
	$fc1 = file_get_contents("../inc/vodarea.txt");
	$fc2 = file_get_contents("../inc/vodlang.txt");
?>
<script type="text/javascript">
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
	});
</script>
<form action="?action=save" method="post" id="form1" name="form1">
<table class="tb">
<tr class="thead"><th colspan="2">自定义地区和语言 ->>> 1.每个各占一行;2.不要有多余的空行</th></tr>
<tr><td width="50%">地区</td>
<td width="50%">语言</td>
</tr>
<tr>
	<td>
	<textarea id="areastr" name="areastr" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc1?></textarea>
	</td>
	<td>
	<textarea id="langstr" name="langstr" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc2?></textarea>
	</td>
	</tr>
	<tr>
	<td align="center" colspan="2"> <input type="submit" id="btnSave" name="btnSave" value="保存" class="input" /> </td>
	</tr>
</table>
</form>
</body>
</html>
<?php
}
?>