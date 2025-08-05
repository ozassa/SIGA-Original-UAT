<?php  /*
  Envia e-mail notificando o segurado que o limite temporário
  do seu importador esta se encerrando.
  
  Criado por Tiago V N - Elumini - 10/07/2006
  
*/

require_once("../../dbOpen.php");


if ( empty($idInform) Or empty($idBuyer) Or empty($tipo) ) {
   echo "Variavel Principal esta vazio !!!!";
}else{

if(! function_exists('ymd2dmy')){
  // converte a data de yyyy-mm-dd para dd/mm/yyyy
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

//envia email para o cliente e os contatos
$t = odbc_exec($db, "SELECT i.emailContact, i.name, i.state, i.Litigio from Inform i WHERE i.id = $idInform");
$emailContact = trim(odbc_result($t, 1));
$state = trim(odbc_result($t, 3));
$litigio = trim(odbc_result($t, 4));
//$emailContact = "tvilanova.elumini@sbce.com.br";

$nameCl = trim(odbc_result($t, 2));
$currency = odbc_result($t, "currency");
if ($currency == "2") {
    $extMoeda = "US$";
}else if ($currency == "6"){
    $extMoeda = "€";
}else if ($currency == "0") {
    $extMoeda = "US$";
}

/*
$x = odbc_exec($db, "SELECT i.name, c.name FROM Importer i join Country c on i.idCountry=c.id WHERE i.id = $idBuyer");
$name = odbc_result($x, 1);
$country = odbc_result($x, 2);
*/

$sql = "SELECT inf.contrat, imp.name, imp.c_Coface_Imp, imp.limCredit,
       c.name, ch.credit, imp.validityDate AS creditDate, imp.id,
       ch.creditTemp, ch.limTemp, c.code, c.name as pais
       FROM (SELECT idImporter, credit, creditDate, creditTemp,
       limTemp FROM ChangeCredit ch WHERE ch.id IN (SELECT MAX(id)
       FROM ChangeCredit GROUP BY idImporter)) ch RIGHT JOIN
       Importer imp ON (ch.idImporter = imp.id) JOIN Inform inf ON (imp.idInform = inf.id) JOIN
       Country c ON (imp.idCountry = c.id) WHERE imp.id = $idBuyer AND (imp.state = 6 OR
      ((imp.state = 2 OR imp.state = 4) AND NOT ch.credit IS NULL))";

$x = odbc_exec($db, $sql);

$name        = odbc_result($x, "name");
$country     = odbc_result($x, "pais");
$creditTemp  = odbc_result($x, "creditTemp");
$limTemp     = ymd2dmy(odbc_result($x, "limTemp"));
$credit      = odbc_result($x, "credit");


$ob = odbc_exec($db, "SELECT * FROM ImpComment WHERE idImporter = $idBuyer AND hide = 0");

    $msgEmail = '';

    $msgEmail = "<font class=texto><br>Prezado Segurado,\r\n";

    /*
      Variavel Tipo tem o seguinte descrição :
      1 - Quando o limite temporario faltar 15 dias para vencer;
      2 - Quando o limite temporario já tiver vencido após 1 dia;
    */
    if ($tipo=="1") {
        $msgEmail = $msgEmail . "<br><br><br>Informamos que o limite de crédito temporário para o importador abaixo, expira em $limTemp\r\n";
    }else if ($tipo=="2") {
      //  $msgEmail = $msgEmail . "<br><br><br>Informamos que o limite de crédito temporário, Vigênte até $limTemp foi expirado para  o seguinte importador.\r\n";
    }
    $msgEmail = $msgEmail . "<br><br><ul><li>Importador: $name / $country </li>\r\n";
    $msgEmail = $msgEmail . "<li>Limite de crédito: ".$extMoeda. number_format($credit / 1000, 0). " Mil</li>\r\n";

    if($creditTemp > 0)
    {

       $msgEmail = $msgEmail . "<li>Limite de crédito temporário: ".$extMoeda. number_format($creditTemp / 1000, 0). " Mil</li>\r\n";

    }

       $i=1;
       while (odbc_fetch_row($ob)) {
           if($i==1) { $msgEmail = $msgEmail . "<li>Observação : " . odbc_result($ob, "comment") . "<br>"."</li>\r\n"; };
           $i=$i+1;
       }


	$msgEmail = $msgEmail . "</ul>\r\n";
    $msgEmail = $msgEmail . "<br><br>Para verificar o limite de crédito concedido, consulte o site <a href=\"http://www.coface.com.br\">http://www.coface.com.br</a><br><br><br> Atenciosamente, <br><br><br> Departamento de Crédito";

    $msgEmail = $msgEmail . "<br><br><br><br><center><b>Esta é uma mensagem automática, favor não responder para este e-mail.</b></center></font><br><br><br>";

   // $msgEmail = $msgEmail . "<br><br><br><fonte color='red'>Esta msg é de teste para o limite de crédito Temporário  !!!!</font>";

//$to = "tvilanova.elumini@sbce.com.br";
//$to = "credito@sbce.com.br,tvilanova.elumini@sbce.com.br,msaddock.elumini@sbce.com.br";
$to = "siex@cofacedobrasil.com";

 require_once("../MailSend.php"); 
		 
			 if(trim($email)==""){
				$email = "siex@cofacedobrasil.com";
			 }
			 
			 $mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
			 $mail->FromName = "Credito"; // Seu nome 
			 // Define os destinatário(s)
			
			 $mail->AddAddress(trim($to));
			 

if($emailContact)
{
  //$to .= ", $emailContact";
}

$query = "SELECT email FROM  Contact  WHERE idInform = $idInform AND notificationForChangeCredit = 1";
$not = odbc_exec($db, $query);
while(odbc_fetch_row($not))
{
  $email = trim(odbc_result($not, 1));
  //$to .= ", $email";
  $mail->AddAddress($email);
  
//  $email = "tvilanova.elumini@sbce.com.br";
//  $to .= ", tvilanova.elumini@sbce.com.br";

}

 // $to = "";
  //$to = "tvilanova.elumini@sbce.com.br";
  
$headers_hc  = "MIME-Version: 1.0\r\n";
$headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";

$headers_hc .= "From: credito@sbce.com.br\r\n";

echo "Para:" . trim($to) . "<br>Assunto:" .  trim($nameCl) . "<br>Texto:<br>" . $msgEmail . "<br>Header:" . $headers_hc . "<br><br>";

           
			 				
			 if($emailContact)
				 $mail->AddAddress($emailContact); 
					 
			 $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
			 $mail->Subject  = trim($nameCl); // Assunto da mensagem
			 $mail->Body =  $msgEmail;
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

} // Verificação das variavel inicial
?>
