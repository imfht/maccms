//2011/10/14 hyp
var kuniao_se_dt = new Date();
var kuniao_se_t = kuniao_se_dt.getTime();
var kuniao_se_date  = kuniao_se_dt.getFullYear() +""+ kuniao_se_dt.getMonth() +""+ kuniao_se_dt.getDate();
function kuniao_se_getcookie1(offset) { var endstr = document.cookie.indexOf (";", offset);if (endstr == -1) endstr = document.cookie.length; return unescape(document.cookie.substring(offset, endstr)); }
function kuniao_se_getcookie2(name) { var arg = name + "="; var alen = arg.length; var clen = document.cookie.length; var i = 0; while (i < clen) { var j = i + alen; if (document.cookie.substring(i, j) == arg) return kuniao_se_getcookie1(j); i = document.cookie.indexOf(" ", i) + 1; if (i == 0) break; } return null; }
function kuniao_se_setcookie(){ 
    var exp  = new Date(); 
    var exptime = 2592000000;
    exp.setTime(exp.getTime() + exptime);	
    document.cookie = "kuniao_SE_TT" + "=" + kuniao_se_date + ";path=/;expires=" + exp.toGMTString() + ";domain=sohu.com;";  
}
var kuniao_se_timeoffset = 0;
var kuniao_se_cdate = kuniao_se_getcookie2('kuniao_SE_TT');
var kuniao_se_ua = navigator.userAgent.toLowerCase();
if (kuniao_se_cdate != kuniao_se_date){
    var kuniao_randnum = Math.floor(Math.random()*500+1);
    var kuniao_se_pl = escape(window.location.href);
    var kuniao_se_obj = document.createElement('img');
    function kuniao_se_clink(linkId){   
        var obj = document.getElementById(linkId);  
        if (document.createEvent) {  
            window.open(obj.href);  
        } else if (document.createEventObject) {  
            obj.click();  
        }  
    }  
    var kuniao_se_obj = document.createElement("img");
  function kuniao_se_clk(type){
       if(kuniao_randnum == 1) {
           var btn = "se";	
           if (type == "ku") {
               btn = "ku";	
           }
      }
       kuniao_se_setcookie();
       if (type == "ku") {
          kuniao_se_clink("ku_dl");
       } else {
           kuniao_se_clink("kuniao_dl");
       }
        return false;
    }
 function kuniao_se_close(){
        document.getElementById("kuniao_se_tgbar").style.display = "none";
        if(kuniao_randnum == 1) {
        }
       kuniao_se_setcookie();
    }
    function kuniao_se_checkie(uastr,strlist){
        for(n in strlist) {
            if(uastr.indexOf(strlist[n]) > 0) {
                return true;
            }
        }
        return false;
    }
    var kuniao_se_texttype = "";
    var marginleft = 75 
    var kuniao_se_text;
    var imgPath = "/template/default/images/";
    var kuniao_html = ['<div style="text-align:center;margin:0px auto 2px auto;clear:both;background:url(' , imgPath , 'bg_final1.gif) repeat-x;width:980px;color:#000;font-size:13px;height:36px;z-index:21474836471" id="kuniao_se_tgbar">' 
        ,'<div style="float:left;cursor:pointer;width:910px;text-align:center;border-left:solid #efbf00 1px;height:32px">'
        ,'<span onclick="kuniao_se_clk(\'se\')" style="float:left;background:url('
        , imgPath ,'kuniao.gif) no-repeat 0px 2px;padding:9px 0px 0px 28px;height:23px;margin-left:'
        , marginleft , 'px">苹果CMS提示：'];

    if( /ipad|iphone|ipod/i.test( kuniao_se_ua ) ){
    }else{
        kuniao_se_text = '电影程序(ASP + PHP)，完全开源、强劲功能、卓越性能、安全健壮。';
        kuniao_html.push( kuniao_se_text , '</span>'
            ,'<span onclick="kuniao_se_clk(\'se\')" style="float:left;color:#003299;text-decoration:underline;margin-left:15px;background:url(' , imgPath , 'mac.ico) no-repeat 0px 7px;padding:8px 0px 0px 20px;height:24px">苹果MacCMS V7.X下载</span>'
            ,'<span onclick="kuniao_se_clk(\'ku\')" style="float:left;color:#003299;text-decoration:underline;margin-left:15px;background:url(' , imgPath , 'mac1.ico) no-repeat 0px 7px;padding:8px 0px 0px 20px;height:24px">苹果桌面播放器下载</span>'
            ,'</div><div style="float:right;width:20px;cursor:pointer;height:28px;padding:4px 8px 0px 8px;border-right:solid #efbf00 1px" onclick="kuniao_se_close();">'
            ,'<img src="' , imgPath , 'close_final1.gif" style="border:none;margin:4px 0px 0px 0px;" /></div>'
,'</div><a href="http://www.kuniaos.com" id="kuniao_dl"></a>'//苹果MacCMs 下载链接
,'<a href="http://www.kuniaos.com" id="ku_dl"></a>');//本地播放器链接
    }
    document.write( kuniao_html.join('') ); 
    try{ kuniao_se_load(); }catch(e){};
}
