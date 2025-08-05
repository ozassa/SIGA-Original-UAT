<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
	<?php
	if (!isset($importerName)) {
		$importerName = $_REQUEST['importerName'];
		$dve_action = $_REQUEST['dve_action'];
		$idBuyer = $_REQUEST['idBuyer'];
		$newdve = $_REQUEST['newdve'];
		$client = $_REQUEST['client'];
		$idInform = $_REQUEST['idInform'];
		$idNotification = $_REQUEST['idNotification'];
		$idDVE = $_REQUEST['idDVE'];
		$idDetail = $_REQUEST['idDetail'];
		$viewflag = $_REQUEST['viewflag'];
	}

	$link = $root . "role/dve/gerapdf.php?idInform=" . urlencode($idInform) . "&idDVE=" . urlencode($idDVE);
	$linkexcel = $root . "role/dve/geraexcel.php?idInform=" . urlencode($idInform) . "&idDVE=" . urlencode($idDVE);


	if (!isset($primeira_tela)) {
		$primeira_tela = $_REQUEST['primeira_tela'];
	}
	if (!isset($status)) {
		$status = $_REQUEST['status'];
	}
	if (!isset($pode_mudar)) {
		$pode_mudar = $_REQUEST['pode_mudar'];
	}
	if (!isset($criei)) {
		$criei = isset($_REQUEST['criei']) ? $_REQUEST['criei'] : null;
	}
	if (!isset($pode_imprimir)) {
		$pode_imprimir = $_REQUEST['pode_imprimir'];
	}


	?>

	<script language="javascript">
		function enviaForm(obj, statePa, comm) {
			if (statePa > 2) {
				verErro('N&atilde;o &eacute; poss&iacute;vel incluir nova Declara&ccedil;&atilde;o de Volume Exporta&ccedil;&atilde;o. Parcela de Ajuste j&aacute; foi calculada.');
			} else {
				if (comm == "send") {
					//verErro('DVE será enviada');
				}

				obj.form.comm.value = comm;
				obj.form.submit()
			}
		}

		function opendve(noval) {
			w = window.open('<?php echo $link; ?>&noval=' + encodeURIComponent(noval), 'pdf_windowoficial', 'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1');
			w.moveTo(5, 5);
			w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
		}

		function opendveexcel(noval) {
    w = window.open('<?php echo $linkexcel; ?>&noval=' + encodeURIComponent(noval), 'excel_windowoficial', 'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1');
    w.moveTo(5, 5);
    w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
}
	</script>

	<script language=javascript src="<?php echo $root; ?>scripts/utils.js"></script>
	<script language=javascript src="<?php echo $root; ?>scripts/calendario.js"></script>

	<?php

	$pode_enviar = pode_enviar($role);

	$qry = "SELECT 
            Inf.name,
            Inf.n_Apolice,
            Inf.startValidity,
            Inf.endValidity, 
            D.num,
            D.inicio AS Inicio,
            ISNULL(DATEADD(D, -1, D2.inicio), Inf.endValidity) AS Fim,
            CONVERT(VARCHAR, D.inicio, 103) + ' à ' + CONVERT(VARCHAR, ISNULL(DATEADD(D, -1, D2.inicio), Inf.endValidity), 103)
                + ' (' + CAST(D.num AS VARCHAR) + 'ª DVN)' AS 'Período de declaração',
            D.sentDate AS 'DataEnvioDVN',
            CASE DD.modalidade
                WHEN 1 THEN 'À Vista e/ou Cobrança a Prazo'
                WHEN 2 THEN 'Vendas via Coligadas'
                WHEN 3 THEN 'Antecipado e/ou Carta de Crédito'
            END AS 'TipoEmbarque',
            SUM(DD.totalEmbarcado) AS TotalEmbarcado
        FROM 
            Inform Inf 
        INNER JOIN DVE D ON
            D.idInform = Inf.id
        LEFT JOIN DVE D2 ON
            D2.idInform = Inf.id
            AND D2.num = D.num + 1
        LEFT JOIN DVEDetails DD ON
            DD.idDVE = D.id
        LEFT JOIN Importer Imp ON
            Imp.id = DD.idImporter
        INNER JOIN Country C ON
            C.id = Imp.idCountry
        WHERE 
            Inf.id = ? 
            AND D.id = ? 
            AND DD.state <> 3
        GROUP BY
            Inf.name,
            Inf.n_Apolice,
            Inf.startValidity,
            Inf.endValidity,
            D.id,
            D.num,
            D.inicio,
            D2.inicio,
            D.sentDate,
            DD.modalidade";

				$sql = "SELECT ISNULL(statePa, 1) AS statePa 
        FROM Inform 
        WHERE id = ?";
	$rs = odbc_prepare($db, $sql);
	odbc_execute($rs, [$idInform]);

	$statePa = odbc_result($rs, "statePa");

	odbc_free_result($rs);

	$res = odbc_prepare($db, $qry);
	odbc_execute($res, [$idInform, $idDVE]);


	$name = odbc_result($res, 'name');
	?>

	<li class="campo3colunas">
		<label>Segurado:</label>
		<?php echo $nome_segurado; ?>
	</li>

	<li class="campo3colunas">
		<label>Ap&oacute;lice n&deg;:</label>
		<?php echo $apolice; ?>
	</li>

	<li class="campo3colunas">
		<label>Vig&ecirc;ncia:</label>
		<?php echo $start; ?> &aacute; <?php echo $end; ?>
	</li>

	<div style="clear:both">&nbsp;</div>

	<li class="campo3colunas">
		<label>Per&iacute;odo de Declara&ccedil;&atilde;o:</label>
		<?php echo $inicio; ?> &agrave; <?php echo ($fim . ' (' . $num . 'ª DVN)'); ?>
	</li>

	<li class="campo3colunas">
		<label>Data Limite para Declara&ccedil;&atilde;o: </label>
		<?php echo $Data_Limite_Periodo; ?>
	</li>

	<?php
	if (!check_menu(['client'], $role)) { ?>
		<li class="campo2colunas">
			<label>Data envio Dvn: </label><?php echo $sentDate; ?>
		</li>
		<?php
	}
	?>

	<?php

	// Alterado Hicom - 09/10/2004 (Gustavo) - encontra o status da Pa para bloquear ou não as DVEs
	

	// fim alterado Hicom
	
	$hc_i = 0;
	$total_vista = 0;

	$primeira_tela = 1;

	if ($primeira_tela) {
		//print 'oi';
	
		$total2 = odbc_result($res, 'TotalEmbarcado');

		dve_header($total2, $total3);

		$total_1 = $total_embarcado = $total_proex = $total_ace = 0;

		$ja_mostrou = 0;


		// Verificar esta consulta direito 
		// $qry = "select * from Importer where idInform = ".$idInform;
		$qry = "SELECT 
            Imp.c_Coface_Imp,
            Imp.name AS 'Comprador',
            C.name AS 'Pais',
            DD.embDate,
            DD.fatura,
            DD.vencDate,
            DD.totalEmbarcado,
            DD.proex,
            DD.ace,
            CASE DD.modalidade
                WHEN 1 THEN 'À Vista e/ou Cobrança a Prazo'
                WHEN 2 THEN 'Vendas via Coligadas'
                WHEN 3 THEN 'Antecipado e/ou Carta de Crédito'
            END AS 'TipoEmbarque',
            D.state
        FROM 
            Inform Inf
        INNER JOIN DVE D ON
            D.idInform = Inf.id
        LEFT JOIN DVE D2 ON
            D2.idInform = Inf.id
            AND D2.num = D.num + 1
        LEFT JOIN DVEDetails DD ON
            DD.idDVE = D.id AND DD.state <> 3
        LEFT JOIN Importer Imp ON
            Imp.id = DD.idImporter
        LEFT JOIN Country C ON
            C.id = Imp.idCountry
        WHERE 
            Inf.id = ? AND
            D.id = ?
        ORDER BY
            DD.modalidade,
            Imp.name,
            DD.embDate";

		$cur = odbc_prepare($db, $qry);
		odbc_execute($cur, [$idInform, $idDVE]);

		$state = odbc_result($cur, "state");
		//		error_log($state);
	
		$avista = isset($avista) ? $avista : 0;
		$coligadas = isset($coligadas) ? $coligadas : 0;


		if ($avista > 0) {
			viewdve_header(1);
			$hc_i = viewdve_body("Operações à Vista e/ou Cobrança a Prazo", $cur, 1);

			// Alterado Hicom (Gustavo) - chamada para a função com argumentos incompletos (ViewDve.php)
			// antes: viewdve_footer("Total à Vista e/ou Cobrança a Prazo", 0);
			viewdve_footer("", "Total à Vista e/ou Cobrança a Prazo", 0);
			// Fim Alterado Hicom
	
			$total_vista = $total_1;
			$ja_mostrou = 1;
		}
		if ($coligadas > 0) {
			$old_total = $total_1;
			$total_1 = $total_embarcado = $total_proex = $total_ace = 0;
			viewdve_header(!$ja_mostrou);
			viewdve_body("Operações via Coligada", $cur2, 2);
			viewdve_footer("Total via Coligada", 1);
			$total_1 += $old_total;
		}
		dve_footer();
	}
	odbc_free_result($res);
	odbc_free_result($cur);

	// Checa mensagens 
	if ($msg) {
		//tratamento criado por Wagner 5/9/2008
		if ($msg = "Erro: DVN inexistente:") {
			$msg = "DVN inexistente:";
		}

		?>
		<div style="clear:both">&nbsp;</div>
		<li class="campo2colunas"><label><?php echo $msg; ?></label>
		</li>
		<?php
	}


	if ($primeira_tela) { ?>
		<div style="clear:both">&nbsp;</div>
		<form id="Form1" action="<?php echo $root; ?>role/dve/Dve.php#tabela">
			<input type="hidden" name="comm" value="">
			<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="idNotification"
				value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="client" value="<?php echo htmlspecialchars($client, ENT_QUOTES, 'UTF-8'); ?>">

			<input type="hidden" name="fieldfocus" value="">
			<input type="hidden" name="formfocus" value="">

			<?php if ($state == 1 && !(check_menu(["bancoBB", "bancoOB", "bancoParc"], $role))) { ?>
				<li class="campo2colunas">
					<label>Escolha a Modalidade de Venda:</label>
					<select class="caixa" name="modalidade">
						<option value="1">&Agrave; vista, cobran&ccedil;a a prazo</option>
						<option value="2">Via coligada</option>
						<option value="3">Antecipado e/ou Carta de Cr&eacute;dito</option>
					</select>
				</li>
				<?php
				if (!$statePa)
					$statePa = 1;
				?>
				<label>&nbsp;</label>
				<button class="botaoagm" type="button"
					onClick="javascript:enviaForm(this, <?php echo $statePa; ?>, 'modalidade');">OK</button>

			<?php } ?>

			<?php if ($state == 1 && (check_menu(["regionalManager"], $role))) { ?>
				<li class="campo2colunas">
					<label>Escolha a Modalidade de Venda:</label>
					<select class="caixa" name="modalidade">
						<option value="1">&Agrave; vista, cobran&ccedil;a a prazo</option>
						<option value="2">Via coligada</option>
						<option value="3">Antecipado e/ou Carta de Cr&eacute;dito</option>
					</select>
				</li>
				<?php
				if (!$statePa)
					$statePa = 1;
				?>
				<label>&nbsp;</label>
				<button class="botaoagm" type="button"
					onClick="javascript:enviaForm(this, <?php echo $statePa; ?>, 'modalidade');">OK</button>

			<?php } ?>

		</form>
		<?php

		// Preparar a consulta SQL para a primeira execução
		$query1 = "SELECT D.num AS Num, DD.MinDVE AS MinDVE 
FROM Inform Inf
INNER JOIN DVE D ON D.idInform = Inf.id
LEFT JOIN (SELECT idInform, MIN(num) AS MinDVE FROM DVE WHERE state = 1 GROUP BY idInform) DD ON DD.idInform = Inf.id
WHERE D.id = ?";
		$stmt1 = odbc_prepare($db, $query1);
		odbc_execute($stmt1, [$idDVE]);

		// Obter os resultados
		$min = null;
		$num = null;
		if (odbc_fetch_row($stmt1)) {
			$min = odbc_result($stmt1, "MinDVE");
			$num = odbc_result($stmt1, "Num");
		}

		odbc_free_result($stmt1);
		// Verificar se pode enviar
		$pode_enviar = ($num == $min);

		// Preparar a consulta SQL para a segunda execução
		$query2 = "SELECT * FROM DVEDetails WHERE idDVE = ? AND state = 1";
		$stmt2 = odbc_prepare($db, $query2);
		odbc_execute($stmt2, [$idDVE]);

		// Verificar se há resultados e exibir mensagem
		if (!odbc_fetch_row($stmt2) && $pode_enviar) {
			echo "<p align=center><font size=2 color=#FF0000>Caso não haja nenhum embarque no período, clique em \"CONCLUIR ENVIO DE DVN\"</font>";
		}
		odbc_free_result($stmt2);

	}



	if ($status == 2 && !$role['dve'] && $pode_mudar) {
		echo "<p align=center><font size=2 color=#4444ff>Esta declaração só poderá ser alterada até o dia 15 do corrente mês</font>";
	} else if ($status == 3 && !$role['dve']) {
		echo "<p align=center><font size=2 color=#4444ff>Após alterada, esta DVE foi desconsiderada do sistema. Favor reenviá-la</font>";
	}
	?>
	<div style="clear:both">&nbsp;</div>

	<?php
	if ($client && !$viewflag) { ?>
		<form action="<?php echo $root; ?>role/client/Client.php" method="post" style="height:40px;">
			<?php
	} else if ($idNotification) { ?>
				<form action="<?php echo $root; ?>role/notification/BoxInput.php" method="post" style="height:40px;">
				<?php
	} else if ($viewflag) { ?>
						<form action="<?php echo $root; ?>role/dve/Dve.php#tabela" method="post" style="height:40px;">
							<input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
							<input type="hidden" name="client"
								value="<?php echo htmlspecialchars($client, ENT_QUOTES, 'UTF-8'); ?>">
							<input type="hidden" name="primeira_tela" value="1">
					<?php
	} else { ?>
							<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post" style="height:40px;">
						<?php
	}
	?>

					<input type="hidden" name="comm" value="open">
					<input type="hidden" name="idInform"
						value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">

					<?php if ($idNotification) { ?>
						<input type="hidden" name="idNotification"
							value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
						<button class="botaoagm" type="button"
							onClick="this.form.action='<?php echo $root; ?>role/dve/Dve.php';this.form.comm.value='done';this.form.submit()">Revisada</button>
					<?php } ?>

				</form>

				<?php
				if ($pode_mudar || $criei || $pode_imprimir) { ?>
					<form id="Form2" name="Form2" action="<?php echo $root; ?>role/dve/Dve.php#tabela" style="height:40px;">
						<input type="hidden" name="comm" value="">
						<input type="hidden" name="idInform"
							value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="idDVE"
							value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="idNotification"
							value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="client"
							value="<?php echo htmlspecialchars($client, ENT_QUOTES, 'UTF-8'); ?>">

						<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
							<?php
							if ($_SESSION['pefil'] == 'C') { ?>
								<button class="botaovgm" type="button"
									onClick="window.location = '<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . 'role/client/Client.php?comm=open&idInform=' . urlencode($idInform); ?>';">
									Voltar
								</button>
								<?php
							} else { ?>
								<button class="botaovgm" type="button"
									onClick="window.location = '<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . 'role/searchClient/ListClient.php?comm=view&idInform=' . urlencode($idInform); ?>';">
									Voltar
								</button>
								<?php
							}
							?>


							<?php if (!(check_menu(["bancoBB"], $role) || check_menu(["bancoOB"], $role) || check_menu(["bancoParc"], $role))) { ?>
								<?php if ($primeira_tela) { ?>
									<input type="hidden" name="viewflag" value="1">
									<!-- <button class="botaoagm" type="button"  onClick="this.form.comm.value='view';this.form.submit()">Visualizar</button> -->
									<?php
								}
							}  // primeira_tela
						
							if ($pode_imprimir) { ?>
								<button class="botaoagm" type="button"
									onClick="javascript: opendve(<?php echo $status == 2 ? 0 : 1; ?>)">Imprimir</button>

									<button class="botaoagm" type="button"
									onClick="javascript: opendveexcel(<?php echo $status == 2 ? 0 : 1; ?>)">excel</button>
								<?php
							}

							if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'F') {
								$sql = "SELECT D.state FROM DVE D WHERE D.id = ?";

								$rsSql = odbc_prepare($db, $sql);
								odbc_execute($rsSql, [$idDVE]);
								if (odbc_result($rsSql, "state") == 2) { ?>
									<button type="button" class="botaoagg"
										onClick="window.location = '<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . 'role/dve/excelDeclaracaoDVE.php?Id_Periodo=' . urlencode($idDVE); ?>';">
										Gerar Arquivo Cofanet
									</button>
									<?php
								}

								odbc_free_result($rsSql);

							}

							if ($status == 2 && !$role['dve'] && $pode_mudar) { ?>
								<button class="botaoagm" type="button" onClick="document.formulario.submit();">Incluir</button>
								<?php
							}
							?>

							<?php
							if ($pode_enviar) {

								$id_Parametro = '10050';
								require_once("../consultaCertificacao.php"); ?>

								<button class="botaoagg <?php echo $perm_cert ? 'js-concluir_dve' : 'js-certificado'; ?>"
									type="button">Concluir envio de DVN's</button>

								<?php
							}
							?>
						</li>

					</form>
					<?php
				}
				?>

				<?php
				if ($idInform == "3930" || $idInform == "4669" || $idInform == "5565") { //Verificação para aparecer o botão arquivo de dve só para Mangels
					$sql = "SELECT * FROM DVEDetails WHERE idDVE = ? AND state = 1";

					$yy = odbc_prepare($db, $sql);
					odbc_execute($yy, [$idDVE]);

					if (!odbc_fetch_row($yy)) {
						odbc_free_result($yy);
						$sql = "SELECT * FROM DVE WHERE id = ?";

						$rs = odbc_prepare($db, $sql);
						odbc_execute($rs, [$idDVE]);

						if (odbc_result($rs, "state") == '1') {
							odbc_free_result($rs);
							?>
							<form action="<?php echo $root; ?>role/dve/Dve.php#tabela" method="post" name="formimp"
								style="height:40px;">
								<input type="hidden" name="comm" value="impdve">
								<input type="hidden" name="idInform"
									value="<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
								<input type="hidden" name="idDVE"
									value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
								<input type="hidden" name="idNotification"
									value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
								<input type="hidden" name="client"
									value="<?php echo htmlspecialchars($client, ENT_QUOTES, 'UTF-8'); ?>">
								<div class="barrabotoes">
									<button class="botaoagg" type="button" onclick="this.form.submit();">Importar DVE's de um
										arquivo</button>

									<div class="barrabotoes">
							</form>
							<?php
						}
					}
				}
				?>

				<form action="<?php echo $root; ?>role/dve/Dve.php#tabela" method="post" name="formulario">
					<input type="hidden" name="comm" value="modalidade">
					<input type="hidden" name="idInform"
						value="<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="idDVE"
						value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="client"
						value="<?php echo htmlspecialchars($client, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="idNotification"
						value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
					<!--<input type="hidden" name="modalidade" value="<?php echo $modalidade; ?>">-->
					<input type="hidden" name="modalidad" e value="1">
					<!--<input type="hidden" name="idDetail" value="<?php echo htmlspecialchars($idDetail, ENT_QUOTES, 'UTF-8'); ?>">-->
					<input type="hidden" name="idDetail"
						value="<?php echo htmlspecialchars($idDetail, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="dataEmb" value="">
					<input type="hidden" name="dataVenc" value="">
					<input type="hidden" name="viewflag" value="1">
					<input type="hidden" name="primeira_tela" value="">
					<input type="hidden" name="registro" value="<?php echo $hc_i + 1; ?>">
				</form>

				<script type="text/javascript">
					// todo esse codigo é pra arrumar os campos numéricos do cabeçalho colorido da parte de cima (deu pra entender?)
					var f = document.dve_form;

					<?php
					if (isset($total_vista)) {
						if (!$primeira_tela) { // alterado Hicom (Gustavo) - f.total2 e 3 só existe se !primeira_tela ?>
							//f.total1.value = <?php echo str_replace(".", ",", $total_vista); ?>;
							f.total1.value = '<?php echo number_format($total_vista, 2, ',', '.'); ?>';
							<?php
						} // fim alterado
					}
					?>

					//verErro('<?php echo str_replace(".", ",", $total2); ?>');
					//verErro('<?php echo number_format($total2, 2, ',', '.'); ?>');

					<?php
					if (!$primeira_tela) { // alterado Hicom (Gustavo) - f.total2 e 3 só existe se !primeira_tela ?>
						f.total2.value = '<?php echo number_format($total2, 2, ',', '.'); ?>';
						f.total3.value = '<?php echo number_format($total3, 2, ',', '.'); ?>';
						//f.total.value = Number(f.total1.value) + Number(f.total2.value) + Number(f.total3.value);
						f.total.value = '<?php echo number_format($total_vista + $total2 + $total3, 2, ',', '.'); ?>';

						formatDecimals(f.total1, f.total1.value);
						formatDecimals(f.total2, f.total2.value);
						formatDecimals(f.total3, f.total3.value);
						//formatDecimals(f.total, f.total.value);

						f.total1.disabled = true;
						f.total2.disabled = true;
						f.total3.disabled = true;
						f.total.disabled = true;
						<?php
					} // fim alterado   
					?>

					<?php
					if ($role['dve']) {
						if (!$primeira_tela) { // alterado Hicom (Gustavo) - f.total2 e 3 só existe se !primeira_tela ?>
							f.total2.disabled = false;
							f.total3.disabled = false;
							<?php
						}
					}
					?>
				</script>

				<div style="clear:both">&nbsp;</div>

</div>

<script>
	$(document).ready(function () {
		$(".js-certificado").on("click", function () {
			$(".modal-certificado").show();
		});

		$(".js-concluir_dve").on("click", function () {
			$(".modal-concluir-dve").show();
		});

		$("#close_modal_certificado").on("click", function () {
			$(".modal-certificado").hide();
		});

		$("#close_modal_concluir_dve").on("click", function () {
			$(".modal-concluir-dve").hide();
		});
	});

	function enviaForm2(statePa, comm) {
		if (statePa > 2) {
			verErro('N&atilde;o &eacute; poss&iacute;vel incluir nova Declara&ccedil;&atilde;o de Volume Exporta&ccedil;&atilde;o. Parcela de Ajuste j&aacute; foi calculada.');
		} else {
			Form2.comm.value = comm;
			Form2.submit();
		}
	}
</script>

<!-- Modal Certificado -->
<div class="modal-certificado" style="display:none">
	<div class="bg-black"></div>

	<div class='modal-int'>
		<h1>Aten&ccedil;&atilde;o</h1>
		<div class="divisoriaamarelo"></div>

		<li class="campo2colunas" style="width: 690px;">
			<label>&nbsp;</label>
			<p>Para concluir o envio de DVN's, &eacute; obrigat&oacute;rio a Certifica&ccedil;&atilde;o Digital.</p>
		</li>

		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
			<button type="button" class="botaovgm" id="close_modal_certificado">Fechar</button>
			<button type="button" class="botaoagg" onClick="window.location = '../../../auth_cert/index.php';">Usar
				certificado</button>
		</li>

	</div>
</div>
<!-- Fim modal -->

<!-- Modal Recusar -->
<div class="modal-concluir-dve" style="display:none">
	<div class="bg-black"></div>

	<div class='modal-int'>
		<h1>Aten&ccedil;&atilde;o</h1>
		<div class="divisoriaamarelo"></div>

		<li class="campo2colunas" style="width: 690px;">
			<label>&nbsp;</label>
			<p>Ao concluir o per&iacute;odo n&atilde;o ser&aacute; mais poss&iacute;vel incluir novos faturamentos. Tem
				certeza que deseja conclu&iacute;-lo?</p>
		</li>

		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
			<button type="button" class="botaovgm" id="close_modal_concluir_dve">N&atilde;o</button>
			<button class="botaoagm" type="button"
				onClick="javascript:enviaForm2('<?php echo $statePa; ?>', 'send');">Sim</button>
		</li>

	</div>
</div>
<!-- Fim modal -->