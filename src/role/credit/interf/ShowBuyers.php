<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
	<form action="<?php echo  $root;?>role/credit/Credit.php" method="post" style="width:950px;">
		<?php  
    		$origem = $field->getField("origem");
    		$ret = "";
    		$idBuyer = $field->getField("idBuyer");
    
    		if ($origem==4) {
      			$ret = "clientChangeImporterInsert";
    		} else if ($origem == 3){
      			$ret="PendencyCoface";
    		}else if ($origem == 2){
      			$ret="view";
    		}else{
      			$ret="import";
    		}
    		?>
     
		<?php  if(odbc_fetch_row($head)){
                 	$nameInform= odbc_result($head, 1);
                 	$ciInform 	= odbc_result($head, 2);
                 	$idInform	= odbc_result($head, 3);
                 	$nMoeda = odbc_result($head, 4);

               		if ($nMoeda == "1") {
                 		$extMoeda = "R\$";
               		}else if ($nMoeda == "2") {
                 		$extMoeda = "US\$";
               		}else if($nMoeda == "6"){
                 		$extMoeda = "&euro;";
               		}else if ($nMoeda == "0"){
                 		$extMoeda = "US\$";
               		}
        
    			?>
			<script language="javascript">
				function validaInteiroPositivo(fieldName, fieldValue){
					if (isNaN(parseInt(fieldValue))) {
						verErro("Este não é um número válido.");
						fieldName.value='1';
						fieldName.focus();
					}else {
						if (parseInt(fieldValue) < 1) {
							verErro("Este não é um número válido.");
							fieldName.value='1';
							fieldName.focus();
						}

						fieldName.value=parseInt(fieldValue);
					}
				}
			</script>

    			<ul>
       				<li class="campo3colunas">
       					<label>Segurado:</label>
       					<?php echo $nameInform;?>
       				</li>

       				<li class="campo3colunas">
       					<label>DPP:</label>
       					<?php echo $ciInform;?> 
       				</li>
    			</ul>   
		<?php  } ?>
  
		<?php  if($msg){
             		echo "<script>verErro('$msg')</script>";
           	}
    		?>

     		<div style="clear:both">&nbsp;</div>

		<?php  $i = 0;

		if($field->getField("idBuyer")){
			$idBuyer = $field->getField("idBuyer");
		}

		$query  = "SELECT Importer.name, Importer.address, Importer.risk, 
			Importer.city, Country.name, Importer.tel, 
			Importer.limCredit, Importer.numShip12, Importer.periodicity,
			Importer.przPag, Importer.idCountry, Importer.c_Coface_Imp, 
			Importer.prevExp12,Importer.fax, Importer.cep, 
			Importer.contact, Importer.divulgaNome, Importer.emailContato,
			Importer.cnpj, Importer.relation, Importer.Easy_Number,Importer.state 
			FROM Importer
			Inner Join Country On
			Country.id = Importer.idCountry
			WHERE Importer.id = $idBuyer";

		$cur = odbc_exec($db,$query);

		while(odbc_fetch_row($cur)) {
			$nameBuyer_original		= odbc_result($cur, 1);
			$nameBuyer         		= odbc_result($cur, 1);
			$addressBuyer      		= odbc_result($cur, 2);
			$addressBuyer_original		= odbc_result($cur, 2);
			$riskBuyer         		= odbc_result($cur, 3);
			$cityBuyer         		= odbc_result($cur, 4);
			$cityBuyer_original		= odbc_result($cur, 4);
			$countryBuyer      		= odbc_result($cur, 5);
			$telBuyer          		= odbc_result($cur, 6);
			$limCreditBuyer    		= odbc_result($cur, 7);
			$numShip12Buyer    		= odbc_result($cur, 8);
			$periodicityBuyer  		= odbc_result($cur, 9);
			$przPagBuyer       		= odbc_result($cur, 10);
			$przPagBuyer_original 		= odbc_result($cur, 10);
			$idCountryBuyer    		= odbc_result($cur, 11);
			$idCountryBuyer_original    	= odbc_result($cur, 11);
			$c_Coface_Imp      		= odbc_result($cur, 12);
			$prevExp12	       		= odbc_result($cur, 13);
			$faxBuyer	       		= odbc_result($cur, 14);
			$cepBuyer	       		= odbc_result($cur, 15);
			$cepBuyer_original 		= odbc_result($cur, 15);
			$contactBuyer      		= odbc_result($cur, 16);
			$divulgaNome       		= odbc_result($cur, 17);
			$email             		= odbc_result($cur, 18);
			$cnpj              		= odbc_result($cur, "cnpj");
			$relation           	= odbc_result($cur, "relation");
			$limCreditBuyer    		= number_format($limCreditBuyer, 2, ",", ".");
			$prevExp12         		= number_format($prevExp12, 2, ",", ".");
			$EasyNumber            	= odbc_result($cur, "Easy_Number");
			$state            		= odbc_result($cur, "state");

			$i++;
					
			if ($riskBuyer == 1)
				$riskBuyer = "RC - Risco Comercial";
			else if($riskBuyer == 2)
				$riskBuyer = "RP - Risco Político";
			else $riskBuyer = "RP & RC - Risco Político & Risco Comercial";
			?>

			<ul>
				<li class="campo3colunas">
					<label>Raz&atilde;o Social</label>
					<input type="hidden" name="nameBuyer_original" value="<?php echo $nameBuyer_original;?>">
					<input type="text"   name="nameBuyer"          value="<?php echo  $nameBuyer;?>">
				</li>

                    		<li class="campo3colunas">
                        		<label>C&oacute;digo</label>
                    			<?php  if ($origem != 2 && $origem != 4) { ?>
						<input type="text" style="" name="c_Coface_Imp" value="<?php echo  $c_Coface_Imp;?>">
					<?php  } else { echo $c_Coface_Imp;?> 
						<input type="hidden" name="c_Coface_Imp" value="<?php echo  $c_Coface_Imp;?>"> 
					<?php  } ?>
				</li>

				<li class="campo3colunas">
					<label>Easy Number</label>
					<input type="text" maxlength="14" id="easynumber" name="easynumber" value="<?php echo  $EasyNumber;?>">
				</li>

                    		<li class="campo3colunas">
                        		<label>Registro fiscal do importador</label>
                        		<input type="text" id="cnpj" name="cnpj" value="<?php echo  $cnpj;?>">
                    		</li>

                    		<li class="campo3colunas">
                        		<label>Tel</label>
                        		<input type="text" class="caixa" name="telBuyer" value="<?php echo $telBuyer;?>">
                    		</li>

                    		<li class="campo3colunas">
                        		<label>Fax</label>
                        		<input type="text" id="faxBuyer" name="faxBuyer" value="<?php echo  $faxBuyer;?>">
                    		</li>

                    		<li class="campo3colunas">
                        		<label>Endere&ccedil;o</label>
                        		<input type="hidden" name="addressBuyer_original" value="<?php echo str_replace('"', "", $addressBuyer_original);?>" >
                        		<input type="text" name="addressBuyer" id="addressBuyer" value="<?php echo str_replace('"', " ", $addressBuyer);?>">
                    		</li>

                    		<li class="campo3colunas">
                        		<label>Cidade</label>
                        		<input type="hidden" id="cityBuyer_original" name="cityBuyer_original" value="<?php echo $cityBuyer_original;?>">
                       			<input type="text" name="cityBuyer" id="cityBuyer" value="<?php echo  $cityBuyer;?>">
                    		</li>

                    		<li class="campo3colunas">
                        		<label>Pa&iacute;s</label>
                        		<input type="hidden" name="country_original" id="country_original" value="<?php echo $idCountryBuyer_original;?>">
                          		<?php  // Monta a lista de paises
                            		$sql = "SELECT id, name FROM Country ORDER BY name";
                            		$sel = $idCountryBuyer;
                            		$name = "country";
                            		require_once("../../interf/Select.php");
                          		?>
                    		</li>

                    		<li class="campo3colunas">
                        		<label>Cep</label>
                          		<input type="hidden" name="cepBuyer_original" id="cepBuyer_original" value="<?php echo $cepBuyer_original;?>">
                          		<input type="text" class="caixa" name="cepBuyer" value="<?php echo  $cepBuyer;?>">
                    		</li>
           
                    		<li class="campo3colunas">
                        		<label>Contato</label>
                        		<?php if ($contactBuyer) 
										echo $contactBuyer;
									 else
										echo ("-");
									?>
                    		</li>
                    
                   		<li class="campo3colunas">
                        		<label>Email</label>
                        		<?php if ($email)
										echo  $email;
									else
										echo ("-");?>
                    		</li>
                    
                    		<li class="campo3colunas">
                        		<label>Risco</label>
                        		<?php echo  $riskBuyer;?>
                    		</li>
                    
                    		<li class="campo3colunas">
                       			<label>Autoriza divulgar nome ao importador</label>
                        		<?php  if ($divulgaNome == 1)
										  echo("Sim");
									   else
										  echo("Não");
									?>
							</li>
            
                    		<li class="campo3colunas">
                        		<label>Volume <?php echo $extMoeda;?></label>
                        		<?php echo  $prevExp12;?>
                    		</li>
                  
				<?php  
                 		if ($role['creditManager']){
		    			?>
                      			<li class="campo3colunas">
                        			<label>Cr&eacute;dito Solicitado <?php echo $extMoeda;?></label>
                        			<input type="text" name="limCredit" id="limCredit" value="<?php echo $limCreditBuyer?>" />
                      			</li>
           			<?php } else { ?>
                        		<li class="campo3colunas">
                            			<label>Cr&eacute;dito Solicitado <?php echo $extMoeda;?></label>
                            			<?php echo $limCreditBuyer;?>
                        		</li>
				<?php } ?>
            
            			<li class="campo3colunas">
                			<label>Cr&eacute;dito Concedido <?php echo $extMoeda;?></label>
                			<?php echo number_format($credCons,2,",",".");?>
            			</li>
            
            			<li class="campo3colunas">
                			<label>N.&ordm; Emb./Ano</label>
                			<?php echo $numShip12Buyer;?>
            			</li>
            
            			<li class="campo3colunas">
               			 	<label>Per/Emb(dias)</label>
               			 	<?php echo $periodicityBuyer;?>
            			</li>
            
           			<?php if ($role['credit']) {?>
                        		<li class="campo3colunas">
                            			<label>Prz./Pag.(dias)</label>
                            			<input type="hidden" name="przPag_original" value="<?php echo $przPagBuyer_original;?>" />
                            			<input type="text" style="width:18%;" name="przPag" onBlur="validaInteiroPositivo(this, this.value);" value="<?php echo $przPagBuyer;?>" >
                        		</li>
            			<?php } else { ?>
                        		<li class="campo3colunas">
                            			<label>Prz./Pag.(dias)</label>
                            			<input type="hidden" name="przPag" value="<?php echo $przPagBuyer;?>" />
                            			<?php echo $przPagBuyer;?>
                        		</li>
            			<?php }?>
            			<?php  
           
                 		$aux_stateExp = odbc_result(odbc_exec($db, "select state from Inform where id=$idInform"), 1);
            
                 		if ($aux_stateExp==10){
                   			$fl_default_email = "checked";
                   			$fl_default_email = "";
                 		}else{
                   			$fl_default_email = "";
                 		}
				 
            			?>
            			<li class="campo3colunas">
                			<label>e-mail</label>
                			<div class="formopcao">
                  				<input name="frm_env_mail" type="checkbox" <?php echo  $fl_default_email;?> />
                			</div>

                			<div class="formdescricao"><span>Enviar e-mail de alteração de dados na atualização</span></div>
            			</li>

            			<li class="campo3colunas">
                			<label>Rela&ccedil;&atilde;o Comercial(Ano)</label>
                			<input type="text" name="relation" id="relation" value="<?php echo  $relation;?>">
            			</li>
            		</ul>
            
		<?php  } // while
            
				if ($i == 0) {
                 	echo("<span>Nenhum encontrado.</span>");
            	}
            
            	?>
            	<input type="hidden" name="comm"	 	value="setBuyer">
            	<input type="hidden" name="inativar"   		value="0">
            	<input type="hidden" name="idInform" 		value="<?php echo  $idInform;?>">
            	<input type="hidden" name="idNotification" 	value="<?php echo  $idNotification;?>">
            	<input type="hidden" name="idBuyer"  		value="<?php echo  $idBuyer;?>">
            	<input type="hidden" name="flag_renovacao" 	value="<?php echo  $flag_renovacao;?>">
            	<input type="hidden" name="origem" 	 	value="<?php echo  $field->getField("origem");?>">
            	<input type="hidden" name="state_scape" 	value="<?php echo  $state_scape;?>">
            	<input type="hidden" name="ret" 		value="<?php echo  $ret;?>">

              	<div class="barrabotoes">  
                	<button class="botaovgm" type="button" onClick="this.form.comm.value='<?php echo  $ret;?>';this.form.submit()">Voltar</button>
			<?php  
                	if ($role['creditManager'] || $role['client']){ 
					 
					     if($state != 7){?>
							<button class="botaoagm" type="button" onclick="this.form.submit()">Atualizar</button>
                        	<button class="botaoagm" type="button" onClick="this.form.inativar.value=1;this.form.submit()" >Inativar</button>
				   <?php }else{ ?>	
						    <button class="botaoagm" type="button" onclick="this.form.submit()">Atualizar</button>
							<button class="botaoagm" type="button" onClick="this.form.inativar.value=2;this.form.submit()" >Ativar</button>
				<?php    } ?>			
							
          	<?php   } ?>
              	</div>  

              	<div style="clear:both">&nbsp;</div>

              	<li class="campo3colunas">      
                   	<div class="formopcao">
                      		<input type="checkbox" name="analise" checked="checked">
                      	</div>

			<div class="formdescricao"><span>Inativar com cobran&ccedil;a de an&aacute;lise</span></div>
		</li>
	</form>
</div>

<div class="conteudopagina">      
	<div style="clear:both">&nbsp;</div>
		<table width="100%">
			<thead>
            			<tr>
                			<th colspan="10">Opções</th>
            			</tr>
        		</thead>

        		<tbody>
            			<tr>
                			<td colspan="10">
                				<a href="<?php echo $root;?>role/credit/Credit.php?comm=original&idBuyer=<?php echo $idBuyer;?>&idInform=<?php echo $idInform;?>&origem=<?php echo $origem;?>">Ver cadastro original</a>
                			</td>
            			</tr>
			
				<tr>
                			<td colspan="10">
					        <a href="<?php  echo $root.'role/credit/Credit.php?comm=obs&idBuyer='.$idBuyer. '&idInform='. $field->getField("idInform").'&idNotification='. $field->getField("idNotification"). '&origem='.$field->getField("origem"); ?>&full=0">Observa&ccedil;&otilde;es</a>
					</td>
				</tr>


                        	<?php  if ($role['creditManager']){ ?> 
					<tr>
                				<td colspan="10">
                        				<a href="<?php echo  $root;?>role/credit/Credit.php?comm=addAddress&idBuyer=<?php echo  $idBuyer;?>&origem=<?php echo $field->getField('origem');?>&idInform=<?php echo $field->getField('idInform');?>">Acrescentar endere&ccedil;os adicionais</a>
						</td>
					</tr>
                        	<?php  } ?>
        		</tbody>
     		</table>
               
            	<div style="clear:both">&nbsp;</div>    

          	<?php  $strSQL = "select id, comment, hide from ImpComment where idImporter=$idBuyer";
                 $cur = odbc_exec($db, $strSQL);
    
                if (odbc_fetch_row($cur)) {    ?>
			<p>(Existem observa&ccedil;&otilde;es cadastradas)</p>
          	<?php  }?>
    
         	<div style="clear:both">&nbsp;</div>

        	<table width="100%">
            		<caption>Hist&oacute;rico de Cr&eacute;dito</caption>
            		<thead>
                		<tr>
                  			<th width="10%" style="text-align:center;">Data de Solicita&ccedil;&atilde;o</th>
                  			<th width="10%" style="text-align:center;">Data de Decis&atilde;o</th>
                  			<th width="10%" style="text-align:right;">Solicitado <br> <?php echo $extMoeda;?> Mil</th>
                  			<th width="10%" style="text-align:right;">Concedido <br> <?php echo $extMoeda;?> Mil</th>
                  			<th width="12%" style="text-align:right;">Cr&eacute;dito Temp. <br> <?php echo $extMoeda;?> Mil</th>
                  			<th width="12%" style="text-align:center;">Vencimento <br> Cr&eacute;dito Temp</th>
                  			<th>Solicitante</th>
                  			<th width="16%" style="text-align:left;">Situa&ccedil;&atilde;o</th>
                		</tr>
            		</thead>

            		<tbody>
			  	<?php  $sql_showbuyers = "
                                  SELECT 
					CC.creditDate As creditDate, 
					CC.creditSolic As creditSolic, 
					CC.credit As credit, 
					CC.creditTemp As creditTemp, 
					CC.limTemp As limTemp, 
					CC.analysis As analysis, 
					CC.monitor As monitor, 
					CC.state As state, 
					CC.stateDate As stateDate, 
					Case 
						When CC.Capri = 1 Then 'CAPRI'
						Else U.name 
					End As Solicitante,
					CC.Capri As Capri
				FROM 
					ChangeCredit CC
				Left Join Users U On
					U.id = CC.userIdChangeCredit
				WHERE 
					CC.idImporter = $idBuyer
				ORDER BY 
					CC.stateDate";
              			$cur = odbc_exec ($db,$sql_showbuyers);
            
              			$count = 0;

              			while (odbc_fetch_row ($cur)) {
                			$state = odbc_result ($cur, 'state');
                			$stateString = "Nulo";

                			if ($state == 1)
                  				$stateString = "Novo";
                			else if ($state == 2)
                  				$stateString = "Solicitado";
                			else if ($state == 3)
                 				$stateString = "Demanda Novo";
                			else if ($state == 4)
                  				$stateString = "Reativado";
                			else if ($state == 6)
                  				$stateString = "Concedido";
                			else if ($state == 7)
                  				$stateString = "Inativado";
                			else if ($state == 8)
                  				$stateString = "Recusado";
            
                			$count ++;
            
                			$data = odbc_result($cur, 1);
                			$data = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
                			$DataSolicitacao = $data;
            
                			$data = odbc_result($cur, 5);
                			$dataTemp = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
                			if ($dataTemp == "//") $dataTemp = "";

					$data = odbc_result($cur, stateDate);
					$dataCred = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
            
                			$creditSolic = odbc_result ($cur, "creditSolic");

                			if ($creditSolic != '' && $creditSolic != 0) {
                  				$creditSolic /= 1000;
                  				$creditSolic = number_format($creditSolic, 0, ",", ".");
                			} else {
                  				$creditSolic='';
                			}
            
                			$credit = odbc_result ($cur, "credit");

                			if ($credit != '' && $credit != 0)
                  				$credit /= 1000;
                			else $credit='';

                			$creditTemp = odbc_result ($cur, "creditTemp");

                			if ($creditTemp != '' && $creditTemp != 0)
                  				$creditTemp /= 1000;
                			else $creditTemp='';

             				?>
         	 			<tr>
                   				<td style="text-align:center;"><?php echo  $DataSolicitacao;?></td>
                   				<td style="text-align:center;"><?php echo  $dataCred;?></td>
                   				<td style="text-align:right;"><?php echo  $creditSolic == '' ? '' : $creditSolic;?></td>
                   				<td style="text-align:right;"><?php echo  $credit == '' ? '0' : number_format ($credit,0,",",".");?></td>
                   				<td style="text-align:right;"><?php echo  $creditTemp == '' ? '' : number_format ($creditTemp,0,",",".");?></td>
                   				<td style="text-align:center;"><?php echo  $dataTemp;?></td>
                   				<td><?php echo  $Solicitante;?></td>
                   				<td><?php echo  $stateString;?></td>
             				</tr>
			  	<?php  } // while

              			if ($count == 0){
              				?>
           				</tbody>
           				<tfoot>
                				<tr><th colspan=8 bgcolor="#a4a4a4">Histórico Inexistente</th></tr>
           				</tfoot>
              			<?php  } ?>
		</table>
</div>