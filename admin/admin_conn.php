<?php
require_once (dirname(__FILE__)."/../inc/conn.php");
$menulist= "系统管理||2|||扩展功能||3|||视频管理||4|||文章管理||5|||用户管理||6|||模板及生成||7|||采集管理||8";

function headAdmin($title)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo $title?> - 苹果CMS</title>
<link rel="stylesheet" type="text/css" href="../images/admin.css" />
<link rel="stylesheet" type="text/css" href="../images/default/easyui.css" />
<link rel="stylesheet" type="text/css" href="../images/icon.css" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="../js/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="../js/adm/admin.js"></script>
<script type="text/javascript" src="../js/function.js"></script>
</head>
<body>
<?php
}

function footAdmin()
{
	echo "<div align=center>" . getRunTime() .  "</div><div align=center >Copyright 2008-2013 All rights reserved. <a target=\"_blank\" href=\"http://www.maccms.com\">Maccms</a></div>" . chr(10) . "</body>" . chr(10) . "</html>";
	dispseObj();
}

function chkSql($str,$flag)
{
	$checkStr="<|>|%|%27|'|''|;|*|and|exec|dbcc|alter|drop|insert|select|update|delete|count|master|truncate|char|declare|where|set|declare|mid|chr";
	if (isN($str)){ return ""; }
	$arr=explode("|",$checkStr);
	for ($i=0;$i<count($arr);$i++){
		if (strpos(strtolower($str),$arr[$i]) >0){
			if ($flag==false){
				switch ($arr[$i]){
					case "<":$re="&lt;";break;
					case ">":$re="&gt;";break;
					case "'":
					case "\"":$re="&quot;";break;
					case ";":$re="；";break;
					default:$re="";break;
				}
				$str=str_replace($arr[$i],$re,$str);
			}
			else{
				errMsg ("系统提示","数据中包含非法字符");
			}
		}
	}
	return $str;
}

function chkLogin()
{
	global $db;
	$m_id = getCookie("adminid");
	$m_id = chkSql($m_id,true);
	$m_name = getCookie("adminname");
	$m_name = chkSql($m_name,true);
	
	if (!isN($m_name) && !isN($m_id)){
		$row = $db->getRow("SELECT * FROM {pre}manager WHERE m_name='" . $m_name ."' AND m_id= '".$m_id ."' AND m_status ='1'");
		if($row){
			$loginValidate = md5($row["m_random"] . $row["m_name"] . $row["m_id"]);
			if (getCookie("admincheck") != $loginValidate){ 
			   sCookie ("admincheck","");
			   die( "<script>top.location.href='index.php?action=login';</script>");
			}
		}
		else{
			sCookie ("admincheck","");
		    die("<script>top.location.href='index.php?action=login';</script>");
		}
	}
	else{
		die("<script>top.location.href='index.php?action=login';</script>");
	}
}
function getcon($varName)
{
	switch($res = get_cfg_var($varName))
	{
	case 0:
		return NO;
		break;
	case 1:
		return YES;
		break;
	default:
		return $res;
		break;
	}
}
function isfun($funName)
{
	return (false !== function_exists($funName))?YES:NO;
}

function chkPopedom($str)
{
}
define("macUrl","h"."t"."tp"."://w"."w"."w".".ma"."ccm"."s.co"."m/");
function cBreakpoint($fname)
{
	if (file_exists($fname.".html")) { return true ;} else { return false;}
}

function dBreakpoint($fname)
{
	if (file_exists($fname.".html"))  { unlink($fname.".html"); }
}

function wBreakpoint($fname,$url)
{
	$fdes = "<meta http-equiv=\"refresh\" content=\"0;url=".$url."\">";
	fwrite(fopen($fname.".html","wb"),$fdes);
}
	
function gBreakpoint($fname)
{
	return file_get_contents($fname . ".html");
}

function imageWaterMark($groundImage,$cpath,$waterPos=0,$waterText="") 
{ 
      $isWaterImage = FALSE; 
      $textFont=5;
      $textColor="#FF0000";
      $formatMsg = "暂不支持该文件格式，请用图片处理软件将图片转换为Gif、JPG、PNG格式。"; 
      if(!empty($groundImage) && file_exists($groundImage)) {
          $ground_info = @getimagesize($groundImage);
          $ground_w = $ground_info[0];
          $ground_h = $ground_info[1];
          switch($ground_info[2]) {
              case 1:$ground_im = @imagecreatefromgif($groundImage);break; 
              case 2:$ground_im = @imagecreatefromjpeg($groundImage);break; 
              case 3:$ground_im = @imagecreatefrompng($groundImage);break; 
              default: echo $formatMsg;return;
          } 
      } else { 
          echo "需要加水印的图片不存在！"; return;
      } 
      
		$temp = @imagettfbbox(ceil($textFont*2.5),0,$cpath."\arial.ttf",$waterText);
		$w = $temp[2] - $temp[6]; 
		$h = $temp[3] - $temp[7];
		unset($temp); 
		$label = "文字区域"; 
		
      if( ($ground_w<$w) || ($ground_h<$h) ) {
		echo "需要加水印的图片的长度或宽度比水印".$label."还小，无法生成水印！"; 
		return; 
      } 
      switch($waterPos) {
          case 0:
              $posX = ($ground_w - $w) / 2; 
              $posY = ($ground_h - $h) / 2; 
              break; 
          case 1:
              $posX = $ground_w - $w; 
              $posY = 0; 
              break; 
          case 2:
              $posX = $ground_w - $w; 
              $posY = $ground_h - $h - 10; 
              break; 
          case 3:
              $posX = 0; 
              $posY = 0; 
              break; 
          case 4:
              $posX = 0; 
              $posY = $ground_h - $h; 
              break; 
          default:
              $posX = rand(0,($ground_w - $w)); 
              $posY = rand(0,($ground_h - $h)); 
              break;
      }
      @imagealphablending($ground_im, true); 
      if( !empty($textColor) && (strlen($textColor)==7) ) {
		$R = hexdec(substr($textColor,1,2));
        $G = hexdec(substr($textColor,3,2));
        $B = hexdec(substr($textColor,5)); 
      }
      else{ 
        echo "水印文字颜色格式不正确！";return;
      } 
      @imagestring ( $ground_im, $textFont, $posX, $posY, $waterText, @imagecolorallocate($ground_im, $R, $G, $B));
      @unlink($groundImage); 
      switch($ground_info[2]) {
          case 1: @imagegif($ground_im,$groundImage);break; 
          case 2: @imagejpeg($ground_im,$groundImage);break; 
          case 3: @imagepng($ground_im,$groundImage);break; 
          default: echo $errorMsg;return;
      } 
      if(isset($water_info)) unset($water_info); 
      if(isset($water_im)) @imagedestroy($water_im); 
      unset($ground_info); 
      @imagedestroy($ground_im); 
} 

function uploadftp($picpath,$picfile)
{
	$Newpicpath = replaceStr($picpath,"../../","");
	$Newpicpath = replaceStr($Newpicpath,"../","");
	$ftp = new AppFtp(app_ftphost , app_ftpuser ,app_ftppass , app_ftpport , app_ftpdir);
	if( $ftp->ftpStatus == 1){;
		$localfile= root . $Newpicpath . $picfile;
		$remotefile= app_ftpdir.$Newpicpath . $picfile;
		$ftp -> mkdirs( app_ftpdir.$Newpicpath );
		$ftpput = $ftp->put($localfile, $remotefile);
		if(!$ftpput){
			echo "上传图片到FTP远程服务器失败!";
			exit;
		}
		$ftp->bye();
		if (app_ftpdel==1){
			unlink( $picpath . $picfile );
		}
	}
	else{
		echo $ftp->ftpStatusDes;exit;
	}
}

function getSavePicPath()
{
	if (app_picpath == 1){
		$res = date('Y')."-".date("m");
	}
	elseif (app_picpath == 2){
		$res  = date('Y')."-".date("m")."-".date("d");
	}
	elseif (app_picpath == 3){
		for ($i=0; $i<50;$i++)
		{
			$path = date('Y')."-".date("m")."-".date("d") . "-".$i;
			$path1 = $_SESSION["upfolder"] . "/" . $path . "/";
			if (file_exists($path1)){
				$filecount= sizeof(scandir($path1));
				if($filecount>500){
					$res = date('Y')."-".date("m")."-".date("d") . "-". ($i+1);
				}
				else{
					$res = $path;
					break;
				}
			}
			else{
				$res = $path;
				break;
			}
		}
	}
	else{
		$res  = "";
	}
	return $res ;
}

function checkField($fieldName,$tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query("SHOW COLUMNS FROM ".$tableName);
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row["Field"];
	}
	unset($rs);
	if(in_array($fieldName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function checkIndex($sIndexName,$tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query("SHOW INDEX FROM ".$tableName);
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row["Column_name"];
	}
	if(in_array($sIndexName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function checkTable($tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query("SHOW TABLES ");
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row["Tables_in_".app_dbname];
	}
	unset($rs);
	if(in_array($tableName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}
?>