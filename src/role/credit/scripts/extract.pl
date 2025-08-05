#!/usr/bin/perl
# Extrai os dados dos importadores (versao Win)

#################################################################################
# pacote Temp: usado apenas para testes no linux, já q nao tem o modulo Win32::ODBC no esfiha
package Temp;
sub new{ return bless {}; }
sub Sql{ return 1; }
sub FetchRow{ return 1; }
sub Close{ return 1; }
sub Data{ return 1; }
sub RowCount{ return 1; }
#################################################################################

package main;

use funcoes;

@argv = &process(@ARGV);
&show_help($0) unless @argv;

if($temp){ # se estamos usando o pacote Temp
    print "using Temp...\n";
    $db = new Temp;
}else{
    eval 'use Win32::ODBC';
    # abre a conexao com o banco de dados
    eval "\$db = new Win32::ODBC(\"dsn=sbce;UID=sa;PWD=\")";
    if($@){ # se nao conseguiu abrir o BD, cai fora
	die "Nao consegui me conectar ao banco de dados: $DBI::errstr\n$@\n";
    }
}

#&update_ci_coface();

for $file (@argv){
    open FILE, $file or
	# se nao conseguir abrir o arquivo, nao aborta o programa,
	# apenas avisa e vai para o proximo arquivo
	do{
	    warn "Nao pude abrir o arquivo '$file': $!\n";
	    next;
	};

    unless($temp){
	open OUT, ">$file.saida.out" or die "ops: $!\n";
	select OUT;
    }

    $dummy = <FILE>; # ignora a primeira linha

    # le o arquivo e extrai os dados
    $_ = <FILE>;
    ($policy_number, $country_importer_id,
     $c_coface_imp, $order_number, $date,
     $time, $section_number, $fields_ref) = &separa_dados($_);

  OUTER: while(1){
      last OUTER unless $_;

      print "********************\n" if ($section_number == 1);
      print "* section: $section_number\n";

      ##########################################################################
      # Inicio do processo
      # busca o POLICY TYPE.
      # (policy type == BS1) -> segurado Notification ROLE 10
      # (policy type == DPP) -> segurado Notification ROLE 12

      # situação do segurado na COFACE
      if($section_number == 1){ # ph = policy holder
	  ($policy_type, $exportador) = &get_fields($fields_ref, [ "Policy Type",
								   "Policy Holder Trade Name" ]);
      }

      $_ = <FILE>;
      last OUTER unless $_;
      ($policy_number, $country_importer_id,
       $c_coface_imp, $order_number, $date,
       $time, $section_number, $fields_ref) = &separa_dados($_);

    MIDDLE: while(1){
	if($section_number == 2){
	    ($buyer_area_code, $country_name,
	     $buyer_ilkey1, $address1, $address2,
	     $name1, $name2, $new_city) = &get_fields($fields_ref,
						 [ "Buyer Area Code", "Country Name",
						   "Buyer Ilkey 1", "Address 1", "Address 2",
						   "Trade Name 1", "Trade Name 2", "Country Name"]);
	    if($address2){
		$new_address = $address2;
	    }else{
		$new_address = $address1;
	    }
	    if($name2){
		$new_name = $name2;
	    }else{
		$new_name = $name1;
	    }
	    print "* section: $section_number\n";

	    # hash com os valores atuais q serao utilizados
	    $current{c_coface_imp} = $c_coface_imp;
	    $current{country_importer_id} = $country_importer_id;
	    $current{policy_number} = $policy_number;
	}

	$_ = <FILE>;
	last OUTER unless $_;
	($policy_number, $country_importer_id,
	 $c_coface_imp, $order_number, $date,
	 $time, $section_number, $fields_ref) = &separa_dados($_);

	if($section_number == 10){
	    print "* section: $section_number\n";
	    ($buyer_country_number,
	     $buyer_number, $buyer_phone,
	     $buyer_country_name) = &get_fields($fields_ref,
						[ "Country Number",
						  "Buyer Number", "Buyer Phone Number",
						  "Buyer Country Name" ]);
	}

	$_ = <FILE>;
	last OUTER unless $_;
	($policy_number, $country_importer_id,
	 $c_coface_imp, $order_number, $date,
	 $time, $section_number, $fields_ref) = &separa_dados($_);

      INNER: while(1){
	  if($section_number == 3){
	      print "* section: $section_number\n";
	      ($amount_in_currency, $coeficient,
	       $currency, $date_of_request) = &get_fields($fields_ref,
							  [ "Amount of Request in Currency",
							    "Currency Coefficient", "Currency",
							    "Policy Holder Date of the Request" ]);
	      if($currency ne 'USD'){ # nao está em dolar
		  $now = `date +'%c'`;
		  chomp($now);
		  &add_log($file, "$now: Moeda não é dolar (Currency=$currency)");
	      }

	      # calcula o valor de credito requisitado
	      $requisicao = $amount_in_currency * (10 ** $coeficient);
	      # deixa o valor com espacos a cada 3 casas (1234567 -> 1 234 567)
	      #1 while($requisicao =~ s/(\d)(\d\d\d)(?!\d)/$1 $2/);

	      # transforma a data de yyyymmdd para dd/mm/yy
	      $date_of_request = &conserta_data($date_of_request);
	  }

	  $_ = <FILE>;
	  last OUTER unless $_;
	  ($policy_number, $country_importer_id,
	   $c_coface_imp, $order_number, $date,
	   $time, $section_number, $fields_ref) = &separa_dados($_);

	  if($section_number >= 0 && $section_number <= 2){
	      $credito_solicitado = $requisicao;
	      # deixa o valor com ponto a cada 3 casas (1234567 -> 1.234.567)
	      1 while($credito_solicitado =~ s/(\d)(\d\d\d)(?!\d)/$1.$2/);
	      open F, ">>$file.nao_concedido" or die "S!\n";
	      print F "----\nNão foi encontrada linha correspondente à decisão de conceder ou não crédito para:\n";
	      print F "Exportador: $exportador /Importador:$name1 /Credito Solicitado: $credito_solicitado\n";
	      close F;
	  }

	  &label($section_number);

	  if($section_number == 6 or $section_number == 20){
	      print "* section: $section_number\n";
	      ($amount, $decision_type, $currency,
	       $coeficient, $date_of_decision,
	       $decision_number, $decision_ilkey2,
	       $overtrade_amount, $overtrade_date) = &get_fields($fields_ref,
								 [ "Amount of the Decision in Currency",
								   "Decision Type", "Currency",
								   "Currency Coefficient",
								   "Effect Date of the Decision",
								   "Decision Reference Number",
								   "Decision Ilkey 2",
								   "Overtrade Amount (if existing) in Currency",
								   "Date of Overtrade End" ]);
	      if($currency ne 'USD'){ # nao está em dolar
		  $now = `date +'%c'`;
		  chomp($now);
		  &add_log($file, "$now: Moeda não é dolar (Currency=$currency)");
	      }

	      # credito liberado pela COFACE.
	      $current_credit = $amount * (10 ** $coeficient);
	      # deixa o valor com espacos a cada 3 casas (1234567 -> 1 234 567)
	      #1 while($current_credit =~ s/(\d)(\d\d\d)(?!\d)/$1 $2/);

	      $date_of_decision = &conserta_data($date_of_decision);

 	      if($overtrade_amount > 0){
		  $overtrade = $overtrade_amount * (10 ** $coeficient);
		  # deixa o valor com espacos a cada 3 casas (1234567 -> 1 234 567)
		  #1 while($overtrade =~ s/(\d)(\d\d\d)(?!\d)/$1 $2/);

		  $overtrade_date = &conserta_data($overtrade_date);
	      }

	      # deixa o telefone com espacos (passa de '012345' para '01 23 45')
	      #1 while($underwriter_phone =~ s/(\d\d)(?=\d)/$1 /);

	      $_ = <FILE>;
	      last OUTER unless $_;
	      ($policy_number, $country_importer_id,
	       $c_coface_imp, $order_number, $date,
	       $time, $section_number, $fields_ref) = &separa_dados($_);

	      $comment = '';
	      if($section_number == 8){
		  do{ # loop para o caso do comentario continuar por varias linhas
		      print "* section: $section_number\n";
		      my ($com) = &get_fields($fields_ref, [ "Underwriter's Comments" ]);
		      $comment .= $com;
		      $_ = <FILE>;
		      last OUTER unless $_;
		      ($policy_number, $country_importer_id,
		       $c_coface_imp, $order_number, $date,
		       $time, $section_number, $fields_ref) = &separa_dados($_);
		  }while($section_number == 8);
	      }

	      ###################################################################
	      # começa outro exportador, outro importador, ou outra alteraçao de crédito
	      # deve gerar as notificacoes aqui...
	      # nao sei se o codigo abaixo está certo...

	      if($section_number == 1 || $section_number == 2 || $section_number == 3){
		  # id do importer
		  $importer_id = 0;
		  $q =
		      "SELECT Importer.id, Importer.state ".
		      "FROM Importer INNER JOIN ".
		      " Inform ON Importer.idInform=Inform.id INNER JOIN ".
		      " Country ON Importer.idCountry=Country.id ".
		      "WHERE Importer.c_Coface_Imp=$current{c_coface_imp} ".
		      "AND Inform.contrat=$current{policy_number} ".
		      "AND Country.code=$current{country_importer_id}";
		  &query($q);
		  $db->FetchRow;
		  ($importer_id, $importer_state) = $db->Data;

		  undef $credit_assigned;
		  if($importer_id){ # importador existe
		      &query("SELECT credit FROM ChangeCredit ".
			     "WHERE idImporter = $importer_id AND state <> 14 ".
			     "ORDER BY stateDate DESC ");
		      $db->FetchRow;
		      ($credit_assigned) = $db->Data;

		      if(! $credit_assigned){
			  &query("select Importer.limCredit from Importer where Importer.id = $importer_id");
			  $db->FetchRow;
			  ($credit_assigned) = $db->Data;
		      }

		      ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = localtime(time);
		      $add++;
		      $key_cookie = $sec.$min.$hour.$mday.$mon.$add;

		      #########################################################################
		      # Gera as notificacoes
		      # As notificacoes estao de acordo com as atuais?
		      # Existe algum padrao para os textos das notificacoes?
		      # Qual o sentido da vida?? :-)
		      &notification(int($decision_type), int($importer_id), $key_cookie,
				    $policy_type, $importer_state, $date_of_decision);
		  }else{ # importador nao existe
		      $not_found_msg =
			  "Não existe importador para CI Coface=$current{c_coface_imp}, ".
			  "código de país=$current{country_importer_id} e ".
			  "Inform.contrat=$current{policy_number}";
		      #print $not_found_msg, "\n";
		      &add_log($file, $not_found_msg);
		  }
	      }

	      &label($section_number);
	  }
      } # INNER
    } # MIDDLE
  } # OUTER
    close FILE;
    close OUT unless $temp;
}

$db->Close();

############################################################
# gera notificacao de acordo com o decision_type
sub notification{
    my ($decision_type, $importer_id, $key_cookie,
	$policy_type, $importer_state, $decision_date) = @_;
    my $msg;
    my $func = \&notification_limit;

    print "Decisao: $decision_type ";
    # Depedendo do $decision_type e do $policy_type, a notificação muda.
    if($decision_type == 2 or $decision_type == 3 or
       $decision_type == 4 or $decision_type == 5 or
       $decision_type == 6 or $decision_type == 11 or
       $decision_type == 13 or $decision_type == 21 or
       $decision_type == 28 or $decision_type == 17) {
	# credito zerado
	# no SIEX equivale ao 4.
	$type = 4;
	if($decision_type == 2){
	    print "Credito recusado para importer $importer_id\n";
	    $msg = "COFACE recusa o limite de crédito";
	}else{
	    print "Credito zerado para importer $importer_id\n";
	    $msg = "COFACE zera o limite de crédito";
	}

    }elsif($decision_type == 22 or $decision_type == 18 or $decision_type == 20) { # mantem decisão.
	# NO SIEX equivale ao 7.
	$type = 7;
	print "Mantem a decisão para importer $importer_id\n";
	$msg = "COFACE mantém a decisão";

	# credit_assigned e current_credit sao globais
    } elsif (($decision_type == 1 && $credit_assigned != $current_credit) or
	     $decision_type == 7) { # redução
	# NO SIEX equivale ao 5.
	$type = 5;
	print "Altera o limite para importer $importer_id\n";
	$msg = "COFACE altera de limite de crédito";

    } elsif ($decision_type == 1 && $credit_assigned == $current_credit) { # aprovado
	# no SIEX state 6 aprovado
	print "APROVADO para $importer_id\n";
	$type = 6;
	$msg = "COFACE aprova o limite de crédito";

    } elsif ($decision_type == 8) {
	# no SIEX state 3 mudanca de dados
	print "Mudanca de dados para importer $importer_id\n";
	$type = 3;
	$msg = "COFACE muda dados do importador";
	$func = \&notification_data;

    } else {
	print "Erro: subtipo nao determinado ($decision_type), importer: $importer_id\n";
	return;
    }

    &{ $func }($msg, $importer_id, $key_cookie, $type,
	       $policy_type, $importer_state, $decision_date);
}

######################################
sub separa_dados{
    my ($header, $data);
    my @header_lengths = (12, 3, 6, 2, 8, 6, 2);
    local $_ = shift;

    # separa o header dos dados
    # o header tem 39 caracteres, os 264 restantes sao dados
    if(/(.{39})(.{264})/){
	($header, $data) = ($1, $2);
    }

    # extrai dados de $header
    my ($policy_number, $country_number, $buyer_number,
	$order_number, $date, $time, $section_number) = &get_data($header,
								  [ @header_lengths ]);

    $buyer_number = int($buyer_number);
    $country_number = int($country_number);
    $policy_number = int($policy_number);

    # extrai dados de $data
    my %fields = (); # hash onde serao guardados os dados
    my @keys   = (); # vetor onde serao guardados os nomes dos campos
    my @dados  = &get_data($data, [ &get_correct_lengths($section_number, \@keys) ]);
    my $i;
    for $i (0..$#keys){
	$fields{$keys[$i]} = $dados[$i];
    }
    return ($policy_number, $country_number, $buyer_number,
	    $order_number, $date, $time, $section_number, { %fields });
}

############################################################
# executa uma query, devolve o numero de linhas resultantes
sub query{
    my $q = shift;

    print "$q\n";
    $db->Sql($q);
    return $db->RowCount;
}

############################################################
# mostra os resultados de uma query
sub show_results{
    while ($db->FetchRow) {
	for my $val ($db->Data){
	    print "$val ";
	}
	print "\n";
    }
}

############################################################
# pega a enesima linha do resultado de uma query
sub pega_linha{
    my $n = shift;

    for my $i (1..$n){
	$db->FetchRow;
    }
    return $db->Data;
}

#############################################################################
sub notification_limit{
    my ($msg, $importer_id, $key_cookie, $type,
	$policy_type, $importer_state, $decision_date) = @_;
    print "-----------------------------------------------\n";
    print " * notification_limit, importer=$importer_id *\n";

    # Envia notificação sobre o limite
    # idUser = 13 COFACE
    # idUser = 10 credit
    # idUser = 11 creditManager
    # idUser = 12 creditInform

    $key_cookie++;

    &query("SELECT Inform.id, Inform.name, Inform.state FROM Inform, Importer WHERE Importer.id = $importer_id ".
	   "AND Inform.id = Importer.idInform ");
    $db->FetchRow;
    my ($idInform, $nameCl, $inform_state) = $db->Data;

    # cria notificacao, o link é criado mais abaixo
    $key_cookie++;
    &query("INSERT INTO NotificationR (notification, cookie, state) VALUES".
	   " ('$msg [$nameCl]', '$key_cookie', '1')");

    &query("select max(id) from NotificationR where cookie = '$key_cookie'");
    $db->FetchRow;
    ($notification_id) = $db->Data;
    print "notification id: $notification_id\n";

    if($importer_state >= 1 && $importer_state <= 8 && $inform_state >= 2){
	&query("INSERT INTO RoleNotification (idRole, idNotification) VALUES ('12', $notification_id)");
    }
    if($importer_state >= 9 && $importer_state <= 11){
	&query("INSERT INTO RoleNotification (idRole, idNotification) VALUES ('10', $notification_id)");
    }

    # gera o link para a notificacao
    $decision_date =~ s/([^a-zA-Z0-9_\-. ])/uc sprintf("%%%02x", ord($1))/eg; # perl is magic
    my $link =
	"../credit/Credit.php?comm=replyCoface&idBuyer=$importer_id&idNotification=$notification_id&type=$type&".
	"decision_date=$decision_date&idInform=$idInform&credit=$current_credit&creditReq=$requisicao&id_da_coface=254";
    if($comment){
	# faz o url encode na string
	$comment =~ s/([^a-zA-Z0-9_\-. ])/uc sprintf("%%%02x", ord($1))/eg; # perl is magic
	$comment =~ s/ /+/g;
	$link .= "&obs=$comment";
    }
    if($decision_type == 2){
	$link .= "&refusal=1";
    }
    # verifica se existe credito temporario
    if($overtrade > 0){
	my ($d, $m, $a) = $overtrade_date =~ m{(\d\d)/(\d\d)/(\d\d\d\d)};
	$link .= "&creditTemp=$overtrade&limTemp=$d\%2F$m\%2F$a";
	$overtrade = 0;
    }
    &query("UPDATE NotificationR SET link='$link' WHERE id=$notification_id");

    # log
    &query("INSERT INTO TransactionLog (idUser, description) VALUES (13, '$msg')");

    # atualiza o state do importer
    &query("SELECT DISTINCT Importer.idCountry FROM Importer, Country ".
	   "WHERE Importer.idCountry=Country.id ".
	   "AND Country.id=Importer.idCountry AND ".
	   "Country.code='$current{country_importer_id}' AND Importer.id=$importer_id");
    $db->FetchRow;
    $id_country = $db->Data;

    &query("UPDATE Importer SET state=$type, stateDate=getDate() ".
	   "WHERE c_Coface_Imp='$current{c_coface_imp}' ".
	   "AND idCountry=$id_country AND id=$importer_id");

    print " * end notification_limit *\n";
    print "-----------------------------------------------\n";
} # notification_limit

#######################################################################################
# sei la se isso tah certo... (provavelmente nao)
sub notification_data{
    my ($msg, $importer_id, $key_cookie, $type, $policy_type, $importer_state, $decision_date) = @_;
    print "-----------------------------------------------\n";
    print " * notification_data, importer=$importer_id *\n";

    # envia notificação sobre data.
    # idUser = 13 COFACE
    # idUser = 10 credit
    # idUser = 11 creditManager
    # idUser = 12 creditInform

    $key_cookie++;

    &query("SELECT Inform.name, Inform.id FROM Inform, Importer, Country WHERE Importer.c_Coface_Imp = $current{c_coface_imp} ".
	   "AND Inform.id = Importer.idInform AND Country.id = Importer.idCountry ".
	   "AND Country.code = '$current{country_importer_id}'");
    $db->FetchRow;
    ($nameCl, $idInform) = $db->Data;

    $key_cookie++;
    &query("INSERT INTO NotificationR (notification, cookie, state) VALUES".
	   " ('$msg [$nameCl]', '$key_cookie', '1')");

    &query("select max(id) from NotificationR where cookie = '$key_cookie'");
    $db->FetchRow;
    ($notification_id) = $db->Data;
    print "notification id: $notification_id\n";

    if($importer_state >= 1 && $importer_state <= 8){
	&query("INSERT INTO RoleNotification (idRole, idNotification) VALUES ('12', $notification_id)");
    }
    if($importer_state >= 9 && $importer_state <= 11){
	&query("INSERT INTO RoleNotification (idRole, idNotification) VALUES ('10', $notification_id)");
    }

    # Atualiza o endereço do importador
    # idUser = 13 COFACE
#     ($new_address, $new_name, $new_address2,
#      $new_name2, $new_city, $new_idCountry) = &get_fields($fields_ref,
# 							  [ "Address 1", "Trade Name 1", "Address 2",
# 							    "Trade Name 2", "City", "Country Number" ]);

    &query("select Importer.name, Importer.address, Importer.city, Importer.idCountry from Importer, Country ".
	   "where Importer.c_Coface_Imp = '$current{c_coface_imp}' ".
	   "AND Country.code = '$current{country_importer_id}' AND Country.id = Importer.idCountry");
    $db->FetchRow;
    ($name, $address, $city, $id_country) = $db->Data;

    ###################################
    # o trecho abaixo nao faz sentido... (ou eu q nao entendi...)
#     if ($new_address == $address){
# 	$new_address = $new_address2;
#     }
#     if ($new_name == $name){
# 	$new_name = $new_name2;
#     }
#     if ($new_city == $city){
# 	$new_city = $new_city2;
#     }
    # fim do trecho sem sentido... :-)
    ###################################

    &query("select id from Country where code=$current{country_importer_id}");
    $db->FetchRow;
    ($new_countryId) = $db->Data;
    ############################################################################################
    ### Como faço para descobrir o novo c_Coface_Imp?
    ### R. Boa pergunta... :-)
    ############################################################################################

    &query("INSERT INTO ChangeAddress (idImporter, idNotification, userIdChangeAddress, state, name, address, idCountry,".
	   " city, c_Coface_Imp, tel, stateDate) values ($importer_id, '$notification_id', '13', '$type', '$new_name', '$new_address',".
	   " '$new_countryId', '$new_city', '$current{c_coface_imp}', '$buyer_phone', getdate())");

    # constroi o super-link
    $link = "../credit/Credit.php?comm=replyCoface&idBuyer=$importer_id&type=$type&idNotification=$notification_id".
	"&idInform=$idInform";
    if($comment){
	# faz o url encode na string
	$comment =~ s/([^a-zA-Z0-9_\-. ])/uc sprintf("%%%02x",ord($1))/eg; # perl is magic
	$comment =~ s/ /+/g;
	$link .= "&obs=$comment";
    }
    &query("UPDATE NotificationR SET link='$link' WHERE id=$notification_id");

#     if($policy_type ne "DPP") { # se nao eh PROSPECT
# 	&query("INSERT INTO RoleNotification (idRole, idNotification) VALUES ('10', '$notification_id')");
#     }

    # log
    &query("INSERT INTO TransactionLog (idUser, description) VALUES (13, '$msg')");

    &query("UPDATE Importer SET state = '$type', stateDate = getDate() WHERE ".
	   " c_Coface_Imp = '$current{c_coface_imp}' ".
	   " AND idCountry = '$id_country'");

    print " * end notification_data *\n";
    print "-----------------------------------------------\n";
} # notification_data

#####################################
# adiciona linha no log
# arquivo de log guarda apenas erros
sub add_log{
    my ($file, $msg) = @_;
    open LOG, ">>$file.errors.log" or die "$!\n";
    print LOG "$msg\n";
    close LOG;
}

#############################
# vai para a proxima iteracao de algum loop, de acordo com o section_number
sub label{
    my $section_number = shift;
    my @labels = ('', 'OUTER', 'MIDDLE', 'INNER');

    if($section_number >= 0 && $section_number <= 3){
	eval "next $labels[$section_number]"; # perl is magic :-)
    }
}

################################
# transforma a data de 20020910 para 10/09/2002
sub conserta_data{
    my $date = shift;
    my ($a, $m, $d) = $date =~ /(\d\d\d\d)(\d\d)(\d\d)/;
    return "$d/$m/$a";
}

sub process{ # processa os argumentos
    my @v;
    for (@_){
	if(/^-temp$/){
	    $temp = 1; # indica que vai usar o pacote Temp
	}else{
	    push @v, $_;
	}
    }
    return @v;
}

sub show_help{
    print <<HELP;
Uso: $_[0] [-temp] <arquivo(s) de dados>
HELP
    exit;
}

# atualiza os ci_coface
sub update_ci_coface{
    my @queries = ();
    my $q;

    &query("select 'update Importer set c_Coface_Imp = ' ".
	   "+ substring(c_Coface_Imp, 2,5) ".
	   "+ ' where id =' + cast(id as varchar) from ".
	   "Importer where substring(c_Coface_Imp, 1,1) ".
	   "= '0'  and c_Coface_Imp <> 0");
    while($db->FetchRow){
	$q = $db->Data;
	push @queries, $q;
    }
    for $q (@queries){
	&query($q);
    }
}
