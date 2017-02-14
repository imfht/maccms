<?php
	require_once ("../inc/conn.php");
	if (app_user == 0){ echo "会员系统关闭中";exit;}
	
    $sessionState = false;
    if ($_SESSION["userid"] == ""){
        $cname = "template_userlogin";
        $filepath = root . "template/" . app_templatedir . "/" . app_htmldir . "/userlogin.html";
    }
    else{
        $sessionState = true;
        $cname = "template_userlogged";
        $filepath = root . "template/" . app_templatedir . "/" . app_htmldir . "/userlogged.html";
    }
    
    $template->html = getFileByCache($cname, $filepath);
	$template->html = replaceStr($template->html, "{maccms:userlink}", app_installdir . "user/index.php?action=main");
	$template->html = replaceStr($template->html, "{maccms:userreglink}", app_installdir . "user/index.php?action=reg");
	$template->html = replaceStr($template->html, "{maccms:userfindpasslink}", app_installdir . "user/index.php?action=findpass");
	$template->html = replaceStr($template->html, "{maccms:userlogoutlink}", app_installdir . "user/index.php?action=logout");
	
    if ($sessionState){
        $row = $db->getRow("SELECT u_id, u_name, u_qq, u_email, u_phone ,u_regtime,u_status,u_points,u_tj, u_loginnum, u_logintime,u_ip,u_random,u_flag,u_start,u_end,ug_id,u_group,ug_name,ug_type,ug_popedom,ug_popvalue FROM ({pre}user LEFT OUTER JOIN {pre}user_group ON {pre}user.u_group = {pre}user_group.ug_id) where u_id=" . $_SESSION["userid"] );
        if($row){
			$template->html = replaceStr($template->html, "{maccms:userid}", $row["u_id"]);
			$template->html = replaceStr($template->html, "{maccms:username}", $row["u_name"]);
			$template->html = replaceStr($template->html, "{maccms:userqq}", $row["u_qq"]);
			$template->html = replaceStr($template->html, "{maccms:useremail}", $row["u_email"]);
			$template->html = replaceStr($template->html, "{maccms:userphone}", $row["u_phone"]);
			$template->html = replaceStr($template->html, "{maccms:userregtime}", $row["u_regtime"]);
			$template->html = replaceStr($template->html, "{maccms:userpoints}", $row["u_points"]);
			$template->html = replaceStr($template->html, "{maccms:userlogintime}", $row["u_logintime"]);
			$template->html = replaceStr($template->html, "{maccms:userloginnum}", $row["u_loginnum"]);
			$template->html = replaceStr($template->html, "{maccms:usertj}", $row["u_tj"]);
			$template->html = replaceStr($template->html, "{maccms:usergroupid}", $row["ug_id"]);
			$template->html = replaceStr($template->html, "{maccms:usergroupname}", $row["ug_name"]);
			$template->html = replaceStr($template->html, "{maccms:userloginip}", $row["u_ip"]);
        }
        unset ($row);
    }
    
    $template->mark();
    $template->ifEx();
    $template->run();
    echo $template->html;
    dispseObj();
?>