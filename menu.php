<div id="cabecalhomenu">
	<div id="cabecalhoconteudo">
    	<div id="painel">
      		<?php

          // echo debug_print_backtrace();die;
          
        		$qry = "SELECT a.id,a.name,c.login 
					FROM Role a 
                    INNER JOIN UserRole b on b.idRole = a.id 
                    INNER JOIN Users c on c.id = b.idUser
                  	WHERE c.id = '$userID'
                  	ORDER BY a.name,c.login";
                  	
        		$cur = odbc_exec($db,$qry);

            $x = 0;
        		While (odbc_fetch_row($cur)) {
          			$x = $x + 1;
          			$name  = odbc_result ($cur, 'name');
          			$id    = odbc_result ($cur, 'id');
          			$role[$name] = $id.'<br>';
        		}


        		// Perfil de acesso do Banco ao sistema
        		If ($_SESSION['pefil'] == 'B') {
          			$sql = "SELECT P.i_Perfil AS i_Perfil, ISNULL(PT.Leitura, 0) AS Possui_Acesso, T.i_Tela AS i_Tela, T.Nome_Tela AS Nome_Tela, ISNULL(T.Descricao_Tela, '') AS Descricao_Tela 
                    		FROM Users U
                      		INNER JOIN Perfil P ON P.i_Perfil = U.i_Perfil 
                      		Left JOIN Tela T ON T.t_Perfil = P.t_Perfil AND T.s_Tela = 0  -- Ativa
                      		Left JOIN Perfil_Tela PT ON PT.i_Tela = T.i_Tela AND PT.i_Perfil = P.i_Perfil 
                    		WHERE U.id = ".$_SESSION['userID']." AND T.s_Tela = 0  -- Tela ativa";
                    	
          			$rsSql = odbc_exec($db, $sql);

          			$perfil_banco = array();
          			
          			While(odbc_fetch_row($rsSql)) { 
            			$i_Tela = odbc_result($rsSql, "i_Tela") ? odbc_result($rsSql, "i_Tela") : "";
            			$Possui_Acesso = odbc_result($rsSql, "Possui_Acesso") ? odbc_result($rsSql, "Possui_Acesso") : "";

            			$perfil_banco[$i_Tela] = array(
              				"Possui_Acesso"       => $Possui_Acesso
            			);
          			}
        		}
			?>
      
      		<ul id="nav">
        		<?php if ($userID) { ?>
          			<li>
            			<a href="#" class="menu" style="z-index: 9999;" ></a><label class="lblTopMenu" style="z-index: 9997">Principal</label> 
            			<ul>
	              			<?php
	                   
	                		If(($_SESSION['pefil'] == 'B' || $_SESSION['pefil'] == 'C') && $_SESSION['id_user'] > 0){ 
	                  			$query = "SELECT IsNull(COUNT(a.id), 0) AS qtd 
	                              			FROM Inform a 
	                                		INNER JOIN Inform_Usuarios b ON b.idInform = a.id 
	                              			WHERE b.idUser = ".$userID."";
	                              	
	                  			$curz = odbc_exec($db, $query);
	
	                  			If(odbc_result($curz,'qtd') == 0){ 

	                    				If ($_SESSION['pefil'] == 'B') { ?>
	                      					<?php if ($perfil_banco[1]["Possui_Acesso"] == 1) { ?>
	                        					<li class="biMenu"><a href="<?php echo $root; ?>role/user/User.php?comm=index">Acessos</a></li>
		                      				<?php } ?>
		
	        	              				<?php 
	                	    			} else if ($_SESSION['pefil'] == 'C') { ?>
	                      					<li class="biMenu"><a href="<?php echo $root; ?>role/inform/Inform.php?comm=cliente_acessos">Acessos</a></li>
	                      					<?php
	                    				}
	                  			} 
	                		} 

	                		if(check_menu(['bancoBB', 'bancoOB', 'bancoParc'], $role)){ ?>
	                  			<li class="biMenu"><a href="../access/Access.php?comm=openBanco">Notifica&ccedil;&otilde;es</a></li>
	                  			<?php 
	                		} else { ?> 
	                  			<li class="biMenu"><a href="../access/Access.php">Notifica&ccedil;&otilde;es</a></li>
	                  			<?php
	                		} 


                      $cfg = ["executive","executiveLow","regionalManager","credit","creditManager","creditInform","tariffer","policy","financ","backoffice","sinistro","endosso","cessao","viewCredit"];


	                		if (check_menu($cfg, $role)) { ?>
	                  			<li class="biMenu"><a href="../searchClient/SearchClient.php">Pesquisa Clientes</a></li>                      
	                  			<!-- <li class="biMenu"><a href="../credit/Credit.php?comm=fechamentoMensal">Fechamento</a></li> -->
	                  			<!-- <li class="biMenu"><a href="../credit/Credit.php?comm=statistics">Estat&iacute;sticas</a></li> -->
	                  			<?php 
	                		} 
	
	                		If (check_menu(['policy', 'financ'], $role)){ ?>
	                  			<!-- <li class="biMenu"><a href="../financ/Financ.php?comm=Esusep">Exporta&ccedil;&atilde;o SUSEP</a></li> -->
	                  			<?php 
	                		}
	
					If(check_menu(['policy'], $role)){ ?>
                  				<li class="biMenu"><a href="../policy/Policy.php?comm=allPolicies&menu=1">Ap&oacute;lices emitidas</a></li> 
	                  			<?php 
	                		} 

	                		If(check_menu(['regionalManager'], $role)){ ?>
	                  			<li class="biMenu"><a href="../regionalManager/RegionalManager.php?comm=prod">Produ&ccedil;&atilde;o</a></li>
	                  			<?php 
	                		} 
	
	                		If ($_SESSION['pefil'] == 'B') { ?>
	                  			<?php if ($perfil_banco[2]["Possui_Acesso"] == 1) { ?>
	                    			<li class="biMenu"><a href="../agency/Agency.php">Rela&ccedil;&atilde;o de Ag&ecirc;ncias</a></li>
	                  			<?php } ?>
	                       
                          <?php $pa3 = isset($perfil_banco[3]["Possui_Acesso"]) ? $perfil_banco[3]["Possui_Acesso"] : 0; ?>
	                  			<?php if ($pa3 == 1) { ?>
	                    			<li class="biMenu"><a href="../report/Report.php">Relat&oacute;rios</a></li>
	                  			<?php } ?>
	                       
                          <?php $pa9 = isset($perfil_banco[9]["Possui_Acesso"]) ? $perfil_banco[9]["Possui_Acesso"] : 0; ?>
	                  			<?php if ($pa9 == 1) { ?>
	                    			<li class="biMenu"><a href="../accessProfile/AccessProfile.php">Perfis de Acesso</a></li>
	                  			<?php } ?>
	                  			
	                  			<?php 
	                		} ?>
            			</ul>
          			</li>

          <?php if(check_menu(['bancoBB', 'bancoOB', 'bancoParc', 'Declara_DVN'], $role)) { ?>
            <li>
              <?php if (check_menu(['Declara_DVN'], $role)) { ?>     
                <a href="#" class="menu" style="z-index: 9999;" ></a><label class="lblTopMenu" style="z-index: 9997">Declara&ccedil;&atilde;o DVN</label> 
              <?php } else { ?>
                <a href="#" class="menu" style="z-index: 9999;" ></a><label class="lblTopMenu" style="z-index: 9997">Declara&ccedil;&atilde;o DVE</label> 
              <?php } ?>

              <ul>
                <?php if (check_menu(['Declara_DVN'], $role)) {   ?>
                  <li class="biMenu"><a href="../dve/Dve.php?comm=consultadve">Consulta DVN</a></li>
                  <li style="display:none" class="biMenu"><a href="../dve/Dve.php?comm=consultaDveEmitidaBanco">Hist&oacute;rico Doc DVN</a></li>
                <?php } elseif (check_menu(['bancoBB', 'bancoOB', 'bancoParc'], $role)){   ?>  

                  <?php $pb4 = isset($perfil_banco[4]["Possui_Acesso"]) ? $perfil_banco[4]["Possui_Acesso"] == 1 : 0; ?>
                  <?php if ($pb4 == 1) { ?>
                    <li class="biMenu"><a href="../dve/Dve.php?comm=DVEConsulta">Consulta DVE</a></li>
                  <?php } ?>

                <?php } ?>

                <?php if (check_menu(['Declara_DVN'], $role)) {  ?>
                  <li class="biMenu"><a href="../dve/Dve.php?comm=libera_dve">Liberar DVN</a></li>
                <?php } ?>

                <?php if (check_menu(['dve'], $role)) {  ?>
                  <!-- <li class="biMenu"><a href="../dve/dve.php?comm=consultaPa">Parcela de Ajuste</a></li> -->
                <?php } ?>
                
                <?php if ($_SESSION['pefil'] == 'B') { ?>

                  <?php if ($perfil_banco[5]["Possui_Acesso"] == 1) { ?>
                    <li class="biMenu"><a href="../dve/Dve.php?comm=DVELiquidacao">Conclus&atilde;o da Liquida&ccedil;&atilde;o de DVE</a></li>
                  <?php } ?>

                <?php } ?>
                
                <?php if ($_SESSION['pefil'] == 'C') { ?>
                  <li class="biMenu"><a href="../dve/Dve.php?comm=DVELiquidacao">Conclus&atilde;o da Liquida&ccedil;&atilde;o de DVE</a></li>
                <?php } ?>
              </ul>
            </li>
          <?php } ?>

        <?php /* ?>


          <?php if(($role["financ"] || $role["Altera_DPP"] || $role["backoffice"]) || ($role["policy"] || $role["backoffice"]) || ($role["regionalManager"]) || ($role["policy"]) || ($role["credit"]) || ($role["creditManager"]) || ($role["creditInform"]) || ($role["Emissao_Apolice"]) || ($role["Aceita_Proposta"])){ ?> 
            <li>
              <a href="#" class="menu" style="z-index: 9999;" ></a><label class="lblTopMenu" style="z-index: 9997">Propostas e Ap&oacute;lices</label> 
              
              <ul>
                <?php  if ($role["financ"] || $role["backoffice"]) {   ?>
                  <!-- <li class="biMenu"><a href="../financ/Financ.php?comm=view">Prop. Emitidas</a></li> -->
                <?php } ?>

                <?php  if ($role["Aceita_Proposta"]) { ?>
                  <li class="biMenu"><a href="../backoffice/BackOffice.php?comm=view">Aceita&ccedil;&atilde;o Prop.</a></li>
                <?php } ?>

                <?php  if ($role["Emissao_Apolice"]) { ?>
                  <li class="biMenu"><a href="../policy/Policy.php?comm=view">Ap&oacute;lices a Emitir</a></li>
                <?php } ?>

                <?php  if ($role["policy"]) { ?>
                  <li class="biMenu"><a href="../policy/Policy.php?comm=allPolicies&menu=1">Ap&oacute;lices emitidas</a></li>
                <?php } ?>

                <?php if (($role["Altera_DPP"])){ ?>
                  <li class="biMenu"><a href="../searchClient/ListClient.php?comm=ListContrat">Alterar DPP</a></li>
                <?php } ?>
              </ul>
            </li>
          <?php } ?> 

          <?php if ($role["Libera_Credito"]) {    ?> 
            <li>
              <a href="#" class="menu" style="z-index: 9999;" ></a><label class="lblTopMenu" style="z-index: 9997">Cr&eacute;dito</label> 
              
              <ul> 
                <?php if ($role["executive"] ||$role["executiveLow"] || $role["regionalManager"] || $role["credit"] || $role["creditManager"] || $role["creditInform"] || $role["tariffer"]) {    ?>
                  <!-- <li class="biMenu"><a href="../credit/Credit.php?comm=IncompleteInform">Informes Incompletos</a></li> -->
                <?php } ?> 

                <?php if ($role["Libera_Credito"]) { ?>
                  <li class="biMenu"><a href="../credit/Credit.php?comm=PendenciesCoface">Pend&ecirc;ncias</a></li>
                <?php } ?>
              </ul>
            </li>
          <?php } ?>


        <?php */  ?>


          <?php if (check_menu(['cessao', 'bancoBB', 'bancoOB', 'bancoParc'], $role)) { ?>
            <li>
              <a href="#" class="menu" style="z-index: 9999;" ><label class="lblTopMenu" style="z-index: 9997">Cess&atilde;o de direito</label> </a>
             
              <ul>
                <?php if (check_menu(['cessao'], $role)) { ?>
                  <li class="biMenu"><a href="../cessao/Cessao.php?comm=cadBanco">Cadastro de Banco</a></li>
                  <li class="biMenu"><a href="../cessao/Cessao.php?comm=cadAg">Cadastro de Ag&ecirc;ncia</a></li>
                <?php } ?>

                <?php if ($_SESSION['pefil'] == 'F') { ?>
                  <li class="biMenu"><a href="../cessao/Cessao.php?comm=cancelaCessaoDireitoSeguradora">Cancelar Cess&atilde;o de Direitos</a></li>
                <?php } ?>

                <?php if ($_SESSION['pefil'] == 'B') { ?>

                  <?php $pa6 = isset($perfil_banco[6]["Possui_Acesso"]) ? $perfil_banco[6]["Possui_Acesso"] : 0; ?>
                  <?php if ($pa6 == 1) { ?>
                    <li class="biMenu"><a href="../cessao/Cessao.php?comm=consultaCessao">Ap&oacute;lices Cedidas</a></li>
                  <?php } ?>

                  <?php $pa7 = isset($perfil_banco[7]["Possui_Acesso"]) ? $perfil_banco[7]["Possui_Acesso"] : 0; ?>
                  <?php if ($pa7 == 1) { ?>
                    <li class="biMenu"><a href="../cessao/Cessao.php?comm=cancelaCessaoDireitoBB">Cancelar Cess&atilde;o de Direitos</a></li>
                  <?php } ?>

                <?php } ?>

                <?php if (check_menu(['cessao'], $role)){ ?>  
                  <li class="biMenu"><a href="../searchCessao/SearchCessao.php">Cess&atilde;o de Direito</a></li>
                  <!-- <li class="biMenu"><a href="../dve/Dve.php?comm=NotificacaoNPC">Notifica&ccedil;&atilde;o de N.P.C</a></li> -->
                <?php } ?>


                <?php if ($_SESSION['pefil'] == 'B') { ?>
                  
                  <?php if ($perfil_banco[8]["Possui_Acesso"] == 1) { ?>
                    <li class="biMenu"><a href="../cessao/Cessao.php?comm=emiteCessaoDireito">Emiss&atilde;o de Cess&atilde;o de Direito</a></li>
                  <?php } ?>
                  
                <?php } ?>

                <?php if ($_SESSION['pefil'] == 'F') { ?>
                  <li class="biMenu"><a href="../cessao/Cessao.php?comm=emiteCessaoDireitoSeguradora">Emiss&atilde;o de Cess&atilde;o de Direito</a></li>                
                <?php } ?>

                <!-- <li class="biMenu"><a href="../arquivoEletronico/arquivoeletronico.php?comm=exibeArquivo">Arquivo Eletr&ocirc;nico</a></li> -->
              </ul>
            </li>
          <?php } ?>

           <?php 
   //COloca o menu relatorios pra planilha automatica
    if ($_SESSION['pefil'] == 'F') { ?>
        <li>
            <a href="#" class="menu" style="z-index: 9999;"></a>
            <label class="lblTopMenu" style="z-index: 9997">Relat&oacute;rios</label>
            <ul>
                <li class="biMenu"><a href="../relatorios/Relatorios.php?comm=TOD">TOD Step 1</a></li>
                <li class="biMenu"><a href="../relatorios/Relatorios.php?comm=situacaoFinanceira">Situa&ccedil;&atilde;o Financeira</a></li>
                <li class="biMenu"><a href="../relatorios/Relatorios.php?comm=calculoClCofSbce">C&aacute;lculo Loss Ratio</a></li>
                <li class="biMenu"><a href="../relatorios/Relatorios.php?comm=malusBonus ">Controle de Malus e Bonifica&ccedil;&atilde;o</a></li>
                <li class="biMenu"><a href="../relatorios/Relatorios.php?comm=CAPRI ">CAPRI - Limites por Segurado</a></li>
            </ul>
        </li>
    <?php } 

    ?>

          <?php  if (check_menu(['policy', 'listbc'], $role)){ ?>
            <li>
              <a href="#" class="menu" style="z-index: 9999;"><label class="lblTopMenu" style="z-index: 9997">Configura&ccedil;&otilde;es</label></a>
              
              <ul>
                <?php if (check_menu(['relacaoCliExec'], $role)){ ?>
                  <li class="biMenu"><a href="../searchClient/ListClient.php?comm=ViewExecRegion">Rela&ccedil;&atilde;o Executivo/Regi&atilde;o</a></li>
                  <li class="biMenu"><a href="../searchClient/RelacaoClientExecutivo.php">Rela&ccedil;&atilde;o Cliente/Executivo</a></li>
                <?php } ?>  

                <?php if (check_menu(['generalManager'], $role)) { ?>
                  <li style="display:none" class="biMenu"><a href="../access/Access.php?comm=log">Log Administrativo</a></li>
                <?php } ?>

                <?php if (check_menu(['generalManager'], $role)) { ?>
                  <li class="biMenu"><a href="../access/Access.php?comm=usuarios">Usu&aacute;rios</a></li>
                <?php }  ?>

                <li class="biMenu"><a href="../access/Access.php?comm=change">Senha</a></li>

                <?php if (check_menu(['clientAdmin'], $role))  {   ?>
                  <li class="biMenu"><a href="../searchClient/searchSenha.php">Rela&ccedil;&atilde;o Senha</a></li>
                <?php } ?>

                <?php if (check_menu(['generalManager'], $role)) { ?>
                  <li style="display:none" class="biMenu"><a href="../executive/Executive.php?comm=historico_transacao">Hist&oacute;rico das Transa&ccedil;&otilde;es</a></li>
                <?php } ?>  

                <?php if (check_menu(['generalManager'], $role)) { ?>
                  <li class="biMenu"><a href="../parameter/ParameterSystem.php">Par&acirc;metros</a></li>
                <?php }  ?>

                <?php if (check_menu(['generalManager'], $role)) { ?>
                  <!-- <li class="biMenu"><a href="../assinatura/Assinatura.php">Assinatura</a></li> -->
                <?php }  ?>

                <?php if (check_menu(['generalManager'], $role)) { ?>
                  <li style="display:none" class="biMenu"><a href="../log/Log.php">Log Certificado Digital</a></li>
                <?php }  ?>
              </ul>

            </li>
          <?php } ?> 


                    <?php /*  ?>


          <?php if ($role["Cadastro_Modulos"] || $role["Cadastra_Corretor"]) { ?>
            <li><a href="#" class="menu" style="z-index: 9999;"><label class="lblTopMenu" style="z-index: 9997">Cadastro</label></a>
              
              <ul>
              <?php if ($role["Cadastro_Modulos"]) { ?>
                <li class="biMenu"><a href="../module/ModuleSystem.php">M&oacute;dulos</a></li>
                <?php }  ?>
                <?php if ($role["Cadastra_Corretor"]) { ?>
                <li class="biMenu"><a href="../area_consultor/listConsultor.php?comm=cadastraConsultor">Corretores</a></li>
                <?php }  ?>
              </ul>
            </li>
          <?php } ?>


                    <?php */ ?>


        <?php } ?>
      </ul>
    </div>
  </div>
</div>