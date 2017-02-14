<?php
	require_once ("../inc/conn.php");
    $query = $_SERVER['QUERY_STRING'];
    if (app_artcontentviewtype == 0 || app_artcontentviewtype == 3){
        $ID1 = replaceStr($query, "." . app_artsuffix . "", "");
        $ID2 = explode( "-",$ID1);
        $id = $ID2[0];
        switch(count($ID2))
		{
			case 2: $page = $ID2[1]; break;
		}
    }
    else if (app_artcontentviewtype == 1){
        $id = be("get", "id");
        $page = be("get", "page");
    }
	else{
	}
    if (!isNum($id)){ showMsg ("请勿传递非法参数！", "../"); }
    if (!isNum($page)){ $page = 1;} else { $page = intval($page);}
    if ($page < 1){ $page = 1;}
    $sql = "SELECT * FROM {pre}art WHERE a_hide=0 and a_id=" . $id;
    $row = $db->getRow($sql);
    if (!$row){ showMsg ("找不到此数据", "../"); }
    $mac["page"] = $page;
    $mac["artid"] = intval($id);
    $mac["arttypeid"] = $row["a_type"];
    $typearr = getValueByArray($cache[1], "t_id", $mac["arttypeid"]);
    $template->loadart ($row,$typearr);
    unset($row);
    echo $template->html;
    dispseObj();
?>