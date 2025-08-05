<?php 
$update = "update Inform set Obs = '$obs' where id = $idInform";
if (odbc_exec($db, $update)) {
	$msg = "Obs alterado com sucesso.";
}else{
	$msg = "Erro em alterar a obs ";
}

?>
