<?php 
	$idNotification = $field->getField(idNotification);
	$idBuyer = $field->getField("idBuyer");
?>

<script type="text/javascript">
	function RecusarImportador() {
  		var entrou = false;
  		var z = 1;

  		for(i=0; i<document.getElementById("totalcheck").value;i++) {
			if (document.getElementById("check"+z).checked) {
	  			entrou = true;
			}

			z++;
  		}

  		if (entrou) {
    			if (confirm("ATENÇÃO! Você que recusar o(s) Comprador(es)?")) {
  	   			document.frmAlterar.comm.value = 'rejeitarChange';
       				//document.frmAlterar.submit();
	   			return true;
    			}
  		} else {
    			verErro('Selecione pelo menos um comprador para ser recusado.');

    			/*
    			//####### ini ####### adicionado por eliel vieira - elumini - em 13/05/2008
    			// referente a demanda 1485 - SAD
    			*/
    			//verifica se deve retirar notificacao da visualizacao

    			ff =  document.getElementById("totalcheck").value;

    			if (ff==0) {
      				if (confirm('Deseja retirar esta notificação da tela de visualização de notificações?')) {
          				document.frmAlterar.comm.value = 'ClientChangeCredit_Ret';
          				//document.frmAlterar.submit();
		  			return true;
      				}else{
		  			return false;  
	  			}
    			}
  		}
	}


	function validaImportador(f) { 
  		var entrou = false;
  		var z = 1;
  
  		for (i=0; i< document.getElementById("totalcheck").value;i++) {
    			if (document.getElementById("check"+z).checked) {
      				entrou = true;
    			}

    			z++;
  		}

  		if(entrou) {
    			document.frmAlterar.comm.value = 'ClientChangeCredit';
			//document.frmAlterar.submit();
			return true;
  		} else {
    			verErro('Selecione pelo menos um comprador para ser validado.');

    			/*
    			//####### ini ####### adicionado por eliel vieira - elumini - em 17/04/2008
    			// referente a demanda 1485 - SAD
    			*/
    			//verifica se deve retirar notificacao da visualizacao

    			ff =  document.getElementById("totalcheck").value;

    			if (ff==0) {
      				if (confirm('Deseja retirar esta notificação da tela de visualização de notificações?')) {
        				document.frmAlterar.comm.value = 'ClientChangeCredit_Ret';
        				//document.frmAlterar.submit();
					return true;
      				}else
	    				return false
    			}else{
	    			return false;	
			}
  		}
	}

</script>
<?php  
    	include_once('../../../navegacao.php'); 
?>

<div class="conteudopagina">
    	<form name="frmAlterar" action="<?php echo  $root;?>role/credit/Credit.php" method="post">
    		<input type="hidden" name="comm">
    		<input type="hidden" name="idBuyer">
    		<input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
    		<input type="hidden" name="idNotification" value="<?php echo  $idNotification;?>">

		<?php 
		$curx = odbc_exec($db,"select b.Sigla from Inform a inner join Moeda b on b.i_Moeda = a.currency where a.id = $idInform");
		 
		$moeda = odbc_result($curx,"Sigla");
		 
		while(odbc_fetch_row($notifyaltercredit)) {
            		$inicio = "";
            		$name           = odbc_result($notifyaltercredit, 1);
            		$contrat        = odbc_result($notifyaltercredit, 2);
            		$data = odbc_result($notifyaltercredit, 3);
            		$inicio = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
            		$data = odbc_result($notifyaltercredit, 4);
            		$fim    = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
            		$situacao = "Segurado";
        
            		if ($inicio == "//") {
              			$situacao = "Prospect";
            		}
          	}
        
        	?>
        	<li class="campo2colunas">
            		<label>Segurado</label>
            		<?php echo $name; ?>
        	</li>

        	<li class="campo2colunas">
            		<label>DPP</label>
            		<?php echo $contrat; ?>
        	</li>

        	<li class="campo2colunas">
            		<label>Situa&ccedil;&atilde;o</label>
            		<?php echo $situacao; ?>
        	</li>

        	<li class="campo2colunas">
            		<label>In&iacute;cio da Vig&ecirc;ncia</label>
            		<?php echo $inicio; ?>
        	</li>

        	<li class="campo2colunas">
            		<label>Fim da Vig&ecirc;ncia</label>
            		<?php echo $fim; ?>
        	</li>

        	<div style="clear:both">&nbsp;</div>
        
        	<table class="tabela01">
        		<!--<table class="tabela01" id="example">-->
              			<!--<thead>
                  			<th colspan="2">
                    			 	&nbsp;
                  			</th>
              			</thead>
              			-->
			<tbody>
				<?php  
			  	$idImporter = 0;
			  
			  	$next = false;
			  	$u = 0;
			  	while($next || odbc_fetch_row($notifyalterImporter)){
			       		$idImporterNew = odbc_result($notifyalterImporter, 7);

					if ($idImporterNew == $idImporter)
					    	continue;
					
					$name                = odbc_result($notifyalterImporter, 1);
					$pais                = odbc_result($notifyalterImporter, 2);
					$c_cof               = odbc_result($notifyalterImporter, 3);
					$credAnt             = odbc_result($notifyalterImporter, 6);
					$credConc            = odbc_result($notifyalterImporter, 5);
					$credAtual           = odbc_result($notifyalterImporter, 4);
					$credAtual           = number_format($credAtual, 2, ",", ".");
					$credConc            = number_format($credConc, 2, ",", ".");
					$idImporter          = $idImporterNew;
					$next = false;
					$credAnt=0;

					if (odbc_fetch_row($notifyalterImporter))
					  	if ($idImporter == odbc_result($notifyalterImporter, 7))
							$credAnt = odbc_result($notifyalterImporter, 6);
					  	else
							$next = true;
				
					$credAnt = number_format($credAnt, 2, ",", ".");
					
					if($u % 2 == 0){
					   	$cor = 'class="odd"';
					}else{
					   	$cor = '';
					}

					$u++;
					?>

					<tr style="background-color:#999">
						<td><div class="formopcao">
                                               		<input type="checkbox" name="check[]" id="check<?php echo $u;?>"  value="<?php echo $idImporter;?>">
                                            	</div>
                                        	</td>

						<td><label style="color:#FFF">Comprador: (<?php echo $name?>)</label></td>
					</tr>

                                     	<tr <?php echo $cor;?>>
                                        	<td>&nbsp;</td>
                                        	<td><strong>Pa&iacute;s/CRS: </strong><?php echo $pais.'/'.$c_cof;?><br>					 
                                            		<strong>Cr&eacute;dito Solicitado (Anterior): </strong><?php echo $moeda;?>&nbsp;<?php echo $credAnt;?> <br>
                                            		<strong>Cr&eacute;dito Concedido: </strong><?php echo $moeda;?>&nbsp;<?php echo  $credConc;?><br>
                                            		<strong>Cr&eacute;dito Solicitado (Atual): </strong><?php echo $moeda;?>&nbsp;<?php echo $credAtual;?>
                                        	</td>
                                     	</tr>
              				<?php 
			 	}
			
			 	if($ren_idInform>0 And $ren_status != 9 And odbc_fetch_row($ren_notifyalterImporter) ) {  ?>
                  			<p>Informe de renovação afetado:</p>

             				<?php  while(odbc_fetch_row($ren_notifyaltercredit)) {
                          			$inicio = "";
                          			$name           = odbc_result($ren_notifyaltercredit, 1);
                          			$contrat        = odbc_result($ren_notifyaltercredit, 2);
                          			$data 		= odbc_result($ren_notifyaltercredit, 3);
                          			$inicio 	= substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
                          			$data 		= odbc_result($ren_notifyaltercredit, 4);
                          			$fim    	= substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
                          			$situacao 	= "Segurado";
                    
                          			if ($inicio == "//") {
                            				$situacao = "Prospect";
                          			}
                     			}  

					print "<tr><td colspan=2>Segurado: $name</td></tr>\n".
						"<tr><td colspan=2>DPP: $contrat</td></tr>\n".
						"<tr><td colspan=2>Situa&ccedil;&atilde;o: $situacao</td></tr>\n".
						"<tr><td colspan=2>In&iacute;cio da Vig&ecirc;ncia: $inicio</td></tr>\n".
						"<tr><td colspan=2>Fim da Vig&ecirc;ncia: $fim </td></TR>\n".
						"<input type=hidden name=ren_idInform value=$ren_idInform";
         				?>

		 			<?php  
					$idImporter = 0;
					$next = false;
					while($next || odbc_fetch_row($ren_notifyalterImporter)){
						$idImporterNew = odbc_result($ren_notifyalterImporter, 7);

					  	if ($idImporterNew == $idImporter) continue;

					  	$name                = odbc_result($ren_notifyalterImporter, 1);
					  	$pais                = odbc_result($ren_notifyalterImporter, 2);
					  	$c_cof               = odbc_result($ren_notifyalterImporter, 3);
					  	$credAnt             = odbc_result($ren_notifyalterImporter, 6);
					  	$credConc            = odbc_result($ren_notifyalterImporter, 5);
					  	$credAtual           = odbc_result($ren_notifyalterImporter, 4);
					  	$credAtual           = number_format($credAtual, 2, ",", ".");
					  	$credConc            = number_format($credConc, 2, ",", ".");
					  	$idImporter          = $idImporterNew;
					  	$next = false;
					  	$credAnt=0;

					  	if (odbc_fetch_row($ren_notifyalterImporter))
							if ($idImporter == odbc_result($ren_notifyalterImporter, 7))
					  			$credAnt = odbc_result($ren_notifyalterImporter, 6);
							else
					  			$next = true;
				
					  	$credAnt = number_format($credAnt, 2, ",", ".");
					  
					  	if($u % 2 == 0)
							$cor = 'class="odd"';
					  	else
						 	$cor = '';
						 
					  	$u++;
					  	?>
						<tr style="background-color:#999">
							<td><div class="formopcao">
								<input type="checkbox" name="check[]" id="check<?php echo $u;?>"  value="<?php echo $idImporter;?>">
							</div>
							</td>

							<td><label style="color:#FFF">Comprador: (<?php echo $name?>)</label></td>
						</tr>
					
						<tr <?php echo $cor;?>>
							<td>&nbsp;</td>
							<td><strong>Pa&iacute;s/CRS: </strong><?php echo $pais.'/'.$c_cof;?><br>					 
                                    				<strong>Cr&eacute;dito Solicitado (Anterior): </strong><?php echo $moeda;?>&nbsp;<?php echo $credAnt;?> <br>
                                    				<strong>Cr&eacute;dito Concedido: </strong><?php echo $moeda;?>&nbsp;<?php echo  $credConc;?><br>
                                    				<strong>Cr&eacute;dito Solicitado (Atual): </strong><?php echo $moeda;?>&nbsp;<?php echo $credAtual;?> </td>
						</tr>
              					<?php            					
					}
            			}
        
        ?>
     <?php  if ($u == 0  && $idImporter == 0){  ?>
                <thead>
                  <tr>
                    <th>&nbsp;</th>
                    <th>Comprador</th>
                 </tr>
                </thead>
	<?php   }else{  
	             if($idBuyer){
			  ?>    
                <tr>
                    <td>
                            <?php  $strSQL = "select id, comment, hide from ImpComment where idImporter=$idBuyer";
                                   $cur = odbc_exec($db, $strSQL);       
                                 
          
                                   if (odbc_fetch_row($cur)) {   ?>
                                    <a href="<?php  echo $root.'role/credit/Credit.php?comm=obs&idBuyer='.
                                    $idBuyer. '&idInform='. $field->getField("idInform").
                                    '&idNotification='. $field->getField("idNotification"). '&origem=2'
                                    ?>&full=0">(Existem observa&ccedil;&otilde;es cadastradas)</a>
                            <?php  }
                            ?>
                    </td>
                    <td>&nbsp;</td>
                  </tr>               
         <?php
			      }
			  }  
			  
			  
			  ?>
              </tbody>
              <tfoot>
                  <tr>
                      <th colspan="2">Itens encotrados:  <?php echo $u;?></th>
                      
                  </tr>
             </tfoot>
        </table>
        
        <div class="barrabotoes">
        	<input type="hidden" name="totalcheck"  id="totalcheck" value="<?php echo $u;?>">
            <a href="../access/Access.php"><button  class="botaovgm">Voltar</button></a>
            <button type="button" onClick="if(RecusarImportador(this.frmAlterar)) this.form.submit();" class="botaovgg">Recusar Comprador</button>
            <button type="button" onClick="if(validaImportador(<?php echo $u;?>)) this.form.submit();" class="botaoagm">OK</button>
        </div>
    </form>

</div>
