<?php
$log_query = "";

/*
Altera dados / grava log
*/

if ($executa == 1){

	$sql = "UPDATE Users SET name = '$name',login = '$login',email = '$email',";

/*
  Alterado por Tiago V N - ( Elumini )
*/

	if ($state == 1){

		$sql .= "password = '$password', state = 1";

	}else{

		$sql .= "password = '$password', state = 0";

	}

		$sql .= " WHERE id = $id ";
		
		if (count($role) > 0) {

   		$cur=odbc_exec($db,$sql);
   		
		if($cur)
		{
			$log_query .=$sql;
		}
		
		
			if (!$cur){
				$ok = false;
			}

   	}else{

				$msg = "Selecione ao menos um perfil.";

		}

		$sqlLOG = "INSERT INTO Log VALUES ('4', '$userID', '$id', '".date(Y)."-".date(m)."-".date(d)."', '".date(H).":".date(i).":".date(s)."')";

		$cur=odbc_exec($db,$sqlLOG);
		
		if($cur)
		{
			$log_query .=$sqlLOG;
		}
		
		if (!$cur){
			$ok = false;
		}

		$sqlRLOG = "Select * From Log where tipoLog = '4' And id_User = '". $userID ."' And Inform= '". $id ."' And data= '".date(Y)."-".date(m)."-".date(d)."'And hora='".date(H).":".date(i).":".date(s)."'";
    //echo  $sqlL2 ."<br>";
		$cur=odbc_exec($db,$sqlRLOG);
		if($cur)
		{
			$log_query .=$sqlRLOG;
		}		
		
		
		if (!$cur){
			$ok = false;
		}
		
		while (odbc_fetch_row($cur)) {
		$idLogN2 = odbc_result($cur,"id_Log");

			if (empty($LogEmail){

				$LogEmail = "Vazio";

			}

			$sql11 = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('". $idLogN2 ."','Nome','". $LogNome ."','". $name ."')";
    		$sql12 = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('". $idLogN2 ."','Login','". $LogLogin ."','". $login ."')";
    		$sql13 = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('". $idLogN2 ."','E-mail','". $LogEmail ."','". $email ."')";
    		$sql14 = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('". $idLogN2 ."','Senha','". $LogPass ."','". $password ."')";


				$exe1 = odbc_exec($db,$sql11);
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				   //CRIADO POR WAGNER
				   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				   
				   if ($exe1) {
				      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
				      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
				      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
				          "values ('$cur', '".str_replace("'","",$log_query)."')";
						  
						  //echo $sql;
				          odbc_exec($db, $sql);
				   }//fim if	
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				
				$exe2 = odbc_exec($db,$sql12);
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				   //CRIADO POR WAGNER
				   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				   
				   if ($exe2) {
				      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
				      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
				      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
				          "values ('$cur', '".str_replace("'","",$log_query)."')";
						  
						  //echo $sql;
				          odbc_exec($db, $sql);
				   }//fim if	
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				
				

				$exe3 = odbc_exec($db,$sql13);
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				   //CRIADO POR WAGNER
				   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				   
				   if ($exe3) {
				      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
				      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
				      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
				          "values ('$cur', '".str_replace("'","",$log_query)."')";
						  
						  //echo $sql;
				          odbc_exec($db, $sql);
				   }//fim if	
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				
				
				$exe4 = odbc_exec($db,$sql14);
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				   //CRIADO POR WAGNER
				   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				   
				   if ($exe4) {
				      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
				      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
				      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
				          "values ('$cur', '".str_replace("'","",$log_query)."')";
						  
						  //echo $sql;
				          odbc_exec($db, $sql);
				   }//fim if	
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				
				
				if (!$exe1){
					$ok = false;
				}
				if (!$exe2){
					$ok = false;
				}
				if (!$exe3){
					$ok = false;
				}
				if (!$exe4){
					$ok = false;
				}


		}


		$sqlROLE = "Select * From UserRole WHERE idUser = '". $id ."'";
                     //echo  $sqlL ."<br>";
		$cur=odbc_exec($db,$sqlROLE);
		
		if($cur)
		{
			$log_query .=$sqlROLE;
		}
	
	
			if (!$cur){
				$ok = false;
			}
				
   	while (odbc_fetch_row($cur)) {
   	$idRole = odbc_result($cur,"idRole");

			$sqlLOG = "Select * From Log where tipoLog = '4' And id_User = '". $userID ."' And Inform= '". $id ."' And data= '".date(Y)."-".date(m)."-".date(d)."'And hora='".date(H).":".date(i).":".date(s)."'";
                             //echo  $sqlL ."<br>";
			$cur=odbc_exec($db,$sqlLOG);
			if($cur)
			{
				$log_query .=$sqlLOG;
			}
			
			
			if (!$cur){
				$ok = false;
			}
			
			while (odbc_fetch_row($cur)) {
			$idLogN = odbc_result($cur,"id_Log");

			$sqlNOVODETALHE = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('". $idLogN ."','Perfil','".$idRole."','Exclusão')";
                                   //echo $sqlnD;
			$cur=odbc_exec($db,$sqlNOVODETALHE);
			
			if($cur)
			{
				$log_query .=$sqlNOVODETALHE;
			}
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				   //CRIADO POR WAGNER
				   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				   
				   if ($cur) {
				      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
				      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
				      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
				          "values ('$cur', '".str_replace("'","",$log_query)."')";
						  
						  //echo $sql;
				          odbc_exec($db, $sql);
				   }//fim if	
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			
			
			
			
			
			
			
			if (!$cur){
				$ok = false;
			}

			}

		}

      for ($i=1; $i<=count($role); $i++) {

             $sqlLOG = "Select * From Log where tipoLog = '4' And id_User = '". $userID ."' And Inform= '". $id ."' And data= '".date(Y)."-".date(m)."-".date(d)."'And hora='".date(H).":".date(i).":".date(s)."'";
             //echo  $sqlL ."<br>";
             $cur=odbc_exec($db,$sqlLOG);

							if (!$cur){
								$ok = false;
							}
							
             while (odbc_fetch_row($cur)) {
             $idLogN = odbc_result($cur,"id_Log");


                //echo $role[$i - 1] ."<br>";
                $sqlNOVODETALHE = "INSERT INTO Log_Detalhes(id_Log,campo,valor,alteracao)VALUES('".$idLogN."','Perfil','".$role[$i - 1]."','Inclusão')";
                //echo $sqlnD;
                $cur = odbc_exec($db,$sqlNOVODETALHE);
				
				if($cur)
				{
					$log_query .=$sqlNOVODETALHE;
				}
				
				
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				   //CRIADO POR WAGNER
				   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				   
				   if ($cur) {
				      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
				      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
				      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
				          "values ('$cur', '".str_replace("'","",$log_query)."')";
						  
						  //echo $sql;
				          odbc_exec($db, $sql);
				   }//fim if	
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


								if (!$cur){
									$ok = false;
								}


             }

       }

  /*
	 Fim log de alteração - Cristiano (Elumini) 6/9/2005
	*/

		$sqlDELETEROLE = "DELETE FROM UserRole WHERE idUser = '$id'"; // deleta todas as roles

    //echo $sql;

    $cur = odbc_exec($db,$sqlDELETEROLE);
    
				if($cur)
				{
					$log_query .=$sqlDELETEROLE;
				}
	
	
    if (!$cur){
			$ok = false;
		}

			for ($i=1; $i<=count($role); $i++) {
			
				$sqlNOVAROLE = "INSERT INTO UserRole (idUser, idRole) VALUES ('$id', ".$role[$i - 1].")";

   			$cur = odbc_exec($db,$sqlNOVAROLE);

    		if (!$cur){
					$ok = false;
				}

			}

}

/*
Fim altera dados / grava log
*/
?>