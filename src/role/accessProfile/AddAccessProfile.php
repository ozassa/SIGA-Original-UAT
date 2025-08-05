<?php
	$i_Perfil = isset($_REQUEST['i_Perfil']) ? $_REQUEST['i_Perfil'] : "";
	
	$sql = "SELECT P.i_Perfil AS i_Perfil, P.Descricao AS Descricao_Perfil, P.s_Perfil AS Situacao, IsNull(PT.Leitura, 0) AS Possui_Acesso, T.i_Tela AS i_Tela, T.Descricao_Tela AS Descricao_Tela 
        FROM Perfil P 
        LEFT JOIN Tela T ON T.t_Perfil = P.t_Perfil AND T.s_Tela = 0
        LEFT JOIN Perfil_Tela PT ON PT.i_Tela = T.i_Tela AND PT.i_Perfil = P.i_Perfil
        WHERE P.i_Perfil = ?
        ORDER BY T.Descricao_Tela";

$stmt = odbc_prepare($db, $sql);
odbc_execute($stmt, [$i_Perfil]);

$rsSql = $stmt;



	$dados = array();
	while(odbc_fetch_row($rsSql)) {	
		$i_Perfil = odbc_result($rsSql, "i_Perfil") ? odbc_result($rsSql, "i_Perfil") : "";
		$Descricao_Perfil = odbc_result($rsSql, "Descricao_Perfil") ? odbc_result($rsSql, "Descricao_Perfil") : "";
		$Situacao = odbc_result($rsSql, "Situacao");
		$Possui_Acesso = odbc_result($rsSql, "Possui_Acesso") ? odbc_result($rsSql, "Possui_Acesso") : "";
		$i_Tela = odbc_result($rsSql, "i_Tela") ? odbc_result($rsSql, "i_Tela") : "";
		$Descricao_Tela = odbc_result($rsSql, "Descricao_Tela") ? odbc_result($rsSql, "Descricao_Tela") : "";

		$dados[] = array(
			"i_Perfil" 						=> $i_Perfil,
			"Descricao_Perfil"		=> $Descricao_Perfil,
			"Situacao"						=> $Situacao,
			"Possui_Acesso"				=> $Possui_Acesso,
			"i_Tela"							=> $i_Tela,
			"Descricao_Tela"			=> $Descricao_Tela
		);
	}

	odbc_close($db);

	$sqlSit = "SELECT i_Item, Descricao_Item 
							FROM Campo_Item 
							WHERE i_Campo = 1310 
							ORDER BY i_Item";
	$rsSqlSit = odbc_exec($db, $sqlSit);

	$dados_sit = array();
	while(odbc_fetch_row($rsSqlSit)) {
		$i_Item = odbc_result($rsSqlSit, "i_Item");
		$Descricao_Item = odbc_result($rsSqlSit, "Descricao_Item");

		$dados_sit[] = array(
			"i_Item" 						=> $i_Item,
			"Descricao_Item"		=> $Descricao_Item
		);
	}

	odbc_close($db);

	$title = "Cadastro de Perfis";
	$content = "../accessProfile/interf/ViewCadAccessProfile.php";
?>