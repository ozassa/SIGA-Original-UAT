<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html><head>
<title>QUERY</title>
</head><body>
<h1>QUERY</h1>
<hr>
<?php

$query = "INSERT INTO Segurado (n_Pais, c_Estado, Nome, CNP, IE, t_Pessoa, Contato,
                                Nat_Oper, Endereco, Cargo, s_Seg, Cidade, Telefone,
                                Fax, e_Mail, n_User, n_User_Cadastro, c_NAPCE, cookie)
                 VALUES (  '175',  'SP',  'TESTE SA',  '',  '32132',  'J',  'ALDO ',
                           'ALFACE',  'END DE TESTE',  'ANALISTA',  2,  'CIDADE DO TESTE',
                           '11 36817423',  '11 36817423',  'teste@teste.com.br',  66,66,
                           '22200',  '5b1e441f9eea0d67d9030411e63d7b691023130475')";

$cur = odbc_exec($dbSisSeg, $query);

if($cur) {
 print "<h1>ok<h1>";
} else {
 print "<h1>erro3</h1>";
}
print $query;

?> 
<address></address>
<!-- hhmts start -->
Last modified: Mon Jun  3 17:06:56 BRT 2002
<!-- hhmts end -->
</body></html>
