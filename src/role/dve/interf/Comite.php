<?php
if($client){
  echo "<center><h3>$title</h3></center>";
}else{
  require_once("../../../../site/includes/sbce.css");
}

echo "Segurado: $name<br>";
echo "Apólice n°: 062". sprintf("%06d", $apolice). "<br>";
echo "Vigência: $start à $end<br>";
echo "Período de Declaração: $inicio à $fim ($num". "ª DVE)<br>";

echo "<p>Comentários:<br>";
if($num_comments > 0){
?>
<table border=1 width=600 cellpadding=3>
<tr class=bgCinza>
<th width=70 class=textoBold>Data</th>
<th class=textoBold>Texto</th>
</tr>
<?php
   $i = 0;
   while(odbc_fetch_row($cur)){
     $data = ymd2dmy(odbc_result($cur, 1));
     $texto = odbc_result($cur, 2);
     echo "<tr". ($i % 2 ? ' bgcolor=#eaeab4' : '').
       "><td align=center class=texto>$data</td>".
       "<td class=texto>$texto</td></tr>";
     $i++;
   } // while
?>
</table>
<?php
}else{
  echo "Nenhum comentário para esta DVE<br>";
}
?>
<br>
<div align=center>
<form action="<?php echo $root;?>role/dve/Dve.php">
<input type="hidden" name="comm" value="comite">
<input type="hidden" name="dve_action" value="include_comment">
<input type="hidden" name="idDVE" value=<?php echo $idDVE;?>>
<input type="hidden" name="idNotification" value=<?php echo $idNotification;?>>
<table border=0 width=600>
<tr><td align=left>
Inclusão de comentários:<br>
<textarea name=texto rows=5 cols=82></textarea>
</td></tr>
<tr><td align=center>
<input type="submit" value="Incluir" class="Sair">
</td></tr>
</table>
</form>

<?php
if($sentDate){
  echo "DVE enviada em $sentDate<br>";
}
?>

<form action="<?php echo $root;?>role/dve/Dve.php">
<input type="hidden" name="comm" value="comite">
<input type="hidden" name="dve_action">
<input type="hidden" name="idDVE" value=<?php echo $idDVE;?>>
<input type="hidden" name="idNotification" value=<?php echo $idNotification;?>>
<table border=0 cellspacing=15 width=600>
<tr>
<td align=center>
<input type=button class="Sair" value="Cancelar Apólice"
onClick="if(confirm('Cancelar apólice?')) { this.form.dve_action.value='cancel'; this.form.submit();}">
</td>
<?php if($sentDate){ ?>
<td align=center>
<input type=button class="Sair" value="Reativar Apólice" onClick="this.form.dve_action.value='reactivate'; this.form.submit()">
</td>
<?php  } ?>
<td align=center>
<input type=button class="Sair" value="Emitir Perda de Garantia" onClick="this.form.comm.value=''; this.form.submit()">
</td>
</tr>
</table>
</form>
</div>

<?php
if($msg){
  echo "<center><font color=#ff0000>$msg</font></center>";
}
?>
