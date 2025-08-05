<?php function arruma_cnpj($c){
  if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
    return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
  }
}


$c = odbc_exec($db,
	       "select i.name, i.address, i.city, i.cep, r.description,
                i.startValidity, i.endValidity, i.contrat, i.i_Seg, i.prodUnit
                from Inform i join Region r on i.idRegion = r.id where i.id=$idInform");
if(odbc_fetch_row($c)){
  $ini_vig = odbc_result($c, 6);
  $fim_vig = odbc_result($c, 7);
  $contrat = odbc_result($c, 8);
  $iSeg = odbc_result($c, 9);
  $prod = odbc_result($c, 10);
}

$c = odbc_exec($db, "select bornDate, solicitante, idUser from Endosso where id=$idEndosso");
if(odbc_fetch_row($c)){
  $bornDate = ymd2dmy(odbc_result($c, 1));
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
}

$c = odbc_exec($db,
	       "select e.name, e.address, e.city, e.cep, r1.description, e.cnpj, e.nameOld,
		e.addressOld, e.cityOld, e.cepOld, e.cnpjOld, r2.description, r1.id,e.nunmber,
        e.addresscomp,e.numberOld,e.addresscompOld from EndossoDados e join Region r1 on e.idRegion=r1.id join Region r2 on e.idRegionOld=r2.id
                where e.idEndosso=$idEndosso");
if(odbc_fetch_row($c)){
  $new_name = odbc_result($c, 1);
  $new_address = odbc_result($c, 2);
  $new_number = odbc_result($c, 13);
  $new_addresscompl = odbc_result($c, 14);
  $new_city = odbc_result($c, 3);
  $new_cep = odbc_result($c, 4);
  $new_region = odbc_result($c, 5);
  $new_cnpj = arruma_cnpj(odbc_result($c, 6));
  $name = odbc_result($c, 7);
  $address = odbc_result($c, 8);
  $number = odbc_result($c, 15);
  $addresscomp = odbc_result($c, 16);
  $city = odbc_result($c, 9);
  $cep = odbc_result($c, 10);
  $region = odbc_result($c, 12);
  $cnpj = arruma_cnpj(odbc_result($c, 11));
  $new_idRegion = odbc_result($c, 13);
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
