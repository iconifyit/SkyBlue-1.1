<?php

defined('SKYBLUE') or die('Unauthorized file request');

global $Core;
global $Router;
global $Filter;

$pid = $Filter->get($_GET, 'pid', DEFAULT_PAGE);
$aid = $Filter->get($_GET, 'show', null);

$data = get_published($data);

$the_article = null;
if (!empty($aid)) {
    $the_article = $Core->SelectObj($data, $aid);
}

?>
<div class="news-archive">
<?php if (empty($the_article)) : ?>
    <!-- News Excerpts -->
    <?php foreach ($data as $item) : ?>
        <?php $the_link = get_link(array('aid'=>$item->id)); ?>
        <div class="news-intro">
        <h2><a href="<?php echo $the_link; ?>"><?php echo $item->title; ?></a></h2>
        <p class="item-date"><?php echo $item->date; ?></p>
        <p class="item-intro"><?php echo get_intro($item); ?></p>
        <p class="item-link"><a href="<?php echo $the_link; ?>"><span class="link-text">More &#187;</span></a></p>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <!-- News Article -->
    <div class="news-item">
    <h2 class="item-title"><?php echo $the_article->title; ?></h2>
    <?php echo get_story($the_article); ?>
    <p class="item-link"><a href="<?php echo get_link(); ?>">&#171; Back to list</a></p>
    </div>
<?php endif; ?>
</div>