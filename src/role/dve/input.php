<?php //Alterado HiCom mes 04

	$userID  = $_SESSION['userID'];

	error_log("select i_Seg, startValidity, endValidity, name, idAnt, prodUnit, tipoDve, n_Apolice, IsNull(Prazo_Entrega_DVN, 15) As Prazo_Entrega_DVN from Inform where id=$idInform");

	$cur = odbc_exec($db, "select i_Seg, startValidity, endValidity, name, idAnt, prodUnit, tipoDve, n_Apolice, IsNull(Prazo_Entrega_DVN, 15) As Prazo_Entrega_DVN from Inform where id=$idInform");

	if(odbc_fetch_row($cur)){
		$idSeg = odbc_result($cur, 1);
		$start = ymd2dmy(odbc_result($cur, 2));
		$end   = ymd2dmy(odbc_result($cur, 3));
		$name  = odbc_result($cur, 4);
		$namecl  = odbc_result($cur, 4);
		$idAnt = odbc_result($cur, 5);
		$prod  = odbc_result($cur, 6);
		$tipoDve = odbc_result($cur, "tipoDve");
    	$apolice = odbc_result($cur, "n_Apolice");
    	$Prazo_Entrega_DVN = odbc_result($cur, "Prazo_Entrega_DVN");
    	$apolice = sprintf("062%06d", $apolice). ($prod != 62 ? "/$prod" : '');
	}

	$x = odbc_exec($db, "SELECT D.num AS num, D.inicio As Inicio_Periodo, IsNull(DateAdd(D, -1, DP.inicio), Inf.endValidity) AS Fim_Periodo, DateAdd(D, IsNull(Inf.Prazo_Entrega_DVN, 15), IsNull(DateAdd(D, -1, DP.inicio), Inf.endValidity)) As Limite_Periodo, D.periodo As Periodo, D.LiberaAtraso As LiberaAtraso, D.LiberaVencida As LiberaVencida FROM Inform Inf Inner Join DVE D On D.idInform = Inf.id Left Join DVE DP On DP.idInform = Inf.id And DP.num = D.num + 1 WHERE D.id=$idDVE");
	
	$num = odbc_result($x, "num");
	$inicio = ymd2dmy(odbc_result($x, "Inicio_Periodo"));
	$fim = ymd2dmy(odbc_result($x, "Fim_Periodo"));
	$Data_Limite_Periodo = ymd2dmy(odbc_result($x, "Limite_Periodo"));
	$periodo = odbc_result($x, "Periodo");
	$LiberaAtraso = odbc_result($x, "LiberaAtraso");
	$LiberaVencida = odbc_result($x, "LiberaVencida");

	$x = odbc_exec($db, "SELECT SUM(totalEmbarcado) As Total_Embarcado, SUM(proex) AS PROEX, SUM(ace) AS ACE, COUNT(*) AS Num_Registros FROM DVEDetails DD INNER JOIN Importer Imp On Imp.id = DD.idImporter WHERE idDVE=$idDVE AND DD.state In (1) AND DD.modalidade=$modalidade");
	
	$totalEmbarcado = odbc_result($x, "Total_Embarcado");
	$totalProex = odbc_result($x, "PROEX");
	$totalAce = odbc_result($x, "ACE");
	$num_registros = odbc_result($x, "Num_Registros");

	If(! $registro){
  		$registro = $num_registros + 1;
	}

	$details[0] = 'dummy';
	$x = odbc_exec($db, "select id from DVEDetails where idDVE=$idDVE and modalidade=$modalidade and state=1 order by id");

	While(odbc_fetch_row($x)){
  		$details[] = odbc_result($x, 1);
	}

	If($registro <= $num_registros){
  		$show = 1;
  		
  		$x = odbc_exec($db,
			"select idImporter, idCountry, embDate, vencDate, fatura, totalEmbarcado, proex, ace, id, DataCadastro from DVEDetails where id=$details[$registro]");
			
  		$idBuyer = odbc_result($x, 1);
  		$idCountry = odbc_result($x, 2);
  		$dataEmb = ymd2dmy(odbc_result($x, 3));
  		list($dataEmbDia, $dataEmbMes, $dataEmbAno) = explode('/', $dataEmb);
  		$dataVenc = ymd2dmy(odbc_result($x, 4));
  		list($dataVencDia, $dataVencMes, $dataVencAno) = explode('/', $dataVenc);

  		$fatura = odbc_result($x, 5);
  		$valorEmb = odbc_result($x, 6);
  		$proex = odbc_result($x, 7);
  		$ace = odbc_result($x, 8);
  		$idDetail = odbc_result($x, 9);
	}

	/*if($no_values){
  		$dataEmbDia = $dataEmbAno = $dataEmbMes = $dataVencDia = $dataVencMes = $dataVencAno = $fatura = $valorEmb = $proex = $ace = '';
	}*/
?>