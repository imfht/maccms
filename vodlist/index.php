<?php
	require_once ("../inc/conn.php");
    $page = be("get", "page");
    $query = $_SERVER['QUERY_STRING'];
    if (app_vodlistviewtype == 0 || app_vodlistviewtype == 3){
        $ID1 = replaceStr($query, "." . app_vodsuffix . "", "");
        if (strpos($ID1, "-") > 0){
            $ID2 = explode("-",$ID1);
            $id = $ID2[0];
            
            switch(count($ID2))
            {
                case 2: $page = $ID2[1]; break;
                case 3: $page = $ID2[1]; $mac["area"]=$ID2[2]; break;
                case 4: $page = $ID2[1]; $mac["area"]=$ID2[2]; $mac["year"]=$ID2[3]; break;
                case 5: $page = $ID2[1]; $mac["area"]=$ID2[2]; $mac["year"]=$ID2[3]; $order = $ID2[4]; break;
                case 6: $page = $ID2[1]; $mac["area"]=$ID2[2]; $mac["year"]=$ID2[3]; $order = $ID2[4]; $by = $ID2[5]; break;
            }
            $mac["area"] = urldecode($mac["area"]);
        }
        else{
            $id = $ID1;
            $page = 1;
        }
	}
    else if (app_vodlistviewtype == 1){
        $id = be("get", "id");
        $mac["area"] = be("get", "area");
        $mac["year"] = be("get", "year");
        $order = be("get", "order");
        $by = be("get", "by");
    }
    else{
    }
    if (!isNum($id)){ showMsg ("请勿传递非法参数！", "../"); }
    if (!isNum($page)){ $page = 1;} else { $page = intval($page);}
    if ($page < 1){ $page = 1;}
    $mac["area"] = chkSql($mac["area"], true);
    $mac["year"] = chkSql($mac["year"], true);
    
    if (app_user == 1){ if (!getUserPopedom($id, "list")){ showMsg ("您没有权限浏览此内容!", "../"); }  }
    $mac["vodtypeid"] = intval($id);
    $mac["page"] = $page;
    $mac["order"] =$order;
    $mac["by"] =$by;
    $typearr = getValueByArray($cache[0], "t_id", $mac["vodtypeid"]);
    $mac["vodtypepid"] = $typearr["t_pid"];
    $template->loadlist ("vod", $typearr);
    echo $template->html;
    dispseObj();
?>