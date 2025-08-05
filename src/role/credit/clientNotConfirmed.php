<?php  $query = "SELECT DISTINCT Inform.name, Inform.contrat, MailConfirm.sentDate, MailConfirm.type
	  FROM MailConfirm, Inform, Importer
	  WHERE MailConfirm.idImporter = Importer.id AND 
	  Importer.idInform = Inform.id AND Importer.state <> 2 AND MailConfirm.state = 1";
$clientnotconfirmed = odbc_exec($db,$query);

?>
