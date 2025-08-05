<script>
<!--
function seleciona (obj) {
//  verErro(obj.selectedIndex);
  form = obj.form;
  form.submit();
}
// -->
</script>

<script language="JavaScript" src="<?php echo $root;?>scripts/cnpj.js"></script>

<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD class="bgCinza" align="center">Busca de Agencia</TD>
  </TR>
</table> 
<form action="<?php echo $root;?>role/cessao/Cessao.php" method=post>
<input type=hidden name="comm" value="buscaAg">
<div align=center>
<p align=center>Nome:&nbsp;&nbsp;
<input type="text" name=nameBusca size="50" class="caixa"><br>
Código:&nbsp;&nbsp;
<input type="text" name=codBusca size="10" class="caixa"><br>
Banco:&nbsp;&nbsp;
 <?php // Monta a lista de Bancos
   $sql = "SELECT id, name FROM Banco Where tipo = 1 or tipo = 2 ORDER BY name";
   //echo "<pre>$sql</pre>";
   $name = "Banco";
   $empty = "Selecione um Banco";
   require_once("../../interf/Select.php");
 ?>
<input type="submit" value=" OK " name=submit class="servicos"></p>
</div>
<?php if($submit){
  require_once("buscaAg.php");
}

?>
</form>
<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
<tr class="bgAzul">
<td width="10%" class="bgAzul">Código</td>
<td width="60%" class="bgAzul">Nome</td>
<td width="30%" class="bgAzul">Banco</td>
</tr>
<?php if($submit){
    while (odbc_fetch_row($busca)) {
      $i++;
      $nome = odbc_result($busca, 1);
      $codigo = odbc_result($busca,2);	
      $Banco = odbc_result($busca, 3);
      $idAg = odbc_result($busca, 4);
?>
<tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
  <td class="texto"><a href="<?php echo $root;?>role/cessao/Cessao.php?comm=DetAg&idAg=<?php echo $idAg;?>"><?php echo $codigo;?></a></td>
  <td class="texto"><?php echo $nome;?></td>
  <td class="texto"><?php echo $Banco;?></td>
</tr>
<?php } // while
} // if
?>
</table> 

<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
   <TR>
    <TD colspan="2">&nbsp;<br><br><br></TD>
  </TR>
  <TR>
    <TD class="bgCinza" align="center" colspan="2">Cadastro de Agencia</TD>
  </TR>
   <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
  <TR>
    <TD align="center" colspan="2">Preencha os Dados da Agência</TD>
  </TR>
   <TR>
    <TD align="center" class="verm" colspan="2"><br><?if($msgAg){ echo $msgAg; }?></TD>
  </TR>
<form action="<?php echo $root;?>role/cessao/Cessao.php" method="post" name="cadastro" onsubmit="return checa_formulario(this)">
<input type=hidden name="comm"     value="cadAg">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">
  <TR>
    <TD class="textoBold">Banco :</TD>
     <TD>
      <?php // Monta a lista de Bancos
        $sql = "SELECT id, name, tipo FROM Banco WHERE tipo = 2 or tipo = 1  ORDER BY name";
//echo "<pre>$sql</pre>";
        $sel = $idBanco;
	$name = "idBanco";
        $acao = "onChange=seleciona(this)";
        $empty = "Selecione um Banco";
        require_once("../../interf/Select.php");
      ?>
     </TD>
  </TR>
<?php $q = "SELECT tipo FROM Banco WHERE id = $idBanco";
  $c = odbc_exec($db, $q);
  $tipoBanco = odbc_result($c, tipo);
?>
<?php if($tipoBanco == 1){?>
  <TR>
    <TD class="textoBold">Nurim :</TD>
     <TD>
      <?php // Monta a lista de Nurim
        $sql = "SELECT id, name FROM Nurim ORDER BY name";
        //echo "<pre>$sql</pre>";
        $sel = '';
	$name = "idNurim";
        $acao = '';
        $empty = "Selecione um Nurim";
        require_once("../../interf/Select.php");
      ?>
     </TD>
  </TR>
<?php } ?>
   <TR>
    <TD width="20%">Código da Agência (sem dv): </TD>
    <TD width="80%"><input type="text" name="agencia" size="10"></TD>
   </TR>
   <TR>
    <TD>Nome: </TD>
    <TD><input type="text" name="agNome" size="20"></TD>
   </TR>
   <TR>
    <TD>Endereço: </TD>
    <TD><input type="text" name="agEnd" size="45"></TD>
   </TR>
   <TR>
    <TD>Cidade: </TD>
    <TD><input type="text" name="agCid" size="20"></TD>
   </TR>
  <TR>
    <TD class="textoBold">Região :</TD>
     <TD>
      <?php // Monta a lista de UF
        $sql = "SELECT uf, name FROM UF WHERE uf <> 'NA' ORDER BY name";
//echo "<pre>$sql</pre>";
        $sel = '';
	$name = "uf";
        $acao = '';
        $empty = "Selecione uma Região";
        require_once("../../interf/Select.php");
      ?>
     </TD>
  </TR>
   <TR>
    <TD>CNPJ: </TD>
    <TD><input type="text" name="cnpj" size="20" onBlur="valida_cadastro()"></TD>
   </TR>
   <TR>
    <TD>IE: </TD>
    <TD><input type="text" name="agIE" size="20"></TD>
   </TR>
   <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
   <TR>
    <TD align="center" colspan="2"><INPUT class=servicos onclick="this.form.comm.value='voltar';this.form.submit()" type=button value="Voltar">   <input class="servicos" name="botao" type="button" value="Cadastrar" onClick="this.form.comm.value='cadAgSQL';this.form.submit()"> </TD>
  </TR>
</form>
   <TR>
    <TD colspan="2"><br>&nbsp;</TD>
  </TR>
</TABLE>
<!--TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
   <TR>
    <TD colspan="4">&nbsp;</TD>
  </TR>
  <TR>
    <TD class="bgCinza" align="center" colspan="4">Agências Cadastradas</TD>
  </TR>
   <TR>
    <TD colspan="4">&nbsp;</TD>
  </TR>
   <TR>
    <TD class="bgAzul">&nbsp;</TD>
    <TD class="bgAzul">Código </TD>
    <TD class="bgAzul">Banco</TD>
    <TD class="bgAzul">Agência</TD>
   </TR>
<?php $query = "SELECT a.name, a.codigo, b.name FROM Agencia a JOIN Banco b ON (b.id = a.idBanco)";
    $cur = odbc_exec($db, $query);
    while (odbc_fetch_row($cur)) {
      $i++;
      $codigo = odbc_result($cur,2);		
      $banco = odbc_result($cur, 3);
      $nome = odbc_result($cur, 1);
?>

  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td><?php echo $i;?></td>
    <td><?php echo $codigo;?></td>
    <td><?php echo $banco;?></td>
    <td><?php echo $nome;?></td>
  </tr>
<?php } // while
  if ($i == 0) {
?>
  <TR class="bgCinza">
    <TD align="center" colspan=4 class="bgCinza">Nenhum Banco Cadastrado</TD>
  </TR>
<?php }
?>
</table-->
