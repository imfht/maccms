<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");


switch($action)
{
	case "add" : 
	case "edit" : headAdminCollect ("文章自定义采集项目编辑"); edit();break;
	case "save":
	case "savecs" : save();break;
	case "del" : del();break;
	case "copy" : copynew();break;
	case "delall" : delall();break;
	case "export" : export(); break;
	case "upexpsave" : upexpsave(); break;
	case "getcode" : getcode();break;
	case "breakpoint" : breakpoint(); break;
	default :  clearSessionart();headAdminCollect ("文章自定义采集项目编辑");main();break;
}

function export()
{
	global $db;
	$p_id= be("get","p_id");
	$fields = $db->getTableFields(app_dbname,"{pre}cj_art_projects");
	$colsnum = mysql_num_fields($fields);
	$row = $db->getRow("select * from {pre}cj_art_projects where p_id='".$p_id."'");
	$result="";
	$fileName= $row["p_name"];
	for ($i = 0; $i < $colsnum; $i++) {
		$colname = mysql_field_name($fields, $i);
		$result .= "<".$colname.">".$row[$colname]."</".$colname.">"."\r\n";
	} 
	unset($row);
	$filePath = "../../upload/export/". iconv("UTF-8", "GBK", $fileName) .".txt";
	fwrite(fopen($filePath,"wb"),$result);
	redirect ("collect_down.php?file=".$fileName);
}

function upexpsave()
{
	global $db;
	$str = file_get_contents($_FILES['file1']['tmp_name']);
	$labelRule = buildregx("<(p_[\s\S]*?)>(.*?)</(p_[\s\S]*?)>","is");
	preg_match_all($labelRule,$str,$iar);
	$arlen=count($iar[1]);
	$in1="";
	$in2="";
	$rc=false;
	for($m=0;$m<$arlen;$m++){
		if($iar[1][$m] !="p_id"){
			if ($rc){  $in1 .= ","; $in2 .= ","; }
		 	$in1 .= $iar[1][$m] ;
		 	$in2 .= "'". replaceStr($iar[2][$m],"'","\'") . "'";
		 	$rc=true;
		}
	}
	$sql = "insert into {pre}cj_art_projects (".$in1.") values(".$in2.")";
	
	$status = $db->query($sql);
	if($status){
		showmsg ("导入规则成功!","collect_art_manage.php");
	}
	else{
		alert("导入失败，请检查规则是否正确!");
	}
}

function breakpoint()
{
	echo gBreakpoint("../../upload/artbreakpoint") . "正在载入断点续传数据，请稍后......";
	exit;
}

function copynew()
{
	global $db;
	$p_id=be("get","p_id");
    $sql = "INSERT INTO  {pre}cj_art_projects(p_name, p_coding, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_collecorder, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_authortype, p_authorstart, p_authorend, p_titletype, p_titlestart, p_titleend, p_timestart, p_timeend, p_typestart, p_typeend, p_contentstart, p_contentend, p_hitsstart, p_hitsend, p_cpagetype, p_cpagecodestart, p_cpagecodeend, p_cpagestart, p_cpageend) SELECT p_name, p_coding, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_authortype, p_authorstart, p_authorend, p_titletype, p_titlestart, p_titleend, p_timestart, p_timeend, p_typestart, p_typeend, p_contentstart, p_contentend, p_hitsstart, p_hitsend, p_cpagetype, p_cpagecodestart, p_cpagecodeend, p_cpagestart, p_cpageend FROM  {pre}cj_art_projects WHERE p_id =" .$p_id;
	$db->query($sql);
    showmsg ("复制采集栏目成功！","collect_art_manage.php");
}

function del()
{
	global $db;
	$p_id=be("get","p_id");
    $sql= "delete from {pre}cj_art_projects WHERE p_id=".$p_id;
    $db->query($sql);
    showmsg ("采集项目删除成功！","collect_art_manage.php");
}

function delall()
{
	global $db;
    $ids=be("arr","p_id");
    if (!isN($ids)){
	  $db->query("delete from {pre}cj_art_projects WHERE p_id in (".$ids.")");
	}
    showmsg ("采集项目删除成功！","collect_art_manage.php");
}

function getcode()
{
	$charset = be("get","charset");
	$url = be("get","url");
	$html = getPage($url,$charset);
	echo $html;
}

function edit()
{
	global $db;
	$p_id=be("get","p_id");
	if(!isN($p_id)){
		$sql="select * from {pre}cj_art_projects where p_id = ".$p_id;
		$row = $db->getRow($sql);
		$p_name = $row["p_name"];
		$p_coding = $row["p_coding"]; $p_coding = strtolower($p_coding);
		$p_pagetype = $row["p_pagetype"];
		$p_url = $row["p_url"];
		$p_pagebatchurl = $row["p_pagebatchurl"];
		$p_manualurl = $row["p_manualurl"];
		$p_pagebatchid1 = $row["p_pagebatchid1"];  $p_pagebatchid1 = intval($p_pagebatchid1);
		$p_pagebatchid2 = $row["p_pagebatchid2"];  $p_pagebatchid2 = intval($p_pagebatchid2);
		$p_script = $row["p_script"];
		$p_showtype = $row["p_showtype"];
		$p_collecorder = $row["p_collecorder"];
		$p_savefiles = $row["p_savefiles"];
		$p_ontime = $row["p_ontime"];
		$p_listcodestart = $row["p_listcodestart"];
		$p_listcodeend = $row["p_listcodeend"];
		$p_classtype = $row["p_classtype"];
		$p_collect_type = $row["p_collect_type"];
		$p_time = $row["p_time"];
		$p_listlinkstart = $row["p_listlinkstart"];
		$p_listlinkend = $row["p_listlinkend"];
		$p_authortype = $row["p_authortype"];
		$p_authorstart = $row["p_authorstart"];
		$p_authorend = $row["p_authorend"];
		$p_titletype = $row["p_titletype"];
		$p_titlestart = $row["p_titlestart"];
		$p_titleend = $row["p_titleend"];
		$p_timestart = $row["p_timestart"];
		$p_timeend = $row["p_timeend"];
		$p_typestart = $row["p_typestart"];
		$p_typeend = $row["p_typeend"];
		$p_contentstart = $row["p_contentstart"];
		$p_contentend = $row["p_contentend"];
		$p_hitsstart = $row["p_hitsstart"];
		$p_hitsend = $row["p_hitsend"];
	}
	else{
		$p_pagetype=0;
	}
	
?>
<script>
$(document).ready(function(){
	$("#loading",window.parent.document).ajaxStart(function(){
		$(this).show();
	});
	$("#loading",window.parent.document).ajaxStop(function(){
    	$(this).hide();
 	});
});

function $$(id){return document.getElementById(id);}
function trim(s){ return (""+s).replace(/(^\s*)|(\s*$)/g,"");}
function isNum(s){
	var r,re;
	if(s==""){ return false; }
	re = /\d*/i;
	r = s.match(re);
	return (r==s);
}
function getBody(str,s1,s2){
	if(s2=='') return false;
	var tstr=str.toLowerCase(),ind=tstr.indexOf(s1.toLowerCase()),sLen=s1.length;
	if(ind==-1) return false;
	ind=sLen>0 ? ind+sLen : 0;
	s1 = s1.replace(/\\t/g,'\u0009').replace(/\\n/g,'\u000a').replace(/\\r/g,'\u000d');
	s2 = s2.replace(/\\t/g,'\u0009').replace(/\\n/g,'\u000a').replace(/\\r/g,'\u000d');
	return str.substr(ind,tstr.slice(ind).indexOf(s2.toLowerCase()));
}

function definiteUrl(ls,curl){
	var b,t,H,i,j,k,siteUrl,rg=/^\s*[\/\\]/i,ab=/^http:\/\//i,qt=/\\([\\\/])/ig;
	curl=trim(curl).replace(qt,"/");
	siteUrl=curl.replace(/(https?\:\/\/)((\w+)(\.\w+)*(:\d+)?)\/.*/i,"$1$2");
	curl=curl.replace(siteUrl,"");
	for(var i=0;i<ls.length;i++){
		H=""+ls[i];
		if(ab.test(H)){
		}else if(rg.test(H)){
			ls[i]=siteUrl+H
		}else{
			j=H.split("../").length,t=curl.split("/"),k=t.length
			t.length=j<k ? k-j : 0
			ls[i]=siteUrl+t.join("/")+"/"+H.replace(/([\.]{2}\/)/g,"")
		}
	}
	return ls;
}

var stepnum=1;
var listurl="",listcode="",listcutcode="",contenturl="",contentcode="";
function prestep(){
	if (stepnum>1){
		if (stepnum==2 ){
			$("#msgurl").html("");
			$("#htmlcode").val("");
			$("#htmltable").hide();
		}
		if ($("input[name='p_pagetype']:checked").val()=="3" && stepnum==3 ){
			stepnum--;
		}
		stepnum--;
		stepshow();
	}
	else{
		if(confirm("确实要离开该页面吗？\n\n这将会导致规则页面上未保存的数据丢失，确定？\n\n按“确定”继续，或按“取消”留在当前页面。")){
			history.go(-1);
		}
	}
}
function nextstep(){
	var rc = true;
	
	if(stepnum==1){
		if($("#p_name").val().trim()==""){
			alert("项目名称不能为空");
			$("#p_name").focus();
			return;
		}
		switch( $("input[name='p_pagetype']:checked").val() )
		{
			case "0":
				listurl = $("#p_url").val().trim();
				if( $("#p_url").val().trim()=="" ){
					rc=false;
					alert("采集地址不能为空");
					$("#p_url").focus();
				}
				break;
			case "1":
			case "3":
				listurl = $("#p_pagebatchurl").val().trim().replace("{ID}",$("#p_pagebatchid1").val());
				if( $("#p_pagebatchurl").val().trim()=="" ){
					rc=false;
					alert("采集地址不能为空");
					$("#p_pagebatchurl").focus();
				}
				if (rc && !isNum($("#p_pagebatchid1").val()) ){
					rc=false;
					alert("请输入数字");
					$("#p_pagebatchid1").focus();
				}
				if (rc && !isNum($("#p_pagebatchid2").val()) ){
					rc=false;
					alert("请输入数字");
					$("#p_pagebatchid2").focus();
				}
				break;
			case "2":
				listurl = $("#p_manualurl").val().trim().split("\r\n")[0];
				if( $("#p_manualurl").val().trim()=="" ){
					rc=false;
					alert("采集地址不能为空");
					$("#p_manualurl").focus();
				}
				break;
		}
		if( rc && !isNum($("#p_hitsstart").val()) ){
			rc=false;
			alert("随机人气必须是数字哦");
			$("#p_hitsstart").focus();
		}
		if( rc && !isNum($("#p_hitsend").val()) ){
			rc=false;
			alert("随机人气必须是数字哦");
			$("#p_hitsend").focus();
		}
		if(!rc){
			return;
		}
		else{
			if ($("input[name='p_pagetype']:checked").val()=="3"){
				stepnum++;
			}
			$("#listurl").val(listurl);
			showurl(listurl);
			$.ajax({ cache: false, dataType: 'html', type: 'GET', url: 'collect_art_manage.php?action=getcode&charset=' + $("#p_coding").val() + '&url=' + encodeURI(listurl),
				success: function(r){
					if(r!="false"){
						listcode = r;
						if($("#showcode").attr("checked")){
							$("#htmlcode").val(r);
							$("#htmltable").show();
						}
					}
					else{
						alert("获取列表代码出错，请重试");
						return;
					}
				},
				error:function(r){
					alert("err");
				}
			});
		}
	}
	else if(stepnum==2){
		if($("#p_listcodestart").val().trim()==""){
			rc=false;
			alert("列表开始代码不能为空");
			$("#p_listcodestart").focus();
		}
		if(rc && $("#p_listcodeend").val().trim()==""){
			rc=false;
			alert("列表结束代码不能为空");
			$("#p_listcodeend").focus();
		}
		if(rc && $("#p_listlinkstart").val().trim()==""){
			rc=false;
			alert("链接开始代码不能为空");
			$("#p_listlinkstart").focus();
		}
		if(rc && $("#p_listlinkend").val().trim()==""){
			rc=false;
			alert("链接结束代码不能为空");
			$("#p_listlinkend").focus();
		}
		
		if(rc && $("input[name='p_titletype']:checked").val() == "1"){
			if(rc && $("#p_listtitlestart").val().trim()==""){
				rc=false;
				alert("名称开始代码不能为空");
				$("#p_listtitlestart").focus();
			}
			if(rc && $("#p_listtitleend").val().trim()==""){
				rc=false;
				alert("名称结束代码不能为空");
				$("#p_listtitleend").focus();
			}
		}
		if(rc && $("input[name='p_authortype']:checked").val() == "1"){
			if(rc && $("#p_listauthorstart").val().trim()==""){
				rc=false;
				alert("作者开始代码不能为空");
				$("#p_listauthorstart").focus();
			}
			if(rc && $("#p_listauthorend").val().trim()==""){
				rc=false;
				alert("作者结束代码不能为空");
				$("#p_listauthorend").focus();
			}
		}
		if(!rc){
			return;
		}
		else{
			listcutcode=getBody(listcode,$("#p_listcodestart").val(), $("#p_listcodeend").val() );
			if(listcutcode==false){if(!confirm("截取 列表开始~列表结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			var mlink=getBody(listcutcode,$("#p_listlinkstart").val(),$("#p_listlinkend").val());
			if(mlink==false){if(!confirm("截取 链接开始~链接结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			if($("input[name='p_titletype']:checked").val()=="1"){
				var title = getBody(listcutcode, $("#p_listtitlestart").val(), $("#p_listtitleend").val() );
				if(title==false){if(!confirm("截取 标题开始~标题结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			}
			if($("input[name='p_authortype']:checked").val()=="1"){
				var starring = getBody(listcutcode, $("#p_listauthorstart").val() , $("#p_listauthorend").val() );
				if(starring==false){if(!confirm("截取 主演开始~主演结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			}
			contenturl = definiteUrl([mlink],listurl);
			
			showurl(contenturl);
			$.ajax({ cache: false, dataType: 'html', type: 'GET', url: 'collect_art_manage.php?action=getcode&charset=' + $("#p_coding").val() + '&url=' + encodeURI(contenturl),
				success: function(r){
					if(r!="false"){
						contentcode = r;
						if($("#showcode").attr("checked")){
							$("#htmlcode").val(r);
							$("#htmltable").show();
						}
					}
					else{
						alert("获取内容页代码出错，请重试");
						return;
					}
				},
				error:function(r){
					alert("err");
				}
			});
		}
	}
	else if(stepnum==3){
		
		if( $("input[name='p_titletype']:checked").val()=="0" ){
			if( $("#p_titlestart").val()=="" || $("#p_titleend").val()==""){
				alert("截取 名称开始~名称结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改");
				return;	
			}
		}
		$("#form1").attr("action","?action=savecs")
		$("#form1").submit();
		
	}
	else if(stepnum==4){
		$("#form1").submit();
	}
	stepnum++;
	stepshow();
}
function stepshow()
{
	for(i=1;i<=4;i++){
		if(i==stepnum){
			$("#step"+i).show();
			$("#tabs"+i).attr("class","hover");
		}
		else{
			$("#step"+i).hide();
			$("#tabs"+i).removeClass("hover");
		}
	}
}
function showurl(s){
	$('#msgurl').html("&nbsp;&nbsp;当前采集地址：<font color=red>"+s+"</font>");
}
</script>
<div>
	<ul id="htmltabs" style="padding:0">
		<li id="tabs1" class="hover" style="cursor:pointer">1，采集基本信息</li>
		<li id="tabs2" style="cursor:pointer">2，采集列表信息</li>
		<li id="tabs3" style="cursor:pointer">3，采集内容信息</li>
		<li id="tabs4" style="cursor:pointer">4，采集测试结果</li>
	</ul>
</div>
<table class="tb2">
<tr>
<td>
	<span id="msgurl"></span>
</td>
</tr>
<tr id="htmltable" style="display:none">
	<th colspan="2"><textarea id="htmlcode" style="width:99%;height:200px;font-family:Fixedsys" wrap="off" readonly></textarea></th>
</tr>
</table>
	
<form action="?action=save" method="post" id="form1" name="form1">
<table class="tb" id="step1">
	<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
	<INPUT id="listurl" name="listurl" type="hidden" value="" >
    <tbody>
    <tr>
	<td width="15%">项目名称：</td>
	<td>
	<INPUT id="p_name" name="p_name" size="50" value="<?php echo $p_name?>" >
	</td>
    </tr>
    <tr>
    <td>采集过程方式：</td>
	<td>
	<input name="p_showtype" type="radio" value="0" <?php if ($p_showtype==0) { echo "checked";} ?>>显示采集一个列表 &nbsp;&nbsp;
	<input name="p_showtype" type="radio" value="1" <?php if ($p_showtype==1) { echo "checked";} ?>>显示采集一条数据 &nbsp;&nbsp;
	</td>
	</tr>
	<tr>
	<td>采集参数：</td>
	<td>
	<input id="p_collecorder" name="p_collecorder" type="checkbox" value="1" <?php if ($p_collecorder==1){ echo "checked";} ?>>倒序采集 &nbsp;&nbsp; 
	</td>
	</tr>
	<tr>
	<td>目标网页编码：</td>
	<td>
	<select id="p_coding" name="p_coding">
	<option value="gb2312" <?php if ($p_coding=="gb2312") { echo "selected";} ?>>gb2312</option>
	<option value="utf-8" <?php if ($p_coding=="utf-8") { echo "selected";} ?>>utf-8</option>
	<option value="big5" <?php if ($p_coding=="big5") { echo "selected";} ?>>big5</option>
	</select>
	</td>
    </tr>
    <tr>
	<td>分页设置：</td>
	<td>
	<input type="radio" value="0" name="p_pagetype" checked="checked" onClick="showpageshow('0');" <?php if ($p_pagetype==0) { echo "checked";} ?>>
不分页&nbsp;&nbsp;
	<input type="radio" value="1" name="p_pagetype" onClick="showpageshow('1');" <?php if ($p_pagetype==1 ) { echo "checked";} ?>>
批量分页&nbsp;
	<input type="radio" value="2" name="p_pagetype" onClick="showpageshow('2');" <?php if ($p_pagetype==2 ) { echo "checked";} ?>>
手动分页&nbsp;
	<input type="radio" value="3" name="p_pagetype" onClick="showpageshow('3');" <?php if ($p_pagetype==3 ) { echo "checked";} ?>> 
按ID直接采集内容
	</td>
    </tr>
	<tr id="IndexCutPage" >
	<td>采集地址：</td>
	<td>
	<INPUT id="p_url" name="p_url" size="80" value="<?php echo $p_url?>">
	</td>
	</tr>
	<tr id="HandCutPage" style="display:none">
	<td><span id="CutPageName"></span>：</td>
	<td><input id="p_pagebatchurl" name="p_pagebatchurl" type="text" value="<?php echo $p_pagebatchurl?>" size="80">
	分页代码 <font color=red>{ID}</font><br>
	标准格式：Http://www.xxxxx.com/list/list_{ID}.html<br>
	采集范围：
	<input id="p_pagebatchid1" name="p_pagebatchid1" type="text" value="<?php echo $p_pagebatchid1?>" size="4">
	To 
	<input id="p_pagebatchid2" name="p_pagebatchid2" type="text" value="<?php echo $p_pagebatchid2?>" size="4">
	例如：1 - 9</td>
	</tr>
	<tr id="ListContent" style="display:none">
	<td>手动分页：</td>
	<td><textarea id="p_manualurl" name="p_manualurl" cols="60" rows="3"><?php echo $p_manualurl?></textarea></td>
	</tr>
	<tr>
	<td>随机人气：</td>
	<td>
	从&nbsp;<input id="p_hitsstart" name="p_hitsstart" type="text" size="4" value="<?php echo $p_hitsstart?>"> 
	到 &nbsp; <input id="p_hitsend" name="p_hitsend" type="text" size="4" value="<?php echo $p_hitsend?>"> 
	之间 (前小后大)
	</td>
	</tr>
	<tr>
	<td>过滤选项：</td>
	<td height=16 >
	<input name="p_script[]" type="checkbox" value="1" <?php if (($p_script & 1)>0) { echo "checked";} ?>>
	Iframe
	<input name="p_script[]" type="checkbox" value="2" <?php if (($p_script & 2)>0) { echo "checked";} ?>>
	Object
	<input name="p_script[]" type="checkbox" value="4" <?php if (($p_script & 4)>0) { echo "checked";} ?>>
	Script
	<input name="p_script[]" type="checkbox" value="8" <?php if (($p_script & 8)>0) { echo "checked";} ?>>
	Div
	<input name="p_script[]" type="checkbox" value="16" <?php if (($p_script & 16)>0) { echo "checked";} ?>>
	Class
	<input name="p_script[]" type="checkbox" value="32" <?php if (($p_script & 32)>0) { echo "checked";} ?>>
	Table<br>
	&nbsp;&nbsp; <br>
	<input name="p_script[]" type="checkbox" value="64" <?php if (($p_script & 64)>0) { echo "checked";} ?>>
	Span
	<input name="p_script[]" type="checkbox" value="128" <?php if (($p_script & 128)>0) { echo "checked";} ?>>
	Img
	<input name="p_script[]" type="checkbox" value="256" <?php if (($p_script & 256)>0) { echo "checked";} ?>>
	Font
	<input name="p_script[]" type="checkbox" value="512" <?php if (($p_script & 512)>0) { echo "checked";} ?>>
	A
	<input name="p_script[]" type="checkbox" value="1024" <?php if (($p_script & 1024)>0) { echo "checked";} ?>>
	Tr
	<input name="p_script[]" type="checkbox" value="2048" <?php if (($p_script & 2048)>0) { echo "checked";} ?>>
	Td
	<input name="p_script[]" type="checkbox" value="4096" <?php if (($p_script & 4096)>0) { echo "checked";} ?>>
	Html
	</td>
	</tr>
</table>
		
<table class="tb" id="step2" style="display:none">
    <tr>
	<td>列表开始代码：</td>
	<td>
	<span onClick="if($$('p_listcodestart').rows>2)$$('p_listcodestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listcodestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listcodestart" name="p_listcodestart" cols="70" rows="3"><?php echo $p_listcodestart?></textarea>
	</td>
	</tr>
	<tr>
	<td>列表结束代码：</td>
	<td>
	<span onClick="if($$('p_listcodeend').rows>2)$$('p_listcodeend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listcodeend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listcodeend" name="p_listcodeend" cols="70" rows="3"><?php echo $p_listcodeend?></textarea>
	</td>
    </tr>
    <tr>
	<td>链接开始代码：</td>
	<td>
	<span onClick="if($$('p_listlinkstart').rows>2)$$('p_listlinkstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listlinkstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listlinkstart" name="p_listlinkstart" cols="70" rows="3"><?php echo $p_listlinkstart?></textarea>
	</td>
    </tr>
    <tr>
	<td>链接结束代码：</td>
	<td>
	<span onClick="if($$('p_listlinkend').rows>2)$$('p_listlinkend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listlinkend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listlinkend" name="p_listlinkend" cols="70" rows="3"><?php echo $p_listlinkend?></textarea>
	</td>
    </tr>
    <tr>
	<td>列表采集名称：</td>
	<td>
	<input type="radio" value="0" name="p_titletype" <?php if ($p_titletype==0) {echo "checked=\"checked\"";}?> onClick="ChangeCutPara('0','trp_listtitlestart','trp_listtitleend');ChangeCutPara('1','trp_titlestart','trp_titleend');">
否&nbsp;&nbsp;
	<input type="radio" value="1" name="p_titletype" <?php if ($p_titletype==1) { echo "checked=\"checked\"";}?> onClick="ChangeCutPara('1','trp_listtitlestart','trp_listtitleend');ChangeCutPara('0','trp_titlestart','trp_titleend');">
是&nbsp;
	</td>
	</tr>
    <tr id="trp_listtitlestart" <?php if ($p_titletype==0) { echo "style=\"display:none\"";} ?>>
	<td>名称开始代码：</td>
	<td>
	<span onClick="if($$('p_listtitlestart').rows>2)$$('p_listtitlestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listtitlestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listtitlestart" name="p_listtitlestart" cols="70" rows="3"><?php echo $p_titlestart?></textarea>
	</td>
    </tr>
    <tr id="trp_listtitleend" <?php if ($p_titletype==0) { echo "style=\"display:none\"";} ?>>
	<td>名称结束代码：</td>
	<td>
	<span onClick="if($$('p_listtitleend').rows>2)$$('p_listtitleend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listtitleend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listtitleend" name="p_listtitleend" cols="70" rows="3"><?php echo $p_titleend?></textarea>
	</td>
    </tr>
    <tr>
	<td>列表采集作者：</td>
	<td>
	<input type="radio" value="0" name="p_authortype" <?php if ($p_authortype==0) {echo " checked=\"checked\"";}?> onClick="ChangeCutPara('0','trp_listauthorstart','trp_listauthorend');ChangeCutPara('1','trp_authorstart','trp_authorend');">
否&nbsp;&nbsp;
	<input type="radio" value="1" name="p_authortype" <?php if ($p_authortype==1) {echo "checked=\"checked\"";}?> onClick="ChangeCutPara('1','trp_listauthorstart','trp_listauthorend');ChangeCutPara('0','trp_authorstart','trp_authorend');">
是&nbsp;
	</td>
	</tr>
    <tr id="trp_listauthorstart" <?php if ($p_authortype==0) { echo "style=\"display:none\"";} ?>>
	<td>作者开始代码：</td>
	<td>
	<span onClick="if($$('p_listauthorstart').rows>2)$$('p_listauthorstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listauthorstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listauthorstart" name="p_listauthorstart" cols="70" rows="3"><?php echo $p_authorstart?></textarea>
	</td>
    </tr>
    <tr id="trp_authorend" <?php if ($p_authortype==0) { echo "style=\"display:none\"";} ?>>
	<td>作者结束代码：</td>
	<td>
	<span onClick="if($$('p_listauthorend').rows>2)$$('p_listauthorend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listauthorend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listauthorend" name="p_listauthorend" cols="70" rows="3"><?php echo $p_authorend?></textarea>
	</td>
	</tr>
</table>
		
<table class="tb" id="step3" style="display:none">
  	<?php if ($p_titletype == 0) {?>
	<tr id="trp_titlestart">
	<td>标题开始代码：</td>
	<td>
	<span onClick="if($$('p_titlestart').rows>2)$$('p_titlestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_titlestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_titlestart" cols="70" rows="3"><?php echo $p_titlestart?></textarea>
	</td>
    </tr>
    <tr id="trp_titleend">
	<td>标题结束代码：</td>
	<td>
	<span onClick="if($$('p_titleend').rows>2)$$('p_titleend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_titleend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_titleend" cols="70" rows="3"><?php echo $p_titleend?></textarea>
	</td>
	</tr>
	<?php
    	}
    if ($p_authortype ==0) {
    ?>
    <tr id="trp_authorstart">
	<td>作者开始代码：</td>
	<td>
	<span onClick="if($$('p_authorstart').rows>2)$$('p_authorstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_authorstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_authorstart" cols="70" rows="3"><?php echo $p_authorstart?></textarea>
	</td>
    </tr>
	<tr id="trp_authorend">
	<td>作者结束代码：</td>
	<td>
	<span onClick="if($$('p_authorend').rows>2)$$('p_authorend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_authorend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_authorend" cols="70" rows="3"><?php echo $p_authorend?></textarea>
	</td>
    </tr>
    <?php
    	}
    ?>
	<tr>
	<td><font color="#FF0000">栏目设置：</font></td>
	<td>
	<input type="radio" value="0" name="p_classtype" onClick="$('#trp_typestart').css('display','none');$('#trp_typeend').css('display','none');$('#trp_classtype').css('display','');$('#p_collect_type').css('display','');" <?php if ($p_classtype==0) { echo "checked";} ?>>
	固定栏目&nbsp;&nbsp; 
	<input type="radio" value="1" name="p_classtype" onClick="$('#trp_classtype').css('display','none');$('#p_collect_type').css('display','none');$('#trp_typestart').css('display','');$('#trp_typeend').css('display','');" <?php if ($p_classtype==1 ) { echo "checked";} ?>>
按对应栏目自动转换
	</td>
	</tr>
	<tr  id="trp_classtype" <?php if ($p_classtype==1 ) { echo "style=\"display:none\"";} ?>>
	<td><font color="#FF0000">选择入库栏目：</font></td>
	<td id="CollectClassN2" >
	<select name="p_collect_type" id="p_collect_type" size="1">
	<option value="0">请选择入库分类</option>
	<?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$p_collect_type)?>
	</select></td>
	</tr>
 	<tr id="trp_typestart" <?php if ($p_classtype==0 ){ echo "style=\"display:none\"";} ?>>
	<td><font color="#FF0000">栏目开始代码：</font></td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_typestart').rows>2)$$('p_typestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_typestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_typestart" cols="70" rows="3" id="p_typestart"><?php echo $p_typestart?></textarea></td>
	</tr>
	<tr id="trp_typeend" <?php if ($p_classtype==0 ){ echo "style=\"display:none\"";} ?>>
	<td><font color="#FF0000">栏目结束代码：</font></td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_typeend').rows>2)$$('p_typeend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_typeend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_typeend" cols="70" rows="3" id="p_typeend"><?php echo $p_typeend?></textarea></td>
	</tr>
	<tr>
	<td>发布日期开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_timestart').rows>2)$$('p_timestart').rows-=1" style='cursor:hand'><b>缩小	</b></span> <span onClick="$$('p_timestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_timestart" cols="70" rows="3" id="p_timestart"><?php echo $p_timestart?></textarea></td>
	</tr>
	<tr>
	<td>发布日期结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_timeend').rows>2)$$('p_timeend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_timeend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_timeend" cols="70" rows="3" id="p_timeend"><?php echo $p_timeend?></textarea></td>
	</tr>
	<tr>
	<td>文章内容开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_contentstart').rows>2)$$('p_contentstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_contentstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_contentstart" cols="70" rows="3" id="p_contentstart"><?php echo $p_contentstart?></textarea></td>
	</tr>
	<tr>
	<td>文章内容结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_contentend').rows>2)$$('p_contentend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_contentend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_contentend" cols="70" rows="3" id="p_contentend"><?php echo $p_contentend?></textarea></td>
	</tr>
</table>

<table class="tb" id="step4" style="display:none">
	<tbody>
	<tr>
	<td>
	<strong>正在获取数据请稍后...</strong>
	</td>
	</tr>
</table>
	
<table class="tb2">
<tr>
<td>
	&nbsp;&nbsp;<input type="button" class="btn" name="back" value="上一步" onclick="prestep()" />&nbsp;&nbsp;<input type="button" class="btn" name="next" value="下一步" onclick="nextstep()" />&nbsp;&nbsp;<input type="checkbox" class="checkbox" name="showcode" id="showcode" value="1"/>下一步显示源码 &nbsp;&nbsp;<input type="submit" class="btn" value="保存规则"/>
</td>
</tr>
</table>
</form>
<script language="JavaScript">
var currObj = "uuuu";
function getActiveText(obj)
{
	obj.focus();
	currObj = obj;
}
function addTag(ibTag)
{
	var isClose = false;
	var obj_ta = currObj;
	if (obj_ta.isTextEdit){
		obj_ta.focus();
		var sel = document.selection;
		var rng = sel.createRange();
		rng.colapse;
		if((sel.type == "Text" || sel.type == "None") && rng != null){
			rng.text = ibTag;
		}
		obj_ta.focus();
		return isClose;
	}
	else return false;
}

function ChangeCutPara(flag,e1,e2)
{
	switch (flag)
	{
		case "0" :
		$("#"+e1).css("display","none");
		$("#"+e2).css("display","none");
		break;
	case "1" :
		$("#"+e1).css("display","");
		$("#"+e2).css("display","");
		break;
	}
}
function showpageshow(flag)
{
	switch (flag)
	{
	case "0" :
	$("#IndexCutPage").css("display","");
	$("#HandCutPage").css("display","none");
	$("#ListContent").css("display","none");
	break;
	case "1" :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","");
	$("#ListContent").css("display","none");
	$("#CutPageName").html("批量分页");
	break;
	case "2" :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","none");
	$("#ListContent").css("display","");
	break;
	case "3" :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","");
	$("#ListContent").css("display","none");
	$("#CutPageName").html("按ID采集内容页");
	break;
	default :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","none");
	$("#ListContent").css("display","none");
	break;
	}
}
showpageshow("<?php echo $p_pagetype?>");
</script>
<?php
}

function save()
{	
	global $db,$cache,$action;
	
	//1
	$p_id = be("all","p_id") ;
	$p_name = be("post","p_name") ; $p_coding = be("post","p_coding") ;
	$p_pagetype = be("post","p_pagetype") ; $p_url = be("post","p_url");
	$p_pagebatchurl = be("post","p_pagebatchurl") ; $p_manualurl = be("post","p_manualurl");
	$p_pagebatchid1 = be("post","p_pagebatchid1") ; $p_pagebatchid2 = be("post","p_pagebatchid2");
	$p_collecorder = be("post","p_collecorder") ; $p_savefiles = be("post","p_savefiles");
	$p_ontime = be("post","p_ontime");  $p_hitsstart = be("post","p_hitsstart");
	$p_hitsend = be("post","p_hitsend"); $p_showtype = be("post","p_showtype");
	$p_script = be("arr","p_script");
	$sarr =explode(",",$p_script);
	$p_script = 0;
	foreach($sarr as $s){
		if (!isN($s)){
			$p_script = $p_script | intval($s);
		}
	}
	
	if (isN($p_collecorder)) { $p_collecorder = 0;}
	if (isN($p_savefiles)) { $p_savefiles = 0;}
	if (isN($P_IntoLib)) { $P_IntoLib = 0;}
	if (isN($p_ontime)) { $p_ontime = 0;}
	if (isN($p_server)) { $p_server = 0;}
	if(!isNum($p_pagebatchid1)){$p_pagebatchid1=1;}
	if(!isNum($p_pagebatchid2)){$p_pagebatchid2=1;}
	
	//2
	$p_listcodestart = be("post","p_listcodestart"); $p_listcodeend = be("post","p_listcodeend");
	$p_listlinkstart = be("post","p_listlinkstart"); $p_listlinkend = be("post","p_listlinkend");
	$p_titletype = be("post","p_titletype");
	$p_authortype = be("post","p_authortype");
	if (isN($p_authortype)) { $p_authortype = 0;} else { $p_authortype=intval($p_authortype); }
	if (isN($p_titletype)) { $p_titletype = 0;} else { $p_titletype=intval($p_titletype); }
	
	//3
	$p_timestart=  be("post","p_timestart") ;
	$p_timeend = be("post","p_timeend") ; $p_classtype=  be("post","p_classtype") ;
	$p_collect_type = be("post","p_collect_type") ; $p_typestart=  be("post","p_typestart") ;
	$p_typeend = be("post","p_typeend") ; $p_contentstart=  be("post","p_contentstart") ;
	$p_contentend = be("post","p_contentend") ;
	if (isN($p_collect_type)){ $p_collect_type=0; }
	
	$strlisturl = be("post","listurl");
	if($p_authortype==1){
		$p_authorstart = be("post","p_listauthorstart"); 
		$p_authorend = be("post","p_listauthorend");
	}
	else{
		$p_authorstart = be("post","p_authorstart"); 
		$p_authorend = be("post","p_authorend");
	}
	if($p_titletype==1){
		$p_titlestart = be("post","p_listtitlestart");
		$p_titleend = be("post","p_listtitleend");
	}
	else{
		$p_titlestart = be("post","p_titlestart");
		$p_titleend = be("post","p_titleend");
	}
	
	
	
	$strSet = "";
	if( isN($p_id) ) {
		$sql="INSERT {pre}cj_art_projects(p_time)  values ('".date('Y-m-d H:i:s',time())."')";
		$db->query($sql);
		$p_id = $db->insert_id();
	}
	
	$strSet.= " p_name='".$p_name."',p_coding='".$p_coding."',p_pagetype='".$p_pagetype."',p_url='".$p_url."',p_pagebatchurl='".$p_pagebatchurl."',p_manualurl='".$p_manualurl."',p_pagebatchid1='".$p_pagebatchid1."',p_pagebatchid2='".$p_pagebatchid2."',p_script='".$p_script."',p_showtype='".$p_showtype."',p_collecorder='".$p_collecorder."',p_savefiles='".$p_savefiles."',p_ontime='".$p_ontime."',p_hitsstart='".$p_hitsstart."',p_hitsend='".$p_hitsend."',";
	$strSet.="p_authorstart='".$p_authorstart."',p_authorend='".$p_authorend."',p_titlestart='".$p_titlestart."',p_titleend='".$p_titleend."',p_listcodestart='".$p_listcodestart."',p_listcodeend='".$p_listcodeend."',p_listlinkstart='".$p_listlinkstart."',p_listlinkend='".$p_listlinkend."',p_authortype='".$p_authortype."',p_titletype='".$p_titletype."',";
	$strSet.="p_timestart='".$p_timestart."',p_timeend='".$p_timeend."',p_classtype='".$p_classtype."',p_collect_type='".$p_collect_type."',p_typestart='".$p_typestart."',p_typeend='".$p_typeend."',p_contentstart='".$p_contentstart."',p_contentend='".$p_contentend."'";
	
	
 	$db->query("update {pre}cj_art_projects set " .$strSet . " where p_id=" . $p_id);
 	
	
	if ($action=="save"){
 	  	alertUrl ("保存成功","collect_art_manage.php");
 	}
 	else{
 		headAdminCollect ("文章自定义采集项目测试");
 		
		if ($p_pagetype != 3){ 
			if( isN($_SESSION["strListCodeart"] )){
				$strListCode = getPage($strlisturl,$p_coding);
				$_SESSION["strListCodeart"] = $strListCode;
			}
			else{
				$strListCode = $_SESSION["strListCodeart"];
			}
			
			if( isN($_SESSION["strListCodeCutart"] )){
				$strListCodeCut = getBody($strListCode,$p_listcodestart,$p_listcodeend);
				$_SESSION["strListCodeCutart"] = $strListCodeCut;
			}
			else{
				$strListCodeCut = $_SESSION["strListCodeCutart"];
			}
			
			if( isN($_SESSION["linkarrcodeart"] )){
				$linkarrcode = getArray($strListCodeCut,$p_listlinkstart,$p_listlinkend);
				$_SESSION["linkarrcodeart"] = $linkarrcode;
			}
			else{
				$linkarrcode = $_SESSION["linkarrcodeart"];
			}
			
			if ($p_authortype ==1){
				$starringarr = getArray($strListCodeCut,$p_authorstart,$p_authorend);
			}
			if ($p_titletype ==1) {
				$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
			}
			
			
			switch($linkarrcode)
			{
			Case False:
				errmsg ("采集提示","<li>在获取链接列表时出错。</li>");break;
			default:
				$linkarr = explode("{Array}",$linkarrcode);
				$UrlTest = $linkarr[0];
				$UrlTest = definiteUrl($UrlTest,$strlisturl);
				$linkcode = getPage($UrlTest,$p_coding);
				
				break;
			}
		}
		else{
			$strlisturl = $p_pagebatchurl;
			$p_pagebatchurl = replaceStr($p_pagebatchurl,"{ID}",$p_pagebatchid1);
			$linkcode = getPage($p_pagebatchurl,$p_coding);
		}
		
		if ($p_titletype ==1) {
			switch($titlearrcode)
			{
			Case False:
				$titlecode = "获取失败";break;
			default:
				$titlearr = explode("{Array}",$titlearrcode);
				$titlecode = $titlearr[0];
				break;
			}
		}
		else{
			$titlecode = getBody($linkcode,$p_titlestart,$p_titleend);
		}
		
		if ($p_authortype ==1) {
			switch($titlearrcode)
			{
			Case False:
				$starringcode = "获取失败";break;
			default:
				$starringarr = explode("{Array}",$starringarrcode);
				$starringcode = $starringarr[0];
				break;
			}
		}
		else{
			$starringcode = getBody($linkcode,$p_authorstart,$p_authorend);
		}
		
		$timecode = getBody($linkcode,$p_timestart,$p_timeend);
		$timecode = replaceStr($timecode,"False",now);
		$contentcode = getBody($linkcode,$p_contentstart,$p_contentend);
		$contentcode = replaceStr($contentcode,"False","未知");
		$contentcode = replaceFilters($contentcode,$p_id,2,1);
		
		if ($p_classtype ==1) {
			$typecode = getBody($linkcode,$p_typestart,$p_typeend);
		}
		else{
			$typecode = $p_collect_type;
			$typearr = getValueByArray($cache[1], "t_id" ,$typecode );
			$typecode = $typearr["t_name"];
		}
		
		$titlecode = filterScript($titlecode,$p_script);
		$titlecode = replaceFilters($titlecode,$p_id,1,1);
		$starringcode = filterScript($starringcode,$p_script);
		$timecode = filterScript($timecode,$p_script);
		$typecode = filterScript($typecode,$p_script);
?>
<table class="tb">
  	<tr>
	<td  colspan="2" align="center">保存规则并采集测试结果</td>
  	</tr>
    <tr>
	<td width="15%">标题：</td><td> <input type="text" size="50" name="d_name" value="<?php echo $titlecode?>" /> </td>
    </tr>
    <tr>
	<td>作者：</td><td> <input type="text" size="50" name="d_author" value="<?php echo $starringcode?>" /> </td>
    </tr>
    <tr>
	<td>日期：</td><td> <input type="text" size="50" name="d_time" value="<?php echo $timecode?>" /> </td>
    </tr>
    <tr>
	<td>栏目：</td><td> <input type="text" size="50" name="d_typename" value="<?php echo $typecode?>" /> </td>
    </tr>
    <tr>
	<td>内容：</td>
	<td> <div style="height:300px;overflow:hidden;overflow-y:auto;"><?php echo $contentcode?> </div></td>
    </tr>
	<tr>
	<td colspan="2"><input type="button" onClick="window.location.href='javascript:history.go(-1)'" value="返回规则">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onClick="window.location.href='?action=main'" value="返回列表"></td>
	</tr>
</table>
<?php
	}
} 

function main()
{
	global $db,$cache;
	$pagenum = be("get","page");
	if (isN($pagenum) || !isNum($pagenum)){ $pagenum = 1; }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$pagenum = intval($pagenum);
	
	 $sql = "select * from {pre}cj_art_projects ";
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount=ceil($nums/app_pagenum);//总页数
	$sql = $sql ."limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td>
		菜单：<a href="collect_art_manage.php?action=add">添加采集规则</a> | <a href="collect_art_change.php">分类转换</a> | <a href="collect_art_filters.php">信息过滤</a> 
	</td>
	</tr>
</table>
<form action="" method="post" name="form1">
<table class=tb >
	<tr>
	<td width="4%">&nbsp;</td>
	<td>项目名称</td>
	<td width="25%">入库分类</td>
	<td width="20%">上次采集</td>
	<td width="25%">操作</td>
	</tr>
	<?php
	if (!$rs){
	?>
    <tr><td align="center" colspan="7" >没有任何记录!</td></tr>
    <?php
	}
	else{
	  	while ($row = $db ->fetch_array($rs))
	  	{
	?>
    <tr>
	  <td><input name="p_id[]" type="checkbox" id="p_id" value="<?php echo $row["p_id"]?>" /></td>
      <td><a href="?action=edit&p_id=<?php echo $row["p_id"]?>"><?php echo $row["p_name"]?></a></td>
	  <td>
	  <?php
	  	if ($row["p_classtype"] == 1) {
	  		echo "<font color=red>自定义分类</font>";
	  }
	  	else{
	  		$typearr = getValueByArray($cache[1], "t_id", $row["p_collect_type"]);
	  		echo $typearr["t_name"];
	  	}
	  ?>
	  </td>
      <td><?php echo getColorDay($row['p_time']) ?></td>
 	  <td>
 	   <A href="collect_art_cj.php?p_id=<?php echo  $row["p_id"] ?>">采集</A>｜
 	  <A href="?action=edit&p_id=<?php echo $row["p_id"]?>">修改</A>｜
 	  <A href="?action=copy&p_id=<?php echo  $row["p_id"] ?>">复制</A>｜
 	  <A href="?action=export&p_id=<?php echo  $row["p_id"] ?>">导出</A>｜
 	  <A href="?action=del&p_id=<?php echo $row["p_id"]?>">删除</A>
 	  </td>
    </tr>
	<?php
		}
	}
	?>
	<tr>
	<td  colspan="7">
	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'p_id[]');"/>&nbsp;
	<input type="submit" value="批量删除" onClick="if(confirm('确定要删除吗')){form1.action='?action=delall';}else{return false}"  class="input"/>
	<input type="submit" value="批量采集" onClick="if(confirm('确定要批量采集吗')){form1.action='collect_art_cj.php?action=pl';}else{return false}"  class="input"/>
	<input type="button" value="导入规则" onClick="$('#win1').window('open');" class="input"/>
	</td>
	</tr>
    <tr align="center" bgcolor="#f8fbfb">
	<td colspan="7">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_art_manage.php?page={p}") ?>
	</td>
	</tr>
</table>
</form>
<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:400px;" closed="true" closable="true" minimizable="false" maximizable="false">
<form enctype="multipart/form-data" action="?action=upexpsave" method="post">
<table class="tb">
  <tbody>
  	<tr>
  		<td colspan="2" align="center">
  		上传采集规则
         <input type="file" id="file1" name="file1">
		<input type="submit" name="submit" value="开始导入">
		</td>
  	</tr>
</table>
</form>
</div>
<?php
}
?>