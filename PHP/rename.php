<?php
		function my_rename($sock, $old_name, $new_name)
		{
			
			$message_to_server = "RNFR ".$old_name."\r\n";
		
			$temp = -1;	
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
		                if(null != strstr($buff,"550 "))
					$temp = 1;
	
				if((null != strstr($buff,"350 ")) || (null != strstr($buff,"450 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"421 ")))
					break;
			}
			
			if($temp == 1)
				return FALSE;
			
			$message_to_server = "RNTO ".$new_name."\r\n";
			
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
				echo $buff;
	
				if((null != strstr($buff,"250 ")) || (null != strstr($buff,"532 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"503 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"553 ")) || (null != strstr($buff,"421 ")))
					break;
			}
			echo "\n";
			return TRUE;
		}

?>
