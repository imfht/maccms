$.fn.ajaxSubmit = function(callback){
	return this.each(function(){
	var _this = $(this);
	_this.submit(function(){
		$('.input_error,.textarea_error,.ul_error',_this).removeClass('input_error').removeClass('textarea_error').removeClass('ul_error');
		var button = $('button[type=submit]',this).attr('disabled',true);
		var url = _this.attr('action'); if (url==''||typeof url=='undefined') { url = self.location.href; }
		$.ajax({
		cache: false, url: url, dataType:'json',
		type: _this.attr('method') && _this.attr('method').toUpperCase() || 'POST',
		data: _this.serializeArray(),
		success: function(data, status, xhr){
			if ($.isFunction(callback)) callback.call(_this,data, status, xhr);
			},
			complete: function(){
			button.attr('disabled',false);
			}
		});
		return false;
		});
	});
};
    
document.write([
	'<a name="cmt_post"></a>',
	'<div class="gbooks" id="cmt_gbook_form">',
            '<form action="index.php?action=save" method="post" name="cmt_form" id="cmt_form">',
                '<div class="top"><strong>网友留言:</strong><a href="#cmt_list">共<em>0</em>条留言，已审核<em>0</em>条留言。</a></div>',
                '<div class="info">',
                    '<p><img src="../../images/face/1.gif" onclick="addEm(1);"/><img src="../../images/face/2.gif" onclick="addEm(2);"/><img src="../../images/face/3.gif" onclick="addEm(3);"/><img src="../../images/face/4.gif" onclick="addEm(4);"/><img src="../../images/face/5.gif" onclick="addEm(5);"/><img src="../../images/face/6.gif" onclick="addEm(6);"/><img src="../../images/face/7.gif" onclick="addEm(7);"/><img src="../../images/face/8.gif" onclick="addEm(8);"/><img src="../../images/face/9.gif" onclick="addEm(9);"/><img src="../../images/face/10.gif" onclick="addEm(10);"/><img src="../../images/face/11.gif" onclick="addEm(11);"/><img src="../../images/face/12.gif" onclick="addEm(12);"/><img src="../../images/face/13.gif" onclick="addEm(13);"/><img src="../../images/face/14.gif" onclick="addEm(14);"/><img src="../../images/face/15.gif" onclick="addEm(15);"/><img src="../../images/face/16.gif" onclick="addEm(16);"/></p>',
                '</div>',
                '<div class="textarea"><textarea class="text" name="g_content">'+ (g_vname ? '[ID:'+g_vid+'-名称:'+g_vname+'] 无法观看请检查修复':'')+'</textarea></div>',
       			'<input type="hidden" name="g_vid" value="' + g_vid + '">',
                '<div class="bottom"><button type="submit">发表留言</button>',
        		'昵称:<input type="text" name="g_name" size="4" maxlength="4" value="' + g_name + '" onclick=""/>',
        		(gbookverify=="1" ? '验证码:<input type="text" name="verifycode" size="4" maxlength="4"/>&nbsp;&nbsp;<img src="../../inc/code.php?a=gbook&s='+Math.random()+'" title="看不清楚? 换一张！" style="cursor:hand;" onClick="src=\'../../inc/code.php?a=gbook&s=\'+Math.random()"/>' : ""),
    			'</div>',
            '</form>',
        '</div>',
	'<a name="cmt_list"></a>',
	'<div class="gbook_list"></div>'
].join(''));

var page=1;
gbook_ajax_submit();
$(document).ready(function(){
	gbook_tongji();
	gbook_loading();
});

function addEm(emid){
	var oldtext = $('#cmt_gbook_form #cmt_form .textarea textarea').val();
 	$('#cmt_gbook_form #cmt_form .textarea textarea').val( oldtext + '[em:' + emid +']' );
} 
function gbook_tongji() {
    $.getJSON(maccms_path + 'plus/gbook/?action=tongji&rnd='+ Math.random(), function(data){
        var wrap = $('form#cmt_form'), post = $('#post');
        $('span.cmt-count', post).text(data[0]);
        $('div.top em:eq(0)', wrap).text(data[0]);
        $('div.top em:eq(1)', wrap).text(data[1]);
    });
}
function gbook_loading(){
    $.ajax({
        cache: false, dataType: 'html', type: 'GET', url: maccms_path + 'plus/gbook/?action=glist&page='+page,
        success: function(r) {
        		
            $('div.gbook_list').html(r);
            $('div.gbook_list div.pages a').click(function(){
            	page = $(this).attr("p") ;
                gbook_loading();
                return false;
            });
        }
    });
}

function gbook_reply(cmtid) {
    $('div.dd div.gbooks').remove();
    $('#toolbar-' + cmtid).after(gbook_reply_form(cmtid));
    gbook_ajax_submit();
}
function gbook_ajax_submit() {
    $('form#cmt_form').ajaxSubmit(function(r){
    	if(r=="1"){
    		alert('请勿传递非法参数');
    	}
    	else if(r=="2"){
    		alert('验证码不正确');
    	}
    	else if(r=="3"){
    		alert('系统繁忙，请稍候再试');
    	}
    	else if(r=="0"){
    		$('textarea', this).val('');
    		if(gbookverify=="1"){$('input', this)[2].value="";}
        	gbook_tongji();
        	gbook_loading();
    	}
    });
}