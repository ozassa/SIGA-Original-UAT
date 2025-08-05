<?php  if($user->hasRole('backoffice') || $user->hasRole('endosso')){
  $solic = 1;
  // enviar email para o cliente com a proposta
  require_once("carta13.php");
  //echo nl2br($mensagem);
  // mail($email, 'Proposta de Endosso de Natureza da Operação', $mensagem, 'From: sbce@sbce.com.br');
}else{
  $solic = 2;
}

$q = "SELECT MAX (codigo) + 1 FROM Endosso";
//echo "<pre>$q</pre>";
$cur = odbc_exec($db, $q);
odbc_fetch_row ($cur);
$codigo = odbc_result ($cur,1);

  $ano = date ('Y');
  $codE = $codigo."/".$ano;

$query = "INSERT INTO Endosso (idInform, tipo, solicitante, idUser, codigo) values ($idInform, '$tipo', $solic, $userID, $codigo)";
$c = odbc_exec($db, $query);
if(! $c){
  $msg = "Erro ao inserir endosso";
  return;
}

$idEndosso = odbc_result(odbc_exec($db,
				   "select max(id) from Endosso where idInform=$idInform"),
			 1);
$c = odbc_exec($db, "insert into EndossoNatureza (idEndosso, natureza, idSector, naturezaOld, idSectorOld) values ($idEndosso, '$altNat', $idSector, '$naturezaOld', $idSectorOld)");
if(! $c){
  $msg = "Erro ao inserir dados do endosso";
  return;
}


if($user->hasRole('backoffice') || $user->hasRole('endosso')){
  $status = 1;
  $c = odbc_exec($db, "update Endosso set state=$status where id=$idEndosso");
  if(! $c){
    $msg = 'Erro ao atualizar endosso';
    odbc_rollback($db);
    return;
  }
  // envia para a tarifacao endosso de natureza de operação
  $name = odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1);
  if(! $notif->newTarifN($user, $name, $idInform, $db, $idEndosso, 'natOper', $codE)){
    $msg = 'Erro ao enviar para a tarifação';
    odbc_rollback($db);
    return;
  }

}else{
 $notif->newEndossoNatureza($idInform, $idEndosso, $db, $codE);
}
$msg = "Endosso de Natureza da Operação Solicitado";

?>
