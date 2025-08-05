<br><br>

<!-- Tela em que o cliente escolhe o consultor desejado -->
<!-- Criado por Michel Saddock 12/09/2006 -->

<form action="<?php echo $root;?>role/area_consultor/listConsultor.php" method="post">
<input type=hidden name="comm" value="AdicionaConsultor">
<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($_GET['idInform'], ENT_QUOTES, 'UTF-8'); ?>">


<fieldset><legend>Deseja Selecionar um Consultor ?</legend>
&nbsp;&nbsp; <br><br>
&nbsp;&nbsp;<input type="radio" name="concorda" value="0">&nbsp;Sim
&nbsp;&nbsp;<input type="radio" name="concorda" value="1">&nbsp;Não
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<font class="texto">Selecione o consultor desejado:</font>
<select name="idConsultor" onChange="enviar()" class="texto">
<option value="0">Selecione</option>
 	  	   
<?php
    $query = "SELECT * FROM consultor where ativo='1'";

    $cur = odbc_exec($db, $query);

    while (odbc_fetch_row($cur))
    {
      $idconsultor = odbc_result($cur,1);
      $contato = odbc_result($cur, 3);
?>

<option value="<?php echo $idconsultor;?>" > <?php echo $contato;?> </option>

<?php
  } // Fecha while
?>
</select>

<br><br><br><br>
</fieldset>
<br><br><br><br>
<center>
Atenção, leia o informativo abaixo:<br><br>
<textarea name="aviso" cols= "45" rows= "5" id="textarea" class="texto"></textarea>
<br><input type="checkbox" name="concordo" value="sim"><font class="texto">Li e concordo com os termos acima</font>



<br><br><br><br><input class="servicos" name="botao" type="button" value="Cadastrar" onClick="this.form.comm.value='AdicionaConsultor2';this.form.submit()">
</center>
</form>

