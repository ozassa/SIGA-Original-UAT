<?php  //ALTERADO HICOM 06/04/2004
//Alterado HiCom mes 04


if($cobrar){
  $a = 1;
}else{
  $a = 0;
}

$x = odbc_exec($db, "select * from ChangeCredit where idImporter=$idBuyer order by id desc");
if(odbc_fetch_row($x)){
  $credit = odbc_result($x, 'credit');
  $analysis = odbc_result($x, 'analysis');
  $monitor = odbc_result($x, 'monitor');
  $creditDate = odbc_result($x, 'creditDate');
  $creditSolic = odbc_result($x, 'creditSolic');
  $creditTemp = odbc_result($x, 'creditTemp');
  $limTemp = odbc_result($x, 'limTemp');
/*
  odbc_exec($db,
	    "insert into ChangeCredit
             (idImporter, userIdChangeCredit, state, stateDate, monitor,
              analysis, creditDate, creditSolic, creditTemp".
	    ($limTemp ? ', limTemp' : '').
	    ") values ($idBuyer, $userID, 8, getdate(),
             $monitor, $analysis, '$creditDate', '$creditSolic', '$creditTemp'".
	    ($limTemp ? ", '$limTemp'" : ''). ")");
*/
}

$idNotification = $field->getField("idNotification");
$qQ = "UPDATE NotificationR SET state='2', i_Usuario = ".$_SESSION["userID"].", d_Encerramento = GETDATE() WHERE id='$idNotification'";
$cCur = odbc_exec($db, $qQ);

// envia email

 require_once("../MailSend.php"); 

 $mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
 $mail->FromName = "Credito"; // Seu nome 
 // Define os destinatário(s)

 
			 
$cot = odbc_exec($db, "SELECT emailContact, name, state, Litigio from Inform WHERE id = $idInform");
$emailContact = odbc_result($cot, 1);
$state = odbc_result($cot, 3);
$litigio = odbc_result($cot, 4);

if($emailContact){
  //$emailContact .= ", credito@sbce.com.br";
}else{
  $emailContact = "siex@cofacedobrasil.com";
}
$exportador = odbc_result($cot, 2); 

$ex = odbc_exec($db, "SELECT i.name, c.name, i.state FROM Importer i join Country c on i.idCountry=c.id WHERE i.id=$idBuyer"); 
$name = odbc_result($ex, 1);
$pais = odbc_result($ex, 2);
$state = odbc_result($ex, 3);

$msgmail = "Prezado Segurado,<br><br><br>\r\n";
$msgmail = $msgmail . "Informamos que sua Ficha de Aprovação de Limites de Crédito foi atualizada, havendo exclusão do seguinte importador:<ul><li>Importador: " . $name . "/" . $pais . "</ul>\r\n";
$msgmail = $msgmail . "<br><br>Para verificar sua Ficha de Aprovação de Limites de Crédito atualizada, consulte o site <a href=\"http://www.coface.com.br\">http://www.coface.com.br</a>\r\n";
$msgmail = $msgmail . "<br><br>Atenciosamente,\r\n";
$msgmail = $msgmail . "<br><br>Departamento de Crédito\r\n";

$query = "SELECT email FROM Contact WHERE idInform=$idInform AND notificationForChangeCredit=1";
$not = odbc_exec($db, $query);
$to = $emailContact;
while(odbc_fetch_row($not)){
  $email = trim(odbc_result($not, 1));
  //$to .= ", $email";
  
  $mail->AddAddress($email);
}
// envia o email
  $headers_hc  = "MIME-Version: 1.0\r\n";
  $headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";

  /* additional headers */
  //$headers_hc .= "To: " . $to . "\r\n";
  $headers_hc .= "From: credito@sbce.com.br\r\n";

//ALTERADO HICOM 06/04/2004
             $mail->AddAddress('siex@cofacedobrasil.com'));
			 
			 $mail->AddAddress(trim($to));
			 				
			 if($emailContact)
				 $mail->AddAddress($emailContact); 
					 
			 $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
			 $mail->Subject  = trim($exportador); // Assunto da mensagem
			 $mail->Body =  nl2br($msgmail);
        // verificação por status e litigio
        if($state == 10 && $litigo == 1){
          $enviado = $mail->Send();   // envia o email
        } else {
          $enviado = false;
        }
			 $mail->ClearAllRecipients();
			 $mail->ClearAttachments();
			
		 // Exibe uma mensagem de resultado
	 	 if ($enviado) {			
		     $msg = "E-mail enviado com sucesso";
		  
		 } else {
			  $msg = "Problemas no envio do e-mail".$mail->ErrorInfo;
		 } 
//mail(trim($to), trim($exportador), nl2br($msgmail). "",$headers_hc);

if($state == 9){
  $x = odbc_exec ($db, "DELETE FROM ChangeCredit WHERE idImporter=$idBuyer");
  $y = odbc_exec ($db, "DELETE FROM AnaliseImporter WHERE idImporter=$idBuyer");
  $z = odbc_exec ($db, "DELETE FROM Importer WHERE id=$idBuyer");
  if($x && $y && $z){
    $msg = 'Importador deletado da base';
  }else{
    $msg = 'Erro ao deletar importador';
  }
}
?>
