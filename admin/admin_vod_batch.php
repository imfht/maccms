<?php
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");
chkLogin();

$action = be("all","action");
$stype = be("all", "stype");
$area = be("all", "area");   $topic = be("all", "topic");
$level = be("all", "level");     $from = be("all", "from");
$sserver = be("all", "sserver");  $sstate = be("all", "sstate");
$page = be("all", "page");
$spic = be("all", "spic");    $hide = be("all", "hide");
if(!isNum($level)) { $level = 0;} else { $level = intval($level);}
if(!isNum($sstate)) { $sstate = 0;} else { $sstate = intval($sstate);}
if(!isNum($stype)) { $stype = 0;} else { $stype = intval($stype);}
if(!isNum($area)) { $area = 0;} else { $area = intval($area);}
if(!isNum($topic)) { $topic = 0;} else { $topic = intval($topic);}
if(!isNum($spic)) { $spic = 0;} else { $spic = intval($spic);}
if(!isNum($hide)) { $hide=-1;} else { $hide = intval($hide);}
if (!isNum($page)){ $page = 1;} else { $page = intval($page);}
if ($page < 1) { $page = 1; }


$where = "where 1=1 ";
if ($stype > 0) { 
	$typearr = getValueByArray($cache[0], "t_id" ,$stype );
	if(is_array($typearr)){
		$where = $where . " and d_type in (" . $typearr["childids"] . ")";
	}
	else{
		$where .= " AND d_type=" . $stype . " ";
	}
}
if ($stype ==-1) { $where .= " AND d_type=0 ";}
if ($area > 0) { $where .= " AND d_area = " . $area . " ";}
if ($topic > 0) { $where .= " AND d_topic = " . $topic . " ";}
if ($level > 0) { $where .= " AND d_level = " . $level . " ";}
if ($sstate ==1){ 
	$where .= " AND d_state>0 ";
}
else if ($sstate==2){ 
	$where .= " AND d_state=0 ";
}

if($hide>-1){
	$where .= " AND d_hide=".$hide ." ";
}
if(!isN($sserver)) { $where .= " AND d_playserver like '%" . $sserver . "%' ";}
if(!isN($from)) { $where .= " and d_playfrom like  '%" . $from . "%' ";}
if($spic==1){
	$where .= " AND d_pic = '' ";
}
else if($spic==2){
	$where .= " AND d_pic like 'http://%' ";
}

switch($action)
{
	case "save" : save();break;
	case "del" : del();break;
	case "delplay" : delplay();break;
	default : headAdmin ("视频管理"); main();break;
}
dispseObj();


function save()
{
	global $db,$where;
    
    echo "保存完毕";
}

function del()
{
	global $db,$where;
    if($where=="where 1=1"){
    	$status = $db->query("truncate from {pre}vod ");
    }
    else{
    	$status = $db->query("delete from {pre}vod ".$where);
    }
    if($status){
    	showMsg ("删除数据成功!", "admin_vod_batch.php"); 
    }
    else{
    	showMsg ("删除数据失败!", "admin_vod_batch.php"); 
    }
}

function delplay()
{
	global $db,$where,$stype,$area,$topic,$level,$from,$sserver,$sstate,$page;
	$sql = "SELECT count(*) FROM {pre}vod ".$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	
    $sql = "SELECT d_id,d_name,d_playfrom,d_playurl,d_playserver FROM {pre}vod ".$where . " ORDER BY d_id desc limit ".(app_pagenum * ($pagecount-1)) .",".app_pagenum;
    
	$rs = $db->query($sql);
	if($nums==0){
		showMsg ("数据处理完毕!", "admin_vod_batch.php");
	}
	else{
		echo "<font color=red>共".$nums."条数据包含".$from."播放器,共".$pagecount."页正在开始删除第".$pagecount."页数据</font><br>";
		
		while ($row = $db ->fetch_array($rs))
		{
			$d_playfrom = $row["d_playfrom"];
			$d_playserver = $row["d_playserver"];
			$d_playurl = $row["d_playurl"];
			
			$fromarr = explode("$$$",$d_playfrom);
			$serverarr = explode("$$$",$d_playserver);
			$urlarr = explode("$$$",$d_playurl);
			
			$new_playfrom = "";
			$new_playserver = "";
			$new_playurl = "";
			$rc=false;
			for ($i=0;$i<count($fromarr);$i++){
				$sfrom = $fromarr[$i];
				$sserver = $serverarr[$i];
				$surl= $urlarr[$i];
				
				if($sfrom==$from){
					
				}
				else{
					if($rc){
						$new_playfrom .= "$$$";
						$new_playserver .= "$$$";
						$new_playurl .= "$$$";
					}
					$new_playfrom .= $sfrom;
					$new_playserver .= $sserver;
					$new_playurl .= replaceStr($surl,"'","''");
					$rc=true;
				}
			}
				$db->query("UPDATE {pre}vod set d_playfrom='".$new_playfrom."',d_playserver='".$new_playserver."',d_playurl='".$new_playurl."' where d_id='".$row["d_id"]."'");
			echo $row["d_name"] . "---ok<br>";
		}
		echo "<br><font color=red>暂停5秒后继续</font><br><script>setTimeout(\"updatenext();\",5000);function updatenext(){location.href='admin_vod_batch.php?action=delplay&page=".($page+1)."&stype=".$stype."&area=".$area."&topic=".$topic."&level=".$level."&from=".$from."&sserver=".$ssserver."&sstate=".$sstate."';}</script>";
	}
	unset($rs);
}


function main()
{
?>
<script language="javascript">
$(document).ready(function(){
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","admin_vod_batch.php?action=del");
			$("#form1").submit();
		}
		else{return false}
	});
	$("#btnDelPlay").click(function(){
		if($("#from").val()==""){
			alert("请选择要删除的播放器类型");
			return;
		}
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","admin_vod_batch.php?action=delplay");
			$("#form1").submit();
		}
		else{return false}
	});
});

</script>
<form id="form1" name="form1" method="post">
<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	过滤条件：<select id="stype" name="stype">
	<option value="0">视频栏目</option>
	<option value="-1">没有栏目</option>
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","")?>
	</select>
	&nbsp;
	<select id="state" name="state">
	<option value="0">视频连载</option>
	<option value="1">连载中</option>
	<option value="2">未连载</option>
	</select>
	&nbsp;
	<select id="level" name="level">
	<option value="0">视频推荐</option>
	<option value="1">推荐1</option>
	<option value="2">推荐2</option>
	<option value="3">推荐3</option>
	<option value="4">推荐4</option>
	<option value="5">推荐5</option>
	</select>
	&nbsp;
	<select id="topic" name="topic">
	<option value="0">视频专题</option>
	<?php echo makeSelect("{pre}vod_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;","")?>
	</select>
	&nbsp;
	<select id="sserver" name="sserver">
	<option value="">视频服务器</option>
	<?php echo makeSelectServer("")?>
	</select>
	&nbsp;
	<select id="from" name="from">
	<option value="">视频播放器</option>
	<?php echo makeSelectPlayer("")?>
	</select>
	&nbsp;
	<select id="spic" name="spic">
	<option value="0">视频图片</option>
	<option value="1">无图片</option>
	<option value="2">远程图片</option>
	</select>
	&nbsp;
	<select id="hide" name="hide">
	<option value="-1">视频显隐</option>
	<option value="0">显示</option>
	<option value="1">隐藏</option>
	</select>
	</td>
	</tr>
	<tr>
	<td colspan="6">
		<input class="input" type="button" value="批量删除数据" id="btnDel">
		<input class="input" type="button" value="批量删除播放组" id="btnDelPlay">
	</td>
	</tr>
	</table>
	</td>
	</tr>
</table>
</form>
<?php
}
?>
</body>
</html>