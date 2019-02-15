<html><head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>workerman-chat PHP聊天室 Websocket(HTLM5/Flash)+PHP多进程socket实时推送技术</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jquery-sinaEmotion-2.1.0.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	
  <script type="text/javascript" src="js/swfobject.js"></script>
  <script type="text/javascript" src="js/web_socket.js"></script>
  <script type="text/javascript" src="js/jquery.min.js"></script>
   <script type="text/javascript" src="js/jquery-sinaEmotion-2.1.0.min.js"></script>
	<script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
	
		<!--<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">  
	<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
  <script type="text/javascript">
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    // 如果浏览器不支持websocket，会使用这个flash自动模拟websocket协议，此过程对开发者透明
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    // 开启flash的websocket debug
    WEB_SOCKET_DEBUG = true;

	    // 连接服务端
    ws = new WebSocket("ws://"+document.domain+":7272");
    
    
    // 当有消息时根据消息类型显示不同信息
    ws.onmessage = onmessage; 
    ws.onclose = function() {
    	  console.log("连接关闭，定时重连");
       };
    ws.onerror = function() {
     	  console.log("出现错误");
       };

    function onopen(){

			$uname = $('#uname').val();
			$uid = $('#uid').val();
			$pwd = $('#pwd').val();

			var login_data = '{"type":"login","uname":"'+$uname+'","uid":"'+$uid+'","pwd":"'+$pwd+'"}';
        
        console.log("websocket握手成功，发送登录数据:"+login_data);
        
        ws.send(login_data);
    }

    // 服务端发来消息时
    function onmessage(e)
    {
    	
    	
        var data = JSON.parse(e.data);
        switch(data['type']){
            // 服务端ping客户端
            case 'ping':
                ws.send('{"type":"pong"}');
                break;
            // 登录 更新用户列表
            case 'login':
            
           console.log(data.content);
                //{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"}
//              say(data['client_id'], data['client_name'],  data['client_name']+' 加入了聊天室', data['time']);
//              if(data['client_list'])
//              {
//                  client_list = data['client_list'];
//              }
//              else
//              {
//                  client_list[data['client_id']] = data['client_name']; 
//              }
//              flush_client_list();
//              console.log(data['client_name']+"登录成功");
                break;
//          // 发言
            case 'say':
             console.log(data.send);
             alert(data.send);
             
//              //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
//              say(data['from_client_id'], data['from_client_name'], data['content'], data['time']);
                break;
                
            case 'file':
             console.log(data.file);
             alert(data.file);
             
//              //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
//              say(data['from_client_id'], data['from_client_name'], data['content'], data['time']);
              
              
              var content = data.file;
							var date = new Blob([content]);
							var downloadUrl = window.URL.createObjectURL(date);
							var anchor = document.createElement("a");
							anchor.href = downloadUrl;
							anchor.download = data.name;
							anchor.click();
							window.URL.revokeObjectURL(date); 

              
              
                break;
                    
                
                
                
//          // 用户退出 更新用户列表
            case 'logout':
             alert(e);
            
//              //{"type":"logout","client_id":xxx,"time":"xxx"}
//              say(data['from_client_id'], data['from_client_name'], data['from_client_name']+' 退出了', data['time']);
//              delete client_list[data['from_client_id']];
//              flush_client_list();
        }
    }



  </script>
</head>
<body >
	
	
	<div id="login">
		
		<input type="text" name="uname" id="uname" value="1" placeholder="用户名"/>
		<input type="password" name="pwd" id="pwd" value="123456" placeholder="密码"/>
		<button onclick="onopen()">登录</button>
		
	</div>
	
	<div style="padding: 100px 100px 10px;">
		<form class="bs-example bs-example-form" role="form">
			<div class="input-group">
				<span class="input-group-addon">@</span>
				<input type="text" class="form-control" placeholder="twitterhandle">
			</div>
			<br>
			<div class="input-group">
				<input type="text" class="form-control">
				<span class="input-group-addon">.00</span>
			</div>
			<br>
			<div class="input-group">
				<span class="input-group-addon">$</span>
				<input type="text" class="form-control">
				<span class="input-group-addon">.00</span>
			</div>
		</form>
</div>
	
	
	
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<div id="">
		<input type="text" name="send" id="send" value="10000000" placeholder="发送的内容" />
		<input type="text" name="send_uid" id="send_uid" value="1" placeholder="发送者id"/>
		<input type="text" name="send_uname" id="send_uname" value="我是10发送的" placeholder="发送者的名字" />
		<input type="text" name="for_uid" id="for_uid" value="2" placeholder="发给谁的id"/>
		<button onclick="onSubmit()">发送</button>
	</div>
	
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />	
	
<div>
    上传文件 ： <input type="file" name = "file" id = "fileId" /> 
</div>

<div onclick="getFile()">
	获取文件流
</div>

<script>
    function getFile() {
    	var $uid = 1;
    	var $for_uid = 2;
    //js写法        
    var file=document.getElementById('fileId').files[0];//获取文件流
    var fileName =  file.name;//获取文件名
    //jq写法
    var file = $('#fileId')[0].files[0];
    
//  console.log(file)
////var json = JSON.stringify(file, function (key,value) { return value.toString().toUpperCase()}); 
//// var json =  JSON.parse(file)
//  alert(JSON.stringify(file))
    
    
    var login_data = '{"type":"file","uid":"'+$uid+'","for_uid":"'+$for_uid+'","data":"'+file+'","name":"'+fileName+'"}';
        
//  console.log(login_data);
        
    ws.send(login_data);
    
//var content = file;
//var data = new Blob([content]);
//var downloadUrl = window.URL.createObjectURL(data);
//var anchor = document.createElement("a");
//anchor.href = downloadUrl;
//anchor.download = fileName;
//anchor.click();
//window.URL.revokeObjectURL(data); 
    
var img = document.createElement("img");

img.src = window.URL.createObjectURL($('#fileId')[0].files[0]);
console.log(img.src)
img.height = 200;

img.onload = function(e) {
    window.URL.revokeObjectURL(this.src);
    
    console.log(this.src)
}

$('#add').append(img);

var info = document.createElement("span");

info.innerHTML = $('#fileId')[0].name + ": " + $('#fileId')[0].size + " bytes";

$('#add').append(info);
    
    }
	</script>
	
	
	
	
	
	
	
	
	
	<script type="text/javascript">
		
		    // 提交对话
    function onSubmit() {
			
			$send = $('#send').val();
			$send_uid = $('#send_uid').val();
			$send_uname = $('#send_uname').val();
			$for_uid =  $('#for_uid').val();
			
			var login_data = '{"type":"say","send_uname":"'+ $send_uname +'","send":"'+$send+'","send_uid":"'+$send_uid+'","for_uid":"'+$for_uid+'"}';
       
        console.log("websocket握手成功，发送登录数据:"+login_data);
        
        ws.send(login_data);
        
    }

		
	</script>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<div id="add">
		55555555555555
	</div>
	
	
	
	
	
	
    <!--<div class="container">
	    <div class="row clearfix">
	        <div class="col-md-1 column">
	        </div>
	        <div class="col-md-6 column">
	           <div class="thumbnail">
	               <div class="caption" id="dialog"></div>
	           </div>
	           <form onsubmit="onSubmit(); return false;">
	                <select style="margin-bottom:8px" id="client_list">
                        <option value="all">所有人</option>
                    </select>
                    <textarea class="textarea thumbnail" id="textarea"></textarea>
                    <div class="say-btn">
                        <input type="button" class="btn btn-default face pull-left" value="表情" />
                        <input type="submit" class="btn btn-default" value="发表" />
                    </div>
               </form>
               <div>
               &nbsp;&nbsp;&nbsp;&nbsp;<b>房间列表:</b>（当前在&nbsp;房间<?php echo isset($_GET['room_id'])&&intval($_GET['room_id'])>0 ? intval($_GET['room_id']):1; ?>）<br>
               &nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=1">房间1</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=2">房间2</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=3">房间3</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=4">房间4</a>
               <br><br>
               </div>
               <p class="cp">PHP多进程+Websocket(HTML5/Flash)+PHP Socket实时推送技术&nbsp;&nbsp;&nbsp;&nbsp;Powered by <a href="http://www.workerman.net/workerman-chat" target="_blank">workerman-chat</a></p>
	        </div>
	        <div class="col-md-3 column">
	           <div class="thumbnail">
                   <div class="caption" id="userlist"></div>
               </div>
               <a href="http://workerman.net:8383" target="_blank"><img style="width:252px;margin-left:5px;" src="/img/workerman-todpole.png"></a>
	        </div>
	    </div>
    </div>-->
    <!--<script type="text/javascript">var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F7b1919221e89d2aa5711e4deb935debd' type='text/javascript'%3E%3C/script%3E"));</script>-->
    <script type="text/javascript">
//    // 动态自适应屏幕
//    document.write('<meta name="viewport" content="width=device-width,initial-scale=1">');
//    $("textarea").on("keydown", function(e) {
//        // 按enter键自动提交
//        if(e.keyCode === 13 && !e.ctrlKey) {
//            e.preventDefault();
//            $('form').submit();
//            return false;
//        }
//
//        // 按ctrl+enter组合键换行
//        if(e.keyCode === 13 && e.ctrlKey) {
//            $(this).val(function(i,val){
//                return val + "\n";
//            });
//        }
//    });
    </script>
</body>
</html>
