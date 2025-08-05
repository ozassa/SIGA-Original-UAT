      <TABLE width="100%" cellspacing=0 cellpadding=0>
        <TR>
          <td align=left>
	<br>
<?php  if(odbc_fetch_row($c)){
		$nameCl = odbc_result($c, 1);
		$CiCl = odbc_result($c, 2);
	}
?>
<?php
  include_once("../../consultaCoface.php");
?>
            <P><FONT color=#000066 size=3><?php echo $nameCl;?> Ci=<?php echo $CiCl;?></FONT></P>
          </td>
        </TR>
      </TABLE>
      <TABLE width="100%" cellspacing=0 cellpadding=6>
         <TR bgcolor=#cccccc>
          <th align=middle><FONT color=#000066 size=2>País/Ci <?php echo $nomeEmp; ?></FONT></th>
          <th align=middle><FONT color=#000066 size=2>Nome</FONT></th>
          <th align=middle><FONT color=#000066 size=2>Validade<br>a partir de</FONT></th>
          <th align=middle><FONT color=#000066 size=2>País</FONT></th>
          <th align=middle><FONT color=#000066 size=2>Per/Emb(dias):</font></th>
          <th align=middle><FONT color=#000066 size=2>Prz./Pag.(dias):</font></th>
          <th align=middle><FONT color=#000066 size=2>Crédito Solicitado<br>(U$ mil)</FONT></th>
          <th align=middle><FONT color=#000066 size=2>Crédito Concedido<br>(U$ mil)</FONT></th>
        </TR>
<?php  $query = "SELECT Importer.przPag, Importer.periodicity, Importer.name, 
		    Importer.c_Coface_Imp, Importer.limCredit, Country.code, 
		    Country.name, Importer.id
		FROM Importer, Country, Inform
		WHERE Importer.idCountry = Country.id AND 
		    Importer.idInform = Inform.id AND Inform.id = $idInform 
		AND (Importer.state <> 2 OR Importer.state <> 1 OR Importer.state <> 14 OR Importer.state <> 15)";
		
	$cur = odbc_exec($db, $query);

	$i = 1;
	
	while(odbc_fetch_row($cur)){
		$importerPrzPag      = odbc_result($cur, 1);
		$importerPeriodicity = odbc_result($cur, 2);
		$importerName        = odbc_result($cur, 3);
		$importerCoface      = odbc_result($cur, 4);
		$importerLimCred     = odbc_result($cur, 5);
		$CountryCode         = odbc_result($cur, 6);
		$CountryName         = odbc_result($cur, 7);
		$importerId          = odbc_result($cur, 8);

		$importerLimCred = number_format($importerLimCred, 2, ",", ".");

		$q = "SELECT credit, stateDate
			FROM ChangeCredit
			WHERE idImporter = $importerId
			ORDER BY stateDate DESC";

		$c = odbc_exec ($db, $q);

		if (odbc_fetch_row ($c)) {
			$ChangeCreditCredit = odbc_result ($c, 1);
			$ChangeCreditDate   = odbc_result ($c, 2);
		}

		$i++;

		$ChangeCreditCredit = number_format($ChangeCreditCredit, 2, ",", ".");

		$ChangeCreditDate = substr($ChangeCreditDate, 8, 2)."/".substr($ChangeCreditDate, 5, 2)."/".substr($ChangeCreditDate, 0, 4);

?>

        <TR>
          <td align=middle><FONT color=#000066 size=2><?php echo $CountryCode;?>/<?php echo $importerCoface;?></FONT></td>
          <td align=middle><FONT color=#000066 size=2><?php echo $importerName;?></FONT></td>
          <td align=middle><FONT color=#000066 size=2><?php echo $ChangeCreditDate;?></FONT></td>
          <td align=middle><FONT color=#000066 size=2><?php echo $CountryName;?></FONT></td>
          <td align=middle><FONT color=#000066 size=2><?php echo $importerPeriodicity;?>100</FONT></td>
          <td align=middle><FONT color=#000066 size=2><?php echo $importerPrzPag;?></FONT></td>
          <td align=middle><FONT color=#000066 size=2><?php echo $importerLimCred;?></FONT></td>
          <td align=middle><FONT color=#000066 size=2><?php echo $ChangeCreditCredit;?></font></td>
<?php  }
?>
        </TR>
        <TR>
          <td>&nbsp;</td>
        </TR>
        <TR>
          <td align=middle colspan=10><FONT color=red size=3><i><b>Estudo Finalizado</b></i></FONT></td>
        </TR>
        <TR>  
          <td align=middle colspan=10>
        	<form action="<?php echo $root;?>/role/credit/Credit.php" method=post>
			<INPUT type=submit value=OK> </td>
			<input type=hidden name=idInform value=<?php echo $idInform;?>>	
			<input type=hidden name=comm value=done >

	        </form>



<?php echo $msg;?>
        </TR>    
      </TABLE>
    </td>
  </tr>
  </table></TABLE> 
