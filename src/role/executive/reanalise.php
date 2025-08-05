<?php  

$log_query ="";

$x = odbc_exec($db, "update Inform set state=3 where id=$idInform");
	if($x)
	{
		$log_query .="update Inform set state=3 where id=$idInform";
	}
	
	
	
$x = odbc_exec($db, "update Importer set state=1 where idInform='$idInform' And state='6'");
	if($x)
	{
		$log_query .="update Importer set state=1 where idInform='$idInform' And state='6'";
	}
	
	
	
$x = odbc_exec($db, "select name from Inform where id=$idInform");
if(odbc_fetch_row($x)){
  $name = odbc_result($x, 1);
}

if($notif->newCredit($userID, $name, $idInform, $db, 12)){
  $notif->doneRole($idNotification, $db);
  $msg = 'Informe devolvido para Analise de Credito';
}else{
  $msg = 'Problemas ao enviar Informe para Analise de Credito';
}

//Criado Por Tiago V N - 19/10/2005
//Log do Reestudo ( Informe para Reanalise de Crédito )
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('32'," .
           "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           "','".date("H").":".date("i").":".date("s")."')"; 
   if (odbc_exec($db, $sql) ) {
   		$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		$cur = odbc_result(odbc_exec($db, $sql_id), 1);
    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', 'Reanalise', 'Envio Analise Crédito', 'Alteração')";
		$rs = odbc_exec($db, $sql);	   

	   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		
   }else{
     $msg = "Erro no incluir do Log";
   }
?>

