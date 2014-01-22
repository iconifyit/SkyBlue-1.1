<?php defined('SKYBLUE') or die('Bad file request');
    
    global $Core;
    
    $Core->RegisterEvent('OnRenderPage', 'lightbox_scripts');
    
    $groups = get_portfolio_groups();
    $group  = $Core->GetVar($params, 'category');
    
    $items = $data;
    if (trim($group) != "") {
        $items  = $Core->SelectObjsByKey($data, 'category', $group);
    }
?>
<?php if (count($items)) : ?>
<div id="lightbox">
    <?php foreach ($items as $item) : ?>
    <?php 
        $img   = $Core->GetVar($item, 'artwork', ''); 
        $thumb = $Core->GetVar($item, 'thumbnail', ''); 
        $w = $Core->ImageWidth($thumb);
        $h = $Core->ImageHeight($thumb);
    ?>
    <a href="<?php echo $img; ?>" class="lightbox"><img src="<?php echo $thumb; ?>" width="<?php echo $w; ?>" height="<?php echo $h; ?>" alt="" /></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>