var bstartnextplay = false;
function BaiduAdsStart() {
    if (document.documentElement.clientHeight > 0) {
        $('#buffer').height ( MacPlayer.height - 62 );
        $('#buffer').show();
    }
}
function BaiduStatus() {
    if (Player.IsPlaying()) {
        AdsEnd()
    } else {
        BaiduAdsStart()
    }
}
MacPlayer.playhtml='<object id="Player" classid="clsid:02E2D748-67F8-48B4-8AB4-0A085374BB99" width="100%" height="'+MacPlayer.height+'" onError="MacPlayer.install();"><param name="URL" value="'+ MacPlayer.playurl +'"><param name="NextWebPage" value="'+ MacPlayer.nexturl +'"><param name="NextCacheUrl" value="'+ MacPlayer.playurl1 +'"><param name="Autoplay" value="1"></object>';
if(!window.ActiveXObject){
	if (navigator.plugins){
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'BaiduPlayer Browser Plugin'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
	MacPlayer.playhtml = '<object id="Player" name="Player" type="application/player-activex" width="100%" height="'+MacPlayer.height+'" progid="Xbdyy.PlayCtrl.1" param_URL="'+MacPlayer.playurl+'"param_NextCacheUrl="'+MacPlayer.playurl1+'" param_LastWebPage="" param_NextWebPage="'+MacPlayer.nexturl+'" param_OnPlay="onPlay" param_OnPause="onPause" param_OnFirstBufferingStart="onFirstBufferingStart" param_OnFirstBufferingEnd="onFirstBufferingEnd" param_OnPlayBufferingStart="onPlayBufferingStart" param_OnPlayBufferingEnd="onPlayBufferingEnd" param_OnComplete="onComplete" param_Autoplay="1"></object>'
	}
	else{
		MacPlayer.install();
	}
}
MacPlayer.show();
setTimeout(function(){
	if (MacPlayer.status == true && maccmsplay==1){
		setInterval("BaiduStatus()", 1000);
		if (MacPlayer.nexturl) {
			Player.NextWebPage = MacPlayer.nexturl
		}
	}
},
adsloadtime + 1000);