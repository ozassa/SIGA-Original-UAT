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
	require_once("../../../navegacao.php");
	// Incluir funções de segurança
	require_once("../../../../security_functions.php");
	?>

    <!-- CONTE�DO P�GINA - IN�CIO -->


<?php
	//number_format ($analiseT,2,',','.')
	//fun��o para criar a data
	function mkdate ($a, $m, $d) 
	{
	  return date ("Y-m-d", mktime (0, 0, 0, $m, $d, $a));
	}
	
	//esta fun��o cria as datas de cobran�a, sendo que faz um calculo para retornar as 4 parcelas anteriores e com isto chegar ao mes
	//inicial de vigencia da apolice, inform
	//include("funcCriaTrimesAnt.php");
	$anoCorrente = date('Y');
	$mesCorrente = date('m');
	
	// Sanitizar dados de entrada
	$ano_input = safe_request('ano', 'int', 0);
	$mes_input = safe_request('mes', 'int', 0);
	$inform_input = safe_request('inform', 'int', 0);
	$segurado_input = safe_request('segurado', 'string', '', 255);
	$visaoImp_input = safe_request('visaoImp', 'int', 0);
	
	if(($ano_input < $anoCorrente || $mes_input < $mesCorrente) && $ano_input != 0 && $mes_input != 0 && $ano_input < 2009 && $mes_input < 12)
	{
		echo "<script>location.href='".$root."role/credit/Credit.php?comm=resMonitor&ano=".safe_output($ano_input)."&mes=".safe_output($mes_input)."&ret=retornar'</script>";
		break;
	}
	
	if($ano_input > 0)
	{
		$ano          = $ano_input;
	}else
	{	
		$ano          = date("Y");   //PEGA O ANO ATUAL
	}
	if($mes_input > 0)
	{
		$mesIniCorr   = $mes_input + 1;
	}else
	{
		$mesIniCorr   = date("m") + 1;//PEGA O MES ATUAL E SOMA MAIS 1 PARA DAR O MES SEGUINTE
	}	
	//$datasTrimes = criaTrimesAnt($ano,$mesIniCorr);//pega a primeira data 	
	


	$dtIniUltimoTrimestre=mkdate($ano, $mesIniCorr - 4, 1);
	$dtFimUltimoTrimestre=mkdate($ano, $mesIniCorr - 3, 0);
	
	//variaveis que cria os 4 trimestres anteriores de forma que seja poss�vel pegar os expotadores no trimestre certo
	//pois as cobrn�as s�o realizadas de 3 em 3 meses
	 $dt4Ini = mkdate($ano, $mesIniCorr - 3, 1);
	 $dt4Fim = mkdate($ano, $mesIniCorr - 2, 0);
	
	 $dt3Ini = mkdate($ano, $mesIniCorr - 6, 1);
	 $dt3Fim = mkdate($ano, $mesIniCorr - 5, 0);
	
	 $dt2Ini = mkdate($ano, $mesIniCorr - 9, 1);
	 $dt2Fim = mkdate($ano, $mesIniCorr - 8, 0);
	
	 $dt1Ini = mkdate($ano, $mesIniCorr - 12, 1);
	 $dt1Fim = mkdate($ano, $mesIniCorr - 11, 0);
	 
	$ultimaCob        = mkdate($ano,$mesIniCorr-3,0)." 00:00:00.000";//deixar isto autom�tico
	$penultimaCob     = mkdate($ano,$mesIniCorr-6,0);
	$ultimoDiaDataCob = explode(" ",$ultimaCob);
	
	//echo " - ".$ultimoDiaDataCob[0]."<br>";
	
	//exit();
	
	function inativaComCobAnalise($db,$idImporter)
	{		
		$sql2="select top 1 analysis,limTemp from ChangeCredit where idImporter=$idImporter ORDER BY id desc";
		$rs2 = odbc_exec($db, $sql2);
		$analysis = odbc_result ($rs2,1);
		$limTemp    = odbc_result ($rs2,2);
		$dateLimTemp = explode(" ",$limTemp);
		
		return $analysis.";".$dateLimTemp[0];
	}
	
	if($visaoImp_input == 1)
	{
		echo "Cobran�a referente: ".safe_output($mes_input)."/".safe_output($ano_input)."<br>";
		echo "Segurado:".safe_output($segurado_input)."<br><br>";
		echo "<table>
			<thead>
			<tr>
			<th>Comprador</th>
			<th>Pa&iacute;s</th>
			<th>Cr&eacute;dito Solicit.US$ Mil</th>
			<th>Cr&eacute;dito Conced.US$ Mil</th>
			<th>An&aacute;lises</th>
			<th>Monitoramento</th>
			<th>Total</th>
			<th>Motivo</th>
			</tr>
			</thead>";
		
		$cosnulta = odbc_exec($db, "select imp.idImporter,imp.importador,imp.creditoSolicitado,imp.creditoConcedido,imp.txAnalise,imp.txMonitor,imp.total,imp.motivo,c.name 
from resFatAnaliseMonitorImport as imp join Importer as imp2 on imp.idImporter = imp2.id
join Country as c on imp2.idCountry = c.id  
where imp.idInform = ".$inform_input." and imp.ano=$ano and imp.mes=$mes order by imp.importador");
	
	$count = 1;
	while(odbc_fetch_row($cosnulta))
	{
		 if($count % 2 == 0){
				      $ver = 'style="background-color:#FFF"';
			       }else{
				      $ver = ''; 
			       }
		  ?>
		<tr <?php echo $ver;?>><?php
		$importador = odbc_result($cosnulta,2);
		$creditSolic = odbc_result($cosnulta,3);
		$credit = odbc_result($cosnulta,4);
		$analyse = odbc_result($cosnulta,5);
		$monitor = odbc_result($cosnulta,6);
		$total = odbc_result($cosnulta,7);
		$mot = odbc_result($cosnulta,8);
		$cidade = odbc_result($cosnulta,9);
		
		$totalCobrado +=$total; 
		$totA +=$analyse;
		$totM +=$monitor;
	    echo "<td>$importador</td>";	
	?>
	<td><?php echo ($cidade);?></td>
   <td><?php echo  number_format($creditSolic/1000, 0, ',', '.');?></td>
   <td><?php echo  $credit == '' ? '0' : number_format($credit/1000, 0, ",", ".");?>&nbsp;</td>
   <td><?php echo  $analyse != 0 ? number_format($analyse, 2, ',', '.') : '0,00';?></td>
   <td><?php echo  $monitor != 0 ? number_format($monitor/4, 2, ',', '.') : '0,00';?></td>
   <td><?php echo  number_format($total, 2, ',', '.');?></td>
   <td><?php echo  $mot;?></td>
	<?php  echo "</tr>";
	$count++;
	}
	
	?>
<tr>
<td colspan=4>Total</td>
<th><?php echo  $totA > 0 ? number_format($totA, 2, ',', '.') : '&nbsp;';?></th>
<th><?php echo  $totM > 0 ? number_format($totM/4, 2, ',', '.') : '&nbsp;';?></th>
<th><?php echo  number_format($totalCobrado, 2, ',', '.');?></th>
<th>&nbsp;</th>
</tr>
</table>
<br><br>
<div align=center>
<input type=button class="servicos" value="Voltar" onclick="javascript:window.history.back()"> &nbsp; 
<!-- <input type=button class="servicos" value="Imprimir" onclick="javascript:print()"> -->
</div>
<hr>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo  $root;?>role/credit/interf/Demonstrativo.php?idInform=<?php  echo safe_output($inform_input);?>&mes=<?php echo  safe_output($mes);?>&ano=<?php echo  safe_output($ano);?>&key=<?php echo  safe_output(session_id().time());?>" target="_blank">
Vers�o em pdf desta p�gina</a>
<?php  }
	else	
	{

	
	
	
	$queryExportador="";
	if(!$_REQUEST['mes'])
	{
		
		$queryExportador="select id,idInform,exportador,qtdAnalise,txAnalise,qtdMonitor,txMonitor,total,ano,mes 
from resFatAnaliseMonitor where mes=".date("m")." and ano=".date("Y")." order by exportador";
		
	}else
	{
		$queryExportador="select id,idInform,exportador,qtdAnalise,txAnalise,qtdMonitor,txMonitor,total,ano,mes 
from resFatAnaliseMonitor where mes=".$_REQUEST['mes']." and ano=".$_REQUEST['ano']." order by exportador";
	
	}
	
	$generated = odbc_exec($db,$queryExportador);
	if(odbc_num_rows($generated)>0)
	{
	?>
	<form action="<?php echo  $root;?>role/credit/Credit.php" name="frm" id="frm">
    <div class="conteudopagina">
      <li class="campo2colunas">
        <label>M&ecirc;s</label>
        <select name=mes class="caixa">
		<?php  $mes = $_REQUEST['mes'] ? $_REQUEST['mes'] : date("m");
        $anoAtu = $_REQUEST['ano'] && $_REQUEST['ano']==date("Y") ? $_REQUEST['ano'] :date("Y");
        for ($i = 1; $i <= 12; $i++) 
        {
        ?>
          <option<?php echo  $i == $mes ? ' SELECTED' : '';?>><?php echo  $i;?></option>
        <?php  }
        ?>
        </select>
      </li>
      
      <li class="campo2colunas"><label>Ano</label>
        <select name=ano class="caixa">
	  <option<?php echo  $ano == $anoAtu ? ' SELECTED' : '' ?>><?php echo  $anoAtu;?></option>
      <option<?php echo  $ano == ($anoAtu - 1) ? ' SELECTED' : '' ?>><?php echo  $anoAtu - 1;?></option>
    </select>
      </li>
      <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
      <a href="#" onclick="javascript: frm.submit();"><img src="<?php echo $host;?>images/botao_pesquisar.png" alt="" /></a></li>
    </div>
    <input type="hidden" name="comm" value="fechamentoMensal">
    <?php echo csrf_token_field(); ?>
    
    </form>


	<?php  echo "
	<div class='conteudopagina'>
	<table summary='Submitted table designs' id=\"example\">
			<thead>
			<tr>
			<th>N&deg;</th>
			<th>Segurado</th>
			<th>Qtde. An&aacute;lises</th>
			<th>An&aacute;lises</th>
			<th>Qtde. Monitor.</th>
			<th>Monitoramento</th>
			<th>Total</th>
			</tr>
			</thead>
			<tbody>";
		 
		$linha=1;
		while (odbc_fetch_row ($generated)) 
		{
			$id            = odbc_result($generated,1);//id data tabela ExporterReport
			$idInform      = odbc_result($generated,2);//id do exportador da tabela Inform
			$exportador    =  odbc_result($generated,3);
			
			$nAnalisen       = odbc_result($generated,4);;
			$nMonitoramento = odbc_result($generated,6);;
			$totNAnalise       = null;
			$totNMonitoramento = null;
			$txMonitoramento =odbc_result($generated,7);
			$txAnalise =odbc_result($generated,5);
			$txTotal =odbc_result($generated,8);
			
			//flag para verificar apenas a sanremos nesta faze de teste
			//if($idInform==5829)
			//{
				//dados do exportador atual e 				
				
				$nAnT   += $nAnalisen;
				$nAmT   += $nMonitoramento;	
				$txAnaliseTot    = number_format($txAnalise,2,",",".");
				$txMonitoramentoTot = number_format($txMonitoramento,2,",",".");
				$total     = number_format($txTotal,2,",",".");
				$analiseT += $txAnalise;
				$monitorT += $txMonitoramento;
				$totalT   += ($txAnalise) + ($txMonitoramento);
				
				if($linha % 2 == 0){
				      $ver = 'style="background-color:#FFF"';
			       }else{
				      $ver = ''; 
			       }
		  				
				$anoCorr="";$mesCorr="";
				if(!$_REQUEST['ano'] && !$_REQUEST['mes'])
				{
					$anoCorr=date("Y");$mesCorr=date("m");
				}else
				{
					$anoCorr=$_REQUEST['ano'];$mesCorr=$_REQUEST['mes'];
				}
					
				$modal="window.location.href='Credit.php?comm=".$_REQUEST['comm']."&inform=$idInform&segurado=$exportador&ano=".$anoCorr."&mes=".$mesCorr."&visaoImp=1'";
				$link = "javascript:void(0);";
				
				//imprime linha a linha
				echo "<tr $ver>
						<td>".$linha."</td>
						<td><a href=\"javascript:$modal\" >".($exportador)."</a></td>
						<td>$nAnalisen</td>
						<td>$txAnaliseTot</td>
						<td>$nMonitoramento</td>
						<td>$txMonitoramentoTot</td>
						<td>$total<br></td>
					  </tr>"; 
			//}
			//if($linha==1){break;}
			$linha++;
		}
		
		//aqui entra o rodap� 
		
		echo "<tr>
				<th colspan=2>Totais</th>
				<td>$nAnT</td>
				<td>".number_format ($analiseT,2,',','.')."</td>
				<td>$nAmT</td>
				<td>".number_format ($monitorT,2,',','.')."</td>
				<td>".number_format ($totalT,2,',','.')."</td>
		       </tr>
			  </div>";
		echo "</tbody></table><div class=\"divisoria01\"></div>"; 
		
	}
	else{
	
	$datasTrimes="(startValidity >= '$dt1Ini' AND startValidity <= '$dt1Fim')
			   OR (startValidity >= '$dt2Ini' AND startValidity <= '$dt2Fim')
			   OR (startValidity >= '$dt3Ini' AND startValidity <= '$dt3Fim')
			   OR (startValidity >= '$dt4Ini' AND startValidity <= '$dt4Fim')
				";
	
	//echo $dtIniUltimoTrimestre."-".$dtFimUltimoTrimestre;
	$dataCobran�aUltimoTrimestre = "'$dtIniUltimoTrimestre' and '$dtFimUltimoTrimestre'";	
	//esta query traz todos os exportadores vigentes e os encerrados no ultimo trimestre
	$query = "select name,id,idAnt
				from Inform where state = 10 and (txAnalize<>0 or txMonitor<>0)
				AND ($datasTrimes) 
				order by name";

	//echo "<pre>$query</pre>";
	//exit();
	//break;
	$cur = odbc_exec($db, $query);
?>
<!--
<strong style="color:#4682B4">Em Desenvolvimento!!!!!!!!</strong><br><br><br><br>
-->
<form action="<?php echo  $root;?>role/credit/Credit.php" name="frm2" id="frm2">
    <div class="conteudopagina">
      <li class="campo2colunas">
        <label>M&ecirc;s</label>
        <select name=mes class="caixa">
		<?php  $mes = $_REQUEST['mes'] ? $_REQUEST['mes'] : date("m");
        $anoAtu = $_REQUEST['ano'] && $_REQUEST['ano']==date("Y") ? $_REQUEST['ano'] :date("Y");
        for ($i = 1; $i <= 12; $i++) 
        {
        ?>
          <option<?php echo  $i == $mes ? ' SELECTED' : '';?>><?php echo  $i;?></option>
        <?php  }
        ?>
        </select>
      </li>
      
      <li class="campo2colunas"><label>Ano</label>
        <select name=ano class="caixa">
	  <option<?php echo  $ano == $anoAtu ? ' SELECTED' : '' ?>><?php echo  $anoAtu;?></option>
      <option<?php echo  $ano == ($anoAtu - 1) ? ' SELECTED' : '' ?>><?php echo  $anoAtu - 1;?></option>
    </select>
      </li>
      <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
      <button name="enviar" type="submit" class="botaoagm" onclick="javascript: frm2.submit();">Pesquisar</button>
      </li>
    
    <input type="hidden" name="comm" value="fechamentoMensal">
    <?php echo csrf_token_field(); ?>
    
    </form>
<?php
	//ESTA FUN��O IR� AJUDAR A PEGAR A TAXA E OS INFORMES DESTE EXPORTADOR
	include_once("funcTaxaInform.php");
	$arrTodosImportadores = array();
	
	/* ESTA PARTE ABAIXO IR� LISTAR TODOS OS EXPORTADORES E ARMAZENAR� EM UM ARRAY, APENAS OS NOMES, PARA DEPOIS TRABALHAR
		COM ESTES NOMES DEPOIS.
	*/
	$arrFech 	 = array();
	$id 		 = 1;
	$cont 		 = 1;
	$qtdRegistro = odbc_num_rows($cur);
	
	while (odbc_fetch_row ($cur)) 
	{
		//aqui pega todos os campos da query que s�o necess�rios
		$nome  = odbc_result ($cur, 'name');
		$id    = odbc_result ($cur, 'id');
		$idAnt = odbc_result ($cur, 'idAnt');
		
		//if("GRAMAZON GRANITOS DA AMAZONIA SA                           "==$nome)
		//{
			//chama a fun��o que ir� pegar todos os dados do exportador
			
			//if($id==6113)
			//{
			
			$dadosDoExportador = taxaInform($nome,$id,$idAnt,$db);
			
			//armazena todos arrays passados em um unico array
			array_push($arrTodosImportadores,$dadosDoExportador);		

$cont++;
	}
	
	/* echo "<pre>";
	print_r($arrTodosImportadores);
	echo "</pre>"; */  
	
	
	//LISTA TODOS OS NOMES DE EXPORTADORES PRESENTES NO ARRAY
	/*foreach($arrFech as $chave=>$valor)
	{
		//chama a fun��o que ir� pegar todos os dados do exportador
		$dadosDoExportador = taxaInform($valor,$db);
		
		//armazena todos arrays passados em um unico array
		array_push($arrTodosImportadores,$dadosDoExportador);		
		
		//echo $chave."::<strong style='color:#4682B4'>".$valor."</strong><br>";			
		//a estrutura abaixo serve unicamente para avaliarmos o primeiro exportador, no caso � alcan(Isto � uma FLAG PARA TESTE)
		if($chave == 5)
		{
			break;
		}else
		{
			continue;
		}  
	}*/
	
	//este array � criado para poder pegar o complemento do primeiro array que � o seguinte, depois de pegar os dados do exportador junta 
	//com os importadores e seus daos
	$arrImporterDados = array();
	
	//este foreach ira pegar todos os importadores e realizar uma serie  de verifica��es para poder pular os importer do exportador especifico
	include_once("arrayDadosImporterDeExportadores.php");//foreach esta dentro de um arquivo porque o c�digo ficou grande
	
	/*  echo "<pre>";
	print_r($arrImporterDados);
	echo "</pre>"; */  
	
	//monta os t�tulos da tabela
	echo "
	
	<table summary='Submitted table designs' id=\"example\">
			<caption>Total de Exportadores:".$qtdRegistro."</caption>
				<thead>
					<tr>
						<th>N&deg;</th>
						<th>Segurado</th>
						<th>Qtde. An&aacute;lises</th>
						<th>An&aacute;lises</th>
						<th>Qtde. Monitor.</th>
						<th>Monitoramento</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>";
	$linha=1;
	//apartir deste array que foi montado, temos informa��es relativas as taxas de analise e monitoramento do exportador, e seus importadores, bem como
	//se os mesmos estavam presentes em outras apolices, informes anteriores, apartir de agora segue a verifica��o de cada importador para saber se dele
	//ser ou n�o cobrado as taxas para cada um e sendo assim somar todas as analises e monitoramentos por exportador, de forma que teremos um resumo
	//final por exportador
	// o array a ser utilizado para verifica��o � este $arrImporterDados
	//percorre todos os exportadores e seus importadores
		
	//$ultimaCob = mkdate(date("Y"),(date("m")+1)-3,0)." 00:00:00.000";//deixar isto autom�tico
	//$penultimaCob = mkdate(date("Y"),(date("m")+1)-6,0);
	//echo $ultimoDiaDataCob[0];
	$a = false;
	$m = false;
	
	$motivoMonitor = "";
	$motivoAna = "";
	
	$tot = count($arrImporterDados);
	for($l=0;$l<$tot;$l++)
	{
		//esta �rea serve para poder quebrar as string que est�o dentro de alguns indices de array, sendo possivel recuperar seus valores individuais
		$taxas              = explode(",",$arrImporterDados[$l]['TAXASAM']);
		$inf                = explode(",",$arrImporterDados[$l]['INFORMS']);
		$state              = explode(",",$arrImporterDados[$l]['STATE']);
		$endValid="";
		
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
		/* echo "<pre>";
		print_r($endValid);
		echo "</pre>"; */
				
		//armazena nas variaveis os valores encontrados dentro de cada indice, para ser uilizado depois
		$exportador         = $arrImporterDados[$l]['EXPORTADOR'];
		$txAnalise          = $taxas[0];//analise
		$txMonitoramento    = $taxas[1];//monitoramento
		$informes           = $inf[0];
		$informesGerais     = $inf[0].";".$inf[1];
		$stateGerais        = $state[0].";".$state[1];
		
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
		  *esta variavel servir� para enviar os ultmos trimestres de cada apolice
		  */
		$ultimoTrimestreAplolices = $dateFimApFim.";".$dateFimApFim2;
		
		//estas variaveis ser�o usadas para somar as analises e monitoramentos
		$nAnalisen       = 0;
		$nMonitoramento = 0;
		$totNAnalise       = 0;
		$totNMonitoramento = 0;
		
		//echo $ultimoDiaDataCob."<br>";
		
		//importadores locais para serem recuperados depois
		$importadoresLocais=null;
				
		//esta �rea � reservada a consulta de dados como apolice vigente e encerrada
		//consulta do status Vigente
		/*$consultaDadosAp1="select id,CONVERT(VARCHAR,startValidity,103)as inicio,CONVERT(VARCHAR,endValidity,103) as fim,n_Apolice,state,name 
		from Inform where id=".$inf[0]." and state=".$state[0]."";
		$execConsultaApoliceVigente = odbc_exec($db, $consultaDadosAp1);	
		$execConsultaApoliceVigente == true ? $startValidityApolice = odbc_result ($execConsultaApoliceVigente,2) :  $startValidityApolice="";
		*/
		
		//conta todos os importadores	
		$tot2 = count($arrImporterDados[$l]['IMPORTADORES']);
		for($w=0;$w<$tot2;$w++)
		{
			
			//echo $arrImporterDados[$l]['IMPORTADORES'][$w]."<br>";
			//aqui esta o array com todos os importadores
			$importerAtualAnterior = explode(";",$arrImporterDados[$l]['IMPORTADORES'][$w]);
			
			$diaIncluImp = explode(" ",$importerAtualAnterior[2]);
									
			//verifica se o importador foi inserido antes da apolice ficar vigente e verificar se � a 1� cobran�a 
			$mesVigencia = explode("/",$partes1StartValid);
			$dataStartValidity = $mesVigencia[2]."-".$mesVigencia[1]."-".$mesVigencia[0];
			//echo "<br>".$partes1StartValid."::".$dataStartValidity."<br>";
			
			//*****************************************************************************************************
			//monitoramento****************************************************************************************
			//regra para cobran�a de moniramento
			
			$rsTxMonitor = 0;
			
			if($importerAtualAnterior[7]=='ImpAnt' && $importerAtualAnterior[1]<>7)
			{
				
				$nMonitoramento +=1;
				$m = true;
			}
			else if($importerAtualAnterior[1]<>7 && $importerAtualAnterior[3]<>0 || (($importerAtualAnterior[4]<>0 && $importerAtualAnterior[7]>$ultimoDiaDataCob[0]) 
			|| ($importerAtualAnterior[7] >= date("Y-m-d"))) && count($importerAtualAnterior)==13)
			{
				//se possuir credito atual ser� cobrado monitoramento
				 $nMonitoramento +=1;
				 $m = true;
				
			}
			
			
			//*****************************************************************************************************				
			//analise***********************************************************************************************
			
			//consulta se tem analise cobrada na ultima cobran�a feita
			$idImportador ="";
			$importerAtualAnterior[7] =="ImpAnt" ? $idImportador = $importerAtualAnterior[9] : $idImportador = $importerAtualAnterior[6];
			
			// Acesso � nova estrutura de an�lise e monitoramento
			$sqlAnaUltCob="select b.txAnalise AS analyse from resFatAnaliseMonitor as a join resFatAnaliseMonitorImport as b on a.id = b.IdResFat
							where a.idInform = ".$informes." and b.idImporter=".$idImportador." ";
			$exceSql = odbc_exec($db, $sqlAnaUltCob);
			$analiseAnterior = odbc_result ($exceSql,1);
			
			$anoMes = explode("-",$dateFimApFim);
			$penultimaCobTrimestre = mkdate($anoMes[0],$anoMes[1]-2,0);
			$arrDatePenTrim = explode("-",$penultimaCobTrimestre);
			$antePenultimo = mkdate($arrDatePenTrim[0],$arrDatePenTrim[1]-2,0);
			
			$rsTxAnalise = 0;
			
			if(count($importerAtualAnterior)==13 && $importerAtualAnterior[1]==6 && ($diaIncluImp[0] > $ultimoDiaDataCob[0]) )
			{			
				$nAnalisen +=1;
				$a = true;
			}
			elseif (($importerAtualAnterior[7]=='ImpAnt' && $importerAtualAnterior[1]== 6 && $importerAtualAnterior[3]>0) )
			{
               $nAnalisen +=1;
               $a = true;
         }
							
			//colocar aqui tempor�riamente a inclus�o da tabela de  resFatAnaliseMonitorImport
			$codigoImportador=0;
			$importerAtualAnterior[7] =="ImpAnt" ? $codigoImportador = $importerAtualAnterior[9] : $codigoImportador = $importerAtualAnterior[6] ;
			
			$limiteSolicitado=0;
			$importerAtualAnterior[7] =="ImpAnt" ? $limiteSolicitado = $importerAtualAnterior[15] : $limiteSolicitado = $importerAtualAnterior[12] ;
			
			if($limiteSolicitado==0){$limiteSolicitado=0;}
			if($importerAtualAnterior[3]==0){$importerAtualAnterior[3]=0;}
			if($rsTxAnalise==0){$rsTxAnalise=0;}
			if($rsTxMonitor==0){$rsTxMonitor=0;}
			if($rsTotalAnaliseMonitoramento==0){$rsTotalAnaliseMonitoramento=0;}
			
			if($m==true)
			{
				if($a == true)
				{
					//echo $nMonitoramento." - Monitoramento - ".$nAnalisen."-An�lise<br>";
					
					$rsTxMonitor = $txMonitoramento;
					$motivoMonitor = "Monitoramento";
					
					$rsTxAnalise  = $txAnalise;
					$motivoAna = "<br>An�lise";
					
					$rsTotalAnaliseMonitoramento = ($rsTxAnalise + ($rsTxMonitor/4));
					$sqlImp = "insert into resFatAnaliseMonitorImport values(".$inf[0].",".$codigoImportador.",'".str_replace("'","''",$importerAtualAnterior[0])."',$limiteSolicitado,".$importerAtualAnterior[3].",$rsTxAnalise,$rsTxMonitor,$rsTotalAnaliseMonitoramento,'".$motivoMonitor.$motivoAna."',".$_REQUEST['ano'].",".$_REQUEST['mes'].")";
					//$rsSqlImp = odbc_exec($db, $sqlImp);
				}
				else
				{
					//echo $nMonitoramento." - Monitoramento <br>";
					$rsTxMonitor = $txMonitoramento;
					$motivoMonitor = "Monitoramento";
					
					$rsTotalAnaliseMonitoramento = ($rsTxMonitor/4);
					$sqlImp = "insert into resFatAnaliseMonitorImport values(".$inf[0].",".$codigoImportador.",'".str_replace("'","''",$importerAtualAnterior[0])."',$limiteSolicitado,".$importerAtualAnterior[3].",$rsTxAnalise,$rsTxMonitor,$rsTotalAnaliseMonitoramento,'".$motivoMonitor."',".$_REQUEST['ano'].",".$_REQUEST['mes'].")";
					//$rsSqlImp = odbc_exec($db, $sqlImp);
				}
			}
			else if($a==true)
			{
				//echo $nAnalisen."-An�lise<br>";
				$rsTxAnalise  = $txAnalise;
				$motivoAna = "An�lise";
				
				$rsTotalAnaliseMonitoramento = ($rsTxAnalise);
				$sqlImp = "insert into resFatAnaliseMonitorImport values(".$inf[0].",".$codigoImportador.",'".str_replace("'","''",$importerAtualAnterior[0])."',$limiteSolicitado,".$importerAtualAnterior[3].",$rsTxAnalise,$rsTxMonitor,$rsTotalAnaliseMonitoramento,'".$motivoAna."',".$_REQUEST['ano'].",".$_REQUEST['mes'].")";
				//$rsSqlImp = odbc_exec($db, $sqlImp); 	
			}
			
			$m = false;
			$a = false;
			$motivoMonitor = "";
			$motivoAna = "";
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
		
		if($nAnalisen == 0){$nAnalisen=0;}
		if($analiseT ==0){$analiseT=0;}
		if($nMonitoramento ==0){$nMonitoramento=0;}
		if($monitorT ==0){$monitorT=0;}
		if($totalT ==0){$totalT=0;}

		$sqlExport = "insert into resFatAnaliseMonitor values(".$inf[0].",'".str_replace("'","''",$exportador)."',$nAnalisen,$analiseT,$nMonitoramento,$monitorT,$totalT,".$_REQUEST['ano'].",".$_REQUEST['mes'].")";
		//$rsSqlExp = odbc_exec($db, $sqlExport);
		
		/* if(odbc_num_rows($rsSqlExp)>1)
		{
			echo "Exportador incluido<br>";
		} */
		
		
			
			
		$modal="window.open('interf/pgSBCE/moldurapg.php?importer=$informesGerais;$stateGerais;$ultimoTrimestreAplolices&ano=".($_REQUEST['ano']?$_REQUEST['ano']:$ano)."&mes=".($_REQUEST['mes']?$_REQUEST['mes']:$mes)."','Importadores','width=780,height=400,scrollbars=yes,left=200,top=150')";
		
      $link = "javascript:void(0);";
		
		//imprime linha a linha
		if($l % 2 == 0){
				      $ver = 'style="background-color:#FFF"';
			       }else{
				      $ver = ''; 
			       }
		  ?>
		      <tr <?php echo $ver;?>>		
		        <td><?php echo $linha++;?></td>
				<td><a href="<?php echo $link?>" onclick="<?php echo $modal;?>"><?php echo ($exportador);?></a></td>
				<td style="text-align:right"><?php echo $nAnalisen;?></td>
				<td style="text-align:right"><?php echo $txAnaliseTot;?></td>
				<td style="text-align:right"><?php echo $nMonitoramento;?></td>
				<td style="text-align:right"><?php echo $txMonitoramentoTot;?></td>
				<td style="text-align:right"><?php echo $total;?><br></td>
			  </tr>
	<?php				
	} 
	/*
		echo "
			<tr>
				<td colspan=2>Totais</td>
				<td>$nAnT</td>
				<td>".number_format ($analiseT,2,',','.')."</td>
				<td>$nAmT</td>
				<td>".number_format ($monitorT,2,',','.')."</td>
				<td>".number_format ($totalT,2,',','.')."</td>
		   </tr>
		   ";
		*/
		   
	echo '
	</tbody>
	
	<tfoot>
			<tr>
				<td colspan=2>Total Geral</td>
				<td style="text-align:right">'.$nAnT.'</td>
				<td style="text-align:right">'.number_format ($analiseT,2,',','.').'</td>
				<td style="text-align:right">'.$nAmT.'</td>
				<td style="text-align:right">'.number_format ($monitorT,2,',','.').'</td>
				<td style="text-align:right">'.number_format ($totalT,2,',','.')."</td>
		   </tr>
	</tfoot>
	</table>
	";
	
	$nAnalisen = 0;
		$analiseT = 0;
		$nMonitoramento = 0;
		$monitorT= 0;
		$totalT= 0;
		
	/*  echo"<pre>";
	print_r($arrImporterDados);
	echo"</pre>";  */  
	
?>
<?php  
// Acesso � nova estrutura de an�lise e monitoramento
$cur = odbc_exec($db, "SELECT id FROM resFatAnaliseMonitor WHERE mes=$mes AND ano=$ano");
$generated = odbc_fetch_row($cur);

	/*  if (date("d") >= 25 && !$generated && $mes==date("m")) 
	{  */
?>
    <form action="<?php echo  $root;?>role/credit/Credit.php" onSubmit="return confirm('Confirmar cobran&ccedil;a?')">
        <input type=hidden name=comm value="setCobrNovo">
        <input type=hidden name=mes value="<?php echo  $mes;?>">
        <input type=hidden name=ano value="<?php echo  $ano;?>">
        <?php echo csrf_token_field(); ?>
        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
            
                <button name="enviar" type="submit" class="botaoagg" onclick="document.getElementById('enviarCobr').style.display='none'">Enviar Cobran&ccedil;a</button>
            
        </li>
    </form>
<?php  //}
  }
  }
?>
</div>
