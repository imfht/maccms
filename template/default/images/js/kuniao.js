function $TAB(x){return document.getElementById(x);}
function tab_show(a,b,c,d){
	x=a,max_i=b,tn=c,tc=d;
	for(var i=1;i<=max_i;i++){
		if($TAB(tn+i))$TAB(tn+i).className=$TAB(tc+i).className="";
	}
	$TAB(tn+x).className=$TAB(tc+x).className="on";
}
function switchTab(identify,index,count,cnon,cnout) {
	try{
		for(i=0;i<count;i++) {
			var CurTabObj = document.getElementById("Tab_"+identify+"_"+i) ;
			var CurListObj = document.getElementById("List_"+identify+"_"+i) ;
			if (i != index) {
				CurTabObj.className=cnout ;
				CurListObj.style.display="none" ;
			}
		}
		try {
			for (ind=0;ind<CachePic[identify][index].length ;ind++ ) {
				var picobj = document.getElementById(identify+"_pic_"+index+"_"+ind) ;
				//if (picobj.src == "http://www.maccms.com/images/img_default.gif") {
					picobj.src = CachePic[identify][index][ind] ;
				//}
			}
		}
		catch (e) {}
		
		document.getElementById("Tab_"+identify+"_"+index).className=cnon ;
		document.getElementById("List_"+identify+"_"+index).style.display="block";
	}catch (ee) {}
}
function showDiv(){
var div_obj = $("#popDiv"); 
var windowWidth = document.documentElement.clientWidth; 
var windowHeight = document.documentElement.clientHeight; 
var popupHeight = div_obj.height(); 
var popupWidth = div_obj.width(); 
$("<div id='mask'></div>").addClass("mask") 
.width(windowWidth * 0.99) 
.height(windowHeight * 0.99) 
.click(function() {hideDiv(div_id); }) 
.appendTo("body") 
.fadeIn(200); 
div_obj.css({"position": "absolute"}) 
.animate({left: windowWidth/2-popupWidth/2, 
top: windowHeight/2-popupHeight/2, opacity: "show" }, "slow"); 
}
function closeDiv(){
$("#mask").remove(); 
$("#popDiv").animate({left: 0, top: 0, opacity: "hide" }, "slow"); 
}