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

function faq_get_items($groupId, $faqs) {
    $items = array();
	foreach ($faqs as $f) {
        if (isset($f->group) && $f->group == $groupId) {
            array_push($items, $f);
		}
	}
	return $items;
}

function faq_decode($item, $prop, $default=null) {
    if (isset($item->$prop) && !empty($item->$prop)) {
        return base64_decode($item->$prop);
	}
	return $default;
}