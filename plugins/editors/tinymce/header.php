<?php 

// Load the header links for Wymeditor 

global $Core;

$path = "plugins/editors/";
$csspath = ACTIVE_SKIN_DIR;

$cssname= basename(ACTIVE_SKIN_DIR);

$styleSheetPath = ACTIVE_SKIN_DIR . "css/{$cssname}.css";

?>
<!--[TinyMCE]-->
<script type="text/javascript" src="<?php echo SB_EDITORS_DIR; ?>tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        // General options
        
        mode : "specific_textareas",
        editor_selector : /(editor|wymeditor|story_content)/,
    
        theme : "advanced",
        plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "fullscreen,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl",
        theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Example content CSS (should be your site CSS)
        
        content_css : "",

        // Drop lists for link/image/media/template dialogs
        
        external_link_list_url     : "plugins/editors/tinymce/link_list.php",
        external_image_list_url    : "plugins/editors/tinymce/image_list.php",
        
        // Not yet implemented
        
        // template_external_list_url : "plugins/editors/tinymce/template_list.php",
        // media_external_list_url    : "plugins/editors/tinymce/media_list.php",

        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    });
</script>
<!--[/TinyMCE]-->