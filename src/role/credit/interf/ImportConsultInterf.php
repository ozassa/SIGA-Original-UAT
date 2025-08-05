<TABLE width="100%" cellspacing=0 cellpadding=3> 
<p>&nbsp;  </p>
  <TR bgcolor=#cccccc>
    <th align=center><FONT color=#000066 size=2>Importador</FONT></th>
    <th align=center><FONT color=#000066 size=2>País  /  Ci Importador</FONT></th>
    <th align=center><FONT color=#000066 size=2>País</FONT></th>
    <th align=center><FONT color=#000066 size=2>Exposição Total US$ Mil</FONT></th>
  </TR>




<?php  $i=0;
  while(odbc_fetch_row($import)){   
	     $Import		= odbc_result($import, 1);
	     $idpais            = odbc_result($import, 2); 
	     $Cimport           = odbc_result($import, 4); 
	     $Pais              = odbc_result($import, 3);
	     $credit            = odbc_result($import, 5); 
	     $idBuyer		= odbc_result($import, 6);
              $i++;

     	     $credit            = number_format($credit, 2, ",", ".");


?>
<tr <?php echo $i % 2 == 0 ?  "bgcolor = #eaeab4" : "";?>>
 	  <th align=center><FONT face=arial size=2 color=#000066>
	<a href="<?php echo $root;?>role/credit/Credit.php?comm=showBuyers&idBuyer=<?php echo $idBuyer;?>"><?php echo $Import;?></a></font></th>
          <th align=center><FONT face=arial size=2 color=#000066><?php echo  $idpais;?>  /  <?php echo  $Cimport;?></font></th>
          <th align=center><FONT face=arial size=2 color=#000066><?php echo  $Pais;?></font></th>
          <th align=center><FONT face=arial size=2 color=#000066><?php echo  $credit;?></font></th>
          </TR>

<?php  }

?>

  </TABLE>




