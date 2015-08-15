<?php

	function my_chmod($sock, $perm, $file)
	{
		
		$message_to_server = "SITE CHMOD ".$perm." ".$file."\r\n";
		socket_send($sock, $message_to_server, strlen($message_to_server), 0);
				
	       	while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
	        {
			echo $buff;
			if((null != strstr($buff,"200 ")) || (null != strstr($buff,"530 ")) || (null != strstr($buff,"501 ")) > 0 || (null != strstr($buff,"500 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")) || (null != strstr($buff,"550 ")))
						break;
		}
		
		echo "\n";
				
		if(null != strstr($buff,"200 "))
		{
			return TRUE;
		}
		else
			return FALSE;

	}

?>
