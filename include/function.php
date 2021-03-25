<?php
/**
* 清空录音目录
*/
function delDir($directory){//自定义函数递归的函数整个目录
    if(file_exists($directory)){//判断目录是否存在，如果不存在rmdir()函数会出错
        if($dir_handle=@opendir($directory)){//打开目录返回目录资源，并判断是否成功
            while($filename=readdir($dir_handle)){//遍历目录，读出目录中的文件或文件夹
                if($filename!='.' && $filename!='..'){//一定要排除两个特殊的目录
                    $subFile=$directory."/".$filename;//将目录下的文件与当前目录相连
                    if(is_dir($subFile)){//如果是目录条件则成了
                        delDir($subFile);//递归调用自己删除子目录
                    }
                    if(is_file($subFile)){//如果是文件条件则成立
                        unlink($subFile);//直接删除这个文件
                    }
                }
            }
            closedir($dir_handle);//关闭目录资源
            rmdir($directory);//删除空目录
        }
    }
}
/**
* 删除会话
*/
function delRoom($conv_id, $appId, $appKey){
	$url="https://hk72pmwt.api.lncld.net/1.2/rtm/conversations/".$conv_id;
    $body=array();
    $patoken=json_encode($body);
    $res = tlechat_httpCurl($url,"",array(),"DELETE", $appId, $appKey);
    $result = array();
    $result = json_decode($res, true);
    //var_dump($result);
	return $result;
}
/**
* 创建会话
*/
function createRoom($name,$users, $appId, $appKey){
	$url="https://hk72pmwt.api.lncld.net/1.2/rtm/conversations";
    $body=array(
		"name"=>$name,
		"m"=>$users
    );
	$patoken=json_encode($body);
    $res = tlechat_httpCurl($url,$patoken,array(),"POST", $appId, $appKey);
    $result = array();
    $result = json_decode($res, true);
    //var_dump($result);
	return $result;
}
/**
 * 发起一个请求到指定接口
 * 
 * @param string $url 请求的接口
 * @param array $body 参数
 * @param int $header 头
 * @param int $method 请求方式
 * @return string json请求结果
 */
function tlechat_httpCurl($url, $body, $header = array(), $method = "POST", $appId, $appKey){
    array_push($header, 'X-LC-Id: '.$appId);
	array_push($header, 'X-LC-Key: '.$appKey.",master");
    array_push($header, 'Content-Type:application/json');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
    switch ($method){ 
        case "GET" : 
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        break; 
        case "POST": 
            curl_setopt($ch, CURLOPT_POST,true); 
        break; 
        case "PUT" : 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
        break; 
        case "DELETE":
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 
        break; 
    }
    
    curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    if (isset($body{3}) > 0) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    if (count($header) > 0) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    $ret = curl_exec($ch);
    $err = curl_error($ch);
    
    curl_close($ch);
    if ($err) {
        return $err;
    }

    return $ret;
}
?>