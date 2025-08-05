<?php //Criado Hicom 25/10/2004 (Gustavo)

//echo("<BR>Dir: ". $idInform." fim<BR>");
//echo("<BR>PA: ". $PA." fim<BR>");
//echo("<BR>Desconto: ". $desconto." fim<BR>");

require_once("../../dbOpen.php");

if(! function_exists('arruma_cnpj')){
	function arruma_cnpj($c){
    	if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
      		return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
	    }
    return $c;
	}
}
 
function geraPdfFaturas($db, $idInform) {
	
	$ok = true; 
	$numberExtensive = new NumberUtils();
	
	require_once("../../pdfConf.php");
	
	$sql =	"SELECT		name, address, city, uf, cep, cnpj, Ga, currency ".
			"FROM		Inform ".
			"WHERE		id = ".$idInform;
	$cur=odbc_exec($db,$sql);
	$nomSeg = odbc_result($cur,"name");
	$lugSeg = odbc_result($cur,"address")." - ".odbc_result($cur,"city")." - ".odbc_result($cur,"uf");
    $ga = odbc_result($cur, "Ga");
    if (($ga=="0") || ($ga=="")){
    $susep = "15.414005212/2005-89";
    $cp    = "CP/RC/06-01";
    }else{
    $susep = "15.414004768/2004-08";
    $cp    = "CP/GA/07-01";
    }
	$cep = odbc_result($cur,"cep");
    $cepSeg = substr($cep, 0, 5);
    if(! preg_match("\.", $cepSeg)){
      $cepSeg = substr($cepSeg, 0, 2). '.'. substr($cepSeg, 2, 3);
    }else{
      $inc = 1;
      $cepSeg = substr($cep, 0, 6);
    }
    if(! preg_match("-", $cep)){
      $cep = "$cepSeg-". substr($cep, 5);
    }else{
      $cep = "$cepSeg-". substr($cep, 6 + $inc);
    }
	
	$cnpjSeg = odbc_result($cur,"cnpj");
	
    $currency = odbc_result($cur, "currency");
    if ($currency == 2) {
       $extMoeda = "US$";
       $ext      = "DÓLARES NORTE-AMERICANOS";
    }elseif ($currency == 6) {
       $extMoeda = "€";
       $ext      = "EURO";
    }
    
	$sql =	"SELECT		count(id) qtd, sum(valor) tot ".
			"FROM		PADet ".
			"WHERE		idInform = ".$idInform." ".
			"GROUP BY 	idInform ";
	$cur=odbc_exec($db,$sql);
	$numPre = odbc_result($cur,"qtd");
	
	$sql =	"SELECT		* ".
			"FROM		PADet ".
			"WHERE		idInform = ".$idInform." ".
			"ORDER BY 	num ";
	$cur=odbc_exec($db,$sql);
	
	while (odbc_fetch_row($cur)) {
		$valPar = odbc_result($cur,"valor");
		$dataVenc = odbc_result($cur,"vencimento");
		$numPar = odbc_result($cur,"num");
		$valParExt = $numberExtensive->extensive(number_format($valPar, 2, '.', ''),$currency);
		
		$key = session_id().time().$numPar."PA.pdf";
		
		$sql =	"UPDATE		PADet ".
				"SET		arquivoFatura = '".$key."' ".
				"WHERE		idInform = ".$idInform." ".
				"			AND num = ".$numPar;
		odbc_exec($db,$sql);
		
		$h = new Java ('java.util.HashMap');
		
	//	extract($_SESSION);
		$h->put('dir', $pdfDir);
		$h->put ('key', $key);
		$h->put ('fatNum', "");
		$h->put ('apoNum', "");
		$h->put ('endNum', "");
		$h->put ('proNum', "");
		$h->put ('lugSeg', $lugSeg);
		$h->put ('cepSeg', $cep);
		$h->put ('nomSeg', $nomSeg);
		$h->put ('cnpjSeg', arruma_cnpj($cnpjSeg));
		$h->put ('valPar', number_format($valPar, 2, ',', '.'));
		$h->put ('valParExt', $valParExt);
		$h->put ('valPre', "");
		$h->put ('valPreExt', "");
		$h->put ('numPre', $numPre);
		if($numPre > 1){
			$h->put ('demais_prestacoes', "demais".number_format($demais_prestacoes, 2, ',', '.'));
		}
        $h->put ('susep', $susep. '');
        $h->put ('cp', $cp. '');
        $h->put ('dataVenc', substr($dataVenc, 8, 2)."/".substr($dataVenc, 5, 2)."/".substr($dataVenc, 0, 4));
		$h->put ('vigApo', "");
		$h->put ('numPar', $numPar);
		$h->put ('contract', "");
		$h->put ('extMoeda', $extMoeda);
		$h->put ('ext', $ext);
		if($segundavia){
			$h->put('segundavia', '1');
		}
		else{
			$h->put('segundavia', '0');
		}
		
		$prop = new Java ('PA', $h);
		if ($prop == null) {
			$ok = false;
		}
		else {
			$prop->generate();
		}
	}
	return $ok;
}


function gravaFaturasSisseg($dbSisSeg, $db, $idInform, $PA, $totExpInfEst, $volumePA,
							$volumeExcluido, $volumePA, $txPremio, $PremioPago,
							$PremioEfetivo, $desconto, $volumeMora) {

	$ok = true;

	$sql = 	"SELECT 	i_Seg, nProp  ".
			"FROM 		Inform ".
			"WHERE 		id = $idInform ";
//echo ("<BR>".$sql."<BR>");
	$cur=odbc_exec($db,$sql);
	if (!$cur)
		$ok = false;
	$i_Seg = odbc_result($cur,"i_Seg");
	$n_Prop = odbc_result($cur,"nProp");

	if ($i_Seg && $n_Prop) {
	

//1-) Verificar se existe o valor da moeda de ontem (em Valor_Moeda)
		
		$sql = 	"SELECT 	v_Compra ".
				"FROM 		Valor_Moeda ".
				"WHERE 		day(d_Cotacao) = day(getdate()-1) ".
				"			AND month(d_Cotacao) = month(getdate()-1) ".
				"			AND year(d_Cotacao) = year(getdate()-1) ";
//echo ("<BR>".$sql."<BR>");
		$cur=odbc_exec($dbSisSeg,$sql);
		if (!$cur)
			$ok = false;
		$v_Compra = odbc_result($cur,"v_Compra");
	
		if ($v_Compra) {

//2-) o número do endosso será:

			$sql = 	"SELECT 	n_Sucursal, ".
					"			n_Ramo, ".
					"			n_Apolice, ".
					"			Renova_n_Apolice, ".
					"			p_Cobertura, ".
					"			d_Ini_Vig, ".
					"			d_Fim_Vig, ".
					"			d_Ult_Fec, ".
					"			n_Filial, ".
					"			c_Coface, ".
					"			n_Mod, ".
					"			v_Frac ".
					"FROM 		Base_Calculo ".
					"WHERE 		i_Seg = $i_Seg ".
					"			AND n_Prop = $n_Prop ";
//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($dbSisSeg,$sql);
			if (!$cur)
				$ok = false;

			$n_Sucursal = odbc_result($cur,"n_Sucursal");
			$n_Ramo = odbc_result($cur,"n_Ramo");
			$n_Apolice = odbc_result($cur,"n_Apolice");
			$Renova_n_Apolice = odbc_result($cur,"Renova_n_Apolice");
			if (! $Renova_n_Apolice)
				$Renova_n_Apolice = "NULL";
			$p_Cobertura = odbc_result($cur,"p_Cobertura");
			if (! $p_Cobertura)
				$p_Cobertura = "NULL";
			$d_Ini_Vig = odbc_result($cur,"d_Ini_Vig");
			$d_Fim_Vig = odbc_result($cur,"d_Fim_Vig");
			$d_Ult_Fec = odbc_result($cur,"d_Ult_Fec");
			$n_Filial = odbc_result($cur,"n_Filial");
			$c_Coface = odbc_result($cur,"c_Coface");
			$n_Mod = odbc_result($cur,"n_Mod");
			$v_Frac = odbc_result($cur,"v_Frac");
			if (! $v_Frac)
				$v_Frac = "NULL";
			
			$sql = 	"SELECT 	MAX(n_Endosso) + 1 n_Endosso ".
					"FROM 		Endosso ".
					"WHERE 		n_Sucursal = $n_Sucursal ".
					"			AND n_Ramo = $n_Ramo ";
// Andréa achou melhor retitar esse teste.
//					"			AND n_Apolice = $n_Apolice "; 
//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($dbSisSeg,$sql);
			if (!$cur)
				$ok = false;
			$n_Endosso = odbc_result($cur,"n_Endosso");

//nao entendi pq ele faz esse select com o n_Apolice, pq na verdade o numero
//do endosso é um sequencial geral pra todos os clientes.

//3-) Gera novo endosso sem i_BC:

			$sql = 	"INSERT 	Endosso ( ".
					"				n_Sucursal, ".
					"				n_Ramo, ".
					"				n_Apolice, ".
					"				n_Endosso, ".
					"				n_User, ".
					"				d_Endosso ) ".
					"VALUES 	(	$n_Sucursal, ".
					"				$n_Ramo, ".
					"				$n_Apolice, ".
					"				$n_Endosso, ".
					"				66, ".
					"				GETDATE() ) "; 
//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($dbSisSeg,$sql);      
			if (!$cur)
				$ok = false;
/*
* o n_Sucursal e o n_Ramo deverão ser consultados na tabela Base_Calculo
(utilize o i_Seg)
* o n_Apolice deverá ser consultado na tabela Base_Calculo (use o i_Seg,
n_Prop)
* para o n_Endosso utilize a consulta acima
* o n_User deverá ser 66. Mas o número do usuário que calculou a PA e o que
gerou o endosso deverá ficar gravado no SIEX.
*/

//4-) Insere em Base_Calculo e ...
//5-) Atualiza o valor de i_BC(que foi criado no item acima) no registro do endosso:

			$sql = 	"INSERT 	Base_Calculo ( ".
					"				n_Sucursal, ". 
					"				c_Coface, ". 
					"				n_Prop, ". 
					"				i_Seg, ". 
					"				n_Ramo, ". 
					"				n_Mod, ". 
					"				n_Moeda, ". 
					"				n_Apolice, ". 
					"				n_Endosso, ". 
					"				i_Corr, ". 
					"				n_User, ". 
					"				t_Endosso, ". 
					"				t_Apolice, ". 
					"				Renova_n_Apolice, ". 
					"				v_Premio, ". 
					"				v_IOF, ". 
					"				p_Comissao, ". 
					"				p_Cobertura, ". 
					"				Tx_Moeda, ". 
					"				d_Ini_Vig, ". 
					"				d_Fim_Vig, ". 
					"				d_Ult_Fec, ".
					"				d_Aceitacao, ". 
					"				d_Emissao, ". 
					"				Num_Dias_Ganho, ". 
					"				s_Doc, ". 
					"				d_Situacao, ". 
					"				d_Doc, ". 
					"				n_Filial ) ".
					"VALUES		(	$n_Sucursal, ". 
					"				$c_Coface, ". 
					"				$n_Prop, ". 
					"				$i_Seg, ". 
					"				$n_Ramo, ". 
					"				$n_Mod, ". 		//n_Mod
					"				2, ". 			//n_Moeda
					"				$n_Apolice, ".
					"				$n_Endosso, ". 
					"				null, ". 		//i_Corr
					"				66, ".		 	//n_User
					"				1, ". 			//t_Endosso
					"				5, ". 			//t_Apolice
					"				$Renova_n_Apolice, ".
					"				$PA, ". 		//v_Premio
					"				0, ". 			//v_IOF
					"				NULL, ". 		// p_Comissao
					"				$p_Cobertura, ". 
					"				$v_Compra, ".    //@Tx_Moeda
					"				'$d_Ini_Vig', ". 
					"				'$d_Fim_Vig', ". 
					"				NULL, ".      //'$d_Ult_Fec', ".
					"				GETDATE()-1, ".	//d_Aceitacao	
					"				GETDATE(), ". 	//d_Emissao
					"				NULL, ". 		//Num_Dias_Ganho
					"				1, ". 			//s_Doc
					"				GETDATE(), ". 	//d_Situacao
					"				GETDATE(), ". 	//d_Doc
					"				$n_Filial ) ";
//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($dbSisSeg,$sql);      
			if (!$cur)
				$ok = false;
			$sql = 	"SELECT	* FROM Base_Calculo WHERE i_BC = ( ".
					"	SELECT MAX(i_BC) FROM Base_Calculo )";
//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($dbSisSeg,$sql);      
			if (!$cur)
				$ok = false;

			$i_BC = odbc_result($cur,"i_BC");
			$n_Sucursal = odbc_result($cur,"n_Sucursal");
			$n_Ramo = odbc_result($cur,"n_Ramo");
			$n_Apolice = odbc_result($cur,"n_Apolice");
			$n_Endosso = odbc_result($cur,"n_Endosso");
			$c_Coface = odbc_result($cur,"c_Coface");
			$n_Prop = odbc_result($cur,"n_Prop");
			$v_IOF = odbc_result($cur,"v_IOF");
			if (! $v_IOF)
				$v_IOF = "NULL";
			$i_Corr = odbc_result($cur,"i_Corr");
			if (! $i_Corr)
				$i_Corr = "NULL";

/*
			$Renova_n_Apolice = odbc_result($cur,"Renova_n_Apolice");
			$p_Cobertura = odbc_result($cur,"p_Cobertura");
			$d_Ini_Vig = odbc_result($cur,"d_Ini_Vig");
			$d_Fim_Vig = odbc_result($cur,"d_Fim_Vig");
			$d_Ult_Fec = odbc_result($cur,"d_Ult_Fec");
			$n_Filial = odbc_result($cur,"n_Filial");
			$n_Mod = odbc_result($cur,"n_Mod");
			
*/
			
			$sql = 	"UPDATE Endosso SET i_BC = $i_BC ".
					"WHERE	n_Sucursal = $n_Sucursal ".
					"		AND n_Ramo = $n_Ramo ".
					"		AND n_Apolice = $n_Apolice ".
					"		AND n_Endosso = $n_Endosso ";
//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($dbSisSeg,$sql);      
			if (!$cur)
				$ok = false;

/*
* n_Moeda = 2
* i_Corr = null
* t_Endosso = 1
* t_Apolice = 5
* Renova_n_Apolice: consultar na tabela Base_Calculo (use o i_Seg, n_Prop)
* v_Premio: valor total da PA
* v_IOF = 0
* p_Comissao = NULL
* p_Cobertura: consultar na tabela Base_Calculo (use o i_Seg, n_Prop)
* Tx_Moeda: pegar o v_Compra na tabela Valor_Moeda da data de aceitação			
* d_Ini_Vig: consultar na tabela Base_Calculo (use o i_Seg, n_Prop)
* d_Fim_Vig: consultar na tabela Base_Calculo (use o i_Seg, n_Prop)
* d_Ult_Fec: consultar na tabela Base_Calculo (use o i_Seg, n_Prop)
* d_Aceitacao: ontem
* d_Emissao: hoje
* Num_Dias_Ganho: NULL
* s_Doc: 1
* n_Filial: consultar na tabela Base_Calculo (use o i_Seg, n_Prop)
*/

//6-) Insere em resseguro:

			$sql = 	"INSERT 	Resseguro ( ".
					"				i_BC, ".
					"				c_Seg, ".
					"				p_Seguradora, ".
					"				p_Resseguro, ".
					"				p_Com_Resseguro, ".
					"				v_Max_Retencao ) ".
					"		SELECT 		BC.i_BC, ".
					"					c_Seg, ".
					"					p_Seguradora, ".
					"					p_Resseguro, ".
					"					p_Com_Resseguro, ".
					"					v_Max_Retencao ".
					"		FROM 		Base_Calculo BC ".
					"					JOIN Resseguro RE ON BC.i_BC = RE.i_BC ".
					"		WHERE 		BC.i_BC = ".$i_BC." ".
					"					AND n_Sucursal = $n_Sucursal ".
					"					AND n_Ramo = $n_Ramo ".
					"					AND n_Apolice = $n_Apolice ".
					"					AND n_Endosso = 0 ";
// isso está certo (parâmetros do where)???
// vai encontrar registro??? mais de um???

//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($dbSisSeg,$sql);		
			if (!$cur)
				$ok = false;
//7-) Insere as parcelas

			$sql = 	"SELECT * FROM PADet WHERE idInform = $idInform ORDER BY num";

//echo ("<BR>".$sql."<BR>");
			$cur=odbc_exec($db,$sql);		
			if (!$cur)
				$ok = false;
			$numberExtensive = new NumberUtils();
			
			while (odbc_fetch_row($cur)) {

				$num = odbc_result($cur,"num");
				$valor = odbc_result($cur,"valor");
				$vencimento = odbc_result($cur,"vencimento");
			
				$v_Extenso = $numberExtensive->extensive(number_format($valor, 2, '.', ''),2);			
				
				$sql = 	"INSERT 	Parcela ( ".
						"				c_Coface, ".
						"				n_Ramo, ".
						"				n_Sucursal, ".
						"				i_Seg, ".
						"				t_parcela, ".
						"				n_Prop, ".
						"				n_Apolice, ".
						"				n_Endosso, ".
						"				v_Parcela, ".
						"				d_Venc, ".
						"				n_Seq_Parcela, ".
						"				n_Moeda, ".
						"				d_Parcela, ".
						"				s_Parcela, ".
						"				v_IOF, ".
						"				i_BC, ".
						"				v_Extenso, ".
						"				v_Frac ) ".
						"VALUES		(	$c_Coface, ".
						"				$n_Ramo, ".
						"				$n_Sucursal, ".
						"				$i_Seg, ".
						"				2, ". 			// t_Parcela
						"				$n_Prop, ".
						"				$n_Apolice, ".
						"				$n_Endosso, ".
						"				$valor, ".		//v_Parcela
						"				'$vencimento', ".	//d_Venc
						"				$num, ".		// n_Seq_Parcela
						"				2, ".			//n_Moeda
						"				GETDATE(), ".	// d_Parcela
						"				1, ".			// s_Parcela
						"				$v_IOF, ".
						"				$i_BC, ".
						"				'$v_Extenso', ".
						"				$v_Frac ) ";

//echo ("<BR>".$sql."<BR>");
				$cur2=odbc_exec($dbSisSeg,$sql);
				if (!$cur2)
					$ok = false;

				$sql = "SELECT MAX(i_Parcela) i_Parcela FROM Parcela ";
						
//echo ("<BR>".$sql."<BR>");
				$cur2=odbc_exec($dbSisSeg,$sql);
				if (!$cur2)
					$ok = false;
				$i_Parcela = odbc_result($cur2,"i_Parcela");
				
// pegar da tabela Base_Calculo no registro recém-inserido
			
/*
* os campos n_Ramo, n_Sucursal, n_Apolice, n_Endosso, n_Moeda, v_IOF, i_BC,
v_Frac   vc deve pegar na tabela Base_Calculo
* t_parcela = 2
* n_Seq_Parcela será o sequencia da parcela
* n_Moeda = 2
* d_Parcela = getdate
* s_Parcela = 1
*/				

//8-) Insere em PagRec

				$sql = 	"INSERT 	PagRec ( ".
						"			i_Seg, ".
						"			i_BC, ".
						"			i_Parcela, ".
						"			i_Corr, ".
						"			n_Seq_Parcela, ".
						"			n_Sucursal, ".
						"			n_Ramo, ".
						"			n_Apolice, ".
						"			n_Endosso, ".
						"			c_Coface, ".
						"			n_Prop, ".
						"			d_Vencimento, ".
						"			v_Documento, ".
						"			n_Moeda, ".
						"			t_Doc, ".
						"			s_Pagamento, ".
						"			d_Situacao, ".
						"			d_Sistema, ".
						"			v_Frac ) ".
						"VALUES		( ".
						"			$i_Seg, ".
						"			$i_BC, ".
						"			$i_Parcela, ".
						"			$i_Corr, ". 	//BC
						"			$num, ".
						"			$n_Sucursal, ".
						"			$n_Ramo, ".
						"			$n_Apolice, ".
						"			$n_Endosso, ".
						"			$c_Coface, ".
						"			$n_Prop, ".
						"			'$vencimento', ".
						"			$valor, ".		// v_Documento
						"			2, ".			// n_Moeda
						"			2, ". 			//t_Doc ?????
						"			1, ".			// s_Pagamento
						"			GETDATE(), ".
						"			GETDATE(), ".
						"			$v_Frac ) "; // v_Frac do BC original

//echo ("<BR>".$sql."<BR>");
				$cur2=odbc_exec($dbSisSeg,$sql);				
				if (!$cur2)
					$ok = false;
			}

//10) Inserir em Calculo de Ajuste:

			$sql = 	"INSERT 	Calc_Ajuste ( ".
					"				i_BC, ".
					"				v_Exportado, ".		//com estimativa 150.000
					"				v_Coberto, ".		//com estimativa 100.000
					"				v_n_Coberto, ".		// 50.000
					"				v_Paises_Excluidos, ".	//null
					"				v_Base_Calculo, ". //100.000
					"				t_Pr, ".		//taxa
					"				v_PrMin, ".		//premioPago
//					PremioPago = (select sum(v_Documento) from PagRec where i_Seg=2171 and n_Prop=1
//                  and t_Doc in (1,2) and s_Pagamento=2) 
					
					"				v_Pr_Total, ".	//1.000 (taxa * base_calculo) + juros de mora
					"				v_Saldo ) ". //pr_total - prmin
					"VALUES		( ".
					"				$i_BC, ".
					"				$totExpInfEst, ".
					"				$volumePA, ".
					"				$volumeExcluido, ".
					"				NULL, ".
					"				$volumePA, ".
					"				$txPremio, ".
					"				$PremioPago, ".
					"				".($PremioEfetivo + $volumeMora).", ".
					"				".($PA - $desconto)." ) ";

			$cur=odbc_exec($dbSisSeg,$sql);
			if (!$cur)
				$ok = false;
//11) Atualiza dados da PA no Inform (Siex)

			$sql = 	"UPDATE 	Inform SET ".
					"			totalExportadoPa = $totExpInfEst, ".
					"			baseCalculoPa = $volumePA, ".
					"			taxaPa = $txPremio, ".
					"			premioPa = $PremioEfetivo, ".
					"			jurosPa = $volumeMora, ".
					"			saldoPa = $PA, ".
					"			saldoFinalPa = ".($PA - $desconto);

			$cur=odbc_exec($db,$sql);			
			if (!$cur)
				$ok = false;
		}
		else {
			?><SCRIPT language="javascript">verErro('Valor da Moeda não existente para a data de aceitação.');</SCRIPT><?php }
	}
	
	return $ok;
}

function enviaEmailFaturas($db, $idInform, $dbSisSeg) {
	
	$t = odbc_exec($db, "SELECT i.emailContact, i.name, i.prodUnit from Inform i WHERE i.id = $idInform");
	$emailContact = trim(odbc_result($t, 1));
//	$nameCl = trim(odbc_result($t, 2));
	$prodUnit = trim(odbc_result($t, "prodUnit"));
	
	$numApolice = numApolice ($idInform,$db,$dbSisSeg);
	$numApolice = sprintf("062%06d", $numApolice);
	if ($prodUnit <> 62){
		$numApolice = $numApolice."/".$prodUnit;
	}

	
// 	precisa disto?
//	$x = odbc_exec($db, "SELECT i.name, c.name FROM Importer i join Country c on i.idCountry=c.id WHERE i.id = $idBuyer");
//	$name = odbc_result($x, 1);
//	$country = odbc_result($x, 2);

    

	
	$msgEmail = "<html><head><title>:::SBCE:::</title>\r\n".
				"<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>\r\n";
	
	$lines = file ("../../scripts/styles.inc");

	foreach ($lines as $line_num => $line) {
   		$msgEmail = $msgEmail . " " . $line . "\r\n";
	}
		
	 require_once("../../MailSend.php"); 
	
	 $mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
	 $mail->FromName = "Credito"; // Seu nome 
	 // Define os destinatário(s)
	
	
	$msgEmail = $msgEmail ."</head>\r\n".
				"<body bgcolor='#FFFFFF' text='#000000' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>\r\n".
				"<TABLE cellSpacing=0 cellpadding='2' width='90%' align='center' border='0'>\r\n".
				"<TR><TD class='texto'>\r\n".
				"<br>Prezado Segurado,\r\n".
				"<br><br><br>Segue abaixo link para download da(s) fatura(s) de Parcela de Ajuste referente(s) à apólice número $numApolice .\r\n".
				"<br><br>Clique no vencimento para imprimir sua fatura. <br><br> \r\n".
				"<TABLE cellSpacing=0 cellpadding='2' align='center' border='0'>\r\n".
				"	<TR class='bgAzul'>\r\n".
				"    	<TD></TD>\r\n".
				"    	<TD align='center'>Parcela</TD>\r\n".
				"    	<TD align='center'>Vencimento</TD>\r\n".
				"    	<TD align='right' width='20%'>Valor (U$)</TD>\r\n".
				"    	<TD></TD>\r\n".
				"	</TR>\r\n";
	
	$sql = 	"SELECT * from PADet ".
			"WHERE	idInform = ".$idInform." ".
			"ORDER BY num";
	$cur=odbc_exec($db,$sql);
	
	$i = 0;
	
	while (odbc_fetch_row($cur)) {
		$num = odbc_result($cur,"num");
		$valor = odbc_result($cur,"valor");
		$vencimento = odbc_result($cur,"vencimento");
		$arquivoFatura = odbc_result($cur,"arquivoFatura");
		
		if (($i) % 2) {
			$msgEmail = $msgEmail ."<TR bgcolor=#e9e9e9>\r\n";
		}
		else {
			$msgEmail = $msgEmail ."<TR>\r\n";
		}
		$msgEmail = $msgEmail .
		    "	<TD width='10%'></TD>\r\n".
		    "	<TD class='texto' align='center'>".$num."</TD>\r\n".
		    "	<TD class='texto' align='center'><a href='http://www.sbce.com.br/siex/src/download/$arquivoFatura'>".substr($vencimento, 8, 2)."/".substr($vencimento, 5, 2)."/".substr($vencimento, 0, 4)."</a></TD>\r\n".
		    "	<TD class='texto' align='right' width='20%'>".number_format($valor, 2, ',', '.')."</TD>\r\n".
		    "	<TD width='10%'></TD>\r\n".
			"</TR>\r\n";
			
		$i++;
	}
	$msgEmail = $msgEmail."</TABLE>\r\n".
				"<br><br><br> Atenciosamente, <br><br><br> Seguradora Brasiliera de Crédito à Exportação \r\n".
				"</TD></TR></TABLE>\r\n".
				"</body></html>\r\n";
	
	//$to = "credito@sbce.com.br";
	 // $to = "tvilanova.elumini@sbce.com.br";
	if($emailContact){
	//	$to .= ", $emailContact";
    //    $to .= "tvilanova.elumini@sbce.com.br";
	}
	$query = "SELECT email FROM  Contact  WHERE idInform = $idInform AND notificationForChangeCredit = 1";
	$not = odbc_exec($db, $query);
	while(odbc_fetch_row($not)){
		$email = trim(odbc_result($not, 1));
		//$to .= ", $email";
	
	    $mail->AddAddress($email);
	}
	
    $headers_hc  = "MIME-Version: 1.0\r\n";
	$headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";
	
	//$headers_hc .= "From: credito@sbce.com.br\r\n";
	$headers_hc .= "From: tvilanova.elumini@sbce.com.br\r\n";
	
	 $mail->AddAddress('siex@cofacedobrasil.com');
	 $mail->AddAddress(trim($to));
	 			 
	 $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	 $mail->Subject  = "Parcela de Ajuste - SBCE"; // Assunto da mensagem
	 $mail->Body =  $msgEmail;
	 $enviado = $mail->Send();   // envia o email
	 $mail->ClearAllRecipients();
	 $mail->ClearAttachments();
	
	 // Exibe uma mensagem de resultado
	 if ($enviado) {			
		 return true;
	  
	 } else {
		  return false;
	 }
    //	echo ("<br>".$msgEmail."<br>");
    /*

	if (mail(trim($to), "Parcela de Ajuste - SBCE", $msgEmail, $headers_hc))
      //	if (mail("gustavo@hi.com.br", "Parcela de Ajuste - SBCE", $msgEmail, "From: gustavo@hi.com.br\r\n"))
		return true;
	else
		return false;
	*/	
}

function mkdate ($a, $m, $d) {
	return date ("Y-m-d", mktime (0, 0, 0, $m, $d, $a));
}

// geração da PA em parcela única, com possibilidade de ajuste de preço
// através do desconto (perfil DVE) - statePa deve estar como calculada
if ($executa == 1) { 
	$desconto = str_replace(".", "", $desconto);
	$desconto = str_replace(",", ".", $desconto);

	$cur=odbc_exec($db,"BEGIN TRAN");
	$ok = true;
	
	$sql =	"DELETE		PADet ".
			"WHERE		idInform = ".$idInform;
	$cur=odbc_exec($db,$sql);
	if (!$cur)
		$ok = false;

	$dia = substr($vencimento, 0, 2);
	$mes = substr($vencimento, 3, 2);
	$ano = substr($vencimento, 6, 4);
	
	$data = mkdate ($ano, $mes, $dia);

	$sql =	"INSERT 	PADet ( ".
			"			idInform, ".
			"			num, ".
			"			valor, ".
			"			vencimento) ".
			"			VALUES ( ". 
			"			".$idInform.", ".
			"			1, ".
			"			".($PA - $desconto).", ".
			"			'".$data."' ) ";
			
	$cur=odbc_exec($db,$sql);
	
	//echo("<br>".$sql."<BR>$dia - $mes - $ano<br>");
	
	if (!$cur)
		$ok = false;

	$sql =	"UPDATE		Inform SET descontoPa = $desconto , ".
			"					idUserPa = ".$userID.", ".
			"					dataPa = getdate() ".
			"WHERE		id = ".$idInform;
	$cur=odbc_exec($db,$sql);
	if (!$cur)
		$ok = false;

	if ($ok)	
		$cur=odbc_exec($db,"COMMIT TRAN");
	else 
		$cur=odbc_exec($db,"ROLLBACK TRAN");

}

// Regeração da PA em uma ou mais parcelas, com possibilidade de ajuste de data
// (perfil financ) - statePa deve estar como Financeiro
if ($executa == 2) {
	$valParcela = (floor((($PA - $desconto)/$parcelas)*100))/100;
	$totParcelas = 0;
	$cur=odbc_exec($db,"BEGIN TRAN");
	$ok = true;
	
//	$cur=odbc_exec($db,$sql);
	
	$sql =	"DELETE		PADet ".
			"WHERE		idInform = ".$idInform;
	$cur=odbc_exec($db,$sql);
	if (!$cur)
		$ok = false;
	
	for ($i=1; $i<=$parcelas; $i++)	{
		
		// última parcela, verifica se o floor deixou diferença
		if ($i == $parcelas) {
			$valParcela = $PA - $desconto - $totParcelas;
		}

//		echo("<BR>Val: ".$valParcela."<BR>");		
		$totParcelas = $totParcelas + $valParcela;
		
		$dia = substr($vencimento, 0, 2);
		$mes = substr($vencimento, 3, 2);
		$ano = substr($vencimento, 6, 4);
		
		$data = mkdate ($ano, $mes - 1 + $i, $dia);
		$sql =	"INSERT 	PADet ( ".
				"			idInform, ".
				"			num, ".
				"			valor, ".
				"			vencimento) ".
				"			VALUES ( ". 
				"			".$idInform.", ".
				"			".$i.", ".
				"			".$valParcela.", ".
				"			'".$data."' ) ";
		
		$cur=odbc_exec($db,$sql);
		if (!$cur)
			$ok = false;
	}
	if ($ok)	
		$cur=odbc_exec($db,"COMMIT TRAN");
	else 
		$cur=odbc_exec($db,"ROLLBACK TRAN");
	
}

// usuário DVE envia valor ajustado da PA (com parcela única) para usuário Financeiro,
// statePa passa de 2 para 3 e usuário DVE não pode mais alterar
if ($executa == 3) {
	$cur=odbc_exec($db,"BEGIN TRAN");
	$ok = true;
	
	$sql =	"SELECT name FROM Inform WHERE id = ".$idInform;
	$cur=odbc_exec($db,$sql);
	if (!$cur)
		$ok = false;
	
	$name = odbc_result($cur,"name");
	
	if($notif->gravaPa($userID, $idInform, $name, $db)){
		$sql =	"UPDATE	Inform SET 	statePa = 3, ".
				"					idUserPa = ".$userID.", ".
				"					dataPa = getdate() ".
				"WHERE		id = ".$idInform;
		$cur=odbc_exec($db,$sql);
		if (!$cur)
			$ok = false;
	}
	if ($ok) {
		$cur=odbc_exec($db,"COMMIT TRAN");
		?><SCRIPT language="javascript">verErro('Notificação enviada com sucesso!');</SCRIPT><?php }
	else {
		$cur=odbc_exec($db,"ROLLBACK TRAN");
	}
}

// altera vencimentos das parcelas
if ($executa == 4) {
	$cur=odbc_exec($db,"BEGIN TRAN");
	$ok = true;
	
	for ($i=1; $i<=$qtdParcelas; $i++)	{
		$sql =	"UPDATE	PADet set vencimento = '".dmy2ymd($HTTP_POST_VARS["vencimentoParcela".$i])."' ".
				"WHERE 	idInform = ".$idInform." ".
				"		AND num = ".$i;
		$cur=odbc_exec($db,$sql);
		if (!$cur)
			$ok = false;
	}
	if ($ok)	
		$cur=odbc_exec($db,"COMMIT TRAN");
	else 
		$cur=odbc_exec($db,"ROLLBACK TRAN");
}

// emite títulos (PDF para o cliente), grava no SISSEG
// e altera o statePa p/ 4 (emitida)
if ($executa == 5) {
	ob_end_flush();
?>
	<TABLE name="aguarde" id="aguarde" style="DISPLAY:block" cellSpacing=0 cellpadding="2" width="100%" align="center" border="0" bgcolor ="#e9e9e9">
		<TR>
			<TD align="center"><BR><BR><BR><BR><BR>Processando. Aguarde...<BR><BR><BR><BR></TD>
		</TR>
	</TABLE>	
<?php flush();
	
	$cur=odbc_exec($db,"BEGIN TRAN");
	$cur=odbc_exec($dbSisSeg,"BEGIN TRAN");
	
	$ok = false;
	if (gravaFaturasSisseg(	$dbSisSeg, $db, $idInform, $PA, $totExpInfEst, $volumePA,
							$volumeExcluido, $volumePA, $txPremio, $PremioPago,
							$PremioEfetivo, $desconto, $volumeMora)) {
		if (geraPdfFaturas($db, $idInform)) {
			if (enviaEmailFaturas($db, $idInform, $dbSisSeg)) {
				$sql ="UPDATE	Inform SET statePa = 4 ".
						"WHERE		id = ".$idInform;
				$cur=odbc_exec($db,$sql);
				if ($cur)
					$ok = true;
			}
		}
	}
	if ($ok) {
		$cur=odbc_exec($db,"COMMIT TRAN");
		$cur=odbc_exec($dbSisSeg,"COMMIT TRAN");
		$msg = "Operação executada com sucesso!";
	}
	else {
		$cur=odbc_exec($db,"ROLLBACK TRAN");
		$cur=odbc_exec($dbSisSeg,"ROLLBACK TRAN");
		$msg = "Operação não executada!";
	}
?>
	<SCRIPT language="javascript">
		document.getElementsByName('aguarde')[0].style.display = "none";
		verErro('<?php echo $msg;?>');
	</SCRIPT>
<?php }
?>
