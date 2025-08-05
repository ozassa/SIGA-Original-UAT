<?php

require_once("../rolePrefix.php");

    $q = "UPDATE Inform SET idConsultor = '$idConsultor' WHERE id = '$idInform'";
    $cur = odbc_exec($db, $q);


?>
