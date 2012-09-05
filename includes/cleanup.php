<?php
	require_once('con.php');

	function clean_up_dir($dir,$comparison_date) {
	
		$files = array();
		$index = array();
		
		
		if ($handle = opendir($dir)) {
			clearstatcache();
			while (false !== ($file = readdir($handle))) {
		   		if ($file != "." && $file != "..") {
		   			$files[] = $file;
					$index[] = filemtime( $dir.''.$file );
		   		}
			}
		  	closedir($handle);
		}
		
		asort( $index );
		
		foreach($index as $i => $t) {
		
			if($t < $comparison_date) {
				@unlink($dir.''.$files[$i]);
			}
		
		}
		
		
	}
	
	$yesterday = strtotime('yesterday');
	
	//clean up dirs
	clean_up_dir('../images/future/', $yesterday);
	clean_up_dir('../images/present/', $yesterday);
	
	//clean up db
	mysql_query("DELETE FROM tweet_info WHERE tweet_time < $yesterday");
	
?>	