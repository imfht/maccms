<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");

switch($action)
{
	case "add":
	case "edit" : headAdmin ("模版管理"); info();break;
	case "save" : save();break;
	case "del" :del();break;
	case "view" : view();break;
	case "guide" : headAdmin ("模版管理"); guide();break;
	case "make" : headAdmin ("模版管理"); make();break;
	default : headAdmin ("模版管理"); main();break;
}
dispseObj();

function view()
{
	global $template;
	$file = be("get","file");
	$template->html = file_get_contents($file);
	$template->mark();
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
	$template->run ("other");
	echo $template->html;

}

function make()
{
	global $template;
	$file = be("get","file");
	$fname = be("get","fname");
	$file = be("get","file");
	$template->html = file_get_contents($file);
	
	$template->mark();
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
	$template->run ("other");
	$fname = replaceStr($fname,"label_","");
	$fname = replaceStr($fname,"$$","/");
	fwrite(fopen("../".$fname,"wb"),$template->html);
	echo " 生成完毕 <a target='_blank' href='"."../".$fname."'>&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
}

function del()
{
	$file = be("all","file");
	if(file_exists( $file)){
		unlink($file);
	}
	alertUrl ("模板删除完毕", getReferer() );
}

function save()
{
	$file = be("post","file");
	$filename = be("post","filename");
	$suffix = be("post","suffix");
	$filepath = be("post","path");
	$fcontent =  stripslashes(be("post","fcontent"));
	
	if(isN($filepath)){
		if(substring($file,11)!="../template" || count( explode("../",$file) ) > 2) {
			echo "<tr><td colspan=\"5\"> 非法的目录请求 </td></tr>";
			return;
		}
		if(!file_exists($file)){
			echo "<tr><td colspan=\"5\"> 非法的文件请求 </td></tr>";
			return;
		}
		fwrite(fopen($file.$suffix,"wb"),$fcontent);
	}
	else{
		if (substring($filepath,11)!="../template" || count( explode("../",$filepath) ) > 2) {
			echo "<tr><td colspan=\"5\"> 非法的目录请求 </td></tr>";
			return;
		}
		$extarr = array('.html','.htm','.js','.xml','.wml');
		if(!in_array($suffix,$extarr)){
			$suffix='.html';
		}
		fwrite(fopen($filepath."/".$filename.$suffix,"wb"),$fcontent);
	}
	echo "模板修改完毕";
}

function guide()
{
?>
<script type="text/javascript">
	$(document).ready(function(){
		
	});
</script>
<?php
}

function info()
{
	global $action;
	$backurl = getReferer();
	$file = be("get","file");
	$path = be("get","path");
	if (isN($backurl) || strpos($backurl,"index.php")>0){ $backurl="admin_templates.php";}
	 
	if (!isN($file)){
		$fname = substr($file,strrpos($file,"/")+1);
		if (substring($file,11)!="../template" || count( explode("../",$file) ) > 2) {
			echo "<tr><td colspan=\"5\"> 非法的目录请求 </td></tr>";
			return;
		}
		$fcontent = file_get_contents($file);
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#form1').form({
			onSubmit:function(){
				if(!$("#form1").valid()) {return false;}
			},
		    success:function(data){
		        $.messager.alert('系统提示', data, 'info',function(){
		        	location.href = $("#backurl").val();
		        });
		    }
		});
		$("#btnCancel").click(function(){
			location.href = $("#backurl").val();
		});
	});
</script>
<form id="form1" name="form1" action="?action=save" method="post">
<table class="tb">
	<input id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">
	<tr>
	<td width="10%">文件名称：</td>
	<td><input id="filename" name="filename" type="text" value="<?php echo $fname?>" size="60" 
	<?php
	if ($action=="edit"){
		echo "readonly>&nbsp;注意：编辑时文件名无法修改";
	}
	else{
		echo "><select name=\"suffix\"><option value=\".html\">.html</option><option value=\".htm\">.htm</option><option value=\".js\">.js</option><option value=\".xml\">.xml</option><option value=\".wml\">.wml</option></select>自定义页面以label_开头,如果需要生成到html目录用\$\$替换目录/杠，如：html\$\$hot.html";
		}
	?>
    </td>
	</tr>
	<tr>
	<td colspan="2">
	<textarea class="temp_t" id="fcontent" name="fcontent" style="width:90%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="30"><?php echo $fcontent?></textarea>
	</td>
	</tr>
	<tr>
	<td colspan="2" align="center">
	<input id="path" name="path" type="hidden" value="<?php echo $path?>">
	<input id="file" name="file" type="hidden" value="<?php echo $file?>">
	<input class="input" type="submit" value="保存" id="btnSave">
	<input class="input" type="button" value="返回" id="btnCancel">
	</td>
	</tr>
</table>
</form>
<?php
}

function templatesname($filename)
{
	switch($filename)
	{
		case "head.html":$str="头部模板";break;
		case "foot.html":$str="底部模板";break;
		case "index.html":$str="首页模板";break;
		case "artindex.html":$str="文章首页模版";break;
		case "art.html":$str="文章内容页";break;
		case "artlist.html":$str="文章列表页";break;
		case "arttopic.html":$str="文章专题首页";break;
		case "vod.html":$str="视频内容页";break;
		case "vodlist.html":$str="视频分类页";break;
		case "vodmap.html":$str="视频地图页";break;
		case "vodplay.html":$str="视频播放页";break;
		case "vodsearch.html":$str="视频搜索页";break;
		case "vodtopic.html":$str="视频专题首页";break;
		case "vodplayopen.html":$str="弹窗播放页面";break;
		case "gbook.html":$str="留言本";break;
		case "userlogin.html":$str="登陆框未登录模板";break;
		case "userlogged.html": $str="登陆框已登录模板";break;
		default: $str="自定义文件";break;
	}
	return $str;
}

function main()
{
	global $action;
	$filedir = be("get","filedir");
	$defaultPahth = "../template";
	if (isN($filedir)){ $filedir = $defaultPahth;}
	if (substring($filedir,11) != "../template"){ $filedir = "../template";}
	
?>
<table class="tb">
	<tr>
	<td><strong>文件名</strong></td>
	<td width="15%"><strong>模板类型</strong></td>
    <td width="15%"><strong>文件大小</strong></td>
    <td width="20%"><strong>修改时间</strong></td>
    <td width="15%"><strong>操作</strong></td>
	</tr>
	<?php
	if ($action=="label"){
		$filedir.= "/".app_templatedir."/".app_htmldir;
		echo "<tr><td colspan=\"5\"><a href=\"?action=add&path=". $filedir."\">(添加新页面)</a>  自定义页面可生成单独页面，也可以内嵌入系统模版中</td></tr>";
		
     	$fso=opendir($filedir);
		while ($file=readdir($fso)){
			$fullpath = "$filedir/$file";
			if(is_file($fullpath)){
				if (substring($file,6)== "label_"){
					$fsize = round(filesize("$filedir/$file")/1024,2);
					$ftime = date("Y-n-d H:i:s",filemtime("$filedir/$file"));
					$ftime = getColorDay($ftime);
	?>
	<tr>
	<td><img src="../images/icons/asp.gif"><?php echo $file?> &nbsp;&nbsp; 调用标签:{label:<?php echo replaceStr($file,"label_","")?>}</td>
	<td><?php echo templatesname($file)?></td>
	<td><?php echo $fsize?>KB</td>
	<td><?php echo $ftime?></td>
	<td><?php echo "<a href=\"?action=view&file=".$filedir."/".$file."\">预览</a>&nbsp;<a href=\"admin_makehtml.php?action=diypage&fname=".$file."\">生成</a>&nbsp;<a href=\"?action=edit&file=".$filedir."/".$file."\">编辑</a>&nbsp;<a href=\"?action=del&file=".$filedir."/".$file."\" onClick=\"return confirm('确定要删除吗?');\">删除</a>";
	?></td></tr>
	<?php
				}
			}
		}
		closedir($fso);
		unset($fso);
	}
    else{
    	$upperFolder = substring($filedir,strrpos($filedir,"/"));
		if (count( explode("../",$filedir) ) > 2) {
			echo "<tr><td colspan=\"5\"> 非法的目录请求 </td></tr>";
			return;
		}
		
		if ($upperFolder !=".."){
			echo "<tr><td colspan=\"5\"><img src=\"../images/icons/dir2.gif\"> <a href=\"?filedir=" .$upperFolder."\">上级目录</a> ";
			echo "<a href=\"?action=add&path=".$filedir."\">(添加新页面)</a></td></tr>";
		}
		
		$fso=opendir($filedir);
		while ($file=readdir($fso)){
			$fullpath = "$filedir/$file";
			if(is_dir($fullpath)){
				if($file!=".."&&$file!=".")	{
					echo "<tr><td colspan=\"5\"><img src=\"../images/icons/dir.gif\"> <a href=\"?filedir=".$filedir."/".$file."\">".$file."</a></td></tr>";
				}
			}
			else if(is_file($fullpath)){
				if (substring($file,6)!= "label_"){
					$fsize = round(filesize("$filedir/$file")/1024,2);
					$ftime = date("Y-n-d H:i:s",filemtime("$filedir/$file"));
					$ftime = getColorDay($ftime);
					$farr = explode(".",$file);
					$fext = $farr[count($farr)-1];
		?>
		<tr>
		<td><img src="../images/icons/asp.gif"><?php echo $file?> </td>
		<td><?php echo templatesname($file)?></td>
		<td><?php echo $fsize?>KB</td>
		<td><?php echo $ftime?></td>
		<td><?php 
		if($fext=="jpg" || $fext=="gif" || $fext=="bmp" || $fext=="png"){
			echo "<a target=_blank href=\"".$filedir."/".$file."\">预览</a>&nbsp;";
		}
		else{
			echo "<a href=\"?action=edit&file=".$filedir."/".$file."\">编辑</a>&nbsp;<a href=\"?action=del&file=".$filedir."/".$file."\" onClick=\"return confirm('确定要删除吗?');\">删除</a>";
		}
		?></td></tr>
		<?php
				}
			}
		}
		closedir($fso);
		unset($fso);
	}
?>
</table>
<?php
}
?>
</body>
</html>