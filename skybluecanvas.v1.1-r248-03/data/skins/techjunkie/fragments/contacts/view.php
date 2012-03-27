<?php

defined('SKYBLUE') or die('Unauthorized file request');

if (empty($data)) return;

global $Router;
global $Core;
global $config;

if (the_action() == "send") {
    handle_contact_form(the_contact($data, $config));
}
?>
<div id="contact_form_div">
    <?php echo the_message(); ?>
    <?php 
    echo "<h2>{$config['site_name']}</h2>\n";
    if ($contact_name = Filter::get($config, 'contact_name', null)) {
        echo "<h3>{$contact_name}</h3>\n";
    }
    if (check_address($config)) {
        echo "<address>\n";
        echo empty($config['contact_address'])   ? null : "{$config['contact_address']}<br />\n" ;
        echo empty($config['contact_address_2']) ? null : "{$config['contact_address_2']}<br />\n" ;
        
        echo empty($config['contact_city'])      ? null : "{$config['contact_city']}" 
            . (empty($config['contact_state'])   ? null : ',&nbsp;');
        echo empty($config['contact_state'])     ? null : "{$config['contact_state']}&nbsp;&nbsp;" ;
        echo empty($config['contact_zip'])       ? null : "{$config['contact_zip']}\n" ;
        echo empty($config['contact_phone'])     ? null : "<br />Phone: {$config['contact_phone']}\n" ;
        echo "</address>\n";
    }
    ?>
    <form action="<?php echo the_form_action(); ?>" method="post" id="emailForm">
        <fieldset>
            <div class="contact_email_contact">
                <?php if (count($data) > 1) : ?>
                <label class="fieldlabel">To:</label>
                <select name="cid">
                    <?php foreach ($data as $contact) : ?>
                        <option value="<?php echo $contact->id; ?>"><?php echo $contact->name; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <label class="fieldlabel">Name:</label>
                <input type="text" name="name" size="47" class="inputbox" value="" />
                <label class="fieldlabel">Email Address:</label>
                <input type="text" name="email" size="47" class="inputbox" value="" />
                <label class="fieldlabel">Subject:</label>
                <input type="text" name="subject" size="47" class="inputbox" value="" />
                <label class="fieldlabel">Message:</label>
                <textarea cols="44" rows="5" name="message" class="inputbox"></textarea>
                <div class="buttons">
                    <input type="submit" name="action" value="Send" class="button" />
                </div>
            </div>
        </fieldset>
    </form>
</div>