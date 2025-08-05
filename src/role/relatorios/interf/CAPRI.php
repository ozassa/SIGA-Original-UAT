<?php
$resultado = '';
/*if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitBtn'])) {

  $n_Empresa = $_POST['empresaName'] ?? '0';
  $n_Apolice = $_POST['apoliceName'] ?? '0';
  $n_DPP = $_POST['dppName'] ?? '0';

  //$n_Empresa = $_POST['n_Empresa'] ?? ; 
  //$n_Apolice = $_POST['n_Apolice'] ?? 0; 
  $t_Relatorio = $_POST['opcoes'] ?? '1000';
  //$n_DPP = $_POST['n_DPP'] ?? '0'; 

  // Executa a stored procedure
  $resource = odbc_exec($db, "EXEC spr_BB_Consulta_Financeira @n_Empresa = $n_Empresa, @n_Apolice = $n_Apolice, @t_Relatorio = $t_Relatorio, @n_DPP = '$n_DPP'");

  if ($resource) {
    while ($row = odbc_fetch_array($resource)) {
      $i = 0;
      foreach ($row as $columnValue) {
        //$resultado .= "<td>" . htmlspecialchars($columnValue) . "</td>";
        $dataOriginal = $columnValue;
        $dataObjeto = DateTime::createFromFormat('Y-m-d H:i:s.u', $dataOriginal);

        if ($dataObjeto) {
          $dataFormatada = $dataObjeto->format('d/m/Y');
          $resultado .= htmlspecialchars($dataFormatada) . "|";

        } else {
          if ($i < 3) {
            $resultado .= htmlspecialchars($columnValue) . "|";
          } else {
            if (is_numeric($columnValue)) {
              $numeroString = $columnValue;
              $numeroFloat = floatval($numeroString);
              $resultado .= htmlspecialchars(number_format($numeroFloat, 2, ',', '.')) . "|";
            } else {
              $resultado .= htmlspecialchars($columnValue) . "|";
            }
          }
        }
        $i++;
      }
      $resultado .= "<br>";
    }
  } else {
    $resultado = "Erro ao executar a Stored Procedure.";
  }
}*/

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Executar Stored Procedure</title>
</head>

<body>
  <div class="conteudopagina">
    <form name="frm" id="frm" action="" method="post">
      <ul>
        <li class="campo2colunas">
          <label>DPP</label>
          <input type="number" min="0" name="dppName" id="dppName" required="required">
        </li>
         <li class="campo2colunas">
          <label>Data inicial</label>
          <input type="date" name="dataInicial" value="2023-01-01" id="dataInicial" required="required">
        </li>

         <li class="campo2colunas">
          <label>Data final</label>
          <input type="date" name="dataFinal" id="dataFinal" required="required">
        </li>

        <!--<li>
          <li class="campo2colunas">
          <label>Opções</label>
          <select name="opcoes" id="opcoes">
            <option selected value="1000">Relatório financeiro consolidado por apólice: Prêmio e A&M</option>
            <option value="1100">Relatório detalhado de cobrança de análise e monitoramento</option>
          </select>
        </li>
        </li>-->

        <div class="barrabotoes">
          <button class="botaoagm" type="button" onclick="gerarExcel(2)" name="submitBtn">Limites</button>
          <button class="botaoagm" type="button" onclick="gerarExcel(4)" name="submitBtn">Resumo</button>
        </div>

      </ul>
    </form>


    <!--<table style="display:none;" summary="Submitted table designs" id="example">-->

    <table style="display:none;" >
      <thead>
        <tr>
          <th scope="col">Empresa</th>
          <th scope="col">DPP</th>
          <th scope="col">Apólice</th>
          <th scope="col">Segurado</th>
          <th scope="col">Início Vigência</th>
          <th scope="col">Fim Vigência</th>
          <th scope="col">Minimum premium invoiced</th>
          <th scope="col">Minimum premium paid</th>
          <th scope="col">Premium adjustment paid</th>
          <th scope="col">Total premium paid</th>
          <th scope="col">Credit limit fees invoiced</th>
          <th scope="col">Credit limit fees paid</th>
        </tr>
      </thead>
      <tbody id="tabelaResultado">

      </tbody>
    </table>


    <div class="divisoria01"></div>



  </div>

  <script>

    window.onload = function() {
    var dataCorte = document.getElementById('dataFinal');
    var hoje = new Date();
    var dd = String(hoje.getDate()).padStart(2, '0');
    var mm = String(hoje.getMonth() + 1).padStart(2, '0'); // Janeiro é 0!
    var aaaa = hoje.getFullYear();
    dataCorte.value = aaaa + '-' + mm + '-' + dd; // O formato deve ser aaaa-mm-dd
    };



    function gerarExcel(opcaoEscolhida) {
      var dppName = document.getElementById('dppName').value;
      var dataInicial = document.getElementById('dataInicial').value;
      var dataFinal = document.getElementById('dataFinal').value;
      
      var url = 'https://siga.coface.com/src/role/relatorios/interf/GerarExcel.php?relatorioNome=CAPRI&dppName=' + dppName + '&dataInicial=' + dataInicial + '&dataFinal=' + dataFinal + '&opcoes=' + opcaoEscolhida
      window.open(url, '_blank')
      return true;
    }



    function validarProcedure() {
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