<?php

	if(!isset($_SESSION)){
		session_start();
	}

	$userID = $_SESSION['userID'];  
	//extract($_REQUEST);
	//error_log("request do banco");
	//error_log(print_r($_REQUEST, true));



	ini_set('max_execution_time', 600);
 
	require_once ("../rolePrefix.php");
	//se não recebemos o id do inform da página localizamos um inform disponível ou criamos outro

 	$v = isset($_REQUEST['v']) ? $_REQUEST['v'] : false;	

 	$acessointerno = isset($_REQUEST['acessointerno']) ? $_REQUEST['acessointerno'] : false;
 
 	if (isset($_REQUEST['idInform'])) {
 		if ($_REQUEST['idInform']) {
	 		$idInform  = $_REQUEST['idInform'];
	 	}
 	}
 	
 	if (!$comm) {
    $comm = $_REQUEST['comm'];
 	}

 	$req_comm = false;
 	if (isset($_REQUEST['comm'])) {
 		$req_comm = $_REQUEST['comm'] ? $_REQUEST['comm'] : false;
 	}

 	if(!isset($prev)){
	 	$prev =  isset($_REQUEST['prev']) ? $_REQUEST['prev'] : false;
 	}

  if(count($_POST) > 3){
  	$req_idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
  	$ses_userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;
  	$req_idBuy = isset($_REQUEST['idBuy']) ? $_REQUEST['idBuy'] : false;
  	$idImporter = isset($idImporter) ? $idImporter : false;

		// armazena dados do log
		//$tem = $notif->historicolog($req_idInform, $ses_userID, $db, 1, '', 'Inform', '');  
	//	$tem1 = $notif->historicolog($req_idInform, $ses_userID, $db, 1, '', 'Importer', ($req_idBuy > 0 ? $req_idBuy : $idImporter));  
	}
	
	// verifica se existe o usuário tem acesso Inform
	$stmt    = odbc_prepare($db, "select * from Inform_Usuarios where idUser=?");
	$resulx = odbc_execute($stmt, array($_SESSION['userID']));

	if(odbc_fetch_array($stmt)){		 
		//$idInform  = odbc_result($resulx,'idInform');
	}

	odbc_free_result($stmt);

 	$stmt    = odbc_prepare($db, "select state,i_Produto from Inform where id = ?");
 	$resulx = odbc_execute($stmt, array($idInform));
 	$rows = odbc_fetch_array($stmt);
	odbc_free_result($stmt);
 	
 	$i_Produto = $rows ? $rows['i_Produto'] : false;
 	$state = $rows ? $rows['state'] : false;

  	$ss_id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : false;
	if ($ss_id_user > 0) {
		if ($idInform == NULL) {
			//echo "informe entrei add 1<br>";
			require_once ("addInf.php");
		 } else {
			if (!$state) {	   
			  require_once ("addInf.php");
			}
		}
	} else if((!$idInform) && $_SESSION['idx'] == 0) {
		require_once ("addInf.php");
	}

	$hc_cliente = isset($hc_cliente) ? $hc_cliente : false;
	$_REQUEST['hc_cliente'] = isset($_REQUEST['hc_cliente']) ? $_REQUEST['hc_cliente'] : false;
	// $role["bancoBB"] = isset($role["bancoBB"]) ? $role["bancoBB"] : false;

 	if (!$hc_cliente) {
    $hc_cliente = $_REQUEST['hc_cliente'];
  }
	
	// Matheus Fernandes - 23/02/2022
    // Desabilitar regra para perfil Funcionario e Banco
 	// if (!$idInform && $_REQUEST['Gerar_Novo_Inform'] == '') {
 	if (!$idInform && isset($_REQUEST['Gerar_Novo_Inform']) == '' && $_SESSION['pefil'] != 'B' && $_SESSION['pefil'] != 'F') {
	 	header('location: ../access/Access.php?comm=exit');
	 	// Monta o resumo do informe
 	}else if($comm == 'cliente_acessos' || $req_comm == 'cliente_acessos'){
	 	$title = "CADASTRO DE ACESSOS"; 	 	   
	 	$content = "../inform/interf/lst_cliente_acessos.php";
 	}else if($comm == 'frm_acesso_cliente' || $req_comm == 'frm_acesso_cliente'){
	 	$title = "CADASTRO DE ACESSOS"; 	 	   
	 	$content = "../inform/interf/cliente_acessos.php";
 	}else if($comm == 'edit_acesso_cliente'){
     		require_once('edit_acesso_cliente.php');	
	 	$title = "ÁREA DO CLIENTE";
	 
	 	$content = "../inform/interf/lst_cliente_acessos.php";
 	}else if ($comm == "open" || $comm == 'japossuiapolice'){   
 			$canc = isset($canc) ? $canc : false;

	  	if ($canc && $state != 11 && $state != 9){
		  	$title = "ÁREA DO CLIENTE"; 		   
		  	$content = "../client/interf/Login.php";
	  	}else{
		  	require_once ("verifyAnt.php");
	      
		  	/*/Status do Inform
			  1  - novo
			  2  - 
			  3  - Análise de Crédito
			  4  - Tarifação
			  5  - Oferta
			  6  -
			  7  -
			  8  -
			  9  - Cancelado
			  10 - Apolice
			  11 - Encerrado
		  	*/
           
		 	if($comm == 'inform_res' || $req_comm == 'inform_res'){	  	    
			 	if ($_REQUEST['Gerar_Novo_Inform'] == 1) {		// criar novo inform
				 	$title = "Informa&ccedil;&otilde;es Gerais";	
				 	$content = "../inform/interf/GeneralInf.php";
			 	}else{
				 	$title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
				 	$content = "../inform/interf/InformRes.php";
			 	}
		 	}else if($req_comm == 'changeImporter'){
			   	$title = "ÁREA DO CLIENTE";
			   	$content = "../client/interf/ViewClient.php";
		  	}else if($comm == 'japossuiapolice'){
			  	$title = "ÁREA DO CLIENTE";
			  	$content = "../inform/interf/inform_area_principal_cliente.php";
		  	}else if ($_SESSION['pefil'] == 'B'){
		      $title = "ÁREA DO BANCO"; 
			  	$content = "../../../main.php"; 
		  	}else if($_SESSION['pefil'] == "CO" || $_SESSION['pefil'] == 'C' ){
			  	if($_SESSION['pefil'] == "CO"){ 
				  	//$content = "../area_consultor/selecionaConsultor.php";
				  	$_REQUEST['idInform'] = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
				  	if($_REQUEST['idInform'] > 0){
					 	// $comm = 
					 	//print '?'.$idInform.$_SESSION['pefil'];
					 	$title = "ÁREA DO CLIENTE";
					 	$content = "../client/interf/ViewClient.php";
				  	}else{
					 	$title = "ÁREA DO CONSULTOR";
					 	$content = "../area_consultor/consultorInforme.php";
				  	}
			  	}else{
			  		$_POST['inicial'] = isset($_POST['inicial']) ? $_POST['inicial'] : false;
				   	if($_POST['inicial'] == 1){
					  	$title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
				      		$content = "../inform/interf/InformRes.php";
				   	}else{
					  	$title = "ÁREA DO CLIENTE";
				      		$content = "../inform/interf/inform_area_principal_cliente.php";
				   	}
			  	}
		  	}else if ($_SESSION['pefil'] == "F"){	
			 
			  	if ($state >= 3 ){	
				  	//print $state;
				  	$title = "ÁREA DO CLIENTE - ACESSO COFACE"; 
				  	$content = "../client/interf/ViewClient.php";
			  	}else if($state < 3){
			  		
				    	$title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
				    	$content = "../inform/interf/InformRes.php";
			  	}
		  	}
		  
		  /*else if($_SESSION['id_user'] > 0 ||  $_SESSION['idx'] > 0){				 
				 if($_POST['inicial'] == 1){
					 $title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
				     $content = "../inform/interf/InformRes.php";
				 }else if($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'CO'){
					 $title = "ÁREA DO CLIENTE";
				     $content = "../inform/interf/inform_area_principal_cliente.php";
				 }else if($_SESSION['pefil'] == 'F'){	
				     $title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
				     $content = "../inform/interf/InformRes.php";
					 
		         }else{
					  $title = "ÁREA DO CLIENTE"; 
				      $content = "../area_consultor/consultorInforme.php";
				 }
		  }else if($acessointerno == 1){
			   $comm = 'open';
			   require_once ("../client/Client.php");
			  
		  }else{
			     $title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
			     $content = "../inform/interf/InformRes.php";
		  }*/
			  
		 
		  
	  }
	  
	  
	  
	  
  }else if($comm == 'inform_res' || $req_comm == 'inform_res'){	  
	     $gni = isset($_REQUEST['Gerar_Novo_Inform']) ? $_REQUEST['Gerar_Novo_Inform'] : 0;
		 if ($gni == 1) {		// criar novo inform
		     $title = "Informa&ccedil;&otilde;es Gerais";	
		     $content = "../inform/interf/GeneralInf.php";
	  
		 }else{
	         $title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
		     $content = "../inform/interf/InformRes.php";
	   
		 }
	//Criado por Michel Saddock 27/09/2006
	//Apos cadastro inicial, encaminha cliente para selecionar consultor
   }elseif($comm == "open2"){
		$status = "novo";
		$content = "../area_consultor/selecionaConsultor.php";
		
   }/*else if ($comm == "changeImporter") {
	  $comm = "open";
	  $content = "../client/interf/ViewClient.php";
	  //require_once("../../../home.php");
  
   }*/
    else if ($comm == "changeImporter") {
		
	   $title = 'Área do cliente';
	   $content = "../client/interf/ViewClient.php";
	  // Monta a tela de entrada das informações gerais
	} else if ($comm == "generalInformation") {
	
	  $title = "Informa&ccedil;&otilde;es Gerais";	
	  $content = "../inform/interf/GeneralInf.php";
	
	  // Guarda no banco as informações gerais do segurado
	}else if ($comm == "generalSubmit") {
		
	   require_once ("generalSubmit.php");
	   
	   
	   if ($forward == "success"){

		 
	      //$title = "Distribuição de Vendas por Tipo de Pagamento";
	  	  //$content = "../inform/interf/VolVendExt.php";
		  
		  $title = "Organização do Deparamento e Crédito"; 
	      $content = "../inform/interf/organizacaoDepCred.php";
			
	   }else{

	      //$title = "Distribuição de Vendas por Tipo de Pagamento";
		  $content = "../inform/interf/GeneralInf.php";
	   }
	  // Monta a tela de entrada de volume de exportação
	} else if ($comm == "volVendExt") {
		
		
	  $title = "Distribuição de Vendas por Tipo de Pagamento";	
	  $content = "../inform/interf/VolVendExt.php";
	
	  // tela anterior
	} else if ($comm == "volvend") {
	  $title = 'Distribuição de Vendas por Tipo de Pagamento';	
	  $content = "../inform/interf/VolVendExt.php";
	
	  // Guarda no banco as informações de volume de exportação
	} else if ($comm == "volSubmit") {
	  require_once ("volSubmit.php");
	  if ($forward == "success"){
	    //$title = "Distribuição de Vendas a Prazo por País"; 
		//$content = "../inform/interf/SegVendExt.php";
		$title = "Distribuição de vendas por Tipo de pagamento e Canal"; 
		$content = "../inform/interf/dist_tipo_vendas.php";
		
		
	  }else{
		$content = "../inform/interf/VolVendExt.php";
	  }
	  // Monta a tela de segmentação de vendas
	} else if ($comm == "segVendExt") {
		$title = "Distribuição de Vendas a Prazo por País";
	    $content = "../inform/interf/SegVendExt.php";
	
	  // Insere um elemento da segmentação de exportações
	} else if ($comm == "insSeg") {
	 
	  $incont = $total + $field->getNumField('valExp');
	  
	  	  
	  if ($incont > $prev){		
		  $msgInc = " O volume segmentado ultrapassou o valor declarado na página anterior.  ";
		  $comm = "segVendExt";
	  }else{
		  include_once("insSeg.php");
		  $comm = "segVendExt";
	  }
	  
	  $content = "../inform/interf/SegVendExt.php";
	
	  // Remove um elemento da segmentação de vendas
	} else if ($comm == "remSeg") {
	  require_once ("remSeg.php");
	  $content = "../inform/interf/SegVendExt.php";
	  
	  // Guarda no banco as informações de segmentação de exportações
	} else if ($comm == "segSubmit") {
	  require_once ("segSubmit.php");
	  if ($forward == "success"){
	      $title = "Relação de Clientes para Análise Preliminar – Buyer Study";
		  $content = "../inform/interf/Buyers.php";
	  }else{
		$content = "../inform/interf/SegVendExt.php";
	  }
	  // Monta a tela de previsão de financiamento
	} else if ($comm == "prevFinanc") {
	  $content = "../inform/interf/PrevFinanc.php";
	
	  // Guarda no banco as informações da previsão de financiamento
	} else if ($comm == "prevSubmit") {
	  require_once ("prevSubmit.php");
	  if ($forward == "success")
		$content = "../inform/interf/Buyers.php";
	  else
		$content = "../inform/interf/PrevFinanc.php";
	
	  // Monta a tela de relação de compradores
	} else if ($comm == "buyers") {
	  $title = "Relação de Clientes para Análise Preliminar - Buyer Study";	
	  $content = "../inform/interf/Buyers.php";
	
	  // Insere na base um comprador
	} else if ($comm == "insBuy") {
	  //$incont = $soma + ($field->getNumField('prevExp12')*1000);
	  //if ($incont > $prev){
	  //  $msgInc = "A Soma da Previsão de Exportação para todos os Importadores deve ser Menor ou Igual ao valor informado em 'Exportação Sujeita à Seguro' na linha 'Previsão para os próximos 12 meses' do Quadro I. Por favor, Corrija o Valor do Campo 'Previsão Vol. Export (US$ Mil)' ";
	  //}else{
		require_once ("insBuy.php");
	  //}
	  $title = "Relação de Clientes para Análise Preliminar - Buyer Study";	
	  $content = "../inform/interf/Buyers.php";
	
	  // Remove um comprador
	} else if ($comm == "remBuy") {
	  require_once ("remBuy.php");
	  $content = "../inform/interf/Buyers.php";
	
	  // ALTERA um comprador
	} else if ($comm == "altBuy") {
	  //require_once ("altBuy.php");
	  $content = "../inform/interf/Buyers.php";
	
	} else if ($comm == "setAltBuy") {
	  require_once("soma.php");
	  $incont = $soma + ($field->getNumField('valExp'));
	  //echo "incont=$incont, prev=$prev<br>";
	  //if ($incont > $prev){
	  //  $msgInc = "A Soma da Previsão de Exportação para todos os Importadores deve ser Menor ou Igual ao Volume Passível de Cobertura pelo Seguro (Quadro I). Por favor, Corrija o Valor do Campo 'Previsão Vol. Export (US$ Mil)' ";
	  //  $comm = "segVendExp";
	  //}else{
	  //  echo "<pre>End Inform.php:$address</pre>";
	  //  echo "<pre>End Inform.php:$name</pre>";
		require_once ("setAltBuy.php");
	  //}
	  $content = "../inform/interf/Buyers.php";
	
	  // Guarda no banco as informações dos compradores
	} else if ($comm == "buySubmit") {
	  require_once ("buySubmit.php");
	  
	  if ($forward == "success"){
		 $title = "Dívidas Vencidas";	
		 $content = "../inform/interf/dividas_vencidas.php";
	  }else 
	     $content = "../inform/interf/Buyers.php";
	
	  // Guarda no banco as informações dos compradores
	} else if ($comm == "lost") {
		
	  $title = "Histórico de Perdas";	
	  $content = "../inform/interf/Lost.php";
	
	} else if ($comm == "lostSubmit") {
	     require_once ("lostSubmit.php");
	  
	  
	  //if ($forward == "success")
	  // $content = "../inform/interf/InformRes.php";
	  //else{ 
	     $title = "Detalhamento das perdas efetivas por faixa de valor";
	     $content = "../inform/interf/det_Perda_Faixa_Valor.php";
	     //$content = "../inform/interf/Lost.php";
	  //}
	 
	}else if ($comm == 'lostback'){
		 require_once ("lostSubmit.php");	  
	  
	  //if ($forward == "success")
	  // $content = "../inform/interf/InformRes.php";
	  //else{ dividas_vencidas
	     $title = "Dívidas Vencidas";
	     $content = "../inform/interf/dividas_vencidas.php";
	     //$content = "../inform/interf/Lost.php";
	  //}
		
		
	}else if ($comm == "dist_lim_cred"){ 
	  $title = "Distribuição de vendas  a prazo por faixa de limite de crédito";
	  $content = "../inform/interf/dist_lim_cred.php";
	 
	}else if($comm == "dist_lim_cred_submit"){
		 require_once ("dist_lim_cred_submit.php");
		 
	     $title = "Distribuição de vendas  a prazo por faixa de limite de crédito";
	     if ($forward == "success"){
			$title = "Distribuição de Vendas a Prazo por País";
	       	$content = "../inform/interf/SegVendExt.php";
		 }else 
	        $content = "../inform/interf/dist_lim_cred.php";
		
	}else if($comm == "det_Perda_Faixa_Valor"){
		$title = "Detalhamento das perdas efetivas por faixa de valor";
	    $content = "../inform/interf/det_Perda_Faixa_Valor.php";
		
	}else if($comm == "det_Perda_Faixa_Valor_submit"){
		 require_once ("det_Perda_Faixa_Valor_submit.php");
	  
	     if ($forward == "success"){
			  $title = "FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO"; 
			  $content = "../inform/interf/InformRes.php";
			 
		 }else 
	        $content = "../inform/interf/det_Perda_Faixa_Valor.php";
	
	}else if($comm == 'dist_tipo_vendas'){
		//se interno
		 if($i_Produto == 2){
		    $title = "Distribuição de Vendas por Tipo de Pagamento e Canal"; 
		 }else{
			$title = "Distribuição de Vendas por Canal e Regiões"; 
		 }	
	    $content = "../inform/interf/dist_tipo_vendas.php";
		
	}else if($comm == 'organizacaoDepCred'){
		    $title = "Organização do Deparamento e Crédito"; 
	    	$content = "../inform/interf/organizacaoDepCred.php";

	}else if($comm == 'organizacaoDepCred_submit'){
		 require_once ("organizacaoDepCred_submit.php");
	  
	     if ($forward == "success"){
		     $title = "Distribuição de Vendas por Tipo de Pagamento";
	  	     $content = "../inform/interf/VolVendExt.php";
		 }else {
		    $title = "Organização do Deparamento e Crédito"; 
	    	$content = "../inform/interf/organizacaoDepCred.php";
		 }
		
	}else if ($comm == 'dist_tipo_vendas_submit') {
		 require_once ("dist_tipo_vendas_submit.php");
	     
		 if ($forward == "success"){
		     $title = "Distribuição de Vendas a Prazo por Faixa de Limite de Crédito";
		     $content = "../inform/interf/dist_lim_cred.php";
		 }else 
	         $content = "../inform/interf/dist_tipo_vendas.php";
			  
		 /*
		 
		 if($i_Produto == 2){
		    $title = "Distribuição de Vendas por Tipo de Pagamento e Canal"; 
		 }else{
			$title = "Distribuição de Vendas por Canal e Regiões"; 
		 }	
		 
	     if ($forward == "success"){
		     $title = "Dívidas Vencidas";
		     $content = "../inform/interf/dividas_vencidas.php";
		 }else 
	         $content = "../inform/interf/dist_tipo_vendas.php";
		*/
	
	// Apresentação da simulação de prêmio
	}else if($comm == 'dividas_vencidas'){
		//se interno
		//$title = "Distribuição de Vendas por Canal e Regiões";
		$title = "Dívidas Vencidas";		
	    $content = "../inform/interf/dividas_vencidas.php";
		
		
	}else if ($comm == 'dividas_vencidas_submit') {
		 require_once ("dividas_vencidas_submit.php");
	     $title = "Dívidas Vencidas";	
		 
	     if ($forward == "success"){
			 $title = "Histórico de Perdas";
		     $content = "../inform/interf/Lost.php";
		 }else 
	         $content = "../inform/interf/dividas_vencidas.php";
			 
	}else if ($comm == "simul") {
	  $content = "../inform/interf/Simulation.php";
	
	  // tela anterior
	} else if ($comm == "back" || $comm == 'done') {
		
		 
		 if ($v == "1") {
			//$content = "../inform/interf/GeneralInf.php";
			$title = "Organização do Departamento de Crédido";
			$content = "../inform/interf/organizacaoDepCred.php";
		 }  // Conclusão do informe
		 else if ($comm == "done"){
		     //GEORGE - ALTERADO, COLOQUEI o hc_redirdone!!!
			 
			  $hc_redirdone = false;
			  
			  require_once ("confComp.php");
			  
			   // die('?'.$prev .'?'. $quadro2);
			  if($prev != $quadro2){
				$msgInc = 'A soma dos valores "Distribui&ccedil;&atilde;o de Vendas a Prazo por Pa&iacute;s"
						   deve ser igual ao Volume Passível de Cobertura pelo Seguro (Distribui&ccedil;&atilde;o de Vendas por Tipo de Pagamento). Por favor, corrija uma das opções.';
				//die($msgInc);
				$content = "../inform/interf/VolVendExt.php";
			  }else {
				 
			     //if ($field->getField("mot") == "OK" ||  $field->getField("mot") == "Aceitar" || $field->getField("mot") == "Recusar" ) {
				if ($field->getField("mot") == "Aceitar" || $field->getField("mot") == "Recusar" ) {
                      			//print 'oi'.$msg;
					require_once("../executive/done.php");
                 		}else{
                      			//print 'oi'.$msg;
				     	require_once ("done.php");
				}
				 
				 if ($forward == "success" && $forwardNew == "success" && $_SESSION['pefil'] == 'F'){
				      $title = "Notificações";
                      $content = "../../../main.php";
					  $_SESSION['idNotification']  = '';
				 }else{
					  if ($forward == "success") {
						 //print '?'.$role["executive"];
						 //break;
						 //print $forward.'?'.$role["client"];
						 if ($role["client"] || $_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B' ) {
							$msgINT = 'Informações enviadas com sucesso!'; 
							$title = 'ÁREA DO CLIENTE';
							
							//$content = "../inform/interf/Retorno.php";
							$content = "../inform/interf/inform_area_principal_cliente.php";
							//session_destroy();
						 }else{
							
							//$content = "../notification/BoxInput.php";
							$title = 'FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO';
							$hc_redirdone = true;
							$content = "../inform/interf/InformRes.php";
						 }			  
		  
					  }else{
						    if($forward == "error"){
								$msgInc = "As Informações estão incompletas, verifique.";
						    }
						 	
						  $title = 'FORMULÁRIO DE SOLICITAÇÃO DE SEGURO DE CRÉDITO';
						  $content = "../inform/interf/InformRes.php";
					  }
					  
				  }
		      }
			  
		 }
	
	} else if($comm == "client"){
	  $comm = "open";
	  require_once("../client/Client.php");
	} else if($comm == 'volta'){
	  $comm = 'renovacao';
	  require_once("../executive/Executive.php");
	  
	
	
	}else if($comm == 'goback'){  	
	    
		if ($v == '1') {		
			$comm = 'notif';
			require_once("../executive/Executive.php");
		}elseif ($v == '0'){
			$title = "Notificações";
			$content = "../../../main.php";
		}
	}else if($comm == 'address'){
	    $comm = 'addAddress';
	    $idBuyer = $idBuy;
	    $back = 1;
	    require_once("../client/Client.php");
	}

  if(count($_POST) > 3){  
  	$req_idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
  	$ses_userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;
  	$req_idBuy = isset($_REQUEST['idBuy']) ? $_REQUEST['idBuy'] : false;
  	$idImporter = isset($idImporter) ? $idImporter : false;
  	$tem = isset($tem) ? $tem : false;
  	$tem1 = isset($tem1) ? $tem1 : false;
  	
	 // $notif->historicolog($req_idInform, $ses_userID, $db, 2, $tem, 'Inform', '');  
	 // $notif->historicolog($req_idInform, $ses_userID, $db, 2, $tem1, 'Importer', ($req_idBuy > 0 ? $req_idBuy : $idImporter));
  }

  require_once("../../../home.php");

?>

