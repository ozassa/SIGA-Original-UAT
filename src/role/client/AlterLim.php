<?php  //ALTERADO HICOM
// Alterado Hicom (Gustavo) 25/01/2005 - inclusão do campo divulgaNome em Importer

require_once("../rolePrefix.php");

$log_query = "";

$userID  = $_SESSION['userID'];

$ok = true;
$changed = 0;
odbc_autocommit ($db, false);


$editCreditTxt = $field->getField("edit");
$editCredit    = $field->getNumField("edit");   //valor
$idBuyer       = $field->getField("importer");        //importador
$creditTemp    = $field->getNumField("creditTemp");  //credito temporario
$limTemp       = $field->getField("limTemp");       //limite do credito temporario
$concedido     = $field->getNumField('concedido');

$y = odbc_exec($db, "select idAnt, state from Inform where id=$idInform");
$idAnt = odbc_result($y, 1);
$status = odbc_result($y, 2);



if($editCredit > $concedido){
  $x = odbc_exec($db, "select * from AnaliseImporter where idImporter=$idBuyer and fim is null");
  if(! odbc_fetch_row($x) && $status == 10){
    $x = odbc_exec($db, "insert into AnaliseImporter (idImporter, inicio) values ($idBuyer, getdate())");
	
	//criado por Wagner 29/08/2008
	if($x)
	{
		$log_query .= "insert into AnaliseImporter (idImporter, inicio) values ($idBuyer, getdate())";
	}
  }
}

$query = "SELECT inf.name, imp.limCredit, ch.credit, ch.analysis, ch.monitor, ch.limTemp, ch.creditTemp,
                 imp.c_Coface_Imp, imp.idCountry, imp.idTwin
	  FROM (
                SELECT idImporter, credit, analysis, monitor, limTemp, creditTemp
                FROM   ChangeCredit ch
                WHERE  id IN
                     (
                       SELECT max (id)
                       FROM   ChangeCredit
                       GROUP BY idImporter
                     )
               ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id)
          JOIN Inform inf ON (imp.idInform  = inf.id)
	  WHERE imp.id = $idBuyer";



$cur = odbc_exec($db,$query);
if(odbc_fetch_row($cur)) {
  $oldConcCredit  = odbc_result($cur, "credit");
  $analysis   = odbc_result($cur, "analysis");
  $monitor    = odbc_result($cur, "monitor");
  $creditTemp = odbc_result($cur, "creditTemp");
  $limTemp = odbc_result($cur, "limTemp");
  $ci = odbc_result($cur, 'c_Coface_Imp');
  $idCountry = odbc_result($cur, 'idCountry');
  $idOther = odbc_result($cur, 'idTwin');
  if ($analysis == '') $analysis = 0;
  if ($monitor == '') $monitor = 0;
  if ($oldConcCredit == "") $oldConcCredit = 0;
  $oldSolicCredit = odbc_result($cur, "limCredit");
  $nameCl = odbc_result($cur, "name");
  if(! $idOther){
    $y = odbc_exec($db, "select id from Importer where idTwin=$idBuyer");
    $idOther = odbc_result($y, 1);
  }

  //HICOM
  // Verificar se o importador tem renovacao
  $y = odbc_exec($db, "select id, idInform, state from Importer where idTwin=$idBuyer");
  if(odbc_fetch_row($y))
  {
     $hc_idImporter_ren = odbc_result($y, 1);
     $hc_idInform_ren = odbc_result($y, 2);
     $hc_state_Importer_ren = odbc_result($y, 3);
     $y = odbc_exec($db, "select state from Inform where id=$hc_idInform_ren");
     $hc_state_Inform_ren = odbc_result($y, 1);
  } else {
     $hc_idImporter_ren = 0;
     $hc_idInform_ren = 0;
     $hc_state_Importer_ren = 0;
	 $hc_state_Inform_ren = 0;
  }


  if ($editCreditTxt != "") {
    $r = odbc_exec($db, "UPDATE Importer SET limCredit = $editCredit, state = 2 WHERE id = $idBuyer");
    
	if ($r == 0) 
	   $ok = false;

	//criado por wagner 29/08/2008
	
	
	if($r)
	{
		$log_query .= "UPDATE Importer SET limCredit = $editCredit, state = 2 WHERE id = $idBuyer";
	}
	
    $query =
      "INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state)
       VALUES ($oldConcCredit, $editCredit, $userID, $idBuyer, $analysis, $monitor, 2)";
    if ($limTemp != '')
      $query =
	"INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state, creditTemp, limTemp)
         VALUES ($oldConcCredit, $editCredit, $userID, $idBuyer, $analysis, $monitor, 2, $creditTemp, '$limTemp')";

    
    $r = odbc_exec ($db, $query);
    if ($r == 0) 
	   $ok = false;
	
	//criado por wagner 29/08/2008
	if($r)
	{
		$log_query .= $query;
	}

	//echo "$includeOld " . $includeOld . "  idOther: " . $idOther;


	// HiCom, parece que nunca entra aqui.....
    if($idOther && $includeOld){ // se escolheu incluir no informe vigente

	  //echo "Entrei!";

      $query =
	  "INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state)
       VALUES ($oldConcCredit, $editCredit, $userID, $idOther, $analysis, $monitor, 2)";
      if ($limTemp != '')
	     $query =
	     "INSERT INTO ChangeCredit
           (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state, creditTemp, limTemp)
           VALUES($oldConcCredit, $editCredit, $userID, $idOther, $analysis, $monitor, 2, $creditTemp, '$limTemp')";

      $r = odbc_exec ($db, $query);
      if ($r == 0) $ok= false;
	  
		//criado por wagner 29/08/2008
		if($r)
		{
			$log_query .= $query;
		}
	
    }

	//HiCom, vamos continuar o tratamento....

	//Obter os valores de analise e monitoramento para o caso
	//$hc_str = " Select monitor, analysis FROM from ChangeCredit WHERE idImporter = " . $hc_idImporter_ren . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $hc_idImporter_ren . " ";
	//$hc_c = odbc_exec($db, "select id, idInform, state from Importer where idTwin=$idBuyer");





	if ($hc_idImporter_ren > 0 && $hc_state_Inform_ren < 9)
	{

	   //$hc_str = " Insert into ChangeCredit ";
       //$hc_str = $hc_str . " (idImporter, credit, idNotificationR, cookie, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic, creditTemp, limTemp) ";
       //$hc_str = $hc_str . " (select idImporter, credit, idNotificationR, cookie, -6 , state, stateDate, monitor, analysis, creditDate, " . $lim . " , creditTemp, limTemp ";
       //$hc_str = $hc_str . " from ChangeCredit WHERE idImporter = " . $idBuy . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $idBuy . " and state = 1   ))";
       //odbc_exec($db, $hc_str);


	   // tem renovacao e inform em trabalho...
	   // testa se é novo, se for, deixa como esta
	   if ($hc_state_Importer_ren == 1)
	   {
	      // Só altera o limcredit, sem alterar o status....
		  $r = odbc_exec ($db, "UPDATE Importer SET limCredit = $editCredit WHERE id = $hc_idImporter_ren");
          if ($r == 0) $ok = false;
		  
			//criado por wagner 29/08/2008
			if($r)
			{
				$log_query .= "UPDATE Importer SET limCredit = $editCredit WHERE id = $hc_idImporter_ren";
			}

		  if (ok)
		  {



             $query =
	         "INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state)
             VALUES ($oldConcCredit, $editCredit, $userID, $hc_idImporter_ren, $analysis, $monitor, 1)";
             //Hicom
		     $query = " Insert into ChangeCredit ";
             $query = $query . " (idImporter, credit, idNotificationR, cookie, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic) ";
             $query = $query . " (select idImporter, ". $oldConcCredit . ", idNotificationR, cookie, " . $userID . " , 1, getdate(), monitor, analysis, getdate(), " . $editCredit . "  ";
             $query = $query . " from ChangeCredit WHERE idImporter = " . $hc_idImporter_ren . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $hc_idImporter_ren . "  ))";
             //fim hicom


             if ($limTemp != '')
			 {
	            $query =
	            "INSERT INTO ChangeCredit
                (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state, creditTemp, limTemp)
                VALUES($oldConcCredit, $editCredit, $userID, $hc_idImporter_ren, $analysis, $monitor, 1, $creditTemp, '$limTemp')";
                //Hicom
		        $query = " Insert into ChangeCredit ";
                $query = $query . " (idImporter, credit, idNotificationR, cookie, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic, creditTemp, limTemp) ";
                $query = $query . " (select idImporter, ". $oldConcCredit . ", idNotificationR, cookie, " . $userID . " , 1, getdate(), monitor, analysis, getdate(), " . $editCredit . " , $creditTemp, '$limTemp' ";
                $query = $query . " from ChangeCredit WHERE idImporter = " . $hc_idImporter_ren . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $hc_idImporter_ren . "  ))";
				//fim Hicom
             }

             $r = odbc_exec ($db, $query);


             if ($r == 0) $ok= false;

			//criado por wagner 29/08/2008
			if($r)
			{
				$log_query .= "UPDATE Importer SET limCredit = $editCredit WHERE id = $hc_idImporter_ren";
			}
			
		  }

	   } else {

	      // Altera o status para 2, e altera o limCredit....
		  $r = odbc_exec ($db, "UPDATE Importer SET limCredit = $editCredit, state = 2 WHERE id = $hc_idImporter_ren");
          if ($r == 0) $ok = false;

		//criado por wagner 29/08/2008
		if($r)
		{
			$log_query .= "UPDATE Importer SET limCredit = $editCredit, state = 2 WHERE id = $hc_idImporter_ren";
		}
		  
		  if (ok) {


             $query =
	         "INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state)
             VALUES ($oldConcCredit, $editCredit, $userID, $hc_idImporter_ren, $analysis, $monitor, 2)";

             //Hicom
		     $query = " Insert into ChangeCredit ";
             $query = $query . " (idImporter, credit, idNotificationR, cookie, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic) ";
             $query = $query . " (select idImporter, ". $oldConcCredit . ", idNotificationR, cookie, " . $userID . " , 2, getdate(), monitor, analysis, getdate(), " . $editCredit . "  ";
             $query = $query . " from ChangeCredit WHERE idImporter = " . $hc_idImporter_ren . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $hc_idImporter_ren . "  ))";
             //fim hicom




             if ($limTemp != '')
			 {
	            $query =
	            "INSERT INTO ChangeCredit
                (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state, creditTemp, limTemp)
                VALUES($oldConcCredit, $editCredit, $userID, $hc_idImporter_ren, $analysis, $monitor, 2, $creditTemp, '$limTemp')";

                //Hicom
		        $query = " Insert into ChangeCredit ";
                $query = $query . " (idImporter, credit, idNotificationR, cookie, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic, creditTemp, limTemp) ";
                $query = $query . " (select idImporter, ". $oldConcCredit . ", idNotificationR, cookie, " . $userID . " , 2, getdate(), monitor, analysis, getdate(), " . $editCredit . " , $creditTemp, '$limTemp' ";
                $query = $query . " from ChangeCredit WHERE idImporter = " . $hc_idImporter_ren . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $hc_idImporter_ren . "  ))";
				//fim Hicom

             }

             $r = odbc_exec ($db, $query);
			
			//criado por wagner 29/08/2008
			if($r)
			{
				$log_query .= $query;
			}

             if ($r == 0) $ok= false;


		  }

	   }

	}

	//odbc_rollback ($db);
	//die();


    if ($ok){
        $changed++;
        odbc_commit ($db);
    }else{
        odbc_rollback ($db);
    }

  }

}


if ($ok) {
  $msg = "Solicitação Finalizada com Sucesso para $changed Comprador(es).";
  if ($hc_idImporter_ren > 0 && $hc_state_Inform_ren < 9)
  {
     $msg = $msg . "<BR>Alteração também realizada no informe de renovação!";
  }
  if ($changed > 0)
  {
    $r = $notif->clientChangeCredit($userID, $nameCl, $idInform, $db, 10);

	if ($hc_idImporter_ren > 0 && $hc_state_Inform_ren < 9)
    {
	   //$r = $notif->clientChangeCredit($userID, $nameCl, $hc_idInform_ren, $db, 10);
	}
  }
} else {
  $msg = "problemas na atualização dos limites solicitados.";
}
odbc_autocommit ($db, true);

// alterado hicom
if ($divulgaNome == 1 && !$divulgaNomeOrig) {

	$hc_cur = odbc_exec($db, "Update Importer set divulgaNome = $divulgaNome where id = $idBuyer");
	
	//criado por wagner 29/08/2008
	if($hc_cur)
	{
		$log_query .= "Update Importer set divulgaNome = $divulgaNome where id = $idBuyer";
	}
	
	$idImporter = $idBuyer;
//	require("..\inform\divulgaNomeMail.php");

 	if ($hc_result != "OK") {
 		if ($msg)
			$msg .= ". ".$hc_result;
		else
			$msg = $hc_result;
 	}
}
// fim


$comm = "open";


//Registrar no Log (Sistema) - Criado Por Tiago V N - 03/07/2006
// Tipo Log = Alteração de Limite de Crédito (Tela Cliente) - 42
$sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('42'," .
        "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
        "','".date("H").":".date("i").":".date("s")."')";


if (odbc_exec($db, $sql) ) {
    $sql_id = "SELECT @@IDENTITY AS 'id_Log'";
    $cur = odbc_result(odbc_exec($db, $sql_id), 1);
    $sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
           "values ('$cur', 'id', '$idBuyer', 'Limite Alterado do Comprador')";
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
