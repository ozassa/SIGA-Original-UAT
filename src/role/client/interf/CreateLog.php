<!--
 Alterado Hicom (Gustavo) - 19/01/05 - login não precisa ser um e-mail
-->
<form action="<?php  echo $root;?>/role/access/Access.php" method=post>
  <input type=hidden name=comm value="setCreateLog">
  <P align=center class="textoBold">NOVO USUÁRIO<br>Operações de Curto Prazo</P>
  <P align=center><b>Digite o seu e-mail:<br><span class="textopeq">(que será utilizado como login)</span></b><br><INPUT size=30 name=login type="text" class="servicos"></P>
<!--  <P align=center><b>Digite o seu login:</b><br><INPUT size=30 name=login type="text" class="servicos"></P> -->
  <P align=center><b><a href="<?php  echo $root;?>/role/access/Access.php?comm=client">Já sou cadastrado</a></b></P>
  <P align=center><b>Senha:</b><br><span class="textopeq">(Utilize no mínimo 3 e no máximo 8 caracteres)</span></b><br><INPUT type="password" size=26 name=password1 class="servicos"></P>
  <P align=center><b>Confirme a senha: </b><br><INPUT type="password" size=26 name=password2 class="servicos"></P>
  <?php  if ($msg) { ?>
    <p align=center><font face=arial color=red><b><?php  echo $msg;?></b></font></p>
  <?php  } ?>
  <P align=center><input type=button value="Voltar" onClick="this.form.comm.value='client';this.form.submit()" class="servicos">
  <input type=submit value=" OK " class="servicos"></p>
</form>

