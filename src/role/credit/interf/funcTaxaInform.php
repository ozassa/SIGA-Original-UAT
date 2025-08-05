<?php 
//esta funчуo irс retornar dados do exportador
function taxaInform($nomeExport,$idInform,$idAnt,$db)
	{
		//cria arrays para armazenar o exportador e seus dados
		$arrExp= array();
		$arrExp['EXPORTADOR'] = $nomeExport;
		
		$consulta = "";
		if($idAnt == null || $idAnt=="")
		{
			$consulta="select id,name,state,txAnalize,txMonitor,CONVERT(VARCHAR,startValidity,103) as startValidity,endValidity,notificaRenova,n_Apolice,idAnt,startValidity,i_Seg,contrat,nProp
					from Inform where id =$idInform ";
		}
		else
		{
			$consulta="select top 2 id,name,state,txAnalize,txMonitor,CONVERT(VARCHAR,startValidity,103) as startValidity,endValidity,notificaRenova,n_Apolice,idAnt,startValidity,i_Seg,contrat,nProp
					from Inform where name like '$nomeExport%' and state in(10,11) order by id desc";
		}
		
		
		$exec = odbc_exec($db, $consulta);
		//numero total de linhas da consulta
		$linhaConsulta = odbc_num_rows($exec);
		
		if($exec)
		{			
			//quando a consulta responder apenas 1 linha, ou seja, sendo status 10 ou 11, sera guardado os dados de taxa e analise e seu inform
			if($linhaConsulta == 1)
			{
				$txAnalize        = odbc_result ($exec, 'txAnalize');
				$txMonitor        = odbc_result ($exec, 'txMonitor');
				$id               = odbc_result ($exec, 'id');
				$state            = odbc_result ($exec, 'state');
				$fimDaApoliceEndV = odbc_result ($exec, 'startValidity').";".odbc_result ($exec, 'endValidity');
				
				$startValidity = odbc_result ($exec, 11);
				$i_Seg         = odbc_result ($exec, 12);
				$contrat       = odbc_result ($exec, 13);
				$nProp         = odbc_result ($exec, 14);
				
				
				
				//guarda dentro do array do exportador, os indices com suas taxas e seu id inform
				$arrExp['TAXASAM']        =$txAnalize.','.$txMonitor;
				$arrExp['INFORMS']        =$id;
				$arrExp['STATE']          =$state;
				$arrExp['FIMDATEAPOLICE'] =$fimDaApoliceEndV;
				
				$arrExp['startValidity'] =$startValidity;
				$arrExp['i_Seg']         =$i_Seg;
				$arrExp['contrat']       =$contrat;
				$arrExp['nProp']         =$nProp;
				
				
			}else
			{
				//percorre as linhas da consulta
				$l=1;
				while (odbc_fetch_row($exec)) 
				{
					if($l==1)
					{
						$txAnalize         = odbc_result ($exec, 'txAnalize');
						$txMonitor         = odbc_result ($exec, 'txMonitor');
						$arrExp['TAXASAM'] =$txAnalize.','.$txMonitor;
						$nProp         	   = odbc_result ($exec, 14);
						$arrExp['nProp']   =$nProp;
					}
									
					
					$state                    .= odbc_result ($exec, 'state').",";
					$id                       .= odbc_result ($exec, 'id').",";
					$fimDaApoliceEndV         .= odbc_result ($exec, 'startValidity').";".odbc_result ($exec, 'endValidity').",";
					
					$startValidity = odbc_result ($exec, 11);
					$i_Seg         = odbc_result ($exec, 12);
					$contrat       = odbc_result ($exec, 13);
					
					$arrExp['INFORMS']        =$id;
					$arrExp['STATE']          =$state;
					$arrExp['FIMDATEAPOLICE'] =$fimDaApoliceEndV;
					
					$arrExp['startValidity'] =$startValidity;
					$arrExp['i_Seg']         =$i_Seg;
					$arrExp['contrat']       =$contrat;
					
					$l++;
				}
				
				
			}
		}
	//este array serс utilizado depois para poder armazenas todos os imporadores deste exportador	
	$arrExp['IMPORTADORES'] = array();
	
	return $arrExp;
	}
?>