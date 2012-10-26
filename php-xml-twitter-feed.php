<?php
	// Create the human readable "ago" time for each Tweet
	function humanTiming ($time) {
		
		// Get the current time set to GMT
    	$cur_date = strtotime(gmdate("M d Y H:i:s O"));
		
		// Get the time since Tweet was tweeted
    	$time = $cur_date - $time;
		
		// Set the tokens in seconds to calculate which "ago" string to return
    	$tokens = array (
        	31536000 => 'YEAR',
        	2592000 => 'MONTH',
        	604800 => 'WEEK',
        	86400 => 'DAY',
        	3600 => 'HOUR',
        	60 => 'MINUTE',
        	1 => 'SECOND'
    	);

		// Count the seconds and return the "ago" string
    	foreach ($tokens as $unit => $text) {
        	if ($time < $unit) continue;
        	$numberOfUnits = floor($time / $unit);
        	$returnValue = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'S':'');
        	return $returnValue;
    	}

	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Twitter Feed Example</title>
</head>

<body>
	<?php
		// Set the feed url. Replace YourTwitterFeed with your Twitter Username
		$feed = simplexml_load_file('http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=YourTwitterFeed');
		
		echo "<ul>";
	
		if (isset($feed)) :
			// Loop through each feed item and display each item as text with hyperlinks included.
			foreach ( $feed->channel->item as $tweet ) : 
				// Get the date the Tweet was published
				$pubDate = date(strtotime($tweet->pubDate));
				// Get the text of the Tweet
				$text = htmlspecialchars($tweet->description);
				// Filter each Tweet to make clickable handles and links
				$text = preg_replace('@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@', '<a href="$1" rel="nofollow" target="_blank">$1</a>', $text);
				$text = preg_replace('/@(\w+)/','<a href="http://twitter.com/$1" target="_blank">@$1</a>',$text);
				$text = preg_replace('/\s+#(\w+)/',' <a href="http://search.twitter.com/search?q=%23$1" target="_blank">#$1</a>',$text);
				// Get the human readable "ago" time
				$humantime = humanTiming($pubDate);
				
				// Return each Tweet in a list element
				echo "<li>";
					echo $text;
					echo "<br />";
					echo "<small><i>TWEETED " . $humantime . " AGO</i></small>";
				echo "</li>";
				
			endforeach; 
		else :
			// Handler for an empty list of Tweets
			echo '<li>No items.</li>';
		endif; 
	
		echo "</ul>";
	?>
</body>
</html>