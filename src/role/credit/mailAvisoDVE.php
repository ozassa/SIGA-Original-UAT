<?php  //Alterado HiCom mes 04

require_once("../../dbOpen.php");

include_once("../consultaCoface.php");

//envia email para o cliente e os contatos
$t = odbc_exec($db, "SELECT i.emailContact, i.name from Inform i WHERE i.id = $idInform");
$emailContact = trim(odbc_result($t, 1));
$nameCl = trim(odbc_result($t, 2));

$msgEmail = '';
  
$msgEmail = "<font class=texto><br>Prezados Senhores,\r\n";
$msgEmail = $msgEmail . "<br><br><br>Informamos que a Declaração de Volume de Exportação referente ao período de " . $inicio . " à " . $dt_fim . " deverá ser enviada à SBCE até o dia 15/" . $mesano . ".\r\n"; 
$msgEmail = $msgEmail . "<br>Qualquer dúvida no preenchimento desta declaração entrar em contato com o telefone (011)5509-8181.\r\n";
$msgEmail = $msgEmail . "<br><br>Atenciosamente, \r\n";
$msgEmail = $msgEmail . "<br>".$nomeEmpSBCE."\r\n";
  

//$to = "mferraz@sbce.com.br";
//$to = "mvilela@sbce.com.br, andreaw@sbce.com.br";

if($emailContact)
{
  //$to .= ", $emailContact";
}

require_once("../MailSend.php"); 
 
$query = "SELECT email FROM  Contact  WHERE idInform = $idInform AND notificationForChangeCredit = 1";
$not = odbc_exec($db, $query);
while(odbc_fetch_row($not))
{
  $email = trim(odbc_result($not, 1));
  //$to .= ", $email";
  $mail->AddAddress($email);
}
  
$headers_hc  = "MIME-Version: 1.0\r\n";
$headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";

$headers_hc .= "From: credito@sbce.com.br\r\n";
  
echo "Para:" . trim($to) . "<br>Assunto:" .  trim($nameCl) . "<br>Texto:<br>" . $msgEmail . "<br>Header:" . $headers_hc . "<br><br>";

     
		 
			 if(trim($email)==""){
				$email = "siex@cofacedobrasil.com";
				$mail->AddAddress($email);
			 }
			 
			 $mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
			 $mail->FromName = "Credito"; // Seu nome 
			 // Define os destinatário(s)
			
				 
			 				
			 if($emailContact)
				 $mail->AddAddress($emailContact); 
					 
			 $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
			 $mail->Subject  = trim($nameCl); // Assunto da mensagem
			 $mail->Body =  $msgEmail;
			 $enviado = $mail->Send();   // envia o email
			 $mail->ClearAllRecipients();
			 $mail->ClearAttachments();
			
		 // Exibe uma mensagem de resultado
	 	 if ($enviado) {			
		     $msg = "E-mail enviado com sucesso";
		  
		 } else {
			  $msg = "Problemas no envio do e-mail".$mail->ErrorInfo;
		 }

    /*
	if (!mail(trim($to), trim($nameCl), $msgEmail, $headers_hc)) 
	{
	   echo "NOK! Problemas no envio do e-mail para o cliente SBCE";
	}
	else
	{
	   echo  "OK!";  
	}
	*/

?>
