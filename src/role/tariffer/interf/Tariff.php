<?php
	include_once('../../../navegacao.php'); 
 ?>

<script language="JavaScript" src="<?php echo $root;?>scripts/utils.js"></script>
<script language="JavaScript" src="<?php echo $root;?>scripts/number_format.js"></script>
<?php //Alterador por Tiago V N - Elumini - 05/06/2006
  
$query = "select * from Inform where id = $idInform";
$cur = odbc_exec($db, $query);

$nMoeda = odbc_result($cur, "currency");
$Periodo_Vigencia = odbc_result($cur, "Periodo_Vigencia");
  
if ($nMoeda == "1") {
	$extMoeda = "Real";
}else if ($nMoeda == "2") {
	$extMoeda = "Dol&aacute;r";
}else{
	$extMoeda = "Euro";
}

$query = "SELECT warantyInterest, currency FROM Inform WHERE id = $idInform";
$c = odbc_exec ($db, $query);

$juros = 0;

if (odbc_fetch_row ($c)) $juros = odbc_result ($c,1);
	$redirect = 0;

?>

<script>

  function ymd2dmy($d){
  	if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
  		return "$v[3]/$v[2]/$v[1]";
  	}

  	return $d;
  }

  function checkDecimals2(fieldName, fieldValue) {
  	if (fieldValue == "") {
  		verErro("Preenchimento obrigat&oacute;rio.");
  		fieldName.value='0,00';
  		fieldName.focus();
  	} else {
  		err = false;
  		dec = ",";
  		mil = ".";
  		v = "";
  		c = "";
  		len = fieldValue.length;

  		for (i = 0; i < len; i++) {
  			c = fieldValue.substring (i, i+1);

        			if (c == dec) { break; }

        			if (c != mil) {
          			if (isNaN(c)) {
            				err = true;
            				verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
            				fieldName.value='0,00';
            				fieldName.focus();
            				break;
          			} else {
            				v += c;
          			}
        			}
      		}

      		if (!err) {
        			if (i == len) {
          			v += "00";
        			} else {
          			if (c == dec) i++;

          			if (i == len) {
            				v += "00";
          			} else {
            				c = fieldValue.substring (i, i+1);

            				if (isNaN(c)) {
              					verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
              					fieldName.value='0,00';
              					fieldName.focus();
              					err = true;
            				} else {
              					v += c;
            				}
          			}

          			i++;

          			if (!err && i == len) {
            				v += "0";
          			} else {
            				c = fieldValue.substring (i, i+1);

            				if (isNaN(c)) {
              					verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
              					fieldName.value='0,00';
              					fieldName.focus();
              					err = true;
            				} else {
              					v += c;
            				}
          			}
        			}

        			if(! fieldValue.match(/^(\d+)(,|.)\d+$/) && ! fieldValue.match(/^\d+$/)){
  				verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
  				fieldName.value='0,00';
  				fieldName.focus();
        			}else{
  				if(fieldValue.match(/^\d+$/)){
  	  				fieldName.value = fieldValue + ',000';
  				}else if(fieldValue.match(/^(\d+)(,|.)\d\d\d/)){
  	  				fieldName.value = fieldValue.replace(/^(\d+)(,|.)(\d\d\d)\d*$/, '$1' + ',' + '$3');
  				}else{
  	  				fieldName.value = fieldValue.replace(/\./, ',');
  	  				checkDecimals(fieldName, fieldName.value);
  	  				fieldName.value += '0';
  				}
        			}
      		}
    	}
  }

  function prMaxCalc () {
  	prMax = window.document.pr.prMax;
    err = checkDecimals(prMax, prMax.value);

    //INTERAKTIV - 27/05/2014
  	/*<?php if ($juros) {
  		?>
    		if (!err) {
      			jrMax = window.document.pr.jrMax;
      			jrValue = numVal (prMax.value) *.04;
      			checkDecimals(jrMax, dot2comma(jrValue + ""));
      			prMaxTot = window.document.pr.prMaxTot;
      			prValue = numVal (prMax.value)/1 + numVal (jrMax.value)/1;
      			checkDecimals(prMaxTot, dot2comma(prValue + ""));
    		}
  	<?php }
  ?>*/

  }

  function prMinCalc () {
  	prMin = window.document.pr.prMin;
  	//   if(prMin.value.match(/,\d{4}/)){
  	//     verErro("Não são permitidas mais que 3 casas após a vírgula");
  	//     prMin.value = '';
  	//     prMin.focus();
  	//     return;
  	//   }

    	err = checkDecimals(prMin, prMin.value);

    //INTERAKTIV - 27/05/2014
  	/*<?php if ($juros) {
  	?>
      		if (!err) {
        			jrMin = window.document.pr.jrMin;
        			jrValue = numVal (prMin.value) *.04;
        			checkDecimals(jrMin, dot2comma(jrValue + ""));
        			prMinTot = window.document.pr.prMinTot;
        			prValue = numVal (prMin.value) * 1 + numVal (jrMin.value) * 1;
        			checkDecimals(prMinTot, dot2comma(prValue + ""));
      		}
  	<?php } // juros
  	?>*/
    
  }

  function confvigencia(c){
  	
  	
  	
  	var vigencia = document.getElementById("Periodo_Vigencia").value;
  	
  	if (document.getElementById("Periodo_Vigencia").value == "0" || document.getElementById("Periodo_Vigencia").value == "" ){
  	     verErro("Informe um período de vigência.");
  		 return false;
  	}else{
      if(document.getElementById("js_lmi_fator").value == "0,00" || document.getElementById("js_lmi_fator").value == ""){
        var lmi_fator = false;
      } else {
        var lmi_fator = true;
      }
      if(document.getElementById("js_lmi_valor").value == "0,00" || document.getElementById("js_lmi_valor").value == ""){
        var lmi_valor = false;
      } else {
        var lmi_valor = true;
      }
  		if(!lmi_fator && !lmi_valor){
        verErro("Informe o LMI.");
        return false;
      }

      if(lmi_fator && lmi_valor){
        verErro("Informe somente um LMI.");
        return false;
      }
      
      
      if (confirm('Você confirma a vigência de ' + vigencia + ' Meses?')){
          //c.submit();
        return true;
      }else{
         return false;
      }
    }
  }

  function cancelInf(cancel){

  	 if (confirm('Tem certeza que deseja cancelar esse informe?')){
           //cancel.submit();
           return true;
  	 }else{
  	   return false;
  	 }

  }

  function redireciona(c){

  	//verErro('Informe Cancelado com sucesso.');
      document.all.mot.value='OK';
  	c.submit();
           
  }

  function cancelNot(){
  if(confirm("Deseja ocultar esse notifica&ccedil;&atilde;o?")) {
   document.all.idInform.value=<?php echo $idInform ?>;
   document.all.idNotification.value=<?php echo $idNotification ?>;
   document.all.ocultar.value='sim';
   oculta.submit();
  } else {
   return false;
  }

  }

  function verificaPeriodo(){
       if(numVal(document.getElementById('Periodo_Vigencia').value) > 24){
  		  verErro('Aten&ccedil;&atilde;o! O per&iacute;odo de vig&ecirc;ncia deve ter at&eacute; 24 meses.');
  		  document.getElementById('Periodo_Vigencia').value = '';
  		  document.getElementById('Periodo_Vigencia').focus();
  		  return false;
  	 }
  }
</script>

<script type="text/javascript">

  function calculaCoberturaJurosMax(valor){

    var premio_max = retorna_dinheiro_us(valor);
    var porcent_max = 0;
    var sub_total_max = 0;
    var total_max = 0;
    var total_premio = 0;

    if(premio_max > 0){
      porcent_max = (4/100);
      sub_total_max = (porcent_max*parseFloat(premio_max));
      total_max = (sub_total_max);  

      total_premio = (total_max+parseFloat(premio_max));
    }

    $(".js_adCobMax").val(number_format_js(total_max,2,',','.'));
    $(".js_adCobMax").text(number_format_js(total_max,2,',','.'));
    $(".js_prMaxTot").val(number_format_js(total_premio,2,',','.'));
    $(".js_prMaxTot").text(number_format_js(total_premio,2,',','.'));
  }

  function calculaCoberturaJurosMin(valor){

    var premio_min = retorna_dinheiro_us(valor);
    var porcent_min = 0;
    var sub_total_min = 0;
    var total_min = 0;
    var total_premio = 0;

    if(premio_min > 0){
      porcent_min = (4/100);
      sub_total_min = (porcent_min*parseFloat(premio_min));
      total_min = (sub_total_min);  

      total_premio = (total_min+parseFloat(premio_min));
    }

    $(".js_adCobMin").val(number_format_js(total_min,2,',','.'));
    $(".js_adCobMin").text(number_format_js(total_min,2,',','.'));
    $(".js_prMinTot").val(number_format_js(total_premio,2,',','.'));
    $(".js_prMinTot").text(number_format_js(total_premio,2,',','.'));
  }

  $(document).ready(function(){

    $("#jurosMora").on("click", function(){
      if($(this).prop('checked')){
        $(this).val('1');
        $(".juros-js").show();
      } else{
        $(this).val('0');
        $(".juros-js").hide();
      }
    });

    $("#prMax").on("focusout", function(){
      var valor = $(this).val();

      calculaCoberturaJurosMax(valor);
    });

    $("#prMin").on("focusout", function(){
      var valor = $(this).val();

      calculaCoberturaJurosMin(valor);
    });

  })
</script>

<div class="conteudopagina">
<form name="oculta" action="../tariffer/Tariffer.php?comm=view&ocultar=sim&idInform=<?php echo $idInform; ?>&idNotification=<?php echo $idNotification; ?>&idEndosso=<?php echo $idEndosso; ?>" method="post" style="display:none;">
</form>
<form name="showNot" action="../notification/BoxInput.php" method="post" style="display:none;">
</form>

<form name="pr" action="../tariffer/Tariffer.php" method="post">
    <input type="hidden" name="comm" value="done">
    <input type="hidden" name="mot" value="">
    <input type="hidden" name="ocultar" value="">
    <input type="hidden" name="acao" value="">
    <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
    <input type="hidden" name="idPremio" value="<?php echo $idPremio; ?>">
    <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
    <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
    <input type="hidden" name="idEndosso" value="<?php echo $idEndosso; ?>">

<h3>Per&iacute;odo de Vig&ecirc;ncia</h3>
  	<ul>
  	<li class="campo3colunas">
        <label>Per&iacute;odo de Vig&ecirc;ncia</label>
    	<!-- 
        <select  name="pvigencia" class="texto">
            <option value="0">--------</option>
            <option value="1" <?php echo $pvigencia=="1" ? "selected" : ""; ?> >12 Meses</option>
            <option value="2" <?php echo $pvigencia=="2" ? "selected" : ""; ?> >24 Meses</option>
        </select>
        -->
        <input type="hidden" name="pvigencia" id="pvigencia" value="<?php echo $pvigencia;?>">
        <input type="text" name="Periodo_Vigencia" id="Periodo_Vigencia" style="text-align:right ; width:240px;" onKeyPress="return numeros();" onBlur="verificaPeriodo()" value="<?php echo ($Periodo_Vigencia ? $Periodo_Vigencia :($pvigencia == 1 ? 12:24));?>"><strong>&nbsp;Meses</strong>
         
    </li>
    
    <li class="campo3colunas">
        <label>Moeda</label>
    <?php echo ($extMoeda);?>
    </li>
    
    <li class="campo3colunas">
      <label>Cobertura para Juros de Mora</label>
        <?php 
          $sqlWarant = "select warantyInterest from Inform where id='$idInform'";  
          $cur = odbc_exec($db, $sqlWarant);
          $warantyInterest = odbc_result($cur, 'warantyInterest'); ?>
          <input type="checkbox" name="jurosMora" id="jurosMora" value="<?php echo $warantyInterest; ?>" <?php echo $warantyInterest == 1 ? "checked" : ""; ?> style="width: 14px;"/>
    </li>

	</ul>

	<div class="divisoria01">&nbsp;</div>
    
    
    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Máximo Estimado - EAP</th>
                <th>Mínimo</th>
            </tr>
        </thead>
        
        <tbody>
     
<?php if(!$idEndosso){ ?>
        <tr>
           <td><label>Pr&ecirc;mio</label></td>
           <td><input style="text-align:right;" onBlur="prMaxCalc()" onFocus="select()" name="prMax" id="prMax" value="0,00"></td>
           <td><input style="text-align:right;" onBlur="prMinCalc()" onFocus="select()" name="prMin" id="prMin" value="0,00"></td> 
        </tr>
        
        <tr class="juros-js" <?php echo $warantyInterest == 0 ? 'style="display:none;"' : ''; ?>>
          <td><label>Adicional de Cobertura de Juros de Mora (4%)</label></td>
          <td style="text-align:right"><label class="js_adCobMax">0</label><input onFocus="this.form.txMax.focus();" class="js_adCobMax" readonly="readonly"   name="adCobMax" id="adCobMax" value="0,00" type="hidden"></td>
          <td style="text-align:right"><label class="js_adCobMin">0</label><input onFocus="this.form.txMax.focus()" class="js_adCobMin" readonly="readonly"    name="adCobMin" id="adCobMin" value="0,00" type="hidden"></td>
        </tr> 	
        <tr class="juros-js" <?php echo $warantyInterest == 0 ? 'style="display:none;"' : ''; ?>>
          <td><label>Pr&ecirc;mio M&iacute;nimo Total</label></td>
    	    <td style="text-align:right"><label class="js_prMaxTot">0</label><input onFocus="this.form.txMax.focus();"  class="js_prMaxTot" readonly="readonly"  name="prMaxTot" id="prMaxTot" value="0,00" type="hidden"></td>
          <td style="text-align:right"><label class="js_prMinTot">0</label><input onFocus="this.form.txMax.focus()"   class="js_prMinTot" readonly="readonly"  name="prMinTot" id="prMinTot" value="0,00" type="hidden"></td>
        </tr> 

      	<tr>
          <td><label>Taxa de Pr&ecirc;mio</label></td>
        	<td><input  style="text-align:right;"  onBlur="checkDecimals2(this, this.value)" onFocus="select()" name="txMax" value="0,00"></td>
        	<td><input  style="text-align:right;"  onBlur="checkDecimals2(this, this.value)" onFocus="select()" name="txMin" value="0,00"></td>
        </tr>
    
    </tbody>
    </table>
    <div style="clear:both">&nbsp;</div>
    <ul>
	<li class="campo3colunas">
        <label>Fator LMI</label>
        <input  type="text" id="js_lmi_fator" onBlur="checkValue(this, this.value)" style="text-align:right" onFocus="select()" name="limPag" value="">
    </li>
    <li class="campo3colunas">
        <label>Valor LMI Flat</label>
        <input  type="text" id="js_lmi_valor" name="v_LMI" onBlur="checkDecimals(this, this.value)" style="text-align:right"  onFocus="select()" value="0,00">
    </li>
    <li class="campo3colunas">
        <label>Cobertura %</label>
        <input  type="text" onBlur="checkValue(this, this.value)" style="text-align:right" onFocus="select()" name="cobertura"/>
    </li>
	</ul>
<?php } else {
    $x = odbc_exec($db, "select prMin, txMin, txRise, warantyInterest, codProd, prMTotal, pvigencia from Inform where id=$idInform");
    $txRise = odbc_result($x, 3);
    $waranty = odbc_result($x, 4);
    $prMinOld = odbc_result($x, 1) * (1 + $txRise) * ($waranty ? 1.04 : 1);
    $txMinOld = odbc_result($x, 2) * (1 + $txRise);
    if(odbc_result($x, 5) == 1){
      $prMinOld = odbc_result($x, 6);
    }
    $pvigencia = odbc_result($x, 7);
  if ($juros) {
    $jrMinOld = $prMinOld * 0.04;
    $prMinOldTot = round (($prMinOld / 1.04), 0) ;
    $jrMinOld = $prMinOldTot * 0.04;
#    echo "<pre>$txRise,$prMinOld,$jrMinOld,$prMinOldTot</pre>";
  }

?>
<?php if($tipo=="natOper"){
		  require_once ("../endosso/natureza.php");
		?>
		<input type="hidden" name="tipo" value="natOper">
		  <div class="divisoria01"></div>
		  <h3>Endosso de Natureza da Opera&ccedil;&otilde;o</h3>
		  <ul>
		  <li class="campo3colunas">
				<label>Segurado</label>
				<?php echo $name; ?>
		  </li>
		  <li class="campo3colunas">
				<label>Data de Cria&ccedil;&atilde;o</label>
				<?php echo substr($bornDate, 8, 2). "/". substr($bornDate, 5, 2). "/". substr($bornDate, 0, 4); ?>
		  </li>
		  <li class="campo2colunas">
				<label>Solicitante</label>
				<?php echo $solicitante; ?>
		  </li>
		  </ul>

<?php }?>
      <ul>
	  <li class="campo2colunas">
            <label>Pr&ecirc;mio</label>
            Atual <?php echo number_format($prMinOldTot, 2, ',', '.'); ?>&nbsp;
            Novo <input style="width:33%;  text-align:right;" onBlur="prMinCalc()" onFocus="select()" name="prMin" value="0,00">
      </li>
      <li class="campo2colunas">
            <label>Taxa de Pr&ecirc;mio</label>
            Atual <?php echo number_format($txMinOld * 100, 3, ',', '.'); ?>&nbsp;
            Novo <input style="width:33%;  text-align:right;"  onBlur="checkDecimals2(this, this.value)" onFocus="select()" name="txMin" value="0,00">
      </li>
<?php if ($juros) {?>
          <li class="campo2colunas">
                <label>Cobertura de Juros de Mora</label>
                Atual <?php echo number_format($jrMinOld, 2, ',', '.'); ?>&nbsp;
                Novo <input style="width:33%  text-align:right;;" onFocus="this.form.txMax.focus()" name="jrMin" value="0,00">
          </li>
          
          <li class="campo2colunas">
                <label>Pr&ecirc;mio M&iacute;nimo Total</label>
                Atual <?php echo number_format($prMinOld, 2, ',', '.'); ?>&nbsp;
                Novo <input style="width:33%;  text-align:right;" onFocus="this.form.txMax.focus()" name="prMinTot" value="0,00">
          </li>
		  </ul>
<?php }
?>
  


<?php if($tipo=="natOper"){ ?>
  <ul>
  <li class="campo2colunas">
        <label>Setor</label>
    	Atual <?php echo $sector; ?>&nbsp;
    	Novo <?php echo $new_sector; ?>
  </li>
  <li class="campo2colunas">
        <label>Produtos</label>
    	Atual <?php echo $products; ?>&nbsp;
    	Novo <?php echo $new_natureza; ?>
  </li>
  </ul>
<?php }
?>
<?php if($idPremio != 0){ 
    $r = odbc_exec($db, "select motivo from EndossoPremio where id=$idPremio");
    $motivo = odbc_result($r, 1);

?>
<ul>
  <li class="campo2colunas">
        <label>Motivo</label>
    	<?php echo $motivo; ?>
  </li>
</ul>
<?php }
   }
?>

<?php $acao = isset($acao) ? $acao : ''; ?>
<?php if ($acao != "cancelar") {?>
<div class="barrabotoes">
	<button name="voltar" type="button" onClick="this.form.mot.value='Voltar';this.form.submit()"  class="botaovgm">Voltar</button>
    <button name="ok"	  type="button" onClick="this.form.mot.value='OK'; if(confvigencia(this.form)) this.form.submit();" class="botaoagm">OK</button>
    <button name="canc"	  type="button" onClick="this.form.acao.value='cancelar';if(cancelInf(this.form)) this.form.submit();" class="botaovgg">Cancelar Informe</button>
</div>

<?php echo "<p>".$msg."</p>";?>

<?php }
    if ($acao == "cancelar") {
			    $query = "UPDATE Inform SET state = 9 WHERE id='$idInform'";
				odbc_exec ($db, $query);
				
				$query = "UPDATE NotificationR SET state = 2, i_Usuario = ".$_SESSION["userID"].", d_Encerramento = GETDATE() WHERE id='$idNotification'";
				odbc_exec ($db, $query);
												
				//Gera Notificação de Cancelamento
				$geraNot = "select name from Inform where id='$idInform'";  
				$cur = odbc_exec($db, $geraNot);
				odbc_fetch_row($cur);
				$clientR = odbc_result($cur, 1);
				$notif->newInfCredito($userID, $clientR, $idInform, $db);
				
				$redirect = 1;

?>

<div id="entradageral">
  <div id="entrada">
  
      <p><img src="<?php echo $host;?>images/icone_usuario.png" alt="" width="36" height="44" /></p>
      <p><img src="<?php echo $host;?>images/texto_acessogprint.png" alt="" width="111" height="23" /></p>
      <span class="texto11">Entre com a sua senha <br />abaixo para cancelar definitivamente esse informe no sistema
      </span><br /><br />
     <?php
	      if ($_GET['erro'] == 1){   ?>
			<label style="color:#C00">Usuário ou senha inválidos!</label>        
      <?php			  
		  }else if ($_GET['erro'] == 2){ ?>
             <label style="color:#C00">Sua seção expirou!</label>      
      <?php
		  }
	  
	  ?>
      <label>Usu&aacute;rio</label>
              <input name="login" id="login" type="text" value="<?php echo $loginConf; ?>" onclick="confirmar.blur()"/>
      <label>Senha</label>
              <input name="senha" id="senha" type="password" onclick="this.focus()"/>
              <input name="validar" id="validar" type="hidden" value="login"/>
      <br />
      
      <button class="botaoagm" type="button" name="confirmar" id="confirmar" onClick="this.form.mot.value='Confirmar';this.form.submit()">Confirmar</button>
      <button class="botaovgm" type="button" name="voltar" id="voltar" onClick="this.form.mot.value='Voltar';this.form.submit()">Voltar</button>
      
      
      <!--
      <p><a href="#" onclick="javascript: if(validaFrm()) frm.submit();">
      <img src="images/botao_entrar.png" alt="" class="efeitobotao"/>
      
      </a></p> -->
  
  </div>
</div>

         
<?php } ?>
<input type="hidden" name="mensagem" value="<?php echo $redirect; ?>">

</form> 
<script>
if (<?php  echo $redirect; ?> == 1) {
redireciona(document.pr);
}
</script>
</div>