<?php  if(isset($_POST['insert'])){
	$comentario = $_POST['comentario'];
  
  	If(! trim($comentario)){
    	$msg = 'Comentário não pode estar em branco';
  	}else{
    	$sql = "INSERT INTO InformComment (idInform, idUser, texto, date) 
        VALUES (?, ?, ?, getdate())";

$stmt = odbc_prepare($db, $sql);
$params = [$idInform, $userID, $comentario];

$c = odbc_execute($stmt, $params);

if ($c) {
    $msg = 'Comentário inserido';
} else {
    $msg = 'Erro ao inserir comentário';
}

  	}
}

//$query = "select i.id, u.name, i.texto, i.date from InformComment i join Users u on i.idUser=u.id where i.idInform=$idInform";

//$q = "select idAnt from Inform where id = $idInform";
//$x = odbc_exec($db, $q);

//$q1 = "";

//While (odbc_result($x, 1)) {
//	$id1 = odbc_result($x, 1);
//	$q1 = $q1. " or i.idInform in ($id1)";
//	$q = "select idAnt from Inform where id = $id1";
//	$x = odbc_exec($db, $q);
//}

//$query = $query.$q1;
//$x = odbc_exec($db, $query);

?>