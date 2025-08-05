<?php
  require_once("../rolePrefix.php");

//monta a tela da notificação que envolve a mudanca de credito.
  if ($comm == "alterationCredit") {
//    require ("alterationCredit.php"); motor de alteração
      $title = "Alteração de Crédito";
      $content = "../credit/interf/AlterationCredit.php";

//monta tela codigo COFACE dos principais compradores
  } else if ($comm == "analiseBuyers") {
//    require ("analiseBuyers.php"); motor de alteração
      $title = $nameCl." Principais Compradores";
//$nameCL variael para armazenar o nome do cliente
// criar função para o plural de principal comprador
      $content = "../credit/interf/AnaliseBuyers.php";

//CLIENTES NÃO CONFIRMADOS
  } else if ($comm == "clientNotCofirmed") {
//    require ("ClientNotCofirmed.php"); motor de busca
      $content = "../credit/interf/ClientNotCofirmed.php";
      $title = "Clientes Não Confirmados";
  

  //credito concedido 
  } else if ($comm == "creditAccord") {
//    require ("CreditAccord.php"); motor de alteração
      $content = "../credit/interf/CreditAccord.php";
      $title = "Crédito Concedido";

  } else {
      $content = "";
      $title = "<H1>ERRO</h1>";
  }
  require_once("../functionary/interf/Main.php");
?>

