<?php //Alterado HiCom mes 04

if(!isset($_SESSION)){
	session_start();
}

function Convert_Data($data){
    if (strstr($data, "/")){//verifica se tem a barra /
       $d = explode ("/", $data);//tira a barra
       $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mês etc...
    return $invert_data;
    }
    elseif(strstr($data, "-")){
       $d = explode ("-", $data);
       $invert_data = "$d[2]/$d[1]/$d[0]";
       return $invert_data;
    }

 }

$msg = '';
$state = odbc_result(odbc_exec($db, "select state from DVE where id=$idDVE"), 1);

$DataCadastro   = Convert_Data(date('d/m/Y'));
if(! function_exists('trataValorSQl')){
	function trataValorSQl($valor){
		
		$valor1 = str_replace('.','',$valor);
		$valor1 = str_replace(',','.',$valor1);
		return $valor1;
	}
}

if(check_dates($dataEmb, $dataVenc)){	
 //print '?'.$valorEmb;
  $valorEmb = trataValorSQl($valorEmb);
  $proex = trataValorSQl($proex);
  $ace = trataValorSQl($ace);
  
  
 
  //die();
  
  if($proex + $ace > $valorEmb){
      $msg = "A soma dos valores de PROEX e ACE deve ser menor ou igual ao Valor Embarcado";
  }else{
      $excluido = 0;
      // verifica se tem sinistro pra esse infeliz
      $x = odbc_exec($db,
		   "select sd.dateEmb from SinistroDetails sd join Sinistro s on s.id=sd.idSinistro
                    where s.idImporter=$idBuyer order by dateEmb");
      if(odbc_fetch_row($x)){ // se tiver
		  $dateEmbSinistro = getTimeStamp(ymd2dmy(odbc_result($x, 'dateEmb')));
		  //echo "$dateEmbSinistro -> ". getTimeStamp($dataEmb). "<br>";
		  // se a data de embarque da DVE for depois de 6 meses do embarque do sinistro, marca a fatura como excluida
		  if(getTimeStamp($dataEmb) > $dateEmbSinistro + (180 * 24 * 3600)){
				$excluido = 1;
		  }
 	  }

	  if($idDetail){	
			  //die();
			  $x = odbc_exec($db,
					"update DVEDetails set idImporter=$idBuyer, idCountry=$idCountry, embDate='".
					dmy2ymd($dataEmb). "', vencDate='". dmy2ymd($dataVenc). "', excluido=$excluido, ".
					"fatura='$fatura', totalEmbarcado='" . $valorEmb . "', proex='" . $proex. "', ace='" . $ace . "',
					modalidade=$modalidade where id=$idDetail");
					
			  $z = odbc_exec($db, "select * from DVEDetails where id=$idDetail");
			  
			  $y = odbc_exec($db,
					"insert into ChangeDVE (idDVE, idImporter, idCountry, embDate, vencDate,
					fatura, totalEmbarcado, proex, ace, modalidade, idUser, tipo, date, idDetail) values
					($idDVE, ". odbc_result($z, 'idImporter'). ", ". odbc_result($z, 'idCountry').
					", '". odbc_result($z, 'embDate'). "', '". odbc_result($z, 'vencDate'). "', '".
					odbc_result($z, 'fatura'). "', '". odbc_result($z, 'totalEmbarcado'). "', '".
					odbc_result($z, 'proex'). "', '". odbc_result($z, 'ace'). "', ".
					odbc_result($z, 'modalidade'). ", $userID, 2, getdate(), $idDetail)");
	  }else{
	
	      // echo $idDVE . "TTTTEEEESSSSSTTTTTEEEEEE" . (str_replace (",", ".", $valorEmb) * 100);
	      // die();

		  $cquery = "insert into DVEDetails (idDVE, idImporter, idCountry, embDate, ".
				 "vencDate, fatura, totalEmbarcado, proex, ace, modalidade, excluido,DataCadastro) ".
				 "values ($idDVE, $idBuyer, $idCountry, '". dmy2ymd($dataEmb).
				 "', '". dmy2ymd($dataVenc). "', '$fatura','" . $valorEmb . "' , '" .  $proex. "', ".
				 "'" . (str_replace (",", ".", $ace)) . "', $modalidade, $excluido,'$DataCadastro')";
	
		  
		  $x = odbc_exec($db,$cquery);
	
		  $lastId = odbc_result(odbc_exec($db, "select max(id) from DVEDetails where idDVE=$idDVE"), 1);
		  
		  $qry =  "insert into ChangeDVE (idDVE, idImporter, idCountry, embDate, vencDate,
						  fatura, totalEmbarcado, proex, ace, modalidade, idUser, tipo, date, idDetail) ".
				 "values ($idDVE, $idBuyer, $idCountry, '". dmy2ymd($dataEmb). "', '". dmy2ymd($dataVenc).
				 "', '$fatura', '" . $valorEmb . "', '" . $proex . "', '" . $ace . "', $modalidade, $userID, 1, getdate(), $lastId)";
		  $y = odbc_exec($db,$qry);
      }
      //print $cquery.'<br><br>'.$qry;
	
		if(!$x || !$y){
			 $msg = 'Erro ao '. ($idDetail ? 'alterar' : 'declarar'). ' embarque';
		}else{
		    if($modalidade == 2 || $modalidade == 1){
				$y = odbc_exec($db, "select sum(totalEmbarcado) from DVEDetails where idDVE=$idDVE and modalidade=2 and state=1");
				$total3 = odbc_result($y, 1);
				odbc_exec($db,
					  "update DVE set total3='$total3'".
					  ($state == 2 ? ', state=3' : '').
					  " where id=$idDVE");
     		 }
			 
      	$msg = 'Embarque '. ($idDetail ? 'alterado' : 'declarado');
      	$emb_dec = true;
      	
			  if(! $idDetail){
				 $registro = '';
				 $no_values = 1;
			  }
   		 }
    }
}
?>
