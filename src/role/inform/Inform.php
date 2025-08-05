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
	//se n�o recebemos o id do inform da p�gina localizamos um inform dispon�vel ou criamos outro

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
	
	// verifica se existe o usu�rio tem acesso Inform
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
	 	$title = "�REA DO CLIENTE";
	 
	 	$content = "../inform/interf/lst_cliente_acessos.php";
 	}else if ($comm == "open" || $comm == 'japossuiapolice'){   
 			$canc = isset($canc) ? $canc : false;

	  	if ($canc && $state != 11 && $state != 9){
		  	$title = "�REA DO CLIENTE"; 		   
		  	$content = "../client/interf/Login.php";
	  	}else{
		  	require_once ("verifyAnt.php");
	      
		  	/*/Status do Inform
			  1  - novo
			  2  - 
			  3  - An�lise de Cr�dito
			  4  - Tarifa��o
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
				 	$title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
				 	$content = "../inform/interf/InformRes.php";
			 	}
		 	}else if($req_comm == 'changeImporter'){
			   	$title = "�REA DO CLIENTE";
			   	$content = "../client/interf/ViewClient.php";
		  	}else if($comm == 'japossuiapolice'){
			  	$title = "�REA DO CLIENTE";
			  	$content = "../inform/interf/inform_area_principal_cliente.php";
		  	}else if ($_SESSION['pefil'] == 'B'){
		      $title = "�REA DO BANCO"; 
			  	$content = "../../../main.php"; 
		  	}else if($_SESSION['pefil'] == "CO" || $_SESSION['pefil'] == 'C' ){
			  	if($_SESSION['pefil'] == "CO"){ 
				  	//$content = "../area_consultor/selecionaConsultor.php";
				  	$_REQUEST['idInform'] = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
				  	if($_REQUEST['idInform'] > 0){
					 	// $comm = 
					 	//print '?'.$idInform.$_SESSION['pefil'];
					 	$title = "�REA DO CLIENTE";
					 	$content = "../client/interf/ViewClient.php";
				  	}else{
					 	$title = "�REA DO CONSULTOR";
					 	$content = "../area_consultor/consultorInforme.php";
				  	}
			  	}else{
			  		$_POST['inicial'] = isset($_POST['inicial']) ? $_POST['inicial'] : false;
				   	if($_POST['inicial'] == 1){
					  	$title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
				      		$content = "../inform/interf/InformRes.php";
				   	}else{
					  	$title = "�REA DO CLIENTE";
				      		$content = "../inform/interf/inform_area_principal_cliente.php";
				   	}
			  	}
		  	}else if ($_SESSION['pefil'] == "F"){	
			 
			  	if ($state >= 3 ){	
				  	//print $state;
				  	$title = "�REA DO CLIENTE - ACESSO COFACE"; 
				  	$content = "../client/interf/ViewClient.php";
			  	}else if($state < 3){
			  		
				    	$title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
				    	$content = "../inform/interf/InformRes.php";
			  	}
		  	}
		  
		  /*else if($_SESSION['id_user'] > 0 ||  $_SESSION['idx'] > 0){				 
				 if($_POST['inicial'] == 1){
					 $title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
				     $content = "../inform/interf/InformRes.php";
				 }else if($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'CO'){
					 $title = "�REA DO CLIENTE";
				     $content = "../inform/interf/inform_area_principal_cliente.php";
				 }else if($_SESSION['pefil'] == 'F'){	
				     $title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
				     $content = "../inform/interf/InformRes.php";
					 
		         }else{
					  $title = "�REA DO CLIENTE"; 
				      $content = "../area_consultor/consultorInforme.php";
				 }
		  }else if($acessointerno == 1){
			   $comm = 'open';
			   require_once ("../client/Client.php");
			  
		  }else{
			     $title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
			     $content = "../inform/interf/InformRes.php";
		  }*/
			  
		 
		  
	  }
	  
	  
	  
	  
  }else if($comm == 'inform_res' || $req_comm == 'inform_res'){	  
	     $gni = isset($_REQUEST['Gerar_Novo_Inform']) ? $_REQUEST['Gerar_Novo_Inform'] : 0;
		 if ($gni == 1) {		// criar novo inform
		     $title = "Informa&ccedil;&otilde;es Gerais";	
		     $content = "../inform/interf/GeneralInf.php";
	  
		 }else{
	         $title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
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
		
	   $title = '�rea do cliente';
	   $content = "../client/interf/ViewClient.php";
	  // Monta a tela de entrada das informa��es gerais
	} else if ($comm == "generalInformation") {
	
	  $title = "Informa&ccedil;&otilde;es Gerais";	
	  $content = "../inform/interf/GeneralInf.php";
	
	  // Guarda no banco as informa��es gerais do segurado
	}else if ($comm == "generalSubmit") {
		
	   require_once ("generalSubmit.php");
	   
	   
	   if ($forward == "success"){

		 
	      //$title = "Distribui��o de Vendas por Tipo de Pagamento";
	  	  //$content = "../inform/interf/VolVendExt.php";
		  
		  $title = "Organiza��o do Deparamento e Cr�dito"; 
	      $content = "../inform/interf/organizacaoDepCred.php";
			
	   }else{

	      //$title = "Distribui��o de Vendas por Tipo de Pagamento";
		  $content = "../inform/interf/GeneralInf.php";
	   }
	  // Monta a tela de entrada de volume de exporta��o
	} else if ($comm == "volVendExt") {
		
		
	  $title = "Distribui��o de Vendas por Tipo de Pagamento";	
	  $content = "../inform/interf/VolVendExt.php";
	
	  // tela anterior
	} else if ($comm == "volvend") {
	  $title = 'Distribui��o de Vendas por Tipo de Pagamento';	
	  $content = "../inform/interf/VolVendExt.php";
	
	  // Guarda no banco as informa��es de volume de exporta��o
	} else if ($comm == "volSubmit") {
	  require_once ("volSubmit.php");
	  if ($forward == "success"){
	    //$title = "Distribui��o de Vendas a Prazo por Pa�s"; 
		//$content = "../inform/interf/SegVendExt.php";
		$title = "Distribui��o de vendas por Tipo de pagamento e Canal"; 
		$content = "../inform/interf/dist_tipo_vendas.php";
		
		
	  }else{
		$content = "../inform/interf/VolVendExt.php";
	  }
	  // Monta a tela de segmenta��o de vendas
	} else if ($comm == "segVendExt") {
		$title = "Distribui��o de Vendas a Prazo por Pa�s";
	    $content = "../inform/interf/SegVendExt.php";
	
	  // Insere um elemento da segmenta��o de exporta��es
	} else if ($comm == "insSeg") {
	 
	  $incont = $total + $field->getNumField('valExp');
	  
	  	  
	  if ($incont > $prev){		
		  $msgInc = " O volume segmentado ultrapassou o valor declarado na p�gina anterior.  ";
		  $comm = "segVendExt";
	  }else{
		  include_once("insSeg.php");
		  $comm = "segVendExt";
	  }
	  
	  $content = "../inform/interf/SegVendExt.php";
	
	  // Remove um elemento da segmenta��o de vendas
	} else if ($comm == "remSeg") {
	  require_once ("remSeg.php");
	  $content = "../inform/interf/SegVendExt.php";
	  
	  // Guarda no banco as informa��es de segmenta��o de exporta��es
	} else if ($comm == "segSubmit") {
	  require_once ("segSubmit.php");
	  if ($forward == "success"){
	      $title = "Rela��o de Clientes para An�lise Preliminar � Buyer Study";
		  $content = "../inform/interf/Buyers.php";
	  }else{
		$content = "../inform/interf/SegVendExt.php";
	  }
	  // Monta a tela de previs�o de financiamento
	} else if ($comm == "prevFinanc") {
	  $content = "../inform/interf/PrevFinanc.php";
	
	  // Guarda no banco as informa��es da previs�o de financiamento
	} else if ($comm == "prevSubmit") {
	  require_once ("prevSubmit.php");
	  if ($forward == "success")
		$content = "../inform/interf/Buyers.php";
	  else
		$content = "../inform/interf/PrevFinanc.php";
	
	  // Monta a tela de rela��o de compradores
	} else if ($comm == "buyers") {
	  $title = "Rela��o de Clientes para An�lise Preliminar - Buyer Study";	
	  $content = "../inform/interf/Buyers.php";
	
	  // Insere na base um comprador
	} else if ($comm == "insBuy") {
	  //$incont = $soma + ($field->getNumField('prevExp12')*1000);
	  //if ($incont > $prev){
	  //  $msgInc = "A Soma da Previs�o de Exporta��o para todos os Importadores deve ser Menor ou Igual ao valor informado em 'Exporta��o Sujeita � Seguro' na linha 'Previs�o para os pr�ximos 12 meses' do Quadro I. Por favor, Corrija o Valor do Campo 'Previs�o Vol. Export (US$ Mil)' ";
	  //}else{
		require_once ("insBuy.php");
	  //}
	  $title = "Rela��o de Clientes para An�lise Preliminar - Buyer Study";	
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
	  //  $msgInc = "A Soma da Previs�o de Exporta��o para todos os Importadores deve ser Menor ou Igual ao Volume Pass�vel de Cobertura pelo Seguro (Quadro I). Por favor, Corrija o Valor do Campo 'Previs�o Vol. Export (US$ Mil)' ";
	  //  $comm = "segVendExp";
	  //}else{
	  //  echo "<pre>End Inform.php:$address</pre>";
	  //  echo "<pre>End Inform.php:$name</pre>";
		require_once ("setAltBuy.php");
	  //}
	  $content = "../inform/interf/Buyers.php";
	
	  // Guarda no banco as informa��es dos compradores
	} else if ($comm == "buySubmit") {
	  require_once ("buySubmit.php");
	  
	  if ($forward == "success"){
		 $title = "D�vidas Vencidas";	
		 $content = "../inform/interf/dividas_vencidas.php";
	  }else 
	     $content = "../inform/interf/Buyers.php";
	
	  // Guarda no banco as informa��es dos compradores
	} else if ($comm == "lost") {
		
	  $title = "Hist�rico de Perdas";	
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
	     $title = "D�vidas Vencidas";
	     $content = "../inform/interf/dividas_vencidas.php";
	     //$content = "../inform/interf/Lost.php";
	  //}
		
		
	}else if ($comm == "dist_lim_cred"){ 
	  $title = "Distribui��o de vendas  a prazo por faixa de limite de cr�dito";
	  $content = "../inform/interf/dist_lim_cred.php";
	 
	}else if($comm == "dist_lim_cred_submit"){
		 require_once ("dist_lim_cred_submit.php");
		 
	     $title = "Distribui��o de vendas  a prazo por faixa de limite de cr�dito";
	     if ($forward == "success"){
			$title = "Distribui��o de Vendas a Prazo por Pa�s";
	       	$content = "../inform/interf/SegVendExt.php";
		 }else 
	        $content = "../inform/interf/dist_lim_cred.php";
		
	}else if($comm == "det_Perda_Faixa_Valor"){
		$title = "Detalhamento das perdas efetivas por faixa de valor";
	    $content = "../inform/interf/det_Perda_Faixa_Valor.php";
		
	}else if($comm == "det_Perda_Faixa_Valor_submit"){
		 require_once ("det_Perda_Faixa_Valor_submit.php");
	  
	     if ($forward == "success"){
			  $title = "FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO"; 
			  $content = "../inform/interf/InformRes.php";
			 
		 }else 
	        $content = "../inform/interf/det_Perda_Faixa_Valor.php";
	
	}else if($comm == 'dist_tipo_vendas'){
		//se interno
		 if($i_Produto == 2){
		    $title = "Distribui��o de Vendas por Tipo de Pagamento e Canal"; 
		 }else{
			$title = "Distribui��o de Vendas por Canal e Regi�es"; 
		 }	
	    $content = "../inform/interf/dist_tipo_vendas.php";
		
	}else if($comm == 'organizacaoDepCred'){
		    $title = "Organiza��o do Deparamento e Cr�dito"; 
	    	$content = "../inform/interf/organizacaoDepCred.php";

	}else if($comm == 'organizacaoDepCred_submit'){
		 require_once ("organizacaoDepCred_submit.php");
	  
	     if ($forward == "success"){
		     $title = "Distribui��o de Vendas por Tipo de Pagamento";
	  	     $content = "../inform/interf/VolVendExt.php";
		 }else {
		    $title = "Organiza��o do Deparamento e Cr�dito"; 
	    	$content = "../inform/interf/organizacaoDepCred.php";
		 }
		
	}else if ($comm == 'dist_tipo_vendas_submit') {
		 require_once ("dist_tipo_vendas_submit.php");
	     
		 if ($forward == "success"){
		     $title = "Distribui��o de Vendas a Prazo por Faixa de Limite de Cr�dito";
		     $content = "../inform/interf/dist_lim_cred.php";
		 }else 
	         $content = "../inform/interf/dist_tipo_vendas.php";
			  
		 /*
		 
		 if($i_Produto == 2){
		    $title = "Distribui��o de Vendas por Tipo de Pagamento e Canal"; 
		 }else{
			$title = "Distribui��o de Vendas por Canal e Regi�es"; 
		 }	
		 
	     if ($forward == "success"){
		     $title = "D�vidas Vencidas";
		     $content = "../inform/interf/dividas_vencidas.php";
		 }else 
	         $content = "../inform/interf/dist_tipo_vendas.php";
		*/
	
	// Apresenta��o da simula��o de pr�mio
	}else if($comm == 'dividas_vencidas'){
		//se interno
		//$title = "Distribui��o de Vendas por Canal e Regi�es";
		$title = "D�vidas Vencidas";		
	    $content = "../inform/interf/dividas_vencidas.php";
		
		
	}else if ($comm == 'dividas_vencidas_submit') {
		 require_once ("dividas_vencidas_submit.php");
	     $title = "D�vidas Vencidas";	
		 
	     if ($forward == "success"){
			 $title = "Hist�rico de Perdas";
		     $content = "../inform/interf/Lost.php";
		 }else 
	         $content = "../inform/interf/dividas_vencidas.php";
			 
	}else if ($comm == "simul") {
	  $content = "../inform/interf/Simulation.php";
	
	  // tela anterior
	} else if ($comm == "back" || $comm == 'done') {
		
		 
		 if ($v == "1") {
			//$content = "../inform/interf/GeneralInf.php";
			$title = "Organiza��o do Departamento de Cr�dido";
			$content = "../inform/interf/organizacaoDepCred.php";
		 }  // Conclus�o do informe
		 else if ($comm == "done"){
		     //GEORGE - ALTERADO, COLOQUEI o hc_redirdone!!!
			 
			  $hc_redirdone = false;
			  
			  require_once ("confComp.php");
			  
			   // die('?'.$prev .'?'. $quadro2);
			  if($prev != $quadro2){
				$msgInc = 'A soma dos valores "Distribui&ccedil;&atilde;o de Vendas a Prazo por Pa&iacute;s"
						   deve ser igual ao Volume Pass�vel de Cobertura pelo Seguro (Distribui&ccedil;&atilde;o de Vendas por Tipo de Pagamento). Por favor, corrija uma das op��es.';
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
				      $title = "Notifica��es";
                      $content = "../../../main.php";
					  $_SESSION['idNotification']  = '';
				 }else{
					  if ($forward == "success") {
						 //print '?'.$role["executive"];
						 //break;
						 //print $forward.'?'.$role["client"];
						 if ($role["client"] || $_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B' ) {
							$msgINT = 'Informa��es enviadas com sucesso!'; 
							$title = '�REA DO CLIENTE';
							
							//$content = "../inform/interf/Retorno.php";
							$content = "../inform/interf/inform_area_principal_cliente.php";
							//session_destroy();
						 }else{
							
							//$content = "../notification/BoxInput.php";
							$title = 'FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO';
							$hc_redirdone = true;
							$content = "../inform/interf/InformRes.php";
						 }			  
		  
					  }else{
						    if($forward == "error"){
								$msgInc = "As Informa��es est�o incompletas, verifique.";
						    }
						 	
						  $title = 'FORMUL�RIO DE SOLICITA��O DE SEGURO DE CR�DITO';
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
			$title = "Notifica��es";
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

