<?php 

$r=odbc_exec(
      $db,
      "SELECT dateBack FROM Inform".
      "  WHERE id = $idInform"
    );
    $dateBack = odbc_result($r, 1);

  list($dia, $mes, $ano) = split ('/', $dateBack); //data do recebimento da proposta vinda do bd 
  $d_Back_ini = date ("Y-m-d", mktime (0,0,0, $mes,  $dia,  date("Y")));
  $d_Back_fim = date ("Y-m-d", mktime (0,0,0, $mes,  $dia + 15,  date("Y")));

  list($diaA, $mesA, $anoA) = split ('/', $date); //data da aceita��o vinda do formulario
  $d_Aceit = date ("Y-m-d", mktime (0,0,0, $mesA,  $diaA,  date("Y")));

if (($d_Aceit < $d_Back_ini) or ($d_Aceit > $d_Back_fim)){
   $var = "fracasso";
   $msgData = "J� se passaram mais de 15 dias da data do recebimento da proposta, ou a data de aceita��o � anterior a data de recebimento da proposta.";
}else{
    $s=odbc_exec(
      $db,
      "UPDATE Inform SET".
      " dateAceit = '".$date."'".
      "  WHERE id = $idInform"
    );
    $var = "sucesso";
}


?>