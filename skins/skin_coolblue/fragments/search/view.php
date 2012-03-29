<form id="quick-search" action="[[site.url]]search-results.html">
	<fieldset class="search">
	    <input type="hidden" name="cx" value="[[google_searchid]]" />
	    <input type="hidden" name="cof" value="FORID:9" />
	    <input type="hidden" name="ie" value="ISO-8859-1" />
		<label for="qsearch">Search:</label>
		<input class="tbox" id="qsearch" type="text" name="q" value="" />
		<button class="btn" name="sa" type="submit" id="search-submit" value="Submit Search"></button>
	</fieldset>
	<script type="text/javascript">
	    (function($) {
	        var searchString = "Search...";
	        function clear_search(e) {
				if ($("#qsearch").val().trim() != "") {
					$("#qsearch").val("");
				}
			};
			function set_search(e) {
				if ($("#qsearch").val().trim() != searchString) {
					$("#qsearch").val(searchString);
				}
			};
			set_search();
	        $("#qsearch").bind({
	            focus: clear_search,
	            blur:  set_search
	        });
	    })(jQuery);
	</script>
</form>