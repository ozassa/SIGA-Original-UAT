<?php 
$query = "DELETE FROM Noticias WHERE id=$id";
$news = odbc_exec($db,$query);

?>







