<?php
class AppTemplate
{
    var $html,$markdes,$markval;
    var $page_id, $page_name, $page_enname, $page_addtime,$page_size, $page_typearr,$page_type, $page_count, $data_count, $page,$startnum,$page_content;
	var $par_type, $par_id, $par_by, $par_order, $par_parent, $par_num, $par_table, $par_start, $par_topic, $par_level, $par_day, $par_state,$par_area,$par_lang,$par_letter,$par_tag,$par_label,$par_maxpage,$par_starring,$par_similar,$par_from,$par_year,$par_end;
	
    function AppTemplate()
    {
    	$this->startnum=0;
	}
    
    function getIndexLink()
    {
        return app_installdir;
    }
    
    function getArtIndexLink()
    {
        switch (app_artviewtype)
		{
		case 2:
		case 3:
			$str = "artindex." . app_artsuffix;
			break;
		default:
			$str = "artindex.php";
			break;
        }
        return app_installdir . $str;
    }
    
    function getMapLink()
    {
        switch (app_vodviewtype)
		{
		case 2:
		case 3:
			$str = "map."  .app_vodsuffix;
			break;
		default:
			$str = "map.php";
			break;
        }
        return app_installdir . $str;
    }
    
    function getArtMapLink()
    {
        switch (app_artviewtype)
		{
		case 2:
		case 3:
			$str = "artmap." . app_artsuffix;
			break;
		default:
			$str = "artmap.php";
			break;
        }
        return app_installdir . $str;
    }
    
    function getArtTypeLink($id, $name, $enname, $suffix)
    {
        switch (app_artlistviewtype)
		{
        case 1:
            $str = "artlist/index.php?id=" . $id;
			break;
        case 2:
            $str = replaceStr(app_artlistpath, "{id}", $id);
            $str = replaceStr($str, "{name}", $name);
            $str = replaceStr($str, "{enname}", $enname);
            if (strpos($str,"{md5}")>0){
            	$str = replaceStr($str, "{md5}", md5($id));
            }
            if ($suffix) { $str = $str . "." . app_artsuffix; $str = replaceStr($str,"index.".app_artsuffix,""); }
            break;
        case 3:
            $str = replaceStr(app_artlistpath, "{id}", $id);
            if ($suffix) { $str = $str . "." . app_artsuffix; }
           	break;
        default:
            $str = "artlist/?" . $id;
            if ($suffix) { $str = $str . "." . app_artsuffix; }
            break;
        }
        return app_installdir . $str;
    }
    
    function getArtTopicLink($id, $name, $enname, $suffix)
    {
        switch (app_arttopicviewtype)
		{
        case 1:
            $str = "arttopic/index.php?id=" . $id;
        	break;
        case 2:
            $str = replaceStr(app_arttopicpath, "{id}", $id);
            $str = replaceStr($str, "{name}", $name);
            $str = replaceStr($str, "{enname}", $enname);
            if (strpos($str,"{md5}")>0){
            	$str = replaceStr($str, "{md5}", md5($id));
            }
            if ($suffix) { $str = $str . "." . app_artsuffix; $str = replaceStr($str,"index.".app_artsuffix,""); }
            break;
        case 3:
            $str = replaceStr(app_arttopicpath, "{id}", $id);
            if ($suffix) { $str = $str . "." . app_artsuffix; }
            break;
        default:
            $str = "arttopic/?" . $id;
            if ($suffix) { $str = $str . "." . app_artsuffix; }
            break;
        }
        return app_installdir . $str;
    }
    
    function getArtLink($id, $name, $enname,$addtime, $typeid, $typename, $typeenname,$suffix)
    {
        switch (app_artcontentviewtype)
		{
        case 1:
            $str = "art/index.php?id=" . $id;
        	break;
        case 2:
            $str = replaceStr(app_artpath, "{id}", $id);
            if (strpos(",".$str,"{name}")>0){ $str = replaceStr($str, "{name}", repSpecialChar($name)); }
            if (strpos(",".$str,"{enname}")>0){ $str = replaceStr($str, "{enname}", repSpecialChar($enname)); }
            $str = replaceStr($str, "{typeid}", $typeid);
            $str = replaceStr($str, "{typename}", $typename);
            $str = replaceStr($str, "{typeenname}", $typeenname);
            if (strpos(",".$str,"{md5}")>0){ $str = replaceStr($str, "{md5}", md5($id)); }
            if (strpos(",".$str,"{year}")>0){ $str = replaceStr($str, "{year}", getDatet("Y",$addtime) ); }
            if (strpos(",".$str,"{month}")>0){ $str = replaceStr($str, "{month}", getDatet("m",$addtime) ); }
            if (strpos(",".$str,"{day}")>0){ $str = replaceStr($str, "{day}", getDatet("d",$addtime) ); }
            
            if ($suffix) { $str = $str . "." . app_artsuffix; $str = replaceStr($str,"index.".app_artsuffix,""); }
            break;
        case 3:
            $str = $str = replaceStr(app_artpath, "{id}", $id);
        	if ($suffix) { $str = $str . "." . app_artsuffix; }
        	break;
        default:
            $str = "art/?" . $id;
        	if ($suffix) { $str = $str . "." . app_artsuffix; }
        	break;
        }
        return app_installdir . $str;
    }
	
    function getArtPreNextLink($id,$flag)
    {
    	global $db,$cache;
    	if ($flag==0) { $str1="上一篇"; $where = " and a_id<".$id." order by a_id desc";} else{ $str1="下一篇"; $where = " and a_id>".$id." order by a_id asc";}
    	$row = $db->getRow("select a_id,a_title,a_entitle,a_type,a_addtime from {pre}art where 1=1 and a_hide=0 " .$where . " limit 0,1");
    	if ($row){
    		$tarr = getValueByArray($cache[1],"t_id" ,$row["a_type"]);
    		$str = "<em>".$str1.":<a href=". $this->getArtLink($row["a_id"],$row["a_title"],$row["a_entiele"],$row["a_addtime"],$row["a_type"],$tarr["t_name"],$tarr["t_enname"],true).">".$row["a_title"]."</a></em> ";
    	}
    	else{
    		$str = "<em>".$str1.":没有了</em> ";
    	}
    	unset($row);
    	return $str;
    }
    
    function getVodTypeLink($id, $name,$enname, $suffix)
    {
        switch(app_vodlistviewtype)
        {
        case 1:
            $str = "vodlist/index.php?id=" . $id;
        	break;
        case 2:
            $str = replaceStr(app_vodlistpath, "{id}", $id);
            $str = replaceStr($str, "{name}", $name);
            $str = replaceStr($str, "{enname}", $enname);
            if (strpos($str,"{md5}")>0){
            	$str = replaceStr($str, "{md5}", md5($id));
            }
            if ($suffix){ $str = $str. "." . app_vodsuffix ; $str = replaceStr($str,"index.".app_vodsuffix,""); }
            break;
        case 3:
            $str = replaceStr(app_vodlistpath, "{id}", $id);
            if ($suffix) { $str = $str. "." . app_vodsuffix;}
            break;
        default:
            $str = "vodlist/?" . $id;
            if ($suffix) { $str = $str. "." . app_vodsuffix;}
            break;
        }
        return app_installdir . $str;
    }
    
    function getVodTypeLinkOrder($id, $name, $enname, $suffix, $order, $by)
    {
        $str = getVodTypeLink($id, $name, $enname, false);
        if (isN($order)) { $order = "desc";}
        if (isN($by)) { $order = "time";}
        switch(app_vodlistviewtype)
        {
        case 1:
            $str = "vodlist/index.php?id=" . $id . "&order=" . $order . "&by=" . $by;
        	break;
        case 3:
            $str = replaceStr(app_vodlistpath, "{id}", $id);
            if ($suffix) { $str = $str . "-1" . "-" . $order . "-" . by . "." . app_vodsuffix;}
            break;
        default:
            $str = "vodlist/?" . $id;
            if ($suffix){ $str = $str . "-1" . "-" . order . "-" . by . "." . app_vodsuffix;}
            break;
        }
        return $str;
    }
    
    function getVodTopicLink($id, $name, $enname, $suffix)
    {
        switch(app_vodtopicviewtype)
        {
        case 1:
            $str = "vodtopic/index.php?id=" . $id;
        	break;
        case 2:
            $str = replaceStr(app_vodtopicpath, "{id}", $id);
            $str = replaceStr($str, "{name}", $name);
            $str = replaceStr($str, "{enname}", $enname);
            if (strpos($str,"{md5}")>0){
            	$str = replaceStr($str, "{md5}", md5($id));
            }
            if ($suffix) {$str = $str . "." . app_vodsuffix; $str = replaceStr($str,"index.".app_vodsuffix,"");}
            break;
        case 3:
            $str = replaceStr(app_vodtopicpath, "{id}", $id);
            if ($suffix){ $str = $str . "." . app_vodsuffix;}
            break;
        default:
            $str = "vodtopic/?" . $id;
            if ($suffix) { $str = $str . "." . app_vodsuffix;}
            break;
        }
        return app_installdir . $str;
    }
    
    function getVodLink($id, $name, $enname,$addtime, $typeid, $typename, $typeenname)
    {
    	if (app_playtype ==1) { return $this->getVodPlayUrl($id,$name,$enname,$addtime,$typeid,$typename,$typeenname,1,1); }
    	
        switch(app_vodcontentviewtype)
    	{
        case 1:
            $str = "vod/index.php?id=" . $id;
        	break;
        case 2:
            $str = replaceStr(app_vodpath, "{id}", $id);
            if (strpos(",".$str,"{name}")>0){ $str = replaceStr($str, "{name}", repSpecialChar($name)); }
            if (strpos(",".$str,"{enname}")>0){ $str = replaceStr($str, "{enname}", repSpecialChar($enname)); }
            $str = replaceStr($str, "{typeid}", $typeid);
            $str = replaceStr($str, "{typename}", $typename);
            $str = replaceStr($str, "{typeenname}", $typeenname);
            if (strpos(",".$str,"{md5}")>0){ $str = replaceStr($str, "{md5}", md5($id)); }
            if (strpos(",".$str,"{year}")>0){ $str = replaceStr($str, "{year}", getDatet("Y",$addtime) ); }
            if (strpos(",".$str,"{month}")>0){ $str = replaceStr($str, "{month}", getDatet("m",$addtime) ); }
            if (strpos(",".$str,"{day}")>0){ $str = replaceStr($str, "{day}", getDatet("d",$addtime) ); }
            $str = $str . "." . app_vodsuffix ; $str = replaceStr($str,"index.".app_vodsuffix,"");
            break;
        case 3:
            $str = $str = replaceStr(app_vodpath, "{id}", $id) . "." . app_vodsuffix;
        	break;
        default:
            $str = "vod/?" . $id . "." . app_vodsuffix;
        	break;
        }
        return app_installdir . $str;
    }
    
    function getVodPreNextLink($id,$flag)
    {
    	global $db,$cache;
    	if ($flag==0) { $str1="上一篇"; $where = " and d_id<".$id." order by d_id desc";} else{ $str1="下一篇";$where = " and d_id>".$id." order by d_id asc";}
    	$row = $db->getRow("select d_id,d_name,d_enname,d_type,d_addtime from {pre}vod where 1=1 and d_hide=0 " . $where . " limit 0,1 ");
    	if (!$row){
    		$str = "<em>".$str1.":没有了</em> ";
    	}
    	else{
    		$tarr = getValueByArray($cache[0], "t_id" ,$row["d_type"]);
    		$str = "<em>".$str1.":<a href=". $this->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$tarr["t_name"],$tarr["t_enname"]).">".$row["d_name"]."</a></em> ";
    	}
    	unset($row);
    	return $str;
    }
    
    function getVodPlayUrl($id, $name, $enname,$addtime, $typeid, $typename, $typeenname, $sort, $num)
    {
        if (app_playisopen == 0){ $strJSstart = ""; $strJSend = "";} else {$strJSstart = "javascript:OpenWindow1('"; $strJSend = "',popenW,popenH);";}
        
        switch(app_vodplayviewtype)
        {
        case 1:
            $str = "vodplay/index.php?id=" . $id . "&sort=" . $sort . "&num=" . $num;
        	break;
        case 2:
            $str = $str = replaceStr(app_vodplaypath, "{id}", $id) . "-" . $sort . "-" . $num . "." . app_vodsuffix;
        	break;
        case 3:
        case 4:
        case 5:
        case 6:
            $str = replaceStr(app_vodplaypath, "{id}", $id);
            if (strpos(",".$str,"{name}")>0){ $str = replaceStr($str, "{name}", repSpecialChar($name)); }
            if (strpos(",".$str,"{enname}")>0){ $str = replaceStr($str, "{enname}", repSpecialChar($enname)); }
            $str = replaceStr($str, "{typeid}", $typeid);
            $str = replaceStr($str, "{typename}", $typename);
            $str = replaceStr($str, "{typeenname}", $typeenname);
            if (strpos(",".$str,"{md5}")>0){ $str = replaceStr($str, "{md5}", md5($id)); }
            if (strpos(",".$str,"{year}")>0){ $str = replaceStr($str, "{year}", getDatet("Y",$addtime) ); }
            if (strpos(",".$str,"{month}")>0){ $str = replaceStr($str, "{month}", getDatet("m",$addtime) ); }
            if (strpos(",".$str,"{day}")>0){ $str = replaceStr($str, "{day}", getDatet("d",$addtime) ); }
            
            if (app_vodplayviewtype == 4){
                $str = $str . "-" . $sort . "-" . $num . "." . app_vodsuffix;
            }
            else if (app_vodplayviewtype == 5){
                $str = $str . "-" . $sort . "-1." . app_vodsuffix  . "?" . $id . "-" . $sort . "-" . $num;
            }
            else if (app_vodplayviewtype == 6){
                $str = "play." . app_vodsuffix . "?" . $id . "-" . $sort . "-" . $num;
            }
            else{
                $str = $str . "." . app_vodsuffix . "?" . $id . "-" . $sort . "-" . $num;
            }
            $str = replaceStr($str,"index.".app_vodsuffix,"");
            break;
        default:
            $str = "vodplay/?" . $id . "-" . $sort . "-" . $num . "." . app_vodsuffix;
        	break;
        }
        return $strJSstart . app_installdir . $str . $strJSend;
    }
    
    function getVodDownUrl($id, $name, $enname,$addtime, $typeid, $typename, $typeenname, $sort, $num)
    {
        switch(app_voddownviewtype)
        {
        case 1:
            $str = "voddown/index.php?id=" . $id . "&sort=" . $sort . "&num=" . $num;
        	break;
        case 2:
            $str = $str = replaceStr(app_voddownpath, "{id}", $id) . "-" . $sort . "-" . $num . "." . app_vodsuffix;
        	break;
        case 3:
        case 4:
        case 5:
        case 6:
            $str = replaceStr(app_voddownpath, "{id}", $id);
            if (strpos(",".$str,"{name}")>0){ $str = replaceStr($str, "{name}", repSpecialChar($name)); }
            if (strpos(",".$str,"{enname}")>0){ $str = replaceStr($str, "{enname}", repSpecialChar($enname)); }
            $str = replaceStr($str, "{typeid}", $typeid);
            $str = replaceStr($str, "{typename}", $typename);
            $str = replaceStr($str, "{typeenname}", $typeenname);
            if (strpos(",".$str,"{md5}")>0){ $str = replaceStr($str, "{md5}", md5($id)); }
            if (strpos(",".$str,"{year}")>0){ $str = replaceStr($str, "{year}", getDatet("Y",$addtime) ); }
            if (strpos(",".$str,"{month}")>0){ $str = replaceStr($str, "{month}", getDatet("m",$addtime) ); }
            if (strpos(",".$str,"{day}")>0){ $str = replaceStr($str, "{day}", getDatet("d",$addtime) ); }
            
            if (app_voddownviewtype == 4){
                $str = $str . "-" . $sort . "-" . $num . "." . app_vodsuffix;
            }
            else if (app_voddownviewtype == 5){
                $str = $str . "-" . $sort . "-1." . app_vodsuffix  . "?" . $id . "-" . $sort . "-" . $num;
            }
            else if (app_voddownviewtype == 6){
                $str = "down." . app_vodsuffix . "?" . $id . "-" . $sort . "-" . $num;
            }
            else{
                $str = $str . "." . app_vodsuffix . "?" . $id . "-" . $sort . "-" . $num;
            }
            $str = replaceStr($str,"index.".app_vodsuffix,"");
            break;
        default:
            $str = "voddown/?" . $id . "-" . $sort . "-" . $num . "." . app_vodsuffix;
        	break;
        }
        return app_installdir . $str;
    }
    
    function getTopicIndexLink($flag, $suffix)
    {
        if ($flag == "art"){
            $viewtype = app_arttopicviewtype; $htmlsuffix = app_artsuffix;
        }
        else{
            $viewtype = app_vodtopicviewtype; $htmlsuffix = app_vodsuffix;
        }
        
        switch($viewtype)
        {
        case 1:
            $str = "topicindex/index.php";
        	break;
        case 2:
            $str = "topicindex/index";
            if ($suffix) { $str = $str . "." . $htmlsuffix;}
            break;
        case 3:
            $str = "topicindex/index";
            if ($suffix) { $str = $str . "." . $htmlsuffix;}
            break;
        default:
            $str = "topicindex/" ;
        	break;
        }
        return app_installdir . $flag . $str;
    }
    
    function getTopicPageSuffix($flag, $pagenum)
    {
    	global $mac;
        if ($flag == "art"){
            $viewtype = app_arttopicviewtype; $htmlsuffix = app_artsuffix;
        }
        else{
            $viewtype = app_vodtopicviewtype; $htmlsuffix = app_vodsuffix;
        }
        switch($viewtype)
        {
            case 0:
                $str = "?" . $pagenum;
                if ($mac["listorder"]) { $str = $str . "-" . $mac["order"] . "-" . $mac["by"];}
                $str = $str . "." . $htmlsuffix;
                break;
            case 1:
                $str = "&page=" . $pagenum;
                if ($mac["listorder"]) { $str = $str . "order=" . $mac["order"] . "&by=" . $mac["by"];}
                break;
            default:
                if ($pagenum > 1) { $str = "-" . $pagenum;} else{ $str = "";}
                $str = $str . "." . $htmlsuffix;
                break;
        }
        return $str;
    }
    
    function getTypePageSuffix($flag,$viewtype, $pagenum)
    {
    	global $mac;
        if ($flag == "art"){
            $htmlsuffix = app_artsuffix;
        }
        else{
            $htmlsuffix = app_vodsuffix;
        }
        switch($viewtype)
        {
            case 0:
            case 3:
                $str = "-" . $pagenum;
                if ($mac["listorder"]){ $str = $str ."-". urlencode($mac["area"]) ."-". $mac["year"] ."-". $mac["order"] ."-". $mac["by"];}
                $str = $str . "." . $htmlsuffix;
                break;
            case 1:
                $str = "&page=" . $pagenum;
                if ($mac["listorder"]){ $str = $str ."area=". urlencode($mac["area"]) ."&year=". $mac["year"] ."order=" . $mac["order"] . "&by=" . $mac["by"];}
                break;
            default:
                if ($pagenum > 1){ $str = "-" . $pagenum;} else{ $str = "";}
                
                $str = $str . "." . $htmlsuffix;
                break;
        }
        return $str;
    }
    
    function getPageSuffix($flag,$viewtype, $pagenum)
    {
    	global $mac;
        if ($flag == "art"){
            $htmlsuffix = app_artsuffix;
        }
        else{
            $htmlsuffix = app_vodsuffix;
        }
        switch($viewtype)
        {
            case 0:
            case 3:
                $str = "-" . $pagenum;
                if ($mac["listorder"]){ $str = $str . "-" . $mac["order"] . "-" . $mac["by"];}
                $str = $str . "." . $htmlsuffix;
                break;
            case 1:
                $str = "&page=" . $pagenum;
                if ($mac["listorder"]){ $str = $str . "order=" . $mac["order"] . "&by=" . $mac["by"];}
                break;
            default:
                if ($pagenum > 1){ $str = "-" . $pagenum;} else{ $str = "";}
                
                $str = $str . "." . $htmlsuffix;
                break;
        }
        return $str;
    }
    
    function getPageLink($pagenum)
    {
    	global $mac;
        switch( $this->page_type )
        {
            case "vodtype":
                $str= $this->getVodTypeLink($this->page_id, $this->page_name, $this->page_enname, false) . $this->getTypePageSuffix("vod", app_vodlistviewtype,$pagenum);
            	break;
            case "vodtopic":
                $str= $this->getVodTopicLink($this->page_id, $this->page_name, $this->page_enname, false) . $this->getPageSuffix("vod", app_vodtopicviewtype,$pagenum);
            	break;
            case "arttype":
                $str= $this->getArtTypeLink($this->page_id, $this->page_name, $this->page_enname, false) . $this->getPageSuffix("art", app_artlistviewtype,$pagenum);
            	break;
            case "arttopic":
                $str= $this->getArtTopicLink($this->page_id, $this->page_name, $this->page_enname, false) . $this->getPageSuffix("art", app_arttopicviewtype,$pagenum);
            	break;
            case "vodtopicindex":
                $str= $this->getTopicIndexLink("vod", false) . $this->getTopicPageSuffix("vod", $pagenum);
            	break;
            case "arttopicindex":
                $str= $this->getTopicIndexLink("art", false) . $this->getTopicPageSuffix("art", $pagenum);
            	break;
            case "vodsearch":
                $str= app_installdir . "search.php?page=" . $pagenum . "&searchtype=" . $mac["flag"] . "&keyword=" . urlencode($mac["keyword"]) . "&ids=" . $mac["ids"] . "&pinyin=" . $mac["pinyin"] . "&starring=" . urlencode($mac["starring"]) . "&directed=" . urlencode($mac["directed"]) . "&area=" . urlencode($mac["area"]) . "&language=" . urlencode($mac["language"]) . "&year=" . $mac["year"] . "&letter=". $mac["letter"]  . "&type=". $mac["type"] . "&order=" . $mac["order"] . "&by=" . $mac["by"];
            	break;
            case "artsearch":
                $str = app_installdir . "search.php?page=" . $pagenum . "&searchtype=" . $mac["flag"] . "&keyword=" .urlencode($mac["keyword"]) ."&ids=" . $mac["ids"]  . "&letter=". $mac["letter"]  . "&type=". $mac["type"] . "&order=" . $mac["order"] . "&by=" . $mac["by"];
            	break;
            case "label":
            	$arr = explode(".",$this->par_label);
            	if (count($arr) >0){ $fname=$arr[0]; $suffix = $arr[1];} else { $fname=$arr[0] ; $suffix="html";}
            	unset($arr);
            	if ($pagenum > 1){ $suffix = $pagenum . "." .$suffix;} else { $suffix = "." . $suffix;}
            	$str = app_installdir . replaceStr($fname,'$$','/') . $suffix;
            	break;
            case "art":
            	$str = $this->getArtLink($this->page_id, $this->page_name, $this->page_enname,$this->page_addtime,$this->page_typearr["t_id"],$this->page_typearr["t_name"],$this->page_typearr["t_enname"],false) . $this->getPageSuffix("art", app_artcontentviewtype,$pagenum);
            	break;
            default:
                $str= "?page=" .$pagenum; break;
        }
        unset($arr);
        return $str;
    }
    
    function getPageListSizeByCache($strtype,$flag)
    {
        $appName = $strtype . "_" . $this->page_id . "_pagelistsize";
        if (chkCache($flag.$appName)){
            $tempSize = getCache($flag.$appName);
        }
        else{
            $labelRule = "\{maccms:".$strtype."list[\s\S]+?num=([\d]+)[\s\S]*\}";
		    $labelRule = buildregx($labelRule,"is");
		    preg_match_all($labelRule,$this->html,$arr);
			for($i=0;$i<count($arr[1]);$i++)
			{
				$tempSize=$arr[1][$i];
				break;
			}
			if(!isNum($tempSize)) { $tempSize=10; }
			setCache($flag.$appName,$tempSize,0);
        }
        return $tempSize;
    }
    
    function getParam($regx)
    {
        $this->par_type=""; $this->par_by=""; $this->par_order=""; $this->par_parent=""; $this->par_num="";
        $this->par_table=""; $this->par_start=""; $this->par_topic=""; $this->par_level="";$this->par_day="";
        $this->par_state=""; $this->par_area=""; $this->par_lang=""; $this->par_letter="";$this->par_tag="";
        $this->par_label=""; $this->par_maxpage=""; $this->par_starring=""; $this->par_from="";
        $this->par_year=""; $this->par_end=""; $this->par_id="";
        
        for($i=0;$i< count($regx[0]);$i++)
		{
            $parname = $regx[1][$i];
            $parval = $regx[2][$i];
            
            switch($parname)
            {
            	case "id": $this->par_id = $parval;break;
                case "type": $this->par_type = $parval;break;
                case "by": $this->par_by = $parval;break;
                case "order": $this->par_order = $parval;break;
                case "parent": $this->par_parent = $parval;break;
                case "num": $this->par_num = $parval;break;
                case "table": $this->par_table = $parval;break;
                case "start": $this->par_start = $parval;break;
                case "topic": $this->par_topic = $parval;break;
                case "level": $this->par_level = $parval;break;
                case "day": $this->par_day = $parval;break;
                case "state" : $this->par_state = $parval;break;
                case "area" : $this->par_area = $parval;break;
                case "lang" : $this->par_lang = $parval;break;
                case "letter": $this->par_letter = $parval;break;
                case "tag": $this->par_tag = $parval;break;
                case "label": $this->par_label = $parval;break;
                case "maxpage": $this->par_maxpage = $parval;break;
                case "starring": $this->par_starring = $parval;break;
                case "from": $this->par_from = $parval;break;
                case "year": $this->par_year = $parval;break;
                case "end": $this->par_end = $parval;break;
            }
        }
        
        switch($this->par_order)
        {
            case "desc": $this->par_order = "desc";break;
            default: $this->par_order = "asc";break;
        }
        if(isN($this->par_table)) { $this->par_table = "vod";}
        if(isN($this->par_type)) { $this->par_type = "all";}
        unset($regx);
    }
    
    function run()
    {
        global $mac;
        if ( $mac["curviewtype"] < 2){
            $this->html = replaceStr($this->html, "{maccms:runtime}", getRunTime() );
        }
        else{
            $this->html = replaceStr($this->html, "{maccms:runtime}", "");
        }
    }
    
    function base()
    {
    	global $mac;
    	
        $this->html = replaceStr($this->html, "{maccms:url}", app_siteurl);
        $this->html = replaceStr($this->html, "{maccms:path}", app_installdir);
        $this->html = replaceStr($this->html, "{maccms:templatepath}", app_installdir . "template/" . app_templatedir . "/");
        $this->html = replaceStr($this->html, "{maccms:name}", app_sitename);
        $this->html = replaceStr($this->html, "{maccms:keywords}", app_keywords);
        $this->html = replaceStr($this->html, "{maccms:description}", app_description);
        $this->html = replaceStr($this->html, "{maccms:icp}", app_icp);
        $this->html = replaceStr($this->html, "{maccms:qq}", app_qq);
        $this->html = replaceStr($this->html, "{maccms:email}", app_email);
        
        $this->html = replaceStr($this->html, "{maccms:appid}", $mac["appid"]);
        $this->html = replaceStr($this->html, "{maccms:curvodtypeid}", $mac["vodtypeid"]);
        $this->html = replaceStr($this->html, "{maccms:curvodtypepid}", $mac["vodtypepid"]);
        $this->html = replaceStr($this->html, "{maccms:curvodtopicid}", $mac["vodtopicid"]);
        $this->html = replaceStr($this->html, "{maccms:curarttypeid}", $mac["arttypeid"]);
        $this->html = replaceStr($this->html, "{maccms:curarttypepid}", $mac["arttypepid"]);
        $this->html = replaceStr($this->html, "{maccms:curarttopicid}", $mac["arttopicid"]);
        
        
        $this->html = replaceStr($this->html, "{maccms:userid}", $_SESSION["userid"]);
        $this->html = replaceStr($this->html, "{maccms:username}", $_SESSION["username"]);
        $this->html = replaceStr($this->html, "{maccms:usergroupid}", $_SESSION["usergroup"]);
        
        $this->html = replaceStr($this->html, "{maccms:desktop}", "<a href=\"javascript:void(0)\" onclick=\"desktop('');return false;\"/>保存到桌面</a>");
        
        $this->html = replaceStr($this->html, "{maccms:gbooklink}", app_installdir . "gbook.php" );
        $this->html = replaceStr($this->html, "{maccms:searchlink}", app_installdir . "search.php" );
        $this->html = replaceStr($this->html, "{maccms:userlink}", app_installdir . "user/login.php" );
        
        $this->html = replaceStr($this->html, "{maccms:indexlink}", $this->getIndexLink());
        $this->html = replaceStr($this->html, "{maccms:artindexlink}", $this->getArtIndexLink());
        $this->html = replaceStr($this->html, "{maccms:vodmaplink}", $this->getMapLink());
        $this->html = replaceStr($this->html, "{maccms:artmaplink}", $this->getArtMapLink());
        $this->html = replaceStr($this->html, "{maccms:vodtopiclink}", $this->getTopicIndexLink("vod", true));
        $this->html = replaceStr($this->html, "{maccms:arttopiclink}", $this->getTopicIndexLink("art", true));
        $this->html = replaceStr($this->html, "{maccms:visits}", "<script src=\"" . app_installdir . "js/tj.js\"></script>");
        
        if (indexOf($this->html, "{maccms:vodallcount}")) { $this->html = replaceStr($this->html, "{maccms:vodallcount}", getVodCount("all"));}
        if (indexOf($this->html, "{maccms:voddaycount}")) { $this->html = replaceStr($this->html, "{maccms:voddaycount}", getVodCount("day"));}
        if (indexOf($this->html, "{maccms:artallcount}")) { $this->html = replaceStr($this->html, "{maccms:artallcount}", getArtCount("all"));}
        if (indexOf($this->html, "{maccms:artdaycount}")) { $this->html = replaceStr($this->html, "{maccms:artdaycount}", getArtCount("day"));}
        if (indexOf($this->html, "{maccms:usercount}")) { $this->html = replaceStr($this->html, "{maccms:usercount}", getUserCount("all"));}
        if (indexOf($this->html, "{maccms:userdaycount}")) { $this->html = replaceStr($this->html, "{maccms:userdaycount}", getUserCount("day"));}
        
    }
    
    function headfoot()
    {
        $this->html = replaceStr($this->html, "{maccms:head}", getFileByCache("template_head",root. "template/" . app_templatedir . "/" . app_htmldir . "/head.html"));
        $this->html = replaceStr($this->html, "{maccms:foot}", getFileByCache("template_foot",root. "template/" . app_templatedir . "/" . app_htmldir . "/foot.html"));
    }
    
    function mark()
    {
        $this->headfoot();
        $this->labels();
        $this->base();
        $this->typematrix();
       	
        $labelRule = buildregx('{maccms:([\S]+)\s+(.*?)}([\s\S]+?){/maccms:\1}',"");
		preg_match_all($labelRule ,$this->html,$matches1);
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
            $markname = $matches1[1][$i];
            $markpar = $matches1[2][$i];
            $this->markdes = $matches1[3][$i];
            $this->markval = $matches1[0][$i];
            
            $labelRule = buildregx("([a-z0-9]+)=([\x{4e00}-\x{9fa5}|a-zA-Z0-9|,]+)","") . "u";
            preg_match_all($labelRule,$markpar,$matches2);
            $this->getParam ($matches2);
            switch($markname)
            {
                case "menulist": $this->menulist();break;
                case "artlist": $this->artlist();break;
                case "vodlist": $this->vodlist();break;
                case "topiclist": $this->topiclist();break;
                case "linklist": $this->linklist();break;
                case "arealist": $this->arealist();break;
                case "languagelist": $this->languagelist();break;
                case "yearlist": $this->yearlist();break;
            }
            unset($matches2);
    	}
        
        unset($matches1);
    }
    
    function ifEx()
    {
        if (!strpos(",".$this->html,"{if:")) { return; }
		$labelRule = buildregx("{if:([\s\S]+?)}([\s\S]*?){end\s+if}","is");
		$labelRule2="{elseif";
		$labelRule3="{else}";
		preg_match_all($labelRule,$this->html,$iar);
		$arlen=count($iar[1]);
		for($m=0;$m<$arlen;$m++){
			$strif= asp2phpif( $iar[1][$m] ) ;
			$strThen= $iar[2][$m];
			//$strThen= replaceStr($strThen,"'","\'");
			$elseifFlag=false;
			
			if (strpos(",".$strThen,$labelRule2)>0){
				$elseifArray=explode($labelRule2,$strThen);
				$elseifArrayLen=count($elseifArray);
				$elseifSubArray=explode($labelRule3,$elseifArray[$elseifArrayLen-1]);
				$resultStr=$elseifSubArray[1];
				eval("if($strif){\$resultStr='$elseifArray[0]';\$elseifFlag=true;}");
				if(!$elseifFlag){
					for($elseifLen=1;$elseifLen<$elseifArrayLen-1;$elseifLen++){
						$strElseif=getSubStrByFromAndEnd($elseifArray[$elseifLen],":","}","");
						$strElseif=asp2phpif($strElseif);
						$strElseifThen=getSubStrByFromAndEnd($elseifArray[$elseifLen],"}","","start");
						$strElseifThen=replaceStr($strElseifThen,"'","\'");
						eval("if($strElseif){\$resultStr='$strElseifThen'; \$elseifFlag=true;}");
						if ($elseifFlag) {break;}
					}
				}
				if(!$elseifFlag){
					$strElseif0=getSubStrByFromAndEnd($elseifSubArray[0],":","}","");
					$strElseif0=asp2phpif($strElseif0);
					$strElseifThen0=getSubStrByFromAndEnd($elseifSubArray[0],"}","","start");
					$strElseifThen0=replaceStr($strElseifThen0,"'","\'");
					eval("if($strElseif0){\$resultStr='$strElseifThen0';\$elseifFlag=true;}");
				}
				$this->html=str_replace($iar[0][$m],$resultStr,$this->html);
			}
			else{
				$ifFlag = false;
				if (strpos(",".$strThen,"{else}")>0){
					$elsearray=explode($labelRule3,$strThen);
					$strThen1=$elsearray[0];
					$strElse1=$elsearray[1];
					eval("if($strif){\$ifFlag=true;}else{\$ifFlag=false;}");
					if ($ifFlag){ $this->html=str_replace($iar[0][$m],$strThen1,$this->html);} else {$this->html=str_replace($iar[0][$m],$strElse1,$this->html);}
				}
				else{
					eval("if($strif){\$ifFlag=true;}else{\$ifFlag=false;}");
					if ($ifFlag){ $this->html=str_replace($iar[0][$m],$strThen,$this->html);} else {$this->html=str_replace($iar[0][$m],"",$this->html);}
				 }
			}
		}
		unset($elsearray);
		unset($elseifArray);
		unset($iar);
    }
	
    function labels()
    {	
    	$labelRule = buildregx("{label:([\s\S]*?)}","");
		preg_match_all($labelRule ,$this->html,$matches1);
		
		for($i=0;$i<count($matches1[0]);$i++)
		{
            $markpar = $matches1[1][$i];
            $labelhtml = getFileByCache("label_".$markpar, root . "template/" . app_templatedir . "/" . app_htmldir . "/label_" . $markpar);
            $this->html = replaceStr($this->html,  $matches1[0][$i], $labelhtml);
        }
        unset($matches1);
    }
    
    function typematrix()
    {
    	global $db;
    	$labelRule = buildregx("{maccms:typematrix([\s\S]*?)}([\s\S]*?){/maccms:typematrix}","");
		preg_match_all($labelRule ,$this->html,$matches1);
		
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
            $markpar = $matches1[1][$i];
            $this->markdes = $matches1[2][$i];
            $this->markval = $matches1[0][$i];
            $labelRule = buildregx("([a-z0-9]+)=([\x{4e00}-\x{9fa5}|a-z0-9|,]+)","")  . "u";
            preg_match_all($labelRule,$markpar,$matches2);
            $this->getParam($matches2);
            
            if($this->par_type=="letter"){
            	if (isN($this->par_letter) || $this->par_letter=="all"){
            		$this->par_letter = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
            	}
            	$matches2 = explode(",",$this->par_letter);
            	for ($j=0;$j<count($matches2);$j++){
            		if ($matches2[$j]!=""){
            			$marktemp = $this->markdes;
            			$marktemp = replaceStr($marktemp,"[typematrix:num]",$j+1);
						$marktemp = replaceStr($marktemp,"[typematrix:name]",$matches2[$j]);
						$marktemp = replaceStr($marktemp,"[typematrix:link]", app_installdir . "search.php?letter=". $matches2[$j] );
						$markhtml = $markhtml . $marktemp;
            		}
            	}
            }
            else if($this->par_type=="tag"){
            	if(!isN($this->par_tag)){
            		$matches2 = explode(",",$this->par_tag);
	            	for ($j=0;$j<count($matches2);$j++){
	            		if ($matches2[$j]!=""){
	            			$marktemp = $this->markdes;
	            			$marktemp = replaceStr($marktemp,"[typematrix:num]",$j+1);
							$marktemp = replaceStr($marktemp,"[typematrix:name]",$matches2[$j]);
							$marktemp = replaceStr($marktemp,"[typematrix:link]", app_installdir . "search.php?keyword=". urlencode($matches2[$j]) );
							$markhtml = $markhtml . $marktemp;
	            		}
	            	}
            	}
            }
            else{
            	
	            $sql = "SELECT t_id,t_name,t_enname,t_pid,t_key,t_des FROM {pre}" . $this->par_table . "_type WHERE t_hide=0 ";
				$where="";
				$orderstr="";
				$markhtml="";
	            if ($this->par_type == "all"){
	        	}
		        else if ($this->par_type== "parent"){
		        	$where = $where . " AND t_pid =0 ";
		        }
		        else if ($this->par_type== "child"){
		        	$where = $where . " AND t_pid >0 ";
		        }
		        else if (!isN($this->par_type)){ 
		        	$where = $where . " AND t_id IN(" . $this->par_type . ") ";
		        }
		        
		        if ($this->par_parent=="all"){
	        		$where = $where . " AND t_pid =0 ";
	        	}
		        else if (!isN($this->par_parent)){
		        	$where = $where . " AND t_pid IN(" . $this->par_parent . ") ";
		        }
		        if ($this->par_table=="vod" && app_user==1){
					$where = $where . getTypeByPopedomFilter("menu");
				}
	            switch($this->par_by)
	            {
	                case "id": $orderstr = " ORDER BY t_id " .$this->par_order;break;
	                default: $orderstr = " ORDER BY t_sort " . $this->par_order;break;
	            }
            	
	            $rs = $db->query($sql . $where . $orderstr);
				if ($rs){
					$rscount=$db->num_rows($rs);
				}
				else{
					$rscount=0;
					$this->html = replaceStr($this->html,$this->markval,"typematrix标签出错:");
					return;
				}
	            
	            $labelRule = buildregx("\[typematrix:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				$num=0;
				while ($row = $db ->fetch_array($rs))
		        {
		        	$num = $num + 1;
		        	$marktemp = $this->markdes;
		        	for($j=0;$j<count($matches2[0]);$j++)
					{
						$marktemp = $this->parse("type", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], $row, $num);
					}
		        	$markhtml = $markhtml . $marktemp;
		        }
		        unset($rs);
		    }
	        $this->html = replaceStr($this->html, $this->markval, $markhtml);
	        unset($matches2);
        }
        unset($matches1);
    }
    
    function menulist()
    {
    	global $db,$mac;
    	
    	if (isN($this->par_start)) { $this->par_start = 0;} else {$this->par_start = intval($this->par_start);}
    	if ($this->par_start>0){ $this->par_start--; }
        if (!isNum($this->par_num)){ $topstr = ""; $this->startnum = $this->par_start;} else { $par_num = intval($this->par_num); $this->startnum = $this->par_start; $topstr = " limit 0," . ($this->par_num + $this->startnum); }
        
        $sql = "SELECT t_id,t_name,t_enname,t_pid,t_key,t_des FROM {pre}" . $this->par_table . "_type WHERE t_hide=0 ";
        
        if ($this->par_type == "all"){
        }
        else if ($this->par_type== "parent"){
        	$where = $where . " AND t_pid =0 ";
        }
        else if ($this->par_type== "child"){
        	$where = $where . " AND t_pid >0 ";
        }
        else if ($this->par_type== "auto"){
        	if($mac["vodtypeid"]==-1){
        		$where = $where . " AND t_pid =0 ";
        	}
        	else if($mac["vodtypepid"]==0){
        		$where = $where . " AND t_pid = " . $mac["vodtypeid"];
        	}
        	else if($mac["vodtypepid"]>0){
        		$where = $where . " AND t_pid = " . $mac["vodtypepid"];
        	}
        }
        else if (!isN($this->par_type)){ 
        	$where = $where . " AND t_id IN(" . $this->par_type . ") ";
        }
        
        if ($this->par_parent=="all"){
        	$where = $where . " AND t_pid =0 ";
        }
        else if (!isN($this->par_parent)){
        	$where = $where . " AND t_pid IN(" . $this->par_parent . ") ";
        }
        
        if ($this->par_table=="vod" && app_user==1){
			$where = $where . getTypeByPopedomFilter("menu");
		}
        
        switch($this->par_by)
        {
            case "id": $orderstr = " ORDER BY t_id " . $this->par_order;break;
            default: $orderstr = " ORDER BY t_sort " . $this->par_order;break;
        }
		
        $rs = $db->query($sql . $where . $orderstr . $topstr);
		if ($rs){
			$rscount=$db->num_rows($rs);
		}
		else{
			$rscount=0;
			$this->html = replaceStr($this->html,$this->markval,"menulist标签出错:");
			return;
		}
		
        $labelRule = buildregx("\[menulist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->markdes,$matches2);
		$num=0;
        while ($row = $db ->fetch_array($rs))
	    {
	    	if($i>= intval($this->startnum)){
				$num = $num + 1;
				$marktemp = $this->markdes;
		        
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("type", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
			$i = $i+1;
		}
        unset($rs);
	    unset($matches2);
	    $this->html = replaceStr($this->html, $this->markval, $markhtml);
    }
    
    function artlist()
    {
    	global $db,$mac,$cache;
    	if (indexOf($this->markval, "[artlist:content")) { $field_des = "a_content";} else {$field_des = "0";}
    	if (isN($this->par_start)){ $this->par_start = 0;} else{ $this->par_start = intval($this->par_start); }
        if (!isNum($this->par_num)) { $this->par_num = 12;} else { $this->par_num = intval($this->par_num); }
        if ($this->par_start>0){ $this->par_start--; }
        $this->startnum = $this->par_start; $topstr = " limit 0,". ($this->par_num + $this->startnum) ; 
        
        $sql = "SELECT  a_id,a_title,a_subtitle,a_entitle,a_from,a_type,a_pic,a_author,a_color,a_hits,a_dayhits,a_weekhits,a_monthhits,a_addtime,a_time,a_level,".$field_des." FROM {pre}art WHERE a_hide=0 ";
        
        if (!isN($this->par_level)){
            if ($this->par_level != "all"){
                $where = $where . " and a_level in(" . $this->par_level . ")";
            }
            else{
                $where = $where . " and a_level >0";
        	}
        }
        
        $where = $where . " AND a_type>0 ";
        if (!isN($this->par_type)){
	        if ($this->par_type != "all"){
	            if ($this->par_type == "current" && $mac["arttypeid"] > -1){
	                $typearr = getValueByArray($cache[1], "t_id" ,$mac["arttypeid"] );
	                $where = $where . " and a_type in (" . $typearr["childids"] .")";
	            }
	            else{
	                if ( strpos ($this->par_type,",")>0){
						$where = $where . " and a_type in (" . $this->par_type. ")";
					}
					else{
						$typearr = getValueByArray($cache[1], "t_id" , intval($this->par_type) );
						if(is_array($typearr)){
							$where = $where . " and a_type in (" . $typearr["childids"] . ")";
						}
					}
					
	            }
	        }
        }
        if (isNum($this->par_day)){
			$where = $where ." and datediff(now(), a_time) <".$this->par_day;
		}
        if (!isN($this->par_topic)){
	        if ($this->par_topic != "all"){
	            if ($this->par_topic == "current" && $mac["arttopicid"] > -1){
	                $where = $where . " and a_topic in (" . $mac["arttopicid"] . ")";
	            }
		        else{
	                $where = $where . " and a_topic in (" .$this->par_topic . ")";
	            }
		    }
        }
        switch($this->par_by)
        {
            case "id": $orderstr = " ORDER BY a_id " . $this->par_order;break;
            case "level": $orderstr = " ORDER BY a_level " . $this->par_order;break;
            case "hits": $orderstr = " ORDER BY a_hits " . $this->par_order;break;
            case "dayhits": $orderstr = " ORDER BY a_dayhits " . $this->par_order;break;
            case "weekhits": $orderstr = " ORDER BY a_weekhits " . $this->par_order;break;
            case "monthhits": $orderstr = " ORDER BY a_monthhits " . $this->par_order;break;
            case "addtime": $orderstr = " ORDER BY a_addtime " . $this->par_order;break;
            case "rnd": 
            	$orderstr = " ORDER BY a_id asc " ;
            	$sqlr = " {pre}art AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(a_id) as id FROM `{pre}art`)-(SELECT MIN(a_id) as id FROM `{pre}art`))+(SELECT MIN(a_id) as id FROM `{pre}art`)) AS id) AS t2  ";
            	$sql = replaceStr($sql,"{pre}art",$sqlr);
            	$where .= " and t1.a_id >= t2.id  " ;
            	break;
            default: $orderstr = " ORDER BY a_time " . $this->par_order;break;
        }
        
        $rs = $db->query($sql . $where . $orderstr . $topstr );
		if ($rs){
			$rscount=$db->num_rows($rs);
		}
		else{
			$rscount=0;
			$this->html = replaceStr($this->html,$this->markval,"artlist标签出错");
			return;
		}
        
        $labelRule = buildregx("\[artlist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->markdes,$matches2);
		$num=0;
        while ($row = $db ->fetch_array($rs))
	    {
	    	if($i>= intval($this->startnum)){
				$num = $num + 1;
				$marktemp = $this->markdes;
		        
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("art", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
			$i = $i+1;
		}
        unset($rs);
	    unset($matches2);
	    $this->html = replaceStr($this->html, $this->markval, $markhtml);
    }
    
    function artpagelist()
    {
    	global $db,$mac;
    	
        if (indexOf($this->html, "[pagelist:content")){ $field_des = "a_content";} else{ $field_des = "0"; }
        $labelRule = buildregx("{maccms:artpagelist([\s\S]*?)}([\s\S]*?){/maccms:artpagelist}","");
		preg_match_all($labelRule ,$this->html,$matches1);
		if (isN($this->page)){ $this->page = $mac["page"]; }
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
            $markpar = $matches1[1][$i];
            $this->markdes = $matches1[2][$i];
            $this->markval = $matches1[0][$i];
            $labelRule = buildregx("([a-z0-9]+)=([a-z0-9|,]+)","");
            preg_match_all($labelRule,$markpar,$matches2);
            $this->getParam($matches2);
            
            $sql = "SELECT a_id,a_title,a_subtitle,a_entitle,a_from,a_type,a_pic,a_author,a_color,a_hits,a_dayhits,a_weekhits,a_monthhits,a_addtime,a_time,a_level,".$field_des ." FROM {pre}art WHERE a_hide=0 ";
            if (!isNum($this->par_num)){ $this->par_num = 12;} else { $this->par_num = intval($this->par_num); }
            
            if (isNum($this->par_day)){
				$where = $where ." and datediff(now(), a_time) <".$this->par_day;
			}
			
			if (!isN($this->par_level)){
	            if ($this->par_level != "all"){
	                $where = $where . " and a_level in(" . $this->par_level . ")";
	            }
	            else{
	                $where = $where . " and a_level >0";
	        	}
	        }
	        
			if (!isN($this->par_topic)){
				if ($this->par_topic != "all"){
		            if ($this->par_topic == "current" && $mac["arttopicid"] > -1){
		                $where = $where . " and a_topic in (" . $mac["arttopicid"] . ")";
		        	}
		            else{
		                $where = $where . " and a_topic in (" . $this->par_topic . ")";
		            }
	            }
	        }
	        
            if ($mac["arttypeid"] != -1){
                $this->page_id = $mac["arttypeid"];
                $where = $where . " AND a_type IN (" . $this->page_typearr["childids"] . ")";
                $this->page_type = "arttype";
            }
            else if ($mac["arttopicid"] != -1){
                $this->page_id = $mac["arttopicid"];
                $where = $where . " AND a_topic IN (" . $mac["arttopicid"] . ")";
                $this->page_type = "arttopic";
            }
            else if (!isN($mac["ids"])){
            	$s = explode(',',$mac['ids']);
		        $lp['ids'] = '';
		        foreach($s as $a){
		        	$tmp[] = intval($a);
		        }
		        $mac['ids'] = join(',',$tmp);
		        
                $where = $where . " AND a_id IN( " . $mac["ids"] . " )";
                $this->page_type = "artsearch";
            }
            else{
                $where = $mac["where"];
                if (!isN($mac["des"])){ $this->page_type = "artsearch"; $this->par_type=""; }
            }
            
            switch($this->par_by)
            {
                case "id": $orderstr = " ORDER BY a_id " . $this->par_order;break;
                case "level": $orderstr = " ORDER BY a_level " . $this->par_order;break;
                case "hits": $orderstr = " ORDER BY a_hits " . $this->par_order;break;
                case "dayhits": $orderstr = " ORDER BY a_dayhits " . $this->par_order;break;
                case "weekhits": $orderstr = " ORDER BY a_weekhits " . $this->par_order;break;
                case "monthhits": $orderstr = " ORDER BY a_monthhits " . $this->par_order;break;
                case "addtime": $orderstr = " ORDER BY a_addtime " . $this->par_order;break;
                default: $orderstr = " ORDER BY a_time " . $this->par_order;break;
            }
            
            $topstr = " limit ".($this->par_num * ($this->page-1)) .",".$this->par_num;
            $rscount=$db->getOne("SELECT COUNT(a_id) FROM {pre}art WHERE a_hide=0 " . $where);
            $this->page_size = $this->par_num;
            $this->page_count = 0;
            $this->data_count = 0;
            
            if ($rscount ==0){
                $this->html = replaceStr($this->html, $this->markval, "<font style='font-size:13px'><div align='center'>没有相关记录！</div></font>");
                return;
            }
            $pagecount=ceil($rscount/$this->par_num);
            $rs = $db->query($sql . $where . $orderstr . $topstr );
			if (!$rs){
				$rscount=0;
				$pagecount=0;
				$this->html = replaceStr($this->html,$this->markval,"artpagelist标签出错");
				return;
			}
            $this->page_count = $pagecount;
            $this->data_count = $rscount;
            
            $labelRule = buildregx("\[pagelist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
			preg_match_all($labelRule ,$this->markdes,$matches2);
			$num=0;
	        while ($row = $db ->fetch_array($rs))
		    {
				$num = $num + 1;
				$marktemp = $this->markdes;
			        
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("art", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
	        unset($rs);
		    unset($matches2);
		    $this->html = replaceStr($this->html, $this->markval, $markhtml);
	    }
	    unset($matches1);
    }
    
    function vodlist()
    {
    	global $db,$mac,$cache;
    	if (indexOf($this->markval, "[vodlist:content")) { $field_des = "d_content";} else {$field_des = "0";}
        if (isN($this->par_start)) { $this->par_start = 0;} else {$this->par_start = intval($this->par_start);}
        if (!isNum($this->par_num)){ $this->par_num = 12;} else {$this->par_num = intval($this->par_num);}
        if ($this->par_start>0){ $this->par_start--; }
        $this->startnum = $this->par_start; $topstr = " limit 0,". ($this->par_num + $this->startnum);
        
        $sql = "SELECT d_id,d_name,d_subname,d_enname,d_type,d_letter,d_state,d_color,d_pic,d_picthumb,d_picslide,d_starring,d_directed,d_area,";
        $sql = $sql . "d_year,d_language,d_level,d_stint,d_stintdown,d_hits,d_dayhits,d_weekhits,d_monthhits,d_topic," . $field_des . ",";
        $sql = $sql . "d_remarks,d_good,d_bad,d_score,d_scorecount,d_duration,d_addtime,d_time,d_playfrom,d_downfrom FROM {pre}vod WHERE d_hide=0 ";
        
        if ($this->par_state == "series"){ 
        	$where = $where . " and d_state > 0";
        }
        else if( isNum($this->par_state) ){
        	$where = $where . " and d_state = " . $this->par_state ;
        }
        
        if (!isN($this->par_level)){
            if ($this->par_level != "all"){
                $where = $where . " and d_level in(" . $this->par_level . ")";
            }
            else{
                $where = $where . " and d_level >0";
        	}
        }
        
        $where = $where . " AND d_type>0 ";
        if (!isN($this->par_type)){
	        if ($this->par_type != "all"){
	            if ($this->par_type == "current" && $mac["vodtypeid"] > -1){
	                $typearr = getValueByArray($cache[0], "t_id" , $mac["vodtypeid"]);
	                $where = $where . " and d_type in (" . $typearr["childids"].")";
	            }
				else{
					if ( strpos ($this->par_type,",")>0){
						$where = $where . " and d_type in (" . $this->par_type. ")";
					}
					else{
						$typearr = getValueByArray($cache[0], "t_id" , intval($this->par_type) );
						if(is_array($typearr)){
							$where = $where . " and d_type in (" . $typearr["childids"] . ")";
						}
					}
	            }
	        }
        }
        
        if (!isN($this->par_topic)){
	        if ($this->par_topic != "all"){
	            if ($this->par_topic == "current" && $mac["vodtopicid"] > -1){
	                $where = $where . " and d_topic in (" . $mac["vodtopicid"] . ")";
	            }
		        else{
	                $where = $where . " and d_topic in (" . $this->par_topic . ")";
	            }
	        }
        }
        
        if (!isN($this->par_area)){
        	$where = $where . " and d_area ='" . $this->par_area . "'";
        }
        if (!isN($this->par_lang)){
        	$where = $where . " and d_language ='" . $this->par_lang . "'";
        }
        if(!isN($this->par_letter)){
        	$where = $where . " and d_letter ='" . $this->par_letter . "'";
        }
        if(!isN($this->par_year)){
        	$where = $where . " and d_year ='" . $this->par_year . "'";
        }
        
		if (isNum($this->par_day)){
			$where = $where ." and datediff(now(), d_time) <".$this->par_day;
		}
		
		if (app_vodviewtype!=2 && app_user==1){
			$where = $where . getTypeByPopedomFilter("vod");
		}
        if (!isN($this->par_starring)){
        	$where = $where . " AND d_starring LIKE '%". $this->par_starring ."%' ";
        }
        
        switch($this->par_by)
        {
            case "id": $orderstr = " ORDER BY d_id " . $this->par_order;break;
            case "hits": $orderstr = " ORDER BY d_hits " . $this->par_order;break;
            case "dayhits": $orderstr = " ORDER BY d_dayhits " . $this->par_order;break;
            case "weekhits": $orderstr = " ORDER BY d_weekhits " . $this->par_order;break;
            case "monthhits": $orderstr = " ORDER BY d_monthhits " . $this->par_order;break;
            case "addtime": $orderstr = " ORDER BY d_addtime " . $this->par_order;break;
            case "level": $orderstr = " ORDER BY d_level " . $this->par_order;break;
            case "good": $orderstr = " ORDER BY d_good " . $this->par_order;break;
            case "bad": $orderstr = " ORDER BY d_bad " . $this->par_order;break;
            case "score": $orderstr = " ORDER BY d_score " . $this->par_order;break;
            case "scorecount": $orderstr = " ORDER BY d_scorecount " . $this->par_order;break;
            case "rnd": 
            	$orderstr = " ORDER BY d_id asc " ;
            	$sqlr = " {pre}vod AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(d_id) as id FROM `{pre}vod`)-(SELECT MIN(d_id) as id FROM `{pre}vod`))+(SELECT MIN(d_id) as id FROM `{pre}vod`)) AS id) AS t2  ";
            	$sql = replaceStr($sql,"{pre}vod",$sqlr);
            	$where .= " and t1.d_id >= t2.id  " ;
            	break;
            case "similar":
            	if ($this->par_similar !=""){
            		$where = $where . " AND " . $this->par_similar ;
            	}
            	break;
            default: $orderstr = " ORDER BY d_time " . $this->par_order;break;
        }
        
		$rs = $db->query($sql . $where . $orderstr . $topstr );
		if ($rs){
			$rscount=$db->num_rows($rs);
		}
		else{
			$rscount=0;
			$this->html = replaceStr($this->html,$this->markval,"vodlist标签出错");
			return;
		}
        
        $labelRule = buildregx("\[vodlist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->markdes,$matches2);
		$num=0;
        while ($row = $db ->fetch_array($rs))
	    {
	    	if($i>= intval($this->startnum)){
				$num = $num + 1;
				$marktemp = $this->markdes;
		        
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("vod", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
			$i = $i+1;
		}
        unset($rs);
	    unset($matches2);
	    $this->html = replaceStr($this->html, $this->markval, $markhtml);
    }
    
    function vodpagelist()
    {
    	global $db,$mac,$cache;
    	
        if (indexOf($this->html, "[pagelist:content")){ $field_des = "d_content";} else{ $field_des = "0"; }
        $labelRule = buildregx("{maccms:vodpagelist([\s\S]*?)}([\s\S]*?){/maccms:vodpagelist}","");
		preg_match_all($labelRule ,$this->html,$matches1);
		if (isN($this->page)){ $this->page = $mac["page"]; }
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
			$markhtml="";
            $markpar = $matches1[1][$i];
            $this->markdes = $matches1[2][$i];
            $this->markval = $matches1[0][$i];
            
            $labelRule = buildregx("([a-z0-9]+)=([\x{4e00}-\x{9fa5}|a-z0-9|,|$|.]+)","")  . "u";
            preg_match_all($labelRule,$markpar,$matches2);
            $this->getParam($matches2);
        	
            $sql = "SELECT d_id,d_name,d_subname,d_enname,d_type,d_letter,d_state,d_color,d_pic,d_picthumb,d_picslide,d_starring,d_directed,d_area,";
            $sql = $sql . "d_year,d_language,d_level,d_stint,d_stintdown,d_hits,d_dayhits,d_weekhits,d_monthhits,d_topic," . $field_des . ",";
            $sql = $sql . "d_remarks,d_good,d_bad,d_score,d_scorecount,d_duration,d_addtime,d_time,d_playfrom,d_downfrom  FROM {pre}vod WHERE d_hide=0 ";
            
            if (!isNum($this->par_num)){ $this->par_num = 12;} else { $this->par_num = intval($this->par_num); }
            if (!isN($mac["order"])){ $this->par_order = $mac["order"]; $mac["listorder"] = true; }
            if (!isN($mac["by"])){ $this->par_by = $mac["by"]; $mac["listorder"] = true;}
            if (!isN($mac["area"])){ $this->par_area = $mac["area"]; $mac["listorder"] = true;}
            if (!isN($mac["year"])){ $this->par_year = $mac["year"]; $mac["listorder"] = true;}
            
            if ($this->par_state == "series"){ 
	        	$where = $where . " and d_state > 0";
	        }
	        else if( isNum($this->par_state) ){
	        	$where = $where . " and d_state = " . $this->par_state ;
	        }
	        
            if (!isN($this->par_level)){
                if ($this->par_level != "all"){
                    $where = $where . " AND d_level in(" . $this->par_level . ")";
                }
                else{
                    $where = $where . " AND d_level >0";
            	}
            }
			
			if (!isN($this->par_topic)){
				if ($this->par_topic != "all"){
		            if ($this->par_topic == "current" && $mac["vodtopicid"] > -1){
		                $where = $where . " and d_topic in (" . $mac["vodtopicid"] . ")";
		        	}
		            else{
		                $where = $where . " and d_topic in (" . $this->par_topic . ")";
		            }
	            }
	        }
        	if (!isN($this->par_area)){
	        	$where = $where . " and d_area ='" . $this->par_area . "'";
	        }
	        if (!isN($this->par_lang)){
	        	$where = $where . " and d_language ='" . $this->par_lang . "'";
	        }
	        if(!isN($this->par_letter)){
	        	$where = $where . " and d_letter ='" . $this->par_letter . "'";
	        }
	       	if(!isN($this->par_year)){
	        	$where = $where . " and d_year ='" . $this->par_year . "'";
	        }
        	if (isNum($this->par_day)){
				$where = $where ." and datediff(now(), d_time) <".$this->par_day;
			}
			
            $where = $where . " AND d_type>0 ";
            if ($mac["vodtypeid"] != -1){
                $this->page_id = $mac["vodtypeid"];
                $where = $where . " AND d_type IN (" . $this->page_typearr["childids"] . ")";
                $this->page_type = "vodtype";
            }
            else if ($mac["vodtopicid"] != -1){
                $this->page_id = $mac["vodtopicid"];
                $where = $where . " AND d_topic IN(" . $mac["vodtopicid"] . ")";
                $this->page_type = "vodtopic";
            }
            else if (!isN($mac["ids"])){
                $s = explode(',',$mac['ids']);
		        $lp['ids'] = '';
		        foreach($s as $a){
		        	$tmp[] = intval($a);
		        }
		        $mac['ids'] = join(',',$tmp);
                $where = $where . " AND d_id IN( " . $mac["ids"]. " )";
                $this->page_type = "vodsearch";
            }
            else if(!isN($this->par_label)){
            	$this->page_type = "label";
            }
            else{
                $where = $mac["where"];
                if (!isN($mac["des"])){ $this->page_type = "vodsearch"; $this->par_type=""; }
            }
            
            
            if ($mac["vodtypeid"] == -1 && !isN($this->par_type)){
		        if ($this->par_type != "all"){
					if ( strpos ($this->par_type,",")>0){
						$where = $where . " and d_type in (" . $this->par_type. ")";
					}
					else{
						$typearr = getValueByArray($cache[0], "t_id" , intval($this->par_type) );
						if(is_array($typearr)){
							$where = $where . " and d_type in (" . $typearr["childids"] . ")";
						}
					}
		        }
	        }
            
            if (app_user==1){
				$where = $where . getTypeByPopedomFilter("vod");
			}
            if (!isN($this->par_starring)){
            	$where = $where . " AND d_starring LIKE '%". $this->par_starring ."%' ";
            }
            
            switch($this->par_order)
            {
                case "desc":   $this->par_order = "desc";break;
                default: $this->par_order = "asc";break;
            }
            switch($this->par_by)
            {
                case "id": $orderstr = " ORDER BY d_id " . $this->par_order;break;
                case "hits": $orderstr = " ORDER BY d_hits " . $this->par_order;break;
                case "dayhits": $orderstr = " ORDER BY d_dayhits " . $this->par_order;break;
                case "weekhits": $orderstr = " ORDER BY d_weekhits " . $this->par_order;break;
                case "monthhits": $orderstr = " ORDER BY d_monthhits " . $this->par_order;break;
                case "addtime": $orderstr = " ORDER BY d_addtime " . $this->par_order;break;
                case "level": $orderstr = " ORDER BY d_level " . $this->par_order;break;
                case "good": $orderstr = " ORDER BY d_good " . $this->par_order;break;
                case "bad": $orderstr = " ORDER BY d_bad " . $this->par_order;break;
                case "score": $orderstr = " ORDER BY d_score " . $this->par_order;break;
                case "scorecount": $orderstr = " ORDER BY d_scorecount " . $this->par_order;break;
                default: $orderstr = " ORDER BY d_time " . $this->par_order;break;
            }
            
    		$topstr = " limit ".($this->par_num * ($this->page-1)) .",".$this->par_num;
    		$this->page_size = $this->par_num;
            $this->page_count = 0;
            $this->data_count = 0;
    		
            $rscount=$db->getOne("SELECT COUNT(d_id) FROM {pre}vod WHERE d_hide=0 " . $where);
            
            if ($rscount ==0){
                $this->html = replaceStr($this->html, $this->markval, "<font style='font-size:13px'><div align='center'>没有相关记录！</div></font>");
                return;
            }
            
            $pagecount=ceil($rscount/$this->par_num);
            $rs = $db->query($sql . $where . $orderstr . $topstr );
			if (!$rs){
				$rscount=0;
				$pagecount=0;
				$this->html = replaceStr($this->html,$this->markval,"vodpagelist标签出错");
				return;
			}
            $this->page_count = $pagecount;
            $this->data_count = $rscount;
            
	        $labelRule = buildregx("\[pagelist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
			preg_match_all($labelRule ,$this->markdes,$matches2);
			$num=0;
	        while ($row = $db ->fetch_array($rs))
		    {
				$num = $num + 1;
				$marktemp = $this->markdes;
			        
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("vod", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
	        unset($rs);
		    unset($matches2);
		    $this->html = replaceStr($this->html, $this->markval, $markhtml);
		}
		unset($matches1);
    }
    
    function topiclist()
    {
    	global $db;
    	if (isN($this->par_start)) { $this->par_start = 0;} else {$this->par_start = intval($this->par_start); }
    	if ($this->par_start>0){ $this->par_start--; }
        If (!isNum($this->par_num)) { $topstr = ""; $this->startnum = $this->par_start;} else{ $this->par_num = intval($this->par_num); $this->startnum = $this->par_start; $topstr = " limit 0," .($this->par_num + $this->startnum); }
        
        if($this->par_id=="" || $this->par_id == "all"){
		}
		else if (!isN($this->par_id)){ 
			$where = $where . " AND t_id IN(" . $this->par_id . ") ";
		}
		        
        $sql = "SELECT t_id,t_name,t_enname,t_pic,t_des FROM {pre}" . $this->par_table . "_topic WHERE 1=1 ";
        if (!isN($this->par_topic) && $this->par_topic != "all"){ $where = $where . " AND t_id IN(" . $this->par_topic . ") "; }
        
        switch($this->par_by)
        {
            case "id": $orderstr = " ORDER BY t_id " . $this->par_order;break;
            default: $orderstr = " ORDER BY t_sort " . $this->par_order;break;
        }
        
        
        $rs = $db->query($sql . $where . $orderstr . $topstr );
		if ($rs){
			$rscount=$db->num_rows($rs);
		}
		else{
			$rscount=0;
			$this->html = replaceStr($this->html,$this->markval,"topiclist标签出错");
			return;
		}
		
		$labelRule = buildregx("\[topiclist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->markdes,$matches2);
		$num=0;
        while ($row = $db ->fetch_array($rs))
	    {
	    	if($i>= intval($this->startnum)){
				$num = $num + 1;
				$marktemp = $this->markdes;
		        
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("topic", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
			$i = $i+1;
		}
        unset($rs);
	    unset($matches2);
	    $this->html = replaceStr($this->html, $this->markval, $markhtml);
    }
    
    function topicpagelist()
    {
    	global $db,$mac;
    	
        $labelRule = buildregx("{maccms:topicpagelist([\s\S]*?)}([\s\S]*?){/maccms:topicpagelist}","");
		preg_match_all($labelRule ,$this->html,$matches1);
		if (isN($this->page)){ $this->page = $mac["page"]; }
		
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
            $markpar = $matches1[1][$i];
            $this->markdes = $matches1[2][$i];
            $this->markval = $matches1[0][$i];
            $labelRule = buildregx("([a-z0-9]+)=([a-z0-9|,]+)","");
            preg_match_all($labelRule,$markpar,$matches2);
            $this->getParam($matches2);
            
            $sql = "SELECT t_id,t_name,t_enname,t_sort,t_template,t_pic,t_des FROM {pre}" . $this->par_table . "_topic WHERE 1=1 ";
            if (!isNum($this->par_num)){ $this->par_num = 12;} else { $this->par_num = intval($this->par_num); }
            $this->page_type = $this->par_table . "topicindex";
            switch($this->par_by)
            {
                case "id": $orderstr = " ORDER BY t_id " . $this->par_order;break;
                default: $orderstr = " ORDER BY t_sort " . $this->par_order;break;
            }
            $topstr = " limit ".($this->par_num * ($this->page-1)) .",".$this->par_num;
            $rscount=$db->getOne("SELECT COUNT(t_id) FROM {pre}" . $this->par_table . "_topic WHERE 1=1 " . $where);
            $this->page_size = $this->par_num;
            $this->page_count = 0;
            $this->data_count = 0;
            
            if ($rscount ==0){
                $this->html = replaceStr($this->html, $this->markval, "<font style='font-size:13px'><div align='center'>没有相关记录！</div></font>");
                return;
            }
            $pagecount=ceil($rscount/$this->par_num);
            $rs = $db->query($sql . $where . $orderstr . $topstr );
			if (!$rs){
				$rscount=0;
				$pagecount=0;
				$this->html = replaceStr($this->html,$this->markval,"topicpagelist标签出错");
				return;
			}
            $this->page_count = $pagecount;
            $this->data_count = $rscount;
            
            $labelRule = buildregx("\[pagelist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
			preg_match_all($labelRule ,$this->markdes,$matches2);
			$num=0;
            while ($row = $db ->fetch_array($rs))
		    {
				$num = $num + 1;
				$marktemp = $this->markdes;
			        
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("topic", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
	        unset($rs);
		    unset($matches2);
		    $this->html = replaceStr($this->html, $this->markval, $markhtml);
        }
        unset($matches1);
    }
    
    function linklist()
    {
    	global $db;
    	
        if (isN($this->par_start)){ $this->par_start = 0;} else {$this->par_start = intval($this->par_start);}
        if ($this->par_start>0){ $this->par_start--; }
        if (!isNum($this->par_num)){ $topstr = ""; $this->startnum = $this->par_start;} else { $this->par_num = intval($this->par_num); $this->startnum = $this->par_start; $topstr = " limit 0," . ($this->par_num + $this->startnum); }
        if (!isN($this->par_type) && $this->par_type != "all"){ $where = $where . " AND l_type ='" . $this->par_type . "' ";}
        switch($this->par_by)
        {
            case "id": $orderstr = " ORDER BY l_id " . $this->par_order;break;
            default: $orderstr = " ORDER BY l_sort " . $this->par_order;break;
        }
        $sql = "SELECT l_id,l_name,l_type,l_url,l_logo FROM {pre}link WHERE 1=1 ";
        
        $rs = $db->query($sql . $where . $orderstr . $topstr);
		if ($rs){
			$rscount=$db->num_rows($rs);
		}
		else{
			$rscount=0;
			$this->html = replaceStr($this->html,$LabelStr,"linklist标签出错:");
			return;
		}
		
		$labelRule = buildregx("\[linklist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->markdes,$matches2);
		$num=0;
        while ($row = $db ->fetch_array($rs))
	    {
	    	if($i>= intval($this->startnum)){
				$num = $num + 1;
				$marktemp = $this->markdes;
				for($j=0;$j<count($matches2[0]);$j++)
				{
					$marktemp = $this->parse("link", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], $row, $num);
				}
				$markhtml = $markhtml . $marktemp;
			}
			$i = $i+1;
		}
        unset($rs);
	    unset($matches2);
	    $this->html = replaceStr($this->html, $this->markval, $markhtml);
    }
    
    function arealist()
    {
    	global $cache,$mac;
    	
    	if(isNum($this->par_type)){
    		$alink = app_installdir. "search.php?type=". $this->par_type . "&area={area}";
    	}
    	else if($this->par_type=="auto" && $mac["vodtypeid"]>0){
    		$alink = $this->getVodTypeLink($this->page_id, $this->page_name, $this->page_enname, false);
    		switch(app_vodlistviewtype)
	        {
	            case 0:
	            case 3:
	                $str = "-1" ."-{area}-{year}-". $mac["order"] ."-". $mac["by"] . "." . app_vodsuffix;
	                break;
	            case 1:
	                $str = "&page=1&area={area}&year={year}&order=" . $mac["order"] . "&by=" . $mac["by"];
	                break;
	            default:
	                $str = "." . app_vodsuffix;
	                break;
	        }
	        $alink = $alink . replaceStr($str,"{year}",$mac["year"]);
    	}
    	else{
    		$alink = app_installdir. "search.php?area={area}";
    	}
        
        if ($this->par_order == "desc"){
            $onstart = count($cache[4]);
            $onend = 0;
            for ($i=$onstart;$i>$onend;$i--){
                $labelRule = buildregx("\[arealist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				$num=0;
		        foreach($cache[4] as $v)
		        {
					$num = $num + 1;
					$marktemp = $this->markdes;
					for($j=0;$j<count($matches2[0]);$j++)
					{
						//function parse($mtype, $mdes, $m1, $m2, $m3, $mrs, $mnum)
						//$marktemp = $this->parse("area", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], replaceStr($alink,"{area}",$v), $num);
						switch($matches2[1][$j])
		           		{
		                    case "num":
		                        $marktemp = replaceStr($marktemp, $matches2[0][$j], $num);
		                    	break;
		                    case "name":
		                        $marktemp = replaceStr($marktemp, $matches2[0][$j], $v);
		                    	break;
		                    case "link":
		                        $marktemp = replaceStr($marktemp, $matches2[0][$j], replaceStr($alink,"{area}", urlencode($v)) );
		                        break;
		            	}
					}
					$markhtml = $markhtml . $marktemp;
				}
			    unset($matches2);
			    $this->html = replaceStr($this->html, $this->markval, $markhtml);
            }
        }
        else{
            $onstart = 0;
            $onend = count($cache[4]);
            for ($i=$onstart;$i<$onend;$i++){
            	$labelRule = buildregx("\[arealist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				$num=0;
		        foreach($cache[4] as $v)
		        {
					$num = $num + 1;
					$marktemp = $this->markdes;
					for($j=0;$j<count($matches2[0]);$j++)
					{
						//$marktemp = $this->parse("area", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], replaceStr($alink,"{area}",$v), $num);
						switch($matches2[1][$j])
		           		{
		                    case "num":
		                        $marktemp = replaceStr($marktemp, $matches2[0][$j], $num);
		                    	break;
		                    case "name":
		                        $marktemp = replaceStr($marktemp, $matches2[0][$j], $v);
		                    	break;
		                    case "link":
		                        $marktemp = replaceStr($marktemp, $matches2[0][$j], replaceStr($alink,"{area}", urlencode($v)) );
		                        break;
		            	}
					}
					$markhtml = $markhtml . $marktemp;
				}
			    unset($matches2);
			    $this->html = replaceStr($this->html, $this->markval, $markhtml);
            }
        }
    }
    
    function yearlist()
    {
    	global $cache,$mac;
    	
    	if(isNum($this->par_type)){
    		$alink = app_installdir. "search.php?type=". $this->par_type . "&year={year}";
    	}
    	else if($this->par_type=="auto" && $mac["vodtypeid"]>0){
    		$alink = $this->getVodTypeLink($this->page_id, $this->page_name, $this->page_enname, false);
    		switch(app_vodlistviewtype)
	        {
	            case 0:
	            case 3:
	                $str = "-1" ."-{area}-{year}-". $mac["order"] ."-". $mac["by"] . "." . app_vodsuffix;
	                break;
	            case 1:
	                $str = "&page=1&area={area}&year={year}&order=" . $mac["order"] . "&by=" . $mac["by"];
	                break;
	            default:
	                $str = "." . app_vodsuffix;
	                break;
	        }
	        $alink = $alink . replaceStr($str,"{area}", urlencode($mac["area"]) );
    	}
    	else{
    		$alink = app_installdir. "search.php?year={year}";
    	}
    	
    	if (!isNum($this->par_start) || strlen($this->par_start)<4){ $this->par_start=2000; } else {$this->par_start=intval($this->par_start);}
        if (!isNum($this->par_end) || strlen($this->par_end)<4) { $this->par_end=date('Y',time()); }else {$this->par_end=intval($this->par_end);}
        
        if ($this->par_order == "desc"){
            $onstart = $this->par_end;
            $onend = $this->par_start;
            $num=0;
            for ($i=$onstart;$i>=$onend;$i--){
                $labelRule = buildregx("\[yearlist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				$num = $num + 1;
				$marktemp = $this->markdes;
				for($j=0;$j<count($matches2[0]);$j++)
				{
					//$marktemp = $this->parse("lang", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], replaceStr($alink,"{area}",$i), $num);
					switch($matches2[1][$j])
		           	{
						case "num":
							$marktemp = replaceStr($marktemp, $matches2[0][$j], $num);
							break;
						case "name":
							$marktemp = replaceStr($marktemp, $matches2[0][$j], $i);
							break;
						case "link":
							$marktemp = replaceStr($marktemp, $matches2[0][$j], replaceStr($alink,"{year}", $i) );
							break;
		            }
				}
				$markhtml = $markhtml . $marktemp;
			    unset($matches2);
            }
            $this->html = replaceStr($this->html, $this->markval, $markhtml);
        }
        else{
            $onstart = $this->par_start;
            $onend = $this->par_end;
            $num=0;
            for ($i=$onstart;$i<=$onend;$i++){
            	$labelRule = buildregx("\[yearlist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				
				$num = $num + 1;
				$marktemp = $this->markdes;
				for($j=0;$j<count($matches2[0]);$j++)
				{
					//$marktemp = $this->parse("year", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j],replaceStr($alink,"{area}",$i), $num);
					switch($matches2[1][$j])
		           	{
						case "num":
							$marktemp = replaceStr($marktemp, $matches2[0][$j], $num);
							break;
						case "name":
							$marktemp = replaceStr($marktemp, $matches2[0][$j], $i);
							break;
						case "link":
							$marktemp = replaceStr($marktemp, $matches2[0][$j], replaceStr($alink,"{year}", $i) );
							break;
		            }
				}
				$markhtml = $markhtml . $marktemp;
			    unset($matches2);
            }
            $this->html = replaceStr($this->html, $this->markval, $markhtml);
        }
    }
    
    function languagelist()
    {
    	global $cache;
        if ($this->par_order == "desc"){
            $onstart = count($cache[5]);
            $onend = 0;
            for ($i=$onstart;$i>$onend;$i--){
                $labelRule = buildregx("\[languagelist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				$num=0;
		        foreach($cache[5] as $v)
		        {
					$num = $num + 1;
					$marktemp = $this->markdes;
					for($j=0;$j<count($matches2[0]);$j++)
					{
						$marktemp = $this->parse("lang", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], $v, $num);
					}
					$markhtml = $markhtml . $marktemp;
				}
			    unset($matches2);
			    $this->html = replaceStr($this->html, $this->markval, $markhtml);
            }
        }
        else{
            $onstart = 0;
            $onend = count($cache[5]);
            for ($i=$onstart;$i<$onend;$i++){
            	$labelRule = buildregx("\[languagelist:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				$num=0;
		        foreach($cache[5] as $v)
		        {
					$num = $num + 1;
					$marktemp = $this->markdes;
					for($j=0;$j<count($matches2[0]);$j++)
					{
						$marktemp = $this->parse("lang", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], $v, $num);
					}
					$markhtml = $markhtml . $marktemp;
				}
			    unset($matches2);
			    $this->html = replaceStr($this->html, $this->markval, $markhtml);
            }
        }
    }
    
    function parse($mtype, $mdes, $m1, $m2, $m3, $mrs, $mnum)
    {
    	global $db,$mac,$cache;
        if ($mnum < 10){ $numfill = "0" . $mnum;} else{ $numfill = $mnum;}
		
        switch($mtype)
        {
            case "type":
                switch($m2)
            	{
                    case "num":
                        $markstr = replaceStr($mdes, $m1, $mnum);
                    	break;
                    case "numfill":
                        $markstr = replaceStr($mdes, $m1, $numfill);
                    	break;
                    case "id":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_id"]);
                    	break;
                    case "name":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_name"]);
                    	break;
                    case "enname":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_enname"]);
                    	break;
                    case "pid":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_pid"]);
                    	break;
                    case "key":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_key"]);
                    	break;
                    case "des":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_des"]);
                    	break;
                    case "link":
                        if ($this->par_table == "art"){
                            $markstr = replaceStr($mdes, $m1, $this->getArtTypeLink($mrs["t_id"], $mrs["t_name"], $mrs["t_enname"], true));
                    	}
                        else{
                            $markstr = replaceStr($mdes, $m1, $this->getVodTypeLink($mrs["t_id"], $mrs["t_name"], $mrs["t_enname"], true));
                        }
                        break;
                    case "linktype":
                    	if ($this->par_table == "vod"){
	                    	$alink = $this->getVodTypeLink($mrs["t_id"], $mrs["t_name"], $mrs["t_enname"], false);
				    		switch(app_vodlistviewtype)
					        {
					            case 0:
					            case 3:
					                $str = "-1" ."-{area}-{year}-". $mac["order"] ."-". $mac["by"] . "." . app_vodsuffix;
					                break;
					            case 1:
					                $str = "&page=1&area={area}&year={year}&order=" . $mac["order"] . "&by=" . $mac["by"];
					                break;
					            default:
					                $str = "." . app_vodsuffix;
					                break;
					        }
					        $str = replaceStr($str,"{year}",$mac["year"]);
					        $alink = $alink . replaceStr($str,"{area}", urlencode($mac["area"]) );
					        $markstr = replaceStr($mdes, $m1, $alink);
                    	}
                    	break;
                    case "count":
                        if ($this->par_table == "art"){
                        	$typearr = getValueByArray($cache[1], "t_id" , $mrs["t_id"] );
							if(is_array($typearr)){
								$where = " and a_type in (" . $typearr["childids"] . ")";
							}
							else{
								$where = " and a_type=" . $mrs["t_id"];
							}
                            $datacount = $db->getOne("SELECT count(*) FROM {pre}art WHERE 1=1=" . $where);
                            $markstr = replaceStr($mdes, $m1, $datacount);
                    	}
                        else{
                        	$typearr = getValueByArray($cache[0], "t_id" , $mrs["t_id"] );
							if(is_array($typearr)){
								$where = " and d_type in (" . $typearr["childids"] . ")";
							}
							else{
								$where = " and d_type=" . $mrs["t_id"];
							}
                            $datacount = $db->getOne("SELECT count(*) FROM {pre}vod WHERE 1=1 " . $where);
                            $markstr = replaceStr($mdes, $m1, $datacount);
                        }
                        break;
                    default:
                        $markstr = $mdes;
                    	break;
            	}
            	break;
            case "link":
            	switch($m2)
            	{
            		case "num":
						$markstr = replaceStr($mdes, $m1, $mnum);
						break;
				    case "numfill":
                        $markstr = replaceStr($mdes, $m1, $numfill);
                    	break;
                    case "id":
						$markstr = replaceStr($mdes, $m1, $mrs["l_id"]);
						break;
                    case "name":
						$markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["l_name"]) );
						break;
                    case "type":
						if ($mrs["l_type"] == "pic"){ $linktype = "图片";} else{ $linktype = "文字";}
						$markstr = replaceStr($mdes, $m1, $linktype);
						break;
                    case "link":
						$markstr = replaceStr($mdes, $m1, $mrs["l_url"]);
						break;
                    case "pic":
						$markstr = replaceStr($mdes, $m1, $mrs["l_logo"]);
						break;
            	}
                break;
            case "area":
            	switch($m2)
           		{
                    case "num":
                        $markstr = replaceStr($mdes, $m1, $mnum);
                    	break;
                    case "name":
                        $markstr = replaceStr($mdes, $m1, $mrs);
                    	break;
                    case "link":
                        $markstr = replaceStr($mdes, $m1, app_installdir . "search.php?area=" .urlencode($mrs) );
                        break;
            	}
            	break;
            case "lang":
            	switch($m2)
           		{
                    case "num":
                        $markstr = replaceStr($mdes, $m1, $mnum);
                    	break;
                    case "name":
                        $markstr = replaceStr($mdes, $m1, $mrs);
                    	break;
                    case "link":
                        $markstr = replaceStr($mdes, $m1, app_installdir . "search.php?language=" .urlencode($mrs) );
                        break;
            	}
            	break;
            case "year":
            	switch($m2)
           		{
                    case "num":
                        $markstr = replaceStr($mdes, $m1, $mnum);
                    	break;
                    case "name":
                        $markstr = replaceStr($mdes, $m1, $mrs);
                    	break;
                    case "link":
                        $markstr = replaceStr($mdes, $m1, app_installdir . "search.php?year=" .$mrs );
                        break;
            	}
            	break;
            case "vod":
            	if ($mac["vodtypeid"] == -1){
                    $typearr = getValueByArray($cache[0],"t_id" , $mrs["d_type"]);
            	}
                else{
                    if (strpos("," . $mrs["d_type"], ",".$mac["vodtypeid"].",") > 0){
                        $typearr = $this->page_typearr;
                    }
                    else{
                        $typearr = getValueByArray($cache[0],"t_id" , $mrs["d_type"]);
                    }
                }
                if(strpos($mdes,"topic")>0){
                	$topicarr = getValueByArray($cache[2],"t_id" , $mrs["d_topic"]);
                }
                
                if (!is_array($typearr)){ return; }
                
                switch($m2)
                {
                    case "num":
                        $markstr = replaceStr($mdes, $m1, $mnum);
                    	break;
                    case "numfill":
                        $markstr = replaceStr($mdes, $m1, $numfill);
                    	break;
                    case "numjoin":
                        $markstr = replaceStr($mdes, $m1, $this->startnum + $mnum );
                    	break;
                    case "id":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_id"]);
                    	break;
                    case "name":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["d_name"]));
                    	break;
                    case "encodename":
                        $markstr = replaceStr($mdes, $m1, urlencode($mrs["d_name"]));
                    	break;
                    case "colorname":
                    	if( $mrs["d_color"]==""){
                    		$markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["d_name"]));
                    	}
                    	else{
                        	$markstr = replaceStr($mdes, $m1, "<font color=\"" . $mrs["d_color"] . "\">" . getTextt($m3, $mrs["d_name"]) . "</font>");
                        }
                    	break;
                    case "subname":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["d_subname"]));
                    	break;
                    case "enname":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["d_enname"]));
                    	break;
                    case "ennamelink":
                    	$markstr = replaceStr($mdes, $m1, getKeysLink($mrs["d_enname"], "pinyin"));
                    	break;
                    case "state":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_state"]);
                    	break;
                    case "color":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_color"]);
                    	break;
                    case "pic":
                        $pic = $mrs["d_pic"];
                        
                        if(app_ftp==1 && app_ftpurl!=""){
                        	$pic = app_ftpurl . $pic;
                        }
                        else{
                        	if (strpos(",".$pic, "http://") <= 0) { $pic = app_installdir . $pic;} else { $pic=$pic.""; }
                        }
                        $markstr = replaceStr($mdes, $m1, $pic);
                        break;
                    case "picthumb":
                        $pic = $mrs["d_picthumb"];
                        if (strpos(",".$pic, "http://") <= 0) { $pic = app_installdir . $pic;} else { $pic=$pic.""; }
                        $markstr = replaceStr($mdes, $m1, $pic);
                        break;
                    case "picslide":
                        $pic = $mrs["d_picslide"];
                        if (strpos(",".$pic, "http://") <= 0) { $pic = app_installdir . $pic;} else { $pic=$pic.""; }
                        $markstr = replaceStr($mdes, $m1, $pic);
                        break;
                    case "starring":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["d_starring"]));
                    	break;
                    case "starringlink":
                        $markstr = replaceStr($mdes, $m1, getKeysLink($mrs["d_starring"], "starring"));
                    	break;
                    case "directed":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["d_directed"]));
                    	break;
                    case "directedlink":
                        $markstr = replaceStr($mdes, $m1, getKeysLink($mrs["d_directed"], "directed"));
                    	break;
                    case "area":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_area"]);
                    	break;
                    case "arealink":
                        $markstr = replaceStr($mdes, $m1, getKeysLink($mrs["d_area"], "area"));
                    	break;
                    case "year":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_year"]);
                    	break;
                    case "yearlink":
                        $markstr = replaceStr($mdes, $m1, getKeysLink($mrs["d_year"], "year"));
                    	break;
                    case "language":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_language"]);
                    	break;
                    case "languagelink":
                        $markstr = replaceStr($mdes, $m1, getKeysLink($mrs["d_language"], "language"));
                    	break;
                    case "level":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_level"]);
                    	break;
                    case "stint":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_stint"]);
                    	break;
                    case "stintdown":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_stintdown"]);
                    	break;
                    case "hits":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_hits"]);
                        break;
                    case "dayhits":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_dayhits"]);
                    	break;
                    case "weekhits":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_weekhits"]);
                    	break;
                    case "monthhits":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_monthhits"]);
                    	break;
                    case "content":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, repPse($mrs["d_content"],$mrs["d_id"])));
                    	break;
                    case "contenttext":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, strip_tags(repPse($mrs["d_content"],$mrs["d_id"]))));
                    	break;
                    case "remarks":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_remarks"]);
                    	break;
                    case "good":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_good"]);
                    	break;
                    case "bad":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_bad"]);
                    	break;
                    case "score":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_score"]);
                    	break;
                    case "scorecount":
                        $markstr = replaceStr($mdes, $m1, $mrs["d_scorecount"]);
                    	break;
                    case "scorepjf":
                        if ($mrs["d_scorecount"] == 0){
                            $markstr = replaceStr($mdes, $m1, "0.0");
                    	}
                        else{
                            $pjf = round($mrs["d_score"] / $mrs["d_scorecount"],1);
                        	if(strpos($pjf,".") <=0){ $pjf=$pjf.".0"; }
                            $markstr = replaceStr($mdes, $m1, $pjf);
                        }
                        break;
                    case "duration":
                    	$markstr = replaceStr($mdes, $m1, $mrs["d_duration"]);
                    	break;
                    case "addtime":
                        $markstr = replaceStr($mdes, $m1, getDatet($m3, $mrs["d_addtime"]));
                    	break;
                    case "time":
                        $markstr = replaceStr($mdes, $m1, getDatet($m3, $mrs["d_time"]));
                    	break;
                    case "from":
                        $markstr = replaceStr($mdes, $m1, getVodXmlText("vodplay.xml","player",$mrs["d_playfrom"],1));
                    	break;
                    case "fromdown":
                        $markstr = replaceStr($mdes, $m1, getVodXmlText("voddown.xml","down",$mrs["d_downfrom"],1));
                    	break;
                    case "link":
                        $markstr = replaceStr($mdes, $m1, $this->getVodLink($mrs["d_id"], $mrs["d_name"], $mrs["d_enname"],$mrs["d_addtime"], $typearr["t_id"], $typearr["t_name"], $typearr["t_enname"]));
                        break;
                    case "playlink":
                        $markstr = replaceStr($mdes, $m1, $this->getVodPlayUrl($mrs["d_id"], $mrs["d_name"], $mrs["d_enname"],$mrs["d_addtime"], $typearr["t_id"], $typearr["t_name"], $typearr["t_enname"], 1, 1));
                    	break;
                    case "downlink":
                        $markstr = replaceStr($mdes, $m1, $this->getVodDownUrl($mrs["d_id"], $mrs["d_name"], $mrs["d_enname"],$mrs["d_addtime"], $typearr["t_id"], $typearr["t_name"], $typearr["t_enname"], 1, 1));
                    	break;
                    case "type":
                    	$markstr = replaceStr($mdes, $m1, $mrs["d_type"]);
                    	break;
                    case "typepid":
                    	$markstr = replaceStr($mdes, $m1, $typearr["t_pid"]);
                    	break;
                    case "typelink":
                        $markstr = replaceStr($mdes, $m1, $this->getVodTypeLink($typearr["t_id"], $typearr["t_name"], $typearr["t_enname"], true));
                    	break;
                    case "typename":
                        $markstr = replaceStr($mdes, $m1, $typearr["t_name"]);
                    	break;
                    case "typeenname":
                        $markstr = replaceStr($mdes, $m1, $typearr["t_enname"]);
                    	break;
                    case "typekey":
                    	$markstr = replaceStr($mdes, $m1, $typearr["t_key"]);
                    	break;
                   	case "typedes":
                   		$markstr = replaceStr($mdes, $m1, $typearr["t_des"]);
                   		break;
					case "topic":
						$markstr = replaceStr($mdes, $m1, $mrs["d_topic"]);
                    	break;
					case "topicname":
						if(is_array($topicarr)){
							$markstr = replaceStr($mdes, $m1, $topicarr["t_name"]);
						}
						else{
							$markstr = replaceStr($mdes, $m1, "");
						}
						break;
					case "topiclink":
						if(is_array($topicarr)){
							$markstr = replaceStr($mdes, $m1, $this->getVodTopicLink($mrs["d_topic"], $topicarr["t_name"], $topicarr["t_enname"], true) );
						}
						else{
							$markstr = replaceStr($mdes, $m1, "###" );
						}
						break;
                    case "userfav":
                        $markstr = replaceStr($mdes, $m1, "<a href=\"javascript:void(0)\" onclick=\"userFav('" . $mrs["d_id"] . "');return false;\"/>会员收藏</a>");
                    	break;
                    case "desktop":
                    	 $markstr = replaceStr($mdes, $m1, "<a href=\"javascript:void(0)\" onclick=\"desktop('" . $mrs["d_name"] . "');return false;\"/>保存到桌面</a>");
                    	break;
                    default:
                        $markstr = $mdes;
        		}
            	break;
            case "art":
                if ($mac["arttypeid"] == -1){
                    $typearr = getValueByArray($cache[1],"t_id" , $mrs["a_type"]);
            	}
                else{
                    if (strpos("," . $mrs["ad_type"], ",".$mac["arttypeid"].",") > 0){
                        $typearr = $this->page_typearr;
                    }
                    else{
                        $typearr = getValueByArray($cache[1],"t_id" , $mrs["a_type"]);
                    }
                }
                if(strpos($mdes,"topic")>0){
                	$topicarr = getValueByArray($cache[3],"t_id" , $mrs["a_topic"]);
                }
                
                if (!is_array($typearr)){ return; }
                
                switch($m2)
				{
                    case "num":
                        $markstr = replaceStr($mdes, $m1, $mnum);
                    	break;
                    case "numfill":
                        $markstr = replaceStr($mdes, $m1, $numfill);
                    	break;
                    case "numjoin":
                        $markstr = replaceStr($mdes, $m1, $this->startnum + $mnum );
                    	break;
                    case "id":
                        $markstr = replaceStr($mdes, $m1, $mrs["a_id"]);
                    	break;
                    case "title":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["a_title"]));
                    	break;
                    case "colortitle":
                    	if( $mrs["a_color"]==""){
                    		$markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["a_title"]));
                    	}
                    	else{
                        	$markstr = replaceStr($mdes, $m1, "<font color=\"" . $mrs["a_color"] . "\">" . getTextt($m3, $mrs["a_title"]) . "</font>");
                        }
                    	break;
                    case "subtitle":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["a_subtitle"]));	
                    	break;
                    case "entitle":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["a_entitle"]));
                    	break;
                    case "from":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["a_from"]));
                    	break;
                    case "content":
                    	$content = $mrs["a_content"];
                    	if ($this->page_type=="art"){ $content = $this->page_content; }
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $content));
                    	break;
                    case "contenttext":
                    	$content = $mrs["a_content"];
                    	if ($this->page_type=="art"){ $content = $this->page_content; }
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, strip_tags($content)));
                    	break;
                    case "author":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["a_author"]));
                    	break;
                    case "color":
                        $markstr = replaceStr($mdes, $m1, $mrs["a_color"]);
                    	break;
                    case "hits":
                        $markstr = replaceStr($mdes, $m1, $mrs["a_hits"]);
                    	break;
                    case "dayhits":
                        $markstr = replaceStr($mdes, $m1, $mrs["a_dayhits"]);
                    	break;
                    case "weekhits":
                        $markstr = replaceStr($mdes, $m1, $mrs["a_weekhits"]);
                        break;
                    case "monthhits":
                        $markstr = replaceStr($mdes, $m1, $mrs["a_monthhits"]);
                    	break;
                    case "addtime":
                        $markstr = replaceStr($mdes, $m1, getDatet($m3, $mrs["a_addtime"]));
                    	break;
                    case "time":
                        $markstr = replaceStr($mdes, $m1, getDatet	($m3, $mrs["a_time"]));
                    	break;
                    case "pic":
                    	$pic = $mrs["a_pic"];
                        if (strpos(",".$pic, "http://") <= 0){ $pic = app_installdir . $pic;} else {$pic=$pic."";}
                        $markstr = replaceStr($mdes, $m1, $pic);
                        break;
                    case "link":
                        $markstr = replaceStr($mdes, $m1, $this->getArtLink($mrs["a_id"], $mrs["a_title"], $mrs["a_entitle"],$mrs["a_addtime"], $typearr["t_id"], $typearr["t_name"], $typearr["t_enname"],true));
                        break;
                    case "type":
                    	$markstr = replaceStr($mdes, $m1, $mrs["a_type"]);
                    	break;
                    case "typepid":
                    	$markstr = replaceStr($mdes, $m1, $typearr["t_pid"]);
                    	break;
                    case "typelink":
                        $markstr = replaceStr($mdes, $m1, $this->getArtTypeLink($typearr["t_id"], $typearr["t_name"], $typearr["t_enname"], true));
                        break;
                    case "typename":
                        $markstr = replaceStr($mdes, $m1, $typearr["t_name"]);
                        break;
                    case "typeenname":
                        $markstr = replaceStr($mdes, $m1, $typearr["t_enname"]);
                    	break;
                    case "typekey":
                    	$markstr = replaceStr($mdes, $m1, $typearr["t_key"]);
                    	break;
                   	case "typedes":
                   		$markstr = replaceStr($mdes, $m1, $typearr["t_des"]);
                   		break;
                    case "topic":
						$markstr = replaceStr($mdes, $m1, $mrs["a_topic"]);
                    	break;
                    case "level":
                        $markstr = replaceStr($mdes, $m1, $mrs["a_level"]);
                    	break;
					case "topicname":
						if(is_array($topicarr)){
							$markstr = replaceStr($mdes, $m1, $topicarr["t_name"]);
						}
						else{
							$markstr = replaceStr($mdes, $m1, "");
						}
						break;
					case "topiclink":
						if(is_array($topicarr)){
							$markstr = replaceStr($mdes, $m1, $this->getArtTopicLink($mrs["a_topic"], $topicarr["t_name"], $topicarr["t_enname"], true) );
						}
						else{
							$markstr = replaceStr($mdes, $m1, "###" );
						}
						break;
                    default:
                        $markstr = $mdes;
                        break;
                }
            	break;
            case "topic":
            	switch($m2)
				{
                    case "num":
                        $markstr = replaceStr($mdes, $m1, $mnum);
                    	break;
                    case "numfill":
                        $markstr = replaceStr($mdes, $m1, $numfill);
                    	break;
                    case "id":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_id"]);
                    	break;
                    case "name":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["t_name"]));
                    	break;
                    case "enname":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["t_enname"]));
                    	break;
                    case "sort":
                        $markstr = replaceStr($mdes, $m1, $mrs["t_sort"]);
                    	break;
                    case "pic":
                    	$pic = $mrs["t_pic"];
                        
                        if(app_ftp==1 && app_ftpurl!=""){
                        	$pic = app_ftpurl . $pic;
                        }
                        else{
                        	if (strpos(",".$pic, "http://") <= 0) { $pic = app_installdir . $pic;} else { $pic=$pic.""; }
                        }
                        $markstr = replaceStr($mdes, $m1, $pic);
                    	break;
                    case "count":
                        if ($this->par_table == "art"){
                            $datacount = $db->getOne("SELECT count(a_id) FROM {pre}art WHERE a_topic=" .$mrs["t_id"]);
                            $markstr = replaceStr($mdes, $m1, $datacount);
                    	}
	                    else{
                            $datacount = $db->getOne("SELECT count(d_id) FROM {pre}vod WHERE d_topic=" .$mrs["t_id"]);
                            $markstr = replaceStr($mdes, $m1, $datacount);
	                    }
	                    break;
                    case "des":
                        $markstr = replaceStr($mdes, $m1, getTextt($m3, $mrs["t_des"]));
                    	break;
                    case "link":
                        if ($this->par_table == "art"){
                            $markstr = replaceStr($mdes, $m1, $this->getArtTopicLink($mrs["t_id"], $mrs["t_name"], $mrs["t_enname"], true));
                    	}
                        else{
                            $markstr = replaceStr($mdes, $m1, $this->getVodTopicLink($mrs["t_id"], $mrs["t_name"], $mrs["t_enname"], true));
                        }
                        break;
                    default:
                        $markstr = $mdes;
                    	break;
                }
            	break;
            default:
                break;
        }
        return $markstr;
    }
    
    function pageshow()
	{
        $labelRule = buildregx("{maccms:pagenum([\s\S]*?)}","");
		preg_match_all($labelRule ,$this->html,$matches1);
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
            $pagenumval = $matches1[0][$i];
            $labelRule = buildregx("([a-z0-9]+)=([a-z0-9|,]+)","");
			preg_match_all($labelRule ,$pagenumval,$matches2);
			
            for($j=0;$j<count($matches2[0]);$j++)
			{
            	switch($matches2[1][$j])
            	{
            		case "len" : $pagenum = $matches2[2][$j];break;
            		case "linktype" : $this->page_type =$matches2[2][$j];break;
            	}
            }
            unset($matches2);
        }
        unset($matches1);
        
        if ($this->page_count<1){ $this->page_count=1;}
        
        $pagefirst = $this->getPageLink(1);
        
        if ($this->page == 1 && $this->page_count == 1){
            $pagepre = $pagefirst;
            $pagenext = $pagefirst;
            $pagelast = $pagefirst;
        }
        else if ($this->page == $this->page_count && $this->page_count > 1){
            $pagepre = $this->getPageLink($this->page - 1);
            $pagenext = $this->getPageLink($this->page_count);
            $pagelast = $pagenext;
        }
        else if ($this->page == 1 && $this->page_count > 1){
            $pagepre = $pagefirst;
            $pagenext = $this->getPageLink($this->page + 1);
            $pagelast = $this->getPageLink($this->page_count);
        }
        else{
            $pagepre = $this->getPageLink($this->page - 1);
            $pagenext = $this->getPageLink($this->page + 1);
            $pagelast = $this->getPageLink($this->page_count);
        }
        
        $this->startnum = 1;
        $endnum = intval($pagenum);
        
        //if ($this->page > ($endnum / 2) ){
         // $this->startnum = $this->page - $endnum /2;
          //$endnum = $this->page + $endnum / 2;
        //}
        
        if ($endnum%2==0){
			$loopnum1=$endnum/2;
			$loopnum2=$endnum/2;
		}
		else{
			$loopnum1=intval($endnum/2)+1;
			$loopnum2=intval($endnum/2);
		}
		$i = $this->page - $loopnum1+1;
		$j = $this->page + $loopnum2;
		if ($i<1){
			$i=1; 
			$j=$endnum;
		} 
		if ($j> $this->page_count){
			$i = $i+( $this->page_count-$j);
			$j = $this->page_count;
			if ($i<1){
				$i=1;
			} 
		} 
        
        for ($p=$i; $p<=$j; $p++){
            if ($p > $this->page_count){ break; }
            if ($p == $this->page){
                $strpagenum = $strpagenum . "<em class=\"current\">" . $p . "</em>&nbsp;";
            }
            else{
                $strpagenum = $strpagenum . "<a href=\"" . $this->getPageLink($p) . "\">" . $p . "</a>&nbsp;";
            }
        }
        
        $cacheName = "pageselect_" . $this->page_type . "_" . $this->page_id;
        if (chkCache($cacheName)){
            $pagesel = getCache($cacheName);
        }
        else{
            $pagesel = "<select name=\"pagelist\" onchange=javascript:window.location=this.options[this.selectedIndex].value; ><option value=\"\">请选择页码</option>";
            for($i=1;$i<=$this->page_count;$i++){
                $pagesel = $pagesel . "<option value=\"" . $this->getPageLink($i) . "\">第" . $i . "页</option>";
            }
            $pagesel = $pagesel . "</select>";
            setCache ($cacheName, $pagesel,0);
        }
        
        $this->html = replaceStr($this->html, $pagenumval, $strpagenum);
        $this->html = replaceStr($this->html, "{maccms:pagenow}", $this->page);
        $this->html = replaceStr($this->html, "{maccms:pagefirst}", $pagefirst);
        $this->html = replaceStr($this->html, "{maccms:pagelast}", $pagelast);
        $this->html = replaceStr($this->html, "{maccms:pagepre}", $pagepre);
        $this->html = replaceStr($this->html, "{maccms:pagenext}", $pagenext);
        $this->html = replaceStr($this->html, "{maccms:pagedata}", $this->data_count);
        $this->html = replaceStr($this->html, "{maccms:pagesize}", $this->page_size);
        $this->html = replaceStr($this->html, "{maccms:pagecount}", $this->page_count);
        $this->html = replaceStr($this->html, "{maccms:pageselect}", $pagesel);
        $this->html = replaceStr($this->html, "{searchpage:count}", $this->data_count);
    }
    
    function playdownlist($flag,$id, $name, $enname,$addtime, $typeid, $typename, $typeenname, $playfrom, $playserver, $playurl)
    {
    	global $mac;
    	$playurlarrlen=0;
    	$playfromarrlen=0;
    	$playserverarrlen=0;
    	
    	if($playurl !=""){
        	$playurlarr = explode("$$$",$playurl); $playurlarrlen = count($playurlarr);
        }
        if($playfrom !=""){
        	$playfromarr = explode("$$$",$playfrom); $playfromarrlen = count($playfromarr);
        }
        if($playserver !=""){
        	$playserverarr = explode("$$$",$playserver); $playserverarrlen = count($playserverarr);
        }
        
        
        $labelRule = buildregx("{maccms:".$flag."list([\s\S]*?)}([\s\S]*?){/maccms:".$flag."list}","");
		preg_match_all($labelRule ,$this->html,$matches1);
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
            $markpar = $matches1[1][$i];
            $this->markdes = $matches1[2][$i];
            $this->markval = $matches1[0][$i];
            $labelRule = buildregx("([a-z0-9]+)=([a-z0-9|,]+)","");
            preg_match_all($labelRule,$markpar,$matches2);
            $this->getParam($matches2);
            
            $markhtml="";
			$markslist1=array();
            $num=0;
            $oldsort=0;
            for ($j=0;$j<$playfromarrlen;$j++){
                $num = $num + 1;
                $fromrc=true;
                $from = $playfromarr[$j] ;
                if($flag=="play"){
                	$show = getVodXmlText("vodplay.xml","player", $playfromarr[$j] , 1);
                	$des = getVodXmlText("vodplay.xml","player", $playfromarr[$j] , 2);
                	$sortt = getVodXmlText("vodplay.xml","player", $playfromarr[$j] , 3);
                	$tip = getVodXmlText("vodplay.xml","player", $playfromarr[$j] , 4);
                }
                else{
                	$show = getVodXmlText("voddown.xml","down", $playfromarr[$j] , 1);
                	$des = getVodXmlText("voddown.xml","down", $playfromarr[$j] , 2);
                	$sortt = getVodXmlText("voddown.xml","down", $playfromarr[$j] , 3);
                	$tip = getVodXmlText("voddown.xml","down", $playfromarr[$j] , 4);
                }
                
                if ($playserverarrlen >= $j){
                    $server = getVodXmlText("vodserver.xml","server", $playserverarr[$j] , 1);
                    $serverdes = getVodXmlText("vodserver.xml","server", $playserverarr[$j] , 2);
                    $serversortt = getVodXmlText("vodserver.xml","server", $playserverarr[$j] , 3);
                    $servertip = getVodXmlText("vodserver.xml","server", $playserverarr[$j] , 4);
                }
                
                if($flag=="play"){
	                if (app_vodplayviewtype !=3 && $this->par_from=="current"){
	                	if ($num==$mac["vodsrc"]){ $fromrc=true; } else{ $fromrc=false; $markhtml=""; }
	                }
                }
                else{
                	if (app_voddownviewtype !=3 && $this->par_from=="current"){
	                	if ($num==$mac["vodsrc"]){ $fromrc=true; } else{ $fromrc=false; $markhtml=""; }
	                }
                }
                
                if ($fromrc && $playurlarrlen >= $j){
                    $url = $playurlarr[$j];
                    $urlarr = explode("#",$url);
                    $urlarrcount = count($urlarr);
                    $n=0;
                    $labelRule = buildregx("{maccms:urllist([\s\S]*?)}([\s\S]*?){/maccms:urllist}","");
					preg_match_all($labelRule ,$this->markval,$matches3);
					
                    for($k=0;$k<count($matches3[0]);$k++)
					{
						$markorder = $matches3[1][$k];
                        $marktemp = $matches3[2][$k];
                        $markslist2 = "";
                        
                        if ( strpos($markorder,"desc")>0){
	                        for ($m=count($urlarr);$m>=0;$m--){
	                            if (!isN($urlarr[$m])){
	                                $urlone = explode("$",$urlarr[$m]);
	                                
	                                if (count($urlone) == 2){
	                                    $urlname = $urlone[0];
	                                    $urlpath = $urlone[1];
	                                }
	                                else{
	                                    $urlpath = $urlone[0];
	                                    $urlname = "第" . ($m + 1)  ."集";
	                                }
	                                $markstr = replaceStr($marktemp, "[urllist:num]", $m+1 );
	                                $markstr = replaceStr($markstr, "[urllist:name]", $urlname);
	                                $markstr = replaceStr($markstr, "[urllist:path]", $urlpath);
	                                if($flag=="play"){
	                                	$playlink = $this->getVodPlayUrl($id, $name, $enname,$addtime, $typeid, $typename, $typeenname, $j + 1, $m + 1);
	                                }
	                                else{
	                                	$playlink = $this->getVodDownUrl($id, $name, $enname,$addtime, $typeid, $typename, $typeenname, $j + 1, $m + 1);
	                                }
	                                $markstr = replaceStr($markstr, "[urllist:link]", $playlink);
	                                $markslist2 = $markslist2 . $markstr;
	                            }
	                        }
                        }
                        else{
                        	for ($m=0;$m<count($urlarr);$m++){
	                            if (!isN($urlarr[$m])){
	                                $urlone = explode("$",$urlarr[$m]);
	                                
	                                if (count($urlone) == 2){
	                                    $urlname = $urlone[0];
	                                    $urlpath = $urlone[1];
	                                }
	                                else{
	                                    $urlpath = $urlone[0];
	                                    $urlname = "第" . ($m + 1)  ."集";
	                                }
	                                $markstr = replaceStr($marktemp, "[urllist:num]", $m+1 );
	                                $markstr = replaceStr($markstr, "[urllist:name]", $urlname);
	                                $markstr = replaceStr($markstr, "[urllist:path]", $urlpath);
	                                if($flag=="play"){
	                                	$playlink = $this->getVodPlayUrl($id, $name, $enname,$addtime, $typeid, $typename, $typeenname, $j + 1, $m + 1);
	                                }
	                                else{
	                                	$playlink = $this->getVodDownUrl($id, $name, $enname,$addtime, $typeid, $typename, $typeenname, $j + 1, $m + 1);
	                                }
	                                $markstr = replaceStr($markstr, "[urllist:link]", $playlink);
	                                $markslist2 = $markslist2 . $markstr;
	                            }
	                        }
                        }
                        if (app_playisopen==1){
                        	$markslist2 = replaceStr($markslist2,"target=\"_blank\"","target=\"_self\"");
                        }
                        if($n==0){
                        	$markhtml = replaceStr($this->markdes, $matches3[0][$k] , $markslist2);
                        }
                        else{
                        	$markhtml = replaceStr($markhtml, $matches3[0][$k] , $markslist2);
                        }
                        $n++;
                    }
                    unset($matches3);
                }
                $markhtml = replaceStr($markhtml, "[".$flag."list:urlcount]", $urlarrcount);
                $markhtml = replaceStr($markhtml, "[".$flag."list:count]", $playfromarrlen);
                $markhtml = replaceStr($markhtml, "[".$flag."list:num]", $num);
                $markhtml = replaceStr($markhtml, "[".$flag."list:from]", $from);
                $markhtml = replaceStr($markhtml, "[".$flag."list:show]", $show);
                $markhtml = replaceStr($markhtml, "[".$flag."list:des]", $des);
                $markhtml = replaceStr($markhtml, "[".$flag."list:sort]", $sortt);
                $markhtml = replaceStr($markhtml, "[".$flag."list:tip]", $tip);
                $markhtml = replaceStr($markhtml, "[".$flag."list:server]", $server);
                $markhtml = replaceStr($markhtml, "[".$flag."list:serversort]", $serversortt);
                $markhtml = replaceStr($markhtml, "[".$flag."list:serverurl]", $serverdes);
                $markhtml = replaceStr($markhtml, "[".$flag."list:servertip]", $servertip);
                
                if(app_vodplayersort==1){
                	$markslist1[$sortt + ($playfromarrlen-$num) ] = $markhtml;
                }
                else{
                	$markslist1[$num] = $markhtml ;
                }
            }
            unset($matches2);
            if(app_vodplayersort==1){
            	krsort($markslist1);
            }
            $playlisthtml = join("",$markslist1);
            $this->html = replaceStr($this->html, $this->markval, $playlisthtml);
	    }
	    unset($markslist1);
	    unset($urlarr);
	    unset($urlone);
	    unset($playurlarr);
	    unset($playfromarr);
	    unset($playserverarr);
	    unset($matches1);
    }
    
    function replaceComment($c_type, $c_vid)
    {
    	if ($c_type == 1){ $strLabel = "vodinfo";} else {$strLabel = "artinfo";}
        
        $str = "<div id=\"maccms_comment\"><div style=\"padding:5px;text-align:center;\"><img src=\"" . app_installdir . "images/loading.gif\"/> &nbsp;&nbsp;<strong>评论载入中，请稍候.....</strong></div></div><script language=\"javascript\">getComment('" . app_installdir . "plus/comment/?id=". $c_vid . "&type=" . $c_type . "');</script>";
        
        $this->html = replaceStr($this->html, "[" . $strLabel . ":comment]", $str);
    }
    
    function replaceMood($m_type, $m_vid)
    {
    	if ($m_type == 1){ $strLabel = "vodinfo";} else { $strLabel = "artinfo";}
        
        $str = "<script type=\"text/javascript\" src=\"" . app_installdir . "plus/mood/mood.js\"></script><script language = \"JavaScript\" src =\"" . app_installdir . "plus/mood/?m_vid=" . $m_vid . "&m_type=" . $m_type . "\"></script>";
        
        $this->html = replaceStr($this->html, "[" . $strLabel . ":mood]", $str);
    }
    
    function replaceDigg($d_type, $d_vid)
    {
    	if ($d_type == 1){ $strLabel = "vodinfo";} else { $strLabel = "artinfo";}
        $str = "<script type=\"text/javascript\" src=\"" . app_installdir . "plus/digg/digg.js\"></script><script language =\"JavaScript\" src =\"" . app_installdir . "plus/digg/?d_vid=" . $d_vid . "&d_type=" . $d_type . "\"></script>";
        $this->html = replaceStr($this->html, "[" . $strLabel . ":digg]", $str);
    }
    
    function getTypeText($tid,$tpid,$tname,$tlink,$flag)
    {
    	global $cache;
    	$res="";
		if (!chkCache($flag."_typetext_".$tid)){
			if ($tpid != 0){
				if ($flag == "art"){
					$typeParr =  getValueByArray($cache[1], "t_id", $tpid);
					$typePLink =  $this->getArtTypeLink($tpid, $typeParr["t_name"], $typeParr["t_enname"], true);
				}
				else{
					$typeParr = getValueByArray($cache[0], "t_id", $tpid);
					$typePLink = $this->getVodTypeLink($tpid, $typeParr["t_name"], $typeParr["t_enname"], true);
				}
				$res = "<a href='". app_installdir ."'>首页</a>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $typePLink ."' >". $typeParr["t_name"] ."</a>" . "&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $tlink ."'>". $tname ."</a>";
			}
			else{
				$res = "<a href='". app_installdir ."'>首页</a>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $tlink ."' >". $tname ."</a>";
			}
		}
		else{
			$res = getCache($flag."_typetext_".$tid);
		}
		return $res;
	}
    
    function loadlist($flag, $typearr)
    {
    	global $mac;
        if (!is_array($typearr)){ showMsg ("找不到此数据", "../"); }
        if ($flag == "art"){
            $this->page_id = $mac["arttypeid"];
            $mac["arttypepid"] = $typearr["t_pid"];
            $typeLink = $this->getArtTypeLink($this->page_id, $typearr["t_name"], $typearr["t_enname"], true);
            $viewtype = app_artlistviewtype;
        }
        else{
            $this->page_id = $mac["vodtypeid"];
            $mac["vodtypepid"] = $typearr["t_pid"];
            $typeLink = $this->getVodTypeLink($this->page_id, $typearr["t_name"], $typearr["t_enname"], true);
            $viewtype = app_vodlistviewtype;
        }
		
        $this->page_name = $typearr["t_name"];
        $this->page_enname = $typearr["t_enname"];
        $t_pid = $typearr["t_pid"];
        $t_key = $typearr["t_key"];
        $t_des = $typearr["t_des"];
        $t_template = $typearr["t_template"];
        $this->page_typearr = $typearr;
        $this->page = $mac["page"];
        
        if ($viewtype!=2) { attemptCacheFile ($flag . "list", "list" . $this->page_id . $mac["page"] . urlencode($mac["area"]) . $mac["year"]. $mac["order"] .$mac["by"] ); }
        $cacheName = $flag . "list_" . $this->page_id . $mac["page"] . urlencode($mac["area"]) . $mac["year"]. $mac["order"] .$mac["by"];
        $cachetemplatename = "template_" . $flag . "list_" . $this->page_id;
        $templatepath = root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $t_template;
        
        if (chkCache($cacheName)){
            $this->html = getCache($cacheName);
        }
        else{
            $this->html = getFileByCache($cachetemplatename, $templatepath);
            $this->html = replaceStr($this->html, "{typepage:id}", $this->page_id);
            $this->html = replaceStr($this->html, "{typepage:name}", $this->page_name);
            $this->html = replaceStr($this->html, "{typepage:enname}", $this->page_enname);
            $this->html = replaceStr($this->html, "{typepage:pid}", $t_pid);
            $this->html = replaceStr($this->html, "{typepage:key}", $t_key);
            $this->html = replaceStr($this->html, "{typepage:des}", $t_des);
            $this->html = replaceStr($this->html, "{typepage:link}", $typeLink);
            $this->html = replaceStr($this->html, "{typepage:textlink}",  $this->getTypeText($this->page_id,$t_pid,$this->page_name,$typeLink,$flag)  );
            $this->mark();
            setCache ($cacheName, $this->html, 0);
        }
        
        $this->html = replaceStr($this->html, "{typepage:page}", $this->page);
        $this->html = replaceStr($this->html, "{typepage:order}", $mac["order"]);
        $this->html = replaceStr($this->html, "{typepage:by}", $mac["by"]);
        $this->html = replaceStr($this->html, "{typepage:area}", $mac["area"]);
        $this->html = replaceStr($this->html, "{typepage:year}", $mac["year"]);
        
        
        if ($flag == "art"){
        	$this->artpagelist();
        }
        else{
        	$vlink = $this->getVodTypeLink($this->page_id, $typearr["t_name"], $typearr["t_enname"], false);
			switch(app_vodlistviewtype)
			{
				case 0:
				case 3:
					$str = "-1" ."-{area}-{year}-". $mac["order"] ."-". $mac["by"] . "." . app_vodsuffix;
					break;
				case 1:
					$str = "&page=1&area={area}&year={year}&order=" . $mac["order"] . "&by=" . $mac["by"];
					break;
				default:
					$str = "." . app_vodsuffix;
					break;
			}
			$alink = $vlink . $str;
			$linkyear = replaceStr($alink,"{area}", urlencode($mac["area"]) );
	        $linkyear = replaceStr($linkyear,"{year}","");
	        $this->html = replaceStr($this->html, "{typepage:linkyear}", $linkyear );
	        
	        $linkarea = replaceStr($alink,"{year}",$mac["year"]);
	        $linkarea = replaceStr($linkarea,"{area}","");
	        $this->html = replaceStr($this->html, "{typepage:linkarea}", $linkarea );
	        
	        if($typearr["t_pid"]==0){$linkid=$typearr["t_id"]; } else { $linkid= $typearr["t_pid"]; }
	        $vlink = $this->getVodTypeLink($linkid, $typearr["t_name"], $typearr["t_enname"], false);
	        $blink = $vlink . $str;
			$linktype = replaceStr($blink,"{year}",$mac["year"]);
			$linktype = replaceStr($linktype,"{area}", urlencode($mac["area"]) );
			$this->html = replaceStr($this->html, "{typepage:linktype}", $linktype );
        	
        	$this->vodpagelist();
       	}
        $this->pageshow();
        $this->ifEx();
        if ($viewtype!=2) { setCacheFile ($flag . "list", "list" . $this->page_id . $mac["page"] . urlencode($mac["area"]) . $mac["year"]. $mac["order"] .$mac["by"] , $this->html); }
        $this->run();
    }
    
    function loadtopic($flag, $typearr)
    {
    	global $mac;
        if (!is_array($typearr)){ showMsg ("找不到此数据", "../"); }
        
        if ($flag == "art"){
            $this->page_id = $mac["arttopicid"];
            $typeLink = $this->getArtTopicLink($this->page_id, $typearr["t_name"], $typearr["t_enname"], true);
            $viewtype = app_arttopicviewtype;
        }
        else{
            $this->page_id = $mac["vodtopicid"];
            $typeLink = $this->getVodTopicLink($this->page_id, $typearr["t_name"], $typearr["t_enname"], true);
            $viewtype = app_vodtopicviewtype;
        }
        $this->page_name = $typearr["t_name"];
        $this->page_enname = $typearr["t_enname"]; 
        $t_sort = $typearr["t_sort"]; 
        $t_template = $typearr["t_template"]; 
        $t_pic = $typearr["t_pic"]; 
        $t_des = $typearr["t_des"]; 
        $this->page_typearr = $typearr;
        $this->page = $mac["page"];
        
        if ($viewtype!=2) { attemptCacheFile ($flag. "topiclist", "topiclist" . $this->page_id .$this->page); }
        $cacheName = $flag. "topiclist_" . $this->page_id;
        $cachetemplatename = "template_" . $flag . "topiclist_" . $this->page_id;
        $templatepath = root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $t_template;
        
        if (chkCache($cacheName)){
            $this->html = getCache($cacheName);
        }
        else{
            $this->html = getFileByCache($cachetemplatename, $templatepath);
            $this->html = replaceStr($this->html, "{topicpage:id}", $this->page_id);
            $this->html = replaceStr($this->html, "{topicpage:pic}", $t_pic);
            $this->html = replaceStr($this->html, "{topicpage:des}", $t_des);
            $this->html = replaceStr($this->html, "{topicpage:name}", $this->page_name);
            $this->html = replaceStr($this->html, "{topicpage:enname}", $this->page_enname);
            $this->html = replaceStr($this->html, "{topicpage:link}", $typeLink);
            $this->mark();
            setCache ($cacheName, $this->html,0);
        }
        
        $this->html = replaceStr($this->html, "{topicpage:page}", $this->page);
        if ($flag == "art"){
        	$this->artpagelist();
        }
        else{
        	$this->vodpagelist();
       	}
        $this->pageshow();
        $this->ifEx();
        if ($viewtype!=2) { setCacheFile ($flag . "topiclist", "topiclist" . $this->page_id . $this->page, $this->html); }
        $this->run();
    }
    
    function replacePlayName($flag,$from,$url,$src,$num)
    {
    	$playfromarr = explode("$$$",$from); $playfromarrlen = count($playfromarr);
    	$playurlarr = explode( "$$$",$url);  $playurlarrlen = count($playurlarr);
    	
	    for ($i=0;$i<$playfromarrlen;$i++){
	        if (($src-1) == $i){
	        	$urlfrom = $playfromarr[$i] ;
	        	if($flag=="play"){
                	$urlfromshow = getVodXmlText("vodplay.xml","player", $playfromarr[$i] , 1);
                }
                else{
                	$urlfromshow = getVodXmlText("voddown.xml","down", $playfromarr[$i] , 1);
                }
	            $url = $playurlarr[$i];
	            $urlarr = explode("#",$url);
	            $url = "";
	            for ($j=0;$j<count($urlarr);$j++){
					if (!isN($urlarr[$j])){
						if ($j==($num-1)){
							$urlone = explode("$",$urlarr[$j]);
							if (count($urlone)==2){
								$urlname = $urlone[0];
								$urlpath = $urlone[1];
							}
							else{
								$urlname = "第" . ($j + 1) . "集";
								$urlpath = $urlone[0];
							}
							
							$this->html = replaceStr($this->html,"[".$flag."info:from]",$urlfrom);
							$this->html = replaceStr($this->html,"[".$flag."info:fromshow]",$urlfromshow);
							$this->html = replaceStr($this->html,"[".$flag."info:name]",$urlname);
							$this->html = replaceStr($this->html,"[".$flag."info:urlpath]",$urlpath);
							
							break;
						}
	                }
	            }
		    }
	    }
	    unset($urlarr);
	    unset($urlone);
	    unset($playfromarr);
	    unset($playurlarr);
    }
    
    function loadvod($row,$typearr,$flag)
    {
    	global $cache,$mac;
    	//if (!is_array($this->page_typearr)){ $this->page_typearr = getValueByArray($cache[0], "t_id", $mac["vodtypeid"]); }
        
        $this->page_typearr = $typearr;
        $mac["vodtypepid"] = $this->page_typearr["t_pid"];
        if ($flag == "view"){
            $cacheName = "vod_" . $mac["vodtypeid"];
            $cachetemplatename = "template_vod" . $mac["vodtypeid"];
            $templatepath = root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $this->page_typearr["t_vodtemplate"];
        }
	    else if ($flag == "play"){
            if (app_playisopen == 1){ $playtemplatefile = "vodplayopen.html";} else { $playtemplatefile = $this->page_typearr["t_playtemplate"]; }
            $cacheName = "vodplay_" . $mac["vodtypeid"];
            $cachetemplatename = "template_vodplay" . $mac["vodtypeid"];
            $templatepath = root . "template/" . app_templatedir . "/" . app_htmldir ."/" . $playtemplatefile;
        }
        else if ($flag == "down"){
            $cacheName = "voddown_" . $mac["vodtypeid"];
            $cachetemplatename = "template_voddown" . $mac["vodtypeid"];
            $templatepath = root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $this->page_typearr["t_downtemplate"];
        }
        
        if (chkCache($cacheName)){
            $this->html = getCache($cacheName);
        }
        else{
            $this->html = getFileByCache($cachetemplatename, $templatepath);
            setCache ($cacheName, $this->html, 0 );
        }
        
        $slink = $this->getVodLink($row["d_id"], $row["d_name"], $row["d_enname"],$row["d_addtime"], $this->page_typearr["t_id"], $this->page_typearr["t_name"], $this->page_typearr["t_enname"]);
        
        $score = $row["d_score"];
        $scorecount = $row["d_scorecount"];
        if (!isNum($score)){ $score = 0;}
        if (!isNum($scorecount)){ $scorecount = 0;}
       
        $this->html = replaceStr($this->html, "[vodinfo:hits]", "<em id=\"hit\">加载中</em><script>getHit('vod','" . $row["d_id"] . "')</script>");
        $this->html = replaceStr($this->html, "[vodinfo:fav]", "<a href=\"javascript:void(0)\" onclick=\"sitefav('http://" . app_siteurl . $slink . "','" . $row["d_name"] . "在线看');return false;\"/>我要收藏</a>");
        $this->html = replaceStr($this->html, "[vodinfo:share]", "<a href=\"javascript:void(0)\" onclick=\"copyData(document.title +'   ' + window.location.href);return false;\"/>我要分享</a>");
        $this->html = replaceStr($this->html, "[vodinfo:error]", "<a href=\"javascript:void(0)\" onclick=\"vodError('". $row["d_id"] ."','" . $row["d_name"] . "');return false;\"/>我要报错</a>");
        $this->html = replaceStr($this->html, "[vodinfo:error2]", "<a href=\"javascript:void(0)\" onclick=\"vodError2('". $row["d_id"] ."','" . $row["d_name"] . "');return false;\"/>我要报错</a>");
		$this->html = replaceStr($this->html, "[vodinfo:scorenummark]", "<em id=\"score_num\"></em><script>getScore('','" . $row["d_id"] . "')</script>");
		$this->html = replaceStr($this->html, "[vodinfo:scorepjfmark]", "<em id=\"scorepjf_num\"></em><script>getScore('pjf','" . $row["d_id"] . "')</script>");
		$this->html = replaceStr($this->html, "[vodinfo:scorecountmark]", "<em id=\"scorecount_num\"></em><script>getScore('count','" . $row["d_id"] . "')</script>");
		
        $this->html = replaceStr($this->html, "[vodinfo:goodmark]", "<a href=\"javascript:void(0)\" onclick=\"vodGood(" . $row["d_id"] . ",'good_num');return false;\"/><em id=\"good_num\"><script>getGoodBad('good','" . $row["d_id"] . "')</script></em>顶一下</a>");
        $this->html = replaceStr($this->html, "[vodinfo:badmark]", "<a href=\"javascript:void(0)\" onclick=\"vodBad(" .$row["d_id"] . ",'bad_num');return false;\"/><em id=\"bad_num\"><script>getGoodBad('bad','" . $row["d_id"] . "')</script></em>踩一下</a>");
        
        $this->html = replaceStr($this->html, "[vodinfo:history]", "<script>history_New(window.location.href,'" . $row["d_name"] . "');</script>");
            
        $this->html = replaceStr($this->html, "[vodinfo:scoremark]", "<link rel=\"stylesheet\" href=\"" . app_installdir . "images/rater.css\" type=\"text/css\"  /><script src=\"" . app_installdir . "js/jquery.rater.js\"></script> <script>vodScoreMark(" . $row["d_id"] . "," . $scorecount . "," . $score . ");</script>");
        $this->html = replaceStr($this->html, "[vodinfo:scoremark1]", "<script>vodScoreMark1(" . $row["d_id"] . "," . $scorecount . "," . $score . ");</script>");
        
        if (indexOf($this->html,"[vodinfo:textlink]")){
        	$typeLink = $this->getVodTypeLink($this->page_typearr["t_id"], $this->page_typearr["t_name"], $this->page_typearr["t_enname"], true);
        	$this->html = replaceStr($this->html, "[vodinfo:textlink]", $this->getTypeText($mac["vodtypeid"],$this->page_typearr["t_pid"],$this->page_typearr["t_name"],$typeLink,"vod")."&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $slink ."'>".$row["d_name"]."</a>"  );
        }
        if (indexOf($this->html,"[vodinfo:prelink]")){
			$this->html = replaceStr($this->html, "[vodinfo:prelink]", $this->getVodPreNextLink($row["d_id"],0));
		}
        
        if  (indexOf($this->html,"[vodinfo:nextlink]")){
       		$this->html = replaceStr($this->html, "[vodinfo:nextlink]", $this->getVodPreNextLink($row["d_id"],1));
        }
        
        $this->replaceComment (1, $row["d_id"]);
        $this->replaceMood (1, $row["d_id"]);
        $this->replaceDigg (1, $row["d_id"]);
        
        $labelRule = buildregx("\[vodinfo:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->html,$matches2);
		for($j=0;$j<count($matches2[0]);$j++)
		{
			$marktemp = $this->parse("vod", $matches2[0][$j], $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, 0);
			$this->html = replaceStr($this->html, $matches2[0][$j], $marktemp);
		}
		unset($matches2);
        
        if ($flag == "play" || $flag=="down"){
        	if($flag == "play"){ $pdviewtype= app_vodplayviewtype;$this->html = replaceStr($this->html, "[playinfo:player]", getPlayer()); }
        	if($flag == "down"){ $pdviewtype= app_voddownviewtype;$this->html = replaceStr($this->html, "[downinfo:downer]", getDowner()); }
            if ($pdviewtype <3){
            	$this->html = replaceStr($this->html,"[".$flag."info:num]",$mac["vodnum"]);
            	$this->html = replaceStr($this->html,"[".$flag."info:src]",$mac["vodsrc"]);
            	
            	$this->replacePlayName($flag,$row["d_".$flag."from"],$row["d_".$flag."url"],$mac["vodsrc"],$mac["vodnum"]);
            	$this->html = replaceStr($this->html, "[".$flag."info:info]", "<script>" . "\n" . getAddressInfo($flag,$row["d_name"], $row["d_".$flag."from"], $row["d_".$flag."server"], $row["d_".$flag."url"]) ."\n" . " </script>". "\n" );
            }
            else{
            	$playstr = getAddressInfo($flag,$row["d_name"], $row["d_".$flag."from"], $row["d_".$flag."server"], $row["d_".$flag."url"]) ;
            	$playfile = "upload/".$flag."data/" . getDatet("Ymd",$row["d_addtime"]) . "/" . $row["d_id"] . "/" . $row["d_id"] .".js";
            	$path = dirname( "../". $playfile);
				mkdirs($path);
				fwrite(fopen( "../".  $playfile,"wb"),$playstr);
            	$this->html = replaceStr($this->html, "[".$flag."info:info]", "<script src=\"". app_installdir . $playfile ."\"></script>");
            }
        }
        
        if (!( $flag=="play" && app_vodplayviewtype>3)){
        	$this->playdownlist ("play",$row["d_id"], $row["d_name"], $row["d_enname"], $row["d_addtime"], $row["d_type"], $this->page_typearr["t_name"], $this->page_typearr["t_enname"], $row["d_playfrom"], $row["d_playserver"], $row["d_playurl"]);
        }
        if (!( $flag=="down" && app_voddownviewtype>3)){
        	$this->playdownlist ("down",$row["d_id"], $row["d_name"], $row["d_enname"], $row["d_addtime"], $row["d_type"], $this->page_typearr["t_name"], $this->page_typearr["t_enname"], $row["d_downfrom"],$row["d_downserver"],$row["d_downurl"]);
        }
        
        $rc=false;
        $this->par_similar="";
        $starring = replaceStr($row["d_starring"],","," ");
        $arr1 = explode(" ",$starring);
        for ($i=0;$i<count($arr1);$i++){
        	if (!isN( $arr1[$i] )){
        		if ($rc){ $this->par_similar .= " or "; }
         		$this->par_similar .= " d_starring like '%". replaceStr($arr1[$i],"'","''") ."%' ";
         		$rc=true;
         	}
        }
        unset($arr1);
        if ($rc){  $this->par_similar = " (" . $this->par_similar . ") "; }
        
        $this->mark();
        $this->ifEx();
        $this->run();
    }
    
    function loadart($row,$typearr)
    {
    	global $cache,$mac;
    	//if (!is_array($this->page_typearr)){ $this->page_typearr = getValueByArray($cache[1], "t_id", $mac["arttypeid"]); }
        
        $this->page_typearr = $typearr;
        $mac["arttypepid"] = $this->page_typearr["t_pid"];
        $cacheName = "art_" . $mac["arttypeid"];
        $cachetemplatename = "template_art";
        $templatepath = root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $this->page_typearr["t_arttemplate"];
        
        if (chkCache($cacheName)){
            $this->html = getCache($cacheName);
        }
        else{
            $this->html = getFileByCache($cachetemplatename, $templatepath);
            setCache ($cacheName, $this->html,0);
        }
        
        $this->html = replaceStr($this->html,"[artinfo:page]",$mac["page"]);
        $this->page_type = "art";
        $contentarr = explode("[artinfo:page]",$row["a_content"]);
        if ($mac["page"]<= count($contentarr)){ $this->page_content = $contentarr[$mac["page"]-1]; }
		$this->page = $mac["page"];
		$this->page_id = $row["a_id"];
		$this->page_name = $row["a_title"];
		$this->page_enname = $row["a_entitle"];
		$this->page_addtime = $row["a_addtime"];
		$this->page_size = 1;
		$this->data_count = count($contentarr);
		$this->page_count = count($contentarr);
		$this->pageshow();
        unset($contentarr);
        
        $slink = $this->getArtLink($row["a_id"], $row["a_title"],$row["a_entitle"], $row["a_addtime"],$this->page_typearr["t_id"], $this->page_typearr["t_name"], $this->page_typearr["t_enname"],true);
        $this->html = replaceStr($this->html, "[artinfo:hits]", "<em id=\"hit\">加载中</em><script>getHit('art','" . $row["a_id"] . "')</script>");
        $this->html = replaceStr($this->html, "[artinfo:fav]", "<a href=\"javascript:void(0)\" onclick=\"sitefav('http://" . app_siteurl . $slink . "','" . $row["a_title"] . "');return false;\"/>我要收藏</a>");
        $this->html = replaceStr($this->html, "[artinfo:share]", "<a href=\"javascript:void(0)\" onclick=\"copyData(document.title +'   ' + window.location.href);return false;\"/>我要分享</a>");
        
        if (indexOf($this->html,"[artinfo:textlink]")){
        	$typeLink = $this->getArtTypeLink($this->page_typearr["t_id"], $this->page_typearr["t_name"], $this->page_typearr["t_enname"], true);
        	$this->html = replaceStr($this->html, "[artinfo:textlink]", $this->getTypeText($mac["arttypeid"],$this->page_typearr["t_pid"],$this->page_typearr["t_name"],$typeLink,"art")."&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $slink ."'>".$row["a_title"]."</a>"  );
        }
        if (indexOf($this->html,"[artinfo:prelink]")){
			$this->html = replaceStr($this->html, "[artinfo:prelink]", $this->getArtPreNextLink($row["a_id"],0));
	    }
        if  (indexOf($this->html,"[artinfo:nextlink]")){ 
       		$this->html = replaceStr($this->html, "[artinfo:nextlink]", $this->getArtPreNextLink($row["a_id"],1));
        }
        
        $this->replaceComment (2, $row["a_id"]);
        $this->replaceMood (2, $row["a_id"]);
        
        $labelRule = buildregx("\[artinfo:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->html,$matches2);
		for($j=0;$j<count($matches2[0]);$j++)
		{
			$marktemp = $this->parse("art", $matches2[0][$j], $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $row, 0);
			$this->html = replaceStr($this->html, $matches2[0][$j], $marktemp);
		}
		unset($matches2);
		
        $this->mark();
        $this->ifEx();
        $this->run();
    }
}
?>