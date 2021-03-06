$(function(){
    setWorkspace();
    initTopTab();
    $(window).resize(setWorkspace);
});
function setWorkspace(){
    var wWidth = $(window).width();
    var wHeight = $(window).height();
    $('#workspace').width(wWidth - $('#left').width() - parseInt($('#left').css('margin-right')) - 5);
    $('#workspace').height(wHeight - $('#head').height() );
}
function initTopTab(){
    $.each(menu, function(k, v){
        var item = $('<li><a class="link" href="javascript:;" id="tab_' + k + '">' + v.text + '</a></li>');
        item.children('a').click(function(){
			var tabName = this.id.substr(4);
			if(tabName == currTab){
			return;
			}
			switchTab(tabName);
			openItem();
		});
        if( levels.indexOf(k.replace("m","")) >-1){
        	$('#nav').append(item);
    	}
    });
	
    switchTab(currTab);
    openItem(firstOpen[1], firstOpen[0]);
    $('#iframe_refresh').click(function(){
        $('#workspace').get(0).contentWindow.location.reload();
    });
}
function switchTab(tabName){
    currTab = tabName;
    pickTab();
    loadSubmenu();
}
function pickTab(){
    var id = '#tab_' + currTab;
    $('#nav').find('a').each(function(){
        $(this).removeClass('actived');
        $(this).addClass('link');
    });
    $(id).addClass('actived');
}
function menuNewwin(obj) {
	window.open($(obj).parent().attr("url"));
}
function loadSubmenu(){
    var m = menu[currTab];
    var ds="";
    $('#submenuTitle').text(m.subtext ? m.subtext : m.text);
    $('#submenu').find('dd').remove();
    $.each(m.children, function(k, v){
        var p = v.parent ? v.parent : currTab;
        ds="";
        if (v.url=="#"){
        	ds="class=ln";
        }
        var item = $('<dd '+ds+'><a href="javascript:;" url="' + v.url + '" parent="' + p + '" id="item_' + k + '"><em title="Open in new window" onclick="menuNewwin(this)"></em>' + v.text + '</a></dd>');
        item.children('a').click(function(){
            openItem(this.id.substr(5));
        });
        $('#submenu').append(item);
    });
}
function openItem(itemIndex, tab){
    if(typeof(itemIndex) == 'undefined')
    {
        var itemIndex = menu[currTab]['default'];
    }
    var id      = '#item_' + itemIndex;
    if(tab){
        var parent = tab;
    }else{
        var parent  = $(id).attr('parent');
    }
    if(parent != currTab){
        switchTab(parent);
    }
    $('#submenu').find('a').each(function(){
        $(this).removeClass('selected');
    });
    $(id).addClass('selected');
    $('#workspace').show();
    $('#workspace').attr('src', $(id).attr('url'));
}