<?php  // Alterado Hicom (Gustavo) - 03/01/05 - incluir vari�vel para validade da proposta
// Alterado Hicom (Gustavo) - 03/01/05 - alterar texto informativo de renova��o se fim da validade
// do informe anterior for menor q 3 meses e status = 11 (encerrado)

function formata($numero)
{
if(strpos($numero,'.')!='')
{
$var=explode('.',$numero);
if(strlen($var[0])==4)
{
$parte1=substr($var[0],0,1);
$parte2=substr($var[0],1,3);
if(strlen($var[1])<2)
{
$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
}else
{
$formatado=$parte1.'.'.$parte2.','.$var[1];
}
}
elseif(strlen($var[0])==5)
{
$parte1=substr($var[0],0,2);
$parte2=substr($var[0],2,3);
if(strlen($var[1])<2)
{
$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
}
else
{
$formatado=$parte1.'.'.$parte2.','.$var[1];
}
}
elseif(strlen($var[0])==6)
{
$parte1=substr($var[0],0,3);
$parte2=substr($var[0],3,3);
if(strlen($var[1])<2)
{
$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
}
else
{
$formatado=$parte1.'.'.$parte2.','.$var[1];
}
}
elseif(strlen($var[0])==7)
{
$parte1=substr($var[0],0,1);
$parte2=substr($var[0],1,3);
$parte3=substr($var[0],4,3);
if(strlen($var[1])<2)
{
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
}
else
{
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
}
}
elseif(strlen($var[0])==8)
{
$parte1=substr($var[0],0,2);
$parte2=substr($var[0],2,3);
$parte3=substr($var[0],5,3);
if(strlen($var[1])<2){
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
}else{
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
}
}
elseif(strlen($var[0])==9)
{
$parte1=substr($var[0],0,3);
$parte2=substr($var[0],3,3);
$parte3=substr($var[0],6,3);
if(strlen($var[1])<2)
{
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
}
else
{
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
}
}
elseif(strlen($var[0])==10)
{
$parte1=substr($var[0],0,1);
$parte2=substr($var[0],1,3);
$parte3=substr($var[0],4,3);
$parte4=substr($var[0],7,3);
if(strlen($var[1])<2)
{
$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1].'0';
}
else
{
$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1];
}
}
else
{
if(strlen($var[1])<2)
{
$formatado=$var[0].','.$var[1].'0';
}
else
{
$formatado=$var[0].','.$var[1];
}
}
}
else
{
$var=$numero;
if(strlen($var)==4)
{
$parte1=substr($var,0,1);
$parte2=substr($var,1,3);
$formatado=$parte1.'.'.$parte2.','.'00';
}
elseif(strlen($var)==5)
{
$parte1=substr($var,0,2);
$parte2=substr($var,2,3);
$formatado=$parte1.'.'.$parte2.','.'00';
}
elseif(strlen($var)==6)
{
$parte1=substr($var,0,3);
$parte2=substr($var,3,3);
$formatado=$parte1.'.'.$parte2.','.'00';
}
elseif(strlen($var)==7)
{
$parte1=substr($var,0,1);
$parte2=substr($var,1,3);
$parte3=substr($var,4,3);
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
}
elseif(strlen($var)==8)
{
$parte1=substr($var,0,2);
$parte2=substr($var,2,3);
$parte3=substr($var,5,3);
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
}
elseif(strlen($var)==9)
{
$parte1=substr($var,0,3);
$parte2=substr($var,3,3);
$parte3=substr($var,6,3);
$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
}
elseif(strlen($var)==10)
{
$parte1=substr($var,0,1);
$parte2=substr($var,1,3);
$parte3=substr($var,4,3);
$parte4=substr($var,7,3);
$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.'00';
}
else
{
$formatado=$var.','.'00';
}
}
return $formatado;
}

function mkdate ($a, $m, $d) {
  return date ("Y-m-d", mktime (0, 0, 0, $m, $d, $a));
}

// Juros mora

//Condi��o Especial de Cobertura de Coligadas

    	$rsquery = odbc_exec($db, "select a.idInform,a.razaoSocial,a.endereco,a.zipCode, a.pais,b.name, a.taxID
		from ParModEsp_Coligada a inner join Country b on b.id = a.pais
		where a.idInform = $idInform ORDER BY a.razaoSocial ");
    
	$condespcol = "";

    	while (odbc_fetch_row($rsquery)){
		$temempcol	= 1;

       		$condespcol   	.= "\n";
       		$condespcol   	.= " |".  odbc_result ($rsquery, 'razaoSocial')."|\n";
       		$condespcol   	.= " Endere�o: ".odbc_result ($rsquery, 'endereco')."\n";
       		$condespcol   	.= " Pais: ". odbc_result ($rsquery, 'name')."\n Zip Code: ".odbc_result ($rsquery, 'zipCode') ."\n";
       		$condespcol   	.= " Tax ID: ". odbc_result ($rsquery, 'taxID')." \n";
    	}
 //print $condespcol;
 //break;

$jurosMora = odbc_result(odbc_exec($db, "select warantyInterest from Inform where id=$idInform"), 1);

$Litigio = odbc_result(odbc_exec($db, "select Litigio from Inform where id=$idInform"), 1);

$inicio_vigencia = odbc_result(odbc_exec($db, "select startValidity from Inform where id=$idInform"), 1);

$periodoMaxCredito = odbc_result(odbc_exec($db, "select periodMaxCred from Inform where id=$idInform"), 1);

$Periodo_Vigencia   = odbc_result(odbc_exec($db, "select Periodo_Vigencia from Inform where id=$idInform"), 1);

$msg1 = "
                                                     PROPOSTA DE SEGURO DE CR�DITO � EXPORTA��O";
$msg2 = "Seguro novo";


        $csql =
        "SELECT  convert(char,startValidity,103) as startValidity,
         convert(char,endValidity,103) as endValidity FROM Volume JOIN
         Inform ON (idInform = Inform.id) WHERE  idInform =". $idInform;
        $cur = odbc_exec($db, $csql);
        $startValidity = trim(odbc_result($cur, "startValidity"));
        $endValidity   = trim(odbc_result($cur, "endValidity"));
  



if($idAnt){
	
	  // finalmente essa coisa vai dar certo!
	  // Caso1) endValidity = hj (renova)
	  // Caso2) endValidity = hj - 30 (renova)
	  // Caso3) endValidity = hj-31(nova)
	  // Caso4)  endValidity = hj + 15 (renova)
	  // Caso5) endValidity = hj + 16 (n�o envia)
	
	// Alterado Hicom (Gustavo) - a data de encerramento deve ser consultada no sisseg
	/*
	antes:
	  $x = odbc_exec($db,
			 "select * from Inform where id=$idAnt and
					  endValidity >= getdate() - 30 and endValidity <= getdate() + 15");
	*/
	
	// primeiro testa se � renova��o de informe vigente ou encerrado
	  $x = odbc_exec($db,
			 "select 	*
			  from 		Inform
			  where 	id=$idAnt
						and (state = 9 or state = 10 or state = 11)"); // vigente ou encerrado
	//echo "select 	* 	  from 		Inform 	  where 	id=$idInform and (state = 10 or state = 11)<BR>";
	
	  if(odbc_fetch_row($x)){ // renovacao	
			//$y = odbc_exec($db, "select i_Seg, nProp, prodUnit from Inform where id=$idAnt");
			$iseg = odbc_result($x, "i_Seg");
			$n_Prop = odbc_result($x, "nProp");
			$prod = odbc_result($x, "prodUnit");
			if(isset($dbSisSeg)){
				$n_apolice = odbc_result(odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iseg"),1);
				$n_apolice = sprintf("062%06d", $n_apolice). ($prod != 62 ? "/$prod" : '');
		
		
		
				$x = odbc_exec($dbSisSeg,
					 "select 	*
					  from 		Base_Calculo
					  where 	n_Prop = $n_Prop
								and i_Seg = $iseg
								and n_Endosso = 0
								and d_Fim_Vig >= '".
									mkdate(date("Y"),date("m") - 3,date("d")) ."'
								and d_Fim_Vig <= getdate() + 15");
			
				if(odbc_fetch_row($x)){
				  $msg2 = "Renova Ap�lice n�: $n_apolice";
				}
			}
	  }

}


//   $msg3 = "
// Vig�ncia: 1 ano a partir da data de aceita��o da presente PROPOSTA DE SEGURO, sendo automaticamente renovada por igual per�odo, caso n�o haja  manifesta��o expressa em contr�rio, por parte do segurado ou da seguradora, no prazo m�ximo de 30 (trinta) dias antecedentes ao final da vig�ncia.";

$msg5 = "DADOS DO PROPONENTE:";

if(! function_exists('arruma_cnpj')){
  function arruma_cnpj($c){
    if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
      return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
    }
    return $c;
  }
}


// Aqui ser� verificado o nome da corretora e c�digo SUSEP para ser inserido nos documentos sugeridos
            //23-07-2009 e a invers�o dos dados da natureza da opera��o
            //Criado por Iteraktiv - (Elias Vaz)
            /*
            $cons = "Select a.idconsultor, a.razao, a.c_SUSEP
                     from consultor a inner join Inform b on (b.idConsultor = a.idconsultor)
                     where b.id = $idInform";
            $resultado = odbc_exec($db, $cons);
			*/
			$cons = " select a.idCorretor,a.p_Comissao,a.p_Corretor,a.CorretorPrincipal,b.razao, b.c_SUSEP
					from InformCorretor a
						  inner join consultor b on b.idconsultor = a.idCorretor
					where a.idInform = ".$idInform. " order by a.CorretorPrincipal desc";					
			$resultado = odbc_exec($db, $cons);
			$pularlinha = '';
			$MultiCorretor  = '';
			
			$linhas = odbc_num_rows($resultado);
             if ($linhas){  
			    while (odbc_fetch_row($resultado)){
				   	if (odbc_result($resultado,'CorretorPrincipal') == 1){	
				       $Corretor =  odbc_result($resultado,'razao');
				       $codSusep = odbc_result($resultado,'c_SUSEP');
					 }
					
					$MultiCorretor .= $pularlinha.odbc_result($resultado,'razao');
					$pularlinha = '<br>';
				   
			   } 
			 }else{
				 $Corretor = '';
				 $codSusep = '';
			 }
			 //print $MultiCorretor.'<br>'.$linhas;
			 //break;

$msg6 = "\n1.	Raz�o social: ".trim($name)."\n".
"2.	CNPJ: ".arruma_cnpj($cnpj)."\n".
"3.	Inscri��o Estadual: ". (preg_match("/^[0-9]+$/", $ie) ? number_format($ie, 0, '', '.') : $ie). "\n".
//"4.	Atividade Principal Desenvolvida: $products\n".
"4.	Atividade Principal Desenvolvida: $descrip\n".
"5.	Endere�o: ".$end."\n".
"6. Complemento: ".$complemento."\n".
"7.	Telefone: ".$tel."\n".
"8.	Fax: ".$fax."\n".
"9.	E-mail: ".$email."\n".
"10.	Contato: ".$respName."\n".
"11.	Cargo: ".$oContact."\n".
"12. Corretor: ".$Corretor;

//print $msg6;

$msg7 = "COBERTURA:";

$sql = "Select nas, tipoDve, currency from Inform where id = '".$idInform."'";
$cur = odbc_exec($db, $sql);
if (odbc_fetch_row ($cur)) {
  $nas    =  odbc_result($cur, 1);
}

$tipdve = odbc_result($cur, 2);

    if ($tipdve == "1") {
         $tipodve = "m�s";
         $periodo = "mensal";
    }elseif($tipdve == "2") {
         $tipodve = "trimestre";
         $periodo = "trimestral";
	}elseif($tipdve == "3") {
         $tipodve = "ano";
         $periodo = "anual";
    }else{
         $tipodve = "m�s";
         $periodo = "mensal";
    }


$currency = odbc_result($cur, 3);


if ($currency == "1") {
	$extM = "Real";
    $mo = "R$";
    $sifra = "R$";
}else if ($currency == "2") {
      $extM = "d�lar norte-americano";
      $mo = "USD ";
      $sifra = "US$";
}elseif ($currency == "6") {
      $extM = "Euro";
      $mo = "� ";
      $sifra = "�";
}elseif ($currency == "0") {
      $extMoeda = "d�lar norte-americano";
      $mo = "US$ ";
      $sifra = "US$";
}

$msg8 = "\n1.	Abrang�ncia do seguro:|\n".
//"1.1.	Natureza das vendas seguradas: ".$descrip."\n".
"1.1.	Natureza das vendas seguradas: ".$products."\n".
"|2.	Riscos cobertos:|\n".
"2.1.	Tipo de importador: Pessoa jur�dica de direito privado.\n".
"2.2.	Pa�ses cobertos: Todos.\n".
"2.3.	Percentagem de cobertura: ".number_format($percCoverage, 0, ",", ".")."%\n".
"2.4.	Dura��o m�xima de cr�dito: at� 180 dias contados a partir do embarque internacional das mercadorias ou dos servi�os prestados.\n".
"|3.	Outros:|\n".
"3.1.	Moeda da Ap�lice: $mo ($extM).\n".
"|4.	Indeniza��es:|\n".
"4.1.	Limite m�ximo para pagamento de indeniza��es: ".
number_format($limPagIndeniz, 0, '', '.').
" (".trim($numberExtensive->extensive($limPagIndeniz, 0)).
") vezes o pr�mio pago por exerc�cio da Ap�lice.\n".
"4.2.	Limite m�nimo para pagamento de indeniza��es: $mo".number_format($nas, 2, ',','.')." (".$numberExtensive->extensive(number_format($nas,2, '.',''),$currency).").\n"; //Quinhentos D�lares Norte-Americanos

$extnas = $mo." ".number_format($nas, 2, ',','.')." (".strtolower($numberExtensive->extensive(number_format($nas,2, '.',''),$currency)).')';



$msg10 = "CUSTOS DA COBERTURA:";
$txExt = number_format($tx, 3, '.', '');
// $txPrint = ($currency == 1 ? "R$" : "US$").number_format($tx,2,",",".");
$xaplicaTaxa = isset($xaplicaTaxa) ? $xaplicaTaxa : '';
if($xaplicaTaxa=="1") {
	$txPrint = number_format($tx, 3, ",", ".");
	$xValorSomado = number_format($pr,2,",",".");
	$msg99x = "";
	$dasda = odbc_exec($db, "INSERT INTO TB_TAXA_INFORME_BB(ID_INFORME, IC_APLICA, DT_REGISTRO) VALUES($idInform, 1, getdate())");
} else {
	$dasda = odbc_exec($db, "INSERT INTO TB_TAXA_INFORME_BB(ID_INFORME, IC_APLICA, DT_REGISTRO) VALUES($idInform, 0, getdate())");
	$txPrint = number_format($tx, 3, ",", ".");
	$xValorSomado = number_format($pr,2,",",".");
	//$txPrint = number_format($tx+($tx*0.10), 3, ",", ".");
	//$xValorSomado = number_format($pr+($pr*0.10),2,",",".");
}



if($tipobanco==0) 
	$msg99x = "";
else
	$msg99x = "";



$prExt = number_format($pr, 2, '.', '');
//$prPrint = ($currency == 1 ? "R$" : "US$").number_format($pr,2,",",".");

if ($currency == 1) {
  $prPrint = "R$ ". $xValorSomado;
}elseif ($currency == "2") {
  $prPrint = "USD ". $xValorSomado;
}elseif ($currency == "6") {
  $prPrint = "� ". $xValorSomado;
}

$txMonExt =  number_format($txMonitor, 2, '.', '');
//$txMon = ($currency == 1 ? "R$" : "US$").number_format($txMonitor,2,",",".");
$txMon = "R$ ".number_format($txMonitor,2,",",".");
$txAn = "R$ ".number_format($txAnalise,2,",",".");


//$extAnalise = $numberExtensive->extensive(number_format($txAnalise,2,",","."), 1);
//$extAnalise = $numberExtensive->extensive(number_format($txAnalise,2,"",""), 1);
$extAnalise = $numberExtensive->extensive($txAnalise, 1);

//verificar o valor do pr�mio

$qryx = "SELECT inf.txMin,inf.prMTotal,inf.prMin,txMTotal, inf.v_LMI,Renovacao_Tacita  FROM  Inform inf WHERE inf.id = $idInform";
$curx = odbc_exec ($db,$qryx);
$pr = odbc_result($curx,'prMTotal');
$txMTotal = odbc_result($curx, 'txMTotal');
$tx = $txMTotal;
$ValorLMI = odbc_result($curx, 'v_LMI');
$renovacao_Tacica  = odbc_result($curx,'Renovacao_Tacita');

//print $pr;
//die;
$extPremioMinimo = $numberExtensive->extensive(number_format($pr,0,'.',''),$currency);

$ExtValorLMI = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$currency);


/*Rever Numero de parcelas */

$numPre = ($field->getField("Num_Parcelas") ? $field->getField("Num_Parcelas") : $field->getField("numParc"));
//$valPar = number_format($pr / $numPre, 2, ".", "");
$valPar = $pr / $numPre;
$parc = $valPar;



$taxa_analise = number_format($txAnalise,2,",",".");
//$extMonit = $numberExtensive->extensive(number_format($txMonitor,2,",","."), 1);
$extMonit = $numberExtensive->extensive($txMonitor, 1);
$taxa_monit = number_format($txMonitor,2,",",".");
$taxa_monitoramento = $taxa_monit;
# echo "<br>$txMon<br>";
// $msg11a = $txPrint." ( ".$numberExtensive->extensive($txExt, $currency).")";
$msg11a = $txPrint."%";
// $msg11b = $prPrint." ( ".$numberExtensive->extensive($prExt, $currency).")";

$txtParcs="";


    if($Periodo_Vigencia){
		$pvigencia = $Periodo_Vigencia;
	}else{
		$pvigencia = ($pvigencia == "" || $pvigencia == 1 ? 12: 24);
		
	}
	
	if($t_Vencimento || $Num_Parcelas){
		  if ($numParc == 1){
			  $txtParcs .= "� vista.";
			  $periodo1 = "� vista";
		  }else{
			  $txtParcs .= $Num_Parcelas." presta��es iguais e ".$t_Vencimento;
			  //$periodo = "trimestral";
			  $periodo1 = $t_Vencimento;
		  }
	}else if ($pvigencia <=12) {
			$Periodo_Vigencia   = $pvigencia;
			if ($numParc == 1){
			  $txtParcs .= "� vista.";
			  //$periodo = "� vista";
			  $periodo1 = "� vista";
			}else if ($numParc == 2){
			  $txtParcs .= "duas presta��es iguais: 1 e mais 1 em noventa dias.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 4){
			  $txtParcs .= "quatro presta��es iguais: 1 e mais 3 trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 7){
			  $txtParcs .= "sete presta��es iguais mensais.";
			  //$periodo = "mensal";
			  $periodo1 = "mensais";
			}
		
	 }elseif ($pvigencia > 12){	
			if ($numParc == 1){
			  $txtParcs .= "� vista.";
			  //$periodo = "� vista";
			  $periodo1 = "� vista";
			}else if ($numParc == 4){
			  $txtParcs .= "quatro presta��es iguais: 1 e mais 3 trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 7){
			  $txtParcs .= "sete presta��es iguais mensais.";
			  //$periodo = "mensal";
			  $periodo1 = "mensais";
			}else if ($numParc == 8){
			  $txtParcs .= "oito presta��es iguais trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}
	 }
$msg11b = $prPrint.$txtParcs;
$msg99x = $msg99x;


//$msg11b = $prPrint ." pago em quatro presta��es trimestrais iguais / pago � vista.";

//Alterado por Tiago V N - Elumini - 20/03/2006
$arrV = array(",");
$arrP = array(".");
$txM = str_replace($arrV, $arrP, $txMonitor);
$txA = str_replace($arrV, $arrP, $txAnalise);

$msg11c = $txMon." (".trim($numberExtensive->extensive(number_format($txM,2,".",","), 1)).")";
$msg11e = $txAn." (".trim($numberExtensive->extensive(number_format($txA, 2,".",","), 1)).")";

$sql = "Select mModulos, pLucro, perBonus, perPart0, perPart1, Ga, mPart from Inform where id = '".$idInform."'";
$cur = odbc_exec($db, $sql);
if (odbc_fetch_row ($cur)) {
  $mBonus    =  odbc_result($cur, 1);
  $mPart     =  odbc_result($cur, 7);
  $pLucro    =  odbc_result($cur, 2);
  $perBonus  =  odbc_result($cur, 3);
  $perPart0  =  odbc_result($cur, 4);
  $perPart1  =  odbc_result($cur, 5);
  $ga        = odbc_Result($cur, 6);
}

//if ($mBonus == "1" Or $mBonus == "2") {
    $modulos = "1";
//}
  $msg11d = "";
  $a502 = "";
  $a502titulo = "";
  $b1202X = "";
  $b1202 = "";
 
  $x = odbc_exec($db, "select a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602,
						   p_cobertura_d701, limite_d701,adequacao_sinistralidade,adequacao_premio,franquia_anual, 
						   CONVERT(varchar(1000),condicoes_especiais) AS condicoes_especiais,
						   PrazoMaxEmiNota, b904, b2604Perc, b2604NivelMax, 
						   b2801NivelMax, b2801Perc, d201, f401NivelSinistralidade,f401PercPremio,b802,b404NivelMax,b404Perc,GerenteCredito,Texto_Risco_Politico
					   from ModuloOferta where idInform=$idInform");
	 
	  // Defini�ao dos modulos
      $mod_a801 = odbc_result($x,1);
      $mod_a502 = odbc_result($x,2);
      $mod_b603 = odbc_result($x,3);
      $mod_b1504 = odbc_result($x,4);
      $mod_b1202 = odbc_result($x,5);
      $mod_c102 = odbc_result($x,6);
      $mod_d101 = odbc_result($x,7);
      $mod_d602 = odbc_result($x,8);
      $mod_d701 = odbc_result($x,9);
      $mod_e101 = odbc_result($x,10);
      $mod_f305 = odbc_result($x,11);
      $mod_f3301 = odbc_result($x,12);
	  
	  $GerenteNome    = odbc_result($x,'GerenteCredito');

      $ad_sinistr = odbc_result($x,'adequacao_sinistralidade');
      $ad_premio = odbc_result($x,'adequacao_premio');
	  
	  
	  $PrazoMaxEmiNota           = odbc_result($x,'PrazoMaxEmiNota');
	  $mod_b904                  = number_format(odbc_result($x,'b904'),2,',','.'); 
	  $mod_b904Ext               = $numberExtensive->extensive(number_format(odbc_result($x,'b904'),2,'.',''),$currency);
	  $mod_b2604                 = number_format(odbc_result($x,'b2604Perc'),2,',','.');
	  $b2604NivelMax             = number_format(odbc_result($x,'b2604NivelMax'),2,',','.');
	  $b2604NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2604NivelMax'),2,'.',''),$currency);
	  
	  $b2801NivelMax             = number_format(odbc_result($x,'b2801NivelMax'),2,',','.');
	  $b2801NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2801NivelMax'),2,'.',''),$currency);
	  $b2801Perc                 = number_format(odbc_result($x,'b2801Perc'),2,',','.');
	  
	  $b404NivelMax              = number_format(odbc_result($x,'b404NivelMax'),2,',','.');
    $b404NivelMaxExt           = $numberExtensive->extensive(number_format(odbc_result($x,'b404NivelMax'),2,'.',''),$currency);
    $mod_b404                  = odbc_result($x,'b404Perc');
	  $riscopolitico             = odbc_result($x,'Texto_Risco_Politico');
	  
	  $d201                      = number_format(odbc_result($x,'d201'),2,',','.');
	  $valorExtD201              = $numberExtensive->extensive(number_format(odbc_result($x,'d201'),2,'.',''),$currency);
	  $f401NivelSinistralidade   = number_format(odbc_result($x,'f401NivelSinistralidade'),2,',','.');
	  $f401PercPremio            = number_format(odbc_result($x,'f401PercPremio'),2,',','.');
	  $mod_b802                  = number_format(odbc_result($x,'b802'),2,',','.');
	  
	  
      if ($ad_sinistr > 0 || $ad_premio > 0){
           $ad_sinistr = $ad_sinistr;
           $ad_premio = $ad_premio;
           $exibe_ad = 1;

      }else {
           $ad_sinistr = 0;
           $ad_premio = 0;
           $exibe_ad = 0;
      }

      $franquia_anual = odbc_result($x,'franquia_anual');

      if ($franquia_anual > 0){
          $franquia_anual = number_format(odbc_result($x,'franquia_anual'),2,',','.') ." (".strtolower($numberExtensive->extensive(number_format(odbc_result($x,'franquia_anual'),2,'.',''),$currency)).").";
          $exibe_franq = 1;

      }else{
          $franquia_anual = 0;
          $exibe_franq = 0;
      }

      if (odbc_result($x,'condicoes_especiais') != "" ){
         $condicoes_especiais = odbc_result($x,'condicoes_especiais') ;
         $exibe_cond = 1;
      }else{
         $condicoes_especiais = "";
         $exibe_cond = 0;
      }

      if(odbc_result($x, 1) == "1") {
		$modulo_final = "A8.01 Risco de N�o Pagamento \n\n";
	  }
	  if(odbc_result($x, 2) == "1") {
		$modulo_final .= "A5.02 Cobertura de Risco de Produ��o\n\n";
            // altera��o para o novo layout da proposta
           $a502titulo = "|A5.02    COBERTURA DE RISCO DE PRODU��O| ";
	   $a502 = $a502."i) A cobertura de risco de produ��o se aplica para todos os importadores indicados na ficha de aprova��o de limites de cr�dito.";
	  }
	  if(odbc_result($x, 3) == "1") {
		$modulo_final .= "B6.03 Pedidos Pendentes\n\n";
	  }
	  if(odbc_result($x, 4) == "1") {
		$modulo_final .= "B15.04 Limites de Cr�dito\n\n";
	  }


         
       

          // EXTENS�O DO CONTRATO A UMA OU MAIS EMPRESAS
          if(odbc_result($x, 5) == "1") {
             $b1202X = "|B12.02|    |EXTENS�O DO CONTRATO A UMA OU MAIS EMPRESAS|   ";
             $b1202 = "";
             $b1202 = $b1202."A cobertura � estendida aos contratos de vendas celebrados pelas seguintes empresas: \n\n";

             $modulo_final .= "B12.02 Extens�o do contrato a uma ou mais empresas\n";

                       $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = $idInform ORDER BY no_razao_social ";
                       $cur = odbc_exec ($db, $query);
             $i = 0;
                      while (odbc_fetch_row($cur)){

                            $no_razao_social       = odbc_result ($cur, 'no_razao_social');
                            $nu_cnpj               = odbc_result ($cur, 'nu_cnpj');
                            $nu_inscricao_estadual = odbc_result ($cur, 'nu_inscricao_estadual');
                            $ds_endereco           = odbc_result ($cur, 'ds_endereco');
                            $nu_endereco           = odbc_result ($cur, 'nu_endereco');
                            $ds_complemento        = odbc_result ($cur, 'ds_complemento');
                            $nu_cep                = odbc_result ($cur, 'nu_cep');
                            $no_cidade             = odbc_result ($cur, 'no_cidade');
                            $no_estado             = odbc_result ($cur, 'no_estado');
                            $i ++;
                                if ($no_razao_social){
                                    if ($i > 1){
                                          $b1202 .= "\n";
                                       }
                                    $modulo_final .= ''.$no_razao_social." - Endere�o: ".$ds_endereco.", ".$nu_endereco."  ".$ds_complemento."\nCidade: ".$no_cidade."  Estado: ".$no_estado."  CEP: ".$nu_cep."   CNPJ: ".$nu_cnpj."  IE: ".$nu_inscricao_estadual." \n";
                                    $b1202 .= 'Raz�o Social:'.$no_razao_social."\n";
                                    $b1202 .= 'Endere�o:'.$ds_endereco.", ".$nu_endereco." ".$ds_complemento."\nCidade: ".$no_cidade."  Estado: ".$no_estado."  CEP: ".$nu_cep."\nCNPJ: ".$nu_cnpj."\n";

                                }

                      }
              //$b1202 = $b1202."\n";
	      $modulo_final .= "\n";
        
	  }
     
	  
		
	  if(odbc_result($x, 6) == "1") {
		$modulo_final .= "C1.02 Servi�o de cobran�a integral\n\n";
	  }
	  if(odbc_result($x, 7) == "1") {
		$modulo_final .= "D1.01 Limite m�nimo para notifica��o de amea�a de sinistro\n\n";
	  }
	  
     if(odbc_result($x, 8) == "1") {
       //$d602  = "|D6.02 PEQUENOS SINISTROS| ";
       $nivel_d602 = number_format(odbc_result($x, 13),2,'.','');
       $nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$currency));
       $d602 = "O n�vel de pequenos sinistros � de: ".$sifra." ".formata($nivel_d602)." (".$nivel.").";
     }

     if(odbc_result($x, 9) == "1") {
        if($d602=="") {
           $d701  = "\n\n|D7.01 LIT�GIO|";
        } else {
           $d701  = "\n\n|D7.01 LIT�GIO|";
        }
       $p_cobertura_d701  = odbc_result($x, 14);
       $limite_d701       = odbc_result($x, 15);
       //$d701 = $d701."\n\ni) Percentual de Cobertura: ".$p_cobertura_d701."% \nii) Limite de pagamento por Lit�gio:".$limite_d701."%\n";
       $d701 = "O percentual de cobertura � de: ".$p_cobertura_d701."% <br>".$limite_d701."";
  }
     /*

     if(odbc_result($x, 8) == "1") {
		$modulo_final .= "D6.02 Pequenos Sinistros";
 	    $nivel_d602 = odbc_result($x, 'nivel_d602');
	    $modulo_final .= "\n   i) N�vel de pequenos sinistros: US$ ".formata($nivel_d602)."\n\n";
	  }
	  if(odbc_result($x, 9) == "1") {
		$modulo_final .= "D7.01 Lit�gio";
	    $p_cobertura_d701  = odbc_result($x, 'p_cobertura_d701');
	    $limite_d701 = odbc_result($x, 'limite_d701');
	    $modulo_final .= "\n   i) Percentual de Cobertura: ".$p_cobertura_d701."% \n   ii) Limite de pagamento por Lit�gio: ".$limite_d701."\n\n";
	  }
	  */

     if(odbc_result($x, 10) == "1") {
		$modulo_final .= "E1.02 Recupera��es\n\n";
	  }
	  if(odbc_result($x, 11) == "1") {
		$modulo_final .= "F3.05 Pagamento do Pr�mio e Declara��es de Volume de Exporta��es\n\n";
	  }
	  if(odbc_result($x, 12) == "1") {
		$modulo_final .= "F33.01 Custos Adicionais\n\n";
	  }

     $bonus = "";
        // ALTERA��O B�NUS
     if ($mBonus == "1") {
     		$perBonus = (int)$perBonus;
        $bonus = "A percentagem de b�nus referente ao item 2 deste m�dulo � de ".round($perBonus)."%.\n\n"; //10%(dez por cento).
     }

     if ($mPart == "1") {
        if ($pLucro == "F13") {
           $partic  = "F13.02    PARTICIPA��O NOS LUCROS";
        }else if ($pLucro == "F14") {
           $partic  = "F14.02    PARTICIPA��O NOS LUCROS";
        }else if ($pLucro == "F15") {
           $partic  = "F15.02    PARTICIPA��O NOS LUCROS";
        }
		
        $extpart0   = $numberExtensive->porcentagem(number_format($perPart0,2,'.',''));
        $extpart1   = $numberExtensive->porcentagem(number_format($perPart1,2,'.',''));
        $part .= "Percentagem de Dedu��o: ".$perPart0."% (".$extpart0.")\n".
                 "Participa��o nos Lucros: ".$perPart1."% (".$extpart1.")\n";
        $valbo ="1";
       
      }
  
  //die('?'.$perPart1);
  
	   $modulo = $modulo_final;
	   $msg11d = "\n".$modulo;
		$msg13 = "CONDI��ES ESPECIAIS DE COBERTURA:";
		
		$msg14 = "O Proponente solicita a emiss�o das Condi��es Especiais de Cobertura Acess�ria de Juros Operacionais e Morat�rios, que ensejar� a cobran�a de pr�mio adicional correspondente a 4% do pr�mio da Ap�lice?";

if($interest == 0) {
  $msg15 = "N�O"; //"[] SIM	 	[X] N�O";
} else {
  $msg15 = "SIM"; //"[X] SIM	 	[] N�O";
}

$msg17 = "CONDI��ES PARA ACEITA��O:";

//Alterado por Tiago V N - Elumini - 14/06/2006
//$msg18a = "1. O Proponente declara expressamente ter recebido, estar ciente e inteiramente de acordo com todas as cl�usulas das Condi��es Gerais da Ap�lice de Seguro de Cr�dito � Exporta��o registrada na SUSEP sob o n.� $susep, com as cl�usulas das respectivas Condi��es Particulares, a serem emitidas pela Seguradora nos exatos termos desta Proposta de Seguro, bem como disposi��es das Condi��es Especiais de Cobertura solicitadas atrav�s desta Proposta.\n";
//$msg18b = "2. Esta Proposta de Seguro � v�lida por 30 dias, contados a partir de seu recebimento pelo Proponente.\n3. Ap�s protocolada a Proposta, devidamente assinada pelo representante legal do Proponente, e mediante a comprova��o do pagamento integral ou da primeira parcela do Pr�mio M�nimo, a SBCE ter� o prazo de 15 (quinze) dias para se manifestar a respeito da aceita��o do seguro. Caso n�o haja nenhuma manifesta��o da SBCE neste prazo, o seguro estar� automaticamente aceito.\n4. Caso a SBCE decida n�o aceitar a Proposta de Seguro e o Pr�mio respectivo j� tenha sido pago, este ser� devolvido integralmente.\n\n\n";
//$validCot ($validCotExt)
$msg4 = "____________________________________________________________________________________________________________________\n";
$msg9 = $msg4;
$msg12 = $msg4;
$msg16 = $msg4;


?>
