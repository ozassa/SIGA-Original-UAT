<?php  $cur=odbc_exec(
    $db,
    " UPDATE Inform".
    "  SET     ".
    "      financState = ".($role["client"] ? "2" : "3").
    "  WHERE id = $idInform"
  );
?>