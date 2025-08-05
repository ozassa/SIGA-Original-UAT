<?php
//alterado em 12/04/2004
//Alterado HiCom mes 04
//Alterado Hicom - 09/11/04 (Gustavo)
//Alterado Hicom - 02/12/04 (Gustavo) - link p/ PA
//ALTERADO PELA ANDREA ANTES LISTA 2
//Alterado Hicom - 15/12/04 (Gustavo) - Possibilidade de alterar o campo
//"Cobertura para juros de mora" se status < 6
//alterado Hicom 17/01/2005 (Gustavo) - possibilidade de alterar alguns campos de endereço, email, etc.

require_once("../RolePrefix.php");
require_once("../client/query.php");

$user = $_SESSION['userID'];

if ($_REQUEST['idInform']) {
	$idInform = $_REQUEST['idInform'];
}

function mkdate($a, $m, $d)
{
	return date("Y-m-d", mktime(0, 0, 0, $m, $d, $a));
}

if (!function_exists('getEndDate')) {
	function getEndDate($d, $n, $c = 0)
	{
		global $idDVE, $db, $idInform;

		$sql = "SELECT startValidity, tipoDve FROM Inform WHERE id = ?";
		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, [$idInform]);

		$start = ymd2dmy(odbc_result($stmt, 1));
		$tipodve = odbc_result($stmt, 2);

		odbc_free_result($stmt);

		$sql = "SELECT MAX(num) FROM DVE WHERE idInform = ?";
		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, [$idInform]);

		$num_dves = 0;
		$num_dves = odbc_result($stmt, 1);

		odbc_free_result($stmt);



		// Alterado Hicom (Gustavo) - query abaixo apresentava erro pq $idDVE estava nulo.
		// Adicionei o teste de nulo. Isso não corrige o erro, apenas a mensagem de erro.
		$num = 0;

		if ($idDVE) {
			$sql = "SELECT num FROM DVE WHERE id = ?";
			$stmt = odbc_prepare($db, $sql);
			odbc_execute($stmt, [$idDVE]);

			$num = odbc_result($stmt, 1);


			// Libera a conexão ODBC
			odbc_free_result($stmt);
		}

		// Fim alterado Hicom

		if ($num != $num_dves) {
			if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)) {
				//return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1], $v[3]));

				if ($tipodve == 3) { // Dve do tipo Anual
					return date("d/m/Y", mktime(0, 0, 0, $v[2] + 12, $c, $v[3]));
				} else if ($tipodve == 2) { // Dve do tipo Trimestral
					return date("d/m/Y", mktime(0, 0, 0, $v[2] + 3, $c, $v[3]));
				} else { // Dve do tipo Mensal
					return date("d/m/Y", mktime(0, 0, 0, $v[2] + 1, $c, $v[3]));
				}
			}
		} else {
			$sql = "SELECT endValidity FROM Inform WHERE id = ?";
			$stmt = odbc_prepare($db, $sql);
			odbc_execute($stmt, [$idInform]);

			$end = null;
			if ($stmt) {
				$end = odbc_result($stmt, 1);
				odbc_free_result($stmt);
			}

			return ymd2dmy($end);

		}
	}
}

function arruma($str)
{
	list($dia, $mes, $ano) = explode("/", $str);
	return "$ano-$mes-$dia";
}

if (!function_exists('getTimeStamp')) {
	function getTimeStamp($date)
	{
		if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})/', $date, $res)) {
			return mktime(0, 0, 0, $res[2], $res[3], $res[1]);
		} else if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/', $date, $res)) {
			return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
		}
	}
}

if (!function_exists('ymd2dmy')) {
	// converte a data de yyyy-mm-dd para dd/mm/yyyy
	function ymd2dmy($d)
	{
		if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d ?? '', $v)) {
			return "$v[3]/$v[2]/$v[1]";
		}
	}
}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('temrenovacao1')) {
	// Verifica se informe tem renovação....
	function temrenovacao1($db, $idInform)
	{
		$sql = "SELECT COUNT(id) AS qtd FROM Inform WHERE idAnt = ? AND state <> 9";
		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, [$idInform]);

		if ($stmt) {
			$qtd = odbc_result($stmt, 1);
			odbc_free_result($stmt);

			return $qtd > 0;
		} else {
			return false;
		}
	}

}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('temrenovacao2')) {
  //odbc_exec($db, "SELECT 1=1"); // Libera a conexão ODBC
	// Verifica se informe tem renovação....
	function temrenovacao2($db, $idInform, $userID)
	{
		$sql = "SELECT COUNT(i.id) AS qtd
		            FROM Inform i
		            JOIN Insured ins ON (ins.id = i.idInsured)
		            WHERE ins.idResp = ?
		              AND i.name IS NOT NULL
		              AND i.id <> ?
		              AND i.state = 1";

		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, [$userID, $idInform]);

		if ($stmt) {
			$qtd = odbc_result($stmt, 1);
			odbc_free_result($stmt);

			return $qtd > 0;
		} else {
			odbc_free_result($stmt);
			
			return false;
		}
	}

}

////////////////////////////////////////////////////////

if (!function_exists('temrenovacao')) {
	// Verifica se informe tem renovação....
	function temrenovacao($db, $idInform, $user)
	{
		if (temrenovacao1($db, $idInform) || temrenovacao2($db, $idInform, $user)) {

			return true;

		} else {

			return false;

		}
	}
}

//////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////

if (!function_exists('get_de_st_inform')) {
	// Retorna o status do Inform
	function get_de_st_inform($status)
	{
		if ($status == 1) {
			return "Novo";

		} elseif ($status == 2) {
			return "Preenchido";

		} elseif ($status == 3) {
			//ALterador Por Tiago V N - Elumini - 29/12/2005
			//	    return "Validado";
			return "An. Crédito";

		} elseif ($status == 4) {
			//ALterador Por Tiago V N - Elumini - 29/12/2005
			//	    return "Analisado";
			return "Tarifação";

		} elseif ($status == 5) {
			//ALterador Por Tiago V N - Elumini - 29/12/2005
			//	    return "Tarifado";
			return "Oferta";

		} elseif ($status == 6) {
			return "Proposta";

		} elseif ($status == 7) {
			return "Confirmado";

		} elseif ($status == 8) {
			return "Alterado";

		} elseif ($status == 9) {
			return "Cancelado";

		} elseif ($status == 10) {
			return "Apólice";

		} elseif ($status == 11) {
			return "Encerrado";
		} else {
			return "Indefinido ($status)";
		}
	}
}
////////////////////////////////////////////////////////

$sql_sum = "SELECT ISNULL(SUM(ROUND(dt.totalEmbarcado, 2)), 0) AS totalEmbarcado
            FROM DVE d, DVEDetails dt, Importer imp
            WHERE d.id = dt.idDVE 
              AND dt.idImporter = imp.id 
              AND d.idInform = ? 
              AND ISNULL(dt.state, 1) = 1 
              AND d.state > 1";

$xx = odbc_prepare($db, $sql_sum);
odbc_execute($xx, [$idInform]);

$sum_dve_ok = odbc_result($xx, 1);
$sum_dve_ok = number_format($sum_dve_ok, 2, ',', '.');

odbc_free_result($xx);

//pesquisa o tipo da moeda
$sql_mod = "SELECT currency 
            FROM Inform 
            WHERE id = ?";

$xxx = odbc_prepare($db, $sql_mod);
odbc_execute($xxx, [$idInform]);


// Libera a conexão após o uso, se necessário

$currency = odbc_result($xxx, 'currency');

//define a moeda
if ($currency == 1) {
	//real
	$moeda = "R$";
} elseif ($currency == 2) {
	//dolar
	$moeda = "US$";
} elseif ($currency == 6) {
	//euro
	$moeda = "€";
}
odbc_free_result($xxx);

$hc_temrenovacao = false;

$hc_temrenovacao = temrenovacao($db, $idInform, $user);

$wsql = "SELECT startValidity, endValidity, state, ISNULL(statePa, 1) AS statePa, Ga, name 
         FROM Inform 
         WHERE id = ?";

$stmt = odbc_prepare($db, $wsql);
odbc_execute($stmt, [$idInform]);

if ($stmt) {
	$hc_startValidityCl = odbc_result($stmt, 1);
	$hc_endValidityCl = odbc_result($stmt, 2);
	$hc_stateCl = odbc_result($stmt, 3);
	$hc_statePa = odbc_result($stmt, "statePa");
	$hc_ga = odbc_result($stmt, "Ga");

	// Libera a conexão após uso, se necessário
	odbc_free_result($stmt);
}


$vigente = 0;
if ($statusCl == 10) {
	$vigente = 1;
}

require_once("../../../navegacao.php");
?>
<div class="conteudopagina">
	<ul>
		<li class="campo3colunas">
			<label>Segurado</label>
			<?php echo ($nameCl ? $nameCl : odbc_result($cur, 'name')); ?>
		</li>

		<?php if ($hc_startValidityCl) { ?>
			<li class="campo3colunas" style="width: 190px;">
				<label>Vig&ecirc;ncia</label>
				<?php echo ymd2dmy($hc_startValidityCl); ?> &agrave; <?php echo ymd2dmy($hc_endValidityCl); ?>
			</li>
		<?php } ?>

		<li class="campo3colunas" style="width: 190px;">
			<label>Situa&ccedil;&atilde;o</label>
			<p><?php echo (get_de_st_inform(odbc_result($cur, 3))) . ($statusCl == 10 ? '  Vigente' : ''); ?></p>
		</li>

		<?php
		$query = "SELECT c.contato AS consultor
	          FROM Inform i
	          INNER JOIN consultor c ON c.idconsultor = i.idConsultor
	          WHERE i.id = ?";

		$stmt = odbc_prepare($db, $query);
		odbc_execute($stmt, [$idInform]);

		$consultor = null;
		if ($stmt) {
			$consultor = odbc_result($stmt, 'consultor');

			// Libera a conexão após uso
			odbc_free_result($stmt);
		}

		?>

		<li class="campo3colunas" style="width: 190px;">
			<label>Consultor Respons&aacute;vel</label>
			<p><?php echo $consultor; ?></p>
		</li>

	</ul>

	<p>&nbsp;<?php echo isset($_REQUEST['msg']) ? htmlspecialchars($_REQUEST['msg'], ENT_QUOTES, 'UTF-8') : ''; ?></p>

	<br clear="all" />
	<?php
	if ($vigente || ($statusCl >= 3 && $statusCl < 9)) { ?>
		<br clear="all" />
		<table class="tabela01" width="100%">
			<tbody>
				<?php if ($statusCl == 10) { ?>
					<tr class="odd">
						<td>
							<label><a
    href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/client/Client.php?comm=ficha&idInform=<?php echo urlencode($idInform); ?>">
    Ficha de Aprova&ccedil;&atilde;o de Limites de Cr&eacute;dito
</a></label>

						</td>
					</tr>
				<?php } ?>

				<?php
				if ($statusCl == 10 && $codProd) { // apolice foi emitida ?>
					

					<?php
					// Obtém o valor de warantyInterest
					$query_juros = "SELECT warantyInterest FROM Inform WHERE id = ?";
					$stmt_juros = odbc_prepare($db, $query_juros);
					odbc_execute($stmt_juros, [$idInform]);
					$juros = odbc_result($stmt_juros, 1);
					odbc_free_result($stmt_juros);

					if ($juros == 0) { ?>
    <form action="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/client/Client.php" method="post" name="juros"
        style="min-height:inherit !important">
        <input type="hidden" name="comm" value="condEsp">
        <input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
        <?php if ($hc_ga != 1) { ?>
            <tr class="odd">
                <td>
                    <label><a
                        href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/client/Client.php?comm=jurosMora&idInform=<?php echo urlencode($idInform); ?>#condEsp">
                        Condi&ccedil;&atilde;o Especial Juros de Mora
                    </a></label>
                </td>
            </tr>
        <?php } ?>
						</form>
						<?php
						// Consulta para obter dados de JurosMora
						$query_jm = "SELECT id, datePag FROM JurosMora WHERE idInform = ? AND state = 2";
						$stmt_jm = odbc_prepare($db, $query_jm);
						odbc_execute($stmt_jm, [$idInform]);
						$idJuros = odbc_result($stmt_jm, 1);
						$datePag = odbc_result($stmt_jm, 2);
						odbc_free_result($stmt_jm);

						if ($datePag) { ?>
							<tr class="odd">
								<td>
									<label><a
											href="<?php echo $root; ?>role/client/Client.php?comm=condEsp&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idJuros=<?php echo $idJuros; ?>#condEsp">Emitir
											Endosso</a></label>
								</td>
							</tr>
							<?php
						} // emitir endosso
					} // if juros
			


					$query = "SELECT FORMAT(endValidity, 'yyyy-MM-dd') AS endValidity FROM Inform WHERE id = ?";
					$cur = odbc_prepare($db, $query);
					odbc_execute($cur, [$idInform]);

					// Liberação de recursos, se necessário
					$end_vig = odbc_result($cur, 'endValidity');
					odbc_free_result($cur);

					list($ano, $mes, $dia) = explode('-', $end_vig);
					$d_Venc = date("Y-m-d", mktime(0, 0, 0, $mes, $dia + 210, $ano));

					$dataH = date("d/m/Y"); //formata data de hoje
					$dateHoje = arruma($dataH);

					if ($statusCl == 10 && $codProd) { // apolice foi emitida, disponibilizar Cessão de Direitos ?>
						<tr class="odd">
							<td>
								<label><a
										href="<?php echo $root; ?>role/client/Client.php?comm=cessao&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>#cessao">Cess&atilde;o
										de Direito</a></label>
							</td>
						</tr>

							<tr class="odd">
								<td>
									<label><a
											href="<?php echo $root; ?>role/cessao/Cessao.php?comm=cancelaCessaoDireito&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Cancelamento
											de Cess&atilde;o de Direito</a></label>
								</td>
							</tr>

							<tr class="odd">
								<td>
									<label><a
											href="<?php echo $root; ?>role/dve/Dve.php?comm=DVEConsulta&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Consulta
											de DVE</a></label>
								</td>
							</tr>

							<tr class="odd">
								<td>
									<label><a
											href="<?php echo $root; ?>role/dve/Dve.php?comm=DVELiquidacao&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Conclus&atilde;o
											da Liquida&ccedil;&atilde;o de DVE</a></label>
								</td>
							</tr>

					<?php
					} // cessao 
					?>

				</tbody>
			</table>

			<br clear="all" />

			<?php
				}
	} elseif ($statusCl = 11) { ?>
		<br clear="all" />
		<table class="tabela01" width="100%">
			<tbody>
				<tr class="odd">
					<td>
						<label><a
								href="<?php echo $root; ?>role/dve/Dve.php?comm=DVEConsulta&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Consulta
								de DVE</a></label>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<label><a
								href="<?php echo $root; ?>role/dve/Dve.php?comm=DVELiquidacao&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Conclus&atilde;o
								da Liquida&ccedil;&atilde;o de DVE</a></label>
					</td>
				</tr>
			</tbody>
		</table>
		<br clear="all" />

		<?php
	} else {

		if ($hc_temrenovacao) {
			$msg = "Ap&oacute;lice encerrada.";
		} else {
			$msg = "Ap&oacute;lice encerrada. Voc&ecirc; pode renov&aacute;-la se quiser.";
		}
	}

	if ($statusCl >= 10) { ?>
		<form action="<?php echo $root; ?>role/client/Client.php" method="post" style="min-height:40px !important;">
			<ul>
				<li class="campo2colunas">
					<label>Demonstrativo do Faturamento de An&aacute;lise e Monitoramento</label>

					<input type="hidden" name="comm" value="reportImporter">
					<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="origem" value="3">

					<select name="anoMes" style="width:200px ;min-height:inherit !important">
						<?php  	// Acesso à nova estrutura de análise e monitoramento
							$query = "SELECT ano, mes 
							          FROM resFatAnaliseMonitor 
							          WHERE idInform = ? 
							          ORDER BY ano, mes";

							$cur = odbc_prepare($db, $query);
							odbc_execute($cur, [$idInform]);
							$count = 0;
							while (odbc_fetch_row($cur)) {  //fixme: remover aqui, quando o demonstrativo estiver correto
								$ano = odbc_result($cur, 1);
								$mes = odbc_result($cur, 2);
								?>
							<option value=<?php echo "$ano-$mes"; ?>><?php echo "$mes/$ano"; ?></option>
							<?php $count++;
							}
							?>
					</select>
					<?php if ($count > 0) { ?>
						<button class="botaoapm" type="submit">Ver Resumo</button>
					<?php } else { ?>
						Nenhum Demonstrativo dispon&iacute;vel
					<?php } ?>
				</li>
			</ul>
		</form>
	<?php }

	$possui_renovacao = isset($possui_renovacao) ? $possui_renovacao : false;
	if (!$hc_temrenovacao) {
		?>
		<?php if ($possui_renovacao) {  /* pode continuar.... não afeta em nada...*/ ?>
			<ul>
				<li class="campo2colunas">
					<label>
						<a href="<?php echo $root; ?>role/inform/Inform.php?comm=open&idInform=<?php echo $idRenovacao; ?>">
							<?php
							if ($statusCl == 11) {
								if ($hc_temrenovacao) {
									echo ('Novo seguro');
								} else {
									echo ('Solicitar novo seguro');
								}
							} else {
								echo ('Renova&ccedil;o de Ap&oacute;lice');
							}

							?></a>
					</label>
				</li>
			</ul>
		<?php } else {

			// $cur = odbc_exec($db, "SELECT format(endValidity, 'yyyy-MM-dd') as endValidity FROM Inform WHERE id = $idInform");
			// $end_vig = odbc_result($cur, 'endValidity');
			// list($anoT, $mesT, $diaT) = explode ('-', $end_vig);
			// $d_Venc = date ("Y-m-d", mktime (0, 0, 0, $mesT, $diaT + 210, $anoT));
			$dataH = date("d/m/Y"); //formata data de hoje
			$dateHoje = arruma($dataH);

			$query = "SELECT COUNT(*) 
							          FROM Inform 
							          WHERE id = ? AND state = 10 AND endValidity < GETDATE() + 90";

			$c = odbc_prepare($db, $query);
			odbc_execute($c, [$idInform]);

			// Liberação de recursos, se necessário
			$cont = odbc_result($c, 1);

			odbc_free_result($c);


			if (($statusCl == 10 && $cont > 0) || /*!$vigente || */ $statusCl == 11 || $statusCl == 9) {

				?>
				<ul>
					<li class="campo2colunas">
						<label>
							<a href="<?php echo $root; ?>role/client/Client.php?comm=renovacao&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
								<button type="button"
									class="botaoapg"><?php echo $statusCl == 11 ? 'Solicitar novo seguro' : 'Renovar ap&oacute;lice'; ?></button>
							</a>
						</label>
					</li>
				</ul>
				<br clear="all" />
			<?php
			}
		}
	}
	?>
	<?php  //echo "AQUI..." . $statusCl . $codProd;
	if (($statusCl == 10 || $statusCl == 11) && $codProd) { // apolice foi emitida, disponibilizar DVE
		?>
		<form action="<?php echo $root; ?>role/dve/Dve.php#tabela" onSubmit="return verifica(this.numDVE)"
			style="min-height:inherit !important">
			<ul>
				<li class="campo2colunas">
					<label>Declara&ccedil;&atilde;o do Volume de Vendas</label>
					<input type="hidden" name="comm" value="view">
					<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="client" value="1">
					<input type="hidden" name="primeira_tela" value="1">

					<select name="numDVE" style="width:200px">
						<option value="0" selected>Selecione o per&iacute;odo</option>
						<?php

						$inicio = ymd2dmy($startValidity);

						$query = "SELECT MAX(num) FROM DVE WHERE idInform = ?";
						$stmt = odbc_prepare($db, $query);
						odbc_execute($stmt, [$idInform]);

						$num_dves = null;
						if ($stmt) {
							$num_dves = odbc_result($stmt, 1);

							// Liberação de recursos
							odbc_free_result($stmt);
						}

						for ($num = 1; $num <= $num_dves; $num++) {
							$fim = getEndDate($inicio, 1);

							$sql = "SELECT		state " .
								"FROM		DVE " .
								"WHERE		idInform = $idInform and num = $num";

							$cur = odbc_prepare($db, $sql);
							odbc_execute($cur, [$param1, $param2]); // Substitua $param1, $param2 pelos valores corretos, se necessário
					
							// Liberação de recursos, se necessário
							$state = odbc_result($cur, "state");

							odbc_free_result($cur);

							$vencimentoDve = mkdate(substr($fim, 6, 4), substr($fim, 3, 2), substr($fim, 0, 2) + 16);
							if ($state == 1 && getTimeStamp($vencimentoDve) < time()) {
								echo "<option value=$num>$inicio a $fim *</option>";
							} else {
								echo "<option value=$num>$inicio a $fim</option>";
							}
							$inicio = getEndDate($inicio, 1, 1);
							if (getTimeStamp($inicio) > time()) {
								break;
							}
						}
						?>
					</select>
					<button class="botaoapm" type="submit">Abrir</button>
					<strong>(*) DVEs vencidas</strong>
				</li>
			</ul>

			<br clear="all" />
			<br clear="all" />
			<ul>
				<li class="campo2colunas">
					<label>Total das DVE'S :
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $moeda . " " . $sum_dve_ok; ?></strong></label>
				</li>
			</ul>


		</form>

		<ul>
			<?php  // alterado Hicom (Gustavo)
				if ($hc_statePa > 1) { ?>
				<li class="campo2colunas">
					<label><a
							href="<?php echo $root; ?>role/client/Client.php?comm=consultaPa&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Parcela
							de Ajuste</a></label>
				</li><br clear="all" />
			<?php } ?>
			<li class="campo2colunas">
				<label><a href="<?php echo $root; ?>role/client/codigoAprovacao.php?idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>"
						target="_blank">C&oacute;digos de Aprova&ccedil;&atilde;o de Limite de Cr&eacute;dito</a></label>
			</li>
		</ul>
		<?php
	} else if ($hc_stateCl <> 9 && $hc_stateCl <> 11) { ?>

			<ul>
				<li class="campo2colunas">
					<label><a
							href="<?php echo $root; ?>role/client/Client.php?comm=changeGeneralInf&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Alterar
							Informa&ccedil;&otilde;es Gerais</a></label>
				</li>
			</ul>
	<?php } ?>

	<br clear="all" />
	<?php  //Alterado Hicom (Gustavo) opção de juros de mora
	if ($hc_stateCl == 1 || $hc_stateCl == 2 || $hc_stateCl == 3 || $hc_stateCl == 4 || $hc_stateCl == 5) {
		if ($hc_ga != 1) {
			?>
			<ul>
				<li class="campo2colunas">
					<label><a
							href="<?php echo $root; ?>role/client/Client.php?comm=changeWarantyInterest&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">Alterar
							op&ccedil;&atilde;o de Juros de Mora</a></label>
				</li>
			</ul>
		<?php }
	}
	?>

	<?php
	require_once("../inform/interf/listainformes.php");
	?>

	<div style="clear:both">&nbsp;</div>
	<label>
		<p style="color:#F00"><?php echo ($msg); ?></p>
	</label>

	<div style="clear:both">&nbsp;</div>
	<?php
	$j = 0; // Variavel para validar a existência do Botão voltar
	//print $statusCl;
	if (($statusCl == 10) && ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')) {
		$j++; ?>
		<form action="../inform/Inform.php" method="post" style="min-height:inherit !important">
			<input type="hidden" name="comm" value="open" class="sair">
			<div class="barrabotoes">
				<button class="botaovgm" type="submit">Voltar</button>
				<button type="button" class="botaoagg"
					onClick="window.location = '<?php echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=senha';">Alterar
					Senha</button>
				<?php if (check_menu(['executive'], $role)) { ?>
					<button type="button" class="botaoagg"
						onClick="window.location = '<?php echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=login';">Alterar
						Login</button>
				<?php } ?>
			</div>
		</form>
	<?php
	} else if (($statusCl == 10) && ($_SESSION['pefil'] == 'F' || $_SESSION['pefil'] == 'CO')) {
		$j++; ?>
			<form action="../searchClient/ListClient.php?comm=view&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>" method="post"
				style="min-height:inherit !important">
				<div class="barrabotoes">
					<button class="botaovgm" type="submit">Voltar</button>
					<button type="button" class="botaoagg"
						onClick="window.location = '<?php echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=senha';">Alterar
						Senha</button>
				<?php if ($role['executive']) { ?>
						<!--<button type="button" class="botaoagg" onClick="window.location = '<?php //echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php  //htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=login';">Alterar Login</button>-->
				<?php } ?>
				</div>
			</form>
	<?php }


	if (check_menu(['executive'], $role) && $statusCl != 10) {
		$j++; ?>
		<form action="<?php echo $root; ?>role/inform/Inform.php" method="post" name="formulario"
			style="min-height:inherit !important">
			<input type="hidden" name="comm" value="goback">
			<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="v" value="0">
			<div class="barrabotoes">
				<button class="botaovgm" type="button"
					onclick="javascript: window.location = '../searchClient/ListClient.php?comm=view&idInform=<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>';">Voltar</button>
				<button type="button" class="botaoagg"
					onClick="window.location = '<?php echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=senha';">Alterar
					Senha</button>
				<?php if ($role['executive']) { ?>
					<button type="button" class="botaoagg"
						onClick="window.location = '<?php echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=login';">Alterar
						Login</button>
				<?php } ?>
			</div>

			<br clear="all" />
		</form>
		<?php
	}
	?>

	<?php
	if (isset($renova)) {
		if ($renova) {
			$j++; ?>
			<form action="<?php echo $root; ?>role/inform/Inform.php" method="post" name="formulario"
				style="min-height:inherit !important">
				<input type="hidden" name="comm" value="client">
				<input type="hidden" name="idInform" value="<?php echo $idAnt; ?>">
				<div class="barrabotoes">
					<button class="botaovgm" type="submit">Voltar</button>
					<button type="button" class="botaoagg"
						onClick="window.location = '<?php echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=senha';">Alterar
						Senha</button>
					<?php if ($role['executive']) { ?>
						<button type="button" class="botaoagg"
							onClick="window.location = '<?php echo $host; ?>src/role/client/Client.php?comm=changePassword&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&opc=login';">Alterar
							Login</button>
					<?php } ?>
				</div>
			</form>
		<?php
		}
	}
	?>

</div>

<script language=javascript>
	function verifica(s) {
		var v = s.options[s.selectedIndex].value;
		if (v == 0) {
			verErro("Selecione um período de DVN");
			return false;
		}
		return true;
	}

	function condEsp(myId) {
		if (confirm("Deseja Realmente Criar uma Condição Especial Juros de Mora?")) {
			document.forms["juros"].idInform.value = myId;
			document.forms["juros"].submit();
		}
	}
</script>