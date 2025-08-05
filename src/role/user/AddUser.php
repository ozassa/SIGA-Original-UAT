<?php
	// Obtendo o valor de id_User de forma segura
	$id_User = isset($_REQUEST['id_User']) ? $_REQUEST['id_User'] : "";

	// Preparando a consulta para obter o idBanco
	$sqlB = "SELECT UB.idBanco AS id_Banco
						FROM Users U
							INNER JOIN UsersBanco UB ON UB.idUser = U.id
						WHERE U.id = ?";
	$stmtB = odbc_prepare($db, $sqlB);
	odbc_execute($stmtB, [$_SESSION['userID']]);
	$id_Banco = odbc_result($stmtB, "id_Banco");

	// Preparando a consulta para obter os dados do usuário
	$sql = "SELECT U.id AS ID, U.name AS Name, U.cookie AS Cookie, U.login AS Login, ISNULL(U.email, '') AS Email, U.CPF AS CPF, U.i_Perfil AS Perfil,
						CASE U.state
							WHEN 1 THEN 'Inativo'
							ELSE 'Ativo'
						END AS Situacao
				FROM Users U 
					INNER JOIN UsersBanco UB ON UB.idUser = U.id AND UB.idBanco = ?
				WHERE U.perfil = 'B' AND U.id = ?
				ORDER BY U.name";
	$stmt = odbc_prepare($db, $sql);
	odbc_execute($stmt, [$id_Banco, $id_User]);

	$Name = odbc_result($stmt, "Name") ?: "";
	$Cookie = odbc_result($stmt, "Cookie") ?: "";
	$Login = odbc_result($stmt, "Login") ?: "";
	$Email = odbc_result($stmt, "Email") ?: "";
	$Situacao = odbc_result($stmt, "Situacao") ?: "";
	$CPF = odbc_result($stmt, "CPF") ?: "";
	$Perfil = odbc_result($stmt, "Perfil") ?: "";

	// Preparando a consulta para obter as regiões
	$sqlReg = "SELECT
					CASE 
						WHEN UN.idUser IS NULL THEN 0
						ELSE 1
					END AS Possui_Regiao,
					N.id AS ID_Regiao,
					N.name AS Nome_Regiao
				FROM Nurim N
					LEFT JOIN UsersNurim UN ON UN.idNurim = N.id AND UN.idUser = ?
				ORDER BY N.name";
	$stmtReg = odbc_prepare($db, $sqlReg);
	odbc_execute($stmtReg, [$id_User]);

	$dados_reg = [];
	while (odbc_fetch_row($stmtReg)) {
		$Possui_Regiao = odbc_result($stmtReg, "Possui_Regiao");
		$ID_Regiao = odbc_result($stmtReg, "ID_Regiao");
		$Nome_Regiao = odbc_result($stmtReg, "Nome_Regiao");

		$dados_reg[] = [
			"Possui_Regiao" => $Possui_Regiao,
			"ID_Regiao" => $ID_Regiao,
			"Nome_Regiao" => $Nome_Regiao
		];
	}

	// Preparando a consulta para obter os perfis
	$sqlPerf = "SELECT P.i_Perfil, P.Descricao 
						FROM Perfil P 
						WHERE P.t_Perfil = 3 -- Banco do Brasil
								AND P.s_Perfil = 0 -- Ativo
						ORDER BY P.Descricao";
	$stmtPerf = odbc_prepare($db, $sqlPerf);
	odbc_execute($stmtPerf);

	$dados_perf = [];
	while (odbc_fetch_row($stmtPerf)) {
		$i_Perfil = odbc_result($stmtPerf, "i_Perfil");
		$Descricao = odbc_result($stmtPerf, "Descricao");

		$dados_perf[] = [
			"i_Perfil" => $i_Perfil,
			"Descricao" => $Descricao
		];
	}

	$title = "Cadastro de Usuários";
	$content = "../user/interf/ViewCadUser.php";
?>
