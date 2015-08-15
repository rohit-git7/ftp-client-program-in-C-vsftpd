<?php

	function my_list_client($file)
	{
		$array_files = scandir($file);
		for($i = 0; $i < count($array_files); $i++)
		{
			$file_details = stat($array_files[$i]);

			$perms = fileperms($array_files[$i]);


			if (($perms & 0xC000) == 0xC000) {
			    // Socket
				$info = 's';
			} elseif (($perms & 0xA000) == 0xA000) {
			    // Symbolic Link
			    $info = 'l';
			} elseif (($perms & 0x8000) == 0x8000) {
			    // Regular
			    $info = '-';
			} elseif (($perms & 0x6000) == 0x6000) {
			    // Block special
			    $info = 'b';
			} elseif (($perms & 0x4000) == 0x4000) {
			    // Directory
			    $info = 'd';
			} elseif (($perms & 0x2000) == 0x2000) {
			    // Character special
			    $info = 'c';
			} elseif (($perms & 0x1000) == 0x1000) {
			    // FIFO pipe
			    $info = 'p';
			} else {
			    // Unknown
			    $info = 'u';
			}
	
			// Owner
			$info .= (($perms & 0x0100) ? 'r' : '-');
			$info .= (($perms & 0x0080) ? 'w' : '-');
			$info .= (($perms & 0x0040) ?
			            (($perms & 0x0800) ? 's' : 'x' ) :
			            (($perms & 0x0800) ? 'S' : '-'));
	
			// Group
			$info .= (($perms & 0x0020) ? 'r' : '-');
			$info .= (($perms & 0x0010) ? 'w' : '-');
			$info .= (($perms & 0x0008) ?
			            (($perms & 0x0400) ? 's' : 'x' ) :
			            (($perms & 0x0400) ? 'S' : '-'));

			// World
			$info .= (($perms & 0x0004) ? 'r' : '-');
			$info .= (($perms & 0x0002) ? 'w' : '-');
			$info .= (($perms & 0x0001) ?
			            (($perms & 0x0200) ? 't' : 'x' ) :
			            (($perms & 0x0200) ? 'T' : '-'));

		
			$user_details = posix_getpwuid($file_details[4]);
			$grp_details = posix_getgrgid($file_details[5]);

			echo $info."  ".$file_details[3]."  ".$user_details['name']."  ".$grp_details['name']."\t".filesize($array_files[$i])."\t".date("F d Y H:i:s",filemtime($array_files[$i]))."\t".$array_files[$i]."\n";

		}
	
		echo "\n";	
		
	}

?>
