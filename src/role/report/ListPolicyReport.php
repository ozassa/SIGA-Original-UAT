<?php

	$sqlProcEst = "EXEC SPR_BB_Relatorio_Apolices_Agencia '1', '100'";
	$rsSqlProcEst = odbc_exec($db, $sqlProcEst);

	$dados_estado = array();
	while(odbc_fetch_row($rsSqlProcEst)) {
		$UF = odbc_result($rsSqlProcEst, "UF");
		$Estado = odbc_result($rsSqlProcEst, "Estado");
		$ContVig = odbc_result($rsSqlProcEst, "ContVig");
		$ContEnc = odbc_result($rsSqlProcEst, "ContEnc");

		$dados_estado[] = array(
			"UF"				=> $UF,
			"Estado"			=> $Estado,
			"ContVig"			=> $ContVig,
			"ContEnc"			=> $ContEnc
		);
	}

	

	$sqlProcReg = "EXEC SPR_BB_Relatorio_Apolices_Agencia '1', '200'";
	$rsSqlProcReg = odbc_exec($db, $sqlProcReg);

	$dados_regiao = array();
	while(odbc_fetch_row($rsSqlProcReg)) {
		$i_Regiao = odbc_result($rsSqlProcReg, "i_Regiao");
		$Regiao = odbc_result($rsSqlProcReg, "Regiao");
		$ContVig = odbc_result($rsSqlProcReg, "ContVig");
		$ContEnc = odbc_result($rsSqlProcReg, "ContEnc");

		$dados_regiao[] = array(
			"i_Regiao"		=> $i_Regiao,
			"Regiao"			=> $Regiao,
			"ContVig"			=> $ContVig,
			"ContEnc"			=> $ContEnc
		);
	}

	

	$sqlProcAg = "EXEC SPR_BB_Relatorio_Apolices_Agencia '1', '300'";
	$rsSqlProcAg = odbc_exec($db, $sqlProcAg);

	$dados_agencia = array();
	while(odbc_fetch_row($rsSqlProcAg)) {
		$i_Agencia = odbc_result($rsSqlProcAg, "i_Agencia");
		$Cod_Agencia = odbc_result($rsSqlProcAg, "Cod_Agencia");
		$Agencia = odbc_result($rsSqlProcAg, "Agencia");
		$ContVig = odbc_result($rsSqlProcAg, "ContVig");
		$ContEnc = odbc_result($rsSqlProcAg, "ContEnc");

		$dados_agencia[] = array(
			"i_Agencia" 	=> $i_Agencia,
			"Cod_Agencia" => $Cod_Agencia,
			"Agencia" 		=> $Agencia,
			"ContVig"			=> $ContVig,
			"ContEnc"			=> $ContEnc
		);
	}

	

	$title = "Relatório de apólices por região/estado/agência";
	$content = "../report/interf/ViewPolicyReport.php";
?>