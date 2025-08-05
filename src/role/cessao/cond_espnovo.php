<?php
  
  require_once("../rolePrefix.php");
  require_once("../../../config.php");
  require_once("../../pdfConf.php");
  require_once("../../../gerar_pdf/MPDF45/mpdf.php");

  include_once("../consultaCoface.php");




  $idInform        = $_REQUEST['idInform'];
  $idAgencia       = $_REQUEST['idAgencia'];
  $agencia         = $_REQUEST['agencia'];
  $idBanco         = $_REQUEST['idBanco'];
  $idCDBB          = $_REQUEST['idCDBB'];
  $idCDOB          = $_REQUEST['idCDOB'];
  $idCDParc        = $_REQUEST['idCDParc'];
  $tipoBanco       = $_REQUEST['tipoBanco'];
  $total           = isset($_REQUEST['total']) ? $_REQUEST['total'] : 0;
  $totalR          = isset($_REQUEST['totalR']) ? $_REQUEST['totalR'] : 0;
  $comm            = $_REQUEST['comm'];
  $novalue         = isset($_REQUEST['novalue']) ? $_REQUEST['novalue'] : '';


    function ymd2dmy($d){
      if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
          return "$v[3]/$v[2]/$v[1]";
        } 
      
        return $d;
    } 



  include_once("consultasCondEsp.php");


    function arruma_cnpj($c){
      if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
      return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
      }
      
      return $c;
    }

    /*SELECIONA O NUMERO DA APOLICE*/
    $x = odbc_prepare($db, "SELECT n_Apolice FROM Inform WHERE id = ?");
    odbc_execute($x, array($idInform));
      
      if(odbc_fetch_row($x)){
        $apolice = sprintf("062%06d", odbc_result($x, 1));
      }
      odbc_free_result($x);
  
    /*DADOS DA COFACE*/

    $sqlEmp  = "SELECT Nome, CNPJ,  Endereco, Complemento, CEP, Cidade, Estado, Cod_Area, Telefone, Bairro, Fax, HomePage
    FROM Empresa 
        WHERE i_Empresa = 1";  
   
    $resEmp = odbc_exec($db,$sqlEmp);
    $dadosEmp = odbc_fetch_array($resEmp);

    $compEmp = $dadosEmp['Complemento'] ? ' - '.$dadosEmp['Complemento'] : '';
    $cepEmp = $dadosEmp['CEP'] ? ' - Cep: '.$dadosEmp['CEP'] : '';
    $cidEmp = $dadosEmp['Cidade'] ? ' - '.$dadosEmp['Cidade'] : '';
    $estEmp = $dadosEmp['Estado'] ? ', '.$dadosEmp['Estado'] : '';
    $telEmp = $dadosEmp['Telefone'] ? ' Tel.: '.$dadosEmp['Telefone'] : '';
    $faxEmp = $dadosEmp['Fax'] ? ' Fax: '.$dadosEmp['Fax'] : '';
    $enderecoEmp = $dadosEmp['Endereco'].$compEmp.$cepEmp.$cidEmp.$estEmp.$telEmp.$faxEmp;
    $siteEmp = $dadosEmp['HomePage'];
    $nomeEmp = $dadosEmp['Nome'];
    $bairroEmp = $dadosEmp['Bairro'];
    $cnpjEmp = $dadosEmp['CNPJ'];

    /*DADOS DO BANCO*/
   

    $x = odbc_prepare($db, "SELECT A.name, A.endereco, A.cidade, A.uf, A.cnpj, A.ie, A.idBanco, B.name
  FROM Agencia A INNER JOIN Banco B ON B.id = A.idBanco WHERE A.id = ?");
odbc_execute($x, array($idAgencia));
        
        $bb_nome = odbc_result($x, 1);
        $bb_address = odbc_result($x, 2);
        $bb_ce = odbc_result($x, 3). " - ". odbc_result($x, 4);
        $bb_cnpj = arruma_cnpj(odbc_result($x, 5));
        $bb_ie = odbc_result($x, 6);
        $bb_ie = preg_match("/^[0-9]+$/", $bb_ie) ? number_format($bb_ie, 0, '', '.') : $bb_ie;
        $idBanco = odbc_result($x, 7);

  $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4',
        'margin_left' => 20,
        'margin_right' => 15,
        'margin_top' => 42,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
    ];

    odbc_free_result($x);

    $mpdf=new  \Mpdf\Mpdf($opt);

  
    $html = ob_get_clean();
    
    $mpdf->SetProtection(array('print'));
    $mpdf->SetTitle("Proposta");
    $mpdf->SetAuthor($nomeEmp);
    
    if (isset($_REQUEST['rascunho'])) {
      $mpdf->SetWatermarkText("RASCUNHO"); // fundo marca dágua
    } else {
      $mpdf->SetWatermarkText(""); // fundo marca dágua
    }
    
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->SetDisplayMode('fullpage');

    // Endereço do logotipo
    $logo       =  $root .'images/logo.jpg';
    $logo_peq   = '../../images/logo_peq.jpg';
    //$logo_mini  = $root .'images/logo_peq.jpg';
    $logo_mini  = $root .'images/logo.jpg';
    $assinatura  = $root .'images/Assinatura Fernando.gif';
      
    $datahoje =  '';

    $conta_clausula = 1;

    $html = '<html>
        <head>
          <style>
            body {font-family: Arial, Helvetica, sans-serif;
              font-size: 12pt;
              color: #03365f;
            }
            p {    margin: 0pt;
            }
    
            ol {counter-reset: item; font-weight:bold; }
                    li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify}
                    li:before {content: counters(item, "."); counter-increment: item; }
            
            ul      {list-style-type: none; font-weight:normal } 
            ul li   {padding: 3px 0px;color: #000000;text-align:justify} 
    
            #cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
            #sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
            #disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
            
            #img1{
              width: 300px;
              height: 70px;
              background:url('.$logo.') no-repeat;
    
            }';

            $html .='
          </style>
        </head>
        
        <body>
                <htmlpageheader name="myheader2">
                  <div style="text-align: center;">
                      <img src="'.$logo.'" width="260" height="70"/>
                  </div>
                  
                  <div style="text-align: center;">
                    <span style="font-weight: bold; font-size: 12pt;">ENDOSSO PARA DESIGNAÇÃO DE BENEFICIÁRIO n° '.$Cod_Clausula.' - CONDIÇÕES ESPECIAIS VINCULADAS À APÓLICE N.º 06200'.$Num_Apolice.' 
                    </span>
                    <span style="font-weight: bold; font-size: 12pt;"></span>
                    <br>
                    <br>
                    <br>

                    <span style="font-weight: bold; font-size: 10pt;">
                      <h4><b>Esta Condição Especial para Designação de Beneficiário (doravante referida como "Condição Especial") é celebrada entre:</b>
                      </h4>
                  </span>
                  </div> 
                </htmlpageheader>

                <htmlpageheader name="myheader" style="height: 5px;">
                  <div style="text-align: center">
                    <img src="'.$logo.'" width ="260" height="70"/>
                  </div>
                </htmlpageheader>

                <htmlpagefooter name="myfooter">
                  <table width="100%" border="0">
                      <tr>
                        <td width="22%">&nbsp;</td>
                        <td width="56%" style="text-align:center; font-size: 8pt;">
                            Página {PAGENO} de {nb}
                            <br><br>
                        </td>
  
                        <td width="22%" style="font-size:4pt;">V.RTL2</td>
                      </tr>
                  </table>
                </htmlpagefooter>
                
                <sethtmlpageheader name="myheader2" value="on" show-this-page="1" />
                <sethtmlpageheader name="myheader" value="on" show-this-page="0" />
                <sethtmlpagefooter name="myfooter" value="on" />

          <div style="clear:both">&nbsp;</div>'; 

          $html .= '
          
          <div style="margin-top: 100px;">&nbsp;</div>
            <div style="clear:both">  
              <div id="cobtexto" style="font-size: 10pt;"> 
                <p><b>COFACE DO BRASIL SEGUROS DE CRÉDITO S/A</b>, uma empresa registrada sob as leis do Brasil, com sede social na cidade e Estado de São Paulo, à '.$Endereco_Empresa.' / '.$Complemento_Empresa.' - Brooklin Novo inscrita no CNPJ/MF sob o nº '.formata_string('CNPJ', $CPNJ_Empresa).', neste ato devidamente representada de acordo com o seu Estatuto Social (doravante denominada, simplesmente, "SEGURADORA");</p>
              </div>
              
              <div style="margin-top: 25px;">
                <p style="font-size: 10pt;">e</p>
              </div>
              
              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p><b>'. strtoupper(trim($Nome_Banco)).' - '.$Nome_Agencia.'</b>, uma instituição financeira registrada sob as leis do Brasil, inscrita no CNPJ sob nº '.formata_string('CNPJ', $CNPJ_Agencia).', com sede na '.$Endereco_Agencia.' neste ato representada na forma de seu Estatuto Social/Contrato Social (doravante denominada, simplesmente, "BANCO")</p>
              </div>
              
              <div style="margin-top: 25px;">
                <p style="font-size: 10pt;">e</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p><b>'.strtoupper(trim($Nome_Segurado)).'</b>, uma empresa registrada sob as leis do Brasil, inscrita no CNPJ sob nº '.formata_string('CNPJ', $CNPJ_Segurado).', com sede na '.$Endereco_Segurado.' neste ato representada na forma de seu Estatuto Social/Contrato Social (doravante denominada, simplesmente, "SEGURADO")</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>SEGURADORA, BANCO E SEGURADO serão doravante denominadas, em conjunto, como "PARTES" ou também, separadamente, cada uma como "PARTE".</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>Considerando que o SEGURADO e a SEGURADORA negociaram e esta última emitiu em favor do SEGURADO a Apólice de Seguro nº 06200'.$Num_Apolice.' através da qual é garantida a cobertura aos riscos de não pagamento de <b>dívidas</b> (conforme definição prevista na Apólice de Seguro) dos compradores do SEGURADO.</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>Considerando que o SEGURADO possui com o BANCO um acordo de financiamento - seja através do Programa de Financiamento às Exportações ("PROEX"), Adiantamento de Cambiais Entregues ("ACE") ou Desconto de Cambiais de Exportação - que envolvam as faturas comerciais/embarques cobertos pela referida Apólice de Seguro (doravante denominado "Cessão de Direitos ao Crédito"). </p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>E considerando que o SEGURADO através deste instrumento deseja transferir ao BANCO seu direito ao recebimento de eventuais indenizações devidas pela SEGURADORA decorrentes do não pagamento das <b>dívidas</b> cobertas pela Apólice de Seguro.</p>
              </div>
          </div>  
            ';

            //$html .= '<pagebreak />';

            $dataCessao = date('d/m/y');

            $html .= '
              <div>&nbsp;</div>
                <div id="cobtexto" style="font-size: 10pt;"> 
                  <p>Diante disso, as PARTES através desta Condição Especial resolvem suplementar a Apólice de Seguro, com as condições abaixo descritas:</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                  <p><b>  Consoante com o previsto na Cláusula 11º das Condições Gerais da Apólice de Seguro, as Partes concordam com o seguinte:</b></p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">1.  O SEGURADO através deste documento cede ao BANCO seus direitos ao recebimento de eventuais indenizações devidas pela SEGURADORA ao SEGURADO decorrentes do não pagamento de <b>dívidas</b> cobertas pela <b>Apólice de Seguro</b>.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">2.  A SEGURADORA através deste documento reconhece a cessão dos direitos mencionados na Cláusula acima e se compromete a pagar ao BANCO as indenizações devidas ao Segurado decorrentes do não pagamento de <b>dívidas</b> cobertas pela <b>Apólice de Seguro</b>, de acordo com os seus termos e condições, sujeito a oposição de direitos de terceiros.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">3.  O BANCO através deste documento reconhece a presente cessão de direitos e declara ter conhecimento e aceitar os termos e condições da <b>Apólice de Seguro</b>. </p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">  O BANCO reconhece e concorda que seu direito é estrito ao recebimento das indenizações devidas pela SEGURADORA nos termos da <b>Apólice de Seguro</b> 06200'.$Num_Apolice.' e que a SEGURADORA poderá opor ao BANCO qualquer exceção a tais direitos ou defesa que lhe caiba no âmbito da <b>Apólice de Seguro</b>. Nesta medida, o BANCO não possuirá quaisquer direitos sobre a <b>Apólice de Seguro</b> além do direito ao recebimento de indenizações decorrentes do não pagamento de <b>dívidas</b> pela Apólice de Seguro, como beneficiário.</p>
                </div>

                    <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">  Na hipótese de <b>insolvência</b> do SEGURADO, legalmente reconhecida pelas Leis do seu país, caso o BANCO deseje assumir todos os direitos e obrigações para se tornar o SEGURADO da Apólice de Seguro, o BANCO tem 10 dias úteis contados da data da insolvência para enviar a SEGURADORA, por escrito, sua confirmação sobre a intenção de exercer esse direito.  A SEGURADORA irá recusar ou confirmar, se for o caso indicando as condições aplicáveis, no prazo de 10 dias úteis contados da data da notificação do BANCO.</p>
                </div>

                    <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">4.    O SEGURADO e o BANCO se comprometem a informar a SEGURADORA o recebimento – por quaisquer das partes, de quaisquer <b>Recuperações</b>.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">5.  O SEGURADO reconhece que a cessão dos seus direitos a indenizações bem como quaisquer outras obrigações decorrentes desta Condição Especial não o isentam do cumprimento de todas e quaisquer obrigações estipuladas pela <b>Apólice de Seguro</b>.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">6.  A SEGURADORA está autorizada a divulgar as informações do SEGURADO, informações sobre quaisquer <b>dívidas</b> cobertas, sobre a <b>Apólice de Seguro</b> e ainda sobre esta Condição Especial para a sua matriz, qualquer das suas subsidiárias ou subsidiárias de sua empresa holding, afiliadas, representantes e sucursais em qualquer jurisdição, como também para o BANCO (denominadas juntamente com as partes correspondentes, as "Partes Permitidas").</p>
                </div>
                <br>
                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">7.  Conforme solicitado pela SEGURADORA, o BANCO concorda em fornecer diretamente (ou providenciar o envio) da documentação necessária para que a SEGURADORA realize e cumpra com todas as verificações necessárias para o procedimento "Know Your Customer", de acordo com todas as Leis e regulamentos aplicáveis.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">8.  Esta Condição Especial é aplicável a todas as indenizações a serem pagas pela SEGURADORA - referentes a embarques realizados dentro da vigência da <b>Apólice de Seguro</b> - em um prazo de cinco dias após a data da sua assinatura.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">9.  Esta Condição Especial deverá ser regida e interpretada de acordo com as leis brasileiras. As Partes contratantes elegem o Foro da Comarca de São Paulo, como o competente para dirimir quaisquer dúvidas decorrentes desta Condição Especial, com renúncia a qualquer outro, por mais privilegiado que seja.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">10. Esta Condição Especial terá eficácia a partir da data de sua assinatura até o momento em que uma das condições abaixo ocorrer:</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 0px;"> 
                  <p style="margin-left: 30px;">(i) cancelamento da Condição Especial por consentimento mútuo das Partes formalizado por escrito;</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt;"> 
                  <p style="margin-left: 30px;">ou</p>
                  <p style="margin-left: 30px;">(ii) término ou rescisão da Apólice de Seguro.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">Qualquer alteração realizada à esta Condição Especial será feita através de endosso e deverá ser assinada pelas Partes.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">Emitido em '.$dataCessao.', em 3 (três) vias de igual valor para que produza seus efeitos jurídicos.</p>
                </div>
              </div>
            ';


            $html .= '<br>
              <br>
              
              <br>
              <br>
              <br>
              <br>
              <br>
              
              <table width="100%" border="0" cellpadding="1">
                <tr>
                  <td style="text-align:center">_________________________________</td>
                  <td style="text-align:center"> __________________________________</td>
                </tr>
                <tr>
                  <td style="text-align:center">SEGURADO</td>
                  <td style="text-align:center">BANCO DO BRASIL</td>
                </tr>
                
              </table>
              
              <br>
              <br>
              <br>
              <br>
              <br>
              <div style="text-align:center">______________________________________________________________
                <br>COFACE DO BRASIL SEGUROS DE CRÉDITO S/A<br>
              </div>
            
              <br>
              <br>
              <br>
              <br>

              <table width="100%" border="0">
                  <tr>
                  <td width="30%">&nbsp;</td>
                  <td align="right" width="70%">
                      
                    </td> 
                  </tr>
                  </table>  
            </body>
          </html>';

 
        
        $html = utf8_encode($html);
        
        $mpdf->allow_charset_conversion=true;
        $mpdf->charset_in='UTF-8';
        $mpdf->WriteHTML($html);
        $mpdf->Output();
   
?>