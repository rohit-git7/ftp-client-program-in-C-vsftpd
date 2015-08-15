<?php

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
	
?>
