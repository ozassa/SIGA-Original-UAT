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

if ($frm_i > 0)
{
  
  $hc_i = 0;
  for ($hc_i;$hc_i<$frm_i;$hc_i++)
  {
  
    $hc_frm_aceitar = $field->getField("frm_aceitar".$hc_i);
    echo "---------------------" . $hc_frm_aceitar;
	
  
  }
  
}

die();

?>
