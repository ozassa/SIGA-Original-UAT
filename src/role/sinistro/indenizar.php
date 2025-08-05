<?php $q = "UPDATE Sinistro SET status = 8, indenizacao = $valorInd, vmi = $vmi WHERE id = $idSinistro";
	
	$cur = odbc_exec($db, $q);
       // echo "<pre>$q</pre>";
		
	if (!$cur) {
		$msg = "Problemas na atualização da base";
	} else {
             $var=odbc_exec($db, "SELECT Inform.name, Importer.name FROM Inform, Importer Where Inform.id = $idInform AND Importer.id = $idImporter");
             $name = odbc_result($var, 1);
             $nameI = odbc_result($var, 2);

             $r = $notif->doneRole($idNotification, $db);
             if ($r){
                 $r = $notif->pagInd($idNotification, $idImporter, $idInform, $idInform, $db, $name, $idSinistro, $nameI);
                 if (!$r) {
                    $msg = "problemas na criação da notificação";
                    $ok = false;
                 }
             } else {
                    $msg = "problemas na criação da notificação";
                    $ok = false;
             }
             $q = "INSERT INTO SinistroObs (idSinistro, name, date, obs) VALUES ($idSinistro, '$user->name', getdate(),  'Indenização Aprovada')";
  	     $obs = odbc_exec($db, $q);
       	}



?>