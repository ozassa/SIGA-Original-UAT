<?php

$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : '';

$qry = "SELECT name, i_Empresa, n_Apolice 
        FROM Inform 
        WHERE id = ?";
$cur = odbc_prepare($db, $qry);
odbc_execute($cur, [$idInform]);

$Nome_Segurado = odbc_result($cur, "name");
$n_Empresa = odbc_result($cur, "i_Empresa");
$n_Apolice = odbc_result($cur, "n_Apolice");



$sqlCab = "EXEC SPR_BB_Consulta_Financeira '" . $n_Empresa . "', '" . $n_Apolice . "', '10'";
$rsSqlCab = odbc_exec($db, $sqlCab);

while (odbc_fetch_row($rsSqlCab)) {
	$Nome_Segurado = odbc_result($rsSqlCab, "Nome_Segurado");
	$n_Apolice = odbc_result($rsSqlCab, "n_Apolice");
	$d_Emissao = odbc_result($rsSqlCab, "d_Emissao") ? Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Emissao"), 0, 10)) : "";
	$d_Inicio_Vigencia = odbc_result($rsSqlCab, "d_Inicio_Vigencia") ? Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Inicio_Vigencia"), 0, 10)) : "";
	$d_Fim_Vigencia = odbc_result($rsSqlCab, "d_Fim_Vigencia") ? Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Fim_Vigencia"), 0, 10)) : "";
	$d_Consulta = odbc_result($rsSqlCab, "d_Consulta") ? Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Consulta"), 0, 10)) : "";
}



$sql = "EXEC SPR_BB_Consulta_Financeira '" . (int)$n_Empresa . "', '" . (int)$n_Apolice . "', '300'";
//error_log($sql); se der erro foi o (int)
$rsSql = odbc_exec($db, $sql);

$dados = array();
while (odbc_fetch_row($rsSql)) {
	erro_log("entrei no while do sinistro");
	$n_Sinistro = odbc_result($rsSql, "n_Sinistro");
	$s_Sinistro = odbc_result($rsSql, "s_Sinistro");
	$n_CRS = odbc_result($rsSql, "n_CRS");
	$Nome_Comprador = odbc_result($rsSql, "Nome_Comprador");
	$Nome_Pais = odbc_result($rsSql, "Nome_Pais");
	$d_Aviso = odbc_result($rsSql, "d_Aviso") ? Convert_Data_Geral(substr(odbc_result($rsSql, "d_Aviso"), 0, 10)) : "";
	$v_Declarado = odbc_result($rsSql, "v_Declarado") ? number_format(odbc_result($rsSql, "v_Declarado"), 2, ",", ".") : "";
	$v_Reserva = odbc_result($rsSql, "v_Reserva") ? number_format(odbc_result($rsSql, "v_Reserva"), 2, ",", ".") : "";
	$v_Indenizado = odbc_result($rsSql, "v_Indenizado") ? number_format(odbc_result($rsSql, "v_Indenizado"), 2, ",", ".") : "";
	$d_Pagamento = odbc_result($rsSql, "d_Pagamento") ? Convert_Data_Geral(substr(odbc_result($rsSql, "d_Pagamento"), 0, 10)) : "";

	$dados[] = array(
		"n_Sinistro" => $n_Sinistro,
		"s_Sinistro" => $s_Sinistro,
		"n_CRS" => $n_CRS,
		"Nome_Comprador" => $Nome_Comprador,
		"Nome_Pais" => $Nome_Pais,
		"d_Aviso" => $d_Aviso,
		"v_Declarado" => $v_Declarado,
		"v_Reserva" => $v_Reserva,
		"v_Indenizado" => $v_Indenizado,
		"d_Pagamento" => $d_Pagamento
	);
}



require_once("../../../navegacao.php");
?>

<div class="conteudopagina">

	<li class="campo3colunas" style="width: 400px;">
		<label>Nome do Segurado</label>
		<?php echo $Nome_Segurado; ?>
	</li>

	<li class="campo3colunas" style="width: 150px;">
		<label>N&ordm; da Ap&oacute;lice</label>
		<?php echo $n_Apolice; ?>
	</li>

	<?php if (isset($d_Emissao)) { ?>
		<li class="campo3colunas" style="width: 150px;">
			<label>Data de Emiss&atilde;o</label>
			<?php echo $d_Emissao; ?>
		</li>

		<li class="campo3colunas" style="width: 150px;">
			<label>Vig&ecirc;ncia da Ap&oacute;lice</label>
			<?php echo $d_Inicio_Vigencia . ' &agrave; ' . $d_Fim_Vigencia; ?>
		</li>

		<br clear="all">

		<li class="campo2colunas" style="width: 738px;"></li>

		<li class="campo2colunas" style="width: 150px;">
			<label>Data Consulta</label>
			<?php echo $d_Consulta; ?>
		</li>
	<?php } ?>

	<br clear="all">

	<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
		<label>
			<h2>Relat&oacute;rio de Parcelas de Sinistros</h2>
		</label>

		<table summary="" id="">
			<thead>
				<tr>
					<th>N&ordm; do Sinistro</th>
					<th>Situa&ccedil;&atilde;o do Sinistro</th>
					<th>N&ordm; de Cadastro do Importador (CRS)</th>
					<th>Nome do Importador</th>
					<th>Pa&iacute;s do Importador</th>
					<th>Data de Aviso do Sinistro</th>
					<th>Valor Declarado</th>
					<th>Valor Coberto pelo Seguro</th>
					<th>Valor da Indeniza&ccedil;&atilde;o</th>
					<th>Data de Pagamento da Indeniza&ccedil;&atilde;o</th>
				</tr>
			</thead>
			<?php if (empty($dados)) { ?>
				<tbody>
					<tr>
						<td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td>
					</tr>
				</tbody>
			<?php
			} else {
				for ($a = 0; $a < count($dados); $a++) { ?>
					<tr>
						<td><?php echo $dados[$a]['n_Sinistro']; ?></td>
						<td><?php echo $dados[$a]['s_Sinistro']; ?></td>
						<td><?php echo $dados[$a]['n_CRS']; ?></td>
						<td><?php echo $dados[$a]['Nome_Comprador']; ?></td>
						<td><?php echo $dados[$a]['Nome_Pais']; ?></td>
						<td><?php echo $dados[$a]['d_Aviso']; ?></td>
						<td><?php echo $dados[$a]['v_Declarado']; ?></td>
						<td><?php echo $dados[$a]['v_Reserva']; ?></td>
						<td><?php echo $dados[$a]['v_Indenizado']; ?></td>
						<td><?php echo $dados[$a]['d_Pagamento']; ?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>
	</li>

	<div class="barrabotoes">
		<button class="botaovgm" type="button" onClick="window.history.back()">Voltar</button>
	</div>
</div>