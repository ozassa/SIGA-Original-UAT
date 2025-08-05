<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">

<?php 

   	if($field->getField("idBuyer")){
	   $idBuyer = $field->getField("idBuyer");
	} 
	
	$c = odbc_exec($db,
	       "select i.origName, i.origAddress, c.name, i.origTel, i.origCity, i.origCep, i.origFax
                from Importer i join Country c on i.idCountry = c.id where i.id=$idBuyer");

	if(odbc_fetch_row($c)){
		
	  $name = odbc_result($c, 1);
	  $address = odbc_result($c, 2);
	  $country = odbc_result($c, 3);
	  $tel = odbc_result($c, 4);
	  $city = odbc_result($c, 5);
	  $cep = odbc_result($c, 6);
	  $fax = odbc_result($c, 7);
	}
	
?>
<form action="<?php echo  $root;?>role/credit/Credit.php" method="post">
    <input type="hidden" name="origem" 	 value="<?php echo  $origem;?>">
    <input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
    <input type="hidden" name="comm" 	 value="addAddress">
    <input type="hidden" name="idBuyer"  value="<?php echo  $idBuyer;?>">
	
    <li class="campo2colunas">
        <label>Importador</label>
        <?php echo  $name;?>
    </li>
    
    <li class="campo2colunas">
        <label>Endere&ccedil;o</label>
        <?php echo  $address;?>
    </li>
    
    <li class="campo2colunas">
        <label>Pa&iacute;s</label>
        <?php echo  $country;?>
    </li>
    
    <li class="campo2colunas">
        <label>Telefone</label>
        <?php echo  $tel;?>
    </li>
    
    <li class="campo2colunas">
        <label>Cidade</label>
        <?php echo  $city;?>
    </li>
    
    <li class="campo2colunas">
        <label>CEP</label>
        <?php echo  $cep;?>
    </li>
    
    <li class="campo2colunas">
        <label>Fax</label>
        <?php echo  $fax;?>
    </li>
	<div class="barrabotoes">  
        <button class="botaovgm" type="button" onClick="this.form.comm.value='showBuyers'; this.form.submit()">Voltar</button>
    </div>  
</form>
</div>
<br clear="all" />