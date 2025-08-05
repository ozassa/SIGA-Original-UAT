<br style="clear:both">

<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">  
   <label><h2>Par&acirc;metros de An&aacute;lise e Monitoramento</h2></label>
</li>

<li class="campo3colunas">  
 <?php
	   
   $sql7  = "select i_Item, Descricao_Item from Campo_Item where i_Campo = 110 and Situacao = 0 order by i_Item 
		   ";
   $cur7 = odbc_exec($db,$sql7);
 ?>
   <label>Regra de Cobran&ccedil;a</label>
	   <select name="Forma_Cobranca" id="Forma_Cobranca" onChange=" if(this.value != 4){ document.getElementById('valcampo').style.display = 'none'; }else{ document.getElementById('valcampo').style.display = 'block';}">
		 <option value="">Selecione...</option>
		 <?php while ($dados7 = odbc_fetch_row($cur7)){ 
				 if($Forma_Cobranca == odbc_result($cur7,'i_Item') )
					 $selt = 'selected';
				 else
					$selt = ''; 
		   ?>
			  <option value="<?php echo odbc_result($cur7,'i_Item');?>"  <?php echo $selt;?>><?php echo (odbc_result($cur7,'Descricao_Item'));?></option>
		  <?php  }  ?>
   </select>
</li> 

<li class="campo3colunas">
  <label>Taxa de An&aacute;lise R$:</label>
  <input name="txAnalize" style="text-align: right;" onBlur="checkDecimals(this, this.value)" value="<?php   echo  number_format($txAnalize, 2, ',', '.');?>">
</li>

<li class="campo3colunas">
   <label>Taxa de Monitoramento &nbsp;R$:</label>
	 <input name="txMonitor" style="text-align: right;" onBlur="checkDecimals(this, this.value)" value="<?php   echo  number_format($txMonitor, 2, ',', '.');?>">
</li>

<div id="valcampo" style="display:<?php echo($Forma_Cobranca != 4 ? 'none' : 'block');?>">
	<li class="campo3colunas">
	   <label>Data Primeiro Vencimento</label>
		 <input name="Primeiro_Vencimento" id="Primeiro_Vencimento"  value="<?php   echo  Convert_Data_Geral(substr($primeiro_venc,0,10));?>" onKeyUp="mascara(this,data);" maxlength="10" style="width:240px;">
		 <img src="<?php echo $host; ?>images/icone_calendario.png" name="imgPrimeiro_Vencimento" id="imgPrimeiro_Vencimento" alt="" class="imagemcampo" />
		 <script type="text/javascript">
			Calendar.setup({
				inputField     :    "Primeiro_Vencimento",     	// id of the input field
				ifFormat       :    "dd/mm/y",      	// format of the input field
				button         :    "imgPrimeiro_Vencimento",  	// trigger for the calendar (button ID)
				align          :    "Tl",           	// alignment (defaults to "Bl")
				singleClick    :    true
			});
		</script>
		
	</li>
	
	<li class="campo3colunas">
	   <label>N&uacute;mero de Parcelas</label>
		 <input name="Numero_Parcelas" id="Numero_Parcelas" style="text-align: right;" onBlur="" value="<?php   echo  $num_parc;?>">
	</li>
	
	<li class="campo3colunas">  
	   <label>Tipo Vencimento</label>
		<?php
		   
			$sql8  = "select i_Item, Descricao_Item from Campo_Item where i_Campo = 130 and Situacao = 0 order by i_Item 
			   ";
			 $cur8 = odbc_exec($db,$sql8);
		  ?>
		   <select name="Tipo_Vencimento" id="Tipo_Vencimento" onChange="">
			 <option value="">Selecione...</option>
			 <?php while ($dados8 = odbc_fetch_row($cur8)){ 
					 if($Tipo_Vencimento == odbc_result($cur8,'i_Item') )
						 $selt = 'selected';
					 else
						$selt = ''; 
			   ?>
				  <option value="<?php echo odbc_result($cur8,'i_Item');?>"  <?php echo $selt;?>><?php echo (odbc_result($cur8,'Descricao_Item'));?></option>
			  <?php  }  ?>
	   </select>
	</li> 
</div>


<?php 
	if($Forma_Cobranca){
		$sql  = "SELECT 
	            *
	          FROM Campo_Item CI
	          WHERE CI.i_Campo = 110 AND CI.i_Item = ".$Forma_Cobranca;
		$cur = odbc_exec($db,$sql);

		$descCob = (odbc_result($cur,'Texto_Item'));
	} else{
		$descCob = '';
	}
?>
<li class="valDescCob-js" style="display:<?php echo($Forma_Cobranca == '' ? 'none' : 'block');?>">
	 <label>Descrição da Regra:</label>
	 <textarea name="descReg" class="descReg" style="resizable:width:100%;max-width:915px;max-height:150px;" disabled><?php echo $descCob; ?></textarea>
</li>

<li class="campo3colunas">
	<div class="formopcao">
		  <input  type="checkbox" name="Cobra_Apenas_Comprador_Novo" id="Cobra_Apenas_Comprador_Novo" style="text-align: right;"  value="<?php   echo  $Cobra_Apenas_Comprador_Novo;?>" <?php echo  ($Cobra_Apenas_Comprador_Novo ? 'checked' : '');?> onClick="if(this.checked) this.value = 1; else this.value = 0;">
	</div>
	 <div class="formdescricao">Cobra apenas compradores novos</div>
 </li>
	

<br clear="all">

<!--  fim  -->