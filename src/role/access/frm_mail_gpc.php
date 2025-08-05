<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Mail</title>
  </head>
<body>
<?php
/* recipients */

$to  = "";
$from  = "";
$cc = "";
$subject ="";
$erro = "";

if ($frm_ok == "OK") 
{

   $to  = "credito@sbce.com.br" . ", " . $frm_para;
   $from  = $frm_de;
   $cc = $frm_cc;

   /* subject */
   $subject = $frm_assunto;

   /* message */
   $message = '
   <html>
   <head>
   <title>Birthday Reminders for August</title>
   </head>
   <body>
   <p>Here are the birthdays upcoming in August!</p>
   <table>
   <tr>
   <th>Person</th><th>Day</th><th>Month</th><th>Year</th>
   </tr>
   <tr>
   <td>Joe</td><td>3rd</td><td>August</td><td>1970</td>
   </tr>
   <tr>
   <td>Sally</td><td>17th</td><td>August</td><td>1973</td>
   </tr>
   </table>
  </body>
  </html>
  ';
  
  $message = '
  <font class=texto><br>Prezado Segurado (MENSAGEM DE TESTE DE ENVIO DE EMAIL!),
  <br><br><br>Informamos que sua Ficha de Aprovação de Limites de Crédito foi atualizada, havendo alteração no montante concedido ao seguinte importador:
  <br><br><ul><li>Importador: teste imp / teste pais </li><li>Limite de crédito: US$ 0,00 Mil</li></ul> <br>
  Caso haja alguma exportação em curso,  favor entrar em contato no prazo máximo de oito dias. <br><br>
  Para verificar o limite de crédito concedido, consulte o site <a href=\"http://www.sbce.com.br\">http://www.sbce.com.br</a><br><br><br> Atenciosamente, <br><br><br> Departamento de Crédito </font>';
  
  
  /* To send HTML mail, you can set the Content-type header. */
  $headers  = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

  /* additional headers */
  $headers .= "To: " . $to . "\r\n";
  $headers .= "From: " . $from . "\r\n";
  if ($cc != "")
  { 
     $headers .= "Cc: " . $cc . "\r\n";
  } 	 
  $headers .= "Bcc: tvilanova.elumini@sbce.com.br\r\n"; <!-- Alterado por Fábio Benevides 25/07/2005 (gpc@hi.com.br)-->

  /* and now mail it */
  if (!mail($to, $subject, $message, $headers))
  {
     $erro = "e-mail NÃO enviado!";
  }
  
  
  echo "TO->" . $to . "<BR><BR>";
  echo "subject->" . $subject . "<BR><BR>";
  echo "message->" . $message . "<BR><BR>";
  echo "headers->" . $headers . "<BR><BR>";
  echo "<BR><STRONG>" . $erro . "</STRONG><BR>";
  
}
  
?> 


<!-- -------------------------Formulário de envio de e-mail---------------------------------------- -->
<form name="FRM1" method="post" action="frm_mail_gpc.php">
  <input type="hidden" name="frm_ok" value="OK">
  <table width="60%" border="0" cellspacing="0">
    <tr> 
       <td width="13%">De:</td>
       <td width="87%"><input name="frm_de" type="text" size="100" maxlength="100" value="<?php echo $from;?>"></td>
    </tr>
    
	<tr> 
       <td>Para:</td>
       <td><input name="frm_para" type="text" size="100" maxlength="100" value="<?php echo $frm_para;?>"></td>
    </tr>
    
	<tr> 
      <td>Cc:</td>
      <td><input name="frm_cc" type="text" size="100" maxlength="100" value="<?php echo $cc;?>"></td>
    </tr>
  
    <tr> 
      <td>Assunto:</td>
      <td><input name="frm_assunto" type="text" size="50" maxlength="50" value="<?php echo $subject;?>"></td>
    </tr>
   
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <!-- 
	
    -->
    <tr>
      <td colspan="2">
	     <input type="submit" name="enviar" value="Enviar">
	  </td>
    </tr> 
</table> 
</form> 
</body>
</html>
