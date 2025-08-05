<?php
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();

require_once("../../../dbOpen.php");


function formataValorSql($formataValorSql)
{
	$formataValorSql = str_replace('.', '', $formataValorSql);
	$formataValorSql = str_replace(',', '.', $formataValorSql);
	return $formataValorSql;
}


function Convert_Data_Geral($data)
{
	if (strstr($data, "/")) {//verifica se tem a barra /
		$d = explode("/", $data);//tira a barra
		$invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mês etc...
		return $invert_data;
	} elseif (strstr($data, "-")) {
		$d = explode("-", $data);
		$invert_data = "$d[2]/$d[1]/$d[0]";
		return $invert_data;
	}

}

$Operacao = $_REQUEST['operacao'];
$idInform = $_REQUEST['idInform'];
$n_Preriodo = $_REQUEST['n_Preriodo'];
$d_Vigencia_Inicial = Convert_Data_Geral($_REQUEST['d_Vigencia_Inicial']);
$d_Vigencia_Final = Convert_Data_Geral($_REQUEST['d_Vigencia_Final']);
$v_Premio = formataValorSql($_REQUEST['v_Premio']);


if ($Operacao == 'Adicionar' && (!$n_Preriodo)) {
	$sql = "SELECT MAX(n_Preriodo) AS id FROM Periodo_Vigencia WHERE i_Inform = ?";
	$stmt = odbc_prepare($db, $sql);
	odbc_execute($stmt, [$idInform]);
	$rr = $stmt;
	$id = odbc_result($rr, 'id');
	$id += 1;

	$qry = "INSERT INTO Periodo_Vigencia (n_Preriodo, d_Vigencia_Inicial, d_Vigencia_Final, v_Premio, i_Inform) VALUES (?, ?, ?, ?, ?)";
	$stmt = odbc_prepare($db, $qry);
	$resp = odbc_execute($stmt, [$id, $d_Vigencia_Inicial, $d_Vigencia_Final, $v_Premio, $idInform]);

	if ($resp) {
		$msg = 'Gravado com Sucesso';
	} else {
		$msg = 'Erro na gravação';
	}
	odbc_free_result($rr);
} else if ($Operacao == 'Adicionar' && $n_Preriodo > 0) {
	$qry = "UPDATE Periodo_Vigencia SET d_Vigencia_Inicial = ?, d_Vigencia_Final = ?, v_Premio = ? WHERE i_Inform = ? AND n_Preriodo = ?";
	$stmt = odbc_prepare($db, $qry);
	$resp = odbc_execute($stmt, [$d_Vigencia_Inicial, $d_Vigencia_Final, $v_Premio, $idInform, $n_Preriodo]);

	if ($resp) {
		$msg = 'Alterado com Sucesso';
	} else {
		$msg = 'Erro na alteração';
	}
} else if ($Operacao == 'Remover') {
	$qry = "DELETE FROM Periodo_Vigencia WHERE n_Preriodo = ? AND i_Inform = ?";
	$stmt = odbc_prepare($db, $qry);
	$resp = odbc_execute($stmt, [$n_Preriodo, $idInform]);

	if ($resp) {
		$msg = 'Removido com Sucesso';
	} else {
		$msg = 'Erro na Remoção';
	}
}

// Libera a conexão ODBC após o uso
if (isset($stmt)) {
	odbc_free_result($stmt);
}


//   echo '???'.$idInform;
?>

<table width="100%">
	<label>
		<font style="color:#F00; text-align:right"><?php echo $msg; ?></font>
	</label>
	<thead>
		<th scope="col">N&ordm; Seq</th>
		<th scope="col">In&iacute;cio Vig&ecirc;ncia</th>
		<th scope="col">Fim Vig&ecirc;ncia</th>
		<th scope="col">Valor Pr&ecirc;mio</th>
		<th scope="col" colspan="2">Op&ccedil;&otilde;es</th>

	</thead>
	<tbody> <?php
	$sq = "SELECT * FROM Periodo_Vigencia WHERE i_Inform = ? ORDER BY n_Preriodo";
	$resp1 = odbc_prepare($db, $sq);
	odbc_execute($resp1, [$idInform]);
	
	//print $sq;
	$totalVigPremio = 0;
	while (odbc_fetch_row($resp1)) {
		$n_Preriodo = odbc_result($resp1, 'n_Preriodo');
		$d_Vigencia_Inicial = Convert_Data_Geral(substr(odbc_result($resp1, 'd_Vigencia_Inicial'), 0, 10));
		$d_Vigencia_Final = Convert_Data_Geral(substr(odbc_result($resp1, 'd_Vigencia_Final'), 0, 10));
		$v_Premio = odbc_result($resp1, 'v_Premio');

		?>
			<tr id="lastRow_corr">
				<td><?php echo $n_Preriodo; ?></td>
				<td><?php echo $d_Vigencia_Inicial; ?></td>
				<td><?php echo $d_Vigencia_Final; ?></td>
				<td style="text-align: right;"><?php echo ($v_Premio ? number_format($v_Premio, 1, ',', '.') : '0,00'); ?></td>
				<td><a href="#"
						onClick="edita_Form_Vig('<?php echo $n_Preriodo; ?>','<?php echo $d_Vigencia_Inicial; ?>','<?php echo $d_Vigencia_Final; ?>','<?php echo number_format($v_Premio, 2, ',', '.'); ?>');return false;"><img
							src="../../images/icone_editar.png" title="Editar Registro" width="24" height="24"
							class="iconetabela" /></a></td>
				<td><a href="#"
    onClick="javascript: enviaPeriodo_v2('Remover','<?php echo htmlspecialchars($n_Preriodo, ENT_QUOTES, 'UTF-8'); ?>',<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>);return false;">
    <img src="../../images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24"
         class="iconetabela" />
</a>
</td>
			</tr>

			<?php
			$totalVigPremio += $v_Premio;
	} 
	odbc_free_result($resp1);
	?>
		<tr>
			<td colspan="3" style="text-align:right"><strong>Totaliza&ccedil;&atilde;o Pr&ecirc;mio:</strong></td>
			<td colspan="1" style="text-align:right">
				<strong><?php echo number_format($totalVigPremio, 2, ',', '.'); ?></strong></td>
			<td colspan="2">&nbsp;</td>
		</tr>

	</tbody>
</table>
<input type="hidden" name="TotalVigPremio" id="TotalVigPremio" value="<?php echo $totalVigPremio; ?>">