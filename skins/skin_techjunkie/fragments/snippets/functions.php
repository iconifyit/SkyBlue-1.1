<?php

function compare_fields($item1, $item2) {
    $field1 = $item1->order;
    $field2 = $item2->order;
    
    if ($field1 == $field2) {
        return 0;
    }
    else if ($field1 > $field2) {
        return 1;
    }
    return -1;
}

?>