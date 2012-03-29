<?php if (empty($data)) return; ?>

<!--#plugin:fragment(teasers,view)-->
<div class="col3">
	<?php if (isset($data[0])) : ?>
	<h2><?php echo $data[0]->title; ?></h2>
	<p><?php echo $data[0]->intro; ?></p>
	<p><a href="<?php echo $data[0]->link; ?>">Continue</a></p>
	<?php endif; ?>
</div>
<div class="col3-center">
	<?php if (isset($data[1])) : ?>
	<h2><?php echo $data[1]->title; ?></h2>
	<p><?php echo $data[1]->intro; ?></p>
	<p><a href="<?php echo $data[1]->link; ?>">Continue</a></p>
	<?php endif; ?>
</div>
<div class="col3">
	<?php if (isset($data[2])) : ?>
	<h2><?php echo $data[2]->title; ?></h2>
	<p><?php echo $data[2]->intro; ?></p>
	<p><a href="<?php echo $data[2]->link; ?>">Continue</a></p>
	<?php endif; ?>
</div>