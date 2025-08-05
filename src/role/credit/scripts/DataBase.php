<html>
	<head>
		<title>TENDECIES CONSULTORIA</title>
	</head>
	<body>
		<h1>ATUALIZAÇÃO DO SIEX</h1><hr>
<?php  require_once("../../../dbOpen.php");

	$sisSegSeg = odbc_exec ($dbSisSeg, "SELECT i_Seg
				   FROM Segurado
				   WHERE NOT (cookie IS NULL)");

	$msg = 	"EM EXECUÇÃO, POR FAVOR, AGUARDE.";

	print $msg." \n";

	$i = 0;

	while (odbc_fetch_row ($sisSegSeg)) {
		$i_Seg = odbc_result ($sisSegSeg, 1);
		$i ++;

		$sisSegImp =  odbc_exec ($dbSisSeg, "SELECT Segurado.i_Seg, Importador.s_Imp, Importador.n_Pais, 
							    Importador.c_Coface_Imp, Importador.Nome, 
							    Importador.Endereco, Importador.Cidade, Importador.Telefone, 
							    Importador.v_Credito_Solicitado, Importador.v_Credito, 
							    Importador.s_Credito, Importador.s_Faturamento, 
							    Importador.d_Estudo, Importador.n_Imp
							FROM Base_Calculo INNER JOIN
							    Proposta ON 
							    Base_Calculo.c_Coface = Proposta.c_Coface AND 
							    Base_Calculo.n_Prop = Proposta.n_Prop INNER JOIN
							    Segurado ON Proposta.i_Seg = Segurado.i_Seg INNER JOIN
							    Importador ON Segurado.i_Seg = Importador.i_Seg
							WHERE (Base_Calculo.n_Endosso = 0) AND 
							    (Segurado.cookie IS NULL) AND Segurado.i_Seg = $i_Seg AND 
							    (Base_Calculo.d_Fim_Vig > GETDATE()) AND 
							    (Base_Calculo.s_Doc = 1 OR
							    Base_Calculo.s_Doc = 3) AND (Importador.s_Imp <> 0) AND 
							    NOT (Importador.s_Faturamento IS NULL)
							ORDER BY Importador.n_Imp, Importador.d_Estudo DESC");

		$j = 0;

		while (odbc_fetch_row ($sisSegImp)) {
			$importerIdCountry      = odbc_result ($sisSegImp, 3); 
		        $importerC_Coface_Imp   = odbc_result ($sisSegImp, 4);
			$importerName           = odbc_result ($sisSegImp, 5);
			$importerAddress        = odbc_result ($sisSegImp, 6);
			$importerCity           = odbc_result ($sisSegImp, 7);
			$importerTel            = odbc_result ($sisSegImp, 8); 
			$importerLimCredit      = odbc_result ($sisSegImp, 9);
			$changeCreditCredit     = odbc_result ($sisSegImp, 10);
			$changeCreditState      = odbc_result ($sisSegImp, 11);
			$invoiceState           = odbc_result ($sisSegImp, 12);

			$importerName = strtr($addr, "'", " ");

			print $importerName."\n";

			$country = odbc_exec ($db, "SELECT id FROM Country WHERE $importerIdCountry = code ");

			if (odbc_fetch_row ($country)) {
				$importerIdCountry = odbc_result ($country, 1);
			}

			if ($invoiceState == 0) {
				$stateInvoice = "4";
		       	} else if ($invoiceState == 1) {
				$stateInvoice = "2";
			} else if ($invoiceState == 2) {
				$stateInvoice = "3";
			} else if ($invoiceState == 3) {
				$stateInvoice = "2"; 
			} else if ($invoiceState == 4) {
				$stateInvoice = "1";
			} else {
				$stateInvoice = "0";
			}

       
		       	if ($changeCreditState == "2") {
				$stateCredit = "16";
			} else if ($changeCreditState  == "3" && $changeCreditState == "4") {
				$stateCredit = "17";
			} else {
				$stateCredit = "0";
			}	

			if (!($importerC_Coface_ImpAnterior == $importerC_Coface_Imp)) {	
				$siExImp = odbc_exec ($db, "Insert Into Importer (state, idInform, name,
							 address, idCountry, tel, prevExp12, numShip12,
							 periodicity, city, limCredit, c_Coface_Imp) 
							values ($stateCredit, $idInform, $importerName, 
							$importerAddress, $importerIdCountry, $importerTel,
							0, 0, 0, $importerCity, $importerLimCredit,
							 $importerC_Coface_Imp)");	       

				$siExIdImp = odbc_exec ($db, "SELECT id from Importer order by id desc");

				if (odbc_fetch_row ($siExIdImp)) {
					$idImporter = odbc_result ($siExIdImp, 1);
				}
			}	

		       	$siExUp = odbc_exec ($db, "INSERT INTO ChangeCredit (idImporter, credit, state, userIdChangeCredit)
						 VALUES ( $idImporter, $changeCreditCredit, $stateCredit, 88)");

			$siExUp = odbc_exec ($db, "Insert into Invoice (idImporter, state) values
						 ($idImporter, $stateInvoice)");
			       
			       $importerC_Coface_ImpAnterior   = $importerC_Coface_Imp;

			       $importerName           = "";
			       $importerAddress        = "";
			       $importerCity           = "";
			       $importerTel            = "";
			       $importerLimCredit      = "";
			       $changeCreditCredit     = "";
			       $changeCreditState      = "";
			   $j ++;	
		}
		print "id do segurado no SisSeg: ".$i_Seg." quantidade de Importadores: ".$j."\n";

	}
		$msg = "sucesso";
?>
		<br>
		<hr>
		<h1> <?php echo ($i % 2 == 0) ? "<font color=red>" : "<font color=blue>" ?>MSG:<?php echo $msg?> i:<?php echo $i?></font></h1>
		<hr>
	</body>
</html>