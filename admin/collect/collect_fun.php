<?php
require_once ("../../inc/pinyin.php");

function headAdminCollect($title)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo $title?> - 苹果MacCMS</title>
<link rel="stylesheet" type="text/css" href="../../images/admin.css" />
<link rel="stylesheet" type="text/css" href="../../images/default/easyui.css" />
<link rel="stylesheet" type="text/css" href="../../images/icon.css" />
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquery.validate.js"></script>
<script type="text/javascript" src="../../js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="../../js/adm/admin.js"></script>
</head>
<body>
<?php
}

function changeId($cname,$pid,$flag,$c_sys)
{
	//$flag=0分类, $flag=1地区, $flag=2地区
	global $db;
	$sql = "Select c_id,c_name,c_toid,c_pid,c_type From {pre}cj_change where  c_name='".$cname."' and c_type=".$flag . " and c_sys=".$c_sys;
	$rs = $db->query($sql);
	$RelativeID = 0;
	while ($row = $db ->fetch_array($rs))
	{
		if ($row["c_pid"] == 0 || $row["c_pid"] == intval($pid)){
			$RelativeID = $row["c_toid"];
			break;
		}
	}
	unset($rs);
	return $RelativeID;
}

function replaceFilters($strContent,$pid,$sobject,$f_sys)
{
	//$f_sys=0视频, $flag=1文章
	global $db;
	$sql = "select * from  {pre}cj_filters where f_flag=1 and f_sys=".$f_sys." and (f_pid=0 or f_pid='".$pid."') and (f_object=0 or f_object='".$sobject."')";
	
	$rs = $db->query($sql);
	if( $rs ) {
		while ($row = $db ->fetch_array($rs))
		{
			if ($row["f_type"] == 1) { 
				$strContent = replaceStr($strContent,$row["f_content"],$row["f_rep"]);
			}
			else{
				$FilterStr = getBody($strContent,$row["f_strstart"],$row["f_strend"]);
				if ($FilterStr !=false) {
				$strContent = replaceStr($strContent,$row["f_strstart"],"");
				$strContent = replaceStr($strContent,$row["f_strend"],"");
			    $strContent = replaceStr($strContent,$FilterStr,$row["f_rep"]);
			    }
			}
		}
	}
	unset($rs);
	return $strContent;
}

function definiteUrl($surl,$refurl)
{
	$i = $pathStep = 0;
	$dstr = $pstr = $okurl = '';
	$refurl = trim($refurl);
	$surl = trim($surl);  $surl = replaceStr($surl,"\/","/"); 
	$urls = @parse_url($refurl);
	$basehost = ( (!isset($urls['port']) || $urls['port']=='80') ? $urls['host'] : $urls['host'].':'.$urls['port']);
	$basepath = $basehost;
	$paths = explode('/',eregi_replace("^http://","",$refurl));
	$n = count($paths);
	for($i=1;$i < ($n-1);$i++){
		if(!ereg("[\?]",$paths[$i])) $basepath .= '/'.$paths[$i];
	}
	if(!ereg("[\?\.]",$paths[$n-1])){
		$basepath .= '/'.$paths[$n-1];
	}
	if($surl==''){
		return $basepath;
	}
	$pos = strpos($surl,"#");
	if($pos>0){
		$surl = substr($surl,0,$pos);
	}
	if($surl[0]=='/'){
		$okurl = $basehost.$surl;
	}
	else if($surl[0]=='.'){
		if(strlen($surl)<=2){
			return '';
		}
		else if($surl[1]=='/'){
			$okurl = $basepath.ereg_replace('^.','',$surl);
		}
		else{
			$okurl = $basepath.'/'.$surl;
		}
	}
	else{
		if( strlen($surl) < 7 ){
			$okurl = $basepath.'/'.$surl;
		}
		else if( eregi('^http://',$surl) ){
			$okurl = $surl;
		}
		else{
			$okurl = $basepath.'/'.$surl;
		}
	}
	$okurl = eregi_replace('^http://','',$okurl);
	$okurl = 'http://'.eregi_replace('/{1,}','/',$okurl);
	return $okurl;
}

function filterScript($Content,$p_script)
{
	if(($p_script && 1) >0) {$Content=scriptHtml($Content,"iframe",1);}
	if(($p_script && 2) >0) {$Content=scriptHtml($Content,"object",2);}
	if(($p_script && 4) >0) {$Content=scriptHtml($Content,"script",2);}
	if(($p_script && 8) >0) {$Content=scriptHtml($Content,"div",3);}
	if(($p_script && 16) >0) {$Content=scriptHtml($Content,"class",1);}
	if(($p_script && 32) >0) {$Content=scriptHtml($Content,"table",3);}
	if(($p_script && 64) >0) {$Content=scriptHtml($Content,"span",3);}
	if(($p_script && 128) >0) {$Content=scriptHtml($Content,"img",3);}
	if(($p_script && 256) >0) {$Content=scriptHtml($Content,"font",3);}
	if(($p_script && 512) >0) {$Content=scriptHtml($Content,"a",4);}
	if(($p_script && 1024) >0) {$Content=scriptHtml($Content,"tr",3);}
	if(($p_script && 2048) >0) {$Content=scriptHtml($Content,"td",3);}
	if(($p_script && 5096) >0) {$Content=strip_tags($Content);}
	
	$rc=false;
	$Content = replaceStr($Content,"&nbsp;"," ");
	$Content = replaceStr($Content,"'","''");
	$Content = replaceStr($Content,chr(10),"");
	$Content = replaceStr($Content,chr(13)," ");
	
	$arr = explode(" ",$Content);
	$Content="";
	for ($i=0;$i<count($arr);$i++){
		if (!isN($arr[$i])){
			if ($rc) { $Content = $Content . " ";}
			$Content = $Content . $arr[$i];
			$rc=true;
		}
	}
	unset($arr);
	$Content = trim($Content);
	return $Content;
}

function scriptHtml($ConStr,$TagName,$FType)
{
    switch($FType)
    {
    Case 1:
       $rule="<". $TagName . "([^>])*>";
       $ConStr=regReplace($ConStr,$rule,"");
       break;
    Case 2:
       $rule = "<" . $TagName ."([^>])*>.*?</" . $TagName . "([^>])*>";
       $ConStr=regReplace($ConStr,$rule,"") ;
       break;
	Case 3:
       $rule="<" . $TagName . "([^>])*>";
       $ConStr=regReplace($ConStr,$rule,"");
       $rule="</" . $TagName . "([^>])*>";
       $ConStr=regReplace($ConStr,$rule,"");
       break;
    Case 4:
       $rule="<" . $TagName . "([^>])*>";
       $ConStr=regReplace($ConStr,$rule," ");
       $rule="</" . $TagName . "([^>])*>";
       $ConStr=regReplace($ConStr,$rule," ");
       break;
    }
    $ConStr = str_replace(chr(9),"",$ConStr);
    $ConStr = str_replace(chr(10),"",$ConStr);
    return $ConStr;
}

function clearSession()
{
	$_SESSION["strListCode"]="";
	$_SESSION["strListCodeCut"]="";
	$_SESSION["strViewCode"]="";
	$_SESSION["linkarrcode"]="";
}
function clearSessionart()
{
	$_SESSION["strListCodeart"]="";
	$_SESSION["strListCodeCutart"]="";
	$_SESSION["strViewCodeart"]="";
	$_SESSION["linkarrcodeart"]="";
}
?>