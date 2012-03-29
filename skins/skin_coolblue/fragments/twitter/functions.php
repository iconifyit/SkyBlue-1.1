<?php defined('SKYBLUE') or die('Bad file request'); ?>
<?php
/**
 * This script was adapted from a script by Ryan Davidson.
 * @url http://ryancdavidson.com/blog/2010/04/simple-php-twitter-script/
 */

if (! function_exists('curl_exec') || ! is_callable('curl_exec')) {
    echo "<!--Twitter feed cannot be read because 'is_callable' is not enabled on this server-->";
    return;
}

function get_url_contents($url){
	$crl = curl_init();
	$timeout = 5;
	curl_setopt ($crl, CURLOPT_URL,$url);
	curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	$ret = curl_exec($crl);
	curl_close($crl);
	return $ret;
}

function twitter_feed($options=array()) {

    if (! isset($options['twittername']) || trim($options['twittername']) == "") return;
	
	$default = array(
	    'limit'       => 5,
	    'prefix'      => "<ul>",
	    'suffix'      => "</ul>",
	    'tweetprefix' => "<li>",
	    'tweetsuffix' => "</li>",
	    'title'       => "Twitter Feed",
	    'title_tag'   => "<h3>%s</h3>",
	    'feed_string' => "http://search.twitter.com/search.atom?q=from:".$options['twittername']."&rpp=" . Filter::get($options, 'limit', 5)
	);
	
	$options = array_merge($default, $options);

	function parse_feed($feed, $options) {

		$feed   = str_replace("&lt;", "<", $feed);   		
		$feed   = str_replace("&gt;", ">", $feed);
		$clean  = explode("<content type=\"html\">", $feed);
		$link   = explode("search.twitter.com,2005:", $feed);
		$amount = count($clean) - 1;
		
		$output  = sprintf($options['title_tag'], $options['title']);
		$output .= $options['prefix'];
		for ($i = 1; $i <= $amount; $i++) {   				
		    $cleaner     = explode("</content>", $clean[$i]);
			$linker      = explode("</id>", $link[$i+1]);
			$this_tweets = str_replace("&apos;", "'", $cleaner[0]);
			$this_tweets = str_replace("&gt;", ">", $this_tweets);
			$this_tweets = preg_replace('!<a href.*?>!', '', $this_tweets);
			$this_tweets = preg_replace('!</a>!', '', $this_tweets);
			
			$output .= $options['tweetprefix'];
			$output .= '<a href="http://twitter.com/'.$options['twittername'].'/status/';
			$output .= $linker[0];
			$output .= '" target="_blank">'.$this_tweets.'</a>';
			$output .= $options['tweetsuffix'];
		}
		$output .= $options['suffix'];
		return $output;
	}

	$twitter_cache_file = SB_XML_DIR . 'twittercache.xml';

	$mtime= null;

	if ($mtime== null || (time()-$mtime)>(9*60)) {
		$content = get_url_contents(Filter::get($default, 'feed_string'));
		if (strlen($content) > 0) {
		    FileSystem::write_file($twitter_cache_file, $content);
		}
	}
	else {
		$mtime = filemtime($twitter_cache_file);
	}

    $output = "<!--No Twitter Content Found-->";
	if ($twitterfeed = FileSystem::read_file($twitter_cache_file)) {
	    $output = parse_feed($twitterfeed, $options);  
	}
	return $output;
}