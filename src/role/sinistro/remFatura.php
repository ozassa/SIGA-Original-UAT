<?php $q = "SELECT dateEmb, dateVenc, valueFat, valuePag, valueAbt, numFat, idSinistro FROM SinistroDetails WHERE id = $idSinDet";
   //echo $q;
   $alt = odbc_exec($db, $q);
   $dateEmbA = odbc_result($alt, 1);
   $dateVencA = odbc_result($alt, 2);
   $valueFatA = odbc_result($alt, 3);
   $valuePagA = odbc_result($alt, 4);
   $valueAbtA = odbc_result($alt, 5);
   $numFatA = odbc_result($alt, 6);
   $idSinistro = odbc_result($alt, 7);

   $q = "INSERT INTO ChangeSinistroDetails (idSinistro, dateEmb, dateVenc, valueFat, valuePag, valueAbt, numFat, dateAlt, idUser, tipo) VALUES ($idSinistro, '$dateEmbA', '$dateVencA', $valueFatA, $valuePagA, $valueAbtA, $numFatA, getdate(), $userID, 2)";
   $cur = odbc_exec($db, $q);

   //tipo 2 = alteração


   $p = odbc_exec($db, "DELETE FROM SinistroDetails WHERE id=$idSinDet");

?>
