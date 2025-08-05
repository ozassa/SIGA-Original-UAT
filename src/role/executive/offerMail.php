<?php  /**
currency
*/
//Alterado por Tiago V N - 30/09/2005
//Alterado por Tiago V N - 13/01/2006
function formata($numero){
    if(strpos($numero,'.')!=''){
       $var=explode('.',$numero);
           if(strlen($var[0])==4){
              $parte1=substr($var[0],0,1);
              $parte2=substr($var[0],1,3);
                 if(strlen($var[1])<2){
                    $formatado=$parte1.'.'.$parte2.','.$var[1].'0';
                 }
                 else{
                    $formatado=$parte1.'.'.$parte2.','.$var[1];
                 }
           }
           elseif(strlen($var[0])==5){
              $parte1=substr($var[0],0,2);
              $parte2=substr($var[0],2,3);
                   if(strlen($var[1])<2){
                      $formatado=$parte1.'.'.$parte2.','.$var[1].'0';
                   }
                   else{
                      $formatado=$parte1.'.'.$parte2.','.$var[1];
                   }
           }
           elseif(strlen($var[0])==6){
              $parte1=substr($var[0],0,3);
              $parte2=substr($var[0],3,3);
                 if(strlen($var[1])<2){
                    $formatado=$parte1.'.'.$parte2.','.$var[1].'0';
                 }
                 else{
                    $formatado=$parte1.'.'.$parte2.','.$var[1];
                 }
           }
           elseif(strlen($var[0])==7){
               $parte1=substr($var[0],0,1);
               $parte2=substr($var[0],1,3);
               $parte3=substr($var[0],4,3);
                   if(strlen($var[1])<2){
                      $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
                   }
                   else{
                      $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
                   }
           }
           elseif(strlen($var[0])==8){
               $parte1=substr($var[0],0,2);
               $parte2=substr($var[0],2,3);
               $parte3=substr($var[0],5,3);
                  if(strlen($var[1])<2){
                     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
                  }else{
                     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
                  }
           }
           elseif(strlen($var[0])==9){
               $parte1=substr($var[0],0,3);
               $parte2=substr($var[0],3,3);
               $parte3=substr($var[0],6,3);
                  if(strlen($var[1])<2){
                     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
                  }
                  else{
                     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
                  }
           }
           elseif(strlen($var[0])==10){
               $parte1=substr($var[0],0,1);
               $parte2=substr($var[0],1,3);
               $parte3=substr($var[0],4,3);
               $parte4=substr($var[0],7,3);
                   if(strlen($var[1])<2){
                      $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1].'0';
                   }
                   else{
                      $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1];
                   }
           }
           else{
              if(strlen($var[1])<2){
                 $formatado=$var[0].','.$var[1].'0';
              }
              else{
                 $formatado=$var[0].','.$var[1];
              }
           }
    }
    else{
       $var=$numero;
            if(strlen($var)==4){
                $parte1=substr($var,0,1);
                $parte2=substr($var,1,3);
                $formatado=$parte1.'.'.$parte2.','.'00';
            }
            elseif(strlen($var)==5){
                $parte1=substr($var,0,2);
                $parte2=substr($var,2,3);
                $formatado=$parte1.'.'.$parte2.','.'00';
            }
            elseif(strlen($var)==6){
                $parte1=substr($var,0,3);
                $parte2=substr($var,3,3);
                $formatado=$parte1.'.'.$parte2.','.'00';
            }
            elseif(strlen($var)==7){
                $parte1=substr($var,0,1);
                $parte2=substr($var,1,3);
                $parte3=substr($var,4,3);
                $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
            }
            elseif(strlen($var)==8){
                $parte1=substr($var,0,2);
                $parte2=substr($var,2,3);
                $parte3=substr($var,5,3);
                $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
            }
            elseif(strlen($var)==9){
                $parte1=substr($var,0,3);
                $parte2=substr($var,3,3);
                $parte3=substr($var,6,3);
                $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
            }
            elseif(strlen($var)==10){
                $parte1=substr($var,0,1);
                $parte2=substr($var,1,3);
                $parte3=substr($var,4,3);
                $parte4=substr($var,7,3);
                $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.'00';
            }
            else{
                $formatado=$var.','.'00';
            }
    }
    return $formatado;
}

include_once("../consultaCoface.php");

$sql = "Select pvigencia, numParc, mModulos, pLucro, perBonus, perPart0, perPart1, currency,Num_Parcelas,t_Vencimento from Inform where id = '".$idInform."'";
$cur = odbc_exec($db, $sql);
if (odbc_fetch_row ($cur)) {
  $vigencia  =  odbc_result($cur, 1);
  $numParc   =  odbc_result($cur, 2);
  $mBonus    =  odbc_result($cur, 3);
  $pLucro    =  odbc_result($cur, 4);
  $perBonus  =  odbc_result($cur, 5);
  $perPart0  =  odbc_result($cur, 6);
  $perPart1  =  odbc_result($cur, 7);
  $moeda       = odbc_result($cur, "currency");
  $Num_Parcelas = (odbc_result($cur, "Num_Parcelas") ? odbc_result($cur, "Num_Parcelas"):$numParc);
  $t_Vencimento  = odbc_result($cur, "t_Vencimento");
  if ($moeda == "1") {
       $tipoM = "1";
       $extMoeda = "R$";
  }else if ($moeda == "2") {
       $tipoM = "2";
       $extMoeda = "US$";
  }else{
       $tipoM = "6";
       $extMoeda = "€";
  }

}
//$numParc = $field->getNumField("numParc");
$valPar = sprintf("%.2f", $pr / $Num_Parcelas);
$diferenca = $pr - ($valPar * $Num_Parcelas);
$primeira = $valPar + $diferenca;

$prExt = number_format($pr, 2, '.', '');
$tx = number_format($tx, 3, ",", ".");
$pr = number_format($pr, 2, ",", ".");


$txMonFull = ($field->getNumField("currencyMonitor") == 1 ? "R$ " : "US$ ").
             number_format($field->getNumField("txMonitor"),2,",",".");
$txMonExt = number_format($field->getNumField("txMonitor"), 2, '.', '');
$txMonQ = $field->getNumField("txMonitor") / 4;
$txQExt = strval(number_format($txMonQ, 2, '.', ''));
$txMonQ = "R$ ".number_format($txMonQ ,2,",",".");

$cA = strval($field->getNumField("currency"));
$txA = $field->getNumField("txAnalize");
$txAnal = ($field->getNumField("currencyAnalize") == 1 ? "R$ " : "US$").
number_format($field->getNumField("txAnalize"), 2, ",", ".");
$txAnalExt = strval(number_format($txA, 2, '.', ''));
$validCot = $field->getNumField("validCot");

//Alterado por Tiago V N -30/09/2005
if ($vigencia =="") {
   $pvigencia = "1";
}else if ($vigencia=="1"){
   $pvigencia = "1";
}else{
   $pvigencia = "2";
}
$msg =
"
Oferta SBCE - $name

Prezad(o/a) $respName,

Apresentamos,a seguir, nossa oferta firme para a contratação de sua
apólice de seguro de crédito à exportação:

Taxa de prêmio: ".$tx."%
Prêmio mínimo: $extMoeda ".$pr." (".$numberExtensive->extensive($prExt, $tipoM).").
Vigência da apólice: $pvigencia ano a partir da aceitação da proposta de seguro.
Validade desta cotação: $validCot dia(s).

Uma vez assinado o contrato entre a SBCE e essa empresa, o pagamento do prêmio mínimo deverá ser efetuado à vista";

if ($Num_Parcelas != 1) {
  $msg .= " ou em $Num_Parcelas parcela(s), da seguinte forma: parcela à vista, na contratação da apólice, no valor de $extMoeda ". number_format($primeira, 2, ',', '.'). " e as demais, conforme a seguir, em  $extMoeda:

Parcela            Valor\n";
               
  for($j = 2; $j <= $Num_Parcelas; $j++){
    $msg_aux = "$j". "ª               ".
      number_format($valPar, 2, ',', '.'). "\n";
    $msg .= $msg_aux;
  }
}else{
  $msg .= '.';
}
// 1ª parcela: na assinatura do contrato (à vista);
// 2ª parcela: 90 dias a partir do início da vigência da apólice;
// 3ª parcela: 180 dias a partir do início da vigência da apólice;
// 4ª parcela: 270 dias a partir do início da vigência da apólice;

	  $x = odbc_exec($db, "select a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602, p_cobertura_d701, limite_d701 from ModuloOferta where idInform=$idInform");
	  if(odbc_result($x, 1) == "1") {
		$modulo_final = "A8.01 Risco de Não Pagamento \n\n";
	  }
	  if(odbc_result($x, 2) == "1") {
		$modulo_final .= "A5.02 Cobertura de Risco de Produção\n\n";
	  }
	  if(odbc_result($x, 3) == "1") {
		$modulo_final .= "B6.03 Pedidos Pendentes\n\n";
	  }
	  if(odbc_result($x, 4) == "1") {
		$modulo_final .= "B15.04 Limites de Crédito\n\n";
	  }
	  
	  if(odbc_result($x, 5) == "1") {
		$modulo_final .= "B12.02 Extensão do Contrato a uma ou mais Empresas\n";
		  $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = $idInform ORDER BY no_razao_social ";
		  $cur = odbc_exec ($db, $query);
		  while (odbc_fetch_row($cur)) 
		  {
			$no_razao_social       = odbc_result ($cur, 'no_razao_social');
			$nu_cnpj               = odbc_result ($cur, 'nu_cnpj');
			$nu_inscricao_estadual = odbc_result ($cur, 'nu_inscricao_estadual');
			$ds_endereco           = odbc_result ($cur, 'ds_endereco');
			$nu_cep                = odbc_result ($cur, 'nu_cep');
			$no_cidade             = odbc_result ($cur, 'no_cidade');
			$no_estado             = odbc_result ($cur, 'no_estado');
			$modulo_final .= $no_razao_social." \n";
		  }
		  $modulo_final .= "\n";
	  }
	  if(odbc_result($x, 6) == "1") {
		$modulo_final .= "C1.02 Serviço de Cobrança Integral\n\n";
	  }
	  if(odbc_result($x, 7) == "1") {
		$modulo_final .= "D1.01 Limite Mínimo para Notificação de Ameaça de Sinistro\n\n";
	  }
	  if(odbc_result($x, 8) == "1") {
		$modulo_final .= "D6.02 Pequenos Sinistros";
 	    $nivel_d602 = odbc_result($x, 'nivel_d602');
	    $modulo_final .= "\n   i) Nível de pequenos sinistros: US$ ".formata($nivel_d602)."\n\n";
	  }
	  if(odbc_result($x, 9) == "1") {
		$modulo_final .= "D7.01 Litígio";
	    $p_cobertura_d701  = odbc_result($x, 'p_cobertura_d701');
	    $limite_d701 = odbc_result($x, 'limite_d701');
	    $modulo_final .= "\n   i) Percentual de Cobertura: ".$p_cobertura_d701."% \n   ii) Limite de pagamento por Litígio: ".$limite_d701."\n\n";
	  }
	  if(odbc_result($x, 10) == "1") {
		//$modulo_final .= "E1.01 Recuperações\n\n";
        // 15/05/2009 - Interaktiv (Elias Vaz) - Alteração de acordo com a solicitação nº 8 do documento versão 1.1
        $modulo_final .= "E1.02 Recuperações\n\n";
	  }
	  if(odbc_result($x, 11) == "1") {
		$modulo_final .= "F3.05 Pagamento do Prêmio e Declarações de Volume de Exportações\n\n";
	  }
	  if(odbc_result($x, 12) == "1") {
		$modulo_final .= "F33.01 Custos Adicionais\n\n";
	  }

		if ($mBonus == "2") {
		     if ($pLucro == "F13") {
		         $modulo_final .= "F13.02 - Participação nos Lucros\n";
		     }else if ($pLucro == "F14") {
		         $modulo_final .= "F14.02 - Participação nos Lucros\n";
		     }else if ($pLucro == "F15") {
		         $modulo_final .= "F15.02 - Participação nos Lucros\n";
		     }
    		 $extpart0   = $numberExtensive->porcentagem($perPart0);
		     $extpart1   = $numberExtensive->porcentagem($perPart1);
	         $modulo_final .= "Percentagem de Dedução: ".$perPart0."% (".$extpart0.")\n".
		                  "Participação nos Lucros: ".$perPart1."% (".$extpart1.")\n";
		}else if ($mBonus == "0"){
		         $part = "";
		}

		  if ($mBonus == "1") {
		        $modulo_final .= "F9.02 - Bônus por Ausência de Sinistros\nvinculado a renovação da apólice por mais um período de seguro.\n" .
		                 "Percentual do bônus por ausência de sinistros: ". $perBonus."%\n"; //10%(dez por cento).
		  }else if ($mBonus == "0") {
		        $bonus = "";
		  }


        //Condição Especial de Cobertura de Coligadas
	
		$query = " select * from ParModEsp_Coligada where idInform = $idInform ORDER BY razaoSocial ";
                $curc = odbc_exec ($db, $query);
           
             if(odbc_result($curc, 1) > 0) {
                $modulo_final .= "Condição Especial de Cobertura de Coligadas \n";

		
		  while (odbc_fetch_row($curc))
		  {
			$razaoSocial_col       = odbc_result ($curc, 'razaoSocial');
			$cnpj_col               = odbc_result ($curc, 'numRegistro');
			$endereco_col           = odbc_result ($curc, 'endereco');
                        $numendereco_col        = odbc_result ($curc, 'numeroEndereco');
                        $Complemento_col        = odbc_result ($curc, 'complementoEndereco');
			$cidade_col             = odbc_result ($curc, 'cidade');
			$estado_col             = odbc_result ($curc, 'estado');

			$modulo_final .= "Razão Social: ". $razaoSocial_col." \n";
                        $modulo_final .= "Número Registro: ". $cnpj_col." \n";
                        $modulo_final .= "Endereço: ". $endereco_col.", ".$numendereco_col ."  Compl.".$Complemento_col." \n";
                        $modulo_final .= "Cidade: ". $cidade_col." - Estado ".$estado_col  ." \n";
		  }
		  $modulo_final .= "\n";
	    }



	   $modulo = $modulo_final;
	   $part = $modulo;

$msg .= "
Após o final da vigência de sua apólice, com base nas Declarações de Volume de Exportação (DVE) enviadas por essa empresa, verificaremos se existe alguma parcela de ajuste ao prêmio mínimo, multiplicando-se o volume de exportações realizadas passíveis de seguro pela taxa de prêmio proposta para a sua apólice, no caso ". $tx. "%.\n\n".

$bonus .  $part .

"Serão cobradas as seguintes taxas:

- Taxa de análise cadastral: paga, anualmente, por importador analisado, no final do primeiro trimestre de
vigência da apólice ou quando houver inclusão de novo importador.  O valor atual da taxa de análise é 
de ".$txAnal."(".$numberExtensive->extensive($txAnalExt, 1).") por importador analisado.

- Taxa de monitoramento: devida ao final de cada trimestre e cobrada enquanto o importador permanecer segurado.
O valor atual da taxa de monitoramento é de ".$txMonFull."(".$numberExtensive->extensive($txMonExt, 1).")
por importador monitorado, cobrado em 4 parcelas trimestrais.

Uma vez acordadas todas as condições pertinentes à contratação de sua apólice, enviaremos a documentação para sua assinatura e a fatura para o pagamento da parcela devida de prêmio mínimo.

Permanecemos à disposição no que for preciso e aguardamos seu retorno.

Atenciosamente,

".$user->name."
Divisão Comercial
".$nomeEmpSBCE."
".$siteEmpSBCE."

Rio de Janeiro
Rua Senador Dantas, 74 - 16º andar
Centro - Rio de Janeiro - RJ - 20031-201
Tel.: (21) 2510.5000

São Paulo
Pça. João Duran Alonso, 34 - 12º andar
Brooklin Novo - SP - 04571-070
Tel.: (11) 5509 8181
Fax : (11) 5509 8182\n\n";

?>
