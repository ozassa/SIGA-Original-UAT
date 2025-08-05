<?php 
        
  $sql = "Select mModulos, pLucro, perBonus, perPart0, perPart1, Ga,p_Comissao, mPart from Inform where id = '".$idInform."'";
  $cur = odbc_exec($db, $sql);
  if (odbc_fetch_row ($cur)) {
    $mBonus    =  odbc_result($cur, 1);
		$mPart     =  odbc_result($cur, 'mPart');
    $pLucro    =  odbc_result($cur, 2);
    $perBonus  =  odbc_result($cur, 3);
    $perPart0  =  odbc_result($cur, 4);
    $perPart1  =  odbc_result($cur, 5);
    $ga        = odbc_Result($cur, 6);
    $percComissao = number_format(odbc_Result($cur, 'p_Comissao'),2,',','.');
  }

  $part = '';
  $txtParcs = '';

  $sql_i = "Select nas, tipoDve, currency, Num_Parcelas, t_Vencimento from Inform where id = ".$idInform;
	$cur_i = odbc_exec($db, $sql_i);
	if (odbc_fetch_row ($cur_i)) {
	  $nas    =  odbc_result($cur_i, 'nas');
	}	  

	$Num_Parcelas  =  (odbc_result($cur_i, 'Num_Parcelas') ? odbc_result($cur_i, 'Num_Parcelas') : '0');

  $t_Venc  =  odbc_result($cur_i, 't_Vencimento');
  
  $t_Vencimento = '';
  if($t_Venc == 1){
	  $t_Vencimento = 'mensais';
  }else if ($t_Venc == 2){
	  $t_Vencimento = 'bimestrais';
  }else if ($t_Venc == 3){
	  $t_Vencimento = 'trimestrais';
  }else if ($t_Venc == 4){
	  $t_Vencimento = 'semestrais';
  }

	$tipdve = odbc_result($cur_i, 'tipoDve');

  if ($tipdve == "1") {
       $tipodve = "m�s";
       $periodo = "mensal";
  }elseif($tipdve == "2") {
       $tipodve = "trimestre";
       $periodo = "trimestral";
	}elseif($tipdve == "3") {
       $tipodve = "ano";
       $periodo = "anual";
  }else{
       $tipodve = "m�s";
       $periodo = "mensal";
  }

	$currency = odbc_result($cur_i, 'currency');

	if ($currency == "1") {
		$extM = "Real";
	    $mo = "R$";
	    $sifra = "R$";
	}else if ($currency == "2") {
	      $extM = "d�lar norte-americano";
	      $mo = "USD ";
	      $sifra = "US$";
	}elseif ($currency == "6") {
	      $extM = "Euro";
	      $mo = "� ";
	      $sifra = "�";
	}elseif ($currency == "0") {
	      $extMoeda = "d�lar norte-americano";
	      $mo = "US$ ";
	      $sifra = "US$";
	}

	$extnas = $mo." ".number_format($nas, 2, ',','.')." (".strtolower($numberExtensive->extensive(number_format($nas,2, '.',''),$currency)).')';


	$sql = "SELECT a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602, p_cobertura_d701, 
	 								limite_d701, ic_nao_aplica_taxa, adequacao_sinistralidade, adequacao_premio, franquia_anual,
	 								CONVERT(varchar(1000), condicoes_especiais) AS condicoes_especiais, PrazoMaxEmiNota, b904, b2604Perc, b2604NivelMax, 
	 								b2801NivelMax, b2801Perc, d201, f401NivelSinistralidade, f401PercPremio, b802, b404NivelMax, b404Perc, GerenteCredito, 
	 								Texto_Risco_Politico		 
	 				FROM ModuloOferta WHERE idInform = $idInform";
	$cur4=odbc_exec($db,$sql);
	 
  // Defini�ao dos modulos
  $mod_a801 = odbc_result($cur4,1);
  $mod_a502 = odbc_result($cur4,2);
  $mod_b603 = odbc_result($cur4,3);
  $mod_b1504 = odbc_result($cur4,4);
  $mod_b1202 = odbc_result($cur4,5);
  $mod_c102 = odbc_result($cur4,6);
  $mod_d101 = odbc_result($cur4,7);
  $mod_d602 = odbc_result($cur4,8);
  $mod_d701 = odbc_result($cur4,9);
  $mod_e101 = odbc_result($cur4,10);
  $mod_f305 = odbc_result($cur4,11);
  $mod_f3301 = odbc_result($cur4,12);
	  
  $GerenteNome    = odbc_result($cur4,'GerenteCredito');

  $ad_sinistr = odbc_result($cur4,'adequacao_sinistralidade');
  $ad_premio = odbc_result($cur4,'adequacao_premio');
  
  
  $PrazoMaxEmiNota           = odbc_result($cur4,'PrazoMaxEmiNota');
  $mod_b904                  = number_format(odbc_result($cur4,'b904'),2,',','.'); 
  $mod_b904Ext               = $numberExtensive->extensive(number_format(odbc_result($cur4,'b904'),2,'.',''),$currency);
  $mod_b2604                 = number_format(odbc_result($cur4,'b2604Perc'),2,',','.');
  $b2604NivelMax             = number_format(odbc_result($cur4,'b2604NivelMax'),2,',','.');
  $b2604NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($cur4,'b2604NivelMax'),2,'.',''),$currency);
  
  $b2801NivelMax             = number_format(odbc_result($cur4,'b2801NivelMax'),2,',','.');
  $b2801NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($cur4,'b2801NivelMax'),2,'.',''),$currency);
  $b2801Perc                 = number_format(odbc_result($cur4,'b2801Perc'),2,',','.');
  
  $b404NivelMax              = number_format(odbc_result($cur4,'b404NivelMax'),2,',','.');
  $b404NivelMaxExt           = $numberExtensive->extensive(number_format(odbc_result($cur4,'b404NivelMax'),2,'.',''),$currency);
  $mod_b404                  = number_format(odbc_result($cur4,'b404Perc'),2,',','.');
  $riscopolitico             = odbc_result($cur4,'Texto_Risco_Politico');
  
  $d201                      = number_format(odbc_result($cur4,'d201'),2,',','.');
  $valorExtD201              = $numberExtensive->extensive(number_format(odbc_result($cur4,'d201'),2,'.',''),$currency);
  $f401NivelSinistralidade   = number_format(odbc_result($cur4,'f401NivelSinistralidade'),2,',','.');
  $f401PercPremio            = number_format(odbc_result($cur4,'f401PercPremio'),2,',','.');
  $mod_b802                  = number_format(odbc_result($cur4,'b802'),2,',','.');
	  
	  
  if ($ad_sinistr > 0 || $ad_premio > 0){
       $ad_sinistr = number_format($ad_sinistr,2,',','.');
       $ad_premio = number_format($ad_premio,2,',','.');
       $exibe_ad = 1;

  }else {
       $ad_sinistr = 0;
       $ad_premio = 0;
       $exibe_ad = 0;
  }

  $franquia_anual = odbc_result($cur4,'franquia_anual');

  if ($franquia_anual > 0){
      $franquia_anual = number_format(odbc_result($cur4,'franquia_anual'),2,',','.') ." (".strtolower($numberExtensive->extensive(number_format(odbc_result($cur4,'franquia_anual'),2,'.',''),$currency)).").";
      $exibe_franq = 1;

  }else{
      $franquia_anual = 0;
      $exibe_franq = 0;
  }

  if (odbc_result($cur4,'condicoes_especiais') != "" ){
     $condicoes_especiais = odbc_result($cur4,'condicoes_especiais') ;
     $exibe_cond = 1;
  }else{
     $condicoes_especiais = "";
     $exibe_cond = 0;
  }

  if(!isset($modulo_final)){
  	$modulo_final = '';
  }

  if(odbc_result($cur4, 6) == "1") {
	$modulo_final .= "C1.02 Servi�o de cobran�a integral\n\n";
  }
  if(odbc_result($cur4, 7) == "1") {
	$modulo_final .= "D1.01 Limite m�nimo para notifica��o de amea�a de sinistro\n\n";
  }
	  
	 if(odbc_result($cur4, 8) == "1") {
	   //$d602  = "|D6.02 PEQUENOS SINISTROS| ";
	   $nivel_d602 = number_format(odbc_result($cur4, 13),2,'.','');
	   $nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$currency));
	   $d602 = "O n�vel de pequenos sinistros � de: ".$sifra." ".formata($nivel_d602)." (".$nivel.")";
	 }

   if(odbc_result($cur4, 9) == "1") {
      if($d602=="") {
         $d701  = "\n\n|D7.01 LIT�GIO|";
      } else {
         $d701  = "\n\n|D7.01 LIT�GIO|";
      }
     $p_cobertura_d701  = odbc_result($cur4, 14);
     $limite_d701       = odbc_result($cur4, 15);
     //$d701 = $d701."\n\ni) Percentual de Cobertura: ".$p_cobertura_d701."% \nii) Limite de pagamento por Lit�gio:".$limite_d701."%\n";
     $d701 = "O percentual de cobertura � de: ".$p_cobertura_d701."% <br>".$limite_d701."";
	}    
   
  	$extpart0 = '';
  	$extpart1 = '';
	if ($mPart == "1") {
	  if ($pLucro == "F13") {
	     $partic  = "F13.02    PARTICIPA��O NOS LUCROS";
	  }else if ($pLucro == "F14") {
	     $partic  = "F14.02    PARTICIPA��O NOS LUCROS";
	  }else if ($pLucro == "F15") {
	     $partic  = "F15.02    PARTICIPA��O NOS LUCROS";
	  }

	  $extpart0   = $numberExtensive->porcentagem(number_format($perPart0,2,'.',''));
	  $extpart1   = $numberExtensive->porcentagem(number_format($perPart1,2,'.',''));
	  $part .= "Percentagem de Dedu��o: ".$perPart0."% (".$extpart0.")\n".
	           "Participa��o nos Lucros: ".$perPart1."% (".$extpart1.")\n";
	  $valbo ="1";
	 
	}

  if($Periodo_Vigencia){
		$pvigencia = $Periodo_Vigencia;
	}else{
		$pvigencia = ($pvigencia == "" || $pvigencia == 1 ? 12: 24);
		
	}
	
	if($t_Vencimento || $Num_Parcelas){
		  if ($numParc == 1){
			  $txtParcs .= "� vista.";
			  $periodo1 = "� vista";
		  }else{
			  $txtParcs .= $Num_Parcelas." presta��es iguais e ".$t_Vencimento;
			  //$periodo = "trimestral";
			  $periodo1 = $t_Vencimento;
		  }
	}else if ($pvigencia <=12) {
			$Periodo_Vigencia   = $pvigencia;
			if ($numParc == 1){
			  $txtParcs .= "� vista.";
			  //$periodo = "� vista";
			  $periodo1 = "� vista";
			}else if ($numParc == 2){
			  $txtParcs .= "duas presta��es iguais: 1 e mais 1 em noventa dias.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 4){
			  $txtParcs .= "quatro presta��es iguais: 1 e mais 3 trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 7){
			  $txtParcs .= "sete presta��es iguais mensais.";
			  //$periodo = "mensal";
			  $periodo1 = "mensais";
			}
		
	 }elseif ($pvigencia > 12){	
			if ($numParc == 1){
			  $txtParcs .= "� vista.";
			  //$periodo = "� vista";
			  $periodo1 = "� vista";
			}else if ($numParc == 4){
			  $txtParcs .= "quatro presta��es iguais: 1 e mais 3 trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 7){
			  $txtParcs .= "sete presta��es iguais mensais.";
			  //$periodo = "mensal";
			  $periodo1 = "mensais";
			}else if ($numParc == 8){
			  $txtParcs .= "oito presta��es iguais trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}
	 }
	
	  $txMonitor = $field->getNumField("txMonitor");
	  $txAnalise = $field->getNumField("txAnalize");


		$taxa_analise = number_format($txAnalise,2,",",".");
		$extAnalise = $numberExtensive->extensive($txAnalise, 1);
		$extMonit = $numberExtensive->extensive($txMonitor, 1);
		$taxa_monit = number_format($txMonitor,2,",",".");
		$taxa_monitoramento = $taxa_monit;



  	$bonus = "";
        // ALTERA��O B�NUS
     if ($mBonus == "1") {
        $bonus = "A percentagem referente ao item 2 deste m�dulo � de ". $perBonus."%\n\n"; //10%(dez por cento).
     }

        //if ($mBonus == "1" Or $mBonus == "2") {
            $modulos = "1";
        //}
          $msg11d = "";

				$qryM = "select a.i_Modulo, b.Cod_Modulo,b.Grupo_Modulo,b.Titulo_Modulo, 
					cast(a.Desc_Modulo as nvarchar(3900)) as Desc_Modulo
					from Inform_Modulo a inner join Modulo b on a.i_Modulo = b.i_Modulo 
					where a.idInform = ". $idInform. " order by b.Ordem_Modulo";						
		   $mod = odbc_exec($db,$qryM);



		   $GrupoModulo = "";

		   $html_mod = "<table class='tbl_mods'>";


						
				while(odbc_fetch_row($mod)){ 
					$descricao_Modulo = odbc_result($mod,'Desc_Modulo');
					//print $descricao_Modulo;
							   
					if(odbc_result($mod,'Grupo_Modulo') != $GrupoModulo){
						$GrupoModulo  = odbc_result($mod,'Grupo_Modulo');

						$html_mod .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">'.odbc_result($mod,'Grupo_Modulo').'</div></td>
							</tr>';
					}
								   
					$Titulo = strlen(odbc_result($mod,'Titulo_Modulo'));

					$html_mod .= '<tr>
						<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="1" width="8%" style="font-weight:bold">'.odbc_result($mod,'Cod_Modulo').'</td>
						<td  colspan="1" width="76%" style="font-weight:bold">'.odbc_result($mod,'Titulo_Modulo').'</td>						   			
					</tr>';

					if(odbc_result($mod,'Cod_Modulo') == "B4.04"){
						$html_mod .= '									  									  
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">
								- O valor de limite de cr�dito m�ximo referente da cl�usula 1 deste m�dulo � de '.$mo.' '.$b404NivelMax.' ('.$b404NivelMaxExt.' ' . strtolower($ext) .').<br>
								- A percentagem segurada para estes compradores � de '.$mod_b404.'% (ICMS, IPI e ISS inclu�dos).</td>
							</tr>

							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">
								O nome do Gerente de cr�dito � <strong>'.$GerenteNome.'.</strong><br>
                                                		O procedimento de gerenciamento de cr�dito est� anexado ao presente contrato.
							</tr>';					
					}else if(odbc_result($mod,'Cod_Modulo') == "B12.02"){
						$query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = '".$idInform."' ORDER BY no_razao_social ";
            $cury = odbc_exec ($db, $query);
										  
						if ($cury){ 
							$html_mod .= '								   
								<tr>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="2" width="84%">											   
										<table width="100%" border="0" style="font-size: 12pt;"> ';	 
											$i = 0;
											$empre = '';  
											while (odbc_fetch_row($cury)){				
												$no_razao_social       = odbc_result ($cury, 'no_razao_social');
												$nu_cnpj               = odbc_result ($cury, 'nu_cnpj');
												$nu_inscricao_estadual = odbc_result ($cury, 'nu_inscricao_estadual');
												$ds_endereco           = odbc_result ($cury, 'ds_endereco');
												$nu_endereco           = odbc_result ($cury, 'nu_endereco');
												$ds_complemento        = odbc_result ($cury, 'ds_complemento');
												$nu_cep                = odbc_result ($cury, 'nu_cep');
												$no_cidade             = odbc_result ($cury, 'no_cidade');
												$no_estado             = odbc_result ($cury, 'no_estado');
												$i ++;
												$empre .= '<tr><td colspan="2"><b>'.$no_razao_social.'</b></td></tr>
													<tr><td><b>CNPJ   : </b>'.$nu_cnpj.'</td><td><b>IE:</b> '.$nu_inscricao_estadual.'</td></tr>
													<tr><td colspan="2"><b>Endere�o: </b>'.$ds_endereco.', '.$nu_endereco. ($ds_complemento != "" ? " - ".$ds_complemento : "").'</td></tr>
													<tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td></b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
											}
													
											$html_mod .= $empre.'</table>
									</td>
								</tr>';	
						}
					}else if (odbc_result($mod,'Cod_Modulo') == "B26.04"){	
						$mod_b2604 = (int)$mod_b2604;							       
						$html_mod .= '									  									  
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%">
									- O n�vel m�ximo de cr�dito referente � cl�usula 1 deste m�dulo � de '. $mo.' '.$b2604NivelMax.' ('.$b2604NivelMaxExt.' ' . strtolower($ext) .').
									<br>
									<br>
									- A percentagem segurada referente � cl�usula 1.2 deste m�dulo � de '.$mod_b2604.'%.</td>
							</tr>';
					}else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
						$html_mod .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%">
									O n�vel m�ximo de cr�dito referente � cl�usula 1 deste m�dulo � de  '. $mo.' '.$b2801NivelMax.' ('.$b2801NivelMaxExt.' ' . strtolower($ext) .').<br/>
									As empresas de informa��es comerciais referentes � cl�usula 1.4 deste m�dulo s�o SERASA e SCI EQUIFAX.<br/>
									A percentagem segurada referente � cl�usula 1.6 deste m�dulo � de '.$b2801Perc.'% (ICMS, IPI e ISS inclu�dos).<br/><br/>
												
									Revoga-se parcialmente a reda��o da cl�usula 1.4 do mesmo m�dulo, sendo a mesma substitu�da pelo texto abaixo:<br/><br/>
												
									�1.4. Na ocasi�o da data de faturamento da mercadoria ou presta��o de servi�os, o SEGURADO dever� dispor de informa��es provenientes de Empresas de Informa��es Comerciais aprovadas pela Seguradora, atualizadas h� menos de 2 (dois) meses, que mostrem que o Comprador n�o apresenta exist�ncia de qualquer restri��o ou apontamento.�<br/><br/>
												
									Inclui-se na Cl�usula 1-�REA DE LIMITE DE CR�DITO DISCRICION�RIO, deste mesmo M�dulo o item 1.7, com a seguinte reda��o:<br></br/> 
												
									1.7 � O SEGURADO n�o poder� conceder cr�dito  a comprador que, anteriormente ao faturamento da mercadoria ou presta��o de servi�o, tenha sido objeto de recusa total, redu��o ou cancelamento de Limite de Cr�dito por parte da SEGURADORA, na vig�ncia de qualquer ap�lice emitida pela SEGURADORA a favor do SEGURADO.�
								</td>
							</tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){
						$val_extnas = str_replace(")"," ", $extnas);						
						$html_mod .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%">O limite m�nimo para <b>notifica��o</b> de <b> amea�a de sinistro � de </b>'.$val_extnas.strtolower($ext) .'). </td>
							</tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){										  
						$html_mod .= '<tr>
								   <td  colspan="1" width="8%">&nbsp;</td>
								   <td  colspan="1" width="8%">&nbsp;</td>
								   <td  colspan="2" width="84%">O valor da franquia � de '.$mo.' '. $d201 .' ('. $valorExtD201 .' ' . strtolower($ext) .')<br></td>
								 </tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
						$val_franquia_anual = str_replace(")."," ", $franquia_anual);
						 $html_mod .= '<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="84%">A franquia anual global � de '.$mo.' '. $val_franquia_anual.strtolower($ext) .'). <br></td>
						  </tr>';
							
					}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
						$nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$fMoeda));
						$d602 = "O n�vel de pequenos sinistros � de: ".$mo." ".number_format($nivel_d602,2,',','.')." (".$nivel." ". strtolower($ext).")";
						$html_mod .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%">'.$d602.'</td>
							</tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
						$html_mod .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%">'.$d701.'</td>
							</tr>';	
					}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
						if ($numParc == 1){
							$html_mod .= ' 
								<tr>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="2" width="84%">
									<div id="cobtexto">O per�odo de declara��o � '.$periodo.'<br>
										A forma de declara��o � volume total de neg�cios aberto por n�mero 
										de fatura comercial, importador e valor.<br>
										O pr�mio m�nimo ser� pago em 01 parcela � vista.</div></td>
								</tr>';
						}else{
							$html_mod .= '
								<tr>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="2" width="84%">
									<div id="cobtexto">O per�odo de declara��o � '.$periodo.'.<br>
										A forma de declara��o � volume total de neg�cios aberto por n�mero 
										de fatura comercial, importador e valor.<br>
										O pr�mio m�nimo ser� pago em '.$numParc.' parcelas iguais e '.$periodo1.'</div>
									</td>
								</tr>';
						}
					}else if(odbc_result($mod,'Cod_Modulo') == "F9.02"){
						$html_mod .= '<tr>
														<td  colspan="1" width="8%">&nbsp;</td>
														<td  colspan="1" width="8%">&nbsp;</td>
														<td  colspan="2" width="84%">'.$bonus.'</td>
													</tr>';	
					}else if(odbc_result($mod,'Cod_Modulo') == "F13.02"){
						$perPart1 = (int)$perPart1;
						$perPart0 = (int)$perPart0;
						
						$html_mod .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%">A percentagem referente ao item a deste m�dulo � de '. $perPart0 .'% ('.$extpart0.').<br>
									A percentagem referente ao item b deste m�dulo � de '. $perPart1 .'% ('.$extpart1.').</td>
							</tr>  
						';
					}else if(odbc_result($mod,'Cod_Modulo') == "F14.02"){
						$html_mod .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">A percentagem referente ao item a deste m�dulo � de '. $perPart0 .'% ('.$extpart0.').<br>
									A percentagem referente ao item b deste m�dulo � de '. $perPart1 .'% ('.$extpart1.').</td>
							</tr>  
						';
					}else if(odbc_result($mod,'Cod_Modulo') == "F15.02"){
						$html_mod .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%">A percentagem referente ao item a deste m�dulo � de '. $perPart0 .'% ('.$extpart0.').<br>
									A percentagem referente ao item b deste m�dulo � de '. $perPart1 .'% ('.$extpart1.').</td>
							</tr>  
						';
					}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
						$html_mod .= '<tr>
													<td  colspan="1" width="8%">&nbsp;</td>
													<td  colspan="1" width="8%">&nbsp;</td>
													<td  colspan="2" width="84%">';
													           
													if($taxa_analise > 0) 
														$html_mod .= 'A tarifa de an�lise cadastral � de R$ '. $taxa_analise.' ('.$extAnalise.')<br>';

													if($taxa_monitoramento > 0)
														$html_mod .= 'A tarifa de monitoramento cadastral � de R$ '. $taxa_monitoramento.' ('.$extMonit.')';

												$html_mod .= '</td>
											</tr>';
					}

					if ($descricao_Modulo != '' || odbc_result($mod,'Desc_Modulo') != ''){
						$html_mod .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify">
								'. nl2br(odbc_result($mod,'Desc_Modulo')).'
								</td>
							</tr>';
					}
				}




	  $countNumber = 10; 

	  $html_mod .= '</table>';

	  echo $html_mod; ?>