<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
  <script language="javascript" src="<?php echo $root; ?>scripts/calendario.js"></script>
  <script language="javascript" src="<?php echo $root; ?>scripts/utils.js"></script>
  <script language="javascript">
    var ci = new Array();
  </script>

  <?php

  // Função Para validar a datas
  function Convert_Data_Interf($data)
  {
    if (strstr($data, "/")) {//verifica se tem a barra /
      $d = explode("/", $data);//tira a barra
      $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mês etc...
  
      return $invert_data;
    } elseif (strstr($data, "-")) {
      $d = explode("-", $data);
      $invert_data = "$d[2]/$d[1]/$d[0]";

      return $invert_data;
    }
  }

  function SomarData($data, $dias, $meses, $ano)
  {
    //passe a data no formato dd/mm/yyyy
    $data = explode("/", $data);
    $newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[0] + $dias, $data[2] + $ano));

    return $newData;
  }
  ?>

  <li class="campo3colunas">
    <label>Segurado: </label>
    <?php echo $namecl; ?>
  </li>

  <li class="campo3colunas">
    <label>Ap&oacute;lice n&deg;: </label>
    <?php echo $apolice; ?>
  </li>

  <li class="campo3colunas">
    <label>Vig&ecirc;ncia: </label>
    <?php echo $start . ' &agrave; ' . $end; ?>
  </li>

  <div style="clear:both">&nbsp;</div>

  <li class="campo3colunas">
    <label>Per&iacute;odo de Declara&ccedil;&atilde;o: </label>
    <?php echo $inicio . ' &agrave; ' . $fim . ' (' . $num . ' &ordf; DVE)'; ?>
  </li>

  <li class="campo3colunas">
    <label>Data Limite para Declara&ccedil;&atilde;o: </label>
    <?php echo $Data_Limite_Periodo; ?>
  </li>

  <div style="clear:both">&nbsp;</div>

  <?php

  //$dataFimperiodo =  SomarData($fim, 15,0 , 0); // acresecenta 15 dias para a validação
  
  $dataFimperiodo = $Data_Limite_Periodo;
  $dataHoje = Convert_Data_Interf(date('d/m/Y'));

  $DataCadatro = $dataHoje;

  if (($dataHoje > Convert_Data_Interf($dataFimperiodo)) && (!$LiberaAtraso == 1)) {
    $Valida = 1;
  } else {
    $Valida = 0;
  }

  $sql = "select  state from DVE where id = '" . $idDVE . "'";
  $res = odbc_exec($db, $sql);

  $stateDVE = odbc_result($res, 'state');

  $cur = odbc_exec($db, "select  currency, isnull(periodMaxCred, 180) as periodMaxCred from Inform where id=$idInform");

  if (odbc_fetch_row($cur)) {
    $moeda = odbc_result($cur, "currency");
    $periodMaxCred = odbc_result($cur, "periodMaxCred");
  }

  if ($moeda == "1") {
    $tmoeda = "Real";
    $extMoeda = "R$";
    $ext = "Real";
  } else if ($moeda == "2") {
    $tmoeda = "Dolár";
    $extMoeda = "US$";
    $ext = "Dolar Norte Americano";
  } else if ($moeda == "6") {
    $tmoeda = "Euro";
    $extMoeda = "€";
    $ext = "Euro";
  } else if ($moeda == "0") {
    $tmoeda = "Dolár";
    $extMoeda = "US$";
    $ext = "Dolar Norte Americano";
  }
  ?>

  <form action="<?php echo $root; ?>role/dve/Dve.php#tabela">
    <input type="hidden" name="comm" value="modalidade">
    <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
    <input type="hidden" name="idDVE" value="<?php echo $idDVE; ?>">
    <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
    <input type="hidden" name="client" value="<?php echo $client; ?>">
    <input type="hidden" name="viewflag" value="<?php echo $viewflag; ?>">
    <input type="hidden" name="fieldfocus" value="">
    <input type="hidden" name="formfocus" value="">

    <div style="clear:both">&nbsp;</div>

    <li class="campo3colunas">
      <label>Escolha a Modalidade de Venda:</label>
      <select class="caixa" name="modalidade">
        <option value="1" <?php echo $modalidade == 1 ? 'selected' : ''; ?>>&Agrave; vista, cobran&ccedil;a a
          prazo
        </option>
        <option value="2" <?php echo $modalidade == 2 ? 'selected' : ''; ?>>Via coligada</option>
        <option value="3" <?php echo $modalidade == 3 ? 'selected' : ''; ?>>Antecipado e/ou Carta de
          Cr&eacute;dito
        </option>
      </select>
    </li>

    <li class="campo3colunas">
      <label>&nbsp;</label>
      <button class="botaoagm" type="button" onClick="this.form.submit()">OK</button>
    </li>
  </form>

  <form action="<?php echo $root; ?>role/dve/Dve.php#tabela" method="post" name="formulario">
    <input type="hidden" name="emb_dec" id="emb_dec" value="<?php echo isset($emb_dec) ? $emb_dec : false; ?>">
    <input type="hidden" name="comm" value="view">
    <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
    <input type="hidden" name="idDVE" value="<?php echo $idDVE; ?>">
    <input type="hidden" name="client" value="<?php echo $client; ?>">
    <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
    <input type="hidden" name="modalidade" value="<?php echo $modalidade; ?>">
    <input type="hidden" name="idDetail" value="<?php echo $idDetail; ?>">
    <input type="hidden" name="dataEmb" value="">
    <input type="hidden" name="dataVenc" value="">
    <input type="hidden" name="viewflag" value=1>
    <input type="hidden" name="primeira_tela" value="">

    <div style="clear:both">&nbsp;</div>

    <?php if ($modalidade == 1) { ?>
      <li class="campo2colunas" style="width:600px;">
        <label>
          <?php echo ("Preencher somente as vendas com cobrança à vista ou Cobrança a prazo até " . $periodMaxCred . " dias"); ?>
        </label>
      </li>
      <div style="clear:both">&nbsp;</div>
    <?php } else if ($modalidade == 2) { ?>
        <li class="campo2colunas">
          <label>
          <?php echo ("Preencher somente as vendas através de Coligadas"); ?>
          </label>
        </li>
        <div style="clear:both">&nbsp;</div>
    <?php } ?>

    <li class="campo3colunas">
      <label>Pa&iacute;s</label>
      <select tabindex="1" name="idCountry" onChange="showimporters(this)">
        <?php
        if (!$idCountry) {
          $show = 0;
        }

        if (!$show) {
          echo ("<option value=0>Escolha um país\n");
        } else {
          echo "<option value=0></option>\n";
          // Alterado Hicom (Gustavo)
          // antes:   $cur = odbc_exec($db, "select id, name, c_Coface_Imp from Importer where idInform=$idInform and idCountry=$idCountry");
          $cur = odbc_exec($db, "select   id, name, c_Coface_Imp " .
            "from     Importer " .
            "where    idInform=$idInform " .
            "and idCountry=$idCountry " .
            //Alterador Por Tiago V N - 03/10/2005
            "and c_Coface_Imp IS NOT NULL " .
            "and (state <> 9) " .
            "and (state <> 7) " .
            "order by   name");
          // fim alterado Hicom
        
          if (!odbc_fetch_row($cur)) {
            $show = 0;
          }
        }

        $x = odbc_exec($db, "select distinct c.id, c.name from Country c join Importer imp on imp.idCountry=c.id
                  join Inform inf on inf.id=imp.idInform where inf.id=$idInform order by c.name");

        while (odbc_fetch_row($x)) {
          $id = odbc_result($x, 1);
          $pais = odbc_result($x, 2);
          echo ("<option value=$id" . ($id == $idCountry ? ' selected' : '') . "> $pais </option>");
        }
        ?>
      </select>
    </li>



   <li class="campo3colunas">
    <label>&nbsp;</label>
    <input type="hidden" name="registro" value="<?php echo $registro; ?>">

    <button class="botaoapm" type="button" tabindex="2"
        <?php if ($registro > 1) { ?>
            onClick="this.form.comm.value='modalidade'; this.form.registro.value=<?php echo $registro - 1; ?>; this.form.submit();"
        <?php } ?>>
        &nbsp;<<&nbsp;
    </button>

    <?php echo ($registro == $num_registros + 1) ? "<span>Reg. $registro</span>" : "<span>Reg. $registro de $num_registros</span>"; ?>

    <button class="botaoapm" type="button" tabindex="3"
        <?php if ($registro < $num_registros + 1) { ?>
            onClick="this.form.comm.value='modalidade'; this.form.registro.value=<?php echo $registro + 1; ?>; if(this.form.registro.value == <?php echo $num_registros + 1; ?>){ this.form.show.value = 0; this.form.idCountry.selectedIndex = 0; } this.form.submit();"
        <?php } ?>>
        &nbsp;>>&nbsp;
    </button>
</li>


    <div style="clear:both">&nbsp;</div>

    <?php
    if ($show == 0 && $idCountry > 0) {
      $show = 1;
    }

    //$show = 1;
    if ($show) { ?>
      <li class="campo3colunas">
        <label>Comprador Final</label>
        <select name="idBuyer" id="idBuyer" tabindex="4" onChange="muda_ci(this)">
          <option value="0">Selecione um comprador</option>
          <?php $cis[0] = '';
          do {
            $idImp = odbc_result($cur, 1);
            $nameImp = trim(odbc_result($cur, 2));
            $cis[] = odbc_result($cur, 3);
            echo "<option value=$idImp" . ($idImp == $idBuyer ? ' selected' : '') . ">$nameImp</option>";
          } while (odbc_fetch_row($cur));
          ?>
        </select>
      </li>
      <?php
      echo "<script language=javascript>\n";
      for ($i = 1; $i < count($cis); $i++) {
        echo "ci[$i] = '$cis[$i]';\n";
      }
      echo "</script>\n";
      ?>

      <li class="campo3colunas">
        <label>N&ordm; Seguradora</label>
        <label id="cicofacelbl"></label>
        <input type="hidden" tabindex="5" name="cicoface" onFocus="blur()">
      </li>

      <li class="campo3colunas">
        <label>N&ordm; Fatura</label>
        <input type="text" tabindex="6" name="fatura" id="fatura" value="<?php echo $fatura; ?>">
      </li>

      <li class="campo3colunas">
        <label>Embarque (dd/mm/aaaa)</label>
        <input type="text" name="dataEmbDia" style="width:60px;" maxlength=2 tabindex=7
          value="<?php echo $dataEmbDia; ?>" onkeyup="proximo(this, 2, this.form.dataEmbMes, 31)"> /
        <input type=text name="dataEmbMes" style="width:60px;" maxlength=2 tabindex=8
          value="<?php echo $dataEmbMes; ?>" onkeyup="proximo(this, 2, this.form.dataEmbAno, 12)"> /
        <input type=text name="dataEmbAno" style="width:100px;" maxlength=4 tabindex=9
          value="<?php echo $dataEmbAno; ?>" onkeyup="proximo(this, 4, this.form.dataVencDia, 9999)">
      </li>

      <li class="campo3colunas">
        <label>Vencimento Fatura (dd/mm/aaaa)</label>
        <input type="hidden" name="LiberaVencida" id="LiberaVencida" value="<?php echo $LiberaVencida; ?>">
        <input type="hidden" name="Valida" id="Valida" value="<?php echo $Valida; ?>">
        <input type="hidden" name="DataCadastro" id="DataCadastro" value="<?php echo $DataCadastro; ?>">
        <input type=text name="dataVencDia" style="width:60px;" maxlength=2 tabindex=10
          value="<?php echo $dataVencDia; ?>" onkeyup="proximo(this, 2, this.form.dataVencMes, 31)"> /
        <input type=text name="dataVencMes" style="width:60px;" maxlength=2 tabindex=11
          value="<?php echo $dataVencMes; ?>" onkeyup="proximo(this, 2, this.form.dataVencAno, 12)"> /
        <input type=text name="dataVencAno" style="width:100px;" maxlength=4 tabindex=12
          value="<?php echo $dataVencAno; ?>" onkeyup="proximo(this, 4, this.form.valorEmb, 9999)">
      </li>

      <div style="clear:both">&nbsp;</div>

      <?php
      function get_correct_amount($val, $req, $field)
      {
        if ($val > 0) {
          return number_format($val, 2, ',', '.');
        } else {
          if (isset($req[$field])) {
            return $req[$field];
          } else {
            return 0;
          }
        }
      }
      $valorEmb = isset($valorEmb) ? $valorEmb : 0;
      $proex = isset($proex) ? $proex : 0;
      $ace = isset($ace) ? $ace : 0;

      $valorEmb = get_correct_amount($valorEmb, $_REQUEST, 'valorEmb');
      $proex = get_correct_amount($proex, $_REQUEST, 'proex');
      $ace = get_correct_amount($ace, $_REQUEST, 'ace');




      ?>
      <li class="campo3colunas">
        <label>Valor Embarcado (<?php echo $extMoeda; ?>)</label>
        <input type="text" size="15" name="valorEmb" tabindex="13" value="<?php echo htmlspecialchars($valorEmb, ENT_QUOTES, 'UTF-8'); ?>" onBlur="if(this.value != '') { checkDecimals(this, this.value); }">

      </li>

      <li class="campo3colunas">
        <label>PROEX (<?php echo $extMoeda; ?>)</label>
        <input type="text" name="proex" tabindex="14" value="<?php echo htmlspecialchars($proex, ENT_QUOTES, 'UTF-8'); ?>" onBlur="if(this.value != '') { checkDecimals(this, this.value); }">

      </li>

      <li class="campo3colunas">
        <label>ACE (<?php echo $extMoeda; ?>)</label>
        <input type="text" name="ace" value="<?php echo htmlspecialchars($ace, ENT_QUOTES, 'UTF-8'); ?>" tabindex="15" onBlur="if(this.value != '') { checkDecimals(this, this.value); }">

      </li>

      <li class="campo3colunas">
        <label>Total</label>
        <label
          id="totalEmb"><?php echo htmlspecialchars(isset($_REQUEST['totalEmb']) ? $_REQUEST['totalEmb'] : number_format($totalEmbarcado, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></label>
        <input type="hidden" name="totalEmb" onFocus="blur()"
          value="<?php echo htmlspecialchars(isset($_REQUEST['totalEmb']) ? $_REQUEST['totalEmb'] : number_format($totalEmbarcado, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>">
      </li>


      <li class="campo3colunas">
        <label>Total</label>
        <label
          id="totalProex"><?php echo htmlspecialchars(isset($_REQUEST['totalProex']) ? $_REQUEST['totalProex'] : number_format($totalProex, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></label>
        <input type="hidden" name="totalProex" onFocus="blur()"
          value="<?php echo htmlspecialchars(isset($_REQUEST['totalProex']) ? $_REQUEST['totalProex'] : number_format($totalProex, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>">
      </li>

      <li class="campo3colunas">
        <label>Total</label>
        <label
          id="totalAce"><?php echo htmlspecialchars(isset($_REQUEST['totalAce']) ? $_REQUEST['totalAce'] : number_format($totalAce, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></label>
        <input type="hidden" name="totalAce" onFocus="blur()"
          value="<?php echo htmlspecialchars(isset($_REQUEST['totalAce']) ? $_REQUEST['totalAce'] : number_format($totalAce, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>">
      </li>


      <div class="barrabotoes">
        <button class="botaovgm" type="button" tabindex="16"
          onClick="<?php echo check_menu(['dve'], $role) ? '' : 'this.form.client.value=1;'; ?>this.form.viewflag.value=0;this.form.primeira_tela.value=1;this.form.submit()">
          <?php
          if ($idDetail)
            $dis = "";
          else
            $dis = "disabled";
          ?> Voltar</button>

        <?php if ($stateDVE == 1) { ?>
          <button class="botaoagm" type="button" tabindex=17 onClick="manda(this.form, 'include')">
            <?php echo $idDetail ? 'Alterar' : 'Incluir'; ?>
          </button>

          <button class="botaoagm" type="button" tabindex=18
            onClick="<?php echo $idDetail ? "manda(this.form, 'exclude')" : 'limpa(this.form)'; ?>">
            <?php echo $idDetail ? 'Excluir' : 'Limpar'; ?>
          </button>
        <?php } ?>
        <!--<button class="botaoagm" type="button" tabindex=19 onClick="if(confirm('Atenção. Ao concluir o período não será mais possível incluir novos faturamentos.\nTem certeza que deseja concluí-lo?')){ this.form.submit();}">Concluir</button>-->
      </div>

    <?php } else { /* show */ ?>
      <button class="botaovgm" type="button" tabindex=20
        onClick="<?php echo check_menu(['dve'], $role) ? '' : 'this.form.client.value=1;'; ?>this.form.viewflag.value=0;this.form.primeira_tela.value=1;this.form.submit()">Voltar</button>
    <?php } ?>

    <?php if ($msg) { ?>
      <label style="color:#F00"><?php echo ($msg); ?></label>
    <?php } ?>

    <input type="hidden" name="show" value="<?php echo $show; ?>">
  </form>

  <script language=javascript>
    var f = document.formulario;

  <?php // Alterado Hicom - 09/10/04 (Gustavo) - erros de Javascript, os objetos abaixo
  // só existe if ($show)
  if ($show) { ?>

        muda_ci(f.idBuyer);
        f.cicoface.disabled = true;
        f.totalEmb.disabled = true;
        f.totalProex.disabled = true;
        f.totalAce.disabled = true;
        formatDecimals(f.totalEmb, f.totalEmb.value);
        formatDecimals(f.totalProex, f.totalProex.value);
        formatDecimals(f.totalAce, f.totalAce.value);

      <?php if ($valorEmb > 0) { ?>
            formatDecimals(f.valorEmb, f.valorEmb.value);
      <?php }

      if ($proex > 0) { ?>
            formatDecimals(f.proex, f.proex.value);
      <?php }

      if ($ace > 0) { ?>
            formatDecimals(f.ace, f.ace.value);
      <?php }
  }
  // fim alterado Hicom
  
  ?>

      function showimporters(s) {
        var link = '<?php echo $root; ?>role/dve/Dve.php?comm=modalidade&idInform=<?php echo $idInform; ?>&modalidade=<?php echo $modalidade; ?>&idDVE=<?php echo $idDVE; ?>&client=<?php echo $client; ?>&idCountry=' + s.options[s.selectedIndex].value + '&fieldfocus=fatura&formfocus=formulario&show=1&viewflag=<?php echo $viewflag; ?>#tabela';
        //verErro(link);
        top.location = link;
      }

    function muda_ci(s) {
      if (s.selectedIndex > 0) {
        document.formulario.cicoface.value = ci[s.selectedIndex];
        document.getElementById("cicofacelbl").innerHTML = ci[s.selectedIndex];
      }
      document.formulario.fatura.focus();
    }

    function manda(f, c) {
      if (c == 'exclude') {
        f.comm.value = c;
        f.submit();
        return;
      }
      if (confirma(f)) {
        f.comm.value = c;
        f.submit();
      }
    }

    function confirma(f) {
      //alert('oi'); 
      if (f.fatura.value == '') {
        verErro("Favor preencher o n&uacute;mero da fatura");
        f.fatura.focus();
        return false;
      }
      if (f.dataEmbDia.value == '') {
        verErro("Favor preencher a data de embarque");
        f.dataEmbDia.focus();
        return false;
      }
      if (f.dataEmbMes.value == '') {
        verErro("Favor preencher a data de embarque");
        f.dataEmbMes.focus();
        return false;
      }
      if (f.dataEmbAno.value == '') {
        verErro("Favor preencher a data de embarque");
        f.dataEmbAno.focus();
        return false;
      }
      if (f.dataVencDia.value == '' || f.dataVencMes.value == '' || f.dataVencAno.value == '') {
        verErro("Favor preencher a data de vencimento");
        f.dataVencDia.focus();
        return false;
      }
      if (f.dataVencDia.value == '' || f.dataVencDia.value.length < 2) {
        verErro("Favor preencher o dia de vencimento com dois digitos");
        f.dataVencDia.focus();
        return false;
      }
      if (f.dataVencMes.value == '' || f.dataVencMes.value.length < 2) {
        verErro("Favor preencher o mes de vencimento com dois digitos ");
        f.dataVencMes.focus();
        return false;
      }
      if (f.dataVencAno.value == '' || f.dataVencAno.value.length < 4) {
        verErro("Favor preencher o ano de vencimento com quatro digitos ");
        f.dataVencAno.focus();
        return false;
      }

      data = new Date();
      dia = data.getDate();
      mes = data.getMonth();
      ano = data.getFullYear();
      meses = new Array(12);

      meses[0] = "01";
      meses[1] = "02";
      meses[2] = "03";
      meses[3] = "04";
      meses[4] = "05";
      meses[5] = "06";
      meses[6] = "07";
      meses[7] = "08";
      meses[8] = "09";
      meses[9] = "10";
      meses[10] = "11";
      meses[11] = "12";

      var data1 = (parseInt(dia).toString() < 10 ? ("0" + dia) : dia) + "/" + meses[mes] + "/" + ano;
      var data2 = f.dataVencDia.value + "/" + f.dataVencMes.value + "/" + f.dataVencAno.value;
      var verif1 = (parseInt(data1.split("/")[2].toString() + data1.split("/")[1].toString() + data1.split("/")[0].toString()));
      var verif2 = (parseInt(data2.split("/")[2].toString() + data2.split("/")[1].toString() + data2.split("/")[0].toString()));

      if (f.Valida.value == 1) {
        verErro("Voc&ecirc; n&atilde;o pode declarar DVE com atraso. Favor entrar em contato com o atendimento.");
        return false;
      }

      //verErro(data1 + "?" + data2 + "?" + f.LiberaVencida.value + "?" + f.Valida.value + "?" + verif1 + "?" + verif2);

      if ((f.Valida.value == 1) && (f.LiberaVencida.value == 0) && (verif2 < verif1)) {
        verErro("Voc&ecirc; n&atilde;o pode declarar uma DVE j&aacute; vencida. Favor entrar em contato com o atendimento.");
        return false;
      }

      if (f.valorEmb.value == '') {
        verErro("Favor preencher o valor embarcado");
        f.valorEmb.value = '';
        f.valorEmb.focus();
        return false;
      }
      if (f.valorEmb.value == '0,00' || numVal(f.valorEmb.value) == 0) {
        verErro("O valor embarcado n&atilde;o pode ser zero");
        f.valorEmb.value = '';
        f.valorEmb.focus();
        return false;
      }


      if (f.proex.value == '') {
        f.proex.value = '0,00';
      }
      if (f.ace.value == '') {
        f.ace.value = '0,00';
      }

      f.dataEmb.value = f.dataEmbDia.value + '/' + f.dataEmbMes.value + '/' + f.dataEmbAno.value;
      f.dataVenc.value = f.dataVencDia.value + '/' + f.dataVencMes.value + '/' + f.dataVencAno.value;



      return true;
    }

    function proximo(atual, size, prox, max) {
      if (atual.value.length == size) {
        if (checknumber(atual, max))
          prox.focus();
      }
    }

    function checknumber(f, n) {
      if (f.value > 0) {
        if (f.value > n) {
          verErro("Valor inv&aacute;lido: " + f.value);
          f.value = '';
          f.focus();
          return false;
        }
      } else {
        verErro("Valor inv&aacute;lido: " + f.value);
        f.value = '';
        f.focus();
        return false;
      }
      return true;
    }

    if (document.getElementById("emb_dec").value) {
      formulario.idBuyer.value = '0';
      formulario.fatura.value = '';
      formulario.dataEmbDia.value = '';
      formulario.dataEmbMes.value = '';
      formulario.dataEmbAno.value = '';
      formulario.dataVencDia.value = '';
      formulario.dataVencMes.value = '';
      formulario.dataVencAno.value = '';
      formulario.valorEmb.value = '';
      formulario.proex.value = '';
      formulario.ace.value = '';
    }

    function limpa(f) {
      f.fatura.value = '';
      f.dataEmbDia.value = '';
      f.dataEmbMes.value = '';
      f.dataEmbAno.value = '';
      f.dataVencDia.value = '';
      f.dataVencMes.value = '';
      f.dataVencAno.value = '';
      f.valorEmb.value = '';
      f.proex.value = '';
      f.ace.value = '';
    }
  </script>
</div>