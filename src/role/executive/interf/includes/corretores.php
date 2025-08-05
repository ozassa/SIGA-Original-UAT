<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">   
	<label><h2>Cadastro de Corretores</h2></label>
</li>

<li class="campo1colunas" style="list-style:none;margin-top:10px;display:table;">


	<label>Para adicionar novo corretor preencha os campos abaixo e pressione o bot&atilde;o "Adicionar corretor" *</label>
</li>

<li class="campo3colunas">
	<label>Corretor</label>
	<?php
	
	$cSql = "select idconsultor, SUBSTRING(razao, 1, 80) as razao From consultor Where ativo = 1 Order by razao";
	$cur8 = odbc_exec($db,$cSql);
//print $cSql;		
	?>			
	<select name="Corretor1" id="Corretor1">
		<option value="">Selecione..</option>
		<?php
		$corr .= '<option value="">Selecione o corretor</option>';
		while (odbc_fetch_row($cur8)){  
			echo '<option value="'.odbc_result($cur8, 'idconsultor').'">'.(odbc_result($cur8, 'razao')).'</option>';

		}
		?>		  
	</select>           
</li>
<li class="campo3colunas" style="width:150px;">
	<label>Grupo Corretor</label>
	<select name="i_Grupo" id="i_Grupo" style="width:150px;">
		<option value="">Selecione..</option>
		<?php
		$cSql = "select * from Grupo_Corretor where Situacao = 0 order by Descricao";
		$cur9 = odbc_exec($db,$cSql);

		while (odbc_fetch_row($cur9)){  
			echo '<option value="'.odbc_result($cur9, 'i_Grupo').'">'.(odbc_result($cur9, 'Descricao')).'</option>';

		}
		?>		  
	</select>           
</li>
<li class="campo3colunas" >
	<label>Comiss&atilde;o %
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		Participa&ccedil;&atilde;o %</label>
		<input type="text" name="Comissao" maxlength="6" id="Comissao" value="0,00" onKeypress="return numeros();" style="text-align: right; width:110px;" onBlur="return limitarvalorCOM('Comissao');"/>&nbsp;&nbsp;&nbsp;

		<input type="text" name="Participacao" id="Participacao" maxlength="6"  value="0,00" onKeypress="return numeros();" style="text-align: right; width:110px;" onBlur="return limitarvalor(this);"/>
	</li>


	<li class="campo3colunas" style="width:130px;">
		<label>Principal S/N</label>                 
		<div class="formopcao">
			<input name="CorretorPrincipal" id="CorretorPrincipal" type="radio" value="1" onClick=""/>
		</div>
		<div class="formdescricao"><span>Sim</span></div>
		<div class="formopcao">
			<input name="CorretorPrincipal" id="CorretorPrincipal" type="radio" value="0"  checked onClick=""/>
		</div>
		<div class="formdescricao"><span>N&atilde;o</span></div>
	</li>


	<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">  

		<button class="botaoagg" type="button"  onClick="javascript: if(validadoFormCorretor()) { loadHTML('<?php echo $root.'role/executive/interf/Adiciona_corretor.php'; ?>','Retorno','<?php if($_SESSION['browser'] == 'IE') echo 'GET'; else echo 'POST'; ?>','','<?php echo $idInform;?>',document.getElementById('Operacao').value,'Corretor'); }return false;">Adicionar Corretor</button>             
	</li>


	<div id="Retorno">

		<input type="hidden" name="temCorretorPrincipal" id="temCorretorPrincipal" value="<?php echo $temCorretorPrincipal;?>">
		<input type="hidden" name="ParticipacaoCorr"     id="ParticipacaoCorr"     value="<?php echo $ParticipacaoCorr;?>">

		<table summary="Submitted table designs">
			<thead>
				<th scope="col">Corretor</th>
				<th scope="col">Grupo Corretor</th>
				<th scope="col">Comiss&atilde;o %</th>
				<th scope="col">Participa&ccedil;&atilde;o %</th>
				<th scope="col">Principal S/N?</th>
				<th scope="col" colspan="2">Op&ccedil;&otilde;es</th>

			</thead>
			<tbody> 
				<?php

// Consulta os corretores 
				$cqry = "select a.idInform, a.idCorretor, SUBSTRING(b.razao,1,40)as Razao, b.razao as RazaoSocial, a.p_Comissao,
				a.p_Corretor,a.CorretorPrincipal, a.i_Grupo, c.Descricao 
				from InformCorretor a
				inner join consultor b on a.idCorretor = b.idconsultor
				left join Grupo_Corretor c on c.i_Grupo = a.i_Grupo
				where a.idInform = '".$idInform."'";
				$resp = odbc_exec($db,$cqry);

				$TotalParticipacao = 0;
				while (odbc_fetch_row($resp)){
					$CorretorPrincipal = odbc_result($resp, 'CorretorPrincipal');
					$Razao             = (odbc_result($resp, 'Razao'));
					$Grupo_Corretor    = (odbc_result($resp, 'i_Grupo'));
					$Grupo_Descricao    = (odbc_result($resp, 'Descricao'));
					$idConsultor       = odbc_result($resp, 'idCorretor');
					$p_Comissao        = number_format(odbc_result($resp, 'p_Comissao'),2,',','.') ;
					$Participacao      = number_format(odbc_result($resp, 'p_Corretor'),2,',','.');
					$TotalParticipacao += odbc_result($resp, 'p_Corretor'); 
//print '?'. $TotalParticipacao;

					if($CorretorPrincipal == 1){
						$sim  = 'Sim';
					}else{
						$sim = 'N&atilde;o';
					}
					?> 
					<tr id="lastRow_corr">
						<td><a href="#" title="<?php echo (odbc_result($resp, 'RazaoSocial'));?>" alt="<?php echo (odbc_result($resp, 'RazaoSocial'));?>"><?php echo $Razao;?></a></td>
						<td><?php echo $Grupo_Descricao;?></td>
						<td style="text-align: right;"><?php echo ($p_Comissao?$p_Comissao:'0,00'); ?></td>
						<td style="text-align: right;"><?php echo ($Participacao?$Participacao:'0,00');?></td>
						<td><?php echo $sim; ?></td>
						<td><a href="#" onClick="edita_Form('<?php echo $idConsultor;?>','<?php echo ($p_Comissao?$p_Comissao:'0,00'); ?>','<?php echo ($Participacao?$Participacao:'0,00');?>',<?php echo $CorretorPrincipal; ?>,'<?php echo $Grupo_Corretor;?>');  return false;"><img src="<?php echo $root;?>images/icone_editar.png" title="Editar Registro" width="24" height="24" class="iconetabela" /></a></td>
						<td><a href="#" onClick="javascript: loadHTML('<?php echo $root.'role/executive/interf/Adiciona_corretor.php'; ?>','Retorno','<?php if($_SESSION['browser'] == 'IE') echo 'GET'; else echo 'POST'; ?>','<?php echo  $idConsultor;?>','<?php echo $idInform;?>','Remover','Corretor'); return false;"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a></td>
					</tr>

<?php  } ?><!--
<tr>
<td colspan="2">&nbsp;</td>
<td colspan="1" style="text-align:right"><?php echo number_format($TotalParticipacao,2,',','.'); ?></td>
<td colspan="3">&nbsp;</td>
</tr>
-->
</tbody>
</table>
<input type="hidden" name="TotalParticipacao" id="TotalParticipacao" value="<?php echo number_format($TotalParticipacao,2,',','.'); ?>">
</div>