<?php  $ano = 2008;
$mes = 6;

function mkdate ($a, $m, $d) 
{
  return date ("Y-m-d", mktime (0, 0, 0, $m, $d, $a));
}
  
  $last = mkdate ($ano, $mes + 1, 1);

  $m = ($mes - 3) % 12;
  $m = $m + 1;

  $a = $ano;

  $dInicio = mkdate ($a, $m, 1);
  $dFim = mkdate ($a, $m + 1, 0);

  $meses = "(startValidity >= '$dInicio' AND startValidity <= '$dFim')";
  
  for ($i = 1; $i < 4; $i++) {
    $m = ($m + 2) % 12;
    $m ++;
    $a = $m > $mes ? $ano -1 : $ano;
    $dInicio = mkdate ($a, $m, 1);
    $dFim = mkdate ($a, $m + 1, 0);
    $meses .= "\n  OR (startValidity >= '$dInicio' AND startValidity <= '$dFim')";
	
  }
    
  $idExporterReport = 0;
  
echo $meses;

  
  ?>