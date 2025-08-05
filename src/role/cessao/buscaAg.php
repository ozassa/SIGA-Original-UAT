<?php 
$query  = "SELECT a.name, a.codigo, b.name, a.id
           FROM Agencia a
           JOIN Banco b ON (b.id = a.idBanco)
           WHERE a.name is not null ";

if ($nameBusca){
  $query = $query." AND a.name LIKE '%".strtoupper($nameBusca)."%'";
}

if($codBusca){
  $query .= " AND a.codigo = $codBusca";
}

if($Banco){
  $query .= " AND a.idBanco = $Banco";
}

$query = $query." ORDER BY a.name";

$busca = odbc_exec($db,$query);

?>
