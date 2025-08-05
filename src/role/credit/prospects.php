<?php  // converte a data de yyyy-mm-dd para dd/mm/yyyy
if(! function_exists('ymd2dmy')){
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

function getTimeStamp($date){
  if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/', $date, $res)){
    return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
  }
}

// retorna a diferenca em dias
function diferenca($inicio, $fim){
  return (int) ((getTimeStamp(ymd2dmy($fim)) - getTimeStamp(ymd2dmy($inicio))) / (24 * 3600));
}

if(!$mes || !$ano){
  $mes = date('m');
  $ano = date('Y');
  $dia = date('d');
  $begin = date("d/m/Y", mktime(0, 0, 0, $mes, $dia - 30, $ano));
  $end = date("d/m/Y");
  $data = 'getdate() - 30';
}else{
  $dia = 1;
  $begin = "01/$mes/$ano";
  $end = date("d/m/Y", mktime(0, 0, 0, $mes + 1, $dia - 1, $ano));
  $data = "'$ano-$mes-01 00:00:00.000' and a.inicio < '$ano-". ($mes + 1). "-01 00:00:00.000'";
}

$c = odbc_exec($db,
	       "select i.name, a.inicio, a.fim
                from Inform i join AnaliseInform a on (a.idInform=i.id)
                where a.fim is not null AND a.inicio >= $data and
                (i.idAnt is null or i.idAnt = 0 or i.id in
                 (select inf.id from Inform inf where inf.startValidity is null and
                  inf.id in
                  (select idAnt from Inform where idAnt > 0)
                 )
                )
                order by i.name");
?>

<div align=center>
<p class="textoBold">Tempo de Análise - Prospectivos</p>
<p class="textobold">Período: <?php echo  "$begin à $end" ?></p>

<table border=0 width="100%" cellpadding="2" cellspacing="0" align="center">
<tr class="bgAzul">
<th align=left width=70%>Empresa</th>
<th align=center>Tempo de Análise</th>
</tr>

<?php  $i = $soma = 0;
while(odbc_fetch_row($c)){
  $name = odbc_result($c, 1);
  $inicio = odbc_result($c, 2);
  $fim = odbc_result($c, 3);
  $dias = diferenca($inicio, $fim);
  echo "<tr". ($i % 2 ? ' class= bgCinza ' : ''). ">";
  echo "<td align=left>$name</td><td align=center>$dias dias</td></tr>\n";
  $i++;
  $soma += $dias;
}
$media = (int) ($soma / $i);
?>

<tr>
<td>&nbsp;</td>
</tr>
<tr class="bgAzul">
<td align=center>Média do Período</td>
<td align=center><?php echo  $media == 0 ? 1 : $media ?> dia<?php echo  $media == 0 ? '' : 's' ?></td>
</tr>
</table>

<hr noshade>

<p class="subtitulo">Média (em dias)</p>

<table border=0 cellspacing=0 cellpadding=2 width=100% align="center">
<tr class="bgAzul">
<th class="bgAzul" align=center width=60>ANO/MÊS</th>
<th class="bgAzul" align=center>Jan</th>
<th class="bgAzul" align=center>Fev</th>
<th class="bgAzul" align=center>Mar</th>
<th class="bgAzul" align=center>Abr</th>
<th class="bgAzul" align=center>Mai</th>
<th class="bgAzul" align=center>Jun</th>
<th class="bgAzul" align=center>Jul</th>
<th class="bgAzul" align=center>Ago</th>
<th class="bgAzul" align=center>Set</th>
<th class="bgAzul" align=center>Out</th>
<th class="bgAzul" align=center>Nov</th>
<th class="bgAzul" align=center>Dez</th>
</tr>

<?php  $n = 0;
$c = odbc_exec($db,
	       "select month(a.inicio) as mes, year(a.inicio) as ano, avg(cast(a.fim - a.inicio as int))
                from AnaliseInform a join Inform i on a.idInform=i.id
                where a.fim is not null and
                (i.idAnt is null or i.idAnt = 0 or i.id in
                 (select inf.id from Inform inf where inf.startValidity is null and
                  inf.id in
                  (select idAnt from Inform where idAnt > 0)
                 )
                )
                group by (month(a.inicio)), (year(a.inicio))
                order by ano, mes");
$v = '';
while(odbc_fetch_row($c)){
  $mes = odbc_result($c, 1);
  $ano = odbc_result($c, 2);
  $dias = odbc_result($c, 3);
  $v[$ano][$mes] = $dias;
}
$ano_atual = date('Y');
$mes_atual = date('m');

if($v){
  foreach($v as $ano => $meses){
    echo "<tr><td class=textoBold align=center>$ano</td>";
    for($j = 1; $j <= 12; $j++){
      $aux = $meses[$j];
      echo "<td class=texto align=center>".
	($ano <= $ano_atual && $j <= $mes_atual ?
	 "<a href=\"$root". "role/credit/Credit.php?comm=statistics&act=prospects&ano=$ano&mes=$j\">".
	 ($aux == 0 ? "1" : "$aux"). '</a>' :
	 '&nbsp;').
	"</td>";
    }
    echo "</tr>\n";
  }
}
?>

</table>

</div>
