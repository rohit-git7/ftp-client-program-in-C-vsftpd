<?php

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

?>
