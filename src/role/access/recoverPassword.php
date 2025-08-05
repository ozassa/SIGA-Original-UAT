<?php
// Alterado Hicom (Gustavo) - 19/01/05 - login não é mais um e-mail obrigatoriamente, sendo assim 
// o destinatário do e-mail deve ser lido do informe

/*
$sql = "	SELECT 		u.password, inf.emailContact, inf.id
      		FROM 		Inform inf, Insured ins, Users u
      		WHERE 		u.id = ins.idResp
      					and ins.id = inf.idInsured
      					and inf.state <> 9
						and isnull(u.state,0) <> 1
      					and u.login = '".$field->getField("login")."' 
      		ORDER BY inf.id DESC ";
*/

/*
Alterado por Tiago V N - Data - 16/08/2005.
Verificacao do campo login e email para envio da senha.
*/
//$sql = "	SELECT password FROM Users WHERE login = '".$field->getField("login")."'";
/*
$sql = "SELECT password FROM Users " .
       "WHERE login = '".$field->getField("login")."' " .
       "Or email = '".$field->getField("login")."'";

$cur = odbc_exec($db,$sql);

if (odbc_fetch_row($cur)) {
//  if (mail(odbc_result($cur, "emailContact"),
  $headers = '';
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
  $headers .= "From: atendimento@sbce.com.br\r\n";
  if (mail($field->getField("login"),
	   "Sua Senha SBCE",
	   "Prezado cliente, \n\n Esta é a sua senha de acesso : ".odbc_result($cur, 1). "\n\n", $headers)){
    $msg = "E-mail enviado com sucesso";
  }else{
    $msg = "Problemas no envio do e-mail";
    $forward = "error";
  }
  //    mail ("eduardo@tendencies.com.br", "Sua Senha SBCE", "Prezado cliente,  esta é a sua senha de acesso : 123");
} else {
    $forward = "error";
     //  $msg = "Este login não consta em nossos cadastros";
    $msg = "Este e-mail não consta em nossos cadastros";
}
*/

		$headers = '';


  		$headers .= "MIME-Version: 1.0\r\n";


	    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";


        $headers .= "From: atendimento@sbce.com.br\r\n";

        $assunto = "Sua Senha SBCE";
        $menssagem = "Prezado cliente, \n\n Esta é a sua senha de acesso : ".odbc_result($cur, 1). "\n\n";
        
        $sql = "SELECT login, email, password FROM Users " .
               "WHERE login = '".$field->getField("login")."' " .
               "Or email = '".$field->getField("login")."'";

        $cur = odbc_exec($db,$sql);
        odbc_fetch_row($cur);
        if (odbc_num_rows($cur)) {
                include_once("../consultaCoface.php");
   
                $assunto = "Sua senha ".$nomeEmp;
                $mensagem = "Prezado cliente,<br><br> Esta é a sua senha de acesso : ".odbc_result($cur, 3). "<br><br>";

                if (odbc_result($cur, "login") == "") {
                   $email = odbc_result($cur, "email");
                }else{
                   $email = odbc_result($cur, "login");
                }

              //smoreira@braspack.com.br
			   
			     require_once("../MailSend.php"); 
		 
				
				 
				 $mail->From = "atendimento@coface.com"; // Seu e-mail
				 $mail->FromName = "Atendimento"; // Seu nome 
				 // Define os destinatário(s)
				
				 
				 $mail->AddAddress($email);
				
				 $mail->IsHTML(false); // Define que o e-mail será enviado como HTML
				 $mail->Subject  = $assunto; // Assunto da mensagem
				 $mail->Body = $mensagem;
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
                mail($email, $assunto, $mensagem, $headers);
                $msg = "E-mail enviado com sucesso";
				
				*/
        }else{
                $forward = "error";
                $msg = "Este e-mail não consta em nossos cadastros";
        }
?>
