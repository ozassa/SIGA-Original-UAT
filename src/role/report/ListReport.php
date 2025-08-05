<?php

	$sql = "SELECT P.i_Processamento, P.Descricao, P.Nome_Procedure
					FROM Processamento P
						WHERE P.t_Processamento = 1	AND s_Processamento = 0
					ORDER BY P.i_Processamento";
	$rsSql = odbc_exec($db, $sql);

	$dados = array();
	while(odbc_fetch_row($rsSql)) {
		$i_Processamento = odbc_result($rsSql, "i_Processamento");
		$Descricao = odbc_result($rsSql, "Descricao");
		$Nome_Procedure = odbc_result($rsSql, "Nome_Procedure");

		$dados[] = array(
			"i_Processamento" => $i_Processamento,
			"Descricao" 			=> $Descricao,
			"Nome_Procedure"	=> $Nome_Procedure
		);
	}

	$title = "Relatórios";
	$content = "../report/interf/ViewReport.php";
?>