<?php  //ALTERADO HICOM 26/04/2004
//Alterado HiCom mes 04

$x = odbc_exec($db, "select idAnt, name from Inform where id=$idInform");
$idAnt = odbc_result($x, 1);
$informName = odbc_result($x, 2);

$x = odbc_exec($db,
	       "select creditSolic from ChangeCredit where idImporter=$idBuyer ".
	       "and creditSolic is not null order by id desc");
$creditSolic = odbc_result($x, 1);


//--hicom
$hc_x = odbc_exec($db, "select idCountry from Importer where id=$idBuyer ");
$hc_country = odbc_result($hc_x, 1);
//--fim hicom


odbc_autocommit ($db, false);
$eCoface = $field->getField('ci');
$ok = true;

if(! odbc_exec($db,
	       "INSERT INTO ChangeCredit ".
	       "(idImporter, analysis, monitor, state, creditSolic, userIdChangeCredit) ".
	       "VALUES ($idBuyer, 1, 1, 1, $creditSolic, $userID)")){
  $msg = "Erro ao inserir mudança de credito para importador";
  $ok = false;
}
if(! odbc_exec($db, "update Importer set state=3 where id=$idBuyer"))
{
  $msg = "Erro ao mudar status do importador";
  $ok = false;
}

if ($ok)
{
    //ALTERADO HICOM 26/04/2004---------------
   // ATIVO E INATIVO do mesmo PAIS
   
   $ver_coface = odbc_exec($db,"SELECT c_Coface_Imp from Importer where c_Coface_Imp='" . $eCoface . "' and idCountry=$hc_country and idInform=$idInform AND id <> $idBuyer ");
  
   if(odbc_fetch_row($ver_coface))
   { 
     $msg = "Já existe um importador deste cliente com mesmo CI e país";
	 $ok = false;
   }

  //----------------------------------------

  if ($ok)
  {
    $query = "UPDATE Importer SET c_Coface_Imp='$eCoface', state=3 WHERE id=$idBuyer";
    if(!odbc_exec($db, $query))
    { // passa para pendencias
      $msg = "Erro ao mudar status do importador";
      $ok = false;
    }
  
  }
  
}
  
if ($ok)
{
  odbc_commit($db);
}
else
{
  odbc_rollback($db);
}
?>
