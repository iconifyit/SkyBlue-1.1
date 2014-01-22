<?php
    $groups = $Core->xmlHandler->ParserMain(
        $Core->path . SB_XML_DIR . 'portfolio/category.xml'
    );
    $group = $Core->GetVar($_GET, 'cid', $groups[0]->id);
    $items = $Core->SelectObjsByKey($data, 'category', $group);
?>
<div id="lightbox">
    <?php foreach ($data as $item) : ?>
    <?php 
        $img = $item->artwork; 
        $thumb = $item->thumbnail;
        $w = $Core->ImageWidth($thumb);
        $h = $Core->ImageHeight($thumb);
    ?>
    <a href="<?php echo $img; ?>" class="lightbox"><img src="<?php echo $thumb; ?>" width="<?php echo $w; ?>" height="<?php echo $h; ?>" alt="" /></a>
    <?php endforeach; ?>
</div>