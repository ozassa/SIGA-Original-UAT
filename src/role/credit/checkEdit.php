<?php  require_once("../../rolePrefix.php");

$continue = 0;
$count = 1;
while($field->getField("edit".$count) != ""){
  if ($field->getField("edit".$count) != "")
    $continue = 1;
  else $continue = 0;
  $ok = 0;

  if($continue == 1){
    if ($role["creditManager"]){
      $ok = 1;
    }
    $contentEdit = $field->getField("edit".$count);
    $value 	=  $field->getField("value".$count);
    if($contentEdit < 0)
      $ok = 0;
    if ($role["credit"] || $role["creditInform"]){
      if ($value < $contentEdit){
	$ok = 0;
      } else {
	$ok = 1;
      }
    }
    $count++;

    if($ok){
      if($role["creditInform"]) {
	$Role = 12;
	$State = 8;
      } else if($role["creditManager"]) {
	$Role = 11;
	$State = 9;
      } else {
	$Role = 10;
	$State = 8;
      }
      //atualiza limite
      $u = $userID;
      $query = "INSERT INTO ChangeCredit (idImporter, userIdChangeCredit, state, credit)
	        VALUES ($idImporter, $u, $State, $contentEdit)";
      $r = odbc_exec ($db, $query);

      //grava o log
      $d = "[$u] Mudou limite crédito ImporterID[$idImporter], Original[$value] New[$contentEdit]";
      $r = odbc_exec ($db,"INSERT INTO TransactionLog (idUser, description) VALUES ($u, '$d')");
    }
  }
}

if($ok){
  require_once("credit.php");
  $comm = cCofaceImp;
}

?>
