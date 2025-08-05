


<form action="<?php echo $root;?>role/endosso/Endosso.php" method=post>
<input type=hidden name="comm" value="gerar">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">
<input type=hidden name="idNotification" value="<?php echo $idNotification;?>">
<input type=hidden name="idEndosso" value="<?php echo $idEndosso;?>">

<div align=center>
<table  border="0" cellSpacing="0" cellpadding="2" width="96%" align="center">
 <tr>
  <td colspan="6">Cliente: <span class="texto"><?php echo $name;?></span>&nbsp;</td>
 </tr>
 <tr>
  <td colspan="6">Vigência: <span class="texto"><?php echo $start;?></span> à <span class="texto"><?php echo $end;?></span>&nbsp;</td>
 </tr>
 <tr>
  <td colspan="6">&nbsp;</td>
 </tr>
 <tr>
  <td colspan="6">Valores em US$</td>
 </tr>
 <tr>
  <td colspan="6">&nbsp;</td>
 </tr>
 <tr>
  <td class=bgAzul>&nbsp;</td>
  <td class=bgAzul align=center>Total Exportado</td>
  <td class=bgAzul align=center>Total Negado</td>
  <td class=bgAzul align=center>Total Inativado</td>
  <td class=bgAzul align=center>Total Analisado</td>
  <td class=bgAzul align=center>Total Coberto</td>
 </tr>
<?php $i = 0;
while(odbc_fetch_row($c)){
  $n = odbc_result($c, 1);
  $i = 1 - $i;
?>
 <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
  <td class="texto" nowrap><?php echo $n;?>ª DVE</td>
  <td class="texto" align=center>100000</td>
  <td class="texto" align=center>20000</td>
  <td class="texto" align=center>5000</td>
  <td class="texto" align=center>10000</td>
  <td class="texto" align=center>65000</td>
 </tr>
<?php }
?>
 <tr bgcolor="#CCCCCC">
  <td class="textoBold">Totais</td>
  <td class="textoBold" align=center>0</td>
  <td class="textoBold" align=center>0</td>
  <td class="textoBold" align=center>0</td>
  <td class="textoBold" align=center>0</td>
  <td class="textoBold" align=center>965.000</td>
 </tr>
 <tr>
  <td colspan="6">&nbsp;</td>
 </tr>
 <tr>
  <td colspan="6" align="center">
   <table border="0" cellSpacing="0" cellpadding="2" width="70%" align="center">
    <tr>
     <td colspan="2" ALIGN="center">RESUMO DO CÁLCULO DA PARCELA DE AJUSTE</td>
    </tr>
    <tr>
     <td colspan="2" ALIGN="center">&nbsp;</td>
    </tr>
    <tr>
     <td nowrap>Total Líquido:</td>
     <td width="100%">965.000,00</td>
    </tr>
    <tr>
     <td nowrap>Taxa de Prêmio:</td>
     <td>0,5%</td>
    </tr>
    <tr>
     <td colspan="2" ALIGN="center">&nbsp;</td>
    </tr>
    <tr>
     <td nowrap>Prêmio Total:</td>
     <td>4.825,00</td>
    </tr>
    <tr>
     <td nowrap>Prêmio Mínimo</td>
     <td>3.000,00</td>
    </tr>
    <tr>
     <td colspan="2" ALIGN="center">&nbsp;</td>
    </tr>
    <tr>
     <td nowrap>Valor da Parcela de Ajuste:</td>
     <td class="verm">1.825,00</td>
    </tr>
   </TABLE> 
  </td>
 </tr>
 <tr>
  <td colspan="6">&nbsp;</td>
 </tr>
</table>

<br>
<input type=button value="Voltar" class=servicos onClick="check(this.form, 'view')">&nbsp;&nbsp;
<input type=submit value="Emitir" class=sair>
</div>
</form>

<script language=javascript>
function check(f, c){
  f.comm.value = c;
  f.submit();
}
</script>
