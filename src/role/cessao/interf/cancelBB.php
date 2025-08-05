<?php 


include_once('../../../navegacao.php');



function getStatus($key) {
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
     SELECT DISTINCT cdbb.codigo, ag.codigo, cdbb.status, ag.name, cdbb.dateClient, cdbb.id, 1 AS 'tipo'
     FROM CDBB cdbb
       Join Agencia ag   ON (ag.id = cdbb.idAgencia)
       Join CDBBDetails cdd ON (cdd.idCDBB = cdbb.id)
       Join Importer imp ON (imp.id = cdd.idImporter)
       JOIN UsersNurim us ON (us.idUser = $userID)
       JOIN Nurim nu ON (nu.id = us.idNurim AND ag.idNurim = nu.id)
     WHERE cdbb.idInform = $idInform AND cdbb.status = 2
     ORDER BY cdbb.codigo
  ";
}else if($role["bancoParc"]){
  $query = "
     SELECT DISTINCT cdp.codigo, ag.codigo, cdp.status, ag.name, cdp.dateClient, cdp.id, 2 AS 'tipo', bc.id
     FROM CDParc cdp
       JOIN Banco bc ON (bc.idUser = $userID)
       Join Agencia ag   ON (ag.id = cdp.idAgencia)
       Join CDParcDetails cdd ON (cdd.idCDParc = cdp.id)
     WHERE cdp.idInform = $idInform AND (cdp.status = 2 OR cdp.status = 1) AND cdp.idBanco = bc.id
     ORDER BY cdp.codigo
  ";
}else{
  $query = "
     SELECT DISTINCT cdob.codigo, cdob.agencia, cdob.status, bc.name, cdob.dateClient, cdob.id, 3 AS 'tipo'
     FROM CDOB cdob
       Join Banco bc   ON (bc.id = cdob.idBanco)
       Join CDOBDetails cdd ON (cdd.idCDOB = cdob.id)
     WHERE cdob.idInform = $idInform AND cdob.status = 2
     ORDER BY cdob.codigo
  ";
}
?>
<div class="conteudopagina">

<li class="campo3colunas"> 
<label><?php echo htmlspecialchars($_REQUEST['name'], ENT_QUOTES, 'UTF-8'); ?></label>
</li>

<table class="tabela01" style="width:950px !Important;">
   <thead>
      <tr>
        <th width="15%">Cód. Cessão</th>
        <th align="center">Agência</th>
        <th aling=center>Status</th>
      </tr>
  </thead>
  <tbody>
<?php
	  $cur=odbc_exec($db,$query);
	  $i = 0;
	  while (odbc_fetch_row($cur)) {
		$i++;
		//$idImporter = odbc_result($cur,'id');
		$status = odbc_result($cur,3);
		$agencia = odbc_result($cur,2);
		$codigo = odbc_result($cur,1);
		$idCDBB = odbc_result($cur,6);
		$tipo = odbc_result($cur,7);
		$dateEnv = odbc_result($cur, 5);
		list($ano, $mes, $dia) = split ('-', $dateEnv);
		$codigo = $codigo."/".$ano;
?>
      <tr <?php  echo ((($i % 2) != 0) ? 'class=odd' : "");?>>
        <td width="15%" align="center"> <a href="javascript:onClick=cancela(<?php  echo $idCDBB;?>,<?php  echo $tipo;?>,<?php  echo $idInform;?>)"> <?php  echo $codigo;?></a></td>
        <td align="center">(<?php  echo $agencia;?>) <?php  echo odbc_result($cur,4);?></td>
        <td><?php  echo getStatus ($status);?></td>
     </tr>
<?php

  } // while
  
  if ($i == 0) {  ?>
  <TR class="bgCinza">
    <TD align="center" colspan=3 class="bgCinza">Nenhuma Cessão Cadastrada</TD>
  </TR>

<?php
  }
$total = $i;
?></tbody>
</TABLE>

<form action="<?php echo $root;?>role/cessao/Cessao.php" method="post">
      <input type="hidden" name="comm">
      <div class="barrabotoes"> 
			<button class="botaovgm" type="button" onClick="this.form.comm.value='cancelCessaoBB';this.form.submit()">Voltar</button> 
      </div>
</form> 


<form name="cancel" action="<?php  echo $root;?>role/cessao/Cessao.php">
    <input type=hidden name="comm" value="cancelBB">
    <input type=hidden name="idCDBB" value="">
    <input type=hidden name="idInform" value="">
    <input type=hidden name="tipo" value="">
</form>




<script>
function cancela(myIdCDBB,myTipo,myIdInform) { 
if (confirm ("Deseja Realmente Cancelar essa Cessão de Direitos?")) {
   document.forms["cancel"].idCDBB.value=myIdCDBB;
   document.forms["cancel"].tipo.value=myTipo;
   document.forms["cancel"].idInform.value=myIdInform;
   window.open('<?php  echo $root;?>role/cessao/distrato.php?idCDBB='+myIdCDBB+'&idInform='+myIdInform+'&tipo='+myTipo+'&comm=gerapdf', 'pdf_window', 'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1,width=950,height=700');
   document.forms["cancel"].submit();
}
}
</script>
</div>

