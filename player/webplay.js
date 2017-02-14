MacPlayer.playhtml = '<OBJECT id="Player" height="'+MacPlayer.height+'" width="100%" data=data:application/x-oleobject;base64,Q5uJqr0ka0u70EVVfY0R4AADAAB7TAAATygAAAEAAAA= classid=CLSID:AA899B43-24BD-4B6B-BBD0-45557D8D11E0 VIEWASTEXT onError="MacPlayer.install();"></OBJECT>';
MacPlayer.show();

setTimeout(function(){
	if (MacPlayer.status == true && maccmsplay==1) {
		Player.ServerMode = 2;
		Player.PlayModeValue = MacPlayer.playurl;
		Player.ChannelID = MacPlayer.playurl;
		Player.AuthenHost = MacPlayer.playfrom;
		Player.ServerHost = MacPlayer.playfrom;
		Player.ContorlWidth = MacPlayer.width;
		Player.ContorlHeight = MacPlayer.height;
		Player.UserName = "";
		Player.UserID = "";
		Player.PlayMode = 1;
		Player.Session = "";
		Player.ProtocolType = 1;
		Player.EmbedMode = 2;
		Player.ProgName = "";
		Player.VODVersion = 6000;
		Player.Start()
	}
},
adsloadtime + 1000);