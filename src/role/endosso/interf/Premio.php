<a name=endosso></a>
<FORM action="<?php echo $root;?>role/endosso/Endosso.php#endosso" method="post">
<input type=hidden name="comm" value="sendProp">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">
<input type=hidden name="idNotification" value="<?php echo $idNotification;?>">
<input type=hidden name="idEndosso" value="<?php echo $idEndosso;?>">
<input type=hidden name="idPremio" value="<?php echo $idPremio;?>">

<TABLE border="0" cellSpacing="0" cellpadding="2" width="100%" align="center">
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="textoBold" width="35%">Cliente:</TD>
    <TD class="texto" colspan="2"><?php echo $name;?></TD>
  </TR>
  <TR>
    <TD class="textoBold" width="25%">Número da Proposta:</TD>
    <TD class="texto" width="75%" colspan="2"> <?php echo $contrat;?></TD>
  </TR>
  <TR>
    <TD class="textoBold" width="25%">Número da Apólice:</TD>
    <TD class="texto" width="75%" colspan="2"> <?php echo $apolice;?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Vigência:</TD>
    <TD class="texto" colspan="2"><?php echo substr($ini_vig, 8, 2). "/". substr($ini_vig, 5, 2). "/". substr($ini_vig, 0, 4);?> a <?php echo substr($fim_vig, 8, 2). "/". substr($fim_vig, 5, 2). "/". substr($fim_vig, 0, 4);?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Tipo de Endosso:</TD>
    <TD class="texto" colspan="2">Prêmio Mínimo</TD>
  </TR>
  <TR>
    <TD class="textoBold">Data de Criação:</TD>
    <TD class="texto" colspan="2"><?php echo $bornDate;?></TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="bgAzul" align="center">&nbsp;</TD>
    <TD class="bgAzul" align="center" width="40%">Dados Antigos</TD>
    <TD class="bgAzul" align="center" width="35%">Dados Novos</TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="textoBold">Prêmio Mínimo</TD>
    <TD class="texto" align=center>US$ <?php echo number_format($premio_min_old, 2, ',', '.');?></TD>
    <TD class="texto" align=center>US$ <?php echo number_format($premio_min, 2, ',', '.');?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Taxa Mínima</TD>
    <TD class="texto" align=center><?php echo number_format( ($tx_min_old * 100), 3, ',', '.');?>%</TD>
    <TD class="texto" align=center><?php echo number_format( ($tx_min * 100), 3, ',', '.');?>%</TD>
  </TR>
<input type=hidden name=premio_min value="<?php echo $premio_min;?>">
<input type=hidden name=premio_min_old value="<?php echo $premio_min_old;?>">
<input type=hidden name=tx_min value="<?php echo $tx_min;?>">
  <TR>
    <td align=center colspan=3><br><br><br><br>
    <input type="button" class="servicos" value="Voltar" onClick="check(this.form, 'view')">
<?php if($propSent){ ?>
<input type=button class=servicos value="Cancelar" onClick="cancela(<?php echo $idInform;?>, <?php echo $idEndosso;?>)">
<!--<INPUT type=button value="Cancelar Proposta" class="sair" onClick="check(this.form, 'cancelar')">-->
<INPUT type=button value="Emitir" class="sair" onClick="check(this.form, 'propRecebidaPM')">
<?php }else{ ?>
    <INPUT type="submit" class="servicos" value="Enviar Proposta">
<?php } ?>
    </td>
  </TR>
</TABLE>
</form>

<form name="cancel" action="<?php echo $root;?>role/endosso/Endosso.php">
<input type=hidden name="comm" value="cancelar">
<input type=hidden name=idInform value="<?php echo $idInform;?>">
<input type=hidden name=idNotification value="<?php echo $idNotification;?>">
<input type=hidden name="idEndosso" value="<?php echo $idEndosso;?>">
</form>

<script language=javascript>
function check(f, c){
  f.comm.value = c;
  f.submit();
}
</script>

<script>
function cancela(IdInform, IdEndosso) { 
if (confirm ("Deseja Realmente Cancelar esse Endosso?")) {
   document.forms["cancel"].idInform.value=IdInform;
   document.forms["cancel"].idEndosso.value=IdEndosso;
   document.forms["cancel"].submit();
}
}
</script>

