#!/usr/bin/perl
# Extrai os dados dos importadores

use funcoes;
#use DBI;

&help($0) unless @ARGV;

# abre a conexao com o banco de dados
#$db = DBI->connect("dbi:Pg:dbname=alex-credit", "alex", "alex")
 #   or die "Nao consegui me conectar ao banco de dados: $DBI::errstr\n";

# gera notificacao
# &query("insert into notificationR (notification, bornDate, state, link) values ('testando essa coisa', '".
#        `date +'%Y-%m-%d'`.
#        "', 1, 'www.tendencies.com.br')");

# pega o id da notificacao q acabou de ser inserida
#($notification_id) = (&query("select max(id) from notificationR"))[0]->fetchrow;

for $file (@ARGV){
    open FILE, $file or
	# se nao conseguir abrir o arquivo, nao aborta o programa,
	# apenas avisa e vai para o proximo arquivo
	do{
	    warn "Nao pude abrir o arquivo '$file': $!\n";
	    next;
	};

    # le o arquivo e extrai as porcarias de la :-)
    while(<FILE>){
	# separa o header dos dados
	if(/(.{39})(.{264})/){
	    ($header, $data) = ($1, $2);
	}else{ # se nao conseguiu, tenta a proxima linha
	    next;
	}

	# extrai dados de $header
	($policy_number, $country_number, $buyer_number,
	 $order_number, $date, $time, $section_number) = &get_data($header, [ 12, 3, 6, 2, 8, 6, 2 ]);

	$buyer_number = int($buyer_number);

# 	# ve se o importador existe
#   	($result, $rows) = &query("select id from importer where id=$importer_id");
#   	if(! $rows){
#   	    print "Importador $importer_id nao existe\n";
#   	    next;
#   	}
# 	$result->finish;

	# extrai dados de $data
	@keys = ();
	@dados = &get_data($data, [ &get_correct_lengths($section_number, \@keys) ]);
	my $i;
	for $i (0..$#keys){
	    $fields{$keys[$i]} = $dados[$i];
	}

    	print "-------------------------\n";
   	print "section number: $section_number, apolice: $policy_number,$order_number\n";
 	print "cCoface: $buyer_number, country: $country_number\n";
	for $i (sort keys %fields){
	    print "$i: $fields{$i}\n";
	}


# 	if($section_number == 6){
# 	    ($amount, $coefficient) = &get_fields(\%fields,
# 						  [ "Amount of the Decision in Currency",
# 						    "Currency Coefficient" ]
# 						  );
# 	    ##############################################################################
# 	    # por enquanto vou usar $amount como sendo o credito correto (mas nao eh!!!) #
# 	    ##############################################################################
# 	    $current_credit = sprintf("%.2f", $amount);

# 	    # verifica se jah existe credito para o importador
# 	    # (pega sempre o credito mais recente)
# 	    ($sth, $rows) = &query("select credit_assigned from credit where idImporter=$importer_id and credit_date=".
# 				   "(select max(credit_date) from credit where idImporter=$importer_id) order by id");
# 	    ($credit_assigned) = &get_line($sth, $rows);
# 	    $sth->finish;

# 	    if(!defined($credit_assigned)){ # credito inexistente, deve-se criar um
# 		print "Credito inexistente para $importer_id\n";
# 		$credit_status = 1; # status para criacao
# 	    }elsif($credit_assigned != $current_credit){ # alteracao de credito
# 		print "$credit_assigned != $current_credit\n";
# 		$credit_status = 2; # status para alteracao
# 	    }else{
# 		$credit_status = 0; # nenhum status
# 	    }
# 	    if($credit_status){
# 		&query("insert into change_credit (idNotificationR, idImporter, credit, state) values ".
# 		       "($notification_id, $importer_id, $current_credit, $credit_status)");
# 	    }
# 	}

	%fields = (); # "zera" o hash para a proxima iteracao
    } # while (<FILE>)

    close FILE;
}

#$db->disconnect;

############################################################
# executa uma query, devolve um array com 
# o 'statement handler' e o numero de linhas devolvidas

sub query{
#     my $q = shift;
#     my $sth = $db->prepare($q) or
# 	do{
# 	    $db->disconnect;
# 	    die "Erro ao preparar query '$q': $DBI::errstr\n";
# 	};

#     my $num_rows = $sth->execute or
# 	do{
# 	    $db->disconnect;
# 	    die "Erro ao executar query '$q': $DBI::errstr\n";
# 	};
#     return ($sth, $num_rows);
    return 1;
}

############################################################
# mostra os resultados de uma query
sub show_results{
    my $sth = shift;
    print join(" ", @{ $sth->{NAME} }), "\n";
    while (my @row = $sth->fetchrow) {
	for my $val (@row){
	    print "$val ";
	}
	print "\n";
    }
}
