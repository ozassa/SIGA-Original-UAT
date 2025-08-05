<?php
  include_once("../../consultaCoface.php");

?>
<TABLE width=100% cellspacing=0 cellpadding=2 border="0" align="center">
<TR align=center class="bgAzul">

<td class="bgAzul">Cliente</td>
<td class="bgAzul">Ci <?php echo $nomeEmp; ?></td>
<td class="bgAzul">Data de envio</td>
<td class="bgAzul">Intervalo (Dias)</td>
<td class="bgAzul">Tipo de Alteração</td>

</TR>

<?php  $i = 0;
while(odbc_fetch_row($clientnotconfirmed)) {
  $name     = odbc_result($clientnotconfirmed, 1);
  $contrat  = odbc_result($clientnotconfirmed, 2);
  $send     = odbc_result($clientnotconfirmed, 3);
  $alter    = odbc_result($clientnotconfirmed, 4);

  if ($alter == 3){
    $alter = "Mudança de dados";
  }else{
    $alter = "Mudança de limite de crédito";
  }

  $i++;

  print("<tr ");
  print ($i % 2 == 0) ? "bgcolor = #e9e9e9": "";
  print(" align=center class=texto>");

  $date  = substr($send, 8, 2)."/".substr($send, 5, 2)."/".substr($send, 0, 4);
  $today = date("j/n/Y"); // dia/mes/ano
  $time  = $today - $date;

  print ("<td class=texto>$name</td>");
  print ("<td class=texto>$contrat</td>");
  print ("<td class=texto>$date</td>");
  print ("<td class=texto>$time</td>");
  print ("<td class=texto>$alter</td></tr>");
} // while

?>

<tr><br>
</tr>
</table>

<table>

<tr><br></tr>
</tr>

</TABLE>
