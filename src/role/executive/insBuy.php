<?php  if (!is_numeric($field->getNumField("prevExp12")) || !is_numeric($field->getNumField("limCredit")) || !is_numeric($field->getNumField("numShip12"))) {
  $msg = "Campo n�o num�rico";
} else if ($field->getField("name") == "" || $field->getField("address") == "" || $field->getNumField("country") == "") {
  $msg = "Todos os campos s�o obrigat�rios";
} else if(preg_match("/[\/����������������������������]/", $name)){
  $msg = "No nome do importador nao s�o permitidos acentos ou barras (/)";
}else{
  $name = ereg_replace("'", "''", strtoupper($field->getField("name"))); 
  $cur = odbc_exec(
    $db,
    " INSERT INTO Importer (idInform, name, address, idCountry, tel, prevExp12, limCredit, numShip12, periodicity, risk)".
    " VALUES ($idInform,'".
    $name. "','".
    strtoupper($field->getField("address")). "',".
    $field->getField("idCountry").",'".
    $field->getField("tel"). "',".
    $field->getNumField("prevExp12"). ",".
    $field->getField("limCredit"). ",".
    $field->getField("numShip12"). ",'".
    $field->getField("periodicity"). "','".
    $field->getField("risk"). "')");
  if (!$cur)
    $msg = "Problemas na atualiza��o da base";
}
?>
