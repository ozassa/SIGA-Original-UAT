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
		Segue a posi��o de cr�dito de seu importador:<br>
		<br>
		CI SEGURADO: <?php  echo $coface;?><br>
		NOME DO IMPORTADOR: <?php  echo $name;?><br>
		ENDERE�O DO IMPORTADOR: <?php  echo $address;?> <?php  echo $city;?> <?php  echo $tel;?><br>
		PA�S: <?php  echo $country;?><br>
		CR�DITO SOLICITADO: US$<?php  echo $limCred;?><br>
		CR�DITO CONCEDIDO: US$<?php  echo $changeCredit;?><br>
		DATA DA DECIS�O: <?php  echo $changeDate;?><br>
		<br>
		Caso haja alguma exporta��o em curso, favor entrar em contato no prazo m�ximo de oito dias.<br>
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
		</p>
	</body>
</html>