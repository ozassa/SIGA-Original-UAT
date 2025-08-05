<?php 

require_once("../../../config.php");

$allowedPages = [
    "index" => "ListLog.php"
];

$comm = isset($_GET["comm"]) ? $_GET["comm"] : "index";
$page = isset($allowedPages[$comm]) ? $allowedPages[$comm] : "ListLog.php";

require_once($page);
require_once("../../../home.php");

?>