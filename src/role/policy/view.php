<?php 
session_start();
include_once("../../../config.php"); 
include_once("consultaParcelas.php");

$diaT = date ('d');
$mesT = date ('m');
$dMoeda = date ("Y-m-d", mktime (0,0,0, $mesT, $diaT - 8, date("Y"))). ' 00:00:00.000'; //formata data de ontem
$errorx = 0;
$erroq = '';

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

function data_string($d){
    $meses = array("janeiro", "fevereiro", "março", "abril", "maio", "junho",
        "julho", "agosto", "setembro", "outubro", "novembro", "dezembro");
    //$dias_da_semana = array("Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado");
    list($dia, $mes, $ano) = explode('/', $d);
    $mes = $meses[$mes - 1];
    //$dia_da_semana = $dias_da_semana[$dia_da_semana];
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

$ano = date("y", time());
$key = time(). session_id();

require_once("../../pdfConf.php");

$prefix = $pdfDir. $key;
    
$query = "select t_Vencimento, Num_Parcelas,convert(varchar,DataPrimeiraParcela,103) as DataPrimeiraParcela from Inform where id = ". $field->getField("idInform");
$cury = odbc_exec($db, $query);

$t_Vento         = odbc_result($cury, "t_Vencimento");

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
}else if ($t_Venc == 2){
    $t_DescVencimento = 'bimestrais';
}else if ($t_Venc == 3){
    $t_DescVencimento = 'trimestrais';
}else if ($t_Venc == 4){
    $t_DescVencimento = 'semestrais';
}

$primeirovencimento   = odbc_result($cury, "DataPrimeiraParcela");
$num_parcelas = ($Num_Parcelas ? $Num_Parcelas : $numParc);


//print '?'.$num_parcelas;
//die();
// Começa a verifica ção de dados para voltar a apólice do modo anterior...

if(! file_exists($prefix. 'Apolice.pdf')){
    $ssq  ="update Inform set startValidity=dateAceit where id=". $field->getField("idInform");
    odbc_exec($db,$ssq);



    
    //die($ssq);   
    verifica_continua($db, $idInform);

    $query = "SELECT b.startValidity, b.endValidity, b.i_Seg, b.policyKey, b.idAnt, b.percCoverage, b.dateEmission,
                    b.pvigencia, b.currency,b.i_Produto,b.state,b.Periodo_Vigencia,dateAceit,
                    b.DataPrimeiraParcela, c.SistemaDestino, c.v_IOF, c.p_Multa, c.p_Juros_Dia, 
                    CASE WHEN SP.Descricao LIKE '%Tradeliner%' THEN 'RTL' ELSE 'GBL' END as tpProd 
              FROM  Inform b  
              INNER JOIN Config_Produto c ON c.i_Produto = b.i_Produto
              INNER JOIN Sub_Produto   SP On SP.i_Produto = b.i_Produto AND SP.i_Sub_Produto = b.i_Sub_Produto    
              WHERE b.id = ". $field->getField("idInform");
      
    $cur = odbc_exec($db, $query);

    $startValidity      = "";
    $endValidity        = "";
    $vencFirst          = "";
    $StateInform        = odbc_result($cur, "state");
    $ValidaData         = odbc_result($cur, "startValidity"); // Valor será utilizado na consulta em Par_Resseguro adiante.
    $i_Produto          = odbc_result($cur, "i_Produto");
    $SistemaDestino     = odbc_result($cur, "SistemaDestino");
    $linha              = odbc_num_rows($cur);
    $dateEmition        = odbc_result($cur, 7);
    $v_IOF              = odbc_result($cur, "v_IOF");
    $p_Multa            = odbc_result($cur, "p_Multa");
    $p_Juros_Dia        = odbc_result($cur, "p_Juros_Dia");
    // 2018/09/12 - AIP: Identificar os informes de Tradeliner para layout das cartas em PDF (carta.php / carta_Dom.php)
    $tpProd             = odbc_result($cur, "tpProd");

    //ver o ID anterior
    $sl = "select  
              infant.n_Apolice as Anterior,
              inf.n_Apolice as Atual
            from Inform inf
            inner join Inform infant on infant.id = inf.id
            where inf.id = ".  odbc_result($cur, 5) ."";
    $rrs =  odbc_exec($db, $sl);       
    $n_Anterior = odbc_result($rrs,'Anterior');
    

    if (Convert_Data($dateEmition) >= "2009-10-26"){
        $emiteTextoNovo = 1;
    }else{
        $emiteTextoNovo = 0;
    }


    //if(odbc_fetch_row($cur)) {
        if($linha){              
        $idAnt = odbc_result($cur, 5);
        $cobertura = number_format(odbc_result($cur, 6), 0, '', '');
        $inicio_vigencia = $data = $d_venc = odbc_result($cur, "startValidity");
        $d_venc  =  odbc_result($cur, "DataPrimeiraParcela");

        //Alterado por Tiago V N - 23/09/2005
        $vigencia = odbc_result($cur, "pvigencia");

        if ($vigencia == "") {
            $pvigencia = "1";
        }else if ($vigencia == "1"){
            $pvigencia = "1";
        }else{
            $pvigencia = "2";
        }


        if($Periodo_Vigencia){
            $pvigencia = $Periodo_Vigencia; 
        }

        $moeda = odbc_result($cur, "currency");

        //Alterado por Tiago V N - Elumini - 10/04/2006
        //Verifica qual é a moeda que o informe esta utilizando

        //if($SistemaDestino == 1){
            if ($moeda == "1") {
                $ext = "R$ ";
                $DescMoeda = "REAIS";
                $extmoeda = "real (R$)";
                $fMoeda = "1";
            }else if ($moeda == "2") {
                $ext = "USD ";
                $DescMoeda = "DÓLARES NORTE-AMERICANOS";
                $extmoeda = "dólar norte-americano (US$)";
                $fMoeda = "2";
            }else if ($moeda == "6") {
                $ext = "€ ";
                $DescMoeda = "EUROS";
                $extmoeda = "euro (€)";
                $fMoeda = "6";
            }
        //}
        $meses = array ("01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");

        if($data){
                    $d = substr($data, 8, 2);
                    $m = $meses[substr($data, 5, 2)];
                    $a = substr($data, 0, 4);
                    $startValidity = $d." de ".$m." de ".$a;
                    $dataEmissao = odbc_result($cur, 7);
                }
     
        if($Periodo_Vigencia){  
            $dataf1 =  SomarData(Convert_Data_Geral(odbc_result($cur, "dateAceit")),"+", 0, $Periodo_Vigencia, 0);
            $dataf2 =  SomarData($dataf1,"-", 1, 0, 0);             
            $fimVigencia = $dataf2; 
            $xd = substr($fimVigencia, 8, 2);
                    $ym = substr($fimVigencia, 5, 2);
                    $za = substr($fimVigencia, 0, 4);
            $data = Convert_Data_Geral($fimVigencia);
        }else{
            $data  = date("Y-m-d", mktime(0, 0, 0, $m, $d - 1, $a + $pvigencia));
        }


        
        //die($data);    
        
        $query_update = "update Inform set endValidity='$data 00:00:00.000' where id=". $field->getField("idInform");
        odbc_exec($db, $query_update);

        $m_fim = $meses[substr($data, 5, 2)];

        $endValidity = substr($data, 8, 2). " de ". $m_fim. " de ". substr($data, 0, 4);
        $idSeg = odbc_result ($cur, "i_Seg");
     
        if($idSeg == 0){
            $cc = odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)");
            $idSeg = odbc_result($cc, 1);
        }

        $policyKey = odbc_result($cur, "policyKey");
        // Países
        $cur = odbc_exec ($db,
                " SELECT Distinct Country.name, Country.score".
                " FROM Inform inf INNER JOIN".
                " VolumeSeg v ON inf.id=v.idInform INNER JOIN".
                /* " Importer ON inf.id = Importer.idInform INNER JOIN". */
                " Country ON v.idCountry = Country.id".
                " WHERE inf.id = $idInform AND score < 8");
        $countries = "";
        $first = true;

        while (odbc_fetch_row ($cur)) {
            $countries .= ($first ? "" : ", ").odbc_result($cur, 1);
                if ($first) $first = false;
        }

        $countries .= ".";

        //Alterado por Tiago V N - Elumini - 13/02/2006
        // 2018/09/14 - AIP: Inclusão Do Número SUSEP Do produto
        $csql = "SELECT Distinct u.login, inf.respName, inf.idRegion, inf.name, inf.txMin, inf.prMin,
                inf.cnpj, inf.ie, inf.address, inf.tel, inf.fax, inf.email, inf.ocupationContact,
                inf.city, reg.name, Sector.description, inf.warantyInterest,
                inf.periodMaxCred, Country.name, inf.cep, inf.contrat, inf.contact,
                inf.txRise, inf.i_Seg, inf.txAnalize, inf.txMonitor, inf.limPagIndeniz,
                inf.prodUnit, inf.prMTotal, inf.percCoverage, inf.mModulos, inf.perBonus,
                inf.perPart0, inf.perPart1, inf.pLucro, inf.nas, inf.tipoDve,
                inf.addressNumber, inf.Ga, inf.addressComp, inf.products,inf.mPart,inf.txMTotal,inf.prMTotal,
                inf.i_Ramo,inf.i_Empresa,inf.startValidity as DataInicio,inf.i_Produto, inf.v_LMI, inf.Renovacao_Tacita,currency, 
                SP.c_SUSEP 
                FROM Users u 
                INNER JOIN Insured i ON u.id = i.idResp 
                INNER JOIN Inform inf ON inf.idInsured = i.id 
                INNER JOIN Region reg ON inf.idRegion = reg.id 
                INNER JOIN Sector ON inf.idSector = Sector.id 
                INNER Join Sub_Produto SP ON SP.i_Produto = inf.i_Produto AND SP.i_Sub_Produto = inf.i_Sub_Produto 
                LEFT  Join Importer ON inf.id = Importer.idInform 
                LEFT  Join Country ON Importer.idCountry = Country.id 
                WHERE inf.id = $idInform";
        $cur = odbc_exec($db,$csql);



        // tenta achar o usuário responsável
        if (odbc_fetch_row($cur)) {


            $c_SUSEP  = odbc_result($cur,'c_SUSEP');
            $currency     = odbc_result($cur,'currency');
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
            $ValorLMI = odbc_result($curx, 'v_LMI');
            $ExtValorLMI = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$fMoeda);                              
            $renovacao_Tacica  = odbc_result($cur,'Renovacao_Tacita');

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
//            $sub = substr($cep, 0, 5);
//                if(! preg_match("@\.@", $sub)){
//                    $sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
//                }else{
//                    $inc = 1;
//                    $sub = substr($cep, 0, 6);
//                }
//
//                if(! preg_match("@-@", $cep)){
//                    $cep = "$sub-". substr($cep, 5);
//                }else{
//                    $cep = "$sub-". substr($cep, 6 + $inc);
//                }

            // encontrar o número de propostas
            $contract     = odbc_result($cur, 21) . "/$nProp";
            $contato      = odbc_result($cur, 22);
            $txRise       = odbc_result($cur, 23);
            $extAnalise   = $numberExtensive->extensive(number_format(odbc_result($cur, "txAnalize"), 2, ".", ""), 1);
            $taxa_analise = number_format(odbc_result($cur, "txAnalize"), 2, ',', '.');
            $extMonit     = $numberExtensive->extensive(number_format (odbc_result($cur, "txMonitor"), 2, ".", ""), 1);
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
            
                    

            if (odbc_result($cur, 36)== "" Or odbc_result($cur, 36)=="0.00"){

                if ($moeda == "2") {
                        $nas = "500.00";
                }else if ($moeda == "6") {
                        $nas = "400.00";
                }
            }else{
                $nas = odbc_result($cur, 36);

                /*if ($moeda == "2") {
                        $nas =  number_format(odbc_result($cur, 36), 2, ',', '.');
                }else if ($moeda == "6") {
                        $nas =  number_format(odbc_result($cur, 36), 2, ',', '.');
                }
                */
            }

            //$extnas = $ext." ".number_format($nas, 2, ',','.')." (".strtolower($numberExtensive->extensive(number_format($nas,2, '.',''),$fMoeda)).').';
            $extnas = strtolower($numberExtensive->extensive(number_format($nas,2, '.',''),$fMoeda));                 
            $tipodve    = odbc_result($cur, 37);
            $extpart0   = $numberExtensive->porcentagem($perPart0);
            $extpart1   = $numberExtensive->porcentagem($perPart1);
            $addressNumber = odbc_result($cur, 38);
            $ga            = odbc_result($cur, 39);

            if (($ga=="0") || ($ga=="")){
                $susep = "15.414005218/2005-89";
                $cp    = "CP/RC/06-01";
            }else{
                $susep = "15.414004768/2004-08";
                $cp    = "CP/GA/07-01";
            }

            $idSeg = odbc_result($cur, 'i_Seg');
              
                if($idSeg == 0){
                    $cc = odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)");
                    $idSeg = odbc_result($cc, 1);
                }

            $prodUnit = odbc_result($cur, 'prodUnit');
            //Alterado por Elias Vaz Interaktiv - Dez/2009
            //$tx = $txMin * (1 + $txRise) * 100;
            $tx = $txMTotal;

            $IC_APLICA = odbc_exec($db, "select IC_APLICA from TB_TAXA_INFORME_BB where ID_INFORME=$idInform");
            $IC_APLICA_TAXA = odbc_result($IC_APLICA, 1);

                //# Taxa de prêmio + 10%
                if($IC_APLICA_TAXA=="1") {
                    $tx = $tx;
                } else {
                    //$tx = $tx+($tx*0.10);
                    $tx = $tx;
                }

            $exttx = $numberExtensive->porcentagem($tx);
            //$pr = $prMin * ($interest == 1 ? 1.04 : 1) * (1 + $txRise);
            $pr =  number_format(odbc_result($cur, 'prMTotal'),0,".","");
            $extPremioMinimo = $numberExtensive->extensive(number_format($pr,0,'.',''),$fMoeda);
            
            if($policyKey == ""){
                    $ja_foi = 0;
            }else{
                    $ja_foi = 1;
            }

                    //******************************************************************* 
                    //ATUALIZA DADOS NO INFORM
            /******************************************************************/
            $qry     = "select top 1 i_Ramo From Ramo where i_Empresa = ".$i_Empresa." and Inicio_Vigencia <= '".$DataInicio."' order by Inicio_Vigencia desc ";
                    $rep     = odbc_exec($db,$qry);
  
                    $i_Ramo  = odbc_result($rep,'i_Ramo');
  
            $qry = " Update Inform Set i_Ramo = ".$i_Ramo." where id =".$idInform;
            $rep = odbc_exec($db,$qry);
            
            //die($qry);       
                  
            //******************************************************************* 
                    //VERIFICA DAQUI PRA FRENTE SE VAI ADICIONAR NO SISSEG OU OIM
            /******************************************************************/
            // gera a apolice (se ainda nao foi gerada)

            

            if(! $ja_foi){
                // INICIO GERAÇÃO APÓLICE DOMÉSTICO
                        
                if($SistemaDestino == 1){ 

                    
                    /*
                    •   SPR_COF_SIEX_OIM – SP do SIEx que retornará todas as informações que deverão ser passadas para a base do OIM;
                                    •   spr_IM_Proc_Imp_CDT – SP que realiza o cadastro do Segurado, Corretor, Contato, Gerente Comercial no OIM;
                                    •   spr_IM_Proc_Imp_DCT – SP que realiza o cadastro da apólice no OIM.
                    */
                            
                    $max = '';
                            
                    // Passara 4 vezes pela sp  no loop
                    // primeiro 200,300,400,500
                    $contador = 200;
                    odbc_autocommit($db, false);
                            
                    for($i =0; $i< 4;$i++){
                        //Adiciona informações para geração da apólice no OIM
                                                
                        $qrySP = 'exec SPR_COF_SIEX_OIM '.$idInform.','.$contador;
                                
                        $resSP2 = odbc_exec($db,$qrySP);
                                
                        if(odbc_fetch_row($resSP2)){
                            $qrySP = 'exec spr_IM_Proc_Imp_CDT '.
                                (odbc_result($resSP2,'Empresa') ? " '".odbc_result($resSP2,'Empresa')."'," : 'null, ').
                                " ".(odbc_result($resSP2,'t_Registro')? odbc_result($resSP2,'t_Registro'): 'null').",".
                                (odbc_result($resSP2,'s_Cadastro') ? " '".substr(odbc_result($resSP2,'s_Cadastro'),0,15)."'," : '0,').
                                (odbc_result($resSP2,'Usuario') ? " '".substr(odbc_result($resSP2,'Usuario'),0,15)."'," : 'null,').
                                (odbc_result($resSP2,'CNP') ? " '".substr(odbc_result($resSP2,'CNP'),0,14)."'," : 'null,').
                                (odbc_result($resSP2,'Nome') ? " '".substr(odbc_result($resSP2,'Nome'),0,60)."'," : 'null,').
                                (odbc_result($resSP2,'Endereco') ? " '".substr(odbc_result($resSP2,'Endereco'),0,254)."'," : 'null,').
                                (odbc_result($resSP2,'Complemento') ? " '".substr(odbc_result($resSP2,'Complemento'),0,60)."'," : 'null,').
                                (odbc_result($resSP2,'Bairro') ? " '".substr(odbc_result($resSP2,'Bairro'),0,30)."'," : 'null,').
                                (odbc_result($resSP2,'CEP') ? " '".substr(odbc_result($resSP2,'CEP'),0,9)."'," : 'null,').
                                (odbc_result($resSP2,'Cidade') ? " '".substr(odbc_result($resSP2,'Cidade'),0,60)."'," : 'null,').
                                (odbc_result($resSP2,'Estado') ? " '".substr(odbc_result($resSP2,'Estado'),0,2)."'," : 'null,').
                                (odbc_result($resSP2,'Pais') ? " '".substr(odbc_result($resSP2,'Pais'),0,15)."'," : 'null,').
                                (odbc_result($resSP2,'Cod_Area') ? " '".substr(odbc_result($resSP2,'Cod_Area'),0,4)."'," : 'null,').
                                (odbc_result($resSP2,'Telefone') ? " '".substr(odbc_result($resSP2,'Telefone'),0,15)."'," : 'null,').
                                (odbc_result($resSP2,'Fax') ? " '".substr(odbc_result($resSP2,'Fax'),0,15)."'," : 'null,').
                                (odbc_result($resSP2,'Celular') ? " '".substr(odbc_result($resSP2,'Celular'),0,15)."'," : 'null,').
                                (odbc_result($resSP2,'Email') ? " '".substr(odbc_result($resSP2,'Email'),0,60)."'," : 'null,').
                                (odbc_result($resSP2,'c_Susep') ? " '".substr(odbc_result($resSP2,'c_Susep'),0,18)."'," : 'null,').
                                (odbc_result($resSP2,'c_Proprio') ? " '".substr(odbc_result($resSP2,'c_Proprio'),0,18)."'," : 'null,').
                                (odbc_result($resSP2,'c_Contabil') ? " '".substr(odbc_result($resSP2,'c_Contabil'),0,15)."'," : 'null,').
                                (odbc_result($resSP2,'Endereco_Corresp') ? " '".substr(odbc_result($resSP2,'Endereco_Corresp'),0,255)."'," : 'null,').
                                (odbc_result($resSP2,'Complemento_Corresp') ? " '".substr(odbc_result($resSP2,'Complemento_Corresp'),0,60)."'," : 'null,').
                                (odbc_result($resSP2,'Bairro_Corresp') ? " '".substr(odbc_result($resSP2,'Bairro_Corresp'),0,30)."'," : 'null,').
                                (odbc_result($resSP2,'CEP_Corresp') ? " '".substr(odbc_result($resSP2,'CEP_Corresp'),0,9)."'," : 'null,').
                                (odbc_result($resSP2,'Cidade_Corresp') ? " '".substr(odbc_result($resSP2,'Cidade_Corresp'),0,60)."'," : 'null,').
                                (odbc_result($resSP2,'Estado_Corresp') ? " '".substr(odbc_result($resSP2,'Estado_Corresp'),0,2)."'," : 'null,').
                                (odbc_result($resSP2,'Pais_Corresp') ? " '".substr(odbc_result($resSP2,'Pais_Corresp'),0,15)."'," : 'null,').
                                (odbc_result($resSP2,'CNP_Ref') ? " '".substr(odbc_result($resSP2,'CNP_Ref'),0,14)."'," : 'null,').
                                 " ".(odbc_result($resSP2,'t_Registro_Ref')? odbc_result($resSP2,'t_Registro_Ref') : 'null').",".
                                (odbc_result($resSP2,'Cargo') ? " '".substr(odbc_result($resSP2,'Cargo'),0,60)."'" : 'null');                                       
                                // Ação
                                                     
                            odbc_autocommit($dbOIM,false);
                            
                            $resSP6 = odbc_exec($dbOIM,$qrySP);
                            $j=0;               
                            while(odbc_fetch_row($resSP6)){                                             
                               
                                $erroq .= '<br><br><strong>Erro '.($errorx +=1) .'</strong>: A seguinte instrução não foi executada, informe ao administrador do sistema.
                                           <br><br>'.odbc_result($resSP6,'Mensagem').'<br><br>'.$qrySP.'<br>';
                               
                               
                                $j++;
                            }

                            odbc_autocommit($dbOIM, true);
                            //$erroq .= 'Erro '.($errorx +=1) .': A seguinte instrução não foi executada, informe ao administrador do sistema.<br><br>'.$qrySP.'<br>';
                            if($j > 0){
                               $ta_errado = true;
                            }
                        }
                                
                        $contador += 100;
                    }
                    
                    $j=0;                           
                    //Adiciona informações para geração da apólice no OIM
                    //odbc_autocommit($db,false);


                    //Inserido por Interaktiv 16/07/2013
                    // odbc_close($db);

                    



                    $qrySP = 'exec SPR_COF_SIEX_OIM '.$idInform.',100';
                    $resSP = odbc_exec($db,$qrySP);
                            
                    //odbc_autocommit($db, trume);      
                    $VF  = 0;



                   $n_Apolice_Ret = isset($n_Apolice_Ret) ? $n_Apolice_Ret : "";
                    
                            
                    if(odbc_fetch_row($resSP)){
                        //RODAR AQUI A SP QUE GERA A APOLICE NO OEM
                        $qrySP = 'exec spr_IM_Proc_Imp_DCT  '.
                            (odbc_result($resSP,'Empresa') ? " '".substr(odbc_result($resSP,'Empresa'),0,5)."'," : 'null,').
                            " ".odbc_result($resSP,'t_Registro').",".
                            (odbc_result($resSP,'CNP_Segurado') ? " '".substr(odbc_result($resSP,'CNP_Segurado'),0,14)."'," : 'null,').
                            (odbc_result($resSP,'Resseguro') ? " '".substr(odbc_result($resSP,'Resseguro'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Sucursal') ? " '".substr(odbc_result($resSP,'Sucursal'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Ramo') ? " '".substr(odbc_result($resSP,'Ramo'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Produto') ? " '".substr(odbc_result($resSP,'Produto'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Moeda') ? " '".substr(odbc_result($resSP,'Moeda'),0,15)."'," : 'null,').
                            " ".odbc_result($resSP,'n_Parcelas').",".
                            " ".(odbc_result($resSP,'v_Premio')? odbc_result($resSP,'v_Premio') : 'null').",".
                            (odbc_result($resSP,'d_Inicio_Vigencia') ? " '".odbc_result($resSP,'d_Inicio_Vigencia')."'," : 'null,').
                            (odbc_result($resSP,'d_Fim_Vigencia') ? " '".odbc_result($resSP,'d_Fim_Vigencia')."'," : 'null,').
                            (odbc_result($resSP,'d_Emissao') ? " '".odbc_result($resSP,'d_Emissao')."'," : 'null,').
                            (odbc_result($resSP,'CNP_Corretor1') ? " '".substr(odbc_result($resSP,'CNP_Corretor1'),0,14)."'," : 'null,').
                            " ".(odbc_result($resSP,'p_Comissao1') ? odbc_result($resSP,'p_Comissao1') :'null').",".
                            (odbc_result($resSP,'Usuario') ? " '".substr(odbc_result($resSP,'Usuario'),0,15)."', " : 'null,').
                            " ".(odbc_result($resSP,'v_IOF') ? odbc_result($resSP,'v_IOF'): 'null').",".
                            " ".(odbc_result($resSP,'v_Frac') ? odbc_result($resSP,'v_Frac') : 'null').",".
                            " ".(odbc_result($resSP,'v_Custo') ? odbc_result($resSP,'v_Custo') : 'null').",".
                            " ".(odbc_result($resSP,'v_IS')? odbc_result($resSP,'v_IS') : 'null' ).",".
                            " ".(odbc_result($resSP,'v_LMI') ? odbc_result($resSP,'v_LMI') : 'null').",".
                            " ".(odbc_result($resSP,'n_Fator_LMI') ? odbc_result($resSP,'n_Fator_LMI'):'null').",".
                            " ".(odbc_result($resSP,'p_Taxa_Seguro') ? odbc_result($resSP,'p_Taxa_Seguro') : 'null').",".
                            (odbc_result($resSP,'n_Apolice_Renovada') ? " '".substr(odbc_result($resSP,'n_Apolice_Renovada'),0,20)."'," : 'null,'). 
                            (odbc_result($resSP,'c_Proprio') ? " '".substr(odbc_result($resSP,'c_Proprio'),0,18)."'," : 'null,').
                            " ".(odbc_result($resSP,'v_TE') ? odbc_result($resSP,'v_TE') :'null').",".
                            (odbc_result($resSP,'c_Processo_Susep') ? " '".substr(odbc_result($resSP,'c_Processo_Susep'),0,20)."'," : 'null,').
                            (odbc_result($resSP,'c_Proposta') ? " '".substr(odbc_result($resSP,'c_Proposta'),0,20)."'," : 'null,').
                            (odbc_result($resSP,'d_Proposta') ? " '".odbc_result($resSP,'d_Proposta')."'," : 'null,').
                            (odbc_result($resSP,'Regiao') ? " '".substr(odbc_result($resSP,'Regiao'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Centro_Custo') ? " '".substr(odbc_result($resSP,'Centro_Custo'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Atividade_Segurada') ? " '".substr(odbc_result($resSP,'Atividade_Segurada'),0,100)."'," : 'null,'). 
                            " ".(odbc_result($resSP,'n_Exercicio') ? odbc_result($resSP,'n_Exercicio') : 'null') .",".
                            " ".(odbc_result($resSP,'p_Taxa_Desagio') ? odbc_result($resSP,'p_Taxa_Desagio') : 'null').",".
                            (odbc_result($resSP,'CNP_Gerente') ? " '".substr(odbc_result($resSP,'CNP_Gerente'),0,11)."'," : 'null,').
                            (odbc_result($resSP,'c_Grupo_Corretor1') ? " '".substr(odbc_result($resSP,'c_Grupo_Corretor1'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'CNP_Corretor2') ? " '".substr(odbc_result($resSP,'CNP_Corretor2'),0,14)."'," : 'null,').
                            " ".(odbc_result($resSP,'p_Comissao2') ? odbc_result($resSP,'p_Comissao2') : 'null').",".
                            (odbc_result($resSP,'c_Grupo_Corretor2') ? " '".substr(odbc_result($resSP,'c_Grupo_Corretor2'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'CNP_Corretor3') ? " '".substr(odbc_result($resSP,'CNP_Corretor3'),0,14)."'," : 'null,').
                            " ".(odbc_result($resSP,'p_Comissao3') ? odbc_result($resSP,'p_Comissao3') :'null').",".
                            (odbc_result($resSP,'c_Grupo_Corretor3') ? " '".substr(odbc_result($resSP,'c_Grupo_Corretor3'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'CNP_Corretor4') ? " '".substr(odbc_result($resSP,'CNP_Corretor4'),0,14)."'," : 'null,').
                            " ".(odbc_result($resSP,'p_Comissao4') ? odbc_result($resSP,'p_Comissao4') :'null').",".
                            (odbc_result($resSP,'c_Grupo_Corretor4') ? " '".substr(odbc_result($resSP,'c_Grupo_Corretor4'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Origem_Negocio') ? " '".substr(odbc_result($resSP,'Origem_Negocio'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'Setor') ? " '".substr(odbc_result($resSP,'Setor'),0,15)."'," : 'null,').
                            " ".(odbc_result($resSP,'v_CoberturaApo') ? odbc_result($resSP,'v_CoberturaApo') :'null').",".
                            " ".(odbc_result($resSP,'n_PrazoCredito') ? odbc_result($resSP,'n_PrazoCredito') :'null').",".
                            " ".(odbc_result($resSP,'v_PequenosSin') ? odbc_result($resSP,'v_PequenosSin') :'null').",".
                            " ".(odbc_result($resSP,'n_PrazoRecDVN') ? odbc_result($resSP,'n_PrazoRecDVN') :'null').",".
                            (odbc_result($resSP,'PeriodoVencParc') ? " '".substr(odbc_result($resSP,'PeriodoVencParc'),0,15)."'," : 'null,').
                            (odbc_result($resSP,'d_Primeiro_Venc') ? " '".odbc_result($resSP,'d_Primeiro_Venc')."'," : 'null,').
                            " ".(odbc_result($resSP,'p_PartLucros') ? odbc_result($resSP,'p_PartLucros') :'null').",".
                            " ".(odbc_result($resSP,'p_TxPremioAdeq') ? odbc_result($resSP,'p_TxPremioAdeq') :'null').",".
                            " ". (odbc_result($resSP,'p_BonusAusenciaSin') ? odbc_result($resSP,'p_BonusAusenciaSin') : 'null').",".
                            (odbc_result($resSP,'Modalidade') ? " '".substr(odbc_result($resSP,'Modalidade'),0,15)."'," : 'null,').
                            $n_Apolice_Ret .' out ';
                            //(odbc_result($resSP,'n_Apolice_Ret') ? " '".odbc_result($resSP,'n_Apolice_Ret')."'" : 'null');
                                    
                        

                        odbc_autocommit($dbOIM,false);

                        
                        
                        
                        
                        
                        
                        $resSP3 = odbc_exec($dbOIM,$qrySP);


                        
                        //print $qrySP;
                    
                        
                                    
                        if($resSP3){                        

                            $max  = '';
                            $j=0;
                            $x=0;
                            $erroq .='<br><br><strong>Atenção!</strong><br>';
                                while(odbc_fetch_row($resSP3)){                 
                                    if(is_numeric(odbc_result($resSP3,'Mensagem'))){
                                        $UltimoNumeroOEM = odbc_result($resSP3,'Mensagem');
                                        $max = $UltimoNumeroOEM;
                                        $x++;
                                        
                                    }else{
                                        $erroq .=  '<br>'.odbc_result($resSP3,'Mensagem').'<br>';
                                        $j++;
                                    }
                                }
                                
                                if($j > 0 && $x == 0){          
                                    $ta_errado = true;
                                    
                                }else{
                                    $ta_errado = false;
                                    odbc_autocommit($dbOIM,true);
                                }
                                                    
                        }else{
                            $max  = '';
                            $erroq .= '<br><br><strong>Erro: '.($errorx +=1) .'</strong> A pólice não foi gerada, informe este erro ao administrador do sistema:<br><br>'.$qrySP.'<BR>';
                            $ta_errado = true;
                        }
                        


                        

                        odbc_autocommit($db,true);
                        
                        //die($erroq.'<br>?'.$max.'<br>'.$apoNum.'<br>');
                        
                        if($ta_errado){
                            //defaz as atualizações:
                            odbc_rollback($dbSisSeg);
                            odbc_rollback($db);                         
                            odbc_rollback($dbOIM);
                        
                            $erroq .= "<br><br><h3>Nenhum dado foi registrado para esta apólice<h3><br>";
                            
                            $_SESSION['msg'] = $erroq;
                            $query = " Update Inform Set state = 6,startValidity= null, endValidity = null, policyKey= null where id = $idInform ";
                            $r = odbc_exec($db, $query);
                            
                            ?><script> window.location = '<?php echo $host;?>src/role/policy/Policy.php?comm=view'; </script>
                            <?php
                            
                            exit;
                            
                        }
                    }

                    
                                                                     
                    // FIM GERAÇÃO APÓLICE DOMÉSTICO    
                }else{   // INICIO GERAÇÃO APÓLICE EXTERNO


                    //Moeda --
                    $start_aux = dmy2ymd($startValidity);
                    // verifica se existe valor de compra
                    $r = odbc_exec($dbSisSeg, "select v_Compra from Valor_Moeda where d_Cotacao='$dMoeda' And n_Moeda='$moeda'");

                    if(! odbc_fetch_row($r)){
                        


                        $MoedaVal = substr($dMoeda, 8, 2). "/". substr($dMoeda, 5, 2). "/". substr($dMoeda, 0, 4);
                        $msg = "N&atilde;o existe valor de compra para o dia $MoedaVal";
                        // echo "dMoeda $dMoeda";
                        // echo "<br>MoedaVal $MoedaVal";
                        $return_flag = 1;
                        return;
                    }else{
                        $v_compra = odbc_result($r, 1);
                    }

                    odbc_autocommit($dbSisSeg, false);
                    $ta_errado = false;
                    // atualiza dados da proposta
                    $end_aux = dmy2ymd($endValidity);
                    $query =
                        "update Proposta set d_Aceitacao='$start_aux', d_Inicio='$start_aux', ".
                        "d_Fim='$end_aux', s_Proposta=11 where c_Coface=$c_coface and n_Prop=$nProp";
                    $r = odbc_exec($dbSisSeg, $query);
                    
                        
                    if(! $r){
                        $erroq .= '<br>erro: '.($errorx +=1) .'<br>'.$query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                        $ta_errado = true;
                    }
    
                    // atualiza dados da parcela
                    $query =
                        "select i_Parcela, d_Venc from Parcela where i_Parcela=".
                        "(select max(i_Parcela) from Parcela where ".
                        "c_Coface=".$c_coface." and n_Prop=".$nProp." and n_Seq_Parcela=1)";
                    $r = odbc_exec($dbSisSeg,$query);
                                
                    if(! $r){
                        $erroq .= '<br>'. odbc_errormsg($dbSisSeg).'<br>';
                    }

                    if(odbc_fetch_row($r)){
                        $id_primeira_parcela = odbc_result($r, 1);
                        //Alterador por Tiago V N - Elumini - 24/02/2006
                        //$d_venc = odbc_result($r, 2);
                    }

                    $query = "update Parcela set t_parcela=2 where i_Parcela=$id_primeira_parcela";
                    $r = odbc_exec($dbSisSeg, $query);

                    if(! $r){
                        $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                        $ta_errado = true;
                        
                        
                    }

                    // insere nova entrada na Base de Calculo
                    $query =
                        "insert into Base_Calculo (c_Coface, i_Seg, n_Prop, Tx_Moeda, d_Ini_Vig, d_Fim_Vig,".
                        "v_Premio, d_Aceitacao, n_Filial, d_Emissao, p_Cobertura, n_Sucursal, n_Moeda, n_Ramo, t_Apolice,".
                        "t_Endosso, d_Doc, n_User, s_Doc, n_Mod, d_Situacao, s_TipDoc) values (".
                        "$c_coface,".     // c_Coface
                        "$idSeg,".        // i_Seg
                        "$nProp,".        // n_Prop
                        "$v_compra,".     // taxa da moeda (Tabela Valor_Moeda.v_Compra) do dia da Aceitação(Tabela Proposta)
                        "'$start_aux',".  // início da vigência
                        "'$end_aux',".    // fim da vigência
                        "$pr,".           // valor do prêmio
                        "'$start_aux',".  // Pegar na tabela Proposta
                        "$prodUnit,".     // numero da filial
                        "getdate(),".     // Data de emissão da Apólice
                        "$cobertura,".    // Percentual de cobertura
                        // Dados Fixos
                        "62, '$moeda', 49, 0, 0, getdate(), 47, 0, 0, getdate(), 0)";
                        //Moeda--
                   //print '???<br>'.$query; 
                   //die();  
                    $r = odbc_exec($dbSisSeg, $query);

                    if(! $r){
                        $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                        $ta_errado = true;
                    }
                                
                    if($r){ // pega o id da Base de Calculo (vai ser muito usado depois)
                        $r = odbc_exec($dbSisSeg, "select max(i_BC) from Base_Calculo where c_Coface=$c_coface");

                        if(! $r){
                            $erroq .= '<br>erro: '.($errorx +=1).'<br>'. odbc_errormsg($dbSisSeg);
                        }
                                  
                        if(odbc_fetch_row($r)){
                            $idBC = odbc_result($r, 1);
                        }
                    }
    
                    // pega o maior id de apolice até entao e incrementa
                    $rr = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice");
    
                    if(! $rr){
                        $erroq .= '<br>erro: '.($errorx +=1).'<br>'. odbc_errormsg($dbSisSeg);
                    }

                    if(odbc_fetch_row($rr)){
                        $max = odbc_result($rr, 1);
                        $max++;
                    }

                    // Gera a apolice
                    $query =
                        "insert into Apolice (n_Sucursal, n_Ramo, i_Seg, n_Apolice) ".
                         "values (62, 49, $idSeg, $max)"; // 62 ao inves de $prodUnit
                    $r = odbc_exec($dbSisSeg, $query);

                    if(! $r) {
                        $erroq .= '<br>erro: '.($errorx +=1).'<br>'. "Não foi poasível gerar apolice $max: $query<br>". odbc_errormsg();
                        $ta_errado = true;
                    }
                        
                    // Ricardo - Verificar o número certo da apólice renovada
                    if($idAnt){ // se for renovacao
                    
                        if(! odbc_exec($dbSisSeg, "update Base_Calculo set Renova_n_Apolice=". ($n_Anterior ? $n_Anterior : $max). ", t_Apolice=1 where i_BC=$idBC")){
                            $erroq .= '<br>erro: '.($errorx +=1).'<br>'. "Nao foi possivel atualizar Base_Calculo para apolice renovada<br>";
                            $ta_errado = true;
                        }
                                    
                        $query =
                            "update Proposta set Renova = 1, n_Apolice_Renova=". ($n_Anterior ? $n_Anterior : $max). " where c_Coface=$c_coface and n_Prop=$nProp";
                        $r = odbc_exec($dbSisSeg, $query);

                        if(! $r){
                            $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                            $ta_errado = true;
                        }
                    }


    
                    // insere dados do endosso
                    $query =
                        "insert into Endosso (i_BC, n_Apolice, ".
                        "n_Sucursal, n_Ramo, n_Endosso, d_Endosso, n_User) ".
                        "values (".
                        "$idBC,".    // i_BC
                        "$max,".     // número da Apólice
                        // Valores Fixos
                        "62, 49, 0, getdate(), 47)";
    
                    $r = odbc_exec($dbSisSeg, $query);

                    if(! $r){
                        $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                        $ta_errado = true;
                    }
    
                    if($ta_errado){
                        odbc_rollback($dbSisSeg);
                        odbc_rollback($db);
                        //print 'oi';
                        //odbc_autocommit($dbSisSeg, true);
                        
                        //odbc_autocommit($db, true);
                        //defaz as atualizações:
                        $erroq .= "<br<br><h3>Nenhum dado foi registrado para esta apólice<h3><br>";
                        
                        $_SESSION['msg'] = $erroq;
                        $query = " Update Inform Set state = 6,startValidity= null, endValidity = null, policyKey= null where id = $idInform ";
                        $r = odbc_exec($db, $query);
                        
                        
                        $query = " delete Parcela where i_Seg = " . $idSeg." and n_Apolice is null";
                        $r = odbc_exec($dbSisSeg, $query);
                            
                        /*
                        if($r){
                            print 'sim';
                        }else{
                            print 'não';
                        }
                        */
                        ?><script> window.location = '<?php echo $host;?>src/role/policy/Policy.php?comm=view'; </script>
                        <?php
                        
                        exit;
                    }else{
                        odbc_commit($dbSisSeg);
                    }
                                    
                    odbc_autocommit($dbSisSeg, true);
                    
                    
                    if($erroq != ""){
                                                
                        odbc_rollback($dbSisSeg);
                        odbc_rollback($db);
                        //print 'oi';
                        //odbc_autocommit($dbSisSeg, true);
                        
                        //odbc_autocommit($db, true);
                        //defaz as atualizações:
                        $erroq .= "<br<br><h3>Nenhum dado foi registrado para esta apólice<h3><br>";
                        
                        $_SESSION['msg'] = $erroq;
                        $query = " Update Inform Set state = 6,startValidity= null, endValidity = null, segundaVia = null,policyKey= null where id = $idInform ";
                        $r = odbc_exec($db, $query);
                        
                        
                        
                        
                        /*
                        if($r){
                            print 'sim';
                        }else{
                            print 'não';
                        }
                        */
                        ?><script> window.location = '<?php echo $host;?>src/role/policy/Policy.php?comm=view'; </script>
                        <?php
                        
                        exit;
                        
                    }
                    
                } // FIM GERAÇÃO APÓLICE DOMÉSTICO OU EXTERNO
                
                
            } //fim if(! $ja_foi){
        } // fim da geracao da apolice

        /*************************************************************************************/
                /*     if(!odbc_exec($dbSisSeg, */
                /*        "UPDATE Proposta SET s_Proposta=11 WHERE i_Seg=$idSeg AND n_Prop IN ". */
                /*        "(SELECT MAX(n_Prop) FROM Proposta WHERE i_Seg=$idSeg)")){ */
                /*       die("nao consegui atualizar proposta"); */
                /*     } */
             
        // Pegar o número da apólice uma vez gerada. 
        if($SistemaDestino == 1){
            $NumeroApoliceOIM = $max;        
            $apoNum = $NumeroApoliceOIM;
            
            if ($i_Produto == 2){
                $apoNum = sprintf("062%06d", $apoNum);
            }
                 
            if($prodUnit != 62){
                $apoNum .= "/".$prodUnit;
            } 
        }else{
            $rr = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$idSeg");

            if(odbc_fetch_row($rr)){
                $apoNum = odbc_result($rr, 1);
            }
                    
            $apoNum = sprintf("062%06d", $apoNum);
            
                
            if($prodUnit != 62){
                $apoNum .= "/$prodUnit";
            }
            
            
            
        }
            
        //Seguir

                $end = "$address $addressNumber - $complemento - $city - $uf";
                $endcompleto = "$address $addressNumber - $complemento";
                $prMin = number_format($prMin, 2, ',', '.');
                //$pr = number_format($pr, 2, ',', '.');
                $tx = number_format($tx, 3, '.', '');
                $txMin = number_format($txMin, 3, '.', '');

                if ($startValidity == ""){
                    $today = "";
                }else{
                    $today = "$startValidity à $endValidity";
                }

                $data = data_string($startValidity);

                if($t_Venc){
            $periodo = $t_Venc;
        }else{
            if ($pvigencia !="2"){
                switch($num_parcelas){ // periodo em meses
                    case 2:
                          $periodo = 3;
                          break;
                    case 4:
                          $periodo = 3;
                          break;
                    case 6:
                          $periodo = 2;
                          break;
                    case 7:
                    case 10:
                          $periodo = 1;
                    break;
                }
            }else{
                switch($num_parcelas){ // periodo em meses
                    case 4:
                          //$periodo = 6;
                          $periodo = 3;
                          break;
                    case 7:
                          $periodo = 1;
                          break;
                    case 8:
                          $periodo = 3;
                    break;
                }
            }
        }
    } // fim ** if

    $valPar = $pr / $num_parcelas;
    $parc = $valPar;
    $AuxParc = $valPar;
    //$parc = number_format($prMTotal / $num_parcelas, 0 "", "");

    //$pr = $num_parcelas * $valPar;
    $valExt = $numberExtensive->extensive($pr, $fMoeda);
  
    $valExtReal =  $numberExtensive->extensive($pr, 1);
  
    //************************** calcular valor final da parcela
    $VerResto = ($valPar * $num_parcelas);
    $restoParc = $pr - $VerResto;
    $restoParc1 = $VerResto - $pr;
    //valor da última parcela

    if ($restoParc > 0){
            $VlrUltparc = ($restoParc +  $valPar);
    }else if ($restoParc < 0){
            $VlrUltparc = ($valPar - $restoParc1);
    }else{
            $VlrUltparc = $parc;
    }
    //***********************************************
  
  
    
  
    //Define Valores fixos   Daqui para frente
  
    // Dados da Empresa Coface de Configurações
	$sqlquery  = "Select E.*, P.Nome as Produto, SP.c_SUSEP as CodSusep, P.i_Produto, SP.Descricao as DescSubProduto, Inf.i_Gerente, P.n_SUSEP AS n_SUSEP
		From Inform Inf
        Inner Join Produto P On
        	P.i_Produto = Inf.i_Produto
        Inner Join Empresa_Produto EP On
        	EP.i_Produto = P.i_Produto
        Inner Join Empresa E On
        	E.i_Empresa = EP.i_Empresa
        Inner Join Sub_Produto SP On
        	SP.i_Produto = Inf.i_Produto
        	And SP.i_Sub_Produto = Inf.i_Sub_Produto    
        Where Inf.id = ".$idInform;
   
    $res = odbc_exec($db,$sqlquery);
    $dados = odbc_fetch_array($res);
    
    $i_Gerente = $dados['i_Gerente'];
    $c_SUSEP   = $dados['CodSusep'];
    
    $retornoRodape =  $dados['Endereco'].' - '.
        $dados['Complemento'].' - '.
        'CEP '.formata_string('CEP',$dados['CEP']).' - '.
        $dados['Cidade'].', '.
        $dados['Estado']. ' '.
        'Tel.: '.$dados['Telefone'].'  '.
        'Fax: ' . $dados['Fax'].'  '.
        'Home Page: '. $dados['HomePage'];
                
    $disclame_retorno = $dados['Nome'].'(uma Empresa do Grupo Coface)<br/>CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: '. $dados['CodSusep'];
  
    $qry = "select c_Grupo + ' - ' + Grupo as Grupo_Susep, c_Ramo + ' - '+ Descricao as Ramo_Susep from Ramo where i_Ramo = ".$i_Ramo;
    $result      = odbc_exec($db,$qry);
    $Grupo_Susep = odbc_result($result,'Grupo_Susep');
    $Ramo_Susep  = odbc_result($result,'Ramo_Susep');
  
    // gera os pdfs
    /*************************************************************************************/
        // 03/06/2009 - Interaktiv - (Elias Vaz)
        //              Alteração: Adicionado a consulta na tabela Par_Resseguro para utilizar os valores
        //                         na inserção na tabela Resseguro e outras
        //              Solitações conforme documento anexo
        
        $csql = "select * from Par_Resseguro where (".substr(str_replace('-','',$ValidaData),0,8)." between n_Per_Inicio and n_Per_Fim)";
        $retorno1 = odbc_exec($dbSisSeg, $csql);
        
        $c_Seguro = odbc_result($retorno1,'c_Seg');
        $PerRess = odbc_result($retorno1,'p_Rsg');
        $PerComRess = odbc_result($retorno1,'p_Com_Rsg');
    //********************************************************************************************************************
        $_SESSION['c_Seguro'] = $c_Seguro;  
        
        
        if($SistemaDestino == 1){
           $parcela  = 2;
           if($i_Produto == 2){
                //require_once("segParc.php");                 
                require_once("parcela.php");
                require_once("modulePdf.php");
           }
        }else if($SistemaDestino == 2){
           $parcela  = 2;
           require_once("segParc.php");                
           require_once("parcela.php");
           require_once("modulePdf.php");
        }

        //$total = $num_parcelas * $parcExt;
        //$valor_primeira_parcela = $parcExt;
        $valPar = $AuxParc;
        // Alterado por elias Vaz 26-10-2009
        $total = ($num_parcelas * $valPar) + $restoParc;
        //$total = $num_parcelas * $valPar;
        $valor_primeira_parcela = $valPar;

        // if($total != $valor_total && $num_parcelas > 1){
        //   $dif = $total - $valor_total;
        //   $valor_primeira_parcela = ($parcExt + $dif);
        //  }else{
        //   $valor_primeira_parcela = $parcExt;
        // }

        // Encontrar o vencimento da primeira parcela
        if(! $vencFirst){
            //     $cur = odbc_exec($dbSisSeg,
            //           "SELECT d_Venc FROM Parcela WHERE i_Seg=$idSeg and s_Parcela=1 and ".
            //           "c_Coface=$c_coface and n_Prop=$nProp ORDER BY d_Venc");
            $cur = odbc_exec($dbSisSeg,
            "SELECT d_Venc FROM Parcela WHERE i_Parcela=$id_primeira_parcela");
            //echo "SELECT d_Venc FROM Parcela WHERE i_Parcela=$id_primeira_parcela";

                if(odbc_fetch_row($cur)){
                    $data = odbc_result($cur, "d_Venc");

                        if($data){
                            $vencFirst = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
                            //echo "vencfirst: $vencFirst";
                        }
                }
    }

    // Aqui será verificado o nome da corretora e código SUSEP para ser inserido nos documentos sugeridos
    //23-07-2009
    //Criado por Iteraktiv - (Elias Vaz)
    /*
    $cons = "Select a.idconsultor, a.razao, a.c_SUSEP
                from consultor a inner join Inform b on (b.idConsultor = a.idconsultor)
                where b.id = $idInform";
    $resultado = odbc_exec($db, $cons);
    */
   
        $cons = " select a.idCorretor,a.p_Comissao,a.p_Corretor,a.CorretorPrincipal,b.razao, b.c_SUSEP, b.cnpj
        from InformCorretor a
        inner join consultor b on b.idconsultor = a.idCorretor
        where a.idInform = ".$idInform. " order by a.CorretorPrincipal desc";                   
    $resultado = odbc_exec($db, $cons);

    $pularlinha = '';
    $MultiCorretor  = '';
                
    $linhas = odbc_num_rows($resultado);

    if ($linhas){   
        $connect = '';
        
        while (odbc_fetch_row($resultado)){
			If (odbc_result($resultado,'CorretorPrincipal') == 1){  
				$Corretor =  odbc_result($resultado,'razao');
                $codigoSusep = odbc_result($resultado,'c_SUSEP');
                $CNPJ_CORRETOR = odbc_result($resultado,'cnpj');
            }
            
            $corretorID .= $connect.odbc_result($resultado,'idCorretor');
            $MultiCorretor .=  $pularlinha.strtoupper(odbc_result($resultado,'razao'));
            $pularlinha = '<br>';
            $connect = ',';
        } 
    }else{
		$Corretor = '';
        $codigoSusep = '';
        $CNPJ_CORRETOR = '';
    }  

    if($SistemaDestino == 1){// OIM   = Exportação e doméstico
        $erroq = '';



        if($max){
            //Alterado por Tiago V N - Elumini - 09/11/2005
            $sql = "update Inform set n_Apolice = ".$max.",policyKey='".$key."' where id = " . $field->getField("idInform");
            $ap = odbc_exec($db, $sql);

            //$d_venc = conserta($d_venc);

            // manda primeira ou segunda carta
            if($idAnt > 0){
                $idant = 2;
            }else{
                $idant = 1;
            }

            if ($i_Produto == 1){
                require_once("apolice.php");

                require_once("condpart_Dom.php");

                if($interest){
                    require_once("condjuros_Dom.php");
                }

                require_once("carta_Dom.php");
                require_once("carta_credito_Dom.php");
                
            }else{
                require_once("apolice.php");     
                require_once("apolice_real.php");

                require_once("condpart.php");

                if($interest){
                    require_once("condjuros.php");
                }

                require_once("carta.php");
                require_once("carta_credito.php");
            }
        }else{


            $sql = "update Inform set policyKey = null, state = ".$StateInform." where id = " . $field->getField("idInform");
            $ap = odbc_exec($db, $sql);
            
            $return_flag = 1;
        }   
    }else{         
        require_once("apolice.php");     
        require_once("apolice_real.php");

        if(!$ja_foi){
            odbc_autocommit($dbSisSeg, true);
            odbc_autocommit($dbSisSeg, false);
            $ta_errado = false;
            // atualiza dados da parcela e da Base de Calculo
            $query =
                "update Parcela set n_Apolice=$max, i_BC=$idBC, n_Endosso=0 ".
                "where i_Seg=$idSeg and n_Prop=$nProp";
            $r = odbc_exec($dbSisSeg, $query);

            if(! $r){
                $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                $ta_errado = true;
            }

            $query =
                "update Base_Calculo set n_Apolice=$max, s_Doc=1, n_Endosso=0 ".
                "where i_Seg=$idSeg and n_Prop=$nProp";
            $r = odbc_exec($dbSisSeg, $query);

            if(! $r){
                $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                $ta_errado = true;
            }
        }

        //Alterado por Tiago V N - Elumini - 09/11/2005
        $sql = "update Inform set n_Apolice = ".$max." where id = " . $field->getField("idInform");

        $ap = odbc_exec($db, $sql);

        if(!$ap) {
            $erroq .= '<br>erro: '.($errorx +=1).'<br>'.$sql.'<br><br>'. odbc_errormsg($db).'<br>';
            $ta_errado = true;
        }

        //$d_venc = conserta($d_venc);

        // Aqui será executada a SP criada pelo Agnaldo para atualização das comissões e corretores nas apólices 
        //06-04-2010
        //Criado por Iteraktiv - (Elias Vaz..)     
        $sql = "select n_Apolice from Inform where id = " . $field->getField("idInform");
        $resp = odbc_exec($db, $sql);

        $n_Apolicex =  odbc_result($resp,'n_Apolice');

        $sqlSP = " exec ssI_Importar_Corretor '". $n_Apolicex."'";
        $res = odbc_exec($dbSisSeg, $sqlSP);

        if(! $res){
            $erroq1 .= '<br>erro: '.($errorx +=1).'<br>Verifique os dados de importação de corretor(es): <br>'.$sqlSP.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
            //$ta_errado = true;
        }

       //die();
        // inclui Prêmio de Resseguro na tabela PagRec
        //Moeda --
        $query =
            "insert into PagRec (i_Seg,
            c_Coface,
            n_Prop,
            i_BC,
            i_Parcela,
            n_Apolice,
            d_Vencimento,
            v_Documento,
            c_Seg,
            n_Seq_Parcela,
            n_Ramo,
            n_Sucursal,
            n_Endosso,
            n_Moeda,
            t_Doc,
            s_Pagamento,
            d_Situacao,
            d_Sistema) 
            values (".
            "$idSeg,".                // i_Seg
            "$c_coface,".             // código da Coface
            "$nProp,".                // n_Prop
            "$idBC,".                 // pegar i_BC na tabela Base_Calculo
            "$id_primeira_parcela,".  // pegar o i_Parcela da 1a parcela, que já deve estar com s_Parcela = 2(paga)
            "$max,".                  // número da apólice
            "'$d_venc',".             // dia=15, mes=3 meses na frente no mês de pagamento
            (-$valPar * (odbc_result($retorno1,'p_Rsg')/100)). ",'".      // 80% do valor da 1a parcela já paga
            // Dados Fixos
            ($c_Seguro ? $c_Seguro : 1)."',
            1, 
            49,
            62,
            0,
            '$moeda',
            1001,
            1,
            getdate(),
            getdate())";

        // die($query);               
        //echo "1a parc: $valPar";
        //echo "<pre>$query</query>";
                
        $r = odbc_exec($dbSisSeg, $query);

        if(! $r){
            $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
            $ta_errado = true;
        }

        //Moeda --
        // inclui Comissão de Resseguro na tabela PagRec
                        $query =
                          "insert into PagRec (
                           i_Seg,
                           c_Coface,
                           n_Prop,
                           i_BC,
                           i_Parcela,
                           n_Apolice,
                           d_Vencimento,
                           v_Documento,
                           c_Seg,
                           n_Seq_Parcela,
                           n_Ramo,
                           n_Sucursal,
                           n_Endosso,
                           n_Moeda,
                           t_Doc,
                           s_Pagamento,
                           d_Situacao,
                           d_Sistema)
                           values (".
                              "$idSeg,".                // i_Seg
                              "$c_coface,".             // código da Coface
                              "$nProp,".                // n_Prop
                              "$idBC,".                 // pegar i_BC na tabela Base_Calculo
                              "$id_primeira_parcela,".  // pegar o i_Parcela da 1a parcela, que já deve estar com s_Parcela = 2(paga)
                              "$max,".                  // número da apólice
                              "'$d_venc',".             // dia=15, mes=3 meses na frente no mês de pagamento
                              ($valPar * (odbc_result($retorno1,'p_Com_Rsg')/100)). ",'".      // 24% do valor da 1a parcela já paga
                               (odbc_result($retorno1,'c_Seg') ? odbc_result($retorno1,'c_Seg'): 1) ."',
                               1, 
                               49,
                               62,
                                0,
                              '$moeda',
                                12,
                                1,
                                getdate(), 
                                getdate()
                                )";
                    
                             $r = odbc_exec($dbSisSeg, $query);
                                if(! $r){
                                  $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                                  $ta_errado = true;
                                }
                
                         //Consulta o valor max de retenção para o Informe
                         $sql = "SELECT * FROM RetLiquida WHERE Ano = '". date("Y")."'";
                         $curret = odbc_exec($dbSisSeg, $sql);
                             if (odbc_fetch_row($curret)) {
                                    $v_Max_Retencao = odbc_result($curret, "Valor");
                             }else{
                
                                 $anoAnt = date("Y") - 1;
                                 $sql = "SELECT * FROM RetLiquida WHERE Ano = '$anoAnt'";
                                 $curret = odbc_exec($dbSisSeg, $sql);
                                 $v_Max_Retencao = odbc_result($curret, "Valor");
                             }
                        
                        
                
                         $query  = "insert into Resseguro (
                                   i_BC,
                                   c_Seg,
                                   p_Seguradora,
                                   p_Resseguro,
                                   p_Com_Resseguro,
                                   v_Max_Retencao,
                                   p_Exc_Danos)
                                   values ('";
                                     $query .= $idBC."','";
                                     $query .= (odbc_result($retorno1,'c_Seg') ? odbc_result($retorno1,'c_Seg') : 1) ."',";
                                     $query .= "100,'"; // Valor fixo seguradora ????
                                     $query .= (odbc_result($retorno1,'p_Rsg') ? odbc_result($retorno1,'p_Rsg') : 0)."','";
                                     $query .= (odbc_result($retorno1,'p_Com_Rsg')? odbc_result($retorno1,'p_Com_Rsg'): 0) ."',";
                                     $query .= (odbc_result($retorno1,'v_Max_Retencao') ? odbc_result($retorno1,'v_Max_Retencao') :0) .",'";
                                     $query .= (odbc_result($retorno1,'p_Exc_Danos') ? odbc_result($retorno1,'p_Exc_Danos') : 0)."')";
                            
                         $r = odbc_exec($dbSisSeg, $query);
                   
                            if(! $r){
                              $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                              $ta_errado = true;
                            }
                       
                         // inclui dados em Parcela_Resseguro
                         $x = odbc_exec($dbSisSeg, "select * from Parcela_Resseguro where i_Parcela=$id_primeira_parcela");
                            if(! odbc_fetch_row($x)){
                                $query = "insert into Parcela_Resseguro (i_Parcela, v_Resseguro, v_Com_Resseguro, d_Venc, s_PR,";
                                $query .= "c_Seg, d_Situacao) values (".$id_primeira_parcela.",";     // i_Parcela
                                //($valPar * 0.8). ",".        // 80% do valor da parcela
                                //($valPar * 0.24). ",".       // 24% do valor da parcela
                                $query .= ($valPar * (odbc_result($retorno1,'p_Rsg')/100)). ",";       // 80% do valor da parcela
                                $query .= ($valPar * (odbc_result($retorno1,'p_Com_Rsg')/100)). ",";       // 24% do valor da parcela
                                $query .= "'$d_venc',";       // dia=15, mes=3 meses na frente no mês de pagamento
                                $query .= "1,'";                        // Colocar 1 para a 1a parcela e 0 para as outras
                                // valores fixos
                                $query .= (odbc_result($retorno1,'c_Seg') ? odbc_result($retorno1,'c_Seg') : 1) ."', getdate())";
                                
                                $r = odbc_exec($dbSisSeg, $query);
                                   if(! $r){
                                      $erroq .= '<br>erro: '.($errorx +=1).'<br>'. $query.'<br><br>'. odbc_errormsg($dbSisSeg).'<br>';
                                      $ta_errado = true;
                                   }
                             
                             }
                             if($ta_errado){
                                odbc_rollback($dbSisSeg);
                             }else{
                                odbc_commit($dbSisSeg);
                             }
                          odbc_autocommit($dbSisSeg, true);
                               
                
                        odbc_exec($db, "update Inform set policyKey='$key' where id=". $field->getField("idInform"));

                        require_once("condpart.php");
                       
                       
                         if($interest){
                            require_once("condjuros.php");
                         }
                         
                         // manda primeira ou segunda carta
                         if($idAnt > 0){
                            $idant = 2;
                         }else{
                            $idant = 1;
                         }
                    
                    
                         require_once("carta.php");
                         require_once("carta_credito.php");
      }
   }// Fecha se Existe Apólice
   
   if($erroq != ''){
        
        // Desfazer a geração de apólices  caso algum erro seja detectado
        odbc_rollback($dbSisSeg);
        odbc_rollback($db);
        odbc_rollback($dbOIM);
        
        $erroq .= "<br<br><h3>Nenhum dado foi registrado para esta apólice<h3><br>".$csql;
                    
        $_SESSION['msg'] = $erroq;
        $query = " Update Inform Set n_Apolice = null, state = 6,startValidity= null, endValidity = null, policyKey= null where id = $idInform ";
        $r0 = odbc_exec($db, $query);
         
        $query = " delete from PagRec where i_Seg = ". $idSeg ." and n_Apolice = '".$max."'";
        $r1 = odbc_exec($dbSisSeg, $query);
        
        $query = " delete from Base_Calculo where i_Seg = ". $idSeg . " and n_Apolice is null and i_BC = ". $idBC."";
        $r2 = odbc_exec($dbSisSeg, $query);
        
        $query = " delete from Parcela_Resseguro where i_Parcela = ".$id_primeira_parcela;
        $r3 = odbc_exec($dbSisSeg, $query);

        $query = " delete from Parcela where i_Seg = ". $idSeg ." and n_Apolice is null and n_Seq_Parcela <> 1 and i_BC = '".$idBC."'";
        $r4 = odbc_exec($dbSisSeg, $query);
        
        $query = " delete from Endosso where n_Apolice = ". $max. "";
        $r5 = odbc_exec($dbSisSeg, $query);
        
        $query = " delete from Resseguro where i_BC = ". $idBC. "";
        $r5 = odbc_exec($dbSisSeg, $query);
        
        $query = " delete from Apolice where n_Apolice = ". $max. "";
        $r6 = odbc_exec($dbSisSeg, $query);

        $query = " delete from Corretor_Calculo  where i_BC = '". $idBC ."' and i_Corr in(". $corretorID .")";
        $r6 = odbc_exec($dbSisSeg, $query);

        odbc_autocommit($dbSisSeg, true);
        
        ?><script> window.location = '<?php echo $host;?>src/role/policy/Policy.php?comm=view'; </script>
        <?php
        
        exit;
   }
?>