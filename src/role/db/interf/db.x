<?
// interface para o banco de dados
?>

<head><title><?= $title ?></title></head>
<body bgcolor=#FFFFFF>
<form action="/siex/src/role/access/Access.php" method=post name=f>
<input type="hidden" name="comm" value="database">
<p>Query:<br>
<textarea name="query" rows=10 cols=90>
<? echo $query; ?>
</textarea>
<br>
<input type="submit" value="OK">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=button onClick="document.forms[0].query.value=''; document.forms[0].query.focus()" name="b" value="limpar">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=checkbox name=sisseg <?= $sisseg == 'on' ? 'checked' : '' ?>>sisseg
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=checkbox name=fields>mostrar apenas os campos
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
inicio: <input type=text name=inicio value=<?= $inicio? $inicio : '0'?>>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
fim: <input type=text name=fim value=<?= $fim ? $fim : '10'?>>
</form>

<script language=javascript>
document.f.query.focus();
</script>

<?

if($result){
  echo "<p>Resultados:<br>";
  echo $result;
}

?>
