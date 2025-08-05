<?php 
	$r = odbc_exec($db, "UPDATE Inform SET state = 10 WHERE id = $idInform");

	$x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");

	If(odbc_fetch_row($x)){
  		$id = odbc_result($x, 1);
  		$fim = odbc_result($fim, 2);

  		If(! $fim){
			odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
  		}
	}
	/****************************************************************************************/

	// gera as dves q faltam
	$x = odbc_exec($db, "select id from Inform WHERE state = 10 and id = $idInform");

	while(odbc_fetch_row($x)){
	  	$id_inform = odbc_result($x, 1);

	  	$y = odbc_exec($db, "select startValidity, endValidity, tipoDve, pvigencia, Periodo_Vigencia from Inform where id=$id_inform");

	  	$start = odbc_result($y, 'startValidity');
	  	$EndValidity = odbc_result($y, 'endValidity');
	  	$tipoDve = odbc_result($y, 'tipoDve');
	  	$pvigencia = odbc_Result($y, 'pvigencia');
	  	$Periodo_Vigencia  =  odbc_Result($y, 'Periodo_Vigencia');
	  
	  	if(preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $start, $v)){
			$dia_inicial = $v[3];
	  	}
	  	
	  	$qry =  "SELECT
				Inf.id,
				Inf.n_Apolice, 
				Cast(DateDiff(M, startValidity, endValidity) + 1 as varchar) + ' meses' as Vigencia,
				IsNull(TD.Descricao_Item, '') As TipoDVE,
				Case tipoDve
					WHEN 2 THEN
						Case
							WHEN Cast((DateDiff(M, startValidity, endValidity) + 1) / 3 as Int) <> Cast((DateDiff(M, startValidity, endValidity) + 1) as Real) / 3 Then (DateDiff(M, startValidity, endValidity + 1) / 3) + 1
							Else (DateDiff(M, startValidity, endValidity) + 1) / 3 
						End
					WHEN 3 THEN
						Case
							WHEN Cast((DateDiff(M, startValidity, endValidity) + 1) / 6 as Int) <> Cast((DateDiff(M, startValidity, endValidity) + 1) as Real) / 6 Then (DateDiff(M, startValidity, endValidity + 1) / 6) + 1
							Else (DateDiff(M, startValidity, endValidity) + 1) / 6 
						End
					WHEN 4 THEN
						Case
							WHEN Cast((DateDiff(M, startValidity, endValidity) + 1) / 12 as Int) <> Cast((DateDiff(M, startValidity, endValidity) + 1) as Real) / 12 Then (DateDiff(M, startValidity, endValidity + 1) / 12) + 1
							Else (DateDiff(M, startValidity, endValidity) + 1) / 12 
						End
					Else DateDiff(M, startValidity, endValidity) + 1
				End AS NumDVNs,
				IsNull(D.NUM_DVE, 0) As Num_DVE
			FROM
				Inform Inf
			Left Join Campo_Item TD On 
				TD.i_Campo = 120 and TD.i_Item = ISNull(Inf.tipoDve, 1)
			Left JOIN (SELECT COUNT(*) AS NUM_DVE, idInform From DVE Group By idInform) D ON
				D.idInform = Inf.id
			WHERE Inf.id = ".$idInform." order by n_Apolice desc";
				  
		$retornv = odbc_exec($db,$qry);
		 
		if($retornv){
			$NumDVNs = odbc_result($retornv,'NumDVNs');
			$aux = odbc_result($retornv,'Num_DVE');
		}
	  
	  	$num_dves = $NumDVNs;
	  
	  	//$y = odbc_exec($db, "select count(*) from DVE where idInform=$id_inform");
	  	//$aux = odbc_result($y, 1);
	  
		If($aux < $num_dves){ # nao tem todas as dves, criar as que faltam
			$ss = 4;

			for ($num = 1; $num <= $num_dves; $num++){
		  		$y = odbc_exec($db, "select count(*) from DVE where idInform=$id_inform and num=$num");
		  		$aux = odbc_result($y, 1);

				if($aux == 0){ # se nao existe, cria
					if($num == 1){
						$inicio = $start;
					}else{
						//Alterado por Tiago V N - Elumini - 10/03/2006
						if ($tipoDve=="2"){	//	Trimestral
							$inicio = dmy2ymd(getStartDate(ymd2dmy($start), $ss - 1, 1));
							$ss = $ss + 3;
						}else if($tipoDve == "3"){ // Semetral
							$inicio = dmy2ymd(getStartDate(ymd2dmy($start), $ss - 1, 1));
							$ss = $ss + 6;
						}else if($tipoDve == "4"){ // Anual
							//$inicio = dmy2ymd(getStartDate(ymd2dmy($start),$inicio + 12, 1));
							$inicio = dmy2ymd(getStartDate(ymd2dmy($start), 12, 1));
						}else{ // Mensal
							$inicio = dmy2ymd(getStartDate(ymd2dmy($start), $num - 1, 1));
						}
					}
				
					$y = odbc_exec($db, "insert into DVE (idInform, state, inicio, num, periodo) values ($id_inform, 1, '$inicio', $num, 11)");
				}
			} // for
		}
	} // while
	/****************************************************************************************/

	$x = odbc_exec($db, "select name, idAnt from Inform where id=$idInform");
	$name = odbc_result($x, 1);
	$idAnt = odbc_result($x, 2);

  	$hc_query = "SELECT imp.name, c.name, imp.c_Coface_Imp, ch.creditDate, ch.stateDate FROM Importer imp, ChangeCredit ch, Country c ".
  	" where c.id=imp.idCountry  ".
  	" AND imp.idInform = $idInform ".
  	" AND imp.id = ch.idImporter ".
  	" AND ch.id = (SELECT max(id) FROM ChangeCredit WHERE idImporter = imp.id)".
  	" AND ch.state = 6 AND ".
  	" (ch.creditDate <= getdate() - 180 or ch.stateDate <= getdate() - 180 ) ".
  	" AND imp.id not in ( ".
 	" select distinct id from Importer ".
  	" where (idInform=$idInform and (state=9 or state = 7)) ".
  	" or id in (select ir.idImporter from ImporterRem ir join Importer i on i.id=ir.idImporter and i.idInform=$idInform) ".
  	" ) ".
  	"order by imp.name ";

	if($idAnt){
		If(odbc_fetch_row(odbc_exec($db,
			"select distinct name, c_Coface_Imp from Importer
                        where (idInform=$idInform and state=9)
			or id in
                               (select ir.idImporter from ImporterRem ir join Importer i on i.id=ir.idImporter and i.idInform=$idInform)
                               order by name")) ||
     odbc_fetch_row(odbc_exec($db,$hc_query)))
  {
	 
	 // 		      "SELECT imp.* FROM
     //                           (SELECT idImporter, creditDate
     //                            FROM ChangeCredit ch
     //                            WHERE ch.id IN
     //                            (SELECT max(id) FROM ChangeCredit GROUP BY idImporter)
     //                           ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id)
     //                           JOIN Inform inf ON (imp.idInform  = inf.id)
	 //                       WHERE inf.id = $idInform AND ch.creditDate <= getdate() - 180"
	 
	 //))
							
	 //						){
    $notif->apoliceEmitida($user, $idInform, $name, $db);
  } 
}Else{
	If( odbc_fetch_row(odbc_exec($db,$hc_query))){
      	$notif->apoliceEmitida($user, $idInform, $name, $db);
   	}
}
?>