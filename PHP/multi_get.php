<?php

	function my_getm($sock, $user_input ,$ip)
	{
		$type = "TYPE I\r\n";
		$passive = "PASV\r\n";
			
		socket_send($sock, $type, strlen($type), 0);
		while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		{
	
			if((null != strstr($buff,"200 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
				break;
			
		}
			

		if((null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
			return TRUE;

		socket_send($sock, $passive, strlen($passive), 0);
		while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		{

			if((null != strstr($buff,"227 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
				break;
			
		}
			
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
			$file = substr($user_input, 4,strlen($user_input));
				
			$size = "SIZE ".$file."\r\n";
			socket_send($sock, $size, strlen($size), 0);
				
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
			{
				if((null != strstr($buff,"213 ")) || (null != strstr($buff,"501 ")) > 0 || (null != strstr($buff,"500 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")) || (null != strstr($buff,"550 ")))
					break;
			}
				
			$file_size = (int)substr($buff, 4, strlen($buff));

			$message_to_server = "RETR ".$file."\r\n";

			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
				
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
			{
	
				if((null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")) || (null != strstr($buff,"450 ")) || (null != strstr($buff,"226 ")) || (null != strstr($buff,"250 ")) || (null != strstr($buff,"451 ")) || (null != strstr($buff,"426 ")) || (null != strstr($buff,"425 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"421 ")))
					break;
			}
		
			 if((null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")))
			{
				echo $buff."\n";

				$down = 2.0;
				$temp = bcdiv((string)$file_size,'100',2);

				$temp1 = $temp;
			
				$fp = fopen($file,"w");

				if($fp == null)
				{
					socket_close($newsock);
		        		while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
					{
						echo $buff;
						if(null != strstr($buff,"226 "))
							break;
					}
					echo "\n";
					return FALSE;			
				}
				else
				{
					$file_size_value = 0.0;
					echo "Downloading [";
		        		while(($bytes = socket_recv($newsock, $buff, 1024, 0)) > 0)
				        {
						$file_size_value += $bytes;
						$temp1 = (double)($temp * $down);
						while($temp1 <= $file_size_value)
						{
							echo "#";
							$down += 2.0;
							$temp1 = (double)($temp * $down);
						}			
	
						fwrite($fp,$buff);
					}
	
					fclose($fp);
					echo "] 100%\n";
					socket_close($newsock);
			
			        	while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
					{
						echo trim($buff)."(".$file.")\n";
						if(null != strstr($buff,"226 "))
							break;
					}
					echo "\n";
					return TRUE;
				}
			}
			else
				echo trim($buff)."(".$file.")\n\n";
		}
		return FALSE;
	}
?>
