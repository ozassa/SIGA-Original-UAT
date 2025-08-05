<?php  //Alterador por Tiago V N - Elumini - 05/06/2006
  $query = "select * from Inform where id = $idInform";
  $cur = odbc_exec($db, $query);
  $nMoeda = odbc_result($cur, "currency");

  if ($nMoeda == "2") {
     $extMoeda = "US$";
  }else{
     $extMoeda = "€";
  }
  
?>
<?php include_once('../../../navegacao.php'); ?>
<div class="conteudopagina">
<form action="<?php   echo $root;?>role/executive/Executive.php" method="post" name="">
<input type="hidden" name="comm" value="segSubmit">
<input type="hidden" name="idInform" value="<?php   echo $field->getField("idInform");?>">
<input type="hidden" name="idNotification" value="<?php   echo $idNotification;?>">
<table>
	<caption>QUADRO II  Valores por Pa&iacute;s a Serem Segurados. A Soma de Exportadores por Pa&iacute;s deve ser Igual ao Declarado no Quadro I</caption>
	<thead>
      <tr>
            <th>&nbsp;</th>
            <th>Valor a ser Exportado - <?php   echo $extMoeda;?></th>
            <th>Pa&iacute;s</th>
            <th>N&ordm; de compradores</th>
      </tr>
     </thead>
     <tbody>
		<?php  $cur=odbc_exec(
            $db,
            "SELECT vol2 + vol3 + vol4 FROM Volume WHERE idInform = ".$field->getField("idInform")
          );
          $abc = 0;
          if (odbc_fetch_row($cur))
            $abc = odbc_result($cur,1);
          $cur=odbc_exec(
            $db,
            "SELECT cat, valExp, name, buyers, VolumeSeg.id, expMax FROM VolumeSeg JOIN Country ON (idCountry = Country.id) WHERE idInform = ".$field->getField("idInform") 
          );
          $total = 0;
          $totComp = 0;
          $totalExp = 0;
          $i = 0;
          while (odbc_fetch_row($cur)) {
            $total += odbc_result($cur,2);
            $totComp += odbc_result($cur,4);
            $totalExp += odbc_result($cur,6);
            $i ++;
        ?>
      <tr>
        <td>&nbsp;</td>
        <td><?php   echo number_format(odbc_result($cur,2),2,",",".");?></td>
        <td><?php   echo odbc_result($cur,3);?></td>
        <td><?php   echo odbc_result($cur,4);?></td>
      </TR>
	<?php  }
      if ($i == 0) {
    ?>
      <tr>
        <td colspan="4">Nenhuma entrada cadastrada</td>
      </tr>
    <?php  } else {?>
      <tr>
        <td>Total</td>
        <td><?php   echo number_format($total,2,",",".");?></td>
        <td>&nbsp;</td>
        <td>&nbsp;<?php   echo $totComp;?></td>
      </tr>
	<?php  }?>
	</tbody>
</table>
<div class="barrabotoes">
<button name="inicial"  onClick="this.form.comm.value='open';this.form.submit()" class="botaoagg">Tela Inicial</button>
<button name="anterior" onClick="this.form.comm.value='voltar';this.form.submit()" class="botaovgg">Tela Anterior</button>
<button name="proxima"  type="submit" class="botaoagg">Pr&oacute;xima Tela</button>
</div>
</form>
</div>