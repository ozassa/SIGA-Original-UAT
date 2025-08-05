<TABLE border="0" cellSpacing=0 cellpadding="5" width="98%" align="center">
  <TR>
    <TD colspan="6" class="bgCinza" align="center">Cessão do Direito - Banco do Brasil</TD>
  </TR>
   <TR>
    <TD colspan="6">&nbsp;</TD>
  </TR>
  <TR class="bgAzul">
    <TD colspan="6" align="center" class="bgAzul">Lista de Importadores</TD>
  </TR>

  <!-- início de um importador -->
<?php $cur=odbc_exec($db,
	"SELECT Importer.name, Importer.address, Importer.risk,
	    Importer.city, Country.name, Importer.tel, Importer.prevExp12,
	    Importer.limCredit, Importer.numShip12, Importer.periodicity,
	    Importer.przPag, Importer.id, Importer.cep, Importer.fax, Importer.contact
	FROM Importer, Inform, Country
	WHERE Importer.idInform = $idInform AND Importer.state <> 7 AND Importer.state <> 8 AND
	    Importer.idInform = Inform.id AND
	    Importer.idCountry = Country.id AND
            Importer.state <> 1 AND Importer.state <> 3 AND 
            Importer.state <> 4  
	ORDER BY Importer.name");


  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $idImporter = odbc_result($cur,12);
    $aux = odbc_exec($db, "select credit from ChangeCredit where idImporter=$idImporter");
    if(odbc_fetch_row($aux)){
?>

  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td width="5%"><?php echo $i;?></td>
    <td><font color=#4169e1>Razão:</font></td><td><?php echo odbc_result($cur,1);?></td>
    <td><font color=#4169e1>País:</font></td><td><?php echo odbc_result($cur,5);?></td>
    <td width="5%"><a href="<?php echo $root;?>role/cessao/Cessao.php?comm=cessaoBB&idInform=<?php echo $idInform;?>&idImporter=<?php echo $idImporter;?>&codAg=<?php echo $agencia;?>&codBc=<?php echo $banco;?>">Selecionar</a></td>
  </tr>

<?php } // if

  } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=6 class="bgCinza">Nenhum Importador Cadastrado</TD>
  </TR>

<?php }
?>
   <TR>
    <TD colspan="6">&nbsp;</TD>
  </TR>

</TABLE>

<form action="<?php echo $root;?>role/cessao/Cessao.php" method="post">
<input type=hidden name="comm">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">

<p align="center"><INPUT class=servicos onclick="this.form.comm.value='cessaoBB';this.form.submit()" type=button value="Voltar">

</form>
