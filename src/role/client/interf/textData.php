<?php  $stateDate = substr($stateDate, 8, 2)."/".substr($stateDate, 5, 2)."/".substr($stateDate, 0, 4);
?>

<html>

	<head>
		<title>SBCE</title>
	</head>

	<body>

	<p>Aviso de Altera��o Cadastral de Importadores<br>
	<br>	
	URGENTE<br>
	<br>
	<br>
	Prezados Senhores, <?php  echo $nameCl;?>,<br>
	<br>
	<br>
	Segue altera��o cadastral de seu importador:<br>
	<br>
	CI SEGURADO: <?php  echo $coface;?><br>
	NOME DO IMPORTADOR: <?php  echo $newName;?><br>
	ENDERE�O DO IMPORTADOR: <?php  echo $newAddress;?> <?php  echo $newCity;?> <?php  echo $newTel;?><br>
	PA�S: <?php  echo $newCountry;?><br>
	<br>
	<br>
	<br>
	Tendo em vista a mudan�a ocorrida em certas caracter�sticas de seu importador, a aprova��o que V. S.� dispunha passa, de hoje em diante, para o organismo supra citado.<br>
	<br>
	DATA DA DECIS�O: <?php  echo $stateDate;?><br>
	RAZ�O SOCIAL ANTERIOR: <?php  echo $name;?><br>
	ENDERE�O ANTERIOR: <?php  echo $address;?> <?php  echo $city;?> <?php  echo $tel;?><br>
	PA�S ANTERIOR: <?php  echo $country;?><br>
	<br>
	Qualquer d�vida, estaremos � disposi��o para maiores esclarecimentos.<br>
	<br>
	<br>
	Atenciosamente,<br>
	<br>
	<br>
	<br>
	Elisa Salom�o<br>
	Departamento de Cr�dito<br>
	<br>
	</p>

	</body>

</html>
