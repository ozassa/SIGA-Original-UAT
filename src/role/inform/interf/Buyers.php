<?php

// alterado em 07/04/2004
// alterado em 08/04/2004
// alterado em 14/04/2004
// Alterado Hicom (Gustavo) 02/12/2004 - inclusão do campo divulgaNome em Importer
// Alterado Hicom (Gustavo) 10/12/2004 - inclusão do campo emailContato em Importer



$idInform = $_REQUEST['idInform'];
$idNotification = $_REQUEST['idNotification'];
$volta = $_REQUEST['volta'];
$hc_cliente = $_REQUEST['hc_cliente'];
$tipo_apolice = $_REQUEST['tipo_apolice'];
$idBuy = isset($_REQUEST['idBuy']) ? $_REQUEST['idBuy'] : 0;
$renov = esta_em_renovacao($idInform);


?>
<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js"></script>


<script>
	//******************************************************************************************
	//Development by Julio Cezar da Silva (julio_cs@terra.com.br/julio@southern.com.br)
	//http://planeta.terra.com.br/informatica/iportal
	//******************************************************************************************
	function caracteres_invalidos() {
		var strinvalido
		strinvalido = '!"#$%&\\\\()*+,-./:;<=>?@'
		strinvalido += 'àèìòùâêîôûäëïöüáéíóúãõÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÁÉÍÓÚÃÕ'
		strinvalido += '[\\\\]^_`'
		strinvalido += '{|}~'
		strinvalido += "'"
		return strinvalido
	}
	//Funcao que faz a verificacao do campo


	function validacaoEmail(dado) {
		var valido;
		var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		if (filter.test(dado))
			return true;
		else {
			return false;
		}

	}

	function isNumeric(campo, sText) {
		// caso queira utilizar a virgula como separador decimal coloque nesta variável
		var ValidChars = "0123456789.,";
		var IsNumber = true;
		var Char;

		for (i = 0; i < sText.length && IsNumber == true; i++) {
			Char = sText.charAt(i);
			if (ValidChars.indexOf(Char) == -1) {
				verErro("Este campo aceita somente valores num&eacute;ricos.");
				campo.value = '';
				campo.focus();
				IsNumber = false;
			}
		}
		return IsNumber;
	}



	function ApenasNum(e) {
		navegador = /msie/i.test(navigator.userAgent);
		if (navegador)
			var tecla = event.keyCode;
		else
			var tecla = e.which;

		if (tecla > 47 && tecla < 58) // numeros de 0 a 9
			return true;
		else {
			if (tecla != 8) // backspace
				return false;
			else
				return true;
		}
	}




	//valida telefone
	function ValidaTelefone(tel) {
		if (tel.length > 0) {
			exp = /\(\d{2}\)\ \d{4}\-\d{4}/
			if (!exp.test(tel.value))
				verErro('Numero de Telefone Inv&aacute;lido!');
		}
	}
	//adiciona mascara ao telefone
	function MascaraTelefone(tel) {
		if (mascaraInteiro(tel) == false) {
			event.returnValue = false;
		}
		return formataCampo(tel, '(00) 0000-0000', event);
	}


	//valida numero inteiro com mascara
	function mascaraInteiro() {
		if (event.keyCode < 48 || event.keyCode > 57) {
			event.returnValue = false;
			return false;
		}
		return true;
	}

	function MascaraCep(cep) {
		if (mascaraInteiro(cep) == false) {
			event.returnValue = false;
		}
		return formataCampo(cep, '00000-000', event);
	}

	function formataCampo(campo, Mascara, evento) {
		var boleanoMascara;

		var Digitato = evento.keyCode;
		exp = /\-|\.|\/|\(|\)| /g
		campoSoNumeros = campo.value.toString().replace(exp, "");

		var posicaoCampo = 0;
		var NovoValorCampo = "";
		var TamanhoMascara = campoSoNumeros.length;;

		if (Digitato != 8) { // backspace 
			for (i = 0; i <= TamanhoMascara; i++) {
				boleanoMascara = ((Mascara.charAt(i) == "-") || (Mascara.charAt(i) == ".")
					|| (Mascara.charAt(i) == "/"))
				boleanoMascara = boleanoMascara || ((Mascara.charAt(i) == "(")
					|| (Mascara.charAt(i) == ")") || (Mascara.charAt(i) == " "))
				if (boleanoMascara) {
					NovoValorCampo += Mascara.charAt(i);
					TamanhoMascara++;
				} else {
					NovoValorCampo += campoSoNumeros.charAt(posicaoCampo);
					posicaoCampo++;
				}
			}
			campo.value = NovoValorCampo;
			return true;
		} else {
			return true;
		}
	}

	function ValidaCep(cep) {
		exp = /\d{5}\-\d{3}/
		if (cep.length > 0) {
			if (!exp.test(cep.value))
				verErro('Numero de Cep Inv&aacute;lido!');
		}
	}

	function apenasNumeros(evento) {
		var tecla = (window.event) ? event.keyCode : e.which;
		if ((tecla > 47 && tecla < 58))
			return true;
		else {
			if (tecla == 8 || tecla == 0)
				return true;
			else
				return false;
		}

	}


	function verifica_name(form) {
		var strlogin = form.name.value; //Recebe o valor do campo
		var caracteres = caracteres_invalidos(); //recebe a string com caracters invalidos
		var result = true;
		for (i = 0; i < caracteres.length; i++) //loop executado de 0 ao numero total de caracters invalidos
		{
			if (strlogin.indexOf(caracteres.charAt(i)) != -1) //verifica se o value do campo strlogin contem alguma caracter invalido
			{
				var strerror = caracteres.substring(i, i + 1); //recebe o caracter errado
				var result = false;
				window.verErro("Voc&ecirc; digitou o seguinte caracter invalido:" + "  " + strerror + "  "); //alerta mostrando o caracter digitado errado
				//window.verErro("Você digitou nome do importador com caracter invalido"); //alerta mostrando o caracter digitado errado
				break; //interrompe o loop
			}
		}
		if (result) {
			return true;
		} else {
			return false;
		}
	}

	function verifica(nome) {
		var strlogin = nome; //Recebe o valor do campo
		var caracteres = caracteres_invalidos(); //recebe a string com caracters invalidos
		var result = true;
		for (i = 0; i < caracteres.length; i++) //loop executado de 0 ao numero total de caracters invalidos
		{
			if (strlogin.indexOf(caracteres.charAt(i)) != -1) //verifica se o value do campo strlogin contem alguma caracter invalido
			{
				var strerror = caracteres.substring(i, i + 1); //recebe o caracter errado
				var result = false;
				//window.verErro("Você digitou o seguinte caracter invalido:" + "  " + strerror + "  "); //alerta mostrando o caracter digitado errado
				//window.verErro("Você digitou nome do importador com caracter invalido"); //alerta mostrando o caracter digitado errado
				break; //interrompe o loop
			}
		}
		if (result) {
			return false;
		} else {
			return true;
		}
	}

	function checkDecimals(fieldName, fieldValue) {

		if (fieldValue != "") {

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
						verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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
							verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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
							verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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

	function consist(form) {
		// alert(form.idCountry.value);
		if (form.i_Produto.value == 1) {


			if (form.emailContato.value == "" || form.emailContato.value.length < 4) {
				verErro("Favor preencher corretamente o campo E-mail de contato do comprador");
				form.cnpj.focus();
				return false;
			} else if (form.cnpj.value == "" || form.cnpj.value.length < 0) {
				verErro("Favor preencher corretamente o campo Registro fiscal do comprador");
				form.cnpj.focus();
				return false;
			}
		}

		//alert(form.name.value.indexOf("\'"));

		if (form.name.value == "") {
			verErro("Informe a Raz&atilde;o Social");
			form.name.focus();
			return false;
		} else if (form.address.value == "") {
			verErro("Informe o Endere&ccedil;o");
			form.address.focus();
			return false;
		} else if (form.city.value == "") {
			verErro("Informe a Cidade");
			form.city.focus();
			return false;

		} else if (form.idCountry.value == "0" || form.idCountry.value == "Selecione...") {
			verErro("Informe o Pa&iacute;s");
			form.idCountry.focus();
			return false;

		} else if (form.cep.value == "") {
			verErro("Favor preencher  corretamente o campo CEP");
			form.cep.focus();
			return false;
		} else if (form.tel.value == "") {
			verErro("Informe o Telefone");
			form.tel.focus();
			return false;
		} else if (form.fax.value == "") {
			verErro("Informe o FAX");
			form.fax.focus();
			return false;
		} else if (form.contact.value == "") {
			verErro("Informe o Contato");
			form.contact.focus();
			return false();

		} else if (form.relation.value == "") {
			verErro("Informe a Rela&ccedil;&atilde;o Comercial");
			form.relation.focus();
			return false;
		} else if (form.prevExp12.value == "") {
			verErro("Informe a Previs&atilde;o de Exporta&ccedil;&atilde;o");
			form.prevExp12.focus();
			return false;
		} else if (form.numShip12.value == "") {
			verErro("Informe o N&uacute;mero de Embarques");
			form.numShip12.focus();
			return false;
		}
		else if (form.periodicity.value == "") {
			verErro("Informe a Periodicidade");
			form.periodicity.focus();
			return false;
		}
		else if (form.przPag.value == "") {
			verErro("Informe o Prazo de Pagamento");
			form.przPag.focus();
			return false;
		} else {
			<?php if ($idBuy > 0 && $comm == "altBuy") { ?>
				form.comm.value = 'setAltBuy';
			<?php } else { ?>
				form.comm.value = 'insBuy';
			<?php } ?>

			<?php if ($renov) { ?>
				if ((form.includeOld[0].checked && confirm('Confirma inclusão de comprador na apólice vigente e no estudo da renovação')) ||
					form.includeOld[1].checked)
				<?php } ?>

			//alterado em 08/04/2004
			if (form.comm.value == 'setAltBuy') {
				if ((confirm("Confirmar alteração de dados?"))) {
					return true;
				}
			}
			else {
				return true;
			}

		}
		return true;
	}



	var hc_receber = true;

	function testaLimite() {

		var hc_prevExp12 = 0;
		var hc_numShip12 = 0;
		var hc_periodicity = 0;
		var hc_przPag = 0;
		var hc_limCredit = 0;
		var hc_limCreditCalc = 0;

		return;

		hc_prevExp12 = document.altform.prevExp12.value;
		hc_numShip12 = document.altform.numShip12.value;
		hc_periodicity = document.altform.periodicity.value;
		hc_przPag = document.altform.przPag.value;
		hc_limCredit = document.altform.limCredit.value;

		//verErro("a");

		if (hc_prevExp12 == 0 || hc_numShip12 == 0 || hc_periodicity == 0 || hc_przPag == 0) {
			return true;
		}
		else {
			// calcula formula
			hc_limCreditCalc = parseInt((hc_prevExp12 / hc_numShip12) * ((hc_przPag / hc_periodicity) + 1));
			if (hc_limCreditCalc > hc_prevExp12) {
				hc_limCreditCalc = hc_prevExp12;
			}
			if (hc_limCredit != hc_limCreditCalc) {
				if (hc_limCredit != "") {

					if (hc_receber) {


						if (confirm("O Limite de cr&eacute;dito sugerido pelo sistema &eacute;: US$" + hc_limCreditCalc + " mil. Deseja substituir o informado pelo sugerido? ")) {
							document.altform.limCredit.value = hc_limCreditCalc;
						}
						else {
							if (!confirm("Deseja receber este tipo de informa&ccedil;&atilde;o durante o processo?")) {
								hc_receber = false;
							}
						}

					}
				}
			}

		}

	}


	function formatar(src, mask, e) {
		var tecla = (window.event) ? event.keyCode : e.which;
		if ((tecla > 57 || tecla < 47)) {
			return false;
		}
		var i = src.value.length;
		var saida = mask.substring(0, 1);
		var texto = mask.substring(i)
		if (texto.substring(0, 1) != saida) {
			src.value += texto.substring(0, 1);
		}
	}


	function validadorCnpj(cnpj) {
		//alert (cnpj.value);	
		var i = 0;
		var l = 0;
		var strNum = "";
		var strMul = "6543298765432";
		var character = "";
		var iValido = 1;
		var iSoma = 0;
		var strNum_base = "";
		var iLenNum_base = 0;
		var iLenMul = 0;
		var iSoma = 0;
		var strNum_base = 0;
		var iLenNum_base = 0;

		strNum = cnpj.value;
		strNum = strNum.replace('.', '');
		strNum = strNum.replace('.', '');
		strNum = strNum.replace('/', '');
		strNum = strNum.replace('-', '');

		//alert(strNum);
		if (strNum.length > 0) {

			if (strNum.length != 14) {
				verErro(' CNPJ deve conter 14 caracteres.');
				cnpj.value = '';
				cnpj.focus();
				return false;
			} else if (strNum == '00000000000000') {
				verErro(' CNPJ inv&aacute;lido.');
				//verErro("CNPJ inv&aacute;lido.");
				cnpj.value = '';
				cnpj.focus();
				return false;
			}


			strNum_base = strNum.substring(0, 12);
			iLenNum_base = strNum_base.length - 1;
			iLenMul = strMul.length - 1;
			for (i = 0; i < 12; i++)
				iSoma = iSoma +
					parseInt(strNum_base.substring((iLenNum_base - i), (iLenNum_base - i) + 1), 10) *
					parseInt(strMul.substring((iLenMul - i), (iLenMul - i) + 1), 10);

			iSoma = 11 - (iSoma - Math.floor(iSoma / 11) * 11);
			if (iSoma == 11 || iSoma == 10)
				iSoma = 0;

			strNum_base = strNum_base + iSoma;
			iSoma = 0;
			iLenNum_base = strNum_base.length - 1
			for (i = 0; i < 13; i++)
				iSoma = iSoma +
					parseInt(strNum_base.substring((iLenNum_base - i), (iLenNum_base - i) + 1), 10) *
					parseInt(strMul.substring((iLenMul - i), (iLenMul - i) + 1), 10)

			iSoma = 11 - (iSoma - Math.floor(iSoma / 11) * 11);
			if (iSoma == 11 || iSoma == 10)
				iSoma = 0;
			strNum_base = strNum_base + iSoma;
			if (strNum != strNum_base) {
				verErro(' CNPJ inv&aacute;lido.');
				cnpj.value = '';
				cnpj.focus();
				return false;
			}
		}

		return true;

	}

</script>

<?php

include_once('../../../navegacao.php');

$query = "SELECT idAnt, state, currency, name, i_Produto FROM Inform WHERE id = ?";
$c = odbc_prepare($db, $query);
odbc_execute($c, [$idInform]);

$idAnt = odbc_result($c, 1);
$informState = odbc_result($c, 2);
$i_Produto = odbc_result($c, 5);

//print $i_Produto;

/*
  Alterador por Tiago V N - Elumini - 13/07/2006
*/
$currency = odbc_result($c, "currency");

odbc_free_result($c);

if ($currency == "1") {
	$moeda = "R\$";
} elseif ($currency == "6") {
	$moeda = "&euro;";
} elseif ($currency == "2") {
	$moeda = "US\$";
}

$pode_incluir = false;
if ($idAnt) { // verifica se é renovacao de um informe q ainda esta em vigor
	$idVigente = $idAnt;
	$pergunta = "Incluir importador na apólice vigente?";
} else { // verifica se este informe tem uma renovacao ativa
	$query = "SELECT state FROM Inform WHERE idAnt = ?";
	$x = odbc_prepare($db, $query);
	odbc_execute($x, [$idInform]);

	$stateOther = odbc_result($x, 1);
	if ($stateOther >= 1 || $stateOther <= 6) {
		$pode_incluir = true; // significa q pode incluir no outro informe
	}
	$idVigente = $idInform;
	$pergunta = '';//"Incluir importador na apólice de renovação?";
	odbc_free_result($x);
}
?>
<div class="conteudopagina">
	<FORM action="<?php echo $root; ?>role/inform/Inform.php" method="post" name="altform">

		<input type="hidden" name="comm" value="buySubmit">
		<input type="hidden" name="idBuy" value="<?php echo htmlspecialchars($idBuy, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="i_Produto" id="i_Produto"
			value="<?php echo htmlspecialchars($i_Produto, ENT_QUOTES, 'UTF-8'); ?>" />
		<input type="hidden" name="risk" value="1">
		<input type="hidden" name="idVigente" value="<?php echo htmlspecialchars($idVigente, ENT_QUOTES, 'UTF-8'); ?>">

		<input type="hidden" name="volta" value="<?php echo htmlspecialchars($volta, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="hc_cliente"
			value="<?php echo htmlspecialchars($hc_cliente, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="tipo_apolice"
			value="<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">


		<?php if (isset($msgInc)) { ?>
			<p><label><?php echo $msgInc; ?></label></p>
		<?php } ?>

		<?php if ($i_Produto == 1) { ?>
			<label>Instru&ccedil;&otilde;es: Indicar at&eacute; 15 clientes para An&aacute;lise Preliminar,
				preferencialmente 5 maiores, 5 m&eacute;dios e 5 menores.<br /> Importante: preeencher endere&ccedil;o
				completo, com telefone, fax, pessoa e e-mail de contato. </label>
		<?php } else { ?>
			<label>Instru&ccedil;&otilde;es: Indicar at&eacute; 15 clientes para An&aacute;lise Preliminar.<br />
				Importante: preencher endere&ccedil;o completo com telefone e fax.</label>
		<?php } ?>


		<div style="clear:both">&nbsp;</div>
		<div style="clear:both">&nbsp;</div>
		<?php

		// Alterado Hicom (Gustavo) 02/12/2004 - inclusão do campo divulgaNome e emailContato em Importer
		$qry = "SELECT imp.name, address, city, c.name, tel, prevExp12, numShip12,
               periodicity, przPag, imp.id, cep, fax, contact, relation, seasonal, 
               ISNULL(imp.divulgaNome, 0) AS divulgaNome, emailContato, cnpj, expMax, limCredit
        FROM Importer imp
        JOIN Country c ON (idCountry = c.id)
        WHERE idInform = ? AND state <> ? AND state <> ?
        AND imp.id NOT IN (SELECT DISTINCT idImporter FROM ImporterRem)
        ORDER BY imp.id";

		$cur = odbc_prepare($db, $qry);
		odbc_execute($cur, [$idInform, '7', 9]);

		$i = $soma = 0;
		?>
		<table class="tabela01" summary="Submitted table designs" id="example">
			<thead>
				<tr>
					<th scope="col" colspan="1">&nbsp;</th>
					<th scope="col" colspan="3">Buyers Inclu&iacute;dos</th>
					<th scope="col" colspan="1">Op&ccedil;&otilde;es</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$qt = 1;
				while (odbc_fetch_row($cur)) {

					if ($comm != "altBuy" && odbc_result($cur, 10) == $idBuy) {
						//$soma += ($prevExp12 * 1000);
						//echo "somando: comm=$comm, prev=$prevExp12, soma=$soma<br>";
					} else {
						$soma += odbc_result($cur, 6);
					}

					if ($i % 2 == 0) {
						$cor = 'class="odd"';
					} else {
						$cor = '';
					}

					$i++;

					?>
					<tr <?php echo $cor; ?>>
						<td><?php echo $qt; ?></td>
						<td colspan="3">
							<ul>
								<li class="campo2colunas">
									<label>Raz&atilde;o Social:</label>
									<a name="<?php echo odbc_result($cur, 10); ?>"></a>
									<?php echo (odbc_result($cur, 1)); ?>
								</li>
							</ul>
							<div style="clear:both">&nbsp;</div>
							<ul>
								<li class="campo3colunas">
									<label><?php echo ($i_Produto == 1 ? 'CNPJ' : 'Registro fiscal'); ?>:</label>
									<?php echo odbc_result($cur, "cnpj"); ?>
								</li>
								<li class="campo2colunas">
									<label>Endere&ccedil;o:</label>
									<?php echo (odbc_result($cur, 2)); ?>
								</li>
								<li class="campo3colunas">
									<label>Cidade:</label>
									<?php echo (odbc_result($cur, 3)); ?>
								</li>
								<li class="campo3colunas">
									<label>Pa&iacute;s:</label>
									<?php echo (odbc_result($cur, 4)); ?>
								</li>
								<li class="campo3colunas">
									<label>CEP:</label>
									<?php echo odbc_result($cur, 11); ?>
								</li>
								<li class="campo3colunas">
									<label>Tel:</label>
									<?php echo odbc_result($cur, 5); ?>
								</li>
								<li class="campo3colunas">
									<label>FAX:</label>
									<?php echo odbc_result($cur, 12); ?>
								</li>
								<li class="campo3colunas">
									<label>Contato:</label>
									<?php echo odbc_result($cur, 13); ?>
								</li>
								<li class="campo3colunas">
									<label>E-mail:</label>
									<?php echo odbc_result($cur, "emailContato"); ?>
								</li>
								<li class="campo3colunas">
									<label>Rela&ccedil;&atilde;o Comercial:</label>
									desde o ano de <?php echo odbc_result($cur, 14); ?>
								</li>
								<li class="campo3colunas">
									<label>Vendas Sazonais:</label>
									<?php echo (odbc_result($cur, 15) ? "Sim" : "Não"); ?>
								</li>
								<li class="campo3colunas">
									<label>Limite de cr&eacute;dito solicitado:</label>
									<?php echo number_format(odbc_result($cur, 'limCredit'), 2, ',', '.'); ?>
								</li>
								<!--<li class="campo3colunas"> 
							<label>Autorizo divulgar o nome do comprador:</label>
							<?php echo ((odbc_result($cur, "divulgaNome") == 1) ? "Sim" : "Não"); ?>
						</li>-->
							</ul>
						</td>
						<td colspan="1" width="90">
							<!--<a href="#"><img src="<?php echo $root; ?>images/icone_editar.png" alt="" title="Alterar Importador" width="30" height="30" class="iconetabela" onClick="this.form.comm.value='altBuy';this.form.idBuy.value=<?php echo odbc_result($cur, 10); ?>;this.form.action += '#alterar';this.form.submit()"/></a>&nbsp;&nbsp;
							<a href="#"><img src="<?php echo $root; ?>images/icone_detalhar.png" alt="" title="Consultar Endere&ccedil;os" width="30" height="30" class="iconetabela" onClick="this.form.comm.value='address';this.form.idBuy.value=<?php echo odbc_result($cur, 10); ?>;this.form.submit()"/></a>&nbsp;&nbsp;
							<?php if ($idAnt) { ?>
							<a href="#"><img src="<?php echo $root; ?>images/icone_deletar.png" alt="" title="Remover este importador" width="30" height="30" class="iconetabela" onClick="if(confirm('Remover o importador <?php echo odbc_result($cur, 1); ?>?')){ this.form.comm.value='remBuy';this.form.idBuy.value=<?php echo odbc_result($cur, 10); ?>;this.form.submit(); }"/></a>
							<?php } ?>
				-->

							<button class="botaovpm" type="button"
								onClick="if(confirm('Remover o importador <?php echo odbc_result($cur, 1); ?>?')){ this.form.comm.value='remBuy';this.form.idBuy.value=<?php echo odbc_result($cur, 10); ?>;this.form.submit(); }">Remover</button>
							<button class="botaoapm" type="button"
								onClick="this.form.comm.value='altBuy';this.form.idBuy.value=<?php echo odbc_result($cur, 10); ?>;this.form.action += '#alterar';this.form.submit()">Alterar</button>
							<?php if ($idAnt) { ?>
								<button class="botaoapm" type="button"
									onClick="this.form.comm.value='address';this.form.idBuy.value=<?php echo odbc_result($cur, 10); ?>;this.form.submit()">Endere&ccedil;os</button>
							<?php } ?>

						</TD>
					</tr>
					<tr>
						<thead>
							<th></th>
							<Th align="right" scope="col">Prev. Vol. Vendas</Th>
							<!--	<Th align="right" scope="col">N&ordm; de Vendas por ano</Th>
					<Th align="right" scope="col">Periodicidade de Vendas (Dias);</Th>-->
							<Th align="right" scope="col">Prazo de Pagto.</Th>
							<Th align="right" scope="col">Exporta&ccedil;&atilde;o M&aacute;xima</Th>
							<Th align="right" scope="col">&nbsp;</Th>
						</thead>
					</tr>
					<tr <?php echo $cor; ?>>
						<td></td>
						<TD align="right"><strong><?php echo number_format(odbc_result($cur, 6), 2, ",", "."); ?></strong>
						</TD>
						<!--<TD align="right"><strong><?php echo odbc_result($cur, 7); ?></strong></TD>
				<TD align="right"><strong><?php echo odbc_result($cur, 8); ?></strong></TD>-->

						<TD align="right"><strong><?php echo odbc_result($cur, 9); ?></strong></TD>
						<TD align="right">
							<strong><?php echo number_format(odbc_result($cur, 'expMax'), 2, ",", "."); ?></strong>
						</TD>
						<TD align="right">&nbsp;</TD>
					</TR>
					<tr class="odd">
						<td colspan="5">&nbsp;</td>
					</tr>
					<?php
					$qt++;

				}
				if ($i == 0) { ?>
					<TR bgcolor="#cccccc">
						<TD align="center" colspan=5>Nenhum buyer cadastrado</TD>
					</TR>
				<?php } ?>
			</tbody>
		</TABLE>
		<?php
		odbc_free_result($cur);

		$query = "SELECT vol2 FROM Volume WHERE idInform = ?";
		$var = odbc_prepare($db, $query);
		odbc_execute($var, [$idInform]);

		$prev = odbc_result($var, 1);
		odbc_free_result($var);
		?>
		<P>&nbsp;</P>
		<P>&nbsp;</P>
		<?php $limCredit = 0;
		$hc_idTwin = "";

		$hc_bloqueia = false;

		//echo $hc_bloqueia;
		
		$nameImporter = "";
		$address = "";
		$idCountry = "";
		$tel = "";
		$prevExp12 = 0;
		$numShip12 = "";
		$periodicity = "";
		$city = "";
		$przPag = "";
		$cep = "";
		$fax = "";
		$contact = "";
		$relation = "";
		$seasonal = "";
		$divulgaNome = "";
		$emailContato = "";
		$cnpj = "";
		$expMax = "";
		$limCredit = "";

		if ($idBuy > 0 && $comm == "altBuy") {
			// Alterado Hicom (Gustavo) - adicionei campo divulgaNome
			$query = "SELECT name, address, idCountry, tel, prevExp12,
                 numShip12, periodicity, city, przPag, cep, fax, contact, relation, seasonal, limCredit, idTwin, state, 
                 ISNULL(divulgaNome, 0) AS divulgaNome, emailContato, cnpj, expMax, limCredit
				FROM Importer 
				WHERE id = ?";

			$rAlter = odbc_prepare($db, $query);
			odbc_execute($rAlter, [$idBuy]);

			if (odbc_fetch_row($rAlter)) {
				$nameImporter = odbc_result($rAlter, 1);
				$address = odbc_result($rAlter, 2);
				$idCountry = odbc_result($rAlter, 3);
				$tel = odbc_result($rAlter, 4);
				$prevExp12 = odbc_result($rAlter, 5);
				$numShip12 = odbc_result($rAlter, 6);
				$periodicity = odbc_result($rAlter, 7);
				$city = odbc_result($rAlter, 8);
				$przPag = odbc_result($rAlter, 9);
				$cep = odbc_result($rAlter, 10);
				$fax = odbc_result($rAlter, 11);
				$contact = odbc_result($rAlter, 12);
				$relation = odbc_result($rAlter, 13);
				$seasonal = odbc_result($rAlter, 14);
				$divulgaNome = odbc_result($rAlter, "divulgaNome");
				$emailContato = odbc_result($rAlter, "emailContato");
				$cnpj = odbc_result($rAlter, "cnpj");
				$expMax = odbc_result($rAlter, "expMax");
				$limCredit = odbc_result($rAlter, "limCredit");

				//echo "AAAAAAAAAAAAAAAAAAAAA<br>";
		
				//$limCredit   = odbc_result($rAlter, 15);
		
				$hc_idTwin = odbc_result($rAlter, 'idTwin');

				//echo "AAAAAAAAAAAAAAAAAAAAA<br>" . $hc_idTwin;
		

				$hc_state = odbc_result($rAlter, 'state');

				odbc_free_result($rAlter);

				if ($hc_state == 3 || $hc_state == 4 || $hc_state == 6) {
					$hc_bloqueia = true;
					//echo "aaaa" . $hc_bloqueia;
				}

			} else {
				$msg = "Problemas na Leitura dos Dados para Alteração";
			}


		}


		// Temos que ver o que bloquear
		// Vamos bloquear se renovação ou seja, tem twin
		//echo "=>" . $hc_idTwin;
		//if ($hc_idTwin != "" && $hc_idTwin != "0")
		if ($hc_idTwin) {
			$hc_bloqueia = true;
			//echo "bbbbbb" . $hc_idTwin;
		}

		//echo $hc_bloqueia;
		?>
		<a name="alterar"></a>
		<?php if ($idAnt) {
			if ($idBuy > 0 && $comm == "altBuy") {
				echo "<label>Alterar dados do buyer</label>";
			}
		} else {
			if ($idBuy > 0 && $comm == "altBuy")
				echo "<label>Alterar dados do buyer</label>";
			else
				echo "<label>Incluir buyers novos</label>";
		}

		?>
		<div style="clear:both">&nbsp;</div>
		<ul>
			<li class="campo2colunas">
				<label>Raz&atilde;o Social Completa *</label>
				<?php //alterado HIcom 14/04/2004
				if (isset($erro)) {
					if ($erro == 'true') {
						$name = $name_erro;
					}
				}

				//--------------------------
				?>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($nameImporter) != "") { ?>
					<?php echo $nameImporter; ?>
					<input type="hidden" size="50" maxlength="150" name="name" value="<?php echo $nameImporter; ?>">
				<?php } else { ?>
					<input type="text" size="50" maxlength="150" name="name" value="<?php echo $nameImporter; ?>">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label><?php echo ($i_Produto == 1 ? 'CNPJ *' : 'Registro fiscal'); ?></label>
				<?php if ($i_Produto == 1) { ?>
					<!--<input type="text"   maxlength="18" name="cnpj"  id="cnpj" value="<?php echo $cnpj; ?>" onkeypress="return formatar(this,'##.###.###/####-##',event);" onblur="validadorCnpj(this);">-->
					<input type="text" maxlength="30" name="cnpj" id="cnpj" value="<?php echo $cnpj; ?>">
				<?php } else { ?>
					<input type="text" maxlength="30" name="cnpj" id="cnpj" value="<?php echo $cnpj; ?>">
				<?php } ?>

			</li>
			<li class="campo2colunas">
				<label>Endere&ccedil;o *</label>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($address) != "") { ?>
					<?php echo $address; ?><input type="hidden" size="50" maxlength="150" name="address"
						value="<?php echo $address; ?>">
				<?php } else { ?>
					<input type="text" size="50" maxlength="150" name="address" value="<?php echo $address; ?>">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label>Cidade *</label>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($city) != "") { ?>
					<?php echo $city; ?><input type="hidden" size="30" maxlength="100" name="city"
						value="<?php echo $city; ?>">
				<?php } else { ?>
					<input type="text" size="30" maxlength="100" name="city" value="<?php echo $city; ?>">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label>Pa&iacute;s *</label>
				<?php $hc_ant_disabled = isset($disabled) ? $disabled : "";
				if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia) {
					$disabled = true;

				} else {
					$disabled = false;
				}
				?>

				<?php // Monta a lista de países
				$sql = "SELECT id, name FROM Country ORDER BY name";
				$sel = $idCountry;
				$name = "idCountry";
				$empty = ("Selecione...");
				require("../../interf/Select.php");

				if ($disabled) {
					?>
					<input type="hidden" name="idCountry" value="<?php echo $sel; ?>">
				<?php }

				$disabled = $hc_ant_disabled;

				?>
			</li>
			<li class="campo2colunas">
				<label>CEP *</label>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($cep) != "") { ?>
					<?php echo $cep ?><input type="hidden" size="30" maxlength="9" name="cep" value="<?php echo $cep; ?>">
				<?php } else { ?>
					<!--<input type="text"  size="30" name="cep" maxlength="9" value="<?php echo $cep; ?>" onKeyPress="MascaraCep(this);" onBlur="ValidaCep(this)">-->
					<input type="text" size="30" name="cep" maxlength="9" value="<?php echo $cep; ?>">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label>Telefone *</label>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($tel) != "") { ?>
					<?php echo $tel ?><input type="hidden" size="20" maxlength="50" name="tel" value="<?php echo $tel; ?>">
				<?php } else { ?>
					<!--<input type="text"  size="20" maxlength="14" name="tel" value="<?php echo $tel; ?>" onKeyPress="MascaraTelefone(this)" onBlur="ValidaTelefone(this)">-->
					<input type="text" size="20" maxlength="50" name="tel" value="<?php echo $tel; ?>">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label>FAX *</label>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($fax) != "") { ?>
					<?php echo $fax; ?><input type="hidden" size="20" maxlength="50" name="fax" value="<?php echo $fax; ?>">
				<?php } else { ?>
					<!--<input type="text"  size="20" maxlength="14" name="fax" value="<?php echo $fax; ?>" onKeyPress="MascaraTelefone(this)" onBlur="ValidaTelefone(this)">-->
					<input type="text" size="20" maxlength="50" name="fax" value="<?php echo $fax; ?>">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label>Contato *</label>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($contact) != "") { ?>
					<?php echo $contact; ?><input type="hidden" maxlength="140" name="contact"
						value="<?php echo $contact; ?>">
				<?php } else { ?>
					<input type="text" maxlength="140" name="contact" value="<?php echo $contact; ?>">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label>E-mail contato <?php echo ($i_Produto == 1 ? '*' : ''); ?></label>
				<?php if ($idBuy > 0 && $comm == "altBuy" && $hc_bloqueia && trim($emailContato) != "") { ?>
					<?php echo $emailContato; ?><input type="hidden" maxlength="60" name="emailContato"
						value="<?php echo $emailContato; ?>"
						onblur="if(!validacaoEmail(this.value) && document.getElementById('i_Produto').value == 1){ verErro('O e-mail informado &eacute; inv&aacute;lido!'); this.value = '';}">
				<?php } else { ?>
					<input type="text" maxlength="60" name="emailContato" value="<?php echo $emailContato; ?>"
						onblur="if(!validacaoEmail(this.value) && document.getElementById('i_Produto').value == 1){ verErro('O e-mail informado &eacute; inv&aacute;lido!'); this.value = '';}">
				<?php } ?>
			</li>
			<li class="campo2colunas">
				<label>Vendas Sazonais *</label>
				<?php //---- Hicom alterado em 14/04/2003 ---------------
				if ($seasonal < 1) {
					$seasonal = 0;
				}
				//-------------------------------------------------
				?>
				<div class="formopcao">
					<input type="radio" name="seasonal" value="1" <?php echo $seasonal == "1" ? "checked" : ""; ?>>
				</div>
				<div class="formdescricao">Sim</div>
				<div class="formopcao">
					<input type="radio" name="seasonal" value="0" <?php echo $seasonal == ("0" & "") ? "checked" : ""; ?>>
				</div>
				<div class="formdescricao">N&atilde;o</div>
			</li>
			<li class="campo2colunas">
				<label>Rela&ccedil;&atilde;o Comercial desde o ano de *</label>
				<input type="text" maxlength="4" name="relation" value="<?php echo $relation; ?>"
					onkeypress="return ApenasNum(event);">
			</li>
			<li class="campo2colunas">
				<label>Limite de cr&eacute;dito solicitado </label>
				<input type="text" style="text-align:right" maxlength="15" name="limCredit" id="limCredit"
					value="<?php echo ($limCredit ? number_format($limCredit, 2, ',', '.') : 0); ?>"
					onkeypress="return ApenasNum(event);" onBlur="checkDecimals(this, this.value) ; testaLimite();">
			</li>
			<!--<li class="campo2colunas">
			<label>Autorizo divulgar nome ao comprador *</label>    
			   <input type="hidden" name="divulgaNomeOrig" value="<?php echo $divulgaNome; ?>">
			<?php
			if ($idBuy > 0 && $comm == 'altBuy') { //alteração
				if ($divulgaNome == 1) { ?>
							<input type="hidden" name="divulgaNome" value="1"><?php echo ("Sim");
				} else { ?> 
					   <div class="formopcao">
						  <input type="radio" name="divulgaNome" value="1" <?php echo (($divulgaNome == 1) ? "checked" : ""); ?>>
						  </div><div class="formdescricao">Sim</div>
						  <div class="formopcao">
						  <input type="radio" name="divulgaNome" value="0" <?php echo (($divulgaNome == 0) ? "checked" : ""); ?>>
						  </div><div class="formdescricao">N&atilde;o</div>
				  
			<?php }
			} else { // inclusão   ?>
				  <div class="formopcao">
				  <input type="radio" name="divulgaNome" value="1"></div><div class="formdescricao">Sim</div>
				   <div class="formopcao">
				  <input type="radio" name="divulgaNome" value="0" checked ></div><div class="formdescricao">N&atilde;o</div>
			<?php
			}
			?>
		   </li>-->
		</ul>
		<input type="hidden" name="divulgaNomeOrig" value="<?php echo $divulgaNome; ?>">
		<input type="hidden" name="divulgaNome" id="divulgaNome" value="0" />
		<div style="clear:both">&nbsp;</div>
		<table class="tabela01">
			<thead>
				<th scope="col" width="33%">Previs&atilde;o Vol. Vendas (<?php echo $moeda; ?>) pr&oacute;x. 12 meses *
				</th>
				<!--<th scope="col">N&ordm; de Vendas por ano *</th>
			<th scope="col">Periodicidade de Vendas (Dias) *</th>
			-->
				<th scope="col" width="33%">Prazo de Pagamento (Dias) *</th>
				<th scope="col" width="33%">Exportação M&aacute;xima *</th>
			</thead>
			<tr>
				<TD align="left" width="33%"><input style="text-align:right" type="text" maxlength="15" name="prevExp12"
						onBlur="checkDecimals(this, this.value) ; testaLimite();" onkeypress="return ApenasNum(event);"
						value="<?php echo ($comm == 'segVendExp' || $comm == 'insBuy' || $comm == 'setAltBuy' ? number_format($prevExp12, 2, ',', '.') : number_format($prevExp12, 2, ',', '.')); ?>">
				</TD>
				<!-- <TD align="left"><input style="text-align:right"  type="text"   maxlength="4"   name="numShip12"   onBlur="testaLimite();" value="<?php echo $numShip12; ?>" onkeypress="return ApenasNum(event);"></TD>
			<TD align="left"><input style="text-align:right"  type="text"   maxlength="50"  name="periodicity" onBlur="testaLimite();" value="<?php echo $periodicity; ?>" onkeypress="return ApenasNum(event);"></TD>-->
				<input type="hidden" maxlength="4" name="numShip12"
					value="<?php echo ($numShip12 ? $numShip12 : 0); ?>">
				<input type="hidden" maxlength="50" name="periodicity"
					value="<?php echo ($periodicity ? $periodicity : 0); ?>">
				<TD align="left" width="33%"><input style="text-align:right" type="text" maxlength="4" name="przPag"
						onBlur="testaLimite();" value="<?php echo $przPag; ?>" onkeypress="return ApenasNum(event);">
				</TD>
				<TD align="left" width="33%"><input style="text-align:right" type="text" maxlength="15" name="expMax"
						id="expMax" onBlur="checkDecimals(this, this.value) ; testaLimite();"
						onkeypress="return ApenasNum(event);"
						value="<?php echo ($expMax ? number_format($expMax, 2, ',', '.') : 0); ?>"></TD>
			</TR>
		</table>
		<div style="clear:both">&nbsp;</div>
		<ul>
			<?php if ($pode_incluir && $pergunta) { ?>
				<li class="campo2colunas">
					<label><?php echo $pergunta; ?></label>
					<div class="formopcao"><input type="radio" name="includeOld" value="1" checked></div>
					<div class="formdescricao">Sim</div>
					<div class="formopcao"><input type="radio" name="includeOld" value="0"></div>
					<div class="formdescricao">N&atilde;o</div>
				</li>
			</ul>
			<div style="clear:both">&nbsp;</div>
		<?php } ?>
		<input type="hidden" name="soma" value="<?php echo $soma; ?>">
		<input type="hidden" name="prev" value="<?php echo $prev; ?>">
		<?php if ($msg != "") { ?>
			<p>
				<font color="red"><?php echo $msg; ?></font>
			</p>
		<?php } ?>
		<div style="clear:both">&nbsp;</div>
		<label>
			<font <?php $idAnt ? 'color=#000000' : "class=\"verm\""; ?>>
				<?php //alterado em 07/04/2004
				if ($comm != 'altBuy') {
					echo ("Para cadastrar os principais compradores, preencha o formulário e clique em 'Incluir'");
				}
				?>
			</font>
		</label>
		<div style="clear:both">&nbsp;</div>
		<?php if ($idAnt && !$pergunta) { ?>
			<label>
				<font class="verm">Esta solicitação de inclusão será considerada somente para o informe de renovação</font>
			</label>
		<?php } ?>
		<div style="clear:both">&nbsp;</div>
		<div class="barrabotoes">
			<input type="hidden" name="inicial" id="inicial" value="" />
			<button class="botaoagg" type="button"
				onClick="this.form.inicial.value= 1; this.form.comm.value='open'; this.form.submit()">Tela
				Inicial</button>
			<button class="botaovgg" type="button" onClick=" this.form.comm.value='segVendExt'; this.form.submit()">Tela
				Anterior</button>
			<?php //if ($soma <= $prev) {
			?>
			<button class="botaoagm" type="button"
				onClick=" if (consist(this.form)) this.form.submit();"><?php echo (($idBuy > 0 && $comm == 'altBuy') ? 'Alterar' : 'Incluir'); ?></button>
			<?php //}
			if ($i > 0) { ?>
				<button class="botaoagg" type="button" onClick="this.form.submit();">Pr&oacute;xima Tela</button>
			<?php }
			?>
			<button class="botaovgm" name="Reset" type="reset">Limpar</Button>
			&nbsp;&nbsp;&nbsp;<label>Exportar informa&ccedil;&otilde;es para Excel
				<a href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/inform/interf/relatorio_informe_excel.php?inform=<?php echo urlencode($idInform); ?>&pagina=6"
					target="new">
					<img border="0"
						src="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>../images/excel_icon.png"
						title="Exportar para EXCEL" />
				</a>.
			</label>

		</div>
	</form>
</DIV>



<?php if ($idAnt && $comm == 'altBuy') { ?>
	<script language=javascript>
		var f = document.altform;
		//f.address.disabled = true;
		//f.city.disabled = true;
		//f.idCountry.disabled = true;
		//f.cep.disabled = true;
	</script>
<?php } ?>