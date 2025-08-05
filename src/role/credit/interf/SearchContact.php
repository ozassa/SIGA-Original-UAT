	
<?php 

 $idInform = $field -> getField ("idInform");?>

<?php require_once("../../../navegacao.php");?>
<div class="conteudopagina">
	<form  action="<?php echo  $root;?>role/credit/Credit.php?comm=searchContact" name="busca" method="post">
       <input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
           <li class="campo2colunas">
                <label>Contato</label>
                <input type="text" name="searchContact" id="searchContact" value="">
           </li>        
           <li class="campo2colunas">
                <label>&nbsp;</label>
                <button type="button" class="botaoagm" onclick="document.busca.submit();">Buscar</button>
           </li>
      </form>
    <div style="clear:both">&nbsp;</div>
<?php  
      
	  require_once("../credit/searchcontact.php"); 

      require_once("searchContactInterf.php");  //formata a saida


	$q = "SELECT name, contact FROM Inform WHERE id = ?";
     $c = odbc_prepare($db, $q);
     odbc_execute($c, array($idInform));

     if(odbc_fetch_row($c))      {
          $nameExpo = odbc_result($c, 1); 
          $ciExpo     = odbc_result($c, 2); 
}    else {
          $nameExpo =    "Erro";
}

     odbc_free_result($c);


	

?>
</div>