<?php  session_register ("uid");

  $uid = $_SESSION["uid"];

     if ($dataState == 2) {
         echo "revisar";
     }else if ($dataState == 3) {  
         echo (($uid > 0) ? "Revisar" : "OK");
     }
	 
?>
