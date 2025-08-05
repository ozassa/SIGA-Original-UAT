<script type="text/javascript">
var xmlhttp;
function loadXMLDoc(importadores)
{
	url='interf/imPrimeImportadodres.php?importer='+importadores;
xmlhttp=null;
if (window.XMLHttpRequest)
  {// code for Firefox, Opera, IE7, etc.
  xmlhttp=new XMLHttpRequest();
  }
else if (window.ActiveXObject)
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
if (xmlhttp!=null)
  {
  xmlhttp.onreadystatechange=state_Change;
  xmlhttp.open("GET",url,true);
  xmlhttp.send(null);
  }
else
  {
  verErro("Your browser does not support XMLHTTP.");
  }
}

function state_Change()
{
if (xmlhttp.readyState==4)
  {// 4 = "loaded"
  if (xmlhttp.status==200)
    {// 200 = "OK"
	
    document.getElementById('T1').innerHTML=xmlhttp.responseText;
    }
  else
    {
    verErro("Problem retrieving data:" + xmlhttp.statusText);
    }
  }
}
</script>
<?php
	//number_format ($analiseT,2,',','.')
	//função para criar a data
	function mkdate ($a, $m, $d) 
	{
	  return date ("Y-m-d", mktime (0, 0, 0, $m, $d, $a));
	}
	
	//função para o SisSeg
	//função que envia para o SisSeg
	function addSisSeg ($i_Seg, $c_Coface, $nSeq, $parc, $dbSisSeg, $ano, $mes, $nProp) 
	{

	  $curBC = odbc_exec (
			      $dbSisSeg,
			      "SELECT * FROM Base_Calculo WHERE i_BC = (SELECT MAX(i_BC) FROM Base_Calculo WHERE i_Seg = $i_Seg and n_Prop = $nProp and n_Endosso = 0)"
			     );

	  if (!odbc_fetch_row ($curBC)) {
	  	echo "SELECT * FROM Base_Calculo WHERE i_BC = (SELECT MAX(i_BC) FROM Base_Calculo WHERE i_Seg = $i_Seg and n_Prop = $nProp and n_Endosso = 0)"."<br>";
	  	return false;
	  }
	  
	  $nEndosso = odbc_result($curBC, 'n_Endosso');
	  if ($nEndosso == '') $nEndosso = 'NULL';

	  if ($nProp == '') $nProp = 'NULL';
	  

	  $hcparc = number_format ($parc,2,".",",");
	  $hcparc = str_replace (",","",$hcparc);
	  
	  $query = 
	    " INSERT INTO Parcela".
	    "   ( ".
	    "     n_Sucursal,".
	    "     n_Ramo,".
	    "     n_Apolice,".
	    "     n_Endosso,".
	    "     c_Coface,".
	    "     i_Seg,".
	    "     d_Parcela,".
	    "     n_Seq_Parcela,".
	    "     t_parcela,".
	    "     v_Parcela,".
	    "     n_Moeda,".
	    "     s_Parcela,".
	    "     n_Prop,".
	    "     v_Frac,".
	    "     i_BC".
	    "   )".
	    " VALUES".
	    "   (".
	    "     ".odbc_result($curBC, 'n_Sucursal').",".                 // -- Sucursal
	    "     49,".                                                    // -- Ramo
	    "     ".odbc_result($curBC, 'n_Apolice').",".                  // -- n_Apolice
	    "     0,".                                                     // -- n_Endosso
	    "     ".$c_Coface.",".                                         // -- c_Coface
	    "     ".$i_Seg.",".                                            // -- i_Seg
	    "     getdate(),".                                             // -- data de criação da parcela
	    "     $nSeq,".                                                 // -- número de sequencia da parcela*
	    "     3,".                                                     // -- t_Parcela - tipo de parcela *
	    "     $hcparc,".                                                 // -- Valor da Parcela
	    "     1,".                                                     // -- Moeda,
	    "     0,".                                                     // -- Situação da Parcela *
	    "     $nProp,".                                                // -- n_Prop
	    "     0,".                                                     // -- v_Frac
	    "     " . odbc_result($curBC, 'i_BC').                           // -- i_BC
	    "   ) ";
		
		
	 // echo "<p>$query</p>";

	 
	  if (odbc_exec ($dbSisSeg, $query)) {
	  
	    $curParc = odbc_exec ($dbSisSeg, "SELECT i_Parcela FROM Parcela WHERE i_Parcela = (SELECT MAX (i_Parcela) FROM Parcela WHERE i_Seg = $i_Seg)");
	    if (!odbc_fetch_row($curParc)) return false;
	    $query = "
    INSERT INTO Parc_Serv_Ger (i_BC, Ano, Mes, i_Parcela)
    VALUES (?, ?, ?, ?)";
$stmt = odbc_prepare($dbSisSeg, $query);
$r = odbc_execute($stmt, [
    odbc_result($curBC, 'i_BC'),
    $ano,
    $mes,
    odbc_result($curParc, 'i_Parcela')
]);

if ($r) {
    return true;
}

	  
	  }
	  //echo "<p>$query</p>";
	  //return true;
	  
	  return false;
	}
		
	
	odbc_autocommit($db, false);
	odbc_autocommit($dbSisSeg, false);
	
	//se o mes de envio já foi enviado então não será gerado as rotinas para envio para o SisSeg, do contrário serão executadas todas as rotinas
	// Acesso à nova estrutura de análise e monitoramento
$cur = odbc_exec ($db,"SELECT id FROM resFatAnaliseMonitor WHERE mes=$mes AND ano = $ano");

//if (!odbc_fetch_row ($cur)) {
if (odbc_fetch_row ($cur)) {
	
	//esta função cria as datas de cobrança, sendo que faz um calculo para retornar as 4 parcelas anteriores e com isto chegar ao mes
	//inicial de vigencia da apolice, inform
	//include("funcCriaTrimesAnt.php");
	$anoCorrente = date('Y');
	$mesCorrente = date('m');
	
	/* if(($_REQUEST['ano'] < $anoCorrente || $_REQUEST['mes'] < $mesCorrente) && $_REQUEST['ano']!="" && $_REQUEST['mes']!="")
	{
		echo "<script>location.href='".$root."role/credit/Credit.php?comm=resMonitor&ano=".$_REQUEST['ano']."&mes=".$_REQUEST['mes']."&ret=retornar'</script>";
		break;
	} */
	
	
	if($_REQUEST['ano'])
	{
		$ano          = $_REQUEST['ano'];
	}else
	{	
		$ano          = date("Y");   //PEGA O ANO ATUAL
	}
	if($_REQUEST['mes'])
	{
		$mesIniCorr   = $_REQUEST['mes']+1;
	}else
	{
		$mesIniCorr   = date("m")+ 1;//PEGA O MES ATUAL E SOMA MAIS 1 PARA DAR O MES SEGUINTE
	}	
	//$datasTrimes = criaTrimesAnt($ano,$mesIniCorr);//pega a primeira data 	
	
	
	$dtIniUltimoTrimestre=mkdate($ano, $mesIniCorr - 4, 1);
	$dtFimUltimoTrimestre=mkdate($ano, $mesIniCorr - 3, 0);
	
	//variaveis que cria os 4 trimestres anteriores de forma que seja possível pegar os expotadores no trimestre certo
	//pois as cobrnças são realizadas de 3 em 3 meses
	 $dt4Ini = mkdate($ano, $mesIniCorr - 3, 1);
	 $dt4Fim = mkdate($ano, $mesIniCorr - 2, 0);
	
	 $dt3Ini = mkdate($ano, $mesIniCorr - 6, 1);
	 $dt3Fim = mkdate($ano, $mesIniCorr - 5, 0);
	
	 $dt2Ini = mkdate($ano, $mesIniCorr - 9, 1);
	 $dt2Fim = mkdate($ano, $mesIniCorr - 8, 0);
	
	 $dt1Ini = mkdate($ano, $mesIniCorr - 12, 1);
	 $dt1Fim = mkdate($ano, $mesIniCorr - 11, 0);
	
	$datasTrimes="(startValidity >= '$dt1Ini' AND startValidity <= '$dt1Fim')
			   OR (startValidity >= '$dt2Ini' AND startValidity <= '$dt2Fim')
			   OR (startValidity >= '$dt3Ini' AND startValidity <= '$dt3Fim')
			   OR (startValidity >= '$dt4Ini' AND startValidity <= '$dt4Fim')
				";
	
	//echo $dtIniUltimoTrimestre."-".$dtFimUltimoTrimestre;
	$dataCobrançaUltimoTrimestre = "'$dtIniUltimoTrimestre' and '$dtFimUltimoTrimestre'";	
	//esta query traz todos os exportadores vigentes e os encerrados no ultimo trimestre
	$query = "select name,id,idAnt
				from Inform where state = 10 and (txAnalize<>0 or txMonitor<>0)
				AND ($datasTrimes) 
				order by name";

	//echo "<pre>$query</pre>";
	
	//break;
	$cur = odbc_exec($db, $query);
?>

<strong style="color:#4682B4">Em Desenvolvimento!!!!!!!!</strong><br><br><br><br>


<?php
	//ESTA FUNÇÃO IRÁ AJUDAR A PEGAR A TAXA E OS INFORMES DESTE EXPORTADOR
	include_once("funcTaxaInform.php");
	$arrTodosImportadores = array();
	
	/* ESTA PARTE ABAIXO IRÁ LISTAR TODOS OS EXPORTADORES E ARMAZENARÁ EM UM ARRAY, APENAS OS NOMES, PARA DEPOIS TRABALHAR
		COM ESTES NOMES DEPOIS.
	*/
	$arrFech = array();
	$id = 1;
	$cont = 1;
	//echo "Total de Exportadores:<strong style='color:#4682B4'>".odbc_num_rows($cur)."</strong><br><br>";
	while (odbc_fetch_row ($cur)) 
	{
		//aqui pega todos os campos da query que são necessários
		$nome  = odbc_result ($cur, 'name');
		$id    = odbc_result ($cur, 'id');
		$idAnt = odbc_result ($cur, 'idAnt');
		
		//if("LG ELECTRONICS DA AMAZONIA LTDA"==$nome || "ODEBRECHT COMERCIO E INDUSTRIA DE CAFE LTDA"==$nome)
		//{
			//chama a função que irá pegar todos os dados do exportador
			$dadosDoExportador = taxaInform($nome,$id,$idAnt,$db);
			
			//armazena todos arrays passados em um unico array
			array_push($arrTodosImportadores,$dadosDoExportador);		
			
			//echo $chave."::<strong style='color:#4682B4'>".$valor."</strong><br>";			
			//a estrutura abaixo serve unicamente para avaliarmos o primeiro exportador, no caso é alcan(Isto é uma FLAG PARA TESTE)
			if($cont == 1)
			{
				break;
			}else
			{
				continue;
			}   
		//}
		$cont++;
	}
	
	/* echo "<pre>";
	print_r($arrTodosImportadores);
	echo "</pre>"; */  
	
	
	function inativaComCobAnalise($db,$idImporter)
	{		
		$sql2="select top 1 analysis,limTemp from ChangeCredit where idImporter=$idImporter ORDER BY id desc";
		$rs2 = odbc_exec($db, $sql2);
		$analysis = odbc_result ($rs2,1);
		$limTemp    = odbc_result ($rs2,2);
		$dateLimTemp = explode(" ",$limTemp);
		
		return $analysis.";".$dateLimTemp[0];
	}
	

	
	//este array é criado para poder pegar o complemento do primeiro array que é o seguinte, depois de pegar os dados do exportador junta 
	//com os importadores e seus daos
	$arrImporterDados = array();
	
	//este foreach ira pegar todos os importadores e realizar uma serie  de verificações para poder pular os importer do exportador especifico
	include("arrayDadosImporterDeExportadores.php");//foreach esta dentro de um arquivo porque o código ficou grande
	
	/*  echo "<pre>";
	print_r($arrImporterDados);
	echo "</pre>"; */  
	
	//monta os títulos da tabela
	/* echo "<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%'>
			<tbody>
			<tr class='bgAzul'>
			<th class='bgAzul' align='left'>Nº</th>
			<th class='bgAzul' align='left'>Segurado</th>
			<th class='bgAzul'>Qtde. Análises</th>
			<th class='bgAzul'>Análises</th>
			<th class='bgAzul'>Qtde. Monitor.</th>
			<th class='bgAzul'>Monitoramento</th>
			<th>Total</th>
			</tr>"; */
	

	$linha=1;
	//apartir deste array que foi montado, temos informações relativas as taxas de analise e monitoramento do exportador, e seus importadores, bem como
	//se os mesmos estavam presentes em outras apolices, informes anteriores, apartir de agora segue a verificação de cada importador para saber se dele
	//ser ou não cobrado as taxas para cada um e sendo assim somar todas as analises e monitoramentos por exportador, de forma que teremos um resumo
	//final por exportador
	// o array a ser utilizado para verificação é este $arrImporterDados
	//percorre todos os exportadores e seus importadores
		$ultimaCob = mkdate(date("Y"),(date("m")+1)-3,0)." 00:00:00.000";//deixar isto automático
		$penultimaCob = mkdate(date("Y"),(date("m")+1)-6,0);
		$ultimoDiaDataCob = explode(" ",$ultimaCob);
		
		//*********************************************************************************************************
		//variaveis usadas para o envio ao SisSeg
		$idImporter = 0;
		$idInform = 0;
		$parc = 0;
		$i_Seg = 0;
		$contrat = 0;
		$ok = true;
		$total = 0;

		$gpc_txA   = 0;
		$qt_analise = 0;
		$gpc_txM   = 0;
		$qt_monitor = 0;  
		$arrExportador = "";	
		
	$tot = count($arrImporterDados);
	for($l=0;$l<$tot;$l++)
	{
		//esta área serve para poder quebrar as string que estão dentro de alguns indices de array, sendo possivel recuperar seus valores individuais
		$taxas              = explode(",",$arrImporterDados[$l]['TAXASAM']);
		$inf                = explode(",",$arrImporterDados[$l]['INFORMS']);
		$state              = explode(",",$arrImporterDados[$l]['STATE']);
		$endValid="";
		
		$txAnalise          = $taxas[0];//analise
		$txMonitoramento    = $taxas[1];//monitoramento
		$informes           = $inf[0];
		$informesGerais     = $inf[0].";".$inf[1];
		$stateGerais        = $state[0].";".$state[1];
		
		if(count($state)==3)
		{
			$endValid = $endValidAtual  = explode(",",$arrImporterDados[$l]['FIMDATEAPOLICE']);
			//$endValid       = explode(";",$endValidAtual[0]);
		}
		else if(count($state)==2)
		{
			$endValid = $endValidAtual  = explode(",",$arrImporterDados[$l]['FIMDATEAPOLICE']);
			//$endValid       = explode(";",$endValidAtual[1]);
			
		}

				
		//armazena nas variaveis os valores encontrados dentro de cada indice, para ser uilizado depois
		$exportador         = $arrImporterDados[$l]['EXPORTADOR'];
		
		//*****SisSeg -SisSeg-SisSeg-SisSeg-SisSeg-SisSeg-SisSeg-SisSeg-SisSeg-SisSeg-SisSeg-SisSeg*******************
		//dados levantados unicamente par ao lance de enviar para o SisSeg*************************************************
		$startValidity1      = $arrImporterDados[$l]['startValidity'];//**********************************
		$i_Seg1              = $arrImporterDados[$l]['i_Seg'];//*******************************************
		$contrat1            = $arrImporterDados[$l]['contrat'];//*****************************************
		$nProp1              = $arrImporterDados[$l]['nProp'];//*******************************************
		
		
		  $idInform = $inf[0];
		  $i_Seg   = $i_Seg1;
		  $contrat = $contrat1;
		  $startValidity = $startValidity1;
		  $mesVig = substr ($startValidity, 5, 2);
		  $nProp =  $nProp1;
		  
		  if ($mesVig < ($mes + 1))
			 $nSeq = ($mes + 1 - $mesVig) / 3;
		  else
			 $nSeq = ($mes + 13 - $mesVig) / 3;

			// Acesso à nova estrutura de análise e monitoramento
		  $query = "
    INSERT INTO resFatAnaliseMonitor (idInform, mes, ano, txAnalise, txMonitor)
    VALUES (?, ?, ?, ?, ?)";
		$stmt = odbc_prepare($db, $query);
		odbc_execute($stmt, [$idInform, $mes, $ano, $txAnalise, $txMonitoramento]);

		$r = $stmt;

		odbc_free_result($stmt);

		  if ($idExporterReport == 0) {
		   // Acesso à nova estrutura de análise e monitoramento
			 $curRep = odbc_exec ($db, "SELECT MAX (id) FROM resFatAnaliseMonitor");
			 if (odbc_fetch_row ($curRep)) 
			 {
				$idExporterReport = odbc_result ($curRep,1);
				if ($idExporterReport == '') $idExporterReport = 0;
			 }
		  } 
		  else $idExporterReport++;

		  if (!$r) {
			 $ok = false;
			 $msg = "Problemas na inserção do informe no relatório";
			 echo "<br>".$query;
		  }
		  

		//atualiza a tabela change credit
		 if ($ok) 
			{
				$query = "
				  UPDATE ChangeCredit SET analysis = 0 WHERE id IN (
					SELECT cc.id
					FROM Inform inf
					  JOIN Importer imp ON imp.idInform = inf.id
					  JOIN ChangeCredit cc ON cc.idImporter = imp.id
					WHERE
					  inf.state = 10 AND
					  ($datasTrimes) AND
					  cc.id in (
						SELECT MAX (id)
						FROM ChangeCredit
						WHERE state = 2 OR state = 4 OR state = 5 OR state = 6 OR state = 7
						GROUP BY idImporter
					  )
				  )
				";
				$r = odbc_exec ($db, $query);

				$query = "
				  UPDATE ChangeCredit SET monitor = 0 WHERE id IN (
					SELECT cc.id
					FROM Inform inf
					  JOIN Importer imp ON imp.idInform = inf.id
					  JOIN ChangeCredit cc ON cc.idImporter = imp.id
					WHERE
					  inf.state = 10 AND
					  NOT cc.limTemp IS NULL AND
					  cc.limTemp < getDate() AND
					  (cc.credit IS NULL OR cc.credit = 0) AND
					  ($datasTrimes) AND
					  cc.id in (
						SELECT MAX (id)
						FROM ChangeCredit
						WHERE state = 2 OR state = 4 OR state = 5 OR state = 6 OR state = 7
						GROUP BY idImporter
					  )
				  )
				";
				$r = odbc_exec ($db, $query);

				if (!$r) {
				  $ok = false;
				  $msg = "Problemas na atualização da situação de cobrança de análise";
				}
			}
		//*********************************************************************************************************
		//*********************************************************************************************************

		
		//criando o ultimo trimestre da apaolice atual********************************************************************
		//referente ao startValidity
		$startIniAtual     = explode(";",$endValid[0]);
		$partes1StartValid = $startIniAtual[0];
		
		//referente ao endValidity
		$t1 = explode(" ",$endValid[0]);
		$partes1EndValid   = explode(";",$t1[0]);
		
		$partes2EndValid   = explode("-",$partes1EndValid[1]);		
		$dateFimApIni      = mkdate($partes2EndValid[0],$partes2EndValid[1] - 3,1);//dt ini do trimestre atual
		$dateFimApFim      = mkdate($partes2EndValid[0],$partes2EndValid[1] - 2,0);//dt fim do trimestre atual
		//echo $dateFimApFim."<br>"; 
		
		//criando o ultimo trimestre da apaolice anterior*****************************************************************
		$startIniAnterior   = explode(";",$endValid[1]);
		$partes1StartValid2 = $startIniAnterior[0];
		
		//referente a segundo endValidity da apolice anterior
		$t2 = explode(" ",$endValid[1]);
		$partes1EndValid2   = explode(";",$t2[0]);
		
		$partes2EndValid2   = explode("-",$partes1EndValid2[1]);
		$dateFimApIni2      = mkdate($partes2EndValid2[0],$partes2EndValid2[1] - 3,1);//dt ini do trimestre anterior
		$dateFimApFim2      = mkdate($partes2EndValid2[0],$partes2EndValid2[1] - 2,0);//dt fim do trimestre anterior
		
		
		/*
		  *esta variavel servirá para enviar os ultmos trimestres de cada apolice
		  */
		$ultimoTrimestreAplolices = $dateFimApFim.";".$dateFimApFim2;
		
		
		//estas variaveis serão usadas para somar as analises e monitoramentos
		$nAnalisen       = null;
		$nMonitoramento = null;
		$totNAnalise       = null;
		$totNMonitoramento = null;
		
		//echo $ultimoDiaDataCob."<br>";
		
		//importadores locais para serem recuperados depois
		$importadoresLocais=null;
			
			
		//conta todos os importadores
		$tot2 = count($arrImporterDados[$l]['IMPORTADORES']);
		for($w=0;$w<$tot2;$w++)
		{
			//echo $arrImporterDados[$l]['IMPORTADORES'][$w]."<br>";
			//aqui esta o array com todos os importadores
			$importerAtualAnterior = explode(";",$arrImporterDados[$l]['IMPORTADORES'][$w]);
			
			$diaIncluImp = explode(" ",$importerAtualAnterior[2]);
									
			//verifica se o importador foi inserido antes da apolice ficar vigente e verificar se é a 1ª cobrança 
			$mesVigencia = explode("/",$partes1StartValid);
			$dataStartValidity = $mesVigencia[2]."-".$mesVigencia[1]."-".$mesVigencia[0];
			//echo "<br>".$partes1StartValid."::".$dataStartValidity."<br>";
			
			//*****************************************************************************************************
			//monitoramento****************************************************************************************
			//regra para cobrança de moniramento
			if($importerAtualAnterior[7]=='ImpAnt' && $importerAtualAnterior[1]<>7)
			{
				$nMonitoramento +=1;
				
			}
			else if($importerAtualAnterior[1]<>7 && $importerAtualAnterior[3]<>0 || (($importerAtualAnterior[4]<>0 && $importerAtualAnterior[7]>$ultimoDiaDataCob[0]) 
			|| ($importerAtualAnterior[7] >= date("Y-m-d"))) && count($importerAtualAnterior)==13)
			{
				//se possuir credito atual será cobrado monitoramento
				$nMonitoramento +=1;
			}
			

			//*****************************************************************************************************				
			//analise***********************************************************************************************
			
			//consulta se tem analise cobrada na ultima cobrança feita
			$idImportador ="";
			$importerAtualAnterior[7] =="ImpAnt" ? $idImportador = $importerAtualAnterior[9] : $idImportador = $importerAtualAnterior[6];
			// Acesso à nova estrutura de análise e monitoramento
			$sqlAnaUltCob="select b.txAnalise AS analyse from resFatAnaliseMonitor as a join resFatAnaliseMonitorImport as b on a.id = b.IdResFat
							where a.idInform = ".$informes." and b.idImporter=".$idImportador." ";
			$exceSql = odbc_exec($db, $sqlAnaUltCob);
			$analiseAnterior = odbc_result ($exceSql,1);
			
			if(count($importerAtualAnterior)==16 && ($importerAtualAnterior[5] < $dateFimApFim2) && ($importerAtualAnterior[3]<>0 && $importerAtualAnterior[6]==0) && ($analiseAnterior==0 || $analiseAnterior==""))
			{
				$nAnalisen +=1;
			}
         // Alteração 31/07/2009 Regra para cobrar análise dos importadores incluídos antes do ultimo trimestre apolíce anterior
         elseif (($importerAtualAnterior[7]=='ImpAnt' && $importerAtualAnterior[1]== 6 && $importerAtualAnterior[3]>0) ){
             $nAnalisen +=1;
             $a = true;
         }
			
			$anoMes = explode("-",$dateFimApFim);
			$penultimaCobTrimestre = mkdate($anoMes[0],$anoMes[1]-2,0);
			$arrDatePenTrim = explode("-",$penultimaCobTrimestre);
			$antePenultimo = mkdate($arrDatePenTrim[0],$arrDatePenTrim[1]-2,0);
			
			if(count($importerAtualAnterior)==13 && ($diaIncluImp[0] > $dateFimApFim) && ($analiseAnterior==0 || $analiseAnterior=="") && ($importerAtualAnterior[3]<>0 || $importerAtualAnterior[4]<>0) && $importerAtualAnterior[1]!=7)
			{
				$nAnalisen +=1;
			}
			if(count($importerAtualAnterior)==13 && ($diaIncluImp[0] > $dateFimApFim) && ($analiseAnterior==0 || $analiseAnterior=="") && ($importerAtualAnterior[3]==0 && $importerAtualAnterior[4]==0) && $importerAtualAnterior[1]!=7)
			{
				$nAnalisen +=1;
			}			
			if(count($importerAtualAnterior)==13 && ($diaIncluImp[0] > $penultimaCobTrimestre && $diaIncluImp[0] < $dateFimApFim) && $importerAtualAnterior[5]=="" && ($analiseAnterior==0 || $analiseAnterior==""))
			{
				$nAnalisen +=1;
			}
			
			//esta parte é responsável por verificar a entrada do importador no segundo trimestre, no primeiro trimestre ou antes
			$dataAtualDoDia = date("Y-m-d");
			if(count($importerAtualAnterior)==13 && ($diaIncluImp[0] > $antePenultimo && $diaIncluImp[0] < $penultimaCobTrimestre ) && ($dataAtualDoDia > $antePenultimo && $dataAtualDoDia < $penultimaCobTrimestre))
			{
				$nAnalisen +=1;
			}
			if(count($importerAtualAnterior)==13 && ($diaIncluImp[0] < $antePenultimo && $dataAtualDoDia< $antePenultimo))
			{
				$nAnalisen +=1;
			}
			
			if($importerAtualAnterior[1]==7 && ( $importerAtualAnterior[5]=="" || $importerAtualAnterior[8]=="") && ($analiseAnterior==0 || $analiseAnterior==""))
			{
				$nAnalisen +=1;
			}
				
			
			
			
			///////////////////////////////////////////////////////////////////////////////////////////////////////
			//inclusão na tabela ImporterReport
			if($tot2>0)
			{
				$nAnalisen=="" ? $vlnAnalisen=0 : $vlnAnalisen=$nAnalisen;
				$nMonitoramento=="" ? $vlnMonitoramento=0 : $vlnMonitoramento=$nMonitoramento;
			
				$importerAtualAnterior[7]=="ImpAnt" ? $idImpInter = $importerAtualAnterior[9] : $idImpInter = $importerAtualAnterior[6];
				$importerAtualAnterior[7]=="ImpAnt" ? $limiteCredito=$importerAtualAnterior[15] : $limiteCredito=$importerAtualAnterior[12] ;
				
				$idImporter = $idImpInter;
      
				  // Encontrar o crédito solicitado e p crédito concedido correntes
			      $creditSolic = $limiteCredito;
			      if(! $creditSolic)
				  {
				     $creditSolic = 0;
			      }
				  
			      $credit = $importerAtualAnterior[3];
			      if(! $credit)
				  {
				     $credit = 0;
			      }
			      // Acesso à nova estrutura de análise e monitoramento
			      $query = "
			        INSERT INTO resFatAnaliseMonitorImport (IdResFat, idImporter, txMonitor, txAnalise, creditoSolicitado, creditoConcedido)
			        VALUES (".($idExporterReport).", $idImporter, $vlnMonitoramento,$vlnAnalisen, $creditSolic , $credit)";
			      //echo "<pre>Insert : $query</pre>";
			      $r = odbc_exec ($db, $query);
			      if (!$r) 
				  {
				     $ok = false;
				     $msg = "Problemas na inserção do importador no relatório";
					 //echo "<br>".$query;
			      }
			}
			
			
			
			$hc_atu_imp = 0;
			$query_imp = "";
			$query_cha = "";
			
			//atualiza changeCredit com a data da cobrança da analise
		    if ($nAnalisen)
			{
		      $parc += $txAnalise;
			  $gpc_txA   = $txAnalise;
			  $qt_analise += 1;
			  // Atualiza o importer
			  $hc_atu_imp = 1;
			  $query_imp = " dt_cob_analise = getDate(), nu_cob_analise = nu_cob_analise + 1 "; 	
			  
			  // Atualiza o ChangeCredit
			  $query_cha = " UPDATE ChangeCredit SET dt_cob_analise = getDate() " .	  
			               " WHERE idImporter = " . $idImportador. " " .
						   " AND id = (select max(id) from ChangeCredit where idImporter = " .$idImportador. "  ) ";
		      
			  $hc_r = odbc_exec ($db, $query_cha);
		      if (!$hc_r) 
			  {
			     $ok = false;
			     $msg = "Problemas atualizando changeCredit!";
			  }
				  
		    }
			
			
			//atualiza changeCredit com a data da cobrança do monitoramento
		    if ($nMonitoramento)
		    {
			  $parc += $txMonitoramento / 4;
			  $gpc_txM   = $txMonitoramento;
			  $qt_monitor += 1;
			  // Atualiza o importer  
			  if ($hc_atu_imp == 1)
			  {
			     $query_imp = $query_imp . ", ";
			  }
			  $hc_atu_imp = 1;	  
		  	  $query_imp = " dt_cob_monitor = getDate(), nu_cob_monitor = nu_cob_monitor + 1 "; 	
			
		    }
		    
			if ($hc_atu_imp == 1)
			{
		       $query_imp = " UPDATE Importer set " . $query_imp .
		                    " WHERE id = " .$idImportador. " ";       
		       $hc_r = odbc_exec ($db, $query_imp);
		       if (!$hc_r) 
			   {
			      $ok = false;
			      $msg = "Problemas atualizando importador!";
			   }
			}

	///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////	
			
				//fim tipo 2				
		}//fim for
			
		
		//calculos para o rodape e para somatorio de analise e monitoramente linha a linha
			
		$nAnT   += $nAnalisen;
		$nAmT   += $nMonitoramento;	
		$txAnaliseTot    = number_format($nAnalisen * $txAnalise,2,",",".");
		$txMonitoramentoTot = number_format($nMonitoramento * $txMonitoramento/ 4,2,",",".");
		$total     = number_format(($nAnalisen * $txAnalise) + ($nMonitoramento * $txMonitoramento/ 4),2,",",".");
		$analiseT += $nAnalisen * $txAnalise;
		$monitorT += $nMonitoramento * $txMonitoramento/ 4;
		$totalT   += ($nAnalisen * $txAnalise) + ($nMonitoramento * $txMonitoramento/ 4);
		
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////Envio para SisSeg//////////////////////////////////////////////
		//$parc = $qt_analise * $gpc_txA + $qt_monitor * $gpc_txM/ 4;	
		$parc = ($nAnalisen * $txAnalise) + ($nMonitoramento * $txMonitoramento/ 4);	
		$total += $parc;
		if (!addSisSeg ($i_Seg, $contrat, $nSeq, $parc, $dbSisSeg, $ano, $mes, $nProp))
		{
		  echo htmlspecialchars($i_Seg, ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($contrat, ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($nSeq, ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($parc, ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($dbSisSeg, ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($ano, ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($nProp, ENT_QUOTES, 'UTF-8');

		  $ok = false;
		  $msg = "Problema na inserção da parcela no SisSeg i_Seg[$i_Seg] c_Coface [$contrat]";
		  //$msg = "2" . odbc_errormsg();
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		  if ($ok == true) 
		  {
		    odbc_commit ($db);
		    odbc_commit ($dbSisSeg);
		    //odbc_rollback ($db);
		    //odbc_rollback ($dbSisSeg);	

		    $msg="Cobrança gerada com sucesso total [".number_format ($total,2,",",".")."]";
		  } 
		  else 
		  {
		    odbc_rollback ($db);
		    odbc_rollback ($dbSisSeg);
		  }

		  odbc_autocommit($db, false);
		  odbc_autocommit($dbSisSeg, false);
		
		
		
		($l % 2) ? $corSimCorNao="" : $corSimCorNao="bgcolor=#e9e9e9";
			
			
		$modal="window.open('interf/pgSBCE/moldurapg.php?importer=$informesGerais;$stateGerais;$ultimoTrimestreAplolices','Importadores','width=780,height=400,scrollbars=yes,left=200,top=150')";
		$link = "javascript:void(0);";
		
		//imprime linha a linha
		/* echo "<tr $corSimCorNao>
				<td class='texto'>".$linha++."</td>
				<td class='texto'><a href='$link' onclick=\"$modal\">$exportador</a></td>
				<td align='center' class='texto'>$nAnalisen</td>
				<td align='center' class='texto'>$txAnaliseTot</td>
				<td align='center' class='texto'>$nMonitoramento</td>
				<td align='center' class='texto'>$txMonitoramentoTot</td>
				<td align='center' class='texto'>$total<br></td>
				</tr>"; */
			
	} 
	
		/* echo "<tr bgcolor=#cccccc>
				<th class='textoBold' colspan=2>Totais</th>
				<td class='textoBold' align=center>$nAnT</td>
				<td class='textoBold' align=center>".number_format ($analiseT,2,',','.')."</td>
				<td class='textoBold' align=center>$nAmT</td>
				<td class='textoBold' align=center>".number_format ($monitorT,2,',','.')."</td>
				<td class='textoBold' align=center>".number_format ($totalT,2,',','.')."</td>
		   </tr>";
	echo "</table>"; */
	
	/*  echo"<pre>";
	print_r($arrImporterDados);
	echo"</pre>";  */  
	
?>
<?php  echo $msg."<br>";
  }
?>
