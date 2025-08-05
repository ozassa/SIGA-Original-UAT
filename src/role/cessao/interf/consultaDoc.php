<?php

  $qry = "SELECT policyKey, name, id, segundaVia, i_Produto, dateEmissionP 
  				FROM Inform 
  				WHERE id = ".$idInform." 
  				ORDER BY startValidity DESC";
  $cur = odbc_exec($db,$qry); 

  require_once("../../../navegacao.php"); ?>

<div class="conteudopagina" >
<?php
	$i = 0;

  $pKey = odbc_result($cur, 1);
  $empresa = (odbc_result($cur, 2));
  $idInform = odbc_result($cur, 3);
  $pKeyVia = odbc_result($cur, 4);
  $i_Produto  = odbc_result($cur, 5);
  $DataEmissao = odbc_result($cur, 6);

  if ($pKey != "") {
  	$propFile = $pdfDir.$pKeyVia."Prop.pdf";
    $policyFile = $pdfDir.$pKey."Apolice.pdf";
    $condPartFile = $pdfDir.$pKey."CondPart.pdf";
    $condJurosFile = $pdfDir.$pKey.'CondJuros.pdf';
    $parc1File = $pdfDir.$pKey."Parcela1.pdf"; ?>

    <?php if (file_exists($propFile)) { 
    	$i++; ?>
			<li class="campo2colunas" style="height:30px">
				<label>
					<a href="<?php echo $root.'download/'.$pKeyVia.'Prop.pdf'; ?>" target=_blank>Proposta</a>
				</label>
			</li>  
			<div style="clear:both; height:1px;">&nbsp;</div> 
    <?php } ?>
    
    <?php if (file_exists($policyFile)) { 
    	$i++; ?>
			<li class="campo2colunas" style="height:30px">
				<label>
					<a class="textoBold" href="<?php echo $root.'download/'.$pKey.'Apolice.pdf'; ?>" target=_blank>Ap&oacute;lice</a>
				</label>
			</li>
			<div style="clear:both; height:1px;">&nbsp;</div> 
		<?php } ?>
		
		<?php if (file_exists($policyFile)) { ?>
	    <?php if ($i_Produto != 1) { 
	    	$i++; ?>					
				<li class="campo2colunas" style="height:30px">
					<label>
						<a class="textoBold" href="<?php echo $root.'download/'.$pKey.'ApoliceReal.pdf'; ?>" target=_blank>Ap&oacute;lice em Reais</a>
					</label>
				</li>  
				<div style="clear:both; height:1px;">&nbsp;</div> 
	    <?php } ?>
	  <?php } ?>
		
		<?php if (file_exists($condPartFile)) { 
			$i++; ?>
			<li class="campo2colunas" style="height:30px">
				<label>
					<a class="textoBold" href="<?php echo $root.'download/'.$pKey.'CondPart.pdf'; ?>" target=_blank>Condi&ccedil;&otilde;es Particulares</a>
				</label>
			</li>
			<div style="clear:both; height:1px;">&nbsp;</div> 
		<?php } ?>

    <?php if (file_exists($condJurosFile)) {
    	$i++; ?>
			<li class="campo2colunas" style="height:30px">
				<label>
					<a class="textoBold" href="<?php echo $root.'download/'.$pKey.'CondJuros.pdf';?>" target=_blank>Condi&ccedil;&otilde;es Especiais Juros de Mora</a>
				</label>
			</li>
			<div style="clear:both; height:1px;">&nbsp;</div> 
    <?php } ?>

    <?php 
  	if (file_exists($parc1File)) {
  		$i++;
    	if ($i_Produto != 1){ 
    		$p = 2; 

    		if (!file_exists($parc1File)) { ?>
					<li class="campo2colunas" style="height:30px">
						<label>
							<h2>Parcelas:</h2>
						</label>
					</li>  

					<div style="clear:both; height:1px;">&nbsp;</div> 
					<li class="campo2colunas" style="height:30px">
						<label>
							<a class="textoBold" href="<?php echo $root.'download/'.$pKeyVia. 'Parcela'. ".pdf"; ?>" target=_blank>Parcela 1</a>
						</label>
					</li>

					<div style="clear:both; height:1px;">&nbsp;</div> 
      	<?php 
      	} else { 
      		$p = 1; 
      	} 

      	while (file_exists($pdfDir. $pKey. "Parcela". $p. ".pdf")) {  ?>
					<li class="campo2colunas" style="height:30px">
						<label>
							<a class="textoBold" href="<?php echo $root.'download/'.$pKey. 'Parcela'. $p. ".pdf"; ?>" target=_blank>Parcela <?php echo $p; ?></a>
						</label>
					</li>

					<div style="clear:both; height:1px;">&nbsp;</div> 
        <?php           
          $p++;
        }
      }
    }
  } ?>

  <?php if ($i == 0) { ?>
  	<label>Nenhum documento foi encontrado</label>
  <?php } ?>

	<div style="clear:both">&nbsp;</div> 
  <form action="<?php echo $root;?>role/cessao/Cessao.php?comm=consultaCessao" name="form" method="post">
		<input type="hidden" name="comm" value="consultaCessaoExp">
		<input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
		<input type="hidden" name="name" value="<?php echo $empresa; ?>">
		<li class="campo2colunas">
			<button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/cessao/Cessao.php?comm=consultaCessao';">Voltar</button>
		</li>
  </form>

	<div style="clear:both">&nbsp;</div> 
</div>
