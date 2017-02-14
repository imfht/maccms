var bstartnextplay = false;
function QvodAdsStart() {
    if (document.documentElement.clientHeight > 0) {
        var xml = '\u003c\u0069\u006e\u0076\u006f\u006b\u0065\u0020\u006e\u0061\u006d\u0065\u003d\u0022\u0051\u0076\u006f\u0064\u0056\u0065\u0072\u0073\u0069\u006f\u006e\u0022\u0020\u0072\u0065\u0074\u0075\u0072\u006e\u0074\u0079\u0070\u0065\u003d\u0022\u0078\u006d\u006c\u0022\u003e\u003c\u002f\u0069\u006e\u0076\u006f\u006b\u0065\u003e';
        var version = Player.CallFunction(xml).replace('\u0022\u003e\u003c\u002f\u0069\u006e\u0076\u006f\u006b\u0065\u003e', '').replace('\u003c\u0069\u006e\u0076\u006f\u006b\u0065\u0020\u006e\u0061\u006d\u0065\u003d\u0022\u0051\u0076\u006f\u0064\u0056\u0065\u0072\u0073\u0069\u006f\u006e\u0022\u0020\u0072\u0065\u0074\u0075\u0072\u006e\u0074\u0079\u0070\u0065\u003d\u0022\u0078\u006d\u006c\u0022', '').replace('\u0076\u006e\u003d\u0022', '').split('.');
        version = Number(version[0]);
        var h1 = 69;
        if (version > 3) {
            h1 = 42
        }
        $('#buffer').height ( MacPlayer.height - h1 );
        $('#buffer').show();
    }
}
function QvodStatus() {
    if (Player.Full == 0) {
        if (Player.PlayState == 3) {
            AdsEnd()
        } else {
            QvodAdsStart()
        }
    }
}
function QvodNextDown() {
    if (Player.get_CurTaskProcess() > 900 && !bstartnextplay) {
        Player.StartNextDown(MacPlayer.playurl1);
        bstartnextplay = true
    }
}
MacPlayer.playhtml ='<object id="Player" name="Player" width="100%" height="'+MacPlayer.height+'" classid="clsid:F3D0D36F-23F8-4682-A195-74C92B03D4AF" onError="MacPlayer.install();"><param name="URL" VALUE="'+MacPlayer.playurl+'"><param name="Autoplay" VALUE="1"><param name="QvodAdUrl" VALUE="'+MacPlayer.pauseurl +'"><param name="NextWebPage" VALUE="'+ MacPlayer.nexturl +'"></object>';
if(!window.ActiveXObject){
	if (navigator.plugins) {
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'QvodInsert'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
		MacPlayer.playhtml ='<embed id="Player" name="Player" URL="'+MacPlayer.playurl+'" type="application/qvod-plugin" width="100%" height="'+MacPlayer.height+'"></embed>';
	}
	else{
		MacPlayer.install();
	}
}
MacPlayer.show();
setTimeout(function() {
	if (MacPlayer.status == true && maccmsplay==1){
		setInterval("QvodStatus()", 1000);
		if (MacPlayer.nexturl) {
			Player.NextWebPage = MacPlayer.nexturl;
			setInterval("QvodNextDown()", 9333)
		}
	}
},
adsloadtime + 1000);