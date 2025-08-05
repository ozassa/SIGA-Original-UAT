<?php
  if(session_id() == '') {
   // Detectar se está em HTTPS para configurar cookies seguros
   $is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
   
   session_set_cookie_params([
    'secure' => $is_https,
    'httponly' => true
]);
session_start();
  }
   
  // Production error handling - log errors but don't display them
  $is_production = true; // Set to production mode
  
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  ini_set('log_errors', 1);
  ini_set('error_log', dirname(__FILE__) . '/logs/php_errors.log');
  
  // Secure error handler for production
  function secure_error_handler($errno, $errstr, $errfile, $errline) {
    $log_message = "Error [$errno]: $errstr in $errfile on line $errline";
    error_log($log_message);
    
    // Don't expose sensitive information in production
    return true;
  }
  
  // Secure exception handler for production
  function secure_exception_handler($exception) {
    $log_message = "Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($log_message);
    error_log($exception->getTraceAsString());
    
    // Show generic error message
    http_response_code(500);
    echo "<div style='color: red; font-weight: bold;'>Sistema temporariamente indisponível. Tente novamente em alguns minutos.</div>";
    exit();
  }
  
  // Set error handlers
  set_error_handler('secure_error_handler');
  set_exception_handler('secure_exception_handler');
   
   //ob_flush;
  
   //$host =  'http://'.$_SERVER['HTTP_HOST'].'/siga/'; // endere?os da localiza??o das p?ginas na Web


   $is_https = "//";
   //$is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
   
   $req =   $_SERVER['REQUEST_URI'];
   if (strpos($req,'coface-siga') !== false) {
    $host =  $is_https.$_SERVER['HTTP_HOST'].'/coface-siga/'; // endere?os da localiza??o das p?ginas na Web
   } else {
    if (strpos($_SERVER['HTTP_HOST'],'siga.coface') !== false) {
      $host =  $is_https.$_SERVER['HTTP_HOST'].'/'; // endere?os da localiza??o das p?ginas na Web
    } else {
      $host =  $is_https.$_SERVER['HTTP_HOST'].'/siga/'; // endere?os da localiza??o das p?ginas na Web
    }
   }


   //$root = "/web/src/"; // endere?o f?sico dos arquivos
   $root = "../../"; // endere?o f?sico dos arquivos
   //$pdfDir = "E:\\projetos\\siex\\src\\download\\";  // Endere?o f?sico dos pdfs
    //Coface
   $original_path = "C:\\Inetpub\\wwwroot\\siga\\src\\download\\";

   $pdfDir = file_exists($original_path) 
             ? $original_path
             : dirname(__FILE__)."\\src\\download\\";

   // file_put_contents($pdfDir."teste_coface.txt", "a123");
   
   
   
   
   $hostFixo  = '';
   $diretorio = "index.php/";
   $dirImage = "images";
   
   $date = date("d/m/Y");
	
   function saudacoes(){
	  $acaso= date("G");
	  if ($acaso < ("12")) {
	       $sd =  "Bom dia, ";
		}elseif ($acaso > ("11") & $acaso < ("19")){
		   $sd =  "Boa tarde, ";
		}elseif ($acaso > ("18")){
		   $sd =  "Boa noite, ";}   
	  return $sd;  
   }
    
   	
   require_once("src/role/rolePrefix.php");
   
   // Incluir funções de segurança (proteção XSS/CSRF)
   require_once(dirname(__FILE__) . "/security_functions.php");
   
   // Include validation framework
   require_once(dirname(__FILE__) . "/InputValidationFramework.php"); 
   
   $userID     =  $_SESSION['userID'];
   $nameUser   =  $_SESSION['nameUser'];
   $login      =  $_SESSION['login'];
   $per        =  $_SESSION['pefil']; 
   
   
   
   // Identificar BRowser
   
    $useragent = $_SERVER['HTTP_USER_AGENT'];
	 
	  if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'IE';
	  } elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Opera';
	  } elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Firefox';
	  } elseif(preg_match('|Chrome/([0-9\.]+)|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Chrome';
	  } elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
		$browser_version=$matched[1];
		$browser = 'Safari';
	  } else {
		// browser not recognized!
		$browser_version = 0;
		$browser= 'other';
	  }
	  //print "browser: $browser $browser_version";
      $_SESSION['browser'] = $browser;
	  
  
  
  function Convert_Data_Geral($data){
    if (strstr($data, "/")){//verifica se tem a barra /
       $d = explode ("/", $data);//tira a barra
       $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = m?s etc...
    return $invert_data;
    }
    elseif(strstr($data, "-")){
       $d = explode ("-", $data);
       $invert_data = "$d[2]/$d[1]/$d[0]";    
       return $invert_data;
    }
      
  } 
	  
  function formata_string($modo, $doc){
    if ($modo == "CPF") {
        if ($doc == "" || $doc == null) return "000.000.000-00";
        $doc = preg_replace("/[^0-9]/", "", $doc);
        $doc = str_pad($doc, 11, 0, STR_PAD_LEFT);
        if (strlen($doc) == 11) return $doc[0] . $doc[1] . $doc[2] . "." . $doc[3] . $doc[4] . $doc[5] . "." . $doc[6] . $doc[7] . $doc[8] . "-" . $doc[9] . $doc[10];
        else  return false;
    }
 
    if ($modo == "CNPJ") {
        if ($doc == "" || $doc == null) return "00.000.000/00000-00";
        $doc = preg_replace("/[^0-9]/", "", $doc);
        $doc = str_pad($doc, 14, 0, STR_PAD_LEFT);
        if (strlen($doc) == 14) return $doc[0] . $doc[1] . "." . $doc[2] . $doc[3] . $doc[4] . "." . $doc[5] . $doc[6] . $doc[7] . '/' . $doc[8] . $doc[9] . $doc[10] . $doc[11] . "-" . $doc[12] . $doc[13];
        else  return false;
    }
 
    
    if ($modo == "IE") {
        if ($doc == "" || $doc == null) return "000.000.000.000";
        $doc = preg_replace("/[^0-9]/", "", $doc);
        $doc = str_pad($doc, 12, 0, STR_PAD_LEFT);
        if (strlen($doc) == 12) return $doc[0] . $doc[1] . $doc[2] . "." . $doc[3] . $doc[4] . $doc[5] . "." . $doc[6] . $doc[7] . $doc[8] . "." . $doc[9] . $doc[10] . $doc[11];
        else  return false;
    }
    
	if ($modo == "TEL") {
        if ($doc == "" || $doc == null) return "(00) 0000-0000";
        $doc = preg_replace("/[^0-9]/", "", $doc);
        $doc = str_pad($doc, 10, 0, STR_PAD_LEFT);
        if (strlen($doc) == 10) return "(" . $doc[0] . $doc[1] . ") " . $doc[2] . $doc[3] . $doc[4] . $doc[5] . "-" . $doc[6] . $doc[7] . $doc[8] . $doc[9];
        else  return false;
    }
	
    if ($modo == "CEP") {
        if ($doc == "" || $doc == null) return "00000-000";
        $doc = preg_replace("/[^0-9]/", "", $doc);
        $doc = str_pad($doc, 8, 0, STR_PAD_LEFT);
        if (strlen($doc) == 8) return $doc[0] . $doc[1] . $doc[2] . $doc[3] . $doc[4] . '-' . $doc[5] . $doc[6] . $doc[7];
        else  return false;
    }
 
   
  }
?>