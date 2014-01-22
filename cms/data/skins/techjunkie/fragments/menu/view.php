<?php

    global $Router; 
    global $Filter;
    
    $menu = $Core->SelectObj(
        $Core->xmlHandler->ParserMain(SB_XML_DIR . 'menus.xml'),
        1
    );
    
?>
<div class="horizontal-menu">
    <?php if (count($data)) : ?>
    <ul id="mainmenu">
        <?php foreach ($data as $item) : ?>
        <?php if (not_valid_menu($item)) continue; ?>
        <?php if ($item->menu != $menu->id) continue; ?>
        <li<?php the_class($item); ?>>
            <a href="<?php the_link($item); ?>"><span><?php the_text($item); ?></span></a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>