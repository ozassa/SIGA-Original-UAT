<form action="<?php  echo $root;?>/role/access/Access.php" method=post>
<input type=hidden name=comm value="setRecoverPassword">
<P align="center" class="textoBold">ESQUECEU A SENHA?<br>Operações de Curto Prazo</P>
<P>&nbsp;</P>
<P align=center><b>Digite o seu e-mail que lhe enviaremos sua senha: </b></P>
<P align=center><INPUT type="text" size=28 name=login class="servicos"></P>
<?php  if ($msg) { ?>							   
<p align=center><font face=arial color=red ><?php  echo $msg;?></font></p>
<?php  } ?>
<P>&nbsp;</P>
<P align=center><input type=button value="Voltar" onClick="this.form.comm.value='client';this.form.submit()" class="servicos"> <input type=submit value=" OK " class="servicos"></p>
</form>

