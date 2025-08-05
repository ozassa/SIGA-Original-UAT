<?php // Criado Hicom (Gustavo) 25/01/2005 - envia e-mail informando a opção de divulgar nome ao importador

//extract($_SESSION);

$hc_cur = odbc_exec($db, "select name, emailContact, state, Litigio from Inform where id = ".$idInform);
$hc_name = odbc_result($hc_cur, "name");
$hc_email = odbc_result($hc_cur, "emailContact");
$state = odbc_result($hc_cur, "state");
$litigio = odbc_result($hc_cur, "Litigio");

$hc_cur = odbc_exec($db, "select name from Importer where id = ".$idImporter);
$hc_importerName = odbc_result($hc_cur, "name");

include_once("../consultaCoface.php");

$msgMail =
"Prezado Segurado,

Informamos que nos contatos feitos junto ao importador ".$hc_importerName.", informaremos 
o nome ".$hc_name." conforme opção feita no cadastro de importadores.

Permanecemos à disposição no que for necessário.


Atenciosamente,


Departamento de Operações de Curto Prazo
".$nomeEmpSBCE."
Homepage: ".$siteEmpSBCE."

Rio de Janeiro
Rua Senador Dantas, 74 - 16º andar
Centro - Rio de Janeiro - RJ - 20031.205
Telefone: (21) 2510.5000

São Paulo
Pça. João Duran Alonso, 34 - 12º andar
Brooklin Novo - SP - 04571-070
Tel.: (11) 5509 8181
Fax.: (11) 5509 8182\n\n";

//$hc_cur = odbc_exec($db, "select email from Users where id = .$userID");

if($hc_email) {
//	if ($hc_email != odbc_result($hc_cur, "email") && odbc_result($hc_cur, "email")) {
//		$hc_email .= ", ".odbc_result($hc_cur, "email");
//	}
	//$hc_email .= ", credito-brasil@coface.com"; // cópia para o sbce
	//if (mail($hc_email, $hc_name, $msgMail, "From: desenvolvimento@sbce.com.br")) {
		
	 require_once("../MailSend.php"); 
		 
	 if(trim($email)==""){
		$email = "credito-brasil@coface.com";
	 }
	 
	 $mail->From = "credito-brasil@coface.com"; // Seu e-mail
	 $mail->FromName = "Credito"; // Seu nome 
	 // Define os destinatário(s)
	
	 $mail->AddAddress($hc_email);
	 $mail->AddAddress("credito-brasil@coface.com");
	 
	 $mail->IsHTML(false); // Define que o e-mail será enviado como HTML
	 $mail->Subject  = $hc_name; // Assunto da mensagem
	 $mail->Body =  $msgMail;
	 if ($stateExp == 10 && $litigio == 1) {
	 	$enviado = $mail->Send();   // envia o email
	 } else {
	 	$enviado = false;
	 }
	 $mail->ClearAllRecipients();
	 $mail->ClearAttachments();
	
	 // Exibe uma mensagem de resultado
	 if ($enviado) {			
		 $hc_result = "OK";
	  
	 } else {
		  $hc_result = "Problemas no envio do e-mail" ;
	 }	
	/*	
    if (mail($hc_email, $hc_name, $msgMail, "From: credito@sbce.com.br")) {
		$hc_result = "OK";
	}
	else {
		$hc_result = "Problemas no envio do e-mail" ;
	}
	*/
}
else {
	$hc_result = "Email do contato não encontrado";
}

?>
