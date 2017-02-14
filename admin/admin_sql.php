<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "sqlexe" : headAdmin ("执行sql语句"); sqlexe();break;
	default :  headAdmin ("执行sql语句"); main();break;
}
dispseObj();

function sqlexe()
{
	global $db;
	$sql = be("post","sql");
	
	if (!isN($sql)){
		$sql= stripslashes($sql);
		if (strtolower(substr($sql,0,6))=="select"){
			$isselect=true;
		}
		else{
			$isselect=false;
		}
		$rs = $db->query($sql);
		$num=mysql_affected_rows();
	}
?>
<table class="tb">
	<?php
	if($isselect && $num==0){
		echo "<tr><td>未找到任何数据</td></tr>";
	}
	else if ($isselect){
		$i=0;
	    while($row=$db->fetch_array($rs)){
			if($i==0){
				$strcol = "";
				foreach($row as $k=>$v){
					$strcol .= "<td><strong>$k</strong></td>";
				}
				echo "<tr>".$strcol."</tr>";
			}
			$one="";
			foreach( $row as $k=>$v){
				$one = "<td>$v</td>";
			}
			echo "<tr>".$one."</tr>";
	  	  $i++;
		}
	}
	else{
	?>
	<tr>
	<td><strong>执行结果</strong></td>
	</tr>
	<tr>
	<td><?php echo $nums ."条纪录被影响"?></td>
	</tr>
	<?php
	}
	?>
</table>
<?php
}

function main()
{
?>
<script language="javascript">
	$(document).ready(function(){
		$("#form1").validate({
			rules:{
				sql:{
					required:true
				}
			}
		});
	});
</script>
<form action="?action=sqlexe" method="post" name="form1" id="form1">
<table class="tb">
    <tr>
      <td>
      	<textarea name="sql" type="text" id="sql" rows="10" style="width:90%"></textarea>
      </td>
      </tr>
    <tr>
      <td><input class="input" type="submit" value="执行" name="Submit"></td>
    </tr>
    <tr>
	<td valign="top" >
	<strong><br>常用语句对照<br></strong><br>
	<strong>1.查询数据</strong><br>
	SELECT * FROM {pre}vod&nbsp;&nbsp; 查询所有数据<br>
	SELECT * FROM {pre}vod WHERE d_id=1000&nbsp;&nbsp; 查询指定ID数据<br>
    <strong>2.删除数据</strong><br>
    DELETE FROM {pre}vod&nbsp;&nbsp; 删除所有数据<br>
	DELETE FROM {pre}vod WHERE d_id=1000 &nbsp; 删除指定的第几条数据<br>
	DELETE FROM {pre}vod WHERE d_starring LIKE '%刘德华%'&nbsp;&nbsp; 删除d_starring字段里有&quot;刘德华&quot;的数据<br>
	<strong>&nbsp;3.修改数据</strong><br>
	UPDATE {pre}vod SET d_hits=1&nbsp;&nbsp; 将所有d_hits字段里的值修改成&quot;1&quot;<br>
	UPDATE {pre}vod SET d_hits=1 WHERE d_id=1000&nbsp; 指定的第几条数据把d_hits字段里的值修改成&quot;1&quot; <br>
	</td>
</tr>
</table>
</form>
<?php
}
?>
</body>
</html>