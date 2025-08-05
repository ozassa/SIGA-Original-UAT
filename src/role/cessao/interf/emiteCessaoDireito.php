<?php
  
  $idUser = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;
  // $idUser = '6786';

  $sql = "EXEC SPR_BB_Consulta_Cessao_Direito '100', '".$idUser."', NULL, NULL";
  $rsSql = odbc_exec($db, $sql);

  $dados = array();
  while(odbc_fetch_row($rsSql)) {
    $id_Cessao = odbc_result($rsSql, "id_Cessao");
    $n_Apolice = odbc_result($rsSql, "n_Apolice");
    $Segurado = odbc_result($rsSql, "Segurado");
    $Banco = odbc_result($rsSql, "Banco");
    $Agencia = odbc_result($rsSql, "Agencia");
    $Cod_Cessao = odbc_result($rsSql, "Cod_Cessao");
    $Data_Solic = Convert_Data_Geral(substr(odbc_result($rsSql, "Data_Solic"), 0, 10));

    $dados[] = array(
      "id_Cessao"       => $id_Cessao,
      "n_Apolice"       => $n_Apolice,
      "Segurado"        => $Segurado,
      "Banco"           => $Banco,
      "Agencia"         => $Agencia,
      "Cod_Cessao"      => $Cod_Cessao,
      "Data_Solic"      => $Data_Solic
    );
  }

  

  $sqlPend = "EXEC SPR_BB_Consulta_Cessao_Direito '200', '".$idUser."', NULL, NULL";
  $rsSqlPend = odbc_exec($db, $sqlPend);

  $dadosPend = array();
  while(odbc_fetch_row($rsSqlPend)) {
    $id_Cessao = odbc_result($rsSqlPend, "id_Cessao");
    $n_Apolice = odbc_result($rsSqlPend, "n_Apolice");
    $Segurado = odbc_result($rsSqlPend, "Segurado");
    $Banco = odbc_result($rsSqlPend, "Banco");
    $Agencia = odbc_result($rsSqlPend, "Agencia");
    $Cod_Cessao = odbc_result($rsSqlPend, "Cod_Cessao");
    $Data_Solic = Convert_Data_Geral(substr(odbc_result($rsSqlPend, "Data_Solic"), 0, 10));
    $d_Aceite_Banco = Convert_Data_Geral(substr(odbc_result($rsSqlPend, "d_Aceite_Banco"), 0, 10));

    $dadosPend[] = array(
      "id_Cessao"       => $id_Cessao,
      "n_Apolice"       => $n_Apolice,
      "Segurado"        => $Segurado,
      "Banco"           => $Banco,
      "Agencia"         => $Agencia,
      "Cod_Cessao"      => $Cod_Cessao,
      "Data_Solic"      => $Data_Solic,
      "d_Aceite_Banco"  => $d_Aceite_Banco
    );
  }

  

  require_once("../../../navegacao.php"); 
?>

<div class="conteudopagina">

  <label>
    <h2>Rela&ccedil;&atilde;o de cess&otilde;es de direito a emitir</h2>
  </label>

  <table summary="" id="">
    <thead>
      <tr>
        <th>Ap&oacute;lice</th>
        <th>Segurado</th>
        <th>Banco</th>
        <th>Ag&ecirc;ncia</th>
        <th>Cod. Cess&atilde;o</th>
        <th>Data Solicita&ccedil;&atilde;o</th>
        <th>Op&ccedil;&otilde;es</th>
      </tr>
    </thead>  

    <?php if(empty($dados)){ ?>
      <tbody><tr><td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
    <?php 
      } else { 
        for ($a=0; $a < count($dados); $a++) { ?>
          <tr>
            <td><?php echo $dados[$a]['n_Apolice']; ?></td>
            <td><?php echo $dados[$a]['Segurado']; ?></td>
            <td><?php echo $dados[$a]['Banco']; ?></td>
            <td><?php echo $dados[$a]['Agencia']; ?></td>
            <td><?php echo $dados[$a]['Cod_Cessao']; ?></td>
            <td><?php echo $dados[$a]['Data_Solic']; ?></td>
            <td>
              <a href="<?php echo $host;?>src/role/cessao/Cessao.php?comm=viewEmiteCessaoDireito&id_Cessao=<?php echo $dados[$a]['id_Cessao']; ?>" class="btn">Visualizar</a>
            </td>
          </tr>
      <?php } ?>
    <?php } ?>    
  </table>
  
  <br clear="all">

  <label>
    <h2>Rela&ccedil;&atilde;o de cess&otilde;es de direito pendentes junto a Seguradora</h2>
  </label>

  <table summary="" id="">
    <thead>
      <tr>
        <th>Ap&oacute;lice</th>
        <th>Segurado</th>
        <th>Banco</th>
        <th>Ag&ecirc;ncia</th>
        <th>Cod. Cess&atilde;o</th>
        <th>Data Solicita&ccedil;&atilde;o</th>
        <th>Data Aceite Banco</th>
      </tr>
    </thead>  

    <?php if(empty($dadosPend)){ ?>
      <tbody><tr><td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
    <?php 
      } else { 
        for ($a=0; $a < count($dadosPend); $a++) { ?>
          <tr>
            <td><?php echo $dadosPend[$a]['n_Apolice']; ?></td>
            <td><?php echo $dadosPend[$a]['Segurado']; ?></td>
            <td><?php echo $dadosPend[$a]['Banco']; ?></td>
            <td><?php echo $dadosPend[$a]['Agencia']; ?></td>
            <td><?php echo $dadosPend[$a]['Cod_Cessao']; ?></td>
            <td><?php echo $dadosPend[$a]['Data_Solic']; ?></td>
            <td><?php echo $dadosPend[$a]['d_Aceite_Banco']; ?></td>
          </tr>
      <?php } ?>
    <?php } ?>    
  </table>
  
  <div class="barrabotoes">
    <button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/cessao/Cessao.php';">Voltar</button>
  </div>  
</div>