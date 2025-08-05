<?php  if ($role["executive"]){
	$baixapend = 3; // baixa pendência com perfil executivo
  }else{
    $baixapend = 2; // mantém status como pendente
  }

  $cur=odbc_exec(
      $db,
      " UPDATE Inform".
      "  SET     ".
      "      lostState = ".$baixapend. "  WHERE id = $idInform"
    );
?>