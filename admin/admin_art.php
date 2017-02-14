<?php
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");
chkLogin();

$action = be("all","action");
$_SESSION["upfolder"] = "../upload/art";

switch($action)
{
	case "add":
	case "edit" : headAdmin ("文章管理"); info();break;
	case "save" : save();break;
	default : headAdmin ("文章管理"); main();break;
}
dispseObj();

function save()
{
	global $db;
	$a_id = be("post","a_id"); $a_title = be("post","a_title");
	$a_subtitle = be("post","a_subtitle"); $a_entitle = be("post","a_entitle");
	$a_type = be("post","a_type"); $a_content = be("post","a_content");
	$a_author = be("post","a_author"); $a_color = be("post","a_color");
	$a_hits = be("post","a_hits"); $a_dayhits = be("post","a_dayhits");
	$a_weekhits = be("post","a_weekhits"); $a_monthhits = be("post","a_monthhits");
	$a_hide = be("post","a_hide"); $a_addtime = be("post","a_addtime");
	$a_time = be("post","a_time"); $a_hitstime = be("post","a_hitstime");
	$a_pic = be("post","pic"); $a_from = be("post","a_from");
	$backurl = be("post","backurl"); $a_letter = be("post","a_letter");
	$flag = be("post","flag"); $a_topic = be("post","a_topic");
	$uptime = be("post", "uptime"); $rndhits= be("post","rndhits");
	$a_level = be("post","a_level");
	
	if (isN($a_addtime)) {  $a_addtime = date('Y-m-d H:i:s',time()); }
    if( ($flag=="edit" && $uptime=="1") || ($flag=="add") ){
    	$a_time = date('Y-m-d H:i:s',time());
    }
    if($rndhits=="1") { $a_hits= rndNum(1,1000); }
	if (isN($a_title)) { echo "标题不能为空";exit;}
	if (!isNum($a_type)) { echo "分类不能为空";exit;}
	if (!isNum($a_hits)) { $a_hits= 0;}
	if (!isNum($a_hide)) { $a_hide = 0;}
	if (!isNum($a_topic)) { $a_topic = 0;}
	if (!isNum($a_level)) { $a_level = 0;}
    if (!isNum($a_dayhits)) { $a_dayhits = 0;}
    if (!isNum($a_weekhits)) { $a_weekhits = 0;}
    if (!isNum($a_monthhits)) { $a_monthhits = 0;}
	if (isN($a_entitle)) { $a_entitle = Hanzi2Pinyin($a_title);}
	if (isN($a_letter)) { $a_letter = strtoupper(substring($a_entitle,1)); }
	
	
	if (strpos($a_entitle, "*")>0 || strpos($a_entitle, ":")>0 || strpos($a_entitle, "?")>0 || strpos($a_entitle, "\"")>0 || strpos($a_entitle, "<")>0 || strpos($a_entitle, ">")>0 || strpos($a_entitle, "|")>0 || strpos($a_entitle, "\\")>0){
        echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号";exit;
    }
    
	if ($flag=="edit"){
		$db->Update ("{pre}art",array("a_title","a_subtitle","a_entitle","a_letter","a_from","a_type","a_content","a_author","a_color","a_hits","a_dayhits","a_weekhits","a_monthhits","a_hide","a_time","a_pic","a_topic","a_level"),array($a_title,$a_subtitle,$a_entitle,$a_letter,$a_from,$a_type,$a_content,$a_author,$a_color,$a_hits,$a_dayhits,$a_weekhits,$a_monthhits,$a_hide,$a_time,$a_pic,$a_topic,$a_level),"a_id=".$a_id);
	}
	else{
		$backurl="admin_art.php?action=add";
		$db->Add ("{pre}art",array("a_title","a_subtitle","a_entitle","a_letter","a_from","a_type","a_content","a_author","a_color","a_hits","a_dayhits","a_weekhits","a_monthhits","a_addtime","a_time","a_pic","a_topic","a_level"),array($a_title,$a_subtitle,$a_entitle,$a_letter,$a_from,$a_type,$a_content,$a_author,$a_color,$a_hits,$a_dayhits,$a_weekhits,$a_monthhits,$a_addtime,$a_time,$a_pic,$a_topic,$a_level));
	}
	echo "保存完毕";
}

function main()
{
	global $db,$template,$cache;
	$keyword = be("all","keyword"); $stype = be("all","stype");
	$order = be("all","order");  $topic = be("all","topic");
	$pagenum = be("all","page"); $hide = be("all","hide");
	$repeat = be("all", "repeat");   $repeatlen = be("all", "repeatlen");
	$level = be("all","level");
	
	if (!isNum($stype)) { $stype =0; } else {$stype=intval($stype);}
	if (!isNum($topic)) { $topic =0 ; } else {$topic=intval($topic);}
	if (!isNum($level)) { $level =0; } else {$level=intval($level);}
	if (!isNum($repeatlen)) { $repeatlen = 0;}
	if (!isNum($hide)) { $hide=-1;} else {$hide=intval($hide);}
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	
	$where = " 1=1 ";
	if(!isN($keyword)){	$where = $where . " AND a_title LIKE '%". $keyword ."%' ";	}
	if ($stype >0) {
		$typearr = getValueByArray($cache[1], "t_id" ,$stype );
		if(is_array($typearr)){
			$where = $where . " and a_type in (" . $typearr["childids"] . ")";
		}
		else{
    		$where .= " AND a_type=" . $stype . " ";
    	}
    }
		
	if ($stype ==-1){ $where = $where . " AND a_type =0 "; }
	if ($topic >0) { $where = $where . " AND a_topic = ".$topic." "; }
	if ($level > 0) { $where .= " AND a_level = " . $level . " ";}
	if($hide>-1){
    	$where .= " AND a_hide=".$hide ." ";
    }
    
	if ($repeat == "ok"){
        $repeatSearch = " a_title ";
        if($repeatlen>0){
			$repeatSearch = " substring(a_title,1,".$repeatlen.") as `a_title1` ";
		}
        $where .= " AND a_title IN (SELECT " . $repeatSearch . " FROM {pre}art GROUP BY " . $repeatSearch . " HAVING COUNT(*)>1) ";
    }
    
	if (isN($order)) { $order = "a_addtime"; }
	
	$sql = "SELECT count(*) FROM {pre}art where ".$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	
	$sql = "SELECT a_id, a_title,a_subtitle, a_entitle, a_type,a_topic, a_content, a_author, a_color,a_addtime, a_time, a_hits,a_hide,a_level FROM {pre}art WHERE " . $where . " ORDER BY " . $order . " DESC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#btnrepeat").click(function(){
		var repeatlen = $("#repeatlen").val();
		var reg = /^\d+$/;
		var re = repeatlen.match(reg);
		if (!re){ alert("请输入数字");$("#repeatlen").focus();return;}
		if (repeatlen >10){ alert("长度最大10");$("#repeatlen").focus();return;}
		var url = "admin_art.php?repeat=ok&repeatlen=" + repeatlen;
		window.location.href=url;
	});
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&tab={pre}art");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#plsc").click(function(){
		var ids="",rc=false;
		$("input[name='a_id[]']").each(function() {
			if(this.checked){
				if(rc)ids+=",";
				ids =  ids + this.value;
				rc=true;
			}
        });
		$("#form1").attr("action","admin_makehtml.php?action=viewpl&flag=art&d_id="+ids);
		$("#form1").submit();
	});
});

function filter(){
	var url = "admin_art.php?keyword="+encodeURI($("#keyword").val())+"&stype="+$("#stype").val()+"&level="+$("#level").val()+"&topic="+$("#topic").val()+"&order="+$("#order").val()+"&hide="+$("#hide").val()+"&repeat="+$("#repeatok").val()+"&repeatlen="+$("#repeatlen").val();
	window.location.href=url;
}
function gosyncpic(){
	if(confirm('确定要同步下载远程图片吗?数据不可恢复，请做好备份')){
		location.href = 'admin_pic.php?action=syncartpic';
	}
}
</script>
<table class="tb">
    <tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2" >
	过滤条件：<select id="stype" name="stype">
	<option value="0">文章栏目</option>
	<option value="-1" <?php if($stype==-1) { echo "selected";} ?>>没有栏目</option>
	<?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$stype)?>
	</select>
	&nbsp;
	<select id="topic" name="topic">
	<option value="0">文章专题</option>
	<?php echo makeSelect("{pre}art_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;",$topic)?>
	</select>
	&nbsp;
	<select id="order" name="order">
	<option value="a_time">文章排序</option>
	<option value="a_id" <?php if($order=="a_id") { echo "selected";}?> >文章编号</option>
	<option value="a_title" <?php if($order=="a_title") { echo "selected";}?> >文章名称</option>
	<option value="a_hits" <?php if($order=="a_hits") { echo "selected";}?>>文章人气</option>
	</select>
	&nbsp;
	<select id="level" name="level">
	<option value="0">文章推荐</option>
	<option value="1" <?php if($level==1) { echo "selected";} ?>>推荐1</option>
	<option value="2" <?php if($level==2) { echo "selected";} ?>>推荐2</option>
	<option value="3" <?php if($level==3) { echo "selected";} ?>>推荐3</option>
	<option value="4" <?php if($level==4) { echo "selected";} ?>>推荐4</option>
	<option value="5" <?php if($level==5) { echo "selected";} ?>>推荐5</option>
	</select>
	&nbsp;
	<select id="hide" name="hide">
	<option value="-1">文章显隐</option>
	<option value="0" <?php if ($hide==1){ echo "selected";} ?>>显示</option>
	<option value="1" <?php if ($hide==2){ echo "selected";} ?>>隐藏</option>
	</select>
	</td>
	</tr>
	<tr>
	<td>
	&nbsp;关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">
	<?php
		if ($repeat!="ok"){
			echo "<span style=\"display:none\">";
		}
		else{
			echo "<span>";
		}
	?>
	&nbsp; 检测名称长度：<input id="repeatlen" size="2" name="repeatlen" >
	&nbsp;<input class="input" type="button" value="检测重复数据" id="btnrepeat" name="btnrepeat" >
	</span>
	</td>
	<td width="150px">
	&nbsp;<span>【<a href="###" onclick="javascript:gosyncpic();"><font color="red"><strong>同步下载远程图片</strong></font></a>】</span>
	</td>
	</tr>
	</table>
	</td>
	</tr>
</table>
<form action="" method="post" id="form1" name="form1">
<table class="tb">
	<tr>
	<td width="4%">&nbsp;</td>
	<td>标题</td>
	<td width="9%">分类</td>
	<td width="6%">人气</td>
	<td width="5%">推荐</td>
	<td width="5%">专题</td>
	<td width="5%">浏览</td>
	<td width="15%">更新时间</td>
	<td width="15%">操作</td>
    </tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="7">没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$a_id=$row["a_id"];
		  		$tname= "未知";
				$tenname="";
		  		$typearr = getValueByArray($cache[1], "t_id" ,$row["a_type"]);
				if(is_array($typearr)){
					$tname= $typearr["t_name"];
					$tenname= $typearr["t_enname"];
				}
	?>
    <tr>
	<td><input name="a_id[]" type="checkbox" value="<?php echo $row["a_id"]?>" /></td>
	<td><?php echo getColorText($row["a_title"],$row["a_color"],0)?>
		<?php if($row["a_hide"]==1){echo "<font color=\"red\">[隐藏]</font>";} ?>
	</td>
	<td><?php echo $tname?></td>
	<td><?php echo $row["a_hits"]?></td>
	<td id="tj<?php echo $a_id?>">
	<?php echo "<img src=\"../images/icons/ico".$row["a_level"].".gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('tj','".$a_id."','art')\"/>"?>
	</td>
	<td id="zt<?php echo $row["a_id"]?>" >
	<?php if($row["a_topic"]==0){?>
	<?php echo "<img src=\"../images/icons/icon_02.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('zt','".$row["a_id"]."','art')\"/>"?>
	<?php }else{?>
	<?php echo "<img src=\"../images/icons/icon_01.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"ajaxdivdel('".$row["a_id"]."','zt','art')\"/>"?>
	<?php }?>
	</td>
	<td>
	<?php
		if ($row["a_type"] == 0){
			$mlink = "#";
		}
		else{
	 		$mlink = "../". $template->getArtLink($row["a_id"],$row["a_title"],$row["a_entitle"],$row["a_addtime"],$row["a_type"],$tname,$tenname,true);
		}
		$mlink = replaceStr($mlink, "../".app_installdir,"../");
		if (substring($mlink,1,strlen($mlink)-1)=="/") { $mlink = $mlink ."index.". app_artsuffix;}
		
		if (app_artcontentviewtype == 2){
			if (file_exists($mlink)){
		 	?>
		 	<a target="_blank" href="<?php echo $mlink?>"><Img src="../images/icons/html_ok.gif" border="0" alt='浏览' /></a>
		 	<?php
		 	}
		 	else{
		 	?>
		 	<a  href="admin_makehtml.php?action=viewpl&flag=art&d_id=<?php echo $row["a_id"]?>"><Img src="../images/icons/html_no.gif" border="0" alt='生成' /></a>
		 	<?php
   		 	}
		}
	 else{
	 ?>
	 <a target="_blank" href="<?php echo $mlink?>"><Img src="../images/icons/html_ok.gif" border="0" alt='浏览' /></a>
	 <?php
	 }
	?>
	</td>
	<td><?php echo getColorDay($row["a_time"])?></td>
	<td><A href="admin_art.php?action=edit&a_id=<?php echo $row["a_id"]?>">修改</a> | <a href="admin_ajax.php?action=del&tab={pre}art&a_id=<?php echo $row["a_id"]?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td colspan="9">
    全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'a_id[]');"/>&nbsp;
	批量操作：<input type="button" id="btnDel" value="删除" class="input" />
	<input type="button" id="pltj" value="推荐" onClick="plset('pltj','art')" class="input">
	<input type="button" id="plfl" value="分类" onClick="plset('plfl','art')" class="input">
	<input type="button" id="plrq" value="人气" onClick="plset('plrq','art')" class="input">
	<input type="button" id="plzt" value="专题" onClick="plset('plzt','art')" class="input">
	<input type="button" id="plsc" value="生成" class="input">
	<input type="button" id="plyc" value="显隐" onClick="plset('plyc','art')" class="input">
	<span id="plmsg" name="plmsg"></span>
	</td></tr>
	<tr>
	<td align="center" colspan="8">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_art.php?page={p}&keyword=" . urlencode($keyword) ."&order=".$order ."&stype=" . $stype ."&state=".$state."&level=".$level."&repeat=".$repeat."&repeatlen=".$repeatlen."&hide=".$hide)?>
	</td>
    </tr>
</table>
</form>
<?php
unset($rs);
}

function info()
{
	global $db,$action;
	$backurl = getReferer();
	if (strpos($backurl,"admin_art.php")<=0){ $backurl="admin_art.php";}
	if ($action=="edit"){
		$a_id = be("get","a_id");
		$row = $db->getRow("SELECT * FROM {pre}art WHERE a_id=" . $a_id);
		if (!$row){
			errmsg ("系统信息","错误没有找到该数据");
		}
		else{
			$a_title=$row["a_title"]; $a_subtitle=$row["a_subtitle"];
			$a_entitle=$row["a_entitle"]; $a_type=$row["a_type"];
			$a_content=$row["a_content"]; $a_author=$row["a_author"];
			$a_color=$row["a_color"]; $a_hits=$row["a_hits"];
			$a_dayhits=$row["a_dayhits"]; $a_weekhits=$row["a_weekhits"];
			$a_monthhits=$row["a_monthhits"]; $a_hide=$row["a_hide"];
			$a_addtime=$row["a_addtime"]; $a_time=$row["a_time"];
			$a_hitstime=$row["a_hitstime"]; $a_pic=$row["a_pic"];
			$a_from=$row["a_from"]; $a_letter=$row["a_letter"];
			$a_topic=$row["a_topic"]; $a_level=$row["a_level"];
		}
		unset($row);
	}
	else{
		$a_from="本站";
		$a_author=app_sitename;
	}
?>
<script language="javascript" src="editor/xheditor-zh-cn.min.js"></script>
<script language="javascript" src="editor/xheditor_lang/zh-cn.js"></script>
<script type="text/javascript" src="../js/adm/jscolor.js"></script>
<script language="javascript">
var ac="<?php echo $action?>";
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			a_type:{
				required:true
			},
			a_title:{
				required:true,
				maxlength:128
			},
			a_letter:{
				maxlength:1	
			},
			a_hits:{
				number:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        if (ac=="add"){
		        $.messager.defaults.ok = "确定";
				$.messager.defaults.cancel = "返回";
				$.messager.confirm('系统提示', '是否继续添加数据?', function(r){
					if(r==true){
						location.href = "admin_art.php?action=add";
					}
					else{
		        		location.href = $("#backurl").val();
		        	}
		        });
	        }
	        else{
	        	location.href = $("#backurl").val();
	        }
	    }
	});
	$("#btnCancel").click(function(){
		location.href = $("#backurl").val();
	});
});
</script>
<div id="showpic" style="display:none;"><img name="showpic_img" id="showpic_img" width="120" height="160"></div>
<form action="?action=save" method="post" name="form1" id="form1">
<table class="tb">
	<input name="flag" type="hidden" value="<?php echo $action?>">
	<input name="a_id" type="hidden" value="<?php echo $a_id?>">
	<input name="a_addtime" type="hidden" value="<?php echo $a_addtime?>">
	<input name="a_time" type="hidden" value="<?php echo $a_time?>">
	<input id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">
	<tr>
	<td width="20%">文章标题：</td>
	<td>
	&nbsp;<select id="a_type" name="a_type" >
	<option value="">请选择分类</option>
	<?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$a_type)?>
	</select>
	&nbsp;
	<select id="a_topic" name="a_topic">
	<option value="0">文章专题</option>
	<?php echo makeSelect("{pre}art_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;",$a_topic)?>
	</select>
	&nbsp;<select id="a_level" name="a_level" >
	<option value="">选择推荐值</option>
	<option value="1" <?php if($a_level == 1) { echo "selected";} ?>>推荐1</option>
	<option value="2" <?php if($a_level == 2) { echo "selected";} ?>>推荐2</option>
	<option value="3" <?php if($a_level == 3) { echo "selected";} ?>>推荐3</option>
	<option value="4" <?php if($a_level == 4) { echo "selected";} ?>>推荐4</option>
	<option value="5" <?php if($a_level == 5) { echo "selected";} ?>>推荐5</option>
	</select>
	&nbsp;<select id="a_hide" name="a_hide">
	<option value="0" <?php if($a_hide==0) { echo "selected";} ?>>显示</option>
	<option value="1" <?php if($a_hide==1) { echo "selected";} ?>>隐藏</option>
	</select>
	&nbsp;<input type="checkbox" name="uptime" value="1" checked>更新时间
	&nbsp;<input type="checkbox" name="rndhits" value="1" >随机人气
	</td>
	</tr>
	<tr>
	<td>文章标题：</td>
	<td>
	&nbsp;<input id="a_title" name="a_title" size=70 value="<?php echo $a_title?>" >
	&nbsp;高亮颜色：<input id="a_color" name="a_color" type="text" size="5" class="color" value="<?php echo $a_color?>" style="background-color:<? echo $a_color?>">
	</td>
	</tr>
	<tr>
	<td>文章副标：</td>
	<td>
	&nbsp;<input id="a_subtitle" name="a_subtitle" size=70 value="<?php echo $a_subtitle?>" >
	</td>
	</tr>
	<tr>
	<td>拼音标题：</td>
	<td>
	&nbsp;<input id="a_entitle" name="a_entitle" size=70 value="<?php echo $a_entitle?>" >
	&nbsp;首字母： <input id="a_letter" name="a_letter" size=4 value="<?php echo $a_letter?>" >
	</td>
	</tr>
	<tr>
	<td>文章作者：</td>
	<td>
	&nbsp;<input id="a_author" name="a_author" size="40" value="<?php echo $a_author?>" >
	&nbsp;文章来源：&nbsp;<input id="a_from" name="a_from" size="40" value="<?php echo $a_from?>" >
	</td>
	</tr>
	<tr> 
    <td>图片：</td>
    <td>&nbsp;<input id="pic" name="pic" type="text" size="50" value="<?php echo $a_pic?>" onMouseOver="showpic(event,this.value);" onMouseOut="hiddenpic();"/>&nbsp;<iframe src="editor/uploadshow.php?action=art" scrolling="no" topmargin="0" width="320" height="24" marginwidth="0" marginheight="0" frameborder="0" align="center"></iframe></td>
	</tr>
	<tr>
	<td>其他：</td>
	<td>总人气：<input id="a_hits" name="a_hits" type="text" size="8" value="<?php echo $a_hits?>">
	&nbsp;月人气：<input id="a_monthhits" name="a_monthhits" type="text" size="8" value="<?php echo $a_monthhits?>">
	&nbsp;周人气：<input id="a_weekhits" name="a_weekhits" type="text" size="8" value="<?php echo $a_weekhits?>">
	&nbsp;日人气：<input id="a_dayhits" name="a_dayhits" type="text" size="8" value="<?php echo $a_dayhits?>">
	</td>
	</tr>
	<tr>
	<td>文章内容：<br>分页标示[artinfo:page]<br>模板中调用分页标签{maccms:page<br>才解析分页</td>
	<td>
<textarea name="a_content" id="a_content" class="xheditor {tools:'BtnBr,Cut,Copy,Paste,Pastetext,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,Align,List,Outdent,Indent,Link,Unlink,Img,Flash,Media,Table,Source,Fullscreen',width:'630',height:'250',upBtnText:'上传',html5Upload:false,upMultiple:1,upLinkUrl:'{editorRoot}upload.php?action=xht',upImgUrl:'{editorRoot}upload.php?action=xht'}"><?php echo $a_content?></textarea>
      </td>
	</tr>
	<tr align="center">
	<td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
    </tr>
</table>
</form>
<?php
}
?>
</body>
</html>