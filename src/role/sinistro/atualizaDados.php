<?php 
$value = $field->getNumField("sinistro");
$juro = $field->getNumField("juro");
$limiteCredito = $field->getNumField("limiteCredit");
$limCredit = $field->getNumField("limiteC");


$tt =  number_format(($limiteCredito),2,",",".");

//echo "<br>limiteCredito: $limiteCredito";
//echo "<br>limCredit: $limCredit";
//echo "<br>$tt";

if($limCredit > $limiteCredito){
   $msgAtualiza =  "O Limite de Crédito deverá ser igual ou menor que $tt";
}else{
	$q = "UPDATE Sinistro SET value = $value, juro = $juro, limCredit = $limCredit WHERE id = $idSinistro";
	
	$cur = odbc_exec($db, $q);
        //echo "<pre>$q</pre>";
}
?>