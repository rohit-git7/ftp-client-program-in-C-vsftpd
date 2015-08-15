<?php

	function my_uniqput($sock, $user_input, $ip)
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
			$file = substr($user_input, 8,strlen($user_input));

			$message_to_server = "STOU ".$file."\r\n";

			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
				
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
			{
				echo $buff;
	
				if((null != strstr($buff,"550 ")) || (null != strstr($buff,"452 ")) || (null != strstr($buff,"532 ")) || (null != strstr($buff,"553 ")) || (null != strstr($buff,"552 ")) || (null != strstr($buff,"551 ")) || (null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")) || (null != strstr($buff,"450 ")) || (null != strstr($buff,"226 ")) || (null != strstr($buff,"250 ")) || (null != strstr($buff,"451 ")) || (null != strstr($buff,"426 ")) || (null != strstr($buff,"425 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"421 ")))
					break;
			}
			echo "\n";
		
			if((null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")))
			{

				$fp = fopen($file,"r");
					
				if($fp == null)
				{	
					echo "Error: Could not open file.\n\n";
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
					$file_size = filesize($file);
					$temp = bcdiv((string)$file_size,'100',2);
	
					$temp1 = $temp;
					$down = 2.0;
						
					$file_size_value = 0.0;
					echo "Uploading[";
			       		while(($buff = fread($fp, 1024)) != null)
				        {
						$total = strlen($buff);
						$file_size_value += ((double)$total);
						$temp1 = (double)($temp * $down);
						while($temp1 <= $file_size_value)
						{
							echo "#";
							$down += 2.0;
							$temp1 = (double)($temp * $down);
						}			
						$sent_data = 0;	
	
						while($sent_data < $total)
						{
							$bytes = socket_send($newsock,$buff,strlen($buff),0);
							if($bytes < strlen($buff))
							{
								$buff = substr($buff,$bytes,strlen($buff));
								
							}
							$sent_data += $bytes;
								
						}
					}
						
					echo "] 100%\n\n";


					fclose($fp);
					socket_close($newsock);
			
	        			while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
			        	{
						echo $buff;
				 		if(null != strstr($buff,"226 ") || null != strstr($buff,"421 "))
							break;
					}
					echo "\n";
					return TRUE;
				}

			}
			echo trim($buff)."(".$file.")\n\n";
		}	
		return FALSE;
	}

?>
