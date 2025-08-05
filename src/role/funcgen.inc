<?php

#*********************************************************
#Nome do arquivo: modelos_embarque.php
#Data da criacao: 10/11/2004
#Autor: Fábio Lucena
#objetivo: Arquivo com as funções globais utlizadas em 
#		   todo sistema
#
#data da modificacao: 16/08/2006
#autor: Fábio Lucena
#***************************************************

include('sessao.inc');
include('funcDB.inc');

$base_de_dados = "SQLSERVER";

//verifica a base de dados utilizada
if ($base_de_dados == "MYSQL") {
  $USUARIO = trim($_REQUEST['LOGIN']);
  $SENHA   = '';
  //definindo sessao manualmente
  $_SESSION['MANUTENCAO'] = "M";
  $_SESSION['CONSULTA']   = "C";
  $_SESSION['MASTER']     = "MASTER";
} else {
  $USUARIO = trim($_REQUEST['LOGIN']);
  $SENHA   = trim($_REQUEST['SENHA']);
}
//**  Carrega o total de registros por página de uma navegação **/
if (!isset($_SESSION['TOT_PAG'])){
	$_SESSION['TOT_PAG'] = 10;
}

if (!isset($_SESSION['root'])){
//	$_SESSION['root'] = "E:\\Inetpub\\wwwroot\\sbce\\novo_mprazo\\func\\";
	$_SESSION['root'] = "D:\\projects\\sbce\\sgp\\func\\";
}
//**  Variável global que contém a mensagem do último erro ocorrido no banco de dados **/
global $MSG_ERRO_BANCO;

/** Constrole de login o usuário - Início **/
global $MSG_RESP_LOGIN;

if (($USUARIO)&&($SENHA)){
//se ele quiser se logar...
	apagaSessaoUsuario();
	login($USUARIO,Encripta($SENHA),$base_de_dados);
}else{
	if (!isset($_SESSION['ID_USUARIO'])){
	    $url = getUrl().'index.php';
		
		//echo "USUARIO: " . $_SESSION['ID_USUARIO'] . "<BR>";
		//echo "URL: " . $url . "<BR>";
		//echo "HTTP: http://" . $_SERVER['SERVER_NAME'].strtolower($_SERVER['PHP_SELF']) . "<BR>";
		//pre($_SERVER);
		//die;

	}
}
/** Controle de login o usuário - Fim **/

function login($login, $senha,$base_de_dados){
// Loga o usuário através do login e senha passados
	global $MSG_RESP_LOGIN;
        $flagLogin="";
        $flagSenha="";
	if (($login)&&($senha)){

            //verifica se a base e mysql ou sqlserver
            if ($base_de_dados == "MYSQL") {
	      $sql = "Select * from USUARIOS where CD_LOGIN = '". $login ."' and CD_SENHA = '". $senha ."' and FL_ATIVO = 1 AND IC_MPME = 1 ";
	    } else {
	      $sql = "Select * from USUARIOS where CD_LOGIN = '". $login ."' and CD_SENHA = '". $senha ."' and FL_ATIVO = 1 AND IC_MPME = 1 ";
	    }  

		//vou tentar logar o usuário
		$usuarioVet = execQueryGetRow($sql);
		if ($usuarioVet){
		    //login e senha confere
		    carregaSessaoUsuario($usuarioVet);
		    return true;
		}else{
		    //login e senha nao conferem, vou verificar qual dos dois é diferente
	    	$sql = "Select * from USUARIOS where CD_LOGIN = '". $login ."' AND FL_ATIVO = 1 ";
			if (execQueryGetRow($sql)){
			    //login confere
			    $flagLogin = 1;
			}
			$sql = "Select * from USUARIOS where CD_SENHA = '". $senha ."' AND FL_ATIVO = 1 ";
			if (execQueryGetRow($sql)){
			    //senha confere
			    $flagSenha = 1;
			}
			if ((!$flagLogin)&&(!$flagSenha)){
			    //login e senha não conferem
			    $MSG_RESP_LOGIN = "Usuário não cadastrado.";
			}elseif (!$flagLogin){
			    $MSG_RESP_LOGIN = "Login não confere.";
			}elseif (!$flagSenha){
			    $MSG_RESP_LOGIN = "Senha não confere.";
			}else{
			    $MSG_RESP_LOGIN = "Usuário não cadastrado.";
			}

			return false;
		}

	}elseif((!$login)&&($senha)){
	    $MSG_RESP_LOGIN = "Este login não é válido.";
	    return false;
	}elseif(($login)&&(!$senha)){
	    $MSG_RESP_LOGIN = "Esta senha não é válida.";
	    return false;
	}else{
	    $MSG_RESP_LOGIN = "Login e senha não são válidos.";
	    return false;
	}
}

function apagaSessaoUsuario(){
//apaga a sessão corrente do usuário
	unset($_SESSION['ID_USUARIO']);
	unset($_SESSION['CD_LOGIN']);
	unset($_SESSION['NM_USUARIO']);
	unset($_SESSION['MASTER']);
	unset($_SESSION['MANUTENCAO']);
	unset($_SESSION['CONSULTA']);
}

function carregaSessaoUsuario($usuarioVet){
/**  Método que carrega os seguintes dados do usuário na session:
**  $_SESSION['ID_USUARIO']
**  $_SESSION['CD_LOGIN']
**  $_SESSION['NM_USUARIO']
**  $_SESSION['MASTER']
**  $_SESSION['MANUTENCAO']
**  $_SESSION['CONSULTA']
**/

	$_SESSION['ID_USUARIO'] = intval($usuarioVet['ID_USUARIO']);
	$_SESSION['CD_LOGIN'] = $usuarioVet['CD_LOGIN'];
	$_SESSION['NM_USUARIO'] = $usuarioVet['NM_USUARIO'];
	
	$sql = "Select VL_PARAM from PARAMETROS where CD_PARAM = 'MASTER'";
	$vet = execQueryGetRow($sql);
	if (is_array($vet)){
       if (instr($usuarioVet['CD_LOGIN']."|", $vet['VL_PARAM'])){
          $_SESSION['MASTER'] = "1";
       }else{
          $_SESSION['MASTER'] = "0";
       }
	}else{
       $_SESSION['MASTER'] = "0";
	}

	carregaSessaoFuncoesUsuario();
}

function carregaSessaoFuncoesUsuario(){
//carrega as funções de acesso do usuário na Session
	if ($_SESSION['ID_USUARIO']){
            $_SESSION['MANUTENCAO'] = "";
            $_SESSION['CONSULTA'] = "";
            
			$sql = "Select CD_FUNCAO, FL_MANUT
			        From        GRUPOUSU gu
			        Inner Join  PERFIL p   on  p.ID_GRUPO = gu.ID_GRUPO
					Where gu.ID_USUARIO = ". $_SESSION['ID_USUARIO'] . "
				    Group by CD_FUNCAO, FL_MANUT";
			if (montaRS($sql, $rs)){

			    for($i=0;$i<count($rs);$i++){
				    if ($rs[$i]['FL_MANUT']=="M"){
				        if ($i==0){
			            	$_SESSION['MANUTENCAO'] = "|".$rs[$i]['CD_FUNCAO'];
			            	$_SESSION['CONSULTA'] = "|".$rs[$i]['CD_FUNCAO'];							
				        }else{
				            $_SESSION['MANUTENCAO'] .= "|".$rs[$i]['CD_FUNCAO'];
				            $_SESSION['CONSULTA'] .= "|".$rs[$i]['CD_FUNCAO'];
				        }					
					}else{
				        if ($i==0){
			            	$_SESSION['CONSULTA'] = "|".$rs[$i]['CD_FUNCAO'];
				        }else{
				            $_SESSION['CONSULTA'] .= "|".$rs[$i]['CD_FUNCAO'];
				        }
					}	
			    }

	            $_SESSION['MANUTENCAO'] .= "|";
	            $_SESSION['CONSULTA']   .= "|";
			}
	}
}

function logout(){
//desloga o usuário
  apagaSessaoUsuario();
}

/****      Para o caso de login automático -  Início**/
function loginFromCookie(){
	//para o caso de login automático
	$key = $_COOKIE['C_key'];
	if ($key){
	    $login = base64_decode($key);
	    $sql = "Select * from USUARIOS where CD_LOGIN = '". $login ."' AND FL_ATIVO = 1 ";
		$usuarioVet = execQueryGetRow($sql);
	    carregaSessionUsuario($usuarioVet);
	}else{
	    return false;
	}
}

function setCookieLogin($login){
	$key = base64_encode($login);
	setCookie("C_key", $key, time() + 10*365*24*60*60,"/"); // 10 anos de cookie
}

function unsetCookieLogin(){
    setcookie("C_key","",0,"/");
}
/****      Para o caso de login automático -  Fim**/

function getUrl(){
  //esta função monta a url que está sendo executada no browser; será utilizada para redirecionamentos
  $vet = explode('/',$_SERVER['SCRIPT_NAME']);
  $str = "";
  for($i=0;$i<count($vet)-1;$i++){
    $str .= $vet[$i].'/';
  }
  return  'http://'.$_SERVER['SERVER_NAME']. $str;
}

function acesso($codFuncao, $op){
  //retorna true caso o usuário tenha acesso à função ou false caso contrário
  $retorno = false;
  if ($_SESSION) {
    if ($_SESSION['MASTER']=="1") {
      $retorno = true;
    } else {
      if (strtoupper($op)=="M") {
        //Manutenção
        if ($_SESSION['MANUTENCAO']){
          //se a função for permitida para a sessão deste usuário, retorna true
          $retorno = instr("|".$codFuncao."|",$_SESSION['MANUTENCAO']);
        }	
      } else {
        //Consulta
        if ($_SESSION['CONSULTA']){
          //se a função for permitida para a sessão deste usuário, retorna true
          $retorno = instr("|".$codFuncao."|",$_SESSION['CONSULTA']);
        }	
      }
    }
  }
  return $retorno;
}

function getUF(){
//retorna a lista de UFs do Brasil
	return array("AC","AL","AM","AP","BA","CE","DF","ES","GO","MA","MG","MS","MT","PA","PB","PE","PI","PR",
	               "RJ","RN","RO","RR","RS","SC","SE","SP","TO");
}

function getEstado($UF){
   switch($UF){
      case "AC":  return "Acre";      break;
      case "AL":  return "Alagoas";   break;
      case "AM":  return "Amazonas";  break;
      case "AP":  return "Amapá";     break;
      case "BA":  return "Bahia";     break;
      case "CE":  return "Ceará";     break;
      case "DF":  return "Distrito Federal";  break;
      case "ES":  return "Espírito Santo";    break;
      case "GO":  return "Goiás";     break;
      case "MA":  return "Maranhão";  break;
      case "MG":  return "Minas Gerais";      break;
      case "MS":  return "Mato Grosso do Sul";break;
      case "MT":  return "Mato Grosso";       break;
      case "PA":  return "Pará";      break;
      case "PR":  return "Paraná";      break;
      case "PB":  return "Paraíba";   break;
      case "PE":  return "Pernambuco";        break;
      case "PI":  return "Piauí";     break;
      case "RJ":  return "Rio de Janeiro";    break;
      case "RN":  return "Rio Grande do Norte";break;
      case "RO":  return "Rondônia";  break;
      case "RR":  return "Roraima";   break;
      case "RS":  return "Rio Grande do Sul"; break;
      case "SC":  return "Santa Catarina";    break;
      case "SE":  return "Sergipe";   break;
      case "SP":  return "São Paulo"; break;
      case "TO":  return "Tocantins"; break;
   }
}

function getArtigoCidade($cidade){
    switch($cidade){
      case "Aracaju":  return "de"; break;
      case "Belém":  return "de"; break;
      case "Belo Horizonte":  return "de"; break;
      case "Boa Vista":  return "de"; break;
      case "Brasília":  return "de"; break;
      case "Campo Grande":  return "de"; break;
      case "Cuiabá":  return "de"; break;
      case "Curitiba":  return "de"; break;
      case "Florianópolis":  return "de"; break;
      case "Fortaleza":  return "de"; break;
      case "Goiânia":  return "de"; break;
      case "João Pessoa":  return "de"; break;
      case "Macapá":  return "de"; break;
      case "Maceio":  return "de"; break;
      case "Manaus":  return "de"; break;
      case "Natal":  return "de"; break;
      case "Palmas":  return "de"; break;
      case "Porto Alegre":  return "de"; break;
      case "Porto Velho":  return "de"; break;
      case "Recife":  return "de"; break;
      case "Rio Branco":  return "de"; break;
      case "Rio de Janeiro":  return "do"; break;
      case "Salvador":  return "de"; break;
      case "São Luis":  return "de"; break;
      case "São Paulo":  return "de"; break;
      case "Teresina":  return "de"; break;
      case "Vitória":  return "de"; break;
  }
}
function getPais(){
  //retorna a lista de países salvas no banco de dados
  $sql = "SELECT * FROM PAISES ORDER BY NM_ORDER";
  montaRS($sql, $rs);
  return $rs;
}

function getListaChecks($ID_OPER, $ST_OPER){
   $sql = "Select NU_CHECK from STATUSINF where ID_OPER = $ID_OPER AND ST_OPER = $ST_OPER";
   if (montaRS($sql, $lista)){
     if (is_array($lista)){
         foreach($lista as $k=>$l){
            $ret[$k] = $l['NU_CHECK'];
         }
     }else{
        if ($l['NU_CHECK']!=''){
           $ret[0] = $l['NU_CHECK'];
        }
     }
     return $ret;
   }else{
     return false;
   }
}


/********************************** métodos para acessar o banco de dados *********************************/
function getInicioPaginacao($pagina, $limite){
//retorna o índice inicial da paginação
	if (($pagina!=1)&&($pagina!=0)){
		return $inicio = ($pagina-1)*$limite;
	}else{
	    return false;
	}
}

function montaRS($sql, &$rs){
  //esta função retorna um array com os registros solicitados pela query $sql
  global $MSG_ERRO_BANCO;

    $db = new DB();
    if ($ret = $db->query($sql)){
    	$rs = $db->loadArray();
    	return true;
    }else{
        $MSG_ERRO_BANCO = $db->getErro();
       return false;
    }
}

function montaRSComPaginacao($sql, &$rs, $pagina, $limite, &$paginacaoVet, $registroVet=NULL){

//esta função retorna um array ($rs) com os registros solicitados pela query $sql
//paginacaoVet -> vetor paginacao que será carregado
//registroVet[0] -> nome do campo procurado
//registroVet[1] -> valor do campo procurado
    global $MSG_ERRO_BANCO;
    $db = new DB();

	if (!$registroVet){
		if (!$pagina){
		    $pagina = 1;
		}
	
		if ($pagina){
 		    $db->setPagina($pagina);
		    $inicio = getInicioPaginacao($pagina, $limite);
		    if (!$limite){
		        //caso tenha passado a página e não tenha passado o limite, o default será 10 para a paginação
		        $limite = 10;
		    }
		}
	    if ($inicio){
	    	$db->setInicio($inicio);
	    }
	}else{
	    $db->setRegistro($registroVet);
	}
	
    if ($limite){
        $db->setLimite($limite);
    }

    if ($ret = $db->query($sql)){
		if ($registroVet){
    	    $rs = $db->loadArrayComRegistro();
		}else{
		    $rs = $db->loadArray();
		}
    	if ($db->_limite){
    	    $total = $db->getTotal();
    		if ($total>$db->_limite){
    		    //número da página anterior
    		    $paginacaoVet['pag_ant'] = $db->_pagina - 1;
    		    //número da próxima página
    		    $paginacaoVet['pag_prox'] = $db->_pagina + 1;
    		    //número da quantidade de páginas
    		    $quant_pag = intval($total / $db->_limite);
    		    if ($total % $db->_limite > 0){
    		        $quant_pag = $quant_pag + 1;
    		    }
    		    $paginacaoVet['quant_pag'] = $quant_pag;
    		    //número da página atual
    		    $paginacaoVet['pag_atual'] = $db->_pagina;
    		    //número do resultado da busca
    		    $paginacaoVet['total'] = $total;
    		    //número do primeiro item a ser mostrado na página
    		    $paginacaoVet['reg_ini'] = $db->_inicio+1;
    		    //número do último item a ser mostrado na página
    		    $paginacaoVet['reg_fim'] = $db->_inicio+count($rs);
    		    if ($total>1){
    		        $paginacaoVet['s'] = 's';
    		    }else{
    		        $paginacaoVet['s'] = '';
    		    }
    		}
    	}
    	return true;
    }else{
        $MSG_ERRO_BANCO = $db->getErro();
        return false;
    }
}

function gravaLog($sql) {

    $dbLog = new DB();
	$sqlLog = "INSERT INTO LOG(ID_USUARIO, DT_LOG, DE_SQL) VALUES ('".$_SESSION['ID_USUARIO']."', getdate(), '".str_replace("'","''",$sql)."')";
	$_SESSION['DS_LOG'] = $sqlLog;
	if ($dbLog->query($sqlLog)){
        return true;
    }else{
      $MSG_ERRO_BANCO = $dbLog->getErro();
        return false;
    }
	
}

function execQuery($sql){
//esta função não retorna nada, somente esecuta uma query no banco
    global $MSG_ERRO_BANCO;

    $db = new DB();
	gravaLog($sql);
    if ($db->query($sql)){
        return true;
    }else{
      $MSG_ERRO_BANCO = $db->getErro();
        return false;
    }
}

function execQueryGetRow($sql){
//método que executa uma query e retorna somente um registro
    global $MSG_ERRO_BANCO;
    $db = new DB();
	gravaLog($sql);
    if ($db->query($sql)){
       	return $db->getRow();
    }else{
        $MSG_ERRO_BANCO = $db->getErro();
        return false;
    }
}

function execQueryGetField($sql,$nomeCampo=""){
//método que executa uma query e retorna somente o valor de um campo (caso a query tenha retornado algum resultado)
//se $nomeCampo for vazio, o método vai retornar o valor do primeiro campo no select, senão vai retornar o valor
//do campo solicitado

    global $MSG_ERRO_BANCO;

    $db = new DB();

	gravaLog($sql);
    if ($db->query($sql)){

       	if ($vet = $db->getRow()){
            $c = array_keys($vet);
            if ($nomeCampo){
              	return $vet[$nomeCampo];
       	    }else{
                return $vet[$c[0]];
       	    }
       	}else{
          return false;
        }
    }else{
        $MSG_ERRO_BANCO = $db->getErro();
        return false;
    }
}

function trataErroBD(){
//método que trata o último erro ocorrido no banco para exibir para o usuário
    global $MSG_ERRO_BANCO;

	if (instr("Violation of PRIMARY KEY",$MSG_ERRO_BANCO)){
		$msg = "Erro no banco de dados: Este registro já foi cadastrado e, por isto, não pôde ser cadastrado novamente.";
	}elseif (instr("Cannot insert duplicate key row",$MSG_ERRO_BANCO)){
		$msg = "Erro no banco de dados: Este login já existe.";
	}else{
		$msg = str_replace("[Microsoft][ODBC SQL Server Driver][SQL Server]","Erro no banco de dados: ",$MSG_ERRO_BANCO);
	}
	return $msg;	
}

//************* Métodos para facilitar a utilizacão do php *****//
function pre($var){
//escreve na tela qq variavel, sendo objeto, ou array ou não
	print("<pre>");
	print_r($var);
	print("</pre>");
}

function instr($str, $main_str){
//retorna true se a string $str estiver contida na string $main_str
// e false se ela nao estiver contida  
    return ( strpos($main_str,$str) === false ) ? false : true;
}

function trataStr(&$str){
//método que trata uma url retirando as '/' inseridas quando o formulário é postado e
//subsituindo ' por ´ para não dar problema no momento da inserção
    $str = stripslashes($str);
    $str = str_replace("'", "´", $str);
}

function toUpperFirst($str, $todasPal = false){
//se $todasPal = true, converte todas as primeiras letras da palavras de $str para maiúsculas
//se $todasPal = false, converte a primeira letra de $str para maiúscula
//todas as outras letrar são convertidas para minúsculas para garantir
   if ($todasPal){
      //vetor de palavras
      $vetStr = explode(" ",$str);
      foreach($vetStr as $strAux){
      //para cada palavra...
           for($i=0;$i<strlen($strAux);$i++){
           //para cada letra
               if ($i==0){
                  $s .= strtoupper($strAux[$i]);
               }else{
                  $s .= strtolower($strAux[$i]);
               }
           }
           $s .= " ";
      }
      return $s;
   }else{
       for($i=0;$i<strlen($str);$i++){
           if ($i==0){
              $s .= strtoupper($str[$i]);
           }else{
              $s .= strtolower($str[$i]);
           }
       }
       return $s;
   }
}

//--------------------------------------------------------------------
//--- FUNÇÃO PARA SUBSTITUICAO DE "," Por "." E DE "." POR ","
//--- PARA INSERCAO DE VALORES COM CASA DECIMAIS
//--------------------------------------------------------------------
function Moeda($valor)
{
   $valor_A =  trim(str_replace(",","_",$valor));
   $valor_B =  trim(str_replace(".",",",$valor_A)); 
   $valor_FINAL =  trim(str_replace("_",".",$valor_B)); 
   return $valor_FINAL;
}

//--------------------------------------------------------------------
//--- FUNÇÃO PARA SUBSTITUICAO DE "," Por "." 
//--------------------------------------------------------------------
function Moeda_numeric($valor)
{
   $valor_A =  trim(str_replace(".","",$valor));
   return  trim(str_replace(",",".",$valor_A));

}
//--------------------------------------------------------------------
//--- FUNÇÃO PARA CONVERSÃO DE strings em numéricos, colocando "." e ","
//--------------------------------------------------------------------
function aplicaMascaraNumerica($valor, $numCasasDecimais)
{
   if ($valor){
       if (!instr(",", $valor) && !instr(".", $valor))
       {  
          $valor .=  ".";
	      for($j=1;$j<=$numCasasDecimais;$j++){
  	          $valor .= "0";
		  }	
       } 

       $valor = str_replace(",","",$valor);
       $valor = str_replace(".","",$valor);   
 
	   $parteInteira = substr($valor,0,strlen($valor)-$numCasasDecimais);
	   $parteDecimal = substr($valor,strlen($valor)-$numCasasDecimais,$numCasasDecimais);
	   $k = 1;
	   
	   for($i=strlen($parteInteira)-1;$i>=0;$i--){
	       $p .= $parteInteira[$i];
	       if (($k % 3 ==0)&&($i!=0)){
	          $p .= ".";
	       }
	       $k += 1;		   
	   }
	   for($i=strlen($p)-1;$i>=0;$i--){
	       $pF .= $p[$i];
	   }
	   return $pF.",".$parteDecimal;
   }	   
}

//--------------------------------------------------------------------
//--- FUNÇÃO PARA CONVERSÃO DE DATAS (INDEPENDE DO REGIONAL SETTINGS)
//--------------------------------------------------------------------
function addData($formato, $num, $tipo, $data = ""){
//adiciona o numero $num à data corrente ou à $data
//e retorna a data no formato desejado
//tipo = day, month, year
//$data -> formato d/m/Y
    if ($data){
  	    return date($formato,strtotime("+$num $tipo", strtotime(formataData("m/d/Y", $data))));
  	}else{
  	    return date($formato,strtotime("+$num $tipo"));
  	}
}

function subData($formato, $num, $tipo, $data = ""){
//subtrai o numero $num à data corrente ou à $data
// e retorna a data no formato desejado
//tipo = day, month, year
//$data -> formato d/m/Y
    if ($data){
       return date($formato,strtotime("-$num $tipo", strtotime(formataData("m/d/Y", $data))));
  	}else{
       return date($formato,strtotime("-$num $tipo"));
  	}
}

function TrataData($data, $tipo, $saida){
	
	#
	# Variavel $data é a String que contém a Data em qualquer formato
	# Variavel $tipo é que contém o tipo de formato data.
	# $tipo : 
	#		1 - Brasil - No formato -> Dia/Mes/Ano ou DD/MM/YYYY
	#		2 - USA	 - No formato -> YYYY-Mes-Dia ou YYYY-MM-DD
	#
	# $saida :
	# 	    1 - Brasil
	# 	    2 - USA		
	#
	# Obs
	# Esta função não funciona com timestemp no formato a seguir :
	# DD/MM/YYYY H:M:S.MS ou YYYY-MM-DD H:M:S:MS
	# Pode configurar o formato da Data
	
	if ( $tipo == 1) {
		list($dia, $mes, $ano) = explode("[/-]", $data);		
	}elseif ( $tipo == 2 ) {
		list($ano, $mes, $dia) = explode("[-/]", $data);		
	}else{
		$msg = "Erro - Formato de data não existe.";
	}	
	
	if ($saida == 1) {		
		return $dia."/".$mes."/".$ano;
	}elseif ($saida == 2){
		return $ano."-".$mes."-".$dia;
	}else{
		return 0;
	}
}

function formataData($formato, $data, $eng = 0, $pos = ""){
//formata uma data qualquer no formato desejado
//se $eng = 1, data passada está em inglês, senão está em português
    $d = $data;

	if(!$eng){
    if (instr("/",$data)){

		//vou converter a string em português para inglês, pois é assim que
		// a funcao strtotime entende
		    $vet = explode("/",$data);
        $dia = $vet[0];
	    	$mes = $vet[1];
		    $d = str_replace($dia."/".$mes, $mes."/".$dia, $data);
		}
	}

    //caso seja passsado o paramentro $pos para funcao, ira formatar no formato datatime do sql
    //ini indicara que datatime sera partir do primeiro segundo do data informada e fim do ultimo segundo
    //necessario para fazer pesquias entre datas tipo: dta_ini<= data and dta_fim>= data
    if ($pos=="ini")
    {
       $d = $d . " 00:00:00";
    }
    elseif ($pos=="fim")
        {
           $d = $d . " 23:59:59";
        }
    
    return date($formato,strtotime($d));
}//fim formataData

function ConvertData($wdata_hora){
//retorna a data no formato dd/mm/aaaa
   if ($wdata_hora != "")
   {
	  $wdata = trim(str_replace("00:00:00","",$wdata_hora));

      if (strlen($wdata)< 10)
      {
      
	     if (strlen($wdata)== 8) //Ex: 1/1/2004
	     {
	        $dia = trim("0".substr($wdata,0,1));
	        $mes = trim("0".substr($wdata,2,1));
	        $ano = trim(substr($wdata,4,8));
         }
         elseif (strlen($wdata)== 9) //Ex: 1/10/2004 ou 10/1/2004
	     {
	        $wdia = trim(str_replace("/","",substr($wdata,0,2)));
	        $ano  = trim(substr($wdata,5,9));

		    if (strlen($wdia)== 1) //Ex: dia:d, mes:mm
		    {
		       $dia = trim("0".$wdia);
		       $mes = trim(substr($wdata,2,2));
		    }
            elseif (strlen($wdia)== 2) //Ex: dia:dd, mes:m
		    {
		        $dia = $wdia;
			    $mes = trim("0".substr($wdata,3,1));
		    }
          }
       $data = $dia."/".$mes."/".$ano;
       return trim($data);
      }
      else
      {
       return trim($wdata);
      }
   }
}

function getMes($nuMes){
  switch($nuMes){
     case "01":
         return "Janeiro";
     break;
     case "02":
         return "Fevereiro";
     break;
     case "03":
         return "Março";
     break;
     case "04":
         return "Abril";
     break;
     case "05":
         return "Maio";
     break;
     case "06":
         return "Junho";
     break;
     case "07":
         return "Julho";
     break;
     case "08":
         return "Agosto";
     break;
     case "09":
         return "Setembro";
     break;
     case "10":
         return "Outubro";
     break;
     case "11":
         return "Novembro";
     break;
     case "12":
         return "Dezembro";
     break;
  }
}

/*
Funcao generica para montar um linst box.
vpTabela=tabela a ser listada
vpCampoCodigo=campo de codigo id ou chave,
vpCampoMostra=campo a ser exibo: descricao etc,
vpCodigo=codigo atual para posicionar a lista,
vpLinhaVazia= listar a primeira linha em branco "opcional",
vpLinhaProcura= exibir uma linha expecial para procura "usada no listBoxExpert.php
*/
   function montaGen($vpTabela, $vpCampoCodigo, $vpCampoMostra, $vpCodigo= "", $vpLinhaVazia=false, $vpLinhaProcura=false)
   {

      if ($vpTabela=="PAISES") {
        $sql= "select " . $vpCampoCodigo . ", " . $vpCampoMostra . " from " . $vpTabela . " order by NM_ORDER, " . $vpCampoMostra;
      } else {
        $sql= "select " . $vpCampoCodigo . ", " . $vpCampoMostra . " from " . $vpTabela . " order by " . $vpCampoMostra;
      }
      montaRS($sql, $rs);

      $listBox="";
      if ($vpLinhaVazia)   $listBox.="<option></option>";
      if ($vpLinhaProcura) $listBox.="<option value='digitar'>[Procurar...]</option>";

      foreach($rs as $line)
      {
         $listBox.= "<option ";
         if ( $vpCodigo == $line[$vpCampoCodigo] ) { $listBox.= " selected "; }
         $listBox.= " value=" . $line[$vpCampoCodigo] . ">" . $line[$vpCampoMostra] . "</option>";
      }

      return $listBox;
   }
   
   //Lista somente usuários que são responsáveis por alguma operação - Cristiano Elumini
   
   function montaSelResOper($vpCampoCodigo, $vpLinhaVazia=false)
   {


			//$sql= "select * from USUARIOS WHERE TP_USUARIO ='F' And ID_USUARIO <> '1' And ID_USUARIO <> '15' And ID_USUARIO <> '13' ORDER BY NM_USUARIO";
            $sql = "SELECT U.ID_USUARIO, CD_LOGIN, NM_USUARIO FROM OPERACOES O ";
            $sql.= "INNER JOIN USUARIOS U ";
            $sql.= "ON O.ID_ANALISTA = U.ID_USUARIO ";
            $sql.= "WHERE U.FL_ATIVO = 1 "; // FL_ATIVO = 0; ENTÃO = SIM
            $sql.= "AND U.TP_USUARIO ='F' ";
            $sql.= "GROUP BY U.ID_USUARIO, CD_LOGIN, NM_USUARIO ";
            $sql.= "ORDER BY U.NM_USUARIO ";

//            echo $sql;
					
			montaRS($sql, $vet);
			
      $listBox="";
      if ($vpLinhaVazia)   $listBox.="<option></option>";

			foreach($vet as $line)
			{

 				$NM_USUARIO = $line['NM_USUARIO'];
				$CD_LOGIN = $line['CD_LOGIN'];
				$ID_USUARIO = $line['ID_USUARIO'];

					$listBox.= "<option ";
 					if ( $vpCodigo == $CD_LOGIN ) { $listBox.= " selected "; }
 					$listBox.= " value=" . $CD_LOGIN . ">" . $NM_USUARIO . "</option>";

			}

      return $listBox;
   }
   
	 //Fim - Cristiano Elumini


/*
FUNCAO PARA CHECAR SE TAL VALOR E VAZIO DE ACORDO COM UM TIPO QUALQUER
vpCampo=VALOR A SER ANALIZADO
vpTipo=TEX: string
vpRetornoVazio=VALOR A SER RETORNADO CASO VAZIO
vpRetornoNaoVazio: RETORNA UM VALOR CASO NAO VAZIO "OPCIONAL, CASO NAO SEJA INFORMADO RETORNA O PROPRIO VALOR DE campo"
*/
   function vazio($vpCampo, $vpTipo, $vpRetornoVazio, $vpRetornoNaoVazio="*-*")
   {
       switch ($vpTipo)
       {
          case 'string': //string
               if (trim($vpCampo)!= "")
               {
                  if ($vpRetornoNaoVazio!= "*-*") return $vpRetornoNaoVazio;
                  else return $vpCampo;
               }
               else return $vpRetornoVazio;
               break;

       }
   }

//--------------------------------------------------------------------
//--- FUNÇÃO PARA ENCRIPTAR SENHAS
//--------------------------------------------------------------------

function Encripta($info)
{
   $aux = "";
   $chave = "";
   
   for ($i = 0; $i <= (strlen($info) - 1); $i++)
   {  $charaux = substr($info, $i, 1);
      $charaux = dechex(ord($charaux));
	  
      if (strlen($charaux) == 1)
      {   $charaux = "0" . $charaux;
      }

	  $charaux = $charaux . "F"; 

      $aux = $aux . $charaux;
   }
   
   $aux = $aux . $chave;	  
   
   return $aux;
}

//---------------------------------------------------------------
//--- FUNÇÃO PARA DECRIPTAR A SENHA
//---------------------------------------------------------------

function Decripta($info)
{
   $aux = "";
   $i   = 0;
   
   while ($i <= (strlen($info) - 1))
   {  $charaux = substr($info, $i, 2);

      $charaux = chr(hexdec($charaux));
	  $aux = $aux . $charaux;
	  
      $i = $i + 3;
   }
   
   return $aux;
}

//********************** Método que carrega os dados numa template ********************************************//
//num -> número de elementos do array $val
//$val -> array com os valores a serem substituidos
//$pathArquivoMatriz -> diretório + nome do arquivo origem no formato .rtf
//$pathArquivoCopia -> diretório + nome do arquivo destino
//$flgIni -> flag inicio do token
//$flgFim -> flag fim do token
//$show -> escreve o arquivo na tela
//********************** Método que carrega os dados numa template ********************************************//
/*
ESTE METODO IRA ANALIZAR TODO O TEXTO A PROCURA DE TOKENS(PARALAVRAS E OU CARACTERES RESEVADOS COMO EM LIGUANGEM DE PROGRAMACAO E TAGS HTML)
APARTIR DAI, IRA SUBSTITUIR O QUE ESTIVER ENTRE O TAG DE INICIO E FIM.
A MELHOR EXTRATEGIA E UTILIZAR O CONTEUDO QUE ESTARÁ ENTRE OS TOKENS DE INICIO E FIM, ONDE ELES SERAM LIDOS E PROCURADOS
NO VETOR DE PARAMETROS QUE E PASSADO PARA FUNCAO
ESTE VETOR DEVE SER ASSOCIATIVO ONDE PODE SER INFORMADO UM NOME AO INVEZ DE UM NUMERO, COM ISTO O VETOR
PODE SER INDEXADO TANTO POR NUMERO QUANTO POR PALAVRA, DESDE QUE A MESMA ESTEJA NOS TOKENS

OBS: UM MACETE VISTO PARA PREENCHIMENTO NOS ARQUIVOS RTF É QUE OS TOKENS DEVEM ESTAR FORMATADOS JUNTO COM O CONTEUDO
, POIS SE NAO SERA INSERIDO TAGS NO TEXTO QUE NAO SAO VISTOS NO WORD(COMO EM HTML), SENDO ASSIM QUANDO ABERTO O ARQUIVO EM
ASCII PODERA ESTE TOKEN NAO SER SUBSTITUIDO, POIS HA MAIS TEXTO ENTRE ELE.
*/
   function gerarCopiaPreechida($num, $val, $pathArquivoMatriz, $pathArquivoCopia, $flgIni, $flgFim, $show=false)
   {
      if ( is_file($pathArquivoMatriz) )
           {
             if (file_exists($pathArquivoCopia)){
                unlink($pathArquivoCopia);
             }
             $fMatriz = fopen($pathArquivoMatriz, 'r');
             $fCopia  = fopen($pathArquivoCopia,  'w');

             while ( !feof($fMatriz) )
             {
                $ch= fgets($fMatriz, 2);
                if ($show) echo $ch . "<br>";

                if ($ch!= $flgIni) fwrite($fCopia, $ch);
                else
                {
                   $token="";
                   while ( !feof($fMatriz) )
                   {
                      $ch= fgets($fMatriz, 2);
                      if ($show) echo $ch . "<br>->";

                      if ($ch!= $flgFim) $token.= $ch;
                      else
                      {
                         fwrite($fCopia, $val[$token]);
                         break;
                      }
                   }
                   if (feof($fMatriz)) fwrite($fCopia, $token);
                }
             }
             fclose($fMatriz);
             fclose($fCopia);
             return 1;
           }
           else  return -2;

   }



   /*
       FUNCOES PARA TRATAMENTO DE NUMEROS DE RETORNO DO SQL E APLICACAO DE MASCARAS NUMERICAS.
   */
   //CONVERTE O NUMERO RETORNADO DO SQL SERVER.
   function convercaoTipo(&$var, $tipo)
   {
      switch($tipo)
      {
         case "BRfloat": $var= str_replace(".","",$var);
                         $var= str_replace(",",".",$var);
                         settype($var, "float");
                         break;

          case "BRint":  $var= str_replace(".","",$var);
                         $var= str_replace(",",".",$var);
                         settype($var, "integer");
                         break;
      }
   }

   /*
      inseri n casas decimais. diminui o numero que ja existe do que sera incluido
      ex: 9.6 -> para 2 casas -> 9.60
   */
   function inserirCasasDecimais(&$string, $num)
   {
      $tmp= explode(".", $string);
//      $neg= $tmp[0]{0};
//      if ($neg== "-") $tmp[0]{0}= "0";

//      $tmp[0]*=1;
//      $tmp[0]+='';

      if ($tmp[0]=="")
      {
         $tmp[0]= $string;
         $tmp[1]= "";
      }
      
      if ($num<=0)
      {
         $string= $tmp[0];
         return;
      }

      $string= $tmp[0] . "." . $tmp[1];
      for($i= strlen($tmp[1]) ; $i< $num ; $i++) $string.= "0";
//      if ($neg== "-") $string= "-" . $string;
   }


   /*
      mascara uma string no formato float
   */
   function mascaraNumerica(&$string, $num)
   {
      //settype($string, "float");
	  
	  if ($string == "")
	  {  $string = "0.0";
	  }

	  if (!$posdec)
	  {  $string = $string . ".0";
	  }

	  $posdec = strpos($string, ".");
	  $strdec = "";
	  
	  if ($posdec)
	  {   $strdec = "" . round(substr($string, $posdec), $num);
	      $strdec = str_replace("0.", "", $strdec);
      }
	  
      //$string= round($string, $num);
	  
	  $string = substr($string, 0, $posdec + 1) . $strdec;
	  
      settype($string, "string");

      inserirCasasDecimais($string, $num);
	  
      $string= str_replace(".",",",$string);

      if ((strlen($string)-$num-4)<= 0){ return; }

      $f= ($num>0) ? strlen($string)-$num-1 : strlen($string)-$num;
      $i= ($f%3);

      if($i>0) $ret= substr($string, 0, $i) . ".";
      for( ; $i< $f-3 ; $i+=3) $ret.= substr($string, $i, 3) . ".";
      $ret.= substr($string, $f-3);

      $string= $ret;
   }

   function convertRetornoSql($val, $tipo, $num)
   {//$val -> valor a ser convertido
    //$tipo -> tipo a ser convertido
    //$num -> número de casas decimais
      switch ($tipo)
      {
         case "float": convercaoTipo($val, "BRfloat");
                       settype($val, "string");
                       mascaraNumerica($val, $num);
                       break;
      }
      return $val;
   }


   /*
      PEGA UM NUMERO CONVERTE SUA MASCARA PARA MASCARA NUMERICA NATIVA
      CONVERTE PARA STRING
      INSERE A MASCARA COM N CASAS
   */
   
   function convertToFloat($val, $tipo, $num)
   {
     convercaoTipo($val, "BRfloat");
     settype($val, "string");
     mascaraNumerica($val, $num);
     return $val;
   }

   /*
      INSERE MASCARA NUMERICA EM UM NUMERO FLOAT INFORMADO RETORNANDO COM N CASAS DECIMAIS USANDO AS QUE JA EXISTIAM NO NUMERO(OBVIO)
   */
   function retMascaraNumerica($valor, $num)
   {
      settype($valor, "string");
      mascaraNumerica($valor, $num);
      return $valor;
   }

  /*
     FUNCAO PARA MONTAR O OBJ HELP NO HTML
     MONTA UMA TAG DE IMAGEM COM UM LINK SOBRE ELA
     O HELP COM UMA D