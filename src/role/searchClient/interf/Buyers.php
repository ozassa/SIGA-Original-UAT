<script>
	function gerarPdf(f){
		f.comm.value = "pdf";
		f.submit();
	}
</script>

<?php 
  	//Alterado por Tiago V N - Elumini - 10/04/2006
  	$cur=odbc_exec($db,"SELECT * FROM Inform WHERE id = ".$field->getField("idInform"));

  	if (odbc_result($cur, 'currency') == 2) {
     		$ext = "US$";
  	}else if (odbc_result($cur, 'currency') == 6) {
     		$ext = "€";
	}else{
		$ext = "R$";
  	}
?>

<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
	<script language="JavaScript" src="<?php echo $root;?>scripts/utils.js"></script>

	<form action="<?php echo $root;?>role/searchClient/ListClient.php" method="post">
   		<label>Aten&ccedil;&atilde;o: O limite de cr&eacute;dito &eacute; rotativo.  Deve ser calculado para cobrir a 
   		exposi&ccedil;&atilde;o m&aacute;xima do segurado em rela&ccedil;&atilde;o a cada comprador.  
   		Depende do valor e da frequ&ecirc;ncia dos faturamentos, assim como do prazo de pagamento.  
   		Para cadastrar a Raz&atilde;o Social do comprador, n&atilde;o utilize ap&oacute;strofo ( ' ).  
   		<br>Em caso de d&uacute;vida, entre em contato conosco: (11) 5501-8181<br></label>
   
		<div style="clear:both">&nbsp;</div>

		<table id="example" class="tabela01">
  			<thead>
      				<tr>
        				<th colspan="7" scope="col">Compradores Inclu&iacute;dos</th>
      				</tr>
  			</thead>

  			<tbody>
				<?php 
  				$cur = odbc_exec($db,
		   			"SELECT imp.name, address, risk, city, c.name, tel, prevExp12, limCredit, numShip12, periodicity, ".
		   			"przPag, imp.id, imp.hold, cep, fax, contact, relation, seasonal FROM Importer imp JOIN Country c ON ".
		   			"(idCountry = c.id) WHERE idInform = $idInform AND state <> 7 AND state <> 15 AND state <> 8 and ".
		   			"state <> 9 and imp.id not in (select distinct idImporter from ImporterRem) ORDER BY imp.id");

  				$i = 1;
  				$count = 0;

  				while (odbc_fetch_row($cur)) {
					if($i % 2 == 0){
	    					$cor = 'class="odd"';
					}else{
	    					$cor = '';
					}

					?>
  
  					<tr <?php echo $cor?>>
       						<td width="7%"><?php echo $i; ?>
    							<?php  if (($role['executive'] || $role['executiveLow']) && odbc_result ($cur, 'hold')) { $count ++; ?>
        							<input type="hidden" name="buyId<?php echo $count; ?>" value="<?php echo odbc_result ($cur, 'id'); ?>">
          							<div class="formopcao">
              								<input type="checkbox" name="free<?php echo $count; ?>" value ="1" <?php echo odbc_result($cur,'hold') == 0 ? ' checked' : ''; ?>>
          							</div>
								<?php 
	     						} ?></td>

       						<td colspan="6">
       							<li class="campo3colunastable"><label>Raz&atilde;o Social:</label><?php echo odbc_result($cur,1); ?></li>
       							<li class="campo3colunastable"><label>Endere&ccedil;o:</label><?php echo odbc_result($cur,2); ?></li>
       							<li class="campo3colunastable"><label>Cidade:</label><?php echo odbc_result($cur,4); ?></li>
       							<li class="campo3colunastable"><label>CEP:</label><?php echo odbc_result($cur,14); ?></li>
       							<li class="campo3colunastable"><label>Pa&iacute;s:</label><?php echo odbc_result($cur,5);?></li>
       							<li class="campo3colunastable"><label>Telefone:</label><?php echo odbc_result($cur,6); ?></li>
       							<li class="campo3colunastable"><label>Fax:</label><?php echo odbc_result($cur,15); ?></li>
       							<li class="campo3colunastable"><label>Contato:</label><?php echo odbc_result($cur,16); ?></li>
       							<li class="campo3colunastable"><label>Vendas Sazonais:</label><?php echo odbc_result($cur,18) ? "Sim" : "N&atilde;o"; ?></li>
       							<li class="campo3colunastable"><label>Rela&ccedil;&atilde;o Comercial:</label>desde o ano de <?php echo odbc_result($cur,17); ?></li>
       							<li class="campo3colunastable"><label>Volume</label> <?php echo $ext?>:<?php echo number_format(odbc_result($cur,7),2,",","."); ?></li>
       							<li class="campo3colunastable"><label>N.&ordm; Emb./Ano:</label><?php echo odbc_result($cur,9); ?></li>
       							<li class="campo3colunastable"><label>Exp. M&aacute;x.</label> <?php echo $ext?> (Mil): <?php  if (odbc_result ($cur, 'hold')) { ?>
           							<input name="limCredit<?php echo $count; ?>" value="<?php echo odbc_result($cur,8)/1000; ?>" onBlur="checkDecimalsMil(this, this.value)" size=5><?php  } else {?><?php echo odbc_result($cur,8)/1000; ?><?php  } ?></li>
       							<li class="campo3colunastable"><label>Per/Emb(dias):</label><?php echo odbc_result($cur,10); ?></li>
       							<li class="campo3colunastable"><label>Prz./Pag.(dias):</label><?php echo odbc_result($cur,11); ?></li>
      						</td>
   					</tr>
   
					<?php 
     					$i++;
  				}
			?></tbody>
		</TABLE>

		<?php  if ($msg != "") {?>
              		<p><font color="red"><?php echo $msg; ?></font></p>
		<?php  } ?>

    		<input type="hidden" name="idInform" value="<?php echo $field->getField("idInform"); ?>">
    		<input type="hidden" name="reltipo" value="informIII">
    		<input type="hidden" name="comm" value="buySubmit">
    
    		<div class="barrabotoes">
        		<button  type="button" class="botaoagm" onClick="this.form.comm.value='open'; this.form.submit()">Voltar</button>
       		 	<button  type="button" class="botaoagm" onClick="this.form.submit();">OK</button> 
        		<button  type="button" class="botaoagm" onclick="gerarPdf(this.form)" name="pdf" id="pdf">Vers&atilde;o PDF</button>
    		</div>
	</form>
</div> 