<?php
  require_once("../rolePrefix.php");

//monta a tela da notifica��o que envolve a mudanca de credito.
  if ($comm == "alterationCredit") {
//    require ("alterationCredit.php"); motor de altera��o
      $title = "Altera��o de Cr�dito";
      $content = "../credit/interf/AlterationCredit.php";

//monta tela codigo COFACE dos principais compradores
  } else if ($comm == "analiseBuyers") {
//    require ("analiseBuyers.php"); motor de altera��o
      $title = $nameCl." Principais Compradores";
//$nameCL variael para armazenar o nome do cliente
// criar fun��o para o plural de principal comprador
      $content = "../credit/interf/AnaliseBuyers.php";

//CLIENTES N�O CONFIRMADOS
  } else if ($comm == "clientNotCofirmed") {
//    require ("ClientNotCofirmed.php"); motor de busca
      $content = "../credit/interf/ClientNotCofirmed.php";
      $title = "Clientes N�o Confirmados";
  

  //credito concedido 
  } else if ($comm == "creditAccord") {
//    require ("CreditAccord.php"); motor de altera��o
      $content = "../credit/interf/CreditAccord.php";
      $title = "Cr�dito Concedido";

  } else {
      $content = "";
      $title = "<H1>ERRO</h1>";
  }
  require_once("../functionary/interf/Main.php");
?>

