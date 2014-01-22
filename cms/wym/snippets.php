<?php

define('SKYBLUE', 1);

define('SB_BASE_PATH', str_repeat('../', 1));
define('_SBC_ROOT_', SB_BASE_PATH);
define('BASE_PAGE', 'snippets.php');
include('../base.php');

$Core = new Core;

if (!$Core->ValidateRequest("upload_image", true)) {
     die ("<h2>Your session has expired. Please log in.</h2>");
}

$index = $Core->GetVar($_GET, 'index', '0');

$snippets = $Core->xmlHandler->ParserMain(SB_XML_DIR . "snippets.xml");

?>
<?php if (empty($snippets)) : ?>
<div>
  <p>
      It appears you do not have any Snippets created. To use this tool please create at least 
      one Snippet by going to <strong>Main Dashboard &gt; Collections &gt; Snippets</strong>.
  </p>
</div>
<?php else: ?>
    <script type="text/javascript">
        SBC.InsertSnippet = function(index) {
            var wym = window.WYMeditor.INSTANCES[index];
            var doc = window.document;
            var selected = wym.selected();
            var dialogType = jQuery(wym._options.dialogTypeSelector).val();
            var sStamp = wym.uniqueStamp();
            var snippet = SBC.val('snippets');
            
            if (snippet != "" && typeof(snippet) != "undefined") {
                if (selected && selected.tagName.toLowerCase() != WYMeditor.BODY) {
                    jQuery(selected).after(snippet);
                } 
                else {
                    jQuery(wym._doc.body).append(snippet);
                }
            }
        };
        function snippets_submit() {
            SBC.InsertSnippet(<?php echo $index; ?>); 
            SBC.hideOverlay(<?php echo $index; ?>);
        };
    </script>
    <form id="urlform" method="get" action="javascript:return void(0);">
      <p>This tool inserts a token to place a code Snippet on a page. 
         When the page is rendered, the token will be replaced with the Snippet code (text or HTML). 
         To create or edit Snippets, go to 
         <strong>Main&nbsp;Dashboard&nbsp;&gt;&nbsp;Collections&nbsp;&gt;&nbsp;Snippets</strong>.
      </p>
      <div class="inputdiv">
          <h3 style="margin-bottom: 4px;">Select a Snippet:</h3>
          <select name="snippets" id="snippets">
              <option value=""> -- Snippets -- </option>
              <?php for ($i=0; $i<count($snippets); $i++) : ?>
                  <?php $snippet = $snippets[$i]; ?>
                  <?php $snippetName = isset($snippet->name) ? $snippet->name : 'unknown' ; ?>
                  <option value="{snippet(<?php echo $snippetName; ?>)}"><?php echo $snippetName; ?></option>
              <?php endfor; ?>
          </select>
      </div>
      <div class="inputdivlast">
      <input type="button" 
             class="button" 
             name="save" 
             value="Ok" 
             onclick="snippets_submit();"
             />
      <input type="button" 
             class="button" 
             name="cancel" 
             value="Cancel" 
             onclick="SBC.hideOverlay(<?php echo $index; ?>);"
             />
      </div>
    </form>
<?php endif; ?>
