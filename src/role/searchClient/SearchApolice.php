<?php 
require_once ("../rolePrefix.php");

//extract($_REQUEST);

//criado por Wagner 1/9/2008
$log_query="";


if ($comm == "viewImportador"){
  $title = " Lista de Compradores";
  $content = "../searchClient/viewImportador.php";

}else if ($comm=="notif"){
	$title = "Notificações";
	$content = "../../../main.php";

}else if ($comm=="notig"){
	$notif->doneRole($idNotification, $db);
	// Grava Log
	$sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('26'," .
			"'$userID','$idInform','".date("Y")."-".date("m")."-".date("d").
			"','".date("H").":".date("i").":".date("s")."')";                          
	$result =  odbc_exec($db, $sql);

	if($result){
		$log_query .=$sql;
	}

	if ($result) {
	  $sql_id = "SELECT @@IDENTITY as id_Log";
	  $cur = odbc_result(odbc_exec($db, $sql_id), 1);
	  $sql = " Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
			 "values ('$cur', 'state', '$idInform', 'Cancelamento de Notificação');";
	  $rs = odbc_exec($db, $sql);
			
		   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		   //CRIADO POR WAGNER
		   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
		   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		   
		   if ($rs) {
			  $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
			  $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
			  $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
				  "values ('$cur', '".str_replace("'","",$log_query)."')";
				  
				  //echo $sql;
				  odbc_exec($db, $sql);
		   }//fim if	
		   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  
	 } 
    // Fim da gravação do log
	$title = "Notificações";
	$content = "../../../main.php";
}else if ( $bloqueia == "" ) {
	$nome = $field->getField ("nome");
	$napolice = $field->getField ("napolice");

	if ( $comm == "listlogin" ) {
	$idclient = $field->getField ("idclient");
	$title = " Cancelamento de Informe";
	$content = "../searchClient/viewapolice.php";
	}else if ( $comm == "cancelar" ) {
	$sql= "Update Inform set state='9', contrat='' where id = '$idinform'";
	$cur = odbc_exec($db, $sql);

	if($cur)
	{
		$log_query .=$sql;
	}

    // Grava Log
			$sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('6'," .
                    "'$userID','$idinform','".date("Y")."-".date("m")."-".date("d").
                    "','".date("H").":".date("i").":".date("s")."')";                          
            $result =  odbc_exec($db, $sql);
            if ($result) {
                $sql_id = "SELECT @@IDENTITY as id_Log";
                $cur = odbc_result(odbc_exec($db, $sql_id), 1);
                $sql = " Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
                       "values ('$cur', 'state', '$status_at', 'Cancelamento');";
                $rs = odbc_exec($db, $sql);
				
							
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	   //CRIADO POR WAGNER
	   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   
	   if ($rs) {
	      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
	      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
	      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
	          "values ('$cur', '".str_replace("'","",$log_query)."')";
			  
			  //echo $sql;
	          odbc_exec($db, $sql);
	   }//fim if	
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
} 
// Fim da gravação do log

$sql = "select name from Inform where id='$idinform'";  
$cur = odbc_exec($db, $sql);
odbc_fetch_row($cur);
$clientR = odbc_result($cur, 1);
$notif->newInfCredito($userID, $clientR, $idinform, $db);

$title = "Notificações";
$content = "../../../main.php";
}else
if ( $comm == "" or $comm == "back")
{
	$title = "Listagem de Informe para Cancelar";
	$content = "../searchClient/listbapolice.php";
	
}
}

require_once("../../../home.php");

?>
