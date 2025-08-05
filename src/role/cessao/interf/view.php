<?php


include_once('../../../navegacao.php');



if (!$tipoBanco)
	$tipoBanco = $_REQUEST['tipoBanco'];


/*$idInform         =  $_REQUEST['idInform'];
$idCDBB           =  $_REQUEST['idCDBB'];
$idCDOB           =  $_REQUEST['idCDOB'];
$idCDParc         =  $_REQUEST['idCDParc'];
$idBanco          =  $_REQUEST['idBanco'];
$idNotification   =  $_REQUEST['idNotification'];
*/

// Funcao para extrair os valores de _REQUEST para suas variaveis apropriadas
//extract($_REQUEST);

if ($tipoBanco == 3) {
	$stmt = odbc_prepare($db, "SELECT inf.name, cdob.agencia, bc.name as bcname, cdob.status FROM Inform inf JOIN CDOB cdob ON (cdob.idInform = inf.id) JOIN Banco bc ON (cdob.idBanco = bc.id) WHERE inf.id = ? AND cdob.id = ?");
	$resulx = odbc_execute($stmt, array($idInform, $idCDOB));
	$rows = odbc_fetch_array($stmt);
	$nameInf = $rows ? $rows['name'] : '';
	$agencia = $rows ? $rows['agencia'] : '';
	$banco = $rows ? $rows['bcname'] : '';
	$status = $rows ? $rows['status'] : '';
} else if ($tipoBanco == 1) {
	$stmt = odbc_prepare($db, "SELECT inf.name, cdbb.idAgencia, ag.name as agname, cdbb.status FROM Inform inf JOIN CDBB cdbb ON (cdbb.idInform = inf.id) JOIN Agencia ag ON (cdbb.idAgencia = ag.id) WHERE inf.id = ? AND cdbb.id = ?");
	$resulx = odbc_execute($stmt, array($idInform, $idCDBB));
	$rows = odbc_fetch_array($stmt);
	$nameInf = $rows ? $rows['name'] : '';
	$agencia = $rows ? $rows['agname'] : '';
	$status = $rows ? $rows['status'] : '';
} else {
	$stmt = odbc_prepare($db, "SELECT inf.name, cdparc.idAgencia, ag.name as agname, cdparc.status, bc.name as bcname FROM Inform inf JOIN CDParc cdparc ON (cdparc.idInform = inf.id) JOIN Agencia ag ON (cdparc.idAgencia = ag.id) JOIN Banco bc ON (cdparc.idBanco = bc.id) WHERE inf.id = ? AND cdparc.id = ?");
	$resulx = odbc_execute($stmt, array($idInform, $idCDParc));
	$rows = odbc_fetch_array($stmt);
	$nameInf = $rows ? $rows['name'] : '';
	$agencia = $rows ? $rows['agname'] : '';
	$status = $rows ? $rows['status'] : '';
	$banco = $rows ? $rows['bcname'] : '';
}
?>

<div class="conteudopagina">
	<h2>
		<?php if (isset($banco)) {
			echo htmlspecialchars($banco, ENT_QUOTES, 'UTF-8');
		} else {
			echo "Banco do Brasil";
		} ?>
	</h2>
	<ul>
		<li class="campo2colunas">
			<label>Exportador</label>
			<?php echo htmlspecialchars($nameInf, ENT_QUOTES, 'UTF-8'); ?>
		</li>
		<li class="campo2colunas">
			<label>Ag&ecirc;ncia</label>
			<?php echo htmlspecialchars($agencia, ENT_QUOTES, 'UTF-8'); ?>
		</li>
	</ul>
	<table>
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>C&oacute;d. Cess&atilde;o</th>
				<th>Raz&atilde;o</th>
				<th>Pa&iacute;s</th>
			</tr>
		</thead>
		<tbody>
			<?php if ($tipoBanco == 3) {
				$stmt = odbc_prepare($db, "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
					        FROM Importer imp
					              JOIN Country c ON (imp.idCountry = c.id)
					              JOIN CDOBDetails cd ON (imp.id = cd.idImporter)
					              JOIN CDOB cb ON (cd.idCDOB = cb.id)
					        WHERE imp.idInform = ?
					              AND cb.status <> 3
					              AND cb.id = ?
					        ORDER BY imp.name");
				$resulx = odbc_execute($stmt, array($idInform, $idCDOB));
			} else if ($tipoBanco == 1) {
				$stmt = odbc_prepare($db, "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
					        FROM Importer imp
					              JOIN Country c ON (imp.idCountry = c.id)
					              JOIN CDBBDetails cd ON (imp.id = cd.idImporter)
					              JOIN CDBB cb ON (cd.idCDBB = cb.id)
					        WHERE imp.idInform = ?
					              AND cb.status <> 3
					              AND cb.id = ?
					        ORDER BY imp.name");
				$resulx = odbc_execute($stmt, array($idInform, $idCDBB));
			} else {
				$stmt = odbc_prepare($db, "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
					        FROM Importer imp
					              JOIN Country c ON (imp.idCountry = c.id)
					              JOIN CDParcDetails cd ON (imp.id = cd.idImporter)
					              JOIN CDParc cb ON (cd.idCDParc = cb.id)
					        WHERE imp.idInform = ?
					              AND cb.status <> 3
					              AND cb.id = ?
					        ORDER BY imp.name");
				$resulx = odbc_execute($stmt, array($idInform, $idCDParc));
			}

			$i = 0;
			while ($row = odbc_fetch_array($stmt)) {
				$i++;
				$dateEnv = $row['dateClient'];

				list($ano, $mes, $dia) = explode('-', $dateEnv);
				?>
				<tr>
					<td><?php echo htmlspecialchars($i, ENT_QUOTES, 'UTF-8') ?></td>
					<td><?php echo htmlspecialchars($row['codigo'] . "/" . $ano, ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($row['impName'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($row['cName'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
			<?php } // while ?>
			<?php if ($i == 0) { ?>
				<tr>
					<td colspan="4">Nenhum Importador Cadastrado</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<form action="<?php echo $root; ?>role/cessao/Cessao.php" method="post">
		<input type="hidden" name="comm">
<input type="hidden" name="idInform" value="<?php echo htmlspecialchars((int)$idInform, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="idCDBB" value="<?php echo htmlspecialchars((int)$idCDBB, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="idCDOB" value="<?php echo htmlspecialchars((int)$idCDOB, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="idCDParc" value="<?php echo htmlspecialchars((int)$idCDParc, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="idBanco" value="<?php echo htmlspecialchars((int)$idBanco, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="tipoBanco" value="<?php echo htmlspecialchars((int)$tipoBanco, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="idNotification" value="<?php echo htmlspecialchars((int)$idNotification, ENT_QUOTES, 'UTF-8'); ?>">

		<div class="barrabotoes"> <a href="../access/Access.php">
				<button name="voltar" onclick="" class="botaovgm">Voltar</button>
			</a>
			<?php if ($status == 3) { ?>
				<button name="ok" onClick="this.form.comm.value='donenotif';this.form.submit()" class="botaoagm">OK</button>
			<?php } else { ?>
				<button name="Recusar" onClick="this.form.comm.value='cancelarBB';this.form.submit()"
					class="botaovgm">Recusar</button>
				<button name="Aceitar" onClick="this.form.comm.value='aceitarBB';this.form.submit()"
					class="botaoagm">Aceitar</button>
			<?php } ?>
		</div>
	</form>
</div>