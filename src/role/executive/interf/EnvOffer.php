<body onLoad="tipoApl();tipoBanco(1);if (document.getElementById('xaplicataxa')){exibir_p_cobertura(document.getElementById('xaplicataxa'));};">
<?php

		include_once('../../../navegacao.php');
		?>

		<script src="scripts_js/controle_envoffer.js" language="javascript"></script>
		<script src="scripts_js/ajax_functions.js" language="javascript"></script>
		<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js"></script>
		<script language="JavaScript" src="<?php echo $root; ?>scripts/handlebars.js"></script>
		<script language="JavaScript" src="<?php echo $root;?>scripts/number_format.js"></script>

<?php  
	
		require_once("interf/includes/consultas.php");

		require_once("scripts_proposta.php");
?>

<?php if($valido){ ?>

<!-- Exibir Calendario -->
 
<!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="<?php echo $host?>src/scripts/calendario/calendar-win2k-cold-1.css" title="win2k-cold-1" />
  <script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/calendar.js"></script>
  <script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/lang/calendar-en.js"></script>
  <script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/calendar-setup.js"></script>

 <script>
	 function mascara(o,f){
		v_obj=o
		v_fun=f
		setTimeout("execmascara()",1)
	}
	
	function execmascara(){
			v_obj.value=v_fun(v_obj.value)
	}
	
	function data(v){
	
			v=v.replace(/\D/g,"")
			v=v.replace(/(\d{2})(\d)/,"$1/$2")
			v=v.replace(/(\d{2})(\d)/,"$1/$2")
			return v
	}
 </script>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
	<div class="conteudopagina">
		<!-- Exibir informação do Retorno AJAX --> 
	 <form action="<?php echo $root;?>role/executive/Executive.php" method="post" name="f">
		<input type=hidden name="comm" id="comm" value="sendOffer">
		<input type=hidden name="idInform" value="<?php   echo  $idInform;?>">
		<input type=hidden name="idNotification" value="<?php   echo  $idNotification;?>">
		<input type=hidden name="idRegion" value="<?php   echo  $field->getDBField('idRegion', 8);?>">
		<input type="hidden" name="jurosmora" value="<?php   echo $jurosmora;?>">
		
		<input type="hidden" name="novoPrMin" id="novoPrMin" value="<?php   echo $premioMinimo;?>">
		<input type="hidden" name="novoTxPrMin" id="novoTxPrMin" value="<?php   echo (100 * odbc_result($cur, 6) * (1 + odbc_result($cur, 7)));?>">
		
		<input type="hidden" name="totalElement" id="totalElement" value="<?php echo $x; ?>">
		<input type="hidden" name="totalElement_col" id="totalElement_col" value="<?php echo isset($x_col) ? $x_col : null; ?>">
		
		<input type="hidden" name="Operacao" id="Operacao" value="Inserir">
		<input type="hidden" name="i_Produto" id="i_Produto" value="<?php echo $i_Produto;?>">

		<?php 
			$ped = explode("/executive/", $_SERVER["REQUEST_URI"]); 
			$ped[0] = str_replace("/inform", "", $ped[0]);
			$url = $ped[0]."/inform/ajax_cnae.php";
			$url_ac = $ped[0]."/inform/ajax_ac_cnae.php";
			$url_cob = $ped[0]."/inform/ajax_descricao_cob.php";
			$url_dt_parc = $ped[0]."/inform/ajax_dt_parcelas.php"; 
			$url_mod_f401 = $ped[0]."/inform/ajax_mod_f401.php";  
			$url_mod_f5202 = $ped[0]."/inform/ajax_mod_f5202.php";
			$url_periodo_vigencia = $ped[0]."/inform/ajax_periodo_vigencia.php"; ?>		

		<?php require_once("interf/includes/header.php"); ?>
	   
		<?php require_once("interf/includes/periodo_vigencia.php"); ?>

		<?php require_once("interf/includes/cnae.php"); ?>

		<?php require_once("interf/includes/analise_monitoramento.php"); ?>

		<?php require_once("interf/includes/corretores.php"); ?>

		<?php require_once("interf/includes/proposta.php"); ?>

		<?php require_once("interf/includes/certificacao_digital.php"); ?>
	   
		<?php require_once('interf/includes/modulos.php'); ?>

		<?php require_once('interf/includes/condicoes.php'); ?>

		<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
			<input  type="hidden" name="currencyAnalize" value="<?php   echo  $currencyAnalize; ?>">
			<input  type="hidden" name="currencyMonitor" value="<?php   echo  $currencyMonitor; ?>">
			<button class="botaoagm" type="button"  onClick="this.form.comm.value='notif';this.form.submit()">Voltar</button>
			<button class="botaoagg" type="button" onClick="this.form.comm.value='cancelInf';confirma(this.form);">Cancelar Informe</button>
			<button class="botaoagg" type="button" onClick="this.form.comm.value='retarifar'; this.form.submit()">Retarifa&ccedil;&atilde;o</button>
		   
			

			<?php if ($sentOffer != 0 || $idAnt)  {?>
					<button class="botaoagg" type="button" onClick="this.form.comm.value='sendOffer';consist(this.form);">Enviar Nova Oferta</button>
					<button class="botaoagg" type="button" onClick="if(enviar(this.form)) f.submit();">Enviar Proposta</button>
			<?php } else { ?>
					<button class="botaoagg" type="button" onClick="this.form.comm.value='sendOffer';consist(this.form);">Enviar Oferta</button>
			<?php  } if ($role["creditManager"]) { ?>
				<button class="botaoagg" type="button"  onClick="this.form.comm.value='devolve';this.form.submit();">Reestudo</button>
				<button class="botaoagg" type="button"  onClick="this.form.comm.value='devcredito';this.form.submit();">Analis. cr&eacute;dito</button>
			<?php } ?>
	
		</li>
	
		<div style="clear:both">&nbsp;</div>
		<li  id ="clear" class="campo2colunas" style="width:800px;">
			<label>* Todas as parcelas vencer&atilde;o sempre no mesmo dia do m&ecirc;s, baseadas no dia do vencimento da primeira parcela.</label>
		</li>
	</form>
	
	
	
<?php  } else {   ?>  
	
		  <div style="clear:both">&nbsp;</div>
				 <ul>
					<li  class="campo2colunas">  
						 <p class="verm">Inform inv&aacute;lido</p>
					</li> 
				</ul>
	
	<?php  }   ?>
 
	<div style="clear:both">&nbsp;</div>
	<div style="clear:both">&nbsp;</div>
	
 
	
	
 <script language="javascript" type="text/javascript">
	


	
	
	function confirma(c){
	 if (confirm('Tem certeza que deseja cancelar?')){
		 c.submit();
		return true;
	 }else{
	   return
	 }
	}
	
	calc(document.f);
	

	
	
	</script>
	<?php 
		if ($mBonus == "1") {
		   $bonus = "checked";  ?>
			<script> document.getElementById("idModuloF9.02").checked = true; 
					 exibir_bonus();
			</script>
   <?php }
	
		if ($mPart == "1") {
		   $part = "checked";
		   echo "<script>exibir_part();</script>";
		}
	   
		if ($pLucro == "F13") {
		   $f13 = "checked";
		   echo "<script>document.all.idModuloRadio[0].checked=true;</script>";
		}else if ($pLucro == "F14") {
		   $f14 = "checked";
		   echo "<script>document.all.idModuloRadio[1].checked=true;</script>";
		}else if ($pLucro == "F15") {
		   $f15 = "checked";
		   echo "<script>document.all.idModuloRadio[2].checked=true;</script>";
		}
		
	
	
	?>

 </div>  
 
 <!-- 
<script type="text/javascript" src="<?php echo $host;?>Scripts/script.js"></script>
<script type='text/javascript' src='<?php echo $host;?>Scripts/packed.js'></script>
<script type="text/javascript">
	var parentAccordion=new TINY.accordion.slider("parentAccordion");
	parentAccordion.init("acc","h3",0,0);
	var nestedAccordion=new TINY.accordion.slider("nestedAccordion");
	nestedAccordion.init("nested","h3",1,-1,"acc-selected");
</script>
-->		
	 <?php
	 	$tp_banco = isset($tp_banco) ? $tp_banco : 0;
		   if($tp_banco != 0){  ?>
			   <script>
				desmarca_bb();																	
				tipoBanco(0); 
				</script>
	<?php   }
	?>    
<script>

	if(document.getElementById("i_Sub_Produto").value == ""){
		//alert("Atenção: Para prossegir com o preenchimento deste formulário nos módulos, você deve selecionar o Sub-Produto. Por favor faça isso agora.");
	   //verErro("Atenção: Selecione o Sub-Produto para prosseguir com o preenchimento deste formulário.");
	   document.getElementById("i_Sub_Produto").focus();
	}
	
	 function indiceAtiva(obj){
		 
		 if(document.getElementById("indice"+obj).style.display == 'none'){
			   document.getElementById("indice"+obj).style.display = 'block';
			  
		 }else{
			  document.getElementById("indice"+obj).style.display = 'none';
		 }
		 
	   
		
	}
	
	function expandir_todos(){		 
		 var total = document.getElementById("totModulos").value;
		
		 for(i=0; i<total; i++){
			 document.getElementById("indice"+i).style.display = 'block'; 
		 }
	}
	
	function recolher_todos(){
		 var total = document.getElementById("totModulos").value;
		 for(i=0; i<total; i++){
			 document.getElementById("indice"+i).style.display = 'none'; 
		 }
	}
	
	
				
</script> 

