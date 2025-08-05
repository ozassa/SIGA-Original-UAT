<?php 
$erro = '';
while($cob != 0){
  $idDetails = $field->getField("idSinDet$cob");
  $valorCob = $field->getNumField("valueCob$cob");
  $valoraberto = $field->getField("valoraberto$cob");
  $numFat = $field->getField("numFat$cob");
  if($valorCob > $valoraberto){
    if ($erro != "") { 
       $esp = ", "; 
    }
    $erro = $erro.$esp.$numFat;
  }else{
    $q = "UPDATE SinistroDetails SET valueCob = $valorCob WHERE id = $idDetails";
    //echo "$q<br>";
    $cur = odbc_exec($db, $q);
  }
  $cob--;
}

?>