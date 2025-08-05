<?php
/*
 * Created on 07/08/2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * Envio de e-mail para notificar o executivo que 
 * já passou 30 dias da emissão da proposta.
 */
 
require_once("../../dbOpen.php");
  	
  	
  	
   $strSQL = "SELECT  dbo.Inform.name as cliente, dbo.Users.name as executivo, dbo.Users.email, dbo.Inform.dateEmission
			 FROM    dbo.Inform INNER JOIN
        	 dbo.Users ON dbo.Inform.idUser = dbo.Users.id
			 WHERE   (dbo.Users.state = 0) AND (dbo.Users.perfil = 'F') AND (dbo.Inform.state = 6) 
			 AND dbo.Inform.id = $idInform";	 
 
   
   $cur   = odbc_exec($db, $strSQL);
   $nome  = odbc_result($cur, "cliente");
  
   $email = odbc_result($cur, "email");
   //$email .= ",tvilanova.elumini@sbce.com.br";
   	
   $headers_hc  = "MIME-Version: 1.0\r\n";
   $headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";
   $headers_hc .= "From: financeiro@sbce.com.br\r\n";
  
  
   $msgmail = "Prezado Executivo,<br><br>A proposta de seguro de crédito à exportação " .
  			 "da ($nome) foi enviada há 30 dias.<br><br><br>Atenciosamente,<br><br>Departamento Financeiro<br>";

    require("../MailSend.php"); 
		 								 
	$mail->From = "financeiro@sbce.com.br"; // Seu e-mail
	$mail->FromName = "Financeiro"; // Seu nome 
	// Define os destinatário(s)
	
	
	$mail->AddAddress($email);
	
	$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	$mail->Subject  = "Financeiro"; // Assunto da mensagem
	$mail->Body = $msgmail;
	$enviado = $mail->Send();   // envia o email
	$mail->ClearAllRecipients();
	$mail->ClearAttachments();
	
	// Exibe uma mensagem de resultado
	if ($enviado) {			
	    echo "e-mail enviado com sucesso.";
	    
	} else {
	    echo "Problemas no envio do e-mail".$mail->ErrorInfo;
	} 
  /*
  if (mail($email, "Financeiro", $msgmail, $headers_hc)) {
     echo "e-mail enviado com sucesso.";
  }else{
     echo "erro no envio de e-mail.";
  }
  */
   
?>
