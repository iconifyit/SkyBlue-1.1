<?php

defined('SKYBLUE') or die(basename(__FILE__));

function num_of_pages(&$items, $items_per_page) {
	return ceil(count($items) / $items_per_page);
}

function get_offset(&$items, $key, $match) {
    for ($i=0; $i<count($items); $i++) {
		if (isset($items[$i]->$key) && $items[$i]->$key == $match) {
			return $i;
		}
	}
	return 0;
}

function get_next_item($items, $item) {
    $offset = get_offset($items, 'id', $item->id);
    if (isset($items[$offset+1])) {
        return $items[$offset+1];
    }
    else {
        return $item;
    }
}

function get_previous_item($items, $item) {
    $offset = get_offset($items, 'id', $item->id);
    if (isset($items[$offset-1])) {
        return $items[$offset-1];
    }
    else {
        return $item;
    }
}

function build_thumbs_table($items) {

	global $Core;
	global $Filter;
	
	$cols = 4;
	$rows = 3;
	$ipp = $cols * $rows;
	
	$pageCount = num_of_pages($items, $ipp);
	
	$page = $Filter->get($_GET, 'page', 1);
	$page = get_num_in_range($this->page, $pageCount, 1);
	
	$start = ($page * $ipp) - $ipp ;
	
	// Get the cross section from the array
	
	$objs = array_slice($items, $start, $ipp);
	
	$tds   = array();
	foreach ($objs as $obj) {
		$href = $this->ItemSubNavLink($obj->id, $obj->title, $this->page);
		$tds[] = $this->buildThumbsLinks($href, $obj->thumbnail, $obj->title);
	}
	
	if (count($objs) < $ipp && $this->page > 1) {
		for ($i=count($objs); $i<$ipp; $i++) {
			$tds[] = $this->buildTableCell($this->makeImgElement(BLANK_IMG, sNO_IMAGE));
		}
	}

	return $html;
}


function get_buffer($view, $data) {
    ob_start();
	include($view);
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}

function make_image($imgpath, $alt=null, $title=null, $id=null, $class=null) {
	global $Core;
	
	$alt   = $alt != null ? " alt=\"$alt\"" : null ;
	$title = $title != null ? " title=\"$title\"" : null ;
	$id    = $id != null ? " id=\"$id\"" : null ;
	$class = $class != null ? " class=\"$class\"" : null ;

	$w = DEFAULT_WIDTH;
	$h = DEFAULT_HEIGHT;
	if (file_exists($imgpath)) {
		$w = $Core->imageWidth($imgpath);
		$h = $Core->imageHeight($imgpath);
	}
	
	$html  = "<img src=\"$imgpath\" \n";
	$html .= " width=\"$w\"";
	$html .= " height=\"$h\"";
	$html .= " $alt";
	$html .= " $title";
	$html .= " $id";
	$html .= " $class";
	$html .= " />\n";
	return $html;
}

function get_published($items) {
    $published = array();
    foreach ($items as $item) {
        if (!isset($item->published)) {
            array_push($published, $item);
        }
        else if ($item->published) {
            array_push($published, $item);
        }
    }
    return $published;
}

function get_current_items(&$group, &$items) {
    $new_items = array();
    for ($i=0; $i<count($items); $i++) {
        if ($items[$i]->category == $group->id) {
            array_push($new_items, $items[$i]);
        }
    }
    return $new_items;
}

function get_current_group($cid, &$groups) {
    global $Core;
    if (empty($cid) && count($groups) == 0) {
        return null;
    }
    if (empty($cid)) {
        return $groups[0];
    }
    return $Core->SelectObj($groups, $cid);
}

function get_current_item($id, &$items) {
    global $Core;
    if (empty($id) || count($items) == 0) {
        return null;
    }
    return $Core->SelectObj($items, $id);
}

function get_id_format($str) {
    return Router::normalize($str);
}

function get_item_link($item, $page) {
    global $Router;
    global $Filter;
    
    // Escondido-ca-pg-4-c4-p1-52.htm
    
	if (USE_SEF_URLS) {
	    $link = get_id_format($item->title) .
	    "-pg-" . $Filter->get($_GET, 'pid') .
	    "-c" . $item->category .
	    "-p" . $page . 
	    "-"  . $item->id . 
	    ".html";
	}
	else {
	    $query = http_build_query(
	    	array(
	           'pid' => $Filter->get($_GET, 'pid'),
	           'cid' => $item->id
	    	)
	    );
	    $link = "index.php?$query";
	}
	return $link;
}

function get_category_link($item, $page=1) {
    global $Router;
    global $Filter;
	
	// Portraits-pg-4-c17.htm
	
	if (USE_SEF_URLS) {
	    $link = get_id_format($item->title) .
	    "-pg-" . $Filter->get($_GET, 'pid') .
	    "-c" . $item->id . 
	    "-p" . $page . 
	    ".html";
	}
	else {
	    $query = http_build_query(
	    	array(
	           'pid'  => $Filter->get($_GET, 'pid'),
	           'cid'  => $item->id,
	           'page' => $page
	    	)
	    );
	    $link = "index.php?$query";
	}
	return $link;
}

function get_item_subnav($item, $objs) {
	global $Core;
	global $Filter;
	
	$nav = "";
	
	$ipp = 12;
	
	$active = $item->id;
	
	$page = $Filter->get($_GET, 'page');
	
	$start = ($page * $ipp) - $ipp ;
	$end   = $start + $ipp <= count($objs) ? $start + $ipp : count($objs) ;

	$groups = get_published(
	    $Core->xmlHandler->parserMain(CATEGORY_XML)
	);
	$cat = $Core->SelectObj($groups, $item->category);

    // First item

	if ($page > 1) {
		$nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_item_link($objs[0], 1)
	            ),
	            FIRST_ICON
	        )
	    );
	}    

	// Previous item
	
	$offset = get_offset($objs, 'id', $active);
	
	if ($offset > 0 && $offset < count($objs)) {
		$nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_item_link($objs[$offset-1], $page)
	            ),
	            PREV_ICON
	        )
	    );
	}
	
	// Current items
	
	$ticker = 1 + $start;
	for ($i=$start; $i<$end; $i++) {
		$obj = $objs[$i];
		$nav .= $Core->HTML->MakeElement(
	        'li',
	        array(
	            'class' => $obj->id == $active ? 'active' : 'item'
	        ),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_item_link($obj, $page)
	            ),
	            $ticker < 10 ? '.0'.$ticker : '.'.$ticker
	        )
	    );
		$ticker++;
	}
	
	// Next item
	
	if ($offset < (count($objs) - 1)) {
		if ($end <= ($offset + 1) && $page <= ceil(count($objs) / $ipp)) {
	    	$next = $page + 1;
		}
		else {
			$next = $page;
		}
		$nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_item_link($objs[$offset+1], $next)
	            ),
	            NEXT_ICON
	        )
	    );
	}
	
	// Last item
	
	if ($page < ceil(count($objs) / $ipp)) {
		$nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
					'href' => get_item_link(end($objs), ceil(count($objs) / $ipp))
				),
	            LAST_ICON
	        )
	    );
	}
	
	return $nav;
}

function get_category_subnav($items) {
	global $Core;
	global $Filter;
	global $groups;
	
	$nav = "";
	
	$ipp = 12;
	
	$page = $Filter->get($_GET, 'page');

	$numOfPages = num_of_pages($items, $ipp);
	
	$groups = get_published(
	    $Core->xmlHandler->parserMain(CATEGORY_XML)
	);
	$group = get_current_group(
	    $Filter->get($_GET, 'cid'), $groups
	);
	
	// First item
	
	if ($page > 1) {
	    $nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_category_link($group, 1)
	            ),
	            FIRST_ICON
	        )
	    );
	}
	
	// Previous item
	
	if ($page > 1 && $page <= $numOfPages) {
	    $nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_category_link($group)
	            ),
	            PREV_ICON
	        )
	    );
	}
	
	// Current items
	
	for ($i=0; $i<$numOfPages; $i++) {
	    $nav .= $Core->HTML->MakeElement(
	        'li',
	        array(
	            'class' => $Filter->get($_GET, 'page', 1) == $i+1 ? 'active' : 'item'
	        ),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href'  => get_category_link($group, $i+1)
	            ),
	            ($i + 1) < 10 ? '.0'.($i + 1) : '.'.($i + 1)
	        )
	    );
	}
	
	// Next item
	
	if ($page < $numOfPages) {
	    $nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_category_link($group)
	            ),
	            NEXT_ICON
	        )
	    );
	}
	
	// Last item

	if ($page < $numOfPages) {
	    $nav .= $Core->HTML->MakeElement(
	        'li',
	        array(),
	        $Core->HTML->MakeElement(
	            'a',
	            array(
	                'href' => get_category_link($group)
	            ),
	            LAST_ICON
	        )
	    );
	}
	return $nav;
}

?>
