<?php



  function valor_extenso($valor=0, $maiusculas=false){
    $singular = array("Centavo", "Real", "Mil", "Milh�o", "Bilh�o", "Trilh�o", "Quatrilh�o"); 
    $plural = array("Centavos", "Reais", "Mil", "Milh�es", "Bilh�es", "Trilh�es", "Quatrilh�es"); 

    $c = array("", "Cem", "Duzentos", "Trezentos", "Quatrocentos", "Quinhentos", "Seiscentos", "Setecentos", "Oitocentos", "Novecentos"); 
    $d = array("", "Dez", "Vinte", "Trinta", "Quarenta", "Cinquenta", "Sessenta", "Setenta", "Oitenta", "Noventa"); 
    $d10 = array("Dez", "Onze", "Doze", "Treze", "Quatorze", "Quinze", "Dezesseis", "Dezessete", "Dezoito", "Dezenove"); 
    $u = array("", "Um", "Dois", "Tr�s", "Quatro", "Cinco", "Seis", "Sete", "Oito", "Nove"); 

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

  require_once("../rolePrefix.php");
  require_once("../../../config.php");
  require_once("../../pdfConf.php");
  require_once("../../../gerar_pdf/MPDF45/mpdf.php");

  include_once("../consultaCoface.php");

  



  $idInform        = $_REQUEST['idInform'];
  $idAgencia       = $_REQUEST['idAgencia'];
  $agencia         = $_REQUEST['agencia'];
  $idBanco         = $_REQUEST['idBanco'];
  $idCDBB          = $_REQUEST['idCDBB'];
  $idCDOB          = $_REQUEST['idCDOB'];
  $idCDParc        = $_REQUEST['idCDParc'];
  $tipoBanco       = $_REQUEST['tipoBanco'];
  $total           = isset($_REQUEST['total']) ? $_REQUEST['total'] : 0;
  $totalR          = isset($_REQUEST['totalR']) ? $_REQUEST['totalR'] : 0;
  $comm            = $_REQUEST['comm'];
  $novalue         = isset($_REQUEST['novalue']) ? $_REQUEST['novalue'] : '';
  
    $sqlEmp  = "SELECT Nome, CNPJ,  Endereco, Complemento, CEP, Cidade, Estado, Cod_Area, Telefone, Bairro, Fax, HomePage
    FROM Empresa 
        WHERE i_Empresa = 1";  
   
    $resEmp = odbc_exec($db,$sqlEmp);
    $dadosEmp = odbc_fetch_array($resEmp);

    $compEmp = $dadosEmp['Complemento'] ? ' - '.$dadosEmp['Complemento'] : '';
    $cepEmp = $dadosEmp['CEP'] ? ' - Cep: '.$dadosEmp['CEP'] : '';
    $cidEmp = $dadosEmp['Cidade'] ? ' - '.$dadosEmp['Cidade'] : '';
    $estEmp = $dadosEmp['Estado'] ? ', '.$dadosEmp['Estado'] : '';
    $telEmp = $dadosEmp['Telefone'] ? ' Tel.: '.$dadosEmp['Telefone'] : '';
    $faxEmp = $dadosEmp['Fax'] ? ' Fax: '.$dadosEmp['Fax'] : '';
    $enderecoEmp = $dadosEmp['Endereco'].$compEmp.$cepEmp.$cidEmp.$estEmp.$telEmp.$faxEmp;
    $siteEmp = $dadosEmp['HomePage'];
    $nomeEmp = $dadosEmp['Nome'];
    

    $sqlquery = "SELECT E.*, P.Nome AS Produto, Inf.n_Apolice, P.c_Susep AS susep_real
             FROM Inform Inf
             INNER JOIN Produto P ON P.i_Produto = Inf.i_Produto
             INNER JOIN Empresa_Produto EP ON EP.i_Produto = P.i_Produto
             INNER JOIN Empresa E ON E.i_Empresa = EP.i_Empresa
             WHERE Inf.id = ?";

$res = odbc_prepare($db, $sqlquery);
odbc_execute($res, [$idInform]);

    $dados = odbc_fetch_array($res);
    odbc_free_result($res);
    $apoNum   = $dados['n_Apolice'];
  
    $retorno_rodape = $dados['Endereco'].' - '.
           $dados['Complemento'].' - '.
           'CEP '.formata_string('CEP',$dados['CEP']).' - '.
           $dados['Cidade'].', '.
           $dados['Estado']. ' '.
           'Tel.: '.$dados['Telefone'].'  '.
           'Fax: ' . $dados['Fax'].'  '.
           'Home Page: '. $dados['HomePage'];
        
    $disclame_retorno =  "*Seguro garantido pela ".$dados['Nome']." (uma empresa Coface)<br>"
                          .' CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: 15414.005218/2005-89, 15414.004768/2004-08';
  
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
      }
      
      return $d;
  }
  
  function arruma_cnpj($c){
      if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
      return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
      }
      
      return $c;
  }
  
  function Convert_Data($data){
    if (strstr($data, "/")){//verifica se tem a barra /
      $d = explode ("/", $data);//tira a barra
        $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = m�s etc...
      return $invert_data;
    }elseif(strstr($data, "-")){
        $d = explode ("-", $data);
        $invert_data = "$d[2]/$d[1]/$d[0]";    
        return $invert_data;
    }  
   }
   
  function dataconvert($dt){
    // leitura das datas
    $dia     = date('d');
    $mes     = date('m');
    $ano     = date('Y');
    $semana  = date('w');
    
    
    // configura��o mes
    
    switch ($mes){
      case 1: $mes = "Janeiro"; break;
      case 2: $mes = "Fevereiro"; break;
      case 3: $mes = "Mar�o"; break;
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
  
    // configura��o semana
    
    switch ($semana) {
      case 0: $semana = "Domingo"; break;
      case 1: $semana = "Segunda Feira"; break;
      case 2: $semana = "Ter�a Feira"; break;
      case 3: $semana = "Quarta Feira"; break;
      case 4: $semana = "Quinta Feira"; break;
      case 5: $semana = "Sexta Feira"; break;
      case 6: $semana = "S�bado"; break;
    }
    
      $data =  $dia.' de '. $mes. ' de '. $ano; 
      return $data; 
  }
      
  if(!$novalue){
    if (!isset($_REQUEST['gera_notif_false'])) {
      require_once('geraNotif.php');
    }
  }

  include_once("consultasCondEsp.php");

  $sql = "SELECT Ga, startValidity, n_Apolice, currency FROM Inform WHERE id = ?";
  $y = odbc_prepare($db, $sql);
  odbc_execute($y, [$idInform]);
  
  //odbc_fetch_row($y);
  $ga = odbc_result($y, "Ga");
  $ini_vigencia = odbc_result($y, "startValidity");
  $apoNum   = odbc_result($y,'n_Apolice');
    $moeda   = odbc_result($y,'currency');
    
    if ($moeda == "2") {
     $ext = "US$ ";
     $DescMoeda = "D�LARES NORTE-AMERICANOS";
     $extmoeda = "d�lar norte-americano (US$)";
     $fMoeda = "2";
  }else if ($moeda == "6") {
     $ext = "� ";
     $DescMoeda = "EUROS";
     $extmoeda = "euro (�)";
     $fMoeda = "6";
    }else if($moeda == "1"){
     $ext = "R$ ";
     $DescMoeda = "REAIS";
     $extmoeda = "reais R$ ";
     $fMoeda = "1";
  }
  odbc_free_result($y);
  $query = "SELECT t_Vencimento, Num_Parcelas, numParc, CONVERT(varchar, DataPrimeiraParcela, 103) AS DataPrimeiraParcela FROM Inform WHERE id = ?";
$cury = odbc_prepare($db, $query);
odbc_execute($cury, [$_REQUEST["idInform"]]);

  $t_Vento   = odbc_result($cury, "t_Vencimento");
  
  if($t_Vento == 1){
    $t_Vencimento  = 30; // dias
  }else if($t_Vento == 2){
    $t_Vencimento  = 60; // dias
  }else if($t_Vento == 3){
    $t_Vencimento  = 90; // dias
  }else if($t_Vento == 4){
    $t_Vencimento  = 180; // dias
  }
  
  if($t_Vento == 1){
    $t_DescVencimento = 'mensais';
  }else if ($t_Vento == 2){
    $t_DescVencimento = 'bimestrais';
  }else if ($t_Vento == 3){
    $t_DescVencimento = 'trimestrais';
  }else if ($t_Vento == 4){
    $t_DescVencimento = 'semestrais';
  }
  
  $numParc  = odbc_result($cury,'numParc');
  
  $Num_Parcelas   = odbc_result($cury,'Num_Parcelas');
  
  if ($numParc == 1 || $Num_Parcelas ==1){
    $txtParcs = "� vista";
    $periodo = "� vista";
    $periodo1 = "� vista";
  }else if ($numParc == 2 || $Num_Parcelas == 2){
    $txtParcs = "em duas presta��es: 1 e mais 1 em noventa dias";
    $periodo = "trimestral";
    $periodo1 = "trimestrais";
  }else if ($numParc == 4 || $Num_Parcelas == 4){
    $txtParcs = "em 4 parcela(s) iguais e trimestral(is)";
    $periodo = "trimestral";
    $periodo1 = "trimestrais";
  }else if ($numParc == 7 || $Num_Parcelas == 7){
    $txtParcs = "em 7 parcela(s) iguais e mensal(is)";
    $periodo = "mensal";
    $periodo1 = "mensais";
  }else if ($numParc == 10 || $Num_Parcelas == 10){
    $txtParcs = "em 10 parcela(s) iguais e mensal(is)";
    $periodo = "mensal";
    $periodo1 = "mensais";
  }
    
  $primeirovencimento   = odbc_result($cury, "DataPrimeiraParcela");
  $num_parcelas = ($Num_Parcelas ? $Num_Parcelas : $numParc);
  odbc_free_result($cury);
  //die("?".$num_parcelas);

  if ($ga=="0" || $ga==""){ //Apolice Antiga

      $dt_ini = explode("[ ]", $ini_vigencia);

      if ($dt_ini[0] < "2006-01-20") {
        $susep = "15.414004350/97-75";
        $cp    = "CP/RC/03-01";
      }else{
        $susep = "15.414005218/2005-89";
        $cp    = "CP/RC/06-01";
      }

      //$list = new Java("java.util.ArrayList");

      if($tipoBanco == 3){
        $sql = "SELECT dateIniVig, status FROM CDOB WHERE id = ?";
        $x = odbc_prepare($db, $sql);
        odbc_execute($x, [$idCDOB]);
        $start  = ymd2dmy(odbc_result($x, 1));
        $status = odbc_result($x, 2);
    
        $query = "SELECT imp.name, imp.address, imp.city, c.name, imp.c_Coface_Imp
                  FROM Importer imp
                  JOIN Country c ON imp.idCountry = c.id
                  JOIN CDOBDetails cdd ON cdd.idImporter = imp.id
                  WHERE cdd.idCDOB = ?
                  ORDER BY imp.name";
        $x = odbc_prepare($db, $query);
        odbc_execute($x, [$idCDOB]);
    
    } else if($tipoBanco == 1){ // Banco do Brasil
        $sql = "SELECT dateIniVig, status FROM CDBB WHERE id = ?";
        $x = odbc_prepare($db, $sql);
        odbc_execute($x, [$idCDBB]);
        $start = ymd2dmy(odbc_result($x, 1));
        $status = odbc_result($x, 2);
    
        $query = "SELECT imp.name, imp.address, imp.city, c.name, imp.c_Coface_Imp
                  FROM Importer imp
                  JOIN Country c ON imp.idCountry = c.id
                  JOIN CDBBDetails cdd ON cdd.idImporter = imp.id
                  WHERE cdd.idCDBB = ?
                  ORDER BY imp.name";
        $x = odbc_prepare($db, $query);
        odbc_execute($x, [$idCDBB]);
    
    } else { // Banco Parceiros
        $sql = "SELECT dateIniVig, status FROM CDParc WHERE id = ?";
        $x = odbc_prepare($db, $sql);
        odbc_execute($x, [$idCDParc]);
        $start  = ymd2dmy(odbc_result($x, 1));
        $status = odbc_result($x, 2);
    
        $query = "SELECT imp.name, imp.address, imp.city, c.name, imp.c_Coface_Imp
                  FROM Importer imp
                  JOIN Country c ON imp.idCountry = c.id
                  JOIN CDParcDetails cdd ON cdd.idImporter = imp.id
                  WHERE cdd.idCDParc = ?
                  ORDER BY imp.name";
        $x = odbc_prepare($db, $query);
        odbc_execute($x, [$idCDParc]);
    }
    
    while(odbc_fetch_row($x)){
        strtoupper(trim(odbc_result($x, 1)));
        strtoupper(trim(odbc_result($x, 2)));
        strtoupper(trim(odbc_result($x, 3)));
        strtoupper(trim(odbc_result($x, 4)));
        trim(odbc_result($x, 5));
    }
    odbc_free_result($x);
    

      $key = session_id(). time();
      
      $sql = "SELECT i.name, i.address, i.city, CAST(r.name AS varchar(2)), i.cnpj, i.ie, i.dateEmissionP, i.i_Seg,
        i.endValidity, i.percCoverage, i.prodUnit, i.startValidity, CAST(GETDATE() - i.startValidity AS int),
        i.nProp, i.currency, i.addressNumber, i.n_Apolice, periodMaxCred
        FROM Inform i
        JOIN Region r ON i.idRegion = r.id
        WHERE i.id = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);

          
      $segurado = trim(odbc_result($x, 1));
      $addressNumber = odbc_result($x,17);
      $address = odbc_result($x, 2);
      $periodMaxCred = (odbc_result($x, 'periodMaxCred') ? odbc_result($x, 'periodMaxCred') : 180); 
      $extMax   = $numberExtensive->extensive($periodMaxCred, 3);
      $periodMaxCred  = $periodMaxCred. ' ('.$extMax.')';
      $cidade_estado = odbc_result($x, 3). " - ". odbc_result($x, 4);
      $cnpj = arruma_cnpj(odbc_result($x, 5));
      $ie = odbc_result($x, 6);
      $ie = preg_match("/^[0-9]+$/", $ie) ? number_format($ie, 0, '', '.') : $ie;
      $emissao = ymd2dmy(odbc_result($x, 7));
      $iSeg = odbc_result($x, 8);
      $fim = ymd2dmy(odbc_result($x, 9));
      $inivig = ymd2dmy(odbc_result ($x, 12));
      $cobertura = (int) odbc_result($x, 10);
      $cobertura .= "% (". $numberExtensive->extensive($cobertura, 3). " por cento) ";
      $prod = odbc_result($x, 11);
      $nProp = odbc_result($x, "nProp");
      $currency = odbc_result($x, "currency");

      odbc_free_result($x);
      
      $sql = "SELECT n_Apolice FROM Inform WHERE id = ?";
      $x = odbc_prepare($db, $sql);
      odbc_execute($x, [$idInform]);
            
      if(odbc_fetch_row($x)){
        $apolice = sprintf("062%06d", odbc_result($x, 1));
      }
      
      $dbSisSeg = isset($dbSisSeg) ? $dbSisSeg : false;
      
      odbc_free_result($x);

      if($dbSisSeg){
    $strSQL = "SELECT MAX(dbo.Base_Calculo.n_Apolice)
               FROM dbo.Base_Calculo
               INNER JOIN dbo.Apolice ON dbo.Base_Calculo.n_Sucursal = dbo.Apolice.n_Sucursal
               AND dbo.Base_Calculo.i_Seg = dbo.Apolice.i_Seg
               WHERE dbo.Base_Calculo.n_Prop = ?
               AND dbo.Base_Calculo.n_Moeda = ?
               AND dbo.Base_Calculo.i_Seg = ?";

    $y = odbc_prepare($dbSisSeg, $strSQL);
    odbc_execute($y, [$nProp, $currency, $iSeg]);

    if(odbc_fetch_row($y)){
        // $apolice = sprintf("062%06d", odbc_result($y, 1));
        if($prod != 62){
            $apolice .= "/$prod";
        }
    }
}

    odbc_free_result($y);    

    if($tipoBanco == 3){
      $q = "SELECT c.name, c.endereco, c.cidade, r.description, c.cnpj, c.ie
            FROM CDOB c
            JOIN Region r ON c.idRegion = r.id
            WHERE c.id = ?";
      $x = odbc_prepare($db, $q);
      odbc_execute($x, [$idCDOB]);
  
      $bb_nome = odbc_result($x, 1);
      $bb_address = odbc_result($x, 2);
      $bb_ce = odbc_result($x, 3) . " - " . odbc_result($x, 4);
      $bb_cnpj = arruma_cnpj(odbc_result($x, 5));
      $bb_ie = odbc_result($x, 6);
      $bb_ie = preg_match("/^[0-9]+$/", $bb_ie) ? number_format($bb_ie, 0, '', '.') : $bb_ie;
  
  } else if($tipoBanco == 1){
      $sql = "SELECT name, endereco, cidade, uf, cnpj, ie, idBanco
              FROM Agencia
              WHERE id = ?";
      $x = odbc_prepare($db, $sql);
      odbc_execute($x, [$idAgencia]);
  
      $bb_nome = odbc_result($x, 1);
      $bb_address = odbc_result($x, 2);
      $bb_ce = odbc_result($x, 3) . " - " . odbc_result($x, 4);
      $bb_cnpj = arruma_cnpj(odbc_result($x, 5));
      $bb_ie = odbc_result($x, 6);
      $bb_ie = preg_match("/^[0-9]+$/", $bb_ie) ? number_format($bb_ie, 0, '', '.') : $bb_ie;
      $idBanco = odbc_result($x, 7);
  
  } else {
      $sql = "SELECT name, endereco, cidade, uf, cnpj, ie, idBanco
              FROM Agencia
              WHERE id = ?";
      $x = odbc_prepare($db, $sql);
      odbc_execute($x, [$idAgencia]);
  
      $bb_nome = odbc_result($x, 1);
      $bb_address = odbc_result($x, 2);
      $bb_ce = odbc_result($x, 3) . " - " . odbc_result($x, 4);
      $bb_cnpj = arruma_cnpj(odbc_result($x, 5));
      $bb_ie = odbc_result($x, 6);
      $bb_ie = preg_match("/^[0-9]+$/", $bb_ie) ? number_format($bb_ie, 0, '', '.') : $bb_ie;
      $idBanco = odbc_result($x, 7);
  }

  odbc_free_result($x);
  
  $sql = "SELECT name, tipo FROM Banco WHERE id = ?";
  $x = odbc_prepare($db, $sql);
  odbc_execute($x, [$idBanco]);

  $nome_banco = strtoupper(odbc_result($x, 1));
   $tipo = odbc_result($x, 2);
   $tira12 = 0;
   if ($tipo == 2 || $tipo == 3) {
     $parceiro = 1;
     if ($tipo == 3) {
       $tira12 = 1; // tirar a clausula 12
     }
   }else{
     $parceiro = 0;
   }

   odbc_free_result($x);

   $sql = "SELECT YEAR(MAX(dateClient)) FROM CDBB WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
$anoBB = odbc_result($x, 1);
odbc_free_result($x);

$sql = "SELECT YEAR(MAX(dateClient)) FROM CDOB WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
$anoOB = odbc_result($x, 1);
odbc_free_result($x);

$sql = "SELECT YEAR(MAX(dateClient)) FROM CDParc WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
$anoParc = odbc_result($x, 1);
odbc_free_result($x);

$sql = "SELECT a502, b1202, d602, d701, nivel_d602, p_cobertura_d701, limite_d701,
               a801, b603, b1504, b1202, c102, d101, e101, f305, f3301
        FROM ModuloOferta
        WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);

   $mod_a801 = odbc_result($x,8);
   $mod_a502 = odbc_result($x,1);
   $mod_b603 = odbc_result($x,9);
   $mod_b1504 = odbc_result($x,10);
   $mod_b1202 = odbc_result($x,11);
   $mod_c102 = odbc_result($x,12);
   $mod_d101 = odbc_result($x,13);
   $mod_d602 = odbc_result($x,3);
   $mod_d701 = odbc_result($x,4);
   $mod_e101 = odbc_result($x,14);
   $mod_f305 = odbc_result($x,15);
   $mod_f3301 = odbc_result($x,16);

   if(odbc_result($x, 1) == "1") {
      $a502 = "\nA5.02 Cobertura de Risco de Produ��o;";
   }
   if(odbc_result($x, 2) == "1") {
      $b1202 = "B12.02 Extens�o do Contrato a uma ou mais Empresas;";
   }
   if(odbc_result($x, 3) == "1") {
      $d602  = "\nD6.02 Pequenos Sinistros;";
   }
   if(odbc_result($x, 4) == "1") {
      $d701  = "D7.01 Lit�gio;";
   }

odbc_free_result($x);

$sql = "SELECT perPart0, perPart1, pLucro, perBonus FROM Inform WHERE id = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
// Ex: $perPart0 = odbc_result($x, "perPart0");

   if( (odbc_result($x, 1) != "") or (odbc_result($x, 2) != "") ) {
      $perBonus  =  odbc_result($x, 4);
      $f902 = "F9.02 - B�nus por Aus�ncia de Sinistros\nvinculado a renova��o da ap�lice por mais um per�odo de seguro.\n" .
               "Percentual do b�nus por aus�ncia de sinistros: ". $perBonus."%\n"; //10%(dez por cento).
   }
   if(odbc_result($x, 3) == "F13") {
      $f1302  = "F13.2 - Ao Termino de cada Per�odo de Seguro.;";
      $f902 = "";
   }
   if(odbc_result($x, 3) == "F14") {
      $f1402  = "F14.2 - Caso a presente ap�lice se mantenha vigente durante 2 (dois) Per�odos de Seguro.;";
      $f902 = "";
   }
   if(odbc_result($x, 3) == "F15") {
      $f1502  = "F15.2 - Caso a presente ap�lice se mantenha vigente durante 3 (tr�s) Per�odos de Seguro.;";
      $f902 = "";
   }

odbc_free_result($x);

$sql = "SELECT nu_banco FROM ParModEsp WHERE idInform = ?";
$cur3 = odbc_prepare($db, $sql);
odbc_execute($cur3, [$idInform]);
// Ex: $nu_banco = odbc_result($cur3, 1);

   $tp_bancp = '';
   if (odbc_fetch_row($cur3)) {
      $tp_banco = odbc_result($cur3, 'nu_banco');
   }
   odbc_free_result($cur3);

   if( ($tp_banco=="0") || ($tp_banco=="") ) {
      $ano = max($anoBB, $anoOB, $anoParc);
      $ano = date ('Y');
    /*
    
    
      $h = new Java("java.util.HashMap");
      $h->put('key', $pdfDir. $key. "CondEspBB.pdf");
      $h->put('dir', $pdfDir);
      $h->put('apolice', $apolice. '');
      if(!$novalue){
        $h->put('codigo', "$codigo/$ano");
      }
      $h->put('segurado', $segurado. '');
      $h->put('endereco', $address. '');
      $h->put('num', $addressNumber. '');
      $h->put('cidade_estado', $cidade_estado);
      $h->put('cnpj', $cnpj. '');
      $h->put('inscricao_estadual', $ie. '');
      $h->put('data_emissao', $emissao. '');
      $h->put('list', $list);
      $h->put('hoje', date('d/m/Y'));
      $h->put('start', $start. '');
      $h->put('cobertura', $cobertura. '');
      $h->put('fim_vigencia', $fim. '');
      $h->put('agencia', strtoupper($agencia));
      $h->put('nome_banco', $nome_banco. '');
      $h->put('bb_nome', $bb_nome. '');
      $h->put('bb_endereco', $bb_address. '');
      $h->put('bb_cidade_estado', $bb_ce. '');
      $h->put('bb_cnpj', $bb_cnpj. '');
      $h->put('bb_inscricao_estadual', $bb_ie. '');
      $h->put('parceiro', $parceiro. '');
      $h->put('tira12', $tira12. '');
      $h->put('susep', $susep. '');
      $h->put('cp', $cp.'');

      $h->put('mod_a801',$mod_a801."");
      $h->put('mod_a502',$mod_a502."");
      $h->put('mod_b603',$mod_b603."");
      $h->put('mod_b1504',$mod_b1504."");
      $h->put('mod_b1202',$mod_b1202."");
      $h->put('mod_c102',$mod_c102."");
      $h->put('mod_d101',$mod_d101."");
      $h->put('mod_d602',$mod_d602."");
      $h->put('mod_d701',$mod_d701."");
      $h->put('mod_e101',$mod_e101."");
      $h->put('mod_f305',$mod_f305."");
      $h->put('mod_f3301',$mod_f3301."");
      $h->put('jurosMora',$jurosMora."");

      // chama a classe CondEsp (role/client/CondEsp.java)
      if($novalue){
        $pdf = new Java("CondEsp", $h, 1);
      }else{
        $pdf = new Java("CondEsp", $h);
      }
    
    */
    
    
    
    
   } else {
    
      $ano = max($anoBB, $anoOB, $anoParc);
      $ano = date ('Y');
    /*
      $h = new Java("java.util.HashMap");
      $h->put('key', $pdfDir. $key. "CondEspBB.pdf");
      $h->put('dir', $pdfDir);
      $h->put('apolice', $apolice. '');
      if(!$novalue){
        $h->put('codigo', "$codigo/$ano");
      }
      $h->put('segurado', $segurado. '');
      $h->put('endereco', $address. '');
      $h->put('num', $addressNumber. '');
      $h->put('cidade_estado', $cidade_estado);
      $h->put('cnpj', $cnpj. '');
      $h->put('inscricao_estadual', $ie. '');
      $h->put('data_emissao', $emissao. '');
      $h->put('list', $list);
      $h->put('hoje', date('d/m/Y'));
      $h->put('start', $start. '');
      $h->put('cobertura', $cobertura. '');
      $h->put('fim_vigencia', $fim. '');
      $h->put('agencia', strtoupper($agencia));
      $h->put('nome_banco', $nome_banco. '');
      $h->put('bb_nome', $bb_nome. '');
      $h->put('bb_endereco', $bb_address. '');
      $h->put('bb_cidade_estado', $bb_ce. '');
      $h->put('bb_cnpj', $bb_cnpj. '');
      $h->put('bb_inscricao_estadual', $bb_ie. '');
      $h->put('parceiro', $parceiro. '');
      $h->put('tira12', $tira12. '');
      $h->put('susep', $susep. '');
      $h->put('cp', $cp.'');

      $h->put('a502', $a502.'');
      $h->put('b1202', $b1202.'');
      $h->put('d602', $d602.'');
      $h->put('d701', $d701.'');
      $h->put('f902', $f902.'');
      $h->put('f1302', $f1302.'');
      $h->put('f1402', $f1402.'');
      $h->put('f1502', $f1502.'');

      $h->put('mod_a801',$mod_a801."");
      $h->put('mod_a502',$mod_a502."");
      $h->put('mod_b603',$mod_b603."");
      $h->put('mod_b1504',$mod_b1504."");
      $h->put('mod_b1202',$mod_b1202."");
      $h->put('mod_c102',$mod_c102."");
      $h->put('mod_d101',$mod_d101."");
      $h->put('mod_d602',$mod_d602."");
      $h->put('mod_d701',$mod_d701."");
      $h->put('mod_e101',$mod_e101."");
      $h->put('mod_f305',$mod_f305."");
      $h->put('mod_f3301',$mod_f3301."");
      $h->put('jurosMora',$jurosMora."");

      // chama a classe CondEsp (role/client/CondEsp.java)
      if($tipoBanco==1) {
         if($novalue){
           $pdf = new Java("CondEspBB", $h, 1);
         }else{
           $pdf = new Java("CondEspBB", $h);
         }
      } else {
         if($novalue){
           $pdf = new Java("CondEsp", $h, 1);
         }else{
           $pdf = new Java("CondEsp", $h);
         }
      }
    */
    
  //$loc = $root. 'download/'.$key.'CondEspBB.pdf';
  //$pdf->generate();
  //echo "<HTML><HEAD><META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"><TITLE></TITLE></HEAD></html>";
    }

  
  
  //Exibe  o relat�rio  de acordo com a consulta

    $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4',
        'margin_left' => 20,
        'margin_right' => 15,
        'margin_top' => 42,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
        ];
    
    $mpdf=new  \Mpdf\Mpdf($opt);
    //$mpdf=new mPDF('win-1252','A4','','');
    $html = ob_get_clean();
    // $mpdf->useOnlyCoreFonts = true;    // false is default
    //$mpdf->SetProtection(array('print')); // prote��o de arquivo
    $mpdf->SetTitle("Carta de esclarecimentos");
    $mpdf->SetAuthor($nomeEmp);
    
    if (isset($_REQUEST['rascunho'])) {
      $mpdf->SetWatermarkText("RASCUNHO"); // fundo marca d�gua
    } else {
      $mpdf->SetWatermarkText(""); // fundo marca d�gua
    }
    
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->SetDisplayMode('fullpage');
    
    // Endere�o do logotipo
    $logo  = $root .'../../images/logo_pdf.jpg';
    //$logo_mini  = $root .'../../images/logo_mini.jpg';
    $logo_mini  = $root .'../../images/logo_pdf.jpg';
    $assinatura  = $root .'images/Assinatura Fernando.gif';
    
    
    $datahoje =  '';
    


       // In�cio do arquivo montando primeiro o CSS
   
        $html = '<html>
        <head>
        <style>
        body {font-family: Arial, Helvetica, sans-serif;
          font-size: 12pt;
        }
        p {    margin: 0pt;
        }
          
        
        ol {counter-reset: item; font-weight:bold; }
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify}
                li:before {content: counters(item, "."); counter-increment: item; }
        
        ul      {list-style-type: none; font-weight:normal } 
        ul li   {padding: 3px 0px;color: #000000;text-align:justify} 

                
        
        #cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
        #sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
        #disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
        #img1{
          width: 100px;
          height: 30px;
          background:url('.$logo.') no-repeat;

        }';
      
      
        
          for($i=2;$i <=100;$i++){
            
          $html .='
                  #img'.$i.'{
                    width: 100px;
                    height: 27px;
                    background:url('.$logo_mini.') no-repeat;
                  }';
          
            
            
          }
        
        
        $html .='
        </style>
        
        </head>
        <body>
        <htmlpageheader name="myheader">
   
                <!--mpdf
          <htmlpageheader name="myheader">
           <div style="width:100%">
            <div id="img{PAGENO}"></div>

          </div>
           <br>
           <br>
           <div style="text-align: center;">
              <span style="font-weight: bold; font-size: 12pt;">CONDI��ES ESPECIAIS DA AP�LICE N.� 06200'.$Num_Apolice.'
              <br>ALTERA��O DE CL�USULAS BENEFICI�RIA N.� '.$Cod_Clausula.'</span>
            </div>
            <br>  
            <br>
           
      
          </htmlpageheader>
          
          <htmlpagefooter name="myfooter">
             <table width="100%" border="0">
               <tr>
                 <td width="22%">&nbsp;</td>
                 <td width="56%" style="text-align:center; font-size: 8pt;">                  
                    
                    '.$enderecoEmp.'<br>
                    '.$siteEmp.'<br>

                    P�gina {PAGENO} de {nb}
                   
                </td>
                <td width="22%">&nbsp;</td>
              </tr>
              </table>
            
          </htmlpagefooter>
          
          <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
          <sethtmlpagefooter name="myfooter" value="on" />
          mpdf-->
          <div style="clear:both">&nbsp;</div>';  
          
          
          
          
          $html .= '
          <table width="100%" border="0" style="font-size: 12pt;">
            <tr>
              <td width="25%"><b>SEGURADORA:</b></td>
              <td width="75%"><b>'.strtoupper($Nome_Empresa).'</b></td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">'.$Endereco_Empresa.' / '.$Complemento_Empresa.' - '.$Bairro_Empresa.'</td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">'.$Cidade_Empresa.' - '.$UF_Empresa.'</td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td>CNPJ: '.formata_string('CNPJ', $CPNJ_Empresa).'</td>
            </tr>            
          </table>

          <div style="clear:both">&nbsp;</div>

          <table width="100%" border="0" style="font-size: 12pt;">
            <tr>
              <td width="25%"><b>SEGURADO:</b></td>
              <td width="75%"><b>'.strtoupper(trim($Nome_Segurado)).'</b></td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">'.$Endereco_Segurado.'</td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">'.$Cidade_Segurado.' - '.$UF_Segurado.'</td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">CNPJ: '.formata_string('CNPJ', $CNPJ_Segurado).'</td>
            </tr>            
          </table>
          
          <div style="clear:both">&nbsp;</div>

          <table width="100%" border="0" style="font-size: 12pt;">
            <tr>
              <td width="25%"><b>BENEFICI�RIO:</b></td>
              <td width="75%"><b>'.strtoupper(trim($Nome_Banco)).' - '.$Nome_Agencia.'</b></td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">'.$Endereco_Agencia.'</td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">'.$Cidade_Agencia.' - '.$UF_Agencia.'</td>
            </tr>
            <tr>
              <td width="25%">&nbsp;</td>
              <td width="75%">CNPJ: '.formata_string('CNPJ', $CNPJ_Agencia).'</td>
            </tr>            
          </table>
                  
          <div style="clear:both">&nbsp;</div>
                   CL�USULA 1� - OBJETO -
                     <br> 
               <br> 
                    <div id="cobtexto">
                As disposi��es destas CONDI��ES ESPECIAIS ser�o aplic�veis a todas as opera��es
                de exporta��o em que o SEGURADO haja obtido financiamento � exporta��o atrav�s do
                PROGRAMA DE FINANCIAMENTO �S EXPORTA��ES - PROEX, de ADIANTAMENTO
                SOBRE CAMBIAIS ENTREGUES - ACE e DESCONTO DE CAMBIAIS DE
                EXPORTA��O e prevalecer�o sobre o que
                estiver disposto nas CONDI��ES GERAIS e PARTICULARES da ap�lice, em rela��o aos
                direitos e obriga��es aqui tratados.
                  </div>
                   <br> 
              CL�USULA 2� - CESS�O E TRANSFER�NCIA DO DIREITO �S INDENIZA��ES -
              <br> 
              <br> 
                <div id="cobtexto">
                Exclusivamente para os casos de opera��es de exporta��o realizadas para os
                <b>importadores</b> elencados na Cl�usula 3�, abaixo, e financiadas atrav�s do PROEX,
                realizadas atrav�s de ACE, atrav�s de Desconto de Cambiais de Exporta��o, 
                concedido pelo BENEFICI�RIO, o SEGURADO cede e
                transfere ao BENEFICI�RIO, em car�ter irrevog�vel e irretrat�vel, o direito a todas as
                indeniza��es resultantes da ap�lice n� 123456789/012, emitida em '.$start.', pela
                SEGURADORA. Para efeito desta disposi��o ser�o observadas, na data do embarque
                internacional das mercadorias vendidas ou na data do faturamento dos servi�os prestados,
                as respectivas coberturas e limites contratados na referida ap�lice, em especial no que se
                refere aos limites de cr�dito rotativos atribu�dos aos <b>importadores</b> e �s eventuais
                condi��es vinculativas estabelecidas nos limites de cr�dito de acordo com a Cl�usula 1
                item 1.2.3 (ii) das Condi��es Gerais da <b>ap�lice</b>. As condi��es vinculativas aos limites de
                cr�dito correspondem a informa��es complementares relacionadas �quele limite de
                cr�dito, s�o exig�ncias estabelecidas pela SEGURADORA quando da concess�o do limite
                de cr�dito e s�o obrigat�rias, devendo ser atendidas quando da ocorr�ncia de um sinistro.
                Quaisquer das condi��es vinculativas relativas aos limites de cr�dito estar�o relacionadas
                nas Fichas de Aprova��o de Limites de Cr�dito de cada SEGURADO, dispon�veis no site
                da SEGURADORA http://www.coface.com.br/siga/index.php no item, Ficha de Aprova��o de Limites de
                Cr�dito.
              </div>
               <br> 
              CL�USULA 3� - <b>IMPORTADORES ABRANGIDOS -</b>
               <br> <br> 
              <div id="cobtexto">
                A cess�o referida na Cl�usula anterior somente produzir� efeitos em rela��o �s
                indeniza��es resultantes das opera��es de exporta��o destinadas aos seguintes
                importadores:
              </div>
               <br>';
               
               // $xx = odbc_exec($db, $query);
               $ix = 1;
               
               /*while(odbc_fetch_row($xx)){   
                 
                $html .='<div><b>3.'.$ix.'. '. strtoupper(trim(odbc_result($xx, 1))).'</b></div>'; // nome
                $html .='<div>&nbsp;&nbsp;&nbsp;&nbsp;'. strtoupper(trim(odbc_result($xx, 2))).'</div>'; // endereco
                $html .='<div>&nbsp;&nbsp;&nbsp;&nbsp;'. strtoupper(trim(odbc_result($xx, 3))).'</div>'; // cidade
                $html .='<div>&nbsp;&nbsp;&nbsp;&nbsp;'. strtoupper(trim(odbc_result($xx, 4))).'</div>'; // pais
                $html .='<div>&nbsp;&nbsp;&nbsp;&nbsp;'. trim(odbc_result($xx, 5)).'</div><br>';             // ci cliente
                $ix++;
              } */

            for ($i=0; $i < count($dados_comp); $i++) { 
              $html .='
              <table width="100%" border="0" style="font-size: 12pt;">
                <tr>
                  <td width="10%"><b>3.1.'.($i+1).'</b></td>
                  <td><b>'.strtoupper($dados_comp[$i]['Nome_Comprador']).'</b></td>
                </tr>
                <tr>
                  <td width="10%">&nbsp;</td>
                  <td><b>'.$dados_comp[$i]['Endereco_Comprador'].'</b></td>
                </tr>
                <tr>
                  <td width="10%">&nbsp;</td>
                  <td><b>'.$dados_comp[$i]['Pais_Comprador'].'</b></td>
                </tr>
                <tr>
                  <td width="10%">&nbsp;</td>
                  <td><b>N.� SBCE: '.$dados_comp[$i]['Cod_Comprador'].'</b></td>
                </tr>            
              </table>';
            }
               
              $html .= '<br>';
               // Aqui ser� for�ado o pulo de p�gina 
               
               
            $html .='CL�USULA 4� - DISPOSI��ES GERAIS -
                   <div style="clear-both">&nbsp;</div> 
                 <div id="cobtexto">
                    4.1. A SEGURADORA aceita a presente cess�o e transfer�ncia de direito �s indeniza��es
                e se compromete a pagar ao BENEFICI�RIO, nos termos estabelecidos nestas
                CONDI��ES ESPECIAIS, as indeniza��es devidas em raz�o da ap�lice.
                <BR><BR>
                4.2. O BENEFICI�RIO declara ter conhecimento das CONDI��ES GERAIS,
                CONDI��ES PARTICULARES, CONDI��ES ESPECIAIS e M�DULOS da ap�lice,
                reconhecendo, ainda, expressamente, que n�o poder� se valer de mais direitos que
                aqueles de que for titular o SEGURADO, previstos na referida ap�lice, especialmente em
                rela��o � <i>Percentagem Segurada</i> dos riscos referentes � cobertura b�sica, pelo que a
                SEGURADORA poder� opor ao SEGURADO e ao BENEFICI�RIO qualquer exce��o ou
                defesa que lhe caiba no �mbito da ap�lice.
                <BR><BR>
                4.3. Em rela��o �s opera��es de exporta��o financiadas atrav�s do PROEX, realizadas
                atrav�s de ACE, realizadas atrav�s de Desconto de Cambiais de Exporta��o, a garantia 
                complementar constitu�da pelo SEGURADO, referente � percentagem do risco n�o coberto 
                pela SEGURADORA, n�o ser� computada como cr�dito no c�lculo da conta de perdas, estabelecida 
                pela SEGURADORA, de acordo com a Cl�usula 14 - Defini��es - <B>D�vida L�quida</B> das 
                Condi��es Gerais da Ap�lice.
                <BR><BR>
                4.4. A SEGURADORA obriga-se a comunicar ao BENEFICI�RIO, no prazo de 10 (dez)
                dias �teis, o descumprimento pelo SEGURADO de qualquer das obriga��es estipuladas
                na ap�lice, bem como qualquer outro fato que a possa desobrigar de efetuar o pagamento
                da respectiva indeniza��o.
                <BR><BR>
                4.5. Na hip�tese do n�o cumprimento de qualquer das obriga��es estipuladas na ap�lice,
                a SEGURADORA compromete-se a conceder ao BENEFICI�RIO o prazo m�ximo de 10
                (dez) dias �teis para a solu��o do fato comunicado, contados a partir da ci�ncia pelo
                BENEFICI�RIO, obrigando-se ainda a n�o rescindir a ap�lice durante este per�odo.
                Todavia, em n�o sendo sanado, at� o final desse prazo, o fato levado ao conhecimento do
                BENEFICI�RIO, a ap�lice ser� considerada rescindida retroativamente � data em que o
                referido fato houver se verificado.
                <BR><BR>
                4.6. O SEGURADO declara expressamente estar ciente e aceitar que o BENEFICI�RIO
                poder� exercer o seu direito de regresso em rela��o ao valor financiado que a
                SEGURADORA recusar-se a pagar em raz�o da falta ou descumprimento, total ou parcial,
                das obriga��es contratuais pelo SEGURADO, ou em caso de suspens�o do direito �
                indeniza��o em raz�o de <b>lit�gio</b>, acrescido dos encargos legais e contratuais respectivos.
                <BR><BR>
                4.7 A SEGURADORA tem o dever de informar ao <b>BENEFICI�RIO</b> do direito �s
                <b>indeniza��es</b> qualquer viola��o do SEGURADO em rela��o �s obriga��es assumidas em
                decorr�ncia da <b>ap�lice</b>. A transfer�ncia do direito �s <b>indeniza��es</b> n�o tem por efeito
                isentar o SEGURADO de qualquer das obriga��es estipuladas pela <b>ap�lice</b>, de forma que
                as obriga��es previstas neste contrato permanecer�o inalteradas.
                <BR><BR>
                4.8 De acordo com o item '.$Item_48.' do M�dulo de Risco '.$Mod_48.' - Limites de Cr�dito previsto 
                nas Condi��es Particulares da Ap�lice, a SEGURADORA reserva-se o direito de recusar, reduzir 
                ou cancelar qualquer limite de cr�dito a qualquer tempo. Estas decis�es tornar-se-�o efetivas 
                para expedi��es ou presta��es de servi�os efetuados, a partir da data em que o SEGURADO receba 
                a notifica��o pela SEGURADORA, permanecendo em pleno vigor o seguro sobre as opera��es j� 
                realizadas at� o momento da redu��o ou do cancelamento do limite de cr�dito.
                <BR><BR>
                4.9 De acordo com o item 2 do M�dulo Recupera��es E1.02, as <b>recupera��es</b> recebidas
                antes do pagamento pela SEGURADORA de uma <b>indeniza��o</b> ser�o imputadas ao
                pagamento das Faturas Comerciais n�o pagas pela sua respectiva ordem cronol�gica de
                emiss�o. Caso exista mais de uma fatura com a mesma data de emiss�o, a <b>recupera��o</b>
                ser� imputada pela ordem cronol�gica do vencimento original.
                <BR><BR>
                44.10 Caso se verifique uma das situa��es previstas na Cl�usula 7 das Condi��es Gerais
                da <b>Ap�lice</b>, e a SEGURADORA decida rescindir a ap�lice, as coberturas que j�
                produziram efeitos, permanecer�o v�lidas, desde que seja efetuado o pagamento imediato
                do montante dos <b>pr�mios e taxas de an�lise e monitoramento cadastral</b> devidos a
                t�tulo de tais coberturas.
                </div><br>';
            
            
            
            
                $html .='
                     CL�USULA 5� - PREFER�NCIA DO BENEFICI�RIO NO RECEBIMENTO DE INDENIZA��ES -
                <div style="clear-both">&nbsp;</div> 
                 <div id="cobtexto">O BENEFICI�RIO possuir� prefer�ncia no recebimento de indeniza��es, caso o valor total
                dos CR�DITOS sinistrados do SEGURADO e do BENEFICI�RIO seja superior ao limite
                de cr�dito do <b>importador</b> que deu causa ao sinistro.
                </div><br>
                CL�USULA 6� - INFORMA��ES AO BENEFICI�RIO -
                <div style="clear-both">&nbsp;</div> 
                 <div id="cobtexto">O SEGURADO autoriza e a SEGURADORA se compromete a informar ao
                BENEFICI�RIO, no prazo de 7 (sete) dias, mediante solicita��o deste, o valor dos limites
                de cr�dito vigentes, referentes aos <b>importadores</b> enumerados na Cl�usula 3�, supra.
                </div><br>
                CL�USULA 7� - MODIFICA��O DAS CONDI��ES DA AP�LICE -
                <div style="clear-both">&nbsp;</div> 
                  <div id="cobtexto">Quaisquer instrumentos que venham a ser celebrados entre o SEGURADO e a
                SEGURADORA, que impliquem modifica��o das condi��es da ap�lice em vigor,
                violentando os direitos previstos nestas CONDI��ES ESPECIAIS, dever�o receber
                aprova��o pr�via do BENEFICI�RIO. Ficam excepcionados os documentos emitidos pela
                SEGURADORA relativamente � redu��o ou � rescis�o dos limites de cr�dito (avisos de
                redu��o ou rescis�o dos limites de cr�dito). Em qualquer dos casos ficam ressalvados os
                direitos adquiridos antes da entrada em vigor dos mencionados instrumentos, de acordo com o item 
                '.$Item_48.' do M�dulo de Risco '.$Mod_48.' - Limites de Cr�dito previsto nas Condi��es 
                Particulares da Ap�lice.
                O <i>Prazo M�ximo de Cr�dito</i> encontra-se estabelecido nas Condi��es Particulares da
                Ap�lice. <u>A cobertura aplica-se �s vendas do SEGURADO com prazo final de
                pagamento que n�o exceda o <i>Prazo M�ximo de Cr�dito</i>.</u>
                </div><br>
                
                CL�USULA 8� - AMEA�A DE SINISTRO -
                <div style="clear-both">&nbsp;</div> 
                  <div id="cobtexto">8.1 A SEGURADORA e o SEGURADO aceitam que a <b>notifica��o</b> de <b>amea�a de sinistro</b>
                prevista nas CONDI��ES GERAIS seja formulada pelo SEGURADO ou pelo
                BENEFICI�RIO, observados os prazos para tanto estabelecidos, e mediante o envio dos
                documentos solicitados pela SEGURADORA.
                8.2 O <u>prazo m�ximo</u> para a  <b>notifica��o</b> de <b>amea�a de sinistro</b> encontra-se estabelecido
                nas Condi��es Particulares da Ap�lice - Prazo para apresentar a  
                <b>notifica��o</b> de <b>amea�a de sinistro</b>.
                8.3 De acordo com a Cl�usula 3 das Condi��es Gerais da <b>Ap�lice</b>, item 3.2, os
                <u>documentos obrigat�rios</u> para o pagamento da <b>indeniza��o</b> s�o: Fatura Comercial,
                Conhecimento de Embarque e Registro de Exporta��o Averbado.
                8.4 De acordo com a Cl�usula 3 das Condi��es Gerais da Ap�lice, item 3.3, o prazo para
                o pagamento da indeniza��o ser� de, no m�ximo, 30 (trinta) dias ap�s o transcurso dos
                prazos previstos no M�dulo A8.01 - Risco de <b>N�o Pagamento</b>.
                8.5 A Devolu��o da <b>Indeniza��o<b> paga que trata a Cl�usula 3 das Condi��es Gerais da
                Ap�lice item 3.6, somente poder� ser imputada ao SEGURADO da <b>ap�lice</b>, e nunca ao
                BENEFICI�RIO.
                </div><br>
                
                CL�USULA 9� - OBRIGA��ES DO SEGURADO -
                <div style="clear-both">&nbsp;</div> 
                  <div id="cobtexto">O SEGURADO expressamente reconhece e aceita que o estipulado nestas CONDI��ES
                ESPECIAIS n�o tem o efeito de desobrig�-lo de quaisquer obriga��es contra�das em
                virtude da ap�lice.<br>
                De acordo com a Cl�usula 10 das Condi��es Gerais da <b>ap�lice</b>:
                <u>N�o ser� permitido que o SEGURADO, na vig�ncia da <b>ap�lice</b>, obtenha outro seguro
                sobre os mesmos riscos, total ou parcialmente cobertos.</u>
                Em caso de descumprimento, o SEGURADO dever� reembolsar a SEGURADORA da
                indeniza��o paga indevidamente ao BENEFICI�RIO, sob pena de cancelamento da
                <b>ap�lice</b>, de acordo com a Cl�usula 9 itens 9.2 e 9.4 das Condi��es Gerais.
                </div><br>
                
                CL�USULA 10� - PRORROGA��O DE VENCIMENTO -
                <div style="clear-both">&nbsp;</div> 
                  <div id="cobtexto">O SEGURADO somente poder� prorrogar qualquer vencimento na medida em que a
                dura��o total do cr�dito concedido ao <b>importador</b>, ap�s a prorroga��o, n�o ultrapasse o
                <i>Prazo M�ximo de Cr�dito</i> de acordo com as Condi��es Particulares da <b>Ap�lice</b>.
                Tal prorroga��o somente poder� ser concedida mediante anu�ncia expressa do
                BENEFICI�RIO, notifica��o a SEGURADORA, e ainda estar� limitada ao prazo total de
                '.$Prazo_Max_Cred.' ('.trim(strtolower(valor_extenso($Prazo_Max_Cred))).') dias contados a partir da data do embarque internacional das
                mercadorias vendidas ou da data do faturamento dos servi�os prestados e ao prazo
                m�ximo de pagamento definido pelo PROEX.
                </div><br>
                
                CL�USULA 11� - VIG�NCIA -
                <div style="clear-both">&nbsp;</div> 
                  <div id="cobtexto">As disposi��es constantes destas CONDI��ES ESPECIAIS aplicam-se �s opera��es de
                exporta��o efetuadas durante o per�odo compreendido entre '.$Data_Inicio_Vigencia.' e '.$Data_Fim_Vigencia.'
                , salvo disposi��o em contr�rio e por escrito firmada pela SEGURADORA, pelo
                SEGURADO e pelo BENEFICI�RIO.
                </div><br>
                
                CL�USULA 12� - AUTORIZA��O -
                <div style="clear-both">&nbsp;</div> 
                  <div id="cobtexto">O SEGURADO autoriza expressamente a SEGURADORA a conceder acesso ao
                BENEFICI�RIO �s informa��es constantes na �rea restrita da <i>web page</i> da
                SEGURADORA que sejam relativas ao cumprimento das obriga��es contratuais do
                SEGURADO no �mbito da ap�lice, aos valores declarados nas declara��es de volume de
                exporta��es, bem como a quaisquer informa��es relacionadas aos <b>importadores</b>
                elencados na Ap�lice, sobretudo, seus respectivos limites de cr�dito.
                </div><br>
                
                CL�USULA 13� - M�DULOS
                <div style="clear-both">&nbsp;</div> 
                  <div id="cobtexto">Os seguintes M�dulos fazem parte integrante da ap�lice:</div><br>
                              
          <table width="100%" border="0" style="font-size: 12pt;"> ';
                
                if($mod_a801 == 1 || $mod_a502 == 1){
                  $html .= '<tr>
                         <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">M�DULOS FATOS GERADORES DE SINISTRO</div></td>                    
                       </tr>';
                }
                 if($mod_a502 == 1){
                   $html .= '<tr>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="1" width="8%" style="font-weight:bold">A5.02</td>
                           <td  colspan="1" width="76%" style="font-weight:bold">COBERTURA DE RISCO DE PRODU��O</td>                    
                       </tr>';  
                 }
            
                 if ($mod_a801 == 1){
                   $html .= '
                         <tr>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="1" width="8%" style="font-weight:bold">A08.01</td>
                           <td  colspan="1" width="76%" style="font-weight:bold">RISCO DE N�O PAGAMENTO</td>
                         </tr>';     
                  
                 }
            
            
                if ($mod_b1504 == 1 || $mod_b1202 == 1 ){
                 $html .= '   <tr>
                         <td colspan="4">&nbsp;</td>
                         </tr>
                         <tr>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">M�DULOS DE RISCO</div></td>
                       </tr>';
               
            
                 if($mod_b1202 == 1){
                  $html .= ' 
                        <tr>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="1" width="8%">&nbsp;</td>
                           <td  colspan="1" width="8%" style="font-weight:bold">B12.02</td>
                           <td  colspan="1" width="76%" style="font-weight:bold">EXTENS�O DA AP�LICE A UMA OU MAIS EMPRESAS</td>
                         </tr> ';
                  
                    
                    
                       $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = ? ORDER BY no_razao_social";
                        $cury = odbc_prepare($db, $query);
                        odbc_execute($cury, array($idInform));

                      
                       
                      if ($cury){ 
                         $html .= '                  
                            <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="2" width="84%">                         
                             <table>';
                             
                            while (odbc_fetch_row($cury)){        
                              $no_razao_social       = odbc_result ($cury, 'no_razao_social');
                              $nu_cnpj               = odbc_result ($cury, 'nu_cnpj');
                              $nu_inscricao_estadual = odbc_result ($cury, 'nu_inscricao_estadual');
                              $ds_endereco           = odbc_result ($cury, 'ds_endereco');
                              $nu_endereco           = odbc_result ($cury, 'nu_endereco');
                              $ds_complemento        = odbc_result ($cury, 'ds_complemento');
                              $nu_cep                = odbc_result ($cury, 'nu_cep');
                              $no_cidade             = odbc_result ($cury, 'no_cidade');
                              $no_estado             = odbc_result ($cury, 'no_estado');
                              $i ++;
                              $empre .= '<tr><td colspan="2"><b>'.$no_razao_social.'</b></td></tr>
                                     <tr><td><b>CNPJ   : </b>'.$nu_cnpj.'</td><td><b>IE:</b> '.$nu_inscricao_estadual.'</td></tr>
                                     <tr><td colspan="2"><b>Endere�o: </b>'.$ds_endereco.', '.$nu_endereco. ($ds_complemento != "" ? " - ".$ds_complemento : "").'</td></tr>
                                     <tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td></b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
                              
                            }

                            odbc_free_result($cury);
                            
                              $html .= $empre.'</table>
                             </td>
                           </tr>';
                      }
            
            
                  }
            
                  if ($mod_b1504 == 1){
                     $html .= '<tr>
                         <td colspan="4">&nbsp;</td>
                        </tr>
                         
                          <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%" style="font-weight:bold">B15.04</td>
                             <td  colspan="1" width="76%" style="font-weight:bold">LIMITES DE CR�DITO</td>
                         </tr> 
                     
                     ';
                  
                   }
                }
            
             
                if ($mod_c102 == 1){
                 $html .= '    <tr>
                         <td colspan="4">&nbsp;</td>
                         </tr>
                         
                         <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">M�DULOS DE COBRAN�A:</div></td>                        
                         </tr> 
                         <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%" style="font-weight:bold">C01.02</td>
                             <td  colspan="1" width="76%" style="font-weight:bold">SERVI�OS DE COBRAN�A INTEGRAL</td>
                         </tr>';
                
            
                }
            
                if ($mod_d101 == 1 || $mod_d602 == 1 || $mod_d701 == 1 || $mod_b603 == 1){
                 $html .= '   <tr>
                         <td colspan="4">&nbsp;</td>
                         </tr>
                         <tr>
                         <td  colspan="1" width="8%">&nbsp;</td>
                         <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">M�DULOS DE INDENIZA��O</div></td>                        
                      </tr>';
                 
                 if($mod_d101 == 1){
                  $html .= '<tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%" style="font-weight:bold">D01.01</td>
                             <td  colspan="1" width="76%" style="font-weight:bold">LIMITE M�NIMO PARA NOTIFICA��O DE AMEA�A DE SINITRO</td>
                       </tr>
                       <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="2" width="84%">O limite m�nimo para <b>notifica��o</b> de <b> amea�a de sinistro � de</b>'.$extnas.'</td>
                       </tr>';
               
                 }
            
                 if ($mod_d602 == 1){
                  $html .= '<tr>
                         <td colspan="4">&nbsp;</td>
                         </tr>
                         <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%" style="font-weight:bold">D06.02</td>
                             <td  colspan="1" width="76%" style="font-weight:bold">PEQUENOS SINISTROS</td>
                       </tr>
                       <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="2" width="84%">'.$d602.'</td>
                       </tr>
                    ';
                   
            
                 }
            
                 if($mod_b603 == 1){
                  $html .= '<tr>
                         <td colspan="4">&nbsp;</td>
                         </tr>
                         <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%" style="font-weight:bold">B06.03</td>
                             <td  colspan="1" width="76%" style="font-weight:bold">PEDIDOS PENDENTES</td>
                        </tr>
                       ';
                   
                 }
                 if ($mod_d701 == 1){
                  $html .= '<tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%" style="font-weight:bold">D07.01</td>
                             <td  colspan="1" width="76%" style="font-weight:bold">LIT�GIO</td>
                       </tr>
                       <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="2" width="84%">'.$d701.'</td>
                       </tr>
                  
                   ';
                   
            
                 }
            
                }
            
                
                if ($mod_e101 == 1) {
                   $html .= '<tr>
                         <td colspan="4">&nbsp;</td>
                         </tr>
                         <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">M�DULO DE RECUPERA��O</div></td>
                             
                       </tr>
                       <tr>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%">&nbsp;</td>
                             <td  colspan="1" width="8%" style="font-weight:bold">E1.02</td>
                             <td  colspan="1" width="76%" style="font-weight:bold">M�DULO DE RECUPERA��O</td>
                       </tr>';
                  
                  
                }
    
         if ($mod_f305 == 1 ) {
           $html .= '<tr>
                   <td colspan="4">&nbsp;</td>
               </tr>
               <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">M�DULOS DE FATURAMENTO</div></td>
                     
               </tr>
               <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%" style="font-weight:bold">F3.05</td>
                   <td  colspan="1" width="76%" style="font-weight:bold">PAGAMENTO DO PR�MIO E DECLARA��ES DE VOLUME DE</td>
               </tr>
               <tr>
                 <td  colspan="1" width="8%">&nbsp;</td>
                 <td  colspan="1" width="8%">&nbsp;</td>
                 <td colspan="1" width="8%">&nbsp;</td>
                 <td colspan="1" width="76%" style="font-weight:bold">EXPORTA��ES</td>
               </tr>
              ';
          
            if ($numParc == 1){
             $html .= ' <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="2" width="84%">
                    <div id="cobtexto">O per�odo de declara��o � '.$periodo.'<br>
                    A forma de declara��o � volume total de neg�cios aberto por n�mero 
                    de fatura comercial, importador e valor.<br>
                    O pr�mio m�nimo ser� pago em 01 parcela � vista.</div></td>
                    
                   </tr>';
                     
            
            }else{
             $html .= '<tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="2" width="84%">
                   <div id="cobtexto">O per�odo de declara��o � '.$periodo.'.<br>
                    A forma de declara��o � volume total de neg�cios aberto por n�mero 
                    de fatura comercial, importador e valor.<br>
                    O pr�mio m�nimo ser� pago em '.$numParc.' parcelas iguais e '.$periodo1.'</div>
                   </td>
                   </tr>';
    
            
    
            }
        }
    
          $bonus = isset($bonus) ? $bonus : '';
           if($bonus != ""){ // F.09 B�NUS POR AUS�NCIA DE SINISTROS
             $html .= '<tr>
                  <td colspan="4">&nbsp;</td>
                  </tr>
                         
                  <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%" style="font-weight:bold">F9.02</td>
                   <td  colspan="1" width="76%" style="font-weight:bold">B�NUS POR AUS�NCIA DE SINISTROS</td>
                 </tr>
                 <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="2" width="84%">'.$bonus.'</td>
                 </tr>';
           }
          
          $partic = isset($partic) ? $partic : '';
          if ($partic != ''){
            $html .= ' <tr>
                  <td colspan="4">&nbsp;</td>
                  </tr>
                 <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="2" width="84%" style="font-weight:bold">'.$partic.'</td>
                 </tr>
                 <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="2" width="84%">A percentagem referente ao item a deste m�dulo � de '.$partDeducao.'
                       A percentagem referente ao item b deste m�dulo � de '.$partLucro.'</td>
                 </tr>  
           ';
            
          }
    
         if ($mod_f3301 == 1){
            $html .= '<tr>
                  <td colspan="4">&nbsp;</td>
                  </tr>
                <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%" style="font-weight:bold">F33.01</td>
                   <td  colspan="1" width="84%" style="font-weight:bold">CUSTOS ADICIONAIS</td>
                 </tr>
                 <tr>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="1" width="8%">&nbsp;</td>
                   <td  colspan="2" width="84%">A tarifa de an�lise cadastral � de '.$ext.' '. $taxa_analise.' ('.$extAnalise.')<br>
                       A tarifa de monitoramento cadastral � de '.$ext.' '. $taxa_monit.' ('.$extMonit.')
                  </td>
                </tr>';
         }
    
      
        $html .= '</table><br>';

              
              
              
              $mpdf->AddPage();

                            $html .= '<pagebreak/>
              
                     CL�USULA 14� - PODERES DE REPRESENTA��O
                     <div style="clear-both">&nbsp;</div> 
                       <div id="cobtexto">Os signat�rios do presente documento declaram, sob as penas da lei, 
                       estarem investidos de poderes por seus representados para celebrarem o presente na forma em que est�
                       redigido, com a assun��o das obriga��es ora contra�das.
                      </div>
              
              <br>
              <br>
              <div style="text-align:center">S�o Paulo, '. $datahoje. '</div>
              <br>
              <br>
              <br>
              <br>
              
              <table width="100%" border="0" cellpadding="1">
                <tr>
                  <td style="text-align:center">_________________________________</td>
                  <td style="text-align:center"> __________________________________</td>
                </tr>
                <tr>
                  <td style="text-align:center">'.strtoupper(trim($Nome_Segurado)).'</td>
                  <td style="text-align:center">'.strtoupper(trim($Nome_Banco)).'</td>
                </tr>
                <tr>
                  <td style="text-align:center">(Carimbo do Signat�rio)</td>
                  <td style="text-align:center">(Carimbo do Signat�rio)</td>
                </tr>
              </table>
              
              <br>
              <br>
              <br>
              <div style="text-align:center">______________________________________________________________
                <br>'.$Nome_Empresa.'<br>
                (Carimbo do Signat�rio)
              </div>
            
          <br>
          <br>
          <br>
          <table width="100%" border="0">
              <tr>
               <td width="30%">&nbsp;</td>
               <td align="right" width="70%">
                
              </td> 
              </tr>
              </table>  
          
          </body>
          </html>';
          
          $html = utf8_encode($html);
          $mpdf->allow_charset_conversion=true;
          $mpdf->charset_in='UTF-8';
          $mpdf->WriteHTML($html);
          
          if (isset($_REQUEST['rascunho'])) {
            $mpdf->Output(); 
          } else { 
            $mpdf->Output($pdfDir.$key.'CondEspBB.pdf', 'F'); 
          }
           
          $mpdf->Output();
          $url_pdf = $host.'src/download/'.$key.'CondEspBB.pdf';
  
  //Fim do relat�rio
}else{
    
  //Apolice Ga

   // $list = new Java("java.util.ArrayList");
   //*****************************************************************************************************************
   $csql = "SELECT DISTINCT u.login, inf.respName, inf.idRegion, inf.name, inf.txMin, inf.prMin,
   inf.cnpj, inf.ie, inf.address, inf.tel, inf.fax, inf.email, inf.ocupationContact,
   inf.city, reg.name, Sector.description, inf.warantyInterest,
   inf.periodMaxCred, Country.name, inf.cep, inf.contrat, inf.contact,
   inf.txRise, inf.i_Seg, inf.txAnalize, inf.txMonitor, inf.limPagIndeniz,
   inf.prodUnit, inf.prMTotal, inf.percCoverage, inf.mModulos, inf.perBonus,
   inf.perPart0, inf.perPart1, inf.pLucro, inf.nas, inf.tipoDve,
   inf.addressNumber, inf.Ga, inf.addressComp, inf.products, inf.mPart, inf.txMTotal, inf.prMTotal,
   inf.i_Ramo, inf.i_Empresa, inf.startValidity AS DataInicio, inf.i_Produto, inf.v_LMI, inf.Renovacao_Tacita,
   SP.c_SUSEP
   FROM Users u
   INNER JOIN Insured i ON u.id = i.idResp
   INNER JOIN Inform inf ON inf.idInsured = i.id
   INNER JOIN Region reg ON inf.idRegion = reg.id
   INNER JOIN Sector ON inf.idSector = Sector.id
   INNER JOIN Sub_Produto SP ON SP.i_Produto = inf.i_Produto AND SP.i_Sub_Produto = inf.i_Sub_Produto
   LEFT JOIN Importer ON inf.id = Importer.idInform
   LEFT JOIN Country ON Importer.idCountry = Country.id
   WHERE inf.id = ?";

$cur = odbc_prepare($db, $csql);
odbc_execute($cur, [$idInform]);
// Ex: $login = odbc_result($cur, "login");

  // tenta achar o usu�rio respons�vel
  if (odbc_fetch_row($cur)) {
    $c_SUSEP       = odbc_result($cur, 'c_SUSEP');
    $login    = odbc_result($cur, 1);
    $respName = odbc_result($cur, 2);
    $idRegion = odbc_result($cur, 3);
    $name     = odbc_result($cur, 4);   
    $i_Ramo       = odbc_result($cur, 'i_Ramo');
    $i_Empresa    = odbc_result($cur, 'i_Empresa');
    $DataInicio   =  odbc_result($cur, 'DataInicio');   
    $txMin    = odbc_result($cur, 5);
    $prMin    = odbc_result($cur, 6);
    $prMTotal = odbc_result($cur, 'prMTotal');
    $txMTotal = odbc_result($cur, 'txMTotal');
    $ValorLMI = odbc_result($cur, 'v_LMI');
    $ExtValorLMI = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$fMoeda);                
    $renovacao_Tacica  = odbc_result($cur,'Renovacao_Tacita');
    $periodMaxCred = (odbc_result($cur, 'periodMaxCred') ? odbc_result($cur, 'periodMaxCred') : 180); 
        $extMax   = $numberExtensive->extensive($periodMaxCred, 3);
        $periodMaxCred  = $periodMaxCred. ' ('.$extMax.')';

    $cnpj     = odbc_result($cur, 7);
    $cnpj =
      substr($cnpj, 0, 2). ".".
      substr($cnpj, 2, 3). ".".
      substr($cnpj, 5, 3). "/".
      substr($cnpj, 8, 4). "-".
      substr($cnpj, 12);
    $ie       = odbc_result($cur, 8);
    $address  = odbc_result($cur, 9);
    $tel      = odbc_result($cur, 10);
    $fax      = odbc_result($cur, 11);
    $email    = odbc_result($cur, 12);
    $oContact = odbc_result($cur, 13);
    $city     = odbc_result($cur, 14);
    $uf       = substr(odbc_result($cur, 15), 0, 2);
    $descrip  = odbc_result($cur, 16);
    $interest = odbc_result($cur, 17);
    $period   = odbc_result($cur, 18);
    $DescrNatureza = odbc_result($cur, 'products');
    $complemento = odbc_result($cur, "addressComp");
    //$country  = odbc_result($cur, 19);
    $cep      = odbc_result($cur, 20);
//    $sub = substr($cep, 0, 5);
//      if(! preg_match("/\./", $sub)){
//        $sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
//      }else{
//        $inc = 1;
//        $sub = substr($cep, 0, 6);
//      }
//      if(! preg_match("/-/", $cep)){
//        $cep = "$sub-". substr($cep, 5);
//      }else{
//        $cep = "$sub-". substr($cep, 6 + $inc);
//      }

    // encontrar o n�mero de propostas
    $prop = isset($nProp) ? "/$nProp" : '';
    $contract     = odbc_result($cur, 21).$prop;
    $contato      = odbc_result($cur, 22);
    $txRise       = odbc_result($cur, 23);
    $extAnalise   = $numberExtensive->extensive(number_format(odbc_result($cur, "txAnalize"), 2, ".", ""), $fMoeda);
    $taxa_analise = number_format(odbc_result($cur, "txAnalize"), 2, ',', '.');
    $extMonit     = $numberExtensive->extensive(number_format (odbc_result($cur, "txMonitor"), 2, ".", ""), $fMoeda);
    $taxa_monit   = number_format(odbc_result($cur, "txMonitor"), 2, ',', '.');
    $limite       = odbc_result($cur, "limPagIndeniz");
    //die('?'.$limite. '<br>'.$csql.'<br>?'.odbc_result($cur, "limPagIndeniz"));    
    $cobertura    = odbc_result($cur, 30);
    $extcob       = $numberExtensive->porcentagem(odbc_result($cur, 30));
    $mBonus = odbc_result($cur, 31);
    $mPart  = odbc_result($cur, 'mPart');
    $perBonus = odbc_result($cur, 32);
    $perPart0 = odbc_result($cur, 33);
    $perPart1 = odbc_result($cur, 34);
    $pLucro = odbc_result($cur, 35);
   
  }

  odbc_free_result($cur);
   //**********************************************************************************************************************
   

   $sql = "SELECT ISNULL(adequacao_sinistralidade, 0) AS adequacao_sinistralidade,
   ISNULL(adequacao_premio, 0) AS adequacao_premio
FROM ModuloOferta
WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);

  //$adeq_sinist = odbc_result($x, "adequacao_sinistralidade");
  $Adequacao_Sinistralidade = odbc_result($x, "adequacao_sinistralidade");
  
  //$adeq_premio = odbc_result($x, "adequacao_premio");
  $Adequacao_Premio = odbc_result($x, "adequacao_premio");
odbc_free_result($x);
 
if($tipoBanco == 3){ // outros
  $sql = "SELECT dateIniVig, status FROM CDOB WHERE id = ?";
  $x = odbc_prepare($db, $sql);
  odbc_execute($x, [$idCDOB]);
  $start  = ymd2dmy(odbc_result($x, 1));
  $status = odbc_result($x, 2);
  odbc_free_result($x);

  $query = "SELECT imp.name, imp.address, imp.city AS city, c.name, imp.c_Coface_Imp, c.code
            FROM Importer imp
            JOIN Country c ON imp.idCountry = c.id
            JOIN CDOBDetails cdd ON cdd.idImporter = imp.id
            WHERE cdd.idCDOB = ?
            ORDER BY imp.name";
  $x = odbc_prepare($db, $query);
  odbc_execute($x, [$idCDOB]);

} else if($tipoBanco == 1){ // Banco do Brasil
  $sql = "SELECT dateIniVig, status, codigo FROM CDBB WHERE id = ?";
  $x = odbc_prepare($db, $sql);
  odbc_execute($x, [$idCDBB]);
  $start  = ymd2dmy(odbc_result($x, 1));
  $status = odbc_result($x, 2);
  $codigo = odbc_result($x, 3);
  $ano_cdbb = substr($start, 6, 4);
  odbc_free_result($x);

  $query = "SELECT imp.name, imp.address, imp.city AS city, c.name, imp.c_Coface_Imp, c.code
            FROM Importer imp
            JOIN Country c ON imp.idCountry = c.id
            JOIN CDBBDetails cdd ON cdd.idImporter = imp.id
            WHERE cdd.idCDBB = ?
            ORDER BY imp.name";
  $x = odbc_prepare($db, $query);
  odbc_execute($x, [$idCDBB]);

} else { // Banco Parceiros
  $sql = "SELECT dateIniVig, status FROM CDParc WHERE id = ?";
  $x = odbc_prepare($db, $sql);
  odbc_execute($x, [$idCDParc]);
  $start  = ymd2dmy(odbc_result($x, 1));
  $status = odbc_result($x, 2);
  odbc_free_result($x);

  $query = "SELECT imp.name, imp.address, imp.city AS city, c.name, imp.c_Coface_Imp, c.code
            FROM Importer imp
            JOIN Country c ON imp.idCountry = c.id
            JOIN CDParcDetails cdd ON cdd.idImporter = imp.id
            WHERE cdd.idCDParc = ?
            ORDER BY imp.name";
  $x = odbc_prepare($db, $query);
  odbc_execute($x, [$idCDParc]);
}

while(odbc_fetch_row($x)){
  strtoupper(trim(odbc_result($x, 1))); // nome
  strtoupper(trim(odbc_result($x, 2))); // endereco
  strtoupper(trim(odbc_result($x, 3))); // cidade
  strtoupper(trim(odbc_result($x, 4))); // pais
  trim(odbc_result($x, 5));             // ci cliente
}
odbc_free_result($x);

   $key = session_id(). time();
   $sql = "SELECT
        i.name,
        i.address,
        i.city,
        CAST(r.name AS varchar(2)),
        i.cnpj,
        i.ie,
        i.dateEmissionP,
        i.i_Seg,
        i.endValidity,
        i.percCoverage,
        i.prodUnit,
        i.startValidity,
        CAST(GETDATE() - i.startValidity AS int),
        i.addressNumber,
        i.NPC
        FROM Inform i
        JOIN Region r ON i.idRegion = r.id
        WHERE i.id = ?";

$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);

                
   $segurado    = trim(odbc_result($x, 1));
   $address     = odbc_result($x, 2);
   $addressNumber = odbc_result($x, 14);
   $cidade_estado = odbc_result($x, 3). " - ". odbc_result($x, 4);
   $cnpj        = arruma_cnpj(odbc_result($x, 5));
   $ie          = odbc_result($x, 6);
   $ie          = preg_match("/^[0-9]+$/", $ie) ? number_format($ie, 0, '', '.') : $ie;
   $emissao     = ymd2dmy(odbc_result($x, 7));
   $iSeg        = odbc_result($x, 8);
   $fim         = ymd2dmy(odbc_result($x, 9));
   $inivig      = ymd2dmy(odbc_result ($x, 12));
   $cobertura   = (int) odbc_result($x, 10);
   $cobertura   .= "% (". $numberExtensive->extensive($cobertura, 3). " por cento)";
   $prod        = odbc_result($x, 11);
   $npc         = odbc_result($x, "NPC");

odbc_free_result($x);

$sql = "SELECT n_Apolice FROM Inform WHERE id = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
   
   if(odbc_fetch_row($x)){
      $apolice = sprintf("062%06d", odbc_result($x, 1));
      
      if($prod != 62){
          $apolice .= "/$prod";
      }
   }
   odbc_free_result($x);

   if($tipoBanco == 3){
    $q = "SELECT c.name, c.endereco, c.cidade, r.description, c.cnpj, c.ie
          FROM CDOB c
          JOIN Region r ON c.idRegion = r.id
          WHERE c.id = ?";
    $x = odbc_prepare($db, $q);
    odbc_execute($x, [$idCDOB]);

    $bb_nome   = odbc_result($x, 1);
    $bb_address = odbc_result($x, 2);
    $bb_ce     = odbc_result($x, 3) . " - " . odbc_result($x, 4);
    $bb_cnpj   = arruma_cnpj(odbc_result($x, 5));
    $bb_ie     = odbc_result($x, 6);
    $bb_ie     = preg_match("/^[0-9]+$/", $bb_ie) ? number_format($bb_ie, 0, '', '.') : $bb_ie;
    odbc_free_result($x);

} else if($tipoBanco == 1){
    $sql = "SELECT A.name, A.endereco, A.cidade, A.uf, A.cnpj, A.ie, A.idBanco, B.name
            FROM Agencia A
            INNER JOIN Banco B ON B.id = A.idBanco
            WHERE A.id = ?";
    $x = odbc_prepare($db, $sql);
    odbc_execute($x, [$idAgencia]);

    $bb_nome     = odbc_result($x, 1);
    $bb_address  = odbc_result($x, 2);
    $bb_ce       = odbc_result($x, 3) . " - " . odbc_result($x, 4);
    $bb_cnpj     = arruma_cnpj(odbc_result($x, 5));
    $bb_ie       = odbc_result($x, 6);
    $bb_ie       = preg_match("/^[0-9]+$/", $bb_ie) ? number_format($bb_ie, 0, '', '.') : $bb_ie;
    $idBanco     = odbc_result($x, 7);
    $nome_banco  = odbc_result($x, 8);
    odbc_free_result($x);

} else {
    $sql = "SELECT A.name, A.endereco, A.cidade, A.uf, A.cnpj, A.ie, A.idBanco, B.name
            FROM Agencia A
            INNER JOIN Banco B ON B.id = A.idBanco
            WHERE A.id = ?";
    $x = odbc_prepare($db, $sql);
    odbc_execute($x, [$idAgencia]);

    $bb_nome     = odbc_result($x, 1);
    $bb_address  = odbc_result($x, 2);
    $bb_ce       = odbc_result($x, 3) . " - " . odbc_result($x, 4);
    $bb_cnpj     = arruma_cnpj(odbc_result($x, 5));
    $bb_ie       = odbc_result($x, 6);
    $bb_ie       = preg_match("/^[0-9]+$/", $bb_ie) ? number_format($bb_ie, 0, '', '.') : $bb_ie;
    $idBanco     = odbc_result($x, 7);
    $nome_banco  = odbc_result($x, 8);
    odbc_free_result($x);
}
   
$sql = "SELECT name, tipo FROM Banco WHERE id = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idBanco]);
   
    $nome_banco = strtoupper(odbc_result($x, 1));
    $tipo = odbc_result($x, 2);
    $tira12 = 0;
   
    if ($tipo == 2 || $tipo == 3) {
      $parceiro = 1;
      
      if ($tipo == 3) {
          $tira12 = 1; // tirar a clausula 12
      }
    }else{
      $parceiro = 0;
    }

odbc_free_result($x);

$sql = "SELECT YEAR(MAX(dateClient)) FROM CDBB WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
$anoBB = odbc_result($x, 1);
odbc_free_result($x);

$sql = "SELECT YEAR(MAX(dateClient)) FROM CDOB WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
$anoOB = odbc_result($x, 1);
odbc_free_result($x);

$sql = "SELECT YEAR(MAX(dateClient)) FROM CDParc WHERE idInform = ?";
$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);
$anoParc = odbc_result($x, 1);
odbc_free_result($x);

$sql = "SELECT nu_banco FROM ParModEsp WHERE idInform = ?";
$cur3 = odbc_prepare($db, $sql);
odbc_execute($cur3, [$idInform]);
// Ex: $nu_banco = odbc_result($cur3, 1);
  
  if (odbc_fetch_row($cur3)) {
        $tp_banco = odbc_result($cur3, 'nu_banco');
    }

    $ano = max($anoBB, $anoOB, $anoParc);
    $ano = isset($ano_cdbb) ? $ano_cdbb : date ('Y');

    odbc_free_result($cur3);

  $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4',
        'margin_left' => 20,
        'margin_right' => 15,
        'margin_top' => 42,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
        ];
    
    $mpdf=new  \Mpdf\Mpdf($opt);

  //$mpdf=new mPDF('win-1252','A4','','');
    $html = ob_get_clean();
    // $mpdf->useOnlyCoreFonts = true;    // false is default
  $mpdf->SetProtection(array('print'));
  $mpdf->SetTitle("Carta de esclarecimentos");
  $mpdf->SetAuthor($nomeEmp);
  
  if (isset($_REQUEST['rascunho'])) {
    $mpdf->SetWatermarkText("RASCUNHO"); // fundo marca d�gua
  } else {
    $mpdf->SetWatermarkText(""); // fundo marca d�gua
  }
  
  $mpdf->showWatermarkText = true;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->SetDisplayMode('fullpage');

  // Endere�o do logotipo
    $logo       =  $root .'images/logo.jpg';
    $logo_peq   = '../../images/logo_peq.jpg';
    //$logo_mini  = $root .'images/logo_peq.jpg';
    $logo_mini  = $root .'images/logo.jpg';
  $assinatura  = $root .'images/Assinatura Fernando.gif';
      
    $datahoje =  '';

    $conta_clausula = 1;

   // In�cio do arquivo montando primeiro o CSS
   
        $html = '<html>
        <head>
          <style>
            body {font-family: Arial, Helvetica, sans-serif;
              font-size: 12pt;
            }
            p {    margin: 0pt;
            }
    
            ol {counter-reset: item; font-weight:bold; }
                    li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify}
                    li:before {content: counters(item, "."); counter-increment: item; }
            
            ul      {list-style-type: none; font-weight:normal } 
            ul li   {padding: 3px 0px;color: #000000;text-align:justify} 
    
            #cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
            #sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
            #disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
            
            #img1{
              width: 300px;
              height: 70px;
              background:url('.$logo.') no-repeat;
    
            }';

            $html .='
          </style>
        </head>
        
        <body>
                <htmlpageheader name="myheader2">
                  <div style="text-align: center;">
                      <img src="'.$logo.'" width="260" height="70"/>
                  </div>
                  
                  <div style="text-align: center;">
                    <span style="font-weight: bold; font-size: 12pt;">CARTA DE ESCLARECIMENTOS
                    </span>
                  </div> 
                </htmlpageheader>
      
                <htmlpageheader name="myheader" style="height: 5px;">
                  <div style="text-align: center">
                    <img src="'.$logo_peq.'" width ="80" height="40"/>
                  </div>
                </htmlpageheader>
            
                <htmlpagefooter name="myfooter">
                  <table width="100%" border="0">
                      <tr>
                        <td width="22%">&nbsp;</td>
                        <td width="56%" style="text-align:center; font-size: 8pt;">
                            P�gina {PAGENO} de {nb}
                            <br><br>
                        </td>
  
                        <td width="22%">&nbsp;</td>
                      </tr>
                  </table>
                </htmlpagefooter>
                
                <sethtmlpageheader name="myheader2" value="on" show-this-page="1" />
                <sethtmlpageheader name="myheader" value="on" show-this-page="0" />
                <sethtmlpagefooter name="myfooter" value="on" />

          <div style="clear:both">&nbsp;</div>';  

          $html .= '

            <div id="cobtexto">
              Ref: Ap�lice de Seguro de Cr�dito � Exporta��o N.� 06200'.$Num_Apolice.'
              <br>
              <br>
            </div>

            <div id="cobtexto">Considerando a negocia��o de uma Ap�lice de Seguro de Cr�dito � Exporta��o entre '.strtoupper(trim($Nome_Segurado)).', inscrito sob o CNPJ '.formata_string('CNPJ', $CNPJ_Segurado).' ("<b>Segurado</b>") e Coface do Brasil Seguros de Cr�dito S/A, Seguradora inscrita sob o CNPJ n�07.644.868/0001-73 ("<b>Seguradora</b>") aqui devidamente representados por seu representante legal; Ap�lice essa a qual ser� utilizada como garantia a opera��es do <b>Segurado</b> junto ao Banco do Brasil S/A ("<b>Banco</b>"), inscrito no CNPJ sob o '.formata_string('CNPJ', $CNPJ_Agencia).', de acordo com as previs�es do "ENDOSSO n� '.$Cod_Clausula.' - CONDI��ES ESPECIAIS VINCULADAS � AP�LICE N.� 06200'.$Num_Apolice.'" - DESIGNA��O DE BENEFICI�RIO"

            O <b>Segurado</b> vem, com a finalidade de esclarecer procedimentos referentes a embarques negociados em opera��es de financiamento exclusivamente com o <b>Banco</b>, declarar o que segue:

<br><br><br>1 - <b>Segurado</b> declara que o <b>Banco</b> possui prefer�ncia no recebimento de indeniza��es, caso o valor total dos Cr�ditos sinistrados do <b>Segurado</b> e do <b>Banco</b> seja superior ao valor da Decis�o de Cr�dito do importador que deu causa ao sinistro.
A prefer�ncia no recebimento de indeniza��es pelo <b>Banco</b> ser� v�lida inclusive para os embarques realizados pelo <b>Segurado</b> antes da data de emiss�o da <b>Condi��o Especial para Designa��o de Benefici�rio</b> e para aqueles cuja cobertura de seguro esteja vinculada � vig�ncia de Ap�lice encerrada junto a SEGURADORA.
<br><br><br>2 - O <b>Segurado</b> declara que quaisquer instrumentos que venham a ser celebrados entre si e a <b>Seguradora</b>, que impliquem modifica��o das condi��es da ap�lice em refer�ncia, os quais modifiquem os direitos do <b>Banco</b>, ser�o previamente notificados ao <b>Banco</b>.
<br><br><br>3 - O <b>Segurado</b> aceita que a notifica��o de amea�a de sinistro prevista nas Condi��es Gerais, com prazo definido na Especifica��o da Ap�lice, seja formulada por si ou pelo <b>Banco</b>, observados os prazos e procedimentos para tanto estabelecidos, e mediante o envio dos documentos solicitados pela <b>Seguradora</b>. 
<br><br><br>4 - O <b>Segurado</b> autoriza expressamente a <b>Seguradora</b> a conceder acesso ao <b>Banco</b> �s informa��es constantes na �rea restrita da web page da <b>Seguradora</b> que sejam relativas ao cumprimento das obriga��es contratuais do <b>Segurado</b> no �mbito da ap�lice, em especial aos valores declarados nas declara��es de volume de exporta��es e a notifica��o de amea�a de sinistro bem como a quaisquer informa��es relacionadas aos importadores elencados na Ap�lice, sobretudo, seus respectivos limites de cr�dito. 
<br><br><br>5 - Para aplica��o e concess�o dos acessos mencionados no item acima o Segurado declara e se compromete a cumprir e manter as obriga��es de confidencialidade e prote��o de dados pessoais da Ap�lice.
</div>
            <br>
              <br>
              <br>
              <br>
              <br>
              
              <table width="100%" border="0" cellpadding="1">
                <tr>
                  <td style="text-align:center">______________________________________________________________</td>
                  
                </tr>
                <tr>
                  <td style="text-align:center">'.strtoupper(trim($Nome_Segurado)).'</td>
                  
                </tr>
                <tr>
                  <td style="text-align:center">(Carimbo do Signat�rio)</td>
                  
                </tr>
              </table>
              
              <br>
              <br>
              <br>
              <div style="text-align:center">______________________________________________________________
                <br>'.$Nome_Empresa.'<br>
                (Carimbo do Signat�rio)
              </div>
            
              <br>
              <br>
              <br>
              <br><br><br><br>
              <table width="100%" border="0">
                  <tr>
                  <td width="30%">&nbsp;</td>
                  
                  </tr>
                  </table>  
            </body>
          </html>';

          $html =mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
          $mpdf->allow_charset_conversion=true;
          $mpdf->charset_in='UTF-8';
          $mpdf->WriteHTML($html);
          $mpdf->Output();
   }

?>