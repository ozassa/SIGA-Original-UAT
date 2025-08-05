<?php 



    $q = "UPDATE Sinistro SET cause = '$cause', nameRes = '$nameRes', position = '$position', tel = '$tel', email = '$email', status = 2 WHERE id = $idSinistro";
	
	$cur = odbc_exec($db, $q);
        //echo "<pre>$q</pre>";
		
	if (!$cur) {
		$msg = "Problemas na atualização da base";
	} else {
             $var=odbc_exec($db, "SELECT Inform.name, Importer.name FROM Inform, Importer Where Inform.id = $idInform AND Importer.id = $idImporter");
             $name = odbc_result($var, 1);
             $nameI = odbc_result($var, 2);

             $r = $notif->newSinistro($idInform, $name, $idInform, $db, $idImporter, $idSinistro, $nameI);
             if (!$r) {
         	$msg = "problemas na criação da notificação";
        	$ok = false;
             }
       	}



?>