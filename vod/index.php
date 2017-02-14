<?php
	require_once ("../inc/conn.php");
    $query = $_SERVER['QUERY_STRING'];
    
    if (app_vodcontentviewtype == 0 || app_vodcontentviewtype == 3){
        $ID1 = replaceStr($query, "." . app_vodsuffix . "", "");
        $ID2 = explode( "-",$ID1);
        $id = $ID2[0];
    }
    else if (app_vodcontentviewtype == 1){
        $id = be("get", "id");
    }
	else{
	}
    if (!isNum($id)){ showMsg ("请勿传递非法参数！", "../"); }
    $sql = "SELECT * FROM {pre}vod WHERE d_hide=0 and d_id=" . $id;
    $row = $db->getRow($sql);
    if (!$row){ showMsg ("找不到此数据", "../"); }
    $mac["vodid"] = intval($id);
    $mac["vodtypeid"] = $row["d_type"];
    if (app_user == 1){
        if (!getUserPopedom($mac["vodtypeid"], "vod")){ showMsg ("您没有权限浏览此内容!", "../user/"); }
    }
    $typearr = getValueByArray($cache[0], "t_id", $mac["vodtypeid"]);
    $template->loadvod ($row,$typearr, "view");
    unset($row);
    echo $template->html;
    dispseObj();
?>