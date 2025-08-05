<?php  // Alterado Hicom (Gustavo) - 23/12/04 - Alteração do processo de solicitação de cobertura para juros de mora

$sql =   "SELECT		inf.i_Seg, inf.nProp, inf.currency ".
		   "FROM 		Inform inf ".
         "WHERE		inf.id = ".$idInform;

$cur = odbc_exec($db,$sql);

$i_Seg = odbc_result($cur, "i_Seg");
$nProp = odbc_result($cur, "nProp");
$currency = odbc_result($cur, "currency");

$valorExt = number_format(round($premioTotal * 0.04,2), 2, '.', '');
$valorExt = $numberExtensive->extensive($valorExt, $currency);

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
			"			AND n_Prop = $nProp ";



$cur=odbc_exec($dbSisSeg,$sql);
$n_Sucursal = odbc_result($cur,"n_Sucursal");
$n_Ramo = odbc_result($cur,"n_Ramo");
$c_Coface = odbc_result($cur,"c_Coface");

// cria Parcela no Sisseg com t_parcela = 1 (Proposta), opc_Mora = 1 (este será o identificador de juros de mora) e s_Parcela = 0 (incompleta)
if ($solicita == 1) {
//   $sql = "DELETE Parcela where i_Seg = $i_Seg and nProp = $nProp and opc_Mora = 1"
   $sql = " INSERT INTO Parcela (
                           i_Seg,
                           t_parcela,
                           n_Prop,
                           d_Venc,
                           v_Extenso,
                           v_Parcela,
                           s_Parcela,
                           opc_Mora,
                           c_Coface,
                           n_Ramo,
                           n_Sucursal,
                           n_Seq_Parcela,
                           n_Moeda,
                           d_Parcela
                           )
            VALUES (
                           $i_Seg,
                           1,
                           $nProp,
                           '$vencimento',
                           '$valorExt',
                           ".($premioTotal * 0.04)." ,
                           0,
                           1,
                           $c_Coface,
                           $n_Ramo,
                           $n_Sucursal,
									1,
									2,
									getdate())";

   //echo $sql;
   $cur = odbc_exec($dbSisSeg, $sql);
   
   if ($cur) {
      require_once("dataVenc.php");
      require_once("envNotfJuros.php");
   }

   //echo "<BR>$sql<BR>";

   //$i_Parcela = odbc_result(odbc_exec($dbSisSeg, "select max(i_Parcela) from Parcela where i_Seg=$i_Seg and n_Prop=$nProp"), 1);
   //echo "<BR>$i_Parcela<BR>";
}

//$query = "SELECT dateVenc FROM JurosMora WHERE id = $idJuros";
//echo $query;
//$cur = odbc_exec($db, $query);

//$vencimento = odbc_result($cur, 1);

?>

<a name=condEsp></a>
<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD align="center"><H3>Condição Especial de Cobertura de Juros de Mora</H3><br><hr></TD>
  </TR>
  <TR>
    <TD align="center">&nbsp;</TD>
  </TR>
  <TR>
    <form name=data_form>
    <TD>
     <ul>
      <li><a href="javascript:onClick=imprime(<?php  echo $idInform;?>)">Imprimir Fatura</a>
      <input type=hidden name="vencimento">
      <input type=hidden name="idInform">
    <!--Escolha a data de vencimento da fatura: <select name=vencimento>-->

<?php  //$dia = date('d');
    //$mes = date('m');
    //$ano = date('Y');
    //for($i = 1; $i <= 15; $i++){
    //  $venc = date('Y-m-d 00:00:00.000', mktime(0, 0, 0, $mes, $dia + $i, $ano));
    //  echo "<option value=\"$venc\">". ymd2dmy($venc). "</option>\n";
    //}
?>

    <!--/select-->
    </form>
      <li><a href="<?php  echo $root;?>role/client/condjuros.php?idInform=<?php  echo $idInform;?>" target="_blank">Imprimir Proposta de CEJM</a>
     </ul>
    </TD>
  </TR>
</TABLE>

<form action="<?php  echo $root;?>role/client/Client.php"  method="get">
<input type=hidden name=idInform value="<?php  echo $idInform;?>">
<input type=hidden name=idJuros value="<?php  echo $idJuros;?>">
<input type=hidden name=usuario value="<?php  echo $userID;?>">
<!-- Hicom <input type=hidden name="comm" value="envNotfJuros"> -->
<input type=hidden name="comm" value="open">
<p align="center">
<input class="sair" type="button" value="Voltar" onClick="this.form.comm.value='open';this.form.submit()">
<!-- Hicom <input type="submit" class="sair" value="Concluir"> -->
</form>

<script language=javascript>

    function imprime(myidInform) {
      document.forms["data_form"].idInform.value = myidInform;
      window.open('<?php  echo $root;?>role/client/faturajuros.php?idInform=' + myidInform + '&comm=gerapdf',
		  'pdf_window1', 'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1,width=950,height=700');
    }
</script>



