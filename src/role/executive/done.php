<?php  //HICOM Alterado

$log_query = "";

$mot             = $field->getField("mot");
$idInform        =  $field->getField("idInform");
$idNotification  = $field->getField("idNotification");
$userID          = $_SESSION['userID'];

//die('ops!');
$ok = true;
$forward = "error";
odbc_autocommit ($db, false);

$qry = "SELECT 
a.generalState, a.volState, a.segState, a.buyersState, a.lostState, a.idRegion, a.name, a.idAnt, 
a.prMax, a.prMin, a.sentOffer, a.pvigencia, a.contrat,a.naf, a.napce, a.siren, a.dossier, a.quest,
a.Periodo_Vigencia,a.i_Seg,a.i_Produto, b.SistemaDestino
FROM Inform a
inner join Config_Produto b on b.i_Produto = a.i_Produto
WHERE id = ".$idInform."";

$cur=odbc_exec($db, $qry);

odbc_fetch_row($cur);

$idRegion           = odbc_result($cur,'idRegion');
$name               = odbc_result($cur,'name');
$idAnt              = odbc_result($cur, 'idAnt');
$i_Seg              = odbc_result($cur, 'i_Seg');
$prMax              = odbc_result($cur, 'prMax');
$prMin              = odbc_result($cur, 'prMin');
$sentOffer          = odbc_result($cur, 'sentOffer');
$i_Produto          = odbc_result($cur, 'i_Produto');
$SistemaDestino     = odbc_result($cur, 'SistemaDestino');
//Alterado por Tiago V N - 03/10/2005
$vigencia           = odbc_result($cur, 13);
$Periodo_Vigencia   = odbc_result($cur, 'Periodo_Vigencia');

if ($vigencia==""){
	$pvigencia = "12 Meses";
}else if($vigencia=="1"){
	$pvigencia = "12 Meses";
}else if($vigencia=="2"){
	$pvigencia = "24 Meses";
}

if($Periodo_Vigencia){
	$pvigencia   = $Periodo_Vigencia. " Meses";   
}

$naf     = odbc_result($cur, "naf");
$napce   = odbc_result($cur, "napce");
$siren   = odbc_result($cur, "siren");
$dossier = odbc_result($cur, "dossier");
$quest   = odbc_result($cur, "quest");
$contrat = odbc_result($cur, "contrat");

if($prMax > 0 || $prMin > 0 || $sentOffer){
	$reestudo = 1;
}

$test = 3;
$i = 1;

for (;$i <= 5; $i++) {
	if (odbc_result($cur,$i) != $test) 
		$ok = false;
}

if ($i == 1){
	$ok = false;
}

$r = false;

if (!$ok){
	$forward = "error";
}else{
	if($mot == "Recusar"){
		$r = odbc_exec($db, "insert into TransactionLog (idUser, description) values ($userID, 'Informe recusado [$name]')");

		//criado por Wagner 29/08/2008
		if($r){
			$log_query .= "insert into TransactionLog (idUser, description) values ($userID, 'Informe recusado [$name]')";
		}

		//Registrar no Log (Sistema) - Criado Por Tiago V N - 03/10/2005
		// Tipo Log = 10 - Informe Recusado

		$sql  = "Insert Into Log (tipoLog, id_User, Inform, data, hora) Values
		('10', '$userID', '$idInform','".date("Y")."-".date("m")."-".date("d")."',".
			"'".date("H").":".date("i").":".date("s")."')";
$result = odbc_exec($db, $sql);

if ($result) {
	$sql_id = "SELECT @@IDENTITY AS 'id_Log'";        
	$cur = odbc_result(odbc_exec($db, $sql_id), 1);

	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) values ".
	"('$cur', 'Informe', '$pvigencia', 'Recusado')";
	$rs =  odbc_exec($db, $sql);	

	if ($rs) {
		$sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
		$cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);

		$sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
		"values ('$cur', '".str_replace("'","",$log_query)."')";

			  	//echo $sql;
		odbc_exec($db, $sql);
	   		}//fim if	
	   	}else{
	   		$msg = "Erro na inclusão do Log";
	   	}
	   }

	   if ($field->getField("mot") == "Aceitar"){
	   	// print '?'.$sisseg;
	   	if (empty($contrat)) {
	   		$msg = "ATENçÃO!!! Numero do contrat está vazio.";
	   	}elseif (empty($naf)) {
	   		$msg = "ATENÇÃO!!! Numero do Naf está vazio.";
	   	}elseif (empty($napce)) {
	   		$msg = "ATENÇÃO!!! Numero do Napce está vazio.";
	   	}elseif (empty($siren)) {
	   		$msg = "ATENÇÃO!!! Numero do Siren está vazio.";
	   	}elseif (empty($dossier)) {
	   		$msg = "ATENÇÃO!!! Numero do Dossier está vazio.";
	   	}elseif (empty($quest)) {
	   		$msg = "ATENÇÃO!!! Numero do Questionnaire está vazio.";
	   	}else{
	   		// insere o segurado e os importadores no SisSeg
	   		$idSegurado = 0;

       		// aqui verifica se o cliente existe no sisseg
	   		//if ($sisseg && !$idAnt){ // se nao for renovacao	   

	   		//Verifica  se o produto é externo e inclui no sisseg	   
	   		if($SistemaDestino == 2){
	   			if (!$i_Seg && !$idAnt){	
	   				require ("segSisSeg.php");
		   		}else if($idAnt && !$idSegurado > 0){ // se for renovacao
		   			$idSegurado = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
		   		}
		   	}else{
		   		$idSegurado = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
		   	}

		   	$r = false;

		   	if ($ok){
		  		//Alterado por Tiago V N - Elumini - 18/11/2005

		   		$qry = "  UPDATE Inform SET".
		   		"    idUser = ". $userID. ",".
				 	($idSegurado ? "    i_Seg = $idSegurado," : '').  //id_Seg
				 	" dataPreench = getDate(),".
				 	"   state = 3 ".
				 	"  WHERE id = $idInform";
				 	$r = odbc_exec($db,$qry);

				 	$x = odbc_exec($db, "select id from AnaliseInform where idInform=$idInform");

				//criado por Wagner 29/08/2008
				 	if($r){
				 		$log_query .= "  UPDATE Inform SET".
				 		"    idUser = ". $userID. ",".
					 	($idSegurado ? "    i_Seg = $idSegurado," : '').  //id_Seg
					 	" dataPreench = getDate(),".
					 	"   state = 3 ".
					 	"  WHERE id = $idInform";
					 }

					 if(! odbc_fetch_row($x)){
					 	$w = odbc_exec($db, "insert into AnaliseInform (idInform, inicio) values ($idInform, getdate())");			
			  		//criado por Wagner 29/08/2008
					 	if($w){
					 		$log_query .= "insert into AnaliseInform (idInform, inicio) values ($idInform, getdate())";
					 	}
					 }

					 if (!$r){
					 	$msg = "problemas na atualização do informe";
					 } else {
			 		//HICOM Alterado por GPC 28/04/2004 - incluir o not in.....  e hold = 0
					 	$query = "UPDATE Importer SET hold = 0 WHERE idInform = $idInform ";
					 	odbc_exec($db, $query);

					 	$r = $notif->newCredit($userID, $name, $idInform, $db, 12);

					 	if (!$r){
					 		$msg = "A notificação já foi enviada para a área de crédito. Clique em voltar para retornar.";
					 	}

					// $imp = odbc_exec ($db,"SELECT id, hold FROM Importer WHERE idInform = $idInform ORDER BY id");


					//$count = 0;

					//   			while (odbc_fetch_row ($imp)) {
					//  			if ($count < 10){
					//   			$query = "UPDATE Importer SET hold = 0 WHERE id =";
					//  			}else if (odbc_result ($imp, 'hold') != 0){
					//  	 		$query = "UPDATE Importer SET hold = 1 WHERE id =";
					//  			}
					//  		$query .= odbc_result ($imp, 'id');
					//  		$b = odbc_exec ($db, $query);
					// if($b){
					//      	$log_query .=$query;
					// }
					// 			//}
					// $count ++;
					//  			}

					// $qry = "select imp.* 
					//  	      	from Importer imp 
					//      	 	where 
					// imp.hold=0
					// and imp.idInform=$idInform 
					// and imp.state=1 
					//          		and  (imp.id NOT IN (SELECT idImporter FROM ImporterRem WHERE idImporter = imp.id))";
					// $x = odbc_exec($db, $qry);

					// if(odbc_fetch_row($x)){
					// //echo "Envia para credito<BR>";
					// // $r = $notif->newCredit($userID, $name, $idInform, $db, 12);

					// // if (!$r){
					// //    		$msg = "A notificação já foi enviada para a área de crédito. Clique em voltar para retornar.";
					// // }		 
					// }else if($reestudo){

					//     	06-06-2010 - Interaktiv  - Elias Vaz
					// Alteração: Foi comentado o trecho de código abaixo sob solicitação do Sr. Cleber da técnica
					// Motivo: Todos os informes de renovação deverão passar primeiro pela análise de crédito. 


					// /*
					// //echo "Envia para Tarifacao<BR>";
					// $r = $notif->newTarif($userID, $name, $idInform, $db);
					// $r = odbc_exec($db, "UPDATE Inform SET state = 4 WHERE state <= 4 And id = $idInform");

					// //criado por Wagner 29/08/2008
					// if($r)
					// {
					// $log_query .= "UPDATE Inform SET state = 4 WHERE state <= 4 And id = $idInform";

					// }

					// //print '?'.$log_query;
					// */
					// 	}


					 }
					}

			//Registrar no Log (Sistema) - Criado Por Tiago V N - 03/10/2005
			// Tipo Log = 9 - Validando Informe

			$sql  = "Insert Into Log (tipoLog, id_User, Inform, data, hora) Values
			('9', '$userID', '$idInform','".date("Y")."-".date("m")."-".date("d")."',".
				"'".date("H").":".date("i").":".date("s")."')";
			$result = odbc_exec($db, $sql);

		if ($result) {
			$sql_id = "SELECT @@IDENTITY AS 'id_Log'";        
			$cur = odbc_result(odbc_exec($db, $sql_id), 1);

			$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) values ".
			"('$cur', 'Informe', '$pvigencia', 'Validação')";
			$rs = odbc_exec($db, $sql);	
		}else{
			$msg = "Erro na inclusão do Log";
		}
	}
} else if ($mot == "OK") {
	   	$r = true;
}else{
	$r = odbc_exec($db,
		"UPDATE Inform SET".
		"  state = 9".
		"  WHERE id = $idInform");
	$x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");

	if(odbc_fetch_row($x)){
		$id = odbc_result($x, 1);
		$fim = odbc_result($x, 2);

		if(! $fim){
			odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
		}
	}
}

	if ($r){
	   	if ($notif->doneRole($idNotification, $db)) {
	   		odbc_commit ($db);
	       		//if(! $msg) {
	   		if ($mot == 'OK') {
	   			$msg = "Informe disponível para alterações do cliente";
	   		}else if ($mot == 'Aceitar'){ 
	   			$msg = 'Informe aceito';
	   		}else{
	   			$msg = "Processo Cancelado";
	   		}
      			//}

	   		$forward = "success";
	   		$forwardNew = "success"; 
	   	}else{
	   		$msg = "Problemas na desativação da notificação";
	   		odbc_rollback ($db);
	   	}
	}else{
     		//HiCom---  Se deu M com r....  nao atualiza banco....
	   	odbc_rollback ($db);
	}
}

	if ($erroSisSeg=='1') {

	}else{
		odbc_autocommit ($db, true);
	}
//print 'oi'.$msg;
	?>