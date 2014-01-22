<?php defined('SKYBLUE') or die('Bad file request');

global $Core;
global $Router;

/**
 * NOTE: The Fragments plugin will pass the data set to your fragment 
 * in a variable named $data, if there is a data storage file corresponding 
 * to your fragment name. For instance, if your fragment name is 'links', 
 * the Fragments Plugin will look for a file in /skyblue/data/xml/links.xml.
 * If it finds one, it will load the data set as an array of Links objects 
 * and pass them to your fragment in the $data variable.
 */

$groups = articles_get_published(
    $Core->xmlHandler->ParserMain(SB_XML_DIR . 'articlesgroups.xml')
);

$pid = Filter::get($_GET, 'pid', DEFAULT_PAGE);
$cid = Filter::get($_GET, 'cid', null);
$aid = Filter::get($_GET, 'show', null);

$the_group = null;
if (count($groups)) {
    $cid = empty($cid) ? $groups[0]->id : $cid ;
    $the_group = $Core->SelectObj($groups, $cid);
}

if (!empty($cid)) {
    $data = articles_get_current_items($data, $cid);
}

$the_article = null;
if (!empty($aid)) {
    $the_article = $Core->SelectObj($data, $aid);
}

/**
 * NOTE: About Links
 * 
 * SkyBlueCanvas v1.1 RC1 has a new Router class that enables more SEF URLs. However, 
 * at the current time, mutli-level content types - those with items and groups - are not 
 * fully implemented as SEF (search engine friendly) URLs. For this reason, it is necessary 
 * to use the legacy link format in SBC.
 *
 * For more details about the legacy link format, please refer to the in-line comments 
 * in /skyblue/includes/router.php
 */
?>
<!-- Article Categories -->
<?php if (empty($the_article)) : ?>
<h2>Categories</h2>
	<?php if (count($groups)) : ?>
		<ul class="category-list">
		<?php foreach ($groups as $group) : ?>
		<?php $params = array('cid' => $group->id); ?>
			<li><a href="<?php echo articles_get_link($params); ?>"><?php echo $group->name; ?></a></li>
		<?php endforeach; ?>
	<?php else : ?>
	    <p>There are no groups to display</p>
	<?php endif; ?>
</ul>
<?php endif; ?>

<!-- List of articles in current group -->
<?php if (empty($cid)) return; ?>
<?php if (empty($the_article)) : ?>
	<h3>Article List<?php echo (isset($the_group->name) ? " - " . $the_group->name : null ); ?></h3>
	<?php if (count($data)) : ?>
		<ul>
		    <?php foreach ($data as $item) : ?>
			<li>
			    <?php $params = array('cid' => $cid, 'aid' => $item->id); ?>
			    <?php $more_link = articles_get_link($params); ?>
				<h3><a href="<?php echo $more_link; ?>"><?php echo $item->name; ?></a></h3>
				<p><?php echo base64_decode($item->intro); ?></p>
				<p><a href="<?php echo $more_link; ?>">Read More</a></p>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p>There are no articles to display</p>
	<?php endif; ?>
<?php else : ?>
<h3><?php echo $the_article->name; ?></h3>
<?php 
    if (file_exists(SB_STORY_DIR . $the_article->story)) {
		echo FileSystem::read_file(SB_STORY_DIR . $the_article->story); 
	}
?>
<?php $params = array('cid' => $cid); ?>
<?php $back_link = articles_get_link($params); ?>
<p><a href="<?php echo $back_link; ?>">Back To Article List</a></p>
<?php endif; ?>