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
       $extMoeda = "�";
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

Apresentamos,a seguir, nossa oferta firme para a contrata��o de sua
ap�lice de seguro de cr�dito � exporta��o:

Taxa de pr�mio: ".$tx."%
Pr�mio m�nimo: $extMoeda ".$pr." (".$numberExtensive->extensive($prExt, $tipoM).").
Vig�ncia da ap�lice: $pvigencia ano a partir da aceita��o da proposta de seguro.
Validade desta cota��o: $validCot dia(s).

Uma vez assinado o contrato entre a SBCE e essa empresa, o pagamento do pr�mio m�nimo dever� ser efetuado � vista";

if ($Num_Parcelas != 1) {
  $msg .= " ou em $Num_Parcelas parcela(s), da seguinte forma: parcela � vista, na contrata��o da ap�lice, no valor de $extMoeda ". number_format($primeira, 2, ',', '.'). " e as demais, conforme a seguir, em  $extMoeda:

Parcela            Valor\n";
               
  for($j = 2; $j <= $Num_Parcelas; $j++){
    $msg_aux = "$j". "�               ".
      number_format($valPar, 2, ',', '.'). "\n";
    $msg .= $msg_aux;
  }
}else{
  $msg .= '.';
}
// 1� parcela: na assinatura do contrato (� vista);
// 2� parcela: 90 dias a partir do in�cio da vig�ncia da ap�lice;
// 3� parcela: 180 dias a partir do in�cio da vig�ncia da ap�lice;
// 4� parcela: 270 dias a partir do in�cio da vig�ncia da ap�lice;

	  $x = odbc_exec($db, "select a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602, p_cobertura_d701, limite_d701 from ModuloOferta where idInform=$idInform");
	  if(odbc_result($x, 1) == "1") {
		$modulo_final = "A8.01 Risco de N�o Pagamento \n\n";
	  }
	  if(odbc_result($x, 2) == "1") {
		$modulo_final .= "A5.02 Cobertura de Risco de Produ��o\n\n";
	  }
	  if(odbc_result($x, 3) == "1") {
		$modulo_final .= "B6.03 Pedidos Pendentes\n\n";
	  }
	  if(odbc_result($x, 4) == "1") {
		$modulo_final .= "B15.04 Limites de Cr�dito\n\n";
	  }
	  
	  if(odbc_result($x, 5) == "1") {
		$modulo_final .= "B12.02 Extens�o do Contrato a uma ou mais Empresas\n";
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
		$modulo_final .= "C1.02 Servi�o de Cobran�a Integral\n\n";
	  }
	  if(odbc_result($x, 7) == "1") {
		$modulo_final .= "D1.01 Limite M�nimo para Notifica��o de Amea�a de Sinistro\n\n";
	  }
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
	  if(odbc_result($x, 10) == "1") {
		//$modulo_final .= "E1.01 Recupera��es\n\n";
        // 15/05/2009 - Interaktiv (Elias Vaz) - Altera��o de acordo com a solicita��o n� 8 do documento vers�o 1.1
        $modulo_final .= "E1.02 Recupera��es\n\n";
	  }
	  if(odbc_result($x, 11) == "1") {
		$modulo_final .= "F3.05 Pagamento do Pr�mio e Declara��es de Volume de Exporta��es\n\n";
	  }
	  if(odbc_result($x, 12) == "1") {
		$modulo_final .= "F33.01 Custos Adicionais\n\n";
	  }

		if ($mBonus == "2") {
		     if ($pLucro == "F13") {
		         $modulo_final .= "F13.02 - Participa��o nos Lucros\n";
		     }else if ($pLucro == "F14") {
		         $modulo_final .= "F14.02 - Participa��o nos Lucros\n";
		     }else if ($pLucro == "F15") {
		         $modulo_final .= "F15.02 - Participa��o nos Lucros\n";
		     }
    		 $extpart0   = $numberExtensive->porcentagem($perPart0);
		     $extpart1   = $numberExtensive->porcentagem($perPart1);
	         $modulo_final .= "Percentagem de Dedu��o: ".$perPart0."% (".$extpart0.")\n".
		                  "Participa��o nos Lucros: ".$perPart1."% (".$extpart1.")\n";
		}else if ($mBonus == "0"){
		         $part = "";
		}

		  if ($mBonus == "1") {
		        $modulo_final .= "F9.02 - B�nus por Aus�ncia de Sinistros\nvinculado a renova��o da ap�lice por mais um per�odo de seguro.\n" .
		                 "Percentual do b�nus por aus�ncia de sinistros: ". $perBonus."%\n"; //10%(dez por cento).
		  }else if ($mBonus == "0") {
		        $bonus = "";
		  }


        //Condi��o Especial de Cobertura de Coligadas
	
		$query = " select * from ParModEsp_Coligada where idInform = $idInform ORDER BY razaoSocial ";
                $curc = odbc_exec ($db, $query);
           
             if(odbc_result($curc, 1) > 0) {
                $modulo_final .= "Condi��o Especial de Cobertura de Coligadas \n";

		
		  while (odbc_fetch_row($curc))
		  {
			$razaoSocial_col       = odbc_result ($curc, 'razaoSocial');
			$cnpj_col               = odbc_result ($curc, 'numRegistro');
			$endereco_col           = odbc_result ($curc, 'endereco');
                        $numendereco_col        = odbc_result ($curc, 'numeroEndereco');
                        $Complemento_col        = odbc_result ($curc, 'complementoEndereco');
			$cidade_col             = odbc_result ($curc, 'cidade');
			$estado_col             = odbc_result ($curc, 'estado');

			$modulo_final .= "Raz�o Social: ". $razaoSocial_col." \n";
                        $modulo_final .= "N�mero Registro: ". $cnpj_col." \n";
                        $modulo_final .= "Endere�o: ". $endereco_col.", ".$numendereco_col ."  Compl.".$Complemento_col." \n";
                        $modulo_final .= "Cidade: ". $cidade_col." - Estado ".$estado_col  ." \n";
		  }
		  $modulo_final .= "\n";
	    }



	   $modulo = $modulo_final;
	   $part = $modulo;

$msg .= "
Ap�s o final da vig�ncia de sua ap�lice, com base nas Declara��es de Volume de Exporta��o (DVE) enviadas por essa empresa, verificaremos se existe alguma parcela de ajuste ao pr�mio m�nimo, multiplicando-se o volume de exporta��es realizadas pass�veis de seguro pela taxa de pr�mio proposta para a sua ap�lice, no caso ". $tx. "%.\n\n".

$bonus .  $part .

"Ser�o cobradas as seguintes taxas:

- Taxa de an�lise cadastral: paga, anualmente, por importador analisado, no final do primeiro trimestre de
vig�ncia da ap�lice ou quando houver inclus�o de novo importador.  O valor atual da taxa de an�lise � 
de ".$txAnal."(".$numberExtensive->extensive($txAnalExt, 1).") por importador analisado.

- Taxa de monitoramento: devida ao final de cada trimestre e cobrada enquanto o importador permanecer segurado.
O valor atual da taxa de monitoramento � de ".$txMonFull."(".$numberExtensive->extensive($txMonExt, 1).")
por importador monitorado, cobrado em 4 parcelas trimestrais.

Uma vez acordadas todas as condi��es pertinentes � contrata��o de sua ap�lice, enviaremos a documenta��o para sua assinatura e a fatura para o pagamento da parcela devida de pr�mio m�nimo.

Permanecemos � disposi��o no que for preciso e aguardamos seu retorno.

Atenciosamente,

".$user->name."
Divis�o Comercial
".$nomeEmpSBCE."
".$siteEmpSBCE."

Rio de Janeiro
Rua Senador Dantas, 74 - 16� andar
Centro - Rio de Janeiro - RJ - 20031-201
Tel.: (21) 2510.5000

S�o Paulo
P�a. Jo�o Duran Alonso, 34 - 12� andar
Brooklin Novo - SP - 04571-070
Tel.: (11) 5509 8181
Fax : (11) 5509 8182\n\n";

?>
