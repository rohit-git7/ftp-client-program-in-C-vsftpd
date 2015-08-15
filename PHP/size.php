<?php

	function my_size($sock, $file)
	{
		$size = "SIZE ".$file."\r\n";
		socket_send($sock, $size, strlen($size), 0);
				
	       	while(($bytes = socket_recv($sock, $buff, 1024, 0)) > 0)
	        {
			if((null != strstr($buff,"213 ")) || (null != strstr($buff,"501 ")) > 0 || (null != strstr($buff,"500 ")) || (null != strstr($buff,"502 ")) || (null != strstr($buff,"421 ")) || (null != strstr($buff,"550 ")))
						break;
		}
				
		if(null != strstr($buff,"213 "))
		{
			$file_size = (int)substr($buff, 4, strlen($buff));
			return $file_size;
		}
		else
			return -1;
	}

?>
