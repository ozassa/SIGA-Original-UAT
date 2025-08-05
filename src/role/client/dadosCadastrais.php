<?php  if($idRegion == 0){
   $idRegion = 2;
}

odbc_autocommit($db, false);
if($user->hasRole('endosso') || $user->hasRole('backoffice')){
  $solic = 1;
  // enviar email para o cliente com a proposta
  //require("carta11.php");
  //echo nl2br($mensagem);
  // mail($email, 'Proposta de Endosso de Dados Cadastrais', $mensagem, 'From: sbce@sbce.com.br');
}else{
  $solic = 2;
}


$q = "SELECT MAX (codigo) + 1 FROM Endosso";
//echo "<pre>$q</pre>";
$cur = odbc_exec($db, $q);
odbc_fetch_row ($cur);
$codigo = odbc_result ($cur,1);


$q = "INSERT INTO Endosso (idInform, tipo, solicitante, idUser, codigo) values ($idInform, $tipo, $solic, $userID, $codigo)";
//echo $q;
$c = odbc_exec($db, $q);
//echo '<pre>1'.odbc_errormsg ($db).'</pre>';
if(! $c){
  $msg = "Erro ao inserir endosso";
  odbc_rollback($db);
  odbc_autocommit($db, true);
  return;
}

$idEndosso = odbc_result(odbc_exec($db,
				   "select max(id) from Endosso where idInform=$idInform"),
			 1);
//echo '<pre>2'.odbc_errormsg ($db).'</pre>';

$cnpjo = $new_cnpj;
$len = strlen ($new_cnpj);
$cnpj = "";
//for ($i = 0; $i < $len && $i < 18; $i ++) {
for ($i = 0; $i < $len; $i ++) {
  $cnpj .= is_numeric ($cnpjo[$i]) ? $cnpjo[$i] : "";
}

$new_name = strtoupper($new_name);
if(preg_match("/[\/ÁÉÍÓÚÀÂÊÎÔÛÃÕÜ]/", $new_name)){
  $msg = "Não são permitidos acentos ou barras para a Razão Social";
  odbc_rollback($db);
  odbc_autocommit($db, true);
  $volta = 1;
  $new_name = '';
  return;
}else{ // se nao achou nenhum erro, insere o importador
  $new_name = ereg_replace("'", "''", $new_name);
}
$query = "insert into EndossoDados
		(idEndosso, name, address, city, cep, idRegion, cnpj, nameOld, addressOld,
		 cityOld, cepOld, idRegionOld, cnpjOld,addresscomp,number,addresscompOld,numberOld)
	  values
		($idEndosso, '$new_name', '$new_address', '$new_city', '$new_cep', $idRegion,
		 '$cnpj', '$nameOld', '$addressOld', '$cityOld', '$cepOld', $idRegionOld,
		 '$cnpjOld','$new_complemento','$new_number','$addresscompOld','$numberlold')";
//echo "<pre>$query</pre>";
$c = odbc_exec($db, $query);
//echo '<pre>3'.odbc_errormsg ($db).'</pre>';
if(! $c){
  $msg = "Erro ao inserir dados do endosso";
  odbc_rollback($db);
  odbc_autocommit($db, true);
  return;
}

$ano = date ('Y');
$c = $codigo."/".$ano;


$notif->newEndossoDados($idInform, $idEndosso, $db, $c);
$msg = "Endosso de Dados Cadastrais Solicitado";
//echo '<pre>4'.odbc_errormsg ($db).'</pre>';
odbc_commit($db);
odbc_autocommit($db, true);
?>
