<?php
    $groups = $Core->xmlHandler->ParserMain(
        $Core->path . SB_XML_DIR . 'portfolio/category.xml'
    );
    $group = $Core->GetVar($_GET, 'cid', $groups[0]->id);
    $items = $Core->SelectObjsByKey($data, 'category', $groups[0]->id);
?>
<div id="thickbox">
    <?php foreach ($groups as $group) : ?>
    <h2><?php echo $group->title; ?></h2>
    <?php $gallery = strtolower($Core->SafeURLFormat($group->title)); ?>
    <?php $items = $Core->SelectObjsByKey($data, 'category', $group->id); ?>
        <?php foreach ($items as $item) : ?>
			<?php 
				$img = $item->artwork; 
				$thumb = $item->thumbnail;
				$w = $Core->ImageWidth($thumb);
				$h = $Core->ImageHeight($thumb);
			?>
            <a href="<?php echo $img; ?>" class="thickbox" 
                rel="gallery-<?php echo $gallery; ?>"><img src="<?php echo $thumb; ?>" 
			        width="<?php echo $w; ?>" 
			        height="<?php echo $h; ?>" 
			        alt="<?php echo $item->title; ?>" 
			        /></a>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<!--
<a href="images/plant1.jpg" title="add a caption to title attribute / or leave blank" class="thickbox" rel="gallery-plants"><img src="images/plant1_t.jpg" alt="Plant 1" /></a> 
<a href="images/plant2.jpg" title="add a caption to title attribute / or leave blank" class="thickbox" rel="gallery-plants"><img src="images/plant2_t.jpg" alt="Plant 2" /></a> 
<a href="images/plant3.jpg" title="add a caption to title attribute / or leave blank" class="thickbox" rel="gallery-plants"><img src="images/plant3_t.jpg" alt="Plant 3" /></a> 
<a href="images/plant4.jpg" title="add a caption to title attribute / or leave blank" class="thickbox" rel="gallery-plants"><img src="images/plant4_t.jpg" alt="Plant 4" /></a>
-->