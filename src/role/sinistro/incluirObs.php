<?php $q = "INSERT INTO SinistroObs (idSinistro, name, obs, date) VALUES ($idSinistro, '$name', '$obs', getdate())";
  	$cur = odbc_exec($db, $q);

?>