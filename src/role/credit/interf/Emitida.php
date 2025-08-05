<?php include_once('../../../navegacao.php'); 
      include_once("../../consultaCoface.php");
?>

<div class="conteudopagina">
	<ul>      
	  <li class="campo2colunas">
        <label>Cliente</label>
        <?php echo $nameCl; ?>
      </li>
      <li class="campo2colunas">
        <label>CI <?php echo $nomeEmp; ?></label>
        <?php echo $contrat; ?>
      </li>
      <?php  if ($endValidity) { ?>
      <li class="campo2colunas">
        <label>Final da Vig&ecirc;ncia</label>
        <?php echo $endValidity; ?>
      </li>
	</ul>
      <?php  } ?>


<table>
<caption>Importadores exclu&iacute;dos durante a renova&ccedil;&atilde;o</caption>
	<thead>
    <tr>
      <th>Importador</th>
      <th>CI <?php echo $nomeEmp; ?></th>
    </tr>
	</thead>
  <tbody>
    <?php
      if($dados1){
        for ($a=0; $a < count($dados1); $a++) {
          echo "<tr>";
          echo "<td>".$dados1[$a]['name']."</td><td>".$dados1[$a]['ciCoface']."</td>";
          echo "</tr>";
        }
      }
    ?>	
  </tbody>
</table>

<?php  if ($msg != "") { ?>
<p><?php echo  $msg;?></p>
<?php  } ?>

<table>
<caption>Importadores com cr&eacute;dito reduzido automaticamente na renova&ccedil;&atilde;o</caption>
	<thead>
    <tr>
      <th>Importador</th>
      <th>Pa&iacute;s</th>
      <th>CI <?php echo $nomeEmp; ?></th>
      <th>Crédito(US$ Mil)</th>
    </tr>
	</thead>
  <tbody>
    <?php
      if($dados2){
        for ($r=0; $r < count($dados2); $r++) { 
          echo "<tr>";
          echo "<td>".$dados2[$r]['name']."</td><td>".$dados2[$r]['pais']."</td><td>".$dados2[$r]['ciCoface']."</td><td>".$dados2[$r]['credit']."</td>";
          echo "</tr>";
        }
      }
    ?>
	</tbody>
</table>

<table>
<caption>Importadores com cr&eacute;dito concedido h&aacute; mais de 6 meses</caption>
  <thead>
    <tr class=bgAzul>
      <th align=left>Importador</th>
      <th>Pa&iacute;s</th>
      <th>CI <?php echo $nomeEmp; ?></th>
      <th>Data do cr&eacute;dito</th>
      <th>Data Status</th>
    </tr>
  </thead>
  <tbody>
	<?php
    if($dados3){
      for ($i=0; $i < count($dados3); $i++) { 
        echo "<tr>";
        echo "<td>".$dados3[$i]['name']."</td><td>".$dados3[$i]['pais']."</td><td>".$dados3[$i]['ciCoface']."</td><td>".$dados3[$i]['creditDate']."</td><td>".$dados3[$i]['stateDate']."</td>";
        echo "</tr>";
      }
    } ?>
  </tbody>
</table>

<form action="<?php echo  $root;?>role/credit/Credit.php" method="post">
<input type="hidden" name="comm" value="open">
<input type="hidden" name="idNotification" value="<?php echo  $idNotification;?>">
<input type="hidden" name="idInform" value='<?php echo  $idInform;?>'>
<input type="hidden" name="done" value="">
<div class="barrabotoes">
	<button name="voltar" type="submit" class="botaovgm">Voltar</button>
    <button name="ok" onClick="this.form.comm.value='emitida';this.form.done.value=1;this.form.submit()"class="botaoagm">OK</button>
</div>

</form>
</div>