<?php

// Alterado Hicom (Gustavo) - 19/01/05 - possibilidade do usuário alterar o login separado da senha

$login = odbc_result(odbc_exec($db, "select login from Users where id= $userID "), 1);


require_once("../../../navegacao.php");

include_once("../consultaCoface.php");

$indicador = "dentroPrazo";

if (!function_exists('somadata')) {
    function somadata($data, $nDias)
    {
        if (!isset($nDias)) {
            $nDias = 1;
        }
        $aVet = Explode("-", $data);
        return date("Y-m-d", mktime(0, 0, 0, $aVet[1], $aVet[0] + $nDias, $aVet[2]));
    }
}
?>

<div class="conteudopagina">

    <?php

    $opc = isset($_REQUEST['opc']) ? $_REQUEST['opc'] : '';
    $valoresPermitidos = ['login', 'alteraSenhaLogin', 'senha'];
    if (!in_array($opc, $valoresPermitidos)) {
        $opc = '';
    }
    $opc = htmlspecialchars($opc, ENT_QUOTES, 'UTF-8');
    if (!isset($tipoCli)) {
        $tipoCli = isset($_REQUEST['tipoCli']) ? htmlspecialchars($_REQUEST['tipoCli'], ENT_QUOTES, 'UTF-8') : ''; // Corrigido
    }

    if ($opc == "login" && $_SESSION['pefil'] == 'F') { ?>
        <form action="<?php echo $root; ?>/role/access/Access.php" method="post">

            <?php // Adicionado por Michel Saddock 06/11/2006
            
                if ($tipoCli == "banco") { // Se o tipo de cliente é banco
                    echo "<input type=\"hidden\" name=tipoCli value=\"banco\">";
                    echo "<input type=\"hidden\" name=banco value=\"" . htmlspecialchars($banco, ENT_QUOTES, 'UTF-8') . "\">"; // Corrigido
                } elseif ($tipoCli == "comum") {
                    echo "<input type=\"hidden\" name=tipoCli value=\"comum\">";
                } elseif ($tipoCli == "consultor") {
                    echo "<input type=\"hidden\" name=tipoCli value=\"consultor\">";
                } elseif ($tipoCli == "funcionario") {
                    echo "<input type=\"hidden\" name=tipoCli value=\"funcionario\">";
                } else {
                    echo "<label>Erro: Tipo de cliente n&atilde;o encontrado!</label>";
                    die;
                }
                ?>

            <input type="hidden" name="comm" value="setAlterLog">
            <input type="hidden" name="idInform"
                value="<?php echo htmlspecialchars($field->getField("idInform"), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="opc" value="alteraSenhaLogin">
            <ul>
                <li class="campo3colunas"><label>Login:</label>
                    <INPUT type="text" size="35" name="login" class="caixa"
                        value="<?php echo htmlspecialchars($login, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </li>
            </ul>
            <div style="clear:both">&nbsp;</div>

            <label>(Utilize no m&iacute;nimo 10 caracteres)</label>
            <ul>
                <li class="campo3colunas"><label>Senha Nova:</label>
                    <INPUT type="password" size="35" name="senha" id="novaSenha">
                    <div id="message" style="color:#f33"></div>
                </li>
            </ul>
            <?php if ($msg) { ?>
                <label><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></label> <!-- Corrigido -->
            <?php } ?>
            <ul>
                <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
                    <button class="botaovgm" type="button"
                        onClick="window.location = '<?php echo $root; ?>role/access/Access.php';">Voltar</button>
                    <button class="botaoagm" type="button"
                        onClick="if(validaFormSimples()){this.form.submit();}">OK</button>
                </li>
            </ul>
        </form>
        <?php
    } else {           //Tela para o cliente Alterar Senha
    

        if ($indicador == "foraPrazo") //Caso já tenha passado do limite de 7 dias para alterar senha
        {
            echo "
    <table width=\"630px\" border=\"0\" cellspacing=\"0\" border-color=\"#D0D0D0\" align=\"center\">
    <tr><td>
    <font class=\"texto\">
    <B><h2>ATEN&Ccedil;&Atilde;O</h2></B><br><br>
    Prezado usu&aacute;rio,<br><br>
  
    por motivos de seguran&ccedil;a informamos que sua senha de acesso expirou. <br><br>
  
    Sendo assim, gentileza cadastrar a nova senha,  &eacute; importante ter no m&iacute;nimo 10 caracteres.<br><br>
  
    Esta medida visa manter a confidencialidade dos seus dados. <br><br>
  
    Atenciosamente, <br>
    COFACE - " . htmlspecialchars($nomeEmpSBCE, ENT_QUOTES, 'UTF-8') . "
    </font>
    </tr></td>
    </table> <br><br><br>
    ";

        } elseif ($indicador == "dentroPrazo") {


            echo ("
      <table width=\"630px\" border=\"0\" cellspacing=\"0\" border-color=\"#D0D0D0\" align=\"center\">
      <tr><td>
      <font class=\"texto\">
      <B><center>ATENÇÃO</center></B><br><br>
      Prezado usuário,<br><br>
    
      por motivos de segurança informamos que sua senha de acesso <b>expira em ");

            if (!isset($alterSenha)) {
                echo "7 dias";
            } else {
                echo htmlspecialchars($alterSenha, ENT_QUOTES, 'UTF-8');
            }

            echo ("</b>. <br><br>Sendo assim, gentileza cadastrar nova senha, é importante que ter no mínimo 10 caracteres.<br><br>
  
    Esta medida visa manter a confidencialidade dos seus dados. <br><br>
  
    Atenciosamente, <br>
    COFACE - " . htmlspecialchars($nomeEmpSBCE, ENT_QUOTES, 'UTF-8') . "
    </font>
    </tr></td>
    </table> <br><br><br>
    ");


        } else { ?>

        <?php } ?>
        <form action="<?php echo $root; ?>role/access/Access.php" method="post">

            <?php  //echo $indicador;
            
                if (($indicador == "dentroPrazo") || ($indicador == "foraPrazo")) {
                    $soma = somadata(date('d-m-Y'), 180);
                    echo "<input type=\"hidden\" name=comm value=\"alterSenha\">";
                    echo "<input type=\"hidden\" name=\"indicador\" value=\"" . htmlspecialchars($indicador, ENT_QUOTES, 'UTF-8') . "\">";
                    echo "<input type=\"hidden\" name=\"soma\" value=\"" . htmlspecialchars($soma, ENT_QUOTES, 'UTF-8') . "\">";

                    if ($tipoCli == "banco") { // Se o tipo de cliente é banco
                        $banco = isset($banco) ? htmlspecialchars($banco, ENT_QUOTES, 'UTF-8') : ''; // Sanitizado
                        echo "<input type=\"hidden\" name=\"tipoCli\" value=\"banco\">";
                        echo "<input type=\"hidden\" name=\"banco\" value=\"$banco\">";
                    } elseif ($tipoCli == "comum") {
                        echo "<input type=\"hidden\" name=tipoCli value=\"comum\">";
                    } elseif ($tipoCli == "consultor") {
                        echo "<input type=\"hidden\" name=tipoCli value=\"consultor\">";
                    } elseif ($tipoCli == "funcionario") {
                        echo "<input type=\"hidden\" name=tipoCli value=\"funcionario\">";
                    } else {
                        echo "<label>Erro: Tipo de cliente não encontrado! <br> AlterLog.php</label>";
                        //die;
                    }

                } else {

                    echo "<input type=\"hidden\" name=comm value=\"setAlterLog\">";

                    if ($tipoCli == "banco") // Se o tipo de cliente é banco
                    {
                        echo "<input type=\"hidden\" name=tipoCli value=\"banco\">";
                    }
                    if ($tipoCli == "funcionario") // Se o tipo de cliente é banco
                    {
                        echo "<input type=\"hidden\" name=tipoCli value=\"funcionario\">";
                    }
                    if ($tipoCli == "comum") // Se o tipo de cliente é banco
                    {
                        echo "<input type=\"hidden\" name=tipoCli value=\"comum\">";
                    }

                }


                ?>
            <input type="hidden" name="idInform"
                value="<?php echo htmlspecialchars($field->getField("idInform"), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="opc" value="<?php echo htmlspecialchars($opc, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="login" value="<?php echo htmlspecialchars($login, ENT_QUOTES, 'UTF-8'); ?>">

            <ul>
                <li class="campo3colunas"><label>Senha Atual:</label>
                    <INPUT type="password" size="35" name="senha" id="senhaAtual">
                </li>
            </ul>
            <div style="clear:both">&nbsp;</div>
            <ul>
                <li class="campo3colunas"><label>Senha Nova:</label>
                    <INPUT type="password" size="35" name="p1" id="novaSenha">
                    <div id="message" style="display:none;color:#f00"></div>
                </li>
            </ul>
            <div style="clear:both">&nbsp;</div>
            <label>(Utilize no m&iacute;nimo 10 caracteres)</label>
            <ul>
                <li class="campo3colunas"><label>Confirme a Nova Senha: </label>
                    <INPUT type="password" size="35" name="p2" id="confsenha">
                </li>
            </ul>
            <?php if ($msg) { ?>
                <label style="color:#FF0000"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></label>
            <?php } ?>


            <ul>
                <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">

                    <?php if ($indicador == "dentroPrazo") {
                        if ($tipoCli == "banco") // Se o tipo de cliente é banco
                        { ?>
                            <button class="botaoagm" type="button"
                                onClick="this.form.comm.value='openBanco';this.form.submit()">Continuar</button>
                            <?php
                        } elseif ($tipoCli == "comum") { ?>
                            <button class="botaoagg" type="button" onClick="this.form.comm.value='open';this.form.submit()">Manter a
                                senha atual</button>
                            <?php
                        } elseif ($tipoCli == "consultor") { ?>
                            <button class="botaoagg" type="button"
                                onClick="this.form.comm.value='openConsultor';this.form.submit()">Manter a senha atual</button>

                            <?php
                        } elseif ($tipoCli == "funcionario") { ?>

                            <button class="botaoagg" type="button"
                                onClick="this.form.comm.value='openFuncionario';this.form.submit()">Manter a senha atual</button>

                            <?php
                        } else {
                            echo "<label>Erro02: Tipo de cliente não encontrado! <br> AlterLog.php</label>";
                            die;
                        }

                    } elseif ($indicador == "foraPrazo") {
                        //Não exibe botão de continuar
                    } else {
                        ?>
                        <button class="botaovgm" type="button"
                            onClick="window.location = '<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/access/Access.php';">Voltar</button>

                    <?php }
                    ?>
                    <button class="botaoagg" type="button"
                        onClick="javascript: if (validaform())this.form.submit();">Alterar senha agora</button>
                </li>
            </ul>
        </form>

        <?php

    }


    ?>
</div>

<script language="javascript" type="text/javascript">

    function validaFormSimples() {
        var message = '';
        var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");
        var novaSenha = document.getElementById('novaSenha').value;

        if (novaSenha == '') {
            message = 'Por favor informe a nova senha.';
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerText = message;
            document.getElementById('novaSenha').focus();
            return false;
        } else if (!strongRegex.test(novaSenha)) {
            message = " A senha deve :<br> - Conter pelo menos 1 caractere alfabético minúsculo<br> - Conter pelo menos 1 caractere alfabético maiúsculo<br> - Conter pelo menos 1 caractere numérico<br> - Conter pelo menos um caractere especial<br> - Conter dez caracteres ou mais";
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerHTML = message;
            document.getElementById('novaSenha').focus();
            return false;
        } else {
            return true;
        }

    }

    function validaform() {
        var message = '';
        var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
        var novaSenha = document.getElementById('novaSenha').value;
        var senhaAtual = document.getElementById('senhaAtual').value;
        var confSenha = document.getElementById('confsenha').value

        if (senhaAtual == '') {
            message = 'Por favor informe a sua senha atual.';
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerText = message;
            document.getElementById('senhaAtual').focus();
            return false;
        } else if (novaSenha == '') {
            message = 'Por favor informe a nova senha.';
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerText = message;
            document.getElementById('novaSenha').focus();
            return false;
        } else if (document.getElementById('confsenha').value == '') {
            message = 'Por favor confirme a nova senha.';
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerText = message;
            document.getElementById('confsenha').focus();
            return false;
        } else if ((senhaAtual == novaSenha) || (senhaAtual == confSenha)) {
            message = 'A nova senha não pode ser igual à senha anterior.';
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerText = message;
            document.getElementById('novaSenha').focus();
            return false;
        } else if (novaSenha != confSenha) {
            message = 'A senha de confirmação deve ser igual à nova senha.';
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerText = message;
            document.getElementById('cofsenha').focus();
            return false;
        } else if (!strongRegex.test(novaSenha)) {
            message = " A senha deve :<br> - Conter pelo menos 1 caractere alfabético minúsculo<br> - Conter pelo menos 1 caractere alfabético maiúsculo<br> - Conter pelo menos 1 caractere numérico<br> - Conter pelo menos um caractere especial<br> - Conter oito caracteres ou mais";
            document.getElementById('message').style.display = 'block';
            document.getElementById('message').innerHTML = message;
            document.getElementById('novaSenha').focus();
            return false;
        } else {
            return true;
        }
    }
</script>