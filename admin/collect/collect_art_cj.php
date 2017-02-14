<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("文章自定义采集");

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
		dBreakpoint ("../../upload/artbreakpoint");
		showmsg ("<font color='red'><b>批量采集完成</b></font>","collect_art_manage.php");
	}
	$p_id = $arrid[$num];
}
else{
	$p_id = $p_ids;
}

if (isN($p_id)) { errmsg ("采集提示","采集项目ID不能为空!");}

if ($sb=="" && $cg==""){
	$db->query ("update {pre}cj_art_projects set p_time='".date('Y-m-d H:i:s',time())."' where p_id=".$p_id);
	$sb=0;
	$cg=0;
}

$sql = "select * from {pre}cj_art_projects where p_id=".$p_id;
$row= $db->getRow($sql);

$p_id = $row["p_id"];
$p_name = $row["p_name"];
$p_coding = $row["p_coding"];
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
$p_cpagecodestart = $row["p_cpagecodestart"];
$p_cpagecodeend = $row["p_cpagecodeend"];
$p_cpagetype = $row["p_cpagetype"];
$p_cpagestart = $row["p_cpagestart"];
$p_cpageend = $row["p_cpageend"];

$starringarr=array();
$titlearr=array();


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
		if ($action != "pl") {	
			dBreakpoint ("../../upload/artbreakpoint");
			showmsg ("<font color='red'><b>采集完成</b></font>","collect_art_manage.php");
		}
		else{
			if ($num >= $arrcount) {
				dBreakpoint ("../../upload/artbreakpoint");
				showmsg ("<font color='red'><b>批量采集完成</b></font>","collect_art_manage.php");
			}
			else{
			echo "<br> 此项目数据采集完毕 ---   暂停1秒后继续采集<a href=\"?action=collect_art_cj.php?p_id=".$p_ids."&listnum=0&num=".($num+1)."&action=".$action."\">点击采集下一页</a>";
			echo "<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_art_cj.php?p_id=".$p_ids."&listnum=0&num=".($num+1)."&action=".$action."';}</script>";
			}
		}
		break;
	default:
	cjList();
	
}

function cjList()
{
	global $listnum,$strListUrl,$p_pagetype,$p_collecorder,$p_listcodestart,$p_listcodeend,$p_listlinkstart,$p_listlinkend,$p_authorstart,$p_authorend,$p_titlestart,$p_titleend,$p_authortype,$p_titletype,$p_pictype,$p_coding,$p_showtype,$viewnum,$p_ids,$sb,$cg,$action,$starringarr,$titlearr;
	
	if (isN($_SESSION["strListCodeart"])) {
		$strListCode = getPage($strListUrl,$p_coding);
		$_SESSION["strListCodeart"] = $strListCode;
	}
	else{
		$strListCode = $_SESSION["strListCodeart"];
	}
	
	if ($strListCode == false) {
		echo "<tr><td vAlign=center class=\"tdxingmu\" colspan=\"2\">在获取:".$strListUrl."网页源码时发生错误！</td></tr>";
		return;
	}
	$listnum =$listnum+1;
	$tempStep = 1;
	
	
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
			wtablehead();
			for ($i=$startnum ;$i< $endnum;$i++)
			{
				$UrlTest = replaceStr($p_pagebatchurl,"{ID}",$i);
				echo "<tr><td vAlign=center colspan=\"2\"></td>正在采集列表：".$UrlTest."的数据 </tr>";
				cjView($UrlTest,$j);
				$j = $j + 1;
			}
			wtablefoot();
			break;
		default:
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
			
			if ($p_authortype ==1) {
				$starringarr = getArray($strListCodeCut,$p_authorstart,$p_authorend);
			}
			if ($p_titletype ==1) {
				$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
			}
			
			
			if ($linkarrcode ==False) {
				echo "<tr><td vAlign=center class=\"tdxingmu\" colspan=\"2\"></td>在获取链接列表时出错！</tr>";
				$sb = $sb+1;
				return;
			}
			
			wBreakpoint ("../../upload/artbreakpoint",getUrl());
			
			$linkarr = explode("{Array}",$linkarrcode);
			if ($p_authortype ==1) {
				$starringarr = explode("{Array}",$starringarrcode);
			}
			if ($p_titletype ==1) {
				$titlearr = explode("{Array}",$titlearrcode);
			}
			
			$viewcount = count($linkarr);
			if ($p_showtype==1) {
				if ($viewnum >= $viewcount){
					clearSessionart();
					echo "<br> 此分页数据采集完毕 ---   暂停2秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_art_cj.php?p_id=".$p_ids."&listnum=".$listnum."&sb=".$sb."&cg=".$cg."&num=".$num."&action=".$action."';}</script>";
				}
				else{
					if ($p_savefiles==1){ $strdstate = "false"; }else{ $strdstate = "true"; }
					wtablehead();
					cjView($linkarr[$viewnum],$viewnum);
					wtablefoot();
					echo "数据采集完毕 --- 稍后继续采集<script language=\"javascript\">var dstate=".$strdstate.";setInterval(\"makeNextPage();\",500);function makeNextPage(){if(dstate){dstate=false;location.href='collect_art_cj.php?p_id=".$p_ids."&listnum=".($listnum-1)."&sb=".$sb."&cg=".$cg."&num=".$num."&viewnum=".($viewnum+1)."&action=".$action."';}}</script>";
					exit;
				}
			}
			else{
				for ($i=0 ;$i<count($linkarr);$i++)
				{
					//if ($i > 0)  { die(""); break; exit;}
					wtablehead();
					if ($i==0){
						echo "<tr><td vAlign=center class=\"tdxingmu\" colspan=\"2\"></td>正在采集列表：".$strListUrl."的数据 </tr>";
					}
					cjView($linkarr[$i],$i);
					wtablefoot();
				}
				clearSessionart();
				echo "<br> 此分页数据采集完毕 ---   暂停2秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_art_cj.php?p_id=".$p_ids."&listnum=".$listnum."&sb=".$sb."&cg=".$cg."&num=".$num."&action=".$action."';}</script>";
			}
	}
}

function cjView($strlink,$num)
{
	global $db,$strListUrl,$p_titletype,$starringarr,$titlearr,$p_id,$p_titlestart,$p_titleend,$p_hitsstart,$p_hitsend,$p_authortype,$p_authorstart,$p_authorend,$p_typestart,$p_typeend,$p_classtype,$p_collect_type,$p_timestart,$p_timeend,$p_contentstart,$p_contentend,$p_cpagecodestart,$p_cpagecodeend,$p_cpagetype,$p_cpagestart,$p_cpageend,$p_coding,$p_script,$p_showtype,$sb,$cg,$cache;
	
	$strlink = definiteUrl($strlink,$strListUrl);
	$strViewCode = getPage($strlink,$p_coding);
	
	if ($strViewCode ==False) {
		echo "<tr><td vAlign=center class=\"tdxingmu\" colspan=\"2\">在获取内容页时出错：".$strlink." </td></tr>";
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
		
		$sql="select count(*) from {pre}cj_art where m_title='".$titlecode."'";
		$rowcount=$db->getOne($sql);
		
		if (intval($rowcount>0)){
			echo "<tr><td vAlign=center class=\"tdxingmu\" colspan=\"2\">遇到重复文章数据跳过采集!</td></tr>";
			return;
		}
		
		if (isN($p_hitsstart) || !isnum($p_hitsstart) ){ $p_hitsstart = 0 ;}
		if (isN($p_hitsend)  || !isnum($p_hitsend)) { $p_hitsend = 0 ;}
		if ($p_hitsstart ==0 && $p_hitsend ==0 ){ $m_hits = 0;} else {$m_hits = rand($p_hitsend,$p_hitsstart);}
		
		if ($p_authortype ==1) {
			$starringcode = $starringarr[$num];
		}
		else{
			$starringcode = getBody($strViewCode,$p_authorstart,$p_authorend);
		}
		$starringcode = replaceStr($starringcode,"false","未知");
		$starringcode = filterScript($starringcode,$p_script);
		
		if ($p_classtype ==1) {
			$typecode = filterScript(getBody($strViewCode,$p_typestart,$p_typeend),$p_script);
			$typecode = trim($typecode);
			$m_typeid = changeId($typecode,$p_id,1);
		}
		else{
			$typecode = $p_collect_type;
			$typecode = trim($typecode);
			$m_typeid = $p_collect_type;
			$typearr = getValueByArray($cache[1], "t_id" ,$typecode );
			$typecode = $typearr["t_name"];
		}
		$typecode = filterScript($typecode,$p_script);
		
		$timecode = getBody($strViewCode,$p_timestart,$p_timeend);
		$timecode = replaceStr($timecode,"false",date('Y-m-d',time()));
		$timecode = filterScript($timecode,$p_script);
		
		
		$contentcode = getBody($strViewCode,$p_contentstart,$p_contentend);
		$cpagecode = getBody($strViewCode,$p_cpagecodestart,$p_cpagecodeend);
		$contentcode = replaceStr($contentcode,$cpagecode,"");
		
		if ($p_cpagetype==1){
			$cpagelinkarrcode= getArray($cpagecode,$p_cpagestart,$p_cpageend);
			$cpagelinkarr = explode("{Array}",$cpagelinkarrcode);
			for ($i=0;$i<count($cpagelinkarr);$i++){
				$cpagelink = $cpagelinkarr[$i];
				if ($cpagelink != "" && $cpagelink!="#"){
					$cpagelink = definiteUrl($cpagelink,$strListUrl);
					$cpagelinkcode = getPage($cpagelink,$p_coding);
					if ($cpagelinkcode != "false"){
						$cpagecode = getBody($cpagelinkcode,$p_cpagecodestart,$p_cpagecodeend);
						$cpagelinkcode = getBody($cpagelinkcode,$p_contentstart,$p_contentend);
						$cpagelinkcode = replaceStr($cpagelinkcode,$cpagecode,"");
						$contentcode = $contentcode . $cpagelinkcode;
					}
				}
			}
		}
		$contentcode = replaceFilters($contentcode,$p_id,2,0);
		
	echo "<tr><td colspan=\"2\" align=\"center\">此列表中第".($num+1)."条数据采集结果</td></tr><tr><td vAlign=center width=\"20%\">来源地址：</td><td class=\"tdback\">".$strlink."</td></tr><td>文章标题：</td><td>".$titlecode."</td></tr><td>文章作者：</td><td>".$starringcode."</td></tr><td>发布时间：</td><td>".$timecode."</td></tr><td>分类：</td><td>".$typecode."</td></tr> <td>内容：</td><td>".$contentcode."</td></tr>";

		$sql="select m_id,m_title,m_type,m_author,m_content,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits from {pre}cj_art where m_urltest='".$strlink."' order by m_id desc";
		$rowart=$db->getRow($sql);
	    if ($rowart) {
			$cg=$cg+1;
			$movieid=$rowart["m_id"];
			$sql = "update {pre}cj_art set m_type='".$typecode."',m_urltest='".$strlink."',m_title='".$titlecode."',m_author='".$starringcode."',m_content='".$contentcode."',m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_pid='".$p_id."',m_typeid='".$m_typeid."' where m_id=".$rowart["m_id"];
			
			$db->query($sql);
		}
		else{
			$cg=$cg+1;
			$sql="insert {pre}cj_art (m_title,m_type,m_author,m_content,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_addtime) values('".$titlecode."','".$typecode."','".$starringcode."','".$contentcode."','".$strlink."','0','".$p_id."','".$m_typeid."','".$m_hits."','".date('Y-m-d H:i:s',time())."')";
			
 			$status = $db->query($sql);
			$movieid=$db->insert_id();
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