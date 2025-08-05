<?php //Criado Hicom 19/10/2004 (Gustavo)
?>
<SCRIPT language="javascript"> 

	function recalcular(){
		document.Form1.action = '../dve/dve.php?comm=atualizaDetPa';
		document.Form1.submit();
	}

	function ok(){
		document.Form1.action = '../dve/dve.php?comm=atualizaDetPaOK&mantem=1';
		document.Form1.submit();
	}

	function voltar(){
		document.Form1.action = '../dve/dve.php?comm=calculaPa&mantem=1';
		document.Form1.submit();
	}

	function ShowObj(id,obj){
		var i = 0;
 		for (i==0;i < document.getElementsByName(obj).length;i++) {
   			document.getElementsByName(obj)[i].style.display = "none";
 		}

		if (document.getElementsByName(obj)[id].style.display == "block"){
	   		document.getElementsByName(obj)[id].style.display = "none";
 		}
		else {
   			document.getElementsByName(obj)[id].style.display = "block";
 		}    
	}
	
</SCRIPT>

<?php $sql =	"SELECT	inf.name, inf.i_Seg, inf.startValidity, ".
		" inf.endValidity, isnull(inf.statePa, 1) statePa, inf.txMTotal, inf.prMTotal ".
		"FROM 		Inform inf ".
		"WHERE		inf.id = ".$idInform;
$cur=odbc_exec($db,$sql);

$name = odbc_result($cur,"name");
$startValidity = odbc_result($cur,"startValidity");
$endValidity = odbc_result($cur,"endValidity");
$i_Seg = odbc_result($cur,"i_Seg");
$statePa = odbc_result($cur,"statePa");
$txMTotal = odbc_result($cur,"txMTotal");
$prMTotal = odbc_result($cur,"prMTotal");


if ($statePa == 1)
	$sit = "Não calculada";
if ($statePa == 2)
	$sit = "Calculada";
if ($statePa == 3)
	$sit = "Financeiro";
if ($statePa == 4)
	$sit = "Emitida";

?>

<?php include_once("../../../navegacao.php");?>

<div class="conteudopagina">

	<li class="campo2colunas">
        <label>Cliente</label>
        <?php echo ($name); ?>
    </li>
    
    <li class="campo2colunas">
        <label>Vig&ecirc;ncia</label>
         <?php echo  substr($startValidity, 8, 2)."/".substr($startValidity, 5, 2)."/".substr($startValidity, 0, 4).
			" a ".substr($endValidity, 8, 2)."/".substr($endValidity, 5, 2)."/".substr($endValidity, 0, 4);?>
    </li>
    
    <li class="campo2colunas">
        <label>Situa&ccedil;&atilde;o</label>
        <?php echo ($sit);?>
    </li>

<form name="Form1" action="" method="post">

	<li class="campo2colunas">
        <label>Embarques efetuados</label>
        <select id="formDVE" name="formDVE" onChange="javascript:ShowObj(selectedIndex,'tabDVE');">
			<?php $sql = 	"SELECT	d.num ".
                    "FROM 	DVE d left join DVEDetails dt on d.id = dt.idDVE ".
                    "WHERE 	d.idInform = ".$idInform." ".
                    "GROUP BY num ";
                    
            $curDVE=odbc_exec($db,$sql);
            while (odbc_fetch_row($curDVE)) {
                $numDVE = odbc_result($curDVE,"num");
            ?>
                 <option value=<?php echo $numDVE;?> <?php echo  $formDVE == $numDVE ? ' SELECTED' : '';?>><?php echo $numDVE;?>&ordf; DVE</option>
            <?php }?>					
		 </select>
    </li>

	<?php odbc_fetch_row($curDVE,0);
    
    // determina se negado em função da tabela DVEDetails (campo negado)
    $sql = 	"SELECT	d.num, ".
            "		d.inicio, ".
            "		dt.id, ".
            "		dt.embDate, ".
            "		round(dt.totalEmbarcado,2) totalEmbarcado, ".
            "		isnull(dt.negado,0) negado, ".
            "		imp.name ".
            "FROM 	DVE d, DVEDetails dt, Importer imp ".
            "WHERE 	d.id = dt.idDVE ".
            "		AND dt.idImporter = imp.id ".
            "		AND d.idInform = ".$idInform." ".
            "		AND isnull(dt.state,1) = 1 ".
            "		AND d.state > 1 ".
            "ORDER BY d.num, imp.name ";
    
    $cur=odbc_exec($db,$sql);
    
    $totDVE = 0;
    $totDVENegado = 0;
    $totDVEGeral = $totExpInfEst;
    $totDVENegadoGeral = 0;
    
    while (odbc_fetch_row($curDVE)) {
    
        $numDVE = odbc_result($curDVE,"num");
        
        // vai p/ primeira linha
        odbc_fetch_row($cur,0);
        
        $i = 0;
    ?>
    
	<table class="tabela01">
    	<thead>
            <tr>
                <th>Importador</th>
                <th>&nbsp;Embarque</th>
                <th>&nbsp;Valor (U$)</th>
                <th>&nbsp;Negado?</th>
            </tr>
        </thead>
        <tbody>

		<?php while (odbc_fetch_row($cur)) {
                $num = odbc_result($cur,"num");
                $inicio = odbc_result($cur,"inicio");
                $id = odbc_result($cur,"id");
                $totalEmbarcado = odbc_result($cur,"totalEmbarcado");
                $negado = odbc_result($cur,"negado");
                $name = odbc_result($cur,"name");
                $embDate = odbc_result($cur,"embDate");
                
                // exibe embarques (por DVE)
                if ($num == $numDVE) {
        		// $totDVEGeral = $totDVEGeral + $totalEmbarcado;
                    $totDVE = $totDVE + $totalEmbarcado;
                    if ($negado == 1) {
                        $totDVENegadoGeral = $totDVENegadoGeral + $totalEmbarcado;
                        $totDVENegado = $totDVENegado + $totalEmbarcado;
                    }
                    $i = $i + 1;
        ?>

		<tr>
	    	<td><?php echo ($name);?></td>
	    	<td><?php echo  substr($embDate, 8, 2)."/".substr($embDate, 5, 2)."/".substr($embDate, 0, 4);?></td>
	    	<td><?php echo  number_format($totalEmbarcado, 2, ',', '.');?></td>
	    	<td><input type="checkbox" name="negado[]" value="<?php echo $id;?>" <?php if ($negado == 1) echo "checked";?> /></td>
		</tr>

		<?php }
            }
            if ($i == 0) {
        ?>

		<tr>
	    	<td colspan="4">Nenhum embarque realizado</td>
		</tr>

		<?php }?>
		
        </tbody>
		<tr>
	    	<td>Total embarcado (U$):&nbsp;</td>
	    	<td colspan="2"><?php echo  number_format($totDVE, 2, ',', '.');?></td>
	    	<td>&nbsp;</td>
		</tr>
		<tr>
	    	<td>Total negado (U$):&nbsp;</td>
	    	<td colspan="2"><?php echo  number_format($totDVENegado, 2, ',', '.');?></td>
	    	<td>&nbsp;</td>
		</tr>
	</table>

<?php $totDVE = 0;
	$totDVENegado = 0;
}
$totalLiquido = $totDVEGeral - $totDVENegadoGeral;

//$premioTotal = round(($totalLiquido * $txPremio),2);

$premioTotal = round(($totalLiquido * $txMTotal),2);
$volumeMora = round(($premioTotal * $txMora),2)

?>
	<div class="divisoria01"></div>
    <h3>Resumo do C&aacute;lculo da Parcela de Ajuste</h3>
	
    <li class="campo3colunas">
    	<label>Total L&iacute;quido Embarcado(TL)</label>
		$<?php echo number_format($totalLiquido, 2, ',', '.');?>
    </li>
    
    <li class="campo3colunas">
    	<label>Taxa de Pr&ecirc;mio (Tx)</label>
		<?php echo number_format($txMTotal, 5, ',', '.');?>%
    </li>
    
    <li class="campo3colunas">
    	<label>Pr&ecirc;mio Total (TL x Tx)</label>
		$<?php echo number_format($premioTotal, 2, ',', '.');?>
    </li>
    
    <li class="campo3colunas">
    	<label>Juros de Mora</label>
		U$ <?php echo number_format($volumeMora , 2, ',', '.');?>
    </li>
    
    <li class="campo3colunas">
    	<label>Pr&ecirc;mio Total + Juros</label>
		U$ <?php echo number_format($premioTotal + $volumeMora, 2, ',', '.');?>
    </li>
    
    <li class="campo3colunas">
    	<label>Pr&ecirc;mio Pago</label>
		$<?php echo number_format($PremioPago, 2, ',', '.');?>
    </li>
    
    <li class="campo3colunas">
    	<label>Parcela de Ajuste Devida</label>
		$<?php echo number_format($premioTotal + $volumeMora - $PremioPago, 2, ',', '.');?>
    </li>
    
    <li class="campo3colunas">
    	<label>Desconto</label>
		$<?php echo number_format($descontoPa, 2, ',', '.');?>
    </li>
    
    <li class="campo3colunas">
    	<label>Parcela de Ajuste</label>
		$<?php echo number_format($PAEmitida, 2, ',', '.');?>
    </li>
    
	<div class="barrabotoes">
    	<input type="hidden" name="status" value="<?php echo $status;?>">
        <input type="hidden" name="cliente" value="<?php echo $cliente;?>">
        <input type="hidden" name="opcDVE" value="<?php echo $opcDVE;?>">
        <input type="hidden" name="opcPA" value="<?php echo $opcPA;?>">
        <input type="hidden" name="idInform" value="<?php echo $idInform;?>">
        <input type="hidden" name="PremioPago" value="<?php echo $PremioPago;?>">
        <input type="hidden" name="txPremio" value="<?php echo $txMTotal;?>">
        <input type="hidden" name="idNotification" value="<?php echo $idNotification;?>">
        <input type="hidden" name="txMora" value="<?php echo $txMora;?>">
        <input type="hidden" name="descontoPa" value="<?php echo $descontoPa;?>">
        <input type="hidden" name="totExpInfEst" value="<?php echo $totExpInfEst;?>">
        
        
        <?php if ($statePa == 1 || $statePa == 2) {

			if ($role["dve"]) {
		?>
        	    <button name="Recalcular"  type="button" onClick="javascript:recalcular();" class="botaoagg">Recalcular</button>
                <button name="ok"   type="button" onClick="javascript:ok();" class="botaoagm">OK</button>
		<?php }
        }
        ?>
				<button name="voltar" type="button" onClick="javascript:voltar();" class="botaovgm">Voltar</button>
            
	</div>
</form>
</div>
<SCRIPT language="javascript">

	if (document.getElementsByName('tabDVE').length > 0) 
		echo ("verErro('teste1');");
		ShowObj(0,'tabDVE');
		
<?php //echo ("verErro('teste-$formDVE');");
if ($formDVE) {
	echo ("ShowObj(".($formDVE - 1).",'tabDVE');");
}
?>		
	
</SCRIPT>
