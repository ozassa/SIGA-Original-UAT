<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once("../../../dbOpen.php");
require_once 'L:/inetpub/wwwroot/siga/vendor/autoload.php'; // Incluindo o autoload do Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!function_exists('ymd2dmy')) {
    function ymd2dmy($d)
    {
        if (preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d, $v)) {
            return "$v[3]/$v[2]/$v[1]";
        }

        return $d;
    }
}

$Id_User = isset($_REQUEST['Id_User']) && $_REQUEST['Id_User'] ? $_REQUEST['Id_User'] : false;
$Id_Inform = isset($_REQUEST['Id_Inform']) && $_REQUEST['Id_Inform'] !== 'NULL' ? $_REQUEST['Id_Inform'] : null;
$Nome_Comprador = isset($_REQUEST['Nome_Comprador']) && $_REQUEST['Nome_Comprador'] !== '' && $_REQUEST['Nome_Comprador'] !== 'NULL' ? $_REQUEST['Nome_Comprador'] : null;
$Num_Fatura = isset($_REQUEST['Num_Fatura']) && $_REQUEST['Num_Fatura'] !== '' && $_REQUEST['Num_Fatura'] !== 'NULL' ? $_REQUEST['Num_Fatura'] : null;
$s_Declaracao = isset($_REQUEST['s_Declaracao']) && $_REQUEST['s_Declaracao'] !== '0' ? $_REQUEST['s_Declaracao'] : '0';
$CRS = isset($_REQUEST['CRS']) && $_REQUEST['CRS'] !== '' && $_REQUEST['CRS'] !== 'NULL' ? $_REQUEST['CRS'] : null;


$sqlProc = "EXEC SPR_BB_Consulta_Declaracao_Faturamento ?, ?, ?, ?, ?";
$exSqlProc = odbc_prepare($db, $sqlProc);
odbc_execute($exSqlProc, [$Id_Inform, $Nome_Comprador, $Num_Fatura, $s_Declaracao, $CRS]);

$logMessage = sprintf(
    "Valores do DVEConsultaExcel.php: Id_Inform=%s, Nome_Comprador=%s, Num_Fatura=%s, s_Declaracao=%s, CRS=%s",
    var_export($Id_Inform, true),
    var_export($Nome_Comprador, true),
    var_export($Num_Fatura, true),
    var_export($s_Declaracao, true),
    var_export($CRS, true)
);

//error_log($logMessage);


$dados = [];
while (odbc_fetch_row($exSqlProc)) {
    $dados[] = [
        "CodDVE" => odbc_result($exSqlProc, "CodDVE"),
        "n_Apolice" => odbc_result($exSqlProc, "n_Apolice"),
        "Num_DPP" => odbc_result($exSqlProc, "Num_DPP"),
        "Nome_Segurado" => mb_convert_encoding(odbc_result($exSqlProc, "Nome_Segurado"), 'UTF-8', 'ISO-8859-1'),
        "d_Inicio_Vigencia" => odbc_result($exSqlProc, "d_Inicio_Vigencia") ? ymd2dmy(odbc_result($exSqlProc, "d_Inicio_Vigencia")) : "",
        "d_Fim_Vigencia" => odbc_result($exSqlProc, "d_Fim_Vigencia") ? ymd2dmy(odbc_result($exSqlProc, "d_Fim_Vigencia")) : "",
        "s_Apolice" => mb_convert_encoding(odbc_result($exSqlProc, "s_Apolice"), 'UTF-8', 'ISO-8859-1'),
        "Moeda" => mb_convert_encoding(odbc_result($exSqlProc, "Moeda"), 'UTF-8', 'ISO-8859-1'),
        "p_Cobertura" => odbc_result($exSqlProc, "p_Cobertura"),
        "v_LMI" => odbc_result($exSqlProc, "v_LMI") ? number_format(odbc_result($exSqlProc, "v_LMI"), 2, ',', '.') : "",
        "CRS" => mb_convert_encoding(odbc_result($exSqlProc, "CRS"), 'UTF-8', 'ISO-8859-1'),
        "Nome_Comprador" => mb_convert_encoding(odbc_result($exSqlProc, "Nome_Comprador"), 'UTF-8', 'ISO-8859-1'),
        "Nome_Pais" => mb_convert_encoding(odbc_result($exSqlProc, "Nome_Pais"), 'UTF-8', 'ISO-8859-1'),
        
        #"v_Credito_Cocedido" => odbc_result($exSqlProc, "v_Credito_Cocedido") ? number_format(odbc_result($exSqlProc, "v_Credito_Cocedido"), 2, ',', '.') : "",
        "v_Limite_Disponivel" => odbc_result($exSqlProc, "v_Limite_Disponivel") ? number_format(odbc_result($exSqlProc, "v_Limite_Disponivel"), 2, ',', '.') : "",
        "n_Fatura" => odbc_result($exSqlProc, "n_Fatura"),
        "d_Embarque" => odbc_result($exSqlProc, "d_Embarque") ? ymd2dmy(odbc_result($exSqlProc, "d_Embarque")) : "",
        "d_Vencimento" => odbc_result($exSqlProc, "d_Vencimento") ? ymd2dmy(odbc_result($exSqlProc, "d_Vencimento")) : "",
        "v_Embarque" => odbc_result($exSqlProc, "v_Embarque") ? number_format(odbc_result($exSqlProc, "v_Embarque"), 2, ',', '.') : "",
        "d_Pagamento" => odbc_result($exSqlProc, "d_Pagamento") ? ymd2dmy(odbc_result($exSqlProc, "d_Pagamento")) : "",
        "v_Pago" => odbc_result($exSqlProc, "v_Pago") ? number_format(odbc_result($exSqlProc, "v_Pago"), 2, ',', '.') : "",
        "v_Saldo" => odbc_result($exSqlProc, "v_Saldo") ? number_format(odbc_result($exSqlProc, "v_Saldo"), 2, ',', '.') : "",
        "s_Fatura" => mb_convert_encoding(odbc_result($exSqlProc, "s_Fatura"), 'UTF-8', 'ISO-8859-1'),
    ];
}


// Criar um novo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Definir cabeçalhos
$headers = [
    'Nº da Apólice',            // n_Apolice
    'Segurado',                 // Nome_Segurado
    'Início de Vigência',       // d_Inicio_Vigencia
    'Fim de Vigência',          // d_Fim_Vigencia
    'Situação da Apólice',      // s_Apolice
    'Moeda',                    // Moeda
    'Cobertura (%)',            // p_Cobertura
    'LMI',                      // v_LMI
    'CRS',                      // CRS
    'Comprador',                // Nome_Comprador
    'País',                     // Nome_Pais
    //'Crédito Concedido',        // v_Credito_Cocedido
    'Limite Disponível',        // v_Limite_Disponivel
    'Fatura',                   // n_Fatura
    'Data de Embarque',         // d_Embarque
    'Data de Vencimento',       // d_Vencimento
    'Valor da Fatura',          // v_Embarque
    'Data de Pagamento',        // d_Pagamento
    'Valor Pago',               // v_Pago
    'Valor Saldo',              // v_Saldo
    'Situação',                 // s_Fatura
];

foreach ($headers as $colIndex => $header) {
    // Suporte até coluna Z (26 colunas). Para mais, usar função para AA, AB...
    $cell = chr(65 + $colIndex) . '1'; // A1, B1, etc.
    $sheet->setCellValue($cell, $header);
}

// Adicionar dados
foreach ($dados as $rowIndex => $row) {
    $sheet->setCellValue('A' . ($rowIndex + 2), $row['n_Apolice']);
    $sheet->setCellValue('B' . ($rowIndex + 2), $row['Nome_Segurado']);
    $sheet->setCellValue('C' . ($rowIndex + 2), $row['d_Inicio_Vigencia']);
    $sheet->setCellValue('D' . ($rowIndex + 2), $row['d_Fim_Vigencia']);
    $sheet->setCellValue('E' . ($rowIndex + 2), $row['s_Apolice']);
    $sheet->setCellValue('F' . ($rowIndex + 2), $row['Moeda']);
    $sheet->setCellValue('G' . ($rowIndex + 2), $row['p_Cobertura']);
    $sheet->setCellValue('H' . ($rowIndex + 2), $row['v_LMI']);
    $sheet->setCellValue('I' . ($rowIndex + 2), $row['CRS']);
    $sheet->setCellValue('J' . ($rowIndex + 2), $row['Nome_Comprador']);
    $sheet->setCellValue('K' . ($rowIndex + 2), $row['Nome_Pais']);
    //$sheet->setCellValue('L' . ($rowIndex + 2), $row['v_Credito_Cocedido']);
    $sheet->setCellValue('L' . ($rowIndex + 2), $row['v_Limite_Disponivel']);
    $sheet->setCellValue('M' . ($rowIndex + 2), $row['n_Fatura']);
    $sheet->setCellValue('N' . ($rowIndex + 2), $row['d_Embarque']);
    $sheet->setCellValue('O' . ($rowIndex + 2), $row['d_Vencimento']);
    $sheet->setCellValue('P' . ($rowIndex + 2), $row['v_Embarque']);
    $sheet->setCellValue('Q' . ($rowIndex + 2), $row['d_Pagamento']);
    $sheet->setCellValue('R' . ($rowIndex + 2), $row['v_Pago']);
    $sheet->setCellValue('S' . ($rowIndex + 2), $row['v_Saldo']);
    $sheet->setCellValue('T' . ($rowIndex + 2), $row['s_Fatura']);
}


// Salvar arquivo Excel
$file = 'consulta_dve_' . date("dmY_His") . '.xlsx';
$savePath = realpath(__DIR__ . '/../../../consulta_dve') . DIRECTORY_SEPARATOR . $file;

$writer = new Xlsx($spreadsheet);
$writer->save($savePath);

// Redirecionar para o download
header('Location: ../../../consulta_dve/' . $file);
exit;
