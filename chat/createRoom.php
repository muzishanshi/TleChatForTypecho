<?php
header("Content-type: text/html; charset=utf-8");
include '../../../../config.inc.php';
include_once "../include/function.php";
date_default_timezone_set("Etc/GMT-8");
$db = Typecho_Db::get();

$query = $db->select('value')->from('table.options')->where('name = ?', 'plugin:TleChat');
$result = $db->fetchAll($query);
$pluginOption = unserialize($result[0]["value"]);

if(!isset($pluginOption["appId"])||!isset($pluginOption["appKey"])){echo('有未填写参数');exit;}
$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=="createRoom"){
	$uid = isset($_POST['uid']) ? addslashes($_POST['uid']) : '';
	$queryUser= $db->select()->from('table.users')->where('uid = ?', $uid); 
	$rowUser = $db->fetchRow($queryUser);
	$queryOption= $db->select("value")->from('table.options')->where('name = ?', "title"); 
	$rowOption = $db->fetchRow($queryOption);
	//创建聊天室
	$nickname=$rowUser['screenName']?$rowUser['screenName']:$rowUser['name'];
	$result=createRoom($rowOption["value"],array($nickname), $pluginOption["appId"], $pluginOption["appKey"]);

	file_put_contents(dirname(__FILE__).'/../../../plugins/TleChat/config/config_room.php','<?php die; ?>'.serialize(array(
		'objectId'=>$result["objectId"],
		'createdAt'=>$result["createdAt"]
	)));

	echo('创建成功');exit;
}
?>