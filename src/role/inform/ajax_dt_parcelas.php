<?php 
	header('Content-type: text/json');

	$dataExplode = explode( "/", $_POST['dtVenc']);
	$numParc = $_POST['numParc'];
	$tipoVenc = $_POST['tipoVenc'];

	if($tipoVenc == 4){
		$tipoVenc = 6;
	}

	$dia = $dataExplode[0];
	$mes = $dataExplode[1];
	$ano = $dataExplode[2];
 	
 	$dataVencimento = array($_POST["dtVenc"]); 	
	for ($i=1;$i<$numParc;$i++){
    $mes = $mes + $tipoVenc;
		if ($mes > 12){
			$mes = $mes - 12;
      $ano = $ano + 1;
		}

		$dataVencimento[] = date("d/m/Y", mktime(0,0,0,$mes,$dia,$ano));
	}	

	echo json_encode($dataVencimento);
?>