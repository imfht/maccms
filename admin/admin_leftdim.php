<?php
require_once ("admin_conn.php");
chkLogin();
$action = be("get","action");
switch(trim($action))
{
	case "save" : save();break;
	default : headAdmin ("自定义菜单管理") ; main();break;
}
dispseObj();

function save()
{
	$menudiy = be("post","menudiy");
	fwrite(fopen("../inc/dim_menu.txt","wb"),$menudiy);
	echo "修改插件菜单成功";
}

function main()
{
	$fc = file_get_contents("../inc/dim_menu.txt");
?>
<script language="javascript">
$(document).ready(function(){
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        	top.location.href = top.location.href;
	        });
	    }
	});
});
</script>
<form action="?action=save" method="post" id="form1" name="form1">
<table class="tb">
<tr class="thead"><th colspan="2">自定义快捷菜单</th></tr>
<tr><td width="25%">1.格式：菜单名称,菜单链接地址</td>
<td width="75%">2.每个快捷菜单各占一行</td>
</tr>
<tr>
	<td colspan="2">
	<textarea id="menudiy" name="menudiy" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="20"><?php echo $fc?></textarea>
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