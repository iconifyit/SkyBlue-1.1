<?php  defined('SKYBLUE') or die('Bad file request');

    global $Core;
    global $Filter;
    
    $groups = get_published_links(
        parse_xml(SB_XML_DIR . "links/linksgroups.xml")
    );

    $items = get_published_links(get_published_links(
        parse_xml(SB_XML_DIR . "links/links.xml")
    ));

?>
<?php if (count($groups)) : ?>
	<?php foreach ($groups as $group) : ?>
		<?php $data = get_links($group, $items); ?>
		<?php if (count($data)) : ?>
		    <div class="sidemenu">
				<h3><?php echo $group->name; ?></h3>
				<ul>
					<?php foreach ($data as $obj) : ?>
						<li><a href="<?php echo $obj->url; ?>"<?php get_rel($obj); ?>><?php echo $obj->name; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>