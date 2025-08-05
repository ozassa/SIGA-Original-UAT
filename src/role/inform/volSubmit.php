<?php // verifica se a entrada é válida

  //criado por Wagner
  $log_query ="";
  
  $idInform  = $field->getField("idInform");
  
  $fail = 0;
  $sqlStr = "";
  for ($i = 1; $i <= 45; $i++) {
    if (!is_numeric($field->getNumField("vol".$i))) {
      $forward = "error";
      $fail = 1;
      $msg = "Campo não numérico";
      break;
    }
    $sqlStr = $sqlStr."vol".$i."=".$field->getNumField("vol".$i).($i == 45 ? " " : ",");
  }
  if (!$fail) {
    odbc_autocommit ($db, FALSE);
    $ok = true;
    $r = odbc_exec(
      $db,
      " UPDATE Volume".
      " SET ".$sqlStr.
      "  WHERE idInform =".$field->getField("idInform")
    );
	
	if($r)
	{
		$log_query .=" UPDATE Volume".
      " SET ".$sqlStr.
      "  WHERE idInform =".$field->getField("idInform");
	}
	
	
    if (!$r)
	   $ok = false;
    else {

    if ($hc_cliente == "N" && ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')) {
      $novo_estatus = "2";
    } else {
      if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') {
        $novo_estatus = "2";
      } else {
        $novo_estatus = "3";
      }
    }
			
	
	
      $r = odbc_exec(
        $db,
        " UPDATE Inform".
        "  SET     ".
        "      volState = ". $novo_estatus.
        "  WHERE id =".$field->getField("idInform")
      );
      if (!$r) $ok = false;
	  
	  if($r)
	  {
		$log_query .=" UPDATE Inform".
        "  SET     ".
        "      volState = ". $novo_estatus.
        "  WHERE id =".$field->getField("idInform");
	  }
    }

    if ($ok){
     //Registrar no Log (Sistema) - Criado Por Tiago V N - 03/07/2006
     // Tipo Log = Alteração do Resumo do Volume de Vendas Externas (Tela Cliente) - 34

        $sql  = "Insert Into Log (tipoLog, id_User, Inform, data, hora) Values
               ('34', '$userID', '$idInform','".date("Y")."-".date("m")."-".date("d")."',".
               "'".date("H").":".date("i").":".date("s")."')";
		$result = odbc_exec($db, $sql);
	    if ($result) {
           $sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		   $cur = odbc_result(odbc_exec($db, $sql_id), 1);

		   $sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) values ".
                     "('$cur', 'Informe', '-', 'Dados do Pag e Exportação')";
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
	         // odbc_exec($db, $sql);
	   }//fim if	
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		   
		   
		   
		}
		
      odbc_commit ($db);
    }
    else {
      odbc_rollback ($db);
      $msg = "falha na inserção na base de dados";
      $forward = "error";
    }

    odbc_autocommit ($db, TRUE);
  }
?>






