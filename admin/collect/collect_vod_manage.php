<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");

switch(trim($action))
{
	case "add" :
	case "edit" : headAdminCollect ("视频自定义采集项目编辑"); edit();break;
	case "save" :
	case "savecs" : save();break;
	case "del" : del();break;
	case "copy" : copynew();break;
	case "delall" : delall();break;
	case "export" : export(); break;
	case "upexpsave" : upexpsave(); break;
	case "getcode" : getcode();break;
	case "breakpoint" : breakpoint(); break;
	default :  clearSession(); headAdminCollect ("视频自定义采集项目管理"); main(); break;
}

function export()
{
	global $db;
	$p_id = be("get","p_id");
	$fields = $db->getTableFields(app_dbname,"{pre}cj_vod_projects");
	$colsnum = mysql_num_fields($fields);
	$row = $db->getRow("select * from {pre}cj_vod_projects where p_id='".$p_id."'");
	$result="";
	$fileName= $row["p_name"];
	for($i = 0; $i < $colsnum; $i++){
		$colname = mysql_field_name($fields, $i);
		$result .= "<".$colname.">".$row[$colname]."</".$colname.">"."\r\n";
	} 
	unset($row);
	$filePath = "../../upload/export/". iconv("UTF-8", "GBK", $fileName) .".txt";
	fwrite(fopen($filePath,"wb"),$result);
	echo "<script language=\"javascript\">setTimeout(\"gonextpage();\",0);function gonextpage(){location.href='collect_down.php?file=".$fileName."';}</script> ";
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
		if($iar[1][$m] !="p_id" && $iar[1][$m] !="p_lzphasestart" && $iar[1][$m] !="p_lzphaseend" ){
			if ($rc){  $in1 .= ","; $in2 .= ","; }
			 $in1 .= $iar[1][$m];
			 $in2 .= "'". replaceStr(replaceStr($iar[2][$m],'\\',"\\\\"),"'","\'") . "'";
			 $rc=true;
		}
	}
	$sql = "insert into {pre}cj_vod_projects (".$in1.") values(".$in2.")";
	
	$status = $db->query($sql);
	if($status){
		showmsg ("导入规则成功!","collect_vod_manage.php");
	}
	else{
		alert("导入失败，请检查规则是否正确!");
	}
}

function breakpoint()
{
	echo gBreakpoint("../../upload/vodbreakpoint") . "正在载入断点续传数据，请稍后......";
	exit;
}

function copynew()
{
	global $db;
	$p_id = be("get","p_id");
    $sql = "INSERT INTO  {pre}cj_vod_projects(p_name, p_coding, p_playtype, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_collecorder, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_starringtype, p_starringstart, p_starringend, p_titletype, p_titlestart, p_titleend, p_pictype, p_picstart, p_picend, p_timestart, p_timeend, p_areastart, p_areaend, p_typestart, p_typeend, p_contentstart, p_contentend, p_playcodetype, p_playcodestart, p_playcodeend, p_playurlstart, p_playurlend, p_playlinktype, p_playlinkstart, p_playlinkend, p_playspecialtype, p_playspecialrrul, p_playspecialrerul, p_server, p_hitsstart, p_hitsend, p_lzstart, p_lzend, p_colleclinkorder, p_lzcodetype, p_lzcodestart, p_lzcodeend, p_languagestart, p_languageend, p_remarksstart, p_remarksend,p_directedstart,p_directedend,p_setnametype,p_setnamestart,p_setnameend) SELECT p_name, p_coding, p_playtype, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_collecorder, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_starringtype, p_starringstart, p_starringend, p_titletype, p_titlestart, p_titleend, p_pictype, p_picstart, p_picend, p_timestart, p_timeend, p_areastart, p_areaend, p_typestart, p_typeend, p_contentstart, p_contentend, p_playcodetype, p_playcodestart, p_playcodeend, p_playurlstart, p_playurlend, p_playlinktype, p_playlinkstart, p_playlinkend, p_playspecialtype, p_playspecialrrul, p_playspecialrerul, p_server, p_hitsstart, p_hitsend, p_lzstart, p_lzend, p_colleclinkorder, p_lzcodetype, p_lzcodestart, p_lzcodeend, p_languagestart, p_languageend, p_remarksstart, p_remarksend,p_directedstart,p_directedend,p_setnametype,p_setnamestart,p_setnameend FROM  {pre}cj_vod_projects WHERE p_id =" .$p_id;
	$db->query($sql);
    showmsg ("复制采集栏目成功！","collect_vod_manage.php");
}

function del()
{
	global $db;
	$p_id=be("get","p_id");
    $sql= "delete from {pre}cj_vod_projects WHERE p_id=".$p_id;
    $db->query($sql);
    showmsg ("采集项目删除成功！","collect_vod_manage.php");
}

function delall()
{
	global $db;
    $ids=be("arr","p_id");
    if (!isN($ids)){
	  $db->query("delete from {pre}cj_vod_projects WHERE p_id in (".$ids.")");
	}
    showmsg ("采集项目删除成功！","collect_vod_manage.php");
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
	$p_id=be("all","p_id");
	$p_pagetype=0;
	$p_pagebatchid1=1;
 	$p_pagebatchid2=1;
 	$p_hitsstart=0;
 	$p_hitsend=0;
 	
	if(!isN($p_id)){
		$sql="select * from {pre}cj_vod_projects where p_id = ".$p_id;
		$row = $db->getRow($sql);
		$p_id = $row["p_id"];
		$p_name = $row["p_name"];
		$p_coding = $row["p_coding"]; $p_coding = strtolower($p_coding);
		$p_playtype = $row["p_playtype"];
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
		$p_starringtype = $row["p_starringtype"];
		$p_starringstart = $row["p_starringstart"];
		$p_starringend = $row["p_starringend"];
		$p_titletype = $row["p_titletype"];
		$p_titlestart = $row["p_titlestart"];
		$p_titleend = $row["p_titleend"];
		$p_pictype = $row["p_pictype"];
		$p_picstart = $row["p_picstart"];
		$p_picend = $row["p_picend"];
		$p_timestart = $row["p_timestart"];
		$p_timeend = $row["p_timeend"];
		$p_areastart = $row["p_areastart"];
		$p_areaend = $row["p_areaend"];
		$p_typestart = $row["p_typestart"];
		$p_typeend = $row["p_typeend"];
		$p_contentstart = $row["p_contentstart"];
		$p_contentend = $row["p_contentend"];
		$p_playcodetype = $row["p_playcodetype"];
		$p_playcodestart = $row["p_playcodestart"];
		$p_playcodeend = $row["p_playcodeend"];
		$p_playurlstart = $row["p_playurlstart"];
		$p_playurlend = $row["p_playurlend"];
		$p_playlinktype = $row["p_playlinktype"];
		$p_playlinkstart = $row["p_playlinkstart"];
		$p_playlinkend = $row["p_playlinkend"];
		$p_playspecialtype = $row["p_playspecialtype"];
		$p_playspecialrrul = $row["p_playspecialrrul"];
		$p_playspecialrerul = $row["p_playspecialrerul"];
		$p_server = $row["p_server"];
		$p_hitsstart = $row["p_hitsstart"];
		$p_hitsend = $row["p_hitsend"];
		$p_lzstart = $row["p_lzstart"];
		$p_lzend = $row["p_lzend"];
		$p_colleclinkorder = $row["p_colleclinkorder"];
		$p_lzcodetype = $row["p_lzcodetype"];
		$p_lzcodestart = $row["p_lzcodestart"];
		$p_lzcodeend = $row["p_lzcodeend"];
		$p_languagestart = $row["p_languagestart"];
		$p_languageend = $row["p_languageend"];
		$p_remarksstart = $row["p_remarksstart"];
		$p_remarksend = $row["p_remarksend"];
		$p_directedstart = $row["p_directedstart"];
		$p_directedend = $row["p_directedend"];
		$p_setnametype = $row["p_setnametype"];
		$p_setnamestart = $row["p_setnamestart"];
		$p_setnameend = $row["p_setnameend"];
		unset($row);
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
			$.ajax({ cache: false, dataType: 'html', type: 'GET', url: 'collect_vod_manage.php?action=getcode&charset=' + $("#p_coding").val() + '&url=' + encodeURI(listurl),
				success: function(r){
					if(r!="false"){
						listcode = r;
						if($("#showcode").attr("checked")){
							$("#htmlcode").val(r);
							$("#htmltable").show();
						}
					}
					else{
						alert("获取页面代码出错，请重试");
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
		if(rc && $("input[name='p_starringtype']:checked").val() == "1"){
			if(rc && $("#p_liststarringstart").val().trim()==""){
				rc=false;
				alert("主演开始代码不能为空");
				$("#p_liststarringstart").focus();
			}
			if(rc && $("#p_liststarringend").val().trim()==""){
				rc=false;
				alert("主演结束代码不能为空");
				$("#p_liststarringend").focus();
			}
		}
		if(rc && $("input[name='p_pictype']:checked").val() == "1"){
			if(rc && $("#p_listpicstart").val().trim()==""){
				rc=false;
				alert("图片开始代码不能为空");
				$("#p_listpicstart").focus();
			}
			if(rc && $("#p_listpicend").val().trim()==""){
				rc=false;
				alert("图片结束代码不能为空");
				$("#p_listpicend").focus();
			}
		}
		if(!rc){
			return;
		}
		else{
			listcutcode=getBody(listcode,$("#p_listcodestart").val(), $("#p_listcodeend").val() );
			if(listcutcode==false){if(!confirm("截取 列表开始~列表结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			var mlink=getBody(listcutcode,$("#p_listlinkstart").val(), $("#p_listlinkend").val() );
			if(mlink==false){if(!confirm("截取 链接开始~链接结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			if($("input[name='p_titletype']:checked").val()=="1"){
				var title = getBody(listcutcode,$("#p_listtitlestart").val(), $("#p_listtitleend").val() );
				if(title==false){if(!confirm("截取 标题开始~标题结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			}
			if($("input[name='p_pictype']:checked").val()=="1"){
				var pic = getBody(listcutcode,$("#p_listpicstart").val(), $("#p_listpicend").val() );
				if(pic==false){if(!confirm("截取 图片开始~图片结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			}
			if($("input[name='p_starringtype']:checked").val()=="1"){
				var starring = getBody(listcutcode,$("#p_liststarringstart").val(), $("#p_liststarringend").val() );
				if(starring==false){if(!confirm("截取 主演开始~主演结束 失败\n\n点[确定]忽略这错误提示，[取消]返回修改")){return;}}
			}
			contenturl = definiteUrl([mlink],listurl);
			
			showurl(contenturl);
			$.ajax({ cache: false, dataType: 'html', type: 'GET', url: 'collect_vod_manage.php?action=getcode&charset=' + $("#p_coding").val() + '&url=' + encodeURI(contenturl),
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
	<td width="15%" >项目名称：</td>
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
  	<input id="p_collecorder" name="p_collecorder" type="checkbox" value="1" <?php if ($p_collecorder==1){ echo "checked";} ?>>分页倒序采集 &nbsp;&nbsp;
  	<input id="p_colleclinkorder" name="p_colleclinkorder" type="checkbox" value="1" <?php if ($p_colleclinkorder==1){echo "checked";}?>>列表倒序采集 &nbsp;&nbsp;
  	<input id="p_savefiles" name="p_savefiles" type="checkbox" value="1" <?php if ($p_savefiles==1){ echo "checked";} ?>> 采集中保存图片(采集过程方式为:显示采集一条数据时使用)
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
	<td>播放器：</td>
	<td>
	&nbsp;<select id="p_playtype" name="p_playtype">
	<option value=''>暂没有数据</option>
	<?php echo makeSelectPlayer($p_playtype)?>
	</select>
	</td>
    </tr>
    <tr>
	<td>服务器组：</td>
	<td>
	&nbsp;<select id="p_server" name="p_server">
	<option value='0'>无服务器组</option>
	<?php echo makeSelectServer($p_server)?>
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
	<tr ID="IndexCutPage" >
	<td>采集地址：</td>
	<td>
	<INPUT id="p_url" name="p_url" size="50" value="<?php echo $p_url?>">
	</td>
	</tr>
	<tr ID="HandCutPage" style="display:none">
	<td><span id="CutPageName"></span>：</td>
	<td><input type="text"  id="p_pagebatchurl" name="p_pagebatchurl" size="60" value="<?php echo $p_pagebatchurl?>"/>
	分页代码 <font color=red>{ID}</font><br>
	标准格式：Http://www.xxxxx.com/list/list_{ID}.html<br>
	采集范围：
	<input id="p_pagebatchid1" name="p_pagebatchid1" type="text" value="<?php echo $p_pagebatchid1?>" size="4">
	To 
	<input id="p_pagebatchid2" name="p_pagebatchid2" type="text" value="<?php echo $p_pagebatchid2?>" size="4">
	例如：1 - 9</td>
	</tr>
	<tr ID="ListContent" style="display:none">
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
	<td width="15%">列表开始代码：</td>
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
	<td>列表采集主演：</td>
	<td>
	<input type="radio" value="0" name="p_starringtype" <?php if ($p_starringtype==0) {echo " checked=\"checked\"";}?> onClick="ChangeCutPara('0','trp_liststarringstart','trp_liststarringend');ChangeCutPara('1','trp_starringstart','trp_starringend');">
否&nbsp;&nbsp;
	<input type="radio" value="1" name="p_starringtype" <?php if ($p_starringtype==1) {echo "checked=\"checked\"";}?> onClick="ChangeCutPara('1','trp_liststarringstart','trp_liststarringend');ChangeCutPara('0','trp_starringstart','trp_starringend');">
是&nbsp;
	</td>
	</tr>
	<tr id="trp_liststarringstart" <?php if ($p_starringtype==0) { echo "style=\"display:none\"";} ?>>
	<td>主演开始代码：</td>
	<td>
	<span onClick="if($$('p_liststarringstart').rows>2)$$('p_liststarringstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_liststarringstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_liststarringstart"  name="p_liststarringstart" cols="70" rows="3"><?php echo $p_starringstart?></textarea>
	</td>
    </tr>
    <tr id="trp_liststarringend" <?php if ($p_starringtype==0) { echo "style=\"display:none\"";} ?>>
	<td>主演结束代码：</td>
	<td>
	<span onClick="if($$('p_liststarringend').rows>2)$$('p_liststarringend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_liststarringend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_liststarringend" name="p_liststarringend" cols="70" rows="3"><?php echo $p_starringend?></textarea>
	</td>
    </tr>
	<tr>
	<td>列表采集图片：</td>
	<td>
	<input type="radio" value="0" name="p_pictype" <?php if ($p_pictype==0 ){echo "checked=\"checked\"";}?> onClick="ChangeCutPara('0','trp_listpicstart','trp_listpicend');ChangeCutPara('1','trp_picstart','trp_picend');">
否&nbsp;&nbsp;
	<input type="radio" value="1" name="p_pictype" <?php if ($p_pictype==1){echo "checked=\"checked\"";}?> onClick="ChangeCutPara('1','trp_listpicstart','trp_listpicend');ChangeCutPara('0','trp_picstart','trp_picend');">
是&nbsp;
	</td>
    </tr>
    <tr id="trp_listpicstart" <?php if ($p_pictype==0) { echo "style=\"display:none\"";} ?>>
	<td>图片开始代码：</td>
	<td>
	<span onClick="if($$('p_listpicstart').rows>2)$$('p_listpicstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listpicstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listpicstart" name="p_listpicstart" cols="70" rows="3"><?php echo $p_picstart?></textarea>
	</td>
	</tr>
    <tr id="trp_listpicend" <?php if ($p_pictype==0) { echo "style=\"display:none\"";} ?>>
	<td>图片结束代码：</td>
	<td>
	<span onClick="if($$('p_listpicend').rows>2)$$('p_listpicend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_listpicend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea id="p_listpicend" name="p_listpicend" cols="70" rows="3"><?php echo $p_picend?></textarea>
	</td>
    </tr>
</table>

<table class="tb" id="step3" style="display:none">
  	<?php if ($p_titletype == 0) {?>
    <tr id="trp_titlestart">
	<td width="20%">名称开始代码：</td>
	<td>
	<span onClick="if($$('p_titlestart').rows>2)$$('p_titlestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_titlestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_titlestart" cols="70" rows="3"><?php echo $p_titlestart?></textarea>	  </td>
	</tr>
    <tr id="trp_titleend">
	<td>名称结束代码：</td>
	<td>
	<span onClick="if($$('p_titleend').rows>2)$$('p_titleend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_titleend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_titleend" cols="70" rows="3"><?php echo $p_titleend?></textarea>	  </td>
	</tr>
	<?php }?>
	<tr>
	<td>连载代码范围：</td>
	<td><input type="radio" value="0" name="p_lzcodetype" onClick="ChangeCutPara('0','trp_lzcodestart','trp_lzcodeend');" <?php if ($p_lzcodetype==0) {echo "checked";} ?>>  
	 关闭&nbsp;&nbsp; <input type="radio" value="1" name="p_lzcodetype" onClick="ChangeCutPara('1','trp_lzcodestart','trp_lzcodeend');" <?php if ($p_lzcodetype==1) {echo "checked";} ?>> 
	 开启</td>
	 </tr>
	<tr id="trp_lzcodestart" <?php if ($p_lzcodetype <> 1 ) { echo "style=\"display:none\""; }?>>
	<td>连载范围开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_lzcodestart').rows>2)$$('p_lzcodestart').rows-=1" style='cursor:hand'><b>缩小	</b></span> <span onClick="$$('p_lzcodestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_lzcodestart" cols="70" rows="3" id="p_lzcodestart"><?php echo $p_lzcodestart ?></textarea></td>
	</tr>
	<tr id="trp_lzcodeend" <?php if ($p_lzcodetype <>1) { echo "style=\"display:none\"";} ?>>
	<td>连载范围结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_lzcodeend').rows>2)$$('p_lzcodeend').rows-=1" style='cursor:hand'><b>缩小	</b></span> <span onClick="$$('p_lzcodeend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_lzcodeend" cols="70" rows="3" id="p_lzcodeend"><?php echo $p_lzcodeend?></textarea></td>
	</tr>
    <tr>
	<td vAlign=center >连载开始代码：</td>
	<td>
	<span onClick="if($$('p_lzstart').rows>2)$$('p_lzstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_lzstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_lzstart" cols="70" rows="3"><?php echo $p_lzstart?></textarea>	 
	</td>
    </tr>
    <tr>
	<td vAlign=center >连载结束代码：</td>
	<td>
	<span onClick="if($$('p_lzend').rows>2)$$('p_lzend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_lzend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_lzend" cols="70" rows="3"><?php echo $p_lzend?></textarea>	 
	</td>
    </tr>
	<tr>
	<td vAlign=center >备注开始代码：</td>
	<td>
	<span onClick="if($$('p_remarksstart').rows>2)$$('p_remarksstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_remarksstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_remarksstart" cols="70" rows="3"><?php echo $p_remarksstart?></textarea>	 
	</td>
	</tr>
    <tr>
	<td vAlign=center >备注结束代码：</td>
	<td>
	<span onClick="if($$('p_remarksend').rows>2)$$('p_remarksend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_remarksend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_remarksend" cols="70" rows="3"><?php echo $p_remarksend?></textarea>	 
	</td>
	</tr>
    <?php if ($p_starringtype ==0) {?>
    <tr id="trp_starringstart">
	<td>主演开始代码：</td>
	<td>
	<span onClick="if($$('p_starringstart').rows>2)$$('p_starringstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_starringstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_starringstart" cols="70" rows="3"><?php echo $p_starringstart?></textarea>	  </td>
	</tr>
    <tr id="trp_starringend">
	<td>主演结束代码：</td>
	<td>
	<span onClick="if($$('p_starringend').rows>2)$$('p_starringend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_starringend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_starringend" cols="70" rows="3"><?php echo $p_starringend?></textarea>	  </td>
	</tr>
    <?php }?>
    <tr id="trp_directedstart">
	<td>导演开始代码：</td>
	<td>
	<span onClick="if($$('p_directedstart').rows>2)$$('p_directedstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_directedstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_directedstart" cols="70" rows="3"><?php echo $p_directedstart?></textarea>	  </td>
	</tr>
    <tr id="trp_directedend">
	<td>导演结束代码：</td>
	<td>
	<span onClick="if($$('p_directedend').rows>2)$$('p_directedend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_directedend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_directedend" cols="70" rows="3"><?php echo $p_directedend?></textarea>	  </td>
	</tr>
    <?php if ($p_pictype ==0) {?>
    <tr id="trp_picstart">
	<td>图片开始代码：</td>
	<td>
	<span onClick="if($$('p_picstart').rows>2)$$('p_picstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_picstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_picstart" cols="70" rows="3"><?php echo $p_picstart?></textarea>	  </td>
    </tr>
    <tr id="trp_picend">
	<td>图片结束代码：</td>
	<td>
	<span onClick="if($$('p_picend').rows>2)$$('p_picend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_picend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_picend" cols="70" rows="3"><?php echo $p_picend?></textarea>	  </td>
    </tr>
    <?php }?>
 	<tr>
	<td><font color="#FF0000">栏目设置：</font></td>
	<td>
	<input type="radio" value="0" name="p_classtype" onClick="$('#trp_typestart').css('display','none');$('#trp_typeend').css('display','none');$('#trp_classtype').css('display','');$('#p_collect_type').css('display','');" <?php if ($p_classtype==0) { echo "checked";} ?>>
	固定栏目&nbsp;&nbsp; 
	<input type="radio" value="1" name="p_classtype" onClick="$('#trp_classtype').css('display','none');$('#p_collect_type').css('display','none');$('#trp_typestart').css('display','');$('#trp_typeend').css('display','');" <?php if ($p_classtype==1 ) { echo "checked";} ?>>
按对应栏目自动转换</td>
	</tr>
	<tr id="trp_classtype" <?php if ($p_classtype==1 ) { echo "style=\"display:none\"";} ?>>
	<td><font color="#FF0000">选择入库栏目：</font></td>
	<td id="CollectClassN2" >
	<select name="p_collect_type" id="p_collect_type" size="1">
	<option value="0">请选择入库分类</option>
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$p_collect_type)?>
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
	<td>上映日期开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_timestart').rows>2)$$('p_timestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_timestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_timestart" cols="70" rows="3" id="p_timestart"><?php echo $p_timestart?></textarea></td>
	</tr>
	<tr>
	<td>上映日期结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_timeend').rows>2)$$('p_timeend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_timeend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_timeend" cols="70" rows="3" id="p_timeend"><?php echo $p_timeend?></textarea></td>
	</tr>
	<tr>
	<td>地区开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_areastart').rows>2)$$('p_areastart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_areastart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_areastart" id="p_areastart" cols="70" rows="3"><?php echo $p_areastart?></textarea></td>
	</tr>
	<tr>
	<td>地区结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_areaend').rows>2)$$('p_areaend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_areaend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_areaend" id="p_areaend" cols="70" rows="3"><?php echo $p_areaend?></textarea></td>
	</tr>
	<tr>
	<td>语言开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_languagestart').rows>2)$$('p_languagestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_languagestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_languagestart" id="p_languagestart" cols="70" rows="3"><?echo $p_languagestart?></textarea></td>
	</tr>
	<tr>
	<td>语言结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_languageend').rows>2)$$('p_languageend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_languageend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_languageend" id="p_languageend" cols="70" rows="3"><?echo $p_languageend?></textarea></td>
	</tr>
	<tr>
	<td>介绍开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_contentstart').rows>2)$$('p_contentstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_contentstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_contentstart" cols="70" rows="3" id="p_contentstart"><?php echo $p_contentstart?></textarea></td>
	</tr>
	<tr>
	<td>介绍结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_contentend').rows>2)$$('p_contentend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_contentend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_contentend" cols="70" rows="3" id="p_contentend"><?php echo $p_contentend?></textarea></td>
	</tr>
	<tr>
	<td>列表范围：</td>
	<td><input type="radio" value="0" name="p_playcodetype" onClick="ChangeCutPara('0','trp_playcodestart','trp_playcodeend');" <?php if ($p_playcodetype==0 ){ echo "checked" ;}?>>  
 关闭&nbsp;&nbsp; <input type="radio" value="1" name="p_playcodetype" onClick="ChangeCutPara('1','trp_playcodestart','trp_playcodeend');" <?php if ($p_playcodetype==1 ){ echo "checked";} ?>> 
	开启</td>
	</tr> 
	<tr id="trp_playcodestart" <?php if ($p_playcodetype !=1 ){ echo "style=\"display:none\"";} ?>>
	<td>播放列表开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playcodestart').rows>2)$$('p_playcodestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playcodestart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_playcodestart" cols="70" rows="3" id="p_playcodestart"><?php echo $p_playcodestart?></textarea></td>
	</tr>
	<tr id="trp_playcodeend" <?php if ($p_playcodetype !=1 ){ echo "style=\"display:none\"";} ?>>
	<td>播放列表结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playcodeend').rows>2)$$('p_playcodeend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playcodeend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_playcodeend" cols="70" rows="3" id="p_playcodeend"><?php echo $p_playcodeend?></textarea></td>
	</tr>
	<tr>
	<td><font color="#FF0000">获取地址设置：</font></td>
	<td><input type="radio" value="0" name="p_playlinktype" onClick="ChangeCutPara('0','trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==0) {echo "checked" ;}?>>
	内容页直接获取地址&nbsp;&nbsp; <input type="radio" value="1" name="p_playlinktype" onClick="ChangeCutPara('1','trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==1){ echo "checked";}?>>
	&nbsp;&nbsp; 播放页获取地址
	<input type="radio" value="2" name="p_playlinktype" onClick="ChangeCutPara('1','trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==2){ echo "checked";}?>>
	播放链接中获取地址
	&nbsp;&nbsp; <input type="radio" value="3" name="p_playlinktype" onClick="ChangeCutPara('1','trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==3) {echo "checked" ;}?> >
  单播放页获取所有播放地址
	</td>
	</tr>
	<tr id="trp_playlinkstart" <?php if ($p_playlinktype==0 ){ echo "style=\"display:none\"";} ?>>
	<td>播放链接开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playlinkstart').rows>2)$$('p_playlinkstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playlinkstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_playlinkstart" cols="70" rows="3" id="p_playlinkstart"><?php echo $p_playlinkstart?></textarea></td>
	</tr>
	<tr id="trp_playlinkend" <?php if ($p_playlinktype==0 ){ echo "style=\"display:none\"";} ?>>
	<td>播放链接结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playlinkend').rows>2)$$('p_playlinkend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playlinkend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_playlinkend" cols="70" rows="3" id="p_playlinkend"><?php echo $p_playlinkend?></textarea></td>
	</tr>
	<tr id="trp_playspecialtype" >
	<td>特殊播放链接处理：</td>
	<td><input type="radio" value="0" name="p_playspecialtype" checked="checked" onClick="ChangeCutPara('0','listurl2','listurl3');" <?php if ($p_playspecialtype==0 ){ echo "checked";}?>>
	不作设置&nbsp;&nbsp;
	<input type="radio" value="1" name="p_playspecialtype" onClick="ChangeCutPara('1','listurl2','listurl3');" <?php if ($p_playspecialtype==1) { echo "checked";}?>>
	替换地址<br>
	<font color="red">对于使用了JavaScript:openwindow形式的连接请使用以下格式处理:<br>
	脚本连接:内容[变量] 内容 如:javaScript:OpenWnd([变量])<br>
	实际连接:内容[变量] 内容 如:play.php?id=[变量]</font></td>
	</tr>
	<tr id="listurl2" <?php if ($p_playspecialtype!=1) { echo "style=\"display:none\"";} ?>>
	<td>要替换的地址：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playspecialrrul').rows>2)$$('p_playspecialrrul').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playspecialrrul').rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_playspecialrrul);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
	<textarea name="p_playspecialrrul" cols="70" rows="3" id="p_playspecialrrul"><?php echo $p_playspecialrrul?></textarea></td>
	</tr>
	<tr id="listurl3" <?php if ($p_playspecialtype !=1) { echo "style=\"display:none\"";} ?>>
	<td>替换为的地址：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playspecialrerul').rows>2)$$('p_playspecialrerul').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playspecialrerul').rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_playspecialrerul);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
	<textarea name="p_playspecialrerul" cols="70" rows="3" id="p_playspecialrerul"><?php echo $p_playspecialrerul?></textarea></td>
	</tr>
	<tr>
	<td>地址开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playurlstart').rows>2)$$('p_playurlstart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playurlstart').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_playurlstart" cols="70" rows="3" id="p_playurlstart"><?php echo $p_playurlstart?></textarea></td>
	</tr>
	<tr>
	<td>地址结束代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_playurlend').rows>2)$$('p_playurlend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_playurlend').rows+=1" style='cursor:hand'><b>扩大</b></span><br>
	<textarea name="p_playurlend" cols="70" rows="3" id="p_playurlend"><?php echo $p_playurlend?></textarea></td>
	</tr>
	<tr id="tr_SetNameType">
	<td>截取集数名称：</td>
	<td><input type="radio" value="0" name="p_setnametype" checked="checked" onClick="ChangeCutPara('0','trP_SetNameStart','trP_SetNameEnd');" <?php if( $p_setnametype==0) { echo "checked";}?>>
	不截取&nbsp;&nbsp;
	<input type="radio" value="1" name="p_setnametype" onClick="ChangeCutPara('1','trP_SetNameStart','trP_SetNameEnd');" <?php if($p_setnametype==1){echo "checked";}?>>
	播放地址中截取&nbsp;&nbsp;
	<input type="radio" value="2" name="p_setnametype" onClick="ChangeCutPara('1','trP_SetNameStart','trP_SetNameEnd');" <?php if($p_setnametype==2){echo "checked";}?>>
	播放页中截取&nbsp;&nbsp;
	<input type="radio" value="3" name="p_setnametype" onClick="ChangeCutPara('1','trP_SetNameStart','trP_SetNameEnd');" <?php if($p_setnametype==3){echo "checked";}?>>
	内容页中截取&nbsp;&nbsp;
	<br>
	</td>
	</tr>
	<tr  id="trP_SetNameStart" <?php if ($p_setnametype ==0) {echo "style=\"display:none\"";} ?>>
	<td>集数名称开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_setnamestart').rows>2)$$('p_setnamestart').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_setnamestart').rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_setnamestart);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
	<textarea name="p_setnamestart" cols="70" rows="3" id="p_setnamestart"><?php echo $p_setnamestart?></textarea></td>
	</tr>
	<tr id="trP_SetNameEnd" <?php if ($p_setnametype ==0){ echo "style=\"display:none\"";} ?>>
	<td>集数名称结束代码：</td>
 	<td>&nbsp;&nbsp;输入区域： <span onClick="if($$('p_setnameend').rows>2)$$('p_setnameend').rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="$$('p_setnameend').rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_setnameend);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
	<textarea name="p_setnameend" cols="70" rows="3" id="p_setnameend"><?php echo $p_setnameend?></textarea></td>
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
	$p_id = be("all","p_id") ; 
	
	//1
	$p_name = be("post","p_name") ; $p_coding = be("post","p_coding") ;
	$p_playtype = be("post","p_playtype") ; $p_pagetype = be("all","p_pagetype") ; $p_url = be("post","p_url");
	$p_pagebatchurl = be("post","p_pagebatchurl") ; $p_manualurl = be("post","p_manualurl");
	$p_pagebatchid1 = be("post","p_pagebatchid1") ; $p_pagebatchid2 = be("post","p_pagebatchid2");
	$p_collecorder = be("post","p_collecorder") ; $p_savefiles = be("post","p_savefiles");
	$p_intolib = be("post","p_intolib") ; $p_ontime = be("post","p_ontime");
	$p_server = be("post","p_server") ; $p_hitsstart = be("post","p_hitsstart");
	$p_hitsend = be("post","p_hitsend"); $p_colleclinkorder = be("post","p_colleclinkorder");
	$p_showtype = be("post","p_showtype");
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
	if (isN($p_intolib)) { $p_intolib = 0;}
	if (isN($p_ontime)) { $p_ontime = 0;}
	if (isN($p_server)) { $p_server = 0;}
	if (isN($p_colleclinkorder)) {$p_colleclinkorder=0;}
	if(!isNum($p_pagebatchid1)){$p_pagebatchid1=1;}
	if(!isNum($p_pagebatchid2)){$p_pagebatchid2=1;}
	
	//2
	$p_listcodestart = be("post","p_listcodestart"); $p_listcodeend = be("post","p_listcodeend");
	$p_listlinkstart = be("post","p_listlinkstart"); $p_listlinkend = be("post","p_listlinkend");
	$p_starringtype = be("post","p_starringtype");
	$p_titletype = be("post","p_titletype");
	$p_pictype = be("post","p_pictype");
	if (isN($p_starringtype)) { $p_starringtype = 0;} else { $p_starringtype=intval($p_starringtype); }
	if (isN($p_titletype)) { $p_titletype = 0;} else { $p_titletype=intval($p_titletype); }
	if (isN($p_pictype)) { $p_pictype = 0;} else { $p_pictype=intval($p_pictype); }
	
	//3
	$p_timestart=  be("post","p_timestart") ;
	$p_timeend = be("post","p_timeend") ; $p_areastart=  be("post","p_areastart") ;
	$p_areaend = be("post","p_areaend") ; $p_classtype=  be("post","p_classtype") ;
	$p_collect_type = be("post","p_collect_type") ; $p_typestart=  be("post","p_typestart") ;
	$p_typeend = be("post","p_typeend") ; $p_contentstart=  be("post","p_contentstart") ;
	if (isN($p_collect_type)){ $p_collect_type=0; }
	
	$p_contentend = be("post","p_contentend") ; $p_playcodetype=  be("post","p_playcodetype") ;
	$p_playcodestart = be("post","p_playcodestart") ; $p_playcodeend=  be("post","p_playcodeend") ;
	$p_playurlstart = be("post","p_playurlstart") ; $p_playurlend=  be("post","p_playurlend") ;
	$p_playlinktype = be("post","p_playlinktype") ; $p_playlinkstart=  be("post","p_playlinkstart") ;
	
	$p_playlinkend = be("post","p_playlinkend") ; $p_playspecialtype=  be("post","p_playspecialtype") ;
	$p_playspecialrrul = be("post","p_playspecialrrul") ; $p_timestart=  be("post","p_timestart") ;
	$p_playspecialrerul = be("post","p_playspecialrerul");
	
	if($p_starringtype==1){
		$p_starringstart = be("post","p_liststarringstart"); $p_starringend = be("post","p_liststarringend");
	}
	else{
		$p_starringstart = be("post","p_starringstart"); $p_starringend = be("post","p_starringend");
	}
	
	if($p_titletype==1){
		$p_titlestart = be("post","p_listtitlestart"); $p_titleend = be("post","p_listtitleend");
	}
	else{
		$p_titlestart = be("post","p_titlestart"); $p_titleend = be("post","p_titleend");
	}
	
	if($p_pictype==1){
		$p_picstart = be("post","p_listpicstart");$p_picend = be("post","p_listpicend");
	}
	else{
		$p_picstart = be("post","p_picstart");$p_picend = be("post","p_picend");
	}
	
	$p_listcodestart = be("post","p_listcodestart"); 	$p_listcodeend = be("post","p_listcodeend");
	$p_listlinkstart = be("post","p_listlinkstart"); $p_listlinkend = be("post","p_listlinkend");
	$p_lzstart = be("post","p_lzstart"); 	$p_lzend = be("post","p_lzend");
	$p_lzcodetype = be("post","p_lzcodetype"); 
	$p_lzcodestart = be("post","p_lzcodestart"); $p_lzcodeend = be("post","p_lzcodeend");
	$p_languagestart = be("post","p_languagestart"); $p_languageend = be("post","p_languageend");
	$p_remarksstart = be("post","p_remarksstart"); $p_remarksend = be("post","p_remarksend");
	$p_directedstart = be("post","p_directedstart"); $p_directedend = be("post","p_directedend");
	$p_setnametype = be("post","p_setnametype"); 
	$p_setnamestart = be("post","p_setnamestart"); $p_setnameend = be("post","p_setnameend");
	$strlisturl = be("post","listurl"); 
	if (isN($p_lzcodetype)){$p_lzcodetype=0;}
	if (isN($p_playcodetype)){$p_playcodetype=0;}
	if (isN($p_playlinktype)){$p_playlinktype=0;}
	if (isN($p_playspecialtype)){$p_playspecialtype=0;}
	if (isN($p_setnametype)){$p_setnametype=0;}
	
	$strSet = "";
	if( isN($p_id) ) {
		$sql="INSERT {pre}cj_vod_projects(p_time)  values ('".date('Y-m-d H:i:s',time())."')";
		$db->query($sql);
		$p_id = $db->insert_id();
	}
	
	$strSet =" p_name='".$p_name."',p_coding='".$p_coding."',p_playtype='".$p_playtype."',p_pagetype='".$p_pagetype."',p_url='".$p_url."',p_pagebatchurl='".$p_pagebatchurl."',p_manualurl='".$p_manualurl."',p_pagebatchid1='".$p_pagebatchid1."',p_pagebatchid2='".$p_pagebatchid2."',p_script='".$p_script."',p_showtype='".$p_showtype."',p_collecorder='".$p_collecorder."',p_savefiles='".$p_savefiles."',p_ontime='".$p_ontime."',p_server='".$p_server."',p_hitsstart='".$p_hitsstart."',p_hitsend='".$p_hitsend."',p_colleclinkorder='".$p_colleclinkorder."', " ;
	$strSet.= "p_starringstart='".$p_starringstart."',p_starringend='".$p_starringend."',p_titlestart='".$p_titlestart."',p_titleend='".$p_titleend."',p_picstart='".$p_picstart."',p_picend='".$p_picend."',p_listcodestart='".$p_listcodestart."',p_listcodeend='".$p_listcodeend."',p_listlinkstart='".$p_listlinkstart."',p_listlinkend='".$p_listlinkend."',p_starringtype='".$p_starringtype."',p_titletype='".$p_titletype."',p_pictype='".$p_pictype."',";
	$strSet.="p_lzstart='".$p_lzstart."',p_lzend='".$p_lzend."',p_timestart='".$p_timestart."',p_timeend='".$p_timeend."',p_areastart='".$p_areastart."',p_areaend='".$p_areaend."',p_classtype='".$p_classtype."',p_collect_type='".$p_collect_type."',p_typestart='".$p_typestart."',p_typeend='".$p_typeend."',p_contentstart='".$p_contentstart."',p_contentend='".$p_contentend."',p_playcodetype='".$p_playcodetype."',p_playcodestart='".$p_playcodestart."',p_playcodeend='".$p_playcodeend."',p_playurlstart='".$p_playurlstart."',p_playurlend='".$p_playurlend."',p_playlinktype='".$p_playlinktype."',p_playlinkstart='".$p_playlinkstart."',p_playlinkend='".$p_playlinkend."',p_playspecialtype='".$p_playspecialtype."',p_playspecialrrul='".$p_playspecialrrul."',p_playspecialrerul='".$p_playspecialrerul."',p_lzcodetype='".$p_lzcodetype."',p_lzcodestart='".$p_lzcodestart."',p_lzcodeend='".$p_lzcodeend."',p_languagestart='".$p_languagestart."',p_languageend='".$p_languageend."',p_remarksstart='".$p_remarksstart."',p_remarksend='".$p_remarksend."',p_directedstart='".$p_directedstart."',p_directedend='".$p_directedend."',p_setnametype='".$p_setnametype."',p_setnamestart='".$p_setnamestart."',p_setnameend='".$p_setnameend."'";
	
 	$db->query("update {pre}cj_vod_projects set " .$strSet . " where p_id=" .$p_id);
	
	if ($action=="save"){
 	  	alertUrl ("保存成功","collect_vod_manage.php");
 	}
 	else{
 		headAdminCollect ("视频自定义采集项目测试");
 		
		if ($p_server > 0 ) { $p_server_address = getVodXmlText("vodserver.xml","server", $p_server , 1);}
		if ($p_pagetype != 3){
			if( isN($_SESSION["strListCode"] )){
				$strListCode = getPage($strlisturl,$p_coding);
				$_SESSION["strListCode"] = $strListCode;
			}
			else{
				$strListCode = $_SESSION["strListCode"];
			}
			
			if( isN($_SESSION["strListCodeCut"] )){
				$strListCodeCut = getBody($strListCode,$p_listcodestart,$p_listcodeend);
				$_SESSION["strListCodeCut"] = $strListCodeCut;
			}
			else{
				$strListCodeCut = $_SESSION["strListCodeCut"];
			}
			
			if( isN($_SESSION["linkarrcode"] )){
				$linkarrcode = getArray($strListCodeCut,$p_listlinkstart,$p_listlinkend);
				$_SESSION["linkarrcode"] = $linkarrcode;
			}
			else{
				$linkarrcode = $_SESSION["linkarrcode"];
			}
			
			if ($p_starringtype ==1){
				$starringarr = getArray($strListCodeCut,$p_starringstart,$p_starringend);
			}
			if ($p_titletype ==1) {
				$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
			}
			if ($p_pictype ==1) {
				$picarrcode = getArray($strListCodeCut,$p_picstart,$p_picend);
			}
			
			switch($linkarrcode)
			{
				Case False:
					errmsg ("采集提示","<li>在获取链接列表时出错。</li>");break;
				default:
					$linkarr = explode("{Array}",$linkarrcode);
					$strlink = $linkarr[0];
					$strlink = definiteUrl($strlink,$strlisturl);
					$linkcode = getPage($strlink,$p_coding);
				break;
			}
		
		}
		else{
			$strlisturl = $p_pagebatchurl;
			$p_pagebatchurl = replaceStr($p_pagebatchurl,"{ID}",$p_pagebatchid1);
			$strlink = $p_pagebatchurl;
			$linkcode = getPage($p_pagebatchurl,$p_coding);
		}
		
		if ($linkcode ==False) { 
			errmsg ("采集提示","获取内容页失败!" );
		    return;
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
		
		if ($p_starringtype ==1) {
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
			$starringcode = getBody($linkcode,$p_starringstart,$p_starringend);
		}
		
		if ($p_pictype ==1) {
			switch($picarrcode)
			{
			Case False:
				$piccode = "获取失败";break;
			default:
				$picarr = explode("{Array}",$picarrcode);
				$piccode = $picarr[0];
				break;
			}
		}
		else{
			$piccode = getBody($linkcode,$p_picstart,$p_picend);
		}
		
		
		if ($p_lzcodetype ==1){
			$lzfwcode = getBody($linkcode,$p_lzcodestart,$p_lzcodeend);
			$lzcode = getBody($lzfwcode,$p_lzstart,$p_lzend);
			$lzcode = replaceStr($lzcode,"False","0");
		}
		else{
			$lzcode = getBody($linkcode,$p_lzstart,$p_lzend);
			$lzcode = replaceStr($lzcode,"False","0");
		}
		
		if ($p_classtype ==1) {
			$typecode = getBody($linkcode,$p_typestart,$p_typeend);
		}
		else{
			$typecode = $p_collect_type;
			$typearr = getValueByArray($cache[0], "t_id" ,$typecode );
			$typecode = $typearr["t_name"];
		}
		$typecode = filterScript($typecode,$p_script);
		
		$remarkscode = getBody($linkcode,$p_remarksstart,$p_remarksend);
		$remarkscode = replaceStr($remarkscode,"False","");
		$remarkscode = filterScript($remarkscode,$p_script);
		
		$directedcode = getBody($linkcode,$p_directedstart,$p_directedend);
		$directedcode = replaceStr($directedcode,"False","未知");
		$directedcode = filterScript($directedcode,$p_script);
		
		$languagecode = getBody($linkcode,$p_languagestart,$p_languageend);
		$languagecode = replaceStr($languagecode,"False","未知");
		$languagecode = filterScript($languagecode,$p_script);
		
		$areacode = getBody($linkcode,$p_areastart,$p_areaend);
		$areacode = replaceStr($areacode,"False","未知");
		$areacode = filterScript($areacode,$p_script);
		
		$timecode = getBody($linkcode,$p_timestart,$p_timeend);
		$timecode = replaceStr($timecode,"False",date('Y-m-d',time()));
		$timecode = filterScript($timecode,$p_script);
		
		$starringcode = replaceStr($starringcode,"False","未知");
		$starringcode = filterScript($starringcode,$p_script);
		
		$piccode = replaceStr($piccode,"False","");
		$piccode = definiteUrl($piccode,$strlisturl);
		$piccode = filterScript($piccode,$p_script);
		
		$titlecode = replaceStr($titlecode,"False","未知");
		$titlecode = filterScript($titlecode,$p_script);
		$titlecode = replaceFilters($titlecode,$p_id,1,0);
		
		$contentcode = getBody($linkcode,$p_contentstart,$p_contentend);
		$contentcode = replaceStr($contentcode,"False","未知");
		$contentcode = filterScript($contentcode,$p_script);
		$contentcode = replaceFilters($contentcode,$p_id,2,0);
		
		if ($p_playcodetype ==1) {
			$playcode = getBody($linkcode,$p_playcodestart,$p_playcodeend);
			if ($p_playlinktype >0) {
				$weburl = getArray($playcode,$p_playlinkstart,$p_playlinkend);
			}
			else{
				$weburl = getArray($playcode,$p_playurlstart,$p_playurlend);
			}
			if ($p_setnametype == 3) {
				$setnames = getArray($playcode,$p_setnamestart,$p_setnameend);
			}
		}
		else{
			if ($p_playlinktype >0) {
				$weburl = getArray($linkcode,$p_playlinkstart,$p_playlinkend);
			}
			else{
				$weburl = getArray($linkcode,$p_playurlstart,$p_playurlend);
				
			}
			if ($p_setnametype == 3) {
				$setnames = getArray($linkcode,$p_setnamestart,$p_setnameend);
			}
		}
?>
<table class="tb">
	<tbody>
  	<tr>
	<td  colspan="2" align="center">保存规则并采集测试结果</td>
  	</tr>
    <tr>
	<td width="15%">名称：</td><td>  <input type="text" size="50" name="d_name" value="<?php echo $titlecode?>" />  连载:<input type="text" size="10" name="d_state" value="<?php echo $lzcode?>" /> 备注：<input type="text" size="10" name="d_remarks" value="<?php echo $remarkscode?>" /> </td>
    </tr>
    <tr>
	<td>演员：</td><td> <input type="text" size="50" name="d_starrinig" value="<?php echo $starringcode?>" /> </td>
    </tr>
    <tr>
	<td>导演：</td><td> <input type="text" size="50" name="d_directed" value="<?php echo $directedcode?>" /> </td>
    </tr>
    <tr>
	<td>日期：</td><td> <input type="text" size="50" name="d_year" value="<?php echo $timecode?>" /> </td>
    </tr>
    <tr>
	<td>栏目：</td><td> <input type="text" size="50" name="d_typename" value="<?php echo $typecode?>" /> </td>
    </tr>
    <tr>
	<td>地区：</td><td> <input type="text" size="50" name="d_area" value="<?php echo $areacode?>" /> </td>
    </tr>
    <tr>
	<td>语言：</td><td> <input type="text" size="50" name="d_language" value="<?php echo $languagecode?>" /> </td>
    </tr>
    <tr>
	<td>图片：</td><td> <input type="text" size="50" name="d_pic" value="<?php echo $piccode?>" /> </td>
    </tr>
    <tr>
	<td>介绍：</td><td> <textarea name="d_content" style="width:500px;height:100px;"/><?php echo strip_tags($contentcode)?></textarea> </td>
    </tr>
    <?php
		 if ($weburl != False) {
		 	  $webArray=explode("{Array}",$weburl);
		 	  $setnamesArray=explode("{Array}",$setnames);
		 	  
		 	  
			  for ($i=0 ;$i<count($webArray);$i++){
			  	$UrlTest = $webArray[$i];
			  	
				if ($p_playspecialtype ==1 && strpos(",".$p_playspecialrrul,"[变量]")) {
					$Keyurl = explode("[变量]",$p_playspecialrrul);
					$urli = getBody ($UrlTest,$Keyurl[0],$Keyurl[1]);
				    if ($urli==False) { break; }
					$UrlTest = replaceStr($p_playspecialrerul,"[变量]",$urli);
				}
					
				if ($p_playlinktype ==1) {
					$UrlTest = definiteUrl($UrlTest,$strlink);
					$webCode = getPage($UrlTest,$p_coding);
					$url = getBody($webCode,$p_playurlstart,$p_playurlend);
					$url = replaceFilters($url,$p_id,3,0);
				}
				else if($p_playlinktype ==2) {
					
					if (isN($p_playurlend)){
						$tmpA = strpos($UrlTest, $p_playurlstart);
                		$url = substr($UrlTest,strlen($UrlTest)-$tmpA-strlen($p_playurlstart)+1);
					}
					else{
						$url = getBody($UrlTest,$p_playurlstart,$p_playurlend);
					}
				
				}
				else if($p_playlinktype ==3) {
					$UrlTest = definiteUrl($UrlTest,$strlink);
					
					
					$webCode = getPage($UrlTest,$p_coding);
					$tmpB = getArray($webCode,$p_playurlstart,$p_playurlend);
					$tmpC = explode("$Array$",$tmpB);
					foreach($tmpC as $tmpD)
					{
						$url = $tmpD;
						?>
						<tr>
					      <td>地址：</td><td> <?php echo $p_server_address . $url?> </td>
					    </tr>
						<?php
					}
					break;
				}
				else{
					$url = replaceFilters($UrlTest,$p_id,3,0);
					?>
						<tr>
					      <td>地址：</td>
					      <td> <?php echo $p_server_address . $url?> </td>
					    </tr>
						<?php
						continue;
				}
				if ($p_setnametype == 1) {
					$setname = getBody($url,$p_setnamestart,$p_setnameend);
					$url = $setname ."$" .$url;
				}
				else if($p_setnametype == 1 && $p_playlinktype ==1) {
					$setname = getBody($webCode,$p_setnamestart,$p_setnameend);
					$url = $setname ."$" .$url;
				}
				else if($p_setnametype==3){
					$url = $setnamesArray[$i] . "$" .$url;
				}
		?>
		    <tr>
		    <td>播放列表：</td><td> <?php echo $UrlTest?> </td>
		    </tr>
		    <tr>
			<td>地址：</td><td> <?php echo $url?> </td>
			</tr>
       <?php
           }
		 }
	?>
	<tr>
	<td colspan="2"><input type="button" onClick="window.location.href='javascript:history.go(-1)'" value="返回规则">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onClick="window.location.href='?action=main'" value="返回列表"></td>
	</tr>
</tbody>
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
	
	$sql = "select * from {pre}cj_vod_projects ";
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount=ceil($nums/app_pagenum);//总页数
	$sql = $sql ."limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td>
		菜单：<a href="collect_vod_manage.php?action=add">添加采集规则</a> | <a href="collect_vod_change.php">分类转换</a> | <a href="collect_vod_filters.php">信息过滤</a> 
	</td>
	</tr>
</table>
<form action="" method="post" name="form1">
<table class="tb">
    <tr>
	  <td width="4%">&nbsp;</td>
      <td>项目名称</td>
      <td width="10%">播放类型</td>
      <td width="10%">入库分类</td>
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
      
	  <td><?php echo $row["p_playtype"]?></td>
	  <td>
	  <?php
	  	if ($row["p_classtype"] == 1){
	  		echo "<font color=red>自定义分类</font>";
	  }
	  	else{
	  		$typearr = getValueByArray($cache[0], "t_id", $row["p_collect_type"]);
	  		echo $typearr["t_name"];
	  	}
	  ?>
	  </td>
      <td><?php echo getColorDay($row['p_time']) ?></td>
 	  <td>
 	  <A href="collect_vod_cj.php?p_id=<?php echo  $row["p_id"] ?>">采集</A>｜
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
	<td  colspan="6">
	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'p_id[]');"/>&nbsp;
	<input type="submit" value="批量删除" onClick="if(confirm('确定要删除吗')){form1.action='?action=delall';}else{return false}"  class="input"/>
	<input type="submit" value="批量采集" onClick="if(confirm('确定要批量采集吗')){form1.action='collect_vod_cj.php?action=pl';}else{return false}"  class="input"/>
	<input type="button" value="导入规则" onClick="$('#win1').window('open');" class="input"/>
	</td>
	</tr>
    <tr align="center" bgcolor="#f8fbfb">
      <td colspan="7">
        <?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_vod_manage.php?page={p}") ?>
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