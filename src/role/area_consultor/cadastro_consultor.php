<script type="text/javascript">
  $(document).ready(function(){
    $("#js_redir_consult").on("click", function(event){
      window.location = "listConsultor.php?comm=listaConsultor";
      event.preventDefault();
      return false;
    })
    $("#js_form_cad").on("submit", function(event){
      if(!verificaForm()){
        event.preventDefault();
        return false;
      }
    })
  })
 function verificaForm()
 {
   if(document.form1.razao.value=="") {
     verErro('O campo Raz&atilde;o Social deve ser preenchido');
     return false;
   } else if(document.form1.contato.value=="") {
     verErro('O campo Nome Contato deve ser preenchido');
     return false;
   } else if(document.form1.cnpj.value=="") {
     verErro('O campo CNPJ deve ser preenchido');
     return false;
   } else if(document.form1.telefone.value=="") {
     verErro('O campo Telefone deve ser preenchido');
     return false;
   } else if(document.form1.email.value=="") {
     verErro('O campo Email deve ser preenchido');
     return false;
   }

   return true;
 }
</script>
<?php include_once("../../../navegacao.php");?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->

  <?php if(isset($msgAg)){ ?>
    <div class="conteudopagina" style="padding-bottom: 10px;">
        <p style="color:#6e6"><?php echo $msgAg; ?></p>
   </div>
  <?php } ?>  
  <?php if (isset($msgs) && count($msgs) > 0) { ?>
    <div class="conteudopagina" style="padding-bottom: 10px;">
      <p style="color:#e66">Há erros na página</p>
      <ul style=" padding-left: 25px;">
        <?php foreach ($msgs as $key => $value) { ?>
          <li style="color:#e66; list-style:disc;"><?php echo $value; ?></li>
        <?php } ?>
      </ul>
    </div>
  <?php }?>
    <form name="form1" id="js_form_cad" action="<?php echo $root; ?>role/area_consultor/listConsultor.php" method="post">
      <input type="hidden" name="comm" value="insereConsultor">
    <div class="conteudopagina">
      <li class="campo2colunas">
        <label>Raz&atilde;o Social</label>
        <input name="razao" type="text" value="<?php echo isset($_SESSION["valida_razao"]) ? $_SESSION["valida_razao"] : ""; ?>">
      </li>
      <li class="campo2colunas">
        <label>Nome Contato</label>
        <input name="contato" type="text" value="<?php echo isset($_SESSION["valida_contato"]) ? $_SESSION["valida_contato"] : ""; ?>">
      </li>
      <li class="campo2colunas">
        <label>CNPJ</label>
        <input name="cnpj" type="text" value="<?php echo isset($_SESSION["valida_cnpj"]) ? $_SESSION["valida_cnpj"] : ""; ?>">
      </li>
      <li class="campo2colunas">
        <label>Telefone</label>
        <input name="telefone" type="text" value="<?php echo isset($_SESSION["valida_telefone"]) ? $_SESSION["valida_telefone"] : ""; ?>">
      </li>
      <li class="campo2colunas">
        <label>Celular</label>
        <input name="celular" type="text" value="<?php echo isset($_SESSION["valida_celular"]) ? $_SESSION["valida_celular"] : ""; ?>">
      </li>
      <li class="campo2colunas">
        <label>Email</label>
        <input name="email" type="text" value="<?php echo isset($_SESSION["valida_email"]) ? $_SESSION["valida_email"] : ""; ?>">
      </li>
      <li class="campo2colunas">
        <label>C&oacute;digo SUSEP</label>
        <input name="codigosusep" type="text" value="<?php echo isset($_SESSION["valida_codigosusep"]) ? $_SESSION["valida_codigosusep"] : ""; ?>">
      </li>
      <?php 
        if(isset($_SESSION["valida_razao"])){
          unset($_SESSION["valida_razao"]);
          unset($_SESSION["valida_contato"]);
          unset($_SESSION["valida_telefone"]);
          unset($_SESSION["valida_celular"]);
          unset($_SESSION["valida_email"]);
          unset($_SESSION["valida_codigosusep"]);
          unset($_SESSION["valida_cnpj"]);
        }
      ?>
      <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
      <button class="botaoagm2" id="js_redir_consult">Exibir Corretores</button>
      <button class="botaoagm2" onclick="javascript: this.form.reset();return false;">Limpar</button>
      <button name="Cadastrar" type="submit" class="botaoagm2" >Cadastrar</button>
      </li>
    </div>
    
    </form>
    <div class="conteudopagina">
        <p>Observa&ccedil;&otilde;es</p>
        <p>
        	 <b>1)</b> Ao ser cadastrado, o sistema envia automaticamente o login e senha
             para o e-mail do consultor. O login ser&aacute; o e-mail do consultor e a senha
             ser&aacute; gerada automaticamente pelo sistema.
        </p>
        <p>
        	 <b>2)</b> O consultor poder&aacute; alterar posteriormente sua senha.
        </p>
        <p>
        	 <b>3)</b> Somente ser&aacute; aceito 1 e-mail por consultor.
        </p>
        <p>
        	 <b>4)</b> &Eacute; importante cadastrar o nome completo do consultor.
        </p>
    </div>


