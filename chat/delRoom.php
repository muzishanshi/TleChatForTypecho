<?php
header("Content-type: text/html; charset=utf-8");
include '../../../../config.inc.php';
include_once "../include/function.php";
date_default_timezone_set("Etc/GMT-8");
$query = $db->select('value')->from('table.options')->where('name = ?', 'plugin:TleChat');
$result = $db->fetchAll($query);
$pluginOption = unserialize($result[0]["value"]);

$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=="delRoom"){
	$config_room=@unserialize(ltrim(file_get_contents(dirname(__FILE__).'/../../../plugins/TleChat/config/config_room.php'),'<?php die; ?>'));
	if(!isset($pluginOption["appId"])||!isset($pluginOption["appKey"])){echo('有未填写参数');exit;}
	if($config_room["objectId"]==""){echo('聊天室为空，不必删除。');exit;}
	//删除聊天室
	$result=delRoom($config_room["objectId"], $pluginOption["appId"], $pluginOption["appKey"]);

	file_put_contents(dirname(__FILE__).'/../../../plugins/TleChat/config/config_room.php','<?php die; ?>'.serialize(array(
		'objectId'=>"",
		'createdAt'=>""
	)));
	echo('删除完成');exit;
}
?>