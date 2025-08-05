<script language=javascript src="<?php echo $root; ?>scripts/calendario.js"></script>

<div align=center>
<p class="textoBold">Consulta de Prospectivos</p>

<form action="../credit/Credit.php" method=post name=f>
<input type=hidden name="comm" value="statistics">
<input type=hidden name="act" value="month">
<input type=hidden name="vai" value="1">
<!-- p>Mostrar apenas informes cadastrados de:<br -->
<input type=text class=caixa size=2 name=dia_inicio maxlength=2 tabindex=7 value="<?php echo $dia_inicio; ?>"
  onkeyup="proximo(this, 2, this.form.mes_inicio, 31)"> / 
<input type=text class=caixa size=2 name=mes_inicio maxlength=2 tabindex=8 value="<?php echo $mes_inicio; ?>"
  onkeyup="proximo(this, 2, this.form.ano_inicio, 12)"> / 
<input type=text class=caixa size=4 name=ano_inicio maxlength=4 tabindex=9 value="<?php echo $ano_inicio; ?>"
  onkeyup="proximo(this, 4, this.form.dia_fim, 9999)">
a
<input type=text class=caixa size=2 name=dia_fim maxlength=2 tabindex=7 value="<?php echo $dia_fim; ?>"
  onkeyup="proximo(this, 2, this.form.mes_fim, 31)"> / 
<input type=text class=caixa size=2 name=mes_fim maxlength=2 tabindex=8 value="<?php echo $mes_fim; ?>"
  onkeyup="proximo(this, 2, this.form.ano_fim, 12)"> / 
<input type=text class=caixa size=4 name=ano_fim maxlength=4 tabindex=9 value="<?php echo $ano_fim; ?>"
  onkeyup="proximo(this, 4, this.form.tipo, 9999)">
<br>
<!-- Se não for especificada uma data de fim de período, será usada a data atual -->
<br><select name=tipo>
<option value=1 <?php echo $tipo == 1 ? ' selected' : '';?>>Prospectivos
<option value=2 <?php echo $tipo == 2 ? ' selected' : '';?>>Apólice
</select>
<br><input type=button class=servicos value="OK" onClick="checkform(this.form)">
</form>

<?
if($vai){
//   list($dia_inicio, $mes_inicio, $ano_inicio) = explode('/', $inicio);
//   list($dia_fim, $mes_fim, $ano_fim) = explode('/', $fim);
  if(! $dia_fim){
    list($dia_fim, $mes_fim, $ano_fim) = explode('/', date('d/m/Y'));
  }

  if(mktime(0, 0, 0, $mes_inicio, $dia_inicio, $ano_inicio) > mktime(0, 0, 0, $mes_fim, $dia_fim, $ano_fim)){
    echo '<p><font color="#ff0000">A data de início é maior que a data de término</font>';
  }else{
    $inicio2 = "'$ano_inicio-$mes_inicio-$dia_inicio'";
    if(! $fim){
      $fim2 = "getdate()";
    }else{
      $fim2 = "'$ano_fim-$mes_fim-$dia_fim'";
    }
    echo "<br><br><TABLE width=\"80%\" cellspacing=0 cellpadding=2 border=0 align=center>\n";
    echo "<TR align=center class=bgAzul>
    <th class=bgAzul width=\"70%\">Nome</th>
    <th class=bgAzul>Data de cadastro</th>
 </TR>\n";

    $query = "select inf.id, inf.name, a.inicio from Inform inf join AnaliseInform a on a.idInform=inf.id
              where inf.name is not null and inf.bornDate between $inicio2 and $fim2";
    if($tipo == 1){ // pegar apenas os prospectivos (nao pegar os informes de renovacao)
      $query .= " and inf.state < 9 and inf.state <> 7 and (inf.idAnt is null or inf.idAnt = 0)";
    }else if($tipo == 2){ // pegar todos q estao com apolice vigente
      $query .= " and inf.state = 10";
    }
// " and (startValidity is null or $fim2 < startValidity)".
// " and state <> 9 and state <> 11 and state <> 7" :
    $x = odbc_exec($db, "$query order by a.inicio");
    $i = 0;
    while(odbc_fetch_row($x)){
      $idInform = odbc_result($x, 1);
      $name = odbc_result($x, 2);
      $bornDate = ymd2dmy(odbc_result($x, 3));
      echo "<tr align=center".
	($i % 2 != 0 ? " class=bgCinza" : ""). ">\n";
      echo "<td class=texto align=center>$name</td>";
      echo "<td class=texto align=center>$bornDate</td></tr>\n";
      $i = 1 - $i;
    }
    echo "</table>";
  }
}
?>

</div>

<script language=javascript>
function checkform(f){
  if(f.dia_inicio.value == '' && f.mes_inicio.value == '' && f.ano_inicio.value == ''){
    verErro('A data de início do período é obrigatória');
    return;
  }
  f.submit();
}

function proximo(atual, size, prox, max){
  if(atual.value.length == size){
    if(checknumber(atual, max))
      prox.focus();
  }
}

function checknumber(f, n){
  if(f.value > 0){
    if(f.value > n){
      verErro("Valor inválido: " + f.value);
      f.value = '';
      f.focus();
      return false;
    }
  }else{
    verErro("Valor inválido: " + f.value);
    f.value = '';
    f.focus();
    return false;
  }
  return true;
}
</script>
