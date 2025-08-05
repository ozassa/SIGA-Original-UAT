<?php 
      include_once("../../consultaCoface.php");
?>
<?php 
  //HICOM, Atualizacao de aviso de VMI
  //Alterado Hicom 10/01/05 (Gustavo - inclusão de um novo perfil igual ao crédito, sem permissão de alteração)
?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

<link rel="stylesheet" type="text/css" media="all" href="<?php echo $host?>src/scripts/calendario/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/calendar.js"></script>
<script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/calendar-setup.js"></script>

<script language="JavaScript" src="<?php echo  $root;?>scripts/utils.js"></script>
<script language="javascript" >

function muda_endosso(b, inform, session, importer){
  if(b.checked){
    location = "/siex/src/role/credit/Credit.php?comm=import&idInform=" + inform + "&action=1&val=1&idImporter=" + importer;
  }else{
    location = "/siex/src/role/credit/Credit.php?comm=import&idInform=" + inform + "&action=1&val=0&idImporter=" + importer;
  }
}

function muda_calcPA(b, inform, session, importer){
  if(b.checked){
    location = "/siex/src/role/credit/Credit.php?comm=import&idInform=" + inform + "&action=2&val=1&idImporter=" + importer;
  }else{
    location = "/siex/src/role/credit/Credit.php?comm=import&idInform=" + inform + "&action=2&val=0&idImporter=" + importer;
  }
}

function checa(f){
  if(f.ci.value == '' || f.ci.value == 0) {
    verErro('CI n&atilde;o pode ser vazio e nem 0');
    f.ci.focus();
    return false;
  }
  return true;
}


function testaVMI(f)
{

  var wret = false;
  var cred = 0;

  if (f.STATE.value > 3)
  {
  	 if(f.credit.value!="")
	 {
	    cred = cred + parseFloat(f.credit.value);
	 }
  	 if(f.creditTemp.value!="")
	 {
	    cred = cred + parseFloat(f.creditTemp.value);
	 }

	 cred = cred * 1000;

	 cred = cred * (parseFloat(f.PERCCOVERAGE.value) / 100);

	 if(cred > parseFloat(f.VMI.value) && parseFloat(f.VMI.value) > 0)
	 {
	    if (confirm("ATENÇÃO! Crédito: US$ " + cred + ",00 é maior que VMI: US$ " + f.VMI.value + ",00! Deseja continuar a operação?"))
		{
		   wret = true;
		}
	 }
	 else
	 {
	     wret = true;
	 }
  }
  else
  {
      wret = true;
  }

  return wret;

}

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
	
	
function validaDat(campo,valor) {
	var date=valor;
	
	if (date.length > 0){
		var ardt=new Array;
		var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
		ardt=date.split("/");
		erro=false;
		if ( date.search(ExpReg)==-1){
			erro = true;
			}
		else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30))
			erro = true;
		else if ( ardt[1]==2) {
			if ((ardt[0]>28)&&((ardt[2]%4)!=0))
				erro = true;
			if ((ardt[0]>29)&&((ardt[2]%4)==0))
				erro = true;
		}
		if (erro) {
			verErro("\"" + valor + "\" n&atilde;o &eacute; uma data v&aacute;lida!!!");
			campo.focus();
			campo.value = "";
			return false;
		}
	}
	return true;
}


</script>

<?php  
if(! function_exists('getTimeStamp')){
  function getTimeStamp($date){
    if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
    }
    return 0;
  }
}



if(! function_exists('getValidityDate')){
  function getValidityDate($idBuyer){
    global $db;
    $x = odbc_exec($db, "select state, stateDate from ChangeCredit where idImporter=$idBuyer order by id desc");
    if(odbc_fetch_row($x)){
      $state = odbc_result($x, 1);
      if($state == 1){
	return getStrDate(substr(odbc_result($x, 2), 0, 10));
      }
    }
    return date("d/m/Y");
  }
}

if(! function_exists('getStrDate')){
  function getStrDate($str){
    $row = explode('-', $str);
    $ret = $row[2]. "/". $row[1] ."/". $row[0];
    if ($ret == '//')
      return '';
    return $ret;
  }
}
if($_REQUEST['idInform'])
    $idInform  = $_REQUEST['idInform'];

$query = "SELECT Inform.name, Inform.contrat, Inform.state, Inform.limPagIndeniz, Inform.prMTotal, Inform.percCoverage, 
          Inform.currency, Inform.prMin, Inform.warantyInterest 
          FROM Inform 
          WHERE Inform.id = ?";

$stmt = odbc_prepare($db, $query);
odbc_execute($stmt, [$idInform]);

$cc = $stmt;
odbc_free_result($stmt);

if(odbc_fetch_row($cc)){
  $nameCl   = odbc_result($cc, 1);
  $CiClient = odbc_result($cc, 2);
  $stateR   = odbc_result($cc, 3);


  //HICOM, para calculo do VMI
  $hcx_state = odbc_result($cc, 3);
  $hcx_limPagIndeniz = odbc_result($cc, 4);
  //$hcx_prMTotal = odbc_result($cc, 5);

  	 if (odbc_result($cc, 9)) {
		$juros =  odbc_result($cc, 8) * 0.04;
		$hcx_prMTotal = odbc_result($cc, 8)  + $juros;
	}else{
		$hcx_prMTotal = odbc_result($cc, 8);
	}

  $hcx_percCoverage = odbc_result($cc, 6);
}

//echo "  STATE  :" . $hcx_state . "    " . $idInform;
//die();

switch ($stateR) {
 case 1:
   $stateR = "Novo";
   break;
 case 2:
   $stateR = "Preenchido";
   break;
 case 3:
   $stateR = "Validado";
   break;
 case 4:
   $stateR = "Analisado";
   break;
 case 5:
   $stateR = "Tarifado";
   break;
 case 6:
   $stateR = "Proposta";
   break;
 case 7:
   $stateR = "Confirmado";
   break;
 case 8:
   $stateR = "Alterado";
   break;
 case 9:
   $stateR = "Cancelado";
   break;
 case 10:
   $stateR = "Vigente";
   break;
 case 11:
   $stateR = "Encerrado";
   break;
}

   $moeda =  odbc_result($cc, 7);
   
   if($moeda == "1"){
      $ext = "R$";
   }else if ($moeda == "2") {
     $ext = "US$";
   }else if ($moeda == "6") {
     $ext = "€";
   }

?>
<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
	<form action="<?php echo $root;?>role/credit/Credit.php" method="post" style="min-height:inherit !important">
		<ul>
			<li class="campo3colunas">
				<label>Segurado:</label>
    				<?php echo  ($nameCl);?>
			</li>

			<li class="campo3colunas">
				<label>DPP:</label>
    				<?php echo  ($CiClient);?>
			</li>

			<li class="campo3colunas">
				<label>Situa&ccedil;&atilde;o:</label>
    				<?php echo  ($stateR);?>
			</li>

    			<input type="hidden" name="comm" value="import">
    			<input type="hidden" name="submit" value="1">
<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
          <br clear="all" />
          
            <li class="campo3colunas">
                <label>Comprador:</label>
                <input type="text" name="name_query" value="<?php echo $name_query;?>">
            </li>

            <li class="campo3colunas">
            <label>Pa&iacute;s:</label>
                <select name="pais" id="pais">
                <option value="0">Selecione um Pa&iacute;s</option>
                <?php  
                    $strSQL = "SELECT * FROM Country order by name asc";
                        $cur = odbc_exec($db, $strSQL);

                        while(odbc_fetch_row($cur)) {
                            if ($pais == odbc_result($cur, "id")) {
                                    $selected = "selected";
                            }else{
                                    $selected = "";
                            }
                        ?>
                        <option value="<?php echo odbc_result($cur, "id");?>" <?php echo $selected;?> ><?php echo (odbc_result($cur, "name"));?></option>
                    <?php  }?>
            </select>
        </li>
		</ul>

		<div class="barrabotoes">
    			<button type="submit" name="ok_bt" class="botaoagm">Pesquisar</button>
		</div>
	</form>

	<table class="tabela01" width="100%" style="height:auto !important; min-height:inherit">
  		<thead>
      			<tr>
					<th width="5%">#</th>
        			<th width="35%">Comprador</th>
        			<th width="10%">CRS</th>
        			<th width="15%">Pa&iacute;s</th>
        			<th width="15%" style="text-align:right !important">Cr&eacute;dito Solicitado <?php echo $ext;?> Mil</th>
        			<th width="15%" style="text-align:right !important">Cr&eacute;dito Concedido <?php echo $ext;?> Mil</th>
                    <th width="8%" style="text-align:center !important">In&iacute;cio da Validade</th>
      			</tr>
  		</thead>

  		<tbody>
			<?php  $i = 0;

			/*
			//####### ini ####### adicionado por eliel vieira - elumini - 20/03/2008
			//
			// variavel $import tem sua origem no arquivo ../credit/importer.php
			//
			*/
			while(odbc_fetch_row($import)){
		  		$nameImporter = odbc_result($import, 1);
		  		$codeCountry  = odbc_result($import, 7);
		  		$ciImporter   = odbc_result($import, 4);
		  		$nameCountry  = odbc_result($import, 5);
		  		$idBuyer      = odbc_result($import, 6);
		  		$limCredit    = odbc_result($import, 2);
		  		$creditTemp   = odbc_result($import, 8);
		  		$limTemp      = odbc_result($import, 9);
		  		$state        = odbc_result($import, 10);
		  		$endosso      = odbc_result($import, 11);
		  		$idTwin       = odbc_result($import, 12);
		  		$hold         = odbc_result($import, 13);
		  		$calcPA       = odbc_result($import, 14);
		  		$limCredit    = number_format($limCredit / 1000, 0, ",", ".");
		  		$codAprovacao = odbc_result($import, "idAprov");
				$validityDate = odbc_result($import,'validityDate');
		
				$clss  = "background-color:#CCC";
				
		
		  		$i++;
		
			  	if(! $idTwin){
					$x = odbc_exec($db, "select id from Importer where idTwin=$idBuyer");
					$idTwin = odbc_result($x, 1);
			  	}
		
		  		$q = "SELECT ChangeCredit.credit, ChangeCredit.stateDate
					FROM ChangeCredit, Importer
					WHERE Importer.id = $idBuyer
					AND ChangeCredit.idImporter = Importer.id
					ORDER BY ChangeCredit.id DESC";
		
		  		$c = odbc_exec($db, $q);
		
		  		// print $q; 
		  		$manager = "0";
		  		$enabled = true;

			  	if (odbc_fetch_row($c)) {
				 	$ChangeCredit = odbc_result($c, 1) / 1000;
				 	$ChangeCredit = number_format($ChangeCredit, 0, ",", "");
				 	$manager = "1";

					if($state == 3 || $state == 4) {
						$enabled = false;
						$ChangeCredit = "Pend&ecirc;ncia $ChangeCredit";
					}else if ($state == 7) {
						$enabled = false;
						$ChangeCredit = "Cancelado $ChangeCredit";
					}else if ($state == 8) {
						$enabled = false;
						$ChangeCredit = "Recusado $ChangeCredit";
					}else if ($state == 9) {
						$enabled = false;
						$ChangeCredit = "Excluído $ChangeCredit";
					}else if($hold){
						$enabled = false;
						$ChangeCredit = "Executivo $ChangeCredit";
					}else if($status == 1 && !$hold && ! $ciImporter){
						$enabled = false;
						$ChangeCredit = "Notifica&ccedil;&atilde;o $ChangeCredit";
					}
			  	}else {
				   	if($hold){
					   	$enabled = false;
					   	$ChangeCredit = "Executivo";
				   	}else if($status == 1 && !$hold && ! $ciImporter){
					   	$enabled = false;
					   	$ChangeCredit = "Notifica&ccedil;&atilde;o";
				   	}else{
					   	$enabled = false;
					   	$ChangeCredit = "Pend&ecirc;ncia";
				   	}
			  	}
				
				?>
		
             		<tr class="odd">
						<td><?php echo $i;?></td>
						<td ><a href="<?php echo  $root;?>role/credit/Credit.php?comm=showBuyers&origem=1&idInform=<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');?>&idBuyer=<?php echo  $idBuyer;?>"><strong><?php echo $nameImporter;?></strong></a>
							<!--	
							<?php  if ($enabled && $state != 7 || $state == 3 || $state == 4)   { ?>							
										<form name="endosso"  style="width:auto !important; min-height:inherit !important">
											<div class="formopcao" style="height:auto !important;">
												<input type="checkbox" name="box" <?php  if (! $role['viewCredit']){ ?> OnClick="muda_endosso(this, <?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');?>, '', <?php echo  $idBuyer;?>)" <?php  } ?> <?php echo  ($endosso == 1 ? 'checked' : '') ?>>
											</div>Endosso do Banco do Brasil
										</form>
										
							<?php  } ?>
			   
							<?php  if ($role['tariffer']){ ?>
										<form name="calculo" style="width:auto !important; min-height:inherit !important">
												<div class="formopcao" style="height:auto !important;">
													<input type="checkbox" name="box" OnClick="muda_calcPA(this, <?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');?>, '', <?php echo  $idBuyer;?>)" <?php echo  ($calcPA == 1 ? 'checked' : '');?>>
												</div>Excluir o comprador do c&aacute;lculo da PA
										</form>
							<?php  } ?>
                               -->
						</td>
                     
			
						<td><?php echo  $ciImporter;?></td>
						<td><?php echo  $nameCountry;?></td>
						<td style="text-align:right !important;"><?php echo  $limCredit;?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td><?php 
							     if ($enabled && $state != 7) {
									if ($creditTemp != ""){
										$creditTemp = number_format ($creditTemp/1000, 0, ",", ".");
									}  ?>				
											  
										<form name="hccre<?php echo  $i;?>" action="<?php echo  $root;?>role/credit/Credit.php" method="post" onSubmit="return (confirm('Confirma alteração de limite de crédito?') && testaVMI(hccre<?php echo  $i;?>))"  style="width:auto !important; min-height:inherit !important">
											<input type="hidden" name="comm" value="changeManager">
											<input type="hidden" name="origem" value="1">
										 <ul>
											<li class="campo3colunas" style="width:auto !important; height:auto !important; min-height:inherit;"> 
												  <input type="text" style="width:100px; text-align:right !important;" name="credit" onBlur="checkDecimalsMil(this,this.value)" onClick="select()" value="<?php echo  $ChangeCredit == 0 ? '' : $ChangeCredit;?>">
											</li>
										 </ul>
									  	 
						 <?php }else{ 
							          echo $ChangeCredit;

						       } ?>
							   					 
						   </td>
                           <td>
                             <li class="campo3colunas" style="width:130px !important;text-align:center !important;"> 
									<input type="text" style="width:80px;" name="inicioValidade" id="inicioValidade" maxlength="10" value="<?php echo  ($validityDate == '' ? date('d/m/Y') : getStrDate(substr($validityDate,0,10)));?>" onKeyUp="mascara(this,data);" onblur="validaDat(this,this.value);">
                                    <img src="<?php echo $host; ?>images/icone_calendario.png" name="imginicioValidade" id="imginicioValidade" alt="" class="imagemcampo" />
                                    <script type="text/javascript">
                                        Calendar.setup({
                                            inputField     :    "inicioValidade",     	// id of the input field
                                            ifFormat       :    "dd/mm/y",      	// format of the input field
                                            button         :    "imginicioValidade",  	// trigger for the calendar (button ID)
                                            align          :    "Tl",           	// alignment (defaults to "Bl")
                                            singleClick    :    true
                                        });
                                    </script>
                            </li>
													
                           
                           </td>
						  </tr>
						  
						<?php  if ($enabled && $state != 7) {  ?>
									<tr class="odd">
									<td></td>
									<td colspan="6">						 
										   	     <ul>
                                                    <li class="campo3colunas" style="width:240px !important;"> 
															<label>Cr&eacute;dito Tempor&aacute;rio (Mil):</label>
															<input  type="text" style="width:180px;" name="creditTemp" onBlur="checkDecimalsMil(this,this.value)" onClick="select()" value="<?php echo  $creditTemp ? $creditTemp : '';?>">
													</li> 
													<li class="campo3colunas" style="width:240px !important;"> 
															<label>Data de Expira&ccedil;&atilde;o:</label>
															<input  type="text" style="width:120px;" name="limTemp" id="limTemp"  maxlength="10" value="<?php echo  $limTemp ? getStrDate(substr($limTemp, 0, 10)) : '';?>" onKeyUp="mascara(this,data);" onblur="validaDat(this,this.value);">
															<img src="<?php echo $host; ?>images/icone_calendario.png" name="imglimTemp" id="imglimTemp" alt="" class="imagemcampo" />
															<script type="text/javascript">
																Calendar.setup({
																	inputField     :    "limTemp",     	// id of the input field
																	ifFormat       :    "dd/mm/y",      	// format of the input field
																	button         :    "imglimTemp",  	// trigger for the calendar (button ID)
																	align          :    "Tl",           	// alignment (defaults to "Bl")
																	singleClick    :    true
																});
															</script>
													</li>
	
															   
														<?php  
														$hc_credito_total =  (odbc_result($c, 1) + odbc_result($import, 8)) * ($hcx_percCoverage/100);
														$hcx_VMI = 0;
							
														if ($hcx_state > 3){
															$hcx_VMI = $hcx_limPagIndeniz * $hcx_prMTotal;
															if ($hc_credito_total > $hcx_VMI && $hcx_VMI > 0 && $state = 6){
																?>
																<li class="campo3colunas"> 
																	<label>
																		<?php echo "ATEN&Ccedil;&Atilde;O! Cr&eacute;dito maior que VMI!" ?>
																	</label>
																</li>
														<?php  }
													}
			
										?>			<li class="campo3colunas" style="width:240px !important;"> 
														<label>C&oacute;digo de Aprova&ccedil;&atilde;o de Cr&eacute;dito:</label>
															<select name="codAprovacao" style="width:190px;" >                    
																	<option value="0"></option>
																	<?php  $strSQL = "SELECT * FROM tb_Aprovacao order by codigo";
																	$cur = odbc_exec($db, $strSQL);
						
																	while(odbc_fetch_row($cur)) {
																		if ( $codAprovacao == odbc_result($cur, "id") ) {
																				$selected = "selected";
																		}else{
																				$selected = "";
																		}
																		?>
																			<option value="<?php echo odbc_result($cur, 'id');?>" <?php echo ($selected);?> ><?php echo odbc_result($cur, 'codigo');?></option>
																	<?php  }
																	?>
															</select>
													</li>
			
												<?php  if(($tem_renov || $is_renov) && $idTwin){ ?>
														<input type="hidden" name="includeOld" value="1">
												<?php
													} ?>
					 
												<input type="hidden" name="idBuyer" 		value="<?php echo  $idBuyer;?>">
												<input type="hidden" name="idInform" 		value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');?>">
												<input type="hidden" name="VMI" 		value="<?php echo  $hcx_VMI;?>">
												<input type="hidden" name="PERCCOVERAGE" 	value="<?php echo  $hcx_percCoverage;?>">
												<input type="hidden" name="STATE" 		value="<?php echo  $hcx_state;?>">
											
											
											
											<?php
											 $sqi = " select top 1 Motivo_Decisao_1, Motivo_Decisao_2, Motivo_Decisao_3 from ChangeCredit where idImporter = ".$idBuyer." and state = 6 order by id desc";
											 $cur6 = odbc_exec($db, $sqi);
											 $Decisao1  =  odbc_result($cur6,'Motivo_Decisao_1'); 
											 $Decisao2  =  odbc_result($cur6,'Motivo_Decisao_2'); 
											 $Decisao3  =  odbc_result($cur6,'Motivo_Decisao_3'); 
											 
										   $sql = "Select
													MotivoDecisao.i_Item,
													Right('00000' + Cast(MotivoDecisao.i_Item As varchar), 5) As DescricaoMotivo
												From
													Campo_Item MotivoDecisao
												Where
													MotivoDecisao.i_Campo = 500
													And MotivoDecisao.Situacao = 0
												Order By
													MotivoDecisao.i_Item";
											
								?> 
										   <li class="campo3colunas" style="width:240px;">
											  <label>Decis&atilde;o 1</label>
											  <select name="Motivo_Decisao_1<?php echo $idBuyer;?>" style="width:190px;">
																<option value="0">Selecione</option>
													<?php 
														$cur1 = odbc_exec($db, $sql);
	
														while(odbc_fetch_row($cur1)) {  ?>
														<option value="<?php echo odbc_result($cur1, 'i_Item');?>" <?php echo ($Decisao1 == odbc_result($cur1, 'i_Item') ? 'selected' : '');?> ><?php echo odbc_result($cur1, 'DescricaoMotivo');?></option>
													<?php  }
																?>
											 </select>
											 </li>
											  <li class="campo3colunas" style="width:240px;">
											  <label>Decis&atilde;o 2</label>
												  <select name="Motivo_Decisao_2<?php echo $idBuyer;?>" style="width:190px;">
																	<option value="0">Selecione</option>
														<?php 
															$cur2 = odbc_exec($db, $sql);
		
															while(odbc_fetch_row($cur2)) {  ?>
															<option value="<?php echo odbc_result($cur2, 'i_Item');?>" <?php echo ($Decisao2 == odbc_result($cur2, 'i_Item') ? 'selected' : '');?> ><?php echo odbc_result($cur2, 'DescricaoMotivo');?></option>
														<?php  }
																	?>
												 </select>
											 </li>
											  <li class="campo3colunas" style="width:240px;">
											  <label>Decis&atilde;o 3</label>
												  <select name="Motivo_Decisao_3<?php echo $idBuyer;?>" style="width:190px;">
														<option value="0">Selecione</option>
														<?php 
															print 
															$cur6 = odbc_exec($db, $sql);
		
															while(odbc_fetch_row($cur6)) {  ?>
															<option value="<?php echo odbc_result($cur6, 'i_Item');?>" <?php echo ($Decisao3 == odbc_result($cur6, 'i_Item') ? 'selected' : '');?> ><?php echo odbc_result($cur6, 'DescricaoMotivo');?></option>
														<?php  }
																	?>
												 </select>
											 </li>
											
											
									 
										<li class="campo3colunas" style="width:80px;">
											  <label>&nbsp;</label>
											 <?php  if ( $role['creditManager']){ ?>   
														<button type="submit" name="alterar_bt" class="botaoapm">Alterar</button>
											<?php  } ?>
									   </li>
									  </ul>
									  
									</form>
						<?php  } else {
	
										 if($state == 8){ // se o status for 8 (recusado), coloca botao de voltar pro informe
										   ?>
									  		 <ul>	   
												 <form action="<?php echo  $root;?>role/credit/Credit.php" method="post" onSubmit="return checa(this)"  style="width:auto !important; min-height:inherit !important">
															<input type="hidden" name="comm" value="estudo">
															<input type="hidden" name="idBuyer" value="<?php echo  $idBuyer;?>">
															<input type="hidden" name="name_query" value="<?php echo  $name_query;?>">
															<input type="hidden" name="submit" value="1">
															<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');?>">
															<div style="clear:both">&nbsp;</div>
								   
															<li class="campo3colunas"> 
																<label>CI <?php echo $nomeEmp; ?>:</label>
																<input type="text" name="ci" value="<?php echo  $ciImporter;?>">
															</li>
							
											<?php  if ($role['creditManager']){ ?>
													<button type="submit" class="botaoagg">Solicitar Estudo</button>
											<?php  } ?>
											   </form>
										
									<?php  }  ?>
									
									  </ul>
								
					     <?php
							} 
						?>	
						 </td>
				   </tr>
                  
                   <tr style="<?php echo  $clss;?>; height:12px;">
                       <td colspan="7">&nbsp;</td>
                    </tr>		
					
					
					
			<?php } // fecha wilhe

  			if ($role["creditManager"]){  ?>
     				</table>
        		
        				<form action="<?php echo  $root;?>role/credit/Credit.php" method="post" style="min-height:inherit !important">
         					<input type="hidden" value="notif" name="comm">
        					<div class="barrabotoes">
    <button type="submit" name="ok_bt" class="botaoagg">Notifica&ccedil;&otilde;es</button>
    <button type="button" class="botaovgm" onClick="window.location = '../searchClient/ListClient.php?comm=view&idInform=<?php echo urlencode($_REQUEST['idInform']); ?>';">Voltar</button>
</div>

        				</form>
        		

				<?php
  			}else{  ?>
      				</table>
				<?php 
  			}  
  
  			if ($msg != ''){ ?>
          			<script> verErro('<?php echo $msg;?>'); </script>
  				<?php 
     				$msg  = '';
  			} ?>  
  
    			<form action="<?php echo  $root;?>role/searchClient/ListClient.php">
    				<input type="hidden" name="comm" value="view">
    				<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');?>">
    			</form>

    			<?php  if (! $role["creditManager"]){  ?> 
                		<div class="barrabotoes">
    <button type="button" class="botaovgm" onClick="window.location = '../searchClient/ListClient.php?comm=view&idInform=<?php echo urlencode($_REQUEST['idInform']); ?>';">Voltar</button>
</div>

     			<?php  } ?>           
</div>