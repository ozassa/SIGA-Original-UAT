<?php  // Alterado Hicom (Gustavo) - 23/12/04 - Alteração do processo de solicitação de cobertura para juros de mora

$query = "SELECT id FROM JurosMora WHERE idInform = $idInform AND state = 1";
//echo $query;
$cur = odbc_exec($db, $query);
  
if(odbc_fetch_row($cur)){
   $idJuros = odbc_result($cur, id);
}else{
   $q = "INSERT INTO JurosMora (idInform) values ($idInform)";
   //echo $q;
   $c = odbc_exec($db, $q);
   $idJuros = odbc_result(odbc_exec($db,"select max(id) from JurosMora where idInform=$idInform"),1);
}

$sql =	"SELECT		inf.i_Seg, inf.nProp ".
//		"			isnull(round((txMin * (1+txRise)),5), 0) txPremio, ".
//		"			isnull(round(taxaPa,5),0) taxaPa, inf.warantyInterest ".
		"FROM 		Inform inf ".
		"WHERE		inf.id = ".$idInform;
$cur = odbc_exec($db,$sql);
if(odbc_fetch_row($cur)){
   $i_Seg = odbc_result($cur, "i_Seg");
   $nProp = odbc_result($cur, "nProp");

   $sql = 	"SELECT		isnull(sum(round(v_Documento,2)), 0) tot ".
   			"FROM		   PagRec ".
   			"WHERE		i_Seg = ".$i_Seg." ".
   			" 			   and n_Prop= ".$nProp." ".
   			" 			   and t_Doc in (1,2) ".
				"			   and s_Pagamento in (1,2)";

   $cur=odbc_exec($dbSisSeg,$sql);
   if(odbc_fetch_row($cur)){
      $premioTotal = odbc_result($cur,"tot");
   }
}

$sql =	"	SELECT 	i_Parcela, v_Parcela, d_Venc, DATEDIFF(day, d_Venc, getdate()) atraso
				FROM 		Parcela
				WHERE 	i_Seg = $i_Seg
							and n_Prop = $nProp
							and t_parcela = 1
							and opc_Mora = 1
							and s_Parcela = 0 ";
							
$cur = odbc_exec($dbSisSeg,$sql);
$i_Parcela = odbc_result($cur, "i_Parcela");
$v_Parcela = odbc_result($cur, "v_Parcela");
$d_Venc = odbc_result($cur, "d_Venc");
$atraso = odbc_result($cur, "atraso");

if ($atraso > 0) { // deleta parcela para ser criada uma nova com novo vencimento
	$sql =	"	DELETE 	Parcela
					WHERE 	i_Seg = $i_Seg
								and n_Prop = $nProp
								and t_parcela = 1
								and opc_Mora = 1
								and s_Parcela = 0 ";

	$cur = odbc_exec($dbSisSeg,$sql);
	$i_Parcela = false;
}
?>

<a name=condEsp></a>
<form action="<?php  echo $root;?>role/client/Client.php#condEsp"  method="post" name=data_form>
<input type=hidden name=idInform value="<?php  echo $idInform;?>">
<input type=hidden name=idJuros value="<?php  echo $idJuros;?>">
<input type=hidden name=usuario value="<?php  echo $userID;?>">
<input type=hidden name=premioTotal value="<?php  echo $premioTotal;?>">
<input type=hidden name=solicita value="0">
<!-- input type=hidden name="comm" value="envNotfJuros" -->
<input type=hidden name="comm" value="condEsp">
<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD align="center"><H3>Condição Especial de Cobertura de Juros de Mora </H3><br><hr></TD>
  </TR>
  <TR>
    <TD align="center">
As Condições Especiais de Cobertura Acessória de Juros Operacionais e
Moratórios permite a  cobertura securitária das quantias referentes aos
juros operacionais e aos juros de mora, calculados entre a data de vencimento
da fatura de exportação (inicial ou devidamente prorrogada) e a data do
efetivo pagamento da indenização.
    </TD>
  </TR>
<?php  if (! $i_Parcela) {
?>
  <TR>
    <TD align="center">
    Escolha a data de vencimento da fatura: <select name=vencimento>
<?php  $dia = date('d');
    $mes = date('m');
    $ano = date('Y');
    for($i = 1; $i <= 15; $i++){
      $venc = date('Y-m-d', mktime(0, 0, 0, $mes, $dia + $i, $ano));
      echo "<option value=\"$venc\">". ymd2dmy($venc). "</option>\n";
    }
?>
    </select>
    </TD>
  </TR>
  <TR>
    <TD align="center">
         Parcela de Juros de Mora: <?php echo number_format(round($premioTotal * 0.04,2), 2, ',', '.');?>
    </TD>
  </TR>
<?php  }
else {
?>
  <TR>
    <TD align="center">
    Data de vencimento da fatura: <?php  echo ymd2dmy($d_Venc);?></TD>
  </TR>
  <TR>
    <TD align="center">
         Parcela de Juros de Mora: <?php  echo number_format($v_Parcela, 2, ',', '.');?>
    </TD>
  </TR>
<?php  }
?>
</TABLE>

<p align="center">
<input class="sair" type="button" value="Voltar" onClick="this.form.comm.value='open';this.form.submit()">
<?php  if ($i_Parcela) {
?>
<input class="sair" type="button" value="Imprimir" onClick="this.form.solicita.value='0'; this.form.submit();">
<?php  }
else {
?>
<input class="sair" type="button" value="Solicitar" onClick="if(confirm('Será efetivada a solicitação de Cobertura de Juros de Mora. Confirma?')) { this.form.solicita.value='1'; this.form.submit();}">
<?php  }
?>
</form>

