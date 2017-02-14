<?php
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("conn.php");

$action = be("get", "action");
$action2 = be("get", "action2");
$flag = be("all", "flag");
$psize = be("all", "psize");
$stime = execTime();
$makeinterval=5;
$sql="";
$sql1="";

if (app_timming==0){ $action="main"; echo "closed"; }

$sql="";
$sql1="";
if ($flag == "art"){
	$sql = "SELECT * FROM {pre}art WHERE a_hide=0 AND a_type >0 ";
	$sql1 = "SELECT count(a_id) FROM {pre}art WHERE a_hide=0 AND a_type >0 ";
}
else{
	$sql = "SELECT * FROM {pre}vod WHERE d_hide=0 AND d_type>0 ";
	$sql1 = "SELECT count(d_id) FROM {pre}vod WHERE d_hide=0 AND d_type>0 ";
}


switch($action)
{
	case "index": makeindex();break;
	case "map": makemap();break;
	
	case "googlexml": makegoogle();break;
	case "baiduxml": makebaidu();break;
	case "rssxml": makerss();break;
	case "otherday" : makeotherday();break;
	
	case "diypage": makediypage();break;
        
	case "type": checkViewType("type"); maketype();break;
	case "typeday": checkViewType("type"); maketypeday();break;
	
	case "viewday": checkViewType("content"); makeviewday();break;
	case "viewnomake" : checkViewType("content"); makeviewnomake();break;
	
	case "topicindex": checkViewType("topic"); maketopicindex();break;
	case "topic": checkViewType("topic"); maketopic();break;
	default:   main();break;
}

function checkViewType($cs)
{
	global $flag,$makeinterval;
    if ($flag == "art"){
    	$makeinterval = app_artmakeinterval;
    	switch($cs)
		{
			case "type":
				$viewtype=app_artlistviewtype;
				$des="【分类页面】";
				break;
			case "content":
				$viewtype=app_artcontentviewtype;
				$des="【内容页面】";
				break;
			case "topic":
				$viewtype=app_arttopicviewtype;
				$des="【专题页面】";
				break;
		}
    }
    else{ 
    	$makeinterval = app_vodmakeinterval;
    	switch($cs)
		{
			case "type":
				$viewtype=app_vodlistviewtype;
				$des="【分类页面】";
				break;
			case "content":
				$viewtype=app_vodcontentviewtype;
				$des="【内容页面】";
				break;
			case "topic":
				$viewtype=app_vodtopicviewtype;
				$des="【专题页面】";
				break;
		}
    }
    if ($viewtype != 2 ){ echo $des.":浏览为静态模式时才可以生成相关页面,请切换后重试"; exit; }
}


function main()
{
}

function makeindex()
{
	global $flag,$template,$mac;
	$fpath="";
	if ($flag=="art"){
		$suffix= app_artsuffix; $fpath= $flag."index.html";
		$mac["appid"] = 20;
	}
	else{
		$suffix=app_vodsuffix; $fpath= "index.html";
		$mac["appid"] = 10;
	}
	$template->html = getFileByCache("template_index",root ."template/". app_templatedir ."/" .app_htmldir ."/". $fpath);
	$template->mark();
	$template->ifEx();
	$template->run("vod");
	$slink = "../". $fpath;
	fwrite(fopen($slink,"wb"),$template->html);
	echo  "首页生成完毕 <a target='_blank' href='". $slink."'><font color=red>浏览首页</font></a><br>";
}

function makemap()
{
	global $flag,$template,$mac;
	if ($flag=="art"){
		$suffix= app_artsuffix;
		$slink= "../artmap.".$suffix;
		$mac["appid"] = 21;
	}
	else{
		$flag = "vod";
		$suffix= app_vodsuffix;
		$slink="../map.".$suffix;
		$mac["appid"] = 11;
	}
	$template->html = getFileByCache("template_".$flag."map", root."template/".app_templatedir."/".app_htmldir."/".$flag."map.html");
	$template->mark();
	$template->ifEx();
	$template->run ($flag);
	fwrite(fopen($slink,"wb"),$template->html);
	echo  "地图页生成完毕 <a target='_blank' href='".$slink."'><font color=red>浏览地图页</font></a><br>";
}

function makegoogle()
{
	global $db,$template,$cache;
	$allmakenum = be("all","gallmakenum");
	if (!isNum($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_time FROM {pre}vod WHERE d_type >0 ORDER BY d_time DESC limit 0,".$allmakenum;
	$rs = $db->query($sql);
	$googleStr =  "<?xml version=\"1.0\" encoding=\"utf-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">"."\n";
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$googleStr .= "<url><loc>".$viewLink."</loc><lastmod>".getDatet("Y-m-d",$row["d_time"])."</lastmod><changefreq>hourly</changefreq><priority>1.0</priority></url>";
	}
	unset($rs);
	$googleStr .= "</urlset>";
	$slink = "../google.xml";
	fwrite(fopen($slink,"wb"),$googleStr);
	echo "生成完毕 <a target='_blank' href='../google.xml'><font color=red>浏览谷歌XML</font></a>  请通过<a href='http://www.google.com/webmasters/tools/' target='_blank'>http://www.google.com/webmasters/tools/</a>提交!<br>";
}

function makebaidu()
{
	global $db,$template,$cache;
	$allmakenum = be("all","ballmakenum");
	if (!isNum($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_time FROM {pre}vod WHERE d_type >0 ORDER BY d_time DESC limit 0,".$allmakenum;
	
	$rs = $db->query($sql);
	$baiduStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?><urlset>". "\n";
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$baiduStr .= "<url><loc>".$viewLink."</loc><lastmod>".getDatet("Y-m-d",$row["d_time"])."</lastmod>	<changefreq>always</changefreq><priority>1.0</priority></url>";
	}
	unset($rs);
	$baiduStr .= "</urlset>";
	$slink = "../baidu.xml";
	fwrite(fopen($slink,"wb"),$baiduStr);
	echo "生成完毕 <a target='_blank' href='../baidu.xml'><font color=red>浏览百度XML</font></a>  请通过<a href='http://news.baidu.com/newsop.html' target='_blank'>http://news.baidu.com/newsop.html</a>提交!<br>";
}

function makerss()
{
	global $db,$template,$cache;
	$allmakenum = be("all","rallmakenum");
	if (!isNum($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_time FROM {pre}vod WHERE d_type >0 ORDER BY d_time DESC limit 0,".$allmakenum;
	
	$rs = $db->query($sql);
	$rssStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?><rss version='2.0'><channel><title><![CDATA[".app_sitename."]]></title>	<description><![CDATA[".app_sitename."]]></description><link>http://".app_siteurl."</link><language>zh-cn</language><docs>".app_sitename."</docs><generator>Rss Powered By ".app_siteurl."</generator><image><url>http://".app_siteurl."/images/logo.gif</url></image>";
	
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$rssStr .= "<item><title><![CDATA[".$row["d_name"]."]]></title><link>".$viewLink."</link><author><![CDATA[".$row["d_starring"]."]]></author><pubDate>".$row["d_time"]."</pubDate><description><![CDATA[".strip_tags ( substring($row["d_content"], 150) )."]]></description></item>";
	}
	unset($rs);
	$rssStr .= "</channel></rss>";
	$slink = "../rss.xml";
	fwrite(fopen($slink,"wb"),$rssStr);
	echo "生成完毕<a target='_blank' href='../rss.xml'><font color=red>浏览RSS</font></a><br>"	;
}

function makeotherday()
{
	makegoogle();
	makebaidu();
	makerss();
	makeindex();
	makemap();
	echo "一键生成当天数据完毕!";
}

function makediypage()
{
	$fname = be("all","fname");
	if (isN($fname)){ echo "请选择自定义页面"; exit; }
	makediypagebyid ($fname);
}

function makediypagebyid($fname)
{
	global $template,$mac;
	$template->page_type = "label";
	$template->html = getFileByCache("label_".$fname,"../template/" . app_templatedir ."/" . app_htmldir . "/" .$fname);
	$template->mark();
	$tmphtml = $template->html;
	$template->vodpagelist();
	$num = $template->page_count;
		
	if (isNum($template->par_maxpage)){
		if($num>= $template->par_maxpage){
			$num = $template->par_maxpage;
			$template->page_count = intval($num);
		}
	}
	
	$template->pageshow();
	$template->ifEx();
	$template->run("other");
	
	$fname = replaceStr($fname,"label_","");
	$fname = replaceStr($fname,'$$',"/");
	
	
	$fpath = "../" . substring($fname,strrpos($fname,"/")+1);
	$fname = substring($fname, strlen($fname) - strrpos($fname,"/"),strrpos($fname,"/")+1 );
	$dlink = $fpath . $fname;
	$path = dirname($dlink);
	mkdirs($path);
	
	fwrite(fopen($dlink,"wb"),$template->html);
	echo " 生成完毕 <a target='_blank' href='".$dlink."'>".$dlink."&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
	
	$arr = explode(".",$fname);
	if (count($arr) >0){ $dname=$arr[0]; $suffix = $arr[1];} else { $dname=$arr[0] ; $suffix="html";}
    unset($arr);
    
	for($i=2;$i<=$num;$i++){
		$template->html = $tmphtml;
		$template->page = $i;
		$template->vodpagelist();
		$dlink = $fpath . $dname . $i . "." . $suffix;
		$template->page_count = intval($num);
		$template->pageshow();
		$template->ifEx();
		$template->run("other");
		fwrite(fopen($dlink,"wb"),$template->html);
		echo " 生成完毕 <a target='_blank' href='".$dlink."'>".$dlink."&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
	}
}

function maketype()
{
	global $action,$action2,$typeids,$makeinterval,$psize,$flag,$num;
	$typeid = be("get","mtype"); 
	if(!isNum($typeid)) { echo "非法传递分类mtype参数"; exit; }
	maketypebyid ($typeid);
}

function maketypebyid($typeid)
{
	global $flag,$stime,$psize,$makeinterval,$cache,$mac,$db,$template,$makesize,$action,$action2,$typeids,$startnum,$num;
	
	if ($flag=="art"){
		$typearr = getValueByArray($cache[1],"t_id",$typeid);
		$mac["arttypeid"] = $typeid;
		$sql = "select count(a_id) from {pre}art where a_type IN (". $typearr["childids"].")";
	}
	else{
		$typearr = getValueByArray($cache[0],"t_id",$typeid);
		$mac["vodtypeid"] = $typeid;
		$sql = "select count(d_id) from {pre}vod where d_type IN (". $typearr["childids"] .") ";
	}
    $template->html = getFileByCache("template_" . $flag . "list_" . $typeid, root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $typearr["t_template"]);
    if (isN($psize)){ $psize = $template->getPageListSizeByCache($flag . "page",$flag);}
    if (!isNum($psize)) { $psize = 10;}
    $tempLabelStr = $template->html;
    $nums = $db->getOne($sql);
    $pcount = ceil($nums/$psize);
    if ($nums == 0){ echo  $typeid . " 的分类没有数据</font><br>"; $pcount = 1; }
    if($action2=="all" && $pcount>5){ $pcount=5; }
    
    for ($i=1;$i<=$pcount;$i++){
        $mac["page"] = $i;
        if ($flag=="art"){
        	$template->loadlist ("art", $typearr);
        }
        else{
        	$template->loadlist ("vod", $typearr);
        }
        $typeLink = $template->getPageLink($i);
        if (app_installdir != "/"){ $typeLink = replaceStr($typeLink, app_installdir, "../");} else { $typeLink = ".." . $typeLink;}
        $path = dirname($typeLink);
        if(!file_exists($path)){
			mkdir($path);
		}
        fwrite(fopen($typeLink,"wb"),$template->html);
        echo " 第" . $i . "页 <a target='_blank' href='" . $typeLink . "'>" . replaceStr($typeLink, "../", "") . "&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
    }
}

function maketypeday()
{
	global $action,$action2,$flag,$db,$makeinterval;
	if ($flag=="art"){
		$sql2 = "SELECT DISTINCT a_type FROM {pre}art WHERE 1=1 ";
		$where = " and to_days(a_time) = to_days(now())";
	}
	else{
		$sql2 = "SELECT DISTINCT d_type FROM {pre}vod WHERE 1=1 ";
		$where = " and to_days(d_time) = to_days(now())";
	}
	$rs = $db->query($sql2.$where);
	while ($row = $db ->fetch_array($rs)){
		if ($flag=="art"){
			maketypebyid ($row["a_type"]);
		}
		else{
			maketypebyid ($row["d_type"]);
		}
	}
	unset($rs);
}

function makeviewday()
{
	global $action2,$flag,$sql,$sql1,$stime,$makeinterval,$db,$cache;
	$macpage = be("get","page");
	$num = 	be("get","num");
	if (isN($num)){ $num = 0;} else { $num = intval($num);}
	
	if ($flag=="art"){
		$where = " and to_days(a_time) = to_days(now())";
	}
	else{
		$where = " and to_days(d_time) = to_days(now())";
	}
	$sql = $sql . $where;
	$nums = $db->getOne($sql1. $where);
	
	if (isN($macpage)){ $macpage=1;} else { $macpage= intval($macpage);}
	if ($nums==0){ 
		echo "今天没有更新数据";
		exit;
	}
	
	$rs = $db->query($sql);
	while($row = $db ->fetch_array($rs)){
		if ($flag=="art"){
			$id = $row["a_type"];
			$typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);
		} 
		else {
			$id = $row["d_type"];
			$typearr = getValueByArray($cache[0],"t_id",$row["d_type"]);
		}
		
		if (strpos($ids,",".$id.",") <=0){
			$ids = $ids . $id . ",";
		}
		
		makeviewbyrs ($row,$typearr);
	}
	unset($rs);
	
	echo "ok恭喜今日数据搞定";
	if($action2=="all"){
		$idsarr = explode(",",$ids);
		for ($i=0;$i<count($idsarr);$i++){
			if (!isN( $idsarr[$i] )){
				maketypebyid ( $idsarr[$i] );
			}
		}
		echo "ok恭喜今日分类搞定";
		makeotherday();
	}
}

function makeviewnomake()
{
	global $action,$action2,$flag,$sql,$sql1,$stime,$makeinterval,$db,$cache;
	$macpage = be("get","page");
	$num = 	be("get","num");
	if (isN($num)){ $num = 0;} else { $num = intval($num);}
	
	if ($flag=="art"){
		$where = " and a_maketime < a_time or a_maketime is null ";
	}
	else{
		$where = " and d_maketime < d_time or d_maketime is null  ";
	}
	
	$sql = $sql . $where;
	$nums = $db->getOne($sql1 . $where );
	$pcount = ceil($nums/100);
	
	if (isN($macpage)){ $macpage=1;} else { $macpage= intval($macpage);}
	if ($nums==0){ 
		echo "<font color='red'>没有更新后未生成数据</font><br>";
		return;
	}
	$rs = $db->query($sql);
	while($row = $db ->fetch_array($rs)){
		if ($flag=="art"){ $typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);} else { $typearr = getValueByArray($cache[0],"t_id",$row["d_type"]);}
		makeviewbyrs ($row,$typearr);
	}
	unset($rs);
	
	if ($macpage == $pcount || $nums<100){
		echo "<font color='red'>恭喜更新后未生成数据搞定</font>";
		return;
	}
}

function makeviewbyrs($rs,$typearr)
{
	global $flag,$db,$template,$mac,$stime;
	$tname = $typearr["t_name"];
	$tpath = $typearr["t_enname"];
	
	if ($flag=="vod"){
		$rcfrom=false;
		$playstatus=true;
		$downstatus=true;
		$mac["vodtypeid"] = $rs["d_type"];
		$viewId = $rs["d_id"];
		$strName= $rs["d_name"];
		
		if(isN($rs["d_playfrom"])){
			$playstatus=false;
		}
		if(isN($rs["d_downfrom"])){
			$downstatus=false;
		}
		
		if (app_playtype == 0){
			$viewLink = $template->getVodLink($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath);
			if (app_installdir != "/"){ $viewLink = replaceStr($viewLink,app_installdir,"../");} else { $viewLink = ".." . $viewLink;}
			if (substring($viewLink,1,strlen($viewLink)-1) =="/"){ $viewLink = $viewLink . "index." . app_vodsuffix;}
			$template->loadvod ($rs,$typearr,"view");
			$template->run ("vod");
			$path = dirname($viewLink);
			mkdirs( $path);
			fwrite(fopen($viewLink,"wb"),$template->html);
		}
		
		$template->html = "";
		if($playstatus){
			if (app_vodplayviewtype ==3){
				$template->loadvod ($rs,$typearr,"play");
				$template->html = replaceStr($template->html,"[playinfo:num]","");
				$template->html = replaceStr($template->html,"[playinfo:src]","");
	            $template->html = replaceStr($template->html, "[playinfo:name]","");
	            $template->html = replaceStr($template->html, "[playinfo:urlpath]","");
				$template->run ("vod");
				$playLink = $template->getVodPlayUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath,1,1);
				$playLink = substring($playLink,strpos($playLink,"?"));
				if (app_installdir != "/"){ $playLink = replaceStr($playLink,app_installdir,"../");} else { $playLink = ".." .  $playLink;}
				if (substring($playLink,1,strlen($playLink)-1)=="/"){ $playLink = $playLink . "index." . app_vodsuffix;}
				$path = dirname($playLink);
				mkdirs($path);
				fwrite(fopen($playLink,"wb"),$template->html);
			}
			else if (app_vodplayviewtype ==4){
				$template->loadvod ($rs,$typearr,"play");
				
				if (strpos($template->html,"from=current")>0) {
					$rcfrom=true;
				}
				else{
					$template->playdownlist ("play",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_playfrom"], $rs["d_playserver"], $rs["d_playurl"]);
				}
				$tmpHtml = $template->html;
				
				$playarr1 = explode("$$$",$rs["d_playurl"]);
				$playarr2 = explode("$$$",$rs["d_playfrom"]);
				$playarr3 = explode("$$$",$rs["d_playserver"]);
				
				for ($i=0;$i<count($playarr2);$i++){
					$sserver = $playarr3[$i]; $from = $playarr2[$i]; $url= $playarr1[$i]; 
					$urlfrom = $playarr2[$i] ;
	                $urlfromshow = getVodXmlText("vodplay.xml","player", $playarr2[$i] , 1);
	                $mac["vodsrc"] = $i+1;
					$urlarr = explode("#",$url);
					
					if($rcfrom){
						$template->html = $tmpHtml;
						$template->playdownlist ("play",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_playfrom"], $rs["d_playserver"], $rs["d_playurl"]);
						$tmpHtml1 = $template->html;
					}
					else{
						$tmpHtml1 = $tmpHtml;
					}
					
					for ($j=0;$j<count($urlarr);$j++){
						if(!isN($urlarr[$j])){
							$template->html = $tmpHtml1;
							$urlone = explode("$",$urlarr[$j]);
							$urlname = "";
							$urlpath = "";
							if (count($urlone)==2){
								$urlname = $urlone[0];
								$urlpath = $urlone[1];
							}
							else{
								$urlname = "第" . $j + 1 . "集";
								$urlpath = $urlone[0];
							}
							
							$playLink = $template->getVodPlayUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath, ($i+1),($j+1));
							if (app_playtype == 1 && $i==0 && $j==0){ $viewLink = $playLink . "?" .$rs["d_id"].",1,0." . app_htmlSuffix;}
							if (app_installdir != "/"){ $playLink = replaceStr($playLink,app_installdir,"../");} else { $playLink = ".." .  $playLink;}
							if (substring($playLink,1,strlen($playLink)-1)=="/"){ $playLink = $playLink . "index." . app_vodsuffix;;}
							$template->html = replaceStr($template->html,"[playinfo:from]",$urlfrom);
							$template->html = replaceStr($template->html,"[playinfo:fromshow]",$urlfromshow);
							$template->html = replaceStr($template->html,"[playinfo:num]",($j+1));
							$template->html = replaceStr($template->html,"[playinfo:src]",($i+1));
							$template->html = replaceStr($template->html, "[playinfo:name]", $urlname );
							$template->html = replaceStr($template->html, "[playinfo:urlpath]", $urlpath );
							$template->run ("vod");
							$path = dirname($playLink);
							mkdirs($path);
							fwrite(fopen($playLink,"wb"),$template->html);
							
						}
					}
					unset($urlarr);
					unset($urlone);
				}
			}
			else if(app_vodplayviewtype ==5){
				$template->loadvod ($rs,$typearr,"play");
				$template->html = replaceStr($template->html,"[playinfo:num]","");
				$template->html = replaceStr($template->html,"[playinfo:src]","");
	            $template->html = replaceStr($template->html, "[playinfo:name]","");
	            $template->html = replaceStr($template->html, "[playinfo:urlpath]","");
	            
	            if (strpos($template->html,"from=current")>0) {
					$rcfrom=true;
				}
				else{
					$template->playdownlist ("play",$rs["d_id"], $rs["d_name"], $rs["d_enname"],$rs["d_addtime"], $rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_playfrom"], $rs["d_playserver"], $rs["d_playurl"]);
				}
				$tmpHtml = $template->html;
				
				$playarr2 = explode("$$$",$rs["d_playfrom"]);
				for ($i=0;$i<count($playarr2);$i++){
					
					$mac["vodsrc"] = $i+1;
					$urlfrom = $playarr2[$i] ;
	                $urlfromshow = getVodXmlText("vodplay.xml","player", $playarr2[$i] , 1);
	                
					$template->html = $tmpHtml;
					if($rcfrom){
						$template->playdownlist ("play",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_playfrom"], $rs["d_playserver"], $rs["d_playurl"]);
						
					}
					$template->html = replaceStr($template->html,"[playinfo:from]",$urlfrom);
					$template->html = replaceStr($template->html,"[playinfo:fromshow]",$urlfromshow);
					
					$playLink = $template->getVodPlayUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath, ($i+1),1);
					$playLink = substring($playLink,strpos($playLink,"?"));
					if (app_installdir != "/"){ $playLink = replaceStr($playLink,app_installdir,"../");} else { $playLink = ".." .  $playLink;}
					if (substring($playLink,1,strlen($playLink)-1)=="/"){ $playLink = $playLink . "index." . app_vodsuffix;}
					$template->run ("vod");
					$path = dirname($playLink);
					mkdirs($path);
					fwrite(fopen($playLink,"wb"),$template->html);
					
				}
			}
			else if(app_vodplayviewtype ==6){
				$template->loadvod ($rs,$typearr,"play");
			}
			unset($playarr1);
			unset($playarr2);
			unset($playarr3);
		}
		
		$template->html = "";
		if($downstatus){
			if (app_voddownviewtype ==3){
				$template->loadvod ($rs,$typearr,"down");
				$template->html = replaceStr($template->html,"[downinfo:num]","");
				$template->html = replaceStr($template->html,"[downinfo:src]","");
	            $template->html = replaceStr($template->html, "[downinfo:name]","");
	            $template->html = replaceStr($template->html, "[downinfo:urlpath]","");
				$template->run ("vod");
				$downLink = $template->getVoddownUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath,1,1);
				$downLink = substring($downLink,strpos($downLink,"?"));
				if (app_installdir != "/"){ $downLink = replaceStr($downLink,app_installdir,"../");} else { $downLink = ".." .  $downLink;}
				if (substring($downLink,1,strlen($downLink)-1)=="/"){ $downLink = $downLink . "index." . app_vodsuffix;}
				$path = dirname($downLink);
				mkdirs($path);
				fwrite(fopen($downLink,"wb"),$template->html);
			}
			else if (app_voddownviewtype ==4){
				$template->loadvod ($rs,$typearr,"down");
				
				if (strpos($template->html,"from=current")>0) {
					$rcfrom=true;
				}
				else{
					$template->playdownlist ("down",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_downfrom"], $rs["d_downserver"], $rs["d_downurl"]);
				}
				$tmpHtml = $template->html;
				
				$downarr1 = explode("$$$",$rs["d_downurl"]);
				$downarr2 = explode("$$$",$rs["d_downfrom"]);
				$downarr3 = explode("$$$",$rs["d_downserver"]);
				
				for ($i=0;$i<count($downarr2);$i++){
					$sserver = $downarr3[$i]; $from = $downarr2[$i]; $url= $downarr1[$i]; 
					$urlfrom = $downarr2[$i] ;
	                $urlfromshow = getVodXmlText("voddown.xml","down", $downarr2[$i] , 1);
	                $mac["vodsrc"] = $i+1;
					$urlarr = explode("#",$url);
					
					if($rcfrom){
						$template->html = $tmpHtml;
						$template->playdownlist ("down",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_downfrom"], $rs["d_downserver"], $rs["d_downurl"]);
						$tmpHtml1 = $template->html;
					}
					else{
						$tmpHtml1 = $tmpHtml;
					}
					
					for ($j=0;$j<count($urlarr);$j++){
						if(!isN($urlarr[$j])){
							$template->html = $tmpHtml1;
							$urlone = explode("$",$urlarr[$j]);
							$urlname = "";
							$urlpath = "";
							if (count($urlone)==2){
								$urlname = $urlone[0];
								$urlpath = $urlone[1];
							}
							else{
								$urlname = "第" . $j + 1 . "集";
								$urlpath = $urlone[0];
							}
							
							$downLink = $template->getVoddownUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath, ($i+1),($j+1));
							if (app_downtype == 1 && $i==0 && $j==0){ $viewLink = $downLink . "?" .$rs["d_id"].",1,0." . app_htmlSuffix;}
							if (app_installdir != "/"){ $downLink = replaceStr($downLink,app_installdir,"../");} else { $downLink = ".." .  $downLink;}
							if (substring($downLink,1,strlen($downLink)-1)=="/"){ $downLink = $downLink . "index." . app_vodsuffix;;}
							$template->html = replaceStr($template->html,"[downinfo:from]",$urlfrom);
							$template->html = replaceStr($template->html,"[downinfo:fromshow]",$urlfromshow);
							$template->html = replaceStr($template->html,"[downinfo:num]",($j+1));
							$template->html = replaceStr($template->html,"[downinfo:src]",($i+1));
							$template->html = replaceStr($template->html, "[downinfo:name]", $urlname );
							$template->html = replaceStr($template->html, "[downinfo:urlpath]", $urlpath );
							$template->run ("vod");
							$path = dirname($downLink);
							mkdirs($path);
							fwrite(fopen($downLink,"wb"),$template->html);
							
						}
					}
					unset($urlarr);
					unset($urlone);
				}
			}
			else if(app_voddownviewtype ==5){
				$template->loadvod ($rs,$typearr,"down");
				$template->html = replaceStr($template->html,"[downinfo:num]","");
				$template->html = replaceStr($template->html,"[downinfo:src]","");
	            $template->html = replaceStr($template->html, "[downinfo:name]","");
	            $template->html = replaceStr($template->html, "[downinfo:urlpath]","");
	            
	            if (strpos($template->html,"from=current")>0) {
					$rcfrom=true;
				}
				else{
					$template->playdownlist ("down",$rs["d_id"], $rs["d_name"], $rs["d_enname"],$rs["d_addtime"], $rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_downfrom"], $rs["d_downserver"], $rs["d_downurl"]);
				}
				$tmpHtml = $template->html;
				
				$downarr2 = explode("$$$",$rs["d_downfrom"]);
				for ($i=0;$i<count($downarr2);$i++){
					
					$mac["vodsrc"] = $i+1;
					$urlfrom = $downarr2[$i] ;
	                $urlfromshow = getVodXmlText("voddown.xml","down", $downarr2[$i] , 1);
	                
					$template->html = $tmpHtml;
					if($rcfrom){
						$template->playdownlist ("down",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_downfrom"], $rs["d_downserver"], $rs["d_downurl"]);
						
					}
					$template->html = replaceStr($template->html,"[downinfo:from]",$urlfrom);
					$template->html = replaceStr($template->html,"[downinfo:fromshow]",$urlfromshow);
					
					$downLink = $template->getVoddownUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath, ($i+1),1);
					$downLink = substring($downLink,strpos($downLink,"?"));
					if (app_installdir != "/"){ $downLink = replaceStr($downLink,app_installdir,"../");} else { $downLink = ".." .  $downLink;}
					if (substring($downLink,1,strlen($downLink)-1)=="/"){ $downLink = $downLink . "index." . app_vodsuffix;}
					$template->run ("vod");
					$path = dirname($downLink);
					mkdirs($path);
					fwrite(fopen($downLink,"wb"),$template->html);
					
				}
			}
			else if(app_voddownviewtype ==6){
				$template->loadvod ($rs,$typearr,"down");
			}
			unset($downarr1);
			unset($downarr2);
			unset($downarr3);
		}
		$db->Update("{pre}vod",array("d_maketime"),array(date('Y-m-d H:i:s',time())),"d_id=".$rs["d_id"]);
	}
	else{
		$mac["arttypeid"] = $rs["a_type"];
		$viewId = $rs["a_id"];
		$strName= $rs["a_title"];
		$urlarr = explode("[artinfo:page]",$rs["a_content"]);
		$urlarrlen = count($urlarr);
		for ($i=1;$i<=$urlarrlen;$i++){
			$mac["page"] = $i;
			$template->page_type = "art";
			$template->page_typearr = $typearr;
			$template->page_id = $rs["a_id"];
			$template->page_name = $rs["a_title"];
			$template->page_enname = $rs["a_entitle"];
			$playLink = $template->getPageLink($i);
			if (app_installdir != "/"){ $playLink = replaceStr($playLink,app_installdir,"../");} else { $playLink = ".." . $playLink;}
			if (substring($playLink,1,strlen($playLink)-1) =="/"){ $playLink = $playLink . "index." . app_artsuffix;}
			if($i==1){ $viewLink = $playLink; }
			$template->loadart ($rs,$typearr);
			$template->run ("art");
			$path = dirname($playLink);
			mkdirs($path);
			fwrite(fopen($playLink,"wb"),$template->html);
		}
		unset($urlarr);
		$db->Update("{pre}art",array("a_maketime"),array(date('Y-m-d H:i:s',time())),"a_id=".$rs["a_id"]);
	}
	echo $strName . " <a target='_blank' href='".$viewLink."'>&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
}

function maketopicindex()
{
	global $flag,$db,$mac,$template;
	$t1 = root."template/".app_templatedir."/".app_htmldir."/".$flag."topic.html";
	$template->html =  getFileByCache("template_".$flag."topic",$t1);
	$tempLabelStr = $template->html;
	
	if($flag=="art"){
		$mac["appid"] = 22;
	}
	else{
		$mac["appid"] = 12;
	}
	$psize = $template->getPageListSizeByCache("topicpage",$flag);
	if (isN($psize)){ $psize = 10;}
	$sql = "select t_id from {pre}".$flag."_topic";
	$nums = $db->getOne( "select count(t_id) from {pre}".$flag."_topic" );
	$pcount=ceil($nums/$psize);
	if ($nums==0){ echo "<font color='red'>没有专题数据!</font><br>" ; $pcount =1; }
	echo "正在开始生成专题首页...<br>";
	
	for ($i=1;$i<=$pcount;$i++){
		$template->page = $i;
		$template->page_type = $flag . "topicindex";
		$template->html = $tempLabelStr;
		$template->topicpagelist();
		$template->pageshow();
		$template->mark();
		$template->ifEx();
		$template->run ("other");
		$topicLink = $template->getPageLink($i);
		if (app_installdir != "/"){	$topicLink = replaceStr($topicLink,app_installdir,"../");} else	{ $topicLink = ".." .  $topicLink;}
		$path = dirname($topicLink);
		mkdirs($path);
		fwrite(fopen($topicLink,"wb"),$template->html);
		echo "第".$i."页 生成完毕 <a target='_blank' href='".$topicLink."'>&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
		ob_flush();
		flush();
	}
}

function maketopic()
{
	$topic=  be("get","mtopic");
	if(!isNum($topic)){ echo "非法传递专题mtopic参数"; return; }
	makeTopicById ($topic);
	echo "生成专题列表完成";exit;
}

function makeTopicById($topicid)
{
	global $flag,$db,$mac,$cache,$template;
	if ($flag =="art"){
		$mac["arttopicid"] = $topicid;
		$typearr = getValueByArray($cache[3],"t_id",$topicid);
		$sql = "select  a_id from {pre}art where a_topic = ".$topicid."";
		$sql1 = "select count(*) from {pre}art where a_topic = ".$topicid."";
	}
	else{
		$mac["vodtopicid"] = $topicid;
		$typearr = getValueByArray($cache[2],"t_id",$topicid);
		$sql = "select  d_id from {pre}vod where d_topic = ".$topicid."";
		$sql1 = "select  count(*) from {pre}vod where d_topic = ".$topicid."";
	}
	$template->html = getFileByCache("template_" . $flag . "topiclist_" . $topicid,root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $typearr["t_template"]);
	$psize = $template->getPageListSizeByCache($flag."page",$flag);
	if (!isNum($psize)){ $psize = 10;}
	$nums = $db->getOne($sql1);
	$pcount=ceil($nums/$psize);
	if ($nums==0){ echo "<font color='red'>ID为 ".$topicid." 的专题没有数据</font><br>" ; $pcount =1;}
	echo "正在开始生成专题<font color='red'>" . $typearr["t_name"] . "</font>的列表<br>";
	
	for ($i=1;$i<=$pcount;$i++){
		$mac["page"] = $i;
		$template->loadtopic ($flag,$typearr);
		$topicLink = $template->getPageLink($i);
		if (app_installdir != "/" ){ $topicLink = replaceStr($topicLink,app_installdir,"../");} else { $topicLink = ".." .$topicLink;}
		$path = dirname($topicLink);
		mkdirs($path);
		fwrite(fopen($topicLink,"wb"),$template->html);
		echo $typearr["t_name"] . " 生成完毕 <a target='_blank' href='".$topicLink."'>".$topicLink."&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
		ob_flush();flush();
	}
}
?>