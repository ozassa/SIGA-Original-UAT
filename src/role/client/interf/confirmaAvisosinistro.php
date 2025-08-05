<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD colspan="4" class="bgCinza" align="center">Aviso de Sinistro - Lista de Importadores</TD>
  </TR>
   <TR>
    <TD colspan="4">&nbsp;</TD>
  </TR>
<?php  $var=odbc_exec($db,
	"SELECT Importer.name, Importer.address, Importer.tel, Importer.fax, Importer.contact, Importer.endosso, Country.name
	FROM Importer, Country
	WHERE Importer.id = $idImporter AND Importer.idCountry = Country.id");
?>
  <TR class=bgAzul>
    <td align=center colspan=4>Dados do Importador</TD>
  </TR>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
  <TR>
    <td>Nome:</td>
    <td colspan=3 class="texto"><?php  echo odbc_result($var,1);?></td>
  </TR>
  <TR>
    <td width="25%">País:</td>
    <td width="25%" class="texto"><?php  echo odbc_result($var,7);?></td>
    <td width="25%">Ci Importador:</td>
    <td width="25%" class="texto">&nbsp; </td>
  </TR>
  <TR>
    <td>Endereço:</td>
    <td colspan=3 class="texto"><?php  echo odbc_result($var,2);?></td>
  </TR>
  <TR>
    <td>Tel.:</td>
    <td class="texto"><?php  echo odbc_result($var,3);?></td>
    <td>Fax:</td>
    <td class="texto"><?php  echo odbc_result($var,4);?></td>
  </TR>
  <TR>
    <td>Contato na Empresa:</td>
    <td colspan=3 class="texto"><?php  echo odbc_result($var,5);?></td>
  </TR>
  <TR>
    <td>Tipo de Cessão:</td>
    <td colspan=3 class="texto"><?php  if (odbc_result($var,6) == 1){ echo "Banco do Brasil"; }?></td>
  </TR>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=4>Faturas declaradas na DVE</td>
  </TR>
  <!-- início da DVE -->
  <TR>
    <td class=bgAzul align=center WIDTH="10%">Nº Fatura</td>
    <td class=bgAzul align=center>Data de Embarque</td>
    <td class=bgAzul align=center>Data de Vencimento</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
  </TR>
<?php  $cur=odbc_exec($db,
	"SELECT d.inicio, d.periodo,
             dd.embDate, dd.vencDate, dd.fatura, dd.totalEmbarcado, d.id
	FROM DVE d JOIN DVEDetails dd ON (dd.idDVE = d.id)
	WHERE d.idInform = $idInform AND dd.idImporter = $idImporter");
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $dateEmb = odbc_result($cur,3); 
    $dateVenc = odbc_result($cur,4);
    $valor = odbc_result($cur, 6);
    $numFat = odbc_result($cur,5);
    $idDVE = odbc_result($cur,7);

    $query = "SELECT valuePag, valueAbt FROM Sinistro WHERE idDVE = $idDVE";
    $sol = odbc_exec($db,$query);
    if (!odbc_fetch_row($sol)) {
       $aparece = 1;
?>
  <TR <?php  echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"")?>>
    <td align=center><?php  echo $numFat;?></td>
    <td align=center><?php  echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,0,4);?></td>
    <td align=center><?php  echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,0,4);?></td>
    <td align=center><?php  echo number_format($valor,2,",",".");?></td>
  </TR>

<?php  } // if
  } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=4 class="bgCinza">Nenhuma DVE Cadastrada</TD>
  </TR>

<?php  }
?>

  <TR>
    <td colspan=4><br><br><br><br><br>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=4 align="center">
<FORM action="<?php  echo $root;?>role/client/Client.php" method="post">
<input type=hidden name="comm" value="geraravisosinistro">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="idImporter" value="<?php  echo $idImporter;?>">
<INPUT class=servicos onclick="this.form.comm.value='open';this.form.submit()" type=button value="Voltar">
<input type="submit" value="Deseja Gerar Aviso de Sinistro para este Importador?" class="servicos">
</form>
    </td>
  </TR>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>

</TABLE>
