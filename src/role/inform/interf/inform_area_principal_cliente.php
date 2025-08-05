<?php

include_once('../../../navegacao.php');

$userID = $_SESSION['userID'];

if ($field->getField("idNotification")) {
  $_SESSION['idNotification'] = $field->getField("idNotification");
  $idNotification = $field->getField("idNotification");
} else {
  $idNotification = isset($_SESSION['idNotification']) ? $_SESSION['idNotification'] : null;
}



?>
<div class="conteudopagina">


  <?php

  ////////////////////////////////////////////////////////////////////////////
  
  if (!function_exists('ymd2dmy')) {
    // converte a data de yyyy-mm-dd para dd/mm/yyyy
    function ymd2dmy($d)
    {
      if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d ?? '', $v)) {
        return "$v[3]/$v[2]/$v[1]";
      }
    }
  }
  /////////////////////////////////////////////////////////////////////////////
  if (!function_exists('get_de_st_inform')) {
    // Retorna o status do Inform
    function get_de_st_inform($status)
    {
      if ($status == 1) {
        return "Novo";

      } elseif ($status == 2) {
        return "Preenchido";

      } elseif ($status == 3) {
        return "Validado";

      } elseif ($status == 4) {
        return "Analisado";

      } elseif ($status == 5) {
        return "Tarifado";

      } elseif ($status == 6) {
        return "Proposta";

      } elseif ($status == 7) {
        return "Confirmado";

      } elseif ($status == 8) {
        return "Alterado";

      } elseif ($status == 9) {
        return "Cancelado";

      } elseif ($status == 10) {
        return "Ap&oacute;lice";

      } elseif ($status == 11) {
        return "Encerrado";
      } else {
        return "Indefinido ($status)";
      }
    }
  }
  ////////////////////////////////////////////////////////
  
  //print $idInform;
  
  //print '?'.$_SESSION['id_user'];
  
  $nao_exibebotao_novo = 0;



  if (isset($_REQUEST['idInform']) && $_SESSION['pefil'] != "C" && $_SESSION['pefil'] != "B") {
    $query = "SELECT idInsured from Inform where id = ?";
    $cury = odbc_prepare($db, $query);
    $params = [$_REQUEST['idInform']];
    odbc_execute($cury, $params);
    $idInsu = odbc_result($cury, 'idInsured');
    odbc_free_result($cury);


    // print $_REQUEST['idInform'].' ?'.$query;
  
    $query = "SELECT i.id, i.name, i.state, i.startValidity, i.i_Produto, IsNull(i.n_Apolice, '') As Apolice
			  FROM Inform i
				JOIN Insured ins ON
					 (ins.id = i.idInsured)
			 WHERE i.idInsured = " . $idInsu . "  
			 ORDER BY i.id ";

    //print '<br>'.$query;	 
  
  } else {

    //if($_SESSION['idx'] > 0 && $_SESSION['id_user'] == 0 ){
    //	   print $_SESSION['id_user'];
  
    $query = "select count(a.id) as qtd from Inform a inner join Inform_Usuarios b on b.idInform = a.id where b.idUser = ?";
    $curz = odbc_prepare($db, $query);
    $params= [$userID];
    odbc_execute($curz, $params);

    //print $query;
    if (odbc_result($curz, 'qtd') > 0) {
      $usuarioseg = 1;
    } else {
      $usuarioseg = 0;
    }

    odbc_free_result($curz);
    if ($usuarioseg == 1) {
      $query = "select a.id, a.name, a.state, a.startValidity, a.endValidity, a.i_Produto, IsNull(a.n_Apolice, '') As Apolice from Inform a 
					inner join Inform_Usuarios b on b.idInform = a.id
					where b.idUser = ? ORDER BY a.id";
      $nao_exibebotao_novo = 1;
      //print $query;
  
    } else {

      $query = "SELECT i.id, i.name, i.state, i.startValidity, i.i_Produto, IsNull(i.n_Apolice, '') As Apolice, i.endValidity
			  FROM Inform i
				JOIN Insured ins ON
					 (ins.id = i.idInsured)
			 WHERE ins.idResp = ?  
			 ORDER BY i.id ";

      //print $query;
    }
  }

  $params= [$userID];
  
  $cur = odbc_prepare($db, $query);
  odbc_execute($cur, $params);
  $acc = 0;


  //print $_SESSION['idx'];
  


  if (isset($msgINT)) {
    ?>
    <script> verErro('<?php echo $msgINT; ?>');</script>
    <?php
    $msgINT = '';

  }


  ?>

  <table width="100%">
    <caption>Seus informes:</caption>
    <thead>

      <tr>
        <th width="10%" align="left">
          Ap&oacute;lice
        </th>

        <th width="20%" align="left">
          Vig&ecirc;ncia
        </th>

        <th width="15%" align="left">
          Status
        </th>

        <th width="30%" align="left">
          Segurado
        </th>
        <th width="25%" align="left">
          Tipo Produto
        </th>

      </tr>
    </thead>
    <tbody>
      <?php



      while (odbc_fetch_row($cur)) {
        if ($acc == 0)
          $idInform_old = odbc_result($cur, 1);

        if (odbc_result($cur, 'state') > 3) {
          $caminho = 'changeImporter';
        } else {
          $caminho = 'inform_res';
        }
        ?>



        <tr>

          <td align="left">
            <a
              href="<?php echo $root; ?>role/inform/Inform.php?comm=<?php echo $caminho; ?>&idInform=<?php echo odbc_result($cur, 1); ?>"><?php echo (odbc_result($cur, 'Apolice')); ?></a>
          </td>


          <td align="left">
            <a
              href="<?php echo $root; ?>role/inform/Inform.php?comm=<?php echo $caminho; ?>&idInform=<?php echo odbc_result($cur, 1); ?>"><?php echo (ymd2dmy(odbc_result($cur, 4)) ? ymd2dmy(odbc_result($cur, 4)) . " at&eacute; " . ymd2dmy(odbc_result($cur, "endValidity")) : 'Informe em Cria&ccedil;&atilde;o'); ?></a>
          </td>

          <td align="left">
            <a
              href="<?php echo $root; ?>role/inform/Inform.php?comm=<?php echo $caminho; ?>&idInform=<?php echo odbc_result($cur, 1); ?>"><?php echo get_de_st_inform(odbc_result($cur, 'state')); ?></a>
          </td>

          <td align="left">
            <a
              href="<?php echo $root; ?>role/inform/Inform.php?comm=<?php echo $caminho; ?>&idInform=<?php echo odbc_result($cur, 1); ?>"><?php echo odbc_result($cur, 'name'); ?></a>
          </td>

          <td align="left">
            <a
              href="<?php echo $root; ?>role/inform/Inform.php?comm=<?php echo $caminho; ?>&idInform=<?php echo odbc_result($cur, 1); ?>"><?php if (odbc_result($cur, 'i_Produto') == 1)
                         echo 'Cr&eacute;dito Interno';
                       else if (odbc_result($cur, 'i_Produto') == 2)
                         echo 'Cr&eacute;dito Exporta&ccedil;&atilde;o';
                       else
                         echo ''; ?></a>
          </td>

        </tr>
        <?php
        $acc++;
      }
      odbc_free_result($cur);

      if ($acc == 0) { ?>
        <tr>

          <td colspan="5" style="text-align:center">
            Nenhum Informe Cadastrado
          </td>


        </tr>

      <?php }


      ?>
    </tbody>
  </table>
  <!--
  FUNÇÃO FOI INATIVA NO DIA 26/04/2012 POR RODOLFO TELES (SOLICITADO PELO CLOVIS ROSA)
  FUNÇÃO FOI REATIVADA  NO DIA 28/05/2012 POR ELIAS VAZ (SOLICITADO PELO CLOVIS ROSA)
  
  -->
  <div class="barrabotoes">
    <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
    <?php if ($nao_exibebotao_novo == 0) { ?>
      <button class="botaoagg" type="button"
        onClick="window.location = '<?php echo $root; ?>role/inform/Inform.php?comm=inform_res&Gerar_Novo_Inform=1&idInform_old=<?php echo $idInform_old; ?>';">Novo
        Informe</button>
    <?php } ?>

  </div>

  <!--  <div class="barrabotoes">
        <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
        <button class="botaoagg" type="button"  onClick="window.location = '<?php echo $root; ?>role/inform/Inform.php?comm=inform_res&Gerar_Novo_Inform=1&idInform_old=<?php echo $idInform_old; ?>';">Novo Informe</button>
    </div>-->

</div>