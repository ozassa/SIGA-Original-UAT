<?php
  
include_once('../../../navegacao.php'); 

?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
	<ul>
   		<li class="campo2colunas"><label>Segurado:</label><?php echo  $nameSegurado;?></li>
  		<li class="campo2colunas"><label>Ci Segurado:</label><?php echo  $contrat;?></li>
 		<li class="campo2colunas"><label>Comprador: </label><?php echo  $importer;?></li>
		<li class="campo2colunas"><label>Crédito Antigo: US$</label> <?php echo  number_format($oldCredit, 2, ',', '.');?></li>
		<li class="campo2colunas"><label>Crédito Atual: US$</label> <?php echo  number_format($newCredit, 2, ',', '.');?></li>
	</ul>
	<br clear="all" />
    <form action="<?php echo  $root;?>role/credit/Credit.php" method="post">
        <input type="hidden" name="comm" value="reduzido">
        <input type="hidden" name="finish" value="1">
        <input type="hidden" name="idNotification" value="<?php echo  $idNotification;?>">
        <div class="barrabotoes">
            <button type="button"  class="botaovgm" onClick="this.form.comm.value='notif'; this.form.submit()">Voltar</button>
            <button type="button"  class="botaoagm" onClick="this.form.submit();">OK</button>
        </div>
    </form>
</div>
