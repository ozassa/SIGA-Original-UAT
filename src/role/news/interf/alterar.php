<?php 

$cur=odbc_exec(

$db,

"SELECT titulo, data, exibir, noticia FROM Noticias WHERE id=".$id.""

);



?>



<script language="JavaScript" src="<?php echo $root;?>scripts/calendario.js"></script>



<FORM action="<?php echo  $root; ?>role/news/News.php" method="post" name="form" onsubmit="return valida_cadastro()">

<input type=hidden name="comm" value="alterarsql">

<input type=hidden name="id" value="<?php echo  $id; ?>">



<TABLE border="0" cellPadding="2" cellSpacing="0" width="100%">

  <TR>

    <TD align="center" class="bgCinza">Alterar Notícia</TD>

  </TR>

  <TR>

    <TD>&nbsp;</TD>

  </TR>

  <TR>

    <TD class="textoBold">Título: <input class="caixa" type="text" name="titulo" size="60" value="<?php echo  odbc_result($cur,1); ?>"></TD>

  </TR>

  <TR>

    <TD class="textoBold">Data&nbsp;&nbsp;: <input class="caixa" type="text" name="data" size="15" value="<?php echo  odbc_result($cur,2); ?>" onfocus="blur()"> <A HREF="javascript:showCalendar(document.form.data)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A></TD>

  </TR>

  <TR>

    <TD class="textoBold">Exibir Notícia: <input type="radio" name="exibir" <?php echo  odbc_result($cur,3) ? "checked " : ""; ?> value="1"> Sim  <input type="radio" name="exibir"  <?php echo  odbc_result($cur,3) ? "" : "checked "; ?>value="0"> Não </TD>

  </tr>

  <TR>

    <TD class="textoBold">Notícia:</TD>

  </tr>

  <TR>

    <TD class="texto"><textarea class="caixa" rows="10" cols="100" name="news"><?php echo  odbc_result($cur,4); ?></textarea></TD>

  </TR>

</TABLE>

<?php echo $msg;?>

<P><INPUT class="servicos" type=submit value="Alterar"> <input class="servicos" type=button value="Excluir essa Notícia" onClick="this.form.comm.value='excluir';this.form.submit()"></P>

</form>

