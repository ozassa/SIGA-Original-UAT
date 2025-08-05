<?php 
require_once("../../../config.php");

// Lista de comandos permitidos
$allowedPages = [
    "coface"    => ["empresaID" => 1, "page" => "ListParameter.php"],
    "sbce"      => ["empresaID" => 2, "page" => "ListParameter.php"],
    "cad_par"   => ["page" => "InsertParameter.php"],
    "edit_par"  => ["page" => "EditParameter.php"]
];

$comm = isset($_GET["comm"]) ? $_GET["comm"] : "coface";

// Se o comm for inválido, define página padrão
if (!array_key_exists($comm, $allowedPages)) {
    $comm = "coface";
}

$page = $allowedPages[$comm]["page"];
$empresaID = isset($allowedPages[$comm]["empresaID"]) ? $allowedPages[$comm]["empresaID"] : null;

require_once($page);
require_once("../../../home.php");

?>
