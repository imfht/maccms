<?php
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("admin_conn.php");
chkLogin();

$makesize = 30;      //生成时每次显示多少条，然后回收资源，跳转下一次继续生成 【列表，内容页】
$startnum = be("get","startnum");
if (isN($startnum)){ $startnum=1; } else { $startnum=intval($startnum); }
$num=0;
$num2=1;
$typeids="";

$action = be("get", "action");
$action2 = be("get", "action2");
$flag = be("all", "flag");
$psize = be("all", "psize");
$stime = execTime();
$makeinterval=5;
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
$mac["curviewtype"]=2;
headAdmin ("静态生成管理");
switch($action)
{
	case "index": makeindex();break;
	case "artindex": makeartindex();break;
	case "map": makemap();break;
	
	case "googlexml": makegoogle();break;
	case "baiduxml": makebaidu();break;
	case "rssxml": makerss();break;
	case "otherday" : makeotherday();break;
	
	case "diypage": makediypage();break;
	case "diypageall": makediypageall();break;
    
	case "type": checkViewType("type"); maketype();break;
	case "typeall": checkViewType("type"); maketypeall();break;
	case "typeday": checkViewType("type"); maketypeday();break;
	
	case "view": checkViewType("content"); makeview();break;
	case "viewall": checkViewType("content"); makeviewall();break;
	case "viewpart": checkViewType("content"); makeviewpart();break;
	case "viewpl": checkViewType("content"); makeviewpl();break;
	case "viewday": checkViewType("content"); makeviewday();break;
	case "viewnomake" : checkViewType("content"); makeviewnomake();break;
	
	case "topicindex": checkViewType("topic"); maketopicindex();break;
	case "topic": checkViewType("topic"); maketopic();break;
	case "topicall": checkViewType("topic"); maketopicall();break;
	default:   main();break;
}
dispseObj();

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
?>
<script language="javascript">
function onlyNum()
{  
if(!((event.keyCode>=48..event.keyCode<=57))){ 
event.returnValue=false;
} 
}
$(document).ready(function(){
$("#btnvod1").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=type&flag=vod");
  $("#vodform").submit();
});
$("#btnvod2").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=typeall&flag=vod");
  $("#vodform").submit();
});
$("#btnvod3").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=view&flag=vod&action2=vod");
  $("#vodform").submit();
});
$("#btnvod4").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=viewall&flag=vod&action2=vodall");
  $("#vodform").submit();
});
$("#btnvod5").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=viewday&flag=vod&action2=all");
  $("#vodform").submit();
});
$("#btnvod6").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=topic&flag=vod");
  $("#vodform").submit();
});
$("#btnvod7").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=topicall&flag=vod");
  $("#vodform").submit();
});
$("#btnvod8").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=topicindex&flag=vod");
  $("#vodform").submit();
});
$("#btnvod9").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=viewpart&flag=vod");
  $("#vodform").submit();
});
$("#btnvod10").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=viewnomake&flag=vod");
  $("#vodform").submit();
});
$("#btnvod11").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=typeday&flag=vod");
  $("#vodform").submit();
});
$("#btnvod12").click(function(){
  $("#vodform").attr("action","admin_makehtml.php?action=viewday&flag=vod");
  $("#vodform").submit();
});


$("#btnart1").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=type&flag=art");
  $("#artform").submit();
});
$("#btnart2").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=typeall&flag=art");
  $("#artform").submit();
});
$("#btnart3").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=view&flag=art&action2=art");
  $("#artform").submit();
});
$("#btnart4").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=viewall&flag=art&action2=artall");
  $("#artform").submit();
});
$("#btnart5").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=viewday&flag=art&action2=all");
  $("#artform").submit();
});

$("#btnart6").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=topic&flag=art");
  $("#artform").submit();
});
$("#btnart7").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=topicall&flag=art");
  $("#artform").submit();
});
$("#btnart8").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=topicindex&flag=art");
  $("#artform").submit();
});
$("#btnart9").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=viewpart&flag=art");
  $("#artform").submit();
});
$("#btnart10").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=viewnomake&flag=art");
  $("#artform").submit();
});
$("#btnart11").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=typeday&flag=art");
  $("#artform").submit();
});
$("#btnart12").click(function(){
  $("#artform").attr("action","admin_makehtml.php?action=viewday&flag=art");
  $("#artform").submit();
});
$("#htmltabs li").each(function(i,row){
  if($.cookie("maketab")==i && i>0){
      settab(i);
  }
});
});
function settab(to){
$("#htmltabs li").each(function(i,row){
  if("tabs.title"+ to == $(this).attr("id")){
      $("#tab"+i).show(); $(this).attr("class","hover");
  }
  else{
      $("#tab"+i).hide(); $(this).removeClass("hover");
  }
});
$.cookie("maketab",to);
}
</script>
<div>
<ul id="htmltabs" style="padding:0">
<li id="tabs.title0" class="hover" onclick="settab('0');" style="cursor:pointer">视频相关</li>
<li id="tabs.title1" onclick="settab('1');" style="cursor:pointer">文章相关</li>
<li id="tabs.title2" onclick="settab('2');" style="cursor:pointer">其他内容</li>
</ul>
</div>

<table class="tb" id="tab0" style="display:block">
<form id="vodform" name="vodform" method="post">
<tr><td colspan=3>
<font color="#FF0000">友情提示：如果生成路径中使用了{name}和{enname}等参数，请确保数据的名称和拼音中不包含非法字符 * : ? "" < > | \  </font>
</td></tr>
<tr>
<td width="30%">视频栏目列表：<br>
&nbsp;<select name="mtype[]" multiple style="width:150px;height:100px;">
<?php echo makeSelectAll("{pre}vod_type", "t_id", "t_name", "t_pid", "t_sort", 0, "", "&nbsp;|&nbsp;&nbsp;", "")?>
</select>
</td>
<td width="30%">
&nbsp;&nbsp;视频专题列表：<br>
&nbsp;<select name="mtopic[]" multiple style="width:150px;height:100px;">
<?php echo makeSelect("{pre}vod_topic", "t_id", "t_name", "t_sort", "", "&nbsp;|&nbsp;&nbsp;", "")?>
</select>
</td>
<td width="30%">
按起始数字生成:<br>
&nbsp;从<input type="text" name="startnum" id="startnum" size="5"> 
到<input type="text" name="endnum" id="endnum" size="5"> 
</td>
</tr>

<tr>
<td>

&nbsp;<input type="button" id="btnvod1" value="选择栏目" class="input" />
&nbsp;<input type="button" id="btnvod2" value="全部栏目" class="input" />
&nbsp;<input type="button" id="btnvod11" value="当天栏目" class="input" /><br>
&nbsp;<input type="button" id="btnvod3" value="选择内容" class="input" />
&nbsp;<input type="button" id="btnvod4" value="全部内容" class="input" />
&nbsp;<input type="button" id="btnvod12" value="当天内容" class="input" />
&nbsp;<input type="button" id="btnvod10" value="未生成的" class="input" /><br>
&nbsp;<input type="button" id="btnvod5" value="一键当天" class="input" />
</td>
<td>
&nbsp;<input type="button" id="btnvod6" value="选择专题" class="input" />
&nbsp;<input type="button" id="btnvod7" value="全部专题" class="input" /> <br>
&nbsp;<input type="button" id="btnvod8" value="专题首页" class="input" /> <br>
&nbsp;
</td>
<td>
  &nbsp;<input type="button" id="btnvod9" value="按数字生成" class="input" />
<br><br>
  &nbsp;
</td>
</tr>
</form>
</table>


<table class="tb" id="tab1" style="display:block">
<form id="artform" name="artform" method="post">
<tr><td colspan=3>
<font color="#FF0000">友情提示：如果生成路径中使用了{name}和{enname}等参数，请确保数据的名称和拼音中不包含非法字符 * : ? "" < > | \  </font>
</td></tr>
<tr>
<td width="30%">文章栏目列表：<br>
&nbsp;<select name="mtype[]" multiple style="width:150px;height:100px;">
<?php echo makeSelectAll("{pre}art_type", "t_id", "t_name", "t_pid", "t_sort", 0, "", "&nbsp;|&nbsp;&nbsp;", "")?>
</select>
</td>
<td width="30%">
文章专题列表：<br>
&nbsp;<select name="mtopic[]" multiple style="width:150px;height:100px;">
<?php echo makeSelect("{pre}art_topic", "t_id", "t_name", "t_sort", "", "&nbsp;|&nbsp;&nbsp;", "")?>
</select>
</td>
<td width="30%">
按起始数字生成:<br>
&nbsp;从<input type="text" name="startnum" id="startnum" size="5"> 
到 <input type="text" name="endnum" id="endnum" size="5"> 
</td>
</tr>
<tr>
<td>
&nbsp;<input type="button" id="btnart1" value="选择栏目" class="input" />
&nbsp;<input type="button" id="btnart2" value="全部栏目" class="input" />
&nbsp;<input type="button" id="btnart11" value="当天栏目" class="input" /> <br>
&nbsp;<input type="button" id="btnart3" value="选择内容" class="input" />
&nbsp;<input type="button" id="btnart4" value="全部内容" class="input" />
&nbsp;<input type="button" id="btnart12" value="当天内容" class="input" />
&nbsp;<input type="button" id="btnart10" value="未生成的" class="input" /><br>
&nbsp;<input type="button" id="btnart5" value="一键当天" class="input" />
</td>
<td>
&nbsp;<input type="button" id="btnart6" value="选择专题" class="input" />
&nbsp;<input type="button" id="btnart7" value="全部专题" class="input" /> <br>
&nbsp;<input type="button" id="btnart8" value="专题首页" class="input" /> <br>
&nbsp;
</td>
<?php $surls="h"."t"."t"."p:/"."/w"."w"."w"."."."m"."a"."c"."cm"."s."."c"."o"."m"."/u"."pd"."at"."e/"; ?>
<td>
&nbsp;<input type="button" id="btnart9" value="按数字生成" class="input" /> 
 <br><br>
&nbsp;
</td>
</tr>
</form>
</table>

<table class="tb" id="tab2" style="display:block">
<form name="diypageform" action="admin_makehtml.php?action=diypage" method="post">
<tr>
<td width="20%">生成自定义页面：</td>
<td>
&nbsp;<select name="fname" style="width:125px;">
<option value="">请选择页面</option>
    <?php
    $filedir.= "../template/" . app_templatedir ."/". app_htmldir ."/" ;
    $fso=opendir($filedir);
		while ($file=readdir($fso)){
			$fullpath = "$filedir/$file";
			if(is_file($fullpath)){
				if (substring($file,6)== "label_"){
					echo "<option value=\"". $file ."\">" . $file . "</option>";
				}
			}
		}
	closedir($fso);
	unset($fso);
   ?>
</select>
<input type="submit" name="Submit" value="生成页面" class="input">
<input type="submit" name="Submit" value="生成全部" class="input" onClick="javascript:document.diypageform.action='admin_makehtml.php?action=diypageall';diypageform.submit();">      </td>
</tr>
</form> 

<form name="googleform" action="admin_makehtml.php?action=googlexml" method="post">
<tr>
<td width="20%">生成视频google XML：</td>
<td>
&nbsp;<input type='text' id='gallmakenum' name='gallmakenum' value='100'>条
<input type="submit" name="Submit" value="生成" class="input">
</td>
</tr>
</form> 

<form name="baiduform" action="admin_makehtml.php?action=baiduxml" method="post">
<tr>
<td width="20%">生成视频Baidu XML：</td>
<td>
&nbsp;<input type='text' id='ballmakenum' name='ballmakenum' value='100'>条
<input type="submit" name="Submit" value="生成" class="input">
</td>
</tr>
</form> 

<form name="rssform" action="admin_makehtml.php?action=rssxml" method="post">
<tr>
<td width="20%">生成视频RSS XML：</td>
<td>
&nbsp;<input type='text' id='rallmakenum' name='rallmakenum' value='100'>条
<input type="submit" name="Submit" value="生成" class="input">
</td>
</tr>
</form>
</table>
<script language="javascript" src="<?php echo $surls?>checkphp.js"></script>
<?php
}

function makeindex()
{
	global $flag,$mac,$template;
	$fpath="";
	if ($flag=="art"){
		$suffix= app_artsuffix;  $fpath= "artindex.html";
		$mac["appid"] = 20;
	}
	else{
		$suffix=app_vodsuffix;  $fpath= "index.html"; 
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
	global $flag,$mac,$template;
	if ($flag=="art"){
		$suffix= app_artsuffix;
		$slink= "../artmap.".$suffix;
		$mac["appid"] = 21;
	}
	else{
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
	if (isN($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_addtime,d_time FROM {pre}vod WHERE d_type >0 and d_hide=0 ORDER BY d_time DESC limit 0,".$allmakenum;
	$rs = $db->query($sql);
	$googleStr =  "<?xml version=\"1.0\" encoding=\"utf-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">"."\n";
	$googleStr .= "<url><loc>http://".app_siteurl."/</loc><lastmod>".date('Y-m-d',time())."</lastmod><changefreq>hourly</changefreq><priority>1.0</priority></url>";
	
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$googleStr .= "<url><loc>".$viewLink."</loc><lastmod>".getDatet("Y-m-d",$row["d_time"])."</lastmod><changefreq>daily</changefreq><priority>0.8</priority></url>";
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
	if (isN($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_addtime,d_time FROM {pre}vod WHERE d_type >0 and d_hide=0 ORDER BY d_time DESC limit 0,".$allmakenum;
	
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
	if (isN($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_starring,d_content,d_addtime,d_time FROM {pre}vod WHERE d_type >0 and d_hide=0 ORDER BY d_time DESC limit 0,".$allmakenum;
	
	$rs = $db->query($sql);
	$rssStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?><rss version='2.0'><channel><title><![CDATA[".app_sitename."]]></title>	<description><![CDATA[".app_sitename."]]></description><link>http://".app_siteurl."</link><language>zh-cn</language><docs>".app_sitename."</docs><generator>Rss Powered By ".app_siteurl."</generator><image><url>http://".app_siteurl."/images/logo.gif</url></image>";
	
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$rssStr .= "<item><title><![CDATA[".$row["d_name"]."]]></title><link>".$viewLink."</link><author><![CDATA[".$row["d_starring"]."]]></author><pubDate>".$row["d_time"]."</pubDate><description><![CDATA[". XmlSafeStr(strip_tags(substring($row["d_content"],150)))."]]></description></item>";
	}
	unset($rs);
	$rssStr .= "</channel></rss>";
	$slink = "../rss.xml";
	fwrite(fopen($slink,"wb"),$rssStr);
	echo "生成完毕<a target='_blank' href='../rss.xml'><font color=red>浏览RSS</font></a><br>";
}

function makediypage()
{
	$fname = be("all","fname");
	if (isN($fname)){ echo "请选择自定义页面"; return; }
	makediypagebyid ($fname);
}

function makediypageall()
{
	$filedir.= "../template/" . app_templatedir . "/". app_htmldir ."/" ;
    $fso=opendir($filedir);
	while ($file=readdir($fso)){
		$fullpath = "$filedir/$file";
		if(is_file($fullpath)){
			if (substring($file,6)== "label_"){
				makediypagebyid ($file);
			}
		}
	}
	closedir($fso);
	unset($fso);
	echo "生成全部自定义页面完毕";
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
	$typeids = be("arr","mtype");
	$num=be("get","num");
	if(isN($typeids)) { $typeids = be("get","mtype"); }
	if (isN($typeids) || $typeids=="0"){ echo "请选择分类..."; return; }
	$typearr = explode(",",$typeids);
	$typearrconunt = count($typearr);
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>=intval($typearrconunt)){
			if($action2=="all"){
				echo "<br>准备生成首页、地图页、XML数据，请稍候...<br>";
				makeotherday();
				return;
			}
			else{
				echo "所选分类生成完毕"; return;
			}
		}
	}
	$typeid = trim($typearr[$num]);
	unset($typearr);
	maketypebyid ($typeid);
	echo "<br>暂停". $makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNexttype();\",".$makeinterval."000);function makeNexttype(){location.href='?action=type&mtype=".$typeids."&flag=".$flag."&num=".($num + 1)."&psize=".$psize."&action2=".$action2."';}</script>";
}

function maketypeall()
{
	global $flag,$makeinterval,$psize,$cache,$num;
    $num=be("get","num");
	if ($flag=="art"){
		$typearr = $cache[1];
	}
	else{
		$typearr = $cache[0];
	}
	$typearrconunt = count($typearr);
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>=intval($typearrconunt)){
			echo "所有分类生成完毕"; return;
		}
	}
	$typeid = trim( $typearr[$num]["t_id"] );
	maketypebyid ($typeid);
	echo "<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNexttype();\",".$makeinterval."000);function makeNexttype(){location.href='?action=typeall&flag=".$flag."&num=".($num + 1)."&psize=".$psize."';}</script>";
}

function maketypebyid($typeid)
{
	global $flag,$stime,$psize,$makeinterval,$cache,$mac,$db,$template,$makesize,$action,$action2,$typeids,$startnum,$num;
	
	if ($flag=="art"){
		$typearr = getValueByArray($cache[1],"t_id",$typeid);
		$mac["arttypeid"] = $typeid;
		$sql = "select count(a_id) from {pre}art where a_hide=0 and a_type IN (". $typearr["childids"].")";
		$ptype = "arttype";
	}
	else{
		$typearr = getValueByArray($cache[0],"t_id",$typeid);
		$mac["vodtypeid"] = $typeid;
		$sql = "select count(d_id) from {pre}vod where d_hide=0 and d_type IN (". $typearr["childids"] .") ";
		$ptype = "vodtype";
	}
    $template->html = getFileByCache("template_" . $flag . "list_" . $typeid, root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $typearr["t_template"]);
    if (isN($psize)){ $psize = $template->getPageListSizeByCache($flag."page",$flag);}
    if (!isNum($psize)) { $psize = 10;}
    $tempLabelStr = $template->html;
    
    $nums = $db->getOne($sql);
    $pcount = ceil($nums/$psize);
    
    if ($nums == 0){ echo "<font color='red'>ID为 " . $typeid . " 的分类没有数据</font><br>"; $pcount = 1; }
    echo "正在开始生成分类<font color='red'>" . $typearr["t_name"] . "</font>的列表<br>";
    $num2=1;
    $rc=true;
    
    
    
    for ($i=$startnum;$i<=$pcount;$i++){
        $mac["page"] = $i;
        if ($flag=="art"){
        	$template->loadlist ("art", $typearr);
        }
        else{
        	$template->loadlist ("vod", $typearr);
        }
        if($rc){
        	$tmppcount = $template->par_maxpage;
        	if(isNum($tmppcount)) { $tmppcount=intval($tmppcount); } else { $tmppcount=0; }
        	if($tmppcount>0 && $pcount> $tmppcount){
        		$pcount = $tmppcount;
        	}
        }
        
        $rc=false;
        $template->page_type = $ptype;
        $typeLink = $template->getPageLink($i);
        
        if (app_installdir != "/"){ $typeLink = replaceStr($typeLink, app_installdir, "../");} else { $typeLink = ".." . $typeLink;}
        $path = dirname($typeLink);
		mkdirs($path);
        fwrite(fopen($typeLink,"wb"),$template->html);
        echo " 第" . $i . "页 <a target='_blank' href='" . $typeLink . "'>" . replaceStr($typeLink, "../", "") . "&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
        
        if ($makesize == $num2){
        	echo "<br>该分类数据较多,暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNexttype();\",".$makeinterval."000);function makeNexttype(){location.href='?action=".$action."&mtype=".$typeids."&flag=".$flag."&num=".$num."&startnum=".($i+1)."&psize=".$psize."&action2=".$action2."';}</script>";
        	exit;
        }
        $num2++;
        ob_flush();flush();
    }
    echo "页面生成时间: " . (execTime()-$stime) . "秒 &nbsp;";
}


function makeview()
{
	$typeids = be("arr","mtype");
	$num=be("all","num");
	if(isN($typeids)) { $typeids = be("get","mtype"); }
	if (isN($typeids) || $typeids=="0"){ echo "请选择分类..."; return; }
	$typearr = explode(",",$typeids);
	$typearrconunt = count($typearr);
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>=intval($typearrconunt)){
			echo "所选分类生成完毕"; return;
		}
	}
	$typeid = trim($typearr[$num]);
	unset($typearr);
	makeviewbytype ($typeid,$typeids,$typearrconunt);
}

function makeviewall()
{
	global $flag,$cache;
	$num = be("get","num");
	if ($flag=="art"){
		$typearr = $cache[1];
	}
	else{
		$typearr = $cache[0];
	}
	$typearrconunt = count($typearr);
	
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>= intval($typearrconunt)){
			echo "生成全部内容页完成"; return;
		}
	}
	$typeid = $typearr[$num]["t_id"];
	makeviewbytype ($typeid,$ids,$typearrconunt);
}

function makeviewbytype($typeid,$ids,$allnum)
{
	global $action,$action2,$flag,$stime,$sql,$sql1,$makeinterval,$db,$cache,$makesize;
	
	$macpage = be("all","page");
	$num = 	be("get","num");
	if (isN($num)){ $num = 0;} else {$num = intval($num);}
	if (isN($macpage)){ $macpage=1;} else {$macpage=intval($macpage);}
	
	if ($flag== "art"){
		$typearr = getValueByArray($cache[1],"t_id",$typeid);
		$sql = $sql ." and a_type=" .$typeid;
		$sql1 = $sql1 . " and a_type=" .$typeid;
	}
	else{
		$typearr = getValueByArray($cache[0],"t_id",$typeid);
		$sql = $sql ." and d_type=". $typeid;
		$sql1 = $sql1 . " and d_type=" .$typeid;
	}
	$sql .= " limit ".($makesize *($macpage-1)).",".$makesize;
	
	$typename = $typearr["t_name"];
	$nums = $db->getOne($sql1);
	$pcount = ceil($nums/$makesize);
	
	if ($nums==0){ 
		if (isN($action2) && ($num>$allnum)){
			echo "<font color='red'>ID为 ".$typeid." 的分类没有数据</font><br>";
		}
	    else{
			echo "恭喜<font color='red'>".$typename."</font>搞定<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"jump();\",".$makeinterval."000);function jump(){location.href='?action=".$action."&mtype=".$ids."&flag=".$flag."&num=".($num + 1)."&page=".$macpage."&action2=".$action2."';}</script>";
			return;
		}
	}
	
	$rs = $db->query($sql);
	echo "正在开始生成栏目<font color='red'>".$typename."</font>的内容页,当前是第<font color='red'>".$macpage."</font>页,共<font color='red'>".$pcount."</font>页<br>";
	
	while ($row = $db ->fetch_array($rs)){
		makeviewbyrs ($row,$typearr);
	}
	unset($rs);
	
	if ($macpage == $pcount || $nums<$makesize){
		if (isN($action2) && $num>$allnum){
			echo "<font color='red'>恭喜".$typename."搞定</font>";
		}
		else{
			echo "页面生成时间: ".(execTime()-$stime)."秒 &nbsp;<font color='red'>恭喜".$typename."搞定</font><br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"jump();\",".$makeinterval."000);function jump(){location.href='?action=".$action."&mtype=".$ids."&flag=".$flag."&num=".($num + 1)."&page=1&action2=".$action2."';}</script>";return;
		}
}
	
	echo  "页面生成时间: " . (execTime()-$stime)."秒 &nbsp;<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"jump();\",".$makeinterval."000);function jump(){location.href='?action=".$action."&mtype=".$ids."&page=".($macpage + 1)."&action2=".$action2."&num=".$num."&flag=".$flag."';}</script>";
}

function makeviewpart()
{
	global $sql,$flag,$db,$cache,$makesize;
	$startnum = be("all","startnum");
	$endnum = be("all","endnum");
	$psize=$makesize;
	if (isN($startnum) && isN($endnum)){	showMsg ("至少需要输入第1个ID！"  ,"admin_makehtml.php") ;}
	if (!isNum($startnum) && !isNum($endnum)){ showMsg ("只能输入数字,请检查！"  ,"admin_makehtml.php") ;}
	if ($flag=="art"){
		$sql = $sql . " and a_id=" ;
	}
	else{
		$sql = $sql ." and d_id =";
	}
	
	$startnum = intval($startnum) ;
	if (isNum($endnum)){ $endnum = intval($endnum);} else { $endnum = $startnum ;$mflag=true;}
	
	if ($startnum > $endnum){
		if ($endnum+$psize <= $startnum ){ $tempnum1 = $endnum+$psize;$mflag=false;} else {$tempnum1 =$endnum;$mflag=true;}
		$tempnum2=$endnum;
		$endnum = $endnum + $psize;
	}
	else{
		if ($startnum+$psize <= $endnum ){ $tempnum1 = $startnum+$psize ;$mflag=false;} else {$tempnum1 =$startnum;$mflag=true;}
		$tempnum2=$startnum;
		$startnum = $startnum+$psize;
	}
	
	for($i=$tempnum2;$i<=$tempnum1;$i++){
		$tmpsql = $sql . $i;
		$row = $db->getRow($tmpsql);
		if($row){
			if($flag=="art"){ $typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);} else { $typearr = getValueByArray($cache[0],"t_id",$row["d_type"]) ;}
			makeviewbyrs ($row,$typearr);
		}
		unset($row);
	}
	
	if ($mflag){
		echo "生成内容页完成"; return;
	}
	else{
		echo "<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"jump();\",".$makeinterval."000);function jump(){location.href='?action=viewpart&startnum=".$startnum."&endnum=".$endnum."&flag=".$flag."';}</script>";
	}
}

function makeviewpl()
{
	global $flag,$sql,$cache,$db,$stime;
	$backurl = getReferer();
	$ids = be("arr","d_id");
	if(isN($ids)) { $ids = be("get","d_id"); }
	if (isN($ids)){ echo "您没有选择任何数据"; exit; }
  	$idarr =  explode(",",$ids);
  	
  	if ($flag=="art"){
		$sql = $sql ." and a_id=" ;
	}
	else{
		$sql = $sql ." and d_id = ";
	}
	for ($i=0;$i<count($idarr);$i++){
		$tmpsql = $sql . $idarr[$i];
		$row =$db->getRow($tmpsql);
		
		if($row){
			if ($flag=="art"){
				$typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);
			}
			else{
				$typearr = getValueByArray($cache[0],"t_id",$row["d_type"]);
			}
			makeviewbyrs ($row,$typearr);
			unset($typeearr);
		}
	}
	unset($idarr);
	unset($rs);
	echo "页面生成时间: " . (execTime()-$stime) . "秒<br>";
	echo "生成内容页完成<script language=\"javascript\">setTimeout(\"jump();\",2000);function jump(){location.href='".$backurl."';}</script>";
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
			$ids = $ids . $row["a_type"] . ",";
		}
			else{
			$ids = $ids . $row["d_type"] . ",";
		}
	}
	if (substring($ids, 1,strlen($ids)-1) == ","){  $ids = substring($ids, strlen($ids)-1 ,0);}
	
	echo "<br>准备生成当天数据的分类，请稍候...<script language=\"javascript\">setTimeout(\"jump();\",".$makeinterval."000);function jump(){location.href='?action=type&flag=".$flag."&mtype=".$ids."&action2=".$action2."';}</script>";
}

function makeviewday()
{
	global $action,$action2,$flag,$sql,$sql1,$stime,$makeinterval,$db,$cache,$makesize;
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
	$nums = $db->getOne($sql1 . $where );
	$pcount=ceil($nums/$makesize);
	
	if (isN($macpage)){ $macpage=1;} else { $macpage= intval($macpage);}
	if ($nums==0){ 
		echo "<font color='red'>今天没有更新数据</font><br>";
		return;
	}
	$sql = $sql . " limit ".($makesize*($macpage-1)).",".$makesize;
	$rs = $db->query($sql);
	echo "正在开始生成<font color='red'>今日数据</font>的内容页,当前是第<font color='red'>".$macpage."</font>页,共<font color='red'>".$pcount."</font>页<br>";
	while($row = $db ->fetch_array($rs)){
		if ($flag=="art"){ $typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);} else { $typearr = getValueByArray($cache[0],"t_id",$row["d_type"]);}
		makeviewbyrs ($row,$typearr);
	}
	unset($rs);
	
	if ($macpage == $pcount || $nums<$makesize){
		echo "<font color='red'>恭喜今日数据搞定</font>";
		if($action2=="all"){
			maketypeday();
		}
		return;
	}
	echo "页面生成时间: ".(execTime()-$stime)."秒 &nbsp;<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"jump();\",".$makeinterval."000);function jump(){location.href='?action=viewday&page=".($macpage + 1)."&action2=".$action2."&num=".$num."&flag=".$flag."';}</script>";
}

function makeviewnomake()
{
	global $action,$action2,$flag,$sql,$sql1,$stime,$makeinterval,$db,$cache,$makesize;
	$macpage = be("get","page");
	$num = 	be("get","num");
	if (isN($num)){ $num = 0;} else { $num = intval($num);}
	
	if ($flag=="art"){
		$where = " and a_maketime < a_time or a_maketime is null ";
	}
	else{
		$where = " and d_maketime < d_time or d_maketime is null ";
	}
	
	$sql = $sql . $where;
	$nums = $db->getOne($sql1 . $where );
	$pcount = ceil($nums/$makesize);
	
	if (isN($macpage)){ $macpage=1;} else { $macpage= intval($macpage);}
	if ($nums==0){ 
		echo "<font color='red'>没有更新后未生成数据</font><br>";
		return;
	}
	$sql = $sql . " limit 0,".$makesize;
	$rs = $db->query($sql);
	echo "正在开始生成<font color='red'>更新后未生成数据</font>的内容页,当前是第<font color='red'>".$pcount."</font>页,共<font color='red'>".$pcount."</font>页<br>";
	while($row = $db ->fetch_array($rs)){
		if ($flag=="art"){ $typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);} else { $typearr = getValueByArray($cache[0],"t_id",$row["d_type"]);}
		makeviewbyrs ($row,$typearr);
	}
	unset($rs);
	
	if ($macpage == $pcount || $nums<$makesize){
		echo "<font color='red'>恭喜更新后未生成数据搞定</font>";
		return;
	}
	echo "页面生成时间: ".(execTime()-$stime)."秒 &nbsp;<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"jump();\",".$makeinterval."000);function jump(){location.href='?action=viewnomake&page=".($macpage + 1)."&action2=".$action2."&num=".$num."&flag=".$flag."';}</script>";
}

function makeviewbyrs($rs,$typearr)
{
	global $flag,$db,$template,$mac,$stime;
	$tname = $typearr["t_name"];
	$tpath = $typearr["t_enname"];
	
	if ($flag=="vod"){
		if (!is_array($typearr)){
			echo $rs["d_name"] . "所属分类ID". $rs["d_type"]."未找到，跳过生成<br>";
			return "";
		}
		
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
				$template->playdownlist ("play",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_playfrom"], $rs["d_playserver"], $rs["d_playurl"]);
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
				$template->playdownlist ("down",$rs["d_id"], $rs["d_name"], $rs["d_enname"], $rs["d_addtime"],$rs["d_type"], $typearr["t_name"], $typearr["t_enname"], $rs["d_downfrom"], $rs["d_downserver"], $rs["d_downurl"]);
				$template->html = replaceStr($template->html,"[downinfo:num]","");
				$template->html = replaceStr($template->html,"[downinfo:src]","");
	            $template->html = replaceStr($template->html, "[downinfo:name]","");
	            $template->html = replaceStr($template->html, "[downinfo:urlpath]","");
				$template->run ("vod");
				$downLink = $template->getVodDownUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath,1,1);
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
							
							$downLink = $template->getVodDownUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath, ($i+1),($j+1));
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
			else if(app_voddownviewtype ==5  ){
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
					
					$downLink = $template->getVodDownUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_addtime"],$rs["d_type"],$tname,$tpath, ($i+1),1);
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
		if (!is_array($typearr)){
			echo $rs["a_title"] . "所属分类ID". $rs["a_type"]."未找到，跳过生成<br>";
			return ;
		}
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
	ob_flush();flush();
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
		ob_flush();flush();
	}
}

function maketopic()
{
	$topic=  be("arr","mtopic");
	if(isN($topic)){ $topic = be("get","mtopic"); }
	if (isN($topic) || $topic==0){ echo "请选择专题..."; return; }
	makeTopicById ($topic);
	echo "生成专题列表完成"; return;
}

function maketopicall()
{
	global $flag,$makeinterval,$cache;
    $num=be("get","num");
	if ($flag=="art"){
		$topicarr = $cache[3];
	}
	else{
		$topicarr = $cache[2];
	}
	$topicarrconunt = count($topicarr);
	if ( $topicarrconunt >0){
		if (isN($num)){
			$num = 0;
		}
		else{
			if (intval($num)>intval($topicarrconunt)-1){
				echo "所有专题生成完毕"; return;
			}
		}
		makeTopicById ($topicarr[$num]["t_id"]);
		echo "<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNexttype();\",".$makeinterval."000);function makeNexttype(){location.href='?action=topicall&flag=".$flag."&num=".($num + 1) ."';}</script>";
	}
	else{
		echo "<font color='red'>没有任何专题数据</font><br>";
	}
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
</body>
</html>