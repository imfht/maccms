<?php
function redirect($url)
{
	header("Location:$url");
	exit;
}

function head()
{
	return '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
}

function alert($str)
{
	echo '<script type="text/javascript">alert("' .$str. '\t\t");history.go(-1);</script>';
	exit;
}

function alertUrl($str,$url)
{
	echo '<script type="text/javascript">alert("' .$str. '\t\t");location.href="' .$url .'";</script>';
	exit;
}


function confirmMsg($msg,$url1,$url2)
{
	echo '<script>if(confirm("' .$msg. '")){location.href="' .$url1. '"}else{location.href="' .$url2. '"}</script>';
	exit; 
}

function showMsg($msg,$url)
{
    if($url == ""){ $url = "history.go(-1)"; 	} else{ $url = "location='" .$url. "'"; }
    echo '<style>body{text-align:center}</style><script>function JumpUrl(){' .$url. ';}document.write("<div style=\"background-color:white;border:1px solid #1C93E5;margin:0 auto;width:400px;text-align:left;\"><div style=\"padding:3px 3px;color:white;font-weight:700;line-height:21px;height:25px;font-size:12px;border-bottom:1px solid #1C93E5; text-indent:3px; background-color:#1C93E5;text-align:center\">系统提示信息</div><div style=\"font-size:12px;padding:40px 8px 50px;line-height:25px;text-align:center\">' .$msg. '稍后自动返回...</div></div>");setTimeout("JumpUrl()",1500);</script>';
    exit;
}

function errMsg($e,$d)
{
    echo '<style>body{text-align:center}</style><div style="background-color:white;border:1px solid #1C93E5;margin:0 auto;width:400px;text-align:left;"><div style="padding:3px 3px;color:white;font-weight:700;line-height:21px;height:25px;font-size:12px;border-bottom:1px solid #1C93E5; text-indent:3px; background-color:#1C93E5;text-align:center">【' .$e. '】</div><div style="font-size:12px;padding:40px 8px 50px;line-height:25px;text-align:center">' .$d. '</div></div>';
    exit;
}

function initObj()
{
	date_default_timezone_set('Etc/GMT-8');
	@ini_set("display_errors","On");
	@ini_set('max_execution_time', '0');
	error_reporting(7);
	set_error_handler("my_error_handler");
	define("appTime",execTime());	
	$rpath = ereg_replace("[/\\]{1,}",'/',dirname(__FILE__));
	$rpath = ereg_replace("[/\\]{1,}",'/',substr($rpath,0,-3));
	define("root",$rpath);
}

function dispseObj()
{
	global $db,$template,$mac;
	unset($db);
	unset($template);
	unset($mac);
	$mac=null;
	$db=null;
	$template=null;
}

function my_error_handler($errno, $errmsg, $filename, $linenum, $vars) 
{
	$the_time = date("Y-m-d H:i:s (T)");
	$errno = $errno & error_reporting();
    if($errno === 0) return;
	$filename=str_replace(getcwd(),"",$filename);
    $errortype = array (
    E_ERROR           => "Error",
    E_WARNING         => "Warning",
    E_PARSE           => "Parsing Error",
    E_NOTICE          => "Notice",
    E_CORE_ERROR      => "Core Error",
    E_CORE_WARNING    => "Core Warning",
    E_COMPILE_ERROR   => "Compile Error",
    E_COMPILE_WARNING => "Compile Warning",
    E_USER_ERROR      => "User Error",
    E_USER_WARNING    => "User Warning",
    E_USER_NOTICE     => "User Notice",
    E_STRICT          => "Runtime Notice"
	);
	$err = "系统提示:";
    $err .= "<br>\nMsg: " . $errmsg ;
    $err .= "<br>\nFile: " . $filename;
    $err .= "<br>\nLine: " . $linenum ;
    die($err);
}

function chkArray($arr1,$arr2)
{
	$res = true;
	if(is_array($arr1) && is_array($arr2)){
		if(count($arr1) != count($arr2)){
			$res = false;
		}
	}
	else{
		$res = false;
	}
	return $res;
}

function isN($str)
{
	if (is_null($str) || $str==''){ return true; }else{ return false;}
}

function isNum($str)
{
	if(!isN($str)){
		if(is_numeric($str)){return true;}else{ return false;}
  	}
}

function indexOf($str,$strfind)
{
	if(isN($str) || isN($strfind)){ return false; }
	if(strpos(",".$str,$strfind)>0){ return true; } else{ return false; }
}

function isIp($ip)
{
	$e="([0-9]|1[0-9]{2}|[1-9][0-9]|2[0-4][0-9]|25[0-5])";  
	if(ereg("^$e\.$e\.$e\.$e$",$ip)){ return true; } else{ return false; }
}

function isObjInstalled($objstr)
{
	return true;
}

function getRndNum($length)
{
	$pattern = "1234567890";
	for($i=0; $i<$length; $i++){
		$res .= $pattern{mt_rand(0,10)};
	}
	return $res;
}

function rndNum($minnum,$maxuum)
{
	return rand($minnum,$maxuum);
}

function getRndStr($length)
{
	$pattern = "1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ";
	for($i=0; $i<$length; $i++){
		$res .= $pattern{mt_rand(0,36)};
	}
	return $res;
}

function be($mode,$key)
{
	ini_set("magic_quotes_runtime", 0);
	$magicq= get_magic_quotes_gpc();
	switch ($mode)
	{
		case 'post':
			$res=isset($_POST[$key]) ? $magicq?$_POST[$key]:addslashes($_POST[$key]) : '';
			break;
		case 'get':
			$res=isset($_GET[$key]) ? $magicq?$_GET[$key]:addslashes($_GET[$key]) : '';
			break;
		case 'arr':
			$arr =isset($_POST[$key]) ? $_POST[$key] : '';
			if($arr==""){
				$value="0";
			}
			else{
				for($i=0;$i<count($arr);$i++){
					$res=implode(',',$arr);
				} 
			}
			break;
		default:
			$res=isset($_REQUEST[$key]) ? $magicq?$_REQUEST[$key]:addslashes($_REQUEST[$key]) : '';
			break;
	}
	return $res;
}

function mkdirs($path)
{
	if (!is_dir(dirname($path))){
		mkdirs(dirname($path));
	}
	if(!file_exists($path)){
		mkdir($path);
	}
}

function getIP()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else if(!empty($_SERVER["REMOTE_ADDR"])){
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	else{
		$cip = '';
	}
	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = isset($cips[0]) ? $cips[0] : 'unknown';
	unset($cips);
	return $cip;
}

function getReferer()
{
	return $_SERVER["HTTP_REFERER"];
}

function getUrl()
{
  if(!empty($_SERVER["REQUEST_URI"])){
		$nowurl = $_SERVER["REQUEST_URI"];
	}
	else{
		$nowurl = $_SERVER["PHP_SELF"];
	}
	return $nowurl;
}

function delCookie($key)
{
	setcookie($key,"",time()-3600,"/");
}

function getCookie($key)
{
	if(!isset($_COOKIE[$key])){
		return '';
	}
	else{
		return $_COOKIE[$key];
	}
}

function sCookie($key,$val)
{
	setcookie($key,$val,0,"/");
}

function execTime()
{
	$time = explode(" ", microtime());
	$usec = (double)$time[0];
	$sec = (double)$time[1];
	return $sec + $usec;
}

function getTimeSpan($sessionName)
{
	$lastTime = $_SESSION[$sessionName];
	if (isN($lastTime)){
		$lastTime= "1228348800";
	}
	$res = time() - intval($lastTime);
	return $res;
}

function getRunTime()
{
	global $db;
	$t2= execTime() - appTime;
	return "页面执行时间: ".round($t2,4)."秒&nbsp;" . $db->iqueryCount . "次数据查询";
}

function repSpecialChar($str)
{
	$str = str_replace("/","_",$str);
	$str = str_replace("\\","_",$str);
	$str = str_replace("[","",$str);
	$str = str_replace("]","",$str);
	$str = str_replace("<","",$str);
	$str = str_replace(">","",$str);
	$str = str_replace("*","",$str);
	$str = str_replace(":","",$str);
	$str = str_replace("?","",$str);
	$str = str_replace("|","",$str);
	$str = str_replace(" ","",$str);
	$str = trim($str);
	return $str;
}

function getTextt($num,$sname)
{
	if (isNum($num)){
		if (!isN($sname)){
			$res= substring($sname,$num);
		}
		else{
			$res="";
		}
	}
	else{
		$res=$sname;
	}
	return $res;
}

function getDatet($iformat,$itime)
{
	$iformat = str_replace("yyyy","Y",$iformat);
	$iformat = str_replace("yy","Y",$iformat);
	$iformat = str_replace("hh","H",$iformat);
	$iformat = str_replace("mm","m",$iformat);
	$iformat = str_replace("dd","d",$iformat);
	
	if (isN($iformat)) { $iformat = "Y-m-d";}
	$res = date($iformat,strtotime($itime));
	return $res;
}

function buildregx($regstr,$regopt)
{
	return '/'.str_replace('/','\/',$regstr).'/'.$regopt;
}

function replaceStr($text,$search,$replace)
{
	if(isN($text)){ return "" ;}
	$res=str_replace($search,$replace,$text);
	return $res;
}

function regReplace($str,$rule,$value)
{
	$rule = buildregx($rule,"is");
	if (!isN($str)){
		$res = preg_replace($rule,$value,$str);
	}
	return $res;
}

function getSubStrByFromAndEnd($str,$startStr,$endStr,$operType)
{
	switch ($operType)
	{
		case "start":
			$location1=strpos($str,$startStr)+strlen($startStr);
			$location2=strlen($str)+1;
			break;
		case "end":
			$location1=1;
			$location2=strpos($str,$endStr,$location1);
			break;
		default:
			$location1=strpos($str,$startStr)+strlen($startStr);
			$location2=strpos($str,$endStr,$location1);
			break;
	}
	$location3 = $location2-$location1;
	$res= substring1($str,$location3,$location1);
	return $res;
}

function regMatch($str, $rule)
{
	$rule = buildregx($rule,"is");
	preg_match_all($rule,$str,$MatchesChild);
	$matchfieldarr=$MatchesChild[1];
	$matchfieldstrarr=$MatchesChild[0];
	$matchfieldvalue="";
	foreach($matchfieldarr as $f=>$matchfieldstr)
	{
		$matchfieldvalue=$matchfieldstrarr[$f];
		$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
		break;
	}
	unset($MatchesChild);
	return $matchfieldstr;
}


function XmlSafeStr($s) { return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$s); }

function utf2ucs($str)
{
	$n=strlen($str);
	if ($n=3) {
		$highCode = ord($str[0]);
		$midCode = ord($str[1]);
		$lowCode = ord($str[2]);
		$a   = 0x1F & $highCode;
		$b   = 0x7F & $midCode;
		$c   = 0x7F & $lowCode;
		$ucsCode = (64*$a + $b)*64 + $c;
	}
	elseif ($n==2) {
		$highCode = ord($str[0]);
		$lowCode = ord($str[1]);
		$a   = 0x3F & $highCode;
		$b   = 0x7F & $lowCode;
		$ucsCode = 64*$a + $b; 
	}
	elseif($n==1) {
		$ucscode = ord($str);
	}
	return dechex($ucsCode);
}

function escape($str)
{
	preg_match_all("/[\xC0-\xE0].|[\xE0-\xF0]..|[\x01-\x7f]+/",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v) {
	$ord = ord($v[0]);
	    if( $ord<=0x7F)
	      $ar[$k] = rawurlencode($v);
	    elseif ($ord<0xE0) {
	      $ar[$k] = "%u".utf2ucs($v);
	    }
		elseif ($ord<0xF0) {
	      $ar[$k] = "%u".utf2ucs($v);
		}
	}
	return join("",$ar);
}

function unescape($str)
{
	$str = rawurldecode($str);
	preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v) {
		if(substr($v,0,2) == "%u"){
			$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,-4)));
		}
		else if(substr($v,0,3) == "&#x"){
			$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,3,-1)));
		}
		else if(substr($v,0,2) == "&#") {
			$ar[$k] = iconv("UCS-2","GB2312",pack("n",substr($v,2,-1)));
		}
	}
	unset($r);
	return join("",$ar);
}

function htmlEncode($str)
{
	if (!isN($str)){
		$str = replaceStr($str, chr(38), "&#38;");
		$str = replaceStr($str, ">", "&gt;");
		$str = replaceStr($str, "<", "&lt;");
		$str = replaceStr($str, chr(39), "&#39;");
		$str = replaceStr($str, chr(32), "&nbsp;");
		$str = replaceStr($str, chr(34), "&quot;");
		$str = replaceStr($str, chr(9), "&nbsp;&nbsp;&nbsp;&nbsp;");
		$str = replaceStr($str, chr(13), "<br />");
		$str = replaceStr($str, chr(10), "<br />");
	}
	return $str;
}

function htmlDecode($str)
{
	if (!isN($str)){
		$str = replaceStr($str, "<br/>", chr(13)&chr(10));
		$str = replaceStr($str, "<br>", chr(13)&chr(10));
		$str = replaceStr($str, "<br />", chr(13)&chr(10));
		$str = replaceStr($str, "&nbsp;&nbsp;&nbsp;&nbsp;", Chr(9));
		$str = replaceStr($str, "&amp;", chr(38));
		$str = replaceStr($str, "&#39;", chr(39));
		$str = replaceStr($str, "&apos;", chr(39));
		$str = replaceStr($str, "&nbsp;", chr(32));
		$str = replaceStr($str, "&quot;", chr(34));
		$str = replaceStr($str, "&gt;", ">");
		$str = replaceStr($str, "&lt;", "<");
		$str = replaceStr($str, "&#38;", chr(38));
	}
	return $str;
}

function htmlFilter($str)
{
	$str = strip_tags($str);
	$str = str_replace("\"","",$str);
	$str = str_replace("'","",$str);
	return $str;
}

function htmltojs($content)
{
	$arrLines = explode(chr(10),$content);
	for ($i=0 ;$i<count($arrLines);$i++){
		$sLine = replaceStr( $arrLines[$i] , "\\" , "\\\\");
		$sLine = replaceStr( $sLine , "/" , "\/");
		$sLine = replaceStr( $sLine , "'" , "\'");
		$sLine = replaceStr( $sLine , "\"\"" , "\"");
		$sLine = replaceStr( $sLine , chr(13) , "" );
		$strNew = $strNew . "document.writeln('". $sLine  . "');" . chr(10);
	}
	unset($arrLines);
	return $strNew;
}

function jstohtml($str)
{
	if (!isN($str)){
		$str = replaceStr( $str , "document.writeln('" , "");
		$str = replaceStr( $str , "\'" , "'");
		$str = replaceStr( $str , "\"" , "\"\"");
		$str = replaceStr( $str , "\\\\" , "\\");
		$str = replaceStr( $str , "\/" , "/");
		$str = replaceStr( $str , "');" , "");
	}
    return $str;
}

function jsEncode($str)
{
	if (!isN($str)){
		$str = replaceStr($str,chr(92),"\\");
		$str = replaceStr($str,chr(34),"\"");
		$str = replaceStr($str,chr(39),"\'");
		$str = replaceStr($str,chr(9),"\t");
		$str = replaceStr($str,chr(13),"\r");
		$str = replaceStr($str,chr(10),"\n");
		$str = replaceStr($str,chr(12),"\f");
		$str = replaceStr($str,chr(8),"\b");
	}
	return $str;
}

function badFilter($str)
{
	$arr=explode(",",app_filter);
	for ($i=0;$i<count($arr);$i++){
		$str= replaceStr($str,$arr[$i],"***");
	}
	unset($arr);
	return $str;
}

function asp2phpif($str)
{
	$str= str_replace("not","!",$str);
	$str= str_replace("==","=",$str);
	$str= str_replace("=","==",$str);
	$str= str_replace("<>","!=",$str);
	$str= str_replace("and","&&",$str);
	$str= str_replace("or","||",$str);
	$str= str_replace("mod","%",$str);
	return $str;
}

function bytesToBstr($Body,$CharSet)
{
	return "";
}

function substring1($str,$len, $start) {
     $tmpstr = "";
     $len = $start + $len;
     for($i = $start; $i < $len; $i++){
         if(ord(substr($str, $i, 1)) > 0xa0) {
             $tmpstr .= substr($str, $i, 2);
             $i++;
         } else
             $tmpstr .= substr($str, $i, 1);
     }
     return $tmpstr;
} 

function substring($str, $lenth, $start=0) 
{ 
	$len = strlen($str); 
	$r = array(); 
	$n = 0;
	$m = 0;
	
	for($i=0;$i<$len;$i++){ 
		$x = substr($str, $i, 1); 
		$a = base_convert(ord($x), 10, 2); 
		$a = substr( '00000000 '.$a, -8);
		
		if ($n < $start){ 
            if (substr($a, 0, 1) == 0) { 
            }
            else if (substr($a, 0, 3) == 110) { 
              $i += 1; 
            }
            else if (substr($a, 0, 4) == 1110) { 
              $i += 2; 
            } 
            $n++; 
		}
		else{ 
            if (substr($a, 0, 1) == 0) { 
             	$r[] = substr($str, $i, 1); 
            }else if (substr($a, 0, 3) == 110) { 
             	$r[] = substr($str, $i, 2); 
            	$i += 1; 
            }else if (substr($a, 0, 4) == 1110) { 
            	$r[] = substr($str, $i, 3); 
             	$i += 2; 
            }else{ 
             	$r[] = ' '; 
            } 
            if (++$m >= $lenth){ 
              break; 
            } 
        }
	}
	return  join('',$r);
}

function getFolderItem($tmppath){
	$fso=@opendir($tmppath);
    $attr=array();
    $i=0;
	while (($file=@readdir($fso))!==false){
		if($file!=".." && $file!="."){
			array_unshift($attr,$file);
			$i=$i+1;
		}
	}
	closedir($fso);
	unset($fso);
	return $attr;
}

function convert_encoding($str,$nfate,$ofate){
	if ($ofate=="UTF-8"){ return $str; }
	if ($ofate=="GB2312"){ $ofate="GBK"; }
	
	if(function_exists("mb_convert_encoding")){
		$str=mb_convert_encoding($str,$nfate,$ofate);
	}
	else{
		$ofate.="//IGNORE";
		$str=iconv(  $nfate , $ofate ,$str);
	}
	return $str;
}

function getPage($url,$charset)
{
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') ){
			$ch = @curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; )');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, 'domain=www.baidu.com');
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$content = @curl_exec($ch);
			curl_close($ch);
		}
		else if( ini_get('allow_url_fopen')==1 ){
			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
		$content = convert_encoding($content,"utf-8",$charset);
	}
	return $content;
}

function getBody($strBody,$strStart,$strEnd)
{
	if(isN($strBody)){ return false; }
	if(isN($strStart)){ return false; }
	if(isN($strEnd)){ return false; }
	
    $strStart=stripslashes($strStart);
   	$strEnd=stripslashes($strEnd);
	
	if(strpos($strBody,$strStart)!=""){
		$str = substr($strBody,strpos($strBody,$strStart)+strlen($strStart));
		$str = substr($str,0,strpos($str,$strEnd));
	}
	else{
		$str=false;
	}	
	return $str;
}

function getArray($strBody,$strStart,$strEnd)
{
	$strStart=stripslashes($strStart);
    $strEnd=stripslashes($strEnd);
	if(isN($strBody)){ return false; }
	if(isN($strStart)){ return false; }
	if(isN($strEnd)){ return false; }
	
	$strStart = replaceStr($strStart,"(","\(");
	$strStart = replaceStr($strStart,")","\)");
	$strStart = replaceStr($strStart,"'","\'");
	$strStart = replaceStr($strStart,"?","\?");
	$strEnd = replaceStr($strEnd,"(","\(");
	$strEnd = replaceStr($strEnd,")","\)");
	$strEnd = replaceStr($strEnd,"'","\'");
	$strEnd = replaceStr($strEnd,"?","\?");
	
	$labelRule = $strStart."(.*?)".$strEnd;
	$labelRule = buildregx($labelRule,"is");
	preg_match_all($labelRule,$strBody,$tmparr);
	$tmparrlen=count($tmparr[1]);
	$rc=false;
	for($i=0;$i<$tmparrlen;$i++)
	{
		if($rc){ $str .= "{Array}"; }
		$str .= $tmparr[1][$i];
		$rc=true;
	}
	
	if (isN($str)) { return false ;}
	$str=replaceStr($str,$strStart,"");
	$str=replaceStr($str,$strEnd,"");
	$str=replaceStr($str,"\"\"","");
	$str=replaceStr($str,"'","");
	$str=replaceStr($str," ","");
	if (isN($str)) { return false ;}
	return $str;
}

$span="";
function makeSelect($tabName,$colID,$colName,$colSort,$byurl,$separateStr,$id)
{
	global $db,$cache;
	if (!isN($colSort)){ $strOrder=" order by ".$colSort." asc";} 
	if (isN($id)){ $id=0; }
	
	$rc=true;
	switch($tabName)
	{
		case "{pre}user_group": $arr = $cache[6]; break;
		case "{pre}art_topic": $arr = $cache[3]; break;
		case "{pre}vod_topic": $arr = $cache[2]; break;
		default : $rc=false;
	}
	if($rc){
		for($i=0;$i<count($arr);$i++){
			$arr1 = $arr[$i];
			if (intval($id)==$arr1[$colID]){ $strSelected=" selected"; } else{ $strSelected=""; } 
			if (isN($byurl)){ 
				$strValue=$arr1[$colID]; 
			} 
			else{ 
				$strValue=$byurl;
				if(strpos($byurl,"?")>0){ $strValue.="&"; } else{ $strValue.="?"; }
				$strValue.= $tabName."=".$arr1[$colID];
			}
			$str=$str."<option value='".$strValue."' ".$strSelected.">".$span."&nbsp;|—".$arr1[$colName]."</option>";
		}
		unset($arr1);
		unset($arr);
	}
	else{
		$sql="select ".$colID.",".$colName." from ".$tabName.$strOrder;
		$rs=$db->query($sql);
		while($row = $db ->fetch_array($rs))
		{
			if (intval($id)==$row[$colID]){ $strSelected=" selected"; } else{ $strSelected=""; } 
			if (isN($byurl)){
				$strValue=$row[$colID];
			}
			else{
				$strValue=$byurl;
				if(strpos($byurl,"?")>0){ $strValue.="&"; } else{ $strValue.="?"; }
				$strValue.= $tabName."=".$row[$colID];
			}
			$str=$str."<option value='".$strValue."' ".$strSelected.">".$span."&nbsp;|—".$row[$colName]."</option>";
		} 
		if (!isN($span)){
			$span=substr($span,0,strlen($span)-strlen($separateStr));
		}
	}
	return $str;
}

function makeSelectAll($tabName,$colID,$colName,$colPID,$colSort,$pid,$byurl,$separateStr,$id)
{
	global $db,$cache;
	if (isN($id)){ $id=0; }
	switch($tabName)
	{
		case "{pre}vod_type": $arr = $cache[0]; break;
		case "{pre}art_type": $arr = $cache[1]; break;
	}
	for($i=0;$i<count($arr);$i++){
		$arr1 = $arr[$i];
		if($arr1[$colPID]==0){
			if (intval($id)==$arr1[$colID]){ $strSelected=" selected"; } else{ $strSelected=""; } 
			if (isN($byurl)){ $strValue=$arr1[$colID]; } else{ $strValue=$byurl."?".$tabName."=".$arr1[$colID]; } 
			$str=$str."<option value='".$strValue."' ".$strSelected.">&nbsp;|—".$arr1[$colName]."</option>";
			for($j=0;$j<count($arr);$j++){
				$arr2 = $arr[$j];
				if($arr2[$colPID]==$arr1[$colID]){
					if (intval($id)==$arr2[$colID]){ $strSelected=" selected"; } else{ $strSelected=""; } 
					if (isN($byurl)){ $strValue=$arr2[$colID]; } else{ $strValue=$byurl."?".$tabName."=".$arr2[$colID]; } 
					$str=$str."<option value='".$strValue."' ".$strSelected.">&nbsp;|&nbsp;&nbsp;&nbsp;|—".$arr2[$colName]."</option>";
				}
			}
		}
	}
	unset($arr2);
	unset($arr1);
	unset($arr);
	/*
	$sql="select ".$colID.",".$colName." from ".$tabName." where ".$colPID." = ".$pid." order by ".$colSort." Asc";
	$rs=$db->query($sql);
 	while ($row = $db ->fetch_array($rs))
	{
		if ($pid!=0){ $span .=$separateStr; } 
		if (intval($id)==$row[$colID]){ $strSelected=" selected"; } else{ $strSelected=""; } 
		if (isN($byurl)){ $strValue=$row[$colID]; } else{ $strValue=$byurl."?".$tabName."=".$row[$colID]; } 
		$str=$str."<option value='".$strValue."' ".$strSelected.">".$span."&nbsp;|—".$row[$colName]."</option>";
		$str=$str.makeSelectAll($tabName,$colID,$colName,$colPID,$colSort,$row[$colID],$byurl,$separateStr,$id);
	} 
	if (!isN($span)){ $span=substr($span,0,strlen($span)-strlen($separateStr));	}
	*/
	return $str;
}

function makeSelectPlayer($strfrom)
{
	$arr1 = getVodXml("vodplay.xml","player");
	$rc=false;
	for($j=0;$j<count($arr1);$j++){
		if ($strfrom == $arr1[$j][0]) { $strSelected=" selected"; } else{ $strSelected=""; }
		$str = $str. "<option value='" .$arr1[$j][0]. "' " .$strSelected. ">" .$arr1[$j][1]. "</option>";
	}
	return $str;
}

function makeSelectDown($strfrom)
{
	$arr1 = getVodXml("voddown.xml","down");
	$rc=false;
	for($j=0;$j<count($arr1);$j++){
		if ($strfrom == $arr1[$j][0]) { $strSelected=" selected"; } else{ $strSelected=""; }
		$str = $str. "<option value='" .$arr1[$j][0]. "' " .$strSelected. ">" .$arr1[$j][1]. "</option>";
	}
	return $str;
}

function makeSelectServer($strserver)
{
	$arr1 = getVodXml("vodserver.xml","server");
	$rc=false;
	for($j=0;$j<count($arr1);$j++){
		if ($strserver == $arr1[$j][0]) { $strSelected=" selected"; } else{ $strSelected=""; }
		$str = $str. "<option value='" .$arr1[$j][0]. "' " .$strSelected. ">" .$arr1[$j][1]. "</option>";
	}
	return $str;
}

function makeSelectAreaLang($flag,$val)
{
	global $cache;
	switch($flag)
	{
		case "area": $arr = $cache[4]; break;
		case "lang": $arr = $cache[5]; break;
	}
	$i=0;
	foreach($arr as $v){
		if ($val == $v){ $i++; $strSelected=" selected"; } else{ $strSelected=""; }
		$str = $str . "<option value='" .$v. "' " .$strSelected. ">" .$v. "</option>";
	}
	if($val!="" && $i==0){
		$str = $str . "<option value='" .$val. "' selected>" .$val. "</option>";
	}
	return $str;
}

function pagelist_manage($pagecount,$page,$recordcount,$pagesize,$url)
{
	if( $recordcount ==0 ){
		return "";	
	}
	$str = "{<<} {循环} {>>} {跳转} 共{总条数}数据&nbsp;每页{每页数量}条&nbsp; 页次:{当前页}/{总页数}";
	$str=str_replace("{总页数}",$pagecount,$str);
	$str=str_replace("{总条数}",$recordcount,$str);
	$str=str_replace("{当前页}",$page,$str);
	$str=str_replace("{每页数量}",$pagesize,$str);
	$str=str_replace("{<<}","<a href=".str_replace("{p}",1,$url)." class='page'><<</a>",$str);
	$str=str_replace("{>>}","<a href=".str_replace("{p}",$pagecount,$url)." class='page'>>></a>",$str);
	
	if ($page>1){
		$str=str_replace("{<}","<a href=".str_replace("{p}",$page-1,$url)." class='page'><</a>",$str);
	}
	else{
		$str=str_replace("{<}","<span class='page'><</span>",$str);
	} 
	if ($page<$pagecount){
		$str=str_replace("{>}","<a href=".str_replace("{p}",$page+1,$url)." class='page'>></a>",$str);
	}
	else{
		$str=str_replace("{>}","<span class='page'>></span>",$str);
	} 
	
	if (strpos($url,"onclick")>0){
    	$clickstr = getBody($url,"onclick=\"",";");
    	$clickstr = replaceStr($clickstr, "{p}", "document.getElementById('page').value");
    }
    else{
    	$clickstr = "location.href='" . replaceStr($url, "{p}", "' + document.getElementById('page').value + '") . "'";
    }
    	
	$jumpurl = "<input name=\"page\" type=\"text\" id=\"page\" size=3  style='font-size:10px;color:#666;'><input name=\"go\" type=\"button\" id=\"go\" value=\"GO\" style='font-size:10px;color:#666;' onclick=\"var intstr=/^\d+$/;if(intstr.test(document.getElementById('page').value)&&document.getElementById('page').value<=" . $pagecount . "&&document.getElementById('page').value>=1){". $clickstr.";}\">";
		
	$str=str_replace("{跳转}",$jumpurl,$str);
	$i=$page-4; 
	$j=$page+5;
	if ($i<1){
		$j=$j+(1-$i); 
		$i=1;
	} 
	if ($j>$pagecount){
		$i=$i+($pagecount-$j); 
		$j=$pagecount;
		if ($i<1){
			$i=1;
		} 
	} 
	$loopurl="";
	for ($m=$i; $m<=$j; $m=$m+1){
		if ($m==$page){
			$loopurl=$loopurl." <a href=".str_replace("{p}",$m,$url)." class='pagein'>".$m."</a>";
		}
		else{
			$loopurl=$loopurl." <a href=".str_replace("{p}",$m,$url)." class='page'>".$m."</a>";
		}
	}
	$str=str_replace("{循环}",$loopurl,$str);
	return $str;
}

function getColorText($txt,$color,$lens)
{
	if (isN($txt)) { return "";}
	if ($lens>0){ $txt = substring($txt,$lens); }
	if (!isN($color)){ $txt="<font color=".$color.">". $txt . "</font>"; }
	return $txt;
}
function getColorDay($strTime)
{
	if (isN($strTime)) { return ""; }
	$strNow = date('Y-m-d',time());
	if (strpos(",".$strTime,$strNow)>0){ $strColor = "color=\"#FF0000\""; }
	return  "<font " .$strColor. ">" .$strTime. "</font>";
}

function getVodXml($name,$path)
{
	$arr = array();
	if (chkCache($name)){
		$arr = getCache($name,"php");
	}
	else{
		$xmlpath = root ."inc/" .$name;
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xmlpath);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName($path);
		$n=0;
		
		foreach($nodes as $node){
			$status = $node->attributes->item(0)->nodeValue;
			if(!isNum($status)){$status=0;} else { $status=intval($status);}
			
			if($status==1){
				$arr[$n][0] = $node->attributes->item(2)->nodeValue;
				$arr[$n][1] = $node->attributes->item(3)->nodeValue;
				$arr[$n][2] = $node->attributes->item(4)->nodeValue;
				$arr[$n][3] = $node->attributes->item(1)->nodeValue;
				$arr[$n][4] = $node->getElementsByTagName("tip")->item(0)->nodeValue;
				
				$n++;
			}
		}
		unset($nodes);
		unset($xmlnode);
		unset($doc);
		
		$l=count($arr);
		for($i=0;$i<=$l;$i++)
		{
			for($j=($i+1);$j<=$l;$j++)
			{
				if($arr[$i][3] < $arr[$j][3]){
					$tmp=$arr[$j];$arr[$j]=$arr[$i];$arr[$i]=$tmp;
				}
			}
		}
		setCache($name,$arr,1,"php");
	}
	return $arr;
}

function getVodXmlText($name,$path,$from,$k)
{
	$fromarr = explode("$$$",$from);
	$arr1 = getVodXml($name,$path);
	$rc=false;
	$res="";
	
	for($i=0;$i<count($fromarr);$i++){
		for($j=0;$j<count($arr1);$j++){
			if ($fromarr[$i] == $arr1[$j][0]){
				if($rc){ $res.=","; }
				$res.= $arr1[$j][$k];
				$rc=true;
			}
		}
	}
	return $res;
}

function updateCacheFile()
{
	global $db;
	//视频分类缓存
	$arr =array();
	try{
		$cachevodtype= $db->queryarray("SELECT *,\"\" AS childids FROM {pre}vod_type");
		$i=0;
		foreach($cachevodtype as $v){
			$strchild="";
			$rc=false;
			$rs= $db->query("SELECT t_id FROM {pre}vod_type WHERE t_pid=" .$v["t_id"]);
			while ($row = $db ->fetch_array($rs)){
				if($rc){ $strchild .=","; }
				$strchild .= $row["t_id"];
				$rc=true;
			}
			unset($rs);
			if (isN($strchild)){ $strchild = $v["t_id"];} else{$strchild = $v["t_id"] . "," . $strchild;}
			$cachevodtype[$i]["childids"] = $strchild;
			$i++;
		}
		//setGlobalCache("cache_vodtype",$cachevodtype,1,'php');
	}
	catch(Exception $e){ 
		echo "更新视频分类缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachevodtype;
	//文章分类缓存
	try{
		$cachearttype=$db->queryarray("SELECT *,\"\" AS childids FROM {pre}art_type");
		$i=0;
		foreach($cachearttype as $v){
			$strchild="";
			$rc=false;
			$rs= $db->query("SELECT t_id FROM {pre}art_type WHERE t_pid=" .$v["t_id"]);
			while ($row = $db ->fetch_array($rs)){
				if($rc){ $strchild .=","; }
				$strchild .= $row["t_id"];
				$rc=true;
			}
			unset($rs);
			if (isN($strchild)){ $strchild = $v["t_id"];} else{$strchild = $v["t_id"] . "," . $strchild;}
			$cachearttype[$i]["childids"] = $strchild;
			$i++;
		}
		//setGlobalCache("cache_arttype",$cachearttype,1,'php');
	}
	catch(Exception $e){ 
		echo "更新文章分类缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachearttype;
	
	//视频专题缓存
	try{
		$cachevodtopic=$db->queryarray("SELECT * FROM {pre}vod_topic");
		//setGlobalCache("cache_vodtopic",$cachevodtopic,1,'php');
	}
	catch(Exception $e){ 
		echo "更新视频专题缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachevodtopic;
	
	//文章专题缓存
	try{
		$cachearttopic=$db->queryarray("SELECT * FROM {pre}art_topic");
		//setGlobalCache("cache_arttopic",$cachearttopic,1,'php');
	}
	catch(Exception $e){ 
		echo "更新文章专题缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachearttopic;
	
	//地区缓存
	try{
		$str = file_get_contents(root."inc/vodarea.txt");
		$str = replaceStr($str,chr(10),"");
		$cachearea = explode(chr(13),$str);
		//setGlobalCache("cache_vodarea",$cachearea,1,'php');
	}
	catch(Exception $e){ 
		echo "更新地区缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachearea;
	
	//语言缓存
	try{
		$str = file_get_contents(root."inc/vodlang.txt");
		$str = replaceStr($str,chr(10),"");
		$cachelang = explode(chr(13),$str);
		//setGlobalCache("cache_vodlang",$cachearea,1,'php');
	}
	catch(Exception $e){ 
		echo "更新语言缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachelang;
	
	//用户组缓存
	try{
		$cacheusergroup=$db->queryarray("SELECT * FROM {pre}user_group");
		//setGlobalCache("cache_usergroup",$cacheusergroup,1,'php');
	}
	catch(Exception $e){ 
		echo "更新用户组缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cacheusergroup;
	
	setGlobalCache("cache",$arr,1,'php');
	echo "";
}

function getValueByArray($arr,$item,$val)
{
	foreach($arr as $row){
		if($row[$item] == $val){
			$res =  $row;
			break;
		}
	}
	return $res;
}

function chkCache($cacheName,$flag='inc')
{
	$cacheFile=root.'upload/cache/'.app_cacheid.$cacheName.'.'.$flag;
	$mintime = time() - app_cachetime*60;
	if(app_cache ==0){ return false; }
	if (file_exists($cacheFile) && ($mintime < filemtime($cacheFile))){
		return true;
	}
	else{
		return false;
	}
}

function getCache($cacheName,$flag='inc')
{
	$cacheFile=root.'upload/cache/'.app_cacheid.$cacheName.'.'.$flag;
	if($flag=='inc'){
		$result= file_get_contents($cacheFile);
	}
	else{
		$result= @include $cacheFile;
	}
	return $result;
}

function setCache($cacheName,$cacheValue,$cacheType,$flag='inc')
{
	$cacheFile=root.'upload/cache/'.app_cacheid.$cacheName.'.'.$flag;
	if (app_cache==1){
		if($cacheType==1){
			$cacheValue = "<?php\nreturn ".var_export($cacheValue, true).";\n?>";
	        $strlena = file_put_contents($cacheFile, $cacheValue);
		}
		fwrite(fopen($cacheFile,"wb"),$cacheValue);
	}
}

function chkGlobalCache($cacheName,$flag='inc')
{
	$cacheFile=root.'inc/'.app_cacheid.$cacheName.'.'.$flag;
	$mintime = time() - app_cachetime*60;
	if (file_exists($cacheFile) && ($mintime < filemtime($cacheFile))){
		return true;
	}
	else{
		return false;
	}
}

function getGlobalCache($cacheName,$flag='inc')
{
	$cacheFile=root.'inc/'.$cacheName.'.'.$flag;
	if($flag=='inc'){
		$result= file_get_contents($cacheFile);
	}
	else{
		$result= @include $cacheFile;
	}
	return $result;
}

function setGlobalCache($cacheName,$cacheValue,$cacheType,$flag='inc')
{
	$cacheFile=root.'inc/'.$cacheName.'.'.$flag;
	if($cacheType==1){
		$cacheValue = "<?php\nreturn ".var_export($cacheValue, true).";\n?>";
		$strlena = file_put_contents($cacheFile, $cacheValue);
	}
	fwrite(fopen($cacheFile,"wb"),$cacheValue);
}

function getFileByCache($cacheName,$filePath)
{
	if(!file_exists($filePath)){
		die("找不到文件：".$filePath);
	}
	else{
		$res=file_get_contents($filePath);
	}
	return $res;
}

function attemptCacheFile($cPath,$cName)
{
	if(app_dynamiccache==1){
		$cacheFile = root."upload/cache/".$cPath."/".$cName.".html";
		$mintime = time() - app_cachetime*60;
		
		if(file_exists($cacheFile)){
			if($mintime < filemtime($cacheFile)){
				$cachecontent = file_get_contents($cacheFile);
				$cachecontent = replaceStr($cachecontent,"{maccms_runtime}",getRunTime(appTime));
				echo $cachecontent;
				exit;
			}
		}
	}
}

function setCacheFile($cPath,$cName,$cValue)
{
	if(app_dynamiccache==1){
		$cacheFile = root."upload/cache/".$cPath."/".$cName.".html";
		fwrite(fopen($cacheFile,"wb"),$cValue);
	}
}

function getVodCount($flag)
{
	global $db;
	if ($flag == "day"){
		$where = " AND STR_TO_DATE(d_time,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	return $db->getOne("SELECT count(*) FROM {pre}vod WHERE d_hide=0 AND d_type>0" .$where);
}

function getArtCount($flag)
{
	global $db;
	if ($flag == "day"){
		$where = " AND STR_TO_DATE(a_time,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	return $db->getOne("SELECT count(*) FROM {pre}art WHERE a_hide=0 AND a_type>0".$where);
}

function getUserCount($flag)
{
	global $db;
	if ($flag == "day"){
		$where = " AND STR_TO_DATE(u_regtime,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	return $db->getOne("SELECT count(*) FROM {pre}user WHERE u_status=1".$where);
}

function getKeysLink($key,$ktype)
{
	if (!isN($key)){
		$key = str_replace(","," ",$key);
		$key = str_replace("|"," ",$key);
		
		$arr = explode(" ",$key);
		for ($i=0;$i<count($arr);$i++){
			if (!isN($arr[$i])){
				$str = $str . "<a target='_blank' href='".app_installdir."search.php?".$ktype."=". urlencode($arr[$i])."'>".$arr[$i]."</a>&nbsp;";
			}
		}
	}
	return $str;
}

function repPse($txt,$id)
{
	$id = $id % 7;
	if (isN($txt)){ $txt=""; }
	$psecontent = getFileByCache("dim_pse2",root. "inc/dim_pse2.txt" );
	if (isN($psecontent)){ $psecontent = ""; }
	$psecontent = replaceStr($psecontent,chr(10),"");
	$psearr = explode(chr(13),$psecontent);
	$i=count($psearr)+1;
	$j=strpos($txt,"<br>");
	
	if ($j==0){ $j=strpos($txt,"<br/>");}
	if ($j==0){ $j=strpos($txt,"<br />");}
	if ($j==0){ $j=strpos($txt,"</p>");}
	if ($j==0){ $j=strpos($txt,"。")+1;}
	
	if ($j>0){
		$res= substring($txt,$j-1) . $psearr[$id % $i] . substring($txt,strlen($txt)-$j,$j);
	}
	else{
		$res= $psearr[$id % 1]. $txt;
	}
	return $res;
}

function getTypeIDS($id,$tabName)
{
	global $db;
	$rc=false;
	$arr = $db->queryarray("select t_id,t_pid from ". $tabName ." a order by t_sort asc");
	$tmpSp= explode(",",$id);
	$str="";
	for($j=0;$j<count($tmpSp);$j++){
		if (isN($str)){ $str = $tmpSp[$j];} else {$str = $str . "," . $tmpSp[$j]; }
		for($i=0;$i<count($arr);$i++){
			if($arr[$i]["t_pid"]=="".$tmpSp[$j]){
				$str= $str .",". getTypeIDS($arr[$i]["t_id"],$tabName);
			}
		}
	}
	return $str;
}


function getAddressInfo($flag, $vodname, $vodfrom, $vodserver, $vodurl)
{
    if (isN($vodurl)) { $vodurl="";}
    if (isN($vodfrom)) { $vodfrom="";}
    $rc=false;
    
    $playurlarr = explode("$$$",$vodurl); $playurlarrlen = count($playurlarr);
    $playfromarr = explode("$$$",$vodfrom); $playfromarrlen = count($playfromarr);
    $playserverarr = explode("$$$",$vodserver); $playserverarrlen = count($playserverarr);
    
    for ($i=0;$i<$playfromarrlen;$i++){
        $rc1 = false;
        if ($rc) { $str = $str . "$$$"; }
        
        $from = $playfromarr[$i];
        if (isN($from)) { $from = "null"; }
        
        if ($playserverarrlen >= $i){
        	$server = getVodXmlText("vodserver.xml","server", $playserverarr[$i] , 2);
        }
        if (isN($server)) { $server = "null"; }
        
        if ($playurlarrlen >= $i){
            $url = $playurlarr[$i];
            if (isN($url)){
            	$url = "null";
            }
            else{
                $urlarr = explode("#",$url);
                $url = "";
                for ($j=0;$j<count($urlarr);$j++){
                    if (!isN($urlarr[$j])){
                        if ($rc1) { $url = $url ."#"; }
                        $url = $url . $urlarr[$j];
                        $rc1 = true;
                    }
                }
            }
        }
        $str = $str . $server . "$$" . $from . "$$" . $url;
        $rc = true;
    }
    $str = replaceStr($str,"'","\'");
    if (app_encrypt == 1){
        $urlStr = "var maccms_".$flag."list = unescape('" . escape($str) . "');";
    }
    else if (app_encrypt == 2){
        $urlStr = base64_encode(escape($str));
        $urlStr = "var maccms_".$flag."list = unescape(base64decode('" . $urlStr . "'));";
    }
    else{
        $urlStr = "var maccms_".$flag."list = '" . $str . "';";
	}
    return  "var playname='" . replaceStr($vodname,"'","\'") . "';" . "\n" . $urlStr ;
}

function getPlayer()
{
    return "<script>var strUrlQS=getQS('" . app_vodsuffix ."');getPlayer(strUrlQS[1],strUrlQS[2]);</script>";
}

function getDowner()
{
    return "<script>var strUrlQS=getQS('" . app_vodsuffix ."');getDowner(strUrlQS[1],strUrlQS[2]);</script>";
}

function getUserFlag($flag)
{
	switch ($flag)
	{
		case 1:
			$res="包时";
			break;
		case 2:
			$res="IP段";
			break;
		default:
			$res="计点";
			break;
	}
	return $res;
}

function getTypeByPopedomFilter($flag)
{
	global $cache;
	$a="0,";
	$b="0,";
	$rc=false;
	$userid=$_SESSION["userid"];
	$usergroup=$_SESSION["usergroup"];
	if (isN($usergroup)) { $usergroup=0; } else { $usergroup=intval($usergroup);}
	
	
	$arr = getValueByArray($cache[6],"ug_id" ,$usergroup);
	for ($i=0;$i<count( $cache[6] );$i++){
		$arr1 = $cache[6][$i];
		
		if ($usergroup==0 ){
			if  ( strpos(",".$arr1["ug_popedom"], ",1,") >0 ){
				$a .= $a . $arr1["ug_type"];
			}
		}
		else{
			if (  strpos(",".$arr1["ug_popedom"], ",1,") >0  && $arr1["ug_popvalue"] > $arr["ug_popvalue"] ){
				$a .= $a . $arr1["ug_type"];
			}
			if ($arr1["ug_popvalue"] <= $arr["ug_popvalue"]){
				$b = $b . $arr1["ug_id"]. ",";
			}
		}
	}
	
	$a = replaceStr($a,",,",",");
	$b = replaceStr($b,",,",",");
	
	if (substring($a,1,strlen($a)-1) == ","){ $a = substring($a,strlen($a)-1); }
	if (substring($b,1,strlen($b)-1) == ","){ $b = substring($b,strlen($b)-1); }
	
	if ($flag=="menu"){
		$res = " and t_id not in(". $a .") ";
	}
	else{
		$res = " and d_type not in(". $a .") and d_usergroup in(". $b .") ";
	}
	return $res;
}

function getUserPopedom($id,$flag)
{
	global $cache;
	
	$userid=$_SESSION["userid"];
	$usergroup=$_SESSION["usergroup"];
	if (isN($usergroup)) { $usergroup=0; } else { $usergroup=intval($usergroup);}
	$result=false;
	$ug_popvalue1=0;
	$num=0;
	if ($flag== "list"){
		$flag = "1";
	}
	else if ($flag== "vod"){
		$flag = "2";
	}
	else if ($flag=="play"){
		$flag = "3";
	}
	else if ($flag=="down"){
		$flag = "4";
	}
	
	for ($i=0;$i<count($cache[6]);$i++){
		$ug_id=$cache[6][$i]["ug_id"];
		if ($ug_id==$usergroup){ $ug_popvalue1=$cache[6][$i]["ug_popvalue"]; }
		
		if (strpos(",".$cache[6][$i]["ug_type"],",".$id.",")>0 && strpos(",".$cache[6][$i]["ug_popedom"],",".$flag.",")>0){
			 $num++;
			 if ($ug_popvalue1 >= $cache[6][$i]["ug_popvalue"]){ $result=true; break;}
		}
	}
	if ($num==0){ $result=true; }
	return $result;
}
?>