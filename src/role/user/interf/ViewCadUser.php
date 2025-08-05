<?php include_once('../../../navegacao.php'); ?>

<script>
	function VerificaEmail (obj) {
		var email = obj.value; 
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

		if (!re.test(email)) {
			return false;
		}
	}

	function VerificaCPF (obj) {
	  var cpf = obj.value; 
	  var numeros, digitos, soma, i, resultado, digitos_iguais;
	  cpf = cpf.replace('.','');
	  cpf = cpf.replace('.','');
	  cpf = cpf.replace('-','');
	 
	  if (cpf.length > 0){  
		  digitos_iguais = 1;
		  if (cpf.length < 11){
			    verErro('CPF informado est&aacute; incompleto!'); 
				obj.value = '';
				return false;
		  }
		  for (i = 0; i < cpf.length - 1; i++)
			 if (cpf.charAt(i) != cpf.charAt(i + 1)){
					  digitos_iguais = 0;
					  break;
		 	 }
			 
		     if (!digitos_iguais){
				numeros = cpf.substring(0,9);
				digitos = cpf.substring(9);
				soma = 0;
				for (i = 10; i > 1; i--)
					  soma += numeros.charAt(10 - i) * i;
				resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado != digitos.charAt(0)){
					  verErro('CPF informado &eacute; inv&aacute;lido!'); 
					  obj.value = '';
					  return false;
				}
				numeros = cpf.substring(0,10);
				soma = 0;
				for (i = 11; i > 1; i--)
					  soma += numeros.charAt(11 - i) * i;
				resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado != digitos.charAt(1)){
					verErro('CPF informado &eacute; inv&aacute;lido!'); 
					obj.value = '';
					return false;
				}
				return true;
		    }else{
				verErro('CPF informado &eacute; inv&aacute;lido!'); 
				obj.value = '';
				return false;
			}
    }
	}

	function FormataCpf(campo, teclapres){
		var tecla = teclapres.keyCode;
		var vr = new String(campo.value);
		vr = vr.replace(".", "");
		vr = vr.replace("/", "");
		vr = vr.replace("-", "");
		tam = vr.length + 1;
		if (tecla != 14)
		{
			if (tam == 4)
				campo.value = vr.substr(0, 3) + '.';
			if (tam == 7)
				campo.value = vr.substr(0, 3) + '.' + vr.substr(3, 6) + '.';
			if (tam == 11)
				campo.value = vr.substr(0, 3) + '.' + vr.substr(3, 3) + '.' + vr.substr(7, 3) + '-' + vr.substr(11, 2);
		}
	}

	function proc(opc){

		if (opc == 1 || opc == 3) { // alterar ou incluir
			var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");

			if (Form1.NomeUsuario.value == '') {
				verErro('Informe o Nome.');
		  	document.Form1.NomeUsuario.focus();
			} else if (Form1.EmailUsuario.value == '') {
				verErro('Informe o Email.');
		  	document.Form1.EmailUsuario.focus();
			} else if (VerificaEmail(document.Form1.EmailUsuario) == false) {
				verErro('Email inv&aacute;lido!');
		  	document.Form1.EmailUsuario.focus();				
			}	else if (Form1.LoginUsuario.value == '') {
				verErro('Informe o Login.');
		  	document.Form1.LoginUsuario.focus();
			} else if (Form1.CPFUsuario.value == '') {
				verErro('Informe o CPF.');
		  	document.Form1.CPFUsuario.focus();
			} else if (Form1.PerfilUsuario.value == '') {
				verErro('Informe o Perfil.');
		  	document.Form1.PerfilUsuario.focus();
			}
			<?php if (!$id_User) { ?>
				else if (Form1.SenhaUsuario.value == '') {
					verErro('Informe a Senha.');
			  		document.Form1.SenhaUsuario.focus();
				} else if(Form1.ConfirmaSenha.value == '' || Form1.ConfirmaSenha.value != Form1.SenhaUsuario.value){
					verErro('As senhas devem coincidir.');
			  		document.Form1.SenhaUsuario.focus();
				} else if (!strongRegex.test(Form1.SenhaUsuario.value)) {
					message = " A senha deve :<br> - Conter pelo menos 1 caractere alfabético minúsculo<br> - Conter pelo menos 1 caractere alfabético maiúsculo<br> - Conter pelo menos 1 caractere numérico<br> - Conter pelo menos um caractere especial<br> - Conter dez caracteres ou mais";
	   				verErro(message)
				}
			<?php } ?>
			else {
				document.Form1.submit();
			}
		}
	}

</script>

<?php 

	function mask($val, $mask) {
		$maskared = '';
		$k = 0;

		for($i = 0; $i<=strlen($mask)-1; $i++) {
			if($mask[$i] == '#') {
				if(isset($val[$k]))
					$maskared .= $val[$k++];
			} else {
				if(isset($mask[$i]))
					$maskared .= $mask[$i];
			}
		}
		return $maskared;
	}

	if ($CPF) {
		$CPF = mask($CPF,'###.###.###-##');
	} else {
		$CPF = "";
	}
?>

<div class="conteudopagina">
 	<form action="<?php $root;?>User.php" name="Form1" id="Form1" method="post"> 
    <input type="hidden" name="comm" id="comm" value="<?php echo $id_User ? 'alter' : 'new'; ?>">
    <input type="hidden" name="id_User" id="id_User" value="<?php echo $id_User; ?>">
    <input type="hidden" name="id_Banco" id="id_Banco" value="<?php echo $id_Banco; ?>">
    
		<li class="campo3colunas"><label>Nome Usu&aacute;rio</label>       
			<input type="text" name="NomeUsuario"  id="NomeUsuario" value="<?php echo $Name; ?>" maxlength="100">
		</li>
        
		<li class="campo3colunas"><label>E-mail</label>       
			<input type="text" name="EmailUsuario" id="EmailUsuario" value="<?php echo $Email; ?>" maxlength="70">
		</li>

		<li class="campo3colunas"><label>Login</label>
			<input type="text" name="LoginUsuario" id="LoginUsuario" value="<?php echo $Login; ?>" maxlength="70">
		</li>

		<li class="campo3colunas"><label>CPF</label>
			<input type="text" name="CPFUsuario" id="CPFUsuario" value="<?php echo $CPF; ?>" maxlength="14" onkeypress="FormataCpf(this, event);" onblur="VerificaCPF (this)">
		</li>

		<li class="campo3colunas">
			<label>Perfil</label>
			<select name="PerfilUsuario" id="PerfilUsuario">
				<option value="">Selecione...</option>
				<?php for ($r=0; $r < count($dados_perf); $r++) { ?>
					<?php 
						if ($Perfil == $dados_perf[$r]['i_Perfil']) {
							$select = "selected";
						} else { 
							$select = "";
						}  
					?>
					<option value="<?php echo $dados_perf[$r]['i_Perfil']; ?>" <?php echo $select; ?>><?php echo $dados_perf[$r]['Descricao']; ?></option>
				<?php } ?>
			</select>
		</li>
		
		<br clear="all">

		<?php if (!$id_User) { ?>
			<li class="campo3colunas"><label>Senha</label>
				<input type="password" name="SenhaUsuario" id="SenhaUsuario" maxlength="70">
			</li>

			<li class="campo3colunas"><label>Confirma Senha</label>
				<input type="password" name="ConfirmaSenha" id="ConfirmaSenha" maxlength="70">
			</li>
		<?php } ?>
        
    <div style="clear:both">&nbsp;</div>
    <label>Informe as regi&otilde;es que poder&atilde;o ser acessadas pelo usu&aacute;rio:</label>		

		<table style="width:400px">
			<thead>
				<tr>
					<th style="text-align:center">Op&ccedil;&atilde;o</th>
					<th style="text-align:center">Regi&atilde;o</th>
				</tr>
			</thead>
			<tbody>
				<?php for ($i=0; $i < count($dados_reg); $i++) { ?>
					<?php if ($dados_reg[$i]['Possui_Regiao']) {
						$check = "checked";
					} else {
						$check = "";
					} ?>
					<tr>
						<td style="text-align:center"><input type="checkbox" name="regiao[]" id="regiao<?php echo $i; ?>" value="<?php echo $dados_reg[$i]['ID_Regiao']; ?>" <?php echo $check; ?>></td>
						<td style="text-align:center"><?php echo $dados_reg[$i]['Nome_Regiao']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
 	</form>

	<div class="barrabotoes">
		<button class="botaoagg" type="button" onClick="javascript:proc(1);">Gravar Usu&aacute;rio</button>
		<?php if ($id_User) { ?>
			<button class="botaoagg" type="button" id="js-modal_pass">Alterar Senha</button>
		<?php } ?>
		<button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/user/User.php?comm=index';">Voltar</button>
	</div>
</div>

<?php 
	$funct = explode("/user/", $_SERVER["REQUEST_URI"]); 
	$url_funct = $funct[0].'/user/ajax_change_password.php'; 
?>	

<script>
  $(document).ready(function(){
    $("#js-modal_pass").on("click", function(){
      $(".modal-ext").show();
      $("#js-title").hide();
    });
    
    $("#close_modal").on("click", function(){
      $(".modal-ext").hide();
    });
       
    $("#change_pass").on("click", function(event){
      
      if(!$("#password").val()){
        verErro('Informe uma senha válida');
        event.preventDefault();
      } else if($("#password").val() != $("#confirm_password").val()){
        verErro('As senhas devem coincidir');
        event.preventDefault();
      } else {
		    var userID = $("#userID").val();
		    var password = $("#password").val();

				$.ajax({
				  type: "POST",
					url: '<?php echo urlencode($url_funct); ?>',
				  data: {userID: userID, password: password},
				  success: function(data) {
						verErro('Senha alterada com sucesso');
				  }
				})

        $(".modal-ext").hide();
        event.preventDefault();
      }
    })
  });
</script>

<!-- Modal -->
  <div class="modal-ext" style="display:none">
    <div class="bg-black"></div>

    <div class='modal-int'>
      <h1>Alterar Senha</h1>
      <div class="divisoriaamarelo"></div>
      
        <input type="hidden" name="userID" id="userID" value="<?php echo $id_User; ?>">

        <li class="campo2colunas">    
	    		<label>Nova Senha</label>
	    		<input type="password" name="password" id="password" size="20" maxlength="100">
				</li>

        <li class="campo2colunas">    
	    		<label>Confirmar Senha</label>
	    		<input type="password" name="confirm_password" id="confirm_password" size="20" maxlength="100">
				</li>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
          <button type="button" class="botaovgm" id="close_modal">Voltar</button>
          <button type="button" class="botaoagg" id="change_pass">Salvar</button>
        </li>

    </div>
  </div>
<!-- Fim modal -->