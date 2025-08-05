<div align=center>
<p class="textoBold">Tempo de Análise - Segurados</p>

<form action="<?php echo  $root;?>role/credit/Credit.php" name=f onSubmit="manda(this)">
<input type=hidden name=comm value=statistics>
<input type=hidden name=act value=<?php echo  $act;?>>
<table border=0 cellspacing=0 align=center width=100% cellpadding="2">
<tr><td colspan=3 align=center class="textoBold"><hr noshade>Selecione os países</td></tr>

<tr>
<td align=center width=45%>
<select class="caixa" multiple size=8 name=all onChange="correct(document.f.all, document.f.chosen)">
<?php  $c = odbc_exec($db, "select id, name from Country");
while(odbc_fetch_row($c)){
  $id = odbc_result($c, 1);
  $name = odbc_result($c, 2);
  echo "<option value=$id>$name\n";
}
?>
</select>
</td>

<td align=center width=5%>&nbsp;</td>

<td align=center>
<select class="caixa" multiple size=8 name=chosen onChange="correct(document.f.chosen, document.f.all)">
</select>
</td>
</tr>
<input type=hidden name=paises>
<tr><td colspan=3 align=center>
<input type=submit value=" OK " class="servicos">
</td></tr>
</table>
</form>

<script language=javascript>
function correct(all, chosen){
  var i = all.selectedIndex;
  var ops = all.options;
  var ch = chosen.options;
  ch[ch.length] = new Option(ops[i].text, ops[i].value, false, false);
  ops[i] = null;
  for(i = 0; i < ch.length; i++){
    ch[i].selected = false;
  }
  for(i = 0; i < ops.length; i++){
    ops[i].selected = false;
  }
}

function manda(f){
  var v = f.chosen.options;
  for(var i = 0; i < v.length; i++){
    if(f.paises.value == ''){
      f.paises.value = v[i].value;
    }else{
      f.paises.value += (", " + v[i].value);
    }
  }
  return true;
}
<?php  if($paises){
  $v = explode(", ", $paises);
  for($i = 0; $i < count($v); $i++){
    $op = $v[$i];
    echo "
var x = document.f.all.options;
for(var i = 0; i < x.length; i++){
  if(x[i].value == $op){
    x[i].selected = true;
    break;
  }
}
correct(document.f.all, document.f.chosen);
";
  }
}
?>
</script>

<?php  /*
$query =
         "select month(a.inicio) as mes, year(a.inicio) as ano, avg(cast(a.fim - a.inicio as int))
          from AnaliseImporter a join Importer i on a.idImporter=i.id
            join Country c on i.idCountry=c.id
            join Inform inf on inf.id=i.idInform
          where inf.state=10 AND i.state=10 and a.fim is not null".
	    ($paises ? " AND c.id in ($paises)" : '' ).
	    " group by (month(a.inicio)), (year(a.inicio))
              order by a.ano, a.mes";
*/
$query =
         "select month(a.inicio) as mes, year(a.inicio) as ano, avg(cast(a.fim - a.inicio as int))
          from AnaliseImporter a join Importer i on a.idImporter=i.id
            join Country c on i.idCountry=c.id
            join Inform inf on inf.id=i.idInform
          where a.fim is not null".
	    ($paises ? " AND c.id in ($paises)" : '' ).
	    " AND inf.state=10
              group by (month(a.inicio)), (year(a.inicio))
              order by a.ano, a.mes";
//echo "<pre>$query</pre>";
$c = odbc_exec($db, $query);
?>

<br><p>Média:
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

<?php  $ano_atual = date('Y');
$mes_atual = date('m');
$v = '';
while(odbc_fetch_row($c)){
  $mes = odbc_result($c, 1);
  $ano = odbc_result($c, 2);
  $dias = odbc_result($c, 3);
  $v[$ano][$mes] = $dias;
}
if($v){
  foreach($v as $ano => $meses){
    echo "<tr><td class=textoBold align=center>$ano</td>";
    for($j = 1; $j <= 12; $j++){
      $aux = $meses[$j];
      echo "<td align=center class=texto>".
	($aux >= 0 && $ano <= $ano_atual && $j <= $mes_atual ?
	 ($aux > 0 ? $aux : 1) :
	 '&nbsp;').
	"</td>";
    }
    echo "</tr>\n";
  }
}
?>

</table>

</div>
