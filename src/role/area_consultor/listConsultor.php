<?php
require_once("../rolePrefix.php");
   
    if ($comm == "cadastraConsultor")  //variavel passada pela url
    {
       $title = "Cadastro de consultor";   //Titulo da p�gina
       $content = "../area_consultor/cadastro_consultor.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php");//Tela do funcionario
    }

    if ($comm == "insereConsultor")  //variavel passada pela url
    {
       require_once('gravaConsultor.php'); //p�gina que sera executada
       $title = "Cadastro de consultor";   //Titulo da p�gina
       $content = "../area_consultor/cadastro_consultor.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php");//Tela do funcionario
    }
      
    if ($comm == "listaConsultor")  //variavel passada pela url
    {
       $title = "Consultores cadastrados";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/exibe_corretores.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php");//Tela do funcionario
     }
     
    if ($comm == "editarConsultor")  //variavel passada pela url
    {
       $title = "Consultores cadastrados";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/editar_consultor.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php");//Tela do funcionario
    }
     
    if ($comm == "editarConsultor2")  //variavel passada pela url
    {
       require_once('editar_consultor2.php'); //p�gina que sera executada
       $title = "Alterado com sucesso!";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/cadastro_consultor.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php");//Tela do funcionario
    }
    
    if ($comm == "voltarCadastro")  //variavel passada pela url
    {
       $title = "Cadastro de consultor";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/cadastro_consultor.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php"); //Tela do funcionario
    }
    
    if ($comm == "voltarCadastro1")  //variavel passada pela url
    {
       $title = "Cadastro de consultor";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/exibe_corretores.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php"); //Tela do funcionario
    }
    
    if ($comm == "selecionaConsultor")  //variavel passada pela url
    {
       $title = "Selecione o consultor desejado";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/selecionaConsultor.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php"); // Tela do cliente
    }

    if ($comm == "selecionaConsultor2")  //Todos os status exceto novo e prenchido variavel passada pela url
    {
       $title = "Selecione o consultor desejado";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/selecionaConsultor2.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php"); // Tela do cliente
    }
    
    if ($comm == "AdicionaConsultor")
    {
    
     $sql = "Select * from Inform where id = '$idInform'";
     $cur = odbc_exec($db, $sql);
     odbc_fetch_row($cur);
     $state = odbc_result($cur, "state");

     if(($status)!="novo")
     {
       require_once('adicionaConsultor.php'); //p�gina que sera executada
       $title = "Selecione o consultor desejado";   //Titulo da p�gina para onde ser� redirecionado
       require_once("../inform/verifyAnt.php");
       
	   if ($state >= 3)
          $content = "../client/interf/ViewClient.php";
       else
          $content = "../inform/interf/InformRes.php";
       
       require_once("../../../home.php"); // Tela do cliente
     }
     else  //Caso seja o 1� acesso do cliente(novo)
     {
        if(($cadastro)=="cadastrar")
        {
          require_once('adicionaConsultor.php'); //p�gina que sera executada
        }
        $content = "../inform/interf/InformRes.php";
        require_once("../../../home.php");
     }


  }

    if ($comm == "AdicionaConsultor2")
    {

     $sql = "Select * from Inform where id = '$idInform'";
     $cur = odbc_exec($db, $sql);
     odbc_fetch_row($cur);
     $state = odbc_result($cur, "state");

     require_once('adicionaConsultor.php'); //p�gina que sera executada
     $title = "Selecione o consultor desejado";   //Titulo da p�gina para onde ser� redirecionado

     require_once("../inform/verifyAnt.php");
     if ($state >= 3)
       $content = "../client/interf/ViewClient.php";
     else
       $content = "../inform/interf/InformRes.php";

     require_once("../../../home.php"); // Tela do cliente

    }

    if ($comm == "escolheConsultor")  //variavel passada pela url
    {
      // require('escolheConsultor.php'); //p�gina que sera executada
       $title = "Selecione o consultor desejado";   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/escolheConsultor.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php"); // Tela do cliente
    }
    
    if ($comm == "DesativarConsultor")  //variavel passada pela url
    {
       require_once('DesativarConsultor.php'); //p�gina que sera executada
       //$title esta recebendo valor dentro da p�gina DesativarConsultor.php   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/exibe_corretores.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php"); //Tela do funcionario
    }
    
    if ($comm == "excluirConsultor")  //variavel passada pela url
    {
       require_once('excluirConsultor.php'); //p�gina que sera executada
       //$title esta recebendo valor dentro da p�gina   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/exibe_corretores.php"; // p�gina para onde sera direcionado
       require_once("../../../home.php");//Tela do funcionario
    }
    
    if ($comm == "alteraSenha")  //variavel passada pela url
    {
       require_once('senhaConsultor.php'); //p�gina que sera executada
       //$title esta recebendo valor dentro da p�gina   //Titulo da p�gina para onde ser� redirecionado
       $content = "../area_consultor/consultorInforme.php"; // p�gina para onde sera direcionado
         require_once("../../../home.php");
       //require_once("../../../../site/func/index.php"); //Tela do funcionario
    }
    

?>
