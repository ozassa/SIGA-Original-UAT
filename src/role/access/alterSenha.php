<?php

    // echo " dentro de alterSenha" ;

    function somadata( $data, $nDias ){
        if( !isset( $nDias ) )
            {
              $nDias = 1;
            }
            $aVet = Explode( "-",$data );
            return date( "Y-m-d",mktime(0,0,0,$aVet[1],$aVet[0]+$nDias,$aVet[2]));
    }

	 //teste para verificar se ele deve alterar a senha
	 $id = $userID; //id do usuário (Users)
	 $dataHoje = date('Y-m-d'); //data atual

	 $q = "SELECT * from Users where id = '$id'" ;
	 $cur = odbc_exec($db, $q);
	 
	
	 
	 while (odbc_fetch_row($cur))
	 {
		$alterSenha = odbc_result($cur,'alterSenha');
	 }

	 if(($alterSenha)=="") //Caso o campo alterSenha seja NULL - primeiro acesso apos criacao
	 {
		 $dataAcrescimo = somadata(date('d-m-Y'), 6); //prazo de 7 dias para trocar senha
		 $q2 = "UPDATE Users set alterSenha = '$dataAcrescimo' where id = '$id'" ;
		 $cur2 = odbc_exec($db, $q2);

		 $indicador = "dentroPrazo";
		 $comm = "open";
       
		  
		 $content = "../client/interf/AlterLog.php";
		 require_once("../../../home.php");
	 }
	 else
	 {

		 function EntreDatas( $inicio, $fim )
		 {
			 $aInicio = Explode( "-",$inicio );
			 $aFim    = Explode( "-",$fim    );
			 $nTempo = mktime(0,0,0,$aFim[1],$aFim[0],$aFim[2]);
			 $nTempo1= mktime(0,0,0,$aInicio[1],$aInicio[0],$aInicio[2]);
			 return round(($nTempo-$nTempo1)/86400)+1;
		 }
		   //Data atual
		   $dataHoje = substr($dataHoje,0,10); //Pegando somente a data sem timestamp
		  //Formatando a data para o padrão Brasil
				  $dia = substr($dataHoje,8,2);
				  $mes = substr($dataHoje,5,2);
				  $ano = substr($dataHoje,0,4) ;
				  $dataHojeBr = $dia.'-'.$mes.'-'.$ano ;
		  //Fim Formatando a data para o padrão Brasil

		  //Data limite de troca de senha
		  $alterSenha =  substr($alterSenha,0,10); //Pegando somente a data sem timestamp
		  //Formatando a data para o padrão Brasil
			   $dia = substr($alterSenha,8,2);
			   $mes = substr($alterSenha,5,2);
			   $ano = substr($alterSenha,0,4);
			   $alterSenha = $dia.'-'.$mes.'-'.$ano ;
		  //Fim Formatando a data para o padrão Brasil

		 //EntreDatas( $inicio, $fim )
		 $intervalo = EntreDatas($dataHojeBr, $alterSenha);
		 $intervalo = $intervalo-1;

		 //Data atual acrescida de 6 meses. Tempo de troca de senha é de 6 em 6 meses
		 $soma =  somadata ($dataHojeBr, 180);
		 //Se ainda esta dentro dos 7 dias a tela é a mesma para todos os tipos de cliente
		 if(($intervalo>=0) && ($intervalo<7))
		 {
			 //$comm = "open";
			 $indicador = "dentroPrazo";
			 $content = "../client/interf/AlterLog.php";
			 require_once("../../../home.php");
		 }
		 elseif($intervalo<0) //Se já estourou o prazo
		 {
			$indicador = "foraPrazo";
			$content = "../client/interf/AlterLog.php";
			require_once("../../../home.php");
		 }
		 else //Se o intervalo de tempo for maior que 7
		 {
			   if(($tipoCli) == "banco")
			   {
					$title = "Notificações";
					$content = "../notification/interf/ViewBox.php";
					require_once("../../../home.php");
			   }
			   elseif(($tipoCli)=="comum")
			   {
				 $comm = "open";
				 require_once("../inform/Inform.php");
			   }
			   elseif(($tipoCli)=="consultor")
			   {
				 $id = $userID;
				 $comm = "escolheConsultor";
				 $content= "../area_consultor/consultorInforme.php";
				 require_once("../../../home.php");
				 exit();
			   }
			   elseif(($tipoCli)=="funcionario")
			   {
				 $title = "Notificações";
				 $content = "../notification/interf/ViewBox.php";
				 require_once("../../../home.php");
			   }
			   else
			   {
				 echo "Erro: Tipo de cliente não encontrado! <br> alterSenha >> Intervalo maior que 7";
				 break;
			   }
		 }

     }
       
 ?>
