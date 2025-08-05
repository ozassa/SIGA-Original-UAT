<?php //Criado Hicom 25/10/2004 (Gustavo)
?>

<SCRIPT language="javascript"> 

	function proc(opc){
		document.Form1.action = '../dve/dve.php?comm=parcelaPA&executa=' + opc;
		document.Form1.submit();
	}

	function voltar(){
		document.Form1.action = '../dve/dve.php?comm=calculaPa';
		document.Form1.submit();
	}

	function validaInteiroPositivo(fieldName, fieldValue){
		if (isNaN(parseInt(fieldValue))) {
        	verErro("Este não é um número válido.");
          	fieldName.value='1';
          	fieldName.focus();
		}
		else {
			if (parseInt(fieldValue) < 1) {
	        	verErro("Este não é um número válido.");
	          	fieldName.value='1';
	          	fieldName.focus();
			}
			fieldName.value=parseInt(fieldValue);
		}
	}

	function checaDesconto(obj, valor){
		if (checkDecimals(obj, valor)) {
//			verErro('');
		}
		if (ReplStr(ReplStr(obj.value, ".", ""), ",", ".") >= <?php echo $PA;?>) {
			verErro('Desconto deve ser menor que a Parcela de Ajuste!');
			obj.focus();
		}		
	}

	function validaData(fieldName, fieldValue){
		if (!CritData(fieldValue)) {
        	verErro("Esta não é uma data válida. Informe a data no formato dd/mm/aaaa.");
          	fieldName.focus();
		}
	}
	
<?php require_once("../../scripts/javafunc.js");
require_once("funcsDve.php");
?>	
	
</SCRIPT>

<?php if ($executa > 0)
	require_once("../dve/procParcelas.php");

$sql =	"SELECT		inf.name, inf.i_Seg, inf.startValidity, ".
		"			inf.endValidity, isnull(inf.statePa, 1) statePa, round(descontoPa,2) descontoPa ".
		"FROM 		Inform inf ".
		"WHERE		inf.id = ".$idInform;
$cur = odbc_exec($db,$sql);

$name = odbc_result($cur,"name");
$startValidity = odbc_result($cur,"startValidity");
$endValidity = odbc_result($cur,"endValidity");
$i_Seg = odbc_result($cur,"i_Seg");
$statePa = odbc_result($cur,"statePa");
$descontoPa = odbc_result($cur,"descontoPa");

if ($statePa == 1)
	$sit = "Não calculada";
if ($statePa == 2)
	$sit = "Calculada";
if ($statePa == 3)
	$sit = "Financeiro";
if ($statePa == 4)
	$sit = "Emitida";
?>

<FORM name="Form1" action="" method="post">
	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0">
		<TR>
	    	<TD colspan="2">Cliente: <?php echo $name;?></TD>
		</TR>
		<TR>
		    <TD>Vigência: <?php echo  substr($startValidity, 8, 2)."/".substr($startValidity, 5, 2)."/".substr($startValidity, 0, 4).
				" a ".substr($endValidity, 8, 2)."/".substr($endValidity, 5, 2)."/".substr($endValidity, 0, 4);?></TD>
	    	<TD>Situação: <?php echo $sit;?></TD>
		</TR>
	</TABLE>
	
	<BR>
	
<?php if ($statePa == 1 || $statePa == 2) {
	if ($role["dve"]) {
?>			

	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0" bgcolor ="#e9e9e9">
		<TR>
			<TD align="right">Parcela de Ajuste (U$): </TD>
			<TD><?php echo number_format($PA, 2, ',', '.');?></TD>
			<TD align="right"><!--Faturas: --></TD>
			<TD><!--<input type="text" name="parcelas" onBlur="validaInteiro(this, this.value);" value="1" size="2" class="caixa">--></TD>
		</TR>
		<TR>
			<TD align="right">Desconto (U$): </TD>
			<TD id="tdDesconto"><input type="text" name="desconto" onBlur="checaDesconto(this, this.value);" value="<?php echo number_format($descontoPa, 2, ',', '.');?>" size="10" class="caixa"></TD>
			<TD align="right">Vencimento: </TD>
			<TD><input type="text" id="vencimento" name="vencimento" onBlur="validaData(this, this.value);" value="<?php echo date("d/m/Y");?>" size="12" class="caixa"></TD>
		</TR>
	</TABLE>

<?php }
}

if ($statePa == 3) {
	if ($role["financ"]) {
?>		

	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0" bgcolor ="#e9e9e9">
		<TR>
			<TD align="right" class="texto">Parcela de Ajuste Devida (U$): </TD>
			<TD align="right" class="texto"><?php echo number_format($PA, 2, ',', '.');?></TD>
			<TD align="right">Parcelas: </TD>
			<TD><input type="text" id="parcelas" name="parcelas" onBlur="validaInteiroPositivo(this, this.value);" value="" size="2" class="caixa"></TD>
		</TR>
		<TR>
			<TD align="right" class="texto">Desconto (U$): </TD>
			<TD align="right" id="tdDesconto" class="texto"><?php echo number_format($descontoPa, 2, ',', '.');?></TD>
			<TD align="right">1º Vencimento: </TD>
			<TD><input type="text" id="vencimento" name="vencimento" onBlur="validaData(this, this.value);" value="" size="12" class="caixa"></TD>
		</TR>
		<TR>
			<TD align="right">Parcela de Ajuste (U$): </TD>
			<TD align="right" id="tdPAFinal"><?php echo number_format($PA - $descontoPa, 2, ',', '.');?></TD>
			<TD align="right"></TD>
			<TD></TD>
		</TR>
	</TABLE>

<?php }
	else if ($role["dve"]) {
?>		

	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0" bgcolor ="#e9e9e9">
		<TR>
			<TD align="right" class="texto" width="50%">Parcela de Ajuste Devida (U$): </TD>
			<TD class="texto" width="20%"><?php echo number_format($PA, 2, ',', '.');?></TD>
		</TR>
		<TR>
			<TD align="right" class="texto">Desconto (U$): </TD>
			<TD id="tdDesconto" class="texto"><?php echo number_format($descontoPa, 2, ',', '.');?></TD>
		</TR>
		<TR>
			<TD align="right">Parcela de Ajuste (U$): </TD>
			<TD id="tdPAFinal"><?php echo number_format($PA - $descontoPa, 2, ',', '.');?></TD>
		</TR>
	</TABLE>

<?php }
}

if ($statePa == 4) {
?>		

	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0" bgcolor ="#e9e9e9">
		<TR>
			<TD align="right" class="texto" width="50%">Parcela de Ajuste Devida (U$): </TD>
			<TD class="texto" width="20%"><?php echo number_format($PA, 2, ',', '.');?></TD>
		</TR>
		<TR>
			<TD align="right" class="texto">Desconto (U$): </TD>
			<TD id="tdDesconto" class="texto"><?php echo number_format($descontoPa, 2, ',', '.');?></TD>
		</TR>
		<TR>
			<TD align="right">Parcela de Ajuste (U$): </TD>
			<TD id="tdPAFinal"><?php echo number_format($PA - $descontoPa, 2, ',', '.');?></TD>
		</TR>
	</TABLE>

<?php }
?>		
	
	<BR>
	
	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0">
		<TR class="bgAzul">
	    	<TD></TD>
	    	<TD align="center">Parcela</TD>
	    	<TD align="center">Vencimento</TD>
	    	<TD align="right" width="20%">Valor (U$)</TD>
	    	<TD></TD>
		</TR>

<?php $sql = 	"SELECT num, round(valor,2) valor, vencimento ".
		"FROM 	PADet ".
		"WHERE	idInform = ".$idInform." ".
		"ORDER BY num";
$cur=odbc_exec($db,$sql);

$qtdParcelas = 0;

while (odbc_fetch_row($cur)) {
	$num = odbc_result($cur,"num");
	$valor = odbc_result($cur,"valor");
	$vencimento = odbc_result($cur,"vencimento");
	
	if ($statePa == 1 || $statePa == 2) {
		if ($role["dve"]) {
			// primeiro vencimento
			if ($qtdParcelas == 0){
?>
		<SCRIPT language="javascript"> 
			document.getElementById('vencimento').value = '<?php echo substr($vencimento, 8, 2)."/".substr($vencimento, 5, 2)."/".substr($vencimento, 0, 4);?>';
		</SCRIPT>
<?php }
?>

		<TR <?php echo  $qtdParcelas % 2 ? "" : " bgcolor=#e9e9e9";?>>
	    	<TD width="10%"></TD>
	    	<TD align="center"><?php echo $num;?></TD>
	    	<TD align="center"><?php echo substr($vencimento, 8, 2)."/".substr($vencimento, 5, 2)."/".substr($vencimento, 0, 4);?></TD>
	    	<TD align="right" width="20%"><?php echo number_format($valor, 2, ',', '.');?></TD>
	    	<TD width="10%"></TD>
		</TR>

<?php }
	}
	if ($statePa == 3) {
		if ($role["financ"]) {
			// primeiro vencimento
			if ($qtdParcelas == 0){
?>
		<SCRIPT language="javascript"> 
			document.getElementById('vencimento').value = '<?php echo substr($vencimento, 8, 2)."/".substr($vencimento, 5, 2)."/".substr($vencimento, 0, 4)?>';
		</SCRIPT>
<?php }
?>

		<TR <?php echo  $qtdParcelas % 2 ? "" : " bgcolor=#e9e9e9";?>>
	    	<TD width="10%"></TD>
	    	<TD align="center"><?php echo $num;?></TD>
	    	<TD align="center"><input type="text" name="vencimentoParcela<?php echo $num?>" onBlur="validaData(this, this.value);" value="<?php echo substr($vencimento, 8, 2)."/".substr($vencimento, 5, 2)."/".substr($vencimento, 0, 4);?>" size="12" class="caixa"></TD>
	    	<TD align="right" width="20%"><?php echo number_format($valor, 2, ',', '.');?></TD>
	    	<TD width="10%"></TD>
		</TR>

<?php }
		else if ($role["dve"]) {
?>

		<TR <?php echo  $qtdParcelas % 2 ? "" : " bgcolor=#e9e9e9";?>>
	    	<TD width="10%"></TD>
	    	<TD align="center"><?php echo $num;?></TD>
	    	<TD align="center"><?php echo substr($vencimento, 8, 2)."/".substr($vencimento, 5, 2)."/".substr($vencimento, 0, 4);?></TD>
	    	<TD align="right" width="20%"><?php echo number_format($valor, 2, ',', '.');?></TD>
	    	<TD width="10%"></TD>
		</TR>

<?php }
	}

	if ($statePa == 4) {
?>
		<TR <?php echo  $qtdParcelas % 2 ? "" : " bgcolor=#e9e9e9";?>>
	    	<TD width="10%"></TD>
	    	<TD align="center"><?php echo $num;?></TD>
	    	<TD align="center"><?php echo substr($vencimento, 8, 2)."/".substr($vencimento, 5, 2)."/".substr($vencimento, 0, 4);?></TD>
	    	<TD align="right" width="20%"><?php echo number_format($valor, 2, ',', '.');?></TD>
	    	<TD width="10%"></TD>
		</TR>
<?php }

	$qtdParcelas = $qtdParcelas + 1;
}
if ($qtdParcelas == 0) {
?>

		<TR <?php echo  $qtdParcelas % 2 ? "" : " bgcolor=#e9e9e9";?>>
	    	<TD width="10%"></TD>
	    	<TD colspan="3" align="center">Nenhuma parcela existente</TD>
	    	<TD width="10%"></TD>
		</TR>

<?php }
?>

	</TABLE>

	<BR>

	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0">
		<TR>
	    	<TD align="center">
				<input type=hidden name=status value="<?php echo $status;?>">
				<input type=hidden name=cliente value="<?php echo $cliente;?>">
				<input type=hidden name=opcDVE value="<?php echo $opcDVE;?>">
				<input type=hidden name=opcPA value="<?php echo $opcPA;?>">
				<input type=hidden name=idInform value="<?php echo $idInform;?>">
				<input type=hidden name=PA value="<?php echo $PA;?>">
				<input type=hidden name=idNotification value="<?php echo $idNotification;?>">
				
				<input type=hidden name=totExpInfEst value="<?php echo $totExpInfEst;?>">
				<input type=hidden name=volumeExcluido value="<?php echo $volumeExcluido;?>">
				<input type=hidden name=volumePA value="<?php echo $volumePA;?>">
				<input type=hidden name=txPremio value="<?php echo $txPremio;?>">
				<input type=hidden name=PremioPago value="<?php echo $PremioPago;?>">
				<input type=hidden name=PremioEfetivo value="<?php echo $PremioEfetivo;?>">
				<input type=hidden name=volumeMora value="<?php echo $volumeMora;?>">
				
<?php if ($statePa == 1 || $statePa == 2) {
	if ($role["dve"]) {
?>
				<input type=button onClick="javascript:proc(1);" value="Gerar Fatura" class="sair">&nbsp;&nbsp;&nbsp;&nbsp;

<?php if ($qtdParcelas > 0) {
?>
				<input type=button onClick="javascript:proc(3);" value="Enviar Financeiro" class="sair">&nbsp;&nbsp;&nbsp;&nbsp;
<?php }
	}
}
if ($statePa == 3) {
	if ($role["financ"]) {
?>
				<input type=hidden name=desconto value="<?php echo $descontoPa;?>">
				<input type=hidden name=qtdParcelas value="<?php echo $qtdParcelas;?>">
				<input type=button onClick="javascript:proc(4);" value="Alterar Vencimento(s)" class="sair">&nbsp;&nbsp;
				<input type=button onClick="javascript:proc(2);" value="Regerar Fatura(s)" class="sair">&nbsp;&nbsp;
				<input type=button onClick="javascript:proc(5);" value="Emitir Título(s)" class="sair">&nbsp;&nbsp;
				<SCRIPT language="javascript"> 
					document.getElementById('parcelas').value = '<?php echo $qtdParcelas;?>';
				</SCRIPT>
<?php }
}
?>
				<input type=button onClick="javascript:voltar();" value="Voltar" class="sair">
			</TD>
		</TR>
	</TABLE>
</FORM>
<?php //echo("<BR>Dir: ". $txPremio." fim<BR>");
//echo("<BR>PA: ". $PA." fim<BR>");
//echo("<BR>DescontoPa: ". $descontoPa." fim<BR>");
?>
