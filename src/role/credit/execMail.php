<?php  // Alterado Hicom (Gustavo) - 19/01/05 - login não é mais um e-mail obrigatoriamente, sendo assim 
// o destinatário do e-mail foi alterado

function manda_email($email, $confirm_id, $cookie){
  global $root;
  $hash = base64_encode("$confirm_id|$cookie");
  $link = "http://sbcesun". $root. "role/credit/confirmMail.php?hash=$hash";
  $msg = "
Olá.

Estou testando essa coisa, e esperando eles mandarem um texto melhor pra colocar aqui.

Clica em $link
e ve se funciona mesmo...

tchau\n\n";

  // manda email para o contato
  return mail(trim($email), 'Testando', $msg, "From: webmaster@tendencies.com.br\nX-Mailer: PHP/" . phpversion());
}

function manda_email_contato($email){
  $msg = "
Olá.

Esta é uma mensagem de teste.

Por favor ignore-a.

obrigado
";

  // manda email para o contato
  return mail(trim($email), 'Testando', $msg,
	      "From: webmaster@tendencies.com.br\nX-Mailer: PHP/" . phpversion());
}

$idNotification = $field->getField('idNotification');
$emailBB        = $field->getField('emailBB');
$obs            = $field->getField('obs');
$idInform       = $field->getField('idInform');
$idBuyer        = $field->getField('idBuyer');
$type           = $field->getField('type');
$invoice        = $field->getField('invoice');
$sucess         = 1;

if ($email) {
  $i = 0;

  // pega o email do usuario
//  $cur = odbc_exec($db, "select login from Users where id=$userID");
//  if (odbc_fetch_row ($cur)) {
//    $email = odbc_result ($cur, 1);

  if ($user->email) {
    $email = $user->email;
    
    $cookie = session_id().time();
    $cookieArray[$i] = $cookie;

    $query = 	   "INSERT INTO MailConfirm ".
		   "(email, type, idImporter, cookie, obs, state, sentDate, idInform, userIdSendMail) ".
		   "VALUES ".
		   "('$email', '$type', '$idBuyer', '$cookie', '$obs', 1, getdate(), $idInform, $userID)";
    $c = odbc_exec($db, $query);
    if(!$c){
      $msg = odbc_errormsg($db);
    }
    $c = odbc_exec($db, "select max(id) from MailConfirm where cookie='$cookie'");
    if(odbc_fetch_row($c)){
      $confirm_id = odbc_result($c, 1);
    }
    $r = odbc_exec($db,
		   "INSERT INTO TransactionLog (User, description) ".
		   "VALUES ($userID, 'Enviou Email [$email] cookie [$cookie]')");
    // manda o email
    if(! manda_email($email, $confirm_id, $cookie)){
      $msg = "Erro ao enviar email para $email";
    }else{
      $msg = "Email enviado com sucesso para $email";
    }
  } else {
    $msg = "Erro ao enviar o e-mail.". odbc_errormsg($db);
  }

  // pega os emails da tabela de contatos
  $cur = odbc_exec ($db, "SELECT email FROM Contact WHERE notificationForChangeCredit = '1' AND idInform = $idInform");
  while (odbc_fetch_row ($cur)) {
    $email = odbc_result ($cur, 1);
    $cookie = session_id().time();
    $cookieArray[$i] = $cookie;
    $i++;

    $c = odbc_exec ($db,
		    "INSERT INTO MailConfirm ".
		    "(email, type, idImporter, cookie, obs, state, sentDate, idInform, userIdSendMail) ".
		    "VALUES ('$email', '$type', '$idBuyer', '$cookie', '$obs', 1, getdate(), $idInform, $userID)");
     $c = odbc_exec($db, "select max(id) from MailConfirm where cookie='$cookie'");
     if(odbc_fetch_row($c)){
       $confirm_id = odbc_result($c, 1);
     }
    $r = odbc_exec ($db,
		    "INSERT INTO TransactionLog (User, description) VALUES ".
		    "($userID, 'Enviou Email [$email] cookie [$cookie]')");
    if(! manda_email($email, $confirm_id, $cookie)){
      $msg = "Erro ao enviar email para $email";
    }else{
      $msg = "Email enviado com sucesso para $email";
      $sucess = true;
    }
  }
}

/*********************************************************/
if ($emailBB == 1) {
  $i++;
  $c = obdc_exec ($db,
		  "INSERT INTO MailConfirm".
		  " (email, type, idImporter, cookie, obs)".
		  " VALUES ('joao@bb.com.br', '$type', '$idBuyer', '$cookie', '$obs')");
  // log
  $r = odbc_exec ($db,
		  "INSERT INTO TransactionLog (User, description) ".
		  "VALUES ('$userID', 'Enviou Email [$email] cookie [$cookie]')");
  $cookie = session_id().time();
  $cookieArray[$i] = $cookie;

  if (!$c) {
    $msg = "Erro ao enviar o e-mail.". odbc_errormsg($db);
  } else {
    $sucess = true;
  }
}

/*********************************************************/
// verifica se é mudança de dados
if($insertImporter == 1){
  if($newName != ""){
    $newName = ereg_replace("'", "''", $newName);
    if(! odbc_exec($db, "update Importer set name='$newName' where id=$idBuyer"))
      $msg = "Erro ao atualizar nome do importador";
  }
  if($newAddress != ""){
    if(! odbc_exec($db, "update Importer set address='$newAddress' where id=$idBuyer"))
      $msg = "Erro ao atualizar endereço do importador";
  }
  if($newCity != ""){
    if(! odbc_exec($db, "update Importer set city='$newCity' where id=$idBuyer"))
      $msg = "Erro ao atualizar cidade do importador";
  }
  $msg = "Dados atualizados com sucesso";
}

// notification
if ($sucess) {
  if(!$table_to_update){
    $table_to_update = 'NotificationU';
  }
  $cur = odbc_exec ($db, "UPDATE $table_to_update SET state = '2' WHERE id = $idNotification");
  if (!$cur) {
    $msg = "Erro idNotification: $idNotification:". odbc_errormsg($db);
  } else {
    if(!$address_flag){
      $b = odbc_exec ($db, "INSERT INTO Invoice (state, idImporter) VALUES ($invoice, $idBuyer)");
      if (!$b) {
	$msg = "Erro na atualização do faturamento para o importador: ($invoice,$idBuyer).";
      }
    }
  }
  if($obs){
    $cur = odbc_exec($db, "insert into ImpComment (idImporter, comment) values ($idBuyer, '$obs')");
    if(!$cur){
      $msg = "Erro ao inserir observação:";
    }
  }
}

?>
