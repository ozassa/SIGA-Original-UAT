<?php  $control = false;
	
	$cookie = $field -> getField ("cookie");

	$query	= "UPDATE MailConfirm SET state = 2 WHERE cookie = '$cookie'";
	$cur	= odbc_exec ($db, $query);

	$query = "SELECT type
		  FROM MailConfirm
		  WHERE cookie = '1'";
	$cur   = odbc_exec ($db, $query);

	if (odbc_fetch_row ($cur)) {
		$type = odbc_result ($cur, 1);
	}
		

?>