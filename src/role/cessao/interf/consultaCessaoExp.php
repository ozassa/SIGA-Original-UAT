<?php function getStatus($key) {
  $value='indefinido';
  switch ($key) {
  case 0: $value='Em cadastramento'; break;
  case 1: $value='Aguard. Aprova&ccedil;&atilde;o'; break;
  case 2: $value='V&aacute;lido'; break;
  case 3: $value='Cancelado'; break;
  case 4: $value='Cancelamento Solicitado'; break;
  case 5: $value='Encerrada'; break;
  }
  return $value;
}

if($role["bancoBB"]){
  $query = "
     SELECT DISTINCT cdbb.codigo, ag.codigo, cdbb.status, ag.name, cdbb.dateClient, cdbb.id
     FROM CDBB cdbb
       Join Agencia ag   ON (ag.id = cdbb.idAgencia)
       Join CDBBDetails cdd ON (cdd.idCDBB = cdbb.id)
       JOIN UsersNurim us ON (us.idUser = $userID)
       JOIN Nurim nu ON (nu.id = us.idNurim AND ag.idNurim = nu.id)
     WHERE cdbb.idInform = $idInform AND (cdbb.status = 2 OR cdbb.status = 1)
     ORDER BY cdbb.codigo
  ";
  //TESTE
 /* $query = "
     SELECT DISTINCT cdbb.codigo, ag.codigo, cdbb.status, ag.name, cdbb.dateClient, cdbb.id
     FROM CDBB cdbb
       Join Agencia ag   ON (ag.id = cdbb.idAgencia)
       Join CDBBDetails cdd ON (cdd.idCDBB = cdbb.id)
       JOIN UsersNurim us ON (us.idUser = 1461)
       JOIN Nurim nu ON (nu.id = us.idNurim AND ag.idNurim = nu.id)
     WHERE cdbb.idInform = $idInform 
     ORDER BY cdbb.codigo
  ";*/
}else if($role["bancoParc"]){
  $query = "
     SELECT DISTINCT cdp.codigo, ag.codigo, cdp.status, ag.name, cdp.dateClient, cdp.id, bc.id
     FROM CDParc cdp
       JOIN Banco bc ON (bc.idUser = $userID)
       Join Agencia ag   ON (ag.id = cdp.idAgencia)
       Join CDParcDetails cdd ON (cdd.idCDParc = cdp.id)
     WHERE cdp.idInform = $idInform AND (cdp.status = 2 OR cdp.status = 1) AND cdp.idBanco = bc.id
     ORDER BY cdp.codigo
  ";
}else{
  $query = "
     SELECT DISTINCT cdob.codigo, cdob.agencia, cdob.status, cdob.name, cdob.dateClient, cdob.id
     FROM CDOB cdob
       Join CDOBDetails cdd ON (cdd.idCDOB = cdob.id)
     WHERE cdob.idInform = $idInform AND cdob.status = 2
     ORDER BY cdob.codigo
  ";
}

?>

<style>
  .css-link {
    color: #777777;
    text-decoration: underline;
    padding: 0px 2px;
    font-weight: bold;
  }
</style>

<?php require_once("../../../navegacao.php");?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
  <table>
<caption><?php echo htmlspecialchars($_REQUEST["name"], ENT_QUOTES, 'UTF-8'); ?></caption>
    <thead>
      <tr>
        <td>C&oacute;d. Cess&atilde;o</td>
        <td>Ag&ecirc;ncia</td>
        <td>Status</td>
      </tr>
    </thead>
    <tbody>
      <?php $cur=odbc_exec($db,$query);
        $i = 0;
        while (odbc_fetch_row($cur)) {
          $i++;
          //$idImporter = odbc_result($cur,'id');
          $status = odbc_result($cur,3);
          $agencia = odbc_result($cur,2);
          $cod = odbc_result($cur,1);
          $idCDBB = odbc_result($cur,6);
          $dateEnv = odbc_result($cur, 5);
          list($ano, $mes, $dia) = explode('-', $dateEnv);
          $codigo = $cod."/".$ano; ?>
          <tr>
            <td> <a href="../cessao/Cessao.php?comm=consultaImp&idInform=<?php echo urlencode($idInform); ?>&codigo=<?php echo urlencode($cod); ?>&agencia=<?php echo urlencode($agencia); ?>&nomeExportador=<?php echo urlencode($name); ?>&NomeSegurado=<?php echo urlencode($_REQUEST['name']); ?>&codCessao=<?php echo urlencode($codigo); ?>">
    <?php echo htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'); ?>
</a>
</td>
            <td>(<?php echo $agencia;?>) <?php echo odbc_result($cur,4);?></td>
            <td><?php echo getStatus ($status);?></td>
          </tr>
        <?php 
        }
        if ($i == 0) { ?>
          <tr>
            <td colspan="3">Nenhuma Cess&atilde;o Cadastrada</td>
          </tr>
        <?php 
        }
      $total = $i;
      ?>
    </tbody>
  </table>

  <form action="<?php echo $root;?>role/cessao/Cessao.php" method="post">
    <div class="barrabotoes">
      <button class="botaovgm" type="button" onClick="this.form.comm.value='consultaCessao';this.form.submit()">Voltar</button>
      <input type="hidden" name="comm">
    </div>          
  </form>
</div>