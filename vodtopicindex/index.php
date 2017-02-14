<?php
	require_once ("../inc/conn.php");
    $page = be("get", "page");
    $query = $_SERVER['QUERY_STRING'];
    
    if (app_vodtopicviewtype == 0 || app_vodtopicviewtype == 3){
        $ID1 = replaceStr($query, "." . app_vodsuffix . "", "");
        if (strpos($ID1, "-") > 0){
            $ID2 = explode("-",$ID1);
            $page = $ID2[0];
        }
        else{
            $page = $ID1;
        }
        
    }
    else if (app_vodtopicviewtype == 1){
        $page = be("get", "page");
    }
    else{
    }
    if (!isNum($page)){ $page = 1;} else { $page = intval($page); }
    if ($page < 1){ $page = 1;}
    $mac["appid"] = 12;
    $mac["page"] = $page;
    $t1 = root . "template/" . app_templatedir . "/" . app_htmldir . "/vodtopic.html";
    $template->html = getFileByCache("template_vodtopic", $t1);
    $template->topicpagelist();
    $template->pageshow();
    $template->mark();
    $template->ifEx();
    $template->run();
    echo $template->html;
    dispseObj();
?>