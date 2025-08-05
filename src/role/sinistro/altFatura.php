<?php function arruma($str){
  list($dia, $mes, $ano) = explode("/", $str);
  return "$ano-$mes-$dia";
}

$numFat   = $field->getField("numFat");
$valueFat = $field->getNumField("valueFat");
$valuePag = $field->getNumField("valuePag");
$valueAbt = $field->getNumField("valueAbt");
$dateEmb  = arruma($field->getField("dateEmb"));
$dateVenc = arruma($field->getField("dateVenc"));

list($ano, $mes, $dia) = split ('-', $dateEmb); 
$d_Max_Venc = date ("Y-m-d", mktime (0,0,0, $mes,  $dia + 180,  date("Y")));

list($ano, $mes, $dia) = split ('-', $dateVenc); 
$d_Venc = date ("Y-m-d", mktime (0,0,0, $mes,  $dia,  date("Y")));


if ($d_Max_Venc > $d_Venc){
  $q = "INSERT INTO ChangeSinistroDetails (idSinistro, dateEmb, dateVenc, valueFat, valuePag, valueAbt, numFat, dateAlt, idUser, tipo) VALUES ($idSinistro, '$dateEmbA', '$dateVencA', $valueFatA, $valuePagA, $valueAbtA, $numFatA, getdate(), $userID, 1)";
  $cur = odbc_exec($db, $q);

//tipo 1 = alteração

  $q = "UPDATE SinistroDetails SET numFat = $numFat, dateEmb = '$dateEmb', dateVenc = '$dateVenc', valueFat = $valueFat, valuePag = $valuePag, valueAbt = $valueAbt WHERE id = $idSinDetA";
  $cur = odbc_exec($db, $q);

  //echo "<pre>$q</pre>";

} else {
  $msgA = "A data de Vencimento não deve ser superior a 180 dias (6 meses) da data de Embarque";
}

?>


