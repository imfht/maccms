var bstartnextplay = false;
function PlayerAdsStart() {
    if (document.documentElement.clientHeight > 0) {
        $('#buffer').height ( MacPlayer.height - 62 );
        $('#buffer').show();
    }
}
function PlayerStatus() {
	try{
	    if (Player.IsPlaying()) {
	        MacPlayer.AdsEnd()
	    } else {
	        PlayerAdsStart()
	    }
	}
	catch(e){
		PlayerAdsStart()
	}
}
function ConvertUrl(url){
	if(url==null || url==undefined) return "";
	url = url.split("|");
	return url[0]+"|"+url[1]+"|["+document.domain+"]"+url[2]+"|";
}

MacPlayer.playhtml='<object id="Player" classid="clsid:D154C77B-73C3-4096-ABC4-4AFAE87AB206" width="100%" height="'+MacPlayer.height+'" onError="MacPlayer.install();"><param name="URL" value="'+ ConvertUrl(MacPlayer.playurl) +'"><param name="NextWebPage" value="'+ MacPlayer.nexturl +'"><param name="NextCacheUrl" value="'+ ConvertUrl(MacPlayer.playurl1) +'"><param name="Autoplay" value="1"></object>';

var rMsie = /(msie\s|trident.*rv:)([\w.]+)/;
var match = rMsie.exec(navigator.userAgent.toLowerCase());
if(match == null){
	if (navigator.plugins){
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'npFFPlayer'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
	MacPlayer.playhtml = '<object id="Player" name="Player" showcontrol="1" type="application/npFFPlayer" width="100%" height="'+MacPlayer.height+'" URL="'+MacPlayer.playurl+'" NextWebPage="'+MacPlayer.nexturl+'" Autoplay="1"></object>'
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
			Player.NextWebPage = MacPlayer.nexturl
		}
	}
},
adsloadtime + 1000);