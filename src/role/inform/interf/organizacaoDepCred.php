<?php

include_once('../../../navegacao.php');

//inclusão

$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : '';
$idAnt = isset($_REQUEST['idAnt']) ? $_REQUEST['idAnt'] : '';

$query = "SELECT * FROM Inform_Organizacao_Credito b 
          INNER JOIN Inform a ON a.id = b.i_Inform 
          WHERE a.id = ?";
$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform]);



?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">


	<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js" type="text/javascript"></script>

	<script language="JavaScript">
		function exibirCampos(str, id, vlr) {
			if (document.getElementById(str).checked && vlr == 1) {
				document.getElementById(id).style.display = 'block';
				document.getElementById('ver2').style.display = 'none';

			} else if (document.getElementById(str).checked && vlr == 2) {
				document.getElementById(id).style.display = 'block';
				document.getElementById('ver2').style.display = 'block';
			} else {
				document.getElementById(id).style.display = 'none';
			}
		}

		function validarForm() {
			var Txtstr = '';
			if (document.getElementById('Possui_Departamento_Credito1').checked) {
				if (document.getElementById('Nome_Responsavel').value == '') {
					Txtstr = Txtstr + ' Nome do Respons&aacute;vel<br>';
				} if (document.getElementById('Cargo_Responsavel').value == '') {
					Txtstr = Txtstr + ' Cargo Respons&aacute;vel<br>';
				} if (document.getElementById('Nome_Reporta').value == '') {
					Txtstr = Txtstr + ' A quem se Reporta<br>';
				} if (document.getElementById('Cargo_Reporta').value == '') {
					Txtstr = Txtstr + ' Cargo Reporta<br>';
				} if (document.getElementById('Periodo_Credito_Medio').value == '') {
					Txtstr = Txtstr + ' Per&iacute;odo de Cr&eacute;dito M&eacute;dio<br>';
				} if (document.getElementById('Periodo_Credito_Maximo').value == '') {
					Txtstr = Txtstr + ' Per&iacute;odo de Cr&eacute;dito M&aacute;ximo<br>';
				} if (document.getElementById('Relacao_PPE2').checked && document.getElementById('Cargo_PPE').value == "") {
					Txtstr = Txtstr + ' Cargo PPE<br>';
				} if (document.getElementById('Relacao_PPE3').checked && (document.getElementById('Cargo_PPE').value == "" || document.getElementById('Parentesco_PPE').value == "")) {
					Txtstr = Txtstr + ' Rela&ccedil;&atilde;o PPE<br>';
				}
			}
			if (Txtstr != '') {
				verErro('Aten&ccedil;&atilde;o!<br>Os seguintes campos devem ser preenchidos corretamente:<br><br>' + Txtstr);
				return false;
			} else {
				return true;
			}
		}

	</script>

	<form action="<?php echo $root; ?>role/inform/Inform.php" method="post" name="formulario"
		style="height:auto !important;">
		<input type="hidden" name="comm" value="organizacaoDepCred_submit">
		<input type="hidden" name="v">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="hc_cliente" value="N">
		<input type="hidden" name="idAnt" value="<?php echo htmlspecialchars($idAnt, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="atualiza"
			value="<?php echo htmlspecialchars(odbc_result($cur, 'i_Inform') ? 2 : 1, ENT_QUOTES, 'UTF-8'); ?>">


		<ul>

			<li class="campo2colunas">
				<label>Existe um departamento de gerenciamento de cr&eacute;dito?</label>
				<div class="formopcao">
					<input type="radio" name="Possui_Departamento_Credito" id="Possui_Departamento_Credito1" <?php echo (odbc_result($cur, "Possui_Departamento_Credito") == 1 ? "checked " : ""); ?> value="1" />
				</div>
				<div class="formdescricao">Sim</div>
				<div class="formopcao">
					<input type="radio" name="Possui_Departamento_Credito" id="Possui_Departamento_Credito0" <?php echo (odbc_result($cur, "Possui_Departamento_Credito") == 0 ? "checked " : ""); ?> value="0" />
				</div>
				<div class="formdescricao">N&atilde;o</div>
			</li>

			<li class="campo2colunas">
				<label>Existe um manual de procedimentos / pol&iacute;tica de cr&eacute;dito?</label>
				<div class="formopcao">
					<input type="radio" name="Possui_Manual_Procedimento" id="Possui_Manual_Procedimento0" <?php echo (odbc_result($cur, "Possui_Manual_Procedimento") == 1 ? "checked " : ""); ?> value="1" />
				</div>
				<div class="formdescricao">Sim</div>
				<div class="formopcao">
					<input type="radio" name="Possui_Manual_Procedimento" id="Possui_Manual_Procedimento1" <?php echo (odbc_result($cur, "Possui_Manual_Procedimento") == 0 ? "checked " : ""); ?> value="0" />
				</div>
				<div class="formdescricao">N&atilde;o</div>
			</li>

			<li class="campo2colunas">
				<label>Respons&aacute;vel Nome</label>
				<input type="text" name="Nome_Responsavel" id="Nome_Responsavel"
					value="<?php echo odbc_result($cur, "Nome_Responsavel"); ?>" maxlength="60" />

			</li>
			<li class="campo2colunas">
				<label>Cargo</label>
				<input type="text" name="Cargo_Responsavel" id="Cargo_Responsavel"
					value="<?php echo odbc_result($cur, "Cargo_Responsavel"); ?>" maxlength="60" />

			</li>
			<li class="campo2colunas">
				<label>A quem se Reporta</label>
				<input type="text" name="Nome_Reporta" id="Nome_Reporta"
					value="<?php echo odbc_result($cur, "Nome_Reporta"); ?>" maxlength="60" />

			</li>
			<li class="campo2colunas">
				<label>Cargo</label>
				<input type="text" name="Cargo_Reporta" id="Cargo_Reporta"
					value="<?php echo odbc_result($cur, "Cargo_Reporta"); ?>" maxlength="60" />

			</li>

			<li class="campo2colunas">
				<label>Per&iacute;odo de Cr&eacute;dito Normalmente Concedido em Dias</label>
				M&eacute;dio <input type="text" name="Periodo_Credito_Medio" id="Periodo_Credito_Medio"
					value="<?php echo odbc_result($cur, "Periodo_Credito_Medio"); ?>" style="width:110px;" />
				&nbsp;&nbsp;
				M&aacute;ximo <input type="text" name="Periodo_Credito_Maximo" id="Periodo_Credito_Maximo"
					value="<?php echo odbc_result($cur, "Periodo_Credito_Maximo"); ?>" style="width:110px;" />

			</li>

			<li class="campo2colunas">
				<label>A Empresa possui uma &aacute;rea dedicada a Controles Internos e Preven&ccedil;&atilde;o &agrave;
					Lavagem de Dinheiro ?</label>
				<div class="formopcao">
					<input type="radio" name="Possui_Area_Controle" id="Possui_Area_Controle0" <?php echo (odbc_result($cur, "Possui_Area_Controle") == 1 ? "checked " : ""); ?> value="1" />
				</div>
				<div class="formdescricao">Sim</div>
				<div class="formopcao">
					<input type="radio" name="Possui_Area_Controle" id="Possui_Area_Controle1" <?php echo (odbc_result($cur, "Possui_Area_Controle") == 0 ? "checked " : ""); ?> value="0" />
				</div>
				<div class="formdescricao">N&atilde;o</div>
			</li>

			<br clear="all" />
			<br clear="all" />
			<li class="campo2colunas" style="width:92%">
				<label>A Empresa tem algum s&oacute;cio/acionista ou seus respectivos familiares enquadrados como pessoa
					politicamente esposta - PPE (Exerce ou exerceu nos &uacute;ltimos 5 anos cargos p&uacute;blicos),
					conforme circular SUSEP 380/08 ?</label>
			</li>

			<li class="campo2colunas" style="width:65%">
				<div class="formopcao">
					<input type="radio" name="Relacao_PPE" id="Relacao_PPE2" <?php echo (odbc_result($cur, "Relacao_PPE") == 1 ? "checked " : ""); ?> value="1"
						onClick="exibirCampos('Relacao_PPE2','ver1',1);" />
				</div>
				<div class="formdescricao">Sim, estou enquadrado ou me enquadrei como PPE nos &uacute;ltimos 5 anos
				</div>
				<br clear="all">
				<div class="formopcao">
					<input type="radio" name="Relacao_PPE" id="Relacao_PPE3" <?php echo (odbc_result($cur, "Relacao_PPE") == 2 ? "checked " : ""); ?> value="2"
						onClick="exibirCampos('Relacao_PPE3','ver1',2);" />
				</div>
				<div class="formdescricao">Sim, tenho um familiar enquadrado ou que tenha se enquadrado como PPE nos
					&uacute;ltimos 5 anos.</div>
				<br clear="all">
				<div class="formopcao">
					<input type="radio" name="Relacao_PPE" id="Relacao_PPE1" <?php echo (odbc_result($cur, "Relacao_PPE") == 0 ? "checked " : ""); ?> value="0"
						onClick="exibirCampos('Relacao_PPE1','ver1',0);" />
				</div>
				<div class="formdescricao">N&atilde;o, nunca me enquadrei ou n&atilde;o me enquadro com PPE nos
					&uacute;ltimos 5 anos.</div>
			</li>
		</ul>
		<div id="ver1"
			style="display:<?php echo (odbc_result($cur, "Relacao_PPE") == 1 || odbc_result($cur, "Relacao_PPE") == 2 ? "block " : "none"); ?>">
			<br clear="all">
			<br clear="all">
			<br clear="all">
			<ul>
				<li class="campo2colunas" style="width:30%">
					<label>Cargo *</label>
					<input type="text" name="Cargo_PPE" id="Cargo_PPE"
						value="<?php echo odbc_result($cur, "Cargo_PPE"); ?>" maxlength="60" style="width:260px;" />
				</li>

				<li id="ver2" class="campo2colunas"
					style="width:30%; display:<?php echo (odbc_result($cur, "Relacao_PPE") == 2 ? "block " : "none"); ?>">
					<label>Parentesco *</label>
					<input type="text" name="Parentesco_PPE" id="Parentesco_PPE"
						value="<?php echo odbc_result($cur, "Parentesco_PPE"); ?>" maxlength="60"
						style="width:260px;" />
				</li>
			</ul>
		</div>

		<div style="clear:both">&nbsp;</div>
		<div style="clear:both">&nbsp;</div>


		<div class="barrabotoes">
			<input type="hidden" name="inicial" id="inicial" value="" />
			<button class="botaoagg" type="button"
				onClick="document.formulario.inicial.value= 1; document.formulario.comm.value='open';document.formulario.submit();">Tela
				Inicial</button>
			<button class="botaovgg" type="button"
				onClick="document.formulario.comm.value='generalInformation';document.formulario.submit();">Tela
				Anterior</button>
			<button class="botaoagg" type="button"
				onClick=" if(validarForm()) document.formulario.submit();">Pr&oacute;xima Tela</button>
			<button class="botaovgm" type="reset" name="Reset">Limpar</button>
			&nbsp;&nbsp;&nbsp;<label>Exportar informa&ccedil;&otilde;es para Excel
				<a href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/inform/interf/relatorio_informe_excel.php?inform=<?php echo urlencode($idInform); ?>&pagina=11"
					target="_blank">
					<img border="0"
						src="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>../images/excel_icon.png"
						title="Exportar para EXCEL" />
				</a>.
			</label>

		</div>
	</form>


	<!-- FIM Conteudo Página -->
</div>