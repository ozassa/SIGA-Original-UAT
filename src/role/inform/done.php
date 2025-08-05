<?php //criado por Wagner 1/9/2008
	$log_query ="";
	
	if(!$userID){
     		$userID = $_SESSION['userID'];
   	}
	 
  	$ok = true;
	$forward = "error";
  	odbc_autocommit ($db, false);
  
  	$cur=odbc_exec($db, "SELECT vol2 + vol3 + vol4 FROM Volume WHERE idInform = ".$field->getField("idInform"));
  	$abc = 0;

     	if (odbc_fetch_row($cur))
         	$abc = odbc_result($cur,1);
  
  	$qry = "SELECT generalState, volState, segState, buyersState, lostState, idRegion, name FROM Inform WHERE id = ".$field->getField("idInform");
 
  	$cur=odbc_exec($db,$qry);
  	odbc_fetch_row($cur);

  	$idRegion = odbc_result($cur,6);
  	$name = odbc_result($cur,7);
  	$test = 0;

	//if ($role["client"] || $hc_cliente == "N"){
	if ($role["client"] || $_SESSION['pefil'] == "C" || $_SESSION['pefil'] == "B") {
		$test = 2;
	}else{
	  $test = 3;
	}

  	$i = 1;

  	for (;$i<=5;$i++) {
      		if (odbc_result($cur,$i) != $test){ 
	      		$ok = false;
	  	}
  	}

  	if ($i == 1) 
      		$ok = false;
  
  	//if ($hc_cliente == "N"){
  	if ($_SESSION['pefil'] == "C" || $_SESSION['pefil'] == "B") {
	   	$novo_estatus = "2";
  	}else{ 
	   	$novo_estatus = ($role["client"] ? "2" : "3");
  	}    

  	if (!ok){
      		$forward = "error";
  	}else{
	  	$cqry =  "UPDATE Inform SET".
			"  state = ".$novo_estatus.",".
			"  respName = '".$field->getField("respName")."',".
			"  ocupation = '".$field->getField("ocupation")."'".
			"  WHERE id = $idInform";
      		$r = odbc_exec($db,$cqry);    

	  	if($r){
			$log_query .="UPDATE Inform SET".
			  	"  state = ".$novo_estatus.",".
			  	"  respName = '".$field->getField("respName")."',".
			  	"  ocupation = '".$field->getField("ocupation")."'".
			  	"  WHERE id = $idInform";
	  	}

	  	if(! $role['client'] && ($hc_cliente != "N")  ){ // mudou para status 3
		  	$x = odbc_exec($db, "insert into AnaliseInform (idInform, inicio) values ($idInform, getdate())");

			if($x){
				$log_query .="insert into AnaliseInform (idInform, inicio) values ($idInform, getdate())";
			}
		    
	  	}else{ // mudou para status 2, verifica se antes estava em status 3
			$x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");

			if(odbc_fetch_row($x)){
				$id = odbc_result($x, 1);
				$fim = odbc_result($fim, 2);

				if(! $fim){
					$a = odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
					  
					if($a){
						$log_query .="update AnaliseInform set fim=getdate() where id=$id";
					}
				}
			}
		}

		if ($r) {
		    	$imp = odbc_exec ($db,"SELECT id, hold FROM Importer WHERE idInform = $idInform ORDER BY id");
		    	$count = 0;

		    	while (odbc_fetch_row ($imp)) {
				$query = "UPDATE Importer SET hold = 0 WHERE id =";
				$query .= odbc_result ($imp, 'id');

				$b = odbc_exec ($db, $query);

				if($b){
				     	$log_query .=$query;
				}

				$count ++;
		     	}

		  	// Gera a notificação
		  	if ($role["client"] or ($hc_cliente == "N")){	
		  
				$notif->newInform($userID, $idRegion, $name, $idInform,  $db);

				if ($idNotification){
					$notif->doneRole ($idNotification,$db);
				}

				//Criado Por Tiago V N - 16/03/2006
				if($idAnt > 0){//Verifica se é renovação
					//Log do Envio de informe de renovação
					$sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('31'," .
						"'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
						"','".date("H").":".date("i").":".date("s")."')";

					if (odbc_exec($db, $sql) ) {
						$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
						$cur = odbc_result(odbc_exec($db, $sql_id), 1);

						$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
							"values ('$cur', 'status', '2', 'Preenchido')";
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
							  
							odbc_exec($db, $sql);
						 }//fim if	
						 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					}else{
						$msg = "Erro no incluir do Log";
					}
				}else{
					$sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('51'," .
						"'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
						"','".date("H").":".date("i").":".date("s")."')";

					if (odbc_exec($db, $sql) ) {
						$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
						$cur = odbc_result(odbc_exec($db, $sql_id), 1);

						$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
						   "values ('$cur', 'status', '1', 'Novo')";
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
						  
							odbc_exec($db, $sql);
					   	}//fim if	
					   	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					}
				}

			  	if ($ok) {
				  	odbc_commit ($db);
				  	$forward = "success";
			  	} else {
				  	odbc_rollback ($db);
			  	}
	      		}
    		}
	}

odbc_autocommit ($db, true);
?>
