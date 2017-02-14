<?php
require_once ("../../inc/conn.php");
require_once ("../../inc/360_safe3.php");
$action = be("get", "action");
$d_vid = be("get", "d_vid");
$d_type = be("get", "d_type");
if (!isNum($d_vid)) { echo "非法访问" ; exit;}
if (!isNum($d_type)) { echo "非法访问"; exit;}

switch($action)
{
	case "show": show();break;
	case "digg": digg();break;
	default: main();break;
}
    
function show()
{
	global $db,$d_vid,$d_type;
	$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=" . $d_vid);
	if ($row){
		$good = $row["d_good"]; $bad = $row["d_bad"];
	}
	$goodall = $good + $bad;
	if ($goodall == 0){
		$digsper = 0;
		$undigsper = 0;
	}
	else{
		$digsper = round($good/$goodall,3) *100 ;
		$undigsper = round($bad/$goodall,3) *100 ;
	}
        
	$str = "<div class='good'>";
	$str = $str . "<a href=JavaScript:isdigs('d_good') >";
	$str = $str . "<p>Good</p><div class='bar'><div id='g_img' style='width:" . $digsper . "%'></div></div>";
	$str = $str . "<span class='num'>" . $digsper . "%(" . $good . ")</span>";
	$str = $str . "</a></div><div class='bad'>";
	$str = $str . "<a href=JavaScript:isdigs('d_bad') >";
	$str = $str . "<p>Bad</p><div class='bar'><div id='b_img' style='width:" . $undigsper . "%'></div></div>";
	$str = $str . "<span class='num'>" . $undigsper . "%(" . $bad . ")</span>";
	$str = $str . "</a></div>";
	echo $str;
}

function digg()
{
	global $db,$d_vid,$d_type;
	$typee = be("get", "typee");
	if (isN($typee)){ echo "非法访问"; exit;}
	if (getCookie("voddigg_" . $d_vid)== "ok"){ echo "1"; exit;}
	$db->query("Update {pre}vod set " . $typee . "=" . $typee . "+1 where d_id=" . $d_vid);
	sCookie ("voddigg_" . $d_vid, "ok");
}
    
function main()
{
	global $d_vid,$d_type;
	$str = "var d_vid=\"" . $d_vid . "\";";
	$str = $str . "var d_type=\"" . $d_type . "\";";
	$str = $str . "showdigg();";
	echo $str;
}
?>