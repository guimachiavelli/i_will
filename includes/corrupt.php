<?php

	require_once('Twittersearch.php');
	require_once('con.php');
	
	function remote_filesize($url, $user = "", $pw = "") {
		ob_start();
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	
		if(!empty($user) && !empty($pw)) {
			$headers = array('Authorization: Basic ' .  base64_encode("$user:$pw"));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
	
		$ok = curl_exec($ch);
		curl_close($ch);
		$head = ob_get_contents();
		ob_end_clean();
		
		$regex = '/Content-Length:\s([0-9].+?)\s/';
		$count = preg_match($regex, $head, $matches);
		
		return isset($matches[1]) ? $matches[1] : "unknown";
	}
	


	function imageIsValid($filename,$file_extension) {
	
		// check extension and load the file
		if($file_extension == '.jpg') {
		    $img = @imageCreateFromjpeg($filename);
		} elseif ($file_extension == '.png') {
			$img = @imageCreateFrompng($filename);
		} elseif ($file_extension == '.gif') {
		    $img = @imageCreateFromgif($filename);
		}

		// return 0 if the image is invalid
		if(!$img) return(0);

		// return 1 otherwise
		return(1);
	}
	
	
	function scramble($content, $size, $the_scrambler) {
		$sStart = 10;
		$sEnd = $size-1;
		$nReplacements = rand(6,10);

		for($i = 0; $i < $nReplacements; $i++) {
			$PosA = rand($sStart, $sEnd);

			$content[$PosA] = $the_scrambler;
		}

		return($content);
	}
	
	$search = new TwitterSearch();
	
	$search->contains('/"I will/" -follow');
	
	$results = $search->rpp(100)->results();
	
	
	foreach ($results as $result) {
    	
    	
    	$the_user = $result->from_user;
		$the_tweet = addslashes($result->text);
		$the_avatar = $result->profile_image_url;
		$the_avatar_original= str_replace('_normal', '', $the_avatar);
		$the_time = strtotime($result->created_at);
		
		$length = strlen($the_avatar_original); 
		$characters = 3; 
		$start = $length - $characters; 
		$avatar_type = strtolower(substr($the_avatar_original , $start ,$characters)); 
		//echo $avatar_type;
		
		
		$avatar_extension;
		if($avatar_type == "jpeg" || $avatar_type == "jpg") { $avatar_extension = '.jpg'; }
		elseif ($avatar_type == "png") { $avatar_extension = '.png'; }
		elseif ($avatar_type == "gif") { $avatar_extension = '.gif'; }
		else { $avatar_extension = '.jpg'; }		
		
		
		
		// load the file and get its size
		$content = file_get_contents($the_avatar_original);		
		
		
		// now store the original one
		$fd = fopen("../images/present/" . $the_user . $avatar_extension, "w") or die("The first fopen went wrong, e-mail webmaster Ben.");
		fwrite($fd, $content) or die("The first fwrite went wrong, e-mail webmaster Ben.");
		fclose($fd);
		
		$avatar_local = $_SERVER['DOCUMENT_ROOT'] . "/corrupt/images/present/" . $the_user . $avatar_extension;
		//$size = filesize($avatar_local);
		$size = strlen(file_get_contents($avatar_local));
		
		//echo $avatar_local;
		//echo $size;
		

		// this value tells this code how many (successful) corrupted files should be generated
		// with a maximum of set retries
		$nCorrupts = 1;
		$nRetries = 1000;

	
		// corrupt it a few times
		for($c = 0, $r = 0; $c < $nCorrupts && $r < $nRetries; $r++) {
			// corrupt the file
			$corrupted = scramble($content, $size, $the_tweet);
			
			// save it to disc
			$fd = fopen("../images/future/" . $the_user . $avatar_extension, "w") or die("The fopen went wrong, e-mail webmaster Ben.");
			fwrite($fd, $corrupted, $size) or die("The fwrite went wrong, e-mail webmaster Ben.");
			fclose($fd);

			// count succeeded corrupts
			if(imageIsValid("../images/future/$the_user.$avatar_extension",$avatar_extension)) { $c++; };
			
			
		}
		
		//echo $the_tweet . "<br>";
		 		
		//place things inside the db
		mysql_query("INSERT INTO tweet_info (tweet, user, file_extension, tweet_time) VALUES ('$the_tweet', '$the_user', '$avatar_extension', '$the_time')");		
		
		
	}
	
	//mysql_close($con);

	//jippie! we're done! now, generate a nice json file for ajax purposes
	
	
	
	header('Content-Type: application/json');
	
	
	
	$result_json = mysql_query("SELECT * FROM tweet_info ORDER BY id DESC LIMIT 400 ");
	
	while($row = mysql_fetch_array($result_json)) {
		$the_tweet 	= $row['tweet'];
		$the_user  	= 'images/future/';
		$the_user  .= $row['user'];
		$the_id 	= $row['id'];
	
		$jpeg_files[] = array('file'=> $the_user, 'time'=> $the_id, 'text'=> $the_tweet);
			  
	}
			
	mysql_close($con);
	
	
	function json_format($json) { 
	    $tab = "  "; 
	    $new_json = ""; 
	    $indent_level = 0; 
	    $in_string = false; 
	
	    $json_obj = json_decode($json); 
	
	    if($json_obj === false) 
	        return false; 
	
	    $json = json_encode($json_obj); 
	    $len = strlen($json); 
	
	    for($c = 0; $c < $len; $c++) 
	    { 
	        $char = $json[$c]; 
	        switch($char) 
	        { 
	            case '{': 
	            case '[': 
	                if(!$in_string) 
	                { 
	                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1); 
	                    $indent_level++; 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case '}': 
	            case ']': 
	                if(!$in_string) 
	                { 
	                    $indent_level--; 
	                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char; 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case ',': 
	                if(!$in_string) 
	                { 
	                    $new_json .= ",\n" . str_repeat($tab, $indent_level); 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case ':': 
	                if(!$in_string) 
	                { 
	                    $new_json .= ": "; 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case '"': 
	                if($c > 0 && $json[$c-1] != '\\') 
	                { 
	                    $in_string = !$in_string; 
	                } 
	            default: 
	                $new_json .= $char; 
	                break;                    
	        } 
	    } 
	
	    return $new_json; 
	} 
		
	function sortByDate($a, $b)	{
	    $time1 = filectime($a);
	    $time2 = filectime($b);
	    
	    return $time1 < $time2;
	}
	
	
	
	$response['images'] = $jpeg_files;
	
	$middleman = json_encode($response);
	
	$final = json_format($middleman);
	
	
	$myFile = "../tweets.json";
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $final );
	fclose($fh);
	
	
?>