<?php //Criado Hicom 19/10/2004 (Gustavo)

require_once("funcsDve.php");

$mes = $_POST['mes'];
$ano = $_POST['ano'];

function mkdate($a, $m, $d)
{
	return date("Y-m-d", mktime(0, 0, 0, $m, $d, $a));
}


if (!$_GET['executa']) {
	$mes = date('m');
	$ano = date('Y');
	$dInicio = mkdate($ano, $mes, 1);
	$dFim = mkdate($ano, $mes + 1, 0);
}
$anoAtu = date('Y');
$mesAtu = date('m');

if ($mes && $mes <> 0) {
	$dInicio = mkdate($ano, $mes, 1);
	$dFim = mkdate($ano, $mes + 1, 0);
}

/*
$mesAtu = date ('m');
if (!$mes || $mes == 0)
	$mes = $mesAtu;

$anoAtu = date('Y');
if (!$ano || $ano == 0)
	$ano = $anoAtu;
	
$dInicio = mkdate ($ano, $mes, 1);
$dFim = mkdate ($ano, $mes + 1, 0);
*/
?>
<SCRIPT language="javascript">

	function alteraData(opc) {
		if (opc == 'mes') {
			if (consulta.mes.options[consulta.mes.selectedIndex].value == 0) {
				consulta.ano.selectedIndex = 0;
			}
			else if (consulta.ano.options[consulta.ano.selectedIndex].value == 0) {
				consulta.ano.selectedIndex = 1;

			}
		}
		else {
			if (consulta.ano.options[consulta.ano.selectedIndex].value == 0) {
				consulta.mes.selectedIndex = 0;
			}
			else if (consulta.mes.options[consulta.mes.selectedIndex].value == 0) {
				consulta.mes.selectedIndex = <?php echo $mesAtu; ?>;
			}
		}
	}

</SCRIPT>

<?php require_once("../../../navegacao.php"); ?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<form name="consulta" action="../dve/dve.php?comm=consultaPa&executa=1" method="post">
	<div class="conteudopagina">
		<li class="campo2colunas">Cliente
			<input type="text" name="cliente" value="<?php echo $cliente; ?>">
		</li>
		<li class="campo2colunas">
			<label>Fim da Vig&ecirc;ncia</label>
			<select name="mes" class="caixa" onchange="javascript:alteraData('mes');" style="width:47%;">
				<option value="0" <?php echo 0 == $mes ? ' SELECTED' : ''; ?>>Todos</option>
				<?php for ($i = 1; $i <= 12; $i++) {
					?>
					<option value="<?php echo $i; ?>" <?php echo $i == $mes ? ' SELECTED' : ''; ?>><?php echo $i; ?></option>
				<?php }
				?>
			</select>
			<select name="ano" class="caixa" onchange="javascript:alteraData('ano');" style="width:47%;">
				<option value="0" <?php echo $ano == 0 ? ' SELECTED' : ''; ?>>Todos</option>
				<option value="<?php echo $anoAtu; ?>" <?php echo $ano == $anoAtu ? ' SELECTED' : ''; ?>>
					<?php echo $anoAtu; ?>
				</option>
				<option value="<?php echo ($anoAtu - 1); ?>" <?php echo $ano == ($anoAtu - 1) ? ' SELECTED' : ''; ?>>
					<?php echo $anoAtu - 1; ?>
				</option>
			</select>
		</li>

		<li class="campo2colunas">
			<label>DVEs</label>
			<select name="opcDVE">
				<option value=0 <?php echo $opcDVE == 0 ? ' SELECTED' : ''; ?>>Quaisquer</option>
				<option value=1 <?php echo $opcDVE == 1 ? ' SELECTED' : ''; ?>>Completas</option>
				<option value=2 <?php echo $opcDVE == 2 ? ' SELECTED' : ''; ?>>Incompletas</option>
			</select>
		</li>
		<li class="campo2colunas"><label>Situa&ccedil;&atilde;o da PA</label>
			<select name="status">
				<option value=0 <?php echo $status == 0 ? ' SELECTED' : ''; ?>>Todas</option>
				<option value=1 <?php echo $status == 1 ? ' SELECTED' : ''; ?>>N&atilde;o calculada</option>
				<option value=2 <?php echo $status == 2 ? ' SELECTED' : ''; ?>>Calculada</option>
				<option value=3 <?php echo $status == 3 ? ' SELECTED' : ''; ?>>Financeiro</option>
				<option value=4 <?php echo $status == 4 ? ' SELECTED' : ''; ?>>Emitida</option>
			</select>
		</li>
		<li class="campo2colunas"><label>Exibir</label>
			<select name="opcPA">
				<option value=0 <?php echo $opcPA == 0 ? ' SELECTED' : ''; ?>>Todos</option>
				<option value=1 <?php echo $opcPA == 1 ? ' SELECTED' : ''; ?>>Com PA</option>
				<option value=2 <?php echo $opcPA == 2 ? ' SELECTED' : ''; ?>>Sem PA</option>
			</select>
		</li>

		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
			<button class="botaoagm" onclick="javascript: consulta.submit();">Pesquisar</button>
		</li>
	</div>
</form>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
	<table summary="Submitted table designs">

		<thead>
			<tr>
				<th scope="col">Ap&oacute;lice</th>
				<th scope="col">Exportador</th>

				<th scope="col">Vig&ecirc;ncia</th>
				<th scope="col">DVEs</th>
				<th scope="col">PA (U$)</th>
				<th scope="col">Sit.</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($_GET['executa'] == 1) {
				$meses = " AND (endValidity >= '$dInicio' AND endValidity <= '$dFim')";
				if ($mes == 0)
					$meses = "";
				?>

				<?php

				/*$cliente = $_POST['cliente'];
										$status = $_POST['status'];
										$opcDVE = $_POST['opcDVE'];
										$opcPA = $_POST['opcPA'];
										$sql =
											"SELECT isnull(sum(p.valor),0) valor, " .
											"		q1.*, " .
											"		isnull(q2.dvesEntregues,0) dvesEntregues " .
											"FROM	( " .
											"		SELECT	count(d.id) dves, " .
											"				i.id, " .
											"				i.name, " .
											"				i.startValidity, " .
											"				i.endValidity, " .
											"				isnull(i.statePa,1) as sit " .
											"		FROM 	Inform i join DVE d on i.id = d.idInform " .
											"		WHERE 	(i.state = 10 or i.state = 11) " . $meses;
										if ($status <> 0) {
											$sql = $sql .
												"				AND isnull(i.statePa,1) = " . $status . " ";
										}
										if ($cliente)
											$sql = $sql .
												"				AND i.name LIKE '%" . strtoupper($cliente) . "%' ";
										$sql = $sql .
											"		GROUP BY i.id, i.name, i.startValidity, i.endValidity, statePa) q1" .
											"		LEFT JOIN PADet p on q1.id = p.idInform " .
											"		LEFT JOIN ( " .
											"			SELECT 	count(d2.id) dvesEntregues, " .
											"					i2.id " .
											"			FROM 	Inform i2 join DVE d2 on i2.id = d2.idInform " .
											"			WHERE 	1=1 " . $meses . " ";
										if ($status <> 0) {
											$sql = $sql .
												"					AND isnull(i2.statePa,1) = " . $status . " ";
										}
										if ($cliente)
											$sql = $sql .
												"					AND i2.name LIKE '%" . strtoupper($cliente) . "%' ";
										$sql = $sql .
											"					AND d2.state <> 1 " .
											"			GROUP BY i2.id ) q2 on q1.id = q2.id ";
										if ($opcDVE == 1)
											$sql = $sql .
												"WHERE	q1.dves = isnull(q2.dvesEntregues,0) ";
										if ($opcDVE == 2)
											$sql = $sql .
												"WHERE	q1.dves > isnull(q2.dvesEntregues,0) ";
										$sql = $sql .
											"GROUP BY q1.dves, q1.id, q1.name, q1.startValidity, q1.endValidity, q1.sit, q2.dvesEntregues ";
										if ($opcPA == 1) {
											$sql = $sql .
												"HAVING		isnull(sum(p.valor),0) > 0";
										}
										if ($opcPA == 2) {
											$sql = $sql .
												"HAVING		isnull(sum(p.valor),0) = 0";
										}
										$sql = $sql .
											"ORDER BY	q1.endValidity ";

										$cur = odbc_exec($db, $sql);
										$i = 0;*/

				$cliente = $_POST['cliente'] ?? null;
				$status = $_POST['status'] ?? null;
				$opcDVE = $_POST['opcDVE'] ?? null;
				$opcPA = $_POST['opcPA'] ?? null;

				$sql = "
    SELECT ISNULL(SUM(p.valor), 0) AS valor, 
           q1.*, 
           ISNULL(q2.dvesEntregues, 0) AS dvesEntregues
    FROM (
        SELECT COUNT(d.id) AS dves, 
               i.id, 
               i.name, 
               i.startValidity, 
               i.endValidity, 
               ISNULL(i.statePa, 1) AS sit
        FROM Inform i
        JOIN DVE d ON i.id = d.idInform
        WHERE (i.state = 10 OR i.state = 11) ";

				$params = [];
				if ($status != 0) {
					$sql .= " AND ISNULL(i.statePa, 1) = ? ";
					$params[] = $status;
				}

				if ($cliente) {
					$sql .= " AND i.name LIKE ? ";
					$params[] = '%' . strtoupper($cliente) . '%';
				}

				$sql .= " 
        GROUP BY i.id, i.name, i.startValidity, i.endValidity, i.statePa
    ) q1
    LEFT JOIN PADet p ON q1.id = p.idInform
    LEFT JOIN (
        SELECT COUNT(d2.id) AS dvesEntregues, 
               i2.id
        FROM Inform i2
        JOIN DVE d2 ON i2.id = d2.idInform
        WHERE 1=1 ";

				if ($status != 0) {
					$sql .= " AND ISNULL(i2.statePa, 1) = ? ";
					$params[] = $status;
				}

				if ($cliente) {
					$sql .= " AND i2.name LIKE ? ";
					$params[] = '%' . strtoupper($cliente) . '%';
				}

				$sql .= " 
            AND d2.state <> 1
        GROUP BY i2.id
    ) q2 ON q1.id = q2.id ";

				if ($opcDVE == 1) {
					$sql .= " WHERE q1.dves = ISNULL(q2.dvesEntregues, 0) ";
				} elseif ($opcDVE == 2) {
					$sql .= " WHERE q1.dves > ISNULL(q2.dvesEntregues, 0) ";
				}

				$sql .= "
    GROUP BY q1.dves, q1.id, q1.name, q1.startValidity, q1.endValidity, q1.sit, q2.dvesEntregues ";

				if ($opcPA == 1) {
					$sql .= " HAVING ISNULL(SUM(p.valor), 0) > 0 ";
				} elseif ($opcPA == 2) {
					$sql .= " HAVING ISNULL(SUM(p.valor), 0) = 0 ";
				}

				$sql .= " ORDER BY q1.endValidity ";

				// Prepara a query para execução
				$cur = odbc_prepare($db, $sql);

				if (!$stmt) {
					die("Erro ao preparar a consulta: " . odbc_errormsg($db));
				}

				// Executa a query com os parâmetros
				$result = odbc_execute($cur, $params);

				if (!$result) {
					die("Erro ao executar a consulta: " . odbc_errormsg($db));
				}

				$i = 0;

				while (odbc_fetch_row($cur)) {
					$i++;
					$id = odbc_result($cur, "id");
					$name = odbc_result($cur, "name");
					$startValidity = odbc_result($cur, "startValidity");
					$endValidity = odbc_result($cur, "endValidity");
					$dves = odbc_result($cur, "dves");
					$statePa = odbc_result($cur, "sit");
					$valor = odbc_result($cur, "valor");
					$dvesEntregues = odbc_result($cur, "dvesEntregues");

					if ($statePa == 1)
						$sit = "N&atilde;o calculada";
					if ($statePa == 2)
						$sit = "Calculada";
					if ($statePa == 3)
						$sit = "Financeiro";
					if ($statePa == 4)
						$sit = "Emitida";

					$apolice = numApolice($id, $db, $dbSisSeg);
					if ($i % 2 == 0) {
						$cor = 'style="background-color:#FFF"';
					} else {
						$cor = '';
					}
					?>
					<tr <?php echo $cor; ?>>
						<td><?php echo $apolice; ?></td>
						<td><a
								href="../dve/dve.php?comm=calculaPa&idInform=<?php echo $id; ?>&opcDVE=<?php echo $opcDVE; ?>&cliente=<?php echo $cliente; ?>&status=<?php echo $status; ?>&opcPA=<?php echo $opcPA; ?>"><?php echo ($name); ?></a>
						</td>
						<td><?php echo substr($startValidity, 8, 2) . "/" . substr($startValidity, 5, 2) . "/" . substr($startValidity, 0, 4) .
							" a " . substr($endValidity, 8, 2) . "/" . substr($endValidity, 5, 2) . "/" . substr($endValidity, 0, 4); ?>
						</td>
						<td><?php echo $dvesEntregues . "/" . $dves; ?></td>
						<td><?php echo number_format($valor, 2, ',', '.'); ?></td>
						<td><?php echo $sit; ?></td>
					</tr>
				<?php }
				if ($i == 0) {
					?>
					<tr>
						<td colspan="6">Nenhum registro encontrado</td>
					</tr>
				<?php } ?>

			<?php } ?>
		</tbody>
	</table>

</div>