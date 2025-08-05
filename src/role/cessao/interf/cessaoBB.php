<script language="javascript">
function seleciona(obj){
//  verErro(obj.selectedIndex);
  form = obj.form;
  if(obj.selectedIndex != 1){
    form.formfocus.value='';
    form.fieldfocus.value='';
  }
  form.submit();
}

function check(cadastro){
  var s = limpa_string(cadastro.agCNPJ.value);
  if(cadastro.agCNPJ.value == "" ||
     (s.length == 11 && !valida_CPF(cadastro.agCNPJ.value)) ||
     (s.length == 14 && !valida_CGC(cadastro.agCNPJ.value)) ||
     (s.length != 11 && s.length != 14)){
    verErro("Por Favor, Preencha corretamente o CNPJ");
    cadastro.agCNPJ.focus();
    cadastro.agCNPJ.select();
    return false;
  }
  cadastro.submit();
}
</script>

<script language="javascript" src="<?php  echo $root;?>scripts/cnpj.js"></script>

<?php  $q = "SELECT id, name FROM Banco WHERE id = $idBanco";
$c = odbc_exec($db, $q);
$idBanco = odbc_result($c, 1);
$bancoName = odbc_result($c, 2);
?>

<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD align="center" colspan="2"><h3>Cessão de Direito</h3></TD>
  </TR>
   <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
   <TR>
    <TD colspan="2" align="center"><?php  echo $bancoName;?></TD>
  </TR>
   <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
  <TR class="bgAzul">
    <TD align="center" class="bgAzul" colspan="2">Preencha os Dados</TD>
  </TR>
   <TR>
    <TD align="center" class="verm" colspan="2"><br><?if($msgAg){ echo $msgAg; }?></TD>
  </TR>
<form action="<?php  echo $root;?>role/cessao/Cessao.php" method="post">
<input type=hidden name="comm"     value="cessaoBB">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="idBanco" value="<?php  echo $idBanco;?>">
<input type=hidden name="tipoBanco"  value="3">
   <TR>
    <TD>Nome do Banco: </TD>
    <TD>
<?php  // Monta a lista de Banco
        $sql = "SELECT id, name FROM Banco WHERE tipo = 3 ORDER BY name";
        $sel = $idBanco;
	$name = "idBanco";
        $acao = "onChange=seleciona(this)";
	$empty = "Selecione o Banco";
        require_once("../../interf/Select.php");
?>
    </TD>
   </TR>
<?php  if($tipoBanco == 3){?>
   <TR>
    <TD width="20%">Código da Agência (sem dv): </TD>
    <TD width="80%"><input type="text" name="agencia" value="<?php  echo $agencia;?>" size="10" class="caixa"></TD>
   </TR>
   <TR>
    <TD>Nome da Agência: </TD>
    <TD><input type="text" name="agNome" value="<?php  echo $agNome;?>" size="20" class="caixa"></TD>
   </TR>
   <TR>
    <TD>Endereço da Agência: </TD>
    <TD><input type="text" name="agEnd" value="<?php  echo $agEnd;?>" size="45" class="caixa"></TD>
   </TR>
   <TR>
    <TD>Cidade: </TD>
    <TD><input type="text" name="agCid" value="<?php  echo $agCid;?>" size="20" class="caixa"></TD>
   </TR>
  <TR>
    <TD class="textoBold">Região :</TD>
     <TD>
<?php  // Monta a lista de Região
      $sql = "SELECT id, description FROM Region ORDER BY name";
   //echo "<pre>$sql</pre>";
   $sel = $idRegion;
   if(! $sel){
     $sel = 0;
   }
   $name = "idRegion";
   $acao = '';
   $empty = "Selecione uma Região";
   require_once("../../interf/Select.php");
      ?>
     </TD>
  </TR>
   <TR>
    <TD>CNPJ: </TD>
    <TD><input type="text" name="agCNPJ" value="<?php  echo $agCNPJ;?>" size="20" class="caixa"></TD>
   </TR>
   <TR>
    <TD>IE: </TD>
    <TD><input type="text" name="agIE" value="<?php  echo $agIE;?>" size="20" class="caixa"></TD>
   </TR>
   <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
   <TR>
    <TD align="center" colspan="2">
<INPUT class=servicos onclick="this.form.comm.value='cessao';this.form.submit()" type=button value="Voltar">
<input class="sair" name="botao" type="button" value="Continuar" onClick="this.form.comm.value='selImp';check(this.form)">
<!--input type="submit" value="Continuar" class="servicos"-->
</TD>
  </TR>
<?php }?>
</form>
   <TR>
    <TD colspan="2"><br>&nbsp;</TD>
  </TR>
</TABLE>
