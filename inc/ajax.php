<?php
require_once ("conn.php");
require_once ("360_safe3.php");

$action = be("get", "action");
$id = be("get", "id");
$id = chkSql($id, true);

switch($action)
{
	case "vodhit":
	case "arthit": gethit();break;
	case "goodnum":
	case "badnum": goodbad();break;
	case "vodgood":
	case "vodbad": voddigg();break;
	case "diypage": diypage();break;
	case "desktop": desktop();break;
	case "score": score();break;
	case "getscore": getscore();break;
	case "userfav": userfav();break;
	case "reporterr": reporterr();break;
	case "suggest": suggest();break;
	default: main();break;
}
dispseObj();

function main()
{}

function goodbad()
{
	global $db,$action,$id;
	if (!isNum($id)){ echo "err"; exit;}
	$row = $db->getRow("SELECT d_good,d_bad FROM {pre}vod WHERE d_id=" . $id);
	if ($row){
		if ($action == "goodnum"){
			$res = $row["d_good"];
		}
		else{
			$res = $row["d_bad"];
		}
	}
	unset ($row);
	echo $res;
}

function voddigg()
{
	global $db,$action,$id;
	
	if (!isNum($id)){ echo "err"; exit;}
	if (getCookie("digg_" . $action . $id) == "ok"){ echo "haved";exit;}
	$row = $db->getRow("SELECT d_good,d_bad FROM {pre}vod WHERE d_id=" . $id);
	if ($row){
		if ($action == "vodgood"){
			$col = "d_good";
			$res = $row["d_good"] + 1;
		}
		else{
			$col = "d_bad";
			$res = $row["d_bad"] + 1;
		}
		$db->Update ("{pre}vod",array($col),array($res),"d_id=".$id);
	}
	unset ($row);
	sCookie ("digg_" . $action . $id, "ok");
	echo $res;
}

function gethit()
{
	global $db,$action,$id;

	if (!isNum($id)){ echo "err"; exit;}
	$res = 0;
	$sday = date('Y-m-d',time());
	$smonth = date('Y-m',time());
	if ($action == "arthit"){
		$row = $db->getRow("SELECT a_hits,a_dayhits,a_weekhits,a_monthhits,a_hitstime FROM {pre}art WHERE a_id=" . $id);
		if($row){
			$a_hits=$row["a_hits"];
			$a_dayhits=$row["a_dayhits"];
			$a_weekhits=$row["a_weekhits"];
			$a_monthhits=$row["a_monthhits"];
			if(file_exists(root."inc/hitstimeart.txt")){
				$hitstime = @file_get_contents( root."inc/hitstimeart.txt" );
			}
			if(!isN(hitstime)){
				if ( date('m',time()) !=  date('m',strtotime($hitstime)) ){
					$db->Update ("{pre}art",array("a_monthhits"),array(0),"a_id>0");
					$a_monthhits;
				}
				if (  date('W',time()) !=  date('W',strtotime($hitstime)) ){
					$db->Update ("{pre}art",array("a_weekhits"),array(0),"a_id>0");
					$a_weekhits=0;
				}
				if ( date('d',time()) !=  date('d',strtotime($hitstime) ) ){
					$db->Update ("{pre}art",array("a_dayhits"),array(0),"a_id>0");
					$a_dayhits=0;
				}
			}
			$res = $a_hits+1;
			$res1 = $a_dayhits + 1;
			$res2 = $a_weekhits + 1;
			$res3 = $a_monthhits + 1;
			$db->Update ("{pre}art",array("a_hitstime","a_hits","a_dayhits","a_weekhits","a_monthhits"),array(date('Y-m-d H:i:s'),$res,$res1,$res2,$res3),"a_id=".$id);
			@fwrite(fopen(root."inc/hitstimeart.txt","wb"),date('Y-m-d H:i:s',time()));
		}
	}
	else{
		$row = $db->getRow("SELECT d_hits,d_dayhits,d_weekhits,d_monthhits,d_hitstime FROM {pre}vod WHERE d_id=". $id);
		if($row){
			$d_hits=$row["d_hits"];
			$d_dayhits=$row["d_dayhits"];
			$d_weekhits=$row["d_weekhits"];
			$d_monthhits=$row["d_monthhits"];
			if(file_exists(root."inc/hitstimevod.txt")){
				$hitstime = @file_get_contents( root."inc/hitstimevod.txt");
			}
			if(!isN($hitstime)){
				if ( date('m',time()) !=  date('m',strtotime($hitstime)) ){
					$db->Update ("{pre}vod",array("d_monthhits"),array(0),"d_id>0");
					$d_monthhits=0; 
				}
				if (  date('W',time()) !=  date('W',strtotime($hitstime)) ){
					$db->Update ("{pre}vod",array("d_weekhits"),array(0),"d_id>0");
					$d_weekhits=0;
				}
				if ( date('d',time()) !=  date('d',strtotime($hitstime) ) ){
					$db->Update ("{pre}vod",array("d_dayhits"),array(0),"d_id>0");
					$d_dayhit=0;
				}
			}
			$res = $d_hits + 1;
			$res1 = $d_dayhits + 1;
			$res2 = $d_weekhits + 1;
			$res3 = $d_monthhits + 1;
			$db->Update ("{pre}vod",array("d_hitstime","d_hits","d_dayhits","d_weekhits","d_monthhits"),array(date('Y-m-d H:i:s'),$res,$res1,$res2,$res3),"d_id=".$id);
			@fwrite(fopen(root."inc/hitstimevod.txt","wb"),date('Y-m-d H:i:s',time()));
		}
	}
	unset($row);
	echo $res;
}

function diypage()
{
	global $template;
	$path = be("get", "path");
	$cacheName = "template_diypage_" . $path;
	if (chkCache($cacheName)){
		$template->html = getCache($cacheName);
	}
	else{
		$path = root. path;
		$template->html = file_get_contents($path);
		$template->mark();
		setCache ($cacheName, $template->html,0);
		$template->run ("vod");
	}
	echo $template->html;
}
    
function desktop()
{
	$url = be("get", "url");
	$name = strip_tags(be("get", "name"));
	$rc = false;
	
	if (isN($name)){
		$rc = true;
		$name = app_sitename;
	}
	
	if($rc){
		$url = "http://" . app_siteurl;
	}
	if (strpos($url,"ttp://")>0){
			
	}
	else{
		$url = "http://" . app_siteurl . $url;
	}
	$Shortcut = "[InternetShortcut]
	URL=".$url."
	IDList=
	IconIndex=1
	[{000214A0-0000-0000-C000-000000000046}]
	Prop3=19,2";
	Header("Content-type: application/octet-stream");
	if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")){
 		Header("Content-Disposition: attachment; filename=". urlencode($name) .".url;");
 	}
 	else{
 		Header("Content-Disposition: attachment; filename=". $name .".url;");
 	}
	echo $Shortcut;
}
function getscore()
{
	global $db,$action,$id;
	$ac2=be("get","ac2");
	$ac3=be("get","ac3");
	$res=0;
	if($ac2=="count"){
		$res = $db->getOne("SELECT d_scorecount FROM {pre}vod WHERE d_id=" .$id);
	}
	else if($ac2=="pjf"){
		$row = $db->getRow("SELECT d_score,d_scorecount FROM {pre}vod WHERE d_id=" .$id);
		if ($row){
			$d_scorecount = $row["d_scorecount"];
			if($d_scorecount==0) { $d_scorecount=1; }
			$res = round( $row["d_score"] / $d_scorecount ,1);
			if($ac3=="all"){
				$res = $row["d_score"] . "," . $d_scorecount . "," .  round( $row["d_score"] / $d_scorecount ,1);
			}
		}
		unset ($row);
	}
	else{
		$res = $db->getOne("SELECT d_score FROM {pre}vod WHERE d_id=" .$id);
	}
	echo $res;
}
function score()
{
	global $db,$action,$id;
	$score = be("get", "score");
	$score = chkSql($score, true);
	$ac3 = be("get","ac3");
	if (getCookie("vodscore_" . $id) == "ok"){ echo "haved";exit;}
	if (!isNum($id)){ echo "err";exit;}
	if (!isNum($score)){ $score = 0;} else { $score = intval($score); }
	if ($score < 0) { $score = 0;}
	if ($score > 10) { $score = 10;}
	$res = 0;
	$row = $db->getRow("SELECT d_score,d_scorecount FROM {pre}vod WHERE d_id=" .$id);
	if ($row){
		$d_score = $row["d_score"] + $score;
		$d_scorecount = $row["d_scorecount"] + 1;
		$db->Update ("{pre}vod",array("d_score","d_scorecount"),array($d_score,$d_scorecount),"d_id=".$id);
		$res = round( $d_score / $d_scorecount ,1);
		if($ac3=="all"){
			$res = $d_score . "," . $d_scorecount . "," .  round( $d_score / $d_scorecount ,1);
		}
	}
	unset ($row);
	sCookie ("vodscore_" . $id, "ok");
	echo $res;
}
function userfav()
{
	global $db,$id;
	if (!isNum($id)){ echo "err";exit;}
	if (isN($_SESSION["userid"])) { echo "login";exit; }
	
	$res = "err";
	$row = $db->getRow("select * from {pre}user where u_id = " . $_SESSION["userid"]);
	if ($row){
		$u_fav = $row["u_fav"];
		
		if (isN($u_fav)){
			$u_fav = ",". $id . ",";
			$res = "ok";
		}
		else{
			if (strpos( ",".$u_fav ,",".$id.",")>0){
				$res = "haved";
			}
			else{
				$u_fav = $u_fav . $id . ",";
				$res = "ok";
			}
		}
		$db->Update ("{pre}user",array("u_fav"),array($u_fav),"u_id=".$_SESSION["userid"]);
	}
	unset($row);
	echo $res;
}

function reporterr()
{
	global $db; 
	$g_vid = be("post","g_vid"); $g_vid = chkSql($g_vid, true);
	$g_name = be("post","g_name"); $g_name = chkSql($g_name, true); 
	$g_content = be("post","g_content"); $g_content = chkSql($g_content, true);
	if (!isNum($g_vid)){ echo "请填写正确参数";exit; }
	if (app_gbookaudit==1){ $g_audit=0;} else { $g_audit=1;}
	if (strlen($g_name) >64){ $g_name = substring($g_name,64);}
	if (strlen($g_content) >255){ $g_content = substring($g_content,255);}
	$g_ip = getIP();
    $g_time = date('Y-m-d H:i:s',time());
	if (getTimeSpan("lastreportErrTime") < app_gbooktime){ echo "请稍后再试";exit;}
	$db->Add ("{pre}gbook", array("g_vid","g_audit","g_name", "g_ip", "g_time", "g_content"), array($g_vid, $g_audit, $g_name, $g_ip, $g_time, $g_content));
	$_SESSION["lastreportErrTime"] = time();
	echo "报错成功,多谢支持!<script>setTimeout(\"window.close()\",1000)</script>";
}

function suggest()
{
	global $db; 
	$q=be("get","q");
	$res = '{"status":0,"info":"err","data":[{}]}';
	if(!empty($q)){
		$rs = $db->queryArray("SELECT d_name from {pre}vod WHERE d_name like '".$q."%' or d_enname like '".$q."%' ",false);
		if($rs){
			echo '{"status":1,"info":"ok","data":'. json_encode($rs) . '}';
			return;
		}
		unset($rs);
	}
	echo $res;
}
?>