<?php
// alterado Hicom (Gustavo) - 05/01/04 - botão para reativar o informe caso cancelado 

require_once("../dve/interf/funcsDve.php");

include_once('../../../navegacao.php');


$idInform = $_REQUEST['idInform'];
$renova = $_REQUEST['renova'];
$envia = $_REQUEST['envia'];
$executa = $_REQUEST['executa'];
$idInform = $_REQUEST['idInform'];
$nome = $_REQUEST['nome'];
$executivo = $_REQUEST['executivo'];
$status = $_REQUEST['status'];
$userID = $_SESSION['userID'];

$idInform = (int) $idInform;

if ($executa == 1 and $idInform) {
  if ($renova == 1) {
    require("../client/renovacao.php");
  } else {
    $sql = "UPDATE Inform SET state = 1 WHERE id = ?";
    $cur = odbc_prepare($db, $sql);
    odbc_free_result($cur);

  }
}
?>



<div class="conteudopagina">

  <SCRIPT language="javascript">

    function vai(renova, idInform) {
      document.Form2.action = '<?php echo $root; ?>role/searchClient/RelacaoClientExecutivo.php';
      document.Form2.executa.value = 1;
      document.Form2.renova.value = renova;
      document.Form2.idInform.value = idInform;
      document.Form2.submit();
    }

  </SCRIPT>

  <FORM id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/RelacaoClientExecutivo.php"
    method="post">
    <input type="hidden" name="renova" value="0">
    <input type="hidden" name="executa" value="0">
    <input type="hidden" name="idInform" value="">
    <li class="campo2colunas">
      <label>Nome:</label>
      <input type="text" name="nome" size="50" class="caixa"><br>
    </li>
    <li class="campo2colunas">
      <label>Executivo:</label>
      <input type="text" name="executivo" size="50" class="caixa">
    </li>

    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
      <input type="hidden" name="envia" value="1">
      <button type="button" class="botaoagm" onclick="this.form.submit()">OK</button>
    </li>


    <?php



    if ($envia) {
      $query = "SELECT inf.id, inf.name, u.name, inf.state, inf.nick,
              inf.idAnt, inf.startValidity, inf.endValidity, inf.idUser, inf.idRegion
              FROM Inform inf
              LEFT JOIN Users u ON (u.id = inf.idUser)
              WHERE inf.name IS NOT NULL";

      // Array para armazenar os parâmetros
      $params = [];

      if ($status) {
        $query .= " AND inf.state = ?";
        $params[] = $status;
      } else {
        $query .= " AND inf.state <> ?";
        $params[] = 9;
      }

      if ($nome) {
        $query .= " AND inf.name LIKE ?";
        $params[] = strtoupper($nome) . '%';
      }

      if ($executivo) {
        $query .= " AND UPPER(u.name) LIKE ?";
        $params[] = '%' . strtoupper($executivo) . '%';
      }

      $query .= " ORDER BY inf.name";

      // Preparar a consulta
      $stmt = odbc_prepare($db, $query);

      // Executar a consulta com os parâmetros
      $cur = odbc_execute($stmt, $params);

      if ($cur) {
        echo "Consulta executada com sucesso.";
      } else {
        echo "Erro ao executar a consulta.";
      }

      // Liberar recursos para evitar problemas
      odbc_free_result($stmt);
    }


    // apresentacao do resultado
    ?>
    <table id="example" class="tabela01">
      <thead>
        <tr>
          <th width="60%">Empresa</th>
          <th width="25%">Executivo</th>
          <th width="15%">Status</th>
        </tr>
      </thead>
      <?php

      if ($envia) {
        // converte a data de yyyy-mm-dd para dd/mm/yyyy
      
        if (!function_exists('ymd2dmy')) {
          function ymd2dmy($d)
          {
            if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)) {
              return "$v[3]/$v[2]/$v[1]";
            }
            return $d;
          }
        }

        if (!function_exists('check_ant')) {
          function check_ant($idAnt)
          {
            global $db;

            // Consulta com placeholder
            $sql = "SELECT state FROM Inform WHERE id = ?";

            // Preparar a consulta
            $stmt = odbc_prepare($db, $sql);

            // Garantir que $idAnt seja um inteiro
            $idAnt = (int) $idAnt;

            // Executar a consulta com o parâmetro
            $result = odbc_execute($stmt, [$idAnt]);

            if ($result) {
              // Obter o valor da primeira coluna
              $state = odbc_result($stmt, 1);

              // Verificar o estado
              if ($state == 10 || $state == 11) {
                odbc_free_result($stmt); // Liberar recursos
                return true;
              }
            }

            odbc_free_result($stmt); // Liberar recursos
            return false;
          }
        }


        if (!function_exists('regiadochefe')) {
          function regiadochefe($db, $userID, $hc_idRegion)
          {
            // Consulta SQL com placeholders
            $sql = "SELECT COUNT(UN.idRegion) AS qtd
                FROM UserRole UR, UserRegion UN
                WHERE UR.idRole = 3
                AND UR.idUser = UN.idUser
                AND UR.idUser IN (SELECT idUser FROM UserRegion WHERE idRegion = ?)
                AND UN.idRegion IN (SELECT idRegion FROM UserRegion WHERE idUser = ?)";

            // Preparar a consulta
            $cur = odbc_prepare($db, $sql);

            // Garantir que as variáveis sejam seguras (inteiros)
            $hc_idRegion = (int) $hc_idRegion;
            $userID = (int) $userID;

            // Executar a consulta com os parâmetros
            $result = odbc_execute($cur, [$hc_idRegion, $userID]);

            if ($result) {
              // Obter o valor da primeira coluna
              $qtd = odbc_result($cur, 1);

              // Liberar recursos
              odbc_free_result($cur);

              // Retornar true se qtd > 0
              return $qtd > 0;
            } else {
              // Liberar recursos em caso de erro
              odbc_free_result($cur);
              return false;
            }
          }
        }






        if (!function_exists('regiaok')) {
          function regiaok($db, $userID, $hc_idRegion)
          {
            // Consulta SQL com placeholders
            $sql = "SELECT COUNT(id) AS qtd 
                FROM UserRegion 
                WHERE idUser = ? 
                AND idRegion = ?";

            // Preparar a consulta
            $cur = odbc_prepare($db, $sql);

            // Garantir que as variáveis sejam seguras (inteiros)
            $userID = (int) $userID;
            $hc_idRegion = (int) $hc_idRegion;

            // Executar a consulta com os parâmetros
            $result = odbc_execute($cur, [$userID, $hc_idRegion]);

            if ($result) {
              // Obter o valor da primeira coluna
              $qtd = odbc_result($cur, 1);

              // Liberar recursos
              odbc_free_result($cur);

              // Retornar true se qtd > 0
              return $qtd > 0;
            } else {
              // Liberar recursos em caso de erro
              odbc_free_result($cur);
              return false;
            }
          }
        }



        $i = 0;
        ?>
        <tbody>
          <?php
          while (odbc_fetch_row($cur)) {
            if ($i % 2 == 0) {
              $class = "odd";
            } else {
              $class = "";
            }
            $inform = odbc_result($cur, 1); //chave de busca para linkar o informe
            $clientR = odbc_result($cur, 2);
            $executiveR = odbc_result($cur, 3); //NO FUTURO OTIMIZAR: $executive e $executiveR
            $stateR = odbc_result($cur, 4);
            $idAnt = odbc_result($cur, 6);
            $start = ymd2dmy(odbc_result($cur, 7));
            $end = ymd2dmy(odbc_result($cur, 8));

            $hc_idUser = trim("" . odbc_result($cur, 9));
            $hc_idRegion = trim("" . odbc_result($cur, 10));

            $hc_visualiza = false;
            // if ($hc_idUser == trim("" . $userID) || $role["credit"])
        
            echo ("<tr class=$class>
       <td><a href=ListClient.php?comm=mudaRelacao&idInform=$inform>$clientR</a></td>
       <td>$executiveR</td>");
            switch ($stateR) {
              case 1:
                $stateR = "Novo";
                break;
              case 2:
                $stateR = "Preenchido";
                break;
              case 3:
                $stateR = "An. Crédito";
                break;
              case 4:
                $stateR = "Tarifação";
                break;
              case 5:
                $stateR = "Oferta";
                break;
              case 6:
                $stateR = "Proposta";
                break;
              case 7:
                $stateR = "1ª Parc. Pg";
                break;
              case 8:
                $stateR = "Alterado";
                break;
              case 9:
                $stateR = "Cancelado";
                break;
              case 10:
                $stateR = "Apólice";
                break;
              case 11:
                $stateR = "Encerrado";
                break;
            }
            echo ("<td class=texto>$stateR</td>
  </tr>");

            $i++;
          }

          if ($i == 0) {
            ?>
            <TR>
              <TD colspan=3 align=center class="textoBold">Nenhum Cliente Encontrado</TD>
            </TR>
            <?php
          }
      }

      ?>
      </tbody>
    </table>
    <div style="clear:both">&nbsp;</div>
  </form>

</div>