<?php

    function dataextenso1($data) {
        $data = explode("-",$data);
        $dia = $data[2];
        $mes = $data[1];
        $ano = $data[0];

        switch ($mes){
            case 1: $mes = "Janeiro"; break;
            case 2: $mes = "Fevereiro"; break;
            case 3: $mes = "Março"; break;
            case 4: $mes = "Abril"; break;
            case 5: $mes = "Maio"; break;
            case 6: $mes = "Junho"; break;
            case 7: $mes = "Julho"; break;
            case 8: $mes = "Agosto"; break;
            case 9: $mes = "Setembro"; break;
            case 10: $mes = "Outubro"; break;
            case 11: $mes = "Novembro"; break;
            case 12: $mes = "Dezembro"; break;
        }

        $mes=strtolower($mes);

        return  ("$dia de $mes de $ano");
    }

    // coloca da no formato Extenso
    function dataconvert($dt){
        // leitura das datas
        $dia     = date('d');
        $mes     = date('m');
        $ano     = date('Y');
        $semana  = date('w');
        
        // configuração mes
        switch ($mes){
            case 1: $mes = "Janeiro"; break;
            case 2: $mes = "Fevereiro"; break;
            case 3: $mes = "Março"; break;
            case 4: $mes = "Abril"; break;
            case 5: $mes = "Maio"; break;
            case 6: $mes = "Junho"; break;
            case 7: $mes = "Julho"; break;
            case 8: $mes = "Agosto"; break;
            case 9: $mes = "Setembro"; break;
            case 10: $mes = "Outubro"; break;
            case 11: $mes = "Novembro"; break;
            case 12: $mes = "Dezembro"; break;
        }

        // configuração semana
        switch ($semana) {
            case 0: $semana = "Domingo"; break;
            case 1: $semana = "Segunda Feira"; break;
            case 2: $semana = "Terça Feira"; break;
            case 3: $semana = "Quarta Feira"; break;
            case 4: $semana = "Quinta Feira"; break;
            case 5: $semana = "Sexta Feira"; break;
            case 6: $semana = "Sábado"; break;
        }
        
        $data =  $dia.' de '. $mes. ' de '. $ano; 
        return $data; 
    }

    if(! function_exists('Convert_Data')){
        function Convert_Data($data){
            if (strstr($data, "/")){//verifica se tem a barra /
                $d = explode ("/", $data);//tira a barra
                $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mês etc...
                return $invert_data;
            }elseif(strstr($data, "-")){
                $d = explode ("-", $data);
                $invert_data = "$d[2]/$d[1]/$d[0]";
                return $invert_data;
            }
        }
    }

    function data_string($d){
        $meses = array("Janeiro", 
                        "Fevereiro",
                        "Março", 
                        "Abril", 
                        "Maio", 
                        "Junho", 
                        "Julho", 
                        "Agosto", 
                        "Setembro", 
                        "Outubro", 
                        "Novembro", 
                        "Dezembro");

        list($dia, $mes, $ano) = explode('/', $d);

        $mes = $meses[$mes - 1];

        return "$dia de $mes de $ano";
    }

    function converte($valor, $conversao){
        return $valor * $conversao;
    }

    function getStartDate($d, $n, $c = 0){
        if(preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@", $d, $v)){
            return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
        }else if(preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})@", $d, $v)){
            return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
        }
    }

    //-----------------------------------------------------------------
    // converte uma data de '31/12/2002' para '2002-12-31 00:00:00.000'
    function dmy2ymd($d){
        if(preg_match("@([0-9]{2})/([0-9]{2})/([0-9]{4})@", $d, $v)){
            return "$v[3]-$v[2]-$v[1] 00:00:00.000";
        }

        return "";
    }

    if(! function_exists('mkdate')){
        function mkdate ($a, $m, $d) {
            return date ("Y-m-d", mktime (0, 0, 0, $m, $d, $a));
        }
    }

    // devolve a data correspondente ao dia 15 de 3 meses após a data fornecida
    function conserta($d){
        if(preg_match("@([0-9]{4})-([0-9]{2})@", $d, $v)){
            $ano = $v[1];

            if($v[2] >= 10){
                $ano++;
            }

            $mes = (($v[2] + 3) % 13) + ((int) ($v[2] / 10));
            return "$ano-$mes-15 00:00:00.000";
        }

        return '';
    }

    // verifica as renovacoes continuas
    function verifica_continua($db, $idInform){
        $x = odbc_exec($db, "select idAnt, startValidity from Inform where id= '".$idInform."'");

        if(odbc_fetch_row($x)){
            $idAnt = odbc_result($x, 1);
            $start = odbc_result($x, 2);
            $sql = "select id, idCountry, c_Coface_Imp from Importer where idInform= '".$idAnt."'"; 
            $x = odbc_exec($db, $sql);
                     
            while(odbc_fetch_row($x)){
                        $idImporterAnt = odbc_result($x, 1);
                        $idCountry = odbc_result($x, 2);
                        $ciCoface = odbc_result($x, 3);
                        $y = odbc_fetch_row($db, "select id from Importer where idInform=$idInform and c_Coface_Imp=$ciCoface and idCountry=$idCountry and id <> $idImporterAnt");

                        if(odbc_fetch_row($y)){
                                $idImporter = odbc_result($y, 1);
                                $y = odbc_exec($db, "update Importer set validityDate='$start' where id=$idImporter");
                        }
                    }
            }
    }

    // converte a data de yyyy-mm-dd para dd/mm/yyyy
    if(! function_exists('ymd2dmy')){
        function ymd2dmy($d){

        if(preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d, $v)){
            return "$v[3]/$v[2]/$v[1]";
            }

            return $d;
        }
    }

    function SomarData($data, $Operacao, $dias, $meses, $ano){
        //passe a data no formato dd/mm/yyyy 
        $data = explode("/", $data);

        if($Operacao == '+'){
            $newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[0] + $dias, $data[2] + $ano));
        }else{
            $newData = date("d/m/Y", mktime(0, 0, 0, $data[1] - $meses, $data[0] - $dias, $data[2] - $ano)); 
        }
       
        return $newData;
     }

    function getTimeStamp($date){
        if(preg_match('@^([0-9]{4})-([0-9]{2})-([0-9]{2})@', $date, $res)){
            return mktime(0, 0, 0, $res[2], $res[3], $res[1]);
        }
    }

    function faltam($fim, $dias, $emission){
        $secs = ($dias - 1) * 24 * 3600;

        return getTimeStamp($fim) - getTimeStamp($emission) <= $secs;
    }

    function arruma_cnpj($c){
        If(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
            return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
        }
        
        return $c;
    }

    function somarDiaApolice1( $data, $dias){
        $dia  = substr($data, 0, 2);
        $mes  = substr($data, 3, 3);
        $ano  = substr($data, 6, 10);
        $dataFinal = mktime(24*$dias, 0, 0, $mes, $dia, $ano);
        $dataFormatada = date('d/m/Y',$dataFinal);
        
        return $dataFormatada;
    }
   
    function dataconvertap(){
        // leitura das datas
        $dia     = date('d');
        $mes     = date('m');
        $ano     = date('Y');
        $semana  = date('w');
        
        // configuração mes
        switch ($mes){
            case 1: $mes = "Janeiro"; break;
            case 2: $mes = "Fevereiro"; break;
            case 3: $mes = "Março"; break;
            case 4: $mes = "Abril"; break;
            case 5: $mes = "Maio"; break;
            case 6: $mes = "Junho"; break;
            case 7: $mes = "Julho"; break;
            case 8: $mes = "Agosto"; break;
            case 9: $mes = "Setembro"; break;
            case 10: $mes = "Outubro"; break;
            case 11: $mes = "Novembro"; break;
            case 12: $mes = "Dezembro"; break;
        }

        // configuração semana
        switch ($semana) {
            case 0: $semana = "Domingo"; break;
            case 1: $semana = "Segunda Feira"; break;
            case 2: $semana = "Terça Feira"; break;
            case 3: $semana = "Quarta Feira"; break;
            case 4: $semana = "Quinta Feira"; break;
            case 5: $semana = "Sexta Feira"; break;
            case 6: $semana = "Sábado"; break;
        }
        
      $data =  $dia.' de '. $mes. ' de '. $ano; 
      return $data; 
    }
    
    function somarDiaApolice2( $data, $dias){
        $dia  = substr($data, 0, 2);
        $mes  = substr($data, 3, 3);
        $ano  = substr($data, 6, 10);
        $dataFinal = mktime(24*$dias, 0, 0, $mes, $dia, $ano);
        $dataFormatada = date('d/m/Y',$dataFinal);
    
        return $dataFormatada;
    }
    
    function Convert_Data_Pt_En($data){
        if (strstr($data, "/")){//verifica se tem a barra /
        $d = explode ("/", $data);//tira a barra
        $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mês etc...
            return $invert_data;
        }elseif(strstr($data, "-")){
        $d = explode ("-", $data);
        $invert_data = "$d[2]/$d[1]/$d[0]";    
        return $invert_data;
        }  
    } 

    function formataValorSql($formataValorSql){
        $formataValorSql = str_replace('.','',$formataValorSql);
        $formataValorSql = str_replace(',','.',$formataValorSql);
        return $formataValorSql;
    }

    function valor_extenso($valor=0, $maiusculas=false){
        $singular = array("Centavo", "Real", "Mil", "Milhão", "Bilhão", "Trilhão", "Quatrilhão"); 
        $plural = array("Centavos", "Reais", "Mil", "Milhões", "Bilhões", "Trilhões", "Quatrilhões"); 

        $c = array("", "Cem", "Duzentos", "Trezentos", "Quatrocentos", "Quinhentos", "Seiscentos", "Setecentos", "Oitocentos", "Novecentos"); 
        $d = array("", "Dez", "Vinte", "Trinta", "Quarenta", "Cinquenta", "Sessenta", "Setenta", "Oitenta", "Noventa"); 
        $d10 = array("Dez", "Onze", "Doze", "Treze", "Quatorze", "Quinze", "Dezesseis", "Dezessete", "Dezoito", "Dezenove"); 
        $u = array("", "Um", "Dois", "Três", "Quatro", "Cinco", "Seis", "Sete", "Oito", "Nove"); 

        $z = 0; 
        $rt = "";

        $valor = number_format($valor, 2, ".", "."); 
        $inteiro = explode(".", $valor); 
        for($i=0;$i<count($inteiro);$i++) 
        for($ii=strlen($inteiro[$i]);$ii<3;$ii++) 
        $inteiro[$i] = "0".$inteiro[$i]; 

        $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2); 
        for ($i=0;$i<count($inteiro);$i++) { 
          $valor = $inteiro[$i]; 
          $rc = (($valor > 100) && ($valor < 200)) ? "Cento" : $c[$valor[0]]; 
          $rd = ($valor[1] < 2) ? "" : $d[$valor[1]]; 
          $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : ""; 

          $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && 
          $ru) ? " e " : "").$ru; 
          $t = count($inteiro)-1-$i; 
          $r .= $r ? " ".($valor > 1 ? '' : '') : ""; 
          if ($valor == "000")$z++; elseif ($z > 0) $z--; 
          if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
          if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && 
          ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r; 
        } 

        if(!$maiusculas){ 
          return($rt ? $rt : "Zero"); 
        } else { 

        if ($rt) $rt=ereg_replace(" E "," e ",ucwords($rt));
          return (($rt) ? ($rt) : "Zero"); 
        } 
    }

    function formata($numero) {
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

?>