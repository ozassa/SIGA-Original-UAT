<?php 
	require_once('../../../../PHPExcel.php');
	require_once("../../../dbOpen.php");

	if(! function_exists('ymd2dmy')){
		function ymd2dmy($d){
	    if(preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d, $v)){
	    	return "$v[3]/$v[2]/$v[1]";
	    }
	    
	    return $d;
	  }
	}

	$Id_Banco = "1";
	$d_Inicio_Vigencia = $_REQUEST['d_Inicio_Vigencia'] != "NULL" ? "'".$_REQUEST['d_Inicio_Vigencia']."'" : "NULL";
	$d_Fim_Vigencia = $_REQUEST['d_Fim_Vigencia'] != "NULL" ? "'".$_REQUEST['d_Fim_Vigencia']."'" : "NULL";
	$Id_Usuario = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;

	$sql = "EXEC SPR_BB_Relatorio_Apolices ?,?,?,?,?";
	$params = [$Id_Banco, $d_Inicio_Vigencia, $d_Fim_Vigencia, $Id_Usuario];
	$rsSql = odbc_prepare($db, $sql);
	odbc_execute($rsSql, $params);

	$dados = array();
	while(odbc_fetch_row($rsSql)) {
		$Nome_Regiao = utf8_encode(odbc_result($rsSql, "Nome_Regiao"));
		$Nome_Agencia = utf8_encode(odbc_result($rsSql, "Nome_Agencia"));
		$n_Apolice = odbc_result($rsSql, "n_Apolice");
		$Segurado = utf8_encode(odbc_result($rsSql, "Segurado"));
		$d_Inicio_Vigencia = ymd2dmy(odbc_result($rsSql, "d_Inicio_Vigencia"));
		$d_Fim_Vigencia = ymd2dmy(odbc_result($rsSql, "d_Fim_Vigencia"));
		$Sigla_Moeda = odbc_result($rsSql, "Sigla_Moeda");
		$v_Premio_Emitido = number_format(odbc_result($rsSql, "v_Premio_Emitido"), 2, ",", ".");
		$v_Premio_Pago = number_format(odbc_result($rsSql, "v_Premio_Pago"), 2, ",", ".");
		$v_Premio_Vencido = number_format(odbc_result($rsSql, "v_Premio_Vencido"), 2, ",", ".");
		$v_Sinistro_Pago = number_format(odbc_result($rsSql, "v_Sinistro_Pago"), 2, ",", ".");
		$v_Sinistro_Pendente = number_format(odbc_result($rsSql, "v_Sinistro_Pendente"), 2, ",", ".");
		$v_LMI = number_format(odbc_result($rsSql, "v_LMI"), 2, ",", ".");
		$v_LMI_Disponivel = number_format(odbc_result($rsSql, "v_LMI_Disponivel"), 2, ",", ".");

		$dados[] = array(
			"Nome_Regiao" => $Nome_Regiao,
			"Nome_Agencia" => $Nome_Agencia,
			"n_Apolice" => $n_Apolice,
			"Segurado" => $Segurado,
			"d_Inicio_Vigencia" => $d_Inicio_Vigencia,
			"d_Fim_Vigencia" => $d_Fim_Vigencia,
			"Sigla_Moeda" => $Sigla_Moeda,
			"v_Premio_Emitido" => $v_Premio_Emitido,
			"v_Premio_Pago" => $v_Premio_Pago,
			"v_Premio_Vencido" => $v_Premio_Vencido,
			"v_Sinistro_Pago" => $v_Sinistro_Pago,
			"v_Sinistro_Pendente" => $v_Sinistro_Pendente,
			"v_LMI" => $v_LMI,
			"v_LMI_Disponivel" => $v_LMI_Disponivel
		);
	}

	odbc_close($db);

	// Criar um novo objeto PHPExcel
	$objPHPExcel = new PHPExcel();

	$styleArrayHeader = array(
		'borders' => array(
			'inside'     => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array(
					'argb' => '000000'
				)
			),
			'outline'     => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array(
					'argb' => '000000'
				)
			)
		),
		'font' => array(
			'name' => 'Calibri',
			'size' => 11,
			'bold' => true,
			'color' => array(
				'rgb' => '000000'
			),
		),
	);

	$styleArrayCont = array(
		'borders' => array(
			'inside'     => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array(
					'argb' => '000000'
				)
			),
			'outline'     => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array(
					'argb' => '000000'
				)
			)
		),
		'font' => array(
			'name' => 'Calibri',
			'size' => 11,
			'bold' => false,
			'color' => array(
				'rgb' => '000000'
			),
		),
	);

	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:L1')->applyFromArray($styleArrayHeader);

	for($col = 'A'; $col !== 'M'; $col++) {
    $objPHPExcel->getActiveSheet()
        				->getColumnDimension($col)
        				->setAutoSize(true);
	}

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', 'Região')
							->setCellValue('B1', 'Agência')
							->setCellValue('C1', 'Nº da Apólice')
							->setCellValue('D1', 'Segurado')
							->setCellValue('E1', 'Ini. Vigência')
							->setCellValue('F1', 'Fim. Vigência')
							->setCellValue('G1', 'Valor Total de Prêmio Emitido')
							->setCellValue('H1', 'Valor Total de Prêmio Pago')
							->setCellValue('I1', 'Valor Total dos Sinistros Pagos')
							->setCellValue('J1', 'Valor Total de Sinistro em Reserva')
							->setCellValue('K1', 'Valor do Limite Máximo de Indenização da Apólice')
							->setCellValue('L1', 'Saldo Disponível do Limite Máximo de Indenização da Apólice');

	for($a=0;$a<count($dados);$a++){
		$num = $a + 2;

		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$num.':L'.$num)->applyFromArray($styleArrayCont);
		
		$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$num, $dados[$a]['Nome_Regiao'])
								->setCellValue('B'.$num, $dados[$a]['Nome_Agencia'])
								->setCellValue('C'.$num, $dados[$a]['n_Apolice'])
								->setCellValue('D'.$num, $dados[$a]['Segurado'])
								->setCellValue('E'.$num, $dados[$a]['d_Inicio_Vigencia'])
								->setCellValue('F'.$num, $dados[$a]['d_Fim_Vigencia'])
								->setCellValue('G'.$num, $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Premio_Emitido'])
								->setCellValue('H'.$num, $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Premio_Pago'])
								->setCellValue('I'.$num, $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Sinistro_Pago'])
								->setCellValue('J'.$num, $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Sinistro_Pendente'])
								->setCellValue('K'.$num, $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_LMI'])
								->setCellValue('L'.$num, $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_LMI_Disponivel']);
	}

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="relatorio_apolice.xlsx"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("php://output");
	exit;
?>