<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD colspan="2" class="bgCinza" align="center">Histórico de Sinistro</TD>
  </TR>
   <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post" name="incluir">
<input type=hidden name="comm" value="obs">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<input type=hidden name="idSinistro" value="<?php echo $idSinistro; ?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter; ?>">
  <TR>
    <td colspan=2>&nbsp;</td>
  </TR>
<?php $name = $user->name;
?>
<input type=hidden name="name" value="<?php echo $name; ?>">
  <TR>
    <td colspan=2>Observação:</td>
  </TR>
  <TR>
    <td colspan=2><TEXTAREA class=caixa rows=6 cols=70 name="obs"></TEXTAREA></td>
  </TR>
  <TR>
    <td colspan=2>
<?php if ($role["sinistro"]) { 
       if($vol == 1){?>
<INPUT class=servicos onclick="this.form.comm.value='detalhesSinistro';this.form.submit()" type=button value="Voltar">
<?php }else{?>
<INPUT class=servicos onclick="this.form.comm.value='view';this.form.submit()" type=button value="Voltar">
<?php }
     } else { ?>
<INPUT class=servicos onclick="this.form.comm.value='detalhesSinistro';this.form.submit()" type=button value="Voltar">
<?php }?>
 <input type="submit" value="Incluir" class="servicos">
</td>
  </TR>
</form>    

</table>



<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
  <TR class=bgCinza>
    <td align=center colspan=3><font size=2>OBSERVAÇÕES</FONT></TD>
  </TR>
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
  <TR>
    <td class=bgAzul align=center width="10%">Data</td>
    <td class=bgAzul align=center width="20%">Usuário</td>
    <td class=bgAzul align=center width="70%">Obs.</td>
  </TR>
<?php $query = "SELECT * FROM SinistroObs WHERE idSinistro = $idSinistro ORDER BY date";
    $cur = odbc_exec($db,$query);
    $i = 0;
    while (odbc_fetch_row($cur)) {
      $i++;
      $name = odbc_result($cur,'name'); 
      $date = odbc_result($cur,'date');
      $observ = odbc_result($cur, 'obs');

?>
  <TR <?php echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"");?>>
    <td class="texto" align=center><?php echo substr($date,8,2)."/".substr($date,5,2)."/".substr($date,0,4); ?></td>
    <td class="texto" align=center><?php echo $name ?></td>
    <td class="texto"><?php echo $observ ?></td>
  </TR>
<?php } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=3 class="bgCinza">Nenhuma Observação Cadastrada</TD>
  </TR>

<?php }
?>

</TABLE>
