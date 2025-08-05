<?php
// DESENVOLVIDO WILSON - 18/01/2012 - COM BASE view.php

require_once("funcsDve.php");

if (!$importerName)
	$importerName = $_REQUEST['importerName'];
if (!$dve_action)
	$dve_action = $_REQUEST['dve_action'];
if (!$idBuyer)
	$idBuyer = $_REQUEST['idBuyer'];
if (!$newdve)
	$newdve = $_REQUEST['newdve'];
if (!$client)
	$client = $_REQUEST['client'];
if (!$idInform)
	$idInform = $_REQUEST['idInform'];
if (!$idNotification)
	$idNotification = $_REQUEST['idNotification'];
if (!$idDVE)
	$idDVE = $_REQUEST['idDVE'];
if (!$idDetail)
	$idDetail = $_REQUEST['idDetail'];
if (!$viewflag)
	$viewflag = $_REQUEST['viewflag'];

$link = $root . "role/dve/gerapdf.php?idInform=$idInform&idDVE=$idDVE";

if (!$primeira_tela)
	$primeira_tela = $_REQUEST['primeira_tela'];
if (!$status) {
	$status = $_REQUEST['status'];
}
if (!$pode_mudar) {
	$pode_mudar = $_REQUEST['pode_mudar'];
}
if (!$criei) {
	$criei = $_REQUEST['criei'];
}
if (!$pode_imprimir) {
	$pode_imprimir = $_REQUEST['pode_imprimir'];
}

?>
<script language="javascript">

	function checaTotalEmbarcado(obj, valor) {
		checkDecimals(obj, valor);
	}

	function ShowObj(id, obj) {

		var i = 0;
		for (i == 0; i < document.getElementsByName(obj).length; i++) {
			document.getElementsByName(obj)[i].style.display = "none";
		}

		if (document.getElementsByName(obj)[id].style.display == "table") {
			document.getElementsByName(obj)[id].style.display = "none";
		} else {
			document.getElementsByName(obj)[id].style.display = "table";
		}
	}

	function proc(opc) {
		document.Form2.action = 'dve.php?comm=exibeDveDet&idInform=<?php echo urlencode($idInform); ?>&idDVE=<?php echo urlencode($idDVE); ?>&novo=1&modalidade=' + encodeURIComponent(opc);
		document.Form2.submit();
	}



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
		w = window.open('<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>&noval=' + encodeURIComponent(noval),
			'pdf_windowoficial',
			'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1'
		);
		w.moveTo(5, 5);
		w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
	}


	<?php require_once("../../scripts/javafunc.js"); ?>
</script>
<script language=javascript src="<?php echo $root; ?>scripts/utils.js"></script>
<script language=javascript src="<?php echo $root; ?>scripts/calendario.js"></script>
<?php

if ($executa == 1) {
	$msg = "";
	$total2 = str_replace(".", "", $total2);
	$total2 = str_replace(",", ".", $total2);

	$sql = "UPDATE DVE SET total2 = ? WHERE id = ?";
	$cur = odbc_prepare($db, $sql);
	odbc_execute($cur, [$total2, $idDVE]);


	if ($cur) {
		$msg = "Total Valor Embarcado alterado com sucesso.";
	}

	odbc_free_result($cur);
}

$sql = "
    SELECT 
        i.name, i.i_Seg, i.nProp, i.startValidity, i.endValidity, 
        isnull(i.statePa, 1) statePa, d.num, d.inicio, d.state stateDVE, 
        d.total2, i.currency, IsNull(DateAdd(D, -1, dp.inicio), i.endValidity) as fim
    FROM Inform i
    INNER JOIN DVE d ON d.idInform = i.id
    LEFT JOIN DVE dp ON dp.idInform = i.id AND dp.num = d.num + 1
    WHERE
        i.id = ? 
        AND d.id = ?
";

$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$idInform, $idDVE]);


$name = odbc_result($cur, "name");
$startValidity = odbc_result($cur, "startValidity");
$endValidity = odbc_result($cur, "endValidity");
$i_Seg = odbc_result($cur, "i_Seg");
$nProp = odbc_result($cur, "nProp");
$num = odbc_result($cur, "num");
$inicio = odbc_result($cur, "inicio");
$fim = odbc_result($cur, "fim");
$stateDVE = odbc_result($cur, "stateDVE");
$total2 = odbc_result($cur, "total2");
$statePa = odbc_result($cur, "statePa");
$moeda = odbc_result($cur, "currency");

odbc_free_result($cur);

if ($moeda == "2") {
	$ext = "US$";
} else if ($moeda == "6") {
	$ext = "€";
}

$apolice = numApolice($idInform, $db, $dbSisSeg);
require_once("../../../navegacao.php");
?>
<div class="conteudopagina">
	<ul>
		<li class="campo3colunas">
			<label>Segurado</label>
			<?php echo $name; ?>
		</li>

		<li class="campo3colunas">
			<label>Vigência</label>
			<?php echo substr($startValidity, 8, 2) . "/" . substr($startValidity, 5, 2) . "/" . substr($startValidity, 0, 4) . " a " . substr($endValidity, 8, 2) . "/" . substr($endValidity, 5, 2) . "/" . substr($endValidity, 0, 4); ?>
		</li>

		<li class="campo3colunas">
			<label>Apólice</label>
			<?php echo $apolice; ?>
		</li>

		<li class="campo3colunas">
			<label>Num. DVN</label><?php echo $num; ?>ª
		</li>

		<li class="campo3colunas">
			<label>Período</label><?php echo substr($inicio, 8, 2) . "/" . substr($inicio, 5, 2) . "/" . substr($inicio, 0, 4) . " a " . substr($fim, 8, 2) . "/" . substr($fim, 5, 2) . "/" . substr($fim, 0, 4); ?>
		</li>
	</ul>

	<div style="clear:both">&nbsp;</div>
	<ul>
		<li class="campo2colunas"><label>Total das vendas com pagamento antecipado e/ou carta de crédito
				confirmada</label></li>
	</ul>

	<br clear="all" />

	<ul>
		<!-- <li class="campo3colunas"><label>Total Valor Embarcado</label>
						<input type="text" readonly="readonly" name="total2" onBlur="checaTotalEmbarcado(this, this.value);" value="<?php echo number_format($total2, 2, ',', '.'); ?>" size="20" style="text-align:right">
				</li> -->

		<?php
		if ($msg) { ?>
			<label style="color:#F00"><?php echo $msg; ?></label>
			<?php
		} ?>
		<!--
				<li class="campo3colunas">
					<label>Modalidade</label>
					<select onChange="javascript:ShowObj(selectedIndex,'tab');">
							<option  SELECTED>Cobrança à vista ou à prazo até 180 dias</option>
							<option>Via Coligada</option>
					</select>
				</li>
				-->
	</ul>

	<div style="clear:both">&nbsp;</div>

	<div id="tab" style="display:table; width:930px;">
		<table name="tab" style="display:table; width:930px;" classe="tabela01">
			<thead>
				<tr>
					<th>País</th>
					<th>Comprador</th>
					<th>Fatura</th>
					<th align="center">Data Embarque</th>
					<th align="center">Vencimento</th>
					<th align="center">Embarcado<br>(<?php echo $ext; ?>)</th>
				</tr>
			</thead>
			<?php
			$sql = "
			SELECT 
				c.name pais, 
				im.name importador, 
				dt.embDate, 
				dt.vencDate, 
				dt.totalEmbarcado, 
				dt.proex, 
				dt.ace, 
				dt.id idDetail,
				dt.fatura
			FROM DVEDetails dt
			INNER JOIN Country c ON dt.idCountry = c.id
			INNER JOIN Importer im ON dt.idImporter = im.id
			WHERE dt.idDVE = ? 
			AND modalidade = 1 
			AND dt.state = 1
		";

			$params = [$idDVE];

			if ($Fat != "") {
				$sql .= " AND dt.fatura = ?";
				$params[] = $Fat;
			}

			$cur = odbc_prepare($db, $sql);
			odbc_execute($cur, $params);


			$i = 0;
			$tot = 0;
			?>
			<tbody>
				<?php
				while (odbc_fetch_row($cur)) {
					$pais = odbc_result($cur, "pais");
					$importador = odbc_result($cur, "importador");
					$embDate = odbc_result($cur, "embDate");
					$vencDate = odbc_result($cur, "vencDate");
					$totalEmbarcado = odbc_result($cur, "totalEmbarcado");
					$proex = odbc_result($cur, "proex");
					$ace = odbc_result($cur, "ace");
					$idDetail = odbc_result($cur, "idDetail");
					$Fatura = odbc_result($cur, "fatura");

					$tot = $tot + $totalEmbarcado;

					$i++;
					?>
					<tr <?php echo $i % 2 ? "" : ' class="odd"'; ?>>
						<td><?php echo $pais; ?></td>
						<td><?php echo $importador; ?></td>
						<td><?php echo $Fatura; ?></td>
						<td align="center">
							<?php echo substr($embDate, 8, 2) . "/" . substr($embDate, 5, 2) . "/" . substr($embDate, 0, 4); ?>
						</td>
						<td align="center">
							&nbsp;<?php echo substr($vencDate, 8, 2) . "/" . substr($vencDate, 5, 2) . "/" . substr($vencDate, 0, 4); ?>
						</td>
						<td align="right">&nbsp;<?php echo number_format($totalEmbarcado, 2, ',', '.'); ?></td>
					</tr>
					<?php
				}
				odbc_free_result($cur);
				if ($i == 0) { ?>
					<tr>
						<td colspan="5" align="center">Nenhum embarque encontrado</td>
					</tr>
					<?php
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th align="center"></th>
					<th align="right">Total:</th>
					<th align="right">&nbsp;<?php echo number_format($tot, 2, ',', '.'); ?></th>
				</tr>
			</tfoot>
		</table>

		<br clear="all" />

		<?php
		if ($pode_mudar || $criei || $pode_imprimir) { ?>
			<form id="Form2" name="Form2" action="<?php echo $root; ?>role/dve/Dve.php#tabela" style="height:40px;">
				<input type="hidden" name="comm" value="">
				<input type="hidden" name="idInform"
					value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="idNotification"
					value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="client" value="<?php echo htmlspecialchars($client, ENT_QUOTES, 'UTF-8'); ?>">

				<?php
				/*
																									   if(!($role["bancoBB"] || ($role["bancoOB"]) || ($role["bancoParc"]))){    ?>    
																										   <?php 
																										   if($primeira_tela){ ?>
																											   <input type="hidden" name="viewflag" value="1">
																											   <button class="botaoagm" type="button"  onClick="this.form.comm.value='view';this.form.submit()">Visualizar</button>
																											   <?php      
																										   } 
																									   } 
																									   */
				if ($pode_imprimir) { ?>
					<button class="botaoagm" type="button"
						onClick="javascript: opendve(<?php echo $status == 2 ? 0 : 1; ?>)">Imprimir</button>
					<?php
				}
				/*
																									if($status == 2  && !$role['dve'] && $pode_mudar){   //Echo "aqui";  	?>
																										<button class="botaoagm" type="button"  onClick="document.formulario.submit();">Incluir</button>
																										<?php
																									}
																									
																									if($pode_enviar){
																										echo "&nbsp;";   ?>
																										<button class="botaoagg" type="button"  onClick="javascript:enviaForm(this, <?php echo $statePa;?>, 'send');">Concluir envio de DVN's</button>
																										<?php // Fim alterado Hicom
																									}
																									*/
				?>
				<button class="botaovgm" type="button"
					onClick="window.location = '<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . 'role/searchClient/ListClient.php?comm=view&idInform=' . urlencode($idInform); ?>';">
					Voltar
				</button>

			</form>
			<?php
		}
		?>
	</div>
	<div id="tab2" style="display:none; width:930px;">
		<table name="tab" style="display:none; width:930px;" classe="tabela01">
			<thead>
				<tr>
					<th>País</th>
					<th>Importador</th>
					<th align="center">Embarque</th>
					<th align="center">Vencimento</th>
					<th align="center">Embarcado<br>(US$)</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sql = "
				SELECT 
					c.name pais, 
					im.name importador, 
					dt.embDate, 
					dt.vencDate, 
					dt.totalEmbarcado, 
					dt.proex, 
					dt.ace, 
					dt.id idDetail 
				FROM DVEDetails dt
				INNER JOIN Country c ON dt.idCountry = c.id
				INNER JOIN Importer im ON dt.idImporter = im.id
				WHERE dt.idDVE = ? 
				AND modalidade = 2 
				AND dt.state = 1
			";

				$cur = odbc_prepare($db, $sql);
				odbc_execute($cur, [$idDVE]);

				$i = 0;
				$tot = 0;

				while (odbc_fetch_row($cur)) {
					$pais = odbc_result($cur, "pais");
					$importador = odbc_result($cur, "importador");
					$embDate = odbc_result($cur, "embDate");
					$vencDate = odbc_result($cur, "vencDate");
					$totalEmbarcado = odbc_result($cur, "totalEmbarcado");
					$proex = odbc_result($cur, "proex");
					$ace = odbc_result($cur, "ace");
					$idDetail = odbc_result($cur, "idDetail");

					$tot = $tot + $totalEmbarcado;

					$i++;

					?>
					<tr <?php echo $i % 2 ? "" : ' class="odd"'; ?>>
						<td><?php echo $pais; ?></td>
						<td><?php echo $importador; ?></td>
						<td align="center">
							<?php echo substr($embDate, 8, 2) . "/" . substr($embDate, 5, 2) . "/" . substr($embDate, 0, 4); ?>
						</td>
						<td align="center">
							&nbsp;<?php echo substr($vencDate, 8, 2) . "/" . substr($vencDate, 5, 2) . "/" . substr($vencDate, 0, 4); ?>
						</td>
						<td align="right">&nbsp;<?php echo number_format($totalEmbarcado, 2, ',', '.'); ?></td>
					</tr>
					<?php
				}
				odbc_free_result($cur);
				if ($i == 0) { ?>
					<tr>
						<td colspan="5" align="center">Nenhum embarque encontrado</td>
					</tr>
					<?php
				} ?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th align="center"></th>
					<th align="right">Total:</th>
					<th align="right">&nbsp;<?php echo number_format($tot, 2, ',', '.'); ?></th>
				</tr>
			</tfoot>
		</table>

		<br clear="all" />
		<button type="button" class="botaoagm" onClick="javascript:proc(2);">Novo</button>&nbsp;&nbsp;
		<button type="button" class="botaoagm" onClick="javascript:voltar();">Voltar</button>
	</div>
</div>