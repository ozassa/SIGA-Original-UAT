<?php  //alterado HICOM em 06/05/2004

	function parteNumerica($var) {
		$res = "";

		for ($i = 0; $i < strlen($var); $i++) {
			if (is_numeric($var[$i]))
				$res .= $var[$i];
		}

		return  $res;
	}

	function insImporters($idInform, $idSegurado, $db, $dbSisSeg) {
  		global $comm;

  		if($comm != 'sendProp'){ // se for envio de proposta, nao inativar
    			$r = odbc_exec($dbSisSeg, "UPDATE Importador SET s_Imp = 1 WHERE i_Seg = $idSegurado");
  		}

  		$cur = odbc_exec($dbSisSeg, "SELECT MAX (n_Imp) FROM Importador WHERE i_Seg = $idSegurado");
  		$nImp = 0;

  		if(odbc_fetch_row($cur))
    			$nImp = odbc_result($cur, 1) + 1;

  		// Inserir Importadores
  		$cur = odbc_exec($db,
			"SELECT i.name, address, limCredit, c.code, c.name
			FROM Importer i JOIN Country c ON (i.idCountry = c.id)
			WHERE i.idInform = $idInform");

  		$ok = false;
  		$r = true;

  		while (odbc_fetch_row($cur)) {
    			$nImp++;
    			$query =
       				" INSERT INTO Importador (i_Seg, n_Imp, n_User, n_Pais, Nome, Endereco, v_Credito_Solicitado)".
       				" VALUES ($idSegurado, $nImp, 66, ". odbc_result($cur, 4). ", '". odbc_result($cur, 1). "', '".
       				odbc_result($cur, 2). "', ". odbc_result($cur, 3). ")";
  		}
  
  		if($r) {
    			$ok = true;
  		}

  		return $ok;
	} // insImporters

	//-------------------------------------

	$ok = true;
	$curInf = odbc_exec($db,
		"SELECT inf.name, inf.contrat, inf.i_Seg".
		" FROM Inform inf JOIN Region reg ON (idRegion = reg.id)".
		" WHERE inf.id = $idInform");

	if(odbc_fetch_row($curInf)){
  		$nameCofaceSeg = odbc_result($curInf, 1);
  		$idCoface = odbc_result($curInf, 2);
  		$iSeg = odbc_result($curInf, 3);
	}

	$reestudo = 0;

	if($iSeg){
  		$reestudo = 1;
  		$idSegurado = $iSeg;
	}
   
  
   
	
	
	if($reestudo){
  		$curInf = odbc_exec($db,
			"SELECT reg.name,inf.cnpj,inf.ie,inf.name,inf.address,inf.respName,inf.ocupation,inf.products, ".
		      	" inf.city, inf.cep, inf.tel, inf.fax, inf.email, inf.napce, inf.contrat, inf.i_Seg,".
		      	" inf.addressNumber, inf.chargeAddress, inf.chargeAddressNumber, inf.chargeCity, ".
		      	" inf.chargeCep, inf.chargeUf, inf.chargeCep, inf.sameAddress, inf.addressComp, inf.chargeAddressComp ".
		      	" FROM Inform inf JOIN Region reg ON (idRegion = reg.id)".
		      	" WHERE inf.id = $idInform");

  		if(odbc_fetch_row($curInf)) {
    			$idNapce = odbc_result($curInf, 14);
    			$idCoface = odbc_result($curInf, 15);
    			$nameCofaceSeg = odbc_result($curInf, 4);
    			$iSeg = odbc_result($curInf, 'i_Seg');

    			if(!is_numeric($idCoface) || !is_numeric($idNapce) || $idCoface == 0 || $idNapce == 0){
      				$msg = "Códigos NAPCE ou CONTRAT inválidos, corrija em Informações Gerais";
    			}else {
      				$query = "update Segurado set ".
	  				"Nome='". odbc_result($curInf, 4). "',".
	  				"CNP='". odbc_result($curInf, 2). "',".
	  				"IE='". odbc_result($curInf, 3). "',".
	  				"Contato='". odbc_result($curInf, 6).  "',".
	  				"Nat_Oper='". odbc_result($curInf, 8). "',";

	  			// alterado Hicom (Gustavo)
	  			if (odbc_result($curInf, "sameAddress")) {
					$query = $query .
        				"c_Estado= '". substr(odbc_result($curInf, 1), 0, 2). "',".
	    				"Cidade='". odbc_result($curInf, 9). "',".
					"Endereco='". odbc_result($curInf, 5). "', ".
					"Numero = '".odbc_result($curInf, "addressNumber")."', ".
					"CEP = '".parteNumerica(odbc_result($curInf, "cep"))."', ".
					"Compl = '".odbc_result($curInf, "addressComp")."', ";
	  			}else {
					$query = $query .
        				"c_Estado= '". odbc_result($curInf, "chargeUf"). "',".
	    				"Cidade='". odbc_result($curInf, "chargeCity"). "',".
					"Endereco='". odbc_result($curInf, "chargeAddress"). "', ".
					"Numero = '".odbc_result($curInf, "chargeAddressNumber")."', ".
					"CEP = '".parteNumerica(odbc_result($curInf, "chargeCep"))."', ".
					"Compl = '".odbc_result($curInf, "chargeAddressComp")."', ";
	  			}
	  			// fim 
	  
	  			$query = $query . 
	  			"Cargo='". odbc_result($curInf, 7). "',".
	  			"s_Seg=2, ".
	  			"Telefone='". odbc_result($curInf, 11). "',".
	  			"Fax='". odbc_result($curInf, 12). "',".
	  			"e_Mail='". odbc_result($curInf, 13). "',".
	  			"c_NAPCE='$idNapce". "00' ".
      				"where i_Seg=$iSeg";

      				$r = odbc_exec($dbSisSeg, $query);

      				if (!$r){
	     				$msg = "Problemas na atualização do Segurado no SisSeg";
	     				$ok = false;
      				}else{
	     				// HiCom para garantir em DONE.PHP
	     				//$idSegurado = $iSeg;
	 			}

        			//Alterado por Tiago V N - Elumini - 01/08/2006
        			//Verificar se o reestudo já este o numero do contrat(c_coface)
        			$query10 = "Select * from Coface_Seg Where i_Seg='$idSegurado' And c_Coface='$idCoface'";
        			$qr = odbc_exec($dbSisSeg, $query10);

        			if(odbc_result($qr, "i_Seg")=="") {
     	    				// Inserir cadastro coface
            				$query = "INSERT INTO Coface_Seg (c_Coface, i_Seg, s_Cod, Nome, d_Cad, d_Situacao) VALUES ($idCoface, $idSegurado, 0, '$nameCofaceSeg',getDate(),getDate())";
            				$r = odbc_exec ($dbSisSeg, $query);

            				if(!$r){
              					$msg = "ATENÇÃO! Erro ao salvar na tabela Coface no SisSeg.";
            				}
       				}
    			}
  		}
	}else{
		
		 
  		// Não é reestudo.....
		$sql = " SELECT reg.name, inf.cnpj, inf.ie, inf.name, inf.address, inf.respName, inf.ocupation, inf.products, 
			inf.city, inf.cep, inf.tel, inf.fax, inf.email, inf.napce, inf.contrat, inf.addressAbrev, 
		      	inf.addressNumber, inf.chargeAddress, inf.chargeAddressNumber, inf.chargeCity, 
		       	inf.chargeCep, inf.chargeUf, inf.chargeCep, inf.sameAddress, inf.addressComp, inf.chargeAddressComp  
		       	FROM Inform inf JOIN Region reg ON (idRegion = reg.id)
		       	WHERE inf.id = $idInform "; 
  		$curInf = odbc_exec($db,$sql);

  		$key = session_id(). time();
        
		
		
		
  		if(odbc_fetch_row($curInf)) {
    			$idNapce = odbc_result($curInf, 14);
    			$idCoface = odbc_result($curInf, 15);
    			$nameCofaceSeg = odbc_result($curInf, 4);

			if(!is_numeric($idCoface) || !is_numeric($idNapce) || $idCoface == 0 || $idNapce == 0){
      				$ok = false;
      				$msg = "Códigos NAPCE ou CONTRAT inválidos, corrija em Informações Gerais";
    			}else {
      				//alterado Hicom 06/05/2004 no INSERT foi adicionado o campo End_Abrev
	  			$query =
	  				" INSERT INTO Segurado (n_Pais, Nome, CNP, IE, t_Pessoa, Contato,".
	  				" Nat_Oper, c_Estado, Cidade, Endereco, Numero, CEP, Compl, Cargo, s_Seg, Telefone, Fax, e_Mail, End_Abrev, n_User,".
	  				" n_User_Cadastro, c_NAPCE, cookie)".
	  				" VALUES (".
	  				" '175',".                            // Código do Brasil
	  				" '". odbc_result($curInf, 4). "',".  // Nome do Segurado
	  				"  '". odbc_result($curInf, 2). "',".  // CNPJ
	  				"  '". odbc_result($curInf, 3). "',".  // IE
	  				"  'J',".                              // Tipo de pessoa
	  				"  '". odbc_result($curInf, 6). "',".  // Contato
	  				"  '". odbc_result($curInf, 8). "',";  // Natureza da operação
	  				
				// alterado Hicom (Gustavo)
				if (odbc_result($curInf, "sameAddress")) {
					$query = $query .
	    				"  '". substr(odbc_result($curInf, 1), 0, 2). "',". // Estado
	    				"  '". odbc_result($curInf, 9). "',".  // Cidade
					" '". odbc_result($curInf, 5). "', ". // Endereço
					" '".odbc_result($curInf, "addressNumber")."', ". // Número
					" '".parteNumerica(odbc_result($curInf, "cep"))."', ". // CEP
					" '".odbc_result($curInf, "addressComp")."', "; // Complemento
	  			}else {
					$query = $query .
 	    				"  '". odbc_result($curInf, "chargeUf"). "',". // Estado
  	    				"  '". odbc_result($curInf, "chargeCity"). "',".  // Cidade
					" '". odbc_result($curInf, "chargeAddress"). "', ". // Endereço
					" '".odbc_result($curInf, "chargeAddressNumber")."', ". // Número
					" ".parteNumerica(odbc_result($curInf, "chargeCep")).", ". // CEP
					" '".odbc_result($curInf, "chargeAddressComp")."', "; // Complemento
	  			}
	  			// fim 
	  			$query = $query .
	  			"  '". odbc_result($curInf, 7). "',".  // Cargo
	  			"  2,".                                // Status
	  			"  '". odbc_result($curInf, 11). "',". // Telefone
	  			"  '". odbc_result($curInf, 12). "',". // Fax
	  			"  '". odbc_result($curInf, 13). "',". // E-mail
	  			"  '". odbc_result($curInf, 16). "',". //Endereço Abreviado End_Abrev (adicionado hicom 06/05/2004)
	  			"  66,66,".                            // Usuário / Usuário que cadastrou
	  			"  '". $idNapce. "00',".               // Napce
	  			"  '$key')";                           // Chave

				$r = odbc_exec($dbSisSeg, $query);

               //print $query;
               //die();

      				if($r){
					    //// ANDREA SBCE /////
					    $cur = odbc_exec($dbSisSeg, "SELECT max(i_Seg) FROM Segurado WHERE cookie = '$key'");

	    				if(odbc_fetch_row($cur)){
	      					$idSegurado = odbc_result($cur, 1);
	      					$query = "INSERT INTO Coface_Seg (c_Coface, i_Seg, s_Cod, Nome) VALUES ($idCoface, $idSegurado, 0, '$nameCofaceSeg')";

	      					$r = odbc_exec ($dbSisSeg, $query);
          
	      					if(!$r){
	         					odbc_rollback($dbSisSeg);
	         					// Sabemos que este exportador está no sisseg
	         					// vamos verificar se possui apólice vigente
			 				//echo "Vou verificar se tem apolice vigente <BR>";
			 
	         					$cur = odbc_exec($dbSisSeg,
			         				"SELECT d_Ini_Vig, d_Fim_Vig, i_Seg FROM Base_Calculo
                              					WHERE d_Ini_Vig <= getDate() AND
                                  				d_Fim_Vig >= getDate() AND
                                  				t_Endosso = 0 AND
                                  				(s_Doc = 1 OR s_Doc = 3) AND
                                  				c_Coface = $idCoface");

         						if(odbc_fetch_row($cur)){
            							// Possui apólice Vigente
								//echo "Tem apolice vigente <BR>";
				
	            						$data = odbc_result($cur, "d_Ini_Vig");
	            						$dataIni = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
	            						$data = odbc_result($cur, "d_Fim_Vig");
	            						$dataFim = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
	            						$msg = "Este exportador possui apólice vigente ($dataIni à $dataFim)";
	            						$vig = true;
	         					}else{
	             						// Não possui apólice vigente
	             						// Recupera id do segurado
				 				//echo "NÃO Tem apolice vigente <BR>";
				 				//echo "Tentendo obter segurado pelo código coface: $idCoface <BR>";
	             						$cur = odbc_exec($dbSisSeg, "SELECT max(i_Seg) FROM Coface_Seg WHERE c_Coface = $idCoface");

             							if(odbc_fetch_row($cur)){
	           							$idSegurado = odbc_result($cur, 1);				   
	           							// Atualiza dados do segurado no SisSeg
	           							$query = " UPDATE Segurado".
	           								" SET n_Pais = '175',".
	           								"   Nome     = '". odbc_result($curInf, 4). "',".
	           								"   CNP      = '". odbc_result($curInf, 2). "',".
	           								"   IE       = '". odbc_result($curInf, 3). "',".
	           								"   t_Pessoa = 'J',".
	           								"   Contato  = '". odbc_result($curInf, 6). "',".
	          								"   Nat_Oper = '". odbc_result($curInf, 8). "',";

	  			   						// alterado Hicom (Gustavo)
			   						if (odbc_result($curInf, "sameAddress")) {
				 						$query = $query .
	            								" c_Estado = '". substr(odbc_result($curInf, 1), 0, 2). "',".
	            								" Cidade   = '". odbc_result($curInf, 9). "',".
										" Endereco='". odbc_result($curInf, 5). "', ".
				 						" Numero = '".odbc_result($curInf, "addressNumber")."', ".
				 						" CEP = ".parteNumerica(odbc_result($curInf, "cep")).", ".
										" Compl='". odbc_result($curInf, "addressComp"). "', ";
			   						}else {
				 						$query = $query .
	            								" c_Estado = '". odbc_result($curInf, "chargeUf"). "',".
	            								" Cidade   = '". odbc_result($curInf, "chargeCity"). "',".
				 						" Endereco='". odbc_result($curInf, "chargeAddress"). "', ".
				 						" Numero = '".odbc_result($curInf, "chargeAddressNumber")."', ".
				 						" CEP = ".parteNumerica(odbc_result($curInf, "chargeCep")).", ".
										" Compl='". odbc_result($curInf, "chargeAddressComp"). "', ";
			   						}
			   						// fim 
			   						$query = $query .
	           							"   Cargo    = '". odbc_result($curInf, 7). "',".
	           							"   s_Seg    = 2,".
	           							"   Telefone = '". odbc_result($curInf, 11). "',".
	           							"   Fax      = '". odbc_result($curInf, 12). "',".
	           							"   e_Mail   = '". odbc_result($curInf, 13). "',".
	           							"   n_User   = 66, n_User_Cadastro = 66,".
	           							"   c_NAPCE  = '". $idNapce. "00'".
	           							" WHERE i_Seg = $idSegurado";

	           							//echo "<pre>$query</pre>";
			   						//echo "Atualizando registro antigo do segurado: $query <BR>";
				   
	           							$r = odbc_exec($dbSisSeg, $query);

	           							if(!$r){
			     							$ok = false;
	             								$msg = "Problemas na atualização de segurado pré-existente. (sisseg)";
	           							}else if(insImporters($idInform, $idSegurado, $db, $dbSisSeg)) {
			     							//echo "Importadores Atualizados! <BR>";
	             								$ok = true;
	             								$msg = "";
	             								odbc_commit($dbSisSeg);
	           							}else{
			     							$ok = false;
	             								$msg = "Problemas na inclusão de importador em segurado pré-existente. (sisseg)";
	           							}
			
             							}else{
			    						$ok = false;
	            							$msg = "Problemas na obtenção do código do segurado pré-existente. (sisseg)";
             							}
         						}
      						}else{
        						// Inserir Importadores
	    						// Comentado HiCom.... a inclusão de importadores.....
        						if(insImporters($idInform, $idSegurado, $db, $dbSisSeg)){
          							$ok = true;
          							$msg = "";
          							odbc_commit($dbSisSeg);
        						}else{
          							$ok = false;
          							$msg = "Problemas na inclusão de importador. (sisseg)";
        						}
	      					}
					}else{
           					$msg = "Não foi possível encontrar i_seg pela chave. (sisseg)";
	       					$ok = false;
					}	
      			 }else{
         				$msg = "Não foi possível cadastrar na tabela de segurado. (sisseg), Verifique o código NAPCE";
	     				$ok = false;
	  			 }
    		} // fim critica napce
  		}else{
     			$msg = "Informe não encontrado na rotina de atualização do Sisseg.";
	 		$ok = false;
  		}
	}

	if(!$ok) {
		odbc_rollback ($dbSisSeg);
		$erroSisSeg = 1;
	}else{
		odbc_commit($dbSisSeg);
	}

	odbc_autocommit ($dbSisSeg, true);

?>