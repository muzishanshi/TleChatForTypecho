<?php
/**
 * 站长聊天室插件为Typecho站长提供用户聊天室功能
 * @package 站长聊天室
 * @author 二呆
 * @version 1.0.6
 * @link http://www.tongleer.com/
 * @date 2019-10-02
 */
define('TLECHAT_VERSION', '6');
class TleChat_Plugin implements Typecho_Plugin_Interface{
    // 激活插件
    public static function activate(){
		$get=TleChat_Plugin::getOptions();
		TleChat_Plugin::saveOptions($get);
		
		Typecho_Plugin::factory('Widget_Archive')->header = array('TleChat_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('TleChat_Plugin', 'footer');
        return _t('插件已经激活');
    }

    // 禁用插件
    public static function deactivate(){
        return _t('插件已被禁用');
    }

    // 插件配置面板
    public static function config(Typecho_Widget_Helper_Form $form){
		$options = Typecho_Widget::widget('Widget_Options');
		$plug_url = $options->pluginUrl;
		$get=TleChat_Plugin::getOptions();
		$div=new Typecho_Widget_Helper_Layout();
		$div->html('
			版本检查：<span id="versionCode"></span><br />
			温馨提示：
			<br />
			1、此页面为<font color="red">旧版</font>站长聊天室功能，填写并提交下面（<a href="https://leancloud.cn/" target="_blank"><font color="blue">leancloud</font></a>）参数后再进行创建、删除、清空聊天室操作。
			<br />
			2、若使用<font color="red">新版</font>的<font color="blue">即时聊天</font>插件，体验更多聊天室功能，则需要更新插件。
			<p>
				<script src="https://apps.bdimg.com/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
				<input type="hidden" id="objectId" value="'.@$get["objectId"].'" />
				<input type="button" id="clearAudio" value="清空所有录音" />
				<input type="button" id="delRoom" value="删除当前聊天室" />
				<input type="button" id="createRoom" value="创建新聊天室" />
				<script>
					$.post("'.$plug_url.'/TleChat/update.php",{version:'.TLECHAT_VERSION.'},function(data){
						var data=JSON.parse(data);
						$("#versionCode").html(data.content);
						$("#chatUrl").html(\'<a href="https://www.tongleer.com" target = "_blank">站长聊天室</a>&nbsp;|&nbsp;<a href="\'+decodeURIComponent(data.url)+\'" target = "_blank">站长直播间</a>\');
					});
					$("#clearAudio").click(function(){
						$.post("'.$plug_url.'/TleChat/chat/clearAudio.php",{action:"clearAudio"},function(data){
							alert("清空录音成功");
						});
					});
					$("#delRoom").click(function(){
						$.post("'.$plug_url.'/TleChat/chat/delRoom.php",{action:"delRoom"},function(data){
							alert(data);
						});
					});
					$("#createRoom").click(function(){
						var flag=false;
						if($("#objectId").val()!=""){
							if(confirm("确认当前聊天室已经销毁后可创建新聊天室，还要继续吗？")){
								flag=true;
							}
						}else{
							flag=true;
						}
						if(flag){
							$.post("'.$plug_url.'/TleChat/chat/createRoom.php",{action:"createRoom",uid:"'.Typecho_Cookie::get('__typecho_uid').'"},function(data){
								alert(data);
							});
						}
					});
				</script>
			</p>
			<div id="chatUrl"></div>
			<small style="color:#aaaaaa">站长聊天室插件为站长和用户提供聊天室功能，让站长与用户之间的联系更加友爱，支持文本、长文本、语音聊天、图片传输及站长之间的QQ、微信、支付宝打赏，共同建立一个友爱的联盟。</small>
		');
		$div->render();
		
		$isEnableJQuery = new Typecho_Widget_Helper_Form_Element_Radio('isEnableJQuery', array(
            'y'=>_t('是'),
            'n'=>_t('否')
        ), 'y', _t('是否加载JQuery'), _t("用于解决jquery冲突的问题，如果主题head中自带jquery，需要选择否；如果主题中未加载jquery，则需要选择是。"));
		$form->addInput($isEnableJQuery->addRule('enum', _t(''), array('y', 'n')));
		//QQ、微信、支付宝链接设置
		$appId = new Typecho_Widget_Helper_Form_Element_Text('appId', array('value'), '', _t('leancloud的appId'), _t('前台聊天室配置<a href="https://leancloud.cn/" target="_blank">leancloud</a>的appId'));
        $form->addInput($appId);
		$appKey = new Typecho_Widget_Helper_Form_Element_Text('appKey', array('value'), '', _t('leancloud的appKey'), _t('前台聊天室配置<a href="https://leancloud.cn/" target="_blank">leancloud</a>的appKey'));
        $form->addInput($appKey);
		$notice = new Typecho_Widget_Helper_Form_Element_Text('notice', array('value'), '', _t('公告'), _t('输入前台显示的公告'));
        $form->addInput($notice);
    }

    // 个人用户配置面板
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    }
	
	public static function getOptions(){
		$db = Typecho_Db::get();
		$query= $db->select('value')->from('table.options')->where('name = ?', 'plugin-custom:TleChat'); 
		$row = $db->fetchRow($query);
		$themeOption=array();
		if($row){
			$themeOption = unserialize(stripslashes($row["value"]));
		}
		unset($db);
		return $themeOption;
	}
	public static function saveOptions($data){
		$db = Typecho_Db::get();
		$query= $db->select('value')->from('table.options')->where('name = ?', 'plugin-custom:TleChat'); 
		$row = $db->fetchRow($query);
		$themeOption=array();
		if($row){
			$update = $db->update('table.options')->rows(array('value'=>addslashes(serialize($data))))->where('name=?',"plugin-custom:TleChat");
			$updateRows= $db->query($update);
		}else{
			$insert = $db->insert('table.options')->rows(array('value' => addslashes(serialize($data)), 'name' => 'plugin-custom:TleChat'));
			$insertId = $db->query($insert);
		}
		unset($db);
	}
	
	/**
	 * 发起一个请求到指定接口
	 * @param string $url 请求的接口
	 * @param array $body 参数
	 * @param int $header 头
	 * @param int $method 请求方式
	 * @return string json请求结果
	 */
	public static function tle_chat_curl($url, $body, $header = array(), $method = "POST", $token = ""){
		array_push($header, 'Accept:application/json');
		array_push($header, 'Content-Type:application/json');
		array_push($header, 'http:multipart/form-data');
		array_push($header, 'Authorization:Bearer '.$token);
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
	
	public static function header(){
		$cssUrl = Helper::options()->pluginUrl . '/TleChat/chat/ui/css/layui.css';
		echo '<link rel="stylesheet" href="'.$cssUrl.'"  media="all">';
		$options = Typecho_Widget::widget('Widget_Options');
		$option=$options->plugin('TleChat');
		if($option->isEnableJQuery=="y"){
			echo '<script src="https://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>';
		}
	}
	
	public static function footer(){
		$cssUrl = Helper::options()->pluginUrl . '/TleChat/chat/ui/css/layui.css';
		echo '
		<div style="position:fixed;bottom:60px;right:2%;z-index:999999;">
			<button id="btnChatroom" class="layui-btn layui-btn-xs">聊天室</button>
		</div>
		<script src="https://www.tongleer.com/api/web/include/layui/layui.js"></script>
		<script>
		layui.use("layer", function(){
			var $ = layui.jquery, layer = layui.layer;
			$("#btnChatroom").click(function(){
				layer.open({
					type: 2
					,title: "聊天室"
					,id: "chatroom"
					,area: ["95%", "95%"]
					,shade: 0
					,maxmin: true
					,offset: "auto"
					,content: "'.Helper::options()->pluginUrl.'/TleChat/chat/chat.php?uid='.Typecho_Cookie::get('__typecho_uid').'"
					,btn: ["关闭"]
					,yes: function(){
					  layer.closeAll();
					}
					,zIndex: layer.zIndex
					,success: function(layero){
					  layer.setTop(layero);
					}
				});
			});
		});
		</script>
	';
	}
}