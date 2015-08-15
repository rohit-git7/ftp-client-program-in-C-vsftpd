<?php

	function my_rawlist($sock, $user_input ,$ip)
	{
		$type = "TYPE I\r\n";
		$passive = "PASV\r\n";		

		socket_send($sock, $type, strlen($type), 0);
	        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
	        {
			echo $buff;
	
			if((null != strstr($buff,"200 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
				break;
		
		}
		
		echo "\n";

		if((null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
			return FALSE;

		socket_send($sock, $passive, strlen($passive), 0);
		while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		{
			echo $buff;
	
			if((null != strstr($buff,"227 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
				break;
			
		}
		echo "\n";	
			
		if(null != strstr($buff,"227"))
		{
			$delim = " ,)";
			$i = 0;
			$count = 0;
			$port = 0;
					
			while(($i < strlen($buff)) && $count < 4)
			{
				if($buff[$i] == ',')
					$count++;

				$i++;
			}
			
			$count = 0;
			$token = strtok(substr($buff,$i,strlen($buff)),$delim);

			while($token !== false)
			{
				if(is_numeric($token[0]))
				{
					if($count == 1)
					{
						$port += (int)$token;
					}
						
					if($count == 0)
					{

						$port = (int)$token * 256;
						$count++;
					}
				}
			
				$token = strtok($delim);
			}

			$newsock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
				
			$conn = socket_connect($newsock, $ip, $port);

			$count = sscanf($user_input,"%s %s %s",$command,$flag,$dir);

			if(strncmp($user_input,"ls -l",5) == 0 && $count < 3)
			{
				$message_to_server = "LIST -l\r\n";
			}
			else if(strncmp($user_input,"ls -l ",6) == 0 && $count == 3)
			{
				$message_to_server = "LIST -l ".$dir."\r\n";
					
			}
			else
			{
				$message_to_server = "NLST\r\n";
			}

	
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
				
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
			{
				echo $buff;
	
				if((null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")) || (null != strstr($buff,"450 ")) || (null != strstr($buff,"226 ")) || (null != strstr($buff,"250 ")) || (null != strstr($buff,"451 ")) || (null != strstr($buff,"426 ")) || (null != strstr($buff,"425 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
					break;
			}
			echo "\n";
		
			if((null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")))
			{
		        	while(($bytes = socket_recv($newsock, $buff, 1024, 0)) > 0)
				{
					echo $buff;	
				}
				
				echo "\n";

				socket_close($newsock);
			
				$time_arr = array('sec' => 1, 'usec' => 500000);
		
				socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $time_arr);
					
		        	while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
				{
					echo $buff;
				 	if(null != strstr($buff,"226 "))
						break;
				}
				echo "\n";
				return TRUE;	
			}
	
		}
		return FALSE;
	}
?>
