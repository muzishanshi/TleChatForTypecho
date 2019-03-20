<?php
header("Content-type: text/html; charset=utf-8");
include '../../../../config.inc.php';
date_default_timezone_set("Etc/GMT-8");

$action = isset($_POST['action']) ? addslashes(trim($_POST['action'])) : '';
if($action=="ip"){
	echo client_ip(0,true);
}else if($action=="audio"){
	$mp3Name = isset($_POST['mp3Name']) ? addslashes(trim($_POST['mp3Name'])) : '';
	move_uploaded_file(@$_FILES['file']['tmp_name'], dirname(__FILE__)."/upload/audio/".$mp3Name);
	echo Helper::options()->pluginUrl."/TleChat/chat/upload/audio/".$mp3Name;
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