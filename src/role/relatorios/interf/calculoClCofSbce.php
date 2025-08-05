<?php
$resultado = '';
$html = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitBtn'])) {

  $n_Empresa = $_POST['empresaName'] ?? '0';
  $n_DPP = $_POST['dppName'] ?? '0';
  $n_Vigencias = $_POST['numeroVigencias'] ?? '0';

  $data_Corte = $_POST['dataCorte'] ?? '0';

  $opcao = $_POST['opcao'] ?? '0';

  $dataObjeto = DateTime::createFromFormat('Y-m-d', $data_Corte);

  if ($dataObjeto) {
    $dataFormatada = $dataObjeto->format('Y/m/d');
  } else {
    $dataFormatada = new DateTime();
    $dataFormatada = $dataFormatada->format('Y/m/d');
  }
/* 
  //$n_Empresa = $_POST['n_Empresa'] ?? ; 
  //$n_Apolice = $_POST['n_Apolice'] ?? 0; 
  //$t_Relatorio = $_POST['t_Relatorio'] ?? 0; 

  //$n_DPP = $_POST['n_DPP'] ?? '0'; 

  // Executa a stored procedure

  $resource = odbc_exec($db, "EXEC spr_COF_LossRatio_Tecnica @Empresa = $n_Empresa, @DPP = '$n_DPP', @Num_Vigencia = $n_Vigencias, @DataCorte = '$dataFormatada', @Opcao = 0, @RenovaRTL = 0");


  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 1;
      $resultado .= "<tr>";
      foreach ($row as $columnValue) {
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
                          <th scope="col">Tipo</th>
                         <th scope="col">DPP</th>
                          <th scope="col">DPP new</th>
                          <th scope="col">Segurado</th>
                          <th scope="col">CNPJ</th>
                          <th scope="col">Corretor</th>
                          <th scope="col">Vig_Inicial</th>
                          <th scope="col">Vig_Final</th>
                          <th scope="col">Apolice</th>
                          <th scope="col">Segmento</th>
                          <th scope="col">Moeda</th>
                          <th scope="col">Cobertura</th>
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



 */
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
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
          <label>Num. Vigências</label>
          <input value="0" type="number" min="0" name="numeroVigencias" id="numeroVigencias" required="required">
        </li>

        <li class="campo2colunas">
          <label>Data de Corte</label>
          <input type="date" name="dataCorte" id="dataCorte" required="required">
        </li>

        <li class="campo2colunas">
          <label>Opções</label>
          <select name="opcoes" id="opcoes">
            <!--<option disabled="true" value="0">Cálculo Loss Ratio - Coface & SBCE</option>-->
            <option selected value="1">Cálculo Loss Ratio - Tradeliner - Coface & SBCE</option>
          </select>
        </li>


        <div class="barrabotoes">
          <button class="botaoagm" type="submit" onclick="gerarExcel()" name="submitBtn">Procurar</button>
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


    function gerarExcel() {
      var empresaName = document.getElementById('empresaName').value;
      var numeroVigencias = document.getElementById('numeroVigencias').value;
      var dppName = document.getElementById('dppName').value;
      var dataCorte = document.getElementById('dataCorte').value;
      var opcoes = document.getElementById('opcoes').value;
      if(dppName === '1' || dppName === '' ) {
        alert("Insira um DPP válido")
        return false;
      } else {
      var url = 'https://siga.coface.com/src/role/relatorios/interf/GerarExcel.php?relatorioNome=calculoClCofSbce&empresaName=' + empresaName + '&numeroVigencias=' + numeroVigencias + '&dppName=' + dppName + '&dataCorte=' + dataCorte + '&opcoes=' + opcoes
      window.open(url, '_blank')
      return true;  
      }
      
    }

    function validarProcedure() {
      var empresaName = document.getElementById('empresaName').value;
      var apoliceName = document.getElementById('apoliceName').value;
      var dppName = document.getElementById('dppName').value;
      if (empresaName === '' || apoliceName === '' || dppName === '' || dppName === '0') {
        alert('Por favor, preencha todos os campos necessários.');
        return false;
      }
       var url = 'https://siga.coface.com/src/role/relatorios/interf/GerarExcel.php?relatorioNome=calculoClCofSbce&empresaName=' + empresaName + '&numeroVigencias=' + numeroVigencias + '&dppName=' + dppName + '&dataCorte=' + dataCorte + '&opcoes=' + opcoes
      window.open(url, '_blank')
      return true;
    }

    document.getElementById('frm').onsubmit = validarProcedure;
  </script>
</body>

</html>