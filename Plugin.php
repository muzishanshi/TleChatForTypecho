<?php
/**
 * 站长聊天室插件为Typecho站长提供用户聊天室功能
 * @package 站长聊天室
 * @author 二呆
 * @version 1.0.3
 * @link http://www.tongleer.com/
 * @date 2019-03-22
 */
class TleChat_Plugin implements Typecho_Plugin_Interface{
    // 激活插件
    public static function activate(){
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
		$config_room=@unserialize(ltrim(file_get_contents(dirname(__FILE__).'/../../plugins/TleChat/config/config_room.php'),'<?php die; ?>'));
		$json=file_get_contents('https://www.tongleer.com/api/interface/TleChat.php?action=update&version=3&domain='.$_SERVER['SERVER_NAME']);
		$result=json_decode($json,true);
		$div=new Typecho_Widget_Helper_Layout();
		$div->html('
			版本检查：'.$result["content"].'<br />
			<small>注：若前台点击午反应，则可能是jquery冲突，只需把插件目录下Plugin.php中footer函数的加载jquery的代码删掉即可。</small>
			<p>
				<script src="https://apps.bdimg.com/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
				<input type="hidden" id="objectId" value="'.$config_room["objectId"].'" />
				<input type="button" id="clearAudio" value="清空所有录音" />
				<input type="button" id="delRoom" value="删除当前聊天室" />
				<input type="button" id="createRoom" value="创建新聊天室" />
				<script>
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
			<iframe src="'.urldecode($result["url"]).'" width="100%" height="700" scrolling = "no"></iframe>
			<small style="color:#aaaaaa">站长聊天室插件为站长和用户提供聊天室功能，让站长与用户之间的联系更加友爱，支持文本、长文本、语音聊天、图片传输及站长之间的QQ、微信、支付宝打赏，共同建立一个友爱的联盟。</small>
		');
		$div->render();
		
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
	
	public static function header(){
		$cssUrl = Helper::options()->pluginUrl . '/TleChat/chat/ui/css/layui.css';
		echo '<link rel="stylesheet" href="'.$cssUrl.'"  media="all">';
	}
	
	public static function footer(){
		$cssUrl = Helper::options()->pluginUrl . '/TleChat/chat/ui/css/layui.css';
		echo '
		<div style="position:fixed;bottom:0;right:0;">
			<button id="btnChatroom" class="layui-btn layui-btn-normal">聊天室</button>
		</div>
		<script src=https://apps.bdimg.com/libs/jquery/1.7.1/jquery.min.js></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/layer/2.3/layer.js"></script>
		<script>
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
		</script>
	';
	}
}