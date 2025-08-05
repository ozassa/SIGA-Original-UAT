<?php  function arruma($str){
  list($dia, $mes, $ano) = explode("/", $str);
  return "$ano-$mes-$dia";
}

$valueFat1 = $field->getNumField("valueFat");
$valuePag1 = $field->getNumField("valuePag");

if ($action == "valor"){
    $valueAbt = $valueFat1 - $valuePag1;
} else {
    $valueAbt = $field->getNumField("valueAbt");
    $dateEmb = arruma($dateEmb);
    $dateVenc = arruma($dateVenc);
}

  list($ano, $mes, $dia) = split ('-', $dateEmb); 
  $d_Max_Venc = date ("Y-m-d", mktime (0,0,0, $mes,  $dia + 180,  date("Y")));

  list($ano, $mes, $dia) = split ('-', $dateVenc); 
  $d_Venc = date ("Y-m-d", mktime (0,0,0, $mes,  $dia,  date("Y")));

  //echo "data máx venc $d_Max_Venc<br>";
  //echo "data venc $dateVenc<br>";
  //echo "dia $dia<br>";
  //echo "mes $mes<br>";

if($valuePag1 > $valueFat1 + 1){
   $msgP = "Valor Pago não pode ser maior que o Valor da Fatura";
}else{
    if($Ndve && ($valueFat1 == 0)){
        $msgA = "Valor da Fatura não pode ser igual a 0,00";
    }else{
        if ($d_Max_Venc > $d_Venc){
    	    $q = "INSERT INTO SinistroDetails (idSinistro, idDVE, numFat, dateEmb, dateVenc, valuePag, valueFat, valueAbt) VALUES ($idSinistro, $idDVE, '$numFat', '$dateEmb',  '$dateVenc', $valuePag1, $valueFat1, $valueAbt)";
      	    $cur = odbc_exec($db, $q);
            //echo $q;
        } else {
            $msgA = "A data de Vencimento não deve ser superior a 180 dias (6 meses) da data de Embarque";
        }
    }
}


//echo "<br>$valueFat1";
//echo "<br>$valuePag1";
//echo "<br>$valueAbt";

?>