<?php
	require_once ("../inc/conn.php");
	require_once ("buyconfig.php");
	if (app_user == 0){ echo "会员系统关闭中";exit;}
	if (isN($_SESSION["userid"])) { echo "非法操作";exit; }
	
	$RespCode = $_POST['RespCode']; 	//应答返回码
	$TrxId = $_POST['TrxId'];  			//银支付交易唯一标识
	$OrdAmt = $_POST['OrdAmt']; 		//金额
	$CurCode = $_POST['CurCode']; 		//币种
	$Pid = $_POST['Pid'];  				//商品编号
	$OrdId = $_POST['OrdId'];  			//订单号
	$MerPriv = $_POST['MerPriv'];  		//商户私有域
	$RetType = $_POST['RetType'];  		//返回类型
	$DivDetails = $_POST['DivDetails']; //分账明细
	$GateId = $_POST['GateId'];  		//银行ID
	$ChkValue = $_POST['ChkValue']; 	//签名信息
	$MsgData = $_POST['MsgData']; 	//数据信息
	
	$SignData = getPage("http://pay.yinshengvip.com/versign/?MsgData=".$MsgData."&ChkValue=".$ChkValue,"utf-8");
	
	if($SignData == "0"){
		if($RespCode == "000000"){
			//交易成功
			//根据订单号 进行相应业务操作
			//在些插入代码
			$point = app_buyexc * intval($OrdAmt);
 			$db->query("update {pre}user set u_points=u_points+".$point." where u_id = ". $_SESSION["userid"]);
			alertUrl("充值成功","index.php");
		}else{
			//交易失败
			//根据订单号 进行相应业务操作
			//在些插入代码
			alertUrl("支付失败请重试","index.php?action=pay2");
		}
	}else{
		//验签失败
		alertUrl("验签失败请重试[".SignData."]","index.php?action=pay2");
	}
?>