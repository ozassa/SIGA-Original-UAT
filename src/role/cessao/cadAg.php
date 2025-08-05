<?php
	$idBanco 	= isset($_POST['idBanco']) ? $_POST['idBanco'] : false;
	$agencia 	= isset($_POST['agencia']) ? $_POST['agencia'] : false;
	$agNome 	= isset($_POST['agNome']) ? $_POST['agNome'] : false;
	$agEnd	 	= isset($_POST['agEnd']) ? $_POST['agEnd'] : false;
	$agCid	 	= isset($_POST['agCid']) ? $_POST['agCid'] : false;
	$uf		 	= isset($_POST['uf']) ? $_POST['uf'] : false;
	$cnpj	 	= isset($_POST['cnpj']) ? $_POST['cnpj'] : false;
	$agIE	 	= isset($_POST['agIE']) ? $_POST['agIE'] : false;
	$idNurim 	= isset($_POST['idNurim']) ? $_POST['idNurim'] : false;
	$telefone 	= isset($_REQUEST['agTelefone']) ? $_REQUEST['agTelefone'] : false;
	$email 		= isset($_REQUEST['agEmail']) ? $_REQUEST['agEmail'] : false;
	$contato 	= isset($_REQUEST['agContato']) ? $_REQUEST['agContato'] : false;
	$agTel 		= isset($_REQUEST['agTel']) ? $_REQUEST['agTel'] : false;
	
	$valida = true;
	
	if($agNome == ""){
		$msgAg = "Favor informar o nome da agência";
		$valida = false;
	}

	// Prevenindo SQL Injection na consulta de verificação
	$q = "SELECT count(id) FROM Agencia WHERE codigo = ? AND idBanco = ?";
	$cur = odbc_prepare($db, $q);
	odbc_execute($cur, array($agencia, $idBanco));
	$cont = odbc_result($cur, 1);

	if($cont > 0){
        $msgAg = 'Código já utilizado para outra agência';
        $valida = false;
    } 

	if($valida) {
		// Utilizando prepared statements para a inserção dos dados
		if($idNurim){
			$q = "INSERT INTO Agencia (idBanco, codigo, name, endereco, cidade, uf, cnpj, ie, idNurim, telefone, email, contato, agTel) 
				  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$cur = odbc_prepare($db, $q);
			$success = odbc_execute($cur, array($idBanco, $agencia, $agNome, $agEnd, $agCid, $uf, $cnpj, $agIE, $idNurim, $telefone, $email, $contato, $agTel));
		} else {
			$q = "INSERT INTO Agencia (idBanco, codigo, name, endereco, cidade, uf, cnpj, ie, telefone, email, contato, agTel) 
				  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$cur = odbc_prepare($db, $q);
			$success = odbc_execute($cur, array($idBanco, $agencia, $agNome, $agEnd, $agCid, $uf, $cnpj, $agIE, $telefone, $email, $contato, $agTel));
		}
		
		if($success){
			$msgAg = "Agência Cadastrada com Sucesso";
		}
	}
?>
