<?php include_once('../../../navegacao.php'); ?>

<script>
	function proc(opc){

		if (opc == 1 || opc == 3) { // alterar ou incluir
			if (Form1.Descricao_Perfil.value == '') {
				verErro('Informe o Perfil.');
		  	document.Form1.Descricao_Perfil.focus();
			} else if (Form1.Situacao_Perfil.value == '') {
				verErro('Informe o Situa&ccedil;&atilde;o.');
		  	document.Form1.Situacao_Perfil.focus();
			}	else {
				document.Form1.submit();
			}
		}
	}
</script>

<div class="conteudopagina">
 	<form action="<?php $root;?>AccessProfile.php" name="Form1" id="Form1" method="post"> 
    <input type="hidden" name="comm" id="comm" value="<?php echo $i_Perfil ? 'alter' : 'new'; ?>">
    <input type="hidden" name="i_Perfil" id="i_Perfil" value="<?php echo $i_Perfil; ?>">
    
		<li class="campo3colunas"><label>Perfil</label>       
			<input type="text" name="Descricao_Perfil"  id="Descricao_Perfil" value="<?php echo $Descricao_Perfil; ?>" maxlength="100">
		</li>

		<li class="campo3colunas">
			<label>Situa&ccedil;&atilde;o</label>
			<select name="Situacao_Perfil" id="Situacao_Perfil">
				<option value="">Selecione...</option>
				<?php for ($r=0; $r < count($dados_sit); $r++) { ?>
					<?php 
						if ($Situacao == $dados_sit[$r]['i_Item']) {
							$select = "selected";
						} else { 
							$select = "";
						}  
					?>
					<option value="<?php echo $dados_sit[$r]['i_Item']; ?>" <?php echo $select; ?>><?php echo $dados_sit[$r]['Descricao_Item']; ?></option>
				<?php } ?>
			</select>
		</li>
  	
  	<?php if ($i_Perfil) { ?>
	    <div style="clear:both">&nbsp;</div>
	    <label>Informe as fun&ccedil;&otilde;es que poder&atilde;o ser acessadas por esse perfil:</label>		

			<table style="width:400px">
				<thead>
					<tr>
						<th style="text-align:center; width: 100px;"></th>
						<th style="text-align:center;">Fun&ccedil;&atilde;o</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i < count($dados); $i++) { ?>
						<?php if ($dados[$i]['Possui_Acesso']) {
							$check = "checked";
						} else {
							$check = "";
						} ?>
						<tr>
							<td style="text-align:center;"><input type="checkbox" name="funcoes[]" id="funcoes<?php echo $i; ?>" value="<?php echo $dados[$i]['i_Tela']; ?>" <?php echo $check; ?>></td>
							<td style="text-align:center;"><?php echo $dados[$i]['Descricao_Tela']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
 	</form>

	<div class="barrabotoes">
		<button class="botaoagg" type="button" onClick="javascript:proc(1);">Gravar </button>
		<button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/accessProfile/AccessProfile.php?comm=index';">Voltar</button>
	</div>
</div>