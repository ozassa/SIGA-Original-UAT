
<?php 
if(!isset($_SESSION)) {
  session_set_cookie_params([
    'secure' => true,
    'httponly' => true
]);
session_start();
}


   require_once("config.php");
   require_once("header.php");
   //require_once("main.php");
   //header("Content-Type: text/html; charset=ISO-8859-1",true);
   
    if(! $_SESSION['userID'] > 0){
	   $_SESSION['userID'] = '';
	   $_SESSION['nameUser']   = '';
	   $_SESSION['login']  = '';
	   $_SESSION['pefil']  = '';
	   $content = "../../../index.php?erro=2";
      ?> <script> window.location = '<?php echo $content?>'; </script> <?php
	   exit();
    }
  
   ?>
<div id="corpo">    
<?php
	$msg = isset($msg) ? $msg : '';
	$msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : $msg;
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : $msg;
	//$msg = str_replace('<', '', $msg);
	//$msg = str_replace('>', '', $msg);
	$msg = htmlspecialchars($msg);
  if($comm == 'japossuiapolice' || $comm == 'open'){
    $msg  = '';
	}
?>
		        <div id="ver" class="success" style="position:absolute; margin:0 0 0 -260px;left:50%; top: 200px; height:auto; overflow: auto; !important;width:420px; z-index:999; display:<?php if ($msg != '') echo 'block'; else echo 'none'; ?>;">
					 <div class="close" style="left:auto!important;right:5px;top:5px;" onClick="ver();">Fechar</div>
					     <br>
						 <div style="width:380px; position:relative;text-align:justify;">
						 <?php  

						 		if($msg!= ''){
								   echo $msg;
								}
								   
								$msg = '';
								unset($_SESSION['msg']);
								unset($_REQUEST['msg']);
								
						  ?>
						 </div>			   
				</div>
			   
				
				<div id="verErro" class="warning">
					 <div class="close" onClick="verErro();">Fechar</div>
				<label id="Meng"></label>
		</div>
<!-- <script type="text/javascript" charset="ISO-8859-1">-->
 <script type="text/javascript">
	      
			     
		
			  	  
	        function ver(){
				if (document.getElementById("ver").style.display == "block")
			        document.getElementById("ver").style.display = "none";
				else{
					document.getElementById("ver").style.display = "block";
				}
			}
			
			function verErro(str){
				 string =  '<div style="position:relative; top:-11px; text-align: right;"><a href="javascript:TINY.box.hide()"  class="linktexto"><img src="../../images/close.png" title="Fechar" border="0" width="15" heigth="15"></a></div>';
				 T$('verErro').onclick = TINY.box.show(string+'<label id="Meng">'+str+'</label><br>',0,0,0,2);
				
			}
			
				
						
			function verConfirm(str){
				    if(str == 1){
						f.submit();
						return true;
					}else{
						 string = '<img src="../../../images/asking.png" title="Fechar" border="0" width="30" heigth="30">';
						 string1 = '<div style="text-align: center;">'+
									 '<button class="botaoagm" type="button" id="sim"  onClick="javascript: verConfirm(1); TINY.box.hide();">Sim</button>&nbsp;'+
									 '<button class="botaoagm" type="button" id="nao"  onClick="javascript:TINY.box.hide();">N&atilde;o</button>'+
									 '</div>';
									 
						 return  T$('verErro').onclick = TINY.box.show(string+'<br><label id="Meng">'+str+'</label><br>'+string1+'<br>',0,0,0,2);
					    
					}
					
					
					
					 
			}
			
			function vConfirm(msg,str){
				
				 string =  '<div style="text-align: center;">'+
				           '<a href="'+ str +'"><button class="botaoapm" type="button">Sim</button></a>'+
				           '<button class="botaovpm" type="button"  onClick="javascript:TINY.box.hide()">N&atilde;o</button></div>';


				 T$('verErro').onclick = TINY.box.show('<label id="Meng">'+msg+'</label><br><br>'+string,0,0,0,2);
			  
				
				
				
			}
			
			$(document).ready(function(){
				
				 if($('#ver').height() > 300){
					 //alert($('#ver').height());
					 $('#ver').css('height' , '300px');
					 $('#ver').css('overflow','auto');
				 }
			});
			
			
				
   </script> 
       
  
<?php   
  
 
   require_once($content);
  
?>
</div>  
<?php   
   require_once("footer.php");
?>
 

