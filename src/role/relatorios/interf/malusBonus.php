<?php


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

        <!--<li class="campo2colunas">
          <label></label>
          <input type="text" name="mesName" id="mesName" required="required">
        </li>-->

        <li class="campo2colunas">
          <label>Mês de Final de Risco</label>
          <select name="mesName" id="mesName">
            <option selected value="1">1</option>
            <option selected value="2">2</option>
            <option selected value="3">3</option>
            <option selected value="4">4</option>
            <option selected value="5">5</option>
            <option selected value="6">6</option>
            <option selected value="7">7</option>
            <option selected value="8">8</option>
            <option selected value="9">9</option>
            <option selected value="10">10</option>
            <option selected value="11">11</option>
            <option selected value="12">12</option>
            
          </select>
        </li>

        <li class="campo2colunas">
          <label>Ano de Final de Risco</label>
          <input type="number" maxlength="4" name="anoName" id="anoName" required="required">
        </li>

        <li class="campo2colunas">
          <label>Opções</label>
          <select name="opcoes" id="opcoes">
            <option selected value="100">Relatório geral</option>
            <option value="200">Cálculo de Malus</option>
            <option value="300">Cálculo de Bônus</option>
          </select>
        </li>


        <div class="barrabotoes">
          <button class="botaoagm" type="button" name="submitBtn" onclick="gerarExcel()">Procurar</button>
        </div>
      </ul>
    </form>

    <div class="divisoria01"></div>


  </div>

  <script>

    function gerarExcel() {
      console.log("clicado")
      var empresaName = document.getElementById('empresaName').value;
      var mesName = document.getElementById('mesName').value;
      var anoName = document.getElementById('anoName').value;
      var opcoes = document.getElementById('opcoes').value;
      var url = 'https://siga.coface.com/src/role/relatorios/interf/GerarExcel.php?relatorioNome=malusBonus&empresaName=' + empresaName + '&mesName=' + mesName + '&anoName=' + anoName + '&opcao=' + opcoes
      window.open(url, '_blank')
    }

    function validarProcedure() {
      var empresaName = document.getElementById('empresaName').value;
      var mesName = document.getElementById('mesName').value;
      var anoname = document.getElementById('anoName').value;
      if (empresaName === '' || mesName === '' || anoname === '') {
        alert('Por favor, preencha todos os campos necessários.');
        return false;
      }
      return true;
    }

    document.getElementById('frm').onsubmit = validarProcedure;
  </script>
</body>

</html>