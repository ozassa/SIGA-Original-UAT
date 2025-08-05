<form action="<?php echo $root;?>role/access/Access.php" method="post">
<input type=hidden name="comm" value="functionaryLogin">
<TABLE border="0" cellSpacing="0" cellpadding="2">
  <TR>							   
    <TD vAlign=center align="center" colspan="2">&nbsp;</TD>
  </TR>							   
  <TR>							   
    <TD vAlign=center align="center" colspan="2" class="textoBold"><U>LOGIN</U></TD>
  </TR>							   
  <TR>							   
    <TD vAlign=center align="center" colspan="2">&nbsp;</TD>
  </TR>							   
  <TR bgcolor="#cccccc">							   
    <TD vAlign=center align="center">Usuário:</TD>
    <TD vAlign=center align="center"><input class="servicos" type="text" size="15" name="login"></TD>
  </TR>							   
  <TR bgcolor="#cccccc">
    <TD vAlign=center align="center">Senha:</TD>
    <TD vAlign=center align="center"><input class="servicos" type="password" size="15" name="password"></TD>
  </tr>							   
  <TR>							   
    <TD vAlign=center align="center" colspan="2">&nbsp;</TD>
  </TR>
  <?php if ($msg) { ?>							   
  <TR>							   
    <TD vAlign=center align="center" colspan="2"><font color=red><?php echo $msg;?></font></TD>
  </TR>
  <?php } ?>
  <TR>							   
    <TD vAlign=center align="center" colspan="2"><INPUT class="servicos" type="submit" value=" OK "></TD>
  </TR>							   
  </TBODY>
</TABLE>
</form>

<P>&nbsp;</P>

<P>Desenvolvido pela Seguradora Brasileira de Crédito à Exportação</P>
<P><FONT size=1>&copy; Todos os Direitos Reservados</FONT></P>

</DIV>

</td>
</tr>
</table>

