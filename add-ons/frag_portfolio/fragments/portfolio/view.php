<?php

defined('SKYBLUE') or die(basename(__FILE__));

unset($data);

define('CATEGORY_XML', SB_XML_DIR.'portfolio/category.xml');
define('ITEMS_XML', SB_XML_DIR.'portfolio/portfolio.xml');
define('SETTINGS_XML', SB_XML_DIR.'portfolio/settings.xml');

define('PREV_ICON', '&lt;');
define('FIRST_ICON', '&lt;&lt;');
define('NEXT_ICON', '&gt;');
define('LAST_ICON', '&gt;&gt;');

define('BLANK_IMG', ACTIVE_SKIN_IMG_DIR.'blank.gif');
define('sNO_IMAGE', 'No Image Available');

define('ACTIVE_CLASS', 'active');

define('DEFAULT_WIDTH', 25);
define('DEFAULT_HEIGHT', 25);

global $Core;
$Filter = new Filter;
$FileSystem = new FileSystem;
$Router = new Router;

$groups = get_published(
    $Core->xmlHandler->parserMain(CATEGORY_XML)
);

$items = get_published(
    $Core->xmlHandler->parserMain(ITEMS_XML)
);

$group = get_current_group(
    $Filter->get($_GET, 'cid'), $groups
);

$item = get_current_item(
    $Filter->get($_GET, 'show'),
    $items
);

$data = get_current_items($group, $items);
?>
<ul id="categorynav">
<?php foreach ($groups as $g) : ?>
    <?php $class = ($g->id == $Filter->get($_GET, 'cid', false)) ? ' class="active"' : '' ; ?> 
    <li id="cat-<?php echo get_id_format($g->title); ?>">
    	<a href="<?php echo get_category_link($g); ?>"<?php echo $class; ?>>
        	<span class="linktext"><?php echo $g->title; ?></span>
        </a>
	</li>
<?php endforeach; ?>
</ul>

<?php if (!$Filter->get($_GET, 'cid', false)) : ?>
    <?php shuffle($items); ?>
	<?php echo get_buffer('item.thumbs.php', $items); ?>
	<?php echo '<input id="show_pop" type="hidden" name="show_popup" value="0" />' ?>
<?php elseif ($Filter->get($_GET, 'cid', $group->id) && !$Filter->get($_GET, 'show')) : ?>
	<?php echo get_buffer('item.thumbs.php', $data); ?>
	<?php echo '<input id="show_pop" type="hidden" name="show_popup" value="1" />' ?>
<?php else: ?>
	<?php echo get_buffer('view.item.php', $data); ?>
<?php endif; ?>
