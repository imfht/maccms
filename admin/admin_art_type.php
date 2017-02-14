<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
$tempnum =0;
$arrtype = "";

switch($action)
{
	case "editall" : editall();break;
	case "editid" : editid();break;
	case "hide" : hide();break;
	case "nohide" : nohide();break;
	case "into" : into();break;
	default : headAdmin ("文章分类管理"); main();break;
}
dispseObj();

function hide()
{
	global $db;
	$t_id = be("get","t_id");
	$db->Update ("{pre}art_type",array( "t_hide"),array("1"),"t_id=".$t_id);
	updateCacheFile();
	redirect ("admin_art_type.php");
}

function nohide()
{
	global $db;
	$t_id = be("get","t_id");
	$db->Update ("{pre}art_type",array("t_hide"),array("0"),"t_id=".$t_id);
	updateCacheFile();
	redirect ("admin_art_type.php");
}

function into()
{
	global $db;
	$type1 = be("post","t_type1");
	$type2 = be("post","t_type2");
	$db->Update ("{pre}art",array("a_type"),array($type2),"a_type=".$type1);
	redirect ("admin_art_type.php");
}

function editid()
{
	global $db;
	$typeid = be("get","typeid");
	$t_pid = be("get","t_pid");
	$db->Update ("{pre}art_type",array("t_pid"),array($t_pid),"t_id=".$typeid);
	updateCacheFile();
	redirect ("admin_art_type.php");
}

function editall()
{
	global $db;
	$t_id = be("arr","t_id");
	$ids = explode(",",$t_id);
	foreach($ids as $id){
		$t_name = be("post","t_name" .$id);
		$t_enname = be("post","t_enname" .$id) ;
		$t_sort = be("post","t_sort" .$id);
		$t_template = be("post","t_template" .$id);
		$t_arttemplate = be("post","t_arttemplate" .$id);
		$t_key = be("post","t_key" .$id);
		$t_des = be("post","t_des" .$id);
		
		if (isN($t_name)) { echo "名称不能为空"; exit;}
		if (isN($t_enname)) { echo "别名不能为空"; exit;}
		if (!isNum($t_sort)) { echo "排序号不能为空或不是数字"; exit;}
		if (isN($t_template)) { $t_template = "artlist.html";}
		if (isN($t_arttemplate)) { $t_vodtemplate = "art.html";}
		
		$db->Update ("{pre}art_type",array("t_name","t_enname", "t_sort","t_template","t_arttemplate","t_key","t_des"),array($t_name,$t_enname,$t_sort,$t_template,$t_arttemplate,$t_key,$t_des),"t_id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function getTypeCount($t_id)
{
	global $db;
	$typearr = getTypeIDS($t_id,"{pre}art_type");
	return $db->getOne("SELECT count(*) FROM {pre}art WHERE a_type=".$t_id." or a_type IN(".$typearr.")");
}

function getTypeList($t_pid)
{
	global $db,$tempnum,$arrtype;
	$sql = "SELECT t_id,t_name,t_enname,t_sort,t_pid,t_hide,t_template,t_arttemplate,t_key,t_des FROM {pre}art_type WHERE t_pid = ".$t_pid. " ORDER BY t_sort,t_id ASC";
	$rs = $db->query($sql);
	$tempnum = $tempnum + 3;
	while ($row = $db ->fetch_array($rs)){
		$tcount = getTypeCount( $row["t_id"]);
?>
	<tr>
    	<td align="left" >
        <div style="float:left;">
        <?php for($i=0;$i<$tempnum-3;$i++){ echo "&nbsp;";}?>
        <?php if($t_pid==0){ echo "╄"; }else{ echo "├"; }?>
        <input type="checkbox" name="t_id[]" value="<?php echo $row["t_id"]?>" class="checkbox" />
        <a href="admin_art.php?{pre}art_type=<?php echo $row["t_id"]?>"><?php echo $row["t_name"]?></a>
        <input type="button" value="父" onClick="ShowPDIV(<?php echo $row["t_id"]?>,<?php echo $row["t_pid"]?>);" name="Input" class="btn" />
        </div>
        (<font color="red"><?php echo $tcount?></font>)
        <div id="type_P_DIV_<?php echo $row["t_id"]?>" style=" float:left;display:none; height:25px; margin-top:5px;">
        <select id="type_P_CID_<?php echo $row["t_id"]?>" name="type_P_CID_<?php echo $row["t_id"]?>" onChange="SelectPid(this.value,<?php echo $row["t_id"]?>);">
	     </select>
	    </div>
        </td>
        <td><?php echo $row["t_id"]?></td>
		<td><input size="10" type="text" name="t_name<?php echo $row["t_id"]?>" value="<?php echo $row["t_name"]?>"></td>
		<td><input size="10" type="text" name="t_enname<?php echo $row["t_id"]?>" value="<?php echo $row["t_enname"]?>"></td>
        <td><input size="10" type="text" name="t_template<?php echo $row["t_id"]?>" value="<?php echo $row["t_template"]?>"></td>
		<td><input size="10" type="text" name="t_arttemplate<?php echo $row["t_id"]?>" value="<?php echo $row["t_arttemplate"]?>"></td>
		<td><input size="10" type="text" name="t_key<?php echo $row["t_id"]?>" value="<?php echo $row["t_key"]?>"></td>
		<td><input size="10" type="text" name="t_des<?php echo $row["t_id"]?>" value="<?php echo $row["t_des"]?>"></td>
		<td><input size="3" type="text" name="t_sort<?php echo $row["t_id"]?>" value="<?php echo $row["t_sort"]?>"></td>
		<td>
		<?php if ($tcount==0){?><a href="admin_ajax.php?action=del&tab={pre}art_type&t_id=<?php echo $row["t_id"]?>" onClick="return confirm('确定要删除吗?');">删除</a>|<?php }?>
		
		<?php if ($row["t_hide"]==1){?>
		<a href="admin_art_type.php?action=nohide&t_id=<?php echo $row["t_id"]?>">显示</a>
		<?php 
			}else if ($row["t_hide"]==0){
		?>
		<a href="admin_art_type.php?action=hide&t_id=<?php echo $row["t_id"]?>">隐藏</a>
		<?php
			}
		?>
		</td>
  	</tr>
<?php
	if ($t_pid==0){ $arrtype = $arrtype . "@|@".$row["t_id"]."|=|" .$row["t_name"];}
	getTypeList( $row["t_id"]);
	}
	$tempnum = $tempnum-3;
	unset($rs);
}

function main()
{
	global $arrtype;
?>
<script language="javascript">
$(document).ready(function(){
	$("#form2").validate({
		rules:{
			t_name:{
				required:true,
				stringCheck:true,
				maxlength:64
			},
			t_enname:{
				required:true,
				stringCheck:true,
				maxlength:128
			},
			t_pid:{
				required:true
			},
			t_template:{
				required:true,
				maxlength:64
			},
			t_arttemplate:{
				required:true,
				maxlength:64
			},
			t_sort:{
				number:true
			},
			t_key:{
				maxlength:254
			},
			t_des:{
				maxlength:254
			}
		}
	});
	$("#form3").validate({
		rules:{
			t_type1:{
				required:true
			},
			t_type2:{
				required:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        	location.href=location.href;
	        });
	    }
	});
	$('#form2').form({
		onSubmit:function(){
			if(!$("#form2").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#btnEdit").click(function(){
		$("#form1").attr("action","?action=editall");
		$("#form1").submit();
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$("#flag").val("add");
		$("#t_template").val("artlist.html");
		$("#t_arttemplate").val("art.html");
		$('#win1').window('open'); 
	});
	$("#btnCancel").click(function(){
		location.href= "admin_art_type.php?updatecache=1";
	});
	$("#btnMove").click(function(){
		$('#form3').form('clear');
		$('#win2').window('open'); 
	});
});
function ShowPDIV(i,p){
    if($("#type_P_CID_"+i)[0].length==0){
	    var v = $("#type_H_V")[0].innerHTML.split('@|@');
		$("#type_P_CID_"+i)[0].options[0] = new Option("作为顶级","0");
		for(j=1;j<v.length;j++){
		    $("#type_P_CID_"+i)[0].options[j] = new Option(v[j].split('|=|')[1],v[j].split('|=|')[0]);
			if(p==v[j].split('|=|')[0]) $("#type_P_CID_"+i)[0].options[j].selected = true;
		}
	}	
	$("#type_P_DIV_"+i)[0].style.display = ($("#type_P_DIV_"+i)[0].style.display == "none") ? "block" : "none";
}
function SelectPid(p,i){
	window.location.href = '?action=editid&typeid='+i+'&t_pid='+p;
}
</script>
<form action="" method="post" id="form1" name="form1">
<table class="tb2">
	<tr>
	<td>&nbsp;</td>
	<td width="35">编号</td>
	<td width="12%">名称</td>
	<td width="12%">别名</td>
	<td width="12%">分类模板</td>
	<td width="12%">内容模板</td>
	<td width="10%">关键字</td>
	<td width="10%">描述</td>
	<td width="5%">排序</td>
	<td width="10%">操作</td>
	</tr>
	<?php
		getTypeList(0);
	?>
	<tr>
	<td colspan="11">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" />
	&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />
	&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />
	&nbsp;<input type="button" value="数据转移" id="btnMove" class="input" />
	</td>
	</tr>
</table>
</form>
<div id="type_H_V" style="display:none"><?php echo $arrtype?></div>


<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:400px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}art_type" method="post" id="form2" name="form2">
<table class="tb">
	<input id="flag" name="flag" type="hidden" value="add">
	<tr>
	<td width="20%">父级分类：</td>
	<td><select id="t_pic" name="t_pid">
      <option value="0">顶级分类</option>
    	<?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","")?>
	  </select>
	</td>
	</tr>
	<tr>
	<td>名称：</td>
	<td><input type="text" id="t_pic" name="t_name" size="25">
	</td>
	</tr>
	<tr>
	<td>别名：</td>
	<td><input type="text" id="t_enname" name="t_enname" size="25">
	</td>
	</tr>
	<tr>
	<td>分类模板：</td>
	<td><input type="text" id="t_template" name="t_template" size="25">
	</td>
	</tr>
	<tr>
	<td>内容模板：</td>
	<td><input type="text" id="t_arttemplate" name="t_arttemplate" size="25">
	</td>
	</tr>
	<tr>
	<td>关键字：</td>
	<td><input type="text" id="t_key" name="t_key" size="25">
	</td>
	</tr>
	<tr>
	<td>描述：</td>
	<td><input type="text" id="t_des" name="t_des" size="25">
	</td>
	</tr>
	<tr>
	<td>排序：</td>
	<td><input type="text" id="t_sort" name="t_sort" size="25">
	</td>
	</tr>
	<tr align="center">
      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
    </tr>
  </form>
</table>
</div>

<div id="win2" class="easyui-window" title="窗口" style="padding:5px;width:400px;" closed="true" minimizable="false" maximizable="false">
<form action="?action=into" method="post" id="form3" name="form3">
<table class="tb">
	<tr>
	<td>
	选择分类：
	<select name="t_type1">
	<?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","")?>
	</select>
	</td>
	</tr>
	<tr>
	<td>
	目标分类：
	<select name="t_type2">
	<?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","")?>
	</select>
	</td>
	</tr>
	<tr>
	<td>
	<input class="input" type="submit" value="保存" id="btnSave">
	
</table>
</form>
</div>

</body>
</html>
<?php
}
?>