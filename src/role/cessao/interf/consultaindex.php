<TABLE border="0" cellSpacing="0" cellpadding="2" width="96%" align="center">

<a name=cessao></a>

  <TR>

    <TD><P>&nbsp;</P><hr></TD>

  </TR>

  <TR align="center">

    <TD align="center" width="100%"><A href="../cessao/Cessao.php?comm=consultaClientBB&idInform=<?php echo $idInform;?>">Consultar Todas as Cessões de Direito</a>

   </TD>

  </TR>

  <TR>

    <TD><hr></TD>

  </TR>

</TABLE>



<?php if($msg){

  echo "<p align=center><font color=#ff0000>$msg</font>";

}

?>



<?php function getStatus($key) {

  $value='indefinido';

  switch ($key) {

  case 0: $value='Em cadastramento'; break;

  case 1: $value='Aguard. Aprova&ccedil;&atilde;o'; break;

  case 2: $value='V&aacute;lido'; break;

  case 3: $value='Cancelado'; break;

  }

  return $value;

}



  $query = "

     SELECT DISTINCT cdob.codigo, cdob.agencia, cdob.status, bc.name, cdob.dateClient, cdob.id

     FROM CDOB cdob

       Join Banco bc   ON (bc.id = cdob.idBanco)

       Join CDOBDetails cdd ON (cdd.idCDOB = cdob.id)

     WHERE cdob.idInform = $idInform AND cdob.status = 2

     ORDER BY cdob.codigo

  ";

?>

<p align="center">Cancelar Cessão de Direito de Outros Bancos</p>

<TABLE border="0" cellSpacing=0 cellpadding="3" width="100%" align="center">

  <tr class="bgAzul">

    <td width="15%">Cód. Cessão</td>

    <td align="center">Agência</td>

    <td aling=center>Status</td>

  </tr>

<?php $cur=odbc_exec($db,$query);

  $i = 0;

  while (odbc_fetch_row($cur)) {

    $i++;

    //$idImporter = odbc_result($cur,'id');

    $status = odbc_result($cur,3);

    $agencia = odbc_result($cur,2);

    $codigo = odbc_result($cur,1);

    $idCDBB = odbc_result($cur,6);

    $dateEnv = odbc_result($cur, 5);

    list($ano, $mes, $dia) = split ('-', $dateEnv);

    $codigo = $codigo."/".$ano;

?>

  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>

    <td width="15%" align="center"> <a href="javascript:onClick=cancela(<?php echo $idCDBB;?>,3)" title="Cancelar Cessão de Direito"> <?php echo $codigo;?> </a></td>

    <td align="center">(<?php echo $agencia;?>) <?php echo odbc_result($cur,4); ?></td>

    <td><?php echo getStatus ($status);?></td>

 </tr>

<?php } // while

  if ($i == 0) {

?>

  <TR class="bgCinza">

    <TD align="center" colspan=3 class="bgCinza">Nenhuma Cessão Cadastrada</TD>

  </TR>



<?php }

?>

</table>



<br><br>

<center>

<form action="<?php echo $root;?>role/searchClient/ListClient.php" method="post">

<input type="hidden" name="idInform" value="<?php echo $idInform;?>">

<input type="hidden" name="comm">

<input class="servicos" type=button value="Voltar" onClick="this.form.comm.value='view';this.form.submit()">

</form>

</center>





<form name="cancel" action="<?php echo $root;?>role/cessao/Cessao.php">

<input type=hidden name="comm" value="cancelBackofficeOutros">

<input type=hidden name="idCDBB" value="">

<input type=hidden name="tipo" value="">

</form>







<script>

function cancela(myIdCDBB,myTipo) { 

if (confirm ("Deseja Realmente Cancelar essa Cessão de Direitos?")) {

   document.forms["cancel"].idCDBB.value=myIdCDBB;

   document.forms["cancel"].tipo.value=myTipo;

   window.open('<?php echo $root;?>role/cessao/distrato.php?idCDBB='+myIdCDBB+'&comm=gerapdf', 'pdf_window', 'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1,width=950,height=700');

   document.forms["cancel"].submit();

}

}

</script>

