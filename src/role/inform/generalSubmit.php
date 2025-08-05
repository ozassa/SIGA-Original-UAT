<?php
if (!isset($_SESSION)) {
	session_start();
}
$userID = $_SESSION['userID'];
//Alterado por Tiago V N - Elumini -> Inclusão campo moeda no infome
$log_query = "";
if (preg_match("/[\/áéíóúàâêîôûãõüÁÉÍÓÚÀÂÊÎÔÛÃÕÜ]/", $name)) {
	$msggs = "No nome não são permitidos acentos ou barras (/)";
	$forward = "error";
	return;
}

$cnpjo = $field->getField("cnpj");
$len = strlen($cnpjo);
$cnpj = "";
for ($i = 0; $i < $len && $i < 18; $i++) {
	$cnpj .= is_numeric($cnpjo[$i]) ? $cnpjo[$i] : "";
}

if ($_REQUEST['idInform'])
	$idInform = $_REQUEST['idInform'];

// criar novo informe

if ($_REQUEST['Gerar_Novo_Inform'] == 1) {
	$sql = "SELECT id FROM Insured WHERE idResp = ?";
	$stmt = odbc_prepare($db, $sql);
	odbc_execute($stmt, [$userID]);
	$idInsured = odbc_result($stmt, 1);

	$insertInform = "INSERT INTO Inform (idInsured) VALUES (?)";
	$stmtInform = odbc_prepare($db, $insertInform);
	odbc_execute($stmtInform, [$idInsured]);

	$sqlMaxId = "SELECT MAX(id) AS id FROM Inform";
	$stmtMaxId = odbc_prepare($db, $sqlMaxId);
	odbc_execute($stmtMaxId);
	$idInform = odbc_result($stmtMaxId, 'id');

	$insertVolume = "INSERT INTO Volume (idInform) VALUES (?)";
	$stmtVolume = odbc_prepare($db, $insertVolume);
	odbc_execute($stmtVolume, [$idInform]);

	$insertLost = "INSERT INTO Lost (idInform) VALUES (?)";
	$stmtLost = odbc_prepare($db, $insertLost);
	odbc_execute($stmtLost, [$idInform]);
}



$sameAddress = $field->getField("sameAddress");

$Periodo_Vigencia = $field->getField("Periodo_Vigencia");
//print '?'.$sameAddress;

if ($sameAddress == 1) {
	$chargeAddress = " ";
	$chargeAddressNumber = " ";
	$chargeCity = " ";
	$chargeUf = "Na";
	$chargeCep = " ";
	$chargeAddressComp = " ";
} else {
	$chargeAddress = $field->getField("chargeAddress");
	$chargeAddressNumber = $field->getField("chargeAddressNumber");
	$chargeCity = $field->getField("chargeCity");
	$chargeUf = $field->getField("chargeUf");
	$chargeCep = $field->getField("chargeCep");
	$chargeAddressComp = $field->getField("chargeAddressComp");
}


if ($hc_cliente == "N" && ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')) {
	$novo_estatus = "2";
} else {
	if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') {
		$novo_estatus = "2";
	} else {
		$novo_estatus = "3";
	}
}


//echo $novo_estatus;
//die();

$complento = "";
if ($_SESSION['pefil'] != 'C' && $_SESSION['pefil'] != 'B') {
	$complento =
		"      naf = '" . $field->getField("naf") . "'," .
		"      siren = '" . $field->getField("siren") . "'," .
		"      quest = '" . $field->getField("quest") . "'," .
		"      napce = '" . $field->getField("napce") . "'," .
		"      dossier = '" . $field->getField("dossier") . "'," .
		"      contrat = '" . $field->getField("contrat") . "',";
}



$qry = "UPDATE Inform
        SET {$complento}
            executive = ?, 
            comRisk = ?, 
            polRisk = ?, 
            name = ?, 
            address = ?, 
            bairro = ?, 
            city = ?, 
            uf = ?, 
            cep = ?, 
            tel = ?, 
            fax = ?, 
            email = ?, 
            contact = ?, 
            ocupationContact = ?, 
            emailContact = ?, 
            cnpj = ?, 
            ie = ?, 
            idSector = ?, 
            products = ?, 
            frameMed = ?, 
            hasGroup = ?, 
            exportMore = ?, 
            companyGroup = ?, 
            hasAssocCompanies = ?, 
            associatedCompanies = ?, 
            warantyExp = ?, 
            warantyFin = ?, 
            hasAnother = ?, 
            another = ?, 
            idRegion = ?, 
            warantyInterest = ?, 
            sameAddress = ?, 
            chargeAddress = ?, 
            chargeCity = ?, 
            chargeUf = ?, 
            chargeCep = ?, 
            generalState = ?, 
            addressNumber = ?, 
            chargeAddressNumber = ?, 
            addressComp = ?, 
            chargeAddressComp = ?, 
            pvigencia = ?, 
            i_Empresa = ?, 
            i_Produto = ?, 
            currency = ?, 
            Periodo_Vigencia = ?, 
            i_Gerente = ?, 
            i_Gerente_Relacionamento = ?, 
            i_CNAE = ? 
        WHERE id = ?";

$params = [
	$field->getField("executive"),
	$field->getNumField("comRisk"),
	$field->getNumField("polRisk"),
	strtoupper($field->getField("name")),
	$field->getField("address"),
	$field->getField("bairro"),
	$field->getField("city"),
	$field->getField("Uf"),
	$field->getField("cep"),
	$field->getField("tel"),
	$field->getField("fax"),
	$field->getField("email"),
	$field->getField("contact"),
	$field->getField("ocupationContact"),
	$field->getField("emailContact"),
	$cnpj,
	$field->getField("ie"),
	$field->getField("idSector"),
	$field->getField("products"),
	$field->getNumField("frameMed"),
	$field->getNumField("hasGroup"),
	$field->getNumField("exportMore"),
	$field->getField("companyGroup"),
	$field->getNumField("hasAssocCompanies"),
	$field->getField("associatedCompanies"),
	$field->getNumField("warantyExp"),
	$field->getNumField("warantyFin"),
	$field->getNumField("hasAnother"),
	$field->getField("another"),
	$field->getNumField("idRegion"),
	$field->getNumField("warantyInterest"),
	$field->getField("sameAddress"),
	$chargeAddress,
	$chargeCity,
	$chargeUf == 'Na' ? null : $chargeUf,
	$chargeCep,
	$novo_estatus,
	$field->getField("addressNumber"),
	$chargeAddressNumber,
	$field->getField("addressComp"),
	$field->getField("chargeAddressComp"),
	$field->getNumField("pvigencia"),
	$field->getNumField("i_Produto") == 1 ? 1 : 2,
	$field->getNumField("i_Produto"),
	$field->getNumField("tipomoeda"),
	$field->getField("Periodo_Vigencia"),
	$field->getField("i_Gerente") > 0 ? $field->getField("i_Gerente") : 0,
	$field->getField("i_GerenteR") > 0 ? $field->getField("i_GerenteR") : 0,
	$field->getField("sel_classe_cnae") > 0 ? $field->getField("sel_classe_cnae") : 0,
	$field->getField("idInform") ? $field->getField("idInform") : $idInform
];

$r = odbc_prepare($db, $qry);
odbc_execute($r, $params);





if ($r == FALSE) {
	$msggs = "Aguns campos foram preenchidos incorretamente, verifique a existência de caracteres inválidos como ( ' ).";
	$forward = "error";
}



if ($r) {
	$log_query .= " UPDATE Inform" .
		"  SET " . $complento .
		"      executive = '" . $field->getField("executive") . "'," .
		"      comRisk = " . $field->getNumField("comRisk") . "," .
		"      polRisk = " . $field->getNumField("polRisk") . "," .
		"      name    = '" . strtoupper($field->getField("name")) . "'," .
		"      address = '" . $field->getField("address") . "'," .
		"      bairro  = '" . $field->getField("bairro") . "'," .
		"      city    = '" . $field->getField("city") . "'," .
		"      uf      = '" . $field->getField("Uf") . "'," .
		"      cep     = '" . $field->getField("cep") . "'," .
		"      tel     = '" . $field->getField("tel") . "'," .
		"      fax     = '" . $field->getField("fax") . "'," .
		"      email   = '" . $field->getField("email") . "'," .
		"      contact = '" . $field->getField("contact") . "'," .
		"      ocupationContact   = '" . $field->getField("ocupationContact") . "'," .
		"      emailContact   = '" . $field->getField("emailContact") . "'," .
		"      cnpj    = '" . $cnpj . "'," .
		"      ie      = '" . $field->getField("ie") . "'," .
		"      idSector = " . $field->getField("idSector") . "," .
		"      products = '" . $field->getField("products") . "'," .
		//"      sazSales =  ".$field->getNumField ("sazSales").",".
		//"      fatDom   =  ".$field->getNumField ("fatDom").",".
		//"      fatExp   =  ".$field->getNumField ("fatExp").",".
		"      frameMed =  " . $field->getNumField("frameMed") . "," .
		//"      frameMin =  ".$field->getNumField ("frameMin").",".
		"      hasGroup =  " . $field->getNumField("hasGroup") . "," .
		"      exportMore =  " . $field->getNumField("exportMore") . "," .
		"      companyGroup = '" . $field->getField("companyGroup") . "'," .
		"      hasAssocCompanies =  " . $field->getNumField("hasAssocCompanies") . "," .
		"      associatedCompanies = '" . $field->getField("associatedCompanies") . "'," .
		//"      creditOwnDep =  ".$field->getNumField ("creditOwnDep").",".
		"      warantyExp = " . $field->getNumField("warantyExp") . "," .
		"      warantyFin = " . $field->getNumField("warantyFin") . "," .
		"      hasAnother = " . $field->getNumField("hasAnother") . "," .
		"      another    = '" . $field->getField("another") . "'," .
		"      idRegion        =  " . $field->getNumField("idRegion") . "," .
		"      warantyInterest =  " . $field->getNumField("warantyInterest") . "," .
		"      sameAddress     =  " . $field->getField("sameAddress") . "," .
		"      chargeAddress   = '" . $chargeAddress . "'," .
		"      chargeCity      = '" . $chargeCity . "'," .
		"      chargeUf        = '" . $chargeUf . "'," .
		"      chargeCep       = '" . $chargeCep . "'," .
		"      generalState    =  " . $novo_estatus . ", " .
		"      addressNumber   = '" . $field->getField('$addressNumber') . "', " .
		"      chargeAddressNumber   = '" . $chargeAddressNumber . "', " .
		"      addressComp   = '" . $field->getField('addressComp') . "', " .
		"      chargeAddressComp   = '" . $chargeAddressComp . "', " .
		"      pvigencia           = '" . $field->getNumField("pvigencia") . "'," .
		"      i_Empresa           = '" . ($field->getNumField("i_Produto") == 1 ? 1 : 2) . "'," .
		"      i_Produto           = '" . $field->getNumField("i_Produto") . "'," .
		"      currency             = '" . $field->getNumField("tipomoeda") . "', " .
		"      Periodo_Vigencia     = '" . $field->getField("Periodo_Vigencia") . "', " .
		"      i_Gerente            = '" . ($field->getField("i_Gerente") > 0 ? $field->getField("i_Gerente") : 0) . "' " .
		"      i_Gerente_Relacionamento            = '" . ($field->getField("i_GerenteR") > 0 ? $field->getField("i_GerenteR") : 0) . "' " .
		"  WHERE id =" . ($field->getField("idInform") ? $field->getField("idInform") : $idInform);
}

odbc_free_result($r);

//print $qry;

//die; 


//Registrar no Log (Sistema) - Criado Por Tiago V N - 03/07/2006
// Tipo Log = Alteração de Informações Gerais (Tela Cliente Novo) - 33

// Inserindo na tabela Log
$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) 
VALUES (?, ?, ?, ?, ?)";
$stmt = odbc_prepare($db, $sql);
$result = odbc_execute($stmt, [
	'33',
	$userID,
	$idInform,
	date("Y-m-d"),
	date("H:i:s")
]);

if ($result) {
	// Recuperando o ID do registro inserido
	$sql_id = "SELECT @@IDENTITY AS id_Log";
	$stmt_id = odbc_prepare($db, $sql_id);
	odbc_execute($stmt_id);
	$cur = odbc_result($stmt_id, 1);

	// Inserindo na tabela Log_Detalhes
	$sql_details = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) 
			VALUES (?, ?, ?, ?)";
	$stmt_details = odbc_prepare($db, $sql_details);
	$rs = odbc_execute($stmt_details, [
		$cur,
		'Informe',
		'-',
		'Dados do Segurado'
	]);
}

?>