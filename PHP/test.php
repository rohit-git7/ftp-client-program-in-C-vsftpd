<?php
	
	$temp = fopen('abc.txt','r');

	while(!feof($temp))
	{
		$line = fgets($temp);
		if(trim($line) != '')
		{
			$arr[] = $line;
		}

	}

	var_dump($arr);
?>
