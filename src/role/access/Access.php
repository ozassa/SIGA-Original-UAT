<?php
require_once __DIR__ . '/../../../session_config.php';

if (isset($_GET['comm'])) {
    $comm = $_GET['comm'];
} elseif (isset($_POST['comm'])) {
    $comm = $_POST['comm'];
} else {
    $comm = "";
}

$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

require_once "../rolePrefix.php";

// Incluir funções de segurança
require_once("../../../security_functions.php");

// Incluir sistema híbrido de autenticação
require_once("../../../hybrid_auth.php");

// Verificar CSRF para requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !in_array($comm, ['exit'])) {
    csrf_middleware();
}

$validar = isset($_REQUEST['validar']) ? $_REQUEST['validar'] : false;

// usado no cert.
if (isset($_SESSION['validar'])) {
    $validar = $_SESSION['validar'];
}

if ($comm == 'exit') {
    $_SESSION['userID'] = '';
    $_SESSION['nameUser'] = '';
    $_SESSION['login'] = '';
    $_SESSION['pefil'] = '';
    $content = "../../../index.php";
    ?> <script> window.location = '<?php echo $content ?>'; </script> <?php
    exit();
}

function somadata($data, $nDias)
{
    if (!isset($nDias)) {
        $nDias = 1;
    }
    $aVet = Explode("-", $data);

    return date("Y-m-d", mktime(0, 0, 0, $aVet[1], $aVet[0] + $nDias, $aVet[2]));
}

function EntreDatas($inicio, $fim)
{
    $aInicio = Explode("-", $inicio);
    $aFim = Explode("-", $fim);
    $nTempo = mktime(0, 0, 0, $aFim[1], $aFim[0], $aFim[2]);
    $nTempo1 = mktime(0, 0, 0, $aInicio[1], $aInicio[0], $aInicio[2]);
    return round(($nTempo - $nTempo1) / 86400) + 1;
}

// Este trecho Faz a vericifa��o da senha caso a mesma tenha sido notificada para altera��o peri�dica.
$userLogin  = '';
$is_cert    = false;

if (isset($_SESSION["is_cert"])) {
    $userLogin = $_SESSION["login"];
    $senhaAtual = $_SESSION["senhaAtual"];
    unset($_SESSION["is_cert"]);
    unset($_SESSION["login"]);
    unset($_SESSION["senhaAtual"]);
    $is_cert = true;
} else {
    if (isset($_POST['login'])) {
        $userLogin = $_POST['login'];
        // limpa login
        if (!filter_var($userLogin, FILTER_VALIDATE_EMAIL)) {
            $userLogin = preg_replace("/[^0-9,a-z,A-Z,\.,_]/","",$userLogin);
        }
    }
    if (isset($_REQUEST["senhaAtual"]) || isset($_POST['password'])) {
        if (isset($_REQUEST['senhaAtual'])) {
            $senhaAtual = crypt($_REQUEST['senhaAtual'], SALT);
        } else {
            $senhaAtual = crypt($_REQUEST['password'], SALT);
        }
    }
}

// Não precisamos mais fazer hash aqui - será feito no sistema híbrido
$novaSenha  = isset($_REQUEST["novaSenha"]) ? $_REQUEST["novaSenha"] : false;
$confsenha  = isset($_REQUEST["confsenha"]) ? $_REQUEST["confsenha"] : false;
$op_opt     = isset($_REQUEST['operacao']) ? $_REQUEST['operacao'] : 0;

if ($op_opt == 3) {
    // Autenticar usuário antes de alterar senha
    $auth_result = hybrid_authenticate($userLogin, $_REQUEST['senhaAtual'], $db);
    
    if (!$auth_result['success']) {
        $content = "../../../index.php?erro=1";
        header("Location:$content");
        die();
    }
    
    $user = $auth_result['user'];
    $alterarSenhaDepois = 0;
    
    if ($user['login'] != '') {
        if ($novaSenha == $confsenha) {
            // Usar novo sistema de hash para a nova senha
            if (hybrid_change_password($novaSenha, $user['id'], $db)) {
                // Definir senha atual como a nova (para compatibilidade com código existente)
                $senhaAtual = hybrid_password_hash($novaSenha);
            } else {
                $content = "../../../index.php?erro=1";
                header("Location:$content");
                die();
            }
        } else {          
            $content = "../../../index.php?erro=1";
            header("Location:$content");
            die();
        }
    } else {
        $content = "../../../index.php?erro=1";
        header("Location:$content");
        die();
    }
}

if (($validar == 'login' || $op_opt == 3) && $userLogin != '' && $senhaAtual != '') {
    // Usar sistema híbrido de autenticação com proteção brute force
    $auth_result = hybrid_authenticate($userLogin, ($op_opt == 3) ? $_REQUEST['senhaAtual'] : $_REQUEST['password'], $db);
    
    if (!$auth_result['success']) {
        // Log da tentativa de login falhada
        error_log("Login failed for user: $userLogin - " . $auth_result['message']);
        
        // Atualizar contador de tentativas na sessão (compatibilidade)
        if (!isset($_SESSION['tentativaSenha_' . $userLogin])) {
            $_SESSION['tentativaSenha_' . $userLogin] = 0;
        }
        $_SESSION['tentativaSenha_' . $userLogin]++;
        $_SESSION['tentativaLoginUsuario'] = $userLogin;
        
        $content = "../../../index.php?erro=1";
        header("Location:$content");
        die();
    }
    
    // Login bem-sucedido - obter dados do usuário
    $cur = $auth_result['user'];
    $perfil = $cur ? $cur['perfil'] : '';
    
    // Log informativo se senha foi atualizada
    if (isset($auth_result['password_upgraded']) && $auth_result['password_upgraded']) {
        error_log("Password upgraded for user: $userLogin");
    }

    if($perfil == "F") {
        session_regenerate_id(true);
       
       $comm = "func";
       $_SESSION['userID'] = $cur ? $cur['id'] : '';
       $userID =  $_SESSION['userID'];
       $_SESSION['nameUser']   = $cur ? $cur['name'] : '';
       $_SESSION['login']  =  $cur ? $cur['login'] : '';
       $_SESSION['pefil']  = $perfil;
       $login = $_SESSION['login'];
       $per   = $perfil;
       
    } else if($perfil == "C") {
        session_regenerate_id(true);
       
       $comm = "client";
       $_SESSION['userID'] = $cur ? $cur['id'] : '';
       $userID =  $_SESSION['userID'];
       $_SESSION['nameUser']   = $cur ? $cur['name'] : '';
       $_SESSION['login']  =  $cur ? $cur['login'] : '';
       $_SESSION['pefil']  =  $perfil;
       $login = $_SESSION['login'];
       $per   = $perfil;
       
    } else if($perfil == "B") {
        session_regenerate_id(true);
       
       $comm = "client";
       $_SESSION['userID'] = $cur ? $cur['id'] : '';
       $userID =  $_SESSION['userID'];
       $_SESSION['nameUser']   = $cur ? $cur['name'] : '';
       $_SESSION['login']  =  $cur ? $cur['login'] : '';
       $_SESSION['pefil']  = $perfil;
       $login = $_SESSION['login'];
       $per   = $perfil;
       
    } else if($perfil == "CO") {
        session_regenerate_id(true);
       
       $comm = "client";
       $_SESSION['userID'] = $cur ? $cur['id'] : '';
       $userID =  $_SESSION['userID'];
       $_SESSION['nameUser']   = $cur ? $cur['name'] : '';
       $_SESSION['login']  =  $cur ? $cur['login'] : '';
       $_SESSION['pefil']  = $perfil;
       $login = $_SESSION['login'];
       $per   = $perfil;     
      
    } else {
        // Interaktiv 06/05/2015
        // Nova fun��o de Bloquear o acesso do usu�rio
        $stmt    = odbc_prepare($db, "SELECT id, perfil, name, login, password, email, tentativaSenha
                                FROM Users 
                                WHERE login = ? AND state = '0'");
        $resulx = odbc_execute($stmt, array($userLogin));
        $curUsr = odbc_fetch_array($stmt);

        $usuarioID = $curUsr ? $curUsr['id'] : '';
        $perfil = $curUsr ? $curUsr["perfil"] : '';
        $name     = $curUsr ? $curUsr['name'] : '';
        $login    = $curUsr ? $curUsr['login'] : '';
        $email    = $curUsr ? $curUsr['email'] : '';
        $tentativaSenha = $curUsr ? $curUsr['tentativaSenha'] : '';

        if ($usuarioID != "") {
            $sqlUpUser = "UPDATE Users SET tentativaSenha = ? WHERE id = ?";
            $curUpUser = odbc_prepare($db, $sqlUpUser);
            odbc_execute($curUpUser, array($tentativaSenha, $usuarioID));
        }

        $_SESSION['tentativaSenha_' . $userLogin . ''] = $tentativaSenha;
        $_SESSION['tentativaPefilUsuario'] = $perfil;
        $_SESSION['tentativaLoginUsuario'] = $userLogin;

        if (isset($msg_email)) {
            $content = "../../../index.php?msg=" . rawurlencode($msg_email);
        } else {
            $content = "../../../index.php?erro=1";
        }

        header("Location:$content");

        die();
    }

    if ($userLogin) {
        unset($_SESSION['tentativaSenha_' . $userLogin . '']);
        unset($_SESSION['tentativaPefilUsuario']);
        unset($_SESSION['tentativaLoginUsuario']);
    }

    $alterarSenhaDepois = isset($alterarSenhaDepois) ? $alterarSenhaDepois : null;

    if ($alterarSenhaDepois == 0) {
        /****************************************/

        //teste para verificar se ele deve alterar a senha
        $id = $userID; //id do usu�rio (Users)
        $dataHoje = date('Y-m-d'); //data atual

        $q = "SELECT alterSenha FROM Users WHERE id = ?";

        $cur = odbc_prepare($db, $q);

        odbc_execute($cur, [$id]);

        while (odbc_fetch_row($cur)) {
            $alterSenha = odbc_result($cur, 'alterSenha');
        }

        if (($alterSenha) == "") //Caso o campo alterSenha seja NULL - primeiro acesso apos criacao
        {
            $dataAcrescimo = somadata(date('d-m-Y'), 6); //prazo de 7 dias para trocar senha
            $q2 = "UPDATE Users SET alterSenha = ? WHERE id = ?";
            $cur2 = odbc_prepare($db, $q2);
            odbc_execute($cur2, [$dataAcrescimo, $id]);

            $indicador = "dentroPrazo";
            $comm = "open";

            $_SESSION['resetSenha']['erroHiddenCode'] = 3;
            $_SESSION['resetSenha']['erroHiddenUser'] = $id;
            $_SESSION['resetSenha']['erroHiddenMotivo'] = $indicador;
            $_SESSION['resetSenha']['alterSenha'] = $alterSenha;

            $content = "../../../index.php"; // Alerta de altera��o de senha
            header("Location:$content");
        } else {

            //Data atual
            $dataHoje = substr($dataHoje, 0, 10); //Pegando somente a data sem timestamp
            //Formatando a data para o padr�o Brasil
            $dia = substr($dataHoje, 8, 2);
            $mes = substr($dataHoje, 5, 2);
            $ano = substr($dataHoje, 0, 4);
            $dataHojeBr = $dia . '-' . $mes . '-' . $ano;
            //Fim Formatando a data para o padr�o Brasil

            //Data limite de troca de senha
            $alterSenha = substr($alterSenha, 0, 10); //Pegando somente a data sem timestamp
            //Formatando a data para o padr�o Brasil
            $dia = substr($alterSenha, 8, 2);
            $mes = substr($alterSenha, 5, 2);
            $ano = substr($alterSenha, 0, 4);
            $alterSenha = $dia . '-' . $mes . '-' . $ano;
            //Fim Formatando a data para o padr�o Brasil

            //EntreDatas( $inicio, $fim )
            $intervalo = EntreDatas($dataHojeBr, $alterSenha);
            $intervalo = $intervalo - 1;

            //Data atual acrescida de 6 meses. Tempo de troca de senha � de 6 em 6 meses
            $soma = somadata($dataHojeBr, 180);
            //Se ainda esta dentro dos 7 dias a tela � a mesma para todos os tipos de cliente
            if (($intervalo >= 0) && ($intervalo < 7)) {
                //$comm = "open";
                $indicador = "dentroPrazo";
                
                $_SESSION['resetSenha']['erroHiddenCode'] = 3;
                $_SESSION['resetSenha']['erroHiddenUser'] = $id;
                $_SESSION['resetSenha']['erroHiddenMotivo'] = $indicador;
                $_SESSION['resetSenha']['alterSenha'] = $alterSenha;

                $content = "../../../index.php";
                header("Location:$content");
            } elseif ($intervalo < 0) //Se j� estourou o prazo
            {
                $indicador = "foraPrazo";

                $_SESSION['resetSenha']['erroHiddenCode'] = 3;
                $_SESSION['resetSenha']['erroHiddenUser'] = $id;
                $_SESSION['resetSenha']['erroHiddenMotivo'] = $indicador;
                $_SESSION['resetSenha']['alterSenha'] = $alterSenha;

                $content = "../../../index.php";
                header("Location:$content");
            }
        }
        /****************************************/
    }
}

if ($comm == "") {
    if ($_SESSION['pefil'] == 'F' && ($comm != 'usuarios')) {
        $comm = "func";
    } else if (($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B' || $_SESSION['pefil'] == 'CO') && ($comm != 'usuarios')) {
        $comm = "client";
    }
}

// Verificar esta parte cuidadosamente
if ($_SESSION['pefil'] != '' && $_SESSION['userID'] != "") {
    $qry = "SELECT a.id, a.name, c.login
        FROM Role a
        INNER JOIN UserRole b ON b.idRole = a.id
        INNER JOIN Users c ON c.id = b.idUser
        WHERE c.id = ? AND c.perfil = ?
        ORDER BY UPPER(a.name), c.login";

    $cur = odbc_prepare($db, $qry);
    odbc_execute($cur, array($_SESSION['userID'], $_SESSION['pefil']));

    //print $qry;
    $x = 0;

    //$cur = odbc_exec($db, $qry);
    while (odbc_fetch_row($cur)) {
        $x = $x + 1;
        $name = odbc_result($cur, 'name');
        $id = odbc_result($cur, 'id');
        $role[$name] = $id . '<br>';
    }

    odbc_free_result($cur);

    //*************************************************************
    if ($comm == "func") {
        //$content = "../functionary/interf/Login.php";

        //require_once("../../../../site/func/Login.php");
        $tipoCli = 'funcionario';

        $comm == "functionaryLogin";

        //Script para verificar a troca de senha de 6 em 6 meses
        //require_once("alterSenha.php");

        $content = "../../../main.php";

        require_once "../../../home.php";

        //require_once("functionaryLogin.php");
    } else if ($comm == "exit") {
        $_SESSION["user"] = null;
        $_SESSION['userID'] = null;
        //$content = "../functionary/interf/Login.php";

        ?><script>window.location  = "<?php echo '../../../'; ?>";</script>;
        <?php

    } else if ($comm == "exitclient") {
        $_SESSION["user"] = null;
        $_SESSION['userID'] = null;

        ?><script>window.location  = "<?php echo '../../../'; ?>";</script>;
        <?php

        //$content = "../client/interf/Login.php";
        require_once "../../../home.php";
    } else if ($comm == "functionaryLogin") {

        //Verifica se login ou senha est�o em branco
        if (empty($login) || empty($password)) {
            $msg = "Login ou Senha <br>n�o pode ser vazio !!!";
            //$content = "../functionary/interf/Login.php";
            ?><script>window.location  = "<?php echo '../../../'; ?>";</script>;
               <?php die; ?>
        <?php } else {
            require_once "functionaryLogin.php";
        } ?>

        <?php
        
        //Se o funcion�rio logou com sucesso
        if ($forward == "success") {
            if ($_SESSION['pefil'] == "F") {
                $tipoCli = "funcionario";
            }
            require_once "../../../home.php";
        } else //Caso senha ou login estejam incorretos
        {
            $msg = "Usu&aacute;rio e senha inv&aacute;lidos";
            // $content = "../functionary/interf/Login.php";
            //require_once("../../../../site/func/index.php");
            ?><script>window.location  = "<?php echo '../../../'; ?>";</script>;
               <?php
        }

    } else if ($comm == "client") {
        $sql = "SELECT COUNT(id) AS id FROM Insured WHERE idResp = ?";
        $cur1 = odbc_prepare($db, $sql);
        odbc_execute($cur1, array($_SESSION['userID']));

        $id_user = odbc_result($cur1, 'id');

        $_SESSION['id_user'] = $id_user;

        odbc_free_result($cur1);

        if (!$_SESSION['id_user']) {
            $sql = "SELECT COUNT(b.idInform) AS idx FROM Inform a
                    INNER JOIN Inform_Usuarios b ON b.idInform = a.id
                    WHERE b.idUser = ?";
            $cur2 = odbc_prepare($db, $sql);
            odbc_execute($cur2, array($_SESSION['userID']));
            $idx = odbc_result($cur2, 'idx');

            $_SESSION['idx'] = $idx;
            odbc_free_result($cur2);
        }

        $bancoOB = isset($role["bancoOB"]) ? $role["bancoOB"] : false;

        if (!check_menu(['bancoBB'], $role) || (!$bancoOB) || (!check_menu(['bancoParc'], $role))) {

            $comm = "open";

            require_once "../inform/Inform.php";

        } else {

            $content = "../../../main.php";
        }

        require_once "../../../home.php";

    } else if ($comm == "changeImporter") {

        $title = 'Informa��es do Segurado';
        $content = "../client/interf/ViewClient.php";
        require_once "../../../home.php";

    } else if ($comm == "recoverClient") {
        $content = "../client/interf/RecoverPassword.php";
        require_once "../../../home.php";

    } else if ($comm == "setRecoverPassword") {
        require_once "recoverPassword.php";
        if ($forward == "success") {
            require_once "../client/interf/Login.php";
        } else {
            require_once "../client/interf/RecoverPassword.php";
        }

    } else if ($comm == "createLog") {
        $content = "../client/interf/CreateLog.php";

    } else if ($comm == "setCreateLog") {
        require_once "setCreateLog.php";

        //Alterado por Michel Saddock 27/09/2006
        if ($forward == "success") {
            $comm = "open2";
            require_once "../inform/Inform.php";
        }
        //Fim Alterado por Michel Saddock 27/09/2006

        else {
            $content = "../client/interf/CreateLog.php";
            require_once "../../../home.php";
        }
    } else if ($comm == "change") //Direciona para alterar senha de clientes tipo banco
    {

        if (check_menu(['bancoBB', 'bancoOB', 'bancoParc'], $role)) {
            $tipoCli = "banco";
            $title = "Altera��o de Senha";
            $content = "../client/interf/AlterLog.php";
            require_once "../../../home.php";

        } elseif ($_SESSION['pefil'] == "F") {

            $tipoCli = "funcionario";
            $title = "Altera��o de Senha";
            $content = "../client/interf/AlterLog.php";
            require_once "../../../home.php";
        } else {

            echo "N�o foi possivel identificar o tipo de cliente<br> Erro: comm=change";
            die();
        }
    } else if ($comm == "setAlterLog") {
        if (!isset($tipoCli)) {
            $tipoCli = $_REQUEST['tipoCli'];
        }

        if ($tipoCli == "comum") {
            $title = '�rea do Cliente';

            require_once "changeClient.php";

            if ($forward == "success") {
                $content = "../client/interf/ViewClient.php";
            } else {
                $content = "../client/interf/AlterLog.php";
            }
            require_once "../../../home.php";

        } else if ($tipoCli == "banco" || $tipoCli == "funcionario") { // Se o cliente � um banco

            $title = '';
            $senha = "senha";

            require_once "changeClient.php";

            if ($forward == "success") {
                $title = "Notifica��es";
                $content = "../../../main.php";
                require_once "../../../home.php";
            } else {
                $content = "../client/interf/AlterLog.php";
                require_once "../../../home.php";
            }

        } else {
            echo "Erro:Tipo de cliente n�o reconhecido!<br> Comm == setAlterLog ";
            die;
        }

    } else if ($comm == "clientLogin") {

        require_once "clientLogin.php";

        if ($forward == "success") {
            if (check_menu(['bancoBB', 'bancoOB', 'bancoParc'], $role)) {
                $tipoCli = "banco";
                //Script para verificar a troca de senha de 6 em 6 meses
                require_once "alterSenha.php";
            }
            else if ($role["client"] && $_SESSION['pefil'] != "CO") {
                $tipoCli = "comum";
                //Script para verificar a troca de senha de 6 em 6 meses
                require_once "alterSenha.php";

            } elseif ($_SESSION['pefil'] == "CO") //Se for consultor
            {
                $tipoCli = "consultor";
                require_once "alterSenha.php";

            } else {
                echo "N�o foi poss�vel identificar o tipo de cliente";
                die;
            }
        } else {
            $msg = "Usu&aacute;rio e senha inv&aacute;lidos";
            $content = "../client/interf/Login.php";
            require_once "../../../home.php";
        }

        // alterado Hicom (Gustavo) - cadastro de usu�rios
    } else if ($comm == "usuarios") {
        if (check_menu(['generalManager'], $role)) {
            $title = "Lista de Usu�rios";

            $content = "../functionary/interf/usuarios.php";
            require_once "../../../home.php";
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo "Acesso n�o autorizado.";
            exit;
        }
    } else if ($comm == "usuariosDet") {
        if (check_menu(['generalManager'], $role)) {
            $title = "Cadastro de Usu�rios";
            $content = "../functionary/interf/usuariosDet.php";
            require_once "../../../home.php";
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo "Acesso n�o autorizado.";
            exit;
        }

        // alterado F�bio Campos (analista - elumini) - Log Administrativo
    } else if ($comm == "log") {
        $title = "Consulta de  Logs efetuados";
        $content = "../functionary/interf/log.php";
        require_once "../../../home.php";
        // fim

    } else if ($comm == "database") {
        require_once "../db/db.php";

        // alterado Cristiano Eugenio (elumini) - Log Administrativo
    } else if ($comm == "loadLog") {
        $title = "Detalhes do log";
        $content = "../functionary/interf/detalhe_log.php";
        require_once "../../../home.php";
        // fim
    }
    //Adicionado por Michel Saddock 06/10/2006
    else if ($comm == "alterSenha") {

        $title = '';
        require_once "changeClient.php";

        if (check_menu(['bancoBB', 'bancoOB', 'bancoParc'], $role)) {
            $tipoCli = 'banco';
        } elseif ($_SESSION['pefil'] == 'F') {
            $tipoCli = 'funcionario';
        } elseif ($_SESSION['pefil'] == "CO") {
            $tipoCli = 'consultor';
        } else {
            $tipoCli = 'comum';
        }

        if ($forward == "success") {
            if ($tipoCli == "banco") {
                $title = "Notifica��es";
                $content = "../../../main.php";
                require_once "../../../home.php";
            } elseif ($tipoCli == "comum") {
                //echo "AQ";
                //break;
                $comm = "open";
                require_once "../inform/Inform.php";
            } elseif ($tipoCli == "consultor") {
                $id = $userID;
                $comm = "escolheConsultor";
                $content = "../area_consultor/consultorInforme.php";
                require_once "../../../home.php";
                exit();
            } elseif ($tipoCli == "funcionario") {
                $title = "Notifica��es";
                $content = "../../../main.php";
                require_once "../../../home.php";
            } else {
                echo "Erro: Tipo de cliente n�o encontrado! <br> Access >> alterSenha";
                die;
            }
        } else {
            $content = "../client/interf/AlterLog.php";
            require_once "../../../home.php";
        }
    } else if ($comm == "open") { //Abre o informe do Cliente comum (N�o banco)
        require_once "../inform/Inform.php";
    } else if ($comm == "openBanco") //Abre o informe do Cliente banco
    {
        $title = "Notifica��es";
        $content = "../../../main.php";
        require_once "../../../home.php";
    } else if ($comm == "openConsultor") //Abre o informe do Cliente banco
    {

        $id = $userID;
        $comm = "escolheConsultor";
        $title = "AREA CONSULTOR";
        $content = "../area_consultor/consultorInforme.php";
        require_once "../../../home.php";
        exit();
    } else if ($comm == "openFuncionario") //Abre o informe do Cliente banco
    {
        $title = "Notifica��es";
        $content = "../../../main.php";
        require_once "../../../home.php";
    }

    // Fim Adicionado por Michel Saddock 06/10/2006
    //definir agrupamento de notificacoes
    else if ($comm == "agrupar") {
        $title = "Tipos de Notifica��es";
        //agrupar arvore
        $content = "../notification/interf/tree_menu_info.php";
        //funcionario
        require_once "../../../home.php";
    }
    //definir agrupamento de notificacoes
    else if ($comm == "arvore") {
        $title = "Tipos de Notifica��es";
        //exibe arvore
        $content = "../../../main.php";
        //funcionario
        require_once "../../../home.php";
    }

} else {
    if ($comm == "createLog") {
        $content = "../client/interf/CreateLog.php";
    } else {

        $content = "../../../index.php?erro=1";
    }
    
    header("Location:$content");
}

function validaSQLInjection($userLogin, $db)
{
    $sql = "SELECT id 
            FROM Users 
            WHERE login = ?
                    AND state = 0";

    $sqlPrep    = odbc_prepare($db, $sql);
    $sqlRs      = odbc_execute($sqlPrep, array($userLogin));
    $sqlCur     = odbc_fetch_array($sqlPrep);

    $id         = $sqlCur ? $sqlCur['id'] : '';

    if ($id) {
        return true;
    } else {
        return false;
    }
}
