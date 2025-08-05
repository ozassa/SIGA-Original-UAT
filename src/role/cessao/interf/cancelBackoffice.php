<?php

if($tipo == "BB"){
  $query = "
     SELECT cdbb.codigo, ag.codigo, cdbb.status, ag.name, cdbb.dateClient, 'Banco do Brasil S/A' AS 'name'
     FROM CDBB cdbb
       Join Agencia ag   ON (ag.id = cdbb.idAgencia)
     WHERE cdbb.idInform = $idInform AND cdbb.id = $idCDBB
  ";
}else if($tipo == "OB"){
  $query = "
     SELECT DISTINCT cdob.codigo, cdob.agencia, cdob.status, cdob.name, cdob.dateClient, bc.name
     FROM CDOB cdob
       Join Banco bc   ON (bc.id = cdob.idBanco)
       Join CDOBDetails cdd ON (cdd.idCDOB = cdob.id)
     WHERE cdob.idInform = $idInform AND cdob.id = $idCDBB
  ";
}else{
  $query = "
     SELECT cdp.codigo, ag.codigo, cdp.status, ag.name, cdp.dateClient, bc.name
     FROM CDParc cdp
       Join Banco bc   ON (bc.id = cdp.idBanco)
       Join Agencia ag   ON (ag.id = cdp.idAgencia)
     WHERE cdp.idInform = $idInform AND cdp.id = $idCDBB
  ";
}
  $cur=odbc_exec($db,$query);
  $dateEnv = odbc_result($cur, 5);
  list($ano, $mes, $dia) = split ('-', $dateEnv);
  $codigo = odbc_result($cur,1);
  $codigo = $codigo."/".$ano;
?>
<TABLE border="0" cellSpacing=0 cellpadding="3" width="100%" align="center">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align=center width="100%" colspan="2"><?php echo  $name;?></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">Código da Cessão: <?php echo  $codigo;?></td>
  </tr>
  <tr>
    <td colspan="2">Banco: <?php  echo odbc_result($cur,6);?></td>
  </tr>
  <tr>
    <td colspan="2">Agência: (<?php  echo odbc_result($cur,2);?>) <?php  echo odbc_result($cur,4);?></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr class="bgAzul">
    <td align="center">Importador</td>
    <td width="20%">País</td>
  </tr>
<?php  if($tipo == "BB"){
    $query = "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
	FROM Importer imp
          JOIN Country c ON (imp.idCountry = c.id)
          JOIN CDBBDetails cd ON (imp.id = cd.idImporter)
          JOIN CDBB cb ON (cd.idCDBB = cb.id)
	WHERE imp.idInform = $idInform
          AND cb.status <> 3
          AND cb.id = $idCDBB
	ORDER BY imp.name";
}else if($tipo == "OB"){
    $query = "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
	FROM Importer imp
          JOIN Country c ON (imp.idCountry = c.id)
          JOIN CDOBDetails cd ON (imp.id = cd.idImporter)
          JOIN CDOB cb ON (cd.idCDOB = cb.id)
	WHERE imp.idInform = $idInform
          AND cb.status <> 3
          AND cb.id = $idCDBB
	ORDER BY imp.name";
}else{
    $query = "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
	FROM Importer imp
          JOIN Country c ON (imp.idCountry = c.id)
          JOIN CDParcDetails cd ON (imp.id = cd.idImporter)
          JOIN CDParc cb ON (cd.idCDParc = cb.id)
	WHERE imp.idInform = $idInform
          AND cb.status <> 3
          AND cb.id = $idCDBB
	ORDER BY imp.name";
}
    $cur = odbc_exec($db, $query);
    $i = 0;
    while (odbc_fetch_row($cur)) {
      $i++;
      $dateEnv = odbc_result($cur, 5);
      list($ano, $mes, $dia) = split ('-', $dateEnv);
?>
  <tr <?php  echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td><?php  echo odbc_result($cur,1);?></td>
    <td><?php  echo odbc_result($cur,2);?></td>
 </tr>
<?php  } // while
  if ($i == 0) {
?>
  <TR class="bgCinza">
    <TD align="center" colspan=2 class="bgCinza">Nenhuma Cessão Cadastrada</TD>
  </TR>

<?php  }
$total = $i;
?>


<form action="<?php echo  $root;?>role/cessao/Cessao.php" method="post">
<input type="hidden" name="comm">
<input type=hidden name="idCDBB" value="<?php echo  $idCDBB;?>">
<input type=hidden name="tipo" value="<?php echo  $tipo;?>">
<input type=hidden name="idNotification" value="<?php echo  $idNotification;?>">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
   <TR>
    <TD colspan="2" align="center">
<INPUT class=servicos onclick="javascript:window.history.back()" type=button value="Voltar"> 
<input class="servicos" type=button value="Cancelar" onClick="this.form.comm.value='cancelBackoffice';this.form.submit()">
<input class="servicos" type=button value="Desconsiderar" onClick="this.form.comm.value='desconsiderar';this.form.submit()">
&nbsp;</TD>
</form> </TR>

</table>