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
      $mpdf->SetWatermarkText("RASCUNHO"); // fundo marca d�gua
    } else {
      $mpdf->SetWatermarkText(""); // fundo marca d�gua
    }
    
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->SetDisplayMode('fullpage');

    // Endere�o do logotipo
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
                    <span style="font-weight: bold; font-size: 12pt;">ENDOSSO PARA DESIGNA��O DE BENEFICI�RIO n� '.$Cod_Clausula.' - CONDI��ES ESPECIAIS VINCULADAS � AP�LICE N.� 06200'.$Num_Apolice.' 
                    </span>
                    <span style="font-weight: bold; font-size: 12pt;"></span>
                    <br>
                    <br>
                    <br>

                    <span style="font-weight: bold; font-size: 10pt;">
                      <h4><b>Esta Condi��o Especial para Designa��o de Benefici�rio (doravante referida como "Condi��o Especial") � celebrada entre:</b>
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
                            P�gina {PAGENO} de {nb}
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
                <p><b>COFACE DO BRASIL SEGUROS DE CR�DITO S/A</b>, uma empresa registrada sob as leis do Brasil, com sede social na cidade e Estado de S�o Paulo, � '.$Endereco_Empresa.' / '.$Complemento_Empresa.' - Brooklin Novo inscrita no CNPJ/MF sob o n� '.formata_string('CNPJ', $CPNJ_Empresa).', neste ato devidamente representada de acordo com o seu Estatuto Social (doravante denominada, simplesmente, "SEGURADORA");</p>
              </div>
              
              <div style="margin-top: 25px;">
                <p style="font-size: 10pt;">e</p>
              </div>
              
              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p><b>'. strtoupper(trim($Nome_Banco)).' - '.$Nome_Agencia.'</b>, uma institui��o financeira registrada sob as leis do Brasil, inscrita no CNPJ sob n� '.formata_string('CNPJ', $CNPJ_Agencia).', com sede na '.$Endereco_Agencia.' neste ato representada na forma de seu Estatuto Social/Contrato Social (doravante denominada, simplesmente, "BANCO")</p>
              </div>
              
              <div style="margin-top: 25px;">
                <p style="font-size: 10pt;">e</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p><b>'.strtoupper(trim($Nome_Segurado)).'</b>, uma empresa registrada sob as leis do Brasil, inscrita no CNPJ sob n� '.formata_string('CNPJ', $CNPJ_Segurado).', com sede na '.$Endereco_Segurado.' neste ato representada na forma de seu Estatuto Social/Contrato Social (doravante denominada, simplesmente, "SEGURADO")</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>SEGURADORA, BANCO E SEGURADO ser�o doravante denominadas, em conjunto, como "PARTES" ou tamb�m, separadamente, cada uma como "PARTE".</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>Considerando que o SEGURADO e a SEGURADORA negociaram e esta �ltima emitiu em favor do SEGURADO a Ap�lice de Seguro n� 06200'.$Num_Apolice.' atrav�s da qual � garantida a cobertura aos riscos de n�o pagamento de <b>d�vidas</b> (conforme defini��o prevista na Ap�lice de Seguro) dos compradores do SEGURADO.</p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>Considerando que o SEGURADO possui com o BANCO um acordo de financiamento - seja atrav�s do Programa de Financiamento �s Exporta��es ("PROEX"), Adiantamento de Cambiais Entregues ("ACE") ou Desconto de Cambiais de Exporta��o - que envolvam as faturas comerciais/embarques cobertos pela referida Ap�lice de Seguro (doravante denominado "Cess�o de Direitos ao Cr�dito"). </p>
              </div>

              <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                <p>E considerando que o SEGURADO atrav�s deste instrumento deseja transferir ao BANCO seu direito ao recebimento de eventuais indeniza��es devidas pela SEGURADORA decorrentes do n�o pagamento das <b>d�vidas</b> cobertas pela Ap�lice de Seguro.</p>
              </div>
          </div>  
            ';

            //$html .= '<pagebreak />';

            $dataCessao = date('d/m/y');

            $html .= '
              <div>&nbsp;</div>
                <div id="cobtexto" style="font-size: 10pt;"> 
                  <p>Diante disso, as PARTES atrav�s desta Condi��o Especial resolvem suplementar a Ap�lice de Seguro, com as condi��es abaixo descritas:</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 25px;"> 
                  <p><b>  Consoante com o previsto na Cl�usula 11� das Condi��es Gerais da Ap�lice de Seguro, as Partes concordam com o seguinte:</b></p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">1.  O SEGURADO atrav�s deste documento cede ao BANCO seus direitos ao recebimento de eventuais indeniza��es devidas pela SEGURADORA ao SEGURADO decorrentes do n�o pagamento de <b>d�vidas</b> cobertas pela <b>Ap�lice de Seguro</b>.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">2.  A SEGURADORA atrav�s deste documento reconhece a cess�o dos direitos mencionados na Cl�usula acima e se compromete a pagar ao BANCO as indeniza��es devidas ao Segurado decorrentes do n�o pagamento de <b>d�vidas</b> cobertas pela <b>Ap�lice de Seguro</b>, de acordo com os seus termos e condi��es, sujeito a oposi��o de direitos de terceiros.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">3.  O BANCO atrav�s deste documento reconhece a presente cess�o de direitos e declara ter conhecimento e aceitar os termos e condi��es da <b>Ap�lice de Seguro</b>. </p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">  O BANCO reconhece e concorda que seu direito � estrito ao recebimento das indeniza��es devidas pela SEGURADORA nos termos da <b>Ap�lice de Seguro</b> 06200'.$Num_Apolice.' e que a SEGURADORA poder� opor ao BANCO qualquer exce��o a tais direitos ou defesa que lhe caiba no �mbito da <b>Ap�lice de Seguro</b>. Nesta medida, o BANCO n�o possuir� quaisquer direitos sobre a <b>Ap�lice de Seguro</b> al�m do direito ao recebimento de indeniza��es decorrentes do n�o pagamento de <b>d�vidas</b> pela Ap�lice de Seguro, como benefici�rio.</p>
                </div>

                    <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">  Na hip�tese de <b>insolv�ncia</b> do SEGURADO, legalmente reconhecida pelas Leis do seu pa�s, caso o BANCO deseje assumir todos os direitos e obriga��es para se tornar o SEGURADO da Ap�lice de Seguro, o BANCO tem 10 dias �teis contados da data da insolv�ncia para enviar a SEGURADORA, por escrito, sua confirma��o sobre a inten��o de exercer esse direito.  A SEGURADORA ir� recusar ou confirmar, se for o caso indicando as condi��es aplic�veis, no prazo de 10 dias �teis contados da data da notifica��o do BANCO.</p>
                </div>

                    <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">4.    O SEGURADO e o BANCO se comprometem a informar a SEGURADORA o recebimento � por quaisquer das partes, de quaisquer <b>Recupera��es</b>.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">5.  O SEGURADO reconhece que a cess�o dos seus direitos a indeniza��es bem como quaisquer outras obriga��es decorrentes desta Condi��o Especial n�o o isentam do cumprimento de todas e quaisquer obriga��es estipuladas pela <b>Ap�lice de Seguro</b>.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">6.  A SEGURADORA est� autorizada a divulgar as informa��es do SEGURADO, informa��es sobre quaisquer <b>d�vidas</b> cobertas, sobre a <b>Ap�lice de Seguro</b> e ainda sobre esta Condi��o Especial para a sua matriz, qualquer das suas subsidi�rias ou subsidi�rias de sua empresa holding, afiliadas, representantes e sucursais em qualquer jurisdi��o, como tamb�m para o BANCO (denominadas juntamente com as partes correspondentes, as "Partes Permitidas").</p>
                </div>
                <br>
                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">7.  Conforme solicitado pela SEGURADORA, o BANCO concorda em fornecer diretamente (ou providenciar o envio) da documenta��o necess�ria para que a SEGURADORA realize e cumpra com todas as verifica��es necess�rias para o procedimento "Know Your Customer", de acordo com todas as Leis e regulamentos aplic�veis.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">8.  Esta Condi��o Especial � aplic�vel a todas as indeniza��es a serem pagas pela SEGURADORA - referentes a embarques realizados dentro da vig�ncia da <b>Ap�lice de Seguro</b> - em um prazo de cinco dias ap�s a data da sua assinatura.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">9.  Esta Condi��o Especial dever� ser regida e interpretada de acordo com as leis brasileiras. As Partes contratantes elegem o Foro da Comarca de S�o Paulo, como o competente para dirimir quaisquer d�vidas decorrentes desta Condi��o Especial, com ren�ncia a qualquer outro, por mais privilegiado que seja.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">10. Esta Condi��o Especial ter� efic�cia a partir da data de sua assinatura at� o momento em que uma das condi��es abaixo ocorrer:</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 0px;"> 
                  <p style="margin-left: 30px;">(i) cancelamento da Condi��o Especial por consentimento m�tuo das Partes formalizado por escrito;</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt;"> 
                  <p style="margin-left: 30px;">ou</p>
                  <p style="margin-left: 30px;">(ii) t�rmino ou rescis�o da Ap�lice de Seguro.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">Qualquer altera��o realizada � esta Condi��o Especial ser� feita atrav�s de endosso e dever� ser assinada pelas Partes.</p>
                </div>

                <div id="cobtexto" style="font-size: 10pt; margin-top: 20px;"> 
                  <p style="margin-left: 30px;">Emitido em '.$dataCessao.', em 3 (tr�s) vias de igual valor para que produza seus efeitos jur�dicos.</p>
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
                <br>COFACE DO BRASIL SEGUROS DE CR�DITO S/A<br>
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