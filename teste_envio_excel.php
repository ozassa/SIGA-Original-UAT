<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8">-->
<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>-->

<?php

 header ("Content-Type: application/xls; charset =iso-8859-1"); 
 header ("Content-Disposition: attachment; filename=Dados_Rel_".date("dmY")."-".time("His").".xls"); 
 ?>
  
  <table>
      <thead>
          <th>Teste1</th>
          <th>Teste2</th>
          <th>Teste3</th>
      </thead>
      <tbody>
         <tr>
            <td>Teste1</td>
            <td>Teste2</td>
            <td>Teste3</td>
         </tr>
      
      </tbody>
      
  
  </table>
  
 </head>
 </html>
 