<!DOCTYPE html> 
<html>

<head>

		<meta charset="utf-8">
		
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		
		<title>I Will</title>
		
		<meta name="description" content="A continuous rendering of future representations">
		<meta name="author" content="gui machiavelli, guilherme machiavelli">
		<meta name="keywords" content="glitching, glitch, glitches, databending, twitter, guilherme machiavelli, gui machiavelli" />

		
		<!--  Mobile viewport optimized: j.mp/bplateviewport -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="apple-touch-icon" href="/apple-touch-icon.png">
		 -->
		 
		<!-- CSS : implied media="all" -->
		<link href='http://fonts.googleapis.com/css?family=IM+Fell+French+Canon:400,400italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/styles.css" type="text/css">
		
		<!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
		<script src="js/libs/modernizr-1.6.min.js"></script>
		<script src="js/libs/jquery-1.4.2.min.js"></script>
		

		<script type="text/javascript">
		
			function loadPics() {
	
	
			  $.getJSON('tweets.json', function(data) {
			    
			    var html = '';
				window.nAux = data.images.length;
				
			    var counter = 0;
			    $.each(data.images, function(i, item) {
				    setTimeout(function(){
				      html = '<a href="' + item.file + '.jpg"><img width="43" src="' + item.file + '.jpg" alt="" title="' + item.text + '" /></a> <p>' + item.text +'</p>';
				      counter++;
				      
				      $('#main.render a:first-child').before(html);
				      
				      if ( i == 100 ) return false;
				      
				      if(--window.nAux == 0) //If cycle ended...
		              	setTimeout('loadPics()', 8000); //Start again in 6 seconds
		        	}, 8000*i); //Wait 6 seconds
			    });
			    
			    
			    
			    //$('#main').prepend(html);
				
				//setTimeout("loadPics()",30000);
			  });
			

			};
			
			setTimeout("loadPics()",6000);
			
			$(document).ready(function() {
				$("#nownext").load("index.php");
		     	var refreshId = setInterval(function() {
	            	$("#nownext").load('response.php?randval=10');
	         	}, 2000);
			});
		</script>

</head>

<body>
	<div id="wrap">
		<header>
			<h1><a href="index.php">I Will</a></h1>
			<h2>A continuous rendering of future representations</h2>
		</header>