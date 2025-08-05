<?php //Alterado HiCom mes 04

 	//####### ini ####### adicionado por eliel vieira - elumini - em 21/06/2007
 	//include_once "tree_view_box.php";
	//####### end ####### adicionado por eliel vieira - elumini - em 21/06/2007

?>

<div class="conteudopagina">
	<table summary="Submitted table designs" id="example">
		<thead>
			<tr>
				<th scope="col">Data</th>
				<th scope="col">Descri&ccedil;&atilde;o</th>
			</tr>
		</thead>	
		
		<tbody>
        	<?php 
		  	
		  	$i=0;
			
			$sql_lista = "SELECT DISTINCT
					n.id, 
					n.notification, 
					n.bornDate, 
					n.link
				FROM NotificationR n 
				INNER JOIN tp_notification_Role TNR ON TNR.tp_notification_id = n.tp_notification_id
				INNER JOIN UserRole UR ON UR.idRole = TNR.idRole 
				Inner Join Inform Inf On Inf.id = n.idInform 
				Inner Join Empresa E On E.i_Empresa = Inf.i_Empresa 
				WHERE ".$criterio.($junta != '' ? $junta : '')." 
							n.state = 1 and TNR.tp_notification_id in (6,7,43,44) AND UR.idUser = $userID
				ORDER BY n.bornDate DESC";
			$cur = odbc_exec($db,$sql_lista);
				
			While (odbc_fetch_row($cur)) {
				$i++;
				
				$idNotif = odbc_result($cur,1);
				$notif   = odbc_result($cur,2);
				$date    = odbc_result($cur,3);
				$link    = odbc_result($cur,4);
				  
				If($i % 2 == 0){
					$ver = 'class="odd"';
				}else{
					$ver = ''; 
				}
				
		  		?>
			  	<tr <?php echo $ver;?>>
					<td><?php echo  substr($date, 8, 2)."/".substr($date, 5, 2)."/".substr($date, 0, 4); ?></td>
					<td scope="row" id="r97"><a href="<?php echo  $link; ?>"><?php echo  $notif; ?></a></a></td>
			   	</tr>
			<?php } // while

			If ($i == 0) {
		      	?>
				<tr>
					<td colspan="2">Nenhuma notifica&ccedil;&atilde;o</td>
				</tr>
			<?php } // If
			
			?>
		</tbody>		
	</table>
	
	<div class="divisoria01">
	</div>
</div>