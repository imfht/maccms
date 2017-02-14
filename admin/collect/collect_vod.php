<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("视频采集入库管理");

switch($action)
{
	case "edit" : edit();break;
	case "editsave" : editsave();break;
	case "del" : del();break;
	case "delpl" : delpl();break;
	case "delall" : delall();break;
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
	if (isN($m_typeid)) {  alert("请选择分类！");}
	$ids = be("arr","m_id");
	$db->query("update {pre}cj_vod set m_typeid=".$m_typeid." where m_id in (" .$ids.")");
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function IDInflow()
{
	global $db;
	$ids = be("arr","m_id");
	if (!isN($ids)){
		$count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_id in (".$ids.") and m_typeid>0");
		$sql="select * from {pre}cj_vod where m_id in (".$ids.") and m_typeid>0";
		MovieInflow($sql,$count);
	}
	else{
		showmsg ("请选择入库数据！",$backurl);
	}
}

function AllInflow()
{
	global $db;
    $count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_typeid>0");
	$sql="select * from {pre}cj_vod where m_typeid>0";
	MovieInflow($sql,$count);
}

function noInflow()
{
	global $db;
    $count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_zt=0 and m_typeid>0");
	$sql="select * from {pre}cj_vod where m_zt=0 and m_typeid>0";
	MovieInflow($sql,$count);
}

function del()
{
	global $db;
	$m_id=be("get","m_id");
	$db->query("delete from {pre}cj_vod_url WHERE u_movieid = ".$m_id);
	$db->query("delete from {pre}cj_vod WHERE m_id =".$m_id);
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function delpl()
{
	global $db;
	$ids = be("arr","m_id");
	if (!isN($ids)){
		$db->query("delete from {pre}cj_vod_url WHERE u_movieid in( ".$ids.")");
		$db->query("delete from {pre}cj_vod WHERE m_id in(".$ids.")");
		echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
	 }
	 else{
	 	alert ("请选择相应数据！");
	 }
}

function delall()
{
	global $db;
	$db->query("delete from {pre}cj_vod_url");
	$db->query("delete from {pre}cj_Vod");
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function delurl()
{
	global $db;
	$u_id=be("get","u_id");
	$db->query("delete from {pre}cj_vod_url WHERE u_id=".$u_id);
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function editsave()
{
	global $db;
	$m_id = be("post","m_id") ; $m_name = be("post","m_name");
	$m_typeid = be("post","m_typeid") ; $m_area = be("post","m_area");
	$m_playfrom = be("post","m_playfrom") ; $m_starring = be("post","m_starring");
	$m_pic = be("post","m_pic") ; $m_content = be("post","m_content");
	$m_zt = be("post","m_zt") ; $m_language = be("post","m_language");
	$m_year = be("post","m_year") ; $m_playserver = be("post","m_playserver");
	$m_hits = be("post","m_hits") ; $m_state = be("post","m_state");
	$m_directed = be("post","m_directed"); $m_remarks = be("post","m_remarks");
	$backurl = be("post","backurl"); 
	
	if (isN($backurl)) { $backurl = "collect_vod.php";}
	if (!isNum($m_typeid)) { alert ( "分类不能为空!");}
	if (!isNum($m_hits)) { $m_hits = 0;}
	if (!isNum($m_playserver)) { $m_playserver=0;}
	if (!isNum($m_zt)) { $m_zt = 0 ; }
	if (!isNum($m_state)) { $m_state = 0 ; }
	
	$sql="update {pre}cj_vod set m_name='".$m_name."',m_type='".$m_type."',m_typeid='".$m_typeid."',m_area='".$m_area."',m_language='".$m_language."',m_playfrom='".$m_playfrom."',m_starring='".$m_starring."',m_directed='".$m_directed."',m_pic='".$m_pic."',m_content='".$m_content."',m_year='".$m_year."',m_zt='".$m_zt."',m_playserver='".$m_playserver."',m_hits='".$m_hits."',m_state='".$m_state."',m_remarks='".$m_remarks."',m_addtime='".date('Y-m-d H:i:s',time())."' where m_id=". $m_id;
	$db->query($sql);
	
	$sql="select * from {pre}cj_vod_url where u_movieid=".$m_id ." order by u_id  asc" ;
	$rs=$db->query($sql);
	$i=0;
	while ($row = $db ->fetch_array($rs))
	{
		$i=$i+1;
		$sql = "update {pre}cj_vod_url set u_url='".be("post","url".$i)."' where u_id=".$row["u_id"];
		$db->query( $sql );
	}
	showmsg ("修改数据成功!",$backurl);
}

function edit()
{
	global $db;
	$m_id = be("get","m_id");
	$sql="select * from {pre}cj_vod where m_id=". $m_id;
	$row = $db->getRow($sql);
	
	$m_name=$row["m_name"];
	$m_type=$row["m_type"];
	$m_typeid=$row["m_typeid"];
	$m_area=$row["m_area"];
	$m_playfrom=$row["m_playfrom"];
	$m_starring=$row["m_starring"];
	$m_directed=$row["m_directed"];
	$m_pic=$row["m_pic"];
	$m_content=$row["m_content"];
	$m_year=$row["m_year"];
	$m_urltest=$row["m_urltest"];
	$m_zt=$row["m_zt"];
	$m_playserver = $row["m_playserver"];
	$m_hits = $row["m_hits"];
	$m_state = $row["m_state"];
 	$m_remarks = $row["m_remarks"];
 	$m_language = $row["m_language"];
	$backurl =  $_SERVER["HTTP_REFERER"];
?>
<script>
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			m_name:{
				required:true,
				maxlength:255
			},
			m_typeid:{
				required:true
			},
			m_playfrom:{
				required:true
			},
			m_state:{
				number:true
			},
			m_remarks:{
				maxlength:255
			},
			m_starring:{
				maxlength:255
			},
			m_directed:{
				maxlength:255
			},
			m_year:{
				maxlength:32
			},
			m_hits:{
				number:true
			},
			m_area:{
				maxlength:32
			},
			m_language:{
				maxlength:32
			},
			m_typeid:{
				required:true
			}
		}
	});
});
</script>
<form id="form1" name="form1" action="?action=editsave" method="post">
	<input type="hidden" id="m_id" name="m_id" value="<?php echo $m_id?>">
	<input id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">
	<table class=tb>
	<tr>
		<td width="70" >名称：</td>
		<td><input id="m_name" name="m_name" type="text" value="<?php echo $m_name?>" size="40">
		&nbsp;分类：<select name="m_typeid" id="m_typeid"  >
        <option value="">请选择数据分类</option>
            <?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$m_typeid)?>
        </select>
        &nbsp;播放类型：<select name="m_playfrom">
		<option value=''>暂没有数据</option>
		<?php echo makeSelectPlayer($m_playfrom)?>
		</select> 
		&nbsp;服务器组：
		<select name="m_playserver">
		<option value=''>暂没有数据</option>
		<?php echo makeSelectServer($m_playserver)?>
		</select>
		</td>
    </tr>
    <tr>
	<td>连载：</td>
	<td><input id="m_state" name="m_state" type="text" value="<?php echo $m_state?>" size="50">
	&nbsp;备注：<input id="m_remarks" name="m_remarks" type="text" value="<?php echo $m_remarks?>" size="50">
	</td>
    </tr>
    <tr>
	<td>演员：</td>
	<td><input id="m_starring" name="m_starring" type="text" value="<?php echo $m_starring?>" size="50">
	&nbsp;导演：<input id="m_directed" name="m_directed" type="text" value="<?php echo $m_directed?>" size="50">
	</td>
    </tr>
    <tr>
	<td>上映：</td>
	<td><input id="m_year" name="m_year" type="text" value="<?php echo $m_year?>" size="50">
	&nbsp;人气：<input id="m_hits" name="m_hits" type="text" value="<?php echo $m_hits?>" size="50"> 
	</td>
    </tr>
    <tr>
	<td>地区：</td>
	<td>
	<input id="m_area" name="m_area" type="text" value="<?php echo $m_area?>" size="50">
	&nbsp;语言：<input id="m_language" name="m_language" type="text" value="<?php echo $m_language?>" size="50">
	</td>
	</tr>
    <tr>
	<td>图片：</td>
	<td><input id="m_pic" name="m_pic" type="text" value="<?php echo $m_pic?>" size="113"> </td>
    </tr>
	<tr>
	<td>入库状态：</td>
	<td>
	<input type="radio" name="m_zt" value="0" <?php if ($m_zt==0) { echo "checked";} ?>> 未入库
	<input type="radio" name="m_zt" value="1" <?php if ($m_zt==1) { echo "checked";} ?>> 已入库
	</td>
	</tr>
    <tr>
	<td>播放地址：</td>
	<td>
	<?php
        $sql="Select * from {pre}cj_vod_url where u_movieid=".$m_id ." order by u_id  asc" ;
        $i=0;
        $rs= $db->query($sql);
        while ($row = $db ->fetch_array($rs))
        {
        $i=$i+1;
        echo "<input type=\"text\" name=\"url".$i."\" size=80 value=\"".$row["u_url"]."\">&nbsp;第".$i."集&nbsp;&nbsp;&nbsp;<a href=\"?action=delurl&u_id=".$row["u_id"]."\"><font color=\"#FF0000\">删除</font></a><br>\r\n";
		}
        ?>
        </td>
    </tr>
    <tr>
		<td>介绍：</td>
		<td>
		<textarea id="m_content" name="m_content" style="width:750px;height:150px;"><?php echo $m_content?></textarea>
		</td>
		</tr>
 	<tr>
		<td  colspan="2" >
		<input type="submit" class="btn" name="submit1" value="修 改">
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
	if (isN($pagenum) || !isNum($pagenum)){ $pagenum = 1; }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$pagenum = intval($pagenum);
	
	$keyword = be("get","keyword");
	$project = be("get","{pre}cj_vod_projects");
	$zt = be("get","zt");
	
	$sql="Select a.*,b.p_name as p_name from {pre}cj_vod a,{pre}cj_vod_projects b where a.m_pid=b.p_id";
	if ($zt != "") {
		$sql = $sql . " and m_zt = " . $zt;
	}
	if ($keyword != "") {
		$sql = $sql . " and m_name like '%" . $keyword . "%' ";
	}
	if ($project!= "") {
		$sql = $sql . " and a.m_pid = " . $project;
	}
	$sql = $sql . " order by m_zt asc,m_addtime desc " ;
	
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount = ceil($nums/app_pagenum);//总页数
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
		if(confirm('确定更新所选数据的分类吗')){
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
		<form action="collect_vod.php" method="get"> 
			<strong>搜索影片：</strong>
			<input id=KeyWord size=40 name=keyword>
			<INPUT class=inputbut type=submit value=搜索 name=submit>
			&nbsp; 按项目查看：
			<select onchange=javascript:window.location.href=this.options[this.selectedIndex].value>
			<option value="collect_vod.php">全部采集项目</option>
			<?php echo makeSelect("{pre}cj_vod_projects","p_id","p_name","","collect_vod.php","&nbsp;|&nbsp;&nbsp;",$project)?>
			</select>	
			<font color="#FF0000">(没有找到对应栏目,不能入库)</font>
		</form>
		</td>
    </tr>
  </TBODY>
</TABLE>

<form action="" method="post" name="form1" id="form1">
<table class=tb >
	<tr>
	  <td width="4%" >&nbsp;</td>
      <td>影片名称</td>
      <td width="7%" >状态</td>
      <td width="7%">播放器</td>
      <td width="10%">栏目分类</td>
      <td width="10%">地区</td>
      <td width="15%">所属采集项目</td> 
      <td width="13%">更新时间</td>
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
	<td><?php echo $row["m_name"]?>  (连载:<?php echo $row["m_state"]?>)</td>
	<td><?php if ($row["m_zt"]==1) { echo "<font color=\"#FF0000\">已入库</font>";} Else  { echo "未入库" ;}?></td>
	<td><?php echo $row["m_playfrom"]?></td>
	<td>
	<?php
	if ($row["m_typeid"]==0){
	?>
		<font color="#FF0000">没找到对应分类请配置</font>
	<?php
	}
	else{
		$typearr = getValueByArray($cache[0], "t_id" , $row["m_typeid"] );
		echo $typearr["t_name"];
	}
	?>
	</td>
	<td><?php echo $row["m_area"]?> </td>
	<td><?php echo $row["p_name"]?></td>
	<td>
	<?php echo getColorDay( $row['m_addtime'] ) ?>
      </td>
      <td><A href="?action=edit&m_id=<?php echo $row["m_id"]?>">修改</A>｜<A href="?action=del&m_id=<?php echo $row["m_id"]?>">删除</A></td>
    </tr>
	<?php
		}
	}
	?>
	<tr>
	<td colspan="9">
    全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'m_id[]');"/>&nbsp;
    &nbsp;<input type="button" id="btnDel" value="批量删除" class="btn"  />
	&nbsp;<input type="button" id="btnDelall" value="删除所有" class="btn"  />
	&nbsp;<input type="button" id="btnSelin" class="btn" name="Submit" value="入库所选" >
	&nbsp;<input type="button" id="btnAllin" class="btn" name="Submit" value="全部入库" >
    &nbsp;<input type="button" id="btnNoin" class="btn" name="Submit" value="入库未入库" >
	&nbsp; <select name="m_typeid" id="m_typeid">
        <option value="">请选择数据分类</option>
        <?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","")?>
        </select>
        <input type="button" id="btnType" class="btn" name="Submit" value="批量分类"><font color="#FF0000">&nbsp; </font>
	</td>
	</tr>
	<tr>
    <td colspan="9" >入库同名处理:
    <input type="radio" name="CCTV" value="0" checked>自动处理
    <input type="radio" name="CCTV" value="1">始终新增数据
    <input type="radio" name="CCTV" value="2">新增播放器组
    <br />
    强制覆盖数据:
	<input type="checkbox" name="CCTV1" value="1">年份
	<input type="checkbox" name="CCTV2" value="2">地区
	<input type="checkbox" name="CCTV3" value="3">演员
	<input type="checkbox" name="CCTV4" value="4">图片
	<input type="checkbox" name="CCTV5" value="5">简介
	<input type="checkbox" name="CCTV6" value="6">语言
	<input type="checkbox" name="CCTV7" value="7">备注
	<input type="checkbox" name="CCTV8" value="8">导演
    <br />
    <font color="#FF0000">注意 ：自动判断播放来源，如遇到相同来源则更新数据。</font>
	</td>
	</tr>
    <tr align="center" bgcolor="#f8fbfb">
	<td colspan="9">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_vod.php?page={p}&{pre}cj_vod_projects=".$project."&keyword=".$keyword) ?></td>
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
		<td  colspan="2" align="center"> 入 库 状 态 </td>
		<div id="refreshlentext" style="background:#006600"></div>
		</td>
	</tr>
  	<tr>
		<td  colspan="2" align="center"><span id="storagetext">正 在 入 库...</span></td>
  	</tr>
</table>
<?php
	$iscover= be("iscover","get");
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
		$title = $row["m_name"];
		$title = replaceStr($title,"'","''");
		$strSet="";
		$sql = "SELECT * FROM {pre}vod WHERE d_name = '".$title."'";
	    $rowvod = $db->getRow($sql);
	    //插入新数据开始
		if ( isN($rowvod["d_id"]) || be("post","CCTV")=="1") {
			$flag=true;
			$d_pic= replaceStr($row["m_pic"],"'","''");
			$d_addtime= date('Y-m-d H:i:s',time());
			$d_year=$row["m_year"];
			$d_content=$row["m_content"];
			$d_hits= $row["m_hits"];
			$d_area = $row["m_area"];
			$d_language = $row["m_language"];
			$d_remarks = $row["m_remarks"];
			$d_state = $row["m_state"];
			$d_starring = $row["m_starring"];
			$d_directed = $row["m_directed"];
			$d_name = $row["m_name"];
			$d_enname = hanzi2pinyin($d_name);
			$d_letter = strtoupper(substring($d_enname,1));
			if ($row["m_typeid"] > 0) {
				$d_type = $row["m_typeid"];
			}
			else{
				if (!isN($row["m_type"])){
					$sql = "select * from {pre}vod_type where t_name like '%" . $row["m_type"]."%' ";
					$rowtype = $db->getRow($sql);
					if ($rowtype) { $d_type = $rowtype["t_id"];}
					unset($rowtype);
				}
			}
			
			$sql="insert {pre}vod (d_type,d_pic,d_addtime,d_time,d_year,d_content,d_hits,d_area,d_language,d_name,d_enname,d_letter,d_starring,d_directed,d_state,d_remarks) values('".$d_type."','".$d_pic."','".$d_addtime."','".$d_addtime."','".$d_year."','".$d_content."','".$d_hits."','".$d_area."','".$d_language."','".$d_name."','".$d_enname."','".$d_letter."','".$d_starring."','".$d_directed."','".$d_state."','".$d_remarks."') ";
			
			$db->query($sql);
			$did = $db->insert_id();
		}
		//插入新数据结束
		else{
		//更新数据开始
			if ($row["m_typeid"] > 0) {
				$d_type = $row["m_typeid"];
			}
			else{
				if (!isN($row["m_type"])){
					$sql = "select * from {pre}vod_type where t_name like '%" . $row["m_type"]."%' ";
					$rowtype = $db->getRow($sql);
					if ($rowtype) { $d_type = $rowtype["t_id"];}
					unset($rowtype);
				}
			}
			$strSet .=" d_type='".$d_type."', ";
			
			if (be("post","CCTV2")=="2") {
				$d_area = $row["m_area"];
				$strSet .="d_area='".$d_area."',";
			}
			if (be("post","CCTV6")=="6") {
				$d_language = $row["m_language"];
				$strSet .= "d_language='".$d_language."',";
			}
			if (be("post","CCTV7")=="7") { 
				$d_remarks = $row["m_remarks"];
				$strSet .="d_remarks='".$d_remarks."',";
			}
			if (be("post","CCTV8")=="8") { 
				$d_directed = $row["m_directed"];
				$strSet .="d_directed='".$d_directed."',";
			}
			if (be("post","CCTV1")=="1") { 
				$d_year=$row["m_year"];
				$strSet .="d_year='".$d_year."',";
			}
			if (be("post","CCTV3")=="3") {
				$d_starring=$row["m_starring"];
				$strSet .="d_starring='".$d_starring."',";
			}
			if (be("post","CCTV4")=="4") {
				 $d_pic = $row["m_pic"];
				 $strSet .="d_pic='".$d_pic."',";
			}
			if (be("post","CCTV5")=="5") {
				$d_content = $row["m_content"];
				$strSet .="d_content='".$d_content."',";
			}
			$d_state = $row["m_state"];
			$strSet .="d_state='".$d_state."',";
			$strSet .="d_name='".$title."',";
			$d_enname = hanzi2pinyin($title);
			$strSet .="d_enname='".$d_enname."',";
			$d_letter = strtoupper(substring($d_enname,1));
			$strSet .="d_letter='".$d_letter."',";
			$d_addtime= date('Y-m-d H:i:s',time());
			$strSet .="d_time='".$d_addtime."',";
		}
		//更新数据结束
		
		if ($flag == false){
			$did= $rowvod["d_id"];
		}
		
		//获取影片URL
		$urls = getVodUrl($row["m_id"]);
		$tmpplayurl = $rowvod["d_playurl"];
		$tmpplayfrom = $rowvod["d_playfrom"];
		$tmpplayserver = $rowvod["d_playserver"];
		
		if (isN($tmpplayurl)) { $tmpplayurl="";}
		if (isN($tmpplayfrom)) { $tmpplayfrom="";}
		
		if(isN($tmpplayfrom)){
			$strSet .="d_playfrom='".$row["m_playfrom"]."',d_playserver='".$row["m_playserver"]."',d_playurl='".$urls."' ";
		}
		else if (strpos( ",". $tmpplayfrom , $row["m_playfrom"] ) >0){
			if (be("post","CCTV")=="2") {
				$strSet .="d_playfrom='".$tmpplayfrom . "$$$". $row["m_playfrom"]."',d_playserver='".$tmpplayfrom ."$$$".$row["m_playserver"]."',d_playurl='". $tmpplayurl ."$$$". $urls."' ";
			}
			else{
				$arr1 = explode("$$$",$tmpplayurl);
				$arr2 = explode("$$$",$tmpplayfrom);
				$rc = false;
				$tmpplayurl = "";
				
				for ($k=0;$k<count($arr2);$k++){
					if ($rc) { $tmpplayurl = $tmpplayurl . "$$$";}
					if ($arr2[$k] == $row["m_playfrom"] ) { $arr1[$k] = $urls; }
					$tmpplayurl = $tmpplayurl . $arr1[$k];
					$rc = true;
				}
				$strSet .="d_playurl='".$tmpplayurl."' ";
			}
		}
		else{
			$strSet .="d_playfrom='".$tmpplayfrom . "$$$". $row["m_playfrom"]."',d_playserver='".$tmpplayserver ."$$$".$row["m_playserver"]."',d_playurl='". $tmpplayurl ."$$$". $urls."' ";
		}
		$sql= "update {pre}vod set ".$strSet." where d_id=" .$did;
		$db->query($sql);
		
		$db->query("update {pre}cj_vod set m_zt=1 where m_id=".$row["m_id"]);
		
		$MovieInflowNum=$MovieInflowNum+1;
		if ($MovieInflowNum >= $MovieNumW){
			echo "<script type=\"text/javascript\" language=\"javascript\">";
			echo "document.getElementById(\"refreshlentext\").style.width = \"100%\";";
			echo "document.getElementById(\"refreshlentext\").innerHTML = \"100%\";";
			echo "document.getElementById(\"storagetext\").innerHTML = \"入库完毕 <a href='collect_vod.php'>返回</a>\";";
			echo "alert('入库完毕'); location.href='collect_vod.php';";
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

function getVodUrl($id)
{
	global $db;
	$TempUrl="";
	$sql2="select * from {pre}cj_vod_url where u_movieid=".$id ." order by u_id  asc";
	$rs_collect2= $db->query($sql2);
	$num=1;
	while ($row = $db ->fetch_array($rs_collect2))
	{
		if ($num ==1) {
			$TempUrl .= $row["u_url"];
		}
		Else{
			$TempUrl .= "#".$row["u_url"];
		}
		$num=$num+1;
	}
	unset($rs_collect2);
	return  $TempUrl;
}
?>