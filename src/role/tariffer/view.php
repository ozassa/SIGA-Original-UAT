<?php
$cur=odbc_exec($db, "SELECT notification FROM NotificationR WHERE id = $idNotification"
	       );
if (!odbc_fetch_row($cur)) $title = "Notificação inválida";
else $title = odbc_result($cur,1);

$idAnt = odbc_result(odbc_exec($db, "select idAnt from Inform where id=$idInform"), 1);

if($idAnt > 0){
  	$title .= "<br><center>(Renovação)</center>";
}

$sqlpv = "Select pvigencia, Periodo_Vigencia from Inform where id='$idInform'";
$pv = odbc_exec($db, $sqlpv);

while(odbc_fetch_row($pv)){
   	$vigencia = odbc_result($pv, 1);
}

//echo $sqlpv ."-" . $vigencia;
if ( $vigencia == "") {
   	$pvigencia = "1";
}else if ( $vigencia =="1"){
   	$pvigencia = "1";
}else{
   	$pvigencia = "2";
}

$Periodo_Vigencia = isset($Periodo_Vigencia) ? $Periodo_Vigencia : false;
if($Periodo_Vigencia){
	$pvigencia  =  $Periodo_Vigencia;
}
?>