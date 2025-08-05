#!/usr/bin/perl

use IO::Socket::INET;
use Mail::Sender;
use Win32::ODBC;

# abre conexao udp
$sock = IO::Socket::INET->new(LocalPort => 1654,
			      Proto     => 'udp');

die "$!\n" unless $sock;

@emails = ('desenvolvimento@sbce.com.br');

$text = "";

# fica esperando chegar confirmacao
print ("Aguardando lista de emails a enviar\n");
#while($sock->recv($idNotificationR, 1024)){
while($sock->recv($text, 1024)){
print ("Recebi notificacao[".$text."]\n");
#    &manda_emails($idNotificationR);
}

close $sock;


############################################################
# manda os emails pra todo mundo...
sub manda_emails{
    my $idNotificationR = shift;

    # abre a conexao com o banco de dados
    $db = new Win32::ODBC("dsn=sbce;UID=sa;PWD=") or die "Nao consegui me conectar ao banco de dados: $DBI::errstr\n";

    &query("select credit, state, idImporter from ".
	   "change_credit where idNotificationR=$idNotificationR");
    while($db->FetchRow){
	my @row = $db->Data;
	for my $to (@emails){
	    &envia('SBCE <desenvolvimento@sbce.com.br>', $to,
		   &subject($row[1]), &gera_mensagem(@row));
	}
    }

    $db->Close;
}
############################################################
sub gera_mensagem{
    my ($credit, $state, $idImporter) = @_;
    my $msg = "URGENTE\n\nPrezados Senhores,\n\n";
#campos
    my ($fields) = ("I.id as \"CI Segurado\", I.name as \"Nome do Importador\",".
		    "I.address as \"Endereço do Importador\", P.nome, C.credit_req as \"Crédito Solicitado\",".
		    "C.credit_assigned as \"Credito Condecido\", C.credit_date as \"Data da Decisão\"");
#table
    my $tables = 'Importer as I, pais as P, credit as C';
#SQL
    my $sql = "select $fields from $tables where I.id = $idImporter and I.idCountry = P.id";

    &query($sql);
    my $dados = "";
    while($st->FetchRow){
	my %data = $db->DataHash;
	for my $k (keys %data){
	    $dados .= "$i: $data{$i}\n";
	}
    }

    # final da mensagem, eh sempre igual 
    my $footer =<<FOOTER;
A Ficha de Aprovação de Limites de Crédito atualizada
pode ser obtida através do site http://www.sbce.com.br

Qualquer dúvida, estaremos à disposição para maiores esclarecimentos.


Atenciosamente,


Elisa Salomão
Departamento de Crédito
FOOTER

    if($state == 1){
	$msg .=<<MSG;
Segue posição de crédito de seu importador:

$dados

$footer
MSG
    }elsif($state == 2){
	$msg .=<<MSG;
Segue alteração de crédito de seu importador:

$dados

Caso haja alguma exportação em curso, favor entrar em contato no
prazo máximo de oito dias.

$footer
MSG
    }else{
	die "Estado invalido para credito: $state\n";
    }
    return $msg;
}
############################################################
sub envia{
    my ($from, $to, $subject, $msg) = @_;
    my $sender = new Mail::Sender(
				  { from => $from,
				    smtp => 'mailhost.rest' }
				  );
    ref ($sender) or die "$Mail::Sender::Error\n";

    my $retorno = $sender->MailMsg(
				   { to => $to,
				     subject => $subject,
				     msg => $msg }
				   );
    ref ($retorno) or die "$Mail::Sender::Error\n";
    print "E-mail enviado com sucesso para $to!\n";
}
############################################################
sub subject{
    my $state = shift;

    return 'Inclusao de limite de credito' if $state == 1;
    return 'Alteracao de limite de credito' if $state == 2;
    return '';
}

############################################################
# executa uma query, devolve um array com 
# o 'statement handler' e o numero de linhas devolvidas

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





