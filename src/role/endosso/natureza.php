<?php $c = odbc_exec($db,
	       "select i.name, i.products, s.description,
                i.startValidity, i.endValidity, i.contrat, i.i_Seg, i.prodUnit
                from Inform i JOIN Sector s ON (s.id = i.idSector) where i.id=$idInform");
if(odbc_fetch_row($c)){
  $name = odbc_result($c, 1);
  $products = odbc_result($c, 2);
  $sector = odbc_result($c, 3);
  $ini_vig = odbc_result($c, 4);
  $fim_vig = odbc_result($c, 5);
  $contrat = odbc_result($c, 6);
  $iSeg = odbc_result($c, 7);
  $prod = odbc_result($c, 8);
}

$c = odbc_exec($db, "select bornDate, solicitante, idUser from Endosso where id=$idEndosso");
if(odbc_fetch_row($c)){
  $bornDate = odbc_result($c, 1);
//   switch(odbc_result($c, 2)){
//   case 1:
//     $solicitante = 'BackOffice'; break;
//   case 2:
//     $solicitante = 'Cliente'; break;
//   }
  $x = odbc_exec($db, "select name from Users where id=". odbc_result($c, 3));
  $solicitante = trim(odbc_result($x, 1));
  if(! $solicitante){
    $solicitante = 'Cliente';
  }
}else{
  $inativo = 1;
  return;
}

$c = odbc_exec($db,
	       "select e.natureza, s.description, e.idSector from EndossoNatureza e JOIN Sector s ON (s.id = e.idSector) where e.idEndosso=$idEndosso");
if(odbc_fetch_row($c)){
  $new_natureza = odbc_result($c, 1);
  $new_sector = odbc_result($c, 2);
  $new_idSector = odbc_result($c, 3);
}


$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($y)){
  $apolice = sprintf("062%06d", odbc_result($y, 1));
  if($prod != 62){
    $apolice .= "/$prod";
  }
}
$z = odbc_exec($dbSisSeg, "select max(n_Prop) from Proposta where i_Seg=$iSeg");
$prop =  odbc_result($z, 1);
$contrat .= "/$prop";

?>
