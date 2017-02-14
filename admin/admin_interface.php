<?php
require_once ("admin_conn.php");
require_once ("../inc/config.interface.php");
require_once ("../inc/pinyin.php");

$action = be("all","action");
$pass = be("all","pass");

switch($action)
{
    case "retype":  chkLogin();headAdmin ("分类转换配置"); retype();break;
    case "retypesave": retypesave();break;
    case "vod": vod();break;
    case "art": art();break;
    default: chkLogin();headAdmin ("接口配置"); main();break;
}
dispseObj();

function gettypere($flag,$tName)
{
	global $cache;
    $res = 0;
    if ($flag=="art"){
        $typearr = $cache[1];
        $file = "../inc/dim_retype_art.txt";
    }
    else{
        $typearr = $cache[0];
        $file = "../inc/dim_retype_vod.txt";
	}
    
    $str = file_get_contents($file);
    if (!isN($str)){
        $str = replaceStr($str, Chr(10), Chr(13));
        $arr1 = explode(Chr(13),$str);
        
        for ($i=0;$i<count($arr1);$i++){
            if (!isN($arr1[$i])){
                $str1 = $arr1[$i];
                $arr2 = explode("=",$str1);
                
                if (trim($tName) == trim($arr2[1])){
                    foreach($typearr as $t){
                        if (trim($t["t_name"]) == trim($arr2[0])){
                            return $t["t_id"];
                            break;
                    	}
                    }
					break;
                }
            }
        }
    }
}

function vod()
{
	global $db,$pass;
	if (app_interfacepass != $pass){ echo "非法使用err";exit; }
	
    $d_id = be("all", "d_id");
    $d_name = be("all", "d_name"); $d_subname = be("all", "d_subname");
    $d_enname = be("all", "d_enname"); $d_type = be("all", "d_type");
    $d_state = be("all", "d_state"); $d_color = be("all", "d_color");
    $d_starring = be("all", "d_starring"); $d_year = be("all", "d_year"); 
    $d_directed = be("all", "d_directed"); $d_area = be("all", "d_area");
    $d_language = be("all", "d_language"); $d_level = be("all", "d_level");
    $d_stint = be("all", "d_stint"); $d_hits = be("all", "d_hits");
    $d_dayhits = be("all", "d_dayhits"); $d_weekhits = be("all", "d_weekhits");
    $d_monthhits = be("all", "d_monthhits"); $d_topic = be("all", "d_topic");
    $d_content = be("all", "d_content"); $d_remarks = be("all", "d_remarks");
    $d_hide = be("all", "d_hide"); $d_good = be("all", "d_good");
    $d_bad = be("all", "d_bad"); $d_usergroup = be("all", "d_usergroup");
    $d_pic = be("all", "d_pic"); $d_picthumb = be("all", "d_picthumb"); $d_picslide = be("all", "d_picslide");
    $d_addtime = be("all", "d_addtime"); $d_time = be("all", "d_time"); 
    $d_playurl = be("all", "d_playurl");  $d_playfrom = be("all", "d_playfrom"); $d_playserver = be("all", "d_playserver");
    $d_downurl = be("all","d_downurl");   $d_downfrom = be("all", "d_downfrom"); $d_downserver = be("all", "d_downserver");
    $d_addtime = date('Y-m-d H:i:s',time()); $d_time = date('Y-m-d H:i:s',time());
    $d_duration = be("all","d_duration"); $d_stintdown = be("all", "d_stintdown");
    	
    	
    $d_pic = stripslashes($d_pic); $d_picthumb = stripslashes($d_picthumb); $d_picslide = stripslashes($d_picslide);
    $d_name = replaceStr($d_name, "'", "''");
    $d_starring = replaceStr($d_starring, "'", "''");
    $d_directed = replaceStr($d_directed, "'", "''");
    $d_content = stripslashes($d_content); $d_content = replaceStr($d_content, "'", "''");
    
    if (!isNum($d_usergroup)) { $d_usergroup = 0;}
    if (isN($d_name)) { echo "视频名称不能为空err"; exit;}
    if (isN($d_type)) { echo "视频分类不能为空err"; exit;}
    
    if (!isNum($d_level)) { $d_level = 0;}
    if (!isNum($d_hits)) { $d_hits = 0;}
    if (!isNum($d_topic)) { $d_topic = 0;}
    if (!isNum($d_stint)) { $d_stint = 0;}
    if (!isNum($d_stintdown)) { $d_stintdown = 0;}
    if (!isNum($d_state)) { $d_state = 0;}
    if (!isNum($d_score)) { $d_score=0;}
    if (!isNum($d_good)) { $d_good=0;}
    if (!isNum($d_bad)) { $d_bad=0;}
    if (!isNum($d_scorecount)) { $d_scorecount=0;}
    if (!isNum($d_hide)) { $d_hide = 0;}
    if (!isNum($d_duration)) { $d_duration=0;}
    
    if (isN($d_enname)) { $d_enname = Hanzi2PinYin($d_name); }
    if (strpos($d_enname, "*")>0 || strpos($d_enname, ":")>0 || strpos($d_enname, "?")>0 || strpos($d_enname, "\"")>0 || strpos($d_enname, "<")>0 || strpos($d_enname, ">")>0 || strpos($d_enname, "|")>0 || strpos($d_enname, "\\")>0){
        echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号err";exit;
    }
    $d_letter = strtoupper(substring($d_enname,1));
    $rc = false;
    
    if (!isN($d_playurl)){
	    if (isN($d_playfrom)) { echo "视频播放器类型不能为空err";exit;}
	    $d_playurl = replaceStr($d_playurl, chr(13), "#");
        $d_playurl = replaceStr($d_playurl, chr(10), "#");
        $d_playurl = replaceStr($d_playurl, "###", "#");
        $d_playurl = replaceStr($d_playurl, "##", "#");
        if (substring($d_playurl, 3,strlen($d_playurl)-3) == '$$$'){ $d_playurl = substring($d_playurl, strlen($d_playurl)-3);}
        if (substring($d_playfrom, 3,strlen($d_playfrom)-3) == '$$$'){ $d_playfrom = substring($d_playfrom, strlen($d_playfrom)-3);}
        if (substring($d_playserve, 3,strlen($d_playserve)-3) == '$$$'){ $d_playserve = substring($d_playserve, strlen($d_playserve)-3);}
        
	    $playurlarr = explode("$$$",$d_playurl) ; $playfromarr = explode("$$$",$d_playfrom) ; $playserverarr = explode("$$$",$d_playserve);
	    if (count($playurlarr) != count($playfromarr)){
	    	echo "播放器类型、播放地址数量不一致,多组数据请用$$$连接err" ; exit;
	    }
	    
    }
    if (!isN($d_downurl)){
	    if (isN($d_downfrom)) { echo "视频下载类型不能为空err";exit;}
	    $d_downurl = replaceStr($d_downurl, chr(13), "#");
        $d_downurl = replaceStr($d_downurl, chr(10), "#");
        $d_downurl = replaceStr($d_downurl, "###", "#");
        $d_downurl = replaceStr($d_downurl, "##", "#");
        if (substring($d_downurl, 3,strlen($d_downurl)-3) == '$$$'){ $d_downurl = substring($d_downurl, strlen($d_downurl)-3);}
        if (substring($d_downfrom, 3,strlen($d_downfrom)-3) == '$$$'){ $d_downfrom = substring($d_downfrom, strlen($d_downfrom)-3);}
        if (substring($d_downserve, 3,strlen($d_downserve)-3) == '$$$'){ $d_downserve = substring($d_downserve, strlen($d_downserve)-3);}
        
	    $downurlarr = explode("$$$",$d_downurl) ; $downfromarr = explode("$$$",$d_downfrom) ; $downserverarr = explode("$$$",$d_downserve);
	    if (count($downurlarr) != count($downfromarr)){
	    	echo "下载类型、下载地址数量不一致,多组数据请用$$$连接err" ; exit;
	    }
    }
    
    $newtype = "";
    $newtypeid = 0;
    $newtype = $d_type;
    if (!isNum($d_type)) { $newtypeid = gettypere("vod", $d_type);} else{ $newtypeid = intval($d_type);}
    if ($newtypeid ==0) { echo $d_name . " " . $newtype . " 没有找到转换的分类err";exit; }
    $d_type = $newtypeid;
    
    $sql = "SELECT * FROM {pre}vod WHERE d_name ='" .$d_name. "' ";
    $row = $db->getRow($sql);
    if (!$row){
        $resultdes = "新增数据ok";
        $db->Add ("{pre}vod", array("d_name", "d_subname", "d_enname", "d_type", "d_state", "d_letter", "d_color", "d_pic","d_picthumb","d_picslide", "d_starring", "d_directed", "d_area", "d_year", "d_language", "d_level", "d_stint","d_stintdown", "d_hits", "d_topic", "d_content", "d_remarks", "d_usergroup", "d_score", "d_scorecount","d_good","d_bad","d_hide","d_duration","d_addtime", "d_time", "d_playurl", "d_playfrom", "d_playserver","d_downurl","d_downfrom", "d_downserver"), array($d_name, $d_subname, $d_enname, $d_type, $d_state, $d_letter, $d_color, $d_pic,$d_picthumb,$d_picslide, $d_starring, $d_directed, $d_area, $d_year, $d_language, $d_level, $d_stint,$d_stintdown, $d_hits, $d_topic, $d_content, $d_remarks, $d_usergroup,$d_score, $d_scorecount,$d_good,$d_bad,$d_hide,$d_duration, $d_addtime, $d_time, $d_playurl, $d_playfrom, $d_playserver,$d_downurl, $d_downfrom, $d_downserver));
    }
    else{
    	if (strpos(",".$row["d_pic"], "http:") > 0) { } else { $d_pic= $row["d_pic"];}
        if (strpos(",".$row["d_picthumb"], "http:") > 0) { } else { $d_picthumb= $row["d_picthumb"];}
        if (strpos(",".$row["d_picslide"], "http:") > 0) { } else { $d_picslide= $row["d_picslide"];}
        
        if (!isN($d_playurl)){
	        $oldplayfrom = $row["d_playfrom"];
	    	$oldplayurl = $row["d_playurl"];
	    	$oldplayserver = $row["d_playserver"];
	        $newplayurl="";
	        $newplayfrom="";
	        $newplayserver="";
	        
	        if ($row["d_playurl"] ==$d_playurl){
	            $resultdes = "无需更新播放地址ok";
	            $newplayfrom = $oldplayfrom;
	            $newplayurl = $oldplayurl;
	            $newplayserver = $oldplayserver;
	        }
	        else if(isN($oldplayfrom)){
	        	$resultdes = "新增播放地址ok";
	            $newplayfrom = $d_playfrom;
	            $newplayurl = $d_playurl;
	            $newplayserver = $d_playserver;
	        }
	        else{
	            $resultdes = "更新播放地址ok";
	            $arr1 = explode("$$$",$oldplayurl);
	            $arr2 = explode("$$$",$oldplayfrom);
	            $arr3 = explode("$$$",$oldplayserver);
	            $rc = false;
	            
	            for ($j=0;$j<count($arr2);$j++){
					if ($rc){
	            		$newplayurl = $newplayurl . "$$$";
	            		$newplayfrom = $newplayfrom . "$$$";
	            		$newplayserver = $newplayserver ."$$$";
	            	}
	            	for ($k=0;$k<count($playfromarr);$k++){
						if ($arr2[$j] == $playfromarr[$k]){
							$arr1[$j] = $playurlarr[$k];
							break;
						}
						
					}
					$newplayurl = $newplayurl . $arr1[$j];
		            $newplayfrom = $newplayfrom . $arr2[$j];
		            if (count($arr3) > $j){
		            	$newplayserver = $newplayserver . $arr3[$j];
		            }
		            else{
		            	$newplayserver = $newplayserver . "0";
		            }
		            $rc=true;
				}
		        
	            for ($k=0;$k<count($playfromarr);$k++){
		            for ($j=0;$j<count($arr2);$j++){
		            	if (strpos(",".$oldplayfrom,$playfromarr[$k])<=0){
		            		$newplayfrom = $newplayfrom . "$$$". $playfromarr[$k];
		            		$newplayurl = $newplayurl ."$$$". $playurlarr[$k];
		            		$oldplayfrom = $oldplayfrom . "$$$". $playfromarr[$k];
		            		if (count($playserverarr) > $k){
		            			$newplayserver = $newplayserver ."$$$". $playserverarr[$k];
		            		}
		            		else{
		            			$newplayserver = $newplayserver ."$$$". "0";
		            		}
		            		$resultdes = "新增播放地址ok";
		            	}
		            }
	            }
	            unset($arr1);
	            unset($arr2);
	            unset($arr3);
			}
		}
		else{
			$newplayfrom = $row["d_playfrom"];
	    	$newplayurl = $row["d_playurl"];
	    	$newplayserver = $row["d_playserver"];
		}
        $newplayurl = replaceStr($newplayurl, Chr(13), "#");
        
        if (!isN($d_downurl)){
	        $olddownfrom = $row["d_downfrom"];
	    	$olddownurl = $row["d_downurl"];
	    	$olddownserver = $row["d_downserver"];
	        $newdownurl="";
	        $newdownfrom="";
	        $newdownserver="";
	        
	        if ($row["d_downurl"] ==$d_downurl){
	            $resultdes = "无需更新下载地址ok";
	            $newdownfrom = $olddownfrom;
	            $newdownurl = $olddownurl;
	            $newdownserver = $olddownserver;
	        }
	        else if(isN($olddownfrom)){
	        	$resultdes = "新增下载地址ok";
	            $newdownfrom = $d_downfrom;
	            $newdownurl = $d_downurl;
	            $newdownserver = $d_downserver;
	        }
	        else{
	            $resultdes = "更新下载地址ok";
	            $arr1 = explode("$$$",$olddownurl);
	            $arr2 = explode("$$$",$olddownfrom);
	            $arr3 = explode("$$$",$olddownserver);
	            $rc = false;
	            
	            for ($j=0;$j<count($arr2);$j++){
					if ($rc){
	            		$newdownurl = $newdownurl . "$$$";
	            		$newdownfrom = $newdownfrom . "$$$";
	            		$newdownserver = $newdownserver ."$$$";
	            	}
	            	for ($k=0;$k<count($downfromarr);$k++){
						if ($arr2[$j] == $downfromarr[$k]){
							$arr1[$j] = $downurlarr[$k];
							break;
						}
						
					}
					$newdownurl = $newdownurl . $arr1[$j];
		            $newdownfrom = $newdownfrom . $arr2[$j];
		            if (count($arr3) > $j){
		            	$newdownserver = $newdownserver . $arr3[$j];
		            }
		            else{
		            	$newdownserver = $newdownserver . "0";
		            }
		            $rc=true;
				}
		        
	            for ($k=0;$k<count($downfromarr);$k++){
		            for ($j=0;$j<count($arr2);$j++){
		            	if (strpos(",".$olddownfrom,$downfromarr[$k])<=0){
		            		$newdownfrom = $newdownfrom . "$$$". $downfromarr[$k];
		            		$newdownurl = $newdownurl ."$$$". $downurlarr[$k];
		            		$olddownfrom = $olddownfrom . "$$$". $downfromarr[$k];
		            		if (count($downserverarr) > $k){
		            			$newdownserver = $newdownserver ."$$$". $downserverarr[$k];
		            		}
		            		else{
		            			$newdownserver = $newdownserver ."$$$". "0";
		            		}
		            		$resultdes = "新增下载地址ok";
		            	}
		            }
	            }
	            unset($arr1);
	            unset($arr2);
	            unset($arr3);
			}
		}
		else{
			$newdownfrom = $row["d_downfrom"];
	    	$newdownurl = $row["d_downurl"];
	    	$newdownserver = $row["d_downserver"];
		}
		$newdownurl = replaceStr($newdownurl, Chr(13), "#");
		
        $db->Update ("{pre}vod",array("d_state","d_remarks","d_time","d_pic","d_picthumb","d_picslide","d_playurl","d_playfrom","d_playserver","d_downurl","d_downfrom","d_downserver"),array($d_state,$d_remarks,date('Y-m-d H:i:s',time()),$d_pic,$d_picthumb,$d_picslide,$newplayurl,$newplayfrom,$newplayserver,$newdownurl,$newdownfrom,$newdownserver),"d_id=".$row["d_id"]);
    }
    unset($row);
    echo $resultdes;
}


function art()
{
    global $db,$pass;
	if (app_interfacepass != $pass){ echo "非法使用err";exit; }
    
    $a_id = be("all", "a_id"); $a_title = be("all", "a_title");
    $a_subtitle = be("all", "a_subtitle"); $a_entitle = be("all", "a_entitle");
    $a_type = be("all", "a_type");$a_content = be("all", "a_content");
    $a_author = be("all", "a_author"); $a_color = be("all", "a_color");
    $a_hits = be("all", "a_hits"); $a_dayhits = be("all", "a_dayhits");
    $a_weekhits = be("all", "a_weekhits");$a_monthhits = be("all", "a_monthhits");
    $a_from = be("all", "a_from"); $a_hide = be("all", "a_hide"); $a_pic = be("all", "a_pic");
    $a_addtime = be("all", "a_addtime"); $a_time = be("all", "a_time"); $a_hitstime = be("all", "a_hitstime");
    $a_level = be("all", "a_level");
    
    $a_addtime = date('Y-m-d H:i:s',time());
    $a_time = date('Y-m-d H:i:s',time());
    $a_title = replaceStr($a_title, "'", "''");
    $a_author = replaceStr($a_author, "'", "''");
    $a_content = stripslashes($a_content); $a_content = replaceStr($a_content, "'", "''");
    
    if (isN($a_title)) { echo "文章标题不能为空err"; exit;}
    if (isN($a_type)) { echo "文章分类不能为空err"; exit;}
    if (!isNum($a_hits)) { $a_hits = 0;}
    if (!isNum($a_hide)) { $a_hide = 0;}
    if (!isNum($a_level)) { $a_level = 0;}
    if (isN($a_entitle)) { $a_entitle = Hanzi2PinYin($a_title); }
    
    if (strpos($a_entitle, "*")>0 || strpos($a_entitle, ":")>0 || strpos($a_entitle, "?")>0 || strpos($a_entitle, "\"")>0 || strpos($a_entitle, "<")>0 || strpos($a_entitle, ">")>0 || strpos($a_entitle, "|")>0 || strpos($a_entitle, "\\")>0){
        echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号err"; exit;
    }
    $a_letter = strtoupper(substring($a_entitle,1));
    if (!isNum($a_type)) { $a_type = gettypere("art", $a_type);}
    if ($a_type== 0) { echo "没有找到转换的分类err";exit;}
    
    $sql = "SELECT * FROM {pre}art WHERE a_title ='" . $a_title . "' ";
    $row = $db->getRow($sql);
    if(!$row){
        $db->Add ("{pre}art", array("a_title", "a_subtitle", "a_entitle", "a_type","a_letter" ,"a_content", "a_author", "a_color", "a_from","a_pic","a_hide", "a_hits", "a_level", "a_addtime", "a_time"), array($a_title, $a_subtitle, $a_entitle, $a_type, $a_letter, $a_content, $a_author, $a_color, $a_from,$a_pic,$a_hide, $a_hits,$a_level, $a_addtime, $a_time));
    }
    else{
        $db->Update ("{pre}art", array("a_content"),array($a_content),"a_id=".$row["a_id"]);
    }
    unset($row);
    echo "ok";
}

function retypesave()
{
    $vodtype = be("post", "vodtype");
    $arttype = be("post", "arttype");
    $oldpass = be("post", "oldpass");
    $temppass = be("post", "temppass");
    
    fwrite(fopen("../inc/dim_retype_vod.txt","wb"),$vodtype);
    fwrite(fopen("../inc/dim_retype_art.txt","wb"),$arttype);
    
	$str = "<" . "?php" . "\n";
	$str.= "define(\"app_interfacepass\",\""  . trim(be("post","app_interfacepass"))  . "\");        //站外入库安全验证密码" . "\n";
	$str.= "?" . ">";
	fwrite(fopen("../inc/config.interface.php","wb"),$str);
	
    showMsg ("修改完毕", "admin_interface.php?action=retype");
}

function retype()
{
    $fc1 = file_get_contents("../inc/dim_retype_vod.txt");
    $fc2 = file_get_contents("../inc/dim_retype_art.txt");
?>
<form action="?action=retypesave" method="post">
<table class="tb">
<tr class="thead"><th colspan="2">此功能主要用于第三方工具（火车头、ET等）入库接口转换。>>> 1.每个各占一行; 2.本地分类在前,采集分类在后(动作片=动作).;3.不要有多余的空行</th></tr>
<tr><td width="50%">视频分类转换</td>
<td width="50%">文章分类转换</td>
</tr>
<tr>
    <td>
    <textarea id="vodtype" name="vodtype" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc1?></textarea>
    </td>
    <td>
    <textarea id="arttype" name="arttype" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc2?></textarea>
    </td>
    </tr>
    <tr>
    <td align="center" colspan="2">入库免登录安全验证密码:<input id="app_interfacepass" name="app_interfacepass" size="20" value="<?php echo app_interfacepass?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" id="btnSave" name="btnSave" value="保存" class="input" /> </td>
    </tr>
</table>
</form>
</body>
</html>
<?php
}

function main()
{

}
?>