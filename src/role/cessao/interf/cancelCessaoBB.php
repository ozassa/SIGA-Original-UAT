<?php if($role["bancoBB"]){
  $query = "
       SELECT DISTINCT inf.name, inf.id
	FROM Inform inf
          JOIN UsersNurim us ON (us.idUser = $userID)
          JOIN Nurim nu ON (nu.id = us.idNurim)
          JOIN Agencia ag ON (ag.idNurim = nu.id)
          JOIN CDBB cd ON (cd.idAgencia = ag.id)
          JOIN Importer imp ON (imp.idInform = inf.id)
          JOIN CDBBDetails cdd ON (cdd.idCDBB = cd.id and cdd.idImporter = imp.id)
	WHERE inf.state in (10,11) AND cd.status in (2,4) And DATEDIFF(DAY, inf.endValidity, GETDATE()) <= 180
        ORDER BY inf.name
";
//And DATEDIFF(DAY, inf.endValidity, GETDATE()) <= 180 -- Alteração solicitado pelo Legey no dia 11/05/2006 - Alterador por Tiago V N - Elumini
}else if($role["bancoParc"]){
  $query = "
       SELECT DISTINCT inf.name, inf.id
	FROM Inform inf
          JOIN Banco bc ON (bc.idUser = $userID)
          JOIN CDParc cd ON (cd.idBanco = bc.id)
          JOIN Importer imp ON (imp.idInform = inf.id)
          JOIN CDParcDetails cdd ON (cdd.idCDParc = cd.id AND cdd.idImporter = imp.id)
	WHERE inf.state in (10,11) AND cd.status in (2,4) And DATEDIFF(DAY, inf.endValidity, GETDATE()) <= 180
";
}else{
  $query = "
       SELECT DISTINCT inf.name, inf.id
	FROM Inform inf
          JOIN Banco bc ON (bc.idUser = $userID)
          JOIN CDOB cd ON (cd.idBanco = bc.id)
          JOIN Importer imp ON (imp.idInform = inf.id)
          JOIN CDOBDetails cdd ON (cdd.idCDOB = cd.id and cdd.idImporter = imp.id)
	WHERE inf.state in (10,11) AND cd.status in (2,4) And DATEDIFF(DAY, inf.endValidity, GETDATE()) <= 180
        ORDER BY inf.name
";
}
//echo "<pre>$query</pre>";

?>
<?php require_once("../../../navegacao.php");?>
<div class="conteudopagina">
<table summary="Submitted table designs" id="example">
  <caption>Lista de Segurados</caption>
  <thead>
      <tr>
        <th>&nbsp;</Th>
        <th>Raz&atilde;o Social</th>
        <th>Declara&ccedil;&atilde;o de Regularidade</th>
      </tr>
  </thead>
  <tbody>
<?php $cur=odbc_exec($db,$query);
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $idInform = odbc_result($cur,2);
?>
  <tr>
    <td><?php echo $i;?></td>
    <td><A href="../cessao/Cessao.php?comm=cancel&idInform=<?php echo odbc_result($cur,2);?>&name=<?php echo odbc_result($cur, 1);?>"><?php echo odbc_result($cur,1);?></a></td>
    <td><a href="<?php echo $root;?>role/cessao/nadaconsta.php?idInform=<?php echo $idInform;?>" target=_blank>Imprimir</a></td>
  </tr>
<?php } // while
  if ($i == 0) {
?>
  <tr>
    <td colspan="3">Nenhum Segurado Cadastrado</td>
  </tr>

<?php }
?>
	</tbody>
	<div class="divisoria01"></div>
</table>
 <div style="clear:both">&nbsp;</div> 
</div>


