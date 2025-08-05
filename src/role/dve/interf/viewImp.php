<?php #
	# Pagina que visualiza todos os importadores que não existe no sistema 
	# ou estão diferente no segurado.
	# Criado por Tiago V N - Elumini - 13/11/2006
	#


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


$strSQL = "SELECT * FROM Inform WHERE id = $idInform";
$rs = odbc_exec($db, $strSQL);
$name = odbc_result($rs, "name");
$start =  TrataData1(odbc_result($rs, "startValidity"), 2);
$end   = TrataData1(odbc_result($rs, "endValidity"), 2);
	
echo "<br><br>";	
echo "Segurado: $name<br>";
echo "Apólice n°: $apolice<br>";
echo "Vigência: $start à $end<br>";
echo "Período de Declaração: $inicio à $fim (".intval($andve)."ª DVE)<br>";

?>
<script language="javascript">
<!-- 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
  } 
  
  function seleciona(){
  	if (document.all.mult_ckeck.checked){
		ticar(document.form, true);
	}else{
		ticar(document.form, false);
	}
  	
  }
  
  function enviar(){
	document.all.comm.value='importDve';
	form.submit();    
  } 	
  function voltar_env(){
	document.all.comm.value='voltar';
	form.submit();  
  }
//--> 
</script>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td colspan="4">&nbsp;</td>
</tr>	
<tr>
	<td colspan="4">&nbsp;</td>
</tr>	
<tr>
	<td colspan="4" align="center">Relações de Importadores para DVE</td>
</tr>	
<tr>
	<td colspan="4">&nbsp;</td>
</tr>	
<form action="<?php echo  $root;?>role/dve/Dve.php#tabela" name="form" method="post">
<!--<input type="hidden" name="comm" value="importDve">-->
<input type="hidden" name="comm" value="">
<input type="hidden" name="idInform" value="<?php echo $idInform;?>">
<input type="hidden" name="andve" value="<?php echo $andve;?>">
<input type="hidden" name="client" value="1">
<input type="hidden" name="primeira_tela" value="1">
<input type="hidden" name="idDVE" value="<?php echo $idDVE;?>">
<input type="hidden" name="numDVE" value="<?php echo $andve;?>">

<tr class="bgAzul">
	<td style="display:none"><input type="checkbox" name="mult_ckeck" onClick="seleciona()"></td>
	<td>Nº</td>	
	<td>Arquivo de importação</td>
	<td>Base de dados SBCE</td>
</tr>

<?php $arrImportadorErro = array();	
	$j = 0;
	$z = 0;
	$n = 0;
	$ncor = 0;
	$ncor1 = 0;	
	$strSQL1 = "SELECT *, convert(varchar, nome) as nm FROM tb_Temp_Dve WHERE idInform ='$idInform' AND idDve='$idDVE' AND numDve='$andve'";
	$rs1 = odbc_exec($db, $strSQL1);
	while(odbc_fetch_row($rs1)) {
    	$arrReplace = array("%A0", "%2F", "%26", "%2C");
    	$busca = str_replace($arrReplace ,"",str_replace("+"," ",UrlEncode(odbc_result($rs1, "nome"))));
    /*
    	$nomeImportador = explode(" ", odbc_result($rs1, "nome"));
		if ($nomeImportador[1] == "SA") {
			$busca = $nomeImportador[0];
		}else{
			$busca = $nomeImportador[0]." ".$nomeImportador[1];
		}
	*/
		
        $strSQL = "SELECT * FROM Importer WHERE idInform = '$idInform' AND
		state <> 3 AND state <> 9 AND name like '".trim($busca)."%'";
		
		//echo $strSQL."<br>";
		$rs = odbc_exec($db, $strSQL);        
		
		if (odbc_result($rs, "name")!="") {

        //echo trim(odbc_result($rs, "name"));

		 if (trim(odbc_result($rs, "name")) == trim($busca)){

	  	  	if ($ncor == 0) {
				$cor = "#CCCCCC";
				$ncor = 1;
			}else{
			    $cor = "#FFFFFF";
	  			$ncor = 0;
		    }
	    	$strUSQL = "UPDATE tb_Temp_Dve SET idImporter='".odbc_result($rs, "id")."' WHERE id='".odbc_result($rs1, "id")."'";
		    $urs = odbc_exec($db, $strUSQL);
		    $j++;
		    echo "<tr bgcolor='".$cor."'>";						  
		    echo "<td style='display:none'><input type='checkbox' name='selectiona[]' value='".odbc_result($rs1, "md5")."' checked></td>";
		    echo "<td align='center'>$j</td>";
		    echo "<td>".odbc_result($rs1, "nome")."</td>";			  
		    echo "<td>".odbc_result($rs, "name")."</td>";			  
		    echo "</tr>";
		  }else{
		 	//Quando existi o importador pareceido no sistema da SBCE	   
		   if(trim(odbc_result($rs1, "nome"))==trim($nome_imp_arquivo)){ 					
	   		   	$nome_imp_arquivo = odbc_result($rs1, "nome");   		       
  		   }else{	  
			  if ($ncor1 == 0) {
			 	$cor1 = "#CCCCCC";
  				$ncor1 = 1;
			  }else{
				$cor1 = "#FFFFFF";
				$ncor1 = 0;
			  }			
			  $n++;
			  $tabela1 .= "
			  <tr bgcolor='".$cor1."'>
			  <td>$n</td>
			  <td>".odbc_result($rs1, "nome")."</td>".
			  "<td>".odbc_result($rs, "name")."</td>".
			  "</tr>";		 		  
			  $nome_imp_arquivo = odbc_result($rs1, "nome");   		       
			}  
 		  }	
		}else{
		 	//Quando não existir o importador no sistema da SBCE
		   if(trim(odbc_result($rs1, "nome"))==trim($nome_imp_arquivo1)){ 					
	   		   	$nome_imp_arquivo1 = odbc_result($rs1, "nome");   		       
  		   }else{	  

	  		  if ($ncor2 == 0) {
				$cor2 = "#CCCCCC";
  			    $ncor2 = 1;
		      }else{
			    $cor2 = "#FFFFFF";
			    $ncor2 = 0;
		      }			
			  $z++;
			  $tabela2 .= "
			  <tr bgcolor='".$cor2."'>
			  <td>$z</td>
			  <td>".odbc_result($rs1, "nome")."</td>".
			  "</tr>";
			  $nome_imp_arquivo1 = odbc_result($rs1, "nome");   		       
		   }	  			
		 }  
	}
?>
<tr>
	<td colspan="4">&nbsp;</td>
</tr>
<tr>
	<td colspan="4" align="center">
	   	<input type="button" onClick="voltar_env()" value="Voltar" class="sair">	   	
	 <?php if ($tabela1=="") {
	 ?>
		<input type="button" onclick="enviar()" name="processar" value="Processar" class="servicos">
	 <?php }
	 ?>	
	</td>
</tr>	
</form>
<tr>
	<td colspan="4">&nbsp;</td>
</tr>	
<tr>
	<td colspan="4">&nbsp;</td>
</tr>	
</table>

<?php if ($tabela1!="") {
?>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td colspan="3" align="center">&nbsp;</td>
</tr>	
<tr>
	<td colspan="3" align="center">ERRO: Nome encontrado no arquivo de importação diferente do cadastrado na SBCE.<br>
	Favor, corrigir o nome do(s) seguinte(s) importador(es).</td>
</tr>	
<tr>
	<td colspan="2">&nbsp;</td>
</tr>	
<tr class="bgAzul">
	<td>Nº</td>	
	<td>Arquivo de importação</td>
	<td>Base de dados SBCE</td>
</tr>
<?php if ($tabela1!="") {
	echo $tabela1;
}else{
	echo "<tr>";
	echo "<td colspan='2'>Não existem importador</td>";
	echo "</tr>";	
}	
?>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
</table>
<?php }
	
	if ($tabela2!="") {
?>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td colspan="2" align="center">Relações de Importadores não cadastrado no Sistema</td>
</tr>	
<tr>
	<td colspan="2">&nbsp;</td>
</tr>	
<tr>
	<td colspan="2" align="center">ATENÇÃO: Os importadores abaixo não serão aceitos pelo sistema.<br>Importadores sem limite de crédito ativo na data do embarque.</td>
</tr>	
<tr>
	<td colspan="2">&nbsp;</td>
</tr>	
<tr class="bgAzul">
	<td>Nº</td>	
	<td>Nome Importador</td>
</tr>
<?php if ($tabela2!="") {
	echo $tabela2;
}else{
	echo "<tr>";
	echo "<td colspan='2'>Não existem importador incorreto</td>";
	echo "</tr>";	
}	
?>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
</table>
<?php } ?>
