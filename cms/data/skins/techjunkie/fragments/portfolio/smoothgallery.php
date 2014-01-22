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
<div id="myGallery">
    <?php foreach ($items as $item) : ?>
    <div class="imageElement">
        <h3><?php echo $item->title; ?></h3>
        <p><?php echo $item->title; ?></p>
        <a href="#" title="open image" class="open"></a>
        <img src="<?php echo $item->artwork; ?>" class="full" />
        <img src="<?php echo $item->thumbnail; ?>" class="thumbnail" />
    </div>
    <?php endforeach; ?>
</div>