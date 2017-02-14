<?php
require_once ("../../inc/conn.php");
require_once ("../../inc/360_safe3.php");

if (app_comment == 0) { echo "评论关闭中"; exit; }

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
	$c_vid = be("all", "c_vid");
	$c_vid = chkSql($c_vid, true);
    $c_type = be("all", "c_type");
    $c_type = chkSql($c_type, true);
    $c_rid = be("all", "c_rid");
    $c_rid = chkSql($c_rid, true);
    $c_name = be("all", "c_name");
    $c_content = be("all", "c_content");
    $verifycode = be("all","verifycode");
    
    if (!isNum($c_vid) || !isNum($c_type)){ echo "1";exit;}
    if (!isN($c_rid)){
    	if (!isNum($c_rid)) { echo "1"; exit; }
    }
    if (isN($c_name) || isN($c_content)){ echo "1";exit;}
    if (app_commentverify==1 && $_SESSION["code_comment"] != $verifycode){ echo "2";exit;}
    if (getTimeSpan("lastCommentTime") < app_commenttime){ echo "3";exit;}
    
    $c_name = badFilter($c_name) ; $c_name = chkSql($c_name, true);
    $c_content = badFilter($c_content) ; $c_content = chkSql($c_content, true);
    $c_ip = getIP();
    $c_time = date('Y-m-d H:i:s',time());
	if (app_commentaudit==1){ $c_audit=0;} else {$c_audit=1;}
	if (strlen($c_name) >64) { $c_name = substring($c_name,64);}
	if (strlen($c_content) >128){ $c_content = substring($c_content,128);}
	
	$db->Add ("{pre}comment", array("c_name", "c_vid","c_rid", "c_type","c_audit" ,"c_ip", "c_time", "c_content"), array($c_name, $c_vid, $c_rid, $c_type, $c_audit, $c_ip, $c_time, $c_content));
	$_SESSION["lastCommentTime"] = time();
    echo "0";
}

function tongji()
{
	global $db;
	$c_vid = be("get", "id") ; $c_vid = chkSql($c_vid, true);
	$c_type = be("get", "type") ; $c_type = chkSql($c_type, true);
	if (!isNum($c_vid) || !isNum($c_type)){ echo "err";exit;}
	$count1 = $db->getOne("select count(*) from {pre}comment where c_type=".$c_type." and c_rid=0 and c_vid=".$c_vid);
	$count2 = $db->getOne("select count(*) from {pre}comment where c_type=".$c_type." and c_vid=".$c_vid);
	echo "[".$count1.",".$count2."]";
}

function main()
{
	$c_vid = be("get", "id") ; $c_vid = chkSql($c_vid, true);
	$c_type = be("get", "type") ; $c_type = chkSql($c_type, true);
	if (!isNum($c_vid) || !isNum($c_type)){ echo "err";exit;}
?>
<iframe width="100%" height="1" frameborder="0" marginheight="0" marginwidth="0" scrolling="yes" src="<?php echo app_installdir?>plus/comment/?action=list&id=<?php echo $c_vid?>&type=<?php echo $c_type?>&title=" id="mac_comment" name="mac_comment"></iframe>
<?php
}

function mlist()
{
	$c_vid = be("get", "id") ; $c_vid = chkSql($c_vid, true);
	$c_type = be("get", "type") ; $c_type = chkSql($c_type, true);
	if (!isNum($c_vid) || !isNum($c_type)){ echo "err";exit;}
	if (isN($_SESSION["username"])){ $c_name = "游客";} else { $c_name = $_SESSION["username"]; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>评论留言</title>
<link type="text/css" rel="stylesheet" href="images/style.css" />
<script type="text/javascript" src="../../js/jquery.js"></script>
</head>
<body>
<script>
var maccms_path = "<?php echo app_installdir?>";
var commentverify = "<?php echo app_commentverify?>";
var c_vid = "<?php echo $c_vid?>";
var c_type = "<?php echo $c_type?>";
var c_name = "<?php echo $c_name?>";
function reinitIframe()
{
	try{
	var main = $(window.parent.document).find("#mac_comment");
	var thisheight = $(document).height() ;
	main.height(thisheight);
	}catch (ex){}
}
$(document).ready(function(){
	window.setInterval("reinitIframe()", 200);
});
</script>
<script type="text/javascript" src="comment.js"></script>
</body>
</html>
<?php
}

function glist()
{
	global $db;
	$c_vid = be("get", "id") ; $c_vid = chkSql($c_vid, true);
	$c_type = be("get", "type") ; $c_type = chkSql($c_type, true);
	$page = be("all","page");
	if (!isNum($page)){ $page=1;} else {$page=intval($page);}
	if (!isNum($c_vid) || !isNum($c_type)){ echo "err";exit;}
	
	$sql = "SELECT count(*) FROM {pre}comment where c_vid=" . $c_vid . " AND c_rid=0 AND c_type= " . $c_type ;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_commentnum);
	
	if ($page > $pagecount){
		$page = $pagecount;
	}
		
	$sql = "SELECT c_id,c_vid,c_type,c_rid,c_name,c_content,c_time,c_ip from {pre}comment WHERE c_vid=" . $c_vid . " AND c_rid=0 AND c_type= " . $c_type . " ORDER BY c_id DESC limit ".(app_commentnum*($page-1)) .",".app_commentnum;
	
    $bgcolorArr=array("#D66203","#513DBD","#784E1A","#C55200","#DA6912","#537752","#C58200","#519DBD","#D60103","#531752");
	$rs = $db->query($sql);
	$i = 0;
	if($nums==0){
		echo "<center><h3> 暂无评论，快来抢沙发吧！</h3></center>";exit;
	}
	else{
		while ($row = $db ->fetch_array($rs)){
			$i++;
			$c_id = $row["c_id"];
			$c_rid = $row["c_rid"];
			
			$replystr = "";
			$bgstr = $bgcolorArr[rand(1,9)];
			$sql = "SELECT c_id,c_name,c_content,c_time,c_ip,c_vid,c_type from {pre}comment WHERE c_rid=".$c_id." ORDER BY c_id DESC";
			$rs1 = $db->query($sql);
			while ($row1 = $db ->fetch_array($rs1)){
				$c_name = $row1["c_name"];
				$c_content = $row1["c_content"];
				$c_content = regReplace($c_content, "\[em:(\d{1,})?\]", "<img src=\"../../images/face/$1.gif\" border=0/>");
				$c_time = $row1["c_time"];
				$c_ip = $row1["c_ip"];
				if (!isN($c_ip)){
					$tmpIpArr = explode(".",$c_ip);
					$c_ip = $tmpIpArr[0] . "." . $tmpIpArr[1] . "." . $tmpIpArr[2] . ".*";
				}
				
				$replystr .= "<div class=\"citation\"><div class=\"citation-title\"><span class=\"author\"><span class=\"name\" style=\"color:".$bgstr.";\">".$c_name."</span><span class=\"address\">&nbsp;IP:[".$c_ip."]</span></span><span class=\"post-time\">".$c_time."</span><div class=\"clear\"><br/></div></div><p class=\"content\">".$c_content."</p></div>";
			}
			unset($rs1);
			
			$c_name = $row["c_name"];
			$c_content = $row["c_content"];
			$c_content = regReplace($c_content, "\[em:(\d{1,})?\]", "<img src=\"../../images/face/$1.gif\" border=0/>");
			$c_time = $row["c_time"];
			$c_ip = $row["c_ip"];
			if (!isN($c_ip)){
				$tmpIpArr = explode(".",$c_ip);
				$c_ip = $tmpIpArr[0] . "." . $tmpIpArr[1] . "." . $tmpIpArr[2] . ".*";
			}
			$span="";
			if (strlen($c_name) <4){
				for ($j=strlen($c_name);$i<4;$i++){
					$span = $span ."&nbsp;&nbsp;";
				}
			}
			
			echo "<div class=\"dt\"><span class=\"author\"><span class=\"name\" style=\"background-color:".$bgstr.";\">".$c_name.$span."</span><span class=\"address\">&nbsp;IP:[".$c_ip."]</span></span><span class=\"post-time\">发表于[".$c_time."]&nbsp;第".(($page-1)*app_commentnum+$i)."楼</span><div class=\"clear\"><br/></div></div><div class=\"dd\"><p class=\"content\">".$c_content."</p><div class=\"toolbar\" id=\"toolbar-".$c_id."\"><a href=\"#cmt_reply\" onclick=\"comment_reply(".$c_id.");\">回复</a></div>".$replystr."</div>";
        }
        unset ($rs);
        echo "<div class=\"pages\">" . getPageInfo($pagecount, $page) . "</div>";
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
		$endnum=$totalPage>=10?10:$totalPage; 
	}else if($has1 && !$has2){ 
		$begin=$totalPage-9;
		$begin=$begin<1?1:$begin; 
		$endnum=$totalPage; 
	}else{ 
		$begin=1; 
		$endnum=$totalPage; 
	}
	$pgStr='共'.$totalPage.'页&nbsp;';
        
	if ($absolutePage > 1){
		$pgStr = $pgStr . "<a href='javascript:' p='1' title='首页'>首页</a>";
	}
	
	if ($absolutePage > 1){
		$pgStr = $pgStr . " <a href='javascript:' p='" . ($absolutePage - 1) . "' title='上一页'>上一页</a>";
	}
        
	for ($i=$begin;$i<=$endnum;$i++){
		if ($i != $absolutePage){
			$pgStr = $pgStr . " <a href='javascript:' p='" . $i . "'>" . $i . "</a>";
		}
		else{
			$pgStr = $pgStr . " <span class=\"active\">" . $i . "</span>";
		}
	}
        
	if ($absolutePage < $totalPage){
		$pgStr = $pgStr . " <a href='javascript:' p='" . ($absolutePage + 1) . "' title='下一页'>下一页</a>";
		$pgStr = $pgStr . " <a href='javascript:' p='" . ($totalPage) . "' title='尾页'>尾页</a>";
	}
	return $pgStr;
}
?>