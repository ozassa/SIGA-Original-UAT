<?php //Criado Hicom 19/10/2004 (Gustavo)

require_once("../../dbOpen.php");

$ok = true;
$cur=odbc_exec($db,"BEGIN TRAN");

$sql =	"SELECT	isnull(statePa, 1) statePa FROM Inform WHERE id = $idInform";
$cur=odbc_exec($db,$sql);
if (!$cur)
	$ok = false;
$statePa = odbc_result($cur,"statePa");

if ($statePa > 1 ) {
	$sql = 	"UPDATE Inform set 	idUserPa = ".$userID.", ".
			"					dataPa = getdate() ".
			"WHERE	id = ".$idInform;
	$cur=odbc_exec($db,$sql);
	if (!$cur)
		$ok = false;
}

$sql =	"UPDATE		DVEDetails SET negado = 0 ".
		"WHERE		idDVE IN ( ".
		"				SELECT id FROM DVE WHERE idInform = ".$idInform." )";
$cur=odbc_exec($db,$sql);
if (!$cur)
	$ok = false;

if (count($negado) > 0) {
	$sql =	"UPDATE		DVEDetails SET negado = 1 ".
			"WHERE		id IN(";
	
	for ($i=1; $i<=count($negado); $i++) {
		$sql = $sql.$negado[$i - 1];
		if ($i <> count($negado))
			$sql = $sql.",";
	}
	$sql = $sql.")";
	
	$cur=odbc_exec($db,$sql);
	if (!$cur)
		$ok = false;
}

if ($ok)	
	$cur=odbc_exec($db,"COMMIT TRAN");
else 
	$cur=odbc_exec($db,"ROLLBACK TRAN");
?>