<?php  require_once ("../../../../site/includes/sbce.css");

$cur = odbc_exec($db,
		 "SELECT name, startValidity, endValidity, prMin, txRise, 
                  nProp, i_Seg, idAnt, prodUnit, prMTotal, codProd, warantyInterest
                  FROM Inform WHERE id = $idInform");
if(odbc_fetch_row($cur)){
  $cliente = odbc_result($cur, 'name');
  $ini_vig = odbc_result($cur, 'startValidity');
  $fim_vig = odbc_result($cur, 'endValidity');
  $txRise = odbc_result($cur, 'txRise');
  $nProp = odbc_result($cur, 'nProp');
  $idSeg = odbc_result($cur, 'i_Seg');
  $idAnt = odbc_result($cur, 'idAnt');
  $prod = odbc_result($cur, 'prodUnit');
  $premio_min = odbc_result($cur, 'prMin') * (1 + $txRise) * (odbc_result($cur, 'warantyInterest') ? 1.04 : 1);
  if(odbc_result($cur, 'codProd') == 1){
    $premio_min = odbc_result($cur, 'prMTotal');
  }
}
// if(! $idSeg){
//   $idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
// }
// if(! $nProp){
//   $x = odbc_exec($dbSisSeg,
// 		 "select n_Prop from Proposta where i_Seg=$idSeg order by n_Prop desc");
//   if(odbc_fetch_row($x)){
//     $nProp = odbc_result($x, 1);
//   }
// }
// $y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$idSeg");
// if(odbc_fetch_row($y)){
//   $apolice = sprintf("062%06d", odbc_result($y, 1));
//   if($prod != 62){
//     $apolice .= "/$prod";
//   }
// }
?>

<br><br>
<FORM action="<?php   echo $root;?>role/endosso/Endosso.php" method="post">
<input type=hidden name="comm" value="tarifacao">
<input type=hidden name="idInform" value="<?php   echo $idInform;?>">
<input type=hidden name="tipo" value="<?php   echo $tipo;?>">
<input type=hidden name="back" value="<?php   echo $back;?>">
<input type=hidden name="prMin" value="<?php   echo $premio_min;?>">

<a name=endosso></a>
<TABLE border="0" cellSpacing="0" cellpadding="2" width="100%" align="center">
  <TR>
    <TD colspan="2"><P>&nbsp;</P></TD>
  </TR>
  <TR>
    <TD class="textoBold" width="35%">Cliente: </TD>
    <TD class="texto"><?php   echo $cliente;?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Vigência: </TD>
    <TD class="texto"><?php   echo substr($ini_vig, 8, 2). "/". substr($ini_vig, 5, 2). "/". substr($ini_vig, 0, 4);?>
    a <?php   echo substr($fim_vig, 8, 2). "/". substr($fim_vig, 5, 2). "/". substr($fim_vig, 0, 4);?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Prêmio Mínimo: </TD>
    <TD class="texto">US$ <?php   echo number_format(($premio_min), 2, ',', '.');?></TD>
  </TR>
  <TR>
    <TD class="textoBold">Motivo: </TD>
    <TD class="texto"><textarea cols="30" rows="2" name="motivo"></textarea></TD>
  </TR>
  <TR>
    <td align=center colspan=2><br>
    <input type="button" class="servicos" value="Voltar" onClick="goback(this.form)">
    <INPUT type="submit" class="servicos" value="Solicitar Tarifação">
    </td>
  </TR>
</TABLE>
</form>

<script language=javascript>
    function goback(f){
      f.action = '<?php   echo  $root;?>role/client/Client.php';
      f.comm.value = 'back';
      f.submit();
    }
</script>

