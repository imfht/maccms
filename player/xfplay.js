
MacPlayer.playhtml ='<object id="Player" name="Player" width="100%" height="'+MacPlayer.height+'" classid="clsid:E38F2429-07FE-464A-9DF6-C14EF88117DD" onError="MacPlayer.install();"><param name="URL" VALUE="'+MacPlayer.playurl+'"><param name="Status" value="1"></object>';

var rMsie = /(msie\s|trident.*rv:)([\w.]+)/;
var match = rMsie.exec(navigator.userAgent.toLowerCase());
if(match == null){
	if (navigator.plugins) {
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			var n = navigator.plugins[i].name;
			if( navigator.plugins[n][0]['type'] == 'application/xfplay-plugin')
			{
				ll = true; break;
			}
		}
	}
	if(ll){
		MacPlayer.playhtml ='<embed id="Player" name="Player" type="application/xfplay-plugin" width="100%" height="'+MacPlayer.height+'" PARAM_URL="'+MacPlayer.playurl+'" PARAM_Status="1" PARAM_Autoplay="1"></embed>';
	}
	else{
		MacPlayer.install();
	}
}
MacPlayer.show();
