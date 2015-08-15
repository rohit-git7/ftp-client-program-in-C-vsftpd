<?php
		function my_mkdir($sock, $user_input)
		{
			$message_to_server = "MKD ".$user_input."\r\n";
			
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
				echo $buff;
	
				if((null != strstr($buff,"257 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
					break;

			}
			echo "\n";
			if(null != strstr($buff, "257 "))
			{
				$pwd_dir = my_pwd($sock);
				return $pwd_dir."/".$user_input;
				
			}
			else
				return FALSE;
		}

?>
