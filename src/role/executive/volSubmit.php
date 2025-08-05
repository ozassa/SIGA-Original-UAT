<?php  // verifica se a entrada é válida

if ($role["executive"]){
	$baixapend = 3; // baixa pendência com perfil executivo
}else{
    $baixapend = 2; // mantém status como pendente
}

 $r = odbc_exec(
   $db,
   " UPDATE Inform".
   "  SET     ".
   "      volState = ".$baixapend. "  WHERE id =".$field->getField("idInform")
 );
?>