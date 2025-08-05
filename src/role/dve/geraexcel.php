<?php

require_once("../rolePrefix.php");
require_once("../../pdfConf.php");

require_once 'L:/inetpub/wwwroot/siga/vendor/autoload.php'; // Incluindo o autoload do Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include_once("../../../gerar_pdf/MPDF45/mpdf.php");
require_once("../../dbOpen.php");


function ymd2dmy($d)
{
	if (preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d, $v)) {
		return "$v[3]/$v[2]/$v[1]";
	}
	return $d;
}

//Alterado por Tiago V N - Elumini - 01/03/2007
if (!function_exists('getEndDate')) {
	function getEndDate($d, $n, $c = 0)
	{
		global $idDVE, $db, $idInform;

		// Preparar e executar consultas seguras para evitar SQL Injection
		$query1 = "SELECT num FROM DVE WHERE id = ?";
		$stmt1 = odbc_prepare($db, $query1);
		odbc_execute($stmt1, [$idDVE]);
		$num = odbc_result($stmt1, 1);

		$query2 = "SELECT startValidity FROM Inform WHERE id = ?";
		$stmt2 = odbc_prepare($db, $query2);
		odbc_execute($stmt2, [$idInform]);
		$start = ymd2dmy(odbc_result($stmt2, 1));

		$query3 = "SELECT max(num) FROM DVE WHERE idInform = ?";
		$stmt3 = odbc_prepare($db, $query3);
		odbc_execute($stmt3, [$idInform]);
		$num_dves = odbc_result($stmt3, 1);

		if (preg_match("@([0-9]{2})/([0-9]{2})/([0-9]{4})@", $start, $v)) {
			$dia_inicial = $v[1];
		}

		if ($num != $num_dves) {
			if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@", $d, $v)) {
				return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
			} else if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})@", $d, $v)) {
				return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
			}
		} else {
			$query4 = "SELECT endValidity FROM Inform WHERE id = ?";
			$stmt4 = odbc_prepare($db, $query4);
			odbc_execute($stmt4, [$idInform]);
			$end = odbc_result($stmt4, 1);
			return ymd2dmy($end);
		}
	}
}


function dataconvert($dt)
{
	// leitura das datas
	$dia = date('d');
	$mes = date('m');
	$ano = date('Y');
	$semana = date('w');


	// configuração mes

	switch ($mes) {

		case 1:
			$mes = "Janeiro";
			break;
		case 2:
			$mes = "Fevereiro";
			break;
		case 3:
			$mes = "Março";
			break;
		case 4:
			$mes = "Abril";
			break;
		case 5:
			$mes = "Maio";
			break;
		case 6:
			$mes = "Junho";
			break;
		case 7:
			$mes = "Julho";
			break;
		case 8:
			$mes = "Agosto";
			break;
		case 9:
			$mes = "Setembro";
			break;
		case 10:
			$mes = "Outubro";
			break;
		case 11:
			$mes = "Novembro";
			break;
		case 12:
			$mes = "Dezembro";
			break;

	}


	// configuração semana

	switch ($semana) {

		case 0:
			$semana = "Domingo";
			break;
		case 1:
			$semana = "Segunda Feira";
			break;
		case 2:
			$semana = "Terça Feira";
			break;
		case 3:
			$semana = "Quarta Feira";
			break;
		case 4:
			$semana = "Quinta Feira";
			break;
		case 5:
			$semana = "Sexta Feira";
			break;
		case 6:
			$semana = "Sábado";
			break;

	}

	$data = $dia . ' de ' . $mes . ' de ' . $ano;
	return $data;
}

$idInform = $_REQUEST['idInform'];
$idDVE = $_REQUEST['idDVE'];
$noval = $_REQUEST['noval'];

$sqlEmp = "SELECT Nome, CNPJ,	Endereco,	Complemento, CEP, Cidade,	Estado,	Cod_Area,	Telefone,	Bairro, Fax, HomePage
							FROM Empresa 
							WHERE i_Empresa = 1";
$resEmp = odbc_exec($db, $sqlEmp);
$dadosEmp = odbc_fetch_array($resEmp);

$compEmp = $dadosEmp['Complemento'] ? ' - ' . $dadosEmp['Complemento'] : '';
$cepEmp = $dadosEmp['CEP'] ? ' - Cep: ' . $dadosEmp['CEP'] : '';
$cidEmp = $dadosEmp['Cidade'] ? ' - ' . $dadosEmp['Cidade'] : '';
$estEmp = $dadosEmp['Estado'] ? ', ' . $dadosEmp['Estado'] : '';
$telEmp = $dadosEmp['Telefone'] ? ' Tel.: ' . $dadosEmp['Telefone'] : '';
$faxEmp = $dadosEmp['Fax'] ? ' Fax: ' . $dadosEmp['Fax'] : '';

$enderecoEmp = $dadosEmp['Endereco'] . $compEmp . $cepEmp . $cidEmp . $estEmp . $telEmp . $faxEmp;
$siteEmp = $dadosEmp['HomePage'];
$nomeEmp = $dadosEmp['Nome'];

$sqlquery = "SELECT P.Nome AS Produto, SP.Descricao AS SubProduto, SP.c_SUSEP 
	FROM Inform Inf
	INNER JOIN Produto P ON
		P.i_Produto = Inf.i_Produto
	INNER JOIN Empresa_Produto EP ON
		EP.i_Produto = P.i_Produto
	INNER JOIN Empresa E ON
		E.i_Empresa = EP.i_Empresa
	INNER JOIN Sub_Produto SP ON
		SP.i_Produto = Inf.i_Produto
		AND SP.i_Sub_Produto = Inf.i_Sub_Produto
	WHERE Inf.id = ?";

$rsSqlquery = odbc_prepare($db, $sqlquery);
odbc_execute($rsSqlquery, [$idInform]);

$dadosQuery = odbc_fetch_array($rsSqlquery);

$c_SUSEP = $dadosQuery['c_SUSEP'] ?? '';

odbc_free_result($rsSqlquery);

$totalace1 = $totalace2 = $totalproex1 = $totalproex2 = $v_Vista_Prazo = $v_Coligadas = 0;

$qry1 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 1 AND dt.state = 1
          ORDER BY dt.embDate";

$cur = odbc_prepare($db, $qry1);
odbc_execute($cur, [$idDVE]);


while (odbc_fetch_row($cur)) {
	$v_Vista_Prazo += odbc_result($cur, 7);
	$totalproex1 += odbc_result($cur, 8);
	$totalace1 += odbc_result($cur, 9);
}
odbc_free_result($cur);
$qry2 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 2 AND dt.state = 1
          ORDER BY dt.embDate";

$cur = odbc_prepare($db, $qry2);
odbc_execute($cur, [$idDVE]);


while (odbc_fetch_row($cur)) {
	$v_Coligadas += odbc_result($cur, 7);
	$totalproex2 += odbc_result($cur, 8);
	$totalace2 += odbc_result($cur, 9);
}
$sql = "SELECT name, i_Seg, endValidity, startValidity, prodUnit, Ga, currency, n_Apolice, 
               IsNull(periodMaxCred, 180) AS PeriodCred
        FROM Inform
        WHERE id = ?";

$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);


$Nome_Segurado = odbc_result($x, 1);
$d_Inicio_Vigencia = ymd2dmy(odbc_result($x, 4));
$d_Fim_Vigencia = ymd2dmy(odbc_result($x, 3));
$prod = odbc_result($x, 5);
$n_Apolice = sprintf("062%06d", odbc_result($x, 8));
$PeriodCred = odbc_result($x, 'PeriodCred');
$vigencia = $d_Inicio_Vigencia . " à " . $d_Fim_Vigencia;
$ga = odbc_result($x, "Ga");
$moeda = odbc_result($x, "currency");
$d_Envio = date('d/m/Y');

if ($moeda == "1") {
	$extMoeda = "Reais";
} else if ($moeda == "2") {
	$extMoeda = "Dólares Norte Americanos";
} else if ($moeda == "6") {
	$extMoeda = "Euros";
}

if (!$c_SUSEP) {
	if (($ga == "0") || ($ga == "")) {
		$c_SUSEP = "15.414005212/2005-89";
	} else {
		$c_SUSEP = "15.414004768/2004-08";
	}
}

if ($prod != 62) {
	$n_Apolice .= "/$prod";
}
odbc_free_result($x);
$query = "SELECT 
            D.inicio AS Inicio,
            D.periodo AS Periodo, 
            D.num AS Num, 
            D.total2 AS Total2, 
            D.sentDate AS SentDate,
            ISNULL(DateAdd(D, -1, D2.inicio), Inf.endValidity) AS Fim
        FROM 
            DVE D
        INNER JOIN Inform Inf ON
            Inf.id = D.idInform
        LEFT JOIN DVE D2 ON
            D2.idInform = D.idInform
            AND D2.num = D.num + 1
        WHERE 
            D.id = ?";

$r = odbc_prepare($db, $query);
odbc_execute($r, [$idDVE]);


if (odbc_fetch_row($r)) {
	$d_Inicio_Vigencia = ymd2dmy(odbc_result($r, "Inicio"));
	$d_Fim_Vigencia = ymd2dmy(odbc_result($r, "Fim"));
	$p = odbc_result($r, "Periodo");
	$num = odbc_result($r, "Num");
	$v_Antecipado_Credito = odbc_result($r, "Total2");
	$sentDate = odbc_result($r, "SentDate");
	$periodo_dve = $d_Inicio_Vigencia . " à " . $d_Fim_Vigencia . " (" . $num . "ª DVE)";
}

$v_Total = $v_Vista_Prazo + $v_Antecipado_Credito + $v_Coligadas;

$key = session_id() . time();

odbc_free_result($r);

// ...código existente até a montagem da tabela...


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabeçalho
$headers = [
    'Nº SBCE', 'Comprador', 'País', 'Data Embarque', 'Nº da Fatura',
    'Data Vencimento', 'Valor Embarcado', 'Valor Financiado PROEX', 'Valor Financiado ACE'
];
$sheet->fromArray($headers, null, 'A1');

// Dados
$row = 2;

// Modalidade 1
$qry1 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 1 AND dt.state = 1
          ORDER BY dt.embDate";
$cur = odbc_prepare($db, $qry1);
odbc_execute($cur, [$idDVE]);
while (odbc_fetch_row($cur)) {
    $sheet->fromArray([
        odbc_result($cur, 1),
        trim(odbc_result($cur, 2)),
        trim(odbc_result($cur, 3)),
        ymd2dmy(odbc_result($cur, 4)),
        odbc_result($cur, 5),
        ymd2dmy(odbc_result($cur, 6)),
        number_format(odbc_result($cur, 7), 2, ',', '.'),
        number_format(odbc_result($cur, 8), 2, ',', '.'),
        number_format(odbc_result($cur, 9), 2, ',', '.')
    ], null, 'A' . $row++);
}
odbc_free_result($cur);

// Modalidade 2
$qry2 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 2 AND dt.state = 1
          ORDER BY dt.embDate";
$cur = odbc_prepare($db, $qry2);
odbc_execute($cur, [$idDVE]);
while (odbc_fetch_row($cur)) {
    $sheet->fromArray([
        odbc_result($cur, 1),
        trim(odbc_result($cur, 2)),
        trim(odbc_result($cur, 3)),
        ymd2dmy(odbc_result($cur, 4)),
        odbc_result($cur, 5),
        ymd2dmy(odbc_result($cur, 6)),
        number_format(odbc_result($cur, 7), 2, ',', '.'),
        number_format(odbc_result($cur, 8), 2, ',', '.'),
        number_format(odbc_result($cur, 9), 2, ',', '.')
    ], null, 'A' . $row++);
}
odbc_free_result($cur);

// Download do arquivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="DVE_exportacao.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');


?>