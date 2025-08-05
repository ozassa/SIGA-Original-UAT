<?php

if (!isset($_SESSION)) {
	session_start();
}

$EXECUTAR = isset($_REQUEST['EXECUTAR']) ? $_REQUEST['EXECUTAR'] : false;
$nomeSegurado = isset($_REQUEST['nomeSegurado']) ? $_REQUEST['nomeSegurado'] : false;
$Apolice = isset($_REQUEST['Apolice']) ? $_REQUEST['Apolice'] : false;
$Dpp = isset($_REQUEST['Dpp']) ? $_REQUEST['Dpp'] : false;
$MesAno = isset($_REQUEST['MesAno']) ? $_REQUEST['MesAno'] : false;
$stateDVE = isset($_REQUEST['stateDVE']) ? $_REQUEST['stateDVE'] : false;

$compl = '';
$junta = '';

$sql = "select
              SituacaoDVN.i_Item As IDSituacao,
              SituacaoDVN.Descricao_Item As DescSituacao
          From
              Campo_Item SituacaoDVN
          Where
              SituacaoDVN.i_Campo = 600
              And SituacaoDVN.Situacao = 0
          Order By
              SituacaoDVN.i_Item
          ";
$res = odbc_exec($db, $sql);

$dadosPer = array();
while (odbc_fetch_row($res)) {
	array_push($dadosPer, array("IDSituacao" => odbc_result($res, 'IDSituacao'), "DescSituacao" => odbc_result($res, 'DescSituacao')));
}
//odbc_close($db);

if ($EXECUTAR == "1") {
	$sql = "SELECT
                Inf.contrat AS DPP,
                Inf.n_Apolice AS Apolice,
                UPPER(Inf.name) AS Segurado,
                D.num AS Num_DVN,
                CONVERT(VARCHAR, D.inicio, 103) + ' até ' + CONVERT(VARCHAR, ISNULL(DATEADD(DAY, -1, DP.inicio), Inf.endValidity), 103) AS Periodo,
                ISNULL(DD.totalEmbarcado, ISNULL(D.Valor_Periodo, 0)) AS Total_Embarcado,
                Situacao.Descricao_Item AS Situacao,
                Inf.id AS idInform,
                D.id AS idDVE
            FROM
                Inform Inf
            INNER JOIN DVE D ON D.idInform = Inf.id
            LEFT JOIN (
                SELECT DD.idDVE, SUM(DD.totalEmbarcado) AS totalEmbarcado
                FROM DVEDetails DD
                WHERE DD.state = 1
                GROUP BY DD.idDVE
            ) DD ON DD.idDVE = D.id
            LEFT JOIN DVE DP ON DP.idInform = Inf.id AND DP.num = D.num + 1
            LEFT JOIN Campo_Item Situacao ON Situacao.i_Campo = 600 AND Situacao.i_Item = D.state";

	$conditions = [];
	$params = [];

	if ($nomeSegurado != '') {
		$conditions[] = "UPPER(Inf.name) LIKE ?";
		$params[] = '%' . strtoupper($nomeSegurado) . '%';
	}

	if ($Apolice != '') {
		$conditions[] = "Inf.n_Apolice = ?";
		$params[] = $Apolice;
	}

	if ($Dpp != '') {
		$conditions[] = "Inf.contrat = ?";
		$params[] = $Dpp;
	}

	if ($MesAno != '') {
		$mes = explode("/", $MesAno);
		$conditions[] = "MONTH(D.inicio) = ? AND YEAR(D.inicio) = ?";
		$params[] = $mes[0];
		$params[] = $mes[1];
	}

	if ($stateDVE > 0) {
		$conditions[] = "D.state = ?";
		$params[] = $stateDVE;
	}

	if (!empty($conditions)) {
		$sql .= ' WHERE ' . implode(' AND ', $conditions);
	}

	$sql .= " AND Inf.n_Apolice IS NOT NULL
              ORDER BY Inf.contrat, Inf.n_Apolice DESC, D.num";

	$cur = odbc_prepare($db, $sql);
	odbc_execute($cur, $params);

	$i = 0;
	$total = 0;
	$total2 = 0;
}


?>

<SCRIPT language="javascript">

	function consultar() {
		// if (document.getElementById('nomeSegurado').value.length == 0){
		//      verErro('Por favor, informe o Nome do Segurado.');
		// 	 return false;	
		// }
		return true;
	}


	function consultar2(idInform, idDVE) {
		var str = "../dve/dve.php?comm=exibeDve" +
			"&idInform=" + idInform +
			"&idDVE=" + idDVE +
			"&Apolice=" + Form1.Apolice.value +
			"&Dpp=" + Form1.Dpp.value +
			"&nomeSegurado=" + Form1.nomeSegurado.value +
			"&stateDVE=" + Form1.stateDVE.options[Form1.stateDVE.selectedIndex].value
		"";
		window.location = str;
	}

</SCRIPT>



<?php require_once("../../../navegacao.php"); ?>
<div class="conteudopagina">
	<!-- CONTEÚDO PÁGINA - INÍCIO -->
	<form name="Form1" id="Form1" action="<?php echo $root; ?>role/dve/Dve.php" method="post">
		<input type="hidden" name="comm" value="consultadve">
		<input type="hidden" name="EXECUTAR" value="1">
		<ul>
			<li class="campo3colunas">
				<label>Segurado</label>
				<input name="nomeSegurado" id="nomeSegurado" type="text"
					value="<?php echo htmlspecialchars($nomeSegurado, ENT_QUOTES, 'UTF-8'); ?>" />
			</li>

			<li class="campo3colunas">
				<label>Ap&oacute;lice</label>
				<input type="text" name="Apolice" id="Apolice"
					value="<?php echo htmlspecialchars($Apolice, ENT_QUOTES, 'UTF-8'); ?>">
			</li>


			<li class="campo3colunas"><label>DPP</label>
				<input type="text" name="Dpp" id="Dpp"
					value="<?php echo htmlspecialchars($Dpp, ENT_QUOTES, 'UTF-8'); ?>">
			</li>

			<li class="campo3colunas">
				<label>M&ecirc;s/Ano</label>
				<?php $cur_mes = date("m");
				$cur_ano = 2000 + date("y");
				$DATA = fmes($cur_mes) . "/" . fmes($cur_ano);
				?>
				<select name="MesAno" id="MesAno">
					<option value="">Todos</option>
					<?php

					$cur_mes_plus = 1 + date("m");
					if ($cur_mes_plus == 13) {
						$cur_ano = $cur_ano + 1;
						$cur_mes_plus = 01;
					}
					$i = 1;
					while ($i <= 14) {
						?>
						<option value="<?php echo fmes($cur_mes_plus) . "/" . $cur_ano; ?>" <?php echo ((fmes($cur_mes_plus) . "/" . $cur_ano) == $MesAno ? 'selected' : ''); ?>>
							<?php echo fmes($cur_mes_plus) . "/" . $cur_ano; ?>
						</option> <?php
						$cur_mes_plus = $cur_mes_plus - 1;
						if ($cur_mes_plus == 0) {
							$cur_mes_plus = 12;
							$cur_ano = ($cur_ano - 1);
						}
						$i = $i + 1;
					}
					?>
				</select>

			</li>


			<li class="campo3colunas">
				<label>Situa&ccedil;&atilde;o Per&iacute;odo</label>

				<select name="stateDVE" id="stateDVE" style="">
					<option value="">Selecione</option>
					<?php for ($i = 0; $i < count($dadosPer); $i++) { ?>
						<option value="<?php echo htmlspecialchars($dadosPer[$i]['IDSituacao'], ENT_QUOTES, 'UTF-8'); ?>"
							<?php echo ($dadosPer[$i]['IDSituacao'] == $stateDVE ? 'selected' : ''); ?>>
							<?php echo htmlspecialchars($dadosPer[$i]['DescSituacao'], ENT_QUOTES, 'UTF-8'); ?>
						</option>

					<?php } ?>
				</select>

			</li>


			<li class="barrabotoes" style="*margin-left:-15px;">
				<button type="button" class="botaoagm"
					onclick=" if(consultar()) document.Form1.submit(); ">Buscar</button>
			</li>
		</ul>



	</form>


	<table id="example" class="tabela01">
		<thead>
			<tr>
				<th>DPP</th>
				<th>Ap&oacute;lice</th>
				<th>Segurado</th>
				<th>Num DVN</th>
				<th>Per&iacute;odo</th>
				<th style="text-align:right !important;">Total Embarcado</th>
				<th style="text-align:center !important;">Situa&ccedil;&atilde;o</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$total = 0;
			while (odbc_fetch_row($cur)) { ?>
				<tr>
					<td><a href="javascript:"
							onclick="consultar2(<?php echo odbc_result($cur, 'idInform'); ?>,<?php echo odbc_result($cur, 'idDVE'); ?>);"><?php echo odbc_result($cur, 'DPP'); ?></a>
					</td>
					<td><a href="javascript:"
							onclick="consultar2(<?php echo odbc_result($cur, 'idInform'); ?>,<?php echo odbc_result($cur, 'idDVE'); ?>);"><?php echo odbc_result($cur, 'Apolice'); ?></a>
					</td>
					<td><a href="javascript:"
							onclick="consultar2(<?php echo odbc_result($cur, 'idInform'); ?>,<?php echo odbc_result($cur, 'idDVE'); ?>);"><?php echo odbc_result($cur, 'Segurado'); ?></a>
					</td>
					<td><a href="javascript:"
							onclick="consultar2(<?php echo odbc_result($cur, 'idInform'); ?>,<?php echo odbc_result($cur, 'idDVE'); ?>);"><?php echo odbc_result($cur, 'Num_DVN'); ?></a>
					</td>
					<td><a href="javascript:"
							onclick="consultar2(<?php echo odbc_result($cur, 'idInform'); ?>,<?php echo odbc_result($cur, 'idDVE'); ?>);"><?php echo odbc_result($cur, 'Periodo'); ?></a>
					</td>
					<td style="text-align:right !important;">
						<?php echo number_format(odbc_result($cur, 'Total_Embarcado'), 2, ',', '.'); ?>
					</td>
					<td style="text-align:center !important;"><?php echo odbc_result($cur, 'Situacao'); ?></td>
				</tr>
				<?php
				$total += odbc_result($cur, 'Total_Embarcado');
			} ?>
		</tbody>
		<tfoot>
			<th colspan="5" style="text-align:right !important;">Total Embarcado</th>
			<th style="text-align:right !important;"><?php echo number_format($total, 2, ',', '.'); ?></th>
			<th>&nbsp;</th>
		</tfoot>
	</table>

	<div style="clear:both">&nbsp;</div>

	<div class="barrabotoes">
		<button class="botaovgm" type="button" onClick="history.back();">Voltar</button>
		<button class="botaoagg" type="button"
			onclick="window.open('<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/dve/interf/consulta_dve_excel.php?EXECUTAR=<?php echo urlencode($EXECUTAR); ?>&nomeSegurado=<?php echo urlencode($nomeSegurado); ?>&Apolice=<?php echo urlencode($Apolice); ?>&Dpp=<?php echo urlencode($Dpp); ?>&MesAno=<?php echo urlencode($MesAno); ?>&stateDVE=<?php echo urlencode($stateDVE); ?>','_blank');">
			Exportar para Excel
		</button>

	</div>



</div>
<?php

function fmes($cur_mes)
{
	if ($cur_mes < 10) {
		$cur_mes = "0" . $cur_mes;
	} else {
		$cur_mes = "" . $cur_mes;
	}

	return $cur_mes;

}


?>