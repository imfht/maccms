<?php
@ini_set('memory_limit','20M');
require_once ("admin_conn.php");
chkLogin();

$action = be("get","action");
switch($action)
{
	case "reduction" : reduction();break;
	case "del" : del();break;
	case "delall" : delall();break;
	case "compress":compress();break;
	case "bak":bak();break;
	case "repair":repair();break;
	case "getsize":getsize();break;
	default : headAdmin ("数据库管理"); main();break;
}
dispseObj();

function make_header($table)
{
	global $db;
	$sql = "DROP TABLE IF EXISTS ".$table."\n";
	$row = $db->getRow("show create table ".$table);
	$tmp = preg_replace("/\n/","",$row["Create Table"]);
	$sql.= $tmp."\n";
	unset($row);
	return $sql;
}

function make_manager($table)
{
	global $db;
	$sql = make_header($table);
	$rsfield = $db->getTableFields(app_dbname,$table);
	$i=0;
	while($rowfield = mysql_fetch_field($rsfield)){
		$fs[$i] = trim($rowfield->name);
		$i++;
	}
	unset($rsfield);
	$fsd=$i-1;
	$rsdata = $db->getAll("select * from $table");
	$rscount = count($rsdata);
	$intable = "INSERT INTO `$table` VALUES(";
	for($j=0;$j<$rscount;$j++){
		$line = $intable;
		for($k=0;$k<=$fsd;$k++){
			if($k < $fsd){
				$line.="'".mysql_escape_string($rsdata[$j][$fs[$k]])."',";
			}
			else{
				$line.="'".mysql_escape_string($rsdata[$j][$fs[$k]])."');\r\n";
			}
		}
		$sql.=$line;
	}
	unset($fs);
	unset($rsdata);
	return $sql;
}

function bak()
{
	global $db;
	$fpath = "bak/" . date('Ymd',time()) . "_". getRndStr(10) ;
	$sql="";
	$p=1;
	$tables = "{pre}art_topic,{pre}art_type,{pre}comment,{pre}gbook,{pre}link,{pre}mood,{pre}user,{pre}user_card,{pre}user_group,{pre}user_visit,{pre}vod_topic,{pre}vod_type,{pre}art,{pre}vod";
	$tables = replaceStr($tables,"{pre}",app_tablepre);
	$tablearr = explode(",",$tables);
	
	foreach( $tablearr as $table ){
		$sql.= make_header($table);
		$rsfield = $db->getTableFields(app_dbname,$table);
		$i=0;
		
		while($rowfield = mysql_fetch_field($rsfield)){
			$fs[$i] = trim($rowfield->name);
			$i++;
		}
		unset($rsfield);
		
		
		$fsd=$i-1;
		$nums = $db->getOne("select count(*) from $table");
		$pagecount = 1;
		if($nums>1000){
			$pagecount = ceil($nums/1000);
		}
		
		for($n=1;$n<=$pagecount;$n++){
			$rsdata = $db->getAll("select * from $table limit ".(1000 * ($n-1)).",1000");
			$rscount = count($rsdata);
			$intable = "INSERT INTO `$table` VALUES(";
			for($j=0;$j<$rscount;$j++){
				$line = $intable;
				for($k=0;$k<=$fsd;$k++){
					if($k < $fsd){
						$line.="'".mysql_escape_string($rsdata[$j][$fs[$k]])."',";
					}
					else{
						$line.="'".mysql_escape_string($rsdata[$j][$fs[$k]])."');\r\n";
					}
				}
				$sql.=$line;
				if(strlen($sql)>= 2000000){
					$fname = $fpath . "-".$p.".sql" ;
					fwrite(fopen($fname,"wb"),$sql);
					$p++;
					unset($sql);
				}
			}
			unset($rsdata);
		}
		unset($fs);
	}
	unset($tablearr);
	
	$sql .= make_manager( replaceStr("{pre}manager","{pre}",app_tablepre) );
	$fname = $fpath . "-".$p.".sql" ;
	fwrite(fopen($fname,"wb"),$sql)	;
	
	echo "备份成功";
}


function reduction()
{
	global $db;
	$fname = be("get","file");
	$num = be("get","num");
	$fcount = be("get","fcount");
	
	if(!isNum($num)){ $num=1;} else{ $num=intval($num); }
	if(!isNum($fcount)){ $fcount=-1;} else { $fcount = intval($fcount); }
	if($fcount==-1){
		$fcount=0;
	    foreach( glob('bak/*') as $single){
	    	$single = str_replace("bak/","",$single);
			if(strpos(",".$single,$fname)>0){
				$fcount++;
	    	}
		}
	}
	if($num>$fcount){
		showMsg ( "数据库还原完毕，请重新登录后更新系统缓存", "admin_db.php" );
	}
    else{
    	for($j=$num;$j<=$fcount;$j++){
			$fpath = "bak/".$fname . "-".$j.".sql";
	    	$sqls = file($fpath);
			foreach($sqls as $sql)
			{
				$sql = str_replace("\r","",$sql);
				$sql = str_replace("\n","",$sql);
				$sql = str_replace(chr(13),"",$sql);
				if (!isN($sql)){
					$db->query(trim($sql));
				}
				unset($sql);
			}
			unset($sqls);
	    }
	    
	    showMsg ( "共有".$fcount."个备份分卷文件需要还原，正在还原第".$num."个文件...", "admin_db.php?action=reduction&num=".($num+1)."&fcount=".$fcount."&file=".$fname );
	   
    }
}

function compress()
{
	global $db;
	$rs = $db->query("show table status from `". app_dbname . "`");
	while($rowt = $db ->fetch_array($rs)){
		$table= $rowt["name"];
		$db->query("optimize table ".$table);
	}
	unset($rs);
	echo "压缩成功";
}

function repair()
{
	global $db;
	$status = $db->query("REPAIR TABLE `{pre}art` ,`{pre}art_topic` ,`{pre}art_type` ,`{pre}comment` ,`{pre}gbook` ,`{pre}link` ,`{pre}manager` ,`{pre}mood` ,`{pre}user` ,`{pre}user_card` ,`{pre}user_group` ,`{pre}user_visit` ,`{pre}vod` ,`{pre}vod_topic` ,`{pre}vod_type` ");
	if($status){
		echo "修复成功";
	}
	else{
		echo "修复失败";
	}
}

function del()
{
	$fname = be("all","file");
	$handle= opendir('bak');
	while($file = readdir($handle)){
		if($file!="" && strpos(",".$file,$fname)>0){
    		unlink("bak/".$file);
    	}
    }
    closedir($handle);
    unset($handle);
	redirect ( getReferer() );
}

function delall()
{
	$fname = be("arr","file");
	$filearr = explode(",",$fname);
	$handle=opendir('bak');
	
	foreach ($filearr as $f){
		if (!isN($f)){
			while($file = readdir($handle)){
				if($file!="" && strpos(",".$file,$fname)>0){
		    		unlink("bak/".$file);
		    	}
			}
		}
	}
	closedir($handle);
    unset($handle);
	echo "删除成功";
}

function getsize()
{
	global $db;
	$fname = be("get","file");
	$handle=opendir('bak');
	$fsize=0;
	while($file = readdir($handle)){
		if(strpos(",".$file,$fname)>0){
			$fsize = $fsize + round(filesize("bak/$file")/1024);
    	}
    }
    closedir($handle);
    unset($handle);
	echo $fsize;
}

function main()
{
	$handle=opendir('bak');
?>
<script type="text/javascript">
	$(document).ready(function(){
    	$("#btnDel").click(function(){
    		$("#form1").attr("action","?action=delall");
			$("#form1").submit();
    	});
    	$("#btnCompress").click(function(){
    		$("#form1").attr("action","?action=compress");
			$("#form1").submit();
    	});
    	$("#btnBak").click(function(){
    		$("#msg").html("<font color=red>正在备份数据，请稍后...</font>");
    		$("#form1").attr("action","?action=bak");
			$("#form1").submit();
    	});
    	$("#btnRiwen").click(function(){
    		location.href= '?action=riwen';
    	});
    	$("#btnRepair").click(function(){
    		$("#form1").attr("action","?action=repair");
			$("#form1").submit();
    	})
    	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	    	$("#msg").html("");
	        $.messager.alert('系统提示', data, 'info',function(){
	        	location.href=location.href;
	        });
	    }
		});
	});
	function getSize(i,n){
		$.ajax({
        cache: false, dataType: 'html', type: 'GET', url:  'admin_db.php?action=getsize&file='+n,
        success: function(obj) {
        	$("#s"+i).html(obj);
        }
    });
	}
</script>
</head>
<body>
<form action="" method="post" id="form1" name="form1">
<table class="tb">
    <tr>
      <td width="5%">编号</td>
      <td>备份名称</td>
      <td width="10%">分卷大小</td>
      <td width="15%">备份时间</td>
      <td width="15%">操作</td>
    </tr>
	 <?php
	 $fnum=0;
	 $oldfname="";
	 while($file = readdir($handle)){
	 	 $fnum++;
	 	 $arr = explode("-",$file);
	 	 if(intval($arr[1]==1)){
	 	 	 $fname = $arr[0];
	 	 	 $ftime = date( 'Y-m-d H:i:s',filemtime("bak/".$file) );
	 	 	 $fsize = round(filesize("bak/$file")/1024);
	 ?>
    <tr>
      <td><input name="file[]" type="checkbox" value="<?php echo $fname?>" /></td>
      <td><?php echo $fname?></td>
      <td><em id="s<?php echo $fnum?>">加载中...</em><script>getSize(<?php echo $fnum?>,"<?php echo $fname?>")</script>KB</td>
      <td><?php echo $ftime?></td>
      <td><A href="admin_db.php?action=reduction&file=<?php echo $fname?>">还原</a> | <A href="admin_db.php?action=del&file=<?php echo $fname?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
    <?php
    	 }
	}
    ?>
    <tr>
    <td colspan="5">
    <input name="chkall" type="checkbox" id="chkall" value="checkbox" onClick="checkAll(this.checked,'file[]');"/>
    <input type="button" id="btnDel" value="批量删除" class="input">
	<input type="button" id="btnCompress" value="压缩数据库" class="input">
	<input type="button" id="btnBak" value="备份数据库" class="input">
	<input type="button" id="btnRepair" value="修复数据库" class="input">
	<span id="msg"></span>
    </td>
    </tr>
</table>
</form>
</body>
</html>
<?php
	closedir($handle);
	unset($handle);
}
?>