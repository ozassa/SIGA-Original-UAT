<?php  // verifica se a entrada � v�lida

if ($role["executive"]){
	$baixapend = 3; // baixa pend�ncia com perfil executivo
}else{
    $baixapend = 2; // mant�m status como pendente
}

 $r = odbc_exec(
   $db,
   " UPDATE Inform".
   "  SET     ".
   "      volState = ".$baixapend. "  WHERE id =".$field->getField("idInform")
 );
?>