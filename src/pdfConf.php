<?php

//$pdfDir = "f:\\projects\\sbce\\siex\\src\\download\\";
$original_path_t = "C:\\Inetpub\\wwwroot\\siga\\src\\download_test\\";
$original_path = "C:\\Inetpub\\wwwroot\\siga\\src\\download\\";

$original_path = "C:\\Inetpub\\wwwroot\\siga\\src\\download\\";

$pdfDir = file_exists($original_path) 
         ? $original_path
         : dirname(__FILE__)."\\download\\";

$pdfDir_test = file_exists($original_path_t) 
         ? $original_path_t
         : dirname(__FILE__)."\\download_test\\";

//Desenvolvimento
//$pdfDir = "E:\\arquivos\\projetos\\coface\\projetos\\siex\\web\\src\\download\\";

//Homologacao
//$pdfDir = "C:\\HOMOLOGACAO\\Coface\\siex\\src\\download\\";  // Endereço físico dos pdfs




?>
