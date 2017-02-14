<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("文章过滤转换管理");

switch(trim($action))
{
	case "add" :
	case "edit" : edit();break;
	case "save" : save();break;
	case "del" : del();break;
	case "sflag": setflag();break;
	default : main();break;
}


function save()
{
	global $db;
	$f_id = be("post","f_id");
	$f_name = be("post","f_name");  $f_object = be("post","f_object");
	$f_type = be("post","f_type");  $f_content = be("post","f_content");
	$f_strstart = be("post","f_strstart");  $f_strend = be("post","f_strend");
	$f_rep = be("post","f_rep");  $f_flag = be("post","f_flag");
	$f_pid = be("post","f_pid");
	
	if (isN($f_id)){ $f_id = 0;}
	if (isN($f_flag)){ $f_flag=0; }
	if ($f_type== 1){
		if (isN($f_content)) { alert ("过滤的内容不能为空!");}
	}
	elseif ($f_type == 2) {
		if (isN($f_strstart) || isN($f_strend)){ alert ("开始/结束标记不能为空!");}
	}
	
	
	if($f_id==0){
		$sql ="insert {pre}cj_filters (f_name,f_object,f_type,f_content,f_strstart,f_strend,f_rep,f_flag,f_pid,f_sys) values('".$f_name."','".$f_object."','".$f_type."','".$f_content."','".$f_strstart."','".$f_strend."','".$f_rep."','".$f_flag."','".$f_pid."','1') ";
	}
	else{
		$sql ="update {pre}cj_filters set f_name='".$f_name."',f_object='".$f_object."',f_type='".$f_type."',f_pid='".$f_pid."',f_content='".$f_content."',f_strstart='".$f_strstart."',f_strend='".$f_strend."',f_rep='".$f_rep."',f_flag='".$f_flag."'	 where f_id=" .$f_id;
	}
	
 	$db->query($sql);
	showmsg ("成功修改过滤规则!", getReferer() );
}

function del()
{
	global $db;
	$f_id = be("arr","f_id");
	$ids = explode(",",$f_id);
	foreach($ids as $id){
		$db->query( "delete from {pre}cj_filters where f_id = ".$id);
	}
	redirect (getReferer());
}

function setflag()
{
	global $db;
	$f_id = be("arr","f_id");
	$f_flag = be("get","f_flag");
	$sql="update {pre}cj_filters set f_flag='".$f_flag."' Where f_id in(" . $f_id .")";
	$db->query($sql);
	redirect (getReferer());
}

function edit()
{
	global $db;
	$f_id = be("get","f_id");
	if(!isN($f_id)){
		$sql="Select * from {pre}cj_filters Where f_id=" . $f_id;
		$row = $db->getRow($sql);
		$f_name = $row["f_name"];
		$f_object = $row["f_object"];
		$f_type = $row["f_type"];
		$f_content = $row["f_content"];
		$f_strstart = $row["f_strstart"];
		$f_strend = $row["f_strend"];
		$f_rep = $row["f_rep"];
		$f_flag = $row["f_flag"];
		$f_pid =$row["f_pid"];
    }
    else{
    	$f_type=1;
    }
?>
<script language="javascript">
$(document).ready(function(){
	
});

function showset(v)
{
	if(v==2)
	{
		$("#FilterType1").hide();
		$("#FilterType2").show();
	}
	else
	{
		$("#FilterType1").show();
		$("#FilterType2").hide();
	}
}
</script>
<form action="?action=save" method="post" name="form">
<input type="hidden" id="f_id" name="f_id" value="<?php echo $f_id?>">
<table width="96%" border=0 align=center cellpadding="4" cellSpacing=0 class=tb >
    <tr>
	<td width="20%">过滤名称：</td>
	<td>
		<INPUT id="f_name" name="f_name" size="50" value="<?php echo $f_name?>" >	  </td>
    </tr>
    <tr>
	<td>过滤项目;</td>
	<td>
	<select name="f_pid" id="f_pid" size="1">
	<option value="0" <?php if (trim($f_pid)=="0"){ echo "selected" ;}?>>全局过滤项目</option>
	<?php
		echo makeSelect("{pre}cj_art_projects","p_id","p_name","","","&nbsp;|&nbsp;&nbsp;","");
	?>
	</select> (全局过滤项目将过滤所有的项目)
	</td>
    </tr>
    <tr>
	<td>过滤对象：</td>
	<td>
		<select name="f_object" id="f_object" size="1">
		<option value="0" <?php if ($f_object==0) { echo " selected";} ?>>全局过滤</option>
		<option value="1" <?php if ($f_object==1) { echo " selected";} ?>>标题过滤</option>
		<option value="2" <?php if ($f_object==2) { echo " selected";}?>>简介过滤</option>
		<option value="3" <?php if ($f_object==3) { echo " selected";}?>>播放地址过滤</option>
		</select>
	</td>
    </tr>
    <tr>
	<td>过滤类型：</td>
	<td>
		<select name="f_type" id="f_type" onchange=showset(this.options[this.selectedIndex].value)>
		<option value="1" <?php if ($f_type==1) { echo " selected";} ?>>简单替换</option>
		<option value="2" <?php if ($f_type==2) { echo " selected";} ?>>高级过滤</option>
		</select>
	</td>
    </tr>
	<tr id="FilterType1"  style="display:<?php if ($f_type==2){ echo "none";} ?>"> 
	<td> 过虑内容：</td>
	<td>
		<textarea id="f_content" name="f_content" cols="49" rows="5"><?php echo $f_content?></textarea>
		<font color="#FF0000">&nbsp;&nbsp;&nbsp; 填你要过虑的内容,每条规则只能填一个内容!</font>
	</td>
	</tr>
	<tr id="FilterType2" style="display:<?php if ($f_type==1) { echo "none";} ?>">
      <td> 开始标记：<br><br><br><br><br> 结束标记：</td>
      <td>
      	<textarea id="f_strstart" name="f_strstart" cols="49" rows="5"><?php echo $f_strstart?></textarea>
        <font color="#FF0000">&nbsp;&nbsp;&nbsp; 
		过虑开始标记,包括标记同时过虑掉!</font><br>
      	<textarea id="f_strend" name="f_strend" cols="49" rows="5"><?php echo $f_strend?></textarea>
        <font color="#FF0000">&nbsp;&nbsp;&nbsp; 
		过虑结束标记,包括标记同时过虑掉!</font>
		</td>
	</tr>
	<tr id="FilterRep"> 
      <td> 过虑替换：</td>
      <td>
      <textarea id="f_rep" name="f_rep" cols="49" rows="5"><?php echo $f_rep?></textarea>&nbsp;
      <font color="#FF0000">&nbsp;&nbsp; 
		可为空,为空则只过虑掉你要过虑的内容.</font>
	</td>
	</tr>
	<tr>
		<td><b>是否启用：</b></td>
        <td>&nbsp;&nbsp;
        <input id='f_flag' name='f_flag' type='checkbox' value='1' <?php if ($f_flag==1) { echo "Checked";}?>></td>
	</tr>
	<tr>
	<td colspan="2">
		<input  type="submit" class="btn" name="submit" value="确&nbsp;&nbsp;定" >&nbsp;&nbsp;&nbsp;&nbsp;
		<input name="Cancel" class="btn" type="button" id="Cancel" value="取&nbsp;&nbsp;消" onClick="window.location.href='collect_art_filters.php'">
	</td>
	</tr>
</table>
</form>
<?php
}

function main()
{
	global $db;
	$pagenum = be("get","page");
	if (isN($pagenum) || !isNum($pagenum)){ $pagenum = 1; }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$pagenum = intval($pagenum);
	$project = be("get","{pre}cj_art_projects");
	
	$sql="select a.*,b.p_name from {pre}cj_filters a left join {pre}cj_art_projects b on a.f_pid=b.p_id where 1=1 and f_sys=1 ";
	if ($project!= "") {
		$sql = $sql . " and a.f_pid = " . $project;
	}
	$sql = $sql . " order by f_id desc ";
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount=ceil($nums/app_pagenum);//总页数
	$sql = $sql ."limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","collect_art_filters.php?action=del");
			$("#form1").submit();
		}
		else{return false}
	});
	$("#btnFlag1").click(function(){
		$("#form1").attr("action","collect_art_filters.php?action=sflag&f_flag=1");
		$("#form1").submit();
	});
	$("#btnFlag2").click(function(){
		$("#form1").attr("action","collect_art_filters.php?action=sflag&f_flag=0");
		$("#form1").submit();
	});
	$("#btnAdd").click(function(){
		location.href='collect_art_filters.php?action=add';
	});
});
</script>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td>
		菜单：<a href="collect_art_manage.php?action=">采集规则列表</a> | <a href="collect_art_change.php">分类转换</a> | <a href="collect_art_filters.php">信息过滤</a>  
	&nbsp; 按项目查看：<select onchange=javascript:window.location.href=this.options[this.selectedIndex].value>
	<option value="?">所有转换</option>
	<option value="?{pre}cj_art_projects=0" <?php if ($project=="0"){ echo "selected";} ?>>全局转换</option>
	<?php echo makeSelect("{pre}cj_art_projects","p_id","p_name","","collect_art_filters.php","&nbsp;|&nbsp;&nbsp;",$project); ?>
	</select>
	</td>
	</tr>
</table>

<form action="" method="post" name="form1" id="form1" >
<table class=tb >
	<tr>
	  <td width="4%">&nbsp;</td>
      <td>过滤名称</td>
      <td width="15%" >过滤对象</td>
      <td width="15%">过滤类型</td>
      <td width="15%">所属项目</td>
      <td width="13%">状态</td>
      <td width="10%">操作</td>
	</tr>
	<?php
		if (!$rs){
	 ?>
    <tr><td align="center" colspan="8">没有任何记录!</td></tr>
    <?php
	}
	else{
	  	while ($row = $db ->fetch_array($rs))
	  	{
	?>
    <tr>
	<td>
	<input type="checkbox" value="<?php echo $row["f_id"]?>" id="f_id" name="f_id[]">
	</td>
	<td><?php echo $row["f_name"]?></td>
	<td>
     <?php
      if ($row["f_object"]==1) {
         echo "标题过滤" ;
      }
      else if ($row["f_object"]==2) { 
         echo "简介过滤" ;
      }
      else if ($row["f_object"]==3) { 
         echo "播放地址过滤" ;
      }
      else{
         echo "全局过滤" ;
      }
      ?>
      </td> 
      <td>
      <?php
      if ($row["f_type"]==1) {
         echo "简单替换" ;
      }
      else if ($row["f_type"]==2) { 
         echo "高级过滤" ;
      }
      else{
         echo "请选择！" ;
      }
      ?>
      </td>
      <td>
      <?php
      if ($row["f_pid"]==0) {
         echo "所有项目" ;
      }
      else{
         echo $row["p_name"];
      }
      ?>
      </td>
      <td>
	  <?php
      if ($row["f_flag"]==1) {
         echo "<b>√</b>" ;
      }
      else {
         echo "<font color='red'><b>×</b></font>" ;
      }
      ?>
	</td>
	<td>
		</a>&nbsp;<a href="collect_art_filters.php?action=edit&f_id=<?php echo $row["f_id"]?>">修改</a>
	</td>
    </tr>
<?php
		}
	}
?>
<tr>
	<td  colspan="8">
	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'f_id[]');"/>&nbsp;
	<input type="button" value="批量删除" id="btnDel" class="input"  />
	<input type="button" value="批量启用" id="btnFlag1" class="input"  />
	<input type="button" value="批量禁用" id="btnFlag2" class="input"  />
	<input type="button" id="btnAdd" value="添加转换" class="input">
	</td>
</tr>
	<tr align="center" bgcolor="#f8fbfb">
	<td colspan="8">
    <?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_art_filters.php?{pre}cj_art_projects=".$project."&page={p}") ?>
	</td>
	</tr>
</table>
</form>
<?php
}
?>