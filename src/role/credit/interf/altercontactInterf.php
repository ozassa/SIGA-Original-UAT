<?php  
	$qq = 	"SELECT Contact.id, Contact.idImporter, Contact.idInform, 
		    Contact.name, Contact.tel, Contact.fax, Contact.title, 
		    Contact.email, Contact.notificationForChangeCredit
		FROM Contact 
		WHERE Contact.id = $idContact";
	$cc = odbc_exec($db, $qq);

	if(odbc_fetch_row($cc)){
	     $cargo		= odbc_result($cc, 7);
	     $tel	        = odbc_result($cc, 5); 
	     $fax               = odbc_result($cc, 6); 
	     $Name              = odbc_result($cc, 4);
	     $email             = odbc_result($cc, 8); 
	     $emailCredit       = odbc_result($cc, 9);
	

?>



<body bgColor=#ffffcc leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
 <form  action="<?php echo  $root;?>role/credit/Credit.php" method="post">
<TABLE "width=100%" cellspacing="0" cellpadding="2" border="0" align="center">
    <TR>
      <td>&nbsp;</td>
    </TR>
    <TR>
      <td width=5% class="textoBold">Contato:</td>
      <td><INPUT class="caixa" type=text size=50 name=Name  value=<?php echo  $Name;?>> </td>
    </TR>
    <TR>
      <td class="textoBold">Telefone:</td>
       <td><INPUT class="caixa" type=text size=18 name=tel value=<?php echo  $tel;?>></td>
    </TR>
    <TR>
      <td class="textoBold">Fax:</td>
       <td><INPUT class="caixa" type=text size=18 name=fax value=<?php echo  $fax;?>></td>
    </TR>
    <TR>
      <td class="textoBold">Cargo:</td>
       <td><INPUT class="caixa" type=text size=30 name=cargo value=<?php echo  $cargo;?>></td>
    </TR>
    <TR>
      <td class="textoBold">E-mail:</td>
       <td><INPUT class="caixa" type=text size=40 name=email value=<?php echo  $email;?>></td>
    </TR>
    <TR>							
       <td align=center><input type="checkbox" name=emailCredit value="1" <?php echo $emailCredit == "1" ? checked : "";?> style="LEFT: 6px; TOP: 4px"></td>
       <td class="textoBold">Receber aviso de Alteração de Limite de Crédito</td>
    </TR>


<?php  }
?>
	<P>&nbsp;</P>
	<tr>
	<td>&nbsp;</td>
	<td align=center>
		<input type=hidden name=comm value=alteration>
		<input type=hidden name=idInform value=<?php echo $idInform;?>>	
	         <INPUT name=Reset type=reset class="servicos" value=Cancelar>
	</td>
    </tr>
</form>
</table>

</body>
/*
		<INPUT type="submit"  value="Alterar">&nbsp;&nbsp;&nbsp;
	  	<input type=button onClick="this.form.comm.value='searchContact';this.form.submit()" value="Cancelar"> 
*/





