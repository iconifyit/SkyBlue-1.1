<?php

defined('SKYBLUE') or die('Unauthorized file request');

global $Core;
global $Router;
global $Filter;

usort($data, 'compare_fields');

?>
<div class="content-teasers">
    <!-- News Excerpts -->
    <?php foreach ($data as $item) : ?>
        <?php $the_link = $Router->GetLink($item->link); ?>
        <div class="teaser-entry">
            <h2><a href="<?php echo $the_link; ?>"><?php echo $item->name; ?></a></h2>
            <p><?php echo base64_decode($item->intro); ?></p>
            <p class="read-more"><a href="<?php echo $the_link; ?>">More &#187;</a></p>
        </div>
    <?php endforeach; ?>
</div>

