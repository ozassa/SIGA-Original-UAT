<?php

if (!isset($_SESSION)) {
	session_start();
}
include_once('../../../navegacao.php');

if (isset($_REQUEST['idNotification'])) {
	$_SESSION['idNotification'] = $field->getField("idNotification");
	$idNotification = $field->getField("idNotification");
} else {
	$idNotification = isset($_SESSION['idNotification']) ? $_SESSION['idNotification'] : 0;
}

/////////////////////////////////////////////////////////////////////////////
if (!function_exists('get_de_st_inform')) {
	// Retorna o status do Inform
	function get_de_st_inform($status)
	{
		if ($status == 1) {
			return "Novo";

		} elseif ($status == 2) {
			return "Preenchido";

		} elseif ($status == 3) {
			return "Validado";

		} elseif ($status == 4) {
			return "Analisado";

		} elseif ($status == 5) {
			return "Tarifado";

		} elseif ($status == 6) {
			return "Proposta";

		} elseif ($status == 7) {
			return "Confirmado";

		} elseif ($status == 8) {
			return "Alterado";

		} elseif ($status == 9) {
			return "Cancelado";

		} elseif ($status == 10) {
			return "Ap&oacute;lice";

		} elseif ($status == 11) {
			return "Encerrado";
		} else {
			return "Indefinido ($status)";
		}
	}
}
////////////////////////////////////////////////////////   
?>
<div class="conteudopagina">
	<script>


		function VerificaCPF(obj) {
			var cpf = obj.value;
			var numeros, digitos, soma, i, resultado, digitos_iguais;
			cpf = cpf.replace('.', '');
			cpf = cpf.replace('.', '');
			cpf = cpf.replace('-', '');

			if (cpf.length > 0) {
				digitos_iguais = 1;
				if (cpf.length < 11) {
					verErro('CPF informado est&aacute; incompleto!');
					obj.value = '';
					return false;
				}
				for (i = 0; i < cpf.length - 1; i++)
					if (cpf.charAt(i) != cpf.charAt(i + 1)) {
						digitos_iguais = 0;
						break;
					}

				if (!digitos_iguais) {
					numeros = cpf.substring(0, 9);
					digitos = cpf.substring(9);
					soma = 0;
					for (i = 10; i > 1; i--)
						soma += numeros.charAt(10 - i) * i;
					resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
					if (resultado != digitos.charAt(0)) {
						verErro('CPF informado &eacute; inv&aacute;lido!');
						obj.value = '';
						return false;
					}
					numeros = cpf.substring(0, 10);
					soma = 0;
					for (i = 11; i > 1; i--)
						soma += numeros.charAt(11 - i) * i;
					resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
					if (resultado != digitos.charAt(1)) {
						verErro('CPF informado &eacute; inv&aacute;lido!');
						obj.value = '';
						return false;
					}
					return true;
				} else {
					verErro('CPF informado &eacute; inv&aacute;lido!');
					obj.value = '';
					return false;
				}
			}



		}

		function FormataCpf(campo, teclapres) {
			var tecla = teclapres.keyCode;
			var vr = new String(campo.value);
			vr = vr.replace(".", "");
			vr = vr.replace("/", "");
			vr = vr.replace("-", "");
			tam = vr.length + 1;
			if (tecla != 14) {
				if (tam == 4)
					campo.value = vr.substr(0, 3) + '.';
				if (tam == 7)
					campo.value = vr.substr(0, 3) + '.' + vr.substr(3, 6) + '.';
				if (tam == 11)
					campo.value = vr.substr(0, 3) + '.' + vr.substr(3, 3) + '.' + vr.substr(7, 3) + '-' + vr.substr(11, 2);
			}
		}

		function validaformUsuario() {
			var message = '';
			var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");
			var password = $('#SenhaUsuario').val();
			var confirm_password = $('#ConfirmaSenha').val();
			var email = $('#EmailUsuario').val();
			var login = $('#LoginUsuario').val();

			$('#message').html('');


			if (email == '') {
				message = 'Por favor informe o e-mail.';
				$('#message').html(message);
				$('#EmailUsuario').focus();
				return false;
			}

			if (login == '') {
				message = 'Por favor informe o login.';
				$('#message').html(message);
				$('#LoginUsuario').focus();
				return false;
			}

			if (password == '') {
				message = 'Por favor informe a nova senha.';
				$('#message').html(message);
				$('#SenhaUsuario').focus();
				return false;
			}

			if (confirm_password == '') {
				message = 'Por favor confirme a nova senha.';
				$('#message').html(message);
				$('#ConfirmaSenha').focus();
				return false;
			}

			if (password != confirm_password) {
				message = 'A senha de confirmação deve ser igual à nova senha.';
				$('#message').html(message);
				$('#ConfirmaSenha').focus();
				return false;
			}

			if (!strongRegex.test(password)) {
				message = " A senha deve :<br> - Conter pelo menos 1 caractere alfabético minúsculo<br> - Conter pelo menos 1 caractere alfabético maiúsculo<br> - Conter pelo menos 1 caractere numérico<br> - Conter pelo menos um caractere especial<br> - Conter dez caracteres ou mais";
				$('#message').html(message);
				$('#SenhaUsuario').focus();
				return false;
			}

			var ckbox_array = document.getElementsByTagName('input');
			var ok = 0;
			for (var i = 0; i < ckbox_array.length; i++) {
				if (ckbox_array[i].type == 'checkbox' && ckbox_array[i].checked) {
					ok = 1;
				}
			}

			if (ok == 0) {
				message = 'Voc&ecirc; precisa marcar pelo menos uma apólice no cadastro de usu&aacute;rios.';
				$('#message').html(message);
				window.scrollTo(0, 0);
				return false;
			}

			return true;


		}



	</script>

	<?php
	$idUsuario = isset($_REQUEST['idUsuario']) ? $_REQUEST['idUsuario'] : 0;
	$sql = "SELECT
		U.id AS UserID,
		Inf.name AS Segurado,
		U.name AS NomeUsuario,
		U.CPF AS CpfUsuario,
		U.login AS LoginUsuario,
		U.password AS SenhaUsuario,
		ISNULL(U.email, U.login) AS EmailUsuario,
		CASE U.state
			WHEN 0 THEN 'Ativo'
			WHEN 1 THEN 'Inativo'
		END AS SituacaoUsuario
	FROM
		Inform Inf
	INNER JOIN Inform_Usuarios InformUsuarios ON
		InformUsuarios.idInform = Inf.id
	INNER JOIN Users U ON
		U.id = InformUsuarios.idUser
	WHERE
		U.id = ?
	ORDER BY U.name";

	$cur2 = odbc_prepare($db, $sql);
	odbc_execute($cur2, [$idUsuario]);




	?>
	<form action="<?php $root; ?>Inform.php" name="acesso_cliente" id="acesso_cliente" method="post">

		<input type="hidden" name="comm" id="comm" value="edit_acesso_cliente">
		<input type="hidden" name="idNotification" id="idNotification" value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="idUsuario" id="idUsuario" value="<?php echo htmlspecialchars($idUsuario, ENT_QUOTES, 'UTF-8'); ?>">

<input type="hidden" name="operacao" id="operacao" value="<?php echo $idUsuario > 0 ? 2 : 1; ?>">
<div id="message" style="color:#f33"></div>


		<li class="campo3colunas"><label>Nome Usu&aacute;rio *</label>
			<INPUT type="text" name="NomeUsuario" id="NomeUsuario"
				value="<?php echo odbc_result($cur2, 'NomeUsuario'); ?>" maxlength="100">
		</li>
		<!--        <li  class="campo3colunas"><label>CPF Usu&aacute;rio *</label>       
			 <INPUT type="text" name="CpfUsuario"  id="CpfUsuario" value="<?php echo odbc_result($cur2, 'CpfUsuario'); ?>" maxlength="14" onkeypress="FormataCpf(this, event);" onblur="VerificaCPF (this)">
		</li>-->
		<INPUT type="hidden" name="CpfUsuario" id="CpfUsuario" value="0" maxlength="14">
		<li class="campo3colunas"><label>E-mail *</label>
			<INPUT type="text" name="EmailUsuario" id="EmailUsuario"
				value="<?php echo odbc_result($cur2, 'EmailUsuario'); ?>" maxlength="70">
		</li>
		<li class="campo3colunas"><label>Login *</label>
			<?php
			$readonly = '';
			if (odbc_result($cur2, 'LoginUsuario') != '') {
				$readonly = 'readonly="readonly"';
			}
			?>
			<INPUT type="text" name="LoginUsuario" id="LoginUsuario"
				value="<?php echo odbc_result($cur2, 'LoginUsuario'); ?>" maxlength="70" <?php echo $readonly; ?>>
		</li>
		<li class="campo3colunas"><label>Senha *</label>
			<INPUT type="password" name="SenhaUsuario" id="SenhaUsuario"
				value="<?php echo odbc_result($cur2, 'SenhaUsuario'); ?>" maxlength="70">
		</li>
		<li class="campo3colunas"><label>Confirma Senha *</label>
			<INPUT type="password" name="ConfirmaSenha" id="ConfirmaSenha"
				value="<?php echo odbc_result($cur2, 'SenhaUsuario'); ?>" maxlength="70">
		</li>

		<div style="clear:both">&nbsp;</div>
		<label>Ap&oacute;lices que dever&atilde;o ser acessadas:</label>
		<?php
		odbc_free_result($cur2);
		$query = "SELECT 
              i.id, 
              i.name, 
              i.state, 
              i.startValidity, 
              i.i_Produto, 
              i.n_Apolice AS Apolice
          FROM 
              Inform i
          JOIN 
              Insured ins ON (ins.id = i.idInsured)
          WHERE 
              ins.idResp = ?
          ORDER BY 
              i.id";

		$cury = odbc_prepare($db, $query);
		odbc_execute($cury, [$userID]);

		$results = [];
while ($row = odbc_fetch_array($cury)) {
    $results[] = $row;
}
odbc_free_result($cury);

		$junta = '';
		?>

		<table style="width:400px" id="cnt_table">
			<thead>
				<tr>
					<th style="text-align:center">Op&ccedil;&atilde;o</th>
					<th style="text-align:center">Ap&oacute;lice</th>
					<th style="text-align:center">Ramo</th>
				</tr>

			</thead>
			<tbody>

				<?php
				$i = 0;
//				error_log(print_r($results, true));
				foreach ($results as $row) {
    $sql = "SELECT * 
            FROM Inform_Usuarios 
            WHERE idInform = ? AND idUser = ?";

    $curx = odbc_prepare($db, $sql);
    odbc_execute($curx, [$row['id'], $idUsuario]);

    if (odbc_result($curx, 'idInform') > 0) {
        $check = 'checked="checked"';
    } else {
        $check = '';
    }
    ?>
    <tr>
        <td style="text-align:center">
            <input type="checkbox" name="infor[]" id="infor_<?php echo $i; ?>" value="<?php echo $row['id']; ?>" <?php echo $check; ?> />
        </td>
        <td style="text-align:center"><?php echo $row['Apolice']; ?></td>
        <td style="text-align:center">
            <?php echo ($row['i_Produto'] == 1 ? 'Cr&eacute;dito Interno' : 'Cr&eacute;dito Externo'); ?>
        </td>
    </tr>
    <?php 
    $i++;
    odbc_free_result($curx);
}

				 ?>
			</tbody>
		</table>
	</form>


	<div class="barrabotoes">
		<button class="botaoagg" type="button"
			onClick="if(validaformUsuario()) document.acesso_cliente.submit();">Gravar Usu&aacute;rio</button>
		<button class="botaovgm" type="button"
			onClick="window.location = '<?php echo $host; ?>src/role/inform/Inform.php?comm=cliente_acessos&idInform=<?php echo $idInform; ?>';">Voltar</button>
	</div>


</div>