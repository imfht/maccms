<?php
require_once ("../inc/conn.php");
require_once ("../inc/360_safe3.php");
require_once ("qqconfig.php");
require_once ("buyconfig.php");
require_once ("qqconnect.php");

if (app_user == 0){ echo "会员系统关闭中";exit;}

$action = be("get","action");
$u_points=0;
$u_plays="";
$u_downs="";
$u_fav="";



switch($action)
{
	case "login" : userhead();login();foot();break;
	case "check" : checkLogin();break;
	case "logout" : logout();break;
	
	case "regcheck":regcheck();break;
	case "reg" : userhead(); reg() ; foot();break;
	case "regsave" : regsave();break;
	
	case "findpass" : userhead(); findpass(); foot();break;
	case "findpasssave" : findpasssave();break;
	
	case "info": chklogin(); userhead(); info(); foot();break;
	case "save": chklogin(); save();break;
	case "popedom" : chklogin();userhead(); popedom();foot();break;
	
	case "plays": chklogin(); userhead(); plays(); foot();break;
	case "playsdel" :  chklogin(); playsdel();break;
	
	case "downs": chklogin(); userhead(); downs(); foot();break;
	case "downsdel" :  chklogin(); downsdel();break;
	
	case "fav" : chklogin(); userhead(); fav(); foot();break;
	case "favdel" : chklogin(); favdel();break;
	case "tg" : tg();break;
	case "upgrade": chklogin(); userhead(); upgrade(); foot();break;
	case "upgradesave": chklogin(); upgradesave();break;
	case "upgradesave2": chklogin(); upgradesave2();break;
	
	case "pay": chklogin(); userhead(); pay();foot();break;
	case "pay2": chklogin(); userhead(); pay2();foot();break;
	case "paysave": chklogin(); paysave();break;
	case "paysave2": chklogin(); paysave2();break;
	case "wel": chklogin(); userhead(); wel(); foot();break;
	default :  chklogin(); userhead(); main(); foot();break;
}
dispseObj();

function chklogin()
{
	global $db,$u_plays,$u_downs,$u_fav,$u_points;
	$u_id = $_SESSION["userid"];
	
	if (!isN($u_id)){
		$row = $db->getRow("SELECT * FROM {pre}user where u_id=" . $u_id);
		$loginValidate = md5($row["u_random"] . $row["u_name"] . $row["u_id"]);
		
		if ($row && $_SESSION["usercheck"] != $loginValidate){
			$_SESSION["userid"] = "";
			$_SESSION["usergourp"] ="";
			$_SESSION["username"] = "";
			$_SESSION["usercheck"] ="";
	    	echo "<script>top.location.href='index.php?action=login';</script>";
	    	exit;
	    }
		$u_points=$row["u_points"];
		$u_plays=$row["u_plays"];
		$u_downs=$row["u_downs"];
		$u_fav=$row["u_fav"];
		unset($row);
	}
	else{
		echo "<script>top.location.href='index.php?action=login';</script>";
	}
}

function checkLogin()
{
	global $db;
	$u_name = be("post","u_name"); $u_name= chkSql($u_name,true);
	$u_password = md5(be("post","u_password"));
	$flag = be("all","flag"); $backurl = be("all","backurl");
	if (isN($flag)){ $flag = "iframe";}
	
	$row = $db->getRow("SELECT u_id, u_name, u_qq, u_email,u_regtime,u_status,u_points,u_tj, u_loginnum, u_logintime,u_ip,u_random,u_flag,u_start,u_end,ug_id,u_group,ug_name,ug_type,ug_popedom,ug_popvalue FROM ({pre}user LEFT OUTER JOIN {pre}user_group ON {pre}user.u_group = {pre}user_group.ug_id) where u_name='".$u_name."' and u_password = '".$u_password."' and u_status =1");
	
	if (!$row){
		if ($flag=="iframe"){
			alertUrl ("您输入的用户名和密码不正确或者您的账户已经被锁定!","login.php");
		}
		else{
			alertUrl ("您输入的用户名和密码不正确或者您的账户已经被锁定!","?action=login&url=".$backurl);
		}
	}
	else{
		$randnum = md5(rand(1,99999999));
		$u_flag = $row["u_flag"];
		
		if ($u_flag == 1){
			if ( time() > strtotime($row["u_end"]) ){
				$u_flag = 0;
				$u_start="";
				$u_end="";
			}
			else{
				$u_start=$row["u_start"];
				$u_end=$row["u_end"];
			}
		}
		else{
			$ugroup=$row["u_group"];
		}
		
		
		$_SESSION["userlastlogintime"] = $row["u_logintime"];
		$_SESSION["userid"] = $row["u_id"];
		$_SESSION["username"] = $row["u_name"];
		$_SESSION["usergroup"] = $row["u_group"];
		$_SESSION["ugpopvalue"] = $row["ug_popvalue"];
		$_SESSION["usercheck"] = md5($randnum . $row["u_name"] . $row["u_id"]);
		
		
		$db->Update ("{pre}user",array("u_logintime","u_ip","u_random","u_loginnum","u_flag","u_start","u_end"),array(date('Y-m-d H:i:s'),getIP(),$randnum,$row["u_loginnum"] + 1,$u_flag,$u_start,$u_end),"u_id=".$row["u_id"]);
		unset($row);
		
		if (isN($backurl)){
			if ($flag=="iframe"){
				echo "<script>window.location.href='login.php';</script>";
			}
			else{
				echo "<script>top.location.href='index.php';</script>";
			}
		}
		else{
			echo "<script>top.location.href='".$backurl ."';</script>";
		}
	}
}

function logout()
{
	$flag = be("all","flag");
	if (isN($flag)){ $flag = "iframe";}
	$_SESSION["userid"] = "";
	$_SESSION["usergroup"]="";
	$_SESSION["username"] = "";
	$_SESSION["usercheck"]="";
	$_SESSION["ugpopvalue"]="";
	$_SESSION["userlastlogintime"]="";
	if ($flag=="iframe"){
		echo "<script>window.location.href='login.php';</script>";
	}
	else{
		echo "<script>top.location.href='index.php?action=login';</script>";
	}
}

function regcheck()
{
	global $db;
	$str=be("all","str"); $str=chkSql($str,true);
	$typea=be("all","typea"); $typea=chkSql($typea,true);
	$okstr = "{\"result\":true}";
	$errstr = "{\"result\":false}";
	switch($typea)
	{
		case "u_name": $where = "u_name='" .$str ."'";break;
		case "u_email": $where = "u_email='" . $str ."'";break;
		case "u_qq": $where = "u_qq='" . $str ."'";break;
		case "verifycode": 
			if ($_SESSION["code_userreg"] != $str){
				echo $errstr;exit;
			}
			else{
				echo $okstr;exit;
			}
		default : $where="";break;
	}
	
	$row = $db->getRow("SELECT * FROM {pre}user WHERE " . $where);
	if(!$row) { $str= $okstr;} else { $str= $errstr;}
	
	unset($row);
	echo $str;
}



function reg()
{
	global $db;
	if (app_reg == 0){ echo "系统已经关闭注册";exit;}
	
	$ref= be("all","ref");
	if ($ref=="qqlogin"){
		$qc = new QqConnect();
		$url = $qc->create_login_url();
	    echo '<script type="text/javascript">location.href="' .$url .'";</script>';
		unset($qc);
		exit;
	}
	else if ($ref=="qqlogged"){
		$qc = new QqConnect();
		
		if($qc->checkLogin()){
			$qc->callback();
			$qqid = $qc->get_openid();
			$userinfo = $qc->get_user_info();
			$nickname = $userinfo["nickname"]; $nickname=chkSql($nickname,true); $nickname=replaceStr($nickname,"'","");
			$tmpname = $nickname;
			
			$i=0;
			$rscount = $db->getOne("SELECT count(*) FROM {pre}user where u_qid='" . $qqid . "'");
			if ($rscount == 0){
				$rscount = $db->getOne("SELECT count(*) FROM {pre}user where u_name='" . $tmpname . "'");
				
				while ($rscount>0)
				{
					$tmpname = $nickname . $i;
					$rscount = $db->getOne("SELECT count(*) FROM {pre}user where u_name='" . $tmpname . "'");
					$i++;
				}
				$nickname = $tmpname;
				$db->Add( "{pre}user",array("u_name","u_qid", "u_password","u_qq","u_email","u_regtime","u_status","u_points","u_group","u_phone","u_question","u_answer"),array($nickname,$qqid,md5(""),"","",date('Y-m-d H:i:s'),app_regstate,app_regpoint,app_reggroup,"","",""));
			}
					    
			$row = $db->getRow("SELECT u_id, u_qid,u_name, u_qq, u_email,u_regtime,u_status,u_points,u_tj, u_loginnum, u_logintime,u_ip,u_random,u_flag,u_start,u_end,ug_id,u_group,ug_name,ug_type,ug_popedom,ug_popvalue FROM ({pre}user LEFT OUTER JOIN {pre}user_group ON {pre}user.u_group = {pre}user_group.ug_id) where u_qid='".$qqid."' and u_status =1");
			
			if ($row){
				$randnum = md5(rand(1,99999999));
				if ($row["u_flag"] == 1){
					if ( time() > strtotime($row["u_end"]) ){
						$u_flag = app_reggroup;
						$u_start="";
						$u_end="";
					}
					else{ 
						$u_flag = $row["u_flag"];
						$u_start=$row["u_start"];
						$u_end=$row["u_end"];
					}
				}
				else{
					$ugroup=$row["u_group"];
				}
				$db->Update ("{pre}user",array("u_logintime","u_ip","u_random","u_loginnum","u_flag","u_start","u_end"),array(date('Y-m-d H:i:s'),getIP(),$randnum,$row["u_loginnum"] + 1,$u_flag,$u_start,$u_end),"u_id=".$row["u_id"]);
							
				$_SESSION["userid"] = $row["u_id"];
				$_SESSION["username"] = $row["u_name"];
				$_SESSION["usergroup"] = $row["u_group"];
				$_SESSION["ugpopvalue"] = $row["ug_popvalue"];
				$_SESSION["usercheck"] = md5($randnum . $row["u_name"] . $row["u_id"]);
			}
			unset($row);
			if ($randnum !=""){ echo '<script type="text/javascript">location.href="' .app_installdir .'";</script>';exit;  }
		}
		unset($qc);
	}
	else{
		
	}
?>
<script type="text/javascript">
function remote_check(typea, str){
  var url="?action=regcheck";
  var ret;
  $.ajax({'url':url,'async':false,'dataType':'json','data':{'typea':typea,'str':str},'success':function(data){ret=data;}});
  return ret.result;
}
var validator={
'u_name':[
  [/\S+/, '请输入用户名'],
  [/\S{4,}/, '用户名少于4位'],
  [function(u_name){return remote_check('u_name',u_name);}, '此用户名已被使用']
],
'u_password1':[
  [/^.+$/, '请输入密码'],
  [/^.{6,}$/, '密码少于6位']
],
'u_password2':[
  [/^.+$/, '请输入确认密码'],
  [function(s){return s==$('#item_u_password1 input').val();}, '两次密码输入不一致']
],
'u_email':[
  [/\S+/, '请输入电子邮件'],
  [/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i, '请输入格式正确的电子邮件'],
  [function(u_email){return remote_check('u_email',u_email)}, '此电子邮件已被使用']
],
'u_qq':[
  [/\S+/, '请输入QQ号码'],
  [/^[\d|\.|,]+$/, '请输入格式正确的QQ号码'],
  [function(u_qq){return remote_check('u_qq',u_qq)}, '此QQ号码已被使用']
],
'u_phone':[
  [/\S+/, '请输入电话号码'],
  [/^[\d|\.|,]+$/, '请输入格式正确的电话号码']
],
'verifycode':[
  [/\S+/,'请输入验证码'],
  [function(verifycode){return remote_check('verifycode',verifycode)}, '验证码不正确']
]
};

function validate(item) {
  var str=$("#item_"+item+" input").val();
  var m={
    'RegExp':function(r,s){
      return r.test(s);
    },
    'Function':function(f,s){
      return f(s);
    },
    'String':function(v,s){return v==s;}
  };
  for (var v in validator[item]) {
    var vi=validator[item][v];
    var c=Object.prototype.toString.apply(vi[0]).match(/(\w+)\]$/)[1];
    if (m[c] && !m[c](vi[0],str)) {
      fail(item,vi[1]);
      return false;
    }
  }
  succ(item);
  return true;
}

var result={};
for (var k in validator){
	result[k]=false;
}

function fail(item, msg){
	$("#item_"+item+" .f3 span").html(msg).removeClass('valid').addClass('fail');
	result[item]=false;
	disable();
}
function succ(item) {
	$("#item_"+item+" .f3 span").html('&nbsp;').removeClass('fail').addClass('valid');
	result[item]=true;
	check_all();
}
function check_all() {
	for (var k in result){
	  if (result[k]==false){
	    disable();
	    return true;
	  }
	}
	if (!$('#agree').attr('checked')) {
	  disable();
	  return true;
	}
	enable();
	return true;
}
function enable(){
	$('#submit_enabled').show();
	$('#submit_disabled').hide();
	return true;
}
function disable(){
	$('#submit_enabled').hide();
	$('#submit_disabled').show();
	return false;
}
function check_and_submit() {
	if (!check_all()) {
	return;
	}
	$('form')[0].submit();
}
$(document).ready(function(){
	$('#header').append('<div class="subNavi"><a href="?action=login">登录</a> <a href="../">返回首页</a> </div>');
});
</script>
<div id="outterWrapper">
<div id="container">
	<div id="header">
    	<h1 class="bannerReg">注册我的通行证</h1>
    </div>
    <div id="content">
    	<div id="main">
		<div id="sideMain">
			<div id="regForm">
			<form action="?action=regsave" method="post">
				<ul>
					<li id="item_u_name">
						<div class="f1"><strong>用户名</strong></div>
						<div class="f2"><input type="text" class="text" maxlength="16" name="u_name" /></div>
						<div class="f3"><span></span></div>
						<div class="f4">由4-16位任意字符组成</div>
					</li>
					<li id="item_u_password1">
						<div class="f1"><strong>密　码</strong></div>
						<div class="f2"><input type="password" class="text" maxlength="16" name="u_password1"  /></div>
						<div class="f3"><span></span></div>
						<div class="f4">由6-16位字符组成</div>
					</li>
					<li id="item_u_password2">
						<div class="f1"><strong>确认密码</strong></div>
						<div class="f2"><input type="password" class="text" maxlength="16" name="u_password2" /></div>
						<div class="f3"><span></span></div>
						<div class="f4">由6-16位字符组成</div>
					</li>
					<li id="item_u_email">
						<div class="f1"><strong>电子邮件</strong></div>
						<div class="f2"><input type="text" class="text" maxlength="32" name="u_email" /></div>
						<div class="f3"><span></span></div>
						<div class="f4">请填写有效电子邮件，以获得注册验证信</div>
					</li>
					<li id="item_u_qq">
						<div class="f1"><strong>QQ号码</strong></div>
						<div class="f2"><input type="text" class="text" maxlength="16" name="u_qq" /></div>
						<div class="f3"><span></span></div>
						<div class="f4">请填写有效QQ号码，可以通过QQ号码登录</div>
					</li>
					<li id="item_u_phone">
						<div class="f1"><strong>电话号码</strong></div>
						<div class="f2"><input type="text" class="text" maxlength="16" name="u_phone" /></div>
						<div class="f3"><span></span></div>
						<div class="f4">请填写有效电话号码</div>
					</li>
					<li id="item_u_question">
						<div class="f1"><strong>找回问题</strong></div>
						<div class="f2"><input type="text" class="text" maxlength="255" name="u_question" /></div>
						<div class="f3"><span></span></div>
						<div class="f4">请认真填写问题，以便日后找回密码</div>
					</li>
					<li id="item_u_answer">
						<div class="f1"><strong>找回答案</strong></div>
						<div class="f2"><input type="text" class="text" maxlength="255" name="u_answer" /></div>
						<div class="f3"><span></span></div>
						<div class="f4">请认真填写答案，以便日后找回密码</div>
					</li>
					
					<li class="validCode" id="item_verifycode">
						<div class="f1"><strong>验证码</strong></div>
						<div class="f2"><div>&nbsp;&nbsp;<img src="../inc/code.php?a=userreg&s='Math.random()" title="看不清楚? 换一张！" style="cursor:hand;" onClick="src='../inc/code.php?a=userreg&s='+Math.random()"/></div></div>
						<div class="clear"></div>
						<div class="f5">
						  <input type="text" class="text" maxlength="4" name="verifycode" />
						</div>
						<div class="f3"><span></span></div>
						<div class="f4"></div>
					</li>
					<li class="agreement" id="item_agree">
						<div class="f4"><input name="agree" id="agree" type="checkbox" value="1" checked="checked" class="checkbox" /> <span>我已阅读并同意</span></div>
					</li>
				</ul>
				<div class="button">
					<img id="submit_enabled" onclick="check_and_submit();" src="../images/user/btn_reg.gif" title="同意协议并注册" style="cursor:pointer;display:none;"/>
					<img id="submit_disabled" src="../images/user/btn_reg_invalid.gif" title="同意协议并注册"/>
				</div>
			</form>
			</div>
		</div>
<script type="text/javascript">
$('#regForm input')
.blur(function(){validate(this.name);})
.change(check_all);
</script>
<div id="sideSub">
		<div class="subNotice02">
		<p>注册通行证可享会员服务</p>
		<p>收费影片</p>
		<p>会员影片</p>
		<p>特殊影片</p>
		<p>让您看片、交友、互动，一个都不少。</p>
		</div>
		<div class="toReg">
			<a href="?action=reg" title="还没有通行证？立即注册 &raquo;"><img src="../images/user/toreg.png" width="210" height="21" alt="还没有通行证？立即注册 &raquo;" /></a>
		</div>
	</div>            <div class="clear"></div>
        </div>
    </div>
    <div class="footer">
		<div class="link_bk">&copy; 2008-2013 <a href="http://www.maccms.com/" target="_blank">MACCMS</a> Inc. Powered by 苹果电影程序</div>
	</div>
</div>
</div>
<?php
}

function regsave()
{
	global $db;
	if (app_reg == 0){ echo "系统已经关闭注册";exit;}
	$verifycode = be("post","verifycode");
	if ($verifycode ==""){ alert ("请返回输入确认码。返回后请刷新登陆页面后重新输入正确的信息。");exit;}
	if (trim($verifycode) != $_SESSION["code_userreg"]) { alert ("确认码错误，请重新输入。返回后请刷新登陆页面后重新输入正确的信息。") ;exit;}
	
	$u_name = be("post","u_name"); $u_name=chkSql($u_name,true);
	$u_password1 = be("post","u_password1"); $u_password1=chkSql($u_password1,true);
	$u_password2 = be("post","u_password2"); $u_password2=chkSql($u_password2,true);
	$u_qq = be("post","u_qq"); $u_qq=chkSql($u_qq,true);
	$u_email = be("post","u_email"); $u_email=chkSql($u_email,true);
	$u_phone = be("post","u_phone"); $u_phone=chkSql($u_phone,true);
	$u_question = be("post","u_question"); $u_question=chkSql($u_question,true);
	$u_answer = be("post","u_answer"); $u_answer=chkSql($u_answer,true);
	
	
	if ($u_password1 != $u_password2){ alert ("两次密码不同");exit;}
	if (strlen($u_name) >32) { $u_name= substring($u_name,32);}
	if (strlen($u_password1) >32) { $u_password1=substring($u_password1,32);}
	if (strlen($u_email) > 32) { $u_email=substring($u_email,32);}
	if (strlen($u_qq) > 16) { $u_qq=substring($u_qq,16);}
	if (strlen($u_phone) >16) { $u_phone=substring($u_phone,16);}
	if (strlen($u_question) >255) { $u_question=substring($u_question,255);}
	if (strlen($u_answer) >255) { $u_answer=substring($u_answer,255);}
	if (!isNum($u_qq)){ alert ("QQ号码格式不正确"); exit;}
	if (!isNum($u_phone)){ alert ("电话号码格式不正确"); exit;}
	
	$u_password1 = md5($u_password1);
	$u_name = strip_tags($u_name);
	
	$row = $db->getRow("SELECT * FROM {pre}user WHERE u_name='".$u_name."'");
	if (!$row){
		 $db->Add  ("{pre}user",array("u_name", "u_password","u_qq","u_email","u_regtime","u_status","u_points","u_group","u_phone","u_question","u_answer"),array($u_name,$u_password1,$u_qq,$u_email,date('Y-m-d H:i:s'),app_regstate,app_regpoint,app_reggroup,$u_phone,$u_question,$u_answer));
		alertUrl ("注册成功,正在转向登录页面","index.php?action=login"); exit;
	}
	else{
		alert ("注册失败,该用户名已经被使用" );exit;
	}
	unset($row);
}

function findpass()
{
?>
<div id="outterWrapper">
<div id="container">
<div id="header">
    	<h1 class="bannerFindPass">找回密码</h1>
</div>
    <div id="content">
    	<div id="main">
            <div id="sideMain">
<div id="pass">
<form method="post" action="?action=findpasssave">
<p class="notice">填写信息并提交后，将自动重设密码</p>
<p class="findPass"><span class="f1">用户名</span><span class="f2">
<input type="text" class="text" name="u_name" maxlength="32" /></span></p>
<p class="findPass"><span class="f1">找回问题</span><span class="f2">
<input type="text" class="text" name="u_question" maxlength="255" /></span></p>
<p class="findPass"><span class="f1">找回答案</span><span class="f2">
<input type="text" class="text" name="u_answer" maxlength="255" /></span></p>
<p class="findPass"><span class="f1">新密码</span><span class="f2">
<input type="text" class="text" name="u_password" maxlength="32" /></span></p>
<p class="button"><input type="submit" value="提交" class="button" /></p>
</form>
</div>
<div id="solution">
<p>如果需要帮助可发送邮件至 <a href="mailto:<?php echo app_email?>"><?php echo app_email?></a></p>
<p>您也可以加QQ:<?php echo app_qq?>为好友与我联系</p>
</div>
</div>
<div id="sideSub">
		<div class="subNotice02">
		<p>注册通行证可享会员服务</p>
		<p>收费影片</p>
		<p>会员影片</p>
		<p>特殊影片</p>
		<p>让您看片、交友、互动，一个都不少。</p>
		</div>
		<div class="toReg">
			<a href="?action=reg" title="还没有通行证？立即注册 &raquo;"><img src="../images/user/toreg.png" width="210" height="21" alt="还没有通行证？立即注册 &raquo;" /></a>
		</div>
            <div class="clear"></div>
        </div>
    </div>
   <div class="footer">
	<div class="link_bk">&copy; 2008-2013 <a href="http://www.maccms.com/" target="_blank">MACCMS</a> Inc. Powered by 苹果电影程序</div>
	</div>
</div>
</div>
<?php
}

function findpasssave()
{
	global $db;
	$u_name = be("post","u_name"); $u_name=chkSql($u_name,true);
	$u_password = be("post","u_password"); $u_password=chkSql($u_password,true);
	$u_email = be("post","u_email"); $u_email=chkSql($u_email,true);
	$u_question = be("post","u_question"); $u_question=chkSql($u_question,true);
	$u_answer = be("post","u_answer"); $u_answer=chkSql($u_answer,true);
	
	if (strlen($u_name) >32) { $u_name= substring($u_name,32);}
	if (strlen($u_password) >32) { $u_password=substring($u_password,32);}
	if (strlen($u_question) >255) { $u_question=substring($u_question,255);}
	if (strlen($u_answer) >255) { $u_answer=substring($u_answer,255);}
	$u_password = md5($u_password);
	if(isN($u_question) || isN($u_answer) || isN($u_password) || isN($u_name)){
		alert ("表单信息不完整,请重填!"); exit;
	}
	
	$row = $db->getRow("SELECT * FROM {pre}user WHERE u_name='".$u_name."'");
	if (!$row){
		alert ("重置密码失败"); exit;
	}
	else{
		if ($u_question != $row["u_question"] || $u_answer != $row["u_answer"]){ alert ("重置密码失败");exit;}
		$db->Update ("{pre}user",array("u_password"),array($u_password),"u_id = ". $row["u_id"]);
		alertUrl ("重置密码成功,正在转向登录页面","index.php?action=login"); exit;
	}
	unset($row);
}

function tg()
{
	global $db;
	$userid = be("get","uid"); $userid=chkSql($userid,true);
	
	if (!chkGlobalCache("tjlastdate")){	setGlobalCache ("tjlastdate", date('Y-m-d'),0); }
	if (isNum($userid)){
		$ip = getIP(); $ip = chkSql($ip,true);
		$ly=  getReferer(); $ly = chkSql($ly,true);
		$row = $db->getRow("select * from {pre}user where u_id=" . $userid .""); 
		if ($row){
			$sql="Select * From {pre}user_visit where uv_uid = " .$userid." and uv_ip ='".$ip."' and STR_TO_DATE(uv_time,'%Y-%m-%d')='".date("Y-m-d")."'";
			
			$row1 = $db->getRow($sql);
			if (!$row1){
				$db->Add ("{pre}user_visit",array("uv_uid","uv_ip","uv_ly","uv_time"), array($userid,$ip,$ly, date("Y-m-d H:i:s")));
				$db->query ("update {pre}user set u_tj=u_tj+1,u_points=u_points+" . app_popularize . " where u_id=". $userid);
				if ( strpos( ",". date('Y-m-d H:i:s',time()), getGlobalCache("tjlastdate") ) <=0 ) {
					 $sql="delete from {pre}user_visit whereSTR_TO_DATE(uv_time,'%Y-%m-%d')<'".date("Y-m-d")."'";
					 $db->query($sql);
					 setGlobalCache ("tjlastdate", date('Y-m-d') , 0);
				}
			}
			unset($row1);
		}
		unset($row);
	}
	redirect ("../");
}

function save()
{
	global $db;
	$oldpass = be("post","u_oldpass"); $oldpass=chkSql($oldpass,true);
	$password1 = be("post","u_password1"); $password1=chkSql($password1,true);
	$password2 = be("post","u_password2"); $password2=chkSql($password2,true);
	$u_qq= be("post","u_qq"); $u_qq=chkSql($u_qq,true);
	$u_email = be("post","u_email"); $u_email=chkSql($u_email,true);
	
	if (strlen($u_email)>32) { $u_email = substring($u_email,32);}
	if (strlen($u_qq)>16) { $u_qq = substring($u_qq,16);}
	if (strlen($password1)>32) { $password1 = substring($password1,32);}
	
	$row = $db->getRow("select * from {pre}user where u_id=" . $_SESSION["userid"]);
	if(!$row){
		alert ("非法访问"); exit;
	}
	else{
		if ($row["u_password"] != md5($oldpass)){
			alert ("旧密码不正确"); exit;
		}
	}
	unset($row);
	if ($password1 != ""){
		if ($password1 != $password2){ alert ("两次密码不同");exit; }
		$password1 = md5($password1);
		$db->Update ("{pre}user",array("u_qq","u_email","u_password") ,array($u_qq,$u_email,$password1) ,"u_id = ". $_SESSION["userid"]);
	}
	else{
		$db->Update ("{pre}user",array("u_qq","u_email") ,array($u_qq,$u_email) ,"u_id = ". $_SESSION["userid"]);
	}
	alertUrl ("修改成功！","?action=wel");
}

function upgradesave()
{
	global $db,$u_points;
	$NewU_Group = be("post","u_group");
	$NewU_Group = chkSql($NewU_Group,true);
	$upgradearr = explode("_",$NewU_Group);
	
	if (intval($_SESSION["ugpopvalue"]) > intval($upgradearr[2])){ alert ("您现在的会员组比目标会员组权限高，不需要升级!");exit;}
	if ($_SESSION["usergroup"] == intval($upgradearr[0])){ alert ("您已经是该会员组成员无需升级!"); exit;}
	if ($u_points < intval($upgradearr[1])) { alert ("您的积分不够，无法升级到该会员组!"); exit;}
	$db->query( "UPDATE {pre}user set u_points=u_points-".$upgradearr[1] . ",u_group=".$upgradearr[0] . "  WHERE u_id =". $_SESSION["userid"]);
	$_SESSION["ugpopvalue"] = $upgradearr[2];
	alertUrl ("升级成功,请重新登陆！","?action=wel");
}

function upgradesave2()
{
	global $db,$u_points;
	$baoshi = be("post","baoshi");
	if(!isNum($baoshi)){ $baoshi=0; } else { $baoshi=intval($baoshi); }
	switch($baoshi)
	{
		case 1:
			$baoshi = app_weekpoint; $dn=7;
			break;
		case 2:
			$baoshi = app_monthpoint; $dn=30;
			break;
		case 3;
			$baoshi = app_yearpoint; $dn=365;
			break;
		default :
			alertUrl ("非法请求，请重试！",""); exit;
			break;
	}
	
	if ($u_points < $baoshi) { alert ("您的积分不够，无法包时长!"); exit;}
	$u_end = $db->getOne("select u_end from {pre}user where u_id=".$_SESSION["userid"]);
	//var_dump($u_end) ;exit;
	if(empty($u_end)){
		$u_end = date('Y-m-d H:i:s',time()+ 24*60*60*$dn );
	}
	else{
		$u_end = date('Y-m-d H:i:s',strtotime($u_end)+ 24*60*60*$dn );
	}
	$db->query( "UPDATE {pre}user set u_points=u_points-".$baoshi . ",u_flag=1,u_start='".date('Y-m-d H:i:s',time())."',u_end='". $u_end ."'  WHERE u_id =". $_SESSION["userid"]);
	alertUrl ("包时长成功,请重新登陆！","?action=wel");
}

function playsdel()
{
	global $db;
	$db->Update  ("{pre}user" ,array("u_plays"), array("") ,"u_id=". $_SESSION["userid"] );
	alerturl ("清空收费播放记录成功!","?action=plays");
}

function downsdel()
{
	global $db;
	$db->Update  ("{pre}user" ,array("u_downs"), array("") ,"u_id=". $_SESSION["userid"] );
	alerturl ("清空收费下载记录成功!","?action=downs");
}

function favdel()
{
	global $db,$u_fav;
	$d_id = be("arr","d_id"); $d_id=chkSql($d_id,true);
	
	if (!isN($d_id) ){
		$arr = explode(",",$d_id);
		for ($i=0;$i<count($arr);$i++){
			if (strpos(",".$u_fav,",".trim($arr[$i]).",") > 0){
				$u_fav = replaceStr($u_fav, trim($arr[$i]),"");
				$u_fav = replaceStr($u_fav,",,",",");
			}
		}
		$db->Update ( "{pre}user" , array("u_fav"), array($u_fav) ,"u_id=". $_SESSION["userid"] );
	}
	alerturl ("删除完毕!","?action=fav");
}

function userhead()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>用户中心 - <?php echo app_sitename?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=7">
    <link rel="stylesheet" type="text/css" href="../images/user.css" />
    <link rel="stylesheet" type="text/css" href="../images/icon.css" />
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/function.js"></script>
</head>
<body>
<?php
}

function foot()
{
?>
</body>
</html>
<?php
}

function main()
{
?>
<script type="text/javascript">
$(document).ready(function(){
	$(".left_menu_btop li").hover(
		function()
		{
			$(this).css("background-color","#E9FBFE");
			},
			function()
			{
			$(this).css("background-color","");
			}
		);
	$(".left_menu_bg").click(function(){
		var tb=$(this).next();
		var tb_display=tb.css("display");
		if (tb_display=="block")
		{
		tb.css("display","none");
		$(this).find("img").attr("src","../images/user/06.gif");
		}
		else
		{
		tb.css("display","block");
		$(this).find("img").attr("src","../images/user/07.gif");
		}
	});
});
</script>
<div class="nav_outer">
<div class="nav idTabs" id="tabshow">
	<div class="clear"></div>
</div>
	<div class="nav_bottom_left"></div>
	<div class="nav_bottom_center">
	  <div id="tab1" style="padding-left:8px;">
		<span style="color:#FFFFFF">欢迎您登录会员中心</span>
	  </div>
    </div>
	<div class="nav_bottom_right"></div>
	<div class="clear"></div>
</div>
<table width="960" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:6px;" >
  <tr>
    <td width="173" height="500" valign="top" class="link_bk">
	<div class="left_menu_box">
		<div class="left_menu_bg">
			<div class="left_menu_tit">数据管理</div>
			<div class="left_menu_img"><img src="../images/user/07.gif"  border="0" /></div>
			<div class="clear"></div>
		</div>
		<div class="left_menu_btop" style="height:450px;">
		<ul>
				<li><a target="ifu" href="?action=wel">会员信息</a></li>
				<li><a target="ifu" href="?action=info">修改信息</a></li>
				<li><a target="ifu" href="?action=popedom">我的权限</a></li>
				<li><a target="ifu" href="?action=plays">收费点播记录</a></li>
				<li><a target="ifu" href="?action=downs">收费下载记录</a></li>
				<li><a target="ifu" href="?action=fav">我的收藏</a></li>
				<li><a target="ifu" href="?action=upgrade">升级会员</a></li>
				<li><a target="ifu" href="?action=pay">充值卡充值</a></li>
				<li><a target="_blank" href="?action=pay2">在线充值</a></li>
				<li><a target="ifu" href="?action=logout&flag=main">退出</a></li>
				</ul>
		</div>
	</div>
	
	</td>
    <td valign="top" height="500">
       <iframe id="ifu" name="ifu" scrolling="auto" frameborder="0"  src="?action=wel" style="width:100%;height:100%;"></iframe>
    </td>
  </tr>
</table>
<div class="footer">
<div class="link_bk">&copy; 2008-2013 <a href="http://www.maccms.com/" target="_blank">MACCMS</a> Inc. Powered by 苹果电影程序</div>
</div>
<?php
}

function login()
{
?>
<script type="text/javascript">
$(document).ready(function(){	
	$("body").bind('keyup',function(event) {
		if(event.keyCode==13){
			$('#btnLogin').click();
		}
	}); 
	$('#btnLogin').click(function() {
		var u_name = $('#u_name').val();
		var u_password = $('#u_password').val();
		if (u_name == '') {
			alert('请输入用户！');
			$("#u_name").focus();
			return false;
		}
		if (u_password  == '') {
			alert('请输入密码！');
			$("#u_password").focus();
			return false;
		}
		$("#form1").submit();
	});
	$('#header').append('<div class="subNavi"><a href="?action=reg">注册</a> <a href="../">返回首页</a> </div>');
});
</script>
<div id="outterWrapper">
<div id="container">
<div id="header">
	<h1 class="bannerLogin">登录通行证</h1>
</div>
<div id="content">
	<div id="main">
		<div id="sideMain">
			<div id="loginFrame">
			<form id="form1" method="post" action="?action=check">
			<input id="flag" name="flag" type="hidden" value="center">
			<ul>
			<li>
			<div class="f1">用户名</div>
			<div class="f2"><input type="text" class="text" maxlength="16" name="u_name" id="u_name" /></div>
			</li>
			<li>
			<div class="f1">密　码</div>
			<div class="f2"><input type="password" class="text" maxlength="16" name="u_password" id="u_password" /></div>
			</li>
			<li class="remember">
			<div class="f2"><span class=""><label><input name="remember" type="checkbox" value="1" class="checkbox" /> 记住我</label></span> <a href="?action=findpass">找回密码</a> </div>
			</li>
			    </ul>
			    <div class="login"><input type="image" id="btnLogin" src="../images/user/btn_login.gif" width="88" height="39" /></div>
			</form>
    		</div>
	</div>
	<div id="sideSub">
		<div class="subNotice02">
		<p>注册通行证可享会员服务</p>
		<p>收费影片</p>
		<p>会员影片</p>
		<p>特殊影片</p>
		<p>让您看片、交友、互动，一个都不少。</p>
		</div>
		<div class="toReg">
			<a href="?action=reg" title="还没有通行证？立即注册 &raquo;"><img src="../images/user/toreg.png" width="210" height="21" alt="还没有通行证？立即注册 &raquo;" /></a>
		</div>
	</div>
	<div class="clear"></div>
	</div>
	</div>
	<div id="footer">
	<div class="link_bk">&copy; 2008-2013 <a href="http://www.maccms.com/" target="_blank">MACCMS</a> Inc. Powered by 苹果电影程序</div>
	</div>
</div>
</div>
<?php
}

function wel()
{
	global $db;
	$row = $db->getRow("SELECT u_id, u_name, u_qq, u_email,u_regtime,u_status,u_points,u_tj, u_loginnum, u_logintime,u_ip,u_random,u_flag,u_start,u_end,ug_id,u_group,ug_name,ug_type,ug_popedom,ug_popvalue FROM ({pre}user LEFT OUTER JOIN {pre}user_group ON {pre}user.u_group = {pre}user_group.ug_id) where u_id=". $_SESSION["userid"]);

?>
<script>
$(document).ready(function(){
});
</script>
<div class="user_right_box">
		<div class="user_right_top_tit_bg">
		  <h1>个人资料</h1>
		</div>
		<form id="form1" name="form1" method="post" action="" >
			<table width="100%" border="0" cellpadding="4" cellspacing="0" style="margin-top:20px; margin-bottom:20px;" >
			<tr>
		        <td width="150" height="30" align="right">用户名：</td>
		        <td> <?php echo $row["u_name"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">所属会员组：</td>
		        <td> <?php echo $row["ug_name"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">计费类型：</td>
		        <td> <?php echo getUserFlag($row["u_flag"])?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">限制时长：</td>
		        <td> 从 <?php echo $row["u_start"]?> 到 <?php echo $row["u_end"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">QQ号码：</td>
		        <td> <?php echo $row["u_qq"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">Email地址：</td>
		        <td> <?php echo $row["u_email"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">注册时间：</td>
		        <td> <?php echo $row["u_regtime"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">上次登录IP：</td>
		        <td> <?php echo $row["u_ip"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">上次登录时间：</td>
		        <td> <?php echo $row["u_logintime"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">账户积分：</td>
		        <td> <?php echo $row["u_points"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">登录次数：</td>
		        <td> <?php echo $row["u_loginnum"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">推广次数：</td>
		        <td> <?php echo $row["u_tj"]?> </td>
		    </tr>
		    <tr>
		        <td width="150" height="30" align="right">推广链接：</td>
		        <td> <input id="page_url" name="page_url"  value="http://<?php echo app_siteurl?>/user/?action=tg&uid=<?php echo $_SESSION["userid"]?>" size="60" style="border: 1 solid #000000">&nbsp;
        <input type="button" name="Button" class="button1" value="复制推广连接" onClick="copyData(document.getElementById('page_url').value);"> </td>
		    </tr>
		    </table>
		</form>
</div>
<?php
	unset($row);
}

function info()
{
	global $db;
	$row = $db->getRow("SELECT u_id,u_qid, u_name, u_qq, u_email,u_regtime,u_status,u_points,u_tj, u_loginnum, u_logintime,u_ip,u_random,u_flag,u_start,u_end,ug_id,u_group,ug_name,ug_type,ug_popedom,ug_popvalue FROM ({pre}user LEFT OUTER JOIN {pre}user_group ON {pre}user.u_group = {pre}user_group.ug_id) where u_id=". $_SESSION["userid"] . "");
?>
<div class="user_right_box">
		<div class="user_right_top_tit_bg">
		  <h1>个人资料</h1>
		</div>
		<form id="form1" name="form1" method="post" action="?action=save" >
		<table width="100%" border="0" cellpadding="4" cellspacing="0" style="margin-top:20px; margin-bottom:20px;" >
			<tr>
			<td width="150" height="30" align="right">用户名：</td>
			<td><?php echo $row['u_name']?>
			</td>
			</tr>
			<tr>
			<td width="150" height="30" align="right">旧密码：</td>
			<td><input id="u_oldpass" size=20 value="" name="u_oldpass">
			</td>
			</tr>
			<tr>
			<td width="150" height="30" align="right">用户密码：</td>
			<td><input id="u_password1" size=20 value="" name="u_password1"> &nbsp;不修改请留空
			</td>
			</tr>
			<tr>
			<td width="150" height="30" align="right">确认密码：</td>
			<td><input id="u_password2" size=20 value="" name="u_password2" >
			</td>
			</tr>
			<tr>
			<td width="150" height="30" align="right">QQ号码：</td>
			<td>
			<input id="u_qq" size="20" value="<?php echo $row["u_qq"]?>" name="u_qq" >
			</td>
			</tr>
			<tr>
			<td width="150" height="30" align="right">邮件地址：</td>
			<td><input id="u_email" size="20" value="<?php echo $row["u_email"]?>" name="u_email" >
			</td>
			</tr>
		    <tr align="center">
		      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
		    </tr>
		</table>
		</form>
</div>
<?php
	unset($row);
}

function popedom()
{
	global $db;
?>
<div class="user_right_box">
		<div class="user_right_top_tit_bg">
		  <h1>我的权限</h1>
		</div>
<table width="100%" border="0" cellpadding="4" cellspacing="0" style="margin-top:20px; margin-bottom:20px;" >
<?php
	
	$i=0;
	$rs = $db->query( "select t_id,t_name from {pre}vod_type");
	while ($row = $db ->fetch_array($rs))
	{
		echo  "<tr><td width=\"150\" height=\"30\" align=\"right\"> ". $row["t_name"] . "</td>";
		echo "<td width=\"400\">";
		echo "浏览分类页 <img src='../images/icons/";
		if (getUserPopedom( $row["t_id"],"list")){ echo "ok.png";} else { echo "cancel.png"; }
		echo "' width=16 height=16 border=0 />      " ;
		      	  
		echo "浏览内容页 <img src='../images/icons/";
		if (getUserPopedom( $row["t_id"],"vod")){ echo "ok.png";} else { echo "cancel.png";}
		echo "' width=16 height=16 border=0 />      " ;
		      	  
		echo "浏览播放页 <img src='../images/icons/";
		if (getUserPopedom( $row["t_id"],"play")){ echo "ok.png";} else { echo "cancel.png";}
		echo "' width=16 height=16 border=0 />      " ;
		
		echo "浏览下载页 <img src='../images/icons/";
		if (getUserPopedom( $row["t_id"],"down")){ echo "ok.png";} else { echo "cancel.png";}
		echo "' width=16 height=16 border=0 />      " ;
		
		echo "</td></tr>";
		
		$i++;
	}
	unset($rs);
?>
</table>
</div>
<?php
}

function plays()
{
	global $db,$cache,$template,$u_plays;
?>
<script>
$(document).ready(function(){
	$("#btnDel").click(function(){
		if(confirm('确定要清空记录吗')){
			$("#form1").attr("action","index.php?action=playsdel");
			$("#form1").submit();
		}
		else{return false}
	});
});
function checkAll(val,objname){
	$("input[name='"+objname+"']").each(function() {
		this.checked = val;
	});
}
</script>
<div class="user_right_box">
	<div class="user_right_top_tit_bg">
	<h1>我的播放记录</h1>
	</div>
<form id="form1" name="form1" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="link_lan" id="list"  align="center">
<tr>
<td width="5%" height="26" class="us_list_title">&nbsp;</td>
<td class="us_list_title" >影片名称</td>
<td width="15%" class="us_list_title">影片分类</td>
<td width="15%" class="us_list_title">影片地址</td>
<td width="15%" class="us_list_title">消费积分</td>
</tr>
	<?php
	if (isN($u_plays)){
	?>
	<tr><td align="center" colspan="5">没有任何播放记录!</td></tr>
	<?php
	}
	else{
		$u_plays= "0".$u_plays;
		if (substring($u_plays, 1, strlen($u_plays)-1 ) == ","){ $u_plays = substring($u_plays, strlen($u_plays)-1 , 0); }
		$pagenum = be("all","page");
		if (!isNum($pagenum)) { $pagenum = 1;} else { $pagenum = intval($pagenum);}
		if ($pagenum < 1) { $pagenum = 1;}
		
		$rc=false;
		$playarr = explode(",",$u_plays);
		$u_plays = "";
		for ($i=1;$i<count($playarr);$i++){
			$arr1 = explode("-",$playarr[$i]);
			if (count($arr1)==3){
				if ($rc) { $u_plays = $u_plays . ","; }
				$u_plays = $u_plays . $arr1[0];
				$rc=true;
			}
		}
		
		$sql = "SELECT d_id,d_name,d_enname,d_type,d_stint,d_addtime FROM {pre}vod  where d_id in (". $u_plays.") order by d_addtime desc ";
		$sql .= " limit ".(30 * ($pagenum-1)) .",30";
		$rs = $db->query($sql);
		while ($row = $db ->fetch_array($rs))
		{
			$tname= "未知";
			$tenname="";
		  	$typearr = getValueByArray($cache[0], "t_id" ,$row["d_type"] );
			if(is_array($typearr)){
				$t_name= $typearr["t_name"];
				$t_enname= $typearr["t_enname"];
			}
	?>
	<tr>
		<td> </td>
		<td><?php echo $row["d_name"]?></td>
		<td><?php echo $t_name?></td>
		<td><a target="_blank" href="<?php echo $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$t_name,$t_enname)?>">浏览</a></td>
		<td><?php echo $row["d_stint"]?></td>
	</tr>
	<?php
		}
	}
	unset($rs);
	?>
	<tr>
	<td colspan="5">
    <input type="button" id="btnDel" value="清空记录" class="input">
	</td>
	</tr>
	</table>
	</form>
	</div>
<?php
}

function downs()
{
	global $db,$cache,$template,$u_downs;
?>
<script>
$(document).ready(function(){
	$("#btnDel").click(function(){
		if(confirm('确定要清空记录吗')){
			$("#form1").attr("action","index.php?action=downsdel");
			$("#form1").submit();
		}
		else{return false}
	});
});
function checkAll(val,objname){
	$("input[name='"+objname+"']").each(function() {
		this.checked = val;
	});
}
</script>
<div class="user_right_box">
	<div class="user_right_top_tit_bg">
	<h1>我的下载记录</h1>
	</div>
<form id="form1" name="form1" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="link_lan" id="list"  align="center">
<tr>
<td width="5%" height="26" class="us_list_title">&nbsp;</td>
<td class="us_list_title" >影片名称</td>
<td width="15%" class="us_list_title">影片分类</td>
<td width="15%" class="us_list_title">影片地址</td>
<td width="15%" class="us_list_title">消费积分</td>
</tr>
	<?php
	if (isN($u_downs)){
	?>
	<tr><td align="center" colspan="5">没有任何下载记录!</td></tr>
	<?php
	}
	else{
		$u_downs= "0".$u_downs;
		if (substring($u_downs, 1, strlen($u_downs)-1 ) == ","){ $u_downs = substring($u_downs, strlen($u_downs)-1 , 0); }
		$pagenum = be("all","page");
		if (!isNum($pagenum)) { $pagenum = 1;} else { $pagenum = intval($pagenum);}
		if ($pagenum < 1) { $pagenum = 1;}
		$rc=false;
		$playarr = explode(",",$u_downs);
		$u_downs = "";
		for ($i=1;$i<count($playarr);$i++){
			$arr1 = explode("-",$playarr[$i]);
			if (count($arr1)==3){
				if ($rc) { $u_downs = $u_downs . ","; }
				$u_downs = $u_downs . $arr1[0];
				$rc=true;
			}
		}
		
		$sql = "SELECT d_id,d_name,d_enname,d_type,d_stintdown,d_addtime FROM {pre}vod  where d_id in (". $u_downs.") order by d_addtime desc ";
		$sql .= " limit ".(30 * ($pagenum-1)) .",30";
		$rs = $db->query($sql);
		while ($row = $db ->fetch_array($rs))
		{
			$tname= "未知";
			$tenname="";
		  	$typearr = getValueByArray($cache[0], "t_id" ,$row["d_type"] );
			if(is_array($typearr)){
				$t_name= $typearr["t_name"];
				$t_enname= $typearr["t_enname"];
			}
	?>
	<tr>
		<td> </td>
		<td><?php echo $row["d_name"]?></td>
		<td><?php echo $t_name?></td>
		<td><a target="_blank" href="<?php echo $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$t_name,$t_enname)?>">浏览</a></td>
		<td><?php echo $row["d_stint"]?></td>
	</tr>
	<?php
		}
	}
	unset($rs);
	?>
	<tr>
	<td colspan="5">
    <input type="button" id="btnDel" value="清空记录" class="input">
	</td>
	</tr>
	</table>
	</form>
	</div>
<?php
}

function fav()
{
	global $db,$template,$cache,$u_fav;
?>
<script>
$(document).ready(function(){
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
		$("#form1").attr("action","index.php?action=favdel");
		$("#form1").submit();
		}
		else{return false}
	});
});
function checkAll(val,objname){
	$("input[name='"+objname+"']").each(function() {
		this.checked = val;
	});
}
</script>
<div class="user_right_box">
	<div class="user_right_top_tit_bg">
	<h1>我的收藏记录</h1>
	</div>
	<form id="form1" name="form1" method="post">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="link_lan" id="list"  align="center">
	<tr>
	<td width="5%" height="26" class="us_list_title">&nbsp;</td>
	<td class="us_list_title" >影片名称</td>
	<td width="15%" class="us_list_title">影片分类</td>
	<td width="15%" class="us_list_title">影片地址</td>
	<td width="15%" class="us_list_title">消费积分</td>
	</tr>
	<?php
	if (isN($u_fav)){
	?>
	<tr><td align="center" colspan="5">没有任何收藏记录!</td></tr>
	<?php
	}
	else{
		$u_fav= "0".$u_fav;
		if (substring($u_fav, 1, strlen($u_fav)-1 ) == ","){ $u_fav = substring($u_fav, strlen($u_fav)-1 , 0); }
		$pagenum = be("all","page");
		if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
		if ($pagenum < 1) { $pagenum = 1;}
		
		$sql = "SELECT d_id,d_name,d_enname,d_type,d_stint,d_addtime FROM {pre}vod  where d_id in (". $u_fav.") order by d_time desc ";
		$sql .= " limit ".(30 * ($pagenum-1)) .",30";
		$rs = $db->query($sql);
		
		while ($row = $db ->fetch_array($rs))
		{
			$tname= "未知";
			$tenname="";
		  	$typearr = getValueByArray($cache[0], "t_id" ,$row["d_type"] );
			if(is_array($typearr)){
				$t_name= $typearr["t_name"];
				$t_enname= $typearr["t_enname"];
			}
	?>
	<tr>
		<td><input name="d_id[]" type="checkbox" value="<?php echo $row["d_id"]?>"></td>
		<td><?php echo $row["d_name"]?></td>
		<td><?php echo $tname?>
		</td>
		<td><a target="_blank" href="<?php echo $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$t_name,$t_enname)?>">浏览</a></td>
		<td><?php echo $row["d_stint"]?></td>
	</tr>
	<?php
		}
	}
	unset($rs);
	?>
	<tr>
	<td colspan="5">
	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'d_id[]');"/>&nbsp;
    <input type="button" id="btnDel" value="批量删除" class="input">
	</td>
	</tr>
	</table>
	</form>
	<?php
}

function upgrade()
{
	global $db;
	$row = $db->getRow("SELECT u_id, u_name, u_qq, u_email,u_regtime,u_status,u_points,u_tj, u_loginnum, u_logintime,u_ip,u_random,u_flag,u_start,u_end,ug_id,u_group,ug_name,ug_type,ug_popedom,ug_popvalue FROM ({pre}user LEFT OUTER JOIN {pre}user_group ON {pre}user.u_group = {pre}user_group.ug_id) where u_id=". $_SESSION["userid"] ."");
?>
<div class="user_right_box">
	<div class="user_right_top_tit_bg"><h1>升级会员组</h1></div>
	<form action="?action=upgradesave" method="post" name="form3" id="form3">
	<table width="100%" border="0" cellpadding="4" cellspacing="0" style="margin-top:20px; margin-bottom:20px;" >
		<tr>
		<td width="150" height="30" align="right">当前会员组：</td>
		<td> <?php echo $row["ug_name"]?>
		</td>
		</tr>
		<tr>
		<td width="150" height="30" align="right">目标会员组：</td>
		<td><select id="u_group" name="u_group">
			<option value="">全部会员组</option>
            <?php
			$rsug = $db->query("select ug_id,ug_name,ug_upgrade,ug_popvalue FROM {pre}user_group");
			while ($row1 = $db ->fetch_array($rsug))
	    	{
				echo   "<option value='".$row1["ug_id"] .  "_". $row1["ug_upgrade"] ."_". $row1["ug_popvalue"] ."'>&nbsp;|—". $row1["ug_name"] ."(". $row1["ug_upgrade"] ."积分)</option>";
			}
			unset($rsug);
            ?>
            </select>
		</td>
		</tr>
	    <tr align="center">
	      <td colspan="2"><input class="input" type="submit" value="升级" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel1"> </td>
	    </tr>
	</table>
	</form>
	
	<div class="user_right_top_tit_bg"><h1>包时长</h1></div>
	<form action="?action=upgradesave2" method="post" name="form3" id="form3">
	<table width="100%" border="0" cellpadding="4" cellspacing="0" style="margin-top:20px; margin-bottom:20px;" >
		<tr>
		<td width="150" height="30" align="right">当前计费类型：<?php echo getUserFlag( $row["u_flag"])?> </td>
		<td>
		<?php
			if ($row["u_flag"]==1){
				echo "&nbsp;&nbsp;时间从：&nbsp;&nbsp;从" . $row["u_start"] . "到" . $row["u_end"];
			}
		?>
		</td>
		</tr>
		<tr>
		<td width="150" height="30" align="right">所需时长：</td>
		<td><select id="baoshi" name="baoshi">
            <option value="1">包周会员</option>
            <option value="2">包月会员</option>
            <option value="3">包年会员</option>
            </select>
		</td>
		</tr>
	    <tr align="center">
	      <td colspan="2"><input class="input" type="submit" value="升级" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel1"> </td>
	    </tr>
	</table>
	</form>
</div>
<?php
unset($rs);
}

function pay()
{
?>
<div class="user_right_box" style="margin-top:10px;">
	<div class="user_right_top_tit_bg"><h1>充值卡充值</h1></div>
	<form action="?action=paysave" method="post" name="form4" id="form4">
	<table width="100%" border="0" cellpadding="4" cellspacing="0" style="margin-top:20px; margin-bottom:20px;" >
		<tr>
		<td width="150" height="30" align="right">充值卡号码：</td>
		<td><input id="cardnum" size="20" value="" name="cardnum" ></td>
		</tr>
		<tr>
		<td width="150" height="30" align="right">充值卡密码：</td>
		<td><input id="cardpwd" size="20" value="" name="cardpwd" ></td>
		</tr>
	    <tr align="center">
	      <td colspan="2"><input class="input" type="submit" value="确认" id="btnSave"> </td>
	    </tr>
	</table>
	</form>
</div>
<?php
}

function pay2()
{
	$url = "http://" .$_SERVER["HTTP_HOST"] . "/user/";
?>
<script language="javascript">
$(document).ready(function(){
	$("#form5").validate({
		rules:{
			OrdAmt:{
				required:true,
				number:true,
				min:<?php echo app_buymin?>
			}
		}
	});
});
</script>
<div class="user_right_box" style="width:500px;margin:80px auto;">
	<div class="user_right_top_tit_bg"><h1>在线充值</h1></div>
	<form action="http://pay.yinshengvip.com/pay/" method="post" name="form5" id="form5">
	<table width="100%" border="0" cellpadding="4" cellspacing="0" style="margin-top:20px; margin-bottom:20px;" >
		<tr>
		<td width="150" height="30" align="right">充值金额：</td>
		<td><input type="text" name="OrdAmt" value="<?php echo app_buymin?>" size="22" maxlength="12"></td>
		</tr>
		<tr>
		<td width="150" height="30" align="right">提示信息：</td>
		<td> 最小充值金额为<?php echo app_buymin?>元，1元可以兑换<?php echo app_buyexc?>个积分
		</td>
		</tr>
	    <tr align="center">
	      <td colspan="2"><input class="input" type="submit" value="确认" id="btnSave">  </td>
	    </tr>
	</table>
		
		<input type="hidden" name="merchant_idcode" value="ksyitnnoudo6oght" size="40" maxlength="40">
		<input type="hidden" name="OrdId" value="<?php echo date(Ymdhms)?>" size="22" maxlength="20">
		<input type="hidden" name="Pid" value="10000" size="6" maxlength="10">
		<input type="hidden" name="RetUrl" value="<?php echo $url?>buy_return_url.php" size="60" maxlength="60">
		<input type="hidden" name="BgRetUrl" value="<?php echo $url?>buy_notify_url.php" size="60" maxlength="260">
		<input type="hidden" name="MerPriv" value="" size="60" maxlength="60">
		<input type="hidden" name="UsrMp" value="" size="15" maxlength="11">
		<input type="hidden" name="partnerID" value="<?php echo app_buyid?>" size="40" maxlength="40">
		<input type="hidden" name="keyID" value="<?php echo app_buykey?>" size="40" maxlength="40">
		
	</form>
</div>
<?php
}

function paysave()
{
	global $db;
	$cardnum = be("post","cardnum"); $cardnum=chkSql($cardnum,true);
	$cardpwd = be("post","cardpwd"); $cardpwd=chkSql($cardpwd,true);
	if (isN($cardnum)){ alert ("卡号不能为空！" );exit;}
	if (isN($cardpwd)) { alert ("卡号密码不能为空" ); exit;}
	
	$sql = "SELECT * FROM {pre}user_card WHERE c_number='". $cardnum ."'and c_pass='". $cardpwd."'";
    $row = $db->getRow($sql);
	if (!$row){
		alert ("该充值卡不存在或者卡号密码错了");exit;
	}
	else{
		if ($row["c_used"]==1){
		   alert ("此卡已经充值，请不要重复使用此卡");exit;
		}
		$c_id = $row["c_id"];
		$c_point = $row["c_point"];
	}
	unset($rs);
	
	$sql = "SELECT * FROM {pre}user WHERE u_id=" . $_SESSION["userid"] . "";
	$row1 = $db->getRow($sql);
	if (!$row1){
		alert ("获取会员信息出错,充值失败"); exit ;
	}
	else{
		$db->query("update {pre}user set u_points=u_points+".$c_point." where u_id = ". $_SESSION["userid"]);
	}
	unset($row);
	unset($row1);
	$db->query("update {pre}user_card set c_used=1,c_user=". $_SESSION["userid"] .",c_sale=1,c_usetime='". date('Y-m-d H:i:s',time()) ."' where c_id = ". $c_id);
	alert ("充值成功");
}

function paysave2()
{
	$buynum = be("post","buynum"); $buynum=chkSql($buynum,true);
	if (!isNum($buynum)){ alert ("充值金额必须是数字！" );exit; } else { $buynum=intval($buynum); }
	if ($buynum < app_buymin) { alert ("最小充值金额是".app_buymin."元，请重填！" );exit; }
	
}
?>