<?php  $query = "INSERT INTO Contact (idInform, name, tel, fax, email, title, notificationForChangeCredit) 
			values ($idInform, $Name, $tel, $fax, $email, $cargo, $emailCredit)";


	$contact = odbc_exec($db,$query);
?>