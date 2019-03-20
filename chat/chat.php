<?php
include '../../../../config.inc.php';
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TleChat');
$db = Typecho_Db::get();

$config_room=@unserialize(ltrim(file_get_contents(dirname(__FILE__).'/../../../plugins/TleChat/config/config_room.php'),'<?php die; ?>'));

$queryTitle= $db->select("value")->from('table.options')->where('name = ?', "title"); 
$rowTitle = $db->fetchRow($queryTitle);
$querySiteUrl= $db->select("value")->from('table.options')->where('name = ?', "siteUrl"); 
$rowSiteUrl = $db->fetchRow($querySiteUrl);

$uid = isset($_GET['uid']) ? addslashes($_GET['uid']) : '';
$queryUser= $db->select()->from('table.users')->where('uid = ?', $uid); 
$rowUser = $db->fetchRow($queryUser);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?=$rowTitle["value"];?>聊天室</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width">
  <meta name="viewport" content="user-scalable=no">
  <meta name="viewport" content="initial-scale=1,maximum-scale=1">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp" />
  <link rel="stylesheet" href="css/style.css" />
  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://cdn.bootcss.com/layer/3.1.0/layer.js"></script>
  <script src="https://cdn.bootcss.com/push.js/1.0.9/push.min.js"></script>
  <!--<script type="text/javascript" src="https://pv.sohu.com/cityjson?ie=utf-8"></script>-->
  <link rel="stylesheet" href="<?=Helper::options()->pluginUrl;?>/TleChat/chat/ui/css/amazeui.min.css"/>
  <link rel="stylesheet" href="<?=Helper::options()->pluginUrl;?>/TleChat/chat/ui/css/admin.css"  media="all">
  <style type="text/css">
	a{text-decoration:none;}
	body {
		background-attachment:fixed;
		background-repeat:no-repeat;
		background-size:cover;
		-moz-background-size:cover;
		-webkit-background-size:cover;
	}
	#send-btn-record{  
		-webkit-touch-callout:none;
		-webkit-user-select:none;
		-khtml-user-select:none;
		-moz-user-select:none;
		-ms-user-select:none;
		user-select:none;
	}
  </style>
</head>
<body>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
  以获得更好的体验！</p>
<![endif]-->
<header class="am-topbar am-topbar-inverse admin-header">
  <div class="am-topbar-brand">
	<?php if($uid){$nickname=$rowUser['screenName']?$rowUser['screenName']:$rowUser['name'];}?>
    <strong>欢迎<?=@$nickname;?></strong> <small></small>
  </div>

  <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

  <div class="am-collapse am-topbar-collapse" id="topbar-collapse">
    <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
      <li><a href="javascript:void();"></a></li>
    </ul>
  </div>
</header>

<div class="am-cf admin-main">
  <!-- sidebar start -->
  <div class="admin-sidebar am-offcanvas" id="admin-offcanvas">
    <div class="am-offcanvas-bar admin-offcanvas-bar">
      <ul class="am-list admin-sidebar-list">
        <li><a href="javascript:;"><span class="am-icon-home"></span> 大厅</a></li>
      </ul>

      <div class="am-panel am-panel-default admin-sidebar-panel">
        <div class="am-panel-bd">
          <p><span class="am-icon-bookmark"></span> 公告</p>
          <p><?=$option->notice?$option->notice:"时光静好，与君语；细水流年，与君同。";?></p>
        </div>
		<img src="https://www.tongleer.com/api/web/?action=img&type=ad" width="100%" alt="" />
      </div>
	  
    </div>
  </div>
  <!-- sidebar end -->

  <!-- content start -->
  <div class="admin-content">
    <div class="admin-content-body">
      <div class="am-cf am-padding am-padding-bottom-0">
        <div class="am-fl am-u-sm-centered">
			<div class="am-form-inline">
			  <div class="am-form-group">
				<?php if($uid){ ?>
				<input id="input-name" type="hidden" value="<?=@$nickname;?>">
				<?php }?>
				<input id="input-site-url" class="am-form-field am-input-sm" type="text" value="" placeholder="http(s)://网址(选填)">
				<input id="input-qq" class="am-form-field am-input-sm" type="text" value="" placeholder="QQ号(选填)">
				<input id="input-qqpay-url" class="am-form-field am-input-sm" type="text" value="" placeholder="QQ钱包url(选填)">
				<input id="input-wxpay-url" class="am-form-field am-input-sm" type="text" value="" placeholder="微信支付url(选填)">
				<input id="input-alipay-url" class="am-form-field am-input-sm" type="text" value="" placeholder="支付宝url(选填)">
				<button id="open-btn" class="am-btn am-btn-secondary am-btn-sm">进入聊天室</button>
				<button id="quit-btn" style="display:none;" class="am-btn am-btn-secondary am-btn-sm">退出聊天室</button>
			  </div>
			</div>
		</div>
      </div>

      <div class="am-g error-log">
        <div class="am-u-sm-12 am-u-sm-centered">
			<ul class="am-comments-list">
				<div id="print-wall" class="print-wall">
					<iframe src="https://me.tongleer.com/weixin/cloud/chat/simple/chat_ad.php" width="100%" height="30"></iframe>
					<div id="msg-end" style="height:0px; overflow:hidden;bottom:0px;"></div>
				</div>
			</ul>
			<p>
				<div class="am-form-inline">
				  <div class="am-form-group">
					<font color="red"><span id="alerterror"></span></font>
					<input id="input-send" lay-verify="required" placeholder="输入聊天内容" autocomplete="off" class="am-form-field am-input-sm input-send">
					<button id="send-btn" class="am-btn am-btn-secondary am-btn-sm">发送</button>
					<button id="send-btn-as-file" class="am-btn am-btn-secondary am-btn-sm">发送长文本</button>
					<button id="send-btn-record" class="am-btn am-btn-secondary am-btn-sm">按住说话</button>
					
					<div id="send-btn-photo-div" class="am-form-group am-form-file">
					  <button type="button" class="am-btn am-btn-danger am-btn-sm">
						<i class="am-icon-cloud-upload"></i> <span id="file-list">选择要上传的文件</span></button>
					  <input id="send-btn-photo" type="file">
					</div>
					<script>
					  $(function() {
						$('#send-btn-photo').on('change', function() {
						  var fileNames = '';
						  $.each(this.files, function() {
							fileNames += this.name;
						  });
						  $('#file-list').html(fileNames);
						});
					  });
					</script>
				  </div>
				</div>
			</p>
        </div>
      </div>
    </div>
    <footer class="admin-content-footer">
      <p class="am-padding-left">
		CopyRight©<?=date("Y");?> <a href="<?php echo $rowSiteUrl["value"]; ?>"><?php echo $rowTitle["value"]; ?></a> Powered by <a href="http://www.emlog.net/" title="Emlog" rel="nofollow">Emlog</a> Plugin By <a id="rightdetail" href="http://www.tongleer.com" title="同乐儿">Tongleer</a>
	  </p>
    </footer>
  </div>
  <!-- content end -->
</div>
<a href="#" class="am-icon-btn am-icon-th-list am-show-sm-only admin-menu" data-am-offcanvas="{target: '#admin-offcanvas'}"></a>
<!--[if lt IE 9]>
<script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
<script src="<?=Helper::options()->pluginUrl;?>/TleChat/chat/ui/js/amazeui.ie8polyfill.min.js"></script>
<![endif]-->
<script src="<?=Helper::options()->pluginUrl;?>/TleChat/chat/ui/js/amazeui.min.js"></script>
  
<div id="recordingslist"></div>
<script src="https://cdn1.lncld.net/static/js/av-min-1.0.0.js"></script>
<script src="<?=Helper::options()->pluginUrl."/TleChat/chat/js/leancloud/dist/realtime.browser.min.js";?>"></script>
<script src="<?=Helper::options()->pluginUrl."/TleChat/chat/js/leancloud/plugins/typed-messages/dist/typed-messages.min.js";?>"></script>
<script>
	Push.Permission.request();

	var roomId = '<?=$config_room["objectId"];?>';
	var appId = '<?=$option->appId;?>';
	var appKey = '<?=$option->appKey;?>';
	
	AV.initialize(appId, appKey);
	
	var clientId = '游客';
	var realtime;
	var client;
	var messageIterator;
	var room;

	var firstFlag = true;
	var logFlag = false;
	var idClickEd=true;
	var blackFlag=false;

	var openBtn = document.getElementById('open-btn');
	var quitBtn = document.getElementById('quit-btn');
	var sendBtnAsFile = document.getElementById('send-btn-as-file');
	var sendBtn = document.getElementById('send-btn');
	var inputName = document.getElementById('input-name');
	var inputSiteUrl = document.getElementById('input-site-url');
	var inputQq = document.getElementById('input-qq');
	var inputQqUrl = document.getElementById('input-qqpay-url');
	var inputWxUrl = document.getElementById('input-wxpay-url');
	var inputAliUrl = document.getElementById('input-alipay-url');
	var inputSend = document.getElementById('input-send');
	var printWall = document.getElementById('print-wall');
	var msgEnd = document.getElementById('msg-end');
	var msgTime;

	bindEvent(openBtn, 'click', main);
	bindEvent(quitBtn, 'click', quitChat);
	bindEvent(sendBtn, 'click', sendMsg);
	bindEvent(sendBtnAsFile, 'click', sendMsgAsFile);
	
	if(getCookie("inputSiteUrl")){inputSiteUrl.value=getCookie("inputSiteUrl");}
	if(getCookie("inputQq")){inputQq.value=getCookie("inputQq");}
	if(getCookie("inputQqUrl")){inputQqUrl.value=getCookie("inputQqUrl");}
	if(getCookie("inputWxUrl")){inputWxUrl.value=getCookie("inputWxUrl");}
	if(getCookie("inputAliUrl")){inputAliUrl.value=getCookie("inputAliUrl");}
	
	bindEvent(document.body, 'keydown', function(e) {
	  if (false) {/*e.keyCode === 13&&inputSend==document.activeElement*/
		if (firstFlag) {
		  main();
		} else {
		  sendMsg();
		}
	  }
	});
	var isJoin=false;
	function main() {
	  if (!roomId) {
		layer.msg("请先创建聊天室");
		return;
	  }
	  if (inputName==null) {
		layer.msg("请先登录");
		return;
	  }
	  var val = inputName.value;
	  if (val) {
		clientId = val;
	  }
	  if(blackFlag){
		if(isBlacked()){
			
		}
	  }
	  setCookie("inputSiteUrl",inputSiteUrl.value,24);
	  setCookie("inputQq",inputQq.value,24);
	  setCookie("inputQqUrl",inputQqUrl.value,24);
	  setCookie("inputWxUrl",inputWxUrl.value,24);
	  setCookie("inputAliUrl",inputAliUrl.value,24);
	  inputName.disabled="false";
	  openBtn.style.display="none";
	  quitBtn.style.display="inline";
	  showLog('<span class="am-badge">正在连接，请等待。。。</span>');
	  if (!firstFlag) {
		client.close();
	  }
	  /*创建实时通信实例*/
	  realtime = new AV.Realtime({
		appId: appId,
		appKey: appKey,
		plugins: AV.TypedMessagesPlugin,
	  });
	  /*创建聊天客户端*/
	  realtime.createIMClient(clientId)
	  .then(function(c) {
		showLog('<span class="am-badge am-badge-success">连接成功！</span>');
		  if(blackFlag){
			
		  }
		firstFlag = false;
		idClickEd=false;
		client = c;
		client.on('disconnect', function() {
		  showLog('<span class="am-badge am-badge-secondary">正在重新连接，请耐心等待。。。</span>');
		});
		/*获取对话*/
		return c.getConversation(roomId);
	  })
	  .then(function(conversation) {
		if (conversation) {
		  return conversation;
		} else {
		  /*如果服务器端不存在这个 conversation*/
		  showLog('<span class="am-badge am-badge-secondary">房间不存在，创建了一个新的。</span>');
		  return client.createConversation({
			name: 'Typecho-Conversation',
			members: [
			  /*默认包含当前用户*/
			  /*'Who'*/
			],
			/*创建暂态的聊天室（暂态聊天室支持无限人员聊天，但是不支持存储历史）*/
			/*transient: true,*/
			/*默认的数据，可以放 conversation 属性等*/
			attributes: {
			  test: ''
			}
		  }).then(function(conversation) {
			showLog('<span class="am-badge am-badge-success">创建新房间成功</span>', "");
			roomId = conversation.id;
			return conversation;
		  });
		}
	  })
	  .then(function(conversation) {
		return conversation.join();
	  })
	  .then(function(conversation) {
		var members = "";
		members += '<div class="am-panel-bd">';
		var k=0;
		for (var i = 0; i < 63;i++){
			if(k>=conversation.members.length){
				break;
			}
			members += '<ul class="am-avg-sm-10 blog-team">';
				for(var j = 0; j < 9;j++){
					if(k<conversation.members.length){
						members += " <li>" + conversation.members[k] + "</li>";
						k++;
					}
				}
			members += "</ul>";
		}
		members += "</div></section>";
		showLog('<section class="am-panel am-panel-default"><div class="am-panel-hd">当前房间的成员列表共'+conversation.members.length+'人（最多可容纳500人）</div>', members,false,"#DDDDDD");
		if (conversation.members.length > 500) {
		  return conversation.remove(conversation.members[0]).then(function(conversation) {
			showLog('<span class="am-badge am-badge-danger">人数过多，踢掉：</span> ', conversation.members[0],false,"#CCCCCC");
			return conversation;
		  });
		}
		return conversation;
	  })
	  .then(function(conversation) {
		/*获取聊天历史*/
		room = conversation;
		messageIterator = conversation.createMessagesIterator();
		getLog(function() {
		  printWall.scrollTop = printWall.scrollHeight;
		  showLog('<span class="am-badge am-badge-success">已经加入房间，可以开始聊天。</span>');
		  isJoin=true;
		});
		/*房间接受消息*/
		conversation.on('message', function(message) {
		  if (!msgTime) {
			/*存储下最早的一个消息时间戳*/
			msgTime = message.timestamp;
		  }
		  showMsg(message);
		});
	  })
	  .catch(function(err) {
		console.error(err);
	  })
	}
	
	function sendMsg() {
	  $("#alerterror").html("");
	  var val = inputSend.value;
	  /*不让发送空字符*/
	  if (!String(val).replace(/^\s+/, '').replace(/\s+$/, '')) {
		$("#alerterror").html('请输入点文字！');
		return;
	  }
	  if(blackFlag){
		  
	  }
	  if(val.length>140){
		  $("#alerterror").html('文字太长了，可尝试一下发送长文本！');
		  return;
	  }
	  $.post("upload.php",{action:"ip"},function(data){
		  /*向这个房间发送消息，这段代码是兼容多终端格式的，包括 iOS、Android、Window Phone*/
		  var message=new AV.TextMessage(val);
		  message.setAttributes({
			ip: data,
			siteUrl:inputSiteUrl.value,
			qq:inputQq.value,
			qqUrl:inputQqUrl.value,
			wxUrl:inputWxUrl.value,
			aliUrl:inputAliUrl.value
		  });
		  room.send(message).then(function(message) {
			/*发送成功之后的回调*/
			inputSend.value = '';
			showLog('<li class="am-comment"><a href="javascript:;"><img src="https://q1.qlogo.cn/g?b=qq&nk='+(inputQq.value!=""?inputQq.value:1)+'&s=100" alt="" class="am-comment-avatar" width="16" height="16"></a><div class="am-comment-main"><header class="am-comment-hd"><div class="am-comment-meta"><a href="'+inputSiteUrl.value+'" class="am-comment-author"><font color="#000">自己</a></a></div></header>', encodeHTML(encodeHTML(message.text)),false,"red");
			printWall.scrollTop = printWall.scrollHeight;
		  });
	  });
	}

	function sendMsgAsAudio(val) {
		$("#alerterror").html("");
		$.post("upload.php",{action:"ip"},function(data){
			var file=new AV.File.withURL('audio.mp3', val);
			file.save().then(function(file) {
				var message=new AV.AudioMessage(file);
				message.setAttributes({
					ip: data,
					siteUrl:inputSiteUrl.value,
					qq:inputQq.value,
					qqUrl:inputQqUrl.value,
					wxUrl:inputWxUrl.value,
					aliUrl:inputAliUrl.value
				});
				return room.send(message);
			}).then(function(message) {
				/*发送成功之后的回调*/
				showLog('<li class="am-comment"><a href="javascript:;"><img src="https://q1.qlogo.cn/g?b=qq&nk='+(inputQq.value!=""?inputQq.value:1)+'&s=100" alt="" class="am-comment-avatar" width="16" height="16"></a><div class="am-comment-main"><header class="am-comment-hd"><div class="am-comment-meta"><a href="'+inputSiteUrl.value+'" class="am-comment-author"><font color="#000">自己</a></a></div></header>', createAudio(message.getFile().url()),false,"red");
				printWall.scrollTop = printWall.scrollHeight;
			}).catch(console.warn);
		});
	}
	
	$('#send-btn-photo').change(function(){
		$("#alerterror").html("");
		if(isBlacked()){
			
		}
		$.post("upload.php",{action:"ip"},function(data){
			if(jQuery("input[type='file']").val()!=""&&$('#send-btn-photo').value!=""){
				var fileUploadControl = $('#send-btn-photo')[0];
				var file=new AV.File('photo.jpg', fileUploadControl.files[0]);
				file.save().then(function(file) {
					var message=new AV.ImageMessage(file);
					message.setAttributes({
						ip: data,
						siteUrl:inputSiteUrl.value,
						qq:inputQq.value,
						qqUrl:inputQqUrl.value,
						wxUrl:inputWxUrl.value,
						aliUrl:inputAliUrl.value
					});
					return room.send(message);
				}).then(function(message) {
					/*发送成功之后的回调*/
					showLog('<li class="am-comment"><a href="javascript:;"><img src="https://q1.qlogo.cn/g?b=qq&nk='+(inputQq.value!=""?inputQq.value:1)+'&s=100" alt="" class="am-comment-avatar" width="16" height="16"></a><div class="am-comment-main"><header class="am-comment-hd"><div class="am-comment-meta"><a href="'+inputSiteUrl.value+'" class="am-comment-author"><font color="#000">自己</a></a></div></header>', createImage(message.getFile().url()),false,"red");
					printWall.scrollTop = printWall.scrollHeight;
				}).catch(console.warn);
			}
		});
	});

	/*发送多媒体消息示例*/
	function sendMsgAsFile() {
	  $("#alerterror").html("");
	  if(isBlacked()){
		  
	  }
	  var val = inputSend.value;
	  /*不让发送空字符*/
	  if (!String(val).replace(/^\s+/, '').replace(/\s+$/, '')) {
		$("#alerterror").html('请输入点文字！');
		return;
	  }
	  if(val.length>500){
		  $("#alerterror").html('长文本这也太长啦，适当缩短点吧！');
		  return;
	  }
	  $.post("upload.php",{action:"ip"},function(data){
		  var file= new AV.File('message.txt', {
			base64: b64EncodeUnicode(val),
		  });
		  file.save().then(function(file) {
			var message=new AV.FileMessage(file);
			message.setAttributes({
				ip: data,
				siteUrl:inputSiteUrl.value,
				qq:inputQq.value,
				qqUrl:inputQqUrl.value,
				wxUrl:inputWxUrl.value,
				aliUrl:inputAliUrl.value
			});
			return room.send(message);
		  }).then(function(message) {
			/*发送成功之后的回调*/
			inputSend.value = '';
			showLog('<li class="am-comment"><a href="javascript:;"><img src="https://q1.qlogo.cn/g?b=qq&nk='+(inputQq.value!=""?inputQq.value:1)+'&s=100" alt="" class="am-comment-avatar" width="16" height="16"></a><div class="am-comment-main"><header class="am-comment-hd"><div class="am-comment-meta"><a href="'+inputSiteUrl.value+'" class="am-comment-author"><font color="#000">自己</a></a></div></header>', createLink(message.getFile().url(),"red"),false,"red");
			printWall.scrollTop = printWall.scrollHeight;
		  }).catch(console.warn);
	  });

	}

	function b64EncodeUnicode(str) {
		return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
			return String.fromCharCode('0x' + p1);
		}));
	}

	/*显示接收到的信息*/
	function showMsg(message, isBefore) {
	  $("#alerterror").html("");
	  var text = message.text;
	  var from = message.from;
	  /*
	  var cip=returnCitySN["cip"];
	  var ip=message.getAttributes().ip;
	  */
	  if (message.from === clientId) {
		from = '自己';
	  }
	  if(isJoin&&message.from === clientId){
		  $("#alerterror").html("由于重名故不可显示内容，可更换一个昵称。");
		  return;
	  }
		var qqUrl="",wxUrl="",aliUrl="",fromHtml="";
		var ip="";
		if(typeof(message.getAttributes())!="undefined"){
			  ip=message.getAttributes().ip;
		}
		if(message.getAttributes().siteUrl!=""){
			var fromHtml='<li class="am-comment"><a href="https://wpa.qq.com/msgrd?v=3&uin='+message.getAttributes().qq+'&site=qq&menu=yes" target=_blank><img src="https://q1.qlogo.cn/g?b=qq&nk='+(message.getAttributes().qq!=""?message.getAttributes().qq:1)+'&s=100" alt="" class="am-comment-avatar" width="16" height="16"></a><div class="am-comment-main"><header class="am-comment-hd"><div class="am-comment-meta"><a href="'+message.getAttributes().siteUrl+'" target=_blank class="am-comment-author">'+encodeHTML(from)+'</a> 评论于 <time datetime="'+formatTime(message.timestamp)+'" title="'+formatTime(message.timestamp)+'">'+formatTime(message.timestamp)+'</time> '+ip+'';
		}else{
			var fromHtml='<li class="am-comment"><a href="https://wpa.qq.com/msgrd?v=3&uin='+message.getAttributes().qq+'&site=qq&menu=yes" target=_blank><img src="https://q1.qlogo.cn/g?b=qq&nk='+(message.getAttributes().qq!=""?message.getAttributes().qq:1)+'&s=100" alt="" class="am-comment-avatar" width="16" height="16"></a><div class="am-comment-main"><header class="am-comment-hd"><div class="am-comment-meta"><a href="javascript:;" target=_blank class="am-comment-author">'+encodeHTML(from)+'</a> 评论于 <time datetime="'+formatTime(message.timestamp)+'" title="'+formatTime(message.timestamp)+'">'+formatTime(message.timestamp)+'</time> '+ip+'';
		}
		if(message.getAttributes().qqUrl!=""){
			var qqUrl=' <a href=https://www.tongleer.com/api/web/?action=qrcode&url='+urlEncode(message.getAttributes().qqUrl)+' target=_blank title="QQ打赏"><img src="https://ws3.sinaimg.cn/large/ecabade5ly1fwwykb7jpsj201c01ca9z.jpg" width="16" /></a>';
		}
		if(message.getAttributes().wxUrl!=""){
			var wxUrl=' <a href=https://www.tongleer.com/api/web/?action=qrcode&url='+urlEncode(message.getAttributes().wxUrl)+' target=_blank title="微信打赏"><img src="https://ws3.sinaimg.cn/large/ecabade5ly1fwwykbkttmj201c01c3yd.jpg" width="16" /></a>';
		}
		if(message.getAttributes().aliUrl!=""){
			var aliUrl=' <a href=https://www.tongleer.com/api/web/?action=qrcode&url='+urlEncode(message.getAttributes().aliUrl)+' target=_blank title="支付宝打赏"><img src="https://ws3.sinaimg.cn/large/ecabade5ly1fwwykaow9ij201c01cmx1.jpg" width="16" /></a>';
		}
		var color="",isFloat="";
		if(isBefore){
			color="#DEB887";
		}else{
			color="blue";
		}
	  var pushBody="";
	  if (message instanceof AV.TextMessage&&message.type==AV.TextMessage.TYPE) {
		if (String(text).replace(/^\s+/, '').replace(/\s+$/, '')) {
		  showLog(fromHtml+qqUrl+wxUrl+aliUrl+ '</div></header>', message.text, isBefore,color);
		  pushBody=encodeHTML(from)+"说："+message.text;
		}
	  } else if (message instanceof AV.FileMessage&&message.type==AV.FileMessage.TYPE) {
		showLog(fromHtml+qqUrl+wxUrl+aliUrl+ '</div></header>', createLink(message.getFile().url(),"#DEB887"), isBefore,color);
		pushBody=encodeHTML(from)+"说：我发了一个文件";
	  } else if (message instanceof AV.ImageMessage&&message.type==AV.ImageMessage.TYPE) {
		  showLog(fromHtml+qqUrl+wxUrl+aliUrl+ '</div></header>', createImage(message.getFile().url()), isBefore,color);
		  pushBody=encodeHTML(from)+"说：我发了一张图片";
	  } else if (message instanceof AV.AudioMessage&&message.type==AV.AudioMessage.TYPE) {
		  showLog(fromHtml+qqUrl+wxUrl+aliUrl+ '</div></header>', createAudio(message.getFile().url()), isBefore,color);
		  pushBody=encodeHTML(from)+"说：我发了一段音频";
	  }
	  msgEnd.scrollIntoView();
	  printWall.scrollTop = printWall.scrollHeight;
	  if(!isBefore){
		Push.create($("title").html(), {
			body: pushBody,
			icon: '<?=Helper::options()->pluginUrl;?>/TleChat/chat/ui/images/icon.png',
			timeout: 8000,
			vibrate: [100, 100, 100],    
			onClick: function() {
				console.log(this);
			}  
		});
	  }
	}

	/*拉取历史*/
	bindEvent(printWall, 'scroll', function(e) {
	  if (printWall.scrollTop < 20) {
		getLog();
	  }
	});

	/*获取消息历史*/
	function getLog(callback) {
	  var height = printWall.scrollHeight;
	  if (logFlag) {
		return;
	  } else {
		/*标记正在拉取*/
		logFlag = true;
	  }
	  messageIterator.next().then(function(result) {
		var data = result.value;
		logFlag = false;
		/*存储下最早一条的消息时间戳*/
		var l = data.length;
		if (l) {
		  msgTime = data[0].timestamp;
		}
		for (var i = l - 1; i >= 0; i--) {
		  showMsg(data[i], true);
		}
		if (l) {
		  printWall.scrollTop = printWall.scrollHeight - height;
		}
		if (callback) {
		  callback();
		}
	  }).catch(function(err) {
		console.error(err);
	  });
	}

	/*demo 中输出代码*/
	function showLog(msg, data, isBefore,color="red") {
	  if (data) {
		/*console.log(msg, data);*/
		msg = msg + '<div class="am-comment-bd"><font color="'+color+'"><p><pre>' + data + '</pre></p></font></div></div></li>';
	  }
	  var p = document.createElement('p');
	  p.innerHTML = msg;
	  if (isBefore) {
		$("#print-wall").prepend(msg);
	  } else {
		$("#print-wall").append(msg);
	  }
	}

	function encodeHTML(source) {
	  return String(source)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/\\/g,'&#92;')
		.replace(/"/g,'&quot;')
		.replace(/'/g,'&#39;');
	}

	function formatTime(time) {
	  var date = new Date(time);
	  var month = date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
	  var currentDate = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
	  var hh = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
	  var mm = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
	  var ss = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
	  return date.getFullYear() + '-' + month + '-' + currentDate + ' ' + hh + ':' + mm + ':' + ss;
	}

	function createLink(url,color="blue") {
	  return '<a target="_blank" href="' + encodeHTML(url) + '"><font color="'+color+'">' + encodeHTML(url) + '</font></a>';
	}

	function createImage(url) {
	  return '<img alt="" src="' + encodeHTML(url) + '" />';
	}

	function createAudio(url) {
	  return '<audio controls=true src="' + encodeHTML(url) + '" />';
	}
	
	function bindEvent(dom, eventName, fun) {
	  if (window.addEventListener) {
		dom.addEventListener(eventName, fun);
	  } else {
		dom.attachEvent('on' + eventName, fun);
	  }
	}

	function quitChat(){
		if(confirm("确定要登出聊天室吗？")){
			room.quit().then(function(conversation) {
			  window.location.reload();
			}).catch(console.error.bind(console));
		}
	}
	/*****************************************
     * url编码函数
     * 输入参数：待编码的字符串
     * 输出参数：编码好的
     ****************************************/
     function urlEncode(String) {
        return encodeURIComponent(String).replace(/'/g,"%27").replace(/"/g,"%22");	
    }
	/*Cookie操作*/
	function clearCookie(){ 
		var keys=document.cookie.match(/[^ =;]+(?=\=)/g); 
		if (keys) { 
			for (var i = keys.length; i--;) 
			document.cookie=keys[i]+'=0;expires=' + new Date( 0).toUTCString() 
		} 
	}
	function setCookie(name,value,hours){  
		var d = new Date();
		d.setTime(d.getTime() + hours * 3600 * 1000);
		document.cookie = name + '=' + value + '; expires=' + d.toGMTString();
	}
	function getCookie(name){  
		var arr = document.cookie.split('; ');
		for(var i = 0; i < arr.length; i++){
			var temp = arr[i].split('=');
			if(temp[0] == name){
				return temp[1];
			}
		}
		return '';
	}
	function removeCookie(name){
		var d = new Date();
		d.setTime(d.getTime() - 10000);
		document.cookie = name + '=1; expires=' + d.toGMTString();
	}
	/*黑名单*/
	var blacklist=new Array();
	function isBlacked(){
		if($.inArray(clientId, blacklist)!=-1){
			return true;
		}
		return false;
	}
</script>
<script src="js/recordmp3.js"></script>
<script>
	var recorder = new MP3Recorder({
		debug:false,
		funOk: function () {
			/*log('初始化成功');*/
		},
		funCancel: function (msg) {
			log('此浏览器不支持语音聊天建议更换或者重装，比如Edge、火狐、UC、QQ、YY等浏览器。<br />提示：有些浏览器本身自带截图功能哦，可以任性发送图片了！');
			recorder = null;
		}
	});
	var mp3Blob;
	function funStart() {
		recorder.start();
		/*log('录音开始...');*/
	}
	function funStop() {
		recorder.stop();
		/*log('录音结束，MP3导出中...');*/
		recorder.getMp3Blob(function (blob) {
			/*log('MP3导出成功');*/
			mp3Blob = blob;
			var url = URL.createObjectURL(mp3Blob);
			var div = document.createElement('div');
			var hf = document.createElement('a');

			hf.href = url;
			hf.download = new Date().toISOString() + '.mp3';
			hf.innerHTML = hf.download;
			div.appendChild(hf);
			funUpload(mp3Blob);
		});
	}
	function log(str) {
		$("#recordingslist").append(str + '<br/>');
	}
	function funUpload(blob) {
		var fd = new FormData();
		var mp3Name = encodeURIComponent('audio_recording_' + new Date().getTime() + '.mp3');
		fd.append('mp3Name', mp3Name);
		fd.append('action', "audio");
		fd.append('file', blob);
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function () {
			if (xhr.readyState == 4 && xhr.status == 200) {
				sendMsgAsAudio(xhr.responseText);
			}
		};
		xhr.open('POST', 'upload.php',true);
		xhr.send(fd);
	}
	var timeout ;
	$("#send-btn-record").mousedown(function() {  
		timeout = setTimeout(function() {
			$("#send-btn-record").css({background: "green" });
			funStart();
		}, 1000);  
	});
	$("#send-btn-record").mouseup(function() {
		$("#send-btn-record").css({background: "#2C97E8" });
		clearTimeout(timeout);
		funStop();
	});
</script>
</body>

</html>
