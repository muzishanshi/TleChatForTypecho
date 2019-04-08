<?php
header("Content-type: text/html; charset=utf-8");
include '../../../../config.inc.php';
date_default_timezone_set("Etc/GMT-8");

$action = isset($_POST['action']) ? addslashes(trim($_POST['action'])) : '';
if($action=="ip"){
	echo client_ip(0,true);
}else if($action=="audio"){
	$mp3Name = isset($_POST['mp3Name']) ? addslashes(trim($_POST['mp3Name'])) : '';
	
	$audiodir=dirname(__FILE__)."/upload/audio/";
	if(!is_dir($audiodir)){mkdir ($audiodir, 0777, true );}
	
	if($_FILES['file']['error']!=0){
		$json=json_encode(array("status"=>"upload error","msg"=>"上传文件出错"));
		echo $json;
		exit;
	}
	if($_FILES['file']['size']>1024*1024*5){
		$json=json_encode(array("status"=>"upload too large","msg"=>"上传语音过大"));
		echo $json;
		exit;
	}
	$audio_type = array('mp3');
	$audio_extension = strtolower(pathinfo($mp3Name,  PATHINFO_EXTENSION));
	if (!in_array($audio_extension, $audio_type)) {
		$json=json_encode(array("status"=>"upload format error","msg"=>"上传语音格式出错"));
		echo $json;
		exit;
	}
	
	move_uploaded_file(@$_FILES['file']['tmp_name'], $audiodir.$mp3Name);
	$mp3url=Helper::options()->pluginUrl."/TleChat/chat/upload/audio/".$mp3Name;
	$json=json_encode(array("status"=>"upload success","msg"=>"上传成功","mp3url"=>$mp3url));
	echo $json;
	exit;
}
/**
 * 获取客户端IP地址
 * @param int $type [IP地址类型]
 * @param bool $strict [是否以严格模式获取]
 * @return mixed [客户端IP地址]
 */
function client_ip($type = 0, $strict = false){
    $ip = null;
    // 0 返回字段型地址(127.0.0.1)
    // 1 返回长整形地址(2130706433)
    $type = $type ? 1 : 0;
    if ($strict) {
        /* 防止IP地址伪装的严格模式 */
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    /* IP地址合法性验证 */
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];
    return $ip[$type];
}
?>