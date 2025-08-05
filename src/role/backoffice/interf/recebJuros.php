<?php
// Alterado Hicom (Gustavo) - 29/12/04 - Alteração do processo de solicitação de cobertura para juros de mora -->

$sql =   "SELECT 		name  ".
         "FROM 		Inform ".
			"WHERE 		id = $idInform ";
$cur=odbc_exec($db,$sql);
$name = odbc_result($cur,"name");

?>
<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD align="center">&nbsp;</TD>
  </TR>
  <TR>
    <TD align="center"><?php echo $name;?></TD>
  </TR>
  <TR>
    <TD align="center">&nbsp;</TD>
  </TR>
  <TR>
    <TD>
     <ul>
      <li><a href="<?php echo $root;?>role/backoffice/condespjurosmora.php?idInform=<?php echo $idInform;?>" target=_blank>Condições Especiais de Juros de Mora</a>
      <li><a href="<?php echo $root;?>role/backoffice/cartaJurosMora.php?idInform=<?php echo $idInform;?>" target=_blank>Carta de Encaminhamento</a>

		<!-- Alterado Hicom (Gustavo) - adicionei o link abaixo -->
		<li><a href="<?php echo $root;?>role/endosso/EndJurMora.php?idInform=<?php echo $idInform;?>&n_Endosso=<?php echo $n_Endosso;?>" target=_blank>Endosso de Cobertura para Juros de Mora</a>
		<!-- fim alterado -->
		
     </ul>
  
    </TD>
  </TR>
</TABLE>


<form action="<?php echo $root;?>role/backoffice/Backoffice.php"  method="get">
<input type=hidden name=idInform value="<?php echo $idInform;?>">
<input type=hidden name=idJuros value="<?php echo $idJuros;?>">
<input type=hidden name=idNotification value="<?php echo $idNotification;?>">
<input type=hidden name=usuario value="<?php echo $userID;?>">
<input type=hidden name="comm" value="concluiJuros">
<P align="center"><input class="sair" type="button" value="Voltar" onClick="this.form.comm.value='back';this.form.submit()"> <input type="submit" value="Concluir" class="sair">
</form>
