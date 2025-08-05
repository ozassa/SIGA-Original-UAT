<?php  
/*
 * Created on 05/06/2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require("../../dbOpen.php");

odbc_autocommit($db, false);

if ($idInform == ""){	
	echo "Não pode excluir os dados do informe";	
} else {


$strSQL = "SELECT * FROM Inform WHERE id = '$idInform'";
$cur1 = odbc_exec($db, $strSQL);

	if (odbc_fetch_row($cur1)) {
		$idInsured = odbc_result($cur1, "idInsured");		
		
		$strSQLLOST = "DELETE FROM Lost WHERE idInform = '$idInform' ";
		if (odbc_exec($db, $strSQLLOST)) {
		
			$strSQLVOLUMESEG = "DELETE FROM VolumeSeg WHERE idInform = '$idInform'";
			if (odbc_exec($db, $strSQLVOLUMESEG)) {		
				$strSQLVOLUME = "DELETE FROM Volume WHERE idInform = '$idInform'";
				if (odbc_exec($db, $strSQLVOLUME)) {
					$strSQLINF = "DELETE FROM Inform WHERE id = '$idInform' AND idInsured ='$idInsured' ";
					if (odbc_exec($db, $strSQLINF) ) {			
						$strSQL = "SELECT * FROM Insured WHERE id = '$idInsured'";
						$cur = odbc_exec($db, $strSQL);
						if (odbc_fetch_row($cur)) {			
							$idUser = odbc_result($cur, "idResp");

							$strSQLINS = "DELETE FROM Insured WHERE id = '$idInsured' AND idResp = '$idUser'";
							if (odbc_exec($db, $strSQLINS)) {
	    						$strSQLUSERROLE = "DELETE FROM UserRole WHERE idUser = '$idUser' ";
								if (odbc_exec($db, $strSQLUSERROLE)) {	    		
									
									$strSQLTRANSACTIONLOG = "DELETE FROM TransactionLog WHERE idUser = '$idUser'";
									if (odbc_exec($db, $strSQLTRANSACTIONLOG)) {
	    								$strSQLUSERS	= "DELETE FROM Users WHERE id = '$idUser'";
										if (odbc_exec($db, $strSQLUSERS)){
											odbc_commit($db);
											echo "Exclusão foi efetudo com sucesso.";
										}else{
											odbc_rollback($db);
											echo "Erro em excluir o registro na tabela Users.". odbc_errormsg();	
										}			
									}else{
											odbc_rollback($db);
											echo "Erro em excluir o registro na tabela TransactionLog.". odbc_errormsg();											
									}				
								}else{
									odbc_rollback($db);
									echo "Erro em excluir o registro na tabela UserRole.". odbc_errormsg();
								}		    		
							}else{
								odbc_rollback($db);
								echo "Erro em excluir o registro na tabela Insured.". odbc_errormsg();
							}	
						}else{				
							odbc_rollback($db);
							echo "Não foi encontrado nenhum id na tabela Insured.". odbc_errormsg();
						}	
					}else{
						odbc_rollback($db);
						echo "Erro em excluir o registro na tabela Informe." . odbc_errormsg();			
					}
				}else{
					odbc_rollback($db);
					echo "Erro em excluir o registro na tabela VolumeSeg.".odbc_errormsg();							
				}
			}else{
				odbc_rollback($db);
				echo "Erro em excluir o registro na tabela Volume.".odbc_errormsg();
			}		  	
		}else{			
			echo "Erro em excluir o registro na tabela Lost." . odbc_errormsg();
			odbc_rollback($db);
		}	    	    
	}else{
		echo "Não existe informe para esse id ou já foi excluido.";
	}

}
?>
