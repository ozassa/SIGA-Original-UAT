<form action="<?php echo $root; ?>/role/access/access.php" method=post>
<input type=hidden name=comm value="setChange">
  <P align=center class="textoBold">Digite sua senha atual:</P>
  <P align=center><INPUT class="caixa" type=password size=30 name=login></P>
  <br>
  <P align=center class="textoBold">Nova senha:</P>
  <P align=center><INPUT class="caixa" type="password" size="26" name=p1></P>
  <P align=center class="textoBold">Confirme a nova senha:</P>
  <P align=center><INPUT class="caixa" type="password" size=26 name=p2></P>

  <?php if ($msg) { ?>							   
  <p align=center><font face=arial color=red><b><?php echo $msg; ?></b></font></p>
  <?php } ?>

  <br>
  <P align=center>
  <input class="servicos" type="submit" value=" OK " name=submit></p>
</form>

