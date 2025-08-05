<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php

// Load the advanced security system
require_once('advanced_security_system.php');

// Initialize advanced security system
// Start in Report-Only mode for testing - change to false for enforcement
$REPORT_ONLY_MODE = true;

// Check browser compatibility and apply appropriate security
if (browser_supports_csp()) {
    // Modern browsers - use advanced CSP system
    $advanced_security = init_advanced_security($REPORT_ONLY_MODE);
} else {
    // Legacy browsers - use basic security headers
    apply_legacy_security_fallback();
    $advanced_security = null;
}


 if(!isset($_SESSION)){
 	session_set_cookie_params([
    'secure' => true,
    'httponly' => true
]);
session_set_cookie_params([
    'secure' => true,
    'httponly' => true
]);
session_start();
 }

	//require_once ("versao_sistema.php");
 
?>
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<!--<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">-->


<title>SIGA - Sistema de Integrado de Gestão de Apólice - COFACE</title>
<link href="<?php echo $host;?>css/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $host;?>css/geral.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $host;?>css/relatorio.css" rel="stylesheet" type="text/css" />


<script type='text/javascript' src='<?php echo $host;?>Scripts/jquery.js'></script>


<script type='text/javascript' src='<?php echo $host;?>Scripts/validation.js'></script>

<?php
// Google Analytics and main menu functions with nonce support
$ga_script = "
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-379068-22']);
_gaq.push(['_trackPageview']);

(function() {
    var ga = document.createElement('script'); 
    ga.type = 'text/javascript'; 
    ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; 
    s.parentNode.insertBefore(ga, s);
})();

// Character cleanup function
function myscript(){ 
    // Character replacement if needed
} 

// Main menu functionality
function mainmenu(){
    $('#nav ul').css({display: 'none'});
    $('#nav li').hover(function(){
        $(this).find('ul:first').css({visibility: 'visible', display: 'none'}).show(200);
    },function(){
        $(this).find('ul:first').css({visibility: 'hidden'});
    });
}

$(document).ready(function(){
    mainmenu();
});
";

// Use nonce if advanced security is available, otherwise regular script
if (isset($advanced_security) && $advanced_security) {
    echo $advanced_security->generateNonceScript($ga_script);
} else {
    echo '<script>' . $ga_script . '</script>';
}
?>

<script type="text/javascript" src="<?php echo $host;?>Scripts/tinybox.js"></script>
<script type="text/javascript" src="<?php echo $host;?>Scripts/jquery.lightbox-0.3.js"></script>

<!-- Inicio Table sorter-->
<style type="text/css">
			/*@import "<?php echo $host;?>src/css/demo_page.css";*/
			/*@import "<?php echo $host;?>src/css/demo_table.css";*/
</style>
<!--<script type="text/javascript" language="javascript" src="<?php echo $host;?>src/scripts/tablesort/jquery.js"></script>-->
<script type="text/javascript" language="javascript" src="<?php echo $host;?>src/scripts/tablesort/jquery.dataTables.js"></script>	
		
<?php
// DataTables initialization script with nonce support
$datatable_script = "
$(document).ready(function() {
    if($('#example').not('.no-sort').length > 0){
        $('#example').not('.no-sort').dataTable( {
            'sPaginationType': 'full_numbers',
        });
    }
    if($('#example.no-sort').length > 0){
        $('#example.no-sort').dataTable( {
            'sPaginationType': 'full_numbers',
            'aaSorting': [[0, 'asc']],
        });
    }
});
";

// Use nonce if advanced security is available
if (isset($advanced_security) && $advanced_security) {
    echo $advanced_security->generateNonceScript($datatable_script);
} else {
    echo '<script type="text/javascript" charset="utf-8">' . $datatable_script . '</script>';
}
?>
<!-- Fim Table sorter-->
</head>
<body class="fundocor">
<?php
// Initialize page scripts with nonce support
$init_script = "myscript();";
if (isset($advanced_security) && $advanced_security) {
    echo $advanced_security->generateNonceScript($init_script);
} else {
    echo '<script>' . $init_script . '</script>';
}
?>

<div id="tudogeral">
  <div id="cabecalho">
    <div id="cabecalhoconteudo">
      <div id="cabecalhomarca"><a href="../access/Access.php"><img src="<?php echo $host;?>images/cabecalho_marca.png" alt="" /></a></div>
      <div id="cabecalhonome"><a href="../access/Access.php"><img src="<?php echo $host;?>images/cabecalho_gprint.png" alt="" /></a></div>
      <div id="cabecalhodados">
        <div id="cabecalhodadoslinha1">
          <div id="cabecalhodadoslinha1texto"> <span class="textobranco14"><strong><?php echo saudacoes();?>
				 <?php 
				  //$nomeUsuario = odbc_result(odbc_exec($db,'select name from Users where id = '. $_SESSION['userID']),'name');
				  // if($_SESSION['nameUser'] != ""){

				    $stmt = odbc_prepare($db, 'SELECT name FROM Users WHERE id = ?');
					odbc_execute($stmt, [$_SESSION['userID']]);
					echo odbc_result($stmt, 'name');
				 //}
				 ?></strong></span> <span class="textobranco10"><br />
            Hoje &eacute; <?php echo $date;?> - &Uacute;ltimo acesso: 
			<?php echo isset($_SESSION['DataAcesso']) ? $_SESSION['DataAcesso'] : ''; ?></span> </div>
          <div id="cabecalhodadoslinha1botao">
             <button class="botaovpp" type="button" id="exitButton">Sair</button>
             <?php
             // Exit button script with nonce support
             $exit_script = "document.getElementById('exitButton').onclick = function() { window.location = '../access/Access.php?comm=exit'; };";
             if (isset($advanced_security) && $advanced_security) {
                 echo $advanced_security->generateNonceScript($exit_script);
             } else {
                 echo '<script>' . $exit_script . '</script>';
             }
             ?>
             </div>
        </div>
        <div id="cabecalhodadoslinha2">
          <!-- <select name="AcessoRapido" id="AcessoRapido" style="width:386px;">
            <option>Acesso r&aacute;pido...</option>
            
          </select>
          -->
        </div>
      </div>
    </div>
  </div>
  
  
  
 
<!-- MENU - IN?CIO -->
<?php require_once("menu.php");



?>
<!-- MENU - FIM -->  
<?php
// Final initialization script with nonce support
$final_init_script = "
$(document).ready(function() {
    // Character cleanup functionality if needed
    // document.body.innerHTML = document.body.innerHTML.replace(/?/g, '');
});
";

if (isset($advanced_security) && $advanced_security) {
    echo $advanced_security->generateNonceScript($final_init_script);
} else {
    echo '<script>' . $final_init_script . '</script>';
}
?> 
