<?php  //alterado HICOM em 06/05/2004
function insImporters($idInform, $idSegurado, $db, $dbSisSeg) {
  global $comm;

  if($comm != 'sendProp'){ // se for envio de proposta, nao inativar
    $r = odbc_exec($dbSisSeg, "UPDATE Importador SET s_Imp = 1 WHERE i_Seg = $idSegurado");
  }

  $cur = odbc_exec($dbSisSeg, "SELECT MAX (n_Imp) FROM Importador WHERE i_Seg = $idSegurado");
  $nImp = 0;
  if(odbc_fetch_row($cur))
    $nImp = odbc_result($cur, 1) + 1;

  // Inserir Importadores
  $cur = odbc_exec($db,
		   "SELECT i.name, address, limCredit, c.code, c.name
                    FROM Importer i JOIN Country c ON (i.idCountry = c.id)
                    WHERE i.idInform = $idInform");

  $ok = false;
  $r = true;
  while (odbc_fetch_row($cur)) {
    $nImp++;
    $query =
       " INSERT INTO Importador (i_Seg, n_Imp, n_User, n_Pais, Nome, Endereco, v_Credito_Solicitado)".
       " VALUES ($idSegurado, $nImp, 66, ". odbc_result($cur, 4). ", '". odbc_result($cur, 1). "', '".
       odbc_result($cur, 2). "', ". odbc_result($cur, 3). ")";
    //echo "$query<br>";
    $r = odbc_exec($dbSisSeg, $query);
    if(!$r) break;
  }
  if($r) {
    $ok = true;
  }
  return $ok;
} // insImporters

$ok = true;
$curInf = odbc_exec($db,
		    "SELECT inf.name, inf.contrat, inf.i_Seg".
		    " FROM Inform inf JOIN Region reg ON (idRegion = reg.id)".
		    " WHERE inf.id = $idInform");
if(odbc_fetch_row($curInf)) {
  $nameCofaceSeg = odbc_result($curInf, 1);
  $idCoface = odbc_result($curInf, 2);
  $iSeg = odbc_result($curInf, 3);
}
//$x = odbc_exec($dbSisSeg, "select * from Coface_Seg where c_Coface=$idCoface and i_Seg=$iSeg");
if($iSeg){
  //echo"<pre>I_Seg = $iSeg</pre>";
  $reestudo = 1;
  $idSegurado = $iSeg;
}

if($reestudo){
  $teste =   "SELECT reg.name,inf.cnpj,inf.ie,inf.name,inf.address,inf.respName,inf.ocupation,inf.products, ".
		      " inf.city, inf.cep, inf.tel, inf.fax, inf.email, inf.napce, inf.contrat, inf.i_Seg".
		      " FROM Inform inf JOIN Region reg ON (idRegion = reg.id)".
		      " WHERE inf.id = $idInform";
  $curInf = odbc_exec($db, $teste);

  if(odbc_fetch_row($curInf)) {
    //echo"<pre>bla bla bla</pre>";
    $idNapce = odbc_result($curInf, 14);
    $idCoface = odbc_result($curInf, 15);
    $nameCofaceSeg = odbc_result($curInf, 4);
    $iSeg = odbc_result($curInf, 'i_Seg');
    if(!is_numeric($idCoface) || !is_numeric($idNapce) || $idCoface == 0 || $idNapce == 0)
      $msg = "Códigos NAPCE ou CONTRAT inválidos, corrija em Informações Gerais";
    else {
      $query = "update Segurado set c_Estado=".
	"  '". substr(odbc_result($curInf, 1), 0, 2). "',".
	"Nome='". odbc_result($curInf, 4). "',".
	"CNP='". odbc_result($curInf, 2). "',".
	"IE='". odbc_result($curInf, 3). "',".
	"Contato='". odbc_result($curInf, 6).  "',".
	"Nat_Oper='". odbc_result($curInf, 8). "',".
	"Endereco='". odbc_result($curInf, 5). "',".
	"Cargo='". odbc_result($curInf, 7). "',".
	"s_Seg=2, Cidade='". odbc_result($curInf, 9). "',".
	"Telefone='". odbc_result($curInf, 11). "',".
	"Fax='". odbc_result($curInf, 12). "',".
	"e_Mail='". odbc_result($curInf, 13). "',".
	"c_NAPCE='$idNapce". "00' ".
        "where i_Seg=$iSeg";
     //echo "<pre>$query</pre>";
      $r = odbc_exec($dbSisSeg, $query);
      if (!$r){
	$msg = "Problemas na atualização do exportador";
	$ok = false;
      }
    }
  }
}else{
  // Insere dados na base SisSeg
  //alterado HIcom 06/05/2004 na string abaixo no SELECT adicionei o seguinte campo
  //inf.addressAbrev
  $curInf = odbc_exec($db,
		      " SELECT reg.name, inf.cnpj, inf.ie, inf.name, inf.address, inf.respName, inf.ocupation, inf.products, ".
		      "   inf.city, inf.cep, inf.tel, inf.fax, inf.email, inf.napce, inf.contrat, inf.addressAbrev".
		      " FROM Inform inf JOIN Region reg ON (idRegion = reg.id)".
		      " WHERE inf.id = $idInform");
  
  $key = session_id(). time();
  if(odbc_fetch_row($curInf)) {
    $idNapce = odbc_result($curInf, 14);
    $idCoface = odbc_result($curInf, 15);
    $nameCofaceSeg = odbc_result($curInf, 4);
    if(!is_numeric($idCoface) || !is_numeric($idNapce) || $idCoface == 0 || $idNapce == 0){
      $ok = false;
      $msg = "Códigos NAPCE ou CONTRAT inválidos, corrija em Informações Gerais";
    }else {
      //alterado Hicom 06/05/2004 no INSERTfoi adicionado o seguinte campo End_Abrev
	  //echo "passou";
   //echo "<pre>andréa</pre>";
   	  $query =
	" INSERT INTO Segurado (n_Pais, c_Estado, Nome, CNP, IE, t_Pessoa, Contato,".
	"   Nat_Oper, Endereco, Cargo, s_Seg, Cidade, Telefone, Fax, e_Mail, End_Abrev, n_User,".
	"   n_User_Cadastro, c_NAPCE, cookie)".
	" VALUES (".
	"  '175',".                            // Código do Brasil
	"  '". substr(odbc_result($curInf, 1), 0, 2). "',". // Estado
	"  '". odbc_result($curInf, 4). "',".  // Nome do Segurado
	"  '". odbc_result($curInf, 2). "',".  // CNPJ
	"  '". odbc_result($curInf, 3). "',".  // IE
	"  'J',".                              // Tipo de pessoa
	"  '". odbc_result($curInf, 6). "',".  // Contato
	"  '". odbc_result($curInf, 8). "',".  // Natureza da operação
	"  '". odbc_result($curInf, 5). "',".  // Endereço
	"  '". odbc_result($curInf, 7). "',".  // Cargo
	"  2,".                                // Status
	"  '". odbc_result($curInf, 9). "',".  // Cidade
	"  '". odbc_result($curInf, 11). "',". // Telefone
	"  '". odbc_result($curInf, 12). "',". // Fax
	"  '". odbc_result($curInf, 13). "',". // E-mail
	"  '". odbc_result($curInf, 16). "',". //Endereço Abreviado End_Abrev (adicionado hicom 06/05/2004)
	"  66,66,".                            // Usuário / Usuário que cadastrou
	"  '". $idNapce. "00',".               // Napce
	"  '$key')";                           // Chave
     
      odbc_autocommit($dbSisSeg, false);
      echo "<pre> $query </pre>";
      $r = odbc_exec($dbSisSeg, $query);
      if($r) {
      
    $cur = odbc_exec($dbSisSeg, "SELECT max(i_Seg) FROM Segurado WHERE cookie = '$key'");
    echo "SELECT max(i_Seg) FROM Segurado WHERE cookie = '$key'";
    echo "$key";
  	if(odbc_fetch_row($cur)) {
  	//if(odbc_result($cur, 1) > 0) {
      $i_Seg = odbc_result($cur, 1);
      echo "<pre>$i_Seg</pre>";
      echo "<pre>entrou    $idSegurado</pre>";
      echo "<pre>SELECT max(i_Seg) FROM Segurado WHERE cookie = '$key'</pre>";
      $idSegurado = odbc_result($cur, 1);

      echo "<pre>$idSegurado</pre>";
	  // Inserir cadastro coface
	  $query = "INSERT INTO Coface_Seg (c_Coface, i_Seg, s_Cod, Nome) VALUES ($idCoface, $idSegurado, 0, '$nameCofaceSeg')";

	  $r = odbc_exec ($dbSisSeg, $query);
	  if(!$r) {
	    odbc_rollback($dbSisSeg);
	    // Sabemos que este exportador está no sisseg
	    // vamos verificar se possui apólice vigente
	    $cur = odbc_exec($dbSisSeg,
			     "SELECT d_Ini_Vig, d_Fim_Vig, i_Seg FROM Base_Calculo
                              WHERE d_Ini_Vig <= getDate() AND
                                  d_Fim_Vig >= getDate() AND
                                  t_Endosso = 0 AND
                                  (s_Doc = 1 OR s_Doc = 3) AND
                                  c_Coface = $idCoface");

	    if(odbc_fetch_row($cur)) {
	      // Possui apólice Vigente
	      $data = odbc_result($cur, "d_Ini_Vig");
	      $dataIni = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
	      $data = odbc_result($cur, "d_Fim_Vig");
	      $dataFim = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
	      $msg = "Este exportador possui apólice vigente ($dataIni à $dataFim)";
	      $vig = true;
	    }else{
	      // Não possui apólice vigente
	      // Recupera id do segurado
          $teste = "SELECT max(i_Seg) FROM Coface_Seg WHERE c_Coface = $idCoface";
          echo "$teste";
          $cur = odbc_exec($dbSisSeg, $teste);
	      if(odbc_fetch_row($cur)) {
		//$idSegurado = odbc_result($cur, "i_Seg");
		// Atualiza dados do segurado no SisSeg
   		$query = " UPDATE Segurado".
		  " SET n_Pais = '175',".
		  "   c_Estado = '". substr(odbc_result($curInf, 1), 0, 2). "',".
		  "   Nome     = '". odbc_result($curInf, 4). "',".
		  "   CNP      = '". odbc_result($curInf, 2). "',".
		  "   IE       = '". odbc_result($curInf, 3). "',".
		  "   t_Pessoa = 'J',".
		  "   Contato  = '". odbc_result($curInf, 6). "',".
		  "   Nat_Oper = '". odbc_result($curInf, 8). "',".
		  "   Endereco = '". odbc_result($curInf, 5). "',".
		  "   Cargo    = '". odbc_result($curInf, 7). "',".
		  "   s_Seg    = 2,".
		  "   Cidade   = '". odbc_result($curInf, 9). "',".
		  "   Telefone = '". odbc_result($curInf, 11). "',".
		  "   Fax      = '". odbc_result($curInf, 12). "',".
		  "   e_Mail   = '". odbc_result($curInf, 13). "',".
		  "   n_User   = 66, n_User_Cadastro = 66,".
		  "   c_NAPCE  = '". $idNapce. "00'".
		  " WHERE i_Seg = $idSegurado";
		echo "<pre>$query</pre>";
		$r = odbc_exec($dbSisSeg, $query);
		if(!$r){
		  $msg = "Problemas na atualização de segurado pré-existente";
		}else if(insImporters($idInform, $idSegurado, $db, $dbSisSeg)) {
		  $ok = true;
		  $msg = "";
		  odbc_commit($dbSisSeg);
		}else{
		  $msg = "Problemas na inclusão de importador em segurado pré-existente";
		}
	      }else{
		$msg = "Problemas na obtenção do código do segurado pré-existente";
	      }
	    }
	  }else{
	    // Inserir Importadores
	    if(insImporters($idInform, $idSegurado, $db, $dbSisSeg)) {
	      $ok = true;
	      $msg = "";
	      odbc_commit($dbSisSeg);
	    }else{
	      $ok = false;
	      $msg = "Problemas na inclusão de importador";
	    }
	  }
	}
      }
    }
  }
}

if(!$ok) {
  odbc_rollback ($dbSisSeg);
  $erroSisSeg = 1;
}else{
  odbc_commit($dbSisSeg);
}
odbc_autocommit ($dbSisSeg, true);
?>
