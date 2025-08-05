<?php  //echo "linha 3 em listbuyers.php";

$idNotification = $_REQUEST['idNotification'];
$idInform = $_REQUEST['idInform'];
$origem = isset($_REQUEST['origem']) ? $_REQUEST['origem'] : 2;

$query = "SELECT * FROM Inform WHERE id = ?";
$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform]);


$nMoeda = odbc_result($cur, "currency");
$Periodo_Vigencia = odbc_result($cur, "Periodo_Vigencia");

if ($nMoeda == "1") {
	$extMoeda = "R$";
	$moeda = "Real";
} else if ($nMoeda == "2") {
	$extMoeda = "US$";
	$moeda = "Dolár";
} else if ($nMoeda == "6") {
	$extMoeda = "&euro;";
	$moeda = "Euro";
} else {
	$extMoeda = "";
	$moeda = "";
}

if ($vigencia == "") {
	$pvigencia = "12 Meses";
} else if ($vigencia == "1") {
	$pvigencia = "12 Meses";
} else {
	$pvigencia = "24 Meses";
}

if ($Periodo_Vigencia) {
	$pvigencia = $Periodo_Vigencia . " Meses";
}
?>

<script language="JavaScript">
	function validaBotao() {
		document.getElementById('operacao').value = "voltar";
		document.forms[1].submit();
	}


</script>

<?php
include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
	<ul>
		<li class="campo2colunas">
			<label>Segurado</label>
			<?php echo ($nameExpo); ?>
		</li>

		<li class="campo2colunas">
			<label>DPP</label>
			<?php echo $ciExpo; ?>
		</li>

		<li class="campo2colunas">
			<label>Per&iacute;odo de Vig&ecirc;ncia</label>
			<?php echo ($pvigencia); ?>
		</li>

		<li class="campo2colunas">
			<label>Moeda</label>
			<?php echo ($moeda); ?>
		</li>
	</ul>
	<div class="divisoria01"></div>

	<form action="<?php echo $root; ?>role/credit/Credit.php" method="post" name="coface_imp">
		<input type="hidden" name="comm" value="c_Coface_Imp">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">

		<?php

		if ($comm == 'view' || $comm == 'clientChangeImporterInsert') {
			$state = 1;
			$state_scape = $state; //variável que recebe o mesmo valor do state
			//	$hold = ' AND hold = 0';
			$hold = ' AND (hold = 0 or hold = 1)';
			$union = '';

			if ($flag_renovacao) {
				$union = "UNION SELECT Importer.id, Importer.name, Country.name, Country.code, " .
					"Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state " .
					"FROM Importer, Country " .
					"WHERE Importer.idInform = $idInform AND " .
					"Importer.idCountry = Country.id AND " .
					"Importer.state=6 AND Importer.creditAut=1 ";
			}
		} else {
			$state = 2;
			$hold = '';
		}

		if ($state_scape == 1) {
			$state = 1;
			//$hold = ' AND hold = 0';
			$hold = ' AND (hold = 0 or hold = 1)';
			$union = '';

			if ($flag_renovacao) {
				$union = "UNION SELECT Importer.id, Importer.name, Country.name, Country.code, " .
					"Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state " .
					"FROM Importer, Country " .
					"WHERE Importer.idInform = $idInform AND " .
					"Importer.idCountry = Country.id AND " .
					"Importer.state=6 AND Importer.creditAut=1 ";
			}
		}

		$cSql = "SELECT Importer.id, Importer.name, Country.name, Country.code, 
                Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, 
                Importer.idTwin, Importer.Easy_Number 
         FROM Importer, Country 
         WHERE Importer.idInform = ? AND 
               Importer.idCountry = Country.id AND (
                   ((Importer.state = '0' OR Importer.state = ?) $hold AND 
                   (Importer.creditAut IS NULL OR Importer.creditAut = 0))
                   " . ($flag_renovacao ? " OR (Importer.state = 1 AND Importer.hold = 1)" : '') . "
               ) 
               AND Importer.id NOT IN (SELECT idImporter FROM ImporterRem) 
         ORDER BY Importer.name";

		$params = [$idInform, $state];
		$cur = odbc_prepare($db, $cSql);
		odbc_execute($cur, $params);


		$i = 1;

		if (isset($includeOld)) {
			$msgRes = "O comprador ser&aacute; inclu&iacute;do na ap&oacute;lice vigente e na renova&ccedil;&atilde;o";
		}


		// print $cSql;
		
		if (odbc_fetch_row($cur)) {
			if ($idAnt > 0) { // renovacao
				$msgRes = "Compradores novos";
				$novos = 0;
			}

			?>

			<table class="tabela01">
				<caption><?php echo $msgRes; ?></caption>
				<thead>
					<tr>
						<th scope="col">Comprador</th>
						<th scope="col">Pa&iacute;s</th>
						<th scope="col">Cod. Pa&iacute;s</th>
						<th scope="col">Cr&eacute;dito Solicitado <?php echo $extMoeda; ?> Mil</th>
						<th scope="col">Easy Number</th>
						<th scope="col">CRS</th>
						<th scope="col">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$linha = odbc_num_rows($cur);
					$i = 0;

					while (odbc_fetch_row($cur)) {
						//for($i = 0; $i < $linha; $i++){ // dim DO){
						$idBuyer = odbc_result($cur, 1); //chave de busca para linkar o informe
						$nameBuyer = odbc_result($cur, 2);
						$nameCountry = odbc_result($cur, 3);
						$idCountry = odbc_result($cur, 4);
						$cId = odbc_result($cur, 'cId');
						$limiteCredit = odbc_result($cur, 5) / 1000;
						$ciCoface = odbc_result($cur, 6);
						$idOther = odbc_result($cur, 8);
						$EasyNumber = odbc_result($cur, 9);

						if (!$idOther) {
							$query = "SELECT id FROM Importer WHERE idTwin = ?";
							$y = odbc_prepare($db, $query);
							odbc_execute($y, [$idBuyer]);
							$idOther = odbc_result($y, 1);
							odbc_free_result($y);
						}

						$query = "SELECT COUNT(*) FROM ChangeCredit WHERE idImporter = ?";
						$qr = odbc_prepare($db, $query);
						odbc_execute($qr, [$idBuyer]);
						$n = odbc_result($qr, 1);
						odbc_free_result($n);


						if (isset($aut)) {
							$query = "SELECT credit FROM ChangeCredit WHERE idImporter = ? ORDER BY id DESC";
							$rr = odbc_prepare($db, $query);
							odbc_execute($rr, [$idBuyer]);


							if (odbc_fetch_row($rr)) {
								$last_credit = odbc_result($rr, 1);
							}
							odbc_Free_result($rr);
						}

						$limiteCredit = number_format($limiteCredit, 0, ",", ".");

						if ($idOther && !$idAnt) {
							$tem_asterisco = 1;
						}

						if ($i % 2 == 0) {
							$cor = 'class="odd"';
						} else {
							$cor = '';
						}

						$i++;

						?>
						<tr <?php echo $cor; ?>>
							<td>
								<a
									href="Credit.php?comm=showBuyers&idBuyer=<?php echo urlencode($idBuyer); ?>&origem=<?php echo urlencode($origem); ?>&idInform=<?php echo urlencode($field->getField('idInform')); ?>&state_scape=<?php echo urlencode($state_scape); ?>&idNotification=<?php echo urlencode($field->getField('idNotification')); ?>&flag_renovacao=<?php echo urlencode($flag_renovacao); ?>">
									<?php echo htmlspecialchars($nameBuyer, ENT_QUOTES, 'UTF-8') . ($idOther && !$idAnt ? '*' : ''); ?>
								</a>
							</td>

							<td><?php echo ($nameCountry); ?></td>
							<td><?php echo $idCountry; ?></td>
							<td><?php echo $limiteCredit; ?></td>

							<td>
								<input size="14" maxlength="14" type="text" name="<?php echo "EasyNumber" . $i; ?>" id=""
									value="<?php echo $EasyNumber > 0 ? $EasyNumber : ''; ?>">
							</td>

							<td>
								<input type="hidden" name="<?php echo "importer" . $i; ?>" id="<?php echo "importer" . $i; ?>"
									value="<?php echo $idBuyer; ?>">
								<input type="hidden" name="<?php echo "name" . $i; ?>" id="<?php echo "name" . $i; ?>"
									value="<?php echo $nameBuyer; ?>">
								<input type="hidden" name="<?php echo "country" . $i; ?>" id="<?php echo "country" . $i; ?>"
									value="<?php echo $cId; ?>">
								<input type="hidden" name="<?php echo "credit" . $i; ?>" value="">

								<input size="9" maxlength="9" type="text" id="cCoface<?php echo $i; ?>"
									name="cCoface<?php echo $i; ?>" value="<?php echo $ciCoface > 0 ? $ciCoface : ''; ?>"
									onblur="buscardados(<?php echo $i; ?>,0);">
							</td>

							<td>
								<button name="recusar"
									onClick="document.forms['rejeitar'].idBuyer.value='<?php echo $idBuyer; ?>';if(confirm('Recusar o comprador <?php echo $nameBuyer; ?>?')){ document.forms['rejeitar'].submit(); }"
									class="botaovgg">Recusar Comprador</button>
							</td>
						</tr>

						<?php

						if ($idAnt > 0) {
							$novos++;
						}
					}

					if (isset($tem_asterisco)) { ?>
						<tr <?php echo $cor; ?>>
							<td colspan="6">
								* Estes compradores ser&atilde;o inclu&iacute;dos na ap&oacute;lice vigente e na
								renova&ccedil;&atilde;o
							</td>
						</tr>
					<?php }
		} else { ?>
				</tbody>
			</table>
		<?php }

		/*******************************************************************************************/
		// Renovação: importadores antigos
		?>

		<?php

		if ($idAnt > 0 || $dateEmission) {
			$cQuery = "SELECT Importer.id, Importer.name, Country.name, Country.code, 
                   Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, 
                   Importer.state 
            FROM Importer, Country 
            WHERE Importer.idInform = ? AND 
                  Importer.idCountry = Country.id AND 
                  (Importer.state = '0' OR Importer.state = ?) 
                  $hold 
                  AND Importer.creditAut = 1 
                  AND Importer.id NOT IN (SELECT idImporter FROM ImporterRem) 
                  $union
            ORDER BY Importer.id";

			$params = [$idInform, $state];
			$cur2 = odbc_prepare($db, $cQuery);
			odbc_execute($cur2, $params);

		}

		if (($idAnt > 0 || $dateEmission) && odbc_fetch_row($cur2)) { ?>
			<input type=hidden name="flag_renovacao" value="<?php echo $flag_renovacao; ?>">

			<table class="tabela01">
				<thead>
					<tr>
						<th scope="col">Comprador</th>
						<th scope="col">Pa&iacute;s</th>
						<th scope="col">Cod. Pa&iacute;s</th>
						<th scope="col">Cr&eacute;dito Solicitado <?php echo $extMoeda; ?> Mil</th>
						<th scope="col">Easy Number</th>
						<th scope="col">CRS</th>
						<th scope="col">Cr&eacute;dito Concedido <?php echo $extMoeda; ?> Mil</th>
						<th scope="col" colspan="2">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					<?php do {
						$idBuyer = odbc_result($cur2, 1); // chave de busca para linkar o informe
						$nameBuyer = odbc_result($cur2, 2);
						$nameCountry = odbc_result($cur2, 3);
						$idCountry = odbc_result($cur2, 4);
						$cId = odbc_result($cur2, 'cId');
						$limiteCredit = odbc_result($cur2, 5) / 1000;
						$ciCoface = odbc_result($cur2, 6);
						$status = odbc_result($cur2, 8);

						odbc_free_result($cur2);

						$query = "SELECT credit FROM ChangeCredit WHERE idImporter = ? ORDER BY id DESC";
						$rr = odbc_prepare($db, $query);
						odbc_execute($rr, [$idBuyer]);


						if (odbc_fetch_row($rr)) {
							$last_credit = odbc_result($rr, 1) / 1000;
						}

						if ($flag_renovacao && $limiteCredit >= $last_credit && $status != 1) {
							// se for renovacao e o credito nao foi reduzido, nao mostra
							continue;
						}

						if ($i % 2 == 0) {
							$cor = 'class="odd"';
						} else {
							$cor = '';
						}

						odbc_free_result($rr);

						$i++;

						?>

						<tr <?php echo $cor; ?>>
							<td><a
									href="Credit.php?comm=showBuyers&idBuyer=<?php echo urlencode($idBuyer); ?>&origem=<?php echo urlencode($origem); ?>&idInform=<?php echo urlencode($field->getField('idInform')); ?>&state_scape=<?php echo urlencode($state_scape); ?>&idNotification=<?php echo urlencode($field->getField('idNotification')); ?>&flag_renovacao=<?php echo urlencode($flag_renovacao); ?>">
									<?php echo htmlspecialchars($nameBuyer, ENT_QUOTES, 'UTF-8') . ($idOther && !$idAnt ? '*' : ''); ?>
								</a>

							</td>
							<td class="texto"><?php echo ($nameCountry); ?></td>
							<td class="texto"><?php echo $idCountry; ?></td>
							<td class="texto" align=center><?php echo number_format($limiteCredit, 0, ",", "."); ?></td>
							<td class="texto"><?php echo ""; ?></td>
							<td class="texto">
								<input type=hidden name="<?php echo "importer" . $i; ?>" id="<?php echo "importer" . $i; ?>"
									value="<?php echo $idBuyer; ?>">
								<input type="hidden" name="<?php echo "name" . $i; ?>" value="<?php echo $nameBuyer; ?>">
								<input type="hidden" name="<?php echo "country" . $i; ?>" id="<?php echo "country" . $i; ?>"
									value="<?php echo $cId; ?>">
								<input type="hidden" name="<?php echo "renovacao" . $i; ?>" value="1">

								<input type="text" class="semformatacao" size="20" maxlength="6"
									name="<?php echo "cCoface" . $i; ?>" value="<?php echo $ciCoface > 0 ? $ciCoface : ''; ?>"
									<?php echo $ciCoface > 0 ? "onFocus='blur()'" : ''; ?>>
							</td>
							<input size="9" maxlength="9" type="hidden" id="cCoface<?php echo $i; ?>"
								name="cCoface<?php echo $i; ?>" value="<?php echo $ciCoface > 0 ? $ciCoface : ''; ?>"
								onblur="buscardados(<?php echo $i; ?>,0);">

							<td class="texto"><input type="hidden" name="<?php echo "credit" . $i; ?>"
									value="<?php echo $limiteCredit; ?>">
								<input type="hidden" name="<?php echo "last_credit" . $i; ?>"
									value="<?php echo $last_credit; ?>">
								<?php if ($limiteCredit <= $last_credit) {
									echo "$limiteCredit";
								} else {
									echo "Solicitar cr&eacute;dito na Coface";
								}
								?>
							</td>

							<td>
								<div class="formopcao"><input type="checkbox" name="<?php echo "flag" . $i; ?>"
										id="<?php echo "flag" . $i; ?>"></div>
							</td>

							<?php if ($idAnt) { //if($ciCoface > 0 && $idAnt){ ?>
								<td>
									<button name="recusar"
										onClick="document.forms[3].idBuyer.value='<?php echo $idBuyer; ?>';if(confirm('Recusar o comprador <?php echo $nameBuyer; ?>?')){ document.forms[3].submit(); }"
										class="botaovgm">Recusar</button>
								</td>
							<?php } else { ?>
								<td>&nbsp;</td>
							<?php } ?>
						</tr>
						<?php
					} while (odbc_fetch_row($cur2));
		}

		if ($i == 0) {
			?>
					<table summary="Submitted table designs" class="tabela01">
						<thead>
							<tr>
								<th scope="col">Comprador</th>
								<th scope="col">Pa&iacute;s</th>
								<th scope="col">Cod. Pa&iacute;s</th>
								<th scope="col">Cr&eacute;dito Solicitado <?php echo $extMoeda; ?> Mil</th>
								<th scope="col">Easy Number</th>
								<th scope="col">CRS</th>
								<th scope="col">&nbsp;</th>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td colspan="6">Nenhum Comprador encontrado</td>
							</tr>
						<?php }
		?>
					</tbody>

					<tfoot>
						<tr>
							<th colspan="9">&nbsp;</th>
						</tr>
					</tfoot>
				</table>

				<div class="barrabotoes">
					<input type="hidden" name="i" value="<?php echo $i; ?>">
					<input type="hidden" name="operacao" id="operacao" value="">

					<!--<button name="voltar" onClick="<?php echo $root . 'access/Access.php'; ?>" class="botaovgm">Voltar</button>-->
					<button name="voltar" onClick="document.forms[1].submit();" class="botaovgm">Voltar</button>

					<?php if ($comm != 'clientChangeImporterInsert') { ?>
						<button name="recusar" onClick="if(confirm('Recusar Informe?')){ document.forms[2].submit(); }"
							class="botaovgg">Recusar Informe</button>
					<?php } ?>

					<?php if ($comm != 'clientChangeImporterInsert') { ?>
						<button name="cancelar" onClick="if(confirm('Cancelar Informe?')){ document.forms[4].submit(); }"
							class="botaovgg">Cancelar Informe</button>
					<?php } ?>

					<input type="hidden" name="novos" value="<?php echo $novos; ?>">
					<input type="hidden" name="val" id="val" value="" />
					<!--<button name="demanda" type="submit" class="botaoagg">Demanda OK</button>-->
					<!--<button name="demanda" type="button" class="botaoagg" onclick=" if(validaCRS(<?php echo $i; ?>,0)) document.coface_imp.submit(); ">Demanda OK</button>-->
					<button name="demanda" type="button" class="botaoagg"
						onclick="document.coface_imp.submit(); ">Demanda OK</button>
				</div>
	</form>

	<form action="<?php echo $root; ?>role/credit/Credit.php" method="post">
		<input type="hidden" name="comm" value="open"> <!--Botão Volta-->
	</form>

	<form action="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/Credit.php" method="post">
		<input type="hidden" name="comm" value="return"><!--Botão Recusar Informe-->
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
	</form>

	<form name="rejeitar" id="rejeitar"
		action="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/Credit.php" method="get">
		<input type="hidden" name="comm" value="rejeitar"> <!--Botão Recusar Importador-->
		<input type="hidden" name="idBuyer" value="">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
	</form>

	<form action="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/Credit.php" method="post">
		<input type="hidden" name="comm" value="cancelar"><!--Botão Cancelar Informe-->
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
	</form>


	<p><?php echo $msg; ?></p>

	<div style="clear:both">&nbsp;</div>
</div>

<script>
	function validaCRS(id, val) {
		if (val == 1) {
			var x = 0;
			for (i = 1; i <= id; i++) {
				x = buscardados(i, val);
			}
		}

		if (val == 0) {
			validaCRS(id, 1);
		}


		if (val == 2) {
			return false;
		} else {
			return true;
		}


	}

	function buscardados(i, val) {

		var importer = document.getElementById('importer' + i).value;
		var cCoface = document.getElementById('cCoface' + i).value;
		var country = document.getElementById('country' + i).value;

		var dados = '';
		var urlstr = '../credit/interf/busca_crs_ajax.php?idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idImporter=' + importer +
			'&crs=' + cCoface + '&paisID=' + country;
		var valor;
		valor = $.post(urlstr,
			function (data) {
				if (data != '') {
					dados = ('Atenção!\n' + data);
					alert(dados);
					document.getElementById('cCoface' + i).value = '';
					validaCRS(0, 2);
				}

			}

		);

	}

</script>