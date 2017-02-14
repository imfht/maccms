<?php
	require_once ("../admin_conn.php");
	
	chkLogin();
	$action=be("get","action");
	$id=be("get","id");
	if(isN($id)){$id="pic";}
	$ftypes=array('jpg','gif','bmp','png',".jpeg");
	$upfileDir= "../". $_SESSION["upfolder"] . "/" . getSavePicPath() . "/";
	$maxSize=1000;
	$isvod = true;
	if(strpos($_SESSION["upfolder"],"/art")>0){
		$isvod=false;
	}
	if(!file_exists($upfileDir)){
		mkdir($upfileDir);
	}
	
	foreach($_FILES as $FILEa){
		if(!in_array(substr($FILEa['name'],-3,3),$ftypes))
			$errm = "文件格式不正确　[ <a href=# onclick=history.go(-1)>重新上传</a> ]";
		if($FILEa['size']> $maxSize*1024)
			$errm = "文件大小超过了限制　[ <a onclick=history.go(-1)>重新上传</a> ]";
		if($FILEa['error'] !=0)
			$errm = "未知错误";
		
		if($errm!=''){
			if($action=="xht"){
				$errm = "{'err':'".$errm."','msg':''}";
			}
			echo $errm;
			exit;
		}
		
		
		$targetDir= "../". $_SESSION["upfolder"] . "/" . getSavePicPath() . "/";
		$targetFile=date('Ymd').time().substr($FILEa['name'],-4,4);
		$realFile=$targetDir.$targetFile;
		
		if(function_exists('move_uploaded_file')){
			move_uploaded_file($FILEa['tmp_name'],$realFile);
			if(app_watermark==1){
				imageWaterMark($targetDir.$targetFile,getcwd(),app_waterlocation,app_waterfont);
			}
			if (app_ftp==1 && $isvod){
				uploadftp( $targetDir ,$targetFile );
			}
			if($action=="xht"){
				echo "{'err':'".$errm."','msg':'".app_installdir. replaceStr($upfileDir,"../../","").$targetFile."'}";
			}
			else{
				die("<script>parent.document.getElementById('".$id."').value='".replaceStr($upfileDir,"../../","").$targetFile."'</script>上传成功![ <a href=### onclick=history.go(-1)>重新上传</a> ]");
			}
		}
		else{
			@copy($FILEa['tmp_name'],$realFile);
			if(app_watermark==1){
				imageWaterMark($targetDir.$targetFile,getcwd(),app_waterlocation,app_waterfont);
			}
			if (app_ftp==1 && $isvod){
				uploadftp( $targetDir.$targetFile );
			}
			if($action=="xht"){
				echo "{'err':'".$errm."','msg':'".app_installdir. replaceStr($upfileDir,"../../","").$targetFile."'}";
			}
			else{
				die("<script>parent.document.getElementById('pic').value='".replaceStr($upfileDir,"../../","").$targetFile."'</script> 上传成功![ <a href=### onclick=history.go(-1)>重新上传</a> ]");
			}
		}
	}
?>