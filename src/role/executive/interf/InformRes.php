<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
     
<?php  //Alterado HiCom mes 04



  if(!$idInforme){
	   $idInform = $_REQUEST['idInform'];  
  }
  
  
  
  odbc_exec ($db, "UPDATE Inform SET financState = 3 WHERE id = $idInform");
  
  $cur=odbc_exec($db,"SELECT generalState, volState, segState, financState, buyersState, lostState, bornDate, respName, ocupation, state, idAnt, pvigencia, currency,Periodo_Vigencia FROM Inform WHERE id = $idInform");
 
  if (odbc_fetch_row($cur)) {
    $ok = 1;
    $i = 1;
    $test = 0;
    $state = odbc_result ($cur, 'state');
    $idAnt = odbc_result($cur, 11);
    $vigencia = odbc_result($cur, 12);
	$Periodo_Vigencia   = = odbc_result($cur, 'Periodo_Vigencia');
	
    if ($vigencia==""){
         $pvigencia="12 meses";
    }else if($vigencia=="1"){
         $pvigencia="12 meses";
    }else{
         $pvigencia="24 meses";
    }

    if($Periodo_Vigencia){
	   	$pvigencia = $Periodo_Vigencia ." Meses";
	}
	
    $tmoeda = odbc_result($cur, 13);
    if($tmoeda == "1"){
		$moeda = "Real";
	}else if ($tmoeda == "2") {
       $moeda = "Dollar";
    }else{
       $moeda = "Euro";
    }

    /*
    if ($role["client")) 
	     $test = 2;
    else 
	     $test = 3;
	*/
	
	if ($role["executive"]) 
	     $test = 3;
    else 
	     $test = 2;	 
		 

    for (;$i<=6;$i++) {
      if (odbc_result($cur,$i) != $test) 
	      $ok = 0;
    }
    if ($i == 1) $ok = 0;
?>
<?php  require_once("client.php") ?>

<h2>Observa&ccedil;&otilde;es</h2>
  	<li class="campo3colunas">
    	<label>Data de Cadastro</label>
        <?php  $data = odbc_result($cur,7); ?><?php   echo substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);?>
    </li>
    <li class="campo3colunas">
    	<label>Nome do Respons&aacute;vel</label>
        <?php   echo (odbc_result($cur, 8));?>
    </li>
    <li class="campo3colunas">
    	<label>Cargo</label>
		<?php   echo (odbc_result($cur, 9));?>
    </li>
    
    <div class="barrabotoes">
        <form action="../executive/Executive.php" method="post">
        <input type="hidden" name="comm" value="done">
        <input type="hidden" name="mot" value="">
        <input type="hidden" name="idInform" value="<?php   echo $idAnt && $reestudo ? $idAnt : $idInform;?>">
        <input type="hidden" name="idNotification" value="<?php   echo $idNotification;?>">
        <?php  if ($msg) { ?>							   
        	<p><?php   echo $msg;?></p>
        <?php  } ?>
        <button name="voltar" onClick="this.form.mot.value='Voltar';this.form.submit()" class="botaovgm">Voltar</button>
       
        <?php  if ($ok) { ?>
        <input type="hidden" name="vig" value="<?php   echo $vig;?>" class="servicos">
        <input type="hidden" name="sisseg" value="<?php   echo !$vig;?>" class="servicos">
        <?php  if ($state == 1) { ?>
        <button name="ok" onClick="this.form.mot.value='OK';this.form.submit()" class="botaagm">OK</button>
        <?php  } else {?>
        <button name="aceitar" onClick="this.form.mot.value='Aceitar';confvigencia(this.form)" class="botaoagm">Aceitar</button>
        <button name="recusar" onClick="this.form.mot.value='Recusar';confirma(this.form)" class="botaovgm">Recusar</button>
        <?php  } ?>
        <?php  } ?>
        </form>
        <?php  } else {
        ?>
        Informe Inv&aacute;lido
        <?php  }
        ?>
     </div>
</div>

<script language=javascript>

function confvigencia(c){
 if (confirm('Você confirma a vigência de <?php  echo $pvigencia; ?>\n'
     + 'e o tipo de moeda <?php   echo $moeda;?> ?')){
     c.submit();
    return true;
 }else{
   return false;
 }
}
function confirma(f){
  if(confirm('Confirma a recusa do Informe?')){
    f.submit();
  }
}
</script>
