<?php  
 //alterado hicom em 08/04/2004
 //alterado gpc
 //Alterado Hicom (Gustavo) 25/01/2005 - inclus�o do campo divulgaNome e emailContato


if($field->getField("action"))
	$action           = $field->getField("action");
if($field->getField("idBuyer"))
	$idBuyer          = $field->getField("idBuyer");
if($field->getField("idInform"))
	$idInform         = $field->getField("idInform");  
if($field->getField("tipo"))
	$tipo             = $field->getField("tipo"); 
if($field->getField("importerName"))
	$importerName     = $field->getField("importerName");
if($field->getField("importerCountry"))   
	$importerCountry  = $field->getField("importerCountry"); 
if($field->getField("idImp"))
	$idImp             = $field->getField("idImp");




$qry = "SELECT limCredit, credit FROM Importer WHERE id = $idImp or id = $idBuyer";
$aux = odbc_exec($db, $qry);						
if(odbc_fetch_row($aux)){
	
	$importerCredit   = number_format(odbc_result($aux,'limCredit'), 0, ",", ".");
    $changeCredit     = number_format(odbc_result($aux,'credit'), 0, ",", "."); 
	
}

//print $qry. $importerCredit;

$divulgaNome      = $field->getField("divulgaNome"); 





  //Alterador por Tiago V N - Elumini - 05/06/2006
  $query = "Select currency From Inform Where id = $idInform";
  $cur = odbc_exec ($db, $query);
    if (odbc_fetch_row ($cur)){
      $nMoeda = odbc_result($cur, "currency");
       if ($nMoeda == "1") {
          $extMoeda = "R$";
       }elseif ($nMoeda == "2") {
          $extMoeda = "US$";
       }
       else if ($nMoeda == "6") {
          $extMoeda = "euro;";
       }
       
	   
    }

?>
<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
<script language="JavaScript" src="<?php echo $root;?>scripts/utils.js"></script>
<script Language="JavaScript">
<!--
function novoValidaEmail(mail){
var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
    if(typeof(mail) == "string"){
       if(er.test(mail)){
           return true;
       }
    }
    else if(typeof(mail) == "object"){
       if(er.test(mail.value)){
          return true;
       }
    }
    else if ((indexOf('.',mail) == 0)|| (indexOf('@',mail) > 1))
       return false
    else{
       return false;
    }
}

function checa_email_contact(form){
  valmail = form.emailContato.value;
  if(! novoValidaEmail(valmail)){
     form.emailContato.focus();
     verErro("O e-mail do Contato &eacute; Inv&aacute;lido!");
     return false;
  }
  else{
      form.emailContato.value = valmail.toLowerCase();
  }
}

function confirma_alter(alterar){
  if(confirm ("Confirma alteração\nCrédito Solicitado= <?php  echo $extMoeda;?> " + alterar.edit.value + "")) {
    return (true);
  } else {
    return (false);
  }
}

function TrataCase(flcase) 
{
   if ((window.event.keyCode >= 65 && window.event.keyCode <= 90) || (window.event.keyCode >= 192 && window.event.keyCode <= 220))
   {  if (flcase == "L")
      {  window.event.keyCode = window.event.keyCode + 32;
	  }
   }
   else
   {  if ((window.event.keyCode >= 97 && window.event.keyCode <= 122) || (window.event.keyCode >= 224 && window.event.keyCode <= 252))
      {  if (flcase == "U")
         {  window.event.keyCode = window.event.keyCode - 32;
	     } 
      }
   }	  
}


function AllowOnly(Expression)
{
	if (document.all)
	{
		Expression = Expression.toLowerCase();
		Expression = Expression.replace('a..z', 'abcdefghijklmnopqrstuvwxyz');
		Expression = Expression.replace('0..9', '0123456789');
		Expression = Expression.replace('|', '');
		var ch = String.fromCharCode(window.event.keyCode);
		ch = ch.toLowerCase();
		Expression = Expression.toLowerCase();
		var a = Expression.indexOf(ch);
		if (a == -1) 
			window.event.keyCode = 0;
	}
}
//-->
</script>
<script language=javascript>

var hc_receber = true;

function testaLimite()
{

var hc_prevExp12 = 0;
var hc_numShip12 = 0;
var hc_periodicity = 0;
var hc_przPag = 0;
var hc_limCredit = 0;
var hc_limCreditCalc = 0;

   hc_prevExp12 = document.frm_altera.prevExp12.value;
   hc_numShip12 = document.frm_altera.numShip12.value;
   hc_periodicity = document.frm_altera.periodicity.value;
   hc_przPag = document.frm_altera.przPag.value;
   hc_limCredit = document.frm_altera.limCredit.value;

   //verErro("a");

   if  (hc_prevExp12 == 0 || hc_numShip12 == 0 || hc_periodicity == 0 || hc_przPag == 0)
   {
      return true;
   }
   else
   {
      // calcula formula
	  hc_limCreditCalc = parseInt(  (hc_prevExp12/hc_numShip12) * ((hc_przPag / hc_periodicity) + 1)  );
	  if (hc_limCreditCalc > hc_prevExp12)
	  {
	     hc_limCreditCalc = hc_prevExp12;
	  }
	  if (hc_limCredit != hc_limCreditCalc)
	  {
	     if (hc_limCredit != "")
		 {

	     if (hc_receber)
		 {


	        if (confirm("O Limite de credito sugerido pelo sistema é: <?php  echo $extMoeda;?>" + hc_limCreditCalc + " . Deseja substituir o informado pelo sugerido?\r\n\r\nOK = Utiliza credito sugerido.\r\nCANCELAR = Não Utiliza o credito sugerido.") )
		    {
		       document.frm_altera.limCredit.value = hc_limCreditCalc;
		    }
		    else
		    {
		       if (!confirm("Deseja receber este tipo de informação durante o processo?\r\n\r\nOK = Continua recebendo sugestões de crédito calculado pelo sistema.\r\nCANCELAR = Não recebe sugestões.") )
			   {
			      hc_receber = false;
			   }
		    }

		 }
		 }
	  }

   }

}



function checkDecimals(fieldName, fieldValue) {

  if (fieldValue == "") {
    verErro("Preenchimento obrigat&oacute;rio.");
    fieldName.select();
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
          fieldName.select();
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
            fieldName.select();
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
            fieldName.select();
            fieldName.focus();
            err = true;
          } else {
            v += c;
          }
        }
      }
      fieldValue = "," + v.substring (v.length - 2, v.length);
      v = v.substring (0, v.length - 2);
      while (v.length > 0) {
        t = v.substring (v.length >= 3 ? v.length - 3 : 0, v.length);
        v = v.substring (0, v.length >= 3 ? v.length - 3 : 0);
        fieldValue = (v.length > 0 ? "." : "") + t + fieldValue;
      }
      fieldName.value = fieldValue;
    }
  }
}

function consist (form) {
  msg = "";
  if (form.nameImporter.value == "") {
    msg += "Razão Social\n";
  }
  if (form.cnpj.value == "") {
   msg += "Registro fiscal do comprador\n";
  }
  if (form.address.value == "") {
    msg += "Endereço\n";
  }
  if (form.city.value == "") {
    msg += "Cidade\n";
  }
  if (form.cep.value == "") {
    msg += "CEP\n";
  }
  if (form.tel.value == "") {
    msg += "Telefone\n";
  }
  if (form.fax.value == "") {
    msg += "Fax\n";
  }
  if (form.contact.value == "") {
    msg += "Contato\n";
  }
  if (form.prevExp12.value == "") {
    msg += "Previsão de Venda\n";
  }
  if (form.limCredit.value == "") {
    msg += "Limite de Crédito\n";
  }
  if (form.numShip12.value == "") {
    msg += "Número de Embarques\n";
  }
  if (form.periodicity.value == "") {
    msg += "Periodicidade\n";
  }
  if (form.przPag.value == "") {
    msg += "Prazo de Pagamento\n";
  }
  if (msg != "") {
    verErro("Favor preencher as seguintes informações:\n"+ msg);
    return false;
  } else {
    return true;
  }
}

//******************************************************************************************
//Development by Julio Cezar da Silva (julio_cs@terra.com.br/julio@southern.com.br)
//http://planeta.terra.com.br/informatica/iportal
//******************************************************************************************
	function caracteres_invalidos(){
		var strinvalido
		strinvalido = '!"#$%&\\\\()*+,-./:;<=>?@'
		//strinvalido+= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
		strinvalido+= '��������������������������������������������'
		strinvalido+= '[\\\\]^_`'
		strinvalido+= '{|}~'
		strinvalido+= "'"
		return strinvalido
	}
    //Funcao que faz a verificacao do campo
	function verifica_name (form)
	{
		var strlogin = form.nameImporter.value; //Recebe o valor do campo
		var caracteres = caracteres_invalidos(); //recebe a string com caracters invalidos
		var result = true;
		for (i = 0; i < caracteres.length;i++) //loop executado de 0 ao numero total de caracters invalidos
		{
			if(strlogin.indexOf(caracteres.charAt(i)) != -1) //verifica se o value do campo strlogin contem alguma caracter invalido
			{
				var strerror = caracteres.substring(i,i+1); //recebe o caracter errado
				var result = false;
				//window.verErro("Voc� digitou o seguinte caracter invalido:" + "  " + strerror + "  "); //alerta mostrando o caracter digitado errado
				window.verErro("Você digitou nome do comprador com caracter invalido"); //alerta mostrando o caracter digitado errado
				form.nameImporter.value = '';
				form.nameImporter.focus();
				break; //interrompe o loop
			}
		}
		if (result){
			return true;
		}else{
			return false;
		}
	}

</script>
<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
  <?php
		
	  //Alterador por Tiago V N - Elumini - 05/06/2006
	$query = "Select name, state, idAnt, state, currency From Inform Where id = $idInform";
	$cur = odbc_exec ($db, $query);
		if (odbc_fetch_row ($cur)){
			  $nameCl = odbc_result($cur, 1);
			  $stateCl = odbc_result($cur, 2);
			  $idAnt = odbc_result($cur, 3);
			  $inform_state = odbc_result($cur, 4);
			  $nMoeda = odbc_result($cur, 5);
			  if ($nMoeda == "1") {
				 $extMoeda = "R\$";
			  }else if ($nMoeda == "2") {
				 $extMoeda = "US\$";
			  }else if ($nMoeda == "6") {
				 $extMoeda = "&euro;";
			  }
		
		}
		
		if($idAnt){ 
		    $idVigente = $idAnt;
		    $pergunta = '';    //"Incluir importador na ap�lice vigente?";
		}else{                 // verifica se este informe tem uma renovacao ativa
		   $x = odbc_exec($db, "select id, state from Inform where idAnt=$idInform");
		  if(odbc_fetch_row($x)){
			$idAnt = odbc_result($x, 1);
			$stateOther = odbc_result($x, 2);
			if($stateOther >= 1 || $stateOther <= 6){
			  $pode_incluir = 1; // significa q pode incluir no outro informe
			}
		  }
		  $idVigente = $idInform;
		  $pergunta = "Incluir comprador na apólice de renovação?";
		}
		//print $action;
		//break;
		if($action == 'approve'){
		  if ($stateCl == 10){
			$r = $notif->clientChangeImporter($userID, $nameCl, $idInform, $db, 10, "i", $importers[$i]);
		  }
		  $msg = "Solicitação será enviada para análise de crédito";
		  $action = 'include';
		}
		
		if(odbc_fetch_row($c)){
		  $contrat = odbc_result($c, 1);
		  $nameCl  = odbc_result($c, 2);
?>
  <FONT size=2 color="#cccccc">
  <?php  echo $nameCl?>
  <br>
  Ci Coface =
  <?php  echo $contrat;?>
  </font>
  <?php   } // if(odbc_fetch_row($c))  ?>
 
 <script type="text/javascript">
      function popup_Clear(){
		  if(document.getElementById('popupClear').style.display == 'block'){
			  document.getElementById('popupClear').style.display = 'none'; 
		  }else{
			  document.getElementById('popupClear').style.display = 'block'; 
		  }
	  }
 </script>
<div id="popupClear" class="popup-alter" style="display:<?php if($tipo) echo 'block'; else echo 'none';?>;">
	<?php          
		if ($tipo == "alterar"){   ?>
       	    <form id="limcred" name="limcred" action="<?php echo $root;?>role/client/Client.php" method="post"  onSubmit="return confirma_alter(this);">
            <input type="hidden" name="comm" id="comm" value="alterLim">
            <input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
            <input type="hidden" name="idImporter" value="<?php  echo $idImp;?>">
            <label><h2>Altera&ccedil;&atilde;o de Limite de Cr&eacute;dito</h2></label>
			<ul>		
                <li class="campo2colunas">
                	<label>Comprador:</label>
                	<?php  echo $importerName;?>
                </li>
                <li class="campo2colunas">
                	<label>Pa&iacute;s:</label>
                	<?php  echo $importerCountry;?>
                </li>
                <li class="campo2colunas">
                	<label>Cr&eacute;dito Solicitado&nbsp;</label>
                	<?php  echo $extMoeda;?>:&nbsp;
                	<?php  echo $importerCredit;?>
                </li>
                <li class="campo2colunas">
                	<label>Cr&eacute;dito Concedido</label>
                	<?php  echo $extMoeda;?>:&nbsp;
	                <?php  echo $changeCredit;?>
                </li>
                <li class="campo2colunas">
                	<label>Autoriza divulgar nome ao Comprador:</label>
                	<INPUT type="hidden" name="divulgaNomeOrig" value="<?php  echo $divulgaNome;?>">
                	<?php    if ($divulgaNome == 1) {  ?>
                	<input type="hidden" name="divulgaNome" value="1">
                	<?php  echo("Sim");
                		}else
					{ ?>
                	<div class="formopcao">
                		<input type="radio" name="divulgaNome" value="1" <?php  echo (($divulgaNome == 1) ? "checked" : "");?>>
                	</div>
                	<div class="formdescricao">Sim</div>
                	<div class="formopcao">
                		<input type="radio" name="divulgaNome" value="0" <?php  echo (($divulgaNome == 0) ? "checked" : "");?>>
                	</div>
                	<div class="formdescricao">N&atilde;o</div>
                	<?php    } 	?>
                </li>
                <!-- Fim alterado Hicom (Gustavo) -->
                <li class="campo2colunas">
                    <label>Solicitar <?php  echo $extMoeda;?></label>
                    <input type="hidden" name="concedido" value=<?php  echo $changeCredit;?>>
                    <INPUT type="text" onBlur="checkDecimalsMil(this,this.value)" name="<?php  echo "edit";?>" value="" style="width:150px; text-align:right;">
                    <input type="hidden" name="importer" value=<?php  echo $idImp;?>>
              	</li>
                </ul>
                <?php  if($pode_incluir && $pergunta){ ?>
                <input type="hidden" name="renovacao" value="1">
                <?php  } ?>

				<input type="hidden" name="idVigente" value=<?php  echo $idVigente;?>>
				<div class="barrabotoes" style="width:700px;">
					<button class="botaoagm" type="button"  onClick="this.form.submit()">Alterar</button>
					<button class="botaoagm" type="button"  onClick="popup_Clear()">Cancelar</button>
				</div>     
            </form>
     	<?php   }     ?>
  		</div>
		<?php
			if($action == 'exclude'){ ?>
                <label>Compradores Ativos</label>
                <p>&nbsp;</p>
                <label> <?php echo 'Aten&ccedil;&atilde;o: Para remover um Comprador, clique no link "Remover este comprador" e este ser&aacute; exclu&iacute;do. Para consultar endere&ccedil;os adicionais de um Comprador, clique no link correspondente. Para alterar os limites de cr&eacute;dito, clique no link " Alterar Limite de Cr&eacute;dito".';?> </label>
                <div style="clear:both">&nbsp;</div>
                <ul>
                <li class="campo2colunas">
				<?php
                    $sql = "SELECT Importer.name
                            FROM Importer, Inform, Country
                            WHERE Importer.idInform = ".$idInform."  AND Importer.state <> 7 AND Importer.state <> 8 AND
                            Importer.idInform = Inform.id AND
                            Importer.idCountry = Country.id AND
                                Importer.state <> 1 AND Importer.state <> 3 AND 
                                Importer.state <> 4 AND Importer.state <> 9 
                            ORDER BY Importer.name";
                    $verif = odbc_exec($db,$sql);
                    $linha = odbc_fetch_row($verif);
                    
                         if($linha){ ?>
                            <label><a href="relimportador.php?idInform=<?php  echo $idInform;?>" target="_blank">Imprimir Rela&ccedil;&atilde;o de Compradores</a></label>
                   <?php }else{   ?>
                            <label><a href="#"  onClick="verErro('N&atilde;o existem compradores para serem listados'); return false;">Imprimir Rela&ccedil;&atilde;o de Compradores</a></label>
                 <?php   }   ?>
                </li>
                </ul>
                <div style="clear:both">&nbsp;</div>
                <!-- início de um importador -->
                <?php  
                     
                      $sql = "     SELECT Importer.name, Importer.address, Importer.risk,
                                    Importer.city, Country.name as Pais, Importer.tel, Importer.prevExp12,
                                    Importer.limCredit, Importer.numShip12, Importer.periodicity,
                                    Importer.przPag, Importer.id, Importer.cep, Importer.fax, Importer.contact,
                                    Importer.emailContato, Importer.divulgaNome, Importer.id, Importer.cnpj, Importer.relation
                             FROM 
                                    Importer, Inform, Country
                             WHERE  
                                    Importer.idInform = $idInform AND 
                                    Importer.idInform = Inform.id AND
                                    Importer.idCountry = Country.id  
                                    AND Importer.state <> 7 AND Importer.state <> 8 AND Importer.state <> 1 AND Importer.state <> 3 AND  Importer.state <> 4 AND Importer.state <> 9
                            ORDER BY Importer.name";
                      
                      $cur = odbc_exec($db,$sql);
                            //tava dentro do WHERE : Importer.id not in (select distinct idImporter from ImporterRem)
                      $i = 0;
            
                ?>
                <table summary="Submitted table designs" class="tabela01">
                  <thead>
                    <Th colspan="1" align="center" scope="col">Raz&atilde;o Social</th>
                    <Th colspan="1" align="center" scope="col">Pa&iacute;s</th>
                    <Th colspan="1" align="center" scope="col">Altoriza divulgar Nome ao Comprador?</th>
                    <Th colspan="3" align="center" scope="col">Op&ccedil;&otilde;es</th>
                  </thead>
                <tbody>
      <?php
				  while (odbc_fetch_row($cur)) {
						$i++;
						$idBuyer = odbc_result($cur,12);
						
						if ($i % 2 == 0) 
						   $odd = 'class="odd"';
						else
						   $odd = "";
						   
						   /* 
						   $aux = odbc_exec($db, "SELECT credit FROM ChangeCredit ch WHERE ch.id IN (SELECT max (id) 
												  FROM ChangeCredit where idImporter = $idBuyer GROUP BY idImporter)");						
						   if(odbc_fetch_row($aux)){
						       $changeCredit  = odbc_result($aux,'credit');
						   }
						   */
						   ?>
                          <form action="<?php  echo $root;?>role/client/Client.php" method="post" name="alterar2" style="height:20px !important;">  
                          	<tr <?php  echo $odd;?>>
                            	<td><?php  echo odbc_result($cur,1);?></td>
                            	<td><?php  echo odbc_result($cur,5);?></td>
                            	<td width="270" style="text-align:center; height:20px" >
                            
                                	<input type="hidden" name="comm" value="alterDivulga">
                                	<input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
                                	<input type="hidden" name="idImporter" value="<?php  echo odbc_result($cur,"id");?>">
                                	<INPUT type="hidden" name="divulgaNomeOrig" value="<?php  echo odbc_result($cur,"divulgaNome");?>">
                                	<?php 
					if (odbc_result($cur,"divulgaNome") == 1) {
                                ?>
                                        <input type="hidden" name="divulgaNome" value="1">
                                        <?php  echo("Sim");
                                    } else {   ?>
                                    <div style="width:220px; text-align:center;">
                                      <div class="formopcao">
                                        <input type="radio" name="divulgaNome" value="1" <?php  echo ((odbc_result($cur,"divulgaNome") == 1) ? "checked" : "");?>>
                                      </div>
                                      <div class="formdescricao">Sim</div>
                                      <div class="formopcao">
                                        <input type="radio" name="divulgaNome" value="0" <?php  echo ((odbc_result($cur,"divulgaNome") == 0) ? "checked" : "");?>>
                                      </div>
                                      <div class="formdescricao">N&atilde;o</div>
                                      <button class="botaoapm" type="button"   onClick="this.form.submit();">Alterar</button>
                                    </div>
            <?php                  }
							?>
                            
                            </td>
                                <td><a href="<?php  echo $root;?>role/client/Client.php?comm=alterImporter&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>&tipo=alterar&idImp=<?php  echo $idBuyer;?>&importerName=<?php echo odbc_result($cur,1);?>&importerCountry=<?php echo odbc_result($cur,5);?>&divulgaNome=<?php echo odbc_result($cur,"divulgaNome");?>&importerCredit=<?php echo odbc_result($cur,'limCredit');?>#cad"><img src="<?php echo $root;?>images/icone_editar.png" alt="" title="Alterar Limite de Cr&eacute;dito" width="16" height="16" class="iconetabela"/></a></td>
                                <td><a href="<?php  echo $root;?>role/client/Client.php?comm=addAddress&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>"><img src="<?php echo $root;?>images/icone_detalhar.png" alt="" title="Consultar Endere&ccedil;os" width="16" height="16" class="iconetabela"/></a></td>
                                <td><a href="<?php  echo $root;?>role/client/Client.php?comm=remove&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>" onClick="return confirm('Remover o comprador <?php  echo odbc_result($cur,1);?>?')"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover este comprador" width="16" height="16" class="iconetabela"/></a></td>
                            </tr>
                            </form>
      <?php     } // while
		
		
                   if ($i == 0) {  ?>
      <TR >
        <TD align="center" colspan="6"><label>Nenhum Comprador Cadastrado</label></TD>
      </TR>
      <?php } ?>
    </TBODY>
  </TABLE>
  <div style="clear:both">&nbsp;</div>
  <form action="<?php  echo $root;?>role/client/Client.php"  method="get">
    <input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
    <input type="hidden" name="comm" value="insertImporter">
    <div class="barrabotoes">
      <button class="botaovgm" type="button"  onClick="this.form.comm.value='open';this.form.submit()">Voltar</button>
    </div>
  </form>
  <!--Alteração de Limite de Crédito-->
  <?php 

	    }else if($action == 'include' || $action == 'alter' ){
			
$idBuyer = $_REQUEST['idBuyer'];

if (!is_numeric($idBuyer)) {
    die('Invalid input');
}

$sql = "SELECT * FROM Importer WHERE id = ?";
$stmt = odbc_prepare($db, $sql);
odbc_execute($stmt, [$idBuyer]);

$cur = $stmt;
odbc_free_result($stmt);             
			$nameImporter       = odbc_result($cur,'name');		
			$cnpj               = odbc_result($cur,'cnpj');
			$address            = odbc_result($cur,'address');
			$city               = odbc_result($cur,'city');
			$cep                = odbc_result($cur,'cep');
			$hc_Twin            = odbc_result($cur,'hc_Twin');
			$tel                = odbc_result($cur,'tel');
			$fax                = odbc_result($cur,'fax');
			$contact            = odbc_result($cur,'contact');
			$emailContato       = odbc_result($cur,'emailContato');
			$divulgaNome        = odbc_result($cur,'divulgaNome');            
			$relation           = odbc_result($cur,'relation');
			$prevExp12          = odbc_result($cur,'prevExp12'); 
			$numShip12          = odbc_result($cur,'numShip12');
			$periodicity        = odbc_result($cur,'periodicity');
			$przPag             = odbc_result($cur,'przPag');
			$limCredit          = odbc_result($cur,'limCredit');
		   
		   $i = 0;
		  
		
			
		
		   //HiCom, verificar se tem twin, se tiver, nao deixar colocar altarar
		
		   $hc_cur = odbc_exec($db, "SELECT a.idTwin FROM Importer a, Inform b where b.state < 10 and b.id = a.idInform and a.id = $idBuyer");
		   if (odbc_fetch_row($hc_cur)){
			   //echo "Achou";
			   $hc_Twin = odbc_result($hc_cur, 1);
		
		   }else{
			   //echo "NAO Achou";
			   $hc_Twin = 0;		
		   }


?>
  <form  name="frm_altera" action="<?php  echo $root;?>role/client/Client.php"  method="post" onSubmit="return consist(this)">
    <input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
    <input type="hidden" name="idBuyer" value="<?php  echo $idBuyer;?>">
    <input type="hidden" name="idVigente" value="<?php  echo $idVigente;?>">
    <input type="hidden" name="action" value="<?php  echo $comm == 'alterarDados' ? 'alter' : 'include';?>">
    <input type="hidden" name="comm" value="insertImporter">
    <?php  if ($idAnt > 0 && $inform_state < 10) { ?>
    <label><?php echo utf8_encode("Esta solicitação será considerada somente para o informe de renovação");?></label>
    <div style="clear:both">&nbsp;</div>
    <?php  }  ?>
    <?php  if ($comm == "alterarDados") { ?>
    <label>Altera&ccedil;&atilde;o de Compradores</label>
    <div style="clear:both">&nbsp;</div>
    <?php  }else{ ?>
    <label>Inclus&atilde;o de Compradores</label>
    <div style="clear:both">&nbsp;</div>
    <?php  } ?>
    <label>Aten&ccedil;&atilde;o: O limite de cr&eacute;dito &eacute; rotativo e deve ser calculado para cobrir a exposi&ccedil;&atilde;o m&aacute;xima do segurado em rela&ccedil;&atilde;o a cada comprador.  
											Depende do valor e da frequ&ecirc;ncia dos embarques, assim como do prazo de pagamento. Em caso de d&uacute;vida, entre em contato conosco:
											<ul><li>S&atilde;o Paulo (11) 3284-3132; 
                                            <!--<li>Rio Grande do Sul e Santa Catarina (47) 455-0455;--> 
                                            <li>Demais localidades (21) 2510-5000. </label>
    <div style="clear:both">&nbsp;</div>
    <ul>
    <li class="campo2colunas">
      <label>Raz&atilde;o Social</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados" ) { ?>
      <?php  echo $nameImporter;?>
      <input type="hidden"  maxlength="150" name="nameImporter" onKeyPress="TrataCase('U');" value="<?php  echo $nameImporter;?>">
      <?php  }else { ?>
      <input  type="text" name="nameImporter" onKeyPress="TrataCase('U');" value="<?php  echo $nameImporter;?>">
      <?php  } ?>
    </li>
    <li class="campo2colunas">
      <label>Registro fiscal do comprador</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $cnpj;?>
      <input  type="hidden" onKeyPress="TrataCase('U');" name="cnpj" value="<?php  echo $cnpj;?>">
      <?php  }else { ?>
      <input type="text" onKeyPress="TrataCase('U');" name="cnpj" value="<?php  echo $cnpj;?>">
      <?php  } ?>
    </li>
    <li class="campo2colunas">
      <label>Endere&ccedil;o</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $address;?>
      <input  type="hidden"  name="address" onKeyPress="TrataCase('U');" value="<?php  echo $address;?>">
      <?php   }else { ?>
      <input type="text" name="address" onKeyPress="TrataCase('U');" value="<?php  echo $address;?>">
      <?php   } ?>
    </li>
    <li class="campo2colunas">
      <label>Cidade</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $city;?>
      <input  type="hidden"  name="city" onKeyPress="TrataCase('U');" value="<?php  echo $city;?>">
      <?php  }else { ?>
      <input  type="text" name="city" onKeyPress="TrataCase('U');" value="<?php  echo $city;?>">
      <?php  } ?>
    </li>
    <li class="campo2colunas">
      <label>CEP</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $cep;?>
      <input  type="hidden"  onKeyPress="AllowOnly('0..9');" name="cep" value="<?php  echo $cep;?>">
      <?php  }else { ?>
      <input type="text"  name="cep" onKeyPress="AllowOnly('0..9');" value="<?php  echo $cep;?>">
      <?php  } ?>
    </li>
    <li class="campo2colunas">
      <label>Pa&iacute;s</label>
      <?php  $hc_ant_disabled = $disabled;
                    if ($hc_Twin > 0 && $comm == "alterarDados")
                    {
                       $disabled = true;
                
                    }else
                    {
                       $disabled = false;
                    }
                    ?>
      <?php  // Monta a lista de pa�ses
                        $sql = "SELECT id, name FROM Country ORDER BY name";
                    $sel = odbc_result(odbc_exec($db, "select idCountry from Importer where id=$idBuyer"), 1);
                    $name = "idCountry";
                    $empty = "";
                        require_once("../../interf/Select.php");
                
                        if ($disabled)
                        {
                        ?>
      <input type="hidden" name="idCountry" value="<?php  echo $sel;?>">
      <?php  }
                
                        $disabled = $hc_ant_disab
                
                
                      ?>
    </li>
    <li class="campo2colunas">
      <label>Telefone (incluir DDI e DDD)</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $tel;?>
      <input  type="hidden" onKeyPress="AllowOnly('0..9|()-');" name="tel" value="<?php  echo $tel;?>">
      <?php  }else { ?>
      <input  type="text" name="tel" onKeyPress="AllowOnly('0..9|()-');" value="<?php  echo $tel;?>">
      <?php  } ?>
    </li>
    <li class="campo2colunas">
      <label>Fax (incluir DDI e DDD)</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $fax;?>
      <input type="hidden" onKeyPress="AllowOnly('0..9|()-');" name="fax" value="<?php  echo $fax;?>">
      <?php  }else { ?>
      <input type="text"  name="fax" onKeyPress="AllowOnly('0..9|()-');" value="<?php  echo $fax;?>">
      <?php  } ?>
    </li>
    <li class="campo2colunas">
      <label>Contato</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $contact;?>
      <input  type="hidden" onKeyPress="TrataCase('U');"  name="contact" value="<?php  echo $contact;?>">
      <?php  } else { ?>
      <input  type="text"  onKeyPress="TrataCase('U');"  name="contact" value="<?php  echo $contact;?>">
      <?php  } ?>
    </li>
    <li class="campo2colunas">
      <label>E-mail contato</label>
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados") { ?>
      <?php  echo $emailContato;?>
      <input  type="hidden" size="30" onKeyPress="TrataCase('U');" name="emailContato" value="<?php  echo $emailContato;?>" onBlur="checa_email_contact(this.form);">
      </TD>
      <?php  } else { ?>
      <input  type="text" size="30" name="emailContato" onKeyPress="TrataCase('U');" value="<?php  echo $emailContato;?>" onBlur="checa_email_contact(this.form);">
      </TD>
      <?php  } ?>
    </li>
    </ul>
    <div style="clear:both">&nbsp;</div>
    <ul>
    <li class="campo2colunas">
      <label>Autoriza divulgar nome ao comprador:</label>
      <INPUT type="hidden" name="divulgaNomeOrig" value="<?php  echo $divulgaNome;?>">
      <?php  if ($hc_Twin > 0 && $comm == "alterarDados"){                ?>
      <div class="formopcao">
        <input type="radio" name="divulgaNome" value="1">
      </div>
      <div class="formdescricao">Sim</div>
      <div class="formopcao">
        <input type="radio" name="divulgaNome" value="0" checked >
      </div>
      <div class="formdescricao">N&atilde;o</div>
      <?php  }
                        else {
                            if ($divulgaNome == 1) {
                ?>
      <div class="formopcao">
        <input type=hidden name="divulgaNome" value="1">
      </div>
      <div class="formdescricao">
        <?php  echo("Sim");?>
      </div>
      <?php
                            }
                            else {                ?>
      <div class="formopcao">
        <input type="radio" name="divulgaNome" value="1" <?php  echo (($divulgaNome == 1) ? "checked" : "");?>>
      </div>
      <div class="formdescricao">Sim</div>
      <div class="formopcao">
        <input type="radio" name="divulgaNome" value="0" <?php  echo (($divulgaNome == 0) ? "checked" : "");?>>
      </div>
      <div class="formdescricao">N&atilde;o</div>
      <?php       }
                        }
                ?>
    </li>
    <!-- Fim alterado Hicom (Gustavo) -->
    <li class="campo2colunas">
      <label>Rela&ccedil;&atilde;o Comercial desde:</label>
      <input type="text" name="relation" onKeyPress="AllowOnly('0..9');" value="<?php  echo $relation;?>">
    </li>
    <li class="campo2colunas">
      <label>Previs&atilde;o Vol. Venda Anual At&eacute; o Final de Vig. da Ap&oacute;lice(
        <?php  echo $extMoeda;?>
        )</label>
      <input onFocus="select()" onBlur="checkDecimalsMil(this, this.value);testaLimite();" onKeyPress="AllowOnly('0..9|.');" type="text" name="prevExp12" value="<?php  echo $comm == 'alterarDados' ? $prevExp12 : $prevExp12;?>">
    </li>
    <li class="campo2colunas">
      <label>N&ordm; de Embarques no Ano</label>
      <input onFocus="select()" type="text"  name="numShip12" onBlur="testaLimite();" onKeyPress="AllowOnly('0..9');" value="<?php  echo $numShip12;?>">
    </li>
    <li class="campo2colunas"> <strong>Periodicidade de Embarques(Dias)</strong>
      <input onFocus="select()" type="text" name="periodicity" onBlur="testaLimite();" onKeyPress="AllowOnly('0..9');" value="<?php  echo $periodicity;?>">
    </li>
    <li class="campo2colunas">
      <label>Prazo de Pagamento (Dias)</label>
      <input onFocus="select()" type="text" name="przPag" onKeyPress="AllowOnly('0..9');" onBlur="testaLimite();" value="<?php  echo $przPag;?>">
    </li>
    <li class="campo2colunas">
      <label>Limite de Cr&eacute;dito Necess&aacute;rio (
        <?php  echo $extMoeda;?>
        ) <font color="red">*</font></label>
      <input onFocus="select()" onBlur="checkDecimalsMil(this, this.value);testaLimite();" onKeyPress="AllowOnly('0..9|.');" type="text"  name="limCredit"  value="<?php  echo $comm == 'alterarDados' ? $limCredit  : $limCredit;?>">
    </li>
    <li class="campo2colunas">
      <label>Tipo de Cobertura</label>
      Risco Comercial
      <input type="hidden" value="1" name="risk">
    </li>
    </ul>
    <div style="clear:both">&nbsp;</div>
    <label><font color="red">*</font><?php echo " Ap&oacute;s preencher os dados de previs&atilde;o vol. venda anual, de embarques no ano, periodicidade de embarques e prazo de pagamento, o sistema ir&aacute; sugerir um valor para o limite de cr&eacute;dito. O aceite deste valor &eacute; opcional.";?></label>
    <div style="clear:both">&nbsp;</div>
    <?php  if ($msg != "") {?>
    <label><font color="red">
      <?php  echo $msg;?>
      </font></label>
    <div style="clear:both">&nbsp;</div>
    <?php  } ?>
    <?php  $msg = "";   ?>
    <div class="barrabotoes">
      <button class="botaovgm" type="button"  onClick="this.form.comm.value='open';this.form.submit()">Voltar</button>
      <?php  $renov = esta_em_renovacao($idInform);
            
                  //alterado em 08/04/2004
            
               ?>
      <button class="botaoagm" type="button"  name="Incluir" onClick="<?php  echo $renov ? " if(confirm('Confirma " . ($comm == "alterarDados" ? "Alteração" : "Inclusão") . " de comprador na apólice vigente e no estudo da renovação?'))
                {
                 this.form.submit();
                }
                else
                {
                  verErro('Comprador não " . ($comm == 'alterarDados' ? 'Alterado' : 'Incluido') . "!');
            
                }" : "if(this.form.Incluir.value == 'Alterar')
                      {
                         if(confirm('Confirma alteração de dados?'))
                         {
                           this.form.submit();
                         }
                      }
                      else
                      {
                         if (document.all.limCredit.value==''){
                               verErro('ATEN&Ccedil;&Atilde;O!!! Campo limite de cr&eacute;dito n&atilde;o pode ser vazio.')
                         }else
                           if (document.all.prevExp12.value==''){
                              verErro('ATEN&Ccedil;&Atilde;O!!! Campo Previs&atilde;o Vol. Venda Anual n&atilde;o pode ser vazio.');
                           }else
                             if (document.all.numShip12.value=='') {
                              verErro('ATEN&Ccedil;&Atilde;O!!! Campo N&ordm; de embarques no ano n&atilde;o pode ser vazio.');
                             }else
                               if (document.all.periodicity.value==''){
                                 verErro('ATEN&Ccedil;&Atilde;O!!! Campo periodicidade de embarques n&atilde;o pode ser vazio.');
                               }else
                                if (document.all.przPag.value=='') {
                                 verErro('ATEN&Ccedil;&Atilde;O!!! Campo prazo de pagamento n&atilde;o pode ser vazio.');
                               }else{
                                  this.form.submit();
                               }
                      }";?>">
      <?php  echo $comm == 'alterarDados' ? 'Alterar' : 'Incluir' ?>
      </button>
      <?php   if ($i > 0) {
                          if ($stateCl == 10) {      ?>
      <button class="botaoagm" type="button" onClick="window.document.approve.submit()">OK</button>
      <?php         } else {
            ?>
      <button class="botaoagm" type="button" onClick="this.form.comm.value='open';this.form.submit()">OK</button>
      <?php         }
                    }
            
            ?>
      <button class="botaovgg" type="button" onClick="limpar(this.form)">Limpar Campos</button>
    </div>
  </form>
  
  <form action="<?php  echo $root;?>role/client/Client.php" method="post" name="approve" style="width:100%">
    <input type=hidden name=idInform value="<?php  echo $idInform;?>">
    <input type=hidden name="action" value="approve">
    <input type=hidden name="comm" value="alterImporter">
    <label>Compradores Novos</label>
    <table summary="Submitted table designs" class="tabela01">
      <thead>
      <Th colspan="1" align="center" scope="col">Raz&atilde;o Social</th>
        <Th colspan="1" align="center" scope="col">Pa&iacute;s</th>
        <Th colspan="1" align="center" scope="col">Cr&eacute;dito Concedido
          <?php  echo $extMoeda;?></th>
        <Th colspan="3" align="center" scope="col">Op&ccedil;&otilde;es</th>
        </thead>
      <tbody>
        <?php  $cur = odbc_exec($db,
                           "SELECT Importer.name, Importer.address, Importer.risk,
                                 Importer.city, Country.name, Importer.tel, Importer.prevExp12,
                                 Importer.limCredit, Importer.numShip12, Importer.periodicity,
                                 Importer.przPag, Importer.id, Importer.cep, Importer.fax, Importer.contact, Importer.emailContato
                                FROM Importer, Inform, Country
                                WHERE Importer.idInform = $idInform AND
                                 Importer.idInform = Inform.id AND
                                     Importer.idCountry = Country.id AND
                                     (Importer.state = 1 OR (Importer.state = 3 OR Importer.state=4))
                                     AND Importer.id not in (select distinct idImporter from ImporterRem)
                            ORDER BY Importer.id");
                  $i = 0;
				  
				  $odd ="";
                  while (odbc_fetch_row($cur)) {
                        $i++;
						
                        $idBuyer = odbc_result($cur,12);
						
                        echo "<input type=hidden name=importers[] value=$idBuyer>";
                        $aux = odbc_exec($db, "select credit from ChangeCredit where idImporter=$idBuyer and id =".
                                 "(select max(id) from credit where idImporter=$idBuyer)");
                        if(odbc_fetch_row($aux))
                          $credit = odbc_result($aux, 'credit');
						  
											
						if ($i % 2 != 0) 
						   $odd = 'class="odd"';
						else
						    $odd = "";
                    ?>
        <tr <?php echo $odd; ?>>
          <td colspan="1"><?php  echo odbc_result($cur,1);?></td>
          <td colspan="1"><?php  echo odbc_result($cur,5);?></td>
          <td colspan="1"><?php  echo number_format($credit, 2, ",", ".");?></td>
          </td>
          <td><a href="<?php  echo $root;?>role/client/Client.php?comm=alterarDados&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>"><img src="<?php echo $root;?>images/icone_editar.png" alt="" title="Editar Comprador" width="16" height="16" class="iconetabela"/></a></td>
          <td><a href="<?php  echo $root;?>role/client/Client.php?comm=addAddress&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>"><img src="<?php echo $root;?>images/icone_detalhar.png" alt="" title="Visualizar Endere&ccedil;os" width="16" height="16" class="iconetabela"/></a></td>
          <td><a href="<?php  echo $root;?>role/client/Client.php?comm=remove&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Comprador" width="16" height="16" class="iconetabela"/></a></td>
        </tr>
        <!--
                      <tr <?php echo $odd; ?>>
                        <td rowspan=3><?php  echo $i;?></td>
                        <td colspan="3"><label>Raz&atilde;o:</label></td><td><?php  echo odbc_result($cur,1);?>&nbsp;</td>
                        <td><a href="<?php  echo $root;?>role/client/Client.php?comm=alterarDados&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>"><label>Altera&ccedil;&atilde;o de Dados</label></a></td>
                      </tr>
    
                      <tr <?php echo $odd; ?>>
                        <td colspan="3"><label>Pa&iacute;s:</label></td><td><?php  echo utf8_encode(odbc_result($cur,5));?></td>
                        <td><a href="<?php  echo $root;?>role/client/Client.php?comm=addAddress&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>"><label>Consultar Endere&ccedil;os</label></a></td>
                      </tr>
    
                      <tr <?php echo $odd; ?>>
                        <td colspan="3"><label>Cr&eacute;dito Concedido <?php  echo $extMoeda;?>:</label></td><td><?php  echo number_format($credit, 2, ",", ".");?></td>
                        <td><a href="<?php  echo $root;?>role/client/Client.php?comm=remove&idBuyer=<?php  echo $idBuyer;?>&idInform=<?php  echo $idInform;?>&action=<?php  echo $action;?>"><label>Remover este importador</label></a></td>
                      </tr>
                    -->
        <?php  } // while
                  if ($i == 0) {     ?>
        <TR <?php echo $odd; ?>>
          <TD align="center" colspan="6">Nenhum Comprador Novo Cadastrado</TD>
        </TR>
        <?php  }    ?>
      </TBODY>
      <tfoot>
        <tr>
          <th colspan="6">&nbsp;
            </td>
        </tr>
      </tfoot>
    </TABLE>
  </form>
  <?php  }         ?>
  <script language=javascript>
        // limpa apenas os campos de texto e os radio buttons, e restaura o select de paises
        function limpar(f){
          f.nameImporter.value = "";
          f.address.value = "";
          f.city.value = "";
          f.cep.value = "";
          f.fax.value = "";
          f.contact.value = "";
          f.emailContato.value = "";
          f.tel.value = "";
          f.prevExp12.value = "0";
          f.limCredit.value = "0";
          f.numShip12.value = "";
          f.periodicity.value = "";
          f.przPag.value = "";
          f.idCountry.options[0].selected = true;
          var i;
          for (i = 0; i < f.risk.length; i++){
            f.risk[i].checked = false;
          }
        }
        </script>
</DIV>

