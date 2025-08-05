#!/usr/bin/perl
# Extrai os dados dos importadores (versao Win)

use funcoes;
use Win32::ODBC;

&help($0) unless @ARGV;

# abre a conexao com o banco de dados
$db = new Win32::ODBC("dsn=sbce;UID=sa;PWD=") or die "Nao consegui me conectar ao banco de dados: $DBI::errstr\n";

for $file (@ARGV){
    open FILE, $file or
	# se nao conseguir abrir o arquivo, nao aborta o programa,
	# apenas avisa e vai para o proximo arquivo
	do{
	    warn "Nao pude abrir o arquivo '$file': $!\n";
	    next;
	};

    # le o arquivo e extrai
    $anterior = 5;
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

	print $policy_number.", ".$country_number.", ".$buyer_number.", ".$order_number.", ".$section_number."\n" ;
	
	$atual = $buyer_number;

	if ($atual == $anterior) {
	$rows = &query ("INSERT Auditoria (informContrat, importerCountryCode, importerC_Coface_Imp,".
		  " cgg, section_Number) values ".
		  "($policy_number, $country_number, $buyer_number, $order_number, $section_number)");
	}
	$anterior = $atual;
      }

	%fields = (); # "zera" o hash para a proxima iteracao
    } # while (<FILE>)

    close FILE;

    $row = &query( "DELETE FROM Auditoria WHERE section_Number = 1" );


}

$db->Close();

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


