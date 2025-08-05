<?php // Criado Hicom 25/11/04 (Gustavo) 
?>
<SCRIPT language="javascript">

	var ci = new Array();

	function checaValor(obj, valor) {
		checkDecimals(obj, valor);
	}

	function validaData(fieldName, fieldValue) {
		if (!CritData(fieldValue)) {
			verErro("Esta não é uma data válida. Informe a data no formato dd/mm/aaaa.");
			//fieldName.focus();
		}
	}

	function voltar() {
		document.Form1.action = '../dve/Dve.php?comm=exibeDve';
		document.Form1.submit();
	}

	function adiciona() {
		document.Form1.action = 'dve.php?comm=exibeDveDet&idInform=<?php echo $idInform; ?>&idDVE=<?php echo $idDVE; ?>&novoLocal=1&modalidade=<?php echo $modalidade; ?>';
		document.Form1.submit();
	}

	function numVal2(n) {
		v = "";
		len = n.length;
		for (i = 0; i < len; i++) {
			c = n.substring(i, i + 1);
			v = v + ((c == ",") ? "." : (isNaN(c) ? "" : c));
		}
		return v;
	}

	function proc(opc) {

		if (opc == 3) {
			if (Form1.idCountry.options[Form1.idCountry.selectedIndex].value == 0) {
				verErro('Selecione um País.');
			}
			else if (Form1.fatura.value == '') {
				verErro('Informe a Fatura.');
				document.Form1.fatura.focus();
			}
			else if (Form1.embDate.value == '') {
				verErro('Informe a Data de Embarque.');
				document.Form1.embDate.focus();
			}
			else if (Form1.vencDate.value == '') {
				verErro('Informe o Vencimento da Fatura.');
				document.Form1.vencDate.focus();
			}
			else if (Form1.totalEmbarcado.value == '0,00') {
				verErro('Informe o Valor Embarcado.');
				document.Form1.totalEmbarcado.focus();
			}
			else if (Number(numVal2(document.Form1.proex.value)) + Number(numVal2(document.Form1.ace.value)) > Number(numVal2(document.Form1.totalEmbarcado.value))) {
				verErro('A soma dos campos PROEX e ACE não deve ser maior que o Valor Embarcado.');
				document.Form1.proex.focus();
			}
			else {
				document.Form1.action = '../dve/Dve.php?comm=exibeDveDet&executa=' + opc;
				document.Form1.submit();
			}
		}
		else if (opc == 1) {
			if (Form1.fatura.value == '') {
				verErro('Informe a Fatura.');
				document.Form1.fatura.focus();
			}
			else if (Form1.embDate.value == '') {
				verErro('Informe a Data de Embarque.');
				document.Form1.embDate.focus();
			}
			else if (Form1.vencDate.value == '') {
				verErro('Informe o Vencimento da Fatura.');
				document.Form1.vencDate.focus();
			}
			else if (Form1.totalEmbarcado.value == '0,00') {
				verErro('Informe o Valor Embarcado.');
				document.Form1.totalEmbarcado.focus();
			}
			else if (Number(numVal2(document.Form1.proex.value)) + Number(numVal2(document.Form1.ace.value)) > Number(numVal2(document.Form1.totalEmbarcado.value))) {
				verErro('A soma dos campos PROEX e ACE não deve ser maior que o Valor Embarcado.');
				document.Form1.proex.focus();
			}
			else {
				document.Form1.action = '../dve/Dve.php?comm=exibeDveDet&executa=' + opc;
				document.Form1.submit();
			}
		}
		else {
			if (confirm("Embarque será excluído! Deseja continuar?")) {
				document.Form1.action = '../dve/Dve.php?comm=exibeDveDet&executa=' + opc;
				document.Form1.submit();
			}
		}
	}

	function selecionaPais(obj) {
		document.Form1.action = '../dve/Dve.php?comm=exibeDveDet&idCountry=' + obj.options[obj.selectedIndex].value;
		document.Form1.submit();
		//		verErro(obj.options[obj.selectedIndex].value);
	}

	function selecionaImportador(obj) {
		document.Form1.c_Coface_Imp.value = ci[obj.selectedIndex];
		//  		document.Form1.fatura.focus();
	}

	<?php require_once("../../scripts/javafunc.js");
	?>

</SCRIPT>

<?php require_once("funcsDve.php");

function mkdate($a, $m, $d)
{
	return date("Y-m-d", mktime(0, 0, 0, $m, $d, $a));
}


$executa = isset($_REQUEST['executa']) ? $_REQUEST['executa'] : 0;
$novoLocal = isset($_REQUEST['novoLocal']) ? $_REQUEST['novoLocal'] : 0;

$userID = $_SESSION['userID'];
$idDetail = isset($_REQUEST['idDetail']) ? $_REQUEST['idDetail'] : 0;
$idDVE = $_REQUEST['idDVE'];
$idBuyer = isset($_REQUEST['idBuyer']) ? $_REQUEST['idBuyer'] : 0;
$idCountry = isset($_REQUEST['idCountry']) ? $_REQUEST['idCountry'] : 0;
$idImporter = isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : 0;
$c_Coface_Imp = isset($_REQUEST['c_Coface_Imp']) ? $_REQUEST['c_Coface_Imp'] : '';
$fatura = isset($_REQUEST['fatura']) ? $_REQUEST['fatura'] : '';
$embDate = isset($_REQUEST['embDate']) ? $_REQUEST['embDate'] : '';
$vencDate = isset($_REQUEST['vencDate']) ? $_REQUEST['vencDate'] : '';
$totalEmbarcado = isset($_REQUEST['totalEmbarcado']) ? $_REQUEST['totalEmbarcado'] : '';
$proex = isset($_REQUEST['proex']) ? $_REQUEST['proex'] : '';
$idInform = $_REQUEST['idInform'];
$modalidade = $_REQUEST['modalidade'];
$STATUS = isset($_REQUEST['STATUS']) ? $_REQUEST['STATUS'] : '';
$STATUSAPOLICE = isset($_REQUEST['STATUSAPOLICE']) ? $_REQUEST['STATUSAPOLICE'] : '';
$nome = isset($_REQUEST['nome']) ? $_REQUEST['nome'] : '';

$DATA = isset($_REQUEST['DATA']) ? $_REQUEST['DATA'] : '';
$ace = isset($_REQUEST['ace']) ? $_REQUEST['ace'] : '';
$novo = isset($_REQUEST['novo']) ? $_REQUEST['novo'] : '';





if ($novoLocal == 1) {
	$novo = 1;
	$idDetail = null;
}



if (!$executa)
	$executa = 0;

if ($executa == 1 || $executa == 3) {
	// alteração || inclusão

	$dataEmb = $embDate;
	$dataVenc = $vencDate;
	$valorEmb = $totalEmbarcado;
	$idBuyer = $idImporter;


	//print $executa .' ? '.$_REQUEST['totalEmbarcado'].''.$valorEmb;
	//die();

	$p = realpath(__DIR__ . '\..\\');

	require_once($p . '\includeImporter.php');

	if (isset($lastId) && $lastId) {
		$idDetail = $lastId;
		$novo = "";
	}
}

if ($executa == 2) {
	// exclusão	
	require_once('excludeImporter.php');

}

$sql = "SELECT i.name, i.i_Seg, i.nProp, i.startValidity, 
               i.endValidity, isnull(i.statePa,1) statePa, 
               d.num, d.inicio, d.state stateDVE, d.total2, i.currency 
        FROM Inform i, DVE d 
        WHERE i.id = d.idInform 
              AND i.id = ? 
              AND d.id = ?";

$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$idInform, $idDVE]);


$name = odbc_result($cur, "name");
$startValidity = odbc_result($cur, "startValidity");
$endValidity = odbc_result($cur, "endValidity");
$i_Seg = odbc_result($cur, "i_Seg");
$nProp = odbc_result($cur, "nProp");
$num = odbc_result($cur, "num");
$inicio = odbc_result($cur, "inicio");
$stateDVE = odbc_result($cur, "stateDVE");
$total2 = odbc_result($cur, "total2");
$statePa = odbc_result($cur, "statePa");

$moeda = odbc_result($cur, "currency");

odbc_free_result($cur);

if ($moeda == "2") {
	$ext = "US$";
} else if ($moeda == "6") {
	$ext = "€";
} else {
	$ext = "R$";
}

$apolice = numApolice($idInform, $db, $dbSisSeg);

?>

<?php require_once("../../../navegacao.php"); ?>
<div class="conteudopagina">



	<FORM name="Form1" action="../dve/dve.php" method="post">
		<ul>
			<li class="campo3colunas">
				<label>Cliente</label>
				<?php echo $name; ?>
			</li>
			<li class="campo3colunas">
				<label>Vigência</label>
				<?php
				echo htmlspecialchars(substr($startValidity, 8, 2) . "/" . substr($startValidity, 5, 2) . "/" . substr($startValidity, 0, 4) .
					" a " .
					substr($endValidity, 8, 2) . "/" . substr($endValidity, 5, 2) . "/" . substr($endValidity, 0, 4), ENT_QUOTES, 'UTF-8');
				?>
			</li>
			<li class="campo3colunas">
				<label>Apólice</label>
				<?php echo $apolice; ?>
			</li>
			<li class="campo3colunas">
				<label>DVE</label><?php echo $num; ?>ª
			</li>
			<li class="campo3colunas">
				<label>Início</label>
				<?php
				echo htmlspecialchars(substr($inicio, 8, 2) . "/" . substr($inicio, 5, 2) . "/" . substr($inicio, 0, 4), ENT_QUOTES, 'UTF-8');
				?>
			</li>
		</ul>
		<?php if ($executa == 2) { ?>
			<label style="color:#F00"><?php echo $msg; ?></label>

			<br clear="all" />
			<button type="button" class="botaoagm" onClick="javascript:adiciona();">Novo</button>&nbsp;&nbsp;
			<button type="button" class="botaoagm" onClick="javascript:voltar();">Voltar</button>
			<br clear="all" />
		<?php } else {

			if ($novo == 1) {
				if ($executa == 3) {
					$totalEmbarcado = str_replace(".", "", $totalEmbarcado);
					$totalEmbarcado = str_replace(",", ".", $totalEmbarcado);
					$proex = str_replace(".", "", $proex);
					$proex = str_replace(",", ".", $proex);
					$ace = str_replace(".", "", $ace);
					$ace = str_replace(",", ".", $ace);
				} else {
					$pais = "";
					$importador = "";
					$c_Coface_Imp = "";
					$embDate = "";
					$vencDate = "";
					$totalEmbarcado = 0;
					$proex = 0;
					$ace = 0;
					$fatura = "";
				}
			} else {

				$sql = "SELECT c.name pais, im.name importador, im.c_Coface_Imp, 
               dt.embDate, dt.vencDate, 
               dt.totalEmbarcado, dt.proex, dt.ace, dt.id idDetail, 
               dt.fatura, dt.idImporter, dt.idCountry 
        FROM DVEDetails dt, Country c, Importer im 
        WHERE dt.idImporter = im.id 
              AND dt.idCountry = c.id 
              AND dt.id = ?";

				$cur = odbc_prepare($db, $sql);
				odbc_execute($cur, [$idDetail]);


				$pais = odbc_result($cur, "pais");
				//$idImportador = odbc_result($cur,"idImportador");
				$importador = odbc_result($cur, "importador");
				$c_Coface_Imp = odbc_result($cur, "c_Coface_Imp");
				$embDate = odbc_result($cur, "embDate");
				$vencDate = odbc_result($cur, "vencDate");
				$totalEmbarcado = odbc_result($cur, "totalEmbarcado");
				$proex = odbc_result($cur, "proex");
				$ace = odbc_result($cur, "ace");
				$fatura = odbc_result($cur, "fatura");

				if (!$idCountry) {
					// primeira entrada, alimenta país e importador
					$idImporter = odbc_result($cur, "idImporter");
					$idCountry = odbc_result($cur, "idCountry");
				} else if ($idCountry <> odbc_result($cur, "idCountry")) {
					// mudou o país, limpa o importador
					$idImporter = "";
				} // senão mantém o país e o importador selecionados
		
				odbc_free_result($cur);

			}
			?>
			<?php if ($modalidade == 1) {
				?> <label>Vendas com cobrança à vista ou à prazo até 180 dias</label>
				<br clear="all" />
			<?php } else {
				?> <label>Vendas através de Coligadas</label>
				<br clear="all" />
			<?php } ?>
			<ul>
				<li class="campo3colunas"><label>País</label>
					<?php
					$show = true;
					if (!$idCountry) {
						$show = false;
					}
					?>
					<select id="idCountry" name="idCountry" onChange="selecionaPais(this)">
						<?php if (!$show) {
							?>
							<option value="0">Selecione um país</option>"
							<?php
						} else if ($novo == 1) {
							?>
								<option value="0"></option>"
							<?php
						}

						$sql = "SELECT DISTINCT c.id, c.name 
        FROM Country c 
        JOIN Importer imp ON imp.idCountry = c.id 
        JOIN Inform inf ON inf.id = imp.idInform 
        WHERE inf.id = ? 
              AND (imp.state <> 8 AND imp.state <> 9) 
        ORDER BY c.name";

						$cur = odbc_prepare($db, $sql);
						odbc_execute($cur, [$idInform]);


						while (odbc_fetch_row($cur)) {
							$idPais = odbc_result($cur, "id");
							$nomePais = odbc_result($cur, "name");
							echo "<option value=$idPais" . ($idPais == $idCountry ? ' selected' : '') . "> $nomePais </option>\n";
						}

						odbc_free_result($cur);
						?>
					</select>
				</li>
				<li class="campo3colunas"><label>Importador Final</label>
					<?php if ($show) { ?>
						<select id="idImporter" name="idImporter" onchange="selecionaImportador(this)"
							onblur="javascript:document.Form1.fatura.focus();">
							<?php $sql = "SELECT id, name, c_Coface_Imp 
        FROM Importer 
        WHERE idInform = ? 
              AND idCountry = ? 
              AND ISNULL(c_Coface_Imp, '') <> '' 
              AND (state <> 8 AND state <> 9) 
        ORDER BY name";

							$cur = odbc_prepare($db, $sql);
							odbc_execute($cur, [$idInform, $idCountry]);


							while (odbc_fetch_row($cur)) {
								$idImp = odbc_result($cur, "id");
								$nomeImp = odbc_result($cur, "name");
								$cofaceImp = odbc_result($cur, "c_Coface_Imp");
								$cis[] = odbc_result($cur, "c_Coface_Imp");

								echo "<option value=$idImp" . ($idImp == $idImporter ? ' selected' : '') . "> $nomeImp</option>\n";
							}
							odbc_free_result($cur);
							?>
						</select>

						<?php echo "<script language=javascript>\n";
						for ($i = 0; $i < count($cis); $i++) {
							echo "ci[$i] = '$cis[$i]';\n";
						}
						echo "</script>\n";
					} else { ?>
						<select id="idImporter" name="idImporter" class="caixa">
							<option value="">Selecione um País</option>
						</select>


					<?php } ?>
				</li>

				<li class="campo3colunas">
					<label>Nº SBCE</label>
					<input type="text" size="15" name="c_Coface_Imp"
						value="<?php echo htmlspecialchars($c_Coface_Imp, ENT_QUOTES, 'UTF-8'); ?>" onFocus="blur()">
				</li>
				<li class="campo3colunas"><label>Nº Fatura</label>
					<input type="text" name="fatura" value="<?php echo htmlspecialchars($fatura, ENT_QUOTES, 'UTF-8'); ?>"
						size="15">
				</li>
				<?php if ($show) {
					?>
					<SCRIPT language=javascript>
						document.Form1.c_Coface_Imp.value = ci[0];
					</SCRIPT>
				<?php }
				?>

				<?php if ($novo == 1) {
					if ($executa == 3) { ?>
						<li class="campo3colunas"><label>Data de Embarque (dd/mm/aaaa)</label>
							<input type="text" id="embDate" name="embDate" onBlur="validaData(this, this.value);"
								value="<?php echo htmlspecialchars($embDate, ENT_QUOTES, 'UTF-8'); ?>" size="12">
						</li>

						<li class="campo3colunas"><label>Vencimento Fatura (dd/mm/aaaa)</label>
							<input type="text" id="vencDate" name="vencDate" onBlur="validaData(this, this.value);"
								value="<?php echo htmlspecialchars($vencDate, ENT_QUOTES, 'UTF-8'); ?>" size="12">
						</li>

					<?php } else { ?>
						<li class="campo3colunas"><label>Data de Embarque (dd/mm/aaaa) </label>
							<input type="text" id="embDate" name="embDate" onBlur="validaData(this, this.value);" value=""
								size="12">
						</li>
						<li class="campo3colunas"><label>Vencimento Fatura (dd/mm/aaaa)</label>
							<input type="text" id="vencDate" name="vencDate" onBlur="validaData(this, this.value);" value=""
								size="12">
						</li>
					<?php }
				} else { ?>
					<li class="campo3colunas"><label>Data de Embarque (dd/mm/aaaa)</label>
						<input type="text" id="embDate" name="embDate" onBlur="validaData(this, this.value);"
							value="<?php echo htmlspecialchars(substr($embDate, 8, 2) . "/" . substr($embDate, 5, 2) . "/" . substr($embDate, 0, 4), ENT_QUOTES, 'UTF-8'); ?>"
							size="12">
					</li>
					<li class="campo3colunas"><label>Vencimento Fatura (dd/mm/aaaa)</label>
						<input type="text" id="vencDate" name="vencDate" onBlur="validaData(this, this.value);"
							value="<?php echo htmlspecialchars(substr($vencDate, 8, 2) . "/" . substr($vencDate, 5, 2) . "/" . substr($vencDate, 0, 4), ENT_QUOTES, 'UTF-8'); ?>"
							size="12">
					</li>

				<?php }
				?>
				<li class="campo3colunas"><label>Valor Embarcado (<?php echo $ext; ?>)</label>
					<input type="text" name="totalEmbarcado" onBlur="checkDecimals(this, this.value);" value="<?php if ($totalEmbarcado > 0)
						echo (number_format($totalEmbarcado, 2, ',', '.'));
					else
						echo ("0,00"); ?>" size="15">
				</li>
				<li class="campo3colunas"><label>PROEX (<?php echo $ext; ?>)</label>
					<input type="text" name="proex" onBlur="checkDecimals(this, this.value);"
						value="<?php echo number_format($proex, 2, ',', '.'); ?>" size="15">
				</li>
				<li class="campo3colunas"><label>ACE (<?php echo $ext; ?>)</label>
					<input type="text" name="ace" onBlur="checkDecimals(this, this.value);"
						value="<?php echo number_format($ace, 2, ',', '.'); ?>" size="15">
				</li>
			</ul>
			<div style="clear:both">&nbsp;</div>

			<?php if ($statePa == 1 || $statePa == 2) {
				if ($novo == 1) {
					?>
					<button type="button" class="botaoagm" onClick="javascript:proc(3);">Incluir</button>&nbsp;&nbsp;
				<?php } else {
					?>
					<button type="button" class="botaoagm" onClick="javascript:proc(1);">Alterar</button>&nbsp;&nbsp;
					<button type="button" class="botaoagm" onClick="javascript:proc(2);">Excluir</button>&nbsp;&nbsp;
				<?php }
				?>
				<button type="button" class="botaoagm" onClick="javascript:adiciona();">Novo</button>&nbsp;&nbsp;
			<?php }
			?>
			<button type="button" class="botaoagm" onClick="javascript:voltar();">Voltar</button>

			<?php if ($msg != '') { ?>
				<br clear="all" />
				<label style="color:#F00"><?php echo $msg; ?></label>
				<br clear="all" />
			<?php } ?>

		<?php }
		?> <br clear="all" />

		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idDetail" value="<?php echo htmlspecialchars($idDetail, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="modalidade"
			value="<?php echo htmlspecialchars($modalidade, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="DATA" value="<?php echo htmlspecialchars($DATA, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="STATUS" value="<?php echo htmlspecialchars($STATUS, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="STATUSAPOLICE"
			value="<?php echo htmlspecialchars($STATUSAPOLICE, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="nome" value="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="novo" value="<?php echo htmlspecialchars($novo, ENT_QUOTES, 'UTF-8'); ?>">


	</FORM>


</div>