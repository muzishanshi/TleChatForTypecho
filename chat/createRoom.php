<?php
header("Content-type: text/html; charset=utf-8");
include '../../../../config.inc.php';
include_once "../include/function.php";
date_default_timezone_set("Etc/GMT-8");
$db = Typecho_Db::get();

$query = $db->select('value')->from('table.options')->where('name = ?', 'plugin:TleChat');
$result = $db->fetchAll($query);
$pluginOption = unserialize($result[0]["value"]);

if(empty($pluginOption["appId"])||empty($pluginOption["appKey"])){echo('有未填写参数');exit;}
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

	$get=TleChat_Plugin::getOptions();
	$get["objectId"]=$result["objectId"];
	$get["createdAt"]=$result["createdAt"];
	TleChat_Plugin::saveOptions($get);

	echo('创建成功');exit;
}
?>