<script>
	function gerarPdf(f){
		f.comm.value = "pdf";
		f.submit();
	}
</script>
<?php require_once("../../../navegacao.php");?>
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

  $cur=odbc_exec(
    $db,
    "SELECT * FROM Volume WHERE idInform = ".$field->getField("idInform")
  );
  if (odbc_fetch_row($cur)) {
    $field->setDB($cur);
?>
<div class="conteudopagina">

  <table> 
    <caption>QUADRO I -  Distribuição de Vendas por Tipo de Pagamento - <?php echo $ext;?> mil <label>Modalidade de Pagamento</label></caption>
      <thead>
          <tr>
            <th width="162">&nbsp;</th>
            <th>Pagamento Antecipado</th>
            <th>Venda Sujeita a Seguro</th>
            <th>Vendas para Companhias Associadas</th>
            <th>Totais</th>
          </tr>
      </thead>
      <tbody>
          <tr>
            <td width="162">Ano Corrente</td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol17", 18, $cur),2,",","."); ?>" name="vol17"></td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol18", 19, $cur),2,",","."); ?>" name="vol18"></td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol22", 23, $cur),2,",","."); ?>" name="vol22"></td>
            <td style="text-align:right">* &nbsp;<input disabled="disabled" style="border:none;text-align:right" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol24", 25, $cur),2,",","."); ?>" name="vol24" disabled="disabled"></td>
         </tr>
          <tr>
            <td width="162">Ano Passado</td>
            <td style="text-align:right"><input disabled="disabled"  style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol25", 26, $cur),2,",","."); ?>" name="vol25"></td>
            <td style="text-align:right"><input disabled="disabled"  style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol26", 27, $cur),2,",","."); ?>" name="vol26"></td>
            <td style="text-align:right"><input disabled="disabled"  style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol30", 31, $cur),2,",","."); ?>" name="vol30"></td>
            <td style="text-align:right">* &nbsp;<input disabled="disabled"  style="border:none;text-align:right" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol32", 33, $cur),2,",","."); ?>" name="vol32" disabled="disabled"></td>
          </tr>
          <tr>
            <td width="162">Ano Retrasado</td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol33", 34, $cur),2,",","."); ?>" name="vol33"></td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol34", 35, $cur),2,",","."); ?>" name="vol34"></td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onBlur="formatDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol38", 39, $cur),2,",","."); ?>" name="vol38"></td>
            <td style="text-align:right">* &nbsp;<input disabled="disabled" style="border:none;text-align:right" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol40", 41, $cur),2,",","."); ?>" name="vol40" disabled="disabled"></td>
          </tr>
          <tr>
            <td colspan="4">(*) N&uacute;mero de Meses</td>
            <!--<td style="text-align:right"><input disabled="disabled" style="border:none;text-align:right" onFocus="select()" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBField("vol41", 42, $cur),0,"",""); ?>" name="vol41" id="vol41" disabled="disabled"></td>-->
            <td style="text-align:right"><input disabled="disabled" style="border:none;text-align:right" onFocus="select()" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBField("vol41", 42, $cur),2,",","."); ?>" name="vol41" id="vol41" disabled="disabled"></td>
         </tr>
    </tbody>
    <tfoot>
         <tr>
            <td colspan="5">(*) Cobran&ccedil;a &agrave; vista, a prazo e carta de cr&eacute;dito n&atilde;o confirmada </td>
         </tr>
    </tfoot>
</table>
<div style="clear:both">&nbsp;</div>
<table>
      <caption>Proje&ccedil;&atilde;o de Vendas
     <!-- <h5><strong>Instru&ccedil;&otilde;es:</strong>&nbsp;Considere dados do &uacute;ltimo ano fechado</h5><caption>-->
    <thead>
          <tr>
            <th>&nbsp;</th>
            <th>Pagamento Antecipado </th>
            <th>Venda Sujeita a Seguro</th>
            <th>Vendas para Companhias Associadas</th>
            <th>Totais</th>
          </tr>
    </thead>
    <tbody>
        <tr>
            <td>Previs&atilde;o Pr&oacute;ximos 12 Meses</td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onFocus="select()" onBlur="checkDecimals(this, this.value);calc(this.form)" type="text" value="<?php echo number_format($field->getDBNumField("vol1", 2, $cur),2,",","."); ?>" class="semformatacao" size="20" name="vol1"></td>
            <td style="text-align:right">* &nbsp;<input disabled="disabled" style="text-align:right" onFocus="select()" onBlur="checkDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol2", 3, $cur),2,",","."); ?>" name="vol2"></td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onFocus="select()" onBlur="checkDecimals(this, this.value);calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol6", 7, $cur),2,",","."); ?>" name="vol6"></td>
            <td style="text-align:right"><input disabled="disabled" style="border:none;text-align:right" onFocus="select()" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBNumField("vol8", 9, $cur),2,",",".") ?>" name="vol8" disabled="disabled"></td>
      </tr>
        <tr>
            <td>N&ordm; de Compradores </td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onFocus="select()" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBField("vol9", 10, $cur),0,".",""); ?>" name="vol9" id="vol9"></td>
            <td style="text-align:right">* &nbsp;<input disabled="disabled" style="text-align:right" onFocus="select()" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBField("vol10", 11, $cur),0,".",""); ?>" name="vol10" id="vol10"></td>
            <td style="text-align:right"><input disabled="disabled" style="text-align:right" onFocus="select()" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBField("vol14", 15, $cur),0,".",""); ?>" name="vol14" id="vol14"></td>
            <td style="text-align:right"><input disabled="disabled" style="border:none;text-align:right" onFocus="select()" onBlur="calc(this.form)" type="text" class="semformatacao" size="20" value="<?php echo number_format($field->getDBField("vol16", 17, $cur),0,".","") ?>" name="vol16" id="vol16" disabled="disabled"></td>
       </tr>
  </tbody>
</table>


<?php  if ($msg != "") {?><p><?php echo $msg; ?></p><?php  } ?>


<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
<input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
<input type="hidden" name="reltipo" value="informI">
<input type="hidden" name="comm" value="segVendExt">
<div class="barrabotoes">
	<button name="voltar_bt" onclick="javascript: this.form.comm.value='open';this.form.submit();" class="botaovgg">Voltar</button>
    <!-- <button type="submit" name="ok_bt" class="botaoagm">OK</button>
    <button name="pdf" class="botaoagm" onclick="gerarPdf(this.form);">Vers&atilde;o PDF</button> -->
</div>


</form>


<?php 
  } else {
?>
<font color=red><p>Informe inválido</p></font>
<?php 
  }
?>
</div>


