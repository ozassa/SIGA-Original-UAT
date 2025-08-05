<?php 

if(isset($consulta)){
  if($tipoBanco == 1){
    $q = "SELECT codigo FROM CDBB WHERE id = $idCDBB"; 
  }else if($tipoBanco == 2){
    $q = "SELECT codigo FROM CDParc WHERE id = $idCDParc"; 
  }else{
    $q = "SELECT codigo FROM CDOB WHERE id = $idCDOB"; 
  }
}else{
  $q = "SELECT MAX (codigo) + 1 from
  (SELECT codigo
  FROM CDOB
  UNION
  SELECT codigo
  FROM CDParc
  UNION
  SELECT codigo
  FROM CDBB) as t";
}
//echo "<pre>$q</pre>";
$cur = odbc_exec($db, $q);
odbc_fetch_row ($cur);
$codigo = odbc_result ($cur,1);

$userId = $_SESSION['userID'];



if($tipoBanco == 3){
	  $q = "SELECT name FROM Banco WHERE id = $idBanco";
	  $bc = odbc_exec ($db, $q);
	  $bancoName = odbc_result($bc, 1);
	  $x = odbc_exec($db, "select startValidity from Inform where startValidity >= getdate() - 30 and id=$idInform");
	  if(odbc_fetch_row($x)){
		$data = "'". odbc_result($x, 1). "'";
	  }else{
		$data = 'getdate()';
	  }
	
	  $var = odbc_exec($db, "SELECT name FROM Inform Where id = $idInform");
	  $name  = odbc_result($var, 1);
	  $ano = date ('Y');
	  $c = $codigo."/".$ano;
	
	  $q = "SELECT status FROM CDOB WHERE id = $idCDOB";
	  $cur = odbc_exec($db, $q);
	  $status = odbc_result($cur, 1); 
	  
	  if ($status != 1){
		$r = $notif->cdob($idInform, $idCDOB, $idInform, $db, $name, $c, $idBanco, $bancoName);
		if (!$r) {
		  $msg = "problemas na criação da notificação";
		  $ok = false;
		}
		else {
		  $q = "UPDATE CDOB SET status = 1, codigo=$codigo, dateClient = getdate(), dateIniVig=$data WHERE id = $idCDOB";
		  $cur = odbc_exec($db, $q);
		  //echo "<pre>$q</pre>";
		}
      }
 }else if($tipoBanco == 1){
	 //$q = "SELECT MAX (codigo) + 1 FROM CDBB";
	 //$cur = odbc_exec($db, $q);
	 //odbc_fetch_row ($cur);
	 //$codigo = odbc_result ($cur,1);
	
	  $x = odbc_exec($db, "select startValidity from Inform where startValidity >= getdate() - 30 and id=$idInform");
	  if(odbc_fetch_row($x)){
		$data = "'". odbc_result($x, 1). "'";
	  }else{
		$data = 'getdate()';
	  }
	
	  $var = odbc_exec($db, "SELECT name FROM Inform Where id = $idInform");
	  $name  = odbc_result($var, 1);
	  $ano = date ('Y');
	  $c = $codigo."/".$ano;
	
	  $q = "SELECT status FROM CDBB WHERE id = $idCDBB";
	  $cur = odbc_exec($db, $q);
	  $status = odbc_result($cur, 1); 
	  if ($status != 1 && $status != 2){	
		$r = $notif->cdbb($userId, $idCDBB, $idInform, $db, $name, $c);
			if (!$r) {
			  $msg = "problemas na criação da notificação";
			  $ok = false;
			}else {
			  $q = "UPDATE CDBB SET status = 1, codigo=$codigo, dateClient = getdate(), dateIniVig=$data WHERE id = $idCDBB";
			  //echo "<pre>$q</pre>";
			  //break;
			  $cur = odbc_exec($db, $q);
			  
			}
		
	  }
}else if($tipoBanco == 2){
	  $x = odbc_exec($db, "select startValidity from Inform where startValidity >= getdate() - 30 and id=$idInform");
	  if(odbc_fetch_row($x)){
		$data = "'". odbc_result($x, 1). "'";
	  }else{
		$data = 'getdate()';
	  }
	  $var = odbc_exec($db, "SELECT name FROM Inform Where id = $idInform");
	  $name  = odbc_result($var, 1);
	  $ano = date ('Y');
	  $c = $codigo."/".$ano;
	  $q = "SELECT status FROM CDParc WHERE id = $idCDPart";
	  $cur = odbc_exec($db, $q);
	  $status = odbc_result($cur, 1); 
	  if ($status != 1){
		$r = $notif->cdparc($idInform, $idCDParc, $idInform, $db, $name, $c);
		if (!$r) {
		  $msg = "problemas na criação da notificação";
		  $ok = false;
		} else {
		  $q = "UPDATE CDParc SET status = 1, codigo=$codigo, dateClient = getdate(), dateIniVig=$data WHERE id = $idCDParc";
		  $cur = odbc_exec($db, $q);
		  //echo "<pre>$q</pre>";
		}
	 }
}

//if($link){
?>
