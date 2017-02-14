<?php
define("QQ_OAUTH_CONSUMER_KEY","1");   //APP ID
define("QQ_OAUTH_CONSUMER_SECRET","1");  //APP KEY
define('QQ_OAUTH_NONCE', rand(100000, 999999));  //时间戳
define('QQ_TIMESTAMP', time());
define("QQ_CALLBACK_URL","http://" .$_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?action=reg&ref=qqlogged"); //返回地址
?>