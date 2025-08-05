<?php
	
	$Id_Cessao = "";
	if ($idCDBB) {
		$Id_Cessao = $idCDBB;
	} 

	$sqlProcEmp = "Exec SPR_BB_Consulta_Doc_Condicoes_Especiais '".$Id_Cessao."', '100'";
	$rsSqlProcEmp = odbc_exec($db, $sqlProcEmp);

	$Id_Apolice = odbc_result($rsSqlProcEmp, "Id_Apolice");
	$Num_Apolice = odbc_result($rsSqlProcEmp, "Num_Apolice");
	$Emissao_Apolice = ymd2dmy(odbc_result($rsSqlProcEmp, "Emissao_Apolice"));
	$Cod_Clausula = odbc_result($rsSqlProcEmp, "Cod_Clausula");
	$Data_Inicio_Vigencia = ymd2dmy(odbc_result($rsSqlProcEmp, "Data_Inicio_Vigencia"));
	$Data_Fim_Vigencia = ymd2dmy(odbc_result($rsSqlProcEmp, "Data_Fim_Vigencia"));
	$Nome_Empresa = odbc_result($rsSqlProcEmp, "Nome_Empresa");
	$Endereco_Empresa = odbc_result($rsSqlProcEmp, "Endereco_Empresa");
	$Complemento_Empresa = odbc_result($rsSqlProcEmp, "Complemento_Empresa");
	$Cidade_Empresa = odbc_result($rsSqlProcEmp, "Cidade_Empresa");
	$Bairro_Empresa = odbc_result($rsSqlProcEmp, "Bairro_Empresa");
	$UF_Empresa = odbc_result($rsSqlProcEmp, "UF_Empresa");
	$CPNJ_Empresa = odbc_result($rsSqlProcEmp, "CPNJ_Empresa");
	$Site_Empresa = odbc_result($rsSqlProcEmp, "Site_Empresa");
	$Cod_SUSEP = odbc_result($rsSqlProcEmp, "Cod_SUSEP");
	$Nome_Segurado = odbc_result($rsSqlProcEmp, "Nome_Segurado");
	$Endereco_Segurado = odbc_result($rsSqlProcEmp, "Endereco_Segurado");
	$Cidade_Segurado = odbc_result($rsSqlProcEmp, "Cidade_Segurado");
	$UF_Segurado = odbc_result($rsSqlProcEmp, "UF_Segurado");
	$CNPJ_Segurado = odbc_result($rsSqlProcEmp, "CNPJ_Segurado");
	$Nome_Banco = odbc_result($rsSqlProcEmp, "Nome_Banco");
	$Nome_Agencia = odbc_result($rsSqlProcEmp, "Nome_Agencia");
	$Endereco_Agencia = odbc_result($rsSqlProcEmp, "Endereco_Agencia");
	$Cidade_Agencia = odbc_result($rsSqlProcEmp, "Cidade_Agencia");
	$UF_Agencia = odbc_result($rsSqlProcEmp, "UF_Agencia");
	$CNPJ_Agencia = odbc_result($rsSqlProcEmp, "CNPJ_Agencia");
	$Mod_48 = odbc_result($rsSqlProcEmp, "Mod_48");
	$Item_48 = odbc_result($rsSqlProcEmp, "Item_48");
	$Prazo_Max_Cred = odbc_result($rsSqlProcEmp, "Prazo_Max_Cred");
	$Adequacao_Sinistralidade = odbc_result($rsSqlProcEmp, "Adequacao_Sinistralidade");
	$Adequacao_Premio = odbc_result($rsSqlProcEmp, "Adequacao_Premio");

	$sqlProcComp = "Exec SPR_BB_Consulta_Doc_Condicoes_Especiais '".$Id_Cessao."', '200'";
	$rsSqlProcComp = odbc_exec($db, $sqlProcComp);

	$dados_comp = array();
	while(odbc_fetch_row($rsSqlProcComp)) {
		$Nome_Comprador = odbc_result($rsSqlProcComp, "Nome_Comprador");
		$Endereco_Comprador = odbc_result($rsSqlProcComp, "Endereco_Comprador");
		$Pais_Comprador = odbc_result($rsSqlProcComp, "Pais_Comprador");
		$Cod_Comprador = odbc_result($rsSqlProcComp, "Cod_Comprador");

		$dados_comp[] = array(
			"Nome_Comprador" 					=> $Nome_Comprador,
			"Endereco_Comprador" 			=> $Endereco_Comprador,
			"Pais_Comprador"					=> $Pais_Comprador,
			"Cod_Comprador"						=> $Cod_Comprador
		);
	}

	$sqlProcMod = "Exec SPR_BB_Consulta_Doc_Condicoes_Especiais '".$Id_Cessao."', '300'";
	$rsSqlProcMod = odbc_exec($db, $sqlProcMod);

	$dados_mod = array();
	while(odbc_fetch_row($rsSqlProcMod)) {
		$i_Modulo = odbc_result($rsSqlProcMod, "i_Modulo");
		$Grupo_Modulo = odbc_result($rsSqlProcMod, "Grupo_Modulo");
		$Cod_Modulo = odbc_result($rsSqlProcMod, "Cod_Modulo");
		$Titulo_Modulo = odbc_result($rsSqlProcMod, "Titulo_Modulo");
		$v_1 = odbc_result($rsSqlProcMod, "v_1");
		$v_2 = odbc_result($rsSqlProcMod, "v_2");
		$Texto_Modulo = odbc_result($rsSqlProcMod, "Texto_Modulo");

		$dados_mod[] = array(
			"i_Modulo" 					=> $i_Modulo,
			"Grupo_Modulo" 			=> $Grupo_Modulo,
			"Cod_Modulo"				=> $Cod_Modulo,
			"Titulo_Modulo"			=> $Titulo_Modulo,
			"v_1" 							=> $v_1,
			"v_2"								=> $v_2,
			"Texto_Modulo"			=> $Texto_Modulo
		);
	}
?>