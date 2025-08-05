<?php

//gravação do usuario

// Realizando um SELECT simples para liberar a ODBC
odbc_exec($db, "SELECT 1");

$idNotification = $_REQUEST['idNotification'];
$idUsuario = $_REQUEST['idUsuario'];
$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : 0;
$operacao = $_REQUEST['operacao'];
$NomeUsuario = $_POST['NomeUsuario'];
$CpfUsuario = $_POST['CpfUsuario'];
$EmailUsuario = $_POST['EmailUsuario'];
$LoginUsuario = $_POST['LoginUsuario'];
$SenhaUsuario = $_POST['SenhaUsuario'] ? crypt($_POST['SenhaUsuario'], SALT) : '';
$ConfirmaSenha = $_POST['ConfirmaSenha'];


if ($_REQUEST['operacao'] == 1) {


	// Verifica se o usuário já está cadastrado
	$sql = "SELECT login FROM Users WHERE login = ?";
	$verificaUsuario = odbc_prepare($db, $sql);
	odbc_execute($verificaUsuario, [$LoginUsuario]);

	if (odbc_result($verificaUsuario, 'login') != '') {
		$msgi = 'Aten&ccedil;&atilde;o!<br>O Usu&aacute;rio j&aacute; se encontra cadastrado no sistema, por favor tente outro.';
	} else {
		odbc_free_result($verificaUsuario);
		// Insere o usuário
		$sql = "INSERT INTO Users(name, login, password, email, state, perfil, CPF) 
            VALUES (?, ?, ?, ?, 0, 'C', ?)";
		$insereUsuario = odbc_prepare($db, $sql);
		odbc_execute($insereUsuario, [$NomeUsuario, $LoginUsuario, $SenhaUsuario, $EmailUsuario, $CpfUsuario]);

		// Obtém o ID do último usuário inserido
		$sql = "SELECT MAX(id) AS id FROM Users";
		$obtemId = odbc_exec($db, $sql);

		if ($obtemId) {
			$id = odbc_result($obtemId, 'id');
		}

		// Associa o usuário ao papel padrão (idRole = 1)
		$sql = "INSERT INTO UserRole(idUser, idRole) VALUES (?, ?)";
		$inserePapel = odbc_prepare($db, $sql);
		odbc_execute($inserePapel, [$id, 1]);

		// Insere as informações adicionais do usuário
		$sql = "INSERT INTO Inform_Usuarios(idInform, idUser) VALUES (?, ?)";
		$insereInformacao = odbc_prepare($db, $sql);

		foreach ($_REQUEST['infor'] as $idinf) {
			odbc_execute($insereInformacao, [$idinf, $id]);
		}

		if ($insereInformacao) {
			$msgi = 'Usu&aacute;rio cadastrado com sucesso.';
		} else {
			$msgi = 'O usu&aacute;rio n&atilde;o foi cadastrado, verifique os dados informados.';
		}
	}


} else if ($_REQUEST['operacao'] == 2) {
	// Atualiza os dados do usuário
	$sql = "UPDATE Users SET 
            name = ?, 
            login = ?, 
            password = ?, 
            email = ?, 
            CPF = ?
        WHERE id = ?";
	$atualizaUsuario = odbc_prepare($db, $sql);
	odbc_execute($atualizaUsuario, [$NomeUsuario, $LoginUsuario, $SenhaUsuario, $EmailUsuario, $CpfUsuario, $idUsuario]);

	// Remove as informações associadas ao usuário
	$sql = "DELETE FROM Inform_Usuarios WHERE idUser = ?";
	$deletaInformacoes = odbc_prepare($db, $sql);
	odbc_execute($deletaInformacoes, [$idUsuario]);

	// Insere as novas informações associadas ao usuário
	$sql = "INSERT INTO Inform_Usuarios(idInform, idUser) VALUES (?, ?)";
	$insereInformacao = odbc_prepare($db, $sql);

	foreach ($_REQUEST['infor'] as $idinf) {
		odbc_execute($insereInformacao, [$idinf, $idUsuario]);
	}

	// Verifica se a última execução foi bem-sucedida
	if ($insereInformacao) {
		$msgi = 'Usu&aacute;rio alterado com sucesso.';
	} else {
		$msgi = 'O usu&aacute;rio n&atilde;o foi alterado, verifique os dados informados.';
	}

} else if ($_REQUEST['operacao'] == 3) {
	// Deleta o usuário da tabela Users
	$sql = "DELETE FROM Users WHERE id = ?";
	$deletaUsuario = odbc_prepare($db, $sql);
	odbc_execute($deletaUsuario, [$idUsuario]);

	// Deleta as informações relacionadas ao usuário na tabela Inform_Usuarios
	$sql = "DELETE FROM Inform_Usuarios WHERE idUser = ?";
	$deletaInformacoes = odbc_prepare($db, $sql);
	odbc_execute($deletaInformacoes, [$idUsuario]);

	// Deleta as associações de papéis do usuário na tabela UserRole
	$sql = "DELETE FROM UserRole WHERE idUser = ?";
	$deletaUserRole = odbc_prepare($db, $sql);
	odbc_execute($deletaUserRole, [$idUsuario]);

	// Verifica se a última execução foi bem-sucedida
	if ($deletaUserRole) {
		$msgi = 'Usu&aacute;rio removido com sucesso.';
	} else {
		$msgi = 'O usu&aacute;rio n&atilde;o foi removido.';
	}

}
?>