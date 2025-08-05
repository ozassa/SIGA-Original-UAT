<?php

  require_once("../../../config.php");
  require_once("../../pdfConf.php");
  require_once("../../../gerar_pdf/MPDF45/mpdf.php");

  $id_Cessao = isset($_REQUEST["id_Cessao"]) ? $_REQUEST["id_Cessao"] : false;
  $key = isset($_REQUEST["key_id"]) ? $_REQUEST["key_id"] : false;

  $stmt = odbc_prepare($db, "EXEC SPR_BB_Doc_Distrato_Condicoes_Especiais ?, ?");
odbc_execute($stmt, [$id_Cessao, '100']);
$rsSql = $stmt;
odbc_free_result($stmt);

  while(odbc_fetch_row($rsSql)) {
    $Cod_Clausula = odbc_result($rsSql, "Cod_Clausula");
    $Ano_Clausula = odbc_result($rsSql, "Ano_Clausula");
    $Nome_Ramo = odbc_result($rsSql, "Nome_Ramo");
    $Num_Apolice = odbc_result($rsSql, "Num_Apolice");
    $Cod_SUSEP = odbc_result($rsSql, "Cod_SUSEP");
    $Site_Empresa = odbc_result($rsSql, "Site_Empresa");
    $Data_Cancelamento = odbc_result($rsSql, "Data_Cancelamento") ? Convert_Data_Geral(substr(odbc_result($rsSql, "Data_Cancelamento"), 0, 10)) : "";

    //SEGURADORA
    $Nome_Empresa = strtoupper(odbc_result($rsSql, "Nome_Empresa"));
    $Endereco_Empresa = odbc_result($rsSql, "Endereco_Empresa");
    $Complemento_Empresa = odbc_result($rsSql, "Complemento_Empresa");
    $Bairro_Empresa = odbc_result($rsSql, "Bairro_Empresa");
    $Cidade_Empresa = odbc_result($rsSql, "Cidade_Empresa");
    $UF_Empresa = odbc_result($rsSql, "UF_Empresa");
    $CNPJ_Empresa = odbc_result($rsSql, "CPNJ_Empresa") ? formata_string('CNPJ', odbc_result($rsSql, "CPNJ_Empresa")) : "";

    //SEGURADO
    $Nome_Segurado = strtoupper(odbc_result($rsSql, "Nome_Segurado"));
    $Endereco_Segurado = odbc_result($rsSql, "Endereco_Segurado");
    $Cidade_Segurado = odbc_result($rsSql, "Cidade_Segurado");
    $UF_Segurado = odbc_result($rsSql, "UF_Segurado");
    $CNPJ_Segurado = odbc_result($rsSql, "CNPJ_Segurado") ? formata_string('CNPJ', odbc_result($rsSql, "CNPJ_Segurado")) : "";

    //BENEFICIÁRIO
    $Nome_Banco = strtoupper(odbc_result($rsSql, "Nome_Banco"));
    $Nome_Agencia = strtoupper(odbc_result($rsSql, "Nome_Agencia"));
    $Endereco_Agencia = odbc_result($rsSql, "Endereco_Agencia");
    $Cidade_Agencia = odbc_result($rsSql, "Cidade_Agencia");
    $UF_Agencia = odbc_result($rsSql, "UF_Agencia");
    $CNPJ_Agencia = odbc_result($rsSql, "CNPJ_Agencia") ? formata_string('CNPJ', odbc_result($rsSql, "CNPJ_Agencia")) : "";
  }

  $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4',
        'margin_left' => 20,
        'margin_right' => 15,
        'margin_top' => 45,
        'margin_bottom' => 5,
        'margin_header' => 10,
        'margin_footer' => 10
        ];
    
    $mpdf=new  \Mpdf\Mpdf($opt);

  $html = ob_get_clean();
  // $mpdf->useOnlyCoreFonts = true;    // false is default
  //$mpdf->SetProtection(array('print'));
  $mpdf->SetTitle("Proposta");
  $mpdf->SetAuthor($Nome_Empresa);
  $mpdf->SetWatermarkText(""); // fundo marca dágua
  $mpdf->showWatermarkText = true;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->SetDisplayMode('fullpage');

  // Endereço do logotipo
  $logo = '../../images/logo.jpg';
  $logo_peq = '../../images/logo_peq.jpg';  

  // Início do arquivo montando primeiro o CSS
  $html = '<html>
            <head>
              <style>
                body {font-family: Arial Black, Helvetica, sans-serif; font-size: 11pt;}
                p {margin: 0pt;}
                ol {counter-reset: item; font-weight:bold;}
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; text-align:justify;}
                ul {list-style-type: none; font-weight:normal;}
                ul li {padding: 3px 0px;color: #000000;text-align:justify;}
                #cobtexto {font-family: Arial, Helvetica, sans-serif; font-size:11pt; text-align:justify;}
                #sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:11pt; text-align:justify; font-weight:bold; text-decoration:underline;}
                #disclame {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
              </style>            
            </head>

            <body>        
              <htmlpageheader name="myheader">
                <div style="text-align: center;">
                  <img src="'.$logo.'" width="230" height="75"/>
                </div>

                <div style="text-align: center;">
                  <span style="font-weight: bold; font-size: 11pt;">
                    TERMO DE DISTRATO ÀS CONDIÇÕES ESPECIAIS DE CLÁUSULA BENEFICIÁRIA<br>
                    Nº '.$Cod_Clausula.' DA APÓLICE DE '.$Nome_Ramo.' Nº '.$Num_Apolice.'
                  </span>
                </div>
              </htmlpageheader>
              
                  
              <sethtmlpageheader name="myheader" value="on" show-this-page="1" />

              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td width="25%"><b>SEGURADORA:</b></td>
                  <td width="75%"><b>'.$Nome_Empresa.'</b></td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">'.$Endereco_Empresa.' / '.$Complemento_Empresa.' - '.$Bairro_Empresa.'</td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">'.$Cidade_Empresa.' - '.$UF_Empresa.'</td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td>CNPJ: '.$CNPJ_Empresa.'</td>
                </tr>            
              </table>

              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td width="25%"><b>SEGURADO:</b></td>
                  <td width="75%"><b>'.$Nome_Segurado.'</b></td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">'.$Endereco_Segurado.'</td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">'.$Cidade_Segurado.' - '.$UF_Segurado.'</td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">CNPJ: '.$CNPJ_Segurado.'</td>
                </tr>            
              </table>

              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td width="25%"><b>BENEFICIÁRIO:</b></td>
                  <td width="75%"><b>'.$Nome_Banco.' - '.$Nome_Agencia.'</b></td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">'.$Endereco_Agencia.'</td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">'.$Cidade_Agencia.' - '.$UF_Agencia.'</td>
                </tr>

                <tr>
                  <td width="25%">&nbsp;</td>
                  <td width="75%">CNPJ: '.$CNPJ_Agencia.'</td>
                </tr>            
              </table>

              <div style="clear:both">&nbsp;</div>
              
              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td width="100%"><b>CLÁUSULA 1ª - OBJETO -</b></td>
                </tr>

                <tr>
                  <td id="cobtexto" width="100%">
                    As partes acima qualificadas, pelo presente <b>TERMO DE DISTRATO</b>, têm por resilidas as disposições contratuais contidas nas <b>CONDIÇÕES ESPECIAIS DE ALTERAÇÃO DE 
                    CLÁUSULA BENEFICIÁRIA Nº '.$Cod_Clausula.'/'.$Ano_Clausula.'</b>, que passam a ser consideradas definitivamente revogadas, ressalvados, contudo, os direitos 
                    adquiridos durante sua vigência.
                  </td>
                </tr>
              </table>

              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td width="100%"><b>CLÁUSULA 2ª - VIGÊNCIA -</b></td>
                </tr>

                <tr>
                  <td id="cobtexto" width="100%">
                    As disposições da Cláusula primeira aplicam-se às operações de exportação efetuadas a partir de '.$Data_Cancelamento.', data em que as <b>CONDIÇÕES ESPECIAIS DE 
                    ALTERAÇÃO DE CLÁUSULA DENEFICIÁRIA Nº '.$Cod_Clausula.'/'.$Ano_Clausula.'</b> deixam de produzir todo e qualquer efeito.
                  </td>
                </tr>
              </table>

              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td width="100%"><b>CLÁUSULA 3ª - PODERES DE REPRESENTAÇÃO -</b></td>
                </tr>

                <tr>
                  <td id="cobtexto" width="100%">
                    Os signatários do presente documento declaram, sob as penas da lei, estarem investidos de poderes por seus representados para celebrarem o presente na forma em 
                    que está redigido.
                  </td>
                </tr>
              </table>

              <div style="clear:both">&nbsp;</div>
              
              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td style="text-align:center">São Paulo, ____ de ________ de _______.</td>
                </tr>
              </table>

              <div style="clear:both">&nbsp;</div>

              <table width="100%" border="0" cellpadding="1">
                <tr>
                  <td style="text-align:center">_________________________________</td>
                  <td style="text-align:center"> __________________________________</td>
                </tr>

                <tr>
                  <td style="text-align:center">'.$Nome_Segurado.'</td>
                  <td style="text-align:center">'.$Nome_Banco.'</td>
                </tr>

                <tr>
                  <td style="text-align:center">(Carimbo do Signatário)</td>
                  <td style="text-align:center">(Carimbo do Signatário)</td>
                </tr>
              </table>

              <div style="clear:both">&nbsp;</div>
              
              <table width="100%" border="0" style="font-size: 11pt;">
                <tr>
                  <td style="text-align:center">______________________________________________________________</td>
                </tr>

                <tr>
                  <td style="text-align:center">'.$Nome_Empresa.'</td>
                </tr>

                <tr>
                  <td style="text-align:center">(Carimbo do Signatário)</td>
                </tr>
              </table>

              <div style="clear:both">&nbsp;</div>

              <table width="100%" border="0">
                <tr>
                  <td width="30%">&nbsp;</td>
                  <td align="right" width="80%">
                    <div align="right" id="disclame">
                      *Seguro garantido pela '.$Nome_Empresa.'. (uma empresa Coface)<br>
                      CNPJ: '.$CNPJ_Empresa.', SUSEP no. '.$Cod_SUSEP.'
                    </div>
                  </td> 
                </tr>
              </table>
              
            </body>
          </html>';

  $html = utf8_encode($html);
  $mpdf->allow_charset_conversion = true;
  $mpdf->charset_in = 'UTF-8';
  $mpdf->WriteHTML($html);
  
  $mpdf->Output($pdfDir.$key.'Distrato.pdf', 'F'); 
  $mpdf->Output($pdfDir.$key.'Distrato.pdf', 'D'); 

?>