<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
$stype = be("get","type");
if (isN($stype)){ $stype= 0;} else { $stype = intval($stype);}

switch($action)
{
	case "cls" : cls();break;
	case "upto": update();break;
	case "uptodata": updatedata();break;
	case "uptofile": updatefile();break;
	case "uptoindex": updateindex();break;
	default : headAdmin ("缓存管理"); main();break;
}
dispseObj();

function update()
{
	$cachePath= root.'upload/cache';
	if ($handle = opendir($cachePath)){
	   while (false !== ($item = readdir($handle))){
		   if ($item != "." && $item != ".." ){
			   if (is_dir("$cachePath/$item")){
			   } 
			   else{
			   	  unlink("$cachePath/$item");
			   }
		   }
	   }
	   closedir( $handle );
	}
	unset($handle);
	echo "";
}

function updatedata()
{
	updateCacheFile();
	echo "";
}

function updatefile()
{
	$cachePath= root.'upload/cache';
	if ($handle = opendir($cachePath)){
		if (is_dir("$cachePath/app")){
			delFileUnderDir("$cachePath/app");
		}
		if (is_dir("$cachePath/vodlist")){
			delFileUnderDir("$cachePath/vodlist");
		}
		if (is_dir("$cachePath/artlist")){
			delFileUnderDir("$cachePath/artlist");
		}
		if (is_dir("$cachePath/search")){
			delFileUnderDir("$cachePath/search");
		}
		if (is_dir("$cachePath/client")){
			delFileUnderDir("$cachePath/client");
		}
	}
	closedir( $handle );
	unset($handle);
	echo "";
}
function updateindex()
{
	if(file_exists("../index.html")){
		unlink("../index.html");
	}
	if(file_exists("../index.htm")){
		unlink("../index.htm");
	}
	if(file_exists("../index.shtml")){
		unlink("../index.shtml");
	}
	echo "";
}

function delDirAndFile( $dirName )
{
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					delDirAndFile( "$dirName/$item" );
				} else {
					if( unlink( "$dirName/$item" ) )echo "成功删除文件： $dirName/$item<br />\n";
				}
	   		}
	   }
	   closedir( $handle );
	   if( rmdir( $dirName ) )echo "成功删除目录： $dirName<br />\n";
	}
	unset($handle);
}
function delFileUnderDir( $dirName )
{
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					delFileUnderDir( "$dirName/$item" );
				} else {
					if( unlink( "$dirName/$item" ) )echo "成功删除文件： $dirName/$item<br />\n";
				}
			}
   		}
	closedir( $handle );
	}
	unset($handle);
}

function getCacheType($a,$f)
{
	$res = "";
	if($a=="../upload/cache"){
		if(strpos($f,"pageselect")>0){
			$res="分页下拉框";
		}
		else if(strpos($f,"vodindex")>0){
			$res="视频首页模板";
		}
		else if(strpos($f,"vodlist")>0){
			$res="视频分类分页模板";
		}
		else if(strpos($f,"vodmap")>0){
			$res="视频地图页模板";
		}
		else if(strpos($f,"vodplay")>0){
			$res="视频播放器";
		}
		else if(strpos($f,"vodserver")>0){
			$res="视频服务器组";
		}
		else if(strpos($f,"voddown")>0){
			$res="视频下载器";
		}
		else if(strpos($f,"vod")>0){
			$res="视频内容页模板";
		}
		else if(strpos($f,"artindex")>0){
			$res="文章首页模板";
		}
		else if(strpos($f,"artlist")>0){
			$res="文章分类分页模板";
		}
		else if(strpos($f,"artmap")>0){
			$res="文章地图页模板";
		}
		else if(strpos($f,"art")>0){
			$res="文章内容页模板";
		}
		else{
			$res="其他";
		}
	}
	else{
		if(strpos($a,"app")>0){
			$res="全局页面";
		}
		else if(strpos($f,"artlist")>0){
			$res="文章列表页面";
		}
		else if(strpos($f,"arttopiclist")>0){
			$res="文章专题页面";
		}
		else if(strpos($f,"client")>0){
			$res="桌面客户端页面";
		}
		else if(strpos($f,"search")>0){
			$res="搜索页面";
		}
		else if(strpos($f,"vodlist")>0){
			$res="视频列表页面";
		}
		else if(strpos($f,"vodtopiclist")>0){
			$res="视频专题页面";
		}
	}
	return $res;
}

function main()
{
?>
<table class="tb">
	<tr>
	<td>缓存目录</td>
	<td>缓存名称</td>
	<td>缓存类型</td>
	<td>缓存大小</td>
	<td>缓存时间</td>
	</tr>
<?php
	$arr[] = "../upload/cache";
	foreach( glob('../upload/cache/*',GLOB_ONLYDIR) as $single){
		if(is_dir($single)){
			$num++;
			$arr[] = str_replace("//","/",$single);
		}
	}
	$num=0;
	for($i=0;$i<count($arr);$i++){
		$a = $arr[$i];
		$fcount= sizeof(scandir($a)) -2 ;
		if($fcount>0){
			foreach( glob($a.'/*') as $single){
				if(is_file($single)){
					$ftype = getCacheType($a,$single);
					$fsize = round( filesize( $single ) / 1024 ,2);
					$ftime = date('Y-m-d h:i:s',filemtime ($single) );
					$single = str_replace($a."/","",$single);
					$num++;
					echo "<tr><td>".$a."</td><td>".$single."</td><td>".$ftype."</td><td>".$fsize."KB</td><td>".$ftime."</td></tr>";
				}
			}
		}
	}
	echo "<tr><td><font color=red>共有".$num."个缓存文件</font></td><td colspan=4> </td></tr>";
?>
</table>
</body>
</html>
<?php
}
?>