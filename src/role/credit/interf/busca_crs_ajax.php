<?php
   require_once('../../../dbOpen.php');
   
   // verificar a existência do Código
   
   function direita($str,$num){
	   $dado = substr($str,(strlen($str)-$num),strlen($str));
	  // $valor =  sprintf('%0'.$num.'d', $str);
	   $valor = $dado;
	   return  $valor;
   }
   
   /*$sql = "Select
			COUNT(Imp.id) as Id
			From
			Importer Imp
			Inner Join Country C On
			C.id = Imp.idCountry
			Where
			Imp.idInform = ".$_REQUEST['idInform'] ."
			And Imp.id <> ". $_REQUEST['idImporter']."
			And Cast(C.code as varchar) + Right('000000', IsNull(Replace(Imp.c_Coface_Imp, ' ', ''), '')) = '". $_REQUEST['paisID']. direita($_REQUEST['crs'],6) ."'
			";*/
			
	 $sql = "SELECT COUNT(id) as Id 
        FROM Importer 
        WHERE idCountry = ? 
        AND id <> ? 
        AND c_Coface_Imp = ? 
        AND idInform = ?";

$stmt = odbc_prepare($db, $sql);
odbc_execute($stmt, [
    $_REQUEST['paisID'],
    $_REQUEST['idImporter'],
    $_REQUEST['crs'],
    $_REQUEST['idInform']
]);

$res = $stmt;
 odbc_free_result($stmt);
	  	
	  if(odbc_result($res, 'Id') > 0 && !empty($_REQUEST['crs'])){
    echo 'Já existe um comprador com este CRS (' . htmlspecialchars($_REQUEST['crs'], ENT_QUOTES, 'UTF-8') . ') nesta carteira.';
}

?>