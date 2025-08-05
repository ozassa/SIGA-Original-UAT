<script language="JavaScript" src="<?php echo $root;?>scripts/cnpj.js"></script>

<?php function arruma_cnpj($c){
  if(strlen($c) == 14 && preg_match("([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})", $c, $v)){
    return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
  }
}
  $cur=odbc_exec($db,
	"SELECT b.name, a.codigo, a.name, a.endereco, a.cidade, a.uf, a.cnpj, a.ie, a.idNurim
	FROM Agencia a 
        JOIN Banco   b ON (b.id = a.idBanco)
	WHERE a.id = $idAg");

  $cnpj = arruma_cnpj(odbc_result($cur, 7));
  $region = odbc_result($cur,6);


?>
<form action="<?php echo $root;?>role/cessao/Cessao.php" method="post" name="cadastro">
<input type=hidden name="comm">
<input type=hidden name="idAgencia" value="<?php echo $idAg;?>">
<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD class="textoBold">Banco :</TD>
    <TD class="texto"><?php echo odbc_result($cur,1);?>&nbsp; </TD>
  </TR>
<?php if(odbc_result($cur,9)){ 
   $idNurim = odbc_result($cur,9);
   $var=odbc_exec($db, "SELECT name FROM Nurim WHERE id = $idNurim");
?>
   <TR>
    <TD>Nurim: </TD>
    <TD class="texto"><?php echo odbc_result($var,1);?>&nbsp;</TD>
   </TR>
<?php }?>
   <TR>
    <TD width="20%">Código da Agência: </TD>
    <TD width="80%" class="texto"><input class="caixa" type = "text" name="codigo" size="10" value="<?php echo odbc_result($cur,2);?>"onFocus="blur()">&nbsp;</TD>
   </TR>
   <TR>
    <TD>Nome: </TD>
    <TD class="texto"><input class="texto" type="text" name="nome" size="50" value="<?php echo odbc_result($cur,3);?>">&nbsp;</TD>
   </TR>
   <TR>
    <TD>Endereço: </TD>
    <TD class="texto"><input class="texto" type="text" name="endereco" size="65" value="<?php echo odbc_result($cur,4);?>">&nbsp;</TD>
   </TR>
   <TR>
    <TD>Cidade: </TD>
    <TD class="texto"><input class="texto" type="text" name="cidade" size="30" value="<?php echo odbc_result($cur,5);?>">&nbsp;</TD>
   </TR>
  <TR>
    <TD class="textoBold">Região :</TD>
    <TD class="texto">
      <?php // Monta a lista de Região
        $sql = "SELECT uf, name FROM UF WHERE uf <> 'NA' ORDER BY name";
        //echo "<pre>$sql</pre>";

        $sel = strtoupper(trim($region));
        $name = "uf";
        $acao = '';
        $empty = "Selecione uma Região";
        require_once("../../interf/Select.php");
      ?>
<!--input class="texto" type="text" name="region" size="30" value="<?php echo odbc_result($cur,6);?>">&nbsp;</TD-->
  </TR>
   <TR>
    <TD>CNPJ: </TD>
    <TD class="texto"><input class="texto" type="text" name="cnpj" size="20" value="<?php if($cnpj){ echo $cnpj; }else{ echo odbc_result($cur, 7);}?>" onBlur="valida_cadastro()"></TD>
   </TR>
   <TR>
    <TD>IE: </TD>
    <TD class="texto"><input class="texto" type="text" name="ie" size="20" value="<?php echo odbc_result($cur,8);?>">&nbsp;</TD>
   </TR>
   <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
   <TR>
    <TD align="center" colspan="2"><INPUT class=servicos onclick="this.form.comm.value='cadAg';this.form.submit()" type=button value="Voltar"> <INPUT class=servicos onclick="this.form.comm.value='atuAg';this.form.submit()" type=button value="Atualizar"></TD>
  </TR>
   <TR>
    <TD colspan="2"><br>&nbsp;</TD>
  </TR>
</TABLE>
</form>
