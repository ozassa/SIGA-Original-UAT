<FORM action="<?php echo $root;?>role/endosso/Endosso.php" method="post">
<input type=hidden name="comm" value="sendProp">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">
<input type=hidden name="idNotification" value="<?php echo $idNotification;?>">
<input type=hidden name="idEndosso" value="<?php echo $idEndosso;?>">
<input type=hidden name="idPremio" value="<?php echo $idPremio;?>">

<TABLE width=100% cellspacing=0 cellpadding=2 border=0 align="center">

<?php require_once("../endosso/natureza.php");
?>
  <TR>
    <TD class="bgCinza" align="center" colspan="4">Endosso de Natureza da Operação</TD>
  </TR>
  <TR>
    <TD class="textoBold">Cliente:</TD>
    <TD class="texto" colspan="3"> <?php echo $name;?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Tipo de Endosso:</TD>
    <TD class="texto" colspan="3">Natureza da Operação</TD>
  </TR>
  <TR>
    <TD class="textoBold">Data de Criação:</TD>
    <TD class="texto" colspan="3"><?php echo $bornDate;?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Solicitante:</TD>
    <TD class="texto" colspan="3"><?php echo $solicitante;?></TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="bgCinza" align="center" colspan="4">Natureza da Operação</TD>
  </TR>
  <TR>
    <TD class="textoBold">Setor Antigo</td>
    <TD class="texto" colspan="3"> <?php echo $sector;?> </TD>
  </TR>
  <TR>
    <TD class="textoBold">Produtos Antigos</td>
    <TD class="texto" colspan="3"> <?php echo $products;?> </TD>
  </TR>
  <TR>
    <TD colspan="4">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="textoBold">Setor Novo</TD>
    <TD class="texto" colspan="3"><?php echo $new_sector;?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Produtos Novos</TD>
    <TD class="texto" colspan="3"><?php echo $new_natureza;?></TD>
  </TR>

</table>
<p align=center>
<INPUT type=button value="Cancelar Proposta" class="sair" onClick="check(this.form, 'cancelar')">
<INPUT type=button value="Proposta Recebida" class="sair" onClick="check(this.form, 'propRecebida')">
</p>
</form>

<script language=javascript>
function check(f, c){
  f.comm.value = c;
  f.submit();
}
</script>
