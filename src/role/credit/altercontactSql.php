<?php  if ($idContact)
	$query ="UPDATE Contact
			SET name = $Name, tel = $tel, fax = $fax, email = $email, title = $cargo,
			 notificationForChangeCredit = $emailCredit, userChangeContact=$userID;
			where id = $idContact";
  else 
	$query ="UPDATE Inform
			SET contact = $Name, tel = $tel, fax = $fax, emailContact = $email, ocupationContact = $cargo,
			where id = $idInform";

 echo "<pre>$query</pre>";
 $cc = odbc_exec($db,$query);


?>