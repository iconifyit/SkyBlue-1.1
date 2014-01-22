<?php
	
defined('SKYBLUE') or die('Bad File Request');

global $Router; 
global $Filter;

$forms = $Core->xmlHandler->ParserMain(
	SB_XML_DIR . 'forms.xml'
);

if (!count($forms)) return;

$form_id = $forms[0]->id;
if (is_array($params) && count($params) == 3 && is_numeric($params[2])) {
    $form_id = $params[2];
}

$form = $Core->SelectObj($forms, $form_id);

$fields = $Core->xmlHandler->ParserMain(
	SB_XML_DIR . 'fields.xml'
);

$fields = formbuilder_get_fields($form->id, $fields);

$Request = new RequestObject;

$form_submitted = false;
$errors = array();

if (formbuilder_action($Request) == "submit") {
    $errors = formbuilder_validate($fields, $Request);
    if (count($errors) == 0) {
    	$form_submitted = handle_formbuilder_action($form, $fields, $Request);
    	$Request = new SkyBlueObject;
    }
}

$formAction = $Router->getLink(Filter::get($_GET, 'pid'));

?>
<?php if (count($errors)) : ?>
	<h2>Please correct the following errors:</h2>
	<ul class="formbuilder-errorlist">
		<?php for ($i=0; $i<count($errors); $i++) : ?>
		<?php $error = $errors[$i]; ?>
			<li><?php echo Filter::get($error, 'errStr'); ?></li>
		<?php endfor; ?>
	</ul>
<?php elseif ($form_submitted) : ?>
	<h2>Thank you for your response.</h2>
<?php endif; ?>

<?php if (Filter::get($form, 'title')) : ?>
	<h2><?php echo Filter::get($form, 'title'); ?></h2>
<?php endif; ?>

<?php if (Filter::get($form, 'blurb')) : ?>
	<p><?php formbuilder_blurb($form); ?></p>
<?php endif; ?>

<form action="<?php echo $formAction; ?>" 
      method="post" <?php if (formbuilder_has_file_field($fields)) : ?>enctype="multipart/form-data"<?php endif; ?>
      id="<?php echo Filter::get($form, 'cssid', 'form_id_' . $form->id); ?>">

	<?php foreach ($fields as $field) : ?>
	<fieldset>
		<?php formbuilder_label($field, $errors); ?>
		<?php formbuilder_field($field, $Request); ?>
	</fieldset>
	<?php endforeach; ?>

	<fieldset>
		<input type="submit" name="action" value="Submit" class="button" />
	</fieldset>
</form>