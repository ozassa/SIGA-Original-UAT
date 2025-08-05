<?php

include_once("../../../config.php");

$_SESSION['Configurar'] = '';

include_once("../../../gerar_pdf/MPDF45/mpdf.php");

$Cod_Cessao = $_GET['codigo'];

$idCessao = isset($_REQUEST['idCessao']) ? $_REQUEST['idCessao'] : 0;

//Empresa OTM ou SISSEG
/*$qry = "SELECT b.SistemaDestino, a.n_Apolice, Case a.i_Empresa When 1 Then 1 Else 5 End As Empresa from Inform a inner join Config_Produto b on b.i_Produto = a.i_Produto
			where a.id = '".$_REQUEST['idInform']."' ";    
   $cur = odbc_exec($db,$qry);	
   
   $SistemaDestino  = odbc_result($cur, "SistemaDestino");
   $n_Apolice       = odbc_result($cur, "n_Apolice");
   $n_Empresa       = odbc_result($cur, "Empresa");*/

// Preparando a consulta com um marcador para o parâmetro
$qry = "SELECT b.SistemaDestino, a.n_Apolice, 
		        CASE a.i_Empresa WHEN 1 THEN 1 ELSE 5 END AS Empresa 
		        FROM Inform a 
		        INNER JOIN Config_Produto b ON b.i_Produto = a.i_Produto
		        WHERE a.id = ?";

// Preparando a query
$stmt = odbc_prepare($db, $qry);

// Executando a consulta com o parâmetro passado
odbc_execute($stmt, array($_REQUEST['idInform']));

// Recuperando os resultados
$SistemaDestino = odbc_result($stmt, "SistemaDestino");
$n_Apolice = odbc_result($stmt, "n_Apolice");
$n_Empresa = odbc_result($stmt, "Empresa");

odbc_free_result($stmt);


/*$x = odbc_exec($db,
	   "SELECT inf.name, inf.address, inf.city, inf.uf, inf.cnpj, inf.ie,
	   inf.dateEmission, inf.i_Seg, inf.endValidity, inf.percCoverage,
	   inf.startValidity, inf.contrat, inf.prodUnit, inf.cep, inf.nProp,
	   r.name, inf.policyKey,currency
	   from Inform inf
	   JOIN Region r ON (r.id = inf.idRegion)
	   where inf.id = $idInform");*/

// Preparando a consulta com marcador de parâmetro
$qry = "SELECT inf.name, inf.address, inf.city, inf.uf, inf.cnpj, inf.ie,
		inf.dateEmission, inf.i_Seg, inf.endValidity, inf.percCoverage,
		inf.startValidity, inf.contrat, inf.prodUnit, inf.cep, inf.nProp,
		r.name, inf.policyKey,currency
		from Inform inf
		Left JOIN Region r ON (r.id = inf.idRegion)
		where inf.id = ?";


// Preparando a consulta
$x = odbc_prepare($db, $qry);

// Executando a consulta com o parâmetro $idInform
odbc_execute($x, [$idInform]);

$segurado = odbc_result($x, 1);
	$address = odbc_result($x, 2);
	$city = odbc_result($x, 3);
	$estado = odbc_result($x, 4);
	$cnpj = arruma_cnpj(odbc_result($x, 5));
	$moeda = odbc_result($x, "currency");
	$key = odbc_result($x, 17);

	$ie = odbc_result($x, 6);
	$ie = preg_match("/^[0-9]+$/", $ie) ? number_format($ie, 0, '', '.') : $ie;
	$emissao = ymd2dmy(odbc_result($x, 7));
	$iSeg = odbc_result($x, 8);
	$final = ymd2dmy(odbc_result($x, 9));
	$cobertura = (int) odbc_result($x, 10);
	$cobertura .= "% (". $numberExtensive->extensive($cobertura, 3). " por cento)";
	$inicio_vig = ymd2dmy(odbc_result($x, 11));
	$contrat = odbc_result($x, 12);
	$prod = odbc_result($x, 13);
	$cep = odbc_result($x, 14);
	$nProp = odbc_result($x, 15);


$sql = "SELECT a.id, a.name, c.login 
        FROM Role a 
        INNER JOIN UserRole b ON b.idRole = a.id 
        INNER JOIN Users c ON c.id = b.idUser
        WHERE c.id = ? 
        ORDER BY a.name, c.login";

$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$userID]);


$x = 0;
while (odbc_fetch_row($cur)) {
	$x = $x + 1;
	$name = odbc_result($cur, 'name');
	$id = odbc_result($cur, 'id');
	$role[$name] = $id . '<br>';
}

if (!function_exists('getStrDate')) {
	function getStrDate($str)
	{
		if (trim($str) == "") {
			return null;
		}
		$row = explode('-', $str);
		$ret = $row[2] . "/" . $row[1] . "/" . $row[0];

		if ($ret == '//')
			return '';

		return $ret;
	}
}
odbc_free_result($cur);

//print '?'.$role["bancoBB"];
$sql = "SELECT COUNT(idUser) AS Id FROM UsersNurim WHERE idUser = ?";
$rr = odbc_prepare($db, $sql);
odbc_execute($rr, [$userID]);

$tipoBanco = isset($_REQUEST['tipoBanco']) ? (int) htmlspecialchars($_REQUEST['tipoBanco']) : 0;

if ($role["bancoBB"]) {
	if (odbc_result($rr, 'Id') > 0) {
		$query = "
            SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
            FROM Inform inf
            JOIN UsersNurim us ON (us.idUser = ?)
            JOIN Nurim nu ON (nu.id = us.idNurim)
            JOIN Agencia ag ON (ag.idNurim = nu.id)
            JOIN CDBB cd ON (cd.idAgencia = ag.id)
            JOIN Importer imp ON (imp.idInform = inf.id)
            JOIN Country c ON (c.id = imp.idCountry)
            JOIN CDBBDetails cdd ON (cdd.idCDBB = cd.id AND cdd.idImporter = imp.id)
            WHERE inf.id = ? AND cd.status = ? AND imp.state NOT IN (7, 8, 9)
            ORDER BY imp.name";
		$cur = odbc_prepare($db, $query);
		odbc_execute($cur, [$userID, $idInform, 2]);
	} else {
		if ($tipoBanco === 1) {
			$query = "
                SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id  
                FROM Importer imp 
                JOIN Country c ON (imp.idCountry = c.id) 
                JOIN CDBBDetails cd ON (imp.id = cd.idImporter) 
                JOIN CDBB cb ON (cd.idCDBB = cb.id) 
                WHERE imp.idInform = ? AND cb.status <> 10 AND cb.id = ? 
                AND imp.state NOT IN (7, 8, 9) 
                ORDER BY imp.name";
			$cur = odbc_prepare($db, $query);
			odbc_execute($cur, [$idInform, $idCessao]);
		} else {
			$query = "
                SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id 
                FROM Importer imp 
                JOIN Country c ON (imp.idCountry = c.id) 
                JOIN CDParcDetails cd ON (imp.id = cd.idImporter) 
                JOIN CDParc cb ON (cd.idCDParc = cb.id) 
                WHERE imp.idInform = ? AND cb.status <> 10 AND cb.id = ? 
                AND imp.state NOT IN (7, 8, 9) 
                ORDER BY imp.name";
			$cur = odbc_prepare($db, $query);
			odbc_execute($cur, [$idInform, $idCessao]);
		}
	}
} else if ($role["bancoParc"]) {
	$query = "
        SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
        FROM Inform inf
        JOIN Banco bc ON (bc.idUser = ?)
        JOIN Agencia ag ON (ag.idBanco = bc.id)
        JOIN CDParc cd ON (cd.idAgencia = ag.id)
        JOIN Importer imp ON (imp.idInform = inf.id)
        JOIN Country c ON (c.id = imp.idCountry)
        JOIN CDParcDetails cdd ON (cdd.idCDParc = cd.id AND cdd.idImporter = imp.id)
        WHERE inf.id = ? AND cd.status = ? AND imp.state NOT IN (7, 8, 9)
        ORDER BY imp.name";
	$cur = odbc_prepare($db, $query);
	odbc_execute($cur, [$userID, $idInform, 2]);
} else {
	$query = "
        SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
        FROM Inform inf
        JOIN Banco bc ON (bc.idUser = ?)
        JOIN CDOB cd ON (cd.idBanco = bc.id)
        JOIN Importer imp ON (imp.idInform = inf.id)
        JOIN Country c ON (c.id = imp.idCountry)
        JOIN CDOBDetails cdd ON (cdd.idCDOB = cd.id AND cdd.idImporter = imp.id)
        WHERE inf.id = ? AND cd.status = ? AND imp.state NOT IN (7, 8, 9)
        ORDER BY imp.name";
	$cur = odbc_prepare($db, $query);
	odbc_execute($cur, [$userID, $idInform, 2]);
}
odbc_free_result($cur);
odbc_free_result($rr);

function ymd2dmy($d)
{
	if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)) {
		return "$v[3]/$v[2]/$v[1]";
	}

	return $d;
}

function arruma_cnpj($c)
{
	if (strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)) {
		return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
	}

	return $c;
}

/* function getEndDate($d, $n, $c = 0)
{
	global $idDVE, $db, $idInform;
	$sql = "SELECT num FROM DVE WHERE id = ?";
	$num = odbc_prepare($db, $sql);

	odbc_execute($num, [$idDVE]);

	if ($num != 12) {
		odbc_free_result($num);
		if (preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})/", $d, $v)) {
			return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
		} else if (preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})/", $d, $v)) {
			return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
		}
	} else {
		odbc_free_result($num);

		$sql = "SELECT endValidity FROM Inform WHERE id = ?";
		$end = odbc_prepare($db, $sql);
		odbc_execute($end, [$idInform]);

		return ymd2dmy($end);
	}
} */

if (!function_exists('getTimeStamp')) {
	function getTimeStamp($date)
	{
		if (preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $date, $res)) {
			return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
		} else if (preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/", $date, $res)) {
			return mktime(0, 0, 0, $res[2], $res[1], $res[3] + 2000);
		}
	}
}

if ($moeda == "1") {
	$extmoeda = "R$";
} else if ($moeda == "2") {
	$extmoeda = "US$";
} else if ($moeda == "6") {
	$extmoeda = "€";
} else {
	$extmoeda = $moeda;
}

if (!$key) {
	$key = session_id() . time();
	$sql = "UPDATE Inform SET policyKey = ? WHERE id = ?";
	$stmt = odbc_prepare($db, $sql);
	odbc_execute($stmt, [$key, $idInform]);

	odbc_free_result($stmt);
}

$sql = "SELECT E.t_Parcela AS t_Doc, E.v_Parcela AS v_Documento
        FROM OIM_Parcela E
        WHERE E.n_Empresa = ?
          AND E.n_Apolice = ?
          AND E.t_Parcela IN (100, 3000)
          AND E.d_Pagamento IS NULL
          AND E.d_Vencimento < GetDate()
          AND E.d_Cancelamento IS NULL
          AND ISNULL(E.v_Parcela, 0) <> 0
        ORDER BY E.n_Endosso, E.n_Parcela";

$x2 = odbc_prepare($db, $sql);
odbc_execute($x2, [$n_Empresa, $n_Apolice]);

$financeiro = 'Pendente';
$financeiro_texto = '';

if (odbc_fetch_row($x2)) {
	do {
		$tdoc = odbc_result($x2, 1);
		$valor = $extmoeda . ' ' . number_format(odbc_result($x2, 2), 2, ',', '.');

		if ($tdoc == 2 || $tdoc == 100) {
			if ($financeiro_texto) {
				$financeiro_texto .= "<br>Parcela de Prêmio no valor de $valor";
			} else {
				$financeiro_texto .= "Parcela de Prêmio no valor de $valor";
			}
		} else if ($tdoc == 3000) {
			if ($financeiro_texto) {
				$financeiro_texto .= "<br>Parcela de Análise e Monitoramento no valor de $valor";
			} else {
				$financeiro_texto .= "Parcela de Análise e Monitoramento no valor de $valor";
			}
		}
	} while (odbc_fetch_row($x2));
} else {
	$financeiro = 'OK';
	$financeiro_texto = 'Não há pendências até o presente momento';
}

odbc_free_result($x2);

$sql = "SELECT E.t_Parcela AS t_Doc, E.v_Parcela AS v_Documento
        FROM OIM_Parcela E
        WHERE E.n_Apolice = ?
          AND E.t_Parcela IN (3)
          AND E.d_Vencimento < GETDATE()";

$x3 = odbc_prepare($db, $sql);
odbc_execute($x3, [$n_Apolice]);

if (odbc_fetch_row($x3)) {
	$AnaliseMonitor = 'Pendente';
	$AnaliseMonitor_texto = '';

	do {
		$tdoc = odbc_result($x3, 1);
		$valor = ($tdoc == 2 ? 'US$ ' : 'R$ ') . number_format(odbc_result($x3, 2), 2, ',', '.');

		if ($tdoc == 3000) {
			if ($AnaliseMonitor_texto) {
				$AnaliseMonitor_texto .= "<br>Parcela de Análise e Monitoramento no valor de $valor";
			} else {
				$AnaliseMonitor_texto .= "Parcela de Análise e Monitoramento no valor de $valor";
			}
		}
	} while (odbc_fetch_row($x3));
} else {
	$AnaliseMonitor = 'OK';
	$AnaliseMonitor_texto = 'Não há pendências até o presente momento';
}

odbc_free_result($x3);


// verifica as DVE's
$dia = date('d');
$ano = date('Y');

if ($dia <= 15) {
	$mes = date('m') - 2;
} else {
	$mes = date('m');
}
$dve_pendente = 0;

//$x = odbc_exec($db, "SELECT inicio, periodo, state, id from DVE where idInform=$idInform and (state=1 or state=3)");

$sql = "SELECT D.inicio, D.periodo, D.state, D.id, 
               ISNULL(DATEADD(D, -1, DD.inicio), Inf.endValidity) AS Fim_Periodo, 
               ISNULL(Inf.Prazo_Entrega_DVN, 15) AS Prazo_Entrega_DVN,
               DATEADD(D, ISNULL(Inf.Prazo_Entrega_DVN, 15), ISNULL(DATEADD(D, -1, DD.inicio), Inf.endValidity)) AS Prazo_Declaracao,
               CASE 
                   WHEN DATEADD(D, ISNULL(Inf.Prazo_Entrega_DVN, 15), ISNULL(DATEADD(D, -1, DD.inicio), Inf.endValidity)) < GETDATE() THEN 1
                   ELSE 0
               END AS Periodo_Atrasado
        FROM Inform Inf
        INNER JOIN DVE D ON D.idInform = Inf.id
        LEFT JOIN DVE DD ON DD.idInform = Inf.id AND DD.num = D.num + 1
        WHERE Inf.id = ? AND D.state = 1";

$x4 = odbc_prepare($db, $sql);
odbc_execute($x4, [$idInform]);

if (odbc_fetch_row($x4)) {
	$dve_texto = '';
	$dve = 'Pendente';

	do {
		$inicio = ymd2dmy(odbc_result($x4, 1));
		$state = odbc_result($x4, 3);
		$idDVE = odbc_result($x4, 4);

		//$fim = getEndDate($inicio, odbc_result($x4, 2));
		$fim = ymd2dmy(odbc_result($x4, "Fim_Periodo"));

		$fim_stamp = getTimeStamp($fim) + (15 * 24 * 3600); // fim do periodo + 15 dias
		$Periodo_Atrasado = odbc_result($x4, "Periodo_Atrasado");

		//if(time() > $fim_stamp){

		if ($Periodo_Atrasado == 1) {
			$dve_pendente = 1;

			if ($dve_texto) {
				$dve_texto .= " e ($inicio à $fim)";
			} else {
				$dve_texto = "($inicio à $fim)";
			}
		}

	} while (odbc_fetch_row($x4));
	$dve_texto = "Declaração período(s) <br> $dve_texto";
}

if (!$dve_pendente) {
	$dve = 'OK';
	$dve_texto = 'Não há pendências até o presente momento';
}

odbc_free_result($x4);

// Emitir pdf pelo PHP diretamente 
$sqlquery = "SELECT E.*, P.Nome AS Produto, SP.c_SUSEP
             FROM Inform Inf
             LEFT JOIN Produto P ON P.i_Produto = Inf.i_Produto
             LEFT JOIN Empresa_Produto EP ON EP.i_Produto = P.i_Produto
             LEFT JOIN Empresa E ON E.i_Empresa = EP.i_Empresa
             LEFT JOIN Sub_Produto SP ON SP.i_Produto = Inf.i_Produto
                 AND SP.i_Sub_Produto = Inf.i_Sub_Produto
             WHERE Inf.id = ?";

$res = odbc_prepare($db, $sqlquery);
odbc_execute($res, [$idInform]);

$dados = odbc_fetch_array($res);

odbc_free_result($res);

$retorno_rodape = $dados['Endereco'] . ' - ' .
	$dados['Complemento'] . ' - ' .
	'CEP ' . formata_string('CEP', $dados['CEP']) . ' - ' .
	$dados['Cidade'] . ', ' .
	$dados['Estado'] . ' ' .
	'Tel.: ' . $dados['Telefone'] . '  ' .
	'Fax: ' . $dados['Fax'] . '  ' .
	'Home Page: ' . $dados['HomePage'];
$disclame_retorno = $dados['Nome'] . ' CNPJ: ' . formata_string('CNPJ', $dados['CNPJ']) . ', SUSEP no.: ' . $dados['c_SUSEP'];


$opt = [
	'mode' => 'win-1252',
	'tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf',
	'format' => 'A4-L',
	'margin_left' => 20,
	'margin_right' => 15,
	'margin_top' => 48,
	'margin_bottom' => 25,
	'margin_header' => 10,
	'margin_footer' => 10
];

$mpdf = new \Mpdf\Mpdf($opt);

$html = ob_get_clean();
// $mpdf->useOnlyCoreFonts = true;    // false is default
//$mpdf->SetProtection(array('print')); // proteção de arquivo
$mpdf->SetTitle("Proposta");
$mpdf->SetAuthor($dados['Nome']);
$mpdf->SetWatermarkText(""); // fundo marca dágua
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// Endereço do logotipo
$logo = $root . 'images/logo.jpg';

// Início do arquivo montando primeiro o CSS
$html = '<html>
		<head>
			<style>
				body {font-family: Arial, Helvetica, sans-serif;
					font-size: 11pt;
				}
				p {    margin: 0pt;
				}

				ol {counter-reset: item; font-weight:bold; }
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; text-align:justify}
                li:before {content: counters(item, "."); counter-increment: item; }
				
				ul			{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify} 

				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:11pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:11pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
				
			</style>
		</head>
		
		<body>
			<htmlpageheader name="myheader">';
$html .= ' <!--mpdf
				<htmlpageheader name="myheader">
					<div style="text-align: right;">
						<img src="' . $logo . '" widht ="120" height="60"/>
					</div><br>
					
					<div style="text-align: center;">
						<span style="font-weight: bold; font-size: 16pt;">DECLARAÇÃO DE REGULARIDADE</span>
					</div>

				</htmlpageheader>
					
				<htmlpagefooter name="myfooter">
					<table width="100%" border="0">
						<tr>
							<td width="22%">&nbsp;</td>
							<td width="56%" style="text-align:center; font-size: 8pt;">';
$html .= $retorno_rodape;
$html .= '<br>
									Página {PAGENO} de {nb}
							</td>
							<td width="22%">&nbsp;</td>
						</tr>
					</table>
				</htmlpagefooter>
					
				<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
				<sethtmlpagefooter name="myfooter" value="on" />
				mpdf-->
					
			<div style="text-align: left;">
				<span style="font-weight: bold; font-size: 11pt;">DADOS DO SEGURADO</span>
			</div>
			
			<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 2mm; ">			
				<table width="100%" border="0" style="font-size: 11pt;">
					<tr>
						<td width="15%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">Apólice Nº </span></td>
						<td width="15%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">Segurado </span></td>
						<td width="15%" colspan="2"><span style="font-weight: bold; font-size: 11pt;">Vigência da Apólice</span></td>
					</tr>
					
					<tr>
						<td width="20%" colspan="1">' . sprintf("062%06d", $n_Apolice) . '</td>
						<td width="40%" colspan="1">' . strtoupper(trim($segurado)) . '</td>
						<td width="40%" colspan="2">' . $inicio_vig . ' até ' . $final . '</td>
					</tr>
					
					<tr>
						<td width="15%"  colspan="4">&nbsp;</td>						  				   
					</tr>
					
					<tr>
						<td width="15%"  colspan="4"><span style="font-weight: bold; font-size: 11pt;">Endereço: </span>' . $address . '</td>						  				   
					</tr>
					
					<tr>
						<td width="35%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">Cidade: </span>' . $city . '</td>
						<td width="15%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">Estado: </span>' . substr($estado, 0, 2) . '</td>
						<td width="15%"  colspan="2"><span style="font-weight: bold; font-size: 11pt;">CEP: </span>' . formata_string('CEP', $cep) . '</td>						   						   
					</tr>
					
					<tr>
						<td width="35%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">CNPJ: </span>' . formata_string('CNPJ', $cnpj) . '</td>
						<td width="15%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">Inscrição Estadual: </span>' . formata_string('IE', $ie) . '</td>						   						   						   
					</tr>
				</table>
				<br>
				<div style="text-align: left;"><span style="font-weight: bold; font-size: 11pt;">SITUAÇÃO ATUAL DA APÓLICE</span></div>
                <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 2mm; ">  
					<table width="100%" border="0" style="font-size: 11pt;">						
						<tr>
						   	<td width="25%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">Cadastro</span></td>
						   	<td width="10%"  colspan="1">OK</td>
						   	<td width="15%"  colspan="3">Não há pendências até o presente momento</td>
						</tr>
						
						<tr>
						   	<td width="25%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">DVN' . "'" . 's</span></td>
						   	<td width="10%"  colspan="1">' . ($dve == 'Pendente' ? '<span style="color:#F00">' . $dve . '</span>' : $dve) . '</td>
						   	<td width="15%"  colspan="3">' . ($dve == 'Pendente' ? '<span style="color:#F00">' . $dve_texto . '</span>' : $dve_texto) . '</td>
						</tr>
						
						<tr>
						   	<td width="25%"  colspan="1"><span style="font-weight: bold; font-size: 11pt;">Financeiro</span></td>
						   	<td width="10%"  colspan="1">' . ($financeiro == 'Pendente' ? '<span style="color:#F00">' . $financeiro . '</span>' : $financeiro) . '</td>
						   	<td width="15%"  colspan="3">' . ($financeiro == 'Pendente' ? '<span style="color:#F00">' . $financeiro_texto . '</span>' : $financeiro_texto) . '</td>
						</tr>		
					</table>';

$mpdf->AddPage();

$html .= '<pagebreak /> 
					
					<div style="text-align: left;"><span style="font-weight: bold; font-size: 11pt;">RESUMO DE COMPRADORES CEDIDOS</span></div>
                 	<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 2mm; "> 
					
					<table width="100%" border="0" style="font-size: 11pt;">
						<tr>
						   <td width="34%" colspan="1"><span style="font-weight: bold; font-size: 11pt;">Nome do Comprador</span></td>
						   <td width="20%" colspan="1"><span style="font-weight: bold; font-size: 11pt;">País</span></td>
						   <td width="18%" colspan="1" style="text-align:right"><span style="font-weight: bold; font-size: 11pt;">Crédito Concedido (' . $extmoeda . ' Mil)</span></td>
						   <td width="18%" colspan="1" style="text-align:right"><span style="font-weight: bold; font-size: 11pt;">Crédito Temporário (' . $extmoeda . ' Mil)</span></td>
                           <td width="10%" colspan="1" style="text-align:center"><span style="font-weight: bold; font-size: 11pt;">Válido até</span></td>
						</tr>';

$i = 0;
$list_aux = '';

$sql = "SELECT COUNT(idUser) AS Id FROM UsersNurim WHERE idUser = ?";
$rr = odbc_prepare($db, $sql);
odbc_execute($rr, [$userID]);

if ($role["bancoBB"]) {
	if (odbc_result($rr, 'Id') > 0) {
		$query = "
            SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
            FROM Inform inf
            JOIN UsersNurim us ON (us.idUser = ?)
            JOIN Nurim nu ON (nu.id = us.idNurim)
            JOIN Agencia ag ON (ag.idNurim = nu.id)
            JOIN CDBB cd ON (cd.idAgencia = ag.id)
            JOIN Importer imp ON (imp.idInform = inf.id)
            JOIN Country c ON (c.id = imp.idCountry)
            JOIN CDBBDetails cdd ON (cdd.idCDBB = cd.id AND cdd.idImporter = imp.id)
            WHERE inf.id = ? AND cd.status = ? AND imp.state NOT IN (7, 8, 9)
            ORDER BY imp.name";
		$cur = odbc_prepare($db, $query);
		odbc_execute($cur, [$userID, $idInform, 2]);
	} else {
		if ($tipoBanco === 1) {
			$query = "
                SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id  
                FROM Importer imp 
                JOIN Country c ON (imp.idCountry = c.id) 
                JOIN CDBBDetails cd ON (imp.id = cd.idImporter) 
                JOIN CDBB cb ON (cd.idCDBB = cb.id) 
                WHERE imp.idInform = ? AND cb.status <> 10 AND cb.id = ? 
                AND imp.state NOT IN (7, 8, 9) 
                ORDER BY imp.name";
			$cur = odbc_prepare($db, $query);
			odbc_execute($cur, [$idInform, $idCessao]);
		} else {
			$query = "
                SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id 
                FROM Importer imp 
                JOIN Country c ON (imp.idCountry = c.id) 
                JOIN CDParcDetails cd ON (imp.id = cd.idImporter) 
                JOIN CDParc cb ON (cd.idCDParc = cb.id) 
                WHERE imp.idInform = ? AND cb.status <> 10 AND cb.id = ? 
                AND imp.state NOT IN (7, 8, 9) 
                ORDER BY imp.name";
			$cur = odbc_prepare($db, $query);
			odbc_execute($cur, [$idInform, $idCessao]);
		}
	}
} else if ($role["bancoParc"]) {
	$query = "
        SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
        FROM Inform inf
        JOIN Banco bc ON (bc.idUser = ?)
        JOIN Agencia ag ON (ag.idBanco = bc.id)
        JOIN CDParc cd ON (cd.idAgencia = ag.id)
        JOIN Importer imp ON (imp.idInform = inf.id)
        JOIN Country c ON (c.id = imp.idCountry)
        JOIN CDParcDetails cdd ON (cdd.idCDParc = cd.id AND cdd.idImporter = imp.id)
        WHERE inf.id = ? AND cd.status = ? AND imp.state NOT IN (7, 8, 9)
        ORDER BY imp.name";
	$cur = odbc_prepare($db, $query);
	odbc_execute($cur, [$userID, $idInform, 2]);
} else {
	$query = "
        SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
        FROM Inform inf
        JOIN Banco bc ON (bc.idUser = ?)
        JOIN CDOB cd ON (cd.idBanco = bc.id)
        JOIN Importer imp ON (imp.idInform = inf.id)
        JOIN Country c ON (c.id = imp.idCountry)
        JOIN CDOBDetails cdd ON (cdd.idCDOB = cd.id AND cdd.idImporter = imp.id)
        WHERE inf.id = ? AND cd.status = ? AND imp.state NOT IN (7, 8, 9)
        ORDER BY imp.name";
	$cur = odbc_prepare($db, $query);
	odbc_execute($cur, [$userID, $idInform, 2]);
}

odbc_free_result($rr);

//die($query);
// Armazene todos os resultados da consulta principal em um array
$rows = [];
while (odbc_fetch_row($cur)) {
	$rows[] = [
		'idImporter' => odbc_result($cur, 4),
		'col1' => trim(odbc_result($cur, 1)),
		'col2' => trim(strtoupper(odbc_result($cur, 2))),
	];
}
odbc_free_result($cur); // Libere o recurso da consulta principal

// Agora processe cada linha e execute a segunda consulta
foreach ($rows as $row) {
	$idImporter = $row['idImporter'];
	$col1 = $row['col1'];
	$col2 = $row['col2'];

	$sql = "SELECT credit, creditTemp, limTemp 
            FROM ChangeCredit 
            WHERE idImporter = ? 
            ORDER BY id DESC";

	$x = odbc_prepare($db, $sql);
	odbc_execute($x, [$idImporter]);

	$credit = odbc_result($x, 1) / 1000;
	$creditTemp = odbc_result($x, 2);
	$limTemp = odbc_result($x, 3);

	$html .= '
        <tr>
            <td colspan="1">' . $col1 . '</td>
            <td colspan="1">' . $col2 . '</td>
            <td colspan="1" style="text-align:right">' . number_format($credit, 0, ',', '.') . '</td>
            <td colspan="1" style="text-align:right">' . ($limTemp ? number_format($creditTemp / 1000, 0, ',', '.') : '0,0') . '</td>
            <td colspan="1" style="text-align:center">' . getStrDate(substr($limTemp ?? '', 0, 10)) . '</td>
        </tr>';
	odbc_free_result($x); // Libere o recurso da consulta interna após o uso
}



$html .= '
					</table>
					
					<br><br>
                 	<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 2mm; "> 
					 	<pagebreak />
						<div style="text-align: center;">
					   		<span style="font-weight: bold; font-size: 11pt;">OBS: Emitido às ' . date('H:i \h\s \d\o \d\i\a d/m/Y') . ' <br>Válido até 24:00 hs do dia da emissão.</span>
					 	</div>
                    	<br>
                   
					<div id="cobtexto">
						Declaramos que as informações acima refletem a situação do SEGURADO no que se refere ao fornecimento de
						informações cadastrais, assinaturas de instrumentos contratuais, tempestividade no envio das Declarações de
						Volume de Exportações (DVE' . "'" . 's) e adimplemento das parcelas do prêmio, vencidas até o presente momento. 
						Salientamos, contudo, que a regularidade da situação Do SEGURADO não obriga a SEGURADORA ao pagamento de INDENIZAÇÕES, 
						que estará condicionado ao cumprimento de todas as demais obrigações previstas nas Condições Gerais, Particulares e Especiais da Apólice.
                    </div>
					<br>
					<div align="right" id="disclame">' . $disclame_retorno . '</div>
					<br>
					<div style="text-align: center;">
					    <span style="font-weight: bold; font-size: 11pt;">ESCLARECIMENTOS ADICIONAIS: Ligue ' . $dados['Telefone'] . '</span></div>

					
		</body>
	</html>';

$html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
$mpdf->allow_charset_conversion = true;
$mpdf->charset_in = 'UTF-8';
$mpdf->WriteHTML($html);

$mpdf->Output();

//$mpdf->Output($pdfDir.$key.$file.'.pdf',F); 

//$url_pdfprop = $host.'src/download/'.$key.$file.'.pdf';

?>