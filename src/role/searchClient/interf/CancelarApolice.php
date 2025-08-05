<?php 
    $sql="SELECT id, name, contrat, state FROM Inform WHERE id='$idInform'";
    $cur = odbc_exec($db, $sql);
    $nome =odbc_result($cur, 2);
    $napolice =odbc_result($cur, 3);
    $idInform=odbc_result($cur, 1);
	$status=odbc_result($cur, 4)
?>
<script language="javascript">
function confirma(c){
 if (document.Form2.senha.value == "") {
    verErro("Digite sua senha!");
    document.Form2.senha.focus();
    return false;
 } else
   if (document.Form2.senha.value != document.Form2.password.value) {
    verErro("Senha não confere!");
    document.Form2.senha.focus();
    return false;
 }
 if (confirm('Tem certeza que deseja cancelar definitivamente esse informe?')){
    return true;
 }else{
   return false;
 }
}

function showConfirmacao(){
   if (confirmacao.style.display == "none") {
    confirmacao.style.display = "block";
   } else {
    confirmacao.style.display = "none";
   }
}

</script>
<?php require_once("../../../navegacao.php");
    if(!$idInform){
       $idInform   = $_REQUEST["idInform"];
	}
?>
<div class="conteudopagina">

    <form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/SearchClient.php" method="post">
    <input type="hidden" name="acao" value="cancelar">
    	<li class="campo2colunas">
        	<label>Nome</label>
            <?php  echo ($nome);?>
        </li>
        <li class="campo2colunas">
        	<label>N&ordm; CI</label>
            <?php  
				if ( empty($napolice) ) {
					echo "--------";
				 }else{
					echo $napolice;
				 }
			?>
        </li>
        <div class="barrabotoes">
    <input type="hidden" value="cancelar" name="comm">
    <input type="hidden" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>" name="idInform">
    <input type="hidden" value="<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>" name="status_at">
    <button class="botaovgm" type="button" onClick="window.location = '<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/searchClient/SearchClient.php';">Voltar</button>
    <button class="botaovgm" type="button" onclick="showConfirmacao();">Cancelar</button>
</div>

        
        <div id="confirmacao" style="display:none;">
        
        <p>Entre com a sua senha abaixo para cancelar definitivamente esse informe no sistema!</p>
        
         <?php

			 $sql = odbc_exec($db, "SELECT login, password FROM Users where id='$userID'");
			 $loginConf = odbc_result($sql, 1);
			 $password = odbc_result($sql, 2);

				 if ($senha != "") {
					 if ($senha != $password) {
						 $erro = "Senha não confere!";

					  } else if (($confirmar) && (!$senha)) {
					  $erro = "Digite sua senha!";
					  }
				 }
		 ?>
        <input type="hidden" name="password" value="<?php echo $password; ?>">
        <li class="campo2colunas">
        	<label>Login</label>
            <input type="text" name="login" value="<?php echo $loginConf; ?>" onclick="confirmar.blur()" readonly="readonly">
        </li>
        <li class="campo2colunas">
        	<label>Senha</label>
            <input type="password" name="senha" value="" onclick="this.focus()">
        </li>
        <?php 
			 if ($erro) {
				echo "<p>".$erro."</p>";
			 }
		?>
        <div class="barrabotoes">
        	 <button class="botaoagm" type="button" onClick="if (confirma()) this.form.submit();">Confirmar</button>
        </div>
        
        </div>
    </form>
</div>