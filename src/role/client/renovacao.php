<?php  // alterado Hicom (Gustavo) - 11/01/05 - campos novos da tabela Inform e Importer que podem gerar erro na renovação

$idInform = $field->getField("idInform");

// verifica se já existe um informe de renovacao
$sql = "select top 1 * from Inform where idAnt=$idInform";

$cur = odbc_exec($db,$sql);

if(! odbc_fetch_row($cur)){ // nao existe

  odbc_autocommit ($db, false);
  $ok = true;

  // Renova o informe
  $cur = odbc_exec($db, "select * from Inform where id=$idInform");

  if(odbc_fetch_row($cur)){
	
    if (odbc_result($cur, "warantyInterest")==""){
        $warantyInterest = 0;
    }else{
        $warantyInterest = "'".odbc_result($cur, "warantyInterest")."'";
    }
    
    $idInsured       = odbc_result($cur, "idInsured");

    if (odbc_result($cur, "idUser")==""){
         $idUser = "null";
    }else{
         $idUser          = "'".odbc_result($cur, "idUser")."'";
    }

    $state = '1';

    if (odbc_result($cur, "respName")==""){
         $respName        = "null";
    }else{
         $respName        = "'".odbc_result($cur, "respName")."'";
    }

    if (odbc_result($cur, "ocupation")==""){
        $ocupation = "null";
    }else{
        $ocupation       = "'".odbc_result($cur, "ocupation")."'";
    }

    if (odbc_result($cur, "name")==""){
          $name            = "null";
    }else{
          $name            = "'".odbc_result($cur, "name")."'";
    }

    if (odbc_result($cur, "idSector")==""){
          $idSector        = "null";
    }else{
          $idSector        = "'".odbc_result($cur, "idSector")."'";
    }

    if (odbc_result($cur, "naf")==""){
          $naf             = "null";
    }else{
          $naf             = "'".odbc_result($cur, "naf")."'";
    }

    if (odbc_result($cur, "napce")=="") {
          $napce           = "null";
    }else{
          $napce           = "'".odbc_result($cur, "napce")."'";
    }
    
    if (odbc_result($cur, "siren")=="") {
          $siren           = "null";
    }else{
          $siren           = "'".odbc_result($cur, "siren")."'";
    }

    if (odbc_result($cur, "dossier")=="") {
          $dossier         = "null";
    }else{
          $dossier         = "'".odbc_result($cur, "dossier")."'";
    }
    
    if (odbc_result($cur, "quest")==""){
          $quest           = "null";
    }else{
          $quest           = "'".odbc_result($cur, "quest")."'";
    }
    
    if (odbc_result($cur, "contrat")==""){
          $contrat         = "null";
    }else{
          $contrat         = "'".odbc_result($cur, "contrat")."'";
    }

    if (odbc_result($cur, "executive")=="") {
          $executive       = "null";
    }else{
          $executive       = "'".odbc_result($cur, "executive")."'";
    }

    if (odbc_result($cur, "comRisk")==""){
          $comRisk         = "null";
    }else{
          $comRisk         = "'".odbc_result($cur, "comRisk")."'";
    }
    
    if (odbc_result($cur, "polRisk")==""){
         $polRisk         = "null";
    }else{
         $polRisk         = "'".odbc_result($cur, "polRisk")."'";
    }

    if (odbc_result($cur, "address")==""){
         $address         = "null";
    }else{
         $address         = "'".odbc_result($cur, "address")."'";
    }
    
    if (odbc_result($cur, "city")==""){
         $city            = "null";
    }else{
         $city            = "'".odbc_result($cur, "city")."'";
    }
    
    if (odbc_result($cur, "uf")==""){
          $uf              = "null";
    }else{
          $uf              = "'".odbc_result($cur, "uf")."'";
    }

    if (odbc_result($cur, "cep")==""){
          $cep             = "null";
    }else{
          $cep             = "'".odbc_result($cur, "cep")."'";
    }

    if (odbc_result($cur, "tel")==""){
          $tel            = "null";
    }else{
          $tel            = "'".odbc_result($cur, "tel")."'";
    }

    if (odbc_result($cur, "fax")==""){
          $fax             = "null";
    }else{
          $fax             = "'".odbc_result($cur, "fax")."'";
    }

    if (odbc_result($cur, "email")==""){
          $email           = "null";
    }else{
          $email           = "'".odbc_result($cur, "email")."'";
    }

    if (odbc_result($cur, "contact")==""){
          $contact         = "null";
    }else{
          $contact         = "'".odbc_result($cur, "contact")."'";
    }

    if (odbc_result($cur, "ocupationContact")==""){
          $ocupationContact   = "null";
    }else{
          $ocupationContact   = "'".odbc_result($cur, "ocupationContact")."'";
    }

    if (odbc_result($cur, "emailContact")==""){
          $emailContact    = "null";
    }else{
          $emailContact    = "'".odbc_result($cur, "emailContact")."'";
    }

    if (odbc_result($cur, "cnpj")==""){
          $cnpj            = "null";
    }else{
          $cnpj            = "'".odbc_result($cur, "cnpj")."'";
    }

    if (odbc_result($cur, "ie")==""){
          $ie              = "null";
    }else{
          $ie              = "'".odbc_result($cur, "ie")."'";
    }
    
    if (odbc_result($cur, "products")==""){
         $products        = "null";
    }else{
         $products        = "'".odbc_result($cur, "products")."'";
    }
    
    if (odbc_result($cur, "sazSales")==""){
         $sazSales        = "null";
    }else{
         $sazSales        = "'".odbc_result($cur, "sazSales")."'";
    }

    if (odbc_result($cur, "fatDom")==""){
       $fatDom = "null";
    }else{
       $fatDom = "'".odbc_result($cur, "fatDom")."'";
    }

    if (odbc_result($cur, "fatExp")==""){
       $fatExp = "null";
    }else{
       $fatExp          = "'".odbc_result($cur, "fatExp")."'";
    }
    
    if (odbc_result($cur, "frameMed")==""){
         $frameMed        = "null";
    }else{
         $frameMed        = "'".odbc_result($cur, "frameMed")."'";
    }

    if (odbc_result($cur, "frameMin")==""){
         $frameMin        = "null";
    }else{
         $frameMin        = "'".odbc_result($cur, "frameMin")."'";
    }
    
    if (odbc_result($cur, "hasGroup")==""){
         $hasGroup        = "null";
    }else{
         $hasGroup        = odbc_result($cur, "hasGroup");
    }

    if (odbc_result($cur, "companyGroup")==""){
          $companyGroup    = "null";
    }else{
          $companyGroup    = "'".odbc_result($cur, "companyGroup")."'";
    }

    if (odbc_result($cur, "hasAssocCompanies")==""){
          $hasAssocCompanies = "null";
    }else{
          $hasAssocCompanies = "'".odbc_result($cur, "hasAssocCompanies")."'";
    }
    
    if (odbc_result($cur, "associatedCompanies")==""){
          $associatedCompanies = "null";
    }else{
          $associatedCompanies = "'".odbc_result($cur, "associatedCompanies")."'";
    }
    
    if (odbc_result($cur, "creditOwnDep")==""){
          $creditOwnDep    = "null";
    }else{
          $creditOwnDep    = "'".odbc_result($cur, "creditOwnDep")."'";
    }

    if (odbc_result($cur, "warantyExp")==""){
          $warantyExp      = "null";
    }else{
          $warantyExp      = "'".odbc_result($cur, "warantyExp")."'";
    }
    
    if (odbc_result($cur, "warantyFin")==""){
        $warantyFin      = "null";
    }else{
        $warantyFin      = "'".odbc_result($cur, "warantyFin")."'";
    }
    
    if (odbc_result($cur, "hasAnother")==""){
        $hasAnother      = "null";
    }else{
        $hasAnother      = "'".odbc_result($cur, "hasAnother")."'";
    }
    
    if (odbc_result($cur, "another")==""){
        $another         = "null";
    }else{
        $another         = "'".odbc_result($cur, "another")."'";
    }

    if (odbc_result($cur, "ace")==""){
          $ace        ="null";
    }else{
          $ace             = "'".odbc_result($cur, "ace")."'";
    }
    
    if (odbc_result($cur, "proex")==""){
          $proex           = "null";
    }else{
          $proex           = "'".odbc_result($cur, "proex")."'";
    }

    if (odbc_result($cur, "idRegion") == ""){
          $idRegion        = "null";
    }else{
          $idRegion        = "'".odbc_result($cur, "idRegion")."'";
    }
    
    if (odbc_result($cur, "sameAddress")==""){
          $sameAddress     = "null";
    }else{
          $sameAddress     = "'".odbc_result($cur, "sameAddress")."'";
    }

    if (odbc_result($cur, "chargeAddress")==""){
          $chargeAddress   = "null";
    }else{
          $chargeAddress   = "'".odbc_result($cur, "chargeAddress")."'";
    }

    if (odbc_result($cur, "chargeCity")==""){
          $chargeCity      = "null";
    }else{
          $chargeCity      = "'".odbc_result($cur, "chargeCity")."'";
    }

    if (odbc_result($cur, "chargeCep")==""){
          $chargeCep       = "null";
    }else{
          $chargeCep       = "'".odbc_result($cur, "chargeCep")."'";
    }
    
    if (odbc_result($cur, "chargeUf")==""){
          $chargeUf        = "null";
    }else{
          $chargeUf        = "'".odbc_result($cur, "chargeUf")."'";
    }

    if (odbc_result($cur, "nick")==""){
         $nick            = "null";
    }else{
         $nick            = "'".odbc_result($cur, "nick")."'";
    }
    
    if (odbc_result($cur, "i_Seg")==""){
         $i_Seg           = "null";
    }else{
         $i_Seg           = "'".odbc_result($cur, "i_Seg")."'";
    }
    
    $endosso         = "'0'";

    if (odbc_result($cur, "exportMore")==""){
         $exportMore      = "null";
    }else{
         $exportMore      = "'".odbc_result($cur, "exportMore")."'";
    }

    if (odbc_result($cur, "telContact")==""){
        $telContact      = "null";
    }else{
        $telContact      = "'".odbc_result($cur, "telContact")."'";
    }

    if (odbc_result($cur, "faxContact")==""){
        $faxContact      = "null";
    }else{
        $faxContact      = "'".odbc_result($cur, "faxContact")."'";
    }
    
    if (odbc_result($cur, "id")==""){
        $idAnt           = "null";
    }else{
        $idAnt           = "'".odbc_result($cur, "id")."'";

/*
		$xname = odbc_result($cur, "name");
		$sql = "SELECT id FROM Inform WHERE name = '".$xname."' AND state IN (10,11) AND datediff(d, endValidity, getdate()) <= 90 ";
        $xcur = odbc_exec($db, $sql);
		if(odbc_fetch_row($xcur)) {
			$idAnt = "'".odbc_result($xcur, "id")."'";
		} else {
			$idAnt = "null";
		}
*/

    }

    $codProd         = "'0'";

    if (odbc_result($cur, "addressAbrev")==""){
        $addressAbrev    = "null";
    }else{
        $addressAbrev    = "'".odbc_result($cur, "addressAbrev")."'";
    }

    $notificaRenova  = "'N'";

    if (odbc_result($cur, "addressNumber")==""){
          $addressNumber   = "null";
    }else{
          $addressNumber   = "'".odbc_result($cur, "addressNumber")."'";
    }
    
    if (odbc_result($cur, "chargeAddressNumber")==""){
          $chargeAddressNumber = "null";
    }else{
          $chargeAddressNumber = "'".odbc_result($cur, "chargeAddressNumber")."'";
    }

    if (odbc_result($cur, "addressComp")==""){
          $addressComp         = "null";
    }else{
          $addressComp         = "'".odbc_result($cur, "addressComp")."'";
    }

    if (odbc_result($cur, "chargeAddressComp")==""){
          $chargeAddressComp   = "null";
    }else{
          $chargeAddressComp   = "'".odbc_result($cur, "chargeAddressComp")."'";
    }

    if (odbc_result($cur, "i_Gerente_Relacionamento")==""){
          $i_GerenteR   = "null";
    }else{
          $i_GerenteR   = "'".odbc_result($cur, "i_Gerente_Relacionamento")."'";
    }
    
/*
    $query = "insert into Inform (warantyInterest,idInsured,idUser,state,respName,
             ocupation,name,idSector,naf,napce,siren,dossier,quest,contrat,
             executive,comRisk,polRisk,address,city,uf,cep,tel,fax,email,
             contact,ocupationContact,emailContact,cnpj,ie,products,sazSales,
             fatDom,fatExp,frameMed,frameMin,hasGroup,companyGroup,hasAssocCompanies,
             associatedCompanies,creditOwnDep,warantyExp,warantyFin,hasAnother,
             another,ace,proex,idRegion,sameAddress,chargeAddress,chargeCity,
             chargeCep,chargeUf,nick,i_Seg,endosso,exportMore,telContact,faxContact,
             idAnt,codProd,addressAbrev,notificaRenova,addressNumber,chargeAddressNumber,
             addressComp,chargeAddressComp, i_Gerente, i_Gerente_Relacionamento) 
			 
			 values	 ($warantyInterest,$idInsured,
             $idUser,$state,$respName,$ocupation,$name,$idSector,$naf,$napce,
             $siren,$dossier,$quest,$contrat,$executive,$comRisk,$polRisk,
             $address,$city,$uf,$cep,$tel,$fax,$email,$contact,$ocupationContact,
             $emailContact,$cnpj,$ie,$products,$sazSales,$fatDom, $fatExp,
             $frameMed,$frameMin,$hasGroup,$companyGroup,$hasAssocCompanies,
             $associatedCompanies,$creditOwnDep,$warantyExp,$warantyFin,
             $hasAnother,$another,$ace,$proex,$idRegion,$sameAddress,
             $chargeAddress,$chargeCity,$chargeCep,$chargeUf,$nick,
             $i_Seg,$endosso,$exportMore,$telContact,$faxContact,$idAnt,
             $codProd,$addressAbrev,$notificaRenova,$addressNumber,
             $chargeAddressNumber,$addressComp,$chargeAddressComp, $i_GerenteR, $i_GerenteR)";
    
			 
*/

    $query = "Insert Into Inform (idAnt, warantyInterest, idInsured, idUser, state, respName, 
			ocupation, name, idSector, naf, napce, siren, dossier, quest, contrat,
			executive, comRisk, polRisk, address, city, uf, cep, tel, fax, email,
			contact, ocupationContact, emailContact, cnpj, ie, currency, products, sazSales,
			fatDom, fatExp, frameMed, frameMin, hasGroup, companyGroup, hasAssocCompanies,
			associatedCompanies, creditOwnDep, warantyExp, warantyFin, hasAnother,
			another, ace, proex, idRegion, sameAddress, chargeAddress, chargeCity,
			chargeCep, chargeUf, nick, i_Seg, endosso, exportMore, telContact, faxContact,
			codProd, addressAbrev, notificaRenova, addressNumber, chargeAddressNumber,
			addressComp, chargeAddressComp, i_Gerente, i_Gerente_Relacionamento, i_CNAE, i_Produto,
			Periodo_Vigencia, i_Sub_Produto, i_Ramo, i_Empresa, p_Taxa_Desagio)
		select id, warantyInterest, idInsured, idUser, 1 As state, respName, 
			ocupation, name, idSector, naf, napce, siren, dossier, quest, contrat,
			executive, comRisk, polRisk, address, city, uf, cep, tel, fax, email,
			contact, ocupationContact, emailContact, cnpj, ie, currency, products, sazSales,
			fatDom, fatExp, frameMed, frameMin, hasGroup, companyGroup, hasAssocCompanies,
			associatedCompanies, creditOwnDep, warantyExp, warantyFin, hasAnother,
			another, ace, proex, idRegion, sameAddress, chargeAddress, chargeCity,
			chargeCep, chargeUf, nick, i_Seg, endosso, exportMore, telContact, faxContact,
			codProd, addressAbrev, notificaRenova, addressNumber, chargeAddressNumber,
			addressComp, chargeAddressComp, i_Gerente_Relacionamento As i_Gerente, i_Gerente_Relacionamento, i_CNAE, i_Produto,
			Periodo_Vigencia, i_Sub_Produto, i_Ramo, i_Empresa, p_Taxa_Desagio
		From Inform Where id = $idInform";
  }
  
  if(! odbc_exec($db, $query)){
    $msg = "Erro ao inserir informe de renovação";

    $ok = false;
  }

  // pega o id do novo informe
  $c = odbc_exec($db, "select max(id) from Inform");

  if(odbc_fetch_row($c)){
	$newIdInform = odbc_result($c, 1);
  }
  

  if(! odbc_exec($db, "insert into Lost (idInform) values ($newIdInform)")){
	$msg = "Erro ao inserir entrada em Lost";
    	$ok = false;
  }
  
  if(! odbc_exec($db, "insert into Volume (idInform) values ($newIdInform)")){
	$msg = "Erro ao inserir entrada em Volume";
    	$ok = false;
  }

/*
  //Queries feitas para substituir o while abaixo que ocupava muito processamento
  $isql = "Insert into Importer
      (idInform,idInsured,name,address,idCountry,
      tel,prevExp12,limCredit,numShip12,periodicity,risk,city,przPag,
      c_Coface_Imp,state,stateDate,endosso,hold,cep,fax,contact,relation,
      expMax,seasonal,credit,creditAut,origName,origAddress,origIdCountry,
      origTel,origCity,origCep,origFax,idTwin,dt_cob_analise,dt_cob_monitor,
      nu_cob_analise,nu_cob_monitor,divulgaNome,emailContato,origPrzPag,idAprov)
     
      Select
      $newIdInform, idInsured, name, address, idCountry,
      tel, prevExp12, limCredit, numShip12, periodicity, risk, city, przPag,
      c_Coface_Imp, 1 as 'state', GetDate() as 'stateDate', endosso, 0 as 'hold', cep, fax, contact, relation,
      expMax, seasonal, credit, 1 as 'creditAut', origName, origAddress, origIdCountry,
      origTel, origCity, origCep, origFax, id As 'idTwin', dt_cob_analise, dt_cob_monitor,
      nu_cob_analise, nu_cob_monitor, 0 as 'divulgaNome', emailContato, 0 as 'origPrzPag', idAprov
      From Importer Where idInform = idInform=$idInform and state = 6 order by id ";


  $test = odbc_exec($db, $isql);


  $sql_change_credit = "Insert into ChangeCredit
  
          (idImporter, monitor, analysis, state, credit, idNotificationR, cookie,
  
          userIdChangeCredit, stateDate, creditSolic, creditTemp, limTemp)
  
    Select
  
          I.id, Case C.credit When 0 Then 0 Else 1 End As 'monitor', Case C.credit When 0 Then 0 Else 1 End As 'analysis', 1 As 'state', C.credit, idNotificationR, C.cookie,
  
          C.userIdChangeCredit, GetDate() As 'stateDate', I.limCredit As 'creditSolic', C.creditTemp, C.limTemp
  
    From ChangeCredit C
  
    Inner Join Importer I On
  
          I.idTwin = C.idImporter
  
    Where
  
          C.id In (Select max(CC.id) From ChangeCredit CC
  
    Inner Join Importer Imp On Imp.id = CC.idImporter
  
    where Imp.idInform = $idInform And Imp.state = 6 Group By Imp.id)
  
    Order by idImporter";

    $test_change = odbc_exec($db, $sql_change_credit);

*/

  // inclui os contatos no novo informe
  $cur = odbc_exec($db, "select * from Contact where idInform=$idInform order by id");

  while(odbc_fetch_row($cur)){

    $fields = $values = '';
    $idContact = odbc_result($cur, 'id');
    $num_fields = odbc_num_fields($cur);
    for($i = 1; $i <= $num_fields; $i++){
      $field_name = odbc_field_name($cur, $i);
      if($field_name != 'id'){
    	 if($field_name == 'idInform'){
	       $field_value = $newIdInform;
      	}else{
      	  $field_value = odbc_result($cur, $i);
      	}
      	if(is_string($field_value) && $field_value != "getdate()" && $field_value != "null"){
      	  $field_value = "'$field_value'";
      	}
      	if($field_value == 'null'){
      	  $field_value = "''";
      	}
      	if($field_value == ''){
      	  $field_value = "null";
      	}
      	if(! $fields){
      	  $fields = $field_name;
      	  $values = $field_value;
      	}else{
      	  $fields = "$fields, $field_name";
      	  $values = "$values, $field_value";
      	}
      }
    } // fim for
    if(!odbc_exec($db, "insert into Contact ($fields) values ($values)")){
      $msg = "Erro ao inserir importador ". odbc_result($cur, 'name');
      //echo "insert into Importer ($fields) values ($values)<br>". odbc_errormsg(). "<br>";
      //exit();
      $ok = false;
    }
  } // fim while



  if($ok){
    odbc_commit($db);	
	//$notif->doneRole($idNotification, $db);
	//$log_query .= "UPDATE NotificationR SET state = 2 WHERE id = $idNotification";
			
    $msg = "Renovação de Apólice iniciada";
  }else{
    odbc_rollback($db);
  }
  odbc_autocommit($db, true);
}
?>