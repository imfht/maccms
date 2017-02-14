<?php
require_once ("../../inc/conn.php");
require_once ("../../inc/360_safe3.php");
if (app_gbook == 0) { echo "留言本关闭中"; exit; }

$action = be("get", "action");

switch($action)
{
	case "save": save();break;
	case "glist" : glist();break;
	case "list" : mlist();break;
	case "tongji" : tongji();break;
	default: main();break;
}
dispseObj();

function save()
{
	global $db;
	
	$g_vid = be("all", "g_vid"); $g_vid = chkSql($g_vid, true);
    $g_name = be("all", "g_name"); $g_content = be("all", "g_content");
    $verifycode = be("all","verifycode");
    
    if (!isN($g_vid)){
    	if (!isNum($g_vid)){ echo "1";exit;}
    }
    else{
    	$g_vid=0;
    }
    if (isN($g_name) || isN($g_content)){ echo "1"; exit;}
    if (app_gbookverify==1 && $_SESSION["code_gbook"] != $verifycode){ echo "2";exit; }
    if (getTimeSpan("lastgbookTime") < app_gbooktime){ echo "3";exit;}
    
    $g_name = badFilter($g_name); $g_name = chkSql($g_name, true);
    $g_content = badFilter($g_content); $g_content = chkSql($g_content, true);
    $g_ip = getIP();
    $g_time = date('Y-m-d H:i:s',time());
    
    if (app_gbookaudit==1){ $g_audit=0;} else { $g_audit=1;}
	if (strlen($g_name) >64){ $g_name = substring($g_name,64);}
	if (strlen($g_content) >255){ $g_content = substring($g_content,255);}
	
	$db->Add ("{pre}gbook", array("g_vid","g_audit","g_name", "g_ip", "g_time", "g_content"), array($g_vid, $g_audit, $g_name, $g_ip, $g_time, $g_content));
	$_SESSION["lastgbookTime"] = time();
    echo "0";
}

function tongji()
{
	global $db;
	$count1 = $db->getOne("select count(*) from {pre}gbook");
	$count2 = $db->getOne("select count(*) from {pre}gbook where g_audit=1");
	echo "[".$count1.",".$count2."]";
}

function main()
{
	$g_vid = be("get", "id"); $g_vid = chkSql($g_vid, true);
	$g_vname = be("get", "name"); $g_vname = chkSql($g_vname, true);
	if (!isN($g_vid)){
		if (isN($g_vname)) { echo "err";exit;}
		if (!isNum($g_vid)) { echo "err";exit;}
	}
?>
<iframe width="100%" height="1" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto" src="<?php echo app_installdir?>plus/gbook/?action=list&id=<?php echo $g_vid?>&name=<?php echo $g_vname?>" id="mac_gbook" name="mac_gbook"></iframe>
<?php
}

function mlist()
{
	$g_vid = be("get", "id"); $g_vid = chkSql($g_vid, true);
	$g_vname = be("get", "name"); $g_vname = chkSql($g_vname, true);
	if (!isN($g_vid)){
		if (isN($g_vname)) { echo "err";exit;}
		if (!isNum($g_vid)) { echo "err";exit;}
	}
	if (isN($_SESSION["username"])){ $g_name = "游客";} else { $g_name = $_SESSION["username"];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>留言本</title>
<link type="text/css" rel="stylesheet" href="images/style.css" />
<script type="text/javascript" src="../../js/jquery.js"></script>
</head>
<body>
<script>
var maccms_path = "<?php echo app_installdir?>";
var gbookverify = "<?php echo app_gbookverify?>";
var g_vid = "<?php echo $g_vid?>";
var g_vname = "<?php echo $g_vname?>";
var g_name = "<?php echo $g_name?>";
function reinitIframe()
{
	try{
	var main = $(window.parent.document).find("#mac_gbook");
	var thisheight = $(document).height() ;
	main.height(thisheight);
	}catch (ex){}
}
$(document).ready(function(){
	window.setInterval("reinitIframe()", 200);
});
</script>
<script type="text/javascript" src="gbook.js"></script>
</body>
</html>
<?php
}

function glist()
{
	global $db;	
	$page = be("all","page");
	if (!isNum($page)){ $page=1;} else { $page=intval($page);}
	
	$sql = "SELECT count(*) FROM {pre}gbook where g_audit=1 " ;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_gbooknum);
	
	if ($page > $pagecount){
		$page = $pagecount;
	}
	
	$sql = "SELECT g_id,g_vid,g_audit,g_name,g_content,g_reply,g_time,g_replytime,g_ip from {pre}gbook WHERE g_audit=1 ORDER BY g_id DESC ";
	$sql.= " limit ".(app_gbooknum * ($page-1)).",".app_gbooknum;
    $bgcolorArr=array("#D66203","#513DBD","#784E1A","#C55200","#DA6912","#537752","#C58200","#519DBD","#D60103","#531752");
    $rs = $db->query($sql);
	$i = 0;
	if($nums==0){
		echo "<center><h3> 暂无留言，快来抢沙发吧！</h3></center>";exit;
	}
	else{
		while ($row = $db ->fetch_array($rs)){
			$i++;
			$g_id = $row["g_id"];
			$g_audit = $row["g_audit"];
			$g_name = $row["g_name"];
			$g_content = $row["g_content"];
			$g_content = regReplace($g_content, "\[em:(\d{1,})?\]", "<img src=\"../../images/face/$1.gif\" border=0/>");
			$g_reply = $row["g_reply"];
			$g_time = $row["g_time"];
			$g_replytime = $row["g_replytime"];
			$g_ip = $row["g_ip"];
			If (!isN($g_ip)){
				$tmpIpArr = explode(".",$g_ip);
				$g_ip = $tmpIpArr[0] . "." . $tmpIpArr[1] . "." . $tmpIpArr[2] . ".*";
			}
			
			$replystr = "";
			$bgstr = $bgcolorArr[rand(1,9)];
			
			if (!isN($g_reply)){
				$replystr = "<div class=\"citation\"><p class=\"content\">站长回复：".$g_reply."</p></div>";
				
			}
			$span="";
			if (strlen($g_name)<4 ){
				for ($j=strlen($g_name);$j<=4;$j++){
					$span = $span ."&nbsp;&nbsp;";
				}
			}
			
			echo "<div class=\"dt\"><span class=\"author\"><span class=\"name\" style=\"background-color:".$bgstr.";\">".$g_name.$span."</span><span class=\"address\">IP:[".$g_ip."]</span></span><span class=\"post-time\">发表于:[".$g_time."]</span><div class=\"clear\"><br/></div></div><div class=\"dd\"><p class=\"content\">".$g_content."</p>".$replystr."</div>";
            
        }
        unset ($rs);
        
        echo "<div class=\"pages\">" . getPageInfo($pagecount, $page ) . "</div>";
	}
}
    
function getPageInfo($totalPage, $absolutePage)
{
	$has1 = $absolutePage - 5 >= 1;
	$has2 = $absolutePage + 5 <= $totalPage;
	$begin = 1;
	$endnum = 1;
	
	if($has1 && $has2){ 
		$begin=$absolutePage-4; 
		$endnum=$absolutePage+5; 
	}else if(!$has1 && $has2){ 
		$begin=1; 
		$endnum=$totalPage>=10 ? 10:$totalPage;
	}else if($has1 && !$has2){ 
		$begin=$totalPage-9;
		$begin=$begin<1?1:$begin;
		$endnum=$totalPage;
	}else{ 
		$begin=1; 
		$endnum=$totalPage;
	}
	$pgStr= '共'.$totalPage.'页&nbsp;';
        
	if ($absolutePage > 1){
		$pgStr .= "<a href='javascript:' p='1' title='首页'>首页</a>";
	}
	
	if ($absolutePage > 1){
		$pgStr .= " <a href='javascript:' p='" . ($absolutePage - 1) . "' title='上一页'>上一页</a>";
	}
        
	for ($i=$begin;$i<=$endnum;$i++){
		if ($i != $absolutePage){
			$pgStr .= " <a href='javascript:' p='" . $i . "'>" . $i . "</a>";
		}
		else{
			$pgStr .=  " <span class=\"active\">" . $i . "</span>";
		}
	}
        
	if ($absolutePage < $totalPage){
		$pgStr .= " <a href='javascript:' p='" . ($absolutePage + 1) . "' title='下一页'>下一页</a>";
		$pgStr .= " <a href='javascript:' p='" . ($totalPage) . "' title='尾页'>尾页</a>";
	}
	return $pgStr;
}
?>