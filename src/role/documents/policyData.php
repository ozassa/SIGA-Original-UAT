<?php 
    
    include_once("docFunctions.php");
    include_once("../consultaCoface.php");
    include_once("../../../gerar_pdf/MPDF45/mpdf.php");

    $numberExtensive = new NumberUtils ();

    $meses = array("01" => "Janeiro", 
                    "02" => "Fevereiro",
                    "03" => "Março", 
                    "04" => "Abril", 
                    "05" => "Maio", 
                    "06" => "Junho", 
                    "07" => "Julho", 
                    "08" => "Agosto", 
                    "09" => "Setembro", 
                    "10" => "Outubro", 
                    "11" => "Novembro", 
                    "12" => "Dezembro");    

    $diaT = date ('d');
    $mesT = date ('m');
    $dMoeda = date ("Y-m-d", mktime (0,0,0, $mesT, $diaT - 8, date("Y"))). ' 00:00:00.000'; //formata data de ontem
    $errorx = 0;
    $erroq = '';
    
    $ano = date("y", time());
    $newKey = time(). session_id();

    $sqlParc = "SELECT P.i_Parcela As IdParcela, 
                        P.n_Parcela As NumParcela, 
                        P.d_Vencimento As DataVencimento, 
                        cast(P.v_Parcela as money) As ValorParcela,
			cast(P.v_IOF as money) As ValorIOF  
                    FROM Parcela P 
                    WHERE P.i_Inform = ".$idInform." AND P.t_Parcela = 100  AND P.n_Endosso = 0 
                    ORDER BY P.n_Parcela";
    $resultParc = odbc_exec($db, $sqlParc);

    $dadosParc = array();

    while(odbc_fetch_row($resultParc)){
        $dadosParc[] = array("idPar" => odbc_result($resultParc,'IdParcela'), 
                                "valPar" => odbc_result($resultParc,'ValorParcela'), 
                                "valIOF" => odbc_result($resultParc,'ValorIOF'), 
                                "parcela" => odbc_result($resultParc,'NumParcela'), 
                                "vencimento" => date("d/m/Y", strtotime(odbc_result($resultParc, 'DataVencimento')))
                            );
    }

    $numResultParc = count($dadosParc);    

    $sqlquery  = "SELECT 
			Inf.i_Ramo,
			Inf.i_Empresa,
			Inf.name, 
			Inf.cnpj, 
			Inf.ie, 
			Inf.address,
			Inf.addressComp,
			Inf.bairro,
			Inf.cep as cep,
			Inf.city, 
			Inf.uf,
			Inf.tel, 
			Inf.fax, 
			Inf.email, 
			Inf.ocupationContact,
			Inf.warantyInterest,
			Inf.policyKey, 
			Inf.prodUnit,
			Inf.t_Vencimento, 
                        Inf.DataPrimeiraParcela as d_venc,
                        convert(varchar, Inf.DataPrimeiraParcela, 103) as DataPrimeiraParcela,
                        Inf.i_Seg,
                        ISNULL(Inf.idAnt, 0) AS idAnt,
                        Inf.contrat,
                        Inf.numParc,
                        Inf.Num_Parcelas,
                        Inf.prMin,
                        Inf.txRise,
                        Right('00' + cast(Inf.nProp as varchar), 2) As nProp,
                        Inf.state,
                        Inf.startValidity,
			Inf.endValidity,
                        Inf.i_Produto,
                        Inf.contact,
                        Inf.ocupationContact,
                        Inf.percCoverage,
                        Inf.pvigencia,
                        Inf.currency,
			Inf.prMTotal,
			IsNull(Inf.prAux, 0) as prAux,
                        Inf.dateEmission,
                        Inf.dateAceit,
                        Inf.Periodo_Vigencia,

			C.razao As Nome_Corretor,
			C.cnpj As CNPJ_Corretor,
			C.c_SUSEP as c_SUSEP_Corretor
		FROM Inform Inf
		Left Join consultor C on C.idconsultor = Inf.idConsultor
		WHERE Inf.id = ".$idInform;
    $res = odbc_exec($db, $sqlquery);


    $startValidity      = "";
    $endValidity        = "";
    $vencFirst          = "";

    $i_Ramo                 = odbc_result($res, "i_Ramo");
    $i_Empresa              = odbc_result($res, "i_Empresa");
    $name                   = odbc_result($res, "name");
    $cnpj                   = odbc_result($res, "cnpj");
    $cnpj                   = substr($cnpj, 0, 2). ".".
				substr($cnpj, 2, 3). ".".
                                substr($cnpj, 5, 3). "/".
                                substr($cnpj, 8, 4). "-".
                                substr($cnpj, 12);
    $ie                     = odbc_result($res, "ie");
    $address                = odbc_result($res, "address");
    $complemento            = odbc_result($res, "addressComp");
    $bairro            	    = odbc_result($res, "bairro");
    $cep                    = odbc_result($res, "cep");
    $tel                    = odbc_result($res, "tel");
    $fax                    = odbc_result($res, "fax");
    $email                  = odbc_result($res, "email");
    $oContact               = odbc_result($res, "ocupationContact");
    $city                   = odbc_result($res, "city");
    $uf                     = substr(odbc_result($res, "uf"), 0, 2);
    $key                    = odbc_result($res, "policyKey");
    $prodUnit               = odbc_result($res, "prodUnit");
    $interest               = odbc_result($res, "warantyInterest");
    $t_Venc                 = odbc_result($res, "t_Vencimento");
    $primeirovencimento     = odbc_result($res, "DataPrimeiraParcela");
    $idSeg                  = odbc_result($res, "i_Seg");
    $idAnt                  = odbc_result($res, "idAnt");
    $nProp                  = odbc_result($res, "nProp");
    $c_coface               = odbc_result($res, "contrat");
    $contract               = substr(odbc_result($res, "contrat"), 0, 3) . "." . substr(odbc_result($res, "contrat"), 3, 3) . "-$nProp";
    $numParc                = odbc_result($res, "numParc");
    $StateInform            = odbc_result($res, "state");
    $ValidaData             = odbc_result($res, "startValidity");
    $dataFim                = odbc_result($res, "endValidity");
    $i_Produto              = odbc_result($res, "i_Produto");
    $dateEmition            = odbc_result($res, "dateEmission");
    $contato                = odbc_result($res, "contact");
    $oContact               = odbc_result($res, "ocupationContact");
    $d_venc                 = odbc_result($res, "d_venc");
    $vigencia               = odbc_result($res, "pvigencia");
    $moeda                  = odbc_result($res, "currency");
    $prMTotal               = odbc_result($res, "prMTotal");
    $v_IOF                  = odbc_result($res, "prAux");
    $pr 		    = number_format(odbc_result($res, "prMTotal"),2,".","");
    $extPremioMinimo 	    = $numberExtensive->extensive(number_format($pr,0,'.',''),$fMoeda);
    $dateAceit              = odbc_result($res, "dateAceit");
    $Periodo_Vigencia       = odbc_result($res, 'Periodo_Vigencia');
    $cobertura              = number_format(odbc_result($res, "percCoverage"), 0, "", "");
    $datahoje               = dataextenso1(date('Y-m-d', strtotime($dateEmition)));
    $Num_Parcelas           = odbc_result($res, "Num_Parcelas") ? odbc_result($res, "Num_Parcelas") : $numParc;
    $parc                   = (odbc_result($res, "prMin") * (1 + odbc_result($res, "txRise")) / $Num_Parcelas);
    $parcExt                = number_format ($parc, 2, ".", "");

	$Corretor 		= odbc_result($res,"Nome_Corretor");
	$codigoSusep 		= odbc_result($res,"c_SUSEP_Corretor");
	$CNPJ_CORRETOR 		= odbc_result($res,"CNPJ_Corretor");

    $inicio_vigencia = $ValidaData;
    $data = $ValidaData;

    $num_parcelas = ($Num_Parcelas ? $Num_Parcelas : $numParc);

    if (!$key) {
        $key = $newKey;

        $sql = "UPDATE Inform 
                    SET policyKey = '".$key."' 
                    WHERE id = ".$idInform;
        odbc_exec($db, $sql);
    }

    if(!$idSeg){
        $idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id = '".$idAnt."' "), 1);
    } 

    if($t_Venc == 1){
        $t_Vencimento  = 30; // dias
    }else if($t_Venc == 2){
        $t_Vencimento  = 60; // dias
    }else if($t_Venc == 3){
        $t_Vencimento  = 90; // dias
    }else if($t_Venc == 4){
        $t_Vencimento  = 180; // dias
    }

    if($t_Venc == 1){
        $t_DescVencimento = 'mensais';
    }else if ($t_Venc == 2){
        $t_DescVencimento = 'bimestrais';
    }else if ($t_Venc == 3){
        $t_DescVencimento = 'trimestrais';
    }else if ($t_Venc == 4){
        $t_DescVencimento = 'semestrais';
    }  

    if ($vigencia == "") {
        $pvigencia = "1";
    } else if ($vigencia == "1") {
        $pvigencia = "1";
    } else {
        $pvigencia = "2";
    }

    if($Periodo_Vigencia){
        $pvigencia = $Periodo_Vigencia; 
    }

    if ($moeda == "1") {
        $ext = "R$ ";
        $DescMoeda = "REAIS";
        $extmoeda = "real (R$)";
        $fMoeda = "1";
    } else if ($moeda == "2") {
        $ext = "USD ";
        $DescMoeda = "D&Oacute;LARES NORTE-AMERICANOS";
        $extmoeda = "d&oacute;lar norte-americano (US$)";
        $fMoeda = "2";
    } else if ($moeda == "6") {
        $ext = "EUR ";
        $DescMoeda = "EUROS";
        $extmoeda = "euro (€)";
        $fMoeda = "6";
    }

    if (date('Y-m-d', strtotime($dateEmition)) >= "2009-10-26") {
        $emiteTextoNovo = 1;
    } else {
        $emiteTextoNovo = 0;
    }

    $dataEmissao = '';

    if ($data) {
        $d = substr($data, 8, 2);
        $m = $meses[substr($data, 5, 2)];
        $a = substr($data, 0, 4);

        $startValidity = $d." de ".$m." de ".$a;

	$d = substr($dataFim, 8, 2);
        $m = $meses[substr($dataFim, 5, 2)];
        $a = substr($dataFim, 0, 4);

	$endValidity = $d." de ".$m." de ".$a;

        $dataEmissao = $dateEmition;
    }
     
    if ($Periodo_Vigencia) {  
        $dataf1 =  SomarData(date('d/m/Y', strtotime($dateAceit)),"+", 0, $Periodo_Vigencia, 0);
        $dataf2 =  SomarData(date('d/m/Y', strtotime($dataf1)),"-", 1, 0, 0);
        
        $fimVigencia = $dataf2; 
        
        $xd = substr($fimVigencia, 8, 2);
        $ym = substr($fimVigencia, 5, 2);
        $za = substr($fimVigencia, 0, 4);

        $data = Convert_Data_Geral($fimVigencia);
    } else {
        $data  = date("Y-m-d", mktime(0, 0, 0, $m, $d - 1, $a + $pvigencia));
    }

    $m_fim = $meses[substr($data, 5, 2)];



    $sqlquery  = "SELECT CP.SistemaDestino,
                            CASE 
                                WHEN SP.Descricao LIKE '%Tradeliner%' THEN 
                                    'RTL' 
                                ELSE 
                                    'GBL' 
                            END AS tpProd, 
				CASE 
                                WHEN SP.Descricao LIKE '%Tradeliner%' THEN 
                                    1 
                                ELSE 
                                    2 
                            END AS SubProduto, 
                            CP.v_IOF, 
                            CP.p_Multa, 
                            CP.p_Juros_Dia,
                            Inf.policyKey 
                    FROM Inform Inf 
                        INNER JOIN Config_Produto CP ON CP.i_Produto = Inf.i_Produto
                        INNER JOIN Sub_Produto SP On SP.i_Produto = Inf.i_Produto AND SP.i_Sub_Produto = Inf.i_Sub_Produto 
                    WHERE Inf.id = ".$idInform;
    $res = odbc_exec($db, $sqlquery);

    $linha              = odbc_num_rows($res);
    $SistemaDestino     = odbc_result($res, "SistemaDestino");
    $p_Multa            = odbc_result($res, "p_Multa");
    $p_Juros_Dia        = odbc_result($res, "p_Juros_Dia");
    $tpProd             = odbc_result($res, "tpProd");
    $SubProduto         = odbc_result($res, "SubProduto");
    $policyKey          = odbc_result($res, "policyKey");

    //ver o ID anterior
    $sl = "SELECT inf.id, infant.n_Apolice as Anterior, inf.n_Apolice as Atual 
                FROM Inform inf
                    LEFT JOIN Inform infant ON infant.id = inf.idAnt
                WHERE inf.id = ".$idInform;
    $rrs =  odbc_exec($db, $sl);
    
    $n_Anterior = odbc_result($rrs, "Anterior");
    $n_Apolice = odbc_result($rrs, "Atual");

    // Países
    $sqlP = "SELECT Distinct Country.name as country_name, Country.score
                FROM Inform inf 
                    INNER JOIN VolumeSeg v ON inf.id = v.idInform 
                    INNER JOIN Country ON v.idCountry = Country.id
                WHERE Country.score < 8 and inf.id = ".$idInform;
    $cur =  odbc_exec($db, $sqlP);

    $countries = "";
    $first = true;

    while (odbc_fetch_row ($cur)) {
        $countries .= ($first ? "" : ", ").odbc_result($cur, "country_name");
        
        if ($first) {
            $first = false;
        }
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
                    inf.i_Ramo,inf.i_Empresa,inf.startValidity as DataInicio,inf.i_Produto, inf.v_LMI, 
                    inf.Renovacao_Tacita,currency, SP.c_SUSEP, Case When SP.Descricao Like '%Tradeliner%' Then 1 Else 2 End As SubProduto
            FROM Users u 
                INNER JOIN Insured i ON u.id = i.idResp 
                INNER JOIN Inform inf ON inf.idInsured = i.id 
                INNER JOIN Region reg ON inf.idRegion = reg.id 
                INNER JOIN Sector ON inf.idSector = Sector.id 
                INNER Join Sub_Produto SP ON SP.i_Produto = inf.i_Produto AND SP.i_Sub_Produto = inf.i_Sub_Produto 
                LEFT  Join Importer ON inf.id = Importer.idInform 
                LEFT  Join Country ON Importer.idCountry = Country.id 
            WHERE inf.id = ".$idInform;
    $cur = odbc_exec($db, $csql);

    $login                  = odbc_result($cur, 1);
    $respName               = odbc_result($cur, 2);
    $idRegion               = odbc_result($cur, 3);
    $DataInicio             =  odbc_result($cur, 'DataInicio');
    $txMin                  = odbc_result($cur, 5);
    $prMin                  = odbc_result($cur, 6);

    $txMTotal               = odbc_result($cur, 'txMTotal');
    $ValorLMI               = odbc_result($cur, 'v_LMI');
    $ExtValorLMI            = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$fMoeda);
    $renovacao_Tacica       = odbc_result($cur,'Renovacao_Tacita');
    $descrip                = odbc_result($cur, 16);
    $period                 = odbc_result($cur, 18);
    $DescrNatureza          = odbc_result($cur, 'products');

    // encontrar o número de propostas

    $contato                = odbc_result($cur, 22);
    $txRise                 = odbc_result($cur, 23);
    $extAnalise             = $numberExtensive->extensive(number_format(odbc_result($cur, "txAnalize"), 2, ".", ""), 1);
    $taxa_analise           = number_format(odbc_result($cur, "txAnalize"), 2, ',', '.');
    $extMonit               = $numberExtensive->extensive(number_format (odbc_result($cur, "txMonitor"), 2, ".", ""), 1);
    $taxa_monit             = number_format(odbc_result($cur, "txMonitor"), 2, ',', '.');
    $limite                 = odbc_result($cur, "limPagIndeniz");
    $cobertura              = odbc_result($cur, 30);
    $extcob                 = $numberExtensive->porcentagem(odbc_result($cur, 30));
    $mBonus                 = odbc_result($cur, 31);
    $mPart                  = odbc_result($cur, 'mPart');
    $perBonus               = odbc_result($cur, 32);
    $perPart0               = odbc_result($cur, 33);
    $perPart1               = odbc_result($cur, 34);
    $pLucro                 = odbc_result($cur, 35);

    $tx                     = $txMTotal;

    if (odbc_result($cur, 36) == "" Or odbc_result($cur, 36) == "0.00") {
        if ($moeda == "2") {
            $nas = "500.00";
        } else if ($moeda == "6") {
            $nas = "400.00";
        }
    } else {
        $nas = odbc_result($cur, 36);
    }

    $extnas                 = strtolower($numberExtensive->extensive(number_format($nas,2, '.',''),$fMoeda));
    $tipodve                = odbc_result($cur, 37);
    $extpart0               = $numberExtensive->porcentagem($perPart0);
    $extpart1               = $numberExtensive->porcentagem($perPart1);
    $addressNumber          = odbc_result($cur, 38);
    $ga                     = odbc_result($cur, 39);
    $SubProduto 	    = odbc_result($cur, "SubProduto");

    if (($ga=="0") || ($ga=="")) {
        $susep = "15.414005218/2005-89";
        $cp    = "CP/RC/06-01";
    } else {
        $susep = "15.414004768/2004-08";
        $cp    = "CP/GA/07-01";
    }

    $IC_APLICA = odbc_exec($db, "select IC_APLICA from TB_TAXA_INFORME_BB where ID_INFORME=$idInform");
    $IC_APLICA_TAXA = odbc_result($IC_APLICA, 1);


    //# Taxa de prêmio + 10%
    if($IC_APLICA_TAXA == "1") {
        $tx = $tx;
    } else {
        $tx = $tx;
    }

    $exttx = $numberExtensive->porcentagem($tx);

    if($policyKey == ""){
        $ja_foi = 0;
    } else {
        $ja_foi = 1;
    }

    // Pegar o número da apólice uma vez gerada. 
    $apoNum = $n_Apolice;
    
    if ($i_Produto == 2){
        $apoNum = sprintf("062%06d", $apoNum);
    }
         
    if($prodUnit != 62){
        $apoNum .= "/".$prodUnit;
    } 

    $end = $address." ".$addressNumber." - ".$complemento." - ".$city." - ".$uf;
    $endcompleto = $address." ".$addressNumber." - ".$complemento." - ".$bairro;

    $prMin = number_format($prMin, 2, ',', '.');
    $tx = number_format($tx, 3, '.', '');
    $txMin = number_format($txMin, 3, '.', '');

    if ($startValidity == "") {
        $today = "";
    } else {
        $today = $startValidity." &agrave; ".$endValidity;
    }

    $data = $startValidity;

    if($t_Venc){
        $periodo = $t_Venc;
    } else {
        if ($pvigencia != "2") {
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
        } else {
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

    $valPar = $pr / $num_parcelas;
    $parc = $valPar;
    $AuxParc = $valPar;

    $valExt = $numberExtensive->extensive($pr, $fMoeda);
  
    $valExtReal =  $numberExtensive->extensive($pr, 1);
  
    //************************** calcular valor final da parcela
    $VerResto = ($valPar * $num_parcelas);
    $restoParc = $pr - $VerResto;
    $restoParc1 = $VerResto - $pr;
    //valor da última parcela

    if ($restoParc > 0) {
        $VlrUltparc = ($restoParc +  $valPar);
    } else if ($restoParc < 0) {
        $VlrUltparc = ($valPar - $restoParc1);
    } else {
        $VlrUltparc = $parc;
    }

    $parcela  = 2;

    // Dados da Empresa Coface de Configurações
    $sqlquery  = "Select E.*, P.Nome as Produto, SP.c_SUSEP as CodSusep, P.i_Produto, SP.Descricao as DescSubProduto, Inf.i_Gerente, P.n_SUSEP AS n_SUSEP, Inf.contrat, Inf.nProp, Inf.Prazo_Nao_Intencao_Renov 
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
   
    $res = odbc_exec($db, $sqlquery);
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
                
    //$disclame_retorno = $dados['Nome'].'(uma Empresa do Grupo Coface)<br/>CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: '. $dados['CodSusep'];
    $disclame_retorno = '*A Seguradora Brasileira de Crédito à Exportação S.A foi incorporada pela Coface do Brasil Seguros de Crédito S/A conforme Portaria 
	SUSEP n° 7.640 de 15/06/2020, que está, desta forma, autorizada a comercializar o produto SUSEP no. '. $dados['CodSusep'];
  
    $qry = "SELECT c_Grupo + ' - ' + Grupo AS Grupo_Susep, c_Ramo + ' - '+ Descricao AS Ramo_Susep 
                FROM Ramo 
                WHERE i_Ramo = ".$i_Ramo;
    $result      = odbc_exec($db,$qry);

    $Grupo_Susep = odbc_result($result, 'Grupo_Susep');
    $Ramo_Susep  = odbc_result($result, 'Ramo_Susep');  

    $cons = "SELECT a.idCorretor, a.p_Comissao, a.p_Corretor, a.CorretorPrincipal, b.razao, b.c_SUSEP, b.cnpj
                FROM InformCorretor a
                    INNER JOIN consultor b ON b.idconsultor = a.idCorretor
                WHERE a.idInform = ".$idInform." 
                ORDER BY a.CorretorPrincipal DESC";
    $rscons = odbc_exec($db, $cons);

    $corretorID = '';
    $MultiCorretor  = '';
    $pularlinha = '';
    $connect = '';
                
    $linhas = odbc_num_rows($rscons);

    if ($linhas) {
        
        while (odbc_fetch_row($rscons)){
            if(odbc_result($rscons,'CorretorPrincipal') == 1){
                $Corretor =  odbc_result($rscons,'razao');
                $codigoSusep = odbc_result($rscons,'c_SUSEP');
                $CNPJ_CORRETOR = odbc_result($rscons,'cnpj');
            }
            
            $corretorID .= $connect.odbc_result($rscons,'idCorretor');
            $MultiCorretor .=  $pularlinha.strtoupper(odbc_result($rscons,'razao'));
            $pularlinha = '<br>';
            $connect = ',';
        }
    }

    if ($idAnt > 0) {
        $idant = 2;
    } else {
        $idant = 1;
    }

    $v_compra = 0;
?>