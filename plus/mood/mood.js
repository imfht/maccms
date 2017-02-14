
function get_mood(mood_id)
{
	if(moodzt == "1"){
		alert("您已经投过票，请不要重复投票！");
	}
	else {
		url = maccms_path + "plus/mood/?action=mood&m_type="+m_type+"&m_vid="+m_vid+"&typee="+mood_id+"&m=" + Math.random();
		$.get(url, function(obj) {
			return_review1(obj);
		})
		moodzt = "1";
	}
}
function remood()
{
	url = maccms_path + "plus/mood/?action=show&m_vid="+m_vid+"&m_type="+m_type+"&m=" + Math.random();
	$.get(url, function(obj) {
		return_review1(obj);
	})
}

function return_review1(str)
{
	if(str=="error"){
		alert("信息不存在！");
	}
	else if(str==0){
		alert("您已经投过票，请不要重复投票！");
	}
	else{
		moodinner(str);
	}
}

function moodinner(moodtext)
{
	var imga = maccms_path + "plus/mood/images/pre_02.gif";
	var imgb = maccms_path + "plus/mood/images/pre_01.gif";
	var color1 = "#666666";
	var color2 = "#EB610E";
	var heightz = "80";
	var hmax = 0;
	var hmaxpx = 0;
	var heightarr = new Array();
	var moodarr = moodtext.split(",");
	var moodzs = 0;
	for(k=0;k<9;k++){
		moodarr[k] = parseInt(moodarr[k]);
		moodzs += moodarr[k];
	}
	for(i=0;i<9;i++) {
		heightarr[i]= Math.round(moodarr[i]/moodzs*heightz);
		if(heightarr[i]<1) heightarr[i]=1;
		if(moodarr[i]>hmaxpx) {
		hmaxpx = moodarr[i];
		}
	}
	$("#moodtitle").html( "<span style='color: #555555;padding-left: 20px;'>已有<font color='#FF0000'>"+moodzs+"</font>人提交了自己的感受：</span>" );
	for(j=0;j<9;j++){
		if(moodarr[j]==hmaxpx && moodarr[j]!=0){
			$("#moodinfo"+j).html( "<span style='color: "+color2+";'>"+moodarr[j]+"</span><br><img src='"+imgb+"' width='20' height='"+heightarr[j]+"'>" );
		} else {
			$("#moodinfo"+j).html( "<span style='color: "+color1+";'>"+moodarr[j]+"</span><br><img src='"+imga+"' width='20' height='"+heightarr[j]+"'>" );
		}
	}
}

document.writeln("<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\" style=\"font-size:12px;margin-top: 10px;margin-bottom: 10px;\"><tr><td colspan=\"9\" id=\"moodtitle\" ><\/td><\/tr><tr align=\"center\" valign=\"bottom\"><td height=\"10\" id=\"moodinfo0\"><\/td><td height=\"10\" id=\"moodinfo1\"><\/td><td height=\"10\" id=\"moodinfo2\"><\/td><td height=\"10\" id=\"moodinfo3\"><\/td><td height=\"10\" id=\"moodinfo4\"><\/td><td height=\"10\" id=\"moodinfo5\"><\/td><td height=\"10\" id=\"moodinfo6\"><\/td><td height=\"10\" id=\"moodinfo7\"><\/td><td height=\"10\" id=\"moodinfo8\"><\/td><\/tr>");
document.writeln("<tr align=\"center\" valign=\"middle\"><td><img src=\""+maccms_path+"plus\/mood\/images\/0.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/1.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/2.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/3.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/4.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/5.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/6.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/7.gif\" width=\"50\" height=\"50\"><\/td><td><img src=\""+maccms_path+"plus\/mood\/images\/8.gif\" width=\"50\" height=\"50\"><\/td><\/tr>");
document.writeln("<tr><td align=\"center\" >无奈<\/td><td align=\"center\" >不解<\/td><td align=\"center\" >感动<\/td><td align=\"center\" >支持<\/td><td align=\"center\" >喜欢<\/td><td align=\"center\" >搞笑<\/td><td align=\"center\" >惊讶<\/td><td align=\"center\" >愤怒<\/td><td align=\"center\" >晕倒<\/td><\/tr><tr align=\"center\"><td><input onClick=\"get_mood(\'mood1\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood2\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood3\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood4\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood5\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood6\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood7\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood8\')\" type=\"radio\" name=\"radiobutton\"><\/td><td><input onClick=\"get_mood(\'mood9\')\" type=\"radio\" name=\"radiobutton\"><\/td><\/tr><\/table>");