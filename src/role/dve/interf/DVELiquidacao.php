<script type="text/javascript" src="<?php echo $host ?>src/scripts/js_dve.js"></script>

<style type="text/css">
	#tbl-responsive table th {
		font-size: 1.1em;
	}
</style>

<script>
	$(document).ready(function () {

		$(".js-concluir").on("click", function () {
			if (!$('.check:checked').length) {
				verErro('Selecione pelo menos uma fatura.');
			} else {
				$(".modal-concluir").show();
			}
		});

		$("#close_modal").on("click", function () {
			$(".modal-concluir").hide();
		});

		$('#valida_checkbox').click(function () {
			if ($(this).prop("checked") == true) {
				$('.checkedDVE').find("input[type='checkbox']").each(function () {
					$(this).prop('checked', true);
				});
			} else {
				$('.checkedDVE').find("input[type='checkbox']").each(function () {
					$(this).prop('checked', false);
				});
			}
		});
	});

	function validaForm() {
		var campo1 = document.getElementById('idInsured').value;
		var campo2 = document.getElementById('idInform').value;

		if (campo1.length == 0) {
			verErro('Voc&ecirc; deve selecionar o cliente.');
			return false;
		} else if (campo2.length == 0) {
			verErro('Voc&ecirc; deve selecionar a ap&oacute;lice.');
			return false;
		} else {
			return true;
		}
	}

</script>
<?php
$CodBanco = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'NULL';

if (isset($_GET['pdf'])) {
    echo '<script>window.open("' . htmlspecialchars($host, ENT_QUOTES, 'UTF-8') . 'src/download/' . urlencode($_GET['pdf']) . '","_blank");</script>';
}


$idInsured = isset($_REQUEST['idInsured']) ? $_REQUEST['idInsured'] : false;

if ($_SESSION['pefil'] == 'B') {
	$perfil = 'banco';
	$t_Usuario = 1;
}

if ($_SESSION['pefil'] == 'C') {
	$perfil = 'segurado';
	$t_Usuario = 0;
}

if ($idInform) {
	$qry = "SELECT name, i_Empresa, n_Apolice, dateEmissionP 
        FROM Inform 
        WHERE id = ?";
	$cur = odbc_prepare($db, $qry);
	odbc_execute($cur, [$idInform]);


	$Nome_Segurado = odbc_result($cur, "name");
	$Data_Emissao = Convert_Data_Geral(substr(odbc_result($cur, "dateEmissionP") ?? '', 0, 10));
	$n_Empresa = odbc_result($cur, "i_Empresa");
	$n_Apolice = odbc_result($cur, "n_Apolice");
	odbc_free_result($cur);

	/* $sql = "EXEC SPR_BB_Consulta_Liquidacao_Faturamento ?, ?, ?, ?";
			 $rsSql = odbc_prepare($db, $sql);
			 odbc_execute($rsSql, ['100', $idInform, $t_Usuario, NULL]); */

	$sql = "EXEC SPR_BB_Consulta_Liquidacao_Faturamento '100', '" . $idInform . "', '" . $t_Usuario . "', NULL";
	$rsSql = odbc_exec($db, $sql);

	$dados = array();
	while (odbc_fetch_row($rsSql)) {

		$Id_DVE = odbc_result($rsSql, "Id_DVE");
		$CRS = odbc_result($rsSql, "CRS");
		$Nome_Comprador = odbc_result($rsSql, "Nome_Comprador");
		$Nome_Pais = odbc_result($rsSql, "Nome_Pais");
		$n_fatura = odbc_result($rsSql, "n_fatura");
		$d_Embarque = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Embarque"), 0, 10));
		$d_Vencimento = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Vencimento"), 0, 10));
		$v_Embarcado = number_format(odbc_result($rsSql, "v_Embarcado"), 2, ",", ".");
		$v_PROEX = number_format(odbc_result($rsSql, "v_PROEX"), 2, ",", ".");
		$v_ACE = number_format(odbc_result($rsSql, "v_ACE"), 2, ",", ".");
		$n_Operacao = odbc_result($rsSql, "n_Operacao");
		$d_Pagamento = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Pagamento"), 0, 10));
		$v_Saldo = number_format(odbc_result($rsSql, "v_Saldo"), 2, ",", ".");
		$v_Pago = number_format(odbc_result($rsSql, "v_Pago"), 2, ",", ".");


		$dados[] = array(
			"Id_DVE" => $Id_DVE,
			"CRS" => $CRS,
			"Nome_Comprador" => $Nome_Comprador,
			"Nome_Pais" => $Nome_Pais,
			"n_fatura" => $n_fatura,
			"d_Embarque" => $d_Embarque,
			"d_Vencimento" => $d_Vencimento,
			"v_Embarcado" => $v_Embarcado,
			"v_PROEX" => $v_PROEX,
			"v_ACE" => $v_ACE,
			"n_Operacao" => $n_Operacao,
			"d_Pagamento" => $d_Pagamento,
			"v_Saldo" => $v_Saldo,
			"v_Pago" => $v_Pago
		);
	}

	//error_log(print_r($dados, true));
	// odbc_free_result($rsSql);

}

if ($perfil == 'banco') {
	$sqlCli = "SELECT DISTINCT Inf.idInsured, Inf.name 
            FROM UsersNurim UN
                INNER JOIN Agencia A ON A.idNurim = UN.idNurim
                INNER JOIN CDBB CB ON CB.idAgencia = A.id
                INNER JOIN Inform Inf ON Inf.id = CB.idInform
                INNER JOIN DVE DVE ON DVE.idInform = Inf.id
            WHERE Inf.state IN (10, 11) AND CB.status = 2 AND DVE.state = 2 AND UN.idUser = ?";
	$rsSqlCli = odbc_prepare($db, $sqlCli);
	odbc_execute($rsSqlCli, [$userID]);


	$dados_cli = array();
	while (odbc_fetch_row($rsSqlCli)) {
		$idInsuredCli = odbc_result($rsSqlCli, "idInsured");
		$nameCli = odbc_result($rsSqlCli, "name");

		$dados_cli[] = array(
			"idInsured" => $idInsuredCli,
			"name" => $nameCli
		);
	}
	odbc_free_result($rsSqlCli);
	$sqlAp = "SELECT DISTINCT Inf.id, Inf.n_Apolice 
            FROM UsersNurim UN
                INNER JOIN Agencia A ON A.idNurim = UN.idNurim
                INNER JOIN CDBB CB ON CB.idAgencia = A.id
                INNER JOIN Inform Inf ON Inf.id = CB.idInform
                INNER JOIN DVE DVE ON DVE.idInform = Inf.id
            WHERE Inf.state = 10 AND CB.status = 2 AND DVE.state = 2 AND Inf.idInsured = ?";
	$rsSqlAp = odbc_prepare($db, $sqlAp);
	odbc_execute($rsSqlAp, [$idInsured]);


	$dados_ap = array();
	while (odbc_fetch_row($rsSqlAp)) {
		$idAp = odbc_result($rsSqlAp, "id");
		$n_Ap = odbc_result($rsSqlAp, "n_Apolice");

		$dados_ap[] = array(
			"id" => $idAp,
			"n_Apolice" => $n_Ap
		);
	}

	odbc_free_result($rsSqlAp);
} else {
	$sqlCli = "SELECT Inf.id, Inf.n_Apolice, Inf.idInsured, Inf.name 
            FROM Inform Inf 
            WHERE Inf.id = ?";
	$rsSqlCli = odbc_prepare($db, $sqlCli);
	odbc_execute($rsSqlCli, [$idInform]);


	$n_Apolice = odbc_result($rsSqlCli, "n_Apolice");
	$idInsured = odbc_result($rsSqlCli, "idInsured");
	$name = odbc_result($rsSqlCli, "name");

	odbc_free_result($rsSqlCli);
}

require_once("../../../navegacao.php");
?>

<div class="conteudopagina">

	<form name="consultarDVE" id="consultarDVE" action="<?php echo $root; ?>role/dve/Dve.php" method="post">
		<input type="hidden" name="comm" value="DVELiquidacao">
		<input type="hidden" name="buscar" value="1">

		<?php if ($perfil == 'banco') { ?>
			<li class="campo3colunas">
				<label>Segurado:</label>
				<select name="idInsured" id="idInsured" onChange="BuscaApolice(this.value, <?php echo $CodBanco; ?>);">
					<option value="">Selecione...</option>
					<?php
					if ($dados_cli) {
						for ($i = 0; $i < count($dados_cli); $i++) {
							if ($dados_cli[$i]['idInsured'] == $idInsured) {
								$selected = 'selected';
							} else {
								$selected = '';
							} ?>
							<option value="<?php echo $dados_cli[$i]['idInsured']; ?>" <?php echo $selected; ?>>
								<?php echo $dados_cli[$i]['name']; ?>
							</option>
							<?php
						}
					}
					?>
				</select>
			</li>

			<li class="campo3colunas">
				<label>N&uacute;mero Ap&oacute;lice:</label>

				<select name="idInform" id="idInform">
					<option value="0">Selecione...</option>
					<?php
					if ($dados_ap) {
						for ($i = 0; $i < count($dados_ap); $i++) {
							if ($dados_ap[$i]['id'] == $idInform) {
								$selected = 'selected';
							} else {
								$selected = '';
							} ?>
							<option value="<?php echo $dados_ap[$i]['id']; ?>" <?php echo $selected; ?>>
								<?php echo $dados_ap[$i]['n_Apolice']; ?>
							</option>
							<?php
						}
					}
					?>
				</select>
			</li>
		<?php } else { ?>
			<input type="hidden" name="idInsured" id="idInsured"
				value="<?php echo htmlspecialchars($idInsured, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="idInform" id="idInform"
				value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">


			<li class="campo3colunas">
				<label>Segurado:</label>
				<input type="text" value="<?php echo $name; ?>" readonly>
			</li>

			<li class="campo3colunas">
				<label>N&uacute;mero Ap&oacute;lice:</label>
				<input type="text" value="<?php echo $n_Apolice; ?>" readonly>
			</li>
		<?php } ?>

		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
			<button type="button" class="botaoagm"
				onClick="javascript: if(validaForm())consultarDVE.submit();">Pesquisar</button>
		</li>
	</form>

	<?php if (isset($_REQUEST['buscar'])) { ?>
		<form name="concluirAlteracao" id="concluirAlteracao" action="<?php echo $root; ?>role/dve/Dve.php" method="post">
			<input type="hidden" name="comm" value="alterDVELiquidacao">
			<input type="hidden" name="idInsured" id="idInsured"
				value="<?php echo htmlspecialchars($idInsured, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">


			<div id="tbl-responsive" style="width:950px;">
				<table summary="" id="" style="font-size: 9px;">
					<thead>
						<tr>
							<th style="width: 12px; padding-left: 3px;"><input type="checkbox" id="valida_checkbox"
									style="width: 11px;"></th>
							<th>N&ordm; SBCE</th>
							<th>Comprador</th>
							<th>Pa&iacute;s</th>
							<th>N&ordm; da Fatura</th>
							<th>Data Embarque</th>
							<th>Data Vencimento</th>
							<th>Valor Embarcado</th>
							<th>PROEX</th>
							<th>ACE</th>
							<th>N&ordm; da Opera&ccedil;&atilde;o</th>
							<th>Data Pagamento</th>
							<th>Valor Pago</th>
							<th>Saldo</th>
						</tr>
					</thead>

					<?php if (empty($dados)) { ?>
						<tbody>
							<tr>
								<td valign="top" colspan="20" class="dataTables_empty">Nenhum dado retornado na tabela</td>
							</tr>
						</tbody>
						<?php
					} else {
						for ($a = 0; $a < count($dados); $a++) { ?>
							<tr>
								<td class="checkedDVE" style="width: 12px; padding-left: 3px;"><input type="checkbox" class="check"
										name="idDve[]" value="<?php echo $dados[$a]['Id_DVE']; ?>" style="width: 11px;"></td>
								<td><?php echo $dados[$a]['CRS']; ?></td>
								<td><?php echo $dados[$a]['Nome_Comprador']; ?></td>
								<td><?php echo $dados[$a]['Nome_Pais']; ?></td>
								<td><?php echo $dados[$a]['n_fatura']; ?></td>
								<td><?php echo $dados[$a]['d_Embarque']; ?></td>
								<td><?php echo $dados[$a]['d_Vencimento']; ?></td>
								<td><?php echo $dados[$a]['v_Embarcado']; ?></td>
								<td><?php echo $dados[$a]['v_PROEX']; ?></td>
								<td><?php echo $dados[$a]['v_ACE']; ?></td>
								<td><?php echo $dados[$a]['n_Operacao']; ?></td>
								<td><?php echo $dados[$a]['d_Pagamento']; ?></td>
								<td><?php echo $dados[$a]['v_Pago']; ?></td>
								<td><?php echo $dados[$a]['v_Saldo']; ?></td>
							</tr>
						<?php } ?>
					<?php } ?>
				</table>
			</div>
		</form>

		<div class="barrabotoes">
			<button class="botaoagg js-concluir" type="button">Concluir Altera&ccedil;&otilde;es</button>
		</div>
	<?php } ?>
</div>

<!-- Modal Concluir -->
<div class="modal-concluir" style="display:none">
	<div class="bg-black"></div>

	<div class='modal-int'>
		<h1>Aten&ccedil;&atilde;o</h1>
		<div class="divisoriaamarelo"></div>

		<li class="campo2colunas" style="width: 690px;">
			<label>&nbsp;</label>
			<p>Deseja confirmar a liquida&ccedil;&atilde;o das faturas selecionadas? Esse processo n&atilde;o
				poder&aacute; ser revertido.</p>
		</li>

		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
			<button type="button" class="botaovgm" id="close_modal">N&atilde;o</button>
			<button type="button" class="botaoagm" onClick="javascript: concluirAlteracao.submit();">Sim</button>
		</li>

	</div>
</div>
<!-- Fim modal -->