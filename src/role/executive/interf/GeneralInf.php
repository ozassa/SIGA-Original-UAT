<?php  // ALTERADO HICOM EM 03/05/2004
// Alterado HiCom mes 04
// alterado Hicom 27/12/2004 (Gustavo) - Adicionei os campos addressNumber e chargeAddressNumber 

?>
<script Language="JavaScript">
function checa_formulario(f){
         if (f.pvigencia.value == 0){
              verErro("Por Favor, Selecione o Per&iacute;odo de vig&ecirc;ncia");
              return (false);
         }
         if (f.tipomoeda.value == "") {
              verErro("Por Favor, Selecione o Tipo de Moeda");
              return (false);
         }
}
function notnull(fieldName, fieldValue){
if (fieldValue == ""){
  verErro("Preenchimento Obrigat&oacute;rio")
  fieldName.focus() }
}
</SCRIPT>
<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">

<?php  $idInform = $field->getField("idInform");
  $cur = odbc_exec($db, "SELECT * FROM Inform WHERE id = $idInform");
  if (odbc_fetch_row($cur)) {
    $field->setDB ($cur);
    $idAnt = odbc_result($cur, 'idAnt');
    $premio = odbc_result($cur, 'prMax');
    
    $addressNumber = odbc_result($cur, 'addressNumber');
    $chargeAddressNumber = odbc_result($cur, 'chargeAddressNumber');
    $addressComp = odbc_result($cur, 'addressComp');
    $chargeAddressComp = odbc_result($cur, 'chargeAddressComp');
    $currency          = odbc_result($cur, 'currency');

    if($premio > 0){
      $reestudo = 1;
    }
?>
<form action="<?php  echo $root;?>role/executive/Executive.php" method="post" name="f" onsubmit="return checa_formulario(this);">
    <input type="hidden" name="comm" value="generalSubmit">
    <input type="hidden" name="idInform" value="<?php   echo $idInform;?>">
    <input type="hidden" name="idNotification" value="<?php   echo $idNotification;?>">
	<?php  if ($role["executive"] || $role["executiveLow"] || $role["creditManager"]) {?>
      <li class="campo3colunas">
      		<label>NAF</label>
            <input name="naf" value="<?php   echo $field->getDBField("naf", 17);?>">
      </li>
      <li class="campo3colunas">
      		<label>SIREN N&ordm; E175</label>
            <input name="siren" value="<?php   echo $field->getDBField("siren", 19);?>">
      </li>
      <li class="campo3colunas">
      		<label>QUESTIONNAIRE</label>
            <input name="quest" value="<?php   echo $field->getDBField("quest", 21);?>">
      </li>
      
      <li class="campo3colunas">
      		<label>NAPCE</label>
            <input name="napce" onBlur="notnull(this,this.value)" value="<?php   echo $field->getDBField("napce", 18) ?>">
      </li>
      <li class="campo3colunas">
      		<label>DOSSIER</label>
            <input name="dossier" value="<?php   echo $field->getDBField("dossier", 20);?>">
      </li>
      <li class="campo3colunas">
      		<label>CONTRAT</label>
            <input name="contrat" onBlur="notnull(this,this.value)" value="<?php   echo $field->getDBField("contrat", 22) ?>"<?php  if ($idAnt || $reestudo) { ?> <?php  } ?> />
      </li>
    
     
<?php  }?>
  <div class="divisoria01"></div>
  <p>Proposta para Risco Comercial</p>
  <li class="campo3colunas">
      		<label>Cobertura para juros de mora</label>
			<?php   echo $field->getDBField ("warantyInterest", 2) ? "Sim" : "N&atilde;o";?>
  </li>
    <?php  if ($field->getDBField ("pvigencia", 125) == "") {
         $pvigencia="1";
      }else if ($field->getDBField ("pvigencia", 125)=="1") {
         $pvigencia="1";
      }else{
         $pvigencia="2";
      }
    ?>
    <li class="campo3colunas">
        <label>Per&iacute;odo de Vig&ecirc;ncia selecionado</label>
        <select  name="pvigencia">
            <option value="0">--------</option>
            <option value="1" <?php  echo $pvigencia=="1" ? "selected" : "";?> >12 Meses</option>
            <option value="2" <?php  echo $pvigencia=="2" ? "selected" : "";?> >24 Meses</option>
        </select>
     </li>

      <?php  //Alterado por Tiago V N - Elumini - 05/04/2006
      if ($currency == "2") {
         $dolla = "selected";
      }else if ($currency == "6"){
         $euro = "selected";
      }else{
         $vazio = "";
      }
      ?>
   	  <li class="campo3colunas">
        <label>Tipo de Moeda selecionado</label>
        <select  name="tipomoeda">
           <option value="" <?php   echo $vazio;?>></option>
           <option value="2" <?php   echo $dolla;?>>Dol&aacute;r</option>
           <option value="6" <?php   echo $euro;?>>Euro</option>
        </select>
      </li>
	  
      <div class="divisoria01"></div>
      <h3>DADOS DA EMPRESA (EXPORTADOR)</h3>
      
  
      <li class="campo3colunas">
        <label>Nome</label>
    	<?php   echo ($field->getDBField("name", 15));?>
      </li>
      
      <li class="campo3colunas">
        <label>Endere&ccedil;o</label>
    	<?php   echo ($field->getDBField("address", 26));?>
      </li>
      <li class="campo3colunas">
        <label>N&ordm;</label>
        <?php   echo $addressNumber;?>
      </li>
      <li class="campo3colunas">
        <label>Complemento</label>
        <?php   echo $addressComp;?>
      </li>
  	  <li class="campo3colunas">
        <label>Cidade</label>
		<?php   echo $field->getDBField("city", 27);?>
      </li>
      <li class="campo3colunas">
        <label>CEP</label>
        <?php   echo $field->getDBField("cep", 29);?>
      </li>
      <li class="campo3colunas">
         <label>Telefone (com DDD)</label>
		 <?php   echo $field->getDBField("tel", 30);?>
      </li>
      <li class="campo3colunas">
        <label>Fax</label>
        <?php   echo $field->getDBField("fax", 31);?>
      </li>
      <li class="campo3colunas">
         <label>Email da Empresa</label>
         <?php   echo $field->getDBField("email", 32);?>
      </li>
      <li class="campo3colunas">
        <label>Contato</label>
		<?php   echo $field->getDBField("contact", 33);?>
      </li>
      <li class="campo3colunas">
        <label>Cargo</label>
		<?php   echo $field->getDBField("ocupationContact", 34);?>
      </li>
      <li class="campo3colunas">
        <label>E-mail do contato</label>
		<?php   echo $field->getDBField("emailContact", 35);?>
      </li>
      <li class="campo3colunas">
        <label>CNPJ</label>
		<?php   echo $field->getDBField("cnpj", 36);?>
      </li>
      <li class="campo3colunas">
        <label>Inscri&ccedil;&atilde;o Estadual ou Municipal</label>
		<?php   echo $field->getDBField("ie", 37);?>
      </li>
      <li class="campo3colunas">
        <label>Regi&atilde;o</label>
		<?php  // Monta a lista de Região
        $sql = "SELECT id, description FROM Region ORDER BY name";
        $sel = $field->getDBField("idRegion", 55);
		$name = "idRegion";
        $disabled = true;
        require_once("../../interf/Select.php");
        ?>
      </li>
    
      
      <div class="divisoria01"></div>
      
      <h3>DADOS PARA COBRAN&Ccedil;A</h3>
  
   	  <li class="campo3colunas">
        <label>Utilizar dados acima?</label>
		<?php   echo $field->getDBField("sameAddress", 56) ? "Sim" : "Não";?>
      </li>
      <li class="campo3colunas">
        <label>Endere&ccedil;o</label>
        <?php   echo $field->getDBField("chargeAddress", 57);?>
      </li>
      <li class="campo3colunas">
        <label>N&ordm;</label>
    	<?php   echo $chargeAddressNumber;?>
      </li>
      <li class="campo3colunas" id="clear">
        <label>Complemento</label>
		<?php   echo $chargeAddressComp;?>
      </li>
  
	 
  	 <li class="campo3colunas">
        <label>Endere&ccedil;o Abreviado</label>
    	<input type="text" name="addressAbrev" value="<?php   echo $addressAbrev;?>">
     </li>
     <li class="campo3colunas">
        <label>Cidade</label>
		<?php   echo $field->getDBField("chargeCity", 58);?>
     </li>
     <li class="campo3colunas" id="clear">
        <label>UF</label>
          <?php  // Monta a lista de UF
			$sql = "SELECT uf, uf FROM UF ORDER BY uf";
			$sel = $field->getDBField("chargeUf", 60);
			$name = "chargeUf";
			$disabled = true;
			require_once("../../interf/Select.php");
		  ?>
      </li>
      <li class="campo3colunas">
        <label>CEP</label>
        <?php   echo $field->getDBField("chargeCep", 59);?>
      </li>
  
      <div class="divisoria01"></div>
      <h3>ATIVIDADE COMERCIAL</h3>

      <li class="campo2colunas">
        <label>Setor</label>
        <?php  // Monta a lista de Países
			$sql = "SELECT id, description FROM Sector ORDER BY description";
			$sel = $field->getDBField("idSector", 16);
			$name = "idSector";
			$disabled = true;
			require_once("../../interf/Select.php");
		  ?>
	   </li>
       <li class="campo2colunas">
        <label>Produto(s) exportado(s)</label>
		<?php   echo ($field->getDBField("products", 38));?>
       </li>
       <li class="campo2colunas">
            <label>Prazo m&eacute;dio usualmente concedido para pagamento</label>
			<?php   echo $field->getDBField("frameMed", 42);?> dias
       </li>
   	   <li class="campo2colunas">
            <label>Exporta a mais de tr&ecirc;s anos</label>
            <?php   echo $field->getDBField("exportMore", 91) ? "Sim" : "N&atilde;o";?>
       </li>
       
       <div class="divisoria01"></div>
       
       <h3>INFORMA&Ccedil;&Otilde;ES GERAIS</h3>
   	   
       <li class="campo2colunas">
        <label>A empresa pertence a algum grupo?</label>
		<?php   echo $field->getDBField("hasGroup", 44) ? "Sim" : "N&atilde;o";?>
       </li>
       <li class="campo2colunas">
        <label>Caso positivo, qual?</label>
		<?php   echo ($field->getDBField("companyGroup", 45));?>
       <li class="campo2colunas">
        <label>A empresa possui companhias associadas no exterior?</label>
		<?php   echo $field->getDBField("hasAssocCompanies", 46) ? "Sim" : "N&atilde;o";?>
       </li>
       <li class="campo2colunas">
        <label>Caso positivo, quais?</label>
		<?php   echo $field->getDBField("associatedCompanies", 47);?>
       </li>
       
       <div class="divisoria01"></div>
  
       <h3>OBJETIVOS</h3>
  		<li class="campo2colunas">
            <label>[ <?php   echo $field->getDBField("warantyExp", 49) ? "X" : "&nbsp;";?> ] Garantia &agrave; exporta&ccedil;&atilde;o</label>
 		</li>
  
  		<li class="campo2colunas">
        	<label>[ <?php   echo $field->getDBField("warantyFin", 50) ? "X" : "&nbsp;";?> ] Garantia para financiamentos &agrave; exporta&ccedil;&atilde;o</label>
        </li>
        <li class="campo2colunas">
            <label>[ <?php   echo $field->getDBField("hasAnother", 51) ? "X" : "&nbsp;";?> ] Outros</label>
        <li class="campo2colunas">
            <label>Quais?</label>
            <?php echo ($field->getDBField("another", 52));?>
        </li>
 

        
        <div class="barrabotoes">
        	<button name="inicial" onClick="this.form.comm.value='open';this.form.submit()" class="botaoagm">Tela Inicial</button>
            <button name="proxima" type="submit" class="botaoagm">Pr&oacute;xima Tela</button>
        </div>
 </form>
        
            <?php  } else {?>
                <p>Informe inv&aacute;lido</p>
            <?php  }?>
        
</div>
