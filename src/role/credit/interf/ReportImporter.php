<?php
require_once("../../../navegacao.php"); ?>

<div class="conteudopagina">
	<?php
	$anoMes = $_POST["anoMes"];
	$ano = substr($anoMes, 0, 4);
	$mes = substr($anoMes, 5);
	$sql = "SELECT name FROM Inform WHERE id = ?";
	$stmt = odbc_prepare($db, $sql);
	odbc_execute($stmt, [$idInform]);
	$cur = $stmt;
	odbc_free_result($stmt);
	$name = "";

	if (odbc_fetch_row($cur)) {
		$name = odbc_result($cur, 1);
	}
	?>

	<li class="campo2colunas">
		<label>Cobran&ccedil;a referente</label>
		<?php echo htmlspecialchars("$mes/$ano", ENT_QUOTES, 'UTF-8'); ?>
	</li>

	<li class="campo2colunas">
		<label>Segurado</label>
		<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
	</li>

</div>

<div class="conteudopagina">
	<table id="example">
		<thead>
			<tr>
				<th style="text-align: left;">Comprador</th>
				<th width="10%" style="text-align: left;">Pa&iacute;s</th>
				<th width="9%" style="text-align: center;">Cr&eacute;dito Solicit. US$ Mil</th>
				<th width="9%" style="text-align: center;">Cr&eacute;dito Conced. US$ Mil</th>
				<th width="7%" style="text-align: center;">An&aacute;lise R$</th>
				<th width="7%" style="text-align: center;">Monitor. R$</th>
				<th width="7%" style="text-align: center;">Total R$</th>
				<th width="10%" style="text-align: left;">Motivo</th>
			</tr>
		</thead>

		<tbody>
			<?php
			if ($mes < 12 && $ano <= 2008) {
				require_once('../credit/fechamento.php');

				$cur = odbc_exec($db, $query);
				$count = 0;

				while (odbc_fetch_row($cur)) {
					$idBuyer = odbc_result($cur, 'idBuyer');
					$impName = odbc_result($cur, 'impName');
					$countryName = odbc_result($cur, 'countryName');
					$creditSolic = odbc_result($cur, 'creditSolic') / 1000;
					$credit = odbc_result($cur, 'credit') / 1000;
					$creditTemp = odbc_result($cur, 'creditTemp') / 1000;
					$limTemp = odbc_result($cur, 'limTemp');
					$stateDate = odbc_result($cur, 'stateDate');
					$qtdStateDate = odbc_result($cur, 'qtdstateDate');
					$stateChange = odbc_result($cur, 'state');
					$ImpState = odbc_result($cur, 'Impstate');
					$monitor = 0;
					$mot = '';

					if (odbc_result($cur, 'monitor') > 0) {
						$monitor = odbc_result($cur, 'txMonitor') / 4;
						$totM += $monitor;
						$mot = 'Monitoramento';
					}

					$analyse = 0;

					if (odbc_result($cur, 'analyse') > 0) {
						$analyse = odbc_result($cur, 'txAnalyse');
						$totA += $analyse;
						$mot .= ($mot != '' ? ' e ' : '') . 'Análise';
					}

					if ($limTemp <> '' && $creditTemp > 0) {
						$credit += $creditTemp;
					}

					$entrar = true;

					if ($stateChange == 7 and $qtdStateDate > 90) {
						$entrar = false;
					}

					if ($ImpState == 8) {
						$entrar = false;
					}

					if ($entrar) {
						?>
						<tr>
							<?php
							if ($generated) {
								echo "<td>" . htmlspecialchars($impName, ENT_QUOTES, 'UTF-8') . "</td>";
							} else {
								echo "<td><a href='Credit.php?comm=viewReportImport&anoMes=" . urlencode($anoMes) . "&idInform=" . urlencode($idInform) . "&origem=" . urlencode($origem) . "&idBuyer=" . urlencode($idBuyer) . "&ret=" . urlencode($_REQUEST['ret']) . "'>" . htmlspecialchars($impName, ENT_QUOTES, 'UTF-8') . "</a></td>";
							}


							?>
							<td><?php echo ($countryName); ?></td>
							<td style="text-align: right;"><?php echo number_format($creditSolic, 0, ',', '.'); ?></td>
							<td style="text-align: right;">
								<?php echo $credit == '' ? '0' : number_format($credit, 0, ",", "."); ?>&nbsp;
							</td>
							<td style="text-align: right;">
								<?php echo $analyse != 0 ? number_format($analyse, 2, ',', '.') : '0,00'; ?>
							</td>
							<td style="text-align: right;">
								<?php echo $monitor != 0 ? number_format($monitor, 2, ',', '.') : '0,00'; ?>
							</td>
							<td style="text-align: right;"><?php echo number_format($monitor + $analyse, 2, ',', '.'); ?></td>
							<td><?php echo ($mot); ?></td>
						</tr>

						<?php $count++;
					}//Verificação sobre analise monitoramento.
				}

				?>
			</tbody>

			<tfoot>
				<tr bgcolor="#cccccc">
					<th colspan=4 align="left" class="textoBold">Total</th>
					<th class="textoBold"><?php echo $totA > 0 ? number_format($totA, 2, ',', '.') : '&nbsp;'; ?></th>
					<th class="textoBold"><?php echo $totM > 0 ? number_format($totM, 2, ',', '.') : '&nbsp;'; ?></th>
					<th class="textoBold"><?php echo number_format($totA + $totM, 2, ',', '.'); ?></th>
					<th class="textoBold">&nbsp;</th>
				</tr>
			</tfoot>
		</table>

		<?php if ($origem == 1) { ?>
			<form action="<?php echo $root; ?>role/credit/Credit.php">
				<input type="hidden" name="comm" value="resMonitor">
			<?php } else if ($origem == 2) { ?>
					<form action="<?php echo $root; ?>role/searchClient/ListClient.php">
						<input type="hidden" name="comm" value="view">
				<?php } else { ?>
						<form action="<?php echo $root; ?>role/client/Client.php">
							<input type="hidden" name="comm" value="open">
					<?php } ?>

					<input type="hidden" name="mes" value="<?php echo htmlspecialchars($mes, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="ano" value="<?php echo htmlspecialchars($ano, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="idInform"
						value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">

					<input name="ret" value="<?php echo htmlspecialchars($_REQUEST['ret'], ENT_QUOTES, 'UTF-8'); ?>"
						type="hidden">

					<div class="barrabotoes">
						<button class="botaoagm" type="submit">Voltar</button>
					</div>
				</form>

				<p>
					<a href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/interf/Demonstrativo.php?idInform=<?php echo urlencode($idInform); ?>&mes=<?php echo urlencode($mes); ?>&ano=<?php echo urlencode($ano); ?>&key=<?php echo urlencode(session_id() . time()); ?>"
						target="_blank">
						Vers&atilde;o em pdf desta p&aacute;gina222
					</a>
				</p>

				<?php
			} else {
				/* $consulta = odbc_exec($db, "select idImporter,importador,creditoSolicitado,creditoConcedido,txAnalise,txMonitor,total,motivo 
																								  from resFatAnaliseMonitorImport where idInform = $idInform and ano=$ano and mes=$mes"); */

				$sql = "SELECT imp.idImporter, imp.importador, imp.creditoSolicitado, imp.creditoConcedido, imp.txAnalise, imp.txMonitor, imp.total, imp.motivo, c.name
			FROM resFatAnaliseMonitorImport AS imp
			LEFT JOIN Importer AS imp2 ON imp.idImporter = imp2.id
			LEFT JOIN Country AS c ON c.code = imp.codPais
			WHERE imp.idInform = ? AND imp.ano = ? AND imp.mes = ?
			ORDER BY imp.importador";

				$consulta = odbc_prepare($db, $sql);
				odbc_execute($consulta, [$idInform, $ano, $mes]);


				$count = 1;
				$totalCobrado = 0;
				$totA = 0;
				$totM = 0;
				while (odbc_fetch_row($consulta)) {
					($count % 2) ? $cor = "" : $cor = " bgcolor=#e9e9e9";
					echo "<tr>";
					$importador = odbc_result($consulta, 2);
					$creditSolic = odbc_result($consulta, 3);
					$credit = odbc_result($consulta, 4);
					$analyse = odbc_result($consulta, 5);
					$monitor = odbc_result($consulta, 6);
					$total = odbc_result($consulta, 7);
					$mot = odbc_result($consulta, 8);
					$cidade = odbc_result($consulta, 9);

					$totalCobrado += $total;
					$totA += $analyse;
					$totM += $monitor;
					echo "<td>" . ($importador) . "</td>";
					?>
					<td><?php echo ($cidade); ?></td>
					<td style="text-align: right;"><?php echo number_format($creditSolic / 1000, 0, ',', '.'); ?></td>
					<td style="text-align: right;">
						<?php echo $credit == '' ? '0' : number_format($credit / 1000, 0, ",", "."); ?>
					</td>
					<td style="text-align: right;"><?php echo $analyse != 0 ? number_format($analyse, 2, ',', '.') : '0,00'; ?>
					</td>
					<td style="text-align: right;">
						<?php echo $monitor != 0 ? number_format($monitor / 4, 2, ',', '.') : '0,00'; ?>
					</td>
					<td style="text-align: right;"><?php echo number_format($total, 2, ',', '.'); ?></td>
					<td><?php echo ($mot); ?></td>
					<?php echo "</tr>";
					$count++;
				}
				odbc_free_result($consulta);
				?>

				</tbody>

				<tfoot>
					<tr>
						<th colspan=4 align="left" class="textoBold">Total</th>
						<th><?php echo $totA > 0 ? number_format($totA, 2, ',', '.') : '&nbsp;'; ?></th>
						<th><?php echo $totM > 0 ? number_format($totM / 4, 2, ',', '.') : '&nbsp;'; ?></th>
						<th><?php echo number_format($totalCobrado, 2, ',', '.'); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>

				</table>

				<br><br>

				<div class="barrabotoes">
					<button class="botaoagm" onClick="javascript:window.history.back()">Voltar</button>
				</div>

				<p>
					<a href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/interf/Demonstrativo.php?idInform=<?php echo urlencode($idInform); ?>&mes=<?php echo urlencode($mes); ?>&ano=<?php echo urlencode($ano); ?>&key=<?php echo urlencode(session_id() . time()); ?>"
						target="_blank">
						Vers&atilde;o em pdf desta p&aacute;gina
					</a>
				</p>
				<p>
					<a href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/interf/geraExcel.php?idInform=<?php echo urlencode($idInform); ?>&mes=<?php echo urlencode($mes); ?>&ano=<?php echo urlencode($ano); ?>&key=<?php echo urlencode(session_id() . time()); ?>"
						target="_blank">
						Vers&atilde;o em excel desta p&aacute;gina
					</a>
				</p>
			<?php } ?>
</div>