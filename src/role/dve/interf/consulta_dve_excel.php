<?php

session_start();

$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';

	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-Disposition: attachment; filename=dve.xls");  //File name extension was wrong
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	require_once("../../../dbOpen.php");

	$html .= '<body>';
	
	$EXECUTAR      = $_REQUEST['EXECUTAR'];
	$nomeSegurado  = $_REQUEST['nomeSegurado'];
	$Apolice       = $_REQUEST['Apolice'];
	$Dpp           = $_REQUEST['Dpp'];
	$MesAno        = $_REQUEST['MesAno'];
	$stateDVE      = $_REQUEST['stateDVE'];

    $compl = '';
    $junta = '';

	if ($EXECUTAR == "1") {
		$sql = "SELECT
					Inf.contrat AS DPP,
					Inf.n_Apolice AS Apolice,
					Upper(Inf.name) AS Segurado,
					D.num AS Num_DVN,
					Cast(Day(D.inicio) as varchar) + '/' + Cast(Month(D.inicio) as varchar) + '/' + Cast(Year(D.inicio) as varchar) + ' até ' + 
					ISNull(Cast(Day(DateAdd(D, -1, DP.inicio)) as varchar) + '/' + Cast(Month(DateAdd(D, -1, DP.inicio)) as varchar) + '/' + Cast(Year(DateAdd(D, -1, DP.inicio)) as varchar), 
					Cast(Day(Inf.endValidity) as Varchar) + '/' + Cast(Month(Inf.endValidity) as Varchar) + '/' + Cast(Year(Inf.endValidity) as Varchar)) AS Periodo,
					IsNull(Sum(DD.totalEmbarcado), IsNull(D.Valor_Periodo, 0)) AS Total_Embarcado,
					Situacao.Descricao_Item AS Situacao
				FROM
					Inform Inf
				INNER JOIN DVE D ON
					D.idInform = Inf.id
				LEFT JOIN (
					SELECT DD.idDVE, SUM(DD.totalEmbarcado) AS totalEmbarcado 
					FROM DVEDetails DD 
					WHERE DD.state = 1 
					GROUP BY DD.idDVE
				) DD ON DD.idDVE = D.id
				LEFT JOIN DVE DP ON
					DP.idInform = Inf.id
					AND DP.num = D.num + 1
				LEFT JOIN Campo_Item Situacao ON
					Situacao.i_Campo = 600
					AND Situacao.i_Item = D.state
				WHERE Inf.n_Apolice IS NOT NULL";
	
		$params = [];
		$compl = '';
	
		if ($nomeSegurado != '') {
			$compl .= " AND Upper(Inf.name) LIKE ?";
			$params[] = '%' . strtoupper($nomeSegurado) . '%';
		}
	
		if ($Apolice != '') {
			$compl .= " AND Inf.n_Apolice = ?";
			$params[] = $Apolice;
		}
	
		if ($Dpp != '') {
			$compl .= " AND Inf.contrat = ?";
			$params[] = $Dpp;
		}
	
		if ($MesAno != '') {
			$mes = explode("/", $MesAno);
			$compl .= " AND Month(D.inicio) = ? AND Year(D.inicio) = ?";
			$params[] = $mes[0];
			$params[] = $mes[1];
		}
	
		if ($stateDVE > 0) {
			$compl .= " AND D.state = ?";
			$params[] = $stateDVE;
		}
	
		$sql .= $compl . "
				GROUP BY
					Inf.contrat,
					Inf.n_Apolice,
					Inf.name,
					Inf.endValidity,
					D.num,
					D.inicio,
					D.Valor_Periodo,
					DP.inicio,
					Situacao.Descricao_Item";
	
		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, $params);
	
		$i = 0;
		$total = 0;
		$total2 = 0;
	}
		
	$html .= '
    <table id="example" class="tabela01"> 
       <thead>
          <tr>
            <th >DPP</th>
            <th >Ap&oacute;lice</th>
            <th >Segurado</th>
            <th >Num DVN</th>
            <th >Per&iacute;odo</th>
            <th style="text-align:right !important;">Total Embarcado</th>
            <th style="text-align:center !important;">Situa&ccedil;&atilde;o</th>
          </tr>
      </thead>
      <tbody>';

         while(odbc_fetch_row($cur))  {
             	$html .= '<tr>
	                 <td>'.odbc_result($cur,'DPP').'</td>
	                 <td>'.odbc_result($cur,'Apolice').'</td>
							<td>'.mb_convert_encoding(odbc_result($cur, 'Segurado'), 'ISO-8859-1', 'UTF-8').'</td>
							<td>'.mb_convert_encoding(odbc_result($cur, 'Num_DVN'), 'ISO-8859-1', 'UTF-8').'</td>
							<td>'.mb_convert_encoding(odbc_result($cur, 'Periodo'), 'ISO-8859-1', 'UTF-8').'</td>
	                 <td style="text-align:right !important;">'.number_format(odbc_result($cur,'Total_Embarcado'),2,',','.').'</td>
	                 <td style="text-align:center !important;">'.odbc_result($cur,'Situacao').'</td>          
	              	</tr>';  
          		$total  += odbc_result($cur,'Total_Embarcado');
	    }

      	$html .= '</tbody>
        <tfoot>
            <th colspan="5" style="text-align:right !important;">Total Embarcado</th>
            <th style="text-align:right !important;">'.number_format($total,2,',','.').'</th>
            <th>&nbsp;</th>        
        </tfoot>  
     </table>

</body>
</html>';

echo "\xEF\xBB\xBF"; //UTF-8 BOM
echo mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
