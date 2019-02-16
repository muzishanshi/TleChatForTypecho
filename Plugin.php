<?php
/**
 * 站长聊天室插件为Typecho站长提供聊天室功能
 * @package 站长聊天室
 * @author 二呆
 * @version 1.0.1
 * @link http://www.tongleer.com/
 * @date 2018-11-06
 */
class TleChat_Plugin implements Typecho_Plugin_Interface{
    // 激活插件
    public static function activate(){
        return _t('插件已经激活');
    }

    // 禁用插件
    public static function deactivate(){
        return _t('插件已被禁用');
    }

    // 插件配置面板
    public static function config(Typecho_Widget_Helper_Form $form){
		$config=@unserialize(ltrim(file_get_contents(dirname(__FILE__).'/../../plugins/TleChat/config.php'),'<?php die; ?>'));
		$json=file_get_contents('https://www.tongleer.com/api/interface/TleChat.php?action=update&version=1&domain='.$_SERVER['SERVER_NAME'].'&token='.$config["token"]);
		$result=json_decode($json,true);
		$div=new Typecho_Widget_Helper_Layout();
		$div->html('
			版本检查：'.$result["content"].'
			<iframe src="'.urldecode($result["url"]).'" width="100%" height="700" scrolling = "no"></iframe>
			<small style="color:#aaaaaa">站长聊天室插件为Typecho站长提供聊天室功能，让站长之间的联系更加友爱，支持文本、长文本、语音聊天、图片传输及站长之间的QQ、微信、支付宝打赏，共同建立一个友爱的站长联盟。</small>
		');
		$div->render();
		
		//QQ、微信、支付宝链接设置
		$qqUrl = new Typecho_Widget_Helper_Form_Element_Text('qqUrl', array('value'), 'https://i.qianbao.qq.com/wallet/sqrcode.htm?m=tenpay&f=wallet&u=2293338477&a=1&n=Mr.%E8%B4%B0%E5%91%86&ac=26A9D4109C10A5D5C08964FCFD5634EAC852E009B700ECDA2A064092BCF6C016', _t('QQ支付二维码url'), _t('此处只是例子，可使用<a href="https://cli.im/deqr/" target="_blank">草料二维码</a>将二维码图片转成url地址填入其中，进入聊天室时需要重新填入该地址，此处只为保存之用。'));
        $form->addInput($qqUrl);
		$wechatUrl = new Typecho_Widget_Helper_Form_Element_Text('wechatUrl', array('value'), 'wxp://f2f0XXfQeK36aDieMEjmveUENW16IZMdDk_c', _t('微信支付二维码url'), _t('此处只是例子，可使用<a href="https://cli.im/deqr/" target="_blank">草料二维码</a>将二维码图片转成url地址填入其中，进入聊天室时需要重新填入该地址，此处只为保存之用。'));
        $form->addInput($wechatUrl);
		$aliUrl = new Typecho_Widget_Helper_Form_Element_Text('aliUrl', array('value'), 'HTTPS://QR.ALIPAY.COM/FKX03546YRHSVIW3YUK925', _t('支付宝支付二维码url'), _t('此处只是例子，可使用<a href="https://cli.im/deqr/" target="_blank">草料二维码</a>将二维码图片转成url地址填入其中，进入聊天室时需要重新填入该地址，此处只为保存之用。'));
        $form->addInput($aliUrl);
		$token = new Typecho_Widget_Helper_Form_Element_Text('token', array('value'), '', _t('token'), _t('在<a href="http://www.tongleer.com/wp-login.php?action=register" target="_blank">同乐儿</a>注册并申请token后联系二呆绑定域名即为授权状态（未授权版仅可以发送文本消息）。'));
        $form->addInput($token);
		
		$token = @isset($_POST['token']) ? addslashes(trim($_POST['token'])) : '';
		if($token!=""){
			file_put_contents(dirname(__FILE__).'/config.php','<?php die; ?>'.serialize(array(
				'token'=>$token
			)));
		}
    }

    // 个人用户配置面板
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    }
}