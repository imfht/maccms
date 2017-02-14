<?php
	require_once ("../inc/conn.php");
    $page = be("get", "page");
    $query = $_SERVER['QUERY_STRING'];
    
    if (app_arttopicviewtype == 0 || app_arttopicviewtype == 3){
        $ID1 = replaceStr($query, "." . app_artsuffix . "", "");
    	if (strpos($ID1, "-") > 0){
            $ID2 = explode("-",$ID1);
            $id = $ID2[0];
            if (count($ID2) >= 1){ $page = $ID2[1]; } else { $page = 1; }
        }
        else{
            $id = $ID1;
            $page = 1;
        }
    }
    else if (app_arttopicviewtype == 1){
    	$id = be("get", "id");
    }
    else{
    }
    if (!isNum($id)) { showMsg ("请勿传递非法参数！", "../");}
    if (!isNum($page)){ $page = 1;} else { $page = intval($page);}
    if ($page < 1){ $page = 1;}
    $mac["arttopicid"] = intval($id);
    $mac["page"] = $page;
    $typearr = getValueByArray($cache[3], "t_id", $mac["arttopicid"]);
    $template->loadtopic ("art", $typearr);
    echo $template->html;
    dispseObj();
?>