<?php

define('SKYBLUE', 1);

define('SB_BASE_PATH', str_repeat('../', 1));
define('_SBC_ROOT_', SB_BASE_PATH);
define('BASE_PAGE', 'table.php');
include('../base.php');
$Core = new Core;

if (!$Core->ValidateRequest("upload_image", true)) {
    die ("<h2>Your session has expired. Please log in.</h2>");
}

$index = $Core->GetVar($_GET, 'index', '0');
if (strlen($index) > 3) die;

?>
<form id="tableform" method="get" action="javascript:return void(0);">
  <div class="inputdiv">
      <h3 style="margin-bottom: 4px;">Table Rows:</h3>
      <input type="text" 
             name="tablerows" 
             value="" 
             class="inputfield" 
             id="tablerows"
             />
  </div>
  <div class="inputdiv">
      <h3 style="margin-bottom: 4px;">Table Columns:</h3>
      <input type="text" 
             name="tablecolumns" 
             value="" 
             class="inputfield" 
             id="tablecolumns"
             />
  </div>
  <div class="inputdiv">
      <h3 style="margin-bottom: 4px;">Table Caption:</h3>
      <input type="text" 
             name="tablecaption" 
             value="" 
             class="inputfield" 
             id="tablecaption"
             />
  </div>
  <div class="inputdiv">
      <h3 style="margin-bottom: 4px;">Table CSS Class:</h3>
      <input type="text" 
             name="tableclass" 
             value="" 
             class="inputfield" 
             id="tableclass"
             />
  </div>
  <div class="inputdivlast">
  <input type="button" 
         class="button" 
         name="save" 
         value="Ok" 
         onclick="SBC.InsertTable(<?php echo $index; ?>); SBC.hideOverlay(<?php echo $index; ?>);"
         />
  <input type="button" 
         class="button" 
         name="cancel" 
         value="Cancel" 
         onclick="SBC.hideOverlay(<?php echo $index; ?>);"
         />
  </div>
</form>