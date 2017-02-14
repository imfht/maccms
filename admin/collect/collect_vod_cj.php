<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("视频自定义采集");

$p_ids = be("get","p_id");
$num = be("get","num");
$listnum = be("get","listnum");
$viewnum = be("get","viewnum");
$sb = be("get","sb");
$cg = be("get","cg");

if (isn($num)){ $num =0;} else {$num = intval($num);}
if (isN($listnum)) { $listnum=0;} else {$listnum = intval($listnum);}
if (isN($viewnum)) { $viewnum=0;} else {$viewnum = intval($viewnum);}


if ($action=="pl" || strpos($p_ids,",")>0 ){
	if(isN($p_ids)) { $p_ids = be("arr","p_id"); }
	$arrid = explode(",",$p_ids);
	$arrcount = count($arrid) + 1;
	if ($num >= $arrcount) {
		dBreakpoint ("../../upload/vodbreakpoint");
		showmsg ("<font color='red'><b>批量采集完成</b></font>","collect_vod_manage.php");
	}
	$p_id = $arrid[$num];
}
else{
	$p_id = $p_ids;
}

if (isN($p_id)) { errmsg ("采集提示","采集项目ID不能为空!"); }

if ($sb=="" && $cg==""){
	$db->query ("update {pre}cj_vod_projects set p_time='".date('Y-m-d H:i:s',time())."' where p_id=".$p_id);
	$sb=0;
	$cg=0;
}

$sql = "select * from {pre}cj_vod_projects where p_id=".$p_id;
$row= $db->getRow($sql);

$p_id = $row["p_id"];
$p_name = $row["p_name"];
$p_coding = $row["p_coding"];
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

switch($p_pagetype)
{
	case 0 :
		if ($listnum < 1) { $strListUrl = $p_url;} else {$ListEnd = 1;}
		break;
	case 1:
	case 3:
		if ($p_collecorder ==1 ){
			if (($p_pagebatchid2-$listnum)< $p_pagebatchid1 || ($p_pagebatchid2-($listnum+1)) < 0){
				$ListEnd=1;
			}
			else{
				$strListUrl= replaceStr($p_pagebatchurl,"{ID}",($p_pagebatchid2-$listnum));
			}
		}
		else{
			if (($p_pagebatchid1+$listnum)> $p_pagebatchid2){
				$ListEnd=1;
			}
			else{
				$strListUrl=replaceStr($p_pagebatchurl,"{ID}",($p_pagebatchid1+$listnum));
			}
		}
		break;
	case 2:
		$ListArray=explode("|",$p_manualurl);
		if (($listnum)>count($ListArray)) {
			$ListEnd=1;
		}
		else{
			$strListUrl = $ListArray[$listnum];
		}
		break;
}

echo "采集中...  第" .($listnum+1). "页， 成功". $cg."条，失败". $sb."条";

switch($ListEnd)
{
	case 1:
		if ($action != "pl"){
			dBreakpoint ("../../upload/vodbreakpoint");
			showmsg ("采集完成","collect_vod_manage.php");
		}
		else{
			if ($num >= $arrcount){
				dBreakpoint ("../../upload/vodbreakpoint");
				showmsg ("批量采集完成","collect_vod_manage.php");
			}
			else{
			echo "此数据采集完毕 ---   暂停3秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=0&num=".($num+1)."&action=".$action."';}</script>";
			}
		}
		break;
	default:
		cjList();break;
}


$starringarr=array();
$titlearr=array();
$picarr=array();
$strdstate = "";

function cjList()
{
	global $listnum,$strListUrl,$p_pagetype,$p_collecorder,$p_listcodestart,$p_listcodeend,$p_listlinkstart,$p_listlinkend,$p_starringstart,$p_starringend,$p_titlestart,$p_titleend,$p_picstart,$p_picend,$p_starringtype,$p_titletype,$p_pictype,$p_coding,$p_showtype,$viewnum,$p_ids,$sb,$cg,$p_savefiles,$p_pagebatchid2,$p_pagebatchid1;
	global $starringarr,$titlearr,$picarr,$strdstate,$action,$p_pagebatchurl,$p_colleclinkorder;
	
	if (isN($_SESSION["strListCode"])) {
		$strListCode = getPage($strListUrl,$p_coding);
		$_SESSION["strListCode"] = $strListCode;
	}
	else{
		$strListCode = $_SESSION["strListCode"];
	}
	
	if ($strListCode == false) {
		echo "<tr><td colspan=\"2\">在获取:".$strListUrl."网页源码时发生错误！</TD></TR>";
		exit;
	}
	$listnum =$listnum+1; $tempStep = 1;
	
	
	switch($p_pagetype)
	{
		case 3:
			$strViewCode = $strListCode;
			$j = 1;
			if ($p_collecorder == 1) {
				$startnum = $p_pagebatchid2 ; $endnum = $p_pagebatchid1;
			}
			else{
				$startnum = $p_pagebatchid1 ; $endnum = $p_pagebatchid2;
			}
			if (!strpos($p_pagebatchurl,"{ID}")){
				$startnum=0; $endnum=0;
			}
			wtablehead();
			
			for ($i=$startnum ;$i<= $endnum;$i++)
			{
				$UrlTest = replaceStr($p_pagebatchurl,"{ID}",$i);
				echo "<tr><td colspan=\"2\"></TD>正在采集列表：".$UrlTest."的数据 </TR>";
				cjView($UrlTest,$i);
				$j = $j + 1;
			}
			wtablefoot();
			echo "<br> 此分页数据采集完毕 --- <script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_manage.php';}</script>";
			break;
		default:			
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
			
			if ($p_starringtype ==1) {
				$starringarr = getArray($strListCodeCut,$p_starringstart,$p_starringend);
			}
			if ($p_titletype ==1) {
				$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
			}
			if ($p_pictype ==1) {
				$picarrcode = getArray($strListCodeCut,$p_picstart,$p_picend);
			}
			
			if ($linkarrcode ==false) {
				echo "<tr><td colspan=\"2\"></TD>在获取链接列表时出错！</TR>";
				$sb = $sb+1;
				return;
			}
			
			wBreakpoint ("../../upload/vodbreakpoint",getUrl());
			
			$linkarr = explode("{Array}",$linkarrcode);
			if ($p_starringtype ==1) {
				$starringarr = explode("{Array}",$starringarrcode);
			}
			if ($p_titletype ==1) {
				$titlearr = explode("{Array}",$titlearrcode);
			}
			if ($p_pictype ==1) {
				$picarr = explode("{Array}",$picarrcode);
			}
			$viewcount = count($linkarr);
			if ($p_showtype==1) {
				if ($viewnum >= $viewcount){
					clearSession();
					echo "<br> 此分页数据采集完毕 ---   暂停2秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=".$listnum."&sb=".$sb."&cg=".$cg."&num=".$num."&action=".$action."';}</script>";
				}
				else{
					if ($p_savefiles==1){ $strdstate = "false"; }else{ $strdstate = "true"; }
					wtablehead();
					cjView($linkarr[$viewnum],$viewnum);
					wtablefoot();
					echo "数据采集完毕 --- 稍后继续采集<script language=\"javascript\">var dstate=".$strdstate.";setInterval(\"makeNextPage();\",500);function makeNextPage(){if(dstate){dstate=false;location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=".($listnum-1)."&sb=".$sb."&cg=".$cg."&num=".$num."&viewnum=".($viewnum+1)."&action=".$action."';}}</script>";
					exit;
				}
			}
			else{
				if($p_colleclinkorder==1){
					for ($i=$viewcount ;$i>=0;$i--){
						wtablehead();
						if ($i==$viewcount){
							echo "<tr><td colspan=\"2\"></TD>正在采集列表：".$strListUrl."的数据 </TR>";
						}
						cjView($linkarr[$i],$i);
						wtablefoot();
					}
				}
				else{
					for ($i=0 ;$i<count($linkarr);$i++){
						wtablehead();
						if ($i==0){
							echo "<tr><td colspan=\"2\"></TD>正在采集列表：".$strListUrl."的数据 </TR>";
						}
						cjView($linkarr[$i],$i);
						wtablefoot();
					}
				}
				clearSession();
				echo "<br> 此分页数据采集完毕 ---   暂停2秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=".$listnum."&sb=".$sb."&cg=".$cg."&num=".$num."&action=".$action."';}</script>";
			}
	}
}

function cjView($strlink,$num)
{
	global $db,$strListUrl,$p_titletype,$starringarr,$titlearr,$picarr,$p_id,$p_titlestart,$p_titleend,$p_lzstart,$p_lzend,$p_hitsstart,$p_hitsend,$p_starringtype,$p_starringstart,$p_starringend,$p_picstart,$p_picend,$p_typestart,$p_typeend,$p_pictype,$p_classtype,$p_collect_type,$p_timestart,$p_timeend,$p_areastart,$p_areaend,$p_contentstart,$p_contentend,$p_playcodestart,$p_playcodeend,$p_playlinkstart,$p_playlinkend,$p_playurlstart,$p_playurlend,$p_playcodetype,$p_playlinktype,$p_playtype,$p_coding,$p_lzstart,$p_lzend,$p_lzcodetype,$p_lzcodestart,$p_lzcodeend,$p_languagestart,$p_languageend,$p_remarksstart,$p_remarksend,$p_script,$p_showtype,$p_savefiles,$strdstate,$p_server,$p_setnametype,$p_setnamestart,$p_setnameend,$p_directedstart,$p_directedend,$cache,$cg,$p_playspecialtype,$p_playspecialrrul,$p_playspecialrerul;
	
	
	$strlink = definiteUrl($strlink,$strListUrl);
	$strViewCode = getPage($strlink,$p_coding);
	
	if ($strViewCode ==false) {
		$strdstate = "true";
		echo "<tr><td colspan=\"2\">在获取内容页时出错：".$strlink." </TD></TR>";
		$sb=$sb+1;
		return;
	}
	else{
		if ($p_titletype ==1) {
			$titlecode = $titlearr[$num];
		}
		else{
			$titlecode = getBody($strViewCode,$p_titlestart,$p_titleend);
		}
		
		$titlecode = filterScript($titlecode,$p_script);
		$titlecode = replaceFilters($titlecode,$p_id,1,0);
		$titlecode = trim($titlecode);
		
		$sql="select count(*) as cc from {pre}cj_vod where m_name='".$titlecode."'";
		$row=$db->getOne($sql);
		$rowcount = $row["cc"];
		
		if ($p_lzcodetype ==1){
			$lzfwcode = getBody($strViewCode,$p_lzcodestart,$p_lzcodeend);
			$lzcode = getBody($lzfwcode,$p_lzstart,$p_lzend);
			$lzcode = replaceStr($lzcode,"false","0");
			$lzcode = trim($lzcode);
			$lzcode = intval($lzcode);
		}
		else{
			$lzcode = getBody($strViewCode,$p_lzstart,$p_lzend);
			$lzcode = replaceStr($lzcode,"false","0");
			$lzcode = trim($lzcode);
			$lzcode = intval($lzcode);
		}
		
		if (($lzcode == 0) && ($rowcount>0)) {
			$strdstate = "true";
			echo "<tr><td colspan=\"2\">遇到重复电影数据跳过采集!</TD></TR>";
			return;
		}
		
		if (isN($p_hitsstart) || !isnum($p_hitsstart) ){ $p_hitsstart = 0 ;}
		if (isN($p_hitsend)  || !isnum($p_hitsend)) { $p_hitsend = 0 ;}
		if ($p_hitsstart ==0 && $p_hitsend ==0 ){ $m_hits = 0;} else {$m_hits = rand($p_hitsend,$p_hitsstart);}
		
		if ($p_starringtype ==1) {
			$starringcode = $starringarr[$num];
		}
		else{
			$starringcode = getBody($strViewCode,$p_starringstart,$p_starringend);
		}
		
		
		if ($p_pictype ==1) {
			$piccode = $picarr[$num];
		}
		else{
		 	$piccode = getBody($strViewCode,$p_picstart,$p_picend);
		}
		$piccode = definiteUrl($piccode,$strListUrl);
		
		if ($p_classtype ==1) {
			$typecode = filterScript(getBody($strViewCode,$p_typestart,$p_typeend),$p_script);
			$typecode = trim($typecode);
			$m_typeid = changeId($typecode,$p_id,0,0);
		}
		else{
			$typecode = $p_collect_type;
			$typecode = trim($typecode);
			$m_typeid = $p_collect_type;
			$typearr = getValueByArray($cache[0], "t_id" ,$typecode );
			$typecode = $typearr["t_name"];
		}
		$typecode = filterScript($typecode,$p_script);
		
		$starringcode = replaceStr($starringcode,"false","未知");
		$starringcode = filterScript($starringcode,$p_script);
		
		$directedcode = getBody($strViewCode,$p_directedstart,$p_directedend);
		$directedcode = replaceStr($directedcode,"false","未知");
		$directedcode = filterScript($directedcode,$p_script);
		
		$remarkscode = getBody($strViewCode,$p_remarksstart,$p_remarksend);
		$remarkscode = replaceStr($remarkscode,"false","");
		$remarkscode = filterScript($remarkscode,$p_script);
		
		$languagecode = getBody($strViewCode,$p_languagestart,$p_languageend);
		$languagecode = replaceStr($languagecode,"false","未知");
		$languagecode = filterScript($languagecode,$p_script);
		
		$timecode = getBody($strViewCode,$p_timestart,$p_timeend);
		$timecode = replaceStr($timecode,"false",date('Y-m-d',time()));
		$timecode = filterScript($timecode,$p_script);
		
		$areacode = getBody($strViewCode,$p_areastart,$p_areaend);
		$areacode = replaceStr($areacode,"false","未知");
		$areacode = filterScript($areacode,$p_script);
		
		$contentcode = getBody($strViewCode,$p_contentstart,$p_contentend);
		$contentcode = replaceStr($contentcode,"false","未知");
		$contentcode = filterScript($contentcode,$p_script);
		$contentcode = replaceFilters($contentcode,$p_id,2,0);
		
		$m_area = $areacode;
		$m_languageid = $languagecode;
	    
		if ($p_playcodetype ==1) {
			$playcode = getBody($strViewCode,$p_playcodestart,$p_playcodeend);
			
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
				$weburl = getArray($strViewCode,$p_playlinkstart,$p_playlinkend);
			}
			else{
				$weburl = getArray($strViewCode,$p_playurlstart,$p_playurlend);
			}
			if ($p_setnametype == 3) {
				$setnames = getArray($strViewCode,$p_setnamestart,$p_setnameend);
			}
		}
	
	if ($p_showtype==1) {
		echo "<tr><td  colspan=\"2\" align=\"center\">此列表中第".($num+1)."条数据采集结果</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</td></tr><tr><td >演员：</td><td >".$starringcode."</td></tr><tr><td >导演：</td><td >".$directedcode."</td></tr><tr><td >时间：</td><td >".$timecode."</td></tr><tr><td >分类：</td><td >".$typecode."</td></tr><tr><td >地区：</td><td >".$areacode."</td></tr><tr><td >语言：</td><td >".$languagecode."</td></tr><tr><td  >图片：</td><td >".$piccode."</td></tr><tr><td >介绍：</td><td >".substring($contentcode,50).".....</td></tr>";
	
		if ($p_savefiles ==1) {
			$filename = time() . $num;
			if (strpos($piccode,".jpg") || strpos($piccode,".bmp") || strpos($piccode,".png") || strpos($piccode,".gif")){
				$extName= substring($piccode,4,strlen($piccode)-4);
			}
			else{
				$extName=".jpg";
			}
			$picpath = "upload/vod/". getSavePicPath() . "/" ;
			$picfile = $filename . $extName;
			
			echo "<tr><td width=\"20%\" >自动下载图片：</td><td><iframe border=\"0\" valign=\"bottom\" vspace=\"0\" hspace=\"0\" marginwidth=\"0\" marginheight=\"0\" framespacing=\"0\" frameborder=\"0\" scrolling=\"no\" width=\"400\" height=\"15\" src=\"../admin_pic.php?action=downpic&wjs=1&path=../".$picpath."&file=".$picfile."&url=".$piccode."\"></iframe></td></tr>";
			$piccode = $picpath . $picfile;
		}
	}
	else{
		echo "<tr><td colspan=\"2\" align=\"center\">第".($num+1)."条数据采集结果</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td width=\"20%\" >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</td></tr>";
	}
	
	if ($weburl ==false) {
			echo "<tr><td colspan=\"2\">在获取播放列表链接时出错</TD></TR>";
			$sb=$sb+1;
			return;
	}
	else{
		$sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where m_urltest='".$strlink."' order by m_id desc";
		
		$rowvod=$db->getRow($sql);
		
	    if ($rowvod) {
			$cg=$cg+1;
			$movieid=$rowvod["m_id"];
			$sql = "update {pre}cj_vod set m_type='".$typecode."',m_area='".$areacode."',m_urltest='".$strlink."',m_name='".$titlecode."',m_starring='".$starringcode."',m_directed='".$directedcode."',m_year='".$timecode."',m_playfrom='".$p_playtype."',m_content='".$contentcode."',m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_pid='".$p_id."',m_typeid='".$m_typeid."',m_playserver='".$p_server."',m_state='".$lzcode."',m_language='".$languagecode."',m_remarks='".$remarkscode."' where m_id=".$rowvod["m_id"];
			
			$db->query($sql);
			
		}
		else{
			
			$cg=$cg+1;
			$sql="insert {pre}cj_vod (m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state,m_addtime,m_language,m_remarks) values('".$titlecode."','".$typecode."','".$areacode."','".$p_playtype."','".$starringcode."','".$directedcode."','".$piccode."','".$contentcode."','".$timecode."','".$strlink."','0','".$p_id."','".$m_typeid."','".$m_hits."','".$p_server."','".$lzcode."','".date('Y-m-d H:i:s',time())."','".$languagecode."','".$remarkscode."')";
			
 			$db->query($sql);
			$movieid= $db->insert_id();
		}
		
		$webArray=explode("{Array}",$weburl);
		$setnamesArray=explode("{Array}",$setnames);
		
		for ($i=0 ;$i< count($webArray);$i++){
			$WebTestx = $webArray[$i];
			
			if ($p_playspecialtype ==1 && strpos(",".$p_playspecialrrul,"[变量]")) {
					$Keyurl = explode("[变量]",$p_playspecialrrul);
					$urli = getBody ($WebTestx,$Keyurl[0],$Keyurl[1]);
				    if ($urli==False) { break; }
					$WebTestx = replaceStr($p_playspecialrerul,"[变量]",$urli);
			}
			
			if ($p_playlinktype == 1){
			    $WebTestx = definiteUrl($WebTestx,$strlink);
			    $playCode = getPage($WebTestx,$p_coding);
			    $url = getBody($playCode,$p_playurlstart,$p_playurlend);
			}
			else if($p_playlinktype == 2){
				if (isN($p_playurlend)){
					$tmpA = strpos($WebTestx, $p_playurlstart);
                	$url = substr($WebTestx,strlen($WebTestx)-$tmpA-strlen($p_playurlstart)+1);
				}
				else{
					$url = getBody($WebTestx,$p_playurlstart,$p_playurlend);
				}
				
			}
			else if($p_playlinktype == 3){
				$WebTestx = definiteUrl($WebTestx,$strlink);
				$playCode = getPage($WebTestx,$p_coding);
				$tmpB = getArray($webCode,$p_playurlstart,$p_playurlend);
				$tmpC = explode("$Array$",$tmpB);
				foreach($tmpC as $tmpD){
					$sql="SELECT {pre}vod_url.u_url FROM ({pre}vod_url INNER JOIN {pre}vod ON {pre}vod_url.u_movieid = {pre}vod.m_id)  where {pre}vod_url.u_url='" . $tmpD . "' and {pre}vod.m_pid=" . $p_id;
     		   		$row = $db->getRow($sql);
			   		if(!$row){
			   			$strTempUrl = $strTempUrl . $tmpD . "<br>";
					  	$db->query( "insert into {pre}vod_url(u_url,u_movieid) values('".$tmpD."','".$movieid."')");
			   		}
				}
				break;
			}
			else{
				$url= $WebTestx;
			}
				
			   $url = replaceFilters($url,$p_id,3,0);
			   if ($p_setnametype == 1){
					$setname = getBody($url,$p_setnamestart,$p_setnameend);
					$url = $setname . "$" . $url;
			   }
			   else if($p_setnametype == 1 && $p_playlinktype ==1) {
					$setname = getBody($playCode,$p_setnamedtart,$p_setnameend);
					$url = $setname ."$" .$url;
				}
				else if($p_setnametype == 3){
					$url = $setnamesArray[$i] . "$" .$url;
				}
			   $sql="SELECT {pre}cj_vod_url.u_url FROM ({pre}cj_vod_url INNER JOIN {pre}cj_vod ON {pre}cj_vod_url.u_movieid = {pre}cj_vod.m_id)  where {pre}cj_vod_url.u_url='" . $url . "' and {pre}cj_vod.m_pid=" . $p_id;
			   
     		   $rowurl = $db->getRow($sql);
			   if (!$rowurl) {
				   if ($p_playlinktype ==1) {
					  $strTempUrl .=  $url . "<br>";
					  $url = replaceStr($url,"'","''");
					  $db->query("insert into {pre}cj_vod_url(u_url,u_movieid,u_weburl) values('".$url."','".$movieid."','".$WebTestx."')");
					}
				   else{
					  $strTempUrl .= $url . "<br>";
					  $db->query("insert into {pre}cj_vod_url(u_url,u_movieid) values('".$url."','".$movieid."')");
				   }
			   }
		}
	}
	 
	}
}

function wtablehead()
{
?>
<TABLE width="96%" border=0 align=center cellpadding="4" cellSpacing=1 class=tb >
<TBODY>
<?php
}

function wtablefoot()
{
?>
</TBODY>
</TABLE>
<?php
}
?>