<?php  if ($role["executive"]){
	$baixapend = 3; // baixa pendência com perfil executivo
  }else{
    $baixapend = 2; // mantém status como pendente
  }


  $cur=odbc_exec(
    $db,
    " UPDATE Inform".
    "  SET     ".
    "      segState = ".$baixapend. "  WHERE id =".$field->getField("idInform")
  );
?>