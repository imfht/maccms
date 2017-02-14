var bstartnextplay = false;
function PlayerAdsStart() {
    if (document.documentElement.clientHeight > 0) {
        $('#buffer').height ( MacPlayer.height - 62 );
        $('#buffer').show();
    }
}
function PlayerStatus() {
    if (Player.Full == 0) {
        if (Player.PlayState == 3) {
            AdsEnd()
        } else {
            PlayerAdsStart()
        }
    }
}
function PlayerNextDown() {
    if (Player.get_CurTaskProcess() > 900 && !bstartnextplay) {
        Player.StartNextDown( ConvertUrl(MacPlayer.playurl1) );
        bstartnextplay = true
    }
}
function ConvertUrl(url){
	if(url==null || url==undefined) return "";
	url = url.split("|");
	return url[0]+"|"+url[1]+"|["+document.domain+"]"+url[2]+"|";
}

MacPlayer.playhtml='<object id="Player" classid="clsid:88CAD623-BC08-7321-C3D7-3A9B739BCA88" width="100%" height="'+MacPlayer.height+'" onError="MacPlayer.install();"><param name="URL" value="'+ ConvertUrl(MacPlayer.playurl) +'"><param name="NextWebPage" value="'+ MacPlayer.nexturl +'"><param name="fvodAdUrl" VALUE="'+MacPlayer.pauseurl +'"><param name="NextCacheUrl" value="'+ ConvertUrl(MacPlayer.playurl1) +'"><param name="Autoplay" value="1"></object>';

var rMsie = /(msie\s|trident.*rv:)([\w.]+)/;
var match = rMsie.exec(navigator.userAgent.toLowerCase());
if(match == null){
	if (navigator.plugins){
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'FvodPlugin'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
	MacPlayer.playhtml = '<object id="Player" name="Player" showcontrol="1" type="application/fvod-plugin" width="100%" height="'+MacPlayer.height+'" URL="'+MacPlayer.playurl+'" NextWebPage="'+MacPlayer.nexturl+'" Autoplay="1"></object>'
	}
	else{
		MacPlayer.install();
	}
}
MacPlayer.show();
setTimeout(function(){
	if (MacPlayer.status == true && maccmsplay==1){
		setInterval("PlayerStatus()", 1000);
		if (MacPlayer.nexturl) {
			Player.NextWebPage = MacPlayer.nexturl;
			setInterval("PlayerNextDown()", 9333);
		}
	}
},
adsloadtime + 1000);