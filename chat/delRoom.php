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
	$get=TleChat_Plugin::getOptions();
	
	if(empty($pluginOption["appId"])||empty($pluginOption["MasterKey"])){echo('有未填写参数');exit;}
	if(empty($get["objectId"])){echo('聊天室为空，不必删除。');exit;}
	//删除聊天室
	$result=delRoom(@$get["objectId"], $pluginOption["appId"], $pluginOption["MasterKey"]);

	$get["objectId"]="";
	$get["createdAt"]="";
	TleChat_Plugin::saveOptions($get);
	
	echo('删除完成');exit;
}
?>