<?php //Consultado HiCom mes 04

function getStatus($key) {
  $value='indefinido';
  switch ($key) {
  case 0: $value='Em cadastramento'; break;
  case 1: $value='Aguard. Aprova&ccedil;&atilde;o'; break;
  case 2: $value='V&aacute;lido'; break;
  case 3: $value='Cancelado'; break;
  case 4: $value='Em Cancelamento'; break;
  }
  return $value;
}
?>
<?php $query = "
     SELECT nameImp, countryName, limCredit, credit, idAgencia, nameAgencia, codAgencia, status, codigo, dateClient, nameBanco
     FROM (
       SELECT DISTINCT imp.name AS 'nameImp', c.name as countryName, imp.limCredit, imp.credit, 
              cdbb.idAgencia, ag.name AS 'nameAgencia', ag.codigo AS 'codAgencia', cdbb.status,
              cdbb.codigo AS 'codigo', cdbb.dateClient, 'Banco do Brasil' as nameBanco
       FROM Importer imp
         JOIN Country c ON (c.id = imp.idCountry)
         JOIN CDBBDetails cdd ON (cdd.idImporter = imp.id)
         Join CDBB cdbb ON (cdbb.id = cdd.idCDBB)
         Join Agencia ag ON (ag.id = cdbb.idAgencia)
       WHERE cdbb.idInform = $idInform
       UNION
       SELECT DISTINCT imp.name AS 'nameImp', c.name as countryName, imp.limCredit, imp.credit, 
              0 AS 'idAgencia', cdob.name AS 'nameAgencia', cdob.codigo AS 'codAgencia', cdob.status,
              cdob.codigo AS 'codigo', cdob.dateClient, 'Outros' as nameBanco
       FROM Importer imp
         JOIN Country c ON (c.id = imp.idCountry)
         JOIN CDOBDetails cdd ON (cdd.idImporter = imp.id)
         Join CDOB cdob ON (cdob.id = cdd.idCDOB)
       WHERE cdob.idInform = $idInform
       UNION
       SELECT DISTINCT imp.name AS 'nameImp', c.name as countryName, imp.limCredit, imp.credit, 
              cdp.idAgencia, ag.name AS 'nameAgencia', ag.codigo AS 'codAgencia', cdp.status,
              cdp.codigo AS 'codigo', cdp.dateClient, b.name as nameBanco
       FROM Importer imp
         JOIN Country c ON (c.id = imp.idCountry)
         JOIN CDParcDetails cdd ON (cdd.idImporter = imp.id)
         Join CDParc cdp ON (cdp.id = cdd.idCDParc)
         Join Agencia ag ON (ag.id = cdp.idAgencia)
         Join Banco b ON (ag.idBanco = b.id)
       WHERE cdp.idInform = $idInform


) as x
order by nameBanco, nameAgencia
";

//     SELECT nameImp, countryName, limiteCredito, credito, idAgencia, nameAgencia, codAgencia, status, codigo, dateClient, nameBanco
//     FROM (
//       SELECT DISTINCT imp.name AS 'nameImp', c.name as countryName, imp.limCredit, imp.credit, 
//              cdbb.idAgencia, ag.name AS 'nameAgencia', ag.codigo, cdbb.status,
//              cdbb.codigo AS 'codigo', cdbb.dateClient, 'Banco do Brasil' as nameBanco
//       FROM Importer imp
//         JOIN Country c ON (c.id = imp.idCountry)
//         JOIN CDBBDetails cdd ON (cdd.idImporter = imp.id)
//         Join CDBB cdbb ON (cdbb.id = cdd.idCDBB)
//         Join Agencia ag ON (ag.id = cdbb.idAgencia)
//       WHERE cdbb.idInform = $idInform
//       UNION
//       SELECT DISTINCT imp.name AS 'nameImp', c.name as countryName, imp.limCredit, imp.credit, 
//              0 AS 'idAgencia', cdob.name AS 'nameAgencia', cdob.codigo AS 'codAgencia', cdob.status,
//              cdob.codigo AS 'codigo', cdob.dateClient, 'Outros' as nameBanco
//       FROM Importer imp
//         JOIN Country c ON (c.id = imp.idCountry)
//         JOIN CDOBDetails cdd ON (cdd.idImporter = imp.id)
//         Join CDOB cdob ON (cdob.id = cdd.idCDOB)
//       WHERE cdob.idInform = $idInform
//       UNION
//       SELECT DISTINCT imp.name AS 'nameImp', c.name as countryName, imp.limCredit, imp.credit, 
//              cdp.idAgencia, ag.name AS 'nameAgencia', ag.codigo, cdp.status,
//              cdp.codigo AS 'codigo', cdp.dateClient, b.name as nameBanco
//       FROM Importer imp
//         JOIN Country c ON (c.id = imp.idCountry)
//         JOIN CDParcDetails cdd ON (cdd.idImporter = imp.id)
//         Join CDParc cdp ON (cdp.id = cdd.idCDParc)
//         Join Agencia ag ON (ag.id = cdp.idAgencia)
//         Join Banco b ON (ag.idBanco = b.id)
//       WHERE cdp.idInform = $idInform
//     ) as x
//     ORDER BY nameBanco, nameAgencia
//echo "<pre>$query</pre>";

      $dateEnv = odbc_result($cur, 10);
	  
      list($ano, $mes, $dia) = split ('-', $dateEnv);

?>
<TABLE border="0" cellSpacing=0 cellpadding="5" width="98%" align="center">
  <TR>
    <TD colspan="7" class="bgCinza" align="center">Lista de Importadores</TD>
  </TR>
   <TR>
    <TD colspan="7">&nbsp;</TD>
  </TR>
  <tr class="bgAzul">
    <td width="5%">&nbsp;</td>
    <td>Cód. Cessão</td>
    <td>Importador</td>
    <td>País</td>
    <td>Status</td>
    <td align="center">Crédito <br>Concedido (US$ Mil)</td>
    <td align="center">Agência</td>
  </tr>
<?php $cur=odbc_exec($db,$query);
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $idImporter = odbc_result($cur,'id');
    $status = odbc_result($cur, 8);
    $dateEnv = odbc_result($cur, 10);
    list($ano, $mes, $dia) = split ('-', $dateEnv);
?>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td width="5%"><?php echo $i;?></td>
    <td><?php echo odbc_result($cur,9)."/".$ano;?></td>
    <td><?php echo odbc_result($cur,1);?></td>
    <td><?php echo odbc_result($cur,2);?></td>
    <td><?php echo getStatus ($status);?></td>
    <td align="center"><?php echo number_format((odbc_result($cur,4)/1000));?></td>
    <td><?php echo odbc_result($cur,6);?> (<?php echo odbc_result($cur,7);?>)</td>
  </tr>
<?php } // while
  if ($i == 0) {
?>
  <TR class="bgCinza">
    <TD align="center" colspan=7 class="bgCinza">Nenhum Importador Cadastrado</TD>
  </TR>

<?php }
$total = $i;
?>
   <TR>
    <TD colspan="7">&nbsp;</TD>
  </TR>
<?php if ($role["client"]){ ?>
<form action="<?php echo $root;?>role/client/Client.php" method="post">
<input type="hidden" name="idInform" value="<?php echo $idInform;?>">
<input type="hidden" name="comm">
   <TR>
    <TD colspan="7" align="center">   <input class="servicos" type=button value="Voltar" onClick="this.form.comm.value='cessao';this.form.submit()">
&nbsp;</TD>
  </TR>
</form>
<?php }else{?>
<form action="<?php echo $root;?>role/searchClient/ListClient.php" method="post">
<input type="hidden" name="idInform" value="<?php echo $idInform;?>">
<input type="hidden" name="comm">
   <TR>
    <TD colspan="7" align="center">   <input class="servicos" type=button value="Voltar" onClick="this.form.comm.value='view';this.form.submit()">
&nbsp;</TD>
  </TR>
</form>
<?php }?>
</TABLE>

