<?php 
	include('includes/header.php');
	require_once('includes/con.php');
	
	//$result = mysql_query("SELECT * FROM tweet_info ORDER BY id DESC LIMIT 200 ");
	$result = mysql_query("SELECT * FROM tweet_info ORDER BY id DESC LIMIT 30");
	
?>
	
	
	
	<section id="main" class="render">

		<?php
			$counter = 0;
			
			while($row = mysql_fetch_array($result)) {
				
				/*
				$counter++;
				if ($counter < 100) {
					continue; 
				} else {
				*/
					$the_tweet = $row['tweet'];
					$the_user  = 'images/future/';
					$the_user  .= $row['user'];
					$the_user  .= $row['file_extension'];
					
					
					echo("<a href=\"$the_user\"><img src=\"$the_user\" title=\"$the_tweet\" alt=\"\" width=\"43\"></a>");
					echo("<p>$the_tweet</p>");
				//}
			}
			
			mysql_close($con);
		?>
		
		
		
	</section>
	
	
	<?php include('includes/footer.php'); ?>
	
