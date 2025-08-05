<?php 
/*
 * Created on 20/04/2007 by Tiago V N
 * 
 * Analista Desenvolvedor Pleno
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 
 */

require_once("../../pdfConf.php");

$reltipo = $_REQUEST['reltipo'];



if ($reltipo == "inform") {

  $cur = odbc_exec($db, "SELECT * FROM Inform WHERE id = '$idInform'");

  if (odbc_fetch_row($cur)){  	  	
  	
  	if (odbc_result($cur, "warantyInterest")== 1) {  		
  		$cobertura = "SIM";
  	}else{
  		$cobertura = "NÃO";
  	}
  	
	
  	if (odbc_result($cur, "pvigencia")== 1) {
  		$pvigencia = "12 Meses";
  	}else if (odbc_result($cur, "pvigencia") == 2){
		$pvigencia = "24 Meses";  		  		
  	}else{
  		$pvigencia = "12 Meses";  		
  	}
	
	if(odbc_result($cur, "Periodo_Vigencia")){
	    $pvigencia = odbc_result($cur, "Periodo_Vigencia") . " Meses"; 
	}
  	
  	if (odbc_result($cur, "Obs") == null) {
  		$obs = "";
  	}else{
  		$obs = odbc_result($cur, "Obs");  		
  	}	
  	
  	if(odbc_result($cur, "sameAddress") == 0) {  		   
    	$numcob = odbc_result($cur, 'chargeAddressNumber');
    	$complcob = odbc_result($cur, 'chargeAddressComp');
		$cidadecob = odbc_result($cur, 'chargeCity');
		$endcob = odbc_result($cur, 'chargeAddress');
		$cepcob = odbc_result($cur, 'chargeCep');
		$endabrevcob = odbc_result($cur, 'addressAbrev');	
  	}else{  	
  		$numcob = "";
    	$complcob = "";
		$cidadecob = "";
		$endcob = "";
		$cepcob = "";
		$endabrevcob = "";
    }
  	
  	$sql = "SELECT id, description FROM Sector WHERE id = '".odbc_result($cur, "idSector")."' ORDER BY description";
  	$cur1 = odbc_exec($db, $sql);
  	odbc_fetch_row($cur1);
  	$setor = odbc_result($cur1, "description");
  	
  	if (odbc_result($cur, "warantyExp")) {
  		
  		$obj = "[X] Garantia à exportação";
  	}else{
  		$obj = "[  ] Garantia à exportação";
  	}
  	 
    if(odbc_result($cur, "warantyFin")){
    	$obj .="\n[X] Garantia para financiamentos à exportação";
    }else{
    	$obj .= "\n[  ] Garantia para financiamentos à exportação";
    } 
    
    if (odbc_result($cur, "hasAnother")){
    	$obj .= "\n[X] Outros";
    }else{
    	$obj .= "\n[  ] Outros";
    } 
  	
  	/*
  	
  	$prefix = md5($idInform);
  	 	   	
  	$h = new Java ('java.util.HashMap');
			
  	$h->put("key", $pdfDir.$prefix."Informe.pdf");
    $h->put("dir", $pdfDir);
          	 
    $h->put("naf", "".odbc_result($cur, "naf")."");
    $h->put("siren", "".odbc_result($cur, "siren")."");
    $h->put("questionaire", "".odbc_result($cur, "quest")."");
    $h->put("napce", "".odbc_result($cur, "napce")."");
    $h->put("dossier", "".odbc_result($cur, "dossier")."");
    $h->put("contrat", "".odbc_result($cur, "contrat")."");

    $h->put("cobertura", "".$cobertura."");
    $h->put("periodo", "".$pvigencia."");
    
    $h->put("obs", "".$obs."");
 
    $h->put("nome", "".odbc_result($cur, "name")."");
    $h->put("endereco", "".odbc_result($cur, "address")."");
    $h->put("numero", "".odbc_result($cur, "addressNumber")."");
    $h->put("complemento", "".odbc_result($cur, "addressComp")."");
    $h->put("cidade", "".odbc_result($cur, "city")."");
    $h->put("cep", "".odbc_result($cur, "cep")."");
    $h->put("telefone", "".odbc_result($cur, "tel")."");
    $h->put("fax", "".odbc_result($cur, "fax")."");
    $h->put("eempresa", "".odbc_result($cur, "email")."");
    $h->put("contato", "".odbc_result($cur, "contact")."");
    $h->put("econtato", "".odbc_result($cur, "emailContact")."");
    $h->put("responsavel", "".odbc_result($cur, "respName")."");
    $h->put("cargo", "".odbc_result($cur, "ocupation")."");
    $h->put("cnpj", "".odbc_result($cur, "cnpj")."");
    $h->put("ie", "".odbc_result($cur, "ie")."");
    
    $h->put("endcob", "".$endcob."");
    $h->put("endabrev", "".$endabrevcob."");
    $h->put("numcob", "".$numcob."");
    $h->put("complcob", "".$complcob."");
    $h->put("cidadecob", "".$cidadecob."");
    $h->put("cepcob", "".$cepcob."");
    
  
    $h->put("setor", "".$setor."");
    $h->put("pexportados", "".odbc_result($cur, "products")."");
    $h->put("prazo", odbc_result($cur, "frameMed")." dias");
    $h->put("exporta", odbc_result($cur, "exportMore") ? "Sim" : "Não");
    $h->put("grupo", odbc_result($cur, "hasGroup") ? "Sim" : "Não");
    $h->put("tipogrupo", odbc_result($cur, "companyGroup"));
    $h->put("associada", odbc_result($cur, "hasAssocCompanies") ? "Sim" : "Não");
    $h->put("tipoassociada", "".odbc_result($cur, "associatedCompanies")."");
    $h->put("itens", "".$obj."");
    $h->put("outros", "".odbc_result($cur, "another")."");
        
    $prop = new Java("Informe", $h);
				
    if($prop == null){
      	die("<h1>Informações Gerais null</h1>");
    }else{
 	  	
    	$loc = '/siex/src/download/'.$prefix.'Informe.pdf';			 
		echo "<script>window.open('phpdf.php?loc=$loc','_blank')</script>";	    
        $prop->generate();
    }
    
	*/
		echo "<script>location.href='ListClient.php?comm=view&idInform=$idInform'</script>";
            
  }//Verificação se existe dados na consulta

/*
 * Fim - Gerar Informações Gerais
 */  
}else if($reltipo == "informI"){
			
		
	$cur=odbc_exec(
    	$db,
    	"SELECT * FROM Inform WHERE id = ".$field->getField("idInform")
    	);
    if (odbc_result($cur, 'currency') == 1) {
      $ext = "R$";
    }else  if (odbc_result($cur, 'currency') == 2) {
      $ext = "US$";
    }else if (odbc_result($cur, 'currency') == 6) {
       $ext = "€";
    }
	
	$name = odbc_result($cur, "name");  
  	  	
  	$cur1=odbc_exec(
    	$db,
    	"SELECT * FROM Volume WHERE idInform = ".$field->getField("idInform")
  		);
  		  	
  		
  	if (odbc_fetch_row($cur1)) {
  		  		  		
  		$prefix = md5($field->getField("idInform"));
  	 	/*   			  	 	   	
  		$h = new Java ('java.util.HashMap');
			
  		$h->put("key", $pdfDir.$prefix."InformQuadroI.pdf");
    	$h->put("dir", $pdfDir);
          	 
    	$h->put("nome", $name);    	    	
    	
    	$h->put("valor1", "".number_format(odbc_result($cur1, "vol1"), 2,",", ".")."");
    	$h->put("valor2", "".number_format(odbc_result($cur1, "vol2"), 2,",", ".")."");
    	$h->put("valor3", "".number_format(odbc_result($cur1, "vol6"), 2,",", ".")."");
    	$h->put("valor4", "".number_format(odbc_result($cur1, "vol8"), 2,",", ".")."");
    	$h->put("valor5", "".number_format(odbc_result($cur1, "vol9"), 0,",", ".")."");
    	$h->put("valor6", "".number_format(odbc_result($cur1, "vol10"), 0,",", ".")."");
    	$h->put("valor7", "".number_format(odbc_result($cur1, "vol14"), 0,",", ".")."");
    	$h->put("valor8", "".number_format(odbc_result($cur1, "vol16"), 0,",", ".")."");	    
	    $h->put("valor9", "".number_format(odbc_result($cur1, "vol17"), 2,",", ".").""); 
	    $h->put("valor10", "".number_format(odbc_result($cur1, "vol18"), 2,",", ".")."");	    	    
	    $h->put("valor11", "".number_format(odbc_result($cur1, "vol22"), 2,",", ".")."");
	    $h->put("valor12", "".number_format(odbc_result($cur1, "vol24"), 2,",", ".")."");
	    $h->put("valor13", "".number_format(odbc_result($cur1, "vol25"), 2,",", ".")."");
	    $h->put("valor14", "".number_format(odbc_result($cur1, "vol26"), 2,",", ".")."");
	    $h->put("valor15", "".number_format(odbc_result($cur1, "vol30"), 2,",", ".")."");
	    $h->put("valor16", "".number_format(odbc_result($cur1, "vol32"), 2,",", ".")."");
	    $h->put("valor17", "".number_format(odbc_result($cur1, "vol33"), 2,",", ".")."");
	    $h->put("valor18", "".number_format(odbc_result($cur1, "vol34"), 2,",", ".")."");
	    $h->put("valor19", "".number_format(odbc_result($cur1, "vol38"), 2,",", ".")."");
	    $h->put("valor20", "".number_format(odbc_result($cur1, "vol40"), 2,",", ".")."");
	    $h->put("moeda", "".$ext."");
        
	    $prop = new Java("InformQuadroI", $h);
				
	    if($prop == null){
    	  	die("<h1>INFORME - Quadro I</h1>");
    	}else{
	
    		$loc = '/siex/src/download/'.$prefix.'InformQuadroI.pdf';
			
			echo "<script>window.open('phpdf.php?loc=$loc','_blank')</script>";

        	$prop->generate();
    	}
		*/
  		
  		echo "<script>location.href='ListClient.php?comm=view&idInform=$idInform'</script>";
  	}
/*
 * Fim do Quandro I
 */  	
}else if($reltipo == "informII"){
	//$list = new Java("java.util.ArrayList");
	if(! $list){
		echo "Erro na list<br>\n";
	}
	
	//$list_aux = new Java('java.util.ArrayList');
	if(! $list_aux){
	   echo "Erro na list_aux<br>\n";
	}	
		$cur=odbc_exec(
			$db,
			"SELECT * FROM Inform WHERE id = ".$field->getField("idInform")
			);
	
		$name = odbc_result($cur, "name");  
		if (odbc_result($cur, 'currency') == 1) {
		  $ext = "R$";
		}else if (odbc_result($cur, 'currency') == 2) {
		  $ext = "US$";
		}else if (odbc_result($cur, 'currency') == 6) {
		   $ext = "€";
		}
		
		$cur=odbc_exec(
			$db,
			"SELECT vol2 + vol3 + vol4 FROM Volume WHERE idInform = ".$field->getField("idInform")
		);
		
		if (odbc_fetch_row($cur)){
		
		$cur1=odbc_exec(
			$db,
			"SELECT cat, valExp, name, buyers, VolumeSeg.id, expMax FROM VolumeSeg JOIN Country ON (idCountry = Country.id) WHERE idInform = ".$field->getField("idInform")
		);
		$total = 0;
		$totComp = 0;
			
		while (odbc_fetch_row($cur1)) {
			$total += odbc_result($cur1,2);
			$totComp += odbc_result($cur1,4);
		
			//$list_aux = new Java('java.util.ArrayList');
			if(! $list_aux){
				echo "Erro na list_aux<br>\n";
			}
			/*
			$list_aux->add(sprintf("%d", odbc_result($cur1,2)));
			$list_aux->add(odbc_result($cur1,3));
			$list_aux->add(odbc_result($cur1,4));
			
			
			$list->add($list_aux);
			*/
		}	
					
			//$list->add("");
			/*
			$prefix = md5($field->getField("idInform"));
									
			$h = new Java ('java.util.HashMap', 25);
				
			$h->put("key", $pdfDir.$prefix."InformQuadroII.pdf");
			$h->put("dir", $pdfDir);          	 
			$h->put("nome", "".$name."");    	    	    	
			$h->put("list", $list);    	
			$h->put("total1", "".number_format($total, 2, ",", ".")."");
			$h->put("total2", "$totComp");
			$h->put("moeda", "".$ext."");
			
			$prop = new Java("InformQuadroII", $h);
					
			if($prop == null){
				die("<h1>INFORME - Quadro II</h1>");
			}else{
			
				$loc = '/siex/src/download/'.$prefix.'InformQuadroII.pdf';
				echo "<script>window.open('phpdf.php?loc=$loc','_blank')</script>";
				
				$prop->generate();
			}
			*/
			
			echo "<script>location.href='ListClient.php?comm=view&idInform=$idInform'</script>";
		
		}
  	
}else if($reltipo == "informIII"){

	//$list = new Java("java.util.ArrayList");
	if(! $list){
		echo "Erro na list<br>\n";
	}
	
	//$list_aux = new Java('java.util.ArrayList');
	if(! $list_aux){
	   echo "Erro na list_aux<br>\n";
	}	
		$cur=odbc_exec(
			$db,
			"SELECT * FROM Inform WHERE id = ".$field->getField("idInform")
			);
	
		$name = odbc_result($cur, "name");  
		if (odbc_result($cur, 'currency') == 1) {
		  $ext = "R$";
		}else if (odbc_result($cur, 'currency') == 2) {
		  $ext = "US$";
		}else if (odbc_result($cur, 'currency') == 6) {
		   $ext = "€";
		}
		
	
		$cur = odbc_exec($db,
			   "SELECT imp.name, address, risk, city, c.name, tel, prevExp12, limCredit, numShip12, periodicity, ".
			   "przPag, imp.id, imp.hold, cep, fax, contact, relation, seasonal FROM Importer imp JOIN Country c ON ".
			   "(idCountry = c.id) WHERE idInform = $idInform AND state <> 7 AND state <> 15 AND state <> 8 and ".
			   "state <> 9 and imp.id not in (select distinct idImporter from ImporterRem) ORDER BY imp.id");
		 $i = 1; 	
		 while (odbc_fetch_row($cur)) {
			
			/*
			$list_aux = new Java('java.util.ArrayList');
			if(! $list_aux){
				echo "Erro na list_aux<br>\n";
			}
			
			$list_aux->add($i);
			$list_aux->add(odbc_result($cur, 1));
			$list_aux->add(odbc_result($cur, 2));
			
			if ( (odbc_result($cur, 6) == "") || (odbc_result($cur, 6) == "-") || 
					(odbc_result($cur, 15)=="") || (odbc_result($cur, 15) == "-")  ) {
						$list_aux->add("");
			}else{
						$list_aux->add(odbc_result($cur, 6)."/".odbc_result($cur, 15) );
			}	
			
			//$list_aux->add(odbc_result($cur, 15));		
			if (odbc_result($cur, 18)) {
				$list_aux->add("SIM"); //	
			}else{
				$list_aux->add("NÃO"); //
			}
			
			if (odbc_result($cur, 17) == "") {
				$list_aux->add("");
			}else{
				$list_aux->add(odbc_result($cur, 17));
			}
			
			$list_aux->add(number_format(odbc_result($cur,7),2,",","."));		
			$list_aux->add(odbc_result($cur,9));
			$list_aux->add(odbc_result($cur,8)/1000);
			$list_aux->add(odbc_result($cur,10));
			$list_aux->add(odbc_result($cur,11));
			
			$list->add($list_aux);
			*/
			$i++;		      
		}	
						
			$prefix = md5($field->getField("idInform"));
									
			//$h = new Java ('java.util.HashMap', 25);
			/*	
			$h->put("key", $pdfDir.$prefix."InformQuadroIII.pdf");
			$h->put("dir", $pdfDir);          	 
			$h->put("nome", "".$name."");    	    	    	
			$h->put("list", $list);    	
			$h->put("moeda", "".$ext."");
			
			$prop = new Java("InformQuadroIII", $h);
					
			if($prop == null){
				die("<h1>INFORME - Quadro III</h1>");
			}else{
		
				$loc = '/siex/src/download/'.$prefix.'InformQuadroIII.pdf';
				
				echo "<script>window.open('phpdf.php?loc=$loc','_blank')</script>";
				
				$prop->generate();
			}
			*/
			echo "<script>location.href='ListClient.php?comm=view&idInform=$idInform'</script>";		
					 
	}else{
	
   }    
?>
