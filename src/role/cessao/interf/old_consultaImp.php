<?php if(! function_exists('getStrDate')){
  function getStrDate($str){
    $row = explode('-', $str);
    $ret = $row[2]. "/". $row[1] ."/". $row[0];
    if ($ret == '//')
      return '';
    return $ret;
  }
}

if($user -> hasRole("bancoBB")){
  $query = "
       SELECT imp.name, c.name as countryName, credit, credit, imp.id
       FROM Importer imp
         JOIN Country c ON (c.id = imp.idCountry)
         JOIN CDBBDetails cdd ON (cdd.idImporter = imp.id)
         Join CDBB cdbb ON (cdbb.id = cdd.idCDBB)
       WHERE cdbb.idInform = $idInform AND cdbb.status in (2,4) AND cdbb.codigo = $codigo
       ORDER BY imp.name
";
}else if($user -> hasRole("bancoParc")){
  $query = "
       SELECT imp.name, c.name as countryName, credit, credit,  imp.id
       FROM Importer imp
         JOIN Country c ON (c.id = imp.idCountry)
         JOIN CDParcDetails cdd ON (cdd.idImporter = imp.id)
         Join CDParc cdp ON (cdp.id = cdd.idCDParc)
       WHERE cdp.idInform = $idInform AND cdp.status in (2,4) AND cdp.codigo = $codigo
       ORDER BY imp.name
";
}else{
  $query = "
       SELECT imp.name, c.name as countryName, credit, credit,  imp.id
       FROM Importer imp
         JOIN Country c ON (c.id = imp.idCountry)
         JOIN CDOBDetails cdd ON (cdd.idImporter = imp.id)
         Join CDOB cdob ON (cdob.id = cdd.idCDOB)
       WHERE cdob.idInform = $idInform AND cdob.status in (2,4) AND cdob.codigo = $codigo
       ORDER BY imp.name
";
}
//echo "<pre>$query</pre>";

?>
<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD colspan="5" class="bgCinza" align="center">Lista de Importadores</TD>
  </TR>
   <TR>
    <TD colspan="5">&nbsp;</TD>
  </TR>
  <tr class="bgAzul">
    <td width="5%" align="center">&nbsp;</td>
    <td width="35%">Importador</td>
    <td width="30%">País</td>
    <td width="20%" align="center">Crédito <br>Concedido (US$ Mil)</td>
	<td width="20%" align="center">Crédito <br>Temporário (US$ Mil)</td>
    <!--td width="10%" align="center">Aviso de Sinistro</td-->  <!--comentei o link de aviso de sinistro-->
  </tr>
<?php $cur=odbc_exec($db,$query);


  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $idImporter = odbc_result($cur,'id');
	
	//incluido HiCom....

      // busca dados de crédito
	  
	  
	  $wsql = "select creditTemp, limTemp, credit, getdate() as hoje from ChangeCredit where id=(select max(id) from ChangeCredit where idImporter= $idImporter) ";
	  
	  //echo  $wsql; 
	  
	  $y = odbc_exec($db, $wsql);
	  
	  $hc_creditTemp_imp = odbc_result($y, 1);
	  $hc_limTemp_imp = odbc_result($y, 2);
	  $hc_credit_imp = odbc_result($y, 3);
	  
	  if ($hc_limTemp_imp) 
	  {
	     
		 $hc_creditTemp_imp = number_format($hc_creditTemp_imp/1000, 0, ",", ".") . "<br>até:&nbsp;" . getStrDate(substr($hc_limTemp_imp, 0, 10));
		 
		 //if(getTimeStamp(getStrDate(substr($hc_limTemp_imp, 0, 10))) >= time())
		 //{
		 
		 //}
		 //else
		 //{
		 
		 //}
	  }
	  else
	  {
	     $hc_creditTemp_imp = number_format(0, 0, ",", ".");
	  }
	  
	  
	  
      // fim busca dados de crédito	  
	
	
	
	
    
	//Fim incluido HiCom
	
	
	// Não estava funcionando.... esse indice 9!!!  Retirei!
	//$dateEnv = odbc_result($cur, 9);
    //list($ano, $mes, $dia) = split ('-', $dateEnv);
    $qu = "SELECT id FROM Sinistro WHERE idImporter = $idImporter and (status >= 2 and status <> 7 and status <> 6)";
    $sin = odbc_exec($db, $qu);
?>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td width="5%"><?php echo $i;?></td>
    <td><?php echo odbc_result($cur,1);?></td>
    <td><?php echo odbc_result($cur,2);?></td>
    <td align="center"><?php echo odbc_result($cur,4)/1000;?></td>
	<td align="center"><?php echo $hc_creditTemp_imp;?></td>
<?php if(odbc_fetch_row($sin)){?>
    <td align="center">-</td>
<?php }else{?>
    <!--td align="center"><A HREF=<?php echo $root;?>role/client/Client.php?comm=geraravisosinistro&idInform=<?php echo $idInform;?>&idImporter=<?php echo $idImporter;?>&codigo=<?php echo $codigo;?>&sol=banco>criar</a></td-->   <!--comentei o link de aviso de sinistro -->
<?php }?>
  </tr>
<?php } // while
  if ($i == 0) {
?>
  <TR class="bgCinza">
    <TD align="center" colspan=5 class="bgCinza">Nenhum Importador Cadastrado</TD>
  </TR>

<?php }
$total = $i;
?>
   <TR>
    <TD colspan="5">&nbsp;</TD>
  </TR>
<form action="<?php echo $root;?>role/cessao/Cessao.php" method="post">
<input type="hidden" name="comm">
   <TR>
    <TD colspan="5" align="center">   <input class="servicos" type=button value="Voltar" onClick="this.form.comm.value='consultaCessao';this.form.submit()">
&nbsp;</TD>
  </TR>
   <TR>
<?php $link = $root . "role/cessao/nadaconsta.php?idInform=$idInform&total=$total";
?>
    <TD colspan="5"><a href="<?php echo $link;?>" target=_blank>Declaração de Regularidade</a></TD>
  </TR>
</form>
</TABLE>
