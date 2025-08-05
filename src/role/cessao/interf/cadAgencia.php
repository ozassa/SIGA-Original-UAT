<?php require_once("../../../navegacao.php");?>
<?php $idNurim 	= isset($_POST['idNurim']) ? $_POST['idNurim'] : false; ?>

<script type="text/javascript">
	<?php 
		if(isset($msgAg) && $msgAg != ''){ ?>
			alert("<?php echo htmlspecialchars($msgAg, ENT_QUOTES, 'UTF-8'); ?>");
		<?php }
	?>
</script>

<form action="<?php echo $root;?>role/cessao/Cessao.php" method="post" name="cadastro" onsubmit="return checa_formulario(this)">	
	<div class="conteudopagina">
		<table summary="Submitted table designs" id="example">
			<caption>Agências Cadastradas</caption>
			<thead>
				<tr>
					<th scope="col">&nbsp;</th>
	                <th scope="col">Banco</th>
	                <th scope="col">Cod. Agência</th>
					<th scope="col">Agência</th>
					<th scope="col">GECEX</th>
				</tr>	
			</thead>
			
			<tbody>
				<?php
					$query = "Select A.id As idAgencia, B.name As Banco, A.codigo As Codigo, A.name As Agencia, N.name As Regiao
						From Agencia A 
						Inner Join Banco B On B.id = A.idBanco 
						left Join Nurim N On N.id = A.idNurim 
						Order By A.codigo";
								
	                $cur = odbc_exec($db, $query);
	                $i = 0;
					While (odbc_fetch_row($cur)) {
						$i++;
						
						$IdAgencia = odbc_result($cur, "idAgencia");
						$Banco = odbc_result($cur, "Banco");
						$Codigo = odbc_result($cur, "Codigo");
						$Agencia = odbc_result($cur, "Agencia");		
						$Regiao = odbc_result($cur, "Regiao");		
	                	?>
	                
						<tr>
	                    	<td><?php echo htmlspecialchars($IdAgencia, ENT_QUOTES, 'UTF-8'); ?></td>
	                    	<td><?php echo htmlspecialchars($Banco, ENT_QUOTES, 'UTF-8'); ?></td>
	                    	<td><?php echo htmlspecialchars($Codigo, ENT_QUOTES, 'UTF-8'); ?></td>
	                    	<td><?php echo htmlspecialchars($Agencia, ENT_QUOTES, 'UTF-8'); ?></td>
	                    	<td><?php echo htmlspecialchars($Regiao, ENT_QUOTES, 'UTF-8'); ?></td>
	                 	</tr>
	                <?php  }
	                
	                If ($i == 0) {
	                	?>
	                  	<tr>
	                    	<td colspan=4><?php echo "Nenhuma agências cadastrada"; ?></td>
	                  	</tr>
	                <?php  }?>			
	                
			</tbody>
		</table>
	</div>

	<div class="conteudopagina">
  		<li class="campo2colunas"><label>Banco</label>
    		<?php  // Monta a lista de Bancos
			$sql = "SELECT id, name, tipo FROM Banco WHERE tipo In (1, 2) And IsNull(s_Banco, 0) = 0 Order By tipo, name";
			
			$sel = $idBanco;
    		$tipoBanco = '';
			$name = "idBanco";
			$acao = "onChange=seleciona(this)";
			$empty = "Selecione um Banco";
			$disabled = false;
			require_once("../../interf/Select.php"); ?>
  		</li>
  		
      	<li class="campo2colunas"><label>C&oacute;digo da Ag&ecirc;ncia (sem dv)</label>
       		<input type="text" name="agencia">
      	</li>
      	
      	<li class="campo2colunas"><label>Nome</label>
       		<input type="text" name="agNome">
      	</li>
      	
      	<li class="campo2colunas">       
			<label>GECEX</label>           
			<select name="idNurim" id="idNurim" onChange="">
				<?php  
					$cSql = "select id, name From Nurim Order by name";

					$cur2 = odbc_exec($db,$cSql);
					echo '<option value="">Selecione a GECEX</option>';
					
					While (odbc_fetch_row($cur2)){ 
						?>
						<option value="<?php echo htmlspecialchars(odbc_result($cur2, 'id'), ENT_QUOTES, 'UTF-8'); ?>" <?php if (odbc_result($cur2, 'id') == $idNurim) echo "selected";?>><?php echo htmlspecialchars(odbc_result($cur2, 'name'), ENT_QUOTES, 'UTF-8'); ?></option>
					<?php  } ?>
			</select>
		</li>
      	
      	<li class="campo2colunas"><label>Telefone</label>
       		<input type="text" name="agTel">
      	</li>
      	
      	<li class="campo2colunas"><label>Endere&ccedil;o</label>
       		<input type="text" name="agEnd">
      	</li>
      	
      	<li class="campo2colunas"><label>Cidade</label>
       		<input type="text" name="agCid">
      	</li>
      	
      	<li class="campo2colunas"><label>Regi&atilde;o</label>
	       	<?php  // Monta a lista de UF
				$sql = "SELECT uf, name FROM UF WHERE uf <> 'NA' AND name IS NOT NULL ORDER BY name";
				$sel = '';
				$name = "uf";
				$acao = '';
				$empty = "Selecione uma Regi&atilde;o";
				require_once("../../interf/SelectReg.php");
		  	?>
      	</li>
      	
      	<li class="campo2colunas"><label>CNPJ</label>
       		<input type="text" name="cnpj" onBlur="valida_cadastro()">
      	</li>
      	
      	<li class="campo2colunas"><label>IE</label>
       		<input type="text" name="agIE">
      	</li>
      	
      	<li class="campo2colunas"><label>Nome do Contato</label>
       		<input type="text" name="agContato">
      	</li>
      	
      	<li class="campo2colunas"><label>Telefone do Contato</label>
       		<input type="text" name="agTelefone">
      	</li>
      	
      	<li class="campo2colunas"><label>E-mail do Contato</label>
       		<input type="text" name="agEmail">
      	</li>
      	
      	<input type="hidden" name="comm" value="cadAg">
	  	<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');?>">
	  	
      	<li class="barrabotoes" style="list-style:none;*margin-left:-15px">
      		<button name="Cadastrar" type="submit" class="botaoagm" onClick="this.form.comm.value='cadAgSQL';this.form.submit()">Cadastrar</button>
      	</li>
	</div>
</form>
