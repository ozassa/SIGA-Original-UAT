<?php  //Alterado HiCom mes 04

$idInform = $_REQUEST["idInform"];

$q = "SELECT contrat, name, idAnt, dateEmission, pvigencia FROM Inform WHERE id = ?";
$stmt = odbc_prepare($db, $q);
odbc_execute($stmt, [$idInform]);
$d = $stmt;
odbc_free_result($stmt);

if(odbc_fetch_row($d)){
  $ciExpo   = odbc_result($d, 1);
  $nameExpo = odbc_result($d, 2);
  $idAnt    = odbc_result($d, 3);
  $dateEmission = odbc_result($d, 4);
  $vigencia    = odbc_result($d, 5);
}else{
  $ciExpo   = "ERRO";
  $nameExpo = "ERRO";
}

if (!isset($flag_renovacao)){
  $flag_renovacao = isset($_REQUEST['flag_renovacao']) ? $_REQUEST['flag_renovacao'] : false;
}

if ($comm == 'view' || $comm == 'clientChangeImporterInsert') 
{
  $state = 1;
  $hold = ' AND hold = 0';
  $union = '';
  if(isset($flag_renovacao)){
    if($flag_renovacao){
      $union = "UNION SELECT Importer.id, Importer.name, Country.name, Country.code, ".
      "Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state ".
      "FROM Importer, Country ".
      "WHERE Importer.idInform = $idInform AND ".
      "Importer.idCountry = Country.id AND ".
      "Importer.state=6 AND Importer.creditAut=1 ";
    }
  }
  
}
else{
  $state = 2;
  $hold = '';
}



// $cQuery = "SELECT Importer.id, Importer.name, Country.name, Country.code, ".
// 		 "Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.idTwin ".
// 		 "FROM Importer, Country ".
// 		 "WHERE Importer.idInform = $idInform AND ".
// 		 "Importer.idCountry = Country.id AND (".
// 		 "((Importer.state = '0' or Importer.state = $state) $hold ".
// 		 "AND (Importer.creditAut is null OR Importer.creditAut = 0)) ".
// 		 ($flag_renovacao ? " or (Importer.state=1 and Importer.hold=1)" : '').
// 		 ") AND Importer.id not in (select idImporter from ImporterRem) ".
// 		 "ORDER BY Importer.id";
// $cur = odbc_exec($db,$cQuery);



// if($idAnt > 0 || $dateEmission){
//   $cQuery = "SELECT Importer.id, Importer.name, Country.name, Country.code, ".
// 		    "Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, ".
// 		    "Importer.state FROM Importer, Country ".
// 		    "WHERE Importer.idInform = $idInform AND ".
// 		    "Importer.idCountry = Country.id AND ".
// 		    "(Importer.state = '0' or Importer.state = $state) $hold ".
// 		    "AND Importer.creditAut = 1 AND Importer.id not in (select idImporter from ImporterRem) $union".
// 		    "ORDER BY Importer.id";
//   $cur2 = odbc_exec($db,$cQuery);
// }
 
//print $cQuery;

?>
