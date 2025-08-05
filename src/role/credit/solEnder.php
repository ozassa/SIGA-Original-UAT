<?php  //Alterado HiCom mes 04

$idInform = $field->getField("idInform");
$q = "SELECT contrat, name, idAnt, dateEmission FROM Inform where id = $idInform";
$d = odbc_exec($db, $q);

if(odbc_fetch_row($d)){
  $ciExpo   = odbc_result($d, 1);
  $nameExpo = odbc_result($d, 2);
  $idAnt    = odbc_result($d, 3);
  $dateEmission = odbc_result($d, 4);
  $hc_name_inform = $nameExpo;
  
  
}else{
  $ciExpo   = "ERRO";
  $nameExpo = "ERRO";
  $hc_name_inform = $nameExpo;
  
}

$hc_str = " SELECT i.id, i.name, " .
          " a.id as idAddress, a.address, a.city, a.tel, a.cep  " .
          " from  ImporterAddress a, Importer i " .
          " where i.id = a.idImporter and a.pendente = 'S' and a.inativeDate is null and i.idInform=$idInform " .
		  " order by i.name, a.address ";

$cur = odbc_exec($db, $hc_str);


?>
