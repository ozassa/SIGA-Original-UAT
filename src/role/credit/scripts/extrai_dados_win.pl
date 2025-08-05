#!/usr/bin/perl
# Extrai os dados dos importadores (versao Win)

use funcoes;
use Win32::ODBC;

&help($0) unless @ARGV;

# abre a conexao com o banco de dados
$db = new Win32::ODBC("dsn=sbce;UID=sa;PWD=") or die "Nao consegui me conectar ao banco de dados: $DBI::errstr\n";

# gera notificacao
&query("insert into NotificationR (notification, bornDate, state, link) values ('testando essa coisa', '".
       `date`.
       "', 1, 'www.tendencies.com.br')");
# pega o id da notificacao q acabou de ser inserida
&query("select max(id) from NotificationR");
$db->FetchRow;
($notification_id) = $db->Data;

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

	#########################################################################
	# vou assumir q $buyer_number eh o id do importador(talvez nao seja...) #
	#########################################################################
	$importer_id = sprintf("%d", $buyer_number);

	# ve se o importador existe
  	$rows = &query("select id from Importer where id=$importer_id");
  	if(! $rows){
  	    print "Importador $importer_id nao existe\n";
  	    next;
  	}

	# extrai dados de $data
	@keys = ();
	@dados = &get_data($data, [ &get_correct_lengths($section_number, \@keys) ]);
	my $i;
	for $i (0..$#keys){
	    $fields{$keys[$i]} = $dados[$i];
	}
	if($section_number == 6){
	    ($amount, $coefficient) = &get_fields(\%fields,
						  [ "Amount of the Decision in Currency",
						    "Currency Coefficient" ]
						  );
	    ##############################################################################
	    # por enquanto vou usar $amount como sendo o credito correto (mas nao eh!!!) #
	    ##############################################################################
	    $current_credit = sprintf("%.2f", $amount);

	    # verifica se jah existe credito para o importador
	    # (pega sempre o credito mais recente)
	    $rows = &query("select creditAssigned from Credit where idImporter=$importer_id and creditDate=".
			   "(select max(creditDate) from Credit where idImporter=$importer_id) order by id");
	    ($credit_assigned) = &pega_linha($rows);

	    if(!defined($credit_assigned)){ # credito inexistente, deve-se criar um
		print "Credito inexistente para $importer_id\n";
		$credit_status = 1; # status para criacao
	    }elsif($credit_assigned != $current_credit){ # alteracao de credito
		print "$credit_assigned != $current_credit\n";
		$credit_status = 2; # status para alteracao
	    }else{
		$credit_status = 0; # nenhum status
	    }
	    if($credit_status){
		&query("insert into ChangeCredit (idNotificationR, idImporter, credit, state) values ".
		       "($notification_id, $importer_id, $current_credit, $credit_status)");
	    }
	}

	%fields = (); # "zera" o hash para a proxima iteracao
    } # while (<FILE>)

    close FILE;
}

$db->disconnect;

############################################################
# executa uma query, devolve o numero de linhas resultantes

sub query{
    my $q = shift;

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
sub pega_linha{
    my $n = shift; # pega a enesima linha de $s
    my @row;

    for my $i (1..$n){
	$db->FetchRow;
    }
    return $db->Data;
}
