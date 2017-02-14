<?php
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("conn.php");
require_once ("pinyin.php");

$action = be("all","action");
$rtype = be("all", "rtype");
$rpage = be("all", "rpage");
$cjurl = be("all", "cjurl");
$rkey = be("all", "rkey");
$rday = be("all", "rday");
$fa = be("all", "fa");
$xt = be("all","xt");
$rid = be("all","rid");
$ct = be("all","ct");
$cjflag = be("all","cjflag");
$backurl = getReferer();
$typearr=array();

if (!isNum($rtype)) { $rtype=0;} else {$rtype = intval($rtype);}
if (!isNum($rpage)) { $rpage=1;} else {$rpage = intval($rpage);}
if (!isNum($rday)) { $rday=1;} else {$rday = intval($rday);}
if ($rpage<1) {$rpage=1;}

if (app_timming==0){ $action="main"; echo "closed"; }
switch($action)
{
	case "cjsel":cjsel();break;
	case "cjday":cjday();break;
	case "cjtype":cjtype();break;
	case "cjall":cjall();break;
	default:main();break;
}
unset($typearr);
dispseObj();

function cjsel()
{
	global $action,$rtype,$rpage,$rkey,$rday,$cjurl,$xt,$ct,$rid,$cjflag;
    $ids = be("arr", "ids");
    if (isN($ids)) { errMsg ("采集提示", "请选择采集数据");}
    if($xt=="1"){
    	$url = $cjurl . "?ac=videolist&rid=".$rid."&ids=" . $ids;
    }
    else if($xt=="2"){
    	$url = $cjurl . "-action-ids-vodids-".$ids."-cid--play--inputer--wd--h-0-p-1";
    }
    else{
    	$url = $cjurl . "?action=cjsel&ids=" . $ids;
    }
    insertdata($url, "cjsel");
}

function cjday()
{
    global $action,$rtype,$rpage,$rkey,$rday,$cjurl,$xt,$ct,$rid,$cjflag;
    if($xt=="1"){
    	$url = $cjurl . "?ac=videolist&rid=".$rid."&h=".$rday."&pg=" . $rpage;
    }
    else if($xt=="2"){
    	$url = $cjurl . "-action-day-vodids--cid--play--inputer--wd--h-".$rday."-p-".$rpage;
    }
    else{
    	$url = $cjurl . "?action=cjday&rday=".$rday."&rpage=" . $rpage;
    }
    insertdata($url, "cjday");
}

function cjtype()
{
    global $action,$rtype,$rpage,$rkey,$rday,$cjurl,$xt,$ct,$rid,$cjflag,$backurl;
    
    $flag = false;
    if (isN($rtype)){
        showMsg ("请先进入分类,否则无法使用采集分类!", $backurl); exit;
    }
    if($xt=="1"){
    	$url = $cjurl . "?ac=videolist&rid=".$rid."&pg=" . $rpage . "&t=" . $rtype;
    }
    else if($xt=="2"){
    	$url = $cjurl . "-action-all-vodids--cid-".$rtype."-play--inputer--wd--h-0-p-".$rpage;
    }
    else{
    	$url = $cjurl . "?action=cjtype&rpage=" . $rpage . "&rtype=". $rtype;
    }
    insertdata($url, "cjtype");
}

function cjall()
{
    global $action,$rtype,$rpage,$rkey,$rday,$cjurl,$xt,$ct,$rid,$clflag;
    if($xt=="1"){
    	$url = $cjurl . "?ac=videolist&rid=".$rid."&pg=". $rpage;
    }
    else if($xt=="2"){
    	$url = $cjurl . "-action-all-vodids--cid--play--inputer--wd--h-0-p-" . $rpage;
    }
    else{
    	$url = $cjurl . "?action=cjall&rpage=". $rpage;
    }
    insertdata($url, "cjall");
}

function getFrom($f)
{
	switch($f)
	{
		case "百度影音" : $f="baidu";break;
		case "bdhd": $f="baidu";break;
		case "皮皮影音": $f="pipi";break;
		case "闪播Pvod": $f="pvod";break;
		case "迅播高清": $f="gvod";break;
		case "yuku":
		case "优酷":
			$f="youku";break;
		case "qq播客": $f="qq";break;
		default : break;
	}
	return $f;
}

function getVUrl($u)
{
	$arr1 = explode("#",$u);
	$rc=false;
	for ($i=0;$i<count($arr1);$i++){
		if (!isN($arr1[$i])){
			if (strpos( $arr1[$i],"$$") > 0){
				$arr3 = explode("$$",$arr1[$i]);
				$arr2= explode("$",$arr3[1]);
			}
			else{
				$arr2= explode("$",$arr1[$i]);
			}
			if ($rc){ $str = $str . "#";}
			if (count($arr2)==3 || count($arr2)==2){
				$str = $str . $arr2[0] . "$" . $arr2[1];
			}
			else{
				$str = $str . $arr2[0];
			}
			$rc = true;
			unset($arr2);
			unset($arr3);
		}
	}
	unset($arr1);
	return $str;
}
function insertdata($url, $cjtype)
{
	global $db,$action,$rtype,$rpage,$rkey,$rday,$cjurl,$xt,$ct,$rid,$cjflag,$backurl;
    
    if($xt=="1"){
    	$xn_vod = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des>([\s\S]*?)<\/video>/';
    	$xn_url = '/<dd flag="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/dd>/';
    	$xn_vod_time=1;
    	$xn_vod_id=2;
	    $xn_vod_name=4;
	    $xn_vod_type=3;
	    $xn_vod_pic=6;
	    $xn_vod_language=7;
	    $xn_vod_area=8;
	    $xn_vod_year=9;
	    $xn_vod_state=10;
	    $xn_vod_remarks=11;
	    $xn_vod_starring=12;
	    $xn_vod_directed=13;
	    $xn_vod_urls=14;
	    $xn_vod_des=15;
    }
    else if($xt=="2"){
    	$xn_vod = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><dt>([\s\S]*?)<\/dt><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des><reurl>([\s\S]*?)<\/reurl><\/video>/';
    	$xn_url = '/<dd flag="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/dd>/';
    	$xn_vod_time=1;
    	$xn_vod_id=2;
	    $xn_vod_name=4;
	    $xn_vod_type=3;
	    $xn_vod_pic=7;
	    $xn_vod_language=8;
	    $xn_vod_area=9;
	    $xn_vod_year=10;
	    $xn_vod_state=11;
	    $xn_vod_remarks=12;
	    $xn_vod_starring=13;
	    $xn_vod_directed=14;
	    $xn_vod_urls=15;
	    $xn_vod_des=16;
    }
    else{
    	$xn_vod = '/<vod><id>([0-9]+)<\/id><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><state>([\s\S]*?)<\/state><type>([\s\S]*?)<\/type><starring><\!\[CDATA\[([\s\S]*?)\]\]><\/starring><directed><\!\[CDATA\[([\s\S]*?)\]\]><\/directed><pic>([\s\S]*?)<\/pic><time>([\s\S]*?)<\/time><year>([\s\S]*?)<\/year><area><\!\[CDATA\[([\s\S]*?)\]\]><\/area><language><\!\[CDATA\[([\s\S]*?)\]\]><\/language><urls>([\s\S]*?)<\/urls><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des><\/vod>/';
    	$xn_url = '/<url from="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/url>/';
    	$xn_vod_id=1;
	    $xn_vod_name=2;
	    $xn_vod_remarks=3;
	    $xn_vod_state=4;
	    $xn_vod_type=5;
	    $xn_vod_starring=6;
	    $xn_vod_directed=7;
	    $xn_vod_pic=8;
	    $xn_vod_time=9;
	    $xn_vod_year=10;
	    $xn_vod_area=11;
	    $xn_vod_language=12;
	    $xn_vod_des=14;
	    $xn_vod_urls=13;
	    $cjflag="";
    }
    
    $html = getPage($url, "utf-8");
    
    if (html==false) { 
    	echo "获取数据失败";
    	exit;
    }
    
    if($xt=="1" || $xt=="2"){
    	$pagecount = intval(getBody($html,"pagecount=\"","\" pagesize"));
    }
    else{
    	$pagecount = intval(getBody($html,"<pagecount>","</pagecount>"));
    }
    if($pagecount==0){
    	echo "没有任何可用数据";
        exit;
    }
    
	
    preg_match_all($xn_vod,$html,$array3);
    $i=0;
    foreach($array3[1] as $key=>$value){
        $rc = false;
        $vodid = $array3[$xn_vod_id][$key];
        $vodname = $array3[$xn_vod_name][$key];
        $vodremarks = $array3[$xn_vod_remarks][$key];
        $vodstate = $array3[$xn_vod_state][$key];
        $vodtype = $cjflag . $array3[$xn_vod_type][$key];
        $vodstarring = $array3[$xn_vod_starring][$key];
        $voddirected = $array3[$xn_vod_directed][$key];
        $vodpic = $array3[$xn_vod_pic][$key];
        $vodtime = $array3[$xn_vod_time][$key];
        $vodyear = $array3[$xn_vod_year][$key];
        $vodarea = $array3[$xn_vod_area][$key];
        $vodlanguage = $array3[$xn_vod_language][$key];
        $voddes = $array3[$xn_vod_des][$key];
        $vodurls = $array3[$xn_vod_urls][$key];
        preg_match_all($xn_url,$vodurls,$array4);
        
        $vodsubname = "";
        $vodcolor = "";
        $vodhitstime = "";
        $voddownfrom = "";
        $voddownserver = "";
        $voddownurl = "";
        $vodplayserver = "0";
        $vodfrom="";
        $vodurl="";
        
        $vodname = replaceStr($vodname, "'", ""); if (strlen($vodname) > 255) { $vodname = substring($vodname,255);}
        $vodenname = Hanzi2PinYin($vodname); if (strlen($vodenname) > 255) { $vodenname = substring($vodenname, 255);}
        if(!isN($vodenname)) { $vodletter = strtoupper(substring($vodenname,1)); }
        $vodstate = replaceStr($vodstate, "[", ""); $vodstate = replaceStr($vodstate, "]", ""); if (!isNum($vodstate)){ $vodstate = 0;}
        $vodstarring = replaceStr($vodstarring, "'", "");  $vodstarring = replaceStr($vodstarring, "、", " "); 
        $vodstarring = replaceStr($vodstarring, ",", " ");   $vodstarring = replaceStr($vodstarring, "，", " ");  
        $vodstarring = replaceStr($vodstarring, "  ", " ");
        if (strlen($vodstarring) > 255){ $vodstarring = substring($vodstarring, 255);}
        
        $vodyear = replaceStr($vodyear, "'", "");  if (strlen($vodyear) > 32){ $vodyear = substring($vodyear, 32);}
        $vodlanguage = replaceStr($vodlanguage, "'", "");  if (strlen($vodlanguage) > 32){ $vodlanguage = substring($vodlanguage, 32);}
        $vodarea = replaceStr($vodarea, "'", "");  if (strlen($vodarea) > 32){ $vodarea = substring($vodarea, 32);}
        $vodpic = replaceStr($vodpic, "'", ""); if (strlen($vodpic) > 255){ $vodpic = substring($vodpic, 255);}
        $voddes = htmlDecode($voddes); $voddes = replaceStr($voddes, "'", "");
        $vodtype = getTypeID($vodtype);  if (!isNum($vodtype)){ $vodtype = 0;}
        
        if ($vodtype > 0){
            $sql = "SELECT * FROM {pre}vod WHERE d_name ='" . $vodname . "' ";
            if(app_vodmaccjsname==1){ $sql.= " and d_type=".$vodtype; }
            $row = $db->getRow($sql);
            if(!$row){
                foreach($array4[1] as $key=>$value){
                    if ($rc){ $vodfrom .= "$$$"; $vodurl .= "$$$";}
                    $vodfrom .= getFrom($value);
					if($xt=="1"){
						$vodurl .=  getVUrl($array4[2][$key]);
					}
					else{
						$vodurl .=  $array4[2][$key];
					}
			        if (substring($vodurl, 1,strlen($vodurl)-1) == Chr(13)){ $vodurl = substring($vodurl, strlen($vodurl)-1);}
			        $vodurl = replaceStr($vodurl, Chr(10), "#");
			        $vodurl = replaceStr($vodurl, Chr(13), "#");
			        $vodurl = replaceStr($vodurl, "##", "#");
			        $vodurl = replaceStr($vodurl, "'", "''");
                    $rc = true;
                }
                if($ct=="1"){
                	$voddownfrom=$vodfrom;
                	$voddownserver="0";
                	$voddownurl=$vodurl;
                	$vodfrom="";
                	$vodplayserver="";
                	$vodurl="";
                }
                else{
                	$vodplayserver="0";
                	$voddownfrom = "";
			        $voddownserver = "";
			        $voddownurl = "";
                }
                $db->Add ("{pre}vod", array("d_type", "d_name", "d_subname", "d_enname","d_letter" , "d_state", "d_color", "d_content", "d_remarks", "d_pic", "d_level", "d_hits", "d_starring", "d_directed", "d_year", "d_area", "d_language", "d_addtime", "d_time", "d_playfrom", "d_playserver","d_playurl","d_downfrom" , "d_downserver", "d_downurl"), array($vodtype, $vodname, $vodsubname, $vodenname, $vodletter, $vodstate, $vodcolor, $voddes, $vodremarks, $vodpic, 0, 0, $vodstarring, $voddirected, $vodyear, $vodarea, $vodlanguage, date('Y-m-d H:i:s',time()), date('Y-m-d H:i:s',time()), $vodfrom, $vodplayserver, $vodurl, $voddownfrom, $voddownserver, $voddownurl));
                
                wTips ($vodname, "新增数据");
	        }
			else{
				if($ct=="1"){
					
	                $tmpplayfrom = $row["d_downfrom"];
	                $tmpplayserver = $row["d_downserver"];
	                $tmpplayurl = $row["d_downurl"];
	                $col1 = "d_downfrom";
	                $col2 = "d_downserver";
	                $col3 = "d_downurl";
				}
				else{
					
	                $tmpplayfrom = $row["d_playfrom"];
	                $tmpplayserver = $row["d_playserver"];
	                $tmpplayurl = $row["d_playurl"];
	                $col1 = "d_playfrom";
	                $col2 = "d_playserver";
	                $col3 = "d_playurl";
				}
				
                
                foreach($array4[1] as $key=>$value){
					$vodfrom = getFrom($value);
					if($xt=="1"){
						$vodurl = getVUrl($array4[2][$key]);
					}
					else{
						$vodurl = $array4[2][$key];
					}
                    
			        if (substring($vodurl, 1,strlen($vodurl)-1) == Chr(13)){ $vodurl = substring($vodurl, strlen($vodurl)-1);}
			        $vodurl = replaceStr($vodurl, Chr(10), "#");
			        $vodurl = replaceStr($vodurl, Chr(13), "#");
			        $vodurl = replaceStr($vodurl, "##", "#");
			        $vodurl = replaceStr($vodurl, "'", "''");
			        
                    if ($tmpplayurl ==$vodurl){
                         $resultdes = "无需更新播放地址";
                         continue;
                    }
                    else if(isN($vodfrom)){
                    	wTips ($vodname, "播放器类型为空，跳过");
        				continue;
                    }
                    else if (isN($tmpplayurl) || strpos(",".$row["d_playfrom"], $vodfrom) <= 0){
                        $resultdes = "新增播放地址组";
                        $tmpplayurl .= "$$$" . $vodurl;
                        $tmpplayfrom .= "$$$" . $vodfrom;
                        $tmpplayserver .= "$$$" . $vodplayserver;
                    }
                    else{
                        $resultdes = "更新播放地址";
                        $arr1 = explode("$$$",$tmpplayurl);
                        $arr2 = explode("$$$",$tmpplayfrom);
                        $rc = false; $tmpplayurl = "";
                        
                        for ($k=0;$k<count($arr2);$k++){
                            if ($rc){ $tmpplayurl .= "$$$";}
                            if(count($arr1)>=$k){
                            	if ($arr2[$k] == $vodfrom){ $arr1[$k] = $vodurl;}
                            	$tmpplayurl .= $arr1[$k];
                            }
                            else{
                            	$tmpplayurl .= $vodurl;
                            }
                            $rc = true;
                        }
	                }
	            }
                $tmpplayurl = replaceStr($tmpplayurl, Chr(13), "#");
                if (strpos(",".$row["d_pic"], "http:") <= 0) { $vodpic=$row["d_pic"] ; }
                $db->Update ("{pre}vod",array("d_state","d_pic","d_remarks","d_time",$col1,$col2,$col3),array($vodstate,$vodpic,$vodremarks,date('Y-m-d H:i:s',time()),$tmpplayfrom,$tmpplayserver,$tmpplayurl),"d_id=".$row["d_id"]);
                wTips ($vodname, $resultdes);
            }
            unset($row);
	    }
        else{
            wTips ($vodname, "没有绑定分类、跳过");
        }
        unset($array4);
	}
	unset($array3);
    
    echo "ok第".$rpage."页完成,共".$pagecount."页<br>";
	
	if($rpage < $pagecount){
		$rpage = $rpage+1;
		cjday();
	}
}


function wTips($vname,$vdes)
{
    
}

function main()
{
    
}

function getTypeID($servertype)
{
    global $db,$typearr;
	if (empty($typearr)){
    	$sql = "select * from {pre}vod_type where length(t_union)>0 ";
    	$typearr = $db->queryArray($sql);
    }
	if(is_array($typearr)){
		for($i=0;$i<count($typearr);$i++){
			$unionids = $typearr[$i]["t_union"];
			if ( strpos(",".$unionids,",".$servertype.",")>0 ){
				return $typearr[$i]["t_id"];
				break;
			}
		}
	}
	return "";
}
?>