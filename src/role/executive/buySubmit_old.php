<?php  $x = odbc_exec($db, "select sentOffer, prMax, prMin, idAnt from Inform where id=$idInform");
$sent = odbc_result($x, 1);
$prMax = odbc_result($x, 2);
$prMin = odbc_result($x, 3);
$hc_idAnt = odbc_result($x, 4);

if($prMax > 0 || $prMin > 0 || $sent){
  $reestudo = 1;
}

odbc_autocommit($db, false);
$cur = odbc_exec($db,
		 "UPDATE Inform SET buyersState=".
		 ($role["client"] ? "2" : "3").
		 " WHERE id=$idInform");

$err = false;
// for($i = 1; $field->getField("buyId$i") != ''; $i++){
for($i = 1; $field->getField("buyId$i") != ''; $i++){
  $idOther = 0;
  $idBuy = $field->getField("buyId$i");
  
  //echo "IMP: " .  $idBuy . "<BR>";
  
  $h = $field->getField("free$i");
  $lim = $field->getNumField("limCredit$i") * 1000;
  
  //echo "LIM: " .  $lim . "<BR>";
  
  if($h && $lim == 0){
    //echo "ERRRo<BR>";
    $err = true;
	//echo "ERRRo<BR>";
  }
  
  $x = odbc_exec($db, "select idTwin, limCredit, state from Importer where id=$idBuy");
  $limite = odbc_result($x, 'limCredit');
  $idOther = odbc_result($x, 'idTwin');
  $hc_state = odbc_result($x, 'state');
  if(! $idOther){
    $y = odbc_exec($db, "select id from Importer where idTwin=$idBuy");
    $idOther = odbc_result($y, 1);
  }

  $x = odbc_exec($db,
		 "select credit, creditTemp, limTemp from ChangeCredit where
                  id=(select max(id) from ChangeCredit where idImporter=$idBuy)");
  $c = odbc_result($x, 1);
  $ct = odbc_result($x, 2);
  $lt = getTimeStamp(odbc_result($x, 3));
  if($lt >= time()){
    $ctotal = $c + $ct;
  }else{
    $ctotal = $c;
  }
  
  
  // Nao entendi o porque..... 
  //if($ctotal <= $lim)

  if($ctotal > $lim)
  {
     // Testa para ver se e renovacao ou reestudo
	 if (($hc_idAnt > 0) || ($reestudo == 1))
	 {	
        // Zera o credito do imorter e change credit
	    $query = "UPDATE Importer SET credit=0 WHERE id=$idBuy";		 
	    odbc_exec($db, $query);
	 
        $query = "UPDATE ChangeCredit SET credit=0, creditTemp=0 WHERE id=(select max(id) from ChangeCredit where idImporter=$idBuy)";
	    odbc_exec($db, $query);
      
	    $ctotal = 0;
	 } 	    
  }
  
  // Nao entendi o porque..... 
  
  if($ctotal <= $lim)  
  {	
	//$query =
    //  "UPDATE Importer SET limCredit=$lim, hold=".
    //  ($h == 1 ? 0 : 1).
    //  ($reestudo && $lim == $limite ? ', state=6' : '').
    //  " WHERE id=$idBuy";
	
	//echo $idBuy . "--->" . $lim . "<--<br>";
	
	
	$query =
      "UPDATE Importer SET limCredit=$lim, hold=".
      ($h == 1 ? 0 : 1).
      " WHERE id=$idBuy";	  
	  
    if (odbc_exec($db, $query))
	{
	  //echo "OK<br>";  
	}
	
	//echo $query . "<br>";
	
    $query = "UPDATE ChangeCredit SET creditSolic=$lim WHERE id=(select max(id) from ChangeCredit where idImporter=$idBuy)";
	odbc_exec($db, $query);
	
	//echo $query . "<br>";
	
	
	
	//echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa:" . $hc_acc;
		
	//Hicom testa reestudo// 
	// Vamos verificar se o limcredit é menor que o concedido, Se for, colocamos tudo como novo
	if ($reestudo == 1) 
	{
	
	   //echo $idBuy . "Reestudo == 1<BR>";
	
	   if($ctotal <= $lim)
	   {
	     // HiCom Vamos colocar como novo se ainda nÃo é novo
		 //
		 
		// echo $idBuy . "ctotal <= lim<BR>";
		 
		 if ($lim != $limite)
		 {
		 
		   // echo $idBuy . "lim != limite<BR>";
		 
		    if ($hc_state != 1 && $hc_state < 7) 
		    {
			
			   //echo $idBuy . "hc_state != 1   (" . $hc_state . ")   <BR>";
			   
		       $hc_str = " Update  Importer SET state = 1 WHERE id = " . $idBuy . " ";
		       odbc_exec($db, $hc_str);
			   
			   $hc_state = 1;
		 
		       $hc_str = " Insert into ChangeCredit ";
               $hc_str = $hc_str . " (idImporter, credit, idNotificationR, cookie, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic, creditTemp, limTemp) ";
               $hc_str = $hc_str . " (select idImporter, credit, idNotificationR, cookie, -6 , state, stateDate, monitor, analysis, creditDate, " . $lim . " , creditTemp, limTemp ";
               $hc_str = $hc_str . " from ChangeCredit WHERE idImporter = " . $idBuy . "  and id = (select max(c.id) from ChangeCredit c where idImporter = " . $idBuy . " and state = 1   ))"; 
               odbc_exec($db, $hc_str);	
		    }   
		 }
	   }
	}
	
	if ($hc_state == 1)
	{
	
	    //echo $idBuy . "hc_state ======= 1   (" . $hc_state . ")   <BR>";
		
		$hc_n = odbc_result(odbc_exec($db, "select count(*) from ChangeCredit where idImporter=$idBuy"), 1);
        if($hc_n == 0)
	    {
	       // Coloquei pois encontrei casos de imp sem change.  neste caso, inluimos um....

		   //echo $idBuy . "RETORNOU ZERO REGISTROS   <BR>";
		   		   
	       $hc_str = " Insert into ChangeCredit ";
           $hc_str = $hc_str . " (idImporter, credit, userIdChangeCredit, state, stateDate, monitor, analysis, creditDate, creditSolic, creditTemp) values ";
           $hc_str = $hc_str . " (" . $idBuy . ", 0" . $c . " , -7 , 1, getdate(), 1, 1, getdate(), 0" . $lim . " , 0  )";
           
		   //echo  $hc_str;
		   
		   odbc_exec($db, $hc_str);	
	   	   
	    }
	}	
	
// Inicio cometado pela HiCom
// Deixamos processar o else do if($ctotal <= $lim), apena zerando os creditos	
 }else{
   $msg = "O limite de crédito concedido (mais o crédito temporário) é maior que o crédito solicitado";
   $err = true;
 }
// Fim Comentado pela hicom

  if($idOther){
//     $query = "UPDATE Importer SET limCredit=$lim, hold=". ($h == 1 ? 0 : 1). " WHERE id = $idOther";
//     odbc_exec($db, $query);
//     $query = "UPDATE ChangeCredit SET creditSolic=$lim WHERE id=(select max(id) from ChangeCredit where idImporter=$idOther)";
//     odbc_exec($db, $query);
  }
}

if($err){
 
   
  
  $msg = $msg . "   Favor Preencher a Exposição Máxima dos Importadores Selecionados   ";
  
  
  $forward = "error";
  odbc_rollback($db);
}else if(!$cur){
  $msg = "Problemas na atualização da base de dados";
  $forward = "error";
  odbc_rollback($db);
}else{
  odbc_commit($db);
}
odbc_autocommit($db, true);
?>
