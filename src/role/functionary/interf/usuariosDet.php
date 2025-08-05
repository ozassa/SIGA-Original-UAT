<?php
if (!check_menu(['generalManager'], $role)) {
    header('HTTP/1.1 403 Forbidden');
    echo "Acesso n�o autorizado.";
    exit;
}

$log_query 	= "";
$novo 		= isset($_REQUEST['novo']) ? $_REQUEST['novo'] : 0;
?>

<script language="javascript">
	function proc(opc) {
		var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");

		Form1.executa.value = opc;
		if (opc == 1 || opc == 3) { // alterar ou incluir
			if (Form1.name.value == '') {
				verErro('Informe o Nome.');
				document.Form1.name.focus();
			}
			else if (Form1.login.value == '') {
				verErro('Informe o Login.');
				document.Form1.login.focus();
			}
			else if (Form1.perfil_usuario.value == '') {
				verErro('Informe o Perfil.');
				document.Form1.perfil_usuario.focus();
			}
			<?php if ($novo == 1) { ?>
										else if (Form1.password.value == '') {
					verErro('Informe a Senha.');
					document.Form1.password.focus();
				}
				else if (!strongRegex.test(Form1.password.value)) {
					message = " A senha deve :<br> - Conter pelo menos 1 caractere alfab�tico min�sculo<br> - Conter pelo menos 1 caractere alfab�tico mai�sculo<br> - Conter pelo menos 1 caractere num�rico<br> - Conter pelo menos um caractere especial<br> - Conter dez caracteres ou mais";
					verErro(message);
					$('#password').focus();
				}
			<?php } ?>
			else {
				document.Form1.submit();
			}
		}
		else { // excluir
			if (confirm("Usu�rio ser� exclu�do! Deseja continuar?")) {
				document.Form1.submit();
			}
		}
	}
</script>

<?php
include_once('../../../navegacao.php');

/*
Fun��o checkbox
*/
function testaPerm($db, $id, $idRole)
{
	if ($id == '') {
		return false;
	}

	// Testar a conex�o com um SELECT simples para garantir que est� livre
	$testQuery = "SELECT 1";
	$testStmt = odbc_exec($db, $testQuery);

	// Verifique se a conex�o n�o est� ocupada
	if ($testStmt === false) {
		// Registra o erro se a conex�o falhar
		error_log("Erro ao testar a conex�o: " . odbc_errormsg($db));
		return false;
	}

	// Consumir resultados do teste
	odbc_fetch_row($testStmt);
	odbc_free_result($testStmt); // Garantir que o cursor foi fechado

	// Agora, prepare sua consulta principal
	$sql = "SELECT count(*) AS qtd FROM UserRole WHERE idUser = ? AND idRole = ?";
	$params = [$id, $idRole];

	// Preparar a consulta
	$stmt = odbc_prepare($db, $sql);

	// Verifique se a prepara��o foi bem-sucedida
	if ($stmt === false) {
		error_log("Erro ao preparar a consulta: " . odbc_errormsg($db));
		return false;
	}

	// Executar a consulta com os par�metros
	$result = odbc_execute($stmt, $params);

	// Verifique se a execu��o foi bem-sucedida
	if ($result === false) {
		error_log("Erro ao executar a consulta: " . odbc_errormsg($db));
		return false;
	}

	// Verificar o resultado
	$qtd = odbc_result($stmt, "qtd");
	odbc_free_result($stmt); // Liberar o resultado ap�s o uso

	return ($qtd > 0);
}
/*
Fim fun��o checkbox
*/

/*
Altera dados / grava log
*/
$id 		= isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$executa 	= isset($_REQUEST['executa']) ? $_REQUEST['executa'] : 0;
$name 		= isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$nome 		= isset($_REQUEST['nome']) ? $_REQUEST['nome'] : '';
$state 		= isset($_REQUEST['state']) ? $_REQUEST['state'] : 0;
$role 		= isset($_REQUEST['role']) ? $_REQUEST['role'] : 0;
$login 		= isset($_REQUEST['login']) ? $_REQUEST['login'] : '';
$email 		= isset($_REQUEST['email']) ? $_REQUEST['email'] : '';

$password 	= isset($_POST["password"]) ? $_POST["password"] : '';
$cli 		= isset($_REQUEST['cli']) ? $_REQUEST['cli'] : 0;
$perfil_usuario = isset($_REQUEST['perfil_usuario']) ? $_REQUEST['perfil_usuario'] : 0;

if (in_array($executa, [1, 3])) {
	$cond = '';
	$params = [$login]; // Adiciona o login como o primeiro par�metro

	if ($id != '') {
		$cond = ' AND id != ?';
		$params[] = $id; // Adiciona o id como segundo par�metro
	}

	// Consulta SQL segura com placeholders
	$q = "SELECT COUNT(id) FROM Users WHERE login = ?" . $cond;

	// Prepara a consulta
	$stmt = odbc_prepare($db, $q);

	// Executa a consulta
	$cur = odbc_execute($stmt, $params);

	// Verifica se a execu��o foi bem-sucedida
	if ($cur) {
		// Recupera o valor do contador
		$cont = odbc_result($stmt, 1);

		if ($cont > 0) {
			$validacao = 'Usu�rio j� cadastrado';
			$executa = 99;
			$novo = 1;
		}
	} else {
		// Caso a execu��o falhe
		echo "Erro ao executar a consulta.";
	}
}

$ok = false;

if ($executa == 1) { //Altera��o de Usu�rio
	$pwd = crypt($password, SALT);

	$sql = "UPDATE Users SET name = ?, login = ?, email = ?, i_Perfil = ?, state = ?";

	// Adicionar a atualiza��o de senha apenas se `$pwd` estiver definido
	$params = [$name, $login, $email, $perfil_usuario, $state];
	if ($pwd) {
		$sql .= ", password = ?";
		$params[] = $pwd;
	}

	$sql .= " WHERE id = ?";
	$params[] = $id;

	if ($role != '') {
		// Preparar a consulta
		$stmt = odbc_prepare($db, $sql);

		// Executar a consulta com os par�metros
		$cur = odbc_execute($stmt, $params);

		if ($cur) {
			$log_query .= $sql; // Adicionar ao log (somente para auditoria, sem os valores reais)
		} else {
			$ok = false;
		}

		$msg = "Perfil atualizado.";
	} else {
		$ok = false;
		$msg = "Selecione ao menos um perfil.";
	}

	$sqlLOG = "INSERT INTO Log (tipoLog, id_User, data, hora)
              VALUES ('2', '" . $userID . "', '" . date('Y-m-d') . "', '" . date('H:i:s') . "')";

	//$cur=odbc_exec($db,$sqlLOG);

	if (!$cur) {
		$ok = false;
	}

	$sqlRLOG = "Select * From Log where tipoLog = '2' And id_User = '" . $userID . "' And data= '" . date('Y-m-d') . "' And hora='" . date('H:i:s') . "' ";
	//echo  $sqlRLOG ."<br>";
	//$cur=odbc_exec($db,$sqlRLOG);

	if (!$cur) {
		$ok = false;
	}

	//$sqlROLE = "Select * From UserRole WHERE idUser = '". $id ."'";
	//echo  $sqlL ."<br>";
	//$cur=odbc_exec($db,$sqlROLE);

	// Consulta parametrizada
	$sqlROLE = "SELECT * FROM UserRole WHERE idUser = ?";

	// Prepara a consulta SQL
	$stmt = odbc_prepare($db, $sqlROLE);

	// Par�metro para a consulta
	$params = [$id];

	// Executa a consulta com o par�metro
	$cur = odbc_execute($stmt, $params);

	if (!$cur) {
		$ok = false;
	}
	while (odbc_fetch_row($stmt)) {
		$idRole = odbc_result($stmt, "idRole");

		//$sqlLOG = "Select * From Log where tipoLog = '2' And id_User = '". $userID ."' And Inform= '". $id ."' And data= '".date('Y-m-d')."' And hora='".date('H:i:s')."' ";
		//echo  $sqlL ."<br>";
		//$cur=odbc_exec($db,$sqlLOG);

		// Consulta parametrizada
		$sqlLOG = "SELECT * FROM Log WHERE tipoLog = ? AND id_User = ? AND Inform = ? AND data = ? AND hora = ?";

		// Par�metros para a consulta
		$params = [
			'2',            // tipoLog
			$userID,        // id_User
			$id,            // Inform
			date('Y-m-d'),  // data
			date('H:i:s')   // hora
		];

		// Prepara a consulta SQL
		$stmt = odbc_prepare($db, $sqlLOG);

		// Executa a consulta com os par�metros
		$cur = odbc_execute($stmt, $params);

		if (!$cur) {
			$ok = false;
		}

		while (odbc_fetch_row($cur)) {
			// $idLogN = odbc_result($cur,"id_Log");

			//$sqlNOVODETALHE = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('". $idLogN ."','Perfil','".$idRole."','Exclus�o')";
			//echo $sqlnD;
			//$cur=odbc_exec($db,$sqlNOVODETALHE);

			// Consulta parametrizada
			$sqlNOVODETALHE = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";

			// Par�metros para a consulta
			$params = [
				$idLogN,    // id_Log
				'Perfil',   // campo
				$idRole,    // valor
				'Exclus�o'  // alteracao
			];

			// Prepara a consulta SQL
			$stmt = odbc_prepare($db, $sqlNOVODETALHE);

			// Executa a consulta com os par�metros
			$cur = odbc_execute($stmt, $params);
		}
	}

	foreach ($role as $item) {
		//$sqlLOG = "Select * From Log where tipoLog = '2' And id_User = '". $userID ."' And Inform= '". $id ."' And data= '".date('Y-m-d')."' And hora='".date('H:i:s')."' ";
		//echo  $sqlL ."<br>";
		//$cur=odbc_exec($db,$sqlLOG);

		// Consulta parametrizada
		$sqlLOG = "SELECT * FROM Log WHERE tipoLog = ? AND id_User = ? AND Inform = ? AND data = ? AND hora = ?";

		// Par�metros para a consulta
		$params = [
			'2',            // Valor fixo para tipoLog
			$userID,        // id_User
			$id,            // Inform
			date('Y-m-d'),  // Data
			date('H:i:s')   // Hora
		];

		// Prepara a consulta SQL
		$stmt = odbc_prepare($db, $sqlLOG);

		// Executa a consulta com os par�metros
		$cur = odbc_execute($stmt, $params);

		if (!$cur) {
			$ok = false;
		}

		while (odbc_fetch_row($stmt)) {
			$idLogN = odbc_result($stmt, "id_Log");

			//echo $role[$i - 1] ."<br>";
			//$sqlNOVODETALHE = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('".$idLogN."','Perfil','".$item."','Inclus�o')";
			//echo $sqlnD;
			//$cur = odbc_exec($db,$sqlNOVODETALHE);

			// Consulta parametrizada
			$sqlNOVODETALHE = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";

			// Par�metros para a consulta
			$params = [
				$idLogN,    // id_Log
				'Perfil',   // campo (valor fixo)
				$item,      // valor
				'Inclus�o'  // alteracao (valor fixo)
			];

			// Prepara a consulta SQL
			$stmt = odbc_prepare($db, $sqlNOVODETALHE);

			// Executa a consulta com os par�metros
			$cur = odbc_execute($stmt, $params);

			if (!$cur) {
				$ok = false;
			}
		}
	}

	//$sqlDELETEROLE = "DELETE FROM UserRole WHERE idUser = '$id'"; // deleta todas as roles

	//$cur = odbc_exec($db,$sqlDELETEROLE);

	$sqlDELETEROLE = "DELETE FROM UserRole WHERE idUser = ?";

	// Prepara a consulta SQL
	$stmt = odbc_prepare($db, $sqlDELETEROLE);

	// Par�metro para a consulta
	$params = [$id];

	// Executa a consulta com o par�metro
	$cur = odbc_execute($stmt, $params);

	if ($cur) {
		$log_query .= $sqlDELETEROLE;
	}

	if (!$cur) {
		$ok = false;
	}

	foreach ($role as $item) {
		//$sqlNOVAROLE = "INSERT INTO UserRole (idUser, idRole) VALUES ('$id', ".$item.")";
		//    $cur = odbc_exec($db,$sqlNOVAROLE);

		$sqlNOVAROLE = "INSERT INTO UserRole (idUser, idRole) VALUES (?, ?)";

		// Par�metros para a consulta
		$params = [
			$id,    // Valor para idUser
			$item   // Valor para idRole
		];

		// Prepara a consulta SQL
		$stmt = odbc_prepare($db, $sqlNOVAROLE);

		// Executa a consulta com os par�metros
		$cur = odbc_execute($stmt, $params);

		if ($cur) {
			$log_query .= $sqlNOVAROLE;
		}

		if (!$cur) {
			$ok = false;
		}
	}
}

if ($executa == 2) { //Exclus�o do Usu�rio
	$ok = true;
	$cur = odbc_exec($db, "BEGIN TRAN");

	if ($cur) {
		$log_query .= "BEGIN TRAN";
	}

	$sql = "DELETE FROM UserRole WHERE idUser = ?";

	// Prepara a consulta SQL
	$stmt = odbc_prepare($db, $sql);

	// Par�metro para a consulta
	$params = [$id];

	// Executa a consulta com o par�metro
	$cur = odbc_execute($stmt, $params);
	if ($cur) {
		$log_query .= $sql;
	}
	if (!$cur) {
		$msg = "Erro na exclus�o dos perfis.";
		$ok = false;
	}

	$sql = "DELETE FROM Users WHERE id = $id "; // deleta o usu�rio

	//$sql = "INSERT INTO Log VALUES ('3', '$userID','$id', '".date(Y)."-".date(m)."-".date(d)."', '".date(H).":".date(i).":".date(s)."')";
	//odbc_exec($db,$sql);

	$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";

	// Preparar os par�metros
	$params = [
		'3',                   // tipoLog (valor fixo)
		$userID,               // idUser
		$id,                   // inform
		date('Y-m-d'),         // data formatada corretamente
		date('H:i:s')          // hora formatada corretamente
	];

	// Prepara a consulta SQL
	$stmt = odbc_prepare($db, $sql);

	// Executa a consulta com os par�metros
	$cur = odbc_execute($stmt, $params);

	//$cur=odbc_exec($db,$sql);
	if (!$cur) {
		$ok = false;
		$msg = "Erro na exclus�o do usu�rio. Utilize a inativa��o.";
	}

	if ($ok) {
		$cur = odbc_exec($db, "COMMIT TRAN");
		$id = "";

		if ($cur) {
			$log_query .= "COMMIT TRAN";
		}
	} else {
		$cur = odbc_exec($db, "ROLLBACK TRAN");
		$executa = 0;

		if ($cur) {
			$log_query .= "ROLLBACK TRAN";
		}
	}
}

if ($executa == 3) { // Inclus�o do Usu�rio
	$ok = true;
	$pwd = crypt($password, SALT);

	// In�cio da transa��o
	$cur = odbc_exec($db, "BEGIN TRAN ins1");

	// Inser��o do usu�rio
	$sql = "
        INSERT INTO Users (name, login, email, password, state, perfil, i_Perfil)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
	$params = [$name, $login, $email, $pwd, '0', 'F', $perfil_usuario];
	$stmt = odbc_prepare($db, $sql);
	$cur = odbc_execute($stmt, $params);

	if ($cur) {
		$log_query .= $sql;
	}

	// Recupera��o do ID do usu�rio rec�m-inserido
	$sql = "SELECT id FROM Users WHERE name = ? AND login = ? AND email = ? AND password = ? AND state = ? AND perfil = ?";
	$params = [$name, $login, $email, $pwd, '0', 'F'];
	$stmt = odbc_prepare($db, $sql);
	$cur = odbc_execute($stmt, $params);

	if ($cur && odbc_fetch_row($stmt)) {
		$id_u = odbc_result($stmt, "id");

		// Registro no Log
		$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";
		$params = ['2', $userID, $id_u, date('Y-m-d'), date('H:i:s')];
		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, $params);
	}

	$msg = "Usu�rio cadastrado.";

	// Obter o �ltimo ID do usu�rio inserido
	$sql = "SELECT MAX(id) AS id FROM Users";
	$stmt = odbc_prepare($db, $sql);
	$cur = odbc_execute($stmt, []);

	if (!$cur || !odbc_fetch_row($stmt)) {
		$ok = false;
	} else {
		$id = odbc_result($stmt, "id");
	}

	// Inser��o de roles do usu�rio
	if (!empty($role)) {
		foreach ($role as $item) {
			$sql = "INSERT INTO UserRole (idUser, idRole) VALUES (?, ?)";
			$params = [$id, $item];
			$stmt = odbc_prepare($db, $sql);
			$cur = odbc_execute($stmt, $params);

			if (!$cur) {
				$ok = false;
			}
		}
	}

	// Registro dos detalhes no Log
	$sql = "SELECT id_Log FROM Log WHERE id_User = ? AND data = ? AND hora = ?";
	$params = [$userID, date('Y-m-d'), date('H:i:s')];
	$stmt = odbc_prepare($db, $sql);
	$cur = odbc_execute($stmt, $params);

	if ($cur && odbc_fetch_row($stmt)) {
		$idLog = odbc_result($stmt, "id_Log");

		if (!empty($role)) {
			foreach ($role as $item) {
				$sql = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";
				$params = [$idLog, 'Perfil', $item, 'Inclus�o'];
				$stmt = odbc_prepare($db, $sql);
				odbc_execute($stmt, $params);
			}
		}
	}

	// Finaliza a transa��o
	if ($ok) {
		$cur = odbc_exec($db, "COMMIT TRAN ins1");
		if ($cur) {
			$log_query .= "COMMIT TRAN ins1";
		}
	} else {
		$cur = odbc_exec($db, "ROLLBACK TRAN ins1");
		if ($cur) {
			$log_query .= "ROLLBACK TRAN ins1";
		}
	}
}

if ($id) {
	$sql = "SELECT * FROM Users WHERE id = ?";
	$stmt = odbc_prepare($db, $sql);
	$cur = odbc_execute($stmt, [$id]);

	if ($cur && odbc_fetch_row($stmt)) {
		$name = trim(odbc_result($stmt, "name"));
		$login = trim(odbc_result($stmt, "login"));
		$email = trim(odbc_result($stmt, "email"));
		$password = trim(odbc_result($stmt, "password"));
		$state = odbc_result($stmt, "state");
		$perfil_usuario = odbc_result($stmt, "i_Perfil");
	}
}

?>
<div class="conteudopagina">
	<?php if ($executa == 2) { ?>
		<form name="Form1" id="Form1" action="../access/access.php" method="post">
			<li class="campo2colunas">
				<label>Usu&aacute;rio exclu&iacute;do com sucesso.</label>
			</li>
			<div class="barrabotoes">
				<button class="botaoagm" type="button" onClick="this.form.submit();">Ok</button>
			</div>
			<input type="hidden" name="nome" value="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="comm" value="usuarios">

		</form>
	<?php } else { ?>
		<form name="Form1" id="Form1" action="../access/access.php" method="post">
			<?php if ($msg) { ?>
				<script type="text/javascript">
					verErro("<?php echo $msg; ?>");
				</script>
			<?php } ?>

			<?php if ($executa == 99) { ?>
				<script type="text/javascript">
					alert("<?php echo $validacao; ?>");
				</script>
			<?php } ?>


			<div style="clear:both">&nbsp;</div>

			<li class="campo2colunas">
				<label>Nome</label>
				<input type="text" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
					maxlength="200">
			</li>
			<li class="campo2colunas">
				<label>Login</label>
				<input type="text" name="login" value="<?php echo htmlspecialchars($login, ENT_QUOTES, 'UTF-8'); ?>"
					size="20" maxlength="100">
			</li>
			<li class="campo2colunas">
				<label>E-mail</label>
				<input type="text" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
					size="45" maxlength="50">
			</li>

			<?php if ($novo == 1) { ?>
				<li class="campo2colunas">
					<label>Senha</label>
					<input type="password" name="password" value="<?php echo htmlspecialchars($password, ENT_QUOTES, 'UTF-8'); ?>" size="20" maxlength="100">
					</li>
			<?php } ?>
			<li class="campo2colunas">
				<?php
				$sqlP = "Select i_Perfil, Descricao From Perfil Order By Descricao";
				$curP = odbc_exec($db, $sqlP); ?>
				<label>Perfil de Acesso</label>
				<select name="perfil_usuario" id="perfil_usuario">
					<option value="">Selecione...</option>
					<?php while ($dados = odbc_fetch_row($curP)) {
						if ($perfil_usuario == odbc_result($curP, 'i_Perfil')) {
							$select = 'selected';
						} else {
							$select = '';
						}
						?>
						<option value="<?php echo odbc_result($curP, 'i_Perfil'); ?>" <?php echo $select; ?>>
							<?php echo odbc_result($curP, 'Descricao'); ?>
						</option>
					<?php } ?>
				</select>
			</li>

			<div style="clear:both">&nbsp;</div>

			<table summary="Submitted table designs">
				<thead>
					<th>Op&ccedil;&atilde;o</th>
					<th>Perfis</th>
					<th>Status</th>
				</thead>

				<?php $sql = "SELECT * FROM Role ORDER BY UPPER(name)";

				$cur = odbc_exec($db, $sql);

				$i = 0;
				while (odbc_fetch_row($cur)) {
					$idRole = odbc_result($cur, "id");
					$nameRole = odbc_result($cur, "name");

					if ($novo == 1) { ?>
						<TR>
							<TD>
								<?php if ($i == 0) {
									if ($state == 1) { ?>
										Inativo? <input type="checkbox" name="state" value="1" checked>
									<?php } else { ?>
										Inativo? <input type="checkbox" name="state" value="1">
									<?php }
								}
								?>
							</TD>


							<TD width="35%"><?php echo htmlspecialchars($nameRole, ENT_QUOTES, 'UTF-8'); ?></TD>
							<TD><input type="checkbox" name="role[]" value="<?php echo $idRole; ?>"></TD>
						</TR>
					<?php } else {
						?>
						<TR>
							<TD width="15%">
								<?php if ($i == 0) {
									if ($state == 1) { ?>
										Inativo? <input type="checkbox" name="state" value="1" checked>
									<?php } else { ?>
										Inativo? <input type="checkbox" name="state" value="1">
									<?php }
								} ?>
							</TD>

							<TD><?php echo htmlspecialchars($nameRole, ENT_QUOTES, 'UTF-8'); ?></TD>
							<TD>
								<input type="checkbox" name="role[]" value="<?php echo $idRole; ?>" <?php if (testaPerm($db, $id, $idRole)) {
									   echo "checked";
								   } ?>>
							</TD>
						</TR>
					<?php }
					$i++;
				}
				?>
			</table>

			<div style="clear:both">&nbsp;</div>

			<div class="barrabotoes">
				<button class="botaovgm" type="button"
					onClick="javascript:document.Form1.comm.value = 'usuarios';document.Form1.submit();">Voltar</button>

				<?php if ($novo == 1) {
					?>
					<button class="botaoagm" type="button" onClick="javascript:proc(3);">Incluir</button>
				<?php } else {
					?>
					<button class="botaoagm" type="button" onClick="javascript:proc(1);">Alterar</button>
					<button class="botaoagg" type="button" id="js-modal_pass">Alterar Senha</button>
					<button class="botaoagm" type="button" onClick="javascript:proc(2);">Excluir</button>
				<?php }
				?>
			</div>

			<input type="hidden" name="comm" value="usuariosDet">
			<input type="hidden" name="executa" value="0">
			<input type="hidden" name="nome" value="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="cli" value="<?php echo htmlspecialchars($cli, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="LogNome" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="LogLogin" value="<?php echo htmlspecialchars($login, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="LogEmail" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="LogState" value="<?php echo htmlspecialchars($state, ENT_QUOTES, 'UTF-8'); ?>">
			<?php
			$funct = explode("/access/", $_SERVER["REQUEST_URI"]);
			$url_funct = $funct[0] . '/functionary/ajax_change_password.php';
			?>
		</form>
	<?php } ?>
	<div style="clear:both">&nbsp;</div>
</div>

<script language="javascript" type="text/javascript">

	function validaform() {
		var message = '';
		var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");
		var password = $('#password').val();
		var confirm_password = $('#confirm_password').val();

		if (password == '') {
			message = 'Por favor informe a nova senha.';
			$('#message').html(message);
			$('#password').focus();
			return false;
		}

		if (confirm_password == '') {
			message = 'Por favor confirme a nova senha.';
			$('#message').html(message);
			$('#confirm_password').focus();
			return false;
		}

		if (password != confirm_password) {
			message = 'A senha de confirma��o deve ser igual � nova senha.';
			$('#message').html(message);
			$('#confirm_password').focus();
			return false;
		}

		if (!strongRegex.test(password)) {
			message = " A senha deve :<br> - Conter pelo menos 1 caractere alfab�tico min�sculo<br> - Conter pelo menos 1 caractere alfab�tico mai�sculo<br> - Conter pelo menos 1 caractere num�rico<br> - Conter pelo menos um caractere especial<br> - Conter dez caracteres ou mais";
			$('#message').html(message);
			$('#password').focus();
			return false;
		}

		return true;
	}

	$(document).ready(function () {
		$("#js-modal_pass").on("click", function () {
			$(".modal-ext").show();
			$("#js-title").hide();
		});

		$("#close_modal").on("click", function () {
			$(".modal-ext").hide();
		});

		$("#change_pass").on("click", function (event) {

			$('#message').html('');

			if (validaform()) {
				var userID = $("#userID").val();
				var password = $("#password").val();

				$.ajax({
					type: "POST",
					url: '<?php echo htmlspecialchars($url_funct, ENT_QUOTES, 'UTF-8'); ?>',
					data: {
						userID: encodeURIComponent(userID),
						password: encodeURIComponent(password)
					},
					success: function (data) {
						verErro('Senha alterada com sucesso');
					}
				});


				$(".modal-ext").hide();
				event.preventDefault();
			} else {
				event.preventDefault();
			}
		})
	});
</script>

<!-- Modal -->
<div class="modal-ext" style="display:none">
	<div class="bg-black"></div>

	<div class='modal-int'>
		<h1>Alterar Senha</h1>
		<div class="divisoriaamarelo"></div>

		<input type="hidden" name="userID" id="userID" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
		<div id="message" style="color: #f00;font-family: Arial;font-size: 12px;"></div>

		<li class="campo2colunas">
			<label>Nova Senha</label>
			<input type="password" name="password" id="password" size="20" maxlength="100">
		</li>

		<li class="campo2colunas">
			<label>Confirmar Senha</label>
			<input type="password" name="confirm_password" id="confirm_password" size="20" maxlength="100">
		</li>

		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
			<button type="button" class="botaovgm" id="close_modal">Voltar</button>
			<button type="button" class="botaoagg" id="change_pass">Salvar</button>
		</li>

	</div>
</div>
<!-- Fim modal -->