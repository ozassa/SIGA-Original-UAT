<?php 

	if ($hc_cliente == "N" && ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')) {
	  $novo_estatus = "2";
  } else {
  	if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') {
		  $novo_estatus = "2";
		} else {
			$novo_estatus = "3";
		}
	}

  $qry = " UPDATE Inform  SET segState = ".$novo_estatus."  WHERE id =".$field->getField("idInform");
  $cur=odbc_exec($db,$qry);	
	
?>