<?php
  require_once("../role/rolePrefix.php");


  class CheckInput {
/*	$ok = 0;

	verifica todos os campo e se o usu�rio pode altera-los.
	o nome do  campo ser� "editX", X � a quantidade de campos que h� na tela.
	$i � a quantidade de $edit
	$value � uma lista de valores iniciais.

	function Input($i) {

	$continue = 0;
	$count = 0;
	$field->getField("edit".$count) != ""){
		if ($field->getField("edit".$count) != "")
			$continue = 1;
		$count++; 
	}
	if($continue == 1){
		if ($user -> hasRole("creditManager")){
			$ok = 1;
		}

		$count = 0;

		while($count <= $i){

			$contentEdit = $field->getField("edit".$count);
			$value 	=  $field->getField("edit".$count); 
			if($contentEdit < 0)
				$ok = 0;   	
			
			if ($user -> hasRole("credit") || ($user -> hasRole("creditInform")){
				if ($value > $contentEdit){
					$ok = 0;			
				} else {
					$ok = 1;
				}
			}
			$count++;

		
	
			if($ok){
		
				if($user -> hasRole("creditInform")) {
					$Role = 12;
					$State = 8;
				} else if($user -> hasRole("creditManager")) {
					$Role = 11;
					$State = 9;
					} else { 
					$Role = 10;
					$State = 8;
					}
//atualiza limite
				$u = $user->id;

				$query = "INSERT INTO ChangeCredit (idImporter, userIdChangeCredit, state, credit)
					  VALUES ($idImporter, $u, $State, $contentEdit)";		

				$r = odbc_exec ($db, $query); 
			
			
//grava o log
				$d = "[$u] Mudou limite cr�dito ImporterID[$idImporter],
					 Original[$value] New[$contentEdit]";

 				$r = odbc_exec ($db,"INSERT INTO TransactionLog (idUser, description) VALUES ($u, '$d')");
  		}

		
	}

	
 }
*/
?>