<?php 
$y = odbc_exec($db, "select Ga from Inform where id = '$idInform'");
$ga = odbc_result($y, "Ga");
$q = "SELECT name FROM Banco WHERE id = $idBanco";
$bc = odbc_exec ($db, $q);
$bancoName = odbc_result($bc, 1);
?>
<form action="<?php echo $root;?>role/cessao/Cessao.php#cessao" method="post">
<input type=hidden name="comm" value="gravaBB">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">
<input type=hidden name="agencia" value="<?php echo $agencia;?>">
<input type=hidden name="idAgencia" value="<?php echo $idAgencia;?>">
<input type=hidden name="idImporter" value="">
<a name=cessao></a>
<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD colspan="5" align="center"><H3>Cessão de Direito - <?php if($idBanco != 1){ echo $bancoName; }else{ echo "Banco do Brasil";}?></H3></TD>
  </TR><?php if($msgC){?>
  <TR>
    <TD colspan="5">&nbsp;</TD>
  </TR>
  <TR>
    <TD colspan="5" class="verm" align="center"><?php echo $msgC;?></TD>
  </TR>
<?php }?>
  <TR>
    <TD colspan="5">&nbsp;</TD>
  </TR>

<?php //
//// Buscar o idAgencia
//
//$query = "SELECT id FROM CDBB WHERE status = 0 AND idAgencia = $idAgencia AND idInform = $idInform";
////echo "<pre>$query</pre>";
//$cur = odbc_exec ($db, $query);
//if (odbc_fetch_row($cur)) {
//  $idCDBB = odbc_result ($cur,'id');
//} else {
//  $q = "INSERT INTO CDBB (idInform, idAgencia) VALUES ($idInform, $idAgencia)";
//  $cur = odbc_exec($db, $q);
//  $q = "SELECT max(id) FROM CDBB";
//  $cur = odbc_exec($db, $q);
//  $idCDBB = odbc_result($cur, 1);
//}

$q = "SELECT name FROM Agencia WHERE id = $idAgencia";
$ag = odbc_exec ($db, $q);
$agName = odbc_result($ag, 1);
?>
  <TR>
    <TD colspan="5">
          <?php echo $bancoName;?> <br>
        <?if(($tipoBanco == 1) || ($tipoBanco == 2)){?>
          Agência: <?php echo $agencia;?> - <?php echo $agName;?></TD>
        <?php }else{?>
          Agência: <?php echo $agencia;?></TD>
        <?php }?>
  </TR>
  <TR>
    <TD colspan="5">&nbsp;</TD>
  </TR>



  <TR>
    <TD colspan="5" align="center" class="textoBold">Lista de Importadores da Cessão de Direito</TD>
  </TR>

  <!-- início de um importador -->
  <tr class="bgAzul">
    <td class="bgAzul" width="5%">&nbsp;</td>
    <td class="bgAzul">Importador</td>
    <td class="bgAzul">País</td>
    <td class="bgAzul" align="center">Lim. de Crédito<br>(US$ Mil)</td>
  </tr>
<?php $link = $root . "role/cessao/cond_esp.php?consulta=1&idInform=$idInform&agencia=$agencia&idAgencia=$idAgencia&idBanco=$idBanco&tipoBanco=$tipoBanco&idCDBB=$idCDBB&idCDParc=$idCDParc&idCDOB=$idCDOB&total=$total&totalR=$totalR";
if($tipoBanco == 1){
  $query = "
        SELECT imp.name AS impName, c.name AS cName, imp.id, imp.credit
	FROM Importer imp
          JOIN Country c ON (imp.idCountry = c.id)
          JOIN CDBBDetails cdd ON (cdd.idImporter = imp.id)
	WHERE cdd.idCDBB = $idCDBB
	ORDER BY imp.name";
}else if($tipoBanco == 2){
  $query = "
        SELECT imp.name AS impName, c.name AS cName, imp.id, imp.credit
	FROM Importer imp
          JOIN Country c ON (imp.idCountry = c.id)
          JOIN CDParcDetails cdd ON (cdd.idImporter = imp.id)
	WHERE cdd.idCDParc = $idCDParc
	ORDER BY imp.name";
}else{
  $query = "
        SELECT imp.name AS impName, c.name AS cName, imp.id, imp.credit
	FROM Importer imp
          JOIN Country c ON (imp.idCountry = c.id)
          JOIN CDOBDetails cdd ON (cdd.idImporter = imp.id)
	WHERE cdd.idCDOB = $idCDOB
	ORDER BY imp.name";
}


  //####### ini ####### adicionado por eliel vieira - elumini - 13/05/2008
  //Lista de Importadores da Cessão de Direito
  //echo $query."<br>";


  $contR = 0;
  $cur=odbc_exec($db,$query);
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $contR++;
    $idImporter = odbc_result($cur,'id');
    $link .= "&idImporterR$i=$idImporter";
?>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td width="5%"><?php echo $i;?></td>
    <td class="texto"><?php echo odbc_result($cur,'impName');?></td>
    <td class="texto"><?php echo odbc_result($cur,'cName');?></td>
    <td class="texto" align="center"><?php echo odbc_result($cur,4)/1000;?></td>
  </tr>

<?php } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=5 class="bgCinza">Nenhum Importador Cadastrado</TD>
  </TR>

<?php }
$totalR = $i;
?>

</TABLE>
<p align="center"><INPUT class=servicos onclick="this.form.comm.value='cessao';this.form.submit()" type=button value="Voltar">
<?php if($status == 1){
   if ($tipoBanco == 1){
     if ($ga == 1 && $idInform != 5413 && $idInform != 3898 && $idInform != 5112 ) {
?>
         &nbsp;<INPUT class=servicos type=button value="Imprimir" onClick="javascript: verErro('Favor entrar em contato com o Departamento de Crédito.\n\nTelefones: 21 2510.5024 / 21 2510.5042.')">
<?php }else{
?>
         &nbsp;<INPUT class=servicos type=button value="Imprimir" onClick="imprime(this.form)">
<?php }
   }else{
?>
&nbsp;<INPUT class=servicos type=button value="Imprimir" onClick="imprime(this.form)">

<?php }
}
?>
</p>
</form>

<script language=javascript>
    function imprime(f) {
      //<?php echo $link;?>&comm=gerapdf
     // verErro('<?php echo $link;?>');
      w = window.open('<?php echo $link;?>&comm=gerapdf', 'pdf_windowoficial',
	  	      'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1');
      w.moveTo(5, 5);
      w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
    }
</script>
