<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
<?php  //Alterador por Tiago V N - Elumini - 05/06/2006
  $query = "select * from Inform where id = $idInform";
  $cur = odbc_exec($db, $query);
  $nMoeda = odbc_result($cur, "currency");

  if ($nMoeda == "2") {
     $extMoeda = "US$";
  }else{
     $extMoeda = "€";
  }

  $cur=odbc_exec(
    $db,
    "SELECT * FROM Volume WHERE idInform = ".$field->getField("idInform")
  );
  if (odbc_fetch_row($cur)) {
    $field->setDB($cur);
?>
<h3>QUADRO I - Distribuição de Vendas por Tipo de Pagamento - <?php   echo $extMoeda;?></h3>
<table>
	<caption>Modalidade de Pagamento</caption>
  	<thead>
    <tr>
        <th>&nbsp;</th>
        <th>Pagamento Antecipado</th>
        <th>Exporta&ccedil;&atilde;o Sujeita &agrave; Seguro (**)</th>
        <th>Vendas para Companhias Associadas</th>
        <th>Totais</th>
    </tr>
    </thead>
	<tbody>
	<tr>
        <td>Previs&atilde;o Pr&oacute;ximos 12 Meses</td>
        <td><?php   echo number_format($field->getDBField("vol1", 2, $cur),2,",",".");?></td>
        <td><?php   echo number_format($field->getDBField("vol2", 3, $cur),2,",",".");?></td>
        <td><?php   echo number_format($field->getDBField("vol6", 7, $cur),2,",",".");?></td>
        <td><?php   echo number_format($field->getDBField("vol8", 9, $cur),2,",",".") ?></td>
     </tr>
  	 <tr>
        <td>N&ordm; de Compradores</td>
        <td><?php   echo number_format($field->getDBField("vol9", 10, $cur),0,",",".");?></td>
        <td><?php   echo number_format($field->getDBField("vol10", 11, $cur),0,",",".") ?></td>
        <td><?php   echo number_format($field->getDBField("vol14", 15, $cur),0,",",".");?></td>
        <td><?php   echo number_format($field->getDBField("vol16", 17, $cur),0,",",".");?></td>
     </tr>
     </tbody>
     </table>
     <div class="divisoria01"></div>
     <table>
     <caption>Exporta&ccedil;&otilde;es Realizadas</caption>
  	 <tbody>
      	<tr>
            <td> Ano Corrente (*)</td>
            <td><?php   echo number_format($field->getDBField("vol17", 18, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol18", 19, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol22", 23, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol24", 25, $cur),2,",",".");?></td>
        </tr>
        <tr>
            <td>Ano Passado</td>
            <td><?php   echo number_format($field->getDBField("vol25", 26, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol26", 27, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol30", 31, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol32", 33, $cur),2,",",".");?></td>
        </tr>
        <tr>
            <td> Ano Retrasado</td>
            <td><?php   echo number_format($field->getDBField("vol33", 34, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol34", 35, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol38", 39, $cur),2,",",".");?></td>
            <td><?php   echo number_format($field->getDBField("vol40", 41, $cur),2,",",".");?></td>
        </tr>
       <tr>
            <td colspan="4">&nbsp;</td>
            <td><?php   echo number_format($field->getDBField("vol41", 42, $cur),0,"","");?></td>
       </tr>
       </tbody>
</table>

<div class="divisoria01"></div>
<p>(*) N&uacute;mero de Meses</p>
<p>(**) Cobran&ccedil;a &agrave; vista, &agrave; prazo e carta de cr&eacute;dito n&atilde;o confirmada</p>
<?php  if ($msg != "") {?><p><?php   echo $msg;?></p><?php  } ?>


<form action="<?php   echo $root ?>role/executive/Executive.php" method="post" name="">
<div class="barrabotoes">
<input type="hidden" name="idInform" value="<?php   echo $field->getField("idInform");?>">
<input type="hidden" name="idNotification" value="<?php   echo $field->getField("idNotification");?>">
<input type="hidden" name="comm" value="volSubmit">

<button name="inicial"  onClick="this.form.comm.value='open'; this.form.submit()" class="botaoagg">Tela Inicial</button>
<button name="anterior" onClick="this.form.comm.value='back';this.form.submit()" class="botaovgg">Tela Anterior</button>
<button name="proxima" type="submit" class="botaoagg">Pr&oacute;xima Tela</button>
</div>
</form>


<?php  } else {
?>
<p>Informe inválido</p>

<?php  }?>
</div>

