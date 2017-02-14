function CoolAdsStart() {
    if (document.documentElement.clientHeight > 0) {
        $('#buffer').height (MacPlayer.height - 45 );
        $('#buffer').show();
    }
}
function CoolStatus() {
    if (Player.Full == 0) {
        if (Player.PlayState == 3) {
            AdsEnd()
        } else {
            CoolAdsStart()
        }
    }
}
function CoolNextDown() {
    if (Player.get_CurTaskProcess() > 900 && !bstartnextplay){
        Player.StartNextDown(MacPlayer.playurl1);
        bstartnextplay = true
    }
}
MacPlayer.playhtml ='<object id="Player" width="100%" height="'+MacPlayer.height+'" classid="clsid:73BAB958-AC02-5108-B2B8-665834A9C63A" onError="MacPlayer.install();"><param name="URL" VALUE="'+MacPlayer.playurl+'"><param name="Autoplay" VALUE="1"><param name="CoolAdUrl" VALUE="'+MacPlayer.pauseurl+'"><param name="NextWebPage" VALUE="'+ MacPlayer.nexturl +'"><PARAM NAME="Showcontrol" VALUE="1"></object>';
if(!window.ActiveXObject){
	if (navigator.plugins){
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'CoolPlugin'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
		MacPlayer.playhtml ='<embed URL="'+MacPlayer.playurl+'" type="application/cool-plugin" autoplay="1" showcontrol="1" width="100%" height="'+MacPlayer.height+'"></embed>';
	}
	else{
		MacPlayer.install();
	}
}
MacPlayer.show();
setTimeout(function(){
	if (MacPlayer.status == true && maccmsplay==1) {
		setInterval("CoolStatus()", 1000);
		if (MacPlayer.nexturl) {
			Player.NextWebPage = MacPlayer.nexturl;
			setInterval("CoolNextDown()", 9333)
		}
	}
},
adsloadtime + 1000);