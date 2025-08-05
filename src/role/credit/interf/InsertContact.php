<?php  // 15/05/2009 - Interaktiv (Elias Vaz) - Alteração
//              Criada a função para validar email

	$idInform = $field->getField("idInform");
	
	$q = "SELECT name, contrat, id FROM Inform WHERE id = ?";
$stmt = odbc_prepare($db, $q);
odbc_execute($stmt, [$idInform]);
$c = $stmt;
odbc_free_result($c);

	if(odbc_fetch_row($c))      {
		$nameExpo = odbc_result($c, 1); 
		$ciExpo	  = odbc_result($c, 2); 
	} else {
		$nameExpo = 	"Erro";
	}
?>
<script Language="JavaScript">
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
       valmail = form.email.value;
		  if (form.email.value != ""){
			  if(! novoValidaEmail(valmail)){
				 form.email.value = '';
				 form.email.focus();
				 verErro("O e-mail do Contato &eacute; Inv&aacute;lido!");
				 return false;
			  }
			  else{
				  form.email.value = valmail.toLowerCase();
			  }
		  }
    }
	
	function validaFrom(){
		if(document.enviarcontato.Name.value == '') {
			verErro('Aten&ccedil;&atilde;o! Informe o nome do contato.');	
			return false;
		}else if(document.enviarcontato.cargo.value == '') {
			verErro('Aten&ccedil;&atilde;o! Informe o cargo do contato.');	
			return false;
		}else if(document.enviarcontato.email.value == '') {
			verErro('Aten&ccedil;&atilde;o! Informe o email do contato.');	
			return false;
		}else if(document.enviarcontato.tipo_contato.value == '') {
		    verErro('Aten&ccedil;&atilde;o! Selecione tipo de contato.');	
			return false;
		}else if(document.enviarcontato.tipo_contato.value == 0) {
		    verErro('Aten&ccedil;&atilde;o! Este contato deve ser diferente do tipo princiapl, tente outro.');	
			return false;	
		}else{
		    return true;	
			
		}
		
	}
	
</script>
<?php require_once("../../../navegacao.php");?>
<div class="conteudopagina">

<li class="campo2colunas">
	<label>Segurado</label>
    <?php echo ($nameExpo);?>
</li>

<li class="campo2colunas">
	<label>Ci Segurado</label>
    <?php echo ($ciExpo);?>
</li>

<form name="back" action="<?php echo  $root;?>role/searchClient/ListClient.php">
  <input type="hidden" name="comm" value="view">
  <input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
</form>

<form action="<?php echo  $root;?>role/credit/Credit.php" method="post" name="enviarcontato">	

<li id="clear" class="campo3colunas">
	<label>Contato *</label>
    <input type="text" name="Name">
</li>

<li class="campo3colunas">
	<label>Telefone</label>
    <input type="text" name="tel">
</li>

<li class="campo3colunas">
	<label>Fax</label>
    <input type="text" name="fax">
</li>

<li class="campo3colunas">
	<label>Cargo *</label>
    <input type="text" name="cargo">
</li>

<li class="campo3colunas">
	<label>E-mail *</label>
    <input type="text" name="email" onblur="checa_email_contact(this.form)">
</li>

<li class="campo3colunas">
	<label>Tipo de contato *</label>
    
    <?php 
	    $sql  = "select i_Tipo_Contato, Descricao
                 From Tipo_Contato where Situacao = 0";
		$cur  = odbc_exec($db, $sql);		 
	?>
    
    <select  name="tipo_contato" id="tipo_contato">
        <option value="">Selecione...</option>
        <option value="0">Principal</option>
        <?php while (odbc_fetch_row($cur)) {  ?>
                  <option value="<?php echo odbc_result($cur,'i_Tipo_Contato');?>"><?php echo odbc_result($cur,'Descricao');?></option>
	   <?php } ?>			  
    </select>
</li>


<li id="clear" class="campo2colunas">
	<div class="formopcao">
      <input type="checkbox" name="emailCredit" value="1"/>
    </div>
    <div class="formdescricao"><span>Receber aviso de Altera&ccedil;&atilde;o de Limite de Cr&eacute;dito</span></div>
</li>

<input type="hidden" name="idInform" value=<?php echo $idInform;?>>
<input type="hidden" name="comm" value="InsertContactDB">

</form>
<div class="barrabotoes">
	<button  type="button" class="botaoagm" onClick="javascript: document.back.submit();" >Voltar</button>
    <button type="button" class="botaoagm" onclick="if(validaFrom()) document.enviarcontato.submit();">Incluir</button>
</div>
<p><?php echo $msg;?></p>
</div>