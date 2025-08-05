<?php

if (!isset($_SESSION)) {
	session_start();
}

include_once("../../../navegacao.php");


function formataValorSql($formataValorSql)
{
	if ($formataValorSql == "") {
		$formataValorSql = '0.00';
	} else {
		$formataValorSql = str_replace('.', '', $formataValorSql);
		$formataValorSql = str_replace(',', '.', $formataValorSql);
	}
	return $formataValorSql;
}

function addDia($data, $qtd)
{
	$data_br = $data;
	list($dia, $mes, $ano) = explode('/', $data_br);
	$time = mktime(0, 0, 0, $mes, $dia + $qtd, $ano);
	return strftime('%Y-%m-%d', $time);
}
?>
<script language="javascript" type="text/javascript"
	src="<?php echo $host; ?>Scripts/tinymce/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript"
	src="<?php echo $host; ?>Scripts/tinymce/tiny_mce/basic_config.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		// General options
		mode: "textareas",
		theme: "advanced",
		plugins: "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		// theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		// theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		//  theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",

		theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect|cut,copy,anchor,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		//theme_advanced_buttons2 : "cut,copy,paste,pastetext|anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_toolbar_location: "top",
		theme_advanced_toolbar_align: "left",
		theme_advanced_statusbar_location: "bottom",
		theme_advanced_resizing: true,

		// Skin options
		skin: "o2k7",
		skin_variant: "silver",

		// Example content CSS (should be your site CSS)
		content_css: "<?php echo $host; ?>Scripts/tinymce/css/example.css",

		// Drop lists for link/image/media/template dialogs
		// template_external_list_url : "<?php echo $host; ?>Scripts/tinymce/js/template_list.js",
		//  external_link_list_url : "<?php echo $host; ?>Scripts/tinymce/js/link_list.js",
		// external_image_list_url : "<?php echo $host; ?>Scripts/tinymce/js/image_list.js",
		//  media_external_list_url : "<?php echo $host; ?>Scripts/tinymce/js/media_list.js",

		// Replace values for the template plugin
		template_replace_values: {
			username: "Some User",
			staffid: "991234"
		}
	});


</script>
<div class="conteudopagina">

	<?php
	/*
								  $_POST['t_endosso'];
								  $_POST['d_Emissao'];
								  $_POST['n_Parcelas'];
								  $_POST['v_Premio'];
								  $_POST['d_Vigencia_Inicial'];
								  $_POST['d_Vigencia_Final'];
								  $_POST['Descricao'];
								  */
	$op = isset($_REQUEST['operacao']) ? $_REQUEST['operacao'] : 0;
	if ($op == 1) {
		$sql = "SELECT MAX(n_Endosso) AS n_Endosso FROM Inform_Endosso WHERE i_Inform = ?";
		$rrm = odbc_prepare($db, $sql);
		odbc_execute($rrm, [$_REQUEST['idInform']]);

		$Max = odbc_result($rrm, 'n_Endosso');

		odbc_free_result($rrm);
		$Max += 1;

		$sql = "INSERT INTO Inform_Endosso 
            (i_Inform, n_Endosso, t_Endosso, d_Emissao, n_Parcelas, v_Premio, d_Vigencia_Inicial, d_Vigencia_Final, Descricao, i_Solicitante, s_Endosso)
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$add = odbc_prepare($db, $sql);

		$params = [
			$_REQUEST['idInform'],
			$Max,
			$_POST['t_endosso'],
			($_POST['d_Emissao'] != '' ? Convert_Data_Geral($_POST['d_Emissao']) : null),
			($_POST['n_Parcelas'] > 0 ? $_POST['n_Parcelas'] : 0),
			($_POST['v_Premio'] > 0 ? formataValorSql($_POST['v_Premio']) : 0),
			($_POST['d_Vigencia_Inicial'] != '' ? Convert_Data_Geral($_POST['d_Vigencia_Inicial']) : null),
			($_POST['d_Vigencia_Final'] != '' ? Convert_Data_Geral($_POST['d_Vigencia_Final']) : null),
			$_POST['Descricao'],
			$_SESSION['userID'],
			1 // Situação: Emitido
		];

		odbc_execute($add, $params);


		//Comando de inclusão dos dados das parcelas que deverá ser repetido para cada parcela na tela:
		$valorParcela = 0;
		$dataVenc = $_POST['d_Emissao'];
		$valorPremio = formataValorSql($_POST['v_Premio']);
		$valorParcela = ($valorPremio / $_POST['n_Parcelas']);
		$dias = 30;
		$p = 1;
		for ($i = 0; $i < $_POST['n_Parcelas']; $i++) {
			$sql = "INSERT INTO Parcela 
            (i_Inform, n_Endosso, i_Sinistro, t_Parcela, n_Parcela, d_Emissao, d_Vencimento, v_Parcela, s_Parcela)
        VALUES 
            (?, ?, NULL, 100, ?, ?, ?, ?, 0)"; // Situação: Emitida
	
			$add = odbc_prepare($db, $sql);

			$params = [
				$_REQUEST['idInform'],
				$Max,
				$p,
				($_POST['d_Emissao'] != '' ? Convert_Data_Geral($_POST['d_Emissao']) : null),
				addDia($dataVenc, $dias),
				$valorParcela
			];

			odbc_execute($add, $params);

			$p++;
			$dias += 30;

			odbc_free_result($add);
		}
		// reload de página
		?>
		<script>
			window.location = '<?php echo $root; ?>role/searchClient/ListClient.php?comm=cadastro_endosso&idInform=<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>'; </script>
		<?php
	} else if ($op == 2) {
		$sql = "UPDATE Inform_Endosso 
        SET t_Endosso = ?, 
            d_Emissao = ?, 
            n_Parcelas = ?, 
            v_Premio = ?, 
            d_Vigencia_Inicial = ?, 
            d_Vigencia_Final = ?, 
            Descricao = ?, 
            i_Solicitante = ?, 
            s_Endosso = 2";

		$add = odbc_prepare($db, $sql);

		$params = [
			$_POST['t_endosso'],
			($_POST['d_Emissao'] != '' ? Convert_Data_Geral($_POST['d_Emissao']) : null),
			($_POST['n_Parcelas'] > 0 ? $_POST['n_Parcelas'] : 0),
			($_POST['v_Premio'] > 0 ? formataValorSql($_POST['v_Premio']) : 0),
			($_POST['d_Vigencia_Inicial'] != '' ? Convert_Data_Geral($_POST['d_Vigencia_Inicial']) : null),
			($_POST['d_Vigencia_Final'] != '' ? Convert_Data_Geral($_POST['d_Vigencia_Final']) : null),
			$_POST['Descricao'],
			$_SESSION['userID']
		];

		odbc_execute($add, $params);


		//Comando de inclusão dos dados das parcelas que deverá ser repetido para cada parcela na tela:
		$valorParcela = 0;
		$dataVenc = $_POST['d_Emissao'];
		$valorPremio = formataValorSql($_POST['v_Premio']);
		$valorParcela = ($valorPremio / $_POST['n_Parcelas']);
		$dias = 30;
		$p = 1;

		odbc_free_result($add);
		$sql = "DELETE FROM Parcela WHERE n_Endosso = ? AND i_Inform = ?";

		$rem = odbc_prepare($db, $sql);

		$params = [
			$_REQUEST['n_Endosso'],
			$_REQUEST['idInform']
		];

		odbc_execute($rem, $params);


		$sql = "INSERT INTO Parcela 
        (i_Inform, n_Endosso, i_Sinistro, t_Parcela, n_Parcela, d_Emissao, d_Vencimento, v_Parcela, s_Parcela)
        VALUES (?, ?, NULL, 100, ?, ?, ?, ?, 0)"; // Situação: Emitida
	
		$add = odbc_prepare($db, $sql);

		$p = 1; // Inicializa o número da parcela
		$dias = 30; // Inicializa o intervalo de dias para vencimento
	
		for ($i = 0; $i < $_POST['n_Parcelas']; $i++) {
			$params = [
				$_REQUEST['idInform'],
				$_REQUEST['n_Endosso'],
				$p,
				($_POST['d_Emissao'] != '' ? Convert_Data_Geral($_POST['d_Emissao']) : null),
				addDia($dataVenc, $dias),
				$valorParcela
			];

			odbc_execute($add, $params);
			$p++;
			$dias += 30;
		}

		// reload de página
	




		?>
			<script>
				window.location = '<?php echo $root; ?>role/searchClient/ListClient.php?comm=cadastro_endosso&idInform=<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>'; </script>
		<?php
	} else if ($op == 3) {
		// Consulta para deletar de Inform_Endosso
		$sql1 = "DELETE FROM Inform_Endosso WHERE n_Endosso = ? AND i_Inform = ?";
		$rem1 = odbc_prepare($db, $sql1);
		odbc_execute($rem1, [$_REQUEST['n_Endosso'], $_REQUEST['idInform']]);

		// Consulta para deletar de Parcela
		$sql2 = "DELETE FROM Parcela WHERE n_Endosso = ? AND i_Inform = ?";
		$rem2 = odbc_prepare($db, $sql2);
		odbc_execute($rem2, [$_REQUEST['n_Endosso'], $_REQUEST['idInform']]);

		?>
				<script>
					window.location = '<?php echo $root; ?>role/searchClient/ListClient.php?comm=cadastro_endosso&idInform=<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>';
				</script>
		<?php
	}

	$sql = "SELECT
            Endosso.n_Endosso AS NumEndosso,
            TipoEndosso.Descricao_Item AS TipoEndosso,
            Endosso.d_Emissao AS DataEmissao,
            SituacaoEndosso.Descricao_Item AS SituacaoEndosso
        FROM
            Inform_Endosso Endosso
        INNER JOIN Campo_Item AS TipoEndosso ON
            TipoEndosso.i_Campo = 400
            AND TipoEndosso.i_Item = Endosso.t_Endosso
        INNER JOIN Campo_Item AS SituacaoEndosso ON
            SituacaoEndosso.i_Campo = 410
            AND SituacaoEndosso.i_Item = Endosso.s_Endosso
        WHERE
            Endosso.i_Inform = ?
        ORDER BY
            Endosso.n_Endosso";

	$res = odbc_prepare($db, $sql);
	odbc_execute($res, [$_REQUEST['idInform']]);

	?>
	<script>

		function checkDecimals(fieldName, fieldValue) {

			if (fieldValue == "") {
				verErro("Preenchimento obrigatório.");
				fieldName.select();
				fieldName.focus();
			} else {
				err = false;
				dec = ",";
				mil = ".";
				v = "";
				c = "";
				len = fieldValue.length;
				for (i = 0; i < len; i++) {
					c = fieldValue.substring(i, i + 1);
					if (c == dec) { break; }
					if (c != mil) {
						if (isNaN(c)) {
							err = true;
							verErro("Este não é um número válido.");
							fieldName.select();
							fieldName.focus();
							break;
						} else {
							v += c;
						}
					}
				}
				if (!err) {
					if (i == len) {
						v += "00";
					} else {
						if (c == dec) i++;
						if (i == len) {
							v += "00";
						} else {
							c = fieldValue.substring(i, i + 1);
							if (isNaN(c)) {
								verErro("Este não é um número válido.");
								fieldName.select();
								fieldName.focus();
								err = true;
							} else {
								v += c;
							}
						}
						i++;
						if (!err && i == len) {
							v += "0";
						} else {
							c = fieldValue.substring(i, i + 1);
							if (isNaN(c)) {
								verErro("Este não é um número válido.");
								fieldName.select();
								fieldName.focus();
								err = true;
							} else {
								v += c;
							}
						}
					}
					fieldValue = "," + v.substring(v.length - 2, v.length);
					v = v.substring(0, v.length - 2);
					while (v.length > 0) {
						t = v.substring(v.length >= 3 ? v.length - 3 : 0, v.length);
						v = v.substring(0, v.length >= 3 ? v.length - 3 : 0);
						fieldValue = (v.length > 0 ? "." : "") + t + fieldValue;
					}
					fieldName.value = fieldValue;
				}
			}
		}
		function numeros() {
			tecla = event.keyCode;
			if ((tecla >= 48 && tecla <= 57) || (tecla == 44 || tecla == 46)) {
				return true;
			} else {
				verErro('Este campo s&oacute; aceita n&uacute;meros.');
				return false;
			}
		}

		function tiraponto(campo) {
			var str = document.getElementById(campo).value;
			document.getElementById(campo).value = str.replace('.', ',');
		}

		function mascara(o, f) {
			v_obj = o
			v_fun = f
			setTimeout("execmascara()", 1)
		}

		function execmascara() {
			v_obj.value = v_fun(v_obj.value)
		}

		function data(v) {
			v = v.replace(/\D/g, "")
			v = v.replace(/(\d{2})(\d)/, "$1/$2")
			v = v.replace(/(\d{2})(\d)/, "$1/$2")
			return v
		}

		function trocaEndosso() {
			if ($('#t_endosso').val() > 0) {
				$('#AddEndosso').show();

				if ($('#t_endosso').val() == 1 || $('#t_endosso').val() == 2 || $('#t_endosso').val() == 3 || $('#t_endosso').val() == 4 || $('#t_endosso').val() == 7 || $('#t_endosso').val() == 8 || $('#t_endosso').val() == 9) {
					$('#cp4').hide();
					$('#cp5').hide();

				} else {
					$('#cp4').show();
					$('#cp5').show();
				}

				if ($('#t_endosso').val() == 3 || $('#t_endosso').val() == 4 || $('#t_endosso').val() == 5) {
					$('#cp2').hide();
					$('#cp3').hide();

				} else {
					$('#cp2').show();
					$('#cp3').show();
				}


			} else {
				$('#AddEndosso').hide();
			}

		}



		function validaRemover(NumEndosso, idInform) {
			if (confirm('Deseja remover este item definitivamente?')) {
				window.location = 'ListClient.php?comm=cadastro_endosso&idInform=' + idInform + '&NumEndosso=' + NumEndosso + '&operacao=3';
			} else {
				return false;
			}

		}

		function validafrm() {
			if ($('#d_Emissao').val() == "") {
				verErro("Por favor informe a data de Emiss&atilde;o");
				return false;

			} else {

				if ($('#t_endosso').val() == 1 || $('#t_endosso').val() == 2 || $('#t_endosso').val() == 3 || $('#t_endosso').val() == 4 || $('#t_endosso').val() == 7 || $('#t_endosso').val() == 8 || $('#t_endosso').val() == 9) {
					// segue				
				} else {
					if ($('#d_Vigencia_Inicial').val().length == 0 || $('#d_Vigencia_Final').val().length == 0) {
						verErro("Por favor informe per&iacute;odo de vig&ecirc;ncia.");
						return false;
					}
				}

				if ($('#t_endosso').val() == 3 || $('#t_endosso').val() == 4 || $('#t_endosso').val() == 5) {
					// segue
				} else {
					if (parseInt($('#n_Parcelas').val()) == 0 || parseFloat($('#v_Premio').val()) == 0) {
						verErro("Por favor informe o n&uacute;mero de parcelas e o valor do Endosso.");
						return false;
					}

				}

			}

			return true;


		}
	</script>
	<table summary="Submitted table designs" id="example">
		<thead>
			<tr>
				<th width="15%">N&uacute;mero Endosso</th>
				<th width="35%">Tipo Endosso</th>
				<th width="20%">Data Emiss&atilde;o</th>
				<th width="20%">Situa&ccedil;&atilde;o</th>
				<th width="10%" colspan="2">Op&ccedil;&atilde;o</th>
			</tr>
		</thead>

		<tbody>
			<?php
			$i = 0;
			while (odbc_fetch_row($res)) { ?>
				<tr>
					<td><?php echo odbc_result($res, 'NumEndosso'); ?></td>
					<td><?php echo odbc_result($res, 'TipoEndosso'); ?></td>
					<td><?php echo Convert_Data_Geral(substr(odbc_result($res, 'DataEmissao'), 0, 10)); ?></td>
					<td><?php echo odbc_result($res, 'SituacaoEndosso'); ?></td>
					<td colspan="2"><a
							href="ListClient.php?comm=cadastro_endosso_detalhe&idInform=<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>&NumEndosso=<?php echo odbc_result($res, 'NumEndosso'); ?>"
							style="color:#81C8E2">Ver Detalhes</a></td>
					<!--			<td><a href="#" onClick="ListClient.php?comm=cadastro_endosso&idInform=<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>&NumEndosso=<?php echo odbc_result($res, 'NumEndosso'); ?>"><img src="<?php echo $root; ?>images/icone_editar.png" title="Editar Registro" width="24" height="24" class="iconetabela" /></a></td>
			<td><a href="#" onClick=" validaRemover(<?php echo odbc_result($res, 'NumEndosso'); ?>,<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>);"><img src="<?php echo $root; ?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a></td>
-->
				</tr>
				<?php
				$i++;
			} ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="6"></th>
			</tr>
		</tfoot>
	</table>

	<?php
	$numEndo = isset($_REQUEST['NumEndosso']) ? $_REQUEST['NumEndosso'] : 0;
	?>

	<form action="<?php echo $root; ?>role/searchClient/ListClient.php" name="frmendosso" method="post"
		style="width:100%">
		<input type="hidden" name="comm" value="cadastro_endosso" />
		<input type="hidden" id="idInform" name="idInform" value="<?php echo $idInform; ?>">
		<input type="hidden" id="NumEndosso" name="NumEndosso" value="<?php echo htmlspecialchars($numEndo, ENT_QUOTES, 'UTF-8'); ?>" />
		<input type="hidden" name="operacao" value="" />

		<div style="clear:both">&nbsp;</div>
		<ul>

			<li class="campo3colunas">
				<label>Tipos de Endossos</label>
				<?php
				odbc_free_result($res);
				$sql = "SELECT 
					TipoEndosso.i_Item,
					TipoEndosso.Descricao_Item AS TipoEndosso
				FROM 
					Campo_Item TipoEndosso
				WHERE 
					TipoEndosso.i_Campo = 400
					AND TipoEndosso.Situacao = 0
				ORDER BY 
					TipoEndosso.Descricao_Item";

				$re = odbc_prepare($db, $sql);
				odbc_execute($re);

				?>
				<select name="t_endosso" id="t_endosso" onChange="trocaEndosso(this.value);">
					<option value="">Selecione..</option>
					<?php while (odbc_fetch_row($re)) { ?>
						<option value="<?php echo odbc_result($re, 'i_Item'); ?>">
							<?php echo odbc_result($re, 'TipoEndosso'); ?>
						</option>
					<?php } ?>
				</select>
			</li>
		</ul>

		<div id="AddEndosso" style="display:<?php echo ($numEndo > 0 ? 'block' : 'none'); ?>; width:100%">
			<ul>
				<li class="campo3colunas" id="cp1"><label>Data de Emiss&atilde;o</label><input type="text"
						name="d_Emissao" id="d_Emissao" value="" maxlength="10" onKeyUp="mascara(this,data);"></li>

				<li class="campo3colunas" id="cp2"><label>Num. Parcelas</label><input type="text" name="n_Parcelas"
						id="n_Parcelas" value="" maxlength="20" onKeypress="return numeros();"></li>

				<li class="campo3colunas" id="cp3"><label>Valor do Endosso</label><input type="text" name="v_Premio"
						id="v_Premio" value="0" maxlength="20" onKeypress="return numeros();"
						onBlur="tiraponto('v_Premio'); checkDecimals(this, this.value);"></li>

				<li class="campo3colunas" id="cp4"><label>Data de In&iacute;cio de Vig&ecirc;ncia</label><input
						type="text" name="d_Vigencia_Inicial" id="d_Vigencia_Inicial" value="" maxlength="10"
						onKeyUp="mascara(this,data);"></li>

				<li class="campo3colunas" id="cp5"><label>Data de Fim de Vig&ecirc;ncia</label><input type="text"
						name="d_Vigencia_Final" id="d_Vigencia_Final" value="" maxlength="10"
						onKeyUp="mascara(this,data);"></li>
				<br />
				<li class="campo3colunas" id="cp6" style="width:100%"><label>Descri&ccedil;&atilde;o</label><textarea
						name="Descricao" id="Descricao" rows="6" cols="80"></textarea>
				</li>
			</ul>
			<br clear="all">
			<br clear="all">
			<button name="voltar" type="button"
				onClick="document.frmendosso.operacao.value = 1; if(validafrm()) document.frmendosso.submit();"
				class="botaoagg">GERAR ENDOSSO</button>
		</div>
	</form>
	<div class="barrabotoes">
		<button name="voltar"
			onClick="window.location = 'ListClient.php?comm=view&idInform=<?php echo htmlspecialchars($_REQUEST['idInform'], ENT_QUOTES, 'UTF-8'); ?>';"
			class="botaovgm">Voltar</button>

	</div>
</div>