<?php  // ALTERADO HICOM EM 28/04/2004
//Alterado HiCom mes 04
include_once('../../../navegacao.php');

$idNotification = $_REQUEST['idNotification'];
$idInform = $_REQUEST['idInform'];
$file = $_REQUEST['file'];

?>

<div class="conteudopagina">
	<ul>
		<li class="campo2colunas">
			<label>Segurado</label>
			<?php echo ($nameClient); ?>
		</li>
		<li class="campo2colunas">
			<label>DPP</label>
			<?php echo $contrat; ?>
		</li>
	</ul>
	<div class="divisoria01"></div>
	<form action="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/Credit.php" method="post"
		name="coface_imp">
		<input type="hidden" name="comm" value="removed">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="file" value="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="remove" value="1">
		<table>
			<thead>
				<tr>
					<th>Excluir</th>
					<th>Comprador</th>
					<th>CRS</th>
					<th>Pa&iacute;s</th>
				</tr>
			</thead>
			<?php
			$i = 1;

			$qryx = "SELECT i.id, i.name, i.c_Coface_Imp, c.name as Pais, c.code
         FROM Importer i
         JOIN ImporterRem ir ON ir.idImporter = i.id
         JOIN Country c ON c.id = i.idCountry
         WHERE i.id IN (SELECT id FROM Importer WHERE idInform = ?)
           AND ir.state = 1
         ORDER BY i.name";

			$stmt = odbc_prepare($db, $qryx);
			odbc_execute($stmt, [$idInform]);

			$xy = $stmt;
			odbc_free_result($stmt);
			$linha = odbc_num_rows($xy);

			while (odbc_fetch_row($xy)) {
				$id = odbc_result($xy, 'id');
				$name = odbc_result($xy, 'name');
				$ci = odbc_result($xy, 'c_Coface_Imp');
				$pais = odbc_result($xy, 'Pais');
				$codigo = odbc_result($xy, 'code'); //Alterado por Tiago V N - Elumini - 07/12/2005
			
				//Criado por Michel Saddock 02/01/2007
			
				// Cessão com Banco do Brasil
				$sql = "SELECT Agencia.name AS agencia, CDBBDetails.idCDBB 
        FROM CDBBDetails 
        JOIN Agencia ON CDBB.idAgencia = Agencia.id
        JOIN CDBB ON CDBBDetails.idCDBB = CDBB.id
        WHERE CDBBDetails.idImporter = ?";

				$stmt = odbc_prepare($db, $sql);
				odbc_execute($stmt, [$id]);

				$cur = $stmt;
				odbc_free_result($stmt);

				while (odbc_fetch_row($cur)) {
					$agencia = odbc_result($cur, 'agencia');
					$resultado = "Banco: Banco do Brasil " . "Ag&ecirc;ncia: " . $agencia . "";
				}

				// Cessão com outros bancos
				$sql2 = "SELECT Banco.name AS banco, CDOB.agencia, CDOBDetails.idCDOB 
         FROM CDOBDetails 
         JOIN CDOB ON CDOBDetails.idCDOB = CDOB.id
         JOIN Banco ON CDOB.idBanco = Banco.id
         WHERE CDOBDetails.idImporter = ?";

				$stmt2 = odbc_prepare($db, $sql2);
				odbc_execute($stmt2, [$id]);

				$cur2 = $stmt2;
				odbc_free_result($stmt2);

				while (odbc_fetch_row($cur2)) {
					$banco2 = odbc_result($cur2, 'banco');
					$agencia2 = odbc_result($cur2, 'agencia');
					$resultado = "Banco: " . $banco2 . "Ag&ecirc;ncia: " . $agencia2 . "";
				}

				// $sql3 = "SELECT idCDParc from CDParcDetails where idImporter = $id";
				$sql3 = "SELECT Agencia.name AS agencia, Banco.name AS banco
         FROM CDParcDetails
         JOIN CDParc ON CDParcDetails.idCDParc = CDParc.id
         JOIN Agencia ON CDParc.idAgencia = Agencia.id
         JOIN Banco ON CDParc.idBanco = Banco.id
         WHERE CDParcDetails.idImporter = ?";

				$stmt3 = odbc_prepare($db, $sql3);
				odbc_execute($stmt3, [$id]);

				$cur3 = $stmt3;
				odbc_free_result($stmt3);

				while (odbc_fetch_row($cur3)) {
					$banco3 = odbc_result($cur3, 'banco');
					$agencia3 = odbc_result($cur3, 'agencia');
					$resultado = "Banco: " . $banco3 . "Ag&ecirc;ncia: " . $agencia3 . "";
				}

				//Fim Criado por Michel Saddock 02/01/2007
				?>
				<input type="hidden" name="idBuyer<?php echo $i; ?>" value="<?php echo $id; ?>">
				<tr>
					<td>
						<div class="formopcao">
							<input type="checkbox" checked name="chkrem<?php echo $i; ?>">
						</div>
					</td>
					<td><?php echo ($name); ?></td>
					<td><?php echo $ci; ?></td>
					<td><?php echo $pais . ' (' . $codigo . ')'; ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan=3><?php
					if ($resultado != "") {
						echo ($i % 2 == 0 ? "<img src=../imagens/estrela1.gif >" : "<img src=../imagens/estrela.gif >") . " Cess&atilde;o " . $resultado . "</td></tr>";
					} else {
						echo "</td></tr>";
					}

					$i++;
					$resultado = "";
			}
			?>
		</table>
		<div class="barrabotoes">
			<!-- this.form.comm.value='notif'; this.form.submit() -->
			<button type="button" onClick="window.location = '../access/Access.php';" class="botaovgm">Voltar</button>
			<button type="submit" name="ok" class="botaoagm">OK</button>
		</div>
	</form>
</div>