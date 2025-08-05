<?php
if (!function_exists(TrataData)){
	function TrataData($data1, $tipo, $saida){
		#
		# Variavel $data é a String que contém a Data em qualquer formato
		# Variavel $tipo é que contém o tipo de formato data.
		# $tipo : 
		#		1 - Brasil - No formato -> Dia/Mes/Ano ou DD/MM/YYYY
		#		2 - USA	 - No formato -> YYYY-Mes-Dia ou YYYY-MM-DD
		#
		# $saida :
		# 	    1 - Brasil
		# 	    2 - USA		
		#
		# Obs
		# Esta função não funciona com timestemp no formato a seguir :
		# DD/MM/YYYY H:M:S.MS ou YYYY-MM-DD H:M:S:MS
		# Pode configurar o formato da Data
		
		
		$data = explode(" ", $data1);
		
		if ( $tipo == 1) {
			list($dia, $mes, $ano) = explode("[/-]", $data[0]);		
		}elseif ( $tipo == 2 ) {
			list($ano, $mes, $dia) = explode("[-/]", $data[0]);		
		}else{
			$msg = "Erro - Formato de data não existe.";
		}	
		
		if ($saida == 1) {		
			return $dia."/".$mes."/".$ano;
		}elseif ($saida == 2){
			return $ano."-".$mes."-".$dia;
		}else{
			return 0;
		}
	}
}

require_once("../../../navegacao.php");

?>

<script language="JavaScript" src="<?php echo $root;?>scripts/calendario.js"></script>
<script Language="JavaScript">

	function checa_formulario(conf_prop){
		if (conf_prop.date.value == ""){
			verErro("Por Favor, Preencha a Data de Recebimento da Proposta");
			conf_prop.date.focus();

			return (false);
		}

		return (true);
	}

	function checa_resseguro(form){	
		if(form.i_Contrato_Resseguro.value == ""){
	        	verErro("Por Favor, selecione o contrato de resseguro");
	        	form.i_Contrato_Resseguro.focus();

	        	return false;
		}else{
		   	return true;
		}
	}

</script>

<!-- Exibir Calendario -->

<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $host;?>src/scripts/calendario/calendar-win2k-cold-1.css" title="win2k-cold-1" />

	<!-- main calendar program -->
	<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/calendar.js"></script>

	<!-- language for the calendar -->
	<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/lang/calendar-en.js"></script>

	<!-- the following script defines the Calendar.setup helper function, which makes
       		adding a calendar a matter of 1 or 2 lines of code. -->
  	<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/calendar-setup.js"></script>
  
<!-- FIM CALENDARIO -->

	<div class="conteudopagina">
    <table summary="Submitted table designs" >
	    <?php if ($msgData){ ?>
				<script type="text/javascript">
					verErro('<?php echo $msgData;?>');
				</script>
	    <?php }?>
			<thead>
				<tr>
					<th scope="col" width="25%">Segurado</th>
	          		<th scope="col" width="8%">Emissão Proposta</th>
	          		<th scope="col" width="8%">Moeda</th>
	          		<th scope="col" width="8%">DPP</th>
	          		<th scope="col" width="20%">Dt Receb/Aceite</th>
	          		<th scope="col" width="42%">Contrato Resseguro</th>
	          		<th scope="col" width="10%">Op&ccedil;&otilde;es</th>
	            </tr>
	        </thead>
			<tbody>
			<?php
				$query = "SELECT 
										Inf.id, Inf.name, Inf.pgOk, Inf.mailOk, Inf.dateEmission, Inf.dateBack, Inf.idUser, Executivo.name As Executivo,
										Inf.state,Inf.mailOk,Inf.codProd, Moeda.Sigla, Inf.contrat, Inf.dateAceit
									FROM Inform Inf
										LEFT JOIN Users Executivo ON Executivo.id = Inf.idUser
										INNER JOIN Moeda Moeda ON Moeda.i_Moeda = Inf.currency
									WHERE (Inf.state = 6 OR Inf.state = 7 OR Inf.state = 10) AND (Inf.mailOk=0 OR Inf.dateBack IS NULL) AND Inf.codProd > 0 
									ORDER BY Inf.name";
				$cur = odbc_exec($db,$query);
			    
				$i = 0;
				while (odbc_fetch_row($cur)) {
					$i ++;  
					$idInf      	= odbc_result($cur,1);
					$pgOK       	= odbc_result($cur,3);
					$mailOK     	= odbc_result($cur,4);
					$idConsulta 	= odbc_result($cur,1);
					$DateBack   	= odbc_result($cur,6);
					$state          = odbc_result($cur,'state');
					$mailOk         = odbc_result($cur,'mailOk');
				
				
					$U 			= odbc_result($cur,7);
		     	$data 		= odbc_result($cur, 5);
		     	$data 		= substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
		     	$sigla 		= odbc_result($cur, 12);
		     	$dpp 		= odbc_result($cur, 13);
		     	$data_aceitacao 		= odbc_result($cur, 14);

		     	$datadata_aceitacao_fin 		= substr($data_aceitacao, 8, 2). "/". substr($data_aceitacao, 5, 2). "/". substr($data_aceitacao, 0, 4); ?>
		    
					<form action="../backoffice/BackOffice.php" method="post" name="conf_prop<?php echo $i;?>">
				      <input type="hidden" name="comm" id="comm" value="">
				      <input type="hidden" name="idInform" value="<?php echo odbc_result($cur,1);?>">
				        
							<tr>
								<td><?php echo odbc_result($cur,2);?></td>
				        <td><?php echo $data;?></td>
				        <td><?php echo $sigla;?></td>
				        <td><?php echo $dpp;?></td>            
				        <td>
									<?php ?>
									<!-- COMENTARIO INTERAKTIV 16/06/2014 -->
									<input class="semformatacao"  type="text" size="11" name="date" id="date_<?php echo $i;?>">&nbsp;
				      		<img src="<?php echo $host;?>images/icone_calendario.png" name="imgDate_<?php echo $i;?>" id="imgDate_<?php echo $i;?>" alt="" class="imagemcampo" />
				      		<script type="text/javascript">
				          	Calendar.setup({
				              		inputField     :    "date_<?php echo $i;?>",     	// id of the input field
				              		ifFormat       :    "dd/mm/y",      			// format of the input field
				              		button         :    "imgDate_<?php echo $i;?>",  	// trigger for the calendar (button ID)
				              		align          :    "Tl",           			// alignment (defaults to "Bl")
				              		singleClick    :    true
				          	});
				      		</script>
				      		<?php ?>
			      		
				        </td>
				        <td>        
								<?php 
									$sql = "SELECT CR.i_Contrato_Resseguro, CR.Desc_Contrato, CR.Inicio_Vigencia, CR.Fim_Vigencia 
													FROM Inform Inf 
														INNER JOIN Empresa_Produto EP ON EP.i_Produto = Inf.i_Produto
							      				INNER JOIN Contrato_Resseguro CR ON CR.i_Empresa = EP.i_Empresa 
							      			WHERE	Inf.id = ".$idInf."
							      			ORDER BY Desc_Contrato";
									$cur1 = odbc_exec($db,$sql); ?>               
										<select name="i_Contrato_Resseguro" id="i_Contrato_Resseguro_<?php echo $i;?>" onChange="">
				             	<option value="">Selecione...</option>
				             	<?php while ($dados = odbc_fetch_row($cur1)){ 
				             		$dt_ini = strtotime(odbc_result($cur1,'Inicio_Vigencia'));
			      						$dt_fim = strtotime(odbc_result($cur1,'Fim_Vigencia'));

			      						if($dt >= $dt_ini && $dt <= $dt_fim){
					      					$selt = 'selected';
					      				} else {
					      					$selt = ''; 
					      				}
								        ?>
												<option value="<?php echo odbc_result($cur1,'i_Contrato_Resseguro');?>"  <?php echo $selt;?>><?php echo (odbc_result($cur1,'Desc_Contrato'));?></option>
				              <?php } ?> 
				            </select>
				        </td>
								
				      	<td>
					    		<?php if (!$DateBack){ ?>
					          <button name="mot" type="button" class="botaoapm" onClick="this.form.comm.value = 'DateBack'; this.form.submit();">Recebida</button>
					      	<?php }
					          if ((($pgOK == 1) || $DateBack) && !$mailOK){    ?>
					            <button name="mot" type="button" class="botaoapm" onClick="this.form.comm.value='done'; if(checa_resseguro(this.form)) this.form.submit();">Aceita</button>
					        <?php } ?>
				        </td>
				    	</tr>
					</form>	

				<?php
				}

				if ($i == 0) { ?>
					<tr>
						<td colspan=8>Nenhuma Proposta foi encontrada</td>
					</tr>
				<?php }	?>

	    </tbody>
    </table>
  <div class="divisoria01"></div>
  </div>