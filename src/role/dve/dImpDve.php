<?php
/*
	Criado por Tiago V N - Elumini - 28/08/2006
	Automatização da importação das DVE's.
	Atualizado para segurança - Implementa validação rigorosa contra RCE
*/

// Configurações de segurança para upload
define('MAX_DVE_FILE_SIZE', 10 * 1024 * 1024); // 10MB para arquivos DVE
define('DVE_LOG_FILE', 'dve_security.log');

// Whitelist de extensões permitidas para DVE
$allowed_dve_extensions = array('txt', 'csv');

/**
 * Função para log de segurança DVE
 */
function dve_security_log($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $log_entry = "[{$timestamp}] [{$level}] IP: {$ip} - DVE: {$message}\n";
    file_put_contents(DVE_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Valida extensão do arquivo DVE
 */
function validate_dve_extension($filename, $allowed_extensions) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $allowed_extensions);
}

/**
 * Valida se é arquivo de texto válido
 */
function validate_text_file($file_path) {
    // Verifica se o arquivo contém apenas caracteres de texto válidos
    $content = file_get_contents($file_path, false, null, 0, 1024); // Lê primeiros 1KB
    
    // Remove caracteres de controle permitidos (CR, LF, TAB)
    $filtered = preg_replace('/[\r\n\t]/', '', $content);
    
    // Verifica se contém apenas caracteres ASCII printable e alguns especiais
    return ctype_print($filtered) || empty($filtered);
}

/**
 * Sanitiza nome do arquivo DVE
 */
function sanitize_dve_filename($filename) {
    // Remove caracteres perigosos
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    // Limita tamanho do nome
    $filename = substr($filename, 0, 100);
    return $filename;
}

/**
 * Valida conteúdo do arquivo DVE
 */
function validate_dve_content($file_path) {
    $handle = fopen($file_path, 'r');
    if (!$handle) {
        return false;
    }
    
    $line_count = 0;
    $has_valid_content = false;
    
    while (($line = fgets($handle, 4096)) !== false && $line_count < 100) {
        $line_count++;
        $line = trim($line);
        
        // Verifica se a linha tem conteúdo suspeito
        if (preg_match('/<\?php|<script|javascript:|vbscript:|onload=|onerror=/i', $line)) {
            fclose($handle);
            return false;
        }
        
        // Verifica se tem conteúdo válido para DVE
        if (strlen($line) > 0) {
            $has_valid_content = true;
        }
    }
    
    fclose($handle);
    return $has_valid_content;
}

function TrataData($data, $tipo){
	
	#
	# Variavel $data � a String que cont�m a Data em qualquer formato
	# Variavel $tipo � que cont�m o tipo de formato data.
	# $tipo : 
	#		1 - Brasil - No formato -> Dia/Mes/Ano ou DD/MM/YYYY
	#		2 - USA	 - No formato -> YYYY-Mes-Dia ou YYYY-MM-DD
	
	# Obs
	# Esta fun��o n�o funciona com timestemp no formato a seguir :
	# DD/MM/YYYY H:M:S.MS ou YYYY-MM-DD H:M:S:MS
	# Pode configurar o formato da Data
	
	if ( $tipo == 1) {
		list($dia, $mes, $ano) = explode("[/-]", $data);		
	}elseif ( $tipo == 2 ) {
		list($ano, $mes, $dia) = explode("[-/]", $data);		
	}else{
		$msg = "Erro - Formato de data n�o existe.";
	}	
	
	return $ano."-".$mes."-".$dia;
	
}

function FormatValor($valor){

	#
	# Fun��o para formatar a moeda
	# Criado por Tiago V N
	#

	$arr1 = array(".", ",");
	$arr2  = array("", ".");

	$valor = str_replace($arr1, $arr2, $valor);
	return floatval($valor);
}


$caminho = "arquivodve";

// Validação inicial de segurança
if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    dve_security_log('Upload DVE falhou - erro no $_FILES', 'ERROR');
    $msg = "Erro no upload do arquivo DVE.";
} else {
    $file_info = $_FILES['arquivo'];
    $original_filename = $file_info['name'];
    $temp_file = $file_info['tmp_name'];
    $file_size = $file_info['size'];
    
    // Log da tentativa
    dve_security_log('Tentativa upload DVE: ' . $original_filename . ' (' . $file_size . ' bytes)');
    
    // Validação de tamanho
    if ($file_size > MAX_DVE_FILE_SIZE) {
        dve_security_log('Upload DVE rejeitado - muito grande: ' . $file_size, 'SECURITY');
        $msg = "Erro: Arquivo muito grande. Máximo " . (MAX_DVE_FILE_SIZE / 1024 / 1024) . "MB.";
    } elseif ($file_size <= 0) {
        dve_security_log('Upload DVE rejeitado - arquivo vazio', 'SECURITY');
        $msg = "Erro: Arquivo vazio não é permitido.";
    }
    // Validação de extensão
    elseif (!validate_dve_extension($original_filename, $allowed_dve_extensions)) {
        dve_security_log('Upload DVE rejeitado - extensão inválida: ' . $original_filename, 'SECURITY');
        $msg = "Erro: Arquivo deve ser .txt ou .csv apenas.";
    }
    // Validação de arquivo de texto
    elseif (!validate_text_file($temp_file)) {
        dve_security_log('Upload DVE rejeitado - não é texto válido: ' . $original_filename, 'SECURITY');
        $msg = "Erro: Arquivo contém dados inválidos ou suspeitos.";
    }
    // Validação de conteúdo DVE
    elseif (!validate_dve_content($temp_file)) {
        dve_security_log('Upload DVE rejeitado - conteúdo suspeito: ' . $original_filename, 'SECURITY');
        $msg = "Erro: Conteúdo do arquivo é inválido ou suspeito.";
    } else {
        // Continua com a lógica original se passou nas validações
        $file = $original_filename;
        
        $policykey = odbc_result(odbc_exec($db, "Select policyKey from Inform where id = '$idInform'"), 1);
        
        // Sanitiza o nome do arquivo
        $safe_filename = sanitize_dve_filename($file);
   // Gera nome seguro para o arquivo
   $destination_filename = $numdve . $policykey . $safe_filename;
   $destination_path = "../../" . $caminho . "/" . $destination_filename;
   
   if (move_uploaded_file($temp_file, $destination_path)) { 
  	 if (file_exists($destination_path)) {
        // Define permissões restritivas
        chmod($destination_path, 0644);
        
        // Log do sucesso
        dve_security_log('Upload DVE bem-sucedido: ' . $original_filename . ' -> ' . $destination_filename, 'SUCCESS');		   
		//Verifica��o do arquivo
		$i = 0;
		$y = 0;
		$erro = 0;
		$handle = fopen ($destination_path, "r");
		while (!feof ($handle)) {
	   	$i++;
   		$buffer = fgets($handle, 4096);

		  //echo  strlen(trim($buffer))."<br>";

		 if ($erro == 0) {
		   if (strlen(trim($buffer))!=296 && strlen(trim($buffer))!=10) {
			if (strlen(trim($buffer)) == "150") {
			    $asegurado           = trim(substr($buffer, 0, 99));
			    $adtinicio           = trim(substr($buffer, 100, 10));
			    $adtfim              = trim(substr($buffer, 110, 10));
			    $andve               = trim(substr($buffer, 120, 10));
			    $aapolice            = trim(substr($buffer, 130, 20));
    
				if (trim(strtoupper($asegurado))!= trim(strtoupper($segurado))) {
			       $msg .= "Erro: Arquivo n�o pertence a este exportador !!!<br>";
				   $erro = "1";	
			    }else if ($adtinicio != $inicio Or $adtfim != $fim) {
			       $msg .= "Erro: Per�odo de Dve selecionado n�o � igual a do arquivo!!!<br>";
				   $erro = "1";	
			    }else if ($andve  != $numDVE) {
			       $msg .= "Erro: Numero da Dve selecionado n�o � igual a do arquivo!!!<br>";
				   $erro = "1";	
			    }

				$idDVE = odbc_result(odbc_exec($db, "Select * from DVE where idInform='$idInform' And num='$andve'"), 1);
				
				$SQL = "DELETE FROM tb_Temp_Dve WHERE idInform='$idInform' AND idDve='$idDVE' AND numDve='$andve'";
				odbc_exec($db, $SQL);				
				
		    }else{

				$msg .= "Erro: Existe erro na linha do cabe�alho. Verifique o layout.";
				$erro = 1;
			}
		   }//	
		 }	// If do Erro
		 
		if ($erro==0) {	
		 if (strlen(trim($buffer)) != 150 && strlen(trim($buffer)) != 10) {
			 if (strlen(trim($buffer)) == 296) {

	    		$importador			 = trim(strtoupper(substr($buffer, 0, 99)));
				$dataEmb	         = TrataData(trim(substr($buffer, 165, 10)), 1);
			    $fatura              = trim(substr($buffer, 175, 50));
		    	$dataVenc            = TrataData(trim(substr($buffer, 225, 10)),1);
		    	$valorEmb            = FormatValor(trim(substr($buffer, 235, 20)));
				$proex               = FormatValor(trim(substr($buffer, 255, 20)));
				$ace                 = FormatValor(trim(substr($buffer, 275, 20)));
				$modalidade			 = trim(substr($buffer, 295, 1));
				$md			         = md5($fatura);
	
				$strSQL = "INSERT INTO tb_Temp_Dve (nome, idInform, idDve, numDve, dt_Embarque, fatura,
						  dt_Vencimento, vl_embarque, proex, ace, modalidade, md5) VALUES ('$importador', '$idInform',
						  '$idDVE', '$andve', '$dataEmb', '$fatura', '$dataVenc', '$valorEmb', '$proex', '$ace', '$modalidade', '$md')";
				$rs = odbc_exec($db, $strSQL);
			}else {
				$msg .= "Erro: Existe erro na linha do detalhe. Verifique o layout.";
				$erro = 1;			
			}
		 }	//
		}//if do Erro
			
		if ($erro==0) {	
		  if (strlen(trim($buffer)) != 150 && strlen(trim($buffer)) != 296) {
			if (strlen(trim($buffer)) == "10") {			  
			  $lfim = trim(substr($buffer, 0, 10));
		      if ($lfim == "") {
        		 $msg .= "Erro no processamento do Arquivo.<br>";
		         $msg .= "Erro: Arquivo sem numero de registro.<br>";
        		 $erro = "1";
		      }
		    }else{
				$msg .= "Erro: Existe erro na linha do rodap�. Verifique o layout.";
				$erro = 1;			
			}
		} //	
       }//if do erro 
		    /*
            else{
         		$msg .= "Erro No processamento do Arquivo.<br>";
				$erro = "1";
 			    break;				
		    }*/
		} //While

	  if ($erro==0) {
		if ($lfim != $i) {
		   $msg .= "Erro no arquivo de Importa��o.<br>O n� de linhas n�o confere com o publicado no rodap�.<br>";
		   $erro = "1";	   
		}
	  }
	  	
		fclose ($handle);
	 }//Fim verifica��o arquivo existente
   } //Fim da verfica��o do envio do arquivo para o servidor  
    }
}//Fim das validações de segurança

//echo $msg;
?>
