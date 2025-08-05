<?php

error_reporting(E_ALL);
require_once ("rolePrefix.php");
	      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
	      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
echo $cur;
?>