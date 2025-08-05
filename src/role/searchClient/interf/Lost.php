<script>
  function gerarPdf(f){
    f.comm.value = "pdf_inter";
    f.conteudo_impressao.value = document.getElementById("to_print").innerHTML;
    
    f.submit();
  }
</script>
<style>
  .titulo_impressao {display: none}
</style>
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
    "SELECT * FROM Lost WHERE idInform = $idInform"
  );
  if (odbc_fetch_row($cur)) {
    $field->setDB ($cur);
?>
<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
  <div id="to_print">
    <h1 class="titulo_impressao">Histórico de Perdas</h1>
    <table>
    <caption>Quadro IV - Hist&oacute;rico de Perdas</caption>
    
    <thead>
        <tr>
          <th>&nbsp;</th>
          <th>Ano Corrente</th>
          <th>Ano Passado</th>
          <th>Ano Retrasado</th>
        </tr> 
    </thead>       
    <tbody>
    <tr>         
      <td>Soma Total das Perdas - <?php echo $ext;?> mil</td>
      <td><?php echo number_format($field->getDBField("val1", 2, $cur),2,",","."); ?></td>
      <td><?php echo number_format($field->getDBField("val2", 3, $cur),2,",","."); ?></td>
      <td><?php echo number_format($field->getDBField("val3", 4, $cur),2,",","."); ?></td>
    </tr>        

    <tr>         
      <td>N&uacute;mero de Perdas</td>
      <td><?php echo number_format($field->getDBField("num1", 5, $cur),0,",","."); ?></td>
      <td><?php echo number_format($field->getDBField("num2", 6, $cur),0,",","."); ?></td>
      <td><?php echo number_format($field->getDBField("num3", 7, $cur),0,",","."); ?></td>
    </tr
    ></tbody>
  </table>

  <table>
    <caption>Quadro V - Detalhes das tr&ecirc;s maiores perdas durante os &uacute;ltimos tr&ecirc;s anos</caption>
    <thead>
    <tr>
      <th>Ano</th>
      <th>Pa&iacute;s</th>
      <th>Raz&atilde;o Social do Inadimplente</th>
      <th>Valor Final da Perda - <?php echo $ext;?></th>
    </tr>     
    </thead> 
    <tbody> 
    <tr>         
      <TD rowspan="3">Corrente</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("country1", 8, $cur); ?>&nbsp;</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name1", 17, $cur); ?>&nbsp;</TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost1", 26, $cur),2,",","."); ?></TD>
    </TR>         
    <TR class="bgCinza">          
      <TD align="center" class="texto"><?php echo $field->getDBField("country2", 9, $cur); ?>&nbsp;</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name2", 18, $cur); ?>&nbsp;</TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost2", 27, $cur),2,",","."); ?></TD>
    </TR>         
    <TR class="bgCinza">          
      <TD align="center" class="texto"><?php echo $field->getDBField("country3", 10, $cur); ?>&nbsp;</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name3", 19, $cur); ?>&nbsp;</TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost3", 28, $cur),2,",","."); ?></TD>
    </TR>        
    <TR>         
      <TD rowspan="3">Passado</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("country4", 11, $cur); ?></TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name4", 20, $cur); ?></TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost4", 29, $cur),2,",","."); ?></TD>
    </TR>
    <TR>          
      <TD align="center" class="texto"><?php echo $field->getDBField("country5", 12, $cur); ?></TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name5", 21, $cur); ?></TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost5", 30, $cur),2,",","."); ?></TD>
    </TR>         
    <TR>          
      <TD align="center" class="texto"><?php echo $field->getDBField("country6", 13, $cur); ?></TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name6", 22, $cur); ?></TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost6", 31, $cur),2,",","."); ?></TD>
    </TR>        
    <TR class="bgCinza">         
      <TD rowspan="3">Retrasado</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("country7", 14, $cur); ?>&nbsp;</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name7", 23, $cur); ?>&nbsp;</TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost7", 32, $cur),2,",","."); ?></TD>
    </TR>
    <TR class="bgCinza">          
      <TD align="center" class="texto"><?php echo $field->getDBField("country8", 15, $cur); ?>&nbsp;</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name8", 24, $cur); ?>&nbsp;</TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost8", 33, $cur),2,",","."); ?></TD>
    </TR>         
    <TR class="bgCinza">          
      <TD align="center" class="texto"><?php echo $field->getDBField("country9", 16, $cur); ?>&nbsp;</TD>
      <TD align="center" class="texto"><?php echo $field->getDBField("name9", 25, $cur); ?>&nbsp;</TD>
      <TD align="right" class="texto"><?php echo number_format($field->getDBField("lost9", 34, $cur),2,",","."); ?></TD>
    </TR>        
    </tbody>
  </table>
  <P>Observa&ccedil;&otilde;es relevantes sobre os importadores inadimplentes:</P>
  <P><?php echo ($field->getDBField("obs", 35, $cur)); ?></P>
</div>


<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
<input type=hidden name=idInform value="<?php echo $field->getField("idInform"); ?>">
<textarea name="conteudo_impressao" id="conteudo_impressao" style="display:none"></textarea>

<input type=hidden name=comm value="simul">
<div class="barrabotoes">
	<button name="voltar_bt" onClick="this.form.comm.value='open';this.form.submit()" class="botaovgg">Voltar</button>
  <button name="pdf" class="botaoagm" onclick="gerarPdf(this.form);">Vers&atilde;o PDF</button>
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

