<?php  $limCred = number_format($limCred, 2, ",", ".");
	$changeCredit = number_format($changeCredit, 2, ",", ".");
       	$changeDate = substr($changeDate, 8, 2)."/".substr($changeDate, 5, 2)."/".substr($changeDate, 0, 4);
?>

<html>
	<head>	
		<title>SBCE</title>
	</head>

	<body>
		<br>
		URGENTE<br>
		<br>
		<br>
		Prezados Senhores, <?php  echo $nameCl;?> ,<br>
		<br>
		<br>
		Segue a posição de crédito de seu importador:<br>
		<br>
		CI SEGURADO: <?php  echo $coface;?><br>
		NOME DO IMPORTADOR: <?php  echo $name;?><br>
		ENDEREÇO DO IMPORTADOR: <?php  echo $address;?> <?php  echo $city;?> <?php  echo $tel;?><br>
		PAÍS: <?php  echo $country;?><br>
		CRÉDITO SOLICITADO: US$<?php  echo $limCred;?><br>
		CRÉDITO CONCEDIDO: US$<?php  echo $changeCredit;?><br>
		DATA DA DECISÃO: <?php  echo $changeDate;?><br>
		<br>
		Caso haja alguma exportação em curso, favor entrar em contato no prazo máximo de oito dias.<br>
		<br>
		Qualquer dúvida, estaremos à disposição para maiores esclarecimentos.<br>
		<br>
		<br>
		Atenciosamente,<br>
		<br>
		<br>
		<br>
		Elisa Salomão<br>
		Departamento de Crédito<br>
		</p>
	</body>
</html>