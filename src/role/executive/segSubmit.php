<?php  if ($role["executive"]){
	$baixapend = 3; // baixa pend�ncia com perfil executivo
  }else{
    $baixapend = 2; // mant�m status como pendente
  }


  $cur=odbc_exec(
    $db,
    " UPDATE Inform".
    "  SET     ".
    "      segState = ".$baixapend. "  WHERE id =".$field->getField("idInform")
  );
?>