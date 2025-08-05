<?php
if (!check_menu(['generalManager'], $role)) {
    header('HTTP/1.1 403 Forbidden');
    echo "Acesso não autorizado.";
    exit;
}

include_once('../../../navegacao.php');

$x 		= 0;
$nome 	= isset($_POST['nome']) ? $_POST['nome'] : '';
$cli 	= isset($_POST['cli']) ? $_POST['cli'] : '';
?>

<div class="conteudopagina">
	<form name="Form2" action="../access/Access.php" method=post>
		<input type="hidden" name="comm" value="usuariosDet">
		<input type="hidden" name="novo" value="1">
		<button class="botaoagm" type="button" onClick="this.form.submit()" style="margin-bottom: 10px;">Novo</button>
	</form>

	<br style="clear: both">

	<table summary="Submitted table designs" id="example">
		<thead>
			<th scope="col">Nome</th>
			<th scope="col">Login</th>
			<th scope="col">E-mail</th>
			<th scope="col">Perfil de Acesso</th>
			<th scope="col">Situa&ccedil;&atilde;o</th>
		</thead>
		<tbody>
			<?php
			$sql = "SELECT 	U.id, 
							U.name As Nome, 
							U.login As Login, 
							IsNull(P.Descricao, '') As Perfil, 
							U.email As Email,
							Case U.state When 0 then 'Ativo' Else 'Inativo' End As Situacao
						From Users U 
							Left Join Perfil P On P.i_Perfil = U.i_Perfil 
						Where U.perfil = 'F' 
						Order by U.name";

			$cur = odbc_exec($db, $sql);
			$i = 0;
			while (odbc_fetch_row($cur)) {
				$i++;
				$id = odbc_result($cur, "id");
				$name = odbc_result($cur, "Nome");
				$login = odbc_result($cur, "Login");
				$email = odbc_result($cur, "Email");
				$perfil = odbc_result($cur, "Perfil");
				$situacao = odbc_result($cur, "Situacao"); ?>
				<tr <?php echo ($i % 2 == 0 ? 'style="bgcolor = #e9e9e9"' : ""); ?>>
					<td>
						<form id="formUser_<?php echo $id; ?>" method="post" action="../access/Access.php">
						    <input type="hidden" name="comm" value="usuariosDet">
						    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
						    <input type="hidden" name="nome" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
						    <input type="hidden" name="cli" value="<?php echo htmlspecialchars($cli, ENT_QUOTES, 'UTF-8'); ?>">
						</form>
						<a href="#" onclick="document.getElementById('formUser_<?php echo $id; ?>').submit(); return false;">
						    <?php echo $name; ?>
						</a>
					</td>
					<td><?php echo htmlspecialchars($login, ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($perfil, ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($situacao, ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
			<?php }
			if ($i == 0) { ?>
				<tr bgcolor=#e9e9e9>
					<td colspan=3 align=center class="textoBold">Nenhum registro encontrado</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>