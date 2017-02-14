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
    if (app_artviewtype < 2 || app_artviewtype == 3){
        attemptCacheFile ("app", "vodmap");
        $template->html = getFileByCache("template_artmap", root . "template/" . app_templatedir . "/" . app_htmldir . "/artmap.html");
        $mac["appid"] = 21;
        $cacheName = "artmap";
        if (chkCache($cacheName)){
            $template->html = getCache($cacheName);
        }
        else{
            $template->mark();
            $template->ifEx();
            setCache ($cacheName, $template->html,0);
        }
        setCacheFile ("app", "artmap", $template->html);
        $template->run();
        echo $template->html;
    }
    else{
        redirect ($template->getArtMapLink());
    }
    dispseObj();
?>