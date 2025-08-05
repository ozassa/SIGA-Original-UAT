<?php //Alterado HiCom mes 04
//Alterado HiCom 19/10/04 Gustavo - adicionei um item de menu para Parcela de Ajuste
//Alterado Hicom 21/12/04 Gustavo - retirei o item de menu "Parcela de Ajste" antigo
//Alterado Hicom 10/01/05 Gustavo - inclusão de um novo perfil igual ao crédito, sem permissão de alteração
//Alterado HiCom 11/01/05 Gustavo - adicionei um item de menu para Usuários
 session_start();
?>

<TABLE border="0" cellPadding="3" cellSpacing=0 width="80%" align="center">
    <TR>
      <TD class="titulo" align="center"><br>
      </TD>
    </TR>
  <!-- Alterado por Michel Saddock 11/08/2006 -->
    <?php
	
    $qry = "select a.id,a.name,c.login 
			from Role a
				inner join UserRole b on b.idRole = a.id
				inner join Users c on c.id = b.idUser
			where c.id = '$userID'
			order by a.name,c.login";
			
	 $cur=odbc_exec($db,$qry);
	  while (odbc_fetch_row($cur)) {
         $x = $x + 1;
	     $name  = odbc_result ($cur, 'name');
		 $id    = odbc_result ($cur, 'id');
		 $role[$name] = $id.'<br>';
	  }
		//echo  $role['executive'];	 
        
        ?>
  
  <?php
  
  if($userID){
  
	  if ($role["executive"] || $role["executiveLow"] || $role["regionalManager"] || $role["credit"] || $role["creditManager"] || $role["creditInform"] || $role["tariffer"] || $role["policy"] || $role["financ"] || $role["backoffice"] || $role["dve"] || $role["sinistro"] || $role["endosso"] || $role["cessao"] || $role["viewCredit"]) { ?>
	  
	  <TR>
		<TD class="titulo" align="left"><a href="../searchClient/SearchClient.php">Clientes</a>
		</TD>
	  </TR>
	  
	
	  
	  
	  <?php }
	  ?>
	  <?php if ($role["executive"] ||$role["executiveLow"] || $role["regionalManager"] || $role["credit"] || $role["creditManager"] || $role["creditInform"] || $role["tariffer"]) {?>
	<!--<TR>
	  //  <TD class="titulo" align="left"><A href="../searchProcess/SearchProcess.php">Processos</A>
	  //  </TD>
		</TR> -->
	  <?php if ($role["executive"]) {
	  ?>
		<TR>
		  <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=IncompleteInform>Informes Incompletos</A>
		  </TD>
		</TR>
	  <?php }
	  ?>
	<!--
	  <TR>
		<TD class="titulo" align="left"><A href="../searchProcess/SearchProcess.php">Processos</A>
		</TD>
	  </TR>
	-->
	  <?php } ?>
	  <TR>
		<TD class="titulo" align="left"><A href="../notification/BoxInput.php">Notificações</A>
		</TD>
	  </TR>
	
	<!----  HICOM MENU DVE ---->  
	<!-- adicionei o perfil "tariffer" (Gustavo) -->
	  <?php if ($role["dve"] || $role["tariffer"]) {
	  ?>
	  
		 <TR>
		   <TD class="titulo" align="left"><A href="../dve/Dve.php?comm=consultadve">Consulta DVE</A>
		   </TD>
		 </TR>
	
		 <TR>
		   <TD class="titulo" align="left"><A href="../dve/Dve.php?comm=consultaDveEmitidaBanco">Histórico Doc DVE</A>
		   </TD>
		 </TR>
		 <TR>
		   <TD class="titulo" align="left"><A href="../dve/Dve.php?comm=NotificacaoNPC">Notificação de N.P.C</A>
		   </TD>
		 </TR>
		 <TR>
		   <TD class="titulo" align="left"><A href="../dve/Dve.php?comm=libera_dve">Liberar DVE</A>
		   </TD>
		 </TR>
	  
	
	  <?php }elseif (($role["bancoBB"] || $role["bancoOB"] || $role["bancoParc"])){
		?>  <TR>
		   <TD class="titulo" align="left"><A href="../dve/Dve.php?comm=consultadveBanco">Consulta DVE</A>
		   </TD>
		 </TR>
		<?php }
	  ?>
	  
	  
	<!----  FIM HICOM MENU DVE---->
	
	
	<!----  Desabilitado por Michel Saddock 15/09/2006 MENU DIF SISSEG---->
	  <?php //if ($role["credit"] ) {
	  ?>
		<!--
		 <TR>
		   <TD class="titulo" align="left"><A href="../credit/Credit.php?comm=showdifsisseg">Diferenças Sisseg</A>
		   </TD>
		 </TR>
		-->
	
	  <?php //}
	  ?>
	  
	  
	<!----  Desabilitado por Michel Saddock 15/09/2006 MENU DIF SISSEG---->
	
	
	
	  <?php if ($role["credit"] || $role["creditManager"] || $role["creditInform"]) {
	  ?>
	  <TR>
		<TD class="titulo" align="left"><A href=../credit/Credit.php?comm=PendenciesCoface>Pendências</A>
		</TD>
	  </TR>
	  <!--
	  <TR>
		<TD class="titulo" align="left"><A href=../credit/Credit.php?comm=resMonitor>Fechamento</A>
		</TD>
	  </TR>
	  -->
	<?php /* $query="select * 
				from 
					Users 
				where 
					email in('wlima.pconsulting@sbce.com.br','klevy.pconsulting@sbce.com.br','capanema.pconsulting@sbce.com.br') 
					and id=".$_SESSION['user']->id."";
		$rs = odbc_exec($db, $query);
		if(odbc_result($rs, 1)==$_SESSION['user']->id)
		{ */
	?>
		
	  <TR>
		<TD class="titulo" align="left"><A href=../credit/Credit.php?comm=fechamentoMensal>Fechamento</A>
		
		</TD>
	  </TR>
	<?php //} 
	?>
	  <!-- Alterado por Michel Saddock 11/08/2006
	  <TR>
		<TD class="titulo" align="left"><A href="../credit/Credit.php?comm=ClientNotConfirmed">E-Mails</A>
		</TD>
	  </TR>
	  -->
	  <TR>
		<TD class="titulo" align="left"><A href="../credit/Credit.php?comm=statistics">Estatísticas</A>
		</TD>
	  </TR>
	  <?php }
	  ?>
	  <?php if ($role["financ"] || $role["backoffice"]) {
	  ?>
	  <TR>
		<TD class="titulo" align="left"><A href="../financ/Financ.php?comm=view">Prop. Emitidas</A>
		</TD>
	  </TR>
	  <?php }
	  ?>
	  <?php if ($role["sinistro"]) {
	  ?>
	  <!--TR>
		<TD class="titulo" align="left"><A href="../sinistro/Sinistro.php?comm=histSinistro">Histórico de Sinistro</A>
		</TD>
	  </TR-->
	  <?php }
	  ?>
	  <?php if ($role["policy"] || $role["backoffice"]) {
	  ?>
	  <TR>
		<TD class="titulo" align="left"><A href="../backoffice/BackOffice.php?comm=view">Aceitação Prop.</A>
		</TD>
	  </TR>
	  <TR>
		<TD class="titulo" align="left"><A href="../policy/Policy.php?comm=view">Apólices a Emitir</A>
		</TD>
	  </TR>
	  <TR>
		<TD class="titulo" align="left"><A href="../policy/Policy.php?comm=allPolicies&menu=1">Apólices emitidas</A>
		</TD>
	  </TR>
	  <?php }
		//Alterado por Tiago V N - Elumini - 23/05/2006
		if ($role["policy"] || $role["financ"]){
	  ?>
	  <!--
	  <TR>
		<TD class="titulo" align="left"><A href="../policy/Policy.php?comm=Esm">End. S/Mov Prêmio/Cancelamento</A></TD>
	  </TR>
	  -->
	  <?php }
	
		# Alterado por Tiago V N - Elumini - 19/10/2006
		# Criar o menu Exportação SUSEP para o financeiro
		if ($role["policy"] || $role["financ"]){
		
	?>
	  <TR>
		<TD class="titulo" align="left"><A href="../financ/Financ.php?comm=Esusep">Exportação SUSEP</A></TD>
	  </TR>
	<?php }	
	// Gustavo - 19/10/04
		if ($role["dve"] || $role["creditManager"]) {
	  ?>
		<TR>
		  <TD class="titulo" align="left"><A href=../dve/dve.php?comm=consultaPa>Parcela de Ajuste</A>
		  </TD>
		</TR>
	  <?php }
	// Gustavo - 19/10/04 FIM
		
	
	if($role["regionalManager"]){
	  ?>
	  <TR>
		<TD class="titulo" align="left"><A href="../regionalManager/RegionalManager.php?comm=prod">Produção</A>
		</TD>
	  </TR>
	<?php }
	?>
	  <?php if ($role["cessao"]) {
	  ?>
	  <TR>
		<TD class="titulo" align="left"><A href="../cessao/Cessao.php?comm=cadBanco">Cadastro de Banco</A>
		</TD>
	  </TR>
	  <TR>
		<TD class="titulo" align="left"><A href="../cessao/Cessao.php?comm=cadAg">Cadastro de Agência</A>
		</TD>
	  </TR>
	  <?php }
	  ?>
	<?php if ($role["bancoBB"] || $role["bancoOB"] || $role["bancoParc"]) {
	  ?>
		<TR>
		  <TD class="titulo" align="left"><A href=../cessao/Cessao.php?comm=consultaCessao>Apólices Cedidas</A>
		  </TD>
		</TR>
	
		<TR>
		  <TD class="titulo" align="left"><A href=../cessao/Cessao.php?comm=cancelCessaoBB>Cancelar Cessão de Direitos</A>
		  </TD>
		</TR>
	  <?php }
	  ?>
	  
	   <?php if ($role["cessao"])
	   {
	   ?>  
		<TR>
		  <TD class="titulo" align="left"><A href=../searchCessao/SearchCessao.php>Cessão de Direito</A>
		  </TD>
		</TR>
	<?php }
	
	?>
	<!-- Adicionado por Michel Saddock 06/09/2006 -->
	<?php // if ( ($userID=='1953') ||($userID=='40') || ($userID=='3905') || ($userID=='36') ||($userID=='2808') || ($userID=='2752') ||($role["policy"]))
	 // Interaktiv - Elias Vaz
	 // 17-12-2009 - Por solicitaçao do Ricardo Turatto, foi comentado o trecho acima mantendo a condição na linha abaixo.
	 if ($role["clientAdmin"])
	 {
	
	 ?>
	
	  <TR>
		  <TD class="titulo" align="left"><A href="../area_consultor/listConsultor.php?comm=cadastraConsultor">Incluir Consultor</A>
			  
		  </TD>
		</TR>
	 <?php } ?>
	<!-- Fim Adicionado por Michel Saddock 06/09/2006 -->
	
	  <TR>
		  <TD class="titulo" align="left"><A href="../arquivoEletronico/listArquivo.php?comm=exibeArquivo">Arquivo Eletrônico</A>
		  </TD>
		</TR>
		
	
	
	<?php if  ($role["policy"] ||
		 $role["capolice"] ||
		 $role["listbc"] ||
		 $role["relacaoCliExec"] )  {
	?>
		<TR>
		  <TD class="titulo" align="left"><img src="/site/func/imgs/bottom_menu.gif" border="0" width="120" height="3"></TD>
		</TR>
	<?php }
	?>
	
	<?php //Criando menu p/ cancelar apólice
	 //Alterado por Rony F S(Elumini) - 31/08/2005
	
	//Desabilitado por Michel Saddock 04/10/2006
	  //if (($role["capolice"]) Or ($role["policy"]) Or
	   //   ($role["financ"])){
	   ?>
	   <!-- <TR>
		  <TD class="titulo" align="left"><A href=../searchClient/SearchApolice.php>Cancelar Informe</A>
		  </TD>
		</TR>
		-->
	   <?php // }
	?>
	<?php //Criando menu p/ limpar numero ci
	 //Alterado por Tiago V N(Elumini) - 13/10/2005
	if (($role["regionalManager"]) Or ($role["policy"]) Or
	   ($role["credit"]) Or ($role["creditManager"]) Or
	   ($role["creditInform"])){
	   ?>
		<TR>
		  <TD class="titulo" align="left"><A href=../searchClient/ListClient.php?comm=ListContrat>Alterar nº contrat</A>
		  </TD>
		</TR>
	   <?php }
	?>
	
	   <?php //Menu Relação Executivo Region
	//Alterado por Tiago V N(Elumini) - 06/10/2005
	//Id do Usuaria Nice - 40
	   if (($role["relacaoCliExec"]) Or ($role["policy"]))
	   {
	   ?>
		<TR>
		  <TD class="titulo" align="left"><A href=../searchClient/ListClient.php?comm=ViewExecRegion>Relação Executivo/Região</A>
		  </TD>
		</TR>
	   <?php }
	   ?>
	
	
	
	
	   <?php //Criando menu p/ listar senha do banco e cliente
	   //Alterado por Tiago V N(Elumini) - 17/08/2005
	   if (($role["listbc"]) Or ($role["policy"]))  {
	   ?>
		<TR>
		  <TD class="titulo" align="left"><A href=../searchClient/searchSenha.php>Relação Senha</A>
		  </TD>
		</TR>
	   <?php }
	   ?>
	   
	   <?php //Criando relação cliente/executivo
	   //Feito por Fabio Campos(Elumini) - 17/08/2005
	   if (($role["policy"]) or ($role["relacaoCliExec"]))   {
	   ?>
		<TR>
		  <TD class="titulo" align="left"><A href="../searchClient/RelacaoClientExecutivo.php">Relação Cliente/Executivo</A>
		  </TD>
		</TR>
	   <?php }
	   ?>
	   
	   <?php //Exibir LOG
	   //Feito por Fabio Campos(Elumini) - 06/09/2005
	   if ($role["policy"]) {
	   ?>
		<TR>
		  <TD class="titulo" align="left"><A href="../access/Access.php?comm=log">Log Administrativo</A>
		  </TD>
		</TR>
	   <?php }
	   ?>
	
	<?php if ($role["endosso"]) {
	  ?>
	<!-- Apagar futuramente - apenas para testar o endosso de parcela -->
		<!--<TR>
		  <TD class="titulo" align="left"><A href=../endosso/Endosso.php?comm=parcela&idInform=799>Parcela de Ajuste</A>
		  </TD>
		</TR>-->
	<!-- até aqui -->
	
	<!-- Alterado Hicom (Gustavo) - 21/12/04
		<TR>
		  <TD class="titulo" align="left"><A href=../endosso/Endosso.php?comm=naogeradoPA>Parcela de Ajuste</A>
		  </TD>
		</TR>
	-->
	  <?php }
		// alterado Hicom (Gustavo)
		if ($role["policy"]) {
	?>
	  <TR>
		<TD class="titulo" align="left"><A href="../access/Access.php?comm=usuarios">Usuários</A>
		</TD>
	  </TR>
	<?php // fim
		}
	?>
	<?php if  ($role["policy"] ||
		 $role["capolice"] ||
		 $role["listbc"] ||
		 $role["relacaoCliExec"] )  {
	?>
		<TR>
		  <TD class="titulo" align="left"><img src="/site/func/imgs/bottom_menu.gif" border="0" width="120" height="3"></TD>
		</TR>
	<?php }
	?>
	
	  <TR>
		<TD class="titulo" align="left"><A href="../access/Access.php?comm=change">Senha</A>
		</TD>
	  </TR>
	<?php if ($role["bancoBB"] || $role["bancoOB"] || $role["bancoParc"]) {
	?>
	  <TR>
		<TD class="titulo" align="left"><A href="javascript: sair()">Sair</A>
		</TD>
	  </TR>
	  
	<script language="javascript">
	/*
	  Alterado por Elumini(Tiago V N) - 17/08/2005
	  Alteracao exitclient por exit(Funcionario)
	*/
	
	function sair () { 
	if (confirm ("Deseja Realmente Abandonar a Sessão?")) 
	   { window.parent.location.href='../access/Access.php?comm=exit';}
	}
	</script>
	
	<?php } else { ?>
	  <TR>
		<TD class="titulo" align="left"><A href="../access/Access.php?comm=exit">Sair</A>
		</TD>
	  </TR>
	<?php }
	
	
  }
	?>

   
<!--  Tela antiga de Análise dos importadores do Informe
    <TR>
      <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=AnaliseBuyers>AnaliseBuyers</A>
      </TD>
    </TR>
    <TR>
      <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=ClientNotConfirmed>E-Mails</A>
      </TD>
    </TR>
    <TR>
      <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=CreditAccord>Crédito Informes</A>
      </TD>
    </TR>
    <TR>
      <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=CountryConsult>Consulta (Países)</A>
      </TD>
    </TR>
    <TR>
      <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=SearchReplyCoface>Consulta de Resp.</A>
      </TD>
    </TR>
    <TR>
      <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=ImportConsult>Importadores(Nomes)</A>
      </TD>
    </TR>
    <TR>
      <TD class="titulo" align="left"><A href=../credit/Credit.php?comm=FichadeAprovação>Ficha de Aprovação</A>
      </TD>
    </TR>
-->    

<!-- Telas estáticas -->
</TABLE>

