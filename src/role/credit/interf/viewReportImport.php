<?php  $log_query = "";
//Criado por Tiago V N - Elumini - 11/101/2005

	if ( $at == "1" ) {
		//echo $idChangeCredit . "- A -> " . $analise . "- M ->" . $monitoramento ;
		if ($analise == ""){
			$analise = "0";
		}
		if ($monitoramento == "") {
			$monitoramento = "0";
		}

		$sql = "Update ChangeCredit set analysis = '$analise', monitor='$monitoramento' where id = '$idChangeCredit'";


//echo $sql." <- debug aqui por eliel...<br>";


		if ( odbc_exec($db, $sql) ){
		

		//criado por Wagner 29/08/2008
		$log_query .= "Update ChangeCredit set analysis = '$analise', monitor='$monitoramento' where id = '$idChangeCredit'";
		/*echo "<script>verErro('$log_query')</script>";*/
			
		    //Criado Por Tiago V N - 11/11/2005
    		//Log Retirar importador p/ cobrança de Analise ou Monitoramento
		    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('24'," .
        		   "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
		           "','".date("H").":".date("i").":".date("s")."')";
	        if (odbc_exec($db, $sql) ) {
			 	
   					$sql_id = "SELECT @@IDENTITY AS 'id_Log'";
					$cur = odbc_result(odbc_exec($db, $sql_id), 1);
			    	$sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
	               "values ('$cur', 'Análise e Monitoramento ', '$idBuyer', 'Alteração')";
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
	          $resp = odbc_exec($db, $sql);
			  
			
				 
			  
			  
			  
	   }//fim if	
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
					
					
					
					
					
		   }else{
				     $msg = "Erro no incluir do Log";
	   }//Log
		}else{
			echo "Erro em atualizar Analise e Monitoramento<br>". odbc_errormsg($db);
		}
	}

	$sql = "select * from Importer where id = $idBuyer";
	$x = odbc_result(odbc_exec($db, $sql), "name");

	$am = "Select id, analysis, monitor from ChangeCredit where id = (
		  select max(id) from ChangeCredit where idImporter = $idBuyer And state = '6')";
	$cur = odbc_exec($db, $am);

	$idImp    = odbc_result($cur, "id");

	if (odbc_result($cur, "analysis") == "1") {
		$checka = "checked";
		$check_a_value = "1";
	}elseif (odbc_result($cur, "analysis") == "0") {
		$checka = "unchecked";
		$check_a_value = "0";
	}
	if (odbc_result($cur, "monitor") == "1") {
		$checkm = "checked";
		$check_m_value = "1";
	}elseif (odbc_result($cur, "monitor") == "0") {
		$check_m_value = "0";
	}
?>
<form name="alterar" action="Credit.php?comm=viewReportImport" method="post">
<table width="100%" border="0" cellpaddind="0" cellspacing="0">
<tr><td class="bgAzul" colspan="2">Nome Importador</td></tr>
<tr><td colspan="2"><?php echo $x;?></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" class="bgAzul">Motivo</td></tr>
<tr>
	<td><input type="checkbox" name="analise" <?php echo $checka;?>>Análise</td>
	<td><input type="checkbox" name="monitoramento" <?php echo $checkm;?>>Monitoramento</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td colspan="2" align="center">
	<input type="hidden" name="voltar">
	<input type="hidden" name="idInform">
	<input type="hidden" name="anoMes">
	<input type="hidden" name="origem">
	<input name="ret" value="<?php echo htmlspecialchars($_REQUEST['ret'], ENT_QUOTES, 'UTF-8'); ?>" type="hidden">
	<input type="button" value=" Voltar " class="servicos" onclick="volt(this.alterar);">&nbsp;
	<input type="hidden" name="idChangeCredit">
	<input type="hidden" name="at">
	<input type="hidden" name="idBuyer">
	<input type="button" value=" Alterar " class="servicos" onclick="alte(this.alterar);">
	</td>
</tr>
</table>
</form>
<script language="javascript">
function volt(c) {
 document.all.voltar.value = '1';
 document.all.idInform.value = '<?php echo $idInform;?>';
 document.all.anoMes.value = '<?php echo $anoMes;?>';
 document.all.origem.value = '<?php echo $origem;?>';
 alterar.submit();
}
function alte(a) {
 document.all.idInform.value = '<?php echo $idInform;?>';
 document.all.anoMes.value = '<?php echo $anoMes;?>';
 document.all.origem.value = '<?php echo $origem;?>';


 document.all.voltar.value = '0';
 document.all.idChangeCredit.value = '<?php echo $idImp;?>';
 if (document.all.analise.checked) {
  document.all.analise.value = '1';
 }else{
  document.all.analise.value = '0';
 }
 if (document.all.monitoramento.checked) {
  document.all.monitoramento.value = '1';
 }else{
  document.all.monitoramento.value = '0';
 }
 document.all.idBuyer.value = '<?php echo $idBuyer;?>';
 document.all.at.value = '1';
 alterar.submit();
}
</script>
