<?php //criado por Wagner 1/9/2008
$log_query ="";

  $rs = odbc_exec(
    $db,
    " DELETE FROM VolumeSeg WHERE id=".$field->getNumField("idSeg")
  );
  
  if($rs)
  {
	$log_query .=" DELETE FROM VolumeSeg WHERE id=".$field->getNumField("idSeg");
  }
      //Registrar no Log (Sistema) - Criado Por Tiago V N - 03/07/2006
     // Tipo Log = Remover Segmentação da Previsão do Volume de Vendas Externas (Tela Cliente) - 48

        $sql  = "Insert Into Log (tipoLog, id_User, Inform, data, hora) Values
               ('48', '$userID', '$idInform','".date("Y")."-".date("m")."-".date("d")."',".
               "'".date("H").":".date("i").":".date("s")."')";
		$result = odbc_exec($db, $sql);
	    if ($result) {
           $sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		   $cur = odbc_result(odbc_exec($db, $sql_id), 1);

		   $sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) values ".
                     "('$cur', 'Informe', '".$field->getNumField("buyers")."', 'Buyers')";
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
?>
