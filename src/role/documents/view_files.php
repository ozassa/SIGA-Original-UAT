<?php 
    error_reporting(E_ALL);
    ini_set("display_errors", 0);
    
    include_once("../../../config.php");
    require_once ("../rolePrefix.php");
    include_once("policyData.php");

    // Configurações de segurança
    define('VIEW_FILES_LOG', 'view_files_security.log');
    
    // Whitelist de tipos de documento permitidos
    $allowed_documents = array(
        'apolice', 'apolice_real', 'aprovacao', 'modulos', 
        'parcela', 'carta', 'carta_credito', 'cond_juros', 'prop'
    );
    
    /**
     * Função para log de segurança
     */
    function view_files_security_log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $log_entry = "[{$timestamp}] [{$level}] IP: {$ip} - UA: {$user_agent} - {$message}\n";
        file_put_contents(VIEW_FILES_LOG, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Valida e sanitiza parâmetros de entrada
     */
    function validate_input($value, $type, $max_length = 50) {
        if (empty($value)) {
            return false;
        }
        
        switch ($type) {
            case 'numeric':
                return filter_var($value, FILTER_VALIDATE_INT, array(
                    'options' => array('min_range' => 1, 'max_range' => 999999)
                ));
            case 'alphanumeric':
                return preg_match('/^[a-zA-Z0-9_-]+$/', $value) && strlen($value) <= $max_length;
            case 'document':
                return preg_match('/^[a-zA-Z_]+$/', $value) && strlen($value) <= $max_length;
            default:
                return false;
        }
    }
    
    /**
     * Canonicaliza e valida path de arquivo
     */
    function validate_file_path($file_path, $allowed_base_dir) {
        // Canonicaliza o path
        $real_path = realpath($file_path);
        $real_base = realpath($allowed_base_dir);
        
        if ($real_path === false || $real_base === false) {
            return false;
        }
        
        // Verifica se o arquivo está dentro do diretório permitido
        return strpos($real_path, $real_base) === 0;
    }
    
    /**
     * Verifica autorização de acesso ao arquivo
     */
    function check_file_authorization($idInform, $document) {
        // Aqui você deve implementar sua lógica de autorização
        // Por exemplo, verificar se o usuário tem permissão para acessar este documento
        // Esta é uma implementação básica:
        
        if (empty($_SESSION['user_id'])) {
            return false;
        }
        
        // Adicione aqui verificações específicas de permissão
        // baseadas no tipo de documento e no usuário
        
        return true; // Substituir por lógica real de autorização
    }

    // Validação e sanitização de parâmetros
    $idInform = validate_input($_REQUEST["idInform"] ?? '', 'numeric');
    $document = validate_input($_REQUEST["document"] ?? '', 'document', 20);
    $parc_num = validate_input($_REQUEST["parc"] ?? '0', 'numeric');
    
    // Log da tentativa de acesso
    view_files_security_log("Tentativa de acesso - idInform: {$idInform}, document: {$document}, parc: {$parc_num}");
    
    // Validações de segurança
    if ($idInform === false) {
        view_files_security_log('Acesso negado - idInform inválido', 'SECURITY');
        http_response_code(400);
        die('Parâmetro idInform inválido');
    }
    
    if ($document === false || !in_array($document, $allowed_documents)) {
        view_files_security_log('Acesso negado - tipo de documento inválido: ' . $document, 'SECURITY');
        http_response_code(400);
        die('Tipo de documento não permitido');
    }
    
    if ($parc_num === false) {
        $parc_num = 0; // Valor padrão seguro
    }
    
    // Verifica autorização
    if (!check_file_authorization($idInform, $document)) {
        view_files_security_log('Acesso negado - sem autorização: idInform=' . $idInform . ', document=' . $document, 'SECURITY');
        http_response_code(403);
        die('Acesso não autorizado');
    }

    $stmt    = odbc_prepare($db, "SELECT Inf.policyKey, Inf.i_Produto FROM Inform Inf WHERE Inf.id = ?");
    $resulx = odbc_execute($stmt, array($idInform));
    $cur = odbc_fetch_array($stmt);
    $key = $cur ? $cur['policyKey'] : '';
    $i_Produto = $cur ? $cur['i_Produto'] : '';

    $arq_name = "";
    $view = "";

    switch ($document) {
        case "apolice":
            $arq_name = "Apolice.pdf";
            //$view = "view_apolice.php";

            

            if (strpos(strtolower($dados['CodSusep']), strtolower("Grandes")) !== false) {
                $view = "view_apolice.php";
              
            } else {
                $view = "view_apolice_GA.php";
            }           

            $variavel = $view;
            $logFile = 'L:\logs_siga\arquivo.txt';
            $logMessage = date('Y-m-d H:i:s') . ' - ' . $variavel . "\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            break;
        case "apolice_real":
            $arq_name = "ApoliceReal.pdf";
            $view = "view_apolice_real.php";

            break;
        case "aprovacao":
            $arq_name = "aprovacao.pdf";
            $view = "view_aprovacao.php";

            break;
        case "modulos":
            $arq_name = "modulos.pdf";
            $view = "view_module.php";

            break;
        case "parcela":
            //$arq_name = "Parcela".$parc_num.".pdf";
            //$view = "view_parcela.php";

 			if (strpos(strtolower($dados['CodSusep']), strtolower("Grandes")) !== false) {
            $arq_name = "Parcela".$parc_num.".pdf";
            $view = "view_parcela_2.php";
            } else {
                $arq_name = "Parcela".$parc_num.".pdf";
            $view = "view_parcela.php";
            }
            break;
        case "carta":
            $arq_name = "Carta.pdf";

            //if ($i_Produto == 1) {
            //   $view = "view_carta_dom.php";
            //} else {
            //   $view = "view_carta.php";
            //}


            if (strpos(strtolower($dados['CodSusep']), strtolower("Grandes")) !== false) {
                $view = "view_carta.php";
            } else {
                $view = "view_carta_dom.php";
            }
        
            break;
        case "carta_credito":
            $arq_name = "CartaCredito.pdf";

            if ($i_Produto == 1) {
                $view = "view_carta_credito_dom.php";
            } else {
                $view = "view_carta_credito.php";
            }

            break;
        case "cond_juros":
            $arq_name = "CondJuros.pdf";

            if ($i_Produto == 1) {
                $view = "view_condjuros_dom.php";
            } else {
                $view = "view_condjuros.php";
            }

            break;
        case "prop":
            $arq_name = "Prop.pdf";

            if ($i_Produto == 1) {
                $view = "view_prop_dom.php";
            } else {
                $view = "view_prop.php";
            }

            break;
    }

    if ($arq_name && $view) {
        // Constrói path seguro do arquivo
        $safe_filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $key . $arq_name);
        $file_path = $root . "download/" . $safe_filename;
        $download_dir = $root . "download/";
        
        // Valida path do arquivo
        if (!validate_file_path($file_path, $download_dir)) {
            view_files_security_log('Path traversal tentado - arquivo: ' . $file_path, 'SECURITY');
            http_response_code(403);
            die('Acesso ao arquivo negado');
        }
        
        // Valida nome do view
        $safe_view = preg_replace('/[^a-zA-Z0-9._-]/', '', $view);
        if ($safe_view !== $view || strpos($view, '..') !== false) {
            view_files_security_log('View inválido tentado: ' . $view, 'SECURITY');
            http_response_code(403);
            die('View não permitido');
        }
        
        /* Verifica se o documento já existe */
        if (!file_exists($file_path)) {
            // Valida se o arquivo de view existe antes de incluir
            $view_path = $safe_view;
            if (!file_exists($view_path)) {
                view_files_security_log('View não encontrado: ' . $view_path, 'ERROR');
                http_response_code(404);
                die('View não encontrado');
            }
            require_once ($view_path);
        }

        // Verifica novamente se o arquivo foi criado e é válido
        if (!file_exists($file_path)) {
            view_files_security_log('Arquivo não criado após view: ' . $file_path, 'ERROR');
            http_response_code(404);
            die('Documento não encontrado');
        }
        
        // Verifica se é realmente um PDF
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $file_path);
        finfo_close($file_info);
        
        if ($mime_type !== 'application/pdf') {
            view_files_security_log('Tipo de arquivo inválido: ' . $mime_type . ' para ' . $file_path, 'SECURITY');
            http_response_code(415);
            die('Tipo de arquivo não suportado');
        }
        
        // Log do acesso bem-sucedido
        view_files_security_log('Acesso autorizado ao arquivo: ' . $safe_filename, 'SUCCESS');
        
        header("Content-Type: application/pdf");
        header("Content-Security-Policy: default-src 'self'");
        header("X-Content-Type-Options: nosniff");
        header("Location: " . $host . "src/download/" . $safe_filename);
        
        exit;
    } else {
        view_files_security_log('Parâmetros insuficientes - arq_name: ' . $arq_name . ', view: ' . $view, 'ERROR');
        http_response_code(400);
        die('Parâmetros insuficientes');
    }

?>