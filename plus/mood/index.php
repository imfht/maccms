<?php
require_once ("../../inc/conn.php");
require_once ("../../inc/360_safe3.php");
if (app_mood == 0) { echo "心情关闭中"; exit; }

$action = be("get", "action");
$m_vid = be("all", "m_vid");
$m_type = be("all", "m_type");

if (!isNum($m_vid)){ echo "非法访问";exit; }
If (!isNum($m_type)) { echo "非法访问";exit; }

switch($action)
{
	case "show": show();break;
	case "mood": mood();break;
	default: main();break;
}
dispseObj();

function show()
{
	global $m_vid,$m_type,$db;
	$row = $db->getRow("SELECT * FROM {pre}mood WHERE m_vid=" . $m_vid . " AND m_type=" . $m_type);
	if ($row){
		$mood1 = $row["mood1"];
		$mood2 = $row["mood2"];
		$mood3 = $row["mood3"];
		$mood4 = $row["mood4"];
		$mood5 = $row["mood5"];
		$mood6 = $row["mood6"];
		$mood7 = $row["mood7"];
		$mood8 = $row["mood8"];
		$mood9 = $row["mood9"];
		echo "" . $mood1 . "," . $mood2 . "," . $mood3 . "," . $mood4 . "," . $mood5 . "," . $mood6 . "," . $mood7 . "," . $mood8 . "," . $mood9 . "";
    }
    else{
		$db->Add ("{pre}mood", array("m_vid", "m_type"), array($m_vid, $m_type));
        echo "0,0,0,0,0,0,0,0,0";
    }
    unset($row);
}
    
function mood()
{
	global $m_vid,$m_type,$db;
	$typee = be("get", "typee");
	if (isN($typee)){ echo "非法访问";exit;}
	$db->query("Update {pre}mood set " . $typee . "=" . $typee . "+1 where m_vid=" . $m_vid . " and m_type=" . $m_type);
	show();
}
    
function main()
{
	global $m_vid,$m_type;
	$str = "var m_vid=\"" . $m_vid . "\";";
	$str = $str . "var m_type=\"" . $m_type . "\";";
	$str = $str . "var moodzt = \"0\";";
	$str = $str . "remood();";
	echo $str;
}
?>