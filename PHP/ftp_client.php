<?php

	include 'put.php';
	include 'get.php';
	include 'move.php';
	include 'multi_get.php';
	include 'multi_put.php';
	include 'uniq_put.php';
	include 'conn.php';
	include 'chdir.php';
	include 'size.php';
	include 'pwd.php';
	include 'chmod.php';
	include 'raw_list.php';
	include 'delete.php';
	include 'rename.php';
	include 'mkdir.php';
	include 'close.php';
	include 'rmdir.php';
	include 'list_client.php';

	ini_set('display_errors','Off');
	error_reporting(0);
	ini_set("precision",2);
	

	$ip = $argv[1];
	for($i = 0; $i < strlen($ip); $i++)
	{
		if($ip[$i] != '.' || (!is_numeric($ip[$i])))
			{
				$ip = gethostbyname($ip);
				break;
			}
	}

	if($ip == FALSE)
	{
		echo "Error: No route to host.\n";
		exit();
	}

	$sock = my_connect($ip);

	if($sock == FALSE)
		exit();

	echo "Name (".$ip."): ";
	$user_name = fgets(STDIN);
	$user_name = substr($user_name,0,strlen($user_name) - 1);
	
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
		echo "Password: ";
		$pass = fgets(STDIN);
		$pass = substr($pass,0,strlen($pass) - 1);
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
		exit();
	
	$message_to_server = "SYST\r\n";
		
	socket_send($sock, $message_to_server, strlen($message_to_server), 0);
	
	while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
	{
		echo $buff;
	
		if((null != strstr($buff,"215 ")) || (null != strstr($buff,"501 ")) || (null != strstr($buff,"500 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")))
			break;
			
	}
	echo "\n";

	while(TRUE)
	{
		echo "ftp> ";
	
		$user_input = fgets(STDIN);
		$user_input = substr($user_input, 0, strlen($user_input) - 1);

		if(strcmp($user_input,"exit") == 0 || strcmp($user_input,"quit") == 0 || strcmp($user_input,"bye") == 0 )
		{
			$val = my_close($sock);
			
			echo "\n";
		
			if($val == TRUE)
				exit();
		}

		if(strcmp($user_input,"pwd") == 0 || strncmp($user_input,"pwd ",4) == 0)
		{
			my_pwd($sock);
		}	

		if(strncmp($user_input,"cd ",3) == 0)
		{
			my_chdir($sock, substr($user_input,3,strlen($user_input)));
		}	

		if(strncmp($user_input,"rename ", 7) == 0)
		{
			$count = sscanf($user_input,"%s %s %s", $command, $old_name, $new_name);
			if($count != 3)
			{
				echo "Error: RENAME expects two arguments.\n\n";
				continue;
			}
			
			my_rename($sock, $old_name, $new_name);	
		}	

		if(strncmp($user_input, "mkdir ", 6) == 0)
		{
			$user_input = substr($user_input, 6, strlen($user_input));
			my_mkdir($sock, $user_input);
		}

		if(strncmp($user_input, "rmdir ", 6) == 0)
		{
			$user_input = substr($user_input, 6, strlen($user_input));
			my_rmdir($sock, $user_input);

		}

		if(strncmp($user_input, "rm ", 3) == 0)
		{
			$user_input = substr($user_input, 3, strlen($user_input));
			my_delete($sock, $user_input);

		}

		if(strncmp($user_input,"ls ", 3) == 0 || strcmp($user_input,"ls") == 0)
		{
			my_rawlist($sock, $user_input, $ip);
		}

		if(strncmp($user_input,"get ",4) == 0 && strncmp($user_input,"get -m ",7) != 0)
		{
			my_get($sock, $user_input, $ip);
		}

		if(strncmp($user_input,"get -m ",7) == 0)
		{
			$user_input = substr($user_input, 7, strlen($user_input));
			$user_input = $user_input."\n";

			$delim = " \n\t";
		
			$arr = array();
			$token = strtok($user_input, $delim);

			while($token !== false)
			{
				$token = "get ".$token;
				$arr[] = $token;
				$token = strtok($delim);
			}
			
			for($i = 0; $i < count($arr); $i++)
			{
				if($i == 0)
					$val = my_get($sock, $arr[$i], $ip);
				else
					$val = my_getm($sock, $arr[$i], $ip);
	
			}
		}

		if(strncmp($user_input,"put ",4) == 0 && strncmp($user_input,"put -m ",7) != 0)
		{
			my_put($sock, $user_input, $ip);
		}	

		if(strncmp($user_input,"uniqput ",8) == 0)
		{
			my_uniqput($sock, $user_input, $ip);
		}	

		if(strncmp($user_input,"put -m ",7) == 0)
		{
			$user_input = substr($user_input, 7, strlen($user_input));
			$user_input = $user_input."\n";

			$delim = " \n\t";
		
			$token = strtok($user_input, $delim);

			while($token !== false)
			{
				$token = "put ".$token;
				$newarr[] = $token;
				$token = strtok($delim);
			}
			
			for($i = 0; $i < count($newarr); $i++)
			{
				if($i == 0)
					$val = my_put($sock, $newarr[$i], $ip);
				else
					$val = my_putm($sock, $newarr[$i], $ip);
			}
		}
	
		if(strncmp($user_input,"size ",5) == 0)
		{
			$user_input = substr($user_input, 5, strlen($user_input));
			$val = my_size($sock, $user_input);
		
			if($val != -1)
				echo $val." bytes.\n\n";
			else
				echo "Couldn't find size of file ".$user_input.".\n\n";
		}
	
		if(strncmp($user_input,"chmod ",6) == 0)
		{
			$val = sscanf($user_input,"%s %s %s",$comm,$perm, $file);
			if($val < 3)
			{
				echo "Error: CHMOD expects 2 arguments.\n\n";
			}
			else
			{
				my_chmod($sock, $perm, $file);
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
			echo "You are in directory ".getcwd().".\n\n";
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

		if(strncmp($user_input,"mv ", 3) == 0 )
		{

			$count = sscanf($user_input, "%s %s %s",$comm,$old_name,$new_name);
			if($count != 3)
			{
				echo "Error: mv expects 2 arguments.\n\n";
			}
			else
			{

				if(my_move($sock, $old_name, $new_name))
				{
					echo "File moved successfully.\n\n";
				}
				else
				{
					echo "Error: Cannot move file.\n\n";

				}
			}
		}


		if(strncmp($user_input,"!ls ",4) == 0 || strcmp($user_input,"!ls") == 0)
		{

			$count = sscanf($user_input, "%s %s %s",$comm,$flg,$dir);
			if(strncmp($user_input,"!ls -l ",7) == 0 && $count == 3)
			{
				if(is_dir($dir))
				{
					my_list_client($dir);
				}
				else if(is_dir(getcwd()."/".$dir))
				{
					my_list_client(getcwd()."/".$dir);
				}
				else
				{
					echo "Error: \"".$dir."\" does not exist.\n\n";
				}
			}
			else if((strncmp($user_input,"!ls -l ",7) == 0 && $count == 2) || strcmp($user_input,"!ls -l") == 0)
			{	
				my_list_client(".");	
			}
			else if(strncmp($user_input,"!ls -",5) != 0 && $count == 2)
			{
				$array_details = scandir($flg);
				for($i = 0; $i < count($array_details); $i++)
					echo $array_details[$i]."\n";
				echo "\n";
			}
			else
			{
				$array_details = scandir(".");
				for($i = 0; $i < count($array_details); $i++)
					echo $array_details[$i]."\n";
				echo "\n";
			}

		
		}
	
		if(strncmp($user_input,"!chmod ",7) == 0)
		{
			$val = sscanf($user_input,"%s %s %s",$comm,$perm, $file);
			if($val < 3)
			{
				echo "Error: CHMOD expects 2 arguments.\n\n";
			}
			else
			{
				if(chmod($file, $perm))
				{
					echo "CHMOD: File permissions successfully changed.\n\n";
				}
				else
				{
					echo "Error: Could not change file permissions.\n\n";
				}
			}
		}
	
	}
?>
