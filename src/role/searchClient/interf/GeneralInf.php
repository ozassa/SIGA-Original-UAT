<style>
	.tbl_mods {
		width: 500px;
	}

	.tbl_mods tr {
		height: 10px;
	}

	.tbl_mods,
	.tbl_mods td,
	.tbl_mods tr,
	.tbl_mods td:hover,
	.tbl_mods tr:hover {
		border: none;
		font-size: 10px;
	}
</style>

<?php

if (!$userID) {
	$usuario = $_SESSION['userID'];
} else {
	$usuario = $userID;
}
// Alterado Hicom (Gustavo) 14/12/04 - Possibilidade do perfil executive alterar o campo
// "Cobertura para juros de mora" se status < 6
// alterado Hicom 27/12/2004 (Gustavo) - Adicionei os campos addressNumber e chargeAddressNumber
// alterado Hicom 17/01/2005 (Gustavo) - possibilidade de alterar alguns campos de endereço, email, etc.

//################################################
//alterado por eliel vieira - elumini - 31/08/2007
$col_span = 4;

function formata($numero)
{
	if (strpos($numero, '.') != '') {
		$var = explode('.', $numero);
		if (strlen($var[0]) == 4) {
			$parte1 = substr($var[0], 0, 1);
			$parte2 = substr($var[0], 1, 3);
			if (strlen($var[1]) < 2) {
				$formatado = $parte1 . '.' . $parte2 . ',' . $var[1] . '0';
			} else {
				$formatado = $parte1 . '.' . $parte2 . ',' . $var[1];
			}
		} elseif (strlen($var[0]) == 5) {
			$parte1 = substr($var[0], 0, 2);
			$parte2 = substr($var[0], 2, 3);
			if (strlen($var[1]) < 2) {
				$formatado = $parte1 . '.' . $parte2 . ',' . $var[1] . '0';
			} else {
				$formatado = $parte1 . '.' . $parte2 . ',' . $var[1];
			}
		} elseif (strlen($var[0]) == 6) {
			$parte1 = substr($var[0], 0, 3);
			$parte2 = substr($var[0], 3, 3);
			if (strlen($var[1]) < 2) {
				$formatado = $parte1 . '.' . $parte2 . ',' . $var[1] . '0';
			} else {
				$formatado = $parte1 . '.' . $parte2 . ',' . $var[1];
			}
		} elseif (strlen($var[0]) == 7) {
			$parte1 = substr($var[0], 0, 1);
			$parte2 = substr($var[0], 1, 3);
			$parte3 = substr($var[0], 4, 3);
			if (strlen($var[1]) < 2) {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . $var[1] . '0';
			} else {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . $var[1];
			}
		} elseif (strlen($var[0]) == 8) {
			$parte1 = substr($var[0], 0, 2);
			$parte2 = substr($var[0], 2, 3);
			$parte3 = substr($var[0], 5, 3);
			if (strlen($var[1]) < 2) {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . $var[1] . '0';
			} else {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . $var[1];
			}
		} elseif (strlen($var[0]) == 9) {
			$parte1 = substr($var[0], 0, 3);
			$parte2 = substr($var[0], 3, 3);
			$parte3 = substr($var[0], 6, 3);
			if (strlen($var[1]) < 2) {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . $var[1] . '0';
			} else {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . $var[1];
			}
		} elseif (strlen($var[0]) == 10) {
			$parte1 = substr($var[0], 0, 1);
			$parte2 = substr($var[0], 1, 3);
			$parte3 = substr($var[0], 4, 3);
			$parte4 = substr($var[0], 7, 3);
			if (strlen($var[1]) < 2) {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . '.' . $parte4 . ',' . $var[1] . '0';
			} else {
				$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . '.' . $parte4 . ',' . $var[1];
			}
		} else {
			if (strlen($var[1]) < 2) {
				$formatado = $var[0] . ',' . $var[1] . '0';
			} else {
				$formatado = $var[0] . ',' . $var[1];
			}
		}
	} else {
		$var = $numero;
		if (strlen($var) == 4) {
			$parte1 = substr($var, 0, 1);
			$parte2 = substr($var, 1, 3);
			$formatado = $parte1 . '.' . $parte2 . ',' . '00';
		} elseif (strlen($var) == 5) {
			$parte1 = substr($var, 0, 2);
			$parte2 = substr($var, 2, 3);
			$formatado = $parte1 . '.' . $parte2 . ',' . '00';
		} elseif (strlen($var) == 6) {
			$parte1 = substr($var, 0, 3);
			$parte2 = substr($var, 3, 3);
			$formatado = $parte1 . '.' . $parte2 . ',' . '00';
		} elseif (strlen($var) == 7) {
			$parte1 = substr($var, 0, 1);
			$parte2 = substr($var, 1, 3);
			$parte3 = substr($var, 4, 3);
			$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . '00';
		} elseif (strlen($var) == 8) {
			$parte1 = substr($var, 0, 2);
			$parte2 = substr($var, 2, 3);
			$parte3 = substr($var, 5, 3);
			$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . '00';
		} elseif (strlen($var) == 9) {
			$parte1 = substr($var, 0, 3);
			$parte2 = substr($var, 3, 3);
			$parte3 = substr($var, 6, 3);
			$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . ',' . '00';
		} elseif (strlen($var) == 10) {
			$parte1 = substr($var, 0, 1);
			$parte2 = substr($var, 1, 3);
			$parte3 = substr($var, 4, 3);
			$parte4 = substr($var, 7, 3);
			$formatado = $parte1 . '.' . $parte2 . '.' . $parte3 . '.' . $parte4 . ',' . '00';
		} else {
			$formatado = $var . ',' . '00';
		}
	}
	return $formatado;
}
?>

<?php
$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : false;

/*if($_GET["idInform"]){
	$idInform   = $_GET["idInform"];
	$comm       = $_GET["comm"];
}else{
	$idInform   = $_POST["idInform"];
	$comm       = $_POST["comm"];
}*/

$executa = isset($_POST['executa']) ? $_POST['executa'] : 0;
$Periodo_Vigencia = isset($_POST['Periodo_Vigencia']) ? $_POST['Periodo_Vigencia'] : "";

// Alterado Hicom (Gustavo)
if ($executa == 1) {
	$sql = "UPDATE Inform SET warantyInterest = ? WHERE id = ?";
	$cur = odbc_prepare($db, $sql);
	odbc_execute($cur, [$_POST['warantyInterest'], $idInform]);
	odbc_free_result($cur);
}

if ($executa == 2) {
	require_once("../client/changeGeneralInf.php");

	$query = odbc_prepare($db, "SELECT * FROM Inform WHERE id = ?");
	odbc_execute($query, [$idInform]);
	$state = odbc_result($query, "state");
	$i_Seg = odbc_result($query, "i_Seg");

	odbc_free_result($query);

	$sql = "UPDATE Inform SET ";

	if ($state != 10 && $state != 11 && $state != 9 && $state != 6) {
		$sql .= "name = ?, cnpj = ?, ie = ?, ";
		$params = [$_POST['nome'], $_POST['cnpj'], $_POST['ie']];
	} else {
		$params = [];
	}

	$sql .= "address = ?, addressNumber = ?, addressComp = ?, respName = ?, ocupation = ?, warantyInterest = ?, ";
	$params = array_merge($params, [
		$_POST['endereco'],
		$_POST['numero'],
		$_POST['comp'],
		$_POST['respName'],
		$_POST['ocupation'],
		$_POST['warantyInterest']
	]);

	if ($_POST['cob'] == 0 && isset($_POST['chargeAddressAbrev'])) {
		$sql .= "addressAbrev = ?, ";
		$params[] = $_POST['chargeAddressAbrev'];
	}

	if ($Periodo_Vigencia) {
		$sql .= "Periodo_Vigencia = ?, ";
		$params[] = $Periodo_Vigencia;
	} elseif ($_POST['cob'] == 1) {
		$sql .= "addressAbrev = ?, ";
		$params[] = $_POST['abrev'];
	}

	$sql .= "city = ?, cep = ?, tel = ?, fax = ?, email = ?, contact = ?, ocupationContact = ?, emailContact = ?, idSector = ?, idConsultor = ?";
	$params = array_merge($params, [
		$_POST['cidade'],
		$_POST['cep'],
		$_POST['tel'],
		$_POST['fax'],
		$_POST['email'],
		$_POST['contact'],
		$_POST['ocupationContact'],
		$_POST['emailContact'],
		$_POST['setor_col'],
		$_POST['idConsultor'] ?? null
	]);

	if ($_POST['cob'] == 0) {
		$sql .= ", sameAddress = ?, chargeAddress = ?, chargeAddressComp = ?, chargeAddressNumber = ?, chargeCity = ?, chargeUf = ?, chargeCep = ?";
		$params = array_merge($params, [
			$_POST['cob'],
			$_POST['chargeAddress'],
			$_POST['chargeAddressComp'],
			$_POST['chargeAddressNumber'],
			$_POST['chargeCity'],
			$_POST['chargeUf'],
			$_POST['chargeCep']
		]);
	} elseif ($_POST['cob'] == 1) {
		$sql .= ", sameAddress = ?, chargeAddress = '', chargeAddressComp = '', chargeAddressNumber = '', chargeCity = '', chargeUf = 'NA', chargeCep = ''";
		$params[] = $_POST['cob'];
	}

	if ($role["credit"] || $role["creditManager"] || $role["policy"]) {
		$sql .= ", Obs = ?, naf = ?, napce = ?";
		$params = array_merge($params, [$_POST['obs'], $_POST['naf'], $_POST['napce']]);
	}

	$sql .= " WHERE id = ?";
	$params[] = $idInform;

	$cur = odbc_prepare($db, $sql);
	odbc_execute($cur, $params);

	// Atualizar dados no SisSeg
	$sqls = "UPDATE Segurado SET ";
	$params_s = [];

	if ($state != 10 && $state != 11 && $state != 9 && $state != 6) {
		$sqls .= "Nome = ?, CNP = ?, IE = ?, ";
		$params_s = [$_POST['nome'], $_POST['cnpj'], $_POST['ie']];
	}

	if ($_POST['cob'] == "0") {
		$chargeCep = str_replace("-", "", $_POST['chargeCep']);
		$sqls .= "Endereco = ?, Numero = ?, Compl = ?, End_Abrev = ?, Cidade = ?, CEP = ?, Telefone = ?, Fax = ?";
		$params_s = array_merge($params_s, [
			$_POST['chargeAddress'],
			$_POST['chargeAddressNumber'],
			$_POST['chargeAddressComp'],
			$_POST['chargeAddressAbrev'],
			$_POST['chargeCity'],
			$chargeCep,
			$_POST['tel'],
			$_POST['fax']
		]);
	} else {
		$cep = str_replace("-", "", $_POST['cep']);
		$sqls .= "Endereco = ?, Numero = ?, Compl = ?, End_Abrev = ?, Cidade = ?, CEP = ?, Telefone = ?, Fax = ?";
		$params_s = array_merge($params_s, [
			$_POST['endereco'],
			$_POST['numero'],
			$_POST['comp'],
			$_POST['abrev'],
			$_POST['cidade'],
			$cep,
			$_POST['tel'],
			$_POST['fax']
		]);
	}

	$sqls .= " WHERE i_Seg = ?";
	$params_s[] = $i_Seg;

	//error_log(print_r($params_s, true));

	if ($i_Seg) {
		$r = odbc_prepare($dbSisSeg, $sqls);
		odbc_execute($r, $params_s);
	}

	// Redirect
	$_SESSION['msg'] = 'Dados do segurado atualizados com sucesso'; ?>
	<script>
		window.location = '<?php echo $host; ?>src/role/searchClient/ListClient.php?comm=view&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>';
	</script>
	<?php
}

// fim alterado


$sql = "SELECT I.*, SUBSTRING(I.Obs, 1, 10000) AS OBSER FROM Inform I WHERE I.id = ?";
$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$idInform]);


if (odbc_fetch_row($cur)) {
	$field->setDB($cur);
	$state = odbc_result($cur, "state"); // alterado Hicom (Gustavo)
	$sameAddress = odbc_result($cur, 'sameAddress');
	$addressNumber = odbc_result($cur, 'addressNumber');
	$chargeAddressNumber = odbc_result($cur, 'chargeAddressNumber');
	$addressComp = odbc_result($cur, 'addressComp');
	$chargeAddressComp = odbc_result($cur, 'chargeAddressComp');
	$chargeCity = odbc_result($cur, 'chargeCity');
	$chargeAddress = odbc_result($cur, 'chargeAddress');
	$chargeCep = odbc_result($cur, 'chargeCep');
	$origemNegocio = odbc_result($cur, 'origemNegocio');
	$v_LMI = odbc_result($cur, 'v_LMI');
	$PrMtotal = odbc_result($cur, 'PrMTotal');
	$Periodo_Vigencia = odbc_result($cur, 'Periodo_Vigencia');
	$idSetor = odbc_result($cur, 'idSector');
	/*
								  //################################################
								  //alterado por eliel vieira - elumini - 29/08/2007
								  //################################################
								  */
	$idConsultor = trim(odbc_result($cur, 'idConsultor') ?? '');
	$chargeAddressAbrev = '';
	$abrev = '';
	if ($sameAddress == 1) {
		$abrev = odbc_result($cur, 'addressAbrev');
	} else if ($sameAddress == 0) {
		$chargeAddressAbrev = odbc_result($cur, 'addressAbrev');
	}

	//Alterado por Tiago V N - 23/09/2005
	$vigencia = odbc_result($cur, 'pvigencia');

	if ($Periodo_Vigencia) {
		$pvigencia = $Periodo_Vigencia;
	} else {
		if ($vigencia == "") {
			$pvigencia = "12 Meses";
		} else if ($vigencia == "1") {
			$pvigencia = "12 Meses";
		} else {
			$pvigencia = "24 Meses";
		}
	}
	$ga = odbc_result($cur, 'Ga');
	$perBonus = odbc_result($cur, 'perBonus');
	$perPart0 = odbc_result($cur, 'perPart0');
	$perPart1 = odbc_result($cur, 'perPart1');
	$pLucro = odbc_result($cur, 'pLucro');
	$mBonus = odbc_result($cur, 'mModulos');
	$obs = odbc_result($cur, 'OBSER');
	$limPagIndeniz = odbc_result($cur, 'limPagIndeniz');
	$percCoverage = odbc_result($cur, 'percCoverage');
	$numParc = odbc_result($cur, 'numParc');
	$txMin = odbc_result($cur, 'txMin');
	$txRise = odbc_result($cur, 'txRise');
	$prMinAuxX2X = odbc_result($cur, 'prAux');

	if ($prMinAuxX2X == null) {
		$prMinAuxX2X = 0;
	}

	if (odbc_result($cur, 'currency') == 1) {
		$ext = "R$";
	} else if (odbc_result($cur, 'currency') == 2) {
		$ext = "US$";
	} else if (odbc_result($cur, 'currency') == 6) {
		$ext = "€";
	}

	$premio_min = odbc_result($cur, 'prMin') * (1 + $txRise);
	$tx = $txMin * (1 + $txRise);
	//$caex = number_format (($premio_min * (odbc_result($cur, 'warantyInterest') == 1 ? 1.04 : 1) / $tx), 2, ',','.');
	if ($premio_min && $tx) {
		$caex = number_format(($premio_min / $tx), 2, ',', '.');
	} else {
		$caex = '0';
	}



	//odbc_free_result($cur);


	?>



	<script Language="JavaScript">

		function novoValidaEmail(mail) {
			var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
			if (typeof (mail) == "string") {
				if (er.test(mail)) {
					return true;
				}
			}
			else if (typeof (mail) == "object") {
				if (er.test(mail.value)) {
					return true;
				}
			}
			else if ((indexOf('.', mail) == 0) || (indexOf('@', mail) > 1))
				return false
			else {
				return false;
			}
		}

		function checa_email(form, campo) {
			valmail = form.elements(campo).value;

			if (!novoValidaEmail(valmail)) {
				form.elements(campo).focus();
				verErro("O e-mail do Contato é Inválido!");
				return false;
			}
			else {
				form.elements(campo).value = valmail.toLowerCase();
			}

		}


		function dados(tipo) {
			var ajax = false;


			if (window.XMLHttpRequest) {
				ajax = new XMLHttpRequest();
			} else if (window.ActiveXObject) {
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			}

			if (tipo == 1) { //Endereço principal
				var cep = document.getElementById("cep").value;
				var url = 'http://republicavirtual.com.br/web_cep.php?cep=' + cep + '&formato=xml';
			} else { //Endereço de cobrança
				var cep = document.getElementById("id_cep").value;
				var url = 'http://republicavirtual.com.br/web_cep.php?cep=' + cep + '&formato=xml';
			}
			if (cep == "") {
				verErro('O campo cep não pode ser vazio.');
			} else {
				if (ajax) {
					ajax.open("GET", url, true);
					ajax.onreadystatechange = function () {
						if (ajax.readyState == 4) {
							if (ajax.status == 200) {
								//var obj.ajax.responseXML;
								var dados = obj.getElementsByTagName("webservicecep");

								if (tipo == 1) {// Endereço principal
									//processarXML(ajax.responseXML);
									var dados = obj.getElementsByTagName("webservicecep");
									//Total de elementos contidos na tag webservicecep
									if (dados.length > 0) {
										//percorre o arquivo XML para extrair os dados
										for (var i = 0; i < dados.length; i++) {
											var item = dados[i];
											if (item.getElementsByTagName("resultado")[0].firstChild.nodeValue != 0) {
												//conteudo dos campos no arquivo XML
												var uf = item.getElementsByTagName("uf")[0].firstChild.nodeValue;
												var cidade = item.getElementsByTagName("cidade")[0].firstChild.nodeValue;
												var bairro = item.getElementsByTagName("bairro")[0].firstChild.nodeValue;
												var tipo_logradouro = item.getElementsByTagName("tipo_logradouro")[0].firstChild.nodeValue;
												var logradouro = item.getElementsByTagName("logradouro")[0].firstChild.nodeValue;
												var resultado = item.getElementsByTagName("resultado")[0].firstChild.nodeValue;
												var resultado_txt = item.getElementsByTagName("resultado_txt")[0].firstChild.nodeValue;
											} else {
												resultado = 0;
											}
										}


										if (resultado == 1) {
											//document.getElementById("tipo").value = tipo_logradouro;
											document.getElementById("logradouro").value = tipo_logradouro + " " + logradouro;
											document.getElementById("cidade").value = cidade;
										} else {
											verErro('Não foi localizado o cep.');
										}
									}



								} else { //Endereço cobrança
									//processarXMLCob(ajax.responseXML);

									if (dados.length > 0) {
										//percorre o arquivo XML para extrair os dados
										for (var i = 0; i < dados.length; i++) {
											var item = dados[i];
											//conteudo dos campos no arquivo XML
											var uf = item.getElementsByTagName("uf")[0].firstChild.nodeValue;
											var cidade = item.getElementsByTagName("cidade")[0].firstChild.nodeValue;
											var bairro = item.getElementsByTagName("bairro")[0].firstChild.nodeValue;
											var tipo_logradouro = item.getElementsByTagName("tipo_logradouro")[0].firstChild.nodeValue;
											var logradouro = item.getElementsByTagName("logradouro")[0].firstChild.nodeValue;
											var resultado = item.getElementsByTagName("resultado")[0].firstChild.nodeValue;
											var resultado_txt = item.getElementsByTagName("resultado_txt")[0].firstChild.nodeValue;
										}


										if (resultado == 1) {
											//document.getElementById("tipo").value = tipo_logradouro;
											document.getElementById("id_uf").value = uf;
											document.getElementById("id_end").value = tipo_logradouro + " " + logradouro;
											document.getElementById("id_cidade").value = cidade;
										} else {
											verErro('Não foi localizado o cep.');
										}
									}

								}
							} else {
								verErro('Houve um problema aoa carregar');
							}
						}
					}
					ajax.send(null);
				}
			}
		}



		function checa_formulario(cadastro) {


			<?php
			if (($role["credit"]) || ($role["creditManager"]) || ($role["policy"])) {
				?>
				if (document.getElementById("naf").value == "") {
					verErro("O campo NAF não pode ser vazio!");
					document.getElementById("naf").focus();
					return (false);
				}

				if (document.getElementById("napce").value == "") {
					verErro("O campo NAPCE não pode ser vazio!");
					document.getElementById("napce").focus();
					return (false);
				}

				<?php
			}
			?>

			<?php
			if (($state != "10") && ($state != "11") && ($state != "9") && ($state != "6")) {
				?>
				/*
				  Criado por Tiago V N - Elumini - 16/08/2006
				*/
				if (cadastro.nome.value.length > 60) {
					verErro("O tamanho do campo nome é de 60 caracteres");
					cadastro.nome.focus();
					return (false);
				}

				if (cadastro.cnpj.value.length > 14) {
					verErro("O tamanho do campo cnpj é de 14 caracteres");
					cadastro.cnpj.focus();
					return (false);
				}

				if (cadastro.ie.value.length > 20) {
					verErro("O tamanho do campo ie é de 20 caracteres");
					cadastro.ie.focus();
					return (false);
				}

				if (cadastro.nome.value == "") {
					verErro("Por Favor, Preencha o nome");
					cadastro.nome.focus();
					return (false);
				}

				if (cadastro.cnpj.value == "") {
					verErro("Por Favor, Preencha o cnpj");
					cadastro.cnpj.focus();
					return (false);
				}
				if (cadastro.ie.value == "") {
					verErro("Por Favor, Preencha a Inscrição estadual ou Municipal");
					cadastro.ie.focus();
					return (false);
				}

				<?php
			} else {

			}
			?>

			/*
			  Criado por Tiago V N - Elumini - 16/08/2006
			*/


			if (cadastro.endereco.value == "") {
				verErro('Campo endereço não pode ser vazio.');
				cadastro.endereco.focus();
				return false;
			}

			if (cadastro.endereco.value.length > 100) {
				verErro("O tamanho do campo endereço é de 100 caracteres");
				cadastro.numero.focus();
				return (false);
			}

			if (cadastro.cep.value == "") {
				verErro('Campo CEP não pode ser vazio.');
				cadastro.cep.focus();
				return false;
			}

			if (cadastro.cep.value.length > 10) {
				verErro("O tamanho do campo cep é de 10 caracteres");
				cadastro.cep.focus();
				return (false);
			}

			if (cadastro.cidade.value == "") {
				verErro('Campo cidade não pode ser vazio.');
				cadastro.cidade.focus();
				return false;
			}

			if (cadastro.cidade.value.length > 50) {
				verErro("O tamanho do campo cidade é de 50 caracteres");
				cadastro.cidade.focus();
				return (false);
			}

			if (cadastro.fax.value.length > 30) {
				verErro("O tamanho do campo fax é de 30 caracteres");
				cadastro.fax.focus();
				return (false);
			}

			if (cadastro.numero.value == "") {
				verErro('Campo numero não pode ser vazio.');
				cadastro.numero.focus();
				return false;
			}

			if (cadastro.numero.value.length > 5) {
				verErro('O tamanho do campo numero é de 5 caracteres');
				cadastro.numero.focus();
				return (false);
			}

			if (cadastro.tel.value.length > 30) {
				verErro("O tamanho do campo telefone é de 30 caracteres");
				cadastro.tel.focus();
				return (false);
			}

			if (cadastro.respName.value == "") {
				verErro('Nome do responsável não pode ser vazio.');
				cadastro.respName.focus();
				return false;
			}

			if (cadastro.cob.value == 0 && cadastro.chargeAddress.value.length > 100) {
				verErro('O tamanho do campo Endereço de cobrança é de 100 caracteres');
				cadastro.chargeAddress.focus();
				return (false);
			}


			if (cadastro.cob.value == 0 && cadastro.chargeAddressNumber.value.length > 5) {
				verErro("O tamanho do campo numero do endereço de cobrança é de 5 caracteres");
				cadastro.chargeAddressNumber.focus();
				return (false);
			}

			if (cadastro.cob.value == 0 && cadastro.chargeAddressComp.value.length > 200) {
				verErro("O tamanho do campo complemento de cobraça é de 200 caracteres");
				cadastro.chargeAddressComp.focus();
				return (false);
			}

			if (cadastro.cob.value == 0 && cadastro.chargeCep.value.length > 10) {
				verErro("O tamanho do campo cep de cobrança é de 10 caracteres");
				cadastro.chargeCep.focus();
				return (false);
			}

			if (cadastro.cob.value == 0 && cadastro.chargeCity.value.length > 50) {
				verErro("O tamanho do campo da cidade de cobrança é de 50 caracteres");
				cadastro.chargeCity.focus();
				return (false);
			}


			if (cadastro.cob.value == 0 && (
				cadastro.abrev.value.length +
				cadastro.chargeCity.value.length +
				cadastro.chargeAddressComp.value.length +
				cadastro.chargeAddressNumber.value.length) > 60) {

				verErro("Por Favor, A soma dos campos Endereço de Cobrança + Numero + Complemento + Cidade" +
					"não pode ser maior que 60 caracteres.");
				return (false);
			}

			if (cadastro.tel.value == "") {
				verErro("Por Favor, Preencha o Telefone");
				cadastro.tel.focus();
				return (false);
			}
			if (cadastro.email.value == "") {
				verErro("Por Favor, Preencha o E-mail da Empresa");
				cadastro.email.focus();
				return (false);
			}
			if (cadastro.email.value.indexOf('@', 0) == -1) {
				verErro("O E-mail da Empresa é Inválido !!!");
				cadastro.email.focus();
				return (false);
			}
			if (cadastro.contact.value == "") {
				verErro("Por Favor, Preencha o Contato");
				cadastro.contact.focus();
				return (false);
			}

			if (cadastro.cob.value == 1 && cadastro.abrev.value == "") {
				verErro("Por Favor, Preencha o endereço abreviado");
				cadastro.abrev.focus();
				return (false);
			}


			if (cadastro.emailContact.value == "") {
				verErro("Por Favor, Preencha o E-mail do Contato");
				cadastro.emailContact.focus();
				return (false);
			}
			if (cadastro.emailContact.value.indexOf('@', 0) == -1) {
				verErro("O E-mail do Contato é Inválido !!!");
				cadastro.emailContact.focus();
				return (false);
			}
			if (cadastro.cob.value == 0 && cadastro.chargeAddress.value == "") {
				verErro("Por Favor, Informe o Endereço para Cobrança");
				cadastro.chargeAddress.focus();
				return (false);
			}
			if (cadastro.cob.value == 0 && cadastro.chargeCity.value == "") {
				verErro("Por Favor, Informe a Cidade para Cobrança");
				cadastro.chargeCity.focus();
				return (false);
			}
			if (cadastro.cob.value == 0 && cadastro.chargeUf.value == 0) {
				verErro("Por Favor, Selecione um Estado para Cobrança");
				cadastro.chargeUf.focus();
				return (false);
			}
			if (cadastro.cob.value == 0 && cadastro.chargeCep.value == "") {
				verErro("Por Favor, Informe o CEP para Cobrança");
				cadastro.chargeCep.focus();
				return (false);
			}
			if (cadastro.cob.value == 0 && cadastro.chargeAddressAbrev.value == "") {
				verErro("Por Favor, Informe o endereço abreviado");
				cadastro.chargeAddressAbrev.focus();
				return (false);
			}
			return (true);
		}

		function ShowHide(id_menu) {
			if (id_menu == 0) {
				document.all.id_end.style.display = 'block';
				document.all.id_num.style.display = 'block';
				document.all.id_comp.style.display = 'block';
				document.all.id_cidade.style.display = 'block';
				document.all.id_cep.style.display = 'block';
				document.all.id_uf.style.display = 'block';
				document.all.id_abrev.style.display = 'block';

				document.all.id_abrev_no.style.display = 'none';
				document.all.id_abrev_n.style.display = 'none';
				document.getElementById('imgfoto').style.display = 'block';
			} else if (id_menu == 1) { //Utiliza endereço acima
				document.all.id_end.style.display = 'none';
				document.all.id_num.style.display = 'none';
				document.all.id_comp.style.display = 'none';
				document.all.id_cidade.style.display = 'none';
				document.all.id_cep.style.display = 'none';
				document.all.id_uf.style.display = 'none';
				document.getElementById('imgfoto').style.display = 'none';
				document.all.id_abrev.style.display = 'none';
				document.all.id_abrev_no.style.display = 'block';
				document.all.id_abrev_n.style.display = 'block';

			}
		}

		function Ncnpj() {
			if (isNaN(document.all.cnpj.value)) {
				verErro("O campo cnpj deve conter apenas numeros!");
				document.all.cnpj.focus();
				return false;
			}
		}

		function gerarPdf(f) {
			f.comm.value = "pdf";
			f.submit();
		}

	</script>

	<?php include_once("../../../navegacao.php"); ?>

	<div class="conteudopagina">
		<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">

			<input type="hidden" name="naf" id="naf" value="<?php echo $field->getDBField("naf", 17); ?>">
			<input type="hidden" name="napce" id="napce" value="<?php echo $field->getDBField("naf", 18); ?>">
			<ul>
				<?php
				if (!$role["client"]) {
					?>
					<li class="campo2colunas">
						<label>NAF:</label>
						<?php
						if (($role["credit"]) || ($role["creditManager"]) || ($role["policy"])) {
							?>
							<input type="text" class="caixa" name="naf" id="naf" maxlength="50" style="width:70px"
								value="<?php echo ($field->getDBField("naf", 17)); ?>">
						<?php } else { ?>

							<?php echo ($field->getDBField("naf", 17)); ?>

						<?php } ?>

					</li>
					<li class="campo2colunas">
						<label>SIREN N&ordm; E175:</label>
						<?php echo ($field->getDBField("siren", 19)); ?>
					</li>
					<li class="campo2colunas">
						<label>QUESTIONNAIRE:</label>
						<?php echo ($field->getDBField("quest", 21)); ?>
					</li>
					<li class="campo2colunas">
						<label>NAPCE:</label>
						<?php
						if (($role["credit"]) || ($role["creditManager"])) {
							?>
							<input type="text" name="napce" id="napce" value="<?php echo ($field->getDBField("napce", 18)); ?>">
							<?php
						} else {
							?>
							<?php echo ($field->getDBField("napce", 18)); ?>
							<?php
						}
						?>
					</li>
					<li class="campo2colunas">
						<label>DOSSIER:</label>
						<?php echo ($field->getDBField("dossier", 20)); ?>
					</li>
					<li class="campo2colunas">
						<label>CONTRAT:</label>
						<?php echo ($field->getDBField("contrat", 22)); ?>
					</li>
					<?php
				}
				?>
			</ul>
			<ul>
				<li class="campo2colunas">
					<label>Cobertura para juros de mora:</label>
					<?php
					// alterado Hicom (Gustavo)
					if ($role["executive"] && ($state == 1 || $state == 2 || $state == 3 || $state == 4 || $state == 5)) {

						?>
						<div class="formopcao">
							<input name="warantyInterest" type="radio" value="1" <?php if ($field->getDBField("warantyInterest", 2) == 1) {
								echo "checked ";
							} ?> />
						</div>
						<div class="formdescricao"><span>SIM</span></div>
						<div class="formopcao">
							<input name="warantyInterest" type="radio" value="0" <?php if ($field->getDBField("warantyInterest", 2) == 0) {
								echo "checked ";
							} ?> />
						</div>
						<div class="formdescricao"><span>N&Atilde;O</span></div>
						<button name="alt_cobertura" onClick="javascript:
				this.form.comm.value='generalInformation';
				this.form.submit();" class="botaoagg">Alterar Cobertura</button>
						<input type="hidden" name="executa" value="1" />

						<?php
						if ($field->getDBField("pvigencia", 125) == "") {
							$pvigencia = "1";
						} else if ($field->getDBField("pvigencia", 125) == "1") {
							$pvigencia = "1";
						} else {
							$pvigencia = "2";
						}
						?>

					<li class="campo2colunas">
						<label>Per&iacute;odo de Vig&ecirc;ncia: Meses</label>
						<?php
						if ($state == "1" || $state == "2") {
							?>

							<input type="hidden" name="pvigencia" id="pvigencia" value="<?php echo $pvigencia; ?>">
							<input type="hidden" name="Periodo_Vigencia" id="Periodo_Vigencia"
								value="<?php echo ($Periodo_Vigencia ? $Periodo_Vigencia : $pvigencia); ?>">
							<?php
						} else {
							if ($Periodo_Vigencia) {
								echo $Periodo_Vigencia . ' Meses';
							} else {
								if ($pvigencia == "1") {
									echo "12 Meses";
								} else if ($pvigencia == "2") {
									echo "24 Meses";
								}
							}
						}
						?>
					</li>

					<?php
					} else {
						?>
					<?php echo $field->getDBField("warantyInterest", 2) ? "Sim" : "N&atilde;o"; ?>
					</li>
					<li class="campo2colunas">
						<label>Per&iacute;odo de Vig&ecirc;ncia:</label>
						<?php if ($Periodo_Vigencia) {
							echo ($Periodo_Vigencia . " Meses");
						} else {
							echo ($pvigencia);
						} ?>
					</li>

					<?php
					}
					?>
				<?php
				if ($state == "10" or $state == "11") { //Quando e Apólice // Análise de Credito
					if (
						$role["executive"] ||
						$role["credit"] || $role["creditManager"] ||
						$role["creditInform"] || $role["sinistro"] ||
						$role["tariffer"]
					) {
						?>
						<li class="campo2colunas">
							<label>Limite m&aacute;ximo de indeniza&ccedil;&atilde;o:</label>
							<?php
							if ($v_LMI > 0) {
								echo $ext . " " . number_format($v_LMI, 2, ',', '.');
							} else {
								echo $ext . " " . number_format(($limPagIndeniz * $PrMtotal), 2, ',', '.');
							}
							?>
						</li>
						<li class="campo2colunas">
							<label>Percentual de cobertura</label>
							<?php echo ($percCoverage); ?>%
						</li>
						<li class="campo2colunas">
							<label>Faturamento Segurável</label>
							<?php echo ($ext); ?>&nbsp;<?php echo ($caex); ?>
						</li>
						<?php
					}
				}

				if ($ga == 1)  //Se for GA
				{
					if (!$mBonus == "1") {
						if ($perBonus != "0") {
							$modulo = "F9.02 - B&ocirc;nus por Aus&ecirc;ncia de Sinistros<br>";
							$modulo = $modulo . "Percentual do b&ocirc;nus por aus&ecirc;ncia de sinistros: $perBonus% <br><br>";
						}
					} else if ($mBonus == "2") {
						$modulo = "$pLucro.02 - Participa&ccedil;&atilde;o nos Lucros<br>";
						$modulo = $modulo . "Percentagem de Dedu&ccedil;&atilde;o: $perPart0% ( $numberExtensive->porcentagem($perPart0))<br>";
						$modulo = $modulo . "Participa&ccedil;&atilde;o nos Lucros: $perPart1% ( $numberExtensive->porcentagem($perPart1)) <br><br>";
					}
					?>
				</ul>
				<div style="clear:both">&nbsp;</div>
				<ul>
					<li class="campo2colunas" style="height: 20px;">
						<label>PAR&Acirc;METROS DE M&Oacute;DULOS ESPECIAIS</label>
						<?php echo isset($modulo) ? $modulo : ""; ?>
					</li>
					<?php
				} //fecha o if($ga==1)
				?>
			</ul>
			<!-- <li class="campo2colunas">-->
			<div style="height:auto; width:930px;clear:both">
				<span>

					<?php require_once('interf/includes/modulos.php'); ?>

				</span>
				<!-- </li>-->
			</div>
			<div style="clear:both">&nbsp;</div>
			<ul>
				<li id="clear" class="campo2colunas" style="width:970px;">
					<label>OBS</label>
					<?php


					if ($obs == "") { // Se o OBS esta vazio
				
						if ($role["creditManager"] || $role["credit"] || $role["policy"]) {
							?>
							<textarea name="obs" rows="3" cols="60"><?php echo ($obs); ?></textarea>
							<?php
						}
					} else // Se o OBS já tem conteúdo
					{
						if ($role["creditManager"] || $role["credit"] || $role["policy"]) //Pode alterar o conteúdo de OBS
						{
							?>
							<textarea name="obs" rows="3" cols="60"><?php echo ($obs); ?></textarea>

							<?php
						} else //Não pode alterar o conteúdo de OBS
						{
							?>
							<?php echo ($obs); ?>
							<?php
						}
					}
					// }
					?>
				</li>
			</ul>
			<div class="divisoria01">&nbsp;</div>
			<ul>
				<li class="campo2colunas">&nbsp;</li>
				<li class="campo2colunas">&nbsp;</li>
			</ul>
			<div style="clear:both">&nbsp;</div>
			<div style="clear:both">&nbsp;</div>

			<div class="barrabotoes"></div>
			<p><b>DADOS DO SEGURADO</b></p>
			<ul>
				<li class="campo2colunas">
					<label>Nome:</label>
					<?php
					if ($state == 10 || $state == 11 || $state == 9 || $state == 6) {
						?>
						<?php echo ($field->getDBField("name", 15)); ?>
					<?php } else { ?>
						<input type="text" name="nome" value="<?php echo ($field->getDBField("name", 15)); ?>">
					<?php } ?>
				</li>
				<li class="campo2colunas">
					<label>Logradouro:</label>
					<input type="text" name="endereco" id="logradouro"
						value="<?php echo ($field->getDBField("address", 26)); ?>">
				</li>
				<li class="campo2colunas">
					<label>N&ordm;:</label>
					<input type="Text" name="numero" value="<?php echo ($addressNumber); ?>">
				</li>
				<li class="campo2colunas">
					<label>Complemento:</label>
					<input type="Text" name="comp" value="<?php echo ($addressComp); ?>">
				</li>
				<li class="campo2colunas">
					<label>Logradouro Abrev:</label>
					<input type="Text" id="id_abrev_n" name="abrev" maxlength="25" value="<?php echo ($abrev); ?>">
				</li>
				<?php
				if ($sameAddress == 0 || $cob = 0) { //$cob 0 - Não
			
					echo "
      <script>
            document.all.id_abrev_n.style.display='none';
            document.all.id_abrev_no.style.display='none';
            //document.getElementById('imgfoto').style.display='block';
      </script>";

				} else if ($sameAddress == 1 || $cob == 1) { //$cob 1  - Sim
					echo "<script>
            document.all.id_abrev_n.style.display='block';
            document.all.id_abrev_no.style.display='block';
            //document.getElementById('imgfoto').style.display='none';
            </script>";
				}

				?>
				<li class="campo2colunas">
					<label>Cidade:</label>
					<input type="text" name="cidade" id="cidade" value="<?php echo ($field->getDBField("city", 27)); ?>">
				</li>
				<li class="campo2colunas">
					<label>CEP:</label>
					<input type="text" name="cep" id="cep" maxlength="10" style="width:82%;"
						value="<?php echo $field->getDBField("cep", 29); ?>">
					<a href="#" onclick="dados(1)"><img src="interf/busca.gif" alt="Consulta de Cep"></a>
				</li>
				<?php
				if ($state <> 9 && $state <> 11) { ?>
					<li class="campo2colunas">
						<label>Telefone (com DDD):</label>
						<input type="text" name="tel" maxlength="10"
							value="<?php echo $field->getDBField("tel", 30); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</li>
					<li class="campo2colunas">
						<label>Fax:</label>
						<input type="text" maxlength="10" name="fax" value="<?php echo $field->getDBField("fax", 31); ?>">
					</li>
					<?php
				} else {
					?>
					<li class="campo2colunas">
						<label>Telefone (com DDD):</label>
						<?php echo $field->getDBField("tel", 30); ?>
					</li>
					<li class="campo2colunas">
						<label>Fax:</label>
						<?php echo $field->getDBField("fax", 31); ?>
					</li>
					<?php
				}
				if ($state <> 9 && $state <> 11) {
					?>
					<li class="campo2colunas">
						<label>Email da Empresa:</label>
						<input type="text" name="email" value="<?php echo ($field->getDBField("email", 32)); ?>"
							onblur="checa_email(this.form,'email')">
					</li>
					<?php
				} else {
					?>
					<li class="campo2colunas">
						<label>Email da Empresa:</label>
						<?php echo ($field->getDBField("email", 32)); ?>
					</li>
					<?php
				}
				if ($state <> 9 && $state <> 11) {
					?>
					<li class="campo2colunas">
						<label>Contato:</label>
						<input type="text" name="contact" maxlength="100"
							value="<?php echo ($field->getDBField("contact", 33)); ?>">
					</li>
					<li class="campo2colunas">
						<label>Cargo:</label>
						<input type="text" maxlength="100" name="ocupationContact"
							value="<?php echo ($field->getDBField("ocupationContact", 34)); ?>">
					</li>
					<?php
				} else {
					?>
					<li class="campo2colunas">
						<label>Contato:</label>
						<?php echo ($field->getDBField("contact", 33)); ?>
					</li>
					<li class="campo2colunas">
						<label>Cargo:</label>
						<?php echo ($field->getDBField("ocupationContact", 34)); ?>
					</li>
					<?php
				}
				if ($state <> 9 && $state <> 11) {
					?>
					<li class="campo2colunas">
						<label>E-mail do contato:</label>
						<input type="text" maxlength="60" name="emailContact"
							value="<?php echo ($field->getDBField("emailContact", 35)); ?>"
							onblur="checa_email(this.form,'emailContact')">
					</li>
					<li class="campo2colunas">
						<label>Respons&aacute;vel:</label>
						<input type="text" maxlength="60" name="respName"
							value="<?php echo ($field->getDBField("Cargo responsável", 'respName')); ?>">
					</li>
					<li class="campo2colunas">
						<label>Cargo:</label>
						<input type="text" maxlength="60" name="ocupation"
							value="<?php echo ($field->getDBField("Email responsável", 'ocupation')); ?>">
					</li>

					<?php
				} else {
					?>
					<li class="campo2colunas">
						<label>E-mail do contato:</label>
						<?php echo ($field->getDBField("emailContact", 35)); ?>
					</li>
					<?php
				}
				?>
				<li class="campo2colunas">
					<label>CNPJ:</label>
					<?php if ($state == 10 || $state == 11 || $state == 9 || $state == 6) { ?>

						<?php echo $field->getDBField("cnpj", 36); ?>&nbsp;<a
							href="http://www.receita.fazenda.gov.br/pessoajuridica/CNPJ/cnpjreva/cnpjreva_solicitacao.asp"
							target="_blank">Cons. CNPJ</a>
					<?php } else { ?>

						<input type="text" class="texto" name="cnpj" value="<?php echo $field->getDBField("cnpj", 36); ?>"
							onBlur="Ncnpj()" style="width:75%;">&nbsp;<a
							href="http://www.receita.fazenda.gov.br/pessoajuridica/CNPJ/cnpjreva/cnpjreva_solicitacao.asp"
							target="_blank">Cons. CNPJ</a>

					<?php } ?>
				</li>
				<li class="campo2colunas">
					<label>Inscri&ccedil;&atilde;o Estadual ou Municipal:</label>
					<?php if (
						$state == 10 || $state == 11
						|| $state == 9 || $state == 6
					) {
						?>
						<?php echo $field->getDBField("ie", 37); ?>
					<?php } else {
						?>
						<input type="text" class="texto" name="ie" value="<?php echo $field->getDBField("ie", 37); ?>">
						<?php
					}
					?>
				</li>
				<li class="campo2colunas">
					<label>Regi&atilde;o:</label>
					<?php
					$sql = "SELECT id, description FROM Region ORDER BY name";
					$sel = $field->getDBField("idRegion", 55);
					$name = "idRegion";
					$disabled = true;
					require_once("../../interf/Select.php");
					?>
				</li>
				<li class="campo2colunas">
					<label>Origem do neg&oacute;cio:</label>
					<?php
					if (trim($origemNegocio ?? '') != "") {
						$query = "SELECT n_Frontier, Pais FROM Frontier WHERE n_Frontier = ?";
						$reCur = odbc_prepare($db, $query);
						odbc_execute($reCur, [$origemNegocio]);
						echo odbc_result($reCur, 'Pais');
					}

					?>
				</li>
				<li class="campo2colunas">
					<label>Corretor:</label>
					<?php

					$cons = "SELECT a.idconsultor, a.razao, a.c_SUSEP
						FROM consultor a
						INNER JOIN Inform b ON (b.idConsultor = a.idconsultor)
						WHERE b.id = ?";
					$resultado = odbc_prepare($db, $cons);
					odbc_execute($resultado, [$field->getField("idInform")]);

					if (odbc_fetch_row($resultado)) {
						$Corretor = odbc_result($resultado, 'razao');
						$codigoSusep = odbc_result($resultado, 'c_SUSEP');
						echo ($Corretor);
					} else {
						$Corretor = '';
						$codigoSusep = '';
					}
					odbc_free_result($resultado);
					?>

				</li>
				<li class="campo2colunas">
					<label>Percental de comiss&atilde;o:</label>
					<?php echo $percComissao; ?>&nbsp;%
				</li>
				<li class="campo2colunas">
					<label>Telefone:</label>
					<?php
					$sql = " select idconsultor, telefone from consultor order by razao ";
					$sel = $idConsultor;
					$name = "cmb_tel_consultor";
					$disabled = false;
					$acao = "style='display:none;' disabled";
					$empty = "--Selecione Um Telefone--";
					$empty = "&nbsp;";
					require_once("../../interf/Select.php");
					?>
					<input type="text" name="tel_consultor" value="" disabled
						style="display:block;width:400px;border-bottom: 0px solid White;border-left: 0px solid Black;border-top: 0px solid Black;border-right: 0px solid White;">
				</li>
				<li class="campo2colunas">
					<label>E-Mail:</label>
					<?php
					$sql = " select idconsultor, email from consultor order by razao ";
					$sel = $idConsultor;
					$name = "cmb_email_consultor";
					$disabled = false;
					$acao = "style='display:none;' disabled";
					$empty = "--Selecione Um E-Mail--";
					$empty = "&nbsp;";
					require_once("../../interf/Select.php");
					?>
					<input type="text" name="email_consultor" value="" disabled
						style="display:block;width:400px;border-bottom: 0px solid White;border-left: 0px solid Black;border-top: 0px solid Black;border-right: 0px solid White;">
				</li>
			</ul>
			<div class="divisoria01">&nbsp;</div>

			<div class="barrabotoes"></div>
			<p><b>DADOS PARA COBRAN&Ccedil;A</b></p>
			<ul>
				<li class="campo2colunas">
					<label>Utilizar dados acima?</label>
					<select name="cob" class="texto" onChange="Javascript: ShowHide(this.value);">
						<?php
						$ssim = '';
						$snao = '';
						if ($sameAddress == 1) { //Sim
							$ssim = "Selected";
						} else if ($sameAddress == 0) { //Não
							$snao = "Selected";
						}

						echo "<option value=\"1\" $ssim >Sim</option>";
						echo "<option value=\"0\" $snao >N&atilde;o</option>";
						?>
					</select>
					<input type="hidden" name="sameAddress" value="<?php echo $sameAddress; ?>">
				</li>
				<?php
				if ($state <> 9 && $state <> 11 && !$sameAddress) {
					?>
					<li class="campo2colunas">
						<label>Logradouro:</label>
						<input type="text" id="id_end" maxlength="100" name="chargeAddress"
							value="<?php echo ($chargeAddress); ?>">
					</li>
					<li class="campo2colunas">
						<label>N&ordm;:</label>
						<input type="text" id="id_num" name="chargeAddressNumber" maxlength="5"
							value="<?php echo ($chargeAddressNumber); ?>">
					</li>
					<li class="campo2colunas">
						<label>Complemento:</label>
						<input type="text" id="id_comp" class="texto" name="chargeAddressComp" maxlength="15"
							value="<?php echo ($chargeAddressComp); ?>">
					</li>
					<li class="campo2colunas">
						<label>Logradouro Abrev:</label>
						<input type="Text" id="id_abrev" name="chargeAddressAbrev" maxlength="25"
							value="<?php echo ($chargeAddressAbrev); ?>">
					</li>

					<?php


					if ($sameAddress == 0 || $cob == 0) {
						echo "<script>document.all.id_abrev.style.display='';</script>";
					}

				} else {
					?>
					<li class="campo2colunas">
						<label>Logradouro:</label>
						<input type="text" id="id_end" maxlength="100" name="chargeAddress" style="display: none; "
							value="<?php echo ($chargeAddress); ?>">
					</li>
					<li class="campo2colunas">
						<label>N&ordm;:</label>
						<input type="Text" id="id_num" name="chargeAddressNumber" style="display: none;" maxlength="5"
							value="<?php echo ($chargeAddressNumber); ?>">
					</li>
					<li class="campo2colunas">
						<label>Complemento:</label>
						<input type="Text" id="id_comp" name="chargeAddressComp" style="display: none;" maxlength="15"
							value="<?php echo ($chargeAddressComp); ?>">
					</li>
					<li class="campo2colunas">
						<label>Logradouro Abrev:</label>
						<input type="text" id="id_abrev" name="chargeAddressAbrev" style="display: none;" maxlength="20"
							value="<?php echo ($chargeAddressAbrev); ?>">
					</li>

					<?php
				}
				if ($state <> 9 && $state <> 11 && !$sameAddress) {
					?>
					<li class="campo2colunas">
						<label>Cidade:</label>
						<input type="text" id="id_cidade" maxlength="50" name="chargeCity" value="<?php echo ($chargeCity); ?>">
					</li>
					<li class="campo2colunas">
						<label>UF:</label>
						<?php
						echo "<select name=\"chargeUf\" id=\"id_uf\">";
						$sql = "SELECT uf FROM UF ORDER BY uf";
						$sel = $field->getDBField("chargeUf", 60);
						$cur = odbc_exec($db, $sql);
						while (odbc_fetch_row($cur)) {
							if ($sel == odbc_result($cur, 1)) {
								$selec = "Selected";
							} else {
								$selec = "";
							}
							echo "<option value=" . odbc_result($cur, 1) . "  $selec >" . (odbc_result($cur, 1)) . "</option>";
						}
						echo "</select>";
						?>

					</li>
					<li class="campo2colunas">
						<label>CEP:</label>
						<input type="text" id="id_cep" maxlength="10" name="chargeCep" value="<?php echo $chargeCep; ?>">
					</li>

					<?php
				} else {
					?>

					<li class="campo2colunas">
						<label>Cidade:</label>
						<input type="Text" id="id_cidade" maxlength="50" name="chargeCity" style="display : none; "
							value="<?php echo ($chargeCity); ?>">
					</li>
					<li class="campo2colunas">
						<label>UF:</label>
						<select name="chargeUf" id="id_uf" style="display:none;">
							<option value=""></option>
							<?php
							// Monta a lista de UF
							$sql = "SELECT uf FROM UF ORDER BY uf";
							$sel = $field->getDBField("chargeUf", 60);
							$cur = odbc_exec($db, $sql);
							while (odbc_fetch_row($cur)) {
								if ($sel == odbc_result($cur, 1)) {
									$selec = "Selected";
								} else {
									$selec = "";
								}
								echo "<option value=" . odbc_result($cur, 1) . "  $selec >" . (odbc_result($cur, 1)) . "</option>";
							}
							?>
						</select>
					</li>
					<li class="campo2colunas">
						<label>CEP:</label>
						<input type="text" id="id_cep" maxlength="20" style="width:82%; display:none;" name="chargeCep"
							value="<?php echo $chargeCep; ?>">
						<a href="#" onclick="dados(2)"><img src="interf/busca.gif" style="display:none;" id="imgfoto"
								alt="Consulta de Cep" width="46"></a>
					</li>
					<?php
				}
				?>
			</ul>
			<div class="divisoria01"></div>

			<div class="barrabotoes"></div>
			<p><b>ATIVIDADE COMERCIAL</b></p>
			<ul>
				<li class="campo2colunas">
					<label>Setor:</label>
					<select name="setor_col" id="">
						<?php
						// Monta a lista de setores
						$sql = "SELECT id, description FROM Sector ORDER BY description";
						$cur = odbc_exec($db, $sql);
						$sel = $idSetor;
						while (odbc_fetch_row($cur)) {
							if ($sel == odbc_result($cur, 1)) {
								$selec = "Selected";
							} else {
								$selec = "";
							}
							echo "<option value=" . odbc_result($cur, 1) . "  $selec >" . (odbc_result($cur, 2)) . "</option>";
						}
						?>
					</select>
				</li>

				<li class="campo2colunas">
					<label>Produto(s) Comercializados(s):</label>
					<?php echo ($field->getDBField("products", 38)); ?>
				</li>
				<li class="campo2colunas">
					<label>Prazo m&eacute;dio usualmente concedido para pagamento:</label>
					<?php echo ($field->getDBField("frameMed", 42)); ?> dias
				</li>

				<li class="campo2colunas">
					<label>Comercializa h&aacute; mais de tr&ecirc;s anos:</label>
					<?php echo $field->getDBField("exportMore", 91) ? "Sim" : "N&atilde;o"; ?>
				</li>
			</ul>
			<div class="divisoria01"></div>

			<div class="barrabotoes"></div>
			<p><b>INFORMA&Ccedil;&Otilde;ES GERAIS</b></p>
			<ul>
				<li class="campo2colunas">
					<label>A empresa pertence a algum grupo?</label>
					<?php echo $field->getDBField("hasGroup", 44) ? "Sim" : "N&atilde;o"; ?>
				</li>
				<li class="campo2colunas">
					<label>Caso positivo, qual?</label>
					<?php echo ($field->getDBField("companyGroup", 45)); ?>
				</li>
				<li class="campo2colunas">
					<label>A empresa possui companhias associadas no exterior?</label>
					<?php echo $field->getDBField("hasAssocCompanies", 46) ? "Sim" : "N&atilde;o"; ?>
				</li>
				<li class="campo2colunas">
					<label>Caso positivo, quais?</label>
					<?php echo ($field->getDBField("associatedCompanies", 47)); ?>
				</li>
			</ul>
			<div class="divisoria01"></div>

			<div class="barrabotoes"></div>
			<p><b>OBJETIVOS</b></p>
			<ul>
				<li class="campo3colunas">
					[ <?php echo $field->getDBField("warantyExp", 49) ? "X" : "&nbsp;"; ?> ] Garantia &agrave;
					exporta&ccedil;&atilde;o
				</li>
				<li class="campo3colunas">
					[ <?php echo $field->getDBField("warantyFin", 50) ? "X" : "&nbsp;"; ?> ] Garantia para financiamentos
					&agrave; exporta&ccedil;&atilde;o
				</li>
				<li class="campo3colunas">
					[ <?php echo $field->getDBField("hasAnother", 51) ? "X" : "&nbsp;"; ?> ] Outros
				</li>
				<li class="campo2colunas">
					<label>Quais?</label>
					<?php echo $field->getDBField("another", 52); ?>
				</li>
			</ul>
			<p>
				<?php
				if ($msg) {
					echo ($msg);
				}
				?>
			</p>

			<?php //require_once('interf/includes/modulos.php'); ?>


			<input type="hidden" name="idInform" value="<?php echo $field->getField("idInform"); ?>">
			<input type="hidden" name="reltipo" value="inform"><!--Variavel para identificar tipo de relatorio-->
			<input type="hidden" name="comm" value="volVendExt">
			<div class="barrabotoes">
				<button name="voltar" onClick="this.form.comm.value='open';this.form.submit()"
					class="botaoagm">Voltar</button>
				<?php if ($state <> 9 && $state <> 11) { ?>
					<input type="hidden" name="executa" value="2" />
					<button name="alterar" class="botaoagm"
						onClick="javascript:if (checa_formulario(this.form)){this.form.comm.value='generalInformation';this.form.submit();}">Alterar</button>
				<?php } ?>

				<?php

				if (($usuario == 1953) || $role["policy"]) {
					?>
					<button name="alterar_OBS" class="botaoagg" onClick="javascript:
			this.form.comm.value='gravaObs';
			verErro('Atenção! Dados do Informe serão alterados. Caso haja proposta emitida, esta deverá ser cancelada e reenviada.');
			this.form.submit();">Alterar OBS</button>
				<?php } ?>
				<button type="submit" name="ok" class="botaoagm">OK</button>
				<!-- <button class="botaoagm" type="button" onclick="gerarPdf(this.form)">Vers&atilde;o PDF</button>-->

				<button class="botaoagm" type="button"
					onclick="window.open('<?php echo $root ?>role/inform/interf/relatorio_informe.php?inform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>');">Vers&atilde;o
					PDF</button>
			</div>
		</form>

		<?php
} else {
	?>
		<font color=red>
			<p>Informe inv&aacute;lido</p>
		</font>
		<?php
}
?>
</div>