<?php defined('SKYBLUE') or die('Bad file request');
    global $Core;
    $Core->RegisterEvent('OnRenderPage', 'slider_scripts');
?>
<?php if ($items = get_portfolio_items($data, $params)) : ?>
<div class="slider-wrap">
	<div class="slider">
		<ul>
		    <?php $i = 1; ?>
			<?php foreach ($items as $item) : ?>
				<?php
					$image = Filter::get($item, 'artwork');
					$title = Filter::get($item, 'title');
					$link  = Filter::get($item, 'link');
					$link  = trim($link) == "" ? "javascript:void(0);" : $link ;
					$theBlurb = $title;
					$theFile = SB_STORY_DIR . Filter::get($item, 'story');
					if (!is_dir($theFile) && file_exists($theFile)) {
					    $theBlurb = FileSystem::read_file($theFile);
					}
				?>
				<li>
				    <a href="<?php echo $link; ?>"><img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" /></a>
				    <div class="slider-blurb" id="slider-blurb-<?php echo $i; ?>"><?php echo $theBlurb; ?></div>
				</li>
		    <?php $i++; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>