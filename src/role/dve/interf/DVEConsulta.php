<?php

$idInsured = isset($_REQUEST['idInsured']) ? $_REQUEST['idInsured'] : false;
$CodBanco = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'NULL';
$Segurado = isset($_REQUEST['Segurado']) ? $_REQUEST['Segurado'] : false;
$Comprador = isset($_REQUEST['Comprador']) ? $_REQUEST['Comprador'] : false;
$Fatura = isset($_REQUEST['Fatura']) ? $_REQUEST['Fatura'] : false;
$NotNPC1 = isset($_REQUEST['NotNPC1']) ? $_REQUEST['NotNPC1'] : false;
$SituacaoDVE = isset($_REQUEST["SituacaoDVE"]) ? $_REQUEST["SituacaoDVE"] : false;
$Num_CRS = isset($_REQUEST['Num_CRS']) ? $_REQUEST['Num_CRS'] : false;
$n_Apolice = isset($_REQUEST['n_Apolice']) ? $_REQUEST['n_Apolice'] : false;
$dataAtual = date('d/m/Y');
$filtro_dve = false;
$return_proc = false;

$idInform = $n_Apolice ? $n_Apolice : 'NULL';

$dados = array();
if (isset($_REQUEST['buscar'])) {
	$filtro_dve = true;
	$SPR_Id_Inform = $idInform !== null ? $idInform : '';
	$SPR_Nome_Comprador = $Comprador !== null ? $Comprador : '';
	$SPR_Num_Fatura = $Fatura !== null ? $Fatura : '';
	$SPR_s_Declaracao = $SituacaoDVE !== null ? $SituacaoDVE : 0;
	$SPR_CRS = $Num_CRS !== null ? $Num_CRS : '';

	$sqlProc = "EXEC SPR_BB_Consulta_Declaracao_Faturamento ?, ?, ?, ?, ?";
	$exSqlProc = odbc_prepare($db, $sqlProc);
	odbc_execute($exSqlProc, [$SPR_Id_Inform, $SPR_Nome_Comprador, $SPR_Num_Fatura, $SPR_s_Declaracao, $SPR_CRS]);

	$logMessage = sprintf(
    "Valores do DVEConsulta.php: SPR_Id_Inform=%s, SPR_Nome_Comprador=%s, SPR_Num_Fatura=%s, SPR_s_Declaracao=%s, SPR_CRS=%s",
    var_export($SPR_Id_Inform, true),
    var_export($SPR_Nome_Comprador, true),
    var_export($SPR_Num_Fatura, true),
    var_export($SPR_s_Declaracao, true),
    var_export($SPR_CRS, true)
);



	while (odbc_fetch_row($exSqlProc)) {
		$return_proc = true;

		$CodDVE = odbc_result($exSqlProc, "CodDVE");
		$n_Apolice = odbc_result($exSqlProc, "n_Apolice");
		$Num_DPP = odbc_result($exSqlProc, "Num_DPP");
		$Nome_Segurado = odbc_result($exSqlProc, "Nome_Segurado");
		$d_Inicio_Vigencia = odbc_result($exSqlProc, "d_Inicio_Vigencia") ? ymd2dmy(odbc_result($exSqlProc, "d_Inicio_Vigencia")) : "";
		$d_Fim_Vigencia = odbc_result($exSqlProc, "d_Fim_Vigencia") ? ymd2dmy(odbc_result($exSqlProc, "d_Fim_Vigencia")) : "";
		$s_Apolice = odbc_result($exSqlProc, "s_Apolice");
		$Moeda = odbc_result($exSqlProc, "Moeda");
		$p_Cobertura = number_format(odbc_result($exSqlProc, "p_Cobertura"), 0, "", "");
		$v_LMI = odbc_result($exSqlProc, "v_LMI") ? number_format(odbc_result($exSqlProc, "v_LMI"), 2, ',', '.') : "";
		$v_LMI_Disponivel = odbc_result($exSqlProc, "v_LMI_Disponivel") ? number_format(odbc_result($exSqlProc, "v_LMI_Disponivel"), 2, ',', '.') : "";
		$v_Exposicao_Total = odbc_result($exSqlProc, "v_Exposicao_Total") ? number_format(odbc_result($exSqlProc, "v_Exposicao_Total"), 2, ',', '.') : "";
		$v_Premio_Emitido = odbc_result($exSqlProc, "v_Premio_Emitido") ? number_format(odbc_result($exSqlProc, "v_Premio_Emitido"), 2, ',', '.') : "";
		$v_Premio_Pago = odbc_result($exSqlProc, "v_Premio_Pago") ? number_format(odbc_result($exSqlProc, "v_Premio_Pago"), 2, ',', '.') : "";
		$v_Premio_Vencido = odbc_result($exSqlProc, "v_Premio_Vencido") ? number_format(odbc_result($exSqlProc, "v_Premio_Vencido"), 2, ',', '.') : "";
		$v_Sinistro_Pago = odbc_result($exSqlProc, "v_Sinistro_Pago") ? number_format(odbc_result($exSqlProc, "v_Sinistro_Pago"), 2, ',', '.') : "";
		$v_Sinistro_Pendente = odbc_result($exSqlProc, "v_Sinistro_Pendente") ? number_format(odbc_result($exSqlProc, "v_Sinistro_Pendente"), 2, ',', '.') : "";
		$CRS = odbc_result($exSqlProc, "CRS");
		$Nome_Comprador = odbc_result($exSqlProc, "Nome_Comprador");
		$Nome_Pais = odbc_result($exSqlProc, "Nome_Pais");
		$v_Credito_Concedido = odbc_result($exSqlProc, "v_Credito_Concedido") ? number_format(odbc_result($exSqlProc, "v_Credito_Concedido"), 2, ',', '.') : "";
		$v_Limite_Disponivel = odbc_result($exSqlProc, "v_Limite_Disponivel") ? number_format(odbc_result($exSqlProc, "v_Limite_Disponivel"), 2, ',', '.') : "";
		$Cod_Cessao = odbc_result($exSqlProc, "Cod_Cessao");
		$n_Fatura = odbc_result($exSqlProc, "n_Fatura");
		$d_Embarque = odbc_result($exSqlProc, "d_Embarque") ? ymd2dmy(odbc_result($exSqlProc, "d_Embarque")) : "";
		$d_Vencimento = odbc_result($exSqlProc, "d_Vencimento") ? ymd2dmy(odbc_result($exSqlProc, "d_Vencimento")) : "";
		$d_Venc = odbc_result($exSqlProc, "d_Vencimento") ? odbc_result($exSqlProc, "d_Vencimento") : "";
		$v_Embarque = odbc_result($exSqlProc, "v_Embarque") ? odbc_result($exSqlProc, "v_Embarque") : "";
		$v_Embarque_format = odbc_result($exSqlProc, "v_Embarque") ? number_format(odbc_result($exSqlProc, "v_Embarque"), 2, ',', '.') : "";
		$n_Operacao = odbc_result($exSqlProc, "n_Operacao");
		$d_Pagamento = odbc_result($exSqlProc, "d_Pagamento") ? ymd2dmy(odbc_result($exSqlProc, "d_Pagamento")) : "";
		$d_Prorrogacao = odbc_result($exSqlProc, "d_Prorrogacao") ? ymd2dmy(odbc_result($exSqlProc, "d_Prorrogacao")) : "";
		$v_Pago = odbc_result($exSqlProc, "v_Pago") ? number_format(odbc_result($exSqlProc, "v_Pago"), 2, ',', '.') : "";
		$v_Saldo = odbc_result($exSqlProc, "v_Saldo") ? number_format(odbc_result($exSqlProc, "v_Saldo"), 2, ',', '.') : "";
		$s_Fatura = odbc_result($exSqlProc, "s_Fatura");
		$t_Declaracao = odbc_result($exSqlProc, "t_Declaracao");

		$p_Alter = false;
		if ($_SESSION['pefil'] == 'B') {
			if ($t_Declaracao == 1) {
				$p_Alter = true;
			}
		}

		if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'F') {
			if ($t_Declaracao == 0) {
				$p_Alter = true;
			}
		}

		$dados[] = array(
			"CodDVE" => $CodDVE,
			"n_Apolice" => $n_Apolice,
			"Num_DPP" => $Num_DPP,
			"Nome_Segurado" => $Nome_Segurado,
			"d_Inicio_Vigencia" => $d_Inicio_Vigencia,
			"d_Fim_Vigencia" => $d_Fim_Vigencia,
			"s_Apolice" => $s_Apolice,
			"Moeda" => $Moeda,
			"p_Cobertura" => $p_Cobertura,
			"v_LMI" => $v_LMI,
			"v_LMI_Disponivel" => $v_LMI_Disponivel,
			"v_Exposicao_Total" => $v_Exposicao_Total,
			"v_Premio_Emitido" => $v_Premio_Emitido,
			"v_Premio_Pago" => $v_Premio_Pago,
			"v_Premio_Vencido" => $v_Premio_Vencido,
			"v_Sinistro_Pago" => $v_Sinistro_Pago,
			"v_Sinistro_Pendente" => $v_Sinistro_Pendente,
			"CRS" => $CRS,
			"Nome_Comprador" => $Nome_Comprador,
			"Nome_Pais" => $Nome_Pais,
			"v_Credito_Concedido" => $v_Credito_Concedido,
			"v_Limite_Disponivel" => $v_Limite_Disponivel,
			"Cod_Cessao" => $Cod_Cessao,
			"n_Fatura" => $n_Fatura,
			"d_Embarque" => $d_Embarque,
			"d_Vencimento" => $d_Vencimento,
			"d_Venc" => $d_Venc,
			"v_Embarque" => $v_Embarque,
			"v_Embarque_format" => $v_Embarque_format,
			"n_Operacao" => $n_Operacao,
			"d_Pagamento" => $d_Pagamento,
			"d_Prorrogacao" => $d_Prorrogacao,
			"v_Pago" => $v_Pago,
			"v_Saldo" => $v_Saldo,
			"s_Fatura" => $s_Fatura,
			"t_Declaracao" => $t_Declaracao,
			"p_Alter" => $p_Alter
		);
	}

	odbc_free_result($exSqlProc);
}

if ($_SESSION['pefil'] == 'B') {
	$sqlCli = "SELECT DISTINCT Inf.idInsured, Inf.name
            FROM UsersNurim UN
            INNER JOIN Agencia A ON A.idNurim = UN.idNurim
            INNER JOIN CDBB CB ON CB.idAgencia = A.id
            INNER JOIN Inform Inf ON Inf.id = CB.idInform
            INNER JOIN DVE DVE ON DVE.idInform = Inf.id
            WHERE Inf.state IN (10, 11) AND CB.status = 2 AND DVE.state = 2 AND UN.idUser = ?
            ORDER BY 2";

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

	$dados_ap = array();
	if ($idInsured != '') {
		$sqlAp = "SELECT DISTINCT Inf.id, Inf.n_Apolice
            FROM UsersNurim UN
            INNER JOIN Agencia A ON A.idNurim = UN.idNurim
            INNER JOIN CDBB CB ON CB.idAgencia = A.id
            INNER JOIN Inform Inf ON Inf.id = CB.idInform
            INNER JOIN DVE DVE ON DVE.idInform = Inf.id
            WHERE Inf.state IN (10, 11) AND CB.status = 2 AND DVE.state = 2 AND UN.idUser = ? AND Inf.idInsured = ?";

		$rsSqlAp = odbc_prepare($db, $sqlAp);
		odbc_execute($rsSqlAp, [$userID, $idInsured]);


		while (odbc_fetch_row($rsSqlAp)) {
			$idAp = odbc_result($rsSqlAp, "id");
			$n_Ap = odbc_result($rsSqlAp, "n_Apolice");

			$dados_ap[] = array(
				"id" => $idAp,
				"n_Apolice" => $n_Ap
			);
		}
		odbc_free_result($rsSqlAp);
	}
} else {
	$idInform = $idInform != 'NULL' ? $idInform : $_REQUEST['idInform'];
	$sqlCli = "SELECT Inf.id, Inf.n_Apolice, Inf.idInsured, Inf.name 
           FROM Inform Inf 
           WHERE Inf.id = ?";
	$rsSqlCli = odbc_prepare($db, $sqlCli);
	
	odbc_execute($rsSqlCli, [$idInform]);


	$infId = odbc_result($rsSqlCli, "id");
	$n_Apolice = odbc_result($rsSqlCli, "n_Apolice");
	$idInsured = odbc_result($rsSqlCli, "idInsured");
	$infName = odbc_result($rsSqlCli, "name");

	odbc_free_result($rsSqlCli);
}


if ($filtro_dve) {
	if (!$return_proc) {
		echo '<script>verErro("N&atilde;o existem faturamentos para os filtros informados. Favor verificar")</script>';
	}
}
?>

<script type="text/javascript" src="<?php echo $host ?>src/scripts/js_dve.js"></script>

<style>
	.tabelaDVE td,
	th {
		border: 0px dotted #cccccc;
		padding: 8px;
		color: #777777;
		background: none;
	}
</style>

<?php include_once('../../../navegacao.php'); ?>

<body onLoad="BuscaApolice('<?php echo htmlspecialchars($idInsured, ENT_QUOTES, 'UTF-8'); ?>', <?php echo $CodBanco; ?>);">

	<div class="conteudopagina">
		<form name="consultarDVE" id="consultarDVE" action="<?php echo $root; ?>role/dve/Dve.php" method="post">
			<input type="hidden" name="comm" value="DVEConsulta">
			<input type="hidden" name="buscar" value="1">
			<input type="hidden" name="Exportar" id="Exportar" value="">
			<input type="hidden" id="perfil" value="<?php echo $_SESSION['pefil']; ?>">

			<?php if ($_SESSION['pefil'] == 'B') { ?>
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
			<?php } else { ?>
				<li class="campo3colunas">
					<label>Segurado:</label>
					<input type="hidden" name="idInsured" id="idInsured" value="<?php echo htmlspecialchars($idInsured, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="text" value="<?php echo $infName; ?>" readonly="readonly">
				</li>
			<?php } ?>

			<li class="campo3colunas">
				<label>Comprador:</label>
				<input type="text" name="Comprador" id="Comprador" value="<?php echo htmlspecialchars($Comprador, ENT_QUOTES, 'UTF-8'); ?>"
					onKeyUp="this.value=this.value.toUpperCase()" maxlength="200">
			</li>

			<li class="campo3colunas">
				<label>N&ordm; SBCE do Comprador:</label>
				<input type="text" name="Num_CRS" id="Num_CRS" value="<?php echo htmlspecialchars($Num_CRS, ENT_QUOTES, 'UTF-8'); ?>"
					maxlength="9">&nbsp;&nbsp;
			</li>

			<?php if ($_SESSION['pefil'] == 'B') { ?>
				<li class="campo3colunas">
					<label>N&uacute;mero Ap&oacute;lice:</label>

					<select name="n_Apolice" id="n_Apolice">
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
				<li class="campo3colunas">
					<label>N&uacute;mero Ap&oacute;lice:</label>
					<input type="hidden" name="n_Apolice" id="n_Apolice" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="text" value="<?php echo htmlspecialchars($n_Apolice, ENT_QUOTES, 'UTF-8'); ?>" readonly="readonly">
				</li>
			<?php } ?>

			<li class="campo3colunas">
				<label>Situa&ccedil;&atilde;o</label>
				<select name="SituacaoDVE" id="SituacaoDVE">
					<option value="0" <?php echo htmlspecialchars($SituacaoDVE, ENT_QUOTES, 'UTF-8') == 0 ? 'selected' : ''; ?>>Todos</option>
					<option value="1" <?php echo htmlspecialchars($SituacaoDVE, ENT_QUOTES, 'UTF-8') == 1 ? 'selected' : ''; ?>>Vencido</option>
					<option value="2" <?php echo htmlspecialchars($SituacaoDVE, ENT_QUOTES, 'UTF-8') == 2 ? 'selected' : ''; ?>>A Vencer</option>
					<option value="3" <?php echo htmlspecialchars($SituacaoDVE, ENT_QUOTES, 'UTF-8') == 3 ? 'selected' : ''; ?>>Pago</option>
				</select>
			</li>

			<li class="campo3colunas">
				<label>Fatura:</label>
				<input type="text" name="Fatura" id="Fatura" value="<?php echo htmlspecialchars($Fatura, ENT_QUOTES, 'UTF-8'); ?>" maxlength="100">&nbsp;&nbsp;
			</li>

			<li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
				<button type="button" class="botaoagm"
					onClick="javascript: if(validaForm())consultarDVE.submit();">Pesquisar</button>
			</li>
		</form>

		<form name="editDVE" id="editDVE" action="<?php echo $root; ?>role/dve/Dve.php?comm=editDveBanco" method="post">
			<?php if ($_SESSION['pefil'] == 'C') { ?>
				<input type="hidden" name="idInform" id="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
			<?php } ?>

			<?php
			$idInsured = isset($_REQUEST['idInsured']) ? $_REQUEST['idInsured'] : '';
			$Comprador = isset($_REQUEST['Comprador']) ? $_REQUEST['Comprador'] : '';
			$Num_CRS = isset($_REQUEST['Num_CRS']) ? $_REQUEST['Num_CRS'] : '';
			$n_Apolice = isset($_REQUEST['n_Apolice']) ? $_REQUEST['n_Apolice'] : '';
			$SituacaoDVE = isset($_REQUEST['SituacaoDVE']) ? $_REQUEST['SituacaoDVE'] : '';
			$Fatura = isset($_REQUEST['Fatura']) ? $_REQUEST['Fatura'] : '';
			?>

			<input type="hidden" name="idInsured" id="idInsured" value="<?php echo htmlspecialchars($idInsured, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="Comprador" id="Comprador" value="<?php echo htmlspecialchars($Comprador, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="Num_CRS" id="Num_CRS" value="<?php echo htmlspecialchars($Num_CRS, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="n_Apolice" id="n_Apolice" value="<?php echo htmlspecialchars($n_Apolice, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="SituacaoDVE" id="SituacaoDVE" value="<?php echo htmlspecialchars($SituacaoDVE, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="Fatura" id="Fatura" value="<?php echo htmlspecialchars($Fatura, ENT_QUOTES, 'UTF-8'); ?>">

			<?php if (isset($_REQUEST['buscar'])) { ?>
				<div id="MostrarTabela">

					<div id="Retorno" style="display:none"></div>

					<table width="100%" class="tabela01 tabelaDVE">
						<thead>
							<th colspan="6">INFORMA&Ccedil;&Otilde;ES DO SEGURADO</th>
						</thead>

						<?php
						$v_Total_Vencido = 0;
						$v_Total_Pago = 0;
						$v_Total_Pendente = 0;
						$v_Total = 0;
						$Nome_Segurado = "";
						$n_Apolice = "";
						$apo = '';
						$Imp = '';
						for ($i = 0; $i < count($dados); $i++) {
							$Nome_Segurado = $dados[$i]['Nome_Segurado'];
							$n_Apolice = $dados[$i]['n_Apolice'];

							if ($dados[$i]['s_Fatura'] == 'Paga') {
								$v_Total_Pago += $dados[$i]['v_Embarque'];
							} else if ($dados[$i]['s_Fatura'] == 'Vencida') {
								$v_Total_Vencido += $dados[$i]['v_Embarque'];
							} else {
								$v_Total_Pendente += $dados[$i]['v_Embarque'];
							}

							$v_Total += $dados[$i]['v_Embarque'];

							if ($i % 2 == 0) {
								$cor = 'class="odd"';
							} else {
								$cor = '';
							}
							?>

							<?php if ($dados[$i]['p_Alter']) { ?>
								<input type="hidden" name="CodDVE[]" id="CodDVE" value="<?php echo $dados[$i]['CodDVE']; ?>">
							<?php } ?>

							<tbody>
								<?php if ($dados[$i]['n_Apolice'] != $apo) {
									$apo = $dados[$i]['n_Apolice']; ?>
									<tr <?php echo $cor; ?>>
										<td>
											<b>N&ordm; da Ap&oacute;lice:</b>
											<?php echo $dados[$i]['n_Apolice']; ?>
										</td>
										<td colspan="5">
											<b>Segurado:</b>
											<?php echo $dados[$i]['Num_DPP'] . ' - ' . $dados[$i]['Nome_Segurado']; ?>
										</td>
									</tr>

									<tr <?php echo $cor; ?>>
										<td>
											<b>In&iacute;cio de Vig&ecirc;ncia</b> <br>
											<?php echo $dados[$i]['d_Inicio_Vigencia']; ?>
										</td>
										<td>
											<b>Situa&ccedil;&atilde;o da Ap&oacute;lice</b> <br>
											<?php echo $dados[$i]['s_Apolice']; ?>
										</td>
										<td>
											<b>Cobertura da Ap&oacute;lice (%)</b> <br>
											<?php echo $dados[$i]['p_Cobertura']; ?>
										</td>
										<td colspan="3">
											<b>Limite M&aacute;ximo de Indeniza&ccedil;&atilde;o da Ap&oacute;lice (LMI)</b> <br>
											<?php echo $dados[$i]['v_LMI']; ?>
										</td>
									</tr>

									<tr <?php echo $cor; ?>>
										<td>
											<b>Fim de Vig&ecirc;ncia</b> <br>
											<?php echo $dados[$i]['d_Fim_Vigencia']; ?>
										</td>
										<td>
											<b>Moeda</b> <br>
											<?php echo $dados[$i]['Moeda']; ?>
										</td>
										<td>
											<b>Saldo Dispon&iacute;vel do Limite M&aacute;ximo de Indeniza&ccedil;&atilde;o</b> <br>
											<?php echo $dados[$i]['v_LMI_Disponivel']; ?>
										</td>
										<td colspan="3">
											<b>Saldo Dispon&iacute;vel de Cobertura Atual da Ap&oacute;lice</b> <br>
											<?php echo $dados[$i]['v_Exposicao_Total']; ?>
										</td>
									</tr>

									<tr <?php echo $cor; ?>>
										<td>
											<b>Valor do Pr&ecirc;mio Emitido</b> <br>
											<?php echo $dados[$i]['v_Premio_Emitido']; ?>
										</td>
										<td>
											<b>Valor do Pr&ecirc;mio Pago</b> <br>
											<?php echo $dados[$i]['v_Premio_Pago']; ?>
										</td>
										<td>
											<b>Valor do Pr&ecirc;mio Vencido</b> <br>
											<?php echo $dados[$i]['v_Premio_Vencido']; ?>
										</td>
										<td>
											<b>Valor total de Sinistros Indenizados</b> <br>
											<?php echo $dados[$i]['v_Sinistro_Pago']; ?>
										</td>
										<td colspan="2">
											<b>Valor Total dos Sinistros Pendentes</b> <br>
											<?php echo $dados[$i]['v_Sinistro_Pendente']; ?>
										</td>
									</tr>
									<?php
								}
								?>

								<?php
								if ($dados[$i]['Nome_Comprador'] != $Imp) {
									$Imp = $dados[$i]['Nome_Comprador']; ?>
									<tr style="background-color:#999">
										<td colspan="4" align="left">
											<strong>
												<font color="#FFFFFF" size="4">Comprador:
													<?php echo $dados[$i]['Nome_Comprador']; ?>
												</font>
											</strong>
										</td>
										<td colspan="2" align="left">
											<strong>
												<font color="#FFFFFF" size="4">Pa&iacute;s <?php echo $dados[$i]['Nome_Pais']; ?>
												</font>
											</strong>
										</td>
									</tr>
									<?php
								}
								?>

								<tr <?php echo $cor; ?>>
									<td style="padding-left: 80px;">
										<b>Cod. Cess&atilde;o:</b> <br>
										<?php echo $dados[$i]['Cod_Cessao']; ?>
									</td>
									<td>
										<b>N&ordm; da Fatura:</b> <br>
										<?php echo $dados[$i]['n_Fatura']; ?>
									</td>
									<td>
										<b>Data de Embarque:</b> <br>
										<?php echo $dados[$i]['d_Embarque']; ?>
									</td>
									<td>
										<b>Data de Vencimento:</b> <br>
										<?php echo $dados[$i]['d_Vencimento']; ?>
									</td>
									<td colspan="2">
										<b>Situa&ccedil;&atilde;o da Fatura:</b> <br>
										<?php echo $dados[$i]['s_Fatura']; ?>
									</td>
								</tr>

								<tr <?php echo $cor; ?>>
									<td style="padding-left: 80px;">
										<b>Valor do Embarque (<?php echo $dados[$i]['Moeda']; ?>):</b> <br>
										<?php echo $dados[$i]['v_Embarque_format']; ?>
									</td>
									<td>
										<b>Valor do Limite de Cr&eacute;dito Aprovado (<?php echo $dados[$i]['Moeda']; ?>):</b>
										<br>
										<?php echo $dados[$i]['v_Credito_Concedido'] ?>
									</td>
									<td>
										<b>Valor Dispon&iacute;vel do Limite de Cr&eacute;dito
											(<?php echo $dados[$i]['Moeda']; ?>):</b> <br>
										<?php echo $dados[$i]['v_Limite_Disponivel'] ?>
									</td>
									<td colspan="3">
										<b>Saldo Pendente da Fatura (<?php echo $dados[$i]['Moeda']; ?>):</b> <br>
										<?php echo $dados[$i]['v_Saldo']; ?>
									</td>
								</tr>

								<tr <?php echo $cor; ?>>
									<td style="padding-left: 80px;">
										<b>Valor Pago da Fatura (<?php echo $dados[$i]['Moeda']; ?>):</b> <br>
										<?php
										if ($dados[$i]['p_Alter']) { ?>
											<li class="campo3colunas" style="width:180px;">
												<label></label>
												<input type="hidden" class="v_Original"
													value="<?php echo $dados[$i]['v_Embarque']; ?>">
												<input type="text" name="v_Pago[]" id="v_Pago" class="v_Pago"
													style="text-align:right; margin-bottom: 0px; height: 12px;"
													value="<?php echo $dados[$i]['v_Pago']; ?>">
											</li>
											<?php
										} else {
											echo $dados[$i]['v_Pago'];
										} ?>
									</td>
									<td>
										<b>Num. Opera&ccedil;&atilde;o:</b> <br>
										<?php
										if ($dados[$i]['p_Alter']) { ?>
											<li class="campo3colunas" style="width:180px;">
												<label></label>
												<input type="text" name="Num_Operacao[]" id="Num_Operacao"
													value="<?php echo $dados[$i]['n_Operacao']; ?>"
													style="margin-bottom: 0px; height: 12px;">
											</li>
											<?php
										} else {
											echo $dados[$i]['n_Operacao'];
										} ?>
									</td>
									<td>
										<b>Data de Pagamento da Fatura:</b> <br>
										<?php
										if ($dados[$i]['p_Alter']) { ?>
											<li class="campo3colunas" style="width:180px;">
												<label></label>
												<input type="text" name="DataLiq[]" id="DataLiq"
													value="<?php echo $dados[$i]['d_Pagamento']; ?>" size="8" maxlength="10"
													OnKeyPress="formatar(this, '##/##/####');"
													onBlur="validaDat(this,this.value); checarDatas(this,this.value,'<?php echo $dados[$i]['d_Embarque']; ?>','data de embarque.');"
													style="margin-bottom: 0px; height: 12px;">
											</li>
											<?php
										} else {
											echo $dados[$i]['d_Pagamento'];
										} ?>
									</td>
									<td colspan="3">
										<b>Data Vencimento Prorrogada:</b> <br>
										<?php
										if ($dados[$i]['p_Alter']) { ?>
											<li class="campo3colunas" style="width:180px;">
												<label></label>
												<input type="hidden" class="dtVenc" value="<?php echo $dados[$i]['d_Venc']; ?>">
												<input type="text" name="DataProrrogacao[]" id="DataProrrogacao"
													class="DataProrrogacao" value="<?php echo $dados[$i]['d_Prorrogacao']; ?>"
													size="8" maxlength="10" OnKeyPress="javascript: formatar(this, '##/##/####');"
													style="margin-bottom: 0px; height: 12px;">
											</li>
											<?php
										} else {
											echo $dados[$i]['d_Prorrogacao'];
										} ?>
									</td>
								</tr>

							</tbody>

							<?php
						} ?>

						<tfoot>
							<tr>
								<th colspan="2" rowspan="4" width="350">
									Crit&eacute;rio:<br>
									<font color="#FFFFFF" face="Arial, Helvetica, sans-serif" size="1">
										Segurado: <?php echo $Nome_Segurado; ?><br>
										Ap&oacute;lice: <?php echo htmlspecialchars($n_Apolice, ENT_QUOTES, 'UTF-8'); ?><br>
										Comprador: <?php echo htmlspecialchars($Comprador, ENT_QUOTES, 'UTF-8'); ?><br>
										Fatura: <?php echo htmlspecialchars($Fatura, ENT_QUOTES, 'UTF-8'); ?><br>
										Situa&ccedil;&atilde;o:
										<?php
										if ($SituacaoDVE == 0) {
											echo 'Todos';
										} else if ($SituacaoDVE == 1) {
											echo 'Vencido';
										} else if ($SituacaoDVE == 2) {
											echo 'A Vencer';
										} else if ($SituacaoDVE == 3) {
											echo 'Pago';
										} ?>
										<br>
									</font>
								</th>

								<th colspan="2" width="120">
									Total Vencido:<br>
									Total Pago:<br>
									Total Pendente:<br>
									Total Geral:
								</th>

								<th colspan="2" style="text-align:right">
									<?php echo number_format($v_Total_Vencido, 2, ',', '.'); ?><br>
									<?php echo number_format($v_Total_Pago, 2, ',', '.'); ?><br>
									<?php echo number_format($v_Total_Pendente, 2, ',', '.'); ?><br>
									<?php echo number_format($v_Total, 2, ',', '.'); ?>
								</th>
							</tr>
						</tfoot>
					</table>

					<div style="clear:both">&nbsp;</div>

					<li class="campo2colunas">
						<?php
						if ($_SESSION['pefil'] == 'B') {
							$id_Parametro = '10070';
						} else {
							$id_Parametro = '10060';
						}

						require_once("../consultaCertificacao.php");

						if ($perm_cert) { ?>
							<button class="botaoagm" type="submit">Gravar</button>
							<?php
						} else { ?>
							<button class="botaoagm js-certificado" type="button">Gravar</button>
							<?php
						} ?>
						<button type="button" class="botaoagg"
							onClick="window.open('<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . 'role/dve/interf/DVEConsultaExcel.php?Id_Inform=' . urlencode($SPR_Id_Inform) . '&Nome_Comprador=' . urlencode($SPR_Nome_Comprador) . '&Num_Fatura=' . urlencode($SPR_Num_Fatura) . '&s_Declaracao=' . urlencode($SPR_s_Declaracao) . '&CRS=' . urlencode($SPR_CRS) . '&Id_User=' . urlencode($userID); ?>', '_blank')">
							Exportar para Excel
						</button>

					</li>

					<div style="clear:both">&nbsp;</div>

					<div
						style="text-align: justify; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #FF0000;">
						* O valor apresentado no campo Limite M&aacute;ximo de Indeniza&ccedil;&atilde;o da Ap&oacute;lice
						(LMI), foi estimado considerando o pr&ecirc;mio de seguro emitido para a
						ap&oacute;lice, sendo certo que para fins de efetivo pagamento de indeniza&ccedil;&atilde;o, a
						Seguradora verificar&aacute; o pr&ecirc;mio efetivamente pago na data em que
						a indeniza&ccedil;&atilde;o for devida, desta forma, se neste momento o valor do LMI for
						insuficiente para pagamento integral de alguma indeniza&ccedil;&atilde;o, as
						parcelas de pr&ecirc;mio &agrave; vencer dever&atilde;o ser quitadas antecipadamente.
					</div>
				</div>
			<?php } ?>

			<div style="clear:both">&nbsp;</div>
		</form>
	</div>

	<script>
		$(document).ready(function () {
			$(".js-certificado").on("click", function () {
				$(".modal-certificado").show();
			});

			$("#close_modal_certificado").on("click", function () {
				$(".modal-certificado").hide();
			});
		});
	</script>

	<!-- Modal Certificado -->
	<div class="modal-certificado" style="display:none">
		<div class="bg-black"></div>

		<div class='modal-int'>
			<h1>Aten&ccedil;&atilde;o</h1>
			<div class="divisoriaamarelo"></div>

			<li class="campo2colunas" style="width: 690px;">
				<label>&nbsp;</label>
				<p>Para a inclus&atilde;o dos dados complementares &agrave; declara&ccedil;&atilde;o de faturamento,
					&eacute; obrigat&oacute;rio a Certifica&ccedil;&atilde;o Digital.</p>
			</li>

			<li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
				<button type="button" class="botaovgm" id="close_modal_certificado">Fechar</button>
				<button type="button" class="botaoagg" onClick="window.location = '../../../auth_cert/index.php';">Usar
					certificado</button>
			</li>

		</div>
	</div>
	<!-- Fim modal -->