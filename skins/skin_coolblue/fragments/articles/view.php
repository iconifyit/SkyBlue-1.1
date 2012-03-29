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

if (! empty($cid)) {
    $data = articles_get_current_items($data, $cid);
}

$the_article = null;
if (! empty($aid)) {
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
	<div class="post">
		<div class="right">
			<h2>Categories</h2>
			<ul class="category-list">
				<?php if (count($groups)) : ?>
					<?php foreach ($groups as $group) : ?>
					<?php $params = array('cid' => $group->id); ?>
						<li><a href="<?php echo articles_get_link($params); ?>"><?php echo $group->name; ?></a></li>
					<?php endforeach; ?>
				<?php else : ?>
					<li>There are no groups to display</li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="left"></div>
	</div>
<?php endif; ?>

<!--
<div class="post">
	<div class="right">
		<h2><a href="index.html">Lorem Ipsum Dolor Sit Amet</a></h2>
		<p class="post-info">Filed under <a href="index.html">templates</a>, <a href="index.html">internet</a></p>
		<p>text, text, text</p>
		<p><a class="more" href="index.html">continue reading &raquo;</a></p>
	</div>
	<div class="left">
		<p class="dateinfo">JAN<span>25</span></p>
		<div class="post-meta">
			<h4>Post Info</h4>
			<ul>
				<li class="user"><a href="#">Erwin</a></li>
				<li class="time"><a href="#">11:30 AM</a></li>
				<li class="comment"><a href="#">7 comments</a></li>
				<li class="permalink"><a href="#">Permalink</a></li>
			</ul>
		</div>
	</div>
</div>
-->

<!-- List of articles in current group -->
<?php if (empty($cid) || ! count($data)) : ?>
    <div class="post">
		<div class="right">
			<h2>Oops!</h2>
			<p>No items to display</p>
		</div>
		<div class="left"></div>
	</div>
<?php endif; ?>
<?php if (empty($the_article)) : ?>
	<?php $article_count = count($data); ?>
	<?php if ($article_count) : ?>
		<?php foreach ($data as $item) : ?>
		<?php
	
			$article_count = count($data);
			$params        = array('cid' => $cid, 'aid' => $item->id);
			$more_link     = articles_get_link($params);
			
			$the_author = Filter::get($item, 'author');
			$the_year   = "";
			$the_month  = "";
			$the_day    = "";
			$the_time   = "";
			
			$item_date = Filter::get($item, 'date');
			if (! empty($item_date)) {
			    $time      = strtotime($item_date);
			    $the_year  = date("Y", $time);
			    $the_day   = date("d", $time);
			    $the_month = date("M", $time);
			    $the_time  = date("H:i A", $time);
			}
		
		?>
		<div class="post">
			<div class="right">
				<h2><a href="index.html">Lorem Ipsum Dolor Sit Amet</a></h2>
				<p><?php echo base64_decode($item->intro); ?></p>
				<p><a class="more" href="<?php echo $more_link; ?>">continue reading &raquo;</a></p>
			</div>
			<div class="left">
				<p class="dateinfo"><?php echo $the_year; ?><span><?php echo $the_month; ?></span><span><?php echo $the_day; ?></span></p>
				<div class="post-meta">
					<h4>Post Info</h4>
					<ul>
						<li class="user"><?php echo ucwords($the_author); ?></li>
						<?php if (! empty($the_time)) : ?>
						    <li class="time"><?php echo $the_time; ?></li>
					    <?php endif; ?>
						<li class="permalink"><a href="<?php echo $more_link; ?>">Permalink</a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>

<?php else : ?>
	<div class="post">
		<div class="right">
			<h2><?php echo Filter::get($the_article, 'name'); ?></h2>
			<p class="post-info">Filed under <a href="index.html"><?php echo Filter::get($the_group, 'name'); ?></a></p>
			<?php 
				if (file_exists(SB_STORY_DIR . $the_article->story)) {
					echo FileSystem::read_file(SB_STORY_DIR . $the_article->story); 
				}
			?>
			<?php $params = array('cid' => $cid); ?>
			<?php $back_link = articles_get_link($params); ?>
			<p><a class="more" href="<?php echo $back_link; ?>">Back To Article List</a></p>
		</div>
		<?php
	
			$params     = array('cid' => $cid, 'aid' => $the_article->id);
			$permalink  = articles_get_link($params);
			$the_author = Filter::get($the_article, 'author');
			$the_year   = "";
			$the_month  = "";
			$the_day    = "";
			$the_time   = "";
			
			$item_date = Filter::get($the_article, 'date');
			if (! empty($item_date)) {
			    $time      = strtotime($item_date);
			    $the_year  = date("Y", $time);
			    $the_day   = date("d", $time);
			    $the_month = date("M", $time);
			    $the_time  = date("H:i A", $time);
			}
		
		?>
		<div class="left">
			<p class="dateinfo"><?php echo $the_year; ?><span><?php echo $the_month; ?></span><span><?php echo $the_day; ?></span></p>
			<div class="post-meta">
				<h4>Post Info</h4>
				<ul>
					<li class="user"><?php echo ucwords($the_author); ?></li>
					<?php if (! empty($the_time)) : ?>
						<li class="time"><?php echo $the_time; ?></li>
					<?php endif; ?>
					<li class="permalink"><a href="<?php echo $permalink; ?>">Permalink</a></li>
				</ul>
			</div>
		</div>
	</div>
<?php endif; ?>