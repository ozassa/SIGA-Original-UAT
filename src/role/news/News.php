<?php 

require_once ("../rolePrefix.php");
  $title = "Notícias";

  if ($comm == "incluir") {
    $content = "../news/interf/incluir.php";
    require_once("../../../home.php");

  } else if ($comm == "noticias") {
    $content = "../news/interf/noticias.php";
    require_once("../../../home.php");

  } else if ($comm == "action") {
    $content = "../news/interf/alterar.php";
    require_once("../../../home.php");

  } else if ($comm == "alterarsql") {
    require_once ("alterarsql.php");
    $content = "../news/interf/alterar.php";
    require_once("../../../home.php");

  } else if ($comm == "excluir") {
    require_once ("excluirsql.php");
    $content = "../news/interf/noticias.php";
    require_once("../../../home.php");
	
  } else if ($comm == "incluirsql") {
    require_once ("incluirsql.php");
    $content = "../news/interf/incluir.php";
    require_once("../../../home.php");
  }

?>









