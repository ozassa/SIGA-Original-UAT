<?php

require_once("../rolePrefix.php");

// Função para validar queries seguras
function isQuerySafe($query) {
    // Converter para minúscula para comparação
    $query = strtolower(trim($query));
    
    // Lista de comandos perigosos
    $dangerous = ['drop', 'delete', 'truncate', 'alter', 'create', 'insert', 'update', 'exec', 'execute', 'sp_', 'xp_'];
    
    foreach ($dangerous as $cmd) {
        if (strpos($query, $cmd) !== false) {
            return false;
        }
    }
    
    // Verificar se contém SELECT (única operação permitida)
    if (strpos($query, 'select') !== 0) {
        return false;
    }
    
    // Verificar se contém caracteres suspeitos
    $suspicious = [';', '--', '/*', '*/', 'union', 'or 1=1', 'or 1 = 1'];
    foreach ($suspicious as $sus) {
        if (strpos($query, $sus) !== false) {
            return false;
        }
    }
    
    return true;
}

if($query){
  // Validação de segurança
  if (!isQuerySafe($query)) {
    // Log da tentativa bloqueada
    if (function_exists('logSecurityEvent')) {
      logSecurityEvent('DANGEROUS_QUERY', "Query bloqueada: " . substr($query, 0, 200));
    } else {
      error_log("BLOCKED DANGEROUS QUERY from IP " . $_SERVER['REMOTE_ADDR'] . ": " . $query);
    }
    die("<div style='color: red; font-weight: bold;'>ERRO DE SEGURANÇA: Query bloqueada por motivos de segurança.</div>");
  }
  
  // Log da query permitida (apenas em desenvolvimento)
  if (defined('ENV') && ENV === 'development') {
    error_log("Query permitida: " . substr($query, 0, 100));
  }
  
  $query = preg_replace("/\\\\/", '', $query);
  if($sisseg){
    $res = odbc_exec($dbSisSeg, $query);
  }else{
    $res = odbc_exec($db, $query);
  }
  $title = $query;
  treat_error(odbc_errormsg($db));
  treat_error(odbc_errormsg($dbSisSeg));
  if($fields){
    $title .= " (fields)";
    $result = "<ul>\n";
    for($i = 1; $i <= odbc_num_fields($res); $i++){
      $result .= "<li>". odbc_field_name($res, $i). "\n";
    }
    $result .= "</ul>\n";
  }else{
    $result = "<table border>\n<tr>";
    for($i = 1; $i <= odbc_num_fields($res); $i++){
      $result .= "<th>". trim(odbc_field_name($res, $i)). "</th>";
    }
    $result .= "</tr>\n";
    for($i = 0; $i < $inicio; $i++){
      odbc_fetch_row($res);
    }
    $ii = $inicio;
    while(odbc_fetch_row($res)){
      $result .= "<tr>";
      for($i = 1; $i <= odbc_num_fields($res); $i++){
	$v = trim(odbc_result($res, $i));
	if($v == '')
	  $v = "&nbsp;";
	$result .= "<td>$v</td>";
      }
      $result .= "</tr>\n";
      $ii++;
      if($ii >= $fim)
	break;
    }
    $result .= "</table>";
  }
}

require_once("interf/db.php");

function treat_error($err){
  if($err){
    echo "$err<br>\n";
  }
}

?>
