<?php
///////////////////////////////////////////////////////////

// Inicio do programa

// Limpando Todas as variáveis

$ID_User = $userID;

$dataIni = $_POST['DATAINI'];
$dataFim = $_POST['DATAFIM'];
$Expor = $_POST['Exportador'];
$Impor = $_POST['Importador'];
$Fat = $_POST['Fatura'];
$emitirDVE = $_POST['Exportar'];
$paginar = $_POST['Paginar'];
$n_Apolice = $_POST['n_Apolice'];
$NotNPC1 = $_POST['NotNPC1'];
$SituacaoDVE = $_POST["SituacaoDVE"];



$dataHoje = date('d/m/Y');

function mesdiaano($d)
{

	if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)) {
		return "$v[2]/$v[1]/$v[3]";
	} else if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/", $d, $v)) {
		return "$v[2]/$v[1]/" . ($v[3] + 2000);
	} else {
		return '';
	}
}

// Executar a procedure
$a = $ID_User;
$b = "'" . $_POST['Exportador'] . "'";
$c = ($_POST['Importador'] != '' ? "'" . $_POST['Importador'] . "'" : 'null');
$d = ($_POST['Fatura'] != '' ? "'" . $_POST['Fatura'] . "'" : 'null');
$e = (mesdiaano($_POST['DATAINI']) != '' ? "'" . mesdiaano($_POST['DATAINI']) . "'" : 'null');
$f = (mesdiaano($_POST['DATAFIM']) != '' ? "'" . mesdiaano($_POST['DATAFIM']) . "'" : 'null');
$g = ($n_Apolice > 0 ? "'" . $n_Apolice . "'" : 'null');
$k = ($NotNPC1 != '' ? $NotNPC1 : '0');
$m = $SituacaoDVE;

$query = "{CALL SPR_DVECessaoDireito(?, ?, ?, ?, ?, ?, ?)}";


//print $query;


$sql = "SELECT Inf.name
        FROM UsersNurim UN
        INNER JOIN Agencia A ON A.idNurim = UN.idNurim
        INNER JOIN CDBB CB ON CB.idAgencia = A.id
        INNER JOIN Inform Inf ON Inf.id = CB.idInform
        INNER JOIN CDBBDetails CBD ON CBD.idCDBB = CB.id
        INNER JOIN Importer Imp ON Imp.id = CBD.idImporter
        INNER JOIN DVE DVE ON DVE.idInform = Inf.id
        INNER JOIN DVEDetails DVED ON DVED.idDVE = DVE.id AND DVED.idImporter = Imp.id
        WHERE Inf.state = ? AND CB.status = ? AND DVE.state = ? AND UN.idUser = ?
        GROUP BY Inf.name";

$cresp = odbc_prepare($db, $sql);
$params = [10, 2, 2, $ID_User];
odbc_execute($cresp, $params);
// Certifique-se de liberar a conexão após o uso


$i = 0;
$virg = '';
while (odbc_fetch_row($cresp)) {
	$infor .= $virg . odbc_result($cresp, "id");
	$virg = ',';
	$print[$i] = odbc_result($cresp, "name");
	$i++;
}

if ($_POST['EXECUTAR'] == 1) {
	$stmt = odbc_prepare($db, $query);
	$params = [$a, $b, $c, $d, $g, $k, $m];
	odbc_execute($stmt, $params);
	$cur = $stmt;
	odbc_free_result($stmt);

}

// primeiros procedimentos para realizar a paginação	
$limitePagina = 10;

$maior = $paginar * $limitePagina;
$menor = $maior - $limitePagina;

if ($maior == 0)
	$maior = $limitePagina;
if ($menor < 0)
	$menor = 0;

$j = 0;

// ****** fim do primeiro procedimento logo abaixo mais validações  

//include_once($root.'role/dve/scripts/validacoesBanco.js');




?>

<script language="javascript">

	UTF8 = {
		encode: function (s) {
			for (var c, i = -1, l = (s = s.split("")).length, o = String.fromCharCode; ++i < l;
				s[i] = (c = s[i].charCodeAt(0)) >= 127 ? o(0xc0 | (c >>> 6)) + o(0x80 | (c & 0x3f)) : s[i]
			);
			return s.join("");
		},
		decode: function (s) {
			for (var a, b, i = -1, l = (s = s.split("")).length, o = String.fromCharCode, c = "charCodeAt"; ++i < l;
				((a = s[i][c](0)) & 0x80) &&
				(s[i] = (a & 0xfc) == 0xc0 && ((b = s[i + 1][c](0)) & 0xc0) == 0x80 ?
					o(((a & 0x03) << 6) + (b & 0x3f)) : o(128), s[++i] = "")
			);
			return s.join("");
		}
	};


	var request = false;
	var dest;


	//Verifica se está usando internet explorer
	try {
		request = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			request = false;
		}
	}
	if (!request && typeof XMLHttpRequest != 'undefined') {
		request = new XMLHttpRequest();
	}

	//************Função para acessar as páginas e envio de parâmentro POST ou GET
	function loadHTML(URL, destination, GetORPost, str, stritem) {
		dest = destination;
		URL = antiCacheRand(URL);
		contentDiv = document.getElementById(dest);
		/*
		str = str+'&EXECUTAR=1&Exportador='+document.getElementById('Exportador').value+
				 '&Importador='+document.getElementById('Importador').value+'&Fatura='+
				 document.getElementById('Fatura').value+'&DATAINI='+
				 document.getElementById('DATAINI').value+'&DATAFIM='+
				 document.getElementById('DATAFIM').value;
		
		*/
		//verErro(document.getElementById('DataNPC'+stritem).value);

		str = str + '&DataLiq=' + document.getElementById('DataLiq' + stritem).value +
			'&DataProrrogacao=' + document.getElementById('DataProrrogacao' + stritem).value +
			'&DataNPC=' + document.getElementById('DataNPC' + stritem).value +
			'&stritem=' + stritem +
			'&validaDataNPC=' + document.getElementById('ValidaDataNPC' + stritem).value +
			'&t_Financiamento=' + document.getElementById('t_Financiamento' + stritem).value +
			'&n_Operacao=' + document.getElementById('n_Operacao' + stritem).value +
			'&v_Financiamento=' + document.getElementById('v_Financiamento' + stritem).value +
			'&v_Pago=' + document.getElementById('v_Pago' + stritem).value +
			'&v_SaldoFinanc=' + document.getElementById('SaldoFinanc' + stritem).value;


		//verErro(str);			  

		if (GetORPost == 'GET') {

			request.open("GET", URL + str, true);

			request.onreadystatechange = function () {
				if (request.readyState == 4 && request.status == 200) {
					if (request.responseXML) {
						//processXML(request.responseXML);	
						verErro(request.responseText);
						//contentDiv.innerHTML = request.responseText;	  
					} else {
						contentDiv.innerHTML = request.responseText;
						verErro(request.responseText);
					}
				} else {
					//contentDiv.innerHTML = "Error: Status "+request.status;
				}
			}
			request.send(null);
		} else {

			request.open('POST', URL, true);
			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			request.setRequestHeader("Content-length", "parameters-length");
			request.setRequestHeader("Connection", "close");
			request.send(str);

			request.onreadystatechange = function () {
				if (request.readyState == 4 && request.status == 200) {
					if (request.responseXML) {
						processXML(request.responseXML);
					} else {
						contentDiv.innerHTML = request.responseText;

					}
				} else {
					//contentDiv.innerHTML = "Error: Status "+request.status;
				}
			}

		}

	}


	//Função para correção de chache da verificação
	function antiCacheRand(aurl) {
		var dt = new Date();
		if (aurl.indexOf("?") >= 0) {// já tem parametros
			return aurl + "&" + encodeURI(Math.random() + "_" + dt.getTime());
		} else { return aurl + "?" + encodeURI(Math.random() + "_" + dt.getTime()); }
	}

	//******************************************************************

	function getformvalues(fobj, valfunc) {
		var str = "";
		for (var i = 0; i < fobj.elements.length; i++) {
			str += fobj.elements[i].name + "=" + escape(fobj.elements[i].value) + "&";
		} return str;
	}

	function SubmitForm(theform, serverPage, objID, getOrPost) {

		var file = serverPage;
		var str = getformvalues(theform, objID);
		var valor = "";
		obj = document.getElementById(objID);

		loadHTML(serverPage, obj, getOrPost, str);

	}

	function processXML(obj) {

		var dataArray = obj.getElementsByTagName("busca");
		if (dataArray.length > 0) {

			// percorre o arquivo XML paara extrair os dados
			for (var i = 0; i < dataArray.length; i++) {
				var item = dataArray[i];
				var nome = item.getElementsByTagName("nome")[0].firstChild.nodeValue;

			}

			if (nome != '')
				verErro(nome);


		}

	}

	//////////////////////////////////////////////////////////////////////////////////////
	// 1º COMBO INICIO
	///////////////////////////////////////////////////////////////////////////////////////
	function BuscaApolice(vlr) {
		{
			//verErro(vlr.length);
			request.open("GET", "<?php echo $root ?>role/dve/interf/control_busca_apolice.php?Cliente=" + vlr, true);
			request.onreadystatechange = handleHttpResponse;
			request.send(null);
		}

		function handleHttpResponse() {
			campo_select = document.forms[0].n_Apolice;
			if (request.readyState == 4) {
				campo_select.options.length = 0;
				results = request.responseText.split(",");

				for (i = 0; i < results.length; i++) {
					string = results[i].split("|");
					campo_select.options[i] = new Option(string[1], string[0]);
				}
			}
		}
		///////////////////////////////////////////////////////////////////////////////////////
		// FIM - 1º COMBO
		///////////////////////////////////////////////////////////////////////////////////////

	}

	function validaForm() {
		var campo1 = document.getElementById('Exportador').value;
		var campo2 = document.getElementById('n_Apolice').value;

		if (campo1.length == 0) {
			verErro('Voc&ecirc; deve selecionar o segurado.');
			return false;
		} else if (campo2.length == 0) {
			verErro('Voc&ecirc; deve selecionar a ap&oacute;lice.');
			return false;
		} else {
			return true;
		}
	}

	function formatar(src, mask) {
		var i = src.value.length;
		var saida = mask.substring(0, 1);
		var texto = mask.substring(i)
		if (texto.substring(0, 1) != saida) {
			src.value += texto.substring(0, 1);
		}
	}


	function validaDat(campo, valor) {
		var date = valor;
		var ardt = new Array;
		var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
		ardt = date.split("/");
		erro = false;
		if (valor.length > 0) {
			if (date.search(ExpReg) == -1) {
				erro = true;
			}
			else if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30))
				erro = true;
			else if (ardt[1] == 2) {
				if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
					erro = true;
				if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
					erro = true;
			}
			if (erro) {
				verErro("\"" + valor + "\" não é uma data válida!!!");
				campo.focus();
				campo.value = "";
				return false;
			}
		}
		return true;
	}

	function checarDatas(campo, data1, data2, str) {
		//var NomeForm = document.Formulario;   

		var data_1 = data1;
		var data_2 = data2;

		if (data1.length > 0) {
			var Compara01 = parseInt(data_1.split("/")[2].toString() + data_1.split("/")[1].toString() + data_1.split("/")[0].toString());
			var Compara02 = parseInt(data_2.split("/")[2].toString() + data_2.split("/")[1].toString() + data_2.split("/")[0].toString());

			if (Compara01 <= Compara02) {
				verErro('Atenção: Esta data deve ser maior que a ' + str);
				campo.focus();
				campo.value = "";
			}

		}
		return false;
	}

	function checarDatas2(campo, data1, data2, str, dado) {
		//var NomeForm = document.Formulario;   

		var data_1 = data1;
		var data_2 = data2;

		if (str == 'validar180') {
			//data_2 = somadiasvalidos(data_2, dado)
			//data_2 = somadias(data_2, dado);
			str = ' data de embarque mais ' + dado + ' dias.';

		}



		if (data_1.length > 0) {
			var Compara01 = parseInt(data_1.split("/")[2].toString() + data_1.split("/")[1].toString() + data_1.split("/")[0].toString());
			var Compara02 = parseInt(data_2.split("/")[2].toString() + data_2.split("/")[1].toString() + data_2.split("/")[0].toString());

			//verErro(Compara01+' - '+ Compara02+'??'+data_1+' '+data2+' '+str+' '+dado);
			if (Compara01 > Compara02) {
				verErro('Atenção: Esta data não pode ser maior que a ' + str);
				campo.focus();
				campo.value = "";
			}

		}

		return false;
	}


	function validaDataNPC(valor, strnum) {

		if (document.getElementById('DataNPC' + strnum).value) {
			document.getElementById('validaDataNPC' + strnum).value = 1;
		} else {
			document.getElementById('validaDataNPC' + strnum).value = 0;
		}

	}


	function numdias(mes, ano) {
		if ((mes < 8 && mes % 2 == 1) || (mes > 7 && mes % 2 == 0))
			return 31;
		if (mes != 2)
			return 30;
		if (ano % 4 == 0)
			return 29;
		return 28;
	}

	function verificardias(data, dias) {
		data = data.split('/');
		dia = parseInt(data[0]);
		mes = parseInt(data[1]);
		ano = parseInt(data[2]);

		valor = ano + mes + dia;
		valor = (valor + dias);
		return valor;
	}

	function somadias(data, dias) {
		data = data.split('/');
		diafuturo = parseInt(data[0]) + dias;
		mes = parseInt(data[1]);

		ano = parseInt(data[2]);
		while (diafuturo > numdias(mes, ano)) {
			diafuturo -= numdias(mes, ano);
			mes++;

			if (mes > 12) {
				mes = 1;
				ano++;
			}
		}

		if (diafuturo <= 9)
			diafuturo = '0' + diafuturo;
		if (mes <= 9)
			mes = '0' + mes;

		return diafuturo + "/" + mes + "/" + ano;
	}


	function paginacao(valor) {
		document.getElementById("Paginar").value = valor;
		//consultarDVE.submit();
	}

	function exportarExcel() {
		document.getElementById("Exportar").value = 1;
		//consultarDVE.submit();
	}

	function ChecarBox(campo, campo1) {
		if (document.getElementById(campo).checked) {
			document.getElementById(campo1).value = 1;
		} else {
			document.getElementById(campo1).value = 0;
		}

	}

</script>
<style>
	#imagem_sobreposta {
		width: 14px;
		height: 14px;
		background: #999;
		background-repeat: no-repeat;
	}
</style>

<?php include_once('../../../navegacao.php'); ?>

<body onLoad="BuscaApolice('<?php echo htmlspecialchars($Expor, ENT_QUOTES, 'UTF-8'); ?>');">



	<div class="conteudopagina">


		<form name="consultarDVE" id="consultarDVE" action="<?php echo $root ?>role/dve/Dve.php" method="post">
			<input type="hidden" name="comm" value="consultadveBanco">
			<input type="hidden" name="EXECUTAR" value="1">
			<input type="hidden" name="Exportar" id="Exportar" value="">
			<input type="hidden" name="Paginar" id="Paginar" value="">


			<li class="campo2colunas" style="width:100%;"><label>Dados para pesquisa:</label>
			</li>

			<div style="clear:both">&nbsp;</div>

			<li class="campo3colunas">
				<label>Cliente:</label>
				<?php
				$sql = "SELECT Inf.name
FROM UsersNurim UN
INNER JOIN Agencia A ON A.idNurim = UN.idNurim
INNER JOIN CDBB CB ON CB.idAgencia = A.id
INNER JOIN Inform Inf ON Inf.id = CB.idInform
INNER JOIN CDBBDetails CBD ON CBD.idCDBB = CB.id
INNER JOIN Importer Imp ON Imp.id = CBD.idImporter
INNER JOIN DVE DVE ON DVE.idInform = Inf.id
INNER JOIN DVEDetails DVED ON DVED.idDVE = DVE.id AND DVED.idImporter = Imp.id
WHERE Inf.state = ? AND CB.status = ? AND DVE.state = ? AND UN.idUser = ?
GROUP BY Inf.name";

				$cresp = odbc_prepare($db, $sql);
				$params = [10, 2, 2, $ID_User];
				odbc_execute($cresp, $params);
				?>
				<select name="Exportador" id="Exportador" onChange="BuscaApolice(this.value);">
					<option value="">Selecione...</option>

					<?php
					if ($cresp) {
						while (odbc_fetch_row($cresp)) {
							?>
							<option value="<?php echo odbc_result($cresp, "name"); ?>" <?php if (odbc_result($cresp, 'name') == $Expor)
									echo 'selected'; ?>><?php echo odbc_result($cresp, "name"); ?></option>
							<?php
						}

					} else {
						for ($x = 0; $x < $i; $x++) { ?>
							<option value="<?php echo $print[$x] ?>" <?php if ($print[$x] == $Expor)
								   echo 'selected'; ?>>
								<?php echo $print[$x] ?>
							</option>

						<?php }

					}
					?>

				</select>
			</li>
			<li class="campo3colunas">
				<label>Comprador:</label>
				<input type="text" name="Importador" id="Importador"
					value="<?php echo htmlspecialchars($Impor, ENT_QUOTES, 'UTF-8'); ?>"
					onKeyUp="this.value=this.value.toUpperCase()">
			</li>


			<li class="campo3colunas">
				<label>Fatura:</label>
<INPUT type="text" name="Fatura" id="Fatura" value="<?php echo htmlspecialchars($Fat, ENT_QUOTES, 'UTF-8'); ?>">&nbsp;&nbsp;
			</li>
			<li class="campo3colunas">
				<label>N&uacute;mero Ap&oacute;lice:</label>
				<?php

				if ($Expor != '') {
					//$cQry = " Select id,n_Apolice From Inform Where name like '%" . $Expor . "%' and n_Apolice is not null order by n_Apolice";
				
					$cQry4 = "SELECT TOP 2
                Inf.id,
                Inf.n_Apolice
          FROM 
                Inform Inf
          WHERE 
                Inf.name LIKE ? AND Inf.n_Apolice IS NOT NULL
                AND EXISTS (
                    SELECT * FROM DVE D 
                    WHERE D.idInform = Inf.id AND D.state = 2
                )
          ORDER BY 
                Inf.n_Apolice";

					$cresp7 = odbc_prepare($db, $cQry4);
					$ExporSanitized = strtoupper(substr($Expor, 0, 30));
					$param = "%$ExporSanitized%";
					odbc_execute($cresp7, [$param]);

					$ver = 1;
				}

				?>
				<select name="n_Apolice" id="n_Apolice">
					<option value="0">Selecione...</option>
					<?php

					while (odbc_fetch_row($cresp7)) {
						$id = odbc_result($cresp7, 0);

						if ($n_Apolice == $id) {
							$selected = 'selected';
						} else {
							$selected = '';
						}

						echo '<option value="' . $id . '" ' . $selected . '>' . odbc_result($cresp7, "n_Apolice") . '</option>';
					}

					?>
				</select>
			</li>
			<li class="campo3colunas">
				<label>Situa&ccedil;&atilde;o</label>
				<select name="SituacaoDVE" id="SituacaoDVE">
					<option value="0" <?php if ($SituacaoDVE == "0")
						echo 'selected'; ?>>Todos</option>
					<option value="1" <?php if ($SituacaoDVE == "1")
						echo 'selected'; ?>>Vencido</option>
					<option value="2" <?php if ($SituacaoDVE == "2")
						echo 'selected'; ?>>A Vencer</option>
					<option value="3" <?php if ($SituacaoDVE == "3")
						echo 'selected'; ?>>Pago</option>
				</select>
			</li>
			<li class="campo3colunas">
				<label>Notifica&ccedil;&atilde;o N.P.C</label>
				<div class="formopcao">
					<input type="checkbox" name="NotNPC" id="NotNPC" value="" <?php if ($NotNPC1 == 1)
						echo 'checked="checked"'; ?> onClick="ChecarBox('NotNPC','NotNPC1');">
				</div>
				<input type="hidden" name="NotNPC1" id="NotNPC1" value="<?php echo htmlspecialchars($NotNPC1, ENT_QUOTES, 'UTF-8'); ?>">

			</li>

			<div style="clear:both">&nbsp;</div>
			<li class="barrabotoes" style="*margin-left:-15px">
				<button class="botaoagm" type="button"
					onClick="javascript:  if (validaForm()) consultarDVE.submit(); ">Pesquisa</button>
			</li>



			<div id="MostrarTabela">

				<div id="Retorno" style="display:none"></div>

				<!--<table class="tabela01" summary="" id="example">-->
				<table width="100%" class="tabela01">
					<thead>
						<!--<th colspan="1" width="1">&nbsp;</th>-->
						<th colspan="6">Informa&ccedil;&otilde;es do Segurado</th>
					</thead>

					<tbody>

						<?php


						$totalVencido = 0;
						$totalPago = 0;
						$totalPendente = 0;
						$TotalGeral = 0;

						$expor = '';
						$numreg = 0;



						while (odbc_fetch_row($cur)) {

							$Apolice = odbc_result($cur, "Apolice");
							$Exportador = odbc_result($cur, "Segurado");
							$Importador = odbc_result($cur, "Importador");
							$Credito = number_format(odbc_result($cur, "Credito"), 2, ',', '.');
							$CodCessao = odbc_result($cur, "CodCessao");
							$Fatura = odbc_result($cur, "Fatura");
							$embDate = ymd2dmy(odbc_result($cur, "DataEmbarque"));
							$vencDate = ymd2dmy(odbc_result($cur, "DataVencimento"));
							$DataProrrogacao = ymd2dmy(odbc_result($cur, "DataProrrogacao"));
							$DataLiquidacao = ymd2dmy(odbc_result($cur, "DataPagamento"));
							$DataNPC = ymd2dmy(odbc_result($cur, "DataNPC"));
							$totalEmbarcado = number_format(odbc_result($cur, "TotalEmbarcado"), 2, ',', '.');
							$Situacao = odbc_result($cur, "Situacao");
							$codSituacao = odbc_result($cur, "CodSituacao");
							$SituacaoDias = odbc_result($cur, "SituacaoDias");
							$ImportadorCedido = odbc_result($cur, "ImportadorCedido");
							$proex = odbc_result($cur, "proex");
							$ace = odbc_result($cur, "ace");
							$CodDVED = odbc_result($cur, "CodDVED");
							$Observacao = odbc_result($cur, "Observacao");

							$PercCobertura = odbc_result($cur, "PercCobertura");
							$SituacaoApolice = odbc_result($cur, "SituacaoApolice");
							$PremioEmitido = odbc_result($cur, "PremioEmitido");
							$PremioPago = odbc_result($cur, "PremioPago");
							$PremioVencido = odbc_result($cur, "PremioVencido");
							$dataLimiteEmbarque = ymd2dmy(odbc_result($cur, "DataLimiteEmbarque"));

							/* Campos novos*/
							// INICIO
						
							$t_Financiamento = odbc_result($cur, "TipoFinanciamento");
							$n_Operacao = odbc_result($cur, "NumOperacao");
							$v_Financiamento = number_format(odbc_result($cur, "ValorFinanciamento"), 2, ',', '.');
							$v_Pago = number_format(odbc_result($cur, "ValorPago"), 2, ',', '.');


							/* FIM */


							$PeriodoMaxCred = number_format(odbc_result($cur, "periodMaxCred"), 0, '', '');
							$FatorLMI = number_format(odbc_result($cur, "FatorLMI"), 2, ',', '.');
							$ValorLMI = number_format(odbc_result($cur, "ValorLMI"), 2, ',', '.');
							$MargemLMI = number_format(odbc_result($cur, "MargemLMI"), 2, ',', '.');

							$MargemDisponivel = number_format(odbc_result($cur, "MargemDisponivel"), 2, ',', '.');
							$limitevigente = number_format(odbc_result($cur, "limitevigente"), 2, ',', '.');
							$SaldoFinanciamento = number_format(odbc_result($cur, "SaldoFinanciamento"), 2, ',', '.');

							if ($codSituacao == 2) {
								$totalVencido += odbc_result($cur, "TotalEmbarcado");
								$exibirSituacao = 'Vencido há ' . $SituacaoDias . " dias";
							} elseif ($codSituacao == 1) {
								$totalPago += odbc_result($cur, "TotalEmbarcado");
								$exibirSituacao = $Situacao;
							} elseif ($codSituacao == 0) {
								$totalPendente += odbc_result($cur, "TotalEmbarcado");
								$exibirSituacao = 'A Vencer em ' . $SituacaoDias . " dias";
							}

							$TotalGeral += odbc_result($cur, "TotalEmbarcado");


							//print '?'.$CodDVED;  
						
							if ($numreg % 2 == 0) {
								$cor = 'class="odd"';
							} else {
								$cor = '';
							}
							//if ($numreg < $maior && $numreg >= $menor){   // Definie a paginacao
							//****************************DEFINE AQUI A PAGINAÇÃO***************************
							if ($Apolice != $apo) {
								$apo = $Apolice;


								?>

								<tr <?php echo $cor; ?>>
									<!--<td colspan="1" align="left" width="1">&nbsp;</td>-->
									<td colspan="6" align="left" style="background-color:#EFEFEF">
										<li class="campo2colunas" style="width:180px;">
											<label>
												<h2>Ap&oacute;lice:<?php echo $Apolice ?></h2>
											</label>
										</li>
										<li class="campo2colunas"><label>
												<h2><?php echo $Exportador ?></h2>
											</label></li>
										<div style="clear:both">&nbsp;</div>
										<li class="campo3colunas" style="width:180px;">
											<label>Situa&ccedil;&atilde;o</label>
											<?php echo ($SituacaoApolice); ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Cobertura da ap&oacute;lice</label>
											<?php echo number_format($PercCobertura, 2, ',', '.') ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Pr&ecirc;mio Emitido</label>
											<?php echo number_format($PremioEmitido, 2, ',', '.') ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Pr&ecirc;mio Pago</label>
											<?php echo number_format($PremioPago, 2, ',', '.') ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Pr&ecirc;mio Vencido</label>
											<?php echo number_format($PremioVencido, 2, ',', '.') ?>
										</li>

										<li class="campo3colunas" style="width:180px;">
											<label>Fator LMI</label>
											<?php echo ($FatorLMI > 0 ? $FatorLMI : '0,00') ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Valor LMI</label>
											<?php echo ($ValorLMI > 0 ? $ValorLMI : '0,00') ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Margem Dispon&iacute;vel do LMI</label>
											<?php echo ($MargemLMI > 0 ? $ValorLMI : '0,00') ?>
										</li>
									</td>
								</tr>

								<?php

							}

							if ($Importador != $Imp) {
								$Imp = $Importador; ?>

								<tr style="background-color:#999">
									<!-- <td colspan="1" align="left" width="1">&nbsp;</td>	-->
									<td colspan="6" align="left"><strong>
											<font color="#FFFFFF" size="4">Comprador:&nbsp;</font>
										</strong>
										<font color="#FFFFFF"><?php echo $Importador; ?></font>
									</td>
								</tr>


								<tr style="background-color:#EFEFEF">
									<!--  <td colspan="1" align="left"  width="1">&nbsp;</td> -->
									<td colspan="6" align="left">
										<li class="campo3colunas" style="width:180px;">
											<label>Limite Vigente</label>
											<?php echo ($LimiteVigente > 0 ? $LimiteVigente : '0,00') ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Margem Dispon&iacute;vel</label>
											<?php echo ($MargemDiponivel > 0 ? $MargemDiponivel : '0,00') ?>
										</li>
									</td>
								</tr>


							<?php } ?>

							<tr <?php echo $cor; ?>>
								<td colspan="1" align="left" width="30">&nbsp;</td>
								<td colspan="5">
									<!-- <li class="campo2colunas">
									<?php //echo $Observacao ?>
								</li>
								<div style="clear:both">&nbsp;</div>  
								-->
									<li class="campo3colunas" style="width:180px;">
										<label>C&oacute;digo Cess&atilde;o:</label>
										<?php echo $CodCessao; ?>
									</li>

									<li class="campo3colunas" style="width:180px;">
										<label>Valor do Embarque:</label>
										<?php echo $totalEmbarcado; ?>
									</li>
									<li class="campo3colunas" style="width:180px;">
										<label>Fatura:</label>
										<?php echo $Fatura; ?>
									</li>
									<li class="campo3colunas" style="width:180px;">
										<label>Cr&eacute;dito Concedido:</label>
										<?php echo $Credito ?>
									</li>
									<li class="campo3colunas" style="width:180px;">
										<label>Data Embarque:</label>
										<?php echo $embDate ?>
									</li>
									<li class="campo3colunas" style="width:180px;">
										<label>Data Vencimento:</label>
										<?php echo $vencDate ?>
									</li>
									<li class="campo3colunas" style="width:180px;">
										<label>Data de Liquida&ccedil;&atilde;o:</label>
										<?php
										if ($ImportadorCedido == 1) { ?>
											<input type="text" name="DataLiq<?php echo $numreg ?>"
												id="DataLiq<?php echo $numreg ?>" value="<?php echo $DataLiquidacao ?>" size="8"
												maxlength="10" OnKeyPress="formatar(this, '##/##/####');"
												onBlur="validaDat(this,this.value); checarDatas(this,this.value,'<?php echo $embDate ?>','data de embarque.');" />
										<?php } else {
											echo ". " . $DataLiquidacao;
										} ?>

									</li>
									<li class="campo3colunas" style="width:180px;">
										<label>Data Novo Vencimento:</label>
										<?php if ($ImportadorCedido == 1) { ?>
											<input type="text" name="DataProrrogacao<?php echo $numreg ?>"
												id="DataProrrogacao<?php echo $numreg ?>" value="<?php echo $DataProrrogacao ?>"
												size="8" maxlength="10" OnKeyPress="javascript: formatar(this, '##/##/####');"
												onBlur="javascript: validaDat(this,this.value); checarDatas2(this,this.value,'<?php echo $dataLimiteEmbarque ?>','validar180','<?php echo $PeriodoMaxCred ?>'); checarDatas(this,this.value,'<?php echo $vencDate ?>',' data de vencimento.');" />
										<?php } else {
											echo ". " . $DataProrrogacao;
										} ?>
									</li>

									<li class="campo3colunas" style="width:180px;">
										<label>Situa&ccedil;&atilde;o:</label>
										<?php echo ($exibirSituacao); ?>
									</li>
									<li class="campo3colunas" style="width:180px;">
										<label>Notifica&ccedil;&atilde;o N.P.C:</label>
										<?php if ($ImportadorCedido == 1) { ?>
											<?php //print '?'.$DataNPC;
													if ($DataNPC != '') {
														echo $DataNPC; ?>
												<input type="hidden" name="DataNPC<?php echo $numreg; ?>"
													id="DataNPC<?php echo $numreg ?>" value="<?php echo $DataNPC ?>" />
												<input type="hidden" name="ValidaDataNPC<?php echo $numreg; ?>"
													id="ValidaDataNPC<?php echo $numreg ?>" value="" />
											<?php } else { ?>
												<input type="checkbox" name="DataNPC<?php echo $numreg; ?>"
													id="DataNPC<?php echo $numreg; ?>" value="<?php echo $dataHoje ?>"
													onClick="validaDataNPC(this.event,'<?php echo $numreg ?>');" />
												<input type="hidden" name="ValidaDataNPC<?php echo $numreg; ?>"
													id="ValidaDataNPC<?php echo $numreg; ?>" value="" />
											<?php } ?>
										<?php } else {
											echo $DataNPC;

										} ?>
									</li>

									<?php
									//print '?'.$ImportadorCedido;
									if ($ImportadorCedido == 0) { ?>
										<li class="campo3colunas" style="width:180px;">
											<label>Valor Financiamento:</label>
											<?php echo ($v_Financiamento != '' ? $v_Financiamento : '0,00') ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>No Opera&ccedil;&atilde;o:</label>
											<?php echo ($n_Operacao ? $n_Operacao : '.'); ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Saldo Financiamento:</label>
											<?php echo ($SaldoFinanciamento != '' ? $SaldoFinanciamento : '0,00') ?>
										</li>

										<li class="campo3colunas" style="width:180px;">
											<label>Valor Pago:</label>
											<?php echo ($v_Pago != '' ? $v_Pago : '0,00'); ?>
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Financiamento:</label>

											<?php
											if ($t_Financiamento == 1)
												echo 'ACE';
											elseif ($t_Financiamento == 2)
												echo 'PROEX';
											elseif ($t_Financiamento == 3)
												echo 'Desconto de Camb';

											?>
										</li>
									<?php } else { ?>
										<li class="campo3colunas" style="width:180px;">
											<label>Valor Financiamento:</label>
											<input type="text" size="16" name="v_Financiamento<?php echo $numreg ?>"
												id="v_Financiamento<?php echo $numreg ?>"
												value="<?php echo ($v_Financiamento != '' ? $v_Financiamento : '0,00') ?>"
												style="text-align:right"
												onBlur="if(this.value != '') checkDecimals(this, this.value);">
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>No Opera&ccedil;&atilde;o:</label>
											<input type="text" maxlength="20" size="15" name="n_Operacao<?php echo $numreg ?>"
												id="n_Operacao<?php echo $numreg ?>" value="<?php echo $n_Operacao ?>">
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Saldo Financiamento:</label>
											<input type="text" size="16" name="SaldoFinanc<?php echo $numreg ?>"
												id="SaldoFinanc<?php echo $numreg ?>"
												value="<?php echo ($SaldoFinanciamento != '' ? $SaldoFinanciamento : '0,00') ?>"
												style="text-align:right"
												onBlur="if(this.value != '') checkDecimals(this, this.value);">
										</li>
										<li class="campo3colunas" style="width:180px;">
											<label>Valor Pago:</label>
											<input type="text" size="15" name="v_Pago<?php echo $numreg ?>"
												id="v_Pago<?php echo $numreg ?>"
												value="<?php echo ($v_Pago != '' ? $v_Pago : '0,00') ?>"
												style="text-align:right"
												onBlur="if(this.value != '') checkDecimals(this, this.value);">
										</li>

										<li class="campo3colunas" style="width:180px;">
											<label>Tipo Financiamento:</label>
											<select name="t_Financiamento<?php echo $numreg ?>"
												id="t_Financiamento<?php echo $numreg ?>" style="width:150px;">
												<option value="">Selecione...</option>
												<option value="1" <?php if ($t_Financiamento == 1)
													echo 'selected="selected"'; ?>>
													ACE</option>
												<option value="2" <?php if ($t_Financiamento == 2)
													echo 'selected="selected"'; ?>>
													PROEX</option>
												<option value="3" <?php if ($t_Financiamento == 3)
													echo 'selected="selected"'; ?>>
													Desconto de Camb.</option>
											</select>
										</li>
									<?php } ?>
									<?php if ($ImportadorCedido == 1) { ?>
										<li class="campo3colunas" style="width:180px;">
											<label>&nbsp;</label>
											<button class="botaoapm" type="button" name="GravaDatas" id="GravaDatas"
												onClick="javascript: loadHTML('<?php echo $root ?>role/dve/interf/control_consulta_dve_banco.php', 'Retorno','GET','&CodDVED=<?php echo $CodDVED ?>',<?php echo $numreg ?>);">Gravar</button>
										</li>

									<?php } ?>
								</td>
							</tr>

							<?php

							$numreg++;
						}




						?>
					</tbody>

				</table>

				<table>
					<tfoot>
						<tr>
							<th colspan="2" rowspan="4" width="550">
								Critério:<br />
								<?php
								$cor = '<font color="#FFFFFF" face="Arial, Helvetica, sans-serif" size="1">';
								echo ($Expor ? 'Cliente: ' . $cor . htmlspecialchars($Expor, ENT_QUOTES, 'UTF-8') . '</font><br />' : '');
								echo ($Impor ? 'Importador: ' . $cor . htmlspecialchars($Impor, ENT_QUOTES, 'UTF-8') . '</font><br />' : '');
								echo ($Fat ? 'Fatura: ' . $cor . htmlspecialchars($Fat, ENT_QUOTES, 'UTF-8') . '</font>' : '');
								?>
							</th>



							<th colspan="2" width="120">
								Total Vencido:<br>
								Total Pago:<br>
								Total Pendente:<br>
								Total Geral:
							</th>

							<th colspan="2" style="text-align:right">
								<?php echo number_format($totalVencido, 2, ',', '.'); ?><br>
								<?php echo number_format($totalPago, 2, ',', '.'); ?><br>
								<?php echo number_format($totalPendente, 2, ',', '.'); ?><br>
								<?php echo number_format($TotalGeral, 2, ',', '.'); ?>
							</th>



						</tr>


						<?php
						/*
																							$conter = 0;
																							if ($numreg > $limitePagina){    
																								$paginas = ($numreg/$limitePagina);
																								
																								if($paginas % 10 != 0)
																								   $paginas = $paginas+1;
																							}
																							if (! $_POST['Paginar']){
																								$pagi = 1;
																							}else{
																							   $pagi = $_POST['Paginar'];
																							}   

																							for ($y=1; $y <= $paginas;$y++){   
																								 //$pg .= '|'.'<a href="#" onclick="javascript: paginacao('.$y.');">'.$y.'</a>'; 
																								 
																								 //$pg .= '<input type="image" src="../../imagens/exibe.gif" alt ="página '.$y.'" name="im'.$y.'" id="im'.$y.'" value="'.$y.'" onclick="paginacao('.$y.');" width="16" height="14" border="0"/><SPAN>'.$y.'</SPAN>';
																								 $pg .= '|<input type="submit" alt ="página '.$y.'" name="im'.$y.'" id="im'.$y.'" value="'.$y.'" onclick="return paginacao('.$y.');"style="width:20px; height:14px; background-color:'.($pagi == $y ? '#999' : '#FFF').'; font-size:8px; border:0px; text-align:center; cursor:pointer;"/>';
																								 $conter++;
																								 
																						   } 
																						   */
						?>
					</tfoot>
				</table>





				<div style="clear:both">&nbsp;</div>

				<li class="campo2colunas">
					<a
						href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/dve/interf/consultadvebanco_xls.php?Cliente=<?php echo urlencode($Expor); ?>&Importador=<?php echo urlencode($Impor); ?>&Fatura=<?php echo urlencode($Fat); ?>&Apolice=<?php echo urlencode($n_Apolice); ?>&Doc=<?php echo urlencode($docexportado); ?>&ID_User=<?php echo urlencode($ID_User); ?>&SituacaoDVE=<?php echo urlencode($SituacaoDVE); ?>&NotNPC1=<?php echo urlencode($NotNPC1); ?>">
						<label style="color:#F00">Exportar para Excel</label>
					</a>
				</li>

		</FORM>

	</div>
	<div style="clear:both">&nbsp;</div>
	</div>