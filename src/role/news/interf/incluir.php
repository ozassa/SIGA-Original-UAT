<script language="JavaScript" src="<?php echo $root;?>scripts/calendario.js"></script>



<FORM action="<?php echo  $root; ?>role/news/News.php" method="post" name="form" onsubmit="return valida_cadastro()">

<input type=hidden name="comm" value="incluirsql">



<TABLE border="0" cellPadding="2" cellSpacing="0" width="100%">

  <TR>

    <TD align="center" class="bgCinza">Incluir Notícia</TD>

  </TR>

  <TR>

    <TD>&nbsp;</TD>

  </TR>

  <TR>

    <TD class="textoBold">Título: <input class="caixa" type="text" name="titulo" size="60"></TD>

  </TR>

  <TR>

    <TD class="textoBold">Data&nbsp;&nbsp;: <input class="caixa" type="text" name="data" size="15" onfocus="blur()"> <A HREF="javascript:showCalendar(document.form.data)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A></TD>

  </TR>

  <TR>

    <TD class="textoBold">Exibir Notícia: <input class="caixa" type="radio" name="exibir" value="1" checked> Sim  <input type="radio" name="exibir" value="0"> Não </TD>

  </tr>

  <TR>

    <TD class="textoBold">Notícia:</TD>

  </tr>

  <TR>

    <TD class="texto"><textarea class="caixa" rows="10" cols="100" name="news"></textarea></TD>

  </TR>

</TABLE>

<?php echo $msg; ?>

<P><INPUT class="servicos" type=submit value="Incluir"> <INPUT class="servicos" name=Reset type=reset value=Cancelar> </P>

</form>

