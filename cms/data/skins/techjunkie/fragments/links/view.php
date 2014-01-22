<?php  defined('SKYBLUE') or die('Bad file request');

    global $Core;
    global $Filter;
    
    $groups = get_published_links(
        $Core->xmlHandler->ParserMain(SB_XML_DIR . "links/linksgroups.xml")
    );

    $items = get_published_links(get_published_links(
        $Core->xmlHandler->ParserMain(SB_XML_DIR . "links/links.xml")
    ));

?>
<?php foreach ($groups as $group) : ?>
	<?php $data = get_links($group, $items); ?>
	<h2 class="linksgroup"><?php echo $group->name; ?></h2>
	<ul class="links">
		<?php if (count($data)) : ?>
			<?php foreach ($data as $obj) : ?>
				<li><a href="<?php echo $obj->url; ?>"<?php get_rel($obj); ?>><?php echo $obj->name; ?></a></li>
			<?php endforeach; ?>
		<?php else: ?>
			<li>No items to display</li>
		<?php endif; ?>
	</ul>
<?php endforeach; ?>
