<?php  // criado Hicom (Gustavo) - 17/01/05
  	$sql = "	INSERT INTO ChangeGeneralInf (
  					idInform,
  					tel,
  					fax,
  					email,
  					contact,
  					ocupationContact,
  					emailContact,
  					chargeAddress,
  					chargeAddressNumber,
  					chargeCity,
  					chargeUf,
  					chargeCep,
  					idUserChangeGeneralInf,
  					dateChangeGeneralInf,
  					chargeAddressComp )
  				VALUES (
  					$idInform,
  					'$tel',
  					'$fax',
  					'$email',
  					'$contact',
  					'$ocupationContact',
  					'$emailContact',
  					'$chargeAddress',
  					'$chargeAddressNumber',
  					'$chargeCity',
  					'$chargeUf',
  					'$chargeCep',
  					$userID,
  					getdate(),
  					'$chargeAddressComp' )	";
  	
	$cur=odbc_exec($db, $sql);
?>