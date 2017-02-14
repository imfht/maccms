<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();

$file = be("get","file");
if(isN($file)){ errMsg("文件不能为空"); }
downFile($file);

function downFile($fileName)
{
	//$fileName = iconv("UTF-8", "GBK", $fileName);
	$filePath = "../../upload/export/". iconv("UTF-8", "GBK", $fileName) .".txt";
	$file = fopen($filePath,"r");
	Header("Content-type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Accept-Length: ".filesize($filePath));
	if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")){
 		Header("Content-Disposition: attachment; filename=". urlencode($fileName) .".txt;");
 	}
 	else{
 		Header("Content-Disposition: attachment; filename=". $fileName .".txt;");
 	}
	echo fread($file,filesize($filePath));
	fclose($file);
	exit;
}
?>