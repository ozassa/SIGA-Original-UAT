
<br><br>

<a name=endosso></a>
<TABLE width=96% cellspacing=0 cellpadding=3 border=0 align="center">
  <tr>     
    <TD colspan="3">&nbsp;</TD>
  </tr>
  <TR>
    <TD class="textoBold" width="25%">Cliente:</TD>
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
    <TD class="texto" colspan="2">Dados Cadastrais</TD>
  </TR>
  <TR>
    <TD class="textoBold">Data de Criação:</TD>
    <TD class="texto" colspan="2"><?php echo $bornDate;?></TD>
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

<?php $dados = array('name' => 'Razão Social',
	       'cnpj' => 'CNPJ',   
	       'address' => 'Endereço',
           'number' => 'Numero',
	       'addresscomp'=>'Complemento',
	       'city' => 'Cidade',
	       'cep' => 'CEP',
	       'region' => 'Região');
foreach ($dados as $d => $label){
		    
  if(${"new_$d"}){
    // Testa se deve ser ignorada a mudança de idRegion (caso não tenha sido mudada efetivamente:
    if ( ($d == "region") && ($new_address == "") ) { 
    }
    else {
//echo "<pre>D:$d NEW_REGION:$new_region NEW_ADDRESS:$new_address!</pre>";
?>
  <TR>
    <TD class="textoBold"><?php echo $label;?></td>
    <TD class="texto"> <?php echo $$d;?> </TD>
    <TD class="texto"><?php echo ${"new_$d"};?></TD>
  </TR>
<?php } // else do if acima
  } // if acima
} // foreach
?>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>

</table>

<br>
<div align="center" >
<FORM action="<?php echo $root;?>role/endosso/Endosso.php#endosso" method="post">
<input type=hidden name="comm" value="emitirDados">
<input type=hidden name=idInform value="<?php echo $idInform;?>">
<input type=hidden name=idEndosso value="<?php echo $idEndosso;?>">
<input type=hidden name=idNotification value="<?php echo $idNotification;?>">
<input type=hidden name="new_name" value="<?php echo $new_name;?>">
<input type=hidden name="new_address" value="<?php echo $new_address;?>">
<input type=hidden name="new_number" value="<?php echo $new_number;?>">
<input type=hidden name="new_addresscomp" value="<?php echo $new_addresscomp;?>">
<input type=hidden name="new_city" value="<?php echo $new_city;?>">
<input type=hidden name="new_cep" value="<?php echo $new_cep;?>">
<input type=hidden name="new_idRegion" value="<?php echo $new_idRegion;?>">
<input type=hidden name="new_cnpj" value="<?php echo $new_cnpj;?>">
<input type=button value="Voltar" onClick="check(this.form, 'view')" class="sair">
<?php if($comm == 'razao'){ ?>
<input type=button class=servicos value="OK" onClick="check(this.form, 'killnotif')">
<?php }else{ ?>
<input type=button class=servicos value="Cancelar" onClick="cancela(<?php echo $idInform;?>, <?php echo $idEndosso;?>)">
<!--<INPUT type=button value="Cancelar" class="sair" onClick="check(this.form, 'cancelar')">-->
<INPUT type=button value="Emitir" class="sair" onClick="check(this.form, 'recebida')">
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
function cancela(IdInform,IdEndosso) { 
if (confirm ("Deseja Realmente Cancelar esse Endosso?")) {
   document.forms["cancel"].idEndosso.value=IdEndosso;
   document.forms["cancel"].idInform.value=IdInform;
   document.forms["cancel"].submit();
}
}
</script>
