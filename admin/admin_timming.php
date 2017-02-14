<?php
require_once ("admin_conn.php");
chkLogin();
headAdmin("定时任务设置");

$xmlpath = "../inc/timmingset.xml";
$doc = new DOMDocument();
$doc -> formatOutput = true;
$doc -> load($xmlpath);
$xmlnode = $doc -> documentElement;
$nodes = $xmlnode->getElementsByTagName("timming");
?>
<script language="javascript">
$(document).ready(function(){
	var app_timming=<?php echo app_timming?>;
	$("#form1").validate({
		rules:{
		name:{
				required:true,
				stringCheck:true,
				maxlength:40
			},
			des:{
				required:true,
				stringCheck:true,
			maxlength:40
			},
			file:{
				required:true,
				maxlength:20
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
	$("#btnCancel").click(function(){
		location.href= "?action=main";
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$("#flag").val("add");
		$("#name").attr("readonly",false);
		$('#win1').window('open');
	});
});
function edit(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$("#name").attr("readonly",true);
	$('#win1').window('open');
	$.get('admin_ajax.php?action=getinfoxml&tab=timming&val='+ encodeURI(id) + "&rnd=" + Math.random(), function (obj) {
		$('#name').val(obj.name);
		$('#des').val(obj.des);
		$('#file').val(obj.file);
		$('#status').val(obj.status);
		$('#paramets').val(obj.paramets);
		$('#form2 input[name="weeks[]"]').each(function(i){
			if(obj.weeks.indexOf( $(this).val()) >-1 ){
				$(this).attr("checked",true);
			}
		});
		obj.hours =  "," + obj.hours + ",";
		$('#form2 input[name="hours[]"]').each(function(i){
			if( obj.hours.indexOf(  ","+ $(this).val()+ "," ) >-1 ){
				$(this).attr("checked",true);
			}
		});
	}, 'json');
}
</script>
<form action="" method="post" name="form1" id="form1">
<table class="tb">
    <tr>
      <td width="20%">任务名称</td>
      <td>任务描述</td>
      <td width="10%">任务状态</td>
      <td width="15%">最后运行时间</td>
      <td width="15%">操作</td>
    </tr>
    <?php 
    if(count($nodes)==0){
    	echo '<tr><td align="center" colspan="6">没有任何记录!</td></tr>';
	}
	else{
    	foreach($nodes as $node){
    		$tname = $node->getElementsByTagName("name")->item(0)->nodeValue;
    		$tdes = $node->getElementsByTagName("des")->item(0)->nodeValue;
    		$tstatus = $node->getElementsByTagName("status")->item(0)->nodeValue;
    		$truntime = $node->getElementsByTagName("runtime")->item(0)->nodeValue;
    		$tfile = $node->getElementsByTagName("file")->item(0)->nodeValue;
    		$tparamets = $node->getElementsByTagName("paramets")->item(0)->nodeValue;
    	?>
    	<tr>
	  	<td><?php echo $tname?></td>
      	<td><?php echo $tdes?></td>
      	<td><?php if ($tstatus=="1"){ echo "<font color=green>启用</font>";} else{ echo "<font color=red>禁用</font>";}?></td>
      	<td><?php echo $truntime?></td>
      	<td>
      	<a target="_blank" href="../inc/<?php echo$tfile?>?<?php echo replaceStr($tparamets,"&amp;","&")?>">测试</a> |
	    <a href="javascript:void(0)" onclick="edit('<?php echo $tname?>');return false;">修改</a> |
	    <a href="admin_ajax.php?action=delxml&tab=timming&val=<?php echo $tname?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
        </tr>
    <?php
    	}
    }
    unset($xmlnode);
    unset($nodes);
    unset($doc);
    ?>
    <tr><td colspan="6">
    &nbsp;<input type="button" value="添加" class="input" id="btnAdd" />
    &nbsp;<?php if (app_timming==0){ echo "<font color=red>站点配置中定时任务执行未开启</font>"; }?>
    </td></tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:600px;" closed="true" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=savexml&tab=timming" method="post" name="form2" id="form2">
<table class="tb">
	<input type="hidden" id="flag" name="flag" value="">
    <tr>
      <td width="20%">任务名称：</td>
      <td>
      <input id="name" name="name" size="50" value="">
	  </td>
    </tr>
    <tr>
      <td>任务描述：</td>
      <td>
      <input id="des" name="des" size="50" value="">
	  </td>
    </tr>
    <tr>
	<td>任务状态：</td>
	<td>
	<select id="status" name="status">
	<option value="1">启用</option>
	<option value="0">禁用</option>
	</select>
	</td>
	</tr>
    <tr>
      <td>执行文件：</td>
      <td>
      <input id="file" name="file" size="50" value="">
      <br>&nbsp;不能包含路径，程序脚本必须存放于 /inc/目录中
	  </td>
    </tr>
    <tr>
      <td>执行参数：</td>
      <td>
      <input id="paramets" name="paramets" size="70" value="" >
      <br>&nbsp;可以留空，格式:action=timming&id=1
	  </td>
    </tr>
    <tr>
      <td>采集周期选择：</td>
      <td>
      	&nbsp;<input type="checkbox" name="weeks[]" class="checkbox" value="1">周一
        &nbsp;<input type="checkbox" name="weeks[]" class="checkbox" value="2">周二
      	&nbsp;<input type="checkbox" name="weeks[]" class="checkbox" value="3">周三
      	&nbsp;<input type="checkbox" name="weeks[]" class="checkbox" value="4">周四
      	&nbsp;<input type="checkbox" name="weeks[]" class="checkbox" value="5">周五
      	&nbsp;<input type="checkbox" name="weeks[]" class="checkbox" value="6">周六
      	&nbsp;<input type="checkbox" name="weeks[]" class="checkbox" value="0">周日
       </td>
    </tr>
    <tr>
      <td>采集时间选择：</td>
      <td>
      	&nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="0">00
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="1">01
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="2">02
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="3">03
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="4">04
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="5">05
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="6">06
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="7">07
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="8">08<br/>
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="9">09
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="10">10
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="11">11
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="12">12
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="13">13
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="14">14
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="15">15
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="16">16<br/>
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="17">17
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="18">18
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="19">19
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="20">20
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="21">21
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="22">22
            &nbsp;<input type="checkbox" name="hours[]" class="checkbox" value="23">23
       </td>
    </tr>
    <tr align="center">
      <td colspan="2"><input class="inputbut" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel">
      &nbsp;</td>
    </tr>
</table>
</form>
</div>
</body>
</html>