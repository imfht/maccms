<?php
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");

chkLogin();
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

switch($action)
{
	case "bindunion": headAdmin ("联盟资源"); bindunion();break;
	case "bindunionsave": bindunionsave();break;
	case "cjsel": headAdmin ("联盟资源"); cjsel();break;
	case "cjday": headAdmin ("联盟资源"); cjday();break;
	case "cjtype": headAdmin ("联盟资源"); cjtype();break;
	case "cjall": headAdmin ("联盟资源"); cjall();break;
	case "breakpoint": headAdmin ("联盟资源"); breakpoint();break;
	case "list": headAdmin ("联盟资源");mlist();break;
	case "glist" : glist();break;
	default: headAdmin ("联盟资源"); main();break;
}
unset($typearr);
footAdmin();

function breakpoint()
{
    echo gBreakpoint("../upload/maccjbreakpoint"). "正在载入断点续传数据，请稍后......";
}

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
		case "百度影音" :
		case "bdhd": 
			$f="baidu";break;
		case "皮皮影音": $f="pipi";break;
		case "闪播Pvod": $f="pvod";break;
		case "迅播高清": $f="gvod";break;
		case "肥佬影音": $f="fvod";break;
		case "非凡影音": $f="ffhd";break;
		case "迅雷看看": $f="kankan";break;
		case "吉吉影音": 
		case "吉吉": 
			$f="jjvod";break;
		case "西瓜影音": 
		case "西瓜": 
			$f="xigua";
			break;
		case "乐视": 
		case "乐视网":
			$f="letv";break;
		case "搜狐": $f="sohu";break;
		case "土豆": $f="tudou";break;
		case "奇艺": $f="qiyi";break;
		case "影音先锋": 
		case "先锋影音": 
			$f="xfplay";break;
		case "yuku":
		case "优酷":
		case "优酷视频":
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
    
    
    wBreakpoint ("../upload/maccjbreakpoint", "admin_maccj.php?action=".$action ."&xt=".$xt. "&ct=".$ct ."&rid=". $rid ."&cjflag=".$cjflag . "&rpage=" . $rpage . "&rtype=" . $rtype . "&rkey=" . $rkey . "&cjurl=" . $cjurl);
    
    if($xt=="1"){
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagecount = 2;
    	
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
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagecount = 2;
    	
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
    	$xn_list = '/<pagecount>([\s\S]*?)<\/pagecount>/';
    	$xn_pagecount = 1;
    	
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
    	echo "<table class=\"tb\"><tr><td colspan=2>&nbsp;<a href=\"javascript:void(0)\" onclick=\"location.reload();\">获取数据失败，请点击我重试</a></td></tr></table>";
    	exit;
    }
    
    preg_match($xn_list ,$html,$array1);
	$pagecount = $array1[$xn_pagecount];
	
    if($pagecount==0){
    	echo "<table class=\"tb\"><tr><td><br>没有任何可用数据<script language=\"javascript\">setTimeout(\"gonextpage();\",5000);function gonextpage(){location.href='?action=list&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&cjurl=" . $cjurl . "';}</script></td></tr></table>";
        exit;
    }
    echo "<table class=\"tb\"><tr><td colspan=2>视频采集地址&nbsp;" . $url. "</td></tr><tr><td colspan=2>&nbsp;共".$pagecount."页，正在采集第" . $rpage . "页</td></tr>";
    
	
	$sql_insert='';
	$sql_update=array();
	$rc_insert=false;
	
    preg_match_all($xn_vod,$html,$array3);
    $i=0;
    foreach($array3[1] as $key=>$value){
        $rc = false;
        $vodid = $array3[$xn_vod_id][$key];
        $vodname = $array3[$xn_vod_name][$key];
        $vodremarks = trim($array3[$xn_vod_remarks][$key]);
        $vodstate = $array3[$xn_vod_state][$key];
        $vodtype = $cjflag . $array3[$xn_vod_type][$key];
        $vodstarring = trim($array3[$xn_vod_starring][$key]);
        $voddirected = trim($array3[$xn_vod_directed][$key]);
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
        
        $vodfrom="";
        $vodurl="";
        
        $vodname = str_replace("'", "",$vodname); if (strlen($vodname) > 255) { $vodname = substring($vodname,255);}
        $vodenname = Hanzi2PinYin($vodname); if (strlen($vodenname) > 255) { $vodenname = substring($vodenname, 255);}
        if(!isN($vodenname)) { $vodletter = strtoupper(substring($vodenname,1)); }
        $vodstate = str_replace(array("[","]"),array("",""),$vodstate); if (!isNum($vodstate)){ $vodstate = 0;}
        $vodstarring = str_replace(array("'","、",",","  "), array(""," "," "," "),$vodstarring);
        if (strlen($vodstarring) > 255){ $vodstarring = substring($vodstarring, 255);}
        
        $vodyear = str_replace("'", "",$vodyear);  if (strlen($vodyear) > 32){ $vodyear = substring($vodyear, 32);}
        $vodlanguage = str_replace("'", "",$vodlanguage);  if (strlen($vodlanguage) > 32){ $vodlanguage = substring($vodlanguage, 32);}
        $vodarea = str_replace("'", "",$vodarea);  if (strlen($vodarea) > 32){ $vodarea = substring($vodarea, 32);}
        $vodpic = str_replace("'", "",$vodpic); if (strlen($vodpic) > 255){ $vodpic = substring($vodpic, 255);}
        $voddes = htmlDecode($voddes); $voddes = str_replace("'", "",$voddes);
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
			        $vodurl = str_replace(array(Chr(10),Chr(13),"'"), array("#","#","''"),$vodurl);
			        $vodurl = str_replace("##", "#",$vodurl);
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
                //$db->Add ("{pre}vod", array("d_type", "d_name", "d_subname", "d_enname","d_letter" , "d_state", "d_color", "d_content", "d_remarks", "d_pic", "d_level", "d_hits", "d_starring", "d_directed", "d_year", "d_area", "d_language", "d_addtime", "d_time", "d_playfrom", "d_playserver","d_playurl","d_downfrom" , "d_downserver", "d_downurl"), array($vodtype, $vodname, $vodsubname, $vodenname, $vodletter, $vodstate, $vodcolor, $voddes, $vodremarks, $vodpic, 0, 0, $vodstarring, $voddirected, $vodyear, $vodarea, $vodlanguage, date('Y-m-d H:i:s',time()), date('Y-m-d H:i:s',time()), $vodfrom, $vodplayserver, $vodurl, $voddownfrom, $voddownserver, $voddownurl));
                if($rc_insert){ $sql_insert.=','; }
                $sql_insert.= "('".$vodtype."','".$vodname."','".$vodsubname."','".$vodenname."','".$vodletter."','".$vodstate."','".$vodcolor."','".$voddes."','".$vodremarks."','".$vodpic."',0,0,'".$vodstarring."','".$voddirected."','".$vodyear."','".$vodarea."','".$vodlanguage."','".date('Y-m-d H:i:s',time())."','".date('Y-m-d H:i:s',time())."','".$vodfrom."','".$vodplayserver."','".$vodurl."','".$voddownfrom."','".$voddownserver."','".$voddownurl."')";
                $rc_insert=true;
                
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
			        $vodurl = str_replace(array(Chr(10),Chr(13),"'"), array("#","#","''"),$vodurl);
			        $vodurl = str_replace("##", "#",$vodurl);
			        
                    if ($tmpplayurl ==$vodurl){
                         $resultdes = "无需更新地址";
                         continue;
                    }
                    else if(isN($vodfrom)){
                    	wTips ($vodname, "类型为空，跳过");
        				continue;
                    }
                    else if (isN($tmpplayurl) || strpos(",".$tmpplayfrom, $vodfrom) <= 0){
                        $resultdes = "新增地址组";
                        $tmpplayurl .= "$$$" . $vodurl;
                        $tmpplayfrom .= "$$$" . $vodfrom;
                        $tmpplayserver .= "$$$" . $vodplayserver;
                    }
                    else{
                        $resultdes = "更新地址";
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
                if (strpos(",".$row["d_pic"], "http:") <= 0) { $vodpic=$row["d_pic"] ; }
                //$db->Update ("{pre}vod",array("d_state","d_pic","d_remarks","d_time",$col1,$col2,$col3),array($vodstate,$vodpic,$vodremarks,date('Y-m-d H:i:s',time()),$tmpplayfrom,$tmpplayserver,$tmpplayurl),"d_id=".$row["d_id"]);
                
                $sql="update {pre}vod set d_state='".$vodstate."',d_pic='".$vodpic."',d_remarks='".$vodremarks."',d_time='".date('Y-m-d H:i:s',time())."',".$col1."='".$tmpplayfrom."',".$col2."='".$tmpplayserver."',".$col3."='".$tmpplayurl."'  where d_id=".$row["d_id"].';';
                
                array_push($sql_update,$sql);
                
                
                wTips ($vodname, $resultdes);
            }
            unset($row);
	    }
        else{
            wTips ($vodname, "没有绑定分类、跳过");
        }
        unset($array4);
	}
	if(!empty($sql_insert)){
		$sql_insert = "insert into {pre}vod (d_type, d_name, d_subname, d_enname,d_letter , d_state, d_color, d_content, d_remarks, d_pic, d_level, d_hits, d_starring, d_directed, d_year, d_area, d_language, d_addtime, d_time, d_playfrom, d_playserver,d_playurl,d_downfrom , d_downserver, d_downurl) values ". $sql_insert;
		$db->query($sql_insert);
	}
	if(!empty($sql_update)){
		foreach($sql_update as $s){
			$db->query($s);
		}
	}
	
	unset($array3,$sql_insert,$sql_update);
    echo "</table>";
    
    if ($action == "cjday" || $action =="cjall" || $action =="cjtype"){
        if ($rpage >= $pagecount){
            dBreakpoint ("../upload/maccjbreakpoint");
            echo "<br>数据采集完成<script language=\"javascript\">setTimeout(\"gonextpage();\",3000);function gonextpage(){location.href='?action=list&fa=1&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&rtype=" . $rtype."&cjurl=" . $cjurl . "';}</script>";
        }
        else{
            echo "<br>暂停3秒后继续采集...<script language=\"javascript\">setTimeout(\"gonextpage();\",3000);function gonextpage(){location.href='?action=" . $action . "&rpage=" . ($rpage + 1). "&rtype=" . $rtype . "&rday=". $rday . "&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&cjurl=" . $cjurl . "';}</script><a href=\"?action=" . $action . "&rpage=" . ($rpage + 1). "&rtype=" . $rtype . "&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&cjurl=" . $cjurl ."\" >点击进入下一页</a>";
        }
    }
    else{
		dBreakpoint ("../upload/maccjbreakpoint");
		echo "<br>数据采集完成<script language=\"javascript\">setTimeout(\"gonextpage();\",3000);function gonextpage(){location.href='?action=list&fa=1&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&rpage=" . $rpage . "&rtype=" . $rtype."&cjurl=" . $cjurl .  "';}</script>";
    }
}

function wTips($vname,$vdes)
{
    echo "<tr><td>&nbsp;" . strip_tags($vname) . "(<font color='#FF0000'>OK</font>)</td><td>&nbsp;" . $vdes . "</td></tr>";
    ob_flush();
	flush();
}

function mlist()
{
	global $action,$rtype,$rpage,$rkey,$rday,$cjurl,$xt,$ct,$rid,$cjflag;
?>
<style>
.nav{margin-top:1px; margin-bottom:1px;width:96%;}
.nav a,.menulist a:visited{ font-size:13px; color:blue;}
.nav ul { width:100%;}
.nav .spanli{ background:#2473A2; }
.nav ul li{ float:left; width:110px; text-align:left}
</style>
<script language="javascript">
var curtype="";
var rpage=<?php echo $rpage?>;
var rtype="<?php echo $rtype?>";
var rkey="<?php echo $rkey?>";
var rday="<?php echo $rday?>";
var cjurl="<?php echo $cjurl?>";
var fa="<?php echo $fa?>";
var xt="<?php echo $xt?>";
var ct="<?php echo $ct?>";
var cjflag="<?php echo $cjflag?>";
var rid="<?php echo $rid?>";

$(document).ready(function(){
	$("#cjsel").click(function(){
	    var item = $("input[@type=radio][name=ids[]][checked]").val();
	    if (item==undefined || item==""){ alert("请先选择所采集的数据"); return;}
	    $("#form3").attr("action","admin_maccj.php?action=cjsel&xt="+xt+'&ct='+ct+'&rid='+rid+"&cjflag="+cjflag+"&rpage="+rpage+ "&rtype="+rtype);
	    $("#form3").submit();
	});
	$("#cjday").click(function(){
		if(xt=="1" || xt=="2"){ rday="24"; } else { rday="1"; }
	    $("#form3").attr("action","admin_maccj.php?action=cjday&rday="+rday+"&xt="+xt+'&ct='+ct+'&rid='+rid+"&cjflag="+cjflag+"" );
	    $("#form3").submit();
	});
	$("#cjtype").click(function(){
	    if (rtype=="0"){ alert("请先进入分类,否则无法使用采集分类"); return;}
	    $("#form3").attr("action","admin_maccj.php?action=cjtype&rtype="+rtype+"&xt="+xt+'&ct='+ct+'&rid='+rid+"&cjflag="+cjflag);
	    $("#form3").submit();
	});
	$("#cjall").click(function(){
	    $("#form3").attr("action","admin_maccj.php?action=cjall&xt="+xt+'&ct='+ct+'&rid='+rid+"&cjflag="+cjflag );
	    $("#form3").submit();
	});
});
function getsearch()
{
	var url= '?action=list&xt='+xt+'&ct='+ct+'&rid='+rid+'&cjflag='+cjflag+'&rpage=1&rtype=0&rkey='+ encodeURI( $("#rkey").val() ) +'&cjurl='+cjurl;
	location.href= url;
}
function setunion(dtype,utype)
{
	$("#u_type").val(utype);
	var offset=$("#type"+dtype).offset();
	var tt=offset.top;
	var tl=offset.left;
	creatediv(99997,250,20);
	curtype=dtype;
	$("#confirm").css('border','1px solid #55BBFF').css('background','#C1E7FF').css('padding',' 3px 0px 3px 4px').css('top',tt-4+'px').css('left',tl-100+'px').html('正在加载内容......');
	$("#confirm").html( $("#typehtml").html() );
	$("#confirm").show();
}
function bindsave()
{
	var dtype = $("#confirm").find("#d_type").val();
	var utype = $("#confirm").find("#u_type").val();
	$.get("admin_maccj.php","action=bindunionsave&u_type="+utype+"&d_type="+dtype+"&rnd="+new Date(), function(obj) {
	    if(obj=="ok"){
	    	if(dtype==""){
	    		$('#type'+curtype).text("[未绑]");
	    		$('#type'+curtype).css("color","");
	    	}
	    	else{
	    		$('#type'+curtype).text("[已绑]");
	    		$('#type'+curtype).css("color","red");
	    	}
	    }
	    else{
	    	alert("绑定时出现错误："+ obj);
	    }
	    $("#confirm").remove();
	});
}
</script>
<span id="typehtml" style="display:none">
<input type="hidden" id="u_type" value=""/>
<select id="d_type" name="d_type">
<option value="">取消绑定</option>
<?php echo makeSelectAll("{pre}vod_type", "t_id", "t_name", "t_pid", "t_sort", 0, "", "&nbsp;|&nbsp;&nbsp;", "")?>
</select>
<input class="input" type="button" value="绑定" onclick="bindsave();">
<input class="input" type="button" value="取消" onclick="closew();">
</span>
<?php
    $unionids = getunionids();
    if ($xt == "1"){
    	$url = $cjurl . "?ac=list&pg=" . $rpage . "&rid=".$rid . "&t=" . $rtype . "&wd=" . $rkey;
    	
	    $html = trim(getPage($url, "utf-8"));
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_type = '/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/';
		$xn_vod = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><dt>([\s\S]*?)<\/dt><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note>([\s\S]*?)<\/video>/';
		$xn_vod_id=2;
		$xn_vod_name = 4;
	    $xn_vod_starring=7;
	    $xn_vod_type=5;
	    $xn_vod_from=6;
	    $xn_vod_time=1;
	    if ( strpos($html, "</rss>") <= 0){
	    	echo "加载数据发生错误 , <a href=\"".getUrl()."\">获取数据失败，请点击我重试</a>"; exit;
    	}
    }
    else if($xt=="2"){
    	$url = $cjurl . "-list-true-cid-". $rtype . "-h-". $rday. "-p-" . $rpage. "-wd-". $rkey;
	    $html = trim(getPage($url, "utf-8"));
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_type = '/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/';
		$xn_vod = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><dt>([\s\S]*?)<\/dt><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des><reurl>([\s\S]*?)<\/reurl><\/video>/';
		$xn_vod_id=2;
		$xn_vod_name = 4;
	    $xn_vod_starring=7;
	    $xn_vod_type=5;
	    $xn_vod_from=6;
	    $xn_vod_time=1;
	    if ( strpos($html, "</rss>") <= 0){
	    	echo "加载数据发生错误 , <a href=\"".getUrl()."\">获取数据失败，请点击我重试</a>"; exit;
    	}
    }
    else{
    	$url = $cjurl . "?action=list&rpage=" . $rpage . "&rtype=" . $rtype . "&rkey=" . $rkey;
	    $html = trim(getPage($url, "utf-8"));
    	$xn_list = '<vods pagesize="([0-9]+)" pagecount="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 1;
    	$xn_pagecount = 2;
    	$xn_recordcount = 3;
    	$xn_type = '/<type id="([0-9]+)">([\s\S]*?)<\/type>/';
		$xn_vod = '/<vod><id>([0-9]+)<\/id><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><starring><\!\[CDATA\[([\s\S]*?)\]\]><\/starring><type>([\s\S]*?)<\/type><from>([\s\S]*?)<\/from><time>([\s\S]*?)<\/time><\/vod>/';
		$xn_vod_id=1;
		$xn_vod_name = 2;
	    $xn_vod_starring=3;
	    $xn_vod_type=4;
	    $xn_vod_from=5;
	    $xn_vod_time=6;
	    if ( strpos($html, "</maccms>") <= 0){
	    	echo "加载数据发生错误 , <a href=\"".getUrl()."\">获取数据失败，请点击我重试</a>"; exit;
    	}
    }
    
    echo "<div class='nav'><ul><li style=\"width:70px;\"><a href=\"?action=list&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&rpage=1&rtype=&rkey=&cjurl=".$cjurl."\"><font color=\"#FF0000\"><b>全部资源</b></font></a></li>";
    
    if($cjflag==""){ $cjflag = getBody($html,"<cjflag>","</cjflag>"); }
    
    preg_match( $xn_list ,$html,$array1);
	$vodpagesize = $array1[$xn_pagesize];
	$vodpagecount = $array1[$xn_pagecount];
	$vodrecordcount = $array1[$xn_recordcount];
	unset($array1);
	
	
	
    preg_match_all($xn_type,$html,$array2);
    foreach($array2[1] as $key=>$value){
		$typeid = $value;
		$typename = $array2[2][$key];
        $typebind = "<a id='type" . $typeid . "' href=\"javascript:void(0)\" onclick='setunion(" . $typeid. ",\"" . $cjflag . $typeid . "\");' ";
        if (strpos(",".$unionids, "," . $cjflag . $typeid . ",") > 0){
            $typebind .= " style=\"color:red;\">[已绑]";
        }
        else{
            $typebind .= ">[未绑]";
        }
        $typebind .= "</a>";
        echo "<li><a href=\"?action=list&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&rpage=1&rtype=".$typeid."&rkey=&cjurl=".$cjurl."\">" . $typename . "</a>" .$typebind . "</li>";
    }
    unset($array2);
    echo "    </ul> </div>";
    
    echo "<table class=\"tb\" style=\"padding-top:0px;border-top:1px solid #DEEFFA;border-bottom:0px solid #DEEFFA;\"><tr><td>数据查询：<input id=\"rkey\" name=\"rkey\" type=\"text\" onFocus=\"if (value =='请输入关键词'){value =''}\" onBlur=\"if (value ==''){value='请输入关键词'}\" value=\"请输入关键词\" size=\"30\" value='" . $rkey . "'>&nbsp;<input type=\"button\" id=\"btnsearch\" value=\"查询数据\" onclick=\"getsearch();\" />&nbsp;<font color='red'>采集前请绑定相应分类 ，没有绑定分类的将不会采集入库</font>(<a href=\"http://www.maccms.com\" target=\"_blank\">maccms联盟专用采集服务端[请不要删除版权]</a>)</td></tr></table>";
	
	
	preg_match_all($xn_vod,$html,$array3);
	
    echo "<form action=\"\" method=\"post\" id=\"form3\" name=\"form3\"><table class=\"tb\"><input type=\"hidden\" id=\"cjurl\" name=\"cjurl\" value=\"" . $cjurl . "\"/>";
    if( count($array3[1])==0){
        echo "<tr><td align=\"center\" colspan=\"5\">没有任何数据</td></tr>";
        exit;
    }
    echo "<tr><td width=\"4%\">&nbsp;</td><td>名称</td><td width=\"20%\">分类</td><td width=\"20%\">来源</td><td width=\"20%\">时间</td></tr>";
    foreach($array3[1] as $key=>$value){
		$vodid = $array3[$xn_vod_id][$key];
		$vodname = $array3[$xn_vod_name][$key];
		$vodstarring = $array3[$xn_vod_starring][$key];
		$vodtype = $array3[$xn_vod_type][$key];
		$vodfrom = $array3[$xn_vod_from][$key];
		$vodtime =$array3[$xn_vod_time][$key];
        $curfontstart = "" ;
        $curfontend = "";
        $strTime = $vodtime;
        $strNow = date('Y-m-d',time());
        $strTo = date('Y-m-d',strtotime($vodtime));
        $timearr = explode("-",$vodtime);
        if(count($timearr)==2){
        	$strTo = substring($vodtime,5);
        }
        if (strpos(",".$strNow,$strTo)>0){
        	$curfontstart = "<font color=#FF0000>";
			$curfontend="</font>";
        }
		
        echo "<tr>" ;
        echo "    <td><input name=\"ids[]\" type=\"checkbox\" value=\"" . $vodid . "\"/></td>";
        echo "    <td>" . $curfontstart . $vodname . $curfontend . "</td>";
        echo "    <td>" . $curfontstart . $vodtype . $curfontend . "</td>";
        echo "    <td>" . $curfontstart . $vodfrom . $curfontend . "</td>";
        echo "    <td>" . $curfontstart . $vodtime . $curfontend . "</td>";
        echo "    </tr>";
    }
    unset($array3);
    
    echo "<tr> <td colspan=\"2\">  全选<input name=\"chkall\" type=\"checkbox\" id=\"chkall\" value=\"1\" onClick=\"checkAll(this.checked,'ids[]');\"/>&nbsp;  <input type='button' id='cjsel' value='采集选中'/>  <input type='button' id='cjday' value='采集当天'/> <input type='button' id='cjtype'value='采集当前分类'/>   <input type='button' id='cjall' value='采集全部'/>   </td>  </tr>   <tr>   <td colspan=\"5\" align=\"center\">". pagelist_manage($vodpagecount, $rpage, $vodrecordcount, $vodpagesize, "?action=list&xt=".$xt."&ct=".$ct."&rid=".$rid."&cjflag=".$cjflag."&rpage={p}&rtype=".$rtype."&rkey=".urlencode($rkey)."&cjurl=".$cjurl) ." </td>   </tr></table></form>";
}

function bindunion()
{
	global $action,$rtype,$rpage,$rkey,$rday,$cjurl,$xt,$ct,$rid,$cjflag;
?>
<style>
.nav{margin-top:1px; margin-bottom:1px;width:96%;}
.nav a,.menulist a:visited{ font-size:13px; color:blue;}
.nav ul { width:100%;}
.nav .spanli{ background:#2473A2; }
.nav ul li{ float:left; width:110px; text-align:left}
</style>
<script language="javascript">

function setunion(dtype,utype)
{
	$("#u_type").val(utype);
	var offset=$("#type"+dtype).offset();
	var tt=offset.top;
	var tl=offset.left;
	creatediv(99997,250,20);
	curtype=dtype;
	$("#confirm").css('border','1px solid #55BBFF').css('background','#C1E7FF').css('padding',' 3px 0px 3px 4px').css('top',tt-4+'px').css('left',tl-100+'px').html('正在加载内容......');
	$("#confirm").html( $("#typehtml").html() );
	$("#confirm").show();
}
function bindsave()
{
	var dtype = $("#confirm").find("#d_type").val();
	var utype = $("#confirm").find("#u_type").val();
	$.get("admin_maccj.php","action=bindunionsave&u_type="+utype+"&d_type="+dtype+"&rnd="+new Date(), function(obj) {
	    if(obj=="ok"){
	    	if(dtype==""){
	    		$('#type'+curtype).text("[未绑]");
	    		$('#type'+curtype).css("color","");
	    	}
	    	else{
	    		$('#type'+curtype).text("[已绑]");
	    		$('#type'+curtype).css("color","red");
	    	}
	    }
	    else{
	    	alert("绑定时出现错误："+ obj);
	    }
	    $("#confirm").remove();
	});
}
</script>
<span id="typehtml" style="display:none">
<input type="hidden" id="u_type" value=""/>
<select id="d_type" name="d_type">
<option value="">取消绑定</option>
<?php echo makeSelectAll("{pre}vod_type", "t_id", "t_name", "t_pid", "t_sort", 0, "", "&nbsp;|&nbsp;&nbsp;", "")?>
</select>
<input class="input" type="button" value="绑定" onclick="bindsave();">
<input class="input" type="button" value="取消" onclick="closew();">
</span>
<p>请绑定分类后进行采集操作，否则没有绑定分类的数据不会入库。 点击【未绑】弹出绑定菜单，选择对应的本地分类即可。</p>
<p>绑定完毕后点击 <a href="?action=list&xt=<?php echo $xt?>&ct=<?php echo $ct?>&rid=<?php echo $rid?>&cjflag=<?php echo $cjflag?>&cjurl=<?php echo $cjurl?>">【进入采集数据列表】 </a> 。</p>
<p>本地分类列表：</p>
<div class='nav'>
<ul>
<?php
    $unionids = getunionids();
    if ($xt == "1"){
    	$url = $cjurl . "?ac=list&pg=" . $rpage . "&rid=". $rid. "&t=" . $rtype . "&wd=" . $rkey;
	    $html = trim(getPage($url, "utf-8"));
	    if ( strpos($html, "</rss>") <= 0){
	    	echo "加载数据发生错误 , <a href=\"".getUrl()."\">获取数据失败，请点击我重试</a>"; exit;
    	}
    }
    else if($xt=="2"){
    	$url = $cjurl . "-list-true-cid-". $rtype . "-h-". $rday. "-p-" . $rpage. "-wd-". $rkey;
	    $html = trim(getPage($url, "utf-8"));
	    if ( strpos($html, "</rss>") <= 0){
	    	echo "加载数据发生错误 , <a href=\"".getUrl()."\">获取数据失败，请点击我重试</a>"; exit;
    	}
    }
    else{
    	$url = $cjurl . "?action=list&rpage=" . $rpage . "&rtype=" . $rtype . "&rkey=" . $rkey;
	    $html = trim(getPage($url, "utf-8"));
    	
	    if ( strpos($html, "</maccms>") <= 0){
	    	echo "加载数据发生错误 , <a href=\"".getUrl()."\">获取数据失败，请点击我重试</a>"; exit;
    	}
    }
    
    echo "<div class='nav'><ul>";
    
    if($cjflag==""){ $cjflag = getBody($html,"<cjflag>","</cjflag>"); }
    if($xt=="1"){
    	$xn_list = '<list page="([0-9]+)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_type = '/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/';
    }
    else if($xt=="2"){
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_type = '/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/';
    }
    else{
    	$xn_list = '<vods pagesize="([0-9]+)" pagecount="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 1;
    	$xn_pagecount = 2;
    	$xn_recordcount = 3;
    	$xn_type = '/<type id="([0-9]+)">([\s\S]*?)<\/type>/';
    }
    
    preg_match( $xn_list ,$html,$array1);
	$vodpagesize = $array1[$xn_pagesize];
	$vodpagecount = $array1[$xn_pagecount];
	$vodrecordcount = $array1[$xn_recordcount];
	unset($array1);
	
    preg_match_all($xn_type,$html,$array2);
    foreach($array2[1] as $key=>$value){
		$typeid = $value;
		$typename = $array2[2][$key];
        $typebind = "<a id='type" . $typeid . "' href=\"javascript:void(0)\" onclick='setunion(" . $typeid. ",\"" . $cjflag . $typeid . "\");' ";
        if (strpos(",".$unionids, "," . $cjflag . $typeid . ",") > 0){
            $typebind .= " style=\"color:red;\">[已绑]";
        }
        else{
            $typebind .= ">[未绑]";
        }
        $typebind .= "</a>";
        echo "<li><a href='###'>" . $typename . "</a>" .$typebind . "</li>";
    }
    unset($array2);
    echo "    </ul> </div>";
}

function main()
{
	$f = be("get","f");
    $html =  '<script language="JavaScript">$(document).ready(function(){$("#xmllist").html($("#xml").html());$("#xml").html("");});</script><table width="100%" border="0" cellspacing="0" cellpadding="0" ><tr><td align=right>';
    if (cBreakpoint("../upload/maccjbreakpoint")){
		$html .= "<a href=\"?action=breakpoint\"><font color=\"red\" style=\"font-size:16px;\">继续上次断点采集</font></a>";
	}
	$html .= "</td></tr><tr><td><span id=\"xmllist\">资源列表载入中……</span></td></tr></table><span id=\"xml\">";
	
	if($f!=""){
		$html .= "<script type=\"text/javascript\" src=\"".$f."\" charset=\"utf-8\"></script>";
	}
	else{
		$html .= "<script type=\"text/javascript\" src=\"http://www.maccms.com/union/xmlutf.js\" charset=\"utf-8\"></script>";
	}
	$html .= "</span></body></html>";
	echo $html;
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

function getunionids()
{
    global $db,$typearr;
    if (empty($typearr)){
    	$sql = "select * from {pre}vod_type where length(t_union)>0 ";
    	$typearr = $db->queryArray($sql);
    }
	if(is_array($typearr)){
		for($i=0;$i<count($typearr);$i++){
			$unionids .= $typearr[$i]["t_union"] . ",";
		}
	}
	
	$unionids = replaceStr($unionids,",,,",",");
	$unionids = replaceStr($unionids,",,",",");
	return $unionids;
}

function bindunionsave()
{
	global $db;
    $u_type = be("all", "u_type");
    $d_type = be("all", "d_type");
    $typeid = getTypeID($u_type);
    $backurl = be("all", "backurl");
    
    if (isN($d_type)){
        $sql = "select * from {pre}vod_type where t_id =" . $typeid;
        $row = $db->getRow($sql);
        if($row){
            if (strpos(",".$row["t_union"],$u_type) > 0){
                $newunionids = replaceStr($row["t_union"], $u_type, "");
                $newunionids = replaceStr($newunionids, ",,", ",");
                if (trim($newunionids) == ","){ $newunionids = "";}
                $db->Update ("{pre}vod_type", array("t_union"), array($newunionids), "t_id=" . $row["t_id"]);
            }
        }
        unset($row);
    }
    else{
        $sql = "select * from {pre}vod_type where t_id =" . $d_type;
        $row = $db->getRow($sql);
        if($row){
            if ( isN($row["t_union"]) || strpos(",".$row["t_union"], "," . $u_type. ",") <= 0){
                $newunionids = $row["t_union"] . "," . $u_type . ",";
                $newunionids = replaceStr($newunionids, ",,", ",");
                if (trim($newunionids) == ","){ $newunionids = "";}
                $db->Update ("{pre}vod_type", array("t_union"), array($newunionids), "t_id=" .$row["t_id"]);
            }
        }
        unset($row);
    }
    dispseObj();
    echo "ok";
    exit;
}
?>