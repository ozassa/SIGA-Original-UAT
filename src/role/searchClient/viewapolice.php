<?php 
    $sql="SELECT id, name, contrat, state FROM Inform WHERE id='$idclient'";
    $cur = odbc_exec($db, $sql);
    $nome =odbc_result($cur, 2);
    $napolice =odbc_result($cur, 3);
    $idinform=odbc_result($cur, 1);
  $status=odbc_result($cur, 4)
?>
<script language="javascript">
function confirma(c){
 if (confirm('Tem certeza que deseja cancelar?')){
     c.submit();
    return true;
 }else{
   return false;
 }
}
</script>

<FORM id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/searchApolice.php" method="post">
<div align=center>
<table border="0" width="550" cellspacing="1" cellpadding="2">
   <tr>
     <td class="bgAzul">Nome :</td>
   </tr>
   </tr>
     <td bgcolor="#e9e9e9"><?php  echo $nome;?></td>
   </tr>
   <tr>
     <td>&nbsp;</td>
   </tr>
   <tr>
     <td class="bgAzul">Nº CI :</td>
   </tr>
   <tr>
     <td bgcolor="#e9e9e9"><?php 
         if ( empty($napolice) ) {
            echo "--------";
         }else{
            echo $napolice;
         }
     ?></td>
   </tr>
   <tr>
   <td>&nbsp;</td>
   </tr>
   <tr>
   <td>
     <input type="hidden" value="cancelar"  name="comm">
     <input type="hidden" value=<?php echo $idinform;?>  name="idinform">
     <input type="hidden" value=<?php echo $status;?> name="status_at">
   <input type="submit" value="voltar " name="voltar"
     class="servicos" onClick="this.form.comm.value='back';this.form.submit()">
     <input type="button" value="cancelar " name="cancelar" class="servicos" onclick="confirma(this.form)">
   </td>
</tr>
</table>
</div>
</form>
