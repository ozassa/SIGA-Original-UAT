<?php 

    include_once("policyData.php");

	$sql = "SELECT i_Modulo, Cod_Modulo, Grupo_Modulo, Titulo_Modulo, ISNULL(Texto_Modulo, '') Texto_Modulo, Ordem_Modulo, s_Modulo
					FROM Modulo
					ORDER BY Ordem_Modulo";

	$cur = odbc_exec($db, $sql);

	$dados_sel = array();
	while(odbc_fetch_row($cur)) {
		$id_mod = odbc_result($cur, "i_Modulo");
		$cod_mod = odbc_result($cur, "Cod_Modulo");
		$grupo_mod = odbc_result($cur, "Grupo_Modulo");
		$titulo_mod = odbc_result($cur, "Titulo_Modulo");
		$txt_mod = odbc_result($cur, "Texto_Modulo");
		$ordem_mod = odbc_result($cur, "Ordem_Modulo");
		$sit_mod = odbc_result($cur, "s_Modulo");

		$dados_sel[] = array(
			"id_mod" => $id_mod,
			"cod_mod" => $cod_mod,
			"grupo_mod" => $grupo_mod,
			"titulo_mod" => $titulo_mod,
			"txt_mod" => $txt_mod,
			"ordem_mod" => $ordem_mod,
			"sit_mod" => $sit_mod
		);
	}

    $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4',
        'margin_left' => 20,
        'margin_right' => 15,
        'margin_top' => 30,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
        ];

    $mpdf=new  \Mpdf\Mpdf($opt);
	$html = ob_get_clean();
	$mpdf->SetTitle("Proposta");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText("");
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');

 	$logo = $root.'images/logo.jpg';
  
  $html = '
			<html>
			   <body>
			      <htmlpageheader name="myheader2">
			         <div style="text-align: left;margin-top:-200px">
			            <span style="font-weight: bold; font-size: 16px;">M&Oacute;DULOS CONTRATADOS</span>
			         </div>
			         <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; ">
			      </htmlpageheader>
			      <htmlpageheader name="myheader">
			         <div style="text-align: left;margin-top:-200px">
			            <span style="font-weight: bold; font-size: 16px;">M&Oacute;DULOS CONTRATADOS</span>
			         </div>
			         <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
			      </htmlpageheader>
			      <htmlpagefooter name="myfooter">
			         <table style="width:100%; border: 0;" >
			            <tr>
			               <td style="width: 22%;">&nbsp;</td>
			               <td style="width: 56%; text-align:center; font-size: 8pt;">
			                  P&aacute;gina {PAGENO} de {nb}
			                  <br><br>


			               </td>
			               <td style="width: 22%;">&nbsp;</td>
			            </tr>
			         </table>
			      </htmlpagefooter>
			      <sethtmlpageheader name="myheader2" value="on" show-this-page="1" />
			      <sethtmlpageheader name="myheader" value="on" show-this-page="0" />
			      <sethtmlpagefooter name="myfooter" value="on" />';

					    if(empty($dados_sel)){

					      $html .= '<tbody><tr><td style="vertical-align: top;" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>';

					    } else {
					    	$modulo_select = '';
				      	for ($i=0; $i < count($dados_sel); $i++) {

					        if($dados_sel[$i]["grupo_mod"] != $modulo_select){
					        	$modulo_select = $dados_sel[$i]["grupo_mod"];

						        $html .= '<h4>'.$dados_sel[$i]["grupo_mod"].'</h4>';

									}
									
									$html .= '<h6>'.$dados_sel[$i]["cod_mod"].' - '.$dados_sel[$i]["titulo_mod"].'</h6>
														<span style="font-size:12px;">'.$dados_sel[$i]["txt_mod"].'</span>';

	        			}
					    }

						$html .= '
			   </body>
			</html>';
  
   $html = utf8_encode($html);
   $mpdf->allow_charset_conversion=true;
   $mpdf->charset_in='UTF-8';
   $mpdf->WriteHTML($html);

   $mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>