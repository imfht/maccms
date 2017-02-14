<?php
	require_once ("../inc/conn.php");
	
	if (app_vodplayviewtype==0 || app_vodplayviewtype==2){
		$query = $_SERVER['QUERY_STRING'];
		$ID1 = replaceStr($query,".". app_vodsuffix."","");
		$ID2 = explode("-",$ID1);
		
		if (count($ID2)!=3){ showMsg ("请勿传递非法参数！","../");}
		$id= $ID2[0];
		$src= $ID2[1];
		$num= $ID2[2];
	}
	else if (app_vodplayviewtype==1){
		$id= be("get","id");
		$src= be("get","sort");
		$num= be("get","num");
	}
	else{
		redirect("../");
	}
	
	if (!isNum($id) || !isNum($num) || !isNum($src)){ showMsg ("请勿传递非法参数！","../");}
	$sql = "SELECT * FROM {pre}vod WHERE d_hide=0 and d_id=". $id;
	$userid = $_SESSION["userid"];
	$row = $db->getRow($sql);
	if (!$row){ showMsg ("请勿传递非法参数","../");}
	$mac["vodid"] = intval($id);
	$mac["vodnum"] = intval($num);
	$mac["vodsrc"] = intval($src);
	$mac["vodtypeid"] = $row["d_type"];
	if (app_user==1){
		if (!getUserPopedom($row["d_type"],"play")){ showMsg ("您没有权限浏览此栏目下的内容,请联系管理员获取权限!","../user/"); }
		if ($row["d_stint"] > 0 && isN($userid)){ alertUrl ("此为收费数据请先登录再操作","../user/"); exit;}
		if (!isN($userid)){
			$rowuser = $db->getRow("SELECT * FROM {pre}user where u_id=".$userid);
			if ($rowuser){
				$stat =false;
				$userpoint = $rowuser["u_points"];
				
				if ($rowuser["u_flag"] == 1){
					if ( time() >= strtotime($rowuser["u_end"]) ){
						$str = "对不起,您的会员时间已经到期,请联系管理员续费!";
					}
				}
				else if ($rowuser["u_flag"] == 2){
					$ip1arr = explode(".",$rowuser["u_ip"]);
					$ip2arr = explode(".",$rowuser["u_start"]);
					$ip3arr = explode(".",$rowuser["u_end"]);
					if (($ip1arr[0] >= $ip2arr[0]) &&  ($ip1arr[0] <= $ip3arr[0])) {$stat = true ;} else {$stat =false;}
					if (($ip1arr[1] >= $ip2arr[1]) &&  ($ip1arr[1] <= $ip3arr[1])) {$stat = true ;} else {$stat =false;}
					if (($ip1arr[2] >= $ip2arr[2]) &&  ($ip1arr[2] <= $ip3arr[2])) {$stat = true ;} else {$stat =false;}
					if (($ip1arr[3] >= $ip2arr[3]) &&  ($ip1arr[3] <= $ip3arr[3])) {$stat = true ;} else {$stat =false;}
					if (!$stat) { $str =  "对不起,您登录IP段不在受理范围，请联系管理员续费!";}
				}
				else{
					if ($rowuser["u_points"] < $row["d_stint"]){
						if (strpos(",".$rowuser["u_plays"],",".$id ."-".$src."-" .$num.",") > 0){ $stat = true;}
						if (!$stat){ $str = "对不起,您的积分不够，无法观看收费数据，请推荐本站给您的好友、赚取更多积分";}
					}
					$userpoint = $rowuser["u_points"] - $row["d_stint"];
				}
				if (!isN($str)){ alertUrl ($str,"../user/");exit;}
				
				if (strpos(",".$rowuser["u_plays"],",".$id ."-".$src."-" .$num.",") > 0){ $stat = true;}
				if (!$stat){
					$playrecords = "," . $rowuser["u_plays"] . $id ."-".$src."-" .$num . ",";
					$playrecords = replaceStr($playrecords,",,",",");
					$db->Update ("{pre}user" ,array("u_points","u_plays"),array($userpoint,$playrecords),"u_id=".$userid);
				}
			}
			unset($rowuser);
		}
	}
	$typearr = getValueByArray($cache[0], "t_id", $mac["vodtypeid"]);
	$template->loadvod ($row,$typearr,"play");
	unset($row);
	echo  $template->html;
	dispseObj();
?>