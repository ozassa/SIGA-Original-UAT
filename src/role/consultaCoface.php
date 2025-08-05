<?php 
	
	if(file_exists('../../../dbOpen.php')){
		require_once('../../../dbOpen.php');
	} elseif(file_exists('../../dbOpen.php')) {
		require_once('../../dbOpen.php');
	} else {
		require_once('src/dbOpen.php');
	}
	
	
	//COFACE
	$sqlEmp  = "SELECT i_Empresa, Nome, CNPJ,	Endereco,	Complemento, CEP, Cidade,	Estado,	Cod_Area,	Telefone,	Bairro, Fax, HomePage
							FROM Empresa 
							WHERE i_Empresa = 1";  
   
 	$resEmp = odbc_exec($db,$sqlEmp);
 	$dadosEmp = odbc_fetch_array($resEmp);
	
	$compEmp = $dadosEmp['Complemento'] ? ' - '.$dadosEmp['Complemento'] : '';
	$cepEmp = $dadosEmp['CEP'] ? ' - Cep: '.$dadosEmp['CEP'] : '';
	$cidEmp = $dadosEmp['Cidade'] ? ' - '.$dadosEmp['Cidade'] : '';
	$estEmp = $dadosEmp['Estado'] ? ', '.$dadosEmp['Estado'] : '';
	$telEmp = $dadosEmp['Telefone'] ? ' Tel.: '.$dadosEmp['Telefone'] : '';
	$faxEmp = $dadosEmp['Fax'] ? ' Fax: '.$dadosEmp['Fax'] : '';

	$enderecoEmp = $dadosEmp['Endereco'].$compEmp.$cepEmp.$cidEmp.$estEmp.$telEmp.$faxEmp;
	$siteEmp = $dadosEmp['HomePage'];
	$nomeEmp = $dadosEmp['Nome'];
	
	//SBCE
	$sqlEmpSBCE  = "SELECT i_Empresa, Nome, CNPJ,	Endereco,	Complemento, CEP, Cidade,	Estado,	Cod_Area,	Telefone,	Bairro, Fax, HomePage
							FROM Empresa 
							WHERE i_Empresa = 2";  
   
 	$resEmpSBCE = odbc_exec($db,$sqlEmpSBCE);
 	$dadosEmpSBCE = odbc_fetch_array($resEmpSBCE);
	
	$compEmpSBCE = $dadosEmpSBCE['Complemento'] ? ' - '.$dadosEmpSBCE['Complemento'] : '';
	$cepEmpSBCE = $dadosEmpSBCE['CEP'] ? ' - Cep: '.$dadosEmpSBCE['CEP'] : '';
	$cidEmpSBCE = $dadosEmpSBCE['Cidade'] ? ' - '.$dadosEmpSBCE['Cidade'] : '';
	$estEmpSBCE = $dadosEmpSBCE['Estado'] ? ', '.$dadosEmpSBCE['Estado'] : '';
	$telEmpSBCE = $dadosEmpSBCE['Telefone'] ? ' Tel.: '.$dadosEmpSBCE['Telefone'] : '';
	$faxEmpSBCE = $dadosEmpSBCE['Fax'] ? ' Fax: '.$dadosEmpSBCE['Fax'] : '';
	$cnpjEmpSBCE = $dadosEmpSBCE['CNPJ'];

	$enderecoEmpSBCE = $dadosEmpSBCE['Endereco'].$compEmpSBCE.$cepEmpSBCE.$cidEmpSBCE.$estEmpSBCE.$telEmpSBCE.$faxEmpSBCE;
	$siteEmpSBCE = $dadosEmpSBCE['HomePage'];
	$nomeEmpSBCE = $dadosEmpSBCE['Nome'];

?>