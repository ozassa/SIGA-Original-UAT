<?php  //ALTERADO HICOM 12/04/2004
//Alterado HiCom mes 04

$idBuyer  = $field->getField("idBuyer");
$idInform = $field->getField("idInform");
$origem   = $field->getField("origem");
$insert   = $field->getField("insert");
$remove   = $field->getField("remove");
$update   = $field->getField("update");

$novo_endereco = strtoupper(trim($field->getField("novo_endereco")));
$nova_cidade   = strtoupper(trim($field->getField("nova_cidade")));
$novo_telefone = strtoupper(trim($field->getField("novo_telefone")));
$novo_cep      = strtoupper(trim($field->getField("novo_cep")));

$frm_env_mail   = strtoupper(trim($field->getField("frm_env_mail")));
$frm_nome = $field->getField("frm_nome");

if($insert){
  if(! $novo_endereco || ! $nova_cidade || ! $novo_telefone || ! $novo_cep){
    $alert = 1;
  }else{
    $auxdesc = "incluido";
    $r = odbc_exec($db,
		   "insert into ImporterAddress (idImporter, address, city, tel, cep) values ".
		   "($idBuyer, '$novo_endereco', '$nova_cidade', '$novo_telefone','$novo_cep')");
    if(!$r){
      $msg = "Problemas ao inserir endereço";
    }
  }
}

if($update){
  if(! $novo_endereco || ! $nova_cidade){
    $alert = 1;
  }else{
    $auxdesc = "alterado";
    $r = odbc_exec($db,
		   "update ImporterAddress set address='$novo_endereco', cep='$novo_cep', city='$nova_cidade', ".
		   "tel='$novo_telefone'  where id=". $field->getField("id"));
    if(!$r){
      $msg = "Problemas ao atualizar endereço";
    }
  }
}

if($update || $insert ) 
{

   //echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>";
   if(($frm_env_mail=="TRUE" || $frm_env_mail=="ON") && $alert != 1)
   {
       //echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
   
      $msgEmail = "<font class=texto><br>Prezado Segurado,\r\n";
      $msgEmail = $msgEmail . "<br><br><br>Informamos que " . " foi " . $auxdesc . " um endereço adicional do importador: \r\n"; 
      $msgEmail = $msgEmail . "<br><br><ul><li>" . $frm_nome  . ".</li></ul>\r\n";
		
	  $msgEmail = $msgEmail . "<br>Telefone: "  . $novo_telefone . "\r\n";
	  $msgEmail = $msgEmail . "<br>Endereço: "  . $novo_endereco . "\r\n";
	  $msgEmail = $msgEmail . "<br>Cidade: "  .  $nova_cidade . "\r\n";
	  $msgEmail = $msgEmail . "<br>CEP: "  . $novo_cep . "\r\n";
		  
	  $msgEmail = $msgEmail . "</br><br>Para verificar os dados, consulte o site <a href=\"http://www.coface.com.br\">http://www.coface.com.br</a><br><br><br> Atenciosamente, <br><br><br> Departamento de Crédito </font>\r\n";
		
	      //envia email para o cliente e os contatos
          $t = odbc_exec($db, "SELECT i.emailContact, i.name , i.state, i.Ligitio from Inform i WHERE i.id = " . $idInform);
          $emailContact = trim(odbc_result($t, 1));
          $nameCl = trim(odbc_result($t, 2));
          $state = odbc_result($exec, 3);
		  $litigio = odbc_result($exec, 4);
	
          $to = "siex@cofacedobrasil.com";
          if($emailContact){
            //$to .= ", " . $emailContact;
          }
		 
		  require_once("../MailSend.php"); 
		  
		  $mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
		  $mail->FromName = "Credito"; // Seu nome 
			 // Define os destinatário(s)
		  
          $query = "SELECT email FROM  Contact  WHERE idInform = " . $idInform . " AND notificationForChangeCredit = 1 ";
          $not = odbc_exec($db, $query);
          while(odbc_fetch_row($not)){
            $email = trim(odbc_result($not, 1));
            //$to .= ", $email";
			//$to = $email;
			$mail->AddAddress($email);
          }	
		

          $headers_hc  = "MIME-Version: 1.0\r\n";
          $headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";

          $headers_hc .= "From: credito@sbce.com.br\r\n";
		  
		 
		 
			 if(trim($email)==""){
				$email = "siex@cofacedobrasil.com";
				$mail->AddAddress($email);
			 }
			 
			
			
			 $mail->AddAddress(trim($to));
			 
			 				
			 if($emailContact)
				 $mail->AddAddress($emailContact); 
					 
			 $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
			 $mail->Subject  = trim($nameCl); // Assunto da mensagem
			 $mail->Body =  $msgEmail;

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
		  
		  /*
          if (!mail(trim($to), trim($nameCl), $msgEmail, $headers_hc)) {
              $msg = "Problemas no envio do e-mail para o cliente SBCE";
          }	
         
		  */
   
   
   }
}

// if($remove){
//   $r = odbc_exec($db, "delete from ImporterAddress where id=". $field->getField("id"));
//   if(!$r){
//     $msg = "Problemas ao remover endereço";
//   }
// }

if($inativa){
  $r = odbc_exec($db, "update ImporterAddress set state=2, inativeDate=getdate() where id=". $field->getField("id"));
  if(!$r){
    $msg = "Problemas ao desativar endereço";
  }
}

$res = odbc_exec($db, "select id, address, city, tel, cep, state, inativeDate from ImporterAddress where idImporter=$idBuyer");
if(! odbc_fetch_row($res)){
  $nao_tem = 1;
}

?>
