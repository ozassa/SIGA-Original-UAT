<form action="<?php echo $root; ?>role/credit/Credit.php" method="post" name="cancel">
  <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
  <input type="hidden" name="comm" value="cancelIncomplete">
  <div class="barrabotoes">
  	<button name="voltar" onClick="this.form.comm.value='IncompleteInform'" class="botaovgm">Voltar</button>
    <button name="cancelar" type="submit" class="botaovgm">Cancelar</button>
  </div>
</form>



