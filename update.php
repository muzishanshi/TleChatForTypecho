<?php
//版本检测
include '../../../config.inc.php';

$version = isset($_POST['version']) ? addslashes($_POST['version']) : '';

$json = TleChat_Plugin::tle_chat_curl('https://www.tongleer.com/api/interface/TleChat.php?action=update&version='.$version.'&domain='.$_SERVER['SERVER_NAME'],json_encode(array()),array(),"GET");
echo $json;
?>