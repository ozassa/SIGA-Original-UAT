<?php if(! function_exists('TrataData1')){
	function TrataData1($data, $tipo){
	
	#
	# Variavel $data é a String que contém a Data em qualquer formato
	# Variavel $tipo é que contém o tipo de formato data.
	# $tipo : 
	#		1 - Brasil - No formato -> Dia/Mes/Ano ou DD/MM/YYYY
	#		2 - USA	 - No formato -> YYYY-Mes-Dia ou YYYY-MM-DD
	
	# Obs
	# Esta função não funciona com timestemp no formato a seguir :
	# DD/MM/YYYY H:M:S.MS ou YYYY-MM-DD H:M:S:MS
	# Pode configurar o formato da Data
	
		$data = explode(" ", $data);
		if ( $tipo == 1) {
			list($dia, $mes, $ano) = explode("[/-]", $data[0]);		
		}elseif ( $tipo == 2 ) {
			list($ano, $mes, $dia) = explode("[-/]", $data[0]);		
		}else{
			$msg = "Erro - Formato de data não existe.";
		}	
	
		return $dia."/".$mes."/".$ano;	
	}
}
for ($i=0; $i< count($selectiona); $i++) {
   $strSQL = "SELECT dbo.Importer.name, dbo.tb_Temp_Dve.dt_Embarque, 
	         dbo.tb_Temp_Dve.fatura, dbo.tb_Temp_Dve.dt_Vencimento, 
             dbo.tb_Temp_Dve.vl_embarque, dbo.tb_Temp_Dve.proex, 
             dbo.tb_Temp_Dve.ace, dbo.tb_Temp_Dve.modalidade,
			 dbo.Importer.idCountry, dbo.tb_Temp_Dve.idDve,
			 dbo.tb_Temp_Dve.idImporter FROM dbo.tb_Temp_Dve INNER JOIN             
             dbo.Importer ON dbo.tb_Temp_Dve.idImporter = dbo.Importer.id
			 WHERE dbo.tb_Temp_Dve.md5 = '" . $selectiona[$i]."'";

   $rs = odbc_exec($db, $strSQL);
   
   $modalidade = odbc_result($rs, "modalidade");
   $idDVE	   = odbc_result($rs, "idDve");
   $dataEmb	   = TrataData1(odbc_result($rs, "dt_Embarque"), 2);
   $dataVenc   = TrataData1(odbc_result($rs, "dt_Vencimento"), 2);
   $valorEmb   = number_format(odbc_result($rs, "vl_embarque"), 2, ',','.');
   $proex      = number_format(odbc_result($rs, "proex"), 2, ',', '.');
   $ace		   = number_format(odbc_result($rs, "ace"), 2, ',', '.');	
   $idBuyer    = odbc_result($rs, "idImporter");
   $fatura     = odbc_result($rs, "fatura"); 
   $idCountry  = odbc_result($rs, "idCountry");
   
   
   if ($modalidade == 2 || $modalidade == 1) {
   		require_once("includeImporter.php");   
   }elseif ($modalidade == 3) {   
   } 
}

   $SQL = "DELETE FROM tb_Temp_Dve WHERE idInform='$idInform' AND idDve='$idDVE' AND numDve='$andve'";
   odbc_exec($db, $SQL);				

?>