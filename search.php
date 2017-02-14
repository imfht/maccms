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
	require_once ("inc/360_safe3.php");
	$mac["page"] = be("all", "page");
    $mac["flag"] = be("all", "searchtype");
    $mac["keyword"] = be("all", "keyword"); $mac["keyword"] = chkSql($mac["keyword"], true);
    $mac["keytype"] = be("all", "keytype"); $mac["keytype"] = chkSql($mac["keytype"], true);
    $mac["ids"] = be("all", "ids"); $mac["ids"] = chkSql($mac["ids"], true);
    $mac["pinyin"] = be("all", "pinyin"); $mac["pinyin"] = chkSql($mac["pinyin"], true);
    $mac["starring"] = be("all", "starring"); $mac["starring"] = chkSql($mac["starring"], true);
    $mac["directed"] = be("all", "directed"); $mac["directed"] = chkSql($mac["directed"], true);
    $mac["area"] = be("all", "area"); $mac["area"] = chkSql($mac["area"], true);
    $mac["language"] = be("all", "language"); $mac["language"] = chkSql($mac["language"], true);
    $mac["year"] = be("all", "year"); $mac["year"] = chkSql($mac["year"], true);
    $mac["letter"] = be("all", "letter"); $mac["letter"] = chkSql($mac["letter"], true);
    $mac["type"] = be("all", "type"); $mac["type"] = chkSql($mac["type"], true);
    $order = be("all", "order"); $order = chkSql($order, true); $mac["order"]=$order;
    $by = be("all", "by"); $by = chkSql($by, true);$mac["by"]=$by;
    
    
    switch($mac["keytype"])
    {
        case "pinyin": $mac["pinyin"] = $mac["keyword"]; $mac["keyword"]=""; break;
        case "starring": $mac["starring"] = $mac["keyword"]; $mac["keyword"]=""; break;
        case "directed": $mac["directed"] = $mac["keyword"];$mac["keyword"]=""; break;
        case "area": $mac["area"] = $mac["keyword"]; $mac["keyword"]=""; break;
        case "language": $mac["language"] = $mac["keyword"]; $mac["keyword"]=""; break;
        case "year": $mac["year"] = $mac["keyword"]; $mac["keyword"]=""; break;
        case "letter" : $mac["letter"] = $mac["keyword"]; $mac["keyword"]=""; break;
        case "type" : $mac["type"] = $mac["keyword"]; $mac["keyword"]=""; break;
        default : $mac["keyword"] = $mac["keyword"]; break;
    }
    
    
    if ($mac["flag"]!="artsearch"){ $mac["flag"] = "vodsearch";}
    if (!isNum($mac["page"])){ $mac["page"] = 1;} else { $mac["page"] = intval($mac["page"]);}
    if ($mac["page"] < 1){ $mac["page"] = 1;}
    if(!isNum($mac["type"])) { $mac["type"]=""; } else { $mac["type"] = intval($mac["type"]); }
    
    if ( $mac["page"]==1 && getTimeSpan("lastSearchTime") < 3){ 
    	showMsg ("每次搜索时间间隔3秒，请稍后重试", app_installdir);
    	exit;
    }
    
    if (isN($mac["keyword"]) && isN($mac["ids"]) && isN($mac["pinyin"]) && isN($mac["starring"]) && isN($mac["directed"]) && isN($mac["area"]) && isN($mac["language"]) && isN($mac["year"]) && isN($mac["letter"])  && isN($mac["type"]) ){ alert ("搜索参数不正确"); }
        
        
    if ($mac["flag"]=="artsearch"){
    	
    	if (!isN($mac["keyword"])) { $mac["key"]=$mac["keyword"] ; $mac["des"] = $mac["des"] . "&nbsp;标题为" . $mac["keyword"]; $mac["where"] = $mac["where"] . " AND a_title LIKE '%" . $mac["keyword"] . "%' ";}
    
	    if (!isN($mac["pinyin"])){ $mac["key"]=$mac["pinyin"] ; $mac["des"] = $mac["des"] . "&nbsp;拼音为" . $mac["pinyin"]; $mac["where"] = $mac["where"] . " AND a_entitle LIKE '%" . $mac["pinyin"] . "%' ";}
	    
	    if (!isN($mac["letter"])){ $mac["key"]=$mac["letter"]; $mac["des"] = $mac["des"] . "&nbsp;首字母为" . $mac["letter"]; $mac["where"] = $mac["where"] . " AND a_letter = '" . $mac["letter"] . "' ";}
    
    	if(!isN($mac["type"])){
    		$typearr = getValueByArray($cache[1], "t_id", $mac["type"]);
    		if (is_array($typearr)){
    			if (isN($mac["key"])){	$mac["key"]= $typearr["t_name"];  }
    			$mac["des"] = $mac["des"] . "&nbsp;分类为" . $typearr["t_name"];
    			$mac["where"] = $mac["where"] . " AND a_type in (" . $typearr["childids"] . ") ";
    		}
    	}
    }
    else{
    	if (!isN($mac["keyword"])) { $mac["key"]=$mac["keyword"] ; $mac["des"] = $mac["des"] . "&nbsp;名称或主演为" . $mac["keyword"]; $mac["where"] = $mac["where"] . " AND ( d_name LIKE '%" . $mac["keyword"] . "%' OR d_starring like '%".$mac["keyword"]."%' ) ";}
    
	    if (!isN($mac["pinyin"])){ $mac["key"]=$mac["pinyin"] ; $mac["des"] = $mac["des"] . "&nbsp;拼音为" . $mac["pinyin"]; $mac["where"] = $mac["where"] . " AND d_enname LIKE '%" . $mac["pinyin"] . "%' ";}
	    
	    if (!isN($mac["letter"])){ $mac["key"]=$mac["letter"]; $mac["des"] = $mac["des"] . "&nbsp;首字母为" . $mac["letter"]; $mac["where"] = $mac["where"] . " AND d_letter = '" . $mac["letter"] . "' ";}
	    
	    if (!isN($mac["starring"])){ $mac["key"]=$mac["starring"] ; $mac["des"] = $mac["des"] . "&nbsp;主演为" . $mac["starring"]; $mac["where"] = $mac["where"] . " AND d_starring LIKE '%" . $mac["starring"] . "%' ";}
	    
	    if (!isN($mac["directed"])){ $mac["key"]=$mac["directed"] ; $mac["des"] = $mac["des"] . "&nbsp;导演为" . $mac["directed"]; $mac["where"] = $mac["where"] . " AND d_directed LIKE '%" . $mac["directed"] . "%' ";}
	    
	    if(!isN($mac["area"])){ $mac["key"]=$mac["area"] ; $mac["des"] = $mac["des"] . "&nbsp;地区为" . $mac["area"]; $mac["where"] = $mac["where"] . " AND d_area LIKE '%" . $mac["area"] . "%' ";}
	    
	    if (!isN($mac["language"])){ $mac["key"]=$mac["language"]; $mac["des"] = $mac["des"] . "&nbsp;语言为" . $mac["language"]; $mac["where"] = $mac["where"] . " AND d_language LIKE '%" . $mac["language"] . "%' ";}
	    
	    if (!isN($mac["year"])){ $mac["key"]=$mac["year"]; $mac["des"] = $mac["des"] . "&nbsp;上映年份为" . $mac["year"]; $mac["where"] = $mac["where"] . " AND d_year LIKE '%" . $mac["year"] . "%' ";}
    	
    	if(!isN($mac["type"])){
    		$typearr = getValueByArray($cache[0], "t_id", $mac["type"]);
    		if (is_array($typearr)){
    			if (isN($mac["key"])){ $mac["key"]= $typearr["t_name"];  }
    			$mac["des"] = $mac["des"] . "&nbsp;分类为" . $typearr["t_name"];
    			$mac["where"] = $mac["where"] . " AND d_type in (" . $typearr["childids"] . ") ";
    		}
    	}
    }
    
    attemptCacheFile ("search", urlencode($mac["keyword"] . "-" . $mac["ids"] . "-" . $mac["pinyin"] . "-" . $mac["area"] . "-" . $mac["language"] . "-" . $mac["year"] . "-" . $mac["letter"] . "-" . $mac["type"] . "-" .$mac["starring"] . "-" . $mac["directed"]  ."-" .$mac["page"]) );
    
    $template->html = getFileByCache("template_" . $mac["flag"], root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $mac["flag"] . ".html");
    $template->mark();
    
    if ($mac["flag"] == "artsearch"){
    	$template->artpagelist();
    }
    else{
    	$template->vodpagelist();
    }
    $template->pageshow();
    $template->html = replaceStr($template->html, "{searchpage:des}", $mac["des"]);
    $template->html = replaceStr($template->html, "{searchpage:key}", $mac["key"]);
    $template->html = replaceStr($template->html, "{searchpage:page}", $mac["page"]);
    $template->html = replaceStr($template->html, "{searchpage:order}", $mac["order"]);
    $template->html = replaceStr($template->html, "{searchpage:by}", $mac["by"]);
    
    $template->html = replaceStr($template->html, "{searchpage:name}", $mac["keyword"]);
    $template->html = replaceStr($template->html, "{searchpage:nameencode}", urlencode($mac["keyword"]) );
    
    $template->html = replaceStr($template->html, "{searchpage:pinyin}", $mac["pinyin"]);
    $template->html = replaceStr($template->html, "{searchpage:letter}", $mac["letter"]);
    $template->html = replaceStr($template->html, "{searchpage:year}", $mac["year"]);
    $template->html = replaceStr($template->html, "{searchpage:type}", $mac["type"]);
    
    
    $template->html = replaceStr($template->html, "{searchpage:starring}", $mac["starring"]);
    $template->html = replaceStr($template->html, "{searchpage:starringencode}", urlencode($mac["starring"]) );
    
    $template->html = replaceStr($template->html, "{searchpage:directed}", $mac["directed"]);
    $template->html = replaceStr($template->html, "{searchpage:directedencode}", urlencode($mac["directed"]) );
    
    $template->html = replaceStr($template->html, "{searchpage:area}", $mac["area"]);
    $template->html = replaceStr($template->html, "{searchpage:areaencode}", urlencode($mac["area"]) );
    
    $template->html = replaceStr($template->html, "{searchpage:language}", $mac["language"]);
    $template->html = replaceStr($template->html, "{searchpage:languageencode}", urlencode($mac["language"]) );
    
    
    $template->ifEx();
    setCacheFile ("search", urlencode($mac["keyword"] . "-" . $mac["ids"] . "-" . $mac["pinyin"] . "-" . $mac["area"] . "-" . $mac["language"] . "-" . $mac["year"] . "-" . $mac["letter"] . "-" . $mac["type"]  . "-" .$mac["starring"] . "-" . $mac["directed"] ."-" .$mac["page"] ."-" .$mac["order"] ."-" .$mac["by"]), $template->html);
    $template->run();
    echo $template->html;
    $_SESSION["lastSearchTime"] = time();
    dispseObj();
?>