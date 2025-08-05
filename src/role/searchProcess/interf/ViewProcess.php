<form action="<?php echo $root;?>role/searchProcess/SearchProcess.php" method=post>

      <p align=center>Nome:&nbsp;&nbsp;<input type="text" name=name size="60" tabindex="1">&nbsp;&nbsp;<input type="submit" value=" OK " class="servicos" align=center name=submit tabindex="2"></p>
</form>

<?php if($submit)
  {
   require_once("../searchMachine/SearchMachine.php");
  }
?>

<?php // apresentacao do resultado
?>
<table width="100%"  cellspacing="0" cellpadding="2" align="center">
  <tr  class="bgAzul">
    <td width="100%">Cliente</td>
    <td>Novo</td>
    <td>Preenc.</td>
    <td>Valid.</td>
    <td>Analis.</td>
    <td>Alter.</td>
    <td>Tarif.</td>
    <td>Prop.</td>
    <td>Conf.</td>
    <td>Apól.</td>
    <td>Canc.</td>
    <td>Enc.</td>	
  </tr>
<?php if($submit)
  {
    require_once("list.php");
  }
 
?>


</table> 
