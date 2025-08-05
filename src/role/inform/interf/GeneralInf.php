<?php

if (!isset($_SESSION)) {
  session_start();
}


function busca_cep($cep)
{
  // Usar cURL com configurações seguras de SSL
  $ch = curl_init();
  $url = 'https://republicavirtual.com.br/web_cep.php?cep=' . urlencode($cep) . '&formato=query_string';
  
  curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_USERAGENT => 'SIGA-Coface/1.0 (Sistema de Informações)',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 3,
    CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
    CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS
  ]);

  $resultado = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $error = curl_error($ch);
  curl_close($ch);

  // Log de erro para monitoramento
  if ($error || $http_code !== 200) {
    error_log("Erro na consulta CEP: HTTP $http_code - $error");
    
    // Fallback para HTTP se HTTPS falhar (apenas para este domínio específico)
    if (strpos($error, 'SSL') !== false) {
      $ch = curl_init();
      curl_setopt_array($ch, [
        CURLOPT_URL => str_replace('https://', 'http://', $url),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_USERAGENT => 'SIGA-Coface/1.0 (Sistema de Informações)',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
      ]);
      
      $resultado = curl_exec($ch);
      $fallback_error = curl_error($ch);
      curl_close($ch);
      
      if ($fallback_error) {
        error_log("Erro na consulta CEP (fallback HTTP): $fallback_error");
      }
    }
  }

  if (!$resultado) {
    $resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";
  }

  parse_str($resultado, $retorno);
  return $retorno;
}


?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js"></script>
<script language="JavaScript" src="<?php echo $root; ?>scripts/cnpj.js"></script>
<script type="text/javascript" src="<?php echo $root; ?>../scripts/jquery.js"></script>
<script type="text/javascript" src="<?php echo $root; ?>../scripts/jquery.maskedinput.js" />
</script>
<script Language="JavaScript" type="text/javascript">

  jQuery(function ($) {
    $("#cep").mask("99999-999");
    $("#cnpj").mask("99.999.999/9999-99");
  });

  var request = false;
  var dest;

  //Verifica se está usando internet explorer
  try {
    request = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      request = false;
    }
  }

  if (!request && typeof XMLHttpRequest != 'undefined') {
    request = new XMLHttpRequest();
  }

  function verificaPeriodo() {
    if (numVal(document.getElementById('Periodo_Vigencia').value) > 24) {
      verErro('Aten&ccedil;&atilde;o! O per&iacute;odo de vig&ecirc;ncia deve ter at&eacute; 24 meses.');
      // 2018/09/25 - AIP: Validity over 24 months will not be criticized - SR221556
      //    document.getElementById('Periodo_Vigencia').value = '';
      //    document.getElementById('Periodo_Vigencia').focus();
      return false;
    }
  }

  function FormataCEP(Campo, teclapres) {
    var tecla = teclapres.keyCode;
    var vr = new String(Campo.value);
    vr = vr.replace("-", "");
    tam = vr.length + 1;

    if (tecla != 8) {
      if (tam == 6)
        Campo.value = vr.substr(0, 5) + '-' + vr.substr(5, 5);
    }
  }

  function FormataCnpj(campo, teclapres) {
    var tecla = teclapres.keyCode;
    var vr = new String(campo.value);
    vr = vr.replace(".", "");
    vr = vr.replace("/", "");
    vr = vr.replace("-", "");
    tam = vr.length + 1;

    if (tecla != 14) {
      if (tam == 3)
        campo.value = vr.substr(0, 2) + '.';
      if (tam == 6)
        campo.value = vr.substr(0, 2) + '.' + vr.substr(2, 5) + '.';
      if (tam == 10)
        campo.value = vr.substr(0, 2) + '.' + vr.substr(2, 3) + '.' + vr.substr(6, 3) + '/';
      if (tam == 15)
        campo.value = vr.substr(0, 2) + '.' + vr.substr(2, 3) + '.' + vr.substr(6, 3) + '/' + vr.substr(9, 4) + '-' + vr.substr(13, 2);
    }
  }

  function ValidaCNPJ(obj, cnpj) {
    var i = 0;
    var l = 0;
    var strNum = "";
    var strMul = "6543298765432";
    var character = "";
    var iValido = 1;
    var iSoma = 0;
    var strNum_base = "";
    var iLenNum_base = 0;
    var iLenMul = 0;
    var iSoma = 0;
    var strNum_base = 0;
    var iLenNum_base = 0;

    if (cnpj == "00.000.000/000-00") {
      verErro('CNPJ inv&aacute;lido');
      //obj.focus();
      obj.value = '';
      return false;
    } else if (cnpj.length > 0) {
      l = cnpj.length;

      for (i = 0; i < l; i++) {
        caracter = cnpj.substring(i, i + 1)

        if ((caracter >= '0') && (caracter <= '9'))
          strNum = strNum + caracter;
      };

      if (strNum.length != 14) {
        verErro("CNPJ deve conter todos os caracteres.");
        //obj.focus();
        obj.value = '';
        return false
      }

      strNum_base = strNum.substring(0, 12);
      iLenNum_base = strNum_base.length - 1;
      iLenMul = strMul.length - 1;
      for (i = 0; i < 12; i++)
        iSoma = iSoma +
          parseInt(strNum_base.substring((iLenNum_base - i), (iLenNum_base - i) + 1), 10) *
          parseInt(strMul.substring((iLenMul - i), (iLenMul - i) + 1), 10);

      iSoma = 11 - (iSoma - Math.floor(iSoma / 11) * 11);

      if (iSoma == 11 || iSoma == 10)
        iSoma = 0;

      strNum_base = strNum_base + iSoma;
      iSoma = 0;
      iLenNum_base = strNum_base.length - 1
      for (i = 0; i < 13; i++)
        iSoma = iSoma +
          parseInt(strNum_base.substring((iLenNum_base - i), (iLenNum_base - i) + 1), 10) *
          parseInt(strMul.substring((iLenMul - i), (iLenMul - i) + 1), 10)

      iSoma = 11 - (iSoma - Math.floor(iSoma / 11) * 11);
      if (iSoma == 11 || iSoma == 10)
        iSoma = 0;
      strNum_base = strNum_base + iSoma;
      if (strNum != strNum_base) {
        verErro("CNPJ inv&aacute;lido.");
        //obj.focus();
        obj.value = '';
        return false;
      }
    }
    return (true);

  }




  function isNumeric(sText) {
    // caso queira utilizar a virgula como separador decimal coloque nesta variável
    var ValidChars = "0123456789.-/";
    var IsNumber = true;
    var Char;

    for (i = 0; i < sText.length && IsNumber == true; i++) {
      Char = sText.charAt(i);
      if (ValidChars.indexOf(Char) == -1) {
        IsNumber = false;
      }
    }
    return IsNumber;
  }





  function novoValidaEmail(mail) {
    var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
    if (typeof (mail) == "string") {
      if (er.test(mail)) {
        return true;
      }
    }
    else if (typeof (mail) == "object") {
      if (er.test(mail.value)) {
        return true;
      }
    }
    else if ((indexOf('.', mail) == 0) || (indexOf('@', mail) > 1))
      return false
    else {
      return false;
    }
  }


  function checa_nome(form) {
    if (cadastro.name.value == "") {
      verErro("Por Favor, Preencha o Nome");
      //cadastro.name.focus();
      return (false);
    }
  }
  function checa_end(form) {
    if (form.address.value == "") {
      verErro("Por Favor, Preencha o Endere&ccedil;o");
      //cadastro.address.focus();
      return (false);
    }
  }

  function checa_Produto(form) {
    if (form.i_Produto.value == "") {
      verErro("Por Favor, Selecione o Produto!");
      form.i_Produto.focus();
      return (false);
    }
  }

  function checa_n(form) {
    if (form.addressNumber.value == "") {
      verErro("Por Favor, Preencha o N&uacute;mero do endere&ccedil;o");
      //cadastro.addressNumber.focus();
      return (false);
    }
  }
  function checa_city(form) {
    if (form.city.value == "") {
      verErro("Por Favor, Preencha a Cidade");
      //cadastro.city.focus();
      return (false);
    }
  }
  function checa_cep(form) {
    if (form.cep.value == "") {
      verErro("Por Favor, Preencha o CEP");
      //cadastro.cep.focus();
      return (false);
    }
  }
  function checa_tel(form) {
    if (form.tel.value == "") {
      verErro("Por Favor, Preencha o Telefone");
      //cadastro.tel.focus();
      return (false);
    }
  }
  function checa_email_emp(form) {
    valmail = form.email.value;

    if (form.email.value == "") {
      verErro("Por Favor, Preencha o e-mail da Empresa");
      //cadastro.email.focus();
      return (false);
    }
    else {
      if (!novoValidaEmail(valmail)) {
        cadastro.email.focus();
        verErro("O e-mail da Empresa &eacute; Inv&aacute;lido!");
        return false;
      }
      else {
        cadastro.email.value = valmail.toLowerCase();
      }
    }
  }

  function checa_contact(form) {
    if (form.contact.value == "") {
      verErro("Por Favor, Preencha o Contato");
      //cadastro.contact.focus();
      return (false);
    }
  }
  function checa_email_contact(form) {
    valmail = form.emailContact.value;

    if (form.emailContact.value == "") {
      verErro("Por Favor, Preencha o e-mail do Contato");
      //cadastro.emailContact.focus();
      return (false);
    }
    else {
      if (!novoValidaEmail(valmail)) {
        cadastro.emailContact.focus();
        verErro("O e-mail do Contato é Inv&aacute;lido!");
        return false;
      }
      else {
        cadastro.emailContact.value = valmail.toLowerCase();
      }
    }
  }
  function checa_cnpj(form) {
    if (form.cnpj.value == "") {
      verErro("Por Favor, Preencha o CNPJ");
      //cadastro.cnpj.focus();
      return (false);
    }
  }
  function checa_ie(form) {
    if (form.ie.value == "") {
      verErro("Por Favor, Preencha a Inscri&ccedil;&aacute;o Estadual ou Municipal");
      //cadastro.ie.focus();
      return (false);
    }
  }
  function checa_chargeAddress(form) {

    if (cadastro.sameAddress[0].checked == 0 && cadastro.chargeAddress.value == "") {
      verErro("Por Favor, Informe o Endere&ccedil;o para Cobran&ccedil;a");
      //cadastro.chargeAddress.focus();
      return (false);
    }
  }
  function checa_chargeCity(form) {
    if (cadastro.sameAddress[0].checked == 0 && cadastro.chargeCity.value == "") {
      verErro("Por Favor, Informe a Cidade para Cobran&ccedil;a");
      //cadastro.chargeCity.focus();
      return (false);
    }
  }
  function checa_chargeCep(form) {
    if (cadastro.sameAddress[0].checked == 0 && cadastro.chargeCep.value == "") {
      verErro("Por Favor, Informe o CEP para Cobran&ccedil;a");
      //cadastro.chargeCep.focus();
      return (false);
    }
  }





  function checa_formulario(cadastro) {


    <?php //Inicio verificação N
    if ((check_menu(['executive'], $role) || check_menu(['executiveLow'], $role)) && $_SESSION['pefil'] != 'C' && $_SESSION['pefil'] != 'B') {
      ?>

      if (cadastro.naf.value == "") {
        verErro("Por Favor, Preencha o campo NAF");
        cadastro.naf.focus();
        return (false);

      } if (cadastro.siren.value == "") {
        verErro("Por Favor, Preencha o campo SIREN");
        cadastro.siren.focus();
        return (false);
      } if (cadastro.quest.value == "") {
        verErro("Por Favor, Preencha o campo QUESTIONNAIRE");
        cadastro.quest.focus();
        return (false);
      } if (cadastro.napce.value == "") {
        verErro("Por Favor, Preencha o campo NAPCE");
        cadastro.napce.focus();
        return (false);
      } if (cadastro.dossier.value == "") {
        verErro("Por Favor, Preencha o campo DOSSIER");
        cadastro.dossier.focus();
        return (false);
      } if (cadastro.contrat.value == "") {
        verErro("Por Favor, Preencha o campo CONTRAT");
        cadastro.contrat.focus();
        return (false);
      } if (cadastro.executive.value == "") {
        verErro("Por Favor, Preencha o campo EXECUTIVE");
        cadastro.executive.focus();
        return (false);
      }


      <?php
    }
    ?>



    // Fim



    if (cadastro.name.value == "") {
      verErro("Por Favor, Preencha o Nome");
      cadastro.name.focus();
      return (false);
    }
    if (cadastro.i_Produto.value == "") {
      verErro("Por Favor, Selecione o Produto!");
      cadastro.i_Produto.focus();
      return (false);
    }
    if (cadastro.tipomoeda.value == "") {
      verErro("Por Favor, Selecione o Tipo de moeda!");
      cadastro.tipomoeda.focus();
      return (false);
    }

    if (cadastro.address.value == "") {
      verErro("Por Favor, Preencha o Endere&ccedil;o");
      cadastro.address.focus();
      return (false);
    }

    if (cadastro.bairro.value == "") {
      verErro("Preencha o bairro");
      cadastro.bairro.focus();
      return (false);
    }

    if (cadastro.city.value == "") {
      verErro("Por Favor, Preencha a Cidade");
      cadastro.city.focus();
      return (false);
    }
    if (cadastro.cep.value == "") {
      verErro("Por Favor, Preencha o CEP");
      cadastro.cep.focus();
      return (false);
    }

    var cep = cadastro.cep.value
    if (cep.length < 8) {
      verErro("Por Favor, Preencha o CEP com 8 d&iacute;gitos");
      cadastro.cep.focus();
      return (false);
    }

    if (cadastro.Uf.value == 0) {
      verErro("Preencha a UF");
      cadastro.Uf.focus();
      return (false);
    }

    if (cadastro.idRegion.value == 0) {
      verErro("Por Favor, Escolha uma Regi&atilde;o");
      cadastro.idRegion.focus();
      return (false);
    }
    if (cadastro.tel.value == "") {
      verErro("Por Favor, Preencha o Telefone");
      cadastro.tel.focus();
      return (false);
    }
    if (cadastro.email.value == "") {
      verErro("Por Favor, Preencha o E-mail da Empresa");
      cadastro.email.focus();
      return (false);
    }
    if (cadastro.email.value.indexOf('@', 0) == -1) {
      verErro("O E-mail da Empresa &eacute; Inv&aacute;lido !!!");
      cadastro.email.focus();
      return (false);
    }
    if (cadastro.contact.value == "") {
      verErro("Por Favor, Preencha o Contato");
      cadastro.contact.focus();
      return (false);
    }
    if (cadastro.emailContact.value == "") {
      verErro("Por Favor, Preencha o E-mail do Contato");
      cadastro.emailContact.focus();
      return (false);
    }
    if (cadastro.emailContact.value.indexOf('@', 0) == -1) {
      verErro("O E-mail do Contato &eacute; Inv&aacute;lido !!!");
      cadastro.emailContact.focus();
      return (false);
    }


    if (cadastro.cnpj.value == "") {
      verErro("Por Favor, Preencha o CNPJ");
      cadastro.cnpj.focus();
      return (false);
    }

    if (!ValidaCNPJ(cadastro.cnpj, cadastro.cnpj.value)) {
      verErro("Por Favor, Preencha corretamente o CNPJ");
      cadastro.cnpj.focus();
      return (false);
    }

    if (cadastro.ie.value == "") {
      verErro("Por Favor, Preencha a Inscri&ccedil;&atilde;o Estadual ou Municipal");
      cadastro.ie.focus();
      return (false);
    }
    if (cadastro.sameAddress[0].checked == 0 && cadastro.chargeAddress.value == "") {
      verErro("Por Favor, Informe o Endere&ccedil;o para Cobran&ccedil;a");
      cadastro.chargeAddress.focus();
      return (false);
    }
    if (cadastro.sameAddress[0].checked == 0 && cadastro.chargeCity.value == "") {
      verErro("Por Favor, Informe a Cidade para Cobran&ccedil;a");
      cadastro.chargeCity.focus();
      return (false);
    }
    if (cadastro.sameAddress[0].checked == 0 && cadastro.chargeUf.value == 0) {
      verErro("Por Favor, Selecione um Estado para Cobran&ccedil;a");
      cadastro.chargeUf.focus();
      return (false);
    }
    if (cadastro.sameAddress[0].checked == 0 && cadastro.chargeCep.value == "") {
      verErro("Por Favor, Informe o CEP para Cobran&ccedil;a");
      cadastro.chargeCep.focus();
      return (false);
    }

    // Interaktiv
    if ($.trim($("#id_sel_classe_cnae").val()) == "") {
      $('html, body').animate({
        scrollTop: $("#abre_modal_cnae").offset().top - 300
      }, 300);
      verErro("Por favor, selecione a classe do CNAE");
      return (false);
    }
    if ($("#Num_Parcelas").length > 0) {
      if ($.trim($("#Num_Parcelas").val()) == "") {
        $('html, body').animate({
          scrollTop: $("#Num_Parcelas").offset().top - 300
        }, 300);
        verErro("Por favor, preencha o número de parcelas.");
        return (false);
      }
    }


    if (cadastro.sameAddress[0].checked == 0 && (
      cadastro.chargeAddress.value.length +
      cadastro.chargeCity.value.length +
      cadastro.chargeAddressComp.value.length +
      cadastro.chargeAddressNumber.value.length) > 60) {

      verErro("Por Favor, A soma dos campos Endere&ccedil;o de Cobran&ccedil;a + Numero + Complemento + Cidade" +
        "n&atilde;o pode ser maior que 60 caracteres.");
      return (false);
    }

    if (cadastro.idRegion.value == "") {
      verErro("Por Favor, Preencha a Regi&atilde;o");
      cadastro.idRegion.focus();
      return (false);
    }
    if (cadastro.idSector.value == 0) {
      verErro("Por Favor, Preencha o Setor");
      cadastro.idSector.focus();
      return (false);
    }

    if (cadastro.products.value == "") {
      verErro("Por Favor, Preencha o(s) Produto(s) Exportado(s)");
      cadastro.products.focus();
      return (false);
    }
    if (cadastro.frameMed.value == 0) {
      verErro("Por Favor, Preencha o Prazo M&eacute;dio Usualmente Concedido Para Pagamento");
      cadastro.frameMed.focus();
      return (false);
    }
    if (cadastro.hasGroup[0].checked == 1 && cadastro.companyGroup.value == "") {
      verErro("Por Favor, Informe o Nome do Grupo");
      cadastro.companyGroup.focus();
      return (false);
    }
    if (cadastro.hasAssocCompanies[0].checked == 1 && cadastro.associatedCompanies.value == "") {
      verErro("Por Favor, Informe as Companhias Associadas No Exterior");
      cadastro.associatedCompanies.focus();
      return (false);
    }
    if (cadastro.Periodo_Vigencia.value == '') {
      verErro("Por Favor, informe o Per&iacute;odo de vig&eacute;ncia");
      return (false);
    }

    if (numVal(cadastro.Periodo_Vigencia.value) > 24) {
      verErro('Aten&ccedil;&atilde;o! O per&iacute;odo de vig&ecirc;ncia deve ter at&eacute; 24 meses.');
      return false;
    }

    if (cadastro.i_Gerente.value == "" && document.getElementById('perfil').value != 'C') {
      verErro('Por favor informe o Gerente Comercial');
      cadastro.i_Gerente.focus();
      return false;

    }
    /*
    var s = cadastro.idRegion;
    var r = s.options[s.selectedIndex].text; 
    if(! confirm("Confirma região (" + r + ")?")){
      return false;
    }
    */
    return (true);
  }
  //-->



  function dados(tipo) {
    var ajax = false;


    if (window.XMLHttpRequest) {
      ajax = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
      ajax = new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (tipo == 1) { //Endereço principal
      var cep = document.getElementById("cep").value;
      var url = 'https://republicavirtual.com.br/web_cep.php?cep=' + cep + '&formato=xml';
    } else { //Endereço de cobrança
      var cep = document.getElementById("chargeCep").value;
      var url = 'https://republicavirtual.com.br/web_cep.php?cep=' + cep + '&formato=xml';
    }

    if (cep == "") {
      verErro('Campo cep n&atilde;o pode ser vazio.');
    } else {
      if (ajax) {
        ajax.open("GET", url, true);
        ajax.onreadystatechange = function () {
          if (ajax.readyState == 4) {
            if (ajax.status == 200) {
              obj = ajax.responseXML;
              var dados = obj.getElementsByTagName("webservicecep");
              if (tipo == 1) {// Endereço principal
                //processarXML(ajax.responseXML);

                //Total de elementos contidos na tag webservicecep
                if (dados.length > 0) {
                  //percorre o arquivo XML para extrair os dados
                  for (var i = 0; i < dados.length; i++) {
                    var item = dados[i];
                    //conteudo dos campos no arquivo XML
                    var uf = item.getElementsByTagName("uf")[0].firstChild.nodeValue;
                    var cidade = item.getElementsByTagName("cidade")[0].firstChild.nodeValue;
                    var bairro = item.getElementsByTagName("bairro")[0].firstChild.nodeValue;
                    var tipo_logradouro = item.getElementsByTagName("tipo_logradouro")[0].firstChild.nodeValue;
                    var logradouro = item.getElementsByTagName("logradouro")[0].firstChild.nodeValue;
                    var resultado = item.getElementsByTagName("resultado")[0].firstChild.nodeValue;
                    var resultado_txt = item.getElementsByTagName("resultado_txt")[0].firstChild.nodeValue;
                  }


                  if (resultado == 1) {
                    //document.getElementById("tipo").value = tipo_logradouro;
                    document.getElementById("logradouro").value = tipo_logradouro + " " + logradouro;
                    document.getElementById("cidade").value = cidade;
                  } else {
                    verErro('N&atilde;o foi localizado o cep.');
                  }
                }





              } else { //Endereço cobrança
                //processarXMLCob(ajax.responseXML);
                if (dados.length > 0) {
                  //percorre o arquivo XML para extrair os dados
                  for (var i = 0; i < dados.length; i++) {
                    var item = dados[i];
                    //conteudo dos campos no arquivo XML
                    var uf = item.getElementsByTagName("uf")[0].firstChild.nodeValue;
                    var cidade = item.getElementsByTagName("cidade")[0].firstChild.nodeValue;
                    var bairro = item.getElementsByTagName("bairro")[0].firstChild.nodeValue;
                    var tipo_logradouro = item.getElementsByTagName("tipo_logradouro")[0].firstChild.nodeValue;
                    var logradouro = item.getElementsByTagName("logradouro")[0].firstChild.nodeValue;
                    var resultado = item.getElementsByTagName("resultado")[0].firstChild.nodeValue;
                    var resultado_txt = item.getElementsByTagName("resultado_txt")[0].firstChild.nodeValue;
                  }


                  if (resultado == 1) {
                    //document.getElementById("tipo").value = tipo_logradouro;
                    document.getElementById("chargeUf").value = uf;
                    document.getElementById("end_cob").value = tipo_logradouro + " " + logradouro;
                    document.getElementById("cidade_cob").value = cidade;
                  } else {
                    verErro('N&atilde;o foi localizado o cep.');
                  }
                }


              }
            } else {
              verErro('Houve um problema aoa carregar');
            }
          }
        }
        ajax.send(null);
      }
    }
  }

</script>

<script language="JavaScript" type="text/javascript" charset="ISO-8859-1">

  //////////////////////////////////////////////////////////////////////////////////////
  // 1º COMBO INICIO
  ///////////////////////////////////////////////////////////////////////////////////////

  function BuscaMoeda(vlr) {
    {
      //verErro(vlr.length);
      request.open("GET", "<?php echo $root ?>role/inform/interf/busca_moeda.php?i_Produto=" + vlr, true);
      request.onreadystatechange = handleHttpResponse;
      request.send(null);
    }

    function handleHttpResponse() {
      campo_select = document.forms[0].tipomoeda;
      if (request.readyState == 4) {
        campo_select.options.length = 0;
        results = request.responseText.split(",");

        for (i = 0; i < results.length; i++) {
          string = results[i].split("|");
          if (string[0] == 2) {
            campo_select.options[i] = new Option('Dolar', string[0]);
          } else {
            campo_select.options[i] = new Option(string[1], string[0]);
          }
        }
      }
    }
    ///////////////////////////////////////////////////////////////////////////////////////
    // FIM - 1º COMBO
    ///////////////////////////////////////////////////////////////////////////////////////

  }

</script>

<?php

include_once('../../../navegacao.php');

if ($field->getField("idInform"))
  $idInform = $field->getField("idInform");

$idNotification = $field->getField("idNotification");
$volta = $field->getField("volta");

$rsSql = odbc_exec($db, "SELECT idAnt FROM Inform WHERE id = $idInform");
$blockInput = odbc_result($rsSql, 'idAnt') != NULL ? 'readonly' : '';

$cur = odbc_exec($db, "SELECT i.*, c.Descricao AS desc_cnae, c.Cod_CNAE AS cod_cnae, p.Nome AS produtoNome, m.Nome AS currencyName
                      FROM Inform i 
                        LEFT JOIN CNAE c ON i.i_CNAE = c.i_CNAE 
                        LEFT JOIN Produto p ON i.i_Produto = p.i_Produto 
                        LEFT JOIN Moeda m on i.currency = m.i_Moeda
                      WHERE id = $idInform");

if (odbc_fetch_row($cur) || $_REQUEST['Gerar_Novo_Inform'] == 1) {
  $idAnt = odbc_result($cur, 'idAnt');
  $addressNumber = odbc_result($cur, 'addressNumber');
  $chargeAddressNumber = odbc_result($cur, 'chargeAddressNumber');
  $addressComp = odbc_result($cur, 'addressComp');
  $bairro = odbc_result($cur, 'bairro');
  $chargeAddressComp = odbc_result($cur, 'chargeAddressComp');
  $currency = odbc_result($cur, 'currency');
  $currencyName = odbc_result($cur, 'currencyName');
  $i_Produto = odbc_result($cur, 'i_Produto');
  $produtoNome = odbc_result($cur, 'produtoNome');
  $Uf = odbc_result($cur, 'uf');
  $chargeUf = odbc_result($cur, 'chargeUf');
  $Periodo_Vigencia = odbc_result($cur, 'Periodo_Vigencia');
  $i_Gerente = odbc_result($cur, 'i_Gerente');
  $i_Gerente_Relacionamento = odbc_result($cur, 'i_Gerente_Relacionamento');
  $desc_cnae = odbc_result($cur, 'desc_cnae') . " - " . odbc_result($cur, 'cod_cnae');
  $i_CNAE = odbc_result($cur, 'i_CNAE');

  $tipo_apolice = isset($_REQUEST['tipo_apolice']) ? $_REQUEST['tipo_apolice'] : '';

  $field->setDB($cur);
  ?>

  <div class="conteudopagina">
    <FORM action="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/inform/Inform.php" method="post"
      name="cadastro" onsubmit="" onLoad="Ini()">
      <input type="hidden" name="comm" value="generalSubmit">
      <input type="hidden" name="Gerar_Novo_Inform" id="Gerar_Novo_Inform"
        value="<?php echo htmlspecialchars(isset($_REQUEST['Gerar_Novo_Inform']) ? $_REQUEST['Gerar_Novo_Inform'] : '', ENT_QUOTES, 'UTF-8'); ?>" />
      <input type="hidden" name="idNotification"
        value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="volta" value="<?php echo htmlspecialchars($volta, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="hc_cliente"
        value="<?php echo htmlspecialchars($hc_cliente, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="perfil" id="perfil"
        value="<?php echo htmlspecialchars($_SESSION['pefil'], ENT_QUOTES, 'UTF-8'); ?>">
      />
      <?php //####### ini ####### adicionado por eliel vieira - elumini - em 31/08/2007
        echo "<input type='hidden' name='tipo_apolice' value='" . htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8') . "'>";
        ?>

      <?php if (isset($msggs)) { ?>
        <script type="text/javascript">
          verErro("<?php echo $msggs; ?>"); 
        </script>

        <?php
        $msggs = '';
      }

      ?>
      <?php
      //if (($role["executive"] || $role["executiveLow"])   &&  $hc_cliente != "N" ) {
      if (check_menu(['executive', 'executiveLow'], $role) && $_SESSION['pefil'] != 'C' && $_SESSION['pefil'] != 'B') {
        ?>
        <ul>
          <li class="campo3colunas">
            <label>NAF:</label>

            <INPUT name="naf" size=60 value="<?php echo $field->getDBField("naf", 17); ?>">
          </li>

          <li class="campo3colunas"><label>SIREN N&ordm; E175:</label>

            <INPUT name="siren" size=60 value="<?php echo $field->getDBField("siren", 19); ?>">
          </li>

          <li class="campo3colunas"><label>QUESTIONNAIRE:</label>

            <INPUT name="quest" size=60 value="<?php echo $field->getDBField("quest", 21); ?>">
          </li>

          <li class="campo3colunas"><label>NAPCE:</label>

            <INPUT name="napce" size=60 value="<?php echo $field->getDBField("napce", 18); ?>">
          </li>

          <li class="campo3colunas"><label>DOSSIER:</label>

            <INPUT name="dossier" size=60 value="<?php echo $field->getDBField("dossier", 20); ?>">
          </li>

          <li class="campo3colunas"><label>CONTRAT:</label>
            <INPUT name="contrat" value="<?php echo $field->getDBField("contrat", 22); ?>" <?php if ($volta) {
                echo "onFocus=\"blur()\"";
              } ?>    <?php echo $blockInput; ?>>
          </li>

          <li class="campo3colunas"><label>Executivo:</label>
            <INPUT name="executive" size=60 value="<?php echo $field->getDBField("executive", 23); ?>">
          </li>
        </ul>
      <?php } ?>
      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo2colunas">
          <label>
            <h3>Proposta Para Risco Comercial</h3>
          </label>
        </li>
      </ul>
      <div style="clear:both">&nbsp;</div>
      <?php if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') { ?>
        <input type="hidden" name="pvigencia" id="pvigencia" value="0"> <!-- Não utilizar mais-->
        <input type="hidden" name="Periodo_Vigencia" id="Periodo_Vigencia" value="0">
      <?php } else { ?>
        <ul>
          <li class="campo3colunas">
            <label>Per&iacute;odo de Vig&ecirc;ncia Pretendido</label>
            <input type="hidden" name="pvigencia" id="pvigencia" value="0"> <!-- Não utilizar mais-->
            <input type="text" name="Periodo_Vigencia" id="Periodo_Vigencia"
              value="<?php echo $Periodo_Vigencia; ?>" style="text-align:right; width:240px;"
              onKeyPress="return numeros();" onBlur="verificaPeriodo()"><strong>&nbsp;Meses</strong>
          </li>
        </ul>
      <?php } ?>
      <ul>

        <li class="campo3colunas">
          <label>Produto</label>
          <?php
          if ($blockInput) { ?>
            <input type="hidden" name="i_Produto" id="i_Produto" value="<?php echo $i_Produto; ?>">
            <input type="text" value="<?php echo $produtoNome; ?>" <?php echo $blockInput; ?>>
            <?php
          } else {
            $qry = "Select * From Produto Where Situacao = 1";
            $cur = odbc_exec($db, $qry); ?>
            <script type="text/javascript">
              function verJm() {
                if (document.getElementById('i_Produto').value == 2) {
                  document.getElementById('verJm').style.display = 'block';
                } else {
                  document.getElementById('verJm').style.display = 'none';
                }
              }
            </script>

            <select size="1" name="i_Produto" id="i_Produto" onChange="BuscaMoeda(this.value); verJm();">
              <option value="">Selecione...</option>
              <?php while (odbc_fetch_row($cur)) { ?>
                <option value="<?php echo odbc_result($cur, 'i_Produto'); ?>" <?php echo (odbc_result($cur, 'i_Produto') == $i_Produto ? 'selected' : ''); ?>>
                  <?php echo odbc_result($cur, 'Nome'); ?>
                </option>
              <?php } ?>
            </select>
          <?php } ?>
        </li>

        <li class="campo3colunas"><label>Tipo de Moeda</label>
          <?php
          if ($blockInput) { ?>
            <input type="hidden" name="tipomoeda" id="tipomoeda" value="<?php echo $currency; ?>">
            <input type="text" value="<?php echo $currencyName; ?>" <?php echo $blockInput; ?>>
            <?php
          } else {
            if ($i_Produto) {
              $qry = "select * From Moeda MO
                    Inner Join Produto_Moeda PM On PM.i_Moeda = MO.i_Moeda 
                    Where MO.Situacao = 1 AND PM.i_Produto = " . $i_Produto . "";
            } else {
              $qry = "select * From Moeda MO Where MO.Situacao = 1 ";
            }
            $cur = odbc_exec($db, $qry);

            if ($currency == "2") {
              $dollar = "selected";
            } else if ($currency == "6") {
              $euro = "selected";
            } else {
              $vazio = "";
            } ?>
            <select size="1" name="tipomoeda" id="tipomoeda" onclick="">
              <option value="">Selecione uma Op&ccedil;&atilde;o</option>
              <?php while (odbc_fetch_row($cur)) { ?>
                <option value="<?php echo odbc_result($cur, 'i_Moeda') ?>" <?php echo (odbc_result($cur, 'i_Moeda') == $currency ? 'selected' : ''); ?>>
                  <?php echo odbc_result($cur, 'Nome'); ?>
                </option>
              <?php } ?>
            </select>
          <?php } ?>
        </li>

        <br clear="all" /><br />
        <li class="campo3colunas"><label>Cobertura Para Juros De Mora :</label>
          <div class="formopcao">
            <input type="radio" name="warantyInterest" <?php echo $field->getDBField("warantyInterest", 2) ? "checked " : ""; ?>value="1">
          </div>
          <div class="formdescricao">SIM</div>
          <div class="formopcao">
            <input type="radio" name="warantyInterest" <?php echo $field->getDBField("warantyInterest", 2) ? "" : "checked "; ?>value="0">
          </div>
          <div class="formdescricao">N&Atilde;O</div>
        </li>
      </ul>
      <div id="verJm" style="display:<?php echo ($i_Produto == 2 ? 'block' : 'none'); ?>">
        <ul>

        </ul>
      </div>
      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo3colunas"><label>
            <h3>Dados da Empresa (Segurado)</h3>
          </label></li>
      </ul>
      <div class="divisoria01"> &nbsp;</div>
      <?php
      $Nome = '';
      $Cnpj = '';
      $address = '';
      $addressComp = '';
      $addressNumber = '';
      $bairro = '';
      $city = '';
      $cep = '';
      if (isset($_REQUEST['Gerar_Novo_Inform'])) {
        if ($_REQUEST['Gerar_Novo_Inform'] == 1) {

          $sql1 = "SELECT * FROM Inform WHERE id = ?";
          $cur1 = odbc_prepare($db, $sql1);
          odbc_execute($cur1, [$_REQUEST['idInform_old']]);

          $Nome = odbc_result($cur1, 'name');
          $Cnpj = odbc_result($cur1, 'cnpj');
          $address = odbc_result($cur1, 'address');
          $addressComp = odbc_result($cur1, 'addressComp');
          $addressNumber = odbc_result($cur1, 'addressNumber');
          $bairro = odbc_result($cur1, 'bairro');
          $city = odbc_result($cur1, 'city');
          $cep = odbc_result($cur1, 'cep');
        }
      }
      ?>
      <div style="clear:both">&nbsp;</div>

      <ul>
        <li class="campo3colunas">
          <label>Nome:</label>
          <input type="text" maxlength="60" name="name"
            value="<?php echo ($field->getDBField("name", 15) ? $field->getDBField("name", 15) : $Nome); ?>"
            onBlur="">
        </li>

        <?php
        if ($field->getDBField("cnpj", 36) || $Cnpj) {
          $cnpj = formata_string('CNPJ', ($field->getDBField("cnpj", 36) ? $field->getDBField("cnpj", 36) : $Cnpj));
        } else {
          $cnpj = "";
        } ?>

        <li class="campo3colunas">
          <label>CNPJ:</label>
          <input type="text" size="30" name="cnpj" id="cnpj" value="<?php echo $cnpj; ?>" <?php if ($idAnt && $field->getDBField("cnpj", 36) != "") { ?> onFocus="" <?php } ?> maxlength="18"
            onBlur="ValidaCNPJ(this,this.value);">
        </li>

        <li class="campo3colunas">
          <label>Inscri&ccedil;&atilde;o Estadual ou Municipal:</label>
          <input type="text" size="30" maxlength="50" name="ie"
            value="<?php echo $field->getDBField("ie", 37); ?>" onBlur="">
        </li>

        <li class="campo3colunas">
          <label>Endere?:</label>
          <input type="text" maxlength="100" name="address" id="logradouro"
            value="<?php echo ($field->getDBField("address", 26) ? $field->getDBField("address", 26) : $address); ?>"
            onBlur="">
        </li>

        <li class="campo3colunas">
          <label>N&ordm;:</label>
          <input type="text" maxlength="5" name="addressNumber" value="<?php echo $addressNumber; ?>" onBlur="">
        </li>

        <li class="campo3colunas">
          <label>Complemento:</label>
          <input type="text" size="20" maxlength="15" name="addressComp" value="<?php echo $addressComp; ?>">
        </li>

        <li class="campo3colunas">
          <label>Bairro:</label>
          <input type="text" size="20" maxlength="60" name="bairro" id="bairro" value="<?php echo $bairro; ?>">
        </li>

        <li class="campo3colunas">
          <label>Cidade:</label>
          <input type="text" size="30" maxlength="50" name="city" id="cidade"
            value="<?php echo ($field->getDBField("city", 27) ? $field->getDBField("city", 27) : $city); ?>"
            onBlur="">
        </li>

        <li class="campo3colunas">
          <label>CEP:</label>
          <input type="text" name="cep" id="cep"
            value="<?php echo ($field->getDBField("cep", 29) ? $field->getDBField("cep", 29) : $cep); ?>"
            onBlur="" maxlength="9">
        </li>

        <li class="campo3colunas">
          <label>UF:</label>
          <?php // Monta a lista de UF
          
            $sql = "SELECT uf FROM UF where uf<>'NA' ORDER BY uf";
            $res = odbc_exec($db, $sql);
            ?>
          <select name="Uf" id="Uf" <?php echo $field->getDBField("sameAddress", 56) ? "" : ""; ?>>
            <option value="">-- Selecione Uma Op&ccedil;&atilde;o --</option>
            <?php while ($resultado = odbc_fetch_row($res)) { ?>
              <option value="<?php echo odbc_result($res, 'uf'); ?>" <?php echo ($Uf == odbc_result($res, 'uf') ? 'selected' : ''); ?>> <?php echo odbc_result($res, 'uf'); ?></option>
            <?php } ?>
          </select>
        </li>

        <li class="campo3colunas"><label>Regi&atilde;o:</label>
          <?php // Monta a lista de Região
            $sql = "SELECT id, description FROM Region ORDER BY name";
            $sel = $field->getDBField("idRegion", 55);
            $name = "idRegion";
            $empty = "--  --";
            require_once("../../interf/Select.php");
            ?>
        </li>

        <li class="campo3colunas">
          <label>Telefone (com DDD):</label>
          <input type="text" maxlength="25" name="tel" value="<?php echo $field->getDBField("tel", 30); ?>"
            onBlur="">
        </li>

        <li class="campo3colunas">
          <label>Fax:</label>
          <input type="text" maxlength="25" name="fax" value="<?php echo $field->getDBField("fax", 31); ?>">
        </li>

        <li class="campo3colunas">
          <label>E-mail da Empresa:</label>
          <input type="text" maxlength="60" name="email" id="email"
            value="<?php echo $field->getDBField("email", 32); ?>" onBlur="">
        </li>

        <li class="campo3colunas">
          <label>Contato:</label>
          <input type="text" size="30" maxlength="150" name="contact"
            value="<?php echo $field->getDBField("contact", 33); ?>" onBlur="">
        </li>

        <li class="campo3colunas">
          <label>E-mail do Contato:</label>
          <input type="text" maxlength="60" name="emailContact" id="emailContact"
            value="<?php echo $field->getDBField("emailContact", 35); ?>" onBlur="">
        </li>

        <li class="campo3colunas">
          <label>Cargo:</label>
          <input type="text" maxlength="150" name="ocupationContact"
            value="<?php echo $field->getDBField("ocupationContact", 34); ?>">
        </li>

      </ul>

      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo3colunas"><label>
            <h3>Dados para Cobran&ccedil;a</h3>
          </label></li>
      </ul>
      <div class="divisoria01"> &nbsp;</div>
      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo3colunas"><label>Utilizar Dados Acima?</label>
          <div class="formopcao">
            <input type="radio" name="sameAddress" <?php echo $field->getDBField("sameAddress", 56) ? "checked " : ""; ?>value="1"
              onclick="this.form.chargeAddress.disabled=true,this.form.chargeCity.disabled=true,this.form.chargeCep.disabled=true,this.form.chargeUf.disabled=true,this.form.chargeAddressNumber.disabled=true,this.form.chargeAddressComp.disabled=true">
          </div>
          <div class="formdescricao">SIM</div>
          <div class="formopcao">
            <input type="radio" name="sameAddress" <?php echo $field->getDBField("sameAddress", 56) ? "" : "checked " ?>value="0"
              onclick="this.form.chargeAddress.disabled=false,this.form.chargeCity.disabled=false,this.form.chargeCep.disabled=false,this.form.chargeUf.disabled=false,this.form.chargeAddressNumber.disabled=false,this.form.chargeAddressComp.disabled=false">
          </div>
          <div class="formdescricao">N&Atilde;O</div>
        </li>
      </ul>
      <div style="clear:both;">&nbsp;</div>
      <ul>
        <li class="campo3colunas">
          <label>Logradouro:</label>
          <input type="text" maxlength="100" name="chargeAddress" id="end_cob"
            value="<?php echo $field->getDBField("chargeAddress", 57); ?>" <?php echo $field->getDBField("sameAddress", 56) ? "disabled " : ""; ?> onBlur="">
        </li>

        <li class="campo3colunas">
          <label>N&ordm;:</label>
          <input type="text" maxlength="5" name="chargeAddressNumber" value="<?php echo $chargeAddressNumber; ?>"
            <?php echo $field->getDBField("sameAddress", 56) ? "disabled " : ""; ?>>
        </li>

        <li class="campo3colunas">
          <label>Complemento:</label>
          <input type="text" size="20" maxlength="15" name="chargeAddressComp"
            value="<?php echo $chargeAddressComp; ?>" <?php echo $field->getDBField("sameAddress", 56) ? "disabled " : ""; ?>>
        </li>

        <li class="campo3colunas"><label>Cidade:</label>
          <input type="text" maxlength="50" name="chargeCity" id="cidade_cob"
            value="<?php echo $field->getDBField("chargeCity", 58); ?>" <?php echo $field->getDBField("sameAddress", 56) ? "disabled " : ""; ?> onBlur="">
        </li>
        <li class="campo3colunas">
          <label>UF:</label>

          <?php // Monta a lista de UF
          
            $sql = "SELECT uf FROM UF where uf<>'NA' ORDER BY uf";
            $res = odbc_exec($db, $sql);
            ?>
          <select name="chargeUf" id="chargeUf" <?php echo $field->getDBField("sameAddress", 56) ? "disabled " : ""; ?>>
            <option value="">-- Selecione Uma Op&ccedil;&atilde;o --</option>
            <?php while ($resultado = odbc_fetch_row($res)) { ?>
              <option value="<?php echo odbc_result($res, 'uf'); ?>" <?php echo ($chargeUf == odbc_result($res, 'uf') ? 'selected' : ''); ?>> <?php echo odbc_result($res, 'uf'); ?></option>
            <?php } ?>

          </select>
        </li>

        <li class="campo3colunas">
          <label>CEP:</label>
          <input type="text" name="chargeCep"
            value="<?php echo trim($field->getDBField("chargeCep", 59) ?? ''); ?>" <?php echo $field->getDBField("sameAddress", 56) ? "disabled " : ""; ?> onBlur="" maxlength="9"
            onkeyup="FormataCEP(this,event)">
        </li>
      </ul>





      <?php //Interaktiv ?>


      <style>
        .ui-autocomplete {
          max-height: 100px;
          overflow-y: auto;
          /* prevent horizontal scrollbar */
          overflow-x: hidden;
        }

        /* IE 6 doesn't support max-height
           * we use height instead, but this forces the menu to always be this tall
           */
        * html .ui-autocomplete {
          height: 100px;
        }
      </style>

      <link href="<?php echo $root; ?>scripts/jquery_ui/jquery_ui.min.css" rel="stylesheet" type="text/css" />
      <script language="JavaScript" src="<?php echo $root; ?>scripts/jquery_ui/jquery_ui.js"></script>


      <?php
      $ped = explode("/inform/", $_SERVER["REQUEST_URI"]);
      $url = $ped[0] . "/inform/ajax_cnae.php";
      $url_ac = $ped[0] . "/inform/ajax_ac_cnae.php";
      ?>

      <script>
        function bloqueia_tr_um() {
          $("#list_secoes").attr("disabled", true);
          $("#list_divisoes").attr("disabled", true);
          $("#list_grupos").attr("disabled", true);
          $("#list_classes").attr("disabled", true);

          $("#txt_busca_cnae").removeAttr("disabled");
          $(".tr_um").find("td").css("background-color", "#f5f5f5");
          $(".tr_dois").find("td").css("background-color", "#fff");
        }
        function bloqueia_tr_dois() {
          $("#list_secoes").removeAttr("disabled");
          $("#list_divisoes").removeAttr("disabled");
          $("#list_grupos").removeAttr("disabled");
          $("#list_classes").removeAttr("disabled");

          $("#txt_busca_cnae").attr("disabled", true);
          $(".tr_um").find("td").css("background-color", "#fff");
          $(".tr_dois").find("td").css("background-color", "#f5f5f5");
        }
        $(document).ready(function () {
          $("input[name='opt_cnae']").on("change", function () {
            var id = $(this).val();

            if (id == "1") {
              bloqueia_tr_dois();
            } else {
              bloqueia_tr_um();
            }
          })

          $(".bg-black").on("click", function () {
            $(".modal-ext").hide();
          })

          $("#txt_busca_cnae").autocomplete({
            source: '<?php echo htmlspecialchars($url_ac, ENT_QUOTES, 'UTF-8'); ?>',
            minLength: 2,
            select: function (event, ui) {
              console.log(ui.item);
              $("#id_class_ac").val(ui.item.id);
            }
          });

          $("#list_secoes").on("change", function () {
            var id = $(this).val();
            $.ajax({
              type: "POST",
              url: '<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>',
              data: { tipo: 2, valor: id },
              success: function (data) {
                var html = '';
                $.each(data, function (index, value) {
                  html += '<option value="' + value.id + '">' + value.titulo + '</option>';
                })
                $("#list_divisoes").html(html);
                if (data.length == 1) {
                  $("#list_divisoes").trigger("change");
                }
              }
            })
          })

          $("#list_divisoes").on("change", function () {
            var id = $(this).val();
            $.ajax({
              type: "POST",
              url: '<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>',
              data: { tipo: 3, valor: id },
              success: function (data) {
                var html = '';
                $.each(data, function (index, value) {
                  html += '<option value="' + value.id + '">' + value.titulo + '</option>';
                })
                $("#list_grupos").html(html);
                if (data.length == 1) {
                  $("#list_grupos").trigger("change");
                }

              }
            })
          })

          $("#list_grupos").on("change", function () {
            var id = $(this).val();
            $.ajax({
              type: "POST",
              url: '<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>',
              data: { tipo: 4, valor: id },
              success: function (data) {
                var html = '';
                $.each(data, function (index, value) {
                  html += '<option value="' + value.id + '">' + value.titulo + '</option>';
                });
                $("#list_classes").html(html);
                if (data.length == 1) {
                  $("#list_classes").trigger("change");
                }
              }
            });
          });


          $("#seleciona_cnae").on("click", function () {
            var tipo = $("input[name='opt_cnae']:checked").val();

            if (tipo == 1) {
              var classe_id = $("#list_classes").val();
              var classe_tit = $("#list_classes option:selected").text();

              $("#nome_classe_cnae").html(classe_tit);
              $("#id_sel_classe_cnae").val(classe_id);
            } else {
              $("#nome_classe_cnae").html($("#txt_busca_cnae").val());
              $("#id_sel_classe_cnae").val($("#id_class_ac").val());

            }

            $(".modal-ext").hide();

          })

          $("#abre_modal_cnae").on("click", function () {
            $(".modal-ext").show();
          })
        })
      </script>

      <div class="divisoria01"></div>
      <div style="clear:both">&nbsp;</div>

      <ul>
        <label>
          <h3>Classifica&ccedil;&atilde;o Nacional de Atividades Econ&ocirc;micas</h3>
        </label>
      </ul>
      <div class="divisoria01"> &nbsp;</div>
      <?php
      // Interaktiv
      // fazendo trazer só as seções do CNAE
      $sqlCnae = "SELECT * FROM CNAE WHERE t_CNAE = 1";
      $curCnae = odbc_exec($db, $sqlCnae);

      $lista_secoes_cnae = array();
      while (odbc_fetch_row($curCnae)) {
        $lista_secoes_cnae[] = array("id" => odbc_result($curCnae, 'i_CNAE'), "nome" => odbc_result($curCnae, 'Descricao') . " - " . odbc_result($curCnae, 'Cod_CNAE'));
      }
      ?>

      <!-- Modal -->
      <div class="modal-ext" style="display:none">
        <div class="bg-black"></div>

        <div class='modal-int'>
          <h1>Selecionar CNAE</h1>
          <div class="divisoriaamarelo"></div>



          <table class="tbl_opts_cnae">
            <tr class="tr_um">
              <td width="10%"><input type="radio" name="opt_cnae" value="1" checked></td>
              <td>
                <p>Busque a classe por seus n&iacute;veis</p>
                <ul>
                  <li class="">
                    <label>Se&ccedil;&atilde;o:</label>
                    <select name="" id="list_secoes">
                      <option value="">Selecione</option>
                      <?php for ($i = 0; $i < count($lista_secoes_cnae); $i++) { ?>
                        <option value="<?php echo $lista_secoes_cnae[$i]["id"] ?>">
                          <?php echo $lista_secoes_cnae[$i]["nome"] ?>
                        </option>
                      <?php } ?>
                    </select>
                  </li>
                  <li class="">
                    <label>Divis&atilde;o:</label>
                    <select name="" id="list_divisoes">
                      <option value="">Selecione uma se&ccedil;&atilde;o</option>
                    </select>
                  </li>
                  <li class="">
                    <label>Grupo:</label>
                    <select name="" id="list_grupos">
                      <option value="">Selecione uma divis&atilde;o</option>
                    </select>
                  </li>
                  <li class="">
                    <label>Classe:</label>
                    <select name="" id="list_classes">
                      <option value="">Selecione um grupo</option>
                    </select>
                  </li>
                </ul>
              </td>
            </tr>
            <tr class="tr_dois">
              <td width="10%" style="background-color:#f5f5f5;"><input type="radio" name="opt_cnae" value="2">
              </td>
              <td style="background-color:#f5f5f5;">
                <ul>
                  <li>
                    <p>Ou busque-a pelo nome</p>
                  </li>
                  <li>
                    <input type="text" name="busca_cnae" id="txt_busca_cnae" disabled>
                    <input type="hidden" id="id_class_ac">
                  </li>
                </ul>
              </td>
            </tr>
          </table>

          <button style="margin: 10px 0" type="button" class="botaoagg" id="seleciona_cnae">OK</button>
        </div>
      </div>

      <!-- Fim modal -->

      <ul>
        <li class="campo3colunas">
          <label>CNAE:</label>
          <p id="nome_classe_cnae"><?php echo trim($desc_cnae) != "" ? $desc_cnae : "Nenhum cadastrado"; ?></p>
          <input type="hidden" id="id_sel_classe_cnae" name="sel_classe_cnae" value="<?php echo $i_CNAE; ?>">
          <button type="button" class="botaoagg" id="abre_modal_cnae">Selecionar</button>
        </li>
      </ul>


      <div style="clear:both">&nbsp;</div>
      <label>
        <h3>Atividade Comercial</h3>
      </label>
      <div class="divisoria01"> &nbsp;</div>
      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo3colunas">
          <label>Setor</label>

          <?php //monta a lista de setor
          
            $sql = "SELECT id, description FROM Sector ORDER BY description";
            $sel = $field->getDBField("idSector", 16);
            $name = "idSector";
            require("../../interf/Select.php");
            ?>
        </li>
        <li class="campo3colunas">
          <label>Produto(s) Comercializado(s):</label>
          <input type="text" size="30" maxlength="200" name="products"
            value="<?php echo $field->getDBField("products", 38); ?>">
        </li>
        <li class="campo2colunas">
          <label>Prazo M&eacute;dio Usualmente Concedido Para Pagamento: (Dias)</label>
          <input type="text" maxlength="4" name="frameMed"
            value="<?php echo $field->getDBField("frameMed", 42); ?>">
        </li>
        <li class="campo3colunas">
          <label>A Empresa comercializa h&aacute; mais de Tr&ecirc;s Anos?</label>
          <div class="formopcao">
            <input type="radio" name="exportMore" <?php echo $field->getDBField("exportMore", 91) ? "checked " : ""; ?>value="1">
          </div>
          <div class="formdescricao">SIM</div>
          <div class="formopcao">
            <input type="radio" name="exportMore" <?php echo !$field->getDBField("exportMore", 91) ? "checked " : ""; ?>value="0">
          </div>
          <div class="formdescricao">N&Atilde;O</div>
        </li>
      </ul>
      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo3colunas">
          <label>
            <h3>Informa&ccedil;&otilde;es Gerais</h3>
          </label>
        </li>
      </ul>
      <div class="divisoria01">&nbsp;</div>
      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo2colunas" style="width:700px; height:80px;">
          <label>A Empresa Pertence a Algum Grupo?</label>
          <div class="formopcao">
            <input type="radio" name="hasGroup" <?php echo $field->getDBField("hasGroup", 44) ? "checked " : ""; ?>value="1">
          </div>
          <div class="formdescricao">SIM</div>
          <div class="formopcao">
            <input type="radio" name="hasGroup" <?php echo !$field->getDBField("hasGroup", 44) ? "checked " : ""; ?>value="0">
          </div>
          <div class="formdescricao">N&Atilde;O</div>
          <div class="formdescricao">Caso Positivo, Qual?</div>
          <input type="text" maxlength="50" name="companyGroup"
            value="<?php echo $field->getDBField("companyGroup", 45); ?>">
        </li>
      </ul>
      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo2colunas" style="width:700px;">
          <label>A Empresa Possui Companhias Associadas No Exterior?</label>
          <div class="formopcao">
            <input type="radio" name="hasAssocCompanies" <?php echo $field->getDBField("hasAssocCompanies", 46) ? "checked " : ""; ?>value="1">
          </div>
          <div class="formdescricao">SIM</div>
          <div class="formopcao">
            <input type="radio" name="hasAssocCompanies" <?php echo !$field->getDBField("hasAssocCompanies", 46) ? "checked " : ""; ?>value="0">
          </div>
          <div class="formdescricao">N&Atilde;O</div>
          <div class="formdescricao">Caso Positivo, Quais?</div>
          <input type="text" size="30" maxlength="50" name="associatedCompanies"
            value="<?php echo $field->getDBField("associatedCompanies", 47); ?>">
        </li>
      </ul>
      <div style="clear:both">&nbsp;</div>
      <label>
        <h3>Objetivos</h3>
      </label>
      <div class="divisoria01">&nbsp;</div>
      <!--<div class="formopcao">
      <input type="checkbox" name="warantyExp"<?php echo $field->getDBField("warantyExp", 49) ? " checked" : ""; ?> value="1">
    </div>
    <div class="formdescricao">Garantia &agrave; exporta&ccedil;&atilde;o</div> -->
      <ul>
        <li class="campo3colunas" style="width:700px;">
          <div class="formopcao">
            <input type="checkbox" name="warantyFin" <?php echo $field->getDBField("warantyFin", 50) ? "checked " : ""; ?> value="1">
          </div>
          <div class="formdescricao">Garantia para financiamento</div>
          <div class="formopcao">
            <input type="checkbox" name="hasAnother" <?php echo $field->getDBField("hasAnother", 51) ? "checked " : ""; ?> value="1">
          </div>
          <div class="formdescricao">Outros. Quais?</div>
          <input type="text" size="30" maxlength="50" name="another"
            value="<?php echo $field->getDBField("another", 52); ?>">
        </li>
        <!-- desativar aqui e ativa na etapa de oferta  -->
        <?php if (check_menu(['executive', 'executiveLow'], $role) && $_SESSION['pefil'] != 'C' && $_SESSION['pefil'] != 'B') { ?>
          <li class="campo3colunas">
            <?php

            $sql = "select GC.i_Gerente, GC.Nome from 
           Gerente_Comercial GC  
           Where Situacao = 0 Or Exists 
           (Select * From Inform Inf Where Inf.i_Gerente = GC.i_Gerente And Inf.id = 6065) Order By GC.Nome ";
            $cur2 = odbc_exec($db, $sql);

            $lista_gerentes = array();
            while ($dados = odbc_fetch_row($cur2)) {
              $lista_gerentes[] = array("id" => odbc_result($cur2, 'i_Gerente'), "nome" => odbc_result($cur2, 'Nome'));
            }

            ?>
            <label>Gerente Originador</label>
            <select name="i_Gerente" id="i_Gerente" onChange="">
              <option value="">Selecione...</option>

              <?php
              for ($i = 0; $i < count($lista_gerentes); $i++) {
                if ($i_Gerente == $lista_gerentes[$i]["id"]) {
                  $selt = 'selected';
                } else {
                  $selt = '';
                }
                echo '<option value="' . $lista_gerentes[$i]["id"] . '"  ' . $selt . '>' . $lista_gerentes[$i]["nome"] . '</option>';
              }
              ?>
            </select>
          </li>

          <li class="campo3colunas">
            <?php
            ?>
            <label>Gerente de Relacionamento</label>
            <select name="i_GerenteR" id="i_GerenteR" onChange="">
              <option value="">Selecione...</option>

              <?php
              for ($i = 0; $i < count($lista_gerentes); $i++) {
                if ($i_Gerente_Relacionamento == $lista_gerentes[$i]["id"]) {
                  $selt = 'selected';
                } else {
                  $selt = '';
                }
                echo '<option value="' . $lista_gerentes[$i]["id"] . '"  ' . $selt . '>' . $lista_gerentes[$i]["nome"] . '</option>';
              }
              ?>
            </select>
          </li>
        <?php } else { ?>
          <input type="hidden" name="i_Gerente" id="i_Gerente" value="<?php echo $i_Gerente; ?>" />
          <input type="hidden" name="i_Gerente_Relacionamento" id="i_Gerente_Relacionamento"
            value="<?php echo $i_Gerente_Relacionamento; ?>" />

        <?php } ?>
      </ul>
      <div style="clear:both">&nbsp;</div>
      <div class="barrabotoes">
        <input type="hidden" name="inicial" id="inicial" value="" />
        <?php if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') { ?>
          <button type="button" class="botaoagg" onClick="this.form.comm.value='open';this.form.submit()">Ver
            Informes</button>
        <?php } ?>
        <button type="button" class="botaoagg"
          onClick="this.form.inicial.value= 1; this.form.comm.value='open';this.form.submit()">Tela
          Inicial</button>
        <button type="button" class="botaoagg"
          onClick="javascript: if (checa_formulario(this.form)) this.form.submit();">Pr&oacute;xima Tela</button>

        &nbsp;&nbsp;&nbsp;<label>Exportar informa&ccedil;&otilde;es para Excel<a
            href="<?php echo $root ?>role/inform/interf/relatorio_informe_excel.php?inform=<?php echo $idInform; ?>&pagina=1"
            target="new"><img border="0" src="<?php echo $root ?>../images/excel_icon.png"
              title="Exportar para EXCEL" /></a>.</label>
      </div>
    </form>
    <?php
} else {
  ?>
    <ul>
      <li class="campo2colunas">
        <label>Informe inv&aacute;lido</label>
      </li>
    </ul>
    <?php
}
?>
  <div style="clear:both">&nbsp;</div>
</div>
<script>
  function confirmar() {
    if (verErro("Deseja Realmente Escolher Esta Região ?")) {
      window.parent.location.href = '../inform/Inform.php?comm=generalInformation';
      return (true);

    }

  }
</script>