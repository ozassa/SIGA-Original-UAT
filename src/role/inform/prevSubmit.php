<?php

  if ($hc_cliente == "N" && ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')) {
    $novo_estatus = "2";
  } else {
    if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') {
      $novo_estatus = "2";
    } else {
      $novo_estatus = "3";
    }
  }

 $cur=odbc_exec(
    $db,
    " UPDATE Inform".
    "  SET     ".
    "      ace =".$field->getNumField("ace").",".
    "      proex =".$field->getNumField("proex").",".
    "      financState = ". $novo_estatus.
    "  WHERE id = $idInform"
  );
?>