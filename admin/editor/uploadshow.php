<?php
require_once ("../admin_conn.php");
chkLogin();
$action=be("get","action");
$id=be("get","id");
?>
<body leftmargin=0 topmargin=0 style="font-size:11px">
<form name="form" enctype="multipart/form-data" action="upload.php?action=<?php echo $action?>&id=<?php echo $id?>" method="post">
<input type=file name=file1>
<input type=submit name=submit value="上传">
</form>