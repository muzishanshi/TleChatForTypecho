<?php
//版本检测
$version = isset($_POST['version']) ? addslashes($_POST['version']) : '';
$json=file_get_contents('https://www.tongleer.com/api/interface/TleChat.php?action=update&version='.$version.'&domain='.$_SERVER['SERVER_NAME']);
echo $json;
?>