<?php
	
session_start();
$userID = $_SESSION['userID'];  
//extract($_REQUEST);
//error_log("Conteúdo de listclient:");
//error_log(print_r($_REQUEST, true));

$comm = isset($_REQUEST['comm']) ? trim($_REQUEST['comm']) : null;
$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

 if (!preg_match('/^[a-zA-Z0-9_&=]+$/', $comm)) {
       
       // die('Input inválido!');
    }

$idInform = isset($_REQUEST['idInform']) ? trim($_REQUEST['idInform']) : null;
$idInform = htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');

//error_log($comm);
//error_log($idInform);
	 
  require_once ("../rolePrefix.php"); 
  
  
 /* if(count($_POST) > 2){
		// armazena dados do log
		$tem = $notif->historicolog((isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false), $_SESSION['userID'], $db,1,'','Inform','');  	
		$tem1 = $notif->historicolog((isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false), $_SESSION['userID'], $db,1,'','Importer',(isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : false));
  }*/
  if ($comm == "ListContrat"){
    $title ="Alterar nº de contrat";
    $content = '../searchClient/interf/listcontrat.php';
  }else if ($comm == "ViewContrat"){
    $title ="Alterar nº de contrat";
    $content = '../searchClient/viewContrat.php';
  }else if($comm == "ViewExecRegion") {
    $title ="Relação Executivo/Região";
    $envia = "1";
    $content = '../searchClient/interf/ViewExecRegion.php';
  }else if($comm=="ExecRegion"){
    $idexecutivo = $field->getField ("idExecutivo");
    $title ="Relação Executivo/Region";
    $content = '../searchClient/ExecRegion.php';
  }else if ($comm=="voltar") {
    $title ="Relação Executivo/Região";
    $envia = "1";
    $content = '../searchClient/interf/ViewExecRegion.php';
  }else if (!$idInform) {
    $title = "Erro : Informe inválido";
    $content = "../searchClient/interf/Error.php";
	
  }else if($comm == "cadastro_endosso"){
	  $title  = "Cadastro de Endosso";
	  $content = "../searchClient/interf/cadastro_endosso.php";
  }else if($comm == "cadastro_endosso_detalhe"){
	  $title  = "Parcelas do Endosso";
	  $content = "../searchClient/interf/cadastro_endosso_detalhe.php";
  }else if ($comm == "view" || $comm == "open") {
	//print 'oi';  
    $content = "../searchClient/interf/InformRes.php";
	
    require_once ("../searchClient/interf/nameClient.php");

  } else if ($comm == "volVendExt") {
      $title = " Distribuição de Vendas por Tipo de Pagamento";
      $content = "../searchClient/interf/VolVendExt.php";

  }  else if ($comm == "distFaixaLimite") {
      $title = " Distribui&ccedil;&atilde;o de Vendas a Prazo por Faixa de Limite de Cr&eacute;dito";
      $content = "../searchClient/interf/distFaixaLimite.php";

  }   else if ($comm == "divVenc") {
      $title = " D&iacute;vidas Vencidas ";
      $content = "../searchClient/interf/divVenc.php";

  } else if ($comm == "distTipoVendas") {
      $title = " Distribui&ccedil;&atilde;o de Vendas por Tipo de Pagamento e Canal";
      $content = "../searchClient/interf/distTipoVendas.php";

  } else if($comm == "generalInformation"){
      $title = "Informações Gerais";
      $content = "../searchClient/interf/GeneralInf.php";
  
  } else if ($comm == "segVendExt") {
      $content = "../searchClient/interf/SegVendExt.php";
      $title = "Distribui&ccedil;&atilde;o de Vendas a Prazo por Pa...";

  } else if ($comm == "perdasFaixa") {
      $content = "../searchClient/interf/perdaFaixas.php";
      $title = "Detalhamento das perdas efetivas por faixa de valor";

  } else if ($comm == "prevFinanc") {
      $content = "../searchClient/interf/PrevFinanc.php";
      $title = "Previsão de Financiamento";

  } else if ($comm == "buyers") {
    $content = "../searchClient/interf/Buyers.php";
    $title = "Principais Compradores";
 
  } else if ($comm == "buySubmit") {
    require ("buySubmit.php");
    if ($forward == "success") {
      $content = "../searchClient/interf/Lost.php";
      $title = "Hist&oacute;rico de Perdas";
    } else {
      $content = "../searchClient/interf/Buyers.php";
      $title = "Principais Compradores";
    }
  } else if ($comm == "lost") {
    $content = "../searchClient/interf/Lost.php";
    $title = "Hist&oacute;rico de Perdas";
  } else if ($comm == "simul") {
    $title = "Simulação de Prêmio";
    $content = "../searchClient/interf/Simulation.php";
 
  } else if ($comm == "import") {
    $title = "Consulta de Importadores";
    $content = "../searchClient/interf/Import.php";
    
  } else if ($comm == "importLimit") {
    $title = "Consulta de Importadores";
    $content = "../searchClient/interf/ImportLimit.php";
   
  } else if ($comm == "insertContact") {
//    require("InsertContact.php"); //SQL
    $title = "Inclusão de Contato";
    $content = "../searchClient/interf/insertContact.php";

  } else if ($comm == "searchContact") {
//    require("SearchContact.php"); //SQL
    $title = "Busca de Contatos";
    $content = "../searchClient/interf/searchContact.php";

// Alterado por Fábio Campos (elumini) em 17/08/2005
   }
   else if ($comm == "mudaRelacao") {
    $title = "Relação Cliente/Executivo";
    $content = "../searchClient/mudaRelacao.php";

  }
  else if ($comm == "gravaObs")
  {
    require("gravaOBs.php");
    $title = "Informações Gerais";
    $content = "../searchClient/interf/GeneralInf.php";
  }
  else if ($comm == "areacliente")
  {
    if ($state==1 || $state==2) {
       $title = "Área do Cliente";
       $content = "../inform/interf/InformRes.php";
    }else if ($state<>1 && $state<>2 && $state<>10){
       $title = "Área do Cliente";
       $content = "../client/interf/ViewClient.php";
    }

  }else if($comm == "pdf" ){
      // não é mais usado
  		$content = "../searchClient/gerarPdf.php";   		
  } else if($comm == "pdf_inter" ){
      $content = "../searchClient/gerarPdfInter.php";      
  } else if($comm == "paramCertif" ){
    
    require("insertParameters.php");
    
    $content = "../searchClient/interf/InformRes.php";
  
    require_once ("../searchClient/interf/nameClient.php");
    //$content = "../searchClient/gerarPdfInter.php";
  }
   //Adicionado por Michel Saddock
 
  if(count($_POST) > 2){
	 // adiciona dados do log após alterações no inform
	 $notif->historicolog((isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false), $_SESSION['userID'], $db, 2, $tem,'Inform',''); 
	 $notif->historicolog((isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false), $_SESSION['userID'], $db, 2, $tem1,'Importer',(isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : false)); 
  }
 
  // para gerar pdf precisamos que só gere a view sem o layout
	if($comm != "pdf_inter"){
    require_once("../../../home.php");
  } else {
    require($content);
  }

?>


