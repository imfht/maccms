<?php
require_once ("admin_conn.php");
require_once ("version.php");
$action = be("get","action");
switch(trim($action))
{
	case "login" : login();break;
	case "check" : checkLogin();break;
	case "logout" : logout();break;
	case "go" : gourl();break;
	case "wel" : chkLogin(); headAdmin ("欢迎页面") ; wel();break;
	default : chkLogin(); main();break;
}
dispseObj();

function gourl()
{
	$url = be("get","url");
	if($url!=""){
		if (strpos($url,".")<1) { $url.=".php"; }
		echo "<script language=\"javascript\">setTimeout(\"gourl();\",500);function gourl(){location.href='".$url."';}</script>";
	}
	else{
		echo "url参数不能为空";
	}
}

function checkLogin()
{
	global $db;
	$m_name = be("post","m_name");
	$m_name = chkSql($m_name,true);
	$m_password = be("post","m_password");
	$m_password = chkSql($m_password,true);
	$m_password = md5($m_password);
	$m_check = be("post","m_check");
	if (isN($m_name) || isN($m_password) || isN($m_check)){
		alertUrl ("请输入您的用户名或密码!","?action=login");
	}
	$row = $db->getRow("SELECT * FROM {pre}manager WHERE m_name='". $m_name ."' AND m_password = '". $m_password ."' AND m_status=1");
	if ($row && ($m_check==app_safecode)){
		sCookie ("adminid",$row["m_id"]);
		sCookie ("adminname",$row["m_name"]);
		sCookie ("adminlevels",$row["m_levels"]);
		$randnum = md5(rand(1,99999999));
		sCookie ("admincheck",md5($randnum . $row["m_name"] .$row["m_id"]));
		$db->Update("{pre}manager",array("m_logintime","m_loginip","m_random"),array(date("Y-m-d H:i:s"),getIP(),$randnum)," m_id=". $row["m_id"]);
		echo "<script>top.location.href='index.php';</script>";
	}
	else{
		alertUrl ("您输入的用户名和密码不正确或者您不是系统管理员!","?action=login");
	}
}

function logout()
{
	sCookie ("adminname","");
	sCookie ("adminid","");
	sCookie ("adminlevels","");
	sCookie ("admincheck", "");
	echo "<script>top.location.href='index.php?action=login';</script>";
}

function wel()
{
?>
</head>
<body>
	<table class="tb">
        <tr><td colspan="4">站点信息</td></tr>
        <tr>
            <td width="90">服务器类型：</td><td width="400"><?php echo PHP_OS;?></td>
            <td width="90">脚本解释引擎：</td><td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
        </tr>
        <tr>
            <td>站点物理路径：</td><td><?php echo $_SERVER['PATH_TRANSLATED'];?></td>
            <td>服务器名：</td><td><?php echo $_SERVER["SERVER_NAME"];?></td>
        </tr>
        <tr>
            <td>访问远程URL allow_url_fopen：</td><td><?php echo getcon("allow_url_fopen");?></td>
        	<td>访问远程URL curl_init：</td><td><?php echo isfun('curl_init');?></td>
        </tr>
        <tr>
        	<td>mb_string 函数支持库：</td><td><?php echo isfun("mb_convert_encoding");?></td>
        	<td>xml解析DOMDocument：</td><td><?php echo isfun("dom_import_simplexml");?></td>
        </tr>
		<tr>
            <td>单页最大使用内存 memory_limit：</td><td><?php echo getcon("memory_limit")?></td>
            <td>POST最大数据量 post_max_size：</td><td><?php echo getcon("post_max_size");?></td>
        </tr>
		<tr>
            <td>最大上传文件 upload_max_filesize：</td><td><?php echo getcon("upload_max_filesize");?></td>
            <td>页面最长运行 max_execution_time：</td><td><?php echo getcon("max_execution_time");?></td>
        </tr>
        <tr>
            <td colspan="4">当前版本 ：<?php echo version?> &nbsp;&nbsp;&nbsp;&nbsp; <span id="update"><font color='red'> </font></span> </td>
        </tr>
    </table>
	<script language="javascript" src="<?php echo macUrl?>update/updateutf.js"></script>
    <script language="javascript" src="<?php echo macUrl?>update/p/?c=check&v=<?php echo version?>"></script>
</body>
</html>
<?php
}

function main()
{
	$menustr = file_get_contents( "../inc/dim_menu.txt" );
	$menustr = replaceStr($menustr,chr(10),"");
	$menuarr = explode(chr(13),$menustr);
	$rc=false;
	
	$menudiy = "\"welcome\":{\"text\":\"欢迎页面\",\"url\":\"index.php?action=wel\"},\"leftdim_config\":{\"text\":\"快捷菜单配置\",\"url\":\"admin_leftdim.php\"},\"diym00\":{\"text\":\"\",\"url\":\"#\"}";
	if( count($menuarr)>0) { $menudiy = $menudiy.","; }
	for ($i=0;$i<count($menuarr);$i++){
		$name="";
		$icon="line";
		$url="#";
		if ($rc) { $menudiy = $menudiy . ",";}
		if ($menuarr[$i] != ""){
			$valarr = explode(",",$menuarr[$i]);
			if (count($valarr)==2) { $icon = "icon-100".$i; $name = $valarr[0]; $url = $valarr[1]; }
		}
		$menudiy = $menudiy ."\"diym".$i."\":{\"text\":\"".$name."\",\"url\":\"".$url."\"}";
		$rc = true;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>管理中心 - 苹果CMS</title>
<link rel="stylesheet" type="text/css" href="../images/adm/style.css" />
<script language="javascript" src="../js/jquery.js"></script>
<script language="javascript" src="../js/jquery.pngFix.js"></script>
</head>
<body>
<script type="text/javascript">
	function updateindex(){
		$("#cachestate").text("Loading....");
		$.get("admin_cache.php?action=uptoindex&rnd"+Math.random(),function(obj){
			if(obj !="" && obj !=undefined){
				$("#cachestate").text("静态首页删除失败！");
			}
			else{
				$("#cachestate").text("静态首页删除完毕！");
			}
		});
	}
	function updatedatacache(){
		$("#cachestate").text("Loading....");
		$.get("admin_cache.php?action=uptodata&rnd"+Math.random(),function(obj){
			if(obj !="" && obj !=undefined){
				$("#cachestate").text("数据缓存更新失败！");
			}
			else{
				$("#cachestate").text("数据缓存更新完毕！");
			}
		});
	}
    function updatecache(){
		$("#cachestate").text("Loading....");
		$.get("admin_cache.php?action=upto&rnd"+Math.random(),function(obj){
			$("#cachestate").text("内存缓存更新完毕！");
		});
	}	
	function updatefilecache(){
		$("#cachestate").text("Loading....");
		$.get("admin_cache.php?action=uptofile&rnd"+Math.random(),function(obj){
			$("#cachestate").text("文件缓存更新完毕！");
		});
	}
var menu = {
	"m1":{"text":"首页快捷","default":"welcome","children":{<?php echo $menudiy;?> }},
	
	"m2":{"text":"系统管理","default":"basic_config","children":{"basic_config":{"text":"站点配置","url":"admin_config.php"},"play_config":{"text":"播放器配置","url":"admin_config.php?action=configplay"},"connect_config":{"text":"一键登录配置","url":"admin_config.php?action=configconnect"},"buy_config":{"text":"在线支付配置","url":"admin_config.php?action=configbuy"},"leftdim_config":{"text":"快捷菜单配置","url":"admin_leftdim.php"},"timming_config":{"text":"定时任务配置","url":"admin_timming.php"},"database":{"text":"数据库管理","url":"admin_db.php"},"cache":{"text":"系统缓存管理","url":"admin_cache.php"}}},
	
	"m3":{"text":"扩展功能","default":"sql","children":{"sql":{"text":"执行SQL语句","url":"admin_sql.php"},"pic":{"text":"图片管理","url":"admin_pic.php"},"link":{"text":"友情链接","url":"admin_link.php"},"ads":{"text":"自定广告","url":"admin_ads.php"},"tongji":{"text":"流量统计","url":"admin_ads.php?action=tj"},"htmltojs":{"text":"HTML互转JS","url":"editor/htmltojs.html"},"urlencode":{"text":"URL汉字编码","url":"editor/urlencode.html"},"gbook":{"text":"系统留言本","url":"admin_gbook.php"},"comment":{"text":"系统评论","url":"admin_comment.php"}}},
	
	"m4":{"text":"视频管理","default":"vod","children":{"vodtype":{"text":"视频分类","url":"admin_vod_type.php"},"arealang":{"text":"地区语言","url":"admin_vod_arealang.php"},"vodtopic":{"text":"视频专题","url":"admin_vod_topic.php"},"server":{"text":"服务器组","url":"admin_vod_server.php"},"player_config":{"text":"播放器管理","url":"admin_player.php"},"vodrepeat":{"text":"检测重复数据","url":"admin_vod.php?repeat=ok"},"vod":{"text":"视频数据","url":"admin_vod.php"},"vodadd":{"text":"添加视频","url":"admin_vod.php?action=add"},"vodpse":{"text":"伪原创","url":"admin_vod.php?action=pse"},"vodbatch":{"text":"批量操作","url":"admin_vod_batch.php"}}},
	
	"m5":{"text":"文章管理","default":"art","children":{"arttype":{"text":"文章分类","url":"admin_art_type.php"},"arttopic":{"text":"文章专题","url":"admin_art_topic.php"},"artrepeat":{"text":"检测重复数据","url":"admin_art.php?repeat=ok"},"art":{"text":"文章数据","url":"admin_art.php"},"artadd":{"text":"添加文章","url":"admin_art.php?action=add"}}},
	
	"m6":{"text":"用户管理","default":"manager","children":{"manager":{"text":"管理员","url":"admin_manager.php"},"usergroup":{"text":"会员组","url":"admin_user_group.php"},"user":{"text":"会员","url":"admin_user.php"},"usercard":{"text":"充值卡","url":"admin_user_card.php"}}},
	
	"m7":{"text":"模板生成","default":"make","children":{"html":{"text":"页面模板","url":"admin_templates.php"},"custom":{"text":"自定义页面","url":"admin_templates.php?action=label"},"makeindex":{"text":"生成首页","url":"admin_makehtml.php?action=index"},"makeartindex":{"text":"生成文章首页","url":"admin_makehtml.php?action=index&flag=art"},"makemap":{"text":"生成地图","url":"admin_makehtml.php?action=map&flag=vod"},"makeartmap":{"text":"生成文章地图","url":"admin_makehtml.php?action=map&flag=art"},"make":{"text":"生成选项","url":"admin_makehtml.php"}}},
	
	"m8":{"text":"采集管理","default":"maccj","children":{"maccj":{"text":"联盟资源库","url":"admin_maccj.php"},"interface":{"text":"站外入库配置","url":"admin_interface.php?action=retype"},"vodcj":{"text":"视频自定义采集","url":"collect/collect_vod_manage.php"},"vodcjchange":{"text":"----分类转换","url":"collect/collect_vod_change.php"},"vodcjfilter":{"text":"----过滤替换","url":"collect/collect_vod_filters.php"},"vodcjdata":{"text":"----入库管理","url":"collect/collect_vod.php"},"artcj":{"text":"文章自定义采集","url":"collect/collect_art_manage.php"},"artcjchange":{"text":"----分类转换","url":"collect/collect_art_change.php"},"artcjfilter":{"text":"----过滤替换","url":"collect/collect_art_filters.php"},"artcjdata":{"text":"----入库管理","url":"collect/collect_art.php"}  }}
};
var currTab = 'm1';
var firstOpen = [];
var levels = '1, <?php echo getCookie("adminlevels")?>';
</script>
<div id="loading">
	数据加载中...<img src="../images/loading.gif" />
</div>
<div class="back_nav">
    <div class="back_nav_list">
        <dl>
            <dt></dt>
            <dd><a href="javascript:;" onclick="openItem('','');none_fn();"></a></dd>
        </dl>
    </div>
    <div class="shadow"></div>
    <div class="close_float"><img src="../images/adm/close2.gif" /></div>
</div>
<div id="head">
    <div id="logo"><img src="../images/adm/logo.png" /></div>
    <div id="menu"><span>您好，<strong><?php echo getCookie("adminname")?></strong> [欢迎使用MacCMS 7.x] [<a href="?action=logout" title="注销登陆">注销登陆</a>]</span>
    <a href="javascript:;" class="menu_btn1" id="iframe_refresh" title="刷新工作区页面">刷新页面</a>
    <a href="../" target="_blank" class="menu_btn1" title="返回网站首页">站点首页</a>
    <a href="http://www.maccms.com/" target="_blank" class="menu_btn1" title="官方网站">官方网站</a>
    <a href="http://bbs.maccms.com/" target="_blank" class="menu_btn1" title="官方论坛">官方论坛</a>
    <iframe id="tongji" name="tongji" src="http://www.maccms.com/update/updatephp7.htm?v=<?php echo version?>" MARGINWIDTH="0" MARGINHEIGHT="0" HSPACE="0" VSPACE="0" FRAMEBORDER="0" SCROLLING="no" width="0" height="0"></iframe>
    </div>
    <div id="menu1">
    	&nbsp;<font id="cachestate" style="color:red;"></font>
    	&nbsp;静态首页：<a href="javascript:void(0)" onClick="updateindex();">[点击删除]</a>
    	&nbsp;数据缓存：<a href="javascript:void(0)" onClick="updatedatacache();">[点击更新]</a>
		&nbsp;内存缓存：<a href="javascript:void(0)" onClick="updatecache();">[点击更新]</a>
		&nbsp;文件缓存：<a href="javascript:void(0)" onClick="updatefilecache();">[点击更新]</a>
    </div>
    <ul id="nav"></ul>
    <div id="headBg"></div>
</div>
<div id="content">
    <div id="left">
        <div id="leftMenus">
            <dl id="submenu">
                <dt><a class="ico1" id="submenuTitle" href="javascript:;"></a></dt>
            </dl>
         </div>
		<div class="copyright">
			<p>Powered by 苹果CMS </p>
			<p>&copy; 2008-2012 <a href="http://www.maccms.com/" target="_blank">MACCMS</a> Inc.</p>
		</div>
    </div>
    <div id="right">
        <iframe hspace="0" vspace="0" frameborder="0" scrolling="auto" style="display:none;" width="100%" id="workspace" name="workspace"></iframe>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript" src="../js/adm/index.js"></script>
<span style="display:none"><script src="http://s11.cnzz.com/stat.php?id=2081333&web_id=2081333" language="JavaScript"></script></span>
</body>
</html>
<?php
}
function login()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>登录管理中心 - 苹果CMS</title>
<link rel="stylesheet" type="text/css" href="../images/adm/login.css" />
<script language="javascript" src="../js/jquery.js"></script>
<script language="javascript" src="../js/jquery.pngFix.js"></script>
</head>
<body>
<div id="header">
   <div class="logo"></div>
</div>
<div id="wrapper">
   <div class="console_left">
	   <div class="title">登录管理中心</div>
	   <p><span>苹果CMS(maccms)</span> 是一个采用ASP(access、mssql)或<span>PHP(mysql)</span>构建的高效视频电影网站管理系统！</p>
	   <div class="intro_1">轻松管理和配置各种信息</div>
	   <div class="intro_2">轻松发布在线视频资源</div>
	   <div class="intro_3">设置网站安全验证过滤无效信息</div>
   </div>
   <div class="console_right">
	   <div class="title">请您在这里登录</div>
	   <div class="login">
		  <form action="?action=check" method="post" name="form1" id="form1" class="s_lo_f" autocomplete="off">
				<div class="user"><label>用户名:</label><input tabindex="1" type="text" name="m_name" id="m_name" size="20" maxLength="20" value=""></div>
		   		<div class="pwd"><label>密　码:</label><input tabindex="2" type="password" name="m_password" id="m_password" size="20" maxLength="20" value=""></div>
		   		<div class="code"><label>安全码:</label><input tabindex="3" type="password" name="m_check" id="m_check" size="20" maxLength="20" value=""></div>
		   		<div class="btn_login"><input type="submit" name="login" id="login" value="登陆" /></div>
		   </form>
	   </div>
   </div>
   <div class="clear"></div>
	<div class="reg"> </div>
	<hr class="hr_solid" /></div>
</div>
<div id="footer"><span class="left">&copy;2008-2012 Powered By 苹果CMS, <a href="http://www.maccms.com/">MACCMS</a> Inc.
</span><span class="right"><a href="../" title="返回首页">返回首页</a>&nbsp;|&nbsp;<a href="http://bbs.maccms.com/">反馈</a></span> 
</div>
<script>
var cururl=",<?php echo geturl();?>";
$(document).ready(function(){
	$("#login").click(
		function(){   
			if($('#m_name').val() == ""){
				alert( "请输入用户名" );
				$('#m_name').focus();
				return false;
			}
			if($('#m_password').val() == ""){
				alert( "请输入密码" );
				$('#m_password').focus();
				return false;
			}
			if($('#m_check').val() == ""){
				alert( "请输入安全码" );
				$('#m_check').focus();
				return false;
			}
			$("#form1").submit();
			$("#login").attr("disabled", "disabled");
		}
	);
	$('#m_name').focus();
    $("img").pngfix();
    if(cururl.indexOf("/admin/") >0){alert('请将文件夹admin改名,避免被黑客入侵攻击');}
});
</script>
</body>
</html>
<?php
}
?>