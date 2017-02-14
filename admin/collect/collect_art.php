<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("文章采集入库管理");

switch($action)
{
	case "edit" : edit();break;
	case "editsave" : editsave();break;
	case "del" : del();break;
	case "delpl" : delpl();break;
	case "delall" : delAll();break;
	case "delurl" : delurl();break;
	case "IDInflow" : IDInflow();break;
	case "AllInflow" : AllInflow();break;
	case "noInflow" : noInflow();break;
	case "editype" : editype();break;
	default : main();
}

function editype()
{
	global $db;
	$m_typeid = be("post","m_typeid");
	if (isN($m_typeid)) { alert ("请选择分类！");}
	$ids=be("arr","m_id");
	$db->query("update {pre}cj_art set m_typeid=".$m_typeid." where m_id in (" .$ids.")");
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}
function IDInflow()
{
	global $db;
	$ids=be("arr","m_id");
	if (!isN($ids)){
		$count = $db->getOne("Select count(m_id) as cc from {pre}cj_art where m_id in (".$ids.") and m_typeid>0");
		$sql="select * from {pre}cj_art where m_id in (".$ids.") and m_typeid>0";
		MovieInflow($sql,$count);
	}
	else{
		showmsg ("请选择入库数据！",$backurl);
	}
}

function AllInflow()
{
	global $db;
    $count = $db->getOne("Select count(m_id) as cc from {pre}cj_art where m_typeid>0");
	$sql="select * from {pre}cj_art where m_typeid>0";
	MovieInflow($sql,$count);
}

function noInflow()
{
	global $db;
    $count = $db->getOne("Select count(m_id) as cc from {pre}cj_art where m_zt=0 and m_typeid>0");
	$sql="select * from {pre}cj_art where m_zt=0 and m_typeid>0";
	MovieInflow($sql,$count);
}

function del()
{
	global $db;
	$m_id=be("get","m_id");
	$db->query("delete from {pre}cj_art WHERE m_id =".$m_id);
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function delpl()
{
	global $db;
	$ids=be("arr","m_id");
	if (!isN($ids)){
		$db->query("delete from {pre}cj_art WHERE m_id in(".$ids.")");
		echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
	 }
	 else{
	 	alert ("请选择相应数据！");
	 }
}

function delall()
{
	global $db;
	$db->query("delete from {pre}cj_art");
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function editsave()
{
	global $db;
	
	$m_id = be("post","m_id") ; $m_title = be("post","m_title");
	$m_typeid = be("post","m_typeid"); $m_author = be("post","m_author");
	$m_content = be("post","m_content"); $m_addtime = be("post","m_addtime") ;
	$m_hits = be("post","m_hits") ; $m_zt = be("post","m_zt");
	$backurl = be("post","backurl");
	
	if (isN($backurl)) { $backurl = "collect_art.php";}
	if (isN($m_typeid)) { errmsg ("采集系统提示","分类不能为空,请选择配置!");}
	if (!isNum($m_hits)) { $m_hits = 0;}
	if (!isNum($m_zt)) { $m_zt = 0 ; }
	
	$sql="update {pre}cj_art set m_title='".$m_title."',m_type='".$m_type."',m_typeid='".$m_typeid."',m_author='".$m_author."',m_content='".$m_content."',m_addtime='".$m_addtime."',m_hits='".$m_hits."',m_zt='".$m_zt."',m_addtime='".date('Y-m-d H:i:s',time())."' where m_id=". $m_id;
	$db->query($sql);
	showmsg ("修改数据成功!",$backurl);
}

function edit()
{
	global $db;
	
	$m_id = be("get","m_id");
	$sql="select * from {pre}cj_art where m_id=". $m_id;
	$row = $db->getRow($sql);
	$m_title=$row["m_title"];
	$m_type=$row["m_type"];
	$m_typeid=$row["m_typeid"];
	$m_author=$row["m_author"];
	$m_content=$row["m_content"];
	$m_addtime=$row["m_addtime"];
	$m_hits = $row["m_hits"];
	$m_zt = $row["m_zt"];
	$backurl =  $_SERVER["HTTP_REFERER"];
?>
<script>
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			m_title:{
				required:true,
				maxlength:255
			},
			m_author:{
				required:true,
				maxlength:255
			}
			m_hits:{
				number:true
			},
			m_typeid:{
				required:true
			}
		}
	});
});
</script>
<form name="form" action="?action=editsave" method="post">
<input id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">
<table class=tb >
	<tr>
		<td width="20%" >文章标题：</td>
		<td><input id="m_title" name="m_title" type="text" value="<?php echo $m_title?>" size="40"> </td>
    </tr>
    <tr>
		<td>文章作者：</td>
		<td><input id="m_author" name="m_author" type="text" value="<?php echo $m_author?>" size="40"> </td>
    </tr>
    <tr>
		<td>发布日期：</td>
		<td><input id="m_addtime" name="m_addtime" type="text" value="<?php echo $m_addtime?>" size="40"> </td>
    </tr>
    <tr>
		<td>文章人气：</td>
		<td><input id="m_hits" name="m_hits" type="text" value="<?php echo $m_hits?>" size="40"> </td>
    </tr>
    <tr>
		<td>入库状态：</td>
		<td>
		<input type="radio" name="m_zt" value="0" <?php if ($m_zt==0) { echo "checked";}?>> 未入库
		<input type="radio" name="m_zt" value="1" <?php if ($m_zt==1) { echo "checked";}?>> 已入库
	  </td>
    </tr>
    <tr>
		<td>分类：</td>
		<td>
		<select name="m_typeid" id="m_typeid"  >
        <option value="">请选择数据分类</option>
            <?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$m_typeid)?>
        </select>
		</td>
    </tr>
    <tr>
		<td>文章内容：</td>
		<td>
		<textarea id="m_content" name="m_content" style="width:750px;height:150px;"><?php echo $m_content?></textarea>
		</td>
    </tr>
 	<tr>
		<td  colspan="2" >
		<input type="hidden" id="m_id" name="m_id" value="<?php echo $m_id?>">
		<input type="submit" class="btn" name="Submit" value="修 改">
		<input  type="button" class="btn" name="button" value="返 回" onClick="window.location.href='javascript:history.go(-1)'">	
		</td>
    </tr>
</table>
</form>
<?php
}

function main()
{
	global $db,$cache;
	$pagenum = be("get","page");
	if (!isNum($pagenum)){ $pagenum = 1; }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$pagenum = intval($pagenum);
	
	$keyword = be("get","keyword");
	$project = be("get","{pre}cj_art_projects");
	$zt = be("get","zt");
	$sql="Select a.*,b.p_name as p_name from {pre}cj_art a,{pre}cj_art_projects b where a.m_pid=b.p_id";
	
	if ($zt != "") {
		$sql = $sql . " and m_zt = " . $zt;
	}
	if ($keyword != "") {
		$sql = $sql . " and m_title like '%" . $keyword . "%' ";
	}
	if ($project!= "") {
		$sql = $sql . " and a.m_pid = " . $project;
	}
	$sql = $sql . " order by m_zt asc,m_addtime desc " ;
	
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount=ceil($nums/app_pagenum);//总页数
	$sql = $sql ." limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","?action=delpl");
			$("#form1").submit();
		}
	});
	$("#btnDelall").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","?action=delall");
			$("#form1").submit();
		}
	});
	$("#btnSelin").click(function(){
		if(confirm('确定入库您所选择的数据吗')){
			$("#form1").attr("action","?action=IDInflow");
			$("#form1").submit();
		}
	});
	$("#btnAllin").click(function(){
		if(confirm('全部入库你所采集的数据吗')){
			$("#form1").attr("action","?action=AllInflow");
			$("#form1").submit();
		}
	});
	$("#btnNoin").click(function(){
		if(confirm('确定入库所有未入库的数据吗')){
			$("#form1").attr("action","?action=noInflow");
			$("#form1").submit();
		}
	});
	 $("#btnType").click(function(){
		if(confirm('确定入库所有未入库的数据吗')){
			$("#form1").attr("action","?action=editype");
			$("#form1").submit();
		}
	});
});
</script>
<TABLE width="96%" border=0 align=center cellpadding=0 cellSpacing=0 class=tbtitle >
  <TBODY>
    <tr>
      <td>
		<form action="collect_art.php" method="get"> 
          <strong>搜索文章：</strong>
          <input id=KeyWord size=40 name=keyword>
		  <INPUT class=inputbut type=submit value=搜索 name=Submit>
        &nbsp; 按项目查看：
        <select onchange=javascript:window.location.href=this.options[this.selectedIndex].value>
        <option value="collect_art.php">全部采集项目</option>
		<?php echo makeSelect("{pre}cj_art_projects","p_id","p_name","","collect_art.php","&nbsp;|&nbsp;&nbsp;","")?>
		  </select>	
          <font color="#FF0000">(没有找到对应栏目,不能入库)</font>
		</form>
        
        </td>
    </tr>
  </TBODY>
</table>

<form action="" method="post" name="form1" id="form1">
<table  class=tb >
	<tr>
	<td width="4%">&nbsp;</td>
	<td>文章名称</td>
	<td width="7%">状态</td>
	<td width="15%">栏目分类</td>
	<td width="15%">采集项目名称</td> 
	<td width="13%">添加时间</td>
	<td width="8%">操作</td>
	</tr>
	<?php
	if (!$rs){
	?>
    <tr><td align="center" colspan="9" >没有任何记录!</td></tr>
    <?php
	}
	else{
		$i=0;
	  	while ($row = $db ->fetch_array($rs))
	  	{
	?>
	<tr>
		<td><input name="m_id[]" type="checkbox" id="m_id" value="<?php echo $row["m_id"]?>" /></td>
		<td><?php echo $row["m_title"]?> </td>
		<td><?php if ($row["m_zt"]==1) { echo "<font color=\"#FF0000\">已入库</font>";} Else  { echo "未入库" ;}?></td>
		<td>
	<?php
		if ($row["m_typeid"]==0){
	?>
		<font color="#FF0000">没找到对应分类请配置</font>
		<?php
      }
	  	else{
			$typearr = getValueByArray($cache[1], "t_id" , $row["m_typeid"] );
			echo $typearr["t_name"];
		}
	  ?>
      </td>
      <td><?php echo $row["p_name"]?></td>
      <td><?php echo getColorDay( $row['m_addtime'] ) ?></td>
      <td><A href="?action=edit&m_id=<?php echo $row["m_id"]?>">修改</A>｜<A href="?action=del&m_id=<?php echo $row["m_id"]?>">删除</A></td></tr>
	<?php
		}
	}
	?>
	<tr>
	<td  colspan="9">
    全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'m_id[]');"/>&nbsp;
    &nbsp;<input type="button" id="btnDel" value="批量删除" class="btn"  />
	&nbsp;<input type="button" id="btnDelall" value="删除所有" class="btn"  />
	&nbsp;<input type="button" id="btnSelin" class="btn" name="Submit" value="入库所选" >
	&nbsp;<input type="button" id="btnAllin" class="btn" name="Submit" value="全部入库" >
    &nbsp;<input type="button" id="btnNoin" class="btn" name="Submit" value="入库未入库" >
        <select name="m_typeid" id="m_typeid"  >
        <option value="">请选择数据分类</option>
            <?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","")?>
        </select>
        <input type="button" id="btnType" class="btn" name="Submit" value="批量分类"><font color="#FF0000">&nbsp; </font></td>
</tr>
 <tr>
    <td colspan="9" >同名处理：  
	<input type="checkbox" name="CCTV1" value="1">覆盖作者:
	<input type="checkbox" name="CCTV2" value="2">覆盖内容:
     </td>
</tr>
	<tr align="center" bgcolor="#f8fbfb">
	<td colspan="8">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_art.php?page={p}&{pre}cj_art_projects=".$project."&keyword=".$keyword) ?>
	</td>
    </tr>
</table>
</form>
<?php
}

function MovieInflow($sql_collect,$MovieNumW)
{
	global $db;
?>
<table class=tb>
	<tr>
		<td  colspan="2" align="center"> 入 库 状 态</td>
	</tr>
	<tr>
		<td  colspan="2" align="center"> 
		<div class="xingmu" id="refreshlentext" style="background:#006600"></div>
		</td>
  	</tr>
  	<tr>
		<td  colspan="2" align="center"><span id="storagetext">正 在 入 库...</span></td>
  	</tr>
</table>
<?php
	$iscover= be("get","iscover");
	$rs = $db->query($sql_collect);
	$rscount = $db -> num_rows($rs);
	
	if($rscount==0){
		echo "<script>alert('没有可入库的数据!'); location.href='collect_art.php';</script>";
		exit;
	}
	
	if ($rscount > 10000){
		$rscount = 1000;
	}
	elseif ($rscount > 5000) {
		$rscount = 500;
	}
	elseif ($rscount > 1000){
		$rscount = 100;
	}
	else{
		$rscount = 10;
	}
	
	while ($row = $db ->fetch_array($rs))
	{
		$flag=false;
		$title = $row["m_title"];
		$title = replaceStr($title,"'"," ''");
		
		$sql = "SELECT * FROM {pre}art WHERE a_title = '".$title."'";
	    $rowart = $db->getRow($sql);
	    $strSet="";
	    
	    //插入新数据开始
		if (!$rowart){
			$flag=true;
 			
			if (isN($row["m_addtime"])) { 
				$a_addtime = date('Y-m-d H:i:s',time());
			}
			else{
				$a_addtime = $row["m_addtime"];
			}
			$a_hits= $row["m_hits"];
			$a_content=$row["m_content"];
 			$a_author = $row["m_author"];
 			$a_title = $row["m_title"];
			$a_entitle = hanzi2pinyin($a_title);
			$a_letter = strtoupper(substring($a_entitle,1));
			if ($row["m_typeid"] > 0) {
				$a_type = $row["m_typeid"];
			}
			else{
				if (!isN($row["m_type"])){
					$sql = "select * from {pre}art_type where t_name like '%" . $row["m_type"]."%' ";
					$rowtype = $db->getRow($sql);
					if ($rowtype) { $a_type = $rowtype["t_id"];}
					unset($rowtype);
				}
			}
			
			$sql="insert {pre}art (a_type,a_addtime,a_time,a_content,a_hits,a_title,a_entitle,a_letter,a_author) values('".$a_type."','".$a_addtime."','".$a_addtime."','".$a_content."','".$a_hits."','".$a_title."','".$a_entitle."','".$a_letter."','".$a_author."') ";
			$status = $db->query($sql);
			$aid = $db->insert_id();
		}
		//插入新数据结束
		else{//更新数据开始
			if (be("post","CCTV1")=="1") {
				$a_author=$row["m_author"];
				$strSet .="a_author='".$a_author."',";
			}
			if (be("post","CCTV2")=="2"){
				$a_content=$row["m_content"];
				$strSet .="a_content='".$a_content."',";
			}
			$a_title= replaceStr($row["m_title"],"'","");
			$strSet .="a_title='".$a_title."',";
			$a_addtime= date('Y-m-d H:i:s',time());
			$strSet .="a_time='".$a_addtime."',";
		}
		//更新数据
		if ($row["m_typeid"] > 0) {
			$a_type = $row["m_typeid"];
		}
		else{
			if (!isN($row["m_type"])){
				$sql = "select * from {pre}art_type where t_name like '%" . $row["m_type"]."%' ";
				$rowtype = $db->getRow($sql);
				if ($rowtype) { $a_type = $rowtype["t_id"];}
				unset($rowtype);
			}
		}
		
		$strSet .=" a_type='".$a_type."' ";
		if ($flag == false){
			$aid= $rowart["a_id"];
		}
		$sql= "update {pre}art set ".$strSet." where a_id=" .$aid;
		$db->query($sql);
		
		$db->query("update {pre}cj_art set m_zt=1 where m_id=".$row["m_id"]);
		
		$MovieInflowNum=$MovieInflowNum+1;
		if ($MovieInflowNum >= $MovieNumW){
			echo "<script type=\"text/javascript\" language=\"javascript\">";
			echo "document.getElementById(\"refreshlentext\").style.width = \"100%\";";
			echo "document.getElementById(\"refreshlentext\").innerHTML = \"100%\";";
			echo "document.getElementById(\"storagetext\").innerHTML = \"入库完毕 <a href='collect_art.php'>返回</a>\";";
			echo "alert('入库完毕'); location.href='collect_art.php';";
			echo "</script>";
		}
		elseif (  fmod($MovieInflowNum,$rscount) == 0) {
		    echo "<script type=\"text/javascript\" language=\"javascript\">";
			echo "document.getElementById(\"refreshlentext\").style.width = \"".($MovieInflowNum/$MovieNumW*100)."%\";";
			echo "document.getElementById(\"refreshlentext\").innerHTML = \"".($MovieInflowNum/$MovieNumW*100)."%\";";
			echo "document.getElementById(\"storagetext\").innerHTML = \"正在入库......\";";
			echo "</script>";
		}
    }
    unset($rs);
}
?>