<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");

switch($action)
{
	case "save" : save();break;
	case "del" : del();break;
	default : headAdmin ("充值卡管理");main();break;
}
dispseObj();

function save()
{
	global $db;
	$flag=be("all","flag");
	$num=be("all","num");
	$c_id=be("all","c_id");
	$c_money=be("all","c_money");
	$c_point=be("all","c_point");
	
	if ($flag=="edit"){
		$colarr = array("c_money","c_point");
		$valarr = array($c_money,$c_point);
		$where = "c_id=".$c_id;
		$db->Update ("{pre}user_card",$colarr,$valarr,$where);
	}
	else{
		$num = intval($num);
		$colarr = array("c_number","c_pass","c_money","c_point","c_addtime");
		for($i=0;$i<$num;$i++){
			$c_number = getRndStr(10);
			$c_pass = getRndStr(6);
			$c_addtime= date('Y-m-d H:i:s',time());
			$valarr = Array($c_number,$c_pass,$c_money,$c_point,$c_addtime);
			$db->Add ("{pre}user_card",$colarr,$valarr);
		}
	}
	echo "保存完毕";
}

function main()
{
	global $db;
	$pagenum = be("all","page");
	$used = be("all","used");
	$sale = be("all","sale");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	if (!isN($used)){ $where .= " and c_used=".$used;}
	if (!isN($sale)){ $where .= " and c_sale=".$sale;}
	$sql = "SELECT count(*) FROM {pre}user_card where 1=1 ". $where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "select c_id,c_number,c_pass,c_money,c_point,c_addtime,c_usetime,c_user,c_used,c_sale from {pre}user_card WHERE 1=1 ";
	$sql .= $where." limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			c_num:{
				number:true
			},
			c_money:{
				required:true,
				number:true,
				min:1
			},
			c_point:{
				required:true,
				number:true,
				min:1
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}user_card");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#btnAdd").click(function(){
		$("#trnum").css("display","block");
		$('#form2').form('clear');
		$("#flag").val("add");
		$('#win1').window('open'); 
	});
	$("#btnCancel").click(function(){
		location.href= location.href;
	});
	$("#used").change(function(){
		var used = $(this).children('option:selected').val();
		location.href= "admin_user_card.php?used="+used;
	});
	$("#sale").change(function(){
		var used = $(this).children('option:selected').val();
		location.href= "admin_user_card.php?sale="+used;
	});
	$.fn.datebox.defaults.formatter = function(date) {
	var y = date.getFullYear();
	var m = date.getMonth() + 1;
	var d = date.getDate();
	return y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d);
	};
});
function edit(id)
{
	$('#form2').form('clear');
	$("#trnum").css("display","none");
	$("#flag").val("edit");
	$('#win1').window('open');
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}user_card&col=c_id&val='+id);
}
</script>
<table class="tb">
	<tr>
	<td colspan="5" ><strong>过滤条件：</strong>
	<select id="used" name="used">
	<option value="">请选择使用情况</option>
	<option value="0" <?php if($used==0){ echo "selected";}?>>未使用</option>
	<option value="1" <?php if($used==1){ echo "selected";}?>>已使用</option>
	</select>
	&nbsp;<select id="sale" name="sale">
	<option value="">请选择出售情况</option>
	<option value="0" <?php if($sale==0){ echo "selected";}?>>未使用</option>
	<option value="1" <?php if($sale==1){ echo "selected";}?>>已使用</option>
	</select>
	</td>
	</tr>
</table>

<form action="" method="post" id="form1" name="form1">
<table class="tb">
	<tr>
    <td width="4%">&nbsp;</td>
    <td	width="10%">充值卡号</td>
    <td width="10%">密码</td>
    <td width="10%">面值</td>
    <td width="10%">点数</td>
    <td width="10%">出售情况</td>
    <td width="10%">使用情况</td>
    <td width="10%">使用者</td>
    <td width="15%">充值时间</td>
    <td width="10%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="11" >没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$c_id=$row["c_id"];
		  		$c_sale=$row["c_sale"];
		  		$c_used=$row["c_used"];
		  		if ($c_sale==1){ $c_sale="已售出";} else{$c_sale="<font color=red>未出售</font>";}
	  			if ($c_used==1){ $c_used="已使用";} else{$c_used="<font color=red>未使用</font>";}
	  			if ($row["c_user"]>0){
	  				$row1 = $db->getRow("SELECT u_name FROM {pre}user WHERE u_id=".$c_user);
         			$c_user = $row1["u_name"];
         			unset($row1);
	  			}
	?>
	<tr>
    <td><input type="checkbox" name="c_id[]" value="<?php echo $c_id?>"></td>
    <td><?php echo $row["c_number"]?></td>
    <td><?php echo $row["c_pass"]?></td>
    <td><?php echo $row["c_money"]?>元</td>
    <td><?php echo $row["c_point"]?>点</td>
    <td><?php echo $c_sale?></td>
    <td><?php echo $c_used?></td>
    <td><?php echo $c_user?></td>
    <td><?php echo $row["c_usetime"]?></td>
    <td>
	<a href="javascript:void(0)" onclick="edit('<?php echo $c_id?>');return false;">修改</a> |
	  <a href="admin_ajax.php?action=del&tab={pre}user_card&c_id=<?php echo $c_id?>" onClick="return confirm('确定要删除吗?');">删除</a>
	</td>
	</tr>
	<?php
			}
		}
	?>
	<td colspan="11">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'c_id[]')" />
	&nbsp;<input type="button" id="btnDel" value="批量删除" class="input" />
	&nbsp;<input type="button" id="btnAdd" value="添加"  class="input"/>
	</td></tr>
	<tr align="center">
	<td colspan="11">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_user_card.php?page={p}&used=$used&sale=$sale")?>
	</td>
	</tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="width:400px;padding:5px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="?action=save&tab={pre}user_card" method="post" name="form2" id="form2">
<table class="tb">
	<input id="c_id" name="c_id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr id="trnum">
	<td width="30%">添加数量：</td>
	<td><input id="num" name="num" size=20 value="">个
	</td>
	</tr>
	<tr>
	<td>充值卡面值：</td>
	<td><input id="c_money" name="c_money" size=20 value="">(元)
	</td>
	</tr>
	<tr>
	<td>充值卡点数：</td>
	<td><input id="c_point" name="c_point" size=20 value="">(点)
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