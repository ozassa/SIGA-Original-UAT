<?php  $c = odbc_exec($db, "select name, contact, ocupationContact, city, uf, emailContact, i_Seg from Inform where id=$idInform");
if(odbc_fetch_row($c)){
  $empresa = odbc_result($c, 1);
  $contato = odbc_result($c, 2);
  $cargo = odbc_result($c, 3);
  $cidade = odbc_result($c, 4);
  $uf = odbc_result($c, 5);
  $email = odbc_result($c, 6);
  $iSeg = odbc_result($c, 7);
}

if($iSeg == 0){
  $cc = odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)");
  $iSeg = odbc_result($cc, 1);
}

$r = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($r)){
  $nApolice = odbc_result($r, 1);
}

$meses = array('janeiro', 'fevereiro', 'março',
	       'abril', 'maio', 'junho', 'julho',
	       'agosto', 'setembro', 'outubro',
	       'novembro', 'dezembro');
$mes = date("m") - 1;
$data = strftime("%d de $meses[$mes] de %Y");

include_once("../consultaCoface.php");

$mensagem = <<<MSG
$nomeEmpSBCE
Rio de Janeiro (RJ), $data. C<ANO>/<N°>

$empresa
Att. Sr(a). $contato
$cargo
$cidade - $uf

Prezados Senhores,

Segue anexo a Proposta de Endosso da apólice nº $nApolice.
Para que possamos atender a vossa demanda, favor encaminhar cópia autenticada, do ato societário devidamente registrado na junta comercial que formalizou a alteração e do cartão de CNPJ atualizado, anexo à Proposta devidamente assinada, com carimbo do signatário e com firma reconhecida, ao endereço que segue abaixo:

Gerência de Operações e Finanças
BACK-OFFICE
Rua Senador Dantas, 74 - 16º andar
Centro
Rio de Janeiro - RJ
20031-201

Com o aceite da Proposta de Endosso, estaremos encaminhando, posteriormente, documento oficial em referência a alteração solicitada.

Atenciosamente,

 
Robson Silva
Gerência de Operações e Finanças



MSG;
?>
