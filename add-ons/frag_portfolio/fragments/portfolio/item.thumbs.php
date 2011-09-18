<?php

	global $Core;
	global $Filter;
	
	$cols = 4;
	$rows = 3;
	$ipp = $cols * $rows;
	
	$pageCount = num_of_pages($data, $ipp);
	
	$page = $Filter->get($_GET, 'page', 1);
	$items = array_slice($data, ($page * $ipp) - $ipp, $ipp);
	
	if (count($items) < $ipp) {
	    $rows = ceil(count($items)/$cols);
	}
	
?>
<div id="gallery_inner">
	<table id="thumbstable" cellpadding="0" cellspacing="0">
		<?php for ($x=0; $x<$rows; $x++) : ?>
		<tr>
			<?php for ($i=($x*$cols); $i<(($x*$cols)+$cols); $i++) : ?>
			<?php if ($i<count($items)) : ?>
			<td>
				<a href="<?php echo get_item_link($data[$i], $page); ?>" class="thumbnail">
				<?php 
					echo make_image(
						$items[$i]->thumbnail,
						$items[$i]->title,
						"", "",
						"dis_thumbnail"
					); 
				?>
				</a>
			</td>
			<?php else : ?>
			<td>
				<?php 
					echo make_image(
						BLANK_IMG,
						sNO_IMAGE,
						"", "",
						"dis_thumbnail"
					); 
				?>
			</td>		
			<?php endif; ?>
			<?php endfor; ?>
		</tr>	
		<?php endfor; ?>
	</table>
	<ul id="thumbSubNav">    
		<?php if ($Filter->get($_GET, 'cid', false)) : ?>
		<?php echo get_category_subnav($data); ?>
		<?php else : ?>
		<li><a style="cursor: pointer;">.01</a></li>
		<?php endif; ?>
		<li id="innercurve"></li>
	</ul>
</div>