<?php
$resultado = '';
$html = '';

/*if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitBtn'])) {

  $n_Empresa = $_POST['empresaName'] ?? '0';
  $n_DPP = $_POST['dppName'] ?? '0';
  $n_Apolice = $_POST['apoliceName'] ?? '0';
  $n_ApoliceAtiva = $_POST['apolicesAtivas'] ?? '0';

  $data_Corte = $_POST['dataCorte'] ?? '0';

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

  $resource = odbc_exec($db, "EXEC spr_COF_TOD_STEP_1 @t_Relatorio = 1, @n_Empresa = $n_Empresa, @Ano = $ano, @Mes=$mes, @Dia=$dia, @DPP = '$n_DPP', @s_Apolice = $n_Apolice, @n_Apolice = '$n_ApoliceAtiva'");


  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 1;
      $resultado .= "<tr>";
      foreach ($row as $columnValue) {
        $resultado .= "<td>" . htmlspecialchars($columnValue ?? '-') . "</td>";
        if($i == 8 || $i == 9 || $i==44) {        
          $dataOriginal2 = substr($columnValue, 0, 9);
          $dataObjeto2 = DateTime::createFromFormat('Y-m-d', $dataOriginal2);
          $dataFormatada2 = $dataObjeto2->format('d/m/Y');
          $resultado .= "<td>" . htmlspecialchars($dataFormatada2) . "</td>";
        } else if($i > 12 && $i < 44 || $i == 48){
          $numeroString = $columnValue;
          $numeroFloat = floatval($numeroString);
          if($i>36 && $i<48) {
            $numeroPorcentagem = $numeroFloat*100;
            $stringResultado = number_format($numeroPorcentagem, 2, ',', '.')."%";
            $resultado .= "<td>" . htmlspecialchars($stringResultado ?? '-') . "</td>";
          } else{
            $resultado .=  "<td>" . htmlspecialchars(number_format($numeroFloat, 2, ',', '.')) . "</td>";
          }

        } else if($i == 52) {
          $textoCorrigido = mb_convert_encoding($columnValue,'ISO-8859-1','UTF-8');
          $resultado .= "<td>" . htmlspecialchars($textoCorrigido ?? '-') . "</td>";
        }
        else {
          $resultado .= "<td>" . htmlspecialchars($columnValue ?? '-') . "</td>";
        }
        $i++;
      }
      $resultado .= "</tr>";
    }
  } else {
    $resultado = "Erro ao executar a Stored Procedure.";
  }


  $html = '<table>
                    <thead>
                        <tr>
                          <th scope="col">Empresa</th>
<th scope="col">Apólice</th>
<th scope="col">Banco</th>
<th scope="col">Situação</th>
<th scope="col">DPP</th>
<th scope="col">Segmentação</th>
<th scope="col">Segurado</th>
<th scope="col">Corretor</th>
<th scope="col">Grupo Corretor</th>
<th scope="col">Gerente Comercial</th>
<th scope="col">Gerente Relacionamento</th>
<th scope="col">Canal</th>
<th scope="col">Data de Emissão</th>
<th scope="col">Início de Vigência</th>
<th scope="col">Fim de Vigência</th>
<th scope="col">Moeda</th>
<th scope="col">Periodicidade</th>
<th scope="col">Prazo de Entrega</th>
<th scope="col">Sequência do Período</th>
<th scope="col">Início Período TOD</th>
<th scope="col">Fim Período TOD</th>
<th scope="col">Data Limite de Entrega</th>
<th scope="col">Data de Envio</th>
<th scope="col">Data Declaração</th>
<th scope="col">Data Cadastro</th>
<th scope="col">Valor Declarado (OIM/SIGA)</th>
<th scope="col">Valor Cofanet - Principal</th>
<th scope="col">Valor Cofanet - Comprador</th>
<th scope="col">Valor Cofanet - Discricionário</th>
<th scope="col">Valor Cofanet - Total</th>
<th scope="col">Versão</th>
<th scope="col">Data Declaração - Cofanet</th>
<th scope="col">Diferença Cofanet x Local</th>
<th scope="col">Situação do Período</th>
<th scope="col">Prêmio Emitido</th>
<th scope="col">TaxaPremio</th>
<th scope="col">Prêmio Declarado</th>
<th scope="col">Prêmio Juros Mora</th>
<th scope="col">Prêmio Total Declarado</th>
<th scope="col">Resultado Parcial</th>
<th scope="col">Ajuste_Prm</th>
<th scope="col">Tipo de Ação</th>
<th scope="col">Atraso em dias</th>
<th scope="col">Chave</th>
<th scope="col">Seq. Viv</th>
<th scope="col">Prz Crédito</th>
<th scope="col">Prz Declaração</th>
<th scope="col">1ª Carta</th>
<th scope="col">Dt_Envio - 1ª Carta</th>
<th scope="col">2ª Carta</th>
<th scope="col">Dt_Envio - 2ª Carta</th>
<th scope="col">Última Decisão - Comitê TOD</th>
<th scope="col">Última Ação Realizada - CRM</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaResultado">                    
                    ' . $resultado . '
                    </tbody>
                  </table>';




}*/

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Executar Stored Procedure</title>
</head>

<body>
  <style>
    .tabela-scroll {
      max-width: 100%;
      /* Altura máxima do contêiner da tabela */
      overflow-x: auto;
      /* Habilita a barra de rolagem vertical quando necessário */
    }

    .tabela-scroll table {
      width: 100%;
      border-collapse: collapse;
    }
  </style>
  <div class="conteudopagina">
    <form name="frm" id="frm" action="" method="post">
      <ul>
        <li class="campo2colunas">
          <label>Empresa</label>
          <select name="empresaName" id="empresaName">
            <option selected value="0">Todas</option>
            <option value="1">Coface</option>
            <option value="5">SBCE</option>
          </select>
        </li>

        <li class="campo2colunas">
          <label>DPP</label>
          <input type="number" min="0" name="dppName" id="dppName" required="required">
        </li>

        <li class="campo2colunas">
          <label>Apólice</label>
          <input value="0" type="number" min="0" name="apoliceName" id="apoliceName" required="required">
        </li>

        <li class="campo2colunas">
          <label>Data</label>
          <input type="date" name="dataCorte" id="dataCorte" required="required">
        </li>

        <li class="campo2colunas">
          <label>Apenas apólices ativas</label>
          <select name="apolicesAtivas" id="apolicesAtivas">
            <option selected value="0">Todas as apólices</option>
            <option value="1">Apenas as apólices ativas</option>
          </select>
        </li>

       <!-- <li class="campo2colunas">
          <label>Opções</label>
          <select name="opcoes" id="opcoes">
            <option selected value="0">Cálculo Loss Ratio - Coface & SBCE</option>
            <option value="1">Cálculo Loss Ratio - Tradeliner - Coface & SBCE</option>
          </select>
        </li> -->


        <div class="barrabotoes">
          <button class="botaoagm" type="button" name="submitBtn" onclick="gerarExcel(1)">Detalhado</button>
          <button class="botaoagm" type="button" name="submitBtn" onclick="gerarExcel(3)">Resumo</button>
          <button class="botaoagm" type="button" name="submitBtn" onclick="gerarExcel(4)">Resumo PA</button>
          <button class="botaoagg" type="button" name="submitBtn" onclick="gerarExcel(5)">Relatório consolidado</button>
          <button class="botaoagg" type="button" name="submitBtn" onclick="gerarExcel(6)">Detalhado para o CRM</button>
        </div>

      </ul>
    </form>
    <?php echo $html; ?>

    <div class="divisoria01"></div>


  </div>

  <script>

    window.onload = function() {
    var dataCorte = document.getElementById('dataCorte');
    var hoje = new Date();
    var dd = String(hoje.getDate()).padStart(2, '0');
    var mm = String(hoje.getMonth() + 1).padStart(2, '0'); // Janeiro é 0!
    var aaaa = hoje.getFullYear();
    dataCorte.value = aaaa + '-' + mm + '-' + dd; // O formato deve ser aaaa-mm-dd
    };

    function gerarExcel(valorOpcao) {
      var empresaName = document.getElementById('empresaName').value;
      var apoliceName = document.getElementById('apoliceName').value;
      var dppName = document.getElementById('dppName').value;
      var dataCorte = document.getElementById('dataCorte').value;
      var apoliceAtiva = document.getElementById('apolicesAtivas').value;
      if(dppName === '' || dppName === '' ) {
        alert("Insira um DPP válido")
        return false;
      } else {
      var url = 'https://siga.coface.com/src/role/relatorios/interf/GerarExcel.php?relatorioNome=TOD&empresaName=' + empresaName + '&apoliceName=' + apoliceName + '&dppName=' + dppName + '&dataCorte=' + dataCorte + '&opcoes=' + valorOpcao + '&apolicesAtivas=' + apoliceAtiva
      window.open(url, '_blank')
    }
    }

    function validarProcedure(valorOpcao) {
      var empresaName = document.getElementById('empresaName').value;
      var apoliceName = document.getElementById('apoliceName').value;
      var dppName = document.getElementById('dppName').value;
      if (empresaName === '' || apoliceName === '' || dppName === '') {
        alert('Por favor, preencha todos os campos necessários.');
        return false;
      }
      return true;
    }

    document.getElementById('frm').onsubmit = validarProcedure;
  </script>
</body>

</html>