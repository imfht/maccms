<?php
require_once ("admin_conn.php");

chkLogin();
$action = be("get","action");
switch(trim($action))
{
	case "configsave" : configsave();break;
	case "configplaysave" : configplaysave();break;
	case "configconnectsave" : configconnectsave();break;
	case "configbuysave" : configbuysave();break;
	case "configplay" : headAdmin ("播放器设置") ; configplay();break;
	case "configconnect" : headAdmin ("一键登录设置") ; configconnect();break;
	case "configbuy" : headAdmin ("在线支付设置") ; configbuy();break;
	default : headAdmin ("站点设置") ; config();break;
}
dispseObj();

function config()
{
?>
<script type="text/javascript">
$(document).ready(function(){
	$("#app_dbtype").change(function(){
		var type=$("#app_dbtype").val();
		if(type=="access"){
			 $("#access").css("display","");
			 $("#sqlserver1").css("display","none")
			 $("#sqlserver2").css("display","none")
			 $("#sqlserver3").css("display","none")
			 $("#sqlserver4").css("display","none")
		}
		else{
			 $("#access").css("display","none");
			 $("#sqlserver1").css("display","")
			 $("#sqlserver2").css("display","")
			 $("#sqlserver3").css("display","")
			 $("#sqlserver4").css("display","")
		}	
	});	
	$("#form1").validate({
		rules:{
			app_sitename:{
				required:true,
				maxlength:64
			},
			app_siteurl:{
				required:true,
				maxlength:64
			},
			app_installdir:{
				required:true,
				maxlength:64
			},
			app_cachetime:{
				required:true,
				number:true
			},
			app_vodmakeinterval:{
				required:true,
				number:true,
				min:1
			},
			app_artmakeinterval:{
				required:true,
				number:true,
				min:1
			},
			app_artlistpath:{
				required:true,
				maxlength:64
			},
			app_artpath:{
				required:true,
				maxlength:64
			},
			app_arttopicpath:{
				required:true,
				maxlength:64
			},
			app_vodlistpath:{
				required:true,
				maxlength:64
			},
			app_vodpath:{
				required:true,
				maxlength:64
			},
			app_vodplaypath:{
				required:true,
				maxlength:64
			},
			app_vodtopicpath:{
				required:true,
				maxlength:64
			},
			app_pagenum:{
				required:true,
				number:true,
				min:15
			},
			app_gbooknum:{
				required:true,
				number:true,
				min:5
			},
			app_gbooktime:{
				required:true,
				number:true,
				min:5
			},
			app_commentnum:{
				required:true,
				number:true,
				min:5
			},
			app_commenttime:{
				required:true,
				number:true,
				min:5
			},
			app_apipagenum:{
				required:true,
				number:true
			},
			app_popularize:{
				required:true,
				number:true
			},
			app_regpoint:{
				required:true,
				number:true
			},
			app_weekpoint:{
				required:true,
				number:true
			},
			app_monthpoint:{
				required:true,
				number:true
			},
			app_yearpoint:{
				required:true,
				number:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#htmltabs li").each(function(i,row){
		if($.cookie("configtab")==i && i>0){
			settab(i);
		}
	});
});

function settab(to){
	$("#htmltabs li").each(function(i,row){
		if("tabs.title"+ to == $(this).attr("id")){
			$("#tab"+i).show(); $(this).attr("class","hover");
		}
		else{
			$("#tab"+i).hide(); $(this).removeClass("hover");
		}
	});
	$.cookie("configtab",to);
}
function sethtmldir(id,value){
	if(value){
		$('#'+id).val(value);
	}
}
</script>
</head>
<body>
<form method="post" action="?action=configsave" id="form1" name="form1">
	<div>
		<ul id="htmltabs" style="padding:0">
		<li id="tabs.title0" class="hover" onclick="settab('0');" style="cursor:pointer">基本</li>
		<li id="tabs.title1" onclick="settab('1');" style="cursor:pointer">视频</li>
		<li id="tabs.title2" onclick="settab('2');" style="cursor:pointer">文章</li>
		<li id="tabs.title3" onclick="settab('3');" style="cursor:pointer">会员</li>
		<li id="tabs.title4" onclick="settab('4');" style="cursor:pointer">其他</li>
		<li id="tabs.title5" onclick="settab('5');" style="cursor:pointer">远程附件</li>
		<li id="tabs.title6" onclick="settab('6');" style="cursor:pointer">资源API</li>
		<li>&nbsp;&nbsp;&nbsp;<input type="submit" id="btnSave" class="input" value="更新网站参数">&nbsp;如配置出错，请手动修改网站 inc 目录下的 config.php文件。</li>
		</ul>
	</div>
	
	<table class="tb" id="tab0" style="display:block">
	<tr>
      <td width="15%">网站名称：</td>
      <td><input name="app_sitename" type="text" id="app_sitename" value="<?php echo app_sitename?>" size="40" maxlength="500"> 
       <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td>网站域名：</td>
      <td><input name="app_siteurl" type="text" id="app_siteurl" value="<?php echo app_siteurl?>" size="40" maxlength="500">
        <font color="#FF0000">＊</font> 如：WwW.MacCms.Com,不要加http://</td>
    </tr>
    <tr>
      <td>网站关键字：</td>
      <td><input name="app_keywords" type="text" id="app_keywords" value="<?php echo app_keywords?>" size="40" maxlength="500"> 
       <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td>网站描述信息：</td>
      <td><input name="app_description" type="text" id="app_description" value="<?php echo app_description?>" size="40" maxlength="500"> 
       <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td>安装目录：</td>
      <td><input name="app_installdir" type="text" id="app_installdir" value="<?php echo app_installdir?>" size="40" maxlength="500">  <font color="#FF0000">＊</font> 根目录 ＂/＂，二级目录 ＂/maccms/＂以此类推 </td>
    </tr>
        <tr>
          <td>当前使用模板：</td>
          <td><select name="app_templatedir" style="width:100px">
          <?php
          $attr = getFolderItem("../template/");
          for ($i=0;$i<count($attr);$i++)
          {
          	  	if ($attr[$i] !="diy"){
           ?>
          <option value="<?php echo $attr[$i] ?>"<?php if(app_templatedir==trim($attr[$i])){ echo "selected";}?>><?php echo $attr[$i] ?></option>
          <?php }
          }
          ?>
          </select>
             模板html目录：<input name="app_htmldir" type="text" id="app_htmldir" value="<?php echo app_htmldir?>" size="10" />
          不怕模板被人盗就不用改          </td>
        </tr>
    <tr>
      <td>网站数据类型：</td>
      <td><select name="app_dbtype" id="app_dbtype">
        <option value="mysql" <?php if (app_dbtype=="mysql" ){ echo "selected" ;}?>>MYSQL数据库</option>
        </select> 出错请手动修改inc目录下的config.php文件</td>
    </tr>
    <tr id="sqlserver1">
      <td>SQL服务器IP：</td>
      <td>
        <input name="app_dbserver" type="text" id="app_dbserver" value="<?php echo app_dbserver?>" size="40" /><font color="#FF0000">＊</font></td>
    </tr>
    <tr id="sqlserver2">
      <td>SQL数据库名称：</td>
      <td>
        <input name="app_dbname" type="text" id="app_dbname" value="<?php echo app_dbname?>" size="40" /><font color="#FF0000">＊</font></td>
    </tr>
    <tr id="sqlserver3">
      <td>SQL数据库帐号：</td>
      <td>
        <input name="app_dbuser" type="text" id="app_dbuser" value="<?php echo app_dbuser?>" size="40" /><font color="#FF0000">＊</font></td>
    </tr>
    <tr id="sqlserver4">
      <td>SQL数据库密码：</td>
      <td><input name="app_dbpass" type="text" id="app_dbpass" value="<?php echo app_dbpass?>" size="40" /><font color="#FF0000">＊</font></td>
	</tr>
    <tr>
      <td>数据库表名前缀名：</td>
      <td><input name="app_tablepre" type="text" id="app_tablepre" value="<?php echo app_tablepre?>" size="40" /><font color="#FF0000">＊</font></td>
	</tr>
	<tr>
      <td>缓存时间：</td>
      <td>
      <input name="app_cachetime" type="text" id="app_cachetime" value="<?php echo app_cachetime?>" size="5">分钟
      <input name="app_cacheid" type="hidden" id="app_cacheid" value="<?php echo app_cacheid?>" size="25">
        </td>
    </tr>
    <tr>
      <td>内存缓存设定：</td>
      <td>
        <input type="radio" name="app_cache" id="app_cache" value="0" <?php if(app_cache==0){echo "checked";}?> class="radio" />关闭
        &nbsp;
        <input type="radio" name="app_cache" id="app_cache" value="1" <?php if(app_cache==1){echo "checked";}?> class="radio" />开启
        &nbsp;内存缓存显著提高系统运行速度。点击【更新内存缓存】更新此缓存
	  </td>
    </tr>
       <tr>
      <td>动态文件缓存设定：</td>
      <td>
      <input type="radio" name="app_dynamiccache" id="app_dynamiccache" value="0" <?php if(app_dynamiccache==0){echo "checked";}?> class="radio" />关闭
        &nbsp;
        <input type="radio" name="app_dynamiccache" id="app_dynamiccache" value="1" <?php if(app_dynamiccache==1){echo "checked";}?> class="radio" />开启
        &nbsp;只在动态模式下起作用，缓存首页，列表及搜索页。缓存路径inc/cache，点击【更新文件缓存】更新此缓存
	  </td>
    </tr>
    <tr>
      <td>会员验证设定：</td>
      <td>
      <input type="radio" name="app_user" id="app_user" value="0" <?php if(app_user==0){echo "checked";}?> class="radio" />关闭
        &nbsp;
        <input type="radio" name="app_user" id="app_user" value="1" <?php if(app_user==1){echo "checked";}?> class="radio" />开启
        &nbsp; 如果您网站没有使用该功能，请关闭，提高运行速度
	  </td>
    </tr>
    <tr>
      <td>定时任务设定：</td>
      <td>
      <input type="radio" name="app_timming" id="app_timming" value="0" <?php if(app_timming==0){echo "checked";}?> class="radio" />关闭
        &nbsp;
        <input type="radio" name="app_timming" id="app_timming" value="1" <?php if(app_timming==1){echo "checked";}?> class="radio" />开启
        &nbsp; 如果您网站没有使用该功能，请关闭，提高运行速度
	  </td>
    </tr>
    <tr>
      <td>后台列表显示数量：</td>
      <td><input name="app_pagenum" type="text" id="app_pagenum" value="<?php echo app_pagenum?>" size="10">
        </td>
    </tr>
    <tr>
      <td>网站备案号：</td>
      <td><input name="app_icp" type="text" id="app_icp" value="<?php echo app_icp?>" size="40"></td>
    </tr>
    <tr>
      <td>站长QQ号码：</td>
      <td><input name="app_qq" type="text" id="app_qq" value="<?php echo app_qq?>" size="40"></td>
    </tr>
    <tr>
      <td>站长Email邮箱：</td>
      <td><input name="app_email" type="text" id="app_email" value="<?php echo app_email?>" size="40"></td>
    </tr>
    <tr>
      <td>后台登陆安全码：</td>
      <td><input name="app_safecode" type="text" id="app_safecode" value="<?php echo app_safecode?>" size="40"></td>
    </tr>
	</table>
	
	<table class="tb" id="tab1" style="display:none;">
	<tr>
      <td width="15%">配置参数 </td>
      <td>
      	
      </td>
    </tr>
	<tr>
      <td>播放器排序：</td>
      <td>
      	<input type="radio" name="app_vodplayersort" id="app_vodplayersort" value="0" <?php if(app_vodplayersort==0){echo "checked";}?> class="radio" />添加顺序
        &nbsp;
        <input type="radio" name="app_vodplayersort" id="app_vodplayersort" value="1" <?php if(app_vodplayersort==1){echo "checked";}?> class="radio" />全局顺序
      </td>
    </tr>
    <tr>
      <td>资源库重名判定：</td>
      <td>
      	<input type="radio" name="app_vodmaccjsname" id="app_vodmaccjsname" value="0" <?php if(app_vodmaccjsname==0){echo "checked";}?> class="radio" />名称
        &nbsp;
        <input type="radio" name="app_vodmaccjsname" id="app_vodmaccjsname" value="1" <?php if(app_vodmaccjsname==1){echo "checked";}?> class="radio" />名称+分类
      </td>
    </tr>
    <tr>
      <td> &nbsp;</td>
      <td> &nbsp;</td>
    </tr>
	<tr>
      <td width="15%">首页浏览模式：</td>
      <td>
		<select name="app_vodviewtype" id="app_vodviewtype" style="width:150px;">
        <option value="0" <?php if(app_vodviewtype==0){echo "selected";}?>>仿伪静态模式</option>
        <option value="1" <?php if(app_vodviewtype==1){echo "selected";}?>>动态模式</option>
        <option value="2" <?php if(app_vodviewtype==2){echo "selected";}?>>静态模式</option>
        <option value="3" <?php if(app_vodviewtype==3){echo "selected";}?>>rewrite伪静态</option>
        </select>
        </td>
    </tr>
    <tr>
      <td width="15%">列表浏览模式：</td>
      <td>
		<select name="app_vodlistviewtype" id="app_vodlistviewtype" style="width:150px;">
        <option value="0" <?php if(app_vodlistviewtype==0){echo "selected";}?>>仿伪静态模式</option>
        <option value="1" <?php if(app_vodlistviewtype==1){echo "selected";}?>>动态模式</option>
        <option value="2" <?php if(app_vodlistviewtype==2){echo "selected";}?>>静态模式</option>
        <option value="3" <?php if(app_vodlistviewtype==3){echo "selected";}?>>rewrite伪静态</option>
        </select>
        &nbsp;专题浏览模式：
        <select name="app_vodtopicviewtype" id="app_vodtopicviewtype" style="width:150px;">
        <option value="0" <?php if(app_vodtopicviewtype==0){echo "selected";}?>>仿伪静态模式</option>
        <option value="1" <?php if(app_vodtopicviewtype==1){echo "selected";}?>>动态模式</option>
        <option value="2" <?php if(app_vodtopicviewtype==2){echo "selected";}?>>静态模式</option>
        <option value="3" <?php if(app_vodtopicviewtype==3){echo "selected";}?>>rewrite伪静态</option>
        </select>
        </td>
    </tr>
    <tr>
      <td>内容页面设置：</td>
      <td>
        <select name="app_playtype" id="app_playtype" style="width:150px">
        <option value="0" <?php if( app_playtype==0){echo "selected";}?>>有内容页</option>
        <option value="1" <?php if( app_playtype==1){echo "selected";}?>>无内容页</option>
        </select>
        &nbsp;内容浏览模式：
        <select name="app_vodcontentviewtype" id="app_vodcontentviewtype" style="width:150px;">
        <option value="0" <?php if(app_vodcontentviewtype==0){echo "selected";}?>>仿伪静态模式</option>
        <option value="1" <?php if(app_vodcontentviewtype==1){echo "selected";}?>>动态模式</option>
        <option value="2" <?php if(app_vodcontentviewtype==2){echo "selected";}?>>静态模式</option>
        <option value="3" <?php if(app_vodcontentviewtype==3){echo "selected";}?>>rewrite伪静态</option>
        </select>
      </td> 
    </tr>
    <tr>
      <td>播放浏览模式：</td>
      <td>
        <select name="app_vodplayviewtype" id="app_vodplayviewtype" style="width:150px;">
        <option value="0" <?php if(app_vodplayviewtype==0){echo "selected";}?>>仿伪静态模式</option>
        <option value="1" <?php if(app_vodplayviewtype==1){echo "selected";}?>>动态模式</option>
        <option value="2" <?php if(app_vodplayviewtype==2){echo "selected";}?>>rewrite伪静态</option>
        <option value="3" <?php if(app_vodplayviewtype==3){echo "selected";}?>>静态每数据一页</option>
        <option value="4" <?php if(app_vodplayviewtype==4){echo "selected";}?>>静态每集一页</option>
        <option value="5" <?php if(app_vodplayviewtype==5){echo "selected";}?>>静态每组一页</option>
        <option value="6" <?php if(app_vodplayviewtype==6){echo "selected";}?>>静态全站一页</option>
        </select>
        &nbsp;下载浏览模式：
        <select name="app_voddownviewtype" id="app_voddownviewtype" style="width:150px;">
        <option value="0" <?php if(app_voddownviewtype==0){echo "selected";}?>>仿伪静态模式</option>
        <option value="1" <?php if(app_voddownviewtype==1){echo "selected";}?>>动态模式</option>
        <option value="2" <?php if(app_voddownviewtype==2){echo "selected";}?>>rewrite伪静态</option>
        <option value="3" <?php if(app_voddownviewtype==3){echo "selected";}?>>静态每数据一页</option>
        <option value="4" <?php if(app_voddownviewtype==4){echo "selected";}?>>静态每集一页</option>
        <option value="5" <?php if(app_voddownviewtype==5){echo "selected";}?>>静态每组一页</option>
        <option value="6" <?php if(app_voddownviewtype==6){echo "selected";}?>>静态全站一页</option>
        </select>
      </td> 
    </tr>
    <tr>
      <td>加密播放地址：</td>
      <td>
        <select name="app_encrypt" id="app_encrypt" style="width:150px">
        <option value="0" <?php if( app_encrypt==0){echo "selected";}?>>不加密</option>
        <option value="1" <?php if( app_encrypt==1){echo "selected";}?>>escape编码</option>
        <option value="2" <?php if( app_encrypt==2){echo "selected";}?>>base64编码</option>
        </select>
        &nbsp;是否弹窗播放：
        <select name="app_playisopen" id="app_playisopen" style="width:150px">
        <option value="0" <?php if( app_playisopen==0){echo "selected";}?>>普通播放</option>
        <option value="1" <?php if( app_playisopen==1){echo "selected";}?>>弹窗播放</option>
        </select>
        </td>
    </tr>
	<tr>
      <td>静态页后缀名：</td>
      <td>
		<select name="app_vodsuffix" id="app_vodsuffix" style="width:150px">
		  <option value="htm" <?php if( app_vodsuffix=="htm"){echo "selected";}?>>htm</option>
		  <option value="html" <?php if( app_vodsuffix=="html"){echo "selected";}?>>html</option>
		  <option value="shtml" <?php if( app_vodsuffix=="shtml"){echo "selected";}?>>shtml</option>
		</select>
		&nbsp;生成页面间隔：
		<input name="app_vodmakeinterval" type="text" id="app_vodmakeinterval" value="<?php echo app_vodmakeinterval?>" size="5">
<font color="#FF0000">＊</font> 以秒为单位</td>
    </tr>
	
	<tr>
      <td> &nbsp;</td>
      <td> &nbsp;</td>
    </tr>
	<tr>
	  <td>列表生成路径：</td>
	  <td><input name="app_vodlistpath" type="text" id="app_vodlistpath" value="<?php echo app_vodlistpath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_vodlistpath',this.value);"><option>常用结构</option><option value="vodlisthtml/{id}/index">1.vodlisthtml/id/</option><option value="vodlisthtml/{md5}/index">2.vodlisthtml/md5值/</option></option><option value="vodlisthtml/{enname}/index">3.vodlisthtml/enname/</option><option value="vodlisthtml/{id}">4.vodlisthtml/id.html</option><option value="vodlisthtml/{md5}">5.vodlisthtml/md5.html</option><option value="vodlisthtml/{enname}">6.vodlisthtml/enname.html</option></select></td>
	</tr>
	<tr>
	  <td>专题生成路径：</td>
	  <td><input name="app_vodtopicpath" type="text" id="app_vodtopicpath" value="<?php echo app_vodtopicpath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_vodtopicpath',this.value);"><option>常用结构</option><option value="vodtopichtml/{id}/index">1.vodtopichtml/id/</option><option value="vodtopichtml/{md5}/index">2.vodtopichtml/md5值/</option></option><option value="vodtopichtml/{enname}/index">3.vodtopichtml/enname/</option><option value="vodtopichtml/{id}">4.vodtopichtml/id.html</option><option value="vodtopichtml/{md5}">5.vodtopichtml/md5.html</option><option value="vodtopichtml/{enname}">6.vodtopichtml/enname.html</option></select></td>
	</tr>
	<tr>
	  <td>内容页生成路径：</td>
	  <td><input name="app_vodpath" type="text" id="app_vodpath" value="<?php echo app_vodpath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_vodpath',this.value);"><option>常用结构</option><option value="vodhtml/{id}/index">1.vodhtml/id/</option><option value="vodhtml/{md5}/index">2.vodhtml/md5值/</option></option><option value="vodhtml/{enname}/index">3.vodhtml/enname/</option><option value="vodhtml/{id}">4.vodhtml/id.html</option><option value="vodhtml/{md5}">5.vodhtml/md5.html</option><option value="vodhtml/{enname}">6.vodhtml/enname.html</option></select> 附加变量：分类ID{typeid} 分类名称{typename} 分类拼音{typeenname} 年{year} 月{month} 日{day}</td>
	</tr>
	<tr>
	  <td>播放页生成路径：</td>
	  <td><input name="app_vodplaypath" type="text" id="app_vodplaypath" value="<?php echo app_vodplaypath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_vodplaypath',this.value);"><option>常用结构</option><option value="vodplayhtml/{id}/index">1.vodplayhtml/id/</option><option value="vodplayhtml/{md5}/index">2.vodplayhtml/md5值/</option></option><option value="vodplayhtml/{enname}/index">3.vodplayhtml/enname/</option><option value="vodplayhtml/{id}">4.vodplayhtml/id.html</option><option value="vodplayhtml/{md5}">5.vodplayhtml/md5.html</option><option value="vodplayhtml/{enname}">6.vodplayhtml/enname.html</option></select> 附加变量：分类ID{typeid} 分类名称{typename} 分类拼音{typeenname} 年{year} 月{month} 日{day}</td>
	<tr>
	  <td>下载页生成路径：</td>
	  <td><input name="app_voddownpath" type="text" id="app_voddownpath" value="<?php echo app_voddownpath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_voddownpath',this.value);"><option>常用结构</option><option value="voddownhtml/{id}/index">1.voddownhtml/id/</option><option value="voddownhtml/{md5}/index">2.voddownhtml/md5值/</option></option><option value="voddownhtml/{enname}/index">3.downhtml/enname/</option><option value="voddownhtml/{id}">4.voddownhtml/id.html</option><option value="voddownhtml/{md5}">5.voddownhtml/md5.html</option><option value="voddownhtml/{enname}">6.voddownhtml/enname.html</option></select> 附加变量：分类ID{typeid} 分类名称{typename} 分类拼音{typeenname} 年{year} 月{month} 日{day}</td>
	</tr>
	</tr>
	<tr>
	  <td>静态提示说明：</td>
	  <td>
	  <font color="#FF0000">不用填写后缀(.html)之类的。禁止出现如下字符:  * : ?  " < > | \ 
	  <br>全部可用变量：编号{id} 名称{name} 拼音{enname}   (至少出现1个)
	  <br><br>选择rewrite伪静态模式时，生成（显示）路径中只支持{id}变量，也只能以 {id}结尾，只能包含1层目录。例如 vodhtml/{id} , dyhtml/{id}
	  <br> 然后需要手工配置一下 rewrite伪静态规则，对应你的配置目录即可。 
	  </font>
	  </td>
	</tr>
	</table>
	
	<table class="tb" id="tab2" style="display:none">
	<tr>
      <td width="15%">文章首页浏览模式：</td>
      <td>
		<select name="app_artviewtype" id="app_artviewtype" style="width:150px;">
        <option value="0" <?php if( app_artviewtype=="0"){echo "selected";}?>>仿伪静态模式</option>
        <option value="2" <?php if( app_artviewtype=="2"){echo "selected";}?>>静态模式</option>
        <option value="1" <?php if( app_artviewtype=="1"){echo "selected";}?>>动态模式</option>
        <option value="3" <?php if( app_artviewtype=="3"){echo "selected";}?>>rewrite伪静态</option>
        </select>
        &nbsp;列表浏览模式：
        <select name="app_artlistviewtype" id="app_artlistviewtype" style="width:150px;">
        <option value="0" <?php if( app_artlistviewtype=="0"){echo "selected";}?>>仿伪静态模式</option>
        <option value="2" <?php if( app_artlistviewtype=="2"){echo "selected";}?>>静态模式</option>
        <option value="1" <?php if( app_artlistviewtype=="1"){echo "selected";}?>>动态模式</option>
        <option value="3" <?php if( app_artlistviewtype=="3"){echo "selected";}?>>rewrite伪静态</option>
        </select>
        </td>
    </tr>
    <tr>
      <td width="15%">专题浏览模式：</td>
      <td>
		<select name="app_arttopicviewtype" id="app_arttopicviewtype" style="width:150px;">
        <option value="0" <?php if( app_arttopicviewtype=="0"){echo "selected";}?>>仿伪静态模式</option>
        <option value="2" <?php if( app_arttopicviewtype=="2"){echo "selected";}?>>静态模式</option>
        <option value="1" <?php if( app_arttopicviewtype=="1"){echo "selected";}?>>动态模式</option>
        <option value="3" <?php if( app_arttopicviewtype=="3"){echo "selected";}?>>rewrite伪静态</option>
        </select>
        &nbsp;内容浏览模式：
        <select name="app_artcontentviewtype" id="app_artcontentviewtype" style="width:150px;">
        <option value="0" <?php if( app_artcontentviewtype=="0"){echo "selected";}?>>仿伪静态模式</option>
        <option value="2" <?php if( app_artcontentviewtype=="2"){echo "selected";}?>>静态模式</option>
        <option value="1" <?php if( app_artcontentviewtype=="1"){echo "selected";}?>>动态模式</option>
        <option value="3" <?php if( app_artcontentviewtype=="3"){echo "selected";}?>>rewrite伪静态</option>
        </select>
        </td>
    </tr>
    <tr>
      <td>静态页后缀名：</td>
      <td>
		<select name="app_artsuffix" id="app_artsuffix" style="width:150px">
		  <option value="htm" <?php if( app_artsuffix=="htm"){echo "selected";}?>>htm</option>
		  <option value="html" <?php if( app_artsuffix=="html"){echo "selected";}?>>html</option>
		  <option value="shtml" <?php if( app_vodsuffix=="shtml"){echo "selected";}?>>shtml</option>
		</select> 
		&nbsp;生成页面间隔：
		<input name="app_artmakeinterval" type="text" id="app_artmakeinterval" value="<?php echo app_artmakeinterval?>" size="5">
<font color="#FF0000">＊</font> 以秒为单位</td>
    </tr>
	<tr>
	  <td>提示说明：</td>
	  <td>
	  <font color="#FF0000">后缀不用填写(.html)之类的。禁止出现如下字符:  * : ?  " < > | \ 
	  <br>全部可用变量：编号{id} 名称{name} 拼音{enname}   (至少出现1个)
	  <br><br>选择rewrite伪静态模式时，生成（显示）路径中只支持{id}变量，也只能以 {id}结尾，只能包含1层目录。例如 vodhtml/{id} , dyhtml/{id}
	  <br> 然后需要手工配置一下 rewrite伪静态规则，对应你的配置目录即可。 
	  </font>
	  </td>
	</tr>
	<tr>
	  <td>列表生成路径配置：</td>
	  <td><input name="app_artlistpath" type="text" id="app_artlistpath" value="<?php echo app_artlistpath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_artlistpath',this.value);"><option>常用结构</option><option value="artlisthtml/{id}/index">1.artlisthtml/id/</option><option value="artlisthtml/{md5}/index">2.artlisthtml/md5值/</option></option><option value="artlisthtml/{enname}/index">3.artlisthtml/enname/</option><option value="artlisthtml/{id}">4.artlisthtml/id.html</option><option value="artlisthtml/{md5}">5.artlisthtml/md5.html</option><option value="artlisthtml/{enname}">6.artlisthtml/enname.html</option></select></td>
	</tr>
	<tr>
	  <td>内容生成路径配置：</td>
	  <td><input name="app_artpath" type="text" id="app_artpath" value="<?php echo app_artpath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_artpath',this.value);"><option>常用结构</option><option value="arthtml/{id}/index">1.arthtml/id/</option><option value="arthtml/{md5}/index">2.arthtml/md5值/</option></option><option value="arthtml/{enname}/index">3.arthtml/enname/</option><option value="arthtml/{id}">4.arthtml/id.html</option><option value="arthtml/{md5}">5.arthtml/md5.html</option><option value="arthtml/{enname}">6.arthtml/enname.html</option></select> 附加变量：分类ID{typeid} 分类名称{typename} 分类拼音{typeenname} 年{year} 月{month} 日{day}</td>
	</tr>
	<tr>
	  <td>专题生成路径配置：</td>
	  <td><input name="app_arttopicpath" type="text" id="app_arttopicpath" value="<?php echo app_arttopicpath?>" size="40" />
<font color="#FF0000">＊</font><select style="width:120px" onChange="sethtmldir('app_arttopicpath',this.value);"><option>常用结构</option><option value="arttopichtml/{id}/index">1.arttopichtml/id/</option><option value="arttopichtml/{md5}/index">2.arttopichtml/md5值/</option></option><option value="arttopichtml/{enname}/index">3.arttopichtml/enname/</option><option value="arttopichtml/{id}">4.arttopichtml/id.html</option><option value="arttopichtml/{md5}">5.arttopichtml/md5.html</option><option value="arttopichtml/{enname}">6.arttopichtml/enname.html</option></select> </td>
	</tr>
	</table>
	
	<table class="tb" id="tab3" style="display:none">
	<tr>
      <td width="15%">是否开启会员注册：</td>
      <td>
        <input type="radio" name="app_reg" id="app_reg" value="0" <?php if( app_reg==0 ){echo "checked";}?> class="radio" />关闭
        &nbsp;
        <input type="radio" name="app_reg" id="app_reg" value="1" <?php if( app_reg==1 ){echo "checked";}?> class="radio" />开启
       </td>
    </tr>
	<tr>
      <td>注册用户默认状态：</td>
      <td>
        <input type="radio" name="app_regstate" id="app_regstate" value="0" <?php if( app_regstate==0 ){echo "checked";}?> class="radio" />锁定
        &nbsp;
        <input type="radio" name="app_regstate" id="app_regstate" value="1" <?php if( app_regstate==1 ){echo "checked";}?> class="radio" />激活
      </td>
    </tr>
    <tr>
      <td>是否开启推广赚积分：</td>
      <td>
        <input type="radio" name="app_popularizestate" id="app_popularizestate" value="0" <?php if( app_popularizestate==0 ){echo "checked";}?> class="radio" />关闭
        &nbsp;
        <input type="radio" name="app_popularizestate" id="app_popularizestate" value="1" <?php if( app_popularizestate==1 ){echo "checked";}?> class="radio" />开启
      </td>
    </tr>
    <tr>
      <td>注册用户默认会员组：</td>
      <td>
        <select name="app_reggroup" id="app_reggroup">
        	<option value="0">请选择会员组</option>
			<?php echo makeSelect("{pre}user_group","ug_id","ug_name","","","&nbsp;|&nbsp;&nbsp;",app_reggroup)?>
        </select>(必须先建立好会员组)
      </td>
    </tr>
    <tr>
      <td>注册赠送点数：</td>
      <td>
      	<input name="app_regpoint" type="text" id="app_regpoint" value="<?php echo app_regpoint?>" size="10" >
        <font color="#FF0000">＊</font></td>
    </tr>
    <tr>
      <td>每推广1个人获取积分数：</td>
      <td>
        <input name="app_popularize" type="text" id="app_popularize" value="<?php echo app_popularize?>" size="10" />
        <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td>包周所需积分数：</td>
      <td>
      	<input name="app_weekpoint" type="text" id="app_weekpoint" value="<?php echo app_weekpoint?>" size="10" >
        <font color="#FF0000">＊</font></td>
    </tr>
    <tr>
      <td>包月所需积分数：</td>
      <td>
      	<input name="app_monthpoint" type="text" id="app_monthpoint" value="<?php echo app_monthpoint?>" size="10" >
        <font color="#FF0000">＊</font></td>
    </tr>
    <tr>
      <td>包年所需积分数：</td>
      <td>
      	<input name="app_yearpoint" type="text" id="app_weekpoint" value="<?php echo app_yearpoint?>" size="10" >
        <font color="#FF0000">＊</font></td>
    </tr>
	</table>
	
	<table class="tb" id="tab4" style="display:none">
	<tr>
      <td width="15%">图片文字水印：</td>
      <td>
        <input type="radio" name="app_watermark" id="app_watermark" value="0" <?php if( app_watermark==0 ){echo "checked";}?> class="radio" onClick="javascript:document.getElementById('opw').style.display='none'"/>
        关闭
		<input type="radio" name="app_watermark" id="app_watermark" value="1" <?php if( app_watermark==1 ){echo "checked";}?> class="radio" onClick="javascript:document.getElementById('opw').style.display=''" />
        开启<font id="opw" <?php if( app_watermark==0 ){?>style="display:none;"<?php }?>>&nbsp;&nbsp;水印位置：
        <select name="app_waterlocation"  id="app_waterlocation" style="width:54px;">
          <option value="0" <?php if( app_waterlocation=="0" ){echo "selected";}?>>居中</option>
          <option value="1" <?php if( app_waterlocation=="1" ){echo "selected";}?>>右上</option>
          <option value="2" <?php if( app_waterlocation=="2" ){echo "selected";}?>>右下</option>
          <option value="3" <?php if( app_waterlocation=="3" ){echo "selected";}?>>左上</option>
          <option value="4" <?php if( app_waterlocation=="4" ){echo "selected";}?>>左下</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;水印文字：
        <input name="app_waterfont" type="text" id="app_waterfont" value="<?php echo app_waterfont?>" size="13" />
        </font> &nbsp;&nbsp;
        
	  </td>
    </tr>
	<tr>
      <td>图片保存方式：</td>
      <td>
		<select name="app_picpath" id="app_picpath">
        <option value="0" <?php if( app_picpath=="0"){echo "selected";}?>>默认</option>
        <option value="1" <?php if( app_picpath=="1"){echo "selected";}?>>按月份</option>
		<option value="2" <?php if( app_picpath=="2"){echo "selected";}?>>按日期</option>
		<option value="3" <?php if( app_picpath=="3"){echo "selected";}?>>每目录500图片</option>
        </select> 
	  </td>
    </tr>
    <tr>
      <td>留言本状态：</td>
      <td>
      	<input type="radio" name="app_gbook" id="app_gbook" value="0" <?php if( app_gbook==0 ){echo "checked";}?> class="radio" />关闭
		&nbsp;
		<input type="radio" name="app_gbook" id="app_gbook" value="1" <?php if( app_gbook==1 ){echo "checked";}?> class="radio" />开启
	  </td>
    </tr>
    <tr>
      <td>留言是否审核：</td>
      <td>
      	<input type="radio" name="app_gbookaudit" id="app_gbookaudit" value="0" <?php if( app_gbookaudit == 0 ){echo "checked";}?> class="radio" />关闭
		&nbsp;
		<input type="radio" name="app_gbookaudit" id="app_gbookaudit" value="1" <?php if( app_gbookaudit == 1 ){echo "checked";}?> class="radio" />开启
	  </td>
    </tr>
    <tr>
      <td>留言是否开启验证码：</td>
      <td>
      	<input type="radio" name="app_gbookverify" id="app_gbookverify" value="0" <?php if( app_gbookverify == 0 ){echo "checked";}?> class="radio" />关闭
		&nbsp;
		<input type="radio" name="app_gbookverify" id="app_gbookverify" value="1" <?php if( app_gbookverify == 1 ){echo "checked";}?> class="radio" />开启
	  </td>
    </tr>
    <tr>
      <td>留言本每页显示个数：</td>
      <td><input name="app_gbooknum" type="text" id="app_gbooknum" value="<?php echo app_gbooknum?>" size="10">
        </td>
    </tr>
    <tr>
      <td>留言时间间隔(单位:秒)：</td>
      <td><input name="app_gbooktime" type="text" id="app_gbooktime" value="<?php echo app_gbooktime?>" size="10">
        </td>
    </tr>
    <tr>
      <td>评论状态：</td>
      <td>
      	<input type="radio" name="app_comment" id="app_comment" value="0" <?php if( app_comment == 0 ){echo "checked";}?> class="radio" />关闭
		&nbsp;
		<input type="radio" name="app_comment" id="app_comment" value="1" <?php if( app_comment == 1 ){echo "checked";}?> class="radio" />开启
	  </td>
    </tr>
    <tr>
      <td>评论是否审核：</td>
      <td>
      	<input type="radio" name="app_commentaudit" id="app_commentaudit" value="0" <?php if( app_commentaudit == 0 ){echo "checked";}?> class="radio" />关闭
		&nbsp;
		<input type="radio" name="app_commentaudit" id="app_commentaudit" value="1" <?php if( app_commentaudit == 1 ){echo "checked";}?> class="radio" />开启
	  </td>
    </tr>
    <tr>
      <td>评论是否开启验证码：</td>
      <td>
      	<input type="radio" name="app_commentverify" id="app_commentverify" value="0" <?php if( app_commentverify == 0 ){echo "checked";}?> class="radio" />关闭
		&nbsp;
		<input type="radio" name="app_commentverify" id="app_commentverify" value="1" <?php if( app_commentverify == 1 ){echo "checked";}?> class="radio" />开启
	  </td>
    </tr>
    <tr>
      <td>评论每页显示个数：</td>
      <td><input name="app_commentnum" type="text" id="app_commentnum" value="<?php echo app_commentnum?>" size="10">
        </td>
    </tr>
    <tr>
      <td>评论时间间隔(单位:秒)：</td>
      <td><input name="app_commenttime" type="text" id="app_commenttime" value="<?php echo app_commenttime?>" size="10">
        </td>
    </tr>
    <tr>
      <td>心情状态：</td>
      <td>
      	<input type="radio" name="app_mood" id="app_mood" value="0" <?php if( app_mood == 0 ){echo "checked";}?> class="radio" />关闭
		&nbsp;
		<input type="radio" name="app_mood" id="app_mood" value="1" <?php if( app_mood == 1 ){echo "checked";}?> class="radio" />开启
	  </td>
    </tr>
    <tr>
      <td>评论留言关键字过滤：</td>
      <td>
		<textarea id="app_filter" NAME="app_filter" ROWS="8" style="width:350px;table-layout:fixed; word-wrap:break-word;"><?php echo app_filter?></textarea>
      </td>
    </tr>
	</table>
	
	<table class="tb" id="tab5" style="display:none">
		<tr>
	      <td width="15%">是否开启FTP远程附件：</td>
	      <td>
	        关闭<input type="radio" name="app_ftp" value="0" <?php if (app_ftp==0){ echo "checked"; }?>>
	        开启<input type="radio" name="app_ftp" value="1" <?php if (app_ftp==1){ echo "checked"; }?>>
	        	<font color="#FF0000">＊</font>开启将影响上传速度,但是可以将附件转移到FTP服务器(上传图片或采集时自动保存到远程服务器)
	      </td>
	    </tr>
		<tr>
	      <td width="20%">FTP 服务器：</td>
	      <td><input name="app_ftphost" type="text" id="app_ftphost" value="<?php echo app_ftphost?>" size="30"> 
	        <font color="#FF0000">＊</font> 服务器地址,不需要加"http://",一般为IP</td>
	    </tr>
	    <tr>
	      <td>FTP 用户名：</td>
	      <td><input name="app_ftpuser" type="text" id="app_ftpuser" value="<?php echo app_ftpuser?>" size="30">
	        <font color="#FF0000">＊</font> FTP服务器登录用的用户名 </td>
	    </tr>
	        <tr>
	      <td>FTP 密码：</td>
	      <td><input name="app_ftppass" type="text" id="app_ftppass" value="<?php echo app_ftppass?>" size="30">
	        <font color="#FF0000">＊</font> FTP服务器登录用的密码 </td>
	    </tr>
		<tr>
	      <td>FTP 端口：</td>
	      <td><input name="app_ftpport" type="text" id="app_ftpport" value="<?php echo app_ftpport?>" size="10"> 
	        <font color="#FF0000">＊</font> 服务器端口, 一般为 21 </td>
	    </tr>
	    <tr>
	      <td>远程附件保存文件夹：</td>
	      <td><input name="app_ftpdir" type="text" id="app_ftpdir" value="<?php echo app_ftpdir?>" size="30"> 
	        <font color="#FF0000">＊</font> (请确保已经建立)相对于FTP服务器根目录, 如/wwwroot/ </td>
	    </tr>
	    <tr>
	      <td>远程附件访问地址：</td>
	      <td><input name="app_ftpurl" type="text" id="app_ftpurl" value="<?php echo app_ftpurl?>" size="30"> 
	        <font color="#FF0000">＊</font> (必须/结尾,不使用则留空)如 http://img.maccms.com/ </td>
	    </tr>
	    <tr>
	      <td>是否删除本地图片：</td>
	      <td>关闭<input type="radio" name="app_ftpdel" value="0" <?php if (app_ftpdel==0){ echo "checked"; }?>>
	        开启<input type="radio" name="app_ftpdel" value="1" <?php if (app_ftpdel==1){ echo "checked"; }?>>
	        <font color="#FF0000">＊</font> 成功上传到远程服务器后，是否删除对应的文件 </td>
	    </tr>
	    <tr>
	      <td>友情提醒信息：</td>
	      <td><font color="#FF0000">请确保空间php环境支持ftp相关函数后再开启，例如（ftp_connect，ftp_login等）。 此功能暂时只针对视频图片。</font></td>
	    </tr>
	</table>
	
	<table class="tb" id="tab6" style="display:none">
		<tr>
	      <td width="15%">采集数据接口API开关：</td>
	      <td>
	        关闭<input type="radio" name="app_api" value="0" <?php if (app_api==0){ echo "checked"; }?>>
	        开启<input type="radio" name="app_api" value="1" <?php if (app_api==1){ echo "checked"; }?>>
	      </td>
	    </tr>
	    <tr>
	      <td>列表每页显示数量：</td>
	      <td><input name="app_apipagenum" type="text" id="app_apipagenum" value="<?php echo app_apipagenum?>" size="5">
	        <font color="#FF0000">＊</font> 数据每页显示量，不建议超过50 </td>
	    </tr>
	    <tr>
	      <td>图片域名：</td>
	      <td><input name="app_apicjflag" type="text" id="app_apicjflag" value="<?php echo app_apicjflag?>" size="50">
	        <font color="#FF0000">＊</font> 显示图片的完整访问路径所需要，以http:开头,/结尾，不包含upload目录。 </td>
	    </tr>
	    <tr>
	      <td>过滤分类参数：</td>
	      <td><input name="app_apitypefilter" type="text" id="app_apitypefilter" value="<?php echo app_apitypefilter?>" size="50">
	        <font color="#FF0000">＊</font> SQL查询条件例如 and t_hide=0 </td>
	    </tr>
		<tr>
	      <td>过滤数据参数：</td>
	      <td><input name="app_apivodfilter" type="text" id="app_apivodfilter" value="<?php echo app_apivodfilter?>" size="50"> 
	        <font color="#FF0000">＊</font> SQL查询条件例如 and d_hide=0 </td>
	    </tr>
	    <tr>
	      <td>友情提醒信息：</td>
	      <td><font color="#FF0000">此数据主要提供给苹果CMS联盟资源库使用或为其他服务商提供数据接口。如不使用请关闭。<br> 接口地址为 【http://域名/inc/api.php】 接口各种参数说明请参考说明文档</font></td>
	    </tr>
	</table>

</form>
</body>
</html>
<?php
}

function SpecialChar($str)
{
	$str = ",".$str;
	if  ( strpos($str,"*")>0 || strpos($str,":")>0 || strpos($str,"?")>0 || strpos($str,"\"")>0  || strpos($str,"<")>0  || strpos($str,">")>0  || strpos($str,"|")>0 || strpos($str,"\\")>0 ){
		alert("每项生成配置中均不能出现 * : ? \" < > | \ 等特殊符号");
	}
	
	if (! (strpos($str,"{id}") || strpos($str,"{name}") || strpos($str,"{enname}")) ) {
		alert("每项生成配置中:{id},{name},{enname} 3个变量至少出现1个");
	}
}

function configsave()
{
	$tempcacheid= time();
	if (be("post","app_vodviewtype") == "2"){
		SpecialChar( be("post","app_vodlistpath") );
		SpecialChar( be("post","app_vodpath") );
		SpecialChar( be("post","app_vodplaypath") );
		SpecialChar( be("post","app_voddownpath") );
		SpecialChar( be("post","app_vodtopicpath") );
	}
	if (be("post","app_artviewtype","post") == "2"){
		SpecialChar( be("post","app_artlistpath") );
		SpecialChar( be("post","app_artpath") );
		SpecialChar( be("post","app_arttopicpath") );
	}
	$vodsuffix = trim(be("post","app_vodsuffix"));
	if($vodsuffix!="htm" && $vodsuffix!="shtml") { $vodsuffix="html"; }
	$artsuffix = trim(be("post","app_artsuffix"));
	if($artsuffix!="htm" && $artsuffix!="shtml") { $artsuffix="html"; }
	
	$str = "<" . "?php" . "\n";
	$str.= "define(\"app_sitename\"," . chr(34) . trim(be("post","app_sitename")) . chr(34) . ");      //网站名称" . "\n";
	$str.= "define(\"app_installdir\"," . chr(34) . trim(be("post","app_installdir")) . chr(34) . ");        //网站路径" . "\n";
	$str.= "define(\"app_siteurl\"," . chr(34) . trim(be("post","app_siteurl")) . chr(34) . ");        //网站域名地址" . "\n";
	$str.= "define(\"app_keywords\"," . chr(34) . trim(be("post","app_keywords")) . chr(34) . ");        //网站关键字" . "\n";
	$str.= "define(\"app_description\"," . chr(34) . trim(be("post","app_description")) . chr(34) . ");        //网站描述信息" . "\n";
    $str.= "define(\"app_templatedir\"," . chr(34) . trim(be("post","app_templatedir")) . chr(34) . ");    //模板目录" . "\n";
	$str.= "define(\"app_htmldir\"," . chr(34) . trim(be("post","app_htmldir")) . chr(34) . ");      //模板html目录" . "\n";
	$str.= "define(\"app_cache\","  . intval(be("post","app_cache")) .  ");       //是否开启缓存" . "\n";
	$str.= "define(\"app_cachetime\"," . intval(be("post","app_cachetime"))  . ");       //缓存时间" . "\n";
	$str.= "define(\"app_cacheid\"," . chr(34) . trim($tempcacheid) . chr(34) . ");      //缓存标示" . "\n";
	$str.= "define(\"app_picpath\"," .  intval(be("post","app_picpath"))  . ");      //图片保存路径方式，0=默认,1=按月份,2=按日期,3=每目录500图片，超过自动创建新目录" . "\n";
	$str.= "define(\"app_dbtype\"," . chr(34) . trim(be("post","app_dbtype")) . chr(34) . ");      //数据库类型: 值分别为 access ; mssql" . "\n";
	$str.= "define(\"app_dbpath\"," . chr(34) . trim(be("post","app_dbpath")) . chr(34) . ");      //access数据库路径" . "\n";
	$str.= "define(\"app_dbserver\"," . chr(34) . trim(be("post","app_dbserver")) . chr(34) . ");      //SQL数据库服务器地址" . "\n";
	$str.= "define(\"app_dbname\"," . chr(34) . trim(be("post","app_dbname")) . chr(34) . ");      					  //SQL数据库名称" . "\n";
	$str.= "define(\"app_dbuser\"," . chr(34) . trim(be("post","app_dbuser")) . chr(34) . ");      //SQL数据库用户名" . "\n";
	$str.= "define(\"app_dbpass\"," . chr(34) . trim(be("post","app_dbpass")) . chr(34) . ");      					  //SQL数据库密码" . "\n";
	$str.= "define(\"app_tablepre\"," . chr(34) . trim(be("post","app_tablepre")) . chr(34) . ");      					  //表结构前缀" . "\n";
	$str.= "define(\"app_icp\"," . chr(34) . trim(be("post","app_icp")) . chr(34) . ");      //网站备案号" . "\n";
	$str.= "define(\"app_email\"," . chr(34) . trim(be("post","app_email")) . chr(34) . ");      //站长邮箱" . "\n";
	$str.= "define(\"app_qq\"," . chr(34) . trim(be("post","app_qq")) . chr(34) . ");      //站长qq" . "\n";
	$str.= "define(\"app_user\"," .  intval(be("post","app_user")) . ");          //是否开启会员验证0关闭，1开启" . "\n";
	$str.= "define(\"app_dynamiccache\"," .  intval(be("post","app_dynamiccache")) . ");          //是否开启动态文件缓存0关闭，1开启" . "\n";
	$str.= "define(\"app_timming\"," .  intval(be("post","app_timming")) . ");          //是否开启定时任务0关闭,1开启" . "\n";
	$str.= "define(\"app_install\",1);          //是否已经安装本程序0未安装，1已安装" . "\n";
	$str.= "define(\"app_safecode\"," . chr(34) . trim(be("post","app_safecode")) . chr(34) . ");      //后台登录安全码" . "\n\n";
	
	$str.= "define(\"app_vodplayersort\"," . intval(be("post","app_vodplayersort"))  . ");        //视频播放器显示顺序0添加顺序，1全局顺序" . "\n";
	$str.= "define(\"app_vodmaccjsname\"," . intval(be("post","app_vodmaccjsname"))  . ");        //联盟资源库入库时重名判断条件0名称，1名称+分类" . "\n";
	
    $str.= "define(\"app_vodviewtype\"," . intval(be("post","app_vodviewtype"))  . ");        //视频首页、地图 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=静态模式,3=rewrite伪静态" . "\n";
    $str.= "define(\"app_vodlistviewtype\"," . intval(be("post","app_vodlistviewtype"))  . ");        //视频列表页 浏览模式, 同上" . "\n";
    $str.= "define(\"app_vodtopicviewtype\"," . intval(be("post","app_vodtopicviewtype"))  . ");        //视频专题首页、列表 浏览模式, 同上" . "\n";
    $str.= "define(\"app_vodcontentviewtype\"," . intval(be("post","app_vodcontentviewtype"))  . ");        //视频内容页 浏览模式, 同上" . "\n";
    $str.= "define(\"app_vodplayviewtype\"," . intval(be("post","app_vodplayviewtype"))  . ");        //视频播放页 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=rewrite伪静态,3=静态每数据一页,4=静态每集一页,5=静态每组一页,6=静态全站一页" . "\n";
    $str.= "define(\"app_voddownviewtype\"," . intval(be("post","app_voddownviewtype"))  . ");        //视频播放页 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=rewrite伪静态,3=静态每数据一页,4=静态每集一页,5=静态每组一页,6=静态全站一页" . "\n";
    
	$str.= "define(\"app_vodsuffix\"," . chr(34) . $vodsuffix . chr(34) . ");      //生成视频静态文件后缀名" . "\n";
	$str.= "define(\"app_vodmakeinterval\"," . intval(be("post","app_vodmakeinterval"))  . ");        //生成视频静态页面间隔" . "\n";
    $str.= "define(\"app_playtype\"," . intval(be("post","app_playtype"))  . ");        //0=有内容页播放,1=无内容页播放" . "\n";
	$str.= "define(\"app_encrypt\"," . intval(be("post","app_encrypt"))  . ");        //是否加密播放地址 0不加密,1 escape加密,2 base64加密" . "\n";
	$str.= "define(\"app_playisopen\"," . intval(be("post","app_playisopen"))  . ");        //否弹窗播放 0普通播放，1弹窗播放" . "\n";
    $str.= "define(\"app_vodlistpath\"," . chr(34) . trim(be("post","app_vodlistpath")) . chr(34) . ");        //视频分类页面目录" . "\n";
	$str.= "define(\"app_vodpath\"," . chr(34) . trim(be("post","app_vodpath")) . chr(34) . ");         //视频内容页面目录" . "\n";
	$str.= "define(\"app_vodplaypath\"," . chr(34) . trim(be("post","app_vodplaypath")) . chr(34) . ");          //视频播放页面目录" . "\n";
	$str.= "define(\"app_voddownpath\"," . chr(34) . trim(be("post","app_voddownpath")) . chr(34) . ");          //视频下载页面目录" . "\n";
	$str.= "define(\"app_vodtopicpath\"," . chr(34) . trim(be("post","app_vodtopicpath")) . chr(34) . ");          //视频专题页目录" . "\n\n";
	
	$str.= "define(\"app_artviewtype\","  . intval(be("post","app_artviewtype"))  . ");        //文章首页、地图 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=静态模式,3=rewrite伪静态" . "\n";
	$str.= "define(\"app_artlistviewtype\","  . intval(be("post","app_artlistviewtype"))  . ");        //文章列表页 浏览模式, 同上" . "\n";
	$str.= "define(\"app_arttopicviewtype\","  . intval(be("post","app_arttopicviewtype"))  . ");        //文章专题首页、列表 浏览模式, 同上" . "\n";
	$str.= "define(\"app_artcontentviewtype\","  . intval(be("post","app_artcontentviewtype"))  . ");        //文章内容页 浏览模式, 同上" . "\n";
	$str.= "define(\"app_artsuffix\"," . chr(34) . $artsuffix . chr(34) . ");        //生成文章静态文件后缀名" . "\n";
	$str.= "define(\"app_artmakeinterval\","  . intval(be("post","app_artmakeinterval"))  . ");        //生成文章静态页面间隔" . "\n";	
    $str.= "define(\"app_artlistpath\"," . chr(34) . trim(be("post","app_artlistpath")) . chr(34) . ");        //文章列表目录" . "\n";
    $str.= "define(\"app_artpath\"," . chr(34) . trim(be("post","app_artpath")) . chr(34) . ");        //文章页面目录" . "\n";
	$str.= "define(\"app_arttopicpath\"," . chr(34) . trim(be("post","app_arttopicpath")) . chr(34) . ");          //文章专题页目录" . "\n\n";
	
	$str.= "define(\"app_watermark\"," .  intval(be("post","app_watermark")) . ");          //上传的图片是否添加水印 0关闭，1开启" . "\n";
	$str.= "define(\"app_waterlocation\"," .  intval(be("post","app_waterlocation")) . ");          //水印在图片的位置" . "\n";
	$str.= "define(\"app_waterfont\"," . chr(34) .  trim(be("post","app_waterfont")) . chr(34) . ");          //水印文字" . "\n"; 
	$str.= "define(\"app_gbook\","  . intval(be("post","app_gbook"))  . ");        //是否开启留言本 0关闭，1开启" . "\n";	
	$str.= "define(\"app_gbooknum\","  . intval(be("post","app_gbooknum"))  . ");        //留言本每页显示数量" . "\n";	
	$str.= "define(\"app_gbooktime\","  . intval(be("post","app_gbooktime"))  . ");        //留言时间间隔" . "\n";	
	$str.= "define(\"app_gbookverify\","  . intval(be("post","app_gbookverify"))  . ");        //留言时是否开启验证码0关闭,1开启" . "\n";	
	$str.= "define(\"app_gbookaudit\","  . intval(be("post","app_gbookaudit"))  . ");        //留言时是否需要审核0关闭,1开启" . "\n";	
	$str.= "define(\"app_comment\","  . intval(be("post","app_comment"))  . ");        //是否开启评论 0关闭，1开启" . "\n";	
	$str.= "define(\"app_commentnum\","  . intval(be("post","app_commentnum"))  . ");        //评论每页显示数量" . "\n";
	$str.= "define(\"app_commenttime\","  . intval(be("post","app_commenttime"))  . ");        //评论时间间隔" . "\n";
	$str.= "define(\"app_commentverify\","  . intval(be("post","app_commentverify"))  . ");        //评论时是否开启验证码0关闭,1开启" . "\n";
	$str.= "define(\"app_commentaudit\","  . intval(be("post","app_commentaudit"))  . ");        //评论时是否需要审核0关闭,1开启" . "\n";
	$str.= "define(\"app_mood\","  . intval(be("post","app_mood"))  . ");        //是否开启心情 0关闭，1开启" . "\n";	
	$str.= "define(\"app_pagenum\"," .  intval(be("post","app_pagenum")) . ");          //后台列表每页显示个数" . "\n";
	$str.= "define(\"app_filter\"," . chr(34) . trim(be("post","app_filter")) . chr(34) . ");          //评论和留言本过滤内容" . "\n";
	
	$str.= "define(\"app_reg\","  . intval(be("post","app_reg"))  . ");        //是否开启会员注册 1开启，0关闭" . "\n";	
	$str.= "define(\"app_regpoint\","  . intval(be("post","app_regpoint"))  . ");        //用户注册赠送点数" . "\n";	
	$str.= "define(\"app_regstate\","  . intval(be("post","app_regstate"))  . ");        //注册用户默认状态，1激活，0锁定" . "\n";	
	$str.= "define(\"app_popularize\","  . intval(be("post","app_popularize"))  . ");        //推广1个人获取积分数" . "\n";	
	$str.= "define(\"app_popularizestate\","  . intval(be("post","app_popularizestate"))  . ");        //是否开启推广赚积分1,开启 0关闭" . "\n";	
	$str.= "define(\"app_reggroup\","  . intval(be("post","app_reggroup"))  . ");        //用户注册默认会员组" . "\n\n";	
	$str.= "define(\"app_weekpoint\","  . intval(be("post","app_weekpoint"))  . ");        //包周所需积分数" . "\n\n";	
	$str.= "define(\"app_monthpoint\","  . intval(be("post","app_monthpoint"))  . ");        //包月所需积分数" . "\n\n";	
	$str.= "define(\"app_yearpoint\","  . intval(be("post","app_yearpoint"))  . ");        //包年所需积分数" . "\n\n";	
	
	$str.= "define(\"app_api\","  . intval(be("post","app_api"))  . ");        //接口API开关1开启，0关闭" . "\n";	
	$str.= "define(\"app_apicjflag\"," . chr(34) . trim(be("post","app_apicjflag"))  . chr(34) . ");        //联盟图片域名，以http:开头,/结尾，不包含upload目录" . "\n";	
	$str.= "define(\"app_apitypefilter\",". chr(34) .  trim(be("post","app_apitypefilter"))  .chr(34) . ");        //过滤分类参数，SQL查询条件例如 and t_hide=0" . "\n";	
	$str.= "define(\"app_apivodfilter\",". chr(34) .  trim(be("post","app_apivodfilter"))  . chr(34) . ");        //过滤数据参数，SQL查询条件例如 and d_hide=0" . "\n";	
	$str.= "define(\"app_apipagenum\","  . intval(be("post","app_apipagenum"))  . ");        //数据每页显示量" . "\n";	
	
	
	$str.= "?" . ">";
	fwrite(fopen("../inc/config.php","wb"),$str);
	
	//写入ftp配置
	$str = "<" . "?php" . "\n";
	$str.= "define(\"app_ftp\","  . intval(be("post","app_ftp"))  . ");        //ftp开关" . "\n";	
	$str.= "define(\"app_ftphost\",". chr(34) .  trim(be("post","app_ftphost"))  . chr(34) . ");        //ftp主机ip" . "\n";	
	$str.= "define(\"app_ftpuser\",". chr(34) .  trim(be("post","app_ftpuser"))  . chr(34) . ");        //ftp帐号" . "\n";	
	$str.= "define(\"app_ftppass\",". chr(34) .  trim(be("post","app_ftppass"))  . chr(34) . ");        //ftp密码" . "\n";	
	$str.= "define(\"app_ftpdir\",". chr(34) .  trim(be("post","app_ftpdir"))  . chr(34) . ");        //ftp目录" . "\n";	
	$str.= "define(\"app_ftpport\",". chr(34) .  trim(be("post","app_ftpport"))  . chr(34) . ");        //ftp端口" . "\n";	
	$str.= "define(\"app_ftpurl\",". chr(34) .  trim(be("post","app_ftpurl"))  . chr(34) . ");        //ftp远程附件访问地址" . "\n";	
	$str.= "define(\"app_ftpdel\","  . intval(be("post","app_ftpdel"))  . ");        //上传成功后是否删除本地文件" . "\n";	
	$str.= "?" . ">";
	fwrite(fopen("../inc/config.ftp.php","wb"),$str);
	
    echo "配置修改成功";
}

function configplaysave()
{
	$pwidth = be("post","pwidth");
	$pheight = be("post","pheight");
	$adsloadtime = be("post","adsloadtime");
	$autoFull = be("post","autofull");
	$popenW = be("post","popenW");
	$popenH = be("post","popenH");
	$maccmsplay = be("post","maccmsplay");
	$loadads = be("post","loadads");
	$loadpau = be("post","loadpau");
	$showlist = be("post","showlist");
	$colors = be("post","colors");
	
	$fpath = "../js/playerconfig.js.bak";
	if(!file_exists($fpath)){ $fpath .= ".bak"; }
	$fc = file_get_contents( $fpath );
	$fc = regReplace($fc,"var\spwidth\=(\d+?)\;","var pwidth=".$pwidth.";");
	$fc = regReplace($fc,"var\spheight\=(\d+?)\;","var pheight=".$pheight.";");
	$fc = regReplace($fc,"var\sadsloadtime\=(\d+?)\;","var adsloadtime=".$adsloadtime.";");
	$fc = regReplace($fc,"var\spopenW\=(\d+?)\;","var popenW=".$popenW.";");
	$fc = regReplace($fc,"var\spopenH\=(\d+?)\;","var popenH=".$popenH.";");
	$fc = regReplace($fc,"var\sautoFull\=(\d+?)\;","var autoFull=".$autoFull.";");
	$fc = regReplace($fc,"var\smaccmsplay\=(\d+?)\;","var maccmsplay=".$maccmsplay.";");
	$fc = regReplace($fc,"var\sloadads\=*\"*(\S+?)'*\"*\;","var loadads=\"".$loadads."\";");
	$fc = regReplace($fc,"var\sloadpau\=*\"*(\S+?)'*\"*\;","var loadpau=\"".$loadpau."\";");
	$fc = regReplace($fc,"var\sshowlist\=(\d+?)\;","var showlist=".$showlist.";");
	$fc = regReplace($fc,"var\scolors\=*\"*(\S+?)'*\"*\;","var colors=\"".$colors."\";");
	
	fwrite(fopen("../js/playerconfig.js","wb"),$fc);
    echo "配置修改成功";
}

function configplay()
{
	$fpath = "../js/playerconfig.js";
	if(!file_exists($fpath)){
		$fpath .= ".bak";
	}
	$fc = file_get_contents( $fpath );
	$pwidth = regMatch($fc,"var\spwidth\=(\d+?)\;");
	$pheight = regMatch($fc,"var\spheight\=(\d+?)\;");
	$adsloadtime = regMatch($fc,"var\sadsloadtime\=(\d+?)\;");
	$popenW = regMatch($fc,"var\spopenW\=(\d+?)\;");
	$popenH = regMatch($fc,"var\spopenH\=(\d+?)\;");
	$autoFull= regMatch($fc,"var\sautoFull\=(\d+?)\;");
	$maccmsplay=regMatch($fc,"var\smaccmsplay\=(\d+?)\;");
	$loadads=regMatch($fc,"var\sloadads\=*\"*(\S+?)'*\"*\;");
	$loadpau=regMatch($fc,"var\sloadpau\=*\"*(\S+?)'*\"*\;");
	$showlist=regMatch($fc,"var\sshowlist\=(\d+?)\;");
	$colors=regMatch($fc,"var\scolors\=*\"*(\S+?)'*\"*\;");
?>
<script language="javascript">
var a = <?php echo $maccmsplay?>;
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			pwidth:{
				required:true,
				number:true
			},
			pheight:{
				required:true,
				number:true
			},
			popenW:{
				required:true,
				number:true
			},
			popenH:{
				required:true,
				number:true
			},
			adsloadtime:{
				required:true,
				number:true
			},
			colors:{
				required:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	
	$("#t1 input:radio").click(function(){
		var v = $("input[name='maccmsplay']:checked").val();
		if(v==3){
			$("#s1").show();
		}
		else{
			$("#s1").hide();
		}
	});
	if(a==3){
		$("#s1").show();
	}
});
function setColor(v)
{
	switch(v)
	{
		case 2: v="EFF4F7,000000,666666,E4E4E4,000000,FF0000,FF0000,DBEBFE,458CE4,DBEBFE,FFFFFF,458CE4,DBEBFE,DBEBFE,fcfcfc";break;
		case 3: v="D8CFDF,000000,666666,E4E4E4,000000,FF0000,FF0000,D8CFDF,926C92,BEAFC9,FFFFFF,926C92,BEAFC9,BEAFC9,fcfcfc";break;
		case 4: v="D7E7B6,000000,666666,E4E4E4,000000,FF0000,FF0000,9EC14C,A3C656,BAD480,FFFFFF,A3C656,BAD480,BAD480,fcfcfc";break;
		default: v="000000,F6F6F6,F6F6F6,333333,666666,FFFFF,FF0000,2c2c2c,ffffff,a3a3a3,2c2c2c,adadad,adadad,48486c,fcfcfc";break;
	}
	$("#colors").val(v);
}
</script>
<body>
<form method="POST" action="?action=configplaysave" id="form1" name="form1">
  <table class="tb">
	<tr>
      <td width="20%">播放器宽度：</td>
      <td><input name="pwidth" type="text" id="pwidth" value="<?php echo $pwidth?>" size="10"> 
        <font color="#FF0000">＊</font> 例如: 540</td>
    </tr>
    <tr>
      <td>播放器高度：</td>
      <td><input name="pheight" type="text" id="pheight" value="<?php echo $pheight?>" size="10">
        <font color="#FF0000">＊</font> 例如: 460</td>
    </tr>
	<tr>
      <td>弹窗窗口宽度：</td>
      <td><input name="popenW" type="text" id="popenW" value="<?php echo $popenW?>" size="10"> 
        <font color="#FF0000">＊</font> 例如: 500</td>
    </tr>
    <tr>
      <td>弹窗窗口高度：</td>
      <td><input name="popenH" type="text" id="popenH" value="<?php echo $popenH?>" size="10">
        <font color="#FF0000">＊</font> 例如: 400</td>
    </tr>
    <tr>
    <tr>
      <td>播放前广告时间：</td>
      <td>
          <input name="adsloadtime" type="text" id="adsloadtime" value="<?php echo $adsloadtime?>" size="10">
           <font color="#FF0000">＊</font> 1000表示1秒,无播放前广告请填写0</td>
    </tr>
    <tr>
      <td>预加载广告地址：</td>
      <td>
          <input name="loadads" type="text" id="loadads" value="<?php echo $loadads?>" size="70">
           <font color="#FF0000">＊</font>不要出现" ' 等特殊字符号</td>
    </tr>
    <tr>
      <td>缓冲广告地址：</td>
      <td>
          <input name="loadpau" type="text" id="loadpau" value="<?php echo $loadpau?>" size="70">
           <font color="#FF0000">＊</font>不要出现" ' 等特殊字符号</td>
    </tr>
    <tr>
      <td>是否自动全屏播放：</td>
      <td>
        关闭<input type="radio" name="autofull" value="0" <?php if( $autoFull == 0 ){echo "checked";}?>>
        开启<input type="radio" name="autofull" value="1" <?php if( $autoFull == 1 ){echo "checked";}?>>
        (此功能仅支持部分播放器)</td>
    </tr>
    <tr>
      <td>播放器文件：</td>
      <td id="t1">
      	<br><input type="radio" name="maccmsplay" value="1" <?php if( $maccmsplay == 1 ){echo "checked";}?>><font color=black>1，本地播放器文件(播放器代码可能失效)</font>
        <br><input type="radio" name="maccmsplay" value="2" <?php if( $maccmsplay == 2 ){echo "checked";}?>><font color=red>2，官方普通窗口版本(去广告、播放器代码随时更新不失效)</font>
        <br><input type="radio" name="maccmsplay" value="3" <?php if( $maccmsplay == 3 ){echo "checked";}?>><font color=red>3，官方美化窗口版本(去广告、播放器代码随时更新不失效)</font>
        <span id="s1" style="display:none">
        <br>
        列表开关：关闭<input type="radio" name="showlist" value="0" <?php if( $showlist == 0 ){echo "checked";}?>>
        开启<input type="radio" name="showlist" value="1" <?php if( $showlist == 1 ){echo "checked";}?>>
        <br>
        播放器颜色配置：[<a href="javascript:void(0)" onclick="setColor(1);return false;">全黑色</a>] [<a href="javascript:void(0)" onclick="setColor(2);return false;">浅蓝色</a>] [<a href="javascript:void(0)" onclick="setColor(3);return false;">浅紫色</a>] [<a href="javascript:void(0)" onclick="setColor(4);return false;">浅绿色</a>]<br>
        <input type="text" size="140" id="colors" name="colors" value="<?php echo $colors?>" />
        <br><font color=red>颜色使用16进制表示法，不带#号，以逗号分割，一共15个可配置颜色!
        <br>依次是：背景色，文字颜色，链接颜色，分组标题背景色，分组标题颜色，当前分组标题颜色，当前集数颜色，集数列表滚动条凸出部分的颜色，滚动条上下按钮上三角箭头的颜色，滚动条的背景颜色，滚动条空白部分的颜色，滚动条立体滚动条阴影的颜色 ，滚动条亮边的颜色，滚动条强阴影的颜色，滚动条的基本颜色
        </span>
        </td>
    </tr>
	
     <tr align="center">
      <td colspan="2">
          <input name="btnSave" type="submit" id="btnSave" class="input" value="更新参数">
          如出错请手动修改网站 js 目录下的 playerconfig.js 文件     </td>
    </tr>
</table>
</form>
</body>
</html>
<?php
}

function configconnectsave()
{
	$str = "<" . "?php" . "\n";
	$str.= "define(\"QQ_OAUTH_CONSUMER_KEY\"," . chr(34) . trim(be("post","QQ_OAUTH_CONSUMER_KEY")) . chr(34) . ");      //APP ID" . "\n";
	$str.= "define(\"QQ_OAUTH_CONSUMER_SECRET\"," . chr(34) . trim(be("post","QQ_OAUTH_CONSUMER_SECRET")) . chr(34) . ");      //APP KEY" . "\n";
	$str.= "define(\"QQ_OAUTH_NONCE\",rand(100000, 999999));       //时间戳" . "\n";
	$str.= "define(\"QQ_TIMESTAMP\",time());       " . "\n";
	$str.= "define(\"QQ_CALLBACK_URL\"," . chr(34) . 'http://' . chr(34)  . '.$_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] .' . chr(34) . "?action=reg&ref=qqlogged" . chr(34) . ");      //返回地址" . "\n";
	$str.= "?" . ">";
	
	fwrite(fopen("../user/qqconfig.php","wb"),$str);
    echo "配置修改成功";
}

function configconnect()
{
	require_once ("../user/qqconfig.php");
?>
<script language="javascript">
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			QQ_OAUTH_CONSUMER_KEY:{
				required:true
			},
			QQ_OAUTH_CONSUMER_SECRET:{
				required:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	
});
</script>
<body>
<form method="POST" action="?action=configconnectsave" id="form1" name="form1">
  <table class="tb">
     <tr align="left">
      <td colspan="2">
          QQ互联登陆配置选项：【<a target="_blank" href="http://connect.qq.com/?maccms">点击进入注册</a>】
      	  &nbsp;&nbsp;&nbsp;&nbsp;<input name="btnSave" type="submit" id="btnSave" class="input" value="更新参数">
          如出错请手动修改网站 user 目录下的 qqconfig.php 文件
      </td>
    </tr>
	<tr>
      <td width="20%">APP ID：</td>
      <td><input name="QQ_OAUTH_CONSUMER_KEY" type="text" id="QQ_OAUTH_CONSUMER_KEY" value="<?php echo QQ_OAUTH_CONSUMER_KEY?>" size="50"> 
        <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td>APP KEY：</td>
      <td><input name="QQ_OAUTH_CONSUMER_SECRET" type="text" id="QQ_OAUTH_CONSUMER_SECRET" value="<?php echo QQ_OAUTH_CONSUMER_SECRET?>" size="50">
        <font color="#FF0000">＊</font> </td>
    </tr>
</table>
</form>
</body>
</html>
<?php
}

function configbuysave()
{
	$str = "<" . "?php" . "\n";
	$str.= "define(\"app_buymin\","  . trim(be("post","app_buymin")) .  ");       //最小充值金额单位RMB元" . "\n";
	$str.= "define(\"app_buyexc\"," . trim(be("post","app_buyexc"))  . ");       //1元RMB兑换多少个积分" . "\n";
	$str.= "define(\"app_buyid\"," . chr(34) . trim(be("post","app_buyid")) . chr(34) . ");      //支付接口作者ID" . "\n";
	$str.= "define(\"app_buykey\"," . chr(34) . trim(be("post","app_buykey")) . chr(34) . ");      //支付接口密钥key" . "\n";
	$str.= "?" . ">";
	fwrite(fopen("../user/buyconfig.php","wb"),$str);
	echo "配置修改成功";
}

function configbuy()
{
	require_once ("../user/buyconfig.php");
?>
<script language="javascript">
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			app_buyid:{
				required:true
			},
			app_buykey:{
				required:true
			},
			app_buymin:{
				required:true,
				number:true
			},
			app_buyexc:{
				required:true,
				number:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	
});
</script>
<body>
<form method="POST" action="?action=configbuysave" id="form1" name="form1">
  <table class="tb">
     <tr align="left">
      <td colspan="2">
          在线支付接口注册：【<a target="_blank" href="http://pay.yinshengvip.com/mobile_reg.php?merchant=ksyitnnoudo6oght">点击进入注册</a>】 &nbsp;&nbsp;&nbsp;&nbsp;<input name="btnSave" type="submit" id="btnSave" class="input" value="更新参数">
          如出错请手动修改网站 user 目录下的 buyconfig.php 文件 </td>
    </tr>
	<tr>
      <td width="20%">作者ID：</td>
      <td><input name="app_buyid" type="text" id="app_buyid" value="<?php echo app_buyid?>" size="50"> 
        <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td>密钥KEY：</td>
      <td><input name="app_buykey" type="text" id="app_buykey" value="<?php echo app_buykey?>" size="50">
        <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td width="20%">最小充值金额（单位RMB元）：</td>
      <td><input name="app_buymin" type="text" id="app_buymin" value="<?php echo app_buymin?>" size="50"> 
        <font color="#FF0000">＊</font> </td>
    </tr>
    <tr>
      <td>兑换比例（1元RMB兑换多少个积分）：</td>
      <td><input name="app_buyexc" type="text" id="app_buyexc" value="<?php echo app_buyexc?>" size="50">
        <font color="#FF0000">＊</font>  </td>
    </tr>
     <tr>
      <td colspan="2">
		<font color=red>提示信息：本接口支持信用卡支付、提现等功能，严禁用于违法操作，如有问题本程序概不负责！！</font>
	    <br>普通支付：1%手续费；快捷支付：1.5%手续费； 
	  </td>
    </tr>
</table>
</form>
</body>
</html>
<?php
}
?>