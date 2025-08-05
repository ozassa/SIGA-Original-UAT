 
<BODY bgColor=#ffffcc>
  <TABLE align=center cellspacing=7 cellpadding=3> 
     <TR>
       <th align=middle><FONT color=#000066 size=2>Data (dd/mm/aaaa)</FONT></th>
	<form action="<?php echo  $root;?>role/credit/Credit.php" method=post>
        <input type=hidden name=comm value=SearchReplyCoface>
        <td align=left><input type="text" name=searchDate size="50" tabindex="1"></td>
        <td><INPUT type=submit value="OK" tabindex="2" name=submit></td>
	</form>
     </TR>
	</font></TABLE>  
	<?php echo $msgERRO?>
<?php  if($submit or $searchDate)
 {
	require_once("../credit/searchReplyCoface.php"); //motor SQL
 }
?>

<?php  // apresentacao do resultado
?>

       <TABLE width="100%" cellspacing=0 cellpadding=5>
        <TR bgcolor=#cccccc>
          <th align=left><FONT color=#000066 size=2>Respostas <?php echo $searchDate;?></FONT></th>
        </TR>
	<tr><br></tr>
        <TR>

<?php  if($submit or $searchDate)
  {
             require_once("ReplyCoface.php");  //formata a saida
  }
?>   
        </TR>

	<br>
	<table>
        <TR align=center>     	
        </TR>
	</table>


