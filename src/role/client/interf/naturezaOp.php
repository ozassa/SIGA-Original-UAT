<br><br>

<FORM action="<?php  echo $root;?>role/client/Client.php#endosso" method="post">
<input type=hidden name="comm" value="alterNatureza">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="tipo" value="<?php  echo $tipo;?>">
<input type=hidden name="back" value="<?php  echo $back;?>">
<a name=endosso></a>
<TABLE border="0" cellSpacing="0" cellpadding="2" width="96%" align="center">
<?php  if($role["client"]){ ?>
  <TR>
    <TD align="center" colspan="2"><H3 align=center>Endosso de Natureza da Operação</H3></TD>
  </TR>
<?php  } ?>
<?php 
include_once("../../consultaCoface.php");
?>
  <TR>
    <TD colspan="2"><P>&nbsp;</P></TD>
  </TR>
  <TR>
    <TD colspan="2" class="bgCinza" align="left">Altera ou inclui os produtos exportados pelo segurado, cujas operações estarão cobertas.</TD>
  </TR>
  <TR>
    <TD colspan="2"><P>&nbsp;</P></TD>
  </TR>
  <TR>
    <TD colspan="2">Instruções:</TD>
  </TR>
  <TR>
    <TD class="texto" valign="top">1-</td>
    <TD class="texto"> Preencha os campos abaixo. Em seguida, clique no botão "Criar Endosso".</TD>
  </TR>
  <TR>
    <TD colspan="2"><P>&nbsp;</P></TD>
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
    <TD colspan="2"><hr></TD>
  </TR>
  <TR>
    <TD colspan="2">&nbsp;</TD>
  </TR>
</TABLE>

<?php  $cur=odbc_exec( $db, "SELECT inf.products, s.description, inf.idSector FROM Inform inf JOIN Sector s ON (s.id = inf.idSector) WHERE inf.id = $idInform");
?>
<TABLE border="0" cellSpacing="0" cellpadding="2" width="96%" align="center">
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
    <TD class="textoBold" width="25%">Setor: </TD>
    <TD class="texto" width="75%"><?php  echo odbc_result($cur, 2);?></TD>
    <TD class="texto">
<?php  // Monta a lista de setor
       $sql = "SELECT id, description FROM Sector ORDER BY description";
       $sel = $field->getDBField("idSector", 16);
       $name = "idSector";
       require_once("../../interf/Select.php");
?>
    </TD>
  </TR>
  <TR>
    <TD class="textoBold" width="25%">Produto:</TD>
    <TD class="texto" width="75%"><?php  echo odbc_result($cur, 1);?></TD>
    <TD class="texto"><input type="text" name="altNat" class="caixa" size="30"></TD>
  </TR>
<input type=hidden name="idSectorOld" value="<?php  echo odbc_result($cur, 3);?>">
<input type=hidden name="naturezaOld" value="<?php  echo odbc_result($cur, 1);?>">
  <TR>
    <TD colspan="3"><P>&nbsp;</P></TD>
  </TR>
</TABLE>


<p align="center"><input type="button" class="servicos" value="Voltar" onClick="this.form.comm.value='back';this.form.submit()">
<INPUT type="button" class="servicos" value="Criar Endosso" onClick="valida(this.form)"> </p>
</form>

<script language=javascript>
  function valida(f){
    if(f.altNat.value == ''){
      verErro('Preencha a nova Natureza da operação');
      f.altNat.focus();
    }else{
      f.submit();
    }
  }
</script>


