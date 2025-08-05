<?php //---- HICOM 14/04/2004 ------
 // Alterado Hicom (Gustavo) 02/12/2004 - inclus„o do campo divulgaNome e emailContato em Importer 

 //criado por Wagner 1/9/2008
 
 $log_query .="";
	 
	$idBuy        = $_REQUEST['idBuy']; 
	$userID       = $_SESSION['userID'];
	
	$ValorCorr    =  str_replace('.','',$_REQUEST["prevExp12"]);
	$ValorCorr    =  str_replace(',','.',$ValorCorr);
	
	$limCredit    =  str_replace('.','',$_POST["limCredit"]);
	$limCredit    =  str_replace(',','.',$limCredit);
	
	$expMax       =  str_replace('.','',$_POST["expMax"]);
	$expMax       =  str_replace(',','.',$expMax);


 
/*if (!is_numeric($field->getNumField("prevExp12")) || !is_numeric($field->getNumField("numShip12"))) {
  $msg = "Campo n„o numÈrico";
}else if ($field->getField("name") == "" || $field->getField("address") == "" || $field->getNumField("country") == "") {
  $msg = "Todos os campos s„o obrigatÛrios";
}else{*/
  //----------- HICOM 14/04/2004---------------------------------
     $name    = str_replace("'","", strtoupper($field->getField("name")));
	 
	 $address = str_replace("'","", strtoupper($field->getField("address")));
	 $address = str_replace("'","", strtoupper($field->getField("address")));
	 
	 $city = str_replace("'","",  strtoupper($field->getField("city")));
	 $city = str_replace("'","", strtoupper($field->getField("city")));
	 
	 $contact = str_replace("'","",  strtoupper($field->getField("contact")));
	 $contact = str_replace("'","", strtoupper($field->getField("contact")));
     $cnpj = str_replace("'","",  $field->getField("cnpj"));
	

  //-----------------------------------------------------------
  
  
  $idBuy = (int) $_GET['idBuy']; // Certifique-se de validar o dado de entrada corretamente

$sql = "SELECT inf.prMax, inf.prMin, inf.sentOffer, imp.prevExp12, imp.numShip12, imp.periodicity, inf.idAnt, inf.state 
        FROM Inform inf, Importer imp 
        WHERE imp.idInform = inf.id AND imp.id = ?";

$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$idBuy]);

odbc_fetch_row($cur);

$prMax = odbc_result($cur, 1);

// Certifique-se de fechar o cursor quando n„o for mais necess·rio

  $prMin = odbc_result($cur, 2);
  $sentOffer = odbc_result($cur, 3);
  
  $hc_prevExp12 = odbc_result($cur, 4);
  $hc_numShip12 = odbc_result($cur, 5);
  $hc_periodicity = odbc_result($cur, 6);
  $hc_idAnt = odbc_result($cur, 7);
  $hc_state = odbc_result($cur, 8);
  
  //Alterado HiCom
  $hc_reestudo = 0;
  $hc_altera_novo = 0;
  $hc_str_novo = "";
  
  if($prMax > 0 || $prMin > 0 || $sentOffer)
  {
  
    $hc_reestudo = 1;
	
	//echo "… reestudo....<BR>";
	
	if (($hc_prevExp12 != $field->getNumField("prevExp12")) || ($hc_numShip12 != $field->getNumField("numShip12")) || ($hc_periodicity != $field->getNumField("periodicity")))
	{
	   $hc_altera_novo = 1;
	   $hc_str_novo = " state = 1, ";
	   // Se colocou o State para novo, devemos alterar o ChangeCredit //
	   //echo "… (" . $hc_str_novo . ") reestudo....<BR>";
	   
	} 
  }
// Alterado Hicom (Gustavo) 02/12/2004 - inclus„o do campo divulgaNome em Importer

/*
Alterado por Tiago V N - 04/11/2005
Motivo - A class de passagem de variavel estava passando a variavel periodicity.
*/
odbc_free_result($cur);

  $sql = "UPDATE Importer
        SET
          name = ?,
          address = ?,
          idCountry = ?,
          tel = ?,
          cep = ?,
          contact = ?,
          fax = ?,
          relation = ?,
          seasonal = ?,
          prevExp12 = ?,
          numShip12 = ?,
          periodicity = ?,
          city = ?,
          przPag = ?,
          divulgaNome = ?,
          emailContato = ?,
          cnpj = ?,
          expMax = ?,
          limCredit = ?
        WHERE id = ?";

$params = [
    $name,
    $address,
    $field->getField("idCountry"),
    str_replace("'", "", $field->getField("tel")),
    str_replace("'", "", $field->getField("cep")),
    $contact,
    str_replace("'", "", $field->getField("fax")),
    str_replace("'", "", $field->getField("relation")),
    str_replace("'", "", $field->getField("seasonal")),
    $ValorCorr,
    str_replace("'", "", $_REQUEST["numShip12"]),
    str_replace("'", "", $_REQUEST["periodicity"]),
    $city,
    $field->getField("przPag"),
    $field->getField("divulgaNome"),
    str_replace("'", "", $field->getField("emailContato")),
    $cnpj,
    $expMax,
    $limCredit,
    $idBuy
];

$cur = odbc_prepare($db, $sql);
odbc_execute($cur, $params);

// Certifique-se de fechar o cursor apÛs o uso
odbc_free_result($cur);
 	 
  

  
  if (!$cur){
    // ------ HICOM 14/04/2004 ------------  
	  //$msg = "Problemas na atualizaÁ„o da base<br>";
	
	    if(preg_match("/[\/'·ÈÌÛ˙‡‚ÍÓÙ˚„ı¸¡…Õ”⁄¿¬ Œ‘€√’‹]/", $name))
		{
              $msg = "Na Raz„o Social do importador n„o s„o permitidos acentos ou barras (/)";
		} 
	    else
		{
	      $msg = "Problemas na atualizaÁ„o da base<br>";
		} 	
		
	   $comm = 'altBuy';
	   $idBuy = $idBuy;
	   $erro = 'true';
	   $name_erro = $name;
       //echo odbc_errormsg();
    //-------------------------------------
  }else{
  
    //print '?'.$hc_altera_novo;
    // HICOM // Vamos testar se aconteceu altaracao no ChangeCredit
	if ($hc_altera_novo == 1) {
    $hc_str = "INSERT INTO ChangeCredit 
                (idImporter, credit, idNotificationR, cookie, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic, creditTemp, limTemp) 
                SELECT idImporter, credit, idNotificationR, cookie, ?, state, stateDate, monitor, analysis, creditDate, creditSolic, creditTemp, limTemp 
                FROM ChangeCredit 
                WHERE idImporter = ? AND id = (SELECT MAX(c.id) FROM ChangeCredit c WHERE idImporter = ? AND state = 1)";

    $params = [-5, $idBuy, $idBuy];
    $stmt = odbc_prepare($db, $hc_str);
    $result = odbc_execute($stmt, $params);

    if ($result) {
        $log_query .= $hc_str;
    }

    odbc_free_result($stmt); // Libera o cursor
}

if ($result) {
    $update_sql = "UPDATE Importer SET
                    name = ?,
                    address = ?,
                    idCountry = ?,
                    tel = ?,
                    cep = ?,
                    contact = ?,
                    fax = ?,
                    relation = ?,
                    seasonal = ?,
                    prevExp12 = ?,
                    numShip12 = ?,
                    periodicity = ?,
                    city = ?,
                    przPag = ?,
                    divulgaNome = ?,
                    emailContato = ?,
                    cnpj = ?,
                    expMax = ?,
                    limCredit = ?
                   WHERE id = ?";

    $update_params = [
        $name,
        $address,
        $idCountry,
        $tel,
        $cep,
        $contact,
        $fax,
        $relation,
        $seasonal,
        str_replace("'", "", $_REQUEST["prevExp12"]),
        str_replace("'", "", $_REQUEST["numShip12"]),
        str_replace("'", "", $_REQUEST["periodicity"]),
        $city,
        $przPag,
        $divulgaNome,
        $emailContato,
        $cnpj,
        $expMax,
        $limCredit,
        $idBuy
    ];

    $stmt_update = odbc_prepare($db, $update_sql);
    $result_update = odbc_execute($stmt_update, $update_params);

    if ($result_update) {
        $log_query .= $update_sql;
    }

    odbc_free_result($stmt_update); // Libera o cursor
}

	
	
	
	
	
	
	
	
	// HICom, Vamos analizar as alteracoes no credito solicitado	
	
	//echo "-- idAnt: " . $hc_idAnt . "  STATE: " . $hc_state . "<BR> ";
	
	if ($hc_idAnt > 0 && $hc_state < 6)
	{
	   // Renovacao 
	   if (($hc_prevExp12 != $field->getNumField("prevExp12")) || ($hc_numShip12 != $field->getNumField("numShip12")) || ($hc_periodicity != $field->getNumField("periodicity")))
	   {
	      // houve alteracao de prev....
		  //Atualiza o lim em importer e no changecredit
		  
		  
		  
		  //Foi Comentado!!!!  por causa do aditivo 6!!!! 
          //$cur = odbc_exec($db,
		  //"UPDATE Importer
          //SET
	      //limCredit = 0 
          //WHERE id = $idBuy");
		  
          //$cur = odbc_exec($db,
		  //"UPDATE ChangeCredit
          //SET
	      //creditSolic = 0 
          //WHERE idImporter = " . $idBuy . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $idBuy . ")  ");
		  //FIM ---- Foi Comentado!!!!  por causa do aditivo 6!!!! 
		  
		  
		  //echo "<BR>Credito solicitado zerado!!!! HICOM<BR>";
		  
	   }
	}

	 $msg = '';
	 
	if ($divulgaNome == 1 && $divulgaNomeOrig == 0) {
    if ($hc_result != "OK") {
        $msg = $hc_result;
    }
}

// Inserir no log principal
$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) 
        VALUES (?, ?, ?, ?, ?)";
$params = [
    49,
    $userID,
    $idInform,
    date("Y-m-d"),
    date("H:i:s")
];
$stmt = odbc_prepare($db, $sql);
$result = odbc_execute($stmt, $params);

if ($result) {
    // Obter o ID do registro no log
    $sql_id = "SELECT @@IDENTITY AS id_Log";
    $stmt_id = odbc_exec($db, $sql_id);
    $cur = odbc_result($stmt_id, 1);

    // Inserir detalhes do log
    $sql_details = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) 
                    VALUES (?, ?, ?, ?)";
    $params_details = [$cur, 'Importador', $name, 'name'];
    $stmt_details = odbc_prepare($db, $sql_details);
    $rs = odbc_execute($stmt_details, $params_details);

    if ($rs) {
        // Obter o ID do detalhe do log
        $sql_id_details = "SELECT @@IDENTITY AS id_detalhes";
        $stmt_id_details = odbc_exec($db, $sql_id_details);
        $cur_details = odbc_result($stmt_id_details, 1);

        // Inserir a query no log de detalhes
        $sql_query = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) 
                      VALUES (?, ?)";
        $params_query = [$cur_details, str_replace("'", "", $log_query)];
        $stmt_query = odbc_prepare($db, $sql_query);
        odbc_execute($stmt_query, $params_query);
    }
}

// Liberar cursores e resultados
if (isset($stmt)) odbc_free_result($stmt);
if (isset($stmt_id)) odbc_free_result($stmt_id);
if (isset($stmt_details)) odbc_free_result($stmt_details);
if (isset($stmt_id_details)) odbc_free_result($stmt_id_details);
if (isset($stmt_query)) odbc_free_result($stmt_query);


    $name = '';
    $address = '';
    $idCountry = 0;
    $tel = '';
    $prevExp12 = 0;
    $numShip12 = 0;
    $periodicity = 0;
    $city = '';
    $przPag = 0;
    $cep = '';
    $fax = '';
    $contact = '';
    $relation = '';
    $seasonal = 0;
    $idBuy = 0;
    $divulgaNome = 0;
    $emailContato = "";
	$cnpj = "";
	$expMax  = 0;
	$limCredit = 0;
   
  }
/*}
*/

 
?>
