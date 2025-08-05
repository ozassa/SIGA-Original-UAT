<HTML>
<HEAD>
 <TITLE></TITLE>
<script language="javascript">
       function Validacao(){
           if (document.getElementById("inicio").value == "" ) {
              verErro('Campo data início não pode ser vazio.');
              return false;
           }else if (document.getElementById("fim").value == "" ) {
              verErro('Campo data fim não pode ser vazio.');
              return false
           }
           
           return true;
       }
       
       function formatar(src, mask) {
           var i = src.value.length;
           var saida = mask.substring(0,1);
           var texto = mask.substring(i)

           if (texto.substring(0,1) != saida) {
            src.value += texto.substring(0,1);
           }
       }
</script>

</HEAD>
<BODY>
<br><br><br>
<form name="frmRelBonus" action="../credit/relBonus.php" method="post" target="_blank" onSubmit="return Validacao()">
    <TABLE align="center" width="70%" border="0" cellspacing="1" cellpadding="1">
   <tr>
      <td>Data Início</td>
      <td><INPUT type="text" name="inicio" id="inicio" style="width:100px" onkeypress="formatar(this,'##/##/####');" maxlength="10" value="00/00/0000"></td>
      <td>Data Fim</td>
      <td><INPUT type="text" name="fim" id="fim" style="width:100px" onkeypress="formatar(this,'##/##/####');" maxlength="10" value="00/00/0000"></td>
   </tr>
   <tr>
       <td width="100%" colspan="3"><input type="checkbox" name="tds" value="1"  >Listar todos segurados</td>
       <td align="right"><input type="submit" name="enviar" value="Enviar"></td>
   </tr>
    </TABLE>
</form>
</BODY>
</HTML>
