<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
headAdmin ("会员管理");main();
dispseObj();

function main()
{
	global $db;
	$pagenum = be("all","page");
	$group = be("get","group");
	$keyword = be("get","keyword");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	if (!isN($group)){ 
		$where .= " and u_group = ".$group ." ";
	}
	if(!isN($keyword)){
		$where .= " and u_name like '%".$keyword."' ";
	}
	$sql = "SELECT count(*) FROM {pre}user where 1=1 ".$where ;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT u_id,u_name,u_group,u_qq,u_email,u_status,u_points,u_tj,u_loginnum,u_logintime,u_ip,u_flag FROM {pre}user where 1=1 ";
	$sql .= $where . " ORDER BY u_id DESC  limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			u_name:{
				required:true,
				stringCheck:true,
				maxlength:32
			},
			u_password:{
				maxlength:32
			},
			u_flag:{
				required:true
			},
			u_group:{
				required:true
			},
			u_qq:{
				number:true,
				maxlength:16
			},
			u_email:{
				email:true,
				maxlength:32
			},
			u_points:{
				number:true
			},
			u_status:{
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
	$.fn.datebox.defaults.formatter = function(date) {
	var y = date.getFullYear();
	var m = date.getMonth() + 1;
	var d = date.getDate();
	return y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d);
	};
	$('#u_starttime').datebox();
	$('#u_endtime').datebox();
});
function showflag(flag){
	if (flag==2){
		$("#flag2").show();
		$("#flag1").hide();
	}
	else if (flag==1){
		$("#flag2").hide();
		$("#flag1").show();
	}
	else{
		$("#flag2").hide();
		$("#flag1").hide();
	}
}
function edit(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$('#win1').window('open');
	$.get('admin_ajax.php?action=getinfo&tab={pre}user&col=u_id&val='+id,function(obj){
		$("#uid").val(obj.u_id);
		$("#u_name").val(obj.u_name);
		$("#u_qq").val(obj.u_qq);
		$("#u_email").val(obj.u_email);
		$("#u_points").val(obj.u_points);
		$("#u_phone").val(obj.u_phone);
		$("#u_question").val(obj.u_question);
		$("#u_answer").val(obj.u_answer);
		
		if(obj.u_status==1){
			$("#u_status").get(0).options[0].selected = true;
		}
		else{
			$("#u_status").get(0).options[1].selected = true;
		}
		if(obj.u_flag==1){
			$("#u_flag").get(0).options[1].selected = true;
			$('#u_starttime').datebox('setValue',obj.u_start);
			$('#u_endtime').datebox('setValue',obj.u_end);
			$("#flag1").show();
			$("#flag2").hide();
		}
		else if(obj.u_flag==2){
			$("#u_flag").get(0).options[2].selected = true;
			$("#u_startip").val(obj.u_start);
			$("#u_endip").val(obj.u_end);
			$("#flag2").show();
			$("#flag1").hide();
		}
		else{
			$("#u_flag").get(0).options[0].selected = true;
		}
		$("#u_group option").each(function(i){
       		if($(this).val() == obj.u_group){
            	$("#u_group").get(0).options[i].selected = true;
            }
        });
	},"json");
}
function filter(){
	var url = "admin_user.php?keyword="+encodeURI($("#keyword").val())+"&group="+$("#group").val();
	window.location.href=url;
}
</script>
<table class="tb">
	<tr>
	<td colspan="5" ><strong>会员组查看：</strong>
	<select id="group" name="group">
	<option value="">全部会员</option>
	<?php echo makeSelect("{pre}user_group","ug_id","ug_name","","","&nbsp;|&nbsp;&nbsp;",$group)?>
	</select>
	&nbsp;会员名：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">
	</td>
	</tr>
</table>

<form action="" method="post" id="form1" name="form1">
<table class="tb">
	<tr>
	<td width="4%">&nbsp;</td>
	<td>会员</td>
	<td width="10%">计费类型</td>
	<td width="15%">会员组</td>
	<td width="10%">积分</td>
	<td width="10%">推荐数</td>
	<td width="15%">是否锁定</td>
	<td width="15%">最后登陆</td>
	<td width="15%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="9" >没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$u_id=$row["u_id"];
	?>
    <tr>
	  <td><input name="u_id[]" type="checkbox" value="<?php echo $u_id?>" /></td>
      <td><?php echo $row["u_name"]?></td>
      <td>
	  <?php echo getUserFlag($row["u_flag"])?>
      </td>
      <td><?php
        $row1= $db->getRow("SELECT ug_name FROM {pre}user_group WHERE ug_id=".$row["u_group"]);
		if($row1){
			echo  $row1["ug_name"];
		}
		else{
			echo "未知";
		}
		unset($row1);
      ?></td>
      <td><?php echo $row["u_points"]?></td>
      <td><?php echo $row["u_tj"]?></td>
      <td><?php if ($row["u_status"]==1){ echo "<font color=green>启用</font>";} else{ echo "<font color=red>禁用</font>";}?></td>
      <td><?php echo $row["u_logintime"]?></td>
      <td><a href="javascript:void(0)" onclick="edit('<?php echo $u_id?>');return false;">修改</a> |
	  <a href="admin_ajax.php?action=del&tab={pre}user&u_id=<?php echo $u_id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td colspan="9">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'u_id[]')" />
	&nbsp;<input type="button" id="btnDel" value="批量删除" class="input" />
	&nbsp;<input type="button" id="btnAdd" value="添加"  class="input"/>
	</td></tr>
    <tr align="center">
	<td colspan="9">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_user.php?page={p}&group=$group&keyword=" . urlencode($keyword) )?>
	</td>
	</tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:400px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}user" method="post" name="form2" id="form2">
<table class="tb">
	<input id="uid" name="uid" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
    <tr>
	<td width="30%">名称：</td>
	<td><INPUT type="text" id="u_name" size=20 value="" name="u_name">
	</td>
    </tr>
    <tr>
	<td>密码：</td>
	<td><INPUT id="u_password" type="password" size=20 value="" name="u_password">
	</td>
    </tr>
    <tr>
	<td>提示问题：</td>
	<td><INPUT type="text" id="u_question" size=30 value="" name="u_question">
	</td>
    </tr>
    <tr>
	<td>提示回答：</td>
	<td><INPUT type="text" id="u_answer" size=30 value="" name="u_answer">
	</td>
    </tr>
    <tr>
	<td>状态：</td>
	<td>
	<select id="u_status" name="u_status" style="width:100px;">
	<option value=1>启动</option>
	<option value=0>锁定</option>
	</select>
	</td>
    </tr>
    <tr>
	<td>计费类型：</td>
	<td>
	<select name="u_flag" id="u_flag" style="width:100px;" onChange="showflag(this.options[this.selectedIndex].value)">
	<option value="0" >计点</option>
	<option value="1" >包时</option>
	<option value="2" >网吧</option>
	</select> 
	</td>
    </tr>
	<tr id="flag1" style="display:none">
	<td>设置时间：</td>
	<td>
	起始时间:<INPUT type="text" id="u_starttime" size=20 value="" name="u_starttime"><br>
	截止时间:<INPUT type="text" id="u_endtime" size=20 value="" name="u_endtime" >
	</td>
	</tr>
	<tr id="flag2" style="display:none">
	<td>设置IP段：</td>
	<td>
	起始IP:<INPUT type="text" id="u_startip" size=20 value="" name="u_startip"><br>
	截止IP:<INPUT type="text" id="u_endip" size=20 value="" name="u_endip">
	</td>
    </tr>
    <tr>
	<td>会员组：</td>
	<td>
	<select id="u_group" name="u_group" style="width:100px;">
	<option value="">请选择会员组</option>
	<?php echo makeSelect("{pre}user_group","ug_id","ug_name","","","&nbsp;|&nbsp;&nbsp;","")?>
	</select>
	</td>
    </tr>
	<tr>
	<td>积分：</td>
	<td><INPUT id="u_points" type="text" size=10 value="0" name="u_points">
	</td>
    </tr>
	<tr>
	<td>QQ：</td>
	<td><INPUT id="u_qq" type="text" size=20 value="" name="u_qq">
	</td>
    </tr>
	<tr>
	<td>email：</td>
	<td><INPUT id="u_email" type="text" size=20 value="" name="u_email">
	</td>
    </tr>
    <tr>
	<td>电话：</td>
	<td><INPUT id="u_phone" type="text" size=20 value="" name="u_phone">
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