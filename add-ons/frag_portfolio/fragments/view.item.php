<?php

	global $Core;
	global $Filter;
	
	$item = get_current_item(
		$Filter->get($_GET, 'show'),
		$data
	);
	
	$group = get_current_group(
		$Filter->get($_GET, 'cid'), 
		$groups
	);
	
?>
<div id="display_block">
	<img src="<?php echo $item->artwork; ?>" 
		 width="400" 
		 height="335" 
		 alt="<?php echo $item->title; ?>" 
		 id="displayimg" 
         />
	<ul id="thumbSubNav">
		<li>
			<a href="<?php echo get_category_link($group, 1); ?>">
			<img src="data/media/pages/grid.gif"
				 width="9" 
				 width="7" 
				 alt="back to thumbnails" 
				 />
			</a>
		</li>
		<?php echo get_item_subnav($item, $data); ?>
		<li id="innercurve"></li>
	</ul>
	<div id="dis_photo_info">
		<div id="dis_sample_title">
			<?php echo $item->title; ?>
		</div>
		<div id="dis_client_name">
			<p><span class="gd_sample_label">Client &#187;</span><?php echo $item->client; ?></p>
			<p><span class="gd_sample_label">Agency &#187;</span><?php echo $item->agency; ?></p>
		</div>
	</div>
</div>