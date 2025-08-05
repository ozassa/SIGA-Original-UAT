<?php  if(! function_exists('ymd2dmy')){
  // converte a data de yyyy-mm-dd para dd/mm/yyyy
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}
if($done){
  $notif->doneRole($idNotification, $db);
}else{
  $x = odbc_exec($db, "select name, contrat from Inform where id=$idInform");
  $name = odbc_result($x, 1);
  $ci = odbc_result($x, 2);

  $x = odbc_exec($db,
		 "select i.id, i.name, i.c_Coface_Imp, c.name ".
		 "from Importer i join Country c on c.id=i.idCountry ".
		 "where i.idInform=$idInform order by i.name");
}
?>
