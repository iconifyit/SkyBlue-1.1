<?php defined('SKYBLUE') or die(basename(__FILE__));

/**
* @version		v1.1 2009-04-12 11:50:00 $
* @package		SkyBlueCanvas
* @copyright	Copyright (C) 2005 - 2009 Scott Edwin Lewis. All rights reserved.
* @license		GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

global $Core;

$faqs = $Core->xmlHandler->ParserMain(SB_XML_DIR.'faqs.xml');
$grps = $Core->xmlHandler->ParserMain(SB_XML_DIR.'faqgroups.xml');

$i=1;

?>
<?php foreach ($grps as $grp) : ?>
    <div class="faqs-group">
	    <h2><?php echo $grp->name; ?></h2>
	    <?php $items = faq_get_items($grp->id, $faqs); ?>
	    <?php $j=1; ?>
	    <?php foreach ($items as $item) : ?>
		    <?php $uid = "{$i}-{$j}"; ?>
	        <h3 class="faqs-question" id="question<?php echo $uid; ?>">
				<a href="#answer<?php echo $uid; ?>"><?php echo stripslashes($item->question); ?></a>
			</h3>
			<div class="faqs-answer" id="answer<?php echo $uid; ?>">
	            <?php echo faq_decode($item, 'answer', ''); ?>
			</div>
			<?php $j++; ?>
		<?php endforeach; ?>
		<?php $i++; ?>
	</div>
<?php endforeach; ?>