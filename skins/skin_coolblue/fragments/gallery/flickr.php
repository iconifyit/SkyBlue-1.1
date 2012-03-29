<?php if (function_exists('doMyVarsPlugin')) : ?>
<?php if ($flickr = doMyVarsPlugin("[[flicker]]")) : ?>
<!--gallery-->
<div id="gallery" class="clear">
	<h3>Flickr Photos </h3>
	<script type="text/javascript">
		$(function() {
			$('#flickr-gallery').jflickrfeed({
				limit: 10,
				qstrings: {id: "[[flickr]]"},
				itemTemplate: '<li><a href="{{image}}"><img src="{{image_s}}" width="40" height="40" alt="{{title}}" /></a></li>'
			}, function(data) {
				$("#flickr-gallery a").lightBox({
					imageLoading:  '[[site.url]][[skin.path]]js/lightbox/images/lightbox-ico-loading.gif',
					imageBtnPrev:  '[[site.url]][[skin.path]]js/lightbox/images/lightbox-btn-prev.gif',
					imageBtnNext:  '[[site.url]][[skin.path]]js/lightbox/images/lightbox-btn-next.gif',
					imageBtnClose: '[[site.url]][[skin.path]]js/lightbox/images/lightbox-btn-close.gif',
					imageBlank:    '[[site.url]][[skin.path]]js/lightbox/images/lightbox-blank.gif'
				});
			});
		});
	</script>
	<ul id="flickr-gallery" class="thumbs"></ul>
</div>
<!--/gallery-->
<?php endif; ?>
<?php endif; ?>