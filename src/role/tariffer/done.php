<?php 

$ok = true;
$forward = "error";
odbc_autocommit($db, false);

if ($mensagem == "1") {
	$msg = "Informe cancelado com sucesso.";
} else {
  	$msg = "Informe enviado para o executivo de contas";
}

// soma dos campos A,B,C
$cur = odbc_exec($db,
	"SELECT vol2 + vol3 + vol4 FROM Volume WHERE idInform = ".
	$field->getField("idInform"));

$abc = 0;

if(odbc_fetch_row($cur)){
  	$abc = odbc_result($cur, 1);
}

$cur = odbc_exec($db, "SELECT idRegion, name, prMTotal, idAnt, txRise, txMin, sentOffer FROM Inform WHERE id = $idInform");

if(odbc_fetch_row($cur)){
  	$idRegion = odbc_result($cur, 1);
  	$name = odbc_result($cur, 2);
  	$old_prMin = odbc_result($cur, 3);
  	$old_txMin = odbc_result($cur, 6);
  	$old_txRise = odbc_result($cur, 5);
  	$idAnt = odbc_result($cur, 4);
  	$sentOffer = odbc_result($cur, 7);

  	if($idAnt){
    		$x = odbc_exec($db, "select prMin, txMin from Inform where id=$idAnt");
    		$prMinAnt = odbc_result($x, 1);
    		$txMinAnt = odbc_result($x, 2);
  	}
}

if($field->getField("mot") == "OK") {//Fazer a Verificação Abaixo do Codigo aqui.
  	/*
   	* 
   	* Verificar se o limite de credito não esta dentro do LMI maximo.
   	* Criado por Tiago V N 30/05/2007
  	*/
  
	/*
  	$strSQL = "SELECT max(ch.credit+isnull(ch.creditTemp, 0)) as somaCredit FROM ( SELECT idImporter, credit, creditDate, creditTemp, limTemp 
		FROM ChangeCredit ch WHERE ch.id IN 
		( SELECT max (id) FROM ChangeCredit GROUP BY idImporter ) ) ch RIGHT 
		JOIN Importer imp ON (ch.idImporter = imp.id) JOIN Inform inf ON (imp.idInform = inf.id) 
		JOIN Country c ON (imp.idCountry = c.id) WHERE inf.id = '$idInform' 
		AND (imp.state = 6 OR ((imp.state = 2 OR imp.state = 4) 
		AND NOT ch.credit IS NULL))"; 
  
    
   	$teste = odbc_exec($db, $strSQL);
   	odbc_fetch_row($teste);
  
   	$somaCredit = str_replace(".", "", odbc_result($teste, 1));
	*/

  
   	$premioT = ($field->getNumField("prMin")* $field->getNumField("limPag"));
	  
	/*
	* Esta verificação faz o seguinte:
	* (premio * pgto de indenização) <= (Ao maio limite da ficha de credito)
	*/

	//  if ($premioT <= $somaCredit ) {

	if($idEndosso){
		$msg = "Informe enviado para o Endosso";
		$r = "SELECT codigo, bornDate FROM Endosso WHERE id = $idEndosso";
		$rr = odbc_exec($db, $r);
		$codigo = odbc_result($rr, 1);
		$dateEnv = odbc_result($rr, 2);
		list($ano, $mes, $dia) = explode('-', $dateEnv);
		$codE = $codigo."/".$ano;
	
		if($tipo == "natOper"){
			$query =
				"insert into EndossoPremio (idEndosso, premioOld, premio, txMinOld, txMin, txRise, motivo)
				values ($idEndosso, $old_prMin, ". $field->getNumField("prMin") . ", $old_txMin, ".
				$field->getNumField("txMin") . ", $old_txRise, 'Endosso de Natureza de Operação')";
				 //echo "<pre>1:$query<br></pre>";
				$cur = odbc_exec($db, $query);
				//echo "<pre>cur:$cur<br></pre>";

			if ($cur){
				$id = odbc_result(odbc_exec($db, "select max(id) from EndossoPremio where idEndosso=$idEndosso"), 1);

				if(!$notif->newPrMinNatOper($idInform, $name, $db, $idEndosso, $id, $codE)){
					echo odbc_errormsg();
				}else{
					odbc_exec ($db, "UPDATE Endosso SET state = 4 WHERE id = $idEndosso");
				}
			}
		}else{
			$query = "update EndossoPremio set premioOld=$old_prMin, premio= " . $field->getNumField("prMin") . ", txMinOld=$old_txMin, txMin= " . $field->getNumField("txMin")/100 . ", txRise=$old_txRise where id=$idPremio";

			$c = odbc_exec($db, $query);

			if(! $c){
				$msg = "Erro ao inserir dados do endosso"; 
				return;
			}

			if(!$notif->newEndossoPrMin($idInform, $name, $db, $idEndosso, $idPremio, $codE)){
				echo odbc_errormsg();
			}else{
				odbc_exec ($db, "UPDATE Endosso SET state = 4 WHERE id = $idEndosso");
			}
		}
	}else{
		if ($mensagem != 1) {
			$qry =  "UPDATE Inform SET".
				"  tarifDate         	= getdate(),".
				"  txMax             	= ". ($field->getNumField("txMax") / 100). ",".
				"  txMin             	= ". ($field->getNumField("txMin") / 100). ",".
				"  prMax             	= ". $field->getNumField("prMax"). ",".
				"  prMin             	= ". $field->getNumField("prMin"). ",".
				"  limPagIndeniz     	= ". $field->getNumField("limPag"). ",".
				"  percCoverage      	= ". $field->getNumField("cobertura"). ",".
				"  pvigencia	        = ". $field->getNumField("pvigencia") . ",".
				"  Periodo_Vigencia 	= ". $field->getField("Periodo_Vigencia") . ",".
				"  v_LMI          	  = ". str_replace(',','.',str_replace('.','',$field->getField("v_LMI"))) . ",".
				"  state             	= 5".",".
				"  warantyInterest   	= ". $field->getNumField("jurosMora").
				"  WHERE id          	= $idInform";
			$r = odbc_exec($db,$qry);
		}
	}

	$x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");

	if(odbc_fetch_row($x)){
		$id = odbc_result($x, 1);
		$fim = odbc_result($fim, 2);

		if(! $fim){
			odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
		}
	}

	$r = odbc_exec($db,
		"INSERT INTO ChangeTarif (idInform, prMin, prMax, txMin, txMax) VALUES ($idInform,".
		$field->getNumField("prMin"). ",".
		$field->getNumField("prMax"). ",".
		($field->getNumField("txMin") / 100). ",".
		($field->getNumField("txMax") / 100). ")");

	if ($mensagem != 1) {
		if ($r) {
			if(! $idEndosso){
		          	if(($prMin == $prMinAnt && $txMin == $txMinAnt) || $sentOffer){
		               		$notif->waitOffer($userID, $idRegion, $name, $idInform, $db);
		          	}else{
		               		$notif->newOffer($userID, $idRegion, $name, $idInform, $db);
		     		}
			}
		}else 
	    		$msg = "campos não numéricos";
	}
    	//Criado Por Tiago V N - 19/10/2005  ----  Alterado por Fábio Campos em 04/01/2006
   	//Log do Tarifação ( Informe Tarifado )

    	if ($mensagem == 1) {
    		//Log do Tarifação ( Informe Cancelado )
		$sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('6'," .
			"'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
          		"','".date("H").":".date("i").":".date("s")."')";

		if (odbc_exec($db, $sql) ) {
			$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
			$cur = odbc_result(odbc_exec($db, $sql_id), 1);

			$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               			"values ('$cur', 'Tarifação', '$idInform', 'Cancelamento')";
			odbc_exec($db, $sql);
    		}else{
			$msg = "Erro no incluir do Log";
 		}
     	} else {
         	$sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('17'," .
           		"'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           		"','".date("H").":".date("i").":".date("s")."')"; 

		if (odbc_exec($db, $sql) ) {
			$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
			$cur = odbc_result(odbc_exec($db, $sql_id), 1);

			$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
				"values ('$cur', 'Tarifação', 'vazio', 'Inclusão')";
			odbc_exec($db, $sql);	   

		}else{
			$msg = "Erro no incluir do Log";
		}
	}
}//O Processo da Tarifação termina aqui.

if($r){
  	if($notif->doneRole($idNotification, $db)) {
    		odbc_commit($db);
    		$forward = "success";
  	}else{
    		//$msg = "";
    		odbc_rollback($db);
  	}
}

odbc_autocommit($db, true);
?>