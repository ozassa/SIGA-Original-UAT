<?php

	$sql = "SELECT P.i_Parametro, -- Não deverá ser visível na tela
									P.Parametro,
									PE.i_Empresa, -- Não deverá ser visível na tela
									PE.n_Parametro As Numero,
									PE.v_Parametro As Valor,
									PE.d_Parametro As Data,
									PE.t_Parametro As Texto
					FROM Parametro P
						INNER JOIN Parametro_Empresa PE ON PE.i_Parametro = P.i_Parametro 
					WHERE PE.i_Empresa = ".$empresaID." 
					ORDER BY P.i_Parametro";

	$cur = odbc_exec($db, $sql);

	$dados_sel = array();

	while(odbc_fetch_row($cur)) {
		$i_Parametro = odbc_result($cur, "i_Parametro");
		$Parametro = odbc_result($cur, "Parametro");
		$i_Empresa = odbc_result($cur, "i_Empresa");
		$Numero = odbc_result($cur, "Numero");
		$Valor = odbc_result($cur, "Valor");
		$Data = odbc_result($cur, "Data");
		$Texto = odbc_result($cur, "Texto");

		$dados_sel[] = array(
			"i_Parametro" => $i_Parametro,
			"Parametro" => $Parametro,
			"i_Empresa" => $i_Empresa,
			"Numero" => $Numero,
			"Valor" => $Valor,
			"Data" => $Data,
			"Texto" => $Texto
		);
	}

	$title = "Lista de Par&acirc;metros";
	$content = "../parameter/interf/ViewParameter.php";