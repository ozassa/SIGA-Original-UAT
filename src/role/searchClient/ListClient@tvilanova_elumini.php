<?php    
  require_once ("../rolePrefix.php");


  $idInform = $field->getField ("idInform");

  if ($comm == "ListContrat"){
    $title ="Alterar n� de contrat";
    $content = '../searchClient/interf/listcontrat.php';
  }else if ($comm == "ViewContrat"){
    $title ="Alterar n� de contrat";
    $content = '../searchClient/viewContrat.php';
  }else if($comm == "ViewExecRegion") {
    $title ="Rela��o Executivo/Regi�o";
    $envia = "1";
    $content = '../searchClient/interf/ViewExecRegion.php';
  }else if($comm=="ExecRegion"){
    $idexecutivo = $field->getField ("idExecutivo");
    $title ="Rela��o Executivo/Region";
    $content = '../searchClient/ExecRegion.php';
  }else if ($comm=="voltar") {
    $title ="Rela��o Executivo/Regi�o";
    $envia = "1";
    $content = '../searchClient/interf/ViewExecRegion.php';
  }else if (!$idInform) {
    $title = "Erro : Informe inv�lido";
    $content = "../searchClient/interf/Error.php";

  } else if ($comm == "view" || $comm == "open") {
    $content = "../searchClient/interf/InformRes.php";
    require ("../searchClient/interf/nameClient.php");

  } else if ($comm == "volVendExt") {
      $title = "Resumo do Volume de Vendas Externas";
      $content = "../searchClient/interf/VolVendExt.php";

  } else if($comm == "generalInformation"){
      $title = "Informa��es Gerais";
      $content = "../searchClient/interf/GeneralInf.php";
  
  } else if ($comm == "segVendExt") {
      $content = "../searchClient/interf/SegVendExt.php";
      $title = "Segmenta��o de Previs�o Vendas Externas";

  } else if ($comm == "prevFinanc") {
      $content = "../searchClient/interf/PrevFinanc.php";
      $title = "Previs�o de Financiamento";

  } else if ($comm == "buyers") {
    $content = "../searchClient/interf/Buyers.php";
    $title = "Principais Compradores";
 
  } else if ($comm == "buySubmit") {
    require ("buySubmit.php");
    if ($forward == "success") {
      $content = "../searchClient/interf/Lost.php";
      $title = "Hist�rico de Perdas";
    } else {
      $content = "../searchClient/interf/Buyers.php";
      $title = "Principais Compradores";
    }
  } else if ($comm == "lost") {
    $content = "../searchClient/interf/Lost.php";
    $title = "Hist�rico de Perdas";
  } else if ($comm == "simul") {
    $title = "Simula��o de Pr�mio";
    $content = "../searchClient/interf/Simulation.php";
 
  } else if ($comm == "import") {
    $title = "Consulta de Importadores";
    $content = "../searchClient/interf/Import.php";
    
  } else if ($comm == "importLimit") {
    $title = "Consulta de Importadores";
    $content = "../searchClient/interf/ImportLimit.php";
   
  } else if ($comm == "insertContact") {
//    require("InsertContact.php"); //SQL
    $title = "Inclus�o de Contato";
    $content = "../searchClient/interf/insertContact.php";

  } else if ($comm == "searchContact") {
//    require("SearchContact.php"); //SQL
    $title = "Busca de Contatos";
    $content = "../searchClient/interf/searchContact.php";

// Alterado por F�bio Campos (elumini) em 17/08/2005
   }
   else if ($comm == "mudaRelacao") {
    $title = "Rela��o Cliente/Executivo";
    $content = "../searchClient/mudaRelacao.php";

  }
  else if ($comm == "gravaObs")
  {
    require("gravaOBs.php");
    $title = "Informa��es Gerais";
    $content = "../searchClient/interf/GeneralInf.php";
  }
  else if ($comm == "areacliente")
  {
    if ($state==1 || $state==2) {
       $title = "�rea do Cliente";
       $content = "../inform/interf/InformRes.php";
    }else if ($state<>1 && $state<>2 && $state<>10){
       $title = "�rea do Cliente";
       $content = "../client/interf/ViewClient.php";
    }

  }
   //Adicionado por Michel Saddock
   require_once ("../../../../site/func/index.php");

?>


