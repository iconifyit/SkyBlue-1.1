<?php defined('SKYBLUE') or die('Unauthorized file request');

echo twitter_feed(array(
    'twittername' => 'explainify',
	'limit'       => 3, 
	'title_tag'   => '<h3>%s</h3>',
	'title'       => 'Twitter Feed',
	'prefix'      => '<ul>',
	'suffix'      => '</ul>',
	'tweetprefix' => '<li>',
	'tweetsuffix' => '</li>'
));
?>
<p><a href="http://twitter.com/[[twitter]]">More...</a></p>

