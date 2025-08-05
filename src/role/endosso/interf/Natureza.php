<?php require_once("../../../../site/includes/sbce.css"); ?>
</head>
<body bgColor="#ffffff" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">

<br><br>

<a name=endosso></a>
<FORM action="<?php echo $root;?>role/endosso/Endosso.php#endosso" method="post">
<TABLE width=96% cellspacing=0 cellpadding=3 border=0 align="center">
  <tr>     
    <TD colspan="3">&nbsp;</TD>
  </tr>
  <TR>
    <TD class="textoBold" width="25%">Segurado: </TD>
    <TD class="texto" width="75%" colspan="2"> <?php echo $name;?></TD>
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
    <TD class="texto" colspan="2">Natureza da Operação</TD>
  </TR>
  <TR>
    <TD class="textoBold">Data de Criação:</TD>
    <TD class="texto" colspan="2"><?php echo substr($bornDate, 8, 2). "/". substr($bornDate, 5, 2). "/". substr($bornDate, 0, 4);?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Solicitante:</TD>
    <TD class="texto" colspan="2"><?php echo $solicitante;?></TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="bgAzul" align="center">&nbsp;</TD>
    <TD class="bgAzul" width="40%">Dados Antigos</TD>
    <TD class="bgAzul" width="35%">Dados Novos</TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="textoBold">Natureza de Operação</td>
    <TD class="texto"> <?php echo $sector;?> </TD>
    <TD class="texto"><?php echo $new_sector;?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Produtos</td>
    <TD class="texto"> <?php echo $products;?> </TD>
    <TD class="texto"><?php echo $new_natureza;?></TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
<input type=hidden name=new_sector value="<?php echo $new_idSector;?>">
<input type=hidden name=new_natureza value="<?php echo $new_natureza;?>">

<?php if($idPremio){ 
      $c = odbc_exec($db, "select premioOld, premio, txMin, txMinOld, txRise from EndossoPremio where id=$idPremio");
      if(odbc_fetch_row($c)){
	$premio_min_old = odbc_result($c, 1);
	$premio_min = odbc_result($c, 2);
	$tx_min = odbc_result($c, 3);
	$tx_min_old = (odbc_result($c, 4) + odbc_result($c, 4) * odbc_result($c, 5) )* 100;
      }
      if(($premio_min_old != $premio_min) && ($premio_min != 0)){
?>
  <TR>
    <TD class="textoBold">Prêmio Mínimo</TD>
    <TD class="texto">US$ <?php echo number_format($premio_min_old, 2, ',', '.');?></TD>
    <TD class="texto">US$ <?php echo number_format($premio_min, 2, ',', '.');?></TD>
  </TR>
  <TR>
    <TD colspan="2"><P>&nbsp;</P></TD>
  </TR>
<input type=hidden name=idPremio value="<?php echo $idPremio;?>">
<input type=hidden name=premio_min value="<?php echo $premio_min;?>">
<input type=hidden name=premio_min_old value="<?php echo $premio_min_old;?>">

<?php }
    //  if(($tx_min_old != $tx_min) && ($tx_min != 0)){
      if((number_format($tx_min_old, 3, ',', '.') != number_format($tx_min, 3, ',', '.')) && ($tx_min != 0)){
?>
  <TR>
    <TD class="textoBold">Taxa de Prêmio</TD>
    <TD class="texto"><?php echo number_format($tx_min_old, 3, ',', '.');?>%</TD>
    <TD class="texto"><?php echo number_format($tx_min, 3, ',', '.');?>%</TD>
  </TR>
  <TR>
    <TD colspan="2"><P>&nbsp;</P></TD>
  </TR>
<input type=hidden name=tx_min value="<?php echo $tx_min;?>">
<input type=hidden name=tx_min_old value="<?php echo $tx_min_old;?>">

<?php }
    }
?>

</table>

<br>
<div align="center" >
<input type=hidden name="comm" value="emitirDados">
<input type=hidden name=idInform value="<?php echo $idInform;?>">
<input type=hidden name=idEndosso value="<?php echo $idEndosso;?>">
<input type=hidden name=idNotification value="<?php echo $idNotification;?>">
<input type=button value="Voltar" onClick="check(this.form, 'view')" class="sair">
<input type=button class=servicos value="Cancelar" onClick="cancela(<?php echo $idInform;?>, <?php echo $idEndosso;?>)">
<!--
 <INPUT type=button value="Cancelar" class="sair" onClick="check(this.form, 'cancelar')">
-->
<?php if($comm == "natOperPrMin"){ ?>
<INPUT type=button value="Emitir" class="sair" onClick="check(this.form, 'propRecebida')">
<?php } else { ?>
<INPUT type=button value="Solicitar Tarifação" class="sair" onClick="check(this.form, 'natRecebida')">
<!--
 <INPUT type=button value="Reemitir Proposta" class="sair" onClick="check(this.form, 'reemitir')">
-->
<?php } ?>
</div>
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



</body>
