<?php  $log_query = "";


$cur = odbc_exec($db,
		 "SELECT vol2 + vol3 + vol4, name, idRegion
                  FROM Volume v JOIN Inform inf ON v.idInform = inf.id
                  WHERE idInform = $idInform");

$abc = 0;
$msg = "Informe devolvido com sucesso";
if (odbc_fetch_row($cur)) {
  $abc = odbc_result($cur,1);
  $name = odbc_result($cur,2);
  $idRegion = odbc_result($cur,3);

  $r = odbc_exec ($db, "UPDATE Inform SET state = 1 WHERE id = $idInform");
  $x = odbc_exec($db, "delete from AnaliseInform where idInform=$idInform");
  
  //criado por Wagner 29/08/2008
  if($r)
  {
	$log_query .= "UPDATE Inform SET state = 1 WHERE id = $idInform";
  }  
  
  if($x)
  {
	$log_query .= "delete from AnaliseInform where idInform=$idInform";
  }
  
  if ($r) {
    //      if ($abc > 1000000)
    $r = $notif->retInform($userID, $idRegion, $name, $idInform,  $db);
    //else
    //$r = $notif->retLowInform($userID, $name, $idInform,  $db);
    if (!$r)
      $msg = "Problemas na geração da notificação";
  } else $msg = "Problemas na atualização do status do informe";
} else $msg = "Problemas na obtenção do informe";
if (!$notif->doneRole($idNotification, $db)) $msg = "Problemas ao apagar a notificação";

//Criado Por Tiago V N - 04/10/2005
//Log do Analise de Crédito ( Recusar informe )
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('10'," .
           "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           "','".date("H").":".date("i").":".date("s")."')"; 
   if (odbc_exec($db, $sql) ) {
   		$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		$cur = odbc_result(odbc_exec($db, $sql_id), 1);
    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', 'Informe', 'Recusado', 'Alteração')";
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


		
   }else{
     $msg = "Erro no incluir do Log";
   }
?>
