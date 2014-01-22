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
if (strlen($index) > 3) die;
?>
<form id="pasteform" method="get" action="javascript:return void(0);">
  <div>
      <textarea name="text" id="paste_text" rows="12"></textarea>
  </div>
  <div class="inputdivlast">
  <input type="button" 
         class="button" 
         name="save" 
         value="Ok" 
         onclick="SBC.InsertPaste(<?php echo $index; ?>); SBC.hideOverlay(<?php echo $index; ?>);"
         />
  <input type="button" 
         class="button" 
         name="cancel" 
         value="Cancel" 
         onclick="SBC.hideOverlay(<?php echo $index; ?>);"
         />
  </div>
</form>