<?php
set_time_limit(300);

session_set_cookie_params([
  'secure' => true,
  'httponly' => true
]);
session_start();

$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';

$relatorioNome = $_REQUEST['relatorioNome'];

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=" . $relatorioNome . ".xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
require_once("../../../dbOpen.php");

$resultado = '';
$html = '';

function isFloat($str)
{
  if (!is_numeric($str)) {
    return false;
  }

  if (strpos($str, '.') !== false) {
    return true;
  }

  return false;
}

if ($relatorioNome == 'calculoClCofSbce') {

  $n_Empresa = $_REQUEST['empresaName'] ?? '0';
  $n_DPP = $_REQUEST['dppName'] ?? '0';
  $n_Vigencias = $_REQUEST['numeroVigencias'] ?? '0';

  $data_Corte = $_REQUEST['dataCorte'] ?? '0';

  $Opcao = $_REQUEST['opcoes'] ?? '0';

  $dataObjeto = DateTime::createFromFormat('Y-m-d', $data_Corte);

  if ($dataObjeto) {
    $dataFormatada = $dataObjeto->format('Y/m/d');
  } else {
    $dataFormatada = new DateTime();
    $dataFormatada = $dataFormatada->format('Y/m/d');
  }

  //$n_Empresa = $_POST['n_Empresa'] ?? ; 
  //$n_Apolice = $_POST['n_Apolice'] ?? 0; 
  //$t_Relatorio = $_POST['t_Relatorio'] ?? 0; 

  //$n_DPP = $_POST['n_DPP'] ?? '0'; 

  // Executa a stored procedure

  $resource = odbc_exec($db, "EXEC spr_COF_LossRatio_Tecnica @Empresa = $n_Empresa, @DPP = '$n_DPP', @Num_Vigencia = $n_Vigencias, @DataCorte = '$dataFormatada', @Opcao = '0', @RenovaRTL = '$Opcao'");


  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 1;
      $resultado .= "<tr>";
      foreach ($row as $columnValue) {
        if (isFloat($columnValue ?? '')) {
          $numeroFloat = floatval($columnValue);
          if ($i > 36 && $i < 44) {
            $numeroFloat2 = $numeroFloat * 100;

            $resultado .= "<td>" . (number_format($numeroFloat2, 2, ',', '.')) . "%</td>";
          } else if($i == 14 ) {
            $stringFormatada = (number_format($numeroFloat, 4, ',', '.'));
            $resultado .= "<td>&#8203;" . $stringFormatada . "%</td>";
          } else {
            $resultado .= "<td>" . (number_format($numeroFloat, 4, ',', '.')) . "</td>";
          }
        } else if ($i == 6) {
          $cnpjNumerico = $columnValue;
          $cnpjFormatado = substr($cnpjNumerico, 0, 2) . '.' .
            substr($cnpjNumerico, 2, 3) . '.' .
            substr($cnpjNumerico, 5, 3) . '/' .
            substr($cnpjNumerico, 8, 4) . '-' .
            substr($cnpjNumerico, 12, 2);

          $resultado .= "<td>" . ($cnpjFormatado ?? '-') . "</td>";
        } else if ($i == 8 || $i == 9) {
          $dataOriginal = substr($columnValue ?? '', 0, 10);
          $resultado .= "<td>" . ($dataOriginal) . "</td>";

        } else {
          $resultado .= "<td>" . ($columnValue ?? '-') . "</td>";
        }
        $i++;
      }
      $resultado .= "</tr>";

    }
  } else {
    $resultado = "Erro ao executar a Stored Procedure.";
  }

  if ($Opcao == 0 || $Opcao == 1) {
    $html = '<table>
                    <thead>
                        <tr>
                                                    <th scope="col">Empresa</th>
                          <th scope="col">Tipo</th>
                          <th scope="col">DPP</th>
                          <th scope="col">DPP new</th>
                          <th scope="col">Segurado</th>
                          <th scope="col">CNPJ</th>
                          <th scope="col">Corretor</th>
                          <th scope="col">Vig_Inicial</th>
                          <th scope="col">Vig_Final</th>
                          <th scope="col">Ap&oacute;lice</th>
                          <th scope="col">Segmento</th>
                          <th scope="col">Moeda</th>
                          <th scope="col">Cobertura</th>
                          <th scope="col">Tx_Premio</th>
                          <th scope="col">Pr&ecirc;mio</th>
                          <th scope="col">Ajuste</th>
                          <th scope="col">B&ocirc;nus</th>
                          <th scope="col">Malus</th>
                          <th scope="col">Receita</th>
                          <th scope="col">Faturamento Declarado</th>
                          <th scope="col">Faturamento Estimado</th>
                          <th scope="col">Valor Avisado</th>
                          <th scope="col">Valor Cobrado</th>
                          <th scope="col">Qtde Sinistros</th>
                          <th scope="col">Valor Indenizado Total</th>
                          <th scope="col">Valor Reserva Total</th>
                          <th scope="col">Qtde DCL</th>
                          <th scope="col">Indenizado - DCL</th>
                          <th scope="col">Reserva - DCL</th>
                          <th scope="col">Qtde Nomeado</th>
                          <th scope="col">Indenizado - Nomeado</th>
                          <th scope="col">Reserva - Nomeado</th>
                          <th scope="col">Valor Recuperado</th>
                          <th scope="col">Valor Despesas</th>
                          <th scope="col">Performance</th>
                          <th scope="col">Performance hist&oacute;rica</th>
                          <th scope="col">Loss_Atual</th>
                          <th scope="col">Loss_Potencial</th>
                          <th scope="col">Loss Hist&oacute;rico</th>
                          <th scope="col">Loss n3+CY</th>
                          <th scope="col">Loss n1+CY</th>
                          <th scope="col">TR CY</th>
                          <th scope="col">TR n3+CY</th>
                          <th scope="col">Data Extra&ccedil;&atilde;o</th>
                          <th scope="col">Situa&ccedil;&atilde;o da Ap&oacute;lice</th>
                          <th scope="col">Setor</th>
                          <th scope="col">Origem</th>
                          <th scope="col">Franquia Simples</th>
                          <th scope="col">Franquia Anual Global</th>
                          <th scope="col">PMC</th>
                          <th scope="col">NOA</th>
                          <th scope="col">Cessao_Ativa</th>


                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';
  } else if ($Opcao == 3) {
    $html = '<table>
                    <thead>
                        <tr>
                          <th scope="col">Empresa</th>
                          <th scope="col">Tipo</th>
                          <th scope="col">DPP</th>
                          <th scope="col">DPP new</th>
                          <th scope="col">Segurado</th>
                          <th scope="col">CNPJ</th>
                          <th scope="col">Corretor</th>
                          <th scope="col">Segmento</th>
                          <th scope="col">Moeda</th>
                          <th scope="col">Setor</th>
                          <th scope="col">Vig_Inicial</th>
                          <th scope="col">Vig_Final</th>
                          <th scope="col">Apolice</th>
                          <th scope="col">Tx_Premio</th>
                          <th scope="col">Premio</th>
                          <th scope="col">Ajuste</th>
                          <th scope="col">Bonus</th>
                          <th scope="col">Malus</th>
                          <th scope="col">Receita</th>
                          <th scope="col">Faturamento Declarado</th>
                          <th scope="col">Faturamento Estimado</th>
                          <th scope="col">Valor Avisado</th>
                          <th scope="col">Valor Cobrado</th>
                          <th scope="col">Qtde Sinistros</th>
                          <th scope="col">Valor Indenizado Total</th>
                          <th scope="col">Valor Reserva Total</th>
                          <th scope="col">Qtde DCL</th>
                          <th scope="col">Indenizado - DCL</th>
                          <th scope="col">Reserva - DCL</th>
                          <th scope="col">Qtde Nomeado</th>
                          <th scope="col">Indenizado - Nomeado</th>
                          <th scope="col">Reserva - Nomeado</th>
                          <th scope="col">Valor Recuperado</th>
                          <th scope="col">Valor Despesas</th>
                          <th scope="col">Performance</th>
                          <th scope="col">Performance histórica</th>
                          <th scope="col">Loss_Atual</th>
                          <th scope="col">Loss_Potencial</th>
                          <th scope="col">Loss Histórico</th>
                          <th scope="col">Loss n3+CY</th>
                          <th scope="col">Loss n1+CY</th>
                          <th scope="col">TR CY</th>
                          <th scope="col">TR n3+CY</th>
                          <th scope="col">Data Extração</th>
                          <th scope="col">Situação da Apólice</th>
                          <th scope="col">Origem</th>
                          <th scope="col">Cobertura</th>
                          <th scope="col">Franquia Simples</th>
                          <th scope="col">Franquia Anual Global</th>
                          <th scope="col">PMC</th>
                          <th scope="col">NOA</th>
                          <th scope="col">Cessao_Ativa</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';
  }

} else if ($relatorioNome == 'situacaoFinanceira') {

  $resultado = '';

  $n_Empresa = $_REQUEST['empresaName'] ?? '0';
  $n_Apolice = $_REQUEST['apoliceName'] ?? '0';
  $n_DPP = $_REQUEST['dppName'] ?? '0';
  $t_Relatorio = $_REQUEST['opcoes'] ?? '1000';

  $resource = odbc_exec($db, "EXEC spr_BB_Consulta_Financeira @n_Empresa = $n_Empresa, @n_Apolice = $n_Apolice, @t_Relatorio = $t_Relatorio, @n_DPP = '$n_DPP'");

  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 0;
      $resultado .= "<tr>";
      foreach ($row as $columnValue) {
        //$resultado .= "<td>" . htmlspecialchars($columnValue) . "</td>";
        $dataOriginal = $columnValue ?? '';
        $dataObjeto = DateTime::createFromFormat('Y-m-d H:i:s.u', $dataOriginal);

        if ($dataObjeto) {
          $dataFormatada = $dataObjeto->format('d/m/Y');
          $resultado .= "<td>" . $dataFormatada ?? '-' . "</td>";

        } else {
          if ($i < 3) {
            $resultado .= "<td>" . $columnValue ?? '-' . "</td>";
          } else {
            if (is_numeric($columnValue)) {
              $numeroString = $columnValue;
              $numeroFloat = floatval($numeroString);
              $resultado .= "<td>" . number_format($numeroFloat, 2, ',', '.') . "</td>";
            } else {
              $resultado .= "<td>" . $columnValue ?? '-' . "</td>";
            }
          }
        }
        $i++;
      }
      $resultado .= "</tr>";
    }
  } else {
    $resultado = "Erro ao executar a Stored Procedure.";
  }

  if ($t_Relatorio == '1000') {
    $html = '<table summary="Submitted table designs" id="example">
      <thead>
        <tr>
          <th scope="col">Empresa</th>
          <th scope="col">DPP</th>
          <th scope="col">Ap&oacute;lice</th>
          <th scope="col">Segurado</th>
          <th scope="col">In&iacute;cio Vig&ecirc;ncia</th>
          <th scope="col">Fim Vig&ecirc;ncia</th>
          <th scope="col">Minimum premium invoiced</th>
          <th scope="col">Minimum premium paid</th>
          <th scope="col">Premium adjustment paid</th>
          <th scope="col">Total premium paid</th>
          <th scope="col">Credit limit fees invoiced</th>
          <th scope="col">Credit limit fees paid</th>

        </tr>
      </thead>
      <tbody id="tabelaResultado">
      ' . $resultado . '
      </tbody>
    </table>';

  } else if ($t_Relatorio == '1100') {
    $html = '
    <table summary="Submitted table designs" id="example">
      <thead>
        <tr>
    <th scope="col">Empresa</th>
      <th scope="col">Ap&oacute;lice</th>
      <th scope="col">DPP</th>
      <th scope="col">Segurado</th>
      <th scope="col">Parcela</th>
      <th scope="col">Per&iacute;odo</th>
      <th scope="col">Vlr Parcela</th>
      <th scope="col">Dt Vencimento</th>
      <th scope="col">Dt Pagamento</th>
      <th scope="col">N Fiscal</th>
      <th scope="col">Dt Emiss&atilde;o</th>
      </tr>
      </thead>
      <tbody id="tabelaResultado">
      ' . $resultado . '
      </tbody>
    </table>';
  }

} else if ($relatorioNome == 'TOD') {


  $resultado = '';
  $html = '';

  $n_Empresa = $_REQUEST['empresaName'] ?? '0';
  $n_DPP = $_REQUEST['dppName'] ?? '0';
  $n_Apolice = $_REQUEST['apoliceName'] ?? '0';
  $n_ApoliceAtiva = $_REQUEST['apolicesAtivas'] ?? '0';

  $data_Corte = $_REQUEST['dataCorte'] ?? date('Y-m-d');

  $opcao = $_REQUEST['opcoes'] ?? '0';

  if ($n_DPP == '0' || $n_DPP == 0) {
    set_time_limit(0);
  }

  $dataObjeto = DateTime::createFromFormat('Y-m-d', $data_Corte);

  if ($dataObjeto) {
    $ano = $dataObjeto->format('Y');
    $mes = $dataObjeto->format('m');
    $dia = $dataObjeto->format('d');
  } else {
    $dataAtual = new DateTime();
    $ano = $dataAtual->format('Y');
    $mes = $dataAtual->format('m');
    $dia = $dataAtual->format('d');
  }

  //$n_Empresa = $_POST['n_Empresa'] ?? ; 
  //$n_Apolice = $_POST['n_Apolice'] ?? 0; 
  //$t_Relatorio = $_POST['t_Relatorio'] ?? 0; 

  //$n_DPP = $_POST['n_DPP'] ?? '0'; 

  // Executa a stored procedure

  $resource = odbc_exec($db, "EXEC spr_COF_TOD_STEP_1 @t_Relatorio = $opcao, @n_Empresa = $n_Empresa, @Ano = $ano, @Mes=$mes, @Dia=$dia, @DPP = '$n_DPP', @s_Apolice = $n_Apolice, @n_Apolice = '$n_ApoliceAtiva'");


  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 1;
      $resultado .= "<tr>";
      foreach ($row as $columnValue) {
        if ($i == 13 || $i == 14 || $i == 15) {
          $dataOriginal = substr($columnValue ?? '', 0, 10);
          $resultado .= "<td>" . ($dataOriginal) . "</td>";
        } else if ($i > 19 && $i < 26) {
          if ($columnValue == '') {
            $resultado .= "<td>" . ($columnValue) . "</td>";
          } else {
            $dataFormatada = DateTime::createFromFormat('Y-m-d H:i:s.u', $columnValue ?? '')->format('d/m/Y');
            $resultado .= "<td>" . ($dataFormatada) . "</td>";
          }

        } else if (isFloat($columnValue ?? '')) {
          $numeroFloat = floatval($columnValue);
          $resultado .= "<td>" . (number_format($numeroFloat, 2, ',', '.')) . "</td>";
        } else {
          $resultado .= "<td>" . ($columnValue ?? '-') . "</td>";
        }
        $i++;
      }
      $resultado .= "</tr>";
    }
  } else {
    $resultado = "Erro ao executar a Stored Procedure.";
  }

  if ($opcao == 1) {
    $html = '<table>
    <thead>
        <tr>
          <th scope="col">Empresa</th>
            <th scope="col">Ap&oacute;lice</th>
            <th scope="col">Banco</th>
            <th scope="col">Situa&ccedil;&atilde;o</th>
            <th scope="col">DPP</th>
            <th scope="col">Segmenta&ccedil;&atilde;o</th>
            <th scope="col">Segurado</th>
            <th scope="col">Corretor</th>
            <th scope="col">Grupo Corretor</th>
            <th scope="col">Gerente Comercial</th>
            <th scope="col">Gerente Relacionamento</th>
            <th scope="col">Canal</th>
            <th scope="col">Data de Emiss&atilde;o</th>
            <th scope="col">In&iacute;cio de Vig&ecirc;ncia</th>
            <th scope="col">Fim de Vig&ecirc;ncia</th>
            <th scope="col">Moeda</th>
            <th scope="col">Periodicidade</th>
            <th scope="col">Prazo de Entrega</th>
            <th scope="col">Sequ&ecirc;ncia do Per&iacute;odo</th>
            <th scope="col">In&iacute;cio Per&iacute;odo TOD</th>
            <th scope="col">Fim Per&iacute;odo TOD</th>
            <th scope="col">Data Limite de Entrega</th>
            <th scope="col">Data de Envio</th>
            <th scope="col">Data Declara&ccedil;&atilde;o</th>
            <th scope="col">Data Cadastro</th>
            <th scope="col">Valor Declarado (OIM/SIGA)</th>
            <th scope="col">Valor Cofanet - Principal</th>
            <th scope="col">Valor Cofanet - Comprador</th>
            <th scope="col">Valor Cofanet - Discricion&aacute;rio</th>
            <th scope="col">Valor Cofanet - Total</th>
            <th scope="col">Vers&atilde;o</th>
            <th scope="col">Data Declara&ccedil;&atilde;o - Cofanet</th>
            <th scope="col">Diferen&ccedil;a Cofanet x Local</th>
            <th scope="col">Situa&ccedil;&atilde;o do Per&iacute;odo</th>
            <th scope="col">Pr&ecirc;mio Emitido</th>
            <th scope="col">TaxaPremio</th>
            <th scope="col">Pr&ecirc;mio Declarado</th>
            <th scope="col">Pr&ecirc;mio Juros Mora</th>
            <th scope="col">Pr&ecirc;mio Total Declarado</th>
            <th scope="col">Resultado Parcial</th>
            <th scope="col">Ajuste_Prm</th>
            <th scope="col">Tipo de A&ccedil;&atilde;o</th>
            <th scope="col">Atraso em dias</th>
            <th scope="col">Chave</th>
            <th scope="col">Seq. Viv</th>
            <th scope="col">Prz Cr&eacute;dito</th>
            <th scope="col">Prz Declara&ccedil;&atilde;o</th>
            <th scope="col">1&ordm; Carta</th>
            <th scope="col">Dt_Envio - 1&ordm; Carta</th>
            <th scope="col">2&ordm; Carta</th>
            <th scope="col">Dt_Envio - 2&ordm; Carta</th>
            <th scope="col">&Uacute;ltima Decis&atilde;o - Comit&ecirc; TOD</th>
            <th scope="col">&Uacute;ltima A&ccedil;&atilde;o Realizada - CRM</th>

        </tr>
    </thead>
    <tbody id="tabelaResultado">                    
    ' . $resultado . '
    </tbody>
    </table>';

  } else if ($opcao == 4) {
    $html = '<table>
    <thead>
        <tr>
        <th scope="col">Empresa</th>
        <th scope="col">Apólice</th>
        <th scope="col">DPP</th>
        <th scope="col">Segurado</th>
        <th scope="col">Emissão</th>
        <th scope="col">Ini. Vigência</th>
        <th scope="col">Fim Vigência</th>
        <th scope="col">Gerente de Relacionamento</th>
        <th scope="col">Prêmio Vencido</th>
        <th scope="col">Análise Monitor Vencida</th>
        <th scope="col">Num_Periodos</th>
        <th scope="col">Num_Periodos_Atraso</th>
        <th scope="col">Num_Periodos_Divergentes</th>
        <th scope="col">Res_Parcial</th>
        <th scope="col">Loss_Atual</th>
        <th scope="col">Loss_Potencial</th>
        <th scope="col">Status DVN</th>        
        </tr>
    </thead>
    <tbody id="tabelaResultado">                    
    ' . $resultado . '
    </tbody>
    </table>';
  }

} else if ($relatorioNome == 'malusBonus') {

  $resultado = '';
  $html = '';

  $n_Empresa = $_REQUEST['empresaName'] ?? '0';
  $n_Mes = $_REQUEST['mesName'] ?? '11';
  $n_Ano = $_REQUEST['anoName'] ?? '2023';

  $opcao = $_REQUEST['opcao'] ?? '';

  $resource = odbc_exec($db, "EXEC spr_COF_Calculo_Malus @t_Relatorio = $opcao, @Empresa = $n_Empresa, @Mes = $n_Mes, @Ano = $n_Ano");

  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 1;
      $resultado .= "<tr>";
      foreach ($row as $columnValue) {
        if (isFloat($columnValue ?? '')) {
          $numeroFloat = floatval($columnValue);
          if ($i == 14 || $i > 36 && $i < 44) {
            $numeroFloat2 = $numeroFloat * 100;

            $resultado .= "<td>" . (number_format($numeroFloat2, 2, ',', '.')) . "%</td>";
          } else {
            $resultado .= "<td>" . (number_format($numeroFloat, 4, ',', '.')) . "</td>";
          }
        } else {
           $resultado .= "<td>" . $columnValue ?? '-' . "</td>";
        }
      }
      $resultado .= "</tr>";
      $i++;
    }
  } else {
    $resultado = "Erro ao executar a Stored Procedure.";
  }

  if ($opcao == '100') {
    $html = '<table>
                    <thead>
                        <tr>
                          <th scope="col">Empresa</th>
                          <th scope="col">Ap&oacute;lice</th>
                          <th scope="col">DPP</th>
                          <th scope="col">Segurado</th>
                          <th scope="col">In&iacute;cio de Vig&ecirc;ncia</th>
                          <th scope="col">Fim de Vig&ecirc;ncia</th>
                          <th scope="col">Fim do Risco</th>
                          <th scope="col">Taxa de Pr&ecirc;mio</th>
                          <th scope="col">Tipo de Adequa&ccedil;&atilde;o de Pr&ecirc;mio (Malus)</th>
                          <th scope="col">1&ordm; N&iacute;vel Sinistralidade %</th>
                          <th scope="col">% de Adequa&ccedil;&atilde;o Pr&ecirc;mio - 1&ordm; N&iacute;vel de Sinistralidade</th>
                          <th scope="col">2&ordm; N&iacute;vel Sinistralidade %</th>
                          <th scope="col">% de Adequa&ccedil;&atilde;o Pr&ecirc;mio - 2&ordm; N&iacute;vel de Sinistralidade</th>
                          <th scope="col">Taxa Pr&ecirc;mio Final %</th>
                          <th scope="col">Pr&ecirc;mio Emitido</th>
                          <th scope="col">Pr&ecirc;mio Pago</th>
                          <th scope="col">Sinistro Pago</th>
                          <th scope="col">Sinistro Pendente</th>
                          <th scope="col">Sinistro Pago + Pendente</th>
                          <th scope="col">Faturamento Total Segurado</th>
                          <th scope="col">Faturamento Calculado</th>
                          <th scope="col">Sinistralidade potencial</th>
                          <th scope="col">Sinistralidade Atual</th>
                          <th scope="col">Data de Elegibilidade do Malus</th>
                          <th scope="col">C&aacute;lculo do Malus</th>
                          <th scope="col">Malus Emitido</th>
                          <th scope="col">Data de Emiss&atilde;o</th>
                          <th scope="col">Situa&ccedil;&atilde;o</th>
                          <th scope="col">A&ccedil;&atilde;o do Malus</th>
                          <th scope="col">Moeda</th>
                          <th scope="col">Status da Ap&oacute;lice</th>
                          <th scope="col">Desc. Status</th>
                          <th scope="col">Tipo de B&ocirc;nus</th>
                          <th scope="col">Descritivo do B&ocirc;nus</th>
                          <th scope="col">Despesa ADM %</th>
                          <th scope="col">PART_DIST %</th>
                          <th scope="col">M&oacute;dulo PL</th>
                          <th scope="col">Data de Elegibilidade do B&ocirc;nus</th>
                          <th scope="col">Valor do C&aacute;lculo do B&ocirc;nus</th>
                          <th scope="col">B&ocirc;nus Emitido</th>
                          <th scope="col">Data de emiss&atilde;o do endosso de B&ocirc;nus</th>
                          <th scope="col">B&ocirc;nus Eleg&iacute;vel</th>
                          <th scope="col">A&ccedil;&atilde;o do B&ocirc;nus</th>
                          <th scope="col">Saldo PLR</th>


                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';

  } else if ($opcao == '200') {

    $html = '<table>
                    <thead>
                        <tr>
                          <th scope="col">Empresa</th>
                          <th scope="col">Ap&oacute;lice</th>
                          <th scope="col">DPP</th>
                          <th scope="col">Segurado</th>
                          <th scope="col">In&iacute;cio de Vig&ecirc;ncia</th>
                          <th scope="col">Fim de Vig&ecirc;ncia</th>
                          <th scope="col">Fim do Risco</th>
                          <th scope="col">Faturamento Declarado</th>
                          <th scope="col">Taxa de Pr&ecirc;mio</th>
                          <th scope="col">Tipo de Adequa&ccedil;&atilde;o de Pr&ecirc;mio (Malus)</th>
                          <th scope="col">1&ordm; N&iacute;vel Sinistralidade %</th>
                          <th scope="col">% de Adequa&ccedil;&atilde;o Pr&ecirc;mio - 1&ordm; N&iacute;vel de Sinistralidade</th>
                          <th scope="col">2&ordm; N&iacute;vel Sinistralidade %</th>
                          <th scope="col">% de Adequa&ccedil;&atilde;o Pr&ecirc;mio - 2&ordm; N&iacute;vel de Sinistralidade</th>
                          <th scope="col">Taxa Pr&ecirc;mio Final %</th>
                          <th scope="col">Sinistralidade potencial</th>
                          <th scope="col">Sinistralidade Atual</th>
                          <th scope="col">Data de Elegibilidade do Malus</th>
                          <th scope="col">C&aacute;lculo do Malus</th>
                          <th scope="col">Malus Emitido</th>
                          <th scope="col">Data de Emiss&atilde;o</th>
                          <th scope="col">Situa&ccedil;&atilde;o</th>
                          <th scope="col">A&ccedil;&atilde;o do Malus</th>



                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';

  } else if ($opcao == '300') {

    $html = '<table>
                    <thead>
                        <tr>
                          <th scope="col">Empresa</th>
                          <th scope="col">Ap&oacute;lice</th>
                          <th scope="col">DPP</th>
                          <th scope="col">Segurado</th>
                          <th scope="col">In&iacute;cio de Vig&ecirc;ncia</th>
                          <th scope="col">Fim de Vig&ecirc;ncia</th>
                          <th scope="col">Fim do Risco</th>
                          <th scope="col">Pr&ecirc;mio Emitido</th>
                          <th scope="col">Pr&ecirc;mio Pago</th>
                          <th scope="col">Sinistro Pago</th>
                          <th scope="col">Sinistro Pendente</th>
                          <th scope="col">Sinistralidade potencial</th>
                          <th scope="col">Sinistralidade Atual</th>
                          <th scope="col">N&iacute;vel Sinistralidade %</th>
                          <th scope="col">Descritivo do B&ocirc;nus</th>
                          <th scope="col">Despesa ADM %</th>
                          <th scope="col">PART_DIST %</th>
                          <th scope="col">M&oacute;dulo PL</th>
                          <th scope="col">Data de Elegibilidade do B&ocirc;nus</th>
                          <th scope="col">Valor do C&aacute;lculo do B&ocirc;nus</th>
                          <th scope="col">B&ocirc;nus Emitido</th>
                          <th scope="col">Data de emiss&atilde;o do endosso de B&ocirc;nus</th>
                          <th scope="col">B&ocirc;nus Eleg&iacute;vel</th>
                          <th scope="col">A&ccedil;&atilde;o do B&ocirc;nus</th>
                          <th scope="col">Saldo PLR</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';
  }


} else if ($relatorioNome == 'CAPRI') {

  $n_DPP = $_REQUEST['dppName'] ?? '0';

  $data_Inicial = $_REQUEST['dataInicial'] ?? '0';
  $data_Final = $_REQUEST['dataFinal'] ?? '0';

  $Opcao = $_REQUEST['opcoes'] ?? '0';

  $dataObjeto = DateTime::createFromFormat('Y-m-d', $data_Inicial);

  if ($dataObjeto) {
    $dataFormatada = $dataObjeto->format('Y/m/d');
  } else {
    $dataFormatada = new DateTime();
    $dataFormatada = $dataFormatada->format('Y/m/d');
  }

  $dataObjeto = DateTime::createFromFormat('Y-m-d', $data_Final);

  if ($dataObjeto) {
    $dataFormatada2 = $dataObjeto->format('Y/m/d');
  } else {
    $dataFormatada2 = new DateTime();
    $dataFormatada2 = $dataFormatada->format('Y/m/d');
  }

  //$n_Empresa = $_POST['n_Empresa'] ?? ; 
  //$n_Apolice = $_POST['n_Apolice'] ?? 0; 
  //$t_Relatorio = $_POST['t_Relatorio'] ?? 0; 

  //$n_DPP = $_POST['n_DPP'] ?? '0'; 

  // Executa a stored procedure

  $resource = odbc_exec($db, "EXEC spr_CAPRI_Limites 
    @Option = '$Opcao', 
    @CONTRACT_NB = '$n_DPP', 
    @DataCorteInicial = '$dataFormatada', 
    @DataCorteFinal = '$dataFormatada2'");

  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 1;
      $resultado .= "<tr>";
      foreach ($row as $columnValue) {
        if (isFloat($columnValue ?? '')) {
          $numeroFloat = floatval($columnValue);
          if ($i == 14 || $i > 36 && $i < 44) {
            $numeroFloat2 = $numeroFloat * 100;

            $resultado .= "<td>" . (number_format($numeroFloat2, 2, ',', '.')) . "%</td>";
          } else {
            $resultado .= "<td>" . (number_format($numeroFloat, 4, ',', '.')) . "</td>";
          }
        } else {
          if($i==1 && $Opcao ==2){
          $cnpjNumerico = $columnValue;
          $cnpjFormatado = substr($cnpjNumerico, 0, 2) . '.' .
          substr($cnpjNumerico, 2, 3) . '.' .
          substr($cnpjNumerico, 5, 3) . '/' .
          substr($cnpjNumerico, 8, 4) . '-' .
          substr($cnpjNumerico, 12, 2);
          # &#8203 significa um caractere ZWSP que é um caractere de largura zero, isso força o excel
          #a criar um campo sem formatação de numero 
          $resultado .= "<td>&#8203;" . ($columnValue ?? '-') . "</td>";
          } else {
            $resultado .= "<td>" . $columnValue ?? '-' . "</td>";
          }
        }
        $i++;
      }
      $resultado .= "</tr>";

    }
  } else {
    $resultado = "Erro ao executar a Stored Procedure.";
  }

  if ($Opcao == 2) {
    $html = '<table>
                    <thead>
                        <tr>
                          <th scope="col">Id Legal</th>
                          <th scope="col">Raz&atilde;o Social</th>
                          <th scope="col">Tipo Decis&atilde;o</th>
                          <th scope="col">CNAE</th>
                          <th scope="col">Setor de Atividade</th>
                          <th scope="col">Activity Group 38</th>
                          <th scope="col">Pa&iacute;s</th>
                          <th scope="col">Estado</th>
                          <th scope="col">CRS</th>
                          <th scope="col">Vl Solicitado</th>
                          <th scope="col">Vl Aprovado</th>
                          <th scope="col">Dt Decis&atilde;o</th>
                          <th scope="col">V&aacute;lido at&eacute;</th>
                          <th scope="col">%</th>
                          <th scope="col">DRA</th>
                          <th scope="col">Classifica&ccedil;&atilde;o</th>
                          <th scope="col">Coment&aacute;rios</th>
                          <th scope="col">UF</th>

                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';
  } else if ($Opcao == 4) {
    $html = '<table>
                    <thead>
                        <tr>
                        <th scope="col">Activity Group 38</th>
                        <th scope="col">Qtde Clientes</th>
                        <th scope="col">Vl Solicitado</th>
                        <th scope="col">Vl Aprovado</th>
                        <th scope="col">AR %</th>
                        <th scope="col">AR % TOTAL</th>
                        <th scope="col">OBS</th>

                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';
  }


}




//echo "\xEF\xBB\xBF"; //UTF-8 BOM
echo $html;