<script>
	function gerarPdf(f){
		f.comm.value = "pdf";
		f.submit();
	}
</script>
<?php 
  //Alterado por Tiago V N - Elumini - 10/04/2006
  $cur=odbc_exec(
    $db,
    "SELECT * FROM Inform WHERE id = ".$field->getField("idInform")
  );

  if (odbc_result($cur, 'currency') == 2) {
     $ext = "US$";
  }else if (odbc_result($cur, 'currency') == 6) {
     $ext = "€";
  }
?>
<?php require_once("../../../navegacao.php");?>
<div class="conteudopagina">
<table id="example">
  <caption>QUADRO II  Valores por Pa&iacute;s a Serem Segurados. A Soma de Exportadores por Pa&iacute;s deve ser Igual ao Declarado no Quadro I</caption>
  
      <thead>
          <tr>
            <th>&nbsp;</th>
            <th>Valor a ser Exportado - <?php echo $ext;?></th>
            <th>Pa&iacute;s</th>
            <th>N.&ordm; de Compradores</th>
          </tr>
      </thead>
      <tbody>
	<?php 
    
      $cur=odbc_exec(
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
            <td><?php echo number_format(odbc_result($cur,2),2,",","."); ?></td>
            <td><?php echo (odbc_result($cur,3)); ?></td>
            <td><?php echo odbc_result($cur,4); ?></td>
          </tr>
	<?php 
      }
    ?> 
    </tbody>
    <tfoot>
    <tr>
        <td>Total</td>
        <td><?php echo number_format($total,2,",","."); ?></td>
        <td>&nbsp;</td>
        <td><?php echo $totComp; ?></td>
    </tr>
    </tfoot>
</table>

<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="get">
  <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
  <input type="hidden" name="reltipo" value="informII">
  <input type="hidden" name="comm" value="buyers">

  <div class="barrabotoes">
  	<button onclick="javascript: this.form.comm.value='open';this.form.submit();" class="botaovgg">Voltar</button>
  </div>

</form>
</div>




