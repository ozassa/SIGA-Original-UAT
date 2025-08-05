<?php  // $idBuyer = $field->getField("idBuyer");
// $idInform = $field->getField("idInform");
// $action = $field->getField("action");

if(!$idByer)
   $idByer = $_REQUEST['idBuyer'];

$cur = odbc_exec($db,
		 "select I.name, address, city, tel, cep, contact, prevExp12, risk, numShip12, limCredit, periodicity, przPag, fax, C.name as country, divulgaNome, emailContato, cnpj ".
		 "from Importer I, Country C where I.id=$idBuyer and I.idCountry=C.id");


if(odbc_fetch_row($cur)){
  $nameImporter = $buyer = odbc_result($cur, 'name');
  $address = odbc_result($cur, 'address');
  $city = odbc_result($cur, 'city');
  $cep = odbc_result($cur, 'cep');
  $tel = odbc_result($cur, 'tel');
  $fax = odbc_result($cur, 'fax');
  $contact = odbc_result($cur, 'contact');
  $prevExp12 = odbc_result($cur, 'prevExp12');
  $numShip12 = odbc_result($cur, 'numShip12');
  $limCredit = odbc_result($cur, 'limCredit');
  $periodicity = odbc_result($cur, 'periodicity');
  $przPag = odbc_result($cur, 'przPag');
  $risk = odbc_result($cur, 'risk');
  $country = odbc_result($cur, 'country');
  $divulgaNome = odbc_result($cur, 'divulgaNome');
  $emailContato = odbc_result($cur, 'emailContato');
  $cnpj = odbc_result($cur, 'cnpj');
}

$cur = odbc_exec($db, "select * from ImporterAddress where idImporter=$idBuyer and state=1");

?>
