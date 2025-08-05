<script language="JavaScript" src="<?php  echo $root;?>scripts/cnpj.js"></script>
<script Language="JavaScript">
<!--
function checa_formulario(cadastro){
    alter = "";
  if (cadastro.new_name.value != ""){
     alter += "Razão Social";	 
  }else{
        verErro("Por Favor, Preencha a Razão Social");
        cadastro.new_name.focus();
        return (false);  
  }

  if ((cadastro.new_address.value == "") || (cadastro.new_city.value == "") || (cadastro.new_cep.value != "") || (cadastro.idRegion.value == 0)){
      if (cadastro.new_address.value == ""){
        verErro("Por Favor, Preencha o Endereço");
        cadastro.new_address.focus();
        return (false);
      }
      if (cadastro.new_city.value == ""){
        verErro("Por Favor, Preencha a Cidade ");
        cadastro.new_city.focus();
        return (false);
      }
      if (cadastro.new_cep.value == ""){
        verErro("Por Favor, Preencha o CEP");
        cadastro.new_cep.focus();
        return (false);
      }
      if (cadastro.idRegion.value == 0){
        verErro("Por Favor, Escolha uma Região");
        cadastro.idRegion.focus();
        return (false);
      }
  }
  
   if (cadastro.new_cnpj.value != "" && !valida_CGC (cadastro.new_cnpj.value)) {
    verErro("Por Favor, Preencha corretamente o CNPJ");
    cadastro.new_cnpj.focus();
    cadastro.new_cnpj.select();
    return (false);
  }
   	
  if (cadastro.new_cnpj.value != ""){
    if (alter != "") { alter += ", "; }
    alter += "CNPJ";
  }
 
  if (cadastro.new_address.value != ""){
    if (alter != "") { alter += ", "; }
    alter += "Endereço";
  }else{
    verErro("Por Favor, Preencha o endereço");
    cadastro.new_address.focus();
    return (false);    
  }
  
  if(alter != "" && confirm ("Confirma alteração de: " + alter)) {
    return (true);
  } else {
    return (false);
  }
}

function CheckNum() {
  if (isNaN(document.all.new_number.value)){
                   verErro("O campo Numero deve conter apenas numeros!");
                   document.all.new_number.focus();
                   return false;
         }
}

//-->
</script>
<?php  function arruma_cnpj($c){
  if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
    return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
  }
}

?>
<?php 
include_once("../../consultaCoface.php");
?>
<br><br>
<a name=endosso></a>
<TABLE width=96% cellspacing=0 cellpadding=3 border=0 align="center">

<FORM name="cadastro" action="<?php  echo $root; ?>role/client/Client.php#endosso" method="post" onsubmit="return checa_formulario(this)">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="tipo" value="<?php  echo $tipo;?>">
<input type=hidden name="comm" value="emitirDadosCadastrais">
<input type=hidden name="back" value="<?php  echo $back;?>">
<input type=hidden name="formfocus" value="cadastro">
<input type=hidden name="fieldfocus" value="new_name">
<?php  if($role["client"]){ ?>
  <TR>
    <TD align="center" colspan="2"><H3>Endosso de Dados Cadastrais</H3></TD>
  </TR>
<?php  } ?>
  <TR>
    <TD align="center" colspan="2">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="bgCinza" colspan="2">Altera os dados cadastrais do segurado na apólice</TD>
  </TR>
  <TR>
    <TD align="center" colspan="2">&nbsp;</TD>
  </TR>
  <TR>
    <TD colspan="2">Instruções:</TD>
  </TR>
  <TR>
    <TD class="texto" valign="top">1-</td>
    <TD class="texto" valign="top"> Preencha os campos abaixo de acordo com o tipo de endosso desejado, em seguida, clique no botão "Criar Endosso". </TD>
  </TR>
  <TR>
    <TD class="texto" valign="top">2-</td>
    <TD class="texto" valign="top">Documentos necessários: </TD>
  </TR>
  <TR>
    <TD class="texto" valign="top">-</td>
    <TD class="texto" valign="top">Cópia autenticada do ato societário que formalizou a alteração, devidamente registrada na Junta Comercial;</TD>
  </TR>
  <TR>
    <TD class="texto" valign="top">-</td>
    <TD class="texto" valign="top">Cartão de CNPJ do segurado, atualizado.</TD>
  </TR>
  <TR>
    <TD align="center" colspan="2">&nbsp;</TD>
  </TR>
  <TR>
    <TD colspan="2">Enviar para o seguinte endereço:</TD>
  </TR>
  <TR>
    <TD>&nbsp;</td>
    <td><?php echo $nomeEmpSBCE; ?></TD>
  </TR>
  <TR>
    <TD>&nbsp;</td>
    <TD>Rua Senador Dantas, 74 - 16º Andar</TD>
  </TR>
  <TR>
    <TD>&nbsp;</td>
    <TD>Centro - Rio de Janeiro - CEP:  20031-201 </TD>
  </TR>
  <TR>
    <TD align="center" colspan="2">&nbsp;</TD>
  </TR>
  <TR>
    <TD align="center" colspan="2"><hr></TD>
  </TR>
</table>

<?php  $cur=odbc_exec( $db, "SELECT inf.name, inf.cnpj, inf.address, inf.city, inf.cep, r.description, inf.idRegion, inf.addressComp, inf.addressNumber  FROM Inform inf JOIN Region r ON (inf.idRegion = r.id) WHERE (inf.id = $idInform)");
?>

<input type=hidden name="nameOld" value="<?php  echo odbc_result($cur, 1);?>">
<input type=hidden name="cnpjOld" value="<?php  echo odbc_result($cur, 2);?>">
<input type=hidden name="addressOld" value="<?php  echo odbc_result($cur, 3);?>">
<input type=hidden name="numberlold" value="<?php  echo odbc_result($cur, 9);?>">
<input type=hidden name="addresscomplold" value="<?php  echo odbc_result($cur, 8);?>">
<input type=hidden name="cityOld" value="<?php  echo odbc_result($cur, 4);?>">
<input type=hidden name="cepOld" value="<?php  echo odbc_result($cur, 5);?>">
<input type=hidden name="idRegionOld" value="<?php  echo odbc_result($cur, 7);?>">

<?php  if($msg){
      echo "<p align=center><font color=#ff0000>$msg</font>";
    }
?>

<TABLE width="96%" cellspacing="0" cellpadding="2" border="0" align="center">
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="bgAzul" width="20%" align="right">&nbsp;   </TD>
    <TD class="bgAzul" width="50%" align="center">Dados Atuais   </TD>
    <TD class="bgAzul" width="30%" align="center">Alterar para  </TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="textoBold">Razão Social:</TD>
    <TD class="texto"><?php  echo odbc_result($cur, 1);?></TD>
    <TD align="center"><input type="text" class="caixa" size="40" name="new_name" value="<?php  echo $new_name;?>"></TD>
  </TR>
  <TR>
    <TD class="textoBold">CNPJ:</TD>
    <TD class="texto"><?php  echo arruma_cnpj(odbc_result($cur, 2));?></TD>
	<TD align="center"><input type="text" class="caixa" size="40" name="new_cnpj" value="<?php  echo $new_cnpj;?>" onBlur="if (this.value != '') {valida_cadastro()}"></TD>     
  </TR>  
  <TR>
    <TD class="textoBold">Endereço Atual:</TD>
    <TD class="texto"><?php  echo odbc_result($cur, 3);?></TD>
    <TD align="center"><input type="text" class="caixa" size="40" name="new_address" value="<?php  echo $new_address;?>"></TD>
  </TR>
  <TR>
    <TD class="textoBold">Numero Atual:</TD>
    <TD class="texto"><?php  echo odbc_result($cur, 9);?></TD>
    <TD align="center"><input type="text" class="caixa" size="40" name="new_number" value="<?php  echo $new_address;?>" onBlur="CheckNum()"></TD>
  </TR>
  <TR>
    <TD class="textoBold">Complemento Atual:</TD>
    <TD class="texto"><?php  echo odbc_result($cur, 8);?></TD>
    <TD align="center"><input type="text" class="caixa" size="40" name="new_complemento" value="<?php  echo $new_address;?>"></TD>
  </TR>
  <TR>
    <TD class="textoBold">Cidade:</TD>
    <TD class="texto"><?php  echo odbc_result($cur, 4);?></TD>
    <TD align="center"><input type="text" class="caixa" size="40" name="new_city" value="<?php  echo $new_city;?>"></TD>
  </TR>
  <TR>
    <TD class="textoBold">CEP :</TD>
    <TD class="texto"><?php  echo odbc_result($cur, 5);?></TD>
    <TD align="center"><input type="text" class="caixa" size="40" name="new_cep" value="<?php  echo $new_cep;?>"></TD>
  </TR>
  <TR>
    <TD class="textoBold">Região :</TD>
    <TD class="texto"><?php  echo odbc_result($cur, 6);?></TD>
     <TD align="left">
      <?php  // Monta a lista de Região
        $sql = "SELECT id, description FROM Region ORDER BY name";
//echo "<pre>$sql</pre>";
        $sel = 0;
	$name = "idRegion";
        $empty = "Selecione uma Região";
        require_once("../../interf/Select.php");
      ?>
     </TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>

  <tr align="center">
    <td colspan="3"><br><input type=button value="Voltar" onClick="this.form.comm.value='back';this.form.submit()" class="sair">
     <INPUT type=submit value="Criar Endosso " class="sair"> </td>
  </tr>
</form>
</table>
