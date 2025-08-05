<?php

//$idInform = $_REQUEST['idInform'];
//$idNotification   = $_REQUEST['idNotification'];
  
$sql = "SELECT Inf.id As IDInform, Inf.contrat, Inf.i_Seg, Inf.nProp, Inf.name As Segurado, 
        Inf.cnpj, Inf.startValidity, Inf.endValidity As FimVigencia, Inf.emailContact, Inf.contact, 
		Inf.email, Inf.idInsured As idInsured from Inform Inf WHERE Inf.id = " . $old;
$t = odbc_exec($db, $sql);

$endValidity = trim(odbc_result($t, "FimVigencia"));
$name = trim(odbc_result($t, "Segurado"));
$idInsured = trim(odbc_result($t, "idInsured"));



?>

<div class="conteudopagina">
	<ul>
	<li class="campo2colunas">
		<label>Nome</label>
		<?php echo ($name); ?>
	</li>

	<li class="campo2colunas">
		<label>Fim da vig&ecirc;ncia</label>
		<?php   echo ymd2dmy($endValidity);?>
	</li>
		</ul>
	<div class="barrabotoes">
		<form action="<?php   echo $root;?>role/executive/Executive.php" method="post">
        
		<?php  //Alterador por Tiago V N - Elumini - 21/11/2005
		$dtAtual = date("Y"). "-".date("m")."-".date("d");
		if($endValidity != ''){
			list($dia, $mes, $ano) = explode("/",ymd2dmy($endValidity));
			$fimVig  = $ano."-".$mes."-".$dia;
			$DataInicial = getdate(strtotime($fimVig)); 
		    $DataFinal = getdate(strtotime($dtAtual)); 
		    $dias = round (($DataFinal[0] - $DataInicial[0]) / 86400);
		}else{
			$DataInicial = ''; 
		    $DataFinal = getdate(strtotime($dtAtual)); 
		    $dias = 0;
			
		}
		
	    //print $dias;
		//if ( $dias <= "90"){ //Quando finaliza a vigencia vc tem 90 dias para renovar
			?>
			  
	<button type="button" style="float:left;margin-right:5px;" name="ver" class="botaoagg" onclick="window.location ='<?php echo $root;?>role/inform/Inform.php?comm=open&idInform=<?php echo $idInform;?>&idNotification=<?php   echo $idNotification;?>&volta=1&hc_cliente=N';">Ver Informe de renova&ccedil;&atilde;o</button>
	<button name="voltar" style="float:left;" onClick="this.form.comm.value='notif'; this.form.submit()" class="botaovgm">Voltar</button>      
	<br style="clear:both">
	<input type="hidden" name="comm" value="renovacao">
	<input type="hidden" name="done" value="1">
	<input type="hidden" name="idNotification" value="<?php   echo $idNotification;?>">
	<input type="hidden" name="idInform" value="<?php   echo $idInform;?>">        
        	</form>
	</div>
</div>