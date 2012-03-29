<?php defined('SKYBLUE') or die("Bad file request"); ?>
<?php

$twitter  = "";
$facebook = "";

if (function_exists('doMyVarsPlugin')) {
	$twitter  = doMyVarsPlugin("[[twitter]]");
	$facebook = doMyVarsPlugin("[[facebook]]");
}
?>
<h3>Connect With Us</h3>
<ul class="subscribe-stuff">
	<li>
		<a title="RSS" href="[[site.url]]rss" rel="nofollow">
		<img alt="RSS" title="RSS" src="[[site.url]]data/skins/coolblue/images/social_rss.png" /></a>
	</li>
	<?php if (! empty($twitter)) : ?>
		<li>
			<a title="Facebook" href="http://facebook.com/[[facebook]]" rel="nofollow">
			<img alt="Facebook" title="Facebook" src="[[site.url]]data/skins/coolblue/images/social_facebook.png" /></a>
		</li>
	<?php endif; ?>
	<?php if (! empty($facebook)) : ?>
		<li>
			<a title="Twitter" href="http://twitter.com/[[twitter]]" rel="nofollow">
			<img alt="Twitter" title="Twitter" src="[[site.url]]data/skins/coolblue/images/social_twitter.png" /></a>
		</li>
	<?php endif; ?>
	<li>
		<a title="E-mail this story to a friend!" href="index.html" rel="nofollow">
		<img alt="E-mail this story to a friend!" title="E-mail this story to a friend!" src="[[site.url]]/data/skins/coolblue/images/social_email.png" /></a>
	</li>
</ul>