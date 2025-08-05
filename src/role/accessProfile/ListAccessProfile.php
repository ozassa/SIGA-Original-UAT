<?php
	
	$sql = "SELECT P.i_Perfil As i_Perfil, P.Descricao As Descricao, Situacao.Descricao_Item As Situacao 
						FROM	Perfil P
							INNER JOIN Campo_Item Situacao ON Situacao.i_Campo = 1310	AND Situacao.i_Item = P.s_Perfil 
						WHERE P.t_Perfil = 3 -- Banco do Brasil
						ORDER BY P.Descricao";
	$rsSql = odbc_exec($db, $sql);

	$dados = array();
	while(odbc_fetch_row($rsSql)) {
		$i_Perfil = odbc_result($rsSql, "i_Perfil");
		$Descricao = odbc_result($rsSql, "Descricao");
		$Situacao = odbc_result($rsSql, "Situacao");

		$dados[] = array(
			"i_Perfil" 				=> $i_Perfil,
			"Descricao" 			=> $Descricao,
			"Situacao"				=> $Situacao
		);
	}

	odbc_close($db);

	$title = "Cadastro de Perfis";
	$content = "../accessProfile/interf/ViewAccessProfile.php";
?>