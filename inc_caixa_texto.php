<?php
// Load advanced security system if available
if (file_exists('advanced_security_system.php')) {
    require_once('advanced_security_system.php');
}
?>

<script type="text/javascript" src="../../../../Scripts/tiny_mce.js"></script>

<?php
// TinyMCE configuration with CSP compatibility
$tinymce_config = "
tinyMCE.init({
    // General options
    mode: 'textareas',
    theme: 'advanced',
    plugins: 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups',
    
    // Theme options
    theme_advanced_buttons1: 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontsizeselect',
    theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
    theme_advanced_buttons3: 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
    theme_advanced_buttons4: 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
    
    theme_advanced_toolbar_location: 'top',
    theme_advanced_toolbar_align: 'left',
    theme_advanced_statusbar_location: 'bottom',
    theme_advanced_resizing: true,
    
    // Content CSS
    content_css: 'tinymce/css/word.css',
    
    // External lists
    template_external_list_url: 'tinymce/lists/template_list.js',
    external_link_list_url: 'tinymce/lists/link_list.js',
    external_image_list_url: 'tinymce/lists/image_list.js',
    media_external_list_url: 'tinymce/lists/media_list.js',
    
    // Template values
    template_replace_values: {
        username: 'Some User',
        staffid: '991234'
    },
    
    // CSP compatibility settings
    content_security_policy: false,
    verify_html: false,
    
    // Security settings
    allow_script_urls: true,
    allow_unsafe_link_target: false,
    
    // Performance optimizations
    compress: false,
    remove_script_host: false
});
";

// Use nonce if advanced security system is available
if (isset($advanced_security) && $advanced_security) {
    echo $advanced_security->generateNonceScript($tinymce_config);
} elseif (function_exists('get_security_nonce') && get_security_nonce()) {
    echo '<script type="text/javascript" nonce="' . get_security_nonce() . '">' . $tinymce_config . '</script>';
} else {
    echo '<script type="text/javascript">' . $tinymce_config . '</script>';
}
?>

<!-- /TinyMCE -->

<textarea name="Texto" id="Texto" rows="20" cols="80" class="fontdest"></textarea>