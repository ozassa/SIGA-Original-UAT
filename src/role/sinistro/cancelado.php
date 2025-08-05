<?php $q = "UPDATE Sinistro SET status = 5 WHERE id = $idSinistro";
	
	$cur = odbc_exec($db, $q);
       // echo "<pre>$q</pre>";
		
	if (!$cur) {
		$msg = "Problemas na atualização da base";
	} else {
             $r = $notif->doneRole($idNotification, $db);
             $q = "INSERT INTO SinistroObs (idSinistro, name, date, obs) VALUES ($idSinistro, '$user->name', getdate(),  'Sinistro Cancelado')";
  	     $obs = odbc_exec($db, $q);
       	}



?>