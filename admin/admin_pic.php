<?php
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("admin_conn.php");

chkLogin();
$action = be("all","action");
$wjs = be("get","wjs");

switch($action)
{
	case "downpic" : downpic();break;
	case "syncpic" : headAdmin ("视频远程图片同步"); syncpic();break;
	case "syncartpic" : headAdmin ("文章远程图片同步"); syncartpic();break;
	case "del" : del();break;
	case "picchk":
	case "picchkedit": picchkedit();break;
	case "picchkfile": picchkfile();break;
	
	default : headAdmin ("图片管理") ; pic();break;
}
dispseObj();

function del()
{
    $fnames = be("arr","fname");
    $arr = explode(",",$fnames);
    foreach($arr as $a){
    	
		if ( (substring($a,9) != "../upload") || count( explode("../",$a) ) > 2) {
			
		}
		else{
			$a = iconv('UTF-8','GB2312',$a);
			if(file_exists($a)){
				unlink($a);
			}
		}
    }
    echo "删除记录成功";
}

function downpic()
{
	$path = be("get","path");
	$url = be("get","url");
	$pfile = be("get","file");
	savepic ($url,$path,$pfile);
}

function syncpic()
{
	global $db;
	
	$pic_fw = be("all","pic_fw"); $pic_fwdate = be("all","pic_fwdate");
	$pic_xx = be("all","pic_xx");
	
	
	$flag = "#err". date('Y-m-d',time());
	$sql = "SELECT count(d_id) FROM {pre}vod WHERE 1=1 AND d_pic LIKE 'http://%' ";
	if ($pic_fw=="2" && $pic_fwdate!=""){
		$where = $where . " AND STR_TO_DATE(d_time,'%Y-%m-%d')='".$pic_fwdate."' ";
	}
	if ($pic_xx=="1"){
		$where = $where . " and instr(d_pic,'#err')=0 ";
	}
	else if ($pic_xx=="2"){
		$where = $where . " and instr(d_pic,'".$flag."')=0 ";
	}
	else if ($pic_xx=="3"){
		$where = $where . " and instr(d_pic,'#err')>0 ";
	}
	$nums = $db->getOne($sql.$where);
	
	if($nums>0){
		$page = be("get","page");
		if (isN($page)){ $page=1;} else{ $page=intval($page);}
		$sql = "SELECT d_id,d_pic FROM {pre}vod WHERE 1=1 AND d_pic LIKE 'http://%' ". $where;
		$pagecount = ceil($nums/20);
		$sql .= " limit ". ($pagecount-1) .",20";
		$rs = $db->query($sql);
		echo "<font color=red>当前共".$nums."条数据需要同步下载,每次同步20个数据,正在开始同步第".$pagecount."页数据的的图片</font><br>";
		
		$num=0;
		while ($row = $db ->fetch_array($rs))
		{
				$d_pic = $row["d_pic"];
				if (strpos($d_pic,"#err")){
					$picarr = explode("#err",$d_pic);
					$d_pic =$picarr[0];
				}
				
				$status = false;
				$picname = time(). $num;
				$extName = @substr($d_pic,strlen($d_pic)-4);
				
				if($extName!=".jpg" || $extName!=".bmp" || $extName!=".png" || $extName!=".gif"){
					$extName=".jpg";
				}
				
				$picpath = "../upload/vod" . "/" . getSavePicPath() . "/" ;
				$picpath = replaceStr($picpath,"///","/");
				$picpath = replaceStr($picpath,"//","/");
				if (!is_dir($picpath)) {
					mkdir($picpath);
				}
				
				$picfile = $picname . $extName;
				
				$status = savepic ($d_pic,$picpath,$picfile);
				
				if ($status){
					$d_pic = replaceStr($picpath,"../","").$picfile ;
				}
				else{
					$d_pic = $d_pic . $flag;
				}
				$num++;
				$db->query("UPDATE {pre}vod set d_pic='".$d_pic."' where d_id='".$row["d_id"]."'");
		}
		echo "<br><font color=red>暂停3秒后继续同步图片</font><br><script>setTimeout(\"jump();\",3000);function jump(){location.href='admin_pic.php?action=syncpic&page=".($page+1)."&pic_fw=".$pic_fw."&pic_fwdate=".$pic_fwdate."&pic_xx=".$pic_xx."';}</script>";
		
	}
	else{
		alertUrl ("恭喜，所有外部图片已经成功同步到本地","admin_vod.php");
	}
	unset($rs);
}

function syncartpic()
{
	global $db;
	$ids = be("get","ids");
	$sql = "SELECT count(a_id) FROM {pre}art WHERE a_content LIKE '%src=\"http://%' ";
	if(!isN($ids)){
		$where = " and a_id not in (" .$ids.") ";
	}
	else{
		$ids="0";
	}
	$nums = $db->getOne($sql.$where);
	if($nums>0){
		$page = be("get","page");
		if (isN($page)){ $page=1;} else{ $page=intval($page);}
		$sql = "SELECT a_id,a_content FROM {pre}art WHERE a_content LIKE '%src=\"http://%' " .$where;
		$pagecount = ceil($nums/20);
		$sql .= " limit ". ($pagecount-1) .",20";
		$rs = $db->query($sql);
		echo "<font color=red>共".$nums."条数据需要同步下载,每次同步20个数据,正在开始同步第".$pagecount."页数据的的图片</font><br>";
		
		$num=0;
		while ($row = $db ->fetch_array($rs))
		{
				$a_content = $row["a_content"];
				$status = false;
				
				$rule = buildregx("<img[^>]*src\s*=\s*['".chr(34)."]?([\w/\-\:.]*)['".chr(34)."]?[^>]*>","is");
				preg_match_all($rule,$a_content,$matches);
				
				$matchfieldarr=$matches[1];
				$matchfieldstrarr=$matches[0];
				$matchfieldvalue="";
				foreach($matchfieldarr as $f=>$matchfieldstr)
				{
					$matchfieldvalue=$matchfieldstrarr[$f];
					$a_pic = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
					
					$picname = time(). $num;
					if (strpos($a_pic,".jpg") || strpos($a_pic,".bmp") || strpos($a_pic,".png") || strpos($a_pic,".gif")){
						$extName= substring($a_pic,4,strlen($a_pic)-4);
					}
					else{
						$extName=".jpg";
					}
					$picpath = "../upload/art" . "/" . getSavePicPath() . "/" ;
					$picpath = replaceStr($picpath,"///","/");
					$picpath = replaceStr($picpath,"//","/");
					if (!is_dir($picpath)) {
						mkdir($picpath);
					}
					$picfile = $picname . $extName;
					$status = savepic ($a_pic,$picpath,$picfile,"art");
					if ($status){
						$a_content = replaceStr($a_content,$a_pic,  replaceStr($picpath.$picfile,"../", app_installdir ) );
					}
					else{
						$a_content = replaceStr($a_content,$a_pic,  "" );
					}
				}
				$num++;
				$db->query("UPDATE {pre}art set a_content='".$a_content."' where a_id='".$row["a_id"]."'");
		}
		echo "<br><font color=red>暂停3秒后继续同步图片</font><br><script>setTimeout(\"jump();\",3000);function jump(){location.href='admin_pic.php?action=syncartpic&page=".($page+1)."&ids=".$ids."';}</script>";
	}
	else{
		if ($ids!="0"){ $des= "以下文章ID:" . substring($ids,strlen($ids)-1,2). "的图片同步失败，请检查图片链接是否失效"; }else { $des = "恭喜，所有外部图片已经成功同步到本地！"; }
		alertUrl ("$des","admin_art.php");
	}
	unset($rs);
}

function savepic($picUrl,$picpath,$picfile,$flag='vod')
{
	global $wjs;
	
	
	if($picfile==""){
		echo "file参数不正确";
		$status= false;
	}
	else{
		mkdirs( dirname($picpath) );
		$picUrlFilePath = substring($picUrl, strrpos($picUrl,"/") ,0);
		$picUrlFileName = substring($picUrl, strlen($picUrl)-strrpos($picUrl,"/")-1 ,strrpos($picUrl,"/")+1 );
		$picUrlFileName = rawurlencode  ($picUrlFileName);
		$picUrlNew = $picUrlFilePath . "/" . $picUrlFileName;
		
		$imgsbyte= getPage($picUrlNew,"utf-8");
		$size = round(strlen($imgsbyte)/1024, 3) ;
		if (strlen($imgsbyte) <100 || strpos(",".$imgsbyte,"<html") >0 || strpos(",".$imgsbyte,"<HTML") >0 ){
			echo "保存失败：<font color=red>非正常的图片,请访问测试:</font> <a target=_blank href=".$picUrl.">". $picUrl ."</a><br>";
			$status=false;
		}
		else{
			fwrite(fopen($picpath . $picfile,"wb"),$imgsbyte);
			if(app_watermark==1){
				imageWaterMark($picpath . $picfile,getcwd()."\\editor",app_waterlocation,app_waterfont);
			}
			if ($flag=="vod" && app_ftp==1){
				uploadftp( $picpath ,$picfile );
			}
			echo "<a target=_blank href=".$picpath.$picfile.">".$picpath.$picfile."</a>保存成功：<font color=red>".$size."Kb</font><br>";
			$status=true;
		}
		ob_flush();flush();
		if (!isN($wjs)) { echo "<script>parent.dstate=true;</script>";}
	}
	return $status;
}

function pic()
{
	$rootPath = be("get","rootpath");
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","?action=del");
				$("#form1").submit();
			}
			else{return false}
		});
		$("#btnChk").click(function(){
			if(confirm('确定要清理垃圾图片吗?')){
				location.href= "?action=picchk";
			}
			else{return false}
		});
		$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	    	location.href=location.href;
	    }
		});
	});
</script>
</head>
<body>
<form action="?action=del" method="post" id="form1" name="form1"> 
<table class="tb">
	<tr>
	<td><strong>文件名</strong></td>
    <td width="20%"><strong>文件大小</strong></td>
    <td width="20%"><strong>修改时间</strong></td>
	</tr>
<?php
if( isN($rootPath) ){ $rootPath = "../upload"; }
bianliFolder($rootPath);
?>
<tr><td colspan=4>
<input type="checkbox" name="chkall" onClick="checkAll(this.checked,'fname[]')"/>全选
<input name=button type=submit class=input value=删除 id=btnDel>
<input name=button type=button class=input value=清理垃圾图片 id=btnChk> 【一次无法彻底清理完毕垃圾图片，请尝试多次清理】
</td>
</tr>
</table>
</form>
</body>
</html>
<?php
}

function bianliFolder($currentPath)
{
	if (substring($currentPath,9) != "../upload") { $currentPath = "../upload"; }
	$upperFolder = substring($currentPath,strrpos($currentPath,"/"));
	
	if (count( explode("../",$currentPath) ) > 2) {
		echo "<tr><td colspan=\"5\"> 非法的目录请求 </td></tr>";
		return;
	}
	
	if ($currentPath !="../upload"){
		echo "<tr><td colspan=\"3\"><img src=\"../images/icons/dir2.gif\"> <a href=\"?rootpath=" .$upperFolder."\">上级目录</a></td></tr>";
	}
    
    $filters = ",,cache,artcollect,downdata,playdata,export,vodcollect,";
    
    if(is_dir($currentPath)){
		$fcount= sizeof(scandir($currentPath)) -2 ;
		$num = 0;
		$sumsize = 0;
		if($fcount>0){
			foreach( glob($currentPath.'/*') as $single){
					if ( is_dir($single) ){
						$single = str_replace($currentPath."/","",$single);
						if(strpos($filters,",".$single.",")<=0){
							echo "<tr><td colspan=\"3\"><img src=\"../images/icons/dir.gif\"> <a href=\"?rootpath=".$currentPath."/".$single."\">".$single."</a></td></tr>";
						}
					}
					else{
						if (strpos($single,".html") <=0 && strpos($single,".htm") <=0){
							$num++;
							$fsize = round( filesize( $single ) / 1024 ,2 );
							$filetime = date('Y-m-d h:i:s',filemtime ($single) );
							$sumsize = $sumsize+$fsize;
							$single = convert_encoding($single,"UTF-8","GB2312");
							$single = str_replace($currentPath."/","",$single);
							echo "<tr><td>&nbsp;<input type=\"checkbox\" name=\"fname[]\" value=\"".$currentPath."/".$single."\">&nbsp;<img src=\"../images/icons/asp.gif\"><a href=\"".$currentPath."/".$single."\" target=\"_blank\"> ".$single."</a></td><td>".$fsize." KB</td><td>".$filetime."</td></tr>";
						}
					}
				
			}
		}
		echo "<tr><td colspan=\"3\">本目录下共有<font color=red><b>".$num."</b></font>个文件; 占用<font color=red><b>".round($sumsize/ 1024 )."</b></font><font color=#FF0000><b>K</b></font>空间</td></tr>";
		
	}
}

function picchkedit()
{
	$_SESSION["picchkpath"] = "";
	picchkpath("../upload/vod/");
}

function picchkpath($path)
{
	echo "正在收集图片目录信息请稍后...<br>";
	$arr = array();
	$num = 0;
	foreach( glob($path.'/*',GLOB_ONLYDIR) as $single){
		if(is_dir($single)){
			$num++;
			$arr[] = str_replace("//","/",$single);
		}
	}
	
	$_SESSION["picchkpath"] = $arr;
	echo "目录收集完毕，一共有". $num . "个目录等待检测...<br>下面进入检测图片...<script language=\"javascript\">setTimeout(\"jump();\",3000);function jump(){location.href='?action=picchkfile';}</script>";
}

function picchkfile()
{
	global $db;
	$d= $strPath;
	
	$arr = $_SESSION["picchkpath"];
	
	$num = be("get","num");
	$e = be("get","e");
	$s = be("get","s");
	if(!isNum($num)){ $num=0; }	else{ $num = intval($num); }
	if(!isNum($e)){ $e=0; }	else{ $e = intval($e); }
	if(!isNum($s)){ $s=0; }	else{ $s = intval($s); }
	
	if( $num > count($arr)-1 ){
		echo "无效图片检测完毕，一共有". $e . "个无效图片...<script language=\"javascript\">setTimeout(\"jump();\",3000);function jump(){location.href='?action=main';}</script>";
		exit;
	}
	
	$d = $arr[$num];
	
	if (is_dir($d)) {
		$fcount= sizeof(scandir($d)) -2 ;
		echo "<b>&nbsp;<font color='#ff0000'>".$d." >> 共&nbsp;" . $fcount . " 个文件,开始位置".($s+1).",已累计清理".$e."个无效图片 </font> <br>";
		
		$i=0;
		$endnum = $s + 29;
		$rc=false;
		
		if($fcount>0){
			foreach( glob($d.'/*') as $single){ 
				
		      		if($i>=$s){
						if($s>$endnum){
							$rc=true;
							echo "目录图片过多，暂停3秒后继续检测...<script language=\"javascript\">setTimeout(\"jump();\",3000);function jump(){location.href='?action=picchkfile&num=".$num."&s=". $s."&e=".$e."';}</script>";
							break;
						}
			      		
			      		$fsingle = $single;
			      		$single = convert_encoding($single,"UTF-8","GB2312");
			      		$fname = replaceStr($single,"../upload/","upload/");
			      		
						$sql="select count(*) from {pre}vod where d_pic='$fname'";
						$cc=$db->getOne($sql);
						if($cc==0){
							$e++;
							unlink($fsingle);
							echo "".$fname."<font color=red>无效</font><br/>";
						}
						else{
							echo "".$fname."<font color=green>有效</font><br/>";
						}
						$s++;
					}
					$i++;
					ob_flush();flush();
				
	   		}
		}
		
		if(!$rc){
			echo "该目录的无效图片检测完毕，暂停3秒后继续检测...<script language=\"javascript\">setTimeout(\"jump();\",3000);function jump(){location.href='?action=picchkfile&num=".($num+1)."&s=0&e=".$e."';}</script>";
		}
	}
}
?>