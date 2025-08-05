<?php
	// Preparar a conexão e verificar
	if (!$db) {
		die("Conexão falhou: " . odbc_errormsg());
	}

	// Obter o ID do banco de forma segura
	$sqlB = "SELECT UB.idBanco AS id_Banco
				FROM Users U
				INNER JOIN UsersBanco UB ON UB.idUser = U.id
				WHERE U.id = ?";
	$stmtB = odbc_prepare($db, $sqlB);
	odbc_execute($stmtB, array($_SESSION['userID']));
	$resultB = odbc_fetch_array($stmtB);

	$id_Banco = $resultB['id_Banco'] ?? null;

	if ($id_Banco) {
		// Consultar os usuários associados ao banco de forma segura
		$sql = "SELECT U.id AS ID, 
						U.name AS Name, 
						U.cookie AS Cookie, 
						U.login AS Login, 
						ISNULL(U.email, '') AS Email,
						CASE U.state
							WHEN 1 THEN 'Inativo'
							ELSE 'Ativo'
						END AS Situacao
					FROM Users U 
					INNER JOIN UsersBanco UB ON UB.idUser = U.id AND UB.idBanco = ? 
					WHERE U.perfil = 'B' AND U.d_Cancelamento IS NULL
					ORDER BY U.name";
		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, array($id_Banco));

		$dados = array();
		while ($row = odbc_fetch_array($stmt)) {
			$dados[] = array(
				"id_User"   => $row['ID'],
				"Name"      => $row['Name'],
				"Cookie"    => $row['Cookie'],
				"Login"     => $row['Login'],
				"Email"     => $row['Email'],
				"Situacao"  => $row['Situacao']
			);
		}
	} else {
		$dados = false;
	}

	$title = "Cadastro de Usuários";
	$content = "../user/interf/ViewUser.php";
?>
