<?php
	
	$Id_Banco = "1";
	$UF = isset($_REQUEST['uf']) ? $_REQUEST['uf'] : "";
	$Id_Regiao = isset($_REQUEST['Id_Regiao']) ? $_REQUEST['Id_Regiao'] : "";
	$Id_Agencia = isset($_REQUEST['Id_Agencia']) ? $_REQUEST['Id_Agencia'] : "";
	$Situacao = isset($_REQUEST['situacao']) ? $_REQUEST['situacao'] : "";

	$sql = "EXEC SPR_BB_Relatorio_Apolices_Agencia_Detalhes ?,?,?,?,?";
	$params = [$Id_Banco, $UF, $Id_Regiao, $Id_Agencia, $Situacao];
	$rsSql = odbc_prepare($db, $sql);
	odbc_execute($rsSql, $params);

	$Nome_Banco = "";
	$Estado = "";
	$Regiao = "";
	$Nome_Agencia = "";
	$Situacao = "";
	$dados = array();
	while(odbc_fetch_row($rsSql)) {
		$Nome_Banco = odbc_result($rsSql, "Nome_Banco");
		$Estado = odbc_result($rsSql, "Estado");
		$Regiao = odbc_result($rsSql, "Regiao");
		$Nome_Agencia = odbc_result($rsSql, "Nome_Agencia");
		$Situacao = odbc_result($rsSql, "Situacao");

		$n_Apolice = odbc_result($rsSql, "n_Apolice");
		$Segurado = odbc_result($rsSql, "Segurado");
		$d_Inicio_Vigencia = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Inicio_Vigencia"), 0, 10));
		$d_Fim_Vigencia = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Fim_Vigencia"), 0, 10));
		$Sigla_Moeda = odbc_result($rsSql, "Sigla_Moeda");
		$v_Premio_Emitido = number_format(odbc_result($rsSql, "v_Premio_Emitido"), 2, ",", ".");
		$v_Premio_Pago = number_format(odbc_result($rsSql, "v_Premio_Pago"), 2, ",", ".");
		$v_Premio_Vencido = number_format(odbc_result($rsSql, "v_Premio_Vencido"), 2, ",", ".");
		$v_Sinistro_Pago = number_format(odbc_result($rsSql, "v_Sinistro_Pago"), 2, ",", ".");
		$v_Sinistro_Pendente = number_format(odbc_result($rsSql, "v_Sinistro_Pendente"), 2, ",", ".");
		$v_LMI = number_format(odbc_result($rsSql, "v_LMI"), 2, ",", ".");
		$v_LMI_Disponivel = number_format(odbc_result($rsSql, "v_LMI_Disponivel"), 2, ",", ".");
		$Qtde_Compradores_Cedidos = odbc_result($rsSql, "Qtde_Compradores_Cedidos");

		$dados[] = array(
			"n_Apolice" 								=> $n_Apolice,
			"Segurado" 									=> $Segurado,
			"d_Inicio_Vigencia"					=> $d_Inicio_Vigencia,
			"d_Fim_Vigencia"						=> $d_Fim_Vigencia,
			"Sigla_Moeda"								=> $Sigla_Moeda,
			"v_Premio_Emitido"					=> $v_Premio_Emitido,
			"v_Premio_Pago"							=> $v_Premio_Pago,
			"v_Premio_Vencido"					=> $v_Premio_Vencido,
			"v_Sinistro_Pago"						=> $v_Sinistro_Pago,
			"v_Sinistro_Pendente"				=> $v_Sinistro_Pendente,
			"v_LMI"											=> $v_LMI,
			"v_LMI_Disponivel"					=> $v_LMI_Disponivel,
			"Qtde_Compradores_Cedidos"	=> $Qtde_Compradores_Cedidos
		);
	}

	odbc_close($db);

	$title = "Relatório de apólices por região/estado/agência - Detalhado";
	$content = "../report/interf/ViewPolicyReportDetail.php";
?>