
function AdsStart() {
    if (document.documentElement.clientHeight > 0) {
        $('#buffer').height ( MacPlayer.Height - 62 );
        $('#buffer').show();
    }
}
function PlayStatus() {
    if (Player.PlayState==3) {
        AdsEnd()
	}
	else if (Player.PlayState==2 || Player.PlayState==4){
		AdsStart()
	}
}


MacPlayer.playhtml ='<object id="Player" name="Player" width="100%" height="'+MacPlayer.height+'" classid="clsid:C56A576C-CC4F-4414-8CB1-9AAC2F535837" onError="MacPlayer.install();"><param name="URL" value="'+ MacPlayer.playurl +'"><param name="WEB_URL" value="'+ unescape(window.location.href) +'"><param name="Autoplay" value="1"></object>';

var rMsie = /(msie\s|trident.*rv:)([\w.]+)/;
var match = rMsie.exec(navigator.userAgent.toLowerCase());
if(match == null){
	if (navigator.plugins){
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'JJvod Plugin'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
	MacPlayer.playhtml = '<object id="Player" name="Player" type="application/x-itst-activex" progid="WEBPLAYER.WebPlayerCtrl.2" width="100%" height="'+MacPlayer.height+'" param_URL="'+MacPlayer.playurl+'" param_WEB_URL="'+ unescape(window.location.href) +'" param_Autoplay="1"></object>'
	}
	else{
		MacPlayer.install();
	}
}

MacPlayer.show();
setTimeout(function() {
	if (MacPlayer.status == true && maccmsplay==1){
		setInterval("PlayStatus()", 1000);
	}
},
adsloadtime + 1000);