<?php  $xpy = odbc_exec($db, "update Inform set state=4, prMin = prAux, txMin = txAux, ic_gravou_mod = null where id=$idInform");
$expy = odbc_exec($db, "update Inform set state=4, prAux = null, txAux = null, ic_gravou_mod = null where id=$idInform");
$x = odbc_exec($db, "select name from Inform where id=$idInform");
if(odbc_fetch_row($x)){
  $name = odbc_result($x, 1);
}

if($notif->newTarif($userID, $name, $idInform, $db)){
  $notif->doneRole($idNotification, $db);
  $msg = 'Informe devolvido para tarifação';
}else{
  $msg = 'Problemas ao enviar Informe para retarifação';
}

//Criado Por Tiago V N - 19/10/2005
//Log do retarifar ( Informe para Retarifado )
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('18'," .
           "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           "','".date("H").":".date("i").":".date("s")."')"; 
   if (odbc_exec($db, $sql) ) {
   		$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		$cur = odbc_result(odbc_exec($db, $sql_id), 1);
    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', 'Retarifação', 'vazio', 'Alteração')";
		odbc_exec($db, $sql);	   
   }else{
     $msg = "Erro no incluir do Log";
   }
?>
