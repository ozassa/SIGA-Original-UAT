<?php  //HICOM ALTERADO EM 03/05/2004
//Alterado HiCom mes 04
//Alterado HiCom (Gustavo) - 16/12/04 - validar NAPCE
//Alterado por Tiago V N - Elumini - 5/04/2006 -> Incluir campo n_Moeda





$x = odbc_exec($db, "select idAnt, cnpj from Inform where id=$idInform");
$idAnt = odbc_result($x, 1);
$cnpj  = odbc_result($x, 2);

if(! $idAnt){ // se nao for renovacao, verifica o contrat
  if($contrat){
    $x = odbc_exec($db,
		   "select contrat, cnpj from Inform where contrat='$contrat'");

    while(odbc_fetch_row($x)) {
       if(odbc_result($x, "cnpj")!=$cnpj){
           $msg = "O contrat digitado j� existe";
           $forward = 'error';
           //return;
           break;
       }
    }
  }else{  // comentado pela SBCE(Andr�a) em 3/6, para permitir a recusa de informes sem ci
    //$msg = "Favor digitar um contrat";
    //$forward = 'error';
    //return;
  }
}

//---------------HICOM (Gustavo) 16/12/04------------------------------------

if ($field->getField("napce") > 0) {
	$sql = "SELECT count(c_NAPCE) qtd FROM NAPCE WHERE c_NAPCE = ".$field->getField("napce")."00";
	$x = odbc_exec($dbSisSeg, $sql);
	
	if (odbc_result($x, "qtd") == 0) {
	   $msg = "C�digo NAPCE inv�lido!";  
	   $forward = 'error';
	   return;
	}     
}
//-------------------------------------------------------------------



//---------------HICOM 03/05/2004------------------------------------



if($field->getField("addressAbrev") == "")
{
   $msg = "Favor digitar o endere�o Abreviado!";  
   $forward = 'error';
   return;
}     

  $addressAbrev = preg_replace("/\\\\/", "", $addressAbrev);
  $addressAbrev = preg_replace("/'/", "''", $addressAbrev);
//-------------------------------------------------------------------

//Hicom alterado em 03/05/2004
//na string abaixo foi adcionada a seguinte linha -->  "      addressAbrev = '". $addressAbrev ."'   



if ($role["executive"]){
	$baixapend = 3; // baixa pend�ncia com perfil executivo
}else{
    $baixapend = 2; // mant�m status como pendente
}



$cqry =  " UPDATE Inform".
		  "  SET naf = '". $field->getField("naf"). "',".
		  "      siren = '". $field->getField("siren"). "',".
		  "      quest = '". $field->getField("quest"). "',".
		  "      napce = '". $field->getField("napce"). "',".
		  "      dossier = '". $field->getField("dossier"). "',".
		  "      contrat = '". $field->getField("contrat"). "',".
		  "      generalState = ". $baixapend. ",".
		  "      pvigencia    = ". $field->getField("pvigencia"). ",".
		  "      addressAbrev = '". $addressAbrev ."', ".
		  "      currency      = '". $field->getField("tipomoeda")."' ".
		  "  WHERE id = ". $field->getField("idInform");
$r = odbc_exec($db,$cqry);

if($r == FALSE){
  $msg = "Campos preenchidos incorretamente";
  $s = odbc_exec( $db, " UPDATE Inform SET generalState = ". $baixapend.
  "  WHERE id = ". $field->getField("idInform"));
  $forward = "error";
}

?>
