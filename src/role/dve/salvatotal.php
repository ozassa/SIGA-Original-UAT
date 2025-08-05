<?php //Alterado HiCom mes 04

//echo "-------------------------------------" . str_replace (",", ".", conserta($totalEmbarcado));
//die();

$state = odbc_result(odbc_exec($db, "select state from DVE where id=$idDVE"), 1);
$x = odbc_exec($db,
	       "update DVE set total2='".
	       str_replace (",", ".", conserta($totalEmbarcado)).
	       ($state == 2 ? "', state=3" : "'").
	       " where id=$idDVE");
if(! $x){
  $msg = 'Erro ao atualizar DVE';
}else{
  $msg = 'Valor Total Embarcado atualizado';
}
?>
