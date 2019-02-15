<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;

class Events
{

   
   /**
    * 有消息时
    * @param int $client_id
    * @param mixed $message
    */
   public static function onMessage($client_id, $message){
   		$client_id = $client_id;
		$message_data = json_decode($message, true);

//      // debug
//      echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";
//      
//      // 客户端传递的是json数据
        
        if(!$message_data)
        {
            return ;
        }
        
        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            // 客户端回应服务端的心跳
            case 'pong':
                return;
            // 客户端登录 message格式: {type:login, name:xx, uid:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
		    //1.建立连接
		        $connect=mysqli_connect('localhost','root','root','app_x','3306');
		        $sql_uid = "select uid,uname,pwd from my_user where uid = ". $message_data['uname'] .' and ' . $message_data['pwd'] ;
				$result_uid = mysqli_query($connect,$sql_uid);
		        while($row_uid = mysqli_fetch_array($result_uid)){
		              $uid = $row_uid['uid'];
		        }
			$message_data['uid'] = $uid;
   			Gateway::bindUid($client_id, $message_data['uid']);//绑定uid和$client_id
			Gateway::setSession($client_id, array('uid'=>$message_data['uid']));
		    //2.定义sql语句
		       $sql = "select uid,uname,pwd from my_user where uid = ". $message_data['uid'];
		    //3.发送SQL语句
		        $result = mysqli_query($connect,$sql);
				
		        while($row = mysqli_fetch_array($result)){
		        	
		              $uid = $row['uid'];
		              $uname = $row['uname'];
		              $pwd = $row['pwd'];
		        }
				
                // 判断是否有uid
                if(empty($message_data['uid']))
                {
                    return '请登录';
                }
				if($message_data['uid'] == $uid && $message_data['uname']== $uname && $message_data['pwd'] == $pwd){
					
					$sql_line = "select uid from my_line where uid = ". $message_data['uid'];
					$result_line = mysqli_query($connect,$sql_line);
						
						 while($ro_line = mysqli_fetch_array($result_line)){
			             	 $line_uid = $ro_line['uid'];
			       			 }
						if(empty($line_uid)){
							$line_sql = "INSERT INTO my_line (uid, uname,addtime,client_id) VALUES ( '{$message_data['uid']}', '{$message_data['uname']}',unix_timestamp(now()),'{$client_id}')";
							$result = mysqli_query($connect,$line_sql);
							
							if ($result == true) {
								$login = self::arr_data('登录成功');
								Gateway::sendToUid($message_data['uid'],json_encode($login));
							}else{
								$login = self::arr_data('系统繁忙，请稍后在试');
								Gateway::sendToUid($message_data['uid'],json_encode($login));
							}
						}else{
							$login = self::arr_data('登录成功');
							Gateway::sendToUid($message_data['uid'],json_encode($login));
						}
					
				}else{
					$login = self::arr_data('登录失败');
					Gateway::sendToUid($message_data['uid'],json_encode($login));
				}
//              var_dump($message_data['uname']);
//              var_dump($message_data['pwd']);
//              var_dump($_SERVER['REMOTE_ADDR']);
//              var_dump($message);
                // 把房间号昵称放到session中
//              $uid = $message_data['uid'];
//              $uname = htmlspecialchars($message_data['uname']);
//              $_SESSION['uid'] = $uid;
//              $_SESSION['uname'] = $uname;
              
                // 获取房间内所有用户列表 
//              $clients_list = Gateway::getClientSessionsByGroup($uid);
				
//              var_dump($clients_list);
				
				
//              foreach($clients_list as $tmp_client_id=>$item)
//              {
//                  $clients_list[$tmp_client_id] = $item['uname'];
//              }
//              $clients_list[$client_id] = $uname;
                
                // 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx} 
//              $new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'uname'=>htmlspecialchars($uname), 'time'=>date('Y-m-d H:i:s'));
//              Gateway::sendToGroup($uid, json_encode($new_message));
//              Gateway::joinGroup($client_id, $uid);
//             
//              // 给当前用户发送用户列表 
//              $new_message['client_list'] = $clients_list;
//              Gateway::sendToCurrentClient(json_encode($new_message));
                return;
                
            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
		    //1.建立连接
		        $connect=mysqli_connect('localhost','root','root','app_x','3306');
		    //2.定义sql语句
				$sql_line = "select uid,client_id from my_line where uid = ". $message_data['for_uid'];
		    //3.发送SQL语句
		        $result = mysqli_query($connect,$sql_line);
				
		        while($row = mysqli_fetch_array($result)){
		        	
		              $for_uid = $row['uid'];
		              $for_client_id = $row['client_id'];
		        }
				
				
				
				if(!Gateway::isUidOnline($message_data['for_uid'])){
                // 假设有个your_store_fun函数用来保存未读消息(这个函数要自己实现)
//              	your_store_fun($message);

					var_dump(11111);

	            }else{
	                // 在线就转发消息给对应的uid
			    $new_message = array(
                    'type'=>'say',
                    'send_uname' =>$message_data['send_uname'],//发送者的名字
                    'send'=>$message_data['send'], //发送的内容
                    'send_uid'=>$message_data['send_uid'],//发送者id
                    'for_uid'=>$message_data['for_uid'],//发给谁的id
                    'time'=>date('Y-m-d H:i:s'),//时间
                );
	                Gateway::sendToUid($message_data['for_uid'], json_encode($new_message));
					var_dump(22222);
					
	            }
				
				var_dump( $message_data['for_uid']);
				
//				Gateway::sendToClient('7f00000108fc00000001', json_encode($new_message));
//                  $new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
//                  return Gateway::sendToCurrentClient(json_encode($new_message));
//				Gateway::sendToUid($message_data['send_uid'],json_encode($new_message));
				
				
				return;
				
				
				case 'file':
				$new_message = array(
                    'type'=>'file',
                    'name' =>$message_data['name'],//发送者的名字
                    'file'=>$message_data['data'], //发送的内容
                    'uid'=>$message_data['uid'],//发送者id
                    'for_uid'=>$message_data['for_uid'],//发给谁的id
                    'time'=>date('Y-m-d H:i:s'),//时间
                );
				   Gateway::sendToUid($message_data['for_uid'], json_encode($new_message));
					var_dump($message_data['data']);
				  
				return;
				
				
				
				
//              // 非法请求
//              if(!isset($_SESSION['uid']))
//              {
//                  throw new \Exception("\$_SESSION['uid'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
//              }
//              $uid = $_SESSION['uid'];
//              $uname = $_SESSION['uname'];
//              
//              // 私聊
//              if($message_data['to_client_id'] != 'all')
//              {
//                  $new_message = array(
//                      'type'=>'say',
//                      'from_client_id'=>$client_id, 
//                      'from_client_name' =>$uname,
//                      'to_client_id'=>$message_data['to_client_id'],
//                      'content'=>"<b>对你说: </b>".nl2br(htmlspecialchars($message_data['content'])),
//                      'time'=>date('Y-m-d H:i:s'),
//                  );
//                  Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
//                  $new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
//                  return Gateway::sendToCurrentClient(json_encode($new_message));
//              }
//              
//              $new_message = array(
//                  'type'=>'say', 
//                  'from_client_id'=>$client_id,
//                  'from_client_name' =>$uname,
//                  'to_client_id'=>'all',
//                  'content'=>nl2br(htmlspecialchars($message_data['content'])),
//                  'time'=>date('Y-m-d H:i:s'),
//              );
//              return Gateway::sendToGroup($uid ,json_encode($new_message));
        }


		    //4.关闭连接
		       mysqli_close($connect);
   }

   
   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id){
	$connect = mysqli_connect('localhost','root','root','app_x','3306');
   	
   	$sql = "DELETE  FROM my_line where client_id =".'\''.$client_id.'\'';
	$result = mysqli_query($connect,$sql);
   }

/*
 * 统一返回数据
 */
	static function arr_data($data){
		$login = array(
	        'type'=>'login',
	        'content'=>$data,
	        'time'=>date('Y-m-d H:i:s'),
	    );
		return 	$login;
	}
	


}
