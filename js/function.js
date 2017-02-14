//==========================================================================================
//软件名称：魅魔MacCMS
//开发作者：MagicBlack    '官方网站：http://www.maccms.com/
//Copyright (C) 2009 - 2010 ... maccms.com  All Rights Reserved.
//郑重声明:
//    1、任何个人或组织不得以盈利为目的发布,修改,本软件及其他副本上一切关于版权的信息；
//    2、本人保留此软件的法律追究权利；
//==========================================================================================

String.prototype.replaceAll  = function(s1,s2){
   return this.replace(new RegExp(s1,"gm"),s2);
}
String.prototype.trim=function(){
   return this.replace(/(^\s*)|(\s*$)/g, "");
}

eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('o.5=B(9,b,2){6(h b!=\'E\'){2=2||{};6(b===n){b=\'\';2.3=-1}4 3=\'\';6(2.3&&(h 2.3==\'j\'||2.3.k)){4 7;6(h 2.3==\'j\'){7=w u();7.t(7.q()+(2.3*r*l*l*x))}m{7=2.3}3=\'; 3=\'+7.k()}4 8=2.8?\'; 8=\'+2.8:\'\';4 a=2.a?\'; a=\'+2.a:\'\';4 c=2.c?\'; c\':\'\';d.5=[9,\'=\',C(b),3,8,a,c].y(\'\')}m{4 e=n;6(d.5&&d.5!=\'\'){4 g=d.5.A(\';\');s(4 i=0;i<g.f;i++){4 5=o.z(g[i]);6(5.p(0,9.f+1)==(9+\'=\')){e=D(5.p(9.f+1));v}}}F e}};',42,42,'||options|expires|var|cookie|if|date|path|name|domain|value|secure|document|cookieValue|length|cookies|typeof||number|toUTCString|60|else|null|jQuery|substring|getTime|24|for|setTime|Date|break|new|1000|join|trim|split|function|encodeURIComponent|decodeURIComponent|undefined|return'.split('|'),0,{}));

eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('(7($){$.Q.P=7(t){8 1={d:0,G:0,e:"o",B:"S",3:5};6(t){$.J(1,t)}8 p=c;6("o"==1.e){$(1.3).v("o",7(e){8 F=0;p.C(7(){6($.s(c,1)||$.x(c,1)){}f 6(!$.n(c,1)&&!$.m(c,1)){$(c).w("u")}f{6(F++>1.G){h E}}});8 H=$.N(p,7(9){h!9.k});p=$(H)})}c.C(7(){8 2=c;6(j==$(2).b("r")){}6("o"!=1.e||j==$(2).b("i")||1.z==$(2).b("i")||($.s(2,1)||$.x(2,1)||$.n(2,1)||$.m(2,1))){6(1.z){$(2).b("i",1.z)}f{$(2).Z("i")}2.k=E}f{$(2).b("i",$(2).b("r"));2.k=D}$(2).11("u",7(){6(!c.k){$("<Y />").v("U",7(){$(2).V().b("i",$(2).b("r"))[1.B](1.W);2.k=D}).b("i",$(2).b("r"))}});6("o"!=1.e){$(2).v(1.e,7(e){6(!2.k){$(2).w("u")}})}});$(1.3).w(1.e);h c};$.n=7(9,1){6(1.3===j||1.3===5){8 4=$(5).y()+$(5).I()}f{8 4=$(1.3).g().q+$(1.3).y()}h 4<=$(9).g().q-1.d};$.m=7(9,1){6(1.3===j||1.3===5){8 4=$(5).A()+$(5).M()}f{8 4=$(1.3).g().l+$(1.3).A()}h 4<=$(9).g().l-1.d};$.s=7(9,1){6(1.3===j||1.3===5){8 4=$(5).I()}f{8 4=$(1.3).g().q}h 4>=$(9).g().q+1.d+$(9).y()};$.x=7(9,1){6(1.3===j||1.3===5){8 4=$(5).M()}f{8 4=$(1.3).g().l}h 4>=$(9).g().l+1.d+$(9).A()};$.J($.10[\':\'],{"T-L-4":"$.n(a, {d : 0, 3: 5})","R-L-4":"!$.n(a, {d : 0, 3: 5})","O-K-4":"$.m(a, {d : 0, 3: 5})","l-K-4":"!$.m(a, {d : 0, 3: 5})"})})(X);',62,64,'|settings|self|container|fold|window|if|function|var|element||attr|this|threshold|event|else|offset|return|src|undefined|loaded|left|rightoffold|belowthefold|scroll|elements|top|original|abovethetop|options|appear|bind|trigger|leftofbegin|height|placeholder|width|effect|each|true|false|counter|failurelimit|temp|scrollTop|extend|of|the|scrollLeft|grep|right|lazyload|fn|above|show|below|load|hide|effectspeed|jQuery|img|removeAttr|expr|one'.split('|'),0,{}));

function copyData(text){
	if (window.clipboardData){
		window.clipboardData.setData("Text",text);
	} 
	else{
		var flash_copy = null;
		if( !$('#flash_copy') ){
			var flash_copy = document.createElement("div");
	    	flash_copy.id = 'flash_copy';
	    	document.body.appendChild(flash_copy);
		}
		flash_copy = $('#flash_copy');
		flash_copy.innerHTML = '<embed src='+maccms_path+'"images/_clipboard.swf" FlashVars="clipboard='+escape(text)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
	}
	alert("复制成功");
	return true;
}

function sitehome(obj,strUrl){
    try{
    	obj.style.behavior='url(#default#homepage)';
    	obj.setHomePage(strUrl);
	}
    catch(e){
         if(window.netscape){
         	try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}
            catch (e){alert("此操作被浏览器拒绝！请手动设置");}
            var moz = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
            moz.setCharPref('browser.startup.homepage',strUrl);
          }
     }
}
function sitefav(strUrl,strTitle){
	try{ window.external.addFavorite(strUrl, strTitle);}
	catch (e){
		try{window.sidebar.addPanel(strTitle, strUrl, "");}
		catch (e){alert("加入收藏出错，请使用键盘Ctrl+D进行添加");}
	}
}
function OpenWindow1(url,w,h){
	window.open(url,'openplay1','toolbars=0, scrollbars=0, location=0, statusbars=0,menubars=0,resizable=yes,width='+w+',height='+h+'');
}

function creatediv(z,w,h){
	$('<div id="adddiv"></div>')
	.css('top', '0')
	.css('width',w+"px")
	.css('height',h+"px")
	.css('z-index',z)
	.css('filter','Alpha(Opacity=0)')
	.css('position','absolute')
	.appendTo("body");
	$('<div id="confirm"></div>')
	.css('position','absolute')
	.css('z-index',z+1)
	.css('top','200px')
	.css('left','300px')
	.appendTo("body");
}
function closew(){	$("#confirm").remove(); }

function getHit(action,id){
	$.get(maccms_path+"inc/ajax.php?action="+action+"hit&id="+id,function(obj){
		if(obj=="err"){ $('#hit').html('发生错误');}else{ $('#hit').html(obj);}
	});
}
function getGoodBad(action,id){
	$.get(maccms_path+"inc/ajax.php?action="+action+"num&id="+id+"&rnd="+Math.random(),function(obj){
		if(obj=="err"){ $('#'+action+'_num').html('发生错误');}else{ $('#'+action+'_num').html(obj);}
	});
}
function vodError(id,name){
	location.href= maccms_path + "gbook.php?id="+id+"&name="+ encodeURI(name);
}
function vodError2(id,name){
	OpenWindow1( maccms_path+"js/err.html?id="+id+"&name="+ encodeURI(name),400,220);
}
function userFav(id){
	$.get(maccms_path+"inc/ajax.php?action=userfav&id="+id+"&rnd="+Math.random(),function(obj){
		if(obj=="ok"){
			alert("会员收藏成功");
		}
		else if(obj=="login"){
			alert('请先登录会员中心再进行会员收藏操作');
		}
		else if(obj=="haved"){
			alert('您已经收藏过了');
		}
		else{
			alert('发生错误');
		}
	});
}
function desktop(name){
	location.href= maccms_path + "inc/ajax.php?action=desktop&name="+encodeURI(name)+"&url=" + encodeURI(location.href);
}
function vodGood(id,div){
	$.get(maccms_path+"inc/ajax.php?id="+id+"&action=vodgood&rnd="+Math.random(),function (obj){
		if (!isNaN(obj)){
			try{ $('#'+div).html(obj);}catch(e) {}
			alert('感谢你的支持！');
		}
		else if(obj=="haved"){alert('您已经顶过了！');}
		else{alert('没有顶上去啊!');}
	});	
}

function vodBad(id,div){
	$.get(maccms_path+"inc/ajax.php?id="+id+"&action=vodbad&rnd="+Math.random(),function (obj){
		if(!isNaN(obj)){
			try{$('#'+div).html(obj);}catch(e) {}
			alert('踩我好悲哀！');
		}
		else if(obj=="haved"){alert('您已经踩过了！');}
		else{alert('没有踩下去啊');}
	});
}
function getScore(action,id){
	$.get(maccms_path+"inc/ajax.php?action=getscore&ac2="+action+"&id="+id+"&rnd="+Math.random(),function(obj){
		if(obj=="err"){ $('#score'+action+'_num').html('发生错误');}else{ $('#score'+action+'_num').html(obj);}
	});
}
function vodScoreMark(id,sc,s){
	var pjf = (parseInt(s / sc * 10) * 0.1) || 0;
	pjf = pjf.toFixed(1);
	document.write("<div id=\"vod-rating\" class=\"vod-rating\"></div>");
	$.ajax({ cache: false, dataType: 'html', type: 'GET', url: maccms_path +'inc/ajax.php?id='+id+'&action=getscore&ac2=pjf',
	success: function(r){
		pjf = Number(r);
	},
	complete:function(a,b){
		$("#vod-rating").rater({maxvalue:10,curvalue:pjf ,style:'inline-normal',url: maccms_path +'inc/ajax.php?id='+id+'&action=score&score='});
	}});
}
function vodScoreMark1(id,sc,s){
	var pjf = (parseInt(s / sc * 10) * 0.1) || 0;
	pjf = pjf.toFixed(1);
	var str="";
	str = '<div style="padding:5px 10px;border:1px solid #CCC"><div style="color:#000"><strong>我要评分(感谢参与评分，发表您的观点)</strong></div><div>共 <strong style="font-size:14px;color:red" id="rating_msg1"> '+sc+' </strong> 个人评分， 平均分 <strong style="font-size:14px;color:red" id="rating_msg2"> '+pjf+' </strong>， 总得分 <strong style="font-size:14px;color:red" id="rating_msg3"> '+s+' </strong></div><div>';
	for(var i=1;i<=10;i++){
		str += '<input type="radio" name="score" id="rating'+i+'" value="1"/><label for="rating'+i+'">'+i+'</label>';
	}
	document.write(str +'&nbsp;<input type="button" value=" 评 分 " id="scoresend" style="width:55px;height:21px"/></div></div>');
	
	$.ajax({ cache: false, dataType: 'html', type: 'GET', url: maccms_path +'inc/ajax.php?id='+id+'&action=getscore&ac2=pjf&ac3=all',
		success: function(r){
			var arr = r.split(",");
			$("#rating_msg1").html(arr[1]);
			$("#rating_msg2").html(arr[2]);
			$("#rating_msg3").html(arr[0]);
		}
	});
	
	$("#scoresend").click(function(){
		var rc=false;
		for(var i=1;i<=10;i++){
			if( $('#rating'+i).get(0).checked){
				rc=true;
				break;
			}
		}
		if(!rc){alert('你还没选取分数');return;}
		
		$.get(maccms_path +'inc/ajax.php?id='+id+'&action=score&ac3=all&score='+ i ,function (obj){
			if(obj.indexOf("haved")!=-1){
				alert('你已经评过分啦');
			}else{
				var arr = obj.split(",");
				$("#rating_msg1").html(arr[1]);
				$("#rating_msg2").html(arr[2]);
				$("#rating_msg3").html(arr[0]);
				alert('感谢你的参与!');
			}
			return false;
		});
	});
}

function getComment(url){
	$.get(url,function(obj){
		if (obj=="err"){
			$("#maccms_comment").html("<font color='red'>发生错误</font>");
		}else{
			$("#maccms_comment").html(obj);
		}
	});
}
function getGbook(id,name){
	$.get(maccms_path + "plus/gbook/?action=main&id="+id+"&name="+ encodeURI(name),function(obj){
		if (obj=="err"){
			$("#maccms_gbook").html("<font color='red'>发生错误</font>");
		}else{
			$("#maccms_gbook").html(obj);
		}
	});
}

function history_New(vurl, vname) {
    var urla,
    flag;
    flag = true;
    for (i = 0; i < 20; i++) {
        urla = $.cookie("vurl" + i);
        if (urla == vurl) {
            $.cookie("vurl" + i, vurl, {
                path: '/'
            });
            $.cookie("vname" + i, vname, {
                path: '/'
            });
            flag = false;
            break
        }
    }
    if (flag == true) {
        for (i = 20 - 1; i > 0; i--) {
            if ($.cookie("vurl" + (i - 1)) != null) {
                $.cookie("vurl" + i, $.cookie("vurl" + (i - 1)), {
                    path: '/'
                });
                $.cookie("vname" + i, $.cookie("vname" + (i - 1)), {
                    path: '/'
                })
            }
        }
        $.cookie("vurl0", vurl, {
            path: '/'
        });
        $.cookie("vname0", vname, {
            path: '/'
        })
    }
}
function history_Look(num) {
    var i;
    var tvurl,
    tvname,
    s,
    str;
    str = "<li><a href='{vurl}' target='_blank'>{vname}</a></li>";
    for (i = 0; i < 20; i++) {
        tvurl = $.cookie("vurl" + i);
        tvname = $.cookie("vname" + i);
        if (tvurl != null && tvname != null) {
            s = str.replace(/{vname}/gi, tvname).replace(/{vurl}/gi, tvurl);
            document.writeln(s)
        }
        if (i == num) {
            break
        }
    }
}
function history_del(){
    var name,
    name1,
    domain,
    path;
    path = "/";
    domain = "";
    for (i = 0; i < 20; i++) {
        name = "vurl" + i;
        name1 = "vname" + i;
        if ($.cookie(name)) {
            document.cookie = name + "=" + ((path) ? "; path=" + path: "") + ((domain) ? "; domain=" + domain: "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
            document.cookie = name1 + "=" + ((path) ? "; path=" + path: "") + ((domain) ? "; domain=" + domain: "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT"
        }
    }
    window.location.reload()
}
window.onload = function(){
try {
	if($("#keyword").length>0){
		try{
			$("#keyword").autocomplete(maccms_path + "inc/ajax.php?action=suggest",{
				width: 175,scrollHeight: 300,minChars: 1,matchSubset: 1,max: 10,cacheLength: 10,multiple: true,matchContains: true,autoFill: false,dataType: "json",
				parse:function(obj) {
					if(obj.status){
						var parsed = [];
						for (var i = 0; i < obj.data.length; i++) {
							parsed[i] = {
								data: obj.data[i],value: obj.data[i].d_name,result: obj.data[i].d_name
							};
						}
						return parsed;
					}else{
						return {data:'',value:'',result:''};
					}
				},
				formatItem: function(row,i,max) {
					return row.d_name;
				},
				formatResult: function(row,i,max) {
					return row.d_name;
				}
			}).result(function(event, data, formatted) {
				location.href = maccms_path+"search.php?keyword=" + encodeURIComponent(data.d_name);
			});
			}catch(e){}
	}
    var timmingRun = (new Image());
    timmingRun.src = maccms_path + 'inc/timming.php?t=' + Math.random()
} catch(e) {}
};