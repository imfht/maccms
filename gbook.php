<?php
/*
软件名称：MacCMS
'开发作者：MagicBlack    官方网站：http://www.maccms.com/
'--------------------------------------------------------
'适用本程序需遵循 CC BY-ND 许可协议
'这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用；
'不允许对程序代码以任何形式任何目的的再发布。
'--------------------------------------------------------
*/
?>
<?php
	require_once ("inc/conn.php");
	$template->html = getFileByCache("template_gbook", root . "template/" . app_templatedir . "/" . app_htmldir . "/gbook.html");
    $id = be("get", "id"); $id = chkSql($id, true);
    $name = be("get", "name"); $name = unescape($name); $name = chkSql($name, true);
	$mac["appid"] = 30;
    $str = "<div id=\"maccms_gbook\"><div style=\"padding:5px;text-align:center;\"><img src=\"". app_installdir ."images/loading.gif\"/> &nbsp;&nbsp;<strong>评论载入中，请稍候.....</strong></div></div>";
	$str = $str . "<script>getGbook('".$id."','".$name."');</script>";
	$template->html = replaceStr($template->html, "{maccms:gbook}", $str);
	$template->mark();
	$template->ifEx();
	$template->run();
	echo $template->html;
	dispseObj();
?>