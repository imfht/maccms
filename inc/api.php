<?php
require_once ("conn.php");
require_once ("360_safe3.php");
if (app_api==0){ echo "closed"; exit; }

$action = be("get","ac");
$rtype = be("get","t");
$rpage = be("get","pg");
$rkey = be("get","wd"); $rkey = chkSql($rkey,true);
$rday= be("get","h");

if (!isNum($rtype)) { $rtype=0;} else { $rtype= intval($rtype);}
if (!isNum($rpage)) { $rpage=1;} else { $rpage= intval($rpage);}
if ($rpage < 1){ $rpage=1;}
if (!isNum($rday)) { $rday=0;} else { $rday= intval($rday);}

$app_apiver="5.0";
$apicp=10;

switch($action)
{
	case "videolist":
		cj();
		break;
	default:
		vlist();
		break;
}

function cj()
{
	global $db,$template,$cache,$rtype,$rpage,$rkey,$rday,$action,$apicn,$app_apiver;
	$ids= be("all","ids");
	$ids = chkSql($ids,true);
	
	$apicn = "maccmsapi-videolist-" . $rtype . "-" . $rpage . "-" . $rkey . "-" . $rday . "-" . str_replace(",","",$ids); ;
	if (chkCache($apicn)){
		echo getCache($apicn);
		exit;
	}
	
	$xmla = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	$xmla .= "<rss version=\"".$app_apiver."\">";
	
	$sql = "select * from {pre}vod where 1=1 ";
	$sql1 = "select count(*) from {pre}vod where 1=1 ";
	
	if($ids!=""){
		$sql .= " AND d_id in (". $ids .")";
		$sql1 .= " AND d_id in (". $ids .")";
	}
	if($rtype>0){
		$sql .= " AND d_type =".$rtype;
		$sql1 .= " AND d_type =".$rtype;
	}
	if($rday>0){
		if (!isNum($rday)){ $rday=1; }
		$whereStr=" AND d_time > date_sub(now(),interval ".$rday." hour) " ;
		$sql .=  $whereStr;
		$sql1 .= $whereStr;
	}
	
	$nums = $db->getOne($sql1);
	$pagecount=ceil($nums/app_apipagenum);
	$sql = $sql ." limit ".(app_apipagenum * ($rpage-1)).",".app_apipagenum;
	$rs = $db->query($sql);
	if (!$rs){
		echo "err：" . "<br>" .$sql;exit;
	}
	else{
		$xml .= "<list page=\"".$rpage."\" pagecount=\"".$pagecount."\" pagesize=\"".app_apipagenum."\" recordcount=\"".$nums."\">";
		
		while ($row = $db ->fetch_array($rs))
		{
			$tempurl = urlDeal($row["d_playurl"],$row["d_playfrom"]);
		    if (strpos(",".$row["d_pic"],"http://")>0) { $temppic = $row["d_pic"]; } else { $temppic = app_apicjflag . $row["d_pic"]; }
		    
		    $typearr = getValueByArray($cache[0], "t_id", $row["d_type"]);
			$plink = app_siteurl . $template->getVodPlayUrl($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"],1,1);
				
		    $xml .= "<video>";
		    $xml .= "<last>".$row["d_time"]."</last>";
			$xml .= "<id>".$row["d_id"]."</id>";
			$xml .= "<tid>".$row["d_type"]."</tid>";
			$xml .= "<name><![CDATA[".$row["d_name"]."]]></name>";
			$xml .= "<type>".$typearr["t_name"]."</type>";
			$xml .= "<pic>".$temppic."</pic>";
			$xml .= "<lang>".$row["d_language"]."</lang>";
			$xml .= "<area>".$row["d_area"]."</area>";
			$xml .= "<year>".$row["d_year"]."</year>";
			$xml .= "<state>".$row["d_state"]."</state>";
			$xml .= "<note><![CDATA[".$row["d_remarks"]."]]></note>";
			$xml .= "<actor><![CDATA[".$row["d_starring"]."]]></actor>";
			$xml .= "<director><![CDATA[".$row["d_directed"]."]]></director>";
			$xml .= "<dl>".$tempurl."</dl>";
			$xml .= "<des><![CDATA[".$row["d_content"]."]]></des>";
			//$xml .= "<vlink><![CDATA[".$vlink."]]></vlink>";
			$xml .= "<reurl><![CDATA[".$plink."]]></reurl>";
			$xml .= "</video>";
		}
		$xml .= "</list>";
	}
	unset($rs);
	$xmla .= $xml . "</rss>";
	setCache ($apicn,$xmla,0);
	echo $xmla;
}

function vlist()
{
	global $db,$template,$cache,$rtype,$rpage,$rkey,$app_apiver,$apicn,$apicp,$rday;
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	$xml .= "<rss version=\"".$app_apiver."\">";
	
	$apicn = "maccmsapi-list-" . $rtype . "-" . $rpage . "-" . $rkey . "-" . $rday ;
	if (chkCache($apicn)){
		echo getCache($apicn);
		exit;
	}
	
	//视频列表开始
	if (maccms_field_vod_source !="") {
		$tempmaccms_field_vod_source = ",".maccms_table_vod.".".maccms_field_vod_source;
	}
	
	$sql = "select d_id,d_name,d_enname,d_type,d_time,d_remarks,d_playfrom,d_addtime from {pre}vod where 1=1 ";
	$sql1 = "select count(*) from {pre}vod where 1=1 ";
	
	if ($rtype > 0) { $where .= " and d_type=" . $rtype; }
	if (app_apivodfilter != "") { $where .= " ". app_apivodfilter." "; }
	if ($rkey !="") { $where .= " and d_name like '%".$rkey."%' "; }
	$sql .= $where. " order by d_time desc";
	$sql1 .= $where;
	
	$nums= $db -> getOne($sql1);
	$pagecount=ceil($nums/app_apipagenum);
	$sql = $sql ." limit ".(app_apipagenum * ($rpage-1)).",".app_apipagenum;
	$rs = $db->query($sql);	
	if (!$rs){
		$nums=0;
		echo "err：" . "<br>" .$sql;exit;
	}
	
	if($nums==0){
		$xml .= "<list page=\"".$rpage."\" pagecount=\"0\" pagesize=\"".app_apipagenum."\" recordcount=\"0\">";
	}
	else{
		$xml .= "<list page=\"".$rpage."\" pagecount=\"".$pagecount."\" pagesize=\"".app_apipagenum."\" recordcount=\"".$nums."\">";
		
		while ($row = $db ->fetch_array($rs))
	  	{
	  		$typearr = getValueByArray($cache[0], "t_id", $row["d_type"]);
			$plink = app_siteurl . $template->getVodPlayUrl($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"],1,1);
			
			$xml .= "<video>";
			$xml .= "<last>".$row["d_time"]."</last>";
			$xml .= "<id>".$row["d_id"]."</id>";
			$xml .= "<tid>".$row["d_type"]."</tid>";
			$xml .= "<name><![CDATA[".$row["d_name"]."]]></name>";
			$xml .= "<type>".$typearr["t_name"]."</type>";
			$xml .= "<dt>".replaceStr($row["d_playfrom"],'$$$',',')."</dt>";
			$xml .= "<note><![CDATA[".$row["d_remarks"]."]]></note>";
			//$xml .= "<vlink><![CDATA[".$vlink."]]></vlink>";
			$xml .= "<reurl><![CDATA[".$plink."]]></reurl>";
			$xml .= "</video>";
	  	}
	}
	unset($rs);
	$xml .= "</list>";
	//视频列表结束
	
	//分类列表开始
	$xml .= "<class>";
	$sql = "select * from {pre}vod_type where 1=1 ";
	if (app_apitypefilter != "") { $sql .= app_apitypefilter ; }
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs))
	{
		$xml .= "<ty id=\"". $row["t_id"] ."\">". $row["t_name"] . "</ty>";
	}
	unset($rs);
	$xml .= "</class>";
	//分类列表结束
	
	$xml .= "</rss>";
	if ($rpage<=$apicp){
		setCache ($apicn,$xml,0);
	}
	echo $xml;
}

function urlDeal($urls,$froms)
{
	$arr1 = explode("$$$",$urls); $arr1count = count($arr1);
	$arr2 = explode("$$$",$froms); $arr2count = count($arr2);
	for ($i=0;$i<$arr2count;$i++){
		if ($arr1count >= $i){
			$str = $str . "<dd flag=\"". $arr2[$i] ."\"><![CDATA[" . $arr1[$i]. "]]></dd>";
		}
	}
	$str = replaceStr($str,chr(10),"#");
	$str = replaceStr($str,chr(13),"#");
	$str = replaceStr($str,"##","#");
	return $str;
}
?>