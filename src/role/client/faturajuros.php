<?php  // Alterado Hicom (Gustavo) - 23/12/04 - Alteração do processo de solicitação de cobertura para juros de mora

require_once("../rolePrefix.php");
require_once("../../pdfConf.php");

//echo "andrea2";
$key = time(). session_id();
$prefix = $pdfDir. $key;
//echo "Deia";
function ymd2dmy($d){
  if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
    return "$v[3]/$v[2]/$v[1]";
  }
  return $d;
}
// echo "ANDREA";
function arruma_cnpj($c){
  if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
    return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
  }
  return $c;
}

$x = odbc_exec($db,
	       "select inf.name, inf.address, inf.cep, r.name, inf.cnpj, inf.endValidity,
                inf.startValidity, inf.i_Seg, inf.prodUnit, inf.contrat, inf.nProp,
                inf.city, inf.currency, inf.prMin, inf.warantyInterest, inf.txRise,
                inf.prMTotal, inf.percCoverage,inf.Ga
                from Inform inf JOIN Region r on (r.id = inf.idRegion)
                where inf.id=$idInform");
$name = odbc_result($x, 1);
$address = odbc_result($x, 2);
$cep = odbc_result($x, 3);
$uf = substr(odbc_result($x, 4), 0, 2);
$cnpj = arruma_cnpj(odbc_result($x, 5));
$endValidity = ymd2dmy(odbc_result($x, 6));
$startValidity = ymd2dmy(odbc_result($x, 7));
$i_Seg = odbc_result($x, 8);
$prod = odbc_result($x, 9);
$contrat = odbc_result($x, 10);
$nProp = odbc_result($x, 11);
$city = odbc_result($x, 12);
$currency = odbc_result($x, 13);
$cobertura = odbc_result($x, 18);

//Alterado por Tiago V N - Elumini - 24/02/2006
//Alterado por Tiago V N - Elumini - 27/04/2006
if ($currency == 2) {
    $extMoeda = "US$";
    $ext      = "DÓLARES NORTE-AMERICANOS";
}else if ($currency == 6) {
    $extMoeda = "€";
    $ext      = "EUROS";
}


$ga = odbc_result($x, "Ga");

if (($ga=="0") || ($ga=="")){
    $susep = "15.414005212/2005-89";
    $cp    = "CP/RC/06-01";
}else{
    $susep = "15.414004768/2004-08";
    $cp    = "CP/GA/07-01";
}
// valor do premio
/* Hicom - antes era assim, mas o cálculo deve ser em função do Sisseg.
$premio = odbc_result($x, 17);
if(! $premio){
  $premio = odbc_result($x, 14);
  $interest = odbc_result($x, 15);
  $txRise = odbc_result($x, 16);
  $premio = $premio * ($interest == 1 ? 1.04 : 1) * (1 + $txRise / 100);
}  */
$sql = 	"SELECT		isnull(sum(round(v_Documento,2)), 0) tot ".
			"FROM		   PagRec ".
			"WHERE		i_Seg = ".$i_Seg." ".
			" 			   and n_Prop= ".$nProp." ".
			" 			   and t_Doc in (1,2) ".
				"			   and s_Pagamento in (1,2)";

$cur=odbc_exec($dbSisSeg,$sql);
if(odbc_fetch_row($cur)){
   $premio = odbc_result($cur,"tot");
}

$sql = " SELECT   i_Parcela, t_parcela, d_Venc, v_Extenso, v_Parcela, s_Parcela, opc_Mora
         FROM     Parcela
         WHERE    i_Seg = $i_Seg and n_Prop = $nProp and opc_Mora = 1 "; //and opc_Mora = 1
$cur=odbc_exec($dbSisSeg,$sql);
$valor = odbc_result($cur,"v_Parcela");
$i_Parcela = odbc_result($cur,"i_Parcela");
$vencimento = odbc_result($cur,"d_Venc");

// Hicom $valor = number_format($premio * 0.04, 0, '', ''); // valor = 4% do valor do prêmio
// Hicom $vParcela = sprintf("%.2f", $valor);
// Hicom $valorExt = number_format($valor, 2, '.', '');

//Alterado por Tiago V N - Elumini - 27/04/2006
//Verifica tipo de Moeda : Euro, Dolár e Real
//$valor = ($currency == 1 ? "R$" : "US$") . number_format($valor, 2, ",", ".");
if ($currency == 1) {
   $valor = "R$" . number_format($valor, 2, ",", ".");
}elseif ($currency == 2) {
   $valor = "US$" . number_format($valor, 2, ",", ".");
}elseif ($currency == 6) {
   $valor = "€" . number_format($valor, 2, ",", ".");
}
$valorExt = odbc_result($cur,"v_Extenso");

$premio = number_format($premio, 0, '', '');
$premioExt = number_format($premio, 2, '.', '');

//Alterado por Tiago V N - Elumini - 27/04/2006
//Verifica tipo de Moeda : Euro, Dolár e Real
//$premio = ($currency == 1 ? "R$" : "US$") . number_format($premio, 2, ",", ".");
if ($currency == 1) {
   $premio = "R$" .  number_format($premio, 2, ",", ".");
}elseif ($currency == 2) {
   $premio = "US$" .  number_format($premio, 2, ",", ".");
}elseif ($currency == 6) {
   $premio = "€" .  number_format($premio, 2, ",", ".");
}

$premioExt = $numberExtensive->extensive($premioExt, $currency);

$address = "$address - $city - $uf";
$sub = substr($cep, 0, 5);
if(! preg_match("\.", $sub)){
  $sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
}else{
  $inc = 1;
  $sub = substr($cep, 0, 6);
}
if(! preg_match("-", $cep)){
  $cep = "$sub-". substr($cep, 5);
}else{
  $cep = "$sub-". substr($cep, 6 + $inc);
}

$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$i_Seg");
if(odbc_fetch_row($y)){
  $nApolice = odbc_result($y, 1);
  $apolice = sprintf("062%06d", $nApolice);
  if($prod != 62){
    $apolice .= "/$prod";
  }
  $y = odbc_exec($dbSisSeg, "select n_Sucursal, n_Ramo from Apolice where n_Apolice=$nApolice");
  $sucursal = odbc_result($y, 1);
  $ramo = odbc_result($y, 2);
}
if(! $nProp) {
  $x = odbc_exec($dbSisSeg,
		 "select n_Prop from Proposta where i_Seg=$i_Seg order by n_Prop desc");
  if(odbc_fetch_row($x)){
    $nProp = odbc_result($x, 1);
  }
}

/******************************************************************************************/
/* Hicom
// cria a parcela no SisSeg

// primeiro cria a Base de Calculo (meu, como da trabalho criar isso)

$x = odbc_exec($dbSisSeg,
	       "select Renova_n_Apolice, d_Ini_Vig, d_Fim_Vig, t_Apolice,
                p_Cobertura, n_Filial, Num_Dias_Ganho from Base_Calculo
                where i_Seg=$i_Seg and n_Prop=$nProp and n_Endosso=0");
$Renova_n_Apolice = odbc_result($x, 1);
$d_Ini_Vig = odbc_result($x, 2);
$d_Fim_Vig = odbc_result($x, 3);
$t_Apolice = odbc_result($x, 4);
$p_Cobertura = odbc_result($x, 5);
$n_Filial = odbc_result($x, 6);
$num_dias_ganho = odbc_result($x, 7);

$ontem = date("Y-m-d 00:00:00.000", mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
$x = odbc_exec($dbSisSeg,
	       "select v_Compra from Valor_Moeda where d_Cotacao='$ontem'");
$txMoeda = odbc_result($x, 1);

// pra variar, tao faltando alguns valores q nao faço a menor ideia de quais serao:
// x_Alt, n_Endosso_Alt, i_BC_Alt, d_Ini_Vig_Ant, d_Fim_Vig_Ant, n_Endosso_Canc e i_BC_Canc

$query = "insert into Base_Calculo
                (n_Sucursal, n_Moeda, c_Coface, d_Emissao, n_Ramo, n_Prop, Renova_n_Apolice, Tx_Moeda,
                 i_Seg, d_Ini_Vig, d_Fim_Vig, t_Apolice, t_Endosso, v_Premio, Num_Dias_Ganho, i_Corr,
                 d_Ult_Fec, d_Doc, n_User, n_Apolice, s_Doc, n_Endosso, v_IOF, v_Custo_Apolice, n_Mod,
                 v_Frac, p_Comissao, p_Cobertura, d_Aceitacao, d_Situacao, d_Cancelamento, x_Alt,
                 n_Endosso_Alt, i_BC_Alt, d_Ini_Vig_Ant, d_Fim_Vig_Ant, n_Endosso_Canc, i_BC_Canc, n_Filial)
                values
                (62, 2, $contrat, getdate(), 49, $nProp, $Renova_n_Apolice, $txMoeda,
                 $i_Seg, '$d_Ini_Vig', '$d_Fim_Vig', $t_Apolice, 1, $premio, NULL, NULL,
                 NULL, getdate(), 66, $nApolice, 1, 0, 0, 0, 0,
                 0, NULL, $cobertura, getdate(), getdate(), NULL, NULL,
                 NULL, NULL, NULL, NULL, NULL, NULL, $n_Filial)";
//echo "$query";
$x = odbc_exec($dbSisSeg, $query);

$iBC = odbc_result(odbc_exec($dbSisSeg,
			     "select max(i_BC) from Base_Calculo where i_Seg=$i_Seg"),
		   1);

// agora sim cria a parcela
$x = odbc_exec($dbSisSeg,
	       "insert into Parcela (c_Coface, n_Ramo, n_Sucursal, i_Seg, t_parcela, n_Prop,
                n_Apolice, n_Endosso, d_Venc, n_Seq_Parcela, n_Moeda, d_Parcela, s_Parcela,
                v_IOF, i_BC, d_Pagamento, i_PR, v_Extenso, x_CBR, d_Cancelamento, v_Parcela)
                values ($contrat, $ramo, $sucursal, $i_Seg, 2, $nProp, $nApolice, 0,
                '$vencimento', 1, 2, getdate(), 1, 0, $iBC, NULL, NULL,
                '$valorExt', NULL, NULL, '$vParcela')");
$idParcela = odbc_result(odbc_exec($dbSisSeg,
				   "select max(i_Parcela) from Parcela where i_Seg=$i_Seg"),
			 1);
			 
*/
/******************************************************************************************/

$h = new Java ('java.util.HashMap', 25);

$h->put('key', $prefix. "FaturaJuros.pdf");
$h->put('dir', $pdfDir. "");
$h->put('fatNum', date('y'). "/$i_Parcela");
$h->put('apoNum', $apolice);
$h->put('endNum', " ");
$h->put('proNum', "$contrat/$nProp");
$h->put('lugSeg', $address. "");
$h->put('cepSeg', $cep. "");
$h->put('nomSeg', $name. "");
$h->put('cnpjSeg', arruma_cnpj($cnpj). "");
$h->put('valPar', $valor. "");
$h->put('valParExt', $valorExt. "");
$h->put('valPre', $premio. "");
$h->put('valPreExt', $premioExt. "");
$h->put('numPre', "1"); // numero total de parcelas
$h->put('dataVenc', ymd2dmy($vencimento));
$h->put('vigApo', "$startValidity à $endValidity");
$h->put('numPar', '1'); // esta eh a primeira parcela
$h->put('segundavia', '0');
$h->put('is_juros', '1');
$h->put('semValor', '0');
$h->put('susep', $susep. "");
$h->put('cp', $cp. "");
$h->put('extMoeda', $extMoeda);
$h->put('ext', $ext);


$prop = new Java ('JavaParc', $h);
$loc = '/siex/src/download/'.$key.'FaturaJuros.pdf';

if($prop == null){
  die("<h1>javaparc null</h1>");
}else{
  $prop->generate();
 
  echo "<HTML><HEAD>
         <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\">
         <TITLE></TITLE>
         </HEAD></html>";
}
?>
