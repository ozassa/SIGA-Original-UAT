
<BODY bgColor=#ffffcc>
<TABLE width="100%" cellspacing=0 cellpadding=3> 
  <TR bgcolor=#cccccc>
	<form  action=<?php echo  $root;?>role/credit/Credit.php method="post">
	<input type="hidden" name=comm value=importconsult>
	<th colspan=5 align=center><FONT color=#000066 size=2>Busca</FONT></th>
  </TR>
  <TR>
    <th width=20% align=left><FONT color=#000066 size=2>Razão Social:</FONT></th>
    <th align=left><INPUT type="text" name=importconsult>&nbsp;&nbsp;<INPUT type="submit" value="OK" name=submit></th>
  </TR>
</form>
</TABLE> 


<?php  if($submit){
	       require_once("../credit/importconsult.php"); 
	       require_once("ImportConsultInterf.php");  //formata a saida
	}
?>
  <P>&nbsp;</P>
</BODY>

