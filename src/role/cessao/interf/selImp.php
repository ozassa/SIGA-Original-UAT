<?php 


  function TrataData($data, $tipo, $saida){

    if($data == ''){
      return '';
    }

    #
    # Variavel $data é a String que contém a Data em qualquer formato
    # Variavel $tipo é que contém o tipo de formato data.
    # $tipo :
    #   1 - Brasil - No formato -> Dia/Mes/Ano ou DD/MM/YYYY
    #   2 - USA  - No formato -> YYYY-Mes-Dia ou YYYY-MM-DD
    #
    # $saida :
    #       1 - Brasil
    #       2 - USA
    #
    # Obs
    # Esta função não funciona com timestemp no formato a seguir :
    # DD/MM/YYYY H:M:S.MS ou YYYY-MM-DD H:M:S:MS
    # Pode configurar o formato da Data

    $data = explode(" ", $data);

    if ($tipo == 1) {
      list($dia, $mes, $ano) = explode("[/-]", $data[0]);
    } elseif ( $tipo == 2 ) {
      list($ano, $mes, $dia) = explode("[-/]", $data[0]);
    } else {
      $msg = "Erro - Formato de data não existe.";
    }

    if ($saida == 1) {
      return $dia."/".$mes."/".$ano;
    } elseif ($saida == 2) {
      return $ano."-".$mes."-".$dia;
    } else {
      return 0;
    }
  }

  $sql = "SELECT * FROM Inform WHERE id = $idInform";
  $cur = odbc_exec($db, $sql);
  
  $moeda = odbc_result($cur, "currency");
  if ($moeda == "1") {
    $ext = "R$";
  } else if ($moeda == "2") {
     $ext = "US$";
  } else if ($moeda == "6") {
     $ext = "€";
  }
?>

<script type="text/javascript">
  function operacao(str){
    document.getElementById('sub').value = str;
  }
</script>

<style>
  p {
    margin-bottom: 7px;
  }
  
  thead th {
    font-size: 10px;
  }

  .p_left {
    width: 10%;
    float: left;
    font-weight: bold;
  }

  .p_right {
    float: left;
  }
</style>

<?php $bancoName = isset($bancoName) ? $bancoName : ''; ?>

<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
  <form action="<?php echo $root;?>role/cessao/Cessao.php#cessao" method="post">
    <input type=hidden name="comm" value="gravaBB">
    <input type=hidden name="idInform" value="<?php echo $idInform;?>">
    <input type=hidden name="agencia" value="<?php echo $agencia;?>">
    <input type=hidden name="idAgencia" value="<?php echo $idAgencia;?>">
    <input type=hidden name="bancoName" value="<?php echo $bancoName;?>">
    <input type=hidden name="idBanco" value="<?php echo $idBanco;?>">
    <input type=hidden name="tipoBanco" value="<?php echo $tipoBanco;?>">
    <input type=hidden name="codBanco" value="<?php echo $codBanco;?>">
    <input type=hidden name="idImporter" value="">

    <?php if (isset($msgC)) { ?>
      <label style="color:#F00"><?php echo $msgC; ?></label>
    <?php } ?>

    <?php 
      if ($tipoBanco == 1) {
        // Buscar o idAgencia
        $query = "SELECT id FROM CDBB WHERE status = 0 AND idAgencia = $idAgencia AND idInform = $idInform";
        $cur = odbc_exec ($db, $query);

        if (odbc_fetch_row($cur)) {
          $idCDBB = odbc_result ($cur,'id');
        } else {
          $q = "INSERT INTO CDBB (idInform, idAgencia) VALUES ($idInform, $idAgencia)";
          $cur = odbc_exec($db, $q);

          $q = "SELECT max(id) FROM CDBB";
          $cur = odbc_exec($db, $q);

          $idCDBB = odbc_result($cur, 1);
        }
      } else if($tipoBanco == 2) {
          // Buscar o idAgencia
          $query = "SELECT id FROM CDParc WHERE status = 0 AND idAgencia = $idAgencia AND idInform = $idInform";
          $cur = odbc_exec ($db, $query);

          if (odbc_fetch_row($cur)) {
            $idCDParc = odbc_result ($cur,'id');
          } else {
            $q = "INSERT INTO CDParc (idInform, idAgencia, idBanco) VALUES ($idInform, $idAgencia, $idBanco)";
            $cur = odbc_exec($db, $q);

            $q = "SELECT max(id) FROM CDParc";
            $cur = odbc_exec($db, $q);

            $idCDParc = odbc_result($cur, 1);
          }
      } else if($tipoBanco == 3) {
        $query = "SELECT id FROM CDOB WHERE status = 0 AND idBanco = $idBanco AND idInform = $idInform";
        $cur = odbc_exec ($db, $query);

        if (odbc_fetch_row($cur)) {
          $idCDOB = odbc_result ($cur,'id');
          $q = "UPDATE CDOB SET idBanco=$idBanco, agencia=$agencia, name='$agNome', endereco='$agEnd', cidade='$agCid', idRegion=$idRegion, cnpj='$agCNPJ', ie='$agIE' WHERE id=$idCDOB";

          $cur = odbc_exec($db, $q);
        } else {
          // retirar o id Banco
          $q = "INSERT INTO CDOB (idInform, idBanco, agencia, name, endereco, cidade, idRegion, cnpj, ie) VALUES ($idInform, $idBanco, $agencia, '$agNome', '$agEnd', '$agCid', $idRegion, '$agCNPJ', '$agIE')";
          $cur = odbc_exec($db, $q);

          $q = "SELECT max(id) FROM CDOB";
          $cur = odbc_exec($db, $q);

          $idCDOB = odbc_result($cur, 1);
        }
      }
    
      if ($tipoBanco == 3) {
        $q = "SELECT name FROM CDOB WHERE id = $idCDOB";
        $ag = odbc_exec ($db, $q);

        $agName = odbc_result($ag, 1);
      } else {    
        $q = "SELECT name FROM Agencia WHERE id = $idAgencia";
        $ag = odbc_exec ($db, $q);

        $agName = odbc_result($ag, 1);
      }
      
      $q = "SELECT name FROM Banco WHERE id = $idBanco";
      $bc = odbc_exec ($db, $q);

      $bancoName = odbc_result($bc, 1);
    ?>
    
    <div class="p_left">
      <?php if ($bancoName) { ?>
        <p>Banco:</p>
      <?php } ?>
      <?php if ($agencia) { ?>
        <p>Ag&ecirc;ncia:</p>
      <?php } ?>
    </div>

    <div class="p_right">
      <?php if ($bancoName) { ?>
        <p><?php echo $bancoName; ?></p>
      <?php } ?>
      <?php if ($agencia) { ?>
        <p><?php echo $agencia;?> - <?php echo $agName;?></p>
      <?php } ?>
    </div>

    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">

    <label>
      <h2>Lista de Compradores Inclusos na atual Cess&atilde;o de Direitos</h2>
    </label>
      
    <table class="tabela01">
      <thead>
        <tr>
          <th width="5%">&nbsp;</th>
          <th>CRS</th>
          <th>Comprador</th>
          <th>Pa&iacute;s</td>
          <th align="center">Cr&eacute;dito Concedido<br>(<?php echo $ext;?> Mil)</th>
          <th align="center">Cr&eacute;dito Tempor&aacute;rio<br>(<?php echo $ext;?> Mil)</th>
          <th align="center">V&aacute;lido At&eacute;</th>
          <?php 
            if($tipoBanco == 1) { ?>
              <th  width="5%">
                <button type="button" class="botaoagm" onClick="operacao('Remover Todos'); this.form.idCDBB.value=<?php echo $idCDBB;?>;this.form.submit(); ">Remover Todos</button></th>
              </th>
              <?php 
            } else if($tipoBanco == 2) { ?>
              <th  width="5%">
                <button type="button" class="botaoagm" onClick="operacao('Remover Todos'); this.form.idCDParc.value=<?php echo $idCDParc;?>;this.form.submit(); " name="sub">Remover Todos</button>
              </th>
              <?php 
            } else { ?>
              <th width="5%">
                <button type="button" class="botaoagm" onClick="operacao('Remover Todos'); this.form.idCDOB.value=<?php echo $idCDOB;?>;this.form.submit(); " name="sub">Remover Todos</button>
              </th>
              <?php 
            } ?>
        </tr>
      </thead>

      <?php 
        if ($tipoBanco == 1) {
          $query = "SELECT Imp.id AS Id_Importador, Cast(C.code AS varchar) + Right('000000' + Replace(Imp.c_Coface_Imp, ' ', ''), 6) AS Num_CRS, Upper(Imp.name) AS Nome_Comprador, 
                            C.name AS Nome_Pais, IsNull(Imp.credit, 0) / 1000 AS Cred_Concedido, IsNull(Imp.creditTemp, 0) / 1000 AS Cred_Temporario,
                            Case 
                              When IsNull(Imp.creditTemp, 0) <> 0 Then Imp.validityDate
                              Else NULL
                            End AS d_Validade
                      FROM Importer Imp
                        INNER JOIN Country C ON Imp.idCountry = C.id
                        INNER JOIN CDBBDetails CDB ON CDB.idImporter = Imp.id
                      WHERE CDB.idCDBB = $idCDBB 
                      ORDER BY Imp.name";
        } else if($tipoBanco == 2) {
          $query = "SELECT Imp.id As Id_Importador, Cast(C.code as varchar) + Right('000000' + Replace(Imp.c_Coface_Imp, ' ', ''), 6) AS Num_CRS, Upper(Imp.name) AS Nome_Comprador, 
                            C.name AS Nome_Pais, IsNull(Imp.credit, 0) / 1000 As Cred_Concedido, IsNull(Imp.creditTemp, 0) / 1000 As Cred_Temporario, 
                            Case 
                              When IsNull(Imp.creditTemp, 0) <> 0 Then Imp.validityDate
                              Else NULL
                            End As d_Validade
                      FROM Importer Imp
                        INNER JOIN Country C ON Imp.idCountry = C.id
                        INNER JOIN CDParcDetails CDD ON CDD.idImporter = Imp.id
                      WHERE CDD.idCDParc = $idCDParc
                      ORDER BY Imp.name";
        } else {
          $query = "SELECT Imp.id As Id_Importador, Cast(C.code as varchar) + Right('000000' + Replace(Imp.c_Coface_Imp, ' ', ''), 6) AS Num_CRS, Upper(Imp.name) AS Nome_Comprador, 
                            C.name AS Nome_Pais, IsNull(Imp.credit, 0) / 1000 As Cred_Concedido, IsNull(Imp.creditTemp, 0) / 1000 As Cred_Temporario, 
                            Case 
                              When IsNull(Imp.creditTemp, 0) <> 0 Then Imp.validityDate
                              Else NULL
                            End As d_Validade
                      FROM Importer Imp
                        INNER JOIN Country C ON Imp.idCountry = C.id
                        INNER JOIN CDOBDetails CDD ON CDD.idImporter = Imp.id
                      WHERE CDD.idCDOB = $idCDOB
                      ORDER BY Imp.name";
        }

        $cur = odbc_exec($db, $query);
        $contR = 0;
        $i = 0; 
      ?>

      <tbody>
        <?php  
          while (odbc_fetch_row($cur)) {
            $i++;
            $contR++;
            $idImporter = odbc_result($cur, 'Id_Importador'); ?>
            <tr <?php echo ((($i % 2) == 0) ? ' class="odd"' : ""); ?>>
              <td width="5%"><?php echo $i; ?></td>
              <td class="texto"><?php echo odbc_result($cur, 'Num_CRS'); ?></td>
              <td class="texto"><?php echo odbc_result($cur, 'Nome_Comprador'); ?></td>
              <td class="texto"><?php echo odbc_result($cur, 'Nome_Pais'); ?></td>
              <td class="texto" align="center"><?php echo number_format(odbc_result($cur, 'Cred_Concedido'), 0, '', '.'); ?></td>
              <td class="texto" align="center"><?php echo number_format(odbc_result($cur, 'Cred_Temporario'), 0, '', '.'); ?></td>
              <td class="texto" align="center"><?php echo TrataData(odbc_result($cur, 'd_Validade'), 1, 2); ?></td>
              <td width="5%" align="center">
                <button type="button" class="botaoagm" onClick="operacao('Excluir'); this.form.idImporter.value=<?php echo $idImporter; ?>;this.form.submit(); ">Excluir</button>
              </td>
            </tr>
            <?php 
          } // while

          if ($i == 0) { ?>
            <tr class="odd">
              <td align="center" colspan="8">Nenhum Comprador Cadastrado</td>
            </tr>
            <?php 
          }

          $totalR = $i; ?>
      </tbody>
    </table>

    <br clear="all">
    
    <label>
      <h2>Lista de Compradores sem Cess&atilde;o de Direitos</h2>
    </label>
      
    <table class="tabela01">
      <thead>
        <tr>
          <th width="5%">&nbsp;</th>
          <th>CRS</th>
          <th>Comprador</th>
          <th>Pa&iacute;s</td>
          <th align="center">Cr&eacute;dito Concedido<br>(<?php echo $ext;?> Mil)</th>
          <th align="center">Cr&eacute;dito Tempor&aacute;rio<br>(<?php echo $ext;?> Mil)</th>
          <th align="center">V&aacute;lido At&eacute;</th>
          <?php 
            if ($tipoBanco == 1) { ?>
              <th colspan="1" width="5%">
                <button type="button" class="botaoagm" onClick="operacao('Incluir Todos');  this.form.idCDBB.value=<?php echo $idCDBB;?>;this.form.submit(); ">Incluir Todos&nbsp;</button>
              </th>
              <?php 
            } else if ($tipoBanco == 2) { ?>
              <th colspan="1" width="5%">
                <button type="button" class="botaoagm" onClick="operacao('Incluir Todos');  this.form.idCDParc.value=<?php echo $idCDParc;?>;this.form.submit(); ">Incluir Todos&nbsp;</button>
              </th>
              <?php 
            } else { ?>
              <th colspan="1" width="5%">
                <button type="button" class="botaoagm" onClick="operacao('Incluir Todos');  this.form.idCDOB.value=<?php echo $idCDOB;?>;this.form.submit(); ">Incluir Todos&nbsp;</button>
              </th>
              <?php 
            } 
          ?>
        </tr>
      </thead>

      <?php 
        $query = "SELECT Imp.id As Id_Importador, Cast(C.code as varchar) + Right('000000' + Replace(Imp.c_Coface_Imp, ' ', ''), 6) AS Num_CRS, Upper(Imp.name) AS Nome_Comprador, 
                          C.name AS Nome_Pais, IsNull(Imp.credit, 0) /1000 As Cred_Concedido, IsNull(Imp.creditTemp, 0) / 1000 As Cred_Temporario,
                          Case 
                            When IsNull(Imp.creditTemp, 0) <> 0 Then Imp.validityDate
                            Else NULL
                          End As d_Validade
                    FROM Importer Imp
                      INNER JOIN Country C ON C.id = Imp.idCountry
                    WHERE Imp.idInform = $idInform AND Imp.state NOT in (1,3,7,8,9) AND (Imp.credit > 0 Or Imp.creditTemp > 0)
                          AND Imp.id NOT in (SELECT cdd.idImporter
                                                FROM CDBBDetails cdd
                                                  INNER JOIN CDBB cd ON (cdd.idCDBB = cd.id)
                                                WHERE cd.status Not In (3, 5)
                                              UNION
                                              SELECT cdod.idImporter
                                                FROM CDOBDetails cdod
                                                  INNER JOIN CDOB cdo ON (cdo.id = cdod.idCDOB)
                                                WHERE cdo.status Not In (3, 5)
                                              UNION
                                              SELECT cdpc.idImporter
                                                FROM CDParcDetails cdpc
                                                  INNER JOIN CDParc cdp ON (cdp.id = cdpc.idCDParc)
                                                WHERE cdp.status Not In (3, 5)
                                            ) 
                    ORDER BY Imp.name";
        $cur = odbc_exec($db, $query);

        $i = 0;
      ?>
    
      <tbody>
        <?php
          while (odbc_fetch_row($cur)) {
            $i++;
            $idImporter = odbc_result($cur, 'Id_Importador'); ?>
            <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : ""); ?>>
              <td width="5%"><?php echo $i; ?></td>
              <td class="texto"><?php echo odbc_result($cur, 'Num_CRS'); ?></td>
              <td class="texto"><?php echo odbc_result($cur, 'Nome_Comprador'); ?></td>
              <td class="texto"><?php echo odbc_result($cur, 'Nome_Pais'); ?></td>
              <td class="texto" align="center"><?php echo number_format(odbc_result($cur, 'Cred_Concedido'), 0, '', '.'); ?></td>
              <td class="texto" align="center"><?php echo number_format(odbc_result($cur, 'Cred_Temporario'), 0, '', '.'); ?></td>
              <td class="texto" align="center"><?php echo TrataData(odbc_result($cur, 'd_Validade'), 1, 2); ?></td>
              <td width="5%" align="center">
                <button type="button" class="botaoagm" onClick="operacao('Incluir'); this.form.idImporter.value=<?php echo $idImporter;?>;this.form.submit(); ">Incluir</button>
              </td>
            </tr>
            <?php 
          } // while
          
          if ($i == 0) { ?>
            <tr class="odd">
              <td align="center" colspan="8">Nenhum Comprador Cadastrado</td>
            </tr>
            <?php 
          }

          $total = $i;
        ?>
      </tbody>
    </table>

    <div style="clear:both">&nbsp;</div>
    
    <input type="hidden" name="contR" value="<?php echo $contR;?>">
    <input type="hidden" name="sub" id="sub" value="">
    <input type="hidden" name="idCDBB" value="<?php echo $idCDBB;?>">
    <input type="hidden" name="idCDParc" value="<?php echo $idCDParc;?>">
    <input type="hidden" name="idCDOB" value="<?php echo $idCDOB;?>">
    <input type="hidden" name="total" value="<?php echo $total;?>">
    <input type="hidden" name="totalR" value="<?php echo $totalR;?>">
    <input type="hidden" name="agencia" value="<?php echo $agencia;?>">
    <input type="hidden" name="agNome" value="<?php echo $agNome;?>">
    <input type="hidden" name="agEnd" value="<?php echo $agEnd;?>">
    <input type="hidden" name="agCid" value="<?php echo $agCid;?>">
    <input type="hidden" name="idRegion" value="<?php echo $idRegion;?>">
    <input type="hidden" name="agCNPJ" value="<?php echo $agCNPJ;?>">
    <input type="hidden" name="agIE" value="<?php echo $agIE;?>">
    
    <div class="barrabotoes">
      <button type="button" class="botaovgm" onclick="operacao('Voltar'); this.form.comm.value='<?php echo $tipoBanco == 3 ? 'cessaoBB' : 'cessao'; ?>'; this.form.submit();">Voltar</button>
      <button type="button" class="botaoagm" onClick="operacao('Confirmar');  this.form.submit();">Confirmar</button>
    </div>
    
    <div style="clear:both">&nbsp;</div>
  </form>
</div>
