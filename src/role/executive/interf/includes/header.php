<?php
// Trecho que verifica se há corretor principal e se a soma da participacao é até 100%

$cqry = " select idCorretor,CorretorPrincipal from InformCorretor 
where idInform = '".$idInform."' and CorretorPrincipal = 1 and idCorretor >0;";
$corr = odbc_exec($db,$cqry);

if(odbc_fetch_row($corr) > 0){
	$temCorretorPrincipal  = odbc_result($corr,'idCorretor');
}else{
	$temCorretorPrincipal  = 0;
}

$ParticipacaoCorr  = 0;

if($corr){
	$cqry_par = " select sum(p_Corretor) as Participacao from InformCorretor where idInform = '".$idInform."' and idCorretor > 0";

	$corr1 = odbc_exec($db, $cqry_par);	

	if(odbc_fetch_row($corr1) > 0){
		$ParticipacaoCorr  = odbc_result($corr1,'Participacao');
	}
}
?>

<?php
if ($msg != "") {?>
<p><font color="red"><?php   echo  $msg;?></font></p>
<?php  } ?>



<div style="clear:both"> </div>
<li class="campo3colunas"><label>Nome do Respons&aacute;vel:</label><div class="formdescricao"><span><?php   echo  ($respName);?></span></div></li>
<li class="campo3colunas"><label>Cargo:</label><div class="formdescricao"><span><?php   echo  ($ocupation);?></span></div></li> 
<li class="campo3colunas"><label>Data de Cadastro:</label><div class="formdescricao"><span><?php echo substr($date,8,2)."/".substr($date,5,2)."/".substr($date,0,4);?></span></div></li> 
<div style="clear:both">&nbsp; </div>
<li class="campo3colunas"><label>Data de Tarifa&ccedil;&atilde;o:</label><div class="formdescricao"><span><?php   echo  $tarifDate;?></span></div></li> 
<li class="campo3colunas"><label>Email para enviar Oferta/Proposta:</label><div class="formdescricao"><span><?php   echo  ($emailContact); ?></span></div></li> 
<li class="campo3colunas"><label>DPP:</label><div class="formdescricao"><span><?php   echo  $DPP; ?></span></div></li> 
<li class="campo3colunas"><label>Per&iacute;odo de Vig&ecirc;ncia Selecionado:</label><div class="formdescricao"><span><?php  echo ($Periodo_Vigencia ? $Periodo_Vigencia . ' Meses': $pvigencia);?></span></div></li> 
<li class="campo3colunas"><label>Tipo de Moeda Selecionado:</label><div class="formdescricao"><span><?php  echo ($tmoeda);?></span></div></li> 
<input type="hidden" name="currency" value="<?php echo  $moeda; ?>">
<div style="clear:both">&nbsp; </div>
<li class="campo3colunas"> 
	<label>Produto</label>
	<?php 
		$sql = "select * from Produto where i_Produto = ".$i_Produto;
		$result = odbc_exec($db,$sql);
		$DescricaoProduto = odbc_result($result,'Nome');
		echo ($DescricaoProduto);
	?>
</li>
<li class="campo3colunas"> 
	<label>Sub-Produto</label>
	<?php 
		$sql = "select * from Sub_Produto where i_Produto = ".$i_Produto;
		$result = odbc_exec($db,$sql);
	?>
	<select name="i_Sub_Produto" id="i_Sub_Produto" onChange="exibir_modulo(this.value,'<?php echo $idInform ?>','<?php echo $root.'role/executive/interf/Exibir_modulos.php'; ?>');">
		<option value="">Selecione...</option>
		<?php while(odbc_fetch_row($result)){ ?>
			<option value="<?php echo odbc_result($result,'i_Sub_Produto');?>" <?php if($i_Sub_Produto == odbc_result($result,'i_Sub_Produto')) echo 'Selected';?>><?php echo (odbc_result($result,'Descricao'));?></option>
		<?php } ?>
</select> 



</li>
<li class="campo3colunas">       
	<label>Origem do Neg&oacute;cio:</label>           
	<select name="origemNegocio" id="origemNegocio" onChange="">
		<?php  
			$cSql = "Select Distinct n_Frontier, Pais From Frontier Order By Pais";

			$cur2 = odbc_exec($db,$cSql);
			echo '<option value="">Selecione o Pa&iacute;s</option>';
			while (odbc_fetch_row($cur2)){ 
			?>
			<option value="<?php   echo odbc_result($cur2, 'n_Frontier');?>" <?php  if (odbc_result($cur2, 'n_Frontier') == $origemNegocio) echo "selected";?>><?php   echo (odbc_result($cur2, 'Pais'));?></option>
		<?php  } ?>
	</select>
	</li>