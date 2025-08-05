<?php

$role['dve'] = isset($role['dve']) ? $role['dve'] : false;

$idInform = isset($idInform) ? (int)preg_replace('/\D/', '', $idInform) : 0;
$idDVE = isset($idDVE) ? (int)preg_replace('/\D/', '', $idDVE) : 0;
$client = isset($client) ? (int)preg_replace('/\D/', '', $client) : 0;
$primeira_tela = isset($primeira_tela) ? (int)preg_replace('/\D/', '', $primeira_tela) : 0;


function pode_enviar($role)
{ // só pode enviar no final do periodo
	global $idDVE, $db, $msg, $idInform, $pode_mudar, $userID;

	if (check_menu(['dve'], $role)) {
		return false;
	}

	$strSQL = "Select * from Inform Where id = '$idInform'";

	$rs = odbc_exec($db, $strSQL);

	if (odbc_fetch_row($rs)) {
		$tipoDve = odbc_result($rs, "tipoDve");
	} else {
		$msg = "Erro: Identificador segurado não existente";
		return false;
	}

	$strSQL = "SELECT D.num AS num, D.inicio As Inicio_Periodo, IsNull(DateAdd(D, -1, DP.inicio), Inf.endValidity) AS Fim_Periodo, DateAdd(D, IsNull(Inf.Prazo_Entrega_DVN, 15), IsNull(DateAdd(D, -1, DP.inicio), Inf.endValidity)) As Limite_Periodo, D.state, D.periodo As Periodo, D.LiberaAtraso As LiberaAtraso, D.LiberaVencida As LiberaVencida FROM Inform Inf Inner Join DVE D On D.idInform = Inf.id Left Join DVE DP On DP.idInform = Inf.id And DP.num = D.num + 1 WHERE D.id=$idDVE";
	$r = odbc_exec($db, $strSQL);

	if (odbc_fetch_row($r)) {
		$num = odbc_result($r, "num");
		$inicio = ymd2dmy(odbc_result($r, "Inicio_Periodo"));
		$fim = ymd2dmy(odbc_result($r, "Fim_Periodo"));
		$Data_Limite_Periodo = ymd2dmy(odbc_result($r, "Limite_Periodo"));
		$periodo = odbc_result($r, "Periodo");
		$state = odbc_result($r, "state");

		if ($state == 3) {
			return true;
		}

		//If ($tipoDve == 3) { //Tipo DVE Anual
		//	$time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio), 12, 1));
		//}elseif ($tipoDve == 2) { //Tipo DVE Trimestral
		//	$time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio), 3, 1));
		//}else{ // Tipo DVE Mensal
		//	$time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio), 1, 1));
		//}

		//If (strtotime(Date('d/m/Y')) > strtotime($Data_Limite_Periodo)){
		$x = odbc_exec($db, "select min(num) from DVE where idInform=$idInform and state=1"); // a mais antiga q nao foi enviada
		$min = odbc_result($x, 1);

		if ($num == $min) {
			if ($state == 1) {
				return true;
			}
		}
		//}
	} else {
		$msg = "Erro: DVE inexistente: $idDVE";
	}

	return false;
}

function dve_header($total2, $total3)
{
	global $newdve, $root, $table_size, $newdve, $client, $idInform, $idDVE, $userID, $db, $status;

	$sql = "select currency from Inform where id = $idInform";
	$cur = odbc_exec($db, $sql);

	$moeda = odbc_result($cur, 1);

	if ($moeda == "1") {
		$extMoeda = "Reais";
	} else if ($moeda == "2") {
		$extMoeda = "Dólares Norte Americanos";
	} else if ($moeda == "6") {
		$extMoeda = "Euros";
	}

	$qry = "select 
		Inf.name,
		Inf.n_Apolice,
		Inf.startValidity,
		Inf.endValidity,
		D.num,
		D.inicio,  
		IsNull(DateAdd(D, -1, D2.inicio), Inf.endValidity),
		Cast(Day(D.inicio) as varchar) + '/' + Cast(Month(D.inicio) as varchar) + '/' + Cast(Year(D.inicio) as varchar) + ' à '
			+ Cast(Day(IsNull(DateAdd(D, -1, D2.inicio), Inf.endValidity)) as varchar) + '/' + Cast(Month(IsNull(DateAdd(D, -1, D2.inicio), Inf.endValidity)) as varchar) + '/' + Cast(Year(IsNull(DateAdd(D, -1, D2.inicio), Inf.endValidity)) as varchar)
			+ ' (' +  Cast(D.num as varchar) + 'ª DVN)' as Periodo_Declaracao,
		D.sentDate as Data_Envio_DVN, 
		IsNull((Select Sum(DD.totalEmbarcado) From DVEDetails DD Where DD.idDVE = D.id And DD.modalidade = 1 And DD.state <> 3), 0) as Venda_a_Vista,
		IsNull((Select Sum(DD.totalEmbarcado) From DVEDetails DD Where DD.idDVE = D.id And DD.modalidade = 2 And DD.state <> 3), 0) as Venda_Coligada,
		IsNull((Select Sum(DD.totalEmbarcado) From DVEDetails DD Where DD.idDVE = D.id And DD.modalidade = 3 And DD.state <> 3), 0) as Venda_Antecipado,
		D.total2 as total2 
		From 
			Inform Inf 
		Inner Join DVE D On
			D.idInform = Inf.id
		Left Join DVE D2 On
			D2.idInform = Inf.id
			And D2.num = D.num + 1
		INNER JOIN DVEDetails det On 
			det.idDVE = D.id
		Where 
			Inf.id = " . $idInform . "
			And D.id = " . $idDVE . "		
			AND det.state <> 3
		";

	$cur1 = odbc_exec($db, $qry);

	?>

	<?php
	$idNotification = isset($idNotification) ? $idNotification : 0;
	$pode_remover = isset($pode_remover) ? $pode_remover : false;
	?>

	<form action="<?php echo $root; ?>" ."role/dve/Dve.php" method="post" name="dve_form" style="height:auto !important;">
		<input type="hidden" name="importerName">
		<input type="hidden" name="comm" value="view">
		<input type="hidden" name="newdve" value="<?php echo $newdve; ?>">
		<input type="hidden" name="client" value="<?php echo $client; ?>">
		<input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
		<input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
		<input type="hidden" name="idDVE" value="<?php echo $idDVE; ?>">
		<input type="hidden" name="idDetail">
		<?php

		if (!$client) {
			echo '<input type="hidden" name="dve_action">';
		}

		$Venda_Antecipado = odbc_result($cur1, 'Venda_Antecipado') <> '0' ? odbc_result($cur1, 'Venda_Antecipado') : odbc_result($cur1, 'total2');
		?>

		<div style="clear:both">&nbsp;</div>
		<label>Total Vendido no per&iacute;odo (Em <?php echo ($extMoeda); ?>)</label>

		<table class="tabela01" style="width:945px;">
			<thead>
				<th>&Agrave; Vista e/ou Cobran&ccedil;a a Prazo</th>
				<th>Antecipado e/ou Carta de Cr&eacute;dito</th>
				<th>Vendas via Coligadas</th>
				<th>Valor Total</th>
			</thead>

			<tbody>
				<tr>
					<td style="text-align:right">
						<?php echo number_format(odbc_result($cur1, 'Venda_a_Vista'), 2, ',', '.'); ?>
					</td>
					<td style="text-align:right">
						<?php echo number_format($Venda_Antecipado ?? 0, 2, ',', '.'); ?>
					</td>
					<td style="text-align:right">
						<?php echo number_format(odbc_result($cur1, 'Venda_Coligada'), 2, ',', '.'); ?>
					</td>
					<td style="text-align:right">
						<?php $total = (odbc_result($cur1, 'Venda_a_Vista') + $Venda_Antecipado + odbc_result($cur1, 'Venda_Coligada')); ?>
						<?php echo ($total ? number_format($total, 2, ',', '.') : '0,00'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<div style="clear:both">&nbsp;</div>

		<table class="tabela01" style="width:945px;">
			<?php
			if ($pode_remover) {
				echo "<td rowspan=3>&nbsp;</td>";
			}

			echo "</tr>";
} // dve_header


function dve_footer()
{
	echo "</table>\n";
	echo "</form>";
}

function viewdve_header($show)
{
	global $table_size, $pode_remover;
	if ($show) { ?>
				<thead>
					<tr>
						<th rowspan="2" style="text-align:center;  font-size: 10pt;"><strong>Nº SBCE</strong></th>
						<th width="20%" rowspan="2"><strong>Comprador</strong></th>
						<th width="15%" rowspan="2"><strong>Pa&iacute;s</strong></th>
						<th rowspan="2" style="text-align:center;  font-size: 10pt;"><strong>Data Embarque</strong></th>
						<th rowspan="2" style="text-align:center;  font-size: 10pt;"><strong>Nº da Fatura</strong></th>
						<th rowspan="2" style="text-align:center;  font-size: 10pt;"><strong>Data Vencimento</strong></th>
						<th rowspan="2" style="text-align:center;  font-size: 10pt;"><strong>Valor Embarcado</strong></th>

						<th colspan="2" rowspan="1" style=" padding: 0">
							<table width="100%" style=" padding:0">
								<thead>
									<tr>
										<th style="text-align:center; font-size: 10pt;"><strong>Valor Financiado</strong></th>
									</tr>
								</thead>
							</table>
						</th>
					</tr>
					<tr>
						<th colspan="2" rowspan="1" style="font-size: 10pt; padding: 0">
							<table style="padding:0" cellspacing="0">
								<thead>
									<tr style="padding:0">
										<th width="50%" style="text-align:center;  font-size: 10pt;"><strong>PROEX</strong></th>
										<th width="50%" style="text-align:center; font-size: 10pt; height"><strong>ACE</strong>
										</th>
									<tr>
								</thead>
							</table>
						</th>
					</tr>
					<thead>
					<tbody>

					<?php
	}
}

function viewdve_body($titulo, $cur, $mod)
{
	global $table_size, $client, $idInform, $idDVE, $total_embarcado, $idNotification, $total_proex, $total_ace, $newdve, $total_1, $db, $pode_remover, $status, $userID, $pode_mudar;

	echo '<input type="hidden" name="idNotification" value="' . $idNotification . '">';
	$i = 1;

	$qry = "select 
              Imp.id,
			  DD.id as idDetail,
			  C.code + Right('000000' + Imp.c_Coface_Imp, 6) As c_Coface_Imp,
			  Imp.name as 'Comprador',
			  C.name as 'Pais',
			  CONVERT(VARCHAR, DD.embDate, 103) AS embDate,
			  DD.fatura,
			  CONVERT(VARCHAR, DD.vencDate, 103) AS vencDate,
			  DD.totalEmbarcado,
			  DD.proex,
			  DD.ace,
			  Case DD.modalidade
					When 1 Then 'À Vista e/ou Cobrança a Prazo'
					When 2 Then 'Vendas via Coligadas'
					When 3 Then 'Antecipado e/ou Carta de Crédito'
			  End as 'TipoEmbarque'
		From 
			  Inform Inf 
		Inner Join DVE D On
			  D.idInform = Inf.id
		Left Join DVE D2 On
			  D2.idInform = Inf.id
			  And D2.num = D.num + 1
		Left Join DVEDetails DD On
			  DD.idDVE = D.id
		Left Join Importer Imp On
			  Imp.id = DD.idImporter
		Inner Join Country C On
			  C.id = Imp.idCountry
		Where 
			  Inf.id = " . $idInform . " 
			  And D.id = " . $idDVE . " 
			  And DD.state <> 3
		order By
			  DD.modalidade,
			  Imp.name,
			  DD.embDate ";

	//print $qry ;

	$cur = odbc_exec($db, $qry);

	while (odbc_fetch_row($cur)) {
		$idBuyer = odbc_result($cur, 'id');

		$total_embarcado_imp = 0;
		$total_proex_imp = 0.0;

		if ($i % 2 == 0) {
			$class = 'class="odd"';
		} else {
			$class = "";
		}
		?>
						<tr <?php echo $class; ?>>

							<td style="text-align:center"><a
									href="<?php echo $root; ?>Dve.php?comm=editImporter&idInform=<?php echo $idInform; ?>&idDVE=<?php echo $idDVE; ?>&client=<?php echo $client; ?>&modalidade=<?php echo $mod; ?>&idDetail=<?php echo odbc_result($cur, 'idDetail'); ?>&idNotification=<?php echo $idNotification; ?>#tabela"><?php echo odbc_result($cur, 'c_Coface_Imp'); ?></a>
							</td>
							<td style="text-align:left"><a
									href="<?php echo $root; ?>Dve.php?comm=editImporter&idInform=<?php echo $idInform; ?>&idDVE=<?php echo $idDVE; ?>&client=<?php echo $client; ?>&modalidade=<?php echo $mod; ?>&idDetail=<?php echo odbc_result($cur, 'idDetail'); ?>&idNotification=<?php echo $idNotification; ?>#tabela"><?php echo odbc_result($cur, 'Comprador'); ?></a>
							</td>
							<td style="text-align:left"><a
									href="<?php echo $root; ?>Dve.php?comm=editImporter&idInform=<?php echo $idInform; ?>&idDVE=<?php echo $idDVE; ?>&client=<?php echo $client; ?>&modalidade=<?php echo $mod; ?>&idDetail=<?php echo odbc_result($cur, 'idDetail'); ?>&idNotification=<?php echo $idNotification; ?>#tabela"><?php echo odbc_result($cur, 'Pais'); ?></a>
							</td>
							<td style="text-align:center"><a
									href="<?php echo $root; ?>Dve.php?comm=editImporter&idInform=<?php echo $idInform; ?>&idDVE=<?php echo $idDVE; ?>&client=<?php echo $client; ?>&modalidade=<?php echo $mod; ?>&idDetail=<?php echo odbc_result($cur, 'idDetail'); ?>&idNotification=<?php echo $idNotification; ?>#tabela"><?php echo odbc_result($cur, 'embDate'); ?></a>
							</td>
							<td style="text-align:center"><a
									href="<?php echo $root; ?>Dve.php?comm=editImporter&idInform=<?php echo $idInform; ?>&idDVE=<?php echo $idDVE; ?>&client=<?php echo $client; ?>&modalidade=<?php echo $mod; ?>&idDetail=<?php echo odbc_result($cur, 'idDetail'); ?>&idNotification=<?php echo $idNotification; ?>#tabela"><?php echo odbc_result($cur, 'fatura'); ?></a>
							</td>
							<td style="text-align:center"><a
									href="<?php echo $root; ?>Dve.php?comm=editImporter&idInform=<?php echo $idInform; ?>&idDVE=<?php echo $idDVE; ?>&client=<?php echo $client; ?>&modalidade=<?php echo $mod; ?>&idDetail=<?php echo odbc_result($cur, 'idDetail'); ?>&idNotification=<?php echo $idNotification; ?>#tabela"><?php echo odbc_result($cur, 'vencDate'); ?></a>
							</td>
							<td style="text-align:right">
								<?php echo number_format(odbc_result($cur, 'totalEmbarcado'), 2, ',', '.'); ?></td>
							<td style="text-align:right"> <?php echo number_format(odbc_result($cur, 'proex'), 2, ',', '.'); ?>
							</td>
							<td style="text-align:right"> <?php echo number_format(odbc_result($cur, 'ace'), 2, ',', '.'); ?>
							</td>
							<!--<td style="text-align:left"> <?php echo odbc_result($cur, 'TipoEmbarque'); ?></td>-->
							<?php


							$i++;
							if ($pode_remover) { ?>

							<?php } else {

							}


							$hc_i = $hc_i + 1;

							echo '</tr>';


							$total_1 += odbc_result($cur, 'totalEmbarcado');
							$total_proex += odbc_result($cur, 'proex');
							$total_ace += odbc_result($cur, 'ace');

	} // while

	return $hc_i;
} // viewdve_body

function viewdve_footer($totaltext, $showtext, $tot)
{
	global $total_1, $total_proex, $total_ace, $userID, $pode_remover;

	if (!$hc_i) {
		//echo '<tr><td colspan="8">Nenhum Registro encontrado</td></tr>';
	}

	echo '</tbody>
            <tfoot>
		    <tr>
				<th colspan=6 align=left>' . $totaltext . '</th>
				<th style="text-align:right">' . number_format($total_1, 2, ',', '.') . '</th>
				<th style="text-align:right">' . number_format($total_proex, 2, ',', '.') . '</th>
				<th style="text-align:right">' . number_format($total_ace, 2, ',', '.') . '</th>
             </tr>
		  </tfoot>
		  </table>';
	if ($pode_remover) {
		//echo "<th>&nbsp;</th>";
	}
	if ($showtext) {
		?>
							<div style="clear:both">&nbsp;</div>
							<div style="clear:both">&nbsp;</div>
							<div style="width:945px;">
								<label>Declaramos que as informações constantes neste documento são completas e verdadeiras e
									assumimos, sob as penas contratuais e legais, a responsabilidade por sua exatidão.</label>

							</div>
							<?php
							if (!$role['dve']) { ?>
								<div style="width:945px;">
									<label>De acordo com o Preâmbulo e com o Módulo Faturamento F3.05 das Condições Gerais da
										Apólice de Seguro de Crédito à Exportação, a presente Declaração de Volume de
										Exportações deve abranger a totalidade das exportações realizadas pelo Segurado dentro do
										prazo máximo de crédito da apólice.</label>
								</div>
								<?php

							}
	}
}

$pode_mudar = 0;

if ($dve_action == 'removeImporter') {
	// remove um importador
	$query = "DELETE from DVEDetails where id=$idDetail";

	if (!odbc_exec($db, $query)) {
		$msg = "Erro ao remover a declaração";
	} else {
		$msg = "Declaração removida";
	}
}

$cur = odbc_exec($db, "select i_Seg, startValidity, endValidity, name, idAnt, prodUnit, tipoDve, n_Apolice, IsNull(Prazo_Entrega_DVN, 15) As Prazo_Entrega_DVN from Inform where id=$idInform");

if (odbc_fetch_row($cur)) {
	$idSeg = odbc_result($cur, 1);
	$start = ymd2dmy(odbc_result($cur, 2));
	$end = ymd2dmy(odbc_result($cur, 3));
	$name = odbc_result($cur, 4);
	$nome_segurado = odbc_result($cur, 4);
	$idAnt = odbc_result($cur, 5);
	$prod = odbc_result($cur, 6);
	$tipoDve = odbc_result($cur, "tipoDve");
	$apolice = odbc_result($cur, "n_Apolice");
	$Prazo_Entrega_DVN = odbc_result($cur, "Prazo_Entrega_DVN");
}

//******************

if ($numDVE) {
	$z = odbc_exec($db, "select * from DVE where idInform=$idInform and num=$numDVE");

	if (!odbc_fetch_row($z)) { // se a DVE nao existe, cria
		if ($numDVE == 1) {
			$inicio = dmy2ymd($start);
		} else {
			$inicio = dmy2ymd(getEndDate(ymd2dmy(odbc_result($cur, 2)), $numDVE - 1, 1));
		}

		$r = odbc_exec($db, "insert into DVE (idInform, state, inicio, num, periodo) values " .
			"($idInform, 1, '$inicio', $numDVE, 15)");

		if (!$r) {
			$msg = "Erro ao criar DVE";
		}

		$criei = 1;
	}
}

$numDVE = isset($_REQUEST['numDVE']) ? $_REQUEST['numDVE'] : $numDVE;

if ($numDVE != '') {
	$query = "SELECT id FROM DVE WHERE idInform = ? AND num = ?";
	$stmt = odbc_prepare($db, $query);
	odbc_execute($stmt, [$idInform, $numDVE]);
	$idDVE = odbc_result($stmt, 1);
}


//echo "select id from DVE where idInform=$idInform and num=$numDVE";

$table_size = 711;

// $x = odbc_exec($db, "select state, inicio, periodo, sentDate from DVE where id=$idDVE");
$x = odbc_exec($db, "SELECT D.num AS num, D.sentDate As sentDate, D.inicio As Inicio_Periodo, IsNull(DateAdd(D, -1, DP.inicio), Inf.endValidity) AS Fim_Periodo, DateAdd(D, IsNull(Inf.Prazo_Entrega_DVN, 15), IsNull(DateAdd(D, -1, DP.inicio), Inf.endValidity)) As Limite_Periodo, D.state, D.periodo As Periodo, D.LiberaAtraso As LiberaAtraso, D.LiberaVencida As LiberaVencida FROM Inform Inf Inner Join DVE D On D.idInform = Inf.id Left Join DVE DP On DP.idInform = Inf.id And DP.num = D.num + 1 WHERE D.id=$idDVE");

if (odbc_fetch_row($x)) {
	$status = odbc_result($x, "state");
	$inicio = odbc_result($x, "Inicio_Periodo");
	$periodo = odbc_result($x, "Periodo");
	$sentDate = ymd2dmy(odbc_result($x, "sentDate"));
	$Data_Limite_Periodo = ymd2dmy(odbc_result($x, "Limite_Periodo"));

	if ($status == 2) {
		$primeira_tela = 0;

		if ($tipoDve == 2) { //Tipo DVE Trimestral
			$time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio), 3, 1));
		} else { // Tipo DVE Mensal
			$time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio), 1, 1));
		}

		$fim_15 = $time_fim + (16 * 24 * 3600);

		if (time() <= $fim_15) { // se estamos até o 15o. dia apos o termino do periodo, pode alterar
			$pode_mudar = 1;
		}

	} else if ($status == 1 || $status == 3) {
		$pode_mudar = 1;
	}
}

if (isset($role['dve'])) {
	if ($role['dve']) {
		$pode_mudar = 0;
		//$pode_remover = 1;
	}
}

$pode_imprimir = 1;

$cur = odbc_exec($db, "select total2, total3, inicio, periodo, num, sentDate from DVE where id=$idDVE");

if (odbc_fetch_row($cur)) {

	$total2 = round("0" . odbc_result($cur, 1), 2);
	$total3 = round("0" . odbc_result($cur, 2), 2);

	//echo $total2;
	//die();


	$inicio = ymd2dmy(odbc_result($cur, 3));

	if ($tipoDve == 2) { //Tipo DVE Trimestral
		$fim = getEndDate($inicio, 3);
	} else { // Tipo DVE Mensal
		$fim = getEndDate($inicio, 1);
	}

	//$fim = getEndDate($inicio, odbc_result($cur, 4));
	$num = odbc_result($cur, 5);
	$sentDate = ymd2dmy(odbc_result($cur, 6));
}

if (!($status == 1 || $role['dve'])) {
	$newdve = 0;
}

// $cur = odbc_exec($db,
// 		 "select count(distinct i.id)
//                   from DVE d join DVEDetails dt on d.id=dt.idDVE join Importer i on
//                   dt.idImporter=i.id join Country c on i.idCountry=c.id
//                   where d.id=$idDVE and dt.state=1 and dt.modalidade=1");

// $avista = odbc_result($cur, 1);


// $sql  = "select distinct i.id, i.name, i.c_Coface_Imp, c.name
//                   from DVE d join DVEDetails dt on d.id=dt.idDVE join Importer i on
//                   dt.idImporter=i.id join Country c on i.idCountry=c.id
//                   where d.id=$idDVE and dt.state=1 and dt.modalidade=1 order by i.name";

// $cur = odbc_exec($db,$sql);

// $cur2 = odbc_exec($db,
// 		  "select count(distinct i.id)
//                    from DVE d join DVEDetails dt on d.id=dt.idDVE join Importer i on
//                    dt.idImporter=i.id join Country c on i.idCountry=c.id
//                    where d.id=$idDVE and dt.state=1 and dt.modalidade=2");
// $coligadas = odbc_result($cur2, 1);

// $cur2 = odbc_exec($db,
// 		  "select distinct i.id, i.name, i.c_Coface_Imp, c.name
//                    from DVE d join DVEDetails dt on d.id=dt.idDVE join Importer i on
//                    dt.idImporter=i.id join Country c on i.idCountry=c.id
//                    where d.id=$idDVE and dt.state=1 and dt.modalidade=2 order by i.name");

// simbolos ordinais -> "ª" e "º" (nao apague!!)

?>