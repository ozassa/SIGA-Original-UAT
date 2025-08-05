<script language="Javascript">
   // 15/05/2009 - Interaktiv (Elias Vaz) - Alteração
   //              Criada a função para validar email
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

  function verifica()
  {
     if(document.form1.Name.value=="")
     {
      verErro('O campo nome deve ser preenchido');
      return(0);
     }

     if(document.form1.email.value=="")
     {
      verErro('O campo e-mail deve ser preenchido');
      return(0);
     }
     else
     {
       document.form1.submit();
     }
  }

</script>

<?php  

$idInform = $field -> getField ("idInform");
$idContact = $field -> getField ("idContact");

$princ = $_REQUEST['princ'];

if ($idInform > 0 && (!$idContact)) {
  	$query1 = "SELECT contact, ocupationContact, emailContact, telContact, faxContact, id
	     FROM Inform
	     WHERE id = $idInform";	
		$contact1 = odbc_exec($db,$query1);

	  if (odbc_fetch_row ($contact1)) {
		$cargo	       = odbc_result($contact1, "ocupationContact");
		$tel	       = odbc_result($contact1, "telContact");
		$fax               = odbc_result($contact1, "faxContact");
		$Name              = odbc_result($contact1, "contact");
		$email             = odbc_result($contact1, "emailContact");
		$emailCredit       = 1;
	  }
  
  
} else {

  $qq = 	"SELECT Contact.id, Contact.idImporter, Contact.idInform,
			    Contact.name, Contact.tel, Contact.fax, Contact.title,
			    Contact.email, Contact.notificationForChangeCredit,i_Tipo_Contato
			 FROM Contact
			 WHERE Contact.id = $idContact";

  $cc = odbc_exec($db, $qq);

  if (odbc_fetch_row($cc)) {
    $cargo		= odbc_result($cc, 7);
    $tel	        = odbc_result($cc, 5);
    $fax               = odbc_result($cc, 6);
    $Name              = odbc_result($cc, 4);
    $email             = odbc_result($cc, 8);
    $emailCredit       = odbc_result($cc, 9);
	$i_Tipo_Contato    = odbc_result($cc, 10);
		
    $q = 	"SELECT Inform.name, Inform.id
			FROM Inform, Contact
			WHERE Contact.id = $idContact
			AND Contact.idInform = Inform.id";
		
    $c = odbc_exec($db, $q);
		
    if (odbc_fetch_row ($c)) {
      $nameExpo = odbc_result ($c, 1);
      $idInform = odbc_result ($c, 2);
    } else {
      $nameExpo = "Não localizou o exportador.";
    }
  }
}

?>
<?php include_once('../../../navegacao.php'); ?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->

<div class="conteudopagina">

    <h2><?php echo  $nameExpo;?></h2>
    
    <form name="form1"  action="<?php echo  $root;?>role/credit/Credit.php?idInform=<?php echo $idInform;?>&idContact=<?php echo $idContact;?>" method="post">
     <input type="hidden" name="comm" value="AlterationContact">
     <input type="hidden" name="idContact" id="idContact" value="<?php echo $idContact;?>" />
     <li class="campo3colunas">
     	<label>Contato</label>
        <input type="text" name="Name" value="<?php echo $Name;?>">
     </li>
     <li class="campo3colunas">
     	<label>Telefone</label>
        <input type="text" name="tel" value="<?php echo $tel;?>">
     </li>
     <li class="campo3colunas">
     	<label>Fax</label>
        <input type="text" name="fax" value="<?php echo $fax?>">
     </li>
   
   
     <li class="campo3colunas">
     	<label>Cargo</label>
        <input type="text" name="cargo" value="<?php echo $cargo?>">
     </li>
     <li class="campo3colunas">
     	<label>E-mail</label>
        <input type="text" name="email" value="<?php echo $email?>"  onblur="checa_email_contact(this.form)">
     </li>
     
     <li class="campo3colunas">
	<label>Tipo de contato </label>
    
    <?php 
	    $sql  = "select i_Tipo_Contato, Descricao
                 From Tipo_Contato where Situacao = 0";
		$cur  = odbc_exec($db, $sql);	
		
	if($princ == 1){
	    $disable  = 'disabled="disabled"';	
		
	}else {
		 $disable  = '';	
	}
	?>
    
    <select  name="tipo_contato" id="tipo_contato" <?php echo  $disable;?>>
         <option value="">Principal</option>
        <?php while (odbc_fetch_row($cur)) {  ?>
                  <option value="<?php echo odbc_result($cur,'i_Tipo_Contato');?>" <?php  echo ($i_Tipo_Contato == odbc_result($cur,'i_Tipo_Contato') ? 'selected' : '');?>><?php echo odbc_result($cur,'Descricao');  ?></option>
	   <?php } ?>			  
    </select>
    
</li>
     
    <?php  if (!$princ) {?>
    <li id="clear" class="campo2colunas">
        <label>&nbsp;</label>
        <div class="formopcao">
          <input type="checkbox" name="emailCredit" value="1" <?php echo $emailCredit == "1" ? checked : "";?>/>
        </div>
        <div class="formdescricao"><span>Receber aviso de Altera&ccedil;&atilde;o de Limite de Cr&eacute;dito</span></div>
    </li>
    <?php  } ?>
    <div class="barrabotoes">
          <input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
          <input type="hidden" name="idContact" value="<?php echo  $idContact;?>">
          <button class="botaovgm" type="button" onClick="this.form.comm.value='searchContact';this.form.submit();">Voltar</button>
          <button class="botaoagm" type="button" onClick="javascript:verifica();">Alterar</button>
    </div>
    </form>
    
    
</div>