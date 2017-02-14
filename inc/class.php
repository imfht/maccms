<?php
class AppDataBase
{
	var $sql_id;
	var $iqueryCount = 0;
	
	function AppDataBase($dbhost, $dbuser, $dbpw, $dbname = '', $charset = 'utf8',$newlink=false)
	{
		if(!$this->sql_id=@mysql_connect($dbhost, $dbuser, $dbpw, $newlink)) {				
			$this->ErrorMsg('MYSQL 连接数据库失败,请确定数据库用户名,密码设置正确<br>');
			exit;
		}
		if(!@mysql_select_db($dbname,$this->sql_id)){
			$this->ErrorMsg("MYSQL 连接成功,但当前使用的数据库 {$dbname} 不存在<br>");
			exit;
		}
		
		if( mysql_get_server_info($this->sql_id) > '4.1' ){
			if($charset){
				//mysql_query("SET NAMES '$charset'");
				mysql_query("SET character_set_connection=$charset,character_set_results=$charset,character_set_client=binary",$this->sql_id);
			}
			else{
				mysql_query("SET character_set_client=binary",$this->sql_id);
			}
			if( mysql_get_server_info($this->sql_id) > '5.0' ){
				mysql_query("SET sql_mode=''",$this->sql_id);
			}
		}
		else{
			$this->ErrorMsg("本系统仅支持MYSQL4.1以上版本<br>");
			exit;
		}
	}
	
	function oldAppDataBase($dbhost, $dbuser, $dbpw, $dbname = '', $charset = 'utf8',$newlink=false)
	{
		if(!($this->sql_id = mysql_connect($dbhost, $dbuser, $dbpw ,$newlink))){
			$this->ErrorMsg("Can't pConnect MySQL Server($dbhost)!");
		}
		mysql_query("SET NAMES " . $charset, $this->sql_id);
		mysql_query("SET character_set_client " . $charset, $this->sql_id);
		mysql_query("SET character_set_results " . $charset, $this->sql_id);
		@mysql_query($this->sql_id);
		if ($dbname){
			if (mysql_select_db($dbname, $this->sql_id) === false ){
				$this->ErrorMsg("Can't select MySQL database($dbname)!");
				return false;
			}
			else{
				return true;
			}
		}
	}
	
	function close() {
		$this->iqueryCount=0;
		return mysql_close($this->sql_id);
	}
	
	function select_database($dbname)
	{
		return mysql_select_db($dbname, $this->sql_id);
	}
	
	function fetch_array($query, $result_type = MYSQL_ASSOC)
	{
		return mysql_fetch_array($query, $result_type);
	}
	
	function query($sql)
	{
		$this->iqueryCount++;
		$sql = str_replace("{pre}",app_tablepre,$sql);
		return mysql_query($sql, $this->sql_id);
	}
	
	function queryArray($sql,$keyf='')
	{
		$array = array();
		$result = $this->query($sql);
		while($r = $this->fetch_array($result))
		{
			if($keyf){
				$key = $r[$keyf];
				$array[$key] = $r;
			}
			else{
				$array[] = $r;
			}
		}
		return $array;
	}
	
	function affected_rows()
	{
		return mysql_affected_rows($this->sql_id);
	}
	
	function num_rows($query)
	{
		return mysql_num_rows($query);
	}
	
	function insert_id()
	{
		return mysql_insert_id($this->sql_id);
	}
	
	function selectLimit($sql, $num, $start = 0)
	{
		if ($start == 0){
			$sql .= ' LIMIT ' . $num;
		}
		else{
			$sql .= ' LIMIT ' . $start . ', ' . $num;
		}
		return $this->query($sql);
	}
	
	function getOne($sql, $limited = false)
	{
		if ($limited == true){
			$sql = trim($sql . ' LIMIT 1');
		}
		$res = $this->query($sql);
		if ($res !== false){
			$row = mysql_fetch_row($res);
			return $row[0];
		}
		else{
			return false;
		}
	}
	function getRow($sql)
	{
		$res = $this->query($sql);
		if ($res !== false){
			return mysql_fetch_assoc($res);
		}
		else{
			return false;
		}
	}
	
	function getAll($sql)
	{
		$res = $this->query($sql);
		if ($res !== false){
			$arr = array();
			while ($row = mysql_fetch_assoc($res)){
				$arr[] = $row;
			}
			return $arr;
		}
		else{
			return false;
		}
	}
	
	function getTableFields($dbname,$tablename)
	{
		$tablename = str_replace("{pre}",app_tablepre,$tablename);
		return mysql_list_fields($dbname,$tablename,$this->sql_id);
	}
	
	function Exist($tableName,$fieldName ,$ID)
	{
		$SqlStr="SELECT * FROM ".$tableName." WHERE ".$fieldName."=".$ID;
		$res=false;
		try{
			$row = $this->getRow($SqlStr);
			if($row){ $res=true; }
			unset($row);
		}
		catch(Exception $e){
		}
		return $res;
	}
	
	function AutoID($tableName,$colname)
	{
		$n = $this->getOne("SELECT Max(".$colname.") FROM [".$tableName."]");
		if (!isNum(n)){ $n=0; }
		return $n;
	}
	
	function Add($tableName,$arrFieldName ,$arrValue)
	{
		$res=false;
		if (chkArray($arrFieldName,$arrValue)){
			$sqlcol = "";
			$sqlval = "";
			$rc=false;
			foreach($arrFieldName as $a){
				if($rc){ $sqlcol.=",";}
				$sqlcol .= $a;
				$rc=true;
			}
			$rc=false;
			foreach($arrValue as $b){
				if($rc){ $sqlval.=",";}
				$sqlval .= "'".$b."'";
				$rc=true;
			}
			$sql = " INSERT INTO " . $tableName." (".$sqlcol.") VALUES(".$sqlval.")" ;
			//echo $sql."<br>";exit;
			$res = $this->query($sql);
			if($res){
				//echo "ok";
			}
			else{
				//echo "err";
			}
		}
		return $res;
	}
	
	function Update($tableName,$arrFieldName , $arrValue ,$KeyStr)
	{
		$res=false;
		if (chkArray($arrFieldName,$arrValue)){
			$sqlval = "";
			$rc=false;
			for($i=0;$i<count($arrFieldName);$i++){
				if($rc){ $sqlval.=",";}
				$sqlval .= $arrFieldName[$i]."='".$arrValue[$i]."'";
				$rc=true;
			}
			$res = $this->query(" UPDATE " . $tableName." SET ".$sqlval." WHERE ".$KeyStr."" );
			$res=true;
		}
		return $res;
	}
	
	function Delete($tableName,$KeyStr)
	{
		$res=false;
		$sql = "DELETE FROM ".$tableName." WHERE ".$KeyStr;
		$res = $this->query($sql);
		return $res;
	}
			
	function ErrorMsg($message = '', $sql = '')
	{
		if ($message){
			die("<b>error info</b>: $message\n\n");
		}
		else{
			echo "<b>MySQL server error report:";
			print_r($this->error_message);
			die("");
		}
		exit;
	}
}

class AppFile {
    var $Fp;
    var $Pipe;
    var $File;
    var $OpenMode;
    var $data;
        
    function AppFile($File,$Mode = 'r',$Data4Write='',$Pipe = 'f'){
    	if (!file_exists($dirname = dirname($File))){
			$this->mkdirs($dirname);
		}
        $this -> File = $File;
        $this -> Pipe = $Pipe;
        if($Mode == 'dr'){
            $this -> OpenMode = 'r';
            $this -> open();
            $this -> getdata();
            $this -> close();
        }else{       
            $this -> OpenMode = $Mode;
            $this -> open();
        }       
        if($this->OpenMode=='w'&$Data4Write!=''){
            $this -> write($Data4Write,$Mode = 3);
        }
    }
    function open(){
        if ($this -> OpenMode == 'r'||$this -> OpenMode == 'r+'){
            if($this->CheckFile()){
                if ($this -> Pipe == 'f') {
                    $this->Fp = fopen($this -> File, $this -> OpenMode);
                } elseif ($Pipe == 'p') {       
                    $this->Fp = popen($this -> File, $this -> OpenMode);
                }else{       
                    die("请检查文件打开参数3,f:fopen()");
                }
            } else {
                die("文件访问错误,请检查文件是否存在!");
            }
        } else {
            if ($this -> Pipe == 'f') {
                $this->Fp = fopen($this -> File, $this -> OpenMode);
            } elseif ($Pipe == 'p') {       
                $this->Fp = popen($this -> File, $this -> OpenMode);
            } else {       
                Die("请检查文件打开参数3,f:fopen()");
            }
        }
    }
    function close(){
        if ($this->Pipe == 'f'){
            @fclose($this->Fp);
        } else {       
            @pclose($this->Fp);
        }
    }
    function getdata(){
        @flock($this->Fp, 1);
        $Content = @fread($this->Fp, filesize($this->File));
        $this->data = $Content;
    }
    function CheckFile(){
        if (file_exists($this -> File)) { return true; } else { return false; }
    }
	function mkdirs($path){
		if (!is_dir(dirname($path))) {
			$this->mkdirs(dirname($path));
		}
		if(!file_exists($path)){
			return mkdir($path);
		}
	}
    function write($Data4Write,$Mode = 3){
        @flock($this->Fp,$Mode);
        fwrite($this->Fp,$Data4Write);
        $this->close();
        return true;
    }
}

class AppCache
{
	var $_dir  = "";
	var $_time = 60;
	var $_id = "";
	function __construct($options=array(NULL)){
		if(is_array($options)){
			$available_options = array('_dir','_time');
			foreach($options as $key => $value){
				if(in_array($key,$available_options)){
					$this->$key = $value;
				}
			}
		}
	}
	function get($id){
		$this->_id = md5(md5($id));
		if(file_exists($this->_dir.$this->_id) && ((time() - filemtime($this->_dir.$this->_id)) < $this->_time)){
			if(PHP_VERSION >= '4.3.0'){
				$data = file_get_contents($this->_dir.$this->_id);
			}else{
				$handle = fopen($this->_dir.$this->_id,'rb');
				$data = fread($handle,filesize($this->_dir.$this->_id));
				fclose($handle);
			}
			return $data;
		}else{
			return false;
		}
	}
	function save($data){
		if(!is_writable($this->_dir)){
			if(!@mkdir($this->_dir,0777,true)){
				echo 'Cache directory not writable';
				exit;
			}
		}
		if(PHP_VERSION >= '5'){
			file_put_contents($this->_dir.$this->_id,$data);
		}else{
			$handle = fopen($this->_dir.$this->_id,'wb');
			fwrite($handle,$data);
			fclose($handle);
		}
		return true;
	}
	function start($id){
		$data = $this->get($id);
		if($data !== false && app_cache==1){
			echo($data);
			return true;
		}
		ob_start();
		ob_implicit_flush(false);
		return false;
	}
	function end(){
		$data = ob_get_contents();
		ob_end_clean();
		if(app_cache==1) $this->save($data);
		echo($data);
	}
}

class AppFtp{
var $ftpUrl = "127.0.0.1";
var $ftpUser = "maccms";
var $ftpPass = "123456";
var $ftpDir = "/wwwroot/";
var $ftpPort = "21";
var $ftpR = ''; //R ftp资源;
var $ftpStatus = 0;
var $ftpStatusDes = "";
//R 1:成功;2:无法连接ftp; 3:用户错误;
function AppFtp($ftpUrl="", $ftpUser="", $ftpPass="",  $ftpPort="",  $ftpDir="") {
	if($ftpUrl){
		$this->ftpUrl=$ftpUrl;
	}
	if($ftpUser){
		$this->ftpUser=$ftpUser;
	}
	if($ftpPass){
		$this->ftpPass=$ftpPass;
	}
	if($ftpUrl){
		$this->ftpDir=$ftpDir;
	}
	if($ftpPort){
		$this->ftpPost=$ftpPort;
	}
   if ($this->ftpR = ftp_connect($this->ftpUrl, $this->ftpPost)) {
     if (ftp_login($this->ftpR, $this->ftpUser, $this->ftpPass)) {
		if (!empty($this->ftpDir)) {
			ftp_chdir($this->ftpR, $this->ftpDir);
		}
     	ftp_pasv($this->ftpR, true);
     	$this->ftpStatus = 1;
     	$this->ftpStatusDes = "连接ftp成功";
     }
     else {
     	$this->ftpStatus = 3;
     	$this->ftpStatusDes = "连接ftp功能，但用户或密码错误";
     }
   }
   else {
     $this->ftpStatus = 2;
     $this->ftpStatusDes = "连接ftp失败";
   }
}

//R 切换目录;
function cd($dir) {
   return ftp_chdir($this->ftpR, $dir);
}
//R 返回当前路劲;
function pwd() {
   return ftp_pwd($this->ftpR);
}
function mkdirs($path)
{
	$path_arr  = explode('/',$path);
	$file_name = array_pop($path_arr); 
	$path_div  = count($path_arr); 
	foreach($path_arr as $val)
	{
		if(@ftp_chdir($this->ftpR,$val) == FALSE)
		{
			$tmp = @ftp_mkdir($this->ftpR,$val);
			if($tmp == FALSE)
			{
				echo "目录创建失败，请检查权限及路径是否正确！";
				exit;
			}
			@ftp_chdir($this->ftpR,$val);
		}
	}
	for($i=1;$i<=$path_div;$i++)
	{
		@ftp_cdup($this->ftpR);
	}
}

//R 创建目录
function mkdir($directory) {
   return ftp_mkdir($this->ftpR,$directory);
}
//R 删除目录
function rmdir($directory) {
   return ftp_rmdir($this->ftpR,$directory);
}
//R 上传文件;
function put($localFile, $remoteFile = ''){
   if ($remoteFile == '') {
     $remoteFile = end(explode('/', $localFile));
   }
   $res = ftp_nb_put($this->ftpR, $remoteFile, $localFile, FTP_BINARY);
   while ($res == FTP_MOREDATA) {
     $res = ftp_nb_continue($this->ftpR);
   }
   if ($res == FTP_FINISHED) {
     return true;
   } elseif ($res == FTP_FAILED) {
     return false;
   }
}
//R 下载文件;
function get($remoteFile, $localFile = '') {
   if ($localFile == '') {
     $localFile = end(explode('/', $remoteFile));
   }
   if (ftp_get($this->ftpR, $localFile, $remoteFile, FTP_BINARY)) {
     $flag = true;
   } else {
     $flag = false;
   }
   return $flag;
}
//R 文件大小;
function size($file) {
   return ftp_size($this->ftpR, $file);
}
//R 文件是否存在;
function isFile($file) {
   if ($this->size($file) >= 0) {
     return true;
   } else {
     return false;
   }
}
//R 文件时间
function fileTime($file) {
   return ftp_mdtm($this->ftpR, $file);
}
//R 删除文件;
function unlink($file) {
   return ftp_delete($this->ftpR, $file);
}
function nlist($dir = '/service/resource/') {
   return ftp_nlist($this->ftpR, $dir);
}
//R 关闭连接;
function bye() {
   return ftp_close($this->ftpR);
}
}
?>