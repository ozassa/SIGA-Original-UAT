<?php 
 
 include_once('../../../navegacao.php');

?>

<div class="conteudopagina">
<?php  //Alterador por Tiago V N - Elumini - 05/06/2006
  $query = "select * from Inform where id = $idInform";
  $cur = odbc_exec($db, $query);
  $nMoeda = odbc_result($cur, "currency");

  if ($nMoeda =="2") {
     $extMoeda = "US$";
  }else{
     $extMoeda = "€";
  }
  
  $cur=odbc_exec(
    $db,
    "SELECT * FROM Lost WHERE idInform = $idInform"
  );
  if (odbc_fetch_row($cur)) {
    $field->setDB ($cur);
?>
<form action="<?php   echo $root;?>role/executive/Executive.php" method="post" name="">
<input type="hidden" name="comm" value="lostSubmit">
<input type="hidden" name="idInform" value="<?php   echo $idInform;?>">
<input type="hidden" name="idNotification" value="<?php   echo $idNotification;?>">
<table>
	<caption>Quadro V - Hist&oacute;rico de Perdas</caption>
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
            <td>Soma Total das Perdas - <?php   echo $extMoeda;?> mil</td>

            <td><?php   echo number_format($field->getDBField("val1", 2, $cur),2,",",".");?></td>

            <td><?php   echo number_format($field->getDBField("val2", 3, $cur),2,",",".");?></td>

            <td><?php   echo number_format($field->getDBField("val3", 4, $cur),2,",",".");?></td>

          </tr>		     
        
          <tr>		     
            <td>N&uacute;mero de Perdas</td>

            <td><?php   echo number_format($field->getDBField("num1", 5, $cur),0,",",".");?></td>

            <td><?php   echo number_format($field->getDBField("num2", 6, $cur),0,",",".");?></td>

            <td><?php   echo number_format($field->getDBField("num3", 7, $cur),0,",",".");?></td>

          </tr>
     </tbody>
</table>
<div class="divisoria01"></div>
<table>
	<caption>Quadro VI - Detalhes das Tr&ecirc;s Maiores Perdas Durante os &Uacute;ltimos Tr&ecirc;s Anos</caption>
  	<thead>
          <tr>
            <th>Ano</th>
            <th>Pa&iacute;s</th>
            <th>Raz&atilde;o Social do Inadimplente</th>
            <th>Valor Final da Perda - <?php   echo $extMoeda;?></th>
          </tr>	
    </thead>	
    <tbody>     
          <tr>		     
            <td rowspan="3">Corrente</td>
            <td><?php   echo $field->getDBField("country1", 8, $cur);?></td>
            <td><?php   echo $field->getDBField("name1", 17, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost1", 26, $cur),2,",",".");?></td>
          </tr>		      
          <tr>	
            <td><?php   echo $field->getDBField("country2", 9, $cur);?></td>
            <td><?php   echo $field->getDBField("name2", 18, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost2", 27, $cur),2,",",".");?></td>
          </tr>		      
          <tr>	
            <td><?php   echo $field->getDBField("country3", 10, $cur);?></td>
            <td><?php   echo $field->getDBField("name3", 19, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost3", 28, $cur),2,",",".");?></td>
          </tr>		     
          <tr>		     
            <td rowspan="3">Passado</td>
            <td><?php   echo $field->getDBField("country4", 11, $cur);?></td>
            <td><?php   echo $field->getDBField("name4", 20, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost4", 29, $cur),2,",",".");?></td>
          </tr>
          <tr>		      
            <td><?php   echo $field->getDBField("country5", 12, $cur);?></td>
            <td><?php   echo $field->getDBField("name5", 21, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost5", 30, $cur),2,",",".");?></td>
          </tr>		      
          <tr>		      
            <td><?php   echo $field->getDBField("country6", 13, $cur);?></td>
            <td><?php   echo $field->getDBField("name6", 22, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost6", 31, $cur),2,",",".");?></td>
          </tr>		     
          <tr>		     
            <td rowspan="3">Retrasado</td>
            <td><?php   echo $field->getDBField("country7", 14, $cur);?></td>
            <td><?php   echo $field->getDBField("name7", 23, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost7", 32, $cur),2,",",".");?></td>
          </tr>
          <tr>		      
            <td><?php   echo $field->getDBField("country8", 15, $cur);?></td>
            <td><?php   echo $field->getDBField("name8", 24, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost8", 33, $cur),2,",",".");?></td>
          </tr>		      
          <tr>		      
            <td><?php   echo $field->getDBField("country9", 16, $cur);?></td>
            <td><?php   echo $field->getDBField("name9", 25, $cur);?></td>
            <td><?php   echo number_format($field->getDBField("lost9", 34, $cur),2,",",".");?></td>
          </tr>		     
          <tr>
            <td colspan="5">Observa&ccedil;&otilde;es relevantes sobre os importadores inadimplentes</td>
          </tr>
          <tr>
            <td colspan="5"><?php   echo $field->getDBField("obs", 35, $cur);?></td>
         </tr>
  </tbody>
</table>
<div class="barrabotoes">
<button name="inicial"  onClick="this.form.comm.value='open';this.form.submit()" class="botaoagg">Tela Inicial</button>
<button name="anterior" onClick="this.form.comm.value='volte';this.form.submit()" class="botaovgg">Tela Anterior</button>
<button name="proxima"  type="submit" class="botaoagg">Pr&oacute;xima Tela</button>
</div>
</form>
<?php  } else {
?>
<p>Informe inv&aacute;lido</p>
<?php  }
?>
</div>
