<?php  //Alterado HiCom mes 04

require_once("../../dbOpen.php");
require_once("../../entity/notification/Notification.php");
$notif = new Notification ();

if(! function_exists('ymd2dmy')){
  function ymd2dmy($d){
    if(preg_match("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}


 require_once("../MailSend.php"); 
 include_once("../consultaCoface.php");	

 $mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
 $mail->FromName = "Credito"; // Seu nome 
 // Define os destinatário(s)

 
 
// seleciona os usuários DVE
$wsql = " SELECT US.email";
$wsql = $wsql . " FROM Users US, UserRole UR ";
$wsql = $wsql . " WHERE UR.idRole = 16 "; //'DVE
$wsql = $wsql . " AND UR.idUser = US.id AND US.state = 0 AND perfil = 'F'  ";

$to = "";
//$to = "credito@sbce.com.br";

$not = odbc_exec($db, $wsql);
while(odbc_fetch_row($not))
{
  $email = trim(odbc_result($not, 1));
  
  if (trim($email) != ""){
	 $mail->AddAddress($email);
   
  }	
}

if ($to == "")
{
   $to = "siex@cofacedobrasil.com";
}

// Obtem dados do inform
$t = odbc_exec($db, "SELECT inf.id, inf.contrat, inf.i_Seg, inf.nProp, inf.name, inf.cnpj, inf.startValidity, inf.endValidity, inf.emailContact, inf.contact, inf.email from Inform inf WHERE inf.id = $idInform");
$hc_infName = trim(odbc_result($t, "name"));
$hc_startValidity = ymd2dmy(trim(odbc_result($t, "startValidity")));
$hc_endValidity = ymd2dmy(trim(odbc_result($t, "endValidity")));
$hc_i_Seg = trim(odbc_result($t, "i_Seg"));
$hc_n_Prop = trim(odbc_result($t, "nProp"));
$hc_c_Coface = trim(odbc_result($t, "contrat"));


//echo $hc_infName . "<br>";

// Obtem o número da apólice
$achou=false;
$t = odbc_exec($dbSisSeg, "SELECT n_Apolice from Base_Calculo  WHERE c_Coface =$hc_c_Coface and i_Seg =$hc_i_Seg and n_Prop =$hc_n_Prop and t_Apolice = 0 order by i_BC desc ");
if (!odbc_fetch_row($t))
{
   $t = odbc_exec($dbSisSeg, "SELECT n_Apolice from Base_Calculo  WHERE c_Coface =$hc_c_Coface and i_Seg =$hc_i_Seg and n_Prop =$hc_n_Prop  order by i_BC desc ");
   if (!odbc_fetch_row($t))
   {
      $t = odbc_exec($dbSisSeg, "SELECT n_Apolice from Base_Calculo  WHERE c_Coface =$hc_c_Coface and i_Seg =$hc_i_Seg  order by i_BC desc  ");
	  if (odbc_fetch_row($t))
      {
	    $achou=true;  
	  }
   }
   else
   {
      $achou=true;  
   }
}
else
{
   $achou=true;  
}

if($achou)
{
   $hc_n_Apolice = trim(odbc_result($t, "n_Apolice"));
}
else
{
   $hc_n_Apolice = 0;
}

//echo $hc_n_Apolice . "<br>";

//Inclui em tabela de atrazo envio DVE



//

//Gera notificaçao
$msg = "";
$hc_r = $notif->semDVE16(-1, $hc_infName, $idInform, $db, 16, $num, $idDVE );
if (!$hc_r) 
{
  echo "<BR>A notificação NÃO foi encaminhada aos usuários DVE! " . $hc_r; 
	    
}
else
{
  echo "<BR>A notificação foi encaminhada aos usuários DVE! " . $hc_r;
}


if ($hc_r) 
{

   $msgEmail = '';
   $msgEmail = "<font class=texto><br>Usuários responsáveis pela DVE,\r\n";
   $msgEmail = $msgEmail . "<br><br><br>O Segurado " . $hc_infName . ", Apólice nº " . $hc_n_Apolice . ",vigência de " . $hc_startValidity . " até " . $hc_endValidity . " está com a entrega da " . $num . " DVE (período " . $inicio . " à " . $dt_fim . "), vencida.\r\n"; 
   $msgEmail = $msgEmail . "<br><br>Atenciosamente, \r\n"; 
   $msgEmail = $msgEmail . "<br><br>".$nomeEmpSBCE;
  

   //$to = "credito@sbce.com.br";
   //$to .= ", andreaw@sbce.com.br";
  
   $headers_hc  = "MIME-Version: 1.0\r\n";
   $headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";

   $headers_hc .= "From: credito@sbce.com.br\r\n";

   $nameCl = "DVE (" . $hc_infName . ")";
  
           
		     $mail->AddAddress(trim($to));
						 				
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
      echo "NOK! Problemas no envio do e-mail para usários DVE";
   }
   else
   {
      echo  "OK!";  
   }
   */

   //Inclui em tabela de atrazo envio DVE


   $wstr = " insert into DVE_16dia (" .
          " idInform, " .
	      " n_Apolice, " .
	      " seq_dve, " .
	      " dt_periodo_ini, " .
	      " dt_periodo_fim, " .
	      " fl_doc, " .
	      " pth_doc, " .
	      " pth_doc_banco, " .
	      //" fl_visualizado, " .
	      //" fl_visualizado_banco, " .
	      " nu_envio, " .
	      " state, " .
		  " idDVE, " .
	      " idNotification " .
	      " ) values ( " .
          " " . $idInform . ", " .
	      " " . $hc_n_Apolice . ", " .
	      " " . $num . ", " .
	      " '" . $inicio . "', " .
	      " '". $dt_fim . "', " .
	      " 'N', " .
	      " '', " .
	      " '', " .
	      //" 'N', " .
	      //" 'N', " .
	      " 0, " .
	      " 0, " .
		  " " . $idDVE . ", " .
	      " " . $hc_r . " " .
	      " )";
		  
		  //echo $wstr;
		  
		  $r = odbc_exec ($db,$wstr);
	   
} 	   
	   
	   
	   

?>
