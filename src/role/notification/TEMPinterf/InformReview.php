<body bgColor="#ffffcc" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">

 <?php require_once ("client.php")?>

<table border="0" width="100%">
  <tr>
    <td width="100%" colspan="2" bgcolor="#FF8C00"><FONT size=4>Observações</FONT> 
    </td>
  </tr>
  <tr>
    <td width="90%">
       <H3>Situação do Cadastro: OK<br>
           Data de Cadastro: <FONT color=#ff8c00>27/01/2002</FONT><br>
           Nome do Responsável: <FONT color=#ff8c00>Antônio Iglesias</FONT><br>
           Cargo: <FONT color=#ff8c00>Gerente Administrativo</FONT><br>
       Pré-Tarifação: Tx Prêmio: <FONT color=#ff8c00>0,745</FONT><br> 
           Prêmio Mínimo: <FONT  color=#ff8c00>12..0000,00</FONT><br>
       </h3>
    </td>
    <form action="../notification/BoxInput.php">
    <td width="10%"><INPUT type=submit value=Aceitar><A href="../BoxInput.php"></A>
                    <INPUT type=submit value=Rejeitar><A href="../BoxInput.php"></A>
    </td>
    </form>
  </tr>
</table>