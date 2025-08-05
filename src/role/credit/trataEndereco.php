<?php  //Alterado HiCom mes 04

$idInform = $field->getField("idInform");
$idNotification = $field->getField("idNotification");
$frm_i = $field->getField("frm_i");


$q = "SELECT contrat, name, idAnt, dateEmission FROM Inform where id = $idInform";
$d = odbc_exec($db, $q);

if(odbc_fetch_row($d)){
  $ciExpo   = odbc_result($d, 1);
  $nameExpo = odbc_result($d, 2);
  $idAnt    = odbc_result($d, 3);
  $dateEmission = odbc_result($d, 4);
  $hc_name_inform = $nameExpo;
  
  
}else{
  $ciExpo   = "ERRO";
  $nameExpo = "ERRO";
  $hc_name_inform = $nameExpo;
  
}

$ok = true;

if ($frm_i > 0)
{

  odbc_autocommit ($db, false);
  
  $hc_i = 0;
  for ($hc_i;$hc_i<$frm_i;$hc_i++)
  {
  
    $hc_frm_aceitar = "" . $field->getField("frm_aceitar".$hc_i);
	$hc_id = $field->getField("frm_id".$hc_i);
	
	if ($hc_frm_aceitar != "")
	{
      $hc_str = " update ImporterAddress set pendente='N', idUser=$userID  ".
	           " where id =$hc_id ";
	}
	else
	{
      $hc_str = " delete ImporterAddress ".
	            " where id =$hc_id ";
	}
    
	if (!odbc_exec($db, $hc_str))	
	{
	   $ok = false;
	   $msg = "Problemas atualizando os endereços!";
	}
	
	echo "--" . $hc_id;
   
  }
  
  if ($ok)
  {
     $idNotification = $field->getField("idNotification");
     if (!$notif->doneRole($idNotification, $db)) 
	 {
	    $ok = false;
		$msg = "Não foi possível desativar a notificação!";
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

  odbc_autocommit ($db, true);
  
}

//die();

?>
