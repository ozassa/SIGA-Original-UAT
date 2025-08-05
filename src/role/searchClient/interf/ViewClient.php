<?php 
// alterado Hicom (Gustavo) - 05/01/04 - botão para reativar o informe caso cancelado
require_once("../dve/interf/funcsDve.php");


$executa   = isset($_POST['executa']) ? $_POST['executa'] : 0;
$renova    = isset($_POST['renova']) ? $_POST['renova'] : 0;
$idInform  = isset($_POST['idInform']) ? $_POST['idInform'] : 0;
$executive = isset($_POST['executive']) ? $_POST['executive'] : 0;
$clientR   = isset($_POST['clientR']) ? $_POST['clientR'] : 0;
$envia     = isset($_POST['envia']) ? $_POST['envia'] : 0;

$dtinicio = isset($_REQUEST['dtinicio']) ? $_REQUEST['dtinicio'] : '';
$dtfim = isset($_REQUEST['dtfim']) ? $_REQUEST['dtfim'] : '';

if ($executa == 1 and $idInform) {
	if ($renova == 1) {
		require_once("../client/renovacao.php");

		if ($executive =="") {
		  	$notif->newInfExcutivo($userID, $clientR, $idInform, $db);
		} else {
		  	//
		}
  	} else {
		/*
    		//####### ini ####### adicionado por eliel vieira - elumini - 03/03/2008
    		// limpeza dos parametros dos modulos especiais solicitado na demanda
    		// 1369 - SAD - alteracao dos campos state, mModulos, perPart0, perPart1, e perBonus.
    		*/

		//$sql = " UPDATE Inform SET state = 1 WHERE id = $idInform";
		$sql = "UPDATE Inform SET state = ?, mModulos = ?, perBonus = ?, perPart0 = ?, perPart1 = ? WHERE id = ?";
$stmt = odbc_prepare($db, $sql);

$params = [1, 0, 0, 0, 0, $idInform];

odbc_execute($stmt, $params);

// Liberação do recurso
odbc_free_result($stmt);


		if ($executive =="") {
	  		$notif->newInfExcutivo($userID, $clientR, $idInform, $db);
		} else {
	  		//
		}

    		/*
    		//####### ini ####### adicionado por eliel vieira - elumini - 17/03/2008
    		// limpeza dos campos das tabelas com dados inform, solicitado na demanda 1370 - SAD
    		// opcoes de update validos somente para nao apolices (validado pelos campos
    		// nApolice null e state diferente de 10 na tabela inform
    		*/

		//sql para selecionar registro que nao seja apolice
		$sql_valid_ok = "SELECT id FROM Inform WHERE state NOT IN (?) AND n_Apolice IS NULL AND id = ?";
$stmt_valid_ok = odbc_prepare($db, $sql_valid_ok);

$params_valid_ok = [10, $idInform];

odbc_execute($stmt_valid_ok, $params_valid_ok);

// Processamento do resultado, se necessário
// Exemplo: $row = odbc_fetch_array($stmt_valid_ok);

// Liberação do recurso
$x_valid_ok = $stmt_valid_ok;
odbc_free_result($stmt_valid_ok);

    		$id_valid_ok  = "";

    		while (odbc_fetch_row($x_valid_ok)) {
      			$id_valid_ok = odbc_result($x_valid_ok, 1);
    		}
  	} //fim if ($renova == 1) {
	} //fim if ($executa == 1 and $idInform) {


		?>
		<SCRIPT language="javascript">
			function vai(renova,idInform,clientR,executive){
				document.Form2.action = '<?php echo $root; ?>role/searchClient/SearchClient.php';
				document.Form2.executa.value = 1;
				document.Form2.clientR.value = clientR;
				document.Form2.executive.value = executive;
				document.Form2.renova.value = renova;
				document.Form2.idInform.value = idInform;

				if (renova==0) {
		  			if (confirm('Tem certeza que deseja reativar este informe? Seus valores serão zerados!')) {
		    				document.Form2.submit();
		  			}
				}

				if (renova==1) {
		  			document.Form2.submit();
				}
			}

			function cancela(renova,idInform,clientR,executive) {
	    			verErro(renova +' - '+ idInform +' - '+ clientR +' - '+ executive);
				document.Form2.action = '<?php echo $root;?>role/searchClient/CancelarApolice.php';
				document.Form2.executa.value = 1;
				document.Form2.clientR.value = clientR;
				document.Form2.executive.value = executive;
				document.Form2.renova.value = renova;
				document.Form2.idInform.value = idInform;
				document.Form2.submit();
			}
		</SCRIPT>

<!-- Exibir Calendario -->
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $host;?>src/scripts/calendario/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/calendar.js"></script>

<!-- language for the calendar -->
<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/lang/calendar-en.js"></script>

<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/calendar-setup.js"></script>
  
<!-- FIM CALENDARIO -->

<?php include_once("../../../navegacao.php");?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<form id="Form2" name="Form2" action="<?php echo $root;?>role/searchClient/SearchClient.php" method="post">
	<div class="conteudopagina">
      		<li class="campo2colunas">
        		<label>Nome</label>
        		<input name="nome" id="nome" type="text" />
      		</li>

      		<li class="campo2colunas">
        		<label>Status</label>
        		<select name="status" id="status">
            			<option value=0 selected>Selecione</option>
            			<option value=1>Novo</option>
            			<option value=2>Preenchido</option>
            			<option value=3>An. Cr&eacute;dito</option>
            			<option value=4>Tarifa&ccedil;&atilde;o</option>
            			<option value=5>Oferta</option>
            			<option value=6>Proposta</option>
            			<option value=7>1&ordf; Parc. Pg</option>
            			<option value=8>Alterado</option>
            			<option value=9>Cancelado</option>
            			<option value=10>Ap&oacute;lice</option>
            			<option value=11>Encerrado</option>
            			<option value=12>Prospectivo</option>
        		</select>
      		</li>

      		<li id="clear" class="campo2colunas">
        		<label>Vig&ecirc;ncia</label>
        		<input name="dtinicio" id="dtinicio" value="<?php echo htmlspecialchars($dtinicio, ENT_QUOTES, 'UTF-8'); ?>" type="text" style="width:36%;" />

        		<img src="<?php echo $host;?>images/icone_calendario.png" name="imgDtinicio" id="imgDtinicio" alt="" class="imagemcampo" />
        		<script type="text/javascript">
				Calendar.setup({
					inputField     :    "dtinicio",     	// id of the input field
					ifFormat       :    "dd/mm/y",      	// format of the input field
					button         :    "imgDtinicio",  	// trigger for the calendar (button ID)
					align          :    "Tl",           	// alignment (defaults to "Bl")
					singleClick    :    true
				});
			</script>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<input name="dtfim" id="dtfim" type="text" value="<?php echo htmlspecialchars($dtfim, ENT_QUOTES, 'UTF-8'); ?>" style="width:36%;" />
        		<img src="<?php echo $host;?>images/icone_calendario.png" name="imgDtfim" id="imgDtfim" alt="" class="imagemcampo" />
        		<script type="text/javascript">
				Calendar.setup({
					inputField     :    "dtfim",     	// id of the input field
					ifFormat       :    "dd/mm/y",      	// format of the input field
					button         :    "imgDtfim",  	// trigger for the calendar (button ID)
					align          :    "Tl",           	// alignment (defaults to "Bl")
					singleClick    :    true
				});
			</script>
      		</li>

      		

      		

      		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
      			<button name="pesquisar_bt" onclick="javascript: Form2.submit();" class="botaoagm">Pesquisar</button>
      		</li>

    	</div>

    	<input type="hidden" name="renova" value="0">
    	<input type="hidden" name="executa" value="0">
    	<input type="hidden" name="idInform" value="">
    	<input type="hidden" name="executive" value="">
    	<input type="hidden" name="clientR" value="">
    	<input type="hidden" value="OK" name="envia">
</form>
    
<?php  if($envia){
			require_once("../searchMachine/SearchMachine.php");
		} 
?>

<!-- CONTEÚDO PÁGINA - FIM -->
<!-- CONTEÚDO PÁGINA - INÍCIO -->

<div class="conteudopagina">
	<table summary="" id="example" class="no-sort">
		<thead>
			<tr>
        <th class="th-no-sort" style="display: none;">id</th>
        <th >Segurado</th>
				<th >DPP</th>
				<th >Tipo</th>
				<th >Moeda</th>
				<th >Executivo</th>
				<th >Situa&ccedil;&atilde;o</th>
        <th colspan="2">Op&ccedil;&atilde;o</th>
			</tr>
		</thead>	

		<?php 
		if($envia){	   
			require_once("list.php"); //forma a saida.
		}    
		?>    
	</table>

	<div class="divisoria01"></div>

	<?php  	 
	if($envia){	 	?>
               	<!-- <div class="barrabotoes"><a href="pag_consumiveis.asp"><img src="<?php echo $host;?>images/botao_incluir.png" alt="" /></a></div>-->
      	<?php }    ?>
      
</div>
<!-- CONTEÚDO PÁGINA - FIM -->