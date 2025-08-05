<style>
	.label_emp {
		border-left: 1px solid #eeeeee;
		border-right: 1px solid #eeeeee;
		padding: 0px 0 0 10px;
		font-size: 10px;
		float: left;
		height: 14px;
		border-radius: 15px;
		margin-top: 3px;
	}
	.legenda-acorddion{font: normal 12px Arial, "Trebuchet MS", Verdana, Tahoma, Helvetica; padding: 15px;}
</style>
<?php
	
  if(!isset($_SESSION)){

  	session_set_cookie_params([
    'secure' => true,
    'httponly' => true
]);
session_set_cookie_params([
    'secure' => true,
    'httponly' => true
]);
session_start();
  }

  if(isset($_POST)){
  	//extract($_POST);
  }
  
  // Incluir funções de segurança
  require_once("security_functions.php");
  
  $criterio = "";
  $junta    = "";
  
  if(isset($edt_per_ini) && isset($edt_per_end)){
  	if($edt_per_ini && $edt_per_end){
	  	$criterio .= $junta." n.bornDate between convert(datetime,'$edt_per_ini',103) and convert(datetime,'$edt_per_end',103) ";
	  	$junta = " and ";
	  }
  }

  $cmb_tp_notification = isset($cmb_tp_notification) ? $cmb_tp_notification : 0;
  if ($cmb_tp_notification != 0) {
	  $criterio .= $junta." n.tp_notification_id in ($cmb_tp_notification,0) ";
	  $junta = " and ";
  }

  if (isset($cmb_tp_apolice)) {
  	if ($cmb_tp_apolice) {
	  	$criterio .= $junta." n.tp_apolice in ($cmb_tp_apolice) ";
	  	$junta = " and ";
	  }
  }

  if (isset($cmb_emp)) {
  	if ($cmb_emp) {
	  	$criterio .= $junta." E.i_Empresa in ($cmb_emp) ";
	  	$junta = " and ";
	  }
  }
 
  ?>
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

<?php	require_once("../../../navegacao.php"); ?>

 <div class="conteudopagina">
<!-- CONTEÚDO PÁGINA - INÍCIO -->
   <form name="frm_tree" action="<?php echo $root; ?>role/access/Access.php" method="post" target="_self" font="courier new" onload="">
   
      <li id="clear" class="campo2colunas">
        <label>Vig&ecirc;ncia</label>
        <input name="edt_per_ini" id="edt_per_ini" type="text" style="width:36%;" />
        <img src="<?php echo $host;?>images/icone_calendario.png" name="imgDtinicio" id="imgDtinicio" alt="" class="imagemcampo" />
        <script type="text/javascript">
			Calendar.setup({
				inputField     :    "edt_per_ini",     	// id of the input field
				ifFormat       :    "dd/mm/y",      	// format of the input field
				button         :    "imgDtinicio",  	// trigger for the calendar (button ID)
				align          :    "Tl",           	// alignment (defaults to "Bl")
				singleClick    :    true
			});
		</script>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="edt_per_end" id="edt_per_end" type="text" style="width:37%;" />
        <img src="<?php echo $host;?>images/icone_calendario.png" name="imgDtfim" id="imgDtfim" alt="" class="imagemcampo" />
        <script type="text/javascript">
			Calendar.setup({
				inputField     :    "edt_per_end",     	// id of the input field
				ifFormat       :    "dd/mm/y",      	// format of the input field
				button         :    "imgDtfim",  	// trigger for the calendar (button ID)
				align          :    "Tl",           	// alignment (defaults to "Bl")
				singleClick    :    true
			});
		</script>
      </li>
      <li class="campo2colunas">
        <label>Tipo de Notifica&ccedil;&atilde;o</label>
        <?php		
					$sql3 = " select b.id, a.state, a.id as notif, a.notification from NotificationR a 
							inner join Inform b on a.idInform = b.id
							and a.state = 1 and (b.state = 6 or b.state = 10) and a.tp_notification_id = 4 ";
					$cur2 = odbc_exec($db, $sql3);
					while (odbc_fetch_row($cur2)) {
						$qry21 = "update NotificationR set state= 2 where tp_notification_id = 4 and state = 1 and id = ". odbc_result($cur2,'notif') .""; 
						$res21 = odbc_exec($db,$qry21);
					}		
		
					$sql3 = "
			         select tn.tp_notification_id as id, UPPER(tn.descricao) as descricao
			           from NotificationR n
			          inner join RoleNotification rn ON (idNotification = n.id)
			          inner join Role r ON (rn.idRole = r.id)
			          inner join UserRole ur ON (r.id = ur.idRole)
			           left join tp_notification tn on n.tp_notification_id = tn.tp_notification_id
			          where 0=0
			            and n.state = 1
			            and ur.idUser = $userID
			            and n.tp_notification_id not in (0)
			          group by tn.tp_notification_id, tn.descricao
			          order by tn.descricao
			        ";
					$cur1 = odbc_exec($db, $sql3); ?>
        <select name="cmb_tp_notification" id="cmb_tp_notification">
          <option selected value=''>--Todos--</option>
          <?php
					//loop para carregar as linhas
					while (odbc_fetch_row($cur1)) {
					  $id        = trim(odbc_result($cur1,1));
					  $descricao = trim(odbc_result($cur1,2));
					  $descricao = strtoupper($descricao);
					  $descricao = substr($descricao,0,50);
					  if ($id==$cmb_tp_notification) {
						$selected = "selected";
					  } else {
						$selected = "";
					  }
					  echo "<option $selected value=$id>".$descricao."</option>";
					} ?>
        </select>
      </li>
      <li class="campo2colunas">
        <label>Tipo da Ap&oacute;lice</label>
        <select name="cmb_tp_apolice" id="cmb_tp_apolice">
            <option value=""></option>
            <option value="0">AP&Oacute;LICE GLOBAL</option>
            <option value="1">AP&Oacute;LICE DE RISCO INDIVIDUAL</option>
        </select>
      </li>
      <?php
      	$sql4 = "
			         Select i_Empresa, Nome From Empresa Order by Nome
			        "; 
			  $curr_emp = odbc_exec($db, $sql4);
			?>
      <li class="campo2colunas">
        <label>Empresa</label>
        <select name="cmb_emp" id="cmb_emp">
            <option value=""></option>
            <?php
            	$cmp_emp = isset($_REQUEST["cmb_emp"]) ? $_REQUEST["cmb_emp"] : 0;
							//loop para carregar as linhas
							while (odbc_fetch_row($curr_emp)) {
							  $id        = trim(odbc_result($curr_emp,1));
							  $descricao = trim(odbc_result($curr_emp,2));

							  if ($id==$cmp_emp) {
									$selected = "selected";
							  } else {
									$selected = "";
							  }
							  echo "<option $selected value='".$id."'>".$descricao."</option>";
							} 
						?>
        </select>
      </li>
      <?php if($_SESSION['pefil'] == 'F'){ ?>	  
        <div class="barrabotoes">
             <button class="botaoagm" type="button"  onClick="javascript: frm_tree.submit();">Pesquisar</button>
        </div>   
    	<?php }else{ ?>
        <div class="barrabotoes">
             <button class="botaoagm" type="button"  onClick="">Pesquisar</button>
        </div>                  
     <?php } ?>

    <?php echo csrf_token_field(); ?>
    </form>
    
    <?php 
		 $envia = isset($_POST['envia']) ? true : false;	
			
		if($envia){
	    	require_once("../searchMachine/SearchMachine.php");
		}

      	$qry = "SELECT 
      					b.descricao, 
      					' - DPP: ' + Right('000000' + ISNull(Inf.contrat, ''), 6) + ' - ' + n.notification as notification,
      					n.link,
      					convert(char,n.bornDate,103) as bornDate, 
						n.tp_notification_id,
						n.id,n.cookie,
						n.state, 
						n.idRegion, 
						n.idInform, 
						E.Nome as nome_emp, 
						E.i_Empresa as id_emp
					FROM NotificationR n 
					INNER JOIN tp_notification b ON b.tp_notification_id = n.tp_notification_id 
					INNER JOIN tp_notification_Role c ON c.tp_notification_id = b.tp_notification_id 
					INNER JOIN UserRole d ON d.idRole = c.idRole
					Inner Join Inform Inf On Inf.id = n.idInform 
					Inner Join Empresa E On E.i_Empresa = Inf.i_Empresa			
				    WHERE ".$criterio.($junta != '' ? $junta : '')." n.state = 1  and d.idUser = '".$userID."' and link is not null and b.tp_notification_id in (6,7,43,44)
				    GROUP BY Inf.contrat, b.descricao,n.notification,n.link, n.bornDate,n.tp_notification_id,n.id,n.cookie,n.state, n.idRegion, n.idInform, E.Nome, E.i_Empresa 
					ORDER BY b.descricao, n.bornDate";

		
					   		
			$cur = odbc_exec($db, $qry);
			$x = 0;		

			  	if($cur){	?>
		        <div id="options"> <a href="javascript:parentAccordion.pr(1)" class="linktexto">Expandir todos</a> | <a href="javascript:parentAccordion.pr(-1)" class="linktexto">Recolher todos</a> </div>
		        <ul class="acc" id="acc">
	      			<?php
	      			$nx = '';
	      			
					while (odbc_fetch_row($cur)) {
						$nom = odbc_result($cur,'descricao'); 
						$notif = odbc_result($cur,'notification');
						$dateNotif = odbc_result($cur,'bornDate');
						$linkNotif = odbc_result($cur,'link'); 
						$nome_emp = odbc_result($cur,'nome_emp');
						$id_emp = odbc_result($cur,'id_emp');

						if($id_emp == 1){
							$style = "background-color: #13355F;";
						} else {
							$style = "background-color: #69B77D;";
						}

						if($nom != $nx){
							$nx = $nom; 
							if ($x > 0){
								echo '</div>';
								echo '</li>';
							} ?>
							<li>
							 <h3><?php echo safe_output(str_replace(array('DVE', 'Importador', 'Importadores'), array('DVN', 'Comprador', 'Compradores'), $nom)); ?></h3>
							 <div class="acc-section">
						<?php } ?>
						  <div class="acc-content">
						  	<div class="label_emp" style="<?php echo $style; ?>">&nbsp;</div>
						  	<a href="<?php echo safe_output($linkNotif, 'attr');?>"><?php echo safe_output($dateNotif). ' '. safe_output(str_replace('DVE','DVN',str_replace('importadores','compradores',str_replace('Importadores','Compradores',$notif)))); ?></a>
						  </div>  
						<?php
						 $x++;
					}
					if ($x > 1){
						echo '</div>';
						echo '</li>';
					} ?>
	 
	      </ul>
   		<?php } ?>

   		<div class="legenda-acorddion">
   			<label for="">Legendas:</label>
   			<div class="label_emp" style="background-color:#13355F">&nbsp;</div> <label style="line-height:22px" for="">Coface do Brasil</label>
   			<div class="label_emp" style="background-color:#69B77D">&nbsp;</div> <label style="line-height:20px" for="">SBCE</label>
   		</div>

      <!--<div class="barrabotoes"><a href="pag_principal.asp"><img src="<?php echo $host;?>images/botao_pesquisar.png" alt="" /></a></div> -->
    </div>
    <?php require_once("../notification/interf/ViewBox.php"); ?>
    <!-- CONTEÚDO PÁGINA - FIM -->
  
	<script type="text/javascript" src="<?php echo $host;?>Scripts/script.js"></script>
	<script type='text/javascript' src='<?php echo $host;?>Scripts/packed.js'></script>
	<script type="text/javascript">
	var parentAccordion=new TINY.accordion.slider("parentAccordion");
	parentAccordion.init("acc","h3",0,0);
	var nestedAccordion=new TINY.accordion.slider("nestedAccordion");
	nestedAccordion.init("nested","h3",1,-1,"acc-selected");
	</script>