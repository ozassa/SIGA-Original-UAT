<?php  require_once("../../dbOpen.php");
require_once("../../entity/notification/Notification.php");
$notif = new Notification ();


// Obtem o nome do exportador no informe 

// Obtem dados do inform
$t = odbc_exec($db, "SELECT inf.id, inf.contrat, inf.i_Seg, inf.nProp, inf.name, inf.cnpj, inf.startValidity, inf.endValidity, inf.emailContact, inf.contact, inf.email, inf.idRegion from Inform inf WHERE inf.id = $idInform");
$hc_infName = trim(odbc_result($t, "name"));
//$hc_startValidity = ymd2dmy(trim(odbc_result($t, "startValidity")));
//$hc_endValidity = ymd2dmy(trim(odbc_result($t, "endValidity")));
$hc_i_Seg = trim(odbc_result($t, "i_Seg"));
$hc_n_Prop = trim(odbc_result($t, "nProp"));
$hc_c_Coface = trim(odbc_result($t, "contrat"));
$hc_idRegion = trim(odbc_result($t, "idRegion"));

//Gera notificaçao
$msg = "";
$hc_r = $notif->periodoRenova(-1, $hc_infName, $idInform, $hc_idRegion, $db, $hc_erro );
if (!$hc_r) 
{
  echo "NOK! A notificação NÃO foi encaminhada aos usuários! " . $hc_erro ; 
}
else
{
  echo "OK! A notificação foi encaminhada aos usuários! " . $hc_erro ;
}

?>
