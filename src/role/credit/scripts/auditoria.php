<html><head><title>TENDECIES CONSULTORIA</title><head>
<body>
<h1>AUDITANDO X4502060623.FL FRENTE AO BANCO DE DADOS DO SIEX</h1>
<br>
<?php  $base = odbc_exec ($db, "SELECT informContrat, importerCountryCode, importerC_Coface_Imp
				FROM Auditoria");
	while (odbc_fetch_row ($base)) {
		
	}
?>
</body></html>