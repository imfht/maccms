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
	comment_reply_form(),
	'<a name="cmt_list"></a>',
	'<div class="comment_list"></div>'
].join(''));

var page=1;
comment_ajax_submit();
$(document).ready(function(){
	comment_tongji();
	comment_loading();
});

function addEm(formid,emid){
	var oldtext = $('#cmt_reply_form_'+formid+' #cmt_form .textarea textarea').val();
 	$('#cmt_reply_form_'+formid+' #cmt_form .textarea textarea').val( oldtext + '[em:' + emid +']' );
} 
function comment_tongji() {
    $.getJSON(maccms_path + 'plus/comment/?action=tongji&id='+ c_vid + '&type='+ c_type + "&rnd="+ Math.random(), function(data){
        var wrap = $('form#cmt_form'), post = $('#post');
        $('span.cmt-count', post).text(data[0]);
        $('div.top em:eq(0)', wrap).text(data[0]);
        $('div.top em:eq(1)', wrap).text(data[1]);
    });
}
function comment_loading(){
    $.ajax({
        cache: false, dataType: 'html', type: 'GET', url: maccms_path + 'plus/comment/?action=glist&id='+ c_vid + '&type='+ c_type + '&page='+page,
        success: function(r) {
            $('div.comment_list').html(r);
            $('div.comment_list div.pages a').click(function(){
            	page = $(this).attr("p") ;
                comment_loading();
                return false;
            });
        }
    });
}
function comment_reply_form(cmtid){
    cmtid = cmtid || 0;
    return [
        '<div class="comments" id="cmt_reply_form_' + cmtid + '">',
            '<form action="index.php?action=save" method="post" name="cmt_form" id="cmt_form">',
                '<div class="top">' + (cmtid ? '<strong>回复评论:</strong>' : '<strong>网友评论:</strong><a target="_blank" href="#cmt_list">已有<em>0</em>条评论，共<em>0</em>人参与评论。</a>') + '</div>',
                '<div class="info">',
                    '<p><img src="../../images/face/1.gif" onclick="addEm('+cmtid+',1);"/><img src="../../images/face/2.gif" onclick="addEm('+cmtid+',2);"/><img src="../../images/face/3.gif" onclick="addEm('+cmtid+',3);"/><img src="../../images/face/4.gif" onclick="addEm('+cmtid+',4);"/><img src="../../images/face/5.gif" onclick="addEm('+cmtid+',5);"/><img src="../../images/face/6.gif" onclick="addEm('+cmtid+',6);"/><img src="../../images/face/7.gif" onclick="addEm('+cmtid+',7);"/><img src="../../images/face/8.gif" onclick="addEm('+cmtid+',8);"/><img src="../../images/face/9.gif" onclick="addEm('+cmtid+',9);"/><img src="../../images/face/10.gif" onclick="addEm('+cmtid+',10);"/><img src="../../images/face/11.gif" onclick="addEm('+cmtid+',11);"/><img src="../../images/face/12.gif" onclick="addEm('+cmtid+',12);"/><img src="../../images/face/13.gif" onclick="addEm('+cmtid+',13);"/><img src="../../images/face/14.gif" onclick="addEm('+cmtid+',14);"/><img src="../../images/face/15.gif" onclick="addEm('+cmtid+',15);"/><img src="../../images/face/16.gif" onclick="addEm('+cmtid+',16);"/></p>',
                '</div>',
                '<div class="textarea"><textarea class="text" name="c_content"></textarea></div>',
                '<input type="hidden" name="c_rid" value="' + cmtid + '">',
       			'<input type="hidden" name="c_vid" value="' + c_vid + '">',
       			'<input type="hidden" name="c_type" value="' + c_type + '">',
                '<div class="bottom"><button type="submit">发表评论</button>' + (cmtid ? '<button type="button" onclick="$(\'#cmt_reply_form_' + cmtid + '\').remove();">取消</button>' : ''),
        		'昵称:<input type="text" name="c_name" size="4" maxlength="4" value="' + c_name + '" onclick=""/>',
        		(commentverify=="1" ? '验证码:<input type="text" name="verifycode" size="4" maxlength="4"/>&nbsp;&nbsp;<img src="../../inc/code.php?a=comment&s='+Math.random()+'" title="看不清楚? 换一张！" style="cursor:hand;" onClick="src=\'../../inc/code.php?a=comment&s=\'+Math.random()"/>' + '</div>' : ""),
            '</form>',
        '</div>'
    ].join('');
}
function comment_reply(cmtid) {
    $('div.dd div.comments').remove();
    $('#toolbar-' + cmtid).after(comment_reply_form(cmtid));
    comment_ajax_submit();
}
function comment_ajax_submit() {
    $('form').ajaxSubmit(function(r){
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
    		if(commentverify=="1"){$('input', this)[4].value="";}
        	comment_tongji();
        	comment_loading();
    	}
    });
}