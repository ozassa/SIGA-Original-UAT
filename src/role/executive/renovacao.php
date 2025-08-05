<?php

	if(! function_exists('ymd2dmy')){
	  // converte a data de yyyy-mm-dd para dd/mm/yyyy
	  function ymd2dmy($d){
		if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
		  return "$v[3]/$v[2]/$v[1]";
		}
		return $d;
	  }
	}
	
	$cur = odbc_exec($db, "select * from Inform where idAnt=$idInform");
	$old = $idInform;
	//echo "old(fora):$old";
	//echo "<pre>cur:odbc_fetch_row($cur)</pre>";

	
	if(odbc_fetch_row($cur)){
	//  echo "old(dentro):$old";	
	  //echo "aqui111";


	
	  $idInform = odbc_result($cur, 'id');
	//  echo "idInform:$idInform";
	}else{
		
	 	 //echo "aqui222";
		//  echo "old(no else):$old";
		//  echo "<pre>idInform(no else): $idInform</pre>";
	    //die('oi');
	
	  require_once("../client/renovacao.php");
	  $idInform = $newIdInform;
	  
	  
	}
	$q = "select name, endValidity from Inform where id=$idInform";
	//echo "<pre>select depois do client renovacao:$q</pre>";
	$cur = odbc_exec($db, $q);
	
	if(odbc_fetch_row($cur)){
	  $name = odbc_result($cur, 1);
	//  echo "Andrea";
	  $endValidity = odbc_result($cur, 2);
	}
?>
