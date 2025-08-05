<?php  $stateDate = substr($stateDate, 8, 2)."/".substr($stateDate, 5, 2)."/".substr($stateDate, 0, 4);
?>

<html>

	<head>
		<title>SBCE</title>
	</head>

	<body>

	<p>Aviso de Alteração Cadastral de Importadores<br>
	<br>	
	URGENTE<br>
	<br>
	<br>
	Prezados Senhores, <?php  echo $nameCl;?>,<br>
	<br>
	<br>
	Segue alteração cadastral de seu importador:<br>
	<br>
	CI SEGURADO: <?php  echo $coface;?><br>
	NOME DO IMPORTADOR: <?php  echo $newName;?><br>
	ENDEREÇO DO IMPORTADOR: <?php  echo $newAddress;?> <?php  echo $newCity;?> <?php  echo $newTel;?><br>
	PAÍS: <?php  echo $newCountry;?><br>
	<br>
	<br>
	<br>
	Tendo em vista a mudança ocorrida em certas características de seu importador, a aprovação que V. S.ª dispunha passa, de hoje em diante, para o organismo supra citado.<br>
	<br>
	DATA DA DECISÃO: <?php  echo $stateDate;?><br>
	RAZÃO SOCIAL ANTERIOR: <?php  echo $name;?><br>
	ENDEREÇO ANTERIOR: <?php  echo $address;?> <?php  echo $city;?> <?php  echo $tel;?><br>
	PAÍS ANTERIOR: <?php  echo $country;?><br>
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
	<br>
	</p>

	</body>

</html>
