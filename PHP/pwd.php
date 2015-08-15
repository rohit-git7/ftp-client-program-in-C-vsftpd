<?php
		function my_pwd($sock)
		{
			$message_to_server = "PWD\r\n";
			
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
		                echo $buff;
				if((null != strstr($buff,"257 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"421 ")))
					break;
			}
			echo "\n";
			
			if(null != strstr($buff,"257 "))
			{
				$start = -1;
				$end = -1;
	
				for($i = 0; $i < strlen($buff); $i++ )
				{
					if($buff[$i] == '"' && $start == -1)
					{
						$start = $i;
					}
					else if($buff[$i] == '"' && $start != -1)
					{
						$end = $i;
						break;
					}		

				}	

				$stri = substr($buff,$start + 1,$end - $start - 1);
				return $stri;
			}
			else
				return FALSE;
		
		}

?>
