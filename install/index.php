<?php
	require_once ("../inc/config.php");
	require_once ("../inc/class.php");
	require_once ("../inc/function.php");
	$action = be("all","action");
	$db;
	$rpath = str_replace("\\",'/',dirname(__FILE__));
	$rpath = str_replace("\\",'/',substr($rpath,0,-7));
	define("root",$rpath);
	
	switch($action)
	{
		case "ckdb": ckdb();break;
		case "a": show_header(); stepA(); show_footer();break;
		case "b": show_header(); stepB(); show_footer();break;
		case "c": show_header(); stepC(); show_footer();break;
		case "d": show_header(); stepD(); show_footer();break;
		default : show_header(); main(); show_footer();break;
	}
	dispseObj();
	
	function getcon($varName)
	{
		switch($res = get_cfg_var($varName))
		{
		case 0:
			return "NO";
			break;
		case 1:
			return "YES";
			break;
		default: 
			return $res;
			break;
		}
	}
	
	function ckdb()
	{
		$server=be("get","server");
		$dbname=be("get","db");
		$id=be("get","id");
		$pwd=be("get","pwd");
		$lnk=mysql_connect($server,$id,$pwd);
		if(!$lnk){
			die('servererror');
		}
		else{
			$rs = @mysql_select_db($dbname,$lnk);
			if(!$rs){
				$rs = @mysql_query(" CREATE DATABASE `$dbname`; ",$lnk);
				if(!$rs)
			    {
			    	die('dberror');
			    }
			}
		}
		@mysql_close($lnk);
		die("ok");
	}
	
	function show_header()
	{
		echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>苹果电影程序MacCMS 安装向导</title>
<link rel="stylesheet" href="../images/install/style.css" type="text/css" media="all" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/function.js"></script>
<script type="text/javascript">
	function showmessage(message) {
		document.getElementById('notice').innerHTML += message + '<br />';
	}
</script>
<meta content="Comsenz Inc." name="Copyright" />
</head>
<div class="container">
	<div class="header">
		<h1>苹果电影程序MacCMS 安装向导</h1>
		<span>苹果电影程序UTF8版</span>
EOT;
	}
	
	function show_footer()
	{
	echo <<<EOT
		<div class="footer">&copy;2008 - 2013 <a href="http://www.maccms.com/">苹果电影程序MacCMS</a> Inc.</div>
	</div>
</div>
</body>
</html>
EOT;
	}
	
	function show_step($n,$t,$c)
	{
	$laststep = 4;
	$stepclass = array();
	for($i = 1; $i <= $laststep; $i++) {
		$stepclass[$i] = $i == $n ? 'current' : ($i < $n ? '' : 'unactivated');
	}
	$stepclass[$laststep] .= ' last';
	echo <<<EOT
	<div class="setup step{$n}">
		<h2>$t</h2>
		<p>$c</p>
	</div>
	<div class="stepstat">
		<ul>
			<li class="$stepclass[1]">检查安装环境</li>
			<li class="$stepclass[2]">设置运行环境</li>
			<li class="$stepclass[3]">创建数据库</li>
			<li class="$stepclass[4]">安装</li>
		</ul>
		<div class="stepstatbg stepstat1"></div>
	</div>
</div>
<div class="main">
EOT;
	}
	
	function main()
	{
		echo <<<EOT
</div>
<div class="main" style="margin-top:-123px;">
	<div class="licenseblock">
	请您在使用(苹果MacCMS)前仔细阅读如下条款。包括免除或者限制作者责任的免责条款及对用户的权利限制。您的安装使用行为将视为对本《用户许可协议》的接受，并同意接受本《用户许可协议》各项条款的约束。 <br /><br />
				一、安装和使用： <br />苹果MacCMS是免费和开源提供给您使用的，您可安装无限制数量副本。 您必须保证在不进行非法活动，不违反国家相关政策法规的前提下使用本软件。 <br /><br />
二：郑重声明： <br />
1、任何个人或组织不得在未经授权的情况下删除、修改、拷贝本软件及其他副本上一切关于版权的信息。 <br />
2、苹果工作室保留此软件的法律追究权利。
<br /><br />
				三、免责声明：  <br />
				本软件并无附带任何形式的明示的或暗示的保证，包括任何关于本软件的适用性, 无侵犯知识产权或适合作某一特定用途的保证。  <br />
				在任何情况下，对于因使用本软件或无法使用本软件而导致的任何损害赔偿，作者均无须承担法律责任。作者不保证本软件所包含的资料,文字、图形、链接或其它事项的准确性或完整性。作者可随时更改本软件，无须另作通知。  <br />
				所有由用户自己制作、下载、使用的第三方信息数据和插件所引起的一切版权问题或纠纷，本软件概不承担任何责任。<br /><br />
	<strong>版权所有 (c) 2008-2013，苹果MacCMS,
	  保留所有权利</strong>。 
	</div>
	<div class="btnbox marginbot">
		<form method="get" autocomplete="off" action="index.php">
		<input type="hidden" name="action" value="a">
		<input type="submit" name="submit" value="我同意" style="padding: 2px">&nbsp;
		<input type="button" name="exit" value="我不同意" style="padding: 2px" onclick="javascript: window.close(); return false;">
		</form>
	</div>
EOT;
	}

function dir_writeable($dir) {
	$writeable = 0;
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.txt", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.txt");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}

function stepA()
{
	show_step(1,"开始安装","环境以及文件目录权限检查");
	$os = PHP_OS;
	$pv = PHP_VERSION;
	$up = getcon("upload_max_filesize");
	$cj1 = getcon("allow_url_fopen");
	
	echo <<<EOT
<div class="main"><h2 class="title">环境检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;">
<tr>
	<th>项目</th>
	<th class="padleft">所需配置</th>
	<th class="padleft">最佳配置</th>
	<th class="padleft">当前服务器</th>
</tr>
<tr>
<td>操作系统</td>
<td class="padleft">不限制</td>
<td class="padleft">类Unix</td>
<td class="w pdleft1">$os</td>
</tr>
<tr>
<td>PHP 版本</td>
<td class="padleft">4.4</td>
<td class="padleft">5.0</td>
<td class="w pdleft1">$pv</td>
</tr>
<tr>
<td>附件上传</td>
<td class="padleft">不限制</td>
<td class="padleft">2M</td>
<td class="w pdleft1">$up</td>
</tr>
<tr>
<td>远程访问</td>
<td class="padleft">allow_url_fopen</td>
<td class="padleft">开启</td>
<td class="w pdleft1">$cj1</td>
</tr>
</table>
<h2 class="title">目录、文件权限检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;width:90%;">
	<tr>
	<th>目录文件</th>
	<th class="padleft">所需状态</th>
	<th class="padleft">当前状态</th>
</tr>
EOT;
	$arr = array("inc/config.php","inc/config.ftp.php","inc/config.interface.php","inc/cache.php","inc/timmingset.xml","inc/voddown.xml","inc/vodplay.xml","inc/vodserver.xml","inc/vodarea.txt","inc/vodlang.txt","upload/","upload/art/","upload/arttopic/","upload/vod/","upload/vodtopic/","upload/cache/","upload/export/","upload/playdata/","upload/downdata/","js/player.js","js/playerconfig.js","admin/bak/","install/index.php");
	foreach($arr as $f){
		$st="可写";
		$cs="w";
		if(strpos($f,".")>0){
			if(!is_writable(root.$f)){
				$st="不可写";
				$cs="nw";
			}
		}
		else{
			if(!dir_writeable(root.$f)){
				$st="不可写";
				$cs="nw";
			}
		}
		echo '<tr><td>'.$f.'</td><td class="w pdleft1">可写</td><td class="'.$cs.' pdleft1">'.$st.'</td></tr>';
	}
	unset($arr);
	echo <<<EOT
</table>
<h2 class="title">函数依赖性检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;width:90%;">
<tr>
	<th>函数名称</th>
	<th class="padleft">所需状态</th>
	<th class="padleft">当前状态</th>
</tr>
EOT;
	
	$arr=array("mysql_connect","curl_init","curl_exec","mb_convert_encoding","dom_import_simplexml");
	foreach($arr as $f){
		$st="支持";
		$cs="w";
		if(!function_exists($f)){
			$st="不支持";
			$cs="nw";
		}
		echo '<tr><td>'.$f.'</td><td class="w pdleft1">支持</td><td class="'.$cs.' pdleft1">'.$st.'</td></tr>';
	}
	unset($arr);
	
	echo <<<EOT
</table>
</div>
<form method="get" autocomplete="off" action="index.php">
<input type="hidden" name="action" value="b" /><div class="btnbox marginbot"><input type="button" onclick="history.back();" value="上一步"><input type="submit" value="下一步">
</div>
</form>
EOT;
}

function stepB()
{
	show_step(2,"安装配置","网站默认配置信息");
	
	$strpath = $_SERVER["SCRIPT_NAME"];
	$strpath = substring($strpath, strripos($strpath, "/"));
	$strpath = replaceStr($strpath,"install/","");
	$strpath = replaceStr($strpath,"/install","/");
	
?>
<script language="javascript">
		$(function(){
			$("#btnLicense").click(function(){
				window.location.href= "?action=a";
				return false;
			});
			$("#btnStep1a").click(function(){
				location.href= "?action=";
			});
			$("#btnStep1b").click(function(){
				if($("#app_siteurl").val()==""){
					alert("网站域名不能为空");
					$("#app_siteurl").focus();
					return;
				}
				if($("#app_installdir").val()==""){
					alert("网站安装目录不能为空");
					$("#app_installdir").focus();
					return;
				}
				if($("#app_sitename").val()==""){
					alert("网站名称不能为空");
					$("#app_sitename").focus();
					return;
				}
				if($("#m_name").val()==""){
					alert("帐号不能为空");
					$("#m_name").focus();
					return;
				}
				if($("#m_password1").val()==""){
					alert("密码不能为空");
					$("#m_password1").focus();
					return;
				}
				if($("#app_safecode").val()==""){
					alert("安全码不能为空");
					$("#app_safecode").focus();
					return;
				}
				if($("#m_password1").val() != $("#m_password2").val()){
					alert("验证密码不同");
					$("#m_password2").focus();
					return;
				}
				$("#form2").submit();
			});
			$("#btnStep2a").click(function(){
				location.href= "?action=a";
			});
			$("#btnStep2b").click(function(){
				location.href= "?action=c";
			});
		});
		function setdb(dbtype){
			if (dbtype == "access"){
				$("#sql").css("display","none");
				$("#acc").css("display","");
			}
			else{
				$("#sql").css("display","");
				$("#acc").css("display","none");
			}
		}

function checkdb(){
    	var server=$("#app_dbserver").val();
		var dbname=$("#app_dbname").val();
		var id=$("#app_dbuser").val();
		var pwd=$("#app_dbpass").val();
		if(server=="" || dbname=="" || id=="" || pwd==""){
			alert("数据库信息不能为空");return;
		}
    	$.ajax({cache: false, dataType: 'html', type: 'GET', url: 'index.php?action=ckdb&server='+server+'&db='+dbname+'&id='+id+'&pwd='+pwd,
    		success: function(obj) {
				if(obj=='ok'){
					$("#checkinfo").html( "<font color=green>&nbsp;&nbsp;连接数据库服务器成功!</font>" );
				}
				else if(obj=='dberror'){
					$("#checkinfo").html ("<font color=red>&nbsp;&nbsp;连接数据库服务器成功，但是找不到该数据库，也没有权限创建该数据库!</font>");
				}
				else {
					$("#checkinfo").html("<font color=red>&nbsp;&nbsp;连接数据库服务器失败!</font>");
				}
			},
			complete: function (XMLHttpRequest, textStatus) {
				if( XMLHttpRequest.responseText.length >10){
					$("#checkinfo").html("<font color=red>&nbsp;&nbsp;连接服务器失败!</font>");
				}
			}
		});
    }
</script>

<div class="main"><form action="index.php?action=b" method="post">
<div id="form_items_3" ><br /><div class="desc"><b>填写网站配置信息</b></div>
	<table class="tb2">
	<tr><th class="tbopt" align="left">&nbsp;网站域名:</th>
	<td><input class="txt" type="text" name="app_siteurl" id="app_siteurl" value="<?php echo $_SERVER["SERVER_NAME"]?>" /></td>
	<td>网站的名称</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;安装路径:</th>
	<td><input class="txt" type="text" name="app_installdir" id="app_installdir" value="<?php echo $strpath?>" /></td>
	<td>根目录/，/二级目录/</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;网站关键字:</th>
	<td><input class="txt" type="text" name="app_keywords" id="app_keywords" value="免费在线电影" /></td>
	<td>网站的关键字利于seo优化</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;网站描述:</th>
	<td><input class="txt" type="text" name="app_description" id="app_description" value="提供最新最快的影视资讯和在线播放" /></td>
	<td>网站的描述信息利于seo优化</td>
	</tr>
	</table>
	<div class="desc"><b>填写数据库信息</b></div>
	<table class="tb2">
	<tr><th class="tbopt" align="left">&nbsp;数据库类型:</th>
	<td><select name="app_dbtype" id="app_dbtype" onChange="setdb(this.value);"><option value="mysql">mysql数据库</option></select></td>
	<td>网站使用数据库的类型</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;表前缀:</th>
	<td><input class="txt" type="text" name="app_tablepre" id="app_tablepre" value="mac_" /></td>
	<td>数据库表名前缀</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库服务器:</th>
	<td><input class="txt" type="text" name="app_dbserver" id="app_dbserver" value="localhost" /></td>
	<td>数据库服务器地址, 一般为 localhost</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库名称:</th>
	<td><input class="txt" type="text" name="app_dbname" id="app_dbname" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库用户名:</th>
	<td><input class="txt" type="text" name="app_dbuser" id="app_dbuser" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库密码:</th>
	<td><input class="txt" type="text" name="app_dbpass" id="app_dbpass" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;测试连接数据库:</th>
	<td><strong><a onclick="checkdb()" style="cursor:pointer;"><font color="red">>>>MYSQL连接测试</font></a></strong></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;</th>
	<td><span id="checkinfo"></span></td>
	<td></td>
	</tr>
	</table> 
	<div class="desc"><b>填写管理员信息</b></div>
	<table class="tb2">
	<tr><th class="tbopt" align="left">&nbsp; 管理员账号:</th>
	<td><input class="txt" type="text" name="m_name" id="m_name" value="admin" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp; 管理员密码:</th>
	<td><input class="txt" type="password" name="m_password1" id="m_password1" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp; 确认密码:</th>
	<td><input class="txt" type="password" name="m_password2" id="m_password2" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp; 安全码:</th>
	<td><input class="txt" type="password" name="app_safecode" id="app_safecode" value="" /></td>
	<td></td>
	</tr>
	</table>
	</div>
	<table class="tb2"><tr><th class="tbopt" align="left">&nbsp;</th><td>
<input type="hidden" name="action" value="c" /><div class="btnbox marginbot"><input type="button" onclick="history.back();" value="上一步"><input type="submit" value="下一步"></td><td></td></tr>
</table>
</form>
<?php
}

function checkField($sIndexName,$tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query("SHOW COLUMNS FROM ".$tableName);
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row["Field"];
	}
	if(in_array($sIndexName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function isExistTable($tableName,$dbname)
{
	global $db;
	$dbarr = array();
	$rs = $db->query("SHOW TABLES ");
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row["Tables_in_".dbname];
	}
	if(in_array($tableName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function stepC()
{
	global $db;
	$app_siteurl = be("post","app_siteurl");
	$app_sitename = be("post","app_sitename");
	$app_installdir = be("post","app_installdir");
	$app_keywords = be("post","app_keywords");
	$app_description = be("post","app_description");
	$app_dbtype = be("post","app_dbtype");
	$app_dbpath = "inc/" & be("post","app_dbpath");
	$app_dbserver = be("post","app_dbserver");
	$app_dbname = be("post","app_dbname");
	$app_dbuser = be("post","app_dbuser");
	$app_dbpass = be("post","app_dbpass");
	$app_tablepre = be("post","app_tablepre");
	$m_name = be("post","m_name");
	$m_password1 = be("post","m_password1");
	$m_password2 = be("post","m_password2");
	$app_safecode = be("post","app_safecode");
	
	show_step(3,"安装数据库","正在执行数据库安装写入配置文件");
	
echo <<<EOT
	<div class="main"> 
	<div class="btnbox"><div id="notice"></div></div>
	<div class="btnbox margintop marginbot"><form method="get" autocomplete="off" action="index.php">
	<table class="tb2"><tr><th class="tbopt" align="left">&nbsp;</th><td>
<input type="hidden" name="action" value="d" /><div class="btnbox marginbot"><input type="button" onclick="history.back();" value="上一步"><input type="submit" value="下一步"></td><td></td></tr></table></form></div>
EOT;

	$configstr = file_get_contents( "../inc/config.php" );
	$configstr = regReplace($configstr,"\"app_siteurl\",\"(\S*?)\"","\"app_siteurl\",\"".$app_siteurl."\"");
	$configstr = regReplace($configstr,"\"app_installdir\",\"(\S*?)\"","\"app_installdir\",\"".$app_installdir."\"");
	$configstr = regReplace($configstr,"\"app_keywords\",\"(\S*?)\"","\"app_keywords\",\"".$app_keywords."\"");
	$configstr = regReplace($configstr,"\"app_description\",\"(\S*?)\"","\"app_description\",\"".$app_description."\"");
	$configstr = regReplace($configstr,"\"app_dbtype\",\"(\S*?)\"","\"app_dbtype\",\"".$app_dbtype."\"");
	$configstr = regReplace($configstr,"\"app_dbpath\",\"(\S*?)\"","\"app_dbpath\",\"".$app_dbpath."\"");
	$configstr = regReplace($configstr,"\"app_dbserver\",\"(\S*?)\"","\"app_dbserver\",\"".$app_dbserver."\"");
	$configstr = regReplace($configstr,"\"app_dbname\",\"(\S*?)\"","\"app_dbname\",\"".$app_dbname."\"");
	$configstr = regReplace($configstr,"\"app_dbuser\",\"(\S*?)\"","\"app_dbuser\",\"".$app_dbuser."\"");
	$configstr = regReplace($configstr,"\"app_dbpass\",\"(\S*?)\"","\"app_dbpass\",\"".$app_dbpass."\"");
	$configstr = regReplace($configstr,"\"app_safecode\",\"(\S*?)\"","\"app_safecode\",\"".$app_safecode."\"");
	$configstr = regReplace($configstr,"\"app_tablepre\",\"(\S*?)\"","\"app_tablepre\",\"".$app_tablepre."\"");
	$configstr = regReplace($configstr,"\"app_install\",(\S*?)\)\;","\"app_install\",1);");
	fwrite(fopen("../inc/config.php","wb"),$configstr);
	echo '<script type="text/javascript">showmessage(\'写入网站配置文件... 成功  \');</script>';
	
	error_reporting(E_NOTICE );
	$dbck=false;
	
		$lnk=@mysql_connect($app_dbserver,$app_dbuser,$app_dbpass);
		if(!$lnk){
			echo '<script type="text/javascript">showmessage(\'数据库设置出错：mysql请检查数据库连接信息... \');</script>';
		}
		else{
			if(!@mysql_select_db($app_dbname,$lnk)){
				echo '<script type="text/javascript">showmessage(\'数据库服务器连接成功，没有找到【 '.$app_dbname.' 】数据... \');</script>';
			}
			else{
				$dbck=true;
			} 
		}
	error_reporting(7 );
	
	if ($dbck){
	
	$db = new AppDataBase($app_dbserver,$app_dbuser,$app_dbpass,$app_dbname);
	
	echo '<script type="text/javascript">showmessage(\'开始创建数据库结构... \');</script>';
	
	if(!isExistTable("".$app_tablepre."art",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."art` (  `a_id` int(11) NOT NULL AUTO_INCREMENT,  `a_title` varchar(255) DEFAULT NULL,  `a_subtitle` varchar(255) DEFAULT NULL,  `a_entitle` varchar(255) DEFAULT NULL, `a_letter` char(1) DEFAULT NULL, `a_from` varchar(64) DEFAULT NULL,  `a_color` varchar(8) DEFAULT NULL,  `a_type` int(11) DEFAULT '0',  `a_topic` int(11) DEFAULT '0',  `a_level` int(11) DEFAULT '0',  `a_pic` varchar(255) DEFAULT NULL,  `a_author` varchar(255) DEFAULT NULL,  `a_content` text,  `a_hits` int(11) DEFAULT '0',  `a_dayhits` int(11) DEFAULT '0',  `a_weekhits` int(11) DEFAULT '0',  `a_monthhits` int(11) DEFAULT '0',  `a_hide` int(11) DEFAULT '0',  `a_addtime` datetime DEFAULT NULL,  `a_time` datetime DEFAULT NULL,  `a_hitstime` datetime DEFAULT NULL, `a_maketime` datetime DEFAULT NULL,  PRIMARY KEY (`a_id`),  KEY `a_type` (`a_type`),  KEY `a_topic` (`a_topic`),  KEY `a_level` (`a_level`),  KEY `a_hits` (`a_hits`), KEY `a_dayhits` (`a_dayhits`), KEY `a_weekhits` (`a_weekhits`), KEY `a_monthhits` (`a_monthhits`), KEY `a_addtime` (`a_addtime`), KEY `a_time` (`a_time`), KEY `a_maketime` (`a_maketime`), KEY `a_hide` (`a_hide`), KEY `a_letter` (`a_letter`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	
	}
	
	if(!isExistTable("".$app_tablepre."art_topic",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."art_topic` (  `t_id` int(11) NOT NULL AUTO_INCREMENT,  `t_name` varchar(64) DEFAULT NULL,  `t_enname` varchar(128) DEFAULT NULL,  `t_sort` int(11) DEFAULT '0',  `t_template` varchar(128) DEFAULT NULL,  `t_pic` varchar(255) DEFAULT NULL,  `t_des` varchar(255) DEFAULT NULL,  PRIMARY KEY (`t_id`),  KEY `t_sort` (`t_sort`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'art_topic... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."art_type",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."art_type` (  `t_id` int(11) NOT NULL AUTO_INCREMENT,  `t_name` varchar(64) DEFAULT NULL,  `t_enname` varchar(128) DEFAULT NULL,  `t_sort` int(11) DEFAULT '0',  `t_pid` int(11) DEFAULT '0',  `t_key` varchar(255) DEFAULT NULL,  `t_des` varchar(255) DEFAULT NULL,  `t_template` varchar(64) DEFAULT NULL,  `t_arttemplate` varchar(64) DEFAULT NULL,  `t_hide` int(11) DEFAULT '0',  `t_union` text,  PRIMARY KEY (`t_id`),  KEY `t_pid` (`t_pid`),  KEY `t_sort` (`t_sort`),  KEY `t_hide` (`t_hide`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'art_type... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."comment",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."comment` (  `c_id` int(11) NOT NULL AUTO_INCREMENT,  `c_type` int(11) DEFAULT '0',  `c_vid` int(11) DEFAULT '0',  `c_rid` int(11) DEFAULT '0',  `c_audit` int(11) DEFAULT '0',  `c_name` varchar(64) DEFAULT NULL,  `c_ip` varchar(32) DEFAULT NULL,  `c_content` varchar(128) DEFAULT NULL,  `c_time` datetime DEFAULT NULL,  PRIMARY KEY (`c_id`),  KEY `c_vid` (`c_vid`),  KEY `c_type` (`c_type`),  KEY `c_rid` (`c_rid`), KEY `c_time` (`c_time`),  KEY `c_audit` (`c_audit`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'comment... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."gbook",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."gbook` (  `g_id` int(11) NOT NULL AUTO_INCREMENT,  `g_vid` int(11) DEFAULT '0',  `g_audit` int(11) DEFAULT '0',  `g_name` varchar(64) DEFAULT NULL,  `g_content` varchar(255) DEFAULT NULL,  `g_reply` varchar(255) DEFAULT NULL,  `g_ip` varchar(32) DEFAULT NULL,  `g_time` datetime DEFAULT NULL,  `g_replytime` datetime DEFAULT NULL,  PRIMARY KEY (`g_id`),  KEY `g_vid` (`g_vid`), KEY `g_time` (`g_time`),  KEY `g_audit` (`g_audit`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'gbook... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."link",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."link` (  `l_id` int(11) NOT NULL AUTO_INCREMENT,  `l_name` varchar(64) DEFAULT NULL,  `l_type` varchar(8) DEFAULT NULL,  `l_url` varchar(255) DEFAULT NULL,  `l_sort` int(11) DEFAULT '0',  `l_logo` varchar(255) DEFAULT NULL,  PRIMARY KEY (`l_id`),  KEY `l_sort` (`l_sort`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'link... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."manager",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."manager` (  `m_id` int(11) NOT NULL AUTO_INCREMENT,  `m_name` varchar(32) DEFAULT NULL,  `m_password` varchar(32) DEFAULT NULL,  `m_levels` text,  `m_status` int(11) DEFAULT '0',  `m_logintime` datetime DEFAULT NULL,  `m_loginip` varchar(32) DEFAULT NULL,  `m_random` varchar(64) DEFAULT NULL,  PRIMARY KEY (`m_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'manager... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."mood",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."mood` (  `m_id` int(11) NOT NULL AUTO_INCREMENT,  `m_type` int(11) DEFAULT '0',  `m_vid` int(11) DEFAULT '0',  `mood1` int(11) DEFAULT '0',  `mood2` int(11) DEFAULT '0',  `mood3` int(11) DEFAULT '0',  `mood4` int(11) DEFAULT '0',  `mood5` int(11) DEFAULT '0',  `mood6` int(11) DEFAULT '0',  `mood7` int(11) DEFAULT '0',  `mood8` int(11) DEFAULT '0',  `mood9` int(11) DEFAULT '0',  PRIMARY KEY (`m_id`),  KEY `m_type` (`m_type`),  KEY `m_vid` (`m_vid`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mood... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."user",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."user` (  `u_id` int(11) NOT NULL AUTO_INCREMENT, `u_qid` varchar(32) DEFAULT NULL, `u_name` varchar(32) DEFAULT NULL,  `u_group` int(11) DEFAULT '0',  `u_password` varchar(32) DEFAULT NULL,  `u_qq` varchar(16) DEFAULT NULL,  `u_email` varchar(32) DEFAULT NULL,  `u_phone` varchar(16) DEFAULT NULL,  `u_status` int(11) DEFAULT '0',  `u_question` varchar(255) DEFAULT NULL,  `u_answer` varchar(255) DEFAULT NULL,  `u_points` int(11) DEFAULT '0',  `u_regtime` datetime DEFAULT NULL,  `u_logintime` datetime DEFAULT NULL,  `u_loginnum` int(11) DEFAULT '0',  `u_tj` int(11) DEFAULT '0',  `u_ip` varchar(32) DEFAULT NULL,  `u_random` varchar(64) DEFAULT NULL,  `u_fav` text,  `u_plays` text,  `u_downs` text,  `u_flag` int(11) DEFAULT '0',  `u_start` varchar(64) DEFAULT NULL,  `u_end` varchar(64) DEFAULT NULL,  PRIMARY KEY (`u_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'user... \');</script>';
	}
	
	
	if(!isExistTable("".$app_tablepre."user_card",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."user_card` (  `c_id` int(11) NOT NULL AUTO_INCREMENT,  `c_number` varchar(32) DEFAULT NULL,  `c_pass` varchar(32) DEFAULT NULL,  `c_money` int(11) DEFAULT '0',  `c_point` int(11) DEFAULT '0',  `c_used` int(11) DEFAULT '0',  `c_sale` int(11) DEFAULT '0',  `c_user` int(11) DEFAULT '0',  `c_addtime` datetime DEFAULT NULL,  `c_usetime` datetime DEFAULT NULL,  PRIMARY KEY (`c_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'user_card... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."user_group",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."user_group` (  `ug_id` int(11) NOT NULL AUTO_INCREMENT,  `ug_name` varchar(32) DEFAULT NULL,  `ug_type` varchar(255) DEFAULT NULL,  `ug_popedom` varchar(32) DEFAULT NULL,  `ug_upgrade` int(11) DEFAULT '0',  `ug_popvalue` int(11) DEFAULT '0',  PRIMARY KEY (`ug_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'user_group... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."user_visit",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."user_visit` (  `uv_id` int(11) NOT NULL AUTO_INCREMENT,  `uv_uid` int(11) DEFAULT '0',  `uv_ip` varchar(32) DEFAULT NULL,  `uv_ly` varchar(128) DEFAULT NULL,  `uv_time` datetime DEFAULT NULL,  PRIMARY KEY (`uv_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'user_visit... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."vod",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."vod` (  `d_id` int(11) NOT NULL AUTO_INCREMENT,  `d_name` varchar(255) DEFAULT NULL,  `d_subname` varchar(255) DEFAULT NULL,  `d_enname` varchar(255) DEFAULT NULL,  `d_type` int(11) DEFAULT '0', `d_letter` char(1) DEFAULT NULL,  `d_state` int(11) DEFAULT '0',  `d_color` varchar(8) DEFAULT NULL,  `d_pic` varchar(255) DEFAULT NULL,  `d_picthumb` varchar(255) DEFAULT NULL,  `d_picslide` varchar(255) DEFAULT NULL,  `d_starring` varchar(255) DEFAULT NULL,  `d_directed` varchar(255) DEFAULT NULL,  `d_area` varchar(32) DEFAULT NULL,  `d_year` varchar(32) DEFAULT NULL,  `d_language` varchar(32) DEFAULT NULL,  `d_level` int(11) DEFAULT '0',  `d_stint` int(11) DEFAULT '0',  `d_stintdown` int(11) DEFAULT '0',  `d_hits` int(11) DEFAULT '0',  `d_dayhits` int(11) DEFAULT '0',  `d_weekhits` int(11) DEFAULT '0',  `d_monthhits` int(11) DEFAULT '0',  `d_topic` int(11) DEFAULT '0',  `d_duration` int(11) DEFAULT '0',  `d_content` text,  `d_remarks` varchar(255) DEFAULT NULL,  `d_hide` int(11) DEFAULT '0',  `d_good` int(11) DEFAULT '0',  `d_bad` int(11) DEFAULT '0',  `d_usergroup` int(11) DEFAULT '0',  `d_score` int(11) DEFAULT '0',  `d_scorecount` int(11) DEFAULT '0',  `d_addtime` datetime DEFAULT NULL,  `d_time` datetime DEFAULT NULL,  `d_hitstime` datetime DEFAULT NULL,  `d_maketime` datetime DEFAULT NULL,  `d_playfrom` varchar(255) DEFAULT NULL,  `d_playserver` varchar(255) DEFAULT NULL,  `d_playurl` longtext,  `d_downfrom` varchar(255) DEFAULT NULL,  `d_downserver` varchar(255) DEFAULT NULL,  `d_downurl` longtext,  PRIMARY KEY (`d_id`),  KEY `d_type` (`d_type`),  KEY `d_state` (`d_state`),  KEY `d_level` (`d_level`),  KEY `d_hits` (`d_hits`),  KEY `d_dayhits` (`d_dayhits`), KEY `d_weekhits` (`d_weekhits`), KEY `d_monthhits` (`d_monthhits`),  KEY `d_stint` (`d_stint`),  KEY `d_stintdown` (`d_stintdown`),  KEY `d_hide` (`d_hide`),  KEY `d_usergroup` (`d_usergroup`),  KEY `d_score` (`d_score`), KEY `d_addtime` (`d_addtime`), KEY `d_time` (`d_time`), KEY `d_maketime` (`d_maketime`), KEY `d_topic` (`d_topic`), KEY `d_letter` (`d_letter`), KEY `d_name` (`d_name`), KEY `d_enname` (`d_enname`), KEY `d_year` (`d_year`), KEY `d_area` (`d_area`), KEY `d_language` (`d_language`), KEY `d_starring` (`d_starring`), KEY `d_directed` (`d_directed`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'vod... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."vod_topic",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."vod_topic` (  `t_id` int(11) NOT NULL AUTO_INCREMENT,  `t_name` varchar(64) DEFAULT NULL,  `t_enname` varchar(128) DEFAULT NULL,  `t_sort` int(11) DEFAULT '0',  `t_template` varchar(128) DEFAULT NULL,  `t_pic` varchar(255) DEFAULT NULL,  `t_des` varchar(255) DEFAULT NULL,  PRIMARY KEY (`t_id`),  KEY `t_sort` (`t_sort`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'vod_topic... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."vod_type",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."vod_type` (  `t_id` int(11) NOT NULL AUTO_INCREMENT,  `t_name` varchar(64) DEFAULT NULL,  `t_enname` varchar(128) DEFAULT NULL,  `t_sort` int(11) NOT NULL,  `t_pid` int(11) DEFAULT '0',  `t_key` varchar(255) DEFAULT NULL,  `t_des` varchar(255) DEFAULT NULL,  `t_template` varchar(64) DEFAULT NULL,  `t_vodtemplate` varchar(64) DEFAULT NULL,  `t_playtemplate` varchar(64) DEFAULT NULL,  `t_downtemplate` varchar(64) DEFAULT NULL,  `t_hide` int(11) DEFAULT '0',  `t_union` text,  PRIMARY KEY (`t_id`),  KEY `t_sort` (`t_sort`),  KEY `t_pid` (`t_pid`),  KEY `t_hide` (`t_hide`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'vod_type... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."cj_art",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."cj_art` (  `m_id` int(11) NOT NULL AUTO_INCREMENT,  `m_pid` int(11) NOT NULL DEFAULT '0',  `m_title` varchar(255) DEFAULT NULL,  `m_type` varchar(128) DEFAULT NULL,  `m_typeid` int(11) NOT NULL DEFAULT '0',  `m_author` varchar(255) DEFAULT NULL,  `m_content` text,  `m_addtime` varchar(64) DEFAULT NULL,  `m_urltest` varchar(255) DEFAULT NULL,  `m_zt` int(11) NOT NULL DEFAULT '0',  `m_hits` int(11) NOT NULL DEFAULT '0',  PRIMARY KEY (`m_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'cj_art... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."cj_art_projects",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."cj_art_projects` (  `p_id` int(11) NOT NULL AUTO_INCREMENT,  `p_name` varchar(128) DEFAULT NULL,  `p_coding` varchar(64) DEFAULT NULL,  `p_pagetype` int(11) NOT NULL DEFAULT '0',  `p_url` varchar(255) DEFAULT NULL,  `p_pagebatchurl` varchar(255) DEFAULT NULL,  `p_manualurl` varchar(255) DEFAULT NULL,  `p_pagebatchid1` varchar(128) DEFAULT NULL,  `p_pagebatchid2` varchar(128) DEFAULT NULL,  `p_script` int(11) NOT NULL DEFAULT '0',  `p_showtype` int(11) NOT NULL DEFAULT '0',  `p_collecorder` int(11) NOT NULL DEFAULT '0',  `p_savefiles` int(11) NOT NULL DEFAULT '0',  `p_intolib` int(11) NOT NULL DEFAULT '0',  `p_ontime` int(11) NOT NULL DEFAULT '0',  `p_listcodestart` text,  `p_listcodeend` text,  `p_classtype` int(11) NOT NULL DEFAULT '0',  `p_collect_type` int(11) NOT NULL DEFAULT '0',  `p_time` datetime DEFAULT NULL,  `p_listlinkstart` text,  `p_listlinkend` text,  `p_authortype` int(11) NOT NULL DEFAULT '0',  `p_authorstart` text,  `p_authorend` text,  `p_titletype` int(11) NOT NULL DEFAULT '0',  `p_titlestart` text,  `p_titleend` text,  `p_timestart` text,  `p_timeend` text,  `p_typestart` text,  `p_typeend` text,  `p_contentstart` text,  `p_contentend` text,  `p_hitsstart` int(11) NOT NULL DEFAULT '0',  `p_hitsend` int(11) NOT NULL DEFAULT '0',  `p_cpagetype` int(11) NOT NULL DEFAULT '0',  `p_cpagecodestart` text,  `p_cpagecodeend` text,  `p_cpagestart` text,  `p_cpageend` text,  PRIMARY KEY (`p_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'cj_art_projects... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."cj_change",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."cj_change` (  `c_id` int(11) NOT NULL AUTO_INCREMENT,  `c_name` varchar(64) DEFAULT NULL,  `c_toid` int(11) NOT NULL DEFAULT '0',  `c_pid` int(11) NOT NULL DEFAULT '0',  `c_type` int(4) NOT NULL DEFAULT '0',  `c_sys` int(11) NOT NULL DEFAULT '0',  PRIMARY KEY (`c_id`),  KEY `i_c_projectid` (`c_pid`),  KEY `i_c_type` (`c_type`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'cj_change... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."cj_filters",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."cj_filters` (  `f_id` int(11) NOT NULL AUTO_INCREMENT,  `f_name` varchar(64) DEFAULT NULL,  `f_object` int(11) NOT NULL DEFAULT '0',  `f_type` int(11) NOT NULL DEFAULT '0',  `f_content` varchar(64) DEFAULT NULL,  `f_strstart` text,  `f_strend` text,  `f_rep` varchar(255) DEFAULT NULL,  `f_flag` int(11) NOT NULL DEFAULT '0',  `f_pid` int(11) NOT NULL DEFAULT '0',  `f_sys` int(11) NOT NULL DEFAULT '0',  PRIMARY KEY (`f_id`),  KEY `i_f_type` (`f_type`),  KEY `i_f_object` (`f_object`),  KEY `i_f_projectid` (`f_pid`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'cj_filters... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."cj_vod",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."cj_vod` (  `m_id` int(11) NOT NULL AUTO_INCREMENT,  `m_pid` int(11) NOT NULL DEFAULT '0',  `m_name` varchar(255) DEFAULT NULL,  `m_type` varchar(64) DEFAULT NULL,  `m_typeid` int(11) NOT NULL DEFAULT '0',  `m_area` varchar(64) DEFAULT NULL,  `m_playfrom` varchar(64) DEFAULT NULL,  `m_starring` varchar(255) DEFAULT NULL,  `m_directed` varchar(255) DEFAULT NULL,  `m_pic` varchar(255) DEFAULT NULL,  `m_content` text,  `m_year` varchar(64) DEFAULT NULL,  `m_addtime` varchar(64) DEFAULT NULL,  `m_urltest` varchar(255) DEFAULT NULL,  `m_zt` int(11) NOT NULL DEFAULT '0',  `m_playserver` int(11) NOT NULL DEFAULT '0',  `m_hits` int(11) NOT NULL DEFAULT '0',  `m_state` int(11) NOT NULL DEFAULT '0',  `m_language` varchar(64) DEFAULT NULL,  `m_remarks` varchar(255) DEFAULT NULL,  PRIMARY KEY (`m_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'cj_vod... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."cj_vod_projects",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."cj_vod_projects` (  `p_id` int(11) NOT NULL AUTO_INCREMENT,  `p_name` varchar(128) DEFAULT NULL,  `p_coding` varchar(64) DEFAULT NULL,  `p_playtype` varchar(11) DEFAULT NULL,  `p_pagetype` int(11) NOT NULL DEFAULT '0',  `p_url` varchar(255) DEFAULT NULL,  `p_pagebatchurl` varchar(255) DEFAULT NULL,  `p_manualurl` varchar(255) DEFAULT NULL,  `p_pagebatchid1` varchar(128) DEFAULT NULL,  `p_pagebatchid2` varchar(128) DEFAULT NULL,  `p_script` int(11) NOT NULL DEFAULT '0',  `p_showtype` int(11) NOT NULL DEFAULT '0',  `p_collecorder` int(11) NOT NULL DEFAULT '0',  `p_savefiles` int(11) NOT NULL DEFAULT '0',  `p_intolib` int(11) NOT NULL DEFAULT '0',  `p_ontime` int(11) NOT NULL DEFAULT '0',  `p_listcodestart` text,  `p_listcodeend` text,  `p_classtype` int(11) NOT NULL DEFAULT '0',  `p_collect_type` int(11) NOT NULL DEFAULT '0',  `p_time` datetime DEFAULT NULL,  `p_listlinkstart` text,  `p_listlinkend` text,  `p_starringtype` int(11) NOT NULL DEFAULT '0',  `p_starringstart` text,  `p_starringend` text,  `p_titletype` int(11) NOT NULL DEFAULT '0',  `p_titlestart` text,  `p_titleend` text,  `p_pictype` int(11) NOT NULL DEFAULT '0',  `p_picstart` text,  `p_picend` text,  `p_timestart` text,  `p_timeend` text,  `p_areastart` text,  `p_areaend` text,  `p_typestart` text,  `p_typeend` text,  `p_contentstart` text,  `p_contentend` text,  `p_playcodetype` int(11) NOT NULL DEFAULT '0',  `p_playcodestart` text,  `p_playcodeend` text,  `p_playurlstart` text,  `p_playurlend` text,  `p_playlinktype` int(11) NOT NULL DEFAULT '0',  `p_playlinkstart` text,  `p_playlinkend` text,  `p_playspecialtype` int(11) NOT NULL DEFAULT '0',  `p_playspecialrrul` text,  `p_playspecialrerul` text,  `p_server` varchar(128) DEFAULT NULL,  `p_hitsstart` int(11) NOT NULL DEFAULT '0',  `p_hitsend` int(11) NOT NULL DEFAULT '0',  `p_lzstart` text,  `p_lzend` text,  `p_colleclinkorder` int(11) NOT NULL DEFAULT '0',  `p_lzcodetype` int(11) NOT NULL DEFAULT '0',  `p_lzcodestart` text,  `p_lzcodeend` text,  `p_languagestart` text,  `p_languageend` text,  `p_remarksstart` text,  `p_remarksend` text,  `p_directedstart` text,  `p_directedend` text,  `p_setnametype` int(11) NOT NULL DEFAULT '0',  `p_setnamestart` text,  `p_setnameend` text,  PRIMARY KEY (`p_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'cj_vod_projects... \');</script>';
	}
	
	if(!isExistTable("".$app_tablepre."cj_vod_url",$app_dbname)){
	$db->query( "CREATE TABLE `".$app_tablepre."cj_vod_url` (  `u_id` int(11) NOT NULL AUTO_INCREMENT,  `u_url` varchar(255) DEFAULT NULL,  `u_weburl` varchar(255) DEFAULT NULL,  `u_movieid` int(11) NOT NULL DEFAULT '0',  PRIMARY KEY (`u_id`),KEY `i_u_movieid` (`u_movieid`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'cj_vod_url... \');</script>';
	}
	
	echo '<script type="text/javascript">showmessage(\'数据库结构创建完成... \');</script>';
	
	
	$db->query( "insert into ".$app_tablepre."manager(m_id,m_name,m_password,m_status,m_levels) values('1','".$m_name."','".md5($m_password1)."',1,'2, 3, 4, 5, 6, 7, 8')");
	echo '<script type="text/javascript">showmessage(\'管理员帐号'.$m_name.'初始化成功... \');</script>';
	
	$db->query( "INSERT into ".$app_tablepre."user_group (ug_id,ug_name,ug_type,ug_popedom,ug_upgrade,ug_popvalue) values ('1','普通会员','','',0,1)");
	echo '<script type="text/javascript">showmessage(\'默认会员组初始化完毕... \');</script>';
	
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('1','电影','dianying',1,0,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('2','连续剧','lianxuju',2,0,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('3','综艺','zongyi',3,0,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('4','动漫','dongman',4,0,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('5','动作片','dongzuopian',11,1,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('6','喜剧片','xijupian',12,1,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('7','爱情片','aiqingpian',13,1,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('8','科幻片','kehuanpian',14,1,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('9','恐怖片','kongbupian',14,1,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('10','剧情片','juqingpian',16,1,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('11','战争片','zhanzhengpian',17,1,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('12','国产剧','guochanju',21,2,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('13','港台剧','gangtaiju',22,2,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('14','日韩剧','rihanju',23,2,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."vod_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_vodtemplate,t_playtemplate,t_downtemplate,t_hide,t_union)  VALUES ('15','欧美剧','oumeiju',24,2,'','','vodlist.html','vod.html','vodplay.html','voddown.html',0,'')");
	
	$db->query( "INSERT into ".$app_tablepre."art_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_arttemplate,t_hide,t_union)  VALUES ('1','站内新闻','zhanneixinwen',1,0,'','','artlist.html','art.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."art_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_arttemplate,t_hide,t_union)  VALUES ('2','娱乐动态','yuledongtai',2,0,'','','artlist.html','art.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."art_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_arttemplate,t_hide,t_union)  VALUES ('3','八卦爆料','baguabaoliao',3,0,'','','artlist.html','art.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."art_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_arttemplate,t_hide,t_union)  VALUES ('4','影片资讯','yingpianzixun',4,0,'','','artlist.html','art.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."art_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_arttemplate,t_hide,t_union)  VALUES ('5','明星资讯','mingxingzixun',5,0,'','','artlist.html','art.html',0,'')");
	$db->query( "INSERT into ".$app_tablepre."art_type (t_id,t_name,t_enname,t_sort,t_pid,t_key,t_des,t_template,t_arttemplate,t_hide,t_union)  VALUES ('6','电视资讯','dianshizixun',6,0,'','','artlist.html','art.html',0,'')");
	
	echo '<script type="text/javascript">showmessage(\'数据分类初始化成功... \');</script>';
	updateCacheFile();
	echo '<script type="text/javascript">showmessage(\'数据缓存初始化成功... \');</script>';
	}
	unset($db);
}
 
function stepD()
{
	show_step(4,"安装完毕","正在删除安装脚本");
	if (file_exists("index.php")){
		@unlink("index.php");
    }
?>
<iframe id="tongji" name="tongji" src="http://www.maccms.com/tongji.html?7x-php" MARGINWIDTH="0" MARGINHEIGHT="0" HSPACE="0" VSPACE="0" FRAMEBORDER="0" SCROLLING="no" width="0" height="0"></iframe>
<div class="main"><div class="desc">如果没有自动删除install/index.php，请手工删除。 5秒后自动跳转到后台管理登录页面...</div>
<script> setTimeout("gonextpage();",5000); function gonextpage(){location.href='../admin/index.php';} </script>
<?php
}
?>