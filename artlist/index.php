<?php
	require_once ("../inc/conn.php");
    $page = be("get", "page");
    $query = $_SERVER['QUERY_STRING'];
    if (app_artlistviewtype == 0 || app_artlistviewtype == 3){
        $ID1 = replaceStr($query, "." . app_artsuffix . "", "");
        if (strpos($ID1, "-") > 0){
            $ID2 = explode("-",$ID1);
            $id = $ID2[0];
            switch(count($ID2))
            {
                case 2: $page = $ID2[1]; break;
                case 3: $order = $ID2[1]; $by = $ID2[2]; break;
                case 4: $page = $ID2[1]; $order = $ID2[2]; $by = $ID2[3]; break;
            }
        }
        else{
            $id = $ID1;
            $page = 1;
        }
    }
    else if (app_artlistviewtype == 1){
        $id = be("get", "id");
        $order = be("get", "order");
        $by = be("get", "by");
    }
    else{
    }
    if (!isNum($id)){ showMsg ("请勿传递非法参数！", "../"); }
    if (!isNum($page)){ $page = 1;} else { $page = intval($page);}
    if ($page < 1){ $page = 1;}
    $mac["arttypeid"] = intval($id);
    $mac["page"] = $page;
    $typearr = getValueByArray($cache[1], "t_id", $mac["arttypeid"]);
    $mac["arttypepid"] = $typearr["t_pid"];
    $template->loadlist ("art", $typearr);
    echo $template->html;
    dispseObj();
?>