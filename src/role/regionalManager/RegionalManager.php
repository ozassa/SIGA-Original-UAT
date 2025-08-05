<?php require_once ("../rolePrefix.php");

if($comm == 'prod'){
  $title = "Produção";
  require_once("producao.php");
  $content = "../regionalManager/interf/Producao.php";
}else{
  $title = "Administrador Regional";
  $content = "../regionalManager/interf/ViewParameters.php";
}
require_once("../../../home.php");

?>
