<?php

defined('SKYBLUE') or die('Unauthorized file request');

if (empty($data)) return;

global $Filter;
global $Router;
global $Core;
global $config;

if (the_action() == "send") {
    handle_contact_form(the_contact($data, $config));
}

?>
<!-- CONTACT FORM -->
<div id="contact_form_div">
    <?php echo the_message(); ?>
    <?php 

    if ($contact_name = $Filter->get($config, 'contact_name', null)) {
        echo "<h3>{$contact_name}</h3>\n";
    }
    if (check_address($config)) {
        echo "<p>\n";
        echo empty($config['contact_address'])   ? null : "{$config['contact_address']}<br />\n" ;
        echo empty($config['contact_address_2']) ? null : "{$config['contact_address_2']}<br />\n" ;
        
        echo empty($config['contact_city'])      ? null : "{$config['contact_city']}" 
            . (empty($config['contact_state'])   ? null : ',&nbsp;');
        echo empty($config['contact_state'])     ? null : "{$config['contact_state']}&nbsp;&nbsp;" ;
        echo empty($config['contact_zip'])       ? null : "{$config['contact_zip']}\n" ;
        echo empty($config['contact_phone'])     ? null : "<br />Phone: {$config['contact_phone']}\n" ;
        echo "</p>\n";
    }
    ?>
	<form action="<?php echo the_form_action(); ?>" method="post" id="contactform">
		<p class="no-border"><strong>Send me a message</strong></p>
		<p>
			<?php if (count($data) > 1) : ?>
				<label for="cid">Recipient:</label><br />
				<select name="cid" tabindex="1">
					<?php foreach ($data as $contact) : ?>
					<option value="<?php echo $contact->id; ?>"><?php echo $contact->name; ?></option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</p>
		<p>
			<label for="subject">Subject</label><br />
			<input id="subject" name="subject" value="" type="text" tabindex="2" />
		</p>
		<p>
			<label for="name">Your Name</label><br />
			<input id="name" name="name" value="" type="text" tabindex="3" />
		</p>
		<p>
			<label for="email">Your Email Address</label><br />
			<input id="email" name="email" value="" type="text" tabindex="4" />
		</p>
		<p>
			<label for="message">Your Message</label><br />
			<textarea id="message" name="message" rows="10" cols="20" tabindex="5"></textarea>
		</p>
		<p class="no-border">
			<input type="submit" name="action" value="Send" class="button" tabindex="6" />
		</p>
	</form>
</div>
<!-- END CONTACT FORM -->