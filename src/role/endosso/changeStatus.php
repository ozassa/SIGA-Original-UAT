<?php odbc_autocommit($db, false);

if($comm == 'natRecebida'){
  $c = odbc_exec($db, "update Endosso set state=$status where id=$idEndosso");
  if(! $c){
    $msg = 'Erro ao atualizar endosso';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }

  if(! $notif->doneRole($idNotification, $db)){
    $msg = 'Erro ao encerrar notificação';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }
}else if($comm == 'tarifacao'){
  if($user->hasRole('backoffice') || $user->hasRole('endosso')){
    $solic = 1;
  }else{
    $solic = 2;
  }

  $q = "SELECT MAX (codigo) + 1 FROM Endosso";
  //echo "<pre>$q</pre>";
  $cur = odbc_exec($db, $q);
  odbc_fetch_row ($cur);
  $codigo = odbc_result ($cur,1);

  $ano = date ('Y');
  $codE = $codigo."/".$ano;

  $query = "insert into Endosso (idInform, tipo, solicitante, idUser, codigo) values ($idInform, $tipo, $solic, $userID, $codigo)";
  //echo "<pre>$query</pre>";
  $c = odbc_exec($db, $query);
  if(! $c){
    $msg = 'Erro ao criar endosso';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }
  $idEndosso = odbc_result(odbc_exec($db,
				     "select max(id) from Endosso where idInform=$idInform"),
			   1);
  $query = "insert into EndossoPremio (idEndosso, motivo) values ($idEndosso, '$motivo')";
  $r = odbc_exec($db, $query);
  $idPremio = odbc_result(odbc_exec($db,
				     "select max(id) from EndossoPremio where idEndosso=$idEndosso"),
			   1);

}else if($comm == 'cancelar'){
  $c = odbc_exec($db, "update Endosso set state=$status where id=$idEndosso");
  if(! $c){
    $msg = 'Erro ao cancelar endosso';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }
  if(! $notif->doneRole($idNotification, $db)){
    $msg = 'Erro ao encerrar notificação';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }
} else if ($comm == 'recebida') {
    $cnpjo = $new_cnpj;
    $len = strlen ($new_cnpj);
    $new_cnpj = "";
    for ($i = 0; $i < $len && $i < 18; $i ++) {
      $new_cnpj .= is_numeric ($cnpjo[$i]) ? $cnpjo[$i] : "";
    }

    $c = odbc_exec($db, "update Endosso set state=$status, dateEmission=getdate() where id=$idEndosso");
    if(! $c){
      $msg = 'Erro ao mudar status';
      odbc_rollback($db);
      odbc_autocommit($db, true);
      return;
    }
    $query = "update Inform set";
    $first = true;
    if (trim($new_name) != ''){
      if (trim($new_address) != ''){
 	    $query = $query." name='".$new_name."',";
        $first = false;
        $mudou_nome = 1;	  
	  }else{
 	    $query = $query." name='".$new_name."'";
        $first = false;
        $mudou_nome = 1;
	  }	
    }
    if (trim($new_address) != ''){
      if ($first){
        $query = $query." address='".$new_address."',";
        $query = $query." addressNumber='".$new_number."',";
        $query = $query." addressComp='".$new_addresscomp."',";
  	    $first = false;
      }
      else {
  	    $query = $query." address='".$new_address."',";
        $query = $query." addressNumber='".$new_number."',";
        $query = $query." addressComp='".$new_addresscomp."',";
      }
    }
    if (trim($new_city) != ''){
      if ($first){
        $query = $query." city='".$new_city."',";
		$first = false;
      }
      else {
		$query = $query." city='".$new_city."',";
      }
    }
    if (trim($new_cep) != ''){
      if ($first){
        $query = $query." cep='".$new_cep."',";
	$first = false;
      }
      else {
	$query = $query." cep='".$new_cep."'";
      }
    }
    if ((trim($new_idRegion) != '') && (trim($new_address) != '')){
      if ($first){
        $query = $query." idRegion='".$new_idRegion."',";
	$first = false;
      }
      else {
        $query = $query.",idRegion='".$new_idRegion."'";
      }
    }
    if (trim($new_cnpj) != ''){
      if ($first){
        $query = $query." cnpj='".$new_cnpj."',";
		$first = false;
      }
      else {
		$query = $query." ,cnpj='".$new_cnpj."'";
      }
    }
    $query = $query." where id=".$idInform;


 //name='$new_name', address='$new_address', city='$new_city', cep='$new_cep', idRegion=$new_idRegion, cnpj='$new_cnpj' where id=$idInform ";

    $c = odbc_exec($db, $query);
    // echo "<pre>$query</pre>";
    if(!$c){
      $msg = 'Erro ao atualizar Inform';
      odbc_rollback($db);
      odbc_autocommit($db, true);
      return;
    }
    
    $cur = odbc_exec($db, "Select i_Seg from Inform where id='$idInform'");
    $i_Seg = odbc_result($cur, 1);
    
    
    $query1 = "update Segurado set ";
    
     if ($new_name != ''){
        $query1 =  $query1 . "Nome='$new_name',";
     }
     
     if ($new_address!= ''){
        $query1 =  $query1 . "Endereco='$new_address',";
     }
     
     if ($new_number!= ''){
        $query1 =  $query1 . "Numero='$new_number',";
     }
     
     if ($new_addresscomp!='') {
        $query1 =  $query1 . "Compl='$new_addresscomp',";
     }
     
     if ($new_city != ''){
        $query1 =  $query1 . "Cidade='$new_city',";
     }
     
     if ($new_cep != ''){
        $new_cep = ereg_replace("-", "", $new_cep);
        $query1 =  $query1 . "CEP='$new_cep',";
     }
     
     if ($new_cnpj != '') {
     	$query1 = $query1 . "CNP='$new_cnpj',";
     }

     $cur2 = odbc_exec($db, "select name from Region where id ='$new_idRegion'");
     $c_Estado = odbc_result($cur2, 1);
     $query1 = $query1 . "c_Estado='$c_Estado' Where i_Seg='$i_Seg'";
     

    $r = odbc_exec($dbSisSeg, $query1);
   
  // echo "<br>";
  // echo "<pre>$query1</pre>";
   
	//   exit();

    if(!$r){
      $msg = 'Erro ao atualizar Inform';
      odbc_rollback($db);
      odbc_autocommit($db, true);
      return;
    }


  if(! $notif->doneRole($idNotification, $db)){
    $msg = 'Erro ao gerar o Endosso';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }

  // gera as notificacoes pros usuarios
  if($mudou_nome){
    $notification = "Endosso de Razão Social [$new_name]";
    $x = odbc_exec($db,
		   "select distinct id from Users where id in (select idUser from UserRole where idRole not in (1, 13, 14, 15, 17, 19, 20, 21))");
    while(odbc_fetch_row($x)){
      $idUser = odbc_result($x, 1);
      $key = session_id().time();
      $r = odbc_exec($db, "INSERT INTO NotificationU (state, cookie, notification) VALUES (1, '$key', '$notification')");
      if (!$r){
	$ok = false;
      }else{
	$cur = odbc_exec($db, "SELECT max(id) FROM NotificationU WHERE cookie = '$key'");
	if (!odbc_fetch_row($cur)){
	  $ok = false;
	}else{
	  $idNotification = odbc_result($cur, 1);
	  $r = odbc_exec ($db,
			  " UPDATE NotificationU".
			  "   SET link =".
			  "   '../endosso/Endosso.php?comm=razao&idEndosso=$idEndosso&idInform=$idInform&idNotification=$idNotification#endosso'".
			  " WHERE id = $idNotification");
	  if($r){
	    $r = odbc_exec ($db,
			    "INSERT INTO UserNotification (idUser, idNotification) VALUES ($idUser, $idNotification)");
	  }
	}
      }
    } // while
  }
}

if($status == 3){
  $msg = 'Endosso cancelado';
}else{
  $msg = 'Recebido recebido';
}

$tipo = "natOper";

if($comm == 'tarifacao'){
  // envia para a tarifacao
  $name = odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1);
  if(! $notif->newTarifEndosso($user, $name, $idInform, $db, $idEndosso, $idPremio, $codE)){
    $msg = 'Erro ao enviar para a tarifação';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }
  $msg = "Processo enviado à tarifação";
}else if($comm == 'natRecebida'){
  // envia para a tarifacao endosso de natureza de operação
  $name = odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1);
  $codigo = odbc_result(odbc_exec($db, "select codigo from Endosso where id=$idEndosso"), 1);
  $ano = date ('Y');
  $codE = $codigo."/".$ano;

  if(! $notif->newTarifN($user, $name, $idInform, $db, $idEndosso, $tipo, $codE)){
    $msg = 'Erro ao enviar para a tarifação';
    odbc_rollback($db);
    odbc_autocommit($db, true);
    return;
  }
  $msg = "Processo enviado à tarifação";
}

odbc_commit($db);
odbc_autocommit($db, true);
?>
