<?php
ob_end_clean();
ob_implicit_flush(true);
require_once ("admin_conn.php");
require_once ("version.php");
chkLogin();

$action = be("get","action");
$updateserver = "ht"."tp:/"."/w"."ww"."."."ma"."c"."cm"."s."."c"."om"."/u"."pd"."ate/"."p/";
$updatelog = "bak/update.xml";
$updatelogserver = "bak/updateserver.xml";

$adpath = $_SERVER["SCRIPT_NAME"];
$adpath= substring($adpath,strripos($adpath,"/"));
$n = strripos($adpath,"/");
$adpath= substring($adpath,strlen($adpath)-$n ,$n+1) ."/";

switch($action)
{
	case "autoupfile" : autoupfile();break;
	case "upfile" : headAdmin ("在线更新"); upfile();break;
	default : main();break;
}
dispseObj();

function main()
{
	
}

function upfile()
{
	global $updateserver,$updatelog,$updatelogserver,$adpath;
	echo "<div class='Update'><h1>自动升级进行中,请稍后......</h1><textarea rows=\"15\" readonly>";
	
	$status=false;
	$k=0;
	$n = be("get","n");
	$p = be("get","p") ;
	if (!isNum($n)) {$n = 0;} else {$n=intval($n);}
	if (!isNum($p)) {$p = 0;} else {$p=intval($p);}
	
	if ($n == 0){
		$verstr = getPage($updateserver . "?v=". version,"utf-8");
		fwrite(fopen($updatelogserver,"wb"),$verstr);
		
		if (strpos($verstr,"</maccms>")>0){
			$doc = new DOMDocument();
			$doc -> formatOutput = true;
			$doc -> loadxml($verstr);
			$xmlnode = $doc -> documentElement;
			$serverversion = $xmlnode->getElementsByTagName("version")->item(0)->nodeValue;
			$vardec = $xmlnode->getElementsByTagName("des")->item(0)->nodeValue;
			unset($xmlnode);
		    unset($doc);
			$isupdate = (version != $versionserver);
			
			
			if ($isupdate){
				$msg =  "获取升级文件升级列表成功,进入升级程序...";
				$msg2 =  "<meta http-equiv='refresh' content=2;url='admin_update.php?action=upfile&n=1&p=0'>";
			}
			else{
				$msg = "已经是最新版本无需更新...";
				$msg2 = "<meta http-equiv='refresh' content=2;url='index.php?action=wel'>";
			}
		}
		else{
			$msg =  "获取升级信息失败,请重试...";
			$msg2 =  "<meta http-equiv='refresh' content=2;url='index.php?action=wel'>";
		}
	}
	else{
		
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($updatelogserver);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName("file");
		$fileslen = $nodes->length-1;
		
		$logstr = file_get_contents($updatelog);
		$doc2 = new DOMDocument();
		$doc2 -> formatOutput = true;
		$doc2 -> load($updatelog);
		$xmlnode2 = $doc2 -> documentElement;
		$nodes2 = $xmlnode2->getElementsByTagName("file");
		
		if ($fileslen < 1){
			$msg =  "升级文件列表为空，退出升级程序...";
			$msg2 =  "<meta http-equiv='refresh' content=2;url='index.php?action=wel'>";
		}
		else if ($p >$fileslen){
			$msg =  "恭喜您程序升级完毕，退出升级程序...";
			$msg2 =  "<meta http-equiv='refresh' content=2;url='index.php?action=wel'>";
		}
		else{
			
			for ($i=$p;$i<=$fileslen;$i++){
				
				$filesrc = $nodes->item($i)->attributes->item(2)->nodeValue;
				$filetime = $nodes->item($i)->attributes->item(1)->nodeValue;
				
						
				if (strpos($filesrc,".txt")>0){
					$savefilepath = replaceStr($filesrc,"txt","php");
				}
				else{
					$savefilepath = $filesrc;
				}
				
				$savefilepath= replaceStr($savefilepath,chr(10),"");
				$savefilepath= replaceStr($savefilepath,chr(13),"");
				$savefilepath = replaceStr($savefilepath,"admin/",$adpath);
				$savefilepath = replaceStr($savefilepath,version,"");
				$savefilepath = "../".$savefilepath;
				
				$filestr = getPage($updateserver.$filesrc,"utf-8");
				
				if ($filestr != ""){
					$strTemp = "<file src=\"".$filesrc."\" time=\"".$filetime."\"/>";
					if (strpos($logstr,$strTemp) <= 0){
						
						
						
						fwrite(fopen( $savefilepath,"wb"),$filestr);
						echo "下载文件". ($i+1) .""." ".$savefilepath ." 成功". "\n";
						
						if (strpos($logstr,"<file src=\"".$filesrc."\"") > 0){
							foreach($nodes2 as $node){
								if ($filesrc == $node->attributes->item(0)->nodeValue){
									$node->attributes->item(1)->nodeValue = $filetime;
								}
							}
						}
						else{
							$nodenew = $doc2 -> createElement("file");
							$nodesrc1 =  $doc2 -> createAttribute("src");
							$nodesrc2 =  $doc2 -> createTextNode($filesrc);
							$nodesrc1 -> appendChild($nodesrc2);
							$nodetime1 =  $doc2 -> createAttribute("time");
							$nodetime2 =  $doc2 -> createTextNode($filetime);
							$nodetime1 -> appendChild($nodetime2);
							$nodenew -> appendChild($nodesrc1);
							$nodenew -> appendChild($nodetime1);
							$doc2->getElementsByTagName("updatefiles")-> item(0)  -> appendChild($nodenew);
							unset($nodenew);
							unset($nodesrc1);
							unset($nodetime1);
						}
						$status=true;
					}
					else{
						echo "下载文件".($i+1).""." ".$savefilepath." 跳过". "\n";
					}
				}
				$k = $k + 1;
				$p = $p + 1;
				if ( ($k > 8) || ($p>$fileslen) ){
					$msg = "请稍候,稍做休息继续升级...";
					$msg2 = "<meta http-equiv='refresh' content=2;url='admin_update.php?action=upfile&n=1&p=".$p."'>";
					break;
				}
				
			}
		}
		if($status){
			$doc2 -> save($updatelog);
		}
		
		
		unset($nodes2);
		unset($xmlnode2);
    	unset($doc2);
		unset($nodes);
		unset($xmlnode);
    	unset($doc);
	}
	echo  $msg . "</textarea></div><center>" . $msg2 . "</center>";
}


function autoupfile()
{
	global $updateserver,$updatelog,$updatelogserver,$adpath;
	$a = be("get","a");$b = be("get","b");$c = be("get","c");$d = be("get","d");
	$e = getPage( "h"."t"."tp:/"."/w"."w"."w"."."."m"."a"."c"."cm"."s."."c"."o"."m"."/u"."pd"."ate/".$a."/" . $b,"utf-8");
	if ($e!=""){
	if (($d!="") && strpos(",".$e,$d) <=0){ return; }
	$b = replacestr($b,"admin/",$adpath);$b = replaceStr($b,version,"");$b = "../".$b;	$f=filesize($b);
	if (intval($c)<>intval($f)) { fwrite(fopen( $b,"wb"),$e);  }
	}
}
?>