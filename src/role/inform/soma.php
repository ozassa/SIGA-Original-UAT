<?php $cur = odbc_exec($db,
		 "SELECT prevExp12, imp.id
                  FROM Importer imp JOIN Country c ON (idCountry = c.id)
                  WHERE idInform = $idInform ORDER BY imp.id"
		 );
$i = 0;
$soma = 0;

while (odbc_fetch_row($cur)) {
  if($comm != "altBuy" && odbc_result($cur, 2) == $idBuy){
    $soma += ($prevExp12 * 1000);
    //echo "somando: comm=$comm, prev=$prevExp12, soma=$soma<br>";
  }else{
    $soma += odbc_result($cur, 1);
    //echo "comm=$comm, soma=$soma<br>";
  }
}

?>
