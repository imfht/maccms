
function isdigs(digtype){
	url = maccms_path + "plus/digg/index.php?action=digg&d_vid="+d_vid+"&d_type="+d_type+"&typee="+digtype+"&m=" + Math.random();
	$.get(url, function(obj){
		switch (obj)
		{
			case '1':
				alert("您已经参与过评价！");
				break;
			default:
				showdigg();
				alert("谢谢你的参与！");
				break;
		}
	});
}

function showdigg(){
	url = maccms_path + "plus/digg/index.php?action=show&d_vid="+d_vid+"&d_type="+d_type+"&m=" + Math.random();
	$.get(url, function(obj){
		$("#digg").html(obj);
	});
}

//大图
//document.writeln("<style type=\"text\/css\">.digg {height: auto;font-size:12px;font-weight:normal;}.digg a {display: block;height: 48px;width: 179px;background-image: url("+maccms_path+"plus\/digg\/images\/markbig.gif);background-repeat: no-repeat;position: relative;color: #000;text-decoration: none;}.digg .good {margin-bottom:10px;margin-top:5px;}.digg .good a {background-position: -189px 0px;}.digg .good a:hover {background-position: 0px 0px;}.digg .bad a {background-position: -378px 0px;}.digg .bad a:hover {background-position: -567px 0px;}.digg a p {padding-left:30px;line-height:25px;}.digg .bar {background-color: white;height: 5px;left: 20px;overflow: hidden;position: absolute;text-align: left;top: 30px;width: 55px;}.digg .bar #g_img {background-image: url("+maccms_path+"plus\/digg\/images\/sprites.gif);background-repeat: repeat-x;height: 5px;width: auto;}.digg .bar #b_img {background-image: url("+maccms_path+"plus\/digg\/images\/sprites.gif);background-repeat: repeat-x;height: 5px;width: auto;background-position: 0px -5px;}.digg .num {color: #333;font: normal normal 100 10px\/12px Tahoma;left: 80px;position: absolute;top: 26px;}.digg .good .bar {border: 1px solid #40A300;}.digg .bad .bar {border: 1px solid #555;}<\/style>");
//小图
document.writeln("<STYLE type=text\/css>.digg {	HEIGHT: auto; FONT-SIZE: 12px; FONT-WEIGHT: normal}.digg A {BACKGROUND-IMAGE: url("+maccms_path+"plus\/digg\/images\/mark.gif); POSITION: relative; WIDTH: 104px; DISPLAY: block; BACKGROUND-REPEAT: no-repeat; HEIGHT: 30px; COLOR: #000; TEXT-DECORATION: none}.digg .good {	MARGIN-TOP: 3px; MARGIN-BOTTOM: 5px;float:left;}.digg .bad {MARGIN-TOP: 3px; MARGIN-BOTTOM: 5px;float:left;}.digg .good A {	BACKGROUND-POSITION: -114px 0px}.digg .good A:hover {BACKGROUND-POSITION: 0px 0px}.digg .bad A {BACKGROUND-POSITION: -228px 0px}.digg .bad A:hover {	BACKGROUND-POSITION: -342px 0px}.digg A P {	LINE-HEIGHT: 20px; PADDING-LEFT: 20px}.digg .bar {	POSITION: absolute; TEXT-ALIGN: left; BACKGROUND-COLOR: white; WIDTH: 35px; HEIGHT: 5px; OVERFLOW: hidden; TOP: 17px; LEFT: 10px}.digg .bar #g_img {	BACKGROUND-IMAGE: url("+maccms_path+"plus\/digg\/images\/sprites.gif); WIDTH: auto; BACKGROUND-REPEAT: repeat-x; HEIGHT: 5px}.digg .bar #b_img {BACKGROUND-IMAGE: url("+maccms_path+"plus\/digg\/images\/sprites.gif); WIDTH: auto; BACKGROUND-REPEAT: repeat-x; BACKGROUND-POSITION: 0px -5px; HEIGHT: 5px}.digg .num {	POSITION: absolute; FONT: 100 8px\/10px Tahoma; COLOR: #333; TOP: 15px; LEFT: 43px}.digg .good .bar {	BORDER-BOTTOM: #40a300 1px solid; BORDER-LEFT: #40a300 1px solid; BORDER-TOP: #40a300 1px solid; BORDER-RIGHT: #40a300 1px solid}.digg .bad .bar {	BORDER-BOTTOM: #555 1px solid; BORDER-LEFT: #555 1px solid; BORDER-TOP: #555 1px solid; BORDER-RIGHT: #555 1px solid}<\/STYLE>");
document.writeln("<div class=\"digg\" id=\"digg\"><\/div>");