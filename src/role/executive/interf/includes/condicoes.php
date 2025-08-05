<div class="divisoria01">&nbsp;</div>   
<div style="clear:both">&nbsp;</div>


<li class="campo2colunas">
<label>Renovação Tácita:</label>
<select name="Renovacao_Tacita" id="Renovacao_Tacita" >
	<option value="">Selecione</option>
	<option value="1" <?php echo ($Renovacao_Tacita == 1 ? 'selected' : '');?> >Sim</option>
	<option value="0" <?php echo ($Renovacao_Tacita == 0 ? 'selected' : '');?>>Não</option>
</select>
</li>   

<li class="campo2colunas">
<label>Prazo para aviso da não intenção de renovação:</label>
<input type="text" name="prazo_nao_intencao_renov" id="prazo_nao_intencao_renov" value="<?php echo $data_nao_intencao_renov; ?>">
</li>

<div style="clear:both">&nbsp;</div>
<?php 
if ($adeaquacao != ""){
	$ativaAdeaquacao = "block";
	$chekAdequacao = 1;
}else{
	$ativaAdeaquacao = "none";
	$chekAdequacao = 0;
}
?>

<?php if ($i_Produto == 2){ ?>	
	<div id="container_adeq_prem"> 
		<div id="adeq_prem">
			<li  class="campo3colunas" style="width:100%;">
				<div class="formopcao">
						<input type="checkbox" name="Ad" id="Ad" value="<?php echo $chekAdequacao;?>" <?php echo $adeaquacao;?> onClick="javascript: visualiza(); viewAdequacao(this);" ></div>
						<div class="formdescricao"><span>Adequa&ccedil;&atilde;o de pr&ecirc;mio</span></div>
				</li>

				<div style="clear:both">&nbsp;</div>
				<li id="viewAdequacao_campo" style="display:<?php   echo $ativaAdeaquacao;?>;list-style:none;"> 
				<ul>
					<li  class="campo3colunas" style="width:100%;">
							<label>Adequa&ccedil;&atilde;o de Sinistralidade %:</label>
							<input type="text" name="ad_sinistr" id="ad_sinistr" value="<?php echo $ad_sinistr;?>" style="text-align: right;width:300px !important;" onKeyPress="return numeros();" onBlur="tiraponto('ad_sinistr');"  />
						</li>
						
						<li  class="campo3colunas" style="width:100%;">
							<label>Adequa&ccedil;&atilde;o do Pr&ecirc;mio %:</label>
							<input type="text" name="ad_premio" id="ad_premio" value="<?php echo $ad_premio;?>"  style="text-align: right;width:300px !important;" onKeyPress="return numeros(); " onBlur="tiraponto('ad_premio');" />
						</li>
				</ul>
			</li>
		</div>
	</div>
<?php } else { ?>
	<input type="hidden" name="Ad" id="Ad" value="1" />
	<input type="hidden" name="ad_sinistr" id="ad_sinistr_hidden" value="<?php echo $ad_sinistr;?>" />
	<input type="hidden" name="ad_premio" id="ad_premio_hidden" value="<?php echo $ad_premio;?>" />
<?php } ?>

<?php  
if ($CondEsp != ""){
	$ativaDivcondesp = "block";
	$chekcondesp = 1;
}else{
	$ativaDivcondesp = "none";
	$chekcondesp = 0;
}

?>
<div style="clear:both">&nbsp;</div>

<li  class="campo2colunas" style="width:100%;">
 <div class="formopcao">
 <input type="checkbox" name="condesp_ck" id="condesp_ck" value="<?php   echo $chekcondesp;?>" <?php   echo $CondEsp?> onClick="javascript: visualizacondesp(this);" >
 </div>
 <div class="formdescricao"><span>Condi&ccedil;&otilde;es Complementares</span></div>
</li>

<li id="condicoes_esp" style="display:<?php   echo $ativaDivcondesp;?>;list-style:none;">
<div style="clear:both">&nbsp;</div>
<div><textarea name="condicoes_especiais" id="condicoes_especiais" rows="10" onKeyPress="javascript: ContaCaracteres('condicoes_especiais',10000,'');" onBlur="limitarCaracteresInfo('condicoes_especiais',10000,'');"><?php  echo ($condicoes_especiais); ?></textarea>
<div style="clear:both"></div>
<?php $tocart = strlen($condicoes_especiais); ?>
<div class="formdescricao"><span>Caracteres Restantes:</span><input type="text" name="cont" id="cont" value="<?php echo (10000 - $tocart);?>" style="border: none; width:35px;position:relative;*top:15px;" readonly/></span></div>
</li>

<script>
function visualizaRisco(){
	if(document.getElementById('risk').style.display == 'none'){
		document.getElementById('risk').style.display = 'block';
	}else{
		document.getElementById('risk').style.display = 'none';
		document.getElementById('texto_risco_politico').value = '';
	}
}
</script>
<li  class="campo2colunas" style="width:90%;">
 <div class="formopcao">
 <input type="checkbox" name="rico_pol_ck" id="rico_pol_ck" value="<?php   echo $RiscoPolitico;?>" <?php   echo ($RiscoPolitico ? 'checked' : '');?>  onClick="javascript: visualizaRisco(this);" >
 </div>
 <div class="formdescricao"><span>Risco Pol&iacute;tico</span></div>
</li>
<li  id="risk" class="campo2colunas" style="display:<?php echo ($RiscoPolitico ? 'block' :'none');?>">       
 <label>Informe o Risco Político</label>
 <input type="text" name="texto_risco_politico" id="texto_risco_politico"  maxlength="99" value="<?php   echo $RiscoPolitico;?>">       
</li>	

<li style="clear:both;list-style:none;height: 0;">&nbsp;</li>

<?php
	  
	 $query = "SELECT a.id,a.idInform,a.razaoSocial,a.endereco,a.pais,b.name,
				 a.zipCode,a.taxID FROM ParModEsp_Coligada a inner join Country b on a.pais = b.id
	 WHERE a.idInform = $idInform ORDER BY a.razaoSocial ";
	 
	
	 $curxz_col = odbc_exec ($db, $query);

	
	
	 if (odbc_result($curxz_col,'id')){
		 $CondEsp_col = 'checked="checked"';   
		 $chekcondesp_col = 1;
		 $ativaDivcondesp = "block";
	 }else{
		 $CondEsp_col  = "";
		 $chekcondesp_col = 0;
		 $ativaDivcondesp = "none";
	 }

	 if ($npc == 1){
		 $npc_col = 'checked="checked"';   
		 $cheknpc_col = 1;
		 $ativanpc = "block";
	 }else{
		$npc_col = '';   
		$cheknpc_col = 0;
		$ativanpc = "none";
	 }
	  
	   
?>

<li  class="campo2colunas" style="list-style:none;">
	  <div class="formopcao">
	  <input type="checkbox" name="npc_col" id="npc_col" value="1" <?php   echo $npc_col;?> >
	  </div>
	  <div class="formdescricao">NPC</div>
</li>

<li style="clear:both;list-style:none;height: 0;">&nbsp;</li>

 <li  class="campo2colunas" style="list-style:none;">
	  <div class="formopcao">
	  <input type="checkbox" name="condesp_ck_col" id="condesp_ck_col" value="<?php   echo $chekcondesp_col;?>" <?php   echo $CondEsp_col;?> onClick="javascript: visualizacondesp_col(this);" >
	  </div>
	  <div class="formdescricao">Condi&ccedil;&atilde;o Especial de Cobertura de Coligadas</div>
</li>


<li id="condicoes_esp_col" style="display:<?php echo $ativaDivcondesp;?>;list-style:none">
	
	<ul>
	
		<li style="clear:both">&nbsp;</li>
		<li  class="campo3colunas">
			<label>Raz&atilde;o Social</label>
			<input type="text" name="RazaoSocialCol" id="RazaoSocialCol" value="">
		</li>
		<li  class="campo3colunas">
			<label>Endere&ccedil;o</label>
			<input type="text" name="EnderecoCol" id="EnderecoCol" value="">
		</li>
		<li  class="campo3colunas" style="height:70px;">
			<label>Pa&iacute;s</label>
			<?php
						 $qryCo = "select * from Country order by name";
						 $curCo = odbc_exec ($db, $qryCo);
					 ?>
					 <select name="PaisCol" id="PaisCol">
						<option value="">Selecione...</option>
					 <?php   while (odbc_fetch_row($curCo)) {   ?>
								 <option value="<?php echo odbc_result ($curCo, 'id');?>"><?php echo (odbc_result ($curCo, 'name'));?></option>
					 <?php   }   ?>  
					 </select>
			
			
		</li>
		<li  class="campo3colunas">
			<label>Zip Code</label>
			<input type="text" name="ZipCodeCol" id="ZipCodeCol" value="">
		</li>
		<li  class="campo3colunas">
			<label>Tax ID</label>
			<input type="text" name="TaxIdCol" id="TaxIdCol" value="">
		</li>
		
		<li  class="campo3colunas">
			<label>&nbsp;</label>
			<input type="hidden" name="empresaColID" id="empresaColID" value="">
		</li>
		
		<li class="barrabotoes" style="list-style:none;"> 
		   <button type="button" class="botaoagg" onClick="javascript: loadHTMLIE('<?php echo $root.'role/executive/interf/Adiciona_Coligadas.php'; ?>','Retorno2','<?php if($_SESSION['browser'] == 'IE') echo 'GET'; else echo 'POST'; ?>','','<?php echo $idInform;?>',document.getElementById('Operacao').value,'EmpresaColigada');return false;" />Adicionar Coligadas</button>
		</li>

	  <li id="Retorno2">
			<table summary="Submitted table designs">
			   <thead>
				   <th scope="col">Raz&atilde;o Social</th>
				   <th scope="col">Endere&ccedil;o Completo</th>
				   <th scope="col">Pa&iacute;s</th>
				   <th scope="col">ZIP Code</th>
				   <th scope="col">Tax ID</th>
				   <th scope="col" colspan="2">&nbsp;</th>
			  </thead>
			  <tbody> 
			  	<?php 
					  $curxz_col = odbc_exec ($db, $query);
						while (odbc_fetch_row($curxz_col)){    
							$razao_social_col   = (odbc_result ($curxz_col, 'razaoSocial'));
							$idInform_Col       = odbc_result ($curxz_col, 'idInform');
							$endereco_col       = (odbc_result ($curxz_col, 'endereco'));
							$pais_col           = (odbc_result ($curxz_col, 'pais'));
							$zipcode_col        = odbc_result ($curxz_col, 'zipCode');
							$taxID_col          = odbc_result ($curxz_col, 'taxID'); 
							$nomePaisCol        = (odbc_result ($curxz_col, 'name'));
							$idEmpresa          = (odbc_result ($curxz_col, 'id'));	?>
					 	<tr>
						 	<td><?php echo $razao_social_col;?></td>
						 	<td><?php echo $endereco_col;?></td>
						 	<td><?php echo $nomePaisCol;?></td>
						 	<td><?php echo $zipcode_col;?></td>
						 	<td><?php echo $taxID_col;?></td>
						 	<td><a href="#" onClick="edita_formColigada('<?php echo $idEmpresa;?>','<?php echo $razao_social_col; ?>','<?php echo $endereco_col;?>','<?php echo $pais_col; ?>','<?php echo $zipcode_col; ?>','<?php echo $taxID_col; ?>');return false;"><img src="<?php echo $root;?>images/icone_editar.png" alt=""  title="Editar Registro" width="24" height="24" class="iconetabela" /></a></td>
						 	<td><a href="#" onClick="javascript: loadHTMLIE('<?php echo $root.'role/executive/interf/Adiciona_Coligadas.php'; ?>','Retorno2','<?php if($_SESSION['browser'] == 'IE') echo 'GET'; else echo 'POST'; ?>','<?php echo $idEmpresa;?>','<?php echo $idInform;?>','Remover','EmpresaColigada');return false;"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a></td>
						</tr>
					<?php }  ?>
			  </tbody>  
			</table>
	  </li>
	</ul>

</li>