<?php  $ok = true;
odbc_autocommit($db, false);
odbc_autocommit($dbSisSeg, false);

$log_query = "";

$x = odbc_exec($db, "select nProp, i_Seg from Inform where id=$idInform");
$nProp = odbc_result($x, 1);
$i_Seg = odbc_result($x, 2);

$x = odbc_exec($db, "update Inform set state=1, sentOffer=0, dateCancel=getdate() where id=$idInform");

if($x)
{
	$log_query .= "update Inform set state=1, sentOffer=0, dateCancel=getdate() where id=$idInform";
}


if(!$x){
  $msg = 'Erro ao atualizar Informe';
  $ok = false;
}else{
  // cancela tb no SisSeg
  $x = odbc_exec($dbSisSeg, "update Proposta set s_Proposta=7, d_Situacao=getdate() where i_Seg=$i_Seg and n_Prop=$nProp");
  //criado por Wagner 29/08/2008
  if($x)
	{
		$log_query .= "update Inform set state=1, sentOffer=0, dateCancel=getdate() where id=$idInform";
	}  
  
  
  $y = odbc_exec($dbSisSeg, "update Parcela set s_Parcela=3, d_Cancelamento=getdate() where i_Seg=$i_Seg and n_Prop=$nProp");
  //criado por Wagner 29/08/2008
  if($y)
	{
		$log_query .= "update Parcela set s_Parcela=3, d_Cancelamento=getdate() where i_Seg=$i_Seg and n_Prop=$nProp";
	} 
	
	
  $z = odbc_exec($dbSisSeg, "update PagRec set s_Pagamento=3, d_Situacao=getdate() where i_Seg=$i_Seg and n_Prop=$nProp");
  //criado por Wagner 29/08/2008
  if($z)
	{
		$log_query .= "update PagRec set s_Pagamento=3, d_Situacao=getdate() where i_Seg=$i_Seg and n_Prop=$nProp";
	}
	
  if(! ($x && $y && $z)){
    $msg = 'Erro ao cancelar proposta no SisSeg<br>'. odbc_errormsg();
    $ok = false;
  }
}

//Criado Por Tiago V N - 15/03/2006
//Log do reestudo e cancelamento de proposta (Cancelamento de Proposta)
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('29'," .
           "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
           "','".date("H").":".date("i").":".date("s")."')";
   if (odbc_exec($db, $sql) ) {
   		$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
		$cur = odbc_result(odbc_exec($db, $sql_id), 1);
    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', 'status', '1', 'Envio para reestudo')";
		$rs = odbc_exec($db, $sql);
		
		
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	   //CRIADO POR WAGNER
	   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   
	   if ($rs) {
	      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
	      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
	      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
	          "values ('$cur', '".str_replace("'","",$log_query)."')";
			  
			  //echo $sql;
	          odbc_exec($db, $sql);
	   }//fim if	
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		

   }else{
     $msg = "Erro no incluir do Log";
   }

if($ok){
  odbc_commit($db);
  odbc_commit($dbSisSeg);
  $msg = 'Informe enviado para reestudo';
}else{
  odbc_rollback($db);
  odbc_rollback($dbSisSeg);
}
odbc_autocommit($db, true);
odbc_autocommit($dbSisSeg, true);
?>
