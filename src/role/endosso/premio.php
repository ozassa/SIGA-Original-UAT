<?php $c = odbc_exec($db,
	       "select i.name, i.address, i.city, i.cep, r.description, i.startValidity,
                i.endValidity, i.contrat, i.i_Seg, i.prodUnit, i.txRise, i.nProp, i.warantyInterest
                from Inform i join Region r on i.idRegion = r.id where i.id=$idInform");
if(odbc_fetch_row($c)){
  $name = odbc_result($c, 1);
  $address = odbc_result($c, 2);
  $city = odbc_result($c, 3);
  $cep = odbc_result($c, 4);
  $region = odbc_result($c, 5);
  $ini_vig = odbc_result($c, 6);
  $fim_vig = odbc_result($c, 7);
  $contrat = odbc_result($c, 8);
  $iSeg = odbc_result($c, 9);
  $prod = odbc_result($c, 10);
  $txRise = odbc_result($c, 11);
  $prop = odbc_result($c, 12);
  $juros = odbc_result($c, "warantyInterest");
}

$c = odbc_exec($db, "select bornDate from Endosso where id=$idEndosso");
if(odbc_fetch_row($c)){
  $bornDate = ymd2dmy(odbc_result($c, 1));
}else{
  $inativo = 1;
  return;
}

$c = odbc_exec($db, "select premioOld, premio, txMin, txMinOld from EndossoPremio where id=$idPremio");
if(odbc_fetch_row($c)){
  $premio_min_old = odbc_result($c, 1) * (1 + $txRise) * ($juros ? 1.04 : 1);
  $premio_min = odbc_result($c, 2) * ($juros ? 1.04 : 1);
  $tx_min = odbc_result($c, 3);
  $tx_min_old = odbc_result($c, 4);
}

$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($y)){
  $apolice = sprintf("062%06d", odbc_result($y, 1));
  if($prod != 62){
    $apolice .= "/$prod";
  }
}

if(! $prop){
  $z = odbc_exec($dbSisSeg, "select max(n_Prop) from Proposta where i_Seg=$iSeg");
  $prop =  odbc_result($z, 1);
}
$contrat .= "/$prop";
?>
