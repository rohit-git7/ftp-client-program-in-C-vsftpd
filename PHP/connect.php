<?php

	ini_set('display_errors','Off');
	error_reporting(0);
	ini_set("precision",2);

	$ip = "192.168.26.138";
	$user_name = "rohit";
	$pass = "redhat";

	#$ip = gethostbyname('ftp.secyt.gov.ar');
	
	function my_connect($ip)
	{
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$conn = socket_connect($sock, $ip, 21);
	
		echo "Connected to ".$ip.".\n";
	
		while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		{
			echo $buff;
			if((null != strstr($buff,"220 ")) || (null != strstr($buff,"421 ")))
				break;
		}
	
		echo "\n";
		return $sock;
	}


	function my_login($sock, $user_name, $pass)
	{
		$user = "USER ".$user_name."\r\n";
	
		socket_send($sock, $user, strlen($user), 0);
	        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
	        {
	                echo $buff;
			if(null != strstr($buff,"331 ")) 
				$temp = 1;
			
			if(null != strstr($buff,"230 "))
				$temp = 2;
	
			if(null != strstr($buff,"530 "))
	                        $temp = 0;
	
			if((null != strstr($buff,"331 ")) || (null != strstr($buff,"332 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"230 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"421 ")))
				break;
		
	        }
		echo "\n";
		
		if($temp == 1)
		{
			$password = "PASS ".$pass."\r\n";
	
			socket_send($sock, $password, strlen($password), 0);
	
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
		                echo $buff;
				if(null != strstr($buff,"501 "))
					$temp = 3;
			
				if(null != strstr($buff,"230 "))
					$temp = 2;
	
				if(null != strstr($buff,"530 "))
	        	                $temp = 0;
	
				if((null != strstr($buff,"503 ")) || (null != strstr($buff,"202 ")) || (null != strstr($buff,"332 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"230 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"421 ")))
					break;
			
	     		}
			echo "\n";
			
		}
	
		if($temp == 0 || $temp == 3)
			return FALSE;
	
	
		$message_to_server = "SYST\r\n";
		
		socket_send($sock, $message_to_server, strlen($message_to_server), 0);
	        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
	        {
	                echo $buff;
	
			if((null != strstr($buff,"215 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
				break;
			
	        }
		echo "\n";
	
		return TRUE;
	}
	
	while(true)
	{
		$temp = 0;

		$type = "TYPE I\r\n";
		$passive = "PASV\r\n";

		echo "ftp> ";
	
		$user_input = fgets(STDIN);
		$user_input = substr($user_input, 0, strlen($user_input) - 1);

		function my_close($sock)
		{
			$quit = "QUIT\r\n";

			socket_send($sock, $quit, strlen($quit), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
		                echo $buff;
				if((null != strstr($buff,"221 ")) || (null != strstr($buff,"500 ")))
					break;
			}
		
			if(null != strstr($buff,"221 "))
				return TRUE;	
			else
				return FALSE;
	
		}

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

			}
			else
				return FALSE;
		
		}

		function my_chdir(strncmp($sock, $user_input)
		{
			$message_to_server = "CWD ".$user_input."\r\n";
			
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
		                echo $buff;
				if((null != strstr($buff,"250 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"421 ")))
					break;
			}
			echo "\n";
	
			if(null != strstr($buff,"250 "))
				return TRUE;
			else
				return FALSE;


		}
	
		function my_rename($sock, $old_name, $new_name)
		{
			
			$message_to_server = "RNFR ".$old_name."\r\n";
			
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
			
		function my_rmdir($sock, $user_input)
		{
			$message_to_server = "RMD ".$user_input."\r\n";
			
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
				echo $buff;
	
				if((null != strstr($buff,"250 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
					break;

			}
			echo "\n";
			if(null != strstr($buff,"250 "))
				return TRUE;
			else
				return FALSE;

		}

		function my_delete($sock, $user_input)
		{
			$message_to_server = "DELE ".$user_input."\r\n";
			
			socket_send($sock, $message_to_server, strlen($message_to_server), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
				echo $buff;
	
				if((null != strstr($buff,"450 ")) || (null != strstr($buff,"250 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
					break;

			}
			
			echo "\n";
			if(null != strstr($buff,"250 "))
				return TRUE;
			else
				return FALSE;

		}
		
	
		function my_rawlist($sock)
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
				continue;

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
			
		        		while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
				        {
						echo $buff;
				 		if(null != strstr($buff,"226 "))
							break;
					}
					echo "\n";
				}
	
			}

		}

		function my_get($sock, $user_input)
		{
			socket_send($sock, $type, strlen($type), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
				echo $buff;
	
				if((null != strstr($buff,"200 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
					break;
			
			}
			
			echo "\n";

			if((null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
				continue;

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
					echo $buff;
	
					if((null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")) || (null != strstr($buff,"450 ")) || (null != strstr($buff,"226 ")) || (null != strstr($buff,"250 ")) || (null != strstr($buff,"451 ")) || (null != strstr($buff,"426 ")) || (null != strstr($buff,"425 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"550 ")) || (null != strstr($buff,"421 ")))
						break;
				}
				echo "\n";
		
				 if((null != strstr($buff,"125 ")) || (null != strstr($buff,"150 ")))
				{

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
							echo $buff;
					 		if(null != strstr($buff,"226 "))
								break;
						}
						echo "\n";
					}
				}
	
			}

		}
		
		function my_put($sock, $user_input)
		{
			socket_send($sock, $type, strlen($type), 0);
		        while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
		        {
				echo $buff;
	
				if((null != strstr($buff,"200 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
					break;
			
			}
			
			echo "\n";

			if((null != strstr($buff,"530 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"504 ")) || (null != strstr($buff,"421 ")))
				continue;

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
				$file = substr($user_input, 4,strlen($user_input));

				$message_to_server = "STOR ".$file."\r\n";

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
						
					}		
					else
					{
						$file_size = (double)filesize($file);
						$temp = bcdiv((string)$file_size,'100.0',2);
	
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
					 		if(null != strstr($buff,"226 "))
								break;
						}
						echo "\n";
					}
				}

			}	
		}

		if(strncmp($user_input,"!cd ",4) == 0)
		{
			$user_input = substr($user_input, 4, strlen($user_input));
			$val = chdir($user_input);
			if($val == TRUE)
			{
				echo "Directory successfully changed.\n\n";
			}
			else
			{
				if(!is_dir($user_input))
					echo "Error: No such file or directory.\n\n";
				else
					echo "Error: Permission denied.\n\n";
			}
			
		}

		if(strncmp($user_input,"!pwd ",4) == 0 || strcmp($user_input,"!pwd") == 0)
		{
			echo "You are in driectory ".getcwd().".\n\n";
		}

		if(strncmp($user_input,"!mkdir ",7) == 0)
		{
			$user_input = substr($user_input, 7, strlen($user_input));
			if(mkdir($user_input,0755) == TRUE)
			{
				echo "Directory successfully created.\n\n";
			}
			else
			{
				echo "Error: Could not create directory.\n\n";
			}
		}

		if(strncmp($user_input,"!rmdir ",7) == 0)
		{
			$user_input = substr($user_input, 7, strlen($user_input));
			if(rmdir($user_input) == TRUE)
			{
				echo "Directory successfully removed.\n\n";
			}
			else
			{
				if(!is_dir($user_input))
					echo "Error: No such file or directory.\n\n";
				else
					echo "Error: Permission denied.\n\n";
			}
			
		}

		if(strncmp($user_input,"!rm ",4) == 0)
		{
			$user_input = substr($user_input, 4, strlen($user_input));
			if(unlink($user_input) == TRUE)
			{
				echo "File successfully removed.\n\n";
			}
			else
			{
				echo "Error: Could not remove file.\n\n";
			}
			
		}

		if(strncmp($user_input,"!rename ",8) == 0)
		{
			$user_input = substr($user_input, 8, strlen($user_input));
			sscanf($user_input,"%s %s",$old_name,$new_name);

			if(rename($old_name,$new_name) == TRUE)
			{
				echo "File successfully renamed.\n\n";
			}
			else
			{
				echo "Error: Could not rename file.\n\n";
			}
			
		}
	}
?>
