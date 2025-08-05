<?php


if($done){
  $notif->doneRole($idNotification, $db);
}else{
  $c = odbc_exec($db, "select name, contrat, idAnt from Inform where id=$idInform");
  if(odbc_fetch_row($c)){
    $nameCl = odbc_result($c, 1);
    $contrat = odbc_result($c, 2);
    $idAnt = odbc_result($c, 3);
  }
 
 
 //if(! $idAnt){
  //  $msg = 'Esse não é um informe de renovação';
  //  return;
  //}
  if($idAnt){
    $x = odbc_exec($db, "select endValidity from Inform where id =$idAnt");
  } else{
    $x = odbc_exec($db, "select endValidity from Inform where id =$idInform");
  }
  $endValidity = ymd2dmy(odbc_result($x, 1));
  
  $query = "select distinct name, c_Coface_Imp from Importer
            where (idInform=$idInform and (state=9 or state = 7))
            or id in (select ir.idImporter from ImporterRem ir join Importer i on i.id=ir.idImporter and i.idInform=$idInform)
            order by name";  
  $excluidos = odbc_exec($db, $query);

  $dados1 = array();
  while(odbc_fetch_row($excluidos)){
    array_push($dados1, array("name" => trim(odbc_result($excluidos, 1)), "ciCoface" => odbc_result($excluidos, 2)));
  }

  odbc_close($db);

  
  $query ="select i.name, c.name, i.c_Coface_Imp, i.limCredit 
            from Importer i join Country c on c.id=i.idCountry 
          where i.idInform=$idInform and i.creditAut=1 and i.id not in 
            (select distinct id from Importer where (idInform=$idInform and (state=9 or state = 7)) 
            or id in (select ir.idImporter from ImporterRem ir join Importer i on i.id=ir.idImporter and i.idInform=$idInform) ) order by i.name ";
  $reduzidos = odbc_exec($db, $query);

  $dados2 = array();
  while(odbc_fetch_row($reduzidos)){
    array_push($dados2, array("name" => trim(odbc_result($reduzidos, 1)), "pais" => odbc_result($reduzidos, 2), "ciCoface" => odbc_result($reduzidos, 3),
                              "credit" => number_format(odbc_result($reduzidos, 4) / 1000, 0)));
  }
  
  odbc_close($db);

//  $seis_meses = odbc_exec($db,
//			  "SELECT imp.name, c.name, imp.c_Coface_Imp, ch.creditDate
//	                   FROM (SELECT idImporter, creditDate
//                                 FROM ChangeCredit ch
//                                 WHERE ch.id IN
//                                  (SELECT max (id) FROM ChangeCredit GROUP BY idImporter)
//                                 ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id)
//                                 JOIN Inform inf ON (imp.idInform  = inf.id)
//                                 JOIN Country c ON c.id=imp.idCountry
//	                   WHERE inf.id = $idInform AND ch.creditDate <= getdate() - 180");
					   
  
  
  
  $query = "SELECT imp.name, c.name, imp.c_Coface_Imp, ch.creditDate, ch.stateDate 
   FROM Importer imp, ChangeCredit ch, Country c 
   where c.id=imp.idCountry  
   AND imp.idInform = $idInform 
   AND imp.id = ch.idImporter 
   AND ch.id = 
   (
   SELECT max(id) FROM ChangeCredit WHERE idImporter = imp.id 
   )
   AND ch.state = 6 AND 
   (ch.creditDate <= getdate() - 180 or ch.stateDate <= getdate() - 180 ) 
   AND imp.id not in ( 
   select distinct id from Importer 
   where (idInform=$idInform and (state=9 or state = 7)) 
   or id in (select ir.idImporter from ImporterRem ir join Importer i on i.id=ir.idImporter and i.idInform=$idInform) 
   )
  order by imp.name ";  
  $seis_meses = odbc_exec($db,$query);

  $dados3 = array();
  while(odbc_fetch_row($seis_meses)){
    array_push($dados3, array("name" => trim(odbc_result($seis_meses, 1)), "pais" => odbc_result($seis_meses, 2), "ciCoface" => odbc_result($seis_meses, 3),
                              "creditDate" => ymd2dmy(odbc_result($seis_meses, 4)), "stateDate" => ymd2dmy(odbc_result($seis_meses, 5))));
  }
  
  odbc_close($db);
					   
					   
					   
}



?>
