<?php  $log_query = "";

//ALTERADO HICOM 06/04/2004
//Alterado HiCom mes 04

$query = "select inf.state, inf.idAnt, inf.name from Inform inf JOIN Importer imp ON (imp.idInform = inf.id) where imp.id=$idBuyer";
$c = odbc_exec($db, $query);

if(odbc_fetch_row($c)){
  	$state    = odbc_result($c, 1);
  	$idAnt    = odbc_result($c, 2);
  	$nameinf  = odbc_result($c, 3);
  	
  	$cur = odbc_exec($db, "select idTwin from Importer where id=$idBuyer");
  	
  	if (odbc_fetch_row ($cur)){
    	$idOther = odbc_result($cur, 'idTwin');
  	}
  	
  	if(! $idOther){
    	$y = odbc_exec($db, "select id from Importer where idTwin=$idBuyer");
    	$idOther = odbc_result($y, 1);
  	}

  	if($state >= 1 && $state <= 8){
    	// verifica se o importador tem credito concedido
    	$x = odbc_exec($db, "select * from ChangeCredit where idImporter=$idBuyer and credit > 0 and state = 6 order by id desc");
    	
    	if(odbc_fetch_row($x)){ // se tiver credito
      		// desativa o importador
      		$e = odbc_exec ($db, "UPDATE Importer SET state=7 WHERE id=$idBuyer");

	  		//criado por Wagner 29/08/2008
	  		if($e){
				$log_query .= "UPDATE Importer SET state=7 WHERE id=$idBuyer";
	  		}
	  
      		$query1 =
	 			"INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor, credit)".
	 			"VALUES ('7', $userID, $idBuyer, 0, 1, 0, 0)";
      		$r = odbc_exec($db, $query1);
	  
	  		//criado por Wagner 29/08/2008
	  		if($r){
				$log_query .= $query1;
	  		}
	  
			//       if($idOther){
			// 	$query1 =
			// 	   "INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor, credit)".
			// 	   "VALUES ('7', $userID, $idOther, 0, 1, 0, 0)";
			// 	$r = odbc_exec($db, $query1);
			//       }
    	}else{
      		// o importador deve voltar para a tela do executivo com a opcao de enviar para o setor de credito
      		$f = odbc_exec ($db, "UPDATE Importer SET state=8 WHERE id=$idBuyer");
	  
	  		//criado por Wagner 29/08/2008
	  		if($f){
				$log_query .= "UPDATE Importer SET state=8 WHERE id=$idBuyer";
	  		}
	  
      		if($comm == 'rejeitar'){
				$x = odbc_exec($db,
		       		"select creditSolic from ChangeCredit where idImporter=$idBuyer ".
		       		"and creditSolic is not null order by id desc");
				$creditSolic = odbc_result($x, 1);
      		}else{
				$creditSolic = 0;
      		}

      		$query1 =
	 			"INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor)".
	 			"VALUES ('8', $userID, $idBuyer, $creditSolic, 1, 1)";
      		$r = odbc_exec($db, $query1);
	  
	  		//criado por Wagner 29/08/2008
	  		if($r){
				$log_query .= $query1;
	  		}	  
	  
			//       if($idOther){
			// 	$query1 =
			// 	   "INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor)".
			// 	   "VALUES ('8', $userID, $idOther, 0, 0, 0)";
			// 	$r = odbc_exec($db, $query1);
			//       }
    	}
  	}else if ($state >= 9 && $state <= 11){
    	// inativar o importador sem cobranca de analise e monitoramento, e sem Ci Coface
    	$query = "UPDATE Importer SET state=8, c_Coface_Imp=0 WHERE id=$idBuyer";
    	$g = odbc_exec ($db, $query);
	
	  	//criado por Wagner 29/08/2008
	  	if($g){
			$log_query .= $query;
	  	}	

    	$query1 =
      		"INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor)".
      		"VALUES ('8', $userID, $idBuyer, 0, 1, 1)";
    	$r = odbc_exec ($db,$query1);
	
	  	//criado por Wagner 29/08/2008
	  	if($r){
			$log_query .= $query1;
	  	}
	
		//     if($idOther){
		//       $query1 =
		// 	"INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor)".
		//         "VALUES ('8', $userID, $idOther, 0, 0, 0)";
		//       $r = odbc_exec ($db,$query1);
		//     }
    	$d = odbc_exec($db, "select max(id) from ChangeCredit where idImporter=$idBuyer");
    	
    	if(odbc_fetch_row($d)){
      		$id_cc = odbc_result($d, 1);
      		
      		if(! odbc_exec($db, "update ChangeCredit set monitor=0, analysis=0 where id=$id_cc")){
				$msg = "Erro ao inativar importador";
      		}else{
				$log_query .= "update ChangeCredit set monitor=0, analysis=0 where id=$id_cc";
	  		}
    	}
  	}
}

$cot = odbc_exec($db, "SELECT emailContact, name, state, warantyInterest, Litigio from Inform WHERE id=$idInform");

$emailContact = trim(odbc_result($cot, 1));
$stateExp = odbc_result($cot, 'state');
$ApoliceBB = odbc_result($cot,'Litigio');

if($emailContact){
	//$emailContact .= ", credito@sbce.com.br";
}else{
  	$emailContact = "siex@cofacedobrasil.com";
}

$exportador = odbc_result($cot, 2);

/*
//####### ini ####### adicionado por eliel vieira - elumini - em 14/04/2008
// referente a demanda 1468 - SAD
//
// solicitacao: acrescentar o pais do importador ao nome
// arquivos relacionados: reject.php, rejeitachange.php e pendreject.php
//
*/
$ex = odbc_exec($db, "SELECT name FROM Importer WHERE id = $idBuyer");
$name        = odbc_result($ex, 1);

$ex2          = odbc_exec($db, " select i.name, c.name from Importer i join Country c on c.id=i.idCountry WHERE i.id=$idBuyer ");
$imp_country = odbc_result($ex2, 2);

//echo "Importador 2: $name - $imp_country <br>";

/*
$msgmail = "<font class=texto><br>Prezado Segurado,<br><br><br>\r\n";
$msgmail = $msgmail . " Sua solicitação não pôde ser processada, favor contatar o Departamento de Crédito <u>(21) 2510-5000</u> para maiores esclarecimentos.\r\n";
$msgmail = $msgmail . "<br><br><ul><li>Importador: $name / $imp_country </li></ul> <br>\r\n";
$msgmail = $msgmail . " Atenciosamente, <br><br><br> Departamento de Crédito </font>\r\n";
*/

require_once("../MailSend.php"); 

$mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
$mail->FromName = "Credito"; // Seu nome 
// Define os destinatário(s)

if ($stateExp == 10 && $ApoliceBB == 1) {
	if($state >= 1 && $state <= 8){ // se for prospect, manda email pro executivo responsavel
		$x = odbc_exec($db, "select u.email from Users u join Inform i on u.id=i.idUser where i.id=$idInform");
		
	  	if(odbc_fetch_row($x)){
	    	$email = trim(odbc_result($x, 1));
	    	$to = $email;
	  		/*
	     	Alterado por Tiago V N - 08/08/2007
	  		*/
	
	    	$msgmail = "<font class=texto><br>Prezado Executivo,<br><br><br>\r\n";
	    	$msgmail = $msgmail . " A solicitação abaixo não pôde ser processada, favor contatar o Departamento de Crédito para maiores esclarecimentos.\r\n";
	    	$msgmail = $msgmail . "<br><br><ul><li>Importador: $name<br>País: $imp_country </li></ul> <br>\r\n";
	    	$msgmail = $msgmail . " Atenciosamente, <br><br><br> Departamento de Crédito </font>\r\n";
	
	   		//NÃO!!! ALTERADO HICOM 06/04/2004
			//mail("$email, credito@sbce.com.br", trim($exportador), $msgmail,
			// "From: credito@sbce.com.br\nContent-type: text/html");
			//TESTADO POR WAGNER 26/08/2008
			//TESTE DE VERIFICAÇÃO , NESTE TESTE FOI VERIFICADO SE O NOME DO PAIS VAI NO CORPO DO EMAIL
			//echo "PROSPECT".$msgmail;
	  	}
	}else{ // se for segurado, manda pro contato do Inform e pra lista de contatos
	  	/*
	    Alterado por Tiago V N - 08/08/2007
	  	*/
	
	   	$msgmail = "<font class=texto><br>Prezado Segurado,<br><br><br>\r\n";
	   	$msgmail = $msgmail . " A solicitação abaixo não pôde ser processada, favor contatar o Departamento de Crédito para maiores esclarecimentos.\r\n";
	   	$msgmail = $msgmail . "<br><br><ul><li>Importador: $name <br>País: $imp_country </li></ul> <br>\r\n";
	   	$msgmail = $msgmail . " Atenciosamente, <br><br><br> Departamento de Crédito </font>\r\n";
	
	
	  	$query = "SELECT email FROM Contact WHERE idInform=$idInform AND notificationForChangeCredit=1";
	  	$not = odbc_exec($db, $query);
	  	$to = trim($emailContact);
	  	
	  	while(odbc_fetch_row($not)){
			$email = trim(odbc_result($not, 1));
	     	//$to .= ", $email";	 			
		
		 	$mail->AddAddress($email);	 				
	  	}
	
	   	//ALTERADO HICOM 06/04/2004
	   	/*
	  	$headers_hc  = "MIME-Version: 1.0\r\n";
	  	$headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";
	  	*/
	  	/* additional headers */
	  	//$headers_hc .= "To: " . $to . "\r\n";
	  	//$headers_hc .= "From: credito@sbce.com.br\r\n";
	  	// mail($to, trim($exportador), $msgmail, $headers_hc);
	}
	
  	/*
    Alterado por Tiago V N - 08/08/2007
  	*/

  	$headers_hc  = "MIME-Version: 1.0\r\n";
  	$headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";
  	$headers_hc .= "From: credito@sbce.com.br\r\n";
  	//$to = "tvilanova.elumini@sbce.com.br";
  
  	//TESTADO POR WAGNER 26/08/2008
  	//TESTE DE VERIFICAÇÃO , NESTE TESTE FOI VERIFICADO SE O NOME DO PAIS VAI NO CORPO DO EMAIL
  	//echo "SEGURADO:".$msgmail;
  
  	//echo $msgmail;  
	$mail->AddAddress('siex@cofacedobrasil.com');
    $mail->AddAddress($to);
			 
	if($emailContact)
		$mail->AddAddress($emailContact); 
					 
	$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	$mail->Subject  = trim($exportador); // Assunto da mensagem
	$mail->Body =  $msgmail;
	$enviado = $mail->Send();   // envia o email
	$mail->ClearAllRecipients();
	$mail->ClearAttachments();
			
	// Exibe uma mensagem de resultado
	if ($enviado) {			
		$msg = "E-mail enviado com sucesso"; 
	} else {
		$msg = "Problemas no envio do e-mail".$mail->ErrorInfo;
	} 
  
  	//mail($to, trim($exportador), $msgmail, $headers_hc);
}

$state = 1;
$hold = ' AND hold = 0';
$union = '';
if($flag_renovacao){
  $union = "UNION SELECT Importer.id, Importer.name, Country.name, Country.code, ".
     "Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state ".
     "FROM Importer, Country ".
     "WHERE Importer.idInform = $idInform AND ".
     "Importer.idCountry = Country.id AND ".
     "Importer.state=6 AND Importer.creditAut=1 ";
}
$c = odbc_exec($db,
	       "SELECT Importer.id, Importer.name, Country.name, Country.code, ".
	       "Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId ".
	       "FROM Importer, Country ".
	       "WHERE Importer.idInform = $idInform AND ".
	       "Importer.idCountry = Country.id AND ".
	       "(Importer.state = '0' or Importer.state = $state) $hold ".
	       "AND (Importer.creditAut is null OR Importer.creditAut = 0) ".
	       "ORDER BY Importer.id");
if(! odbc_fetch_row($c)){
  $vazio1 = 1;
}

if($idAnt > 0){
  $c2 = odbc_exec($db,
		  "SELECT Importer.id, Importer.name, Country.name, Country.code, ".
		  "Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, ".
		  "Importer.state FROM Importer, Country ".
		  "WHERE Importer.idInform = $idInform AND ".
		  "Importer.idCountry = Country.id AND ".
		  "(Importer.state = '0' or Importer.state = $state) $hold ".
		  "AND Importer.creditAut = 1 $union".
		  "ORDER BY Importer.id");
  if(! odbc_fetch_row($c2)){
    $vazio2 = 1;
  }
  if($vazio1 && $vazio2){
    $vazio = 1;
  }else{
    $vazio = 0;
  }
}else{
  $vazio = $vazio1;
}

if($vazio){
  $notif->doneRole($idNotification, $db);
}
//Criado Por Tiago V N - 04/10/2005
//Log do Analise de Crédito ( Recusar Importador )
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('12'," .
           "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           "','".date("H").":".date("i").":".date("s")."')";
   if (odbc_exec($db, $sql) ) {
   		$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		$cur = odbc_result(odbc_exec($db, $sql_id), 1);
    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', 'Importador', '$name', 'Alteração')";
		$rs = odbc_exec($db, $sql);
		
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	   //CRIADO POR WAGNER
	   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   
	   if ($rs) {
	      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
	      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
	      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
	          "values ('$cur', '".str_replace("'","",$log_query)."')";
			  
			  //echo $sql;
	          odbc_exec($db, $sql);
	   }//fim if	
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		
		
		
   }else{
     $msg = "Erro no incluir do Log";
   }
?>
