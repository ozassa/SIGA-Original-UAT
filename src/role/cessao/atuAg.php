<?php 
   $cod  = $field -> getField (codigo);
   $Name = $field -> getField (nome);
   $End  = $field -> getField (endereco);
   $Cid  = $field -> getfield (cidade);
   //alterado por Michel Saddock 10/09/2006
   $agTel = $field -> getfield (agTel);
   $email = $field -> getfield (email);
   $contato = $field -> getfield (contato);

   $q = "update Agencia set name = '$Name', agTel = '$agTel', email = '$email', contato = '$contato', endereco = '$End', cidade = '$Cid' where codigo = '$cod'";
//if($idNurim){
//    $q = "INSERT INTO Agencia (idBanco, codigo, name, endereco, cidade, uf, cnpj, ie, idNurim) VALUES ($idBanco, $agencia, '$agNome', '$agEnd', '$agCid', '$uf', '$cnpj', '$agIE', $idNurim)";
//}else{
//    $q = "INSERT INTO Agencia (idBanco, codigo, name, endereco, cidade, uf, cnpj, ie) VALUES ($idBanco, $agencia, '$agNome', '$agEnd', '$agCid', '$uf', '$cnpj', '$agIE')";
//}
    echo $q;
    $cur = odbc_exec($db, $q);
    if($cur){
      $msgAg = "Agência Cadastrada com Sucesso";
    }

?>
