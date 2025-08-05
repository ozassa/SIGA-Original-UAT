<?php

	if (isset($_REQUEST['buscar'])) {
		$Id_Banco = "1";
		$SPR_d_Inicio_Vigencia = $_REQUEST['d_Inicio_Vigencia'] ? "'".Convert_Data_Geral(substr($_REQUEST['d_Inicio_Vigencia'], 0, 10))."'" : "NULL";
		$SPR_d_Fim_Vigencia = $_REQUEST['d_Fim_Vigencia'] ? "'".Convert_Data_Geral(substr($_REQUEST['d_Fim_Vigencia'], 0, 10))."'" : "NULL";
		$Id_Usuario = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;

		$sql = "EXEC SPR_BB_Relatorio_Apolices '".$Id_Banco."', ".$SPR_d_Inicio_Vigencia.", ".$SPR_d_Fim_Vigencia.", '".$Id_Usuario."'";

		$rsSql = odbc_exec($db, $sql);

		$dados = array();
		while(odbc_fetch_row($rsSql)) {
			$Nome_Regiao = odbc_result($rsSql, "Nome_Regiao");
			$Nome_Agencia = odbc_result($rsSql, "Nome_Agencia");
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

			$dados[] = array(
				"Nome_Regiao" => $Nome_Regiao,
				"Nome_Agencia" => $Nome_Agencia,
				"n_Apolice" => $n_Apolice,
				"Segurado" => $Segurado,
				"d_Inicio_Vigencia" => $d_Inicio_Vigencia,
				"d_Fim_Vigencia" => $d_Fim_Vigencia,
				"Sigla_Moeda" => $Sigla_Moeda,
				"v_Premio_Emitido" => $v_Premio_Emitido,
				"v_Premio_Pago" => $v_Premio_Pago,
				"v_Premio_Vencido" => $v_Premio_Vencido,
				"v_Sinistro_Pago" => $v_Sinistro_Pago,
				"v_Sinistro_Pendente" => $v_Sinistro_Pendente,
				"v_LMI" => $v_LMI,
				"v_LMI_Disponivel" => $v_LMI_Disponivel
			);
		}

		
	}

	$title = "Relatório de Apólices";
	$content = "../report/interf/ViewFullPolicyReport.php";
?>