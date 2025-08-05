<?php
   $uid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : null;
   
   if($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B'){
	   if ($dataState == 1) {
		   echo "Preencher";
	   }else if ($dataState == 2 || $dataState == 3 ) {
		   echo "OK";
	   }else if ($dataState == 4) {
		   echo "OK";
	   }
   }else{
	   if ($dataState == 1) {
		   echo '<a href="'.$root.'role/inform/Inform.php?comm=generalInformation&idInform='.$idInform.'&idNotification='.$idNotification.'&volta='.$volta.'&hc_cliente=N&tipo_apolice='.$tipo_apolice.'">Preencher</a>';
	   }else if ($dataState == 2){
	       echo "Revisar";
       }else if ($dataState == 3 || $dataState == 4 ) {
		   echo "OK";
	   }
	   
	   
   }
?>