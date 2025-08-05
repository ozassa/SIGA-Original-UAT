<?php
include_once('../../../navegacao.php');
?>
<div class="conteudopagina">

	<script language=javascript src="<?php echo $root ?>scripts/utils.js"></script>
	<script language=javascript src="<?php echo $root ?>scripts/calendario.js"></script>

	<?php

	$msg = "";



	$idInform = $_REQUEST['idInform'];
	$idNotification = $_REQUEST['idNotification'];
	$idDVE = $_REQUEST['idDVE'];
	$geradocs = $_REQUEST['geradocs'];
	$enviar = $_REQUEST['enviar'];
	$hcrole = $_REQUEST['hcrole'];



	//obtem os dados da notificaçao
	
	$wstr = "SELECT getdate() as data_hoje, i.cnpj, i.respName, i.ocupation, 
            a.idInform, a.n_Apolice, a.seq_dve, a.dt_periodo_ini, a.dt_periodo_fim, 
            a.fl_doc, a.pth_doc, a.pth_doc_banco, 
            a.nu_envio, a.state, a.idDVE, i.name, i.startValidity, i.endValidity, i.Ga, i.prodUnit 
         FROM DVE_16dia a, Inform i 
         WHERE a.idNotification = ? AND a.idInform = i.id";

	// Preparação da consulta
	$stmt = odbc_prepare($db, $wstr);

	// Execução da consulta com o parâmetro protegido contra injeção
	odbc_execute($stmt, [$idNotification]);

	$t = $stmt;

	odbc_free_result($stmt);


	if (odbc_fetch_row($t)) {
		$achou = true;
	} else {
		$achou = false;
	}



	if ($achou) {
		$hc_idInform = trim("" . odbc_result($t, "idInform"));
		$hc_n_Apolice = trim("" . odbc_result($t, "n_Apolice"));
		$hc_n_Apolice = sprintf("062%06d", odbc_result($t, "n_Apolice"));
		$prod = odbc_result($t, 'prodUnit');
		// $apolice = sprintf("062%06d", odbc_result($y, 1));
		if ($prod != 62) {
			$hc_n_Apolice .= "/$prod";
		}

		$hc_seq_dve = trim("" . odbc_result($t, "seq_dve"));
		$hc_dt_periodo_ini = trim("" . odbc_result($t, "dt_periodo_ini"));
		$hc_dt_periodo_fim = trim("" . odbc_result($t, "dt_periodo_fim"));
		$hc_fl_doc = trim("" . odbc_result($t, "fl_doc"));
		$hc_pth_doc = trim("" . odbc_result($t, "pth_doc"));
		$hc_pth_doc_banco = trim("" . odbc_result($t, "pth_doc_banco"));
		//$hc_fl_visualizado = trim("" . odbc_result($t, "fl_visualizado")); 
		//$hc_fl_visualizado_banco  = trim("" . odbc_result($t, "fl_visualizado_banco")); 
		$hc_nu_envio = trim("" . odbc_result($t, "nu_envio"));
		$hc_state = trim("" . odbc_result($t, "state"));
		$idDVE = trim("" . odbc_result($t, "idDVE"));
		$Ga = odbc_result($t, "Ga");
		if ($idDVE == "") {
			$idDVE = 0;
		}

		$hc_name = trim("" . odbc_result($t, "name"));
		$hc_startValidity = ymd2dmy(trim("" . odbc_result($t, "startValidity")));
		$hc_endValidity = ymd2dmy(trim("" . odbc_result($t, "endValidity")));

		$hc_data_hoje = ymd2dmy(trim("" . odbc_result($t, "data_hoje")));
		$hc_respName = trim("" . odbc_result($t, "respName"));
		$hc_ocupation = trim("" . odbc_result($t, "ocupation"));

		$hc_cnpj = trim("" . odbc_result($t, "cnpj"));
		?>

		<ul>
			<li class="campo2colunas">
				<label>Segurado</label>
				<?php echo htmlspecialchars($hc_name, ENT_QUOTES, 'UTF-8'); ?>
			</li>

			<li class="campo2colunas">
				<label>Ap&oacute;lice n&ordm;</label>
				<?php echo htmlspecialchars($hc_n_Apolice, ENT_QUOTES, 'UTF-8'); ?>
			</li>

			<li class="campo2colunas">
				<label>Vig&ecirc;ncia</label>
				<?php echo htmlspecialchars($hc_startValidity, ENT_QUOTES, 'UTF-8') . ' &agrave; ' . htmlspecialchars($hc_endValidity, ENT_QUOTES, 'UTF-8'); ?>
			</li>
			<li class="campo2colunas">
				<label>Per&iacute;odo de Declara&ccedil;&atilde;o</label>
				<?php echo htmlspecialchars($hc_dt_periodo_ini, ENT_QUOTES, 'UTF-8') . ' &agrave; ' . htmlspecialchars($hc_dt_periodo_fim, ENT_QUOTES, 'UTF-8') . '&nbsp;(' . htmlspecialchars($hc_seq_dve, ENT_QUOTES, 'UTF-8') . '. &ordf; DVE)'; ?>
			</li>
		</ul>

		<div style="clear:both">&nbsp;</div>
		<?php

		//echo $geradocs;
	
		//------------------------------------------------------------------------------  
	
		if ($enviar == "S") {
			//envia email para o cliente e os contatos
			$to = "";
			$query = "SELECT i.emailContact, i.name FROM Inform i WHERE i.id = ?";

			$stmt = odbc_prepare($db, $query);

			odbc_execute($stmt, [$idInform]);

			$t = $stmt;

			odbc_free_result($stmt);
			$emailContact = trim(odbc_result($t, 1));
			if ($emailContact) {
				$to .= "$emailContact";
			}
			$query = "SELECT email FROM Contact WHERE idInform = ? AND notificationForChangeCredit = 1";

			// Preparação da consulta
			$stmt = odbc_prepare($db, $query);

			// Execução da consulta com o parâmetro protegido contra injeção
			odbc_execute($stmt, [$idInform]);

			$not = $stmt;
			odbc_free_result($stmt);
			while (odbc_fetch_row($not)) {
				$email = trim(odbc_result($not, 1));
				if ($email != "") {
					if ($to != "") {
						$to .= ", $email";
					} else {
						$to .= "$email";
					}
				}
			}

			// atualizar o banco com o endereço;
			if ($to != "") {
				$wstr = "UPDATE DVE_16dia_doc SET email = ? WHERE idNotification = ? AND tp_doc = '0'";

				// Preparação da consulta
				$stmt = odbc_prepare($db, $wstr);

				// Execução da consulta com os parâmetros protegidos contra injeção
				odbc_execute($stmt, [$to, $idNotification]);

				// Liberando o statement ODBC
	
				$aux = $stmt;
				odbc_free_result($stmt);
			} else {
				$msg = $msg . "<font color=red>E-mail do Cliente não encontrado!</font><br>";
			}

			// BANCO DO BRASIL
	
			// Selecona os bancos  
			// Declaração da consulta SQL com placeholders
			$wstr = "SELECT db.id, db.idBanco, db.pth_doc, b.name, b.idUser, u.email 
         FROM DVE_16dia_doc db
         JOIN Banco b ON db.idBanco = b.id
         JOIN Users u ON b.idUser = u.id
         WHERE db.idNotification = ? 
         AND db.tp_doc = '1'";

			// Preparação da consulta
			$stmt = odbc_prepare($db, $wstr);

			// Execução da consulta com o parâmetro protegido contra injeção
			odbc_execute($stmt, [$idNotification]);

			// Copiando os resultados para a variável $to
			$to = "";
			$not = $stmt;

			// Liberando o statement ODBC
			odbc_free_result($stmt);

			if (odbc_fetch_row($not)) {
				$email = trim(odbc_result($not, "email"));
				$id_doc = trim(odbc_result($not, "id"));
				if ($email != "") {
					$to .= "$email";
				}

				// Declaração da consulta SQL com placeholders
				$wstr = "SELECT 
            c.id AS id_cessao, 
            c.idAgencia AS id_agencia, 
            a.id AS id_agencia, 
            a.idNurim, 
            a.idBanco AS id_banco, 
            b.id AS id_banco, 
            b.name AS nome_banco, 
            b.idUser AS id_user_banco, 
            u.email 
         FROM 
            CDBB c 
         JOIN 
            Agencia a ON c.idAgencia = a.id 
         JOIN 
            Banco b ON a.idBanco = b.id 
         JOIN 
            UsersNurim un ON a.idNurim = un.idNurim 
         JOIN 
            Users u ON un.idUser = u.id 
         WHERE 
            c.status = 2 
         AND 
            c.idInform = ?";

				// Preparação da consulta
				$stmt = odbc_prepare($db, $wstr);

				// Execução da consulta com o parâmetro protegido contra injeção
				odbc_execute($stmt, [$hc_idInform]);

				// Copiando os resultados para a variável $hc_not
				$hc_not = $stmt;

				// Liberando o statement ODBC
				odbc_free_result($stmt);


				while (odbc_fetch_row($hc_not)) {
					$email = trim(odbc_result($hc_not, "email"));
					if ($email != "") {
						if ($to == "") {
							$to .= "$email";
						} else {
							$to .= ", $email";
						}
					}
				}

				// Blz, temos todos os to's
				// atualizar o banco com o endereço;
				if ($to != "") {
					// Declaração da consulta SQL com placeholders
					$wstr = "UPDATE DVE_16dia_doc 
		         SET email = ? 
		         WHERE idNotification = ? AND tp_doc = '1' AND id = ?";

					// Preparação da consulta
					$stmt = odbc_prepare($db, $wstr);

					// Execução da consulta com os parâmetros protegidos contra injeção
					odbc_execute($stmt, [$to, $idNotification, $id_doc]);

					$aux = $stmt;

					// Liberando o statement ODBC
					odbc_free_result($stmt);

				} else {
					$msg = $msg . "<font color=red>E-mail do BB não encontrado!</font><br>";
				}
				//echo "Gravei $to <br>"; 		 
			}

			//---  FIM BANCO DO BRASIL ----
//--------------------------------------------------------------------
	
			// BANCO OUTROS
	
			// Selecona os bancos  
// Declaração da consulta SQL com placeholders
			$wstr = "SELECT db.id, db.idBanco, db.pth_doc, b.name, b.idUser, u.email 
         FROM DVE_16dia_doc db
         JOIN Banco b ON db.idBanco = b.id
         JOIN Users u ON b.idUser = u.id
         WHERE db.idNotification = ? 
         AND db.tp_doc = '2'";

			// Preparação da consulta
			$stmt = odbc_prepare($db, $wstr);

			// Execução da consulta com o parâmetro protegido contra injeção
			odbc_execute($stmt, [$idNotification]);

			// Copiando os resultados para a variável $not
			$not = $stmt;

			// Liberando o statement ODBC
			odbc_free_result($stmt);

			$to = "";
			while (odbc_fetch_row($not)) {
				$to = "";
				$email = trim(odbc_result($not, "email"));
				$id_doc = trim(odbc_result($not, "id"));

				if ($email != "") {
					$to .= "$email";
				}


				//echo "outros $id_doc $to <br>";
	
				// Blz, temos todos os to's
				// atualizar o banco com o endereço;
				if ($to != "") {
					// Declaração da consulta SQL com placeholders
					$wstr = "UPDATE DVE_16dia_doc 
         SET email = ? 
         WHERE idNotification = ? AND tp_doc = '2' AND id = ?";

					// Preparação da consulta
					$stmt = odbc_prepare($db, $wstr);

					// Execução da consulta com os parâmetros protegidos contra injeção
					odbc_execute($stmt, [$to, $idNotification, $id_doc]);

					$aux = $stmt;

					// Liberando o statement ODBC
					odbc_free_result($stmt);

				} else {
					$msg = $msg . "<font color=red>E-mail do " . trim(odbc_result($not, "name")) . " não encontrado!</font><br>";
				}
				//echo "GRAVEI $id_doc $to <br>";   		 
	


			}

			//----FIM OUTROS BANCOS -------	  	  
	

			// Envia os e-mails
			// Declaração da consulta SQL com placeholders
			$wstr = "SELECT db.id, db.idBanco, db.pth_doc, db.Descricao, db.tp_doc, db.email
         FROM DVE_16dia_doc db
         WHERE db.idNotification = ?";

			// Preparação da consulta
			$stmt = odbc_prepare($db, $wstr);

			// Execução da consulta com o parâmetro protegido contra injeção
			odbc_execute($stmt, [$idNotification]);

			// Copiando os resultados para a variável $not
			$not = $stmt;

			// Liberando o statement ODBC
			odbc_free_result($stmt);

			$to = "";
			$enviado = false;
			while (odbc_fetch_row($not)) {
				$to = trim(odbc_result($not, "email"));
				$pth_doc = trim(odbc_result($not, "pth_doc"));
				$e_name = trim(odbc_result($not, "Descricao"));
				$e_tp_doc = trim(odbc_result($not, "tp_doc"));
				$id_doc = trim(odbc_result($not, "id"));

				if ($e_tp_doc == "0") {
					$e_name = $hc_name;
				}

				if ($to != "") {
					// Envia
					$msgEmail = "<font class=texto><br>Prezado Segurado,\r\n";
					$msgEmail = $msgEmail . "<br><br><br>Informamos que o envio da DVE ao SBCE encontra-se em atraso conforme documento suspensão de garantia.\r\n";
					$msgEmail = $msgEmail . "<br><br>Faça o download do documento de suspensão de garantia clicando no seguinte link: <a href='http://$SERVER_NAME" . $root . "download/" . $pth_doc . "'>documento suspensão de garantia</a>.\r\n";

					$msgEmail = $msgEmail . "<br><br><br> Atenciosamente, <br><br><br> SBCE\r\n";
					//modificado por Michel Saddock 10/08/2006
					$msgEmail = $msgEmail . "<br><br><br><center><b>Caso a declaração do Volume de Exportação já tenha sido enviada, favor desconsiderar este e-mail.</b></center>\r\n";

					$headers_hc = "MIME-Version: 1.0\r\n";
					$headers_hc .= "Content-type: text/html; charset=iso-8859-1\r\n";

					/* additional headers */
					//$headers_hc .= "To: " . $to . "\r\n";
					$headers_hc .= "From: credito@sbce.com.br\r\n";

					require_once("../../MailSend.php");

					$mail->From = "siex@cofacedobrasil.com"; // Seu e-mail
					$mail->FromName = "Credito"; // Seu nome 
					// Define os destinatário(s)
	
					$mail->AddAddress(trim($to));

					$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
					$mail->Subject = trim($e_name); // Assunto da mensagem
					$mail->Body = $msgEmail;
					$enviadox = $mail->Send();   // envia o email
					$mail->ClearAllRecipients();
					$mail->ClearAttachments();

					// Exibe uma mensagem de resultado
					if ($enviadox) {
						if ($e_tp_doc == "0") {
							$enviado = true;
						}
						// Declaração da consulta SQL com placeholders
						$wstr = "UPDATE DVE_16dia_doc 
				         SET fl_enviado = 'S', dt_envio = getdate() 
				         WHERE id = ?";

						$stmt = odbc_prepare($db, $wstr);

						odbc_execute($stmt, [$id_doc]);

						$aux = $stmt;

						odbc_free_result($stmt);

					} else {
						$msg = $msg . "<font color=red>Problemas no envio do e-mail para " . $e_name . "</font><br>";
					}


					/*
														   if (!mail(trim($to), trim($e_name), $msgEmail, $headers_hc)){
																$msg = $msg . "<font color=red>Problemas no envio do e-mail para " . $e_name . "</font><br>";
														   }else{
															  if ($e_tp_doc == "0"){
																 $enviado = true;
															  }	
																		   
															  $wstr = " UPDATE DVE_16dia_doc set fl_enviado = 'S', dt_envio = getdate() WHERE  id=$id_doc ";
															  $aux = odbc_exec($db, $wstr);	
														   
														   }
														   */

				}


			}


			if ($enviado) {

				// Declaração da consulta SQL com placeholders
				$wstr = "UPDATE DVE_16dia_doc 
         SET fl_enviado = 'S', dt_envio = getdate() 
         WHERE id = ?";

				// Preparação da consulta
				$stmt = odbc_prepare($db, $wstr);

				// Execução da consulta com o parâmetro protegido contra injeção
				odbc_execute($stmt, [$id_doc]);

				// Liberando o statement ODBC
				odbc_free_result($stmt);

			}


		}




		//------------------------------------------------------------------------------  
	
		if ($geradocs == "S") {

			if ($Ga == "1") { //Ga
				$texto = " nos termos do item F3.05 da cláusula 8ª das Condições Particulares.";
			} else { //RC
				$texto = " nos termos do item 27.2.1 da cláusula 27 das Condições Gerais.";
			}

			$prefix = "";

			$key = time() . session_id();
			require_once("../../pdfConf.php");

			$prefix = $pdfDir . $key;
			//$prefix = "d:\";   
			//echo "--" . $prefix . "--";
			//die();
	
			if (!file_exists($prefix . "DVEaviso16.pdf")) {

				/*        
									   $h = new Java ('java.util.HashMap');
									   //  if ($h != null) echo "<pre>h OK!";
									   $h->put('key', $prefix . "DVEaviso16.pdf");
									   $h->put('dir', $pdfDir);
									   $h->put('data_hoje', $hc_data_hoje);
									   $h->put('inf_nome', $hc_name);
									   $h->put('inf_contato', $hc_respName);
									   $h->put('inf_cargo', $hc_ocupation);
									   $h->put('apolice', $hc_n_Apolice);
									   $h->put('dve_ini', $hc_dt_periodo_ini);
									   $h->put('dve_fim', $hc_dt_periodo_fim);
									   $h->put('cnpj', $hc_cnpj);
									   $h->put('texto', $texto);
									   $h->put('bancobrasil', '0');
									   //$h->put('banco', "Banco Mercantil do Brasil");

									   $dve16 = new Java ("DVEaviso16", $h);
									  
									  */

				if ($dve16 == null) {
					$msg = $msg . "<BR><font color=red>Não foi possível gerar aviso para o cliente!</font>";
					//echo "<h1>Não foi possível gerar aviso para o cliente!</h1>";
				} else {

					//echo "vou criar documento...<br>";
	

					//$dve16->generate();
	

					$query = "DELETE FROM DVE_16dia_doc WHERE idNotification = ? AND tp_doc = ?";
					$stmt = odbc_prepare($db, $query);
					odbc_execute($stmt, [$idNotification, '0']);

					//echo "apaguei documento...<br>";
	
					if (file_exists($prefix . "DVEaviso16.pdf")) {
						// apaga documentos anteriores
						// Dos clientes
						$query = "DELETE FROM DVE_16dia_doc WHERE idNotification = ? AND tp_doc = ?";
						$r = odbc_prepare($db, $query);
						odbc_execute($r, [$idNotification, '0']);
						// Insere documento do cliente
						$query = "INSERT INTO DVE_16dia_doc (
								idInform,
								idNotification,
								idDVE,
								tp_doc,
								pth_doc,
								Descricao
							) VALUES (
								?, ?, ?, ?, ?, ?
							)";

						$r = odbc_prepare($db, $query);
						odbc_execute($r, [
							$idInform,
							$idNotification,
							$idDVE,
							'0',
							$key . "DVEaviso16.pdf",
							'Cliente'
						]);


					}


				}

				// apaga documentos anteriores
				// Do BB
				$query = "DELETE FROM DVE_16dia_doc WHERE idNotification = ? AND tp_doc = '1'";
				$r = odbc_prepare($db, $query);
				odbc_execute($r, [$idNotification]);
				odbc_free_result($r);
				// Verifica se tem cessão Banco do Brasil
				// Declaração da consulta SQL com placeholders
				$wstr = "SELECT DISTINCT 
            a.idBanco AS id_banco, 
            b.id AS id_banco, 
            b.name AS nome_banco, 
            b.idUser AS id_user_banco 
         FROM 
            CDBB c 
         JOIN 
            Agencia a ON c.idAgencia = a.id 
         JOIN 
            Banco b ON a.idBanco = b.id 
         WHERE 
            c.status = 2 
         AND 
            c.idInform = ?";

				// Preparação da consulta
				$stmt = odbc_prepare($db, $wstr);

				// Execução da consulta com o parâmetro protegido contra injeção
				odbc_execute($stmt, [$hc_idInform]);

				// Copiando os resultados para a variável $t
				$t = $stmt;

				// Liberando o statement ODBC
				odbc_free_result($stmt);


				while (odbc_fetch_row($t)) {
					$hc_nome_banco = trim("" . odbc_result($t, "nome_banco"));
					$hc_id_banco = trim("" . odbc_result($t, "id_banco"));

					/*

												  $h = new Java ('java.util.HashMap');
												 //$h->put('key',  "d:/" . $hc_id_banco . "DVEaviso16.pdf");
												  //$h->put('dir', "/");
												  $h->put('key', $prefix . $hc_id_banco . "DVEaviso16.pdf");
												  $h->put('dir', $pdfDir);
												 
												  $h->put('data_hoje', $hc_data_hoje);
												  $h->put('inf_nome', $hc_name);
												  $h->put('inf_contato', $hc_respName);
												  $h->put('inf_cargo', $hc_ocupation);
												  $h->put('apolice', $hc_n_Apolice);
												  $h->put('dve_ini', $hc_dt_periodo_ini);
												  $h->put('dve_fim', $hc_dt_periodo_fim);
												  $h->put('cnpj', $hc_cnpj);
												  $h->put('banco', $hc_nome_banco);
												  $h->put('bancobrasil', '1');

												  $dve16banco = new Java ("DVEaviso16", $h);
												  */

					if ($dve16banco == null) {
						$msg = $msg . "<BR><font color=red>Não foi possível gerar aviso para o Banco do Brasil!</font>";
						//echo "<h1>Não foi possível gerar aviso para o Banco do Brasil!</h1>";
	
					} else {

						//$dve16banco->generate();
	
						if (file_exists($prefix . $hc_id_banco . "DVEaviso16.pdf")) {
							// Insere documento do cliente
							$query = "INSERT INTO DVE_16dia_doc (
                idInform,
                idNotification,
                idDVE,
                tp_doc,
                pth_doc,
                Descricao,
                idBanco
            ) VALUES (?, ?, ?, '1', ?, ?, ?)";

							$wstr = odbc_prepare($db, $query);

							odbc_execute($wstr, [
								$idInform,
								$idNotification,
								$idDVE,
								$key . $hc_id_banco . "DVEaviso16.pdf",
								$hc_nome_banco,
								$hc_id_banco
							]);

							odbc_free_result($wstr);


						}


					}

				}

				// apaga documentos anteriores
				// Do outros
				// Declaração da consulta SQL com placeholders
				$query = "DELETE FROM DVE_16dia_doc WHERE idNotification = ? AND tp_doc = '2'";

				// Preparação da consulta
				$stmt = odbc_prepare($db, $query);

				// Execução da consulta com o parâmetro protegido contra injeção
				odbc_execute($stmt, [$idNotification]);

				// Liberando o statement ODBC
				odbc_free_result($stmt);



				// Verifica se tem cessão OUTROS BANCOS
				// Declaração da consulta SQL com placeholders
				$wstr = "SELECT DISTINCT 
            c.id AS id_cessao, 
            b.id AS id_banco, 
            b.name AS nome_banco, 
            b.idUser AS id_user_banco 
         FROM 
            CDParc c 
         JOIN 
            Banco b ON c.idBanco = b.id 
         WHERE 
            c.status = 2 
         AND 
            c.idInform = ?";

				// Preparação da consulta
				$stmt = odbc_prepare($db, $wstr);

				// Execução da consulta com o parâmetro protegido contra injeção
				odbc_execute($stmt, [$hc_idInform]);

				// Copiando os resultados para a variável $t
				$t = stmt;

				// Liberando o statement ODBC
				odbc_free_result($stmt);

				while (odbc_fetch_row($t)) {
					$hc_nome_banco = trim("" . odbc_result($t, "nome_banco"));
					$hc_id_banco = trim("" . odbc_result($t, "id_banco"));
					//echo $hc_dt_periodo_ini . "<BR>";
					//echo $hc_dt_periodo_fim . "<BR>";
					/*
															  $h = new Java ('java.util.HashMap');
															 //$h->put('key',  "d:/" . $hc_id_banco . "DVEaviso16.pdf");
															 //$h->put('dir', "/");
															 $h->put('key', $prefix . $hc_id_banco . "DVEaviso16.pdf");
															 $h->put('dir', $pdfDir);

															 $h->put('data_hoje', $hc_data_hoje);
															 $h->put('inf_nome', $hc_name);
															 $h->put('inf_contato', $hc_respName);
															 $h->put('inf_cargo', $hc_ocupation);
															 $h->put('apolice', $hc_n_Apolice);
															 $h->put('dve_ini', $hc_dt_periodo_ini);
															 $h->put('dve_fim', $hc_dt_periodo_fim);
															 $h->put('cnpj', $hc_cnpj);
															 $h->put('banco', $hc_nome_banco);
															 $h->put('bancobrasil', '0');
												  
														 $dve16banco = new Java ("DVEaviso16", $h);
													 */
					if ($dve16banco == null) {
						$msg = $msg . "<BR><font color=red>Não foi possível gerar aviso para outros bancos (" . $hc_nome_banco . ")!</font>";

					} else {
						//$dve16banco->generate();
	


						if (file_exists($prefix . $hc_id_banco . "DVEaviso16.pdf")) {
							// Insere documento do cliente
	
							// Declaração da consulta SQL com placeholders
							$wstr = "INSERT INTO DVE_16dia_doc (
            idInform, 
            idNotification, 
            idDVE, 
            tp_doc, 
            pth_doc, 
            Descricao, 
            idBanco
         ) VALUES (
            ?, ?, ?, '2', ?, ?, ?
         )";

							// Preparação da consulta
							$stmt = odbc_prepare($db, $wstr);

							// Execução da consulta com os parâmetros protegidos contra injeção
							odbc_execute($stmt, [
								$idInform,
								$idNotification,
								$idDVE,
								$key . $hc_id_banco . "DVEaviso16.pdf",
								$hc_nome_banco,
								$hc_id_banco
							]);

							// Liberando o statement ODBC
							odbc_free_result($stmt);


						}


					}

				}





			}

			if ($msg == "") {
				$msg = "<BR><h1>Documentos gerados com sucesso!</h1>";
				$msg = "<BR>Documentos gerados com sucesso!";
			}


		}

		//------------------------------------------------------------------------------  
	
		//------------------------------------------------------------------------------------
	
		//Mostra osdocumentos existentes
	
		// Declaração da consulta SQL com placeholders
		$wstr = "SELECT db.pth_doc, db.tp_doc, db.id AS idDoc, db.Descricao, db.email, db.fl_enviado, db.dt_envio
         FROM DVE_16dia_doc db
         WHERE db.idNotification = ?
         ORDER BY db.tp_doc, db.Descricao";

		// Preparação da consulta
		$stmt = odbc_prepare($db, $wstr);

		// Execução da consulta com o parâmetro protegido contra injeção
		odbc_execute($stmt, [$idNotification]);

		// Copiando os resultados para a variável $t
		$t = $stmt;

		// Liberando o statement ODBC
		odbc_free_result($stmt);


		$hc_acc = 0;
		$enviou_cli = false;
		while (odbc_fetch_row($t)) {

			if ($hc_acc == 0) {

				$wstr = "UPDATE DVE_16dia 
         SET fl_doc = 'S' 
         WHERE idNotification = ?";

				$stmt = odbc_prepare($db, $wstr);

				odbc_execute($stmt, [$idNotification]);

				odbc_free_result($stmt);

				$hc_fl_doc = "S";

				?>
				<label>Documento(s) gerado(s): (clique para visualizar)</label>
			<?php }

			$en_email = trim("" . odbc_result($t, "email"));
			$en_fl_enviado = trim("" . odbc_result($t, "fl_enviado"));
			$en_dt_envio = ymd2dmy(trim("" . odbc_result($t, "dt_envio")));

			?>

			<ul><a href="../../download/<?php echo trim("" . odbc_result($t, "pth_doc")); ?>"
					target="gpc"><?php echo trim("" . odbc_result($t, "Descricao")); ?></a>

				<?php if ($en_fl_enviado == "S") {
					$enviou_cli = true;
					?>
					<br>Enviado em: <?php echo htmlspecialchars($en_dt_envio, ENT_QUOTES, 'UTF-8'); ?>.<br>Para:
					<?php echo htmlspecialchars($en_email, ENT_QUOTES, 'UTF-8'); ?>.
				</ul><br>
			<?php } else {

					echo "</ul>";

				}

				$hc_acc = $hc_acc + 1;
		}


	} else {
		echo "<label><font color=red>Erro: Dados n&atilde;o encontrados!</font></label>";
	}



	?>



	<form name="volta" action="../access/Access.php">
	</form>

	<form name="geradoc" action="Dve.php">
		<input type="hidden" name="comm" value="entregavenciada">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="geradocs" value="S">
	</form>



	<form name="apagar" action="Dve.php">
		<input type="hidden" name="comm" value="entregavenciadaok">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="hcrole" value="16">
	</form>



	<form name="enviar" action="Dve.php">
		<input type="hidden" name="comm" value="entregavenciada">
		<input type="hidden" name="idNotification"
			value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
		<input type="hidden" name="enviar" value="S">
	</form>

	<label><?php echo ($msg); ?></label>


	<div class="barrabotoes">
		<button onClick="document.volta.submit()" class="botaovgm">Voltar</button>
		<?php if ($achou) { ?>
			<button onClick="document.geradoc.submit()" class="botaoagm">Gerar doc.</button>
		<?php } else {
			?>
			<button onClick="document.apagar.submit()" class="botaovgm">Apagar</button>
		<?php }
		?>


		<?php if ($achou) {
			?>
			<?php if ($hc_fl_doc == "S") {
				?>
				<!--<input type=button onClick="document.visu.submit()" value="Visualizar" class="sair">-->

			<?php }
			//if ($hc_fl_visualizado=="S")
			if ($hc_fl_doc == "S") {
				if ($enviou_cli) {
					?>
					<button onClick="document.enviar.submit()" class="botaoagg">Enviar novamente</button>
				<?php } else {
					?>
					<button onClick="document.enviar.submit()" class="botaoagm">Enviar</button>
				<?php }
			}
			?>

			<button onClick="document.apagar.submit()" class="botaovgm">Apagar</button>
		<?php }
		?>

	</div>





	<div style="clear:both">&nbsp;</div>

</div>