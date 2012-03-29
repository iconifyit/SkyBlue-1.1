<?php

function the_class(&$item) {
    global $Filter;
    if ($Filter->get($_GET, 'pid', DEFAULT_PAGE) == $item->id) {
		echo ' class="active"';
	}
	echo null;
}

function the_link(&$item) {
    global $Router;
    echo $Router->GetLink($item->id);
}

function the_text(&$item) {
    echo $item->name;
}

function not_valid_menu(&$item) {
    return empty($item->menu) || !$item->published;
}

?>