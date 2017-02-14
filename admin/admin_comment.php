<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "audit" : del();break;
	default : headAdmin ("评论管理");main();break;
}
dispseObj();

function main()
{
	global $db;
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1; } else { $pagenum=intval($pagenum); }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$sql = "SELECT count(*) FROM {pre}comment";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "select c_id,c_type,c_vid,c_rid,c_audit,c_name,c_ip,c_content,c_time from {pre}comment order by c_id desc ";
	$sql .=  " limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){
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
	$("#btnCancel").click(function(){
		location.href= location.href;
	});
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}comment");
			$("#form1").submit();
		}
		else{return false}
	});
	$("#btnAudit").click(function(){
		$("#form1").attr("action","admin_comment.php?action=audit");
		$("#form1").submit();
	});
});

</script>
<form action="" method="post" name="form1" id="form1">
<table class="tb">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="15%">用户</td>
	<td >内容</td>
	<td width="20%">来源</td>
	<td width="15%">时间</td>
	<td width="10%">IP地址</td>
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
		  		$c_id = $row["c_id"];
		  		$c_content=$row["c_content"];
		  		$c_content = regReplace($c_content, "\[em:(\d{1,})?\]", "<img src=\"../images/face/$1.gif\" border=0/>");
	?>
	  <tr>
	  <td><input name="c_id[]" type="checkbox" id="c_id" value="<?php echo $c_id?>" /></td>
      <td><?php echo $row["c_name"]?></td>
      <td><?php echo $c_content?></td>
      <td>
      <?php
      		$vid = $row["c_vid"];
		 	if ($row["c_type"]==2){
			 	$rowinfo = $db->getRow("select a_id,a_title,a_type from {pre}art where a_id=".$vid);
				if (!$rowinfo){
					echo "该数据已经被删除";
				}
				else{
					echo  "(".$vid.")". $rowinfo["a_title"];
				}
				unset($rowinfo);
			}
			else{
				$rowinfo = $db->getRow("select d_id,d_name,d_type from {pre}vod where d_id=".$vid);
				if (!$rowinfo){
					echo "该数据已经被删除";
				}
				else{
					echo "(".$vid.")". $rowinfo["d_name"];
				}
				unset($rowinfo);
			}
      ?>
      </td>
      <td><?php echo $row["c_time"]?></td>
      <td><?php echo $row["c_ip"]?></td>
      </tr>
      <?php
	      	}
	    }
	?>
	<tr>
	<td colspan="7">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'c_id[]')" />
	&nbsp;<input type="button" id="btnDel" value="批量删除" class="input" />
	</td></tr>
    <tr align="center">
      <td colspan="7">
	    <?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_comment.php?page={p}")?>
       </td>
    </tr>
    </table>
    </form>
</body>
</html>
<?php
	unset($rs);
}
?>