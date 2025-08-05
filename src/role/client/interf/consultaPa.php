<?php  require_once("../dve/interf/funcsDve.php");


$totExpInf = 0;
$totExpInfEst = 0;
$volumeExcluido = 0;
$volumePA = 0;
$txPremio = 0;
$taxaPa = 0;

$sql =	"SELECT		inf.name, inf.i_Seg, inf.nProp, inf.startValidity, ".
		"			inf.endValidity, isnull(inf.statePa, 1) statePa, ".
		"			isnull(round(inf.descontoPa,2), 0) descontoPa, ".
		"			isnull(round((txMin * (1+txRise)),5), 0) txPremio, ".
		"			isnull(round(taxaPa,5),0) taxaPa, inf.warantyInterest ".
		"FROM 		Inform inf ".
		"WHERE		inf.id = ".$idInform;
$cur = odbc_exec($db,$sql);

$name = odbc_result($cur,"name");
$startValidity = odbc_result($cur,"startValidity");
$endValidity = odbc_result($cur,"endValidity");
$i_Seg = odbc_result($cur,"i_Seg");
$nProp = odbc_result($cur,"nProp");
$statePa = odbc_result($cur,"statePa");
$descontoPa = odbc_result($cur,"descontoPa");
$txPremio = odbc_result($cur,"txPremio");
$taxaPa = odbc_result($cur,"taxaPa");
$warantyInterest = odbc_result($cur,"warantyInterest");

if ($warantyInterest == 1) {
	$txMora = 0.04;
}
else {
	$txMora = 0;
}

if ($taxaPa <> 0) {
	$txPremio = $taxaPa;
}

if ($statePa == 1)
	$sit = "Não calculada";
if ($statePa == 2)
	$sit = "Calculada";
if ($statePa == 3)
	$sit = "Financeiro";
if ($statePa == 4)
	$sit = "Emitida";

?>
<?php
 require_once("../../../navegacao.php");
?>
<div class="conteudopagina">
<ul>
<li class="campo3colunas"><label>Cliente</label>
   <?php  echo ($name);?>
</li>
<li class="campo3colunas"><label>Vig&ecirc;ncia</label>
    <?php  echo substr($startValidity, 8, 2)."/".substr($startValidity, 5, 2)."/".substr($startValidity, 0, 4).
			" a ".substr($endValidity, 8, 2)."/".substr($endValidity, 5, 2)."/".substr($endValidity, 0, 4);?>
</li>
<li class="campo3colunas"><label>Situa&ccedil;&atilde;o</label>
<?php  echo $sit;?>
</li>
</ul>
<div style="clear:both">&nbsp;</div>


<TABLE class="tabela01" id="example">
<?php  $sql =	" SELECT 	sum(round(dd.totalEmbarcado,2)) totalEmbarcado, d.id, d.num, d.inicio, d.state state ".
		" FROM 		DVE d left join DVEDetails dd on d.id = dd.idDVE ".
		" WHERE		d.idInform = ".$idInform." ".
		"			AND isnull(dd.state,1) = 1 ".
		" GROUP BY 	d.id, d.num, d.inicio, d.state ".
		" ORDER BY 	d.inicio ";
		//echo "<br>".$sql."<br>";
		$i=0;
		$cur=odbc_exec($db,$sql);
		$totExpInf = 0;
		$qtdDVE = 0;
		$qtdDVEVazia = 0;
		$DVE1 = 0;
		$valorDVEEstimada13 = 0;
		
		$exibeEstimada = false;
		$exibeEstimada13 = false;

		while (odbc_fetch_row($cur)) {
			
			$totalEmbarcado = odbc_result($cur,"totalEmbarcado");
			$id = odbc_result($cur,"id");
			$inicio = odbc_result($cur,"inicio");
			$num = odbc_result($cur,"num");
			$state = odbc_result($cur,"state");
			
			if ($num == 1)
				$DVE1 = $totalEmbarcado;
			
			if ($state == 1) {
				$qtdDVEVazia = $qtdDVEVazia + 1;
			}
			else {
				$totExpInf = $totExpInf + $totalEmbarcado;
				$qtdDVE = $qtdDVE + 1;
			}
			
			if ($i == 0) {
?>

            <TR>
                <TD colspan="5" align="center"><label>Volume de Venda</label></TD>
            </TR>
            <TR class="bgAzul">
                <TD width="15%"></TD>
                <TD width="20%" align="center"><label>DVE</label></TD>
                <TD width="25%" align="center"><label>In&iacute;cio</label></TD>
                <TD align="right" width="25%"><label>Valor (U$)</label></TD>
                <TD width="15%"></TD>
            </TR>

<?php  }
	$i = $i + 1;
?>

	<TR <?php  echo $i % 2 ? "" : " class=odd";?>>
    	<TD></TD>
    	<TD align="center" class="texto"><?php  echo $num;?>&ordf;</TD>
    	<TD align="center" class="texto"><?php  echo substr($inicio, 8, 2)."/".substr($inicio, 5, 2)."/".substr($inicio, 0, 4);?></TD>
    	<TD align="right" class="texto">
		
<?php  if ($state == 1) {
		if ($num == 13) {
			echo ("**");
			$exibeEstimada13 = true;
		}
		else {
			echo ("*");
			$exibeEstimada = true;
		}
	}
	else {
		echo(number_format($totalEmbarcado, 2, ',', '.'));
	}
?>

		</TD>
    	<TD></TD>
	</TR>

<?php  }
$totExpInfEst = $totExpInf;

// se houver uma DVE não informada, o valor da mesma é estimado em função da
// média das demais e essa estimativa é somada ao volume total exportado
if ($qtdDVEVazia > 0) {
   if ($qtdDVE == 0) {
		$valorDVEEstimada = 0;
   }
	else {
		if (substr($startValidity, 8, 2) == 1) { // inicio dia 1º - 12 DVEs (faz a média aritmética)
			$valorDVEEstimada = round($totExpInf/$qtdDVE, 2);
		}
		else { // como a 1ª DVE não foi referente a 1 mês completo, o cáculo da média muda
			$valorDVEEstimada = round(($totExpInf - $DVE1)/($qtdDVE - 1), 2); // a 1ª DVE não entra no cálculo da estimativa para as DVEs 2 a 12
			$valorDVEEstimada13 = $valorDVEEstimada - $DVE1; // depois de calcular a média sem a 1ª DVE, subtrai-se o valor da primeira para encontrar a estimativa da 13ª
		}
	}
	if ($valorDVEEstimada13 < 0)
		$valorDVEEstimada13 = 0;
	if ($exibeEstimada) {
?>

	<TR <?php  echo ($i + 1) % 2 ? "" : " bgcolor=#e9e9e9";?>>
    	<TD colspan="5" class="texto">(*) Valor estimado para DVEs n&atilde;o informadas: U$ <?php  echo number_format($valorDVEEstimada, 2, ',', '.');?></TD>
	</TR>
	
<?php  }
	if ($exibeEstimada13) {
?>	

	<TR <?php  echo ($i + 1) % 2 ? "" : " bgcolor=#e9e9e9";?>>
    	<TD colspan="5" class="texto"><label>(**) Valor estimado para DVN n&atilde;o informada (13ª): U$ <?php  echo number_format($valorDVEEstimada13, 2, ',', '.');?></label></TD>
	</TR>

<?php  }
}
?>

	<TR class="bgAzul">
    	<TD colspan="3" align="right" ><label>Volume Total Venda:</label></TD>
    	<TD align="right">U$ <?php  echo number_format($totExpInf, 2, ',', '.');?></TD>
		<TD></TD>
	</TR>

<?php  if ($qtdDVEVazia > 0) {
	if (substr($startValidity, 8, 2) == 1) { // inicio dia 1º - 12 DVEs (faz a média aritmética)	
		$totExpInfEst = $totExpInf + $valorDVEEstimada * $qtdDVEVazia;
	}
	else {
		$totExpInfEst = $totExpInf + $valorDVEEstimada * ($qtdDVEVazia - 1) + $valorDVEEstimada13;
//		echo "<br>$totExpInfEst = $totExpInf + $valorDVEEstimada * ($qtdDVEVazia - 1) + $valorDVEEstimada13<br>";

	}

?>
	<TR class="bgAzul">
    	<TD colspan="3" align="right"><label>Volume Total Vendido Estimado:</label></TD>
    	<TD align="right">U$ <?php  echo number_format($totExpInfEst, 2, ',', '.');?></TD>
		<TD></TD>
	</TR>
	
<?php  }
?>

</TABLE>

<BR>

<?php  // cria tabela temporária para armazenar dados da PA
//$sql = "drop table [PA]";
//$cur=odbc_exec($db,$sql);

$sql = 	"CREATE TABLE #PA( ".
		"	name varchar(60) null, ".
		"	volumeTotal numeric (15,2), ".
		"	volumeExcluido numeric (15,2), ".
		"	volumePA numeric (15,2), ".
		"	txPremio numeric(15,8), ".
		"	PremioEfetivo numeric (15,2), ".
		"	PremioPago numeric (15,2), ".
		"	PA numeric (15, 2), ".
		"	txMora numeric (15, 8), ".
		"	volumeMora numeric (15, 2) ) ";

$cur=odbc_exec($db,$sql);

if (!$cur){
	$msg = "Erro ao criar a tabela temporária de Parcela de Ajuste";
}
else {
	$sql = 	"INSERT	#PA	( ".
			"	name, ".
			"	volumeTotal, ".
			"	txPremio, ".
			"	txMora) ".
			"VALUES ( ".
			"	'$name', ".
			"	$totExpInfEst,  ".
			"	$txPremio, ".
			"	$txMora )";

//echo "<br>".$sql."<br>";

	$cur=odbc_exec($db,$sql);

	// cria tabela temporária para armazenar dados dos Embarques

// 	$sql = "DROP TABLE #Embarques";
//	$cur = odbc_exec($db,$sql);
	
	$sql = 	"CREATE TABLE #Embarques ( ".
			"	id int, ".
			"	name nvarchar(100) null, ".
			"	embDate datetime, ".
			"	totalEmbarcado numeric(15,2) null, ".
			"	c_Coface_Imp nvarchar (50), ".
			"	idImporter int, ".
			"	idChangeCredit int, ".
			"	num int, ".
			"	negado int) ";
	
	$cur = odbc_exec($db,$sql);
	
	if (!$cur){
		$msg = "Erro ao criar a tabela temporária de #Embarques";
	}
	else {
		if ($statePa == 1) { // não calculada

			// insere todos os registros de DVEDetails na #Embarques
			$sql ="  INSERT #Embarques ".
					"	SELECT	dt.id, ".
					"			imp.name, ".
					"			dt.embDate, ".
					"			isnull(round(dt.totalEmbarcado,2), 0), ".
					"			imp.c_Coface_Imp, ".
					"			imp.id, ".
					"			max (c.id), ".
					"			d.num, ".
					"			dt.negado ".
					"	FROM 	DVE d left join DVEDetails dt on d.id = dt.idDVE ".
	            "           join Inform i on i.id = d.idInform ".
	            "           join Importer imp on imp.id = dt.idImporter ".
	            "           join ChangeCredit c on c.idImporter = imp.id ".
	            "	WHERE	i.id = ".$idInform." ".
					"			and c.creditDate < dt.embDate ".
					"			and (c.limTemp is null or limTemp > dt.embDate) ".
					"			and isnull(dt.state,1) = 1 ".
					"	GROUP BY	dt.id, ".
					"				imp.name, ".
					"				dt.embDate, ".
					"				dt.totalEmbarcado, ".
					"				imp.c_Coface_Imp, ".
					"				imp.id, ".
					"				d.num, ".
					"				dt.negado ";
		}
		else {
			// primeiro insere registros novos (campo calculado nulo ou zero), se existirem,
			// na #Embarques
			
			$sql ="  INSERT #Embarques ".
					"	SELECT	dt.id, ".
					"			imp.name, ".
					"			dt.embDate, ".
					"			isnull(round(dt.totalEmbarcado,2), 0), ".
					"			imp.c_Coface_Imp, ".
					"			imp.id, ".
					"			max (c.id), ".
					"			d.num, ".
					"			dt.negado ".
					"	FROM 	DVE d left join DVEDetails dt on d.id = dt.idDVE ".
	            "           join Inform i on i.id = d.idInform ".
	            "           join Importer imp on imp.id = dt.idImporter ".
	            "           join ChangeCredit c on c.idImporter = imp.id ".
	            "	WHERE	i.id = ".$idInform." ".
					"			and c.creditDate < dt.embDate ".
					"			and (c.limTemp is null or limTemp > dt.embDate) ".
					"			and isnull(dt.state,1) = 1 ".
					"			and isnull(dt.calculado, 0) = 0 ".
					"	GROUP BY	dt.id, ".
					"				imp.name, ".
					"				dt.embDate, ".
					"				dt.totalEmbarcado, ".
					"				imp.c_Coface_Imp, ".
					"				imp.id, ".
					"				d.num, ".
					"				dt.negado ";			
			$cur=odbc_exec($db,$sql);
			//echo "<br>".$sql."<br>";

			// determina se negado em função de ChangeCredit (isso somente para os registros novos que foram inseridos acima)
			$sql ="UPDATE #Embarques set negado = 1 ".
					"WHERE id IN ( ".
					"				SELECT	e.id ".
					"				FROM 	#Embarques e, ChangeCredit c ".
					"				WHERE	e.idChangeCredit = c.id ".
					"						AND	(c.state  in (7,1,4) or (c.credit = 0 and (c.creditTemp <> 0 or c.limTemp > e.embDate))))";
// antes estava assim:					
//					"						AND	(c.state  in (7,1,4) or c.credit = 0) )";
					
			$cur=odbc_exec($db,$sql);
			//echo "<br>".$sql."<br>";

			// agora insere os que já existiam (calculado = 1) e estavam negados em função do campo "negado"
			$sql ="  INSERT #Embarques ".
					"	SELECT	dt.id, ".
					"			imp.name, ".
					"			dt.embDate, ".
					"			isnull(round(dt.totalEmbarcado,2), 0), ".
					"			imp.c_Coface_Imp, ".
					"			imp.id, ".
					"			0, ".
					"			d.num, ".
					"			dt.negado ".
					"	FROM 	DVE d left join DVEDetails dt on d.id = dt.idDVE ".
	            "           join Inform i on i.id = d.idInform ".
	            "           join Importer imp on imp.id = dt.idImporter ".
	            "	WHERE	i.id = ".$idInform." ".
					"			and dt.negado = 1 ".
					"			and isnull(dt.state,1) = 1 ".
					"			and isnull(calculado, 0) = 1 ".
					"	GROUP BY	dt.id, ".
					"				imp.name, ".
					"				dt.embDate, ".
					"				dt.totalEmbarcado, ".
					"				imp.c_Coface_Imp, ".
					"				imp.id, ".
					"				d.num, ".
					"				dt.negado ";			
		}
		$cur=odbc_exec($db,$sql);
//echo "<br>".$sql."<br>";

//		atualiza PremioPago da tabela PA
		if ($i_Seg) {
			$sql ="SELECT		isnull(sum(round(v_Documento,2)), 0) tot ".
					"FROM		PagRec ".
					"WHERE		i_Seg = ".$i_Seg." ".
					" 			and n_Prop= ".$nProp." ".
					" 			and t_Doc in (1,2) ".
					"			and s_Pagamento=2 ";
			
			$cur=odbc_exec($dbSisSeg,$sql);
			$PremioPago = odbc_result($cur,"tot");
		}
		else {
			$PremioPago = "0";
		}
		
		$sql = 	"UPDATE	#PA ".
				"SET 	PremioPago = ".$PremioPago;
		$cur=odbc_exec($db,$sql);

// 		exibe volumeExcluido (por DVE)
		if ($statePa == 1 && !$mantem) {
			// primeiro cálculo - determina se negado em função de ChangeCredit
			$sql = 	"SELECT	sum(totalEmbarcado) as totalEmbarcado, e.num ".
					"FROM 	#Embarques e join ChangeCredit c on e.idChangeCredit = c.id ".
					"WHERE 	c.state  in (7,1,4) or (c.credit = 0 and (c.creditTemp <> 0 or c.limTemp > e.embDate))".
					"GROUP BY e.num ".
					"ORDER BY e.num";
		}
		else {
			//determina se negado em função do campo "negado"
			$sql = 	"SELECT	sum(totalEmbarcado) as totalEmbarcado, e.num ".
					"FROM 	#Embarques e ".
					"WHERE 	e.negado = 1 ".
					"GROUP BY e.num ".
					"ORDER BY e.num";
		}
		
		$cur = odbc_exec($db,$sql);
		$i = 0;
?>

<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0">
	<TR>
    	<TD colspan="5" align="center"><label>Importadores Negados</label></TD>
	</TR>
	<TR class="bgAzul">
    	<TD width="25%"></TD>
    	<TD width="5%" align="center"><label>DVN</label></TD>
    	<TD width="20%"></TD>
    	<TD align="right" width="20%"><label>Valor (U$)</label></TD>
		<TD></TD>
	</TR>

<?php  $volumeExcluido = 0;
		while (odbc_fetch_row($cur)) {
			$i = $i + 1;
			$totalEmbarcado = odbc_result($cur,"totalEmbarcado");
			$num = odbc_result($cur,"num");
			$volumeExcluido = $volumeExcluido + $totalEmbarcado;
?>

	<TR <?php  echo $i % 2 ? "" : " bgcolor=#e9e9e9";?>>
    	<TD class="texto"></TD>
    	<TD align="center" class="texto"><?php  echo $num;?>ª</TD>
    	<TD class="texto"></TD>
    	<TD align="right" class="texto"><?php  echo number_format($totalEmbarcado, 2, ',', '.');?></TD>
		<TD class="texto"></TD>
	</TR>

<?php  }
		if ($i == 0) {
?>

	<TR>
    	<TD colspan="5" align="center" class="texto"><label>Nenhum importador negado</label></TD>
	</TR>

<?php  }
//		$volumeExcluido = $volumeExcluido;
?>

	<TR class="bgAzul">
    	<TD colspan="3" align="right" ><label>Volume Total Negado:&nbsp;&nbsp;&nbsp;</label></TD>
    	<TD align="right">U$ <?php  echo number_format($volumeExcluido, 2, ',', '.');?></TD>
		<TD align="center"><button type=button onClick="javascript:detalhe();" value="Detalhe" class="botaoagm">Sair</button>&nbsp;&nbsp;&nbsp;&nbsp;
		</TD>
	</TR>
</TABLE>

<BR>
<BR>

<?php  $sql = 	"UPDATE #PA  set volumeExcluido = ".$volumeExcluido;
		$cur=odbc_exec($db,$sql);
//		echo "<br>".$sql."<br>";

		$sql = 	"UPDATE #PA set volumePA = (volumeTotal - volumeExcluido) ";
		$cur=odbc_exec($db,$sql);

		$sql = 	"UPDATE #PA set PremioEfetivo = round((volumePA * txPremio),2) ";
		$cur=odbc_exec($db,$sql);

		$sql = 	"UPDATE #PA set volumeMora = round((PremioEfetivo * txMora),2) ";
		$cur=odbc_exec($db,$sql);
		
		$sql = 	"UPDATE #PA set PA = (PremioEfetivo + volumeMora - PremioPago) ";
		$cur=odbc_exec($db,$sql);

		$sql = 	"SELECT * from #PA ";
		$cur=odbc_exec($db,$sql);
		
		$name = odbc_result($cur,"name");
		$volumeTotal = odbc_result($cur,"volumeTotal");
		$volumeExcluido = odbc_result($cur,"volumeExcluido");
		$volumePA = odbc_result($cur,"volumePA");
		$txPremio = odbc_result($cur,"txPremio");
		$PremioEfetivo = odbc_result($cur,"PremioEfetivo");
		$PremioPago = odbc_result($cur,"PremioPago");
		$PA = odbc_result($cur,"PA");
		$volumeMora = odbc_result($cur,"volumeMora");

		$temp = $PA;
		
		if ($PA < 0)
			$PA = 0;

		$cur=odbc_exec($db,"BEGIN TRAN");
		$ok = true;
		
		if ($statePa == 1 && !$mantem) {
			// primeiro cálculo - limpa o status de negado
			// e atualiza o status de negado em função da tabela #Embarques e ChangeCredit
			$sql ="UPDATE DVEDetails set negado = 0 ".
					"WHERE EXISTS ( ".
					"				SELECT	e.id ".
					"				FROM 	#Embarques e ".
					"				WHERE	DVEDetails.id = e.id )";
			$cur=odbc_exec($db,$sql);
			if (!$cur)
				$ok = false;
			
			$sql ="UPDATE DVEDetails set negado = 1 ".
					"WHERE id IN ( ".
					"				SELECT	e.id ".
					"				FROM 	#Embarques e, ChangeCredit c ".
					"				WHERE	e.idChangeCredit = c.id ".
					"						AND	(c.state  in (7,1,4) or (c.credit = 0 and (c.creditTemp <> 0 or c.limTemp > e.embDate))))";
// antes estava assim:					
//					"						AND	(c.state  in (7,1,4) or c.credit = 0) )";
					
			//echo "$sql		 <br>";
			$cur=odbc_exec($db,$sql);
			if (!$cur)
				$ok = false;
		}
		else {
			// pode ter registro novo em DVEDetails, então atualiza campo "calculado" de todos
			$sql = 	"UPDATE DVEDetails set calculado = 1 ".
					"WHERE	id IN ( ".
					"			SELECT 	dt.id ".
					"			FROM	DVEDetails dt, DVE d ".
					"			WHERE	dt.idDVE = d.id ".
					"					AND d.idInform = $idInform )";
			$cur=odbc_exec($db,$sql);
			if (!$cur)
				$ok = false;
			
			// pode ter registro novo em DVEDetails, então regrava o status de negado em função de #Embarques
			$sql = 	"UPDATE DVEDetails set negado = 1 ".
					"WHERE id IN ( ".
					"				SELECT	e.id ".
					"				FROM 	#Embarques e ".
					"				WHERE	e.negado = 1 )";
			$cur=odbc_exec($db,$sql);
			if (!$cur)
				$ok = false;
		}
		
		if ($ok)	
			$cur=odbc_exec($db,"COMMIT TRAN");
		else 
			$cur=odbc_exec($db,"ROLLBACK TRAN");
		
		$sql = 	"SELECT sum(valor) tot from PADet WHERE idInform = ".$idInform;
		$cur=odbc_exec($db,$sql);
		$PAEmitida = odbc_result($cur,"tot");
?>
<FORM name="Form1" action="" method="post">
        <TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0">
            <TR>
                <TD align="center" colspan="3"><label>Resumo do C&aacute;lculo da Parcela de Ajuste</label></TD>
            </TR>
            <TR>
                <TD width="50%">&nbsp;</TD>
                <TD width="25%"></TD>
                <TD width="25%"></TD>
            </TR>
            <TR>
                <TD align="right" class="texto"><label>Total L&iacute;quido Embarcado(TL):</label></TD>
                <TD align="right" class="texto">U$ <?php  echo number_format($volumePA, 2, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR>
                <TD align="right" class="texto"><label>Taxa de Pr&ecirc;mio (Tx):</label></TD>
                <TD align="right" class="texto"><?php  echo number_format($txPremio, 5, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR>
                <TD align="right" class="texto"><label>Pr&ecirc;mio Total (TL x Tx):</label></TD>
                <TD align="right" class="texto">U$ <?php  echo number_format($PremioEfetivo, 2, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR>
                <TD align="right" class="texto"><label>Juros de Mora:</label></TD>
                <TD align="right" class="texto">U$ <?php  echo number_format($volumeMora , 2, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR>
                <TD align="right" class="texto"><label>Pr&ecirc;mio Total + Juros:</label></TD>
                <TD align="right" class="texto">U$ <?php  echo number_format($PremioEfetivo + $volumeMora, 2, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR>
                <TD align="right" class="texto"><label>Pr&ecirc;mio Pago:</label></TD>
                <TD align="right" class="texto">U$ <?php  echo number_format($PremioPago, 2, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR>
                <TD align="right"><label>Parcela de Ajuste Devida</label>:</TD>
                <TD align="right">U$ <?php  echo number_format($temp, 2, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR>
                <TD align="right"><label>Desconto:</label></TD>
                <TD align="right">U$ <?php  echo number_format($descontoPa, 2, ',', '.');?></TD>
                <TD></TD>
            </TR>
            <TR class="bgAzul">
                <TD align="right"><label>Parcela de Ajuste:</label></TD>
                <TD align="right">U$ <?php  echo number_format($PAEmitida, 2, ',', '.');?></TD>
                <TD align="center"></TD>
            </TR>
        </TABLE>

<br />

<?php  }
}
?>
	<TABLE cellSpacing=0 cellpadding="2" width="100%" align="center" border="0">
		<TR>
	    	<TD align="center">
				<button type=button onClick="javascript:history.back();" class="botaoagm">OK</button>
			</TD>
		</TR>
	</TABLE>

</FORM>
</div>
