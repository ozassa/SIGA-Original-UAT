<?php  // Alterado Hicom (Gustavo) - 16/12/04 - cancelamento da proposta no Sisseg (!por_email)
// Alterado Hicom (Gustavo) - 19/01/05 - login não é mais um e-mail obrigatoriamente, sendo assim 
// o destinatário do e-mail foi alterado
$log_query = "";
if($por_email){ // reenvia a proposta por email
  $downProp = $key. 'PropSegVia.pdf';
  $downParc = $key. 'ParcSegVia.pdf';
  require_once("propMail.php");
  $cc = odbc_exec($db,
		  "SELECT u.login, u.email FROM (Users u JOIN Insured i ON (u.id = i.idResp)) ".
		  "JOIN Inform inf ON (inf.idInsured = i.id)WHERE inf.id = $idInform");
  if(odbc_fetch_row($cc)){
    $login = odbc_result($cc, 1);
    $hc_mail = odbc_result($cc, 2); // E-mail
  }
  
  if ($hc_mail != "") {
  	 //$hcemail = $hc_mail.", ".$login;
  }else{
  	 $hcemail = $login;
  }

  $x = odbc_exec($db, "select u.email from Inform i join Users u on i.idUser=u.id where i.id=$idInform");
  $email = odbc_result($x, 1);

  $hcemail .= ", ".$email;
  
  /*
  if(mail($hcemail, "Proposta SBCE (segunda via)", $msgMail, "From: $email")) {
    $msg = 'Proposta reenviada';
  }else{
    $msg = 'Problemas no envio do email';
  }
  */
  
  require_once("../MailSend.php"); 
		 
	 if(trim($email)==""){
		$email = "siex@cofacedobrasil.com";
	 }
	 
	 $mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
	 $mail->FromName = "Credito"; // Seu nome 
	 // Define os destinatário(s)
	
	 
	 $mail->AddAddress($email);
	 $mail->AddAddress($login);
	 
	 if ($hc_mail != "")
	     $mail->AddAddress($hc_mail);
		
	 if($emailContact){
		 $mail->AddAddress($emailContact); 
	 }
	 $mail->IsHTML(false); // Define que o e-mail será enviado como HTML
	 $mail->Subject  = "Oferta SBCE"; // Assunto da mensagem
	 $mail->Body = $msg;
	 $enviado = false;
	 $mail->ClearAllRecipients();
	 $mail->ClearAttachments();
		
	 // Exibe uma mensagem de resultado
	 if ($enviado) {			
		 $msg = "E-mail enviado com sucesso";
		 odbc_commit ($db);
		 $forward = "success";
	 } else {
		  $msg = "Problemas no envio do e-mail".$mail->ErrorInfo;
	 }
  
  
}else{ //cancelamento

$cur = odbc_exec($db, "Select * from Inform Where id = $idInform");

$i_Seg = odbc_result($cur, "i_Seg");
$nProp = odbc_result($cur, "nProp");


// Alterado Hicom (Gustavo)	- 16/12/04 - esse procedimento não estava cancelando no Sisseg
// antes:
//  $x = odbc_exec($db, "select idRegion, name from Inform where id=$idInform");
//  $idRegion = odbc_result($x, 1);
//  $name = odbc_result($x, 2);

//  $x = odbc_exec($db, "update Inform set state=9 where id=$idInform");
//  //$notif->waitOffer($userID, $idRegion, $name, $idInform, $db);
	
  $rs = odbc_exec($dbSisSeg, "Select * from PagRec Where i_Seg=$i_Seg and n_Prop=$nProp");

  if (odbc_result($rs, "s_Pagamento")== 1) {	
  $ok = true;
  odbc_autocommit($db, false);
  odbc_autocommit($dbSisSeg, false);
	
  $x = odbc_exec($db, "select nProp, i_Seg, idRegion, name from Inform where id=$idInform");
  $idRegion = odbc_result($x, "idRegion");
  $name = odbc_result($x, "name");
  $nProp = odbc_result($x, "nProp");
  $i_Seg = odbc_result($x, "i_Seg");

  $x = odbc_exec($db, "update Inform set state=9 where id=$idInform");
  //$notif->waitOffer($userID, $idRegion, $name, $idInform, $db);
  
	//criado por Wagner 29/08/2008
	if($x)
	{
		$log_query .= "update Inform set state=9 where id=$idInform";
	}
 
  if(!$x){
    $msg = 'Erro ao atualizar Informe';
    $ok = false;
  }
  else {
  // cancela tb no SisSeg
    $x = odbc_exec($dbSisSeg, "update Proposta set s_Proposta=7, d_Situacao=getdate() where i_Seg=$i_Seg and n_Prop=$nProp");
	
		//criado por Wagner 29/08/2008
		if($x)
		{
			$log_query .= "update Proposta set s_Proposta=7, d_Situacao=getdate() where i_Seg=$i_Seg and n_Prop=$nProp";
		}
	
    $y = odbc_exec($dbSisSeg, "update Parcela set s_Parcela=3, d_Cancelamento=getdate() where i_Seg=$i_Seg and n_Prop=$nProp");
		//criado por Wagner 29/08/2008
		if($y)
		{
			$log_query .= "update Parcela set s_Parcela=3, d_Cancelamento=getdate() where i_Seg=$i_Seg and n_Prop=$nProp";
		}
	
    $z = odbc_exec($dbSisSeg, "update PagRec set s_Pagamento=3, d_Situacao=getdate() where i_Seg=$i_Seg and n_Prop=$nProp");
		//criado por Wagner 29/08/2008
		if($z)
		{
			$log_query .= "update PagRec set s_Pagamento=3, d_Situacao=getdate() where i_Seg=$i_Seg and n_Prop=$nProp";
		}	
		
    if(! ($x && $y && $z)){
      $msg = 'Erro ao cancelar proposta no SisSeg<br>'. odbc_errormsg();
      $ok = false;
    }
  }

  //Criado Por Tiago V N - 15/03/2006
 //Log de Cancelamento de Informe e cancelamento de proposta (Cancelamento de Proposta)
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('29'," .
           "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           "','".date("H").":".date("i").":".date("s")."')";
   if (odbc_exec($db, $sql) ) {
   		$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		$cur = odbc_result(odbc_exec($db, $sql_id), 1);
    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', 'status', '9', 'Envio para Cancelamento de Informe')";
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

  if($ok){
    odbc_commit($db);
    odbc_commit($dbSisSeg);
  }
  else {
    odbc_rollback($db);
    odbc_rollback($dbSisSeg);
  }
  odbc_autocommit($db, true);
  odbc_autocommit($dbSisSeg, true);
  
  }else{
  	 $msg = "Você não pode cancelar esta proposta, pois ela já foi paga.";  
  }  
}
?>
