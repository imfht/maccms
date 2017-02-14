<?php
require_once ("conn.php");
if (app_timming==1){ 
	$xmlpath = root ."inc/timmingset.xml";
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xmlpath);
	$xmlnode = $doc -> documentElement;
	$timmingnodes = $xmlnode->getElementsByTagName("timming");
	
	foreach($timmingnodes as $timmingnode){
		$tname = $timmingnode->getElementsByTagName("name")->item(0)->nodeValue;
    	$tdes = $timmingnode->getElementsByTagName("des")->item(0)->nodeValue;
    	$tstatus = $timmingnode->getElementsByTagName("status")->item(0)->nodeValue;
    	$tfile = $timmingnode->getElementsByTagName("file")->item(0)->nodeValue;
    	$tparamets = $timmingnode->getElementsByTagName("paramets")->item(0)->nodeValue;
    	$tweeks = $timmingnode->getElementsByTagName("weeks")->item(0)->nodeValue;
    	$thours = $timmingnode->getElementsByTagName("hours")->item(0)->nodeValue;
    	$truntime = $timmingnode->getElementsByTagName("runtime")->item(0)->nodeValue;
    	$tparamets = replaceStr($tparamets,"&amp;","&");
    	if (!isN($truntime)) { $oldweek= date('w',strtotime($truntime)); $oldhours= date('H',strtotime($truntime)); }
    	$curweek= date('w',time()) ;	$curhours= date("H",time());
		if (strlen($oldhours)==2 && intval($oldhours) <10){ $oldhours= substr($oldhours,1,1); }
		if (strlen($curhours)==2 && intval($curhours) <10){ $curhours= substr($curhours,1,1); }
		
		if ($tstatus==1 && ( isN($truntime) || ($oldweek."-".$oldhours) != ($curweek."-".$curhours) && strpos($tweeks,$curweek)>-1 && strpos($thours,$curhours)>-1)) {
			$timmingnode->getElementsByTagName("runtime")->item(0)->nodeValue = date('Y-m-d H:i:s');
			$doc -> save($xmlpath);
			header("Location:" . app_installdir."inc/".$tfile."?".$tparamets  );
			return;
		}
	}
	unset($doc);
}
?>