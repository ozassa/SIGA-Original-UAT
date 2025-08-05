<?php

//print 'oi';
class NumberUtils {

  function CGCOK($cgcIN) {
  /* entrada string:  99.999.999/9999-99 */
    if (strlen($cgcIN) <> 18) return 0;
      $s1 = ($cgcIN[0] * 5) +
            ($cgcIN[1] * 4) +
	    ($cgcIN[3] * 3) +
	    ($cgcIN[4] * 2) +
            ($cgcIN[5] * 9) +
	    ($cgcIN[7] * 8) +
	    ($cgcIN[8] * 7) +
	    ($cgcIN[9] * 6) +
	    ($cgcIN[11] * 5) +
	    ($cgcIN[12] * 4) +
	    ($cgcIN[13] * 3) +
	    ($cgcIN[14] * 2);

      $mod = $s1 % 11;
      $d1 = $mod < 2 ? 0 : 11 - $mod;

     $s2 = ($cgcIN[0] * 6) +
           ($cgcIN[1] * 5) +
	   ($cgcIN[3] * 4) +
	   ($cgcIN[4] * 3) +
           ($cgcIN[5] * 2) +
	   ($cgcIN[7] * 9) +
	   ($cgcIN[8] * 8) +
	   ($cgcIN[9] * 7) +
	   ($cgcIN[11] * 6) +
	   ($cgcIN[12] * 5) +
	   ($cgcIN[13] * 4) +
	   ($cgcIN[14] * 3) +
	   ($cgcIN[16] * 2);
     $mod = $s2 % 11;
     $d2 = $mod < 2 ? 0 : 11 - $mod;

     if(($cgcIN[16] == $d1) && ($cgcIN[17] == $d2))
     return 1;
  }
 
  function cpfOK($cpf) {
/* entrada:  (formato: 999999999-99) FUTURO FAZER POLIMORFISMO*/
    if (strlen($cpfIN) <> 12) return 0;
    $s1 = ($cpfIN[0] * 10) +
          ($cpfIN[1] * 9) +
	  ($cpfIN[2] * 8) +
	  ($cpfIN[3] * 7) +
	  ($cpfIN[4] * 6) +
	  ($cpfIN[5] * 5) +
	  ($cpfIN[6] * 4) +
	  ($cpfIN[7] * 3) +
	  ($cpfIN[8] * 2);

   $mod = $s1 % 11;
   $d1 = $mod < 2 ? 0 : 11 - $mod;

   $s2 = ($cpfIN[0] * 11) +
         ($cpfIN[1]  * 10) +
	 ($cpfIN[2]  * 9) +
	 ($cpfIN[3]  * 8) +
	 ($cpfIN[4]  * 7) +
	 ($cpfIN[5]  * 6) +
	 ($cpfIN[6]  * 5) +
	 ($cpfIN[7]  * 4) +
	 ($cpfIN[8]  * 3) +
         ($cpfIN[10] * 2);

   $mod = $s2 % 11;
   $d2 = $mod < 2 ? 0 : 11 - $mod;

   return (($cpfIN[10] == $d1) && ($cpfIN[11] == $d2));

  }


  /**

  entarda de dados: (String Z999.00, int x = 1)
  $tipoM
  0 para nada
  1 para reais
  2 para dollar
  6 para euro
  
 */

	function extensive($valor, $tipoM = null){
		if (strpos($valor, ",") > 0) {
			$valor = str_replace(".", "", $valor);
			$valor = str_replace(",", ".", $valor);
		}

		$singular = null;
		$plural = null;

		if ($tipoM)	{
			$moeda_s = "";
			$moeda_p = "";

			if($tipoM == 1) {
				$moeda_s = "Real";
				$moeda_p = "Reais";
			} 
			if($tipoM == 2) {
				$moeda_s = "Dólar Norte-Americano";
				$moeda_p = "Dólares Norte-Americanos";
			} 
			if($tipoM == 6) {
				$moeda_s = "Euro";
				$moeda_p = "Euros";
			} 

			$singular = array("Centavo", $moeda_s, "Mil", "Milhão", "Bilhão", "Trilhão", "Quatrilhão");
			$plural = array("Centavos", $moeda_p, "Mil", "Milhões", "Bilhões", "Trilhões","Quatrilhões");
		}	else {
			$singular = array("", "", "Mil", "Milhão", "Bilhão", "Trilhão", "Quatrilhão");
			$plural = array("", "", "Mil", "Milhões", "Bilhões", "Trilhões","Quatrilhões");
		}

		$c = array("", "Cem", "Duzentos", "Trezentos", "Quatrocentos", "Quinhentos", "Seiscentos", "Setecentos", "Oitocentos", "Novecentos");
		$d = array("", "Dez", "Vinte", "Trinta", "Quarenta", "Cinquenta", "Sessenta", "Setenta", "Oitenta", "Noventa");
		$d10 = array("Dez", "Onze", "Doze", "Treze", "Quatorze", "Quinze", "Dezesseis", "Dezesete", "Dezoito", "Dezenove");
		$u = array("", "Um", "Dois", "Três", "Quatro", "Cinco", "Seis", "Sete", "Oito", "Nove");

		$z = 0;
		
		$valor = number_format( $valor, 2, ".", "." );
		$inteiro = explode( ".", $valor );

		for ( $i = 0; $i < count( $inteiro ); $i++ ) {
			for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ ) {
				$inteiro[$i] = "0" . $inteiro[$i];
			}
		}

		// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
		$rt = null;
		$fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);

		for ( $i = 0; $i < count( $inteiro ); $i++ ) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? "Cento" : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

			$r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
			$t = count( $inteiro ) - 1 - $i;

			$r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";

			if ( $valor == "000")
				$z++;
			elseif ( $z > 0 )
				$z--;

			if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
				$r .= ( ($z > 1) ? " de " : "") . $plural[$t];

			if ( $r )
				$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
		}

		$rt = mb_substr( $rt ?? '', 1 );
		
		return($rt ? trim( $rt ) : "Zero");
	}

  function extensive1($n, $tipoM = null){

    $tam = intval(strlen($n)); 

    if (substr_count($n, ".") <> 0){
      $ctv = substr($n, -2);

      switch($ctv) {
	      	case(10): $dz = "Dez";
				break;
			      case(11): $dz = "Onze";
				break;
			      case(12): $dz = "Doze";
				break;
			      case(13): $dz = "Treze";
				break;
			      case(14): $dz = "Catorze";
				break;
			      case(15): $dz = "Quinze";
				break;
			      case(16): $dz = "Dezesseis";
				break;
			      case(17): $dz = "Dezessete";
				break;
			      case(18): $dz = "Dezoito";
				break;
			      case(19): $dz = "Dezenove";
				break;
      }

      switch($ctv[0]){
			      case(0): $dz = "";
				break;
			      case(2): $dz = "Vinte";
				break;
			      case(3): $dz = "Trinta";
				break;
			      case(4): $dz = "Quarenta";
				break;
			      case(5): $dz = "Cinquenta";
				break;
			      case(6): $dz = "Sessenta";
				break;
			      case(7): $dz = "Setenta";
				break;
			      case(8): $dz = "Oitenta";
				break;
			      case(9): $dz = "Noventa";
				break;           
      }

      if($ctv[0] == "1") $un = "";
      else { 
				switch($ctv[1]) {           
					case(1): $un = "Um ";
					  break;
					case(2): $un = "Dois";
					  break;
					case(3): $un = "Três";
					  break;
					case(4): $un = "Quatro";
					  break;
					case(5): $un = "Cinco";
					  break;
					case(6): $un = "Seis";
					  break;
					case(7): $un = "Sete";
					  break;
					case(8): $un = "Oito";
					  break;
					case(9): $un = "Nove";
					  break;
					case(0): $un = "";
					  break;         
				}
      }

      if (($ctv[1] == "1") && ($ctv[0] == "0")) $tmp = "Centavo";
      else $tmp = " Centavos";
     
      if (($un != "") && ($dz != "")) $dz = $dz." e ";

      if ($ctv != "00") { 
				$ctv = " e ".$dz.$un.$tmp;
      } else   $ctv = "";
   
      $tam = intval(strlen($n)); 
      $tam = $tam - 3;

      $n = substr($n, 0, $tam);
     
    }

    $nback = $n;
    $tam = intval(strlen($n)); 
    $cont = 0;
    $aux  = -3; 
    $uni = "";
    $dez = "";
    $cem = "";

    if ($tam % 3 == 1) $n = "00".$n;
    if ($tam % 3 == 2) $n = "0".$n;

    $tam = intval(strlen($n)); 
    $vezes = 0;
	
    while($cont <> $tam){ 
    	$mil = "";
    	$text = "";
    	$moeda = "";

      $cdu = substr($n, $aux, 3); //recebe os tres primeiros		
      $c = substr(strval($cdu), -3, 1);        
		
			switch($c){
				case 0 : $cem = "";
				  break;
				case 1 : $cem = "Cem";
				  break;
				case 2 : $cem = "Duzentos";
				  break;
				case 3 : $cem = "Trezentos";
				  break;
				case 4 : $cem = "Quatrocentos";
				  break;
				case 5 : $cem = "Quinhentos";
				  break;
				case 6 : $cem = "Seiscentos";
				  break;
				case 7 : $cem = "Setecentos";
				  break;
				case 8 : $cem = "Oitocentos";
				  break;
				case 9 : $cem = "Novecentos";
				  break;
				case NULL: $cem = "";
				  break; 
			}
		
			$cm = substr(strval($cdu), -6, 1); 
			//print $cm;
			switch($cm){
					case 0 : $cem = "";
					  break;
					case 1 : 
					    if(substr(strval($cdu), -2, 2) == '00'){
						    $cem = "Cem";
						}else{ 
						    $cem = "Cento";		
						}
					  break;
					case 2 : $cem = "Duzentos";
					  break;
					case 3 : $cem = "Trezentos";
					  break;
					case 4 : $cem = "Quatrocentos";
					  break;
					case 5 : $cem = "Quinhentos";
					  break;
					case 6 : $cem = "Seiscentos";
					  break;
					case 7 : $cem = "Setecentos";
					  break;
					case 8 : $cem = "Oitocentos";
					  break;
					case 9 : $cem = "Novecentos";
					  break;
					case NULL: $cem = "";
					  break; 
			}
		


      $du   = substr($cdu, -2, 1);     
      if ($du == "1"){
        $du =  substr($cdu, -2);
        $dez = "";    
			  switch($du){
				  case(10): $uni = "Dez";
					break;
				  case(11): $uni = "Onze";
					break;
				  case(12): $uni = "Doze";
					break;
				  case(13): $uni = "Treze";
					break;
				  case(14): $uni = "Catorze";
					break;
				  case(15): $uni = "Quinze";
					break;
				  case(16): $uni = "Dezesseis";
					break;
				  case(17): $uni = "Dezessete";
					break;
				  case(18): $uni = "Dezoito";
					break;
				  case(19): $uni = "Dezenove";
					break;
			  }       
      } else{
        switch($du){
				  case(0): $dez = "";
					break;
				  case(2): $dez = "Vinte";
					break;
				  case(3): $dez = "Trinta";
					break;
				  case(4): $dez = "Quarenta";
					break;
				  case(5): $dez = "Cinquenta";
					break;
				  case(6): $dez = "Sessenta";
					break;
				  case(7): $dez = "Setenta";
					break;
				  case(8): $dez = "Oitenta";
					break;
				  case(9): $dez = "Noventa";
					break;
        }


	  		$u = substr($cdu, -1);    

        switch($u){
				  case(1): $uni = "Um";
			
					break;
				  case(2): $uni = "Dois";
					break;
				  case(3): $uni = "Três";
					break;
				  case(4): $uni = "Quatro";
					break;
				  case(5): $uni = "Cinco";
					break;
				  case(6): $uni = "Seis";
					break;
				  case(7): $uni = "Sete";
					break;
				  case(8): $uni = "Oito";
					break;
				  case(9): $uni = "Nove";
					break;
				  case(0): $uni = "";
					break;		            
			  }

			}

			if (($uni != "") && ($cem != "") && ($dez == "")) 
				$uni = " e ".$uni;
			if (($dez != "") && ($cem != "")) 
				$dez = " e ".$dez;		       
			if (($dez != "") && ($uni != "")) 
				$uni = " e ".$uni; 

		    
			if (($vezes == 1) && ($cdu / 100))
		    $mil = " Mil ";
			if (($vezes == 2) && ($cdu / 100) && ($cdu  % 100 == 1.0))
			  $mil = " Milhão, ";
			else if (($vezes == 2) && ($cdu / 100) && ($cdu % 100 > 1.0))
			  $mil = " Milhões, ";

			if (($vezes == 3) && ($cdu / 100) && ($cdu % 100 == 1.0))
			  $mil = " Bilhão, ";
			else if (($vezes == 3) && ($cdu / 100) && ($cdu % 100 > 1.0))
			  $mil = " Bilhões, ";

			if (($vezes == 4) && ($cdu / 100) && ($cdu % 100 == 1.0)) 
		    $mil = ", Trilhão, ";
			else if (($vezes == 4) && ($cdu /  100) && ($cdu %  100 > 1.0))
		    $mil = " Trilhões, ";     



			if(($tipoM == 1) && ($nback != 1)) $moeda = " Reais";
			if(($tipoM == 1) && ($nback == 1)) $moeda = " Real";

			if(($tipoM == 2) && ($nback != 1)) $moeda = " Dólares Norte-Americanos";
			if(($tipoM == 2) && ($nback == 1)) $moeda = " Dólar Norte-Americano";

			if(($tipoM == 6) && ($nback != 1)) $moeda = " Euros";
			if(($tipoM == 6) && ($nback == 1)) $moeda = " Euro";

			if(($tipoM == 0) && ($nback != 1)) $moeda = "";
			if(($tipoM == 0) && ($nback == 1)) $moeda = "";

			$text = $cem.$dez.$uni.$mil.$text;
		  
			$cont =  $cont + 3;
			$aux  =  $aux - 3; 
			$vezes++;
    }
 
    $text = $text.$moeda.$ctv;
    return $text;

  }
  
  
function porcentagem($n){
	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
"sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
"dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
"sete", "oito", "nove");

    $z = 0;
    $inteiro = explode(".", $n);
    
    $rt = '';
    if ($inteiro[0] == "0"){
      $rt .= "zero ponto ";
    }

    $singular = array("", "", "Mil", "Milhão", "Bilhão", "Trilhão", "Quatrilhão");
	$plural = array("", "", "Mil", "Milhões", "Bilhões", "Trilhões","Quatrilhões");


	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

	// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
	
	for ($i=0;$i<count($inteiro);$i++) {
		$valor = $inteiro[$i];

		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor >= 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		
		$t = count($inteiro)-1-$i;

		$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		
		if ($valor == "000"){
			$z++;
		} elseif ($z > 0) {
			$z--;	
		}

		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
		

		if ($r) {
			$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " ponto ") : " ") . $r;
		}
	}

//	return($rt ? $rt : "zero");
    return $rt . "por cento";
}

}
?>