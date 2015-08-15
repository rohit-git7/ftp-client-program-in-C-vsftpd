<?php

	function my_connect($ip)
	{
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$conn = socket_connect($sock, $ip, 21);

		if(!$conn)
		{
			echo "Could not connect. Try again!\n\n";
			return FALSE;
		}
	
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

?>
