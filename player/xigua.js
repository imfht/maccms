var $$ = function(value){return document.getElementById(value)};

function PlayStatus() {
    if (Player.IsPlaying()) {
        AdsEnd()
    } else {
        AdsStart()
    }
}

var onPlay = function(){
	$$('buffer').style.display = 'none';
	if(Player.IsPlaying()){
		Player.Play();
	}
}
var onPause = function(){
	$$('buffer').height = MacPlayer.Height-60;
	$$('buffer').style.display = 'block';
}
var onFirstBufferingStart = function(){
	$$('buffer').height = MacPlayer.Height-80;
	$$('buffer').style.display = 'block';
}
var onFirstBufferingEnd = function(){
	$$('buffer').style.display = 'none';
}
var onPlayBufferingStart = function(){
	$$('buffer').height = MacPlayer.Height-80;
	$$('buffer').style.display = 'block';
}
var onPlayBufferingEnd = function(){
	$$('buffer').style.display = 'none';
}
var onComplete = function(){
	onPause();
}
var onAdsEnd = function(){
	if(Player.IsPause()){
		Player.Play();
	}
}

MacPlayer.playhtml ='<object id="Player" name="Player" width="100%" height="'+MacPlayer.height+'" classid="clsid:BEF1C903-057D-435E-8223-8EC337C7D3D0" onError="MacPlayer.install();"><param name="URL" VALUE="'+MacPlayer.playurl+'"><param name="Autoplay" VALUE="1"><param name="Pase" VALUE="'+MacPlayer.pauseurl +'"><param name="NextWebPage" VALUE="'+ MacPlayer.nexturl +'"><param name="NextCacheUrl" VALUE="'+ MacPlayer.playurl1 +'"><param name="OnPlay" value="onPlay"/><param name="OnPause" value="onPause"/><param name="OnFirstBufferingStart" value="onFirstBufferingStart"/><param name="OnFirstBufferingEnd" value="onFirstBufferingEnd"/><param name="OnPlayBufferingStart" value="onPlayBufferingStart"/><param name="OnPlayBufferingEnd" value="onPlayBufferingEnd"/><param name="OnComplete" value="onComplete"/><param name="Autoplay" value="1"/></object>';

var rMsie = /(msie\s|trident.*rv:)([\w.]+)/;
var match = rMsie.exec(navigator.userAgent.toLowerCase());
if(match == null){
	if (navigator.plugins) {
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'XiGua Yingshi Plugin'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
		
		MacPlayer.playhtml = '<object id="Player" name="Player" type="application/xgyingshi-activex" progid="xgax.player.1" width="100%" height="'+MacPlayer.height+'" param_URL="'+MacPlayer.playUrl+'"param_NextCacheUrl="'+MacPlayer.playurl1+'" param_LastWebPage="" param_NextWebPage="'+MacPlayer.nexturl+'" param_OnPlay="onPlay" param_OnPause="onPause" param_OnFirstBufferingStart="onFirstBufferingStart" param_OnFirstBufferingEnd="onFirstBufferingEnd" param_OnPlayBufferingStart="onPlayBufferingStart" param_OnPlayBufferingEnd="onPlayBufferingEnd" param_OnComplete="onComplete" param_Autoplay="1"></object>'
		
	}
	else{
		MacPlayer.install();
	}
}
MacPlayer.show();
setTimeout(function() {
	if (MacPlayer.status == true && maccmsplay==1){
	}
},
adsloadtime + 1000);