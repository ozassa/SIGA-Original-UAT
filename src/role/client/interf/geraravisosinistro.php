<script>
function calc (form) {
   form.valueAbt.value = numVal (form.valueFat.value)/1 - numVal(form.valuePag.value)/1;
   checkDecimals(form.valueAbt,dot2comma(form.valueAbt.value));
}
</script>
<script Language="JavaScript">
<!--
function checa_formulario(cadastro){
  if (cadastro.cause.value == ""){
    verErro("Por Favor, Preencha o Motivo");
    cadastro.cause.focus();
    return (false);
  }
  if (cadastro.nameRes.value == ""){
    verErro("Por Favor, Preencha o Nome do Responsável");
    cadastro.nameRes.focus();
    return (false);
  }
  if (cadastro.position.value == ""){
    verErro("Por Favor, Preencha o Cargo do Responsável");
    cadastro.position.focus();
    return (false);
  }
  if (cadastro.tel.value == ""){
    verErro("Por Favor, Preencha o Telefone");
    cadastro.tel.focus();
    return (false);
  }
  if (cadastro.email.value == ""){
    verErro("Por Favor, Preencha o Email");
    cadastro.email.focus();
    return (false);
  }
  if (cadastro.email.value.indexOf('@', 0) == -1){
    verErro("O E-mail é Inválido !!!");
    cadastro.email.focus();
    return (false);
  }
  return (true);
}
-->
</script>

<script>
function checkDecimals2(fieldName, fieldValue) {

  if (fieldValue == "0,00") {
    verErro("Preenchimento obrigatório.");
    fieldName.value='';
    fieldName.focus();
  } else {
    err = false;
    dec = ",";
    mil = ".";
    v = "";
    c = "";
    len = fieldValue.length;
    for (i = 0; i < len; i++) {
      c = fieldValue.substring (i, i+1);
      if (c == dec) { break; }
      if (c != mil) {
        if (isNaN(c)) {
          err = true;
          verErro("Este não é um número válido.");
          fieldName.value='0,00';
          fieldName.focus();
          break;
        } else {
          v += c;
        }
      }
    }
      if (!err) {
      if (i == len) {
        v += "00";
      } else {
        if (c == dec) i++;
        if (i == len) {
          v += "";
        } else {
          c = fieldValue.substring (i, i+1);
          if (isNaN(c)) {
            verErro("Este não é um número válido.");
            fieldName.value='0,00';
            fieldName.focus();
            err = true;
          } else {
            v += c;
         }
       }
        i++;
        if (!err && i == len) {
          v += "0";
        } else {
          c = fieldValue.substring (i, i+1);
          if (isNaN(c)) {
            verErro("Este não é um número válido.");
            fieldName.value='0,00';
            fieldName.focus();
            err = true;
          } else {
            v += c;
          }
        }
      }	  
	if(fieldValue.match(/^\d+$/)){
	  fieldName.value = fieldValue + ',00';
	}else if(fieldValue.match(/^(\d+)(,|.)\d\d/)){
	  fieldName.value = fieldValue.replace(/^(\d+)(,|.)(\d\d)\d*$/, '$1' + ',' + '$3');
	}else{
	  fieldName.value = fieldValue.replace(/\./, ',');
	  fieldName.value += '';
	}
      }
    }
  }

</script>

<script language="JavaScript" src="<?php  echo $root;?>scripts/calendario.js"></script>
<a name=sinistro></a>
<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD colspan="4" align="center"><H3>Aviso de Sinistro</H3></TD>
  </TR>
   <TR>
    <TD colspan="4">&nbsp;</TD>
  </TR>
<?php  $var=odbc_exec($db,
	"SELECT Importer.name, Importer.address, Importer.tel, 
                Importer.fax, Importer.contact, Importer.endosso, 
                Importer.c_Coface_Imp, Country.name
	FROM Importer, Country
	WHERE Importer.id = $idImporter AND Importer.idCountry = Country.id");
?>
  <TR class=bgAzul>
    <td align=center colspan=4>Dados do Importador</TD>
  </TR>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
  <TR>
    <td>Nome:</td>
    <td colspan=3 class="texto"><?php  echo odbc_result($var,1);?>&nbsp;</td>
  </TR>
  <TR>
    <td width="15%">País:</td>
    <td class="texto" width="50%"><?php  echo odbc_result($var,8);?>&nbsp;</td>
    <td width="15%">Ci Importador:</td>
    <td class="texto" width="20%"><?php  echo odbc_result($var,7);?>&nbsp;</td>
  </TR>
  <TR>
    <td>Endereço:</td>
    <td colspan=3 class="texto"><?php  echo odbc_result($var,2);?>&nbsp;</td>
  </TR>
  <TR>
    <td>Tel.:</td>
    <td class="texto"><?php  echo odbc_result($var,3);?>&nbsp;</td>
    <td>Fax:</td>
    <td class="texto"><?php  echo odbc_result($var,4);?>&nbsp;</td>
  </TR>
  <TR>
    <td nowrap>Contato:</td>
    <td colspan=3 class="texto"><?php  echo odbc_result($var,5);?>&nbsp;</td>
  </TR>
<?php  $q = "SELECT b.name
            FROM CDBB cdb
              JOIN CDBBDetails cdbd ON (cdbd.idImporter = $idImporter)
              JOIN Banco b ON (b.id = 1)
            WHERE cdb.status = 2 or cdb.status = 4
            UNION
            SELECT b.name
            FROM CDOB cdo
              JOIN CDOBDetails cdod ON (cdod.idImporter = $idImporter)
              JOIN Banco b ON (b.id = cdo.idBanco)
            WHERE cdo.status = 2 or cdo.status = 4
            UNION
            SELECT b.name
            FROM CDParc cdp
              JOIN CDParcDetails cdpd ON (cdpd.idImporter = $idImporter)
              JOIN Banco b ON (b.id = cdp.idBanco)
            WHERE cdp.status = 2 or cdp.status = 4";

       $c = odbc_exec($db, $q);
       //echo $q;
       $banco = odbc_result($c, 1);
       if($banco){
?>
  <TR>
    <td>Cessão Para:</td>
    <td colspan=3 class="texto"><?php  echo $banco;?>&nbsp;</td>
  </TR>
<?php  }?>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
</table>

<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR class=bgCinza>
    <td align=center colspan=6><font size=2>INCLUSÃO DE FATURAS EM ABERTO, VENCIDAS E A VENCER, REFERENTES<BR>A EMBARQUES PARA A EMPRESA IMPORTADORA (**):</FONT></TD>
  </TR>
  <TR>
    <td colspan=6>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=6>Faturas declaradas na DVE</td>
  </TR>
  <!-- início da DVE -->
  <TR>
    <td class=bgAzul align=center WIDTH="10%">Nº Fatura</td>
    <td class=bgAzul align=center>Data de Embarque</td>
    <td class=bgAzul align=center>Data de Vencimento</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>&nbsp;</td>
  </TR>
<?php  $cur=odbc_exec($db,
	"SELECT d.inicio, d.periodo,
             dd.embDate, dd.vencDate, dd.fatura, dd.totalEmbarcado, dd.id
	FROM DVE d JOIN DVEDetails dd ON (dd.idDVE = d.id)
	WHERE d.idInform = $idInform AND dd.idImporter = $idImporter");
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $dateEmb = odbc_result($cur,3); 
    $dateVenc = odbc_result($cur,4);
    $valor = odbc_result($cur, 6);
    $numFat = odbc_result($cur,5);
    $idDVE = odbc_result($cur,7);

    $query = "SELECT valuePag, valueAbt FROM SinistroDetails WHERE idDVE = $idDVE";
    $sol = odbc_exec($db,$query);
    if (!odbc_fetch_row($sol)) {
       $aparece = 1;
?>
<FORM action="<?php  echo $root;?>role/client/Client.php#sinistro" method="post" name="gera_aviso<?php  echo $i;?>">
<input type=hidden name="comm" value="geraravisosinistroSQL">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="idImporter" value="<?php  echo $idImporter;?>">
<input type=hidden name="idSinistro" value="<?php  echo $idSinistro;?>">
<input type=hidden name="idDVE" value="<?php  echo $idDVE;?>">
<input type=hidden name="action" value="valor">
<input type=hidden name="i" value="<?php  echo $i;?>">
<input type=hidden name="codigo" value="<?php  echo $codigo;?>">
  <TR <?php  echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"");?>>
    <input type="hidden" name="valueFat" value="<?php  echo number_format($valor,2,",",".");?>">
    <input type=hidden name="dateEmb" value="<?php  echo $dateEmb;?>">
    <input type=hidden name="dateVenc" value="<?php  echo $dateVenc;?>">
    <input type=hidden name="numFat" value="<?php  echo $numFat;?>">
    <td align=center class="texto"><?php  echo $numFat;?></td>
    <td align=center class="texto"><?php  echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,0,4);?></td>
    <td align=center class="texto"><?php  echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,0,4);?></td>
    <td align=center class="texto"><?php  echo number_format($valor,2,",",".");?></td>
    <td align=center class="texto"><INPUT class=caixa size=15 name=valuePag onBlur="checkDecimals(this, this.value);"></td>
    <td align=center class="texto"><INPUT class=servicos type="submit" value="OK" name=button1></td>
  </TR>
</form>

<?php  } // if
  } // while
  if ($i == 0) {
?>

  <TR class="bgCinza">
    <TD align="center" colspan=6 class="bgCinza">Nenhuma DVE Cadastrada</TD>
  </TR>

<?php  }
  if($msgA){
?>
  <TR>
    <td colspan=6 class="verm" align="center"><br><?php  echo $msgA;?></td>
  </TR>
<?php  } ?>

<FORM action="<?php  echo $root;?>role/client/Client.php#sinistro" method="post" name="incluir">
<input type=hidden name="comm" value="">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="idImporter" value="<?php  echo $idImporter;?>">
<input type=hidden name="idSinistro" value="<?php  echo $idSinistro;?>">
<input type=hidden name="idDVE" value="0">
<input type=hidden name="action" value="incluir">
<input type=hidden name="formfocus" value="incluir">
<input type=hidden name="fieldfocus" value="numFat">
<input type=hidden name=dateEmb value="">
<input type=hidden name=dateVenc value="">
<input type=hidden name="sol" value="<?php  echo $sol;?>">
<input type=hidden name="codigo" value="<?php  echo $codigo;?>">
<input type=hidden name="Ndve" value="1">

  <TR>
    <td colspan=6><br>Faturas não declaradas na DVE</td>
  </TR>
<?if($msgP){?>  
  <TR>
    <td colspan=6 class="verm" align="center"><?php  echo $msgP;?></td>
  </TR>
  <TR>
    <td colspan=6>&nbsp;</td>
  </TR>
<?php  }?>
  <TR>
    <td align=center colspan="2">Nº Fatura<br><INPUT class=caixa size=4 name="numFat"></td>
    <td align=center colspan="2">Data de Embarque<br>
<input type=text class=caixa size=2 name=dataEmbDia maxlength=2 onkeyup="proximo(this, 2, this.form.dataEmbMes, 31)"> / 
<input type=text class=caixa size=2 name=dataEmbMes maxlength=2 onkeyup="proximo(this, 2, this.form.dataEmbAno, 12)"> / 
<input type=text class=caixa size=4 name=dataEmbAno maxlength=4 onkeyup="proximo(this, 4, this.form.dataVencDia, 9999)">
<!--INPUT class=caixa size=10 name="dateEmb" onFocus="blur()"> <A HREF="javascript:showCalendar(document.incluir.dateEmb)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"--></A></td>
    <td align=center colspan="2">Data de Vencimento<br>
<input type=text class=caixa size=2 name=dataVencDia maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencMes, 31)"> / 
<input type=text class=caixa size=2 name=dataVencMes maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencAno, 12)"> / 
<input type=text class=caixa size=4 name=dataVencAno maxlength=4 onkeyup="proximo(this, 4, this.form.valueFat, 9999)">
<!--INPUT class=caixa size=10 name="dateVenc" onFocus="blur()"> <A HREF="javascript:showCalendar(document.incluir.dateVenc)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A--></td>
  </TR>
  <TR>
    <td align=center colspan="2">Valor da Fatura<br><INPUT class=caixa size=10 name="valueFat" onBlur="checkDecimals(this, this.value);calc(this.form)"></td>
    <td align=center colspan="2">Valor Pago<br><INPUT class=caixa size=10 name="valuePag" onBlur="checkDecimals2(this, this.value);calc(this.form)"></td>
    <td align=center colspan="2">Valor em Aberto<br><INPUT class=caixa size=10 name="valueAbt" onBlur="checkDecimals(this, this.value)" onFocus="blur()"> <INPUT class=servicos type="button" value="OK" name=button1 onClick="manda(this.form, 'geraravisosinistroSQL')"></td>
  </TR>
  <!--TR>
    <td class=bgAzul align=center>Nº Fatura</td>
    <td class=bgAzul align=center>Data de Embarque</td>
    <td class=bgAzul align=center>Data de Vencimento</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>Valor em Aberto</td>
  </TR-->
  <!--TR>
    <td align=center><INPUT class=caixa size=10 name="numFat" tabindex="1"></td>
    <td align=center><INPUT class=caixa size=11 name="dateEmb" tabindex="2"> <A HREF="javascript:showCalendar(document.incluir.dateEmb)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A></td>
    <td align=center><INPUT class=caixa size=11 name="dateVenc" tabindex="2"> <A HREF="javascript:showCalendar(document.incluir.dateVenc)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A></td>
    <td align=center><INPUT class=caixa size=15 name="valueFat" onBlur="checkDecimals(this, this.value);calc(this.form)" tabindex="4"></td>
    <td align=center><INPUT class=caixa size=15 name="valuePag" onBlur="checkDecimals2(this,this.value);calc(this.form)" tabindex="5"></td>
    <td align=center><INPUT class=caixa size=15 name="valueAbt" onBlur="checkDecimals(this, this.value)" onFocus="blur()" tabindex="6"> <INPUT class=servicos type="submit" value="OK" name=button1></td>
  </TR-->
</form>
  <TR>
    <td colspan=6>&nbsp;</td>
  </TR>
  <TR>
    <td align=center colspan=6>(**) Faturas não pagas, mesmo não cobertas pela SBCE.</td>
  </TR>
  <TR>
    <td colspan=6>&nbsp;</td>
  </TR>
</table>

<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR class=bgCinza>
    <td align=center colspan=7><font size=2>OBJETO DO AVISO DE SINISTRO</FONT></TD>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <td class=bgAzul align=center>Nº Fatura</td>
    <td class=bgAzul align=center>Data de Embarque</td>
    <td class=bgAzul align=center>Data de Vencimento</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>Valor em Aberto</td>
    <td class=bgAzul align=center>&nbsp;</td>
  </TR>
<?php  $query = "SELECT * FROM SinistroDetails WHERE idSinistro = $idSinistro ORDER BY numFat";
    $cur = odbc_exec($db,$query);
    $i = 0;
    $valueTotal = 0;
    while (odbc_fetch_row($cur)) {
      $i++;
      $aparece = 1;
      $idSinDet = odbc_result($cur,1); 
      $dateEmb = odbc_result($cur,5); 
      $dateVenc = odbc_result($cur,6);
      $valuePag = odbc_result($cur, 7);
      $valueFat = odbc_result($cur, 8);
      $valueAbt = odbc_result($cur, 9);
      $valueTotal = $valueTotal + $valueAbt;

?>
  <TR <?php  echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"");?>>
    <td class="texto" align=center><?php  echo odbc_result($cur,4);?></td>
    <td class="texto" align=center><?php  echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,0,4);?></td>
    <td class="texto" align=center><?php  echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,0,4);?></td>
    <td class="texto" align=right><?php  echo number_format($valueFat,2,",",".");?> &nbsp;</td>
    <td class="texto" align=right><?php  echo number_format($valuePag,2,",",".");?> &nbsp;</td>
    <td class="texto" align=right><?php  echo number_format($valueAbt,2,",",".");?> &nbsp;</td>
    <td class="texto" align=center> <a href="javascript:onClick=exclui(<?php  echo $idSinDet ?>);"><!--a href="<?php  echo $root;?>role/client/Client.php?comm=RemFatura&idSinDet=<?php  echo $idSinDet;?>&idInform=<?php  echo $idInform;?>&idImporter=<?php  echo $idImporter;?>&idSinistro=<?php  echo $idSinistro;?>"-->Remover</a></td>
  </TR>
<?php  } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=7 class="bgCinza">Nenhum Dado Cadastrado</TD>
  </TR>

<?php  }
?>
  <TR>
    <td class=bgAzul align=right colspan=5>Total (em aberto):</td>
    <td align=right><?php  echo number_format($valueTotal,2,",",".");?> &nbsp;</td>
    <td align=center>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
<?php  if ($aparece == 1){
?>
  <TR class=bgCinza>
    <td align=center colspan=7><font size=2>SOBRE O SINISTRO</FONT></TD>
  </TR>
<FORM action="<?php  echo $root;?>role/client/Client.php" method="post" name="cadastro" onsubmit="return checa_formulario(this)" onLoad="Ini()">
<input type=hidden name="comm" value="gerarNotf">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="idSinistro" value="<?php  echo $idSinistro;?>">
<input type=hidden name="idImporter" value="<?php  echo $idImporter;?>">
<input type=hidden name="codigo" value="<?php  echo $codigo;?>">
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=7>Motivo alegado para o não pagamento:</td>
  </TR>
  <TR>
    <td colspan=7 align=center><TEXTAREA class=caixa rows=6 cols=70 name="cause"></TEXTAREA></td>
  </TR>
  <TR>
    <td>Nome do Responsável:</td>
    <td colspan=6><INPUT class=caixa size=60 id=text2 name="nameRes"></td>
  </TR>
  <TR>
    <td>Cargo:</td>
    <td colspan=2><INPUT class=caixa id=text2 name="position"></td>
    <td>Telefone:</td>
    <td colspan=3><INPUT class=caixa id=text2 name="tel"></td>
  </TR>
  <TR>
    <td>E-mail:</td>
    <td colspan=6><INPUT class=caixa size=30 id=text2 name="email"></td>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=7>A aceitação do Aviso de Sinistro está condicionada ao recebimento dos documentos abaixo relacionados e verificação do cumprimento das condições gerais e particulares da apólice.<BR><BR>
     - Documentos a serem encaminhados à SBCE por Correio:<BR><BR>
     * Cópia da Fatura<BR>
     * Cópia do Conhecimento de Transporte<BR>
     * RE averbado Completo (Campos 1 a 30)<BR>
     * Cópia do Purchase Order<BR>
     * Cópia da Packing List<BR>
     * Cópia das Correspondências Trocadas<BR>
     * Cópia dos Títulos de Crédito Por Ventura Existentes<BR><BR>
     OBS: Os três primeiros itens são obrigatórios
     </td>
   </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=6>&nbsp;</td>
  </TR>
<?php  if($sol == "banco"){?>
<input type=hidden name="sol" value="banco">
  <TR>
    <td colspan=7 align="center">
<INPUT class=servicos onclick="this.form.comm.value='consultaImpCessao';this.form.submit()" type=button value="Voltar">
<input type="submit" value="Gerar Aviso de Sinistro" class="servicos">
    </td>
  </TR>
<?php  }else{?>
  <TR>
    <td colspan=7 align="center">
<INPUT class=servicos onclick="this.form.comm.value='avisosinistro';this.form.submit()" type=button value="Voltar">
<input type="submit" value="Gerar Aviso de Sinistro" class="servicos">
    </td>
  </TR>
<?php  }?>
</form>
<?php  } else {
?>
  <TR>
    <td colspan=7 align="center">
<FORM action="<?php  echo $root?>role/client/Client.php" method="post">
<input type=hidden name="comm">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="idImporter" value="<?php  echo $idImporter;?>">
<input type=hidden name="codigo" value="<?php  echo $codigo;?>">
<?php  if($sol == "banco"){?>
<INPUT class=servicos onclick="this.form.comm.value='consultaImpCessao';this.form.submit()" type=button value="Voltar">
<?php  }else{?>
<INPUT class=servicos onclick="this.form.comm.value='avisosinistro';this.form.submit()" type=button value="Voltar">
<?php  }?>
</form></td>
  </TR>
<?php  } 
?>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>

</TABLE>

<form name="exclui" action="<?php  echo $root;?>role/client/Client.php">
<input type=hidden name="comm" value="RemFatura">
<input type=hidden name="idSinDet" value="">
<input type=hidden name="idInform" value="<?php  echo $idInform;?>">
<input type=hidden name="idSinistro" value="<?php  echo $idSinistro;?>">
<input type=hidden name="idImporter" value="<?php  echo $idImporter;?>">
<input type=hidden name="codigo" value="<?php  echo $codigo;?>">
</form>

<script>
function exclui(myIdSinDet) { 
if (confirm ("Deseja Realmente Excluir essa Fatura?")) {
   document.forms["exclui"].idSinDet.value=myIdSinDet;
   document.forms["exclui"].submit();
}
}

var f = document.formulario;

function manda(f, c){
  if(confirma(f)){
    f.comm.value = c;
    f.submit();
  }
}

function confirma(f){
  if(f.dataEmbDia.value == ''){
    verErro("Favor preencher a data de embarque");
    f.dataEmbDia.focus();
    return false;  
  }
  if(f.dataEmbMes.value == ''){
    verErro("Favor preencher a data de embarque");
    f.dataEmbMes.focus();
    return false;
  }
  if(f.dataEmbAno.value == ''){
    verErro("Favor preencher a data de embarque");
    f.dataEmbAno.focus();
    return false;
  }
  if(f.dataVencDia.value == ''){
    verErro("Favor preencher a data de vencimento");
    f.dataVencDia.focus();
    return false;
  }
  if(f.dataVencMes.value == ''){
    verErro("Favor preencher a data de vencimento");
    f.dataVencMes.focus();
    return false;
  }
  if(f.dataVencAno.value == ''){
    verErro("Favor preencher a data de vencimento");
    f.dataVencAno.focus();
    return false;
  }

  f.dateEmb.value = f.dataEmbDia.value + '/' + f.dataEmbMes.value + '/' + f.dataEmbAno.value;
  f.dateVenc.value = f.dataVencDia.value + '/' + f.dataVencMes.value + '/' + f.dataVencAno.value;
  return true;
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
