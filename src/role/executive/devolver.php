<?php  //Alterado HiCom mes 04
//Alterado Hicom
$log_query = "";

$x = odbc_exec($db, "update Inform set state=1 where id=$idInform");

if($x)
{
	$log_query .= "update Inform set state=1 where id=$idInform";
}

//$y = odbc_exec($db, "update Importer set state=1 where idInform=$idInform");
//$y = odbc_exec($db, "update Importer set state=1 where state <> 6 and idInform=$idInform");
//if(! $x || ! $y){
if(! $x ){
  $msg = 'Erro ao devolver Informe para o cliente';
  return;
}

$notif->doneRole($idNotification, $db);
$msg = 'Informe devolvido para o cliente';

//Criado Por Tiago V N - 19/10/2005
//Log do Reestudo ( Informe para Reestudo )
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('19'," .
           "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           "','".date("H").":".date("i").":".date("s")."')"; 
   if (odbc_exec($db, $sql) ) {
   		$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		$cur = odbc_result(odbc_exec($db, $sql_id), 1);
    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', 'Reestudo', 'Envio cliente', 'Alteração')";
		$rs = odbc_exec($db, $sql);	 

	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   }else{
     $msg = "Erro no incluir do Log";
   }
?>
